<?php
require_once __DIR__ . '/../models/MasterJamModel.php';

function master_jam_index($pdo) {
    if (!is_logged_in() || !check_access('master_jam')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }

    // Ambil data untuk tabel dan form
    $jam_list = MasterJamModel::getAll($pdo);
    $kegiatan_list = MasterJamModel::getAllKegiatan($pdo); // Untuk dropdown
    
    extract(compact('jam_list', 'kegiatan_list'));
    include __DIR__ . '/../views/master_jam_index.php';
}

function master_jam_save($pdo) {
    if (!is_logged_in() || !can_do($pdo, 'master_jam', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menyimpan data.";
        redirect('index.php?mod=master_jam');
    }
    
    try {
        MasterJamModel::save($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data jam pelajaran berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    
    redirect('index.php?mod=master_jam');
}

function master_jam_delete($pdo, $id) {
    if (!is_logged_in() || !can_do($pdo, 'master_jam', 'delete')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=master_jam');
    }
    
    try {
        MasterJamModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data jam pelajaran berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    
    redirect('index.php?mod=master_jam');
}

/**
 * BARU: Fungsi untuk menangani AJAX update urutan
 */
function master_jam_update_urutan($pdo) {
    if (!can_do($pdo, 'master_jam', 'update')) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk mengubah urutan.']);
        exit;
    }

    $urutan_ids = $_POST['urutan'] ?? [];

    if (empty($urutan_ids)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data urutan.']);
        exit;
    }

    try {
        MasterJamModel::updateUrutan($pdo, $urutan_ids);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Urutan berhasil diperbarui.']);
        exit;

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>