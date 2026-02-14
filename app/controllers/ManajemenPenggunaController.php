<?php
require_once __DIR__ . '/../models/ManajemenPenggunaModel.php';

function pengguna_index($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    
    $guru_users = ManajemenPenggunaModel::getUsersByType($pdo, 'guru');
    $siswa_users = ManajemenPenggunaModel::getUsersByType($pdo, 'siswa');
    $sistem_users = ManajemenPenggunaModel::getUsersByType($pdo, 'sistem');
    
    include __DIR__ . '/../views/manajemen_pengguna_index.php';
}

function pengguna_generate($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    $target = $_POST['target'] ?? '';
    $password = $_POST['password'] ?? '123456';
    
    try {
        $count = ManajemenPenggunaModel::generateAccounts($pdo, $target, $password);
        $_SESSION['pesan_sukses'] = "Berhasil membuat $count akun $target baru.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal generate: " . $e->getMessage();
    }
    redirect('index.php?mod=manajemen_pengguna');
}

function pengguna_cleanup($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    try {
        ManajemenPenggunaModel::cleanupTrialAccounts($pdo);
        $_SESSION['pesan_sukses'] = "Seluruh akun uji coba berhasil dihapus. Akun Admin tetap aman.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal membersihkan akun: " . $e->getMessage();
    }
    redirect('index.php?mod=manajemen_pengguna');
}

function pengguna_form($pdo, $id = null) {
    if (!has_role('Admin')) redirect('index.php');
    
    $user_data = $id ? ManajemenPenggunaModel::findUser($pdo, $id) : ['user' => null, 'user_roles' => [], 'linked_guru_id' => null, 'linked_siswa_id' => null];
    $all_roles = ManajemenPenggunaModel::getAllRoles($pdo);
    // Ambil guru dan siswa yang tersedia untuk dihubungkan
    $available_guru = ManajemenPenggunaModel::getAvailableGuru($pdo, $id);
    $available_siswa = ManajemenPenggunaModel::getAvailableSiswa($pdo, $id);
    
    // Kirim semua variabel ke view
    extract($user_data);
    
    include __DIR__ . '/../views/manajemen_pengguna_form.php';
}

function pengguna_save($pdo) {
    if (!has_role('Admin')) redirect('index.php');
    try {
        ManajemenPenggunaModel::saveUser($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data pengguna berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=manajemen_pengguna');
}

function pengguna_delete($pdo, $id) {
    if (!has_role('Admin')) redirect('index.php');
    try {
        ManajemenPenggunaModel::deleteUser($pdo, $id);
        $_SESSION['pesan_sukses'] = "Pengguna berhasil dihapus.";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
             $_SESSION['pesan_error'] = "Gagal menghapus: Pengguna ini masih terhubung dengan data lain (Guru/Siswa). Silakan hapus data terkait terlebih dahulu.";
        } else {
             $_SESSION['pesan_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=manajemen_pengguna');
}