<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(array $config = []) : PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = $config['host'] ?? ($_ENV['CBT_DB_HOST'] ?? '127.0.0.1');
        $db   = $config['database'] ?? ($_ENV['CBT_DB_NAME'] ?? 'db_simaks_cbt');
        $user = $config['username'] ?? ($_ENV['CBT_DB_USER'] ?? 'root');
        $pass = $config['password'] ?? ($_ENV['CBT_DB_PASS'] ?? '');
        $charset = $config['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$instance = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage(), (int)$e->getCode());
        }

        return self::$instance;
    }

    public static function getInstance(): ?PDO
    {
        return self::$instance;
    }
}
