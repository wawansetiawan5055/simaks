<?php
/**
 * PpdbModel.php - Model untuk PPDB (Terintegrasi Online & Manual)
 * Updated: Support untuk sumber_pendaftaran (online/manual)
 * Tabel: ppdb_pendaftaran
 */

class PpdbModel {
    
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
            ':tanggal_lahir' => $data['tanggal_lahir'],
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
                
                // Insert ke 'siswa'
                $stmt_siswa->execute([
                    $data_ppdb['nama_lengkap'], 
                    $data_ppdb['nisn'], 
                    $nipd_baru,
                    $data_ppdb['nik'], 
                    $data_ppdb['jenis_kelamin'], 
                    $data_ppdb['tempat_lahir'],
                    $data_ppdb['tanggal_lahir'], 
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
}
?>