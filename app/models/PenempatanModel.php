<?php
class PenempatanModel
{

    /**
     * Mengambil daftar semua kelas untuk dropdown filter
     */
    public static function getKelasList($pdo, $id_ta)
    {
        $sql = "SELECT k.id_kelas, k.nama_kelas, k.tingkat, g.nama AS nama_walas
                FROM kelas k
                LEFT JOIN (
                    SELECT id_kelas, id_guru FROM penugasan_wali_kelas WHERE id_ta = ? AND jenis_tugas = 'Wali Kelas'
                ) pg ON k.id_kelas = pg.id_kelas
                LEFT JOIN guru g ON pg.id_guru = g.id_guru
                WHERE k.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil siswa yang BELUM ditempatkan di kelas manapun pada TA ini
     */
    public static function getUnassignedStudents($pdo, $id_ta)
    {
        // [UPDATE] Filter: Hanya tampilkan siswa yang SUDAH masuk pada TA ini (id_ta_masuk <= current_ta)
        $sql = "SELECT id_siswa, nama, nisn, jk, status_aktif, id_ta_masuk 
                FROM siswa 
                WHERE status_aktif = 'Aktif' 
                AND (id_ta_masuk IS NULL OR id_ta_masuk <= ?)
                AND id_siswa NOT IN (
                    SELECT id_siswa FROM penempatan_siswa WHERE id_ta = ?
                )
                ORDER BY nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil siswa yang SUDAH ditempatkan di kelas spesifik
     */
    public static function getAssignedStudents($pdo, $id_kelas, $id_ta, $activeOnly = false)
    {
        if ($activeOnly) {
            // Jika hanya yang aktif, cukup query tabel 'siswa' dan cek statusnya
            // Dan pastikan id_ta_masuk <= id_ta (Bukan siswa masa depan)
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.jk, s.status_aktif
                    FROM siswa s
                    JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                    WHERE ps.id_kelas = ? AND ps.id_ta = ? 
                    AND s.status_aktif = 'Aktif'
                    AND (s.id_ta_masuk IS NULL OR s.id_ta_masuk <= ?)
                    ORDER BY s.nama ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_kelas, $id_ta, $id_ta]);
        } else {
            // Logic HISTORI: Tampilkan semua (Aktif, Alumni, Mutasi)
            // Tapi difilter agar Alumni/Mutasi hanya muncul di TA mereka terakhir aktif
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.jk, s.status_aktif, s.id_ta_masuk
                    FROM (
                        SELECT id_siswa, nama, nisn, jk, status_aktif, id_ta_masuk FROM siswa 
                        WHERE id_ta_masuk IS NULL OR id_ta_masuk <= :ta1
                        
                        UNION ALL
                        
                        SELECT id_siswa, nama, nisn, jk, status_aktif, id_ta_masuk FROM siswa_alumni 
                        WHERE id_ta_lulus >= :ta2 AND (id_ta_masuk IS NULL OR id_ta_masuk <= :ta3)
                        
                        UNION ALL
                        
                        SELECT id_siswa, nama, nisn, jk, status_aktif, id_ta_masuk FROM siswa_mutasi 
                        WHERE id_ta_mutasi >= :ta4 AND (id_ta_masuk IS NULL OR id_ta_masuk <= :ta5)
                    ) s
                    JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                    WHERE ps.id_kelas = :kelas AND ps.id_ta = :ta6
                    ORDER BY s.nama ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':ta1', $id_ta, PDO::PARAM_INT);
            $stmt->bindValue(':ta2', $id_ta, PDO::PARAM_INT);
            $stmt->bindValue(':ta3', $id_ta, PDO::PARAM_INT);
            $stmt->bindValue(':ta4', $id_ta, PDO::PARAM_INT);
            $stmt->bindValue(':ta5', $id_ta, PDO::PARAM_INT);
            $stmt->bindValue(':kelas', $id_kelas, PDO::PARAM_INT);
            $stmt->bindValue(':ta6', $id_ta, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menghapus penempatan siswa dari TA (mengembalikan ke 'Belum Ditempatkan')
     */
    public static function unassignStudent($pdo, $id_siswa, $id_ta)
    {
        $sql = "DELETE FROM penempatan_siswa WHERE id_siswa = ? AND id_ta = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_siswa, $id_ta]);
    }

    /**
     * Menetapkan (Assign) siswa ke kelas baru.
     * Ini menangani (INSERT) atau memindahkan (UPDATE)
     */
    public static function assignStudent($pdo, $id_siswa, $id_kelas, $id_ta)
    {
        // Gunakan REPLACE INTO (atau ON DUPLICATE KEY UPDATE) untuk efisiensi
        // Ini akan menghapus data lama (jika ada) dan memasukkan data baru
        $sql = "REPLACE INTO penempatan_siswa (id_siswa, id_kelas, id_ta) 
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_siswa, $id_kelas, $id_ta]);
    }

    /**
     * [BARU] Menyalin seluruh siswa dari rombel sumber ke rombel target
     */
    public static function copyRombel($pdo, $id_kelas_sumber, $id_ta_sumber, $id_kelas_target, $id_ta_target)
    {
        // 1. Ambil siswa dari sumber
        $siswa_sumber = self::getAssignedStudents($pdo, $id_kelas_sumber, $id_ta_sumber);

        if (empty($siswa_sumber))
            return 0;

        $count = 0;
        $sql = "REPLACE INTO penempatan_siswa (id_siswa, id_kelas, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // 2. Loop insert ke target
        foreach ($siswa_sumber as $siswa) {
            // Cek apakah siswa masih aktif? (Opsional, tapi sebaiknya ya)
            // getAssignedStudents sebenarnya sudah join siswa, tapi tidak filter status aktif secara eksplisit di querynya?
            // Mari cek method getAssignedStudents: "SELECT ... FROM siswa s JOIN ... WHERE ... ORDER ..."
            // Tidak ada "AND s.status_aktif = 'Aktif'" di sana. Sebaiknya kita filter di sini atau update getAssignedStudents.
            // Update: Lebih aman filter di sini atau biarkan user memindahkan semua lalu hapus manual yg tidak aktif.
            // User request: "salin semua". Mari salin semua yang ada di rombel sumber.

            if ($stmt->execute([$siswa['id_siswa'], $id_kelas_target, $id_ta_target])) {
                $count++;
            }
        }
        return $count;
    }

    public static function getTahunAjaranList($pdo)
    {
        return $pdo->query("SELECT * FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>