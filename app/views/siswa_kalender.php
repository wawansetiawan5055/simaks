<?php 
// app/views/siswa_kalender.php
// Redesign Halaman Kalender Akademik Siswa: Kalender Interaktif FullCalendar & Timeline Agenda Terpadu

include __DIR__ . '/partials/header.php'; 

$ta_nama_display = $_SESSION['nama_ta_aktif'] ?? '2026/2027 Ganjil';
$today = date('Y-m-d');

// Cari Agenda Terdekat / Mendatang
$upcoming_event = null;
$total_kegiatan = count($kegiatan ?? []);
$total_ujian = 0;
$total_libur = 0;
$total_sekolah = 0;

foreach ($kegiatan ?? [] as $k) {
    if ($k['kategori'] === 'Ujian') $total_ujian++;
    elseif ($k['kategori'] === 'Libur') $total_libur++;
    else $total_sekolah++;

    if (!$upcoming_event && $k['tanggal_mulai'] >= $today) {
        $upcoming_event = $k;
    }
}
if (!$upcoming_event && !empty($kegiatan)) {
    $upcoming_event = end($kegiatan);
}

// Group kegiatan by month for timeline view
$grouped = [];
$bulan_ind = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

foreach ($kegiatan ?? [] as $k) {
    $m = date('m', strtotime($k['tanggal_mulai']));
    $y = date('Y', strtotime($k['tanggal_mulai']));
    $monthKey = $y . '-' . $m;
    $monthName = $bulan_ind[$m] . ' ' . $y;
    
    if (!isset($grouped[$monthKey])) {
        $grouped[$monthKey] = [
            'name' => $monthName,
            'events' => []
        ];
    }
    $grouped[$monthKey]['events'][] = $k;
}

// Format JSON events for FullCalendar
$fc_events = [];
foreach ($kegiatan ?? [] as $k) {
    // End date in FullCalendar is exclusive for all-day events, so add 1 day if multi-day
    $end_date = $k['tanggal_selesai'];
    if ($end_date && $end_date !== $k['tanggal_mulai']) {
        $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
    } else {
        $end_date = $k['tanggal_mulai'];
    }

    $fc_events[] = [
        'id'          => $k['id_kegiatan'] ?? $k['id_kalender'],
        'title'       => $k['nama_kegiatan'] ?? $k['judul_kegiatan'],
        'start'       => $k['tanggal_mulai'],
        'end'         => $end_date,
        'backgroundColor' => $k['warna'] ?: '#3b82f6',
        'borderColor'     => $k['warna'] ?: '#3b82f6',
        'textColor'       => '#ffffff',
        'extendedProps'   => [
            'kategori'    => $k['kategori'] ?? 'Kegiatan Sekolah',
            'keterangan'  => $k['keterangan'] ?? ($k['deskripsi'] ?? ''),
            'tgl_mulai'   => date('d M Y', strtotime($k['tanggal_mulai'])),
            'tgl_selesai' => $k['tanggal_selesai'] ? date('d M Y', strtotime($k['tanggal_selesai'])) : date('d M Y', strtotime($k['tanggal_mulai'])),
        ]
    ];
}
?>

<!-- FullCalendar 5.11.5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .kalender-header-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(225, 29, 72, 0.25);
        flex-shrink: 0;
    }
    .hero-kalender-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%);
        color: #ffffff;
        border-radius: 18px;
        border: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
    }
    .hero-kalender-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -30px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(244, 63, 94, 0.2), transparent 70%);
        border-radius: 50%;
    }
    .stat-pill-cal {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 10px 12px;
        backdrop-filter: blur(6px);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .nav-pills-kalender .nav-link {
        color: #475569;
        font-weight: 700;
        font-size: 0.86rem;
        padding: 9px 18px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        margin-right: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .nav-pills-kalender .nav-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .nav-pills-kalender .nav-link.active {
        background: #e11d48 !important;
        color: #ffffff !important;
        border-color: #e11d48 !important;
        box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);
    }
    /* FullCalendar Customizations */
    #calendar {
        font-family: 'Poppins', sans-serif;
    }
    .fc-theme-standard th {
        background: #f8fafc;
        padding: 10px 0 !important;
        color: #475569;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        border-color: #e2e8f0 !important;
    }
    .fc-theme-standard td, .fc-theme-standard .fc-scrollgrid {
        border-color: #f1f5f9 !important;
    }
    .fc-daygrid-day-number {
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
        padding: 6px 8px !important;
    }
    .fc-day-today {
        background: #fff1f2 !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        color: #e11d48 !important;
        font-weight: 800;
    }
    .fc-event {
        border-radius: 6px !important;
        padding: 2px 5px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        cursor: pointer;
        border: none !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        margin-bottom: 2px !important;
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        filter: brightness(1.05);
    }
    .fc-button-primary {
        background: #ffffff !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
    }
    .fc-button-primary:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }
    .fc-button-primary:not(:disabled).fc-button-active, .fc-button-primary:not(:disabled):active {
        background: #e11d48 !important;
        border-color: #e11d48 !important;
        color: #ffffff !important;
    }
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
    }
    .date-badge-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (KALENDER AKADEMIK SISWA)          */
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
        .hero-kalender-card {
            padding: 12px 14px !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .hero-kalender-card h5 {
            font-size: 0.92rem !important;
        }
        .hero-kalender-card p, .hero-kalender-card span {
            font-size: 0.70rem !important;
        }
        .stat-pill-cal {
            padding: 6px 8px !important;
            border-radius: 8px !important;
        }
        .stat-pill-cal h3 {
            font-size: 1.1rem !important;
        }
        .nav-pills-kalender {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px !important;
            margin-bottom: 8px !important;
            gap: 4px !important;
        }
        .nav-pills-kalender .nav-link {
            padding: 6px 14px !important;
            font-size: 0.72rem !important;
            white-space: nowrap !important;
            margin-right: 0 !important;
            margin-bottom: 0 !important;
            flex-shrink: 0;
        }
        .card {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .card-header {
            padding: 10px 12px !important;
        }
        .card-body {
            padding: 8px !important;
        }
        .fc-header-toolbar {
            flex-direction: column !important;
            gap: 6px !important;
            align-items: center !important;
            margin-bottom: 8px !important;
        }
        .fc-toolbar-chunk {
            display: flex !important;
            justify-content: center !important;
        }
        .fc-toolbar-title {
            font-size: 0.92rem !important;
            text-align: center !important;
        }
        .fc-button-primary {
            padding: 4px 8px !important;
            font-size: 0.70rem !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.70rem !important;
            padding: 2px 4px !important;
        }
        .fc-event {
            font-size: 0.62rem !important;
            padding: 1px 3px !important;
        }
        .date-badge-box {
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
        }
        .date-badge-box h5 {
            font-size: 0.85rem !important;
            margin: 0 !important;
        }
        .date-badge-box small {
            font-size: 0.58rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; background: linear-gradient(135deg, #e11d48, #be123c); color: #ffffff; box-shadow: 0 6px 16px rgba(225, 29, 72, 0.25);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kalender Akademik Sekolah
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>kalender_akademik/export_pdf?id_ta=<?= $_SESSION['id_ta_aktif'] ?? 7 ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- HERO HIGHLIGHT AGENDA TERDEKAT & STATISTIK -->
        <div class="card hero-kalender-card shadow-sm p-4 mb-4">
            <div class="row align-items-center">
                <!-- Highlight Agenda Terdekat -->
                <div class="col-lg-5 mb-3 mb-lg-0 border-right-lg">
                    <?php if ($upcoming_event): ?>
                    <?php
                        $tgl_mulai_event = $upcoming_event['tanggal_mulai'];
                        $diff_days = (int)((strtotime($tgl_mulai_event) - strtotime($today)) / 86400);
                        
                        $badge_status = 'Mendatang';
                        $badge_cls = 'badge-primary';
                        if ($diff_days === 0) {
                            $badge_status = 'Hari Ini 🔥';
                            $badge_cls = 'badge-danger';
                        } elseif ($diff_days > 0) {
                            $badge_status = $diff_days . ' Hari Lagi';
                            $badge_cls = 'badge-info';
                        } else {
                            $badge_status = 'Sedang Berjalan';
                            $badge_cls = 'badge-success';
                        }
                    ?>
                    <div class="d-flex align-items-start">
                        <div class="mr-3 text-center rounded-lg p-2 text-white shadow" style="background: <?= htmlspecialchars($upcoming_event['warna'] ?: '#e11d48') ?>; min-width: 58px; border-radius: 12px;">
                            <span class="d-block font-weight-bold" style="font-size: 1.35rem; line-height: 1;"><?= date('d', strtotime($upcoming_event['tanggal_mulai'])) ?></span>
                            <small class="text-uppercase" style="font-size: 0.68rem; font-weight: 700;"><?= date('M', strtotime($upcoming_event['tanggal_mulai'])) ?></small>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge <?= $badge_cls ?> px-2 py-0.5 mr-2 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                                    <?= $badge_status ?>
                                </span>
                                <span class="badge badge-light border text-dark font-weight-bold" style="font-size: 0.68rem;">
                                    <?= htmlspecialchars($upcoming_event['kategori'] ?? 'Kegiatan') ?>
                                </span>
                            </div>
                            <h6 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">
                                <?= htmlspecialchars($upcoming_event['nama_kegiatan'] ?? $upcoming_event['judul_kegiatan']) ?>
                            </h6>
                            <p class="small text-light mb-0" style="opacity: 0.85; font-size: 0.78rem;">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?= date('d M Y', strtotime($upcoming_event['tanggal_mulai'])) ?>
                                <?= ($upcoming_event['tanggal_selesai'] && $upcoming_event['tanggal_selesai'] !== $upcoming_event['tanggal_mulai']) ? ' s.d. ' . date('d M Y', strtotime($upcoming_event['tanggal_selesai'])) : '' ?>
                            </p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-calendar-check fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-white mb-0">Kalender Akademik Terpadu</h6>
                            <small class="text-light opacity-75">Tahun Ajaran <?= htmlspecialchars($ta_nama_display) ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 4 Statistik Card -->
                <div class="col-lg-7">
                    <div class="row text-center" style="row-gap: 8px;">
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill-cal">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Total Agenda</small>
                                <span class="font-weight-bold" style="font-size: 1.40rem; color: #38bdf8; font-family: 'Poppins', sans-serif;">
                                    <?= $total_kegiatan ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill-cal">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Kegiatan</small>
                                <span class="font-weight-bold text-info" style="font-size: 1.40rem; font-family: 'Poppins', sans-serif;">
                                    <?= $total_sekolah ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill-cal">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Ujian / STS / SAS</small>
                                <span class="font-weight-bold text-warning" style="font-size: 1.40rem; font-family: 'Poppins', sans-serif;">
                                    <?= $total_ujian ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="stat-pill-cal">
                                <small class="text-uppercase d-block" style="font-size: 0.68rem; color: #cbd5e1 !important; font-weight: 700;">Libur &amp; Cuti</small>
                                <span class="font-weight-bold text-danger" style="font-size: 1.40rem; font-family: 'Poppins', sans-serif;">
                                    <?= $total_libur ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAV TABS DUAL VIEW (KALENDER VISUAL vs TIMELINE AGENDA) -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 10px;">
            <ul class="nav nav-pills nav-pills-kalender mb-0" id="kalenderTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-visual-tab" data-toggle="pill" href="#tab-visual" role="tab">
                        <i class="fas fa-calendar-alt mr-1"></i> Kalender Visual Interaktif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-timeline-tab" data-toggle="pill" href="#tab-timeline" role="tab">
                        <i class="fas fa-list-ul mr-1"></i> Agenda Bulanan (<?= $total_kegiatan ?>)
                    </a>
                </li>
            </ul>

            <!-- Filter Kategori Event Dropdown -->
            <div class="d-flex align-items-center">
                <span class="small font-weight-bold text-muted mr-2 d-none d-sm-inline"><i class="fas fa-filter text-primary mr-1"></i> Filter:</span>
                <select id="filterKategoriCalendar" class="form-control form-control-sm rounded-pill px-3 shadow-sm border" style="min-width: 170px; font-weight: 600;">
                    <option value="">Semua Kategori</option>
                    <option value="Kegiatan Sekolah">🔵 Kegiatan Sekolah</option>
                    <option value="Ujian">🟠 Ujian / Asesmen</option>
                    <option value="Libur">🔴 Hari Libur</option>
                    <option value="Rapat">🟢 Rapat / Dinas</option>
                    <option value="Lainnya">⚪ Lainnya</option>
                </select>
            </div>
        </div>

        <!-- TAB CONTENT -->
        <div class="tab-content" id="kalenderTabsContent">

            <!-- ======================================================== -->
            <!-- 1. TAB KALENDER VISUAL FULLCALENDAR                      -->
            <!-- ======================================================== -->
            <div class="tab-pane fade show active" id="tab-visual" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; background: #ffffff;">
                    <div class="card-body p-3 p-md-4">
                        <div id="calendar"></div>
                    </div>
                    <div class="card-footer bg-white border-top py-2.5 px-4 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <div class="small font-weight-bold text-muted d-flex align-items-center flex-wrap" style="gap: 12px;">
                            <span><i class="fas fa-circle text-primary mr-1"></i> Kegiatan Sekolah</span>
                            <span><i class="fas fa-circle text-warning mr-1"></i> Ujian / Asesmen</span>
                            <span><i class="fas fa-circle text-danger mr-1"></i> Hari Libur</span>
                            <span><i class="fas fa-circle text-success mr-1"></i> Rapat / Dinas</span>
                        </div>
                        <small class="text-muted font-italic">
                            <i class="fas fa-info-circle text-info mr-1"></i> Klik pada kegiatan di kalender untuk melihat rincian lengkap.
                        </small>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 2. TAB TIMELINE AGENDA BULANAN                           -->
            <!-- ======================================================== -->
            <div class="tab-pane fade" id="tab-timeline" role="tabpanel">
                <?php if (empty($grouped)): ?>
                    <div class="card shadow-sm border-0 p-5 text-center text-muted" style="border-radius: 16px;">
                        <i class="far fa-calendar-times fa-3x mb-3 opacity-50"></i>
                        <h6 class="font-weight-bold text-dark mb-1">Belum Ada Agenda Terjadwal</h6>
                        <p class="small mb-0">Belum ada agenda kegiatan yang dijadwalkan pada tahun ajaran ini.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($grouped as $mKey => $monthData): ?>
                        <div class="col-lg-6 col-12 mb-4">
                            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                                <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                                    <h6 class="m-0 text-white font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                                        <i class="far fa-calendar-alt mr-2 text-rose" style="color: #fda4af;"></i>
                                        <?= htmlspecialchars($monthData['name']) ?>
                                    </h6>
                                    <span class="badge badge-light px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                        <?= count($monthData['events']) ?> Agenda
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($monthData['events'] as $ev): ?>
                                        <?php
                                            $d1 = date('d', strtotime($ev['tanggal_mulai']));
                                            $d2 = ($ev['tanggal_selesai'] && $ev['tanggal_selesai'] !== $ev['tanggal_mulai']) ? '-' . date('d', strtotime($ev['tanggal_selesai'])) : '';
                                            $dateStr = $d1 . $d2;
                                            $is_past = ($ev['tanggal_selesai'] ?: $ev['tanggal_mulai']) < $today;
                                        ?>
                                        <div class="list-group-item p-3 d-flex align-items-start border-bottom <?= $is_past ? 'opacity-75' : '' ?>" style="transition: background 0.15s ease;">
                                            <div class="date-badge-box mr-3 text-center" style="border-left: 3px solid <?= htmlspecialchars($ev['warna'] ?: '#3b82f6') ?> !important;">
                                                <span class="font-weight-bold text-dark" style="font-size: 1.05rem; line-height: 1.1; font-family: 'Poppins', sans-serif;">
                                                    <?= $dateStr ?>
                                                </span>
                                                <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.62rem;">
                                                    <?= date('M', strtotime($ev['tanggal_mulai'])) ?>
                                                </small>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.88rem; line-height: 1.3;">
                                                        <?= htmlspecialchars($ev['nama_kegiatan'] ?? $ev['judul_kegiatan']) ?>
                                                    </h6>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center mt-1" style="gap: 6px;">
                                                    <span class="badge font-weight-bold" style="font-size: 0.68rem; background: <?= htmlspecialchars($ev['warna'] ?: '#e2e8f0') ?>; color: #ffffff; border-radius: 6px;">
                                                        <?= htmlspecialchars($ev['kategori'] ?? 'Kegiatan') ?>
                                                    </span>
                                                    <?php if (!empty($ev['keterangan'])): ?>
                                                        <span class="small text-muted" style="font-size: 0.76rem;">
                                                            <i class="fas fa-info-circle mr-1 text-secondary"></i> <?= htmlspecialchars($ev['keterangan']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<!-- FullCalendar 5.11.5 JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var rawEvents = <?= json_encode($fc_events) ?>;
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            list:  'Daftar'
        },
        events: rawEvents,
        eventClick: function(info) {
            var props = info.event.extendedProps;
            var tglStr = props.tgl_mulai;
            if (props.tgl_selesai && props.tgl_selesai !== props.tgl_mulai) {
                tglStr += ' s.d. ' + props.tgl_selesai;
            }

            Swal.fire({
                title: '<strong style="font-family: Poppins, sans-serif; font-size: 1.15rem;">' + info.event.title + '</strong>',
                html: `
                    <div class="text-left py-2" style="font-size: 0.88rem; line-height: 1.6;">
                        <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="far fa-calendar-alt text-primary mr-1"></i> Tanggal:</span>
                            <span class="font-weight-bold text-dark">${tglStr}</span>
                        </div>
                        <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-tag text-info mr-1"></i> Kategori:</span>
                            <span class="badge" style="background: ${info.event.backgroundColor}; color: #fff; font-size: 0.75rem; padding: 4px 8px;">
                                ${props.kategori}
                            </span>
                        </div>
                        ${props.keterangan ? `
                        <div class="mt-2">
                            <span class="text-muted d-block small font-weight-bold mb-1"><i class="fas fa-align-left text-secondary mr-1"></i> Keterangan Kegiatan:</span>
                            <div class="p-2.5 bg-light rounded text-dark border small">${props.keterangan}</div>
                        </div>
                        ` : '<div class="text-muted small font-italic mt-2">Tidak ada catatan keterangan tambahan.</div>'}
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#e11d48',
                customClass: {
                    popup: 'rounded-xl shadow'
                }
            });
        },
        height: 'auto',
        dayMaxEvents: 3
    });

    calendar.render();

    // Re-render when switching tabs
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        if (e.target.id === 'tab-visual-tab') {
            calendar.render();
            calendar.updateSize();
        }
    });

    // Filter Kategori
    $('#filterKategoriCalendar').on('change', function() {
        var selectedCat = $(this).val();
        calendar.removeAllEvents();
        if (!selectedCat) {
            calendar.addEventSource(rawEvents);
        } else {
            var filtered = rawEvents.filter(function(ev) {
                return ev.extendedProps.kategori === selectedCat;
            });
            calendar.addEventSource(filtered);
        }
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
