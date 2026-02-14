<?php
class MapelModel {
    
    /**
     * REVISI:
     * 1. Menambahkan kode_mapel dan urutan ke SELECT.
     * 2. Mengurutkan berdasarkan 'urutan' terlebih dahulu (sesuai permintaan Anda).
     */
    public static function all($pdo) {
        return $pdo->query("SELECT id_mapel, nama_mapel, kategori_mapel, kktp, kode_mapel, urutan 
                            FROM mapel 
                            ORDER BY urutan ASC, nama_mapel ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * REVISI:
     * 1. Menambahkan kode_mapel dan urutan ke SELECT.
     */
    public static function find($pdo, $id) {
        $stmt = $pdo->prepare("SELECT id_mapel, nama_mapel, kategori_mapel, kktp, kode_mapel, urutan 
                               FROM mapel WHERE id_mapel=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * REVISI:
     * 1. Menambahkan kode_mapel dan urutan ke query UPDATE dan INSERT.
     */
    public static function save($pdo, $data) {
        // Menyiapkan parameter, termasuk data baru
        $params = [
            $data['nama_mapel'],
            $data['kategori_mapel'],
            $data['kktp'],
            $data['kode_mapel'] ?? null, // Kolom baru
            $data['urutan'] ?? 0       // Kolom baru
        ];

        if (!empty($data['id_mapel'])) {
            // UPDATE
            $sql = "UPDATE mapel SET nama_mapel=?, kategori_mapel=?, kktp=?, kode_mapel=?, urutan=? 
                    WHERE id_mapel=?";
            $params[] = $data['id_mapel']; // Tambahkan ID di akhir
            $stmt = $pdo->prepare($sql);
        } else {
            // INSERT
            $sql = "INSERT INTO mapel (nama_mapel, kategori_mapel, kktp, kode_mapel, urutan) 
                    VALUES (?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
        }
        $stmt->execute($params);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM mapel WHERE id_mapel=?");
        $stmt->execute([$id]);
    }
}
?>