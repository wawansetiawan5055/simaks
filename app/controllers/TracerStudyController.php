<?php
/**
 * TracerStudyController
 * Controller untuk mengelola Study Tracer Alumni
 */
require_once __DIR__ . '/../models/TracerStudyModel.php';
require_once __DIR__ . '/../models/LulusanModel.php';

/**
 * Halaman Index - Daftar Tracer Study
 */
function tracer_study_index($pdo) {
    if (!check_access('tracer_study')) redirect('index.php');
    
    // Get filters from GET
    $filters = [];
    if (!empty($_GET['tahun_lulus'])) {
        $filters['tahun_lulus'] = $_GET['tahun_lulus'];
    }
    if (!empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    
    // Get data
    $tracer_list = TracerStudyModel::getAll($pdo, $filters);
    $available_years = TracerStudyModel::getAvailableYears($pdo);
    
    // Pass to view
    extract(compact('tracer_list', 'available_years', 'filters'));
    include __DIR__ . '/../views/tracer_study_index.php';
}

/**
 * Halaman Form - Input/Edit Tracer Study
 */
function tracer_study_form($pdo) {
    if (!check_access('tracer_study', 'create')) redirect('index.php');
    
    $id = $_GET['id'] ?? 0;
    $tracer_data = null;
    $alumni_list = [];
    
    if ($id) {
        // Edit mode
        $tracer_data = TracerStudyModel::getById($pdo, $id);
        if (!$tracer_data) {
            $_SESSION['pesan_error'] = "Data tracer tidak ditemukan.";
            redirect('index.php?mod=tracer_study');
            return;
        }
    } else {
        // Add mode - get alumni without tracer
        $alumni_list = TracerStudyModel::getAlumniWithoutTracer($pdo);
    }
    
    extract(compact('tracer_data', 'alumni_list'));
    include __DIR__ . '/../views/tracer_study_form.php';
}

/**
 * Action: Save Tracer Study
 */
function tracer_study_save($pdo) {
    if (!check_access('tracer_study', 'save')) redirect('index.php');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php?mod=tracer_study');
        return;
    }
    
    // Validate required fields
    $required = ['id_siswa', 'tahun_lulus', 'status_setelah_lulus'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['pesan_error'] = "Field $field harus diisi!";
            redirect('index.php?mod=tracer_study&act=form');
            return;
        }
    }
    
    // Prepare data
    $data = [
        'id_tracer' => $_POST['id_tracer'] ?? null,
        'id_siswa' => $_POST['id_siswa'],
        'tahun_lulus' => $_POST['tahun_lulus'],
        'status_setelah_lulus' => $_POST['status_setelah_lulus'],
        'nama_institusi' => $_POST['nama_institusi'] ?? null,
        'jurusan_pekerjaan' => $_POST['jurusan_pekerjaan'] ?? null,
        'kota' => $_POST['kota'] ?? null,
        'keterangan' => $_POST['keterangan'] ?? null
    ];
    
    try {
        TracerStudyModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Data tracer study berhasil disimpan!";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan data: " . $e->getMessage();
    }
    
    redirect('index.php?mod=tracer_study');
}

/**
 * Action: Delete Tracer Study
 */
function tracer_study_delete($pdo) {
    if (!check_access('tracer_study', 'delete')) redirect('index.php');
    
    $id = $_GET['id'] ?? 0;
    
    if ($id) {
        try {
            TracerStudyModel::delete($pdo, $id);
            $_SESSION['pesan_sukses'] = "Data tracer study berhasil dihapus!";
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Gagal menghapus data: " . $e->getMessage();
        }
    }
    
    redirect('index.php?mod=tracer_study');
}

/**
 * API: Get Alumni List (AJAX)
 */
function tracer_study_get_alumni($pdo) {
    header('Content-Type: application/json');
    
    $tahun_lulus = $_GET['tahun_lulus'] ?? null;
    $alumni_list = TracerStudyModel::getAlumniWithoutTracer($pdo, $tahun_lulus);
    
    echo json_encode([
        'status' => 'ok',
        'data' => $alumni_list
    ]);
    exit;
}
?>
