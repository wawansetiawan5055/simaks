<?php
require_once __DIR__ . '/../models/GuruModel.php';
require_once __DIR__ . '/../../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

function guru_index($pdo) {
    if (!check_access('guru', 'index')) redirect('index.php');
    $guru_list = GuruModel::all($pdo);
    include __DIR__ . '/../views/guru_index.php';
}

function guru_form($pdo, $id=null) {
    $permission = $id ? 'update' : 'create';
    if (!can_do($pdo, 'guru', $permission)) redirect('index.php?mod=guru');
    
    $guru = $id ? GuruModel::find($pdo, $id) : null;
    include __DIR__ . '/../views/guru_form.php';
}

function guru_save($pdo) {
    $id = $_POST['id_guru'] ?? null;
    $permission = $id ? 'update' : 'create';
    
    if (!can_do($pdo, 'guru', $permission)) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk " . ($id ? "mengubah" : "menambah") . " data.";
        redirect('index.php?mod=guru');
        return;
    }
    
    try {
        GuruModel::save($pdo, $_POST); 
        
        // AUDIT LOG
        if (function_exists('audit_log')) {
            $action = $id ? 'UPDATE' : 'CREATE';
            $desc = $id ? "Mengubah data guru ID $id ({$_POST['nama']})" : "Menambah data guru baru ({$_POST['nama']})";
            audit_log($action, $desc, 'guru', $id);
        }

        $_SESSION['pesan_sukses'] = "Data guru berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    
    redirect('index.php?mod=guru');
}

function guru_delete($pdo, $id) {
    if (!can_do($pdo, 'guru', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=guru');
        return;
    }
    
    try {
        // Ambil nama dulu untuk log sebelum dihapus (Optional, but nice)
        // $old_data = GuruModel::find($pdo, $id); 
        // $nama_guru = $old_data['nama'] ?? 'Unknown';

        GuruModel::delete($pdo, $id);
        
        // AUDIT LOG
        if (function_exists('audit_log')) {
            audit_log('DELETE', "Menghapus data guru ID $id", 'guru', $id);
        }

        $_SESSION['pesan_sukses'] = "Data guru berhasil dihapus.";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
             $_SESSION['pesan_error'] = "Gagal menghapus: Guru ini masih memiliki data terkait (Jadwal/Wali Kelas). Silakan hapus data terkait terlebih dahulu.";
        } else {
             $_SESSION['pesan_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    
    redirect('index.php?mod=guru');
}

function guru_export($pdo) {
    if (!is_logged_in() || !check_access('guru')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=guru');
    }
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set Headers
    $headers = ['ID Guru','Nama','Kode','NUPTK','NIK','JK','Tempat Lahir','Tanggal Lahir','Status','Status Kepegawaian'];
    $sheet->fromArray($headers, null, 'A1');
    
    // Style headers (Bold + Auto width)
    foreach (range('A', 'J') as $col) {
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $is_template = isset($_GET['template']) && $_GET['template'] == '1';
    
    if (!$is_template) {
        // Export actual data
        $data = GuruModel::all($pdo);
        $row = 2;
        foreach ($data as $g) {
            $sheet->fromArray([
                $g['id_guru'], $g['nama'], $g['kode_guru'], 
                $g['nuptk'], $g['nik'], $g['jk'],
                $g['tempat_lahir'], $g['tanggal_lahir'], $g['status'], $g['status_kepegawaian']
            ], null, 'A'.$row++);
        }
    } else {
        // Add example row for template
        $sheet->fromArray([
            '', 'Budi Santoso', 'BS', '1234567890123456', '3201010101010001', 
            'L', 'Jakarta', '1985-05-15', 'Aktif', 'PNS'
        ], null, 'A2');
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="guru_export.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function guru_import($pdo) {
    if (!is_logged_in() || !can_do($pdo, 'guru', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengimpor data guru.";
        redirect('index.php?mod=guru');
    }
    if (isset($_FILES['file_excel']['tmp_name']) && $_FILES['file_excel']['tmp_name']) {
        try {
            $inputFileName = $_FILES['file_excel']['tmp_name'];
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            
            for ($i = 2; $i <= count($sheetData); $i++) { 
                $row = $sheetData[$i];
                
                // ⭐ REVISI: Menambahkan 'kode_guru'
                // Asumsi 'Kode' ada di kolom C, dan sisanya bergeser
                GuruModel::save($pdo, [
                    'nama' => $row['B'] ?? '', 
                    'kode_guru' => $row['C'] ?? '', // <-- Kolom Baru
                    'nuptk' => $row['D'] ?? '', 
                    'nik' => $row['E'] ?? '', 
                    'jk' => $row['F'] ?? '',
                    'tempat_lahir' => $row['G'] ?? '', 
                    'tanggal_lahir' => !empty($row['H']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['H'])->format('Y-m-d') : null, 
                    'status' => $row['I'] ?? 'Aktif', 
                    'status_kepegawaian' => $row['J'] ?? ''
                ]);
            }
            
            // AUDIT LOG
            if (function_exists('audit_log')) {
                $count = count($sheetData) - 1; // Minus header
                audit_log('IMPORT', "Mengimpor $count data guru dari Excel", 'guru', 0);
            }

             $_SESSION['pesan_sukses'] = "Data guru berhasil diimpor.";
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
             $_SESSION['pesan_error'] = 'Error loading file: '.$e->getMessage();
        } catch (Exception $e) {
             $_SESSION['pesan_error'] = 'Error importing data: '.$e->getMessage();
        }
    } else {
        $_SESSION['pesan_error'] = 'Tidak ada file yang diunggah atau terjadi kesalahan.';
    }
    redirect('index.php?mod=guru');
}
?>