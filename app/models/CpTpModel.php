<?php
class CpTpModel {
    // Mengambil semua CP untuk mapel dan fase tertentu
    public static function getAllCpByMapelAndFase($pdo, $id_mapel, $fase) {
        $stmt = $pdo->prepare("SELECT * FROM capaian_pembelajaran WHERE id_mapel = ? AND fase = ? ORDER BY id_cp");
        $stmt->execute([$id_mapel, $fase]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil semua TP yang terhubung ke CP tertentu
    public static function getAllTpByCp($pdo, $id_cp) {
        $stmt = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_cp = ? ORDER BY kode_tp");
        $stmt->execute([$id_cp]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // FUNGSI YANG HILANG SEBELUMNYA - SEKARANG DITAMBAHKAN
    public static function getTpByMapel($pdo, $id_mapel) {
        $stmt = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_mapel = ? ORDER BY kode_tp");
        $stmt->execute([$id_mapel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // AKHIR FUNGSI PENTING

    // Menyimpan CP baru
    public static function saveCp($pdo, $data) {
        $stmt = $pdo->prepare("INSERT INTO capaian_pembelajaran (id_mapel, fase, deskripsi_cp) VALUES (?, ?, ?)");
        $stmt->execute([$data['id_mapel'], $data['fase'], $data['deskripsi_cp']]);
        return $pdo->lastInsertId(); // Mengembalikan ID CP yang baru dibuat
    }

    // Menyimpan TP baru
    public static function saveTp($pdo, $data) {
        $stmt = $pdo->prepare("INSERT INTO tujuan_pembelajaran (id_cp, id_mapel, kode_tp, deskripsi_tp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['id_cp'], $data['id_mapel'], $data['kode_tp'], $data['deskripsi_tp']]);
    }
    
    // Menghapus CP (dan TP terkait akan terhapus otomatis karena ON DELETE CASCADE)
    public static function deleteCp($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM capaian_pembelajaran WHERE id_cp = ?");
        $stmt->execute([$id]);
    }

    // Menghapus TP
    public static function deleteTp($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM tujuan_pembelajaran WHERE id_tp = ?");
        $stmt->execute([$id]);
    }
}