<?php
// app/models/UtilitasDbModel.php - IMPROVED VERSION with table existence check

class UtilitasDbModel {

    /**
     * Helper: Cek apakah tabel exist di database
     */
    private static function tableExists($pdo, $tableName) {
        try {
            $result = $pdo->query("SHOW TABLES LIKE '{$tableName}'");
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mengambil daftar semua tabel di database beserta jumlah barisnya.
     */
    public static function getAllTables($pdo) {
        $tables = [];
        try {
            $sql = "SHOW TABLES";
            $stmt = $pdo->query($sql);
            $tableNames = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tableNames as $name) {
                // Get row count
                $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$name}`");
                $rowCount = $countStmt->fetchColumn();
                
                // Get Friendly Name & Category
                $info = self::getTableInfo($name);

                if ($info === null) continue; // Skip hidden tables

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
     * Helper: Mendapatkan Nama Ramah dan Kategori berdasarkan nama tabel
     */
    private static function getTableInfo($tableName) {
        // Default
        $label = ucwords(str_replace('_', ' ', $tableName));
        $category = 'Lainnya';

        // Custom Mappings
        $map = [
            // Data Master
            'siswa' => ['label' => 'Data Siswa', 'category' => 'Data Master'],
            'guru' => ['label' => 'Data Guru', 'category' => 'Data Master'],
            'kelas' => ['label' => 'Data Kelas', 'category' => 'Data Master'],
            'mapel' => ['label' => 'Mata Pelajaran', 'category' => 'Data Master'],
            'tahun_ajaran' => ['label' => 'Tahun Ajaran', 'category' => 'Data Master'],
            'pengguna' => ['label' => 'Data Pengguna Sistem', 'category' => 'Data Master'],
            'master_jam' => ['label' => 'Master Jam Pelajaran', 'category' => 'Data Master'],
            'master_kegiatan' => ['label' => 'Master Kegiatan', 'category' => 'Data Master'],
            'peran' => ['label' => 'Data Peran (Role)', 'category' => 'Data Master'],
            'hak_akses' => ['label' => 'Data Hak Akses', 'category' => 'Data Master'],
            
            // Data Konfigurasi/Relasi (Setup)
            'guru_mapel' => ['label' => 'Penugasan Guru Mapel', 'category' => 'Data Konfigurasi/Setup'],
            'jadwal' => ['label' => 'Jadwal Pelajaran (Master)', 'category' => 'Data Konfigurasi/Setup'],
            'jadwal_mengajar' => ['label' => 'Jadwal Mengajar Guru', 'category' => 'Data Konfigurasi/Setup'],
            'penempatan_siswa' => ['label' => 'Penempatan Siswa di Kelas', 'category' => 'Data Konfigurasi/Setup'],
            'penugasan_guru' => ['label' => 'Penugasan Guru (Piket/Lainnya)', 'category' => 'Data Konfigurasi/Setup'],
            'penugasan_wali_kelas' => ['label' => 'Penugasan Wali Kelas', 'category' => 'Data Konfigurasi/Setup'],
            'struktur_kurikulum' => ['label' => 'Struktur Kurikulum', 'category' => 'Data Konfigurasi/Setup'],
            'capaian_pembelajaran' => ['label' => 'Capaian Pembelajaran (CP)', 'category' => 'Data Konfigurasi/Setup'],
            'tujuan_pembelajaran' => ['label' => 'Tujuan Pembelajaran (TP)', 'category' => 'Data Konfigurasi/Setup'],
            'app_menu' => ['label' => 'Konfigurasi Menu Aplikasi', 'category' => 'Data Konfigurasi/Setup'],
            'app_config' => ['label' => 'Konfigurasi Aplikasi Global', 'category' => 'Data Konfigurasi/Setup'],

            // Data Histori/Log/Transaksi
            'absensi_guru' => ['label' => 'Rekap Absensi Guru', 'category' => 'Data Histori/Log'],
            'absensi_mapel' => ['label' => 'Rekap Absensi Mapel (Siswa)', 'category' => 'Data Histori/Log'],
            'absensi_piket' => ['label' => 'Jurnal/Absensi Piket', 'category' => 'Data Histori/Log'],
            'catatan_kasus' => ['label' => 'Catatan Kasus Siswa', 'category' => 'Data Histori/Log'],
            'catatan_kelas' => ['label' => 'Catatan Kelas', 'category' => 'Data Histori/Log'],
            'jurnal_kbm' => ['label' => 'Jurnal KBM Guru', 'category' => 'Data Histori/Log'],
            'nilai' => ['label' => 'Data Nilai Siswa', 'category' => 'Data Histori/Log'],
            'nilai_sumatif' => ['label' => 'Data Nilai Sumatif', 'category' => 'Data Histori/Log'],
            'agenda_sumatif' => ['label' => 'Agenda Sumatif', 'category' => 'Data Histori/Log'],
            'mutasi_masuk' => ['label' => 'Data Mutasi Masuk', 'category' => 'Data Histori/Log'],
            'mutasi_siswa' => ['label' => 'Data Mutasi Keluar', 'category' => 'Data Histori/Log'],
            'lulusan' => ['label' => 'Data Alumni/Lulusan', 'category' => 'Data Histori/Log'],
            'log_aktivitas' => ['label' => 'Log Aktivitas User', 'category' => 'Data Histori/Log'],
            'ppdb_pendaftaran' => ['label' => 'Data Pendaftaran PPDB', 'category' => 'Data Histori/Log'],
            
            // Data Konfigurasi/Relasi (Setup)
            'profil_sekolah' => ['label' => 'Profil Sekolah', 'category' => 'Data Konfigurasi/Setup'],
            'landing_slide' => ['label' => 'Data Slider Landing Page', 'category' => 'Data Konfigurasi/Setup'],
            'landing_news' => ['label' => 'Data Berita Landing Page', 'category' => 'Data Konfigurasi/Setup'],
            'landing_gallery' => ['label' => 'Data Galeri Landing Page', 'category' => 'Data Konfigurasi/Setup'],
            'landing_setting' => ['label' => 'Pengaturan Landing Page', 'category' => 'Data Konfigurasi/Setup'],
            'pengumuman' => ['label' => 'Data Pengumuman', 'category' => 'Data Konfigurasi/Setup'],

            // Data Master Additional
            'jam_pelajaran' => ['label' => 'Master Jam Pelajaran', 'category' => 'Data Master'],
            'pengguna_peran' => ['label' => 'Relasi Pengguna-Peran', 'category' => 'Data Master'],
            'profil_siswa' => ['label' => 'Detail Profil Siswa', 'category' => 'Data Master'],

            // Data Histori/Log Additional
            'absensi_siswa_mapel' => ['label' => 'Absensi Siswa Per Mapel', 'category' => 'Data Histori/Log'],
            'absensi_siswa_piket' => ['label' => 'Absensi Siswa (Piket)', 'category' => 'Data Histori/Log'],
            'nilai_sumatif_tp' => ['label' => 'Nilai Sumatif TP', 'category' => 'Data Histori/Log'],
            'penilaian_sumatif' => ['label' => 'Data Penilaian Sumatif', 'category' => 'Data Histori/Log'],
            'siswa_alumni' => ['label' => 'Data Alumni (Arsip)', 'category' => 'Data Histori/Log'],
            'siswa_mutasi' => ['label' => 'Data Mutasi Siswa (Arsip)', 'category' => 'Data Histori/Log'],

            // HIDDEN (Ignore)
            'audit_log' => null,
            'app_setting' => null, 
            'app_settings' => null, // Hide plural version too
            'notifikasi' => null,
            'pendaftaran_ppdb' => null, 
            'migrations' => null, // Hide framework tables if any
            'sessions' => null,   // Hide session tables if any
        ];

        if (array_key_exists($tableName, $map)) {
            return $map[$tableName];
        }

        // Auto detect prefixes if not in map
        if (strpos($tableName, 'log_') === 0 || strpos($tableName, 'histori_') === 0) {
            $category = 'Data Histori/Log';
        } elseif (strpos($tableName, 'setting_') === 0 || strpos($tableName, 'conf_') === 0) {
            $category = 'Data Konfigurasi/Setup';
        }

        return ['label' => $label, 'category' => $category];
    }

    /**
     * Menjalankan Raw SQL Query (DANGEROUS).
     */
    public static function executeRawSql($pdo, $sql) {
        try {
            // Check for destructive commands if strictly needed, but admin has power.
            // We just execute it.
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            // If SELECT, return results
            if (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0 || stripos(trim($sql), 'DESCRIBE') === 0) {
                return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'type' => 'result'];
            }
            
            return ['success' => true, 'rows_affected' => $stmt->rowCount(), 'type' => 'affected'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Menjalankan operasi TRUNCATE pada daftar tabel untuk mereset aplikasi.
     */
    public static function resetAplikasi($pdo, $tabel_anak, $tabel_induk) {
        
        $pdo->beginTransaction();
        try {
            // 1. Nonaktifkan cek foreign key sementara
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

            // 2. Truncate tabel anak dan transaksi
            foreach ($tabel_anak as $tabel) {
                if (self::tableExists($pdo, $tabel)) {
                    $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                }
            }
            
            // 3. Truncate tabel induk dan master
            foreach ($tabel_induk as $tabel) {
                if (self::tableExists($pdo, $tabel)) {
                    $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                }
            }
            
            // 4. Aktifkan kembali cek foreign key
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            
            $pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            throw new Exception("Gagal menjalankan TRUNCATE: " . $e->getMessage());
        }
    }

    /**
     * Menghapus (TRUNCATE) data dari tabel histori yang dipilih.
     * IMPROVED: Auto-check table existence + lebih banyak tabel aman
     */
    public static function hapusHistori($pdo, $tabel_histori_terpilih) {
        // Daftar tabel LOG/HISTORI yang aman untuk dihapus
        $tabel_yang_aman_dihapus = [
            'jurnal_kbm',
            'absensi_mapel',
            'absensi_piket',
            'absensi_guru',
            'catatan_kasus',
            'nilai',                    // NEW
            'nilai_sumatif',           // NEW
            'detail_absensi_mapel',    // NEW (jika ada)
            'log_aktivitas'            // NEW (jika ada)
        ];
        
        $pesan_sukses = [];
        $pesan_error = [];
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->beginTransaction();
        try {
            foreach ($tabel_histori_terpilih as $tabel) {
                // Cek apakah tabel aman DAN exist
                if (!in_array($tabel, $tabel_yang_aman_dihapus)) {
                    $pesan_error[] = "⚠️ Tabel '{$tabel}' tidak diizinkan (bukan tabel histori).";
                    continue;
                }
                
                if (!self::tableExists($pdo, $tabel)) {
                    $pesan_error[] = "⚠️ Tabel '{$tabel}' tidak ditemukan di database.";
                    continue;
                }
                
                // Safe to truncate
                $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                $pesan_sukses[] = "✅ Tabel '{$tabel}' berhasil dikosongkan.";
            }
            
            $pdo->commit();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
            
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $pesan_error[] = "❌ Error database: " . $e->getMessage();
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
        }
    }
    
    /**
     * Menghapus (TRUNCATE) data dari tabel Konfigurasi/Setup yang dipilih.
     * IMPROVED: Auto-check table existence + lebih banyak tabel aman
     */
    public static function hapusSetup($pdo, $tabel_setup_terpilih) {
        // Daftar tabel SETUP/RELASI yang aman untuk dihapus
        $tabel_yang_aman_dihapus = [
            'guru_mapel',
            'penugasan_wali_kelas',
            'penempatan_siswa',
            'jadwal_mengajar',
            'jadwal',
            'penugasan_guru',         // NEW (untuk piket, dll)
            'struktur_kurikulum',     // NEW (jika ada)
            'tujuan_pembelajaran',    // NEW (jika ada)
            'capaian_pembelajaran'    // NEW (jika ada)
        ];
        
        $pesan_sukses = [];
        $pesan_error = [];
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->beginTransaction();
        try {
            foreach ($tabel_setup_terpilih as $tabel) {
                // Cek apakah tabel aman DAN exist
                if (!in_array($tabel, $tabel_yang_aman_dihapus)) {
                    $pesan_error[] = "⚠️ Tabel '{$tabel}' tidak diizinkan (bukan tabel setup).";
                    continue;
                }
                
                if (!self::tableExists($pdo, $tabel)) {
                    $pesan_error[] = "⚠️ Tabel '{$tabel}' tidak ditemukan di database.";
                    continue;
                }
                
                // Safe to truncate
                $pdo->exec("TRUNCATE TABLE `{$tabel}`");
                $pesan_sukses[] = "✅ Tabel '{$tabel}' berhasil dikosongkan.";
            }
            
            $pdo->commit();
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
            
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $pesan_error[] = "❌ Error database: " . $e->getMessage();
            return ['sukses' => $pesan_sukses, 'error' => $pesan_error];
        }
    }

    /**
     * Menjalankan query SQL dari konten file untuk operasi Restore.
     */
    public static function restoreDatabase($pdo, $sql) {
        if (stripos(trim($sql), 'SET FOREIGN_KEY_CHECKS = 0;') !== 0) {
              $sql = "SET FOREIGN_KEY_CHECKS = 0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS = 1;";
        }
        
        $pdo->exec($sql);
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
            return true;
        } catch (Exception $e) {
            error_log("Failed to ensure app_migrations table: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil daftar file patch SQL yang tersedia di folder sql/ dan patch/
     */
    public static function getAvailablePatches() {
        $patches = [];
        $dirs = [
            __DIR__ . '/../../sql',
            __DIR__ . '/../../patch'
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                        $fullPath = $dir . '/' . $file;
                        $patches[$file] = [
                            'filename' => $file,
                            'folder' => basename($dir),
                            'path' => $fullPath,
                            'size' => filesize($fullPath),
                            'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
                        ];
                    }
                }
            }
        }

        ksort($patches);
        return array_values($patches);
    }

    /**
     * Mengambil daftar patch yang sudah pernah dieksekusi
     */
    public static function getAppliedPatches(PDO $pdo) {
        self::ensureMigrationTable($pdo);
        try {
            $stmt = $pdo->query("SELECT patch_name, executed_at, executed_by, status FROM app_migrations");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $applied = [];
            foreach ($rows as $row) {
                $applied[$row['patch_name']] = $row;
            }
            return $applied;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Menjalankan file patch SQL tertentu dan mencatat riwayatnya
     */
    public static function executePatch(PDO $pdo, string $filename, string $username = 'Admin') {
        self::ensureMigrationTable($pdo);
        
        $dirs = [
            __DIR__ . '/../../sql/' . $filename,
            __DIR__ . '/../../patch/' . $filename
        ];

        $targetFile = null;
        foreach ($dirs as $path) {
            if (file_exists($path)) {
                $targetFile = $path;
                break;
            }
        }

        if (!$targetFile) {
            return ['success' => false, 'message' => "File patch '{$filename}' tidak ditemukan."];
        }

        $sqlContent = file_get_contents($targetFile);
        if (empty(trim($sqlContent))) {
            return ['success' => false, 'message' => "File patch kosong."];
        }

        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $pdo->exec($sqlContent);
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

            // Catat ke app_migrations
            $stmt = $pdo->prepare("
                INSERT INTO app_migrations (patch_name, executed_at, executed_by, status)
                VALUES (?, NOW(), ?, 'Success')
                ON DUPLICATE KEY UPDATE executed_at = NOW(), executed_by = VALUES(executed_by), status = 'Success'
            ");
            $stmt->execute([$filename, $username]);

            return ['success' => true, 'message' => "Patch '{$filename}' berhasil dijalankan."];
        } catch (Exception $e) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return ['success' => false, 'message' => "Gagal menjalankan patch: " . $e->getMessage()];
        }
    }
}
?>