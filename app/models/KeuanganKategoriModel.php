<?php
/**
 * KeuanganKategoriModel
 * Model untuk mengelola data Kategori (Pos) Keuangan
 */

class KeuanganKategoriModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all categories
     */
    public function getAll($tipe = null) {
        $sql = "SELECT k.*, k.tipe 
                FROM keuangan_kategori k";
        
        if ($tipe) {
            $sql .= " WHERE k.tipe = ?";
            $sql .= " ORDER BY k.kode_akun";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$tipe]);
        } else {
            $sql .= " ORDER BY k.tipe, k.kode_akun";
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get category by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM keuangan_kategori WHERE id_kategori = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new category
     */
    public function create($data) {
        $sql = "INSERT INTO keuangan_kategori (tipe, kode_kategori, kode_akun, nama_kategori, keterangan, id_group) 
                VALUES (?, ?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tipe'],
            $data['kode_kategori'],
            $data['kode_akun'],
            $data['nama_kategori'],
            $data['keterangan'] ?? null
        ]);
    }
    
    /**
     * Update category
     */
    public function update($id, $data) {
        $sql = "UPDATE keuangan_kategori 
                SET tipe = ?, nama_kategori = ?, kode_akun = ?, keterangan = ?
                WHERE id_kategori = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tipe'],
            $data['nama_kategori'],
            $data['kode_akun'],
            $data['keterangan'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete category
     */
    public function delete($id) {
        $sql = "DELETE FROM keuangan_kategori WHERE id_kategori = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Get categories with jenis count
     */
    public function getAllWithJenisCount() {
        $sql = "SELECT k.*, 
                       COUNT(j.id_jenis) as jumlah_jenis
                FROM keuangan_kategori k
                LEFT JOIN keuangan_jenis j ON k.id_kategori = j.id_kategori
                GROUP BY k.id_kategori
                ORDER BY k.tipe, k.kode_akun";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get All Hierarchy (Category + Sub-Accounts)
     */
    public function getAllRecursive() {
        // Get Categories
        $sql = "SELECT * FROM keuangan_kategori ORDER BY tipe, kode_akun";
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get All Items
        $sqlItems = "SELECT * FROM keuangan_jenis ORDER BY kode_akun";
        $stmtItems = $this->db->query($sqlItems);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Group Items by Category
        $groupedItems = [];
        foreach($items as $item) {
            $groupedItems[$item['id_kategori']][] = $item;
        }

        // Merge
        foreach($categories as &$cat) {
            $cat['items'] = $groupedItems[$cat['id_kategori']] ?? [];
        }

        return $categories;
    }
}
