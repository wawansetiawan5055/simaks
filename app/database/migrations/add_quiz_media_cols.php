<?php
require_once __DIR__ . '/../../../config/db.php';
$pdo = connect_db();

try {
    // Tambah kolom media untuk soal
    $cols = [
        'file_pertanyaan' => 'VARCHAR(255)',
        'file_a' => 'VARCHAR(255)',
        'file_b' => 'VARCHAR(255)',
        'file_c' => 'VARCHAR(255)',
        'file_d' => 'VARCHAR(255)',
        'file_e' => 'VARCHAR(255)'
    ];

    foreach ($cols as $col => $type) {
        $pdo->exec("ALTER TABLE lms_materi_soal ADD COLUMN $col $type NULL");
    }

    echo "Berhasil menambah kolom media pada tabel lms_materi_soal.\n";
} catch (Exception $e) {
    // Abaikan jika kolom sudah ada
    echo "Info: " . $e->getMessage() . "\n";
}
