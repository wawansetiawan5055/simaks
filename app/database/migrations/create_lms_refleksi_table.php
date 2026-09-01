<?php
require_once __DIR__ . '/../../../config/db.php';
$pdo = connect_db();

try {
    // Create table for student reflections
    $sql = "CREATE TABLE IF NOT EXISTS lms_materi_refleksi (
        id_refleksi INT(11) AUTO_INCREMENT PRIMARY KEY,
        id_materi INT(11) NOT NULL,
        id_siswa INT(11) NOT NULL,
        pertanyaan TEXT NOT NULL,
        jawaban TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_materi) REFERENCES lms_materi(id_materi) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    echo "Berhasil membuat tabel lms_materi_refleksi.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
