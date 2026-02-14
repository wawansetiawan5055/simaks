<?php
class KelasModel {
    /**
     * Ambil semua kelas untuk tahun ajaran aktif
     * @param PDO $pdo
     * @param int $id_ta - ID tahun ajaran dari session
     */
    public static function all($pdo, $id_ta) {
        $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas ASC");
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function find($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_kelas=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function save($pdo, $data) {
        if (!empty($data['id_kelas'])) {
            // UPDATE: tidak perlu update id_ta (tetap seperti semula)
            $stmt = $pdo->prepare("UPDATE kelas SET nama_kelas=?, tingkat=? WHERE id_kelas=?");
            $stmt->execute([$data['nama_kelas'], $data['tingkat'], $data['id_kelas']]);
        } else {
            // INSERT: tambahkan id_ta
            $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, tingkat, id_ta) VALUES (?,?,?)");
            $stmt->execute([$data['nama_kelas'], $data['tingkat'], $data['id_ta']]);
        }
    }
    
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM kelas WHERE id_kelas=?");
        $stmt->execute([$id]);
    }
}