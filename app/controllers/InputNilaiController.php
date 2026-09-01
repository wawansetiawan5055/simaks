<?php
require_once __DIR__ . '/../models/NilaiModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php';
require_once __DIR__ . '/../models/CpTpModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

function input_nilai_index($pdo)
{
    if (!check_access('input_nilai', 'index'))
        redirect('index.php');

    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array('TU', $_SESSION['roles'] ?? [])) {
        $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }
    if (!$id_guru || !$id_ta)
        die("Error: Informasi Guru atau TA tidak valid.");

    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array('TU', $_SESSION['roles'] ?? [])) {
        $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_ta]);
        $kelas_diajar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
    }

    $id_kelas_filter = $_GET['id_kelas'] ?? null;
    $id_guru_mapel_filter = $_GET['id_guru_mapel'] ?? null;
    $id_cp_filter = $_GET['id_cp'] ?? null;
    $id_tp_filter = $_GET['id_tp'] ?? null;

    $mapel_diajar = [];
    $cp_list = [];
    $tp_list = [];
    $siswa_nilai = [];
    $materi_lms_list = [];
    $nama_mapel_terpilih = '';

    if ($id_kelas_filter) {
        $mapel_diajar = NilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas_filter, $id_ta);

        if ($id_guru_mapel_filter) {
            // =============================================
            // PERBAIKAN UTAMA DI SINI (Sekitar baris 47)
            // =============================================
            // Query diubah untuk menentukan alias tabel (m.id_mapel)
            $stmtMapel = $pdo->prepare("
                SELECT m.id_mapel, m.nama_mapel 
                FROM mapel m 
                JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel 
                WHERE gm.id_guru_mapel = ? 
            ");
            // =============================================
            // AKHIR PERBAIKAN
            // =============================================
            $stmtMapel->execute([$id_guru_mapel_filter]);
            $mapelInfo = $stmtMapel->fetch(PDO::FETCH_ASSOC);
            $id_mapel_asli = $mapelInfo['id_mapel'] ?? 0;
            $nama_mapel_terpilih = $mapelInfo['nama_mapel'] ?? '';

            $kelas_info = $pdo->prepare("SELECT tingkat FROM kelas WHERE id_kelas = ?");
            $kelas_info->execute([$id_kelas_filter]);
            $tingkat = $kelas_info->fetchColumn();
            $fase_kelas = ($tingkat == 'X') ? 'E' : (($tingkat == 'XI' || $tingkat == 'XII') ? 'F' : '');

            if ($id_mapel_asli && $fase_kelas) {
                $cp_list = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel_asli, $fase_kelas);

                if ($id_cp_filter) {
                    $tp_list = CpTpModel::getAllTpByCp($pdo, $id_cp_filter);

                    if ($id_tp_filter) {
                        $siswa_nilai = NilaiModel::getSiswaWithNilai($pdo, $id_kelas_filter, $id_guru_mapel_filter, $id_ta, $id_tp_filter);
                    }
                }
            }

            // Ambil daftar modul LMS untuk opsi Tarik Nilai Formatif
            $stmt_mat = $pdo->prepare("SELECT id_materi, judul_materi FROM lms_materi WHERE id_mapel = ? ORDER BY id_materi DESC");
            $stmt_mat->execute([$id_mapel_asli]);
            $materi_lms_list = $stmt_mat->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    $data_for_view = compact(
        'kelas_diajar',
        'id_kelas_filter',
        'mapel_diajar',
        'id_guru_mapel_filter',
        'nama_mapel_terpilih',
        'cp_list',
        'id_cp_filter',
        'tp_list',
        'id_tp_filter',
        'siswa_nilai',
        'materi_lms_list'
    );
    extract($data_for_view);

    include __DIR__ . '/../views/input_nilai_index.php';
}

function input_nilai_save($pdo)
{
    if (!can_do($pdo, 'input_nilai', 'create') && !can_do($pdo, 'input_nilai', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan nilai.";
        // Construct redirect URL from POST data if possible, or fallback
        $id_kelas = $_POST['id_kelas'] ?? 0;
        if ($id_kelas) {
            redirect("index.php?mod=input_nilai&id_kelas={$id_kelas}");
        } else {
            redirect("index.php?mod=input_nilai");
        }
        return;
    }

    $id_kelas = $_POST['id_kelas'];
    $id_guru_mapel = $_POST['id_guru_mapel'];
    $id_tp = $_POST['id_tp'];
    $nilai_data = $_POST['nilai'];

    $data_to_save = [
        'id_kelas' => $id_kelas,
        'id_guru_mapel' => $id_guru_mapel,
        'id_tp' => $id_tp,
        'nilai' => $nilai_data
    ];

    try {
        NilaiModel::save($pdo, $data_to_save);
        if (function_exists('audit_log')) {
            audit_log('UPDATE', "Menyimpan/Memperbarui Nilai Siswa TP ID: $id_tp Kelas ID: $id_kelas", 'nilai');
        }
        $_SESSION['pesan_sukses'] = "Nilai berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan nilai: " . $e->getMessage();
    }
    redirect("index.php?mod=input_nilai&id_kelas={$id_kelas}&id_guru_mapel={$id_guru_mapel}&id_cp={$_POST['id_cp']}&id_tp={$id_tp}");
}
function input_nilai_template($pdo)
{
    $id_kelas = $_GET['id_kelas'] ?? 0;
    $id_guru_mapel = $_GET['id_guru_mapel'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    $id_tp = $_GET['id_tp'] ?? 0;

    if (!$id_kelas || !$id_guru_mapel || !$id_tp) {
        die("Data tidak lengkap untuk generate template.");
    }

    $siswa_nilai = NilaiModel::getSiswaWithNilai($pdo, $id_kelas, $id_guru_mapel, $id_ta, $id_tp);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Style headers
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0A500']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    $sheet->setCellValue('A1', 'NO');
    $sheet->setCellValue('B1', 'ID_PENEMPATAN');
    $sheet->setCellValue('C1', 'NAMA SISWA');
    $sheet->setCellValue('D1', 'NISN');
    $sheet->setCellValue('E1', 'NILAI (0-100)');
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

    // Hide ID_PENEMPATAN column (still needed for processing)
    $sheet->getColumnDimension('B')->setVisible(false);

    // Column widths
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('C')->setWidth(35);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(15);

    $row = 2;
    foreach ($siswa_nilai as $index => $s) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $s['id_penempatan']);
        $sheet->setCellValue('C' . $row, $s['nama']);
        $sheet->setCellValue('D' . $row, $s['nisn']);
        $sheet->setCellValue('E' . $row, $s['nilai'] ?? '');
        // Set nilai column as numeric
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('0.00');
        $row++;
    }

    $filename = "Template_Nilai_Formatif_" . date('YmdHis') . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    if (ob_get_length()) ob_clean();
    flush();

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function input_nilai_import($pdo)
{
    if (!can_do($pdo, 'input_nilai', 'create') && !can_do($pdo, 'input_nilai', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk mengimpor nilai.";
        redirect('index.php?mod=input_nilai');
        return;
    }

    $id_kelas = $_POST['id_kelas'];
    $id_guru_mapel = $_POST['id_guru_mapel'];
    $id_cp = $_POST['id_cp'];
    $id_tp = $_POST['id_tp'];
    $redirect_url = "index.php?mod=input_nilai&id_kelas={$id_kelas}&id_guru_mapel={$id_guru_mapel}&id_cp={$id_cp}&id_tp={$id_tp}";

    if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['pesan_error'] = 'File tidak valid atau tidak diunggah.';
        redirect($redirect_url);
        return;
    }

    try {
        $spreadsheet = IOFactory::load($_FILES['file_excel']['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $nilai_data = [];
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $id_penempatan = $row[1];
            $nilai = $row[4];

            if ($id_penempatan && $nilai !== null && $nilai !== '') {
                $nilai_data[$id_penempatan] = ['nilai' => $nilai];
            }
        }

        if (!empty($nilai_data)) {
            $data_to_save = [
                'id_kelas'     => $id_kelas,
                'id_guru_mapel' => $id_guru_mapel,
                'id_tp'        => $id_tp,
                'nilai'        => $nilai_data
            ];
            NilaiModel::save($pdo, $data_to_save);
            $_SESSION['pesan_sukses'] = "Berhasil mengimpor " . count($nilai_data) . " nilai dari Excel.";
        } else {
            $_SESSION['pesan_error'] = "Tidak ada data nilai yang ditemukan dalam file.";
        }

    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal mengimpor nilai: " . $e->getMessage();
    }

    redirect($redirect_url);
}
