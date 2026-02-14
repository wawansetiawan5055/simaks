<?php
require_once __DIR__ . '/../models/CpTpModel.php';
require_once __DIR__ . '/../models/MapelModel.php';
require_once __DIR__ . '/../models/PenugasanModel.php'; // Untuk mengambil mapel yg diajar guru
require_once __DIR__ . '/../../vendor/autoload.php'; // Untuk Excel

use PhpOffice\PhpSpreadsheet\IOFactory;

function cp_tp_index($pdo) {
    if (!check_access('manajemen_cp_tp', 'index')) redirect('index.php');
    
    $mapel_list = [];
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array('Kurikulum', $_SESSION['roles'] ?? [])) {
        // Admin & Kurikulum bisa lihat semua mapel
        $mapel_list = MapelModel::all($pdo);
    } elseif (in_array('Guru', $_SESSION['roles'] ?? []) && $id_guru_login) {
        // Guru hanya lihat mapel yang diajar
        $mapel_list = PenugasanModel::getMapelDiajarGuru($pdo, $id_guru_login, $_SESSION['id_ta_aktif']); 
    }

    $id_mapel_filter = $_GET['id_mapel'] ?? ($mapel_list[0]['id_mapel'] ?? 0);
    $fase_filter = $_GET['fase'] ?? 'E';

    $cp_list = [];
    $tp_data = [];
    
    if ($id_mapel_filter) {
        $cp_list = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel_filter, $fase_filter);
        foreach ($cp_list as $cp) {
            $tp_data[$cp['id_cp']] = CpTpModel::getAllTpByCp($pdo, $cp['id_cp']);
        }
    }

    $data_for_view = compact('mapel_list', 'id_mapel_filter', 'fase_filter', 'cp_list', 'tp_data');
    extract($data_for_view);

    include __DIR__ . '/../views/manajemen_cp_tp_index.php';
}

function cp_save($pdo) {
    if (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan CP.";
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
        return;
    }
    // Tambahkan validasi di sini: pastikan guru hanya bisa save untuk mapel yg diajar
    CpTpModel::saveCp($pdo, $_POST);
    $_SESSION['pesan_sukses'] = 'CP berhasil disimpan.';
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
}

function tp_save($pdo) {
    if (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update')) {
         $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan TP.";
         redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
         return;
    }
     // Tambahkan validasi di sini: pastikan guru hanya bisa save untuk mapel yg diajar
    CpTpModel::saveTp($pdo, $_POST);
     $_SESSION['pesan_sukses'] = 'TP berhasil disimpan.';
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
}

function cp_delete($pdo, $id) {
    if (!can_do($pdo, 'manajemen_cp_tp', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus CP.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
     // Tambahkan validasi di sini: pastikan guru hanya bisa delete untuk mapel yg diajar
    CpTpModel::deleteCp($pdo, $id);
     $_SESSION['pesan_sukses'] = 'CP berhasil dihapus.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

function tp_delete($pdo, $id) {
    if (!can_do($pdo, 'manajemen_cp_tp', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus TP.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
     // Tambahkan validasi di sini: pastikan guru hanya bisa delete untuk mapel yg diajar
    CpTpModel::deleteTp($pdo, $id);
     $_SESSION['pesan_sukses'] = 'TP berhasil dihapus.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

function tp_import($pdo) {
    if (!can_do($pdo, 'manajemen_cp_tp', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk import TP.";
        redirect('index.php?mod=manajemen_cp_tp');
        return;
    }
    
    $id_mapel = $_POST['id_mapel'] ?? 0;
    $id_cp = $_POST['id_cp'] ?? 0;
    $fase = $_POST['fase'] ?? 'E';
    
    if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['pesan_error'] = 'File tidak valid atau tidak diunggah.';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }
    
    $file = $_FILES['file_excel']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, ['xls', 'xlsx'])) {
        $_SESSION['pesan_error'] = 'Format file harus .xls atau .xlsx';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }
    
    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        $imported = 0;
        foreach ($rows as $index => $row) {
            // Skip header row if exists
            if ($index === 0 && (strtolower($row[0]) === 'kode tp' || strtolower($row[0]) === 'kode')) {
                continue;
            }
            
            $kode_tp = trim($row[0] ?? '');
            $deskripsi_tp = trim($row[1] ?? '');
            
            if (empty($kode_tp) || empty($deskripsi_tp)) {
                continue; // Skip empty rows
            }
            
            $data = [
                'id_cp' => $id_cp,
                'id_mapel' => $id_mapel,
                'kode_tp' => $kode_tp,
                'deskripsi_tp' => $deskripsi_tp
            ];
            
            CpTpModel::saveTp($pdo, $data);
            $imported++;
        }
        
        $_SESSION['pesan_sukses'] = "Berhasil mengimpor {$imported} TP dari Excel.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = 'Gagal membaca file Excel: ' . $e->getMessage();
    }
    
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
}

function cp_import($pdo) {
    if (!can_do($pdo, 'manajemen_cp_tp', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk import CP.";
        redirect('index.php?mod=manajemen_cp_tp');
        return;
    }
    
    $id_mapel = $_POST['id_mapel'] ?? 0;
    $fase = $_POST['fase'] ?? 'E';
    
    if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['pesan_error'] = 'File tidak valid atau tidak diunggah.';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }
    
    $file = $_FILES['file_excel']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, ['xls', 'xlsx'])) {
        $_SESSION['pesan_error'] = 'Format file harus .xls atau .xlsx';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }
    
    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        $imported_cp = 0;
        $imported_tp = 0;
        $current_cp_id = null;
        $start_row = 0;
        
        // Find the header row (DESKRIPSI CP, KODE TP, DESKRIPSI TP)
        foreach ($rows as $index => $row) {
            $col_a = strtolower(trim($row[0] ?? ''));
            $col_b = strtolower(trim($row[1] ?? ''));
            $col_c = strtolower(trim($row[2] ?? ''));
            
            // Check if this is the header row
            if (strpos($col_a, 'deskripsi cp') !== false || 
                strpos($col_b, 'kode tp') !== false || 
                strpos($col_c, 'deskripsi tp') !== false) {
                $start_row = $index + 1;
                break;
            }
        }
        
        // Process data rows
        for ($i = $start_row; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            $deskripsi_cp = trim($row[0] ?? '');
            $kode_tp = trim($row[1] ?? '');
            $deskripsi_tp = trim($row[2] ?? '');
            
            // Skip completely empty rows
            if (empty($deskripsi_cp) && empty($kode_tp) && empty($deskripsi_tp)) {
                continue;
            }
            
            // Skip rows that look like notes/instructions (e.g., "Keterangan Kode:")
            if (stripos($deskripsi_cp, 'keterangan') !== false) {
                break; // Stop processing when we hit the notes section
            }
            
            // If Column A (Deskripsi CP) has content, this is a new CP
            if (!empty($deskripsi_cp)) {
                $data_cp = [
                    'id_mapel' => $id_mapel,
                    'fase' => $fase,
                    'deskripsi_cp' => $deskripsi_cp
                ];
                
                $current_cp_id = CpTpModel::saveCp($pdo, $data_cp);
                $imported_cp++;
                
                // If this row also has TP data (Kode TP and Deskripsi TP), save it
                if (!empty($kode_tp) && !empty($deskripsi_tp)) {
                    $data_tp = [
                        'id_cp' => $current_cp_id,
                        'id_mapel' => $id_mapel,
                        'kode_tp' => $kode_tp,
                        'deskripsi_tp' => $deskripsi_tp
                    ];
                    
                    CpTpModel::saveTp($pdo, $data_tp);
                    $imported_tp++;
                }
            }
            // If Column A is empty but Columns B & C have content, this is a TP for the current CP
            elseif (!empty($kode_tp) && !empty($deskripsi_tp)) {
                if ($current_cp_id === null) {
                    $_SESSION['pesan_error'] = 'Format file salah: TP ditemukan sebelum CP. Pastikan setiap TP memiliki CP di atasnya.';
                    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
                    return;
                }
                
                $data_tp = [
                    'id_cp' => $current_cp_id,
                    'id_mapel' => $id_mapel,
                    'kode_tp' => $kode_tp,
                    'deskripsi_tp' => $deskripsi_tp
                ];
                
                CpTpModel::saveTp($pdo, $data_tp);
                $imported_tp++;
            }
        }
        
        $_SESSION['pesan_sukses'] = "Berhasil mengimpor {$imported_cp} CP dan {$imported_tp} TP dari Excel.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = 'Gagal membaca file Excel: ' . $e->getMessage();
    }
    
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
}