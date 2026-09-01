<?php
/**
 * CBT Admin - Header Partial
 * Require: $page_title (string)
 */
$cbt_user_nama = $_SESSION['cbt_user_nama'] ?? 'Admin';
$cbt_role = $_SESSION['cbt_role'] ?? '';
$current_mod = $_GET['mod'] ?? 'dashboard';

// base URL helper (without trailing slash)
$cbt_base = rtrim(CBT_BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Panel CBT') ?> | CBT SIMAKS</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 4.6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">

    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery (Moved to head for view scripts) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cbt-primary: #e94560;
            --cbt-dark: #1a1a2e;
            --cbt-sidebar: #16213e;
            --cbt-light-bg: #f4f6fb;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif !important;
            background-color: var(--cbt-light-bg);
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .main-header.navbar {
            background: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 0.5rem 1rem !important;
            border-bottom: 1px solid #e9ecef;
        }

        .navbar-brand {
            padding: 0 !important;
        }

        /* Sidebar Styling */
        .main-sidebar {
            background: var(--cbt-sidebar) !important;
            width: 260px !important;
            padding: 0 !important;
        }

        .brand-link {
            background: var(--cbt-dark) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            padding: 1.5rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px;
        }

        .brand-link .brand-icon {
            font-size: 1.5rem;
            color: var(--cbt-primary);
        }

        .brand-link .brand-text {
            color: #fff !important;
            font-weight: 700;
            font-size: 1rem;
        }

        .brand-link .brand-text span {
            color: var(--cbt-primary);
        }

        /* Navigation Items */
        .nav-sidebar {
            padding: 1rem 0;
        }

        .nav-header {
            color: rgba(255, 255, 255, 0.4) !important;
            font-size: 0.75rem !important;
            font-weight: 600;
            letter-spacing: 0.08em;
            padding: 1rem 1.5rem 0.5rem !important;
            text-transform: uppercase;
        }

        .nav-item {
            margin: 0 0.75rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7) !important;
            border-radius: 6px;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(233, 69, 96, 0.15) !important;
            color: #fff !important;
        }

        .nav-link.active {
            background: rgba(233, 69, 96, 0.25) !important;
            color: #fff !important;
            border-left: 3px solid var(--cbt-primary);
            padding-left: calc(1rem - 3px) !important;
        }

        .nav-icon {
            color: var(--cbt-primary) !important;
            width: 1.5rem;
            text-align: center;
            margin-right: 0.75rem;
        }

        .nav-link p {
            margin: 0;
            font-size: 0.95rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 2rem 1rem;
        }

        .content-wrapper {
            background: var(--cbt-light-bg) !important;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-header {
            background: white;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            border-left: 4px solid var(--cbt-primary);
        }

        .content-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--cbt-dark);
            margin: 0;
        }

        .content-header .breadcrumb {
            margin: 0.5rem 0 0 0;
            background: transparent;
            padding: 0;
        }

        .content-header .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Cards */
        .card {
            border: none !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .card-header {
            background: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
            color: var(--cbt-dark);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Small Box (Stats) */
        .small-box {
            border-radius: 8px !important;
            overflow: hidden;
            display: flex !important;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem !important;
            min-height: 140px;
            margin-bottom: 1rem;
        }

        .small-box .inner {
            flex: 1;
        }

        .small-box h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .small-box p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0;
        }

        .small-box .icon {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            font-size: 3rem;
            opacity: 0.15;
        }

        .small-box-footer {
            padding-top: 1rem;
            display: block;
            font-size: 0.9rem;
            opacity: 0.8;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .small-box-footer:hover {
            opacity: 1;
        }

        /* Buttons */
        .btn {
            border-radius: 6px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--cbt-primary) !important;
            border-color: var(--cbt-primary) !important;
        }

        .btn-primary:hover {
            background: #c0392b !important;
            border-color: #c0392b !important;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: var(--cbt-dark);
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Dropdown */
        .dropdown-menu {
            border-radius: 6px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: var(--cbt-dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-sidebar {
                width: 100% !important;
            }

            .content-header {
                margin-bottom: 1rem;
            }

            .content-header h1 {
                font-size: 1.25rem;
            }
        }

        /* Loading & Transitions */
        body.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Compact Refinements */
        .content-header {
            padding: 0.5rem 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        .content-header h1 {
            font-size: 1.15rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .table thead th {
            padding: 0.4rem 0.6rem !important;
            font-size: 0.8rem !important;
            background: #f8f9fa;
        }

        .table tbody td {
            padding: 0.3rem 0.6rem !important;
            font-size: 0.8rem !important;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
        }

        .dropdown-item {
            font-size: 0.8rem !important;
            padding: 0.3rem 0.75rem !important;
        }

        .main-content {
            padding: 1rem !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <span class="nav-link text-muted" style="cursor: default;">
                        <?= htmlspecialchars($page_title ?? '') ?>
                    </span>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" role="button">
                        <i class="fas fa-user-circle text-secondary mr-1"></i>
                        <span><?= htmlspecialchars($cbt_user_nama) ?></span>
                        <span class="badge badge-sm badge-danger ml-1"><?= strtoupper($cbt_role) ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="<?= $cbt_base ?>?mod=logout">
                            <i class="fas fa-sign-out-alt mr-2 text-danger"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary">
            <!-- Brand/Logo -->
            <a href="<?= $cbt_base ?>" class="brand-link">
                <i class="fas fa-laptop-code brand-icon"></i>
                <span class="brand-text">Panel <span>CBT</span></span>
            </a>

            <!-- Sidebar Menu -->
            <div class="sidebar">
                <nav class="mt-3">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=dashboard"
                                class="nav-link <?= $current_mod === 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Admin Section -->
                        <li class="nav-header">ADMINISTRASI</li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=kelola_kelas"
                                class="nav-link <?= $current_mod === 'kelola_kelas' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-school"></i>
                                <p>Data Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=kelola_mapel"
                                class="nav-link <?= $current_mod === 'kelola_mapel' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>Data Mapel</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=kelola_siswa"
                                class="nav-link <?= $current_mod === 'kelola_siswa' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Data Siswa</p>
                            </a>
                        </li>

                        <!-- Bank Soal Section -->
                        <li class="nav-header">BANK SOAL</li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=bank_soal"
                                class="nav-link <?= $current_mod === 'bank_soal' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-database"></i>
                                <p>Bank Soal</p>
                            </a>
                        </li>

                        <!-- Exam Management -->
                        <li class="nav-header">MANAJEMEN UJIAN</li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=kelola_ujian"
                                class="nav-link <?= $current_mod === 'kelola_ujian' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Setting Ujian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=peserta"
                                class="nav-link <?= $current_mod === 'peserta' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Status Peserta</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=hasil_ujian"
                                class="nav-link <?= $current_mod === 'hasil_ujian' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Hasil & Analisis</p>
                            </a>
                        </li>

                        <!-- Back to SIMAKS -->
                        <li class="nav-header">KEMBALI</li>
                        <li class="nav-item">
                            <a href="/" class="nav-link">
                                <i class="nav-icon fas fa-arrow-left"></i>
                                <p>Kembali ke SIMAKS</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">