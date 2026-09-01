<?php
require 'config/db.php';
$pdo = connect_db();

echo "--- REKONSTRUKSI GANJIL 2024/2025 ---\n";

// Mapping Kelas (Ganjil id_3 => Genap id_4)
$mapping = [
    3 => 10,  // X.1
    4 => 11,  // X.2
    5 => 12,  // XI.1
    6 => 13,  // XI.2
    9 => 14   // XII.1
];

$restored = 0;
foreach ($mapping as $id_ganjil => $id_genap) {
    // Cari siswa yang ada di Genap tapi belum ada di Ganjil
    $sql = "INSERT INTO penempatan_siswa (id_siswa, id_kelas, id_ta, status_penempatan)
            SELECT id_siswa, ?, 3, 'Aktif'
            FROM penempatan_siswa 
            WHERE id_kelas = ? AND id_ta = 4
            AND id_siswa NOT IN (SELECT id_siswa FROM penempatan_siswa WHERE id_ta = 3)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ganjil, $id_genap]);
    $count = $stmt->rowCount();
    echo "Kelas " . $id_ganjil . ": Berhasil menyalin $count siswa dari Genap ke Ganjil.\n";
    $restored += $count;
}

echo "Total rekonstruksi Ganjil: $restored records.\n";
