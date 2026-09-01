<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/env.php';

try {
    $pdo = connect_db();

    $sql = "CREATE TABLE IF NOT EXISTS dokumen_tugas_tambahan (
        id_dokumen INT AUTO_INCREMENT PRIMARY KEY,
        id_guru INT NOT NULL,
        jenis_tugas_tambahan VARCHAR(50) NOT NULL,
        kategori_dokumen VARCHAR(50) NOT NULL,
        nama_dokumen VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        deskripsi TEXT NULL,
        id_ta INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "SUCCESS: Table dokumen_tugas_tambahan created or already exists.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
