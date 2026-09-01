<?php
// scripts/setup_uks_menu.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/models/UksModel.php';

try {
    $pdo = connect_db();
    echo "--- MEMULAI SETUP MODUL UKS ---\n";
    
    // 1. Inisialisasi Tabel UKS
    UksModel::initTables($pdo);
    echo "✓ Tabel uks_kunjungan dan uks_obat berhasil diverifikasi/dibuat.\n";

    // 2. Cek/Tambahkan Menu 'Kesehatan (UKS)' di app_menu
    // Cek apakah menu uks sudah ada
    $stmt = $pdo->prepare("SELECT id_menu FROM app_menu WHERE link = 'uks' LIMIT 1");
    $stmt->execute();
    $id_menu_uks = $stmt->fetchColumn();

    if (!$id_menu_uks) {
        // Cari parent 'PIKET & KESISWAAN' (id=30) atau Layanan Siswa
        $stmt_parent = $pdo->query("SELECT id_menu FROM app_menu WHERE nama_menu = 'PIKET & KESISWAAN' LIMIT 1");
        $id_parent_piket = $stmt_parent ? $stmt_parent->fetchColumn() : 0;

        // Ambil urutan terakhir
        $urutan_max = (int)$pdo->query("SELECT MAX(urutan) FROM app_menu")->fetchColumn();

        $stmt_ins = $pdo->prepare("
            INSERT INTO app_menu (nama_menu, link, icon, parent_id, urutan, status)
            VALUES ('Kesehatan (UKS)', 'uks', 'fas fa-heartbeat', ?, ?, 'Aktif')
        ");
        $stmt_ins->execute([0, $urutan_max + 1]);
        $id_menu_uks = $pdo->lastInsertId();
        echo "✓ Menu 'Kesehatan (UKS)' berhasil ditambahkan (ID: $id_menu_uks).\n";
    } else {
        echo "✓ Menu 'Kesehatan (UKS)' sudah ada (ID: $id_menu_uks).\n";
    }

    // 3. Cek/Tambahkan Submenu 'Pembina UKS' di 'Administrasi Jabatan GTK'
    $stmt_gtk = $pdo->query("SELECT id_menu FROM app_menu WHERE link = 'tugas_tambahan/uks' OR nama_menu = 'Pembina UKS' LIMIT 1");
    $id_menu_pembina = $stmt_gtk ? $stmt_gtk->fetchColumn() : null;

    if (!$id_menu_pembina) {
        $stmt_gtk_parent = $pdo->query("SELECT id_menu FROM app_menu WHERE nama_menu = 'Administrasi Jabatan GTK' LIMIT 1");
        $parent_gtk = $stmt_gtk_parent ? $stmt_gtk_parent->fetchColumn() : 4226;

        $stmt_ins2 = $pdo->prepare("
            INSERT INTO app_menu (nama_menu, link, icon, parent_id, urutan, status)
            VALUES ('Pembina UKS', 'tugas_tambahan/uks', 'fas fa-heartbeat', ?, 41, 'Aktif')
        ");
        $stmt_ins2->execute([$parent_gtk]);
        $id_menu_pembina = $pdo->lastInsertId();
        echo "✓ Submenu 'Pembina UKS' berhasil ditambahkan ke Administrasi Jabatan GTK (ID: $id_menu_pembina).\n";
    } else {
        echo "✓ Submenu 'Pembina UKS' sudah ada (ID: $id_menu_pembina).\n";
    }

    // 4. Berikan Hak Akses ke Admin, Guru, TU untuk menu UKS
    $roles_to_grant = $pdo->query("SELECT id_peran, nama_peran FROM peran")->fetchAll(PDO::FETCH_ASSOC);
    $stmt_grant = $pdo->prepare("
        INSERT INTO hak_akses (id_peran, id_menu, can_read, can_create, can_update, can_delete)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            can_read = VALUES(can_read),
            can_create = VALUES(can_create),
            can_update = VALUES(can_update),
            can_delete = VALUES(can_delete)
    ");

    foreach ($roles_to_grant as $r) {
        $nama = strtolower($r['nama_peran']);
        // Berikan akses penuh untuk admin, tu, guru, guru piket, kesiswaan
        if (strpos($nama, 'admin') !== false || strpos($nama, 'tu') !== false || strpos($nama, 'guru') !== false || strpos($nama, 'kesiswaan') !== false || strpos($nama, 'kepala') !== false) {
            if ($id_menu_uks) {
                $stmt_grant->execute([$r['id_peran'], $id_menu_uks, 1, 1, 1, 1]);
            }
            if ($id_menu_pembina) {
                $stmt_grant->execute([$r['id_peran'], $id_menu_pembina, 1, 1, 1, 1]);
            }
        }
    }
    echo "✓ Hak akses menu UKS berhasil dikonfigurasi.\n";

    echo "--- SETUP MODUL UKS SELESAI DENGAN SUKSES ---\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
