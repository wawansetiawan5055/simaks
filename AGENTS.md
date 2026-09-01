# PANDUAN PENGEMBANGAN SIMAKS (SIMAKS AI AGENT DEVELOPER BIBLE)

> **DOKUMEN INI ADALAH INSTRUKSI WAJIB UNTUK SETIAP AI AGENT YANG BEKERJA PADA CODEBASE SIMAKS.**
> Sebelum membuat, mengubah, atau memperbaiki kode, seluruh AI Agent **WAJIB** membaca dan mematuhi panduan arsitektur di bawah ini.

---

## 1. IKHTISAR TEKNOLOGI & ARSITEKTUR

SIMAKS (Sistem Manajemen Akademik Sekolah) adalah aplikasi web berbasis PHP Native (MVC ringan) berkinerja tinggi yang dirancang untuk stabilitas, kecepatan, dan kemudahan deployment.

* **Bahasa & Backend**: Native PHP 8.x dengan PDO MySQL/MariaDB (UTF-8 / utf8mb4).
* **Pola Desain**: Model-View-Controller (MVC) Ringan:
  * **Entry Point & Router**: `public/index.php` (Mendukung query string `?mod=X&act=Y` dan Clean URL `/mod/act/param`).
  * **Controllers**: `app/controllers/*Controller.php` atau file fungsi controller terkait.
  * **Models**: `app/models/*Model.php` (Kelas dengan static / instance method membungkus query PDO).
  * **Views**: `app/views/*.php` (Template PHP dengan partials di `app/views/partials/`).
  * **Konfigurasi & Helper**: `config/` (`app.php`, `db.php`, `helper.php`, `env.php`, `csrf.php`, `security_headers.php`).
* **Frontend**: AdminLTE 3 / Bootstrap, FontAwesome, jQuery, DataTables, Select2, SweetAlert2.

---

## 2. ATURAN EMAS PENGEMBANGAN (GOLDEN RULES - HARUS DIPATUHI)

### ⛔ ATURAN 1: DILARANG MENYISIPKAN OPERASI WRITE/MIGRASI DATABASE DI DALAM VIEW ATAU GETUSERMENU (STRICT SEPARATION OF CONCERNS)
* **JANGAN PERNAH** menyisipkan query `INSERT`, `UPDATE`, `ALTER TABLE`, atau migrasi/seeder otomatis ke dalam:
  * `AppMenuModel::getUserMenu()`
  * `app/views/partials/sidebar.php` / `header.php` / `navbar.php`
  * `public/index.php` (pada siklus request normal)
* **Mengapa?** Karena setiap request/refresh halaman akan memicu transaksi penulisan dan penguncian tabel database (*table/row lock*). Hal ini menyebabkan *deadlock*, antrean koneksi, dan error `Maximum execution time of 100 seconds exceeded`.
* **Solusi yang Benar**: Jika ada menu baru, kolom baru, atau data awal:
  * Buat file migrasi/patch terpisah di folder `patch/` atau `sql/` (misal: `patch/2026_add_menu_uks.sql`).
  * Berikan instruksi kepada pengguna atau sediakan tombol khusus di menu Pengaturan/Utilitas DB untuk menjalankannya sekali saja.

### ⚡ ATURAN 2: INTEGRITAS QUERY DATABASE & PDO
* Selalu gunakan **PDO Prepared Statements** dengan placeholder parameter `?` atau `:named` untuk mencegah SQL Injection.
  ```php
  // BENAR
  $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_kelas = ? AND status = 'Aktif'");
  $stmt->execute([$id_kelas]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // SALAH (Rentan SQL Injection)
  $stmt = $pdo->query("SELECT * FROM siswa WHERE id_kelas = '$id_kelas'");
  ```
* Jangan lakukan query di dalam loop (`N+1 query problem`). Gunakan `JOIN` atau query `WHERE id IN (...)`.
* Gunakan index yang sesuai pada foreign key dan kolom filter (seperti `id_ta`, `id_kelas`, `id_peran`).

### 🔗 ATURAN 3: ROUTING & PENULISAN URL
* Selalu gunakan konstanta `BASE_URL` untuk link internal, redirect, dan aset statis.
  ```php
  // Link di View
  <a href="<?= BASE_URL ?>siswa/detail/<?= $id ?>">Detail Siswa</a>

  // Redirect di Controller
  redirect(BASE_URL . 'keuangan_tagihan');
  ```
* Jangan pernah *hardcode* domain atau path absolut lokal seperti `http://localhost/simaks/...` atau `d:/...`.

### 🛡️ ATURAN 4: KEAMANAN & ROLE-BASED ACCESS CONTROL (RBAC)
* Tabel Hak Akses: `pengguna`, `peran`, `pengguna_peran`, `app_menu`, `hak_akses`.
* Pastikan pengecekan otentikasi di awal Controller:
  ```php
  if (!is_logged_in()) {
      redirect(BASE_URL . 'auth/login');
      exit;
  }
  ```
* Pengecekan Peran (*Roles*):
  ```php
  $user_roles = $_SESSION['roles'] ?? [];
  $is_admin = in_array('Admin', $user_roles);
  $is_guru  = in_array('Guru', $user_roles);
  $is_siswa = in_array('Siswa', $user_roles);
  ```
* Sanitasi output yang berasal dari input pengguna di View dengan `htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8')` atau helper yang ada.

### 📝 ATURAN 5: LOGGING & DISK I/O
* **JANGAN** menulis file log secara sinkron menggunakan `file_put_contents(..., FILE_APPEND)` pada setiap request HTTP global. File log yang membengkak puluhan MB akan memperlambat I/O server.
* Gunakan `error_log()` PHP standar hanya saat menangkap blok `catch (Exception $e)`.

---

## 3. STRUKTUR FOLDER PROYEK

```text
simaks.app/
├── app/
│   ├── controllers/      # Logika Controller & Pemrosesan Request
│   ├── models/           # Query Database & Logika Bisnis (Model)
│   └── views/            # Template UI & File HTML/PHP
│       ├── partials/     # Header, Sidebar, Navbar, Footer, Mobile Modals
│       ├── landing/      # Halaman Publik / Website Profil Sekolah
│       └── ...           # View Modul (Siswa, Guru, CBT, LMS, Keuangan, dll.)
├── config/
│   ├── app.php           # Konfigurasi Umum Aplikasi
│   ├── db.php            # Koneksi Database PDO
│   ├── env.php           # Parser .env
│   ├── helper.php        # Kumpulan fungsi bantuan global
│   ├── security_headers.php # Header Keamanan HTTP
│   └── csrf.php          # Proteksi Token CSRF
├── database/ / sql/      # Skema Database & File Migrasi SQL
├── patch/                # Skrip & Patch Perbaikan Database
├── public/
│   ├── index.php         # Entry Point Aplikasi & Routing
│   ├── assets/           # CSS, JS, Gambar, Vendor Plugins
│   └── uploads/          # Berkas yang diunggah pengguna
└── cbt/                  # Sub-modul / Database Terpisah CBT (jika aktif)
```

---

## 4. PANDUAN PENGEMBANGAN FITUR / MODUL BARU (STEP-BY-STEP)

Jika diminta menambahkan fitur baru (misal: modul `Perpustakaan` atau `Inventaris`):

1. **Step 1: Database (Tabel & Menu)**
   * Buat file SQL di `sql/` atau `patch/` untuk membuat tabel baru dan menambahkan baris ke `app_menu` dan `hak_akses`.
   * Beritahu pengguna query yang perlu dieksekusi.
2. **Step 2: Buat Model**
   * Buat `app/models/NamaModulModel.php`.
   * Gunakan metode static yang menerima `PDO $pdo` sebagai argumen pertama.
3. **Step 3: Buat Controller**
   * Buat `app/controllers/NamaModulController.php` atau tambahkan fungsi handler di controller yang sesuai.
   * Ambil data dari Model, lakukan validasi data, lalu panggil view.
4. **Step 4: Buat View**
   * Buat `app/views/namamodul_index.php` dan form/detail terkait.
   * Sertakan partial header dan footer (`require_once 'partials/header.php'` dan `footer.php`).
   * Gunakan kelas-kelas AdminLTE/Bootstrap yang sudah konsisten dengan modul lainnya.
5. **Step 5: Hubungkan ke Routing**
   * Daftarkan modul di `public/index.php` jika memerlukan pemetaan URL khusus.

---

## 5. CHECKLIST SEBELUM MENYELESAIKAN TUGAS (VERIFICATION CHECKLIST)

Sebelum AI Agent menyatakan tugas selesai, pastikan:
* [ ] Tidak ada script migrasi yang berjalan otomatis di setiap HTTP request.
* [ ] Tidak ada sintaks error PHP (`php -l` jika bisa dijalankan).
* [ ] Semua query menggunakan PDO prepared statements.
* [ ] Semua URL menggunakan `BASE_URL`.
* [ ] Tidak ada `die()`, `var_dump()`, atau file logging berat yang tertinggal di kode produksi.
* [ ] Tampilan responsif dan konsisten dengan komponen AdminLTE yang ada di SIMAKS.
