<?php
/**
 * PerangkatController.php
 * Handles Perangkat Pembelajaran (ATP, Modul Ajar, Prosem, Prota)
 */

require_once __DIR__ . '/../models/PerangkatModel.php';

function perangkat_index($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $type = $_GET['type'] ?? 'modul_ajar';
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    // [BYPASS DATA] Admin bisa melihat semua dokumen
    if (in_array(1, $_SESSION['role_ids'] ?? [])) {
        $id_guru = null; // null means all gurus
    }
    
    // Map URL types to DB jenis
    $jenis_map = [
        'atp' => 'ATP',
        'modul_ajar' => 'Modul Ajar',
        'prosem' => 'Prosem',
        'prota' => 'Prota'
    ];
    
    $jenis = $jenis_map[$type] ?? 'Modul Ajar';
    
    $titles = [
        'ATP' => 'Alur Tujuan Pembelajaran (ATP)',
        'Modul Ajar' => 'Modul Ajar / RPP',
        'Prosem' => 'Program Semester',
        'Prota' => 'Program Tahunan'
    ];
    
    $title = $titles[$jenis];
    
    // Get documents for this teacher
    $dokumen_list = PerangkatModel::getAllDocuments($pdo, $id_guru, $id_ta, $jenis);
    
    include __DIR__ . '/../views/perangkat_index.php';
}

function perangkat_form($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $type = $_GET['type'] ?? 'modul_ajar';
    $id_perangkat = $_GET['id'] ?? null;
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    
    // Map URL types to DB jenis
    $jenis_map = [
        'atp' => 'ATP',
        'modul_ajar' => 'Modul Ajar',
        'prosem' => 'Prosem',
        'prota' => 'Prota'
    ];
    
    $jenis = $jenis_map[$type] ?? 'Modul Ajar';
    
    // Get document for editing or prepare for new
    $dokumen = null;
    if ($id_perangkat) {
        $dokumen = PerangkatModel::findDocument($pdo, $id_perangkat);
    }
    
    // Get available templates for this jenis
    $templates = PerangkatModel::getAllTemplates($pdo, $jenis);
    
    include __DIR__ . '/../views/perangkat_form.php';
}

function perangkat_save($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $type = $_POST['type'] ?? 'modul_ajar';
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    
    // Prepare data
    $data = [
        'id_perangkat' => $_POST['id_perangkat'] ?? null,
        'id_guru' => $id_guru,
        'id_ta' => $id_ta,
        'jenis' => $_POST['jenis'],
        'mapel' => $_POST['mapel'] ?? '',
        'kelas' => $_POST['kelas'] ?? '',
        'judul' => $_POST['judul'],
        'konten_html' => $_POST['konten_html']
    ];
    
    try {
        PerangkatModel::saveDocument($pdo, $data);
        $_SESSION['pesan_sukses'] = "Dokumen berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    
    redirect("index.php?mod=perangkat&act=index&type=$type");
}

function perangkat_delete($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $id = $_GET['id'] ?? 0;
    $type = $_GET['type'] ?? 'modul_ajar';
    
    try {
        PerangkatModel::deleteDocument($pdo, $id);
        $_SESSION['pesan_sukses'] = "Dokumen berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    
    redirect("index.php?mod=perangkat&act=index&type=$type");
}

function perangkat_print($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $id = $_GET['id'] ?? 0;
    $dokumen = PerangkatModel::findDocument($pdo, $id);
    
    if (!$dokumen) {
        die("Dokumen tidak ditemukan.");
    }
    
    include __DIR__ . '/../views/perangkat_print.php';
}

// AJAX: Get Template Content
function perangkat_get_template($pdo)
{
    header('Content-Type: application/json');
    
    $id_template = $_GET['id_template'] ?? 0;
    $template = PerangkatModel::findTemplate($pdo, $id_template);
    
    if ($template) {
        echo json_encode(['status' => 'success', 'konten' => $template['konten_html']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Template tidak ditemukan']);
    }
    exit;
}
