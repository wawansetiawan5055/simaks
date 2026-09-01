<?php
/**
 * KokulikulerController.php
 */

require_once __DIR__ . '/../models/KokulikulerModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

require_once __DIR__ . '/../models/SiswaModel.php';
require_once __DIR__ . '/../models/KelasModel.php';

/**
 * Halaman Utama: Daftar Kokulikuler (Tabs)
 */
function kokulikuler_index($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $tab = $_GET['tab'] ?? 'program'; // program, anggota, jurnal, galeri
    $id = $_GET['id'] ?? null; // ID Kokul require for tabs other than 'kegiatan'

    // List of 8 Dimensions Graduate Profile
    $profil_master = KokulikulerModel::getProfilLulusanMaster($pdo);

    if ($id) {
        $profil_terpilih = KokulikulerModel::getProfilByKokulikuler($pdo, $id);
        // Detail View with Tabs
        $kokul = KokulikulerModel::find($pdo, $id);
        if (!$kokul)
            redirect('index.php?mod=kokulikuler');

        $id_ta = $_SESSION['id_ta_aktif'];

        // Data for Tabs - initialize all to prevent undefined variable errors
        $anggota_list = [];
        $jurnal_list = [];
        $agenda_list = [];
        $galeri_list = [];
        $available_students = [];
        $kelas_list = [];
        $penilaian_list = [];

        if ($tab == 'anggota') {
            $anggota_list = KokulikulerModel::getAnggota($pdo, $id, $id_ta);
            $available_students = KokulikulerModel::getAvailableStudents($pdo, $id, $id_ta);
            $kelas_list = KelasModel::all($pdo, $id_ta);
        } elseif ($tab == 'jurnal') {
            $jurnal_list = KokulikulerModel::getJurnal($pdo, $id);
        } elseif ($tab == 'program' || $tab == 'kegiatan' || $tab == '') {
            $agenda_list = KokulikulerModel::getAgenda($pdo, $id);
        } elseif ($tab == 'galeri') {
            $galeri_list = KokulikulerModel::getGaleri($pdo, $id);
        } elseif ($tab == 'nilai') {
            $penilaian_list = KokulikulerModel::getPenilaian($pdo, $id, $id_ta);
        }

        include __DIR__ . '/../views/kokulikuler_detail_tabs.php';

    } else {
        // Main List View
        $kokul_list = KokulikulerModel::getAll($pdo);
        // Guru list for coordinator dropdown
        $guru_list = GuruModel::all($pdo);
        // For backward compatibility (penugasan dropdown) - kept but may be empty
        $assigned_activities_list = KokulikulerModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);

        // Add dimension mapping to kokul_list
        foreach ($kokul_list as &$k) {
            $k['selected_profil'] = KokulikulerModel::getProfilByKokulikuler($pdo, $k['id_kokulikuler']);
        }

        include __DIR__ . '/../views/kokulikuler_index.php';
    }
}

/**
 * Form Tambah/Edit (Data Master)
 */
function kokulikuler_form($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id'] ?? null;
    $kokul = null;
    if ($id) {
        $kokul = KokulikulerModel::find($pdo, $id);
    }

    $guru_list = GuruModel::all($pdo);
    $assigned_activities = KokulikulerModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);

    include __DIR__ . '/../views/kokulikuler_form.php';
}

/**
 * Simpan Data Kegiatan
 */
function kokulikuler_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    try {
        // Collect profil IDs from checkbox
        $_POST['id_profil_array'] = $_POST['id_profil'] ?? [];
        KokulikulerModel::save($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data kegiatan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }

    redirect('index.php?mod=kokulikuler');
}

/**
 * Hapus Data Kegiatan
 */
function kokulikuler_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id'] ?? 0;
    try {
        KokulikulerModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }

    redirect('index.php?mod=kokulikuler');
}

// --- API / AJAX METHODS ---

function kokulikuler_update_anggota($pdo)
{
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id_kokul = $_POST['id_kokul'] ?? 0;
    $student_ids = $_POST['student_ids'] ?? [];
    $id_ta = $_SESSION['id_ta_aktif'];

    try {
        if ($action == 'add') {
            $count = KokulikulerModel::addAnggota($pdo, $id_kokul, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa ditambahkan."]);
        } elseif ($action == 'remove') {
            $count = KokulikulerModel::removeAnggota($pdo, $id_kokul, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa dihapus."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function kokulikuler_search_students($pdo)
{
    header('Content-Type: application/json');
    $id_kokul = $_GET['id_kokul'] ?? 0;
    $q = $_GET['q'] ?? '';
    $id_kelas = $_GET['id_kelas'] ?? '';
    $id_ta = $_SESSION['id_ta_aktif'];

    $data = KokulikulerModel::getAvailableStudents($pdo, $id_kokul, $id_ta, $q, $id_kelas);
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// --- JURNAL METHODS ---

function kokulikuler_jurnal_save($pdo)
{
    // Save Jurnal Header
    $id_kokul = $_POST['id_kokulikuler'];
    try {
        $id_jurnal = KokulikulerModel::saveJurnal($pdo, $_POST);

        // Save Presensi if exists
        if (isset($_POST['presensi'])) {
            KokulikulerModel::savePresensi($pdo, $id_jurnal, $_POST['presensi']);
        }

        $_SESSION['pesan_sukses'] = "Jurnal & Absensi berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kokulikuler&act=index&id=$id_kokul&tab=jurnal");
}

function kokulikuler_jurnal_delete($pdo)
{
    $id = $_GET['id_jurnal'] ?? 0;
    $id_kokul = $_GET['id_kokul'] ?? 0;
    KokulikulerModel::deleteJurnal($pdo, $id);
    $_SESSION['pesan_sukses'] = "Jurnal dihapus.";
    redirect("index.php?mod=kokulikuler&act=index&id=$id_kokul&tab=jurnal");
}

function kokulikuler_jurnal_form($pdo)
{
    $id_kokul = $_GET['id_kokulikuler'];
    $id_jurnal = $_GET['id_jurnal'] ?? null;

    $kokul = KokulikulerModel::find($pdo, $id_kokul);
    $jurnal = $id_jurnal ? KokulikulerModel::findJurnal($pdo, $id_jurnal) : null;
    $anggota = KokulikulerModel::getAnggota($pdo, $id_kokul, $_SESSION['id_ta_aktif']);

    // If edit, get existing presensi
    $presensi = [];
    if ($jurnal) {
        $presensi = KokulikulerModel::getPresensi($pdo, $id_jurnal);
    }

    include __DIR__ . '/../views/kokulikuler_jurnal_form.php';
}

// --- PROGRAM KERJA & AGENDA ---

function kokulikuler_program_upload($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kokul = $_POST['id_kokulikuler'];

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/kokulikuler/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $filename = 'program_' . $id_kokul . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file)) {
            try {
                // Always create New Agenda Entry (Program Kerja)
                $data = [
                    'id_kokulikuler' => $id_kokul,
                    'tanggal' => date('Y-m-d'),
                    'nama_agenda' => $_POST['nama_kegiatan_baru'] ?? 'Program Kerja',
                    'lokasi' => '-',
                    'keterangan' => 'File Program Kerja',
                    'file_path' => $target_file,
                    'tipe' => 'program'
                ];
                KokulikulerModel::saveAgenda($pdo, $data);
                $_SESSION['pesan_sukses'] = "Program kerja berhasil diupload.";
            } catch (Exception $e) {
                $_SESSION['pesan_error'] = "Error DB: " . $e->getMessage();
            }
        } else {
            $_SESSION['pesan_error'] = "Gagal memindahkan file yang diunggah.";
        }
    } else {
        $_SESSION['pesan_error'] = "Tidak ada file yang diunggah atau terjadi kesalahan.";
    }

    redirect("index.php?mod=kokulikuler&id=$id_kokul&tab=program");
}

function kokulikuler_agenda_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kokul = $_POST['id_kokulikuler'];
    $file_path = null;

    // Handle File Upload for Agenda (Laporan Kegiatan)
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/kokulikuler/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . $id_kokul . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path);
    }

    if ($file_path) {
        $_POST['file_path'] = $file_path;
    }

    if (!isset($_POST['tipe'])) {
        $_POST['tipe'] = 'agenda';
    }

    try {
        KokulikulerModel::saveAgenda($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Agenda berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kokulikuler&id=$id_kokul&tab=program");
}

function kokulikuler_agenda_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_agenda = $_GET['id_agenda'];
    $id_kokul = $_GET['id_kokulikuler'];
    try {
        KokulikulerModel::deleteAgenda($pdo, $id_agenda);
        $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kokulikuler&id=$id_kokul&tab=program");
}

function kokulikuler_nilai_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kokul = $_POST['id_kokulikuler'];
    $id_ta = $_SESSION['id_ta_aktif'];

    // Format: nilai_data[id_siswa][nilai], nilai_data[id_siswa][deskripsi]
    $nilai_data = $_POST['nilai_data'] ?? [];

    try {
        KokulikulerModel::savePenilaian($pdo, $id_kokul, $id_ta, $nilai_data);
        $_SESSION['pesan_sukses'] = "Penilaian berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kokulikuler&id=$id_kokul&tab=nilai");
}

