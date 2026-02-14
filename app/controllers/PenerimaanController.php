<?php
require_once __DIR__ . '/../models/SiswaModel.php';

function penerimaan_index($pdo) {
    // Use dynamic access control
    if (!check_access('penerimaan')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    include __DIR__ . '/../views/penerimaan_form.php';
}

function penerimaan_save($pdo) {
    // Use dynamic access control
    if (!can_do($pdo, 'penerimaan', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mendaftarkan siswa.";
        redirect('index.php?mod=penerimaan');
    }

    // PERBAIKAN: Hanya menyertakan 'sekolah_asal'
    $data_siswa = [
        'nama' => $_POST['nama'],
        'nisn' => $_POST['nisn'],
        'nipd' => $_POST['nipd'] ?? null,
        'nik' => $_POST['nik'] ?? null,
        'jk' => $_POST['jk'],
        'tempat_lahir' => $_POST['tempat_lahir'],
        'tanggal_lahir' => $_POST['tanggal_lahir'],
        'sekolah_asal' => $_POST['sekolah_asal'] ?? null, // Field baru
        'status_aktif' => 'Aktif'
    ];

    SiswaModel::save($pdo, $data_siswa);

    $_SESSION['pesan_sukses'] = "Siswa baru (".htmlspecialchars($_POST['nama']).") berhasil didaftarkan!";
    redirect('index.php?mod=penerimaan');
}