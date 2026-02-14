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

    public static function save($pdo, $data) {
        // PERBAIKAN: Pastikan kolom 'jam_ke' sesuai dengan database
        $sql = "INSERT INTO jurnal_kbm 
                    (id_guru, id_kelas, id_ta, tanggal, jam_ke, tujuan_pembelajaran, tagihan, catatan_absensi, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            $data['id_guru'],
            $data['id_kelas'],
            $data['id_ta'],
            $data['tanggal'],
            $data['jam_ke'], // Menerima string jam yang sudah diformat
            $data['tujuan_pembelajaran'],
            $data['tagihan'],
            $data['catatan_absensi'],
            $data['keterangan']
        ]);
    }

}