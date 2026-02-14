<?php
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/ProfilSekolahModel.php';

function kelas_index($pdo)
{
    if (!check_access('kelas', 'index'))
        redirect('index.php');

    // Ambil id_ta dari session (prioritas: yang dipilih user > TA aktif sistem)
    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? null;

    if (!$id_ta) {
        // Set pesan peringatan tapi JANGAN redirect, biar menu tetap bisadiakses
        $_SESSION['pesan_warning'] = 'Info: Tahun ajaran tidak terdeteksi. Silakan atur tahun ajaran aktif di menu Data Master.';
        $kelas_list = []; // List kosong
    } else {
        $kelas_list = KelasModel::all($pdo, $id_ta);
    }

    include __DIR__ . '/../views/kelas_index.php';
}

function kelas_form($pdo, $id = null)
{
    $permission = $id ? 'update' : 'create';
    if (!can_do($pdo, 'kelas', $permission))
        redirect('index.php?mod=kelas');

    // 1. Ambil data bentuk pendidikan
    $bentuk_pendidikan = ProfilSekolahModel::getBentukPendidikan($pdo);

    // Bersihkan string agar mudah dicek (huruf kecil)
    $bentuk = strtolower($bentuk_pendidikan ?? '');

    // 2. Logika Penentuan Tingkat (Menggunakan strpos agar 'SMA/MA' terbaca)
    $tingkat_list = [];

    if (strpos($bentuk, 'sd') !== false || strpos($bentuk, 'mi') !== false) {
        // Jika mengandung kata 'sd' atau 'mi' (misal: 'SD/MI', 'SDIT')
        $tingkat_list = ['I', 'II', 'III', 'IV', 'V', 'VI'];
    } elseif (strpos($bentuk, 'smp') !== false || strpos($bentuk, 'mts') !== false) {
        // Jika mengandung kata 'smp' atau 'mts'
        $tingkat_list = ['VII', 'VIII', 'IX'];
    } elseif (strpos($bentuk, 'sma') !== false || strpos($bentuk, 'ma') !== false || strpos($bentuk, 'smk') !== false) {
        // Jika mengandung kata 'sma', 'ma', atau 'smk' (termasuk 'SMA/MA')
        $tingkat_list = ['X', 'XI', 'XII'];
    } else {
        // Default cadangan jika format tidak dikenali
        $tingkat_list = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    }

    $kelas = $id ? KelasModel::find($pdo, $id) : null;
    include __DIR__ . '/../views/kelas_form.php';
}

function kelas_save($pdo)
{
    $id = $_POST['id_kelas'] ?? null;
    $permission = $id ? 'update' : 'create';

    if (!can_do($pdo, 'kelas', $permission)) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk " . ($id ? "mengubah" : "menambah") . " data.";
        redirect('index.php?mod=kelas');
        return;
    }

    if (empty($_POST['nama_kelas']) || empty($_POST['tingkat'])) {
        redirect('index.php?mod=kelas&act=form');
        return;
    }

    // Inject id_ta dari session untuk insert baru
    $data = $_POST;
    if (empty($data['id_kelas'])) {
        $data['id_ta'] = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? null;
        if (!$data['id_ta']) {
            $_SESSION['pesan_error'] = 'Gagal menyimpan: Tahun ajaran tidak ditemukan. Silakan pilih tahun ajaran di dropdown header.';
            redirect('index.php?mod=kelas');
            return;
        }
    }

    try {
        KelasModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Data kelas berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=kelas');
}

function kelas_delete($pdo, $id)
{
    if (!can_do($pdo, 'kelas', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=kelas');
        return;
    }
    if ($id) {
        try {
            KelasModel::delete($pdo, $id);
            $_SESSION['pesan_sukses'] = "Data kelas berhasil dihapus.";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['pesan_error'] = "Gagal menghapus: Kelas ini masih memiliki data siswa atau jadwal. Silakan hapus data terkait terlebih dahulu.";
            } else {
                $_SESSION['pesan_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
        }
    }
    redirect('index.php?mod=kelas');
}
?>