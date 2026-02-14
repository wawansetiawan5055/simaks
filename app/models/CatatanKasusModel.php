<?php
class CatatanKasusModel {

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
     * Mengambil semua catatan kasus yang ada, difilter berdasarkan tahun ajaran aktif.
     * Filter dilakukan melalui JOIN dengan penempatan_siswa.
     */
    public static function getAllKasus($pdo, $id_ta) {
        $sql = "SELECT c.*, s.nama as nama_siswa, k.nama_kelas, g.nama as nama_pelapor
                FROM catatan_kasus c
                JOIN siswa s ON c.id_siswa = s.id_siswa
                JOIN kelas k ON c.id_kelas = k.id_kelas
                JOIN penempatan_siswa ps ON c.id_siswa = ps.id_siswa AND c.id_kelas = ps.id_kelas
                LEFT JOIN guru g ON c.id_guru_piket = g.id_guru
                WHERE ps.id_ta = ?
                ORDER BY c.tanggal DESC, c.waktu_input DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menyimpan catatan kasus baru.
     * PERBAIKAN: Menghapus 'id_ta' dari query INSERT.
     */
    public static function save($pdo, $data) {
        $sql = "INSERT INTO catatan_kasus 
                    (id_siswa, id_kelas, id_guru_piket, tanggal, catatan, tindak_lanjut, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_siswa'],
            $data['id_kelas'],
            $data['id_guru_piket'],
            $data['tanggal'],
            $data['catatan'],
            $data['tindak_lanjut'],
            $data['keterangan']
        ]);
    }

    /**
     * Menghapus catatan kasus.
     */
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM catatan_kasus WHERE id_catatan = ?");
        $stmt->execute([$id]);
    }
}