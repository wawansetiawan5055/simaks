<?php
/**
 * PembiasaanController.php
 */

require_once __DIR__ . '/../models/PembiasaanModel.php';

require_once __DIR__ . '/../models/SiswaModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/GuruModel.php';

function pembiasaan_index($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $tab = $_GET['tab'] ?? 'kegiatan';
    $id = $_GET['id'] ?? null;

    if ($id) {
        $pembiasaan = PembiasaanModel::find($pdo, $id);
        if (!$pembiasaan)
            redirect('index.php?mod=pembiasaan');

        $id_ta = $_SESSION['id_ta_aktif'];

        // Initialize all variables to prevent undefined variable errors
        $anggota_list = [];
        $jurnal_list = [];
        $agenda_list = [];
        $galeri_list = [];
        $available_students = [];
        $kelas_list = [];
        $rekap_hybrid = [];
        $penilaian_stored = [];
        $bulan = $_GET['bulan'] ?? date('n');
        $tahun = $_GET['tahun'] ?? date('Y');
        $bulan_opsi = ['1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

        if ($tab == 'anggota') {
            $anggota_list = PembiasaanModel::getAnggota($pdo, $id, $id_ta);
            $available_students = PembiasaanModel::getAvailableStudents($pdo, $id, $id_ta);
            $kelas_list = KelasModel::all($pdo, $id_ta);
        } elseif ($tab == 'jurnal') {
            $jurnal_list = PembiasaanModel::getJurnal($pdo, $id);
        } elseif ($tab == 'program' || $tab == 'kegiatan' || $tab == '') {
            $agenda_list = PembiasaanModel::getAgenda($pdo, $id);
        } elseif ($tab == 'galeri') {
            $galeri_list = PembiasaanModel::getGaleri($pdo, $id);
        } elseif ($tab == 'penilaian' || $tab == 'nilai') {
            $anggota_list = PembiasaanModel::getAnggota($pdo, $id, $id_ta);
            $rekap_hybrid = PembiasaanModel::getRekapHybrid($pdo, $id, $bulan, $tahun);
            $penilaian_stored = PembiasaanModel::getPenilaian($pdo, $id, $bulan, $tahun);
        }

        include __DIR__ . '/../views/pembiasaan_detail_tabs.php';
    } else {
        $pembiasaan_list = PembiasaanModel::getAll($pdo);
        $guru_list = GuruModel::all($pdo);
        include __DIR__ . '/../views/pembiasaan_index.php';
    }
}

function pembiasaan_form($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id'] ?? null;
    $pembiasaan = null;
    if ($id) {
        $pembiasaan = PembiasaanModel::find($pdo, $id);
    }

    $guru_list = GuruModel::all($pdo);

    include __DIR__ . '/../views/pembiasaan_form.php';
}

function pembiasaan_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    try {
        PembiasaanModel::save($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data pembiasaan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=pembiasaan');
}

function pembiasaan_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id = $_GET['id'] ?? 0;
    try {
        PembiasaanModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=pembiasaan');
}

// --- API & SUB FEATURES ---

function pembiasaan_update_anggota($pdo)
{
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id_pem = $_POST['id_pembiasaan'] ?? 0;
    $student_ids = $_POST['student_ids'] ?? [];
    $id_ta = $_SESSION['id_ta_aktif'];

    try {
        if ($action == 'add') {
            $count = PembiasaanModel::addAnggota($pdo, $id_pem, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa ditambahkan."]);
        } elseif ($action == 'remove') {
            $count = PembiasaanModel::removeAnggota($pdo, $id_pem, $student_ids, $id_ta);
            echo json_encode(['status' => 'success', 'message' => "$count siswa dihapus."]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function pembiasaan_search_students($pdo)
{
    header('Content-Type: application/json');
    $id_pem = $_GET['id_pembiasaan'] ?? 0;
    $q = $_GET['q'] ?? '';
    $id_kelas = $_GET['id_kelas'] ?? '';
    $id_ta = $_SESSION['id_ta_aktif'];

    $data = PembiasaanModel::getAvailableStudents($pdo, $id_pem, $id_ta, $q, $id_kelas);
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// --- JURNAL ---

function pembiasaan_jurnal_save($pdo)
{
    $id_pem = $_POST['id_pembiasaan'];
    $tanggal = $_POST['tanggal'];
    $id_jurnal = $_POST['id_jurnal'] ?? null;

    try {
        // Check for duplicate date (only for new entries)
        if (!$id_jurnal) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM jurnal_pembiasaan WHERE id_pembiasaan = ? AND tanggal = ?");
            $stmt->execute([$id_pem, $tanggal]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['pesan_error'] = "Jurnal untuk tanggal ini sudah ada. Satu hari hanya boleh satu kali input.";
                redirect("index.php?mod=pembiasaan&act=index&id=$id_pem&tab=jurnal");
                return;
            }
        }

        $id_jurnal = PembiasaanModel::saveJurnal($pdo, $_POST);
        if (isset($_POST['presensi'])) {
            PembiasaanModel::savePresensi($pdo, $id_jurnal, $_POST['presensi']);
        }
        $_SESSION['pesan_sukses'] = "Jurnal berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=pembiasaan&act=index&id=$id_pem&tab=jurnal");
}

function pembiasaan_jurnal_delete($pdo)
{
    $id = $_GET['id_jurnal'] ?? 0;
    $id_pem = $_GET['id_pembiasaan'] ?? 0;
    PembiasaanModel::deleteJurnal($pdo, $id);
    redirect("index.php?mod=pembiasaan&act=index&id=$id_pem&tab=jurnal");
}

function pembiasaan_jurnal_form($pdo)
{
    $id_pem = $_GET['id_pembiasaan'];
    $id_jurnal = $_GET['id_jurnal'] ?? null;

    $pembiasaan = PembiasaanModel::find($pdo, $id_pem);
    $jurnal = $id_jurnal ? PembiasaanModel::findJurnal($pdo, $id_jurnal) : null;
    $anggota = PembiasaanModel::getAnggota($pdo, $id_pem, $_SESSION['id_ta_aktif']);

    $presensi = [];
    if ($jurnal) {
        $presensi = PembiasaanModel::getPresensi($pdo, $id_jurnal);
    }
    include __DIR__ . '/../views/pembiasaan_jurnal_form.php';
}

// --- REKAP MANUAL ---

function pembiasaan_rekap_form($pdo)
{
    $id_pem = $_GET['id_pembiasaan'];
    $bulan = $_GET['bulan'] ?? date('n');
    $tahun = $_GET['tahun'] ?? date('Y');

    $pembiasaan = PembiasaanModel::find($pdo, $id_pem);
    $anggota = PembiasaanModel::getAnggota($pdo, $id_pem, $_SESSION['id_ta_aktif']);
    $rekap_data = PembiasaanModel::getRekapHybrid($pdo, $id_pem, $bulan, $tahun);

    include __DIR__ . '/../views/pembiasaan_rekap_form.php';
}

function pembiasaan_rekap_save($pdo)
{
    $id_pem = $_POST['id_pembiasaan'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $rekap = $_POST['rekap'] ?? []; // Array [id_siswa][H/S/I/A]

    try {
        PembiasaanModel::saveRekapPresensi($pdo, $id_pem, $rekap, $bulan, $tahun);
        $_SESSION['pesan_sukses'] = "Rekap manual bulan $bulan/$tahun berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }

    redirect("index.php?mod=pembiasaan&act=rekap_form&id_pembiasaan=$id_pem&bulan=$bulan&tahun=$tahun");
}

// --- PROGRAM & AGENDA ---

function pembiasaan_program_upload($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_pem = $_POST['id_pembiasaan'];

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/pembiasaan/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $filename = 'program_' . $id_pem . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file)) {
            try {
                // Always create New Agenda Entry (Program Kerja)
                $data = [
                    'id_pembiasaan' => $id_pem,
                    'tanggal' => date('Y-m-d'),
                    'nama_agenda' => $_POST['nama_kegiatan_baru'] ?? 'Program Kerja',
                    'lokasi' => '-',
                    'keterangan' => 'File Program Kerja',
                    'file_path' => $target_file,
                    'tipe' => 'program'
                ];
                PembiasaanModel::saveAgenda($pdo, $data);
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

    redirect("index.php?mod=pembiasaan&id=$id_pem&tab=program");
}

function pembiasaan_agenda_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_pem = $_POST['id_pembiasaan'];
    $file_path = null;

    // Handle File Upload for Agenda (Laporan Kegiatan)
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $upload_dir = 'uploads/pembiasaan/program/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $file_name = 'report_' . $id_pem . '_' . time() . '.' . $file_ext;
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
        PembiasaanModel::saveAgenda($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Agenda berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=pembiasaan&id=$id_pem&tab=program");
}

function pembiasaan_agenda_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_agenda = $_GET['id_agenda'];
    $id_pem = $_GET['id_pembiasaan'];
    try {
        PembiasaanModel::deleteAgenda($pdo, $id_agenda);
        $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=pembiasaan&id=$id_pem&tab=program");
}

// --- GALERI ---

function pembiasaan_galeri_upload($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_pem = $_POST['id_pembiasaan'];
    $judul = $_POST['judul'] ?? null;
    $foto_cam_data = $_POST['foto_cam_data'] ?? '';

    $upload_dir = 'uploads/pembiasaan/galeri/';
    if (!is_dir($upload_dir))
        mkdir($upload_dir, 0777, true);

    $target_file = null;

    if (!empty($foto_cam_data) && preg_match('/^data:image\/(\w+);base64,/', $foto_cam_data, $cam_match)) {
        $raw_base64 = substr($foto_cam_data, strpos($foto_cam_data, ',') + 1);
        $decoded = base64_decode($raw_base64);
        $ext = strtolower($cam_match[1]) === 'png' ? 'png' : 'jpg';
        if ($decoded) {
            $filename = 'galeri_' . $id_pem . '_' . time() . '.' . $ext;
            $target_file = $upload_dir . $filename;
            file_put_contents($target_file, $decoded);
        }
    } elseif (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $file_ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $filename = 'galeri_' . $id_pem . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_file)) {
            $target_file = null;
        }
    }

    if ($target_file) {
        try {
            $data = [
                'id_pembiasaan' => $id_pem,
                'judul' => $judul,
                'file_path' => $target_file
            ];
            PembiasaanModel::saveGaleri($pdo, $data);
            $_SESSION['pesan_sukses'] = "Foto dokumentasi kegiatan berhasil disimpan.";
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Error DB: " . $e->getMessage();
        }
    } else {
        $_SESSION['pesan_error'] = "Silakan ambil foto dengan kamera atau pilih file gambar.";
    }

    redirect("index.php?mod=pembiasaan&act=index&id=$id_pem&tab=galeri");
}

function pembiasaan_galeri_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_galeri = $_GET['id_galeri'];
    $id_pem = $_GET['id_pembiasaan'];

    try {
        PembiasaanModel::deleteGaleri($pdo, $id_galeri);
        $_SESSION['pesan_sukses'] = "Foto berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    redirect("index.php?mod=pembiasaan&act=index&id=$id_pem&tab=galeri");
    redirect("index.php?mod=pembiasaan&act=index&id=$id_pem&tab=galeri");
}

function pembiasaan_penilaian_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');
    $id_pem = $_POST['id_pembiasaan'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $nilai_data = $_POST['penilaian'] ?? [];

    try {
        PembiasaanModel::savePenilaian($pdo, $id_pem, $nilai_data, $bulan, $tahun);
        $_SESSION['pesan_sukses'] = "Data penilaian bulan $bulan/$tahun berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }

    redirect("index.php?mod=pembiasaan&id=$id_pem&tab=penilaian&bulan=$bulan&tahun=$tahun");
}
