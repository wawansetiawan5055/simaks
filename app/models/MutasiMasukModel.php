<?php
class MutasiMasukModel {
    
    public static function save($pdo, $data) {
        
        $sql = "INSERT INTO mutasi_masuk 
                (id_ta, nama_lengkap, nisn, nik, jk, tempat_lahir, tanggal_lahir, 
                 sekolah_asal, tingkat_sebelumnya, pindah_ke_tingkat, tanggal_mutasi, alasan_mutasi, 
                 id_kelas_tujuan, status_penerimaan)
                VALUES 
                (:id_ta, :nama_lengkap, :nisn, :nik, :jk, :tempat_lahir, :tanggal_lahir, 
                 :sekolah_asal, :tingkat_sebelumnya, :pindah_ke_tingkat, :tanggal_mutasi, :alasan_mutasi, 
                 :id_kelas_tujuan, 'Pending')"; // Status default 'Pending'

        $stmt = $pdo->prepare($sql);

            $params = [
            ':id_ta' => $data['id_ta'],
            ':nama_lengkap' => $data['nama_lengkap'],
            ':nisn' => $data['nisn'],
            ':nik' => $data['nik'],
            ':jk' => $data['jk'],
            ':tempat_lahir' => $data['tempat_lahir'],
            ':tanggal_lahir' => $data['tanggal_lahir'],
            ':sekolah_asal' => $data['sekolah_asal'],
            ':tingkat_sebelumnya' => $data['tingkat_sebelumnya'],
            ':pindah_ke_tingkat' => $data['pindah_ke_tingkat'],
            ':tanggal_mutasi' => $data['tanggal_mutasi'],
            ':alasan_mutasi' => $data['alasan_mutasi'],
            ':id_kelas_tujuan' => $data['id_kelas_tujuan'] ?? null, // NEW
        ];
        
        return $stmt->execute($params);
    }
      public static function all($pdo) {
        $sql = "SELECT mm.*, ta.nama_ta 
                FROM mutasi_masuk mm
                JOIN tahun_ajaran ta ON mm.id_ta = ta.id_ta
                ORDER BY mm.tanggal_pengajuan DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
/**
     * BARU: Mengambil satu data mutasi berdasarkan ID-nya
     */
    public static function getById($pdo, $id_mutasi) {
        $stmt = $pdo->prepare("SELECT * FROM mutasi_masuk WHERE id_mutasi = ?");
        $stmt->execute([$id_mutasi]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * BARU: Fungsi "Promosi"
     * Memindahkan data dari mutasi_masuk ke tabel master siswa dan profil_siswa
     */
    public static function promoteToMaster($pdo, $id_mutasi) {
        // 1. Ambil data calon siswa dari tabel mutasi
        $data_mutasi = self::getById($pdo, $id_mutasi);
        if (!$data_mutasi || $data_mutasi['status_penerimaan'] !== 'Pending') {
            throw new Exception("Data mutasi tidak ditemukan atau sudah diproses.");
        }

        try {
            // 2. Mulai Transaksi Database
            $pdo->beginTransaction();

            // 3. Buat NIPD (Nomor Induk Pokok) baru
            // (Ini contoh sederhana, sesuaikan dengan format NIPD sekolah Anda)
            $tahun_masuk = date('Y', strtotime($data_mutasi['tanggal_mutasi']));
            $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE nipd LIKE ?");
            $prefix = $tahun_masuk . "-";
            $stmt_count->execute([$prefix . '%']);
            $count = $stmt_count->fetchColumn() + 1;
            $nipd_baru = $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);

            // 4. INSERT ke tabel master 'siswa'
            $sql_siswa = "INSERT INTO siswa 
                            (nama, nisn, nipd, nik, jk, tempat_lahir, tanggal_lahir, sekolah_asal, status_aktif, id_ta_masuk)
                          VALUES 
                            (?, ?, ?, ?, ?, ?, ?, ?, 'Aktif', ?)";
            $stmt_siswa = $pdo->prepare($sql_siswa);
            $stmt_siswa->execute([
                $data_mutasi['nama_lengkap'],
                $data_mutasi['nisn'],
                $nipd_baru, // NIPD baru
                $data_mutasi['nik'],
                $data_mutasi['jk'],
                $data_mutasi['tempat_lahir'],
                $data_mutasi['tanggal_lahir'],
                $data_mutasi['sekolah_asal'],
                $data_mutasi['id_ta'] // id_ta_masuk
            ]);
            
            // 5. Ambil ID siswa baru
            $id_siswa_baru = $pdo->lastInsertId();

            // 6. INSERT ke tabel 'profil_siswa' (data keluarga)
            // (Saat ini kita belum mengumpulkan data Ayah/Ibu di form mutasi, jadi kita kosongi)
            $sql_profil = "INSERT INTO profil_siswa (id_siswa) VALUES (?)";
            $stmt_profil = $pdo->prepare($sql_profil);
            $stmt_profil->execute([$id_siswa_baru]);

            // 7. UPDATE status di tabel 'mutasi_masuk'
            $sql_update_mutasi = "UPDATE mutasi_masuk SET status_penerimaan = 'Diterima', id_siswa_master = ? WHERE id_mutasi = ?";
            $stmt_update = $pdo->prepare($sql_update_mutasi);
            $stmt_update->execute([$id_siswa_baru, $id_mutasi]);

            // 8. Selesaikan Transaksi
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            // 9. Jika gagal, batalkan semua
            $pdo->rollBack();
            throw $e; // Lemparkan error agar Controller bisa menangkap
        }
    }
}