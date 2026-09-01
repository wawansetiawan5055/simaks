# SMA Plus Al-Manshuriyah - Website Resmi Bootstrap 5

## 📋 Implementasi Website Sekolah Profesional

Website resmi SMA Plus Al-Manshuriyah telah dikembangkan dengan menggunakan Bootstrap 5, menghadirkan desain modern, responsif, dan user-friendly dengan 10 section konten utama.

---

## 🎯 Fitur Utama Website

### 1. **Profil Sekolah**
- Lokasi, kontak, jam operasional
- Informasi dasar sekolah
- Integrasi dengan data SIMAKS

### 2. **Visi, Misi & Tujuan**
- Visi sekolah yang jelas
- Misi pendidikan
- Tujuan jangka panjang

### 3. **Program Unggulan**
- Program Muslimpreneur
- Program Tahfidz Al-Quran
- Program Pembiasaan Akhlaq Mulia
- Gallery kegiatan per program

### 4. **Profil Guru & Tenaga Kependidikan (GTK)**
- Menampilkan data guru dari SIMAKS
- Foto, jabatan, bidang studi
- Kontak komunikasi (email, WhatsApp)
- Sertifikasi dan pengalaman

### 5. **Daftar Siswa**
- Data siswa dari SIMAKS
- Filter berdasarkan kelas
- Informasi siswa (non-sensitif)
- Prestasi dan penghargaan

### 6. **Ekstrakurikuler**
- Pramuka, Futsal, Voli, Pencak Silat, Hadroh
- Detail kegiatan masing-masing
- Jadwal dan pembina
- Gallery kegiatan

### 7. **Galeri Kegiatan**
- Grid layout yang responsif
- Caption untuk setiap gambar
- Lightbox/modal view
- Kategori kegiatan

### 8. **Video Kegiatan**
- Embed YouTube/Vimeo
- Kategori video
- Filter dan search
- View counter

### 9. **Informasi & Pengumuman**
- Berita sekolah
- Pengumuman penting
- Filter berdasarkan kategori
- Destincted dari pengumuman biasa

### 10. **Media Sosial**
- Link Facebook, Instagram, YouTube, TikTok
- WhatsApp Business
- Social icons di footer
- Share buttons

---

## 🗄️ Database Tables (Baru)

### `landing_ekstrakurikuler`
```sql
- id (INT, PK)
- nama (VARCHAR)
- deskripsi (LONGTEXT)
- icon (VARCHAR)
- gambar (VARCHAR)
- pembina (VARCHAR)
- jumlah_siswa (INT)
- jadwal (VARCHAR)
- lokasi (VARCHAR)
- is_active (TINYINT)
- display_order (INT)
```

### `landing_video`
```sql
- id (INT, PK)
- judul (VARCHAR)
- deskripsi (LONGTEXT)
- video_url (VARCHAR)
- thumbnail (VARCHAR)
- tipe (ENUM: youtube, vimeo, upload)
- kategori (VARCHAR)
- durasi (VARCHAR)
- is_featured (TINYINT)
- is_active (TINYINT)
- view_count (INT)
- display_order (INT)
```

### `landing_guru_profil`
```sql
- id (INT, PK)
- id_guru (INT, FK)
- nama (VARCHAR)
- jabatan (VARCHAR)
- nip (VARCHAR)
- pendidikan_terakhir (VARCHAR)
- bidang_studi (VARCHAR)
- foto (VARCHAR)
- email (VARCHAR)
- no_hp (VARCHAR)
- pengalaman_tahun (INT)
- sertifikasi (VARCHAR)
- is_display (TINYINT)
- display_order (INT)
```

### `landing_siswa_profil`
```sql
- id (INT, PK)
- id_siswa (INT, FK)
- nama (VARCHAR)
- kelas (VARCHAR)
- no_induk (VARCHAR)
- foto (VARCHAR)
- tempat_lahir (VARCHAR)
- tanggal_lahir (DATE)
- jenis_kelamin (ENUM)
- agama (VARCHAR)
- alamat (TEXT)
- no_hp (VARCHAR)
- email (VARCHAR)
- prestasi (VARCHAR)
- is_display (TINYINT)
```

### `landing_informasi`
```sql
- id (INT, PK)
- judul (VARCHAR)
- konten (LONGTEXT)
- kategori (VARCHAR)
- icon (VARCHAR)
- gambar (VARCHAR)
- tanggal_publikasi (DATE)
- link_eksternal (VARCHAR)
- is_featured (TINYINT)
- is_active (TINYINT)
- display_order (INT)
```

---

## 🎨 Design & Styling

### Framework
- **Bootstrap 5.3.0** - Responsive framework
- **Font Awesome 6.5.1** - Icons library
- **Google Fonts** - Poppins & Plus Jakarta Sans

### Color Scheme
- **Primary**: #2d5016 (Hijau)
- **Secondary**: #f39c12 (Kuning/Emas)
- **Accent**: #e74c3c (Merah)
- **Dark**: #1a1a2e
- **Light**: #f8f9fa

### Responsive Breakpoints
- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: <768px

---

## 📁 File Structure

```
app/
├── controllers/
│   └── LandingControllerSMA.php (Baru)
├── views/
│   ├── landing_sma.php (Baru - Main homepage)
│   └── landing/
│       ├── ekstrakurikuler_list.php (Baru)
│       ├── ekstrakurikuler_detail.php (Baru)
│       ├── guru_list.php (Baru)
│       ├── siswa_list.php (Baru)
│       ├── video_list.php (Baru)
│       ├── informasi_list.php (Baru)
│       └── informasi_detail.php (Baru)

database/
├── landing_page_upgrade.sql
└── sma_plus_website.sql (Baru)
```

---

## 🚀 Routing & URL

| Section | URL | Controller Function |
|---------|-----|-------------------|
| Homepage | `index.php?mod=landing_sma&act=index` | `landing_sma_index()` |
| Guru | `index.php?mod=landing_sma&act=guru_list` | `guru_list()` |
| Siswa | `index.php?mod=landing_sma&act=siswa_list` | `siswa_list()` |
| Ekstrakurikuler | `index.php?mod=landing_sma&act=ekstrakurikuler_list` | `ekstrakurikuler_list()` |
| Ekskul Detail | `index.php?mod=landing_sma&act=ekstrakurikuler_detail&id=1` | `ekstrakurikuler_detail()` |
| Video | `index.php?mod=landing_sma&act=video_list` | `video_list()` |
| Informasi | `index.php?mod=landing_sma&act=informasi_list` | `informasi_list()` |
| Info Detail | `index.php?mod=landing_sma&act=informasi_detail&id=1` | `informasi_detail()` |

---

## 💾 Installation Steps

### 1. Import Database Migration
```bash
mysql -u administrator -p20247166 db_simaks < database/sma_plus_website.sql
```

### 2. Copy Controller File
```bash
# File sudah tersedia di: app/controllers/LandingControllerSMA.php
```

### 3. Copy View Files
```bash
# Homepage: app/views/landing_sma.php
# Public pages di: app/views/landing/*.php
```

### 4. Configure Settings
Pastikan `app_settings` sudah ter-fill dengan:
- `website_name`: SMA Plus Al-Manshuriyah
- `website_description`: Deskripsi website
- `jantung_sekolah`: Motto/Tagline
- `visi_sekolah`: Visi
- `misi_sekolah`: Misi
- Social media URLs

### 5. Update Routing (index.php)
```php
// Tambahkan di routing section:
if ($module == 'landing_sma') {
    require 'app/controllers/LandingControllerSMA.php';
    // Router code di sini
}
```

---

## 👤 Admin Interface (Next Phase)

Admin dapat mengelola konten melalui:
- Dashboard content management
- CRUD untuk setiap section
- File upload untuk gambar/video
- SEO settings
- Publishing schedule

---

## 📊 Features Implemented

✅ Bootstrap 5 responsive design
✅ 10 content sections
✅ Database integration with SIMAKS
✅ Public views for all sections
✅ Smooth scrolling navigation
✅ Filter & search functionality
✅ Social media integration
✅ Mobile-friendly design
✅ SEO-friendly URLs
✅ Professional styling

---

## 🔄 Future Enhancements

- [ ] Admin panel untuk manage content
- [ ] Search functionality di semua halaman
- [ ] Pagination untuk data banyak
- [ ] Analytics & view counter
- [ ] Email subscription
- [ ] Comment system
- [ ] Gallery lightbox
- [ ] SSL certificate
- [ ] CDN integration
- [ ] Performance optimization

---

## 📞 Support & Maintenance

Untuk update atau modifikasi konten:
1. Login ke admin panel
2. Navigasi ke section yang ingin diubah
3. Edit data dan publish

---

## 📝 Notes

- Semua konten dapat diatur melalui database
- Images harus diupload ke `public/uploads/`
- Video dapat embed dari YouTube atau Vimeo
- Data guru & siswa dari SIMAKS dapat disinkronkan
- Website fully responsive untuk mobile, tablet, desktop

---

**Version**: 1.0.0  
**Date**: 10 Maret 2026  
**Framework**: Bootstrap 5 + PHP  
**Database**: MySQL
