<?php
/**
 * KeuanganGroupModel
 * Model untuk mengelola data Group Keuangan (Kelompok Besar)
 */

class KeuanganGroupModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all groups
     */
    public function getAll($tipe = null) {
        $sql = "SELECT * FROM keuangan_group";
        if ($tipe) {
            $sql .= " WHERE tipe = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$tipe]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get group by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM keuangan_group WHERE id_group = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get group by kode
     */
    public function getByKode($kode) {
        $sql = "SELECT * FROM keuangan_group WHERE kode_group = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$kode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create new group
     */
    public function create($data) {
        $sql = "INSERT INTO keuangan_group (kode_group, nama_group, tipe, keterangan) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['kode_group'],
            $data['nama_group'],
            $data['tipe'],
            $data['keterangan'] ?? null
        ]);
    }
    
    /**
     * Update group
     */
    public function update($id, $data) {
        $sql = "UPDATE keuangan_group 
                SET nama_group = ?, keterangan = ?
                WHERE id_group = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nama_group'],
            $data['keterangan'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete group (jika tidak ada data terkait)
     */
    public function delete($id) {
        $sql = "DELETE FROM keuangan_group WHERE id_group = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Get groups with category count
     */
    public function getAllWithCategoryCount() {
        $sql = "SELECT g.*, 
                       COUNT(k.id_kategori) as jumlah_kategori
                FROM keuangan_group g
                LEFT JOIN keuangan_kategori k ON g.id_group = k.id_group
                GROUP BY g.id_group
                ORDER BY g.tipe, g.kode_group";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
