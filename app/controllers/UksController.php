<?php
// app/controllers/UksController.php

require_once __DIR__ . '/../models/UksModel.php';

class UksController
{
    public static function index($pdo)
    {
        $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        $tab = $_GET['tab'] ?? 'rekam_medis';

        // Filter Rekam Medis
        $filters = [
            'tanggal' => $_GET['filter_tgl'] ?? '',
            'bulan' => $_GET['filter_bulan'] ?? '',
            'tahun' => $_GET['filter_tahun'] ?? '',
            'tipe_pasien' => $_GET['filter_tipe'] ?? ''
        ];

        // 1. Data Kunjungan / Rekam Medis
        $kunjungan_list = UksModel::getKunjunganList($pdo, $id_ta, $filters);

        // 2. Data Stok Obat & Alkes
        $obat_list = UksModel::getObatList($pdo, $id_ta);

        // 3. Data Agenda Kegiatan UKS (Reuse agenda_tugas_tambahan)
        $stmt_agenda = $pdo->prepare("SELECT * FROM agenda_tugas_tambahan WHERE jenis_tugas_tambahan IN ('pembina_uks', 'manajemen_uks') AND id_ta = ? ORDER BY tanggal DESC");
        $stmt_agenda->execute([$id_ta]);
        $agendas = $stmt_agenda->fetchAll(PDO::FETCH_ASSOC);

        // 4. Data Statistik Rekapitulasi
        $stats = UksModel::getRekapStats($pdo, $id_ta);

        // 5. Data Siswa untuk Auto-Complete / Selector
        $stmt_siswa = $pdo->prepare("
            SELECT s.id_siswa, s.nama, s.nisn, s.nipd, k.nama_kelas 
            FROM siswa s 
            LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
            LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
            WHERE s.status_aktif = 'Aktif'
            ORDER BY s.nama ASC
        ");
        $stmt_siswa->execute([$id_ta]);
        $siswa_list = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

        // 6. Data Guru/Tendik untuk Selector
        $stmt_guru = $pdo->query("SELECT id_guru, nama, nuptk, jenis_guru as jabatan FROM guru WHERE status != 'Pensiun' ORDER BY nama ASC");
        $guru_list = $stmt_guru ? $stmt_guru->fetchAll(PDO::FETCH_ASSOC) : [];

        // 7. Tahun Ajaran List
        $tahun_ajaran = $pdo->query("SELECT * FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Unit Kesehatan Sekolah (UKS)";
        require_once __DIR__ . '/../views/uks_index.php';
    }

    public static function save_kunjungan($pdo)
    {
        $id_ta = $_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        $id_kunjungan = $_POST['id_kunjungan'] ?? null;

        $data = [
            'id_kunjungan' => $id_kunjungan,
            'id_ta' => $id_ta,
            'tanggal' => $_POST['tanggal'] ?? date('Y-m-d'),
            'jam_masuk' => $_POST['jam_masuk'] ?? date('H:i'),
            'jam_keluar' => $_POST['jam_keluar'] ?? null,
            'tipe_pasien' => $_POST['tipe_pasien'] ?? 'Siswa',
            'id_pasien' => $_POST['id_pasien'] ?? null,
            'nama_pasien' => trim($_POST['nama_pasien'] ?? ''),
            'kelas_unit' => trim($_POST['kelas_unit'] ?? ''),
            'keluhan' => trim($_POST['keluhan'] ?? ''),
            'suhu_tubuh' => trim($_POST['suhu_tubuh'] ?? ''),
            'tekanan_darah' => trim($_POST['tekanan_darah'] ?? ''),
            'diagnosa_awal' => trim($_POST['diagnosa_awal'] ?? ''),
            'tindakan' => trim($_POST['tindakan'] ?? ''),
            'obat_diberikan' => trim($_POST['obat_diberikan'] ?? ''),
            'status_tindak_lanjut' => $_POST['status_tindak_lanjut'] ?? 'Kembali ke Kelas',
            'petugas_jaga' => trim($_POST['petugas_jaga'] ?? ($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas UKS')),
            'keterangan' => trim($_POST['keterangan'] ?? '')
        ];

        if (empty($data['nama_pasien']) || empty($data['keluhan'])) {
            $_SESSION['pesan_error'] = "Nama pasien dan keluhan wajib diisi!";
            header("Location: " . BASE_URL . "uks?tab=rekam_medis");
            exit;
        }

        $saved = UksModel::saveKunjungan($pdo, $data);
        if ($saved) {
            $_SESSION['pesan_sukses'] = "Data rekam medis pasien berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan data rekam medis.";
        }

        header("Location: " . BASE_URL . "uks?tab=rekam_medis");
        exit;
    }

    public static function delete_kunjungan($pdo)
    {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            UksModel::deleteKunjungan($pdo, $id);
            $_SESSION['pesan_sukses'] = "Data rekam medis berhasil dihapus.";
        }
        header("Location: " . BASE_URL . "uks?tab=rekam_medis");
        exit;
    }

    public static function save_obat($pdo)
    {
        $id_ta = $_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        $id_obat = $_POST['id_obat'] ?? null;

        $data = [
            'id_obat' => $id_obat,
            'id_ta' => $id_ta,
            'kode_obat' => trim($_POST['kode_obat'] ?? ''),
            'nama_obat' => trim($_POST['nama_obat'] ?? ''),
            'kategori' => $_POST['kategori'] ?? 'Obat Bebas',
            'satuan' => trim($_POST['satuan'] ?? 'Tablet'),
            'stok' => (int)($_POST['stok'] ?? 0),
            'stok_minimum' => (int)($_POST['stok_minimum'] ?? 5),
            'tgl_kadaluarsa' => $_POST['tgl_kadaluarsa'] ?: null,
            'kegunaan_indikasi' => trim($_POST['kegunaan_indikasi'] ?? ''),
            'catatan' => trim($_POST['catatan'] ?? '')
        ];

        if (empty($data['nama_obat'])) {
            $_SESSION['pesan_error'] = "Nama obat wajib diisi!";
            header("Location: " . BASE_URL . "uks?tab=obat");
            exit;
        }

        $saved = UksModel::saveObat($pdo, $data);
        if ($saved) {
            $_SESSION['pesan_sukses'] = "Data inventaris obat berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan data obat.";
        }

        header("Location: " . BASE_URL . "uks?tab=obat");
        exit;
    }

    public static function delete_obat($pdo)
    {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            UksModel::deleteObat($pdo, $id);
            $_SESSION['pesan_sukses'] = "Data obat berhasil dihapus.";
        }
        header("Location: " . BASE_URL . "uks?tab=obat");
        exit;
    }

    public static function save_agenda($pdo)
    {
        $id_ta = $_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        $nama_kegiatan = trim($_POST['nama_kegiatan'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $tempat = trim($_POST['tempat'] ?? 'Ruang UKS');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if (!empty($nama_kegiatan)) {
            $stmt = $pdo->prepare("INSERT INTO agenda_tugas_tambahan (id_ta, jenis_tugas_tambahan, nama_kegiatan, tanggal, tempat, keterangan) VALUES (?, 'pembina_uks', ?, ?, ?, ?)");
            $stmt->execute([$id_ta, $nama_kegiatan, $tanggal, $tempat, $keterangan]);
            $_SESSION['pesan_sukses'] = "Agenda kegiatan UKS berhasil disimpan.";
        }

        header("Location: " . BASE_URL . "uks?tab=administrasi");
        exit;
    }

    public static function delete_agenda($pdo)
    {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM agenda_tugas_tambahan WHERE id_agenda = ?");
            $stmt->execute([$id]);
            $_SESSION['pesan_sukses'] = "Agenda UKS berhasil dihapus.";
        }
        header("Location: " . BASE_URL . "uks?tab=administrasi");
        exit;
    }

    public static function cetak_surat_izin($pdo)
    {
        $id = $_GET['id'] ?? 0;
        $kunjungan = UksModel::getKunjunganById($pdo, $id);
        if (!$kunjungan) {
            die("Data rekam medis tidak ditemukan.");
        }

        // Ambil Profil Sekolah untuk KOP
        $sekolah = $pdo->query("SELECT * FROM profil_sekolah WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/uks_surat_izin_print.php';
    }
}
