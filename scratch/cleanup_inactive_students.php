<?php
require_once __DIR__ . '/../config/db.php';
$pdo = connect_db();

try {
    $pdo->beginTransaction();

    // 1. Ambil list ID Pengguna yang terhubung ke siswa TIDAK AKTIF
    $sql_find = "SELECT id_pengguna, id_siswa, nama FROM siswa WHERE status_aktif != 'Aktif' AND id_pengguna IS NOT NULL";
    $inactive_students = $pdo->query($sql_find)->fetchAll(PDO::FETCH_ASSOC);
    
    $deleted_count = 0;
    foreach ($inactive_students as $s) {
        $id_p = $s['id_pengguna'];
        
        // Hapus peran
        $pdo->prepare("DELETE FROM pengguna_peran WHERE id_pengguna = ?")->execute([$id_p]);
        
        // Putuskan hubungan di tabel siswa
        $pdo->prepare("UPDATE siswa SET id_pengguna = NULL WHERE id_siswa = ?")->execute([$s['id_siswa']]);
        
        // Hapus pengguna
        $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?")->execute([$id_p]);
        
        $deleted_count++;
        echo "Menghapus akun: " . $s['nama'] . "\n";
    }

    $pdo->commit();
    echo "\nTotal akun siswa tidak aktif yang dihapus: $deleted_count\n";
    echo "Akun Guru dan Admin tetap aman.\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
