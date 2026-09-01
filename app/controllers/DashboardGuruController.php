<?php
// app/controllers/DashboardGuruController.php

require_once __DIR__ . '/../models/DashboardModel.php';

function dashboard_guru() {
    global $pdo;
    require_role('Guru');
    
    $guru_id = $_SESSION['user_id'];
    $id_ta = $_SESSION['id_ta_aktif'] ?? null;
    
    // Widget data
    $data = [];
    
    // 1. Jadwal hari ini
    $data['jadwal_hari_ini'] = DashboardModel::getJadwalHariIni($pdo, $guru_id, $id_ta);
    
    // 2. Persetujuan izin (khusus wali kelas)
    $data['permohonan_izin'] = DashboardModel::getPermohonanIzin($pdo, $guru_id, $id_ta);
    
    // 3. Tugas LMS yang belum dinilai
    $data['tugas_pending'] = DashboardModel::getTugasPendingPenilaian($pdo, $guru_id);
    
    // 4. Statistik umum
    $data['stats'] = [
        'total_mapel' => DashboardModel::countMapelGuru($pdo, $guru_id, $id_ta),
        'total_siswa' => DashboardModel::countSiswaGuru($pdo, $guru_id, $id_ta),
        'absensi_hari_ini' => DashboardModel::countAbsensiHariIni($pdo, $guru_id, $id_ta)
    ];
    
    include __DIR__ . '/../views/dashboard_guru.php';
}