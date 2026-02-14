<?php
include __DIR__ . '/partials/header.php';

// --- BAGIAN PHP: KONFIGURASI DAN DATA AWAL ---

// 1. API URL: gunakan index.php routing dengan parameter type=api
// Format: index.php?type=api&mod={api_type}&act={action}
$api_url = 'index.php';

// 2. Ambil ID TA Aktif/Viewing dari sesi PHP
$current_ta_id = (int) ($_SESSION['id_ta_aktif'] ?? $_SESSION['id_ta_viewing'] ?? 0);

// 3. Data statis untuk Informasi Aplikasi
$app_info = [
    'Versi Aplikasi' => 'SIMAKS V 1.8.2',
    'Database' => 'MySQL/MariaDB',
    'Versi PHP' => '7.4',
    'Pengembang' => 'Tim IT Sekolah',
    'Web Pengembang' => '#',
    'Kontak/Medsos' => '+62 812-XXXX-XXXX'
];

$logo_path = get_app_logo();

// 4. Data Dummy (Fallback jika Controller tidak mengirim data)
$info_card = $info_card ?? ['total_siswa' => 1250, 'total_guru' => 75, 'total_kelas' => 40, 'total_mapel' => 15];
$profil_sekolah = $profil_sekolah ?? ['nama_sekolah' => 'SMA Plus Contoh', 'npsn' => '20247166', 'alamat' => 'Jl. Pendidikan No. 1', 'telp' => '021-123456', 'email' => 'info@sekolah.sch.id', 'nama_kepala_sekolah' => 'Kepala Sekolah'];

// 5. Cek Role
$is_guru = (isset($_SESSION['role']) && ($_SESSION['role'] === 'Guru' || $_SESSION['role'] === 'Admin'));
?>

<style>
    /* UTILITIES */
    .content-header {
        padding-top: 20px;
        padding-bottom: 0;
    }

    /* 1. ROW WRAPPER: Memberi jarak antara Banner/Jam dengan Statistik di bawahnya */
    .banner-row {
        margin-bottom: 30px;
        /* Jarak agar tidak berimpit */
    }

    /* 2. STYLE BANNER BARU */
    .info-banner {
        /* ... previous styles ... */
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        color: #fff;
        padding: 1.5rem 2rem;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        height: 100%;
        min-height: 180px;
        /* ENFORCE MIN HEIGHT */
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* ... (skipped ::before for brevity, it remains unchanged in file if not touched) ... */

    /* 3. STYLE KARTU CAROUSEL BARU */
    .info-card-carousel {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-top: 5px solid #007bff;
        /* Biru Konsisten */
        overflow: hidden;
        height: 100%;
        min-height: 180px;
        /* MATCH BANNER MIN HEIGHT */
    }

    .carousel-inner {
        height: 100%;
        border-radius: 0 0 15px 15px;
    }

    .carousel-item {
        height: 100%;
    }

    /* Ensure content is centered and fills space */
    .carousel-content-wrapper {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1rem;
        /* Prevent overflow if content is too large, though min-height covers it */
        overflow-y: auto;
    }

    /* Indikator Carousel di Bawah */
    .carousel-indicators {
        bottom: 0;
        margin-bottom: 0.5rem;
    }

    .carousel-indicators li {
        background-color: #dee2e6;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .carousel-indicators .active {
        background-color: #007bff;
    }

    /* Typography Clock */
    .digital-clock {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        font-size: 2.5rem;
        /* Lebih besar */
        font-weight: 800;
        color: #343a40;
        line-height: 1;
        margin-bottom: 5px;
    }

    /* Typography KBM Active */
    .kbm-active-title {
        color: #0ca678;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
        animation: blink-text 2s infinite;
    }

    .kbm-active-mapel {
        font-size: 1.2rem;
        font-weight: 700;
        color: #212529;
        display: block;
        margin-bottom: 2px;
    }

    .kbm-active-kelas {
        background: #e3fcec;
        color: #0ca678;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    @keyframes blink-text {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.6;
        }

        100% {
            opacity: 1;
        }
    }

    /* CSS LAINNYA (DARI KODE LAMA) */
    .small-box {
        border-radius: 15px !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .filter-block {
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .filter-siswa {
        background-color: #007bff;
        color: #fff;
    }

    .filter-guru {
        background-color: #28a745;
        color: #fff;
    }

    .filter-siswa-absensi {
        background-color: #ffc107;
        color: #333;
    }

    .filter-block label {
        margin-bottom: 0.1rem;
        font-size: 0.85rem;
    }

    .filter-block .form-group {
        margin-bottom: 5px !important;
    }

    .filter-block .form-control-sm,
    .filter-block select.form-control-sm {
        height: calc(1.8125rem + 2px);
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .filter-siswa-absensi .form-control-sm,
    .filter-siswa-absensi select.form-control-sm {
        background-color: #fff;
        border: 1px solid #ccc;
    }

    .filter-row-container {
        display: flex;
        width: 100%;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-row-container .form-group {
        flex: 1;
        margin-bottom: 0 !important;
    }

    .filter-row-container .btn-terapkan {
        height: calc(1.8125rem + 2px);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .info-app-list td:first-child {
        width: 120px;
        font-weight: 600;
    }

    .table-bordered th {
        text-align: center;
        vertical-align: middle !important;
    }

    .profil-header-centered img {
        max-width: 80px;
        height: auto;
        margin-bottom: 10px;
    }

    .daily-task-list .btn {
        width: 100%;
        margin-bottom: 15px;
        text-align: left;
    }

    /* Fix small-box footer border radius */
    .small-box-footer {
        border-radius: 0 0 10px 10px !important;
    }

    /* Responsif Mobile */
    @media (max-width: 768px) {

        .col-md-9,
        .col-md-3 {
            margin-bottom: 15px;
        }

        .banner-row {
            margin-bottom: 15px;
        }
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row banner-row">

            <div class="col-md-9 col-12 mb-3 mb-md-0 h-100">
                <div class="info-banner">
                    <h3><i class="fas fa-door-open mr-2"></i> Selamat Datang,
                        <?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>
                    </h3>
                    <p>
                        di Aplikasi SIMAKS <?= htmlspecialchars($_SESSION['role'] ?? '') ?>.<br>
                        Selamat bertugas di Tahun Ajaran
                        <strong><?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? 'Belum Aktif') ?></strong>.
                    </p>
                </div>
            </div>

            <div class="col-md-3 col-12 h-100">
                <!-- INFO CARD CAROUSEL (New Design) -->
                <div id="infoCarousel" class="carousel slide info-card-carousel h-100" data-ride="carousel"
                    data-interval="5000">
                    <ol class="carousel-indicators">
                        <li data-target="#infoCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#infoCarousel" data-slide-to="1"></li>
                    </ol>
                    <div class="carousel-inner h-100">
                        <!-- SLIDE 1: WAKTU & TANGGAL -->
                        <div class="carousel-item active h-100">
                            <div class="carousel-content-wrapper text-center">
                                <div class="digital-clock" id="realtime-clock">--:--:--</div>
                                <div class="date-display">
                                    <?php
                                    $hari_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                                    $bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    echo $hari_indo[date('l')] . ', ' . date('d') . ' ' . $bulan_indo[(int) date('m')] . ' ' . date('Y');
                                    ?>
                                </div>
                                <div class="mt-2 text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-clock text-primary"></i> Waktu Server
                                </div>
                            </div>
                        </div>

                        <!-- SLIDE 2: STATUS KBM -->
                        <div class="carousel-item h-100">
                            <div class="carousel-content-wrapper text-center">
                                <div id="kbm-status-carousel-content">
                                    <!-- Default Content (Idle) -->
                                    <i class="fas fa-mug-hot fa-3x text-secondary mb-2"></i>
                                    <h5 class="text-secondary font-weight-bold">Tidak Ada KBM</h5>
                                    <small class="text-muted">Menunggu jadwal...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= number_format($info_card['total_siswa'] ?? 0) ?></h3>
                        <p>Total Siswa Aktif</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-graduate"></i></div>
                    <a href="index.php?mod=siswa" class="small-box-footer">Lihat Detail <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= number_format($info_card['total_guru'] ?? 0) ?></h3>
                        <p>Total Guru Aktif</p>
                    </div>
                    <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <a href="index.php?mod=guru" class="small-box-footer">Lihat Detail <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= number_format($info_card['total_kelas'] ?? 0) ?></h3>
                        <p>Total Rombel Kelas</p>
                    </div>
                    <div class="icon"><i class="fas fa-school"></i></div>
                    <a href="index.php?mod=kelas" class="small-box-footer">Lihat Detail <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= number_format($info_card['total_mapel'] ?? 0) ?></h3>
                        <p>Total Mata Pelajaran</p>
                    </div>
                    <div class="icon"><i class="fas fa-book"></i></div>
                    <a href="index.php?mod=mapel" class="small-box-footer">Lihat Detail <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-4">
                <div class="card card-info card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-university"></i> Profil Sekolah</h3>
                    </div>
                    <div class="card-body">
                        <div class="profil-header-centered text-center">
                            <img src="<?= file_exists($logo_path) ? htmlspecialchars($logo_path) : 'assets/img/default_logo.png' ?>"
                                alt="Logo Sekolah" class="img-fluid">
                            <h4><?= htmlspecialchars($profil_sekolah['nama_sekolah'] ?? 'NAMA SEKOLAH BELUM DIATUR') ?>
                            </h4>
                            <small class="text-muted">NPSN:
                                <?= htmlspecialchars($profil_sekolah['npsn'] ?? '-') ?></small>
                        </div>
                        <hr class="mt-2 mb-2">
                        <table class="table table-borderless table-sm">
                            <tbody class="info-app-list">
                                <tr>
                                    <td>Alamat</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($profil_sekolah['alamat'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Telp</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($profil_sekolah['telp'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($profil_sekolah['email'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Kepala Sekolah</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($profil_sekolah['nama_kepala_sekolah'] ?? '-') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-success card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Tugas Harian Guru</h3>
                    </div>
                    <div class="card-body daily-task-list">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#modal-jadwal-mengajar">
                            <i class="fas fa-calendar-alt"></i> Jadwal Mengajar Hari Ini
                        </button>
                        <a href="index.php?mod=jurnal_kbm" class="btn btn-info">
                            <i class="fas fa-feather-alt"></i> Isi Jurnal KBM Harian
                        </a>
                        <a href="index.php?mod=absensi_mapel" class="btn btn-warning">
                            <i class="fas fa-user-check"></i> Input Absensi Mapel
                        </a>
                        <a href="index.php?mod=input_nilai" class="btn btn-secondary">
                            <i class="fas fa-pencil-alt"></i> Input Nilai Formatif
                        </a>
                        <a href="index.php?mod=penilaian_sumatif" class="btn btn-success">
                            <i class="fas fa-pen-square"></i> Input Nilai Sumatif
                        </a>
                        <a href="index.php?mod=catatan_kelas" class="btn btn-danger">
                            <i class="fas fa-exclamation-triangle"></i> Catatan Kejadian Kelas
                        </a>
                        <?php $user_roles = $_SESSION['roles'] ?? [];
                        $is_admin_or_guru = in_array('Admin', $user_roles) || in_array('Guru', $user_roles);
                        if (!$is_admin_or_guru):
                            ?>
                            <small class="text-danger d-block mt-2">Anda login bukan sebagai **Guru/Admin**. Beberapa
                                fungsionalitas mungkin dibatasi.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-secondary card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Info Aplikasi SIMAKS</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm info-app-list">
                            <tbody>
                                <tr>
                                    <td>Versi Aplikasi</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($app_info['Versi Aplikasi'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Database</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($app_info['Database'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Versi PHP</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($app_info['Versi PHP'] ?? '-') ?></td>
                                </tr>

                                <tr class="font-weight-bold pt-2 pb-0">
                                    <td colspan="3" class="text-center">Informasi Pengembang</td>
                                </tr>
                                <tr>
                                    <td>Pengembang</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($app_info['Pengembang'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>No Wa/Hp</td>
                                    <td>:</td>
                                    <td><?= htmlspecialchars($app_info['Kontak/Medsos'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Web</td>
                                    <td>:</td>
                                    <td><a href="<?= htmlspecialchars($app_info['Web Pengembang'] ?? '#') ?>"
                                            target="_blank"><?= htmlspecialchars($app_info['Web Pengembang'] ?? '-') ?></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-6">
                <div class="card card-primary card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-table"></i> Tabel Rekapitulasi Siswa Per Kelas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="filter-block filter-siswa">
                            <div class="filter-row-container">
                                <div class="form-group">
                                    <label for="filter-ta" class="text-white">Tahun Ajaran</label>
                                    <select name="filter_ta" id="filter-ta" class="form-control form-control-sm" style="width: 100%;">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="filter-tingkat" class="text-white">Tingkat</label>
                                    <select id="filter-tingkat" class="form-control form-control-sm" style="width: 100%;">
                                        <option value="all">Semua</option>
                                        <option value="X">X</option>
                                        <option value="XI">XI</option>
                                        <option value="XII">XII</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="rekap-siswa-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Nama Kelas</th>
                                        <th colspan="2">J.Kelamin</th>
                                        <th rowspan="2">Total</th>
                                        <th colspan="2">Mutasi</th>
                                    </tr>
                                    <tr>
                                        <th>L</th>
                                        <th>P</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i>
                                            Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-primary card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-area"></i> Grafik Rekap Siswa Per Kelas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 400px; padding-top: 20px;">
                            <canvas id="rekapSiswaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        </div>

        <!-- ROW BARU: DATA ALUMNI & TRACER STUDY -->
        <!-- ROW BARU: DATA ALUMNI & TRACER STUDY -->
        <div class="row mb-4">
            
            <!-- TABEL REKAP ALUMNI & TRACER -->
            <div class="col-md-6">
                <div class="card card-purple card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-graduate"></i> Rekapitulasi Lulusan & Tracer Study</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0 text-center table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th rowspan="2" style="vertical-align: middle;" class="text-center">No</th>
                                        <th rowspan="2" style="vertical-align: middle;">Tahun Pelajaran</th>
                                        <th colspan="2">JK</th>
                                        <th rowspan="2" style="vertical-align: middle;">TOTAL</th>
                                        <th colspan="4">Status Pasca Lulus</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 50px;">L</th>
                                        <th style="width: 50px;">P</th>
                                        <th><small>Kuliah</small></th>
                                        <th><small>Kerja</small></th>
                                        <th><small>Usaha</small></th>
                                        <th><small>Lainnya</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tracer_stats)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Belum ada data lulusan untuk ditampilkan.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $no_tr = 1;
                                        foreach($tracer_stats as $ts): 
                                            // Format Tahun Pelajaran:
                                            // Asumsi: Lulusan 2025 adalah dari Tahun Pelajaran 2024/2025
                                            $thn_lulus = (int)$ts['tahun_lulus'];
                                            $thn_awal = $thn_lulus - 1;
                                            $thn_display = "$thn_awal/$thn_lulus";
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no_tr++ ?></td>
                                            <td><strong><?= $thn_display ?></strong></td>
                                            <td><?= $ts['laki_laki'] ?></td>
                                            <td><?= $ts['perempuan'] ?></td>
                                            <td class="font-weight-bold"><?= $ts['total_lulus'] ?></td>
                                            <td>
                                                <span class="badge badge-primary"><?= $ts['ptn_pts'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success"><?= $ts['bekerja'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning"><?= $ts['wirausaha'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary"><?= $ts['lain_lain'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK LULUSAN & TRACER -->
            <div class="col-md-6">
                <div class="card card-purple card-outline h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Grafik Lulusan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3 justify-content-end">
                            <li class="nav-item"><a class="nav-link active" href="#chart-lulusan-tab"
                                    data-toggle="tab">Lulusan</a></li>
                            <li class="nav-item"><a class="nav-link" href="#chart-tracer-tab"
                                    data-toggle="tab">Tracer</a></li>
                        </ul>
                        <div class="tab-content m-0">
                            <!-- CHART 1: LULUSAN PER TAHUN -->
                            <div class="chart tab-pane active" id="chart-lulusan-tab" style="position: relative; height: 300px;">
                                <canvas id="dashboardGradChart" height="230"></canvas>
                            </div>
                            <!-- CHART 2: TRACER STUDY STATUS -->
                            <div class="chart tab-pane" id="chart-tracer-tab" style="position: relative; height: 300px;">
                                <canvas id="dashboardTracerChart" height="230"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="row mb-4">

            <div class="col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-table"></i> Detail Absensi Guru</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="filter-block filter-guru">
                            <h4 class="text-light">Filter Absensi Guru</h4>
                            <div class="filter-row-container">
                                <div class="form-group">
                                    <label for="filter-periode-guru">Periode</label>
                                    <select id="filter-periode-guru" class="form-control form-control-sm">
                                        <option value="daily" selected>Harian</option>
                                        <option value="monthly">Bulanan</option>
                                        <option value="semester">Semester</option>
                                    </select>
                                </div>
                                <div class="form-group" id="date-input-group-guru">
                                    <label>Pilih Waktu</label>
                                    <input type="date" id="date-input-guru-daily" class="form-control form-control-sm"
                                        value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group"> 
                                    <label for="filter-guru-absen" class="text-light">Nama Guru</label>
                                    <select id="filter-guru-absen" class="form-control form-control-sm" style="width: 100%;">
                                        <option value="all">-- Semua Guru --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped" id="rekap-absensi-guru-table">
                                <thead>
                                    <tr>
                                        <th>Nama Guru</th>
                                        <th>Hadir</th>
                                        <th>Sakit</th>
                                        <th>Izin</th>
                                        <th>Alpa</th>
                                        <th>% Hadir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-table"></i> Detail Absensi Siswa Per Kelas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="filter-block filter-siswa-absensi">
                            <h4>Filter Absensi Siswa</h4>
                            <div class="filter-row-container">
                                <div class="form-group">
                                    <label for="filter-periode-siswa">Periode</label>
                                    <select id="filter-periode-siswa" class="form-control form-control-sm">
                                        <option value="daily" selected>Harian</option>
                                        <option value="monthly">Bulanan</option>
                                        <option value="semester">Semester</option>
                                    </select>
                                </div>
                                <div class="form-group" id="date-input-group-siswa">
                                    <label>Pilih Waktu</label>
                                    <input type="date" id="date-input-siswa-daily" class="form-control form-control-sm"
                                        value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="filter-kelas-absen">Kelas (Opsional)</label> 
                                    <select id="filter-kelas-absen" class="form-control form-control-sm" style="width: 100%;">
                                        <option value="all">-- Semua Kelas --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped" id="rekap-absensi-siswa-table">
                                <thead>
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Hadir</th>
                                        <th>Sakit</th>
                                        <th>Izin</th>
                                        <th>Alpa</th>
                                        <th>% Hadir</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafik Absensi Guru</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;"><canvas id="absensiGuruChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Grafik Absensi Siswa</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;"><canvas id="absensiSiswaChart"></canvas></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="modal fade" id="modal-detail-absensi-siswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-user-check mr-2"></i> Detail Absensi Siswa - <span id="detail-kelas-nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="text-muted">Periode: <span id="detail-periode-text"></span></small>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="table-detail-siswa">
                        <thead>
                            <tr class="bg-light">
                                <th class="text-center">No</th>
                                <th class="text-center">NIS</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">H</th>
                                <th class="text-center">S</th>
                                <th class="text-center">I</th>
                                <th class="text-center">A</th>
                                <th class="text-center text-primary font-weight-bold">% Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-jadwal-mengajar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-alt mr-2"></i> Jadwal Mengajar Hari Ini</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0" id="tabel-jadwal-modal">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th class="text-center" style="width: 120px;">Jam</th>
                                <th>Jadwal Terintegrasi (Kelas, Mata Pelajaran & Guru)</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="jadwal-mengajar-content">
                        </tbody>
                    </table>
                </div>
                <div id="modal-status-msg" class="text-center p-4" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<audio id="bell-sound" src="assets/sound/bell-notification.mp3" preload="auto"></audio>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    $(document).ready(function () {
        // --- 1. SETUP VARIABEL GLOBAL ---
        const apiUrlDaily = '<?= $api_url ?>?mod=api&type=jadwal&act=get_daily';
        let globalSchedule = [];
        let previousMapel = null; // Untuk deteksi perubahan jadwal (trigger alarm)

        // Web Audio Context untuk Alarm
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        const audioCtx = new AudioContext();

        // Fungsi Membunyikan Alarm (Beep)
        function playAlarmSound() {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(440, audioCtx.currentTime); // Nada A4
            oscillator.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.5); // Slide ke A5

            gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 1);

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 1); // Bunyi selama 1 detik
        }

        // --- 2. FUNGSI LOAD DATA JADWAL (Dipakai Widget & Modal) ---
        function fetchScheduleData(callback) {
            $.ajax({
                url: apiUrlDaily,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response && response.status === 'success' && response.data) {
                        globalSchedule = response.data;
                        if (callback) callback(true, response.data);
                    } else {
                        console.error('API returned unsuccessful status:', response);
                        if (callback) callback(false, response?.msg || 'API tidak mengembalikan data yang valid');
                    }
                    updateTimeAndStatus();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', { xhr, status, error });
                    const errorMsg = xhr.responseJSON?.msg || xhr.responseText || error || 'Gagal menghubungi server';
                    if (callback) callback(false, errorMsg);
                }
            });
        }

        // --- 3. LOGIKA MODAL ---
        $('#modal-jadwal-mengajar').on('show.bs.modal', function (event) {
            const tbody = $('#jadwal-mengajar-content');
            const msgBox = $('#modal-status-msg');
            tbody.empty();
            msgBox.hide();

            if (globalSchedule.length === 0) {
                // Fallback fetch jika kosong
                msgBox.html('<i class="fas fa-spinner fa-spin"></i> Memuat data...').show();

                fetchScheduleData(function (success, data) {
                    if (success) {
                        msgBox.hide();
                        renderModalTable(data);
                    } else {
                        tbody.empty();
                        msgBox.html(`
                             <div class="alert alert-danger">
                                 <i class="fas fa-exclamation-triangle"></i>
                                 <strong>Error:</strong> ${data}
                                 <br><small>Coba refresh halaman atau hubungi admin.</small>
                             </div>
                         `).show();
                    }
                });
            } else {
                renderModalTable(globalSchedule);
            }
        });

        function renderModalTable(data) {
            const tbody = $('#jadwal-mengajar-content');
            const now = new Date();
            const currentHi = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            
            tbody.empty();
            if (!data || data.length === 0) return;

            // 1. Pre-merge contiguous slots for the same class-mapel-guru
            // First, sort by Class, Subject, Guru, and Start Time
            let sortedData = [...data].sort((a, b) => {
                if (a.kelas !== b.kelas) return a.kelas.localeCompare(b.kelas);
                if (a.mapel !== b.mapel) return a.mapel.localeCompare(b.mapel);
                if (a.guru !== b.guru) return a.guru.localeCompare(b.guru);
                return a.jam_mulai.localeCompare(b.jam_mulai);
            });

            let mergedBlocks = [];
            if (sortedData.length > 0) {
                let current = { ...sortedData[0] };
                for (let i = 1; i < sortedData.length; i++) {
                    let item = sortedData[i];
                    // Check if contiguous and same metadata
                    if (item.kelas === current.kelas && 
                        item.mapel === current.mapel && 
                        item.guru === current.guru && 
                        item.jam_mulai.substring(0, 5) === current.jam_selesai.substring(0, 5)) {
                        // Merge contiguous slots
                        current.jam_selesai = item.jam_selesai;
                    } else {
                        mergedBlocks.push(current);
                        current = { ...item };
                    }
                }
                mergedBlocks.push(current);
            }

            // 2. Group these merged blocks by their final time range
            let groupedSlots = {};
            $.each(mergedBlocks, function (index, item) {
                let start = item.jam_mulai.substring(0, 5);
                let end = item.jam_selesai.substring(0, 5);
                let slotKey = `${start} - ${end}`;
                
                if (!groupedSlots[slotKey]) {
                    groupedSlots[slotKey] = {
                        start: start,
                        end: end,
                        items: []
                    };
                }
                groupedSlots[slotKey].items.push(item);
            });

            // 3. Sort grouped slots by start time
            let sortedSlots = Object.keys(groupedSlots).sort((a, b) => {
                return groupedSlots[a].start.localeCompare(groupedSlots[b].start);
            });

            // 4. Render Rows
            let no = 1;
            $.each(sortedSlots, function (idx, slot) {
                let group = groupedSlots[slot];
                let isActive = (currentHi >= group.start && currentHi < group.end);
                
                let badgeStatus = isActive
                    ? '<span class="badge badge-success">Berlangsung</span>'
                    : (currentHi >= group.end ? '<span class="badge badge-secondary">Selesai</span>' : '<span class="badge badge-light">Belum</span>');

                let detailHtml = group.items.map(item => `
                    <div class="mb-2 p-2 border-left" style="border-left: 4px solid #007bff !important; background: #f8f9fa; border-radius: 0 5px 5px 0;">
                        <div class="d-flex justify-content-between">
                            <span class="badge badge-info">${item.kelas}</span>
                            <span class="text-primary font-weight-bold" style="font-size: 0.9rem;">${item.mapel}</span>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-chalkboard-teacher mr-1"></i> ${item.guru}
                        </div>
                    </div>
                `).join('');

                let rowHtml = `
                    <tr class="${isActive ? 'table-success' : ''}">
                        <td class="text-center align-middle">${no++}</td>
                        <td class="text-center align-middle font-weight-bold" style="white-space: nowrap;">${slot}</td>
                        <td class="align-middle">${detailHtml}</td>
                        <td class="text-center align-middle">${badgeStatus}</td>
                    </tr>
                `;
                tbody.append(rowHtml);
            });
        }


        // --- 4. LOGIKA WIDGET REALTIME & ALARM ---
        let currentActiveIds = ""; // Untuk deteksi perubahan data

        function updateTimeAndStatus() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const currentTimeStr = `${hours}:${minutes}`;

            $('#realtime-clock').text(`${hours}:${minutes}:${seconds}`);

            let activeSubjects = [];
            let endingSubjects = [];
            let activeIdsArr = [];

            if (globalSchedule.length > 0) {
                globalSchedule.forEach(function (item) {
                    let start = item.jam_mulai ? item.jam_mulai.substring(0, 5) : '00:00';
                    let end = item.jam_selesai ? item.jam_selesai.substring(0, 5) : '00:00';

                    if (currentTimeStr >= start && currentTimeStr < end) {
                        activeSubjects.push(item);
                        activeIdsArr.push(`${item.mapel}-${item.kelas}`);
                    }

                    if (currentTimeStr === end && seconds === '00') {
                        endingSubjects.push(item);
                    }
                });
            }

            // Trigger Alarm Alert (Consolidated)
            if (endingSubjects.length > 0) {
                playAlarmSound();
                let msg = endingSubjects.map(s => `${s.mapel} di ${s.kelas}`).join('<br>');
                Swal.fire({
                    icon: 'info',
                    title: 'Waktu Habis!',
                    html: `Jam pelajaran berikut telah selesai:<br><strong>${msg}</strong>`,
                    timer: 10000,
                    timerProgressBar: true
                });
            }

            // Hanya re-render jika data berubah (agar carousel tidak reset setiap detik)
            let newActiveIds = activeIdsArr.sort().join('|');
            if (newActiveIds !== currentActiveIds) {
                currentActiveIds = newActiveIds;
                renderCarouselStatus(activeSubjects);
            }
        }

        function renderCarouselStatus(activeList) {
            const carouselInner = $('.carousel-inner');
            const carouselIndicators = $('.carousel-indicators');

            // Bersihkan slide SETELAH slide pertama (Clock)
            carouselInner.children(':not(:first)').remove();
            carouselIndicators.children(':not(:first)').remove();

            if (activeList && activeList.length > 0) {
                // Tambahkan slide untuk setiap mapel aktif
                activeList.forEach((data, index) => {
                    let slideIdx = index + 1;
                    
                    // Indicator
                    carouselIndicators.append(`<li data-target="#infoCarousel" data-slide-to="${slideIdx}"></li>`);
                    
                    // Slide
                    carouselInner.append(`
                        <div class="carousel-item h-100">
                            <div class="carousel-content-wrapper text-center">
                                <div class="kbm-active-title"><i class="fas fa-circle text-danger blink"></i> SEDANG MENGAJAR</div>
                                <span class="kbm-active-mapel">${data.mapel}</span>
                                <span class="kbm-active-kelas">${data.kelas}</span>
                                <div class="mt-2 text-muted small">
                                    ${data.jam_mulai.substring(0, 5)} - ${data.jam_selesai.substring(0, 5)}
                                </div>
                            </div>
                        </div>
                    `);
                });
            } else {
                // Idle / Istirahat
                carouselIndicators.append(`<li data-target="#infoCarousel" data-slide-to="1"></li>`);
                carouselInner.append(`
                    <div class="carousel-item h-100">
                        <div class="carousel-content-wrapper text-center">
                            <i class="fas fa-mug-hot fa-3x text-secondary mb-2"></i>
                            <h5 class="text-secondary font-weight-bold">Tidak Ada KBM</h5>
                            <small class="text-muted">Menunggu jadwal berikutnya...</small>
                        </div>
                    </div>
                `);
            }
            
            // Re-init carousel state
            $('#infoCarousel').carousel('dispose').carousel({
                interval: 5000,
                ride: 'carousel'
            });
        }

        // --- 5. EKSEKUSI AWAL ---
        fetchScheduleData();
        setInterval(updateTimeAndStatus, 1000);

        // Auto Cycle Carousel
        $('.carousel').carousel();

        // ---[BARU] CHART DASHBOARD ALUMNI & TRACER ---
        // Siapkan data dari PHP
        var tracerStats = <?= json_encode($tracer_stats ?? []) ?>;
        
        if(tracerStats.length > 0) {
            var labels = [];
            var dataL = [];
            var dataP = [];
            var dataStatus = {
                'kuliah': 0, 'kerja': 0, 'wirausaha': 0, 'lain': 0
            };

            // Loop untuk chart Lulusan (Bar) & Agregat Tracer (Doughnut)
            // Data diurutkan dari terbaru, kita balik agar grafik tahun lama di kiri
            var chartData = tracerStats.slice().reverse(); 

            chartData.forEach(function(item) {
                var thnLulus = parseInt(item.tahun_lulus);
                var labelThn = (thnLulus - 1) + '/' + thnLulus;
                
                labels.push(labelThn);
                dataL.push(item.laki_laki);
                dataP.push(item.perempuan);

                // Agregat untuk Tracer Chart
                dataStatus.kuliah += parseInt(item.ptn_pts);
                dataStatus.kerja += parseInt(item.bekerja);
                dataStatus.wirausaha += parseInt(item.wirausaha);
                dataStatus.lain += parseInt(item.lain_lain);
            });

            // 1. Chart Lulusan (Bar)
            var ctxGrad = document.getElementById('dashboardGradChart').getContext('2d');
            new Chart(ctxGrad, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Laki-laki', data: dataL, backgroundColor: '#007bff' },
                        { label: 'Perempuan', data: dataP, backgroundColor: '#dc3545' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                    }
                }
            });

            // 2. Chart Tracer (Doughnut)
            var ctxTracer = document.getElementById('dashboardTracerChart').getContext('2d');
            new Chart(ctxTracer, {
                type: 'doughnut',
                data: {
                    labels: ['Kuliah', 'Bekerja', 'Wirausaha', 'Lainnya'],
                    datasets: [{
                        data: [dataStatus.kuliah, dataStatus.kerja, dataStatus.wirausaha, dataStatus.lain],
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#6c757d']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    });
</script>