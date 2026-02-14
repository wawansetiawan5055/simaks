<?php
class GuruModel {
    
    /**
     * REVISI:
     * 1. Menambahkan kode_guru ke SELECT.
     */
    public static function all($pdo) {
        // Mengambil semua kolom, termasuk 'kode_guru'
        return $pdo->query("SELECT * FROM guru WHERE status!='Pensiun' ORDER BY nama ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REVISI:
     * 1. Menambahkan kode_guru ke SELECT.
     */
    public static function find($pdo, $id) {
        // Mengambil semua kolom, termasuk 'kode_guru'
        $stmt = $pdo->prepare("SELECT * FROM guru WHERE id_guru=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * REVISI:
     * 1. Menambahkan kode_guru ke query UPDATE dan INSERT.
     */
    public static function save($pdo, $data) {
        // Menyiapkan parameter, termasuk data baru
        $params = [
            $data['nama'],
            $data['kode_guru'] ?? null,
            $data['nuptk'] ?? null,
            $data['nik'] ?? null,
            $data['jk'],
            $data['tempat_lahir'] ?? null,
            $data['tanggal_lahir'] ?? null,
            $data['status_kepegawaian'] ?? null,
            $data['status'] ?? 'Aktif'
        ];

        if (!empty($data['id_guru'])) {
            // UPDATE
            $sql = "UPDATE guru SET nama=?, kode_guru=?, nuptk=?, nik=?, jk=?, 
                        tempat_lahir=?, tanggal_lahir=?, status_kepegawaian=?, status=? 
                    WHERE id_guru=?";
            $params[] = $data['id_guru']; // Tambahkan ID di akhir
            $stmt = $pdo->prepare($sql);
        } else {
            // INSERT
            $sql = "INSERT INTO guru (nama, kode_guru, nuptk, nik, jk, tempat_lahir, tanggal_lahir, 
                                     status_kepegawaian, status) 
                    VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->execute($params);
    }
    
    public static function delete($pdo, $id) {
        // (Pastikan Anda sudah menerapkan Soft Delete atau ON DELETE CASCADE
        // jika guru ini terhubung dengan tabel guru_mapel atau jadwal)
        $stmt = $pdo->prepare("DELETE FROM guru WHERE id_guru=?");
        $stmt->execute([$id]);
    }
}
?>