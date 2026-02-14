<?php
// LandingController.php - Controller untuk Landing Page Public

/**
 * Tampilkan landing page
 */
function landing_index($pdo)
{
    // Load file config
    $config = require '../config/app.php';

    // Load DB override
    $stmt_settings = $pdo->query("SELECT * FROM app_settings");
    while ($row = $stmt_settings->fetch(PDO::FETCH_ASSOC)) {
        if ($row['setting_key'] == 'landing_page_enabled') {
            $config['landing_page']['enabled'] = ($row['setting_value'] == '1');
        } elseif ($row['setting_key'] == 'landing_slider_interval') {
            $config['landing_page']['slider_interval'] = (int) $row['setting_value'];
        } elseif ($row['setting_key'] == 'school_website') {
            $config['school']['website'] = $row['setting_value'];
        } elseif (strpos($row['setting_key'], 'social_') === 0) {
            $key = str_replace('social_', '', $row['setting_key']);
            $config['social_media'][$key] = $row['setting_value'];
        } elseif (isset($config['school']) && array_key_exists(str_replace('school_', '', $row['setting_key']), $config['school'])) {
            // Map school_name -> name, school_address -> address, etc.
            $key = str_replace('school_', '', $row['setting_key']);
            $config['school'][$key] = $row['setting_value'];
        }
    }

    // Cek apakah landing page enabled
    if (!$config['landing_page']['enabled']) {
        redirect('index.php?mod=auth&act=login');
        return;
    }

    // Jika user sudah login, redirect ke dashboard
    if (is_logged_in()) {
        redirect('index.php?mod=dashboard');
        return;
    }

    // Ambil data profil sekolah dari database
    require_once '../app/models/ProfilSekolahModel.php';
    $profil = ProfilSekolahModel::getProfil($pdo);

    // Ambil data slider (gallery yang is_slider = 1)
    $stmt_slider = $pdo->query("
        SELECT * FROM landing_gallery 
        WHERE is_slider = 1 AND is_active = 1 
        ORDER BY display_order ASC
    ");
    $slider_images = $stmt_slider->fetchAll(PDO::FETCH_ASSOC);

    // Ambil berita featured
    $stmt_news = $pdo->query("
        SELECT * FROM landing_news 
        WHERE is_published = 1 AND is_featured = 1 
        ORDER BY publish_date DESC 
        LIMIT 3
    ");
    $featured_news = $stmt_news->fetchAll(PDO::FETCH_ASSOC);

    // Ambil gallery (non-slider)
    $stmt_gallery = $pdo->query("
        SELECT * FROM landing_gallery 
        WHERE is_active = 1 
        ORDER BY display_order ASC 
        LIMIT 8
    ");
    $gallery = $stmt_gallery->fetchAll(PDO::FETCH_ASSOC);

    // Data untuk view
    $data = [
        'config' => $config,
        'profil' => $profil,
        'slider_images' => $slider_images,
        'featured_news' => $featured_news,
        'gallery' => $gallery,
    ];

    require '../app/views/landing_page.php';
}

/**
 * Form PPDB Public (Tidak perlu login)
 */
function ppdb_public_form($pdo)
{
    $config = require '../config/app.php';

    // Cek apakah PPDB enabled
    if (!$config['ppdb']['enabled']) {
        $_SESSION['pesan_error'] = 'Pendaftaran PPDB belum dibuka.';
        redirect('index.php?mod=landing');
        return;
    }

    // Jika user sudah login, mungkin bisa redirect atau tetap izinkan
    // Kita izinkan saja untuk fleksibilitas

    $data = [
        'config' => $config,
    ];

    require '../app/views/ppdb_public_form.php';
}

/**
 * Save PPDB dari Form Public
 */
function ppdb_public_save($pdo)
{
    $config = require '../config/app.php';

    if (!$config['ppdb']['enabled']) {
        echo json_encode(['success' => false, 'message' => 'Pendaftaran PPDB belum dibuka.']);
        return;
    }

    try {
        // Generate Nomor Pendaftaran
        $year = date('Y');
        $stmt_count = $pdo->query("SELECT COUNT(*) FROM ppdb_pendaftaran WHERE YEAR(created_at) = $year");
        $count = $stmt_count->fetchColumn() + 1;
        $no_pendaftaran = "PPDB{$year}" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Handle file uploads
        $foto_siswa = handle_file_upload('foto_siswa', 'uploads/ppdb/');
        $foto_kk = handle_file_upload('foto_kk', 'uploads/ppdb/');
        $foto_akta = handle_file_upload('foto_akta', 'uploads/ppdb/');
        $foto_ijazah = handle_file_upload('foto_ijazah', 'uploads/ppdb/');
        $foto_raport = handle_file_upload('foto_raport', 'uploads/ppdb/');

        // Insert data
        $sql = "INSERT INTO ppdb_pendaftaran (
            no_pendaftaran, nama_lengkap, nik, nisn, tempat_lahir, tanggal_lahir,
            jenis_kelamin, agama, alamat, rt, rw, kelurahan, kecamatan, kota, provinsi,
            kode_pos, no_hp_siswa, email_siswa,
            nama_ayah, pekerjaan_ayah, penghasilan_ayah, no_hp_ayah,
            nama_ibu, pekerjaan_ibu, penghasilan_ibu, no_hp_ibu,
            nama_wali, pekerjaan_wali, no_hp_wali,
            asal_sekolah, alamat_sekolah, npsn_sekolah,
            foto_siswa, foto_kk, foto_akta, foto_ijazah, foto_raport,
            jalur_pendaftaran, status
        ) VALUES (
            :no_pendaftaran, :nama_lengkap, :nik, :nisn, :tempat_lahir, :tanggal_lahir,
            :jenis_kelamin, :agama, :alamat, :rt, :rw, :kelurahan, :kecamatan, :kota, :provinsi,
            :kode_pos, :no_hp_siswa, :email_siswa,
            :nama_ayah, :pekerjaan_ayah, :penghasilan_ayah, :no_hp_ayah,
            :nama_ibu, :pekerjaan_ibu, :penghasilan_ibu, :no_hp_ibu,
            :nama_wali, :pekerjaan_wali, :no_hp_wali,
            :asal_sekolah, :alamat_sekolah, :npsn_sekolah,
            :foto_siswa, :foto_kk, :foto_akta, :foto_ijazah, :foto_raport,
            :jalur_pendaftaran, 'pending'
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':no_pendaftaran' => $no_pendaftaran,
            ':nama_lengkap' => $_POST['nama_lengkap'] ?? '',
            ':nik' => $_POST['nik'] ?? '',
            ':nisn' => $_POST['nisn'] ?? '',
            ':tempat_lahir' => $_POST['tempat_lahir'] ?? '',
            ':tanggal_lahir' => $_POST['tanggal_lahir'] ?? null,
            ':jenis_kelamin' => $_POST['jenis_kelamin'] ?? '',
            ':agama' => $_POST['agama'] ?? '',
            ':alamat' => $_POST['alamat'] ?? '',
            ':rt' => $_POST['rt'] ?? '',
            ':rw' => $_POST['rw'] ?? '',
            ':kelurahan' => $_POST['kelurahan'] ?? '',
            ':kecamatan' => $_POST['kecamatan'] ?? '',
            ':kota' => $_POST['kota'] ?? '',
            ':provinsi' => $_POST['provinsi'] ?? '',
            ':kode_pos' => $_POST['kode_pos'] ?? '',
            ':no_hp_siswa' => $_POST['no_hp_siswa'] ?? '',
            ':email_siswa' => $_POST['email_siswa'] ?? '',
            ':nama_ayah' => $_POST['nama_ayah'] ?? '',
            ':pekerjaan_ayah' => $_POST['pekerjaan_ayah'] ?? '',
            ':penghasilan_ayah' => $_POST['penghasilan_ayah'] ?? '',
            ':no_hp_ayah' => $_POST['no_hp_ayah'] ?? '',
            ':nama_ibu' => $_POST['nama_ibu'] ?? '',
            ':pekerjaan_ibu' => $_POST['pekerjaan_ibu'] ?? '',
            ':penghasilan_ibu' => $_POST['penghasilan_ibu'] ?? '',
            ':no_hp_ibu' => $_POST['no_hp_ibu'] ?? '',
            ':nama_wali' => $_POST['nama_wali'] ?? '',
            ':pekerjaan_wali' => $_POST['pekerjaan_wali'] ?? '',
            ':no_hp_wali' => $_POST['no_hp_wali'] ?? '',
            ':asal_sekolah' => $_POST['asal_sekolah'] ?? '',
            ':alamat_sekolah' => $_POST['alamat_sekolah'] ?? '',
            ':npsn_sekolah' => $_POST['npsn_sekolah'] ?? '',
            ':foto_siswa' => $foto_siswa,
            ':foto_kk' => $foto_kk,
            ':foto_akta' => $foto_akta,
            ':foto_ijazah' => $foto_ijazah,
            ':foto_raport' => $foto_raport,
            ':jalur_pendaftaran' => $_POST['jalur_pendaftaran'] ?? 'Zonasi',
        ]);

        $_SESSION['pesan_sukses'] = "Pendaftaran berhasil! Nomor pendaftaran Anda: <strong>{$no_pendaftaran}</strong>. Silakan simpan nomor ini untuk pengecekan status.";
        $_SESSION['ppdb_no_pendaftaran'] = $no_pendaftaran;

        redirect('index.php?mod=landing&act=ppdb_success');

    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        redirect('index.php?mod=landing&act=ppdb_form');
    }
}

/**
 * Helper function untuk handle file upload
 */
function handle_file_upload($field_name, $upload_dir)
{
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $upload_path = __DIR__ . '/../../public/' . $upload_dir;
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    $file = $_FILES[$field_name];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $destination = $upload_path . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $upload_dir . $filename;
    }

    return null;
}

/**
 * Halaman sukses setelah daftar PPDB
 */
function ppdb_success_page($pdo)
{
    $no_pendaftaran = $_SESSION['ppdb_no_pendaftaran'] ?? null;

    $data = [
        'no_pendaftaran' => $no_pendaftaran,
    ];

    require '../app/views/ppdb_success.php';
}

/**
 * Cek status PPDB (Public)
 */
function ppdb_check_status($pdo)
{
    $no_pendaftaran = $_GET['no'] ?? $_POST['no_pendaftaran'] ?? null;
    $result = null;

    if ($no_pendaftaran) {
        $stmt = $pdo->prepare("SELECT * FROM ppdb_pendaftaran WHERE no_pendaftaran = ?");
        $stmt->execute([$no_pendaftaran]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $data = [
        'result' => $result,
        'no_pendaftaran' => $no_pendaftaran,
    ];

    require '../app/views/ppdb_check_status.php';
}
