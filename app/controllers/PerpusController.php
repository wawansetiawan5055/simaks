<?php
// app/controllers/PerpusController.php

class PerpusController
{

    public static function index($pdo)
    {
        $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;

        // 1. Ambil Katalog Buku
        $buku = $pdo->query("SELECT * FROM perpus_buku ORDER BY judul_buku ASC")->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil Peminjaman Aktif (Status 'Dipinjam')
        $pinjam = $pdo->query(
            "SELECT p.*, b.judul_buku, s.nama AS nama_peminjam, k.nama_kelas 
             FROM perpus_peminjaman p 
             JOIN perpus_buku b ON p.id_buku = b.id_buku 
             LEFT JOIN siswa s ON p.id_peminjam = s.id_siswa AND p.peminjam_tipe = 'Siswa'
             LEFT JOIN kelas k ON p.id_kelas_peminjam = k.id_kelas
             WHERE p.status != 'Kembali' 
             ORDER BY p.tgl_pinjam DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        // 2.5 Ambil Riwayat Peminjaman (Semua termasuk yang sudah dikembalikan)
        $riwayat = $pdo->query(
            "SELECT p.*, b.judul_buku, s.nama AS nama_peminjam, k.nama_kelas 
             FROM perpus_peminjaman p 
             JOIN perpus_buku b ON p.id_buku = b.id_buku 
             LEFT JOIN siswa s ON p.id_peminjam = s.id_siswa AND p.peminjam_tipe = 'Siswa'
             LEFT JOIN kelas k ON p.id_kelas_peminjam = k.id_kelas
             ORDER BY p.tgl_pinjam DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        // 3. Ambil Kelas untuk filter dropdown
        $kelas = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas ASC");
        $kelas->execute([$id_ta]);
        $kelas_list = $kelas->fetchAll(PDO::FETCH_ASSOC);

        // 4. Ambil Agenda (Reuse table agenda_tugas_tambahan)
        $stmt_agenda = $pdo->prepare("SELECT * FROM agenda_tugas_tambahan WHERE jenis_tugas_tambahan = 'manajemen_perpus' AND id_ta = ? ORDER BY tanggal ASC");
        $stmt_agenda->execute([$id_ta]);
        $agendas = $stmt_agenda->fetchAll(PDO::FETCH_ASSOC);

        $tahun_ajaran = $pdo->query("SELECT * FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Manajemen Perpustakaan";
        require_once __DIR__ . '/../views/perpus_index.php';
    }

    public static function save_buku($pdo)
    {
        $id = $_POST['id_buku'] ?? null;
        $kode = $_POST['kode_buku'] ?? '';
        $judul = $_POST['judul_buku'] ?? '';
        $pengarang = $_POST['pengarang'] ?? '';
        $penerbit = $_POST['penerbit'] ?? '';
        $stok = $_POST['jumlah_stok'] ?? 0;
        $rak = $_POST['lokasi_rak'] ?? '';

        if ($id) {
            $sql = "UPDATE perpus_buku SET kode_buku=?, judul_buku=?, pengarang=?, penerbit=?, jumlah_stok=?, lokasi_rak=? WHERE id_buku=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$kode, $judul, $pengarang, $penerbit, $stok, $rak, $id]);
        } else {
            $sql = "INSERT INTO perpus_buku (kode_buku, judul_buku, pengarang, penerbit, jumlah_stok, lokasi_rak) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$kode, $judul, $pengarang, $penerbit, $stok, $rak]);
        }

        $_SESSION['pesan_sukses'] = "Data buku disimpan.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public static function save_peminjaman($pdo)
    {
        $id_buku = $_POST['id_buku'] ?? 0;
        $tipe = $_POST['peminjam_tipe'] ?? '';
        $id_peminjam = $_POST['id_peminjam'] ?? 0;
        $id_kelas = $_POST['id_kelas_peminjam'] ?? null;
        $tgl_pinjam = $_POST['tgl_pinjam'] ?? date('Y-m-d');
        $tgl_kembali = $_POST['tgl_kembali_rencana'] ?? '';

        if ($tipe === 'Siswa' && empty($id_peminjam)) {
            $_SESSION['pesan_error'] = "Pilih siswa untuk peminjaman.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $sql = "INSERT INTO perpus_peminjaman (id_buku, peminjam_tipe, id_peminjam, id_kelas_peminjam, tgl_pinjam, tgl_kembali_rencana, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Dipinjam')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_buku, $tipe, $id_peminjam, $id_kelas, $tgl_pinjam, $tgl_kembali]);

        $_SESSION['pesan_sukses'] = "Peminjaman dicatat.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public static function get_siswa_by_kelas($pdo)
    {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        
        $stmt = $pdo->prepare(
            "SELECT s.id_siswa, s.nama, s.nisn, k.nama_kelas 
             FROM siswa s
             JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
             JOIN kelas k ON ps.id_kelas = k.id_kelas
             WHERE ps.id_kelas = ? AND ps.status_penempatan = 'Aktif'
             ORDER BY s.nama ASC"
        );
        $stmt->execute([$id_kelas]);
        $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $siswa]);
        exit;
    }

    public static function kembalikan($pdo)
    {
        $id = $_GET['id'] ?? 0;
        $pdo->prepare("UPDATE perpus_peminjaman SET status='Kembali', tgl_kembali_real = CURRENT_DATE WHERE id_peminjaman = ?")->execute([$id]);
        $_SESSION['pesan_sukses'] = "Buku telah dikembalikan.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
