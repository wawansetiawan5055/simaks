<?php
/**
 * KeuanganJenisModel
 * Model untuk mengelola data Jenis Pembayaran
 */

class KeuanganJenisModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all jenis pembayaran
     */
    public function getAll() {
        $sql = "SELECT j.*, k.nama_kategori, k.tipe
                FROM keuangan_jenis j
                JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                ORDER BY k.tipe, k.kode_akun, j.kode_akun";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get by Tipe (MASUK/KELUAR)
     */
    public function getByTipe($tipe) {
        $sql = "SELECT j.*, k.nama_kategori
                FROM keuangan_jenis j
                JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                WHERE k.tipe = ?
                ORDER BY k.kode_akun, j.kode_akun";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new jenis
     */
    public function create($data) {
        $sql = "INSERT INTO keuangan_jenis (id_kategori, kode_jenis, kode_akun, nama_jenis, harga_default, is_recurring, recurring_period, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_kategori'],
            $data['kode_jenis'],
            $data['kode_akun'],
            $data['nama_jenis'],
            $data['harga_default'],
            $data['is_recurring'] ?? 0,
            $data['recurring_period'] ?? null,
            $data['keterangan'] ?? null
        ]);
    }
    /**
     * Update existing jenis
     */
    public function update($id, $data) {
        $sql = "UPDATE keuangan_jenis SET 
                id_kategori = ?, kode_jenis = ?, kode_akun = ?, nama_jenis = ?, harga_default = ?, 
                is_recurring = ?, recurring_period = ?, is_active = ?, keterangan = ?
                WHERE id_jenis = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_kategori'],
            $data['kode_jenis'],
            $data['kode_akun'],
            $data['nama_jenis'],
            $data['harga_default'],
            $data['is_recurring'] ?? 0,
            $data['recurring_period'] ?? null,
            $data['is_active'] ?? 1,
            $data['keterangan'] ?? null,
            $id
        ]);
    }

    /**
     * Get only jenis that have entries in the Matrix (keuangan_tarif)
     */
    public function getJenisFromMatrix() {
        $sql = "SELECT DISTINCT j.*, k.nama_kategori
                FROM keuangan_jenis j
                JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                JOIN keuangan_tarif t ON j.id_jenis = t.id_jenis
                WHERE k.tipe = 'MASUK'
                ORDER BY j.nama_jenis ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get jenis that are activated in the matrix FOR A SPECIFIC CLASS
     */
    public function getActiveKindsByClass($id_kelas) {
        $sql = "SELECT DISTINCT j.*, k.nama_kategori
                FROM keuangan_jenis j
                JOIN keuangan_kategori k ON j.id_kategori = k.id_kategori
                JOIN keuangan_tarif t ON j.id_jenis = t.id_jenis
                JOIN penempatan_siswa ps ON t.id_siswa = ps.id_siswa
                WHERE k.tipe = 'MASUK' 
                AND ps.id_kelas = ?
                ORDER BY j.nama_jenis ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_kelas]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
