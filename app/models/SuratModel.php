<?php
// app/models/SuratModel.php

class SuratModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function initTables() {
        // 1. Table Kategori (Klasifikasi Surat)
        $sqlKategori = "CREATE TABLE IF NOT EXISTS surat_kategori (
            id_kategori INT AUTO_INCREMENT PRIMARY KEY,
            kode_kategori VARCHAR(20) NOT NULL UNIQUE,
            nama_kategori VARCHAR(100) NOT NULL,
            keterangan TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sqlKategori);

        // 2. Table Template Surat
        $sqlTemplate = "CREATE TABLE IF NOT EXISTS surat_template (
            id_template INT AUTO_INCREMENT PRIMARY KEY,
            id_kategori INT,
            nama_template VARCHAR(100) NOT NULL,
            subjek_default VARCHAR(200),
            isi_template LONGTEXT,
            variabel_tersedia TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_kategori) REFERENCES surat_kategori(id_kategori) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sqlTemplate);

        // 3. Table Surat Masuk
        $sqlMasuk = "CREATE TABLE IF NOT EXISTS surat_masuk (
            id_surat_masuk INT AUTO_INCREMENT PRIMARY KEY,
            nomor_surat VARCHAR(100) NOT NULL,
            tgl_surat DATE,
            tgl_terima DATE,
            asal_surat VARCHAR(200),
            perihal TEXT,
            file_scan VARCHAR(255),
            id_penerima INT,
            disposisi TEXT,
            status ENUM('Diterima', 'Diproses', 'Selesai') DEFAULT 'Diterima',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sqlMasuk);

        // 4. Table Surat Keluar
        $sqlKeluar = "CREATE TABLE IF NOT EXISTS surat_keluar (
            id_surat_keluar INT AUTO_INCREMENT PRIMARY KEY,
            id_kategori INT,
            id_template INT,
            nomor_surat VARCHAR(100) UNIQUE,
            tgl_surat DATE,
            tujuan VARCHAR(200),
            perihal TEXT,
            isi_surat LONGTEXT,
            id_referensi_siswa INT NULL,
            id_referensi_guru INT NULL,
            status ENUM('Draft', 'Final') DEFAULT 'Draft',
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_kategori) REFERENCES surat_kategori(id_kategori) ON DELETE SET NULL,
            FOREIGN KEY (id_template) REFERENCES surat_template(id_template) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->pdo->exec($sqlKeluar);

        // Insert Default Categories if empty
        $check = $this->pdo->query("SELECT COUNT(*) FROM surat_kategori")->fetchColumn();
        if ($check == 0) {
            $defaultKategori = [
                ['421.1', 'Kesiswaan', 'Urusan kesiswaan, mutasi, SKL, dll'],
                ['421.2', 'Kurikulum', 'Urusan kurikulum, pembelajaran'],
                ['421.3', 'Kepegawaian', 'Surat tugas, SK guru, dll'],
                ['005', 'Undangan', 'Undangan rapat, kegiatan'],
                ['070', 'Lain-lain', 'Surat umum lainnya']
            ];
            $stmt = $this->pdo->prepare("INSERT INTO surat_kategori (kode_kategori, nama_kategori, keterangan) VALUES (?, ?, ?)");
            foreach ($defaultKategori as $kat) {
                $stmt->execute($kat);
            }
        }
    }

    // --- KATEGORI ---
    public function getKategori() {
        return $this->pdo->query("SELECT * FROM surat_kategori ORDER BY kode_kategori ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- TEMPLATE ---
    public function getTemplates() {
        return $this->pdo->query("SELECT t.*, k.nama_kategori FROM surat_template t 
                                JOIN surat_kategori k ON t.id_kategori = k.id_kategori 
                                ORDER BY t.nama_template ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTemplateById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM surat_template WHERE id_template = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- SURAT MASUK ---
    public function getSuratMasuk() {
        return $this->pdo->query("SELECT * FROM surat_masuk ORDER BY tgl_terima DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- SURAT KELUAR ---
    public function getSuratKeluar() {
        return $this->pdo->query("SELECT s.*, k.kode_kategori, t.nama_template 
                                FROM surat_keluar s 
                                LEFT JOIN surat_kategori k ON s.id_kategori = k.id_kategori 
                                LEFT JOIN surat_template t ON s.id_template = t.id_template 
                                ORDER BY s.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSuratKeluarById($id) {
        $stmt = $this->pdo->prepare("SELECT s.*, k.kode_kategori, k.nama_kategori, t.nama_template 
                                FROM surat_keluar s 
                                LEFT JOIN surat_kategori k ON s.id_kategori = k.id_kategori 
                                LEFT JOIN surat_template t ON s.id_template = t.id_template 
                                WHERE s.id_surat_keluar = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function generateNomorSurat($id_kategori) {
        $stmtKat = $this->pdo->prepare("SELECT kode_kategori FROM surat_kategori WHERE id_kategori = ?");
        $stmtKat->execute([$id_kategori]);
        $kode = $stmtKat->fetchColumn();
        
        $tahun = date('Y');
        $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM surat_keluar WHERE id_kategori = ? AND YEAR(tgl_surat) = ?");
        $stmtCount->execute([$id_kategori, $tahun]);
        $nextNum = $stmtCount->fetchColumn() + 1;
        
        $romawi = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        $bulan = $romawi[date('n')];
        
        // Format: [Nomor]/[Kode]/[Sekolah]/[Bulan]/[Tahun]
        // Contoh: 001/421.1/SMA-AM/I/2026
        return sprintf("%03d/%s/SMA-AM/%s/%d", $nextNum, $kode, $bulan, $tahun);
    }

    public function saveSuratKeluar($data) {
        $sql = "INSERT INTO surat_keluar (id_kategori, id_template, nomor_surat, tgl_surat, tujuan, perihal, isi_surat, id_referensi_siswa, id_referensi_guru, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $data['id_kategori'],
            $data['id_template'],
            $data['nomor_surat'],
            $data['tgl_surat'],
            $data['tujuan'],
            $data['perihal'],
            $data['isi_surat'],
            $data['id_referensi_siswa'] ?? null,
            $data['id_referensi_guru'] ?? null,
            'Draft'
        ]);
    }

    public function saveSuratMasuk($data) {
        $sql = "INSERT INTO surat_masuk (nomor_surat, tgl_surat, tgl_terima, asal_surat, perihal, file_scan, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $data['nomor_surat'],
            $data['tgl_surat'],
            $data['tgl_terima'],
            $data['asal_surat'],
            $data['perihal'],
            $data['file_scan'] ?? null,
            'Diterima'
        ]);
    }

    public function saveTemplate($data) {
        $sql = "INSERT INTO surat_template (id_kategori, nama_template, subjek_default, isi_template) 
                VALUES (?, ?, ?, ?)";
        return $this->pdo->prepare($sql)->execute([
            $data['id_kategori'],
            $data['nama_template'],
            $data['subjek_default'],
            $data['isi_template']
        ]);
    }

    public function parseTemplate($text, $siswa_id = null, $guru_id = null) {
        $vars = [
            '{{tgl_sekarang}}' => date('d F Y'),
            '{{hari_sekarang}}' => date('l'),
        ];

        if ($siswa_id) {
            $s = $this->pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
            $s->execute([$siswa_id]);
            $siswa = $s->fetch(PDO::FETCH_ASSOC);
            if ($siswa) {
                $vars['{{nama_siswa}}'] = $siswa['nama_siswa'];
                $vars['{{nisn}}'] = $siswa['nisn'];
                $vars['{{kelas}}'] = $this->pdo->query("SELECT nama_kelas FROM kelas WHERE id_kelas = (SELECT id_kelas FROM penempatan WHERE id_siswa = ".$siswa_id." ORDER BY id_penempatan DESC LIMIT 1)")->fetchColumn();
            }
        }

        if ($guru_id) {
            $g = $this->pdo->prepare("SELECT * FROM guru WHERE id_guru = ?");
            $g->execute([$guru_id]);
            $guru = $g->fetch(PDO::FETCH_ASSOC);
            if ($guru) {
                $vars['{{nama_guru}}'] = $guru['nama'];
                $vars['{{nip}}'] = $guru['nip'] ?? $guru['kode_guru'];
            }
        }

        return str_replace(array_keys($vars), array_values($vars), $text);
    }
}
?>
