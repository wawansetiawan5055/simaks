<?php

class KeuanganTagihanModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($filters = []) {
        $sql = "SELECT t.*, s.nama as nama_siswa, s.nisn, k.nama_kelas, j.nama_jenis, 
                (t.jumlah_tagihan - t.sisa_tagihan) as jumlah_terbayar
                FROM keuangan_tagihan_siswa t
                JOIN siswa s ON t.id_siswa = s.id_siswa
                JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa 
                    AND ps.id_ta = ?
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE 1=1";
        
        $params = [$filters['id_ta'] ?? 0];
        
        if (!empty($filters['id_kelas'])) {
            $sql .= " AND ps.id_kelas = ?";
            $params[] = $filters['id_kelas'];
        }

        if (!empty($filters['id_jenis'])) {
            $sql .= " AND t.id_jenis = ?";
            $params[] = $filters['id_jenis'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['periode'])) {
            $sql .= " AND t.periode = ?";
            $params[] = $filters['periode'];
        }

        if (!empty($filters['id_ta']) && $filters['id_ta'] !== 'all') {
            $sql .= " AND t.tahun_ajaran = ?";
            $params[] = $filters['id_ta'];
        }

        $sql .= " ORDER BY t.created_at DESC, s.nama ASC LIMIT 500"; // Limit for performance

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if a specific bill already exists for a student
    public function checkExists($id_siswa, $id_jenis, $periode, $tahun_ajaran) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM keuangan_tagihan_siswa 
                                     WHERE id_siswa = ? AND id_jenis = ? 
                                     AND periode = ? AND tahun_ajaran = ?");
        $stmt->execute([$id_siswa, $id_jenis, $periode, $tahun_ajaran]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Efficiently get all existing bills for a set of students, years, and categories
     * Returns a 2D map for O(1) lookup: [student_id][periode] = true
     * If $tahun_ajaran is null, it checks across ALL years (important for non-recurring duplicates)
     */
    public function getExistingMap($id_jenis, $id_ta = null) {
        $sql = "SELECT id_siswa, periode FROM keuangan_tagihan_siswa WHERE id_jenis = ?";
        $params = [$id_jenis];
        
        if ($id_ta) {
            $sql .= " AND tahun_ajaran = ?";
            $params[] = $id_ta;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $map = [];
        foreach ($rows as $row) {
            $map[$row['id_siswa']][$row['periode']] = true;
        }
        return $map;
    }

    public function create($data) {
        $sql = "INSERT INTO keuangan_tagihan_siswa (
                    id_siswa, id_jenis, tahun_ajaran, periode, 
                    tanggal_jatuh_tempo, jumlah_tagihan, sisa_tagihan, 
                    status, keterangan
                ) VALUES (
                    ?, ?, ?, ?, 
                    ?, ?, ?, 
                    'BELUM_BAYAR', ?
                )";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['id_siswa'],
            $data['id_jenis'],
            $data['tahun_ajaran'],
            $data['periode'],
            $data['tanggal_jatuh_tempo'],
            $data['jumlah_tagihan'],
            $data['jumlah_tagihan'], // Initial sisa = total
            $data['keterangan'] ?? null
        ]);
    }

    /**
     * Bulk insert bills for maximum performance
     */
    public function createBatch($rows) {
        if (empty($rows)) return true;
        
        $sql = "INSERT INTO keuangan_tagihan_siswa (
                    id_siswa, id_jenis, tahun_ajaran, periode, 
                    tanggal_jatuh_tempo, jumlah_tagihan, sisa_tagihan, 
                    status, keterangan
                ) VALUES ";
        
        $placeholders = [];
        $values = [];
        foreach ($rows as $row) {
            $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, 'BELUM_BAYAR', ?)";
            $values[] = $row['id_siswa'];
            $values[] = $row['id_jenis'];
            $values[] = $row['tahun_ajaran'];
            $values[] = $row['periode'];
            $values[] = $row['tanggal_jatuh_tempo'];
            $values[] = $row['jumlah_tagihan'];
            $values[] = $row['jumlah_tagihan']; // sisa
            $values[] = $row['keterangan'] ?? null;
        }
        
        $sql .= implode(', ', $placeholders);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    
    // Efficiently count existing bills for batch feedback
    public function countByPeriode($id_kelas, $id_jenis, $periode) {
         $sql = "SELECT COUNT(*) FROM keuangan_tagihan_siswa t
                 JOIN penempatan_siswa ps ON t.id_siswa = ps.id_siswa
                 WHERE ps.id_kelas = ? AND t.id_jenis = ? AND t.periode = ?";
         $stmt = $this->pdo->prepare($sql);
         $stmt->execute([$id_kelas, $id_jenis, $periode]);
         return $stmt->fetchColumn();
    }
    /**
     * Simulation 1: Get Arrears Report (Tunggakan)
     */
    public function getReportArrears($filters = []) {
        $sql = "SELECT s.id_siswa, s.nama, s.nisn, k.nama_kelas, j.nama_jenis, j.kode_akun,
                       t.jumlah_tagihan, t.sisa_tagihan, t.status, t.periode
                FROM keuangan_tagihan_siswa t
                JOIN siswa s ON t.id_siswa = s.id_siswa
                JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa 
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE 1=1"; 
        
        $params = [];
        if (!empty($filters['id_ta'])) {
            $sql .= " AND ps.id_ta = ?";
            $params[] = $filters['id_ta'];
        }
        if (!empty($filters['id_kelas'])) {
            $sql .= " AND ps.id_kelas = ?";
            $params[] = $filters['id_kelas'];
        }
        if (!empty($filters['id_jenis'])) {
            $sql .= " AND t.id_jenis = ?";
            $params[] = $filters['id_jenis'];
        }
        if (!empty($filters['id_ta'])) {
            $sql .= " AND t.tahun_ajaran = ?";
            $params[] = $filters['id_ta'];
        }

        $sql .= " ORDER BY k.tingkat, k.nama_kelas, s.nama ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Monthly Matrix for Recurring Payments (SPP-like)
     * Returns student payment matrix with months as columns
     */
    public function getMonthlyMatrix($id_kelas, $id_jenis, $id_ta, $periods) {
        // Get all students in class
        $sqlStudents = "SELECT s.id_siswa, s.nama, s.nisn
                        FROM siswa s
                        JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                        WHERE ps.id_kelas = ? AND ps.id_ta = ?
                        ORDER BY s.nama ASC";
        $stmtS = $this->pdo->prepare($sqlStudents);
        $stmtS->execute([$id_kelas, $id_ta]);
        $students = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // Get all bills for this jenis & class
        $sqlBills = "SELECT t.id_siswa, t.periode, t.jumlah_tagihan, t.sisa_tagihan
                     FROM keuangan_tagihan_siswa t
                     JOIN penempatan_siswa ps ON t.id_siswa = ps.id_siswa
                     WHERE ps.id_kelas = ? AND t.id_jenis = ? AND ps.id_ta = ?
                     AND t.periode IN (".implode(',', array_fill(0, count($periods), '?')).")";
        $params = array_merge([$id_kelas, $id_jenis, $id_ta], $periods);
        $stmtB = $this->pdo->prepare($sqlBills);
        $stmtB->execute($params);
        $bills = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        // Build matrix
        $matrix = [];
        foreach ($students as $s) {
            $matrix[$s['id_siswa']] = [
                'nama' => $s['nama'],
                'nisn' => $s['nisn'],
                'months' => [],
                'total_tagihan' => 0,
                'total_bayar' => 0,
                'tunggakan' => 0
            ];
            // Initialize all periods
            foreach ($periods as $p) {
                $matrix[$s['id_siswa']]['months'][$p] = [
                    'tagihan' => 0,
                    'bayar' => 0,
                    'sisa' => 0
                ];
            }
        }

        // Fill bills data
        foreach ($bills as $b) {
            $sid = $b['id_siswa'];
            $period = $b['periode'];
            if (isset($matrix[$sid]['months'][$period])) {
                $bayar = $b['jumlah_tagihan'] - $b['sisa_tagihan'];
                $matrix[$sid]['months'][$period] = [
                    'tagihan' => $b['jumlah_tagihan'],
                    'bayar' => $bayar,
                    'sisa' => $b['sisa_tagihan']
                ];
                $matrix[$sid]['total_tagihan'] += $b['jumlah_tagihan'];
                $matrix[$sid]['total_bayar'] += $bayar;
                $matrix[$sid]['tunggakan'] += $b['sisa_tagihan'];
            }
        }

        return $matrix;
    }

    /**
     * Get List Report for One-Time Payments (DSP, Ujian, etc.)
     * New Logic: Detects if id_jenis is recurring. If NOT, it sums across ALL years for each student.
     */
    public function getListReport($id_kelas, $id_jenis, $id_ta) {
        // 1. Fetch Jenis Info to check recurring status
        $stmtJ = $this->pdo->prepare("SELECT is_recurring FROM keuangan_jenis WHERE id_jenis = ?");
        $stmtJ->execute([$id_jenis]);
        $is_recurring = (int)($stmtJ->fetchColumn() ?: 0);

        $sql = "SELECT s.id_siswa, s.nama, s.nisn,
                       SUM(COALESCE(t.jumlah_tagihan, 0)) as jumlah_tagihan, 
                       SUM(COALESCE(t.sisa_tagihan, 0)) as sisa_tagihan,
                       SUM(COALESCE(t.jumlah_tagihan, 0) - COALESCE(t.sisa_tagihan, 0)) as sudah_bayar,
                       CASE 
                         WHEN COUNT(t.id_tagihan) = 0 THEN 'BELUM LUNAS'
                         WHEN SUM(t.sisa_tagihan) = 0 THEN 'LUNAS'
                         ELSE 'BELUM LUNAS'
                       END as status
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                LEFT JOIN keuangan_tagihan_siswa t ON s.id_siswa = t.id_siswa AND t.id_jenis = ? ";
        
        $params = [$id_jenis];

        // Only restrict to TA if RECURRING
        if ($is_recurring) {
            $sql .= " AND t.tahun_ajaran = ? ";
            $params[] = $id_ta;
        }

        $sql .= " WHERE ps.id_kelas = ? AND ps.id_ta = ?
                GROUP BY s.id_siswa, s.nama, s.nisn
                ORDER BY s.nama ASC";
        
        $params[] = $id_kelas;
        $params[] = $id_ta;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Individual History (keeping for backward compatibility)
     */
    public function getIndividualHistory($id_siswa) {
        $sqlBills = "SELECT t.*, j.nama_jenis, j.kode_akun 
                     FROM keuangan_tagihan_siswa t
                     JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                     WHERE t.id_siswa = ? ORDER BY t.periode ASC";
        $stmtB = $this->pdo->prepare($sqlBills);
        $stmtB->execute([$id_siswa]);
        $bills = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        $sqlPay = "SELECT tr.*, j.nama_jenis, r.nama_rekening 
                   FROM keuangan_transaksi tr
                   JOIN keuangan_jenis j ON tr.id_jenis = j.id_jenis
                   LEFT JOIN keuangan_rekening r ON tr.id_rekening = r.id_rekening
                   WHERE tr.id_siswa = ? AND tr.tipe = 'MASUK'
                   ORDER BY tr.tanggal ASC";
        $stmtP = $this->pdo->prepare($sqlPay);
        $stmtP->execute([$id_siswa]);
        $payments = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        return ['bills' => $bills, 'payments' => $payments];
    }

    /**
     * Get Total Arrears (Tunggakan) for Dashboard
     * Filter by Due Date (Jatuh Tempo) <= Today
     */
    public function getTotalTunggakan() {
        $sql = "SELECT SUM(sisa_tagihan) FROM keuangan_tagihan_siswa 
                WHERE status != 'LUNAS' 
                AND tanggal_jatuh_tempo <= CURDATE()";
        $stmt = $this->pdo->query($sql);
        return (float)$stmt->fetchColumn() ?: 0;
    }
}
