<?php
/**
 * KeuanganJurnalModel
 * Model aggregator untuk menyajikan data Jurnal Umum (Gabungan Kas & Memorial)
 */

class KeuanganJurnalModel {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get All Journal Entries (Kronologis)
     * Menggabungkan:
     * 1. Transaksi Masuk (Debit: Kas, Kredit: Akun Pendapatan)
     * 2. Transaksi Keluar (Debit: Akun Beban, Kredit: Kas)
     * 3. Jurnal Memorial (Sesuai inputan)
     */
    public function getAll($tanggal_dari, $tanggal_sampai) {
        // Query Union yang Kompleks
        // Struktur result: tanggal, no_bukti, kode_akun, nama_akun, keterangan, debit, kredit, tipe_sumber

        $sql = "
            /* 1A. TRANSAKSI MASUK - Sisi DEBIT (Kas Bertambah) */
            SELECT 
                t.tanggal, 
                t.no_bukti COLLATE utf8mb4_unicode_ci as no_bukti, 
                r.kode_rekening COLLATE utf8mb4_unicode_ci as kode_akun, 
                r.nama_rekening COLLATE utf8mb4_unicode_ci as nama_akun,
                CONCAT('Penerimaan: ', t.keterangan) COLLATE utf8mb4_unicode_ci as keterangan,
                t.jumlah as debit,
                0 as kredit,
                'KAS_MASUK' COLLATE utf8mb4_unicode_ci as source
            FROM keuangan_transaksi t
            JOIN keuangan_rekening r ON t.id_rekening = r.id_rekening
            WHERE t.tipe = 'MASUK' 
            AND t.tanggal BETWEEN ? AND ?

            UNION ALL

            /* 1B. TRANSAKSI MASUK - Sisi KREDIT (Pendapatan Bertambah) */
            SELECT 
                t.tanggal, 
                t.no_bukti, 
                j.kode_akun, 
                j.nama_jenis,
                CONCAT('Sumber: ', IFNULL(s.nama COLLATE utf8mb4_unicode_ci, IFNULL(t.referensi, '-'))) as keterangan,
                0 as debit,
                t.jumlah as kredit,
                'KAS_MASUK' as source
            FROM keuangan_transaksi t
            JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
            LEFT JOIN siswa s ON t.id_siswa = s.id_siswa
            WHERE t.tipe = 'MASUK'
            AND t.tanggal BETWEEN ? AND ?

            UNION ALL

            /* 2A. TRANSAKSI KELUAR - Sisi DEBIT (Beban Bertambah) */
            SELECT 
                t.tanggal, 
                t.no_bukti, 
                j.kode_akun, 
                j.nama_jenis,
                CONCAT('Biaya: ', t.keterangan) as keterangan,
                t.jumlah as debit,
                0 as kredit,
                'KAS_KELUAR' as source
            FROM keuangan_transaksi t
            JOIN keuangan_jenis j ON t.id_jenis = j.id_jenis
            WHERE t.tipe = 'KELUAR'
            AND t.tanggal BETWEEN ? AND ?

            UNION ALL

            /* 2B. TRANSAKSI KELUAR - Sisi KREDIT (Kas Berkurang) */
            SELECT 
                t.tanggal, 
                t.no_bukti, 
                r.kode_rekening, 
                r.nama_rekening,
                CONCAT('Dibayar ke: ', IFNULL(t.referensi, '-')) as keterangan,
                0 as debit,
                t.jumlah as kredit,
                'KAS_KELUAR' as source
            FROM keuangan_transaksi t
            JOIN keuangan_rekening r ON t.id_rekening = r.id_rekening
            WHERE t.tipe = 'KELUAR'
            AND t.tanggal BETWEEN ? AND ?

            UNION ALL

            /* 3. JURNAL MEMORIAL (Murni dari inputan manual) */
            SELECT 
                m.tanggal,
                m.no_bukti,
                d.kode_akun,
                d.nama_akun,
                m.keterangan,
                IF(d.tipe = 'DEBIT', d.jumlah, 0) as debit,
                IF(d.tipe = 'KREDIT', d.jumlah, 0) as kredit,
                'MEMORIAL' as source
            FROM keuangan_memorial m
            JOIN keuangan_memorial_detail d ON m.id_memorial = d.id_memorial
            WHERE m.tanggal BETWEEN ? AND ?

            ORDER BY tanggal ASC, no_bukti ASC, debit DESC
        ";

        // Parameter berulang karena UNION
        $params = [
            $tanggal_dari, $tanggal_sampai, // 1A
            $tanggal_dari, $tanggal_sampai, // 1B
            $tanggal_dari, $tanggal_sampai, // 2A
            $tanggal_dari, $tanggal_sampai, // 2B
            $tanggal_dari, $tanggal_sampai  // 3
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create Jurnal Memorial (Header + Detail)
     */
    public function createMemorial($data, $details) {
        try {
            $this->db->beginTransaction();

            // 1. Insert Header
            $sqlHeader = "INSERT INTO keuangan_memorial (no_bukti, tanggal, keterangan, created_by) VALUES (?, ?, ?, ?)";
            $stmtHeader = $this->db->prepare($sqlHeader);
            $stmtHeader->execute([
                $data['no_bukti'],
                $data['tanggal'],
                $data['keterangan'],
                $_SESSION['user_id'] ?? 1
            ]);
            $id_memorial = $this->db->lastInsertId();

            // 2. Insert Details
            $sqlDetail = "INSERT INTO keuangan_memorial_detail (id_memorial, kode_akun, nama_akun, tipe, jumlah) VALUES (?, ?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($sqlDetail);

            foreach ($details as $d) {
                // $d structure: ['kode_akun', 'nama_akun', 'tipe' (DEBIT/KREDIT), 'jumlah']
                $stmtDetail->execute([
                    $id_memorial,
                    $d['kode_akun'],
                    $d['nama_akun'],
                    $d['tipe'], // Enum: 'DEBIT' or 'KREDIT'
                    $d['jumlah']
                ]);
            }

            $this->db->commit();
            return $id_memorial;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
