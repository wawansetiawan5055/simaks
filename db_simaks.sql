-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2026 at 06:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_simaks`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi_guru`
--

CREATE TABLE `absensi_guru` (
  `id_absensi` int(11) NOT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa','Lainnya') DEFAULT NULL,
  `keterangan` varchar(100) DEFAULT NULL,
  `tugas` text DEFAULT NULL,
  `id_guru_piket` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_guru`
--

INSERT INTO `absensi_guru` (`id_absensi`, `id_guru`, `id_ta`, `tanggal`, `status`, `keterangan`, `tugas`, `id_guru_piket`) VALUES
(1, 14, 5, '2026-01-20', 'Hadir', '', '', 1),
(2, 17, 5, '2026-01-20', 'Hadir', '', '', 1),
(3, 2, 5, '2026-01-19', 'Hadir', '', '', 1),
(4, 5, 5, '2026-01-19', 'Hadir', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `absensi_siswa_mapel`
--

CREATE TABLE `absensi_siswa_mapel` (
  `id_absensi` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_guru_mapel` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_ke` varchar(50) DEFAULT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa','Lainnya') DEFAULT NULL,
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `absensi_siswa_piket`
--

CREATE TABLE `absensi_siswa_piket` (
  `id_absensi` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpa','Lainnya') DEFAULT NULL,
  `keterangan` varchar(100) DEFAULT NULL,
  `id_guru_piket` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agenda_kokulikuler`
--

CREATE TABLE `agenda_kokulikuler` (
  `id_agenda` int(11) NOT NULL,
  `id_kokulikuler` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_agenda` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tipe` enum('program','agenda') DEFAULT 'agenda',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agenda_pembiasaan`
--

CREATE TABLE `agenda_pembiasaan` (
  `id_agenda` int(11) NOT NULL,
  `id_pembiasaan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_agenda` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tipe` enum('program','agenda') DEFAULT 'agenda',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `anggota_ekskul`
--

CREATE TABLE `anggota_ekskul` (
  `id_anggota_ekskul` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `nilai` varchar(5) DEFAULT NULL,
  `predikat` varchar(20) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `anggota_kewirausahaan`
--

CREATE TABLE `anggota_kewirausahaan` (
  `id_kewirausahaan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `anggota_kokulikuler`
--

CREATE TABLE `anggota_kokulikuler` (
  `id_anggota` int(11) NOT NULL,
  `id_kokulikuler` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `nilai` int(11) DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `anggota_pembiasaan`
--

CREATE TABLE `anggota_pembiasaan` (
  `id_anggota` int(11) NOT NULL,
  `id_pembiasaan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `anggota_tahfidz`
--

CREATE TABLE `anggota_tahfidz` (
  `id_tahfidz` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_config`
--

CREATE TABLE `app_config` (
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_config`
--

INSERT INTO `app_config` (`config_key`, `config_value`) VALUES
('theme_accent_color', '#fa9200'),
('theme_body_bg', '#d1f0ee'),
('theme_color_body', '#242c38'),
('theme_color_header', '#0ca2ed'),
('theme_color_sidebar_text', '#ffffff'),
('theme_color_small', '#64748b'),
('theme_color_subtitle', '#1e293b'),
('theme_color_table_content', '#000000'),
('theme_color_table_header', '#000000'),
('theme_font_body', '0.85rem'),
('theme_font_header', '1.5rem'),
('theme_font_size', '0.8rem'),
('theme_font_small', '0.75rem'),
('theme_font_subtitle', '1rem'),
('theme_font_table_content', '0.8rem'),
('theme_font_table_header', '0.85rem'),
('theme_footer_bg', '#ffffff'),
('theme_menu_active_bg', '#0f0033'),
('theme_navbar_bg', '#ffffff'),
('theme_sidebar_bg', 'deep_carbon'),
('theme_table_header_bg', '#e87d02');

-- --------------------------------------------------------

--
-- Table structure for table `app_menu`
--

CREATE TABLE `app_menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `icon` varchar(50) DEFAULT 'far fa-circle',
  `parent_id` int(11) DEFAULT 0,
  `urutan` int(11) DEFAULT 0,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_menu`
--

INSERT INTO `app_menu` (`id_menu`, `nama_menu`, `link`, `icon`, `parent_id`, `urutan`, `status`) VALUES
(1, 'Dashboard', 'index.php?mod=dashboard', 'fas fa-tachometer-alt', 0, 1, 'Aktif'),
(2, 'Profil Sekolah', 'index.php?mod=profil_sekolah', 'fas fa-university', 0, 2, 'Aktif'),
(3, 'Manajemen Pengguna', 'index.php?mod=manajemen_pengguna', 'fas fa-users-cog', 0, 3, 'Aktif'),
(4, 'Manajemen Hak Akses', 'index.php?mod=hak_akses', 'fas fa-user-shield', 0, 4, 'Aktif'),
(5, 'Utilitas Database', 'index.php?mod=utilitas_db', 'fas fa-hdd', 0, 5, 'Aktif'),
(10, 'MANAJEMEN DATA', '#', '-', 0, 13, 'Aktif'),
(11, 'Data Master', '#', 'fas fa-database', 0, 14, 'Aktif'),
(12, 'Akademik', '#', 'fas fa-cogs', 0, 22, 'Aktif'),
(20, 'ADMINISTRASI KEGIATAN', '#', '-', 0, 28, 'Aktif'),
(21, 'Isi Jurnal KBM', 'index.php?mod=jurnal_kbm', 'far fa-circle', 4129, 37, 'Aktif'),
(22, 'Absensi Siswa', 'index.php?mod=absensi_mapel', 'far fa-circle', 4129, 38, 'Aktif'),
(23, 'Input Nilai Formatif', 'index.php?mod=input_nilai', 'far fa-circle', 4129, 39, 'Aktif'),
(24, 'Input Nilai Sumatif', 'index.php?mod=penilaian_sumatif', 'far fa-circle', 4129, 40, 'Aktif'),
(25, 'Catatan Kejadian Kelas', 'index.php?mod=catatan_kelas', 'far fa-circle', 4129, 41, 'Aktif'),
(30, 'PIKET & KESISWAAN', '#', '', 0, 48, 'Aktif'),
(31, 'Absensi Siswa Piket', 'index.php?mod=absensi_piket', 'fas fa-clipboard-list', 0, 50, 'Aktif'),
(32, 'Absensi Guru', 'index.php?mod=absensi_guru', 'fas fa-user-check', 0, 49, 'Aktif'),
(33, 'Catatan Kasus Siswa', 'index.php?mod=catatan_kasus', 'fas fa-exclamation-triangle', 0, 51, 'Aktif'),
(40, 'CETAK LAPORAN', '#', '', 0, 74, 'Aktif'),
(41, 'Laporan', '#', 'fas fa-file-alt', 0, 75, 'Aktif'),
(50, 'PORTAL PPDB', '#', '', 0, 90, 'Aktif'),
(51, 'Pendaftaran (PPDB)', '#', 'fas fa-user-plus', 0, 91, 'Aktif'),
(60, 'MUTASI & KELULUSAN', '#', '', 0, 94, 'Aktif'),
(61, 'Mutasi Masuk', 'index.php?mod=mutasi_masuk&act=index', 'fas fa-exchange-alt', 4209, 67, 'Aktif'),
(62, 'Mutasi Keluar', 'index.php?mod=mutasi_siswa&act=form', 'fas fa-user-minus', 4209, 68, 'Aktif'),
(63, 'Data Lulusan', '#', 'fas fa-graduation-cap', 0, 95, 'Aktif'),
(113, 'Kelas', 'index.php?mod=kelas', 'far fa-circle', 11, 17, 'Aktif'),
(114, 'Mata Pelajaran', 'index.php?mod=mapel', 'far fa-circle', 11, 18, 'Aktif'),
(115, 'Master Kegiatan', 'index.php?mod=master_kegiatan', 'far fa-circle', 11, 19, 'Aktif'),
(116, 'Master Jam', 'index.php?mod=master_jam', 'far fa-circle', 11, 20, 'Aktif'),
(117, 'Tahun Pelajaran', 'index.php?mod=ta', 'far fa-circle', 11, 21, 'Aktif'),
(121, 'Struktur Kurikulum', 'index.php?mod=struktur_kurikulum', 'far fa-circle', 12, 24, 'Aktif'),
(122, 'Input CP dan TP', 'index.php?mod=manajemen_cp_tp', 'far fa-circle', 4128, 30, 'Aktif'),
(123, 'Penugasan Guru', 'index.php?mod=penugasan_guru', 'far fa-circle', 12, 25, 'Aktif'),
(124, 'Penempatan Siswa', 'index.php?mod=penempatan', 'far fa-circle', 12, 26, 'Aktif'),
(125, 'Jadwal Pelajaran', 'index.php?mod=jadwal', 'far fa-circle', 12, 27, 'Aktif'),
(411, 'Laporan Siswa', 'index.php?mod=laporan&act=siswa', 'far fa-circle', 41, 76, 'Aktif'),
(412, 'Laporan Guru', 'index.php?mod=laporan&act=guru', 'far fa-circle', 41, 77, 'Aktif'),
(413, 'Laporan Rombel', 'index.php?mod=laporan&act=kelas', 'far fa-circle', 41, 78, 'Aktif'),
(414, 'Laporan Mapel', 'index.php?mod=laporan&act=mapel', 'far fa-circle', 41, 79, 'Aktif'),
(415, 'Laporan Jurnal KBM', 'index.php?mod=laporan&act=jurnal', 'far fa-circle', 41, 80, 'Aktif'),
(416, 'Laporan Absensi Siswa', 'index.php?mod=laporan&act=absensi_siswa_mapel', 'far fa-circle', 41, 81, 'Aktif'),
(417, 'Laporan Absensi Piket', 'index.php?mod=laporan&act=absensi_siswa_piket', 'far fa-circle', 41, 82, 'Aktif'),
(418, 'Laporan Absensi Guru', 'index.php?mod=laporan&act=absensi_guru', 'far fa-circle', 41, 83, 'Aktif'),
(419, 'Laporan Catatan Kasus', 'index.php?mod=laporan&act=catatan_kasus', 'far fa-circle', 41, 84, 'Aktif'),
(511, 'Formulir Pendaftaran', 'index.php?mod=ppdb&act=form', 'far fa-circle', 51, 92, 'Aktif'),
(512, 'Verifikasi Pendaftar', 'index.php?mod=ppdb&act=index', 'far fa-circle', 51, 93, 'Aktif'),
(631, 'Proses Kelulusan', 'index.php?mod=lulusan&act=proses', 'far fa-circle', 63, 96, 'Aktif'),
(632, 'Data Alumni', 'index.php?mod=lulusan&act=index', 'far fa-circle', 63, 97, 'Aktif'),
(4110, 'Laporan Catatan Kelas', 'index.php?mod=laporan&act=catatan_kelas', 'far fa-circle', 41, 85, 'Aktif'),
(4111, 'Laporan PPDB', 'index.php?mod=laporan&act=ppdb', 'far fa-circle', 41, 86, 'Aktif'),
(4112, 'Laporan Mutasi Masuk', 'index.php?mod=laporan&act=mutasi_masuk', 'far fa-circle', 41, 87, 'Aktif'),
(4114, 'Laporan Jadwal', 'index.php?mod=laporan&act=jadwal_pelajaran', 'far fa-circle', 41, 89, 'Aktif'),
(4115, 'Laporan Mutasi Keluar', 'index.php?mod=laporan&act=mutasi_keluar', 'far fa-circle', 41, 88, 'Aktif'),
(4116, 'Manajemen Web', '#', 'fas fa-globe', 0, 9, 'Aktif'),
(4117, 'Pengaturan', 'index.php?mod=landing_admin&act=settings', 'fas fa-cogs', 4116, 10, 'Aktif'),
(4118, 'Berita & Pengumuman', 'index.php?mod=landing_admin&act=news', 'fas fa-newspaper', 4116, 11, 'Aktif'),
(4119, 'Galeri Foto', 'index.php?mod=landing_admin&act=gallery', 'fas fa-images', 4116, 12, 'Aktif'),
(4120, 'Manajemen Peran', 'index.php?mod=peran', 'fas fa-user-tag', 0, 6, 'Aktif'),
(4121, 'Manajemen Menu', 'index.php?mod=app_menu', 'fas fa-list', 0, 8, 'Aktif'),
(4124, 'Ekstrakurikuler', 'index.php?mod=ekskul', 'far fa-circle', 4130, 43, 'Aktif'),
(4126, 'Kokurikuler', 'index.php?mod=kokulikuler', 'far fa-circle', 4130, 47, 'Aktif'),
(4127, 'Pembiasaan Ibadah', 'index.php?mod=pembiasaan', 'far fa-circle', 4130, 46, 'Aktif'),
(4128, 'Perangkat Pembelajaran', '#', 'fas fa-folder-open', 0, 29, 'Aktif'),
(4129, 'Administrasi KBM', '#', 'fas fa-chalkboard-teacher', 0, 36, 'Aktif'),
(4130, 'Administrasi Program', '#', 'fas fa-running', 0, 42, 'Aktif'),
(4133, 'Input ATP', 'index.php?mod=perangkat&act=index&type=atp', 'far fa-circle', 4128, 31, 'Aktif'),
(4134, 'Modul Ajar / RPP', 'index.php?mod=perangkat&act=index&type=modul_ajar', 'far fa-circle', 4128, 32, 'Aktif'),
(4135, 'Program Semester', 'index.php?mod=perangkat&act=index&type=prosem', 'far fa-circle', 4128, 34, 'Aktif'),
(4136, 'Program Tahunan', 'index.php?mod=perangkat&act=index&type=prota', 'far fa-circle', 4128, 33, 'Aktif'),
(4137, 'Kewirausahaan', 'index.php?mod=kewirausahaan', 'far fa-circle', 4130, 44, 'Aktif'),
(4138, 'Tahfidz Quran', 'index.php?mod=tahfidz', 'far fa-circle', 4130, 45, 'Aktif'),
(4139, 'Manajemen Template', 'index.php?mod=template_dokumen&act=index', 'far fa-circle', 4128, 35, 'Aktif'),
(4140, 'Kalender Pendidikan', 'index.php?mod=kalender_akademik', 'far fa-calendar-alt', 12, 23, 'Aktif'),
(4178, 'TATA USAHA', '#', NULL, 0, 52, 'Aktif'),
(4179, 'Keuangan', '#', 'fas fa-money-bill-wave', 0, 53, 'Aktif'),
(4180, 'Dashboard', 'index.php?mod=keuangan_dashboard', 'far fa-circle', 4179, 54, 'Aktif'),
(4181, 'Transaksi Pemasukan', 'index.php?mod=keuangan_transaksi_masuk', 'far fa-circle', 4179, 55, 'Aktif'),
(4182, 'Transaksi Pengeluaran', 'index.php?mod=keuangan_transaksi_keluar', 'far fa-circle', 4179, 56, 'Aktif'),
(4195, 'Rekening Kas & Bank', 'index.php?mod=keuangan_master&act=rekening', 'fas fa-university', 4179, 62, 'Aktif'),
(4196, 'Data Akun Transaksi', 'index.php?mod=keuangan_master&act=coa', 'fas fa-tags', 4179, 57, 'Aktif'),
(4197, 'Group Keuangan', 'index.php?mod=keuangan_master&act=group', 'fas fa-folder', 4179, 61, 'Nonaktif'),
(4198, 'Generate Tagihan Siswa', 'index.php?mod=keuangan_tagihan&act=index', 'fas fa-file-invoice-dollar', 4179, 60, 'Aktif'),
(4204, 'Guru', 'index.php?mod=guru', 'fas fa-user-tie', 11, 15, 'Aktif'),
(4205, 'Siswa', 'index.php?mod=siswa', 'fas fa-user-graduate', 11, 16, 'Aktif'),
(4206, 'Manajemen GTK', '#', 'fas fa-chalkboard-teacher', 0, 63, 'Aktif'),
(4207, 'Data Guru', 'index.php?mod=guru', 'fas fa-user-tie', 4206, 64, 'Aktif'),
(4209, 'Manajemen Siswa', '#', 'fas fa-users', 0, 65, 'Aktif'),
(4210, 'Data Siswa', 'index.php?mod=siswa', 'fas fa-user-graduate', 4209, 66, 'Aktif'),
(4211, 'Audit Log', 'index.php?mod=audit_log', 'fas fa-history', 0, 7, 'Aktif'),
(4212, 'Jurnal Umum', 'index.php?mod=keuangan_jurnal', 'fas fa-book', 4179, 61, 'Aktif'),
(4213, 'Pembantu Penerimaan Kas', 'index.php?mod=keuangan_jurnal_pembantu', 'fas fa-file-invoice-dollar', 4179, 59, 'Aktif'),
(4216, 'Aktivasi Biaya Sekolah', 'index.php?mod=keuangan_tarif&act=matrix', 'fas fa-th-large', 4179, 58, 'Aktif'),
(4217, 'Pengaturan Tampilan', 'index.php?mod=app_config&act=index', 'fas fa-paint-brush', 4218, 99, 'Aktif'),
(4218, 'PENGATURAN SISTEM', '#', 'fas fa-cogs', 0, 98, 'Aktif'),
(4219, 'Buku Kas Umum (BKU)', 'index.php?mod=keuangan_bku', 'fas fa-book-open', 4179, 63, 'Aktif'),
(4220, 'Manajemen Gaji', 'index.php?mod=keuangan_gaji', 'fas fa-money-check-alt', 4179, 64, 'Aktif'),
(4221, 'Manajemen Surat', '#', 'fas fa-envelope-open-text', 0, 66, 'Aktif'),
(4222, 'Dashboard Surat', 'index.php?mod=surat', 'fas fa-tachometer-alt', 4221, 1, 'Aktif'),
(4223, 'Surat Masuk', 'index.php?mod=surat&act=masuk', 'fas fa-file-import', 4221, 2, 'Aktif'),
(4224, 'Surat Keluar', 'index.php?mod=surat&act=keluar', 'fas fa-file-export', 4221, 3, 'Aktif'),
(4225, 'Template Surat', 'index.php?mod=surat&act=template', 'fas fa-scroll', 4221, 4, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
('landing_page_enabled', '1', '2025-12-12 09:20:04', '2025-12-12 09:20:04'),
('landing_slider_interval', '5000', '2025-12-12 09:20:04', '2025-12-12 09:20:04'),
('school_address', 'Alamat Sekolah...', '2025-12-12 09:20:04', '2025-12-12 09:20:04'),
('school_email', 'info@sekolah.sch.id', '2025-12-12 09:20:04', '2025-12-12 09:20:04'),
('school_name', 'Nama Sekolah Anda', '2025-12-12 09:20:04', '2025-12-12 09:20:04'),
('school_phone', '021-xxxxxx', '2025-12-12 09:20:04', '2025-12-12 09:20:04');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id_log` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL COMMENT 'ID user yang melakukan aksi',
  `aksi` varchar(50) NOT NULL COMMENT 'Jenis aksi: LOGIN, CREATE, UPDATE, DELETE, dll',
  `target_tabel` varchar(50) DEFAULT NULL COMMENT 'Nama tabel yang diubah (opsional)',
  `deskripsi` text DEFAULT NULL COMMENT 'Detail aktivitas atau data yang berubah',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP Address pengguna (IPv4/IPv6)',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'Info browser dan device',
  `waktu` datetime DEFAULT current_timestamp() COMMENT 'Waktu kejadian'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel pencatatan aktivitas pengguna (Audit Trail)';

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id_log`, `id_pengguna`, `aksi`, `target_tabel`, `deskripsi`, `ip_address`, `user_agent`, `waktu`) VALUES
(1, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 15:49:14'),
(2, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 15:49:17'),
(3, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:02:51'),
(4, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:02:54'),
(5, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:12:54'),
(6, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:12:56'),
(7, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:29:08'),
(8, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:29:09'),
(9, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:37:03'),
(10, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:37:04'),
(11, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:45:52'),
(12, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:45:54'),
(13, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:59:39'),
(14, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 16:59:41'),
(15, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 20:39:35'),
(16, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 20:42:02'),
(17, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-17 20:42:04'),
(18, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-18 08:51:38'),
(19, 2, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-18 08:51:43'),
(20, 2, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-18 08:54:34'),
(21, 2, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', '2026-01-18 08:54:38'),
(22, 2, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 09:04:39'),
(23, 2, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 09:04:41'),
(24, 2, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 09:04:44'),
(25, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 09:04:56'),
(26, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 12:53:51'),
(27, 2, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 12:53:56'),
(28, 2, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 13:01:39'),
(29, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 13:01:44'),
(30, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 13:02:12'),
(31, 2, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 13:02:16'),
(32, 2, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 13:06:46'),
(33, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 13:06:50'),
(34, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 15:45:21'),
(35, 0, 'LOGIN_FAILED', NULL, 'Gagal login dengan username: 3202112004860004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 15:45:50'),
(36, 25, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 15:46:30'),
(37, 25, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 15:52:52'),
(38, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 15:52:55'),
(39, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 21:55:31'),
(40, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-18 21:55:53'),
(41, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 06:13:42'),
(42, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 09:21:48'),
(43, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 10:28:37'),
(44, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 11:49:32'),
(45, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-19 20:01:33'),
(46, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-20 20:52:36'),
(47, 0, 'LOGIN_FAILED', NULL, 'Gagal login dengan username: 3202112004860004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-20 20:53:38'),
(48, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-20 20:53:43'),
(49, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-21 08:50:23'),
(50, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-21 10:09:38'),
(51, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-21 10:42:24'),
(52, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-25 07:31:23'),
(53, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-25 11:01:14'),
(54, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-27 06:10:14'),
(55, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-27 10:27:40'),
(56, 1, 'LOGOUT', NULL, 'User keluar dari sistem', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-27 11:56:54'),
(57, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-27 11:57:42'),
(58, 1, 'LOGIN', NULL, 'User masuk ke sistem (Login Sukses)', '192.168.1.29', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 12:19:18');

-- --------------------------------------------------------

--
-- Table structure for table `capaian_pembelajaran`
--

CREATE TABLE `capaian_pembelajaran` (
  `id_cp` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `fase` enum('A','B','C','D','E','F') NOT NULL COMMENT 'Contoh: E untuk kelas X, F untuk XI-XII',
  `deskripsi_cp` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catatan_kasus`
--

CREATE TABLE `catatan_kasus` (
  `id_catatan` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_guru_piket` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tindak_lanjut` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catatan_kelas`
--

CREATE TABLE `catatan_kelas` (
  `id_catatan_kelas` int(11) NOT NULL,
  `id_jadwal_mengajar` int(11) NOT NULL COMMENT 'FK ke jadwal_mengajar',
  `id_ta` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `catatan_kejadian` text DEFAULT NULL,
  `waktu_input` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ekskul_galeri`
--

CREATE TABLE `ekskul_galeri` (
  `id_galeri` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ekskul_program_kerja`
--

CREATE TABLE `ekskul_program_kerja` (
  `id_program` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL,
  `tipe` enum('program','agenda') DEFAULT 'agenda',
  `tanggal` date NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ekstrakurikuler`
--

CREATE TABLE `ekstrakurikuler` (
  `id_ekskul` int(11) NOT NULL,
  `nama_ekskul` varchar(100) NOT NULL,
  `kategori` enum('Ekstrakurikuler','Kokulikuler') NOT NULL DEFAULT 'Ekstrakurikuler',
  `id_guru_pembina` int(11) DEFAULT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ekstrakurikuler`
--

INSERT INTO `ekstrakurikuler` (`id_ekskul`, `nama_ekskul`, `kategori`, `id_guru_pembina`, `hari`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(1, 'Englis Club', 'Ekstrakurikuler', 11, 'Sabtu', '14:00:00', '15:00:00', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `galeri_pembiasaan`
--

CREATE TABLE `galeri_pembiasaan` (
  `id_galeri` int(11) NOT NULL,
  `id_pembiasaan` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id_guru` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kode_guru` varchar(5) DEFAULT NULL,
  `nuptk` varchar(30) DEFAULT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `jk` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `status_kepegawaian` varchar(50) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif','Pensiun') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id_guru`, `id_pengguna`, `nama`, `kode_guru`, `nuptk`, `nik`, `jk`, `tempat_lahir`, `tanggal_lahir`, `status_kepegawaian`, `status`) VALUES
(1, 9, 'Ai Siti Robiatul Awaliyah', '1', '9042774675130013', '3202125007960009', 'Perempuan', 'Sukabumi', '1996-07-10', 'GTY', 'Aktif'),
(2, 10, 'Awaludin Hardiana', '2', '8445764665200002', '3202121301860004', 'Laki-laki', 'Sukabumi', '1986-01-13', 'GTY', 'Aktif'),
(3, 11, 'Dadan Silahudin', '3', '8747751653200032', '3202121204730001', 'Laki-laki', 'Sukabumi', '1973-04-15', 'GTY', 'Aktif'),
(4, 12, 'Dadun Abdul Manaf', '4', '3844760662200022', '3202291205820011', 'Laki-laki', 'Sukabumi', '1982-05-12', 'GTY', 'Aktif'),
(5, 13, 'Euis Sobariah', '5', '3533763664300013', '3202124112850003', 'Perempuan', 'Sukabumi', '1985-12-01', 'GTY', 'Aktif'),
(6, 14, 'Falah Ependi', '6', '1242740644200003', '3202292012001365', 'Laki-laki', 'Sukabumi', '1962-09-10', 'GTY', 'Aktif'),
(7, 15, 'Kiki Kurniawan', '7', '0533773674130043', '3202120112950007', 'Laki-laki', 'Sukabumi', '1995-12-01', 'GTY', 'Aktif'),
(8, 16, 'Komariah', '8', '5633742642300012', '3272034103640001', 'Perempuan', 'Sukabumi', '1964-03-01', 'GTY', 'Aktif'),
(9, 17, 'Maya Meira', '9', '0839777678230162', '3202124705990003', 'Perempuan', 'Sukabumi', '1999-05-07', 'GTY', 'Aktif'),
(10, 18, 'Nani Maryani', '10', '', '3202124501970005', 'Perempuan', 'Sukabumi', '1997-01-05', 'GTY', 'Aktif'),
(11, 19, 'Peri Barkah', '11', '0038765667200013', '3202120706870006', 'Laki-laki', 'Sukabumi', '1987-06-07', 'GTY', 'Aktif'),
(12, 20, 'Pura Disadad', '12', '0045756657200023', '3202121307780004', 'Laki-laki', 'Sukabumi', '1978-07-13', 'GTY', 'Aktif'),
(13, 21, 'Risdiantika Kamsiel', '13', '8050767667210003', '3202125807890004', 'Perempuan', 'Sukabumi', '1989-07-18', 'GTY', 'Aktif'),
(14, 22, 'Roni Paslah', '14', '3447752652200002', '3202121505740001', 'Laki-laki', 'Sukabumi', '1974-05-15', 'GTY', 'Aktif'),
(15, 23, 'Saepudin', '15', '0047765667130313', '3202121507870003', 'Laki-laki', 'Sukabumi', '1987-07-15', 'GTY', 'Aktif'),
(16, 24, 'Tini Sumartini', '16', '4051775676230013', '3202125907970003', 'Perempuan', 'Sukabumi', '1997-07-19', 'GTY', 'Aktif'),
(17, 25, 'Wawan Setiawan', '17', '7752764666110042', '3202112004860004', 'Laki-laki', 'Sukabumi', '1986-04-20', 'GTY', 'Aktif'),
(18, 26, 'Zaenal Mutaqin Ahirudin', '18', '5551768669130143', '3205111912900002', 'Laki-laki', 'Garut', '1990-12-19', 'GTY', 'Aktif'),
(19, 27, 'Zaidan Ahmad Rabbani', '19', '2255773674130313', '3217072309950002', 'Laki-laki', 'Bandung', '1995-09-23', 'GTY', 'Aktif'),
(20, 28, 'Usep Sanusi', '20', '', '', 'Laki-laki', 'Sukabumi', NULL, 'GTY', 'Aktif'),
(21, 29, 'Tim Tahfidz', '21', '', '', '', 'Sukabumi', NULL, 'GTY', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `guru_mapel`
--

CREATE TABLE `guru_mapel` (
  `id_guru_mapel` int(11) NOT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `id_mapel` int(11) DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru_mapel`
--

INSERT INTO `guru_mapel` (`id_guru_mapel`, `id_guru`, `id_mapel`, `id_ta`) VALUES
(1, 18, 1, 2),
(2, 5, 1, 2),
(3, 2, 2, 2),
(4, 9, 3, 2),
(5, 5, 1, 5),
(6, 2, 2, 5),
(7, 14, 3, 5),
(8, 17, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `hak_akses`
--

CREATE TABLE `hak_akses` (
  `id_akses` int(11) NOT NULL,
  `id_peran` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `can_create` tinyint(1) DEFAULT 0,
  `can_read` tinyint(1) DEFAULT 1,
  `can_update` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hak_akses`
--

INSERT INTO `hak_akses` (`id_akses`, `id_peran`, `id_menu`, `can_create`, `can_read`, `can_update`, `can_delete`) VALUES
(235, 5, 1, 0, 1, 0, 0),
(236, 5, 31, 1, 1, 1, 1),
(237, 5, 32, 1, 1, 1, 1),
(238, 5, 33, 1, 1, 1, 1),
(239, 5, 417, 1, 1, 1, 1),
(240, 5, 418, 1, 1, 1, 1),
(241, 3, 1, 0, 1, 0, 0),
(242, 3, 121, 0, 1, 0, 0),
(243, 3, 123, 0, 1, 0, 0),
(244, 3, 124, 0, 1, 0, 0),
(245, 3, 125, 0, 1, 0, 0),
(246, 3, 411, 0, 1, 0, 0),
(247, 3, 412, 0, 1, 0, 0),
(248, 3, 413, 0, 1, 0, 0),
(249, 3, 414, 0, 1, 0, 0),
(250, 3, 415, 0, 1, 0, 0),
(251, 3, 416, 0, 1, 0, 0),
(252, 3, 417, 0, 1, 0, 0),
(253, 3, 418, 0, 1, 0, 0),
(254, 3, 419, 0, 1, 0, 0),
(255, 3, 4110, 0, 1, 0, 0),
(256, 3, 4111, 0, 1, 0, 0),
(257, 3, 4112, 0, 1, 0, 0),
(258, 3, 4115, 0, 1, 0, 0),
(259, 3, 4114, 0, 1, 0, 0),
(260, 3, 512, 0, 1, 0, 0),
(262, 3, 632, 0, 1, 0, 0),
(263, 3, 4117, 0, 1, 0, 0),
(264, 3, 4118, 0, 1, 0, 0),
(265, 3, 4119, 0, 1, 0, 0),
(266, 9, 1, 0, 1, 0, 0),
(268, 9, 124, 0, 1, 0, 0),
(269, 9, 411, 0, 1, 0, 0),
(270, 9, 413, 0, 1, 0, 0),
(271, 9, 417, 0, 1, 0, 0),
(272, 9, 419, 0, 1, 0, 0),
(273, 9, 4110, 0, 1, 0, 0),
(274, 9, 4112, 0, 1, 0, 0),
(275, 9, 4115, 0, 1, 0, 0),
(277, 9, 632, 0, 1, 0, 0),
(300, 7, 1, 0, 1, 0, 0),
(301, 7, 511, 0, 1, 0, 0),
(302, 7, 512, 0, 1, 0, 0),
(303, 8, 1, 0, 1, 0, 0),
(306, 8, 113, 0, 1, 0, 0),
(307, 8, 114, 0, 1, 0, 0),
(308, 8, 115, 0, 1, 0, 0),
(309, 8, 116, 0, 1, 0, 0),
(310, 8, 117, 0, 1, 0, 0),
(311, 8, 121, 1, 1, 1, 1),
(312, 8, 122, 1, 1, 1, 1),
(313, 8, 123, 1, 1, 1, 1),
(314, 8, 124, 1, 1, 1, 1),
(315, 8, 125, 1, 1, 1, 1),
(316, 8, 411, 0, 1, 0, 0),
(317, 8, 412, 0, 1, 0, 0),
(318, 8, 413, 0, 1, 0, 0),
(319, 8, 414, 0, 1, 0, 0),
(320, 8, 415, 0, 1, 0, 0),
(321, 8, 416, 0, 1, 0, 0),
(322, 8, 417, 0, 1, 0, 0),
(323, 8, 418, 0, 1, 0, 0),
(324, 8, 4114, 0, 1, 0, 0),
(571, 17, 21, 1, 1, 1, 1),
(572, 17, 22, 1, 1, 1, 1),
(625, 3, 4124, 1, 1, 1, 1),
(627, 8, 4124, 1, 1, 1, 1),
(628, 9, 4124, 1, 1, 1, 1),
(629, 15, 4124, 1, 1, 1, 1),
(630, 14, 4124, 1, 1, 1, 1),
(632, 3, 4126, 1, 1, 1, 1),
(634, 8, 4126, 1, 1, 1, 1),
(635, 9, 4126, 1, 1, 1, 1),
(636, 15, 4126, 1, 1, 1, 1),
(637, 14, 4126, 1, 1, 1, 1),
(639, 3, 4127, 1, 1, 1, 1),
(641, 8, 4127, 1, 1, 1, 1),
(642, 9, 4127, 1, 1, 1, 1),
(643, 15, 4127, 1, 1, 1, 1),
(644, 14, 4127, 1, 1, 1, 1),
(646, 3, 20, 1, 1, 1, 1),
(648, 8, 20, 1, 1, 1, 1),
(649, 9, 20, 1, 1, 1, 1),
(650, 15, 20, 1, 1, 1, 1),
(651, 14, 20, 1, 1, 1, 1),
(653, 3, 4128, 1, 1, 1, 1),
(655, 8, 4128, 1, 1, 1, 1),
(656, 9, 4128, 1, 1, 1, 1),
(657, 15, 4128, 1, 1, 1, 1),
(658, 14, 4128, 1, 1, 1, 1),
(660, 3, 4129, 1, 1, 1, 1),
(662, 8, 4129, 1, 1, 1, 1),
(663, 9, 4129, 1, 1, 1, 1),
(664, 15, 4129, 1, 1, 1, 1),
(665, 14, 4129, 1, 1, 1, 1),
(667, 3, 4130, 1, 1, 1, 1),
(669, 8, 4130, 1, 1, 1, 1),
(670, 9, 4130, 1, 1, 1, 1),
(671, 15, 4130, 1, 1, 1, 1),
(672, 14, 4130, 1, 1, 1, 1),
(695, 3, 4133, 1, 1, 1, 1),
(697, 8, 4133, 1, 1, 1, 1),
(698, 9, 4133, 1, 1, 1, 1),
(699, 15, 4133, 1, 1, 1, 1),
(700, 14, 4133, 1, 1, 1, 1),
(702, 3, 4134, 1, 1, 1, 1),
(704, 8, 4134, 1, 1, 1, 1),
(705, 9, 4134, 1, 1, 1, 1),
(706, 15, 4134, 1, 1, 1, 1),
(707, 14, 4134, 1, 1, 1, 1),
(709, 3, 4135, 1, 1, 1, 1),
(711, 8, 4135, 1, 1, 1, 1),
(712, 9, 4135, 1, 1, 1, 1),
(713, 15, 4135, 1, 1, 1, 1),
(714, 14, 4135, 1, 1, 1, 1),
(716, 3, 4136, 1, 1, 1, 1),
(718, 8, 4136, 1, 1, 1, 1),
(719, 9, 4136, 1, 1, 1, 1),
(720, 15, 4136, 1, 1, 1, 1),
(721, 14, 4136, 1, 1, 1, 1),
(778, 3, 4137, 1, 1, 1, 1),
(780, 8, 4137, 1, 1, 1, 1),
(781, 9, 4137, 1, 1, 1, 1),
(782, 15, 4137, 1, 1, 1, 1),
(783, 14, 4137, 1, 1, 1, 1),
(785, 3, 4138, 1, 1, 1, 1),
(787, 8, 4138, 1, 1, 1, 1),
(788, 9, 4138, 1, 1, 1, 1),
(789, 15, 4138, 1, 1, 1, 1),
(790, 14, 4138, 1, 1, 1, 1),
(830, 3, 4140, 0, 1, 0, 0),
(832, 8, 4140, 1, 1, 1, 1),
(833, 9, 4140, 0, 1, 0, 0),
(834, 14, 4140, 0, 1, 0, 0),
(835, 15, 4140, 0, 1, 0, 0),
(836, 17, 4140, 0, 1, 0, 0),
(1299, 2, 1, 1, 1, 1, 1),
(1300, 2, 4140, 0, 1, 0, 0),
(1301, 2, 121, 0, 1, 0, 0),
(1302, 2, 123, 0, 1, 0, 0),
(1303, 2, 124, 0, 1, 0, 0),
(1304, 2, 125, 0, 1, 0, 0),
(1305, 2, 4180, 1, 1, 1, 1),
(1306, 2, 4181, 1, 1, 1, 1),
(1307, 2, 4182, 1, 1, 1, 1),
(1308, 2, 4207, 1, 1, 1, 1),
(1309, 2, 4210, 1, 1, 1, 1),
(1310, 2, 61, 1, 1, 1, 1),
(1311, 2, 62, 1, 1, 1, 1),
(1316, 2, 411, 1, 1, 1, 1),
(1317, 2, 412, 1, 1, 1, 1),
(1318, 2, 413, 1, 1, 1, 1),
(1319, 2, 414, 1, 1, 1, 1),
(1320, 2, 417, 1, 1, 1, 1),
(1321, 2, 418, 1, 1, 1, 1),
(1322, 2, 4111, 1, 1, 1, 1),
(1323, 2, 4112, 1, 1, 1, 1),
(1324, 2, 4115, 1, 1, 1, 1),
(1325, 2, 4114, 1, 1, 1, 1),
(1326, 2, 511, 1, 1, 1, 1),
(1327, 2, 632, 1, 1, 1, 1),
(1331, 1, 1, 1, 1, 1, 1),
(1332, 1, 2, 1, 1, 1, 1),
(1333, 1, 3, 1, 1, 1, 1),
(1334, 1, 4, 1, 1, 1, 1),
(1335, 1, 5, 1, 1, 1, 1),
(1336, 1, 4120, 1, 1, 1, 1),
(1337, 1, 4211, 1, 1, 1, 1),
(1338, 1, 4121, 1, 1, 1, 1),
(1339, 1, 4117, 1, 1, 1, 1),
(1340, 1, 4118, 1, 1, 1, 1),
(1341, 1, 4119, 1, 1, 1, 1),
(1342, 1, 4204, 1, 1, 1, 1),
(1343, 1, 4205, 1, 1, 1, 1),
(1344, 1, 113, 1, 1, 1, 1),
(1345, 1, 114, 1, 1, 1, 1),
(1346, 1, 115, 1, 1, 1, 1),
(1347, 1, 116, 1, 1, 1, 1),
(1348, 1, 117, 1, 1, 1, 1),
(1349, 1, 4140, 1, 1, 1, 1),
(1350, 1, 121, 1, 1, 1, 1),
(1351, 1, 123, 1, 1, 1, 1),
(1352, 1, 124, 1, 1, 1, 1),
(1353, 1, 125, 1, 1, 1, 1),
(1354, 1, 122, 1, 1, 1, 1),
(1355, 1, 4133, 1, 1, 1, 1),
(1356, 1, 4134, 1, 1, 1, 1),
(1357, 1, 4136, 1, 1, 1, 1),
(1358, 1, 4135, 1, 1, 1, 1),
(1359, 1, 4139, 1, 1, 1, 1),
(1360, 1, 21, 1, 1, 1, 1),
(1361, 1, 22, 1, 1, 1, 1),
(1362, 1, 23, 1, 1, 1, 1),
(1363, 1, 24, 1, 1, 1, 1),
(1364, 1, 25, 1, 1, 1, 1),
(1365, 1, 4124, 1, 1, 1, 1),
(1366, 1, 4137, 1, 1, 1, 1),
(1367, 1, 4138, 1, 1, 1, 1),
(1368, 1, 4127, 1, 1, 1, 1),
(1369, 1, 4126, 1, 1, 1, 1),
(1370, 1, 32, 1, 1, 1, 1),
(1371, 1, 31, 1, 1, 1, 1),
(1372, 1, 33, 1, 1, 1, 1),
(1373, 1, 4180, 1, 1, 1, 1),
(1374, 1, 4181, 1, 1, 1, 1),
(1375, 1, 4182, 1, 1, 1, 1),
(1376, 1, 4196, 1, 1, 1, 1),
(1377, 1, 4216, 1, 1, 1, 1),
(1378, 1, 4213, 1, 1, 1, 1),
(1379, 1, 4198, 1, 1, 1, 1),
(1380, 1, 4197, 1, 1, 1, 1),
(1381, 1, 4212, 1, 1, 1, 1),
(1382, 1, 4195, 1, 1, 1, 1),
(1383, 1, 4207, 1, 1, 1, 1),
(1384, 1, 4210, 1, 1, 1, 1),
(1385, 1, 61, 1, 1, 1, 1),
(1386, 1, 62, 1, 1, 1, 1),
(1391, 1, 411, 1, 1, 1, 1),
(1392, 1, 412, 1, 1, 1, 1),
(1393, 1, 413, 1, 1, 1, 1),
(1394, 1, 414, 1, 1, 1, 1),
(1395, 1, 415, 1, 1, 1, 1),
(1396, 1, 416, 1, 1, 1, 1),
(1397, 1, 417, 1, 1, 1, 1),
(1398, 1, 418, 1, 1, 1, 1),
(1399, 1, 419, 1, 1, 1, 1),
(1400, 1, 4110, 1, 1, 1, 1),
(1401, 1, 4111, 1, 1, 1, 1),
(1402, 1, 4112, 1, 1, 1, 1),
(1403, 1, 4115, 1, 1, 1, 1),
(1404, 1, 4114, 1, 1, 1, 1),
(1405, 1, 511, 1, 1, 1, 1),
(1406, 1, 512, 1, 1, 1, 1),
(1407, 1, 631, 1, 1, 1, 1),
(1408, 1, 632, 1, 1, 1, 1),
(1409, 1, 4217, 1, 1, 1, 1),
(1410, 1, 4218, 1, 1, 1, 1),
(1411, 4, 1, 0, 1, 0, 0),
(1412, 4, 4204, 1, 1, 1, 1),
(1413, 4, 4205, 0, 1, 1, 0),
(1414, 4, 113, 0, 1, 0, 0),
(1415, 4, 114, 0, 1, 0, 0),
(1416, 4, 4140, 0, 1, 0, 0),
(1417, 4, 121, 0, 1, 0, 0),
(1418, 4, 123, 0, 1, 0, 0),
(1419, 4, 124, 0, 1, 0, 0),
(1420, 4, 125, 0, 1, 0, 0),
(1421, 4, 122, 1, 1, 1, 1),
(1422, 4, 4133, 1, 1, 1, 1),
(1423, 4, 4134, 1, 1, 1, 1),
(1424, 4, 4136, 1, 1, 1, 1),
(1425, 4, 4135, 1, 1, 1, 1),
(1426, 4, 21, 1, 1, 1, 1),
(1427, 4, 22, 1, 1, 1, 1),
(1428, 4, 23, 1, 1, 1, 1),
(1429, 4, 24, 1, 1, 1, 1),
(1430, 4, 25, 1, 1, 1, 1),
(1431, 4, 415, 1, 1, 1, 1),
(1432, 4, 416, 1, 1, 1, 1),
(1433, 4, 418, 1, 1, 1, 1),
(1434, 4, 4110, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_mengajar`
--

CREATE TABLE `jadwal_mengajar` (
  `id_jadwal_mengajar` int(11) NOT NULL,
  `id_guru_mapel` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `hari_kbm` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') DEFAULT NULL,
  `id_jam` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jam_pelajaran`
--

CREATE TABLE `jam_pelajaran` (
  `id_jam` int(11) NOT NULL,
  `urutan` int(11) DEFAULT NULL,
  `label_jam_ke` varchar(10) DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `durasi_menit` int(11) DEFAULT NULL,
  `id_kegiatan` int(11) DEFAULT NULL,
  `nama_kegiatan_custom` varchar(100) DEFAULT NULL COMMENT 'Untuk nama kegiatan yg tidak ada di master',
  `jenis_kegiatan` enum('KBM','Istirahat','Pembiasaan','Lainnya') NOT NULL DEFAULT 'KBM'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jam_pelajaran`
--

INSERT INTO `jam_pelajaran` (`id_jam`, `urutan`, `label_jam_ke`, `jam_mulai`, `jam_selesai`, `durasi_menit`, `id_kegiatan`, `nama_kegiatan_custom`, `jenis_kegiatan`) VALUES
(5, 1, '1', '08:00:00', '08:35:00', 35, 14, NULL, 'KBM');

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_ekstrakurikuler`
--

CREATE TABLE `jurnal_ekstrakurikuler` (
  `id_jurnal` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `materi` text NOT NULL,
  `keterangan` text DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurnal_ekstrakurikuler`
--

INSERT INTO `jurnal_ekstrakurikuler` (`id_jurnal`, `id_ekskul`, `tanggal`, `materi`, `keterangan`, `id_guru`, `created_at`) VALUES
(1, 1, '2026-01-21', 'fdhdhf', 'hdfh', 0, '2026-01-21 05:06:38');

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_kbm`
--

CREATE TABLE `jurnal_kbm` (
  `id_jurnal` int(11) NOT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_ke` varchar(50) DEFAULT NULL,
  `tujuan_pembelajaran` text DEFAULT NULL,
  `tagihan` text DEFAULT NULL,
  `catatan_absensi` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_kewirausahaan`
--

CREATE TABLE `jurnal_kewirausahaan` (
  `id_jurnal` int(11) NOT NULL,
  `id_kewirausahaan` int(11) DEFAULT NULL,
  `id_tahapan` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `materi` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_kokulikuler`
--

CREATE TABLE `jurnal_kokulikuler` (
  `id_jurnal` int(11) NOT NULL,
  `id_kokulikuler` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `materi` text NOT NULL,
  `keterangan` text DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_pembiasaan`
--

CREATE TABLE `jurnal_pembiasaan` (
  `id_jurnal` int(11) NOT NULL,
  `id_pembiasaan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `materi` text NOT NULL,
  `keterangan` text DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_tahfidz`
--

CREATE TABLE `jurnal_tahfidz` (
  `id_jurnal` int(11) NOT NULL,
  `id_tahfidz` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `materi` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kalender_akademik`
--

CREATE TABLE `kalender_akademik` (
  `id_kalender` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `judul_kegiatan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `kategori` enum('Libur','Ujian','Kegiatan Sekolah','Rapat','Lainnya') NOT NULL DEFAULT 'Lainnya',
  `warna` varchar(7) DEFAULT '#3788d8',
  `is_recurring` tinyint(1) DEFAULT 0,
  `recurring_type` enum('daily','weekly','monthly','yearly') DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kalender_akademik`
--

INSERT INTO `kalender_akademik` (`id_kalender`, `id_ta`, `judul_kegiatan`, `deskripsi`, `tanggal_mulai`, `tanggal_selesai`, `kategori`, `warna`, `is_recurring`, `recurring_type`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 5, 'jnjnj', 'jnnjn', '2026-01-09', '2026-01-09', 'Libur', '#dc3545', 0, NULL, NULL, '2026-01-09 05:54:30', '2026-01-09 05:54:30');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `nama_kelas` varchar(30) DEFAULT NULL,
  `tingkat` enum('X','XI','XII') DEFAULT NULL,
  `id_ta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `nama_kelas`, `tingkat`, `id_ta`) VALUES
(1, 'X.1', 'X', 1),
(2, 'X.2', 'X', 1),
(3, 'X.1', 'X', 3),
(4, 'X.2', 'X', 3),
(5, 'XI.1', 'XI', 3),
(6, 'XI.2', 'XI', 3),
(7, 'X.1', 'X', 2),
(8, 'X.2', 'X', 2),
(9, 'XII.1', 'XII', 3),
(10, 'X.1', 'X', 4),
(11, 'X.2', 'X', 4),
(12, 'XI.1', 'XI', 4),
(13, 'XI.2', 'XI', 4),
(14, 'XII.1', 'XII', 4),
(15, 'X.1', 'X', 5),
(16, 'X.2', 'X', 5),
(17, 'X.3', 'X', 5),
(18, 'X.4', 'X', 5),
(19, 'XI.1', 'XI', 5),
(20, 'XI.2', 'XI', 5),
(21, 'XII.1', 'XII', 5),
(22, 'XII.2', 'XII', 5);

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_anggaran`
--

CREATE TABLE `keuangan_anggaran` (
  `id_anggaran` int(11) NOT NULL,
  `tahun_ajaran` varchar(10) NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `jumlah_anggaran` decimal(15,2) NOT NULL,
  `realisasi` decimal(15,2) DEFAULT 0.00,
  `sisa_anggaran` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_gaji`
--

CREATE TABLE `keuangan_gaji` (
  `id_gaji` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `tgl_generate` date DEFAULT NULL,
  `total_pengeluaran` decimal(15,2) DEFAULT 0.00,
  `status` enum('DRAFT','FINAL') DEFAULT 'DRAFT',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_gaji`
--

INSERT INTO `keuangan_gaji` (`id_gaji`, `bulan`, `tahun`, `id_ta`, `tgl_generate`, `total_pengeluaran`, `status`, `created_by`, `created_at`) VALUES
(18, 1, 2026, 5, '2026-01-21', 4300000.00, 'DRAFT', NULL, '2026-01-21 16:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_gaji_detail`
--

CREATE TABLE `keuangan_gaji_detail` (
  `id_detail` int(11) NOT NULL,
  `id_gaji` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `jml_jjm` int(11) DEFAULT 0,
  `tarif_jjm` decimal(15,2) DEFAULT 0.00,
  `subtotal_jjm` decimal(15,2) DEFAULT 0.00,
  `jml_hadir` int(11) DEFAULT 0,
  `tarif_transport` decimal(15,2) DEFAULT 0.00,
  `subtotal_transport` decimal(15,2) DEFAULT 0.00,
  `jml_kbm` int(11) DEFAULT 0,
  `tarif_kinerja` decimal(15,2) DEFAULT 0.00,
  `subtotal_kinerja` decimal(15,2) DEFAULT 0.00,
  `jml_ekskul` int(11) DEFAULT 0,
  `gaji_pokok` decimal(15,2) DEFAULT 0.00,
  `tunjangan_jabatan` decimal(15,2) DEFAULT 0.00,
  `tunjangan_wali_kelas` decimal(15,2) DEFAULT 0.00,
  `tunjangan_pembina` decimal(15,2) DEFAULT 0.00,
  `tunjangan_ekskul` decimal(15,2) DEFAULT 0.00,
  `tunjangan_lain` decimal(15,2) DEFAULT 0.00,
  `potongan_kasbon` decimal(15,2) DEFAULT 0.00,
  `potongan_bpjs_kes` decimal(15,2) DEFAULT 0.00,
  `potongan_bpjs_tk` decimal(15,2) DEFAULT 0.00,
  `potongan_lain` decimal(15,2) DEFAULT 0.00,
  `total_diterima` decimal(15,2) DEFAULT 0.00,
  `tunj_kepsek` decimal(15,2) DEFAULT 0.00,
  `tunj_kurikulum` decimal(15,2) DEFAULT 0.00,
  `tunj_kesiswaan` decimal(15,2) DEFAULT 0.00,
  `tunj_sarpras` decimal(15,2) DEFAULT 0.00,
  `tunj_humas` decimal(15,2) DEFAULT 0.00,
  `tunj_kepala_lab` decimal(15,2) DEFAULT 0.00,
  `tunj_kepala_perpus` decimal(15,2) DEFAULT 0.00,
  `tunj_ekskul` decimal(15,2) DEFAULT 0.00,
  `tunj_pembina` decimal(15,2) DEFAULT 0.00,
  `tunj_tas` decimal(15,2) DEFAULT 0.00,
  `tunj_plk` decimal(15,2) DEFAULT 0.00,
  `tunj_penjaga` decimal(15,2) DEFAULT 0.00,
  `tunj_satpam` decimal(15,2) DEFAULT 0.00,
  `tunj_sopir` decimal(15,2) DEFAULT 0.00,
  `tunj_operator` decimal(15,2) DEFAULT 0.00,
  `tunj_pembina_keagamaan` decimal(15,2) DEFAULT 0.00,
  `tunj_pengelola_smater` decimal(15,2) DEFAULT 0.00,
  `tunj_walas` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_gaji_detail`
--

INSERT INTO `keuangan_gaji_detail` (`id_detail`, `id_gaji`, `id_guru`, `jml_jjm`, `tarif_jjm`, `subtotal_jjm`, `jml_hadir`, `tarif_transport`, `subtotal_transport`, `jml_kbm`, `tarif_kinerja`, `subtotal_kinerja`, `jml_ekskul`, `gaji_pokok`, `tunjangan_jabatan`, `tunjangan_wali_kelas`, `tunjangan_pembina`, `tunjangan_ekskul`, `tunjangan_lain`, `potongan_kasbon`, `potongan_bpjs_kes`, `potongan_bpjs_tk`, `potongan_lain`, `total_diterima`, `tunj_kepsek`, `tunj_kurikulum`, `tunj_kesiswaan`, `tunj_sarpras`, `tunj_humas`, `tunj_kepala_lab`, `tunj_kepala_perpus`, `tunj_ekskul`, `tunj_pembina`, `tunj_tas`, `tunj_plk`, `tunj_penjaga`, `tunj_satpam`, `tunj_sopir`, `tunj_operator`, `tunj_pembina_keagamaan`, `tunj_pengelola_smater`, `tunj_walas`) VALUES
(232, 18, 1, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(233, 18, 2, 0, 25000.00, 0.00, 1, 25000.00, 25000.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 25000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(234, 18, 3, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(235, 18, 4, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1500000.00, 1500000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(236, 18, 5, 0, 25000.00, 0.00, 1, 25000.00, 25000.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 125000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00),
(237, 18, 6, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(238, 18, 7, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00),
(239, 18, 8, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(240, 18, 9, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(241, 18, 10, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(242, 18, 11, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 300000.00, 0.00, 0.00, 300000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(243, 18, 12, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00),
(244, 18, 13, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(245, 18, 14, 0, 25000.00, 0.00, 1, 25000.00, 25000.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 425000.00, 0.00, 300000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00),
(246, 18, 15, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(247, 18, 21, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(248, 18, 16, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(249, 18, 20, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(250, 18, 17, 0, 25000.00, 0.00, 1, 25000.00, 25000.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 625000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 300000.00, 0.00, 0.00, 100000.00),
(251, 18, 18, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00),
(252, 18, 19, 0, 25000.00, 0.00, 0, 25000.00, 0.00, 0, 5000.00, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_gaji_rules`
--

CREATE TABLE `keuangan_gaji_rules` (
  `id` int(11) NOT NULL,
  `id_guru` int(11) DEFAULT 0,
  `tarif_jjm` decimal(15,2) DEFAULT 0.00,
  `tarif_transport` decimal(15,2) DEFAULT 0.00,
  `tarif_kinerja` decimal(15,2) DEFAULT 0.00,
  `potongan_bpjs_kes` decimal(15,2) DEFAULT 0.00,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `potongan_bpjs_tk` decimal(15,2) DEFAULT 0.00,
  `potongan_kasbon` decimal(15,2) DEFAULT 0.00,
  `potongan_lain` decimal(15,2) DEFAULT 0.00,
  `gaji_pokok` decimal(15,2) DEFAULT 0.00,
  `tunjangan_jabatan` decimal(15,2) DEFAULT 0.00,
  `tunjangan_lain` decimal(15,2) DEFAULT 0.00,
  `tarif_wali_kelas` decimal(15,2) DEFAULT 0.00,
  `tarif_pembina` decimal(15,2) DEFAULT 0.00,
  `tarif_ekskul` decimal(15,2) DEFAULT 0.00,
  `tunj_kepsek` decimal(15,2) DEFAULT 0.00,
  `tunj_kurikulum` decimal(15,2) DEFAULT 0.00,
  `tunj_kesiswaan` decimal(15,2) DEFAULT 0.00,
  `tunj_sarpras` decimal(15,2) DEFAULT 0.00,
  `tunj_humas` decimal(15,2) DEFAULT 0.00,
  `tunj_kepala_lab` decimal(15,2) DEFAULT 0.00,
  `tunj_kepala_perpus` decimal(15,2) DEFAULT 0.00,
  `tunj_ekskul` decimal(15,2) DEFAULT 0.00,
  `tunj_pembina` decimal(15,2) DEFAULT 0.00,
  `tunj_tas` decimal(15,2) DEFAULT 0.00,
  `tunj_plk` decimal(15,2) DEFAULT 0.00,
  `tunj_penjaga` decimal(15,2) DEFAULT 0.00,
  `tunj_satpam` decimal(15,2) DEFAULT 0.00,
  `tunj_sopir` decimal(15,2) DEFAULT 0.00,
  `tunj_operator` decimal(15,2) DEFAULT 0.00,
  `tunj_pembina_keagamaan` decimal(15,2) DEFAULT 0.00,
  `tunj_pengelola_smater` decimal(15,2) DEFAULT 0.00,
  `tunj_walas` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_gaji_rules`
--

INSERT INTO `keuangan_gaji_rules` (`id`, `id_guru`, `tarif_jjm`, `tarif_transport`, `tarif_kinerja`, `potongan_bpjs_kes`, `updated_at`, `potongan_bpjs_tk`, `potongan_kasbon`, `potongan_lain`, `gaji_pokok`, `tunjangan_jabatan`, `tunjangan_lain`, `tarif_wali_kelas`, `tarif_pembina`, `tarif_ekskul`, `tunj_kepsek`, `tunj_kurikulum`, `tunj_kesiswaan`, `tunj_sarpras`, `tunj_humas`, `tunj_kepala_lab`, `tunj_kepala_perpus`, `tunj_ekskul`, `tunj_pembina`, `tunj_tas`, `tunj_plk`, `tunj_penjaga`, `tunj_satpam`, `tunj_sopir`, `tunj_operator`, `tunj_pembina_keagamaan`, `tunj_pengelola_smater`, `tunj_walas`) VALUES
(1, 1, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(2, 2, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(3, 3, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(4, 4, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 5, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(6, 6, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(7, 7, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:43', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(8, 8, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(9, 9, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(10, 10, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(11, 11, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(12, 12, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(13, 13, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(14, 14, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(15, 15, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(16, 21, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(17, 16, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(18, 20, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(19, 17, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(20, 18, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(21, 19, 0.00, 0.00, 0.00, 0.00, '2026-01-21 13:28:44', 0.00, 0.00, 0.00, 0.00, 200000.00, 0.00, 0.00, 0.00, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(22, 0, 0.00, 0.00, 0.00, 0.00, '2026-01-20 19:54:11', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1000000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_jenis`
--

CREATE TABLE `keuangan_jenis` (
  `id_jenis` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `kode_jenis` varchar(20) NOT NULL,
  `nama_jenis` varchar(100) NOT NULL,
  `kode_akun` varchar(10) DEFAULT NULL,
  `harga_default` decimal(15,2) DEFAULT 0.00,
  `is_recurring` tinyint(1) DEFAULT 0,
  `recurring_period` enum('BULANAN','SEMESTERAN','TAHUNAN') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan_jenis`
--

INSERT INTO `keuangan_jenis` (`id_jenis`, `id_kategori`, `kode_jenis`, `nama_jenis`, `kode_akun`, `harga_default`, `is_recurring`, `recurring_period`, `is_active`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 'ACC-4101', 'Biaya Pendaftaran / Seleksi', '4101', 50000.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-13 03:41:21'),
(2, 1, 'ACC-4102', 'DSP (Dana Sumbangan Pendidikan)', '4102', 500000.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-13 03:41:45'),
(3, 1, 'ACC-4103', 'Sampul Rapot & Kartu Pelajar', '4103', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(4, 1, 'ACC-4104', 'Biaya Masa Pengenalan Sekolah (MPLS)', '4104', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(5, 2, 'ACC-4201', 'Baju Khas (P)', '4201', 175000.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-14 07:49:10'),
(6, 2, 'ACC-4202', 'Seragam Batik', '4203', 75000.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-14 07:51:52'),
(7, 2, 'ACC-4203', 'Kaos Olah Raga', '4204', 120000.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-14 07:51:41'),
(8, 3, 'ACC-4301', 'SPP (Sumbangan Pembinaan Pendidikan)', '4301', 75000.00, 1, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-13 03:40:52'),
(9, 3, 'ACC-4302', 'Iuran Komite Sekolah', '4302', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(10, 4, 'ACC-4401', 'Biaya PTS', '4401', 25000.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-13 03:51:46'),
(11, 4, 'ACC-4402', 'Biaya PSAS', '4402', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(12, 4, 'ACC-4403', 'Biaya PSAT', '4403', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(13, 4, 'ACC-4404', 'Biaya PSAJ', '4404', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(14, 4, 'ACC-4405', 'Biaya AN (Asesmen Nasional)', '4405', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(15, 4, 'ACC-4406', 'Biaya TKA', '4406', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(16, 5, 'ACC-4501', 'Biaya Perpisahan & Wisuda', '4501', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(17, 5, 'ACC-4502', 'Sampul Ijazah & Medali', '4502', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(18, 6, 'ACC-5101', 'Honor Guru & Staf (GTT/PTT)', '5101', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(19, 6, 'ACC-5102', 'Tunjangan & Insentif Jabatan', '5102', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(20, 7, 'ACC-5201', 'Alat Tulis Kantor (ATK) & Fotokopi', '5201', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(21, 7, 'ACC-5202', 'Langganan Daya (Listrik, Air, Internet)', '5202', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(22, 7, 'ACC-5203', 'Pemeliharaan Gedung & Inventaris', '5203', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(23, 8, 'ACC-5301', 'Pembelian Seragam & Almamater (ke Vendor)', '5301', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(24, 8, 'ACC-5302', 'Pengadaan Atribut & Kelengkapan', '5302', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(25, 9, 'ACC-5401', 'Pelaksanaan Ujian (Cetak Soal, Snack)', '5401', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(26, 9, 'ACC-5402', 'Honor Pengawas & Panitia Ujian', '5402', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(27, 10, 'ACC-5501', 'Operasional Ekstrakurikuler', '5501', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(28, 10, 'ACC-5502', 'Pelaksanaan Classmeeting', '5502', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(29, 10, 'ACC-5503', 'Kegiatan OSIS & Lomba Siswa', '5503', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(30, 11, 'ACC-5601', 'Biaya IHT (In-House Training)', '5601', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(31, 11, 'ACC-5602', 'Kontribusi MGMP / Musyawarah Guru', '5602', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(32, 11, 'ACC-5603', 'Biaya Workshop, Pelatihan, & Seminar', '5603', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(33, 12, 'ACC-5701', 'Pelaksanaan Wisuda & Perpisahan', '5701', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(34, 12, 'ACC-5702', 'Pengurusan Ijazah & Dokumentasi', '5702', 0.00, 0, NULL, 1, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(35, 2, 'bya-srgm', 'Baju Khas (L)', '4202', 100000.00, 0, NULL, 1, NULL, '2026-01-11 15:45:43', '2026-01-14 07:52:02'),
(36, 2, 'AUTO', 'Almamater', '4205', 100000.00, 0, NULL, 1, NULL, '2026-01-13 03:51:02', '2026-01-14 07:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_kategori`
--

CREATE TABLE `keuangan_kategori` (
  `id_kategori` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `kode_kategori` varchar(20) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `tipe` enum('MASUK','KELUAR') NOT NULL DEFAULT 'MASUK',
  `kode_akun` varchar(10) DEFAULT NULL,
  `is_student_related` tinyint(1) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan_kategori`
--

INSERT INTO `keuangan_kategori` (`id_kategori`, `id_group`, `kode_kategori`, `nama_kategori`, `tipe`, `kode_akun`, `is_student_related`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 0, 'PEND-AWAL', 'Penerimaan Awal Tahun', 'MASUK', '4100', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(2, 0, 'PEND-ATR', 'Atribut & Seragam', 'MASUK', '4200', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(3, 0, 'PEND-RUTIN', 'Biaya Rutin (Bulanan)', 'MASUK', '4300', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(4, 0, 'PEND-AKAD', 'Kegiatan Akademik', 'MASUK', '4400', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(5, 0, 'PEND-AKHIR', 'Biaya Akhir Tahun', 'MASUK', '4500', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(6, 0, 'BLJ-PEG', 'Belanja Pegawai', 'KELUAR', '5100', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(7, 0, 'BLJ-OPS', 'Operasional & Sarpras', 'KELUAR', '5200', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(8, 0, 'BLJ-BRG', 'Belanja Barang (Atribut)', 'KELUAR', '5300', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(9, 0, 'BLJ-AKAD', 'Kegiatan Akademik', 'KELUAR', '5400', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(10, 0, 'BLJ-SISWA', 'Kegiatan Kesiswaan', 'KELUAR', '5500', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(11, 0, 'BLJ-SDM', 'Pengembangan SDM', 'KELUAR', '5600', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46'),
(12, 0, 'BLJ-AKHIR', 'Kegiatan Akhir Tahun', 'KELUAR', '5700', 0, NULL, '2026-01-11 15:34:46', '2026-01-11 15:34:46');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_master_jabatan`
--

CREATE TABLE `keuangan_master_jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `kategori` enum('GURU','STAFF') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_master_jabatan`
--

INSERT INTO `keuangan_master_jabatan` (`id_jabatan`, `nama_jabatan`, `kategori`) VALUES
(1, 'Waka Kurikulum', 'GURU'),
(2, 'Waka Kesiswaan', 'GURU'),
(3, 'Waka Humas', 'GURU'),
(4, 'Waka Sarpras', 'GURU'),
(5, 'Kepala Laboratorium', 'GURU'),
(6, 'Kepala Perpustakaan', 'GURU'),
(7, 'Bendahara BOS', 'GURU'),
(8, 'Guru Piket', 'GURU'),
(9, 'Kepala Sekolah', 'STAFF'),
(10, 'Tenaga Administrasi', 'STAFF'),
(11, 'Petugas Layanan Khusus', 'STAFF'),
(12, 'Penjaga Sekolah', 'STAFF'),
(13, 'Satpam', 'STAFF'),
(14, 'Sopir', 'STAFF'),
(15, 'Operator', 'STAFF'),
(16, 'Pembina Keagamaan', 'STAFF'),
(17, 'Pengelola Smater', 'STAFF');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_memorial`
--

CREATE TABLE `keuangan_memorial` (
  `id_memorial` int(11) NOT NULL,
  `no_bukti` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_memorial_detail`
--

CREATE TABLE `keuangan_memorial_detail` (
  `id_detail` int(11) NOT NULL,
  `id_memorial` int(11) NOT NULL,
  `kode_akun` varchar(20) NOT NULL,
  `nama_akun` varchar(100) DEFAULT NULL,
  `tipe` enum('DEBIT','KREDIT') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_pembayaran_detail`
--

CREATE TABLE `keuangan_pembayaran_detail` (
  `id_pembayaran_detail` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `cicilan_ke` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_rekening`
--

CREATE TABLE `keuangan_rekening` (
  `id_rekening` int(11) NOT NULL,
  `kode_rekening` varchar(20) NOT NULL,
  `nama_rekening` varchar(100) NOT NULL,
  `tipe` enum('KAS','BANK') NOT NULL,
  `nama_bank` varchar(100) DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `atas_nama` varchar(100) DEFAULT NULL,
  `saldo_awal` decimal(15,2) DEFAULT 0.00,
  `saldo_akhir` decimal(15,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan_rekening`
--

INSERT INTO `keuangan_rekening` (`id_rekening`, `kode_rekening`, `nama_rekening`, `tipe`, `nama_bank`, `nomor_rekening`, `atas_nama`, `saldo_awal`, `saldo_akhir`, `is_active`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'KAS-001', 'Kas Tunai', 'KAS', NULL, NULL, NULL, 0.00, 150000.00, 1, 'Kas tunai bendahara sekolah', '2026-01-07 04:57:47', '2026-01-19 03:30:25'),
(2, 'BANK-001', 'Bank Sekolah (Operasional)', 'BANK', 'BRI', '0000-0000-0000-0000', 'NAMA SEKOLAH', 0.00, 0.00, 1, 'Rekening operasional - GANTI NOMOR!', '2026-01-07 04:57:47', '2026-01-07 04:57:47'),
(3, 'BANK-002', 'Bank Sekolah (Dana BOS)', 'BANK', 'BNI', '0000-0000-0000', 'NAMA SEKOLAH', 0.00, 0.00, 1, 'Rekening BOS - GANTI NOMOR!', '2026-01-07 04:57:47', '2026-01-07 04:57:47');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_tagihan_siswa`
--

CREATE TABLE `keuangan_tagihan_siswa` (
  `id_tagihan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `tahun_ajaran` varchar(10) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `jumlah_tagihan` decimal(15,2) NOT NULL,
  `jumlah_terbayar` decimal(15,2) DEFAULT 0.00,
  `sisa_tagihan` decimal(15,2) NOT NULL,
  `is_custom` tinyint(1) DEFAULT 0,
  `status` enum('BELUM_BAYAR','CICIL','LUNAS') DEFAULT 'BELUM_BAYAR',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan_tagihan_siswa`
--

INSERT INTO `keuangan_tagihan_siswa` (`id_tagihan`, `id_siswa`, `id_jenis`, `tahun_ajaran`, `periode`, `tanggal_jatuh_tempo`, `jumlah_tagihan`, `jumlah_terbayar`, `sisa_tagihan`, `is_custom`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 48, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 0.00, 0, 'LUNAS', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 14:08:57'),
(2, 48, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(3, 48, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(4, 48, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(5, 48, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(6, 48, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(7, 49, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 0.00, 0, 'LUNAS', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-19 03:30:25'),
(8, 49, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(9, 49, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(10, 49, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(11, 49, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(12, 49, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(13, 50, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(14, 50, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(15, 50, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(16, 50, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(17, 50, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(18, 50, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(19, 51, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(20, 51, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(21, 51, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(22, 51, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(23, 51, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(24, 51, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(25, 52, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(26, 52, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(27, 52, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(28, 52, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(29, 52, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(30, 52, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(31, 54, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(32, 54, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(33, 54, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(34, 54, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(35, 54, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(36, 54, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(37, 56, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(38, 56, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(39, 56, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(40, 56, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(41, 56, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(42, 56, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(43, 57, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(44, 57, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(45, 57, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(46, 57, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(47, 57, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(48, 57, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(49, 58, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(50, 58, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(51, 58, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(52, 58, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(53, 58, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(54, 58, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(55, 59, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(56, 59, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(57, 59, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(58, 59, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(59, 59, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(60, 59, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(61, 60, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(62, 60, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(63, 60, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(64, 60, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(65, 60, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(66, 60, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(67, 61, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(68, 61, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(69, 61, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(70, 61, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(71, 61, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(72, 61, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(73, 62, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(74, 62, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(75, 62, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(76, 62, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(77, 62, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(78, 62, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(79, 63, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(80, 63, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(81, 63, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(82, 63, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(83, 63, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(84, 63, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(85, 64, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(86, 64, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(87, 64, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(88, 64, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(89, 64, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(90, 64, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(91, 65, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(92, 65, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(93, 65, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(94, 65, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(95, 65, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(96, 65, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(97, 66, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(98, 66, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(99, 66, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(100, 66, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(101, 66, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(102, 66, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(103, 67, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(104, 67, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(105, 67, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(106, 67, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(107, 67, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(108, 67, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(109, 68, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(110, 68, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(111, 68, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(112, 68, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(113, 68, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(114, 68, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(115, 69, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(116, 69, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(117, 69, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(118, 69, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(119, 69, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(120, 69, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(121, 70, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(122, 70, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(123, 70, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(124, 70, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(125, 70, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(126, 70, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(127, 71, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(128, 71, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(129, 71, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(130, 71, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(131, 71, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(132, 71, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(133, 72, 8, '5', '2026-01', '2026-01-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(134, 72, 8, '5', '2026-02', '2026-02-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(135, 72, 8, '5', '2026-03', '2026-03-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(136, 72, 8, '5', '2026-04', '2026-04-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(137, 72, 8, '5', '2026-05', '2026-05-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03'),
(138, 72, 8, '5', '2026-06', '2026-06-10', 75000.00, 0.00, 75000.00, 0, 'BELUM_BAYAR', 'Auto-Gen Ledger (Rutin)', '2026-01-18 13:36:03', '2026-01-18 13:36:03');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_tarif`
--

CREATE TABLE `keuangan_tarif` (
  `id_tarif` int(11) NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_tarif`
--

INSERT INTO `keuangan_tarif` (`id_tarif`, `id_jenis`, `id_kelas`, `id_siswa`, `nominal`, `keterangan`, `created_at`, `updated_at`) VALUES
(24, 2, NULL, 48, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(25, 8, NULL, 48, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(26, 2, NULL, 49, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(27, 8, NULL, 49, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(28, 2, NULL, 50, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(29, 8, NULL, 50, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(30, 2, NULL, 51, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(31, 8, NULL, 51, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(32, 2, NULL, 52, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(33, 8, NULL, 52, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(34, 2, NULL, 54, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(35, 8, NULL, 54, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(36, 2, NULL, 56, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(37, 8, NULL, 56, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(38, 2, NULL, 57, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(39, 8, NULL, 57, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(40, 2, NULL, 58, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(41, 8, NULL, 58, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(42, 2, NULL, 59, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(43, 8, NULL, 59, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(44, 2, NULL, 60, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(45, 8, NULL, 60, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(46, 2, NULL, 61, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(47, 8, NULL, 61, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:21', '2026-01-18 12:03:21'),
(48, 2, NULL, 62, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:21\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(49, 8, NULL, 62, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(50, 2, NULL, 63, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(51, 8, NULL, 63, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(52, 2, NULL, 64, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(53, 8, NULL, 64, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(54, 2, NULL, 65, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(55, 8, NULL, 65, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(56, 2, NULL, 66, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(57, 8, NULL, 66, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(58, 2, NULL, 67, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(59, 8, NULL, 67, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(60, 2, NULL, 68, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(61, 8, NULL, 68, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(62, 2, NULL, 69, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(63, 8, NULL, 69, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(64, 2, NULL, 70, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(65, 8, NULL, 70, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(66, 2, NULL, 71, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(67, 8, NULL, 71, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(68, 2, NULL, 72, 500000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22'),
(69, 8, NULL, 72, 75000.00, '{\"months\":[1,2,3,4,5,6,7,8,9,10,11,12],\"updated_at\":\"2026-01-18 19:03:22\"}', '2026-01-18 12:03:22', '2026-01-18 12:03:22');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_tarif_ekskul`
--

CREATE TABLE `keuangan_tarif_ekskul` (
  `id_tarif_ekskul` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `nominal` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_tarif_ekskul`
--

INSERT INTO `keuangan_tarif_ekskul` (`id_tarif_ekskul`, `id_kegiatan`, `nominal`) VALUES
(1, 2, 40000.00),
(2, 1, 40000.00),
(13, 3, 40000.00),
(16, 4, 40000.00),
(17, 6, 40000.00),
(18, 5, 40000.00);

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_tarif_general`
--

CREATE TABLE `keuangan_tarif_general` (
  `id` int(11) NOT NULL DEFAULT 1,
  `tarif_jjm` decimal(15,2) DEFAULT 0.00,
  `tarif_transport` decimal(15,2) DEFAULT 0.00,
  `tarif_kinerja` decimal(15,2) DEFAULT 0.00,
  `tunj_kepsek` decimal(15,2) DEFAULT 0.00,
  `tunj_tas` decimal(15,2) DEFAULT 0.00,
  `tunj_pegawai_lain` decimal(15,2) DEFAULT 0.00,
  `tunj_waka_kurikulum` decimal(15,2) DEFAULT 0.00,
  `tunj_waka_kesiswaan` decimal(15,2) DEFAULT 0.00,
  `tunj_waka_humas` decimal(15,2) DEFAULT 0.00,
  `tunj_kepala_lab` decimal(15,2) DEFAULT 0.00,
  `tunj_kepala_perpus` decimal(15,2) DEFAULT 0.00,
  `tunj_pembina` decimal(15,2) DEFAULT 0.00,
  `tarif_ekskul_global` decimal(15,2) DEFAULT 0.00,
  `tunj_plk` decimal(15,2) DEFAULT 0.00,
  `tunj_penjaga` decimal(15,2) DEFAULT 0.00,
  `tunj_satpam` decimal(15,2) DEFAULT 0.00,
  `tunj_sopir` decimal(15,2) DEFAULT 0.00,
  `tunj_kurikulum` decimal(15,2) DEFAULT 0.00,
  `tunj_kesiswaan` decimal(15,2) DEFAULT 0.00,
  `tunj_sarpras` decimal(15,2) DEFAULT 0.00,
  `tunj_humas` decimal(15,2) DEFAULT 0.00,
  `tunj_operator` decimal(15,2) DEFAULT 0.00,
  `tunj_pembina_keagamaan` decimal(15,2) DEFAULT 0.00,
  `tunj_pengelola_smater` decimal(15,2) DEFAULT 0.00,
  `tunj_ekskul` decimal(15,2) DEFAULT 0.00,
  `tunj_walas` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan_tarif_general`
--

INSERT INTO `keuangan_tarif_general` (`id`, `tarif_jjm`, `tarif_transport`, `tarif_kinerja`, `tunj_kepsek`, `tunj_tas`, `tunj_pegawai_lain`, `tunj_waka_kurikulum`, `tunj_waka_kesiswaan`, `tunj_waka_humas`, `tunj_kepala_lab`, `tunj_kepala_perpus`, `tunj_pembina`, `tarif_ekskul_global`, `tunj_plk`, `tunj_penjaga`, `tunj_satpam`, `tunj_sopir`, `tunj_kurikulum`, `tunj_kesiswaan`, `tunj_sarpras`, `tunj_humas`, `tunj_operator`, `tunj_pembina_keagamaan`, `tunj_pengelola_smater`, `tunj_ekskul`, `tunj_walas`) VALUES
(1, 25000.00, 25000.00, 5000.00, 1500000.00, 1000000.00, 0.00, 300000.00, 300000.00, 200000.00, 200000.00, 200000.00, 0.00, 0.00, 500000.00, 500000.00, 500000.00, 500000.00, 0.00, 0.00, 0.00, 0.00, 300000.00, 250000.00, 250000.00, 0.00, 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `keuangan_transaksi`
--

CREATE TABLE `keuangan_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `no_bukti` varchar(30) NOT NULL,
  `tanggal` date NOT NULL,
  `tipe` enum('MASUK','KELUAR') NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `id_rekening` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_tagihan` int(11) DEFAULT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `metode_pembayaran` enum('TUNAI','TRANSFER','QRIS','VA') DEFAULT 'TUNAI',
  `referensi` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL,
  `id_pengguna` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keuangan_transaksi`
--

INSERT INTO `keuangan_transaksi` (`id_transaksi`, `no_bukti`, `tanggal`, `tipe`, `id_jenis`, `id_rekening`, `id_siswa`, `id_tagihan`, `jumlah`, `metode_pembayaran`, `referensi`, `keterangan`, `bukti_file`, `id_pengguna`, `created_at`, `updated_at`) VALUES
(1, 'BM.260118.4301.001', '2026-01-18', 'MASUK', 8, 1, 48, 1, 75000.00, 'TUNAI', NULL, 'Periode 2026-01', NULL, 1, '2026-01-18 14:08:57', '2026-01-18 14:08:57'),
(2, 'BM.260119.4301.001', '2026-01-19', 'MASUK', 8, 1, 49, 7, 75000.00, 'TUNAI', NULL, 'Periode 2026-01', NULL, 1, '2026-01-19 03:30:25', '2026-01-19 03:30:25');

-- --------------------------------------------------------

--
-- Table structure for table `kewirausahaan`
--

CREATE TABLE `kewirausahaan` (
  `id_kewirausahaan` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `kelompok` varchar(100) DEFAULT NULL,
  `id_guru_pembina` int(11) DEFAULT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam` varchar(20) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `file_proker` varchar(255) DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kewirausahaan_agenda`
--

CREATE TABLE `kewirausahaan_agenda` (
  `id_agenda` int(11) NOT NULL,
  `id_kewirausahaan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` varchar(50) DEFAULT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tipe` enum('program','agenda') DEFAULT 'agenda',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kewirausahaan_galeri`
--

CREATE TABLE `kewirausahaan_galeri` (
  `id_galeri` int(11) NOT NULL,
  `id_kewirausahaan` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kewirausahaan_keuangan`
--

CREATE TABLE `kewirausahaan_keuangan` (
  `id_transaksi` int(11) NOT NULL,
  `id_kewirausahaan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('Modal','Pemasukan','Pengeluaran') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kewirausahaan_produk`
--

CREATE TABLE `kewirausahaan_produk` (
  `id_produk` int(11) NOT NULL,
  `id_kewirausahaan` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_jual` decimal(15,2) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `foto_produk` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kewirausahaan_tahapan`
--

CREATE TABLE `kewirausahaan_tahapan` (
  `id_tahapan` int(11) NOT NULL,
  `id_kewirausahaan` int(11) NOT NULL,
  `nama_tahapan` varchar(255) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('Belum Mulai','Proses','Selesai') DEFAULT 'Belum Mulai',
  `keterangan` text DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kewirausahaan_tahapan`
--

INSERT INTO `kewirausahaan_tahapan` (`id_tahapan`, `id_kewirausahaan`, `nama_tahapan`, `tanggal_mulai`, `tanggal_selesai`, `status`, `keterangan`, `urutan`, `created_at`) VALUES
(1, 1, 'Training Big Dream', '2026-01-06', '2026-01-06', 'Selesai', 'fdfdf', 1, '2026-01-04 11:33:55'),
(2, 1, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 11:33:55'),
(3, 1, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 11:33:55'),
(4, 1, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 11:33:55'),
(5, 1, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 11:33:55'),
(6, 1, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 11:33:55'),
(7, 2, 'Training Big Dream', NULL, NULL, 'Belum Mulai', NULL, 1, '2026-01-04 14:17:53'),
(8, 2, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 14:17:53'),
(9, 2, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 14:17:53'),
(10, 2, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 14:17:53'),
(11, 2, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 14:17:53'),
(12, 2, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 14:17:53'),
(13, 3, 'Training Big Dream', NULL, NULL, 'Belum Mulai', NULL, 1, '2026-01-04 14:33:39'),
(14, 3, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 14:33:39'),
(15, 3, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 14:33:39'),
(16, 3, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 14:33:40'),
(17, 3, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 14:33:40'),
(18, 3, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 14:33:40'),
(19, 4, 'Training Big Dream', NULL, NULL, 'Belum Mulai', NULL, 1, '2026-01-04 14:38:25'),
(20, 4, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 14:38:25'),
(21, 4, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 14:38:25'),
(22, 4, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 14:38:25'),
(23, 4, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 14:38:25'),
(24, 4, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 14:38:25'),
(25, 5, 'Training Big Dream', NULL, NULL, 'Belum Mulai', NULL, 1, '2026-01-04 14:40:23'),
(26, 5, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 14:40:24'),
(27, 5, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 14:40:24'),
(28, 5, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 14:40:24'),
(29, 5, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 14:40:24'),
(30, 5, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 14:40:24'),
(31, 6, 'Training Big Dream', NULL, NULL, 'Belum Mulai', NULL, 1, '2026-01-04 14:45:58'),
(32, 6, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 14:45:58'),
(33, 6, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 14:45:58'),
(34, 6, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 14:45:58'),
(35, 6, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 14:45:58'),
(36, 6, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 14:45:58'),
(37, 7, 'Training Big Dream', NULL, NULL, 'Belum Mulai', NULL, 1, '2026-01-04 15:02:13'),
(38, 7, 'Sharing Bisnis', NULL, NULL, 'Belum Mulai', NULL, 2, '2026-01-04 15:02:13'),
(39, 7, 'Partner Bisnis', NULL, NULL, 'Belum Mulai', NULL, 3, '2026-01-04 15:02:13'),
(40, 7, 'Mentoring', NULL, NULL, 'Belum Mulai', NULL, 4, '2026-01-04 15:02:13'),
(41, 7, 'Market Day', NULL, NULL, 'Belum Mulai', NULL, 5, '2026-01-04 15:02:13'),
(42, 7, 'Magang', NULL, NULL, 'Belum Mulai', NULL, 6, '2026-01-04 15:02:13');

-- --------------------------------------------------------

--
-- Table structure for table `kokulikuler`
--

CREATE TABLE `kokulikuler` (
  `id_kokulikuler` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `tema` varchar(255) DEFAULT NULL,
  `id_guru_pembina` int(11) DEFAULT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kokulikuler`
--

INSERT INTO `kokulikuler` (`id_kokulikuler`, `nama_kegiatan`, `tema`, `id_guru_pembina`, `hari`, `jam_mulai`, `jam_selesai`, `status`) VALUES
(2, 'Kokurikuler', 'Beriman dan bertakwa', 19, 'Sabtu', '00:00:00', '00:00:00', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `kokulikuler_galeri`
--

CREATE TABLE `kokulikuler_galeri` (
  `id_galeri` int(11) NOT NULL,
  `id_kokulikuler` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kokulikuler_nilai`
--

CREATE TABLE `kokulikuler_nilai` (
  `id_nilai` int(11) NOT NULL,
  `id_kokulikuler` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `nilai` varchar(10) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kokulikuler_profil`
--

CREATE TABLE `kokulikuler_profil` (
  `id_kokulikuler` int(11) NOT NULL,
  `id_profil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_gallery`
--

CREATE TABLE `landing_gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `category` enum('Kegiatan','Fasilitas','Prestasi','Ekstrakurikuler','Lainnya') DEFAULT 'Kegiatan',
  `is_slider` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landing_gallery`
--

INSERT INTO `landing_gallery` (`id`, `title`, `description`, `image_path`, `category`, `is_slider`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Upacara Bendera', 'Upacara bendera hari Senin', 'uploads/gallery/sample1.jpg', 'Kegiatan', 1, 1, 1, '2025-12-11 02:57:41', '2026-01-10 12:22:52'),
(2, 'Ruang Kelas Modern', 'Fasilitas ruang kelas yang nyaman', 'uploads/gallery/sample2.jpg', 'Fasilitas', 1, 2, 1, '2025-12-11 02:57:41', '2025-12-11 02:57:41'),
(3, 'Prestasi Olimpiade', 'Juara 1 Olimpiade Matematika', 'uploads/gallery/sample3.jpg', 'Prestasi', 1, 3, 1, '2025-12-11 02:57:41', '2025-12-11 02:57:41'),
(4, 'Upacara Bendera', 'Upacara bendera hari Senin', 'assets/img/hero-1.webp', 'Kegiatan', 1, 1, 1, '2025-12-12 08:35:27', '2026-01-10 12:22:36'),
(5, 'Ruang Kelas Modern', 'Fasilitas ruang kelas yang nyaman', 'assets/img/hero-2.webp', 'Fasilitas', 1, 2, 1, '2025-12-12 08:35:27', '2025-12-12 08:35:27'),
(6, 'Prestasi Olimpiade', 'Juara 1 Olimpiade Matematika', 'assets/img/hero-3.webp', 'Prestasi', 1, 3, 1, '2025-12-12 08:35:27', '2025-12-12 08:35:27'),
(7, 'Upacara Bendera', 'Upacara bendera hari Senin', 'uploads/gallery/696cf23da10bc.png', 'Kegiatan', 1, 1, 1, '2025-12-12 08:36:10', '2026-01-18 14:46:21'),
(8, 'Ruang Kelas Modern', 'Fasilitas ruang kelas yang nyaman', 'assets/img/hero-2.webp', 'Fasilitas', 1, 2, 1, '2025-12-12 08:36:10', '2025-12-12 08:36:10'),
(9, 'Kegiatan Kewirausahaan', 'Marketing Day merupakan salah satu kegiatan kewirausahaan di SMA Plus Almanshuriyah', 'assets/img/hero-3.webp', 'Kegiatan', 1, 3, 1, '2025-12-12 08:36:10', '2026-01-10 12:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `landing_news`
--

CREATE TABLE `landing_news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `excerpt` varchar(300) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `type` enum('berita','pengumuman','event') DEFAULT 'berita',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `publish_date` date DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landing_news`
--

INSERT INTO `landing_news` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `type`, `is_featured`, `is_published`, `publish_date`, `author_id`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Penerimaan Peserta Didik Baru 2025/2026', 'ppdb-2025-2026', 'Pendaftaran PPDB tahun ajaran 2025/2026 telah dibuka. Daftar sekarang melalui website kami.', 'Pendaftaran PPDB 2025/2026 telah dibuka', NULL, 'pengumuman', 1, 1, '2025-12-12', NULL, 0, '2025-12-12 08:36:10', '2025-12-12 08:36:10'),
(2, 'Prestasi Gemilang di Lomba Nasional', 'prestasi-gemilang-di-lomba-nasional', 'Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains. Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains. Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains.', 'Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains. Siswa kami berhasil meraih juara 1 pada lomba nasional bidang Sains. Siswa kami b', NULL, 'berita', 1, 1, '2025-12-12', NULL, 0, '2025-12-12 08:36:10', '2026-01-10 12:29:40');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `attempt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mapel`
--

CREATE TABLE `mapel` (
  `id_mapel` int(11) NOT NULL,
  `nama_mapel` varchar(100) DEFAULT NULL,
  `kode_mapel` varchar(5) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `kategori_mapel` enum('Mata Pelajaran Wajib','Mata Pelajaran Pilihan','Muatan Lokal','Mulok Yayasan') DEFAULT NULL,
  `kktp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mapel`
--

INSERT INTO `mapel` (`id_mapel`, `nama_mapel`, `kode_mapel`, `urutan`, `kategori_mapel`, `kktp`) VALUES
(1, 'Pendidikan Agama Islam dan Budi Pekerti', 'A', 1, 'Mata Pelajaran Wajib', 70),
(2, 'Pendidikan Pancasila', 'B', 2, 'Mata Pelajaran Wajib', 70),
(3, 'Bahasa Indonesia', 'C', 3, 'Mata Pelajaran Wajib', 70),
(4, 'Matematika', 'D', 4, 'Mata Pelajaran Wajib', 70);

-- --------------------------------------------------------

--
-- Table structure for table `master_kegiatan`
--

CREATE TABLE `master_kegiatan` (
  `id_kegiatan` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `jenis_kegiatan` enum('KBM','Istirahat','Pembiasaan','Lainnya','Ekstrakurikuler','Kokulikuler','Kewirausahaan','Tahfidz') DEFAULT 'Lainnya',
  `kategori` enum('Akademik','Non-Akademik') DEFAULT 'Akademik',
  `durasi_menit` int(11) NOT NULL,
  `hari_pelaksanaan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_kegiatan`
--

INSERT INTO `master_kegiatan` (`id_kegiatan`, `nama_kegiatan`, `jenis_kegiatan`, `kategori`, `durasi_menit`, `hari_pelaksanaan`) VALUES
(1, 'Futsal', 'Ekstrakurikuler', 'Non-Akademik', 60, 'Sabtu'),
(2, 'Englis Club', 'Ekstrakurikuler', 'Non-Akademik', 60, 'Sabtu'),
(3, 'Bola Voli', 'Ekstrakurikuler', 'Non-Akademik', 60, 'Sabtu'),
(4, 'Multimedia', 'Ekstrakurikuler', 'Non-Akademik', 60, 'Sabtu'),
(5, 'TIK', 'Ekstrakurikuler', 'Non-Akademik', 60, 'Sabtu'),
(6, 'Pramuka', 'Ekstrakurikuler', 'Non-Akademik', 60, 'Sabtu'),
(14, 'Kegiatan Belajar Mengajar', 'KBM', 'Akademik', 35, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'),
(15, 'Tadarus dan Solat Duha', 'Pembiasaan', 'Akademik', 60, 'Selasa,Rabu,Kamis,Jumat,Sabtu'),
(16, 'Istirahat', 'Istirahat', 'Akademik', 20, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'),
(17, 'Solat Dzuhur dan Kajian Kitab', 'Pembiasaan', 'Akademik', 60, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'),
(18, 'Tadarus dan Solat Duha*', 'Pembiasaan', 'Akademik', 30, 'Senin'),
(19, 'Upacara Bendera', 'Pembiasaan', 'Akademik', 30, 'Senin');

-- --------------------------------------------------------

--
-- Table structure for table `master_template_dokumen`
--

CREATE TABLE `master_template_dokumen` (
  `id_template` int(11) NOT NULL,
  `jenis` enum('ATP','Modul Ajar','Prosem','Prota') NOT NULL,
  `nama_template` varchar(255) NOT NULL,
  `konten_html` longtext DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_template_dokumen`
--

INSERT INTO `master_template_dokumen` (`id_template`, `jenis`, `nama_template`, `konten_html`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Modul Ajar', 'Template Modul Ajar Kurikulum Merdeka', '<h2>MODUL AJAR</h2><table border=\"1\" width=\"100%\"><tr><td>Mata Pelajaran:</td><td>[ISI]</td></tr><tr><td>Kelas:</td><td>[ISI]</td></tr><tr><td>Alokasi Waktu:</td><td>[ISI]</td></tr></table><h3>A. Tujuan Pembelajaran</h3><p>[ISI TUJUAN]</p><h3>B. Kegiatan Pembelajaran</h3><p>[ISI KEGIATAN]</p>', 1, '2026-01-05 14:26:47', '2026-01-05 14:26:47'),
(2, 'ATP', 'Template ATP Standar', '<h2>ALUR TUJUAN PEMBELAJARAN (ATP)</h2><table border=\"1\" width=\"100%\"><tr><th>No</th><th>Capaian Pembelajaran</th><th>Tujuan Pembelajaran</th><th>Materi Pokok</th></tr><tr><td>1</td><td>[ISI]</td><td>[ISI]</td><td>[ISI]</td></tr></table>', 1, '2026-01-05 14:26:47', '2026-01-05 14:26:47');

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_masuk`
--

CREATE TABLE `mutasi_masuk` (
  `id_mutasi` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `jk` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `sekolah_asal` varchar(100) DEFAULT NULL,
  `tingkat_sebelumnya` varchar(10) DEFAULT NULL,
  `pindah_ke_tingkat` varchar(10) NOT NULL,
  `id_kelas_tujuan` int(11) DEFAULT NULL,
  `tanggal_mutasi` date DEFAULT NULL,
  `alasan_mutasi` text DEFAULT NULL,
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_penerimaan` enum('Pending','Diterima','Ditolak') DEFAULT 'Pending',
  `id_siswa_master` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_siswa`
--

CREATE TABLE `mutasi_siswa` (
  `id_mutasi` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `tanggal_mutasi` date NOT NULL,
  `jenis_mutasi` enum('Pindah','Berhenti','Lainnya') NOT NULL,
  `alasan` text DEFAULT NULL,
  `id_pengguna_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id_nilai` int(11) NOT NULL,
  `id_penempatan` int(11) NOT NULL,
  `id_guru_mapel` int(11) NOT NULL,
  `id_tp` int(11) NOT NULL,
  `jenis_penilaian` enum('Formatif','Sumatif Lingkup Materi','Sumatif Tengah Semester','Sumatif Akhir Semester','Sumatif Akhir Tahun','Sumatif Akhir Jenjang') DEFAULT 'Formatif',
  `nilai` decimal(5,2) DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_ekskul`
--

CREATE TABLE `nilai_ekskul` (
  `id_nilai` int(11) NOT NULL,
  `id_ekskul` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nilai` varchar(10) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_sumatif`
--

CREATE TABLE `nilai_sumatif` (
  `id_nilai_sumatif` int(11) NOT NULL,
  `id_sumatif` int(11) NOT NULL,
  `id_penempatan` int(11) NOT NULL COMMENT 'FK ke penempatan_siswa',
  `nilai` decimal(5,2) DEFAULT NULL,
  `deskripsi_capaian` text DEFAULT NULL COMMENT 'Deskripsi naratif capaian siswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_sumatif_tp`
--

CREATE TABLE `nilai_sumatif_tp` (
  `id_nilai_sumatif` int(11) NOT NULL,
  `id_tp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notif` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `status` enum('Terkirim','Gagal','Belum') DEFAULT 'Belum',
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembiasaan`
--

CREATE TABLE `pembiasaan` (
  `id_pembiasaan` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) NOT NULL,
  `id_guru_pembina` int(11) DEFAULT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penempatan_siswa`
--

CREATE TABLE `penempatan_siswa` (
  `id_penempatan` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `id_penugasan_wali_kelas` int(11) DEFAULT NULL,
  `status_penempatan` enum('Aktif','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penempatan_siswa`
--

INSERT INTO `penempatan_siswa` (`id_penempatan`, `id_siswa`, `id_kelas`, `id_ta`, `id_penugasan_wali_kelas`, `status_penempatan`) VALUES
(1, 1, 1, 1, NULL, 'Aktif'),
(2, 2, 1, 1, NULL, 'Aktif'),
(3, 3, 1, 1, NULL, 'Aktif'),
(4, 4, 1, 1, NULL, 'Aktif'),
(5, 5, 1, 1, NULL, 'Aktif'),
(6, 6, 1, 1, NULL, 'Aktif'),
(7, 7, 1, 1, NULL, 'Aktif'),
(8, 8, 1, 1, NULL, 'Aktif'),
(9, 9, 1, 1, NULL, 'Aktif'),
(10, 10, 1, 1, NULL, 'Aktif'),
(11, 11, 1, 1, NULL, 'Aktif'),
(12, 12, 1, 1, NULL, 'Aktif'),
(13, 13, 1, 1, NULL, 'Aktif'),
(14, 14, 1, 1, NULL, 'Aktif'),
(15, 15, 1, 1, NULL, 'Aktif'),
(16, 16, 1, 1, NULL, 'Aktif'),
(17, 17, 1, 1, NULL, 'Aktif'),
(18, 18, 1, 1, NULL, 'Aktif'),
(19, 19, 1, 1, NULL, 'Aktif'),
(20, 20, 1, 1, NULL, 'Aktif'),
(21, 21, 1, 1, NULL, 'Aktif'),
(22, 22, 1, 1, NULL, 'Aktif'),
(23, 23, 2, 1, NULL, 'Aktif'),
(24, 24, 2, 1, NULL, 'Aktif'),
(25, 25, 2, 1, NULL, 'Aktif'),
(26, 26, 2, 1, NULL, 'Aktif'),
(27, 27, 2, 1, NULL, 'Aktif'),
(28, 28, 2, 1, NULL, 'Aktif'),
(29, 29, 2, 1, NULL, 'Aktif'),
(30, 30, 2, 1, NULL, 'Aktif'),
(31, 31, 2, 1, NULL, 'Aktif'),
(32, 32, 2, 1, NULL, 'Aktif'),
(33, 33, 2, 1, NULL, 'Aktif'),
(34, 34, 2, 1, NULL, 'Aktif'),
(35, 35, 2, 1, NULL, 'Aktif'),
(36, 36, 2, 1, NULL, 'Aktif'),
(37, 37, 2, 1, NULL, 'Aktif'),
(38, 39, 2, 1, NULL, 'Aktif'),
(39, 41, 2, 1, NULL, 'Aktif'),
(40, 42, 2, 1, NULL, 'Aktif'),
(41, 43, 2, 1, NULL, 'Aktif'),
(42, 38, 2, 1, NULL, 'Aktif'),
(43, 40, 2, 1, NULL, 'Aktif'),
(44, 44, 2, 1, NULL, 'Aktif'),
(45, 46, 2, 1, NULL, 'Aktif'),
(46, 45, 2, 1, NULL, 'Aktif'),
(47, 47, 2, 1, NULL, 'Aktif'),
(48, 1, 7, 2, NULL, 'Aktif'),
(49, 3, 7, 2, NULL, 'Aktif'),
(50, 4, 7, 2, NULL, 'Aktif'),
(51, 5, 7, 2, NULL, 'Aktif'),
(52, 6, 7, 2, NULL, 'Aktif'),
(53, 7, 7, 2, NULL, 'Aktif'),
(54, 8, 7, 2, NULL, 'Aktif'),
(55, 9, 7, 2, NULL, 'Aktif'),
(56, 10, 7, 2, NULL, 'Aktif'),
(57, 11, 7, 2, NULL, 'Aktif'),
(58, 12, 7, 2, NULL, 'Aktif'),
(59, 13, 7, 2, NULL, 'Aktif'),
(60, 14, 7, 2, NULL, 'Aktif'),
(61, 15, 7, 2, NULL, 'Aktif'),
(62, 16, 7, 2, NULL, 'Aktif'),
(63, 17, 7, 2, NULL, 'Aktif'),
(64, 18, 7, 2, NULL, 'Aktif'),
(65, 19, 7, 2, NULL, 'Aktif'),
(66, 20, 7, 2, NULL, 'Aktif'),
(67, 21, 7, 2, NULL, 'Aktif'),
(68, 22, 7, 2, NULL, 'Aktif'),
(69, 23, 8, 2, NULL, 'Aktif'),
(70, 24, 8, 2, NULL, 'Aktif'),
(72, 25, 8, 2, NULL, 'Aktif'),
(73, 26, 8, 2, NULL, 'Aktif'),
(74, 27, 8, 2, NULL, 'Aktif'),
(75, 28, 8, 2, NULL, 'Aktif'),
(76, 29, 8, 2, NULL, 'Aktif'),
(77, 30, 8, 2, NULL, 'Aktif'),
(78, 31, 8, 2, NULL, 'Aktif'),
(79, 32, 8, 2, NULL, 'Aktif'),
(80, 33, 8, 2, NULL, 'Aktif'),
(81, 34, 8, 2, NULL, 'Aktif'),
(82, 36, 8, 2, NULL, 'Aktif'),
(83, 37, 8, 2, NULL, 'Aktif'),
(84, 38, 8, 2, NULL, 'Aktif'),
(85, 40, 8, 2, NULL, 'Aktif'),
(86, 41, 8, 2, NULL, 'Aktif'),
(87, 42, 8, 2, NULL, 'Aktif'),
(88, 43, 8, 2, NULL, 'Aktif'),
(89, 44, 8, 2, NULL, 'Aktif'),
(90, 45, 8, 2, NULL, 'Aktif'),
(91, 47, 8, 2, NULL, 'Aktif'),
(92, 35, 8, 2, NULL, 'Aktif'),
(93, 39, 8, 2, NULL, 'Aktif'),
(94, 46, 8, 2, NULL, 'Aktif'),
(95, 2, 7, 2, NULL, 'Aktif'),
(96, 1, 5, 3, NULL, 'Aktif'),
(97, 2, 5, 3, NULL, 'Aktif'),
(98, 3, 5, 3, NULL, 'Aktif'),
(99, 4, 5, 3, NULL, 'Aktif'),
(100, 5, 5, 3, NULL, 'Aktif'),
(101, 6, 5, 3, NULL, 'Aktif'),
(102, 7, 5, 3, NULL, 'Aktif'),
(103, 8, 5, 3, NULL, 'Aktif'),
(104, 9, 5, 3, NULL, 'Aktif'),
(105, 10, 5, 3, NULL, 'Aktif'),
(106, 11, 5, 3, NULL, 'Aktif'),
(107, 12, 5, 3, NULL, 'Aktif'),
(108, 13, 5, 3, NULL, 'Aktif'),
(109, 14, 5, 3, NULL, 'Aktif'),
(110, 15, 5, 3, NULL, 'Aktif'),
(111, 16, 5, 3, NULL, 'Aktif'),
(112, 17, 5, 3, NULL, 'Aktif'),
(113, 18, 5, 3, NULL, 'Aktif'),
(114, 19, 5, 3, NULL, 'Aktif'),
(115, 20, 5, 3, NULL, 'Aktif'),
(116, 21, 5, 3, NULL, 'Aktif'),
(117, 22, 5, 3, NULL, 'Aktif'),
(118, 23, 6, 3, NULL, 'Aktif'),
(119, 24, 6, 3, NULL, 'Aktif'),
(120, 25, 6, 3, NULL, 'Aktif'),
(121, 26, 6, 3, NULL, 'Aktif'),
(122, 27, 6, 3, NULL, 'Aktif'),
(123, 28, 6, 3, NULL, 'Aktif'),
(124, 29, 6, 3, NULL, 'Aktif'),
(125, 30, 6, 3, NULL, 'Aktif'),
(126, 31, 6, 3, NULL, 'Aktif'),
(127, 32, 6, 3, NULL, 'Aktif'),
(128, 33, 6, 3, NULL, 'Aktif'),
(129, 34, 6, 3, NULL, 'Aktif'),
(130, 35, 6, 3, NULL, 'Aktif'),
(131, 36, 6, 3, NULL, 'Aktif'),
(132, 37, 6, 3, NULL, 'Aktif'),
(133, 38, 6, 3, NULL, 'Aktif'),
(134, 39, 6, 3, NULL, 'Aktif'),
(135, 40, 6, 3, NULL, 'Aktif'),
(136, 41, 6, 3, NULL, 'Aktif'),
(137, 42, 6, 3, NULL, 'Aktif'),
(138, 43, 6, 3, NULL, 'Aktif'),
(139, 44, 6, 3, NULL, 'Aktif'),
(140, 45, 6, 3, NULL, 'Aktif'),
(141, 47, 6, 3, NULL, 'Aktif'),
(142, 46, 6, 3, NULL, 'Aktif'),
(143, 48, 3, 3, NULL, 'Aktif'),
(144, 49, 3, 3, NULL, 'Aktif'),
(145, 50, 3, 3, NULL, 'Aktif'),
(146, 51, 3, 3, NULL, 'Aktif'),
(147, 52, 3, 3, NULL, 'Aktif'),
(148, 53, 3, 3, NULL, 'Aktif'),
(149, 54, 3, 3, NULL, 'Aktif'),
(150, 55, 3, 3, NULL, 'Aktif'),
(151, 56, 3, 3, NULL, 'Aktif'),
(152, 57, 3, 3, NULL, 'Aktif'),
(153, 58, 3, 3, NULL, 'Aktif'),
(154, 59, 3, 3, NULL, 'Aktif'),
(155, 60, 3, 3, NULL, 'Aktif'),
(156, 61, 3, 3, NULL, 'Aktif'),
(157, 62, 3, 3, NULL, 'Aktif'),
(158, 63, 3, 3, NULL, 'Aktif'),
(159, 64, 3, 3, NULL, 'Aktif'),
(160, 65, 3, 3, NULL, 'Aktif'),
(161, 66, 3, 3, NULL, 'Aktif'),
(162, 67, 3, 3, NULL, 'Aktif'),
(163, 68, 3, 3, NULL, 'Aktif'),
(164, 69, 3, 3, NULL, 'Aktif'),
(165, 70, 3, 3, NULL, 'Aktif'),
(166, 71, 3, 3, NULL, 'Aktif'),
(167, 72, 3, 3, NULL, 'Aktif'),
(168, 73, 4, 3, NULL, 'Aktif'),
(169, 74, 4, 3, NULL, 'Aktif'),
(170, 75, 4, 3, NULL, 'Aktif'),
(171, 76, 4, 3, NULL, 'Aktif'),
(172, 77, 4, 3, NULL, 'Aktif'),
(173, 78, 4, 3, NULL, 'Aktif'),
(174, 79, 4, 3, NULL, 'Aktif'),
(175, 80, 4, 3, NULL, 'Aktif'),
(176, 81, 4, 3, NULL, 'Aktif'),
(177, 82, 4, 3, NULL, 'Aktif'),
(178, 83, 4, 3, NULL, 'Aktif'),
(179, 84, 4, 3, NULL, 'Aktif'),
(180, 85, 4, 3, NULL, 'Aktif'),
(181, 86, 4, 3, NULL, 'Aktif'),
(182, 87, 4, 3, NULL, 'Aktif'),
(183, 88, 4, 3, NULL, 'Aktif'),
(184, 89, 4, 3, NULL, 'Aktif'),
(204, 24, 13, 4, NULL, 'Aktif'),
(205, 25, 13, 4, NULL, 'Aktif'),
(206, 26, 13, 4, NULL, 'Aktif'),
(207, 27, 13, 4, NULL, 'Aktif'),
(208, 28, 13, 4, NULL, 'Aktif'),
(209, 29, 13, 4, NULL, 'Aktif'),
(210, 30, 13, 4, NULL, 'Aktif'),
(211, 31, 13, 4, NULL, 'Aktif'),
(212, 32, 13, 4, NULL, 'Aktif'),
(213, 33, 13, 4, NULL, 'Aktif'),
(214, 34, 13, 4, NULL, 'Aktif'),
(215, 35, 13, 4, NULL, 'Aktif'),
(216, 36, 13, 4, NULL, 'Aktif'),
(217, 37, 13, 4, NULL, 'Aktif'),
(218, 38, 13, 4, NULL, 'Aktif'),
(219, 39, 13, 4, NULL, 'Aktif'),
(220, 40, 13, 4, NULL, 'Aktif'),
(221, 41, 13, 4, NULL, 'Aktif'),
(222, 42, 13, 4, NULL, 'Aktif'),
(223, 43, 13, 4, NULL, 'Aktif'),
(224, 44, 13, 4, NULL, 'Aktif'),
(225, 45, 13, 4, NULL, 'Aktif'),
(226, 46, 13, 4, NULL, 'Aktif'),
(227, 47, 13, 4, NULL, 'Aktif'),
(229, 23, 13, 4, NULL, 'Aktif'),
(230, 1, 12, 4, NULL, 'Aktif'),
(231, 2, 12, 4, NULL, 'Aktif'),
(232, 3, 12, 4, NULL, 'Aktif'),
(233, 4, 12, 4, NULL, 'Aktif'),
(234, 5, 12, 4, NULL, 'Aktif'),
(235, 6, 12, 4, NULL, 'Aktif'),
(236, 7, 12, 4, NULL, 'Aktif'),
(237, 8, 12, 4, NULL, 'Aktif'),
(238, 9, 12, 4, NULL, 'Aktif'),
(239, 10, 12, 4, NULL, 'Aktif'),
(240, 11, 12, 4, NULL, 'Aktif'),
(241, 12, 12, 4, NULL, 'Aktif'),
(242, 13, 12, 4, NULL, 'Aktif'),
(243, 14, 12, 4, NULL, 'Aktif'),
(244, 15, 12, 4, NULL, 'Aktif'),
(245, 16, 12, 4, NULL, 'Aktif'),
(246, 17, 12, 4, NULL, 'Aktif'),
(247, 18, 12, 4, NULL, 'Aktif'),
(248, 19, 12, 4, NULL, 'Aktif'),
(249, 20, 12, 4, NULL, 'Aktif'),
(250, 21, 12, 4, NULL, 'Aktif'),
(251, 22, 12, 4, NULL, 'Aktif'),
(252, 48, 10, 4, NULL, 'Aktif'),
(253, 49, 10, 4, NULL, 'Aktif'),
(254, 50, 10, 4, NULL, 'Aktif'),
(255, 51, 10, 4, NULL, 'Aktif'),
(256, 52, 10, 4, NULL, 'Aktif'),
(257, 53, 10, 4, NULL, 'Aktif'),
(258, 54, 10, 4, NULL, 'Aktif'),
(259, 55, 10, 4, NULL, 'Aktif'),
(260, 56, 10, 4, NULL, 'Aktif'),
(261, 57, 10, 4, NULL, 'Aktif'),
(262, 58, 10, 4, NULL, 'Aktif'),
(263, 59, 10, 4, NULL, 'Aktif'),
(264, 60, 10, 4, NULL, 'Aktif'),
(265, 61, 10, 4, NULL, 'Aktif'),
(266, 62, 10, 4, NULL, 'Aktif'),
(267, 63, 10, 4, NULL, 'Aktif'),
(268, 64, 10, 4, NULL, 'Aktif'),
(269, 65, 10, 4, NULL, 'Aktif'),
(270, 66, 10, 4, NULL, 'Aktif'),
(271, 67, 10, 4, NULL, 'Aktif'),
(272, 68, 10, 4, NULL, 'Aktif'),
(273, 69, 10, 4, NULL, 'Aktif'),
(274, 70, 10, 4, NULL, 'Aktif'),
(275, 71, 10, 4, NULL, 'Aktif'),
(276, 72, 10, 4, NULL, 'Aktif'),
(277, 73, 11, 4, NULL, 'Aktif'),
(278, 74, 11, 4, NULL, 'Aktif'),
(279, 75, 11, 4, NULL, 'Aktif'),
(280, 76, 11, 4, NULL, 'Aktif'),
(281, 77, 11, 4, NULL, 'Aktif'),
(282, 78, 11, 4, NULL, 'Aktif'),
(283, 79, 11, 4, NULL, 'Aktif'),
(284, 80, 11, 4, NULL, 'Aktif'),
(285, 81, 11, 4, NULL, 'Aktif'),
(286, 82, 11, 4, NULL, 'Aktif'),
(287, 83, 11, 4, NULL, 'Aktif'),
(288, 84, 11, 4, NULL, 'Aktif'),
(289, 85, 11, 4, NULL, 'Aktif'),
(290, 86, 11, 4, NULL, 'Aktif'),
(291, 87, 11, 4, NULL, 'Aktif'),
(292, 88, 11, 4, NULL, 'Aktif'),
(293, 89, 11, 4, NULL, 'Aktif'),
(348, 48, 19, 5, NULL, 'Aktif'),
(349, 49, 19, 5, NULL, 'Aktif'),
(350, 50, 19, 5, NULL, 'Aktif'),
(351, 51, 19, 5, NULL, 'Aktif'),
(352, 52, 19, 5, NULL, 'Aktif'),
(353, 53, 19, 5, NULL, 'Aktif'),
(354, 54, 19, 5, NULL, 'Aktif'),
(355, 55, 19, 5, NULL, 'Aktif'),
(356, 56, 19, 5, NULL, 'Aktif'),
(357, 57, 19, 5, NULL, 'Aktif'),
(358, 58, 19, 5, NULL, 'Aktif'),
(359, 59, 19, 5, NULL, 'Aktif'),
(360, 60, 19, 5, NULL, 'Aktif'),
(361, 61, 19, 5, NULL, 'Aktif'),
(362, 62, 19, 5, NULL, 'Aktif'),
(363, 63, 19, 5, NULL, 'Aktif'),
(364, 64, 19, 5, NULL, 'Aktif'),
(365, 65, 19, 5, NULL, 'Aktif'),
(366, 66, 19, 5, NULL, 'Aktif'),
(367, 67, 19, 5, NULL, 'Aktif'),
(368, 68, 19, 5, NULL, 'Aktif'),
(369, 69, 19, 5, NULL, 'Aktif'),
(370, 70, 19, 5, NULL, 'Aktif'),
(371, 71, 19, 5, NULL, 'Aktif'),
(372, 72, 19, 5, NULL, 'Aktif'),
(373, 73, 20, 5, NULL, 'Aktif'),
(374, 74, 20, 5, NULL, 'Aktif'),
(375, 75, 20, 5, NULL, 'Aktif'),
(376, 76, 20, 5, NULL, 'Aktif'),
(377, 77, 20, 5, NULL, 'Aktif'),
(378, 78, 20, 5, NULL, 'Aktif'),
(379, 79, 20, 5, NULL, 'Aktif'),
(380, 80, 20, 5, NULL, 'Aktif'),
(381, 81, 20, 5, NULL, 'Aktif'),
(382, 82, 20, 5, NULL, 'Aktif'),
(383, 83, 20, 5, NULL, 'Aktif'),
(384, 84, 20, 5, NULL, 'Aktif'),
(385, 85, 20, 5, NULL, 'Aktif'),
(386, 86, 20, 5, NULL, 'Aktif'),
(387, 87, 20, 5, NULL, 'Aktif'),
(388, 88, 20, 5, NULL, 'Aktif'),
(389, 89, 20, 5, NULL, 'Aktif'),
(390, 23, 22, 5, NULL, 'Aktif'),
(391, 24, 22, 5, NULL, 'Aktif'),
(392, 25, 22, 5, NULL, 'Aktif'),
(393, 26, 22, 5, NULL, 'Aktif'),
(394, 27, 22, 5, NULL, 'Aktif'),
(395, 28, 22, 5, NULL, 'Aktif'),
(396, 29, 22, 5, NULL, 'Aktif'),
(397, 30, 22, 5, NULL, 'Aktif'),
(398, 31, 22, 5, NULL, 'Aktif'),
(399, 32, 22, 5, NULL, 'Aktif'),
(400, 33, 22, 5, NULL, 'Aktif'),
(401, 34, 22, 5, NULL, 'Aktif'),
(402, 35, 22, 5, NULL, 'Aktif'),
(403, 36, 22, 5, NULL, 'Aktif'),
(404, 37, 22, 5, NULL, 'Aktif'),
(405, 38, 22, 5, NULL, 'Aktif'),
(406, 39, 22, 5, NULL, 'Aktif'),
(407, 40, 22, 5, NULL, 'Aktif'),
(408, 41, 22, 5, NULL, 'Aktif'),
(409, 42, 22, 5, NULL, 'Aktif'),
(410, 43, 22, 5, NULL, 'Aktif'),
(411, 45, 22, 5, NULL, 'Aktif'),
(412, 46, 22, 5, NULL, 'Aktif'),
(413, 47, 22, 5, NULL, 'Aktif'),
(414, 2, 21, 5, NULL, 'Aktif'),
(415, 3, 21, 5, NULL, 'Aktif'),
(416, 4, 21, 5, NULL, 'Aktif'),
(417, 5, 21, 5, NULL, 'Aktif'),
(418, 6, 21, 5, NULL, 'Aktif'),
(419, 7, 21, 5, NULL, 'Aktif'),
(420, 8, 21, 5, NULL, 'Aktif'),
(421, 9, 21, 5, NULL, 'Aktif'),
(422, 11, 21, 5, NULL, 'Aktif'),
(423, 12, 21, 5, NULL, 'Aktif'),
(424, 13, 21, 5, NULL, 'Aktif'),
(425, 14, 21, 5, NULL, 'Aktif'),
(426, 16, 21, 5, NULL, 'Aktif'),
(427, 17, 21, 5, NULL, 'Aktif'),
(428, 19, 21, 5, NULL, 'Aktif'),
(429, 20, 21, 5, NULL, 'Aktif'),
(430, 21, 21, 5, NULL, 'Aktif'),
(431, 22, 21, 5, NULL, 'Aktif'),
(432, 111, 15, 5, NULL, 'Aktif'),
(434, 182, 15, 5, NULL, 'Aktif'),
(435, 138, 15, 5, NULL, 'Aktif'),
(436, 149, 15, 5, NULL, 'Aktif'),
(437, 150, 15, 5, NULL, 'Aktif'),
(438, 165, 15, 5, NULL, 'Aktif'),
(440, 214, 15, 5, NULL, 'Aktif'),
(441, 183, 15, 5, NULL, 'Aktif'),
(442, 195, 15, 5, NULL, 'Aktif'),
(443, 171, 15, 5, NULL, 'Aktif'),
(444, 154, 15, 5, NULL, 'Aktif'),
(445, 153, 15, 5, NULL, 'Aktif'),
(446, 175, 15, 5, NULL, 'Aktif'),
(447, 206, 15, 5, NULL, 'Aktif'),
(448, 215, 15, 5, NULL, 'Aktif'),
(449, 176, 15, 5, NULL, 'Aktif'),
(450, 119, 15, 5, NULL, 'Aktif'),
(451, 112, 16, 5, NULL, 'Aktif'),
(452, 114, 16, 5, NULL, 'Aktif'),
(453, 125, 16, 5, NULL, 'Aktif'),
(454, 126, 16, 5, NULL, 'Aktif'),
(455, 131, 16, 5, NULL, 'Aktif'),
(456, 134, 16, 5, NULL, 'Aktif'),
(457, 135, 16, 5, NULL, 'Aktif'),
(458, 142, 16, 5, NULL, 'Aktif'),
(459, 144, 16, 5, NULL, 'Aktif'),
(460, 147, 16, 5, NULL, 'Aktif'),
(461, 151, 16, 5, NULL, 'Aktif'),
(462, 152, 16, 5, NULL, 'Aktif'),
(463, 156, 16, 5, NULL, 'Aktif'),
(464, 157, 16, 5, NULL, 'Aktif'),
(465, 155, 16, 5, NULL, 'Aktif'),
(466, 162, 16, 5, NULL, 'Aktif'),
(467, 166, 16, 5, NULL, 'Aktif'),
(468, 167, 16, 5, NULL, 'Aktif'),
(469, 168, 16, 5, NULL, 'Aktif'),
(470, 174, 16, 5, NULL, 'Aktif'),
(471, 178, 16, 5, NULL, 'Aktif'),
(472, 179, 16, 5, NULL, 'Aktif'),
(473, 180, 16, 5, NULL, 'Aktif'),
(474, 181, 16, 5, NULL, 'Aktif'),
(475, 189, 16, 5, NULL, 'Aktif'),
(476, 190, 16, 5, NULL, 'Aktif'),
(477, 191, 16, 5, NULL, 'Aktif'),
(478, 192, 16, 5, NULL, 'Aktif'),
(479, 202, 16, 5, NULL, 'Aktif'),
(480, 204, 16, 5, NULL, 'Aktif'),
(481, 209, 16, 5, NULL, 'Aktif'),
(482, 212, 16, 5, NULL, 'Aktif'),
(483, 220, 16, 5, NULL, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_pengguna` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `nama_pengguna`, `email`, `foto`, `created_at`) VALUES
(1, 'admin', '$2y$10$T883.htbEMqKFQa3j2tk..qLg/j6B6MSHERKBy1gZdNsVB7XfTYKa', 'Administrator', '', NULL, '0000-00-00 00:00:00'),
(9, '3202125007960009', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Ai Siti Robiatul Awaliyah', NULL, NULL, '2026-01-18 08:44:27'),
(10, '3202121301860004', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Awaludin Hardiana', NULL, NULL, '2026-01-18 08:44:27'),
(11, '3202121204730001', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Dadan Silahudin', NULL, NULL, '2026-01-18 08:44:28'),
(12, '3202291205820011', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Dadun Abdul Manaf', NULL, NULL, '2026-01-18 08:44:28'),
(13, '3202124112850003', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Euis Sobariah', NULL, NULL, '2026-01-18 08:44:28'),
(14, '3202292012001365', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Falah Ependi', NULL, NULL, '2026-01-18 08:44:28'),
(15, '3202120112950007', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Kiki Kurniawan', NULL, NULL, '2026-01-18 08:44:28'),
(16, '3272034103640001', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Komariah', NULL, NULL, '2026-01-18 08:44:28'),
(17, '3202124705990003', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Maya Meira', NULL, NULL, '2026-01-18 08:44:28'),
(18, '3202124501970005', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Nani Maryani', NULL, NULL, '2026-01-18 08:44:28'),
(19, '3202120706870006', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Peri Barkah', NULL, NULL, '2026-01-18 08:44:28'),
(20, '3202121307780004', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Pura Disadad', NULL, NULL, '2026-01-18 08:44:28'),
(21, '3202125807890004', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Risdiantika Kamsiel', NULL, NULL, '2026-01-18 08:44:28'),
(22, '3202121505740001', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Roni Paslah', NULL, NULL, '2026-01-18 08:44:28'),
(23, '3202121507870003', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Saepudin', NULL, NULL, '2026-01-18 08:44:28'),
(24, '3202125907970003', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Tini Sumartini', NULL, NULL, '2026-01-18 08:44:28'),
(25, '3202112004860004', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Wawan Setiawan', NULL, NULL, '2026-01-18 08:44:28'),
(26, '3205111912900002', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Zaenal Mutaqin Ahirudin', NULL, NULL, '2026-01-18 08:44:28'),
(27, '3217072309950002', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Zaidan Ahmad Rabbani', NULL, NULL, '2026-01-18 08:44:28'),
(28, '20', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Usep Sanusi', NULL, NULL, '2026-01-18 08:44:28'),
(29, '21', '$2y$10$dHxaqHkQ3ITY1cRq2iuJ6.G2pYkK2Jy5ZLgwQ/PLGKPlpAMo2GjkG', 'Tim Tahfidz', NULL, NULL, '2026-01-18 08:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_peran`
--

CREATE TABLE `pengguna_peran` (
  `id_pengguna` int(11) NOT NULL,
  `id_peran` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna_peran`
--

INSERT INTO `pengguna_peran` (`id_pengguna`, `id_peran`) VALUES
(1, 1),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(13, 4),
(14, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id_pengumuman` int(11) NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penilaian_pembiasaan`
--

CREATE TABLE `penilaian_pembiasaan` (
  `id_penilaian` int(11) NOT NULL,
  `id_pembiasaan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `bulan` int(2) NOT NULL,
  `tahun` int(4) NOT NULL,
  `persentase_kehadiran` float NOT NULL DEFAULT 0,
  `nilai` varchar(1) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penilaian_sumatif`
--

CREATE TABLE `penilaian_sumatif` (
  `id_sumatif` int(11) NOT NULL,
  `id_guru_mapel` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `nama_penilaian` varchar(150) NOT NULL,
  `jenis_sumatif` enum('Sumatif Lingkup Materi','Sumatif Tengah Semester','Sumatif Akhir Semester','Sumatif Akhir Tahun','Sumatif Akhir Jenjang') NOT NULL,
  `tanggal_penilaian` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penugasan_jabatan`
--

CREATE TABLE `penugasan_jabatan` (
  `id_penugasan_jabatan` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `jenis_jabatan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penugasan_jabatan`
--

INSERT INTO `penugasan_jabatan` (`id_penugasan_jabatan`, `id_guru`, `id_ta`, `jenis_jabatan`, `created_at`) VALUES
(1, 17, 5, 'Kepala Laboratorium', '2026-01-20 10:11:33'),
(2, 14, 5, 'Waka Kurikulum', '2026-01-20 10:11:44'),
(3, 11, 5, 'Waka Kesiswaan', '2026-01-20 10:12:27'),
(4, 4, 5, 'Kepala Sekolah', '2026-01-21 08:02:51'),
(5, 9, 5, 'Tenaga Administrasi', '2026-01-21 08:09:32'),
(6, 17, 5, 'Operator', '2026-01-21 08:09:44');

-- --------------------------------------------------------

--
-- Table structure for table `penugasan_pembina`
--

CREATE TABLE `penugasan_pembina` (
  `id_penugasan_pembina` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penugasan_pembina`
--

INSERT INTO `penugasan_pembina` (`id_penugasan_pembina`, `id_kegiatan`, `id_guru`, `id_ta`, `created_at`) VALUES
(1, 2, 11, 5, '2026-01-20 10:12:34'),
(2, 1, 7, 5, '2026-01-20 10:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `penugasan_wali_kelas`
--

CREATE TABLE `penugasan_wali_kelas` (
  `id_penugasan_wali_kelas` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `jenis_tugas` enum('Wali Kelas','Guru Mapel') NOT NULL,
  `id_kelas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penugasan_wali_kelas`
--

INSERT INTO `penugasan_wali_kelas` (`id_penugasan_wali_kelas`, `id_guru`, `id_ta`, `jenis_tugas`, `id_kelas`) VALUES
(1, 7, 1, 'Wali Kelas', 1),
(2, 5, 1, 'Wali Kelas', 2),
(3, 7, 2, 'Wali Kelas', 7),
(4, 5, 2, 'Wali Kelas', 8),
(5, 18, 5, 'Wali Kelas', 15),
(6, 14, 5, 'Wali Kelas', 16),
(7, 12, 5, 'Wali Kelas', 17),
(8, 17, 5, 'Wali Kelas', 18),
(9, 5, 5, 'Wali Kelas', 19),
(10, 7, 5, 'Wali Kelas', 21);

-- --------------------------------------------------------

--
-- Table structure for table `peran`
--

CREATE TABLE `peran` (
  `id_peran` int(11) NOT NULL,
  `nama_peran` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peran`
--

INSERT INTO `peran` (`id_peran`, `nama_peran`) VALUES
(1, 'Admin'),
(4, 'Guru'),
(17, 'Guru Pamong'),
(5, 'Guru Piket'),
(3, 'Kepala Sekolah'),
(9, 'Kesiswaan'),
(16, 'Keuangan'),
(8, 'Kurikulum'),
(15, 'Pembina Ekstrakulikuler'),
(18, 'Pengelola TKB'),
(7, 'PPDB'),
(6, 'Siswa'),
(2, 'TU'),
(14, 'Wali Kelas');

-- --------------------------------------------------------

--
-- Table structure for table `perangkat_pembelajaran`
--

CREATE TABLE `perangkat_pembelajaran` (
  `id_perangkat` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL,
  `jenis` enum('ATP','Modul Ajar','Prosem','Prota') NOT NULL,
  `mapel` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `konten_html` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ppdb_pendaftaran`
--

CREATE TABLE `ppdb_pendaftaran` (
  `id` int(11) NOT NULL,
  `no_pendaftaran` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(5) DEFAULT NULL,
  `rw` varchar(5) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `no_hp_siswa` varchar(15) DEFAULT NULL,
  `email_siswa` varchar(100) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `pekerjaan_ayah` varchar(50) DEFAULT NULL,
  `penghasilan_ayah` varchar(50) DEFAULT NULL,
  `no_hp_ayah` varchar(15) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `pekerjaan_ibu` varchar(50) DEFAULT NULL,
  `penghasilan_ibu` varchar(50) DEFAULT NULL,
  `no_hp_ibu` varchar(15) DEFAULT NULL,
  `nama_wali` varchar(100) DEFAULT NULL,
  `pekerjaan_wali` varchar(50) DEFAULT NULL,
  `no_hp_wali` varchar(15) DEFAULT NULL,
  `asal_sekolah` varchar(100) DEFAULT NULL,
  `alamat_sekolah` text DEFAULT NULL,
  `npsn_sekolah` varchar(20) DEFAULT NULL,
  `foto_siswa` varchar(255) DEFAULT NULL,
  `foto_kk` varchar(255) DEFAULT NULL,
  `foto_akta` varchar(255) DEFAULT NULL,
  `foto_ijazah` varchar(255) DEFAULT NULL,
  `foto_raport` varchar(255) DEFAULT NULL,
  `jalur_pendaftaran` varchar(50) DEFAULT 'Zonasi',
  `status` enum('pending','diverifikasi','diterima','ditolak','diproses_jadi_siswa') DEFAULT 'pending',
  `catatan_verifikasi` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `id_ta` int(11) DEFAULT NULL,
  `sumber_pendaftaran` enum('online','manual') DEFAULT 'online',
  `id_siswa` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ppdb_pendaftaran`
--

INSERT INTO `ppdb_pendaftaran` (`id`, `no_pendaftaran`, `nama_lengkap`, `nik`, `nisn`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `alamat`, `rt`, `rw`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `no_hp_siswa`, `email_siswa`, `nama_ayah`, `pekerjaan_ayah`, `penghasilan_ayah`, `no_hp_ayah`, `nama_ibu`, `pekerjaan_ibu`, `penghasilan_ibu`, `no_hp_ibu`, `nama_wali`, `pekerjaan_wali`, `no_hp_wali`, `asal_sekolah`, `alamat_sekolah`, `npsn_sekolah`, `foto_siswa`, `foto_kk`, `foto_akta`, `foto_ijazah`, `foto_raport`, `jalur_pendaftaran`, `status`, `catatan_verifikasi`, `verified_by`, `verified_at`, `id_ta`, `sumber_pendaftaran`, `id_siswa`, `created_at`, `updated_at`) VALUES
(1, 'PPDB-2026-0001', 'ABDUL MUIZ', '3202132011090006', '3092646133', 'Sukabumi', '2009-11-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:03:56', 5, 'manual', 109, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(2, 'PPDB-2026-0002', 'ADRIAN MAULANA YUSUP', '3202142711090003', '0092931522', 'Sukabumi', '2009-11-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UCUP SUPRIADI', NULL, '085779660796', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:00', 5, 'manual', 110, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(3, 'PPDB-2026-0003', 'AGIS MUTIARA', '3202126808090003', '0095780821', 'Sukabumi', '2009-08-28', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NANANG SUNARYA', NULL, NULL, 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:03', 5, 'manual', 111, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(4, 'PPDB-2026-0004', 'AGUS RAMDANI', '3202113108090004', '0096593025', 'Sukabumi', '2009-08-31', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UTEN', NULL, '088212406294', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:06', 5, 'manual', 112, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(5, 'PPDB-2026-0005', 'AIRA PUTRI ADITIYA', '3202064108090002', '0099755704', 'Sukabumi', '2009-08-01', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Adi Umay Setiawan', NULL, '085892946682', 'SMP NEGERI 2 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:12', 5, 'manual', 113, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(6, 'PPDB-2026-0006', 'ALDI SAPUTRA', '3202281511080001', '0089675328', 'Sukabumi', '2008-11-15', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Aceng Iskandar', NULL, '083877590683', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:15', 5, 'manual', 114, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(7, 'PPDB-2026-0007', 'AMELDA', '3202134607090001', '0094447260', 'Sukabumi', '2009-07-06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ojen', NULL, '085722157471', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:18', 5, 'manual', 115, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(8, 'PPDB-2026-0008', 'AMELIA', '3202136312080007', '0087663921', 'Sukabumi', '2008-12-23', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DABLU', NULL, '085871270231', 'SMP ISLAM YPI PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:20', 5, 'manual', 116, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(9, 'PPDB-2026-0009', 'ANDIKA MAULANA', '3202132003090003', '3091137244', 'Sukabumi', '2009-03-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AMULLOH', NULL, NULL, 'SMPS ISLAM INSAN KAMIL MANDIRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:24', 5, 'manual', 117, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(10, 'PPDB-2026-0010', 'ANISA', '3202146007080001', '3086466602', 'Sukabumi', '2008-07-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:30', 5, 'manual', 118, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(11, 'PPDB-2026-0011', 'ANISA PITRI', '3202096009080001', '0083380895', 'Sukabumi', '2008-09-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Saepuloh', NULL, NULL, 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:33', 5, 'manual', 119, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(12, 'PPDB-2026-0012', 'ANISA RAHMA MUSTIKA', '3202136610080002', '0086222783', 'Sukabumi', '2008-10-26', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Maman', NULL, '088809677881', 'SMP NEGERI 1 PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:36', 5, 'manual', 120, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(13, 'PPDB-2026-0013', 'ANISA SAFITRI', '3202146404100001', '3101261006', 'Sukabumi', '2010-04-24', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:38', 5, 'manual', 121, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(14, 'PPDB-2026-0014', 'ARYASATYA FIRMANSYAH', '3202100105060004', '0067504067', 'Sukabumi', '2006-05-01', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Rudi', NULL, '088290259753', 'SMP NEGERI 2 CIKEMBAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:54', 5, 'manual', 122, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(15, 'PPDB-2026-0015', 'AULIA DEWI SRI WULANDARI', '3202134204100002', '3108366273', 'Sukabumi', '2010-04-02', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:04:57', 5, 'manual', 123, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(16, 'PPDB-2026-0016', 'AWALIAH', '3202136503100002', '3106589031', 'Sukabumi', '2010-03-25', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:00', 5, 'manual', 124, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(17, 'PPDB-2026-0017', 'BAYHAQI ALKAFARO', '3202111605100007', '0109599808', 'Sukabumi', '2010-05-16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AGUS LATIFUROHMAN', NULL, '081546714033', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:03', 5, 'manual', 125, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(18, 'PPDB-2026-0018', 'BINTANG FURQON', '3301130612080006', '          ', 'Malang', '2006-12-06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:05', 5, 'manual', 126, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(19, 'PPDB-2026-0019', 'DESTI ANJANI', '3202145912050003', '0056410894', 'Sukabumi', '2005-12-19', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:07', 5, 'manual', 127, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(20, 'PPDB-2026-0020', 'DIKI AGUSTIAN', '3202141608060002', '0069503994', 'Sukabumi', '2006-08-16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Kusnadi Nata', NULL, '083805427248', 'SMP NEGERI 1 PARAKANSALAK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:09', 5, 'manual', 128, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(21, 'PPDB-2026-0021', 'DINI PUTRI ANDRIANI', '3202145810090001', '0097968668', 'Sukabumi', '2009-10-18', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Andi Sutandi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:12', 5, 'manual', 129, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(22, 'PPDB-2026-0022', 'EDWAR GUPRIYAN', '3202141610080003', '3089716573', 'Sukabumi', '2008-10-16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:14', 5, 'manual', 130, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(23, 'PPDB-2026-0023', 'ELIYA YULIANI', '3202125806100003', '0104684933', 'Sukabumi', '2010-06-18', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'U. ISKANDAR', NULL, '083127236843', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:16', 5, 'manual', 131, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(24, 'PPDB-2026-0024', 'ELSI', '3202146507080003', '0082591669', 'Sukabumi', '2008-07-25', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ilan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:18', 5, 'manual', 132, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(25, 'PPDB-2026-0025', 'FABIAN YUSUF', '3202141909090002', '3093879901', 'Sukabumi', '2009-09-19', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:21', 5, 'manual', 133, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(26, 'PPDB-2026-0026', 'FADHIL ABDILLAH', '3202110601100001', '0107965418', 'Sukabumi', '2010-01-06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UBAIDILLAH', NULL, '088212398287', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:24', 5, 'manual', 134, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(27, 'PPDB-2026-0027', 'FAIRUS MUTIARAHIM', '3202115101100002', '0107660632', 'Sukabumi', '2010-01-11', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SAEPUDIN', NULL, '088212406327', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:26', 5, 'manual', 135, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(28, 'PPDB-2026-0028', 'FAUZIAH', '3202136103080002', '0082973002', 'Sukabumi', '2008-03-21', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Alm. Bubun N.', NULL, NULL, 'SMP ISLAM AL QUDSIYAH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:29', 5, 'manual', 136, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(29, 'PPDB-2026-0029', 'FERA JULIANTI', '3202185007100004', '0105238344', 'Sukabumi', '2010-07-10', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Usman', NULL, '085779372869', 'SMP NEGERI 2 KALAPANUNGGAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:33', 5, 'manual', 137, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(30, 'PPDB-2026-0030', 'GHEA ANANDA AVRIANTY', '3202125404100003', '0107833156', 'Sukabumi', '2010-04-14', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ANWARUDIN', NULL, '0859102814726', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:35', 5, 'manual', 138, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(31, 'PPDB-2026-0031', 'GRESIA SUMAROU', '3202145605080003', '0088748192', 'Sukabumi', '2008-05-16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dede', NULL, '085723674470', 'SMP ISLAM YPI PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:38', 5, 'manual', 139, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(32, 'PPDB-2026-0032', 'HABIBAH', '3202195006070001', '0083788513', 'Sukabumi', '2008-02-11', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ENANG', NULL, '085710951742', 'PKBM ANGGREK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:40', 5, 'manual', 140, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(33, 'PPDB-2026-0033', 'HAIKAL GALIH MULYANA', '3202061407100001', '3108334256', 'Sukabumi', '2010-07-14', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dadang Mulyana', NULL, NULL, 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:43', 5, 'manual', 141, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(34, 'PPDB-2026-0034', 'HERA IDA', '3202124606080004', '0085842308', 'Sukabumi', '2008-06-06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Nandang', NULL, '083160271051', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:45', 5, 'manual', 142, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(35, 'PPDB-2026-0035', 'HERDI', '3202182601100001', '0109278369', 'Sukabumi', '2010-01-26', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Andi', NULL, NULL, 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:47', 5, 'manual', 143, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(36, 'PPDB-2026-0036', 'HILDA MUTIARA ZULFA', '3216216810090006', '0099368150', 'Bekasi', '2009-10-28', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Romi Suheri', NULL, '08872090763', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:50', 5, 'manual', 144, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(37, 'PPDB-2026-0037', 'ICA', '3202145003100001', '0107143054', 'Sukabumi', '2010-03-10', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Amar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:52', 5, 'manual', 145, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(38, 'PPDB-2026-0038', 'INDAH ANJANI', '3202186812060001', '0063676488', 'Sukabumi', '2006-12-28', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ajat Munajat', NULL, '081291799420', 'SMP ISLAM TERPADU AL - MUTAQIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:55', 5, 'manual', 146, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(39, 'PPDB-2026-0039', 'INDRI YULIANTI', '3202117008090006', '0099835840', 'Sukabumi', '2009-08-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMAN', NULL, '088212406339', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:05:57', 5, 'manual', 147, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(40, 'PPDB-2026-0040', 'INDRIYANI', '3202144408090001', '0095556290', 'Sukabumi', '2009-08-04', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Daepi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:00', 5, 'manual', 148, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(41, 'PPDB-2026-0041', 'IRMAN MAULANA', '3202121210090002', '0094272850', 'Sukabumi', '2009-10-12', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'IJI', NULL, '083805213553', 'SMP ISLAM CENDIKIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:02', 5, 'manual', 149, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(42, 'PPDB-2026-0042', 'ISMA', '3202124404100001', '0108779297', 'Sukabumi', '2010-04-04', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NANA SURYANA', NULL, NULL, 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:04', 5, 'manual', 150, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(43, 'PPDB-2026-0043', 'JELITA SURYA SABRINA PUTRI', '3202114512090002', '0094510709', 'Sukabumi', '2009-12-05', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ENDIN SALAHUDIN', NULL, '088212406356', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:06', 5, 'manual', 151, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(44, 'PPDB-2026-0044', 'JIAN BAAMI HABTI', '3202111801100002', '3105275586', 'Sukabumi', '2010-01-18', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMEY HABUDIN', NULL, NULL, 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:08', 5, 'manual', 152, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(45, 'PPDB-2026-0045', 'KASANDRA AQUINI', '3202126607090001', '0096216878', 'Sukabumi', '2009-07-26', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'LILI SUTERLY', NULL, '085624117484', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:11', 5, 'manual', 153, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(46, 'PPDB-2026-0046', 'KESYA PUTRI NATAPLAWIRA', '3202335605080001', '0089846692', 'Sukabumi', '2008-05-16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'HOKI RIKIANTO', NULL, '085863315777', 'SMP N 1 KOTA SUKABUMI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:13', 5, 'manual', 154, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(47, 'PPDB-2026-0047', 'LUSI WIDIA MAULIDA', '3201275304080002', '0097279667', 'Bogor', '2009-03-13', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ASEP KHAIDAR', NULL, '085782659003', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:15', 5, 'manual', 155, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(48, 'PPDB-2026-0048', 'LUTFI ALFIANTI', '3202315111090001', '3091820543', 'Sukabumi', '2009-11-11', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:17', 5, 'manual', 156, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(49, 'PPDB-2026-0049', 'LUTHVIANI ULFA', '3202117004090005', '0097646759', 'Sukabumi', '2009-04-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'HAKIM MUDIN', NULL, '088212406373', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:19', 5, 'manual', 157, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(50, 'PPDB-2026-0050', 'M. DZUBIAN SYAFIQ ABDILLAH', '3202130407090003', '0099587391', 'Sukabumi', '2009-07-04', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NURHADI', NULL, '085723230926', 'SMP NEGERI 1 PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:22', 5, 'manual', 158, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(51, 'PPDB-2026-0051', 'M. FAHRI SUGANDA', '3202140205100001', '0106286817', 'Sukabumi', '2010-05-02', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Suganda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:30', 5, 'manual', 159, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(52, 'PPDB-2026-0052', 'M. RIPAL ALHUSAERI', '3202142701100001', '0092517806', 'Sukabumi', '2010-01-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Heri Husaheri', NULL, '081546276036', 'SMP ISLAM TERPADU AL - MUTAQIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:32', 5, 'manual', 160, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(53, 'PPDB-2026-0053', 'MAEDASARI', '3202144812090001', '0097809406', 'Sukabumi', '2009-12-08', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ayi Suherdin', NULL, '085798471151', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:34', 5, 'manual', 161, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(54, 'PPDB-2026-0054', 'MARWAN SETIAWAN', '3202272706090001', '3094009694', 'Sukabumi', '2009-06-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SARIPUDIN', NULL, NULL, 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:36', 5, 'manual', 162, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(55, 'PPDB-2026-0055', 'MELISA APRILLIANI', '3202146204100001', '3101211123', 'Sukabumi', '2010-04-22', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Imang Sudirman', NULL, NULL, 'SMP ISLAM YPI PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:39', 5, 'manual', 163, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(56, 'PPDB-2026-0056', 'MOH. RIFKI RIZKY ARRAHMAN', '3202142606090001', '0093182092', 'Sukabumi', '2009-06-26', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Maman Suparman', NULL, '085863424761', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:41', 5, 'manual', 164, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(57, 'PPDB-2026-0057', 'MONA MUTIARA', '3202124301100002', '0103932240', 'Sukabumi', '2010-01-03', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ENCING', NULL, '083819825618', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:43', 5, 'manual', 165, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(58, 'PPDB-2026-0058', 'MUCHAMMAD FAISAL', '3202112101100009', '0109697708', 'Sukabumi', '2010-01-21', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DOMI NADORI', NULL, '085862929295', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:46', 5, 'manual', 166, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(59, 'PPDB-2026-0059', 'MUHAMMAD FATHURROHMAN', '3201271901100002', '0103248529', 'Bogor', '2010-01-19', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ABDURRAHMAN', NULL, '081386354440', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:49', 5, 'manual', 167, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(60, 'PPDB-2026-0060', 'MUHAMMAD IBNU MUBAROK AZZEIN', '3201273001110001', '0119632593', 'Bogor', '2011-01-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ZAENAL ABIDIN', NULL, '085774826662', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:52', 5, 'manual', 168, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(61, 'PPDB-2026-0061', 'MUHAMMAD RAFLI AL AZHARI', '3202132908080003', '3083264206', 'Sukabumi', '2008-08-29', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ajhari Salam', NULL, '085722163114', 'SMP NEGERI 2 PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:55', 5, 'manual', 169, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(62, 'PPDB-2026-0062', 'MUHAMMAD RISKY', '3202102302100008', '0101275699', 'Sukabumi', '2010-02-23', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'JOKO WAHYONO', NULL, '085798136374', 'SMP NEGERI 2 CIKEMBAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:56', 5, 'manual', 170, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(63, 'PPDB-2026-0063', 'MUHAMMAD TUGRIL ARRAIHAN', '3202122203090007', '3091958637', 'Sukabumi', '2009-03-22', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:06:58', 5, 'manual', 171, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(64, 'PPDB-2026-0064', 'MUHKLISIHIN', '3202182803100003', '0103596568', 'Sukabumi', '2010-03-28', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AMIN S', NULL, '085721008891', 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:00', 5, 'manual', 172, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(65, 'PPDB-2026-0065', 'MUTIARA LAILA PUTRI', '3202144609100001', '0105179179', 'Sukabumi', '2010-09-06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Lalan Suparlan', NULL, '085692308561', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:02', 5, 'manual', 173, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(66, 'PPDB-2026-0066', 'NABILLAH MEGA FIKRIANI', '3202116506090005', '0098152876', 'Sukabumi', '2009-06-25', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'YUSUF HIDAYATULLAH', NULL, '088212406404', 'SMP AZZAINIYYAH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:04', 5, 'manual', 174, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(67, 'PPDB-2026-0067', 'NADIA MARDIANA', '3202126107100002', '0107573886', 'Sukabumi', '2010-07-21', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'KOMARUDIN', NULL, '083806813659', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:06', 5, 'manual', 175, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(68, 'PPDB-2026-0068', 'NAILA FITRI RAHMADHANI', '3202125709090003', '0095303568', 'Sukabumi', '2009-09-17', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'OPIYANI', NULL, '0859102810549', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:08', 5, 'manual', 176, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(69, 'PPDB-2026-0069', 'NITA MAULANI', '3202196403080002', '0089102130', 'Sukabumi', '2008-03-24', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NURDIN', NULL, '081211437966', 'PKBM ANGGREK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:10', 5, 'manual', 177, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(70, 'PPDB-2026-0070', 'NOVITA ILMIRA DWI PURNAMA', '3202117011090003', '0099066129', 'Sukabumi', '2009-11-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DEPI PURNAMA', NULL, '08811452085', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:13', 5, 'manual', 178, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(71, 'PPDB-2026-0071', 'NUR WAHID SALIM', '3202282209090003', '0092492057', 'Sukabumi', '2009-09-22', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Dadan Samozni', NULL, '081281174625', 'SMP IT MADANI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:16', 5, 'manual', 179, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(72, 'PPDB-2026-0072', 'NURAENI', '3202114306080005', '0088250631', 'Sukabumi', '2008-06-03', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ECEP', NULL, '088212473659', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:18', 5, 'manual', 180, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(73, 'PPDB-2026-0073', 'NURHAIFA', '3202316011090002', '0094887757', 'Sukabumi', '2009-11-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Kuseri', NULL, '088211789549', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:20', 5, 'manual', 181, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(74, 'PPDB-2026-0074', 'NURIL YAHDIK', '3202121907090002', '0093068546', 'Sukabumi', '2009-07-19', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jakaria Supitrah', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:22', 5, 'manual', 182, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(75, 'PPDB-2026-0075', 'NURUL AZMI', '3202296309090001', '0098346836', 'Sukabumi', '2009-09-23', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:24', 5, 'manual', 183, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(76, 'PPDB-2026-0076', 'PAHMI AJIDIN', '3202142202090002', '0038078737', 'Sukabumi', '2009-02-22', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mamad', NULL, '085861768433', 'SMP ISLAM TERPADU AL - MUTAQIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:26', 5, 'manual', 184, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(77, 'PPDB-2026-0077', 'PAHRI RAMADHAN', '3202143008090001', '0099372913', 'Sukabumi', '2009-08-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AMAT', NULL, '083811975731', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:28', 5, 'manual', 185, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(78, 'PPDB-2026-0078', 'PANDI', '3202130802080003', '0085457271', 'Sukabumi', '2008-02-08', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Judin', NULL, '083891071168', 'SMPS ISLAM INSAN KAMIL MANDIRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:30', 5, 'manual', 186, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(79, 'PPDB-2026-0079', 'PIONA ELDI OKTAVIA', '3202134210080004', '3086810012', 'Sukabumi', '2008-10-02', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Edi Junaedi', NULL, NULL, 'SMP ISLAM YPI PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:32', 5, 'manual', 187, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(80, 'PPDB-2026-0080', 'PUTRI AMELIA', '3202136504080002', '0087974126', 'Sukabumi', '2008-04-25', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Taufik Akmal', NULL, '083805509558', 'SMP PGRI PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:38', 5, 'manual', 188, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(81, 'PPDB-2026-0081', 'RAIHAN CAHYA MAULID', '3202111103100001', '0108770955', 'Sukabumi', '2010-03-11', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'KOMARUDIN', NULL, '085863246022', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:41', 5, 'manual', 189, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(82, 'PPDB-2026-0082', 'RANDI', '3202113001100006', '0109178444', 'Sukabumi', '2010-01-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'PEPEN SUPENDI', NULL, '088212406259', 'SMP IT MADANI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:43', 5, 'manual', 190, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(83, 'PPDB-2026-0083', 'RAPLI MAULANA', '3202110201100004', '0108047231', 'Sukabumi', '2010-01-02', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATIN PRATAMA', NULL, '088212473669', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:45', 5, 'manual', 191, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(84, 'PPDB-2026-0084', 'RASNAMILA', '3202116008090004', '0091861319', 'Sukabumi', '2009-08-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jiji', NULL, '083872942775', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:47', 5, 'manual', 192, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(85, 'PPDB-2026-0085', 'RATIH MAULIDA', '3202146702100001', '0103957465', 'Sukabumi', '2010-02-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EMUY MURDANI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:49', 5, 'manual', 193, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(86, 'PPDB-2026-0086', 'REHAN NURJAELANI', '3202130606090005', '3096979240', 'Sukabumi', '2009-06-06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:51', 5, 'manual', 194, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(87, 'PPDB-2026-0087', 'REHAN SOMANTRI', '3202121705090002', '0092294359', 'Sukabumi', '2009-05-17', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Agus Somantri', NULL, '085798372601', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:54', 5, 'manual', 195, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(88, 'PPDB-2026-0088', 'RENDIYAWAN', '3202290109100003', '0102111932', 'Sukabumi', '2010-09-01', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'HERMAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:56', 5, 'manual', 196, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(89, 'PPDB-2026-0089', 'RISA FITRIANI', '3202146009090003', '0094943147', 'Sukabumi', '2009-09-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AHMAD TOTO', NULL, NULL, 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:07:58', 5, 'manual', 197, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(90, 'PPDB-2026-0090', 'RISMA JUNITA', '3202145006090001', '0093101241', 'Sukabumi', '2009-06-10', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DADANG NR', NULL, '085720992274', 'SMP NEGERI 1 BOJONGGENTENG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:00', 5, 'manual', 198, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(91, 'PPDB-2026-0091', 'ROBY ARDIANSYAH', '3202131105090002', '0095721154', 'Sukabumi', '2009-05-11', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '085724139578', 'SMP NEGERI 2 PARUNGKUDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:02', 5, 'manual', 199, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(92, 'PPDB-2026-0092', 'SAEPURROHIM KARIM', '3202060709090002', '3093399442', 'Sukabumi', '2009-09-07', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Enjang Hermawan', NULL, NULL, 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:05', 5, 'manual', 200, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(93, 'PPDB-2026-0093', 'SALMAN ALFARISI SYA`AR', '3202141111090004', '3092640096', 'Sukabumi', '2009-11-11', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:07', 5, 'manual', 201, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(94, 'PPDB-2026-0094', 'SIFA SILFIANA', '3202115805090001', '0099813984', 'Sukabumi', '2009-05-18', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UCI SANUSI', NULL, '088212473687', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:09', 5, 'manual', 202, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(95, 'PPDB-2026-0095', 'SILVIA WAVIQ RAMADHANI', '3201254209080004', '0082778203', 'Bogor', '2008-09-02', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ADI SUCIPTO', NULL, '089518828839', 'SMP ISLAM AL BAROKAH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:11', 5, 'manual', 203, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(96, 'PPDB-2026-0096', 'SITI FATIMAH AZ-ZAHRA', NULL, NULL, 'Sukabumi', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:14', 5, 'manual', 204, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(97, 'PPDB-2026-0097', 'SITI MUNIFAH SIRIN', '3202127005080003', '0084999054', 'Sukabumi', '2008-05-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AHMAD SYUKUR', NULL, '085722113611', 'SMP IT TAHSIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:19', 5, 'manual', 206, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(98, 'PPDB-2026-0098', 'SITI NURHALISA', '3202136006090005', '0095433011', 'Sukabumi', '2009-06-20', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:21', 5, 'manual', 207, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(99, 'PPDB-2026-0099', 'SITI PATIMAH', '3202137006080004', '0093589815', 'Sukabumi', '2008-06-30', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:26', 5, 'manual', 208, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(100, 'PPDB-2026-0100', 'SITI PATIMAH', '3202114503100007', '0105415145', 'Sukabumi', '2010-03-05', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ASEP SAEPURROHMAN', NULL, '088210853409', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:30', 5, 'manual', 209, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(101, 'PPDB-2026-0101', 'SITI SALMA', '3202146411090004', '0094288959', 'Sukabumi', '2009-11-24', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:32', 5, 'manual', 210, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(102, 'PPDB-2026-0102', 'SITI SHOPIA ULFA', '3202134405100001', '3103432560', 'Sukabumi', '2010-05-04', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:36', 5, 'manual', 211, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(103, 'PPDB-2026-0103', 'SITI SYARIFAH MARDHOTILAH', '3201276704100001', '0101013848', 'Bogor', '2010-04-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MISBAH RUSMANA', NULL, '082113714709', 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:38', 5, 'manual', 212, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(104, 'PPDB-2026-0104', 'SRY MARLINA', '3202134711080001', '0087957830', 'Sukabumi', '2008-11-07', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:40', 5, 'manual', 213, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(105, 'PPDB-2026-0105', 'SUSAN MEILANI', '3202126305090006', '0091536546', 'Sukabumi', '2009-05-23', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Iwan', NULL, '083823083989', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:42', 5, 'manual', 214, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(106, 'PPDB-2026-0106', 'SYIFA RACHMAWATI AWALIYAH', '3202125702100002', '0109796338', 'Sukabumi', '2010-02-17', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DEDE SETIAWAN', NULL, '083805848159', 'SMP IT DARUL IBTIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:44', 5, 'manual', 215, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(107, 'PPDB-2026-0107', 'WILDAN', '3275012706090004', '3094374335', 'Bekasi', '2009-06-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Iwan', NULL, NULL, 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:49', 5, 'manual', 217, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(108, 'PPDB-2026-0108', 'WILDANSYAH DWI KUSUMA', '3202142406100004', '0105575590', 'Sukabumi', '2010-06-24', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ADE BUDI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:51', 5, 'manual', 218, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(109, 'PPDB-2026-0109', 'WINDI RAMADANI', '3202184804100001', '0098044990', 'Sukabumi', '2010-04-08', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sumarja', NULL, '085716587598', 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:54', 5, 'manual', 219, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(110, 'PPDB-2026-0110', 'YUNI', '3202315604100001', '0105322682', 'Sukabumi', '2010-04-16', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ISAK', NULL, NULL, 'SMP ISLAM NURUL FIKRI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:57', 5, 'manual', 220, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(111, 'PPDB-2026-0111', 'ZIDAN SYAHRIL ARYANSA', '3202140501100001', '3101276123', 'Sukabumi', '2010-01-05', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:09:00', 5, 'manual', 221, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(112, 'PPDB-2026-0112', 'ZIRA PUSPITA', '3202186706100001', '0103534124', 'Sukabumi', '2010-06-27', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ujang Juarna', NULL, '081288383722', 'SMP BAET EL ANSHAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:09:03', 5, 'manual', 222, '2026-01-27 00:03:24', '2026-01-27 02:05:02');
INSERT INTO `ppdb_pendaftaran` (`id`, `no_pendaftaran`, `nama_lengkap`, `nik`, `nisn`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `alamat`, `rt`, `rw`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `no_hp_siswa`, `email_siswa`, `nama_ayah`, `pekerjaan_ayah`, `penghasilan_ayah`, `no_hp_ayah`, `nama_ibu`, `pekerjaan_ibu`, `penghasilan_ibu`, `no_hp_ibu`, `nama_wali`, `pekerjaan_wali`, `no_hp_wali`, `asal_sekolah`, `alamat_sekolah`, `npsn_sekolah`, `foto_siswa`, `foto_kk`, `foto_akta`, `foto_ijazah`, `foto_raport`, `jalur_pendaftaran`, `status`, `catatan_verifikasi`, `verified_by`, `verified_at`, `id_ta`, `sumber_pendaftaran`, `id_siswa`, `created_at`, `updated_at`) VALUES
(113, 'PPDB-2026-0113', 'WANGI', '3202144412090003', '3095898697', 'Sukabumi', '2009-12-04', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '085736823765', 'SMP ISLAM TERPADU AL JABHATUL ISLAMIYAH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:47', 5, 'manual', 216, '2026-01-27 00:03:24', '2026-01-27 02:05:02'),
(114, 'PPDB-2026-0114', 'SITI MASNONEH', '3202144202100001', '3105548859', 'Sukabumi', '2010-02-02', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Zonasi', 'diproses_jadi_siswa', NULL, 1, '2026-01-27 07:08:16', 5, 'manual', 205, '2026-01-27 00:03:24', '2026-01-27 02:05:02');

-- --------------------------------------------------------

--
-- Table structure for table `presensi_ekstrakurikuler`
--

CREATE TABLE `presensi_ekstrakurikuler` (
  `id_presensi` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `status` enum('H','S','I','A') DEFAULT 'H',
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi_kewirausahaan`
--

CREATE TABLE `presensi_kewirausahaan` (
  `id_presensi` int(11) NOT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `status` enum('H','S','I','A') DEFAULT 'H',
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi_kokulikuler`
--

CREATE TABLE `presensi_kokulikuler` (
  `id_presensi` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `status` enum('H','S','I','A') DEFAULT 'H',
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi_pembiasaan`
--

CREATE TABLE `presensi_pembiasaan` (
  `id_presensi` int(11) NOT NULL,
  `id_jurnal` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `status` enum('H','S','I','A') DEFAULT 'H'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presensi_tahfidz`
--

CREATE TABLE `presensi_tahfidz` (
  `id_presensi` int(11) NOT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `status` enum('H','S','I','A') DEFAULT 'H',
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profil_guru`
--

CREATE TABLE `profil_guru` (
  `id_profil` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `gelar_depan` varchar(50) DEFAULT NULL,
  `gelar_belakang` varchar(50) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email_pribadi` varchar(100) DEFAULT NULL,
  `nama_ibu_kandung` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `file_ijazah_s1` varchar(255) DEFAULT NULL,
  `file_serdik` varchar(255) DEFAULT NULL,
  `file_ktp` varchar(255) DEFAULT NULL,
  `file_kk` varchar(255) DEFAULT NULL,
  `file_akte` varchar(255) DEFAULT NULL,
  `file_npwp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profil_sekolah`
--

CREATE TABLE `profil_sekolah` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(255) DEFAULT NULL,
  `npsn` varchar(20) DEFAULT NULL,
  `bentuk_pendidikan` varchar(10) DEFAULT 'SMA',
  `kurikulum` varchar(20) DEFAULT NULL,
  `nama_kepala_sekolah` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `koordinat` varchar(50) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `status_sekolah` enum('Negeri','Swasta') DEFAULT 'Swasta',
  `nama_yayasan` varchar(100) DEFAULT NULL,
  `sk_izin_operasional` varchar(100) DEFAULT NULL,
  `sk_akreditasi` varchar(100) DEFAULT NULL,
  `moto` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profil_sekolah`
--

INSERT INTO `profil_sekolah` (`id`, `nama_sekolah`, `npsn`, `bentuk_pendidikan`, `kurikulum`, `nama_kepala_sekolah`, `alamat`, `koordinat`, `telepon`, `email`, `website`, `status_sekolah`, `nama_yayasan`, `sk_izin_operasional`, `sk_akreditasi`, `moto`, `logo`) VALUES
(1, 'SMA PLUS AL MANSHURIYAH', '20247166', 'SMA/MA', 'Kurikulum Merdeka', 'Dadun Abdul Manaf, S.E., M.Pd.', 'Jl. Kalaparea KM. 5 RT 03 RW 09 Desa Kalaparea Kec. Nagrak Kab. Sukabumi', '', '083818107386', 'smasplusalmanshuriyah@gmail.com', '', 'Swasta', 'YAYASAN TARBIYATUSSHIBYAN INDONESIA', '', '', '', 'logo_sekolah_1765762479_logo sekolah.png');

-- --------------------------------------------------------

--
-- Table structure for table `profil_siswa`
--

CREATE TABLE `profil_siswa` (
  `id_profil` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `pekerjaan_ayah` varchar(50) DEFAULT NULL,
  `telp_ayah` varchar(20) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `pekerjaan_ibu` varchar(50) DEFAULT NULL,
  `telp_ibu` varchar(20) DEFAULT NULL,
  `nama_wali` varchar(100) DEFAULT NULL,
  `pekerjaan_wali` varchar(50) DEFAULT NULL,
  `telp_wali` varchar(20) DEFAULT NULL,
  `alamat_wali` text DEFAULT NULL,
  `file_ijazah` varchar(255) DEFAULT NULL,
  `file_kartu_keluarga` varchar(255) DEFAULT NULL,
  `file_akte_lahir` varchar(255) DEFAULT NULL,
  `file_ktp_ortu` varchar(255) DEFAULT NULL,
  `file_kip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profil_siswa`
--

INSERT INTO `profil_siswa` (`id_profil`, `id_siswa`, `nama_ayah`, `pekerjaan_ayah`, `telp_ayah`, `nama_ibu`, `pekerjaan_ibu`, `telp_ibu`, `nama_wali`, `pekerjaan_wali`, `telp_wali`, `alamat_wali`, `file_ijazah`, `file_kartu_keluarga`, `file_akte_lahir`, `file_ktp_ortu`, `file_kip`) VALUES
(1, 109, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 110, NULL, NULL, NULL, NULL, NULL, NULL, 'UCUP SUPRIADI', NULL, '085779660796', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 111, NULL, NULL, NULL, NULL, NULL, NULL, 'NANANG SUNARYA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 112, NULL, NULL, NULL, NULL, NULL, NULL, 'UTEN', NULL, '088212406294', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 113, NULL, NULL, NULL, NULL, NULL, NULL, 'Adi Umay Setiawan', NULL, '085892946682', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 114, NULL, NULL, NULL, NULL, NULL, NULL, 'Aceng Iskandar', NULL, '083877590683', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 115, NULL, NULL, NULL, NULL, NULL, NULL, 'Ojen', NULL, '085722157471', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 116, NULL, NULL, NULL, NULL, NULL, NULL, 'DABLU', NULL, '085871270231', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 117, NULL, NULL, NULL, NULL, NULL, NULL, 'AMULLOH', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 119, NULL, NULL, NULL, NULL, NULL, NULL, 'Saepuloh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 120, NULL, NULL, NULL, NULL, NULL, NULL, 'Maman', NULL, '088809677881', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 121, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 122, NULL, NULL, NULL, NULL, NULL, NULL, 'Rudi', NULL, '088290259753', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 123, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 124, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 125, NULL, NULL, NULL, NULL, NULL, NULL, 'AGUS LATIFUROHMAN', NULL, '081546714033', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 127, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 128, NULL, NULL, NULL, NULL, NULL, NULL, 'Kusnadi Nata', NULL, '083805427248', NULL, NULL, NULL, NULL, NULL, NULL),
(21, 129, NULL, NULL, NULL, NULL, NULL, NULL, 'Andi Sutandi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 130, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 131, NULL, NULL, NULL, NULL, NULL, NULL, 'U. ISKANDAR', NULL, '083127236843', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 132, NULL, NULL, NULL, NULL, NULL, NULL, 'Ilan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 133, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 134, NULL, NULL, NULL, NULL, NULL, NULL, 'UBAIDILLAH', NULL, '088212398287', NULL, NULL, NULL, NULL, NULL, NULL),
(27, 135, NULL, NULL, NULL, NULL, NULL, NULL, 'SAEPUDIN', NULL, '088212406327', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 136, NULL, NULL, NULL, NULL, NULL, NULL, 'Alm. Bubun N.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 137, NULL, NULL, NULL, NULL, NULL, NULL, 'Usman', NULL, '085779372869', NULL, NULL, NULL, NULL, NULL, NULL),
(30, 138, NULL, NULL, NULL, NULL, NULL, NULL, 'ANWARUDIN', NULL, '0859102814726', NULL, NULL, NULL, NULL, NULL, NULL),
(31, 139, NULL, NULL, NULL, NULL, NULL, NULL, 'Dede', NULL, '085723674470', NULL, NULL, NULL, NULL, NULL, NULL),
(32, 140, NULL, NULL, NULL, NULL, NULL, NULL, 'ENANG', NULL, '085710951742', NULL, NULL, NULL, NULL, NULL, NULL),
(33, 141, NULL, NULL, NULL, NULL, NULL, NULL, 'Dadang Mulyana', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 142, NULL, NULL, NULL, NULL, NULL, NULL, 'Nandang', NULL, '083160271051', NULL, NULL, NULL, NULL, NULL, NULL),
(35, 143, NULL, NULL, NULL, NULL, NULL, NULL, 'Andi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 144, NULL, NULL, NULL, NULL, NULL, NULL, 'Romi Suheri', NULL, '08872090763', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 145, NULL, NULL, NULL, NULL, NULL, NULL, 'Amar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 146, NULL, NULL, NULL, NULL, NULL, NULL, 'Ajat Munajat', NULL, '081291799420', NULL, NULL, NULL, NULL, NULL, NULL),
(39, 147, NULL, NULL, NULL, NULL, NULL, NULL, 'EMAN', NULL, '088212406339', NULL, NULL, NULL, NULL, NULL, NULL),
(40, 148, NULL, NULL, NULL, NULL, NULL, NULL, 'Daepi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 149, NULL, NULL, NULL, NULL, NULL, NULL, 'IJI', NULL, '083805213553', NULL, NULL, NULL, NULL, NULL, NULL),
(42, 150, NULL, NULL, NULL, NULL, NULL, NULL, 'NANA SURYANA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 151, NULL, NULL, NULL, NULL, NULL, NULL, 'ENDIN SALAHUDIN', NULL, '088212406356', NULL, NULL, NULL, NULL, NULL, NULL),
(44, 152, NULL, NULL, NULL, NULL, NULL, NULL, 'EMEY HABUDIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 153, NULL, NULL, NULL, NULL, NULL, NULL, 'LILI SUTERLY', NULL, '085624117484', NULL, NULL, NULL, NULL, NULL, NULL),
(46, 154, NULL, NULL, NULL, NULL, NULL, NULL, 'HOKI RIKIANTO', NULL, '085863315777', NULL, NULL, NULL, NULL, NULL, NULL),
(47, 155, NULL, NULL, NULL, NULL, NULL, NULL, 'ASEP KHAIDAR', NULL, '085782659003', NULL, NULL, NULL, NULL, NULL, NULL),
(48, 156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 157, NULL, NULL, NULL, NULL, NULL, NULL, 'HAKIM MUDIN', NULL, '088212406373', NULL, NULL, NULL, NULL, NULL, NULL),
(50, 158, NULL, NULL, NULL, NULL, NULL, NULL, 'NURHADI', NULL, '085723230926', NULL, NULL, NULL, NULL, NULL, NULL),
(51, 159, NULL, NULL, NULL, NULL, NULL, NULL, 'Suganda', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 160, NULL, NULL, NULL, NULL, NULL, NULL, 'Heri Husaheri', NULL, '081546276036', NULL, NULL, NULL, NULL, NULL, NULL),
(53, 161, NULL, NULL, NULL, NULL, NULL, NULL, 'Ayi Suherdin', NULL, '085798471151', NULL, NULL, NULL, NULL, NULL, NULL),
(54, 162, NULL, NULL, NULL, NULL, NULL, NULL, 'SARIPUDIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 163, NULL, NULL, NULL, NULL, NULL, NULL, 'Imang Sudirman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 164, NULL, NULL, NULL, NULL, NULL, NULL, 'Maman Suparman', NULL, '085863424761', NULL, NULL, NULL, NULL, NULL, NULL),
(57, 165, NULL, NULL, NULL, NULL, NULL, NULL, 'ENCING', NULL, '083819825618', NULL, NULL, NULL, NULL, NULL, NULL),
(58, 166, NULL, NULL, NULL, NULL, NULL, NULL, 'DOMI NADORI', NULL, '085862929295', NULL, NULL, NULL, NULL, NULL, NULL),
(59, 167, NULL, NULL, NULL, NULL, NULL, NULL, 'ABDURRAHMAN', NULL, '081386354440', NULL, NULL, NULL, NULL, NULL, NULL),
(60, 168, NULL, NULL, NULL, NULL, NULL, NULL, 'ZAENAL ABIDIN', NULL, '085774826662', NULL, NULL, NULL, NULL, NULL, NULL),
(61, 169, NULL, NULL, NULL, NULL, NULL, NULL, 'Ajhari Salam', NULL, '085722163114', NULL, NULL, NULL, NULL, NULL, NULL),
(62, 170, NULL, NULL, NULL, NULL, NULL, NULL, 'JOKO WAHYONO', NULL, '085798136374', NULL, NULL, NULL, NULL, NULL, NULL),
(63, 171, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 172, NULL, NULL, NULL, NULL, NULL, NULL, 'AMIN S', NULL, '085721008891', NULL, NULL, NULL, NULL, NULL, NULL),
(65, 173, NULL, NULL, NULL, NULL, NULL, NULL, 'Lalan Suparlan', NULL, '085692308561', NULL, NULL, NULL, NULL, NULL, NULL),
(66, 174, NULL, NULL, NULL, NULL, NULL, NULL, 'YUSUF HIDAYATULLAH', NULL, '088212406404', NULL, NULL, NULL, NULL, NULL, NULL),
(67, 175, NULL, NULL, NULL, NULL, NULL, NULL, 'KOMARUDIN', NULL, '083806813659', NULL, NULL, NULL, NULL, NULL, NULL),
(68, 176, NULL, NULL, NULL, NULL, NULL, NULL, 'OPIYANI', NULL, '0859102810549', NULL, NULL, NULL, NULL, NULL, NULL),
(69, 177, NULL, NULL, NULL, NULL, NULL, NULL, 'NURDIN', NULL, '081211437966', NULL, NULL, NULL, NULL, NULL, NULL),
(70, 178, NULL, NULL, NULL, NULL, NULL, NULL, 'DEPI PURNAMA', NULL, '08811452085', NULL, NULL, NULL, NULL, NULL, NULL),
(71, 179, NULL, NULL, NULL, NULL, NULL, NULL, 'Dadan Samozni', NULL, '081281174625', NULL, NULL, NULL, NULL, NULL, NULL),
(72, 180, NULL, NULL, NULL, NULL, NULL, NULL, 'ECEP', NULL, '088212473659', NULL, NULL, NULL, NULL, NULL, NULL),
(73, 181, NULL, NULL, NULL, NULL, NULL, NULL, 'Kuseri', NULL, '088211789549', NULL, NULL, NULL, NULL, NULL, NULL),
(74, 182, NULL, NULL, NULL, NULL, NULL, NULL, 'Jakaria Supitrah', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 183, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 184, NULL, NULL, NULL, NULL, NULL, NULL, 'Mamad', NULL, '085861768433', NULL, NULL, NULL, NULL, NULL, NULL),
(77, 185, NULL, NULL, NULL, NULL, NULL, NULL, 'AMAT', NULL, '083811975731', NULL, NULL, NULL, NULL, NULL, NULL),
(78, 186, NULL, NULL, NULL, NULL, NULL, NULL, 'Judin', NULL, '083891071168', NULL, NULL, NULL, NULL, NULL, NULL),
(79, 187, NULL, NULL, NULL, NULL, NULL, NULL, 'Edi Junaedi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 188, NULL, NULL, NULL, NULL, NULL, NULL, 'Taufik Akmal', NULL, '083805509558', NULL, NULL, NULL, NULL, NULL, NULL),
(81, 189, NULL, NULL, NULL, NULL, NULL, NULL, 'KOMARUDIN', NULL, '085863246022', NULL, NULL, NULL, NULL, NULL, NULL),
(82, 190, NULL, NULL, NULL, NULL, NULL, NULL, 'PEPEN SUPENDI', NULL, '088212406259', NULL, NULL, NULL, NULL, NULL, NULL),
(83, 191, NULL, NULL, NULL, NULL, NULL, NULL, 'ATIN PRATAMA', NULL, '088212473669', NULL, NULL, NULL, NULL, NULL, NULL),
(84, 192, NULL, NULL, NULL, NULL, NULL, NULL, 'Jiji', NULL, '083872942775', NULL, NULL, NULL, NULL, NULL, NULL),
(85, 193, NULL, NULL, NULL, NULL, NULL, NULL, 'EMUY MURDANI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 194, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 195, NULL, NULL, NULL, NULL, NULL, NULL, 'Agus Somantri', NULL, '085798372601', NULL, NULL, NULL, NULL, NULL, NULL),
(88, 196, NULL, NULL, NULL, NULL, NULL, NULL, 'HERMAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 197, NULL, NULL, NULL, NULL, NULL, NULL, 'AHMAD TOTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 198, NULL, NULL, NULL, NULL, NULL, NULL, 'DADANG NR', NULL, '085720992274', NULL, NULL, NULL, NULL, NULL, NULL),
(91, 199, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '085724139578', NULL, NULL, NULL, NULL, NULL, NULL),
(92, 200, NULL, NULL, NULL, NULL, NULL, NULL, 'Enjang Hermawan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 201, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 202, NULL, NULL, NULL, NULL, NULL, NULL, 'UCI SANUSI', NULL, '088212473687', NULL, NULL, NULL, NULL, NULL, NULL),
(95, 203, NULL, NULL, NULL, NULL, NULL, NULL, 'ADI SUCIPTO', NULL, '089518828839', NULL, NULL, NULL, NULL, NULL, NULL),
(96, 204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 205, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 206, NULL, NULL, NULL, NULL, NULL, NULL, 'AHMAD SYUKUR', NULL, '085722113611', NULL, NULL, NULL, NULL, NULL, NULL),
(99, 207, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 208, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 209, NULL, NULL, NULL, NULL, NULL, NULL, 'ASEP SAEPURROHMAN', NULL, '088210853409', NULL, NULL, NULL, NULL, NULL, NULL),
(102, 210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 211, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 212, NULL, NULL, NULL, NULL, NULL, NULL, 'MISBAH RUSMANA', NULL, '082113714709', NULL, NULL, NULL, NULL, NULL, NULL),
(105, 213, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 214, NULL, NULL, NULL, NULL, NULL, NULL, 'Iwan', NULL, '083823083989', NULL, NULL, NULL, NULL, NULL, NULL),
(107, 215, NULL, NULL, NULL, NULL, NULL, NULL, 'DEDE SETIAWAN', NULL, '083805848159', NULL, NULL, NULL, NULL, NULL, NULL),
(108, 216, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '085736823765', NULL, NULL, NULL, NULL, NULL, NULL),
(109, 217, NULL, NULL, NULL, NULL, NULL, NULL, 'Iwan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 218, NULL, NULL, NULL, NULL, NULL, NULL, 'ADE BUDI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 219, NULL, NULL, NULL, NULL, NULL, NULL, 'Sumarja', NULL, '085716587598', NULL, NULL, NULL, NULL, NULL, NULL),
(112, 220, NULL, NULL, NULL, NULL, NULL, NULL, 'ISAK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 221, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 222, NULL, NULL, NULL, NULL, NULL, NULL, 'Ujang Juarna', NULL, '081288383722', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ref_profil_lulusan`
--

CREATE TABLE `ref_profil_lulusan` (
  `id_profil` int(11) NOT NULL,
  `nama_dimensi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ref_profil_lulusan`
--

INSERT INTO `ref_profil_lulusan` (`id_profil`, `nama_dimensi`, `deskripsi`) VALUES
(1, 'Keimanan & Ketakwaan', 'Memiliki landasan spiritual yang kuat, akhlak mulia, dan nilai-nilai luhur.'),
(2, 'Kewargaan', 'Memahami tanggung jawab sebagai warga negara dan masyarakat, serta peduli kebangsaan.'),
(3, 'Penalaran Kritis', 'Mampu menganalisis informasi, berpikir logis, dan memecahkan masalah secara efektif.'),
(4, 'Kreativitas', 'Mampu menghasilkan ide baru, solusi inovatif, dan daya cipta yang tinggi.'),
(5, 'Kolaborasi', 'Mampu bekerja sama dalam tim, berbagi ide, dan berinteraksi secara positif.'),
(6, 'Kemandirian', 'Mampu mengambil inisiatif, bertanggung jawab atas pembelajaran, dan mengatasi hambatan sendiri.'),
(7, 'Kesehatan', 'Menjaga kesehatan fisik dan mental (bugar, sehat, seimbang) untuk kesejahteraan lahir batin.'),
(8, 'Komunikasi', 'Mampu menyampaikan ide, gagasan, dan informasi secara efektif melalui berbagai media.');

-- --------------------------------------------------------

--
-- Table structure for table `ref_surah`
--

CREATE TABLE `ref_surah` (
  `id_surah` int(11) NOT NULL,
  `nama_surah` varchar(50) DEFAULT NULL,
  `juz` int(11) DEFAULT NULL,
  `jumlah_ayat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ref_surah`
--

INSERT INTO `ref_surah` (`id_surah`, `nama_surah`, `juz`, `jumlah_ayat`) VALUES
(1, 'Al-Fatihah', 1, 7),
(2, 'Al-Baqarah', 1, 286),
(3, 'Ali Imran', 3, 200),
(4, 'An-Nisa', 4, 176),
(5, 'Al-Maidah', 6, 120),
(6, 'Al-Anam', 7, 165),
(7, 'Al-Araf', 8, 206),
(8, 'Al-Anfal', 9, 75),
(9, 'At-Taubah', 10, 129),
(10, 'Yunus', 11, 109),
(11, 'Hud', 11, 123),
(12, 'Yusuf', 12, 111),
(13, 'Ar-Rad', 13, 43),
(14, 'Ibrahim', 13, 52),
(15, 'Al-Hijr', 14, 99),
(16, 'An-Nahl', 14, 128),
(17, 'Al-Isra', 15, 111),
(18, 'Al-Kahf', 15, 110),
(19, 'Maryam', 16, 98),
(20, 'Ta-Ha', 16, 135),
(21, 'Al-Anbiya', 17, 112),
(22, 'Al-Hajj', 17, 78),
(23, 'Al-Muminun', 18, 118),
(24, 'An-Nur', 18, 64),
(25, 'Al-Furqan', 18, 77),
(26, 'Asy-Syuara', 19, 227),
(27, 'An-Naml', 19, 93),
(28, 'Al-Qasas', 20, 88),
(29, 'Al-Ankabut', 20, 69),
(30, 'Ar-Rum', 21, 60),
(31, 'Luqman', 21, 34),
(32, 'As-Sajdah', 21, 30),
(33, 'Al-Ahzab', 21, 73),
(34, 'Saba', 22, 54),
(35, 'Fatir', 22, 45),
(36, 'Ya-Sin', 22, 83),
(37, 'As-Saffat', 23, 182),
(38, 'Sad', 23, 88),
(39, 'Az-Zumar', 23, 75),
(40, 'Ghafir', 24, 85),
(41, 'Fussilat', 24, 54),
(42, 'Asy-Syura', 25, 53),
(43, 'Az-Zukhruf', 25, 89),
(44, 'Ad-Dukhan', 25, 59),
(45, 'Al-Jasiyah', 25, 37),
(46, 'Al-Ahqaf', 26, 35),
(47, 'Muhammad', 26, 38),
(48, 'Al-Fath', 26, 29),
(49, 'Al-Hujurat', 26, 18),
(50, 'Qaf', 26, 45),
(51, 'Az-Zariyat', 26, 60),
(52, 'At-Tur', 27, 49),
(53, 'An-Najm', 27, 62),
(54, 'Al-Qamar', 27, 55),
(55, 'Ar-Rahman', 27, 78),
(56, 'Al-Waqiah', 27, 96),
(57, 'Al-Hadid', 27, 29),
(58, 'Al-Mujadilah', 28, 22),
(59, 'Al-Hasyr', 28, 24),
(60, 'Al-Mumtahanah', 28, 13),
(61, 'As-Saff', 28, 14),
(62, 'Al-Jumuah', 28, 11),
(63, 'Al-Munafiqun', 28, 11),
(64, 'At-Taghabun', 28, 18),
(65, 'At-Talaq', 28, 12),
(66, 'At-Tahrim', 28, 12),
(67, 'Al-Mulk', 29, 30),
(68, 'Al-Qalam', 29, 52),
(69, 'Al-Haqqah', 29, 52),
(70, 'Al-Maarij', 29, 44),
(71, 'Nuh', 29, 28),
(72, 'Al-Jinn', 29, 28),
(73, 'Al-Muzzammil', 29, 20),
(74, 'Al-Muddassir', 29, 56),
(75, 'Al-Qiyamah', 29, 40),
(76, 'Al-Insan', 29, 31),
(77, 'Al-Mursalat', 29, 50),
(78, 'An-Naba', 30, 40),
(79, 'An-Naziat', 30, 46),
(80, 'Abasa', 30, 42),
(81, 'At-Takwir', 30, 29),
(82, 'Al-Infitar', 30, 19),
(83, 'Al-Mutaffifin', 30, 36),
(84, 'Al-Insyiqaq', 30, 25),
(85, 'Al-Buruj', 30, 22),
(86, 'At-Tariq', 30, 17),
(87, 'Al-Ala', 30, 19),
(88, 'Al-Ghasyiyah', 30, 26),
(89, 'Al-Fajar', 30, 30),
(90, 'Al-Balad', 30, 20),
(91, 'Asy-Syams', 30, 15),
(92, 'Al-Lail', 30, 21),
(93, 'Ad-Duha', 30, 11),
(94, 'Al-Insyirah', 30, 8),
(95, 'At-Tin', 30, 8),
(96, 'Al-Alaq', 30, 19),
(97, 'Al-Qadr', 30, 5),
(98, 'Al-Bayyinah', 30, 8),
(99, 'Az-Zalzalah', 30, 8),
(100, 'Al-Adiyat', 30, 11),
(101, 'Al-Qariahs', 30, 11),
(102, 'At-Takasur', 30, 8),
(103, 'Al-Asr', 30, 3),
(104, 'Al-Humazah', 30, 9),
(105, 'Al-Fil', 30, 5),
(106, 'Quraisy', 30, 4),
(107, 'Al-Maun', 30, 7),
(108, 'Al-Kautsar', 30, 3),
(109, 'Al-Kafirun', 30, 6),
(110, 'An-Nasr', 30, 3),
(111, 'Al-Lahab', 30, 5),
(112, 'Al-Ikhlas', 30, 4),
(113, 'Al-Falaq', 30, 5),
(114, 'An-Nas', 30, 6);

-- --------------------------------------------------------

--
-- Table structure for table `rekap_presensi_pembiasaan`
--

CREATE TABLE `rekap_presensi_pembiasaan` (
  `id_rekap` int(11) NOT NULL,
  `id_pembiasaan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `bulan` int(2) NOT NULL,
  `tahun` int(4) NOT NULL,
  `jml_H` int(11) DEFAULT 0,
  `jml_S` int(11) DEFAULT 0,
  `jml_I` int(11) DEFAULT 0,
  `jml_A` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setoran_tahfidz`
--

CREATE TABLE `setoran_tahfidz` (
  `id_setoran` int(11) NOT NULL,
  `id_tahfidz` int(11) DEFAULT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jenis_setoran` enum('Harian','Ujian') DEFAULT 'Harian',
  `id_surah` int(11) DEFAULT NULL,
  `ayat_awal` int(11) DEFAULT NULL,
  `ayat_akhir` int(11) DEFAULT NULL,
  `nilai` varchar(5) DEFAULT NULL,
  `nilai_hafal` char(1) DEFAULT NULL,
  `nilai_tajwid` char(1) DEFAULT NULL,
  `nilai_makhroj` char(1) DEFAULT NULL,
  `nilai_naghom` char(1) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `catatan_guru` text DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nipd` varchar(20) DEFAULT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `jk` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `sekolah_asal` varchar(100) DEFAULT NULL,
  `status_aktif` enum('Aktif','Lulus','Keluar') DEFAULT 'Aktif',
  `id_ta_masuk` int(11) DEFAULT 1,
  `id_pengguna` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nama`, `nisn`, `nipd`, `nik`, `jk`, `tempat_lahir`, `tanggal_lahir`, `sekolah_asal`, `status_aktif`, `id_ta_masuk`, `id_pengguna`) VALUES
(2, 'ARIA MAULANA MALIK IBRAHIM', '0077094906', '232410004', '3202121407070001', 'Laki-laki', 'Sukabumi', '2007-07-14', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(3, 'DAPID SUNARYA', '0074180364', '232410005', '3202122011070004', 'Laki-laki', 'Sukabumi', '2007-11-20', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(4, 'E. WILDA SASCIA', '0078870656', '232410009', '3202125801070004', 'Perempuan', 'Sukabumi', '2007-01-18', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(5, 'GANJAR', '0079012530', '232410012', '3202120604070002', 'Laki-laki', 'Sukabumi', '2007-04-06', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(6, 'GEBI', '0087946902', '232410013', '3202125605080005', 'Perempuan', 'Sukabumi', '2008-05-16', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(7, 'IBRAHIM NURMAN', '0077474330', '232410014', '3202122802070001', 'Laki-laki', 'Sukabumi', '2007-02-28', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(8, 'KHOIRUNNISA MUTMAINAH', '0084707942', '232410017', '3202125501080003', 'Perempuan', 'Sukabumi', '2008-01-15', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(9, 'M. ALIP MAULA PH', '0082376721', '232410018', '3202121803080006', 'Laki-laki', 'Sukabumi', '2008-03-18', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(11, 'MILA RAHMAWATI', '0075218331', '232410021', '3202124305070002', 'Perempuan', 'Sukabumi', '2007-05-03', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(12, 'MUHAMAD ILHAM MAULANA FIRDAUS', '0079232064', '232410023', '3202121205070001', 'Laki-laki', 'Sukabumi', '2007-05-12', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(13, 'MUHAMAD MUTTAHID AL FAHMI', '3071474401', '232410024', '3202292212070003', 'Laki-laki', 'Sukabumi', '2007-12-22', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(14, 'NAJILAH', '0075068120', '232410028', '3202126809070001', 'Perempuan', 'Sukabumi', '2007-09-28', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(16, 'RAZ AGIS LEOBOY', '0074047194', '232410033', '3202120408070001', 'Laki-laki', 'Sukabumi', '2007-08-04', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(17, 'RD. RAZZY MUSLIM EL KHURASANI', '3082221437', '232410034', '3202350501080001', 'Laki-laki', 'Sukabumi', '2008-05-01', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(19, 'SALMAN ALFANDY', '0086922138', '232410037', '3202120705080006', 'Laki-laki', 'Sukabumi', '2008-07-05', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(20, 'SITI ZAHWA NUR ALIPIA', '0079508802', '232410040', '3202126207070002', 'Perempuan', 'Sukabumi', '2008-10-07', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(21, 'WISMA', '0073480580', '232410044', '3202126003070002', 'Perempuan', 'Sukabumi', '2008-08-03', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(22, 'YULIA SRI PURNAMA', '0077907101', '232410046', '3202126708070003', 'Perempuan', 'Sukabumi', '2009-03-08', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(23, 'AGISTA LUBIS WULAN SAFITRI', '0071617628', '232410002', '3202115205070005', 'Perempuan', 'Sukabumi', '2007-05-12', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(24, 'AI S', '0074510508', '232410003', '3202126010070005', 'Perempuan', 'Sukabumi', '2007-10-20', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(25, 'DESI SRI MULYANI', '0075149326', '232410006', '3202124209070001', 'Perempuan', 'Sukabumi', '2007-09-02', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(26, 'DEWI YANTI', '0087756464', '232410007', '3202314905080004', 'Perempuan', 'Sukabumi', '2008-05-09', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(27, 'DINI AMINARTI', '0076971564', '232410008', '3202125408080002', 'Perempuan', 'Sukabumi', '2008-08-14', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(28, 'FAHRI RAMADANI', '0073421831', '232410010', '3202131809070001', 'Laki-laki', 'Sukabumi', '2007-09-18', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(29, 'FAZLA TUNNISA', '0082470293', '232410011', '3202117004080003', 'Perempuan', 'Sukabumi', '2008-04-30', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(30, 'INTAN PUSPITASARI', '3083209489', '232410015', '3202116504080001', 'Perempuan', 'Sukabumi', '2008-04-25', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(31, 'JALIYAH DWI HANDAYANI', '0087192340', '232410016', '3202115605080009', 'Perempuan', 'Sukabumi', '2008-05-16', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(32, 'MAULANA', '0074839999', '232410020', '3202111111070001', 'Laki-laki', 'Sukabumi', '2007-11-11', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(33, 'MOCH ZAKI ABDUL LATIP', '0084190554', '232410022', '3202111501090003', 'Laki-laki', 'Sukabumi', '2009-01-15', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(34, 'MUHAMAD IRFAN ABDILAH', '0075645495', '232410025', '3202112110070003', 'Laki-laki', 'Sukabumi', '2007-10-21', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(35, 'MUHAMMAD ABDUL FATAH', '0075704503', '232410026', '3202281003070001', 'Laki-laki', 'Sukabumi', '2007-03-10', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(36, 'MUHAMMAD GOLIB MURSIDI', '0078698570', '232410027', '3202161306080002', 'Laki-laki', 'Bogor', '2008-06-13', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(37, 'NENG SITI NURANI', '0079468673', '232410030', '3202314806070001', 'Perempuan', 'Sukabumi', '2007-06-08', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(38, 'RAISA RAHIM', '0075088413', '232410031', '3202116712070006', 'Perempuan', 'Sukabumi', '2007-12-27', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(39, 'RAMLAN', '0066571136', '232410032', '3202121709060001', 'Laki-laki', 'Sukabumi', '2006-09-17', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(40, 'REVA AYUNINGTIAS PUTRI EKA PURNAMA', '0086659475', '232410035', '3202117004080001', 'Perempuan', 'Sukabumi', '2008-04-30', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(41, 'SIPA KODARIAH', '0072957846', '232410038', '3202116408070004', 'Perempuan', 'Sukabumi', '2007-08-24', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(42, 'SITI ANISA', '0078493287', '232410039', '3202124609070003', 'Perempuan', 'Sukabumi', '2007-08-06', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(43, 'SURYA MANDALA PUTRA N', '0052746541', '232410041', '3202110106050005', 'Laki-laki', 'Sukabumi', '2005-06-01', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(45, 'TRIA AMANDA', '0067617773', '232410043', '3202112111060005', 'Perempuan', 'Sukabumi', '2006-11-21', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(46, 'YULIA RAHMA', '0077748064', '232410045', '3202116107070002', 'Perempuan', 'Sukabumi', '2007-07-11', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(47, 'ZAHRA TUNISYA', '0086839494', '232410047', '3202115408080005', 'Perempuan', 'Sukabumi', '2008-08-14', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(48, 'ABDUL AZIS', '0092498029', '242510001', '3202120908090001', 'Laki-laki', 'Sukabumi', '2009-08-09', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(49, 'ARIS RAMADAN', '0083876190', '242510003', '3202122109080003', 'Laki-laki', 'Sukabumi', '2008-09-21', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(50, 'ARISA SITI NURAZIZAH', '0085708361', '242510004', '3202126105080004', 'Perempuan', 'Sukabumi', '2008-05-21', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(51, 'DEDE RAHMAN MAULANA', '0081834479', '242510005', '3202120904080003', 'Laki-laki', 'Sukabumi', '2008-04-09', '', 'Aktif', 1, NULL),
(52, 'DETI RAHMAWATI', '0059591489', '242510043', '3202126006090001', 'Perempuan', 'Sukabumi', '2009-06-20', ' ', 'Aktif', 1, NULL),
(54, 'KARINA ADISTI', '0093496886', '242510012', '3202125401090004', 'Perempuan', 'Sukabumi', '2009-01-14', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(56, 'MUHAMAD ALI RAMDAN', '0081992941', '242510016', '3202122707080001', 'Laki-laki', 'Sukabumi', '2008-07-27', 'SMP WIDYA PRAJA CIJALINGAN', 'Aktif', 1, NULL),
(57, 'MUHAMAD RIFAL', '0087242400', '242510018', '3202111609080007', 'Laki-laki', 'Sukabumi', '2008-09-16', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(58, 'MUHAMMAD RAIHAN NAFIS', '0072292310', '242510042', '3202121506070004', 'Laki-laki', 'Sukabumi', '2007-06-15', 'MA AS-SADAH', 'Aktif', 1, NULL),
(59, 'NENG MIRNASARI', '0094555806', '242510022', '3202126604090001', 'Perempuan', 'Sukabumi', '2009-04-26', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(60, 'NENTI NOVIANTI', '0091768272', '242510024', '3202126311090002', 'Perempuan', 'Sukabumi', '2008-11-23', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(61, 'PIRESTA PUTRI CALISHA', '0092512424', '242510025', '3202126603090002', 'Perempuan', 'Sukabumi', '2009-03-26', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(62, 'PUTRI TRISMAYANTI', '0093514811', '242510026', '3202126805090001', 'Perempuan', 'Sukabumi', '2009-05-28', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(63, 'Ranti Sari Dewi', '0098156005', '242510027', '3202126706090001', 'Perempuan', 'Sukabumi', '2009-06-27', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(64, 'RIFKI HIDAYATULLAH', '0088477308', '242510028', '3202121808080006', 'Laki-laki', 'Sukabumi', '2009-06-08', 'SMP WIDYA PRAJA CIJALINGAN', 'Aktif', 1, NULL),
(65, 'RINDIYANI', '0099745057', '242510029', '3202126803090004', 'Perempuan', 'Sukabumi', '2011-04-03', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(66, 'RIYA RISMA APRIYANA', '0099762530', '242510030', '3202126404090002', 'Perempuan', 'Sukabumi', '2010-12-04', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(67, 'SAHRIL GUNAWAN', '0087331982', '242510032', '3202120103080001', 'Laki-laki', 'Sukabumi', '2008-01-03', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(68, 'SINTA YUSNIANTI', '0086490824', '242510033', '3202126212080001', 'Perempuan', 'Sukabumi', '2009-10-12', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(69, 'SISKA AULIA SAPITRI', '0096038879', '242510034', '3202126008080002', 'Perempuan', 'Sukabumi', '2010-08-08', 'SMP PGRI 2 NAGRAK', 'Aktif', 1, NULL),
(70, 'SITI HARDIYANI', '0082080578', '242510035', '3202126305080004', 'Perempuan', 'Sukabumi', '2009-11-05', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(71, 'YOHANA', '0083031783', '242510040', '3202126311080003', 'Perempuan', 'Sukabumi', '2009-11-11', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(72, 'ZAKARIA', '0081221124', '242510041', '3202121308080001', 'Laki-laki', 'Sukabumi', '2009-01-08', 'SMP IT DARUL IBTIDA', 'Aktif', 1, NULL),
(73, 'AHMAD MUHLIS RIDHO', '0088991177', '242510002', '3201031604080008', 'Laki-laki', 'Bogor', '2008-04-16', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(74, 'DELPA', '0098717497', '242510006', '3202125906090003', 'Perempuan', 'Sukabumi', '2009-06-19', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(75, 'DEWI MILANI', '0084818899', '242510007', '3202115005080006', 'Perempuan', 'Sukabumi', '2008-05-10', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(76, 'HASANUDIN', '0095429829', '242510008', '3202110904090005', 'Laki-laki', 'Sukabumi', '2009-04-09', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(77, 'INDRI', '0088085818', '242510010', '3202115603080007', 'Perempuan', 'Sukabumi', '2008-03-16', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(78, 'INTAN AWALIAH', '0084478014', '242510011', '3202115101080003', 'Perempuan', 'Sukabumi', '2008-03-13', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(79, 'M. RAFLI MAULANA', '0089188745', '242510014', '3202112910080002', 'Laki-laki', 'Sukabumi', '2008-10-29', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(80, 'MOCH. FACHRI PRATAMA', '0089648244', '242510015', '3202112005080007', 'Laki-laki', 'Sukabumi', '2008-05-20', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(81, 'MUHAMAD RAHMADHANI', '0086383084', '242510017', '3202111109080008', 'Laki-laki', 'Sukabumi', '2008-09-11', '', 'Aktif', 1, NULL),
(82, 'MUHAMMAD MISBAHUL MUNIR', '0096252896', '242510019', '3202116111370001', 'Laki-laki', 'Sukabumi', '2009-09-18', 'SMP PLUS AL-BASYARI', 'Aktif', 1, NULL),
(83, 'MUTIA RISMA AYU', '0088187857', '242510020', '3202135703080001', 'Perempuan', 'Sukabumi', '2008-03-17', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(84, 'Nana Firmansyah', '0081455898', '242510021', '3202111311080004', 'Laki-laki', 'Sukabumi', '2008-11-13', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(85, 'NENG SRI NURCAHYATI', '0081633820', '242510023', '3202116810080002', 'Perempuan', 'Sukabumi', '2008-10-28', 'SMP PLUS AL-BASYARI', 'Aktif', 1, NULL),
(86, 'SITI NURASITA', '0082924917', '242510036', '3202116508080003', 'Perempuan', 'Sukabumi', '2008-08-25', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(87, 'SITI NURLAELA', '0087595301', '242510037', '3202126101080008', 'Perempuan', 'Sukabumi', '2008-01-21', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(88, 'SITI SUWANSAH', '0075499469', '242510038', '3202115612070005', 'Perempuan', 'Sukabumi', '2007-12-16', 'SMP ISLAM NURUL FIKRI', 'Aktif', 1, NULL),
(89, 'SITI ZAHRA QURATULAINI', '0095122768', '242510039', '3202116506090001', 'Perempuan', 'Sukabumi', '2011-01-06', 'SMP PLUS AL-BASYARI', 'Aktif', 1, NULL),
(109, 'ABDUL MUIZ', '3092646133', '252610001', '3202132011090006', '', 'Sukabumi', '2009-11-20', NULL, 'Aktif', 5, NULL),
(110, 'ADRIAN MAULANA YUSUP', '0092931522', '252610002', '3202142711090003', '', 'Sukabumi', '2009-11-27', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(111, 'AGIS MUTIARA', '0095780821', '252610003', '3202126808090003', '', 'Sukabumi', '2009-08-28', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(112, 'AGUS RAMDANI', '0096593025', '252610004', '3202113108090004', '', 'Sukabumi', '2009-08-31', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(113, 'AIRA PUTRI ADITIYA', '0099755704', '252610005', '3202064108090002', '', 'Sukabumi', '2009-08-01', 'SMP NEGERI 2 BOJONGGENTENG', 'Aktif', 5, NULL),
(114, 'ALDI SAPUTRA', '0089675328', '252610006', '3202281511080001', '', 'Sukabumi', '2008-11-15', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(115, 'AMELDA', '0094447260', '252610007', '3202134607090001', '', 'Sukabumi', '2009-07-06', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(116, 'AMELIA', '0087663921', '252610008', '3202136312080007', '', 'Sukabumi', '2008-12-23', 'SMP ISLAM YPI PARUNGKUDA', 'Aktif', 5, NULL),
(117, 'ANDIKA MAULANA', '3091137244', '252610009', '3202132003090003', '', 'Sukabumi', '2009-03-20', 'SMPS ISLAM INSAN KAMIL MANDIRI', 'Aktif', 5, NULL),
(118, 'ANISA', '3086466602', '252610010', '3202146007080001', '', 'Sukabumi', '2008-07-20', NULL, 'Aktif', 5, NULL),
(119, 'ANISA PITRI', '0083380895', '252610011', '3202096009080001', '', 'Sukabumi', '2008-09-20', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(120, 'ANISA RAHMA MUSTIKA', '0086222783', '252610012', '3202136610080002', '', 'Sukabumi', '2008-10-26', 'SMP NEGERI 1 PARUNGKUDA', 'Aktif', 5, NULL),
(121, 'ANISA SAFITRI', '3101261006', '252610013', '3202146404100001', '', 'Sukabumi', '2010-04-24', NULL, 'Aktif', 5, NULL),
(122, 'ARYASATYA FIRMANSYAH', '0067504067', '252610014', '3202100105060004', '', 'Sukabumi', '2006-05-01', 'SMP NEGERI 2 CIKEMBAR', 'Aktif', 5, NULL),
(123, 'AULIA DEWI SRI WULANDARI', '3108366273', '252610015', '3202134204100002', '', 'Sukabumi', '2010-04-02', NULL, 'Aktif', 5, NULL),
(124, 'AWALIAH', '3106589031', '252610016', '3202136503100002', '', 'Sukabumi', '2010-03-25', NULL, 'Aktif', 5, NULL),
(125, 'BAYHAQI ALKAFARO', '0109599808', '252610017', '3202111605100007', '', 'Sukabumi', '2010-05-16', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(126, 'BINTANG FURQON', '          ', '252610018', '3301130612080006', '', 'Malang', '2006-12-06', NULL, 'Aktif', 5, NULL),
(127, 'DESTI ANJANI', '0056410894', '252610019', '3202145912050003', '', 'Sukabumi', '2005-12-19', NULL, 'Aktif', 5, NULL),
(128, 'DIKI AGUSTIAN', '0069503994', '252610020', '3202141608060002', '', 'Sukabumi', '2006-08-16', 'SMP NEGERI 1 PARAKANSALAK', 'Aktif', 5, NULL),
(129, 'DINI PUTRI ANDRIANI', '0097968668', '252610021', '3202145810090001', '', 'Sukabumi', '2009-10-18', NULL, 'Aktif', 5, NULL),
(130, 'EDWAR GUPRIYAN', '3089716573', '252610022', '3202141610080003', '', 'Sukabumi', '2008-10-16', NULL, 'Aktif', 5, NULL),
(131, 'ELIYA YULIANI', '0104684933', '252610023', '3202125806100003', '', 'Sukabumi', '2010-06-18', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(132, 'ELSI', '0082591669', '252610024', '3202146507080003', '', 'Sukabumi', '2008-07-25', NULL, 'Aktif', 5, NULL),
(133, 'FABIAN YUSUF', '3093879901', '252610025', '3202141909090002', '', 'Sukabumi', '2009-09-19', NULL, 'Aktif', 5, NULL),
(134, 'FADHIL ABDILLAH', '0107965418', '252610026', '3202110601100001', '', 'Sukabumi', '2010-01-06', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(135, 'FAIRUS MUTIARAHIM', '0107660632', '252610027', '3202115101100002', '', 'Sukabumi', '2010-01-11', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(136, 'FAUZIAH', '0082973002', '252610028', '3202136103080002', '', 'Sukabumi', '2008-03-21', 'SMP ISLAM AL QUDSIYAH', 'Aktif', 5, NULL),
(137, 'FERA JULIANTI', '0105238344', '252610029', '3202185007100004', '', 'Sukabumi', '2010-07-10', 'SMP NEGERI 2 KALAPANUNGGAL', 'Aktif', 5, NULL),
(138, 'GHEA ANANDA AVRIANTY', '0107833156', '252610030', '3202125404100003', '', 'Sukabumi', '2010-04-14', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(139, 'GRESIA SUMAROU', '0088748192', '252610031', '3202145605080003', '', 'Sukabumi', '2008-05-16', 'SMP ISLAM YPI PARUNGKUDA', 'Aktif', 5, NULL),
(140, 'HABIBAH', '0083788513', '252610032', '3202195006070001', '', 'Sukabumi', '2008-02-11', 'PKBM ANGGREK', 'Aktif', 5, NULL),
(141, 'HAIKAL GALIH MULYANA', '3108334256', '252610033', '3202061407100001', '', 'Sukabumi', '2010-07-14', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL),
(142, 'HERA IDA', '0085842308', '252610034', '3202124606080004', '', 'Sukabumi', '2008-06-06', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(143, 'HERDI', '0109278369', '252610035', '3202182601100001', '', 'Sukabumi', '2010-01-26', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL),
(144, 'HILDA MUTIARA ZULFA', '0099368150', '252610036', '3216216810090006', '', 'Bekasi', '2009-10-28', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(145, 'ICA', '0107143054', '252610037', '3202145003100001', '', 'Sukabumi', '2010-03-10', NULL, 'Aktif', 5, NULL),
(146, 'INDAH ANJANI', '0063676488', '252610038', '3202186812060001', '', 'Sukabumi', '2006-12-28', 'SMP ISLAM TERPADU AL - MUTAQIN', 'Aktif', 5, NULL),
(147, 'INDRI YULIANTI', '0099835840', '252610039', '3202117008090006', '', 'Sukabumi', '2009-08-30', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(148, 'INDRIYANI', '0095556290', '252610040', '3202144408090001', '', 'Sukabumi', '2009-08-04', NULL, 'Aktif', 5, NULL),
(149, 'IRMAN MAULANA', '0094272850', '252610041', '3202121210090002', '', 'Sukabumi', '2009-10-12', 'SMP ISLAM CENDIKIA', 'Aktif', 5, NULL),
(150, 'ISMA', '0108779297', '252610042', '3202124404100001', '', 'Sukabumi', '2010-04-04', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(151, 'JELITA SURYA SABRINA PUTRI', '0094510709', '252610043', '3202114512090002', '', 'Sukabumi', '2009-12-05', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(152, 'JIAN BAAMI HABTI', '3105275586', '252610044', '3202111801100002', '', 'Sukabumi', '2010-01-18', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(153, 'KASANDRA AQUINI', '0096216878', '252610045', '3202126607090001', '', 'Sukabumi', '2009-07-26', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(154, 'KESYA PUTRI NATAPLAWIRA', '0089846692', '252610046', '3202335605080001', '', 'Sukabumi', '2008-05-16', 'SMP N 1 KOTA SUKABUMI', 'Aktif', 5, NULL),
(155, 'LUSI WIDIA MAULIDA', '0097279667', '252610047', '3201275304080002', '', 'Bogor', '2009-03-13', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(156, 'LUTFI ALFIANTI', '3091820543', '252610048', '3202315111090001', '', 'Sukabumi', '2009-11-11', NULL, 'Aktif', 5, NULL),
(157, 'LUTHVIANI ULFA', '0097646759', '252610049', '3202117004090005', '', 'Sukabumi', '2009-04-30', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(158, 'M. DZUBIAN SYAFIQ ABDILLAH', '0099587391', '252610050', '3202130407090003', '', 'Sukabumi', '2009-07-04', 'SMP NEGERI 1 PARUNGKUDA', 'Aktif', 5, NULL),
(159, 'M. FAHRI SUGANDA', '0106286817', '252610051', '3202140205100001', '', 'Sukabumi', '2010-05-02', NULL, 'Aktif', 5, NULL),
(160, 'M. RIPAL ALHUSAERI', '0092517806', '252610052', '3202142701100001', '', 'Sukabumi', '2010-01-27', 'SMP ISLAM TERPADU AL - MUTAQIN', 'Aktif', 5, NULL),
(161, 'MAEDASARI', '0097809406', '252610053', '3202144812090001', '', 'Sukabumi', '2009-12-08', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(162, 'MARWAN SETIAWAN', '3094009694', '252610054', '3202272706090001', '', 'Sukabumi', '2009-06-27', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(163, 'MELISA APRILLIANI', '3101211123', '252610055', '3202146204100001', '', 'Sukabumi', '2010-04-22', 'SMP ISLAM YPI PARUNGKUDA', 'Aktif', 5, NULL),
(164, 'MOH. RIFKI RIZKY ARRAHMAN', '0093182092', '252610056', '3202142606090001', '', 'Sukabumi', '2009-06-26', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(165, 'MONA MUTIARA', '0103932240', '252610057', '3202124301100002', '', 'Sukabumi', '2010-01-03', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(166, 'MUCHAMMAD FAISAL', '0109697708', '252610058', '3202112101100009', '', 'Sukabumi', '2010-01-21', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(167, 'MUHAMMAD FATHURROHMAN', '0103248529', '252610059', '3201271901100002', '', 'Bogor', '2010-01-19', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(168, 'MUHAMMAD IBNU MUBAROK AZZEIN', '0119632593', '252610060', '3201273001110001', '', 'Bogor', '2011-01-30', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(169, 'MUHAMMAD RAFLI AL AZHARI', '3083264206', '252610061', '3202132908080003', '', 'Sukabumi', '2008-08-29', 'SMP NEGERI 2 PARUNGKUDA', 'Aktif', 5, NULL),
(170, 'MUHAMMAD RISKY', '0101275699', '252610062', '3202102302100008', '', 'Sukabumi', '2010-02-23', 'SMP NEGERI 2 CIKEMBAR', 'Aktif', 5, NULL),
(171, 'MUHAMMAD TUGRIL ARRAIHAN', '3091958637', '252610063', '3202122203090007', '', 'Sukabumi', '2009-03-22', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(172, 'MUHKLISIHIN', '0103596568', '252610064', '3202182803100003', '', 'Sukabumi', '2010-03-28', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL),
(173, 'MUTIARA LAILA PUTRI', '0105179179', '252610065', '3202144609100001', '', 'Sukabumi', '2010-09-06', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(174, 'NABILLAH MEGA FIKRIANI', '0098152876', '252610066', '3202116506090005', '', 'Sukabumi', '2009-06-25', 'SMP AZZAINIYYAH', 'Aktif', 5, NULL),
(175, 'NADIA MARDIANA', '0107573886', '252610067', '3202126107100002', '', 'Sukabumi', '2010-07-21', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(176, 'NAILA FITRI RAHMADHANI', '0095303568', '252610068', '3202125709090003', '', 'Sukabumi', '2009-09-17', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(177, 'NITA MAULANI', '0089102130', '252610069', '3202196403080002', '', 'Sukabumi', '2008-03-24', 'PKBM ANGGREK', 'Aktif', 5, NULL),
(178, 'NOVITA ILMIRA DWI PURNAMA', '0099066129', '252610070', '3202117011090003', '', 'Sukabumi', '2009-11-30', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(179, 'NUR WAHID SALIM', '0092492057', '252610071', '3202282209090003', '', 'Sukabumi', '2009-09-22', 'SMP IT MADANI', 'Aktif', 5, NULL),
(180, 'NURAENI', '0088250631', '252610072', '3202114306080005', '', 'Sukabumi', '2008-06-03', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(181, 'NURHAIFA', '0094887757', '252610073', '3202316011090002', '', 'Sukabumi', '2009-11-20', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(182, 'NURIL YAHDIK', '0093068546', '252610074', '3202121907090002', '', 'Sukabumi', '2009-07-19', NULL, 'Aktif', 5, NULL),
(183, 'NURUL AZMI', '0098346836', '252610075', '3202296309090001', '', 'Sukabumi', '2009-09-23', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(184, 'PAHMI AJIDIN', '0038078737', '252610076', '3202142202090002', '', 'Sukabumi', '2009-02-22', 'SMP ISLAM TERPADU AL - MUTAQIN', 'Aktif', 5, NULL),
(185, 'PAHRI RAMADHAN', '0099372913', '252610077', '3202143008090001', '', 'Sukabumi', '2009-08-30', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(186, 'PANDI', '0085457271', '252610078', '3202130802080003', '', 'Sukabumi', '2008-02-08', 'SMPS ISLAM INSAN KAMIL MANDIRI', 'Aktif', 5, NULL),
(187, 'PIONA ELDI OKTAVIA', '3086810012', '252610079', '3202134210080004', '', 'Sukabumi', '2008-10-02', 'SMP ISLAM YPI PARUNGKUDA', 'Aktif', 5, NULL),
(188, 'PUTRI AMELIA', '0087974126', '252610080', '3202136504080002', '', 'Sukabumi', '2008-04-25', 'SMP PGRI PARUNGKUDA', 'Aktif', 5, NULL),
(189, 'RAIHAN CAHYA MAULID', '0108770955', '252610081', '3202111103100001', '', 'Sukabumi', '2010-03-11', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(190, 'RANDI', '0109178444', '252610082', '3202113001100006', '', 'Sukabumi', '2010-01-30', 'SMP IT MADANI', 'Aktif', 5, NULL),
(191, 'RAPLI MAULANA', '0108047231', '252610083', '3202110201100004', '', 'Sukabumi', '2010-01-02', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(192, 'RASNAMILA', '0091861319', '252610084', '3202116008090004', '', 'Sukabumi', '2009-08-20', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(193, 'RATIH MAULIDA', '0103957465', '252610085', '3202146702100001', '', 'Sukabumi', '2010-02-27', NULL, 'Aktif', 5, NULL),
(194, 'REHAN NURJAELANI', '3096979240', '252610086', '3202130606090005', '', 'Sukabumi', '2009-06-06', NULL, 'Aktif', 5, NULL),
(195, 'REHAN SOMANTRI', '0092294359', '252610087', '3202121705090002', '', 'Sukabumi', '2009-05-17', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(196, 'RENDIYAWAN', '0102111932', '252610088', '3202290109100003', '', 'Sukabumi', '2010-09-01', NULL, 'Aktif', 5, NULL),
(197, 'RISA FITRIANI', '0094943147', '252610089', '3202146009090003', '', 'Sukabumi', '2009-09-20', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(198, 'RISMA JUNITA', '0093101241', '252610090', '3202145006090001', '', 'Sukabumi', '2009-06-10', 'SMP NEGERI 1 BOJONGGENTENG', 'Aktif', 5, NULL),
(199, 'ROBY ARDIANSYAH', '0095721154', '252610091', '3202131105090002', '', 'Sukabumi', '2009-05-11', 'SMP NEGERI 2 PARUNGKUDA', 'Aktif', 5, NULL),
(200, 'SAEPURROHIM KARIM', '3093399442', '252610092', '3202060709090002', '', 'Sukabumi', '2009-09-07', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL),
(201, 'SALMAN ALFARISI SYA`AR', '3092640096', '252610093', '3202141111090004', '', 'Sukabumi', '2009-11-11', NULL, 'Aktif', 5, NULL),
(202, 'SIFA SILFIANA', '0099813984', '252610094', '3202115805090001', '', 'Sukabumi', '2009-05-18', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(203, 'SILVIA WAVIQ RAMADHANI', '0082778203', '252610095', '3201254209080004', '', 'Bogor', '2008-09-02', 'SMP ISLAM AL BAROKAH', 'Aktif', 5, NULL),
(204, 'SITI FATIMAH AZ-ZAHRA', NULL, '252610096', NULL, '', 'Sukabumi', NULL, NULL, 'Aktif', 5, NULL),
(205, 'SITI MASNONEH', '3105548859', '252610097', '3202144202100001', '', 'Sukabumi', '2010-02-02', NULL, 'Aktif', 5, NULL),
(206, 'SITI MUNIFAH SIRIN', '0084999054', '252610098', '3202127005080003', '', 'Sukabumi', '2008-05-30', 'SMP IT TAHSIN', 'Aktif', 5, NULL),
(207, 'SITI NURHALISA', '0095433011', '252610099', '3202136006090005', '', 'Sukabumi', '2009-06-20', NULL, 'Aktif', 5, NULL),
(208, 'SITI PATIMAH', '0093589815', '252610100', '3202137006080004', '', 'Sukabumi', '2008-06-30', NULL, 'Aktif', 5, NULL),
(209, 'SITI PATIMAH', '0105415145', '252610101', '3202114503100007', '', 'Sukabumi', '2010-03-05', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(210, 'SITI SALMA', '0094288959', '252610102', '3202146411090004', '', 'Sukabumi', '2009-11-24', NULL, 'Aktif', 5, NULL),
(211, 'SITI SHOPIA ULFA', '3103432560', '252610103', '3202134405100001', '', 'Sukabumi', '2010-05-04', NULL, 'Aktif', 5, NULL),
(212, 'SITI SYARIFAH MARDHOTILAH', '0101013848', '252610104', '3201276704100001', '', 'Bogor', '2010-04-27', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(213, 'SRY MARLINA', '0087957830', '252610105', '3202134711080001', '', 'Sukabumi', '2008-11-07', ' ', 'Aktif', 5, NULL),
(214, 'SUSAN MEILANI', '0091536546', '252610106', '3202126305090006', '', 'Sukabumi', '2009-05-23', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(215, 'SYIFA RACHMAWATI AWALIYAH', '0109796338', '252610107', '3202125702100002', '', 'Sukabumi', '2010-02-17', 'SMP IT DARUL IBTIDA', 'Aktif', 5, NULL),
(216, 'WANGI', '3095898697', '252610108', '3202144412090003', '', 'Sukabumi', '2009-12-04', 'SMP ISLAM TERPADU AL JABHATUL ISLAMIYAH', 'Aktif', 5, NULL),
(217, 'WILDAN', '3094374335', '252610109', '3275012706090004', '', 'Bekasi', '2009-06-27', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL),
(218, 'WILDANSYAH DWI KUSUMA', '0105575590', '252610110', '3202142406100004', '', 'Sukabumi', '2010-06-24', NULL, 'Aktif', 5, NULL),
(219, 'WINDI RAMADANI', '0098044990', '252610111', '3202184804100001', '', 'Sukabumi', '2010-04-08', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL),
(220, 'YUNI', '0105322682', '252610112', '3202315604100001', '', 'Sukabumi', '2010-04-16', 'SMP ISLAM NURUL FIKRI', 'Aktif', 5, NULL),
(221, 'ZIDAN SYAHRIL ARYANSA', '3101276123', '252610113', '3202140501100001', '', 'Sukabumi', '2010-01-05', NULL, 'Aktif', 5, NULL),
(222, 'ZIRA PUSPITA', '0103534124', '252610114', '3202186706100001', '', 'Sukabumi', '2010-06-27', 'SMP BAET EL ANSHAR', 'Aktif', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `siswa_alumni`
--

CREATE TABLE `siswa_alumni` (
  `id_siswa` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nipd` varchar(20) DEFAULT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `jk` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `sekolah_asal` varchar(100) DEFAULT NULL,
  `status_aktif` enum('Aktif','Lulus','Keluar') DEFAULT 'Aktif',
  `id_ta_masuk` int(11) DEFAULT 1,
  `id_pengguna` int(11) DEFAULT NULL,
  `tahun_lulus` year(4) DEFAULT NULL,
  `id_kelas_akhir` int(11) DEFAULT 0,
  `id_ta_lulus` int(11) DEFAULT 0,
  `no_ijazah` varchar(100) DEFAULT NULL,
  `tgl_lulus` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_alumni`
--

INSERT INTO `siswa_alumni` (`id_siswa`, `nama`, `nisn`, `nipd`, `nik`, `jk`, `tempat_lahir`, `tanggal_lahir`, `sekolah_asal`, `status_aktif`, `id_ta_masuk`, `id_pengguna`, `tahun_lulus`, `id_kelas_akhir`, `id_ta_lulus`, `no_ijazah`, `tgl_lulus`) VALUES
(91, 'ALFI ALFIYAH', '0063016384', '222310001', '3202125206060002', 'Perempuan', 'Sukabumi', '2006-06-12', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(92, 'ALMA NURUL JULIA', '0074649271', '222310002', '3202276003070001', 'Perempuan', 'Sukabumi', '2007-03-20', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(93, 'ALWAN HAFID FAUZAN QORNI', '0066288001', '222310003', '3202121705060002', 'Laki-laki', 'Sukabumi', '2006-05-17', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(94, 'ASEP SAEPUL MAULANA', '0071134334', '222310004', '3202120207070002', 'Laki-laki', 'Sukabumi', '2007-07-02', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(95, 'DENDI', '0074029016', '222310005', '3202120402070006', 'Laki-laki', 'Sukabumi', '2007-02-04', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(96, 'IMA RAHMA KAMILAH', '0067026664', '222310006', '3202125312060002', 'Perempuan', 'Sukabumi', '2006-12-13', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(97, 'INDAH SILVIA ALVIANI', '0074135295', '222310007', '3202124705070005', 'Perempuan', 'irebon', '2007-05-08', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(98, 'IRMA NURMAYANTI', '0067646824', '222310008', '3202127105060001', 'Perempuan', 'Sukabumi', '2006-05-31', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(99, 'M. RAJIB SIDIK', '0065784488', '222310010', '3202122611060001', 'Laki-laki', 'Sukabumi', '2006-11-26', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(100, 'MUHAMMAD RAMDAN ASIDIK', '0061111768', '222310011', '3202122310060003', 'Laki-laki', 'Sukabumi', '2006-10-23', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(101, 'NANI YUNINGSIH', '0069460620', '222310012', '3202126906060003', 'Perempuan', 'Sukabumi', '2006-06-26', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(102, 'SANTI AMALIA', '0062077422', '222310013', '3202125206060005', 'Perempuan', 'Sukabumi', '2006-06-12', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(103, 'SINDI JULIANA PUTRI', '0077894298', '222310014', '3202125507070006', 'Perempuan', 'Sukabumi', '2007-07-15', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(104, 'SITI MARYAM', '0066077208', '222310015', '3202125809060001', 'Perempuan', 'Sukabumi', '2006-09-18', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(105, 'SITI NURANISA', '0066385637', '222310016', '3202125312060001', 'Perempuan', 'Sukabumi', '2006-12-13', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(106, 'SITI NURHALIZA', '0075718720', '222310017', '3202124103070001', 'Perempuan', 'Sukabumi', '2007-03-01', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(107, 'SOPI SOPIAH', '0067949525', '222310018', '3202125607060002', 'Perempuan', 'Sukabumi', '2008-04-07', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL),
(108, 'SYANIE RAYA JAMA KALIMATIN SYAWA', '0066110475', '222310019', '3202295712060003', 'Perempuan', 'Sukabumi', '2007-05-12', 'SMP IT DARUL IBTIDA', 'Lulus', 1, NULL, '2025', 14, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `siswa_mutasi`
--

CREATE TABLE `siswa_mutasi` (
  `id_siswa` int(11) NOT NULL,
  `id_kelas_asal` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nipd` varchar(20) DEFAULT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `jk` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `sekolah_asal` varchar(100) DEFAULT NULL,
  `status_aktif` enum('Aktif','Lulus','Keluar') DEFAULT 'Aktif',
  `id_ta_masuk` int(11) DEFAULT 1,
  `id_pengguna` int(11) DEFAULT NULL,
  `tgl_mutasi` date DEFAULT NULL,
  `alasan_mutasi` text DEFAULT NULL,
  `jenis_mutasi` varchar(50) DEFAULT NULL,
  `id_ta_mutasi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa_mutasi`
--

INSERT INTO `siswa_mutasi` (`id_siswa`, `id_kelas_asal`, `nama`, `nisn`, `nipd`, `nik`, `jk`, `tempat_lahir`, `tanggal_lahir`, `sekolah_asal`, `status_aktif`, `id_ta_masuk`, `id_pengguna`, `tgl_mutasi`, `alasan_mutasi`, `jenis_mutasi`, `id_ta_mutasi`) VALUES
(1, 7, 'ABDULLAH NASRUDIN', '0082899196', '232410001', '3202120408080002', 'Laki-laki', 'Sukabumi', '2008-08-04', 'SMP IT DARUL IBTIDA', 'Keluar', 1, NULL, '2024-01-08', 'Permintaan Orangtua', 'Keluar', 2),
(10, 7, 'M. FAISAL LUGIS', '', '232410019', '3202121509060005', 'Laki-laki', 'Sukabumi', '2006-09-15', 'SMP IT DARUL IBTIDA', 'Keluar', 1, NULL, '2024-03-12', 'Permintaan Orangtua', 'Keluar', 2),
(15, 1, 'NAUFAL FEBRYAN', '0083636697', '232410029', '3202121702080002', 'Laki-laki', 'Sukabumi', '2008-02-17', 'SMP IT DARUL IBTIDA', 'Keluar', 1, NULL, '2023-11-17', 'Permintaan Orangtua', 'Keluar', 1),
(18, 1, 'RIDWAN MAULANA SAPAAT', '0078278549', '232410036', '3202120408070002', 'Laki-laki', 'Sukabumi', '2007-04-08', 'SMP IT DARUL IBTIDA', 'Keluar', 1, NULL, '2023-10-12', 'Permintaan Orangtua', 'Keluar', 1),
(44, 6, 'TIARA SALMA', '0075988909', '232410042', '3202114512070007', 'Perempuan', 'Sukabumi', '2007-12-05', 'SMP ISLAM NURUL FIKRI', 'Keluar', 1, NULL, '2024-10-07', 'Permintaan Orangtua', 'Keluar', 3),
(53, 19, 'Hasbi Ali', '0082423497', '242510009', '3202110901080002', 'Laki-laki', 'Sukabumi', '2008-01-09', 'SMP PEMBANGUNAN', 'Keluar', 1, NULL, '2025-08-01', 'Permintaan Orangtua', 'Keluar', 5),
(55, 19, 'LISANA SHIDQIN ALIYYA', '0085591230', '242510013', '3202114902080001', 'Perempuan', 'Sukabumi', '2008-02-09', '', 'Keluar', 1, NULL, '2025-07-14', 'Permintaan Orangtua', 'Keluar', 5);

-- --------------------------------------------------------

--
-- Table structure for table `struktur_kurikulum`
--

CREATE TABLE `struktur_kurikulum` (
  `id_struktur` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `tingkat` varchar(10) NOT NULL,
  `kelompok` varchar(100) DEFAULT NULL,
  `alokasi_jp_minggu` int(11) NOT NULL,
  `id_ta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `struktur_kurikulum`
--

INSERT INTO `struktur_kurikulum` (`id_struktur`, `id_mapel`, `tingkat`, `kelompok`, `alokasi_jp_minggu`, `id_ta`) VALUES
(1, 1, 'X', 'Mata Pelajaran Wajib', 2, 2),
(2, 2, 'X', 'Mata Pelajaran Wajib', 2, 2),
(3, 3, 'X', 'Mata Pelajaran Wajib', 3, 2),
(4, 4, 'X', 'Mata Pelajaran Wajib', 3, 2),
(5, 1, 'XI', 'Mata Pelajaran Wajib', 2, 2),
(6, 1, 'XII', 'Mata Pelajaran Wajib', 2, 2),
(7, 2, 'XI', 'Mata Pelajaran Wajib', 2, 2),
(8, 3, 'XI', 'Mata Pelajaran Wajib', 3, 2),
(9, 4, 'XI', 'Mata Pelajaran Wajib', 3, 2),
(10, 2, 'XII', 'Mata Pelajaran Wajib', 2, 2),
(11, 3, 'XII', 'Mata Pelajaran Wajib', 3, 2),
(12, 4, 'XII', 'Mata Pelajaran Wajib', 3, 2),
(13, 1, 'X', 'Mata Pelajaran Wajib', 3, 5),
(14, 2, 'X', 'Mata Pelajaran Wajib', 2, 5),
(15, 3, 'X', 'Mata Pelajaran Wajib', 3, 5),
(16, 4, 'X', 'Mata Pelajaran Wajib', 3, 5),
(17, 1, 'XI', 'Mata Pelajaran Wajib', 3, 5),
(18, 2, 'XI', 'Mata Pelajaran Wajib', 2, 5),
(19, 3, 'XI', 'Mata Pelajaran Wajib', 3, 5),
(20, 4, 'XI', 'Mata Pelajaran Wajib', 3, 5),
(22, 1, 'XII', 'Mata Pelajaran Wajib', 3, 5),
(23, 2, 'XII', 'Mata Pelajaran Wajib', 2, 5),
(24, 3, 'XII', 'Mata Pelajaran Wajib', 3, 5),
(25, 4, 'XII', 'Mata Pelajaran Wajib', 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `surat_kategori`
--

CREATE TABLE `surat_kategori` (
  `id_kategori` int(11) NOT NULL,
  `kode_kategori` varchar(20) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_kategori`
--

INSERT INTO `surat_kategori` (`id_kategori`, `kode_kategori`, `nama_kategori`, `keterangan`) VALUES
(1, '421.1', 'Kesiswaan', 'Urusan kesiswaan, mutasi, SKL, dll'),
(2, '421.2', 'Kurikulum', 'Urusan kurikulum, pembelajaran'),
(3, '421.3', 'Kepegawaian', 'Surat tugas, SK guru, dll'),
(4, '005', 'Undangan', 'Undangan rapat, kegiatan'),
(5, '070', 'Lain-lain', 'Surat umum lainnya');

-- --------------------------------------------------------

--
-- Table structure for table `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id_surat_keluar` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_template` int(11) DEFAULT NULL,
  `nomor_surat` varchar(100) DEFAULT NULL,
  `tgl_surat` date DEFAULT NULL,
  `tujuan` varchar(200) DEFAULT NULL,
  `perihal` text DEFAULT NULL,
  `isi_surat` longtext DEFAULT NULL,
  `id_referensi_siswa` int(11) DEFAULT NULL,
  `id_referensi_guru` int(11) DEFAULT NULL,
  `status` enum('Draft','Final') DEFAULT 'Draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id_surat_masuk` int(11) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `tgl_surat` date DEFAULT NULL,
  `tgl_terima` date DEFAULT NULL,
  `asal_surat` varchar(200) DEFAULT NULL,
  `perihal` text DEFAULT NULL,
  `file_scan` varchar(255) DEFAULT NULL,
  `id_penerima` int(11) DEFAULT NULL,
  `disposisi` text DEFAULT NULL,
  `status` enum('Diterima','Diproses','Selesai') DEFAULT 'Diterima',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surat_template`
--

CREATE TABLE `surat_template` (
  `id_template` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_template` varchar(100) NOT NULL,
  `subjek_default` varchar(200) DEFAULT NULL,
  `isi_template` longtext DEFAULT NULL,
  `variabel_tersedia` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tahfidz`
--

CREATE TABLE `tahfidz` (
  `id_tahfidz` int(11) NOT NULL,
  `nama_kegiatan` varchar(100) DEFAULT NULL,
  `nama_kelompok` varchar(100) NOT NULL,
  `tingkat` varchar(50) DEFAULT NULL,
  `id_guru_pembina` int(11) DEFAULT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `jam` varchar(20) DEFAULT NULL,
  `tingkat_target` varchar(100) DEFAULT NULL,
  `file_proker` varchar(255) DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tahfidz_agenda`
--

CREATE TABLE `tahfidz_agenda` (
  `id_agenda` int(11) NOT NULL,
  `id_tahfidz` int(11) NOT NULL,
  `nama_agenda` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tipe` enum('program','agenda') DEFAULT 'agenda',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id_ta` int(11) NOT NULL,
  `nama_ta` varchar(50) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id_ta`, `nama_ta`, `tanggal_mulai`, `tanggal_selesai`, `status`) VALUES
(1, '2023/2024 Ganjil', '2023-07-01', '2023-12-31', 'Nonaktif'),
(2, '2023/2024 Genap', '2024-01-01', '2024-06-30', 'Nonaktif'),
(3, '2024/2025 Ganjil', '2024-07-01', '2024-12-02', 'Nonaktif'),
(4, '2024/2025 Genap', '2025-01-01', '2025-06-30', 'Nonaktif'),
(5, '2025/2026 Ganjil', '2025-07-01', '2025-12-31', 'Aktif'),
(6, '2025/2026 Genap', '2026-01-01', '2026-06-30', 'Nonaktif');

-- --------------------------------------------------------

--
-- Table structure for table `tracer_study`
--

CREATE TABLE `tracer_study` (
  `id_tracer` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL COMMENT 'FK ke siswa_alumni',
  `tahun_lulus` year(4) NOT NULL COMMENT 'Tahun kelulusan',
  `status_setelah_lulus` enum('PTN/PTS','Bekerja','Wirausaha','Lain-lain') NOT NULL COMMENT 'Status alumni setelah lulus',
  `nama_institusi` varchar(200) DEFAULT NULL COMMENT 'Nama Perguruan Tinggi/Perusahaan/Usaha',
  `jurusan_pekerjaan` varchar(200) DEFAULT NULL COMMENT 'Jurusan kuliah atau Bidang Pekerjaan',
  `kota` varchar(100) DEFAULT NULL COMMENT 'Kota lokasi PT/Perusahaan',
  `keterangan` text DEFAULT NULL COMMENT 'Keterangan tambahan',
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu input data',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Waktu update terakhir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk melacak status alumni setelah lulus (Study Tracer)';

-- --------------------------------------------------------

--
-- Table structure for table `tujuan_pembelajaran`
--

CREATE TABLE `tujuan_pembelajaran` (
  `id_tp` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_cp` int(11) NOT NULL,
  `kode_tp` varchar(20) NOT NULL,
  `deskripsi_tp` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi_guru`
--
ALTER TABLE `absensi_guru`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `id_guru_piket` (`id_guru_piket`),
  ADD KEY `idx_absensi_guru_tanggal` (`tanggal`),
  ADD KEY `idx_absensi_guru_id_guru` (`id_guru`),
  ADD KEY `idx_absensi_guru_status` (`status`),
  ADD KEY `idx_absensi_guru_composite` (`tanggal`,`id_guru`,`status`);

--
-- Indexes for table `absensi_siswa_mapel`
--
ALTER TABLE `absensi_siswa_mapel`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_guru_mapel` (`id_guru_mapel`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `idx_absensi_siswa_mapel_tanggal` (`tanggal`),
  ADD KEY `idx_absensi_siswa_mapel_id_siswa` (`id_siswa`),
  ADD KEY `idx_absensi_siswa_mapel_composite` (`tanggal`,`id_siswa`);

--
-- Indexes for table `absensi_siswa_piket`
--
ALTER TABLE `absensi_siswa_piket`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `id_guru_piket` (`id_guru_piket`),
  ADD KEY `idx_absensi_siswa_piket_tanggal` (`tanggal`),
  ADD KEY `idx_absensi_siswa_piket_id_siswa` (`id_siswa`),
  ADD KEY `idx_absensi_siswa_piket_status` (`status`),
  ADD KEY `idx_absensi_siswa_piket_composite` (`tanggal`,`id_siswa`,`status`);

--
-- Indexes for table `agenda_kokulikuler`
--
ALTER TABLE `agenda_kokulikuler`
  ADD PRIMARY KEY (`id_agenda`),
  ADD KEY `idx_kokulikuler` (`id_kokulikuler`);

--
-- Indexes for table `agenda_pembiasaan`
--
ALTER TABLE `agenda_pembiasaan`
  ADD PRIMARY KEY (`id_agenda`),
  ADD KEY `id_pembiasaan` (`id_pembiasaan`);

--
-- Indexes for table `anggota_ekskul`
--
ALTER TABLE `anggota_ekskul`
  ADD PRIMARY KEY (`id_anggota_ekskul`),
  ADD KEY `id_ekskul` (`id_ekskul`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `idx_anggota_ekskul_id_siswa` (`id_siswa`);

--
-- Indexes for table `anggota_kewirausahaan`
--
ALTER TABLE `anggota_kewirausahaan`
  ADD PRIMARY KEY (`id_kewirausahaan`,`id_siswa`,`id_ta`);

--
-- Indexes for table `anggota_kokulikuler`
--
ALTER TABLE `anggota_kokulikuler`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `unique_member` (`id_kokulikuler`,`id_siswa`,`id_ta`),
  ADD KEY `idx_anggota_kokulikuler_id_siswa` (`id_siswa`);

--
-- Indexes for table `anggota_pembiasaan`
--
ALTER TABLE `anggota_pembiasaan`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `unique_member_pem` (`id_pembiasaan`,`id_siswa`,`id_ta`),
  ADD KEY `idx_anggota_pembiasaan_id_siswa` (`id_siswa`);

--
-- Indexes for table `anggota_tahfidz`
--
ALTER TABLE `anggota_tahfidz`
  ADD PRIMARY KEY (`id_tahfidz`,`id_siswa`,`id_ta`);

--
-- Indexes for table `app_config`
--
ALTER TABLE `app_config`
  ADD PRIMARY KEY (`config_key`);

--
-- Indexes for table `app_menu`
--
ALTER TABLE `app_menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_user` (`id_pengguna`),
  ADD KEY `idx_aksi` (`aksi`),
  ADD KEY `idx_waktu` (`waktu`);

--
-- Indexes for table `capaian_pembelajaran`
--
ALTER TABLE `capaian_pembelajaran`
  ADD PRIMARY KEY (`id_cp`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `catatan_kasus`
--
ALTER TABLE `catatan_kasus`
  ADD PRIMARY KEY (`id_catatan`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_guru_piket` (`id_guru_piket`);

--
-- Indexes for table `catatan_kelas`
--
ALTER TABLE `catatan_kelas`
  ADD PRIMARY KEY (`id_catatan_kelas`),
  ADD KEY `id_jadwal_mengajar` (`id_jadwal_mengajar`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `ekskul_galeri`
--
ALTER TABLE `ekskul_galeri`
  ADD PRIMARY KEY (`id_galeri`),
  ADD KEY `id_ekskul` (`id_ekskul`);

--
-- Indexes for table `ekskul_program_kerja`
--
ALTER TABLE `ekskul_program_kerja`
  ADD PRIMARY KEY (`id_program`),
  ADD KEY `id_ekskul` (`id_ekskul`);

--
-- Indexes for table `ekstrakurikuler`
--
ALTER TABLE `ekstrakurikuler`
  ADD PRIMARY KEY (`id_ekskul`),
  ADD KEY `id_guru_pembina` (`id_guru_pembina`);

--
-- Indexes for table `galeri_pembiasaan`
--
ALTER TABLE `galeri_pembiasaan`
  ADD PRIMARY KEY (`id_galeri`),
  ADD KEY `id_pembiasaan` (`id_pembiasaan`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD UNIQUE KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `idx_guru_status` (`status`);

--
-- Indexes for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  ADD PRIMARY KEY (`id_guru_mapel`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_mapel` (`id_mapel`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `idx_guru_mapel_id_guru` (`id_guru`),
  ADD KEY `idx_guru_mapel_id_ta` (`id_ta`),
  ADD KEY `idx_guru_mapel_id_mapel` (`id_mapel`);

--
-- Indexes for table `hak_akses`
--
ALTER TABLE `hak_akses`
  ADD PRIMARY KEY (`id_akses`),
  ADD KEY `id_peran` (`id_peran`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indexes for table `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  ADD PRIMARY KEY (`id_jadwal_mengajar`),
  ADD KEY `id_guru_mapel` (`id_guru_mapel`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_jam` (`id_jam`),
  ADD KEY `idx_jadwal_mengajar_id_kelas` (`id_kelas`);

--
-- Indexes for table `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  ADD PRIMARY KEY (`id_jam`),
  ADD KEY `jp_fk_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `jurnal_ekstrakurikuler`
--
ALTER TABLE `jurnal_ekstrakurikuler`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indexes for table `jurnal_kbm`
--
ALTER TABLE `jurnal_kbm`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `jurnal_kewirausahaan`
--
ALTER TABLE `jurnal_kewirausahaan`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `fk_jurnal_tahapan` (`id_tahapan`);

--
-- Indexes for table `jurnal_kokulikuler`
--
ALTER TABLE `jurnal_kokulikuler`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indexes for table `jurnal_pembiasaan`
--
ALTER TABLE `jurnal_pembiasaan`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indexes for table `jurnal_tahfidz`
--
ALTER TABLE `jurnal_tahfidz`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indexes for table `kalender_akademik`
--
ALTER TABLE `kalender_akademik`
  ADD PRIMARY KEY (`id_kalender`),
  ADD KEY `idx_ta` (`id_ta`),
  ADD KEY `idx_tanggal` (`tanggal_mulai`,`tanggal_selesai`),
  ADD KEY `idx_kalender_akademik_id_ta` (`id_ta`),
  ADD KEY `idx_kalender_akademik_tanggal_mulai` (`tanggal_mulai`),
  ADD KEY `idx_kalender_akademik_tanggal_selesai` (`tanggal_selesai`),
  ADD KEY `idx_kalender_akademik_kategori` (`kategori`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD KEY `idx_kelas_tingkat` (`tingkat`),
  ADD KEY `idx_kelas_nama` (`nama_kelas`),
  ADD KEY `idx_kelas_composite` (`tingkat`,`nama_kelas`),
  ADD KEY `idx_kelas_id_ta` (`id_ta`);

--
-- Indexes for table `keuangan_anggaran`
--
ALTER TABLE `keuangan_anggaran`
  ADD PRIMARY KEY (`id_anggaran`);

--
-- Indexes for table `keuangan_gaji`
--
ALTER TABLE `keuangan_gaji`
  ADD PRIMARY KEY (`id_gaji`);

--
-- Indexes for table `keuangan_gaji_detail`
--
ALTER TABLE `keuangan_gaji_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_gaji` (`id_gaji`);

--
-- Indexes for table `keuangan_gaji_rules`
--
ALTER TABLE `keuangan_gaji_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_guru` (`id_guru`);

--
-- Indexes for table `keuangan_jenis`
--
ALTER TABLE `keuangan_jenis`
  ADD PRIMARY KEY (`id_jenis`),
  ADD UNIQUE KEY `kode_jenis` (`kode_jenis`);

--
-- Indexes for table `keuangan_kategori`
--
ALTER TABLE `keuangan_kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `kode_kategori` (`kode_kategori`);

--
-- Indexes for table `keuangan_master_jabatan`
--
ALTER TABLE `keuangan_master_jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `keuangan_memorial`
--
ALTER TABLE `keuangan_memorial`
  ADD PRIMARY KEY (`id_memorial`);

--
-- Indexes for table `keuangan_memorial_detail`
--
ALTER TABLE `keuangan_memorial_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_memorial` (`id_memorial`);

--
-- Indexes for table `keuangan_pembayaran_detail`
--
ALTER TABLE `keuangan_pembayaran_detail`
  ADD PRIMARY KEY (`id_pembayaran_detail`);

--
-- Indexes for table `keuangan_rekening`
--
ALTER TABLE `keuangan_rekening`
  ADD PRIMARY KEY (`id_rekening`),
  ADD UNIQUE KEY `kode_rekening` (`kode_rekening`);

--
-- Indexes for table `keuangan_tagihan_siswa`
--
ALTER TABLE `keuangan_tagihan_siswa`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD KEY `idx_keuangan_tagihan_siswa_id_siswa` (`id_siswa`);

--
-- Indexes for table `keuangan_tarif`
--
ALTER TABLE `keuangan_tarif`
  ADD PRIMARY KEY (`id_tarif`),
  ADD KEY `id_jenis` (`id_jenis`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `keuangan_tarif_ekskul`
--
ALTER TABLE `keuangan_tarif_ekskul`
  ADD PRIMARY KEY (`id_tarif_ekskul`),
  ADD UNIQUE KEY `id_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `keuangan_tarif_general`
--
ALTER TABLE `keuangan_tarif_general`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keuangan_transaksi`
--
ALTER TABLE `keuangan_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `no_bukti` (`no_bukti`),
  ADD KEY `idx_keuangan_transaksi_tanggal` (`tanggal`),
  ADD KEY `fk_transaksi_tagihan` (`id_tagihan`);

--
-- Indexes for table `kewirausahaan`
--
ALTER TABLE `kewirausahaan`
  ADD PRIMARY KEY (`id_kewirausahaan`);

--
-- Indexes for table `kewirausahaan_agenda`
--
ALTER TABLE `kewirausahaan_agenda`
  ADD PRIMARY KEY (`id_agenda`),
  ADD KEY `id_kewirausahaan` (`id_kewirausahaan`);

--
-- Indexes for table `kewirausahaan_galeri`
--
ALTER TABLE `kewirausahaan_galeri`
  ADD PRIMARY KEY (`id_galeri`),
  ADD KEY `id_kewirausahaan` (`id_kewirausahaan`);

--
-- Indexes for table `kewirausahaan_keuangan`
--
ALTER TABLE `kewirausahaan_keuangan`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_kewirausahaan` (`id_kewirausahaan`),
  ADD KEY `tanggal` (`tanggal`);

--
-- Indexes for table `kewirausahaan_produk`
--
ALTER TABLE `kewirausahaan_produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kewirausahaan` (`id_kewirausahaan`);

--
-- Indexes for table `kewirausahaan_tahapan`
--
ALTER TABLE `kewirausahaan_tahapan`
  ADD PRIMARY KEY (`id_tahapan`),
  ADD KEY `id_kewirausahaan` (`id_kewirausahaan`);

--
-- Indexes for table `kokulikuler`
--
ALTER TABLE `kokulikuler`
  ADD PRIMARY KEY (`id_kokulikuler`);

--
-- Indexes for table `kokulikuler_galeri`
--
ALTER TABLE `kokulikuler_galeri`
  ADD PRIMARY KEY (`id_galeri`),
  ADD KEY `id_kokulikuler` (`id_kokulikuler`);

--
-- Indexes for table `kokulikuler_nilai`
--
ALTER TABLE `kokulikuler_nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD KEY `id_kokulikuler` (`id_kokulikuler`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `kokulikuler_profil`
--
ALTER TABLE `kokulikuler_profil`
  ADD PRIMARY KEY (`id_kokulikuler`,`id_profil`);

--
-- Indexes for table `landing_gallery`
--
ALTER TABLE `landing_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_slider` (`is_slider`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `landing_news`
--
ALTER TABLE `landing_news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_published` (`is_published`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_time` (`ip_address`,`attempt_time`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_cleanup` (`attempt_time`);

--
-- Indexes for table `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`id_mapel`),
  ADD KEY `idx_mapel_kategori` (`kategori_mapel`),
  ADD KEY `idx_mapel_urutan` (`urutan`);

--
-- Indexes for table `master_kegiatan`
--
ALTER TABLE `master_kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`);

--
-- Indexes for table `master_template_dokumen`
--
ALTER TABLE `master_template_dokumen`
  ADD PRIMARY KEY (`id_template`);

--
-- Indexes for table `mutasi_masuk`
--
ALTER TABLE `mutasi_masuk`
  ADD PRIMARY KEY (`id_mutasi`),
  ADD KEY `idx_kelas_tujuan` (`id_kelas_tujuan`);

--
-- Indexes for table `mutasi_siswa`
--
ALTER TABLE `mutasi_siswa`
  ADD PRIMARY KEY (`id_mutasi`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `mutasi_fk_pengguna` (`id_pengguna_input`),
  ADD KEY `idx_mutasi_siswa_id_siswa` (`id_siswa`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD KEY `id_penempatan` (`id_penempatan`),
  ADD KEY `id_guru_mapel` (`id_guru_mapel`),
  ADD KEY `id_tp` (`id_tp`);

--
-- Indexes for table `nilai_ekskul`
--
ALTER TABLE `nilai_ekskul`
  ADD PRIMARY KEY (`id_nilai`),
  ADD UNIQUE KEY `unique_nilai` (`id_ekskul`,`id_siswa`),
  ADD KEY `id_ekskul` (`id_ekskul`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `nilai_sumatif`
--
ALTER TABLE `nilai_sumatif`
  ADD PRIMARY KEY (`id_nilai_sumatif`),
  ADD UNIQUE KEY `unik_siswa_sumatif` (`id_sumatif`,`id_penempatan`),
  ADD KEY `id_sumatif` (`id_sumatif`),
  ADD KEY `id_penempatan` (`id_penempatan`);

--
-- Indexes for table `nilai_sumatif_tp`
--
ALTER TABLE `nilai_sumatif_tp`
  ADD PRIMARY KEY (`id_nilai_sumatif`,`id_tp`),
  ADD KEY `id_nilai_sumatif` (`id_nilai_sumatif`),
  ADD KEY `id_tp` (`id_tp`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pembiasaan`
--
ALTER TABLE `pembiasaan`
  ADD PRIMARY KEY (`id_pembiasaan`);

--
-- Indexes for table `penempatan_siswa`
--
ALTER TABLE `penempatan_siswa`
  ADD PRIMARY KEY (`id_penempatan`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `id_penugasan_wali_kelas` (`id_penugasan_wali_kelas`),
  ADD KEY `idx_penempatan_siswa_id_siswa` (`id_siswa`),
  ADD KEY `idx_penempatan_siswa_id_ta` (`id_ta`),
  ADD KEY `idx_penempatan_siswa_id_kelas` (`id_kelas`),
  ADD KEY `idx_penempatan_siswa_composite` (`id_siswa`,`id_ta`,`id_kelas`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `pengguna_peran`
--
ALTER TABLE `pengguna_peran`
  ADD PRIMARY KEY (`id_pengguna`,`id_peran`),
  ADD KEY `id_peran` (`id_peran`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id_pengumuman`);

--
-- Indexes for table `penilaian_pembiasaan`
--
ALTER TABLE `penilaian_pembiasaan`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD UNIQUE KEY `unique_penilaian` (`id_pembiasaan`,`id_siswa`,`bulan`,`tahun`);

--
-- Indexes for table `penilaian_sumatif`
--
ALTER TABLE `penilaian_sumatif`
  ADD PRIMARY KEY (`id_sumatif`),
  ADD KEY `id_guru_mapel` (`id_guru_mapel`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `penugasan_jabatan`
--
ALTER TABLE `penugasan_jabatan`
  ADD PRIMARY KEY (`id_penugasan_jabatan`),
  ADD KEY `idx_guru_ta` (`id_guru`,`id_ta`),
  ADD KEY `idx_jabatan` (`jenis_jabatan`);

--
-- Indexes for table `penugasan_pembina`
--
ALTER TABLE `penugasan_pembina`
  ADD PRIMARY KEY (`id_penugasan_pembina`),
  ADD KEY `id_kegiatan` (`id_kegiatan`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `penugasan_wali_kelas`
--
ALTER TABLE `penugasan_wali_kelas`
  ADD PRIMARY KEY (`id_penugasan_wali_kelas`),
  ADD UNIQUE KEY `unique_walas_per_ta` (`id_kelas`,`id_ta`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_ta` (`id_ta`),
  ADD KEY `idx_penugasan_wali_kelas_id_guru` (`id_guru`),
  ADD KEY `idx_penugasan_wali_kelas_id_ta` (`id_ta`),
  ADD KEY `idx_penugasan_wali_kelas_id_kelas` (`id_kelas`);

--
-- Indexes for table `peran`
--
ALTER TABLE `peran`
  ADD PRIMARY KEY (`id_peran`),
  ADD UNIQUE KEY `nama_peran` (`nama_peran`);

--
-- Indexes for table `perangkat_pembelajaran`
--
ALTER TABLE `perangkat_pembelajaran`
  ADD PRIMARY KEY (`id_perangkat`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `ppdb_pendaftaran`
--
ALTER TABLE `ppdb_pendaftaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_pendaftaran` (`no_pendaftaran`),
  ADD KEY `idx_no_pendaftaran` (`no_pendaftaran`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sumber_pendaftaran` (`sumber_pendaftaran`),
  ADD KEY `idx_status_sumber` (`status`,`sumber_pendaftaran`);

--
-- Indexes for table `presensi_ekstrakurikuler`
--
ALTER TABLE `presensi_ekstrakurikuler`
  ADD PRIMARY KEY (`id_presensi`),
  ADD UNIQUE KEY `unique_prensence` (`id_jurnal`,`id_siswa`);

--
-- Indexes for table `presensi_kewirausahaan`
--
ALTER TABLE `presensi_kewirausahaan`
  ADD PRIMARY KEY (`id_presensi`),
  ADD UNIQUE KEY `unique_presensi` (`id_jurnal`,`id_siswa`);

--
-- Indexes for table `presensi_kokulikuler`
--
ALTER TABLE `presensi_kokulikuler`
  ADD PRIMARY KEY (`id_presensi`),
  ADD UNIQUE KEY `unique_prensence` (`id_jurnal`,`id_siswa`);

--
-- Indexes for table `presensi_pembiasaan`
--
ALTER TABLE `presensi_pembiasaan`
  ADD PRIMARY KEY (`id_presensi`),
  ADD UNIQUE KEY `unique_prensence_pem` (`id_jurnal`,`id_siswa`);

--
-- Indexes for table `presensi_tahfidz`
--
ALTER TABLE `presensi_tahfidz`
  ADD PRIMARY KEY (`id_presensi`),
  ADD UNIQUE KEY `unique_presensi` (`id_jurnal`,`id_siswa`);

--
-- Indexes for table `profil_guru`
--
ALTER TABLE `profil_guru`
  ADD PRIMARY KEY (`id_profil`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indexes for table `profil_sekolah`
--
ALTER TABLE `profil_sekolah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil_siswa`
--
ALTER TABLE `profil_siswa`
  ADD PRIMARY KEY (`id_profil`),
  ADD UNIQUE KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `ref_profil_lulusan`
--
ALTER TABLE `ref_profil_lulusan`
  ADD PRIMARY KEY (`id_profil`);

--
-- Indexes for table `ref_surah`
--
ALTER TABLE `ref_surah`
  ADD PRIMARY KEY (`id_surah`);

--
-- Indexes for table `rekap_presensi_pembiasaan`
--
ALTER TABLE `rekap_presensi_pembiasaan`
  ADD PRIMARY KEY (`id_rekap`),
  ADD UNIQUE KEY `unique_rekap_pem` (`id_pembiasaan`,`id_siswa`,`bulan`,`tahun`);

--
-- Indexes for table `setoran_tahfidz`
--
ALTER TABLE `setoran_tahfidz`
  ADD PRIMARY KEY (`id_setoran`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `idx_siswa_status` (`status_aktif`),
  ADD KEY `fk_siswa_ta_masuk` (`id_ta_masuk`);

--
-- Indexes for table `siswa_alumni`
--
ALTER TABLE `siswa_alumni`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `idx_siswa_alumni_tahun_lulus` (`tahun_lulus`);

--
-- Indexes for table `siswa_mutasi`
--
ALTER TABLE `siswa_mutasi`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `idx_kelas_asal` (`id_kelas_asal`),
  ADD KEY `idx_siswa_mutasi_id_siswa` (`id_siswa`),
  ADD KEY `idx_siswa_mutasi_tgl_mutasi` (`tgl_mutasi`);

--
-- Indexes for table `struktur_kurikulum`
--
ALTER TABLE `struktur_kurikulum`
  ADD PRIMARY KEY (`id_struktur`),
  ADD UNIQUE KEY `unique_struktur_mapel` (`id_mapel`,`tingkat`,`id_ta`),
  ADD KEY `id_ta` (`id_ta`);

--
-- Indexes for table `surat_kategori`
--
ALTER TABLE `surat_kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `kode_kategori` (`kode_kategori`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id_surat_keluar`),
  ADD UNIQUE KEY `nomor_surat` (`nomor_surat`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_template` (`id_template`);

--
-- Indexes for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id_surat_masuk`);

--
-- Indexes for table `surat_template`
--
ALTER TABLE `surat_template`
  ADD PRIMARY KEY (`id_template`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `tahfidz`
--
ALTER TABLE `tahfidz`
  ADD PRIMARY KEY (`id_tahfidz`);

--
-- Indexes for table `tahfidz_agenda`
--
ALTER TABLE `tahfidz_agenda`
  ADD PRIMARY KEY (`id_agenda`),
  ADD KEY `id_tahfidz` (`id_tahfidz`);

--
-- Indexes for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id_ta`),
  ADD KEY `idx_tahun_ajaran_status` (`status`);

--
-- Indexes for table `tracer_study`
--
ALTER TABLE `tracer_study`
  ADD PRIMARY KEY (`id_tracer`),
  ADD KEY `idx_siswa` (`id_siswa`),
  ADD KEY `idx_tahun` (`tahun_lulus`),
  ADD KEY `idx_status` (`status_setelah_lulus`);

--
-- Indexes for table `tujuan_pembelajaran`
--
ALTER TABLE `tujuan_pembelajaran`
  ADD PRIMARY KEY (`id_tp`),
  ADD KEY `id_mapel` (`id_mapel`),
  ADD KEY `tp_ibfk_2` (`id_cp`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi_guru`
--
ALTER TABLE `absensi_guru`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `absensi_siswa_mapel`
--
ALTER TABLE `absensi_siswa_mapel`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `absensi_siswa_piket`
--
ALTER TABLE `absensi_siswa_piket`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agenda_kokulikuler`
--
ALTER TABLE `agenda_kokulikuler`
  MODIFY `id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agenda_pembiasaan`
--
ALTER TABLE `agenda_pembiasaan`
  MODIFY `id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `anggota_ekskul`
--
ALTER TABLE `anggota_ekskul`
  MODIFY `id_anggota_ekskul` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `anggota_kokulikuler`
--
ALTER TABLE `anggota_kokulikuler`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `anggota_pembiasaan`
--
ALTER TABLE `anggota_pembiasaan`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_menu`
--
ALTER TABLE `app_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4226;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `capaian_pembelajaran`
--
ALTER TABLE `capaian_pembelajaran`
  MODIFY `id_cp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catatan_kasus`
--
ALTER TABLE `catatan_kasus`
  MODIFY `id_catatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `catatan_kelas`
--
ALTER TABLE `catatan_kelas`
  MODIFY `id_catatan_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ekskul_galeri`
--
ALTER TABLE `ekskul_galeri`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ekskul_program_kerja`
--
ALTER TABLE `ekskul_program_kerja`
  MODIFY `id_program` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ekstrakurikuler`
--
ALTER TABLE `ekstrakurikuler`
  MODIFY `id_ekskul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `galeri_pembiasaan`
--
ALTER TABLE `galeri_pembiasaan`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id_guru` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  MODIFY `id_guru_mapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hak_akses`
--
ALTER TABLE `hak_akses`
  MODIFY `id_akses` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1435;

--
-- AUTO_INCREMENT for table `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  MODIFY `id_jadwal_mengajar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  MODIFY `id_jam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jurnal_ekstrakurikuler`
--
ALTER TABLE `jurnal_ekstrakurikuler`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jurnal_kbm`
--
ALTER TABLE `jurnal_kbm`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_kewirausahaan`
--
ALTER TABLE `jurnal_kewirausahaan`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_kokulikuler`
--
ALTER TABLE `jurnal_kokulikuler`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_pembiasaan`
--
ALTER TABLE `jurnal_pembiasaan`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal_tahfidz`
--
ALTER TABLE `jurnal_tahfidz`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kalender_akademik`
--
ALTER TABLE `kalender_akademik`
  MODIFY `id_kalender` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `keuangan_anggaran`
--
ALTER TABLE `keuangan_anggaran`
  MODIFY `id_anggaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keuangan_gaji`
--
ALTER TABLE `keuangan_gaji`
  MODIFY `id_gaji` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `keuangan_gaji_detail`
--
ALTER TABLE `keuangan_gaji_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT for table `keuangan_gaji_rules`
--
ALTER TABLE `keuangan_gaji_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `keuangan_jenis`
--
ALTER TABLE `keuangan_jenis`
  MODIFY `id_jenis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `keuangan_kategori`
--
ALTER TABLE `keuangan_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `keuangan_master_jabatan`
--
ALTER TABLE `keuangan_master_jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `keuangan_memorial`
--
ALTER TABLE `keuangan_memorial`
  MODIFY `id_memorial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keuangan_memorial_detail`
--
ALTER TABLE `keuangan_memorial_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keuangan_pembayaran_detail`
--
ALTER TABLE `keuangan_pembayaran_detail`
  MODIFY `id_pembayaran_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keuangan_rekening`
--
ALTER TABLE `keuangan_rekening`
  MODIFY `id_rekening` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `keuangan_tagihan_siswa`
--
ALTER TABLE `keuangan_tagihan_siswa`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `keuangan_tarif`
--
ALTER TABLE `keuangan_tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `keuangan_tarif_ekskul`
--
ALTER TABLE `keuangan_tarif_ekskul`
  MODIFY `id_tarif_ekskul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `keuangan_transaksi`
--
ALTER TABLE `keuangan_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kewirausahaan`
--
ALTER TABLE `kewirausahaan`
  MODIFY `id_kewirausahaan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kewirausahaan_agenda`
--
ALTER TABLE `kewirausahaan_agenda`
  MODIFY `id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kewirausahaan_galeri`
--
ALTER TABLE `kewirausahaan_galeri`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kewirausahaan_keuangan`
--
ALTER TABLE `kewirausahaan_keuangan`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kewirausahaan_produk`
--
ALTER TABLE `kewirausahaan_produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kewirausahaan_tahapan`
--
ALTER TABLE `kewirausahaan_tahapan`
  MODIFY `id_tahapan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `kokulikuler`
--
ALTER TABLE `kokulikuler`
  MODIFY `id_kokulikuler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kokulikuler_galeri`
--
ALTER TABLE `kokulikuler_galeri`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kokulikuler_nilai`
--
ALTER TABLE `kokulikuler_nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_gallery`
--
ALTER TABLE `landing_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `landing_news`
--
ALTER TABLE `landing_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id_mapel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `master_kegiatan`
--
ALTER TABLE `master_kegiatan`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `master_template_dokumen`
--
ALTER TABLE `master_template_dokumen`
  MODIFY `id_template` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mutasi_masuk`
--
ALTER TABLE `mutasi_masuk`
  MODIFY `id_mutasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mutasi_siswa`
--
ALTER TABLE `mutasi_siswa`
  MODIFY `id_mutasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_ekskul`
--
ALTER TABLE `nilai_ekskul`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_sumatif`
--
ALTER TABLE `nilai_sumatif`
  MODIFY `id_nilai_sumatif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembiasaan`
--
ALTER TABLE `pembiasaan`
  MODIFY `id_pembiasaan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penempatan_siswa`
--
ALTER TABLE `penempatan_siswa`
  MODIFY `id_penempatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=484;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penilaian_pembiasaan`
--
ALTER TABLE `penilaian_pembiasaan`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penilaian_sumatif`
--
ALTER TABLE `penilaian_sumatif`
  MODIFY `id_sumatif` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penugasan_jabatan`
--
ALTER TABLE `penugasan_jabatan`
  MODIFY `id_penugasan_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penugasan_pembina`
--
ALTER TABLE `penugasan_pembina`
  MODIFY `id_penugasan_pembina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penugasan_wali_kelas`
--
ALTER TABLE `penugasan_wali_kelas`
  MODIFY `id_penugasan_wali_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `peran`
--
ALTER TABLE `peran`
  MODIFY `id_peran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `perangkat_pembelajaran`
--
ALTER TABLE `perangkat_pembelajaran`
  MODIFY `id_perangkat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ppdb_pendaftaran`
--
ALTER TABLE `ppdb_pendaftaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `presensi_ekstrakurikuler`
--
ALTER TABLE `presensi_ekstrakurikuler`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi_kewirausahaan`
--
ALTER TABLE `presensi_kewirausahaan`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi_kokulikuler`
--
ALTER TABLE `presensi_kokulikuler`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi_pembiasaan`
--
ALTER TABLE `presensi_pembiasaan`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presensi_tahfidz`
--
ALTER TABLE `presensi_tahfidz`
  MODIFY `id_presensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profil_guru`
--
ALTER TABLE `profil_guru`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profil_sekolah`
--
ALTER TABLE `profil_sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profil_siswa`
--
ALTER TABLE `profil_siswa`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `ref_profil_lulusan`
--
ALTER TABLE `ref_profil_lulusan`
  MODIFY `id_profil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rekap_presensi_pembiasaan`
--
ALTER TABLE `rekap_presensi_pembiasaan`
  MODIFY `id_rekap` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setoran_tahfidz`
--
ALTER TABLE `setoran_tahfidz`
  MODIFY `id_setoran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `siswa_alumni`
--
ALTER TABLE `siswa_alumni`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `siswa_mutasi`
--
ALTER TABLE `siswa_mutasi`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `struktur_kurikulum`
--
ALTER TABLE `struktur_kurikulum`
  MODIFY `id_struktur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `surat_kategori`
--
ALTER TABLE `surat_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id_surat_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id_surat_masuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `surat_template`
--
ALTER TABLE `surat_template`
  MODIFY `id_template` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tahfidz`
--
ALTER TABLE `tahfidz`
  MODIFY `id_tahfidz` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tahfidz_agenda`
--
ALTER TABLE `tahfidz_agenda`
  MODIFY `id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id_ta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tracer_study`
--
ALTER TABLE `tracer_study`
  MODIFY `id_tracer` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tujuan_pembelajaran`
--
ALTER TABLE `tujuan_pembelajaran`
  MODIFY `id_tp` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi_guru`
--
ALTER TABLE `absensi_guru`
  ADD CONSTRAINT `absensi_guru_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`),
  ADD CONSTRAINT `absensi_guru_ibfk_2` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`),
  ADD CONSTRAINT `absensi_guru_ibfk_3` FOREIGN KEY (`id_guru_piket`) REFERENCES `guru` (`id_guru`);

--
-- Constraints for table `absensi_siswa_mapel`
--
ALTER TABLE `absensi_siswa_mapel`
  ADD CONSTRAINT `absensi_siswa_mapel_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`),
  ADD CONSTRAINT `absensi_siswa_mapel_ibfk_2` FOREIGN KEY (`id_guru_mapel`) REFERENCES `guru_mapel` (`id_guru_mapel`),
  ADD CONSTRAINT `absensi_siswa_mapel_ibfk_3` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  ADD CONSTRAINT `absensi_siswa_mapel_ibfk_4` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`);

--
-- Constraints for table `absensi_siswa_piket`
--
ALTER TABLE `absensi_siswa_piket`
  ADD CONSTRAINT `absensi_siswa_piket_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`),
  ADD CONSTRAINT `absensi_siswa_piket_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  ADD CONSTRAINT `absensi_siswa_piket_ibfk_3` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`),
  ADD CONSTRAINT `absensi_siswa_piket_ibfk_4` FOREIGN KEY (`id_guru_piket`) REFERENCES `guru` (`id_guru`);

--
-- Constraints for table `capaian_pembelajaran`
--
ALTER TABLE `capaian_pembelajaran`
  ADD CONSTRAINT `cp_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE;

--
-- Constraints for table `catatan_kasus`
--
ALTER TABLE `catatan_kasus`
  ADD CONSTRAINT `catatan_kasus_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`),
  ADD CONSTRAINT `catatan_kasus_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  ADD CONSTRAINT `catatan_kasus_ibfk_3` FOREIGN KEY (`id_guru_piket`) REFERENCES `guru` (`id_guru`);

--
-- Constraints for table `catatan_kelas`
--
ALTER TABLE `catatan_kelas`
  ADD CONSTRAINT `catatan_kelas_ibfk_1` FOREIGN KEY (`id_jadwal_mengajar`) REFERENCES `jadwal_mengajar` (`id_jadwal_mengajar`) ON DELETE CASCADE,
  ADD CONSTRAINT `catatan_kelas_ibfk_2` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Constraints for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  ADD CONSTRAINT `guru_mapel_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `guru_mapel_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `guru_mapel_ibfk_3` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `hak_akses`
--
ALTER TABLE `hak_akses`
  ADD CONSTRAINT `hak_akses_ibfk_1` FOREIGN KEY (`id_peran`) REFERENCES `peran` (`id_peran`) ON DELETE CASCADE,
  ADD CONSTRAINT `hak_akses_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `app_menu` (`id_menu`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  ADD CONSTRAINT `jadwal_mengajar_ibfk_1` FOREIGN KEY (`id_guru_mapel`) REFERENCES `guru_mapel` (`id_guru_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_mengajar_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_mengajar_ibfk_3` FOREIGN KEY (`id_jam`) REFERENCES `jam_pelajaran` (`id_jam`) ON DELETE CASCADE;

--
-- Constraints for table `jam_pelajaran`
--
ALTER TABLE `jam_pelajaran`
  ADD CONSTRAINT `jp_fk_kegiatan` FOREIGN KEY (`id_kegiatan`) REFERENCES `master_kegiatan` (`id_kegiatan`) ON DELETE SET NULL;

--
-- Constraints for table `jurnal_kbm`
--
ALTER TABLE `jurnal_kbm`
  ADD CONSTRAINT `jurnal_kbm_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`),
  ADD CONSTRAINT `jurnal_kbm_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`),
  ADD CONSTRAINT `jurnal_kbm_ibfk_3` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`);

--
-- Constraints for table `jurnal_kewirausahaan`
--
ALTER TABLE `jurnal_kewirausahaan`
  ADD CONSTRAINT `fk_jurnal_tahapan` FOREIGN KEY (`id_tahapan`) REFERENCES `kewirausahaan_tahapan` (`id_tahapan`) ON DELETE SET NULL;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `fk_kelas_tahun_ajaran` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `keuangan_gaji_detail`
--
ALTER TABLE `keuangan_gaji_detail`
  ADD CONSTRAINT `keuangan_gaji_detail_ibfk_1` FOREIGN KEY (`id_gaji`) REFERENCES `keuangan_gaji` (`id_gaji`) ON DELETE CASCADE;

--
-- Constraints for table `keuangan_memorial_detail`
--
ALTER TABLE `keuangan_memorial_detail`
  ADD CONSTRAINT `fk_memorial_header` FOREIGN KEY (`id_memorial`) REFERENCES `keuangan_memorial` (`id_memorial`) ON DELETE CASCADE;

--
-- Constraints for table `keuangan_tarif`
--
ALTER TABLE `keuangan_tarif`
  ADD CONSTRAINT `keuangan_tarif_ibfk_1` FOREIGN KEY (`id_jenis`) REFERENCES `keuangan_jenis` (`id_jenis`) ON DELETE CASCADE,
  ADD CONSTRAINT `keuangan_tarif_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `keuangan_tarif_ibfk_3` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE;

--
-- Constraints for table `keuangan_transaksi`
--
ALTER TABLE `keuangan_transaksi`
  ADD CONSTRAINT `fk_transaksi_tagihan` FOREIGN KEY (`id_tagihan`) REFERENCES `keuangan_tagihan_siswa` (`id_tagihan`) ON DELETE SET NULL;

--
-- Constraints for table `kewirausahaan_agenda`
--
ALTER TABLE `kewirausahaan_agenda`
  ADD CONSTRAINT `kewirausahaan_agenda_ibfk_1` FOREIGN KEY (`id_kewirausahaan`) REFERENCES `kewirausahaan` (`id_kewirausahaan`) ON DELETE CASCADE;

--
-- Constraints for table `landing_news`
--
ALTER TABLE `landing_news`
  ADD CONSTRAINT `landing_news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL;

--
-- Constraints for table `mutasi_masuk`
--
ALTER TABLE `mutasi_masuk`
  ADD CONSTRAINT `fk_mutasi_masuk_kelas` FOREIGN KEY (`id_kelas_tujuan`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `mutasi_siswa`
--
ALTER TABLE `mutasi_siswa`
  ADD CONSTRAINT `mutasi_fk_pengguna` FOREIGN KEY (`id_pengguna_input`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL,
  ADD CONSTRAINT `mutasi_fk_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `mutasi_fk_ta` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`id_penempatan`) REFERENCES `penempatan_siswa` (`id_penempatan`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`id_guru_mapel`) REFERENCES `guru_mapel` (`id_guru_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_3` FOREIGN KEY (`id_tp`) REFERENCES `tujuan_pembelajaran` (`id_tp`) ON DELETE CASCADE;

--
-- Constraints for table `nilai_sumatif`
--
ALTER TABLE `nilai_sumatif`
  ADD CONSTRAINT `nilai_sumatif_ibfk_1` FOREIGN KEY (`id_sumatif`) REFERENCES `penilaian_sumatif` (`id_sumatif`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_sumatif_ibfk_2` FOREIGN KEY (`id_penempatan`) REFERENCES `penempatan_siswa` (`id_penempatan`) ON DELETE CASCADE;

--
-- Constraints for table `nilai_sumatif_tp`
--
ALTER TABLE `nilai_sumatif_tp`
  ADD CONSTRAINT `nstp_ibfk_1` FOREIGN KEY (`id_nilai_sumatif`) REFERENCES `nilai_sumatif` (`id_nilai_sumatif`) ON DELETE CASCADE,
  ADD CONSTRAINT `nstp_ibfk_2` FOREIGN KEY (`id_tp`) REFERENCES `tujuan_pembelajaran` (`id_tp`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Constraints for table `penempatan_siswa`
--
ALTER TABLE `penempatan_siswa`
  ADD CONSTRAINT `ps_fk_walas` FOREIGN KEY (`id_penugasan_wali_kelas`) REFERENCES `penugasan_wali_kelas` (`id_penugasan_wali_kelas`) ON DELETE SET NULL,
  ADD CONSTRAINT `ps_ibfk_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `ps_ibfk_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `ps_ibfk_ta` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `pengguna_peran`
--
ALTER TABLE `pengguna_peran`
  ADD CONSTRAINT `pengguna_peran_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengguna_peran_ibfk_2` FOREIGN KEY (`id_peran`) REFERENCES `peran` (`id_peran`) ON DELETE CASCADE;

--
-- Constraints for table `penilaian_sumatif`
--
ALTER TABLE `penilaian_sumatif`
  ADD CONSTRAINT `sumatif_ibfk_1` FOREIGN KEY (`id_guru_mapel`) REFERENCES `guru_mapel` (`id_guru_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `sumatif_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `sumatif_ibfk_3` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `penugasan_wali_kelas`
--
ALTER TABLE `penugasan_wali_kelas`
  ADD CONSTRAINT `p_walas_ibfk_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `p_walas_ibfk_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `p_walas_ibfk_ta` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `perangkat_pembelajaran`
--
ALTER TABLE `perangkat_pembelajaran`
  ADD CONSTRAINT `perangkat_pembelajaran_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `perangkat_pembelajaran_ibfk_2` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_ekstrakurikuler`
--
ALTER TABLE `presensi_ekstrakurikuler`
  ADD CONSTRAINT `presensi_ekstrakurikuler_ibfk_1` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_ekstrakurikuler` (`id_jurnal`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_kokulikuler`
--
ALTER TABLE `presensi_kokulikuler`
  ADD CONSTRAINT `presensi_kokulikuler_ibfk_1` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_kokulikuler` (`id_jurnal`) ON DELETE CASCADE;

--
-- Constraints for table `presensi_pembiasaan`
--
ALTER TABLE `presensi_pembiasaan`
  ADD CONSTRAINT `presensi_pembiasaan_ibfk_1` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal_pembiasaan` (`id_jurnal`) ON DELETE CASCADE;

--
-- Constraints for table `profil_guru`
--
ALTER TABLE `profil_guru`
  ADD CONSTRAINT `fk_profil_guru_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE;

--
-- Constraints for table `profil_siswa`
--
ALTER TABLE `profil_siswa`
  ADD CONSTRAINT `profil_siswa_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `fk_siswa_ta_masuk` FOREIGN KEY (`id_ta_masuk`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE SET NULL,
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL;

--
-- Constraints for table `siswa_mutasi`
--
ALTER TABLE `siswa_mutasi`
  ADD CONSTRAINT `fk_siswa_mutasi_kelas` FOREIGN KEY (`id_kelas_asal`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `struktur_kurikulum`
--
ALTER TABLE `struktur_kurikulum`
  ADD CONSTRAINT `struktur_kurikulum_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `struktur_kurikulum_ibfk_2` FOREIGN KEY (`id_ta`) REFERENCES `tahun_ajaran` (`id_ta`) ON DELETE CASCADE;

--
-- Constraints for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD CONSTRAINT `surat_keluar_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `surat_kategori` (`id_kategori`) ON DELETE SET NULL,
  ADD CONSTRAINT `surat_keluar_ibfk_2` FOREIGN KEY (`id_template`) REFERENCES `surat_template` (`id_template`) ON DELETE SET NULL;

--
-- Constraints for table `surat_template`
--
ALTER TABLE `surat_template`
  ADD CONSTRAINT `surat_template_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `surat_kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `tahfidz_agenda`
--
ALTER TABLE `tahfidz_agenda`
  ADD CONSTRAINT `tahfidz_agenda_ibfk_1` FOREIGN KEY (`id_tahfidz`) REFERENCES `tahfidz` (`id_tahfidz`) ON DELETE CASCADE;

--
-- Constraints for table `tracer_study`
--
ALTER TABLE `tracer_study`
  ADD CONSTRAINT `fk_tracer_siswa_alumni` FOREIGN KEY (`id_siswa`) REFERENCES `siswa_alumni` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tujuan_pembelajaran`
--
ALTER TABLE `tujuan_pembelajaran`
  ADD CONSTRAINT `tp_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `tp_ibfk_2` FOREIGN KEY (`id_cp`) REFERENCES `capaian_pembelajaran` (`id_cp`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
