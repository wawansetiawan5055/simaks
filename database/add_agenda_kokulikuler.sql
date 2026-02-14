-- Create agenda_kokulikuler table for Program Kerja functionality
CREATE TABLE IF NOT EXISTS `agenda_kokulikuler` (
  `id_agenda` int(11) NOT NULL AUTO_INCREMENT,
  `id_kokulikuler` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_agenda` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_agenda`),
  KEY `idx_kokulikuler` (`id_kokulikuler`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
