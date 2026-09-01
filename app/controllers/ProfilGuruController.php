<?php
// app/controllers/ProfilGuruController.php
require_once __DIR__ . '/../models/ProfilGuruModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

function profil_guru_index($pdo) {
    // Jika user adalah Guru (bukan Admin), langsung arahkan ke profil sendiri
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    if ($id_guru_login && !in_array('Admin', $_SESSION['roles'] ?? []) && !in_array('TU', $_SESSION['roles'] ?? [])) {
        redirect('index.php?mod=profil_guru&act=detail&id=' . $id_guru_login);
        return;
    }

    // Admin-only for listing all teachers
    if (!can_do($pdo, 'profil_guru', 'read')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    // Get all teachers (using GuruModel)
    $gurus = GuruModel::all($pdo);
    
    include __DIR__ . '/../views/profil_guru/index.php';
}

function profil_guru_detail($pdo) {
    // Allow access if user has permission OR viewing own profile
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    if (!$id_guru_login && isset($_SESSION['user_id'])) {
        $stmt_cek = $pdo->prepare("SELECT id_guru FROM guru WHERE id_pengguna = ? LIMIT 1");
        $stmt_cek->execute([$_SESSION['user_id']]);
        $id_guru_login = $stmt_cek->fetchColumn() ?: 0;
    }
    $id_guru = $_GET['id'] ?? null;

    // Jika Guru tidak menyertakan ?id= (akses langsung dari sidebar), gunakan id sendiri
    if (!$id_guru && $id_guru_login) {
        $id_guru = $id_guru_login;
    }
    
    $is_own_profile = ($id_guru_login && $id_guru == $id_guru_login);
    
    if (!$is_own_profile && !check_access('profil_guru')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    if (!$id_guru) redirect('index.php?mod=dashboard');

    $guru = GuruModel::find($pdo, $id_guru);
    if (!$guru) {
        $_SESSION['pesan_error'] = "Data Guru tidak ditemukan.";
        redirect('index.php?mod=dashboard');
    }

    $profil = ProfilGuruModel::getByGuruId($pdo, $id_guru);

    // Dapatkan ID TA Aktif (menggunakan standar session aplikasi)
    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    // Ambil Jabatan Struktural (jika ada)
    $stmt_jab = $pdo->prepare("SELECT jenis_jabatan FROM penugasan_jabatan WHERE id_guru = ? AND id_ta = ?");
    $stmt_jab->execute([$id_guru, $id_ta]);
    $jabatans = $stmt_jab->fetchAll(PDO::FETCH_COLUMN);

    // Ambil Tugas Tambahan: Wali Kelas
    $stmt_walas = $pdo->prepare("SELECT k.nama_kelas FROM penugasan_wali_kelas pwk JOIN kelas k ON pwk.id_kelas = k.id_kelas WHERE pwk.id_guru = ? AND pwk.id_ta = ? AND pwk.jenis_tugas = 'Wali Kelas'");
    $stmt_walas->execute([$id_guru, $id_ta]);
    $walas_list = $stmt_walas->fetchAll(PDO::FETCH_COLUMN);
    foreach ($walas_list as $w) {
        $jabatans[] = "Wali Kelas " . $w;
    }

    // Ambil Tugas Tambahan: Pembina Non-Akademik
    $stmt_pembina = $pdo->prepare("SELECT mk.nama_kegiatan FROM penugasan_pembina pp JOIN master_kegiatan mk ON pp.id_kegiatan = mk.id_kegiatan WHERE pp.id_guru = ? AND pp.id_ta = ?");
    $stmt_pembina->execute([$id_guru, $id_ta]);
    $pembina_list = $stmt_pembina->fetchAll(PDO::FETCH_COLUMN);
    foreach ($pembina_list as $p) {
        $jabatans[] = "Pembina " . $p;
    }

    $jabatan_text = empty($jabatans) ? 'Guru Mata Pelajaran' : implode(', ', $jabatans);

    // Ambil Mapel yang Diampu
    $stmt_mapel = $pdo->prepare("SELECT DISTINCT m.nama_mapel FROM guru_mapel gm JOIN mapel m ON gm.id_mapel = m.id_mapel WHERE gm.id_guru = ? AND gm.id_ta = ?");
    $stmt_mapel->execute([$id_guru, $id_ta]);
    $mapels = $stmt_mapel->fetchAll(PDO::FETCH_COLUMN);
    $mapel_text = empty($mapels) ? '-' : implode(', ', $mapels);

    // Ambil semua daftar Mapel untuk dropdown sertifikasi
    $stmt_all_mapel = $pdo->query("SELECT nama_mapel FROM mapel ORDER BY nama_mapel ASC");
    $all_mapel = $stmt_all_mapel->fetchAll(PDO::FETCH_COLUMN);

    include __DIR__ . '/../views/profil_guru/detail.php';
}

function profil_guru_save($pdo) {
    // Allow if has permission or editing own profile
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    if (!$id_guru_login && isset($_SESSION['user_id'])) {
        $stmt_cek = $pdo->prepare("SELECT id_guru FROM guru WHERE id_pengguna = ? LIMIT 1");
        $stmt_cek->execute([$_SESSION['user_id']]);
        $id_guru_login = $stmt_cek->fetchColumn() ?: 0;
    }
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
        'pendidikan_terakhir' => $_POST['pendidikan_terakhir'] ?? '',
        'sertifikasi' => $_POST['sertifikasi'] ?? 'Belum Tersertifikasi',
        'mapel_sertifikasi' => $_POST['mapel_sertifikasi'] ?? ''
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
    if (!$id_guru_login && isset($_SESSION['user_id'])) {
        $stmt_cek = $pdo->prepare("SELECT id_guru FROM guru WHERE id_pengguna = ? LIMIT 1");
        $stmt_cek->execute([$_SESSION['user_id']]);
        $id_guru_login = $stmt_cek->fetchColumn() ?: 0;
    }
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
