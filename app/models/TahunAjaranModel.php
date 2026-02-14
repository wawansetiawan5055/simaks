<?php
class TahunAjaranModel {
    public static function all($pdo) {
        return $pdo->query("SELECT * FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran WHERE id_ta = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // [REVISI BESAR]
    // Fungsi save() sekarang menerima $id dari Controller, bukan dari $data
    public static function save($pdo, $data, $id = null) {
        
        // Logika UPDATE jika ID ada dari URL
        if (!empty($id)) {
            $stmt = $pdo->prepare("UPDATE tahun_ajaran SET nama_ta=?, tanggal_mulai=?, tanggal_selesai=? WHERE id_ta=?");
            $stmt->execute([$data['nama_ta'], $data['tanggal_mulai'], $data['tanggal_selesai'], $id]);
        } 
        // Logika INSERT jika ID tidak ada (mode Tambah)
        else {
            $stmt = $pdo->prepare("INSERT INTO tahun_ajaran (nama_ta, tanggal_mulai, tanggal_selesai, status) VALUES (?,?,?,?)");
            $stmt->execute([$data['nama_ta'], $data['tanggal_mulai'], $data['tanggal_selesai'], 'Nonaktif']);
        }
    }
public static function existsByName($pdo, $nama_ta, $exclude_id = null) {
    if ($exclude_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tahun_ajaran WHERE nama_ta = ? AND id_ta != ?");
        $stmt->execute([$nama_ta, $exclude_id]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tahun_ajaran WHERE nama_ta = ?");
        $stmt->execute([$nama_ta]);
    }
    return $stmt->fetchColumn() > 0;
}

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM tahun_ajaran WHERE id_ta = ?");
        $stmt->execute([$id]);
    }
    
    public static function aktif($pdo) {
        return $pdo->query("SELECT * FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    }

    public static function set_aktif($pdo, $id_ta) {
        $pdo->beginTransaction();
        try {
            $pdo->exec("UPDATE tahun_ajaran SET status='Nonaktif'");
            $stmt = $pdo->prepare("UPDATE tahun_ajaran SET status='Aktif' WHERE id_ta=?");
            $stmt->execute([$id_ta]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}