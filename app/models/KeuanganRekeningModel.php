<?php
/**
 * KeuanganRekeningModel
 * Model untuk mengelola data Rekening Bank/Kas
 */

class KeuanganRekeningModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get all rekenings
     */
    public function getAll() {
        $sql = "SELECT * FROM keuangan_rekening ORDER BY tipe, nama_rekening";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get active rekenings (saldo > 0 or just check active status if added later)
     * For now returning all, or filtered by logic
     */
    public function getActive() {
        return $this->getAll();
    }

    /**
     * Get Total Saldo
     */
    public function getTotalSaldo() {
        $sql = "SELECT SUM(saldo_akhir) as total FROM keuangan_rekening";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Create new rekening
     */
    public function create($data) {
        $sql = "INSERT INTO keuangan_rekening (kode_rekening, nama_rekening, tipe, nama_bank, nomor_rekening, atas_nama, saldo_awal, saldo_akhir, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Saldo akhir starts same as saldo awal
        $saldo_akhir = $data['saldo_awal'];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['kode_rekening'],
            $data['nama_rekening'],
            $data['tipe'],
            $data['nama_bank'] ?? null,
            $data['nomor_rekening'] ?? null,
            $data['atas_nama'] ?? null,
            $data['saldo_awal'],
            $saldo_akhir,
            $data['keterangan'] ?? null
        ]);
    }

    /**
     * Recalculate all balances from transactions
     */
    public function recalculateBalances() {
        // 1. Get all rekenings
        $rekenings = $this->getAll();
        
        foreach ($rekenings as $rek) {
            $id = $rek['id_rekening'];
            $awal = (float)$rek['saldo_awal'];
            
            // 2. Sum MASUK
            $stmt = $this->db->prepare("SELECT SUM(jumlah) FROM keuangan_transaksi WHERE id_rekening = ? AND tipe = 'MASUK'");
            $stmt->execute([$id]);
            $masuk = (float)$stmt->fetchColumn() ?: 0;
            
            // 3. Sum KELUAR
            $stmt = $this->db->prepare("SELECT SUM(jumlah) FROM keuangan_transaksi WHERE id_rekening = ? AND tipe = 'KELUAR'");
            $stmt->execute([$id]);
            $keluar = (float)$stmt->fetchColumn() ?: 0;
            
            // 4. Update Saldo Akhir
            $akhir = $awal + $masuk - $keluar;
            $stmt = $this->db->prepare("UPDATE keuangan_rekening SET saldo_akhir = ? WHERE id_rekening = ?");
            $stmt->execute([$akhir, $id]);
        }
        return true;
    }
}
