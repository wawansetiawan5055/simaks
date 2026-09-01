<?php
// app/controllers/JadwalPiketController.php

require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . '/../models/JadwalPiketModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

function jadwal_piket_index($pdo)
{
    if (!check_access('jadwal_piket') && !in_array('Admin', user_roles()) && !in_array('Kurikulum', user_roles())) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect(BASE_URL . 'index.php');
        return;
    }

    $ta_list = TahunAjaranModel::all($pdo);
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif) {
        $ta_row = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $id_ta_aktif = $ta_row['id_ta'] ?? 1;
    }

    $id_ta_filter = (int)($_GET['id_ta'] ?? $_SESSION['id_ta_viewing'] ?? $id_ta_aktif);

    // Ambil jadwal mingguan piket
    $jadwal_weekly = JadwalPiketModel::getJadwalWeekly($pdo, $id_ta_filter);

    // Ambil matriks penugasan kerja GTK (KBM + Piket + Non-KBM/Ngantor)
    $gtk_matrix = JadwalPiketModel::getGtkWorkMatrix($pdo, $id_ta_filter);

    // Ambil daftar guru aktif untuk form
    $guru_list = $pdo->query("SELECT id_guru, nama, kode_guru, nuptk FROM guru WHERE status='Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

    $active_tab = $_GET['tab'] ?? 'piket';

    extract(compact('ta_list', 'id_ta_filter', 'jadwal_weekly', 'gtk_matrix', 'guru_list', 'active_tab'));
    include __DIR__ . '/../views/jadwal_piket_index.php';
}

function jadwal_piket_save($pdo)
{
    if (!in_array('Admin', user_roles()) && !in_array('Kurikulum', user_roles())) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect(BASE_URL . 'index.php?mod=jadwal_piket');
        return;
    }

    $id_ta = (int)($_POST['id_ta'] ?? 0);
    $hari = trim($_POST['hari'] ?? '');
    $id_guru = (int)($_POST['id_guru'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (!$id_ta || empty($hari) || !$id_guru) {
        $_SESSION['pesan_error'] = "Data tidak lengkap. Mohon pilih Tahun Ajaran, Hari, dan Guru.";
        redirect(BASE_URL . "index.php?mod=jadwal_piket&id_ta=$id_ta&tab=piket");
        return;
    }

    try {
        JadwalPiketModel::save($pdo, [
            'id_ta' => $id_ta,
            'hari' => $hari,
            'id_guru' => $id_guru,
            'keterangan' => $keterangan
        ]);
        $_SESSION['pesan_sukses'] = "Guru piket hari $hari berhasil ditambahkan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan jadwal piket: " . $e->getMessage();
    }

    redirect(BASE_URL . "index.php?mod=jadwal_piket&id_ta=$id_ta&tab=piket");
}

function jadwal_piket_delete($pdo)
{
    if (!in_array('Admin', user_roles()) && !in_array('Kurikulum', user_roles())) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect(BASE_URL . 'index.php?mod=jadwal_piket');
        return;
    }

    $id = (int)($_GET['id'] ?? 0);
    $id_ta = (int)($_GET['id_ta'] ?? 0);

    try {
        JadwalPiketModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Jadwal piket berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus jadwal piket: " . $e->getMessage();
    }

    redirect(BASE_URL . "index.php?mod=jadwal_piket" . ($id_ta ? "&id_ta=$id_ta" : "") . "&tab=piket");
}

function jadwal_piket_save_non_kbm($pdo)
{
    if (!in_array('Admin', user_roles()) && !in_array('Kurikulum', user_roles())) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect(BASE_URL . 'index.php?mod=jadwal_piket&tab=non_kbm');
        return;
    }

    $id_ta = (int)($_POST['id_ta'] ?? 0);
    $non_kbm_raw = $_POST['non_kbm'] ?? []; // Array: [id_guru][hari] = 1 or 'Ngantor / Standby'
    $tugas_keterangan = $_POST['tugas_ket'] ?? []; // Array: [id_guru][hari] = 'Kepala Lab / Kurikulum'

    if (!$id_ta) {
        $_SESSION['pesan_error'] = "Tahun Ajaran tidak valid.";
        redirect(BASE_URL . 'index.php?mod=jadwal_piket&tab=non_kbm');
        return;
    }

    $entries = [];
    foreach ($non_kbm_raw as $id_guru => $days) {
        foreach ($days as $day => $val) {
            if ($val) {
                $ket = $tugas_keterangan[$id_guru][$day] ?? 'Ngantor / Standby';
                $entries[] = [
                    'id_guru' => (int)$id_guru,
                    'hari' => $day,
                    'jenis_tugas' => $ket ?: 'Ngantor / Standby',
                    'keterangan' => null
                ];
            }
        }
    }

    try {
        JadwalPiketModel::saveNonKbmBatch($pdo, $id_ta, $entries);
        $_SESSION['pesan_sukses'] = "Jadwal Hari Ngantor / Non-KBM GTK berhasil disimpan!";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan jadwal non-KBM: " . $e->getMessage();
    }

    redirect(BASE_URL . "index.php?mod=jadwal_piket&id_ta=$id_ta&tab=non_kbm");
}
