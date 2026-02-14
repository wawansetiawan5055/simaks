<?php
require_once __DIR__ . '/../models/MasterKegiatanModel.php';

function master_kegiatan_index($pdo) {
    if (!check_access('master_kegiatan', 'index')) redirect('index.php');
    $kegiatan_akademik = MasterKegiatanModel::getAkademik($pdo);
    $kegiatan_non_akademik = MasterKegiatanModel::getNonAkademik($pdo);
    include __DIR__ . '/../views/master_kegiatan_index.php';
}

function master_kegiatan_save($pdo) {
    $id = $_POST['id_kegiatan'] ?? null; // Adjust if ID field is different
    $permission = $id ? 'update' : 'create';

    if (!can_do($pdo, 'master_kegiatan', $permission)) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan data.";
        redirect('index.php?mod=master_kegiatan');
        return;
    }

    try {
        $data = $_POST;
        $data['hari_pelaksanaan'] = isset($_POST['hari_pelaksanaan']) ? implode(',', $_POST['hari_pelaksanaan']) : null;
        MasterKegiatanModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Data kegiatan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=master_kegiatan');
}

function master_kegiatan_delete($pdo, $id) {
    if (!can_do($pdo, 'master_kegiatan', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=master_kegiatan');
        return;
    }

    try {
        MasterKegiatanModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data kegiatan berhasil dihapus.";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            $_SESSION['pesan_error'] = "Gagal menghapus: Kegiatan ini masih memiliki data terkait (Jadwal/Absensi). Silakan hapus data terkait terlebih dahulu.";
        } else {
            $_SESSION['pesan_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=master_kegiatan');
}