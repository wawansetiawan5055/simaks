<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = connect_db();
    $sql = file_get_contents(__DIR__ . '/../sql/20240406_create_quotes_table.sql');
    
    if ($sql === false) {
        throw new Exception("Gagal membaca file SQL migrasi.");
    }

    $pdo->exec($sql);
    echo "✅ Tabel `quotes` berhasil dibuat atau sudah ada.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
