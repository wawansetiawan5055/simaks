<?php
/**
 * CBT - Database Connection
 * Database terpisah: db_simaks_cbt
 * Mengikuti pola koneksi SIMAKS (PDO, Persistent)
 */
if (!function_exists('cbt_connect_db')) {
    function cbt_connect_db()
    {
        $host = getenv('CBT_DB_HOST') ?: 'localhost';
        $dbname = getenv('CBT_DB_NAME') ?: 'db_simaks';
        $user = getenv('CBT_DB_USER') ?: 'administrator';
        $pass = getenv('CBT_DB_PASS') ?: '20247166';

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
            ];
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die(json_encode(['status' => 'error', 'message' => 'Gagal koneksi DB CBT: ' . $e->getMessage()]));
        }
    }
}
