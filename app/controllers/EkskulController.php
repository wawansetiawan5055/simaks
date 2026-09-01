<?php
/**
 * EkskulController.php
 */

require_once __DIR__ . '/../models/EkskulModel.php';
require_once __DIR__ . '/../models/GuruModel.php'; // Untuk list pembina
require_once __DIR__ . '/../models/SiswaModel.php'; // Jika perlu
require_once __DIR__ . '/../models/KelasModel.php'; // Untuk filter kelas

/**
 * Halaman Utama: Daftar Ekskul (Tabs)
 */
function ekskul_index($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $tab = $_GET['tab'] ?? 'program';
    $id = $_GET['id'] ?? null;

    if ($id) {
        // Detail View with Tabs
        $ekskul = EkskulModel::find($pdo, $id);
        if (!$ekskul)
            redirect('index.php?mod=ekskul');

        $id_ta = $_SESSION['id_ta_aktif'];

        $anggota_list = [];
        $jurnal_list = [];
        $available_students = [];
        $kelas_list = [];
        $program_list = [];
        $galeri_list = [];

        if ($tab == 'anggota') {
            $anggota_list = EkskulModel::getAnggota($pdo, $id, $id_ta);
            $available_students = EkskulModel::getAvailableStudents($pdo, $id, $id_ta);
            $kelas_list = KelasModel::all($pdo, $id_ta);
        } elseif ($tab == 'jurnal') {
            $jurnal_list = EkskulModel::getJurnal($pdo, $id);
        } elseif ($tab == 'program') {
            $program_list = EkskulModel::getProgramKerja($pdo, $id);
        } elseif ($tab == 'galeri') {
            $galeri_list = EkskulModel::getGaleri($pdo, $id);
        }

        if ($tab == 'anggota') {
            $anggota_list = EkskulModel::getAnggota($pdo, $id, $id_ta);
            $available_students = EkskulModel::getAvailableStudents($pdo, $id, $id_ta);
            $kelas_list = KelasModel::all($pdo, $id_ta);
        } elseif ($tab == 'jurnal') {
            $jurnal_list = EkskulModel::getJurnal($pdo, $id);
        } elseif ($tab == 'program') {
            $program_list = EkskulModel::getProgramKerja($pdo, $id);
        } elseif ($tab == 'galeri') {
            $galeri_list = EkskulModel::getGaleri($pdo, $id);
        } elseif ($tab == 'nilai') {
            $nilai_list = EkskulModel::getNilai($pdo, $id);
        }

        include __DIR__ . '/../views/ekskul_detail_tabs.php';

    } else {
        // Main List View
        $ekskul_list = EkskulModel::getAll($pdo);
        // Added for Modal Dropdown
        $assigned_activities_list = EkskulModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);

        include __DIR__ . '/../views/ekskul_index.php';
    }
}

/**
 * Form Tambah/Edit
 */
function ekskul_form($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id'] ?? null;
    $ekskul = null;
    if ($id) {
        $ekskul = EkskulModel::find($pdo, $id);
    }

    $guru_list = GuruModel::all($pdo);
    $guru_list = GuruModel::all($pdo);
    $assigned_activities = EkskulModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);

    include __DIR__ . '/../views/ekskul_form.php';
}

/**
 * Simpan Data
 */
function ekskul_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    try {
        EkskulModel::save($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data ekskul berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }

    redirect('index.php?mod=ekskul');
}

/**
 * Hapus Data
 */
function ekskul_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id'] ?? 0;
    try {
        EkskulModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }

    redirect('index.php?mod=ekskul');
}

// remove legacy separate anggota pages if no longer needed, 
// OR keep logic redirected to tabs. 
// For now, I'll remove separate 'ekskul_anggota' function refs in router if replaced.

// --- JURNAL METHODS ---

function ekskul_jurnal_save($pdo)
{
    $id_ekskul = $_POST['id_ekskul'];
    try {
        $id_jurnal = EkskulModel::saveJurnal($pdo, $_POST);
        if (isset($_POST['presensi'])) {
            EkskulModel::savePresensi($pdo, $id_jurnal, $_POST['presensi']);
        }
        $_SESSION['pesan_sukses'] = "Jurnal & Absensi berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=ekskul&act=index&id=$id_ekskul&tab=jurnal");
}

function ekskul_jurnal_delete($pdo)
{
    $id = $_GET['id_jurnal'] ?? 0;
    $id_ekskul = $_GET['id_ekskul'] ?? 0;
    EkskulModel::deleteJurnal($pdo, $id);
    $_SESSION['pesan_sukses'] = "Jurnal dihapus.";
    redirect("index.php?mod=ekskul&act=index&id=$id_ekskul&tab=jurnal");
}

function ekskul_jurnal_form($pdo)
{
    $id_ekskul = $_GET['id_ekskul'];
    $id_jurnal = $_GET['id_jurnal'] ?? null;

    $ekskul = EkskulModel::find($pdo, $id_ekskul);
    $jurnal = $id_jurnal ? EkskulModel::findJurnal($pdo, $id_jurnal) : null;
    $anggota = EkskulModel::getAnggota($pdo, $id_ekskul, $_SESSION['id_ta_aktif']);

    $presensi = [];
    if ($jurnal) {
        $presensi = EkskulModel::getPresensi($pdo, $id_jurnal);
    }

    include __DIR__ . '/../views/ekskul_jurnal_form.php';
}

/**
 * Manajemen Anggota (Drag & Drop UI)
 */
function ekskul_anggota($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_ekskul = $_GET['id'] ?? 0;
    $ekskul = EkskulModel::find($pdo, $id_ekskul);

    if (!$ekskul) {
        $_SESSION['pesan_error'] = "Data tidak ditemukan.";
        redirect('index.php?mod=ekskul');
        return;
    }

    $id_ta = $_SESSION['id_ta_aktif'];

    // Ambil daftar kelas untuk filter
    $kelas_list = KelasModel::all($pdo, $id_ta);

    // Ambil Anggota Saat Ini
    $anggota_list = EkskulModel::getAnggota($pdo, $id_ekskul, $id_ta);

    // Ambil Calon Anggota (Akan di-load via AJAX search biasanya, tapi untuk awal load 50 pertama)
    $available_students = EkskulModel::getAvailableStudents($pdo, $id_ekskul, $id_ta);

    include __DIR__ . '/../views/ekskul_anggota.php';
}

/**
 * API: Update Anggota (Add/Remove via Drag Drop)
 * Endpoint Ajax
 */
function ekskul_update_anggota($pdo)
{
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $action = $_POST['action'] ?? ''; // 'add' or 'remove'
    $id_ekskul = $_POST['id_ekskul'] ?? 0;
    $student_ids = $_POST['student_ids'] ?? []; // Array of IDs
    $id_ta = $_SESSION['id_ta_aktif'];

    try {
        $count = 0;
        if ($action == 'add') {
            $count = EkskulModel::addAnggota($pdo, $id_ekskul, $student_ids, $id_ta);
            $msg = "$count siswa ditambahkan.";
        } elseif ($action == 'remove') {
            $count = EkskulModel::removeAnggota($pdo, $id_ekskul, $student_ids, $id_ta);
            $msg = "$count siswa dikeluarkan.";
        } else {
            throw new Exception("Invalid action");
        }

        echo json_encode(['status' => 'success', 'message' => $msg]);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

/**
 * API: Search Available Students (untuk kolom kiri Drag & Drop)
 */
function ekskul_search_students($pdo)
{
    $id_ekskul = $_GET['id_ekskul'] ?? 0;
    $keyword = $_GET['q'] ?? '';
    $id_kelas = $_GET['id_kelas'] ?? ''; // Tambahan: filter kelas
    $id_ta = $_SESSION['id_ta_aktif'];

    $results = EkskulModel::getAvailableStudents($pdo, $id_ekskul, $id_ta, $keyword, $id_kelas);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $results]);
    exit;
}

/**
 * Save Program Kerja
 */
function ekskul_program_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_ekskul = $_POST['id_ekskul'];
    $file_path = null;

    // Handle file upload (Laporan Kegiatan)
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/ekskul/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . $id_ekskul . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path);
    }

    $_POST['file_path'] = $file_path;
    $_POST['tipe'] = 'agenda';

    try {
        EkskulModel::saveProgramKerja($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Agenda program berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=program");
}

/**
 * Delete Program Kerja
 */
function ekskul_program_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id_program'] ?? 0;
    $id_ekskul = $_GET['id_ekskul'] ?? 0;

    try {
        EkskulModel::deleteProgramKerja($pdo, $id);
        $_SESSION['pesan_sukses'] = "Program kerja dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=program");
}

function ekskul_program_delete_file($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id_program'] ?? 0;
    $id_ekskul = $_GET['id_ekskul'] ?? 0;

    try {
        EkskulModel::deleteProgramFile($pdo, $id);
        $_SESSION['pesan_sukses'] = "File berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=program");
}

function ekskul_program_upload($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_ekskul = $_POST['id_ekskul'];

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/ekskul/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'program_' . $id_ekskul . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path)) {
            try {
                // Always create New Program Kerja Entry (Program Kerja)
                $data = [
                    'id_ekskul' => $id_ekskul,
                    'tipe' => 'program',
                    'tanggal' => date('Y-m-d'),
                    'nama_kegiatan' => $_POST['nama_kegiatan_baru'] ?? 'Program Kerja',
                    'lokasi' => '-',
                    'file_path' => $file_path
                ];
                EkskulModel::saveProgramKerja($pdo, $data);
                $_SESSION['pesan_sukses'] = "Program kerja berhasil diupload.";
            } catch (Exception $e) {
                $_SESSION['pesan_error'] = "Error DB: " . $e->getMessage();
            }
        } else {
            $_SESSION['pesan_error'] = "Gagal memindahkan file upload.";
        }
    } else {
        $_SESSION['pesan_error'] = "Tidak ada file yang dipilih.";
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=program");
}

/**
 * Save Galeri
 */
function ekskul_galeri_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_ekskul = $_POST['id_ekskul'];
    $judul = $_POST['judul'] ?? null;
    $foto_cam_data = $_POST['foto_cam_data'] ?? '';

    $upload_dir = 'uploads/ekskul/galeri/';
    if (!is_dir($upload_dir))
        mkdir($upload_dir, 0777, true);

    $file_path = null;

    if (!empty($foto_cam_data) && preg_match('/^data:image\/(\w+);base64,/', $foto_cam_data, $cam_match)) {
        $raw_base64 = substr($foto_cam_data, strpos($foto_cam_data, ',') + 1);
        $decoded = base64_decode($raw_base64);
        $ext = strtolower($cam_match[1]) === 'png' ? 'png' : 'jpg';
        if ($decoded) {
            $file_name = 'galeri_' . $id_ekskul . '_' . time() . '.' . $ext;
            $file_path = $upload_dir . $file_name;
            file_put_contents($file_path, $decoded);
        }
    } elseif (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'galeri_' . $id_ekskul . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;
        if (!move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path)) {
            $file_path = null;
        }
    }

    if ($file_path) {
        try {
            EkskulModel::saveGaleri($pdo, $id_ekskul, $file_path, $judul);
            $_SESSION['pesan_sukses'] = "Foto dokumentasi kegiatan berhasil disimpan.";
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['pesan_error'] = "Silakan ambil foto dengan kamera atau pilih file gambar.";
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=galeri");
}

/**
 * Delete Galeri
 */
function ekskul_galeri_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id_galeri'] ?? 0;
    $id_ekskul = $_GET['id_ekskul'] ?? 0;

    try {
        EkskulModel::deleteGaleri($pdo, $id);
        $_SESSION['pesan_sukses'] = "Foto dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=galeri");
}

/**
 * Save Nilai
 */
function ekskul_nilai_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_ekskul = $_POST['id_ekskul'];
    $data_nilai = $_POST['nilai'] ?? [];
    // Structure: nilai[id_siswa][nilai] and nilai[id_siswa][deskripsi]

    try {
        EkskulModel::saveNilai($pdo, $id_ekskul, $data_nilai);
        $_SESSION['pesan_sukses'] = "Penilaian berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=ekskul&id=$id_ekskul&tab=nilai");
}
