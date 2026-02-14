<?php
// app/controllers/HakAksesController.php

require_once __DIR__ . '/../models/HakAksesModel.php';
require_once __DIR__ . '/../models/PeranModel.php';
require_once __DIR__ . '/../models/AppMenuModel.php';

/**
 * FUNGSI INDEX: Menampilkan daftar peran
 */
function hak_akses_index($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=dashboard');
        return; 
    }
    
    $list_peran = PeranModel::getAll($pdo);
    
    if (isset($_GET['id_peran']) && $_GET['id_peran'] > 0) {
        hak_akses_map_form($pdo);
        return;
    }

    extract(compact('list_peran'));
    include __DIR__ . '/../views/hak_akses_index.php'; 
}

/**
 * FUNGSI UTAMA: Form Mapping dengan Pengurutan Hierarki
 */
function hak_akses_map_form($pdo) {
    if (!has_role('Admin')) { 
        redirect('index.php?mod=hak_akses');
        return; 
    }
    $id_peran = $_GET['id_peran'] ?? $_POST['id_peran'] ?? 0;

    if ($id_peran == 0) {
        redirect('index.php?mod=hak_akses');
        return;
    }
    
    $peran = PeranModel::findById($pdo, $id_peran);
    
    // 1. AMBIL SEMUA MENU (Raw Data)
    // Urutkan berdasarkan urutan agar sesuai sidebar
    $stmt = $pdo->query("SELECT * FROM app_menu ORDER BY parent_id ASC, urutan ASC");
    $raw_menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. SUSUN HIERARKI (Induk -> Anak)
    // Agar submenu Guru (Anak) selalu tampil tepat di bawah Data Master (Induk)
    $list_menu = [];
    $parents = [];
    $children = [];

    // Pisahkan Induk dan Anak
    foreach ($raw_menus as $m) {
        if ($m['parent_id'] == 0) {
            $parents[] = $m;
        } else {
            $children[$m['parent_id']][] = $m;
        }
    }

    // Gabungkan kembali secara berurutan
    foreach ($parents as $p) {
        $list_menu[] = $p; // Masukkan Induk
        if (isset($children[$p['id_menu']])) {
            foreach ($children[$p['id_menu']] as $c) {
                $list_menu[] = $c; // Masukkan Anak-anaknya langsung dibawah induk
            }
        }
    }
    
    // 3. Ambil Mapping Izin
    $izin_saat_ini = HakAksesModel::getMappingByPeran($pdo, $id_peran);
    
    extract(compact('peran', 'list_menu', 'izin_saat_ini'));
    include __DIR__ . '/../views/hak_akses_map_form.php'; 
}

/**
 * FUNGSI SAVE
 */
function hak_akses_save_action($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=hak_akses');
        return; 
    }

    $id_peran = $_POST['id_peran'] ?? 0;
    $permissions = $_POST['permissions'] ?? []; 

    if ($id_peran == 0) {
        redirect('index.php?mod=hak_akses');
        return;
    }

    if (HakAksesModel::saveMapping($pdo, $id_peran, $permissions)) {
        $_SESSION['pesan_sukses'] = "Hak Akses berhasil diperbarui.";
        if (isset($_SESSION['role_ids']) && in_array($id_peran, $_SESSION['role_ids'])) {
             if (function_exists('reset_permission_cache')) reset_permission_cache();
        }
    } else {
        $_SESSION['pesan_error'] = "Gagal memperbarui Hak Akses.";
    }
    
    redirect('index.php?mod=hak_akses&id_peran=' . $id_peran);
}
?>