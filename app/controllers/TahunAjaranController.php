<?php
require_once __DIR__ . '/../models/TahunAjaranModel.php';

function ta_index($pdo) {
    if (!is_logged_in() || !check_access('ta')) redirect('index.php');
    
    $ta_aktif = TahunAjaranModel::aktif($pdo);
    $ta_list = TahunAjaranModel::all($pdo);
    
    // [INI PERBAIKANNYA]
    // Mengganti 'ta_list.php' agar memuat file 'ta_index.php' Anda yang baru
    include __DIR__ . '/../views/ta_index.php';
}

function ta_form($pdo, $id = null) {
    if (!is_logged_in() || !check_access('ta', 'form')) redirect('index.php');
    
    $ta = $id ? TahunAjaranModel::find($pdo, $id) : null;
    include __DIR__ . '/../views/ta_form.php';
}

function ta_save($pdo) {
    if (!is_logged_in() || !check_access('ta', 'save')) redirect('index.php');
    
    // Ini masih menggunakan logika 'backup' Anda (rentan bug autofill)
    // tapi akan berfungsi JIKA Anda membersihkan cache browser Anda
    TahunAjaranModel::save($pdo, $_POST);
    
    $_SESSION['pesan_sukses'] = "Data Tahun Ajaran berhasil disimpan!";
    redirect('index.php?mod=ta');
}

function ta_delete($pdo, $id) {
    if (!is_logged_in() || !check_access('ta', 'delete')) redirect('index.php');
    
    TahunAjaranModel::delete($pdo, $id);
    $_SESSION['pesan_sukses'] = "Data Tahun Ajaran berhasil dihapus!";
    redirect('index.php?mod=ta');
}

function ta_set_aktif($pdo, $id) {
    if (!is_logged_in() || !check_access('ta', 'set_aktif')) redirect('index.php');
    
    // 1. Update database
    TahunAjaranModel::set_aktif($pdo, $id);
    
    // 2. PERBAIKAN: Ambil data TA yang baru aktif
    $ta_baru_aktif = TahunAjaranModel::find($pdo, $id);
    
    // 3. Update session secara langsung
    if ($ta_baru_aktif) {
        $_SESSION['id_ta_aktif'] = $ta_baru_aktif['id_ta'];
        $_SESSION['nama_ta_aktif'] = $ta_baru_aktif['nama_ta'];
    }
    
    $_SESSION['pesan_sukses'] = "Tahun Ajaran berhasil diaktifkan!";
    redirect('index.php?mod=ta');
}