<?php
/**
 * KewirausahaanController.php
 */

require_once __DIR__ . '/../models/KewirausahaanModel.php';
require_once __DIR__ . '/../models/SiswaModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

function kewirausahaan_index($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $tab = $_GET['tab'] ?? 'program';
    $id = $_GET['id'] ?? null;

    if ($id) {
        $kewirausahaan = KewirausahaanModel::find($pdo, $id);
        if (!$kewirausahaan)
            redirect('index.php?mod=kewirausahaan');

        $id_ta = $_SESSION['id_ta_aktif'];

        // Initialize all variables to prevent undefined variable errors
        $anggota_list = [];
        $jurnal_list = [];
        $agenda_list = [];
        $galeri_list = [];
        $available_students = [];
        $kelas_list = [];
        $tahapan_list = [];
        $produk_list = [];
        $keuangan_list = [];
        $summary = [];

        if ($tab == 'anggota') {
            $anggota_list = KewirausahaanModel::getAnggota($pdo, $id, $id_ta);
            $available_students = KewirausahaanModel::getAvailableStudents($pdo, $id, $id_ta);
            $kelas_list = KelasModel::all($pdo, $id_ta);
        } elseif ($tab == 'jurnal') {
            $jurnal_list = KewirausahaanModel::getJurnal($pdo, $id);
        } elseif ($tab == 'tahapan') {
            $tahapan_list = KewirausahaanModel::getTahapan($pdo, $id);
        } elseif ($tab == 'produk') {
            $produk_list = KewirausahaanModel::getProduk($pdo, $id);
        } elseif ($tab == 'galeri') {
            $galeri_list = KewirausahaanModel::getGaleri($pdo, $id);
        } elseif ($tab == 'program' || $tab == '') {
            $agenda_list = KewirausahaanModel::getAgenda($pdo, $id);
        } elseif ($tab == 'keuangan') {
            $keuangan_list = KewirausahaanModel::getKeuangan($pdo, $id);
            $summary = KewirausahaanModel::getSummary($pdo, $id);
        }

        include __DIR__ . '/../views/kewirausahaan_detail_tabs.php';
    } else {
        $kewirausahaan_list = KewirausahaanModel::getAll($pdo);
        // Fetch assigned activities for the modal dropdown
        $assigned_activities_list = KewirausahaanModel::getAssignedActivities($pdo, $_SESSION['id_ta_aktif']);
        include __DIR__ . '/../views/kewirausahaan_index.php';
    }
}

function kewirausahaan_form($pdo)
{
    // Redirect to index as we now use modal for add/edit
    redirect('index.php?mod=kewirausahaan');
}

function kewirausahaan_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    try {
        $is_new = empty($_POST['id_kewirausahaan']);
        $_POST['id_ta'] = $_SESSION['id_ta_aktif']; // Pass active TA for lookup
        KewirausahaanModel::save($pdo, $_POST);

        // Auto-create default stages for new activity
        if ($is_new) {
            $id_kew = $pdo->lastInsertId();
            KewirausahaanModel::initDefaultTahapan($pdo, $id_kew);
        }

        $_SESSION['pesan_sukses'] = "Data Kewirausahaan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=kewirausahaan');
}

function kewirausahaan_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id'] ?? 0;
    try {
        KewirausahaanModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=kewirausahaan');
}

// --- API & SUB FEATURES ---

function kewirausahaan_update_anggota($pdo)
{
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $id_kew = $_POST['id_kew'] ?? $_POST['id_kewirausahaan'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    $action = $_POST['action'] ?? '';
    $student_ids = $_POST['student_ids'] ?? [];

    if (!$id_kew || !$id_ta) {
        echo json_encode(['status' => 'error', 'message' => 'ID Kegiatan atau Tahun Ajaran tidak valid']);
        exit;
    }

    try {
        if ($action == 'add') {
            $count = KewirausahaanModel::addAnggota($pdo, $id_kew, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa ditambahkan."]);
        } elseif ($action == 'remove') {
            $count = KewirausahaanModel::removeAnggota($pdo, $id_kew, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa dihapus."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function kewirausahaan_search_students($pdo)
{
    header('Content-Type: application/json');
    $id_kew = $_GET['id_kewirausahaan'] ?? $_GET['id_kew'] ?? 0;
    $q = $_GET['q'] ?? '';
    $id_kelas = $_GET['id_kelas'] ?? '';
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    if (!$id_kew || !$id_ta) {
        echo json_encode(['status' => 'error', 'message' => 'ID Kegiatan atau Tahun Ajaran tidak valid', 'data' => []]);
        exit;
    }

    $results = KewirausahaanModel::getAvailableStudents($pdo, $id_kew, $id_ta, $q, $id_kelas);
    echo json_encode(['status' => 'success', 'data' => $results]);
    exit;
}

/**
 * Save Galeri
 */
function kewirausahaan_galeri_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_kew = $_POST['id_kewirausahaan'];
    $judul = $_POST['judul'] ?? null;

    // Handle file upload
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/kewirausahaan/galeri/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'galeri_' . $id_kew . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path)) {
            try {
                KewirausahaanModel::saveGaleri($pdo, $id_kew, $file_path, $judul);
                $_SESSION['pesan_sukses'] = "Foto berhasil diupload.";
            } catch (Exception $e) {
                $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
            }
        } else {
            $_SESSION['pesan_error'] = "Gagal upload file.";
        }
    } else {
        $_SESSION['pesan_error'] = "Tidak ada file yang diupload.";
    }

    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=galeri");
}

/**
 * Delete Galeri
 */
function kewirausahaan_galeri_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id_galeri'] ?? 0;
    $id_kew = $_GET['id_kewirausahaan'] ?? 0;

    try {
        KewirausahaanModel::deleteGaleri($pdo, $id);
        $_SESSION['pesan_sukses'] = "Foto dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=galeri");
}

// --- JURNAL ---

function kewirausahaan_jurnal_save($pdo)
{
    $id_kew = $_POST['id_kewirausahaan'];
    try {
        $id_jurnal = KewirausahaanModel::saveJurnal($pdo, $_POST);
        if (isset($_POST['presensi'])) {
            KewirausahaanModel::savePresensi($pdo, $id_jurnal, $_POST['presensi']);
        }
        $_SESSION['pesan_sukses'] = "Jurnal berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&act=index&id=$id_kew&tab=jurnal");
}

function kewirausahaan_jurnal_delete($pdo)
{
    $id = $_GET['id_jurnal'] ?? 0;
    $id_kew = $_GET['id_kewirausahaan'] ?? 0;
    KewirausahaanModel::deleteJurnal($pdo, $id);
    redirect("index.php?mod=kewirausahaan&act=index&id=$id_kew&tab=jurnal");
}

function kewirausahaan_jurnal_form($pdo)
{
    $id_kew = $_GET['id_kewirausahaan'];
    $id_jurnal = $_GET['id_jurnal'] ?? null;

    $kewirausahaan = KewirausahaanModel::find($pdo, $id_kew);
    $jurnal = $id_jurnal ? KewirausahaanModel::findJurnal($pdo, $id_jurnal) : null;
    $anggota = KewirausahaanModel::getAnggota($pdo, $id_kew, $_SESSION['id_ta_aktif']);
    $tahapan_list = KewirausahaanModel::getTahapan($pdo, $id_kew);

    $presensi = [];
    if ($jurnal) {
        $presensi = KewirausahaanModel::getPresensi($pdo, $id_jurnal);
    }
    include __DIR__ . '/../views/kewirausahaan_jurnal_form.php';
}

// --- TAHAPAN ---

function kewirausahaan_tahapan_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_POST['id_kewirausahaan'];
    try {
        KewirausahaanModel::saveTahapan($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Tahapan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=tahapan");
}

function kewirausahaan_tahapan_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id_tahapan'] ?? 0;
    $id_kew = $_GET['id_kewirausahaan'] ?? 0;
    try {
        KewirausahaanModel::deleteTahapan($pdo, $id);
        $_SESSION['pesan_sukses'] = "Tahapan dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=tahapan");
}

function kewirausahaan_tahapan_reorder($pdo)
{
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    $id = $_POST['id_tahapan'] ?? 0;
    $urutan = $_POST['urutan'] ?? 0;

    try {
        KewirausahaanModel::updateUrutan($pdo, $id, $urutan);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- PRODUK ---

function kewirausahaan_produk_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_POST['id_kewirausahaan'];
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] == 0) {
        $upload_dir = 'uploads/kewirausahaan/produk/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['foto_produk']['name'], PATHINFO_EXTENSION);
        $file_name = 'produk_' . $id_kew . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        move_uploaded_file($_FILES['foto_produk']['tmp_name'], $file_path);
    }

    $_POST['foto_produk'] = $file_path;

    try {
        KewirausahaanModel::saveProduk($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Produk berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=produk");
}

function kewirausahaan_produk_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id_produk'] ?? 0;
    $id_kew = $_GET['id_kewirausahaan'] ?? 0;
    try {
        KewirausahaanModel::deleteProduk($pdo, $id);
        $_SESSION['pesan_sukses'] = "Produk dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=produk");
}

// --- KEUANGAN ---

function kewirausahaan_keuangan_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_POST['id_kewirausahaan'];
    try {
        KewirausahaanModel::saveKeuangan($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Transaksi berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=keuangan");
}

function kewirausahaan_keuangan_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id_transaksi'] ?? 0;
    $id_kew = $_GET['id_kewirausahaan'] ?? 0;
    try {
        KewirausahaanModel::deleteKeuangan($pdo, $id);
        $_SESSION['pesan_sukses'] = "Transaksi dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=keuangan");
}

/**
 * Program Kerja & Agenda Actions
 */
function kewirausahaan_program_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_POST['id_kewirausahaan'];
    $file_path = null;

    // Handle File Upload if any
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/kewirausahaan/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . $id_kew . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path);
    }

    if ($file_path) {
        $_POST['file_path'] = $file_path;
    }

    // Default to 'agenda' for this save function
    if (!isset($_POST['tipe'])) {
        $_POST['tipe'] = 'agenda';
    }
    if (!isset($_POST['lokasi'])) {
        $_POST['lokasi'] = '-'; // Default lokasi if not provided
    }

    try {
        KewirausahaanModel::saveAgenda($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Agenda berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=program");
}

function kewirausahaan_program_delete_file($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_GET['id_kewirausahaan'];
    try {
        KewirausahaanModel::deleteProkerFile($pdo, $id_kew);
        $_SESSION['pesan_sukses'] = "File Program Kerja berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=program");
}

function kewirausahaan_agenda_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_GET['id_kewirausahaan'];
    $id_agenda = $_GET['id_agenda'];
    try {
        KewirausahaanModel::deleteAgenda($pdo, $id_agenda);
        $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=program");
}

function kewirausahaan_program_upload($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_kew = $_POST['id_kewirausahaan'];

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/kewirausahaan/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $filename = 'program_' . $id_kew . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file)) {
            try {
                // Always create New Agenda Entry (Program Kerja)
                $data = [
                    'id_kewirausahaan' => $id_kew,
                    'tanggal' => date('Y-m-d'),
                    'nama_kegiatan' => $_POST['nama_kegiatan_baru'] ?? 'Program Kerja',
                    'lokasi' => '-',
                    'keterangan' => 'File Program Kerja',
                    'file_path' => $target_file,
                    'tipe' => 'program'
                ];
                KewirausahaanModel::saveAgenda($pdo, $data);
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

    redirect("index.php?mod=kewirausahaan&id=$id_kew&tab=program");
}
