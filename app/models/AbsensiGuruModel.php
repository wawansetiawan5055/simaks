<?php
class AbsensiGuruModel {

    /**
     * FUNGSI YANG DIPERBARUI:
     * Mengambil HANYA guru yang memiliki jadwal pada hari yang dipilih.
     */
    public static function getGuruWithScheduleOnDate($pdo, $tanggal, $id_ta) {
        // 1. Tentukan nama hari (Senin, Selasa, dst.) dari tanggal
        $dayOfWeek = date('w', strtotime($tanggal));
        $hari_map = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari_kbm = $hari_map[$dayOfWeek];

        // 2. Query utama: Menggunakan INNER JOIN ke detail_mengajar
        // Ini secara otomatis HANYA akan memilih guru yang punya jadwal di hari_kbm tersebut
        $sql = "
            SELECT 
                g.id_guru, 
                g.nama,
                GROUP_CONCAT(
                    DISTINCT CONCAT(TIME_FORMAT(jp.jam_mulai, '%H:%i'), '-', TIME_FORMAT(jp.jam_selesai, '%H:%i'), ' (', k.nama_kelas, ')') 
                    ORDER BY jp.jam_mulai
                    SEPARATOR ' \n '
                ) AS jadwal_hari_ini
            FROM guru g
            JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = :id_ta
            JOIN jadwal_mengajar dm ON gm.id_guru_mapel = dm.id_guru_mapel AND dm.hari_kbm = :hari_kbm
            JOIN jam_pelajaran jp ON dm.id_jam = jp.id_jam
            JOIN kelas k ON dm.id_kelas = k.id_kelas
            WHERE g.status = 'Aktif'
            GROUP BY g.id_guru
            ORDER BY g.nama ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_ta' => $id_ta, ':hari_kbm' => $hari_kbm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil data absensi guru yang sudah ada pada tanggal tertentu.
     * (Fungsi ini tidak berubah)
     */
    public static function getAbsensiByTanggal($pdo, $tanggal, $id_ta) {
        $sql = "SELECT * FROM absensi_guru WHERE tanggal = ? AND id_ta = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal, $id_ta]);
        
        $absensi_data = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $absensi_data[$row['id_guru']] = $row;
        }
        return $absensi_data;
    }

    /**
     * Menyimpan data absensi guru.
     * Logika disempurnakan: Hapus-lalu-Sisipkan HANYA untuk guru yang ada di form.
     */
    public static function save($pdo, $data) {
        $pdo->beginTransaction();
        try {
            // Siapkan statement di luar loop
            $deleteStmt = $pdo->prepare("DELETE FROM absensi_guru WHERE tanggal = ? AND id_ta = ? AND id_guru = ?");
            $insertStmt = $pdo->prepare(
                "INSERT INTO absensi_guru (id_guru, id_ta, tanggal, status, keterangan, tugas, id_guru_piket) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            // Loop dan masukkan data baru HANYA untuk guru yang ada di form
            foreach ($data['absensi'] as $id_guru => $absensi_data) {
                // 1. Hapus data lama untuk guru ini di hari ini
                $deleteStmt->execute([$data['tanggal'], $data['id_ta'], $id_guru]);

                // 2. Masukkan data baru
                $status = $absensi_data['status'] ?? 'Hadir';
                $keterangan = $absensi_data['keterangan'] ?? '';
                $tugas = $absensi_data['tugas'] ?? '';

                $insertStmt->execute([
                    $id_guru, $data['id_ta'], $data['tanggal'], 
                    $status, $keterangan, $tugas, $data['id_guru_piket']
                ]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Gagal menyimpan absensi guru: " . $e->getMessage());
        }
    }
}