<?php
// SIMAKS - Sistem Manajemen Akademik Sekolah

// Set Timezone
date_default_timezone_set('Asia/Jakarta');

// =========================================================
// ERROR HANDLING CONFIGURATION
// =========================================================
// PRODUCTION: Set to true and errors will be logged instead of displayed
// DEVELOPMENT: Keep as false for debugging
$is_production = false; // Change to true for production

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =========================================================
// SECURE SESSION COOKIE CONFIGURATION
// =========================================================
// Must be set BEFORE session_start()
session_set_cookie_params([
    'lifetime' => 0, // Until browser closes
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // HTTPS only
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Lax' // CSRF protection
]);

// Mulai sesi
session_start();

// =========================================================
// SECURITY HEADERS - Load after session_start
// =========================================================
require_once '../config/security_headers.php';

// Define BASE_URL constant for assets (dynamic)
// Detect directory where index.php is served to support both:
// - deploying the project with `public` as document root
// - deploying inside a subfolder (e.g., /simaks/public)
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$baseUrl = $scriptDir === '' ? '/' : $scriptDir . '/';
define('BASE_URL', $baseUrl);

// =========================================================
// PENTING: REQUIRE UTAMA
// =========================================================
// Load environment variables FIRST
require_once '../config/env.php';
// Then load database with those env vars
require_once '../config/db.php';
require_once '../config/helper.php';
require_once '../config/cache.php'; // [PERFORMANCE] Query caching
require_once '../config/csrf.php';  // [SECURITY] CSRF protection
require_once '../config/rate_limit.php'; // [SECURITY] Rate limiting
require_once '../config/input_validation.php'; // [SECURITY] Input validation
require_once '../config/secure_upload.php'; // [SECURITY] Secure file uploads
require_once '../vendor/autoload.php'; // Autoloader Composer

// =========================================================
// LOAD APP CONFIG
// =========================================================
$app_config = require '../config/app.php';

// =========================================================
// REQUIRE MODEL YANG DIBUTUHKAN SECARA GLOBAL
// =========================================================
require_once '../app/models/AppMenuModel.php';
require_once '../app/models/PenggunaModel.php';
require_once '../app/models/ProfilSekolahModel.php';

// =========================================================
// INJEKSI GLOBAL (Koneksi Database & Status Sesi)
// =========================================================
$pdo = connect_db();
if (!$pdo) {
    die("<h1>Koneksi Database Gagal</h1><p>Sistem tidak dapat terhubung ke database. Cek file config/db.php.</p>");
}

// [CBT] jika modul terpisah diaktifkan, sambungkan juga ke database CBT
if (function_exists('is_cbt_enabled') && is_cbt_enabled()) {
    if (function_exists('cbt_connect_db')) {
        try {
            $pdo_cbt = cbt_connect_db();
            $GLOBALS['pdo_cbt'] = $pdo_cbt;
        } catch (Exception $e) {
            error_log("CBT DB gagal: " . $e->getMessage());
        }
    }
}

// [BARU] Inisialisasi Logo Sekolah ke Session (Jika belum ada)
if (empty($_SESSION['app_logo'])) {
    $stmt_logo = $pdo->query("SELECT logo FROM profil_sekolah WHERE id = 1");
    $db_logo = $stmt_logo->fetchColumn();
    $_SESSION['app_logo'] = $db_logo;
}

// [BARU] Pastikan Tahun Ajaran Aktif Tersedia di Session
if (empty($_SESSION['id_ta_aktif'])) {
    $ta_aktif_db = $pdo->query("SELECT id_ta, nama_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    if ($ta_aktif_db) {
        $_SESSION['id_ta_aktif'] = $ta_aktif_db['id_ta'];
        $_SESSION['nama_ta_aktif'] = $ta_aktif_db['nama_ta'];
    } else {
        if (is_logged_in() && ($_GET['mod'] ?? '') != 'ta') {
            $_SESSION['pesan_error_global'] = "PERINGATAN: Sistem tidak mendeteksi Tahun Ajaran Aktif! Silakan atur di menu Data Master > Tahun Ajaran.";
        }
    }
}

// =========================================================
// SMART ROUTING - LANDING PAGE INTEGRATION & CLEAN URLS
// =========================================================
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (BASE_URL !== '/' && strpos($request_uri, BASE_URL) === 0) {
    $request_uri = substr($request_uri, strlen(BASE_URL));
}
$request_uri = trim($request_uri, '/');

if (!empty($request_uri) && $request_uri !== 'index.php') {
    $segments = explode('/', $request_uri);

    // Segment 1: mod
    if (!isset($_GET['mod']) && !empty($segments[0]) && $segments[0] !== 'index.php') {
        $_GET['mod'] = $segments[0];
    }
    // Segment 2: act
    if (!isset($_GET['act']) && isset($segments[1]) && !empty($segments[1]) && $segments[1] !== 'index.php') {
        $_GET['act'] = $segments[1];
    }
    // Segment 3: smart extra parameter mapping
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
            'siswa_portal'       => 'tab',
            'portal_siswa'       => 'tab',
            'profil_guru'        => 'id',
            'profil_siswa'       => 'id',
            'siswa'              => 'id',
            'guru'               => 'id',
            'kelas'              => 'id',
            'keuangan_dashboard' => 'tab',
            'uks'                => 'tab',
            'manajemen_uks'      => 'tab',
        ];

        $param_key = $segment3_map[$mod_seg] ?? 'param';
        if (!isset($_GET[$param_key])) {
            $_GET[$param_key] = $extra;
        }

        if (isset($segments[3]) && !empty($segments[3])) {
            if ($param_key === 'id' && !isset($_GET['tab'])) {
                $_GET['tab'] = $segments[3];
            } elseif ($param_key !== 'id' && !isset($_GET['id'])) {
                $_GET['id'] = $segments[3];
            }
        }
    }
}

// Ambil variabel mod dan act dari URL
if (!isset($_GET['mod'])) {
    if ($app_config['landing_page']['enabled'] && !is_logged_in()) {
        $mod = 'landing';
    } else if (is_logged_in()) {
        if (in_array('Siswa', $_SESSION['roles'] ?? []) && !in_array('Admin', $_SESSION['roles'] ?? []) && !in_array('Guru', $_SESSION['roles'] ?? [])) {
            redirect(BASE_URL . 'siswa_portal/dashboard');
        } else {
            redirect(BASE_URL . 'dashboard');
        }
        exit;
    } else {
        $mod = 'auth';
        $_GET['act'] = 'login';
    }
} else {
    $mod = $_GET['mod'];
}
$act = $_GET['act'] ?? 'index';

// Jika modul CBT diaktifkan dan permintaan menuju CBT (mod=cbt)
if (function_exists('is_cbt_enabled') && is_cbt_enabled() && $mod === 'cbt') {
    $query = $_SERVER['QUERY_STRING'];
    $params = [];
    parse_str($query, $params);
    unset($params['mod']);
    $qs = http_build_query($params);
    $url = cbt_base_url();
    if ($qs !== '') {
        $url .= '?' . $qs;
    }
    header('Location: ' . $url);
    exit;
}

// Cek login untuk semua halaman kecuali halaman otentikasi dan landing page
if ($mod !== 'auth' && $mod !== 'landing' && $mod !== 'landing_sma' && !is_logged_in()) {
    if ($mod === 'api') {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => 'Sesi telah berakhir. Silakan login kembali.', 'redirect' => BASE_URL . 'index.php?mod=auth&act=login']);
        exit;
    }
    if ($app_config['landing_page']['enabled']) {
        redirect(BASE_URL . 'landing');
    } else {
        redirect(BASE_URL . 'index.php?mod=auth&act=login');
    }
}

// ============================================================
// MUAT MENU UNTUK SIDEBAR SETELAH LOGIN TERVERIFIKASI
// ============================================================
$user_menu = [];
if (is_logged_in()) {
    $my_roles = $_SESSION['role_ids'] ?? [0];
    $user_menu = AppMenuModel::getUserMenu($pdo, $my_roles);

    // [PRESENCE] Update last activity
    $pdo->prepare("UPDATE pengguna SET last_activity = NOW() WHERE id_pengguna = ?")->execute([$_SESSION['user_id']]);
}

// =========================================================
// MODULAR ROUTE DISPATCHER (FASE 4 ARCHITECTURE)
// =========================================================
require_once '../config/routes.php';

try {
    dispatch_route($pdo, $mod, $act);
} catch (Throwable $e) {
    error_log("Global SIMAKS Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
    http_response_code(500);

    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($mod) && $mod === 'api')) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'msg' => 'Terjadi kendala sistem pada server.',
            'error' => ini_get('display_errors') ? $e->getMessage() : null
        ]);
        exit;
    }

    $errorMessage = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    $errorTrace = $e->getTraceAsString();

    $errorViewPath = __DIR__ . '/../app/views/errors/500.php';
    if (file_exists($errorViewPath)) {
        include $errorViewPath;
    } else {
        echo "<h1>Terjadi Kendala Sistem</h1><p>Silakan hubungi administrator.</p>";
    }
}
