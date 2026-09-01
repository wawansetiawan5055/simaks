<?php
require_once __DIR__ . '/../models/AbsensiGuruModel.php';

function absensi_guru_index($pdo) {
    // [RBAC DINAMIS] Cek akses berdasarkan modul 'absensi_guru'
    if (!check_access('absensi_guru') && !in_array('Admin', user_roles())) redirect('index.php');
    
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta) die("Error: Tahun Ajaran aktif tidak ditemukan.");

    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    
    $active_tab = $_GET['tab'] ?? 'fisik';
    
    // 1. Ambil daftar GTK wajib hadir FISIK (Tatap Muka, Piket, Non-KBM, Tendik)
    $guru_list = AbsensiGuruModel::getGuruWithScheduleOnDate($pdo, $tanggal, $id_ta);

    // 2. Ambil daftar Guru yang terjadwal KBM DARING / ONLINE LMS
    $guru_online_list = AbsensiGuruModel::getGuruOnlineScheduleOnDate($pdo, $tanggal, $id_ta);
    
    // 3. Ambil data absensi fisik yang sudah ada
    $absensi_hari_ini = AbsensiGuruModel::getAbsensiByTanggal($pdo, $tanggal, $id_ta);
    $has_existing_data = !empty($absensi_hari_ini);
    $is_past_date = ($tanggal < date('Y-m-d'));
    $is_edit_mode = ($has_existing_data || $is_past_date);
    
    extract(compact('tanggal', 'active_tab', 'guru_list', 'guru_online_list', 'absensi_hari_ini', 'has_existing_data', 'is_past_date', 'is_edit_mode'));
    include __DIR__ . '/../views/absensi_guru_index.php';
}

function absensi_guru_save($pdo) {
    // [RBAC DINAMIS]
    if (!check_access('absensi_guru')) redirect('index.php');
    
    $id_guru_piket = $_SESSION['id_guru_terkait'] ?? 0;
    if (!$id_guru_piket) {
        $id_guru_piket = (int)$pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn() ?: 1;
    }
    if (!$id_guru_piket) die("Gagal menyimpan: Informasi Guru Piket tidak valid.");

    // Jika tidak ada guru yang punya jadwal hari ini, $POST['absensi'] akan kosong
    if (empty($_POST['absensi'])) {
         $_SESSION['pesan_sukses'] = "Tidak ada data absensi untuk disimpan (tidak ada guru dengan jadwal KBM hari ini).";
         redirect('index.php?mod=absensi_guru&tanggal='.$_POST['tanggal']);
         return;
    }

    $data_to_save = [
        'tanggal' => $_POST['tanggal'],
        'id_ta' => $_SESSION['id_ta_aktif'],
        'id_guru_piket' => $id_guru_piket, 
        'absensi' => $_POST['absensi']
    ];

    AbsensiGuruModel::save($pdo, $data_to_save);

    $_SESSION['pesan_sukses'] = "Absensi guru berhasil disimpan!";
    redirect('index.php?mod=absensi_guru&tanggal='.$_POST['tanggal']);
}