<?php
// config/app.php - Application Configuration

return [
    // =======================================================
    // LANDING PAGE CONFIGURATION
    // =======================================================
    'landing_page' => [
        'enabled' => true,  // Set to false untuk langsung ke login
        'show_ppdb_link' => true,  // Tampilkan tombol PPDB di landing
        'show_gallery' => true,  // Tampilkan gallery foto
        'show_news' => true,  // Tampilkan berita & pengumuman
        'slider_autoplay' => true,  // Auto-play slider
        'slider_interval' => 5000,  // Interval slider (ms)
    ],

    // =======================================================
    // SCHOOL INFORMATION
    // =======================================================
    'school' => [
        'name' => 'SMA/SMK Negeri/Swasta',  // Will be overridden by DB
        'short_name' => 'SMAN',
        'tagline' => 'Menuju Generasi Unggul dan Berakhlak Mulia',
        'address' => '',
        'phone' => '',
        'email' => '',
        'website' => '',
    ],

    // =======================================================
    // PPDB CONFIGURATION
    // =======================================================
    'ppdb' => [
        'enabled' => true,  // Enable/disable form PPDB public
        'year' => '2025/2026',  // Tahun ajaran PPDB
        'start_date' => '2025-01-01',
        'end_date' => '2025-06-30',
        'max_file_size' => 2048,  // KB (2MB)
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf'],
    ],

    // =======================================================
    // THEME & BRANDING
    // =======================================================
    'theme' => [
        'primary_color' => '#C41E3A',  // Merah dari logo
        'secondary_color' => '#2D8A4E',  // Hijau dari logo
        'accent_color' => '#FFD700',  // Gold accent
    ],

    // =======================================================
    // SEO SETTINGS
    // =======================================================
    'seo' => [
        'meta_title' => 'SIMAKS - Sistem Informasi Manajemen Akademik Sekolah',
        'meta_description' => 'Sistem Informasi Manajemen Akademik Sekolah yang modern dan terintegrasi',
        'meta_keywords' => 'sekolah, akademik, manajemen, ppdb, pendaftaran',
    ],
];
