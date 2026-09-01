<?php
require_once __DIR__ . '/../../../config/db.php';
$pdo = connect_db();

try {
    // Ubah tipe data deskripsi menjadi LONGTEXT agar muat artikel panjang + HTML
    $pdo->exec("ALTER TABLE lms_materi MODIFY COLUMN deskripsi LONGTEXT");
    echo "Berhasil memperbarui kolom deskripsi menjadi LONGTEXT.\n";
    
    // Juga untuk lms_materi_soal jika pertanyaannya panjang
    $pdo->exec("ALTER TABLE lms_materi_soal MODIFY COLUMN pertanyaan TEXT");
    echo "Berhasil memperbarui kolom pertanyaan menjadi TEXT.\n";
    
} catch (Exception $e) {
    echo "Gagal: " . $e->getMessage() . "\n";
}
