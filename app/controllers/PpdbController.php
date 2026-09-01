<?php
/**
 * PpdbController.php - Controller untuk PPDB (Terintegrasi Online & Manual)
 * Updated: Support untuk Excel Import & UI Refinent
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Pastikan path ke model benar
require_once __DIR__ . '/../models/PpdbModel.php';

/**
 * Tampilkan form pendaftaran (Manual entry oleh admin)
 */
function ppdb_form($pdo) {
    if (!can_do($pdo, 'ppdb', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=ppdb&act=index');
        return;
    }
    include __DIR__ . '/../views/ppdb_form.php';
}

/**
 * Simpan data pendaftaran (Manual entry oleh admin)
 */
function ppdb_save($pdo) {
    if (!can_do($pdo, 'ppdb', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=ppdb&act=index');
        return;
    }
    // Ambil data dari POST
    $data = [
        'id_ta' => $_SESSION['id_ta_aktif'],
        'nama_lengkap' => $_POST['nama_lengkap'] ?? null,
        'nisn' => $_POST['nisn'] ?? null,
        'nik' => $_POST['nik'] ?? null,
        'jk' => $_POST['jk'] ?? null,
        'tempat_lahir' => $_POST['tempat_lahir'] ?? null,
        'tanggal_lahir' => $_POST['tanggal_lahir'] ?? null,
        'sekolah_asal' => $_POST['sekolah_asal'] ?? null,
        'jalur_pendaftaran' => $_POST['jalur_pendaftaran'] ?? 'Zonasi',
        'nama_wali' => $_POST['nama_wali'] ?? null,
        'telp_wali' => $_POST['telp_wali'] ?? null,
    ];

    try {
        $result = PpdbModel::save($pdo, $data);
        if ($result) {
            $_SESSION['pesan_sukses'] = "Data pendaftar " . htmlspecialchars($data['nama_lengkap']) . " berhasil disimpan (Manual Entry).";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan data.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Terjadi error: " . $e->getMessage();
    }
    
    redirect('index.php?mod=ppdb&act=index');
}

/**
 * Menampilkan daftar pendaftar untuk verifikasi
 */
function ppdb_index($pdo) {
    if (!check_access('ppdb', 'index')) redirect('index.php');
    // Ambil filter dari GET (jika ada)
    $filter_sumber = $_GET['filter_sumber'] ?? 'all';
    $filter_status = $_GET['filter_status'] ?? 'all';
    
    $filters = [
        'sumber' => $filter_sumber,
        'status' => $filter_status
    ];
    
    // Ambil data dengan filter
    $list_pendaftar = PpdbModel::getAll($pdo, $filters);
    
    // Ambil statistik
    $statistics = PpdbModel::getStatistics($pdo);
    
    extract(compact('list_pendaftar', 'statistics', 'filter_sumber', 'filter_status'));
    include __DIR__ . '/../views/ppdb_index.php';
}

/**
 * Mengubah status pendaftar (diterima/ditolak/pending/diverifikasi)
 */
function ppdb_update_status($pdo) {
    if (!can_do($pdo, 'ppdb', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=ppdb&act=index');
        return;
    }
    $id = $_GET['id'] ?? 0;
    $status = $_GET['status'] ?? 'pending';
    
    try {
        PpdbModel::updateStatus($pdo, $id, $status);
        $_SESSION['pesan_sukses'] = "Status pendaftar berhasil diubah menjadi '" . ucfirst($status) . "'.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal mengubah status: " . $e->getMessage();
    }
    
    redirect('index.php?mod=ppdb&act=index');
}

/**
 * Update catatan verifikasi
 */
function ppdb_update_catatan($pdo) {
    $id = $_POST['id'] ?? 0;
    $catatan = $_POST['catatan'] ?? '';
    
    try {
        PpdbModel::updateCatatan($pdo, $id, $catatan);
        $_SESSION['pesan_sukses'] = "Catatan verifikasi berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan catatan: " . $e->getMessage();
    }
    
    redirect('index.php?mod=ppdb&act=index');
}

/**
 * Detail pendaftar (untuk melihat dokumen & data lengkap)
 */
function ppdb_detail($pdo) {
    $id = $_GET['id'] ?? 0;
    $data_pendaftar = PpdbModel::getById($pdo, $id);
    
    if (!$data_pendaftar) {
        $_SESSION['pesan_error'] = "Data pendaftar tidak ditemukan.";
        redirect('index.php?mod=ppdb&act=index');
        return;
    }
    
    extract(compact('data_pendaftar'));
    include __DIR__ . '/../views/ppdb_detail.php';
}

/**
 * Hapus data pendaftar
 */
function ppdb_delete($pdo) {
    if (!can_do($pdo, 'ppdb', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=ppdb&act=index');
        return;
    }
    $id = $_GET['id'] ?? 0;
    
    try {
        PpdbModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data pendaftar berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus data: " . $e->getMessage();
    }
    
    redirect('index.php?mod=ppdb&act=index');
}

/**
 * Menjalankan proses promosi massal (Generate NIPD & pindah ke tabel siswa)
 */
function ppdb_promote_massal($pdo) {
    try {
        $jumlah = PpdbModel::promoteAlphabeticalBatch($pdo);
        $_SESSION['pesan_sukses'] = "$jumlah siswa berhasil dipromosikan ke Data Master Siswa dengan NIPD urut alfabet.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal mempromosikan siswa: " . $e->getMessage();
    }
    
    redirect('index.php?mod=ppdb&act=index');
}

/**
 * Tampilkan halaman form Re-Generate NIPD Massal
 * GET: index.php?mod=ppdb&act=regenerate_nipd
 */
function ppdb_regenerate_nipd_form($pdo) {
    if (!check_access('ppdb', 'read')) redirect('index.php');

    $list_ta = PpdbModel::getAllTa($pdo);

    // Jika ada id_ta di parameter, tampilkan preview
    $id_ta_dipilih = isset($_GET['id_ta']) ? (int)$_GET['id_ta'] : null;
    $preview_data   = null;
    $error_preview  = null;

    if ($id_ta_dipilih) {
        try {
            $preview_data = PpdbModel::previewRegenerateNipd($pdo, $id_ta_dipilih);
        } catch (Exception $e) {
            $error_preview = $e->getMessage();
        }
    }

    include __DIR__ . '/../views/ppdb_regenerate_nipd.php';
}

/**
 * Eksekusi Re-Generate NIPD Massal (POST)
 * POST: index.php?mod=ppdb&act=regenerate_nipd_exec
 */
function ppdb_regenerate_nipd_exec($pdo) {
    if (!check_access('ppdb', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=ppdb&act=regenerate_nipd');
        return;
    }

    $id_ta = isset($_POST['id_ta']) ? (int)$_POST['id_ta'] : 0;
    if (!$id_ta) {
        $_SESSION['pesan_error'] = "Tahun ajaran tidak valid.";
        redirect('index.php?mod=ppdb&act=regenerate_nipd');
        return;
    }

    try {
        $hasil = PpdbModel::regenerateNipdMassal($pdo, $id_ta);
        $_SESSION['pesan_sukses'] = "✅ Berhasil! NIPD {$hasil['jumlah']} siswa untuk TA \"{$hasil['nama_ta']}\" telah di-regenerate ulang secara urut alfabet.";
        $_SESSION['nipd_regen_preview'] = $hasil['preview']; // Simpan untuk ditampilkan
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal regenerasi NIPD: " . $e->getMessage();
    }

    redirect('index.php?mod=ppdb&act=regenerate_nipd&id_ta=' . $id_ta . '&done=1');
}

/**
 * Download Template Excel untuk Import PPDB
 */
function ppdb_get_template($pdo) {
    if (!check_access('ppdb', 'create')) redirect('index.php');
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Template Import PPDB');
    
    // Header
    $headers = [
        'Nama Lengkap', 'NISN', 'NIK', 'JK (L/P)', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)', 
        'Asal Sekolah', 'Jalur Pendaftaran', 'Nama Wali', 'No HP Wali'
    ];
    
    foreach ($headers as $key => $title) {
        $cell = chr(65 + $key) . '1';
        $sheet->setCellValue($cell, $title);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        // Auto width
        $sheet->getColumnDimension(chr(65 + $key))->setAutoSize(true);
    }
    
    // Example row
    $sheet->setCellValue('A2', 'Budi Santoso');
    $sheet->setCellValue('B2', '1234567890');
    $sheet->setCellValue('C2', '3201010101010001');
    $sheet->setCellValue('D2', 'L');
    $sheet->setCellValue('E2', 'Jakarta');
    $sheet->setCellValue('F2', '2010-05-20');
    $sheet->setCellValue('G2', 'SMPN 1 Jakarta');
    $sheet->setCellValue('H2', 'Zonasi');
    $sheet->setCellValue('I2', 'Agus Santoso');
    $sheet->setCellValue('J2', '081234567890');

    $writer = new Xlsx($spreadsheet);
    
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Template_Import_PPDB.xlsx"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');
    
    $writer->save('php://output');
    exit;
}

/**
 * Handle Import Data PPDB dari Excel
 */
function ppdb_import($pdo) {
    if (!can_do($pdo, 'ppdb', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=ppdb&act=index');
        return;
    }

    if (isset($_FILES['file_excel']['tmp_name']) && $_FILES['file_excel']['tmp_name']) {
        try {
            $file = $_FILES['file_excel']['tmp_name'];
            $spreadsheet = IOFactory::load($file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            
            $list_pendaftar = [];
            
            // Start from row 2 (Skip Header)
            for ($i = 2; $i <= count($sheetData); $i++) {
                $row = $sheetData[$i];
                if (empty($row['A'])) continue;
                
                // Handle Date
                $tgl_lahir = null;
                if (!empty($row['F'])) {
                    if (is_numeric($row['F'])) {
                        $tgl_lahir = Date::excelToDateTimeObject($row['F'])->format('Y-m-d');
                    } else {
                        $tgl_lahir = PpdbModel::normalizeDateForSql($row['F']);
                    }
                }

                // Normalize Gender
                $jk_raw = strtoupper($row['D'] ?? '');
                $jk_normalized = ($jk_raw == 'L' || $jk_raw == 'LAKI-LAKI' || $jk_raw == 'LAKI' || $jk_raw == 'PRIA') ? 'L' : (($jk_raw == 'P' || $jk_raw == 'PEREMPUAN' || $jk_raw == 'WANITA') ? 'P' : 'L');

                $list_pendaftar[] = [
                    'nama_lengkap' => $row['A'],
                    'nisn' => $row['B'] ?? null,
                    'nik' => $row['C'] ?? null,
                    'jk' => $jk_normalized,
                    'tempat_lahir' => $row['E'] ?? null,
                    'tanggal_lahir' => $tgl_lahir,
                    'asal_sekolah' => $row['G'] ?? null,
                    'jalur_pendaftaran' => $row['H'] ?? 'Zonasi',
                    'nama_wali' => $row['I'] ?? null,
                    'no_hp_wali' => $row['J'] ?? null
                ];
            }

            if (empty($list_pendaftar)) {
                $_SESSION['pesan_error'] = "Data Excel kosong atau format tidak sesuai.";
            } else {
                $jumlah = PpdbModel::importBatch($pdo, $list_pendaftar);
                $_SESSION['pesan_sukses'] = "$jumlah data pendaftar berhasil diimpor.";
            }
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Gagal memproses file: " . $e->getMessage();
        }
    } else {
        $_SESSION['pesan_error'] = "Tidak ada file yang diunggah.";
    }

    redirect('index.php?mod=ppdb&act=index');
}
?>