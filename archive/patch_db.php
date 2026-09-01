<?php
require 'config/db.php';
$pdo = connect_db();
try {
    $pdo->exec("ALTER TABLE profil_guru ADD COLUMN sertifikasi ENUM('Tersertifikasi', 'Belum Tersertifikasi') DEFAULT 'Belum Tersertifikasi'");
    echo "Added sertifikasi column.\n";
} catch (PDOException $e) {
    echo "Error (might exist): " . $e->getMessage() . "\n";
}
try {
    $pdo->exec("ALTER TABLE profil_guru ADD COLUMN mapel_sertifikasi VARCHAR(100) DEFAULT NULL");
    echo "Added mapel_sertifikasi column.\n";
} catch (PDOException $e) {
    echo "Error (might exist): " . $e->getMessage() . "\n";
}
