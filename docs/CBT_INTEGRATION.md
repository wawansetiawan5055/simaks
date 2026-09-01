# Integrasi Modul CBT

Modul CBT berada dalam folder terpisah (`/cbt`) dan dapat dioperasikan:

1. **Sendiri** – sebagai aplikasi mandiri dengan database dan URL sendiri.
2. **Terintegrasi** – pengguna yang login di SIMAKS dapat menavigasi ke CBT dan
   (opsional) berbagi akun.

## Konfigurasi

1. Salin `.env.example` ke `.env` atau `.env.local` di root workspace.
2. Set `ENABLE_CBT=true` jika ingin mengaktifkan menu/redirect.
3. Atur koneksi database CBT (opsional) dengan variabel `CBT_DB_*`. Jika
   variabel ini kosong, SIMAKS akan menggunakan kredensial DB utama.
4. Tentukan `CBT_BASE_URL` sesuai virtual‑host/alias (mis. `/simaks/cbt`).

## Struktur dan routing

- `public/index.php` SIMAKS memeriksa `mod=cbt` dan otomatis `Location` ke
  URL CBT.
- Sidebar akan menampilkan link ke CBT ketika modul diaktifkan.
- Jika CBT diaktifkan, koneksi PDO terpisah dibuat (`$pdo_cbt`) dan tersedia
  melalui `$GLOBALS['pdo_cbt']` untuk custom integration.

## Database

- SIMAKS menggunakan skema tersendiri (`db_simaks`).
- CBT bisa memiliki skema sendiri (`db_cbt`).
- Jika Anda ingin berbagi data (mis. tabel `pengguna`), implementasikan
  replikasi atau panggilan API antara kedua aplikasi.

## Web server

Pastikan web server (Apache/Nginx) mengarahkan path `CBT_BASE_URL` ke
`/simaks/cbt/public` (alias atau virtual&nbsp;host). Contoh Apache:

```apacheconf
Alias /simaks/cbt /path/to/simaks/cbt/public
<Directory /path/to/simaks/cbt/public>
    AllowOverride All
    Require all granted
</Directory>
```

atau symlink:

```bash
ln -s /path/to/simaks/cbt/public /var/www/html/simaks/cbt
```

## SSO & autentikasi

Integrasi awal belum meliputi single sign‑on otomatis. Pilihan:

- Gunakan sesi berbagi domain (cookie) dan cek pada startup CBT: jika
  tidak ada `cbt_user_id`, redirect ke login SIMAKS dan buat token.
- Buat endpoint API di SIMAKS yang memverifikasi token dan mengembalikan
  informasi pengguna untuk digabungkan di CBT.
- Biarkan user login terpisah; ini paling sederhana.

## Menambahkan menu CBT secara manual

1. Masuk ke SIMAKS sebagai admin.
2. Tambah item menu baru (Parent "Eksternal" atau sesuai) dengan link
   `<?= cbt_base_url() ?>` atau `https://example.com/simaks/cbt/`. 
3. Atur hak akses peran sebagaimana diperlukan.

---

Dokumentasi ini memberikan gambaran awal; sesuaikan sesuai kebutuhan
pengembangan Anda.