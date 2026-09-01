# SIMAKS + CBT Setup Guide

Panduan lengkap untuk mengimplementasikan SIMAKS (Sistem Manajemen Akademik Sekolah) dengan modul CBT (Computer Based Test) dalam 3 skenario deployment.

---

## Daftar Isi

1. [Overview](#overview)
2. [Prasyarat](#prasyarat)
3. [Skenario 1: SIMAKS + CBT Terintegrasi](#skenario-1-simaks--cbt-terintegrasi)
4. [Skenario 2: SIMAKS Saja](#skenario-2-simaks-saja)
5. [Skenario 3: CBT Standalone](#skenario-3-cbt-standalone)
6. [Troubleshooting](#troubleshooting)

---

## Overview

Aplikasi SIMAKS dapat dideploy dalam 3 varian berdasarkan kebutuhan sekolah:

| Skenario | Deskripsi | Database | URL | Modul |
|----------|-----------|----------|-----|-------|
| **Skenario 1** | SIMAKS + CBT integrated dalam satu aplikasi | `db_simaks` (shared) | `http://sekolah.com:7166/simaks/` | SIMAKS + CBT |
| **Skenario 2** | SIMAKS saja (tanpa CBT) | `db_simaks` | `http://sekolah.com:7166/` | SIMAKS only |
| **Skenario 3** | CBT saja (standalone, terpisah dari SIMAKS) | `db_cbt` | `http://cbt.sekolah.com:7166/` atau port berbeda | CBT only |

**Catatan:** 
- Semua skenario menggunakan PHP 8.0+ dan MySQL/MariaDB 10.4+
- aPanel/cPanel untuk management server (optional)
- Nginx sebagai web server

---

## Prasyarat

### Server Requirements
- PHP 8.0 atau lebih tinggi
- MySQL 10.4 atau MariaDB 10.4+ 
- Nginx (recommended) atau Apache 2.4+
- Minimal 2 GB RAM, 20 GB storage

### Akses & Tools
- SSH access ke server
- MySQL CLI atau phpMyAdmin
- Composer (jika ada custom dependencies)

### Credentials Default
- MySQL Root password: `[set di server]`
- SIMAKS DB User: `administrator`
- SIMAKS DB Password: `20247166` (ubah sesuai environment)

---

## Skenario 1: SIMAKS + CBT Terintegrasi

**Untuk sekolah yang ingin menggunakan SIMAKS lengkap + fitur CBT dalam satu aplikasi.**

### A. Persiapan Folder

```bash
# 1. Download/clone SIMAKS repository
git clone https://github.com/your-org/simaks.git /home/user/simaks

# 2. Pastikan struktur folder seperti ini:
/www/wwwroot/simaks.app/
├── config/
│   ├── app.php
│   ├── db.php
│   ├── env.php
│   └── ...
├── public/
│   ├── index.php
│   ├── api.php
│   └── assets/
├── cbt/
│   ├── config/
│   │   └── db.php      ← CBT config
│   ├── public/
│   │   └── index.php   ← CBT router
│   ├── app/
│   └── ...
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
└── vendor/
```

### B. Setup Database

```bash
# 1. Login ke MySQL sebagai administrator
mysql -u administrator -p'20247166'

# 2. Jalankan queries:
# Pastikan database dan tables CBT sudah ada
SHOW DATABASES LIKE 'db_simaks';
USE db_simaks;
SHOW TABLES LIKE 'cbt%';

# Jika belum ada, import dari file SQL (jika tersedia)
# mysql -u administrator -p'20247166' db_simaks < /path/to/cbt_schema.sql

# 3. Verify user memiliki akses
GRANT ALL PRIVILEGES ON db_simaks.* TO 'administrator'@'localhost';
FLUSH PRIVILEGES;
```

### C. Konfigurasi SIMAKS

**File: `config/app.php`**

```php
// Feature flags untuk multi-variant deployment
define('ENABLE_SIMAKS', true);    // Enable SIMAKS modul
define('ENABLE_CBT', true);        // Enable CBT modul
define('CBT_DB_SHARED', true);     // Gunakan database shared (db_simaks)
```

**File: `config/db.php`**

```php
$host = "localhost";
$dbname = "db_simaks";
$user = "administrator";
$pass = "20247166";
```

**File: `cbt/config/db.php`**

```php
$dbname = getenv('CBT_DB_NAME') ?: 'db_simaks';  // ← Shared dengan SIMAKS
$user = getenv('CBT_DB_USER') ?: 'administrator';
$pass = getenv('CBT_DB_PASS') ?: '20247166';
```

### D. Setup Nginx (aPanel)

**Di aPanel: Websites → Add Website**

```
Domain: simaks.app
Root Directory: /www/wwwroot/simaks.app/public
PHP Version: 8.0
```

**Edit vhost config (jika perlu):**

```nginx
server {
    listen 7166;
    server_name simaks.app;
    root /www/wwwroot/simaks.app/public;
    index index.php;
    
    # CBT Sub-application
    location = /simaks/cbt { 
        return 301 /simaks/cbt/; 
    }
    
    location /simaks/cbt/ {
        # Symlink di /public/simaks/cbt -> ../cbt/public
        index index.php;
        try_files $uri $uri/ /index.php?$args;
    }
    
    # Standard PHP routing
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-80.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
}
```

### E. Buat Symlink untuk CBT

```bash
# 1. Dari folder public, buat symlink ke ../cbt/public
cd /www/wwwroot/simaks.app/public
mkdir -p simaks
ln -sfn ../../cbt/public simaks/cbt

# 2. Verify
ls -l simaks/
# lrwxrwxrwx 1 root root ... simaks/cbt -> ../../cbt/public

# 3. Test akses
curl -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/
# Seharusnya return HTML (atau JSON error jika DB issue)
```

### F. Update Menu Sidebar

**File: `app/views/partials/sidebar.php`**

Tambahkan menu CBT secara kondisional:

```php
<?php if (defined('ENABLE_CBT') && ENABLE_CBT): ?>
    <li class="nav-item">
        <a href="<?= BASE_URL ?>?mod=cbt" class="nav-link <?= $mod === 'cbt' ? 'active' : '' ?>">
            <i class="fas fa-laptop-code"></i>
            <p>CBT</p>
        </a>
    </li>
<?php endif; ?>
```

### G. Test Integration

```bash
# 1. Test SIMAKS
curl http://simaks.app:7166/index.php?mod=dashboard

# 2. Test CBT
curl -H "Host: simaks.app" http://192.168.1.100:7166/simaks/cbt/

# 3. Jika ada login redirect, akses dengan credentials CBT admin
```

---

## Skenario 2: SIMAKS Saja

**Untuk sekolah yang hanya menggunakan SIMAKS tanpa CBT.**

### A. Setup Database

Sama seperti Skenario 1, gunakan `db_simaks` dengan tabel SIMAKS saja.

```bash
mysql -u administrator -p'20247166' db_simaks
SHOW TABLES;  # Lihat hanya tabel simaks_*, bukan cbt_*
```

### B. Konfigurasi

**File: `config/app.php`**

```php
define('ENABLE_SIMAKS', true);
define('ENABLE_CBT', false);   // ← Disable CBT
```

### C. Setup Nginx (aPanel)

Buat website baru atau gunakan yang sudah ada:

```
Domain: simaks.app (atau yg lain)
Root: /www/wwwroot/simaks.app/public
PHP: 8.0
```

Standard Nginx config (tanpa CBT location).

### D. Menu Sidebar

Menu CBT tidak akan muncul (disabled via `ENABLE_CBT`).

Jika ada user mencoba akses `/simaks/cbt/`, akan dapat error 404 atau "Feature disabled".

### E. Test

```bash
curl http://simaks.app:7166/
# Seharusnya load dashboard SIMAKS, tidak ada menu CBT
```

---

## Skenario 3: CBT Standalone

**Untuk sekolah yang hanya menggunakan CBT tanpa SIMAKS.**

### A. Persiapan Folder

Copy folder CBT sebagai aplikasi terpisah:

```bash
# 1. Copy CBT ke lokasi baru
cp -r /www/wwwroot/simaks.app/cbt /www/wwwroot/cbt.app

# 2. Struktur folder:
/www/wwwroot/cbt.app/
├── config/
│   ├── db.php          ← Config terpisah
│   ├── bridge.php
│   ├── session.php
│   └── ...
├── public/
│   ├── index.php       ← CBT router utama
│   ├── login.php
│   └── assets/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
└── ...
```

### B. Setup Database Terpisah

```bash
# 1. Login sebagai root
mysql -u root -p

# 2. Create database dan user baru
CREATE DATABASE db_cbt CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'cbt_admin'@'localhost' IDENTIFIED BY 'cbt_password_123';
GRANT ALL PRIVILEGES ON db_cbt.* TO 'cbt_admin'@'localhost';
FLUSH PRIVILEGES;

# 3. Import CBT schema (jika ada file terpisah)
# mysql -u cbt_admin -p'cbt_password_123' db_cbt < cbt_schema.sql

# Atau, dump dari db_simaks tabel cbt_*:
mysqldump -u administrator -p'20247166' db_simaks cbt_* | \
  mysql -u cbt_admin -p'cbt_password_123' db_cbt
```

### C. Update CBT Config

**File: `/www/wwwroot/cbt.app/config/db.php`**

```php
if (!function_exists('cbt_connect_db')) {
    function cbt_connect_db()
    {
        $host = 'localhost';
        $dbname = 'db_cbt';           // ← Database standalone
        $user = 'cbt_admin';           // ← User dedicated
        $pass = 'cbt_password_123';    // ← Password dedicated

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
            ];
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die(json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]));
        }
    }
}
```

### D. Setup Nginx (aPanel) - Opsi 1: Subdomain

Buat website baru untuk CBT:

```
Domain: cbt.sekolah.com
Root: /www/wwwroot/cbt.app/public
PHP: 8.0
```

**Nginx config:**

```nginx
server {
    listen 7166;
    server_name cbt.sekolah.com;
    root /www/wwwroot/cbt.app/public;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-80.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
}
```

### D. Setup Nginx - Opsi 2: Port Berbeda

Jika tidak bisa subdomain, gunakan port berbeda:

```nginx
server {
    listen 7167;  # ← Port berbeda
    server_name sekolah.com;
    root /www/wwwroot/cbt.app/public;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-80.sock;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
}
```

### E. File Permissions

```bash
# Set owner ke www user (aPanel)
sudo chown -R www:www /www/wwwroot/cbt.app

# Set permissions
sudo chmod -R 755 /www/wwwroot/cbt.app
sudo chmod -R 777 /www/wwwroot/cbt.app/uploads
```

### F. Test CBT Standalone

```bash
# Opsi 1: Subdomain
curl http://cbt.sekolah.com:7166/

# Opsi 2: Port berbeda
curl http://192.168.1.100:7167/

# Seharusnya load login page CBT
```

---

## Troubleshooting

### 1. Error: "Gagal koneksi DB"

**Penyebab:**
- Credentials username/password salah
- Database tidak ada
- User tidak punya akses ke database
- Socket MySQL tidak accessible dari PHP

**Solusi:**

```bash
# 1. Verify credentials
mysql -u administrator -p'20247166' -e "SELECT VERSION();"

# 2. Verify database exists
mysql -u administrator -p'20247166' -e "SHOW DATABASES LIKE 'db_simaks%';"

# 3. Verify tables exist
mysql -u administrator -p'20247166' -e "USE db_simaks; SHOW TABLES LIKE 'cbt%';"

# 4. Check grants
mysql -u administrator -p'20247166' -e "SHOW GRANTS FOR 'administrator'@'localhost';"

# 5. Jika belum ada, grant:
mysql -u root -p
GRANT ALL PRIVILEGES ON db_simaks.* TO 'administrator'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Error: 404 Not Found

**Penyebab:**
- Nginx vhost tidak configured dengan benar
- Symlink tidak ada atau broken
- PHP tidak execution

**Solusi:**

```bash
# 1. Check symlink
ls -l /www/wwwroot/simaks.app/public/simaks/cbt
# Seharusnya: lrwxrwxwx -> ../../cbt/public

# 2. Verify folder exists
ls /www/wwwroot/simaks.app/cbt/public/index.php

# 3. Test Nginx config
sudo nginx -t

# 4. Reload Nginx
sudo systemctl reload nginx

# 5. Check Nginx error log
sudo tail -f /var/log/nginx/error.log
```

### 3. Error: Permission Denied (File)

**Penyebab:**
- File ownership tidak tepat (bukan www user)
- Directory tidak executable

**Solusi:**

```bash
# 1. Fix ownership
sudo chown -R www:www /www/wwwroot/simaks.app
sudo chown -R www:www /www/wwwroot/cbt.app

# 2. Fix permissions
sudo chmod -R 755 /www/wwwroot/simaks.app
sudo chmod -R 755 /www/wwwroot/cbt.app

# 3. Allow uploads
sudo chmod 777 /www/wwwroot/simaks.app/public/uploads
sudo chmod 777 /www/wwwroot/cbt.app/public/uploads
```

### 4. Error: PHP-FPM Connection Issue

**Penyebab:**
- PHP-FPM service tidak running
- Socket tidak writable
- PHP config salah

**Solusi:**

```bash
# 1. Check PHP-FPM status
sudo systemctl status php-fpm-80
sudo systemctl status php-fpm

# 2. Restart PHP-FPM
sudo systemctl restart php-fpm-80

# 3. Check socket exists
ls -l /tmp/php-cgi-80.sock

# 4. Test PHP
php -v
php -r "phpinfo();" | head -20
```

### 5. Menu CBT Tidak Muncul (Skenario 2)

**Solusi:**
- Ini expected behavior jika `ENABLE_CBT = false`
- Periksa config `app.php` memiliki `define('ENABLE_CBT', true);`
- Clear browser cache
- Jika perlu aktifkan: ubah `false` → `true` dan reload Nginx

---

## Environment Variables (Optional)

Untuk flexibility, Anda bisa setup environment variables:

**File: `.env` di root aplikasi**

```env
# SIMAKS Database
DB_HOST=localhost
DB_NAME=db_simaks
DB_USER=administrator
DB_PASS=20247166

# CBT Database (jika standalone)
CBT_DB_HOST=localhost
CBT_DB_NAME=db_cbt
CBT_DB_USER=cbt_admin
CBT_DB_PASS=cbt_password_123

# App Settings
ENABLE_SIMAKS=true
ENABLE_CBT=true
```

**Load di config:**

```php
// config/app.php
require '.env';  // Atau gunakan library seperti vlucas/phpdotenv
```

---

## Checklist Setup

### Skenario 1: SIMAKS + CBT

- [ ] Clone/download SIMAKS repository
- [ ] Create/verify `db_simaks` database
- [ ] Import CBT tables ke `db_simaks`
- [ ] Setup `config/app.php` dengan `ENABLE_SIMAKS=true, ENABLE_CBT=true`
- [ ] Setup `config/db.php` dan `cbt/config/db.php` dengan credentials
- [ ] Create aPanel website `simaks.app` → `/www/wwwroot/simaks.app/public`
- [ ] Create symlink `/public/simaks/cbt` → `../../cbt/public`
- [ ] Test: `curl http://simaks.app:7166/simaks/cbt/`
- [ ] Verify menu CBT ada di sidebar
- [ ] Test login dan basic functionality

### Skenario 2: SIMAKS Saja

- [ ] Copy folder SIMAKS (tanpa folder `cbt`)
- [ ] Atau tetap ada folder `cbt` tapi set `ENABLE_CBT=false`
- [ ] Create aPanel website
- [ ] Setup database `db_simaks`
- [ ] Test dashboard, tidak ada menu CBT
- [ ] Verify akses `/simaks/cbt/` return 404 atau error

### Skenario 3: CBT Standalone

- [ ] Copy folder `cbt` ke `/www/wwwroot/cbt.app`
- [ ] Create database `db_cbt` dan user dedicated
- [ ] Dump/migrate CBT tables dari `db_simaks` ke `db_cbt`
- [ ] Update `cbt/config/db.php` dengan credentials `db_cbt`
- [ ] Create aPanel website `cbt.app` → `/www/wwwroot/cbt.app/public`
- [ ] Update Nginx config dan reload
- [ ] Test: `curl http://cbt.app:7166/` atau custom port
- [ ] Verify login page CBT muncul
- [ ] Test basic functionality (login, soal, peserta)

---

## Support & Kontribusi

Jika ada issue atau pertanyaan:
- Periksa section [Troubleshooting](#troubleshooting)
- Lihat log: `/var/log/nginx/error.log`, `/var/log/php-fpm.log`
- Kontak support atau buat issue di repository

---

**Last Updated:** March 2026  
**Version:** 1.0  
**Author:** SIMAKS Development Team
