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
        $jenis_kelas = $data['jenis_kelas'] ?? 'reguler';
        if (!in_array($jenis_kelas, ['reguler', 'pjj', 'menginduk'])) {
            $jenis_kelas = 'reguler';
        }
        if (!empty($data['id_kelas'])) {
            // UPDATE: tidak perlu update id_ta (tetap seperti semula)
            $stmt = $pdo->prepare("UPDATE kelas SET nama_kelas=?, tingkat=?, jenis_kelas=? WHERE id_kelas=?");
            $stmt->execute([$data['nama_kelas'], $data['tingkat'], $jenis_kelas, $data['id_kelas']]);
        } else {
            // INSERT: tambahkan id_ta dan jenis_kelas
            $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, tingkat, jenis_kelas, id_ta) VALUES (?,?,?,?)");
            $stmt->execute([$data['nama_kelas'], $data['tingkat'], $jenis_kelas, $data['id_ta']]);
        }
    }
    
    public static function updateJenisKelas($pdo, $id_kelas, $jenis_kelas) {
        if (!in_array($jenis_kelas, ['reguler', 'pjj', 'menginduk'])) {
            $jenis_kelas = 'reguler';
        }
        $stmt = $pdo->prepare("UPDATE kelas SET jenis_kelas=? WHERE id_kelas=?");
        return $stmt->execute([$jenis_kelas, (int)$id_kelas]);
    }
    
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM kelas WHERE id_kelas=?");
        $stmt->execute([$id]);
    }

    public static function countByTa($pdo, $id_ta) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM kelas WHERE id_ta = ?");
        $stmt->execute([$id_ta]);
        return (int) $stmt->fetchColumn();
    }

    public static function copyFromTa($pdo, $source_ta, $target_ta) {
        if ($source_ta === $target_ta) {
            return 0;
        }

        $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, tingkat, id_ta)
            SELECT nama_kelas, tingkat, ? FROM kelas WHERE id_ta = ?");
        $stmt->execute([$target_ta, $source_ta]);
        return $stmt->rowCount();
    }
}