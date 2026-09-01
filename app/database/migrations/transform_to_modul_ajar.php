<?php
require_once __DIR__ . '/../../../config/db.php';
$pdo = connect_db();

try {
    // 1. Update tabel lms_materi
    $cols_materi = [
        'semester' => "ENUM('1', '2') DEFAULT '1'",
        'tahun_pelajaran' => "VARCHAR(20) NULL",
        'instruksi' => "TEXT NULL",
        'id_cp' => "INT(11) NULL",
        'id_tp' => "TEXT NULL", // Bisa simpan multiple ID TP (comma separated atau JSON)
        'cp_manual' => "TEXT NULL",
        'tp_manual' => "TEXT NULL",
        'refleksi_config' => "TEXT NULL" // Menyimpan JSON pertanyaan refleksi
    ];

    foreach ($cols_materi as $col => $type) {
        $pdo->exec("ALTER TABLE lms_materi ADD COLUMN $col $type");
    }

    // 2. Update tabel lms_materi_soal untuk kategori Diagnostik / Latihan
    $pdo->exec("ALTER TABLE lms_materi_soal ADD COLUMN kategori_soal ENUM('Diagnostik', 'Latihan') DEFAULT 'Latihan'");

    echo "Berhasil mentransformasi database ke struktur Modul Ajar Lengkap.\n";
} catch (Exception $e) {
    echo "Info: " . $e->getMessage() . "\n";
}
