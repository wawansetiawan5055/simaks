<?php
// app/models/UtilitasDbModel.php - COMPREHENSIVE 180+ TABLES SUPPORT & NATIVE PDO BACKUP ENGINE

class UtilitasDbModel {

    /**
     * Helper: Cek apakah tabel exist di database
     */
    public static function tableExists($pdo, $tableName) {
        try {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$tableName]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mengambil daftar semua tabel di database beserta jumlah baris dan kategori terstruktur.
     */
    public static function getAllTables($pdo) {
        $tables = [];
        try {
            $stmt = $pdo->query("SHOW TABLES");
            $tableNames = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tableNames as $name) {
                // Get row count
                try {
                    $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$name}`");
                    $rowCount = (int)$countStmt->fetchColumn();
                } catch (Exception $e) {
                    $rowCount = 0;
                }
                
                // Get Friendly Name & Category
                $info = self::getTableInfo($name);
                if ($info === null) continue; // Skip hidden/internal tables

                $tables[] = [
                    'name' => $name,
                    'label' => $info['label'],
                    'category' => $info['category'],
                    'rows' => $rowCount
                ];
            }
            
            // Sort by category then name
            usort($tables, function($a, $b) {
                if ($a['category'] === $b['category']) {
                    return strcmp($a['label'], $b['label']);
                }
                return strcmp($a['category'], $b['category']);
            });

            return $tables;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Helper: Mendapatkan Nama Ramah dan Kategori Otomatis berdasarkan nama tabel (180+ Tabel)
     */
    public static function getTableInfo($tableName) {
        // Daftar internal/framework yang disembunyikan
        $hidden = ['app_migrations', 'migrations', 'sessions'];
        if (in_array($tableName, $hidden)) return null;

        // Custom Mappings Spesifik
        $map = [
            // Data Master
            'siswa' => ['label' => 'Data Siswa', 'category' => 'Data Master'],
            'guru' => ['label' => 'Data Guru & PTK', 'category' => 'Data Master'],
            'kelas' => ['label' => 'Data Rombel / Kelas', 'category' => 'Data Master'],
            'mapel' => ['label' => 'Mata Pelajaran', 'category' => 'Data Master'],
            'tahun_ajaran' => ['label' => 'Tahun Ajaran', 'category' => 'Data Master'],
            'pengguna' => ['label' => 'Data Akun Pengguna', 'category' => 'Data Master'],
            'pengguna_peran' => ['label' => 'Relasi Pengguna - Peran', 'category' => 'Data Master'],
            'peran' => ['label' => 'Data Peran (Roles)', 'category' => 'Data Master'],
            'hak_akses' => ['label' => 'Hak Akses Menu (Permissions)', 'category' => 'Data Master'],
            'master_jam' => ['label' => 'Master Jam Pelajaran', 'category' => 'Data Master'],
            'jam_pelajaran' => ['label' => 'Master Jam Pelajaran (Legacy)', 'category' => 'Data Master'],
            'master_kegiatan' => ['label' => 'Master Kegiatan Sekolah', 'category' => 'Data Master'],
            'profil_sekolah' => ['label' => 'Profil & Identitas Sekolah', 'category' => 'Data Master'],
            'profil_guru' => ['label' => 'Biodata Detail Guru', 'category' => 'Data Master'],
            'profil_siswa' => ['label' => 'Biodata Detail Siswa', 'category' => 'Data Master'],
            'app_menu' => ['label' => 'Struktur Menu SIMAKS', 'category' => 'Data Master'],
            'app_config' => ['label' => 'Konfigurasi Tema & Fitur', 'category' => 'Data Master'],
            'app_settings' => ['label' => 'Pengaturan Umum SIMAKS', 'category' => 'Data Master'],
            'kalender_akademik' => ['label' => 'Kalender Akademik', 'category' => 'Data Master'],
            'kalender_kategori' => ['label' => 'Kategori Kalender', 'category' => 'Data Master'],
            'ref_surah' => ['label' => 'Referensi Al-Quran & Surah', 'category' => 'Data Master'],
            'ref_profil_lulusan' => ['label' => 'Referensi Profil Lulusan', 'category' => 'Data Master'],
            'master_template_dokumen' => ['label' => 'Template Dokumen Akademik', 'category' => 'Data Master'],
        ];

        if (array_key_exists($tableName, $map)) {
            return $map[$tableName];
        }

        // Auto-detect berdasarkan prefix nama tabel
        $label = ucwords(str_replace('_', ' ', $tableName));

        if (str_starts_with($tableName, 'cbt_')) {
            return ['label' => 'CBT: ' . substr($label, 4), 'category' => 'CBT (Ujian Online)'];
        }
        if (str_starts_with($tableName, 'lms_')) {
            return ['label' => 'LMS: ' . substr($label, 4), 'category' => 'LMS (Pembelajaran)'];
        }
        if (str_starts_with($tableName, 'keuangan_')) {
            return ['label' => 'Keuangan: ' . substr($label, 9), 'category' => 'Keuangan & Payroll'];
        }
        if (str_starts_with($tableName, 'landing_')) {
            return ['label' => 'Web: ' . substr($label, 8), 'category' => 'Website Landing Page'];
        }
        if (str_starts_with($tableName, 'surat_')) {
            return ['label' => 'Persuratan: ' . substr($label, 6), 'category' => 'Persuratan & Dokumen'];
        }
        if (str_starts_with($tableName, 'uks_')) {
            return ['label' => 'UKS: ' . substr($label, 4), 'category' => 'Layanan UKS'];
        }
        if (str_starts_with($tableName, 'perpus_')) {
            return ['label' => 'Perpus: ' . substr($label, 7), 'category' => 'Perpustakaan'];
        }
        if (str_starts_with($tableName, 'sarpras_')) {
            return ['label' => 'Sarpras: ' . substr($label, 8), 'category' => 'Sarana & Prasarana'];
        }
        if (str_starts_with($tableName, 'tahfidz_') || $tableName === 'tahfidz' || $tableName === 'anggota_tahfidz' || $tableName === 'setoran_tahfidz' || $tableName === 'presensi_tahfidz' || $tableName === 'jurnal_tahfidz') {
            return ['label' => 'Tahfidz: ' . $label, 'category' => 'Program Kesiswaan & Karakter'];
        }
        if (str_starts_with($tableName, 'kewirausahaan_') || $tableName === 'kewirausahaan' || $tableName === 'anggota_kewirausahaan' || $tableName === 'jurnal_kewirausahaan' || $tableName === 'presensi_kewirausahaan') {
            return ['label' => 'Kewirausahaan: ' . $label, 'category' => 'Program Kesiswaan & Karakter'];
        }
        if (str_starts_with($tableName, 'kokulikuler_') || $tableName === 'kokulikuler' || $tableName === 'anggota_kokulikuler' || $tableName === 'jurnal_kokulikuler' || $tableName === 'presensi_kokulikuler' || $tableName === 'agenda_kokulikuler') {
            return ['label' => 'Kokulikuler: ' . $label, 'category' => 'Program Kesiswaan & Karakter'];
        }
        if (str_starts_with($tableName, 'pembiasaan_') || $tableName === 'pembiasaan' || $tableName === 'anggota_pembiasaan' || $tableName === 'jurnal_pembiasaan' || $tableName === 'presensi_pembiasaan' || $tableName === 'agenda_pembiasaan' || $tableName === 'rekap_presensi_pembiasaan') {
            return ['label' => 'Pembiasaan: ' . $label, 'category' => 'Program Kesiswaan & Karakter'];
        }
        if (str_starts_with($tableName, 'ekskul_') || $tableName === 'ekstrakurikuler' || $tableName === 'anggota_ekskul' || $tableName === 'jurnal_ekstrakurikuler' || $tableName === 'presensi_ekstrakurikuler' || $tableName === 'nilai_ekskul') {
            return ['label' => 'Ekstrakurikuler: ' . $label, 'category' => 'Program Kesiswaan & Karakter'];
        }
        if (str_starts_with($tableName, 'absensi_') || str_starts_with($tableName, 'jurnal_') || str_starts_with($tableName, 'nilai_') || str_starts_with($tableName, 'catatan_') || str_starts_with($tableName, 'log_') || in_array($tableName, ['audit_log', 'login_attempts', 'mutasi_masuk', 'mutasi_siswa', 'siswa_alumni', 'siswa_mutasi', 'tracer_study', 'internal_chat_messages', 'internal_chat_deleted_messages', 'ppdb_pendaftaran'])) {
            return ['label' => $label, 'category' => 'Data Histori & Log'];
        }
        if (str_starts_with($tableName, 'penugasan_') || str_starts_with($tableName, 'penempatan_') || str_starts_with($tableName, 'jadwal_') || str_starts_with($tableName, 'struktur_') || str_starts_with($tableName, 'capaian_') || str_starts_with($tableName, 'tujuan_') || in_array($tableName, ['guru_mapel', 'perangkat_pembelajaran', 'rekap_bobot_guru'])) {
            return ['label' => $label, 'category' => 'Data Konfigurasi & Setup'];
        }

        return ['label' => $label, 'category' => 'Lainnya'];
    }

    /**
     * NATIVE PDO SQL BACKUP ENGINE (Universal - Works without mysqldump CLI)
     * Mengalirkan SQL Dump utuh langsung ke browser output stream.
     */
    public static function streamSqlBackup(PDO $pdo, $type = 'full', $selectedTables = null) {
        $stmt = $pdo->query("SHOW TABLES");
        $allDbTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $tablesToBackup = [];
        if (!empty($selectedTables) && is_array($selectedTables)) {
            foreach ($selectedTables as $st) {
                if (in_array($st, $allDbTables)) {
                    $tablesToBackup[] = $st;
                }
            }
        } else {
            $tablesToBackup = $allDbTables;
        }

        // Header SQL
        echo "-- ========================================================\n";
        echo "-- SIMAKS Database Backup Utility\n";
        echo "-- Generation Time: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Backup Type: " . strtoupper($type) . "\n";
        echo "-- Total Tables: " . count($tablesToBackup) . "\n";
        echo "-- ========================================================\n\n";
        echo "SET FOREIGN_KEY_CHECKS = 0;\n";
        echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        echo "SET time_zone = \"+00:00\";\n\n";

        foreach ($tablesToBackup as $table) {
            // 1. Structure (CREATE TABLE)
            if ($type === 'full' || $type === 'structure') {
                echo "-- --------------------------------------------------------\n";
                echo "-- Struktur Tabel: `{$table}`\n";
                echo "-- --------------------------------------------------------\n";
                echo "DROP TABLE IF EXISTS `{$table}`;\n";

                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createStmt->fetch(PDO::FETCH_NUM);
                if ($createRow && isset($createRow[1])) {
                    echo $createRow[1] . ";\n\n";
                }
            }

            // 2. Data (INSERT INTO)
            if ($type === 'full' || $type === 'data') {
                $rowsStmt = $pdo->query("SELECT * FROM `{$table}`");
                $rowCount = $rowsStmt->rowCount();

                if ($rowCount > 0) {
                    echo "-- Dumping data untuk tabel `{$table}` ({$rowCount} baris)\n";
                    
                    // Fetch columns
                    $colStmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
                    $columns = $colStmt->fetchAll(PDO::FETCH_COLUMN);
                    $colNames = '`' . implode('`, `', $columns) . '`';

                    $batch = [];
                    $batchSize = 100;

                    while ($row = $rowsStmt->fetch(PDO::FETCH_ASSOC)) {
                        $values = [];
                        foreach ($columns as $col) {
                            $val = $row[$col] ?? null;
                            if ($val === null) {
                                $values[] = "NULL";
                            } else {
                                $values[] = $pdo->quote($val);
                            }
                        }
                        $batch[] = "(" . implode(', ', $values) . ")";

                        if (count($batch) >= $batchSize) {
                            echo "INSERT INTO `{$table}` ({$colNames}) VALUES\n" . implode(",\n", $batch) . ";\n";
                            $batch = [];
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                        }
                    }

                    if (!empty($batch)) {
                        echo "INSERT INTO `{$table}` ({$colNames}) VALUES\n" . implode(",\n", $batch) . ";\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                }
            }
        }

        echo "SET FOREIGN_KEY_CHECKS = 1;\n";
        echo "-- Backup Selesai (" . date('Y-m-d H:i:s') . ")\n";
    }

    /**
     * Menjalankan Raw SQL Query (DANGEROUS).
     */
    public static function executeRawSql($pdo, $sql) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            if (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0 || stripos(trim($sql), 'DESCRIBE') === 0) {
                return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'type' => 'result'];
            }
            
            return ['success' => true, 'rows_affected' => $stmt->rowCount(), 'type' => 'affected'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Optimasi & Periksa Kesehatan Database (OPTIMIZE & CHECK ALL TABLES)
     */
    public static function optimizeAndCheckTables(PDO $pdo) {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $results = [];
        foreach ($tables as $t) {
            try {
                $check = $pdo->query("CHECK TABLE `{$t}`")->fetch(PDO::FETCH_ASSOC);
                $opt = $pdo->query("OPTIMIZE TABLE `{$t}`")->fetch(PDO::FETCH_ASSOC);
                $results[] = [
                    'table' => $t,
                    'status' => $check['Msg_text'] ?? 'OK',
                    'optimize' => $opt['Msg_text'] ?? 'OK'
                ];
            } catch (Exception $e) {
                $results[] = [
                    'table' => $t,
                    'status' => 'Error: ' . $e->getMessage(),
                    'optimize' => 'Failed'
                ];
            }
        }
        return $results;
    }

    /**
     * Menjalankan operasi TRUNCATE pada daftar tabel untuk mereset aplikasi.
     */
    public static function resetAplikasi($pdo, $tabel_anak, $tabel_induk) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->beginTransaction();
        try {
            foreach ($tabel_anak as $tabel) {
                if (self::tableExists($pdo, $tabel)) {
                    $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                }
            }
            
            foreach ($tabel_induk as $tabel) {
                if (self::tableExists($pdo, $tabel)) {
                    $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                }
            }
            
            $pdo->commit();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return true;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            throw new Exception("Gagal menjalankan TRUNCATE: " . $e->getMessage());
        }
    }

    /**
     * Menghapus (TRUNCATE) data dari tabel histori yang dipilih.
     */
    public static function hapusHistori($pdo, $tabel_histori_terpilih) {
        $pesan_sukses = [];
        $pesan_error = [];
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->beginTransaction();
        try {
            foreach ($tabel_histori_terpilih as $tabel) {
                if (!self::tableExists($pdo, $tabel)) {
                    $pesan_error[] = "⚠️ Tabel '{$tabel}' tidak ditemukan di database.";
                    continue;
                }
                
                $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                $pesan_sukses[] = "✅ Tabel '{$tabel}' berhasil dikosongkan.";
            }
            
            $pdo->commit();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $pesan_error[] = "❌ Error database: " . $e->getMessage();
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
        }
    }
    
    /**
     * Menghapus (TRUNCATE) data dari tabel Konfigurasi/Setup yang dipilih.
     */
    public static function hapusSetup($pdo, $tabel_setup_terpilih) {
        $pesan_sukses = [];
        $pesan_error = [];
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->beginTransaction();
        try {
            foreach ($tabel_setup_terpilih as $tabel) {
                if (!self::tableExists($pdo, $tabel)) {
                    $pesan_error[] = "⚠️ Tabel '{$tabel}' tidak ditemukan di database.";
                    continue;
                }
                
                $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                $pesan_sukses[] = "✅ Tabel '{$tabel}' berhasil dikosongkan.";
            }
            
            $pdo->commit();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $pesan_error[] = "❌ Error database: " . $e->getMessage();
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
        }
    }

    /**
     * Menjalankan query SQL dari konten file untuk operasi Restore.
     */
    public static function restoreDatabase($pdo, $sql) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec($sql);
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        return true;
    }

    /**
     * Memastikan tabel riwayat migrasi (app_migrations) tersedia
     */
    public static function ensureMigrationTable(PDO $pdo) {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS `app_migrations` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `patch_name` VARCHAR(255) NOT NULL UNIQUE,
                    `executed_at` DATETIME NOT NULL,
                    `executed_by` VARCHAR(100) DEFAULT 'Admin',
                    `status` ENUM('Success', 'Failed') DEFAULT 'Success',
                    `notes` TEXT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } catch (Exception $e) {
            // Ignore if exists
        }
    }

    /**
     * Mengambil daftar file .sql dari folder patch/ dan sql/
     */
    public static function getAvailablePatches() {
        $patches = [];
        $searchDirs = [
            __DIR__ . '/../../patch',
            __DIR__ . '/../../sql'
        ];

        foreach ($searchDirs as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $f) {
                    if (pathinfo($f, PATHINFO_EXTENSION) === 'sql') {
                        $fullPath = $dir . '/' . $f;
                        $patches[] = [
                            'filename' => $f,
                            'path' => $fullPath,
                            'size' => filesize($fullPath),
                            'modified' => filemtime($fullPath),
                            'dir_name' => basename($dir)
                        ];
                    }
                }
            }
        }

        // Sort descending by filename
        usort($patches, function($a, $b) {
            return strcmp($b['filename'], $a['filename']);
        });

        return $patches;
    }

    /**
     * Mengambil riwayat patch yang sudah dieksekusi dari database
     */
    public static function getAppliedPatches(PDO $pdo) {
        self::ensureMigrationTable($pdo);
        try {
            $stmt = $pdo->query("SELECT * FROM app_migrations ORDER BY executed_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Eksekusi satu file patch SQL ke database secara aman
     */
    public static function executePatch(PDO $pdo, $filename, $username = 'Admin') {
        self::ensureMigrationTable($pdo);

        // Cari file di patch/ atau sql/
        $searchDirs = [
            __DIR__ . '/../../patch',
            __DIR__ . '/../../sql'
        ];

        $targetFile = null;
        foreach ($searchDirs as $dir) {
            $check = $dir . '/' . basename($filename);
            if (file_exists($check)) {
                $targetFile = $check;
                break;
            }
        }

        if (!$targetFile) {
            return ['success' => false, 'message' => "File patch '{$filename}' tidak ditemukan di server."];
        }

        $sqlContent = file_get_contents($targetFile);
        if ($sqlContent === false || trim($sqlContent) === '') {
            return ['success' => false, 'message' => "File patch kosong atau tidak dapat dibaca."];
        }

        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $pdo->exec($sqlContent);
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

            // Catat riwayat migrasi
            $stmt = $pdo->prepare("
                INSERT INTO app_migrations (patch_name, executed_at, executed_by, status, notes)
                VALUES (?, NOW(), ?, 'Success', 'Dieksekusi via Database Patch Runner')
                ON DUPLICATE KEY UPDATE executed_at = NOW(), executed_by = VALUES(executed_by), status = 'Success'
            ");
            $stmt->execute([basename($filename), $username]);

            return ['success' => true, 'message' => "Patch '{$filename}' berhasil dieksekusi ke database!"];
        } catch (Exception $e) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return ['success' => false, 'message' => "Gagal mengeksekusi patch: " . $e->getMessage()];
        }
    }
}