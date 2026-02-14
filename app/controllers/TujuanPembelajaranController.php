<?php
require_once __DIR__ . '/../models/TujuanPembelajaranModel.php';
require_once __DIR__ . '/../models/MapelModel.php';

function tp_index($pdo) {
    if (!check_access('cp_tp')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    $mapel_list = MapelModel::all($pdo);
    $id_mapel_filter = $_GET['id_mapel'] ?? ($mapel_list[0]['id_mapel'] ?? 0);
    
    $tp_list = [];
    if ($id_mapel_filter) {
        $tp_list = TujuanPembelajaranModel::getAllByMapel($pdo, $id_mapel_filter);
    }

    include __DIR__ . '/../views/manajemen_tp_index.php';
}

function tp_save($pdo) {
    if (!can_do($pdo, 'cp_tp', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menyimpan data.";
        redirect('index.php?mod=cp_tp');
    }
    TujuanPembelajaranModel::save($pdo, $_POST);
    redirect('index.php?mod=manajemen_tp&id_mapel=' . $_POST['id_mapel']);
}

function tp_delete($pdo, $id) {
    if (!can_do($pdo, 'cp_tp', 'delete')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=cp_tp');
    }
    TujuanPembelajaranModel::delete($pdo, $id);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}