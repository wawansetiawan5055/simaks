<?php
// app/controllers/ProfilSiswaController.php
require_once __DIR__ . '/../models/ProfilSiswaModel.php';
require_once __DIR__ . '/../models/SiswaModel.php';

function profil_siswa_index($pdo) {
    $user_roles = user_roles();

    // Jika yang mengakses adalah murni siswa, langsung arahkan ke CV mereka sendiri
    $is_pure_siswa = in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles);
    if ($is_pure_siswa) {
        $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
        redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa_login);
        return;
    }

    // Jika yang mengakses adalah Admin/Guru, arahkan ke Data Master Siswa
    // karena halaman list profil_siswa sudah redundan (digantikan oleh Data Master Siswa)
    if (in_array('Admin', $user_roles) || in_array('Guru', $user_roles)) {
        redirect('index.php?mod=siswa');
        return;
    }

    // Fallback keamanan
    redirect('index.php?mod=dashboard');
}

function profil_siswa_detail($pdo) {
    // Allow access if user has permission OR viewing own profile
    $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
    $id_siswa = $_GET['id'] ?? null;
    $is_own_profile = ($id_siswa_login && $id_siswa == $id_siswa_login);
    
    if (!$is_own_profile && !check_access('profil_siswa')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }

    $siswa = SiswaModel::find($pdo, $id_siswa);
    if (!$siswa) {
        $_SESSION['pesan_error'] = "Data Siswa tidak ditemukan.";
        redirect('index.php?mod=profil_siswa');
    }

    $profil = ProfilSiswaModel::getBySiswaId($pdo, $id_siswa);

    // Ambil foto profil siswa terpadu sesuai user & jenis kelamin (berkerudung jika perempuan)
    $id_pengguna_siswa = $siswa['id_pengguna'] ?? null;
    if (!$id_pengguna_siswa) {
        $stmt_u = $pdo->prepare("SELECT id_pengguna FROM siswa WHERE id_siswa = ?");
        $stmt_u->execute([$id_siswa]);
        $id_pengguna_siswa = $stmt_u->fetchColumn();
    }
    $avatar_src = get_user_photo($id_pengguna_siswa, $siswa['nama'] ?? null, $siswa['jk'] ?? null, 'siswa');

    // [BARU] Jika yang login adalah siswa murni, tampilkan CV
    $user_roles = user_roles();
    $is_pure_siswa = in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles);
    
    if ($is_own_profile && $is_pure_siswa) {
        $pengajuan_list = ProfilSiswaModel::getPengajuanSiswa($pdo, $id_siswa);
        include __DIR__ . '/../views/profil_siswa/cv.php';
    } else {
        include __DIR__ . '/../views/profil_siswa/detail.php';
    }
}

// [BARU] Fungsi untuk memproses pengajuan dari siswa
function profil_siswa_ajukan($pdo) {
    $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa_login) {
        $_SESSION['pesan_error'] = "Anda tidak berhak mengajukan perubahan.";
        redirect('index.php?mod=dashboard');
    }

    $kategori = $_POST['kategori'] ?? 'Data Umum';
    
    // Ambil semua data POST selain parameter teknis
    $data_perubahan = $_POST;
    unset($data_perubahan['kategori'], $data_perubahan['id_siswa']);
    
    $data_json = json_encode($data_perubahan);

    try {
        ProfilSiswaModel::ajukanPerubahan($pdo, $id_siswa_login, $kategori, $data_json);
        $_SESSION['pesan_sukses'] = "Pengajuan perubahan data berhasil dikirim dan sedang menunggu validasi Admin.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal mengirim pengajuan: " . $e->getMessage();
    }

    redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa_login);
}

function profil_siswa_save($pdo) {
    // Admin/TU/Guru only for editing
    if (!can_do($pdo, 'profil_siswa', 'update')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengubah data.";
        redirect('index.php?mod=dashboard');
    }

    $id_siswa = $_POST['id_siswa'];
    
    $data = [
        'id_siswa' => $id_siswa,
        'nama_ayah' => $_POST['nama_ayah'] ?? '',
        'pekerjaan_ayah' => $_POST['pekerjaan_ayah'] ?? '',
        'telp_ayah' => $_POST['telp_ayah'] ?? '',
        'nama_ibu' => $_POST['nama_ibu'] ?? '',
        'pekerjaan_ibu' => $_POST['pekerjaan_ibu'] ?? '',
        'telp_ibu' => $_POST['telp_ibu'] ?? '',
        'nama_wali' => $_POST['nama_wali'] ?? '',
        'pekerjaan_wali' => $_POST['pekerjaan_wali'] ?? '',
        'telp_wali' => $_POST['telp_wali'] ?? '',
        'alamat_wali' => $_POST['alamat_wali'] ?? ''
    ];

    try {
        ProfilSiswaModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Data Orang Tua/Wali berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }

    redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa);
}

function profil_siswa_upload($pdo) {
    // Students can upload their own documents
    $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
    $id_siswa = $_POST['id_siswa'];
    $is_own_profile = ($id_siswa_login && $id_siswa == $id_siswa_login);
    
    if (!$is_own_profile && !can_do($pdo, 'profil_siswa', 'update')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengunggah berkas.";
        redirect('index.php?mod=dashboard');
    }

    $id_siswa = $_POST['id_siswa'];
    $jenis_file = $_POST['jenis_file']; 

    $allowed_cols = ['file_ijazah', 'file_kartu_keluarga', 'file_akte_lahir', 'file_ktp_ortu', 'file_kip'];
    if (!in_array($jenis_file, $allowed_cols)) {
        $_SESSION['pesan_error'] = "Jenis file tidak valid.";
        redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa);
    }

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file = $_FILES['file_upload'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
             $_SESSION['pesan_error'] = "Format file harus PDF atau Gambar (JPG/PNG).";
             redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa);
        }
        
        if ($file['size'] > 5*1024*1024) {
             $_SESSION['pesan_error'] = "Ukuran file terlalu besar (Max 5MB).";
             redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa);
        }

        $target_dir = __DIR__ . '/../../public/uploads/siswa/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        // [BARU] Cek apakah yang upload murni siswa
        $user_roles = user_roles();
        $is_pure_siswa = in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles);

        if ($is_pure_siswa) {
            // Siswa: upload sebagai file temp dan masukkan ke pengajuan
            $filename = "temp_" . $jenis_file . "_" . $id_siswa . "_" . time() . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], $target_dir . $filename)) {
                $data_json = json_encode([
                    'jenis_berkas' => $jenis_file,
                    'file_temp' => $filename
                ]);
                ProfilSiswaModel::ajukanPerubahan($pdo, $id_siswa, 'Upload Berkas', $data_json);
                $_SESSION['pesan_sukses'] = "Berkas berhasil diunggah dan sedang menunggu validasi Admin/TU.";
            } else {
                $_SESSION['pesan_error'] = "Gagal memproses file.";
            }
        } else {
            $filename = $jenis_file . '_' . $id_siswa . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $target_dir . $filename)) {
                ProfilSiswaModel::updateFile($pdo, $id_siswa, $jenis_file, $filename);
                $_SESSION['pesan_sukses'] = "Berkas berhasil diupload.";
            } else {
                $_SESSION['pesan_error'] = "Gagal upload ke server.";
            }
        }
    } else {
        $_SESSION['pesan_error'] = "Tidak ada file.";
    }

    redirect('index.php?mod=profil_siswa&act=detail&id=' . $id_siswa . '&tab=berkas');
}

// [BARU] Halaman Riwayat Pengajuan Perubahan Data Siswa
function profil_siswa_riwayat($pdo) {
    $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
    
    // Hanya siswa yang bisa lihat riwayat diri sendiri
    // Admin/Guru dapat lihat dengan permission
    $id_siswa_req = $_GET['id'] ?? $id_siswa_login;
    $is_own = ($id_siswa_login && $id_siswa_req == $id_siswa_login);
    
    if (!$is_own && !check_access('profil_siswa')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke halaman ini.";
        redirect('index.php?mod=dashboard');
        return;
    }

    $id_siswa = $id_siswa_req;
    $pengajuan_list = ProfilSiswaModel::getPengajuanSiswa($pdo, $id_siswa);
    
    include __DIR__ . '/../views/profil_siswa/riwayat.php';
}

function profil_siswa_print($pdo) {
    // Allow if has permission
    if (!check_access('profil_siswa')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    $id_siswa = $_GET['id'];
    $siswa = SiswaModel::find($pdo, $id_siswa);
    $profil = ProfilSiswaModel::getBySiswaId($pdo, $id_siswa);
    
    // Ambil foto profil siswa terpadu
    $avatar_src = get_user_photo($siswa['id_pengguna'] ?? null, $siswa['nama'] ?? null, $siswa['jk'] ?? null, 'siswa');
    
    include __DIR__ . '/../views/profil_siswa/print.php';
}
?>
