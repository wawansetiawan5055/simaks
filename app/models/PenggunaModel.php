<?php
class PenggunaModel {
    
    // Mencari user berdasarkan username (Login)
    public static function findByUsername($pdo, $username) {
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // [REVISI] Mengambil ID Peran DAN Nama Peran
    public static function getRoles($pdo, $user_id) {
        $stmt = $pdo->prepare(
            "SELECT T2.id_peran, T2.nama_peran 
             FROM pengguna_peran AS T1 
             JOIN peran AS T2 ON T1.id_peran = T2.id_peran
             WHERE T1.id_pengguna = ?"
        );
        $stmt->execute([$user_id]);
        // Mengembalikan array assoc: [['id_peran'=>1, 'nama_peran'=>'Admin'], ...]
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}