<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/db.php';
$pdo = connect_db();
$stmt = $pdo->query("SELECT id_menu, nama_menu, link, parent_id, urutan FROM app_menu WHERE nama_menu LIKE '%Layanan%' OR nama_menu LIKE '%Konseling%' OR nama_menu LIKE '%Kesehatan%' OR link IN ('catatan_kasus', 'bk', 'uks', 'manajemen_uks')");
echo "MENUS:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $pdo->query("SHOW KEYS FROM hak_akses");
echo "HAK_AKSES KEYS:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
