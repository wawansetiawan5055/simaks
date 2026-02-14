<?php
require_once __DIR__ . '/../models/AbsensiGuruModel.php';

function absensi_guru_index($pdo) {
    // [RBAC DINAMIS] Cek akses berdasarkan modul 'absensi_guru'
    if (!check_access('absensi_guru')) redirect('index.php');
    
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta) die("Error: Tahun Ajaran aktif tidak ditemukan.");

    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    
    // 1. PERBAIKAN: Ambil daftar guru yang PUNYA JADWAL hari ini
    $guru_list = AbsensiGuruModel::getGuruWithScheduleOnDate($pdo, $tanggal, $id_ta);
    
    // 2. Ambil data absensi yang sudah ada
    $absensi_hari_ini = AbsensiGuruModel::getAbsensiByTanggal($pdo, $tanggal, $id_ta);
    
    extract(compact('tanggal', 'guru_list', 'absensi_hari_ini'));
    include __DIR__ . '/../views/absensi_guru_index.php';
}

function absensi_guru_save($pdo) {
    // [RBAC DINAMIS]
    if (!check_access('absensi_guru')) redirect('index.php');
    
    $id_guru_piket = $_SESSION['id_guru_terkait'] ?? 0;
    if (has_role('Admin') && !$id_guru_piket) {
        $id_guru_piket = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
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