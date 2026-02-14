<?php
/**
 * TahfidzController.php
 */

require_once __DIR__ . '/../models/TahfidzModel.php';
require_once __DIR__ . '/../models/SiswaModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

function tahfidz_index($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $tab = $_GET['tab'] ?? 'program';
    $id = $_GET['id'] ?? null;

    if ($id) {
        $tahfidz = TahfidzModel::find($pdo, $id);
        if (!$tahfidz)
            redirect('index.php?mod=tahfidz');

        $id_ta = $_SESSION['id_ta_aktif'];

        // Initialize all variables to prevent undefined variable errors
        $anggota_list = [];
        $jurnal_list = [];
        $setoran_list = [];
        $agenda_list = [];
        $galeri_list = [];
        $available_students = [];
        $kelas_list = [];
        $surah_list = [];

        if ($tab == 'program' || $tab == '') {
            $agenda_list = TahfidzModel::getAgenda($pdo, $id);
        } elseif ($tab == 'anggota') {
            $anggota_list = TahfidzModel::getAnggota($pdo, $id, $id_ta);
            $available_students = TahfidzModel::getAvailableStudents($pdo, $id, $id_ta);
            $kelas_list = KelasModel::all($pdo, $id_ta);
        } elseif ($tab == 'jurnal') {
            $jurnal_list = TahfidzModel::getJurnal($pdo, $id);
        } elseif ($tab == 'setoran') {
            $anggota_list = TahfidzModel::getAnggota($pdo, $id, $id_ta); // List siswa to click
            $surah_list = TahfidzModel::getRefSurah($pdo);

            // If a student is selected to view history
            $id_siswa_sel = $_GET['id_siswa'] ?? null;
            if ($id_siswa_sel) {
                $jenis_filter = $_GET['jenis'] ?? 'Harian';
                $setoran_list = TahfidzModel::getSetoranBySiswa($pdo, $id_siswa_sel, $id, $jenis_filter);
            }
        } elseif ($tab == 'galeri') {
            $galeri_list = TahfidzModel::getGaleri($pdo, $id);
        }

        include __DIR__ . '/../views/tahfidz_detail_tabs.php';
    } else {
        $tahfidz_list = TahfidzModel::getAll($pdo);
        $tahfidz_list = TahfidzModel::getAll($pdo);
        // [REVISI] Hanya ambil guru yang SUDAH DITUGASKAN di Penugasan Guru (Kategori Tahfidz)
        $guru_list = TahfidzModel::getAssignedPembinaList($pdo, $_SESSION['id_ta_aktif']);
        $assigned_activities = TahfidzModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);
        include __DIR__ . '/../views/tahfidz_index.php';
    }
}

function tahfidz_form($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id'] ?? null;
    $tahfidz = null;
    if ($id) {
        $tahfidz = TahfidzModel::find($pdo, $id);
    }

    $guru_list = GuruModel::all($pdo);
    $assigned_activities = TahfidzModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);

    include __DIR__ . '/../views/tahfidz_form.php';
}

function tahfidz_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    try {
        TahfidzModel::save($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data Tahfidz berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=tahfidz');
}

function tahfidz_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id'] ?? 0;
    try {
        TahfidzModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=tahfidz');
}

// --- API & SUB FEATURES ---

function tahfidz_update_anggota($pdo)
{
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id_tah = $_POST['id_tahfidz'] ?? 0;
    $student_ids = $_POST['student_ids'] ?? [];
    $id_ta = $_SESSION['id_ta_aktif'];

    try {
        if ($action == 'add') {
            $count = TahfidzModel::addAnggota($pdo, $id_tah, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa ditambahkan."]);
        } elseif ($action == 'remove') {
            $count = TahfidzModel::removeAnggota($pdo, $id_tah, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa dihapus."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function tahfidz_search_students($pdo)
{
    header('Content-Type: application/json');
    $id_tah = $_GET['id_tahfidz'] ?? 0;
    $q = $_GET['q'] ?? '';
    $id_kelas = $_GET['id_kelas'] ?? '';
    $id_ta = $_SESSION['id_ta_aktif'];

    $data = TahfidzModel::getAvailableStudents($pdo, $id_tah, $id_ta, $q, $id_kelas);
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// --- JURNAL ---

function tahfidz_jurnal_save($pdo)
{
    $id_tah = $_POST['id_tahfidz'];
    try {
        TahfidzModel::saveJurnal($pdo, $_POST);
        // Add presensi logic if needed, but for Tahfidz user asked for Jurnal Umum vs Setoran.
        // If presensi needed here (general attendance), add it. Assuming Yes for consistency.
        // But for now strict to journal.
        $_SESSION['pesan_sukses'] = "Jurnal berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=tahfidz&act=index&id=$id_tah&tab=jurnal");
}

function tahfidz_jurnal_delete($pdo)
{
    $id = $_GET['id_jurnal'] ?? 0;
    $id_tah = $_GET['id_tahfidz'] ?? 0;
    TahfidzModel::deleteJurnal($pdo, $id);
    redirect("index.php?mod=tahfidz&act=index&id=$id_tah&tab=jurnal");
}

function tahfidz_jurnal_form($pdo)
{
    $id_tah = $_GET['id_tahfidz'];
    $id_jurnal = $_GET['id_jurnal'] ?? null;
    $tahfidz = TahfidzModel::find($pdo, $id_tah);
    $jurnal = $id_jurnal ? TahfidzModel::findJurnal($pdo, $id_jurnal) : null;

    // Fetch Anggota & Presensi
    $id_ta = $_SESSION['id_ta_aktif'];
    $anggota = TahfidzModel::getAnggota($pdo, $id_tah, $id_ta);
    $presensi = $jurnal ? TahfidzModel::getPresensi($pdo, $id_jurnal) : [];

    include __DIR__ . '/../views/tahfidz_jurnal_form.php';
}

// --- SETORAN ---

function tahfidz_setoran_save($pdo)
{
    $id_tah = $_POST['id_tahfidz'];
    $id_siswa = $_POST['id_siswa']; // Redirect back to this student
    $jenis = $_POST['jenis_setoran'] ?? 'Harian';

    try {
        TahfidzModel::saveSetoran($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Setoran berhasil dicatat.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=tahfidz&act=index&id=$id_tah&tab=setoran&id_siswa=$id_siswa&jenis=$jenis");
}

function tahfidz_setoran_delete($pdo)
{
    $id_tah = $_GET['id_tahfidz'];
    $id_setoran = $_GET['id_setoran'];
    $id_siswa = $_GET['id_siswa'];
    $jenis = $_GET['jenis'] ?? 'Harian';

    TahfidzModel::deleteSetoran($pdo, $id_setoran);
    redirect("index.php?mod=tahfidz&act=index&id=$id_tah&tab=setoran&id_siswa=$id_siswa&jenis=$jenis");
}


function tahfidz_program_upload($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_tah = $_POST['id_tahfidz'];

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/tahfidz/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $filename = 'program_' . $id_tah . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file)) {
            try {
                // Always create New Agenda Entry (Program Kerja)
                $data = [
                    'id_tahfidz' => $id_tah,
                    'tanggal' => date('Y-m-d'),
                    'nama_agenda' => $_POST['nama_kegiatan_baru'] ?? 'Program Kerja',
                    'lokasi' => '-',
                    'keterangan' => 'File Program Kerja',
                    'file_path' => $target_file,
                    'tipe' => 'program'
                ];
                TahfidzModel::saveAgenda($pdo, $data);
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

    redirect("index.php?mod=tahfidz&id=$id_tah&tab=program");
}

function tahfidz_agenda_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_tah = $_POST['id_tahfidz'];
    $file_path = null;

    // Handle File Upload for Agenda (Laporan Kegiatan)
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/tahfidz/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . $id_tah . '_' . time() . '.' . $file_ext;
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
        TahfidzModel::saveAgenda($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Agenda berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=tahfidz&id=$id_tah&tab=program");
}

function tahfidz_agenda_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_agenda = $_GET['id_agenda'];
    $id_tah = $_GET['id_tahfidz'];
    try {
        TahfidzModel::deleteAgenda($pdo, $id_agenda);
        $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=tahfidz&id=$id_tah&tab=program");
}

function tahfidz_proker_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_tah = $_GET['id_tahfidz'];
    try {
        TahfidzModel::deleteProkerFile($pdo, $id_tah);
        $_SESSION['pesan_sukses'] = "File Program Kerja berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=tahfidz&id=$id_tah&tab=program");
}
