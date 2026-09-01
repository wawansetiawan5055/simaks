# Database Audit Report - SIMAKS
**Tanggal Audit:** April 19, 2026  
**File Sumber:** db_simaks.sql dan file terkait di folder database/

---

## 1. Konvensi Penamaan Kolom
Berdasarkan analisis tabel eksisting di `db_simaks.sql`:

### Primary Key:
- **Format:** `id_[nama_tabel]` (e.g., `id_absensi`, `id_agenda`, `id_menu`)
- **Tipe Data:** `INT(11)` AUTO_INCREMENT
- **Contoh:** `id_absensi INT(11) NOT NULL AUTO_INCREMENT`

### Foreign Key:
- **Format:** `id_[referensi]` (e.g., `id_guru`, `id_siswa`, `id_ta`, `id_kelas`, `id_mapel`)
- **Tipe Data:** `INT(11)` DEFAULT NULL atau NOT NULL
- **Contoh:** `id_guru INT(11) DEFAULT NULL`

### Kolom Tanggal:
- **Format:** `tanggal` (untuk DATE), `created_at` atau `updated_at` (untuk TIMESTAMP)
- **Tipe Data:** `DATE`, `DATETIME`, atau `TIMESTAMP`
- **Contoh:** `tanggal DATE DEFAULT NULL`, `created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`

### Kolom Status:
- **Format:** `status` (untuk enum sederhana)
- **Tipe Data:** `ENUM('Aktif','Nonaktif')` atau `ENUM('Hadir','Sakit','Izin','Alpa','Lainnya')`
- **Contoh:** `status ENUM('Aktif','Nonaktif') DEFAULT 'Aktif'`

### Kolom Text:
- **Format:** `keterangan`, `deskripsi`, `alasan` (untuk teks panjang)
- **Tipe Data:** `TEXT` untuk konten panjang, `VARCHAR(255)` untuk string pendek
- **Contoh:** `keterangan TEXT DEFAULT NULL`, `nama_menu VARCHAR(100) NOT NULL`

---

## 2. Tabel Master Teridentifikasi
Dari dump database, tabel master utama belum ditemukan di `db_simaks.sql`. Namun, berdasarkan referensi foreign key:

- **`data_guru`** (diasumsikan ada, dengan kolom `id_guru INT(11) PRIMARY KEY`)
- **`data_siswa`** (diasumsikan ada, dengan kolom `id_siswa INT(11) PRIMARY KEY`)
- **`data_ta`** (tahun ajaran, `id_ta INT(11) PRIMARY KEY`)
- **`data_kelas`** (kelas, `id_kelas INT(11) PRIMARY KEY`)
- **`data_mapel`** (mata pelajaran, `id_mapel INT(11) PRIMARY KEY`)

**Catatan:** Tabel master ini mungkin ada di file SQL terpisah atau belum di-dump. Perlu konfirmasi dengan database live.

---

## 3. Tabel Transaksi Utama
- `absensi_guru`: Absensi guru harian
- `absensi_siswa_mapel`: Absensi siswa per mapel
- `absensi_siswa_piket`: Absensi siswa piket
- `agenda_*`: Agenda kegiatan (kokulikuler, pembiasaan, dll.)
- `anggota_*`: Anggota ekskul/kokulikuler
- `app_menu`: Struktur menu aplikasi
- `app_config`: Konfigurasi tema aplikasi
- `audit_log`: Log aktivitas pengguna

---

## 4. Relasi Utama
- Guru → Absensi Guru (1:N)
- Siswa → Absensi Siswa (1:N)
- Kelas → Siswa (1:N)
- Mapel → Absensi Mapel (1:N)
- TA → Semua tabel transaksi (1:N)

---

## 5. Rekomendasi untuk DDL Baru
Untuk tabel LMS dan Layanan Mandiri:
- Gunakan `id_` prefix untuk primary/foreign key
- Tipe data konsisten: `INT(11)` untuk ID, `VARCHAR(255)` untuk file path, `TEXT` untuk deskripsi
- Tambahkan `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP` untuk audit trail
- Pastikan foreign key reference ke tabel master yang benar

**Status:** Audit selesai. Siap untuk langkah berikutnya (setup environment).