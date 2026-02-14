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
        // REVISI: Ambil juga durasi untuk JS
        // Filter agar Jenis Kegiatan Non-Akademik (Ekskul, dll) TIDAK muncul di sini
        return $pdo->query("SELECT id_kegiatan, nama_kegiatan, jenis_kegiatan, durasi_menit 
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

            $sql = "INSERT INTO jam_pelajaran (urutan, label_jam_ke, jam_mulai, jam_selesai, jenis_kegiatan, id_kegiatan, nama_kegiatan_custom, durasi_menit) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            // Hitung durasi
            $durasi = (strtotime($data['jam_selesai']) - strtotime($data['jam_mulai'])) / 60;
            
            $params = [
                $next_urut,
                $data['label_jam_ke'],
                $data['jam_mulai'],
                $data['jam_selesai'],
                $data['jenis_kegiatan'],
                $id_kegiatan,
                $nama_kegiatan_custom,
                $durasi
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
                        durasi_menit = ?
                    WHERE id_jam = ?";
            
            // Hitung durasi
            $durasi = (strtotime($data['jam_selesai']) - strtotime($data['jam_mulai'])) / 60;

            $params = [
                $data['label_jam_ke'],
                $data['jam_mulai'],
                $data['jam_selesai'],
                $data['jenis_kegiatan'],
                $id_kegiatan,
                $nama_kegiatan_custom,
                $durasi,
                $data['id_jam']
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($pdo, $id) {
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
}
?>