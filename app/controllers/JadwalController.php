<?php
require_once __DIR__ . '/../models/JadwalModel.php';

function jadwal_index($pdo)
{
    if (!check_access('jadwal', 'index'))
        redirect('index.php');

    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) {
        $_SESSION['pesan_error'] = "Silakan atur Tahun Ajaran aktif terlebih dahulu.";
        redirect('index.php?mod=ta');
    }

    // Ambil data untuk filter DAN form
    $guru_mapel_list = JadwalModel::all_guru_mapel($pdo, $id_ta_tampil);
    $guru_list = JadwalModel::all_guru_unique($pdo, $id_ta_tampil);
    $kelas_list = JadwalModel::all_kelas($pdo, $id_ta_tampil);
    $jam_list = JadwalModel::getKbmJamSlots($pdo); // Mengambil slot KBM

    $view_type = $_GET['view'] ?? 'kelas';
    if ($view_type === 'sekolah') {
        $view_type = 'kelas';
    }

    $program = !empty($_GET['program']) ? $_GET['program'] : 'reguler';

    // Filter kelas berdasarkan program jika ada
    $filtered_kelas_list = $kelas_list;
    if (!empty($program)) {
        $filtered_kelas_list = array_filter($kelas_list, function($k) use ($program) {
            $jk = $k['jenis_kelas'] ?? 'reguler';
            return $jk === $program;
        });
        $filtered_kelas_list = array_values($filtered_kelas_list);
    }

    // Default id_kelas_filter ke kelas pertama jika tidak dipilih atau parameter kosong
    $id_kelas_param = trim($_GET['id_kelas'] ?? '');
    if ($id_kelas_param !== '' && $id_kelas_param !== '0') {
        $id_kelas_filter = $id_kelas_param;
    } else {
        $id_kelas_filter = ($view_type === 'kelas' && !empty($filtered_kelas_list)) ? $filtered_kelas_list[0]['id_kelas'] : (!empty($kelas_list) ? $kelas_list[0]['id_kelas'] : null);
    }

    $id_guru_param = trim($_GET['id_guru'] ?? '');
    if ($id_guru_param !== '' && $id_guru_param !== '0') {
        $id_guru_filter = $id_guru_param;
    } else {
        $id_guru_filter = ($view_type === 'guru' && !empty($guru_list)) ? $guru_list[0]['id_guru'] : null;
    }

    // Auto sync program dengan jenis kelas yang sedang dipilih jika program belum sinkron
    if ($view_type === 'kelas' && $id_kelas_filter) {
        foreach ($kelas_list as $k) {
            if ($k['id_kelas'] == $id_kelas_filter) {
                $program = $k['jenis_kelas'] ?? 'reguler';
                break;
            }
        }
    }
    if (empty($program)) {
        $program = 'reguler';
    }

    // Re-filter list kelas jika program berubah
    if (!empty($program)) {
        $filtered_kelas_list = array_filter($kelas_list, function($k) use ($program) {
            $jk = $k['jenis_kelas'] ?? 'reguler';
            return $jk === $program;
        });
        $filtered_kelas_list = array_values($filtered_kelas_list);
    }

    $result = JadwalModel::getJadwalLengkap($pdo, $id_ta_tampil, $view_type, $id_kelas_filter, $id_guru_filter);

    extract(compact('guru_mapel_list', 'guru_list', 'kelas_list', 'filtered_kelas_list', 'jam_list', 'view_type', 'id_kelas_filter', 'id_guru_filter', 'program', 'result'));
    include __DIR__ . '/../views/jadwal_index.php';
}

/**
 * ⭐ REVISI: Fungsi Save dengan Deteksi Bentrok dan Cek JJM
 */
function jadwal_save($pdo)
{
    // Jadwal biasanya "Create" atau "Update" (di sini kita anggap Create/Update sama)
    // Karena form jadwal seringkali untuk insert banyak sekaligus.
    // Kita cek izin "create" (atau "update" jika kita anggap edit slot)
    // Mari kita cek 'create' karena ini menambah slot.
    // Atau kita cek 'create' OR 'update' ?
    // Simpelnya, jika user punya izin 'create' dia bisa tambah/timpa.

    if (!can_do($pdo, 'jadwal', 'create') && !can_do($pdo, 'jadwal', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan jadwal.";
        // Redirect kembali dengan filter (params)
        redirect('index.php?mod=jadwal&view=kelas&id_kelas=' . $_POST['id_kelas']);
        return;
    }

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Tidak ada TA Aktif untuk menyimpan data.");

    // Accept multiple selected jam values (jam[]). Normalize to array in the Model.
    $data = [
        'id_guru_mapel' => $_POST['id_guru_mapel'],
        'id_kelas' => $_POST['id_kelas'],
        'hari_kbm' => $_POST['hari_kbm'],
        'mode_kbm' => $_POST['mode_kbm'] ?? 'offline',
        'id_jam' => $_POST['jam'] ?? [], // can be single value or array
        'id_ta' => $id_ta_aktif
    ];

    try {
        // Panggil fungsi 'save' yang baru di Model
        JadwalModel::jadwal_save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Jadwal berhasil disimpan.";

    } catch (Exception $e) {
        // Tangkap pesan error dari Model (cth: "Bentrok!" atau "JJM Penuh!")
        $_SESSION['pesan_error'] = "GAGAL: " . $e->getMessage();
    }

    // Redirect kembali ke halaman dengan filter kelas yang sudah dipilih
    redirect('index.php?mod=jadwal&view=kelas&id_kelas=' . $_POST['id_kelas']);
}

function jadwal_delete($pdo, $id)
{
    if (!can_do($pdo, 'jadwal', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus jadwal.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    try {
        JadwalModel::jadwal_delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Jadwal berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
