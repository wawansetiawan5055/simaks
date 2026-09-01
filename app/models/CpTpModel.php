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

    public static function getAllTpByCps($pdo, $id_cps) {
        if (empty($id_cps)) return [];
        $placeholders = implode(',', array_fill(0, count($id_cps), '?'));
        $stmt = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_cp IN ($placeholders) ORDER BY kode_tp");
        $stmt->execute($id_cps);
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

    // Menyimpan TP baru (dengan materi/topik)
    public static function saveTp($pdo, $data) {
        $materi = trim($data['materi'] ?? '');
        $stmt = $pdo->prepare("INSERT INTO tujuan_pembelajaran (id_cp, id_mapel, kode_tp, materi, deskripsi_tp) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['id_cp'], $data['id_mapel'], $data['kode_tp'], $materi, $data['deskripsi_tp']]);
    }
    
    // Mengambil satu CP berdasarkan ID
    public static function getCpById($pdo, $id_cp) {
        $stmt = $pdo->prepare("SELECT * FROM capaian_pembelajaran WHERE id_cp = ?");
        $stmt->execute([$id_cp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mengupdate CP
    public static function updateCp($pdo, $id_cp, $deskripsi_cp) {
        $stmt = $pdo->prepare("UPDATE capaian_pembelajaran SET deskripsi_cp = ? WHERE id_cp = ?");
        $stmt->execute([$deskripsi_cp, $id_cp]);
    }

    // Mengupdate TP (dengan materi/topik)
    public static function updateTp($pdo, $id_tp, $kode_tp, $deskripsi_tp, $materi = '') {
        $stmt = $pdo->prepare("UPDATE tujuan_pembelajaran SET kode_tp = ?, materi = ?, deskripsi_tp = ? WHERE id_tp = ?");
        $stmt->execute([$kode_tp, $materi, $deskripsi_tp, $id_tp]);
    }

    // Bulk save TP (dengan materi/topik)
    public static function bulkSaveTp($pdo, $id_cp, $id_mapel, $tp_items) {
        $stmt = $pdo->prepare("INSERT INTO tujuan_pembelajaran (id_cp, id_mapel, kode_tp, materi, deskripsi_tp) VALUES (?, ?, ?, ?, ?)");
        $count = 0;
        foreach ($tp_items as $tp) {
            $kode = trim($tp['kode_tp'] ?? '');
            $materi = trim($tp['materi'] ?? '');
            $deskripsi = trim($tp['deskripsi_tp'] ?? '');
            if (!empty($kode) && !empty($deskripsi)) {
                $stmt->execute([$id_cp, $id_mapel, $kode, $materi, $deskripsi]);
                $count++;
            }
        }
        return $count;
    }

    // Menghapus CP (dan TP terkait akan terhapus otomatis karena ON DELETE CASCADE)
    public static function deleteCp($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM capaian_pembelajaran WHERE id_cp = ?");
        $stmt->execute([$id]);
    }

    // Mengambil TP yang belum memiliki materi/topik pada satu CP
    public static function getEmptyTopicTpsByCp($pdo, $id_cp) {
        $stmt = $pdo->prepare("SELECT id_tp, kode_tp, deskripsi_tp FROM tujuan_pembelajaran WHERE id_cp = ? AND (materi IS NULL OR TRIM(materi) = '') ORDER BY id_tp ASC");
        $stmt->execute([$id_cp]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengupdate hanya kolom materi pada TP
    public static function updateTpMateri($pdo, $id_tp, $materi) {
        $stmt = $pdo->prepare("UPDATE tujuan_pembelajaran SET materi = ? WHERE id_tp = ?");
        $stmt->execute([$materi, $id_tp]);
    }

    // Menghapus TP
    public static function deleteTp($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM tujuan_pembelajaran WHERE id_tp = ?");
        $stmt->execute([$id]);
    }
}