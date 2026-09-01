<?php
/**
 * PerangkatUploadController.php
 * Handles File Uploads for Perangkat KBM (ATP, Modul, Prosem, Prota)
 */

require_once __DIR__ . '/../models/PerangkatModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';

function perangkat_upload_index($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    // Default filters
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    // Admin/Kurikulum see all
    if (in_array(1, $_SESSION['role_ids'] ?? []) || check_access('manajemen_perangkat', 'view_all')) {
        // Optional: Allow filter by guru if admin
        // For now, let's stick to simple logic: Admin sees all or selects guru
    } else {
        // Teacher sees their own
    }

    // Handle Filters from UI
    $filter_ta = $_GET['ta'] ?? $id_ta;
    $filter_jenis = $_GET['jenis'] ?? '';
    $filter_mapel = $_GET['mapel'] ?? '';

    // Get documents with multi-filter
    $dokumen_list = PerangkatModel::getAllUploads($pdo, $id_guru, $filter_ta, $filter_jenis, $filter_mapel);

    // Get Mata Pelajaran for dropdown (Subject to teacher assignment)
    require_once __DIR__ . '/../models/PenugasanModel.php';
    if ($id_guru) {
        $mapel_list = PenugasanModel::getMapelDiajarGuru($pdo, $id_guru, $filter_ta);
    } else {
        require_once __DIR__ . '/../models/MapelModel.php';
        $mapel_list = MapelModel::all($pdo);
    }

    // Get related TA for cloning options
    $related_ta = PerangkatModel::getRelatedTA($pdo, $id_ta);

    // Get ALL TA for filter
    $all_ta = TahunAjaranModel::all($pdo);

    include __DIR__ . '/../views/perangkat_upload_index.php';
}

function perangkat_upload_save($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id_guru = $_SESSION['id_guru_terkait'] ?? null;
    if ($id_guru == 0) $id_guru = null;
    
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    // Validate Input
    $jenis = $_POST['jenis'] ?? '';
    $mapel = $_POST['mapel'] ?? '';
    $kelas = $_POST['kelas'] ?? '';
    $judul = $_POST['judul'] ?? ''; // Optional, or auto-generated from filename

    if (empty($jenis) || empty($mapel) || empty($kelas)) {
        $_SESSION['pesan_error'] = "Mohon lengkapi semua field wajib (Jenis, Mapel, Kelas)";
        redirect('index.php?mod=perangkat_upload');
        exit;
    }

    // Handle File Upload
    if (!isset($_FILES['file_perangkat']) || $_FILES['file_perangkat']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['pesan_error'] = "File wajib diupload.";
        redirect('index.php?mod=perangkat_upload');
        exit;
    }

    $file = $_FILES['file_perangkat'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4', 'webm', 'ogg'];

    if (!in_array($ext, $allowed)) {
        $_SESSION['pesan_error'] = "Format file tidak diizinkan. Gunakan PDF, Word, Excel, PowerPoint, atau Video (MP4).";
        redirect('index.php?mod=perangkat_upload');
        exit;
    }

    // File Size Check (e.g. 50MB for video support)
    if ($file['size'] > 50 * 1024 * 1024) {
        $_SESSION['pesan_error'] = "Ukuran file maksimal 50MB.";
        redirect('index.php?mod=perangkat_upload');
        exit;
    }

    // Generate Filename
    $filename = uniqid('doc_') . '.' . $ext;
    $upload_dir = 'uploads/perangkat/';

    // Ensure dir exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $destination = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Save to DB
        $data = [
            'id_guru' => $id_guru,
            'id_ta' => $id_ta,
            'jenis' => $jenis,
            'mapel' => $mapel,
            'kelas' => $kelas,
            'judul' => $judul ?: $file['name'], // Use filename if title empty
            'file_path' => $destination,
            'file_name' => $file['name'],
            'tipe_file' => $ext,
            'ukuran_file' => $file['size']
        ];

        PerangkatModel::saveUpload($pdo, $data);

        // Check for apply to all semester (Option 3)
        $apply_all = $_POST['apply_all_semester'] ?? 0;
        if ($apply_all) {
            $related_ta = PerangkatModel::getRelatedTA($pdo, $id_ta);
            foreach ($related_ta as $ta) {
                $data['id_ta'] = $ta['id_ta'];
                $data['is_reused'] = 1;
                // Note: No source_perangkat_id yet because the first record ID isn't returned in simple saveUpload
                // but we could get it if needed. For bulk upload, it's just 'cloned'.
                PerangkatModel::saveUpload($pdo, $data);
            }
        }

        $_SESSION['pesan_sukses'] = "Dokumen berhasil diupload.";
    } else {
        $_SESSION['pesan_error'] = "Gagal mengupload file ke server.";
    }

    redirect('index.php?mod=perangkat_upload');
}

function perangkat_upload_update($pdo)
{
    // Update Metadata Only (or replace file if needed - future dev)
    if (!is_logged_in())
        redirect('index.php');

    $id = $_POST['id_perangkat'];

    // Check ownership
    // ...

    $data = [
        'id_perangkat' => $id,
        'judul' => $_POST['judul'],
        'mapel' => $_POST['mapel'],
        'kelas' => $_POST['kelas'],
        'jenis' => $_POST['jenis']
    ];

    // Handle File Replacement if uploaded
    if (isset($_FILES['file_perangkat']) && $_FILES['file_perangkat']['error'] === UPLOAD_ERR_OK) {
        // Logic similar to save, but update path & delete old file
        $file = $_FILES['file_perangkat'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // ... validate ...

        $filename = uniqid('doc_') . '.' . $ext;
        $upload_dir = 'uploads/perangkat/';
        $destination = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Delete old file
            $old_doc = PerangkatModel::findDocument($pdo, $id);
            if ($old_doc && file_exists($old_doc['file_path'])) {
                unlink($old_doc['file_path']);
            }

            $data['file_path'] = $destination;
            $data['file_name'] = $file['name'];
            $data['tipe_file'] = $ext;
            $data['ukuran_file'] = $file['size'];
        }
    }

    PerangkatModel::updateUpload($pdo, $data);
    $_SESSION['pesan_sukses'] = "Data dokumen berhasil diperbarui.";
    redirect('index.php?mod=perangkat_upload');
}


function perangkat_upload_delete($pdo)
{
    if (!is_logged_in())
        redirect('index.php');

    $id = $_GET['id'] ?? 0;

    // Get info to delete file
    $doc = PerangkatModel::findDocument($pdo, $id);

    if ($doc) {
        // Verify ownership (if not admin)
        // ...

        if (!empty($doc['file_path']) && file_exists($doc['file_path'])) {
            unlink($doc['file_path']);
        }

        PerangkatModel::deleteDocument($pdo, $id);
        $_SESSION['pesan_sukses'] = "Dokumen berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Dokumen tidak ditemukan.";
    }

    redirect('index.php?mod=perangkat_upload');
}

function perangkat_upload_clone($pdo)
{
    if (!is_logged_in()) redirect('index.php');

    $id_perangkat = $_POST['id_perangkat'] ?? 0;
    $target_id_ta = $_POST['target_id_ta'] ?? 0;

    if (!$id_perangkat || !$target_id_ta) {
        $_SESSION['pesan_error'] = "ID Perangkat atau Semester target tidak valid.";
        redirect('index.php?mod=perangkat_upload');
        exit;
    }

    $success = PerangkatModel::duplicateDocument($pdo, $id_perangkat, $target_id_ta);

    if ($success) {
        $_SESSION['pesan_sukses'] = "Dokumen berhasil disalin ke semester tujuan.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menyalin dokumen.";
    }

    redirect('index.php?mod=perangkat_upload');
}

function perangkat_upload_bulk_clone($pdo)
{
    if (!is_logged_in()) redirect('index.php');

    $ids = $_POST['ids'] ?? [];
    $target_id_ta = $_POST['target_id_ta'] ?? 0;

    if (empty($ids) || !$target_id_ta) {
        $_SESSION['pesan_error'] = "Pilih dokumen dan semester target terlebih dahulu.";
        redirect('index.php?mod=perangkat_upload');
        exit;
    }

    $success_count = 0;
    foreach ($ids as $id) {
        if (PerangkatModel::duplicateDocument($pdo, $id, $target_id_ta)) {
            $success_count++;
        }
    }

    if ($success_count > 0) {
        $_SESSION['pesan_sukses'] = "$success_count dokumen berhasil disalin.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menyalin dokumen.";
    }

    redirect('index.php?mod=perangkat_upload');
}
