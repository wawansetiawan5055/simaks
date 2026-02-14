<?php
// app/controllers/ProfilGuruController.php
require_once __DIR__ . '/../models/ProfilGuruModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

function profil_guru_index($pdo) {
    // Admin-only for listing all teachers
    if (!can_do($pdo, 'profil_guru', 'read')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    // Get all teaches (using GuruModel)
    $gurus = GuruModel::all($pdo);
    
    // We might want to flag which ones have profiles filled?
    // For now, just list them.
    include __DIR__ . '/../views/profil_guru/index.php';
}

function profil_guru_detail($pdo) {
    // Allow access if user has permission OR viewing own profile
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    $id_guru = $_GET['id'] ?? null;
    
    $is_own_profile = ($id_guru_login && $id_guru == $id_guru_login);
    
    if (!$is_own_profile && !check_access('profil_guru')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    // Security check: Guru can only view their own profile (if we implement user linking later)
    // For now assuming Admin manages it or Guru accesses via their own ID.
    // Ideally validation against session if role is Guru. 
    // Implementation Plan: Admin accesses any, Guru accesses own.
    
    if (!$id_guru) redirect('index.php?mod=profil_guru');

    $guru = GuruModel::find($pdo, $id_guru);
    if (!$guru) {
        $_SESSION['pesan_error'] = "Data Guru tidak ditemukan.";
        redirect('index.php?mod=profil_guru');
    }

    $profil = ProfilGuruModel::getByGuruId($pdo, $id_guru);

    include __DIR__ . '/../views/profil_guru/detail.php';
}

function profil_guru_save($pdo) {
    // Allow if has permission or editing own profile
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    $id_guru = $_POST['id_guru'];
    $is_own_profile = ($id_guru_login && $id_guru == $id_guru_login);
    
    if (!$is_own_profile && !can_do($pdo, 'profil_guru', 'update')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengubah data.";
        redirect('index.php?mod=dashboard');
    }

    // Basic array construction
    $data = [
        'id_guru' => $id_guru,
        'gelar_depan' => $_POST['gelar_depan'] ?? '',
        'gelar_belakang' => $_POST['gelar_belakang'] ?? '',
        'alamat_lengkap' => $_POST['alamat_lengkap'] ?? '',
        'no_hp' => $_POST['no_hp'] ?? '',
        'email_pribadi' => $_POST['email_pribadi'] ?? '',
        'nama_ibu_kandung' => $_POST['nama_ibu_kandung'] ?? '',
        'pendidikan_terakhir' => $_POST['pendidikan_terakhir'] ?? ''
    ];

    try {
        ProfilGuruModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Data Profil berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }

    redirect('index.php?mod=profil_guru&act=detail&id=' . $id_guru);
}

function profil_guru_upload($pdo) {
    // Allow if has permission or uploading to own profile
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    $id_guru = $_POST['id_guru'];
    $is_own_profile = ($id_guru_login && $id_guru == $id_guru_login);
    
    if (!$is_own_profile && !can_do($pdo, 'profil_guru', 'update')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengunggah berkas.";
        redirect('index.php?mod=dashboard');
    }

    $jenis_file = $_POST['jenis_file']; // e.g., file_ijazah_s1
    
    // Allowed columns validation
    $allowed_cols = ['file_ijazah_s1', 'file_serdik', 'file_ktp', 'file_kk', 'file_akte', 'file_npwp'];
    if (!in_array($jenis_file, $allowed_cols)) {
        $_SESSION['pesan_error'] = "Jenis file tidak valid.";
        redirect('index.php?mod=profil_guru&act=detail&id=' . $id_guru);
    }

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file = $_FILES['file_upload'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed_ext)) {
            $_SESSION['pesan_error'] = "Hanya file PDF, JPG, PNG yang diperbolehkan.";
            redirect('index.php?mod=profil_guru&act=detail&id=' . $id_guru);
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
             $_SESSION['pesan_error'] = "Ukuran file maksimal 5MB.";
             redirect('index.php?mod=profil_guru&act=detail&id=' . $id_guru);
        }

        // Upload dir
        $target_dir = __DIR__ . '/../../public/uploads/guru/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        // Filename: {jenis}_{id_guru}_{timestamp}.ext
        $filename = $jenis_file . '_' . $id_guru . '_' . time() . '.' . $ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update DB
            ProfilGuruModel::updateFile($pdo, $id_guru, $jenis_file, $filename);
            $_SESSION['pesan_sukses'] = "Berkas berhasil diunggah.";
        } else {
            $_SESSION['pesan_error'] = "Gagal mengunggah file ke server.";
        }

    } else {
        $_SESSION['pesan_error'] = "Tidak ada file yang dipilih atau terjadi error upload.";
    }

    redirect('index.php?mod=profil_guru&act=detail&id=' . $id_guru . '&tab=berkas');
}

function profil_guru_print($pdo) {
    // Allow if has permission or printing own profile
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    $id_guru = $_GET['id'];
    $is_own_profile = ($id_guru_login && $id_guru == $id_guru_login);
    
    if (!$is_own_profile && !check_access('profil_guru')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    $guru = GuruModel::find($pdo, $id_guru);
    $profil = ProfilGuruModel::getByGuruId($pdo, $id_guru);
    
    // Layout khusus print
    include __DIR__ . '/../views/profil_guru/print.php';
}
?>
