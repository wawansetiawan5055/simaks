<?php
class MasterKegiatanModel {
    public static function all($pdo) {
        return $pdo->query("SELECT * FROM master_kegiatan ORDER BY jenis_kegiatan, nama_kegiatan ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getAkademik($pdo) {
        return $pdo->query("SELECT * FROM master_kegiatan 
                            WHERE kategori = 'Akademik'
                            ORDER BY jenis_kegiatan, nama_kegiatan ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNonAkademik($pdo) {
        return $pdo->query("SELECT * FROM master_kegiatan 
                            WHERE kategori = 'Non-Akademik'
                            ORDER BY jenis_kegiatan, nama_kegiatan ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function save($pdo, $data) {
        if (isset($data['id_kegiatan']) && !empty($data['id_kegiatan'])) {
            // Update existing record
            $stmt = $pdo->prepare(
                "UPDATE master_kegiatan SET nama_kegiatan = ?, jenis_kegiatan = ?, durasi_menit = ?, hari_pelaksanaan = ?, kategori = ? WHERE id_kegiatan = ?"
            );
            $stmt->execute([
                $data['nama_kegiatan'], 
                $data['jenis_kegiatan'], 
                $data['durasi_menit'], 
                $data['hari_pelaksanaan'], 
                $data['kategori'] ?? 'Akademik', // Default
                $data['id_kegiatan']
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare(
                "INSERT INTO master_kegiatan (nama_kegiatan, jenis_kegiatan, durasi_menit, hari_pelaksanaan, kategori) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['nama_kegiatan'], 
                $data['jenis_kegiatan'], 
                $data['durasi_menit'], 
                $data['hari_pelaksanaan'],
                $data['kategori'] ?? 'Akademik'
            ]);
        }
    }
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM master_kegiatan WHERE id_kegiatan = ?");
        $stmt->execute([$id]);
    }
}