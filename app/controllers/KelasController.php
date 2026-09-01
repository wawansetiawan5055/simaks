<?php
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/ProfilSekolahModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';

function kelas_index($pdo)
{
    if (!check_access('kelas', 'index'))
        redirect('index.php');

    // Ambil id_ta dari session (prioritas: yang dipilih user > TA aktif sistem)
    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? null;
    $kelas_list = [];
    $kelas_count = 0;
    $previous_ta = null;
    $can_import_previous = false;

    if (!$id_ta) {
        // Set pesan peringatan tapi JANGAN redirect, biar menu tetap bisa diakses
        $_SESSION['pesan_warning'] = 'Info: Tahun ajaran tidak terdeteksi. Silakan atur tahun ajaran aktif di menu Data Master.';
    } else {
        $kelas_list = KelasModel::all($pdo, $id_ta);
        $kelas_count = count($kelas_list);
        $previous_ta = TahunAjaranModel::findPrevious($pdo, $id_ta);
        $can_import_previous = $previous_ta && $kelas_count === 0;
    }

    extract(compact('kelas_list', 'kelas_count', 'previous_ta', 'can_import_previous'));
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

function kelas_import_from_previous($pdo)
{
    if (!can_do($pdo, 'kelas', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk melakukan impor kelas.";
        redirect('index.php?mod=kelas');
        return;
    }

    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? null;
    if (!$id_ta) {
        $_SESSION['pesan_error'] = 'Gagal impor: Tahun ajaran tidak ditemukan. Silakan pilih tahun ajaran di header.';
        redirect('index.php?mod=kelas');
        return;
    }

    $kelas_count = KelasModel::countByTa($pdo, $id_ta);
    if ($kelas_count > 0) {
        $_SESSION['pesan_error'] = 'Gagal impor: TA saat ini sudah memiliki kelas. Hapus kelas terlebih dahulu atau gunakan TA kosong.';
        redirect('index.php?mod=kelas');
        return;
    }

    $previous_ta = TahunAjaranModel::findPrevious($pdo, $id_ta);
    if (!$previous_ta) {
        $_SESSION['pesan_error'] = 'Gagal impor: Tidak ditemukan Tahun Ajaran sebelumnya yang valid.';
        redirect('index.php?mod=kelas');
        return;
    }

    try {
        $copied = KelasModel::copyFromTa($pdo, $previous_ta['id_ta'], $id_ta);
        if ($copied > 0) {
            $_SESSION['pesan_sukses'] = "Berhasil menarik $copied kelas dari TA {$previous_ta['nama_ta']} ke TA saat ini.";
        } else {
            $_SESSION['pesan_warning'] = 'Proses impor selesai, tetapi tidak ada kelas baru yang ditambahkan.';
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal impor kelas: " . $e->getMessage();
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

/**
 * 1-Click Dynamic AJAX Toggle Jenis Program Kelas (Reguler, PJJ, Menginduk)
 */
function kelas_toggle_jenis($pdo)
{
    if (!can_do($pdo, 'kelas', 'update')) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak memiliki izin.']);
            exit;
        }
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=kelas');
        return;
    }

    $id_kelas = (int)($_POST['id_kelas'] ?? $_GET['id_kelas'] ?? 0);
    $jenis = $_POST['jenis_kelas'] ?? $_GET['jenis_kelas'] ?? 'reguler';

    if ($id_kelas > 0 && in_array($jenis, ['reguler', 'pjj', 'menginduk'])) {
        KelasModel::updateJenisKelas($pdo, $id_kelas, $jenis);
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'jenis_kelas' => $jenis, 'id_kelas' => $id_kelas]);
            exit;
        }
        $_SESSION['pesan_sukses'] = "Status program kelas berhasil diperbarui menjadi: " . strtoupper($jenis);
    }
    redirect('index.php?mod=kelas');
}
?>