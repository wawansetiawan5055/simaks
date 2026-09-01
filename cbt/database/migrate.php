<?php
/**
 * CBT - Database Migration Script
 * Jalankan sekali: /cbt/database/migrate.php
 * Akan mencoba buat db_simaks_cbt, jika gagal fallback ke db_simaks
 */
$host = 'localhost';
$user = 'administrator';
$pass = '20247166';

// Coba buat DB terpisah dulu
$using_db = 'db_simaks_cbt';
$is_shared = false;

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `db_simaks_cbt` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `db_simaks_cbt`");
} catch (PDOException $e) {
    // Fallback: gunakan db_simaks (tabel tetap pakai prefix cbt_ jadi tetap terpisah)
    try {
        $pdo = new PDO("mysql:host=$host;dbname=db_simaks;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $using_db = 'db_simaks';
        $is_shared = true;
    } catch (PDOException $e2) {
        die("<h3 style='color:red'>Gagal koneksi: " . $e2->getMessage() . "</h3>");
    }
}

$tables = [];

// 1. USER ADMIN CBT
$tables['cbt_users'] = "CREATE TABLE IF NOT EXISTS `cbt_users` (
    `id_user`    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(50) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `nama`       VARCHAR(100) NOT NULL,
    `role`       ENUM('superadmin','admin','guru') NOT NULL DEFAULT 'guru',
    `is_active`  TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";

// 2. DATA MASTER STANDALONE
$tables['cbt_kelas'] = "CREATE TABLE IF NOT EXISTS `cbt_kelas` (
    `id_kelas`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_kelas` VARCHAR(50) NOT NULL,
    `tingkat`    VARCHAR(10),
    `jurusan`    VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";

$tables['cbt_mapel'] = "CREATE TABLE IF NOT EXISTS `cbt_mapel` (
    `id_mapel`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode_mapel` VARCHAR(20),
    `nama_mapel` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";

$tables['cbt_siswa'] = "CREATE TABLE IF NOT EXISTS `cbt_siswa` (
    `id_siswa`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nisn`           VARCHAR(20) NOT NULL UNIQUE,
    `nipd`           VARCHAR(20) DEFAULT NULL,
    `nama_siswa`     VARCHAR(100) NOT NULL,
    `id_kelas`       INT UNSIGNED,
    `jurusan`        VARCHAR(50) DEFAULT NULL,
    `tempat_lahir`   VARCHAR(100) DEFAULT NULL,
    `tanggal_lahir`  DATE DEFAULT NULL,
    `no_peserta`     VARCHAR(30) DEFAULT NULL,
    `ruang`          VARCHAR(50) DEFAULT NULL,
    `sesi`           VARCHAR(50) DEFAULT NULL,
    `password`       VARCHAR(255) DEFAULT NULL,
    `foto`           VARCHAR(255) DEFAULT NULL,
    `is_active`      TINYINT(1) DEFAULT 1,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_kelas`), INDEX(`no_peserta`)
) ENGINE=InnoDB;";

// 3. BANK SOAL
$tables['cbt_bank_soal'] = "CREATE TABLE IF NOT EXISTS `cbt_bank_soal` (
    `id_bank`     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_bank`   VARCHAR(150) NOT NULL,
    `kode_bank`   VARCHAR(20),
    `id_mapel`    INT UNSIGNED NOT NULL,
    `tingkat`     VARCHAR(10),
    `id_jurusan`  VARCHAR(50),
    `opsi_pg`     TINYINT UNSIGNED DEFAULT 5,
    `jml_pg`      SMALLINT UNSIGNED DEFAULT 0,
    `bobot_pg`    DECIMAL(5,2) DEFAULT 1.00,
    `jml_esai`    SMALLINT UNSIGNED DEFAULT 0,
    `bobot_esai`  DECIMAL(5,2) DEFAULT 1.00,
    `id_user`     INT UNSIGNED NOT NULL,
    `deskripsi`   TEXT,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_mapel`)
) ENGINE=InnoDB;";

$tables['cbt_soal'] = "CREATE TABLE IF NOT EXISTS `cbt_soal` (
    `id_soal`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_bank`           INT UNSIGNED NOT NULL,
    `nomor_urut`        SMALLINT UNSIGNED DEFAULT 1,
    `tipe_soal`         ENUM('pg','essay','tf') NOT NULL DEFAULT 'pg',
    `pertanyaan`        LONGTEXT NOT NULL,
    `is_acak_soal`      TINYINT(1) DEFAULT 1,
    `is_acak_opsi`      TINYINT(1) DEFAULT 1,
    `bobot`             TINYINT UNSIGNED DEFAULT 1,
    `tingkat_kesulitan` ENUM('mudah','sedang','sulit') DEFAULT 'sedang',
    `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_bank`)
) ENGINE=InnoDB;";

$tables['cbt_soal_opsi'] = "CREATE TABLE IF NOT EXISTS `cbt_soal_opsi` (
    `id_opsi`  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_soal`  INT UNSIGNED NOT NULL,
    `label`    VARCHAR(255) NOT NULL,
    `isi_opsi` TEXT NOT NULL,
    `gambar`   VARCHAR(255) DEFAULT NULL,
    `is_benar` TINYINT(1) DEFAULT 0,
    INDEX(`id_soal`)
) ENGINE=InnoDB;";

$tables['cbt_soal_media'] = "CREATE TABLE IF NOT EXISTS `cbt_soal_media` (
    `id_media`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_soal`    INT UNSIGNED NOT NULL,
    `tipe_media` ENUM('gambar','audio','video','pdf') NOT NULL,
    `nama_file`  VARCHAR(255) NOT NULL,
    `path_file`  VARCHAR(500) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_soal`)
) ENGINE=InnoDB;";

// 4. PAKET & JADWAL
$tables['cbt_paket'] = "CREATE TABLE IF NOT EXISTS `cbt_paket` (
    `id_paket`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_paket`     VARCHAR(150) NOT NULL,
    `id_mapel`       INT UNSIGNED NOT NULL,
    `id_bank`        INT UNSIGNED NOT NULL,
    `acak_soal`      TINYINT(1) DEFAULT 1,
    `acak_opsi`      TINYINT(1) DEFAULT 1,
    `jml_soal_pg`    TINYINT UNSIGNED DEFAULT 0,
    `jml_soal_essay` TINYINT UNSIGNED DEFAULT 0,
    `jml_soal_tf`    TINYINT UNSIGNED DEFAULT 0,
    `keterangan`     TEXT,
    `id_user`        INT UNSIGNED NOT NULL,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_bank`)
) ENGINE=InnoDB;";

$tables['cbt_jadwal'] = "CREATE TABLE IF NOT EXISTS `cbt_jadwal` (
    `id_jadwal`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_ujian`      VARCHAR(200) NOT NULL,
    `id_paket`        INT UNSIGNED NOT NULL,
    `id_kelas`        INT UNSIGNED NOT NULL,
    `tanggal_mulai`   DATETIME NOT NULL,
    `tanggal_selesai` DATETIME NOT NULL,
    `durasi_menit`    SMALLINT UNSIGNED DEFAULT 90,
    `pin_proktor`     VARCHAR(10) DEFAULT NULL,
    `status`          ENUM('draft','aktif','selesai') DEFAULT 'draft',
    `passing_grade`   TINYINT UNSIGNED DEFAULT 0,
    `catatan`         TEXT,
    `id_user`         INT UNSIGNED NOT NULL,
    `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_paket`), INDEX(`id_kelas`)
) ENGINE=InnoDB;";

// 5. PESERTA & JAWABAN
$tables['cbt_peserta'] = "CREATE TABLE IF NOT EXISTS `cbt_peserta` (
    `id_peserta`    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_jadwal`     INT UNSIGNED NOT NULL,
    `id_siswa`      INT UNSIGNED NOT NULL,
    `token`         VARCHAR(64) DEFAULT NULL,
    `status`        ENUM('belum','login','mengerjakan','selesai','timeout') DEFAULT 'belum',
    `waktu_mulai`   DATETIME DEFAULT NULL,
    `waktu_selesai` DATETIME DEFAULT NULL,
    `ip_address`    VARCHAR(45) DEFAULT NULL,
    UNIQUE KEY `uq_jadwal_siswa` (`id_jadwal`,`id_siswa`),
    INDEX(`id_jadwal`), INDEX(`id_siswa`)
) ENGINE=InnoDB;";

$tables['cbt_jawaban'] = "CREATE TABLE IF NOT EXISTS `cbt_jawaban` (
    `id_jawaban`    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_peserta`    INT UNSIGNED NOT NULL,
    `id_soal`       INT UNSIGNED NOT NULL,
    `id_jadwal`     INT UNSIGNED NOT NULL,
    `jawaban_pg`    CHAR(1) DEFAULT NULL,
    `jawaban_essay` TEXT DEFAULT NULL,
    `is_ragu`       TINYINT(1) DEFAULT 0,
    `is_benar`      TINYINT(1) DEFAULT NULL,
    `skor_essay`    DECIMAL(5,2) DEFAULT NULL,
    `waktu_jawab`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_peserta_soal` (`id_peserta`,`id_soal`),
    INDEX(`id_peserta`), INDEX(`id_jadwal`), INDEX(`id_soal`)
) ENGINE=InnoDB;";

$tables['cbt_log_aktivitas'] = "CREATE TABLE IF NOT EXISTS `cbt_log_aktivitas` (
    `id_log`     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_peserta` INT UNSIGNED NOT NULL,
    `id_jadwal`  INT UNSIGNED NOT NULL,
    `jenis_log`  ENUM('login','pindah_tab','fullscreen_exit','submit','timeout','reset') NOT NULL,
    `keterangan` VARCHAR(255),
    `log_time`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_peserta`), INDEX(`id_jadwal`)
) ENGINE=InnoDB;";

$tables['cbt_nilai'] = "CREATE TABLE IF NOT EXISTS `cbt_nilai` (
    `id_nilai`     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_peserta`   INT UNSIGNED NOT NULL UNIQUE,
    `id_jadwal`    INT UNSIGNED NOT NULL,
    `id_siswa`     INT UNSIGNED NOT NULL,
    `nilai_pg`     DECIMAL(5,2) DEFAULT 0,
    `nilai_essay`  DECIMAL(5,2) DEFAULT 0,
    `nilai_akhir`  DECIMAL(5,2) DEFAULT 0,
    `status_lulus` TINYINT(1) DEFAULT NULL,
    `dihitung_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`id_jadwal`), INDEX(`id_siswa`)
) ENGINE=InnoDB;";

// Eksekusi
$success = [];
$errors = [];
foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        $success[] = $name;
    } catch (PDOException $e) {
        $errors[] = "$name: " . $e->getMessage();
    }
}
// 6. MIGRATIONS FOR EXISTING TABLES
try {
    // Check if 'nis' still exists (needs rename to 'nisn')
    $check_nis = $pdo->query("SHOW COLUMNS FROM `cbt_siswa` LIKE 'nis'")->fetch();
    if ($check_nis) {
        $pdo->exec("ALTER TABLE `cbt_siswa` CHANGE `nis` `nisn` VARCHAR(20) NOT NULL");
        $success[] = "cbt_siswa: rename nis to nisn";
    }

    // Add new columns if not exist
    $new_cols = [
        'nipd' => "VARCHAR(20) DEFAULT NULL AFTER `nisn`",
        'jurusan' => "VARCHAR(50) DEFAULT NULL AFTER `id_kelas`",
        'tempat_lahir' => "VARCHAR(100) DEFAULT NULL AFTER `jurusan`",
        'tanggal_lahir' => "DATE DEFAULT NULL AFTER `tempat_lahir`",
        'no_peserta' => "VARCHAR(30) DEFAULT NULL AFTER `tanggal_lahir`",
        'ruang' => "VARCHAR(50) DEFAULT NULL AFTER `no_peserta`",
        'sesi' => "VARCHAR(50) DEFAULT NULL AFTER `ruang`"
    ];

    foreach ($new_cols as $col => $def) {
        $check = $pdo->query("SHOW COLUMNS FROM `cbt_siswa` LIKE '$col'")->fetch();
        if (!$check) {
            $pdo->exec("ALTER TABLE `cbt_siswa` ADD `$col` $def");
            $success[] = "cbt_siswa: add $col";
        }
    }

    // BANK SOAL NEW COLUMNS
    $bank_cols = [
        'kode_bank' => "VARCHAR(20) AFTER `nama_bank`",
        'tingkat' => "VARCHAR(10) AFTER `id_mapel`",
        'id_jurusan' => "VARCHAR(50) AFTER `tingkat`",
        'opsi_pg' => "TINYINT UNSIGNED DEFAULT 5 AFTER `id_jurusan`",
        'jml_pg' => "SMALLINT UNSIGNED DEFAULT 0 AFTER `opsi_pg`",
        'bobot_pg' => "DECIMAL(5,2) DEFAULT 1.00 AFTER `jml_pg`",
        'jml_esai' => "SMALLINT UNSIGNED DEFAULT 0 AFTER `bobot_pg`",
        'bobot_esai' => "DECIMAL(5,2) DEFAULT 1.00 AFTER `jml_esai`"
    ];
    foreach ($bank_cols as $col => $def) {
        $check = $pdo->query("SHOW COLUMNS FROM `cbt_bank_soal` LIKE '$col'")->fetch();
        if (!$check) {
            $pdo->exec("ALTER TABLE `cbt_bank_soal` ADD `$col` $def");
            $success[] = "cbt_bank_soal: add $col";
        }
    }

    // CBT_SOAL TIPE_SOAL ENUM UPDATE
    $pdo->exec("ALTER TABLE `cbt_soal_opsi` MODIFY COLUMN `label` VARCHAR(255) NOT NULL");

    // Professional Media & Randomization
    $check_soal_acak = $pdo->query("SHOW COLUMNS FROM `cbt_soal` LIKE 'is_acak_soal'")->fetch();
    if (!$check_soal_acak) {
        $pdo->exec("ALTER TABLE `cbt_soal` ADD `is_acak_soal` TINYINT(1) DEFAULT 1 AFTER `pertanyaan` ");
        $pdo->exec("ALTER TABLE `cbt_soal` ADD `is_acak_opsi` TINYINT(1) DEFAULT 1 AFTER `is_acak_soal` ");
        $success[] = "cbt_soal: add is_acak_soal, is_acak_opsi";
    }

    $check_opsi_gambar = $pdo->query("SHOW COLUMNS FROM `cbt_soal_opsi` LIKE 'gambar'")->fetch();
    if (!$check_opsi_gambar) {
        $pdo->exec("ALTER TABLE `cbt_soal_opsi` ADD `gambar` VARCHAR(255) DEFAULT NULL AFTER `isi_opsi` ");
        $success[] = "cbt_soal_opsi: add gambar";
    }

    $success[] = "cbt_soal: update tipe_soal enum & cbt_soal_opsi: update label length";
} catch (PDOException $e) {
    $errors[] = "Migration Error: " . $e->getMessage();
}

// Buat akun admin default
$pdo->exec("INSERT IGNORE INTO `cbt_users` (username, password, nama, role) VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'Administrator CBT', 'superadmin')");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>CBT - Instalasi</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a2e;
            color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0
        }

        .card {
            background: #16213e;
            border-radius: 12px;
            padding: 40px;
            max-width: 650px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5)
        }

        h1 {
            color: #e94560;
            margin-top: 0
        }

        .ok {
            color: #4eda88
        }

        .err {
            color: #e94560
        }

        .warn {
            color: #f39c12
        }

        .badge {
            display: inline-block;
            background: #0f3460;
            border-radius: 5px;
            padding: 3px 10px;
            margin: 3px;
            font-size: .8rem
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            background: #e94560;
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold
        }

        code {
            background: #0f3460;
            padding: 2px 8px;
            border-radius: 4px
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>🚀 Instalasi CBT</h1>
        <p>Database aktif: <strong class="ok"><?= $using_db ?></strong></p>
        <?php if ($is_shared): ?>
            <p class="warn">⚠️ Menggunakan <code>db_simaks</code> karena tidak ada izin membuat database baru. Tabel CBT
                tetap terpisah via prefix <code>cbt_</code>.</p>
        <?php endif; ?>
        <hr style="border-color:#0f3460">

        <?php if (empty($errors)): ?>
            <h2 class="ok">✅ <?= count($success) ?> tabel berhasil dibuat!</h2>
            <div><?php foreach ($success as $t): ?><span class="badge ok"><?= $t ?></span><?php endforeach; ?></div>
            <br>
            <p><strong>Akun Admin Default:</strong><br>Username: <code>admin</code> &nbsp;|&nbsp; Password:
                <code>admin123</code>
            </p>
            <p class="warn">⚠️ Segera ubah password setelah login!</p>
            <a href="/simaks/cbt/login.php" class="btn">Masuk ke Panel CBT →</a>
        <?php else: ?>
            <h2 class="err">❌ Ada kesalahan (<?= count($errors) ?>):</h2>
            <?php foreach ($errors as $e): ?>
                <p class="err">• <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            <?php if (!empty($success)): ?>
                <p class="ok">Berhasil: <?= implode(', ', $success) ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>