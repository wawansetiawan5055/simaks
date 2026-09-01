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
    'Versi Aplikasi' => 'SIMAKS V 2.3.5',
    'Database' => 'MySQL/MariaDB',
    'Versi PHP' => '8.0',
    'Pengembang' => 'ICT SMA Plus Al Manshuriyah',
    'Web Pengembang' => 'simaksdev.my.id',
    'WhatsApp' => '08886185500'
];

$logo_path = get_app_logo();

// 4. Data Dummy (Fallback jika Controller tidak mengirim data)
$info_card = $info_card ?? ['total_siswa' => 1250, 'total_guru' => 75, 'total_kelas' => 40, 'total_mapel' => 15];
$profil_sekolah = $profil_sekolah ?? ['nama_sekolah' => 'SMA Plus Contoh', 'npsn' => '20247166', 'alamat' => 'Jl. Pendidikan No. 1', 'telp' => '021-123456', 'email' => 'info@sekolah.sch.id', 'nama_kepala_sekolah' => 'Kepala Sekolah'];

// 5. Cek Role
$user_roles = $_SESSION['roles'] ?? [];
$is_guru = (in_array('Guru', $user_roles) || in_array('Admin', $user_roles));
?>

<style>
    /* UTILITIES & SPACING NORMALIZATION */
    .content-header {
        padding-top: 15px !important;
        padding-bottom: 0 !important;
    }
    .content {
        padding-top: 0 !important;
    }

    /* 1. DESKTOP / TABLET: 2-COLUMN SEPARATED CARDS (BANNER KIRI 75% + INFO KANAN 25%) */
    .banner-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        margin-left: -10px !important;
        margin-right: -10px !important;
        margin-bottom: 0 !important;
    }

    .banner-col-main {
        flex: 0 0 75%;
        max-width: 75%;
        padding-left: 10px !important;
        padding-right: 10px !important;
        margin-bottom: 20px !important;
        display: flex;
        flex-direction: column;
    }

    .banner-col-side {
        flex: 0 0 25%;
        max-width: 25%;
        padding-left: 10px !important;
        padding-right: 10px !important;
        margin-bottom: 20px !important;
        display: flex;
        flex-direction: column;
    }

    /* Penyesuaian Lebar Khusus Laptop / Chromebook (1024px - 1400px) */
    @media (min-width: 768px) and (max-width: 1400px) {
        .banner-col-main {
            flex: 0 0 68%;
            max-width: 68%;
        }
        .banner-col-side {
            flex: 0 0 32%;
            max-width: 32%;
        }
    }

    .info-banner-container {
        background: linear-gradient(135deg, #1e293b 0%, #293548 50%, #334155 100%);
        color: #ffffff !important;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        height: 170px;
        min-height: 170px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        flex: 1 1 auto;
    }

    .banner-slide-content {
        padding: 1.1rem 1.6rem;
        height: 170px;
        min-height: 170px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: transparent !important;
        color: #ffffff !important;
        overflow: hidden;
    }

    .info-card-carousel {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        border-top: 4px solid #3b82f6;
        border-left: 1px solid rgba(0, 0, 0, 0.05);
        border-right: 1px solid rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 170px;
        min-height: 170px;
        flex: 1 1 auto;
    }

    .carousel-inner {
        height: 100%;
        border-radius: 0 0 14px 14px;
    }

    .carousel-item {
        height: 100%;
    }

    .carousel-content-wrapper {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 0.6rem 0.8rem;
        overflow-y: auto;
    }

    .digital-clock {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 4px;
    }

    .date-display {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
    }

    .kbm-active-title {
        color: #059669;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 2px;
        font-size: 0.70rem;
    }

    .kbm-active-mapel {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.22;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
        max-height: 2.5em;
        margin: 2px 0;
    }

    .kbm-active-kelas {
        background: #dcfce7;
        color: #15803d;
        padding: 1px 6px;
        border-radius: 6px;
        font-size: 0.70rem;
        font-weight: 600;
    }

    /* 2. MOBILE: MERGED & UNIFIED HERO SLIDER (RAPI, STABIL, KONSISTEN) */
    .mobile-hero-slider {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.14);
        border: 1px solid rgba(255,255,255,0.15);
        margin-bottom: 12px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%) !important;
        min-height: 136px;
        height: auto;
    }
    .mobile-hero-slider .carousel-inner,
    .mobile-hero-slider .carousel-item {
        min-height: 136px;
        height: auto;
    }
    .mobile-hero-slide {
        padding: 0.65rem 0.9rem 1.25rem;
        min-height: 136px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
    }

    /* Pastikan seluruh teks banner kontras tinggi & terbaca jelas */
    .info-banner-container,
    .info-banner-container h1,
    .info-banner-container h2,
    .info-banner-container h3,
    .info-banner-container h4,
    .info-banner-container h5,
    .info-banner-container h6,
    .info-banner-container p,
    .info-banner-container span:not(.badge),
    .mobile-hero-slider,
    .mobile-hero-slide,
    .mobile-hero-slide h1,
    .mobile-hero-slide h2,
    .mobile-hero-slide h3,
    .mobile-hero-slide h4,
    .mobile-hero-slide h5,
    .mobile-hero-slide h6,
    .mobile-hero-slide p,
    .mobile-hero-slide span:not(.badge) {
        color: #ffffff !important;
    }

    .banner-user-name-mobile,
    .mobile-hero-slide h4,
    .mobile-hero-slide .user-greeting {
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
        margin-bottom: 2px !important;
        color: #ffffff !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
        display: block !important;
    }

    .mobile-hero-slide .text-white-50,
    .info-banner-container .text-white-50 {
        color: #e2e8f0 !important; /* Warna abu terang yang sangat kontras di background gelap */
    }

    /* Indicators universal */
    .banner-indicators, .carousel-indicators {
        bottom: 4px !important;
        margin-bottom: 0 !important;
        z-index: 10;
    }
    .banner-indicators li, .carousel-indicators li {
        width: 6px !important;
        height: 6px !important;
        border-radius: 50% !important;
        margin: 0 3px !important;
        background-color: rgba(255, 255, 255, 0.4) !important;
        border: none !important;
        transition: all 0.25s ease;
    }
    .banner-indicators li.active, .carousel-indicators li.active {
        width: 20px !important;
        border-radius: 6px !important;
        background-color: #38bdf8 !important;
    }

    .task-item-box {
        background: rgba(255, 255, 255, 0.12) !important;
        border: 1px solid rgba(255, 255, 255, 0.22) !important;
        backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 5px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        min-height: 36px;
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
        margin-bottom: 0 !important;
    }

    .card {
        margin-bottom: 0 !important;
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

    /* === PREMIUM DASHBOARD CARD & TOOLS (MINIMIZE/MAXIMIZE) === */
    .premium-dashboard-card {
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .premium-dashboard-card .card-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0.85rem 1.25rem !important;
        background: #ffffff !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
    }
    .premium-dashboard-card .card-title {
        font-size: 0.92rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .premium-dashboard-card .card-title i {
        color: #6366f1;
        font-size: 0.95rem;
    }
    .card-tools {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        margin-left: auto !important;
        float: none !important;
    }
    .card-tools .btn-tool {
        width: 30px !important;
        height: 30px !important;
        padding: 0 !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        background: #f8fafc !important;
        color: #64748b !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 0.72rem !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }
    .card-tools .btn-tool:hover {
        background: #e0e7ff !important;
        color: #4338ca !important;
        border-color: #c7d2fe !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 8px rgba(99, 102, 241, 0.18) !important;
    }
    .card-tools .btn-tool:active {
        transform: scale(0.92) !important;
    }

    /* === STAT CARD (Gaya Kartu Waktu) === */
    .stat-card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
        color: inherit;
        margin-bottom: 0;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0,0,0,0.13);
        text-decoration: none;
        color: inherit;
    }
    .stat-card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.4rem 1.5rem 1rem 1.5rem;
        flex: 1;
    }
    .stat-card-info .stat-number {
        font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        font-size: 2.4rem;
        font-weight: 800;
        line-height: 1;
        color: #1a1a2e;
        margin-bottom: 4px;
    }
    .stat-card-info .stat-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin: 0;
    }
    .stat-card-icon {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #fff;
        flex-shrink: 0;
    }
    .stat-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.55rem 1.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border-top: 1px solid rgba(0,0,0,0.06);
        transition: background 0.2s;
    }
    .stat-card-footer:hover {
        background: rgba(0,0,0,0.03);
        text-decoration: none;
    }
    /* Warna border top dan aksen per kartu */
    .stat-card--siswa  { border-top: 5px solid #007bff; }
    .stat-card--guru   { border-top: 5px solid #28a745; }
    .stat-card--rombel { border-top: 5px solid #fd7e14; }
    .stat-card--mapel  { border-top: 5px solid #dc3545; }
    .stat-card--siswa  .stat-card-icon { background: linear-gradient(135deg,#007bff,#0056d2); }
    .stat-card--guru   .stat-card-icon { background: linear-gradient(135deg,#28a745,#1a7a30); }
    .stat-card--rombel .stat-card-icon { background: linear-gradient(135deg,#fd7e14,#e05a00); }
    .stat-card--mapel  .stat-card-icon { background: linear-gradient(135deg,#dc3545,#a71c2c); }
    .stat-card--siswa  .stat-number { color: #007bff; }
    .stat-card--guru   .stat-number { color: #28a745; }
    .stat-card--rombel .stat-number { color: #fd7e14; }
    .stat-card--mapel  .stat-number { color: #dc3545; }
    .stat-card--siswa  .stat-card-footer { color: #007bff; }
    .stat-card--guru   .stat-card-footer { color: #28a745; }
    .stat-card--rombel .stat-card-footer { color: #fd7e14; }
    .stat-card--mapel  .stat-card-footer { color: #dc3545; }

    /* Responsif Mobile & Grid Standardization */
    .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    .row, .row.mb-4, .row.banner-row {
        margin-left: -10px !important;
        margin-right: -10px !important;
        margin-bottom: 0 !important; 
    }

    [class*="col-"] {
        padding-left: 10px !important;
        padding-right: 10px !important;
        margin-bottom: 20px !important; /* The only vertical spacer */
    }

    @media (max-width: 768px) {
        .content-header {
            padding-top: 10px;
        }
        .info-banner {
            padding: 1rem;
            min-height: auto;
        }
        .container-fluid {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        .row, .row.mb-4, .row.banner-row {
            margin-left: -5px !important;
            margin-right: -5px !important;
        }
        [class*="col-"] {
            padding-left: 5px !important;
            padding-right: 5px !important;
            margin-bottom: 10px !important;
        }
        .stat-card {
            min-height: 90px !important;
            border-radius: 12px !important;
        }
        .stat-card-body {
            padding: 8px 10px 6px !important;
        }
        .stat-card-info .stat-number {
            font-size: 1.35rem !important;
            margin-bottom: 1px !important;
        }
        .stat-card-info .stat-label {
            font-size: 0.62rem !important;
            letter-spacing: 0.2px !important;
        }
        .stat-card-icon {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
        }
        .stat-card-footer {
            padding: 3px 8px !important;
            font-size: 0.63rem !important;
        }

        /* 1. KARTU PROFIL SEKOLAH RESPONSIVE */
        .profil-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
        }
        .profil-card-banner {
            padding: 1rem 0.85rem !important;
        }
        .profil-card-logo-wrap {
            width: 58px !important;
            height: 58px !important;
            margin-bottom: 0.45rem !important;
            border-width: 2px !important;
        }
        .profil-card-logo-wrap img {
            width: 46px !important;
            height: 46px !important;
        }
        .profil-card-school-name {
            font-size: 0.84rem !important;
            margin-bottom: 2px !important;
            line-height: 1.25 !important;
        }
        .profil-card-npsn {
            font-size: 0.65rem !important;
            padding: 1.5px 8px !important;
        }
        .profil-card-body {
            padding: 0.85rem 1rem !important;
        }
        .profil-info-row {
            padding: 0.4rem 0 !important;
            gap: 8px !important;
        }
        .profil-info-icon {
            width: 26px !important;
            height: 26px !important;
            font-size: 0.68rem !important;
            border-radius: 6px !important;
        }
        .profil-info-label {
            font-size: 0.62rem !important;
            margin-bottom: 1px !important;
        }
        .profil-info-value {
            font-size: 0.76rem !important;
            line-height: 1.25 !important;
        }

        /* 2. KARTU TUGAS HARIAN GURU RESPONSIVE */
        .tugas-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
        }
        .tugas-card-header {
            padding: 0.75rem 1rem !important;
            gap: 8px !important;
        }
        .tugas-card-header-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.82rem !important;
            border-radius: 8px !important;
        }
        .tugas-card-header h6 {
            font-size: 0.84rem !important;
        }
        .tugas-card-body {
            padding: 0.85rem !important;
            gap: 8px !important;
        }
        .tugas-btn {
            padding: 0.65rem 0.4rem !important;
            font-size: 0.70rem !important;
            gap: 4px !important;
            border-radius: 10px !important;
        }
        .tugas-btn i {
            font-size: 1.12rem !important;
        }

        /* 3. KARTU INFO APLIKASI RESPONSIVE */
        .infoapp-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
        }
        .infoapp-card-header {
            padding: 0.75rem 1rem !important;
            gap: 8px !important;
        }
        .infoapp-card-header-icon {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.82rem !important;
            border-radius: 8px !important;
        }
        .infoapp-card-header h6 {
            font-size: 0.84rem !important;
        }
        .infoapp-card-body {
            padding: 0.85rem 1rem !important;
        }
        .infoapp-badge-row {
            padding: 0.4rem 0 !important;
            font-size: 0.74rem !important;
        }
        .infoapp-badge-value {
            font-size: 0.68rem !important;
            padding: 2px 8px !important;
        }
        .infoapp-dev-section {
            margin-top: 0.6rem !important;
            padding: 0.65rem 0.85rem !important;
            border-radius: 10px !important;
        }
        .infoapp-dev-title {
            font-size: 0.64rem !important;
            margin-bottom: 0.45rem !important;
        }
        .infoapp-dev-row {
            font-size: 0.72rem !important;
            margin-bottom: 0.35rem !important;
        }

        /* 4. TABEL REKAPITULASI & FILTER RESPONSIVE */
        .premium-dashboard-card {
            border-radius: 12px !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
        }
        .premium-dashboard-card .card-header {
            padding: 0.75rem 1rem !important;
        }
        .premium-dashboard-card .card-title {
            font-size: 0.82rem !important;
            font-weight: 700 !important;
        }
        .table-responsive .table thead th,
        .table-responsive .table tbody td,
        .table-bordered th,
        .table-bordered td {
            font-size: 0.74rem !important;
            padding: 6px 8px !important;
        }
        .filter-block {
            padding: 8px 12px !important;
            border-radius: 10px !important;
            margin-bottom: 12px !important;
        }
        .filter-block label {
            font-size: 0.72rem !important;
        }
        .filter-block .form-control-sm {
            font-size: 0.74rem !important;
            height: calc(1.5em + 0.45rem + 2px) !important;
            padding: 0.25rem 0.5rem !important;
        }
        .filter-row-container {
            gap: 8px !important;
        }
    }

    /* === PROFIL SEKOLAH CARD === */
    .profil-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        height: 100%;
    }
    .profil-card-banner {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        padding: 1.5rem;
        text-align: center;
        position: relative;
    }
    .profil-card-logo-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        border: 3px solid rgba(255,255,255,0.4);
        margin-bottom: 0.7rem;
        overflow: hidden;
    }
    .profil-card-logo-wrap img {
        width: 64px;
        height: 64px;
        object-fit: contain;
        border-radius: 50%;
    }
    .profil-card-school-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 2px;
        line-height: 1.3;
    }
    .profil-card-npsn {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.65);
        background: rgba(255,255,255,0.12);
        padding: 2px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .profil-card-body {
        padding: 1.2rem;
        position: relative;
    }
    .profil-info-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
    }
    .profil-info-row:last-child { border-bottom: none; }
    .profil-info-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: #fff;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .profil-info-label { font-size: 0.72rem; color: #adb5bd; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; line-height: 1; margin-bottom: 2px; }
    .profil-info-value { font-size: 0.88rem; color: #343a40; font-weight: 500; }

    /* === TUGAS HARIAN CARD === */
    .tugas-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .tugas-card-header {
        background: linear-gradient(135deg, #1a7a30, #28a745);
        padding: 1rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tugas-card-header-icon {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; color: #fff;
    }
    .tugas-card-header h6 {
        color: #fff; font-weight: 700; margin: 0; font-size: 0.95rem; letter-spacing: 0.3px;
    }
    .tugas-card-body {
        padding: 1.2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        flex: 1;
    }
    .tugas-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0.85rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        line-height: 1.3;
        width: 100%;
    }
    .tugas-btn:hover {
        transform: translateY(-2px);
        filter: brightness(1.08);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        text-decoration: none;
    }
    .tugas-btn i { font-size: 1.3rem; }
    .tugas-btn--jadwal  { background: linear-gradient(135deg,#667eea,#764ba2); color:#fff; }
    .tugas-btn--jurnal  { background: linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff; }
    .tugas-btn--absensi { background: linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }
    .tugas-btn--formatif{ background: linear-gradient(135deg,#64748b,#475569); color:#fff; }
    .tugas-btn--sumatif { background: linear-gradient(135deg,#22c55e,#16a34a); color:#fff; }
    .tugas-btn--catatan { background: linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
    /* full-width jika ganjil */
    .tugas-btn--full { grid-column: 1 / -1; flex-direction: row; justify-content: flex-start; gap: 12px; padding: 0.75rem 1.2rem; font-size: 0.82rem; }
    .tugas-btn--full i { font-size: 1rem; }

    /* === INFO APLIKASI CARD === */
    .infoapp-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .infoapp-card-header {
        background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
        padding: 1rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .infoapp-card-header-icon {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; color: #fff;
    }
    .infoapp-card-header h6 { color:#fff; font-weight:700; margin:0; font-size:0.95rem; }
    .infoapp-card-body { padding: 1.2rem; flex: 1; }
    .infoapp-badge-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.84rem;
    }
    .infoapp-badge-row:last-child { border-bottom: none; }
    .infoapp-badge-label { color: #6c757d; font-weight: 500; }
    .infoapp-badge-value {
        font-weight: 700;
        font-size: 0.78rem;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .infoapp-dev-section {
        margin-top: 0.8rem;
        background: linear-gradient(135deg,#f8f9ff,#eef2ff);
        border-radius: 12px;
        padding: 0.9rem 1rem;
        border: 1px solid #e0e7ff;
    }
    .infoapp-dev-title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6366f1;
        margin-bottom: 0.7rem;
    }
    .infoapp-dev-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.83rem;
        margin-bottom: 0.45rem;
        color: #374151;
    }
    .infoapp-dev-row:last-child { margin-bottom: 0; }
    .infoapp-dev-row i { width: 16px; color: #6366f1; font-size: 0.85rem; }
    .infoapp-dev-row a { color: #6366f1; font-weight: 600; text-decoration: none; }
    .infoapp-dev-row a:hover { text-decoration: underline; }

    /* === PREMIUM DASHBOARD CARD === */
    .premium-dashboard-card {
        background: #fff !important;
        border-radius: 16px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
        border: none !important;
        overflow: hidden;
        margin-bottom: 0 !important;
    }
    .premium-dashboard-card .card-header {
        background: #fff !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1.2rem 1.4rem !important;
    }
    .premium-dashboard-card .card-title {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
    }
    .premium-dashboard-card .card-title i {
        color: #4f46e5;
        margin-right: 6px;
    }
    
    /* === MODERN INLINE FILTER === */
    .modern-filter-siswa {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 12px 16px !important;
        margin-bottom: 1rem !important;
        box-shadow: none !important;
    }
    .modern-filter-siswa label {
        color: #475569 !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        margin-bottom: 6px !important;
    }
    .modern-filter-siswa select {
        height: 38px !important;
        background-color: #fff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        color: #1e293b !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
    }
    .modern-filter-siswa select:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    /* === PREMIUM TABLE === */
    .premium-table {
        width: 100%;
        border-collapse: collapse !important;
        border-spacing: 0;
        margin-bottom: 0 !important;
        border: none !important;
    }
    .premium-table thead,
    .premium-table thead tr {
        background: linear-gradient(135deg, #1e293b, #0f172a) !important;
    }
    .premium-table thead th {
        background: linear-gradient(135deg, #1e293b, #0f172a) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.78rem !important;
        letter-spacing: 0.5px !important;
        padding: 10px 12px !important;
        border: 1px solid rgba(255,255,255,0.08) !important;
        vertical-align: middle !important;
        text-align: center !important;
        white-space: nowrap !important;
        line-height: 1.4 !important;
    }
    .premium-table thead th * {
        color: #ffffff !important;
        background: transparent !important;
        white-space: nowrap !important;
    }
    .premium-table tbody td {
        padding: 10px 12px !important;
        vertical-align: middle !important;
        color: #334155 !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 0.84rem !important;
        background-color: inherit !important;
    }
    .premium-table tbody tr {
        transition: background 0.15s ease;
        background-color: #ffffff;
    }
    .premium-table tbody tr:nth-of-type(odd) {
        background-color: #f8fafc !important;
    }
    .premium-table tbody tr:hover {
        background-color: #f1f5f9 !important;
    }
    .premium-table tr.total-row {
        background-color: #e2e8f0 !important;
        font-weight: 700 !important;
    }
    .premium-table tr.total-row td {
        color: #0f172a !important;
        font-weight: 700 !important;
    }
    .premium-table .font-weight-bold {
        font-weight: 700 !important;
        color: #0f172a !important;
    }

    /* === PREMIUM NAV PILLS === */
    .premium-nav-pills {
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        display: inline-flex;
        border: none;
    }
    .premium-nav-pills .nav-item {
        margin: 0;
    }
    .premium-nav-pills .nav-link {
        border-radius: 8px !important;
        padding: 6px 16px !important;
        color: #64748b !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        transition: all 0.2s ease;
        border: none !important;
    }
    .premium-nav-pills .nav-link.active {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06) !important;
    }

    /* === PREMIUM BADGES === */
    .premium-badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 12px;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        line-height: 1;
    }
    .premium-badge-indigo { background-color: #e0e7ff !important; color: #4f46e5 !important; }
    .premium-badge-emerald { background-color: #d1fae5 !important; color: #059669 !important; }
    .premium-badge-amber { background-color: #fef3c7 !important; color: #d97706 !important; }
    .premium-badge-slate { background-color: #f1f5f9 !important; color: #475569 !important; }

    /* === MODERN FILTER BLOCK === */
    .modern-filter-block {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 12px 16px !important;
        margin-bottom: 1rem !important;
        box-shadow: none !important;
    }
    .modern-filter-block h4 {
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0 !important;
        margin-bottom: 10px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none !important;
    }
    .modern-filter-block label {
        color: #475569 !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        margin-bottom: 6px !important;
    }
    .modern-filter-block select,
    .modern-filter-block input {
        height: 38px !important;
        background-color: #fff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        color: #1e293b !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
    }
    .modern-filter-block select:focus,
    .modern-filter-block input:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    /* === PREMIUM BUTTONS === */
    .premium-btn-xs {
        padding: 4px 10px !important;
        font-size: 0.75rem !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    .premium-btn-outline-primary {
        background-color: transparent !important;
        border: 1px solid #4f46e5 !important;
        color: #4f46e5 !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .premium-btn-outline-primary:hover {
        background-color: #4f46e5 !important;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15) !important;
    }

    /* === PREMIUM DETAIL BUTTON (ABSENSI) === */
    .btn-detail-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 5px 12px !important;
        font-size: 0.73rem !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.25s ease;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
    }
    .btn-detail-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35) !important;
        color: #fff !important;
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%) !important;
    }
    .btn-detail-premium:active {
        transform: translateY(0);
    }

    /* === PREMIUM MODAL === */
    .premium-modal .modal-content {
        border: none !important;
        border-radius: 16px !important;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15) !important;
    }
    .premium-modal .modal-header-gradient {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e1b4b 100%);
        padding: 1.2rem 1.5rem;
        border-bottom: none;
    }
    .premium-modal .modal-header-gradient .modal-title {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .premium-modal .modal-header-gradient .modal-title i {
        color: #a5b4fc;
        font-size: 1.1rem;
    }
    .premium-modal .modal-header-gradient .close {
        color: rgba(255,255,255,0.7) !important;
        text-shadow: none;
        opacity: 1;
        font-size: 1.3rem;
    }
    .premium-modal .modal-header-gradient .close:hover {
        color: #fff !important;
    }
    .premium-modal .modal-body {
        padding: 1.5rem;
    }
    .premium-modal .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 0.8rem 1.5rem;
    }
    .premium-modal .modal-footer .btn {
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 6px 18px;
    }
    .premium-modal .periode-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f1ff;
        color: #4f46e5;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .premium-modal .periode-badge i {
        font-size: 0.75rem;
    }

    /* === STATUS BADGE FOR GRADUATED === */
    .status-lulus-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 6px;
        white-space: nowrap;
    }
    .status-lulus-badge i {
        font-size: 0.6rem;
    }

    /* === PREMIUM ATTENDANCE STATS ROW === */
    .attendance-stats-row {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .attendance-stat-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        flex: 1;
        min-width: 100px;
    }
    .attendance-stat-chip .stat-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .attendance-stat-chip .stat-label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 500;
    }
    .attendance-stat-chip .stat-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        margin-left: auto;
    }

    /* === MOBILE OPTIMIZATIONS FOR DASHBOARD TABLES, FILTERS & MODALS === */
    @media (max-width: 767.98px) {
        /* Tables general responsive */
        .table-responsive {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }
        .premium-table thead th {
            padding: 6px 8px !important;
            font-size: 0.68rem !important;
            letter-spacing: 0.2px !important;
            white-space: nowrap !important;
        }
        .premium-table tbody td {
            padding: 6px 8px !important;
            font-size: 0.75rem !important;
            white-space: nowrap !important;
        }
        .premium-table tfoot td,
        .premium-table tr.total-row td {
            padding: 6px 8px !important;
            font-size: 0.75rem !important;
        }

        /* Filter Blocks on Mobile */
        .modern-filter-block,
        .modern-filter-siswa {
            padding: 6px 10px !important;
            margin-bottom: 0.65rem !important;
            border-radius: 10px !important;
        }
        .modern-filter-block h4 {
            font-size: 0.75rem !important;
            margin-bottom: 6px !important;
        }
        .filter-row-container {
            gap: 6px !important;
        }
        .modern-filter-block .form-group,
        .modern-filter-siswa .form-group {
            margin-bottom: 4px !important;
        }
        .modern-filter-block label,
        .modern-filter-siswa label {
            font-size: 0.68rem !important;
            margin-bottom: 1px !important;
        }
        .modern-filter-block select,
        .modern-filter-block input {
            height: 30px !important;
            font-size: 0.72rem !important;
            padding: 2px 6px !important;
            border-radius: 6px !important;
        }
        .modern-filter-siswa select,
        .modern-filter-siswa input,
        .modern-filter-siswa option {
            height: 28px !important;
            font-size: 0.70rem !important;
            padding: 1px 5px !important;
            border-radius: 6px !important;
        }

        /* Badges & Buttons inside Tables on Mobile */
        .premium-badge {
            padding: 2px 6px !important;
            font-size: 0.65rem !important;
            border-radius: 6px !important;
        }
        .btn-detail-premium {
            padding: 3px 8px !important;
            font-size: 0.68rem !important;
            border-radius: 6px !important;
            gap: 3px !important;
        }
        .premium-nav-pills .nav-link {
            padding: 4px 10px !important;
            font-size: 0.72rem !important;
        }

        /* Dashboard Cards on Mobile */
        .premium-dashboard-card {
            border-radius: 12px !important;
            margin-bottom: 1rem !important;
        }
        .premium-dashboard-card .card-header {
            padding: 0.6rem 0.85rem !important;
        }
        .premium-dashboard-card .card-title {
            font-size: 0.82rem !important;
        }
        .premium-dashboard-card .card-body {
            padding: 0.75rem !important;
        }

        /* Modal Jadwal Mengajar on Mobile */
        .premium-modal .modal-header-gradient {
            padding: 0.85rem 1rem !important;
        }
        .premium-modal .modal-header-gradient .modal-title {
            font-size: 0.88rem !important;
        }
        .premium-modal .modal-body {
            padding: 0.75rem !important;
        }
        .premium-modal .periode-badge {
            padding: 4px 8px !important;
            font-size: 0.70rem !important;
            margin-bottom: 0.6rem !important;
        }

        /* Attendance Stats Chips on Mobile */
        .attendance-stats-row {
            gap: 6px !important;
            margin-bottom: 0.6rem !important;
        }
        .attendance-stat-chip {
            padding: 5px 8px !important;
            border-radius: 8px !important;
            min-width: 80px !important;
        }
        .attendance-stat-chip .stat-label {
            font-size: 0.65rem !important;
        }
        .attendance-stat-chip .stat-value {
            font-size: 0.78rem !important;
        }
    }
</style>

<section class="content pt-3">
    <div class="container-fluid">

        <!-- 1. TAMPILAN DESKTOP/LAPTOP/TABLET (CARD SLIDER BANNER & CARD WAKTU TERPISAH SEPERTI SEMULA) -->
        <div class="d-none d-md-block">
            <div class="banner-row">
                <div class="banner-col-main">
                    <!-- STATIC CONTAINER BANNER UTAMA (75%) -->
                    <div class="info-banner-container">
                        <div class="banner-slide-content h-100">
                            <div class="row align-items-center h-100" style="margin-left: 0; margin-right: 0;">
                                <!-- SISI KIRI: SAMBUTAN & IDENTITAS SESI (55%) -->
                                <div class="col-md-7 col-12 pl-0 pr-md-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge badge-warning px-2 py-1 font-weight-bold text-uppercase d-inline-block shadow-sm mr-2" style="font-size: 0.65rem; background: #fbbf24; color: #78350f; border-radius: 6px;">
                                            <i class="fas fa-school mr-1"></i> SIMAKS PORTAL
                                        </span>
                                        <span class="badge px-2 py-1 font-weight-bold text-white shadow-sm" style="font-size: 0.65rem; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 6px;">
                                            <i class="fas fa-user-shield mr-1 text-warning"></i> <?= htmlspecialchars(isset($_SESSION['roles'][0]) ? $_SESSION['roles'][0] : 'User') ?>
                                        </span>
                                    </div>
                                    <h3 class="font-weight-bold mb-1 text-truncate" style="font-size: 1.02rem; color: #ffffff; line-height: 1.25;" title="<?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>">
                                        Selamat Datang, <?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>! 👋
                                    </h3>
                                    <p class="mb-0 text-white-50" style="font-size: 0.80rem; line-height: 1.45;">
                                        Selamat beraktivitas di Tahun Ajaran <strong class="text-warning"><?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? 'Belum Aktif') ?></strong>.
                                    </p>
                                </div>

                                <!-- SISI KANAN: STATUS PERSONAL GURU ATAU MONITORING ADMIN (45%) -->
                                <div class="col-md-5 col-12 pr-0 pl-md-2 mt-2 mt-md-0">
                                    <div class="p-2" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16); backdrop-filter: blur(8px); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                        <?php 
                                        $current_roles = $_SESSION['roles'] ?? [];
                                        $is_admin_exec = in_array('Admin', $current_roles) || in_array('KepalaSekolah', $current_roles) || in_array('TU', $current_roles);
                                        $adm_mon = $tugas_status['admin_monitoring'] ?? [];
                                        ?>

                                        <?php if ($is_admin_exec && empty($tugas_status['is_guru'])): 
                                            $off_data = $adm_mon['offline'] ?? $adm_mon;
                                            $on_data  = $adm_mon['online'] ?? [];
                                        ?>
                                            <!-- TAMPILAN KHUSUS ADMIN / KEPSEK / TU (EXECUTIVE MONITORING SEKOLAH) -->
                                            <div class="d-flex align-items-center justify-content-between mb-1 pb-1" style="border-bottom: 1px solid rgba(255,255,255,0.12);">
                                                <!-- FILTER SWITCHER MODE (TATAP MUKA VS DARING) -->
                                                <div class="d-inline-flex align-items-center" style="background: rgba(15, 23, 42, 0.65); border: 1px solid rgba(255, 255, 255, 0.16); border-radius: 20px; padding: 2px; gap: 2px;">
                                                    <button type="button" class="btn-mode-switch active" id="btn-switch-offline" onclick="toggleExecutiveMonitoring('offline')" style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 16px; font-size: 0.62rem; font-weight: 700; border: none; background: #0284c7; color: #ffffff; cursor: pointer; line-height: 1.2; box-shadow: 0 1px 3px rgba(0,0,0,0.25);">
                                                        <i class="fas fa-school" style="font-size: 0.58rem;"></i> Tatap Muka
                                                    </button>
                                                    <button type="button" class="btn-mode-switch" id="btn-switch-online" onclick="toggleExecutiveMonitoring('online')" style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 16px; font-size: 0.62rem; font-weight: 700; border: none; background: transparent; color: rgba(255, 255, 255, 0.65); cursor: pointer; line-height: 1.2;">
                                                        <i class="fas fa-globe" style="font-size: 0.58rem;"></i> Daring
                                                    </button>
                                                </div>
                                                <span class="badge badge-light px-2 py-0 text-dark font-weight-bold" style="font-size: 0.62rem;">
                                                    <?= $hari_ini ?? 'Hari Ini' ?>
                                                </span>
                                            </div>
                                            
                                            <!-- VIEW 1: METRIK TATAP MUKA (OFFLINE) -->
                                            <div id="monitoring-view-offline" class="d-flex flex-column" style="gap: 4px;">
                                                <!-- Item 1: Keterlaksanaan KBM Offline -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-chalkboard-teacher text-info mr-1" style="width: 14px;"></i> KBM Tatap Muka:</span>
                                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                        <span class="font-weight-bold text-white"><?= $off_data['jurnal_terisi'] ?? 0 ?> / <?= $off_data['total_pertemuan'] ?? 0 ?> Sesi</span>
                                                        <a href="<?= BASE_URL ?>jurnal_kbm" class="badge badge-info px-1 py-0" style="font-size: 0.58rem;" title="Lihat Rekap Jurnal KBM">Detail <i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>

                                                <!-- Item 2: Kehadiran Fisik GTK/Guru di Sekolah -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-user-check text-warning mr-1" style="width: 14px;"></i> Presensi Fisik GTK:</span>
                                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                        <span class="font-weight-bold text-white"><?= $off_data['guru_hadir'] ?? 0 ?> / <?= $off_data['total_guru'] ?? 0 ?> Hadir</span>
                                                        <a href="<?= BASE_URL ?>absensi_guru" class="badge badge-warning text-dark px-1 py-0 font-weight-bold" style="font-size: 0.58rem;" title="Lihat Presensi Guru">Rekap <i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>

                                                <!-- Item 3: Status Presensi Rombel Piket -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-school text-success mr-1" style="width: 14px;"></i> Presensi Rombel:</span>
                                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                        <span class="font-weight-bold text-white"><?= $off_data['piket_kelas_terdata'] ?? 0 ?> / <?= $off_data['total_kelas'] ?? 0 ?> Kelas</span>
                                                        <a href="<?= BASE_URL ?>absensi_piket" class="badge badge-success px-1 py-0 font-weight-bold" style="font-size: 0.58rem;" title="Lihat Absensi Piket">Pantau <i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- VIEW 2: METRIK DARING / ONLINE LMS -->
                                            <div id="monitoring-view-online" class="d-flex flex-column" style="gap: 4px; display: none !important;">
                                                <!-- Item 1: Keterlaksanaan KBM Online LMS -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-laptop-code text-info mr-1" style="width: 14px;"></i> KBM Daring (LMS):</span>
                                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                        <span class="font-weight-bold text-white"><?= $on_data['jurnal_terisi'] ?? 0 ?> / <?= $on_data['total_pertemuan'] ?? 0 ?> Sesi</span>
                                                        <a href="<?= BASE_URL ?>absensi_guru" class="badge badge-info px-1 py-0" style="font-size: 0.58rem;" title="Pantau KBM Daring">Detail <i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>

                                                <!-- Item 2: Guru Mengajar Online Aktif -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-user-graduate text-success mr-1" style="width: 14px;"></i> Guru Aktif Online:</span>
                                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                        <span class="font-weight-bold text-white"><?= $on_data['guru_hadir'] ?? 0 ?> / <?= $on_data['total_guru'] ?? 0 ?> Aktif</span>
                                                        <a href="<?= BASE_URL ?>absensi_guru" class="badge badge-success text-white px-1 py-0 font-weight-bold" style="font-size: 0.58rem;" title="Lihat Guru Daring">Pantau <i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>

                                                <!-- Item 3: Keterdataan Absen Siswa Terbuka -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-globe text-warning mr-1" style="width: 14px;"></i> Rombel Terbuka:</span>
                                                    <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                        <span class="font-weight-bold text-white"><?= $on_data['piket_kelas_terdata'] ?? 0 ?> / <?= $on_data['total_pertemuan'] ?? 0 ?> Terabsen</span>
                                                        <a href="<?= BASE_URL ?>lms" class="badge badge-warning text-dark px-1 py-0 font-weight-bold" style="font-size: 0.58rem;" title="Buka LMS">LMS <i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- TAMPILAN GURU (AGENDA PRIBADI PER PERTEMUAN) -->
                                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1" style="border-bottom: 1px solid rgba(255,255,255,0.12);">
                                                <span class="font-weight-bold text-uppercase" style="font-size: 0.65rem; color: #fbbf24; letter-spacing: 0.4px;">
                                                    <i class="fas fa-calendar-day mr-1"></i> Agenda Anda Hari Ini
                                                </span>
                                                <span class="badge badge-light px-2 py-0 text-dark font-weight-bold" style="font-size: 0.62rem;">
                                                    <?= $hari_ini ?? 'Hari Ini' ?>
                                                </span>
                                            </div>
                                            
                                            <div class="d-flex flex-column" style="gap: 5px;">
                                                <!-- Item 1: Jam Mengajar Hari Ini -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-chalkboard text-info mr-1" style="width: 14px;"></i> Jadwal Mengajar:</span>
                                                    <?php if (!empty($tugas_status['is_guru']) && $tugas_status['total_jadwal_hari_ini'] > 0): ?>
                                                        <span class="font-weight-bold text-white"><?= $tugas_status['total_jadwal_hari_ini'] ?> Pertemuan KBM</span>
                                                    <?php else: ?>
                                                        <span class="font-weight-bold text-white-50">Tidak Ada Jam</span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Item 2: Status Jurnal & Presensi -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-clipboard-check text-warning mr-1" style="width: 14px;"></i> Jurnal & Absen:</span>
                                                    <?php if (!empty($tugas_status['is_guru']) && $tugas_status['total_jadwal_hari_ini'] > 0): ?>
                                                        <?php if ($tugas_status['jurnal_kbm_selesai'] >= $tugas_status['jurnal_kbm_total'] && $tugas_status['absen_mapel_selesai'] >= $tugas_status['absen_mapel_total']): ?>
                                                            <span class="badge badge-success px-2 py-0 font-weight-bold" style="font-size: 0.62rem;"><i class="fas fa-check"></i> Lengkap</span>
                                                        <?php else: ?>
                                                            <div class="d-inline-flex align-items-center" style="gap: 3px;">
                                                                <?php if ($tugas_status['absen_mapel_selesai'] < $tugas_status['absen_mapel_total']): ?>
                                                                    <a href="<?= BASE_URL ?>absensi_mapel" class="btn btn-warning btn-xs font-weight-bold px-1 py-0 text-dark shadow-sm" style="font-size: 0.60rem; border-radius: 4px;">Isi Absen</a>
                                                                <?php endif; ?>
                                                                <?php if ($tugas_status['jurnal_kbm_selesai'] < $tugas_status['jurnal_kbm_total']): ?>
                                                                    <a href="<?= BASE_URL ?>jurnal_kbm" class="btn btn-info btn-xs font-weight-bold px-1 py-0 text-white shadow-sm" style="font-size: 0.60rem; border-radius: 4px;">Isi Jurnal</a>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary px-2 py-0" style="font-size: 0.62rem;">Bebas Tugas</span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Item 3: Status Penugasan Piket -->
                                                <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem;">
                                                    <span class="text-white-50"><i class="fas fa-user-clock text-success mr-1" style="width: 14px;"></i> Tugas Piket:</span>
                                                    <?php if (!empty($tugas_status['is_piket_today'])): ?>
                                                        <div class="d-inline-flex align-items-center" style="gap: 3px;">
                                                            <span class="badge badge-danger px-1 py-0 font-weight-bold" style="font-size: 0.60rem;"><i class="fas fa-user-shield"></i> Piket</span>
                                                            <?php if (empty($tugas_status['piket']['absen_guru_selesai'])): ?>
                                                                <a href="<?= BASE_URL ?>absensi_guru" class="btn btn-warning btn-xs font-weight-bold px-1 py-0 text-dark" style="font-size: 0.58rem; border-radius: 4px;">Absen Guru</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="font-weight-bold text-white-50">Bukan Jadwal Piket</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="banner-col-side">
                    <!-- KARTU INFO WAKTU & KBM (25%) -->
                    <div id="infoCarousel" class="carousel slide info-card-carousel h-100" data-ride="carousel" data-interval="5000">
                        <ol class="carousel-indicators">
                            <li data-target="#infoCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#infoCarousel" data-slide-to="1"></li>
                            <li data-target="#infoCarousel" data-slide-to="2"></li>
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
                                    <div class="mt-1 text-muted" style="font-size: 0.68rem;">
                                        <i class="fas fa-clock text-primary"></i> Waktu Server
                                    </div>
                                </div>
                            </div>

                            <!-- SLIDE 2: STATUS KBM -->
                            <div class="carousel-item h-100">
                                <div class="carousel-content-wrapper text-center">
                                    <div id="kbm-status-carousel-content">
                                        <i class="fas fa-mug-hot fa-2x text-secondary mb-1" style="opacity: 0.6;"></i>
                                        <h6 class="text-secondary font-weight-bold mb-0" style="font-size: 0.85rem;">Tidak Ada KBM</h6>
                                        <small class="text-muted" style="font-size: 0.70rem;">Menunggu jadwal...</small>
                                    </div>
                                </div>
                            </div>

                            <!-- SLIDE 3: GURU PIKET HARI INI -->
                            <div class="carousel-item h-100">
                                <div class="carousel-content-wrapper text-center px-2">
                                    <div class="d-flex align-items-center justify-content-center mb-1 text-primary" style="font-size: 0.70rem; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase;">
                                        <i class="fas fa-user-clock mr-1"></i> Guru Piket
                                    </div>
                                    <?php if (!empty($guru_piket_hari_ini)): ?>
                                        <div class="d-flex flex-wrap justify-content-center align-items-center" style="max-height: 70px; overflow-y: auto; gap: 3px;">
                                            <?php foreach ($guru_piket_hari_ini as $gp): ?>
                                                <span class="d-inline-flex align-items-center px-2 py-0 text-truncate" 
                                                      style="background: #eef2ff; color: #4f46e5; border-radius: 6px; font-weight: 600; font-size: 0.70rem; max-width: 100%;"
                                                      title="<?= htmlspecialchars($gp['nama_guru']) ?>">
                                                    <i class="fas fa-user-tie mr-1 text-xs"></i> <?= htmlspecialchars($gp['nama_guru']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="text-muted mt-1 d-block" style="font-size: 0.65rem;">Hari <?= $hari_ini ?? 'Ini' ?></small>
                                    <?php else: ?>
                                        <i class="fas fa-user-clock fa-2x text-muted mb-1" style="opacity: 0.4;"></i>
                                        <h6 class="text-secondary font-weight-bold mb-0" style="font-size: 0.82rem;">Guru Piket Hari Ini</h6>
                                        <small class="text-muted" style="font-size: 0.68rem;">Belum ada piket (<?= $hari_ini ?? 'Hari Ini' ?>)</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TAMPILAN MOBILE (MERGED & UNIFIED HERO SLIDER TERPADU, SUPER RAPI & COMPACT) -->
        <div class="d-block d-md-none">
            <div id="mobileHeroCarousel" class="carousel slide mobile-hero-slider" data-ride="carousel" data-interval="7000">
                <ol class="carousel-indicators banner-indicators">
                    <li data-target="#mobileHeroCarousel" data-slide-to="0" class="active" title="Selamat Datang"></li>
                    <li data-target="#mobileHeroCarousel" data-slide-to="1" title="Tugas Harian"></li>
                    <li data-target="#mobileHeroCarousel" data-slide-to="2" title="KBM & Piket"></li>
                </ol>

                <div class="carousel-inner">
                    <!-- MOBILE SLIDE 1: SAPAAN & JAM LIVE -->
                    <div class="carousel-item active">
                        <div class="mobile-hero-slide">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge badge-warning px-1.5 py-0.5 font-weight-bold text-uppercase" style="font-size: 0.58rem; background: #fbbf24; color: #78350f; border-radius: 4px;">
                                    <i class="fas fa-school mr-1"></i> SIMAKS PORTAL
                                </span>
                                <span class="badge px-1.5 py-0.5 font-weight-bold text-white" style="font-size: 0.58rem; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 4px;">
                                    <i class="fas fa-user-tie mr-1 text-warning"></i> <?= htmlspecialchars(isset($_SESSION['roles'][0]) ? $_SESSION['roles'][0] : 'User') ?>
                                </span>
                            </div>
                            <div class="banner-user-name-mobile text-truncate" title="<?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>">
                                Selamat Datang, <?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>! 👋
                            </div>
                            <div class="text-white-50 text-truncate mb-1" style="font-size: 0.64rem;">
                                TA Aktif: <strong class="text-warning"><?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? 'Belum Aktif') ?></strong>
                            </div>
                            <!-- Live Digital Clock Pill on Mobile -->
                            <div class="d-inline-flex align-items-center px-2 py-0.5" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); border-radius: 6px; font-size: 0.65rem; color: #ffffff; width: fit-content; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <i class="fas fa-clock text-warning mr-1"></i>
                                <span id="realtime-clock-mobile" class="font-weight-bold mr-1" style="letter-spacing: 0.5px;">--:--:--</span>
                                <span class="text-white-50 ml-1">&bull; <?= $hari_indo[date('l')] ?>, <?= date('d') ?> <?= $bulan_indo[(int)date('m')] ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- MOBILE SLIDE 2: MONITORING TUGAS HARIAN GURU / SEKOLAH -->
                    <div class="carousel-item">
                        <div class="mobile-hero-slide">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge badge-warning px-1.5 py-0 font-weight-bold text-dark" style="font-size: 0.52rem; border-radius: 4px;">
                                    <i class="fas fa-tasks mr-1"></i> <?= ($is_admin_exec && empty($tugas_status['is_guru'])) ? 'Monitoring Sekolah' : 'Agenda Harian Guru' ?>
                                </span>
                                <small class="text-white-50 font-weight-bold" style="font-size: 0.56rem;"><i class="far fa-calendar-alt mr-1"></i> <?= $hari_ini ?? 'Hari Ini' ?></small>
                            </div>

                            <div id="mobile-tugas-content-static">
                            <?php if ($is_admin_exec && empty($tugas_status['is_guru'])): ?>
                                <!-- MOBILE EXECUTIVE MONITORING (ADMIN/KEPSEK/TU) -->
                                <div class="row" style="margin-left: -3px; margin-right: -3px;">
                                    <div class="col-12 mb-1" style="padding-left: 3px; padding-right: 3px;">
                                        <div class="task-item-box py-0.5 px-1.5 d-flex align-items-center justify-content-between">
                                            <div class="text-truncate">
                                                <span class="font-weight-bold text-white d-block" style="font-size: 0.60rem;"><i class="fas fa-chalkboard-teacher text-info mr-1"></i> KBM Terlaksana</span>
                                                <small class="text-white-50" style="font-size: 0.52rem;"><?= $adm_mon['jurnal_terisi'] ?? 0 ?> / <?= $adm_mon['total_pertemuan'] ?? 0 ?> Pertemuan</small>
                                            </div>
                                            <a href="<?= BASE_URL ?>jurnal_kbm" class="btn btn-info btn-xs font-weight-bold px-1.5 py-0 text-white" style="border-radius: 4px; font-size: 0.54rem;">Detail</a>
                                        </div>
                                    </div>
                                    <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                                        <div class="task-item-box py-0.5 px-1.5 d-flex align-items-center justify-content-between">
                                            <div class="text-truncate">
                                                <span class="font-weight-bold text-white d-block" style="font-size: 0.58rem;"><i class="fas fa-user-check text-warning mr-1"></i> Guru Hadir</span>
                                                <small class="text-white-50" style="font-size: 0.50rem;"><?= $adm_mon['guru_hadir'] ?? 0 ?> / <?= $adm_mon['total_guru'] ?? 0 ?></small>
                                            </div>
                                            <a href="<?= BASE_URL ?>absensi_guru" class="btn btn-warning btn-xs font-weight-bold px-1 py-0 text-dark" style="border-radius: 4px; font-size: 0.52rem;">Rekap</a>
                                        </div>
                                    </div>
                                    <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                                        <div class="task-item-box py-0.5 px-1.5 d-flex align-items-center justify-content-between">
                                            <div class="text-truncate">
                                                <span class="font-weight-bold text-white d-block" style="font-size: 0.58rem;"><i class="fas fa-school text-success mr-1"></i> Piket Kelas</span>
                                                <small class="text-white-50" style="font-size: 0.50rem;"><?= $adm_mon['piket_kelas_terdata'] ?? 0 ?> / <?= $adm_mon['total_kelas'] ?? 0 ?></small>
                                            </div>
                                            <a href="<?= BASE_URL ?>absensi_piket" class="btn btn-success btn-xs font-weight-bold px-1 py-0 text-white" style="border-radius: 4px; font-size: 0.52rem;">Pantau</a>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif (!empty($tugas_status['is_guru']) && $tugas_status['total_jadwal_hari_ini'] > 0): ?>
                                <div class="row" style="margin-left: -3px; margin-right: -3px;">
                                    <!-- Absen Siswa -->
                                    <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                                        <div class="task-item-box py-1 px-1.5">
                                            <div class="text-truncate mr-1">
                                                <span class="font-weight-bold text-white d-block text-truncate" style="font-size: 0.60rem;">
                                                    <i class="fas fa-user-check text-warning mr-1"></i> Presensi
                                                </span>
                                                <small class="text-white-50 d-block" style="font-size: 0.52rem;"><?= $tugas_status['total_jadwal_hari_ini'] ?> Jam Mengajar</small>
                                            </div>
                                            <div>
                                                <?php if ($tugas_status['absen_mapel_selesai'] >= $tugas_status['absen_mapel_total']): ?>
                                                    <span class="badge badge-success px-1.5 py-0.5" style="font-size: 0.54rem; border-radius: 4px;"><i class="fas fa-check"></i> Selesai</span>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>absensi_mapel" class="btn btn-warning btn-xs font-weight-bold px-1.5 py-0 text-dark shadow-sm" style="border-radius: 4px; font-size: 0.56rem;">Isi</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Jurnal KBM -->
                                    <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                                        <div class="task-item-box py-1 px-1.5">
                                            <div class="text-truncate mr-1">
                                                <span class="font-weight-bold text-white d-block text-truncate" style="font-size: 0.60rem;">
                                                    <i class="fas fa-feather-alt text-info mr-1"></i> Jurnal KBM
                                                </span>
                                                <small class="text-white-50 d-block" style="font-size: 0.52rem;"><?= $tugas_status['total_jadwal_hari_ini'] ?> Jam Mengajar</small>
                                            </div>
                                            <div>
                                                <?php if ($tugas_status['jurnal_kbm_selesai'] >= $tugas_status['jurnal_kbm_total']): ?>
                                                    <span class="badge badge-success px-1.5 py-0.5" style="font-size: 0.54rem; border-radius: 4px;"><i class="fas fa-check"></i> Selesai</span>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>jurnal_kbm" class="btn btn-info btn-xs font-weight-bold px-1.5 py-0 text-white shadow-sm" style="border-radius: 4px; font-size: 0.56rem;">Isi</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-1">
                                    <div class="d-flex align-items-center justify-content-center text-success mb-0" style="font-size: 0.70rem; font-weight: 700;">
                                        <i class="fas fa-check-circle mr-1"></i> Bebas Tugas KBM Hari Ini
                                    </div>
                                    <p class="text-white-50 mb-0" style="font-size: 0.58rem; line-height: 1.25;">
                                        Tidak ada jadwal mengajar tatap muka untuk Anda hari <?= $hari_ini ?? 'ini' ?>.
                                    </p>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- MOBILE SLIDE 3: STATUS KBM LIVE & PIKET -->
                    <div class="carousel-item">
                        <div class="mobile-hero-slide">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge badge-info px-1.5 py-0 font-weight-bold" style="font-size: 0.52rem; border-radius: 4px;">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> Status KBM Berlangsung
                                </span>
                                <small class="text-white-50 font-weight-bold" style="font-size: 0.56rem;"><i class="far fa-clock mr-1"></i> <?= $hari_ini ?? 'Ini' ?></small>
                            </div>
                            <div id="kbm-live-mobile-container" class="mb-0.5">
                                <h6 id="kbm-live-mobile-title" class="font-weight-bold text-white mb-0 text-truncate" style="font-size: 0.74rem;">
                                    Menunggu Sesi KBM...
                                </h6>
                                <small id="kbm-live-mobile-desc" class="text-white-50 d-block text-truncate" style="font-size: 0.58rem;">
                                    Jadwal mengajar dan kegiatan KBM hari ini.
                                </small>
                            </div>
                            <!-- Guru Piket Chips on Mobile -->
                            <div class="d-flex align-items-center" style="gap: 3px; max-height: 24px; overflow-x: auto;">
                                <span class="text-warning font-weight-bold text-uppercase mr-1" style="font-size: 0.54rem; white-space: nowrap;">
                                    <i class="fas fa-user-clock mr-1"></i> Piket:
                                </span>
                                <?php if (!empty($guru_piket_hari_ini)): ?>
                                    <?php foreach ($guru_piket_hari_ini as $gp): ?>
                                        <span class="badge px-1.5 py-0.5 text-truncate" style="font-size: 0.56rem; white-space: nowrap; background: rgba(255,255,255,0.15); color:#ffffff; border: 1px solid rgba(255,255,255,0.2); border-radius: 4px;">
                                            <?= htmlspecialchars($gp['nama_guru']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <small class="text-white-50" style="font-size: 0.54rem;">Belum ada piket terdaftar</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <!-- KARTU: TOTAL SISWA AKTIF -->
            <div class="col-lg-3 col-md-6 col-6">
                <a href="<?= BASE_URL ?>siswa" class="stat-card stat-card--siswa d-flex flex-column">
                    <div class="stat-card-body">
                        <div class="stat-card-info">
                            <div class="stat-number" id="summary-total-siswa"><?= number_format($info_card['total_siswa'] ?? 0) ?></div>
                            <p class="stat-label">Total Siswa Aktif</p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <!-- KARTU: TOTAL GURU AKTIF -->
            <div class="col-lg-3 col-md-6 col-6">
                <a href="<?= BASE_URL ?>guru" class="stat-card stat-card--guru d-flex flex-column">
                    <div class="stat-card-body">
                        <div class="stat-card-info">
                            <div class="stat-number" id="summary-total-guru"><?= number_format($info_card['total_guru'] ?? 0) ?></div>
                            <p class="stat-label">Total Guru Aktif</p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <!-- KARTU: TOTAL ROMBEL -->
            <div class="col-lg-3 col-md-6 col-6">
                <a href="<?= BASE_URL ?>kelas" class="stat-card stat-card--rombel d-flex flex-column">
                    <div class="stat-card-body">
                        <div class="stat-card-info">
                            <div class="stat-number" id="summary-total-kelas"><?= number_format($info_card['total_kelas'] ?? 0) ?></div>
                            <p class="stat-label">Total Rombel Kelas</p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <!-- KARTU: TOTAL MAPEL -->
            <div class="col-lg-3 col-md-6 col-6">
                <a href="<?= BASE_URL ?>mapel" class="stat-card stat-card--mapel d-flex flex-column">
                    <div class="stat-card-body">
                        <div class="stat-card-info">
                            <div class="stat-number" id="summary-total-mapel"><?= number_format($info_card['total_mapel'] ?? 0) ?></div>
                            <p class="stat-label">Total Mata Pelajaran</p>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mb-4">

            <!-- ===== KARTU PROFIL SEKOLAH ===== -->
            <div class="col-md-4 mb-3">
                <div class="profil-card">
                    <!-- Banner Gradient -->
                    <div class="profil-card-banner">
                        <div class="profil-card-logo-wrap">
                            <img src="<?= file_exists($logo_path) ? htmlspecialchars($logo_path) : 'assets/img/default_logo.png' ?>" alt="Logo Sekolah">
                        </div>
                        <div class="profil-card-school-name"><?= htmlspecialchars($profil_sekolah['nama_sekolah'] ?? 'NAMA SEKOLAH BELUM DIATUR') ?></div>
                        <div class="mt-1"><span class="profil-card-npsn">NPSN: <?= htmlspecialchars($profil_sekolah['npsn'] ?? '-') ?></span></div>
                    </div>
                    <!-- Info Rows -->
                    <div class="profil-card-body">
                        <div class="profil-info-row">
                            <div class="profil-info-icon" style="background:linear-gradient(135deg,#007bff,#0056d2);"><i class="fas fa-map-marker-alt"></i></div>
                            <div><div class="profil-info-label">Alamat</div><div class="profil-info-value"><?= htmlspecialchars($profil_sekolah['alamat'] ?? '-') ?></div></div>
                        </div>
                        <div class="profil-info-row">
                            <div class="profil-info-icon" style="background:linear-gradient(135deg,#28a745,#1a7a30);"><i class="fas fa-phone-alt"></i></div>
                            <div><div class="profil-info-label">Telepon</div><div class="profil-info-value"><?= htmlspecialchars($profil_sekolah['telp'] ?? '-') ?></div></div>
                        </div>
                        <div class="profil-info-row">
                            <div class="profil-info-icon" style="background:linear-gradient(135deg,#fd7e14,#e05a00);"><i class="fas fa-envelope"></i></div>
                            <div><div class="profil-info-label">Email</div><div class="profil-info-value"><?= htmlspecialchars($profil_sekolah['email'] ?? '-') ?></div></div>
                        </div>
                        <div class="profil-info-row">
                            <div class="profil-info-icon" style="background:linear-gradient(135deg,#dc3545,#a71c2c);"><i class="fas fa-user-tie"></i></div>
                            <div><div class="profil-info-label">Kepala Sekolah</div><div class="profil-info-value"><?= htmlspecialchars($profil_sekolah['nama_kepala_sekolah'] ?? '-') ?></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== KARTU TUGAS HARIAN GURU ===== -->
            <div class="col-md-4 mb-3">
                <div class="tugas-card">
                    <div class="tugas-card-header">
                        <div class="tugas-card-header-icon"><i class="fas fa-clipboard-check"></i></div>
                        <h6>Tugas Harian Guru</h6>
                    </div>
                    <div class="tugas-card-body">
                        <button type="button" class="tugas-btn tugas-btn--jadwal" data-toggle="modal" data-target="#modal-jadwal-mengajar">
                            <i class="fas fa-calendar-alt"></i> Jadwal Mengajar
                        </button>
                        <a href="<?= BASE_URL ?>jurnal_kbm" class="tugas-btn tugas-btn--jurnal">
                            <i class="fas fa-feather-alt"></i> Jurnal KBM
                        </a>
                        <a href="<?= BASE_URL ?>absensi_mapel" class="tugas-btn tugas-btn--absensi">
                            <i class="fas fa-user-check"></i> Absensi Mapel
                        </a>
                        <a href="<?= BASE_URL ?>input_nilai" class="tugas-btn tugas-btn--formatif">
                            <i class="fas fa-pencil-alt"></i> Nilai Formatif
                        </a>
                        <a href="<?= BASE_URL ?>penilaian_sumatif" class="tugas-btn tugas-btn--sumatif">
                            <i class="fas fa-pen-square"></i> Nilai Sumatif
                        </a>
                        <a href="<?= BASE_URL ?>catatan_kelas" class="tugas-btn tugas-btn--catatan">
                            <i class="fas fa-exclamation-triangle"></i> Catatan Kelas
                        </a>
                    </div>
                    <?php $user_roles = $_SESSION['roles'] ?? [];
                    $is_admin_or_guru = in_array('Admin', $user_roles) || in_array('Guru', $user_roles);
                    if (!$is_admin_or_guru): ?>
                        <div class="px-3 pb-2"><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Akses terbatas, login sebagai Guru/Admin.</small></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== KARTU INFO APLIKASI ===== -->
            <div class="col-md-4 mb-3">
                <div class="infoapp-card">
                    <div class="infoapp-card-header">
                        <div class="infoapp-card-header-icon"><i class="fas fa-microchip"></i></div>
                        <h6>Info Aplikasi SIMAKS</h6>
                    </div>
                    <div class="infoapp-card-body">
                        <!-- Badge info teknis -->
                        <div class="infoapp-badge-row">
                            <span class="infoapp-badge-label"><i class="fas fa-code-branch mr-1 text-indigo"></i> Versi Aplikasi</span>
                            <span class="infoapp-badge-value" style="background:#e0e7ff;color:#4338ca;"><?= htmlspecialchars($app_info['Versi Aplikasi'] ?? '-') ?></span>
                        </div>
                        <div class="infoapp-badge-row">
                            <span class="infoapp-badge-label"><i class="fas fa-database mr-1"></i> Database</span>
                            <span class="infoapp-badge-value" style="background:#d1fae5;color:#065f46;"><?= htmlspecialchars($app_info['Database'] ?? '-') ?></span>
                        </div>
                        <div class="infoapp-badge-row">
                            <span class="infoapp-badge-label"><i class="fas fa-server mr-1"></i> Versi PHP</span>
                            <span class="infoapp-badge-value" style="background:#fef3c7;color:#92400e;"><?= htmlspecialchars($app_info['Versi PHP'] ?? '-') ?></span>
                        </div>
                        <!-- Section developer -->
                        <div class="infoapp-dev-section">
                            <div class="infoapp-dev-title"><i class="fas fa-code mr-1"></i> Informasi Pengembang</div>
                            <div class="infoapp-dev-row">
                                <i class="fas fa-building"></i>
                                <span><?= htmlspecialchars($app_info['Pengembang'] ?? '-') ?></span>
                            </div>
                            <div class="infoapp-dev-row">
                                <i class="fab fa-whatsapp"></i>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $app_info['WhatsApp']) ?>" target="_blank"><?= htmlspecialchars($app_info['WhatsApp'] ?? '-') ?></a>
                            </div>
                            <div class="infoapp-dev-row">
                                <i class="fas fa-globe"></i>
                                <a href="<?= htmlspecialchars($app_info['Web Pengembang'] ?? '#') ?>" target="_blank"><?= htmlspecialchars($app_info['Web Pengembang'] ?? '-') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">

            <div class="col-md-6 mb-3">
                <div class="card premium-dashboard-card h-100">
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

                        <div class="filter-block modern-filter-siswa">
                            <div class="filter-row-container">
                                <div class="form-group">
                                    <label for="filter-ta">Tahun Ajaran</label>
                                    <select name="filter_ta" id="filter-ta" class="form-control form-control-sm"
                                        style="width: 100%;">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="filter-tingkat">Tingkat</label>
                                    <select id="filter-tingkat" class="form-control form-control-sm"
                                        style="width: 100%;">
                                        <option value="all">Semua</option>
                                        <option value="X">X</option>
                                        <option value="XI">XI</option>
                                        <option value="XII">XII</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table premium-table" id="rekap-siswa-table" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle" style="width: 7%; white-space: nowrap;">No</th>
                                        <th rowspan="2" class="text-center align-middle" style="width: 18%; white-space: nowrap;">Nama Kelas</th>
                                        <th rowspan="2" class="d-none d-md-table-cell text-left align-middle" style="width: 31%; white-space: nowrap;">Wali Kelas</th>
                                        <th colspan="2" class="text-center align-middle" style="width: 20%;" title="Jenis Kelamin">JK</th>
                                        <th rowspan="2" class="text-center align-middle" style="width: 10%; white-space: nowrap;" title="Jumlah Total">JML</th>
                                        <th colspan="2" class="text-center align-middle" style="width: 14%;" title="Mutasi Siswa">MUTASI</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 10%;" title="Laki-laki">L</th>
                                        <th class="text-center align-middle" style="width: 10%;" title="Perempuan">P</th>
                                        <th class="text-center align-middle" style="width: 7%;" title="Mutasi Masuk">M</th>
                                        <th class="text-center align-middle" style="width: 7%;" title="Mutasi Keluar">K</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i>
                                            Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card premium-dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafik Rekap Siswa Per Kelas</h3>
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
                        <div style="height: 400px; padding-top: 10px;">
                            <canvas id="rekapSiswaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    <!-- ROW BARU: DATA ALUMNI & TRACER STUDY -->
    <!-- ROW BARU: DATA ALUMNI & TRACER STUDY -->
    <div class="row mb-4">

        <!-- TABEL REKAP ALUMNI & TRACER -->
        <div class="col-md-6 mb-3">
            <div class="card premium-dashboard-card h-100">
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
                        <table class="table premium-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align: middle; width: 45px;" class="text-center">No</th>
                                    <th rowspan="2" style="vertical-align: middle;" class="text-center">Tahun Pelajaran</th>
                                    <th colspan="2" class="text-center">
                                        <span class="d-none d-md-inline">Jenis Kelamin</span>
                                        <span class="d-inline d-md-none" title="Jenis Kelamin">JK</span>
                                    </th>
                                    <th rowspan="2" style="vertical-align: middle; width: 70px;" class="text-center">
                                        <span class="d-none d-md-inline">Total</span>
                                        <span class="d-inline d-md-none" title="Jumlah Total">Jml</span>
                                    </th>
                                    <th colspan="4" class="text-center">Status Pasca Lulus</th>
                                </tr>
                                <tr>
                                    <th style="width: 50px;" class="text-center">
                                        <span class="d-none d-md-inline">Laki-laki</span>
                                        <span class="d-inline d-md-none" title="Laki-laki">L</span>
                                    </th>
                                    <th style="width: 50px;" class="text-center">
                                        <span class="d-none d-md-inline">Perempuan</span>
                                        <span class="d-inline d-md-none" title="Perempuan">P</span>
                                    </th>
                                    <th class="text-center">Kuliah</th>
                                    <th class="text-center">Kerja</th>
                                    <th class="text-center">Usaha</th>
                                    <th class="text-center">Lainnya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tracer_stats)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Belum ada data lulusan untuk
                                            ditampilkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $no_tr = 1;
                                    foreach ($tracer_stats as $ts):
                                        // Format Tahun Pelajaran:
                                        // Asumsi: Lulusan 2025 adalah dari Tahun Pelajaran 2024/2025
                                        $thn_lulus = (int) $ts['tahun_lulus'];
                                        $thn_awal = $thn_lulus - 1;
                                        $thn_display = "$thn_awal/$thn_lulus";
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no_tr++ ?></td>
                                            <td class="text-center"><strong><?= $thn_display ?></strong></td>
                                            <td class="text-center"><?= $ts['laki_laki'] ?></td>
                                            <td class="text-center"><?= $ts['perempuan'] ?></td>
                                            <td class="font-weight-bold text-center"><?= $ts['total_lulus'] ?></td>
                                            <td class="text-center">
                                                <span class="premium-badge premium-badge-indigo"><?= $ts['ptn_pts'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="premium-badge premium-badge-emerald"><?= $ts['bekerja'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="premium-badge premium-badge-amber"><?= $ts['wirausaha'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="premium-badge premium-badge-slate"><?= $ts['lain_lain'] ?></span>
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
        <div class="col-md-6 mb-3">
            <div class="card premium-dashboard-card h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafik Lulusan</h3>
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
                    <ul class="nav nav-pills premium-nav-pills mb-3 justify-content-end">
                        <li class="nav-item"><a class="nav-link active" href="#chart-lulusan-tab"
                                data-toggle="tab">Lulusan</a></li>
                        <li class="nav-item"><a class="nav-link" href="#chart-tracer-tab" data-toggle="tab">Tracer</a>
                        </li>
                    </ul>
                    <div class="tab-content m-0">
                        <!-- CHART 1: LULUSAN PER TAHUN -->
                        <div class="chart tab-pane active" id="chart-lulusan-tab"
                             style="position: relative; height: 300px;">
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

        <div class="col-md-6 mb-3">
            <div class="card premium-dashboard-card h-100">
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

                    <div class="modern-filter-block">
                        <h4>Filter Absensi Guru</h4>
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
                                <label for="filter-mode-guru">Jenis Kehadiran</label>
                                <select id="filter-mode-guru" class="form-control form-control-sm">
                                    <option value="tatap_muka" selected>🏫 Tatap Muka (Fisik)</option>
                                    <option value="online">🌐 Daring (Online LMS)</option>
                                </select>
                            </div>
                            <div class="form-group d-none d-md-block">
                                <label for="filter-guru-absen">Nama Guru</label>
                                <select id="filter-guru-absen" class="form-control form-control-sm"
                                    style="width: 100%;">
                                    <option value="all">-- Semua Guru --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table premium-table" id="rekap-absensi-guru-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">No</th>
                                    <th>
                                        <span class="d-none d-md-inline">Nama Guru dan Tendik</span>
                                        <span class="d-inline d-md-none">Nama Guru</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Hadir</span>
                                        <span class="d-inline d-md-none" title="Hadir">H</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Sakit</span>
                                        <span class="d-inline d-md-none" title="Sakit">S</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Izin</span>
                                        <span class="d-inline d-md-none" title="Izin">I</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Alpa</span>
                                        <span class="d-inline d-md-none" title="Alpa">A</span>
                                    </th>
                                    <th class="text-center" style="width: 90px; min-width: 85px;">
                                        <span class="d-none d-md-inline">% Hadir</span>
                                        <span class="d-inline d-md-none" title="Persentase Hadir">% H</span>
                                    </th>
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

        <div class="col-md-6 mb-3">
            <div class="card premium-dashboard-card h-100">
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

                    <div class="modern-filter-block">
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
                                <select id="filter-kelas-absen" class="form-control form-control-sm"
                                    style="width: 100%;">
                                    <option value="all">-- Semua Kelas --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table premium-table" id="rekap-absensi-siswa-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">No</th>
                                    <th>
                                        <span class="d-none d-md-inline">Nama Kelas</span>
                                        <span class="d-inline d-md-none">Kelas</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Hadir</span>
                                        <span class="d-inline d-md-none" title="Hadir">H</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Sakit</span>
                                        <span class="d-inline d-md-none" title="Sakit">S</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Izin</span>
                                        <span class="d-inline d-md-none" title="Izin">I</span>
                                    </th>
                                    <th class="text-center" style="width: 60px;">
                                        <span class="d-none d-md-inline">Alpa</span>
                                        <span class="d-inline d-md-none" title="Alpa">A</span>
                                    </th>
                                    <th class="text-center" style="width: 90px; min-width: 85px;">
                                        <span class="d-none d-md-inline">% Hadir</span>
                                        <span class="d-inline d-md-none" title="Persentase Hadir">% H</span>
                                    </th>
                                    <th class="text-center" style="width: 85px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">

        <div class="col-md-6 mb-3">
            <div class="card premium-dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie"></i> Grafik Absensi Guru</h3>
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

        <div class="col-md-6 mb-3">
            <div class="card premium-dashboard-card">
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

<div class="modal fade premium-modal" id="modal-detail-absensi-siswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-user-check"></i> Detail Absensi Siswa — <span id="detail-kelas-nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="periode-badge">
                    <i class="fas fa-calendar-alt"></i>
                    <span id="detail-periode-text"></span>
                </div>
                <div class="attendance-stats-row" id="detail-stats-row">
                    <!-- Populated by JS -->
                </div>
                <div class="table-responsive">
                    <table class="table premium-table" id="table-detail-siswa">
                        <thead>
                            <tr>
                                <th style="width:45px;">No</th>
                                <th style="width:90px;">NIS</th>
                                <th>Nama Siswa</th>
                                <th style="width:55px;">H</th>
                                <th style="width:55px;">S</th>
                                <th style="width:55px;">I</th>
                                <th style="width:55px;">A</th>
                                <th style="width:80px;">% Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
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
        let globalNonKbm = [];
        let globalLastKbmTime = '15:30';
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
                        globalNonKbm = response.non_kbm || [];
                        globalLastKbmTime = response.last_kbm_time || '15:30';
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
                    if (item.kelas === current.kelas &&
                        item.mapel === current.mapel &&
                        item.guru === current.guru &&
                        item.jam_mulai.substring(0, 5) === current.jam_selesai.substring(0, 5)) {
                        current.jam_selesai = item.jam_selesai;
                    } else {
                        mergedBlocks.push(current);
                        current = { ...item };
                    }
                }
                mergedBlocks.push(current);
            }

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

            let sortedSlots = Object.keys(groupedSlots).sort((a, b) => {
                return groupedSlots[a].start.localeCompare(groupedSlots[b].start);
            });

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

            const timeFull = `${hours}:${minutes}:${seconds}`;
            $('#realtime-clock').text(timeFull);
            $('#realtime-clock-mobile').text(timeFull);

            let activeSubjects = [];
            let endingSubjects = [];
            let activeNonKbm = [];
            let activeIdsArr = [];

            // 1. Cek KBM Aktif
            if (globalSchedule.length > 0) {
                globalSchedule.forEach(function (item) {
                    let start = item.jam_mulai ? item.jam_mulai.substring(0, 5) : '00:00';
                    let end = item.jam_selesai ? item.jam_selesai.substring(0, 5) : '00:00';

                    if (currentTimeStr >= start && currentTimeStr < end) {
                        activeSubjects.push(item);
                        activeIdsArr.push(`KBM:${item.mapel}-${item.kelas}`);
                    }

                    if (currentTimeStr === end && seconds === '00') {
                        endingSubjects.push(item);
                    }
                });
            }

            // 2. Cek Aktivitas Non-KBM Aktif (Istirahat, Sholat, Upacara, Tadarus, dll)
            if (globalNonKbm && globalNonKbm.length > 0) {
                globalNonKbm.forEach(function (item) {
                    let start = item.jam_mulai ? item.jam_mulai.substring(0, 5) : '00:00';
                    let end = item.jam_selesai ? item.jam_selesai.substring(0, 5) : '00:00';

                    if (currentTimeStr >= start && currentTimeStr < end) {
                        activeNonKbm.push(item);
                        activeIdsArr.push(`NONKBM:${item.nama_kegiatan}`);
                    }
                });
            }

            // 3. Cek apakah sudah melewati jam KBM terakhir hari ini
            let isAfterLastKbm = (currentTimeStr >= globalLastKbmTime);
            activeIdsArr.push(`AFTER_KBM:${isAfterLastKbm}`);

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
                renderCarouselStatus(activeSubjects, activeNonKbm, isAfterLastKbm);
            }
        }

        var piketHariIniData = <?= json_encode($guru_piket_hari_ini ?? []) ?>;
        var piketHariIniNama = <?= json_encode($hari_ini ?? 'Ini') ?>;
        var loggedInUserName = <?= json_encode($_SESSION['nama_pengguna'] ?? '') ?>;
        var tanggalStrIndo = "<?= $hari_indo[date('l')] ?>, <?= date('d') ?> <?= $bulan_indo[(int) date('m')] ?> <?= date('Y') ?>";
        var cachedMobileTugasHtml = $('#mobile-tugas-content-static').html() || '';

        function renderCarouselStatus(activeList, activeNonKbmList, isAfterLastKbm) {
            // Sort / Prioritaskan jadwal guru yang login di awal
            let sortedActiveList = (activeList || []).slice();
            if (loggedInUserName && sortedActiveList.length > 1) {
                let myIndex = sortedActiveList.findIndex(item => item.guru && item.guru.toLowerCase().includes(loggedInUserName.toLowerCase()));
                if (myIndex > 0) {
                    let mySchedule = sortedActiveList.splice(myIndex, 1)[0];
                    sortedActiveList.unshift(mySchedule);
                }
            }

            // 1. REBUILD DESKTOP INFO CAROUSEL (Masing-masing mapel menjadi 1 slide utuh)
            rebuildDesktopInfoCarousel(sortedActiveList, activeNonKbmList, isAfterLastKbm);

            // 2. REBUILD MOBILE HERO CAROUSEL (Masing-masing mapel menjadi slide tersendiri)
            rebuildMobileHeroCarousel(sortedActiveList, activeNonKbmList, isAfterLastKbm);
        }

        function getPiketHtmlDesktop() {
            if (piketHariIniData && piketHariIniData.length > 0) {
                let chips = piketHariIniData.map(gp => `
                    <span class="d-inline-flex align-items-center px-2 py-1 text-truncate" 
                          style="background: #eef2ff; color: #4f46e5; border-radius: 6px; font-weight: 600; font-size: 0.72rem; max-width: 100%;"
                          title="${gp.nama_guru}">
                        <i class="fas fa-user-tie mr-1 text-xs"></i> ${gp.nama_guru}
                    </span>
                `).join('');
                return `
                    <div class="d-flex flex-wrap justify-content-center align-items-center" style="max-height: 80px; overflow-y: auto; gap: 4px;">
                        ${chips}
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size: 0.70rem;">Hari ${piketHariIniNama}</small>
                `;
            } else {
                return `
                    <i class="fas fa-user-clock fa-2x text-muted mb-1" style="opacity: 0.4;"></i>
                    <h6 class="text-secondary font-weight-bold mb-0" style="font-size: 0.85rem;">Guru Piket Hari Ini</h6>
                    <small class="text-muted" style="font-size: 0.70rem;">Belum ada jadwal piket (${piketHariIniNama})</small>
                `;
            }
        }

        function getPiketHtmlMobile() {
            if (piketHariIniData && piketHariIniData.length > 0) {
                let chips = piketHariIniData.map(gp => `
                    <span class="badge badge-light text-dark px-2 py-1 mr-1 mb-1 font-weight-bold" style="font-size: 0.68rem;">
                        <i class="fas fa-user-tie mr-1 text-primary"></i> ${gp.nama_guru}
                    </span>
                `).join('');
                return `
                    <div class="d-flex flex-wrap align-items-center mt-1" style="max-height: 60px; overflow-y: auto;">
                        ${chips}
                    </div>
                `;
            } else {
                return `<small class="text-white-50 d-block mt-1">Belum ada data guru piket hari ini.</small>`;
            }
        }

        function rebuildDesktopInfoCarousel(activeList, activeNonKbmList, isAfterLastKbm) {
            const $carousel = $('#infoCarousel');
            if (!$carousel.length) return;

            let indicators = [];
            let slides = [];
            let idx = 0;

            // SLIDE 1: JAM & TANGGAL
            indicators.push(`<li data-target="#infoCarousel" data-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}"></li>`);
            slides.push(`
                <div class="carousel-item ${idx === 0 ? 'active' : ''} h-100">
                    <div class="carousel-content-wrapper text-center">
                        <div class="digital-clock" id="realtime-clock">--:--:--</div>
                        <div class="date-display">${tanggalStrIndo}</div>
                        <div class="mt-1 text-muted" style="font-size: 0.72rem;">
                            <i class="fas fa-clock text-primary"></i> Waktu Server
                        </div>
                    </div>
                </div>
            `);
            idx++;

            // SLIDES KBM AKTIF (1 SLIDE PER MATA PELAJARAN)
            if (activeList && activeList.length > 0) {
                activeList.forEach((item, i) => {
                    let isMyClass = loggedInUserName && item.guru && item.guru.toLowerCase().includes(loggedInUserName.toLowerCase());
                    let myBadge = isMyClass ? `<span class="badge badge-warning ml-1 px-1 py-0 font-weight-bold text-dark" style="font-size: 0.60rem;"><i class="fas fa-star text-danger"></i> Kelas Anda</span>` : '';
                    let countBadge = activeList.length > 1 ? `<span class="badge badge-primary ml-1 px-1 py-0 font-weight-bold" style="font-size: 0.62rem;">${i + 1}/${activeList.length} Mapel</span>` : '';
                    let mapelFontSize = item.mapel.length > 32 ? '0.78rem' : (item.mapel.length > 20 ? '0.84rem' : '0.94rem');

                    indicators.push(`<li data-target="#infoCarousel" data-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}"></li>`);
                    slides.push(`
                        <div class="carousel-item ${idx === 0 ? 'active' : ''} h-100">
                            <div class="carousel-content-wrapper text-center px-2">
                                <div class="kbm-active-title d-flex align-items-center justify-content-center">
                                    <i class="fas fa-dot-circle mr-1 text-danger"></i> Sedang Berlangsung ${countBadge} ${myBadge}
                                </div>
                                <div class="kbm-active-mapel font-weight-bold" style="font-size: ${mapelFontSize};" title="${item.mapel}">
                                    ${item.mapel}
                                </div>
                                <div class="mt-0.5 d-flex align-items-center justify-content-center">
                                    <span class="kbm-active-kelas font-weight-bold px-2 py-0" style="font-size: 0.72rem;">${item.kelas}</span>
                                    <small class="text-muted ml-2 font-weight-bold" style="font-size: 0.72rem;">(${item.jam_mulai.substring(0, 5)} - ${item.jam_selesai.substring(0, 5)})</small>
                                </div>
                                <div class="text-muted text-truncate mt-0.5" style="font-size: 0.72rem;" title="Pengajar: ${item.guru}">
                                    <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i> ${item.guru}
                                </div>
                            </div>
                        </div>
                    `);
                    idx++;
                });
            } else if (activeNonKbmList && activeNonKbmList.length > 0) {
                let item = activeNonKbmList[0];
                indicators.push(`<li data-target="#infoCarousel" data-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}"></li>`);
                slides.push(`
                    <div class="carousel-item ${idx === 0 ? 'active' : ''} h-100">
                        <div class="carousel-content-wrapper text-center px-3">
                            <div class="text-warning font-weight-bold text-uppercase" style="font-size: 0.72rem;"><i class="fas fa-coffee mr-1"></i> Kegiatan Non-KBM</div>
                            <div class="font-weight-bold text-dark text-truncate mt-1" style="font-size: 0.95rem;" title="${item.nama_kegiatan}">${item.nama_kegiatan}</div>
                            <small class="text-muted d-block mt-1 font-weight-bold" style="font-size: 0.75rem;">(${item.jam_mulai ? item.jam_mulai.substring(0, 5) : ''} - ${item.jam_selesai ? item.jam_selesai.substring(0, 5) : ''})</small>
                        </div>
                    </div>
                `);
                idx++;
            } else if (isAfterLastKbm) {
                indicators.push(`<li data-target="#infoCarousel" data-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}"></li>`);
                slides.push(`
                    <div class="carousel-item ${idx === 0 ? 'active' : ''} h-100">
                        <div class="carousel-content-wrapper text-center px-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-1"></i>
                            <h6 class="text-dark font-weight-bold mb-0" style="font-size: 0.92rem;">KBM Hari Ini Selesai</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Pembelajaran telah tuntas</small>
                        </div>
                    </div>
                `);
                idx++;
            } else {
                indicators.push(`<li data-target="#infoCarousel" data-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}"></li>`);
                slides.push(`
                    <div class="carousel-item ${idx === 0 ? 'active' : ''} h-100">
                        <div class="carousel-content-wrapper text-center px-3">
                            <i class="fas fa-mug-hot fa-2x text-secondary mb-1" style="opacity: 0.6;"></i>
                            <h6 class="text-secondary font-weight-bold mb-0" style="font-size: 0.92rem;">Tidak Ada KBM</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Menunggu jam masuk...</small>
                        </div>
                    </div>
                `);
                idx++;
            }

            // SLIDE TERAKHIR: GURU PIKET HARI INI (Hanya jika KBM belum selesai)
            if (!isAfterLastKbm) {
                indicators.push(`<li data-target="#infoCarousel" data-slide-to="${idx}"></li>`);
                slides.push(`
                    <div class="carousel-item h-100">
                        <div class="carousel-content-wrapper text-center px-2">
                            <div class="d-flex align-items-center justify-content-center mb-1 text-primary" style="font-size: 0.72rem; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase;">
                                <i class="fas fa-user-clock mr-1"></i> Guru Piket
                            </div>
                            ${getPiketHtmlDesktop()}
                        </div>
                    </div>
                `);
            }

            $carousel.find('.carousel-indicators').html(indicators.join(''));
            $carousel.find('.carousel-inner').html(slides.join(''));
            $carousel.carousel('dispose').carousel({ interval: 5000, ride: 'carousel' });
        }

        function rebuildMobileHeroCarousel(activeList, activeNonKbmList, isAfterLastKbm) {
            const $carousel = $('#mobileHeroCarousel');
            if (!$carousel.length) return;

            let indicators = [];
            let slides = [];
            let idx = 0;

            // MOBILE SLIDE 1: WELCOME & LIVE CLOCK
            indicators.push(`<li data-target="#mobileHeroCarousel" data-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}"></li>`);
            slides.push(`
                <div class="carousel-item active">
                    <div class="mobile-hero-slide">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge badge-warning px-1.5 py-0.5 font-weight-bold text-uppercase" style="font-size: 0.58rem; background: #fbbf24; color: #78350f; border-radius: 4px;">
                                <i class="fas fa-school mr-1"></i> SIMAKS PORTAL
                            </span>
                            <span class="badge px-1.5 py-0.5 font-weight-bold text-white" style="font-size: 0.58rem; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 4px;">
                                <i class="fas fa-user-tie mr-1 text-warning"></i> <?= htmlspecialchars(isset($_SESSION['roles'][0]) ? $_SESSION['roles'][0] : 'User') ?>
                            </span>
                        </div>
                        <div class="banner-user-name-mobile text-truncate" title="<?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>">
                            Selamat Datang, <?= htmlspecialchars($_SESSION['nama_pengguna'] ?? 'User') ?>! 👋
                        </div>
                        <div class="text-white-50 text-truncate mb-1" style="font-size: 0.64rem;">
                            TA Aktif: <strong class="text-warning"><?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? 'Belum Aktif') ?></strong>
                        </div>
                        <div class="d-inline-flex align-items-center px-2 py-0.5" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); border-radius: 6px; font-size: 0.65rem; color: #ffffff; width: fit-content; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                            <i class="fas fa-clock text-warning mr-1"></i>
                            <span id="realtime-clock-mobile" class="font-weight-bold mr-1" style="letter-spacing: 0.5px;">--:--:--</span>
                            <span class="text-white-50 ml-1">&bull; <?= $hari_indo[date('l')] ?>, <?= date('d') ?> <?= $bulan_indo[(int)date('m')] ?></span>
                        </div>
                    </div>
                </div>
            `);
            idx++;

            // MOBILE SLIDE 2: TUGAS HARIAN
            indicators.push(`<li data-target="#mobileHeroCarousel" data-slide-to="${idx}"></li>`);
            slides.push(`
                <div class="carousel-item">
                    <div class="mobile-hero-slide" id="mobile-slide-tugas-container">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge badge-warning px-1.5 py-0 font-weight-bold text-dark" style="font-size: 0.52rem; border-radius: 4px;">
                                <i class="fas fa-tasks mr-1"></i> Agenda Harian Guru
                            </span>
                            <small class="text-white-50 font-weight-bold" style="font-size: 0.56rem;"><i class="far fa-calendar-alt mr-1"></i> ${piketHariIniNama}</small>
                        </div>
                        ${cachedMobileTugasHtml || $('#mobile-tugas-content-static').html() || ''}
                    </div>
                </div>
            `);
            idx++;

            // MOBILE SLIDES: KBM AKTIF / NON-KBM / SELESAI
            if (activeList && activeList.length > 0) {
                activeList.forEach((item, i) => {
                    let isMyClass = loggedInUserName && item.guru && item.guru.toLowerCase().includes(loggedInUserName.toLowerCase());
                    let myBadge = isMyClass ? `<span class="badge badge-warning px-1.5 py-0 font-weight-bold text-dark" style="font-size: 0.52rem; border-radius: 4px;"><i class="fas fa-star text-danger mr-1"></i>Kelas Anda</span>` : '';
                    let countBadge = activeList.length > 1 ? `<span class="badge badge-primary px-1.5 py-0 font-weight-bold" style="font-size: 0.52rem; border-radius: 4px;">${i + 1}/${activeList.length} Mapel</span>` : '';

                    indicators.push(`<li data-target="#mobileHeroCarousel" data-slide-to="${idx}"></li>`);
                    slides.push(`
                        <div class="carousel-item">
                            <div class="mobile-hero-slide">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge badge-danger px-1.5 py-0 font-weight-bold" style="font-size: 0.52rem; border-radius: 4px;">
                                        <i class="fas fa-broadcast-tower mr-1"></i> KBM Live ${countBadge}
                                    </span>
                                    ${myBadge}
                                </div>
                                <h4 class="font-weight-bold text-white mb-0.5" style="font-size: ${item.mapel.length > 25 ? '0.74rem' : '0.80rem'}; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="${item.mapel}">
                                    ${item.mapel} &bull; <span class="badge badge-success px-1.5 py-0" style="border-radius: 4px;">${item.kelas}</span>
                                </h4>
                                <div class="text-white-50 text-truncate mt-0.5" style="font-size: 0.58rem;">
                                    <i class="fas fa-clock text-warning mr-1"></i> ${item.jam_mulai.substring(0, 5)} - ${item.jam_selesai.substring(0, 5)}
                                </div>
                                <div class="text-white-50 text-truncate" style="font-size: 0.58rem;">
                                    <i class="fas fa-chalkboard-teacher text-info mr-1"></i> Guru: ${item.guru}
                                </div>
                            </div>
                        </div>
                    `);
                    idx++;
                });
            } else if (activeNonKbmList && activeNonKbmList.length > 0) {
                let item = activeNonKbmList[0];
                indicators.push(`<li data-target="#mobileHeroCarousel" data-slide-to="${idx}"></li>`);
                slides.push(`
                    <div class="carousel-item">
                        <div class="mobile-hero-slide">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge badge-warning px-1.5 py-0 font-weight-bold text-dark" style="font-size: 0.52rem; border-radius: 4px;">
                                    <i class="fas fa-coffee mr-1"></i> Non-KBM
                                </span>
                            </div>
                            <h4 class="font-weight-bold text-white mb-0.5 text-truncate" style="font-size: 0.80rem;">
                                ${item.nama_kegiatan}
                            </h4>
                            <div class="text-white-50 text-truncate mt-0.5" style="font-size: 0.58rem;">
                                <i class="fas fa-clock text-warning mr-1"></i> ${item.jam_mulai ? item.jam_mulai.substring(0, 5) : ''} - ${item.jam_selesai ? item.jam_selesai.substring(0, 5) : ''}
                            </div>
                        </div>
                    </div>
                `);
                idx++;
            } else if (isAfterLastKbm) {
                indicators.push(`<li data-target="#mobileHeroCarousel" data-slide-to="${idx}"></li>`);
                slides.push(`
                    <div class="carousel-item">
                        <div class="mobile-hero-slide text-center py-1">
                            <div class="d-flex align-items-center justify-content-center text-success mb-0.5" style="font-size: 0.74rem; font-weight: 700;">
                                <i class="fas fa-check-circle mr-1"></i> KBM Hari Ini Selesai
                            </div>
                            <p class="text-white-50 mb-0" style="font-size: 0.58rem;">
                                Seluruh sesi pembelajaran hari ini telah tuntas.
                            </p>
                        </div>
                    </div>
                `);
                idx++;
            }

            // MOBILE SLIDE TERAKHIR: GURU PIKET (Hanya jika KBM belum selesai)
            if (!isAfterLastKbm) {
                indicators.push(`<li data-target="#mobileHeroCarousel" data-slide-to="${idx}"></li>`);
                slides.push(`
                    <div class="carousel-item">
                        <div class="mobile-hero-slide">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge badge-primary px-1.5 py-0 font-weight-bold" style="font-size: 0.52rem; border-radius: 4px;">
                                    <i class="fas fa-user-clock mr-1"></i> Guru Piket
                                </span>
                                <small class="text-white-50 font-weight-bold" style="font-size: 0.56rem;"><i class="far fa-calendar-alt mr-1"></i> ${piketHariIniNama}</small>
                            </div>
                            ${getPiketHtmlMobile()}
                        </div>
                    </div>
                `);
            }

            $carousel.find('.carousel-indicators').html(indicators.join(''));
            $carousel.find('.carousel-inner').html(slides.join(''));
            $carousel.carousel('dispose').carousel({ interval: 6000, ride: 'carousel' });
        }

        // --- 5. EKSEKUSI AWAL ---
        fetchScheduleData();
        setInterval(updateTimeAndStatus, 1000);

        // Auto Cycle Desktop Carousels
        $('#bannerCarousel').carousel({
            interval: 8000,
            ride: 'carousel'
        });
        $('#infoCarousel').carousel({
            interval: 5000,
            ride: 'carousel'
        });

        // Auto Cycle Mobile Unified Carousel with Touch Swipe Support
        const $mobileHero = $('#mobileHeroCarousel');
        $mobileHero.carousel({
            interval: 7000,
            ride: 'carousel'
        });

        // Touch swipe gesture for smartphone
        let touchStartX = 0;
        let touchEndX = 0;
        const heroEl = document.getElementById('mobileHeroCarousel');
        if (heroEl) {
            heroEl.addEventListener('touchstart', function (e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            heroEl.addEventListener('touchend', function (e) {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 45) {
                    $mobileHero.carousel('next');
                } else if (touchEndX - touchStartX > 45) {
                    $mobileHero.carousel('prev');
                }
            }, { passive: true });
        }

        // --- 6. SMART TIME-TRIGGERED POP-UP PENGINGAT TUGAS HARIAN & PIKET ---
        const tugasStatusData = <?= json_encode($tugas_status ?? []) ?>;

        function checkSmartReminderPopup() {
            if (!tugasStatusData || !tugasStatusData.pending_tasks || tugasStatusData.pending_tasks.length === 0) {
                return; // Semua tugas sudah selesai
            }

            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            const timeInMinutes = currentHour * 60 + currentMinute;
            const todayDateStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');

            // Ambang batas jam 12:00 siang (720 menit)
            if (timeInMinutes < 720) {
                return; // Pagi hari tidak diganggu popup
            }

            // Cek sessionStorage agar popup hanya muncul 1x per sesi
            const dismissedKey = 'simaks_reminder_dismissed_' + todayDateStr;
            if (sessionStorage.getItem(dismissedKey)) {
                return;
            }

            let taskListHtml = '<ul style="text-align: left; margin: 12px 0 6px 0; padding-left: 20px; font-size: 0.88rem; color: #334155; line-height: 1.6;">';
            tugasStatusData.pending_tasks.forEach(t => {
                taskListHtml += `<li><strong>${t.title}</strong></li>`;
            });
            taskListHtml += '</ul>';

            let popupTitle = tugasStatusData.is_piket_today ? 'Pengingat Tugas Piket & KBM Hari Ini' : 'Pengingat Tugas KBM Hari Ini';

            Swal.fire({
                icon: 'warning',
                title: `<span style="font-size: 1.15rem; font-weight: 700; color: #b45309;"><i class="fas fa-bell mr-2"></i>${popupTitle}</span>`,
                html: `
                    <p style="font-size: 0.9rem; color: #475569; margin-bottom: 6px; text-align: left;">
                        Waktu operasional saat ini sudah memasuki siang hari (${String(currentHour).padStart(2, '0')}:${String(currentMinute).padStart(2, '0')}). Berikut tugas harian Anda yang <strong>belum selesai diisi</strong>:
                    </p>
                    ${taskListHtml}
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 10px; margin-bottom: 0; text-align: left;">
                        <i class="fas fa-info-circle mr-1 text-primary"></i> Mohon lengkapi laporan kehadiran/jurnal sebelum jam KBM berakhir.
                    </p>
                `,
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fas fa-edit mr-1"></i> Lengkapi Sekarang',
                cancelButtonText: '<i class="fas fa-clock mr-1"></i> Ingatkan Nanti',
                customClass: {
                    popup: 'shadow-lg rounded-xl',
                    confirmButton: 'px-3 py-2 font-weight-bold',
                    cancelButton: 'px-3 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (tugasStatusData.pending_tasks[0] && tugasStatusData.pending_tasks[0].url) {
                        window.location.href = tugasStatusData.pending_tasks[0].url;
                    }
                } else {
                    sessionStorage.setItem(dismissedKey, '1');
                }
            });
        }

        setTimeout(checkSmartReminderPopup, 1200);

        // ---[BARU] CHART DASHBOARD ALUMNI & TRACER ---
        // Siapkan data dari PHP
        var tracerStats = <?= json_encode($tracer_stats ?? []) ?>;

        if (tracerStats.length > 0) {
            var labels = [];
            var dataL = [];
            var dataP = [];
            var dataStatus = {
                'kuliah': 0, 'kerja': 0, 'wirausaha': 0, 'lain': 0
            };

            // Loop untuk chart Lulusan (Bar) & Agregat Tracer (Doughnut)
            // Data diurutkan dari terbaru, kita balik agar grafik tahun lama di kiri
            var chartData = tracerStats.slice().reverse();

            chartData.forEach(function (item) {
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
            
            // Create gradients for graduate chart
            let gradientL = ctxGrad.createLinearGradient(0, 0, 0, 300);
            gradientL.addColorStop(0, 'rgba(79, 70, 229, 0.85)'); // Indigo
            gradientL.addColorStop(1, 'rgba(99, 102, 241, 0.3)');

            let gradientP = ctxGrad.createLinearGradient(0, 0, 0, 300);
            gradientP.addColorStop(0, 'rgba(236, 72, 153, 0.85)'); // Pink/Rose
            gradientP.addColorStop(1, 'rgba(244, 63, 94, 0.4)');

            new Chart(ctxGrad, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { 
                            label: 'Laki-laki', 
                            data: dataL, 
                            backgroundColor: gradientL, 
                            borderColor: '#4f46e5',
                            borderWidth: 1.5,
                            barPercentage: 0.5,
                            categoryPercentage: 0.6
                        },
                        { 
                            label: 'Perempuan', 
                            data: dataP, 
                            backgroundColor: gradientP, 
                            borderColor: '#db2777',
                            borderWidth: 1.5,
                            barPercentage: 0.5,
                            categoryPercentage: 0.6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'top',
                        labels: {
                            fontFamily: "'Poppins', sans-serif",
                            fontSize: 12,
                            fontColor: '#475569',
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltips: {
                        backgroundColor: '#1e293b',
                        titleFontFamily: "'Poppins', sans-serif",
                        titleFontSize: 13,
                        titleFontColor: '#fff',
                        bodyFontFamily: "'Poppins', sans-serif",
                        bodyFontSize: 12,
                        bodySpacing: 4,
                        xPadding: 12,
                        yPadding: 10,
                        cornerRadius: 8,
                        displayColors: true
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                fontFamily: "'Poppins', sans-serif",
                                fontSize: 11,
                                fontColor: '#64748b',
                                padding: 5
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                color: '#f1f5f9',
                                zeroLineColor: '#e2e8f0'
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                fontFamily: "'Poppins', sans-serif",
                                fontSize: 11,
                                fontColor: '#64748b',
                                padding: 10
                            }
                        }]
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
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#64748b'],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 70, // Sleek ring layout
                    legend: {
                        position: 'right',
                        labels: {
                            fontFamily: "'Poppins', sans-serif",
                            fontSize: 12,
                            fontColor: '#475569',
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltips: {
                        backgroundColor: '#1e293b',
                        titleFontFamily: "'Poppins', sans-serif",
                        titleFontSize: 13,
                        titleFontColor: '#fff',
                        bodyFontFamily: "'Poppins', sans-serif",
                        bodyFontSize: 12,
                        bodySpacing: 4,
                        xPadding: 12,
                        yPadding: 10,
                        cornerRadius: 8,
                        displayColors: true
                    }
                }
            });
        }
    });

    function toggleExecutiveMonitoring(mode) {
        if (mode === 'online') {
            $('#monitoring-view-offline').attr('style', 'display: none !important');
            $('#monitoring-view-online').attr('style', 'display: flex !important; gap: 4px;');
            $('#btn-switch-offline').removeClass('active').css({'background': 'transparent', 'color': 'rgba(255,255,255,0.65)', 'box-shadow': 'none'});
            $('#btn-switch-online').addClass('active').css({'background': '#10b981', 'color': '#ffffff', 'box-shadow': '0 1px 3px rgba(0,0,0,0.25)'});
        } else {
            $('#monitoring-view-online').attr('style', 'display: none !important');
            $('#monitoring-view-offline').attr('style', 'display: flex !important; gap: 4px;');
            $('#btn-switch-online').removeClass('active').css({'background': 'transparent', 'color': 'rgba(255,255,255,0.65)', 'box-shadow': 'none'});
            $('#btn-switch-offline').addClass('active').css({'background': '#0284c7', 'color': '#ffffff', 'box-shadow': '0 1px 3px rgba(0,0,0,0.25)'});
        }
    }
</script>