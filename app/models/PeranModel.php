<?php
class PeranModel {
    
    // R (Read): Mengambil semua peran
    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM peran ORDER BY nama_peran ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // R (Read): Mengambil satu peran berdasarkan ID
    public static function findById($pdo, $id_peran) {
        $stmt = $pdo->prepare("SELECT * FROM peran WHERE id_peran = ?");
        $stmt->execute([$id_peran]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // C/U (Create/Update): Menyimpan atau Mengubah peran
    public static function save($pdo, $data) {
        if (!empty($data['id_peran'])) {
            // Update
            $sql = "UPDATE peran SET nama_peran = ? WHERE id_peran = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$data['nama_peran'], $data['id_peran']]);
        } else {
            // Create
            $sql = "INSERT INTO peran (nama_peran) VALUES (?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$data['nama_peran']]);
        }
    }

    // D (Delete): Menghapus peran
    public static function delete($pdo, $id_peran) {
        // PENTING: Hapus juga di tabel penghubung pengguna_peran
        $pdo->beginTransaction();
        try {
            $stmt1 = $pdo->prepare("DELETE FROM pengguna_peran WHERE id_peran = ?");
            $stmt1->execute([$id_peran]);

            $stmt2 = $pdo->prepare("DELETE FROM peran WHERE id_peran = ?");
            $stmt2->execute([$id_peran]);
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            // error_log("Gagal hapus peran: " . $e->getMessage());
            return false;
        }
    }
}