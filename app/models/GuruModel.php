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
        $stmt = $pdo->prepare("SELECT g.*, p.foto as foto_akun 
                               FROM guru g 
                               LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna 
                               WHERE g.id_guru=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * REVISI:
     * 1. Menambahkan kode_guru ke query UPDATE dan INSERT.
     */
    public static function save($pdo, $data) {
        $jenis_guru = $data['jenis_guru'] ?? 'reguler';
        if (!in_array($jenis_guru, ['reguler', 'koordinator_pjj', 'koordinator_menginduk'])) {
            $jenis_guru = 'reguler';
        }
        $lokasi_tugas = !empty($data['lokasi_tugas']) ? trim($data['lokasi_tugas']) : null;

        // Menyiapkan parameter, termasuk data baru
        $params = [
            $data['nama'],
            $jenis_guru,
            $lokasi_tugas,
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
            $sql = "UPDATE guru SET nama=?, jenis_guru=?, lokasi_tugas=?, kode_guru=?, nuptk=?, nik=?, jk=?, 
                        tempat_lahir=?, tanggal_lahir=?, status_kepegawaian=?, status=? 
                    WHERE id_guru=?";
            $params[] = $data['id_guru']; // Tambahkan ID di akhir
            $stmt = $pdo->prepare($sql);
        } else {
            // INSERT
            $sql = "INSERT INTO guru (nama, jenis_guru, lokasi_tugas, kode_guru, nuptk, nik, jk, tempat_lahir, tanggal_lahir, 
                                     status_kepegawaian, status) 
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)";
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