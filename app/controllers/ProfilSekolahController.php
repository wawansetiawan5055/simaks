<?php
require_once __DIR__ . '/../models/ProfilSekolahModel.php';

function profil_sekolah_index($pdo) {
    // Pastikan hanya yang punya izin yang bisa mengakses
    if (!check_access('profil_sekolah', 'index')) redirect('index.php');
    
    $profil = ProfilSekolahModel::getProfil($pdo);
    include __DIR__ . '/../views/profil_sekolah_form.php';
}

function profil_sekolah_save($pdo) {
    if (!can_do($pdo, 'profil_sekolah', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk mengubah profil sekolah.";
        redirect('index.php?mod=profil_sekolah');
        return;
    }
    
    $data = [
        'nama_sekolah'          => $_POST['nama_sekolah'] ?? '',
        'npsn'                  => $_POST['npsn'] ?? '',
        'bentuk_pendidikan'     => $_POST['bentuk_pendidikan'] ?? '',
        'kurikulum'             => $_POST['kurikulum'] ?? '',
        'nama_kepala_sekolah'   => $_POST['nama_kepala_sekolah'] ?? '',
        'alamat'                => $_POST['alamat'] ?? '',
        'koordinat'             => $_POST['koordinat'] ?? '',
        'telepon'               => $_POST['telepon'] ?? '',
        'email'                 => $_POST['email'] ?? '',
        'website'               => $_POST['website'] ?? '',
        'status_sekolah'        => $_POST['status_sekolah'] ?? 'Swasta',
        'nama_yayasan'          => $_POST['nama_yayasan'] ?? '',
        'sk_izin_operasional'   => $_POST['sk_izin_operasional'] ?? '',
        'sk_akreditasi'         => $_POST['sk_akreditasi'] ?? '',
        'moto'                  => $_POST['moto'] ?? '',
        'logo'                  => '',
        'model_kop'             => $_POST['model_kop'] ?? 'yayasan',
        'kop_baris_1'           => $_POST['kop_baris_1'] ?? '',
        'kop_baris_2'           => $_POST['kop_baris_2'] ?? '',
        'kop_baris_3'           => $_POST['kop_baris_3'] ?? '',
        'kop_baris_4'           => $_POST['kop_baris_4'] ?? '',
        'kop_baris_5'           => $_POST['kop_baris_5'] ?? '',
        'logo_kiri'             => '',
        'logo_kanan'            => '',
        'show_logo_kiri'        => isset($_POST['show_logo_kiri']) ? 1 : 0,
        'show_logo_kanan'       => isset($_POST['show_logo_kanan']) ? 1 : 0,
        'style_garis'           => $_POST['style_garis'] ?? 'double'
    ];
    
    $target_dir = __DIR__ . "/../../public/assets/img/";
    if (!is_dir($target_dir)) {
        $target_dir = "assets/img/";
    }

    // 1. Upload Logo Utama
    if (isset($_FILES['logo_sekolah']) && $_FILES['logo_sekolah']['error'] == 0) {
        $logo_name = "logo_sekolah_" . time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES["logo_sekolah"]["name"]));
        $target_file = $target_dir . $logo_name;
        if (move_uploaded_file($_FILES["logo_sekolah"]["tmp_name"], $target_file)) {
            $data['logo'] = $logo_name;
            $_SESSION['app_logo'] = $logo_name;
        }
    }

    // 2. Upload Logo Kiri Kop
    if (isset($_FILES['logo_kiri_file']) && $_FILES['logo_kiri_file']['error'] == 0) {
        $logo_kiri_name = "logo_kiri_" . time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES["logo_kiri_file"]["name"]));
        $target_file = $target_dir . $logo_kiri_name;
        if (move_uploaded_file($_FILES["logo_kiri_file"]["tmp_name"], $target_file)) {
            $data['logo_kiri'] = $logo_kiri_name;
        }
    }

    // 3. Upload Logo Kanan Kop
    if (isset($_FILES['logo_kanan_file']) && $_FILES['logo_kanan_file']['error'] == 0) {
        $logo_kanan_name = "logo_kanan_" . time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES["logo_kanan_file"]["name"]));
        $target_file = $target_dir . $logo_kanan_name;
        if (move_uploaded_file($_FILES["logo_kanan_file"]["tmp_name"], $target_file)) {
            $data['logo_kanan'] = $logo_kanan_name;
        }
    }
    
    ProfilSekolahModel::save($pdo, $data);
    
    if (function_exists('audit_log')) {
        audit_log('UPDATE', "Memperbarui Data Profil & Konfigurasi Kop Surat Sekolah ({$data['nama_sekolah']})", 'profil_sekolah');
    }
    
    $_SESSION['pesan_sukses'] = "Profil dan konfigurasi Kop Surat sekolah berhasil diperbarui!";
    redirect('index.php?mod=profil_sekolah');
}