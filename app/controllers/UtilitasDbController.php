<?php
// app/controllers/UtilitasDbController.php
require_once __DIR__ . '/../models/UtilitasDbModel.php'; 

function utilitas_db_index($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');
    
    // List of tables for the "All Tables" view
    $tables = UtilitasDbModel::getAllTables($pdo);

    include __DIR__ . '/../views/utilitas_db_index.php';
}

/**
 * Aksi: Menjalankan Raw SQL Query
 */
function utilitas_db_run_sql($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');

    $sql = $_POST['sql_query'] ?? '';
    
    if (empty(trim($sql))) {
        $_SESSION['pesan_error'] = "Query SQL tidak boleh kosong.";
        redirect('index.php?mod=utilitas_db');
    }

    // Protect against accidentally destructive commands if desired, 
    // but user requested "Insert Data" which implies modification.
    // We already warned user in UI about risk.

    $result = UtilitasDbModel::executeRawSql($pdo, $sql);

    if ($result['success']) {
        if ($result['type'] == 'result') {
            // Store result in session to display in view (simple way)
            $_SESSION['sql_result'] = $result['data'];
            $_SESSION['pesan_sukses'] = "Query berhasil dijalankan. Ditampilkan " . count($result['data']) . " baris.";
        } else {
             $_SESSION['pesan_sukses'] = "Query berhasil dijalankan. " . $result['rows_affected'] . " baris terpengaruh.";
        }
    } else {
        $_SESSION['pesan_error'] = "Error SQL: " . $result['error'];
    }
    
    // Pass back the query so user can edit it if error
    $_SESSION['last_query'] = $sql;
    
    redirect('index.php?mod=utilitas_db');
}


/**
 * Aksi: Membuat dan mengunduh backup .sql dengan opsi type
 */
function utilitas_db_backup($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');
    
    global $host, $dbname, $user, $pass; 

    $db_host = $host ?? 'localhost';
    $db_name = $dbname ?? 'db_simaks';
    $db_user = $user ?? 'root';
    $db_pass = $pass ?? '';
    
    // Ambil type dari query string (default: full)
    $type = $_GET['type'] ?? 'full';
    
    $type_label = '';
    $mysqldump_options = '';
    
    switch ($type) {
        case 'structure':
            $type_label = 'structure';
            $mysqldump_options = '--no-data'; // Struktur saja, tanpa data
            break;
        case 'data':
            $type_label = 'data';
            $mysqldump_options = '--no-create-info'; // Data saja, tanpa CREATE TABLE
            break;
        case 'full':
        default:
            $type_label = 'full';
            $mysqldump_options = ''; // Default: struktur + data
            break;
    }
    
    $filename = "backup_simaks_{$type_label}_" . date("Y-m-d_H-i-s") . ".sql";
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Check if coming from "Backup Selected" checkbox form
    if (isset($_POST['backup_selected']) && !empty($_POST['selected_tables'])) {
        $tables = $_POST['selected_tables'];
        $tables_arg = implode(' ', $tables);
        $mysqldump_options .= " --tables $tables_arg";
        $type_label = "selected";
    }

    $filename = "backup_simaks_{$type_label}_" . date("Y-m-d_H-i-s") . ".sql";
    
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $command = "mysqldump {$mysqldump_options} --host={$db_host} --user={$db_user} --password={$db_pass} {$db_name}";
    
    // If selecting tables, append them to command (NOTE: handled above in options if logic matches)
    // Actually, mysqldump syntax is: mysqldump [options] db_name [tbl_name ...]
    // So we need to restructure $command a bit if tables are present.
    
    if (isset($tables) && is_array($tables)) {
         $command = "mysqldump {$mysqldump_options} --host={$db_host} --user={$db_user} --password={$db_pass} {$db_name} " . implode(' ', $tables);
    } else {
         $command = "mysqldump {$mysqldump_options} --host={$db_host} --user={$db_user} --password={$db_pass} {$db_name}";
    }
    
    set_time_limit(300); 

    passthru($command, $return_var);
    
    if ($return_var !== 0) {
        // Only verify/echo error if headers not sent? Actually PASSTHRU sends output directly.
        // If it fails, likely empty file.
    }
    exit;
}

/**
 * Aksi: Restore database dari file .sql
 */
function utilitas_db_restore($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');

    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
        $filePath = $_FILES['backup_file']['tmp_name'];
        $fileMime = mime_content_type($filePath);
        
        if ($fileMime == 'text/plain' || $fileMime == 'application/sql' || $fileMime == 'application/octet-stream') {
            
            $sql = file_get_contents($filePath);
            
            if ($sql === false) {
                 $_SESSION['pesan_error'] = "Gagal membaca file SQL yang diunggah.";
                 redirect('index.php?mod=utilitas_db');
            }

            set_time_limit(300); 
            
            try {
                UtilitasDbModel::restoreDatabase($pdo, $sql);
                $_SESSION['pesan_sukses'] = "Database berhasil di-restore!";
            } catch (Exception $e) {
                $_SESSION['pesan_error'] = "Restore Gagal: " . $e->getMessage();
            }
            
        } else {
            $_SESSION['pesan_error'] = "File tidak valid. Harap unggah file .sql.";
        }
    } else {
        $_SESSION['pesan_error'] = "Gagal mengunggah file. Error code: " . ($_FILES['backup_file']['error'] ?? 'Tidak ada file');
    }
    
    redirect('index.php?mod=utilitas_db');
}

/**
 * Aksi: Hapus data histori (TRUNCATE)
 */
function utilitas_db_hapus_histori($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');

    if (empty($_POST['tabel_histori'])) {
        $_SESSION['pesan_error'] = "Tidak ada tabel histori yang dipilih untuk dihapus.";
        redirect('index.php?mod=utilitas_db');
    }

    $tabel_histori_terpilih = $_POST['tabel_histori'];

    try {
        $results = UtilitasDbModel::hapusHistori($pdo, $tabel_histori_terpilih);
        
        if (!empty($results['sukses'])) {
            $_SESSION['pesan_sukses'] = implode('<br>', $results['sukses']);
        }
        
        if (!empty($results['error'])) {
            // Tampilkan error jika ada tabel yang diblokir oleh model
            $_SESSION['pesan_error'] = implode('<br>', $results['error']);
        }
        
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus histori: " . $e->getMessage();
    }
    
    redirect('index.php?mod=utilitas_db');
}

/**
 * Aksi BARU: Hapus data konfigurasi/setup (TRUNCATE)
 */
function utilitas_db_hapus_setup($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');

    if (empty($_POST['tabel_setup'])) {
        $_SESSION['pesan_error'] = "Tidak ada tabel setup yang dipilih untuk dihapus.";
        redirect('index.php?mod=utilitas_db');
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
    
    redirect('index.php?mod=utilitas_db');
}

/**
 * Aksi: Hapus data terpilih (TRUNCATE) dari Checkbox List Global
 */
function utilitas_db_truncate_selected($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');

    if (empty($_POST['selected_tables'])) {
        $_SESSION['pesan_error'] = "Tidak ada tabel yang dipilih untuk dikosongkan.";
        redirect('index.php?mod=utilitas_db');
    }

    $selected_tables = $_POST['selected_tables'];
    
    // Use the generic truncate logic/loop directly or via Model helper
    // Model's hapusSetup/hapusHistori have safeguards. 
    // We need a GENERIC truncate for this feature as user requested "list checklist" flexibility.
    // We will use a Loop with raw SQL TRUNCATE but careful.
    
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

    redirect('index.php?mod=utilitas_db');
}

/**
 * Aksi: Reset aplikasi (TRUNCATE) - SINKRON
 */
function utilitas_db_reset_aplikasi($pdo) {
    if (!has_role(['Admin'])) redirect('index.php');

    // Daftar semua tabel Anak/Transaksi/Relasi (TRUNCATE DULU)
    $tabel_anak = [
        'absensi_mapel', 'absensi_piket', 'absensi_guru', 'catatan_kasus', 'jurnal_kbm',
        'penempatan_siswa', 'penugasan_wali_kelas', 'jadwal_mengajar', 'jadwal',
        'nilai_sumatif', 'agenda_sumatif', 'tujuan_pembelajaran', 'capaian_pembelajaran', 
        'struktur_kurikulum', 'guru_mapel', 'penugasan_wali_kelas' 
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
    
    redirect('index.php?mod=utilitas_db');
}
?>