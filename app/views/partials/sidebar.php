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
            $extracted_menus = [];
            foreach ($user_menu as &$m) {
                if ($m['nama_menu'] == 'Penugasan Guru')
                    $m['nama_menu'] = 'Penugasan GTK';
                
                // [NEW] Jika ini menu Guru dan user adalah guru (bukan Admin/TU), arahkan langsung ke profilnya
                $is_staff = in_array('Admin', $_SESSION['roles'] ?? []) || in_array('TU', $_SESSION['roles'] ?? []);
                
                if (($m['nama_menu'] == 'Guru' || $m['link'] === 'guru' || $m['link'] === BASE_URL . 'guru') && isset($_SESSION['id_guru_terkait']) && $_SESSION['id_guru_terkait'] > 0 && !$is_staff) {
                    $m['nama_menu'] = 'Profil Saya';
                    $m['link'] = 'profil_guru/detail?id=' . $_SESSION['id_guru_terkait'];
                    $m['icon'] = 'fas fa-user-circle';
                }

                if (!empty($m['children'])) {
                    $new_children = [];
                    foreach ($m['children'] as &$c) {
                        if ($c['nama_menu'] == 'Penugasan Guru')
                            $c['nama_menu'] = 'Penugasan GTK';
                        
                        if (($c['nama_menu'] == 'Guru' || $c['link'] === 'guru' || $c['link'] === BASE_URL . 'guru') && isset($_SESSION['id_guru_terkait']) && $_SESSION['id_guru_terkait'] > 0 && !$is_staff) {
                            $c['nama_menu'] = 'Profil Saya';
                            $c['link'] = 'profil_guru/detail?id=' . $_SESSION['id_guru_terkait'];
                            $c['icon'] = 'fas fa-user-circle';
                            $extracted_menus[] = $c;
                            continue;
                        }
                        $new_children[] = $c;
                    }
                    $m['children'] = $new_children;
                }
            }
            unset($m);
            
            // Masukkan menu yang diekstrak (Profil Saya) ke posisi paling atas (setelah Dashboard)
            if (!empty($extracted_menus)) {
                $user_menu = array_merge($extracted_menus, $user_menu);
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

        $path = trim($url_parts['path'] ?? '', '/');
        // Hilangkan string BASE_URL jika ada (misal menu link mengandung /simaks/)
        if (defined('BASE_URL') && BASE_URL !== '/') {
            $base_url_trim = trim(BASE_URL, '/');
            if (strpos($path, $base_url_trim) === 0) {
                $path = trim(substr($path, strlen($base_url_trim)), '/');
            }
        }
        $segments = explode('/', $path);

        $target_mod = $query_params['mod'] ?? ($segments[0] ?: '');
        $target_act = $query_params['act'] ?? ($segments[1] ?? 'index');

        // 1. Cek mod
        if ($target_mod !== $current_mod) {
            return false;
        }

        // 2. Cek act
        if ($target_act !== $current_act) {
            return false;
        }

        // [FIX] Ekstrak parameter dari segmen Clean URL (mirip public/index.php)
        if (isset($segments[2]) && !empty($segments[2])) {
            $mod_seg = $segments[0] ?? '';
            $extra   = $segments[2];

            $segment3_map = [
                'tugas_tambahan'     => 'jenis',
                'ekskul'             => 'id',
                'kokulikuler'        => 'id',
                'pembiasaan'         => 'id',
                'kewirausahaan'      => 'id',
                'tahfidz'            => 'id',
                'rekap_nilai'        => 'tab',
                'cetak_rapor'        => 'tab',
                'cp_tp'              => 'tab',
                'jadwal'             => 'tab',
                'input_nilai'        => 'tab',
                'absensi_mapel'      => 'tab',
                'lms'                => 'tab',
                'profil_guru'        => 'id',
                'profil_siswa'       => 'id',
                'siswa'              => 'id',
                'guru'               => 'id',
                'kelas'              => 'id',
                'keuangan_dashboard' => 'tab',
            ];

            $param_key = $segment3_map[$mod_seg] ?? 'param';
            if (!isset($query_params[$param_key])) {
                $query_params[$param_key] = $extra;
            }

            if (isset($segments[3]) && !empty($segments[3])) {
                if ($param_key === 'id' && !isset($query_params['tab'])) {
                    $query_params['tab'] = $segments[3];
                } elseif ($param_key !== 'id' && !isset($query_params['id'])) {
                    $query_params['id'] = $segments[3];
                }
            }
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

    <a href="<?= BASE_URL ?>dashboard"
        class="brand-link brand-link-custom d-flex flex-column align-items-center text-center py-3 w-100">
        <img src="<?= BASE_URL ?>assets/img/logoapk.png" alt="Logo" class="brand-image-custom img-circle elevation-3 mb-2"
            style="float: none !important; margin-right: 0 !important; max-height: 55px !important; width: auto !important;">
        <span class="brand-text font-weight-bold d-block h5 mb-0" style="color: #fff !important;">SIMAKS</span>
        <span class="brand-text-small d-block text-wrap px-3"
            style="font-size: 0.75rem; line-height: 1.3; color: rgba(255,255,255,0.85) !important;">
            Sistem Informasi Manajemen Akademik Sekolah
        </span>
    </a>

    <div class="sidebar">
        <!-- Script Restore Scroll Position: Instan Tanpa Kedip / Gerakan -->
        <script>
            (function () {
                var sidebar = document.querySelector('.main-sidebar .sidebar');
                if (sidebar) {
                    var savedPos = localStorage.getItem('sidebarScrollPos');
                    if (savedPos !== null) {
                        sidebar.scrollTop = parseInt(savedPos, 10);
                    }
                }
            })();
        </script>

                <?php
                // ============================================================
                // DETEKSI: apakah user ini murni siswa (bukan admin/guru)?
                // ============================================================
                $is_pure_siswa_sidebar = in_array('Siswa', $_SESSION['roles'] ?? [])
                    && !in_array('Admin', $_SESSION['roles'] ?? [])
                    && !in_array('Guru', $_SESSION['roles'] ?? []);
                ?>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column <?= $is_pure_siswa_sidebar ? 'siswa-nav-manual' : '' ?>" <?= $is_pure_siswa_sidebar ? '' : 'data-widget="treeview"' ?> role="menu" data-accordion="false"
                data-animation-speed="300">

                <?php
                if ($is_pure_siswa_sidebar) {
                    // Bersihkan $user_menu dari semua item LMS agar tidak duplikat dengan menu manual di bawah
                    $lms_indicators = ['lms', 'materi', 'tugas', 'kuis', 'ujian'];
                    $clean_menu = [];
                    foreach ($user_menu as $m) {
                        $link_lower = strtolower($m['link'] ?? '');
                        $name_lower = strtolower($m['nama_menu'] ?? '');
                        
                        $is_lms = false;
                        foreach ($lms_indicators as $ind) {
                            if (strpos($link_lower, $ind) !== false || strpos($name_lower, $ind) !== false) {
                                $is_lms = true;
                                break;
                            }
                        }
                        
                        if (!$is_lms) $clean_menu[] = $m;
                    }
                    $user_menu = $clean_menu;
                }
                ?>

                <?php if ($is_pure_siswa_sidebar): ?>

                    <?php
                    // Variabel aktif & helpers
                    $act_now  = $act ?? '';
                    $id_siswa_aktif = $_SESSION['id_siswa_terkait'] ?? 0;
                    
                    // --- 1. UTAMA ---
                    ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>siswa_portal/dashboard" class="nav-link <?= ($mod === 'siswa_portal' && in_array($act_now, ['', 'index', 'dashboard'])) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-home text-primary"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>profil_siswa/detail?id=<?= $id_siswa_aktif ?>" class="nav-link <?= ($mod === 'profil_siswa') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user text-info"></i>
                            <p>Profil Saya</p>
                        </a>
                    </li>

                    <?php
                    // --- 2. AKADEMIK ---
                    $akademik_items = [
                        ['link' => BASE_URL . 'siswa_portal/jadwal',     'icon' => 'fas fa-calendar-alt', 'label' => 'Jadwal Pelajaran'],
                        ['link' => BASE_URL . 'siswa_portal/materi',     'icon' => 'fas fa-book-open',    'label' => 'Materi Pembelajaran'],
                        ['link' => BASE_URL . 'siswa_portal/tugas',      'icon' => 'fas fa-tasks',        'label' => 'Penugasan Mandiri'],
                        ['link' => BASE_URL . 'siswa_portal/cbt',        'icon' => 'fas fa-laptop-code text-danger', 'label' => 'Ujian Online (CBT)'],
                        ['link' => BASE_URL . 'siswa_portal/nilai',      'icon' => 'fas fa-chart-bar',    'label' => 'Nilai Saya'],
                        ['link' => BASE_URL . 'siswa_portal/kalender',   'icon' => 'fas fa-calendar-week','label' => 'Kalender Akademik'],
                    ];
                    // Define which actions belong to the Akademik treeview
                    $akademik_acts = ['jadwal', 'materi', 'tugas', 'cbt', 'cbt_kerjakan', 'cbt_room', 'nilai', 'kalender'];
                    $is_akademik_open = ($mod === 'siswa_portal' && in_array($act_now, $akademik_acts));
                    ?>

                    <li class="nav-item">
                        <a href="#collapseAkademik" class="nav-link" data-toggle="collapse" role="button" aria-expanded="<?= $is_akademik_open ? 'true' : 'false' ?>" aria-controls="collapseAkademik">
                            <i class="nav-icon fas fa-graduation-cap text-success"></i>
                            <p>Akademik <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <div class="collapse <?= $is_akademik_open ? 'show' : '' ?>" id="collapseAkademik">
                            <ul class="nav nav-treeview" style="display: block; padding-left: 15px;">
                            <?php foreach ($akademik_items as $li): ?>
                            <li class="nav-item">
                                <a href="<?= htmlspecialchars($li['link']) ?>" class="nav-link <?= is_menu_active_dynamic($li['link'], $mod, $act_now) ? 'active' : '' ?>">
                                    <i class="nav-icon <?= $li['icon'] ?>"></i>
                                    <p><?= $li['label'] ?></p>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>

                    <?php
                    // --- 3. KEHADIRAN ---
                    $is_kehadiran_open = ($mod === 'siswa_portal' && in_array($act_now, ['absensi', 'permohonan']));
                    $absen_kelas_active = ($mod === 'siswa_portal' && $act_now === 'absensi' && ($_GET['tab'] ?? 'kelas') === 'kelas') ? 'active' : '';
                    $absen_mapel_active = ($mod === 'siswa_portal' && $act_now === 'absensi' && ($_GET['tab'] ?? '') === 'mapel') ? 'active' : '';
                    $permohonan_active = ($mod === 'siswa_portal' && $act_now === 'permohonan') ? 'active' : '';
                    ?>

                    <li class="nav-item">
                        <a href="#collapseKehadiran" class="nav-link" data-toggle="collapse" role="button" aria-expanded="<?= $is_kehadiran_open ? 'true' : 'false' ?>" aria-controls="collapseKehadiran">
                            <i class="nav-icon fas fa-clipboard-check text-info"></i>
                            <p>Kehadiran <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <div class="collapse <?= $is_kehadiran_open ? 'show' : '' ?>" id="collapseKehadiran">
                            <ul class="nav nav-treeview" style="display: block; padding-left: 15px;">
                                <li class="nav-item">
                                    <a href="<?= BASE_URL ?>siswa_portal/absensi?tab=kelas" class="nav-link <?= $absen_kelas_active ?>">
                                        <i class="nav-icon fas fa-school"></i>
                                        <p>Absensi Kelas (Piket)</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= BASE_URL ?>siswa_portal/absensi?tab=mapel" class="nav-link <?= $absen_mapel_active ?>">
                                        <i class="nav-icon fas fa-book"></i>
                                        <p>Absensi Mapel</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= BASE_URL ?>siswa_portal/permohonan" class="nav-link <?= $permohonan_active ?>">
                                        <i class="nav-icon fas fa-file-medical text-warning"></i>
                                        <p>Pengajuan Izin/Sakit</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <?php
                    // --- 4. PENGEMBANGAN KARAKTER (TREEVIEW HALAMAN MANDIRI) ---
                    $karakter_acts = ['pembiasaan', 'tahfidz', 'ekskul', 'kokulikuler', 'kewirausahaan', 'progress'];
                    $is_karakter_open = ($mod === 'siswa_portal' && in_array($act_now, $karakter_acts));
                    $tab_karakter_now = $_GET['tab'] ?? '';
                    $karakter_items = [
                        ['act' => 'pembiasaan',    'icon' => 'fas fa-praying-hands text-success', 'label' => 'Pembiasaan Ibadah'],
                        ['act' => 'tahfidz',       'icon' => 'fas fa-quran text-primary',         'label' => 'Tahfidz Al-Qur\'an'],
                        ['act' => 'ekskul',        'icon' => 'fas fa-futbol text-warning',        'label' => 'Ekstrakurikuler'],
                        ['act' => 'kokulikuler',   'icon' => 'fas fa-shapes text-info',           'label' => 'Kokurikuler'],
                        ['act' => 'kewirausahaan', 'icon' => 'fas fa-store text-danger',          'label' => 'Kewirausahaan'],
                    ];
                    ?>

                    <li class="nav-item">
                        <a href="#collapseKarakter" class="nav-link" data-toggle="collapse" role="button" aria-expanded="<?= $is_karakter_open ? 'true' : 'false' ?>" aria-controls="collapseKarakter">
                            <i class="nav-icon fas fa-star text-warning"></i>
                            <p>Pengembangan Karakter <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <div class="collapse <?= $is_karakter_open ? 'show' : '' ?>" id="collapseKarakter">
                            <ul class="nav nav-treeview" style="display: block; padding-left: 15px;">
                                <?php foreach ($karakter_items as $ki): ?>
                                <?php 
                                    $is_item_active = ($mod === 'siswa_portal' && ($act_now === $ki['act'] || ($act_now === 'progress' && $tab_karakter_now === $ki['act'])));
                                ?>
                                <li class="nav-item">
                                    <a href="<?= BASE_URL ?>siswa_portal/<?= $ki['act'] ?>" class="nav-link <?= $is_item_active ? 'active' : '' ?>">
                                        <i class="nav-icon <?= $ki['icon'] ?>"></i>
                                        <p><?= $ki['label'] ?></p>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>

                    <?php
                    // --- 5. KEUANGAN ---
                    $tagihan_active = ($mod === 'siswa_portal' && $act_now === 'tagihan') ? 'active' : '';
                    ?>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>siswa_portal/tagihan" class="nav-link <?= $tagihan_active ?>">
                            <i class="nav-icon fas fa-file-invoice-dollar text-danger"></i>
                            <p>Tagihan SPP</p>
                        </a>
                    </li>

                    <?php
                    // --- 6. LAINNYA ---
                    $chat_active = ($mod === 'chat') ? 'active' : '';
                    ?>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>chat" class="nav-link <?= $chat_active ?>">
                            <i class="nav-icon fas fa-comments text-secondary"></i>
                            <p>Pesan</p>
                        </a>
                    </li>

                <?php else: ?>
                    <?php // Non-siswa: Dashboard standar ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>dashboard" class="nav-link <?= ($mod === 'dashboard') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                <?php endif; ?>





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
                                echo '<li class="nav-item ' . $menuOpenClass . '">';
                                echo '  <a href="#" class="nav-link ' . $activeClass . '">';
                                echo '    <i class="nav-icon ' . htmlspecialchars($item['icon']) . '"></i>';
                                echo '    <p>';
                                echo htmlspecialchars($item['nama_menu']);
                                echo '      <i class="right fas fa-angle-left"></i>';
                                echo '    </p>';
                                echo '  </a>';
                                echo '  <ul class="nav nav-treeview" ' . ($isOpen ? 'style="display: block;"' : 'style="display: none;"') . '>';
                                // Panggil diri sendiri untuk render anak (Submenu)
                                renderSidebarRecursive($item['children'], $current_mod, $current_act);
                                echo '  </ul>';
                                echo '</li>';
                            }

                            // 3. Tipe SINGLE LINK (Menu Biasa)
                            else {
                                if ($item['link'] !== '#') {
                                    $final_link = $item['link'];
                                    if (strpos($final_link, 'http') !== 0 && strpos($final_link, '<?=') === false) {
                                        $final_link = BASE_URL . ltrim($final_link, '/');
                                    }
                                    
                                    echo '<li class="nav-item">';
                                    echo '  <a href="' . htmlspecialchars($final_link) . '" class="nav-link ' . $activeClass . '">';
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
                // Untuk siswa, $user_menu sudah bersih (LMS sudah dibuang di atas)
                renderSidebarRecursive($user_menu, $mod, $act);
                ?>

                <?php if (function_exists('is_cbt_enabled') && is_cbt_enabled()): ?>
                    <li class="nav-header">EKSTERNAL</li>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(cbt_base_url()) ?>" class="nav-link" target="_blank">
                            <i class="nav-icon fas fa-laptop-code"></i>
                            <p>CBT</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">AKUN</li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>auth/logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<script>
// [PERBAIKAN TOTAL] Handler Buka-Tutup Menu & Auto-Scroll Fokus Menu Aktif
$(document).ready(function () {
    // 1. Hapus listener default AdminLTE agar tidak terjadi benturan ganda (double-toggle)
    $('.nav-sidebar').removeAttr('data-widget');

    // 2. Pasang handler toggle yang mulus dan pasti merespons pada semua menu bertingkat
    $(document).off('click', '.nav-sidebar li.nav-item > a.nav-link');
    $(document).on('click', '.nav-sidebar li.nav-item > a.nav-link', function (e) {
        var $link = $(this);
        var $parent = $link.parent('li.nav-item');
        var $treeview = $link.next('ul.nav-treeview');

        // Hanya proses jika item ini adalah menu induk yang punya submenu
        if ($treeview.length > 0) {
            e.preventDefault();

            if ($parent.hasClass('menu-open') || $treeview.is(':visible')) {
                // TUTUP (Collapse)
                $treeview.stop(true, true).slideUp(180, function () {
                    $parent.removeClass('menu-open');
                    $treeview.css('display', 'none');
                });
            } else {
                // BUKA (Expand)
                $parent.addClass('menu-open');
                $treeview.stop(true, true).slideDown(180, function () {
                    $treeview.css('display', 'block');
                });
            }
        }
    });

    // 3. Catat posisi scroll saat pengguna menggulir atau mengklik menu
    var sidebarEl = document.querySelector('.main-sidebar .sidebar');
    if (sidebarEl) {
        sidebarEl.addEventListener('scroll', function () {
            localStorage.setItem('sidebarScrollPos', sidebarEl.scrollTop);
        }, { passive: true });
    }

    $(document).on('click', '.nav-sidebar a.nav-link:not([href="#"]):not([href^="javascript"])', function () {
        if (sidebarEl) {
            localStorage.setItem('sidebarScrollPos', sidebarEl.scrollTop);
        }
    });

    // Reset scroll ke atas jika mengklik Dashboard / Logo
    $(document).on('click', '.brand-link, .nav-sidebar a[href*="dashboard"]', function () {
        localStorage.setItem('sidebarScrollPos', '0');
    });
});
</script>
