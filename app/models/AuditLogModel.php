<?php
// app/models/AuditLogModel.php

class AuditLogModel {
    
    /**
     * Mencatat aktivitas pengguna ke dalam database.
     * 
     * @param PDO $pdo Koneksi database
     * @param string $aksi Jenis aksi (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, ACCESS_DENIED, etc.)
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
            
            if ($user_id === null) $user_id = 0; 
            
            // 2. Ambil Info Real Client IP (Mendukung Cloudflare Tunnel, Nginx Reverse Proxy)
            $ip_address = function_exists('get_client_ip') ? get_client_ip() : ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            // Potong User Agent jika terlalu panjang (Database limit 255)
            if (strlen($user_agent) > 255) {
                $user_agent = substr($user_agent, 0, 252) . '...';
            }

            // 3. Insert ke Database
            $sql = "INSERT INTO audit_log (id_pengguna, aksi, target_tabel, deskripsi, ip_address, user_agent, waktu) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user_id, 
                strtoupper(trim($aksi)), 
                $target_tabel, 
                $deskripsi, 
                $ip_address, 
                $user_agent
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("Audit Log Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil daftar log dengan detail pengguna, peran, dan pencarian komprehensif
     */
    public static function getAll(PDO $pdo, $filters = [], $limit = 50, $offset = 0) {
        $sql = "SELECT 
                    l.*, 
                    u.username,
                    u.nama_pengguna,
                    COALESCE(g.nama, s.nama, u.nama_pengguna, 'System / Guest') AS nama_lengkap,
                    (SELECT GROUP_CONCAT(DISTINCT p.nama_peran SEPARATOR ', ')
                     FROM pengguna_peran pp 
                     JOIN peran p ON pp.id_peran = p.id_peran 
                     WHERE pp.id_pengguna = l.id_pengguna) AS roles
                FROM audit_log l 
                LEFT JOIN pengguna u ON l.id_pengguna = u.id_pengguna 
                LEFT JOIN guru g ON u.id_pengguna = g.id_pengguna
                LEFT JOIN siswa s ON u.id_pengguna = s.id_pengguna
                WHERE 1=1";
        
        $params = [];
        
        // Filter Keyword Pencarian
        if (!empty($filters['q'])) {
            $q = '%' . trim($filters['q']) . '%';
            $sql .= " AND (l.deskripsi LIKE ? OR l.target_tabel LIKE ? OR l.ip_address LIKE ? OR u.username LIKE ? OR u.nama_pengguna LIKE ? OR g.nama LIKE ? OR s.nama LIKE ?)";
            $params = array_merge($params, [$q, $q, $q, $q, $q, $q, $q]);
        }

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
        
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql .= " ORDER BY l.id_log DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Hitung total untuk pagination dengan filter lengkap
    public static function countAll(PDO $pdo, $filters = []) {
        $sql = "SELECT COUNT(*) FROM audit_log l 
                LEFT JOIN pengguna u ON l.id_pengguna = u.id_pengguna 
                LEFT JOIN guru g ON u.id_pengguna = g.id_pengguna
                LEFT JOIN siswa s ON u.id_pengguna = s.id_pengguna
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['q'])) {
            $q = '%' . trim($filters['q']) . '%';
            $sql .= " AND (l.deskripsi LIKE ? OR l.target_tabel LIKE ? OR l.ip_address LIKE ? OR u.username LIKE ? OR u.nama_pengguna LIKE ? OR g.nama LIKE ? OR s.nama LIKE ?)";
            $params = array_merge($params, [$q, $q, $q, $q, $q, $q, $q]);
        }

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
        return (int)$stmt->fetchColumn();
    }
    
    // Ambil distinct aksi untuk dropdown filter
    public static function getDistinctActions(PDO $pdo) {
        $stmt = $pdo->query("SELECT DISTINCT aksi FROM audit_log WHERE aksi IS NOT NULL AND aksi != '' ORDER BY aksi ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Statistik Ringkasan Audit Log
    public static function getSummaryStats(PDO $pdo) {
        $today = date('Y-m-d');
        
        // Total All
        $totalAll = $pdo->query("SELECT COUNT(*) FROM audit_log")->fetchColumn();
        
        // Total Login Hari Ini
        $stmtLogin = $pdo->prepare("SELECT COUNT(*) FROM audit_log WHERE aksi IN ('LOGIN', 'LOGIN_QR') AND DATE(waktu) = ?");
        $stmtLogin->execute([$today]);
        $loginToday = $stmtLogin->fetchColumn();

        // Total Perubahan Data Hari Ini (INSERT, UPDATE, DELETE, IMPORT)
        $stmtData = $pdo->prepare("SELECT COUNT(*) FROM audit_log WHERE aksi IN ('CREATE', 'INSERT', 'UPDATE', 'DELETE', 'IMPORT') AND DATE(waktu) = ?");
        $stmtData->execute([$today]);
        $dataChangesToday = $stmtData->fetchColumn();

        // Total Pengguna Unik Aktif Hari Ini
        $stmtUsers = $pdo->prepare("SELECT COUNT(DISTINCT id_pengguna) FROM audit_log WHERE id_pengguna > 0 AND DATE(waktu) = ?");
        $stmtUsers->execute([$today]);
        $uniqueUsersToday = $stmtUsers->fetchColumn();

        return [
            'total_all' => (int)$totalAll,
            'login_today' => (int)$loginToday,
            'data_changes_today' => (int)$dataChangesToday,
            'unique_users_today' => (int)$uniqueUsersToday
        ];
    }
}

