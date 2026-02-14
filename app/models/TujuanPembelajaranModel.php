<?php
class TujuanPembelajaranModel {
    public static function getAllByMapel($pdo, $id_mapel) {
        $stmt = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_mapel = ? ORDER BY kode_tp");
        $stmt->execute([$id_mapel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function save($pdo, $data) {
        $stmt = $pdo->prepare("INSERT INTO tujuan_pembelajaran (id_mapel, kode_tp, deskripsi_tp) VALUES (?, ?, ?)");
        $stmt->execute([$data['id_mapel'], $data['kode_tp'], $data['deskripsi_tp']]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM tujuan_pembelajaran WHERE id_tp = ?");
        $stmt->execute([$id]);
    }
}