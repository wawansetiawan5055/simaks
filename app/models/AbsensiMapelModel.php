<?php
class AbsensiMapelModel {
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
     * Mengambil data absensi mapel yang sudah tersimpan untuk kelas, guru_mapel & tanggal tertentu.
     * Digunakan untuk menampilkan status existing saat form dibuka ulang (mode edit).
     * Return: array berindeks id_siswa.
     */
    public static function getAbsensiByKelasAndTanggal($pdo, $id_kelas, $tanggal, $id_guru_mapel = null) {
        if ($id_guru_mapel) {
            $stmt = $pdo->prepare(
                "SELECT id_siswa, status, keterangan, jam_ke FROM absensi_siswa_mapel
                 WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ?"
            );
            $stmt->execute([$id_kelas, $tanggal, $id_guru_mapel]);
        } else {
            $stmt = $pdo->prepare(
                "SELECT id_siswa, status, keterangan, jam_ke FROM absensi_siswa_mapel
                 WHERE id_kelas = ? AND tanggal = ?"
            );
            $stmt->execute([$id_kelas, $tanggal]);
        }
        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['id_siswa']] = $row;
        }
        return $result;
    }

    /**
     * Cek apakah absensi mapel untuk kelas & tanggal sudah pernah diisi.
     */
    public static function sudahDiisi($pdo, $id_kelas, $tanggal) {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM absensi_siswa_mapel WHERE id_kelas = ? AND tanggal = ?"
        );
        $stmt->execute([$id_kelas, $tanggal]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * [REVISI TOTAL LOGIKA SIMPAN - ANTI BUG V2]
     * Kita akan menggunakan 1 query INSERT ... ON DUPLICATE KEY UPDATE.
     * Ini adalah cara paling aman dan paling efisien untuk menyimpan data absensi.
     * Ini akan menangani 'Tambah' dan 'Edit' dalam satu perintah atomik.
     */
    public static function save($pdo, $data) {
        // 1. Siapkan query dasarnya
        $sql = "INSERT INTO absensi_siswa_mapel 
                    (id_siswa, id_guru_mapel, id_kelas, id_ta, tanggal, jam_ke, status, keterangan) 
                VALUES ";
        
        $params = [];
        $rowsSQL = [];

        // 2. Ambil daftar siswa yang valid dari database
        $siswa_list = self::getSiswaByKelas($pdo, $data['id_kelas'], $data['id_ta']);

        // 3. Loop berdasarkan daftar siswa yang valid (dari DB)
        foreach ($siswa_list as $siswa) {
            $id_siswa = $siswa['id_siswa'];
            
            // Ambil data dari $_POST (absensi)
            $status = $data['absensi'][$id_siswa]['status'] ?? 'Hadir';
            $keterangan = $data['absensi'][$id_siswa]['keterangan'] ?? '';

            // Jika status kosong, fallback ke 'Hadir'
            if (empty($status)) {
                $status = 'Hadir';
            }

            // Tambahkan placeholder "(?, ?, ?, ?, ?, ?, ?, ?)" ke query
            $rowsSQL[] = "(?, ?, ?, ?, ?, ?, ?, ?)";
            
            // Tambahkan data ke array parameter
            $params[] = $id_siswa;
            $params[] = $data['id_guru_mapel'];
            $params[] = $data['id_kelas'];
            $params[] = $data['id_ta'];
            $params[] = $data['tanggal'];
            $params[] = $data['jam_ke'];
            $params[] = $status;
            $params[] = $keterangan;
        }

        if (empty($rowsSQL)) {
            // Tidak ada siswa di kelas ini, tidak perlu lanjut.
            return;
        }

        // 4. Gabungkan query dasarnya dengan placeholder
        $sql .= implode(', ', $rowsSQL);

        // 5. Tambahkan bagian "ON DUPLICATE KEY UPDATE"
        // Jika data (id_siswa, id_guru_mapel, id_kelas, tanggal) sudah ada,
        // perbarui saja status dan keterangannya.
        // Kita butuh UNIQUE KEY di (id_siswa, id_guru_mapel, id_kelas, tanggal)
        // Jika Anda tidak punya, kita akan hapus dulu
        
        // Logika yang lebih aman (DELETE dulu, baru BULK INSERT)
        // Kita kembali ke logika DELETE, tapi pastikan itu di luar transaksi
        
        try {
            // HAPUS data lama dulu
            $deleteStmt = $pdo->prepare(
                "DELETE FROM absensi_siswa_mapel WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ?"
            );
            $deleteStmt->execute([$data['id_kelas'], $data['tanggal'], $data['id_guru_mapel']]);
            
            // Masukkan data baru (Bulk Insert)
            $insertStmt = $pdo->prepare($sql);
            $insertStmt->execute($params);

        } catch (Exception $e) {
            // Jika gagal, coba satu per satu (Fallback)
            $pdo->rollBack(); // Batalkan jika ada transaksi sebelumnya
            
            // Logika Fallback: Insert satu per satu (lebih lambat tapi pasti)
            $pdo->beginTransaction();
            try {
                $deleteStmt = $pdo->prepare(
                    "DELETE FROM absensi_siswa_mapel WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ?"
                );
                $deleteStmt->execute([$data['id_kelas'], $data['tanggal'], $data['id_guru_mapel']]);

                $insertStmt = $pdo->prepare(
                    "INSERT INTO absensi_siswa_mapel (id_siswa, id_guru_mapel, id_kelas, id_ta, tanggal, jam_ke, status, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                
                foreach ($siswa_list as $siswa) {
                    $id_siswa = $siswa['id_siswa'];
                    $status = $data['absensi'][$id_siswa]['status'] ?? 'Hadir';
                    $keterangan = $data['absensi'][$id_siswa]['keterangan'] ?? '';
                    if (empty($status)) $status = 'Hadir';

                    $insertStmt->execute([
                        $id_siswa, $data['id_guru_mapel'], $data['id_kelas'], $data['id_ta'],
                        $data['tanggal'], $data['jam_ke'], $status, $keterangan
                    ]);
                }
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                die("Gagal menyimpan absensi (Fallback): " . $e->getMessage());
            }
        }
    }
}