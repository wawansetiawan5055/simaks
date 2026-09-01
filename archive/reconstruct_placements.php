<?php
require 'config/db.php';
$pdo = connect_db();

echo "--- REKONSTRUKSI RIWAYAT PENEMPATAN (ALUMNI) ---\n";

// 1. Ambil alumni yang tidak punya riwayat penempatan di kelas akhir mereka
$sql = "SELECT id_siswa, id_kelas_akhir, id_ta_lulus 
        FROM siswa_alumni 
        WHERE id_kelas_akhir IS NOT NULL AND id_ta_lulus IS NOT NULL";
$alumni = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$restored = 0;
foreach ($alumni as $a) {
    // Cek apakah sudah ada di penempatan_siswa
    $check = $pdo->prepare("SELECT id_penempatan FROM penempatan_siswa WHERE id_siswa = ? AND id_kelas = ? AND id_ta = ?");
    $check->execute([$a['id_siswa'], $a['id_kelas_akhir'], $a['id_ta_lulus']]);
    
    if (!$check->fetch()) {
        // Jika tidak ada, rekonstruksi
        $ins = $pdo->prepare("INSERT INTO penempatan_siswa (id_siswa, id_kelas, id_ta, status_penempatan) VALUES (?, ?, ?, 'Aktif')");
        if ($ins->execute([$a['id_siswa'], $a['id_kelas_akhir'], $a['id_ta_lulus']])) {
            $restored++;
        }
    }
}

echo "Berhasil merekonstruksi $restored data penempatan siswa alumni.\n";
