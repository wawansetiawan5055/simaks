<?php
require_once __DIR__ . '/../models/StrukturKurikulumModel.php';
require_once __DIR__ . '/../models/MapelModel.php'; // Untuk list mapel
require_once __DIR__ . '/../models/KelasModel.php';  // Untuk list tingkat
require_once __DIR__ . '/../models/TahunAjaranModel.php';

function struktur_kurikulum_index($pdo) {
    if (!check_access('struktur_kurikulum', 'index')) redirect('index.php');
    
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) die("Error: Tahun Ajaran belum diatur.");

    $list_struktur = StrukturKurikulumModel::getAll($pdo, $id_ta_tampil);
    $mapel_list = MapelModel::all($pdo);
    $tingkat_list = ['X', 'XI', 'XII']; // Asumsi tingkat SMA/SMK
    $previous_ta = TahunAjaranModel::findPrevious($pdo, $id_ta_tampil);
    $can_import_previous = !empty($previous_ta) && empty($list_struktur);
    
    extract(compact('list_struktur', 'mapel_list', 'tingkat_list', 'previous_ta', 'can_import_previous'));
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

function struktur_kurikulum_import_previous($pdo) {
    if (!can_do($pdo, 'struktur_kurikulum', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menarik struktur kurikulum.";
        redirect('index.php?mod=struktur_kurikulum');
        return;
    }

    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) {
        $_SESSION['pesan_error'] = 'Gagal menarik struktur: Tahun ajaran belum diatur.';
        redirect('index.php?mod=struktur_kurikulum');
        return;
    }

    $current_count = StrukturKurikulumModel::countByTa($pdo, $id_ta_tampil);
    if ($current_count > 0) {
        $_SESSION['pesan_error'] = 'Gagal menarik struktur: TA saat ini sudah memiliki data struktur kurikulum.';
        redirect('index.php?mod=struktur_kurikulum');
        return;
    }

    $previous_ta = TahunAjaranModel::findPrevious($pdo, $id_ta_tampil);
    if (!$previous_ta) {
        $_SESSION['pesan_error'] = 'Gagal menarik struktur: Tidak ditemukan TA sebelumnya.';
        redirect('index.php?mod=struktur_kurikulum');
        return;
    }

    try {
        $copied = StrukturKurikulumModel::copyFromTa($pdo, $previous_ta['id_ta'], $id_ta_tampil);
        if ($copied > 0) {
            $_SESSION['pesan_sukses'] = "Berhasil menarik $copied data struktur dari TA {$previous_ta['nama_ta']} ke TA saat ini.";
        } else {
            $_SESSION['pesan_warning'] = 'Proses selesai, tetapi tidak ada struktur kurikulum yang ditarik dari TA sebelumnya.';
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menarik struktur kurikulum: " . $e->getMessage();
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