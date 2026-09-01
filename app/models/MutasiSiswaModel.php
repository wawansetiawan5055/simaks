<?php
class MutasiSiswaModel
{

    // Mengambil siswa yang masih aktif di kelas tertentu
    public static function getSiswaAktifByKelas($pdo, $id_kelas, $id_ta = null)
    {
        if (!empty($id_ta)) {
            $sql = "SELECT s.id_siswa, s.nama, s.nisn 
                    FROM siswa s
                    JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                    WHERE ps.id_kelas = ? AND ps.id_ta = ? AND s.status_aktif = 'Aktif'
                    ORDER BY s.nama ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_kelas, $id_ta]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($res)) return $res;
        }

        // Fallback: id_kelas is unique per class, fetch active students
        $sql = "SELECT s.id_siswa, s.nama, s.nisn 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                WHERE ps.id_kelas = ? AND (s.status_aktif = 'Aktif' OR s.status_aktif IS NULL)
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kelas]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper: Copy row antar tabel dengan kolom yang cocok
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

        $placeholders = implode(',', array_fill(0, count($sourceParams), '?'));
        $sql = "INSERT INTO $targetTable ($colStr) SELECT $colStr FROM $sourceTable WHERE id_siswa IN ($placeholders)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($sourceParams);

        if ($deleteSource) {
            $delSql = "DELETE FROM $sourceTable WHERE id_siswa IN ($placeholders)";
            $pdo->prepare($delSql)->execute($sourceParams);
        }
    }

    // Menyimpan data mutasi (Pindah data dari siswa -> siswa_mutasi)
    public static function saveMutasiKeluar($pdo, $data)
    {
        $pdo->beginTransaction();
        try {
            // [PENTING] Matikan cek Foreign Key sementara agar bisa menghapus data master siswa
            // namun data history (nilai/absensi) tetap ada (menjadi orphan / yatim).
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 1. Copy data (TIDAK MENGHAPUS master agar history penempatan terjaga)
            self::moveRow($pdo, [$data['id_siswa']], 'siswa', 'siswa_mutasi', false);

            // 2. Update status di tabel master siswa
            $stmt_m = $pdo->prepare("UPDATE siswa SET status_aktif = 'Keluar' WHERE id_siswa = ?");
            $stmt_m->execute([$data['id_siswa']]);

            // 3. Update informasi mutasi di tabel arsip
            $stmt_update = $pdo->prepare("UPDATE siswa_mutasi SET 
                tgl_mutasi = ?, 
                alasan_mutasi = ?,
                jenis_mutasi = ?,
                id_ta_mutasi = ?,
                id_kelas_asal = ?,
                status_aktif = 'Keluar'
                WHERE id_siswa = ?");
            // Mapping data:
            $stmt_update->execute([
                $data['tanggal_mutasi'],
                $data['alasan'],
                $data['jenis_mutasi'],
                $data['id_ta_mutasi'] ?? null, // NEW: TA mutasi
                $data['id_kelas_asal'] ?? null, // NEW: Kelas asal
                $data['id_siswa']
            ]);

            // 3. [CATATAN] Kita TIDAK MENGHAPUS data dari penempatan_siswa agar histori tetap ada.
            // $pdo->prepare("DELETE FROM penempatan_siswa WHERE id_siswa = ?")->execute([$data['id_siswa']]);

            // Hidupkan kembali cek Foreign Key
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            // Hidupkan kembali cek FK jika terjadi error
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

            // Cek Constraint Violation
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                die("Gagal memutasikan siswa: Data siswa sedang digunakan (FK Constraint). Error: " . $e->getMessage());
            }
            die("Gagal menyimpan data mutasi: " . $e->getMessage());
        }
    }

    // Mengambil riwayat siswa yang sudah mutasi
    public static function getRiwayatMutasi($pdo, $id_ta)
    {
        $sql = "SELECT sm.*, k.nama_kelas 
                FROM siswa_mutasi sm
                LEFT JOIN kelas k ON sm.id_kelas_asal = k.id_kelas
                WHERE sm.id_ta_mutasi = ? 
                ORDER BY sm.tgl_mutasi DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($pdo, $id_siswa)
    {
        $stmt = $pdo->prepare("SELECT * FROM siswa_mutasi WHERE id_siswa = ?");
        $stmt->execute([$id_siswa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($pdo, $data)
    {
        $stmt = $pdo->prepare("UPDATE siswa_mutasi SET 
            tgl_mutasi = ?, 
            alasan_mutasi = ?, 
            jenis_mutasi = ? 
            WHERE id_siswa = ?");
        return $stmt->execute([
            $data['tanggal_mutasi'],
            $data['alasan'],
            $data['jenis_mutasi'],
            $data['id_siswa']
        ]);
    }

    // Membatalkan mutasi (Kembalikan data dari siswa_mutasi -> siswa)
    public static function restoreMutasi($pdo, $id_siswa)
    {
        $pdo->beginTransaction();
        try {
            // 1. Ambil data history dari siswa_mutasi sebelum dihapus
            $mutasi = self::find($pdo, $id_siswa);
            if (!$mutasi) {
                throw new Exception("Data mutasi tidak ditemukan.");
            }

            $id_kelas = $mutasi['id_kelas_asal'];
            $id_ta = $mutasi['id_ta_mutasi'];

            // 2. Matikan FK Checks
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

            // 3. Move row back from siswa_mutasi -> siswa
            self::moveRow($pdo, [$id_siswa], 'siswa_mutasi', 'siswa', true);

            // 4. Update status di tabel siswa jadi Aktif kembali
            $stmt = $pdo->prepare("UPDATE siswa SET status_aktif = 'Aktif' WHERE id_siswa = ?");
            $stmt->execute([$id_siswa]);

            // 5. Kembalikan ke penempatan_siswa jika data id_kelas & id_ta ada
            if ($id_kelas && $id_ta) {
                // Hapus data lama jika ada (mencegah duplikasi) lalu insert baru
                $pdo->prepare("DELETE FROM penempatan_siswa WHERE id_siswa = ? AND id_ta = ?")->execute([$id_siswa, $id_ta]);
                $stmt_ins = $pdo->prepare("INSERT INTO penempatan_siswa (id_siswa, id_kelas, id_ta) VALUES (?, ?, ?)");
                $stmt_ins->execute([$id_siswa, $id_kelas, $id_ta]);
            }

            // Revive FK Checks
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            throw $e;
        }
    }
}