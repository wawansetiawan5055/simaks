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
    
    // PERBAIKAN: Mendefinisikan setiap variabel dari $_POST secara eksplisit
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
        'nama_yayasan'    => $_POST['nama_yayasan'] ?? '',
        'sk_izin_operasional'   => $_POST['sk_izin_operasional'] ?? '',
        'sk_akreditasi'         => $_POST['sk_akreditasi'] ?? '',
        'moto'                  => $_POST['moto'] ?? '',
        'logo'                  => '' // Default kosong, akan diisi oleh logika upload
    ];
    
    // Logika untuk upload logo (tidak berubah, tapi penting)
    if (isset($_FILES['logo_sekolah']) && $_FILES['logo_sekolah']['error'] == 0) {
        // Pastikan folder assets/img ada dan bisa ditulis (writable)
        $target_dir = "assets/img/";
        // Buat nama file yang unik untuk menghindari penimpaan
        $logo_name = "logo_sekolah_" . time() . "_" . basename($_FILES["logo_sekolah"]["name"]);
        $target_file = $target_dir . $logo_name;
        
        if (move_uploaded_file($_FILES["logo_sekolah"]["tmp_name"], $target_file)) {
            $data['logo'] = $logo_name; // Simpan nama file baru ke data
            $_SESSION['app_logo'] = $logo_name; // [BARU] Update Session agar logo langsung berubah
        }
    }
    
    ProfilSekolahModel::save($pdo, $data);
    
    $_SESSION['pesan_sukses'] = "Profil sekolah berhasil diperbarui!";
    redirect('index.php?mod=profil_sekolah');
}