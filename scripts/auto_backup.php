<?php
/**
 * SIMAKS AUTOMATED BACKUP ENGINE
 * 
 * Fitur:
 * 1. Export seluruh database MySQL (Struktur + Data)
 * 2. Kompresi otomatis ke format .ZIP hemat memori (dari ~40MB menjadi ~3MB)
 * 3. Simpan ke folder lokal / Google Drive folder
 * 4. Otomatis kirim file .ZIP ke Telegram Bot (jika token diisi)
 * 5. Auto-cleanup: Menghapus backup lama > 14 hari agar harddisk tidak penuh
 * 
 * Penggunaan CLI: php scripts/auto_backup.php
 * Penggunaan Windows: Dijalankan via Task Scheduler atau batch file
 */

// Set timezone & memory limit
date_default_timezone_set('Asia/Jakarta');
ini_set('memory_limit', '512M');
set_time_limit(600);

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/db.php';

echo "====================================================\n";
echo "       SIMAKS AUTOMATED DATABASE BACKUP ENGINE       \n";
echo "====================================================\n";
echo "[" . date('Y-m-d H:i:s') . "] Memulai proses backup...\n";

// 1. Setup Direktori Backup
$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$timestamp = date('Y-m-d_H-i-s');
$sqlFilename = "simaks_db_{$timestamp}.sql";
$sqlFilePath = $backupDir . '/' . $sqlFilename;
$zipFilename = "simaks_backup_{$timestamp}.zip";
$zipFilePath = $backupDir . '/' . $zipFilename;

// 2. Hubungkan ke Database
try {
    $pdo = connect_db();
    echo "[" . date('Y-m-d H:i:s') . "] Koneksi database berhasil.\n";
} catch (Exception $e) {
    die("[" . date('Y-m-d H:i:s') . "] ERROR: Gagal koneksi DB: " . $e->getMessage() . "\n");
}

// 3. Dump Database via PDO
try {
    $handle = fopen($sqlFilePath, 'w');
    if (!$handle) {
        throw new Exception("Gagal membuat file SQL di: " . $sqlFilePath);
    }

    fwrite($handle, "-- ====================================================\n");
    fwrite($handle, "-- SIMAKS DATABASE BACKUP\n");
    fwrite($handle, "-- Tanggal: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- ====================================================\n\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
    fwrite($handle, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
    fwrite($handle, "SET NAMES utf8mb4;\n\n");

    // Ambil semua tabel
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "[" . date('Y-m-d H:i:s') . "] Mengekspor " . count($tables) . " tabel...\n";

    foreach ($tables as $table) {
        // Struktur Tabel
        fwrite($handle, "\n-- --------------------------------------------------\n");
        fwrite($handle, "-- Struktur Tabel: `{$table}`\n");
        fwrite($handle, "-- --------------------------------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
        
        $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
        $createRow = $createStmt->fetch(PDO::FETCH_NUM);
        fwrite($handle, $createRow[1] . ";\n\n");

        // Data Tabel
        $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
        $numRows = $dataStmt->rowCount();

        if ($numRows > 0) {
            fwrite($handle, "-- Data Tabel: `{$table}` (" . $numRows . " baris)\n");
            
            // Insert in chunks of 500
            $chunk = [];
            while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
                $escapedValues = array_map(function($val) use ($pdo) {
                    if ($val === null) return 'NULL';
                    return $pdo->quote($val);
                }, array_values($row));
                
                $chunk[] = "(" . implode(", ", $escapedValues) . ")";

                if (count($chunk) >= 500) {
                    $cols = array_map(function($col) { return "`{$col}`"; }, array_keys($row));
                    fwrite($handle, "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES\n" . implode(",\n", $chunk) . ";\n");
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                $cols = array_map(function($col) { return "`{$col}`"; }, array_keys($row));
                fwrite($handle, "INSERT INTO `{$table}` (" . implode(", ", $cols) . ") VALUES\n" . implode(",\n", $chunk) . ";\n");
            }
        }
    }

    fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
    fclose($handle);

    $sqlSize = filesize($sqlFilePath);
    echo "[" . date('Y-m-d H:i:s') . "] Ekspor SQL selesai! Ukuran mentah: " . round($sqlSize / 1024 / 1024, 2) . " MB\n";

} catch (Exception $e) {
    if (file_exists($sqlFilePath)) unlink($sqlFilePath);
    die("[" . date('Y-m-d H:i:s') . "] ERROR saat ekspor SQL: " . $e->getMessage() . "\n");
}

// 4. Kompresi ke File .ZIP
echo "[" . date('Y-m-d H:i:s') . "] Mengompresi file ke ZIP...\n";
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        $zip->addFile($sqlFilePath, $sqlFilename);
        $zip->close();
        
        // Hapus file .sql mentah setelah di-zip
        unlink($sqlFilePath);
        
        $zipSize = filesize($zipFilePath);
        echo "[" . date('Y-m-d H:i:s') . "] Kompresi berhasil! File ZIP: {$zipFilename} (" . round($zipSize / 1024 / 1024, 2) . " MB)\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] PERINGATAN: Gagal membuat ZIP, file tetap dalam format .SQL\n";
        $zipFilePath = $sqlFilePath;
        $zipFilename = $sqlFilename;
    }
} else {
    echo "[" . date('Y-m-d H:i:s') . "] ZipArchive tidak tersedia di server, file disimpan sebagai .SQL\n";
    $zipFilePath = $sqlFilePath;
    $zipFilename = $sqlFilename;
}

// 5. Kirim Otomatis ke Telegram Bot (Jika Dikonfigurasi)
$telegramToken = env_get('TELEGRAM_BOT_TOKEN') ?: getenv('TELEGRAM_BOT_TOKEN') ?: '';
$telegramChatId = env_get('TELEGRAM_CHAT_ID') ?: getenv('TELEGRAM_CHAT_ID') ?: '';

if (!empty($telegramToken) && !empty($telegramChatId)) {
    echo "[" . date('Y-m-d H:i:s') . "] Mengirimkan file cadangan ke Telegram Bot...\n";
    
    $caption = "💾 *SIMAKS AUTOMATED DATABASE BACKUP*\n"
             . "📅 Tanggal: `" . date('d-m-Y H:i:s') . " WIB`\n"
             . "📦 File: `" . $zipFilename . "`\n"
             . "📊 Ukuran: `" . round(filesize($zipFilePath) / 1024 / 1024, 2) . " MB`\n"
             . "✅ Status: Cadangan Berhasil";

    $url = "https://api.telegram.org/bot{$telegramToken}/sendDocument";

    $postData = [
        'chat_id' => $telegramChatId,
        'caption' => $caption,
        'parse_mode' => 'Markdown',
        'document' => new CURLFile(realpath($zipFilePath))
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response && strpos($response, '"ok":true') !== false) {
        echo "[" . date('Y-m-d H:i:s') . "] ✅ Berhasil terkirim ke Telegram!\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] ⚠️ Gagal kirim Telegram: " . ($curlError ?: $response) . "\n";
    }
} else {
    echo "[" . date('Y-m-d H:i:s') . "] [Info] Telegram Bot belum dikonfigurasi (bisa diaktifkan di .env).\n";
}

// 6. Auto-Cleanup: Hapus Backup Lama (> 14 Hari)
echo "[" . date('Y-m-d H:i:s') . "] Membersihkan backup lama (> 14 hari)...\n";
$files = glob($backupDir . '/*');
$now = time();
$deletedCount = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        if ($now - filemtime($file) >= 14 * 24 * 60 * 60) {
            unlink($file);
            $deletedCount++;
        }
    }
}
if ($deletedCount > 0) {
    echo "[" . date('Y-m-d H:i:s') . "] Menghapus {$deletedCount} file backup lama.\n";
}

echo "====================================================\n";
echo "[" . date('Y-m-d H:i:s') . "] PROSES BACKUP SELESAI DENGAN SUKSES! 🎉\n";
echo "Lokasi File: {$zipFilePath}\n";
echo "====================================================\n";
