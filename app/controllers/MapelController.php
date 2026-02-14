<?php
require_once __DIR__ . '/../models/MapelModel.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Diperlukan untuk export/import

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

function mapel_index($pdo) {
    if (!check_access('mapel', 'index')) redirect('index.php');
    
    $mapel_list = MapelModel::all($pdo); 
    
    include __DIR__ . '/../views/mapel_index.php'; 
}

function mapel_form($pdo, $id=null) {
    $permission = $id ? 'update' : 'create';
    if (!can_do($pdo, 'mapel', $permission)) redirect('index.php?mod=mapel');

    $mapel = $id ? MapelModel::find($pdo, $id) : null;
    include __DIR__ . '/../views/mapel_form.php';
}

function mapel_save($pdo) {
    $id = $_POST['id_mapel'] ?? null;
    $permission = $id ? 'update' : 'create';

    if (!can_do($pdo, 'mapel', $permission)) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk " . ($id ? "mengubah" : "menambah") . " data.";
        redirect('index.php?mod=mapel');
        return;
    }
    
    try {
        // ⭐ REVISI: Pastikan Model Anda (MapelModel.php)
        // sekarang juga menangani 'kode_mapel' dan 'urutan' dari $_POST
        MapelModel::save($pdo, $_POST); 
        $_SESSION['pesan_sukses'] = "Data mata pelajaran berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    
    redirect('index.php?mod=mapel');
}

function mapel_delete($pdo, $id) {
    if (!can_do($pdo, 'mapel', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=mapel');
        return;
    }

    try {
        MapelModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Data mata pelajaran berhasil dihapus.";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
             $_SESSION['pesan_error'] = "Gagal menghapus: Mata pelajaran ini masih digunakan dalam jadwal atau nilai. Silakan hapus data terkait terlebih dahulu.";
        } else {
             $_SESSION['pesan_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=mapel');
}

/**
 * ⭐ BARU: Fungsi Ekspor Mapel
 */
function mapel_export($pdo) {
    if (!is_logged_in() || !check_access('mapel')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=mapel');
    }
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Header Columns
    $sheet->fromArray(['ID Mapel','Nama Mapel','Kode','Kategori','KKTP','Urutan'], null, 'A1');
    
    // Style headers (Bold + Auto width)
    foreach (range('A', 'F') as $col) {
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $is_template = isset($_GET['template']) && $_GET['template'] == '1';
    
    if (!$is_template) {
        $data = MapelModel::all($pdo); // Ambil data (sudah diurutkan)
        $row = 2;
        foreach ($data as $m) {
            $sheet->fromArray([
                $m['id_mapel'], $m['nama_mapel'], $m['kode_mapel'], $m['kategori_mapel'],
                $m['kktp'], $m['urutan']
            ], null, 'A'.$row++);
        }
        $filename = "mapel_export_" . date('Y-m-d') . ".xlsx";
    } else {
        // Example Row for Template
        $sheet->fromArray([
            '', 'Matematika Wajib', 'MTK-W', 'Umum', '75', '1'
        ], null, 'A2');
        $filename = "template_mapel.xlsx";
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

/**
 * ⭐ BARU: Fungsi Import Mapel
 */
function mapel_import($pdo) {
    if (!is_logged_in() || !can_do($pdo, 'mapel', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengimpor data mapel.";
        redirect('index.php?mod=mapel');
    }
    if (isset($_FILES['file_excel']['tmp_name']) && $_FILES['file_excel']['tmp_name']) {
        try {
            $inputFileName = $_FILES['file_excel']['tmp_name'];
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            
            for ($i = 2; $i <= count($sheetData); $i++) { 
                $row = $sheetData[$i];
                MapelModel::save($pdo, [
                    // Asumsi: B=Nama, C=Kode, D=Kategori, E=KKTP, F=Urutan
                    'nama_mapel' => $row['B'] ?? '', 
                    'kode_mapel' => $row['C'] ?? '',
                    'kategori_mapel' => $row['D'] ?? '',
                    'kktp' => $row['E'] ?? 75,
                    'urutan' => $row['F'] ?? 0
                ]);
            }
             $_SESSION['pesan_sukses'] = "Data mapel berhasil diimpor.";
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
             $_SESSION['pesan_error'] = 'Error loading file: '.$e->getMessage();
        } catch (Exception $e) {
             $_SESSION['pesan_error'] = 'Error importing data: '.$e->getMessage();
        }
    } else {
        $_SESSION['pesan_error'] = 'Tidak ada file yang diunggah atau terjadi kesalahan.';
    }
    redirect('index.php?mod=mapel');
}
function mapel_update_urutan($pdo) {
    if (!can_do($pdo, 'mapel', 'update')) {
        // Kirim response error jika tidak ada hak akses
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk mengubah urutan.']);
        exit;
    }

    // Ambil data urutan yang dikirim oleh JavaScript
    $urutan_ids = $_POST['urutan'] ?? [];

    if (empty($urutan_ids)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data urutan.']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        
        $sql = "UPDATE mapel SET urutan = ? WHERE id_mapel = ?";
        $stmt = $pdo->prepare($sql);
        
        // Loop sebanyak ID yang dikirim dan update urutannya
        // (index + 1) akan menjadi nomor urut baru (1, 2, 3, ...)
        foreach ($urutan_ids as $index => $id_mapel) {
            $stmt->execute([$index + 1, $id_mapel]);
        }
        
        $pdo->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Urutan berhasil diperbarui.']);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>