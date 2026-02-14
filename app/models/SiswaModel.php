<?php
class SiswaModel {
    public static function all($pdo, $search = null) {
        if (!empty($search) && trim($search) !== '') {
            $q = '%' . str_replace(' ', '%', trim($search)) . '%';
            $stmt = $pdo->prepare("SELECT * FROM siswa WHERE nama LIKE :q OR nisn LIKE :q OR nipd LIKE :q ORDER BY nama ASC");
            $stmt->execute([':q' => $q]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pdo->query("SELECT * FROM siswa ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
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