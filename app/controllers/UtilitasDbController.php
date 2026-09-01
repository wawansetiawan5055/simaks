<?php
// app/controllers/UtilitasDbController.php
require_once __DIR__ . '/../models/UtilitasDbModel.php'; 

function utilitas_db_index($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');
    
    // List of tables for the "All Tables" view
    $tables = UtilitasDbModel::getAllTables($pdo);

    // List of SQL patches & applied status
    $available_patches = UtilitasDbModel::getAvailablePatches();
    $applied_patches = UtilitasDbModel::getAppliedPatches($pdo);

    include __DIR__ . '/../views/utilitas_db_index.php';
}

/**
 * Aksi: Menjalankan Raw SQL Query
 */
function utilitas_db_run_sql($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    $sql = $_POST['sql_query'] ?? '';
    
    if (empty(trim($sql))) {
        $_SESSION['pesan_error'] = "Query SQL tidak boleh kosong.";
        redirect(BASE_URL . 'utilitas_db');
    }

    $result = UtilitasDbModel::executeRawSql($pdo, $sql);

    if ($result['success']) {
        if ($result['type'] == 'result') {
            $_SESSION['sql_result'] = $result['data'];
            $_SESSION['pesan_sukses'] = "Query berhasil dijalankan. Ditampilkan " . count($result['data']) . " baris.";
        } else {
            $_SESSION['pesan_sukses'] = "Query berhasil dijalankan. " . $result['rows_affected'] . " baris terpengaruh.";
        }
    } else {
        $_SESSION['pesan_error'] = "Error SQL: " . $result['error'];
    }
    
    $_SESSION['last_query'] = $sql;
    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Membuat dan mengunduh backup .sql secara native via PDO Stream (100% Reliabel)
 */
function utilitas_db_backup($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');
    
    $type = $_GET['type'] ?? 'full';
    $selected_tables = null;
    
    if (isset($_POST['backup_selected']) && !empty($_POST['selected_tables'])) {
        $selected_tables = $_POST['selected_tables'];
        $type = 'selected';
    }

    $filename = "backup_simaks_{$type}_" . date("Y-m-d_H-i-s") . ".sql";
    
    // Clear any previous output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    set_time_limit(300);
    UtilitasDbModel::streamSqlBackup($pdo, $type, $selected_tables);
    exit;
}

/**
 * Aksi: Restore database dari file .sql
 */
function utilitas_db_restore($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
        $filePath = $_FILES['backup_file']['tmp_name'];
        $fileExt = strtolower(pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION));
        
        if ($fileExt === 'sql') {
            $sql = file_get_contents($filePath);
            
            if ($sql === false || trim($sql) === '') {
                $_SESSION['pesan_error'] = "File SQL kosong atau gagal dibaca.";
                redirect(BASE_URL . 'utilitas_db');
            }

            set_time_limit(300); 
            try {
                UtilitasDbModel::restoreDatabase($pdo, $sql);
                $_SESSION['pesan_sukses'] = "Database berhasil di-restore!";
            } catch (Exception $e) {
                $_SESSION['pesan_error'] = "Restore Gagal: " . $e->getMessage();
            }
        } else {
            $_SESSION['pesan_error'] = "File tidak valid. Harap unggah file berekstensi .sql.";
        }
    } else {
        $_SESSION['pesan_error'] = "Gagal mengunggah file backup.";
    }
    
    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Optimasi & Periksa Seluruh Tabel Database
 */
function utilitas_db_optimize($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    try {
        $results = UtilitasDbModel::optimizeAndCheckTables($pdo);
        $_SESSION['pesan_sukses'] = "✅ Berhasil melakukan optimasi & defragmentasi pada " . count($results) . " tabel database!";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal melakukan optimasi database: " . $e->getMessage();
    }

    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Hapus data histori (TRUNCATE)
 */
function utilitas_db_hapus_histori($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    if (empty($_POST['tabel_histori'])) {
        $_SESSION['pesan_error'] = "Tidak ada tabel histori yang dipilih untuk dihapus.";
        redirect(BASE_URL . 'utilitas_db');
    }

    $tabel_histori_terpilih = $_POST['tabel_histori'];

    try {
        $results = UtilitasDbModel::hapusHistori($pdo, $tabel_histori_terpilih);
        
        if (!empty($results['sukses'])) {
            $_SESSION['pesan_sukses'] = implode('<br>', $results['sukses']);
        }
        if (!empty($results['error'])) {
            $_SESSION['pesan_error'] = implode('<br>', $results['error']);
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus histori: " . $e->getMessage();
    }
    
    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Hapus data konfigurasi/setup (TRUNCATE)
 */
function utilitas_db_hapus_setup($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    if (empty($_POST['tabel_setup'])) {
        $_SESSION['pesan_error'] = "Tidak ada tabel setup yang dipilih untuk dihapus.";
        redirect(BASE_URL . 'utilitas_db');
    }

    $tabel_setup_terpilih = $_POST['tabel_setup'];

    try {
        $results = UtilitasDbModel::hapusSetup($pdo, $tabel_setup_terpilih);
        
        if (!empty($results['sukses'])) {
            $_SESSION['pesan_sukses'] = "Berhasil menghapus data konfigurasi:<br>" . implode('<br>', $results['sukses']);
        }
        if (!empty($results['error'])) {
            $_SESSION['pesan_error'] = implode('<br>', $results['error']);
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus setup: " . $e->getMessage();
    }
    
    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Hapus data terpilih (TRUNCATE) dari Checkbox List Global
 */
function utilitas_db_truncate_selected($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    if (empty($_POST['selected_tables'])) {
        $_SESSION['pesan_error'] = "Tidak ada tabel yang dipilih untuk dikosongkan.";
        redirect(BASE_URL . 'utilitas_db');
    }

    $selected_tables = $_POST['selected_tables'];
    $pesan_sukses = [];
    $pesan_error = [];

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    
    foreach ($selected_tables as $table) {
        try {
            $pdo->exec("TRUNCATE TABLE `{$table}`");
            $pesan_sukses[] = "✅ Tabel '{$table}' dikosongkan.";
        } catch (Exception $e) {
            $pesan_error[] = "❌ Gagal '{$table}': " . $e->getMessage();
        }
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    if (!empty($pesan_sukses)) {
        $_SESSION['pesan_sukses'] = implode('<br>', $pesan_sukses);
    }
    if (!empty($pesan_error)) {
        $_SESSION['pesan_error'] = implode('<br>', $pesan_error);
    }

    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Reset aplikasi (TRUNCATE) - SINKRON
 */
function utilitas_db_reset_aplikasi($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    // Daftar semua tabel Anak/Transaksi/Relasi (TRUNCATE DULU)
    $tabel_anak = [
        'absensi_mapel', 'absensi_siswa_mapel', 'absensi_piket', 'absensi_siswa_piket', 'absensi_guru', 
        'catatan_kasus', 'catatan_kelas', 'jurnal_kbm', 'penempatan_siswa', 'penugasan_wali_kelas', 
        'jadwal_mengajar', 'jadwal', 'nilai_sumatif', 'penilaian_sumatif', 'tujuan_pembelajaran', 
        'capaian_pembelajaran', 'struktur_kurikulum', 'guru_mapel', 'penugasan_guru', 'cbt_peserta',
        'cbt_jawaban', 'cbt_nilai', 'lms_pengumpulan', 'lms_tugas_progress', 'lms_materi_progress'
    ];
    
    // Daftar semua tabel Induk/Master (TRUNCATE AKHIR)
    $tabel_induk = [
        'siswa', 'guru', 'kelas', 'mapel', 'tahun_ajaran', 'master_jam', 'master_kegiatan', 'pengguna'
    ];
    
    set_time_limit(300); 

    try {
        UtilitasDbModel::resetAplikasi($pdo, $tabel_anak, $tabel_induk);
        $_SESSION['pesan_sukses'] = "APLIKASI BERHASIL DIRESET! Semua data telah dikosongkan dan ID di-reset ke 1.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal me-reset aplikasi: " . $e->getMessage();
    }
    
    redirect(BASE_URL . 'utilitas_db');
}

/**
 * Aksi: Menjalankan file patch SQL yang dipilih
 */
function utilitas_db_run_patch($pdo) {
    if (!has_role(['Admin'])) redirect(BASE_URL . 'dashboard');

    $filename = $_POST['patch_filename'] ?? '';
    if (empty($filename)) {
        $_SESSION['pesan_error'] = "File patch belum dipilih.";
        redirect(BASE_URL . 'utilitas_db#patch-section');
    }

    $username = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Admin';
    $result = UtilitasDbModel::executePatch($pdo, $filename, $username);

    if ($result['success']) {
        $_SESSION['pesan_sukses'] = $result['message'];
    } else {
        $_SESSION['pesan_error'] = $result['message'];
    }

    redirect(BASE_URL . 'utilitas_db#patch-section');
}