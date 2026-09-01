<?php
class MasterJamModel {

    public static function getAll($pdo) {
        $sql = "SELECT jp.*, mk.nama_kegiatan 
                FROM jam_pelajaran jp
                LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan
                ORDER BY jp.urutan ASC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllKegiatan($pdo) {
        // REVISI: Ambil juga durasi dan hari_pelaksanaan untuk JS
        return $pdo->query("SELECT id_kegiatan, nama_kegiatan, jenis_kegiatan, durasi_menit, hari_pelaksanaan 
                            FROM master_kegiatan 
                            WHERE jenis_kegiatan IN ('KBM', 'Istirahat', 'Pembiasaan', 'Lainnya')
                            ORDER BY nama_kegiatan ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function save($pdo, $data) {
        $id_kegiatan = $data['id_kegiatan'] ?? null;
        $nama_kegiatan_custom = $data['nama_kegiatan_custom'] ?? null;

        // Jika jenis_kegiatan adalah KBM, paksa id_kegiatan
        if ($data['jenis_kegiatan'] == 'KBM') {
            // Cari ID KBM (Asumsi dari SS 87, ID 11 adalah 'Kegiatan Belajar Mengajar')
            $stmt_kbm = $pdo->query("SELECT id_kegiatan FROM master_kegiatan WHERE jenis_kegiatan = 'KBM' LIMIT 1");
            $id_kegiatan_kbm = $stmt_kbm->fetchColumn();
            
            $id_kegiatan = $id_kegiatan_kbm ?: null; // Gunakan 11 (atau null jika tidak ada)
            $nama_kegiatan_custom = null; // KBM tidak punya nama kustom
        } elseif (empty($id_kegiatan)) {
             $id_kegiatan = null; // Jika dropdown tidak dipilih
        }

        if (empty($data['id_jam'])) {
            // INSERT
            $stmt_max = $pdo->query("SELECT MAX(urutan) FROM jam_pelajaran");
            $max_urut = $stmt_max->fetchColumn() ?? 0;
            $next_urut = $max_urut + 1;

            $sql = "INSERT INTO jam_pelajaran (urutan, label_jam_ke, jam_mulai, jam_selesai, jenis_kegiatan, id_kegiatan, nama_kegiatan_custom, durasi_menit, hari_pelaksanaan) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            // Hitung durasi
            $durasi = (strtotime($data['jam_selesai']) - strtotime($data['jam_mulai'])) / 60;
            
            // Handle hari_pelaksanaan array to comma-separated string
            $hari_pelaksanaan_str = is_array($data['hari_pelaksanaan'] ?? null) ? implode(',', $data['hari_pelaksanaan']) : ($data['hari_pelaksanaan'] ?? '');

            $params = [
                $next_urut,
                $data['label_jam_ke'],
                $data['jam_mulai'],
                $data['jam_selesai'],
                $data['jenis_kegiatan'],
                $id_kegiatan,
                $nama_kegiatan_custom,
                $durasi,
                $hari_pelaksanaan_str
            ];
        } else {
            // UPDATE
            $sql = "UPDATE jam_pelajaran SET 
                        label_jam_ke = ?, 
                        jam_mulai = ?, 
                        jam_selesai = ?, 
                        jenis_kegiatan = ?, 
                        id_kegiatan = ?, 
                        nama_kegiatan_custom = ?,
                        durasi_menit = ?,
                        hari_pelaksanaan = ?
                    WHERE id_jam = ?";
            
            // Hitung durasi
            $durasi = (strtotime($data['jam_selesai']) - strtotime($data['jam_mulai'])) / 60;

            // Handle hari_pelaksanaan array to comma-separated string
            $hari_pelaksanaan_str = is_array($data['hari_pelaksanaan'] ?? null) ? implode(',', $data['hari_pelaksanaan']) : ($data['hari_pelaksanaan'] ?? '');

            $params = [
                $data['label_jam_ke'],
                $data['jam_mulai'],
                $data['jam_selesai'],
                $data['jenis_kegiatan'],
                $id_kegiatan,
                $nama_kegiatan_custom,
                $durasi,
                $hari_pelaksanaan_str,
                $data['id_jam']
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($pdo, $id, $day = null) {
        if ($day) {
            // 1. Ambil data hari saat ini
            $stmt = $pdo->prepare("SELECT hari_pelaksanaan FROM jam_pelajaran WHERE id_jam = ?");
            $stmt->execute([$id]);
            $hari_str = $stmt->fetchColumn();
            
            if ($hari_str) {
                $hari_arr = array_filter(explode(',', $hari_str));
                
                // 2. Hapus hari tujuan dari array
                $new_hari_arr = array_diff($hari_arr, [$day]);
                
                if (empty($new_hari_arr)) {
                    // Jika tdk ada hari tersisa, hapus permanen
                    $stmt = $pdo->prepare("DELETE FROM jam_pelajaran WHERE id_jam = ?");
                    return $stmt->execute([$id]);
                } else {
                    // Jika masih ada hari lain, update saja
                    $new_hari_str = implode(',', $new_hari_arr);
                    $stmt = $pdo->prepare("UPDATE jam_pelajaran SET hari_pelaksanaan = ? WHERE id_jam = ?");
                    return $stmt->execute([$new_hari_str, $id]);
                }
            }
        }
        
        // Default: Hapus permanen jika tidak ada konteks hari
        $stmt = $pdo->prepare("DELETE FROM jam_pelajaran WHERE id_jam = ?");
        return $stmt->execute([$id]);
    }

    public static function updateUrutan($pdo, $urutan_ids) {
        $pdo->beginTransaction();
        try {
            $sql = "UPDATE jam_pelajaran SET urutan = ? WHERE id_jam = ?";
            $stmt = $pdo->prepare($sql);
            
            foreach ($urutan_ids as $index => $id_jam) {
                $stmt->execute([$index + 1, $id_jam]);
            }
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function copyDay($pdo, $fromDay, $toDay) {
        $pdo->beginTransaction();
        try {
            // 1. Bersihkan hari tujuan (Overwrite)
            // Jika ada record yang mengandung toDay, hilangkan toDay dari hari_pelaksanaan record tersebut
            $stmt_to = $pdo->prepare("SELECT id_jam, hari_pelaksanaan FROM jam_pelajaran WHERE hari_pelaksanaan LIKE ?");
            $stmt_to->execute(['%' . $toDay . '%']);
            $toDayRecords = $stmt_to->fetchAll(PDO::FETCH_ASSOC);

            foreach ($toDayRecords as $r) {
                $days = array_filter(explode(',', $r['hari_pelaksanaan']));
                $new_days = array_values(array_diff($days, [$toDay]));

                if (empty($new_days)) {
                    // Jika hanya ada hari tersebut, hapus recordnya
                    $pdo->prepare("DELETE FROM jam_pelajaran WHERE id_jam = ?")->execute([$r['id_jam']]);
                } else {
                    // Jika ada hari lain, update recordnya agar tidak lagi mencakup toDay
                    $pdo->prepare("UPDATE jam_pelajaran SET hari_pelaksanaan = ? WHERE id_jam = ?")
                        ->execute([implode(',', $new_days), $r['id_jam']]);
                }
            }

            // 2. Ambil record dari hari asal (fromDay)
            $stmt_from = $pdo->prepare("SELECT * FROM jam_pelajaran WHERE hari_pelaksanaan LIKE ? ORDER BY urutan ASC");
            $stmt_from->execute(['%' . $fromDay . '%']);
            $sourceRecords = $stmt_from->fetchAll(PDO::FETCH_ASSOC);

            // 3. Masukkan sebagai record BARU (Kloning) untuk hari tujuan
            $sql_ins = "INSERT INTO jam_pelajaran (urutan, label_jam_ke, jam_mulai, jam_selesai, jenis_kegiatan, id_kegiatan, nama_kegiatan_custom, durasi_menit, hari_pelaksanaan) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_ins = $pdo->prepare($sql_ins);

            foreach ($sourceRecords as $s) {
                $stmt_ins->execute([
                    $s['urutan'],
                    $s['label_jam_ke'],
                    $s['jam_mulai'],
                    $s['jam_selesai'],
                    $s['jenis_kegiatan'],
                    $s['id_kegiatan'],
                    $s['nama_kegiatan_custom'],
                    $s['durasi_menit'],
                    $toDay // Satu hari saja (Mandiri)
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
?>