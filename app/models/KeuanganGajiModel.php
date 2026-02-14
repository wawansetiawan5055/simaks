<?php
// app/models/KeuanganGajiModel.php

class KeuanganGajiModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initV4Tables();
    }

    public function initV4Tables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS keuangan_tarif_general (
            id INT PRIMARY KEY DEFAULT 1,
            tarif_jjm DECIMAL(15,2) DEFAULT 0,
            tarif_transport DECIMAL(15,2) DEFAULT 0,
            tarif_kinerja DECIMAL(15,2) DEFAULT 0,
            tunj_kepsek DECIMAL(15,2) DEFAULT 0,
            tunj_tas DECIMAL(15,2) DEFAULT 0,
            tunj_plk DECIMAL(15,2) DEFAULT 0,
            tunj_penjaga DECIMAL(15,2) DEFAULT 0,
            tunj_satpam DECIMAL(15,2) DEFAULT 0,
            tunj_sopir DECIMAL(15,2) DEFAULT 0,
            tunj_waka_kurikulum DECIMAL(15,2) DEFAULT 0,
            tunj_waka_kesiswaan DECIMAL(15,2) DEFAULT 0,
            tunj_waka_humas DECIMAL(15,2) DEFAULT 0,
            tunj_kepala_lab DECIMAL(15,2) DEFAULT 0,
            tunj_kepala_perpus DECIMAL(15,2) DEFAULT 0,
            tunj_operator DECIMAL(15,2) DEFAULT 0,
            tunj_pembina_keagamaan DECIMAL(15,2) DEFAULT 0,
            tunj_pengelola_smater DECIMAL(15,2) DEFAULT 0,
            tarif_ekskul_global DECIMAL(15,2) DEFAULT 0,
            tunj_walas DECIMAL(15,2) DEFAULT 0
        );
        CREATE TABLE IF NOT EXISTS keuangan_tarif_ekskul (
            id_tarif_ekskul INT AUTO_INCREMENT PRIMARY KEY,
            id_kegiatan INT NOT NULL,
            nominal DECIMAL(15,2) DEFAULT 0,
            UNIQUE KEY (id_kegiatan)
        );
        INSERT IGNORE INTO keuangan_tarif_general (id) VALUES (1);
        ";
        try {
            $this->pdo->exec($sql);
        } catch(PDOException $e) {
            // ignore if exists
        }

        // Fix: Add tunj_waka_humas if missing (Migration)
        try {
            $this->pdo->exec("ALTER TABLE keuangan_tarif_general ADD COLUMN tunj_waka_humas DECIMAL(15,2) DEFAULT 0 AFTER tunj_waka_kesiswaan");
        } catch(PDOException $e) {
            // Ignore if column already exists
        }

        // V4.1 Migration: Add separated allowance columns to keuangan_gaji_rules
        // Check and add columns one by one with proper error handling
        $table_rules = 'keuangan_gaji_rules';
        $table_detail = 'keuangan_gaji_detail';
        $table_general = 'keuangan_tarif_general';
        
        // Add jml_ekskul to detail table for transparency
        try {
            $this->pdo->exec("ALTER TABLE `$table_detail` ADD COLUMN `jml_ekskul` INT DEFAULT 0 AFTER `subtotal_kinerja` ");
        } catch(PDOException $e) {}

        $newColumns = [
            'tunj_kepsek',
            'tunj_tas',
            'tunj_plk',
            'tunj_penjaga',
            'tunj_satpam',
            'tunj_sopir',
            'tunj_kurikulum', 
            'tunj_kesiswaan',
            'tunj_sarpras',
            'tunj_humas',
            'tunj_kepala_lab',
            'tunj_kepala_perpus',
            'tunj_operator',
            'tunj_pembina_keagamaan',
            'tunj_pengelola_smater',
            'tunj_ekskul',
            'tunj_walas',
            'tunj_pembina'
        ];

        foreach([$table_rules, $table_detail, $table_general] as $table) {
            foreach($newColumns as $col) {
                try {
                    // Check if column exists
                    $check = $this->pdo->query("SHOW COLUMNS FROM `$table` LIKE '$col'")->fetchAll();
                    if(empty($check)) {
                        // Column doesn't exist, add it
                        $this->pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` DECIMAL(15,2) DEFAULT 0");
                    }
                } catch(PDOException $e) {
                    // Continue if any error (table might not exist yet, etc)
                }
            }
        }

        // Add id_ta to keuangan_gaji header table
        try {
            $check = $this->pdo->query("SHOW COLUMNS FROM `keuangan_gaji` LIKE 'id_ta'")->fetchAll();
            if(empty($check)) {
                $this->pdo->exec("ALTER TABLE `keuangan_gaji` ADD COLUMN `id_ta` INT AFTER `tahun` ");
            }
        } catch(PDOException $e) {}

        // Create Master Jabatan table
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS `keuangan_master_jabatan` (
                `id_jabatan` INT AUTO_INCREMENT PRIMARY KEY,
                `nama_jabatan` VARCHAR(100) NOT NULL,
                `kategori` ENUM('GURU', 'STAFF') NOT NULL
            )");

            // Initial Data for Master Jabatan (if empty)
            $checkJabatan = $this->pdo->query("SELECT COUNT(*) FROM `keuangan_master_jabatan`")->fetchColumn();
            if ($checkJabatan == 0) {
                $initialJabatan = [
                    ['Waka Kurikulum', 'GURU'],
                    ['Waka Kesiswaan', 'GURU'],
                    ['Waka Humas', 'GURU'],
                    ['Waka Sarpras', 'GURU'],
                    ['Kepala Laboratorium', 'GURU'],
                    ['Kepala Perpustakaan', 'GURU'],
                    ['Bendahara BOS', 'GURU'],
                    ['Guru Piket', 'GURU'],
                    ['Kepala Sekolah', 'STAFF'],
                    ['Tenaga Administrasi', 'STAFF'],
                    ['Petugas Layanan Khusus', 'STAFF'],
                    ['Penjaga Sekolah', 'STAFF'],
                    ['Satpam', 'STAFF'],
                    ['Sopir', 'STAFF'],
                    ['Operator', 'STAFF'],
                    ['Pembina Keagamaan', 'STAFF'],
                    ['Pengelola Smater', 'STAFF']
                ];
                $stmt = $this->pdo->prepare("INSERT INTO `keuangan_master_jabatan` (nama_jabatan, kategori) VALUES (?, ?)");
                foreach ($initialJabatan as $j) {
                    $stmt->execute($j);
                }
            }
        } catch (PDOException $e) {}
    }
    
    // --- V4 CONFIG ---
    public function getV4Config() {
        $stmt = $this->pdo->query("SELECT * FROM keuangan_tarif_general WHERE id=1");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    
    public function saveV4Config($data) {
        // Upsert ID 1
        $sql = "INSERT INTO keuangan_tarif_general 
                (id, tarif_jjm, tarif_transport, tarif_kinerja, 
                 tunj_kepsek, tunj_tas, tunj_plk, tunj_penjaga, tunj_satpam, tunj_sopir,
                 tunj_waka_kurikulum, tunj_waka_kesiswaan, tunj_sarpras, tunj_waka_humas, 
                 tunj_kepala_lab, tunj_kepala_perpus, tunj_operator, tunj_pembina_keagamaan, tunj_pengelola_smater,
                 tarif_ekskul_global, tunj_walas)
                VALUES (1, :jjm, :trans, :kin, 
                        :kepsek, :tas, :plk, :penjaga, :satpam, :sopir,
                        :kur, :kes, :sar, :hum, 
                        :lab, :per, :op, :pemkeg, :smater,
                        :eko, :walas)
                ON DUPLICATE KEY UPDATE
                tarif_jjm=VALUES(tarif_jjm), tarif_transport=VALUES(tarif_transport), tarif_kinerja=VALUES(tarif_kinerja), 
                tunj_kepsek=VALUES(tunj_kepsek), tunj_tas=VALUES(tunj_tas), tunj_plk=VALUES(tunj_plk),
                tunj_penjaga=VALUES(tunj_penjaga), tunj_satpam=VALUES(tunj_satpam), tunj_sopir=VALUES(tunj_sopir),
                tunj_waka_kurikulum=VALUES(tunj_waka_kurikulum), tunj_waka_kesiswaan=VALUES(tunj_waka_kesiswaan), 
                tunj_sarpras=VALUES(tunj_sarpras), tunj_waka_humas=VALUES(tunj_waka_humas),
                tunj_kepala_lab=VALUES(tunj_kepala_lab), tunj_kepala_perpus=VALUES(tunj_kepala_perpus), 
                tunj_operator=VALUES(tunj_operator), tunj_pembina_keagamaan=VALUES(tunj_pembina_keagamaan), tunj_pengelola_smater=VALUES(tunj_pengelola_smater),
                tarif_ekskul_global=VALUES(tarif_ekskul_global),
                tunj_walas=VALUES(tunj_walas)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'jjm' => $data['tarif_jjm'], 'trans' => $data['tarif_transport'], 'kin' => $data['tarif_kinerja'],
            'kepsek' => $data['tunj_kepsek'], 'tas' => $data['tunj_tas'], 'plk' => $data['tunj_plk'] ?? 0,
            'penjaga' => $data['tunj_penjaga'] ?? 0, 'satpam' => $data['tunj_satpam'] ?? 0, 'sopir' => $data['tunj_sopir'] ?? 0,
            'kur' => $data['tunj_waka_kurikulum'], 'kes' => $data['tunj_waka_kesiswaan'], 
            'sar' => $data['tunj_sarpras'] ?? 0, 'hum' => $data['tunj_waka_humas'],
            'lab' => $data['tunj_kepala_lab'], 'per' => $data['tunj_kepala_perpus'], 
            'op' => $data['tunj_operator'] ?? 0, 'pemkeg' => $data['tunj_pembina_keagamaan'] ?? 0, 'smater' => $data['tunj_pengelola_smater'] ?? 0,
            'eko' => $data['tarif_ekskul_global'],
            'walas' => $data['tunj_walas'] ?? 0
        ]);
    }

    public function getMasterEkskul() {
        try {
            return $this->pdo->query("SELECT * FROM master_kegiatan WHERE jenis_kegiatan='Ekstrakurikuler' ORDER BY nama_kegiatan ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getV4EkskulRates() {
        $rates = [];
        $rows = $this->pdo->query("SELECT * FROM keuangan_tarif_ekskul")->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $r) {
            $rates[$r['id_kegiatan']] = $r['nominal'];
        }
        return $rates;
    }

    public function saveV4EkskulRates($rates) {
        $stmt = $this->pdo->prepare("INSERT INTO keuangan_tarif_ekskul (id_kegiatan, nominal) VALUES (?, ?) ON DUPLICATE KEY UPDATE nominal=VALUES(nominal)");
        foreach($rates as $id_keg => $nom) {
            $stmt->execute([$id_keg, $nom]);
        }
    }

    // --- RULES ---
    public function getRules($id_guru = 0) {
        $stmt = $this->pdo->prepare("SELECT * FROM keuangan_gaji_rules WHERE id_guru = ?");
        $stmt->execute([$id_guru]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'tarif_jjm' => 0, 'tarif_transport' => 0, 'tarif_kinerja' => 0,
            'tunj_kepsek' => 0, 'tunj_kurikulum' => 0, 'tunj_kesiswaan' => 0, 'tunj_sarpras' => 0,
            'tunj_humas' => 0, 'tunj_kepala_lab' => 0, 'tunj_kepala_perpus' => 0, 
            'tunj_walas' => 0, 'tunj_ekskul' => 0,
            'tunj_pembina' => 0, 'tunjangan_lain' => 0,
            'potongan_bpjs_kes' => 0, 'potongan_bpjs_tk' => 0, 
            'potongan_kasbon' => 0, 'potongan_lain' => 0
        ];
    }
    
    public function getAllRulesWithTeachers() {
        // Left Join to get all active teachers, coalesce nulls to 0
        $sql = "SELECT g.id_guru, g.nama, g.kode_guru,
                       COALESCE(r.tarif_jjm, 0) as tarif_jjm,
                       COALESCE(r.tarif_transport, 0) as tarif_transport,
                       COALESCE(r.tarif_kinerja, 0) as tarif_kinerja,
                       COALESCE(r.tunj_kepsek, 0) as tunj_kepsek,
                       COALESCE(r.tunj_tas, 0) as tunj_tas,
                       COALESCE(r.tunj_plk, 0) as tunj_plk,
                       COALESCE(r.tunj_penjaga, 0) as tunj_penjaga,
                       COALESCE(r.tunj_satpam, 0) as tunj_satpam,
                       COALESCE(r.tunj_sopir, 0) as tunj_sopir,
                       COALESCE(r.tunj_kurikulum, 0) as tunj_kurikulum,
                       COALESCE(r.tunj_kesiswaan, 0) as tunj_kesiswaan,
                       COALESCE(r.tunj_sarpras, 0) as tunj_sarpras,
                       COALESCE(r.tunj_humas, 0) as tunj_humas,
                       COALESCE(r.tunj_kepala_lab, 0) as tunj_kepala_lab,
                       COALESCE(r.tunj_kepala_perpus, 0) as tunj_kepala_perpus,
                       COALESCE(r.tunj_operator, 0) as tunj_operator,
                       COALESCE(r.tunj_pembina_keagamaan, 0) as tunj_pembina_keagamaan,
                       COALESCE(r.tunj_pengelola_smater, 0) as tunj_pengelola_smater,
                       COALESCE(r.tunj_ekskul, 0) as tunj_ekskul,
                       COALESCE(r.tunj_walas, 0) as tunj_walas,
                       COALESCE(r.tunj_pembina, 0) as tunj_pembina,
                       COALESCE(r.tunjangan_lain, 0) as tunjangan_lain,
                       COALESCE(r.potongan_bpjs_kes, 0) as potongan_bpjs_kes,
                       COALESCE(r.potongan_bpjs_tk, 0) as potongan_bpjs_tk,
                       COALESCE(r.potongan_kasbon, 0) as potongan_kasbon,
                       COALESCE(r.potongan_lain, 0) as potongan_lain
                FROM guru g
                LEFT JOIN keuangan_gaji_rules r ON g.id_guru = r.id_guru
                WHERE g.status = 'Aktif'
                ORDER BY g.nama ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function saveRules($id_guru, $data) {
        // Upsert Rule
        // Check keys exist or default to 0
        $fields = [
            'tarif_jjm','tarif_transport','tarif_kinerja',
            'tunj_kepsek','tunj_tas','tunj_plk','tunj_penjaga','tunj_satpam','tunj_sopir',
            'tunj_kurikulum','tunj_kesiswaan','tunj_sarpras','tunj_humas',
            'tunj_kepala_lab','tunj_kepala_perpus','tunj_operator','tunj_pembina_keagamaan','tunj_pengelola_smater',
            'tunj_walas', 'tunj_ekskul','tunj_pembina','tunjangan_lain',
            'potongan_bpjs_kes','potongan_bpjs_tk','potongan_kasbon','potongan_lain'
        ];
        $values = [$id_guru];
        foreach($fields as $f) $values[] = $data[$f] ?? 0;
        
        $sql = "INSERT INTO keuangan_gaji_rules (id_guru, tarif_jjm, tarif_transport, tarif_kinerja, 
                tunj_kepsek, tunj_tas, tunj_plk, tunj_penjaga, tunj_satpam, tunj_sopir,
                tunj_kurikulum, tunj_kesiswaan, tunj_sarpras, tunj_humas, 
                tunj_kepala_lab, tunj_kepala_perpus, tunj_operator, tunj_pembina_keagamaan, tunj_pengelola_smater,
                tunj_walas, tunj_ekskul, tunj_pembina, tunjangan_lain,
                potongan_bpjs_kes, potongan_bpjs_tk, potongan_kasbon, potongan_lain)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                tarif_jjm=VALUES(tarif_jjm), tarif_transport=VALUES(tarif_transport), tarif_kinerja=VALUES(tarif_kinerja),
                tunj_kepsek=VALUES(tunj_kepsek), tunj_tas=VALUES(tunj_tas), tunj_plk=VALUES(tunj_plk), tunj_penjaga=VALUES(tunj_penjaga), tunj_satpam=VALUES(tunj_satpam), tunj_sopir=VALUES(tunj_sopir),
                tunj_kurikulum=VALUES(tunj_kurikulum), tunj_kesiswaan=VALUES(tunj_kesiswaan), tunj_sarpras=VALUES(tunj_sarpras), tunj_humas=VALUES(tunj_humas),
                tunj_kepala_lab=VALUES(tunj_kepala_lab), tunj_kepala_perpus=VALUES(tunj_kepala_perpus), tunj_operator=VALUES(tunj_operator), tunj_pembina_keagamaan=VALUES(tunj_pembina_keagamaan), tunj_pengelola_smater=VALUES(tunj_pengelola_smater),
                tunj_walas=VALUES(tunj_walas), tunj_ekskul=VALUES(tunj_ekskul), tunj_pembina=VALUES(tunj_pembina),
                tunjangan_lain=VALUES(tunjangan_lain),
                potongan_bpjs_kes=VALUES(potongan_bpjs_kes), potongan_bpjs_tk=VALUES(potongan_bpjs_tk), 
                potongan_kasbon=VALUES(potongan_kasbon), potongan_lain=VALUES(potongan_lain)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
    }
    
    // --- PERIODS ---
    public function getAllPeriods() {
        return $this->pdo->query("SELECT * FROM keuangan_gaji ORDER BY tahun DESC, bulan DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPeriodById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM keuangan_gaji WHERE id_gaji = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function checkPeriodExists($bulan, $tahun) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM keuangan_gaji WHERE bulan = ? AND tahun = ?");
        $stmt->execute([$bulan, $tahun]);
        return $stmt->fetchColumn() > 0;
    }

    public function deletePeriod($id) {
        $this->pdo->prepare("DELETE FROM keuangan_gaji WHERE id_gaji = ?")->execute([$id]);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE keuangan_gaji SET status = ? WHERE id_gaji = ?";
        return $this->pdo->prepare($sql)->execute([$status, $id]);
    }
    
    public function getDetails($id_gaji) {
        $sql = "SELECT d.*, g.nama as nama_guru, g.kode_guru as nip 
                FROM keuangan_gaji_detail d
                JOIN guru g ON d.id_guru = g.id_guru
                WHERE d.id_gaji = ?
                ORDER BY g.nama ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_gaji]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSlipDetail($id_detail) {
        $sql = "SELECT d.*, g.nama as nama_guru, g.kode_guru as nip, h.bulan, h.tahun, h.tgl_generate 
                FROM keuangan_gaji_detail d
                JOIN keuangan_gaji h ON d.id_gaji = h.id_gaji
                JOIN guru g ON d.id_guru = g.id_guru
                WHERE d.id_detail = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_detail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAssignments($id_ta) {
        // 1. Get positions from penugasan_jabatan
        $sql = "SELECT id_guru, jenis_jabatan FROM penugasan_jabatan WHERE id_ta = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $assignments = [];
        foreach($rows as $r) {
            $assignments[$r['id_guru']][] = $r['jenis_jabatan'];
        }

        // 2. Get homeroom teacher assignments from penugasan_wali_kelas
        $sqlWalas = "SELECT id_guru FROM penugasan_wali_kelas WHERE id_ta = ?";
        $stmtWalas = $this->pdo->prepare($sqlWalas);
        $stmtWalas->execute([$id_ta]);
        $walas = $stmtWalas->fetchAll(PDO::FETCH_COLUMN);

        foreach($walas as $id_guru) {
            if(!isset($assignments[$id_guru])) $assignments[$id_guru] = [];
            if(!in_array('Wali Kelas', $assignments[$id_guru])) {
                $assignments[$id_guru][] = 'Wali Kelas';
            }
        }

        return $assignments;
    }

    // --- HELPER METHODS FOR CALCULATIONS ---

    public function getJJMWeekly($id_guru, $id_ta)
    {
        // Hitung total jam MENGAJAR per minggu dari jadwal pelajaran
        // Asumsi: tabel jadwal_mapel menyimpan jadwal mingguan
        // Jika tabelnya 'jadwal_mengajar', sesuaikan. 
        // Berdasarkan code sebelumnya: jadwal_mengajar
        $sql = "SELECT COUNT(*) FROM jadwal_mengajar dm 
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel 
                WHERE gm.id_guru = ? AND gm.id_ta = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_ta]);
        return $stmt->fetchColumn();
    }

    public function getEkskulAttendance($id_guru, $bulan, $tahun)
    {
        // Hitung kehadiran pembina dari Jurnal Ekstrakurikuler bulan ini
        // Asumsi tabel jurnal_ekstrakurikuler ada kolom id_guru
        $sql = "SELECT COUNT(*) FROM jurnal_ekstrakurikuler 
                WHERE id_guru = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_guru, $bulan, $tahun]);
        return $stmt->fetchColumn();
    }

    public function getJabatanCount($id_guru, $id_ta, $jenis_jabatan)
    {
        // Hitung tugas tambahan (Wakasek, Kaprog, Kepala Lab, dll)
        $sql = "SELECT COUNT(*) FROM penugasan_jabatan 
                WHERE id_guru = ? AND id_ta = ? AND jenis_jabatan LIKE ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_ta, "%$jenis_jabatan%"]);
        return $stmt->fetchColumn();
    }
    
    // --- CORE GENERATOR ---
    public function generateGaji($bulan, $tahun, $id_ta) {
        // 1. Cek apakah sudah ada untuk periode ini?
        $cek = $this->pdo->prepare("SELECT id_gaji FROM keuangan_gaji WHERE bulan=? AND tahun=?");
        $cek->execute([$bulan, $tahun]);
        if($cek->rowCount() > 0) {
            return ['status' => 'error', 'message' => 'Gaji periode ini sudah digenerate. Silakan hapus dulu jika ingin ulang.'];
        }

        // 2. Create Payroll Header
        $stmt = $this->pdo->prepare("INSERT INTO keuangan_gaji (id_ta, bulan, tahun, tgl_generate, status, total_pengeluaran) VALUES (?, ?, ?, NOW(), 'DRAFT', 0)");
        $stmt->execute([$id_ta, $bulan, $tahun]);
        $id_gaji = $this->pdo->lastInsertId();

        $total_pengeluaran = $this->processAllGurus($id_gaji, $bulan, $tahun, $id_ta);

        // Update Total Header
        $upd = $this->pdo->prepare("UPDATE keuangan_gaji SET total_pengeluaran = ?, tgl_generate = NOW() WHERE id_gaji = ?");
        $upd->execute([$total_pengeluaran, $id_gaji]);

        return ['status' => 'success', 'count' => 'all'];
    }

    public function regenerateGaji($id_gaji) {
        // 1. Get existing header info
        $stmt = $this->pdo->prepare("SELECT bulan, tahun, id_ta, status FROM keuangan_gaji WHERE id_gaji = ?");
        $stmt->execute([$id_gaji]);
        $gaji = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$gaji) return ['status' => 'error', 'message' => 'Data gaji tidak ditemukan'];
        if ($gaji['status'] !== 'DRAFT') return ['status' => 'error', 'message' => 'Hanya gaji berstatus DRAFT yang dapat hitung ulang'];

        // 2. Clear existing details
        $this->pdo->prepare("DELETE FROM keuangan_gaji_detail WHERE id_gaji = ?")->execute([$id_gaji]);

        // 3. Re-process
        $total_pengeluaran = $this->processAllGurus($id_gaji, $gaji['bulan'], $gaji['tahun'], $gaji['id_ta']);

        // 4. Update Header
        $upd = $this->pdo->prepare("UPDATE keuangan_gaji SET total_pengeluaran = ?, tgl_generate = NOW() WHERE id_gaji = ?");
        $upd->execute([$total_pengeluaran, $id_gaji]);

        return ['status' => 'success'];
    }

    private function processAllGurus($id_gaji, $bulan, $tahun, $id_ta) {
        $cfg = $this->getV4Config();
        $ekskulRates = $this->getV4EkskulRates();
        $gurus = $this->pdo->query("SELECT id_guru, nama FROM guru WHERE status='Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
        $allAssignments = $this->getAssignments($id_ta);

        $total_pengeluaran = 0;

        foreach($gurus as $g) {
            $id_guru = $g['id_guru'];
            $assigns = $allAssignments[$id_guru] ?? [];
            $rule = $this->getRules($id_guru);
            
            // --- A. HONORARIUM MENGAJAR ---
            $jml_jjm = $this->getJJMWeekly($id_guru, $id_ta);
            $t_jjm = $rule['tarif_jjm'] > 0 ? $rule['tarif_jjm'] : ($cfg['tarif_jjm'] ?? 0);
            $sub_jjm = $jml_jjm * $t_jjm;

            $stmtHadir = $this->pdo->prepare("SELECT COUNT(*) FROM absensi_guru WHERE id_guru = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? AND status = 'Hadir'");
            $stmtHadir->execute([$id_guru, $bulan, $tahun]);
            $jml_hadir = $stmtHadir->fetchColumn();
            $t_transport = $rule['tarif_transport'] > 0 ? $rule['tarif_transport'] : ($cfg['tarif_transport'] ?? 0);
            $sub_transport = $jml_hadir * $t_transport;

            $stmtKBM = $this->pdo->prepare("SELECT COUNT(*) FROM jurnal_kbm WHERE id_guru = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
            $stmtKBM->execute([$id_guru, $bulan, $tahun]);
            $jml_kbm = $stmtKBM->fetchColumn();
            $t_kinerja = $rule['tarif_kinerja'] > 0 ? $rule['tarif_kinerja'] : ($cfg['tarif_kinerja'] ?? 0);
            $sub_kinerja = $jml_kbm * $t_kinerja;
            
            // --- B. KOMPONEN PENDAPATAN ---
            $t_kepsek = in_array('Kepala Sekolah', $assigns) ? ($rule['tunj_kepsek'] > 0 ? $rule['tunj_kepsek'] : $cfg['tunj_kepsek']) : 0;
            $t_tas = in_array('Tenaga Administrasi', $assigns) ? ($rule['tunj_tas'] > 0 ? $rule['tunj_tas'] : $cfg['tunj_tas']) : 0;
            $t_plk = in_array('Petugas Layanan Khusus', $assigns) ? ($rule['tunj_plk'] > 0 ? $rule['tunj_plk'] : $cfg['tunj_plk']) : 0;
            $t_penjaga = in_array('Penjaga Sekolah', $assigns) ? ($rule['tunj_penjaga'] > 0 ? $rule['tunj_penjaga'] : $cfg['tunj_penjaga']) : 0;
            $t_satpam = in_array('Satpam', $assigns) ? ($rule['tunj_satpam'] > 0 ? $rule['tunj_satpam'] : $cfg['tunj_satpam']) : 0;
            $t_sopir = in_array('Sopir', $assigns) ? ($rule['tunj_sopir'] > 0 ? $rule['tunj_sopir'] : $cfg['tunj_sopir']) : 0;
            
            $t_kurikulum = in_array('Waka Kurikulum', $assigns) ? ($rule['tunj_kurikulum'] > 0 ? $rule['tunj_kurikulum'] : $cfg['tunj_waka_kurikulum']) : 0;
            $t_kesiswaan = in_array('Waka Kesiswaan', $assigns) ? ($rule['tunj_kesiswaan'] > 0 ? $rule['tunj_kesiswaan'] : $cfg['tunj_waka_kesiswaan']) : 0;
            $t_sarpras = in_array('Waka Sarpras', $assigns) ? ($rule['tunj_sarpras'] > 0 ? $rule['tunj_sarpras'] : $cfg['tunj_sarpras']) : 0;
            $t_humas = in_array('Waka Humas', $assigns) ? ($rule['tunj_humas'] > 0 ? $rule['tunj_humas'] : $cfg['tunj_waka_humas']) : 0;
            
            $t_kepala_lab = in_array('Kepala Laboratorium', $assigns) ? ($rule['tunj_kepala_lab'] > 0 ? $rule['tunj_kepala_lab'] : $cfg['tunj_kepala_lab']) : 0;
            $t_kepala_perpus = in_array('Kepala Perpustakaan', $assigns) ? ($rule['tunj_kepala_perpus'] > 0 ? $rule['tunj_kepala_perpus'] : $cfg['tunj_kepala_perpus']) : 0;
            $t_operator = in_array('Operator', $assigns) ? ($rule['tunj_operator'] > 0 ? $rule['tunj_operator'] : $cfg['tunj_operator']) : 0;
            $t_pembina_keagamaan = in_array('Pembina Keagamaan', $assigns) ? ($rule['tunj_pembina_keagamaan'] > 0 ? $rule['tunj_pembina_keagamaan'] : $cfg['tunj_pembina_keagamaan']) : 0;
            $t_pengelola_smater = in_array('Pengelola Smater', $assigns) ? ($rule['tunj_pengelola_smater'] > 0 ? $rule['tunj_pengelola_smater'] : $cfg['tunj_pengelola_smater']) : 0;
            $t_walas = in_array('Wali Kelas', $assigns) ? ($rule['tunj_walas'] > 0 ? $rule['tunj_walas'] : $cfg['tunj_walas']) : 0;

            $t_pembina_other = $rule['tunj_pembina'];
            $t_lainnya = $rule['tunjangan_lain'];

            // --- C. HONORARIUM EKSTRAKURIKULER ---
            $stmtEks = $this->pdo->prepare("SELECT id_ekskul FROM jurnal_ekstrakurikuler WHERE id_guru = ? AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
            $stmtEks->execute([$id_guru, $bulan, $tahun]);
            $journals = $stmtEks->fetchAll(PDO::FETCH_COLUMN);
            $jml_ekskul = count($journals);
            
            $sub_ekskul_calculated = 0;
            foreach($journals as $id_keg) {
                $rate = $ekskulRates[$id_keg] ?? ($cfg['tarif_ekskul_global'] ?? 0);
                $sub_ekskul_calculated += $rate;
            }
            $val_ekskul = $rule['tunj_ekskul'] + $sub_ekskul_calculated;

            // --- D. TOTALS ---
            $total_pendapatan = $sub_jjm + $sub_transport + $sub_kinerja + 
                                $t_kepsek + $t_tas + $t_plk + $t_penjaga + $t_satpam + $t_sopir +
                                $t_kurikulum + $t_kesiswaan + $t_sarpras + $t_humas + 
                                $t_kepala_lab + $t_kepala_perpus + $t_operator + $t_pembina_keagamaan + $t_pengelola_smater + $t_walas +
                                $val_ekskul + $t_pembina_other + $t_lainnya;

            $pot_bpjs_kes = $rule['potongan_bpjs_kes'];
            $pot_bpjs_tk = $rule['potongan_bpjs_tk'];
            $pot_kasbon = $rule['potongan_kasbon'];
            $pot_lain = $rule['potongan_lain'];

            $total_potongan = $pot_bpjs_kes + $pot_bpjs_tk + $pot_kasbon + $pot_lain;
            $total_diterima = $total_pendapatan - $total_potongan;
            $total_pengeluaran += $total_diterima;

            $sqlDetail = "INSERT INTO keuangan_gaji_detail 
                (id_gaji, id_guru, jml_jjm, tarif_jjm, subtotal_jjm, jml_hadir, tarif_transport, subtotal_transport, jml_kbm, tarif_kinerja, subtotal_kinerja,
                jml_ekskul, tunj_kepsek, tunj_tas, tunj_plk, tunj_penjaga, tunj_satpam, tunj_sopir, tunj_kurikulum, tunj_kesiswaan, tunj_sarpras, tunj_humas, 
                tunj_kepala_lab, tunj_kepala_perpus, tunj_operator, tunj_pembina_keagamaan, tunj_pengelola_smater, tunj_walas, tunj_ekskul, tunj_pembina, tunjangan_lain,
                potongan_bpjs_kes, potongan_bpjs_tk, potongan_kasbon, potongan_lain, total_diterima)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->pdo->prepare($sqlDetail)->execute([
                $id_gaji, $id_guru, $jml_jjm, $t_jjm, $sub_jjm, $jml_hadir, $t_transport, $sub_transport, $jml_kbm, $t_kinerja, $sub_kinerja, $jml_ekskul,
                $t_kepsek, $t_tas, $t_plk, $t_penjaga, $t_satpam, $t_sopir, $t_kurikulum, $t_kesiswaan, $t_sarpras, $t_humas, $t_kepala_lab, $t_kepala_perpus, $t_operator, $t_pembina_keagamaan, $t_pengelola_smater, $t_walas, $val_ekskul, $t_pembina_other, $t_lainnya,
                $pot_bpjs_kes, $pot_bpjs_tk, $pot_kasbon, $pot_lain, $total_diterima
            ]);
        }
        return $total_pengeluaran;
    }
}
?>
