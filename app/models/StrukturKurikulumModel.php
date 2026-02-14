<?php
class StrukturKurikulumModel {
    
    public static function getAll($pdo, $id_ta) {
        $sql = "SELECT sk.*, m.nama_mapel 
                FROM struktur_kurikulum sk
                JOIN mapel m ON sk.id_mapel = m.id_mapel
                WHERE sk.id_ta = ?
                ORDER BY sk.tingkat, m.urutan, m.nama_mapel";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [REVISI ANTI-BUG]
     * Fungsi 'save' sekarang menerima $id_struktur dari Controller, bukan dari $data.
     */
    public static function save($pdo, $data, $id_ta, $id_struktur = null) {
        
        $params = [
            $data['id_mapel'],
            $data['tingkat'],
            $data['kelompok'] ?? 'Umum',
            $data['alokasi_jp_minggu'],
        ];

        // Memeriksa $id_struktur yang dikirim oleh Controller
        if (!empty($id_struktur)) {
            // Update
            $sql = "UPDATE struktur_kurikulum SET 
                    id_mapel=?, tingkat=?, kelompok=?, alokasi_jp_minggu=?
                    WHERE id_struktur = ?";
            $params[] = $id_struktur; // Menambahkan id_struktur ke akhir parameter
            
        } else {
            // Insert
            $sql = "INSERT INTO struktur_kurikulum (id_mapel, tingkat, kelompok, alokasi_jp_minggu, id_ta)
                    VALUES (?, ?, ?, ?, ?)";
            $params[] = $id_ta; // Menambahkan id_ta ke akhir parameter
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM struktur_kurikulum WHERE id_struktur=?");
        return $stmt->execute([$id]);
    }
}