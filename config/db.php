<?php
// C:\xampp\htdocs\simaks\config\db.php

/**
 * Mendefinisikan fungsi koneksi database PDO
 * yang akan dipanggil di index.php
 * 
 * PENTING: Untuk aPanel, ubah credentials di bawah sesuai aPanel Anda
 */
if (!function_exists('connect_db')) {
    function connect_db() {
        // === KONFIGURASI DATABASE ===
        // JIKA DI APANEL: Update nilai-nilai di bawah sesuai dengan credentials aPanel Anda
        // Bisa dilihat di aPanel > Databases atau phpMyAdmin
        
        // Option 1: Deteksi lingkungan (uncomment jika perlu)
        // $env = getenv('APP_ENV') ?: 'local';
        
        // Koneksi PDO ke MySQL
        $host = getenv('DB_HOST') ?: "localhost";      // Ubah ke hostname DB aPanel jika perlu (biasanya localhost atau 127.0.0.1)
        $dbname = getenv('DB_NAME') ?: "db_simaks";     // Ubah ke nama database aPanel
        $user = getenv('DB_USER') ?: "administrator";            // Ubah ke username database aPanel
        $pass = getenv('DB_PASS') ?: "20247166";                // Ubah ke password database aPanel

        try {
            // Menggunakan DSN (Data Source Name)
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true, // Persistent connections untuk performa concurrent users
            ];
            
            $pdo = new PDO($dsn, $user, $pass, $options);
            return $pdo; // <-- Mengembalikan objek koneksi
            
        } catch (PDOException $e) {
            // Berhenti jika gagal koneksi
            die("Gagal koneksi DB: " . $e->getMessage() . "\n" .
                "Host: $host | DB: $dbname | User: $user\n" .
                "Pastikan credentials di config/db.php sudah benar untuk environment aPanel Anda.");
        }
    }
}

// ------------------------------------------------------------------
// [CBT] tambahan koneksi database opsional
// Gunakan variabel lingkungan CBT_DB_*, fallback ke koneksi utama jika tidak ada.
// Diinstallasi terpisah CBT bisa memiliki database sendiri.
// ------------------------------------------------------------------
if (!function_exists('cbt_connect_db')) {
    function cbt_connect_db() {
        $host = getenv('CBT_DB_HOST') ?: getenv('DB_HOST') ?: "localhost";
        $dbname = getenv('CBT_DB_NAME') ?: getenv('DB_NAME') ?: "db_cbt";
        $user = getenv('CBT_DB_USER') ?: getenv('DB_USER') ?: "";
        $pass = getenv('CBT_DB_PASS') ?: getenv('DB_PASS') ?: "";

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true,
            ];

            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Gagal koneksi CBT DB: " . $e->getMessage() . "\n" .
                "Host: $host | DB: $dbname | User: $user\n");
        }
    }
}
