<?php
/**
 * KalenderAkademikController.php
 * Controller for Academic Calendar management
 */

require_once __DIR__ . '/../models/KalenderAkademikModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';

function kalender_akademik_index($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    if (!check_access('kalender_akademik')) redirect('index.php');
    
    $id_ta = $_SESSION['id_ta_aktif'] ?? null;
    
    // [PERFORMANCE] Close session early setelah baca data
    close_session_early();
    
    $ta_list = TahunAjaranModel::all($pdo);
    
    // Get filter parameters
    $filter_ta = $_GET['id_ta'] ?? $id_ta;
    $filter_kategori = $_GET['kategori'] ?? '';
    
    // Get events for selected TA
    $events = [];
    if ($filter_ta) {
        $events = KalenderAkademikModel::getAll($pdo, $filter_ta);
    }
    
    $kategori_colors = KalenderAkademikModel::getCategoryColors();
    
    // Check permissions for view logic
    $can_create = can_do($pdo, 'kalender_akademik', 'create');
    $can_update = can_do($pdo, 'kalender_akademik', 'update');
    $can_delete = can_do($pdo, 'kalender_akademik', 'delete');
    
    include __DIR__ . '/../views/kalender_akademik_index.php';
}

function kalender_akademik_save($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $id_kalender = $_POST['id_kalender'] ?? null;
    
    // RBAC Check
    if ($id_kalender) {
        if (!can_do($pdo, 'kalender_akademik', 'update')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki akses untuk mengubah kegiatan.";
            redirect('index.php?mod=kalender_akademik');
        }
    } else {
        if (!can_do($pdo, 'kalender_akademik', 'create')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki akses untuk menambah kegiatan.";
            redirect('index.php?mod=kalender_akademik');
        }
    }
    
    try {
        $data = [
            'id_kalender' => $id_kalender,
            'id_ta' => $_POST['id_ta'],
            'judul_kegiatan' => $_POST['judul_kegiatan'],
            'deskripsi' => $_POST['deskripsi'] ?? null,
            'tanggal_mulai' => $_POST['tanggal_mulai'],
            'tanggal_selesai' => $_POST['tanggal_selesai'],
            'kategori' => $_POST['kategori'],
            'warna' => $_POST['warna'] ?? null,
            'is_recurring' => isset($_POST['is_recurring']) ? 1 : 0,
            'recurring_type' => $_POST['recurring_type'] ?? null
        ];
        
        // Set default color based on category if not provided
        if (empty($data['warna'])) {
            $colors = KalenderAkademikModel::getCategoryColors();
            $data['warna'] = $colors[$data['kategori']] ?? '#3788d8';
        }
        
        KalenderAkademikModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Kegiatan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    
    redirect('index.php?mod=kalender_akademik&id_ta=' . $_POST['id_ta']);
}

function kalender_akademik_delete($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    if (!can_do($pdo, 'kalender_akademik', 'delete')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses untuk menghapus kegiatan.";
        redirect('index.php?mod=kalender_akademik');
    }
    
    $id = $_GET['id'] ?? null;
    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'];
    
    if ($id) {
        try {
            KalenderAkademikModel::delete($pdo, $id);
            $_SESSION['pesan_sukses'] = "Kegiatan berhasil dihapus.";
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
        }
    }
    
    redirect('index.php?mod=kalender_akademik&id_ta=' . $id_ta);
}

function kalender_akademik_api($pdo)
{
    if (!is_logged_in()) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;
    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'];
    
    if (!$start || !$end) {
        echo json_encode(['error' => 'Missing parameters']);
        exit;
    }
    
    $events = KalenderAkademikModel::getByDateRange($pdo, $start, $end, $id_ta);
    
    // Format for FullCalendar
    $formatted_events = [];
    foreach ($events as $event) {
        $formatted_events[] = [
            'id' => $event['id_kalender'],
            'title' => $event['judul_kegiatan'],
            'start' => $event['tanggal_mulai'],
            'end' => date('Y-m-d', strtotime($event['tanggal_selesai'] . ' +1 day')), // FullCalendar end is exclusive
            'backgroundColor' => $event['warna'],
            'borderColor' => $event['warna'],
            'extendedProps' => [
                'deskripsi' => $event['deskripsi'],
                'kategori' => $event['kategori'],
                'is_recurring' => $event['is_recurring']
            ]
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($formatted_events);
    exit;
}
