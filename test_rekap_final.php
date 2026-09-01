<?php
require 'config/db.php';
require 'app/models/DashboardModel.php';
$pdo = connect_db();

echo "--- REKAP TA 5 (2025/2026 Ganjil) ---\n";
$res5 = DashboardModel::getRekapSiswaPerKelas($pdo, 5);
foreach($res5 as $r) {
    if($r['nama_kelas'] == 'X.1') {
        print_r($r);
    }
}
 