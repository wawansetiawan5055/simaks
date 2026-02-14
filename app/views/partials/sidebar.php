<?php
// File: app/views/partials/sidebar.php

// =====================================================================
// [LOGIKA] OTOMATIS LOAD DATA MENU JIKA KOSONG
// =====================================================================
if (!isset($user_menu) || empty($user_menu)) {
    // KOREKSI PATH: Mundur 2 langkah dari app/views/partials ke app/models
    $model_path = __DIR__ . '/../../models/AppMenuModel.php';

    if (file_exists($model_path)) {
        require_once $model_path;

        // Pastikan koneksi $pdo tersedia
        if (isset($pdo) && isset($_SESSION['role_ids'])) {
            $user_menu = AppMenuModel::getUserMenu($pdo, $_SESSION['role_ids']);
            // Standardize "Penugasan Guru" to "Penugasan GTK" manually for UI consistency if needed
            foreach ($user_menu as &$m) {
                if ($m['nama_menu'] == 'Penugasan Guru')
                    $m['nama_menu'] = 'Penugasan GTK';
                if (!empty($m['children'])) {
                    foreach ($m['children'] as &$c) {
                        if ($c['nama_menu'] == 'Penugasan Guru')
                            $c['nama_menu'] = 'Penugasan GTK';
                    }
                }
            }
        }
    }
}

// Inisialisasi variabel default
$user_menu = $user_menu ?? [];
$mod = $_GET['mod'] ?? 'dashboard';
$act = $_GET['act'] ?? 'index';

// =====================================================================
// [HELPER] FUNGSI CEK AKTIF
// =====================================================================
if (!function_exists('is_menu_active_dynamic')) {
    function is_menu_active_dynamic($menu_link, $current_mod, $current_act)
    {
        if ($menu_link === '#')
            return false;

        $url_parts = parse_url($menu_link);
        parse_str($url_parts['query'] ?? '', $query_params);

        $target_mod = $query_params['mod'] ?? '';
        $target_act = $query_params['act'] ?? 'index';

        // 1. Cek mod
        if ($target_mod !== $current_mod) {
            return false;
        }

        // 2. Cek act
        if ($target_act !== $current_act) {
            return false;
        }

        // 3. Cek parameter tambahan lainnya (misal: type, id, dll)
        // Semua parameter yang ada di link menu harus cocok dengan yang ada di $_GET
        foreach ($query_params as $key => $value) {
            if ($key === 'mod' || $key === 'act')
                continue;

            $current_val = $_GET[$key] ?? '';
            if ($current_val != $value) {
                return false;
            }
        }

        return true;
    }
}
?>

<style>
    /* Styling Dasar Font */
    .nav-sidebar .nav-link p {
        font-weight: 500;
    }

    .brand-link-custom .brand-text {
        font-weight: 600;
    }

    .brand-link-custom .brand-text-small {
        font-size: 0.75rem;
        line-height: 1.3;
    }

    .sidebar {
        padding-bottom: 125px;
        padding-right: 0 !important;
        /* [FIX] Hapus padding kanan container */
        padding-left: 0 !important;
        /* [FIX] Hapus padding kiri juga biar imbang/custom */
    }

    .nav-sidebar {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }

    /* ============================================================
       [REVISI WARNA] NAVY BLUE THEME OVERRIDE
       Ini memaksa sidebar berwarna biru tua dan teks berwarna putih
       ============================================================ */
    .main-sidebar {
        background-color: #001F3F !important;
    }

    /* Warna Teks Menu (Abu-abu terang mendekati putih) */
    .main-sidebar .nav-link,
    .main-sidebar .brand-link {
        color: #c2c7d0 !important;
    }

    /* Warna Teks saat di-hover (Putih Murni) */
    .main-sidebar .nav-link:hover,
    .main-sidebar .brand-link:hover {
        color: #ffffff !important;
    }

    /* Warna Header Menu (misal: MANAJEMEN DATA) */
    .nav-header {
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #aeb4b9 !important;
    }

    /* Garis pemisah di bawah Logo */
    /* Garis pemisah di bawah Logo & Full Width */
    .brand-link {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    /* --- [FIX] SIDEBAR LEBAR & CONDENSED TEXT --- */
    @media (min-width: 768px) {
        body:not(.sidebar-collapse) .main-sidebar {
            width: 280px !important;
        }

        body:not(.sidebar-collapse) .content-wrapper,
        body:not(.sidebar-collapse) .main-header,
        body:not(.sidebar-collapse) .main-footer {
            margin-left: 280px !important;
        }
    }

    /* Reset Margin & Padding Global untuk Sidebar Item */
    .nav-sidebar .nav-item {
        margin-bottom: 0px !important;
        margin-top: 0px !important;
    }

    /* Optimasi Transisi CSS */
    .nav-sidebar .nav-link {
        display: flex !important;
        align-items: center !important;

        padding-top: 4px !important;
        padding-bottom: 4px !important;
        padding-left: 15px !important;

        margin-right: 5px !important;
        margin-left: 10px !important;

        width: calc(100% - 15px) !important;
        border-radius: 30px 0 0 30px;

        /* [PERFORMANCE] Ganti transition: all dengan property spesifik untuk menghindari layout thrashing */
        transition: background-color 0.2s ease, color 0.2s ease !important;
        min-height: 32px;
    }

    .nav-sidebar .nav-link p {
        font-size: 0.9rem;
        letter-spacing: -0.3px;
        line-height: 1.1;
        white-space: normal;
        margin-bottom: 0 !important;
        margin-top: 0 !important;
        flex: 1;
    }

    /* Icon menu */
    .nav-sidebar .nav-link .nav-icon {
        margin-right: 0.4rem;
        font-size: 0.9rem;
        text-align: center;
        min-width: 1.6rem;
        transition: color 0.2s ease;
        /* Tambahkan transisi halus untuk icon */
    }

    /* [FIX] Submenu lebih menjorok ke dalam */
    .nav-treeview .nav-link {
        padding-left: 35px !important;
    }

    /* Level 3 jika ada */
    .nav-treeview .nav-treeview .nav-link {
        padding-left: 55px !important;
    }

    /* Perbaikan Jarak Header agar konsisten */
    .nav-header {
        padding: 0.5rem 1rem 0.2rem 1rem !important;
        font-size: 0.8rem;
        margin-top: 4px;
        margin-bottom: 0px;
    }

    /* Paksa OverlayScrollbars agar tidak memberi padding kanan pada list menu */
    .os-padding {
        z-index: 15;
    }

    .os-viewport {
        overflow-y: auto !important;
    }
</style>

<aside class="main-sidebar elevation-4">

    <a href="index.php?mod=dashboard"
        class="brand-link brand-link-custom d-flex flex-column align-items-center text-center py-3 w-100">
        <img src="assets/img/logoapk.png" alt="Logo" class="brand-image-custom img-circle elevation-3 mb-2"
            style="float: none !important; margin-right: 0 !important; max-height: 55px !important; width: auto !important;">
        <span class="brand-text font-weight-bold d-block h5 mb-0" style="color: #fff !important;">SIMAKS</span>
        <span class="brand-text-small d-block text-wrap px-3"
            style="font-size: 0.75rem; line-height: 1.3; color: rgba(255,255,255,0.85) !important;">
            Sistem Informasi Manajemen Akademik Sekolah
        </span>
    </a>

    <div class="sidebar">
        <!-- Script Restore Scroll Position (Optimized with Debounce/RequestsAnimationFrame) -->
        <script>
            (function () {
                var sidebar = document.querySelector('.main-sidebar .sidebar');
                if (sidebar) {
                    // 1. Restore Logic
                    var savedPos = localStorage.getItem('sidebarScrollPos');
                    if (savedPos) {
                        requestAnimationFrame(function () {
                            sidebar.scrollTop = savedPos;
                        });
                    }

                    // 2. Save Logic (Debounced)
                    var timeout;
                    sidebar.addEventListener('scroll', function () {
                        if (timeout) clearTimeout(timeout);
                        timeout = setTimeout(function () {
                            localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
                        }, 100);
                    });
                }

                // [OPTIMISASI] Script legacy untuk animasi instan dihapus agar animasi default (smooth) berjalan
            })();
        </script>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
                data-animation-speed="300">

                <li class="nav-item">
                    <?php $active = ($mod === 'dashboard') ? 'active' : ''; ?>
                    <a href="index.php?mod=dashboard" class="nav-link <?= $active ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php
                // --- DEFINISI FUNGSI REKURSIF ---
                // Fungsi ini menangani rendering menu bertingkat tanpa batas (N-Level)
                // dan menangani Header yang memiliki anak.
                
                if (!function_exists('renderSidebarRecursive')) {
                    function renderSidebarRecursive($items, $current_mod, $current_act)
                    {
                        foreach ($items as $item) {
                            // Skip Dashboard (ID 1) if handled manually above
                            if (isset($item['id_menu']) && $item['id_menu'] == 1)
                                continue;

                            // Cek apakah item ini punya anak
                            $has_sub = !empty($item['children']);

                            // Cek Status Aktif (Recursively check children for treeview state)
                            $isActive = false;
                            $isOpen = false;

                            if ($has_sub) {
                                // Cek manual apakah salah satu anak aktif
                                // Fungsi helper rekursif kecil untuk cek active tree
                                $checkActiveTree = function ($children) use (&$checkActiveTree, $current_mod, $current_act) {
                                    foreach ($children as $child) {
                                        if (is_menu_active_dynamic($child['link'], $current_mod, $current_act))
                                            return true;
                                        if (!empty($child['children'])) {
                                            if ($checkActiveTree($child['children']))
                                                return true;
                                        }
                                    }
                                    return false;
                                };

                                if ($checkActiveTree($item['children'])) {
                                    $isActive = true; // Parent ikut aktif (warna)
                                    $isOpen = true;   // Treeview terbuka
                                }
                            } else {
                                $isActive = is_menu_active_dynamic($item['link'], $current_mod, $current_act);
                            }

                            $activeClass = $isActive ? 'active' : '';
                            $menuOpenClass = $isOpen ? 'menu-open' : '';

                            // --- LOGIC RENDERING ---
                
                            // 1. Tipe HEADER (Label Teks)
                            // Jika link '#' dan nama Uppercase -> Dianggap Header
                            if ($item['link'] === '#' && strtoupper($item['nama_menu']) === $item['nama_menu']) {
                                echo '<li class="nav-header">' . htmlspecialchars($item['nama_menu']) . '</li>';

                                // [PENTING] Jika header punya anak di database, render anak-anaknya di bawah header ini
                                // sebagai menu root (bukan menjorok ke dalam treeview)
                                if ($has_sub) {
                                    renderSidebarRecursive($item['children'], $current_mod, $current_act);
                                }
                                continue;
                            }

                            // 2. Tipe TREEVIEW (Menu dengan Submenu)
                            if ($has_sub) {
                                echo '<li class="nav-item has-treeview ' . $menuOpenClass . '">';
                                echo '  <a href="#" class="nav-link ' . $activeClass . '">';
                                echo '    <i class="nav-icon ' . htmlspecialchars($item['icon']) . '"></i>';
                                echo '    <p>';
                                echo htmlspecialchars($item['nama_menu']);
                                echo '      <i class="right fas fa-angle-left"></i>';
                                echo '    </p>';
                                echo '  </a>';
                                echo '  <ul class="nav nav-treeview" ' . ($isOpen ? 'style="display: block;"' : '') . '>';
                                // Panggil diri sendiri untuk render anak (Submenu)
                                renderSidebarRecursive($item['children'], $current_mod, $current_act);
                                echo '  </ul>';
                                echo '</li>';
                            }

                            // 3. Tipe SINGLE LINK (Menu Biasa)
                            else {
                                if ($item['link'] !== '#') {
                                    echo '<li class="nav-item">';
                                    echo '  <a href="' . htmlspecialchars($item['link']) . '" class="nav-link ' . $activeClass . '">';
                                    echo '    <i class="nav-icon ' . htmlspecialchars($item['icon']) . '"></i>';
                                    echo '    <p>' . htmlspecialchars($item['nama_menu']) . '</p>';
                                    echo '  </a>';
                                    echo '</li>';
                                }
                            }
                        }
                    }
                }

                // --- EKSEKUSI RENDER ---
                renderSidebarRecursive($user_menu, $mod, $act);
                ?>

                <li class="nav-header">AKUN</li>
                <li class="nav-item">
                    <a href="index.php?mod=auth&act=logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>