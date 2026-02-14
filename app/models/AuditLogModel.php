<?php
// app/models/AuditLogModel.php

class AuditLogModel {
    
    /**
     * Mencatat aktivitas pengguna ke dalam database.
     * 
     * @param PDO $pdo Koneksi database
     * @param string $aksi Jenis aksi (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, ACCESS_DENIED)
     * @param string $deskripsi Detail aktivitas
     * @param string|null $target_tabel Tabel yang terdampak (opsional)
     * @param int|null $user_id ID pengguna (jika null, ambil dari session)
     */
    public static function log(PDO $pdo, $aksi, $deskripsi, $target_tabel = null, $user_id = null) {
        try {
            // 1. Ambil User ID jika tidak disediakan
            if ($user_id === null && isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
            }
            
            // Jika masih null (misal: login gagal atau sistem cron), set 0 atau ambil dari konteks lain
            if ($user_id === null) $user_id = 0; 
            
            // 2. Ambil Info Client
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            // Potong User Agent jika terlalu panjang (Database limit 255)
            if (strlen($user_agent) > 255) {
                $user_agent = substr($user_agent, 0, 252) . '...';
            }

            // 3. Insert ke Database
            $sql = "INSERT INTO audit_log (id_pengguna, aksi, target_tabel, deskripsi, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user_id, 
                strtoupper($aksi), 
                $target_tabel, 
                $deskripsi, 
                $ip_address, 
                $user_agent
            ]);
            
            return true;
        } catch (Exception $e) {
            // Jangan sampai error logging menghentikan aplikasi aktivitas utama
            // Cukup catat di error log file server
            error_log("Audit Log Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil daftar log dengan filter
     * 
     * @param PDO $pdo
     * @param array $filters (id_pengguna, aksi, tanggal_mulai, tanggal_akhir)
     * @param int $limit
     * @param int $offset
     */
    public static function getAll(PDO $pdo, $filters = [], $limit = 50, $offset = 0) {
        $sql = "SELECT l.*, u.nama_pengguna 
                FROM audit_log l 
                LEFT JOIN pengguna u ON l.id_pengguna = u.id_pengguna 
                WHERE 1=1";
        
        $params = [];
        
        // Filter User
        if (!empty($filters['id_pengguna'])) {
            $sql .= " AND l.id_pengguna = ?";
            $params[] = $filters['id_pengguna'];
        }
        
        // Filter Aksi
        if (!empty($filters['aksi'])) {
            $sql .= " AND l.aksi = ?";
            $params[] = $filters['aksi'];
        }
        
        // Filter Tanggal
        if (!empty($filters['tanggal_mulai'])) {
            $sql .= " AND DATE(l.waktu) >= ?";
            $params[] = $filters['tanggal_mulai'];
        }
        if (!empty($filters['tanggal_akhir'])) {
            $sql .= " AND DATE(l.waktu) <= ?";
            $params[] = $filters['tanggal_akhir'];
        }
        
        $sql .= " ORDER BY l.waktu DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Hitung total untuk pagination
    public static function countAll(PDO $pdo, $filters = []) {
        $sql = "SELECT COUNT(*) FROM audit_log l WHERE 1=1";
        $params = [];
        
        if (!empty($filters['id_pengguna'])) {
            $sql .= " AND l.id_pengguna = ?";
            $params[] = $filters['id_pengguna'];
        }
        if (!empty($filters['aksi'])) {
            $sql .= " AND l.aksi = ?";
            $params[] = $filters['aksi'];
        }
         if (!empty($filters['tanggal_mulai'])) {
            $sql .= " AND DATE(l.waktu) >= ?";
            $params[] = $filters['tanggal_mulai'];
        }
        if (!empty($filters['tanggal_akhir'])) {
            $sql .= " AND DATE(l.waktu) <= ?";
            $params[] = $filters['tanggal_akhir'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    // Ambil distinct aksi untuk dropdown filter
    public static function getDistinctActions(PDO $pdo) {
        $stmt = $pdo->query("SELECT DISTINCT aksi FROM audit_log ORDER BY aksi ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
