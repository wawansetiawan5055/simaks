<?php
require_once __DIR__ . '/../models/PenilaianSumatifModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php';
require_once __DIR__ . '/../models/NilaiModel.php';
require_once __DIR__ . '/../models/CpTpModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

function penilaian_sumatif_index($pdo)
{
    if (!check_access('penilaian_sumatif', 'index'))
        redirect('index.php');
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }
    if (!$id_guru || !$id_ta)
        die("Error: Informasi Guru atau TA tidak valid.");

    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $kelas_diajar = KelasModel::all($pdo, $id_ta);
    } else {
        $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
    }

    $id_kelas_filter = $_GET['id_kelas'] ?? null;
    $id_guru_mapel_filter = $_GET['id_guru_mapel'] ?? null;
    $mapel_diajar = [];
    $agenda_list = [];

    if ($id_kelas_filter) {
        $mapel_diajar = NilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas_filter, $id_ta);
    }

    // Selalu ambil agenda list (bisa difilter atau tidak)
    $agenda_list = PenilaianSumatifModel::getAgendasByGuru($pdo, $id_guru, $id_ta, $id_kelas_filter, $id_guru_mapel_filter);

    $data_for_view = compact('kelas_diajar', 'id_kelas_filter', 'mapel_diajar', 'id_guru_mapel_filter', 'agenda_list');
    extract($data_for_view);
    include __DIR__ . '/../views/penilaian_sumatif_index.php';
}

function penilaian_sumatif_form_agenda($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'create')) { // Agenda = Create
        redirect('index.php?mod=penilaian_sumatif');
    }
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }
    if (!$id_guru || !$id_ta)
        die("Error: Informasi Guru atau TA tidak valid.");
    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $kelas_diajar = KelasModel::all($pdo, $id_ta);
    } else {
        $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
    }
    $id_sumatif = $_GET['id'] ?? 0;
    $agenda = null;
    $selected_tp_ids = [];
    if ($id_sumatif) {
        $agenda = PenilaianSumatifModel::findAgendaSumatif($pdo, $id_sumatif);
        $selected_tp_ids = PenilaianSumatifModel::getSelectedTpIdsForSumatif($pdo, $id_sumatif);

        // Cari CP mana saja yang dipilih berdasarkan TP yang terpilih
        $selected_cp_ids = [];
        if (!empty($selected_tp_ids)) {
            $placeholders = implode(',', array_fill(0, count($selected_tp_ids), '?'));
            $stmtCp = $pdo->prepare("SELECT DISTINCT id_cp FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders)");
            $stmtCp->execute($selected_tp_ids);
            $selected_cp_ids = $stmtCp->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    include __DIR__ . '/../views/penilaian_sumatif_form_agenda.php';
}

function penilaian_sumatif_save_agenda($pdo)
{
    $action_type = !empty($_POST['id_sumatif']) ? 'update' : 'create';
    if (!can_do($pdo, 'penilaian_sumatif', $action_type)) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan agenda.";
        redirect('index.php?mod=penilaian_sumatif');
        return;
    }

    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    // Validasi input
    if (empty($_POST['id_guru_mapel']) || empty($_POST['id_kelas']) || empty($_POST['nama_penilaian']) || empty($_POST['jenis_sumatif']) || !$id_ta) {
        $_SESSION['pesan_error'] = "Gagal membuat agenda: Data tidak lengkap atau sesi TA tidak valid.";
        redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
        return;
    }

    $data_agenda = [
        'id_guru_mapel' => $_POST['id_guru_mapel'],
        'id_kelas' => $_POST['id_kelas'],
        'id_ta' => $id_ta,
        'nama_penilaian' => $_POST['nama_penilaian'],
        'jenis_sumatif' => $_POST['jenis_sumatif'],
        'tanggal_penilaian' => !empty($_POST['tanggal_penilaian']) ? $_POST['tanggal_penilaian'] : null,
        'keterangan' => $_POST['keterangan'] ?? null
    ];

    $selected_tps = $_POST['selected_tps'] ?? [];

    try {
        if (!empty($_POST['id_sumatif'])) {
            $data_agenda['id_sumatif'] = $_POST['id_sumatif'];
            PenilaianSumatifModel::updateAgendaSumatif($pdo, $data_agenda, $selected_tps);
            $_SESSION['pesan_sukses'] = "Agenda Penilaian Sumatif berhasil diperbarui.";
        } else {
            $id_sumatif_baru = PenilaianSumatifModel::saveAgendaSumatif($pdo, $data_agenda, $selected_tps);
            $_SESSION['pesan_sukses'] = "Agenda Penilaian Sumatif berhasil dibuat.";
        }
        
        redirect("index.php?mod=penilaian_sumatif&id_kelas=" . $_POST['id_kelas'] . "&id_guru_mapel=" . $_POST['id_guru_mapel']);
    } catch (PDOException $e) {
        error_log("PDO Error saving agenda sumatif: " . $e->getMessage()); // Log error detail
        $_SESSION['pesan_error'] = "Terjadi kesalahan database. Silakan coba lagi atau hubungi admin."; // Pesan umum untuk user
        redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
    } catch (Exception $e) {
        error_log("General Error saving agenda sumatif: " . $e->getMessage());
        $_SESSION['pesan_error'] = "Terjadi kesalahan sistem. Silakan coba lagi.";
        redirect('index.php?mod=penilaian_sumatif&act=form_agenda');
    }
}


function penilaian_sumatif_form_nilai($pdo)
{
    // Input Nilai = Update Agenda atau Create Nilai? 
    // Kita anggap Create/Update izin yang sama pentingnya untuk guru.
    if (!can_do($pdo, 'penilaian_sumatif', 'create') && !can_do($pdo, 'penilaian_sumatif', 'update')) {
        redirect('index.php?mod=penilaian_sumatif');
    }

    $id_sumatif = $_GET['id_sumatif'] ?? 0;
    if (!$id_sumatif)
        redirect('index.php?mod=penilaian_sumatif');
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    $agenda = PenilaianSumatifModel::findAgendaSumatif($pdo, $id_sumatif);
    if (!$agenda)
        die("Agenda penilaian tidak ditemukan.");

    $stmtMapel = $pdo->prepare("SELECT id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
    $stmtMapel->execute([$agenda['id_guru_mapel']]);
    $id_mapel_asli = $stmtMapel->fetchColumn();
    $tp_list = CpTpModel::getTpByMapel($pdo, $id_mapel_asli);
    $selected_tps_ids = PenilaianSumatifModel::getSelectedTpIdsForSumatif($pdo, $id_sumatif);
    
    // Ambil detail TP untuk generator deskripsi di JS
    $selected_tps_details = [];
    $selected_tps_kodes = [];
    if (!empty($selected_tps_ids)) {
        $placeholders = implode(',', array_fill(0, count($selected_tps_ids), '?'));
        $tpStmt = $pdo->prepare("SELECT id_tp, deskripsi_tp, kode_tp FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders)");
        $tpStmt->execute($selected_tps_ids);
        $rows = $tpStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $selected_tps_details[$r['id_tp']] = $r['deskripsi_tp'];
            $selected_tps_kodes[$r['id_tp']] = $r['kode_tp'];
        }
    }

    // Ambil KKTP Mapel
    $kktpStmt = $pdo->prepare("SELECT m.kktp FROM mapel m JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel WHERE gm.id_guru_mapel = ?");
    $kktpStmt->execute([$agenda['id_guru_mapel']]);
    $kktp = (int) $kktpStmt->fetchColumn();
    if (!$kktp) $kktp = 75;

    $siswa_nilai = PenilaianSumatifModel::getSiswaWithNilaiSumatif($pdo, $agenda['id_kelas'], $id_sumatif, $id_ta);
    $capaian_tp_siswa = PenilaianSumatifModel::getCapaianSiswaForSumatif($pdo, $id_sumatif, $agenda['id_kelas'], $id_ta);
    $data_for_view = compact('agenda', 'tp_list', 'selected_tps_ids', 'siswa_nilai', 'selected_tps_details', 'selected_tps_kodes', 'kktp', 'capaian_tp_siswa');
    extract($data_for_view);
    include __DIR__ . '/../views/penilaian_sumatif_form_nilai.php';
}

function penilaian_sumatif_save_nilai($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'create') && !can_do($pdo, 'penilaian_sumatif', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan nilai.";
        $id_sumatif = $_POST['id_sumatif'] ?? 0;
        redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
        return;
    }

    $id_sumatif = $_POST['id_sumatif'] ?? 0;
    $nilai_data = $_POST['nilai'] ?? [];
    $selected_tps = $_POST['selected_tps'] ?? [];
    $id_guru_mapel = $_POST['id_guru_mapel'] ?? 0;
    if (!$id_sumatif || !$id_guru_mapel)
        die("Gagal menyimpan: Data tidak lengkap.");
    if (empty($selected_tps)) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: Tidak ada TP yang dipilih.";
        redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
        return;
    }

    $data_to_save = [
        'id_sumatif' => $id_sumatif,
        'selected_tps' => $selected_tps,
        'nilai' => $nilai_data,
        'id_guru_mapel' => $id_guru_mapel,
        'capaian_tp' => $_POST['capaian_tp'] ?? []
    ];

    try {
        PenilaianSumatifModel::saveNilaiSumatif($pdo, $data_to_save);
        $_SESSION['pesan_sukses'] = "Nilai sumatif berhasil disimpan.";
    } catch (Exception $e) {
        error_log("Error saving sumatif score: " . $e->getMessage());
        $_SESSION['pesan_error'] = "Terjadi kesalahan saat menyimpan nilai.";
    }
    redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
}

function penilaian_sumatif_template($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'read')) {
        die("Akses ditolak.");
    }

    $id_sumatif = $_GET['id_sumatif'] ?? 0;
    if (!$id_sumatif) die("Agenda tidak ditemukan.");

    $agenda = PenilaianSumatifModel::findAgendaSumatif($pdo, $id_sumatif);
    if (!$agenda) die("Data agenda tidak valid.");

    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    $siswa_list = PenilaianSumatifModel::getSiswaWithNilaiSumatif($pdo, $agenda['id_kelas'], $id_sumatif, $id_ta);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header Info
    $sheet->setCellValue('A1', 'TEMPLATE IMPORT NILAI SUMATIF');
    $sheet->setCellValue('A2', 'Agenda: ' . $agenda['nama_penilaian']);
    $sheet->setCellValue('A3', 'Kelas: ' . $agenda['nama_kelas']);
    $sheet->setCellValue('A4', 'Mapel: ' . $agenda['nama_mapel']);
    $sheet->setCellValue('A5', 'ID Agenda: ' . $id_sumatif);

    // Table Header
    $sheet->setCellValue('A7', 'ID PENEMPATAN');
    $sheet->setCellValue('B7', 'NAMA SISWA');
    $sheet->setCellValue('C7', 'NISN');
    $sheet->setCellValue('D7', 'NILAI (0-100)');

    $selected_tps_ids = PenilaianSumatifModel::getSelectedTpIdsForSumatif($pdo, $id_sumatif);
    $selected_tps_kodes = [];
    if (!empty($selected_tps_ids)) {
        $placeholders = implode(',', array_fill(0, count($selected_tps_ids), '?'));
        $tpStmt = $pdo->prepare("SELECT id_tp, kode_tp FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders)");
        $tpStmt->execute($selected_tps_ids);
        $rows = $tpStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $selected_tps_kodes[$r['id_tp']] = $r['kode_tp'];
        }
    }

    $colIndex = 'E';
    foreach ($selected_tps_ids as $id_tp) {
        $kode = $selected_tps_kodes[$id_tp] ?? "TP";
        $sheet->setCellValue($colIndex . '7', "CAPAIAN " . $kode . " (A/B/C)");
        $colIndex++;
    }

    $row = 8;
    $capaian_tp_siswa = PenilaianSumatifModel::getCapaianSiswaForSumatif($pdo, $id_sumatif, $agenda['id_kelas'], $id_ta);
    foreach ($siswa_list as $s) {
        $sheet->setCellValue('A' . $row, $s['id_penempatan']);
        $sheet->setCellValue('B' . $row, $s['nama']);
        $sheet->setCellValue('C' . $row, $s['nisn']);
        $sheet->setCellValue('D' . $row, $s['nilai'] ?? '');
        
        $currentCol = 'E';
        foreach ($selected_tps_ids as $id_tp) {
            $capaian = $capaian_tp_siswa[$s['id_penempatan']][$id_tp] ?? '';
            $sheet->setCellValue($currentCol . $row, $capaian);
            $currentCol++;
        }
        $row++;
    }

    // Styling
    $lastCol = $colIndex;
    for ($col = 'A'; $col !== $lastCol; $col++) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Decrement lastCol to get the actual last column letter
    $actualLastCol = 'A';
    for ($c = 'A'; $c !== $lastCol; $c++) {
        $actualLastCol = $c;
    }

    $sheet->getStyle('A7:' . $actualLastCol . '7')->getFont()->setBold(true);
    $sheet->getStyle('A5')->getFont()->getColor()->setARGB('FFFF0000'); // ID Agenda important

    $filename = "Template_Sumatif_" . str_replace(' ', '_', $agenda['nama_penilaian']) . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function penilaian_sumatif_import($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'create') && !can_do($pdo, 'penilaian_sumatif', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penilaian_sumatif');
        return;
    }

    if (!isset($_FILES['file_excel']['tmp_name'])) {
        $_SESSION['pesan_error'] = "File tidak ditemukan.";
        redirect('index.php?mod=penilaian_sumatif');
        return;
    }

    try {
        $spreadsheet = IOFactory::load($_FILES['file_excel']['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $id_sumatif = (int) $sheet->getCell('A5')->getValue();
        $id_sumatif = filter_var($id_sumatif, FILTER_SANITIZE_NUMBER_INT);

        if (!$id_sumatif) {
            $_SESSION['pesan_error'] = "Format file salah (ID Agenda tidak ditemukan di cell A5).";
            redirect('index.php?mod=penilaian_sumatif');
            return;
        }

        $agenda = PenilaianSumatifModel::findAgendaSumatif($pdo, $id_sumatif);
        if (!$agenda) {
            $_SESSION['pesan_error'] = "Agenda penilaian tidak valid.";
            redirect('index.php?mod=penilaian_sumatif');
            return;
        }

        // Ambil TP yang sudah dipilih sebelumnya untuk agenda ini
        $selected_tps = PenilaianSumatifModel::getSelectedTpIdsForSumatif($pdo, $id_sumatif);
        if (empty($selected_tps)) {
            $_SESSION['pesan_error'] = "Gagal import: Silakan pilih TP terlebih dahulu di menu Edit Nilai sebelum mengimport nilai.";
            redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
            return;
        }

        $data_nilai = [];
        $data_capaian = [];
        $highestRow = $sheet->getHighestRow();

        for ($row = 8; $row <= $highestRow; $row++) {
            $id_penempatan = $sheet->getCell('A' . $row)->getValue();
            $nilai = $sheet->getCell('D' . $row)->getValue();

            if ($id_penempatan) {
                $data_nilai[$id_penempatan] = ['nilai' => $nilai];

                $currentCol = 'E';
                foreach ($selected_tps as $id_tp) {
                    $capaian = $sheet->getCell($currentCol . $row)->getValue();
                    if ($capaian) {
                        $capaian = strtoupper(trim($capaian));
                        if (in_array($capaian, ['A', 'B', 'C'])) {
                            $data_capaian[$id_penempatan][$id_tp] = $capaian;
                        }
                    }
                    $currentCol++;
                }
            }
        }

        $payload = [
            'id_sumatif' => $id_sumatif,
            'id_guru_mapel' => $agenda['id_guru_mapel'],
            'selected_tps' => $selected_tps,
            'nilai' => $data_nilai,
            'capaian_tp' => $data_capaian
        ];

        PenilaianSumatifModel::saveNilaiSumatif($pdo, $payload);

        $_SESSION['pesan_sukses'] = "Berhasil mengimport nilai sumatif.";
        redirect("index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=" . $id_sumatif);
    } catch (Exception $e) {
        error_log("Error import sumatif: " . $e->getMessage());
        $_SESSION['pesan_error'] = "Terjadi kesalahan saat memproses file Excel.";
        redirect('index.php?mod=penilaian_sumatif');
    }
}

function penilaian_sumatif_delete_agenda($pdo)
{
    if (!can_do($pdo, 'penilaian_sumatif', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus agenda.";
        redirect('index.php?mod=penilaian_sumatif');
        return;
    }

    $id = $_GET['id'] ?? 0;
    if ($id && PenilaianSumatifModel::deleteAgendaSumatif($pdo, $id)) {
        $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus agenda.";
    }
    redirect('index.php?mod=penilaian_sumatif');
}
