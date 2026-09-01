<?php 
// app/views/siswa_nilai.php
// Redesign Halaman Transkrip & Nilai Siswa: Tampilan Tabel Seluruh Mapel Komprehensif (Kurikulum Merdeka)

include __DIR__ . '/partials/header.php'; 

$nama_siswa_display = $siswa_data['nama'] ?? ($_SESSION['nama_lengkap'] ?? ($_SESSION['nama'] ?? 'Siswa'));
$nisn_display = !empty($siswa_data['nisn']) ? $siswa_data['nisn'] : '-';
$nipd_display = !empty($siswa_data['nipd']) ? $siswa_data['nipd'] : '-';
$kelas_display = !empty($siswa_data['nama_kelas']) ? $siswa_data['nama_kelas'] : ($kelas['nama_kelas'] ?? '-');
?>

<style>
    .nilai-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);
        flex-shrink: 0;
    }
    .hero-stat-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
        color: #ffffff;
        border-radius: 18px;
        border: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
    }
    .hero-stat-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -30px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25), transparent 70%);
        border-radius: 50%;
    }
    .stat-pill {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 12px;
        padding: 12px 14px;
        backdrop-filter: blur(6px);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .nav-pills-custom .nav-link {
        color: #475569;
        font-weight: 700;
        font-size: 0.86rem;
        padding: 10px 18px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        margin-right: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .nav-pills-custom .nav-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .nav-pills-custom .nav-link.active {
        background: #4f46e5 !important;
        color: #ffffff !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    }
    .table-rekap-mapel thead th {
        background: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        color: #475569 !important;
        letter-spacing: 0.5px;
        padding: 14px 12px !important;
        vertical-align: middle !important;
    }
    .table-rekap-mapel tbody td {
        vertical-align: middle !important;
        font-size: 0.88rem;
        padding: 14px 12px !important;
    }
    .grade-badge-sm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 1.05rem;
        font-family: 'Poppins', sans-serif;
    }
    .sub-drawer-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (NILAI & TRANSKRIP SISWA)           */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .hero-stat-card {
            padding: 12px 14px !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .hero-stat-card h5 {
            font-size: 0.92rem !important;
            margin-bottom: 2px !important;
        }
        .hero-stat-card p, .hero-stat-card span {
            font-size: 0.70rem !important;
        }
        .stat-pill {
            padding: 6px 8px !important;
            border-radius: 8px !important;
        }
        .stat-pill h3, .stat-pill .font-weight-bold {
            font-size: 1.1rem !important;
        }
        .stat-pill span {
            font-size: 0.60rem !important;
            letter-spacing: 0.2px !important;
        }
        .nav-pills-custom {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px !important;
            margin-bottom: 8px !important;
            gap: 4px !important;
        }
        .nav-pills-custom .nav-link {
            padding: 6px 12px !important;
            font-size: 0.72rem !important;
            white-space: nowrap !important;
            margin-right: 0 !important;
            margin-bottom: 0 !important;
            flex-shrink: 0;
        }
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border: none;
            margin-bottom: 6px !important;
        }
        .table-rekap-mapel thead th {
            padding: 7px 6px !important;
            font-size: 0.66rem !important;
            white-space: nowrap;
        }
        .table-rekap-mapel tbody td {
            padding: 7px 6px !important;
            font-size: 0.72rem !important;
        }
        .grade-badge-sm {
            width: 26px !important;
            height: 26px !important;
            font-size: 0.78rem !important;
            border-radius: 6px !important;
        }
        .sub-drawer-card {
            padding: 8px 10px !important;
            border-radius: 8px !important;
        }
        .card {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .card-header {
            padding: 8px 12px !important;
        }
        .card-header h5 {
            font-size: 0.82rem !important;
        }
        .card-body {
            padding: 8px !important;
        }
    }

    @media print {
        .no-print, .main-sidebar, .main-header, .content-header, .btn, .nav-pills-custom {
            display: none !important;
        }
        body { background: #ffffff !important; color: #000000 !important; }
        .hero-stat-card { background: #ffffff !important; color: #000000 !important; border: 1px solid #333 !important; }
        .hero-stat-card h5, .hero-stat-card span { color: #000000 !important; }
        .collapse { display: block !important; }
    }
</style>

<div class="content-header pt-3 mb-2 no-print">
    <div class="container-fluid">
        <!-- TOP HEADER -->
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="nilai-icon-box mr-3">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Transkrip &amp; Nilai Akademik Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Cetak Transkrip Nilai
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- HERO STATISTIK RINGKASAN AKADEMIK -->
        <div class="card hero-stat-card shadow-sm p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center shadow" style="width: 60px; height: 60px; font-size: 1.5rem; flex-shrink: 0; background: linear-gradient(135deg, #4f46e5, #3b82f6) !important;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold mb-1 text-white" style="font-family: 'Poppins', sans-serif;">
                                <?= htmlspecialchars($nama_siswa_display) ?>
                            </h5>
                            <p class="small text-light mb-0" style="opacity: 0.9;">
                                <span class="badge badge-light border text-dark font-weight-bold px-2 py-0.5 mr-1" style="font-size: 0.74rem;">
                                    Kelas <?= htmlspecialchars($kelas_display) ?>
                                </span>
                                <span>NISN: <strong><?= htmlspecialchars($nisn_display) ?></strong></span>
                                <?php if ($nipd_display !== '-'): ?>
                                    <span class="ml-1">&bull; NIPD: <strong><?= htmlspecialchars($nipd_display) ?></strong></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="row text-center" style="row-gap: 8px;">
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Rata-Rata Rapor</small>
                                <span class="font-weight-bold" style="font-size: 1.40rem; color: #38bdf8; font-family: 'Poppins', sans-serif;">
                                    <?= $summary['global_avg'] > 0 ? number_format($summary['global_avg'], 1) : '-' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Formatif TP</small>
                                <span class="font-weight-bold text-success" style="font-size: 1.40rem; font-family: 'Poppins', sans-serif;">
                                    <?= $summary['total_formatif'] ?> <small style="font-size: 0.7rem; color: #a7f3d0;">Nilai</small>
                                </span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Sumatif (LM/SAS)</small>
                                <span class="font-weight-bold" style="font-size: 1.40rem; color: #818cf8; font-family: 'Poppins', sans-serif;">
                                    <?= $summary['total_sumatif'] ?> <small style="font-size: 0.7rem; color: #c7d2fe;">Nilai</small>
                                </span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Tugas &amp; CBT</small>
                                <span class="font-weight-bold" style="font-size: 1.40rem; color: #fbbf24; font-family: 'Poppins', sans-serif;">
                                    <?= $summary['total_lms'] + $summary['total_cbt'] ?> <small style="font-size: 0.7rem; color: #fde68a;">Aktivitas</small>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAV TABS KATEGORI PENILAIAN -->
        <ul class="nav nav-pills nav-pills-custom mb-3 no-print" id="gradeTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-rekap-tab" data-toggle="pill" href="#tab-rekap" role="tab">
                    <i class="fas fa-table mr-1"></i> Rapor &amp; Rekap Seluruh Mapel (<?= count($nilai_grouped) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-formatif-tab" data-toggle="pill" href="#tab-formatif" role="tab">
                    <i class="fas fa-check-circle text-success mr-1"></i> Formatif TP (<?= $summary['total_formatif'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-sumatif-tab" data-toggle="pill" href="#tab-sumatif" role="tab">
                    <i class="fas fa-award text-primary mr-1"></i> Sumatif LM &amp; SAS (<?= $summary['total_sumatif'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-lms-tab" data-toggle="pill" href="#tab-lms" role="tab">
                    <i class="fas fa-laptop-code text-info mr-1"></i> Tugas LMS (<?= $summary['total_lms'] ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-cbt-tab" data-toggle="pill" href="#tab-cbt" role="tab">
                    <i class="fas fa-desktop text-warning mr-1"></i> Riwayat CBT (<?= $summary['total_cbt'] ?>)
                </a>
            </li>
        </ul>

        <!-- TAB CONTENT -->
        <div class="tab-content" id="gradeTabsContent">

            <!-- ======================================================== -->
            <!-- 1. TAB REKAPITULASI TABEL SELURUH MATA PELAJARAN         -->
            <!-- ======================================================== -->
            <div class="tab-pane fade show active" id="tab-rekap" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-list-alt text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Daftar Nilai Akademik Seluruh Mata Pelajaran
                            </h6>
                        </div>
                        <span class="badge badge-light border text-muted px-3 py-1.5 rounded-pill font-weight-bold">
                            Tahun Ajaran Aktif: 2026/2027 Ganjil
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-rekap-mapel mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 30px;" class="text-center">#</th>
                                    <th style="min-width: 170px;">Mata Pelajaran &amp; Guru</th>
                                    <th class="text-center" style="width: 55px;">KKTP</th>
                                    <th class="text-center" style="width: 65px;" title="Nilai Sikap & Presensi">Sikap</th>
                                    <th class="text-center" style="width: 65px;" title="Penugasan Mandiri LMS">LMS</th>
                                    <th class="text-center" style="width: 75px;" title="Rerata Formatif per TP">Formatif</th>
                                    <th class="text-center" style="width: 75px;" title="Sumatif Lingkup Materi">Sum. LM</th>
                                    <th class="text-center" style="width: 65px;" title="Sumatif Tengah Semester">STS</th>
                                    <th class="text-center" style="width: 65px;" title="Sumatif Akhir Semester">SAS</th>
                                    <th class="text-center bg-light" style="width: 100px;" title="Nilai Akhir Rapor Berdasarkan Pembobotan Guru">Nilai Akhir<br><small class="text-primary font-weight-bold">(NA)</small></th>
                                    <th style="min-width: 200px;">Deskripsi Capaian Rapor</th>
                                    <th class="text-center" style="width: 85px;">Ketuntasan</th>
                                    <th class="text-center" style="width: 75px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($nilai_grouped)): ?>
                                    <tr>
                                        <td colspan="13" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <h6 class="font-weight-bold text-dark mb-1">Belum Ada Nilai Rapor</h6>
                                            <p class="small text-muted mb-0">Guru belum memasukkan data nilai formatif maupun sumatif pada semester ini.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($nilai_grouped as $idx => $item): ?>
                                    <?php
                                        $n_akhir = $item['nilai_akhir'];
                                        $kktp = $item['kktp'] ?: 75;
                                        $tuntas = $item['is_tuntas'];
                                        $bot = $item['bobot'] ?? ['sikap'=>0, 'lms'=>10, 'formatif'=>15, 'sumatif_lm'=>30, 'sts'=>20, 'sas'=>25];
                                        
                                        $badge_bg = '#f1f5f9';
                                        $badge_color = '#64748b';
                                        if ($n_akhir !== null) {
                                            if ($n_akhir >= 85) { $badge_bg = '#ecfdf5'; $badge_color = '#059669'; }
                                            elseif ($n_akhir >= 75) { $badge_bg = '#eef2ff'; $badge_color = '#4f46e5'; }
                                            elseif ($n_akhir >= 65) { $badge_bg = '#fffbeb'; $badge_color = '#d97706'; }
                                            else { $badge_bg = '#fef2f2'; $badge_color = '#dc2626'; }
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <strong class="text-dark d-block" style="font-size: 0.92rem;"><?= htmlspecialchars($item['nama_mapel']) ?></strong>
                                            <small class="text-muted"><i class="fas fa-chalkboard-teacher mr-1"></i> <?= htmlspecialchars($item['nama_guru']) ?></small>
                                        </td>
                                        <td class="text-center font-weight-bold text-muted"><?= $kktp ?></td>
                                        
                                        <!-- 1. Sikap & Presensi -->
                                        <td class="text-center font-weight-bold text-secondary">
                                            <?= $item['val_sikap'] !== null ? number_format($item['val_sikap'], 1) : '<span class="text-muted font-weight-normal">-</span>' ?>
                                        </td>

                                        <!-- 2. LMS Penugasan -->
                                        <td class="text-center font-weight-bold text-info">
                                            <?= $item['val_lms'] !== null ? number_format($item['val_lms'], 1) : '<span class="text-muted font-weight-normal">-</span>' ?>
                                        </td>

                                        <!-- 3. Formatif TP -->
                                        <td class="text-center font-weight-bold text-success">
                                            <?= $item['val_formatif'] !== null ? number_format($item['val_formatif'], 1) : '<span class="text-muted font-weight-normal">-</span>' ?>
                                        </td>

                                        <!-- 4. Sumatif Lingkup Materi -->
                                        <td class="text-center font-weight-bold" style="color: #4f46e5;">
                                            <?= $item['val_sumatif_lm'] !== null ? number_format($item['val_sumatif_lm'], 1) : '<span class="text-muted font-weight-normal">-</span>' ?>
                                        </td>

                                        <!-- 5. Sumatif Tengah Semester (STS) -->
                                        <td class="text-center font-weight-bold text-warning">
                                            <?= $item['val_sts'] !== null ? number_format($item['val_sts'], 1) : '<span class="text-muted font-weight-normal">-</span>' ?>
                                        </td>

                                        <!-- 6. Sumatif Akhir Semester (SAS) -->
                                        <td class="text-center font-weight-bold text-primary">
                                            <?= $item['val_sas'] !== null ? number_format($item['val_sas'], 1) : '<span class="text-muted font-weight-normal">-</span>' ?>
                                        </td>

                                        <!-- Nilai Akhir (NA) & Predikat -->
                                        <td class="text-center bg-light">
                                            <?php if ($n_akhir !== null): ?>
                                                <div class="d-inline-flex align-items-center">
                                                    <span class="font-weight-bold mr-1.5" style="font-size: 1.15rem; color: <?= $badge_color ?>; font-family: 'Poppins', sans-serif;">
                                                        <?= number_format($n_akhir, 2) ?>
                                                    </span>
                                                    <span class="badge font-weight-bold" style="background: <?= $badge_bg ?>; color: <?= $badge_color ?>; border: 1px solid <?= $badge_color ?>; font-size: 0.68rem;">
                                                        <?= $item['predikat'] ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted font-weight-bold">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Deskripsi Rapor / Capaian Kompetensi -->
                                        <td>
                                            <small class="text-muted d-block" style="line-height: 1.35; font-size: 0.80rem;">
                                                <?= htmlspecialchars($item['deskripsi_rapor'] ?: 'Belum ada catatan deskripsi capaian TP dari guru pengampu.') ?>
                                            </small>
                                        </td>

                                        <!-- Ketuntasan -->
                                        <td class="text-center">
                                            <?php if ($tuntas === true): ?>
                                                <span class="badge badge-success px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                    <i class="fas fa-check-circle mr-1"></i> Tuntas
                                                </span>
                                            <?php elseif ($tuntas === false): ?>
                                                <span class="badge badge-danger px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> Remedial
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-light border text-muted px-2 py-0.5" style="font-size: 0.70rem;">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Tombol Drawer Rincian -->
                                        <td class="text-center">
                                            <button class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 font-weight-bold shadow-sm" type="button" data-toggle="collapse" data-target="#detail_row_<?= $idx ?>">
                                                <i class="fas fa-search-plus mr-0.5"></i> Rincian
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- EXPANDABLE DRAWER ROW -->
                                    <tr class="collapse" id="detail_row_<?= $idx ?>">
                                        <td colspan="13" class="p-0 border-0">
                                            <div class="sub-drawer-card m-3">
                                                <!-- INFO BOBOT PENILAIAN MAPEL -->
                                                <div class="d-flex align-items-center justify-content-between flex-wrap p-2.5 mb-3 rounded border" style="background: #ffffff; gap: 8px;">
                                                    <div class="small font-weight-bold text-dark">
                                                        <i class="fas fa-balance-scale text-primary mr-1"></i> Konfigurasi Bobot Penilaian Guru (<?= htmlspecialchars($item['nama_mapel']) ?>):
                                                    </div>
                                                    <div class="small">
                                                        <span class="badge badge-light border mr-1">Sikap: <strong><?= (float)$bot['sikap'] ?>%</strong></span>
                                                        <span class="badge badge-light border mr-1">LMS: <strong><?= (float)$bot['lms'] ?>%</strong></span>
                                                        <span class="badge badge-light border mr-1">Formatif: <strong><?= (float)$bot['formatif'] ?>%</strong></span>
                                                        <span class="badge badge-light border mr-1">Sum. LM: <strong><?= (float)$bot['sumatif_lm'] ?>%</strong></span>
                                                        <span class="badge badge-light border mr-1">STS: <strong><?= (float)$bot['sts'] ?>%</strong></span>
                                                        <span class="badge badge-light border">SAS: <strong><?= (float)$bot['sas'] ?>%</strong></span>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- Rincian Formatif -->
                                                    <div class="col-md-4 col-12 mb-3 mb-md-0 border-right">
                                                        <span class="small font-weight-bold text-success d-block mb-2">
                                                            <i class="fas fa-check-circle mr-1"></i> Penilaian Formatif TP (<?= count($item['formatif']) ?>)
                                                        </span>
                                                        <?php if (!empty($item['formatif'])): ?>
                                                            <ul class="list-unstyled mb-0 small">
                                                                <?php foreach ($item['formatif'] as $f): ?>
                                                                    <li class="d-flex justify-content-between border-bottom py-1">
                                                                        <span class="text-truncate mr-2" style="max-width: 75%;" title="<?= htmlspecialchars($f['deskripsi_tp']) ?>">
                                                                            <strong><?= htmlspecialchars($f['kode_tp']) ?></strong>: <?= htmlspecialchars($f['deskripsi_tp']) ?>
                                                                        </span>
                                                                        <span class="font-weight-bold text-dark"><?= $f['nilai'] ?></span>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <p class="small text-muted font-italic mb-0">Belum ada input penilaian formatif.</p>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Rincian Sumatif -->
                                                    <div class="col-md-4 col-12 mb-3 mb-md-0 border-right">
                                                        <span class="small font-weight-bold text-primary d-block mb-2">
                                                            <i class="fas fa-award mr-1"></i> Penilaian Sumatif Resmi Guru (<?= count($item['sumatif']) ?>)
                                                        </span>
                                                        <?php if (!empty($item['sumatif'])): ?>
                                                            <ul class="list-unstyled mb-0 small">
                                                                <?php foreach ($item['sumatif'] as $s_item): ?>
                                                                    <li class="d-flex justify-content-between border-bottom py-1">
                                                                        <span class="text-truncate mr-2" style="max-width: 75%;">
                                                                            <strong><?= htmlspecialchars($s_item['jenis_sumatif']) ?></strong> &mdash; <?= htmlspecialchars($s_item['nama_penilaian']) ?>
                                                                        </span>
                                                                        <span class="font-weight-bold text-primary"><?= $s_item['nilai'] ?></span>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <p class="small text-muted font-italic mb-0">Belum ada input sumatif resmi guru.</p>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Rincian CBT -->
                                                    <div class="col-md-4 col-12">
                                                        <span class="small font-weight-bold text-warning d-block mb-2" style="color: #d97706 !important;">
                                                            <i class="fas fa-desktop mr-1"></i> Asesmen Ujian CBT (<?= count($item['cbt']) ?>)
                                                        </span>
                                                        <?php if (!empty($item['cbt'])): ?>
                                                            <ul class="list-unstyled mb-0 small">
                                                                <?php foreach ($item['cbt'] as $c_item): ?>
                                                                    <li class="d-flex justify-content-between border-bottom py-1">
                                                                        <span class="text-truncate mr-2" style="max-width: 75%;">
                                                                            <?= htmlspecialchars($c_item['nama_ujian']) ?>
                                                                        </span>
                                                                        <span class="font-weight-bold text-warning" style="color: #d97706 !important;"><?= $c_item['nilai_akhir'] ?></span>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php else: ?>
                                                            <p class="small text-muted font-italic mb-0">Belum ada rekaman ujian CBT.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RINGKASAN KEHADIRAN & CATATAN WALI KELAS -->
                <div class="row">
                    <!-- 1. Rekap Kehadiran / Presensi Siswa -->
                    <div class="col-lg-6 col-12 mb-3">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; background: #ffffff;">
                            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-clock text-info mr-2" style="font-size: 1.1rem;"></i>
                                    <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                        Rekapitulasi Kehadiran Siswa
                                    </h6>
                                </div>
                                <span class="badge badge-success px-3 py-1 rounded-pill font-weight-bold" style="font-size: 0.76rem;">
                                    <?= $kehadiran['persentase'] ?>% Hadir
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row text-center">
                                    <div class="col-3 border-right">
                                        <small class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 0.70rem;">Hadir</small>
                                        <span class="font-weight-bold text-success" style="font-size: 1.45rem; font-family: 'Poppins', sans-serif;">
                                            <?= $kehadiran['hadir'] ?>
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">Hari</small>
                                    </div>
                                    <div class="col-3 border-right">
                                        <small class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 0.70rem;">Sakit</small>
                                        <span class="font-weight-bold text-warning" style="font-size: 1.45rem; font-family: 'Poppins', sans-serif;">
                                            <?= $kehadiran['sakit'] ?>
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">Hari</small>
                                    </div>
                                    <div class="col-3 border-right">
                                        <small class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 0.70rem;">Izin</small>
                                        <span class="font-weight-bold text-info" style="font-size: 1.45rem; font-family: 'Poppins', sans-serif;">
                                            <?= $kehadiran['izin'] ?>
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">Hari</small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 0.70rem;">Alpa</small>
                                        <span class="font-weight-bold text-danger" style="font-size: 1.45rem; font-family: 'Poppins', sans-serif;">
                                            <?= $kehadiran['alpa'] ?>
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.68rem;">Hari</small>
                                    </div>
                                </div>
                                <div class="progress mt-3" style="height: 7px; border-radius: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kehadiran['persentase'] ?>%" aria-valuenow="<?= $kehadiran['persentase'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-2">
                                    <span>Total Hari Efektif: <strong><?= $kehadiran['total'] ?> Pertemuan</strong></span>
                                    <a href="<?= BASE_URL ?>siswa_portal/absensi" class="text-primary font-weight-bold">
                                        Lihat Rincian Presensi &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Catatan Wali Kelas & Karakter -->
                    <div class="col-lg-6 col-12 mb-3">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; background: #ffffff;">
                            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-comment-dots text-primary mr-2" style="font-size: 1.1rem;"></i>
                                    <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                        Catatan &amp; Rekomendasi Wali Kelas
                                    </h6>
                                </div>
                                <?php if (!empty($siswa_data['nama_wali_kelas'])): ?>
                                    <span class="badge badge-light border text-muted px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        <i class="fas fa-user-tie mr-1"></i> <?= htmlspecialchars($siswa_data['nama_wali_kelas']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <?php if (!empty($catatan_wali)): ?>
                                    <div class="p-3 bg-light rounded-lg border" style="font-size: 0.88rem; line-height: 1.6; color: #334155; font-style: italic;">
                                        "<?= nl2br(htmlspecialchars($catatan_wali)) ?>"
                                    </div>
                                <?php else: ?>
                                    <div class="p-3 bg-light rounded-lg border text-muted small font-italic mb-3">
                                        <i class="fas fa-info-circle mr-1"></i> Belum ada catatan perkembangan khusus dari wali kelas untuk semester ini. Pertahankan prestasi dan tingkatkan semangat belajar!
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center small text-muted pt-2 border-top">
                                    <span>Status Rapor: <strong class="text-success"><i class="fas fa-check-circle mr-1"></i> Semester Aktif</strong></span>
                                    <a href="<?= BASE_URL ?>siswa_portal/progress" class="text-info font-weight-bold">
                                        <i class="fas fa-star mr-1"></i> Pengembangan Karakter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 2. TAB DETAIL FORMATIF TP                                 -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-formatif" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-check-circle text-success mr-2"></i> Rekap Penilaian Formatif per Tujuan Pembelajaran (TP)
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-grade mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kode &amp; Tujuan Pembelajaran (TP)</th>
                                    <th>Guru Pengampu</th>
                                    <th class="text-center">Nilai</th>
                                    <th>Deskripsi Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows_formatif)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data penilaian formatif TP.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rows_formatif as $i => $rf): ?>
                                    <tr>
                                        <td class="text-muted font-weight-bold"><?= $i + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($rf['nama_mapel']) ?></strong></td>
                                        <td>
                                            <span class="badge badge-light border text-dark font-weight-bold px-2 py-0.5 mb-1"><?= htmlspecialchars($rf['kode_tp']) ?></span>
                                            <div class="small text-muted"><?= htmlspecialchars($rf['deskripsi_tp']) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($rf['nama_guru'] ?? '-') ?></td>
                                        <td class="text-center font-weight-bold text-success" style="font-size: 1.05rem;"><?= $rf['nilai'] ?></td>
                                        <td class="small text-muted"><?= htmlspecialchars($rf['deskripsi'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 3. TAB DETAIL SUMATIF (LM & SAS)                          -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-sumatif" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-award text-primary mr-2"></i> Rekap Penilaian Sumatif Lingkup Materi &amp; Akhir Semester (SAS)
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-grade mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Jenis &amp; Nama Penilaian</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Status</th>
                                    <th>Deskripsi Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows_sumatif)): ?>
                                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data penilaian sumatif.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rows_sumatif as $i => $rs): ?>
                                    <?php 
                                        $kktp = (float)($rs['kktp'] ?? 75);
                                        $is_tuntas = ((float)$rs['nilai'] >= $kktp);
                                    ?>
                                    <tr>
                                        <td class="text-muted font-weight-bold"><?= $i + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($rs['nama_mapel']) ?></strong></td>
                                        <td>
                                            <span class="badge badge-primary px-2 py-0.5 rounded-pill font-weight-bold mr-1" style="font-size: 0.70rem;"><?= htmlspecialchars($rs['jenis_sumatif']) ?></span>
                                            <strong><?= htmlspecialchars($rs['nama_penilaian']) ?></strong>
                                        </td>
                                        <td class="small text-muted"><?= !empty($rs['tanggal_penilaian']) ? date('d M Y', strtotime($rs['tanggal_penilaian'])) : '-' ?></td>
                                        <td class="text-center font-weight-bold text-primary" style="font-size: 1.05rem;"><?= $rs['nilai'] ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $is_tuntas ? 'success' : 'danger' ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                <?= $is_tuntas ? 'Tuntas' : 'Remedial' ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($rs['deskripsi_capaian'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 4. TAB DETAIL TUGAS LMS                                   -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-lms" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-laptop-code text-info mr-2"></i> Rekap Penilaian Tugas Mandiri LMS
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-grade mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Judul Tugas</th>
                                    <th>Tanggal Pengumpulan</th>
                                    <th class="text-center">Nilai</th>
                                    <th>Catatan Guru</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows_lms)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada tugas mandiri LMS yang dinilai.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rows_lms as $i => $rl): ?>
                                    <tr>
                                        <td class="text-muted font-weight-bold"><?= $i + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($rl['nama_mapel']) ?></strong></td>
                                        <td><?= htmlspecialchars($rl['judul_tugas']) ?></td>
                                        <td class="small text-muted"><?= !empty($rl['tgl_upload']) ? date('d M Y, H:i', strtotime($rl['tgl_upload'])) : '-' ?></td>
                                        <td class="text-center font-weight-bold text-info" style="font-size: 1.05rem;"><?= $rl['nilai'] ?></td>
                                        <td class="small text-muted"><?= htmlspecialchars($rl['catatan_guru'] ?? '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 5. TAB DETAIL RIWAYAT CBT ONLINE                          -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-cbt" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-desktop text-warning mr-2"></i> Rekap Hasil Asesmen Ujian CBT Online
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-grade mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Nama Ujian CBT</th>
                                    <th class="text-center">Nilai PG</th>
                                    <th class="text-center">Nilai Esai</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Status</th>
                                    <th>Waktu Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows_cbt)): ?>
                                    <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat pengerjaan ujian CBT.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rows_cbt as $i => $rc): ?>
                                    <?php 
                                        $pg_pass = (float)($rc['passing_grade'] ?? 75);
                                        $is_lulus = ((float)$rc['nilai_akhir'] >= $pg_pass);
                                    ?>
                                    <tr>
                                        <td class="text-muted font-weight-bold"><?= $i + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($rc['nama_mapel']) ?></strong></td>
                                        <td><?= htmlspecialchars($rc['nama_ujian']) ?></td>
                                        <td class="text-center font-weight-bold"><?= $rc['nilai_pg'] !== null ? number_format((float)$rc['nilai_pg'], 1) : '-' ?></td>
                                        <td class="text-center font-weight-bold"><?= $rc['nilai_essay'] !== null ? number_format((float)$rc['nilai_essay'], 1) : '-' ?></td>
                                        <td class="text-center font-weight-bold text-warning" style="font-size: 1.05rem; color: #d97706 !important;">
                                            <?= $rc['nilai_akhir'] !== null ? number_format((float)$rc['nilai_akhir'], 1) : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $is_lulus ? 'success' : 'danger' ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                <?= $is_lulus ? 'Lulus' : 'Belum Lulus' ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted"><?= !empty($rc['dihitung_pada']) ? date('d M Y, H:i', strtotime($rc['dihitung_pada'])) : '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
