<?php
/**
 * Simple .env file loader untuk SIMAKS
 * Membaca file .env di root project dan load ke $_ENV
 * 
 * Usage: require_once __DIR__ . '/../config/env.php';
 * 
 * Kemudian bisa diakses via:
 * - getenv('DB_HOST')
 * - $_ENV['DB_HOST']
 */

$env_file = __DIR__ . '/../.env';
$env_local_file = __DIR__ . '/../.env.local';

// Prioritas: .env.local (untuk override lokal) > .env
$file_to_load = file_exists($env_local_file) ? $env_local_file : (file_exists($env_file) ? $env_file : null);

if ($file_to_load && is_readable($file_to_load)) {
    $lines = file($file_to_load, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (substr($value, 0, 1) === '"' && substr($value, -1) === '"') {
                $value = substr($value, 1, -1);
            } elseif (substr($value, 0, 1) === "'" && substr($value, -1) === "'") {
                $value = substr($value, 1, -1);
            }
            
            // Set to $_ENV only (putenv may be disabled on some servers)
            if (!empty($key)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

/**
 * Helper function untuk getenv() kompatibilitas
 * Cek $_ENV dulu sebelum getenv()
 */
if (!function_exists('env_get')) {
    function env_get($key, $default = null) {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        return $default;
    }
}
