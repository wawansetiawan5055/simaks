# Landing Page & PPDB Integration

## 🎯 Overview

Implementasi landing page modern dengan fitur PPDB (Penerimaan Peserta Didik Baru) yang terintegrasi dengan SIMAKS.

## ✨ Fitur Utama

### 1. **Landing Page Modern**
- ✅ Hero slider dengan auto-play
- ✅ Section informasi sekolah (Visi & Misi)
- ✅ Berita & Pengumuman terbaru
- ✅ Gallery foto kegiatan
- ✅ Statistik sekolah (counter animation)
- ✅ Responsive design
- ✅ SEO optimized

### 2. **PPDB Public Form**
- ✅ Multi-step form dengan progress indicator
- ✅ Upload dokumen (Foto, KK, Akta, Ijazah, Raport)
- ✅ Tanpa perlu login
- ✅ Generate nomor pendaftaran otomatis
- ✅ Data langsung masuk ke database SIMAKS

### 3. **PPDB Status Check**
- ✅ Cek status pendaftaran dengan nomor registrasi
- ✅ Informasi status real-time
- ✅ Catatan verifikasi dari admin

### 4. **Smart Routing (Opsi 1+2+5)**
- ✅ **Config Toggle**: Admin bisa enable/disable landing page
- ✅ **Direct URL Access**: 
  - `/` → Landing page (jika enabled)
  - `/index.php?mod=login` → Langsung ke login
  - `/index.php?mod=dashboard` → Dashboard (perlu login)
- ✅ **Auto-detect User**: 
  - User belum login → Tampilkan landing page
  - User sudah login → Redirect ke dashboard

## 📁 Struktur File

```
simaks/
├── config/
│   └── app.php                          # ⭐ Config landing page & PPDB
├── app/
│   ├── controllers/
│   │   └── LandingController.php        # ⭐ Controller landing page
│   └── views/
│       ├── landing_page.php             # ⭐ View landing page
│       ├── ppdb_public_form.php         # ⭐ Form PPDB
│       ├── ppdb_success.php             # ⭐ Halaman sukses
│       └── ppdb_check_status.php        # ⭐ Cek status
├── public/
│   ├── index.php                        # ✏️ Updated routing
│   └── uploads/
│       ├── ppdb/                        # Upload dokumen PPDB
│       └── gallery/                     # Upload gallery
└── scripts/
    ├── create_ppdb_table.sql            # ⭐ SQL untuk table
    └── setup_ppdb_database.ps1          # ⭐ Script setup database
```

## 🚀 Cara Install

### 1. Setup Database

Jalankan script PowerShell untuk membuat tabel:

```powershell
cd c:\xampp\htdocs\simaks
.\scripts\setup_ppdb_database.ps1
```

**Atau manual via phpMyAdmin:**
- Import file `scripts/create_ppdb_table.sql`

### 2. Upload Logo Sekolah

- Upload logo yang sudah Anda berikan ke folder: `public/assets/img/`
- Rename menjadi: `logo_sekolah.png`
- Atau update di menu: **Profil Sekolah**

### 3. Konfigurasi (Opsional)

Edit file `config/app.php` untuk customize:

```php
'landing_page' => [
    'enabled' => true,  // false untuk langsung ke login
    'show_ppdb_link' => true,
    'slider_autoplay' => true,
    'slider_interval' => 5000, // ms
],

'ppdb' => [
    'enabled' => true,
    'year' => '2025/2026',
    'start_date' => '2025-01-01',
    'end_date' => '2025-06-30',
],
```

### 4. Setup Konten (Opsional)

**Upload Gallery & Slider:**
- Masuk ke database: `landing_gallery`
- Update `image_path` dengan path gambar yang sudah diupload
- Set `is_slider = 1` untuk gambar yang ingin tampil di hero slider

**Upload Berita:**
- Masuk ke database: `landing_news`
- Insert berita/pengumuman baru
- Set `is_featured = 1` untuk tampil di homepage

## 📱 URL Access

| URL | Akses | Keterangan |
|-----|-------|------------|
| `/` atau `/index.php` | Public | Landing page (jika enabled) |
| `/index.php?mod=landing` | Public | Landing page |
| `/index.php?mod=landing&act=ppdb_form` | Public | Form PPDB |
| `/index.php?mod=landing&act=ppdb_status` | Public | Cek status PPDB |
| `/index.php?mod=auth&act=login` | Public | Login ke sistem |
| `/index.php?mod=dashboard` | Private | Dashboard (setelah login) |

## 🎨 Customization

### Warna Tema

Ganti warna di `config/app.php`:

```php
'theme' => [
    'primary_color' => '#C41E3A',   // Merah
    'secondary_color' => '#2D8A4E', // Hijau
    'accent_color' => '#FFD700',    // Emas
],
```

### SEO Settings

```php
'seo' => [
    'meta_title' => 'Nama Sekolah - SIMAKS',
    'meta_description' => 'Deskripsi sekolah...',
    'meta_keywords' => 'sekolah, ppdb, pendaftaran',
],
```

## 🔧 Fitur Admin (Coming Soon)

Untuk manage konten landing page dari dashboard admin:
- **Gallery Manager**: Upload/delete gambar gallery
- **News Manager**: CRUD berita & pengumuman
- **PPDB Manager**: Verifikasi pendaftaran, update status
- **Landing Page Settings**: Toggle fitur on/off

## 🐛 Troubleshooting

### Landing page tidak muncul?
1. Cek `config/app.php` → `landing_page.enabled = true`
2. Clear session/cookies
3. Akses langsung: `/index.php?mod=landing`

### Database error?
1. Pastikan table sudah dibuat: `ppdb_pendaftaran`, `landing_gallery`, `landing_news`
2. Run ulang: `setup_ppdb_database.ps1`

### Upload file error?
1. Cek folder permissions: `public/uploads/ppdb/`
2. Cek PHP upload settings: `upload_max_filesize`, `post_max_size`

### Gambar tidak muncul?
1. Pastikan path benar di database
2. Upload gambar ke folder `public/uploads/gallery/`
3. Gunakan path relatif: `uploads/gallery/nama-file.jpg`

## 💡 Tips

1. **Untuk disable landing page** (langsung ke login):
   - Set `config/app.php` → `landing_page.enabled = false`

2. **Untuk bookmark login**:
   - User bisa bookmark: `/index.php?mod=auth&act=login`
   - Akan langsung ke halaman login

3. **Untuk testing**:
   - User yang sudah login akan auto-redirect ke dashboard
   - Logout dulu jika ingin lihat landing page

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- Hubungi developer
- Check dokumentasi SIMAKS

---

**Version**: 1.0.0  
**Last Updated**: 11 Desember 2025  
**Created by**: Antigravity AI Assistant 🚀
