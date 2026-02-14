<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/SiswaModel.php';
require_once __DIR__ . '/../models/GuruModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/MapelModel.php';
require_once __DIR__ . '/../models/ProfilSekolahModel.php';
require_once __DIR__ . '/../models/JadwalModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Manual include Dompdf if not autoloaded
if (!class_exists('Dompdf\Dompdf')) {
    require_once __DIR__ . '/../../dompdf_lib/dompdf/autoload.inc.php';
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

// ============ HELPER EXPORT (TIDAK BERUBAH) ==============
function laporan_export_excel_render($data, $filename)
{
    extract($data); // Extract the data array into individual variables
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:' . chr(64 + count($kolom)) . '1');
    $sheet->setCellValue('A1', $kop_nama);
    $sheet->mergeCells('A2:' . chr(64 + count($kolom)) . '2');
    $sheet->setCellValue('A2', $kop_alamat);
    $sheet->mergeCells('A3:' . chr(64 + count($kolom)) . '3');
    $sheet->setCellValue('A3', 'NPSN: ' . $kop_npsn);
    $sheet->setCellValue('A5', $judul);
    $sheet->fromArray($kolom, null, 'A7');
    $no = 8;
    foreach ($rows as $row) {
        $sheet->fromArray($row, null, 'A' . $no++);
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function laporan_export_pdf_render($data, $filename)
{
    ob_start();
    extract($data); // Extract the data array into individual variables
    include __DIR__ . '/../views/laporan_export_pdf.php';
    $html = ob_get_clean();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($filename . '.pdf');
    exit;
}

// ============ HELPER BARU UNTUK KOP SURAT ==============
function get_kop_laporan($pdo)
{
    $profil = ProfilSekolahModel::getProfil($pdo);
    // Menggunakan data profil jika ada, jika tidak, gunakan data statis
    return [
        'nama_yayasan' => $profil['nama_yayasan'] ?? 'YAYASAN TARBIYATUSSHIBYAN INDONESIA', // Use DB or fallback to old hardcoded
        'kop_nama' => $profil['nama_sekolah'] ?? 'SIMAKS',
        'kop_alamat' => $profil['alamat'] ?? 'Alamat Sekolah',
        'kop_npsn' => $profil['npsn'] ?? 'NPSN',
        'logo' => $profil['logo'] ?? '',
        // Data Tambahan untuk Tanda Tangan Otomatis
        'nama_kepala_sekolah' => $profil['nama_kepala_sekolah'] ?? '.......................',
        'nip_kepala_sekolah' => $profil['nip_kepala_sekolah'] ?? '',
        // [BARU] Otomatisasi Waka Kurikulum dari Penugasan
        'nama_waka_kurikulum' => PenugasanModel::getGuruByJabatan($pdo, 'Waka Kurikulum', $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0) ?? '.......................'
    ];
}
// =========================================================

/**
 * [HELPER] Mengambil Nama Guru lengkap dengan Gelar dari profil_guru
 */
function get_formatted_nama_guru($pdo, $id_guru)
{
    if (!$id_guru)
        return '.......................';
    $stmt = $pdo->prepare("SELECT g.nama, pg.gelar_depan, pg.gelar_belakang 
                          FROM guru g 
                          LEFT JOIN profil_guru pg ON g.id_guru = pg.id_guru 
                          WHERE g.id_guru = ?");
    $stmt->execute([$id_guru]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return format_nama_gelar($row['nama'], $row['gelar_depan'] ?? '', $row['gelar_belakang'] ?? '');
    }
    return '.......................';
}

// ============ LAPORAN SISWA (TIDAK BERUBAH) ==============
function laporan_siswa($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil)
        die("Error: Tahun ajaran tidak dipilih.");

    $kelas = $_GET['kelas'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
    $stmt->execute([$id_ta_tampil]);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query = "SELECT s.*, k.nama_kelas, k.tingkat 
              FROM (
                SELECT id_siswa, nama, jk, nisn, nipd, status_aktif, NULL as id_ta_mutasi FROM siswa
                UNION ALL
                SELECT id_siswa, nama, jk, nisn, nipd, status_aktif, NULL as id_ta_mutasi FROM siswa_alumni
                UNION ALL
                SELECT id_siswa, nama, jk, nisn, nipd, status_aktif, id_ta_mutasi FROM siswa_mutasi
              ) s
              LEFT JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
              LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
              WHERE p.id_ta = ?
              AND (s.status_aktif != 'Keluar' OR (s.status_aktif = 'Keluar' AND s.id_ta_mutasi >= ?))";
    $params = [$id_ta_tampil, $id_ta_tampil, $id_ta_tampil];

    if ($kelas) {
        $query .= " AND k.id_kelas = ? ";
        $params[] = $kelas;
    }
    $query .= " ORDER BY k.tingkat, k.nama_kelas, s.nama";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include __DIR__ . '/../views/laporan_siswa.php';
}
function laporan_siswa_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';

    $query = "SELECT s.*, k.nama_kelas, k.tingkat 
              FROM siswa s
              LEFT JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
              LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
              WHERE p.id_ta = ?";
    $params = [$id_ta_tampil, $id_ta_tampil];

    if ($kelas) {
        $query .= " AND k.id_kelas = ?";
        $params[] = $kelas;
    }
    $query .= " ORDER BY k.tingkat, k.nama_kelas, s.nama";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $judul = "Laporan Siswa (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama', 'NISN', 'NIPD', 'JK', 'Kelas', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama'], $d['nisn'], $d['nipd'], $d['jk'], $d['nama_kelas'], $d['status_aktif']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_siswa");
}
function laporan_siswa_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $query = "SELECT s.*, k.nama_kelas, k.tingkat 
              FROM siswa s
              LEFT JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
              LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
              WHERE p.id_ta = ?";
    $params = [$id_ta_tampil, $id_ta_tampil];
    if ($kelas) {
        $query .= " AND k.id_kelas = ?";
        $params[] = $kelas;
    }
    $query .= " ORDER BY k.tingkat, k.nama_kelas, s.nama";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $judul = "Laporan Siswa (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama', 'NISN', 'NIPD', 'JK', 'Kelas', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama'], $d['nisn'], $d['nipd'], $d['jk'], $d['nama_kelas'], $d['status_aktif']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_siswa");
}
function laporan_siswa_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $query = "SELECT s.*, k.nama_kelas, k.tingkat 
              FROM siswa s
              LEFT JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
              LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
              WHERE p.id_ta = ?";
    $params = [$id_ta_tampil, $id_ta_tampil];
    if ($kelas) {
        $query .= " AND k.id_kelas = ?";
        $params[] = $kelas;
    }
    $query .= " ORDER BY k.tingkat, k.nama_kelas, s.nama";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $judul = "Laporan Siswa (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Nama', 'NISN', 'NIPD', 'JK', 'Kelas', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama'], $d['nisn'], $d['nipd'], $d['jk'], $d['nama_kelas'], $d['status_aktif']];

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN GURU, KELAS, MAPEL (TIDAK BERUBAH) ==============
function laporan_guru($pdo)
{
    $guru_list = GuruModel::all($pdo);
    include __DIR__ . '/../views/laporan_guru.php';
}
function laporan_guru_export_excel($pdo)
{
    $data = GuruModel::all($pdo);
    $judul = "Laporan Guru";
    $kolom = ['No', 'Nama', 'NUPTK', 'NIK', 'JK', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama'], $d['nuptk'], $d['nik'], $d['jk'], $d['status']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_guru");
}
function laporan_guru_export_pdf($pdo)
{
    $data = GuruModel::all($pdo);
    $judul = "Laporan Guru";
    $kolom = ['No', 'Nama', 'NUPTK', 'NIK', 'JK', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama'], $d['nuptk'], $d['nik'], $d['jk'], $d['status']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_guru");
}
function laporan_guru_print($pdo)
{
    $data = GuruModel::all($pdo);
    $judul = "Laporan Guru";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Nama', 'NUPTK', 'NIK', 'JK', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama'], $d['nuptk'], $d['nik'], $d['jk'], $d['status']];

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}
// =======================================================================
// ============ [REVISI TOTAL] LAPORAN REKAPITULASI KELAS ================
// =======================================================================

// Helper untuk mengambil data kelas lengkap dengan Walas & Jumlah Siswa
function get_data_rekap_kelas($pdo)
{
    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    // SQL Canggih: Mengambil Kelas, Nama Walas, dan Menghitung Jumlah L/P sekaligus
    // [FIX] Menggunakan UNION untuk menghitung siswa yang sudah Lulus/Mutasi agar histori tetap akurat
    $sql = "SELECT 
                k.id_kelas, 
                k.nama_kelas, 
                k.tingkat,
                g.nama AS nama_walas,
                (SELECT COUNT(*) FROM penempatan_siswa ps 
                 JOIN (
                    SELECT id_siswa, jk, status_aktif, NULL as id_ta_mutasi FROM siswa
                    UNION ALL
                    SELECT id_siswa, jk, status_aktif, NULL as id_ta_mutasi FROM siswa_alumni
                    UNION ALL
                    SELECT id_siswa, jk, status_aktif, id_ta_mutasi FROM siswa_mutasi
                 ) s ON ps.id_siswa = s.id_siswa 
                 WHERE ps.id_kelas = k.id_kelas AND ps.id_ta = ? 
                 AND s.jk = 'Laki-laki'
                 AND (s.status_aktif != 'Keluar' OR (s.status_aktif = 'Keluar' AND s.id_ta_mutasi > ?))
                ) AS jml_l,
                (SELECT COUNT(*) FROM penempatan_siswa ps 
                 JOIN (
                    SELECT id_siswa, jk, status_aktif, NULL as id_ta_mutasi FROM siswa
                    UNION ALL
                    SELECT id_siswa, jk, status_aktif, NULL as id_ta_mutasi FROM siswa_alumni
                    UNION ALL
                    SELECT id_siswa, jk, status_aktif, id_ta_mutasi FROM siswa_mutasi
                 ) s ON ps.id_siswa = s.id_siswa 
                 WHERE ps.id_kelas = k.id_kelas AND ps.id_ta = ? 
                 AND s.jk = 'Perempuan'
                 AND (s.status_aktif != 'Keluar' OR (s.status_aktif = 'Keluar' AND s.id_ta_mutasi > ?))
                ) AS jml_p
            FROM kelas k
            LEFT JOIN penugasan_wali_kelas pwk ON k.id_kelas = pwk.id_kelas AND pwk.id_ta = ?
            LEFT JOIN guru g ON pwk.id_guru = g.id_guru
            ORDER BY k.tingkat, k.nama_kelas";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ta, $id_ta, $id_ta, $id_ta, $id_ta]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function laporan_kelas($pdo)
{
    // Menggunakan helper di atas
    $kelas_list = get_data_rekap_kelas($pdo);
    include __DIR__ . '/../views/laporan_kelas.php';
}

function laporan_kelas_export_excel($pdo)
{
    $data = get_data_rekap_kelas($pdo);
    $judul = "Laporan Rekapitulasi Kelas (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";

    // Kolom Baru Sesuai Permintaan
    $kolom = ['No', 'Nama Kelas', 'Wali Kelas', 'L', 'P', 'Total'];
    $rows = [];
    $no = 1;

    foreach ($data as $d) {
        $total = $d['jml_l'] + $d['jml_p'];
        $walas = $d['nama_walas'] ?? '- Belum Ada -';
        $nama_kelas_full = $d['nama_kelas'];

        $rows[] = [$no++, $nama_kelas_full, $walas, $d['jml_l'], $d['jml_p'], $total];
    }

    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_rekap_kelas");
}

function laporan_kelas_export_pdf($pdo)
{
    $data = get_data_rekap_kelas($pdo);
    $judul = "Laporan Rekapitulasi Kelas (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama Kelas', 'Wali Kelas', 'L', 'P', 'Total'];
    $rows = [];
    $no = 1;
    foreach ($data as $d) {
        $total = $d['jml_l'] + $d['jml_p'];
        $walas = $d['nama_walas'] ?? '-';
        $nama_kelas_full = $d['nama_kelas'];
        $rows[] = [$no++, $nama_kelas_full, $walas, $d['jml_l'], $d['jml_p'], $total];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_rekap_kelas");
}

function laporan_kelas_print($pdo)
{
    $data = get_data_rekap_kelas($pdo);
    $judul = "Laporan Rekapitulasi Kelas (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Nama Kelas', 'Wali Kelas', 'L', 'P', 'Total'];
    $rows = [];
    $no = 1;
    foreach ($data as $d) {
        $total = $d['jml_l'] + $d['jml_p'];
        $walas = $d['nama_walas'] ?? '-';
        $nama_kelas_full = $d['nama_kelas'];
        $rows[] = [$no++, $nama_kelas_full, $walas, $d['jml_l'], $d['jml_p'], $total];
    }

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}
function laporan_mapel($pdo)
{
    $mapel_list = MapelModel::all($pdo);
    include __DIR__ . '/../views/laporan_mapel.php';
}
function laporan_mapel_export_excel($pdo)
{
    $data = MapelModel::all($pdo);
    $judul = "Laporan Mata Pelajaran";
    $kolom = ['No', 'Nama Mapel', 'Kategori', 'KKTP']; // Revisi KKM -> KKTP
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama_mapel'], $d['kategori_mapel'], $d['kktp']]; // Revisi kkm -> kktp
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mapel");
}
function laporan_mapel_export_pdf($pdo)
{
    $data = MapelModel::all($pdo);
    $judul = "Laporan Mata Pelajaran";
    $kolom = ['No', 'Nama Mapel', 'Kategori', 'KKTP']; // Revisi KKM -> KKTP
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama_mapel'], $d['kategori_mapel'], $d['kktp']]; // Revisi kkm -> kktp
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mapel");
}
function laporan_mapel_print($pdo)
{
    $data = MapelModel::all($pdo);
    $judul = "Laporan Mata Pelajaran";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Nama Mapel', 'Kategori', 'KKTP']; // Revisi KKM -> KKTP
    $rows = [];
    $no = 1;
    foreach ($data as $d)
        $rows[] = [$no++, $d['nama_mapel'], $d['kategori_mapel'], $d['kktp']]; // Revisi kkm -> kktp

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}


// ====================================================================
// ============ [REVISI DIMULAI] LAPORAN ABSENSI SISWA MAPEL ==========
// ====================================================================

function laporan_absensi_siswa_mapel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    // Filter Inputs
    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian'; // harian, bulanan, semester
    $id_kelas_filter = $_GET['kelas'] ?? '';
    $id_guru_filter = $_GET['guru'] ?? '';
    $id_mapel_filter = $_GET['mapel'] ?? ''; // New Filter

    // Time Inputs
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d'); // Singgle Date for Harian
    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    // Semester Inputs
    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    // -- Role Based Lists --
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    if ($id_guru_login > 0) {
        // Jika Guru: Hanya kelas dan mapel yang diajarkan di TA ini
        // FIX: Join ke jadwal_mengajar untuk dapat id_kelas, karena guru_mapel tidak punya id_kelas
        $sql_k = "SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat 
                  FROM jadwal_mengajar jm
                  JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                  JOIN kelas k ON jm.id_kelas = k.id_kelas
                  WHERE gm.id_guru = ? AND gm.id_ta = ?
                  ORDER BY k.tingkat, k.nama_kelas";
        $stmt_k = $pdo->prepare($sql_k);
        $stmt_k->execute([$id_guru_login, $id_ta_tampil]);
        $kelas_list = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

        $sql_m = "SELECT DISTINCT m.id_mapel, m.nama_mapel 
                  FROM guru_mapel gm
                  JOIN mapel m ON gm.id_mapel = m.id_mapel
                  WHERE gm.id_guru = ? AND gm.id_ta = ?
                  ORDER BY m.nama_mapel";
        $stmt_m = $pdo->prepare($sql_m);
        $stmt_m->execute([$id_guru_login, $id_ta_tampil]);
        $mapel_list = $stmt_m->fetchAll(PDO::FETCH_ASSOC);

        $guru_list = []; // Guru tidak perlu pilih guru lain

        // Auto set filter guru ke diri sendiri
        $id_guru_filter = $id_guru_login;

    } else {
        // Jika Admin: Semua Data
        $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_ta_tampil]);
        $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $guru_list = $pdo->query("SELECT id_guru, nama FROM guru WHERE status='Aktif' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
        $mapel_list = $pdo->query("SELECT * FROM mapel ORDER BY nama_mapel")->fetchAll(PDO::FETCH_ASSOC);
    }

    // -- Vars for View --
    $data = []; // Unified data container
    $header_info = [];

    // Helper to resolve dates based on mode
    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        // Construct Range from Month-Month inputs
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    // Check minimal filters
    $show_report = ($id_kelas_filter && $id_ta_tampil);
    // If Bulanan, check if month/year set? They have defaults.

    if ($show_report) {

        // 1. Base Query Components
        $where_clauses = ["a.id_kelas = ?", "a.tanggal BETWEEN ? AND ?", "a.id_ta = ?"];
        $params = [$id_kelas_filter, $start_date, $end_date, $id_ta_tampil];

        // Filter Guru (Role Based)
        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_login == 0) { // Admin
            if ($id_guru_filter) {
                $where_clauses[] = "g.id_guru = ?";
                $params[] = $id_guru_filter;
            }
        } else { // Guru
            $where_clauses[] = "g.id_guru = ?";
            $params[] = $id_guru_login;
        }

        // Filter Mapel
        if ($id_mapel_filter) {
            $where_clauses[] = "gm.id_mapel = ?";
            $params[] = $id_mapel_filter;
        }

        $where_sql = implode(" AND ", $where_clauses);

        // 2. Fetch Data Based on Mode
        if ($jenis_laporan == 'harian') {
            // -- HARIAN: Raw List ordered by Date, then Student --
            $sql = "SELECT s.nama, s.nisn, s.nipd, a.tanggal, a.status, a.keterangan, m.nama_mapel, g.nama AS nama_guru
                    FROM absensi_siswa_mapel a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    JOIN mapel m ON gm.id_mapel = m.id_mapel
                    WHERE $where_sql
                    ORDER BY a.tanggal, s.nama";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            // -- BULANAN: Matrix (Student x Date) --
            // Get Dates in Range
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                (new DateTime($end_date))->modify('+1 day')
            );
            $dates = [];
            foreach ($period as $dt)
                $dates[] = $dt->format('Y-m-d');

            // Fetch All Attendance Records
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, a.tanggal, a.status
                    FROM absensi_siswa_mapel a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    WHERE $where_sql
                    ORDER BY s.nama, a.tanggal";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build Matrix
            $matrix = [];
            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                $matrix[$r['id_siswa']]['attendance'][$r['tanggal']] = $r['status'];

                // Count Summary
                $code = strtoupper(substr($r['status'], 0, 1)); // H, S, I, A
                if (isset($matrix[$r['id_siswa']]['summary'][$code])) {
                    $matrix[$r['id_siswa']]['summary'][$code]++;
                }
            }
            // Sort by Nama
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            $data = ['dates' => $dates, 'students' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            // -- SEMESTER: Matrix (Student x Month) --
            // Fetch Month-Aggregated Data
            // We need to group by Student AND Month
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_siswa_mapel a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    WHERE $where_sql
                    GROUP BY s.id_siswa, month_key, a.status
                    ORDER BY s.nama, month_key";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Extract Unique Months for Header
            $months = [];
            $matrix = [];

            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;

                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }

                // Init month if not exists
                if (!isset($matrix[$r['id_siswa']]['months'][$m])) {
                    $matrix[$r['id_siswa']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                }

                $code = strtoupper(substr($r['status'], 0, 1)); // H,S,I,A
                if (isset($matrix[$r['id_siswa']]['total'][$code])) {
                    $matrix[$r['id_siswa']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_siswa']]['total'][$code] += $r['count'];
                }
            }
            sort($months);
            // Sort by Nama
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            $data = ['months' => $months, 'students' => $matrix];
        }

        // Header Info Fetching
        if ($id_kelas_filter) {
            $stmt_k = $pdo->prepare("SELECT nama_kelas FROM kelas WHERE id_kelas = ?");
            $stmt_k->execute([$id_kelas_filter]);
            $header_info['kelas'] = $stmt_k->fetchColumn();
        }
        if ($id_guru_filter) {
            $stmt_g = $pdo->prepare("SELECT nama FROM guru WHERE id_guru = ?");
            $stmt_g->execute([$id_guru_filter]);
            $header_info['guru'] = $stmt_g->fetchColumn();
        } elseif ($id_guru_login) {
            $header_info['guru'] = $_SESSION['nama_guru_terkait'] ?? $_SESSION['nama_pengguna'];
        }
        if ($id_mapel_filter) {
            $stmt_m = $pdo->prepare("SELECT nama_mapel FROM mapel WHERE id_mapel = ?");
            $stmt_m->execute([$id_mapel_filter]);
            $header_info['mapel'] = $stmt_m->fetchColumn();
        }
        $header_info['ta'] = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '';
    }

    // Pass everything to view
    extract(compact(
        'kelas_list',
        'guru_list',
        'mapel_list',
        'data',
        'header_info',
        'jenis_laporan',
        'id_kelas_filter',
        'id_guru_filter',
        'id_mapel_filter',
        'id_mapel_filter',
        'tanggal',
        'bulan_filter',
        'tahun_filter',
        'bulan1',
        'bulan2',
        'tahun_semester'
    ));
    include __DIR__ . '/../views/laporan_absensi_siswa_mapel.php';
}

function laporan_absensi_siswa_mapel_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    // Filters
    $id_kelas_filter = $_GET['kelas'] ?? '';
    $id_guru_filter = $_GET['guru'] ?? '';
    $id_mapel_filter = $_GET['mapel'] ?? '';
    $id_mapel_filter = $_GET['mapel'] ?? '';
    $tanggal = $_GET['tanggal'] ?? '';

    // Semester Inputs
    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    // Note: Excel export currently supports List View (Harian compatible). 
    // Matrix export is complex and kept as future work or rely on Print -> Save as PDF.

    $list = [];
    $params = [];

    // Determine Dates
    $start_date = $tanggal;
    $end_date = $tanggal;
    $is_semester = isset($_GET['jenis_laporan']) && $_GET['jenis_laporan'] == 'semester';

    if ($is_semester) {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    if ($id_kelas_filter && $start_date && $end_date && $id_ta_tampil) {
        $where_clauses = ["a.id_kelas = ?", "a.tanggal BETWEEN ? AND ?", "a.id_ta = ?"];
        $params = [$id_kelas_filter, $start_date, $end_date, $id_ta_tampil];

        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_login == 0) {
            if ($id_guru_filter) {
                $where_clauses[] = "g.id_guru = ?";
                $params[] = $id_guru_filter;
            }
        } else {
            $where_clauses[] = "g.id_guru = ?";
            $params[] = $id_guru_login;
        }

        // Add Mapel Filter
        if ($id_mapel_filter) {
            $where_clauses[] = "gm.id_mapel = ?";
            $params[] = $id_mapel_filter;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "SELECT s.nama, s.nisn, a.tanggal, a.status, a.keterangan, m.nama_mapel, g.nama AS nama_guru
                FROM absensi_siswa_mapel a
                JOIN siswa s ON a.id_siswa = s.id_siswa
                JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE $where_sql
                ORDER BY a.tanggal, s.nama";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Absensi Siswa Mapel (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama', 'NISN', 'Tanggal', 'Mapel', 'Guru', 'Status', 'Keterangan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [$no++, $d['nama'], $d['nisn'], $d['tanggal'], $d['nama_mapel'], $d['nama_guru'], $d['status'], $d['keterangan']];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_absensi_siswa_mapel");
}

function laporan_absensi_siswa_mapel_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $id_kelas_filter = $_GET['kelas'] ?? '';
    $id_guru_filter = $_GET['guru'] ?? '';
    $id_mapel_filter = $_GET['mapel'] ?? '';
    $id_mapel_filter = $_GET['mapel'] ?? '';
    $tanggal = $_GET['tanggal'] ?? '';

    // Semester Inputs
    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    $list = [];
    $params = [];

    // Determine Dates
    $start_date = $tanggal;
    $end_date = $tanggal;
    $is_semester = isset($_GET['jenis_laporan']) && $_GET['jenis_laporan'] == 'semester';

    if ($is_semester) {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    if ($id_kelas_filter && $start_date && $end_date && $id_ta_tampil) {
        $where_clauses = ["a.id_kelas = ?", "a.tanggal BETWEEN ? AND ?", "a.id_ta = ?"];
        $params = [$id_kelas_filter, $start_date, $end_date, $id_ta_tampil];

        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_login == 0) {
            if ($id_guru_filter) {
                $where_clauses[] = "g.id_guru = ?";
                $params[] = $id_guru_filter;
            }
        } else {
            $where_clauses[] = "g.id_guru = ?";
            $params[] = $id_guru_login;
        }

        if ($id_mapel_filter) {
            $where_clauses[] = "gm.id_mapel = ?";
            $params[] = $id_mapel_filter;
        }

        $where_sql = implode(' AND ', $where_clauses);

        $sql = "SELECT s.nama, s.nisn, a.tanggal, a.status, a.keterangan, m.nama_mapel, g.nama AS nama_guru
                FROM absensi_siswa_mapel a
                JOIN siswa s ON a.id_siswa = s.id_siswa
                JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE $where_sql
                ORDER BY a.tanggal, s.nama";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Absensi Siswa Mapel";
    $kolom = ['No', 'Nama', 'NISN', 'Tanggal', 'Mapel', 'Guru', 'Status', 'Keterangan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [$no++, $d['nama'], $d['nisn'], $d['tanggal'], $d['nama_mapel'], $d['nama_guru'], $d['status'], $d['keterangan']];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_absensi_siswa_mapel");
}

function laporan_absensi_siswa_mapel_print($pdo)
{
    // DUPLICATE LOGIC FROM MAIN CONTROLLER FOR PRINT VIEW
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian';
    $id_kelas_filter = $_GET['kelas'] ?? '';
    $id_guru_filter = $_GET['guru'] ?? '';
    $id_mapel_filter = $_GET['mapel'] ?? '';

    $id_mapel_filter = $_GET['mapel'] ?? '';

    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    $data = [];
    $header_info = [];

    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    if ($id_kelas_filter && $id_ta_tampil) {
        $where_clauses = ["a.id_kelas = ?", "a.tanggal BETWEEN ? AND ?", "a.id_ta = ?"];
        $params = [$id_kelas_filter, $start_date, $end_date, $id_ta_tampil];

        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_login == 0) {
            if ($id_guru_filter) {
                $where_clauses[] = "g.id_guru = ?";
                $params[] = $id_guru_filter;
            }
        } else {
            $where_clauses[] = "g.id_guru = ?";
            $params[] = $id_guru_login;
        }

        if ($id_mapel_filter) {
            $where_clauses[] = "gm.id_mapel = ?";
            $params[] = $id_mapel_filter;
        }

        $where_sql = implode(" AND ", $where_clauses);

        if ($jenis_laporan == 'harian') {
            $sql = "SELECT s.nama, s.nisn, s.nipd, a.tanggal, a.status, a.keterangan, m.nama_mapel, g.nama AS nama_guru
                    FROM absensi_siswa_mapel a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    JOIN mapel m ON gm.id_mapel = m.id_mapel
                    WHERE $where_sql
                    ORDER BY a.tanggal, s.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            // [LOGIC BARU] Filter Tanggal Berdasarkan Jadwal KBM (Hari Mengajar)
            $scheduled_days = [];

            // 1. Cari ID Guru Mapel dulu
            $target_id_guru = ($id_guru_login != 0) ? $id_guru_login : $id_guru_filter;

            if ($target_id_guru && $id_mapel_filter && $id_kelas_filter && $id_ta_tampil) {
                // Ambil ID Guru Mapel
                $stmt_gm_id = $pdo->prepare("SELECT id_guru_mapel FROM guru_mapel WHERE id_guru = ? AND id_mapel = ? AND id_ta = ?");
                $stmt_gm_id->execute([$target_id_guru, $id_mapel_filter, $id_ta_tampil]);
                $row_gm = $stmt_gm_id->fetch(PDO::FETCH_ASSOC);

                if ($row_gm) {
                    $id_gm_found = $row_gm['id_guru_mapel'];
                    // 2. Ambil Hari Mengajar dari Jadwal
                    $stmt_jadwal = $pdo->prepare("SELECT DISTINCT hari_kbm FROM jadwal_mengajar WHERE id_guru_mapel = ? AND id_kelas = ?");
                    $stmt_jadwal->execute([$id_gm_found, $id_kelas_filter]);
                    $scheduled_days = $stmt_jadwal->fetchAll(PDO::FETCH_COLUMN); // e.g. ['Senin', 'Kamis']
                }
            }

            $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
            $dates = [];

            // Helper Translate Day
            $day_map = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];

            foreach ($period as $dt) {
                // Jika ada jadwal, filter. Jika kosong (belum set jadwal), tampilkan semua (fallback).
                if (!empty($scheduled_days)) {
                    $day_name_en = $dt->format('l');
                    $day_name_id = $day_map[$day_name_en] ?? '';
                    if (in_array($day_name_id, $scheduled_days)) {
                        $dates[] = $dt->format('Y-m-d');
                    }
                } else {
                    $dates[] = $dt->format('Y-m-d');
                }
            }

            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, a.tanggal, a.status
                    FROM absensi_siswa_mapel a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    WHERE $where_sql
                    ORDER BY s.nama, a.tanggal";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $matrix = [];
            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                // Hanya masukkan data jika tanggalnya valid sesuai filter jadwal (atau jika jadwal kosong)
                if (in_array($r['tanggal'], $dates)) {
                    $matrix[$r['id_siswa']]['attendance'][$r['tanggal']] = $r['status'];
                    $code = strtoupper(substr($r['status'], 0, 1));
                    if (isset($matrix[$r['id_siswa']]['summary'][$code])) {
                        $matrix[$r['id_siswa']]['summary'][$code]++;
                    }
                }
            }
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['dates' => $dates, 'students' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_siswa_mapel a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN guru_mapel gm ON a.id_guru_mapel = gm.id_guru_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    WHERE $where_sql
                    GROUP BY s.id_siswa, month_key, a.status
                    ORDER BY s.nama, month_key";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $months = [];
            $matrix = [];
            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                if (!isset($matrix[$r['id_siswa']]['months'][$m])) {
                    $matrix[$r['id_siswa']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                }
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['total'][$code])) {
                    $matrix[$r['id_siswa']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_siswa']]['total'][$code] += $r['count'];
                }
            }
            sort($months);
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['months' => $months, 'students' => $matrix];
        }

        // Header Info
        if ($id_kelas_filter)
            $header_info['kelas'] = $pdo->query("SELECT nama_kelas FROM kelas WHERE id_kelas = $id_kelas_filter")->fetchColumn();
        if ($id_guru_filter)
            $header_info['guru'] = get_formatted_nama_guru($pdo, $id_guru_filter);
        elseif ($id_guru_login)
            $header_info['guru'] = get_formatted_nama_guru($pdo, $id_guru_login);
        if ($id_mapel_filter)
            $header_info['mapel'] = $pdo->query("SELECT nama_mapel FROM mapel WHERE id_mapel = $id_mapel_filter")->fetchColumn();
        $header_info['ta'] = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '';

        // Dynamic Title with Month Name
        $nama_bulan_str = "";
        if ($jenis_laporan == 'bulanan') {
            $months = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ];
            $nama_bulan_str = isset($months[$bulan_filter]) ? strtoupper($months[$bulan_filter]) . " " . $tahun_filter : "";
        }
        $header_info['judul_bulan'] = $nama_bulan_str;
    }

    $kop = get_kop_laporan($pdo);

    // Pass everything to view
    extract(compact('data', 'header_info', 'jenis_laporan', 'kop'));
    include __DIR__ . '/../views/laporan_print_absensi_siswa_mapel.php';
}
// ==================================================================
// ============ AKHIR REVISI LAPORAN ABSENSI SISWA MAPEL ============
// ==================================================================


// ============ LAPORAN ABSENSI SISWA PIKET (TIDAK BERUBAH) ==============
function laporan_absensi_siswa_piket($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    // Filter Inputs
    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian';
    $kelas_id = $_GET['kelas'] ?? '';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    // Prepare Date Range
    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
    $stmt->execute([$id_ta_tampil]);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = [];

    if ($kelas_id && $id_ta_tampil) {

        $sql_common_where = "a.id_kelas = ? AND a.tanggal BETWEEN ? AND ? AND a.id_ta = ?";
        $params = [$kelas_id, $start_date, $end_date, $id_ta_tampil];

        if ($jenis_laporan == 'harian') {
            $sql = "SELECT s.nama, s.nisn, s.nipd, a.tanggal, a.status, a.keterangan
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa=s.id_siswa
                    WHERE $sql_common_where
                    ORDER BY a.tanggal, s.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            // Generate All Dates in Range
            $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
            $dates = [];
            foreach ($period as $dt) {
                $dates[] = $dt->format('Y-m-d');
            }

            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, a.tanggal, a.status
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE $sql_common_where
                    ORDER BY s.nama, a.tanggal";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $matrix = [];

            // Pre-fill student list to ensure all students in class (even with no absence data) are shown?
            // Ideally yes, but for now we follow mapel pattern which uses query result. 
            // Better: Get all students in class first, then fill data. 
            // Using existing pattern for now (FROM absensi table + join siswa). 
            // Note: This only shows students who HAVE data entries. 
            // Since AbsensiPiketModel::save inserts for ALL students in class (based on recent fix), this should be fine.

            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                $matrix[$r['id_siswa']]['attendance'][$r['tanggal']] = $r['status'];

                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['summary'][$code])) {
                    $matrix[$r['id_siswa']]['summary'][$code]++;
                }
            }

            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            $data = ['dates' => $dates, 'students' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE $sql_common_where
                    GROUP BY s.id_siswa, month_key, a.status
                    ORDER BY s.nama, month_key";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $months = [];
            $matrix = [];

            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;

                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                if (!isset($matrix[$r['id_siswa']]['months'][$m])) {
                    $matrix[$r['id_siswa']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                }

                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['total'][$code])) {
                    $matrix[$r['id_siswa']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_siswa']]['total'][$code] += $r['count'];
                }
            }

            sort($months);
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            $data = ['months' => $months, 'students' => $matrix];
        }
    }

    // Header Info Setup (for standardization with views)
    $header_info = [];
    if ($kelas_id)
        $header_info['kelas'] = $pdo->query("SELECT nama_kelas FROM kelas WHERE id_kelas = $kelas_id")->fetchColumn();
    $header_info['ta'] = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '';

    // Nama Bulan Title
    if ($jenis_laporan == 'bulanan') {
        $months_map = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $header_info['judul_bulan'] = $months_map[$bulan_filter] . " " . $tahun_filter;
    } elseif ($jenis_laporan == 'semester') {
        $header_info['judul_bulan'] = "Semester"; // Or range
    }

    include __DIR__ . '/../views/laporan_absensi_siswa_piket.php';
}
function laporan_absensi_siswa_piket_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, s.nisn, a.tanggal, a.status, a.keterangan
                FROM absensi_siswa_piket a
                JOIN siswa s ON a.id_siswa=s.id_siswa
                WHERE a.id_kelas=? AND a.tanggal BETWEEN ? AND ? AND a.id_ta = ?
                ORDER BY a.tanggal, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Absensi Siswa Piket (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama', 'NISN', 'Tanggal', 'Status', 'Keterangan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['nama'], $d['nisn'], $d['tanggal'], $d['status'], $d['keterangan']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_absensi_siswa_piket");
}
function laporan_absensi_siswa_piket_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian';
    $kelas_id = $_GET['kelas'] ?? '';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    $data = [];
    $list = []; // For Harian

    if ($kelas_id && $id_ta_tampil) {
        $sql_common_where = "a.id_kelas = ? AND a.tanggal BETWEEN ? AND ? AND a.id_ta = ?";
        $params = [$kelas_id, $start_date, $end_date, $id_ta_tampil];

        if ($jenis_laporan == 'harian') {
            $sql = "SELECT s.nama, s.nisn, s.nipd, a.tanggal, a.status, a.keterangan
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa=s.id_siswa
                    WHERE $sql_common_where
                    ORDER BY a.tanggal, s.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
            $dates = [];
            foreach ($period as $dt)
                $dates[] = $dt->format('Y-m-d');

            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, a.tanggal, a.status
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE $sql_common_where
                    ORDER BY s.nama, a.tanggal";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $matrix = [];
            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                $matrix[$r['id_siswa']]['attendance'][$r['tanggal']] = $r['status'];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['summary'][$code]))
                    $matrix[$r['id_siswa']]['summary'][$code]++;
            }
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['dates' => $dates, 'students' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE $sql_common_where
                    GROUP BY s.id_siswa, month_key, a.status
                    ORDER BY s.nama, month_key";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $months = [];
            $matrix = [];
            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                if (!isset($matrix[$r['id_siswa']]['months'][$m]))
                    $matrix[$r['id_siswa']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['total'][$code])) {
                    $matrix[$r['id_siswa']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_siswa']]['total'][$code] += $r['count'];
                }
            }
            sort($months);
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['months' => $months, 'students' => $matrix];
        }
    }

    $header_info = [];
    if ($kelas_id)
        $header_info['kelas'] = $pdo->query("SELECT nama_kelas FROM kelas WHERE id_kelas = $kelas_id")->fetchColumn();
    $header_info['ta'] = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '';

    if ($jenis_laporan == 'bulanan') {
        $months_map = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $header_info['judul_bulan'] = $months_map[$bulan_filter] . " " . $tahun_filter;
    } elseif ($jenis_laporan == 'semester') {
        $header_info['judul_bulan'] = "Semester";
    }

    $judul = "Laporan Absensi Siswa Piket";
    $kop = get_kop_laporan($pdo);

    // Pass everything to the renderer
    $view_data = [
        'judul' => $judul,
        'kop_nama' => $kop['kop_nama'],
        'kop_alamat' => $kop['kop_alamat'],
        'kop_npsn' => $kop['kop_npsn'],
        'logo' => $kop['logo'] ?? '',
        'data' => $data,
        'list' => $list,
        'jenis_laporan' => $jenis_laporan,
        'header_info' => $header_info
    ];

    laporan_export_pdf_render($view_data, "laporan_absensi_siswa_piket");
}
function laporan_absensi_siswa_piket_print($pdo)
{
    // DUPLICATE LOGIC FROM MAIN CONTROLLER FOR PRINT VIEW
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian';
    $kelas_id = $_GET['kelas'] ?? '';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    $data = [];
    $header_info = [];

    if ($kelas_id && $id_ta_tampil) {
        $sql_common_where = "a.id_kelas = ? AND a.tanggal BETWEEN ? AND ? AND a.id_ta = ?";
        $params = [$kelas_id, $start_date, $end_date, $id_ta_tampil];

        if ($jenis_laporan == 'harian') {
            $sql = "SELECT s.nama, s.nisn, s.nipd, a.tanggal, a.status, a.keterangan
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa=s.id_siswa
                    WHERE $sql_common_where
                    ORDER BY a.tanggal, s.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
            $dates = [];
            foreach ($period as $dt)
                $dates[] = $dt->format('Y-m-d');

            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, a.tanggal, a.status
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE $sql_common_where
                    ORDER BY s.nama, a.tanggal";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $matrix = [];
            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                $matrix[$r['id_siswa']]['attendance'][$r['tanggal']] = $r['status'];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['summary'][$code]))
                    $matrix[$r['id_siswa']]['summary'][$code]++;
            }
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['dates' => $dates, 'students' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_siswa_piket a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE $sql_common_where
                    GROUP BY s.id_siswa, month_key, a.status
                    ORDER BY s.nama, month_key";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $months = [];
            $matrix = [];
            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;
                if (!isset($matrix[$r['id_siswa']])) {
                    $matrix[$r['id_siswa']] = [
                        'nama' => $r['nama'],
                        'nisn' => $r['nisn'],
                        'nipd' => $r['nipd'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                if (!isset($matrix[$r['id_siswa']]['months'][$m]))
                    $matrix[$r['id_siswa']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_siswa']]['total'][$code])) {
                    $matrix[$r['id_siswa']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_siswa']]['total'][$code] += $r['count'];
                }
            }
            sort($months);
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['months' => $months, 'students' => $matrix];
        }
    }

    if ($kelas_id)
        $header_info['kelas'] = $pdo->query("SELECT nama_kelas FROM kelas WHERE id_kelas = $kelas_id")->fetchColumn();
    $header_info['ta'] = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '';

    // Nama Bulan Title
    if ($jenis_laporan == 'bulanan') {
        $months_map = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $header_info['judul_bulan'] = $months_map[$bulan_filter] . " " . $tahun_filter;
    } elseif ($jenis_laporan == 'semester') {
        $header_info['judul_bulan'] = "Semester";
    }

    $kop = get_kop_laporan($pdo);

    // [BARU] Tanda Tangan: Wali Kelas atau Waka Kurikulum
    $header_info['signatory_name'] = $kop['nama_waka_kurikulum'];
    $header_info['signatory_label'] = "Waka Kurikulum";

    if ($kelas_id) {
        $stmt_w = $pdo->prepare("SELECT id_guru FROM penugasan_wali_kelas WHERE id_kelas = ? AND id_ta = ?");
        $stmt_w->execute([$kelas_id, $id_ta_tampil]);
        $walas_id = $stmt_w->fetchColumn();
        if ($walas_id) {
            $header_info['signatory_name'] = get_formatted_nama_guru($pdo, $walas_id);
            $header_info['signatory_label'] = "Wali Kelas";
        }
    }

    include __DIR__ . '/../views/laporan_print_absensi_siswa_piket.php';
}

// ============ LAPORAN ABSENSI GURU (DIPERBARUI) ==============
function laporan_absensi_guru($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    // Default dates for Harian
    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    $list = [];
    $data = [];

    if ($id_ta_tampil) {
        $sql_common_where = "a.tanggal BETWEEN ? AND ? AND a.id_ta = ?";
        $params = [$start_date, $end_date, $id_ta_tampil];

        if ($jenis_laporan == 'harian') {
            $sql = "SELECT g.nama, a.tanggal, a.status, a.keterangan, a.tugas
                    FROM absensi_guru a
                    JOIN guru g ON a.id_guru=g.id_guru
                    WHERE $sql_common_where
                    ORDER BY a.tanggal, g.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
            $dates = [];
            foreach ($period as $dt)
                $dates[] = $dt->format('Y-m-d');

            $sql = "SELECT g.id_guru, g.nama, a.tanggal, a.status
                    FROM absensi_guru a
                    JOIN guru g ON a.id_guru = g.id_guru
                    WHERE $sql_common_where
                    ORDER BY g.nama, a.tanggal";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $matrix = [];
            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_guru']])) {
                    $matrix[$r['id_guru']] = [
                        'nama' => $r['nama'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                $matrix[$r['id_guru']]['attendance'][$r['tanggal']] = $r['status'];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_guru']]['summary'][$code]))
                    $matrix[$r['id_guru']]['summary'][$code]++;
            }
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['dates' => $dates, 'teachers' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            $sql = "SELECT g.id_guru, g.nama, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_guru a
                    JOIN guru g ON a.id_guru = g.id_guru
                    WHERE $sql_common_where
                    GROUP BY g.id_guru, month_key, a.status
                    ORDER BY g.nama, month_key";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $months = [];
            $matrix = [];
            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;
                if (!isset($matrix[$r['id_guru']])) {
                    $matrix[$r['id_guru']] = [
                        'nama' => $r['nama'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                if (!isset($matrix[$r['id_guru']]['months'][$m]))
                    $matrix[$r['id_guru']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_guru']]['total'][$code])) {
                    $matrix[$r['id_guru']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_guru']]['total'][$code] += $r['count'];
                }
            }
            sort($months);
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['months' => $months, 'teachers' => $matrix];
        }
    }
    include __DIR__ . '/../views/laporan_absensi_guru.php';
}

function laporan_absensi_guru_print($pdo)
{
    // DUPLICATE LOGIC FOR PRINT
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'harian';
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $bulan_filter = $_GET['bulan'] ?? date('m');
    $tahun_filter = $_GET['tahun'] ?? date('Y');

    $bulan1 = $_GET['bulan1'] ?? '07';
    $bulan2 = $_GET['bulan2'] ?? '12';
    $tahun_semester = $_GET['tahun_semester'] ?? date('Y');

    // Default dates
    $start_date = $tanggal;
    $end_date = $tanggal;

    if ($jenis_laporan == 'bulanan') {
        $start_date = "$tahun_filter-$bulan_filter-01";
        $end_date = date('Y-m-t', strtotime($start_date));
    } elseif ($jenis_laporan == 'semester') {
        $start_date = "$tahun_semester-$bulan1-01";
        $end_date = date('Y-m-t', strtotime("$tahun_semester-$bulan2-01"));
    }

    $list = [];
    $data = [];
    $header_info = [];

    if ($id_ta_tampil) {
        $sql_common_where = "a.tanggal BETWEEN ? AND ? AND a.id_ta = ?";
        $params = [$start_date, $end_date, $id_ta_tampil];

        if ($jenis_laporan == 'harian') {
            $sql = "SELECT g.nama, a.tanggal, a.status, a.keterangan, a.tugas
                    FROM absensi_guru a
                    JOIN guru g ON a.id_guru=g.id_guru
                    WHERE $sql_common_where
                    ORDER BY a.tanggal, g.nama";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($jenis_laporan == 'bulanan') {
            $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));
            $dates = [];
            foreach ($period as $dt)
                $dates[] = $dt->format('Y-m-d');

            $sql = "SELECT g.id_guru, g.nama, a.tanggal, a.status
                    FROM absensi_guru a
                    JOIN guru g ON a.id_guru = g.id_guru
                    WHERE $sql_common_where
                    ORDER BY g.nama, a.tanggal";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $matrix = [];
            foreach ($raw as $r) {
                if (!isset($matrix[$r['id_guru']])) {
                    $matrix[$r['id_guru']] = [
                        'nama' => $r['nama'],
                        'attendance' => [],
                        'summary' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                $matrix[$r['id_guru']]['attendance'][$r['tanggal']] = $r['status'];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_guru']]['summary'][$code]))
                    $matrix[$r['id_guru']]['summary'][$code]++;
            }
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['dates' => $dates, 'teachers' => $matrix];

        } elseif ($jenis_laporan == 'semester') {
            $sql = "SELECT g.id_guru, g.nama, 
                           DATE_FORMAT(a.tanggal, '%Y-%m') as month_key,
                           a.status, COUNT(*) as count
                    FROM absensi_guru a
                    JOIN guru g ON a.id_guru = g.id_guru
                    WHERE $sql_common_where
                    GROUP BY g.id_guru, month_key, a.status
                    ORDER BY g.nama, month_key";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $months = [];
            $matrix = [];
            foreach ($raw as $r) {
                $m = $r['month_key'];
                if (!in_array($m, $months))
                    $months[] = $m;
                if (!isset($matrix[$r['id_guru']])) {
                    $matrix[$r['id_guru']] = [
                        'nama' => $r['nama'],
                        'months' => [],
                        'total' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0]
                    ];
                }
                if (!isset($matrix[$r['id_guru']]['months'][$m]))
                    $matrix[$r['id_guru']]['months'][$m] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                $code = strtoupper(substr($r['status'], 0, 1));
                if (isset($matrix[$r['id_guru']]['total'][$code])) {
                    $matrix[$r['id_guru']]['months'][$m][$code] += $r['count'];
                    $matrix[$r['id_guru']]['total'][$code] += $r['count'];
                }
            }
            sort($months);
            usort($matrix, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
            $data = ['months' => $months, 'teachers' => $matrix];
        }
    }

    $header_info['ta'] = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '';

    // Nama Bulan Title
    if ($jenis_laporan == 'bulanan') {
        $months_map = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $header_info['judul_bulan'] = $months_map[$bulan_filter] . " " . $tahun_filter;
    } elseif ($jenis_laporan == 'semester') {
        $header_info['judul_bulan'] = "Semester";
    }

    $kop = get_kop_laporan($pdo);

    include __DIR__ . '/../views/laporan_print_absensi_guru.php';
}

// ============ LAPORAN ABSENSI GURU (TIDAK BERUBAH) ==============
function laporan_absensi_guru_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT g.nama, a.tanggal, a.status, a.keterangan, a.tugas
                FROM absensi_guru a
                JOIN guru g ON a.id_guru=g.id_guru
                WHERE a.tanggal BETWEEN ? AND ? AND a.id_ta = ?
                ORDER BY a.tanggal, g.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Absensi Guru (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama Guru', 'Tanggal', 'Status', 'Keterangan', 'Tugas'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['nama'], $d['tanggal'], $d['status'], $d['keterangan'], $d['tugas']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_absensi_guru");
}
function laporan_absensi_guru_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT g.nama, a.tanggal, a.status, a.keterangan, a.tugas
                FROM absensi_guru a
                JOIN guru g ON a.id_guru=g.id_guru
                WHERE a.tanggal BETWEEN ? AND ? AND a.id_ta = ?
                ORDER BY a.tanggal, g.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Absensi Guru (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama Guru', 'Tanggal', 'Status', 'Keterangan', 'Tugas'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['nama'], $d['tanggal'], $d['status'], $d['keterangan'], $d['tugas']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_absensi_guru");
}

// ============ LAPORAN JURNAL KBM (TIDAK BERUBAH) ==============
function helper_format_jam_ke_waktu($pdo, $jam_ke_raw)
{
    if (empty($jam_ke_raw))
        return '-';

    // Jika data lama sudah terlanjur format waktu (ada titik dua), rapikan
    if (strpos($jam_ke_raw, ':') !== false) {
        return str_replace(':', '.', $jam_ke_raw);
    }

    // Jika data berupa label "1, 2"
    $labels = explode(',', $jam_ke_raw);
    $labels = array_map('trim', $labels);

    if (empty($labels))
        return $jam_ke_raw;

    $placeholders = implode(',', array_fill(0, count($labels), '?'));

    // Ambil Jam Mulai Paling Awal dan Selesai Paling Akhir
    $sql = "SELECT MIN(jam_mulai) as mulai, MAX(jam_selesai) as selesai 
            FROM jam_pelajaran 
            WHERE label_jam_ke IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($labels);
    $waktu = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($waktu && $waktu['mulai'] && $waktu['selesai']) {
        $start = date('H.i', strtotime($waktu['mulai']));
        $end = date('H.i', strtotime($waktu['selesai']));
        return $start . ' - ' . $end;
    }

    return $jam_ke_raw;
}

// --- REVISI: CONTROLLER TAMPILAN WEB ---
function laporan_jurnal($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    // Ambil data filter dari URL
    $jenis_laporan = $_GET['jenis_laporan'] ?? 'bulanan';
    $kelas = $_GET['kelas'] ?? '';
    $guru = $_GET['guru'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $bulan = $_GET['bulan'] ?? '';
    $tahun = $_GET['tahun'] ?? date('Y');
    $bulan_awal = $_GET['bulan_awal'] ?? '';
    $bulan_akhir = $_GET['bulan_akhir'] ?? '';

    // Logic penentuan rentang tanggal
    if ($jenis_laporan == 'bulanan' && $bulan && $tahun) {
        $tanggal1 = "$tahun-$bulan-01";
        $tanggal2 = date('Y-m-t', strtotime($tanggal1));
    } elseif ($jenis_laporan == 'semester' && $bulan_awal && $bulan_akhir && $tahun) {
        $tanggal1 = "$tahun-$bulan_awal-01";
        $tanggal2 = date('Y-m-t', strtotime("$tahun-$bulan_akhir-01"));
    }

    // Role-based filtering for Class and Teacher lists
    if ($id_guru_login > 0) {
        // Teacher
        $guru_list = $pdo->query("SELECT id_guru, nama FROM guru WHERE id_guru = $id_guru_login")->fetchAll(PDO::FETCH_ASSOC);
        $kelas_list = $pdo->prepare("SELECT DISTINCT k.* FROM kelas k 
                                    JOIN jadwal_mengajar j ON k.id_kelas = j.id_kelas 
                                    JOIN guru_mapel gm ON j.id_guru_mapel = gm.id_guru_mapel 
                                    WHERE gm.id_guru = ? AND gm.id_ta = ? ORDER BY k.tingkat, k.nama_kelas");
        $kelas_list->execute([$id_guru_login, $id_ta_tampil]);
        $kelas_list = $kelas_list->fetchAll(PDO::FETCH_ASSOC);

        $guru = $id_guru_login; // Force teacher to their own ID
    } else {
        // Admin
        $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_ta_tampil]);
        $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $guru_list = $pdo->query("SELECT id_guru, nama FROM guru WHERE status='Aktif' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
    }

    $list = [];

    if ($id_ta_tampil && $tanggal1 && $tanggal2) {
        $sql = "SELECT g.nama AS guru, k.nama_kelas, k.tingkat, j.tanggal, j.jam_ke, j.tujuan_pembelajaran, j.tagihan, j.catatan_absensi, j.keterangan
                FROM jurnal_kbm j
                JOIN guru g ON j.id_guru=g.id_guru
                JOIN kelas k ON j.id_kelas=k.id_kelas
                WHERE j.tanggal BETWEEN ? AND ? AND j.id_ta = ?";

        $params = [$tanggal1, $tanggal2, $id_ta_tampil];
        if ($kelas) {
            $sql .= " AND j.id_kelas = ?";
            $params[] = $kelas;
        }
        if ($guru) {
            $sql .= " AND j.id_guru = ?";
            $params[] = $guru;
        }

        $sql .= " ORDER BY j.tanggal, j.jam_ke";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $raw_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- [REVISI] KONVERSI JAM UNTUK TAMPILAN WEB ---
        foreach ($raw_list as $row) {
            $row['jam_ke'] = helper_format_jam_ke_waktu($pdo, $row['jam_ke']);
            $list[] = $row;
        }
    }

    extract(compact('kelas_list', 'guru_list', 'list', 'kelas', 'guru', 'tanggal1', 'tanggal2'));
    include __DIR__ . '/../views/laporan_jurnal.php';
}

// --- REVISI: CONTROLLER CETAK ---
function laporan_jurnal_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'bulanan';
    $kelas_id = $_GET['kelas'] ?? '';
    $guru_id = $_GET['guru'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $bulan = $_GET['bulan'] ?? '';
    $tahun = $_GET['tahun'] ?? date('Y');
    $bulan_awal = $_GET['bulan_awal'] ?? '';
    $bulan_akhir = $_GET['bulan_akhir'] ?? '';

    // Logic penentuan rentang tanggal (sama dengan controller web)
    if ($jenis_laporan == 'bulanan' && $bulan && $tahun) {
        $tanggal1 = "$tahun-$bulan-01";
        $tanggal2 = date('Y-m-t', strtotime($tanggal1));
    } elseif ($jenis_laporan == 'semester' && $bulan_awal && $bulan_akhir && $tahun) {
        $tanggal1 = "$tahun-$bulan_awal-01";
        $tanggal2 = date('Y-m-t', strtotime("$tahun-$bulan_akhir-01"));
    }

    // Role-based filtering
    if ($id_guru_login > 0) {
        $guru_id = $id_guru_login;
    }

    $rows = [];

    if ($id_ta_tampil && $tanggal1 && $tanggal2) {
        $sql = "SELECT 
                    j.tanggal, j.jam_ke, j.tujuan_pembelajaran, j.tagihan, j.catatan_absensi, 
                    g.nama AS nama_guru, k.nama_kelas,
                    (SELECT m.nama_mapel FROM guru_mapel gm JOIN mapel m ON gm.id_mapel = m.id_mapel WHERE gm.id_guru = j.id_guru AND gm.id_ta = j.id_ta LIMIT 1) AS nama_mapel_guess
                FROM jurnal_kbm j
                JOIN guru g ON j.id_guru = g.id_guru
                JOIN kelas k ON j.id_kelas = k.id_kelas
                WHERE j.tanggal BETWEEN ? AND ? AND j.id_ta = ?";

        $params = [$tanggal1, $tanggal2, $id_ta_tampil];
        if ($kelas_id) {
            $sql .= " AND j.id_kelas = ?";
            $params[] = $kelas_id;
        }
        if ($guru_id) {
            $sql .= " AND j.id_guru = ?";
            $params[] = $guru_id;
        }
        $sql .= " ORDER BY j.tanggal ASC, j.jam_ke ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $hari_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];

        foreach ($data_raw as $d) {
            $time = strtotime($d['tanggal']);
            $waktu_final = helper_format_jam_ke_waktu($pdo, $d['jam_ke']);

            $rows[] = [
                'tanggal_raw' => $d['tanggal'],
                'hari' => $hari_indo[date('l', $time)],
                'tanggal_indo' => date('d-m-Y', $time),
                'waktu' => $waktu_final,
                'guru' => $d['nama_guru'],
                'mapel' => $d['nama_mapel_guess'] ?? '-',
                'tujuan' => $d['tujuan_pembelajaran'],
                'tagihan' => $d['tagihan'],
                'absensi' => $d['catatan_absensi']
            ];
        }
    }

    // -- Ambil Data Kop & TTD --
    $kop = get_kop_laporan($pdo);
    $profil = ProfilSekolahModel::getProfil($pdo);
    // Data info Header
    $info_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '-';
    $info_kelas = "SEMUA KELAS";
    if ($kelas_id) {
        $stmt_k = $pdo->prepare("SELECT nama_kelas, tingkat FROM kelas WHERE id_kelas = ?");
        $stmt_k->execute([$kelas_id]);
        $dt_kelas = $stmt_k->fetch(PDO::FETCH_ASSOC);
        if ($dt_kelas)
            $info_kelas = $dt_kelas['nama_kelas'];
    }

    $info_kepsek = $kop['nama_kepala_sekolah'] ?? '(.......................................)';

    // Signature Logic
    $right_sig_label = "Wali Kelas";
    $right_sig_name = "(.......................................)";

    if ($guru_id) {
        $right_sig_label = "Guru Mata Pelajaran";
        $right_sig_name = get_formatted_nama_guru($pdo, $guru_id);
    } elseif ($kelas_id) {
        // Get Wali Kelas ID
        $stmt_w = $pdo->prepare("SELECT id_guru FROM penugasan_wali_kelas WHERE id_kelas = ? AND id_ta = ?");
        $stmt_w->execute([$kelas_id, $id_ta_tampil]);
        $walas_id = $stmt_w->fetchColumn();
        if ($walas_id) {
            $right_sig_name = get_formatted_nama_guru($pdo, $walas_id);
        }
    }

    // Periode Info
    $info_periode = "";
    $months_id = ['01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MARET', '04' => 'APRIL', '05' => 'MEI', '06' => 'JUNI', '07' => 'JULI', '08' => 'AGUSTUS', '09' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER'];

    if ($jenis_laporan == 'bulanan' && $bulan) {
        $info_periode = "BULAN : " . $months_id[$bulan] . " " . $tahun;
    } elseif ($jenis_laporan == 'semester' && $bulan_awal && $bulan_akhir) {
        $info_periode = "PERIODE : " . $months_id[$bulan_awal] . " - " . $months_id[$bulan_akhir] . " " . $tahun;
    } else {
        $info_periode = "PERIODE : " . date('d/m/Y', strtotime($tanggal1)) . " s/d " . date('d/m/Y', strtotime($tanggal2));
    }

    include __DIR__ . '/../views/laporan_print_preview_jurnal.php';
}

function laporan_jurnal_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'bulanan';
    $kelas_id = $_GET['kelas'] ?? '';
    $guru_id = $_GET['guru'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $bulan = $_GET['bulan'] ?? '';
    $tahun = $_GET['tahun'] ?? date('Y');
    $bulan_awal = $_GET['bulan_awal'] ?? '';
    $bulan_akhir = $_GET['bulan_akhir'] ?? '';

    if ($jenis_laporan == 'bulanan' && $bulan && $tahun) {
        $tanggal1 = "$tahun-$bulan-01";
        $tanggal2 = date('Y-m-t', strtotime($tanggal1));
    } elseif ($jenis_laporan == 'semester' && $bulan_awal && $bulan_akhir && $tahun) {
        $tanggal1 = "$tahun-$bulan_awal-01";
        $tanggal2 = date('Y-m-t', strtotime("$tahun-$bulan_akhir-01"));
    }

    if ($id_guru_login > 0)
        $guru_id = $id_guru_login;

    $list = [];
    if ($id_ta_tampil && $tanggal1 && $tanggal2) {
        $sql = "SELECT j.tanggal, j.jam_ke, k.nama_kelas, g.nama AS nama_guru, j.tujuan_pembelajaran, j.tagihan, j.catatan_absensi
                FROM jurnal_kbm j
                JOIN guru g ON j.id_guru=g.id_guru
                JOIN kelas k ON j.id_kelas=k.id_kelas
                WHERE j.tanggal BETWEEN ? AND ? AND j.id_ta = ?";
        $params = [$tanggal1, $tanggal2, $id_ta_tampil];
        if ($kelas_id) {
            $sql .= " AND j.id_kelas = ?";
            $params[] = $kelas_id;
        }
        if ($guru_id) {
            $sql .= " AND j.id_guru = ?";
            $params[] = $guru_id;
        }
        $sql .= " ORDER BY j.tanggal, j.jam_ke";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Jurnal KBM";
    $kolom = ['No', 'Tanggal', 'Jam', 'Kelas', 'Guru', 'Tujuan Pembelajaran', 'Tagihan', 'Absensi'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [$no++, $d['tanggal'], helper_format_jam_ke_waktu($pdo, $d['jam_ke']), $d['nama_kelas'], $d['nama_guru'], $d['tujuan_pembelajaran'], $d['tagihan'], $d['catatan_absensi']];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_jurnal");
}

function laporan_jurnal_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    $jenis_laporan = $_GET['jenis_laporan'] ?? 'bulanan';
    $kelas_id = $_GET['kelas'] ?? '';
    $guru_id = $_GET['guru'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $bulan = $_GET['bulan'] ?? '';
    $tahun = $_GET['tahun'] ?? date('Y');
    $bulan_awal = $_GET['bulan_awal'] ?? '';
    $bulan_akhir = $_GET['bulan_akhir'] ?? '';

    if ($jenis_laporan == 'bulanan' && $bulan && $tahun) {
        $tanggal1 = "$tahun-$bulan-01";
        $tanggal2 = date('Y-m-t', strtotime($tanggal1));
    } elseif ($jenis_laporan == 'semester' && $bulan_awal && $bulan_akhir && $tahun) {
        $tanggal1 = "$tahun-$bulan_awal-01";
        $tanggal2 = date('Y-m-t', strtotime("$tahun-$bulan_akhir-01"));
    }

    if ($id_guru_login > 0)
        $guru_id = $id_guru_login;

    $list = [];
    if ($id_ta_tampil && $tanggal1 && $tanggal2) {
        $sql = "SELECT j.tanggal, j.jam_ke, k.nama_kelas, g.nama AS nama_guru, j.tujuan_pembelajaran, j.tagihan, j.catatan_absensi
                FROM jurnal_kbm j
                JOIN guru g ON j.id_guru=g.id_guru
                JOIN kelas k ON j.id_kelas=k.id_kelas
                WHERE j.tanggal BETWEEN ? AND ? AND j.id_ta = ?";
        $params = [$tanggal1, $tanggal2, $id_ta_tampil];
        if ($kelas_id) {
            $sql .= " AND j.id_kelas = ?";
            $params[] = $kelas_id;
        }
        if ($guru_id) {
            $sql .= " AND j.id_guru = ?";
            $params[] = $guru_id;
        }
        $sql .= " ORDER BY j.tanggal, j.jam_ke";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Jurnal KBM";
    $kolom = ['No', 'Tanggal', 'Jam', 'Kelas', 'Guru', 'Tujuan Pembelajaran', 'Tagihan', 'Absensi'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [$no++, $d['tanggal'], helper_format_jam_ke_waktu($pdo, $d['jam_ke']), $d['nama_kelas'], $d['nama_guru'], $d['tujuan_pembelajaran'], $d['tagihan'], $d['catatan_absensi']];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_jurnal");
}

// ============ LAPORAN JADWAL (REVISI FINAL - MENGGUNAKAN KODE) ==============
function get_laporan_jadwal_data($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $filter_type = $_GET['filter_type'] ?? 'per_kelas';
    $kelas_id = $_GET['kelas'] ?? null;
    $guru_id = $_GET['guru'] ?? null;

    $list = [];
    $profil = $_SESSION['profil_sekolah'] ?? ['nama_sekolah' => 'NAMA SEKOLAH'];

    $info_tambahan = [
        'nama_sekolah' => $profil['nama_sekolah'],
        'tahun_ajaran' => $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A',
        'nama_kelas' => '',
        'nama_guru' => ''
    ];

    $params = [$id_ta_tampil];

    $sql_base = "SELECT d.hari_kbm, j.label_jam_ke AS jam_ke, j.jam_mulai, j.jam_selesai, 
                        m.nama_mapel, m.kode_mapel, 
                        g.nama AS nama_guru, g.kode_guru, 
                        k.nama_kelas, k.tingkat
                 FROM jadwal_mengajar d
                 JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                 JOIN guru g ON gm.id_guru = g.id_guru
                 JOIN mapel m ON gm.id_mapel = m.id_mapel
                 JOIN kelas k ON d.id_kelas = k.id_kelas
                 JOIN jam_pelajaran j ON d.id_jam = j.id_jam
                 WHERE gm.id_ta = ?";

    if ($filter_type == 'per_kelas' && $kelas_id) {
        $sql_base .= " AND d.id_kelas = ?";
        $params[] = $kelas_id;
        $stmt_info = $pdo->prepare("SELECT nama_kelas FROM kelas WHERE id_kelas = ?");
        $stmt_info->execute([$kelas_id]);
        $info_tambahan['nama_kelas'] = $stmt_info->fetchColumn();

    } elseif ($filter_type == 'per_guru' && $guru_id) {
        $sql_base .= " AND gm.id_guru = ?";
        $params[] = $guru_id;
        $stmt_info = $pdo->prepare("SELECT nama FROM guru WHERE id_guru = ?");
        $stmt_info->execute([$guru_id]);
        $info_tambahan['nama_guru'] = $stmt_info->fetchColumn();
    }

    $sql_base .= " ORDER BY FIELD(d.hari_kbm, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), j.urutan, k.tingkat, k.nama_kelas";

    $stmt = $pdo->prepare($sql_base);
    $stmt->execute($params);
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // [FIX] Grouping data untuk view laporan
    $list_grouped = [];
    foreach ($list as $row) {
        $list_grouped[$row['hari_kbm']][] = $row;
    }

    return [
        'list' => $list,
        'list_grouped' => $list_grouped,
        'info' => $info_tambahan,
        'filter_type' => $filter_type
    ];
}
function laporan_jadwal_pelajaran($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
    $stmt->execute([$id_ta_tampil]);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $guru_list = $pdo->query("SELECT id_guru, nama, kode_guru FROM guru WHERE status='Aktif' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);
    $filter_type = $_GET['filter_type'] ?? 'per_kelas';
    $kelas = $_GET['kelas'] ?? '';
    $guru = $_GET['guru'] ?? '';
    $list = [];
    $judul_laporan = "";
    if (isset($_GET['filter_type'])) {
        $data = get_laporan_jadwal_data($pdo);
        $list = $data['list'];
        $info = $data['info'];
        if ($filter_type == 'per_kelas')
            $judul_laporan = $info['nama_kelas'];
        elseif ($filter_type == 'per_guru')
            $judul_laporan = $info['nama_guru'];
        else
            $judul_laporan = "Keseluruhan";
    }
    extract(compact('kelas_list', 'guru_list', 'list', 'filter_type', 'kelas', 'guru', 'judul_laporan'));
    include __DIR__ . '/../views/laporan_jadwal_pelajaran.php';
}
function laporan_jadwal_pelajaran_export_excel($pdo)
{
    $data = get_laporan_jadwal_data($pdo);
    $list = $data['list'];
    $judul = "Laporan Jadwal Pelajaran (" . $data['info']['tahun_ajaran'] . ")";
    $filter_type = $data['filter_type'];
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Hari', 'Jam Ke', 'Waktu'];
    if ($filter_type == 'keseluruhan')
        $kolom = array_merge($kolom, ['Kelas', 'Mapel (Kode)']);
    if ($filter_type == 'per_kelas')
        $kolom = array_merge($kolom, ['Mata Pelajaran', 'Guru']);
    if ($filter_type == 'per_guru')
        $kolom[] = 'Kelas';
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $row = [$no++, $d['hari_kbm'], $d['jam_ke'], substr($d['jam_mulai'], 0, 5) . '-' . substr($d['jam_selesai'], 0, 5)];
        if ($filter_type == 'keseluruhan') {
            $row[] = $d['tingkat'] . '-' . $d['nama_kelas'];
            $row[] = ($d['kode_guru'] ?? '') . ($d['kode_mapel'] ?? '');
        }
        if ($filter_type == 'per_kelas') {
            $row[] = $d['nama_mapel'];
            $row[] = $d['nama_guru'];
        }
        if ($filter_type == 'per_guru') {
            $row[] = $d['tingkat'] . ' - ' . $d['nama_kelas'];
        }
        $rows[] = $row;
    }
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_jadwal");
}
function laporan_jadwal_pelajaran_export_pdf($pdo)
{
    $filter_type = $_GET['filter_type'] ?? 'per_kelas';
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kop = get_kop_laporan($pdo);
    $judul = "Laporan Jadwal Pelajaran";
    ob_start();
    if ($filter_type == 'keseluruhan') {
        require_once __DIR__ . '/../models/JadwalModel.php';
        $info = get_laporan_jadwal_data($pdo)['info'];
        $jam_slots = JadwalModel::all_jam($pdo);
        $kelas_list = JadwalModel::all_kelas($pdo);
        $legends = JadwalModel::getJadwalLegends($pdo, $id_ta_tampil);
        $guru_legend = $legends['guru'];
        $mapel_legend = $legends['mapel'];
        $jadwal_grid = JadwalModel::getJadwalGridData($pdo, $id_ta_tampil);
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $data_for_view = compact('judul', 'kop', 'info', 'filter_type', 'jam_slots', 'kelas_list', 'guru_legend', 'mapel_legend', 'jadwal_grid', 'hari_list');
        extract($data_for_view);
        include __DIR__ . '/../views/laporan_print_preview_jadwal_grid.php';
    } else {
        $data = get_laporan_jadwal_data($pdo);
        $list_grouped = $data['list_grouped'];
        $info = $data['info'];
        $data_for_view = compact('judul', 'kop', 'info', 'filter_type', 'list_grouped');
        extract($data_for_view);
        include __DIR__ . '/../views/laporan_print_preview_jadwal.php';
    }
    $html = ob_get_clean();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("laporan_jadwal.pdf");
    exit;
}
function laporan_jadwal_pelajaran_print($pdo)
{
    $filter_type = $_GET['filter_type'] ?? 'per_kelas';
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kop = get_kop_laporan($pdo);
    $judul = "Laporan Jadwal Pelajaran";
    if ($filter_type == 'keseluruhan') {
        require_once __DIR__ . '/../models/JadwalModel.php';
        $info = get_laporan_jadwal_data($pdo)['info'];
        $raw_jam_slots = JadwalModel::all_jam($pdo);
        $kelas_list = JadwalModel::all_kelas($pdo);
        $legends = JadwalModel::getJadwalLegends($pdo, $id_ta_tampil);
        $guru_legend = $legends['guru'];
        $mapel_legend = $legends['mapel'];
        $jadwal_grid = JadwalModel::getJadwalGridData($pdo, $id_ta_tampil);
        $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // --- ALGORITMA NORMALISASI TIME SLOT (UNTUK MERGER ROW) ---
        // 1. Kumpulkan semua titik waktu (mulai & selesai)
        $time_points = [];
        foreach ($raw_jam_slots as $j) {
            $time_points[] = substr($j['jam_mulai'], 0, 5);
            $time_points[] = substr($j['jam_selesai'], 0, 5);
        }
        $time_points = array_unique($time_points);
        sort($time_points);

        // 2. Buat Interval Atomik (Baris Dasar Grid)
        $atomic_intervals = [];
        for ($i = 0; $i < count($time_points) - 1; $i++) {
            $start = $time_points[$i];
            $end = $time_points[$i + 1];

            // Validasi: interval harus masuk akal (tidak 0 menit)
            if ($start < $end) {
                $atomic_intervals[] = [
                    'start' => $start,
                    'end' => $end,
                    'label' => "$start - $end"
                ];
            }
        }

        // 3. Petakan Setiap Jam dari DB ke Interval Atomik
        $final_schedule_rows = [];
        foreach ($atomic_intervals as $idx => $interval) {
            $row_data = [
                'start' => $interval['start'],
                'end' => $interval['end'],
                'slots_per_day' => [] // Key: Hari => [ 'id_jam' => ..., 'rowspan' => ... ]
            ];

            foreach ($hari_list as $hari) {
                // Cari Jam ID yang aktif di interval ini untuk hari ini
                // REVISI: Cari kandidat KBM dan Non-KBM (Overlap Handling)
                $found_non_kbm = null;
                $found_kbm = null;
                $rowspan = 1; // Default

                // Track start/end for rowspan calculation logic (using primary match)
                $primary_match_start = null;
                $primary_match_end = null;

                foreach ($raw_jam_slots as $jam) {
                    $j_start = substr($jam['jam_mulai'], 0, 5);
                    $j_end = substr($jam['jam_selesai'], 0, 5);

                    // Cek interval coverage
                    if ($j_start <= $interval['start'] && $j_end >= $interval['end']) {

                        $is_valid_day = true;
                        if ($jam['jenis_kegiatan'] != 'KBM' && !empty($jam['hari_pelaksanaan'])) {
                            if (strpos($jam['hari_pelaksanaan'], $hari) === false) {
                                $is_valid_day = false;
                            }
                        }

                        if ($is_valid_day) {
                            if ($jam['jenis_kegiatan'] == 'KBM') {
                                if (!$found_kbm)
                                    $found_kbm = $jam;
                            } else {
                                if (!$found_non_kbm)
                                    $found_non_kbm = $jam;
                            }
                        }
                    }
                }

                // Decision Logic: Prioritize Non-KBM as "Primary" for display default, but keep KBM as alt.
                // If both exist, View determines if KBM has data.
                $primary_jam = $found_non_kbm ?? $found_kbm;

                if ($primary_jam) {
                    // Recalculate Rowspan based on PRIMARY
                    $j_start = substr($primary_jam['jam_mulai'], 0, 5);
                    $j_end = substr($primary_jam['jam_selesai'], 0, 5);

                    if ($j_start == $interval['start']) {
                        $span_count = 0;
                        foreach ($atomic_intervals as $chk_int) {
                            if ($chk_int['start'] >= $j_start && $chk_int['end'] <= $j_end) {
                                $span_count++;
                            }
                        }
                        $rowspan = $span_count;
                        $status = 'START';
                    } else {
                        // Check if we are inside the span of the SAME jam
                        $status = 'SKIP';
                    }
                } else {
                    $status = 'EMPTY';
                }

                $row_data['slots_per_day'][$hari] = [
                    'jam_data' => $primary_jam,      // Default (Upacara)
                    'kbm_jam_data' => $found_kbm,    // Alternative (Jam 1)
                    'status' => $status,
                    'rowspan' => $rowspan
                ];
            }
            $final_schedule_rows[] = $row_data;
        }

        // Variable untuk View diganti dari jam_slots ke final_schedule_rows\
        // passing raw_jam_slots juga jaga-jaga
        $data_for_view = compact('judul', 'kop', 'info', 'filter_type', 'final_schedule_rows', 'raw_jam_slots', 'kelas_list', 'guru_legend', 'mapel_legend', 'jadwal_grid', 'hari_list');
        extract($data_for_view);
        include __DIR__ . '/../views/laporan_print_preview_jadwal_grid.php';
    } else {
        $data = get_laporan_jadwal_data($pdo);
        $list_grouped = $data['list_grouped'];
        $info = $data['info'];
        $data_for_view = compact('judul', 'kop', 'info', 'filter_type', 'list_grouped');
        extract($data_for_view);
        include __DIR__ . '/../views/laporan_print_preview_jadwal.php';
    }
}

// ============ LAPORAN CATATAN KASUS (DIREVISI) ==============
function laporan_catatan_kasus($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
    $stmt->execute([$id_ta_tampil]);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, k.nama_kelas, c.*
                FROM catatan_kasus c
                JOIN siswa s ON c.id_siswa = s.id_siswa
                JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
                JOIN kelas k ON p.id_kelas = k.id_kelas
                WHERE k.id_kelas = ? AND c.tanggal BETWEEN ? AND ?
                ORDER BY c.tanggal, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil, $kelas, $tanggal1, $tanggal2]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    include __DIR__ . '/../views/laporan_catatan_kasus.php';
}
function laporan_catatan_kasus_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, k.nama_kelas, c.*
                FROM catatan_kasus c
                JOIN siswa s ON c.id_siswa = s.id_siswa
                JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
                JOIN kelas k ON p.id_kelas = k.id_kelas
                WHERE k.id_kelas = ? AND c.tanggal BETWEEN ? AND ?
                ORDER BY c.tanggal, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil, $kelas, $tanggal1, $tanggal2]); // <-- Perbaikan urutan parameter
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Catatan Kasus (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tanggal', 'Kelas', 'Nama Siswa', 'Kasus', 'Tindak Lanjut', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal'], $d['nama_kelas'], $d['nama'], $d['kasus'], $d['tindak_lanjut'], $d['status']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_catatan_kasus");
}
function laporan_catatan_kasus_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, k.nama_kelas, c.*
                FROM catatan_kasus c
                JOIN siswa s ON c.id_siswa = s.id_siswa
                JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
                JOIN kelas k ON p.id_kelas = k.id_kelas
                WHERE k.id_kelas = ? AND c.tanggal BETWEEN ? AND ?
                ORDER BY c.tanggal, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil, $kelas, $tanggal1, $tanggal2]); // <-- Perbaikan urutan parameter
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Catatan Kasus (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tanggal', 'Kelas', 'Nama Siswa', 'Kasus', 'Tindak Lanjut', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal'], $d['nama_kelas'], $d['nama'], $d['kasus'], $d['tindak_lanjut'], $d['status']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_catatan_kasus");
}
function laporan_catatan_kasus_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, k.nama_kelas, c.*
                FROM catatan_kasus c
                JOIN siswa s ON c.id_siswa = s.id_siswa
                JOIN penempatan_siswa p ON s.id_siswa = p.id_siswa AND p.id_ta = ?
                JOIN kelas k ON p.id_kelas = k.id_kelas
                WHERE k.id_kelas = ? AND c.tanggal BETWEEN ? AND ?
                ORDER BY c.tanggal, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil, $kelas, $tanggal1, $tanggal2]); // <-- Perbaikan urutan parameter
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Catatan Kasus (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Tanggal', 'Kelas', 'Nama Siswa', 'Kasus', 'Tindak Lanjut', 'Status'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal'], $d['nama_kelas'], $d['nama'], $d['kasus'], $d['tindak_lanjut'], $d['status']];

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN CATATAN KELAS (REVISI SQL) ==============
function laporan_catatan_kelas($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
    $stmt->execute([$id_ta_tampil]);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        // ⭐ REVISI: Mengganti j.tanggal menjadi ck.tanggal dan j.catatan_kejadian menjadi ck.catatan_kejadian
        $sql = "SELECT k.nama_kelas, k.tingkat, ck.tanggal, ck.catatan_kejadian, g.nama AS nama_guru_mapel, m.nama_mapel
                FROM catatan_kelas ck
                JOIN jadwal_mengajar dm ON ck.id_jadwal_mengajar = dm.id_jadwal_mengajar
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                WHERE dm.id_kelas = ? AND ck.tanggal BETWEEN ? AND ? AND ck.id_ta = ?
                ORDER BY ck.tanggal, k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Kirim $kelas_list, $list, $kelas, $tanggal1, $tanggal2 ke view
    extract(compact('kelas_list', 'list', 'kelas', 'tanggal1', 'tanggal2'));
    include __DIR__ . '/../views/laporan_catatan_kelas.php';
}
function laporan_catatan_kelas_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        // ⭐ REVISI: Mengganti j.tanggal menjadi ck.tanggal dan j.catatan_kejadian menjadi ck.catatan_kejadian
        $sql = "SELECT k.nama_kelas, k.tingkat, ck.tanggal, ck.catatan_kejadian, g.nama AS nama_guru_mapel, m.nama_mapel
                FROM catatan_kelas ck
                JOIN jadwal_mengajar dm ON ck.id_jadwal_mengajar = dm.id_jadwal_mengajar
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                WHERE dm.id_kelas = ? AND ck.tanggal BETWEEN ? AND ? AND ck.id_ta = ?
                ORDER BY ck.tanggal, k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Catatan Kelas (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tanggal', 'Kelas', 'Mata Pelajaran', 'Guru', 'Catatan Kejadian'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['nama_mapel'], $d['nama_guru_mapel'], $d['catatan_kejadian']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_catatan_kelas");
}
function laporan_catatan_kelas_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        // ⭐ REVISI: Mengganti j.tanggal menjadi ck.tanggal dan j.catatan_kejadian menjadi ck.catatan_kejadian
        $sql = "SELECT k.nama_kelas, k.tingkat, ck.tanggal, ck.catatan_kejadian, g.nama AS nama_guru_mapel, m.nama_mapel
                FROM catatan_kelas ck
                JOIN jadwal_mengajar dm ON ck.id_jadwal_mengajar = dm.id_jadwal_mengajar
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                WHERE dm.id_kelas = ? AND ck.tanggal BETWEEN ? AND ? AND ck.id_ta = ?
                ORDER BY ck.tanggal, k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Catatan Kelas (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tanggal', 'Kelas', 'Mata Pelajaran', 'Guru', 'Catatan Kejadian'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['nama_mapel'], $d['nama_guru_mapel'], $d['catatan_kejadian']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_catatan_kelas");
}
function laporan_catatan_kelas_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($kelas && $tanggal1 && $tanggal2 && $id_ta_tampil) {
        // ⭐ REVISI: Mengganti j.tanggal menjadi ck.tanggal dan j.catatan_kejadian menjadi ck.catatan_kejadian
        $sql = "SELECT k.nama_kelas, k.tingkat, ck.tanggal, ck.catatan_kejadian, g.nama AS nama_guru_mapel, m.nama_mapel
                FROM catatan_kelas ck
                JOIN jadwal_mengajar dm ON ck.id_jadwal_mengajar = dm.id_jadwal_mengajar
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                WHERE dm.id_kelas = ? AND ck.tanggal BETWEEN ? AND ? AND ck.id_ta = ?
                ORDER BY ck.tanggal, k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Catatan Kelas (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Tanggal', 'Kelas', 'Mata Pelajaran', 'Guru', 'Catatan Kejadian'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['nama_mapel'], $d['nama_guru_mapel'], $d['catatan_kejadian']];

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN MUTASI SISWA (BARU) ==============
function laporan_mutasi_siswa($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, s.nisn, k.nama_kelas, k.tingkat, ms.tanggal_mutasi, ms.jenis_mutasi, ms.alasan
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.tanggal_mutasi BETWEEN ? AND ? AND ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    include __DIR__ . '/../views/laporan_mutasi_siswa.php';
}
function laporan_mutasi_siswa_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, s.nisn, k.nama_kelas, k.tingkat, ms.tanggal_mutasi, ms.jenis_mutasi, ms.alasan
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.tanggal_mutasi BETWEEN ? AND ? AND ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Mutasi Siswa (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tanggal Mutasi', 'Nama Siswa', 'NISN', 'Kelas', 'Jenis Mutasi', 'Alasan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal_mutasi'], $d['nama'], $d['nisn'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['jenis_mutasi'], $d['alasan']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_siswa");
}
function laporan_mutasi_siswa_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, s.nisn, k.nama_kelas, k.tingkat, ms.tanggal_mutasi, ms.jenis_mutasi, ms.alasan
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.tanggal_mutasi BETWEEN ? AND ? AND ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Mutasi Siswa (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tanggal Mutasi', 'Nama Siswa', 'NISN', 'Kelas', 'Jenis Mutasi', 'Alasan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal_mutasi'], $d['nama'], $d['nisn'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['jenis_mutasi'], $d['alasan']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_siswa");
}
function laporan_mutasi_siswa_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal1 = $_GET['tanggal1'] ?? '';
    $tanggal2 = $_GET['tanggal2'] ?? '';
    $list = [];
    if ($tanggal1 && $tanggal2 && $id_ta_tampil) {
        $sql = "SELECT s.nama, s.nisn, k.nama_kelas, k.tingkat, ms.tanggal_mutasi, ms.jenis_mutasi, ms.alasan
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.tanggal_mutasi BETWEEN ? AND ? AND ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi, s.nama";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal1, $tanggal2, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Mutasi Siswa (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Tanggal Mutasi', 'Nama Siswa', 'NISN', 'Kelas', 'Jenis Mutasi', 'Alasan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['tanggal_mutasi'], $d['nama'], $d['nisn'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['jenis_mutasi'], $d['alasan']];

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN PENILAIAN SUMATIF (BARU) ==============
function laporan_penilaian_sumatif($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $mapel = $_GET['mapel'] ?? '';
    $jenis = $_GET['jenis'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
    $stmt->execute([$id_ta_tampil]);
    $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $mapel_list = $pdo->query("SELECT * FROM mapel ORDER BY nama_mapel")->fetchAll(PDO::FETCH_ASSOC);
    $list = [];

    if ($kelas && $mapel && $jenis && $id_ta_tampil) {
        $sql = "SELECT s.nama AS nama_siswa, s.nisn, k.nama_kelas, k.tingkat, m.nama_mapel, ps.nama_penilaian, ps.jenis_sumatif, ns.nilai, ns.deskripsi_capaian
                FROM nilai_sumatif ns
                JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
                JOIN penempatan_siswa p ON ns.id_penempatan = p.id_penempatan
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE p.id_kelas = ? AND gm.id_mapel = ? AND ps.jenis_sumatif = ? AND ps.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas, s.nama, ps.tanggal_penilaian";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $mapel, $jenis, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    include __DIR__ . '/../views/laporan_penilaian_sumatif.php';
}
function laporan_penilaian_sumatif_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $mapel = $_GET['mapel'] ?? '';
    $jenis = $_GET['jenis'] ?? '';
    $list = [];

    if ($kelas && $mapel && $jenis && $id_ta_tampil) {
        $sql = "SELECT s.nama AS nama_siswa, s.nisn, k.nama_kelas, k.tingkat, m.nama_mapel, ps.nama_penilaian, ps.jenis_sumatif, ns.nilai, ns.deskripsi_capaian
                FROM nilai_sumatif ns
                JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
                JOIN penempatan_siswa p ON ns.id_penempatan = p.id_penempatan
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE p.id_kelas = ? AND gm.id_mapel = ? AND ps.jenis_sumatif = ? AND ps.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas, s.nama, ps.tanggal_penilaian";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $mapel, $jenis, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Penilaian Sumatif (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Mata Pelajaran', 'Nama Penilaian', 'Jenis Sumatif', 'Nilai', 'Deskripsi Capaian'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['nama_siswa'], $d['nisn'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['nama_mapel'], $d['nama_penilaian'], $d['jenis_sumatif'], $d['nilai'], $d['deskripsi_capaian']];
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_penilaian_sumatif");
}
function laporan_penilaian_sumatif_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $mapel = $_GET['mapel'] ?? '';
    $jenis = $_GET['jenis'] ?? '';
    $list = [];

    if ($kelas && $mapel && $jenis && $id_ta_tampil) {
        $sql = "SELECT s.nama AS nama_siswa, s.nisn, k.nama_kelas, k.tingkat, m.nama_mapel, ps.nama_penilaian, ps.jenis_sumatif, ns.nilai, ns.deskripsi_capaian
                FROM nilai_sumatif ns
                JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
                JOIN penempatan_siswa p ON ns.id_penempatan = p.id_penempatan
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE p.id_kelas = ? AND gm.id_mapel = ? AND ps.jenis_sumatif = ? AND ps.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas, s.nama, ps.tanggal_penilaian";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $mapel, $jenis, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Penilaian Sumatif (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Mata Pelajaran', 'Nama Penilaian', 'Jenis Sumatif', 'Nilai', 'Deskripsi Capaian'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['nama_siswa'], $d['nisn'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['nama_mapel'], $d['nama_penilaian'], $d['jenis_sumatif'], $d['nilai'], $d['deskripsi_capaian']];
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_penilaian_sumatif");
}
function laporan_penilaian_sumatif_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $kelas = $_GET['kelas'] ?? '';
    $mapel = $_GET['mapel'] ?? '';
    $jenis = $_GET['jenis'] ?? '';
    $list = [];

    if ($kelas && $mapel && $jenis && $id_ta_tampil) {
        $sql = "SELECT s.nama AS nama_siswa, s.nisn, k.nama_kelas, k.tingkat, m.nama_mapel, ps.nama_penilaian, ps.jenis_sumatif, ns.nilai, ns.deskripsi_capaian
                FROM nilai_sumatif ns
                JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
                JOIN penempatan_siswa p ON ns.id_penempatan = p.id_penempatan
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE p.id_kelas = ? AND gm.id_mapel = ? AND ps.jenis_sumatif = ? AND ps.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas, s.nama, ps.tanggal_penilaian";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kelas, $mapel, $jenis, $id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $judul = "Laporan Penilaian Sumatif (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Mata Pelajaran', 'Nama Penilaian', 'Jenis Sumatif', 'Nilai', 'Deskripsi Capaian'];
    $rows = [];
    $no = 1;
    foreach ($list as $d)
        $rows[] = [$no++, $d['nama_siswa'], $d['nisn'], $d['tingkat'] . '-' . $d['nama_kelas'], $d['nama_mapel'], $d['nama_penilaian'], $d['jenis_sumatif'], $d['nilai'], $d['deskripsi_capaian']];

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN PPDB (BARU) ==============
function laporan_ppdb($pdo)
{
    // Ambil TA Tampil dari SESSION
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $list = [];
    if ($id_ta_tampil) {
        // Ambil data dari pendaftaran_ppdb, JOIN ke siswa untuk mengambil NIPD
        $sql = "SELECT ppdb.*, ppdb.jenis_kelamin AS jk, ppdb.asal_sekolah AS sekolah_asal, s.nipd 
                FROM ppdb_pendaftaran ppdb
                LEFT JOIN siswa s ON ppdb.id_siswa = s.id_siswa
                WHERE ppdb.id_ta = ? AND ppdb.status = 'diterima' 
                ORDER BY ppdb.nama_lengkap ASC"; // Sesuai permintaan (urut alfabet)
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    extract(compact('list', 'id_ta_tampil'));
    include __DIR__ . '/../views/laporan_ppdb.php';
}
function laporan_ppdb_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT ppdb.*, ppdb.jenis_kelamin AS jk, ppdb.asal_sekolah AS sekolah_asal, s.nipd 
                FROM ppdb_pendaftaran ppdb
                LEFT JOIN siswa s ON ppdb.id_siswa = s.id_siswa
                WHERE ppdb.id_ta = ? AND ppdb.status = 'diterima' 
                ORDER BY ppdb.nama_lengkap ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan PPDB Diterima (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'No Pendaftaran', 'Nama Siswa', 'NISN', 'NIPD (Baru)', 'JK', 'Sekolah Asal'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['no_pendaftaran'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['nipd'] ?? 'N/A',
            $d['jk'],
            $d['sekolah_asal']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_ppdb_diterima");
}
function laporan_ppdb_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT ppdb.*, ppdb.jenis_kelamin AS jk, ppdb.asal_sekolah AS sekolah_asal, s.nipd 
                FROM ppdb_pendaftaran ppdb
                LEFT JOIN siswa s ON ppdb.id_siswa = s.id_siswa
                WHERE ppdb.id_ta = ? AND ppdb.status = 'diterima' 
                ORDER BY ppdb.nama_lengkap ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan PPDB Diterima (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'No Pendaftaran', 'Nama Siswa', 'NISN', 'NIPD (Baru)', 'JK', 'Sekolah Asal'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['no_pendaftaran'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['nipd'] ?? 'N/A',
            $d['jk'],
            $d['sekolah_asal']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_ppdb_diterima");
}
function laporan_ppdb_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT ppdb.*, ppdb.jenis_kelamin AS jk, ppdb.asal_sekolah AS sekolah_asal, s.nipd 
                FROM ppdb_pendaftaran ppdb
                LEFT JOIN siswa s ON ppdb.id_siswa = s.id_siswa
                WHERE ppdb.id_ta = ? AND ppdb.status = 'diterima' 
                ORDER BY ppdb.nama_lengkap ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan PPDB Diterima (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'No Pendaftaran', 'Nama Siswa', 'NISN', 'NIPD (Baru)', 'JK', 'Sekolah Asal'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['no_pendaftaran'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['nipd'] ?? 'N/A',
            $d['jk'],
            $d['sekolah_asal']
        ];
    }

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN MUTASI MASUK (BARU) ==============
function laporan_mutasi_masuk($pdo)
{
    // Ambil TA Tampil dari SESSION
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $list = [];
    if ($id_ta_tampil) {
        // Ambil data dari mutasi_masuk, JOIN ke siswa untuk mengambil NIPD
        $sql = "SELECT mm.*, s.nipd 
                FROM mutasi_masuk mm
                LEFT JOIN siswa s ON mm.id_siswa_master = s.id_siswa
                WHERE mm.id_ta = ? AND mm.status_penerimaan = 'Diterima' 
                ORDER BY mm.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    extract(compact('list', 'id_ta_tampil'));
    include __DIR__ . '/../views/laporan_mutasi_masuk.php';
}
function laporan_mutasi_masuk_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT mm.*, s.nipd 
                FROM mutasi_masuk mm
                LEFT JOIN siswa s ON mm.id_siswa_master = s.id_siswa
                WHERE mm.id_ta = ? AND mm.status_penerimaan = 'Diterima' 
                ORDER BY mm.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Mutasi Masuk (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tgl Mutasi', 'Nama Siswa', 'NISN', 'NIPD (Baru)', 'Sekolah Asal', 'Pindah Ke Tingkat'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_mutasi'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['nipd'] ?? 'N/A',
            $d['sekolah_asal'],
            $d['pindah_ke_tingkat']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_masuk");
}
function laporan_mutasi_masuk_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT mm.*, s.nipd 
                FROM mutasi_masuk mm
                LEFT JOIN siswa s ON mm.id_siswa_master = s.id_siswa
                WHERE mm.id_ta = ? AND mm.status_penerimaan = 'Diterima' 
                ORDER BY mm.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Mutasi Masuk (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tgl Mutasi', 'Nama Siswa', 'NISN', 'NIPD (Baru)', 'Sekolah Asal', 'Pindah Ke Tingkat'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_mutasi'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['nipd'] ?? 'N/A',
            $d['sekolah_asal'],
            $d['pindah_ke_tingkat']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_masuk");
}
function laporan_mutasi_masuk_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT mm.*, s.nipd 
                FROM mutasi_masuk mm
                LEFT JOIN siswa s ON mm.id_siswa_master = s.id_siswa
                WHERE mm.id_ta = ? AND mm.status_penerimaan = 'Diterima' 
                ORDER BY mm.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Mutasi Masuk (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Tgl Mutasi', 'Nama Siswa', 'NISN', 'NIPD (Baru)', 'Sekolah Asal', 'Pindah Ke Tingkat'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_mutasi'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['nipd'] ?? 'N/A',
            $d['sekolah_asal'],
            $d['pindah_ke_tingkat']
        ];
    }

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}

// ============ LAPORAN MUTASI KELUAR (BARU) ==============
function laporan_mutasi_keluar($pdo)
{
    // Ambil TA Tampil dari SESSION
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    $list = [];
    if ($id_ta_tampil) {
        // Ambil data dari tabel mutasi_siswa, JOIN ke siswa dan kelas terakhir
        $sql = "SELECT ms.*, s.nama, s.nisn, k.nama_kelas, k.tingkat
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    extract(compact('list', 'id_ta_tampil'));
    include __DIR__ . '/../views/laporan_mutasi_keluar.php';
}
function laporan_mutasi_keluar_export_excel($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT ms.*, s.nama, s.nisn, k.nama_kelas, k.tingkat
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Mutasi Keluar (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tgl Mutasi', 'Nama Siswa', 'NISN', 'Kelas Terakhir', 'Jenis Mutasi', 'Alasan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_mutasi'],
            $d['nama'],
            $d['nisn'],
            $d['tingkat'] . ' - ' . $d['nama_kelas'],
            $d['jenis_mutasi'],
            $d['alasan']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_keluar");
}
function laporan_mutasi_keluar_export_pdf($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT ms.*, s.nama, s.nisn, k.nama_kelas, k.tingkat
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Mutasi Keluar (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kolom = ['No', 'Tgl Mutasi', 'Nama Siswa', 'NISN', 'Kelas Terakhir', 'Jenis Mutasi', 'Alasan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_mutasi'],
            $d['nama'],
            $d['nisn'],
            $d['tingkat'] . ' - ' . $d['nama_kelas'],
            $d['jenis_mutasi'],
            $d['alasan']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_keluar");
}
function laporan_mutasi_keluar_print($pdo)
{
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $list = [];
    if ($id_ta_tampil) {
        $sql = "SELECT ms.*, s.nama, s.nisn, k.nama_kelas, k.tingkat
                FROM mutasi_siswa ms
                JOIN siswa s ON ms.id_siswa = s.id_siswa
                LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ms.id_ta
                LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ms.id_ta = ?
                ORDER BY ms.tanggal_mutasi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_tampil]);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $judul = "Laporan Mutasi Keluar (TA: " . htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') . ")";
    $kop = get_kop_laporan($pdo);
    $kolom = ['No', 'Tgl Mutasi', 'Nama Siswa', 'NISN', 'Kelas Terakhir', 'Jenis Mutasi', 'Alasan'];
    $rows = [];
    $no = 1;
    foreach ($list as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_mutasi'],
            $d['nama'],
            $d['nisn'],
            $d['tingkat'] . ' - ' . $d['nama_kelas'],
            $d['jenis_mutasi'],
            $d['alasan']
        ];
    }

    $data_for_view = ['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop' => $kop];
    extract($data_for_view);
    include __DIR__ . '/../views/laporan_print_preview_generic.php';
}
?>