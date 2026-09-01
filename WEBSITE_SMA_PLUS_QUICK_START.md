# 🎓 Website SMA Plus Al-Manshuriyah - Panduan Akses Cepat

## ✅ STATUS: AKTIF DAN SIAP DIGUNAKAN

---

## 🌐 Akses Website

### URL Utama (Halaman Depan)
```
http://192.168.1.100:7166/?mod=landing_sma&act=index
```

### Cek Sistem (Testing)
```
http://192.168.1.100:7166/test_landing_sma.php
```

---

## 📚 Daftar Halaman

| Halaman | URL Parameter | Deskripsi |
|---------|--------------|-----------|
| **Beranda** | `?mod=landing_sma&act=index` | Halaman utama dengan 8 section |
| **Daftar Guru** | `?mod=landing_sma&act=guru_list` | Profil semua guru |
| **Daftar Siswa** | `?mod=landing_sma&act=siswa_list` | Data siswa per kelas |
| **Ekstrakurikuler** | `?mod=landing_sma&act=ekstrakurikuler_list` | Daftar semua ekskul |
| **Video** | `?mod=landing_sma&act=video_list` | Video gallery sekolah |
| **Informasi** | `?mod=landing_sma&act=informasi_list` | Pengumuman & berita |

---

## 📊 Data Konten yang Tersedia

✅ **3 Program Unggulan**
- Program Akselerasi
- Bilingual Class  
- STEM Program

✅ **5 Ekstrakurikuler**
- Pramuka
- Seni Musik
- Fotografi
- Basket
- Debat

✅ **3 Video**
- Profil Sekolah
- Aktivitas Siswa
- Testimoni Alumni

✅ **3 Pengumuman**
- PPDB 2024
- Libur Semester
- Workshop UTBK

---

## 🎨 Fitur Website

✨ **Desain Responsif**
- Mobile-friendly (smartphone, tablet)
- Desktop optimized
- Fast loading time

✨ **Fitur Interaktif**
- Navbar sticky dengan smooth scroll
- Grid filter untuk kategori
- Search functionality
- Detail page support

✨ **Branding Profesional**
- Bootstrap 5 framework
- Modern color scheme (Hijau, Emas, Merah)
- Font Awesome icons
- Google Fonts (Poppins, Plus Jakarta Sans)

---

## 🔧 Troubleshooting Cepat

### Halaman tidak muncul?
1. **Refresh browser** (Ctrl+F5)
2. **Clear cache** (Ctrl+Shift+Del)
3. **Check URL** - pastikan `mod=landing_sma`

### Data kosong?
1. Cek di `test_landing_sma.php`
2. Verify database tabel punya data
3. Pastikan `is_active = 1` di database

### Styling tidak baik?
1. Periksa CDN tersambung (F12 > Network)
2. Hard refresh browser
3. Cek path CSS/JS benar

---

## 📝 Update Data dari Admin Panel

### Tambah/Edit Program Unggulan
Setelah login ke admin panel, masuk menu:
```
Admin > Landing Page > Program Unggulan
```

### Tambah/Edit Ekstrakurikuler
```
Admin > Landing Page > Ekstrakurikuler
```

### Tambah/Edit Informasi/Pengumuman
```
Admin > Landing Page > Informasi
```

### Upload Video
```
Admin > Landing Page > Video
```

---

## 📞 Dukungan Teknis

| Masalah | Solusi |
|--------|--------|
| 404 Page Not Found | Cek URL benar, refresh browser |
| Database error | Restart MySQL service |
| CSS tidak load | Check CDN connection |
| Gambar tidak muncul | Verify path di /public/images/ |
| Loading lambat | Check server resources |

---

## 📋 File-File Penting

**Configuration:**
- `/public/index.php` - Main router
- `/config/db.php` - Database config

**Frontend:**
- `/app/views/landing_sma.php` - Main page (34 KB)
- `/app/views/landing/*.php` - Sub pages

**Backend:**
- `/app/controllers/LandingControllerSMA.php` - Controller logic

**Database:**
- `landing_programs` - Program unggulan
- `landing_ekstrakurikuler` - Ekstrakurikuler
- `landing_video` - Video content
- `landing_informasi` - Pengumuman

---

## ✅ Verifikasi Sistem

Run test file untuk verifikasi:
```
http://192.168.1.100:7166/test_landing_sma.php
```

Test akan menampilkan:
- ✓ Database connection
- ✓ File existence check
- ✓ Content data count
- ✓ Quick access links

---

## 🚀 Langkah Berikutnya

1. **Personalisasi Konten**
   - Update logo sekolah
   - Ubah warna tema sesuai brand
   - Update informasi kontak

2. **Tambah Konten**
   - Melalui admin panel
   - Atau direct SQL insert

3. **Testing**
   - Test semua halaman
   - Test responsive design
   - Test database queries

4. **Go Live**
   - Set default landing page
   - Update DNS / routing
   - Monitor uptime

---

**Last Updated:** 2024-01-15  
**Version:** 2.3.5  
**Status:** ✅ PRODUCTION READY
