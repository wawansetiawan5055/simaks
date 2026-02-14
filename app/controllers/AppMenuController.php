<?php
// app/controllers/AppMenuController.php

require_once __DIR__ . '/../models/AppMenuModel.php';
// Asumsi helper functions (redirect, can_do) sudah di-load

// --- FUNGSI INDEX (LIST MENU) ---
function app_menu_index($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=dashboard');
        return; 
    }
    
    $list_menu = AppMenuModel::getAllActive($pdo); // Ambil semua menu
    $parent_menus = AppMenuModel::getAllActive($pdo); // Untuk dropdown parent
    $menu = null; // Fix: Undefined variable $menu in app_menu_form.php
    
    extract(compact('list_menu', 'parent_menus', 'menu'));
    include __DIR__ . '/../views/app_menu_index.php'; 
}

// --- FUNGSI FORM (TAMBAH/EDIT) ---
function app_menu_form($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=app_menu');
        return; 
    }

    // LIST MENU (Untuk Tabel & Dropdown Parent)
    // Kita load ini agar saat Edit, user tetap melihat tabel daftar menu (UX lebih baik & layout utuh)
    $list_menu = AppMenuModel::getAllActive($pdo);
    $parent_menus = $list_menu; // Alias untuk dropdown

    // DATA MENU YG DIEDIT
    $menu = null;
    if (isset($_GET['id'])) {
        $menu = AppMenuModel::findById($pdo, $_GET['id']);
    }
    
    // Render Layout Index (yang didalamnya ada include form & tabel)
    // Variabel $menu akan terisi, sehingga form otomatis mode Edit
    extract(compact('list_menu', 'parent_menus', 'menu'));
    include __DIR__ . '/../views/app_menu_index.php'; 
}

// --- FUNGSI SAVE ---
function app_menu_save_action($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=app_menu');
        return; 
    }

    $data = [
        'id_menu' => $_POST['id_menu'] ?? null,
        'nama_menu' => trim($_POST['nama_menu']),
        'link' => strtolower(trim($_POST['link'])), // Link harus konsisten (lowercase)
        'icon' => trim($_POST['icon']),
        'parent_id' => (int)$_POST['parent_id'],
        'urutan' => (int)$_POST['urutan'],
        'status' => $_POST['status'] ?? 'Aktif'
    ];

    if (empty($data['nama_menu']) || empty($data['link'])) {
        $_SESSION['pesan_error'] = "Nama Menu dan Link wajib diisi.";
    } else {
        // Asumsi kita tambahkan fungsi save di AppMenuModel
        $result = AppMenuModel::save($pdo, $data); 
        if ($result) {
            $_SESSION['pesan_sukses'] = "Data Menu berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan data Menu.";
        }
    }
    redirect('index.php?mod=app_menu');
}

// --- FUNGSI DELETE ---
function app_menu_delete_action($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=app_menu');
        return; 
    }
    
    $id_menu = $_GET['id'] ?? 0;
    
    // Asumsi kita tambahkan fungsi delete di AppMenuModel
    if ($id_menu > 0 && AppMenuModel::delete($pdo, $id_menu)) {
        $_SESSION['pesan_sukses'] = "Menu berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus Menu. Pastikan tidak ada sub-menu atau hak akses terkait.";
    }
    redirect('index.php?mod=app_menu');
}

// --- FUNGSI SAVE ORDER (AJAX) ---
function app_menu_save_order($pdo) {
    if (!has_role('Admin')) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['updates']) && is_array($data['updates'])) {
        $success = AppMenuModel::updateOrder($pdo, $data['updates']);
        
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}

// --- FUNGSI DUPLICATE MENU ---
function app_menu_duplicate_action($pdo) {
    if (!has_role('Admin')) {
        redirect('index.php?mod=app_menu');
        return;
    }

    $id_menu = $_GET['id'] ?? 0;
    
    if ($id_menu > 0) {
        $result = AppMenuModel::duplicate($pdo, $id_menu);
        if ($result) {
            $_SESSION['pesan_sukses'] = "Menu berhasil diduplikat.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menduplikat menu.";
        }
    }
    
    redirect('index.php?mod=app_menu');
}