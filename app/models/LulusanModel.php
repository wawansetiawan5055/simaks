<?php
class LulusanModel
{

    /**
     * Mengambil daftar siswa tingkat akhir (Kelas XII) yang masih Aktif
     */
    public static function getCalonLulusan($pdo, $id_ta_aktif)
    {
        // Kita ambil siswa yang ada di penempatan tahun ini, di kelas XII, dan statusnya Aktif
        $sql = "SELECT s.id_siswa, s.nama, s.nisn, s.nipd, k.nama_kelas, k.tingkat
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                WHERE ps.id_ta = ? 
                  AND k.tingkat = 'XII' 
                  AND s.status_aktif = 'Aktif'
                ORDER BY k.nama_kelas, s.nama";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta_aktif]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAlumni($pdo)
    {
        // Ambil dari tabel arsip dengan join kelas agar muncul nama kelas akhirnya
        try {
            $sql = "SELECT sa.*, k.nama_kelas 
                    FROM siswa_alumni sa
                    LEFT JOIN kelas k ON sa.id_kelas_akhir = k.id_kelas
                    ORDER BY sa.tahun_lulus DESC, sa.nama ASC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Mengambil daftar alumni dikelompokkan per tahun lulus
     */
    public static function getAlumniGroupedByYear($pdo)
    {
        try {
            $sql = "SELECT 
                        tahun_lulus,
                        COUNT(id_siswa) as total,
                        SUM(CASE WHEN jk = 'Laki-laki' THEN 1 ELSE 0 END) as laki_laki,
                        SUM(CASE WHEN jk = 'Perempuan' THEN 1 ELSE 0 END) as perempuan
                    FROM siswa_alumni 
                    WHERE tahun_lulus IS NOT NULL AND tahun_lulus != 0
                    GROUP BY tahun_lulus
                    ORDER BY tahun_lulus ASC";

            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Helper: Copy row antar tabel dengan kolom yang cocok
     */
    private static function moveRow($pdo, $sourceParams, $sourceTable, $targetTable, $deleteSource = false)
    {
        // 1. Ambil nama kolom target
        $q = $pdo->prepare("DESCRIBE $targetTable");
        $q->execute();
        $targetCols = $q->fetchAll(PDO::FETCH_COLUMN);

        // 2. Ambil nama kolom source
        $q2 = $pdo->prepare("DESCRIBE $sourceTable");
        $q2->execute();
        $sourceCols = $q2->fetchAll(PDO::FETCH_COLUMN);

        // 3. Cari interseksi kolom
        $commonCols = array_intersect($targetCols, $sourceCols);
        $colStr = implode(',', $commonCols);

        // 4. Lakukan Copy
        // PENTING: Gunakan loop jika multi-id. Tapi fungsi ini untuk single/multi tergantung where.

        // Jika sourceParams array of IDs:
        // Kita loop di luar atau gunakan IN.
        // Untuk sederhananya, fungsi ini memindahkan SATU ID atau BANYAK ID? 
        // Mari buat logic insert select dinamis.

        $placeholders = implode(',', array_fill(0, count($sourceParams), '?'));
        $sql = "INSERT INTO $targetTable ($colStr) SELECT $colStr FROM $sourceTable WHERE id_siswa IN ($placeholders)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($sourceParams);

        if ($deleteSource) {
            $delSql = "DELETE FROM $sourceTable WHERE id_siswa IN ($placeholders)";
            $pdo->prepare($delSql)->execute($sourceParams);
        }
    }

    /**
     * Proses Meluluskan Siswa (Pindah ke siswa_alumni)
     */
    /**
     * Proses Meluluskan Siswa (Pindah ke siswa_alumni)
     * @param PDO $pdo
     * @param array $ids_siswa
     * @param int|null $custom_tahun_lulus Tahun lulus manual (opsional), jika null pakai date("Y")
     */
    public static function luluskanSiswa($pdo, $ids_siswa, $custom_tahun_lulus = null)
    {
        if (empty($ids_siswa))
            return false;

        $pdo->beginTransaction();
        try {
            // Disable FK Check
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 1. Pindahkan data
            // Gunakan loop untuk memastikan aman atau pass array ke moveRow jika support
            // Helper moveRow kita support array.
            self::moveRow($pdo, $ids_siswa, 'siswa', 'siswa_alumni', true);

            // 2. Update status di tabel alumni 
            // Dan set tahun lulus (dari parameter atau current year)
            $placeholders = implode(',', array_fill(0, count($ids_siswa), '?'));

            // LOGIKA TAHUN LULUS:
            // Jika custom_tahun_lulus ada, gunakan itu. Jika tidak, gunakan tahun saat ini.
            $tahun_lulus = $custom_tahun_lulus ? $custom_tahun_lulus : date('Y');

            // [UPDATE] Simpan id_kelas_akhir dan id_ta_lulus dari penempatan terakhir sebelum dihapus
            $sqlUpd = "UPDATE siswa_alumni sa 
                       SET status_aktif = 'Lulus', 
                           tahun_lulus = ?,
                           id_kelas_akhir = (SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = sa.id_siswa ORDER BY id_ta DESC LIMIT 1),
                           id_ta_lulus = (SELECT id_ta FROM penempatan_siswa WHERE id_siswa = sa.id_siswa ORDER BY id_ta DESC LIMIT 1)
                       WHERE id_siswa IN ($placeholders)";
            $params = array_merge([$tahun_lulus], $ids_siswa);
            $pdo->prepare($sqlUpd)->execute($params);

            // 3. Hapus penempatan siswa (HANYA UNTUK TA AKTIF jika diperlukan, 
            // namun sebaiknya tetap dipertahankan agar history rombel tidak hilang)
            // [UPDATE] Kita TIDAK MENGHAPUS agar history penempatan tetap ada.
            // $sqlDelPenempatan = "DELETE FROM penempatan_siswa WHERE id_siswa IN ($placeholders)";
            // $pdo->prepare($sqlDelPenempatan)->execute($ids_siswa);

            // Enable FK Check
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                throw new Exception("Gagal meluluskan siswa: Data masih terhubung dengan data lain (Constraint).");
            }
            throw new Exception("Gagal proses lulus: " . $e->getMessage());
        }
    }

    /**
     * Batalkan Kelulusan (Kembalikan ke tabel siswa)
     */
    public static function batalkanKelulusan($pdo, $id_siswa)
    {
        $pdo->beginTransaction();
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 1. Pindahkan balik
            self::moveRow($pdo, [$id_siswa], 'siswa_alumni', 'siswa', true);

            // 2. Reset status
            $pdo->prepare("UPDATE siswa SET status_aktif = 'Aktif' WHERE id_siswa = ?")->execute([$id_siswa]);

            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            throw $e;
        }
    }
}