# PANDUAN SETUP DATABASE DI APANEL

## Masalah
Data pada dashboard tidak tampil setelah diupload ke aPanel, padahal di XAMPP normal.

## Penyebab
Database credentials di `config/db.php` hardcoded untuk XAMPP lokal. Saat di-upload ke aPanel, credentials database mungkin berbeda.

## Solusi

### Step 1: Cari Database Credentials di aPanel
1. Login ke **aPanel** 
2. Buka menu **Databases** atau **Manage Databases**
3. Lihat nama database, username, password, dan hostname
   - Catat: Database Name, Username, Password
   - Hostname biasanya `localhost` atau `127.0.0.1`

### Step 2: Update File `.env` di Root Folder SIMAKS
1. Buka file `.env` di root folder SIMAKS (parallel dengan folder `public`, `app`, `config`, dll)
2. Update nilai-nilai berikut sesuai credentials aPanel Anda:

```
DB_HOST=localhost          # atau sesuai hostname di aPanel
DB_NAME=nama_database      # Ganti dengan nama database aPanel
DB_USER=username_database  # Ganti dengan username database aPanel
DB_PASS=password_database  # Ganti dengan password database aPanel
```

Contoh:
```
DB_HOST=localhost
DB_NAME=user123_simaks
DB_USER=user123_admin
DB_PASS=MySecurePassword123
```

### Step 3: Simpan dan Test
1. Save file `.env`
2. Akses dashboard: `http://yourdomain.com/simaks/public/index.php?mod=dashboard`
3. Lihat apakah data rekap siswa sudah muncul

## Troubleshooting

### Error "Gagal koneksi DB"
- Pastikan credentials di `.env` sudah benar
- Cek spelling username, password, database name
- Pastikan database sudah di-restore/di-import dengan schema yang benar

### Data masih tidak tampil
- Buka browser DevTools (F12 → Console)
- Lihat apakah ada error AJAX saat load data
- Cek apakah `api/api.php` accessible (buka di browser: `http://yourdomain.com/simaks/api/api.php?mod=dashboard&act=list_ta`)

### Database credentials tidak cocok
- Jika aPanel punya multiple databases, pastikan Anda menggunakan yang tepat
- Biasanya format: `username_dbname` (e.g., `user123_simaksdb`)
- Kalau masih ragu, buka phpMyAdmin di aPanel dan lihat di sidebar

## Keamanan
- **JANGAN** share file `.env` ke publik
- Pastikan `.env` ada di `.gitignore` jika menggunakan Git
- Ubah password database di aPanel jika file ini terlalu lama tidak diupdate
