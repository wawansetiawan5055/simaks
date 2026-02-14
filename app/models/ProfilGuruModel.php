<?php
class ProfilGuruModel {
    
    // Ambil profil berdasarkan id_guru
    public static function getByGuruId($pdo, $id_guru) {
        $stmt = $pdo->prepare("SELECT * FROM profil_guru WHERE id_guru = ?");
        $stmt->execute([$id_guru]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Simpan atau Update data profil (text)
    public static function save($pdo, $data) {
        // Cek apakah sudah ada profil?
        $existing = self::getByGuruId($pdo, $data['id_guru']);

        if ($existing) {
            // Update
            $sql = "UPDATE profil_guru SET 
                    gelar_depan = ?, gelar_belakang = ?, alamat_lengkap = ?, 
                    no_hp = ?, email_pribadi = ?, nama_ibu_kandung = ?, 
                    pendidikan_terakhir = ? 
                    WHERE id_guru = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['gelar_depan'], $data['gelar_belakang'], $data['alamat_lengkap'],
                $data['no_hp'], $data['email_pribadi'], $data['nama_ibu_kandung'],
                $data['pendidikan_terakhir'], $data['id_guru']
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO profil_guru (
                    id_guru, gelar_depan, gelar_belakang, alamat_lengkap, 
                    no_hp, email_pribadi, nama_ibu_kandung, pendidikan_terakhir
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_guru'],
                $data['gelar_depan'], $data['gelar_belakang'], $data['alamat_lengkap'],
                $data['no_hp'], $data['email_pribadi'], $data['nama_ibu_kandung'],
                $data['pendidikan_terakhir']
            ]);
        }
    }

    // Update File Spesifik
    public static function updateFile($pdo, $id_guru, $column, $filename) {
        // Pastikan record ada dulu (kalau belum ada insert dummy/null)
        $existing = self::getByGuruId($pdo, $id_guru);
        if (!$existing) {
            $pdo->prepare("INSERT INTO profil_guru (id_guru) VALUES (?)")->execute([$id_guru]);
        }

        // Hapus file lama jika ada (optional, handled in controller usually for file deletion)
        
        $sql = "UPDATE profil_guru SET $column = ? WHERE id_guru = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$filename, $id_guru]);
    }
}
?>
