<?php
require_once __DIR__ . '/../models/AbsensiPiketModel.php';
require_once __DIR__ . '/../models/KelasModel.php';

function absensi_piket_index($pdo)
{
    // [RBAC DINAMIS] Cek akses berdasarkan modul 'absensi_piket' index
    if (!check_access('absensi_piket', 'index'))
        redirect('index.php');
    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas_list = KelasModel::all($pdo, $id_ta);
    include __DIR__ . '/../views/absensi_piket_pilih_kelas.php';
}

function absensi_piket_form($pdo)
{
    // [RBAC DINAMIS]
    // Cek Create/Update karena ini form input
    if (!can_do($pdo, 'absensi_piket', 'create') && !can_do($pdo, 'absensi_piket', 'update'))
        redirect('index.php?mod=absensi_piket');

    $id_kelas = $_GET['id_kelas'] ?? 0;
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    if (!$id_kelas || !$id_ta) {
        redirect('index.php?mod=absensi_piket');
    }

    $siswa_list = AbsensiPiketModel::getSiswaByKelas($pdo, $id_kelas, $id_ta);

    $kelas_info = $pdo->prepare("SELECT nama_kelas, tingkat FROM kelas WHERE id_kelas = ?");
    $kelas_info->execute([$id_kelas]);
    $kelas = $kelas_info->fetch(PDO::FETCH_ASSOC);

    include __DIR__ . '/../views/absensi_piket_form.php';
}

function absensi_piket_save($pdo)
{
    // [RBAC DINAMIS]
    if (!can_do($pdo, 'absensi_piket', 'create') && !can_do($pdo, 'absensi_piket', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan absensi.";
        // Perlu redirect yang aman
        redirect('index.php?mod=absensi_piket');
        return;
    }

    $id_guru_piket = $_SESSION['id_guru_terkait'] ?? 0;
    if (has_role('Admin') && !$id_guru_piket) {
        $id_guru_piket = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }

    $id_kelas_redirect = $_POST['id_kelas'] ?? 0;
    $tanggal_redirect = $_POST['tanggal'] ?? date('Y-m-d');
    $redirect_url = 'index.php?mod=absensi_piket&act=form&id_kelas=' . $id_kelas_redirect . '&tanggal=' . $tanggal_redirect;

    if (!$id_guru_piket) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: Informasi Guru Piket tidak valid.";
        redirect($redirect_url);
    }

    $data_to_save = [
        'id_kelas' => $_POST['id_kelas'],
        'tanggal' => $_POST['tanggal'],
        'id_ta' => $_SESSION['id_ta_aktif'],
        'id_guru_piket' => $id_guru_piket,
        'absensi' => $_POST['absensi']
    ];

    // Panggil Model dan tangkap hasilnya
    $result = AbsensiPiketModel::save($pdo, $data_to_save);

    // [LOGIKA POP-UP]
    if ($result) {
        $_SESSION['pesan_sukses'] = "Absensi piket berhasil disimpan! Status Hadir telah dicatat.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menyimpan absensi piket. Silakan cek log server.";
    }

    redirect($redirect_url);
}