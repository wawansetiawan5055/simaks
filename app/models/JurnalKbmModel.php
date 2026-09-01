<?php
class JurnalKbmModel {
    public static function getKelasDiajar($pdo, $id_guru, $id_ta_aktif) {
        $sql = "SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat
                FROM jadwal_mengajar dm
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                WHERE gm.id_guru = ? AND gm.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_ta_aktif]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Simpan/Update Jurnal KBM.
     * Jika jurnal untuk (id_guru, id_kelas, id_ta, tanggal, jam_ke) sudah ada ? UPDATE.
     * Jika belum ada ? INSERT baru.
     */
    public static function save($pdo, $data) {
        // Cek apakah jurnal untuk kombinasi ini sudah ada
        $stmt_check = $pdo->prepare("
            SELECT id_jurnal FROM jurnal_kbm 
            WHERE id_guru = ? AND id_kelas = ? AND id_ta = ? AND tanggal = ? AND jam_ke = ?
            LIMIT 1
        ");
        $stmt_check->execute([
            $data['id_guru'],
            $data['id_kelas'],
            $data['id_ta'],
            $data['tanggal'],
            $data['jam_ke']
        ]);
        $existing_id = $stmt_check->fetchColumn();

        if ($existing_id) {
            // UPDATE: update data yang sudah ada
            $sql = "UPDATE jurnal_kbm SET
                        tujuan_pembelajaran = ?,
                        tagihan = ?,
                        catatan_absensi = ?,
                        keterangan = ?" .
                   (!empty($data['foto_kegiatan']) ? ", foto_kegiatan = ?" : "") .
                   " WHERE id_jurnal = ?";

            $params = [
                $data['tujuan_pembelajaran'],
                $data['tagihan'],
                $data['catatan_absensi'],
                $data['keterangan'] ?? ''
            ];
            if (!empty($data['foto_kegiatan'])) {
                $params[] = $data['foto_kegiatan'];
            }
            $params[] = $existing_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            // INSERT: buat jurnal baru
            $sql = "INSERT INTO jurnal_kbm 
                        (id_guru, id_kelas, id_ta, tanggal, jam_ke, tujuan_pembelajaran, tagihan, catatan_absensi, keterangan, foto_kegiatan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['id_guru'],
                $data['id_kelas'],
                $data['id_ta'],
                $data['tanggal'],
                $data['jam_ke'],
                $data['tujuan_pembelajaran'],
                $data['tagihan'],
                $data['catatan_absensi'],
                $data['keterangan'] ?? '',
                $data['foto_kegiatan'] ?? null
            ]);
        }
    }

}
