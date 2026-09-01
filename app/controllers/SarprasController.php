<?php
// app/controllers/SarprasController.php

class SarprasController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard()
    {
        $title = "Dashboard Sarpras";
        
        // Count totals
        $total_gedung = $this->pdo->query("SELECT COUNT(*) FROM sarpras_gedung")->fetchColumn();
        $total_ruang = $this->pdo->query("SELECT COUNT(*) FROM sarpras_ruang")->fetchColumn();
        $total_barang = $this->pdo->query("SELECT COUNT(*) FROM sarpras_barang")->fetchColumn();
        
        $sum_barang = $this->pdo->query("SELECT SUM(kondisi_baik + kondisi_rusak_ringan + kondisi_rusak_berat) FROM sarpras_barang")->fetchColumn();
        if (!$sum_barang) $sum_barang = 0;

        $pdo = $this->pdo;
        require_once '../app/views/sarpras_dashboard.php';
    }

    // ==========================================
    // GEDUNG & PRASARANA
    // ==========================================
    public function gedung_index()
    {
        $title = "Data Gedung & Prasarana";
        $stmt = $this->pdo->query("SELECT * FROM sarpras_gedung ORDER BY id_gedung DESC");
        $gedung = $stmt->fetchAll();
        
        $pdo = $this->pdo;
        require_once '../app/views/sarpras_gedung.php';
    }

    public function gedung_save()
    {
        $id_gedung = $_POST['id_gedung'] ?? '';
        $nama_gedung = $_POST['nama_gedung'] ?? '';
        $kode_gedung = $_POST['kode_gedung'] ?? '';
        $kondisi = $_POST['kondisi'] ?? 'Baik';
        $keterangan = $_POST['keterangan'] ?? '';

        if (!empty($id_gedung)) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE sarpras_gedung SET nama_gedung=?, kode_gedung=?, kondisi=?, keterangan=? WHERE id_gedung=?");
            $stmt->execute([$nama_gedung, $kode_gedung, $kondisi, $keterangan, $id_gedung]);
            $_SESSION['pesan_sukses'] = 'Data gedung berhasil diperbarui.';
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO sarpras_gedung (nama_gedung, kode_gedung, kondisi, keterangan) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_gedung, $kode_gedung, $kondisi, $keterangan]);
            $_SESSION['pesan_sukses'] = 'Data gedung berhasil ditambahkan.';
        }
        redirect(BASE_URL . 'sarpras_gedung');
    }

    public function gedung_delete()
    {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM sarpras_gedung WHERE id_gedung=?");
            $stmt->execute([$id]);
            $_SESSION['pesan_sukses'] = 'Data gedung berhasil dihapus.';
        }
        redirect(BASE_URL . 'sarpras_gedung');
    }

    // ==========================================
    // DATA RUANGAN
    // ==========================================
    public function ruang_index()
    {
        $title = "Data Ruangan";
        
        // Fetch all gedung for dropdown
        $gedung_list = $this->pdo->query("SELECT id_gedung, nama_gedung FROM sarpras_gedung ORDER BY nama_gedung ASC")->fetchAll();
        
        // Fetch ruang with gedung name
        $stmt = $this->pdo->query("
            SELECT r.*, g.nama_gedung 
            FROM sarpras_ruang r 
            LEFT JOIN sarpras_gedung g ON r.id_gedung = g.id_gedung 
            ORDER BY r.id_ruang DESC
        ");
        $ruang = $stmt->fetchAll();
        
        $pdo = $this->pdo;
        require_once '../app/views/sarpras_ruang.php';
    }

    public function ruang_save()
    {
        $id_ruang = $_POST['id_ruang'] ?? '';
        $id_gedung = $_POST['id_gedung'] ?? '';
        $nama_ruang = $_POST['nama_ruang'] ?? '';
        $kode_ruang = $_POST['kode_ruang'] ?? '';
        $kapasitas = $_POST['kapasitas'] ?? 0;
        $lantai = $_POST['lantai'] ?? 1;
        $kondisi = $_POST['kondisi'] ?? 'Baik';
        $keterangan = $_POST['keterangan'] ?? '';

        if (!empty($id_ruang)) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE sarpras_ruang SET id_gedung=?, nama_ruang=?, kode_ruang=?, kapasitas=?, lantai=?, kondisi=?, keterangan=? WHERE id_ruang=?");
            $stmt->execute([$id_gedung, $nama_ruang, $kode_ruang, $kapasitas, $lantai, $kondisi, $keterangan, $id_ruang]);
            $_SESSION['pesan_sukses'] = 'Data ruangan berhasil diperbarui.';
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO sarpras_ruang (id_gedung, nama_ruang, kode_ruang, kapasitas, lantai, kondisi, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_gedung, $nama_ruang, $kode_ruang, $kapasitas, $lantai, $kondisi, $keterangan]);
            $_SESSION['pesan_sukses'] = 'Data ruangan berhasil ditambahkan.';
        }
        redirect(BASE_URL . 'sarpras_ruang');
    }

    public function ruang_delete()
    {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM sarpras_ruang WHERE id_ruang=?");
            $stmt->execute([$id]);
            $_SESSION['pesan_sukses'] = 'Data ruangan berhasil dihapus.';
        }
        redirect(BASE_URL . 'sarpras_ruang');
    }

    // ==========================================
    // INVENTARIS BARANG
    // ==========================================
    public function barang_index()
    {
        $title = "Inventaris Barang";
        
        // Fetch all ruang for dropdown (include gedung name for context)
        $ruang_list = $this->pdo->query("
            SELECT r.id_ruang, r.nama_ruang, g.nama_gedung 
            FROM sarpras_ruang r 
            LEFT JOIN sarpras_gedung g ON r.id_gedung = g.id_gedung 
            ORDER BY g.nama_gedung ASC, r.nama_ruang ASC
        ")->fetchAll();
        
        // Fetch barang
        $stmt = $this->pdo->query("
            SELECT b.*, r.nama_ruang, g.nama_gedung 
            FROM sarpras_barang b 
            LEFT JOIN sarpras_ruang r ON b.id_ruang = r.id_ruang
            LEFT JOIN sarpras_gedung g ON r.id_gedung = g.id_gedung
            ORDER BY b.id_barang DESC
        ");
        $barang = $stmt->fetchAll();
        
        $pdo = $this->pdo;
        require_once '../app/views/sarpras_barang.php';
    }

    public function barang_save()
    {
        $id_barang = $_POST['id_barang'] ?? '';
        $id_ruang = $_POST['id_ruang'] ?? '';
        $nama_barang = $_POST['nama_barang'] ?? '';
        $kode_barang = $_POST['kode_barang'] ?? '';
        $merk = $_POST['merk'] ?? '';
        $tahun_pengadaan = !empty($_POST['tahun_pengadaan']) ? $_POST['tahun_pengadaan'] : null;
        $sumber_dana = $_POST['sumber_dana'] ?? '';
        
        $kondisi_baik = $_POST['kondisi_baik'] ?? 0;
        $kondisi_rusak_ringan = $_POST['kondisi_rusak_ringan'] ?? 0;
        $kondisi_rusak_berat = $_POST['kondisi_rusak_berat'] ?? 0;
        $keterangan = $_POST['keterangan'] ?? '';

        if (!empty($id_barang)) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE sarpras_barang SET id_ruang=?, nama_barang=?, kode_barang=?, merk=?, tahun_pengadaan=?, sumber_dana=?, kondisi_baik=?, kondisi_rusak_ringan=?, kondisi_rusak_berat=?, keterangan=? WHERE id_barang=?");
            $stmt->execute([$id_ruang, $nama_barang, $kode_barang, $merk, $tahun_pengadaan, $sumber_dana, $kondisi_baik, $kondisi_rusak_ringan, $kondisi_rusak_berat, $keterangan, $id_barang]);
            $_SESSION['pesan_sukses'] = 'Data inventaris berhasil diperbarui.';
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO sarpras_barang (id_ruang, nama_barang, kode_barang, merk, tahun_pengadaan, sumber_dana, kondisi_baik, kondisi_rusak_ringan, kondisi_rusak_berat, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_ruang, $nama_barang, $kode_barang, $merk, $tahun_pengadaan, $sumber_dana, $kondisi_baik, $kondisi_rusak_ringan, $kondisi_rusak_berat, $keterangan]);
            $_SESSION['pesan_sukses'] = 'Data inventaris berhasil ditambahkan.';
        }
        redirect(BASE_URL . 'sarpras_barang');
    }

    public function barang_delete()
    {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM sarpras_barang WHERE id_barang=?");
            $stmt->execute([$id]);
            $_SESSION['pesan_sukses'] = 'Data inventaris berhasil dihapus.';
        }
        redirect(BASE_URL . 'sarpras_barang');
    }
}
