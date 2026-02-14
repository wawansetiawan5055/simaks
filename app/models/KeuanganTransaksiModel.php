<?php
/**
 * KeuanganTransaksiModel
 * Model untuk mengelola data Transaksi Keuangan
 */

class KeuanganTransaksiModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all transactions with filters
     */
    public function getAll($filters = []) {
        $sql = "SELECT t.*, 
                       j.nama_jenis, j.kode_akun, j.is_recurring,
                       k.nama_kategori, k.tipe as kategori_tipe, 
                       r.nama_rekening, 
                       s.nama AS nama_siswa,
                       tg.periode
                FROM keuangan_transaksi t
                LEFT JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                LEFT JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                LEFT JOIN keuangan_rekening r ON t.id_rekening = r.id_rekening
                LEFT JOIN siswa s ON t.id_siswa = s.id_siswa
                LEFT JOIN keuangan_tagihan_siswa tg ON t.id_tagihan = tg.id_tagihan
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['tipe'])) {
            $sql .= " AND t.tipe = ?";
            $params[] = $filters['tipe'];
        }
        
        if (isset($filters['tanggal_dari'])) {
            $sql .= " AND t.tanggal >= ?";
            $params[] = $filters['tanggal_dari'];
        }

        if (isset($filters['tanggal_sampai'])) {
            $sql .= " AND t.tanggal <= ?";
            $params[] = $filters['tanggal_sampai'];
        }

        if (isset($filters['bulan'])) {
            $sql .= " AND MONTH(t.tanggal) = ?";
            $params[] = $filters['bulan'];
        }

        if (isset($filters['tahun'])) {
            $sql .= " AND YEAR(t.tanggal) = ?";
            $params[] = $filters['tahun'];
        }

        $sql .= " ORDER BY t.tanggal DESC, t.id_transaksi DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent transactions
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT t.*, j.nama_jenis, j.kode_akun, k.nama_kategori
                FROM keuangan_transaksi t
                LEFT JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                LEFT JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                ORDER BY t.created_at DESC LIMIT " . (int)$limit;
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total sum by type and date range
     */
    public function getTotalByTipe($tipe, $start_date, $end_date) {
        $sql = "SELECT SUM(jumlah) as total 
                FROM keuangan_transaksi 
                WHERE tipe = ? AND tanggal BETWEEN ? AND ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipe, $start_date, $end_date]);
        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Create Transaction
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // 1. Generate No Bukti (Accounting Format: [TIPE].[YYMMDD].[COA].[SEQ])
            $prefix = ($data['tipe'] == 'MASUK') ? 'BM' : 'BK';
            $datePart = date('ymd', strtotime($data['tanggal']));
            
            // 1a. Fetch COA (Kode Akun)
            $coa = '0000';
            if (!empty($data['id_jenis'])) {
                $stmtJ = $this->db->prepare("SELECT kode_akun FROM keuangan_jenis WHERE id_jenis = ?");
                $stmtJ->execute([$data['id_jenis']]);
                $coa = $stmtJ->fetchColumn() ?: '0000';
            }

            // 1b. Get Sequence for the day (Per Tipe per Tanggal)
            $stmtSeq = $this->db->prepare("SELECT COUNT(*) FROM keuangan_transaksi WHERE tipe = ? AND tanggal = ?");
            $stmtSeq->execute([$data['tipe'], $data['tanggal']]);
            $seq = (int)$stmtSeq->fetchColumn() + 1;
            $seqPart = str_pad($seq, 3, '0', STR_PAD_LEFT);

            $no_bukti = "$prefix.$datePart.$coa.$seqPart";
            
            $sql = "INSERT INTO keuangan_transaksi (
                no_bukti, tanggal, tipe, id_jenis, id_rekening, id_siswa, id_tagihan,
                jumlah, metode_pembayaran, referensi, keterangan, id_pengguna
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $no_bukti,
                $data['tanggal'],
                $data['tipe'],
                $data['id_jenis'],
                $data['id_rekening'],
                $data['id_siswa'] ?? null,
                $data['id_tagihan'] ?? null,
                $data['jumlah'],
                $data['metode_pembayaran'] ?? 'TUNAI',
                $data['referensi'] ?? null,
                $data['keterangan'] ?? null,
                $data['id_pengguna']
            ]);
            
            $transaksi_id = $this->db->lastInsertId();
            
            // Update Saldo Rekening
            $operator = ($data['tipe'] == 'MASUK') ? '+' : '-';
            $sql_saldo = "UPDATE keuangan_rekening SET saldo_akhir = saldo_akhir $operator ? WHERE id_rekening = ?";
            $stmt_saldo = $this->db->prepare($sql_saldo);
            $stmt_saldo->execute([$data['jumlah'], $data['id_rekening']]);

            // NEW: Update Sisa Tagihan if id_tagihan is present
            if (!empty($data['id_tagihan'])) {
                $sql_tagihan = "UPDATE keuangan_tagihan_siswa 
                                SET sisa_tagihan = sisa_tagihan - ?, 
                                    status = IF(sisa_tagihan - ? <= 0, 'LUNAS', 'DICICIL') 
                                WHERE id_tagihan = ?";
                $stmt_tagihan = $this->db->prepare($sql_tagihan);
                $stmt_tagihan->execute([$data['jumlah'], $data['jumlah'], $data['id_tagihan']]);
            }

            $this->db->commit();
            return $transaksi_id;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get transaction by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM keuangan_transaksi WHERE id_transaksi = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update Transaction
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // 1. Get Old Data
            $oldData = $this->getById($id);
            if (!$oldData) throw new Exception("Transaksi tidak ditemukan");

            // 2. Revert Old Balance
            $revertOp = ($oldData['tipe'] == 'MASUK') ? '-' : '+';
            $sqlRevert = "UPDATE keuangan_rekening SET saldo_akhir = saldo_akhir $revertOp ? WHERE id_rekening = ?";
            $this->db->prepare($sqlRevert)->execute([$oldData['jumlah'], $oldData['id_rekening']]);

            // NEW: Revert Old Tagihan Sisa if id_tagihan was present
            if (!empty($oldData['id_tagihan'])) {
                $sql_revert_tagihan = "UPDATE keuangan_tagihan_siswa 
                                       SET sisa_tagihan = sisa_tagihan + ?, 
                                           status = IF(sisa_tagihan + ? >= jumlah_tagihan, 'BELUM_BAYAR', 'DICICIL') 
                                       WHERE id_tagihan = ?";
                $this->db->prepare($sql_revert_tagihan)->execute([$oldData['jumlah'], $oldData['jumlah'], $oldData['id_tagihan']]);
            }

            // 3. Update Transaction Record
            $sql = "UPDATE keuangan_transaksi SET 
                    tanggal = ?, id_jenis = ?, id_rekening = ?, id_siswa = ?, id_tagihan = ?,
                    jumlah = ?, metode_pembayaran = ?, referensi = ?, keterangan = ?, updated_at = NOW()
                    WHERE id_transaksi = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['tanggal'],
                $data['id_jenis'],
                $data['id_rekening'],
                $data['id_siswa'] ?? null,
                $data['id_tagihan'] ?? null,
                $data['jumlah'],
                $data['metode_pembayaran'] ?? 'TUNAI',
                $data['referensi'] ?? null,
                $data['keterangan'] ?? null,
                $id
            ]);

            // 4. Apply New Balance
            $applyOp = ($oldData['tipe'] == 'MASUK') ? '+' : '-'; 
            $sqlApply = "UPDATE keuangan_rekening SET saldo_akhir = saldo_akhir $applyOp ? WHERE id_rekening = ?";
            $this->db->prepare($sqlApply)->execute([$data['jumlah'], $data['id_rekening']]);

            // NEW: Apply New Tagihan Sisa if id_tagihan is present
            if (!empty($data['id_tagihan'])) {
                $sql_apply_tagihan = "UPDATE keuangan_tagihan_siswa 
                                      SET sisa_tagihan = sisa_tagihan - ?, 
                                          status = IF(sisa_tagihan - ? <= 0, 'LUNAS', 'DICICIL') 
                                      WHERE id_tagihan = ?";
                $this->db->prepare($sql_apply_tagihan)->execute([$data['jumlah'], $data['jumlah'], $data['id_tagihan']]);
            }

            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete Transaction
     */
    public function delete($id) {
        try {
            $this->db->beginTransaction();

            // 1. Get Data
            $data = $this->getById($id);
            if (!$data) throw new Exception("Transaksi tidak ditemukan");

            // 2. Revert Balance
            $operator = ($data['tipe'] == 'MASUK') ? '-' : '+';
            $sql_saldo = "UPDATE keuangan_rekening SET saldo_akhir = saldo_akhir $operator ? WHERE id_rekening = ?";
            $this->db->prepare($sql_saldo)->execute([$data['jumlah'], $data['id_rekening']]);

            // 3. Revert Tagihan if applicable
            if (!empty($data['id_tagihan'])) {
                $sql_tagihan = "UPDATE keuangan_tagihan_siswa 
                                SET sisa_tagihan = sisa_tagihan + ?, 
                                    status = IF(sisa_tagihan + ? >= jumlah_tagihan, 'BELUM_BAYAR', 'DICICIL') 
                                WHERE id_tagihan = ?";
                $this->db->prepare($sql_tagihan)->execute([$data['jumlah'], $data['jumlah'], $data['id_tagihan']]);
            }

            // 4. Delete Record
            $this->db->prepare("DELETE FROM keuangan_transaksi WHERE id_transaksi = ?")->execute([$id]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    /**
     * Get BKU (Buku Kas Umum) Data
     * Returns ['saldo_awal' => float, 'transaksi' => array]
     */
    public function getBKU($startDate, $endDate, $id_rekening = null) {
        $paramsSaldo = [$startDate];
        $paramsTrans = [$startDate, $endDate];
        
        $whereRekening = "";
        
        if (!empty($id_rekening)) {
            $whereRekening = " AND id_rekening = ?";
            $paramsSaldo[] = $id_rekening;
            $paramsTrans[] = $id_rekening;
        }

        // 1. Calculate Opening Balance (Transactions BEFORE startDate)
        // Saldo Awal = Sum(Masuk) - Sum(Keluar)
        $sqlSaldo = "SELECT 
                        COALESCE(SUM(CASE WHEN tipe = 'MASUK' THEN jumlah ELSE 0 END), 0) as total_masuk,
                        COALESCE(SUM(CASE WHEN tipe = 'KELUAR' THEN jumlah ELSE 0 END), 0) as total_keluar
                     FROM keuangan_transaksi 
                     WHERE tanggal < ? $whereRekening";
        
        $stmtSaldo = $this->db->prepare($sqlSaldo);
        $stmtSaldo->execute($paramsSaldo);
        $resSaldo = $stmtSaldo->fetch(PDO::FETCH_ASSOC);
        $saldoAwal = $resSaldo['total_masuk'] - $resSaldo['total_keluar'];

        // 2. Fetch Transactions in Range
        $sqlTrans = "SELECT t.*, j.nama_jenis, j.kode_akun, k.nama_kategori, r.nama_rekening, s.nama as nama_siswa
                     FROM keuangan_transaksi t
                     LEFT JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                     LEFT JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                     LEFT JOIN keuangan_rekening r ON t.id_rekening = r.id_rekening
                     LEFT JOIN siswa s ON t.id_siswa = s.id_siswa
                     WHERE t.tanggal BETWEEN ? AND ? $whereRekening
                     ORDER BY t.tanggal ASC, t.created_at ASC"; // Order chronologically
        
        $stmtTrans = $this->db->prepare($sqlTrans);
        $stmtTrans->execute($paramsTrans);
        $transaksi = $stmtTrans->fetchAll(PDO::FETCH_ASSOC);

        return [
            'saldo_awal' => (float)$saldoAwal,
            'transaksi' => $transaksi
        ];
    }
}
