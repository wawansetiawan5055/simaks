<?php
class NilaiModel {

    // Fungsi ini HARUS ADA
    public static function getSiswaWithNilai($pdo, $id_kelas, $id_guru_mapel, $id_ta, $id_tp) {
        $sql = "SELECT 
                    s.id_siswa, 
                    s.nama, 
                    s.nisn,
                    ps.id_penempatan,
                    n.nilai,
                    n.keterangan,
                    n.deskripsi
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                LEFT JOIN nilai n ON ps.id_penempatan = n.id_penempatan AND n.id_guru_mapel = ? AND n.id_tp = ?
                WHERE ps.id_kelas = ? AND ps.id_ta = ? AND s.status_aktif = 'Aktif'
                ORDER BY s.nama ASC";

        $stmt = $pdo->prepare($sql);
        // Pastikan urutan parameter benar: id_guru_mapel, id_tp, id_kelas, id_ta
        $stmt->execute([$id_guru_mapel, $id_tp, $id_kelas, $id_ta]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMapelDiajarByKelas($pdo, $id_guru, $id_kelas, $id_ta) {
        $sql = "SELECT gm.id_guru_mapel, m.nama_mapel
            FROM jadwal_mengajar dm
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE gm.id_guru = ? AND dm.id_kelas = ? AND gm.id_ta = ?
                GROUP BY gm.id_guru_mapel";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fungsi ini mungkin belum ada di file Anda sebelumnya, pastikan ditambahkan
    public static function getTpByMapel($pdo, $id_mapel) {
         $stmt = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_mapel = ? ORDER BY kode_tp");
         $stmt->execute([$id_mapel]);
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function save($pdo, $data) {
        $kktpStmt = $pdo->prepare("SELECT m.kktp FROM mapel m JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel WHERE gm.id_guru_mapel = ?");
        $kktpStmt->execute([$data['id_guru_mapel']]);
        $kktp = (int) $kktpStmt->fetchColumn();

        $pdo->beginTransaction();
        try {
            // Hapus nilai lama agar tidak duplikat jika diedit
            $deleteStmt = $pdo->prepare("DELETE FROM nilai WHERE id_guru_mapel = ? AND id_tp = ? AND id_penempatan IN (SELECT id_penempatan FROM penempatan_siswa WHERE id_kelas = ?)");
            $deleteStmt->execute([$data['id_guru_mapel'], $data['id_tp'], $data['id_kelas']]);

            $insertStmt = $pdo->prepare("INSERT INTO nilai (id_penempatan, id_guru_mapel, id_tp, nilai, deskripsi) VALUES (?, ?, ?, ?, ?)");

            foreach ($data['nilai'] as $id_penempatan => $nilai_data) {
                if (isset($nilai_data['nilai']) && $nilai_data['nilai'] !== '') {
                    $nilai_angka = (float)$nilai_data['nilai'];
                    $deskripsi = self::generateDeskripsiOtomatis($nilai_angka, $kktp);

                    $insertStmt->execute([$id_penempatan, $data['id_guru_mapel'], $data['id_tp'], $nilai_angka, $deskripsi]);
                }
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Gagal menyimpan nilai: " . $e->getMessage());
        }
    }

    private static function generateDeskripsiOtomatis($nilai, $kktp) {
        if ($nilai >= 90) return "Sangat Baik, menunjukkan penguasaan materi yang melampaui ekspektasi.";
        elseif ($nilai >= $kktp) return "Baik, telah mencapai kriteria ketercapaian dengan memuaskan.";
        elseif ($nilai >= $kktp - 10) return "Cukup, mendekati kriteria ketercapaian dan perlu sedikit penguatan.";
        else return "Perlu Bimbingan, belum mencapai kriteria ketercapaian.";
    }
}