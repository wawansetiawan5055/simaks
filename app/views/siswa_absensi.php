<?php 
// app/views/siswa_absensi.php
// Redesign Halaman Rekap Presensi Siswa: Absensi Kelas (Piket), Absensi Per Mapel & Riwayat Harian

include __DIR__ . '/partials/header.php'; 

$ta_nama_display = $_SESSION['nama_ta_aktif'] ?? '2026/2027 Ganjil';
$pct_kehadiran = $persentase_kehadiran ?? 100;

// Tentukan warna & predikat kehadiran
if ($pct_kehadiran >= 90) {
    $status_kehadiran_label = 'Sangat Baik (Disiplin)';
    $status_kehadiran_cls   = 'badge-success';
    $status_kehadiran_bg    = 'linear-gradient(135deg, #059669 0%, #10b981 100%)';
} elseif ($pct_kehadiran >= 75) {
    $status_kehadiran_label = 'Cukup Baik';
    $status_kehadiran_cls   = 'badge-primary';
    $status_kehadiran_bg    = 'linear-gradient(135deg, #2563eb 0%, #3b82f6 100%)';
} elseif ($pct_kehadiran >= 60) {
    $status_kehadiran_label = 'Perlu Perhatian';
    $status_kehadiran_cls   = 'badge-warning';
    $status_kehadiran_bg    = 'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)';
} else {
    $status_kehadiran_label = 'Kritis (Banyak Alpa)';
    $status_kehadiran_cls   = 'badge-danger';
    $status_kehadiran_bg    = 'linear-gradient(135deg, #dc2626 0%, #ef4444 100%)';
}
?>

<style>
    .hero-absensi-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%);
        color: #ffffff;
        border-radius: 18px;
        border: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }
    .hero-absensi-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -30px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.2), transparent 70%);
        border-radius: 50%;
    }
    .stat-card-ab {
        background: #ffffff;
        border-radius: 14px;
        padding: 16px 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        height: 100%;
        display: flex;
        align-items: center;
    }
    .stat-card-ab:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.06);
    }
    .stat-card-ab .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        margin-right: 12px;
    }
    .nav-pills-absensi .nav-link {
        color: #475569;
        font-weight: 700;
        font-size: 0.86rem;
        padding: 9px 20px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        margin-right: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .nav-pills-absensi .nav-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .nav-pills-absensi .nav-link.active {
        background: #10b981 !important;
        color: #ffffff !important;
        border-color: #10b981 !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    }
    .table-absensi thead th {
        background: #f8fafc;
        border-top: none;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        padding: 12px 14px;
    }
    .table-absensi tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
    }
    .progress-ab {
        height: 8px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .border-right-lg {
            border-right: 1px solid #e2e8f0;
        }
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (PRESENSI & ABSENSI SISWA)          */
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
        .hero-absensi-card {
            padding: 12px 14px !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .hero-absensi-card h3 {
            font-size: 1.2rem !important;
            margin-bottom: 2px !important;
        }
        .hero-absensi-card p, .hero-absensi-card span {
            font-size: 0.70rem !important;
        }
        .hero-absensi-card .badge {
            font-size: 0.65rem !important;
            padding: 3px 8px !important;
        }
        .stat-card-ab {
            padding: 8px 10px !important;
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .stat-card-ab .icon-box {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.90rem !important;
            margin-right: 8px !important;
            border-radius: 8px !important;
        }
        .stat-card-ab h4 {
            font-size: 1.0rem !important;
            margin-bottom: 0 !important;
        }
        .stat-card-ab .small {
            font-size: 0.62rem !important;
        }
        .nav-pills-absensi {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px !important;
            margin-bottom: 8px !important;
            gap: 4px !important;
        }
        .nav-pills-absensi .nav-link {
            padding: 6px 14px !important;
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
        .table-absensi thead th {
            padding: 7px 6px !important;
            font-size: 0.66rem !important;
            white-space: nowrap;
        }
        .table-absensi tbody td {
            padding: 7px 6px !important;
            font-size: 0.72rem !important;
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
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Rekap Presensi &amp; Kehadiran Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>siswa_portal/permohonan" class="btn btn-outline-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-file-medical mr-1"></i> Pengajuan Izin / Sakit
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- HERO STATISTIK TINGKAT KEHADIRAN -->
        <div class="card hero-absensi-card shadow-sm p-4 mb-4">
            <div class="row align-items-center">
                <!-- Circular/Percentage Info -->
                <div class="col-lg-5 mb-3 mb-lg-0 border-right-lg">
                    <div class="d-flex align-items-center">
                        <div class="text-center rounded-circle mr-3 p-3 text-white shadow d-flex flex-column align-items-center justify-content-center" style="width: 78px; height: 78px; background: <?= $status_kehadiran_bg ?>; flex-shrink: 0;">
                            <span class="font-weight-bold" style="font-size: 1.35rem; line-height: 1; font-family: 'Poppins', sans-serif;"><?= $pct_kehadiran ?>%</span>
                            <small style="font-size: 0.62rem; text-transform: uppercase; font-weight: 700; opacity: 0.9;">Presensi</small>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge <?= $status_kehadiran_cls ?> px-2.5 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                    <?= $status_kehadiran_label ?>
                                </span>
                            </div>
                            <h6 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">
                                Kehadiran Semester Ini
                            </h6>
                            <p class="small text-light mb-0" style="opacity: 0.85; font-size: 0.78rem;">
                                Total <?= $total_semua ?> hari tercatat &bull; <?= $total_hadir ?> hari hadir aktif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4 Counter Box -->
                <div class="col-lg-7">
                    <div class="row" style="row-gap: 8px;">
                        <div class="col-6 col-sm-3">
                            <div class="stat-card-ab">
                                <div class="icon-box" style="background: #ecfdf5; color: #059669;">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <span class="d-block font-weight-bold text-dark" style="font-size: 1.25rem; line-height: 1; font-family: 'Poppins', sans-serif;"><?= $total_hadir ?></span>
                                    <small class="text-muted font-weight-bold" style="font-size: 0.70rem;">HADIR</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-card-ab">
                                <div class="icon-box" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fas fa-clinic-medical"></i>
                                </div>
                                <div>
                                    <span class="d-block font-weight-bold text-dark" style="font-size: 1.25rem; line-height: 1; font-family: 'Poppins', sans-serif;"><?= $total_sakit ?></span>
                                    <small class="text-muted font-weight-bold" style="font-size: 0.70rem;">SAKIT</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-card-ab">
                                <div class="icon-box" style="background: #fffbeb; color: #d97706;">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <div>
                                    <span class="d-block font-weight-bold text-dark" style="font-size: 1.25rem; line-height: 1; font-family: 'Poppins', sans-serif;"><?= $total_izin ?></span>
                                    <small class="text-muted font-weight-bold" style="font-size: 0.70rem;">IZIN</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-card-ab">
                                <div class="icon-box" style="background: #fef2f2; color: #dc2626;">
                                    <i class="fas fa-user-times"></i>
                                </div>
                                <div>
                                    <span class="d-block font-weight-bold text-dark" style="font-size: 1.25rem; line-height: 1; font-family: 'Poppins', sans-serif;"><?= $total_alpa ?></span>
                                    <small class="text-muted font-weight-bold" style="font-size: 0.70rem;">ALPA</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAV TABS DUAL VIEW (ABSENSI KELAS vs ABSENSI MAPEL) -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 10px;">
            <ul class="nav nav-pills nav-pills-absensi mb-0" id="absensiTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= $tab !== 'mapel' ? 'active' : '' ?>" id="tab-kelas-tab" data-toggle="pill" href="#tab-kelas" role="tab">
                        <i class="fas fa-school mr-1.5"></i> Presensi Kelas (Harian &amp; Piket)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $tab === 'mapel' ? 'active' : '' ?>" id="tab-mapel-tab" data-toggle="pill" href="#tab-mapel" role="tab">
                        <i class="fas fa-book-reader mr-1.5"></i> Presensi Tatap Muka Per Mapel (<?= count($absensi_mapel ?? []) ?>)
                    </a>
                </li>
            </ul>
        </div>

        <!-- TAB CONTENT -->
        <div class="tab-content" id="absensiTabsContent">

            <!-- ======================================================== -->
            <!-- 1. TAB PRESENSI KELAS (HARIAN / PIKET)                  -->
            <!-- ======================================================== -->
            <div class="tab-pane fade <?= $tab !== 'mapel' ? 'show active' : '' ?>" id="tab-kelas" role="tabpanel">
                
                <!-- A. Tabel Rekap Bulanan -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-calendar-alt text-primary mr-1.5"></i> Rekapitulasi Presensi Kelas per Bulan
                            </h6>
                            <small class="text-muted">Akumulasi kehadiran yang dicatat oleh guru piket / wali kelas</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-absensi mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center">#</th>
                                        <th>Bulan Periode</th>
                                        <th class="text-center" style="width: 90px;">Hadir</th>
                                        <th class="text-center" style="width: 90px;">Sakit</th>
                                        <th class="text-center" style="width: 90px;">Izin</th>
                                        <th class="text-center" style="width: 90px;">Alpa</th>
                                        <th class="text-center" style="width: 100px;">Total Hari</th>
                                        <th class="text-center" style="width: 140px;">% Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($absensi_piket)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="fas fa-clipboard fa-3x mb-3 opacity-50"></i>
                                                <h6 class="font-weight-bold text-dark mb-1">Belum Ada Catatan Presensi Kelas</h6>
                                                <p class="small text-muted mb-0">Data absensi harian kelas belum dimasukkan oleh guru piket.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($absensi_piket as $idx => $ab): ?>
                                        <?php
                                            $pct = $ab['total'] > 0 ? round($ab['hadir'] * 100 / $ab['total'], 1) : 0;
                                            $badge_bar = $pct >= 85 ? 'bg-success' : ($pct >= 70 ? 'bg-primary' : ($pct >= 55 ? 'bg-warning' : 'bg-danger'));
                                            $text_clr  = $pct >= 85 ? 'text-success' : ($pct >= 70 ? 'text-primary' : ($pct >= 55 ? 'text-warning' : 'text-danger'));
                                        ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $idx + 1 ?></td>
                                            <td>
                                                <strong class="text-dark d-block"><?= htmlspecialchars($ab['bulan_nama'] ?: $ab['bulan_label']) ?></strong>
                                            </td>
                                            <td class="text-center font-weight-bold text-success"><?= (int)$ab['hadir'] ?></td>
                                            <td class="text-center font-weight-bold text-info"><?= (int)$ab['sakit'] ?></td>
                                            <td class="text-center font-weight-bold text-warning"><?= (int)$ab['izin'] ?></td>
                                            <td class="text-center font-weight-bold text-danger"><?= (int)$ab['alpa'] ?></td>
                                            <td class="text-center font-weight-bold text-muted"><?= (int)$ab['total'] ?></td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="font-weight-bold mr-2 <?= $text_clr ?>" style="font-size: 0.90rem; font-family: 'Poppins', sans-serif;">
                                                        <?= $pct ?>%
                                                    </span>
                                                    <div class="progress-ab flex-grow-1" style="max-width: 60px;">
                                                        <div class="progress-bar <?= $badge_bar ?>" style="width: <?= $pct ?>%;"></div>
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
                </div>

                <!-- B. Riwayat Log Harian Terbaru (Dibagi 2 Kolom Sejajar) -->
                <?php if (!empty($riwayat_piket)): ?>
                <?php 
                    $total_logs = count($riwayat_piket);
                    $half = (int)ceil($total_logs / 2);
                    $chunk1 = array_slice($riwayat_piket, 0, $half);
                    $chunk2 = array_slice($riwayat_piket, $half);
                ?>
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-history text-secondary mr-1.5"></i> Riwayat Harian Presensi Kelas Terbaru
                            </h6>
                            <small class="text-muted">Daftar kehadiran per tanggal (Total <?= $total_logs ?> data dibagi dalam 2 kolom)</small>
                        </div>
                        <span class="badge badge-light border font-weight-bold text-muted" style="font-size: 0.74rem;">
                            2 Kolom Sejajar
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-lg-6 col-12 mb-3 mb-lg-0 border-right-lg">
                                <div class="table-responsive">
                                    <table class="table table-hover table-absensi mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 35px;" class="text-center">#</th>
                                                <th style="width: 120px;">Tanggal</th>
                                                <th style="width: 85px;" class="text-center">Status</th>
                                                <th>Keterangan / Pencatat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($chunk1 as $rIdx => $log): ?>
                                            <?php
                                                $st = $log['status'];
                                                $bCls = 'badge-secondary';
                                                if ($st === 'Hadir') $bCls = 'badge-success';
                                                elseif ($st === 'Sakit') $bCls = 'badge-primary';
                                                elseif ($st === 'Izin') $bCls = 'badge-warning';
                                                elseif ($st === 'Alpa') $bCls = 'badge-danger';
                                            ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-muted" style="font-size: 0.82rem;"><?= $rIdx + 1 ?></td>
                                                <td>
                                                    <strong class="text-dark d-block" style="font-size: 0.86rem;"><?= date('d M Y', strtotime($log['tanggal'])) ?></strong>
                                                    <small class="text-muted" style="font-size: 0.72rem;"><?= hari_indo($log['tanggal']) ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?= $bCls ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                                                        <?= htmlspecialchars($st) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-dark d-block font-weight-bold" style="line-height: 1.25; font-size: 0.80rem;">
                                                        <?= htmlspecialchars($log['dicatat_oleh'] ?: 'Petugas Piket') ?>
                                                    </small>
                                                    <small class="text-muted" style="font-size: 0.74rem;">
                                                        <?= htmlspecialchars($log['keterangan'] ?: ($log['nama_kelas'] ?? '-')) ?>
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-lg-6 col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-absensi mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 35px;" class="text-center">#</th>
                                                <th style="width: 120px;">Tanggal</th>
                                                <th style="width: 85px;" class="text-center">Status</th>
                                                <th>Keterangan / Pencatat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($chunk2)): ?>
                                                <tr><td colspan="4" class="text-center text-muted py-4 small"><em>Tidak ada data tambahan.</em></td></tr>
                                            <?php else: ?>
                                                <?php foreach ($chunk2 as $rIdx => $log): ?>
                                                <?php
                                                    $st = $log['status'];
                                                    $bCls = 'badge-secondary';
                                                    if ($st === 'Hadir') $bCls = 'badge-success';
                                                    elseif ($st === 'Sakit') $bCls = 'badge-primary';
                                                    elseif ($st === 'Izin') $bCls = 'badge-warning';
                                                    elseif ($st === 'Alpa') $bCls = 'badge-danger';
                                                ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold text-muted" style="font-size: 0.82rem;"><?= $half + $rIdx + 1 ?></td>
                                                    <td>
                                                        <strong class="text-dark d-block" style="font-size: 0.86rem;"><?= date('d M Y', strtotime($log['tanggal'])) ?></strong>
                                                        <small class="text-muted" style="font-size: 0.72rem;"><?= hari_indo($log['tanggal']) ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge <?= $bCls ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                                                            <?= htmlspecialchars($st) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-dark d-block font-weight-bold" style="line-height: 1.25; font-size: 0.80rem;">
                                                            <?= htmlspecialchars($log['dicatat_oleh'] ?: 'Petugas Piket') ?>
                                                        </small>
                                                        <small class="text-muted" style="font-size: 0.74rem;">
                                                            <?= htmlspecialchars($log['keterangan'] ?: ($log['nama_kelas'] ?? '-')) ?>
                                                        </small>
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
                </div>
                <?php endif; ?>

            </div>

            <!-- ======================================================== -->
            <!-- 2. TAB PRESENSI PER MATA PELAJARAN                      -->
            <!-- ======================================================== -->
            <div class="tab-pane fade <?= $tab === 'mapel' ? 'show active' : '' ?>" id="tab-mapel" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-book text-info mr-1.5"></i> Rekapitulasi Presensi Tatap Muka per Mata Pelajaran
                            </h6>
                            <small class="text-muted">Kehadiran belajar mengajar yang diinput oleh masing-masing guru pengampu</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-absensi mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center">#</th>
                                        <th style="min-width: 200px;">Mata Pelajaran &amp; Guru Pengampu</th>
                                        <th class="text-center" style="width: 80px;">Hadir</th>
                                        <th class="text-center" style="width: 80px;">Sakit</th>
                                        <th class="text-center" style="width: 80px;">Izin</th>
                                        <th class="text-center" style="width: 80px;">Alpa</th>
                                        <th class="text-center" style="width: 100px;">Pertemuan</th>
                                        <th class="text-center" style="width: 150px;">% Kehadiran</th>
                                        <th class="text-center" style="width: 110px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($absensi_mapel)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-5">
                                                <i class="fas fa-book-open fa-3x mb-3 opacity-50"></i>
                                                <h6 class="font-weight-bold text-dark mb-1">Belum Ada Data Presensi Mapel</h6>
                                                <p class="small text-muted mb-0">Guru mata pelajaran belum menginput data absensi pertemuan KBM pada semester ini.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($absensi_mapel as $mIdx => $m): ?>
                                        <?php
                                            $pct = (float)$m['pct_hadir'];
                                            $badge_bar = $pct >= 85 ? 'bg-success' : ($pct >= 70 ? 'bg-primary' : ($pct >= 55 ? 'bg-warning' : 'bg-danger'));
                                            $text_clr  = $pct >= 85 ? 'text-success' : ($pct >= 70 ? 'text-primary' : ($pct >= 55 ? 'text-warning' : 'text-danger'));
                                            $status_tag = $pct >= 85 ? 'Disiplin' : ($pct >= 70 ? 'Cukup' : ($pct >= 55 ? 'Kurang' : 'Kritis'));
                                            $status_tag_cls = $pct >= 85 ? 'badge-success' : ($pct >= 70 ? 'badge-primary' : ($pct >= 55 ? 'badge-warning' : 'badge-danger'));
                                        ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $mIdx + 1 ?></td>
                                            <td>
                                                <strong class="text-dark d-block"><?= htmlspecialchars($m['nama_mapel']) ?></strong>
                                                <small class="text-muted"><i class="fas fa-chalkboard-teacher mr-1"></i> <?= htmlspecialchars($m['nama_guru'] ?? 'Guru Mapel') ?></small>
                                            </td>
                                            <td class="text-center font-weight-bold text-success"><?= (int)$m['hadir'] ?></td>
                                            <td class="text-center font-weight-bold text-info"><?= (int)$m['sakit'] ?></td>
                                            <td class="text-center font-weight-bold text-warning"><?= (int)$m['izin'] ?></td>
                                            <td class="text-center font-weight-bold text-danger"><?= (int)$m['alpa'] ?></td>
                                            <td class="text-center font-weight-bold text-muted"><?= (int)$m['total'] ?></td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="font-weight-bold mr-2 <?= $text_clr ?>" style="font-size: 0.90rem; font-family: 'Poppins', sans-serif;">
                                                        <?= $pct ?>%
                                                    </span>
                                                    <div class="progress-ab flex-grow-1" style="max-width: 60px;">
                                                        <div class="progress-bar <?= $badge_bar ?>" style="width: <?= $pct ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $status_tag_cls ?> px-2.5 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                    <?= $status_tag ?>
                                                </span>
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

        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
