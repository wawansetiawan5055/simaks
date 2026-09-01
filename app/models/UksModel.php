<?php
// app/models/UksModel.php

class UksModel
{
    /**
     * Memastikan tabel database uks_kunjungan, uks_obat, dan menu UKS tersedia
     */
    public static function initTables($pdo)
    {
        try {
            // 1. Tabel Kunjungan / Rekam Medis Pasien UKS
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS uks_kunjungan (
                    id_kunjungan INT AUTO_INCREMENT PRIMARY KEY,
                    id_ta INT NOT NULL DEFAULT 0,
                    tanggal DATE NOT NULL,
                    jam_masuk TIME NOT NULL,
                    jam_keluar TIME NULL,
                    tipe_pasien ENUM('Siswa', 'Guru', 'Tendik') DEFAULT 'Siswa',
                    id_pasien INT NULL,
                    nama_pasien VARCHAR(150) NOT NULL,
                    kelas_unit VARCHAR(50) NOT NULL,
                    keluhan TEXT NOT NULL,
                    suhu_tubuh VARCHAR(10) NULL,
                    tekanan_darah VARCHAR(15) NULL,
                    diagnosa_awal VARCHAR(255) NULL,
                    tindakan TEXT NULL,
                    obat_diberikan VARCHAR(255) NULL,
                    status_tindak_lanjut ENUM('Kembali ke Kelas', 'Istirahat di UKS', 'Rujuk ke Puskesmas/RS', 'Pulang ke Rumah') DEFAULT 'Kembali ke Kelas',
                    petugas_jaga VARCHAR(100) NULL,
                    keterangan TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (id_ta),
                    INDEX (tanggal),
                    INDEX (tipe_pasien)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 2. Tabel Inventaris Obat & Alkes UKS
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS uks_obat (
                    id_obat INT AUTO_INCREMENT PRIMARY KEY,
                    id_ta INT NOT NULL DEFAULT 0,
                    kode_obat VARCHAR(50) NULL,
                    nama_obat VARCHAR(150) NOT NULL,
                    kategori ENUM('Obat Bebas', 'Obat Keras/Resep', 'P3K & Alkes', 'Vitamin & Suplemen', 'Lainnya') DEFAULT 'Obat Bebas',
                    satuan VARCHAR(30) NOT NULL DEFAULT 'Tablet',
                    stok INT NOT NULL DEFAULT 0,
                    stok_minimum INT NOT NULL DEFAULT 5,
                    tgl_kadaluarsa DATE NULL,
                    kegunaan_indikasi TEXT NULL,
                    catatan TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (id_ta)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // 3. Auto-Register Menu di app_menu jika belum ada
            $stmt = $pdo->prepare("SELECT id_menu FROM app_menu WHERE link = 'uks' LIMIT 1");
            $stmt->execute();
            $id_menu_uks = $stmt->fetchColumn();

            if (!$id_menu_uks) {
                $urutan_max = (int)$pdo->query("SELECT MAX(urutan) FROM app_menu")->fetchColumn();
                $stmt_ins = $pdo->prepare("
                    INSERT INTO app_menu (nama_menu, link, icon, parent_id, urutan, status)
                    VALUES ('Kesehatan (UKS)', 'uks', 'fas fa-heartbeat', 0, ?, 'Aktif')
                ");
                $stmt_ins->execute([$urutan_max + 1]);
                $id_menu_uks = $pdo->lastInsertId();

                // Grant default access to Admin & Staff
                $roles = $pdo->query("SELECT id_peran FROM peran")->fetchAll(PDO::FETCH_COLUMN);
                $stmt_grant = $pdo->prepare("INSERT IGNORE INTO hak_akses (id_peran, id_menu, can_read, can_create, can_update, can_delete) VALUES (?, ?, 1, 1, 1, 1)");
                foreach ($roles as $r_id) {
                    $stmt_grant->execute([$r_id, $id_menu_uks]);
                }
            }

            // 4. Auto-Register Submenu Pembina UKS di Administrasi Jabatan GTK
            $stmt_pembina = $pdo->query("SELECT id_menu FROM app_menu WHERE link = 'tugas_tambahan/uks' LIMIT 1");
            $id_pembina = $stmt_pembina ? $stmt_pembina->fetchColumn() : null;
            if (!$id_pembina) {
                $stmt_parent = $pdo->query("SELECT id_menu FROM app_menu WHERE nama_menu = 'Administrasi Jabatan GTK' LIMIT 1");
                $parent_id = $stmt_parent ? $stmt_parent->fetchColumn() : 0;
                if ($parent_id) {
                    $stmt_ins2 = $pdo->prepare("
                        INSERT INTO app_menu (nama_menu, link, icon, parent_id, urutan, status)
                        VALUES ('Pembina UKS', 'tugas_tambahan/uks', 'fas fa-heartbeat', ?, 41, 'Aktif')
                    ");
                    $stmt_ins2->execute([$parent_id]);
                    $id_pembina = $pdo->lastInsertId();

                    $roles = $pdo->query("SELECT id_peran FROM peran")->fetchAll(PDO::FETCH_COLUMN);
                    $stmt_grant2 = $pdo->prepare("INSERT IGNORE INTO hak_akses (id_peran, id_menu, can_read, can_create, can_update, can_delete) VALUES (?, ?, 1, 1, 1, 1)");
                    foreach ($roles as $r_id) {
                        $stmt_grant2->execute([$r_id, $id_pembina]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Init UKS Tables Error: " . $e->getMessage());
        }
    }

    /**
     * Mengambil daftar kunjungan pasien berobat
     */
    public static function getKunjunganList($pdo, $id_ta, $filters = [])
    {
        self::initTables($pdo);
        $sql = "SELECT k.*, 
                       DATE_FORMAT(k.tanggal, '%d/%m/%Y') as tgl_indo
                FROM uks_kunjungan k
                WHERE 1=1";
        $params = [];

        if (!empty($id_ta)) {
            $sql .= " AND k.id_ta = ?";
            $params[] = $id_ta;
        }

        if (!empty($filters['tanggal'])) {
            $sql .= " AND k.tanggal = ?";
            $params[] = $filters['tanggal'];
        }

        if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
            $sql .= " AND MONTH(k.tanggal) = ? AND YEAR(k.tanggal) = ?";
            $params[] = $filters['bulan'];
            $params[] = $filters['tahun'];
        }

        if (!empty($filters['tipe_pasien'])) {
            $sql .= " AND k.tipe_pasien = ?";
            $params[] = $filters['tipe_pasien'];
        }

        $sql .= " ORDER BY k.tanggal DESC, k.jam_masuk DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getKunjunganById($pdo, $id)
    {
        self::initTables($pdo);
        $stmt = $pdo->prepare("SELECT * FROM uks_kunjungan WHERE id_kunjungan = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveKunjungan($pdo, $data)
    {
        self::initTables($pdo);
        $id = $data['id_kunjungan'] ?? null;

        if ($id) {
            $sql = "UPDATE uks_kunjungan SET 
                        id_ta = ?, tanggal = ?, jam_masuk = ?, jam_keluar = ?, tipe_pasien = ?, 
                        id_pasien = ?, nama_pasien = ?, kelas_unit = ?, keluhan = ?, suhu_tubuh = ?, 
                        tekanan_darah = ?, diagnosa_awal = ?, tindakan = ?, obat_diberikan = ?, 
                        status_tindak_lanjut = ?, petugas_jaga = ?, keterangan = ?
                    WHERE id_kunjungan = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_ta'], $data['tanggal'], $data['jam_masuk'], $data['jam_keluar'] ?: null, $data['tipe_pasien'],
                $data['id_pasien'] ?: null, $data['nama_pasien'], $data['kelas_unit'], $data['keluhan'], $data['suhu_tubuh'] ?: null,
                $data['tekanan_darah'] ?: null, $data['diagnosa_awal'] ?: null, $data['tindakan'] ?: null, $data['obat_diberikan'] ?: null,
                $data['status_tindak_lanjut'], $data['petugas_jaga'] ?: null, $data['keterangan'] ?: null,
                $id
            ]);
        } else {
            $sql = "INSERT INTO uks_kunjungan (
                        id_ta, tanggal, jam_masuk, jam_keluar, tipe_pasien, id_pasien, nama_pasien, 
                        kelas_unit, keluhan, suhu_tubuh, tekanan_darah, diagnosa_awal, tindakan, 
                        obat_diberikan, status_tindak_lanjut, petugas_jaga, keterangan
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_ta'], $data['tanggal'], $data['jam_masuk'], $data['jam_keluar'] ?: null, $data['tipe_pasien'],
                $data['id_pasien'] ?: null, $data['nama_pasien'], $data['kelas_unit'], $data['keluhan'], $data['suhu_tubuh'] ?: null,
                $data['tekanan_darah'] ?: null, $data['diagnosa_awal'] ?: null, $data['tindakan'] ?: null, $data['obat_diberikan'] ?: null,
                $data['status_tindak_lanjut'], $data['petugas_jaga'] ?: null, $data['keterangan'] ?: null
            ]);
        }
    }

    public static function deleteKunjungan($pdo, $id)
    {
        self::initTables($pdo);
        $stmt = $pdo->prepare("DELETE FROM uks_kunjungan WHERE id_kunjungan = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Mengambil daftar obat & alkes UKS
     */
    public static function getObatList($pdo, $id_ta)
    {
        self::initTables($pdo);
        $stmt = $pdo->prepare("SELECT * FROM uks_obat WHERE id_ta = ? ORDER BY nama_obat ASC");
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getObatById($pdo, $id)
    {
        self::initTables($pdo);
        $stmt = $pdo->prepare("SELECT * FROM uks_obat WHERE id_obat = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveObat($pdo, $data)
    {
        self::initTables($pdo);
        $id = $data['id_obat'] ?? null;

        if ($id) {
            $sql = "UPDATE uks_obat SET 
                        id_ta = ?, kode_obat = ?, nama_obat = ?, kategori = ?, satuan = ?, 
                        stok = ?, stok_minimum = ?, tgl_kadaluarsa = ?, kegunaan_indikasi = ?, catatan = ?
                    WHERE id_obat = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_ta'], $data['kode_obat'] ?: null, $data['nama_obat'], $data['kategori'], $data['satuan'],
                (int)$data['stok'], (int)($data['stok_minimum'] ?? 5), $data['tgl_kadaluarsa'] ?: null, $data['kegunaan_indikasi'] ?: null, $data['catatan'] ?: null,
                $id
            ]);
        } else {
            $sql = "INSERT INTO uks_obat (
                        id_ta, kode_obat, nama_obat, kategori, satuan, stok, stok_minimum, tgl_kadaluarsa, kegunaan_indikasi, catatan
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_ta'], $data['kode_obat'] ?: null, $data['nama_obat'], $data['kategori'], $data['satuan'],
                (int)$data['stok'], (int)($data['stok_minimum'] ?? 5), $data['tgl_kadaluarsa'] ?: null, $data['kegunaan_indikasi'] ?: null, $data['catatan'] ?: null
            ]);
        }
    }

    public static function deleteObat($pdo, $id)
    {
        self::initTables($pdo);
        $stmt = $pdo->prepare("DELETE FROM uks_obat WHERE id_obat = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Mengambil statistik rekapitulasi UKS
     */
    public static function getRekapStats($pdo, $id_ta)
    {
        self::initTables($pdo);
        $stats = [
            'total_kunjungan' => 0,
            'kunjungan_hari_ini' => 0,
            'total_istirahat' => 0,
            'total_rujuk' => 0,
            'total_obat_tersedia' => 0,
            'obat_hampir_habis' => 0,
            'obat_kadaluarsa' => 0
        ];

        // Total kunjungan TA aktif
        $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM uks_kunjungan WHERE id_ta = ?");
        $stmt1->execute([$id_ta]);
        $stats['total_kunjungan'] = (int)$stmt1->fetchColumn();

        // Kunjungan hari ini
        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM uks_kunjungan WHERE id_ta = ? AND tanggal = CURRENT_DATE()");
        $stmt2->execute([$id_ta]);
        $stats['kunjungan_hari_ini'] = (int)$stmt2->fetchColumn();

        // Total istirahat & rujuk
        $stmt3 = $pdo->prepare("SELECT 
                                    SUM(CASE WHEN status_tindak_lanjut = 'Istirahat di UKS' THEN 1 ELSE 0 END) as istirahat,
                                    SUM(CASE WHEN status_tindak_lanjut = 'Rujuk ke Puskesmas/RS' THEN 1 ELSE 0 END) as rujuk
                                FROM uks_kunjungan WHERE id_ta = ?");
        $stmt3->execute([$id_ta]);
        $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $stats['total_istirahat'] = (int)($row3['istirahat'] ?? 0);
        $stats['total_rujuk'] = (int)($row3['rujuk'] ?? 0);

        // Statistik obat
        $stmt4 = $pdo->prepare("SELECT 
                                    COUNT(*) as total_obat,
                                    SUM(CASE WHEN stok <= stok_minimum THEN 1 ELSE 0 END) as hampir_habis,
                                    SUM(CASE WHEN tgl_kadaluarsa IS NOT NULL AND tgl_kadaluarsa <= CURRENT_DATE() THEN 1 ELSE 0 END) as expired
                                FROM uks_obat WHERE id_ta = ?");
        $stmt4->execute([$id_ta]);
        $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $stats['total_obat_tersedia'] = (int)($row4['total_obat'] ?? 0);
        $stats['obat_hampir_habis'] = (int)($row4['hampir_habis'] ?? 0);
        $stats['obat_kadaluarsa'] = (int)($row4['expired'] ?? 0);

        return $stats;
    }
}
