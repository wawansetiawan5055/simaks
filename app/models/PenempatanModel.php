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
            // [KELOLA] Hanya untuk siswa yang berstatus 'Aktif' saat ini di master
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.jk, s.status_aktif
                    FROM siswa s
                    JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                    WHERE ps.id_kelas = ? AND ps.id_ta = ? 
                    AND s.status_aktif = 'Aktif'
                    ORDER BY s.nama ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_kelas, $id_ta]);
        } else {
            // [LIST/VIEW] Logic HISTORI: Tampilkan semua (Aktif, Alumni, Mutasi)
            // Namun status_aktif ditampilkan secara temporal (Aktif jika di masa lalu mereka belum lulus/keluar)
            $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.jk, s.id_ta_masuk,
                           CASE 
                             WHEN s.status_aktif = 'Aktif' THEN 'Aktif'
                             WHEN s.status_aktif = 'Lulus' AND sa.id_ta_lulus >= :ta1 THEN 'Aktif'
                             WHEN s.status_aktif = 'Keluar' AND sm.id_ta_mutasi > :ta2 THEN 'Aktif'
                             ELSE s.status_aktif
                           END AS status_aktif
                    FROM siswa s
                    JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                    LEFT JOIN siswa_alumni sa ON s.id_siswa = sa.id_siswa
                    LEFT JOIN siswa_mutasi sm ON s.id_siswa = sm.id_siswa
                    WHERE ps.id_kelas = :kelas AND ps.id_ta = :ta6
                    ORDER BY s.nama ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':ta1', $id_ta, PDO::PARAM_INT);
            $stmt->bindValue(':ta2', $id_ta, PDO::PARAM_INT);
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
        // 1. Hapus penempatan lama di TA ini agar tidak ada duplikasi
        $stmt_del = $pdo->prepare("DELETE FROM penempatan_siswa WHERE id_siswa = ? AND id_ta = ?");
        $stmt_del->execute([$id_siswa, $id_ta]);

        // 2. Masukkan penempatan baru
        $sql = "INSERT INTO penempatan_siswa (id_siswa, id_kelas, id_ta, status_penempatan) 
                VALUES (?, ?, ?, 'Aktif')";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_siswa, $id_kelas, $id_ta]);
    }

    /**
     * [BARU] Menyalin seluruh siswa dari rombel sumber ke rombel target
     */
    public static function copyRombel($pdo, $id_kelas_sumber, $id_ta_sumber, $id_kelas_target, $id_ta_target)
    {
        // 1. Ambil siswa aktif dari sumber.
        // Hanya siswa yang ada di tabel utama `siswa` dapat dimasukkan ke `penempatan_siswa`
        // karena ada foreign key `penempatan_siswa.id_siswa -> siswa.id_siswa`.
        $siswa_sumber = self::getAssignedStudents($pdo, $id_kelas_sumber, $id_ta_sumber, true);

        if (empty($siswa_sumber))
            return 0;

        $count = 0;
        $sql = "REPLACE INTO penempatan_siswa (id_siswa, id_kelas, id_ta) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // 2. Loop insert ke target
        foreach ($siswa_sumber as $siswa) {
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