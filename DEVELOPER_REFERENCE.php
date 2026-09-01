<?php
/**
 * DEVELOPER REFERENCE - Landing SMA Plus Al-Manshuriyah
 * 
 * Quick integration guide for developers modifying or extending the website
 */

// =========================================================
// 1. ROUTING REFERENCE
// =========================================================
// Location: /public/index.php (Line 545)
// Pattern: http://hostname/?mod=landing_sma&act=ACTION_NAME

$routing_reference = [
    'index' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'landing_sma_index($pdo)',
        'view' => 'app/views/landing_sma.php',
        'description' => 'Main homepage with 8 sections',
        'url' => '?mod=landing_sma&act=index'
    ],
    'guru_list' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'guru_list($pdo)',
        'view' => 'app/views/landing/guru_list.php',
        'description' => 'Display all active teachers/guru',
        'url' => '?mod=landing_sma&act=guru_list'
    ],
    'siswa_list' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'siswa_list($pdo)',
        'view' => 'app/views/landing/siswa_list.php',
        'description' => 'Display students with class filter',
        'url' => '?mod=landing_sma&act=siswa_list'
    ],
    'ekstrakurikuler_list' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'ekstrakurikuler_list($pdo)',
        'view' => 'app/views/landing/ekstrakurikuler_list.php',
        'description' => 'Display extracurricular activities grid',
        'url' => '?mod=landing_sma&act=ekstrakurikuler_list'
    ],
    'ekstrakurikuler_detail' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'ekstrakurikuler_detail($pdo)',
        'view' => 'app/views/landing/ekstrakurikuler_detail.php (if exists)',
        'description' => 'Display single ekstrakurikuler detail',
        'url' => '?mod=landing_sma&act=ekstrakurikuler_detail&id=1',
        'params' => ['id' => 'ekstrakurikuler ID']
    ],
    'video_list' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'video_list($pdo)',
        'view' => 'app/views/landing/video_list.php',
        'description' => 'Display video gallery with category filter',
        'url' => '?mod=landing_sma&act=video_list'
    ],
    'informasi_list' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'informasi_list($pdo)',
        'view' => 'app/views/landing/informasi_list.php',
        'description' => 'Display announcements/news with category filter',
        'url' => '?mod=landing_sma&act=informasi_list'
    ],
    'informasi_detail' => [
        'file' => 'app/controllers/LandingControllerSMA.php',
        'function' => 'informasi_detail($pdo)',
        'view' => 'app/views/landing/informasi_detail.php (if exists)',
        'description' => 'Display single announcement detail',
        'url' => '?mod=landing_sma&act=informasi_detail&id=1',
        'params' => ['id' => 'informasi ID']
    ]
];

// =========================================================
// 2. DATABASE TABLES REFERENCE
// =========================================================

$database_tables = [
    'landing_programs' => [
        'fields' => ['id', 'title', 'description', 'image', 'icon', 'order_display', 'is_active'],
        'sample_data' => 3,
        'purpose' => 'Program Unggulan (Program Akselerasi, Bilingual, STEM)',
        'query' => 'SELECT * FROM landing_programs WHERE is_active = 1 ORDER BY order_display'
    ],
    'landing_ekstrakurikuler' => [
        'fields' => ['id', 'nama', 'deskripsi', 'icon', 'gambar', 'pembina', 'jumlah_siswa', 'jadwal', 'lokasi', 'is_active', 'display_order'],
        'sample_data' => 5,
        'purpose' => 'Ekstrakurikuler (Pramuka, Musik, Fotografi, Basket, Debat)',
        'query' => 'SELECT * FROM landing_ekstrakurikuler WHERE is_active = 1 ORDER BY display_order'
    ],
    'landing_guru_profil' => [
        'fields' => ['id', 'id_guru', 'nama', 'jabatan', 'nip', 'pendidikan_terakhir', 'bidang_studi', 'foto', 'email', 'no_hp', 'pengalaman_tahun', 'sertifikasi', 'is_display'],
        'sample_data' => 0,
        'purpose' => 'Teacher profiles for guru_list page',
        'query' => 'SELECT * FROM landing_guru_profil WHERE is_display = 1'
    ],
    'landing_siswa_profil' => [
        'fields' => ['id', 'id_siswa', 'nama', 'kelas', 'no_induk', 'foto', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'alamat', 'no_hp', 'email', 'prestasi', 'is_display'],
        'sample_data' => 0,
        'purpose' => 'Student profiles for siswa_list page',
        'query' => 'SELECT * FROM landing_siswa_profil WHERE is_display = 1 ORDER BY kelas, nama'
    ],
    'landing_video' => [
        'fields' => ['id', 'judul', 'deskripsi', 'video_url', 'thumbnail', 'tipe', 'kategori', 'durasi', 'is_featured', 'is_active', 'view_count', 'display_order'],
        'sample_data' => 3,
        'purpose' => 'Videos (Profil, Aktivitas, Alumni)',
        'query' => 'SELECT * FROM landing_video WHERE is_active = 1 ORDER BY display_order'
    ],
    'landing_informasi' => [
        'fields' => ['id', 'judul', 'konten', 'kategori', 'icon', 'gambar', 'tanggal_publikasi', 'link_eksternal', 'is_featured', 'is_active', 'display_order'],
        'sample_data' => 3,
        'purpose' => 'Announcements/News (PPDB, Libur, Workshop)',
        'query' => 'SELECT * FROM landing_informasi WHERE is_active = 1 ORDER BY display_order'
    ]
];

// =========================================================
// 3. COLOR SCHEME & DESIGN TOKENS
// =========================================================

$design_tokens = [
    'colors' => [
        'primary' => '#2d5016',      // Hijau (main color)
        'secondary' => '#f39c12',    // Emas (accent)
        'accent' => '#e74c3c',       // Merah (highlight)
        'light' => '#f8f9fa',        // Light gray
        'dark' => '#343a40'          // Dark gray
    ],
    'fonts' => [
        'primary' => 'Poppins',
        'secondary' => 'Plus Jakarta Sans',
        'source' => 'Google Fonts CDN'
    ],
    'frameworks' => [
        'css' => 'Bootstrap 5.3.0 (CDN)',
        'icons' => 'Font Awesome 6.5.1 (CDN)',
        'backend' => 'PHP with PDO'
    ]
];

// =========================================================
// 4. COMMON SQL QUERIES
// =========================================================

$common_queries = [
    'get_all_programs' => "SELECT * FROM landing_programs WHERE is_active = 1 ORDER BY order_display ASC",
    'get_ekskul_by_id' => "SELECT * FROM landing_ekstrakurikuler WHERE id = ? AND is_active = 1",
    'get_all_videos' => "SELECT * FROM landing_video WHERE is_active = 1 ORDER BY display_order ASC",
    'get_videos_by_category' => "SELECT * FROM landing_video WHERE is_active = 1 AND kategori = ? ORDER BY display_order ASC",
    'get_all_informasi' => "SELECT * FROM landing_informasi WHERE is_active = 1 ORDER BY display_order ASC",
    'get_informasi_by_id' => "SELECT * FROM landing_informasi WHERE id = ? AND is_active = 1",
    'search_informasi' => "SELECT * FROM landing_informasi WHERE is_active = 1 AND (judul LIKE ? OR konten LIKE ?) ORDER BY tanggal_publikasi DESC",
    'count_active_content' => "SELECT 
        (SELECT COUNT(*) FROM landing_programs WHERE is_active = 1) as programs,
        (SELECT COUNT(*) FROM landing_ekstrakurikuler WHERE is_active = 1) as ekskul,
        (SELECT COUNT(*) FROM landing_video WHERE is_active = 1) as videos,
        (SELECT COUNT(*) FROM landing_informasi WHERE is_active = 1) as informasi"
];

// =========================================================
// 5. ADDING NEW FEATURES
// =========================================================

$development_guide = [
    'add_new_page' => [
        'step_1' => 'Create new function in app/controllers/LandingControllerSMA.php',
        'step_2' => 'Create new view file in app/views/landing/page_name.php',
        'step_3' => 'Add case statement in public/index.php routing',
        'step_4' => 'Query database tables as needed',
        'example' => 'function my_page($pdo) { require_once "app/views/landing/my_page.php"; }'
    ],
    'add_new_table' => [
        'step_1' => 'Create SQL migration file in database/ folder',
        'step_2' => 'Run migration: mysql -u user -p db_name < database/migration.sql',
        'step_3' => 'Query new table in controller function',
        'naming' => 'Follow naming convention: landing_[content_type]'
    ],
    'customize_colors' => [
        'file' => 'app/views/landing_sma.php',
        'location' => 'Lines 20-30 (CSS :root variables)',
        'primary' => '--primary-color: #2d5016;',
        'secondary' => '--secondary-color: #f39c12;',
        'accent' => '--accent-color: #e74c3c;'
    ]
];

// =========================================================
// 6. TESTING COMMANDS
// =========================================================

$testing_commands = [
    'check_syntax' => 'php -l app/controllers/LandingControllerSMA.php',
    'test_database' => 'mysql -u administrator -p20247166 db_simaks -e "SELECT COUNT(*) FROM landing_programs"',
    'check_routing' => 'grep -n "case \'landing_sma" public/index.php',
    'verify_files' => 'ls -lh app/controllers/LandingControllerSMA.php app/views/landing_sma.php'
];

// =========================================================
// 7. PERFORMANCE OPTIMIZATION
// =========================================================

$performance_tips = [
    'caching' => 'Consider implementing Redis/Memcached for landing page data',
    'cdn' => 'Bootstrap, Font Awesome, and Google Fonts are already on CDN',
    'images' => 'Optimize image size: use JPG for photos, PNG for graphics',
    'lazy_loading' => 'Add loading="lazy" to image tags for faster load time',
    'database' => 'Add indexes on frequently queried columns: is_active, display_order'
];

// =========================================================
// 8. SECURITY CONSIDERATIONS
// =========================================================

$security_notes = [
    'prepared_statements' => 'Always use prepared statements to prevent SQL injection',
    'output_escaping' => 'Escape all user-controlled output with htmlspecialchars()',
    'input_validation' => 'Validate GET/POST parameters before using in queries',
    'permissions' => 'Set file permissions: chmod 644 for PHP files, 755 for directories',
    'example' => '$stmt = $pdo->prepare("SELECT * FROM table WHERE id = ?"); $stmt->execute([$id]);'
];

?>
<!-- 
USAGE:
This file is a reference guide for developers. It's not meant to be executed.
Instead, view source code or reference this file when:
- Adding new pages to the landing site
- Modifying database queries
- Customizing design colors
- Understanding the routing system
- Performing maintenance or updates
-->
