<?php
class ProfilSiswaModel {
    
    // Ambil profil (parent info & files) berdasarkan id_siswa
    public static function getBySiswaId($pdo, $id_siswa) {
        // Data ortu dan files ada di tabel profil_siswa
        $stmt = $pdo->prepare("SELECT * FROM profil_siswa WHERE id_siswa = ?");
        $stmt->execute([$id_siswa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Save Data (Text) - Parent Info
    public static function save($pdo, $data) {
        $id_siswa = $data['id_siswa'];

        // Cek existing
        $existing = self::getBySiswaId($pdo, $id_siswa);

        if ($existing) {
            // Update
            $sql = "UPDATE profil_siswa SET 
                    nama_ayah = ?, pekerjaan_ayah = ?, telp_ayah = ?,
                    nama_ibu = ?, pekerjaan_ibu = ?, telp_ibu = ?,
                    nama_wali = ?, pekerjaan_wali = ?, telp_wali = ?, alamat_wali = ?
                    WHERE id_siswa = ?";
            $params = [
                $data['nama_ayah'], $data['pekerjaan_ayah'], $data['telp_ayah'],
                $data['nama_ibu'], $data['pekerjaan_ibu'], $data['telp_ibu'],
                $data['nama_wali'], $data['pekerjaan_wali'], $data['telp_wali'], $data['alamat_wali'],
                $id_siswa
            ];
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        } else {
            // Insert
            $sql = "INSERT INTO profil_siswa (
                    id_siswa, 
                    nama_ayah, pekerjaan_ayah, telp_ayah,
                    nama_ibu, pekerjaan_ibu, telp_ibu,
                    nama_wali, pekerjaan_wali, telp_wali, alamat_wali
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $id_siswa,
                $data['nama_ayah'], $data['pekerjaan_ayah'], $data['telp_ayah'],
                $data['nama_ibu'], $data['pekerjaan_ibu'], $data['telp_ibu'],
                $data['nama_wali'], $data['pekerjaan_wali'], $data['telp_wali'], $data['alamat_wali']
            ];
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        }
    }

    // Update File
    public static function updateFile($pdo, $id_siswa, $column, $filename) {
        $existing = self::getBySiswaId($pdo, $id_siswa);
        if (!$existing) {
             // Create blank record if needed
             $pdo->prepare("INSERT INTO profil_siswa (id_siswa) VALUES (?)")->execute([$id_siswa]);
        }
        
        $sql = "UPDATE profil_siswa SET $column = ? WHERE id_siswa = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$filename, $id_siswa]);
    }

    // ==========================================
    // FUNGSI PENGAJUAN PERUBAHAN DATA
    // ==========================================

    public static function ajukanPerubahan($pdo, $id_siswa, $kategori, $data_json) {
        $sql = "INSERT INTO pengajuan_perubahan_data (id_siswa, kategori, data_perubahan, status) VALUES (?, ?, ?, 'Menunggu')";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_siswa, $kategori, $data_json]);
    }

    public static function getPengajuanSiswa($pdo, $id_siswa) {
        $sql = "SELECT * FROM pengajuan_perubahan_data WHERE id_siswa = ? ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPengajuanMenunggu($pdo) {
        // Gabung dengan data siswa untuk melihat nama
        $sql = "SELECT p.*, s.nama, s.nisn 
                FROM pengajuan_perubahan_data p
                JOIN siswa s ON p.id_siswa = s.id_siswa
                WHERE p.status = 'Menunggu' 
                ORDER BY p.created_at ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPengajuanById($pdo, $id_pengajuan) {
        $sql = "SELECT p.*, s.nama 
                FROM pengajuan_perubahan_data p
                JOIN siswa s ON p.id_siswa = s.id_siswa 
                WHERE p.id_pengajuan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_pengajuan]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateStatusPengajuan($pdo, $id_pengajuan, $status, $catatan) {
        $sql = "UPDATE pengajuan_perubahan_data SET status = ?, catatan_admin = ? WHERE id_pengajuan = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$status, $catatan, $id_pengajuan]);
    }
}
?>
