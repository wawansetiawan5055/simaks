<?php
class CatatanKelasModel {
    /**
     * Menyimpan catatan kejadian kelas.
     * Akan menyimpan satu catatan untuk setiap jam pelajaran yang dipilih.
     */
    public static function save($pdo, $data) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO catatan_kelas (id_jadwal_mengajar, id_ta, tanggal, catatan_kejadian) 
                 VALUES (?, ?, ?, ?)"
            );
            
            $id_ta = $data['id_ta'];
            $tanggal = $data['tanggal'];
            $catatan = $data['catatan_kejadian'];

            // Loop untuk setiap jam pelajaran (id_jadwal_mengajar) yang dipilih
            foreach ($data['jam_mengajar'] as $id_jadwal_mengajar) {
                // Hapus catatan lama jika ada, agar tidak duplikat
                $deleteStmt = $pdo->prepare("DELETE FROM catatan_kelas WHERE id_jadwal_mengajar = ? AND tanggal = ?");
                $deleteStmt->execute([$id_jadwal_mengajar, $tanggal]);

                // Masukkan catatan baru
                $stmt->execute([$id_jadwal_mengajar, $id_ta, $tanggal, $catatan]);
            }
            
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Gagal menyimpan catatan kelas: " . $e->getMessage());
        }
    }
public static function getAllByTA($pdo, $id_ta) {
        $sql = "SELECT ck.tanggal, ck.catatan_kejadian, 
                   k.nama_kelas, m.nama_mapel, g.nama AS nama_guru
            FROM catatan_kelas ck
            JOIN jadwal_mengajar dm ON ck.id_jadwal_mengajar = dm.id_jadwal_mengajar
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE ck.id_ta = ?
                ORDER BY ck.tanggal DESC, k.nama_kelas";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
?>