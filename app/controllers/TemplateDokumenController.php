<?php
/**
 * TemplateDokumenController.php
 * Admin interface for managing document templates
 */

require_once __DIR__ . '/../models/PerangkatModel.php';

function template_dokumen_index($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    // Use dynamic access control instead of hardcoded admin check
    if (!check_access('template_dokumen')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=dashboard');
    }
    
    // Get all templates (including inactive ones for admin)
    $template_list = PerangkatModel::getAllTemplates($pdo);
    
    include __DIR__ . '/../views/template_dokumen_index.php';
}

function template_dokumen_save($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    // Use dynamic access control
    if (!can_do($pdo, 'template_dokumen', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menyimpan template.";
        redirect('index.php?mod=template_dokumen&act=index');
    }
    
    $data = [
        'id_template' => $_POST['id_template'] ?? null,
        'jenis' => $_POST['jenis'],
        'nama_template' => $_POST['nama_template'],
        'konten_html' => $_POST['konten_html'],
        'is_active' => $_POST['is_active'] ?? 1
    ];
    
    try {
        PerangkatModel::saveTemplate($pdo, $data);
        $_SESSION['pesan_sukses'] = "Template berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    
    redirect('index.php?mod=template_dokumen&act=index');
}

function template_dokumen_delete($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    // Use dynamic access control
    if (!can_do($pdo, 'template_dokumen', 'delete')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menghapus template.";
        redirect('index.php?mod=template_dokumen&act=index');
    }
    
    $id = $_GET['id'] ?? 0;
    
    try {
        PerangkatModel::deleteTemplate($pdo, $id);
        $_SESSION['pesan_sukses'] = "Template berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    
    redirect('index.php?mod=template_dokumen&act=index');
}
