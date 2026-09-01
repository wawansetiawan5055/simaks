<?php
class SiswaModel {
    public static function all($pdo, $id_ta = null, $status = null, $search = null) {
        $conditions = [];
        $params = [];

        // Gunakan CASE WHEN untuk percepsi status secara temporal (sesuai Tahun Ajaran yang sedang dilihat)
        $sql = "SELECT DISTINCT s.*, 
                       k.nama_kelas,
                       ps.id_kelas,
                       CASE 
                         WHEN s.status_aktif = 'Aktif' THEN 'Aktif'
                         WHEN s.status_aktif = 'Lulus' AND sa.id_ta_lulus <= :ta1 THEN 'Lulus'
                         WHEN s.status_aktif = 'Keluar' AND sm.id_ta_mutasi <= :ta2 THEN 'Keluar'
                         ELSE 'Aktif'
                       END AS status_aktif_relatif
                FROM siswa s
                LEFT JOIN siswa_alumni sa ON s.id_siswa = sa.id_siswa
                LEFT JOIN siswa_mutasi sm ON s.id_siswa = sm.id_siswa";

        if ($id_ta) {
            $sql .= " LEFT JOIN penempatan_siswa ps ON (s.id_siswa = ps.id_siswa AND ps.id_ta = :id_ta_join)
                      LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas";
            $conditions[] = "(ps.id_ta = :id_ta_cond OR s.id_ta_masuk = :id_ta_masuk)";
            $params['id_ta_join'] = $id_ta;
            $params['id_ta_cond'] = $id_ta;
            $params['id_ta_masuk'] = $id_ta;
        } else {
            $sql .= " LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                      LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas";
        }

        // Parameter untuk logic CASE WHEN di atas
        $params['ta1'] = $id_ta;
        $params['ta2'] = $id_ta;

        if ($status && $status != 'Semua') {
            if (!$id_ta) {
                // Filter status GLOBAL (jika tahun ajaran tidak dipilih)
                $conditions[] = "s.status_aktif = :status";
                $params['status'] = $status;
            } else {
                // Filter status TEMPORAL (sadar tahun ajaran)
                if ($status == 'Aktif') {
                    // Dianggap Aktif jika: (Global Aktif) ATAU (Belum Lulus: ta_lulus > ta) ATAU (Belum Keluar: ta_mutasi > ta)
                    $conditions[] = "(
                        s.status_aktif = 'Aktif' 
                        OR (s.status_aktif = 'Lulus' AND sa.id_ta_lulus > :ta5)
                        OR (s.status_aktif = 'Keluar' AND sm.id_ta_mutasi > :ta6)
                    )";
                    $params['ta5'] = $id_ta;
                    $params['ta6'] = $id_ta;
                } elseif ($status == 'Lulus') {
                    // Dianggap Lulus jika: Status Master Lulus DAN sudah masuk semester kelulusan (<= ta)
                    $conditions[] = "(s.status_aktif = 'Lulus' AND sa.id_ta_lulus <= :ta7)";
                    $params['ta7'] = $id_ta;
                } elseif ($status == 'Keluar') {
                    // Dianggap Keluar jika: Status Master Keluar DAN sudah masuk semester mutasi (<= ta)
                    $conditions[] = "(s.status_aktif = 'Keluar' AND sm.id_ta_mutasi <= :ta8)";
                    $params['ta8'] = $id_ta;
                } elseif ($status == 'Pindahan') {
                    // Dianggap Pindahan jika: Ada datanya di mutasi_masuk dengan status Diterima
                    $conditions[] = "EXISTS (SELECT 1 FROM mutasi_masuk mm WHERE mm.id_siswa_master = s.id_siswa AND mm.status_penerimaan = 'Diterima')";
                }
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY s.nama ASC";
        
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_null($val) ? PDO::PARAM_NULL : (is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR));
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function find($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // FUNGSI SAVE YANG DIPERBARUI
    public static function save($pdo, $data) {
        if (!empty($data['id_siswa'])) {
            // Logika UPDATE
            $stmt = $pdo->prepare(
                "UPDATE siswa SET 
                    nama=?, nisn=?, nipd=?, nik=?, jk=?, 
                    tempat_lahir=?, tanggal_lahir=?, sekolah_asal=?, status_aktif=?, id_ta_masuk=? 
                 WHERE id_siswa=?"
            );
            $stmt->execute([
                $data['nama'], $data['nisn'], $data['nipd'], $data['nik'], $data['jk'],
                $data['tempat_lahir'], $data['tanggal_lahir'], 
                $data['sekolah_asal'], // Hanya sekolah asal
                $data['status_aktif'], $data['id_ta_masuk'], $data['id_siswa']
            ]);
        } else {
            // Logika INSERT
            $stmt = $pdo->prepare(
                "INSERT INTO siswa (
                    nama, nisn, nipd, nik, jk, tempat_lahir, tanggal_lahir, 
                    sekolah_asal, status_aktif, id_ta_masuk
                 ) VALUES (?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->execute([
                $data['nama'], $data['nisn'], $data['nipd'], $data['nik'], $data['jk'],
                $data['tempat_lahir'], $data['tanggal_lahir'],
                $data['sekolah_asal'], // Hanya sekolah asal
                $data['status_aktif'] ?? 'Aktif',
                $data['id_ta_masuk']
            ]);
        }
    }
    
    /**
     * [REVISI DIMULAI]
     * Fungsi ini diubah untuk "menangkap" PDOException.
     * @return bool True jika sukses, False jika gagal (karena foreign key)
     */
    public static function delete($pdo, $id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM siswa WHERE id_siswa=?");
            $stmt->execute([$id]);
            return true; // Sukses
            
        } catch (PDOException $e) {
            // Cek jika error-nya adalah "Integrity constraint violation"
            // Kode '23000' adalah kode SQLSTATE untuk ini (seperti di screenshot Anda)
            if ($e->getCode() == '23000') {
                // Gagal karena data masih terpakai (foreign key)
                return false; 
            } else {
                // Jika error lain, biarkan aplikasi menampilkannya
                throw $e;
            }
        }
    }
}