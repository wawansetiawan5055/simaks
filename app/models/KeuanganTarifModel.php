<?php
/**
 * KeuanganTarifModel
 * Model untuk mengelola Tarif Khusus (Tiered Pricing)
 */

class KeuanganTarifModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all tarif rules
     */
    public function getAll() {
        $sql = "SELECT t.*, j.nama_jenis, k.nama_kelas, s.nama as nama_siswa
                FROM keuangan_tarif t
                JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                LEFT JOIN kelas k ON t.id_kelas = k.id_kelas
                LEFT JOIN siswa s ON t.id_siswa = s.id_siswa
                ORDER BY j.nama_jenis, k.nama_kelas, s.nama";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get Valid Tariff for a specific Student and Jenis
     * Logic: Check Student Specific -> Check Class Specific -> Return False (Use Default)
     */
    public function getTarifForStudent($id_siswa, $id_jenis) {
        // 1. Check Specific Student Rule
        $stmt = $this->db->prepare("SELECT nominal, keterangan FROM keuangan_tarif WHERE id_siswa = ? AND id_jenis = ? LIMIT 1");
        $stmt->execute([$id_siswa, $id_jenis]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) return $res;

        // 2. Check Class Specific Rule
        // Need to find student's active class first
        $stmtClass = $this->db->prepare("SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif' LIMIT 1");
        $stmtClass->execute([$id_siswa]);
        $class = $stmtClass->fetch(PDO::FETCH_ASSOC);
        
        if ($class) {
            $stmt2 = $this->db->prepare("SELECT nominal, keterangan FROM keuangan_tarif WHERE id_kelas = ? AND id_jenis = ? AND id_siswa IS NULL LIMIT 1");
            $stmt2->execute([$class['id_kelas'], $id_jenis]);
            $res2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($res2) return $res2;
        }

        return null; // Fallback to Default Price in Controller
    }

    /**
     * Get all active tariffs/activations for a specific student
     * Includes both student-specific and class-inherited rules
     */
    public function getTariffsForStudentMatrix($id_siswa) {
        // Find student class first
        $stmtClass = $this->db->prepare("SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif' LIMIT 1");
        $stmtClass->execute([$id_siswa]);
        $class = $stmtClass->fetch(PDO::FETCH_ASSOC);
        $id_kelas = $class['id_kelas'] ?? 0;

        /**
         * Prioritized Query:
         * We fetch rules for this specific student OR rules for their class.
         * We sort such that student-specific rules (id_siswa IS NOT NULL) come before class rules.
         * Then in PHP we'll filter to keep only one rule per id_jenis (the first one found).
         */
        $sql = "SELECT t.*, j.nama_jenis, j.harga_default, j.is_recurring, k.nama_kategori, k.kode_akun as kode_kategori
                FROM keuangan_tarif t
                JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                WHERE (t.id_siswa = ?) OR (t.id_kelas = ? AND t.id_siswa IS NULL)
                ORDER BY j.id_jenis, t.id_siswa DESC"; // DESC puts non-null (student ID) first
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_siswa, $id_kelas]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Deduplicate in PHP: keep the first occurrence of each id_jenis (which is the most specific due to ORDER BY)
        $uniqueRules = [];
        $seenJenis = [];
        foreach ($rows as $row) {
            if (!in_array($row['id_jenis'], $seenJenis)) {
                $uniqueRules[] = $row;
                $seenJenis[] = $row['id_jenis'];
            }
        }

        return $uniqueRules;
    }

    public function create($data) {
        $sql = "INSERT INTO keuangan_tarif (id_jenis, id_kelas, id_siswa, nominal, keterangan) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_jenis'],
            $data['id_kelas'] ?: null,
            $data['id_siswa'] ?: null,
            str_replace('.', '', $data['nominal']),
            $data['keterangan']
        ]);
    }

    /**
     * Bulk fetch tariffs for an entire class and specific student list
     * Returns a map: [id_siswa][id_jenis] = tariff_row
     */
    public function getTariffsBulk($id_kelas, $student_ids = []) {
        if (empty($student_ids)) return [];

        $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
        
        // Fetch ALL candidate rules: student-specific for our list AND class-wide for this class
        $sql = "SELECT t.*, j.nama_jenis, j.harga_default, j.is_recurring 
                FROM keuangan_tarif t
                JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
                WHERE (t.id_siswa IN ($placeholders)) OR (t.id_kelas = ? AND t.id_siswa IS NULL)
                ORDER BY t.id_siswa DESC"; // Null (class) comes last, student IDs come first
        
        $params = array_merge($student_ids, [$id_kelas]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        // Since we ordered by id_siswa DESC, specific rules come before class rules.
        // We'll fill the map such that student specific rule takes priority for each (student, jenis) pair.
        foreach ($rows as $row) {
            $ij = $row['id_jenis'];
            if ($row['id_siswa']) {
                // Student-specific rule
                $map[$row['id_siswa']][$ij] = $row;
            } else {
                // Class-wide rule: apply to ALL students who don't already have a more specific one
                foreach ($student_ids as $sid) {
                    if (!isset($map[$sid][$ij])) {
                        $map[$sid][$ij] = $row;
                    }
                }
            }
        }
        return $map;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM keuangan_tarif WHERE id_tarif = ?");
        return $stmt->execute([$id]);
    }
}
