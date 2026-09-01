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
    <title>
        <?= htmlspecialchars($page_title ?? 'Panel CBT') ?> | CBT SIMAKS
    </title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="/simaks/public/assets/AdminLTE/dist/css/adminlte.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- Custom CBT Style -->
    <style>
        :root {
            --cbt-primary: #e94560;
            --cbt-dark: #1a1a2e;
            --cbt-sidebar: #16213e;
        }

        body {
            font-family: 'Inter', sans-serif !important;
        }

        .main-sidebar {
            background: var(--cbt-sidebar) !important;
        }

        .brand-link {
            background: var(--cbt-dark) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
        }

        .brand-link .brand-text {
            color: #fff !important;
            font-weight: 700;
        }

        .brand-link .brand-text span {
            color: var(--cbt-primary);
        }

        .nav-sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
            border-radius: 8px;
            margin: 2px 8px;
        }

        .nav-sidebar .nav-item .nav-link:hover,
        .nav-sidebar .nav-item .nav-link.active {
            background: rgba(233, 69, 96, .2) !important;
            color: #fff !important;
        }

        .nav-sidebar .nav-item .nav-link .nav-icon {
            color: var(--cbt-primary) !important;
        }

        .nav-header {
            color: rgba(255, 255, 255, 0.35) !important;
            font-size: .7rem !important;
            letter-spacing: .1em;
            padding-left: 16px;
        }

        .content-wrapper {
            background: #f4f6fb !important;
        }

        .main-header.navbar {
            background: #fff !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, .07);
        }

        .content-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 2px 15px rgba(0, 0, 0, .06) !important;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block"><span class="nav-link text-muted">
                        <?= htmlspecialchars($page_title ?? '') ?>
                    </span></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user-circle text-secondary mr-1"></i>
                        <?= htmlspecialchars($cbt_user_nama) ?>
                        <span class="badge badge-sm badge-danger ml-1">
                            <?= strtoupper($cbt_role) ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="<?= $cbt_base ?>?mod=logout">
                            <i class="fas fa-sign-out-alt mr-2 text-danger"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="<?= $cbt_base ?>" class="brand-link px-3">
                <i class="fas fa-laptop-code mr-2" style="color:var(--cbt-primary)"></i>
                <span class="brand-text">Panel <span>CBT</span></span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=dashboard"
                                class="nav-link <?= $current_mod === 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

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

                        <li class="nav-header">BANK SOAL</li>
                        <li class="nav-item">
                            <a href="<?= $cbt_base ?>?mod=bank_soal"
                                class="nav-link <?= $current_mod === 'bank_soal' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-database"></i>
                                <p>Bank Soal</p>
                            </a>
                        </li>

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

                        <li class="nav-header">KEMBALI</li>
                        <li class="nav-item">
                            <a href="/simaks/public/index.php" class="nav-link">
                                <i class="nav-icon fas fa-arrow-left"></i>
                                <p>Kembali ke SIMAKS</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
        <div class="content-wrapper">