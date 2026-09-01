<?php
class PenugasanModel
{

    // =========================================================
    // WALI KELAS (Menggunakan tabel baru 'penugasan_wali_kelas')
    // =========================================================

    public static function walas_available_guru($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT id_guru, nama FROM guru WHERE status='Aktif' AND id_guru NOT IN (
                SELECT id_guru FROM penugasan_wali_kelas WHERE id_ta=? AND jenis_tugas = 'Wali Kelas'
            ) ORDER BY nama ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function walas_available_kelas($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? AND id_kelas NOT IN (
                SELECT id_kelas FROM penugasan_wali_kelas WHERE id_ta=? AND jenis_tugas = 'Wali Kelas'
            ) ORDER BY tingkat, nama_kelas ASC"
        );
        $stmt->execute([$id_ta, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function walas_list($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT pg.id_penugasan_wali_kelas, g.nama, k.nama_kelas, k.tingkat
                FROM penugasan_wali_kelas pg
                JOIN guru g ON pg.id_guru = g.id_guru
                JOIN kelas k ON pg.id_kelas = k.id_kelas
                WHERE pg.id_ta = ? AND pg.jenis_tugas = 'Wali Kelas'
                ORDER BY k.tingkat, k.nama_kelas ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function walas_save($pdo, $id_guru, $id_kelas, $id_ta)
    {
        $stmt = $pdo->prepare(
            "INSERT INTO penugasan_wali_kelas (id_guru, id_kelas, id_ta, jenis_tugas) 
             VALUES (?, ?, ?, 'Wali Kelas')"
        );
        $stmt->execute([$id_guru, $id_kelas, $id_ta]);
    }

    /**
     * [FUNGSI BARU DITAMBAHKAN]
     * Menghapus data Wali Kelas dari tabel penugasan_wali_kelas
     */
    public static function walas_delete($pdo, $id_penugasan_wali_kelas)
    {
        $stmt = $pdo->prepare("DELETE FROM penugasan_wali_kelas WHERE id_penugasan_wali_kelas = ?");
        return $stmt->execute([$id_penugasan_wali_kelas]);
    }

    // =========================================================
    // GURU MAPEL (Diasumsikan masih menggunakan tabel 'guru_mapel')
    // =========================================================

    public static function all_guru($pdo)
    {
        return $pdo->query("SELECT id_guru, nama FROM guru WHERE status='Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all_mapel($pdo)
    {
        // Query ini diperbarui untuk mengambil kategori_mapel
        return $pdo->query("SELECT id_mapel, nama_mapel, kategori_mapel FROM mapel ORDER BY kategori_mapel, nama_mapel ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function guru_mapel_list($pdo, $id_ta)
    {
        // Menggunakan SEPARATOR ' | ' agar mapel yang mengandung koma (seperti PJOK) tidak terpecah
        $stmt = $pdo->prepare(
            "SELECT 
                gm.id_guru, 
                g.nama AS nama_guru, 
                GROUP_CONCAT(DISTINCT m.nama_mapel ORDER BY m.nama_mapel SEPARATOR ' | ') AS daftar_mapel,
                GROUP_CONCAT(DISTINCT k.nama_kelas ORDER BY k.tingkat, k.nama_kelas SEPARATOR ', ') AS daftar_kelas,
                GROUP_CONCAT(DISTINCT gm.id_mapel SEPARATOR ',') AS mapel_ids,
                GROUP_CONCAT(DISTINCT gm.id_kelas SEPARATOR ',') AS kelas_ids,
                GROUP_CONCAT(gm.id_guru_mapel SEPARATOR ',') AS ids_guru_mapel
            FROM guru_mapel gm
            JOIN guru g ON gm.id_guru = g.id_guru
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            LEFT JOIN kelas k ON gm.id_kelas = k.id_kelas
            WHERE gm.id_ta = ?
            GROUP BY gm.id_guru
            ORDER BY g.nama ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function guru_mapel_save($pdo, $id_guru, $id_mapel, $id_kelas, $id_ta)
    {
        $stmt = $pdo->prepare("INSERT INTO guru_mapel (id_guru, id_mapel, id_kelas, id_ta) VALUES (?,?,?,?)");
        $stmt->execute([$id_guru, $id_mapel, $id_kelas, $id_ta]);
    }

    /**
     * [FUNGSI BARU]
     * Memperbarui penugasan guru mapel dengan sinkronisasi kombinasi (id_mapel, id_kelas)
     */
    public static function guru_mapel_update($pdo, $id_guru, array $id_mapel_list, array $id_kelas_list, $id_ta)
    {
        $pdo->beginTransaction();
        try {
            $stmt_old = $pdo->prepare("SELECT id_guru_mapel, id_mapel, id_kelas FROM guru_mapel WHERE id_guru = ? AND id_ta = ?");
            $stmt_old->execute([$id_guru, $id_ta]);
            $existing = $stmt_old->fetchAll(PDO::FETCH_ASSOC);

            $existing_combos = [];
            foreach ($existing as $ex) {
                $key = $ex['id_mapel'] . '_' . ($ex['id_kelas'] ?? 0);
                $existing_combos[$key] = $ex['id_guru_mapel'];
            }

            $new_combos = [];
            foreach ($id_mapel_list as $m) {
                foreach ($id_kelas_list as $k) {
                    $key = $m . '_' . $k;
                    $new_combos[$key] = ['id_mapel' => $m, 'id_kelas' => $k];
                }
            }

            // 1. Hapus yang tidak dipilih lagi
            foreach ($existing_combos as $key => $id_gm) {
                if (!isset($new_combos[$key])) {
                    // Hapus slot jadwal mengajar lama yang terkait agar tidak menjadi data yatim/rusak
                    $pdo->prepare("DELETE FROM jadwal_mengajar WHERE id_guru_mapel = ?")->execute([$id_gm]);
                    $stmt_del = $pdo->prepare("DELETE FROM guru_mapel WHERE id_guru_mapel = ?");
                    $stmt_del->execute([$id_gm]);
                }
            }

            // 2. Tambah kombinasi baru yang belum ada
            foreach ($new_combos as $key => $combo) {
                if (!isset($existing_combos[$key])) {
                    $stmt_ins = $pdo->prepare("INSERT INTO guru_mapel (id_guru, id_mapel, id_kelas, id_ta) VALUES (?, ?, ?, ?)");
                    $stmt_ins->execute([$id_guru, $combo['id_mapel'], $combo['id_kelas'], $id_ta]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * [FUNGSI BARU DITAMBAHKAN]
     * Menghapus data Guru Mapel dari tabel guru_mapel
     */
    public static function guru_mapel_delete($pdo, $id_guru_mapel)
    {
        $stmt = $pdo->prepare("DELETE FROM guru_mapel WHERE id_guru_mapel = ?");
        return $stmt->execute([$id_guru_mapel]);
    }

    // Helper: Ambil mapel yg diajar oleh guru tertentu (untuk filter CP/TP dll)
    public static function getMapelDiajarGuru($pdo, $id_guru, $id_ta)
    {
        $sql = "SELECT DISTINCT m.* 
                FROM guru_mapel gm
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE gm.id_guru = ? AND gm.id_ta = ?
                ORDER BY m.nama_mapel ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // =========================================================
    // TUGAS TAMBAHAN / JABATAN (MENGGUNAKAN TABEL penugasan_jabatan)
    // =========================================================

    public static function jabatan_list($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT 
                pj.id_guru, 
                g.nama AS nama_guru, 
                GROUP_CONCAT(pj.jenis_jabatan ORDER BY pj.jenis_jabatan SEPARATOR ' | ') AS daftar_jabatan,
                GROUP_CONCAT(pj.id_penugasan_jabatan SEPARATOR ',') AS ids_penugasan_jabatan
            FROM penugasan_jabatan pj
            JOIN guru g ON pj.id_guru = g.id_guru
            WHERE pj.id_ta = ?
            GROUP BY pj.id_guru
            ORDER BY g.nama ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function jabatan_save($pdo, $id_guru, $jenis_jabatan, $id_ta)
    {
        // Cek duplikasi: Satu jabatan satu orang? Atau satu orang bisa rangkap?
        // Asumsi: Satu jabatan bisa dipegang satu orang (misal Kepala Lab ada banyak).
        // Tapi "Waka Kurikulum" cuma satu.
        // Untuk simpelnya, allow insert.
        $stmt = $pdo->prepare(
            "INSERT INTO penugasan_jabatan (id_guru, id_ta, jenis_jabatan) 
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$id_guru, $id_ta, $jenis_jabatan]);
    }

    public static function jabatan_delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM penugasan_jabatan WHERE id_penugasan_jabatan = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Helper untuk mengambil nama guru berdasarkan jabatan tertentu
     * Digunakan di Laporan
     */
    public static function getGuruByJabatan($pdo, $jenis_jabatan, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT g.nama FROM penugasan_jabatan pj
             JOIN guru g ON pj.id_guru = g.id_guru
             WHERE pj.jenis_jabatan = ? AND pj.id_ta = ?
             LIMIT 1"
        );
        $stmt->execute([$jenis_jabatan, $id_ta]);
        return $stmt->fetchColumn();
    }

    // =========================================================
    // PEMBINA NON-AKADEMIK (MENGGUNAKAN TABEL penugasan_pembina)
    // =========================================================

    public static function pembina_list($pdo, $id_ta)
    {
        $stmt = $pdo->prepare(
            "SELECT pp.id_penugasan_pembina, mk.nama_kegiatan, mk.jenis_kegiatan, g.nama AS nama_guru
                FROM penugasan_pembina pp
                JOIN master_kegiatan mk ON pp.id_kegiatan = mk.id_kegiatan
                JOIN guru g ON pp.id_guru = g.id_guru
                WHERE pp.id_ta = ?
                ORDER BY mk.jenis_kegiatan, mk.nama_kegiatan ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function pembina_save($pdo, $id_kegiatan, $id_guru, $id_ta)
    {
        // 1 Kegiatan bisa punya BANYAK pembina?
        // User request: "pembina otomatis sesuai penugasan".
        // Biasanya 1 kegiatan = 1 pembina utama (atau koordinator).
        // Tapi ekskul Pramuka bisa 3 pembina.
        // Asumsi: Allow multiple pembina per activity.

        $stmt = $pdo->prepare(
            "INSERT INTO penugasan_pembina (id_kegiatan, id_guru, id_ta) 
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$id_kegiatan, $id_guru, $id_ta]);
    }

    public static function pembina_delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM penugasan_pembina WHERE id_penugasan_pembina = ?");
        return $stmt->execute([$id]);
    }

    // LIST MASTER KEGIATAN (Untuk Dropdown di Form)
    public static function getMasterKegiatanNonAkademik($pdo)
    {
        return $pdo->query(
            "SELECT * FROM master_kegiatan 
             WHERE jenis_kegiatan IN ('Ekstrakurikuler','Kokulikuler','Kewirausahaan','Tahfidz') 
             ORDER BY jenis_kegiatan, nama_kegiatan ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getJabatanByKategori($pdo, $kategori)
    {
        $stmt = $pdo->prepare("SELECT * FROM keuangan_master_jabatan WHERE kategori = ? ORDER BY nama_jabatan ASC");
        $stmt->execute([$kategori]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all_kelas($pdo, $id_ta)
    {
        $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas ASC");
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}