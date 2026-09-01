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
            if ($key === 'mod' || $key === 'act') continue;
            
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
        padding-right: 0 !important; /* [FIX] Hapus padding kanan container */
        padding-left: 0 !important; /* [FIX] Hapus padding kiri juga biar imbang/custom */
    }
    
    .nav-sidebar {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }

    /* ============================================================
       [DYNAMIC THEME] SIDEBAR STYLE FROM DATABASE
       Generated in header.php ($sidebar_style_css)
       ============================================================ */
    .main-sidebar {
        <?= $sidebar_style_css ?? '' ?>
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
    .brand-link {
        background: transparent !important; /* Ensure gradient shows through */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important; /* Subtle separator */
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
    
    .nav-sidebar .nav-link {
        display: flex !important; 
        align-items: center !important; 
        
        padding-top: 4px !important; /* [FIX] Lebih rapat lagi (4px) */
        padding-bottom: 4px !important; 
        padding-left: 15px !important; 
        
        /* [FIX] Jarak Kanan POSITIF 5px sesuai request */
        margin-right: 5px !important; 
        margin-left: 10px !important; 
        
        width: calc(100% - 15px) !important; /* Sesuaikan width (10px kiri + 5px kanan = 15px) */
        border-radius: 30px 0 0 30px; 
        transition: all 0.3s;
        min-height: 32px; /* [FIX] Kurangi tinggi minimum agar bisa lebih rapat */
    }

    .nav-sidebar .nav-link p {
        font-size: 0.8rem !important; /* Enforce smaller text */
        letter-spacing: -0.3px;
        line-height: 1.1; 
        white-space: normal;
        margin-bottom: 0 !important; 
        margin-top: 0 !important;
        flex: 1; 
    }

    /* Icon menu juga harus konsisten */
    .nav-sidebar .nav-link .nav-icon {
         margin-right: 0.4rem;
         font-size: 0.9rem; /* Perkecil icon sedikit */
         text-align: center;
         min-width: 1.6rem;
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
        margin-top: 4px; /* Rapatkan header juga */
        margin-bottom: 0px; 
    }

    /* Paksa OverlayScrollbars agar tidak memberi padding kanan pada list menu */
    .os-padding {
        z-index: 15; /* Pastikan di atas */
    }
    .os-viewport {
        overflow-y: auto !important;
    }
</style>

<aside class="main-sidebar elevation-4">

    <a href="index.php?mod=dashboard"
        class="brand-link brand-link-custom d-flex flex-column align-items-center text-center py-3">
        <img src="assets/img/logoapk.png" alt="Logo" class="brand-image-custom img-circle elevation-3 mb-2"
            style="float: none !important; margin-right: 0 !important; max-height: 50px !important;">
        <span class="brand-text font-weight-bold d-block h5 mb-0">SIMAKS</span>
        <span class="brand-text-small d-block text-wrap px-2" style="font-size: 0.7rem; line-height: 1.2;">Sistem
            Informasi Manajemen Akademik Sekolah</span>
    </a>

    <div class="sidebar">
        <!-- Script Restore Scroll Position (Immediate) -->
        <script>
            (function() {
                var sidebar = document.querySelector('.main-sidebar .sidebar');
                if (sidebar) {
                    // 1. Restore Logic
                    var savedPos = localStorage.getItem('sidebarScrollPos');
                    if (savedPos) {
                        sidebar.scrollTop = savedPos;
                    }
                    // 2. Save Logic
                    sidebar.addEventListener('scroll', function() {
                        localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
                    });
                }
            })();
        </script>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <?php $active = ($mod === 'dashboard') ? 'active' : ''; ?>
                    <a href="index.php?mod=dashboard" class="nav-link <?= $active ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php foreach ($user_menu as $parent): ?>
                    <?php
                    // Lewati Dashboard ID 1 karena sudah dibuat manual di atas
                    if ($parent['id_menu'] == 1)
                        continue;

                    // Cek Anak Menu
                    $has_sub = !empty($parent['children']);
                    $active_parent = false;
                    $open_class = '';

                    // Cek Active State (Looping ke anak-anaknya)
                    if ($has_sub) {
                        foreach ($parent['children'] as $sub) {
                            if (is_menu_active_dynamic($sub['link'], $mod, $act)) {
                                $active_parent = true;
                                $open_class = 'menu-open';
                                break;
                            }
                        }
                    } else {
                        // Cek Active State untuk Single Link
                        $active_parent = is_menu_active_dynamic($parent['link'], $mod, $act);
                    }

                    $active_class = $active_parent ? 'active' : '';

                    // --- RENDER MENU --- //
                
                    // A. Tipe Header/Divider
                    if ($parent['link'] === '#' && strtoupper($parent['nama_menu']) === $parent['nama_menu']):
                        echo '<li class="nav-header">' . htmlspecialchars($parent['nama_menu']) . '</li>';
                        continue;
                    endif;

                    // B. Tipe Treeview (Parent dengan Submenu)
                    if ($has_sub):
                        ?>
                        <li class="nav-item has-treeview <?= $open_class ?>">
                            <a href="#" class="nav-link <?= $active_class ?>">
                                <i class="nav-icon <?= htmlspecialchars($parent['icon']) ?>"></i>
                                <p>
                                    <?= htmlspecialchars($parent['nama_menu']) ?>
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" <?= $open_class === 'menu-open' ? 'style="display: block;"' : '' ?>>
                                <?php foreach ($parent['children'] as $submenu): ?>
                                    <?php
                                    $active_sub = is_menu_active_dynamic($submenu['link'], $mod, $act) ? 'active' : '';
                                    ?>
                                    <li class="nav-item">
                                        <a href="<?= htmlspecialchars($submenu['link']) ?>" class="nav-link <?= $active_sub ?>">
                                            <i class="<?= htmlspecialchars($submenu['icon']) ?> nav-icon"></i>
                                            <p><?= htmlspecialchars($submenu['nama_menu']) ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                    <?php
                        // C. Tipe Single Link (Parent tanpa Submenu)
                    elseif (!$has_sub && $parent['link'] !== '#'):
                        ?>
                        <li class="nav-item">
                            <a href="<?= htmlspecialchars($parent['link']) ?>" class="nav-link <?= $active_class ?>">
                                <i class="nav-icon <?= htmlspecialchars($parent['icon']) ?>"></i>
                                <p><?= htmlspecialchars($parent['nama_menu']) ?></p>
                            </a>
                        </li>
                    <?php endif; ?>

                <?php endforeach; ?>

                <?php if (function_exists('is_cbt_enabled') && is_cbt_enabled()): ?>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(cbt_base_url()) ?>" class="nav-link" target="_blank">
                            <i class="nav-icon fas fa-laptop-code"></i>
                            <p>CBT</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">KONFIGURASI</li>
                <li class="nav-item">
                    <a href="index.php?mod=tema" class="nav-link <?= ($mod == 'tema') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-palette"></i>
                        <p>Pengaturan Tampilan</p>
                    </a>
                </li>

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