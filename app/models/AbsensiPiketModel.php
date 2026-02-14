<?php
class AbsensiPiketModel {

    /**
     * Mengambil daftar siswa berdasarkan kelas pada tahun ajaran aktif.
     */
    public static function getSiswaByKelas($pdo, $id_kelas, $id_ta) {
        $sql = "SELECT s.id_siswa, s.nama, s.nisn 
                 FROM siswa s
                 JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                 WHERE ps.id_kelas = ? AND ps.id_ta = ? AND s.status_aktif = 'Aktif'
                 ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [REVISI KRUSIAL] Menyimpan semua status absensi piket, termasuk Hadir.
     */
    public static function save($pdo, $data) {
        $pdo->beginTransaction();
        try {
            // Hapus data absensi lama yang persis pada kelas/tanggal ini untuk memastikan tidak ada duplikasi
            $deleteStmt = $pdo->prepare("DELETE FROM absensi_siswa_piket WHERE id_kelas = ? AND tanggal = ?");
            $deleteStmt->execute([$data['id_kelas'], $data['tanggal']]);

            $insertStmt = $pdo->prepare(
                "INSERT INTO absensi_siswa_piket (id_siswa, id_kelas, id_ta, tanggal, status, keterangan, id_guru_piket) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            foreach ($data['absensi'] as $id_siswa => $absensi_data) {
                // [FIX UTAMA]: Logic IF ($status != 'Hadir') DIHAPUS. 
                // SEMUA status (Hadir, Sakit, Izin, Alpa) akan di-INSERT.
                $status = $absensi_data['status'] ?? 'Hadir';
                $keterangan = $absensi_data['keterangan'] ?? '';
                
                $insertStmt->execute([
                    $id_siswa, $data['id_kelas'], $data['id_ta'], $data['tanggal'], 
                    $status, $keterangan, $data['id_guru_piket']
                ]);
            }
            
            $pdo->commit();
            return true; // Sukses

        } catch (Exception $e) {
            $pdo->rollBack();
            // Catat error ke log
            error_log("Absensi Piket Save Error: " . $e->getMessage());
            return false; // Gagal
        }
    }
}
?>