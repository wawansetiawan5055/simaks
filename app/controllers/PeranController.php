<?php
require_once __DIR__ . '/../models/PeranModel.php';

function peran_index($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    $list_peran = PeranModel::getAll($pdo);
    extract(compact('list_peran'));
    include __DIR__ . '/../views/peran_index.php'; // VIEW LIST PERAN
}

function peran_form($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    $peran = null;
    if (isset($_GET['id'])) {
        $peran = PeranModel::findById($pdo, $_GET['id']);
    }
    extract(compact('peran'));
    include __DIR__ . '/../views/peran_form.php'; // VIEW FORM PERAN
}

function peran_save_action($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    $data = [
        'id_peran' => $_POST['id_peran'] ?? null,
        'nama_peran' => trim($_POST['nama_peran'])
    ];

    if (empty($data['nama_peran'])) {
        $_SESSION['pesan_error'] = "Nama Peran wajib diisi.";
    } else {
        $result = PeranModel::save($pdo, $data);
        if ($result) {
            $_SESSION['pesan_sukses'] = "Data Peran berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan data Peran.";
        }
    }
    redirect('index.php?mod=peran');
}

function peran_delete_action($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    $id_peran = $_GET['id'];
    if (PeranModel::delete($pdo, $id_peran)) {
        $_SESSION['pesan_sukses'] = "Peran berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus Peran. Pastikan tidak ada pengguna yang terkait.";
    }
    redirect('index.php?mod=peran');
}