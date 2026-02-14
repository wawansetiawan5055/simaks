-- Tabel Header Memorial
CREATE TABLE IF NOT EXISTS `keuangan_memorial` (
  `id_memorial` int(11) NOT NULL AUTO_INCREMENT,
  `no_bukti` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_memorial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Detail Memorial (Debit/Kredit Lines)
CREATE TABLE IF NOT EXISTS `keuangan_memorial_detail` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_memorial` int(11) NOT NULL,
  `kode_akun` varchar(20) NOT NULL,     -- Menyimpan Kode Akun (misal: 1101, 5101)
  `nama_akun` varchar(100) DEFAULT NULL, -- Snapshot nama akun saat transaksi
  `tipe` enum('DEBIT','KREDIT') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_detail`),
  KEY `id_memorial` (`id_memorial`),
  CONSTRAINT `fk_memorial_header` FOREIGN KEY (`id_memorial`) REFERENCES `keuangan_memorial` (`id_memorial`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
