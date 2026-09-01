# Roadmap Implementasi SIMAKS Blueprint (Versi Final)
**Panduan Langkah Demi Langkah untuk Pengembangan Portal Guru, Portal Siswa, Modul LMS, dan Layanan Mandiri.**

---

## 1. Persiapan Awal (Phase 0: Audit & Setup)
**Durasi Estimasi:** 1-2 hari  
**Status:** ✅ COMPLETED  
**Tujuan:** Memastikan fondasi teknis dan database siap sebelum pengembangan.

### A. Audit Database Eksisting
- **Status:** ✅ COMPLETED
- **Output:** File `DATABASE_AUDIT.md` dengan ringkasan konvensi.

### B. Setup Environment & Dependencies
- **Status:** ✅ COMPLETED
- **Output:** File `SETUP_CHECKLIST.md` dengan status environment.

### C. Backup & Version Control
- **Status:** ⏳ Belum Dicek
- **Rekomendasi:** Backup sebelum modifikasi

---

## 2. Implementasi Fondasi Teknis (Phase 1: Core Infrastructure)
**Durasi Estimasi:** 3-5 hari  
**Status:** ✅ COMPLETED  
**Tujuan:** Membangun arsitektur MVC, RBAC, dan keamanan dasar.

### A. Role-Based Access Control (RBAC)
- **Status:** ✅ COMPLETED
- **Output:** Middleware `require_access()` dan `require_role()` di `config/helper.php`, role Siswa ditambah.

### B. Keamanan Upload & Validasi
- **Status:** ✅ COMPLETED
- **Output:** Class `SecureFileUpload` sudah ada di `config/secure_upload.php`.

### C. Struktur MVC untuk Modul Baru
- **Status:** ✅ COMPLETED
- **Output:** Skeleton MVC LMS (`LmsController.php`, `LmsModel.php`, view dasar), routing di `public/index.php`, DDL tabel LMS di `database/add_lms_tables.sql`, menu LMS di `app_menu`.

---

## 3. Implementasi Portal Guru (Phase 2: Guru Portal)
**Durasi Estimasi:** 7-10 hari  
**Status:** ✅ IN PROGRESS  
**Tujuan:** Rebranding dan penambahan fitur LMS.

### A. Dashboard Guru
- **Status:** ✅ COMPLETED
- **Output:** `DashboardGuruController.php`, methods di `DashboardModel.php`, view `dashboard_guru.php`, routing di `index.php`.
- **Features:** Statistik mata pelajaran, total siswa, absensi hari ini, tugas pending, jadwal hari ini, permohonan izin siswa, quick actions.
- **Menu Sidebar:** ✅ Ditambahkan menu "Dashboard Guru" dan "Profil Guru" di sidebar untuk role Guru.

### B. Profil Guru
- **Status:** ✅ COMPLETED
- **Output:** Routing di `index.php`, view `profil_guru/index.php` dan `profil_guru/detail.php`, controller `ProfilGuruController.php`.
- **Features:** List guru, detail profil guru dengan informasi pribadi dan tambahan.

### C. LMS Guru (Materi & Tugas)
- **Status:** ✅ COMPLETED
- **Output:** DDL tabel `lms_materi`, `lms_tugas`, `lms_pengumpulan`, `LmsController.php`, `LmsModel.php`, routing lengkap, view lengkap.
- **Features:** Upload materi, pembuatan tugas, pengumpulan siswa, koreksi nilai, dashboard LMS.
- **Routing Issues:** ✅ Diperbaiki - semua route LMS sekarang berfungsi (dashboard, materi_list, tugas_list, koreksi_list).

### D. Update Modul Eksisting
- **Langkah 1:** Rebrand header "Administrasi Kegiatan" ke "Portal Guru".
- **Langkah 2:** Sinkronisasi nilai LMS ke `nilai_formatif`.
- **Langkah 3:** Test integrasi dengan jurnal mengajar.
- **Output:** Portal guru lengkap.

---

## 4. Implementasi Portal Siswa (Phase 3: Siswa Portal)
**Durasi Estimasi:** 7-10 hari  
**Tujuan:** Akses mandiri siswa.

### A. Dashboard Siswa
- **Langkah 1:** Buat `DashboardSiswaController.php` dengan widget tagihan, tugas, pengumuman.
- **Langkah 2:** Query data dari tabel eksisting dan baru.
- **Langkah 3:** Buat view responsif.
- **Output:** Dashboard siswa.

### B. Profil & Akademik
- **Langkah 1:** View profil dengan data orang tua dan histori.
- **Langkah 2:** Integrasi jadwal dan grafik nilai (gunakan library chart).
- **Langkah 3:** E-Rapor dari tabel nilai.
- **Output:** Halaman akademik.

### C. LMS Siswa
- **Langkah 1:** Akses materi download dari `lms_materi`.
- **Langkah 2:** Upload tugas ke `lms_pengumpulan`.
- **Langkah 3:** Status penilaian real-time.
- **Output:** Modul e-learning siswa.

### D. Presensi & Keuangan
- **Langkah 1:** Rekap absensi pribadi dari tabel absensi.
- **Langkah 2:** DDL tabel `keuangan_tagihan` dan view rincian.
- **Langkah 3:** Integrasi pembayaran.
- **Output:** Modul keuangan dan presensi.

### E. Layanan Mandiri
- **Langkah 1:** DDL tabel `layanan_izin`.
- **Langkah 2:** Form izin dengan upload 2 foto.
- **Langkah 3:** Approval workflow (guru/TU).
- **Output:** Sistem e-surat.

---

## 5. Testing & Deployment (Phase 4: Quality Assurance)
**Durasi Estimasi:** 3-5 hari  
**Tujuan:** Validasi dan peluncuran.

### A. Unit & Integration Testing
- **Langkah 1:** Test RBAC dan upload security.
- **Langkah 2:** Validasi query database (cek SQL injection).
- **Langkah 3:** Test UI/UX di browser berbeda.
- **Output:** Laporan bug di `TEST_REPORT.md`.

### B. Deployment
- **Langkah 1:** Migrasi database production.
- **Langkah 2:** Deploy kode ke server.
- **Langkah 3:** Monitoring performa.
- **Output:** Sistem live.

---

## 6. Future Plans (Phase 5: Enhancements)
- Portal Piket: Update dari "Piket & Kesiswaan".
- Optimisasi: Caching, API enhancements.
- Monitoring: Logs di `logs/`.

**Catatan:** Setiap phase lakukan commit Git dan dokumentasi. Jika ada blocker, prioritaskan audit database dulu. Total estimasi: 3-5 minggu untuk implementasi penuh.