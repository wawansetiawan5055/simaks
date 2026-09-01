<?php
/**
 * PpdbModel.php - Model untuk PPDB (Terintegrasi Online & Manual)
 * Updated: Support untuk sumber_pendaftaran (online/manual)
 * Tabel: ppdb_pendaftaran
 */

class PpdbModel {
    
    /**
     * Normalisasi format tanggal lahir menjadi format standar SQL (YYYY-MM-DD)
     * Menangani berbagai format: Excel serial, DD/MM/YYYY, DD-MM-YYYY, YYYY-MM-DD, teks bulan Indonesia
     */
    public static function normalizeDateForSql($raw) {
        if (empty($raw)) return null;
        $raw = trim((string)$raw);
        if ($raw === '' || $raw === '0000-00-00' || $raw === '-' || $raw === 'NULL') return null;

        // 1. Jika sudah YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            [$y, $m, $d] = explode('-', $raw);
            if (checkdate((int)$m, (int)$d, (int)$y)) {
                return $raw;
            }
        }

        // 2. Jika format DD/MM/YYYY atau DD-MM-YYYY (format umum Indonesia)
        if (preg_match('/^(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{4})$/', $raw, $matches)) {
            $day = (int)$matches[1];
            $month = (int)$matches[2];
            $year = (int)$matches[3];
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // 3. Konversi nama bulan Indonesia ke Inggris
        $bulan_indo = [
            'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
            'april' => 'april', 'mei' => 'may', 'juni' => 'june',
            'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
            'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
            'jan' => 'jan', 'feb' => 'feb', 'mar' => 'mar', 'apr' => 'apr',
            'agt' => 'aug', 'ags' => 'aug', 'sep' => 'sep', 'okt' => 'oct', 'nov' => 'nov', 'des' => 'dec'
        ];
        $clean_str = str_ireplace(array_keys($bulan_indo), array_values($bulan_indo), strtolower($raw));

        try {
            $dt = new DateTime($clean_str);
            return $dt->format('Y-m-d');
        } catch (Exception $e) {
            $time = strtotime($clean_str);
            if ($time !== false && $time > 0) {
                return date('Y-m-d', $time);
            }
        }

        return null;
    }

    /**
     * Simpan data pendaftaran PPDB (Manual Entry dari Admin)
     * @param PDO $pdo
     * @param array $data
     * @return bool
     */
    public static function save($pdo, $data) {
        // 1. Buat Nomor Pendaftaran Unik (Contoh: PPDB-TAHUN-URUTAN)
        $prefix = "PPDB-" . date('Y') . "-";
        $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM ppdb_pendaftaran WHERE no_pendaftaran LIKE ?");
        $stmt_count->execute([$prefix . '%']);
        $count = $stmt_count->fetchColumn() + 1;
        $no_pendaftaran = $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        // 2. Query Insert (Tabel: ppdb_pendaftaran, Sumber: manual)
        $sql = "INSERT INTO ppdb_pendaftaran 
                (id_ta, sumber_pendaftaran, no_pendaftaran, 
                 nama_lengkap, nisn, nik, jenis_kelamin, tempat_lahir, tanggal_lahir, 
                 asal_sekolah, jalur_pendaftaran, 
                 nama_wali, no_hp_wali, 
                 status)
                VALUES 
                (:id_ta, 'manual', :no_pendaftaran, 
                 :nama_lengkap, :nisn, :nik, :jk, :tempat_lahir, :tanggal_lahir, 
                 :asal_sekolah, :jalur_pendaftaran, 
                 :nama_wali, :no_hp_wali, 
                 'pending')"; // Status default 'pending'

        $stmt = $pdo->prepare($sql);
        
        $params = [
            ':id_ta' => $data['id_ta'],
            ':no_pendaftaran' => $no_pendaftaran,
            ':nama_lengkap' => $data['nama_lengkap'],
            ':nisn' => $data['nisn'],
            ':nik' => $data['nik'],
            ':jk' => $data['jk'],
            ':tempat_lahir' => $data['tempat_lahir'],
            ':tanggal_lahir' => self::normalizeDateForSql($data['tanggal_lahir']),
            ':asal_sekolah' => $data['sekolah_asal'] ?? null,
            ':jalur_pendaftaran' => $data['jalur_pendaftaran'] ?? 'Zonasi',
            ':nama_wali' => $data['nama_wali'] ?? null,
            ':no_hp_wali' => $data['telp_wali'] ?? null
        ];
        
        return $stmt->execute($params);
    }

    /**
     * Import pendaftar secara massal
     * @param PDO $pdo
     * @param array $list_data
     * @return int Jumlah berhasil
     */
    public static function importBatch($pdo, $list_data) {
        $id_ta_aktif = $_SESSION['id_ta_aktif'];
        $prefix = "PPDB-" . date('Y') . "-";
        
        // Hitung urutan awal
        $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM ppdb_pendaftaran WHERE no_pendaftaran LIKE ?");
        $stmt_count->execute([$prefix . '%']);
        $count = $stmt_count->fetchColumn();
        
        $sql = "INSERT INTO ppdb_pendaftaran 
                (id_ta, sumber_pendaftaran, no_pendaftaran, 
                 nama_lengkap, nisn, nik, jenis_kelamin, tempat_lahir, tanggal_lahir, 
                 asal_sekolah, jalur_pendaftaran, 
                 nama_wali, no_hp_wali, status)
                VALUES 
                (?, 'manual', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare($sql);
            $imported = 0;
            foreach ($list_data as $row) {
                if (empty($row['nama_lengkap'])) continue;
                
                $count++;
                $no_pendaftaran = $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
                $stmt->execute([
                    $id_ta_aktif,
                    $no_pendaftaran,
                    $row['nama_lengkap'],
                    $row['nisn'] ?? null,
                    $row['nik'] ?? null,
                    $row['jk'] ?? 'L',
                    $row['tempat_lahir'] ?? null,
                    $row['tanggal_lahir'] ?? null,
                    $row['asal_sekolah'] ?? null,
                    $row['jalur_pendaftaran'] ?? 'Zonasi',
                    $row['nama_wali'] ?? null,
                    $row['no_hp_wali'] ?? null
                ]);
                $imported++;
            }
            $pdo->commit();
            return $imported;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    /**
     * Mengambil semua data pendaftar dengan filter
     * @param PDO $pdo
     * @param array $filters ['sumber' => 'online'|'manual'|'all', 'status' => 'pending'|'diterima'|...]
     * @return array
     */
    public static function getAll($pdo, $filters = []) {
        $where = ["id_ta = ?"];
        $params = [$_SESSION['id_ta_aktif']];
        
        // Filter sumber pendaftaran
        if (!empty($filters['sumber']) && $filters['sumber'] != 'all') {
            $where[] = "sumber_pendaftaran = ?";
            $params[] = $filters['sumber'];
        }
        
        // Filter status
        if (!empty($filters['status']) && $filters['status'] != 'all') {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT * FROM ppdb_pendaftaran 
                WHERE $whereClause 
                ORDER BY sumber_pendaftaran, status, nama_lengkap ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil detail pendaftar berdasarkan ID
     * @param PDO $pdo
     * @param int $id
     * @return array|false
     */
    public static function getById($pdo, $id) {
        $sql = "SELECT * FROM ppdb_pendaftaran WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil detail pendaftar berdasarkan nomor pendaftaran (untuk cek status public)
     * @param PDO $pdo
     * @param string $no_pendaftaran
     * @return array|false
     */
    public static function getByNoPendaftaran($pdo, $no_pendaftaran) {
        $sql = "SELECT * FROM ppdb_pendaftaran WHERE no_pendaftaran = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$no_pendaftaran]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mengubah status pendaftar (diterima/ditolak/pending/diverifikasi)
     * @param PDO $pdo
     * @param int $id
     * @param string $status_baru
     * @return bool
     */
    public static function updateStatus($pdo, $id, $status_baru) {
        // Validasi status yang diizinkan
        $allowed_status = ['pending', 'diverifikasi', 'diterima', 'ditolak', 'diproses_jadi_siswa'];
        if (!in_array($status_baru, $allowed_status)) {
            return false;
        }
        
        $sql = "UPDATE ppdb_pendaftaran 
                SET status = ?, 
                    verified_by = ?, 
                    verified_at = NOW() 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $status_baru, 
            $_SESSION['user_id'] ?? null, 
            $id
        ]);
    }
    
    /**
     * Update catatan verifikasi
     * @param PDO $pdo
     * @param int $id
     * @param string $catatan
     * @return bool
     */
    public static function updateCatatan($pdo, $id, $catatan) {
        $sql = "UPDATE ppdb_pendaftaran SET catatan_verifikasi = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$catatan, $id]);
    }

    /**
     * Fungsi "Promosi Massal Alfabetis"
     * Promosikan pendaftar berstatus 'diterima' ke tabel siswa
     * @param PDO $pdo
     * @return int Jumlah siswa yang dipromosikan
     * @throws Exception
     */
    public static function promoteAlphabeticalBatch($pdo) {
        $id_ta_aktif = $_SESSION['id_ta_aktif'];
        
        // 1. Ambil semua pendaftar yang 'diterima' tapi BELUM dipromosikan
        // Fix: Cek NULL atau 0 (untuk antisipasi default value database)
        $sql_select = "SELECT * FROM ppdb_pendaftaran 
                       WHERE status = 'diterima' 
                       AND (id_siswa IS NULL OR id_siswa = 0 OR id_siswa = '')
                       AND id_ta = ?
                       ORDER BY nama_lengkap ASC";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute([$id_ta_aktif]);
        $pendaftar_diterima = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

        if (empty($pendaftar_diterima)) {
            throw new Exception("Tidak ada siswa berstatus 'diterima' yang siap dipromosikan.");
        }

        // 2. Siapkan kode TA dan Prefix NIPD
        // Ambil nama TA (misal 2025/2026)
        $stmt_ta_name = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
        $stmt_ta_name->execute([$id_ta_aktif]);
        $nama_ta = $stmt_ta_name->fetchColumn();
        
        if (!$nama_ta) {
            throw new Exception("Tahun ajaran tidak ditemukan.");
        }
        
        // Fix: Gunakan Regex untuk mengambil tahun (antisipasi format "2025/2026 Ganjil")
        if (preg_match('/(\d{4})\/(\d{4})/', $nama_ta, $matches)) {
            $start_y = substr($matches[1], -2); // "25"
            $end_y = substr($matches[2], -2);   // "26"
        } else {
             // Fallback jika format tidak dikenali
             $start_y = date('y');
             $end_y = date('y', strtotime('+1 year'));
        }
        
        // 3. Tentukan kode tingkat (Asumsi PPDB default masuk kelas 10)
        $kode_tingkat = '10'; 

        $prefix_nipd = $start_y . $end_y . $kode_tingkat; // "252610"

        // 4. Hitung urutan terakhir berdasarkan NIPD yang sudah ada
        // Fix: Gunakan MAX(nipd) dengan validasi numerik agar tidak duplikat
        $stmt_max = $pdo->prepare("SELECT nipd FROM siswa WHERE nipd LIKE ? ORDER BY nipd DESC LIMIT 1");
        $stmt_max->execute([$prefix_nipd . '%']);
        $max_nipd = $stmt_max->fetchColumn(); 
        
        $next_urut = 0;
        if ($max_nipd && strlen($max_nipd) >= 9) {
            // Ambil 3 digit terakhir dan pastikan numerik
            $last_three = substr($max_nipd, -3);
            if (is_numeric($last_three)) {
                $next_urut = (int) $last_three;
            }
        }

        // 5. Mulai Transaksi
        $pdo->beginTransaction();
        try {
            $sql_siswa = "INSERT INTO siswa 
                          (nama, nisn, nipd, nik, jk, tempat_lahir, tanggal_lahir, sekolah_asal, status_aktif, id_ta_masuk)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Aktif', ?)";
            $stmt_siswa = $pdo->prepare($sql_siswa);
            
            $sql_profil = "INSERT INTO profil_siswa (id_siswa, nama_wali, telp_wali) VALUES (?, ?, ?)";
            $stmt_profil = $pdo->prepare($sql_profil);
            
            $sql_update = "UPDATE ppdb_pendaftaran SET id_siswa = ?, status = 'diproses_jadi_siswa' WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);

            $jumlah_dipromosikan = 0;

            // 6. Loop pendaftar
            foreach ($pendaftar_diterima as $data_ppdb) {
                $next_urut++;
                // Format: 252610 + 001
                $nipd_baru = $prefix_nipd . str_pad($next_urut, 3, '0', STR_PAD_LEFT);
                
                // Map jenis_kelamin dari PPDB (L/P) ke Siswa (Laki-laki/Perempuan)
                $jk_mapped = ($data_ppdb['jenis_kelamin'] == 'L') ? 'Laki-laki' : (($data_ppdb['jenis_kelamin'] == 'P') ? 'Perempuan' : $data_ppdb['jenis_kelamin']);
                
                $tgl_lahir_clean = self::normalizeDateForSql($data_ppdb['tanggal_lahir']);

                // Insert ke 'siswa'
                $stmt_siswa->execute([
                    $data_ppdb['nama_lengkap'], 
                    $data_ppdb['nisn'], 
                    $nipd_baru,
                    $data_ppdb['nik'], 
                    $jk_mapped, 
                    $data_ppdb['tempat_lahir'],
                    $tgl_lahir_clean, 
                    $data_ppdb['asal_sekolah'],
                    $id_ta_aktif // id_ta_masuk
                ]);
                
                $id_siswa_baru = $pdo->lastInsertId();
                
                // Insert Profil
                $stmt_profil->execute([
                    $id_siswa_baru, 
                    $data_ppdb['nama_wali'], 
                    $data_ppdb['no_hp_wali']
                ]);

                // Update status 'ppdb_pendaftaran'
                $stmt_update->execute([$id_siswa_baru, $data_ppdb['id']]);
                
                $jumlah_dipromosikan++;
            }

            // 7. Selesaikan Transaksi
            $pdo->commit();
            return $jumlah_dipromosikan;

        } catch (Exception $e) {
            $pdo->rollBack();
            // Log error dengan detail lebih lengkap
            error_log("PPDB Promotion Error: " . $e->getMessage());
            error_log("PPDB Promotion Trace: " . $e->getTraceAsString());
            throw new Exception("Gagal mempromosikan siswa: " . $e->getMessage());
        }
    }
    
    /**
     * Hapus data pendaftar (hanya jika belum dipromosikan)
     * @param PDO $pdo
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public static function delete($pdo, $id) {
        // Cek apakah sudah dipromosikan
        $data = self::getById($pdo, $id);
        if ($data && $data['id_siswa']) {
            throw new Exception("Tidak dapat menghapus data yang sudah dipromosikan ke siswa.");
        }
        
        // Hapus file dokumen jika ada
        $upload_dir = __DIR__ . '/../../public/uploads/ppdb/';
        $dokumen_fields = ['foto_siswa', 'foto_kk', 'foto_akta', 'foto_ijazah', 'foto_raport'];
        
        foreach ($dokumen_fields as $field) {
            if (!empty($data[$field])) {
                $file_path = $upload_dir . basename($data[$field]);
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        $sql = "DELETE FROM ppdb_pendaftaran WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Statistik PPDB
     * @param PDO $pdo
     * @return array
     */
    public static function getStatistics($pdo) {
        $id_ta_aktif = $_SESSION['id_ta_aktif'];
        
        $sql = "SELECT 
                    sumber_pendaftaran,
                    status,
                    COUNT(*) as jumlah
                FROM ppdb_pendaftaran
                WHERE id_ta = ?
                GROUP BY sumber_pendaftaran, status
                ORDER BY sumber_pendaftaran, status";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_aktif]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Re-Generate NIPD Massal untuk siswa berdasarkan TA Masuk tertentu.
     * 
     * Aturan:
     * - Hanya siswa dengan id_ta_masuk = $id_ta yang di-update NIPD-nya
     * - Tingkat ditentukan dari penempatan kelas pertama di TA masuk tersebut
     *   (X → 10, XI → 11, XII → 12)
     * - Siswa diurutkan alfabet dalam setiap tingkat
     * - Nomor urut NIPD bersifat KONTINU antar tingkat (XI mulai dari nomor setelah X)
     * - Format NIPD: [2-digit-tahun-mulai][2-digit-tahun-akhir][tingkat][3-digit-urut]
     *   Contoh: TA 2026/2027, tingkat 10, urut 1 → 262710001
     * - Username siswa (NISN) TIDAK diubah
     * 
     * @param PDO $pdo
     * @param int $id_ta ID Tahun Ajaran yang akan di-regenerate NIPD-nya
     * @return array ['jumlah' => int, 'preview' => array]
     */
    public static function regenerateNipdMassal($pdo, $id_ta) {
        // 1. Ambil nama TA untuk membuat prefix NIPD
        $stmt_ta = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
        $stmt_ta->execute([$id_ta]);
        $nama_ta = $stmt_ta->fetchColumn();

        if (!$nama_ta) {
            throw new Exception("Tahun ajaran dengan ID $id_ta tidak ditemukan.");
        }

        // Parse tahun dari nama_ta (format: "2026/2027 Ganjil" atau "2026/2027")
        if (!preg_match('/(\d{4})\/(\d{4})/', $nama_ta, $matches)) {
            throw new Exception("Format nama TA tidak dikenali: $nama_ta");
        }
        $prefix_base = substr($matches[1], -2) . substr($matches[2], -2); // "2627"

        // 2. Ambil semua siswa dengan id_ta_masuk = $id_ta,
        //    sekaligus tentukan tingkat dari penempatan pertama mereka
        $sql = "SELECT 
                    s.id_siswa, s.nama, s.nipd AS nipd_lama,
                    COALESCE(
                        CASE 
                            WHEN k.nama_kelas LIKE 'XII%' THEN 12
                            WHEN k.nama_kelas LIKE 'XI%' THEN 11
                            ELSE 10 
                        END,
                        10
                    ) AS tingkat,
                    k.nama_kelas
                FROM siswa s
                LEFT JOIN (
                    SELECT ps.id_siswa, ps.id_kelas
                    FROM penempatan_siswa ps
                    WHERE ps.id_ta = ?
                    GROUP BY ps.id_siswa
                ) AS first_ps ON first_ps.id_siswa = s.id_siswa
                LEFT JOIN kelas k ON k.id_kelas = first_ps.id_kelas
                WHERE s.id_ta_masuk = ?
                ORDER BY 
                    COALESCE(
                        CASE 
                            WHEN k.nama_kelas LIKE 'XII%' THEN 12
                            WHEN k.nama_kelas LIKE 'XI%' THEN 11
                            ELSE 10 
                        END,
                        10
                    ) ASC,
                    s.nama ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_ta]);
        $siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($siswa_list)) {
            throw new Exception("Tidak ada siswa dengan id_ta_masuk = $id_ta.");
        }

        // 3. Generate NIPD baru secara kontinu antar tingkat
        $urut_global = 0;
        $current_tingkat = null;
        $current_prefix = '';
        $preview = [];

        $pdo->beginTransaction();
        try {
            $stmt_update = $pdo->prepare("UPDATE siswa SET nipd = ? WHERE id_siswa = ?");

            foreach ($siswa_list as $s) {
                $urut_global++;
                $tingkat = $s['tingkat'];
                $prefix_nipd = $prefix_base . $tingkat; // "262710" / "262711" / "262712"
                $nipd_baru = $prefix_nipd . str_pad($urut_global, 3, '0', STR_PAD_LEFT);

                $stmt_update->execute([$nipd_baru, $s['id_siswa']]);

                $preview[] = [
                    'id_siswa'   => $s['id_siswa'],
                    'nama'       => $s['nama'],
                    'tingkat'    => $tingkat,
                    'nama_kelas' => $s['nama_kelas'] ?? '-',
                    'nipd_lama'  => $s['nipd_lama'],
                    'nipd_baru'  => $nipd_baru,
                ];
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        return [
            'jumlah'   => count($siswa_list),
            'nama_ta'  => $nama_ta,
            'preview'  => $preview,
        ];
    }

    /**
     * Ambil semua Tahun Ajaran untuk dropdown pilih TA pada regenerasi NIPD.
     */
    public static function getAllTa($pdo) {
        return $pdo->query("SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Preview regenerasi NIPD tanpa benar-benar menyimpan (dry-run).
     */
    public static function previewRegenerateNipd($pdo, $id_ta) {
        $stmt_ta = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
        $stmt_ta->execute([$id_ta]);
        $nama_ta = $stmt_ta->fetchColumn();

        if (!$nama_ta) {
            throw new Exception("Tahun ajaran tidak ditemukan.");
        }

        if (!preg_match('/(\d{4})\/(\d{4})/', $nama_ta, $matches)) {
            throw new Exception("Format nama TA tidak dikenali: $nama_ta");
        }
        $prefix_base = substr($matches[1], -2) . substr($matches[2], -2);

        $sql = "SELECT 
                    s.id_siswa, s.nama, s.nipd AS nipd_lama,
                    COALESCE(
                        CASE 
                            WHEN k.nama_kelas LIKE 'XII%' THEN 12
                            WHEN k.nama_kelas LIKE 'XI%' THEN 11
                            ELSE 10 
                        END,
                        10
                    ) AS tingkat,
                    k.nama_kelas
                FROM siswa s
                LEFT JOIN (
                    SELECT ps.id_siswa, ps.id_kelas
                    FROM penempatan_siswa ps
                    WHERE ps.id_ta = ?
                    GROUP BY ps.id_siswa
                ) AS first_ps ON first_ps.id_siswa = s.id_siswa
                LEFT JOIN kelas k ON k.id_kelas = first_ps.id_kelas
                WHERE s.id_ta_masuk = ?
                ORDER BY 
                    COALESCE(
                        CASE 
                            WHEN k.nama_kelas LIKE 'XII%' THEN 12
                            WHEN k.nama_kelas LIKE 'XI%' THEN 11
                            ELSE 10 
                        END,
                        10
                    ) ASC,
                    s.nama ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_ta]);
        $siswa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $urut_global = 0;
        $preview = [];
        foreach ($siswa_list as $s) {
            $urut_global++;
            $prefix_nipd = $prefix_base . $s['tingkat'];
            $nipd_baru = $prefix_nipd . str_pad($urut_global, 3, '0', STR_PAD_LEFT);
            $preview[] = [
                'id_siswa'   => $s['id_siswa'],
                'nama'       => $s['nama'],
                'tingkat'    => $s['tingkat'],
                'nama_kelas' => $s['nama_kelas'] ?? '-',
                'nipd_lama'  => $s['nipd_lama'],
                'nipd_baru'  => $nipd_baru,
                'berubah'    => ($s['nipd_lama'] !== $nipd_baru),
            ];
        }

        return [
            'jumlah'  => count($siswa_list),
            'nama_ta' => $nama_ta,
            'preview' => $preview,
        ];
    }
}

?>