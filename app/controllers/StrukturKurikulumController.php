<?php
require_once __DIR__ . '/../models/StrukturKurikulumModel.php';
require_once __DIR__ . '/../models/MapelModel.php'; // Untuk list mapel
require_once __DIR__ . '/../models/KelasModel.php';  // Untuk list tingkat

function struktur_kurikulum_index($pdo) {
    if (!check_access('struktur_kurikulum', 'index')) redirect('index.php');
    
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) die("Error: Tahun Ajaran belum diatur.");

    $list_struktur = StrukturKurikulumModel::getAll($pdo, $id_ta_tampil);
    $mapel_list = MapelModel::all($pdo);
    $tingkat_list = ['X', 'XI', 'XII']; // Asumsi tingkat SMA/SMK
    
    extract(compact('list_struktur', 'mapel_list', 'tingkat_list'));
    include __DIR__ . '/../views/struktur_kurikulum_index.php';
}

/**
 * [REVISI] Logika 'save' sekarang memeriksa ID dari URL
 */
function struktur_kurikulum_save($pdo) {
    if (!can_do($pdo, 'struktur_kurikulum', 'create') && !can_do($pdo, 'struktur_kurikulum', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan struktur kurikulum.";
        redirect('index.php?mod=struktur_kurikulum');
        return;
    }
    
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    
    // Ambil ID dari POST (utama) atau URL (fallback)
    $id_struktur = $_POST['id'] ?? $_GET['id'] ?? null; 
    
    try {
        // Kirim $id_struktur ke Model
        StrukturKurikulumModel::save($pdo, $_POST, $id_ta_aktif, $id_struktur);
        
        if ($id_struktur) {
            $_SESSION['pesan_sukses'] = "Struktur kurikulum berhasil diperbarui.";
        } else {
            $_SESSION['pesan_sukses'] = "Struktur kurikulum berhasil disimpan.";
        }
        
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    
    redirect('index.php?mod=struktur_kurikulum');
}

function struktur_kurikulum_delete($pdo, $id) {
    if (!can_do($pdo, 'struktur_kurikulum', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus struktur kurikulum.";
        redirect('index.php?mod=struktur_kurikulum');
        return;
    }
    
    try {
        StrukturKurikulumModel::delete($pdo, $id);
        $_SESSION['pesan_sukses'] = "Struktur kurikulum berhasil dihapus.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    redirect('index.php?mod=struktur_kurikulum');
}