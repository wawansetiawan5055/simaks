<?php
// helpers.php - Global Helper Functions

// 1. Mulai Session jika belum mulai
if (session_status() === PHP_SESSION_NONE) session_start();

// =========================================================================
// PERFORMANCE OPTIMIZATION - Session Management
// =========================================================================

/**
 * Tutup session setelah membaca data untuk mencegah blocking concurrent requests
 * PENTING: Panggil fungsi ini SEGERA SETELAH membaca data session yang dibutuhkan
 * 
 * Manfaat:
 * - User dapat membuka multiple tabs tanpa waiting
 * - Concurrent requests tidak saling blocking
 * - Response time berkurang 50% untuk multi-tab users
 * 
 * Cara Pakai:
 * 1. Baca semua session data yang diperlukan di awal controller
 * 2. Panggil close_session_early()
 * 3. Lanjutkan processing tanpa session lock
 * 
 * CATATAN: Setelah dipanggil, $_SESSION masih bisa dibaca tapi tidak bisa diubah
 */
function close_session_early() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
}

// =========================================================================


// 2. Load Model Hak Akses (Pastikan path sesuai struktur folder Anda)
// Asumsi: helpers.php ada di folder sejajar dengan folder models atau di root app
// Sesuaikan path require ini jika folder Anda berbeda.
require_once __DIR__ . '/../app/models/HakAksesModel.php'; 

// =========================================================================
// FUNGSI DASAR (LOGIN & REDIRECT)
// =========================================================================

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    $role_ids = $_SESSION['role_ids'] ?? [];
    return in_array(1, $role_ids);
}

function redirect($url) {
    if (!preg_match('#^https?://#i', $url)) {
        if (strpos($url, 'index.php?mod=') === 0 || strpos($url, 'index.php?') === 0 || strpos($url, '?mod=') === 0) {
            $query_part = parse_url($url, PHP_URL_QUERY) ?? '';
            parse_str($query_part, $queryParams);
            $mod = $queryParams['mod'] ?? '';
            $act = $queryParams['act'] ?? '';
            unset($queryParams['mod'], $queryParams['act']);

            if ($mod) {
                $clean_path = $mod . ($act ? '/' . $act : '');
                $query_str = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
                $url = (defined('BASE_URL') ? BASE_URL : '/') . $clean_path . $query_str;
            } else {
                $url = (defined('BASE_URL') ? BASE_URL : '/') . ltrim($url, '/');
            }
        } elseif (defined('BASE_URL') && strpos($url, BASE_URL) !== 0) {
            $url = BASE_URL . ltrim($url, '/');
        }
    }
    header("Location: $url"); 
    exit;
}

/**
 * Cek apakah modul CBT diaktifkan melalui environment variable.
 * Mengembalikan true jika ENABLE_CBT di-set ke "true" (case-insensitive).
 */
function is_cbt_enabled() {
    $val = getenv('ENABLE_CBT');
    return $val !== false && strtolower($val) === 'true';
}

/**
 * Helper untuk mengambil base URL CBT jika disetel, atau default kosong.
 */
function cbt_base_url() {
    // default path when environment variable is not provided.
    // changed to match typical installation under /simaks/cbt
    return getenv('CBT_BASE_URL') ?: '/simaks/cbt';
}

// =========================================================================
// FUNGSI RBAC LAMA (BACKWARD COMPATIBILITY)
// =========================================================================
// Kita pertahankan dulu agar kode yang masih pakai has_role() tidak error
// sampai semua diganti ke sistem baru.

function user_roles() {
    return $_SESSION['roles'] ?? [];
}

function has_role($roles) {
    foreach ((array)$roles as $role) {
        if (in_array($role, user_roles())) return true;
    }
    return false;
}

// =========================================================================
// FUNGSI RBAC BARU (DYNAMIC ACCESS CONTROL)
// =========================================================================

/**
 * [BARU] Middleware: Require akses ke modul tertentu, redirect jika tidak punya izin
 * Digunakan di awal controller untuk proteksi halaman.
 * 
 * @param string $mod_param Nama modul
 * @param string $act_param Nama action (opsional)
 * @param string $redirect_url URL redirect jika tidak punya akses (default: index.php)
 */
function require_access($mod_param, $act_param = 'index', $redirect_url = 'index.php') {
    if (!check_access($mod_param, $act_param)) {
        $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini.";
        redirect($redirect_url);
    }
}

/**
 * [BARU] Middleware: Require role tertentu, redirect jika tidak punya
 * 
 * @param array|string $required_roles Role yang diperlukan (array atau string)
 * @param string $redirect_url URL redirect jika tidak punya role
 */
function require_role($required_roles, $redirect_url = 'index.php') {
    $user_roles = user_roles();
    foreach ((array)$required_roles as $role) {
        if (in_array($role, $user_roles)) return; // OK
    }
    $_SESSION['error'] = "Role tidak sesuai untuk mengakses halaman ini.";
    redirect($redirect_url);
}

/**
 * Helper internal untuk mencari ID Menu yang cocok dengan modul & aksi saat ini
 * Mendukung Clean URL (e.g. 'siswa', 'lms/materi_list') dan Legacy Query String ('index.php?mod=siswa')
 */
function find_menu_id_smart($pdo, $mod_param, $act_param = 'index') {
    $user_role_ids = $_SESSION['role_ids'] ?? [];
    if (empty($user_role_ids)) return null;

    $placeholders = implode(',', array_fill(0, count($user_role_ids), '?'));
    $exact_mod = $mod_param;
    $exact_mod_act = ($act_param && $act_param !== 'index') ? "{$mod_param}/{$act_param}" : $mod_param;
    $mod_slash = "{$mod_param}/%";
    $mod_query = "%mod={$mod_param}%";

    $sql = "
        SELECT am.id_menu,
               (SELECT COUNT(*) FROM hak_akses ha WHERE ha.id_menu = am.id_menu AND ha.id_peran IN ({$placeholders}) AND ha.can_read = 1) as has_access
        FROM app_menu am
        WHERE am.status = 'Aktif' 
          AND (
             am.link = ?
          OR am.link = ?
          OR am.link LIKE ?
          OR am.link LIKE ?
          )
        ORDER BY 
            has_access DESC,
            (am.link = ?) DESC,
            (am.link = ?) DESC,
            CHAR_LENGTH(am.link) ASC
        LIMIT 1
    ";

    $params = array_merge(
        $user_role_ids,
        [$exact_mod, $exact_mod_act, $mod_slash, $mod_query],
        [$exact_mod_act, $exact_mod]
    );

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id_menu'] : null;
}

/**
 * [BARU] Cek apakah user memiliki izin akses ke modul tertentu (Dynamic RBAC)
 * Menggantikan has_role() yang hardcoded.
 * 
 * @param string $mod_param Nama modul dari parameter $_GET['mod'] (default: current mod)
 * @param string $act_param Nama action dari parameter $_GET['act'] (opsional)
 * @return bool True jika diizinkan
 */
function check_access($mod_param = null, $act_param = null) {
    global $pdo; // Pastikan koneksi DB tersedia dari scope global (index.php)

    // 1. Dapatkan Role ID User saat ini
    $user_role_ids = $_SESSION['role_ids'] ?? [];
    if (empty($user_role_ids)) return false;

    // [BYPASS UTAMA] Admin (ID 1) selalu punya akses ke semua modul
    if (in_array(1, $user_role_ids)) return true;

    // Default ke modul saat ini jika null
    if ($mod_param === null) $mod_param = $_GET['mod'] ?? 'dashboard';
    if ($act_param === null) $act_param = $_GET['act'] ?? 'index';

    // Dashboard & Portal Siswa default public untuk user logged-in
    if ($mod_param == 'dashboard' || $mod_param == 'siswa_portal') return true;

    // 2. Cari Menu ID berdasarkan Link (Smart Link Matching)
    $menu_id = find_menu_id_smart($pdo, $mod_param, $act_param);

    if (!$menu_id) {
        return false; // Default deny
    }

    return _check_permission_by_id($pdo, $menu_id, $user_role_ids);
}

// Fungsi Internal untuk query cek izin
function _check_permission_by_id($pdo, $menu_id, $role_ids) {
    if (empty($role_ids)) return false;
    
    $placeholders = implode(',', array_fill(0, count($role_ids), '?'));
    $sql = "SELECT COUNT(*) FROM hak_akses 
            WHERE id_menu = ? 
            AND id_peran IN ($placeholders) 
            AND can_read = 1"; // Asumsi kita cek Read access untuk view/controller index
            
    $params = array_merge([$menu_id], $role_ids);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchColumn() > 0;
}


/**
 * [BARU] Cek permission spesifik (Create/Update/Delete) untuk UI
 * Contoh: can_do($pdo, 'jadwal', 'delete')
 */
function can_do($pdo, $mod_param, $action_type) {
    // 1. Map action type ke kolom tabel (SESUAIKAN DENGAN TABLE hak_akses)
    $col_map = [
        'read' => 'can_read',
        'create' => 'can_create', // FIX: DB uses 'can_create', not 'can_add'
        'update' => 'can_update', // FIX: DB uses 'can_update', not 'can_edit'
        'delete' => 'can_delete'
    ];
    
    $db_col = $col_map[$action_type] ?? null;
    if (!$db_col) return false;

    // 2. Dapatkan Role ID
    $user_role_ids = $_SESSION['role_ids'] ?? [];
    if (empty($user_role_ids)) return false;
    
    // [BYPASS UTAMA] Admin (ID 1) bypass semua izin per-aksi
    if (in_array(1, $user_role_ids)) return true;

    if ($mod_param == 'dashboard' || $mod_param == 'siswa_portal') return true;

    // 3. Cari Menu ID
    $menu_id = find_menu_id_smart($pdo, $mod_param, $_GET['act'] ?? 'index');
    
    if (!$menu_id) return false;

    // 4. Cek Permission
    $placeholders = implode(',', array_fill(0, count($user_role_ids), '?'));
    
    $sql = "SELECT * FROM hak_akses WHERE id_menu = ? AND id_peran IN ($placeholders)";
    $params = array_merge([$menu_id], $user_role_ids);
    $stmt_check = $pdo->prepare($sql);
    $stmt_check->execute($params);
    $permissions = $stmt_check->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($permissions as $p) {
        // Jika salah satu role mengizinkan, return true (Cumulative permission)
        if (
            ($action_type == 'create' && !empty($p['can_create'])) ||
            ($action_type == 'update' && !empty($p['can_update'])) ||
            ($action_type == 'delete' && !empty($p['can_delete'])) ||
            ($action_type == 'read'   && !empty($p['can_read']))
        ) {
            return true;
        }
    }
    
    return false;
}

/**
 * Fungsi untuk mereset cache izin.
 */
function reset_permission_cache() {
    unset($_SESSION['permissions_cache']);
}

/**
 * [BARU] Mendapatkan URL Logo Sekolah secara Global
 * Mengambil dari Session (yang di-load saat login/startup) atau default.
 */
function get_app_logo() {
    $default_logo = 'assets/img/logo_sekolah.png';
    // Fallback jika file default tidak ada
    $backup_logo = 'assets/img/default_logo.png'; 
    
    // Cek Session
    if (!empty($_SESSION['app_logo'])) {
        $custom_logo = 'assets/img/' . $_SESSION['app_logo'];
        if (file_exists(__DIR__ . '/../public/' . $custom_logo)) {
            return $custom_logo;
        }
    }
    
    // Cek Default (logo_sekolah.png - biasanya logo bawaan app)
    if (file_exists(__DIR__ . '/../public/' . $default_logo)) {
        return $default_logo;
    }
    
    return $backup_logo;
}

/**
 * [BARU] Mendapatkan URL Foto Profil Pengguna
 * - Jika sudah ada foto kustom yang diupload -> kembalikan foto asli
 * - Jika belum -> kembalikan Avatar Resmi sesuai Peran (Guru/Staf vs Siswa) dan Jenis Kelamin (L/P):
 *   * Guru/Staf Perempuan: Avatar Batik PGRI Berhijab (assets/img/avatar-guru-female.jpg)
 *   * Guru/Staf Laki-laki: Avatar Batik PGRI Berpeci (assets/img/avatar-guru-male.jpg)
 *   * Siswa Perempuan: Avatar Seragam Siswi Berhijab (assets/img/avatar-female.jpg)
 *   * Siswa Laki-laki: Avatar Seragam Siswa Berdasi (assets/img/avatar-male.jpg)
 */
function get_user_photo($user_id = null, $custom_name = null, $gender = null, $user_type = null) {
    global $pdo;
    if (!$user_id && isset($_SESSION['user_id'])) $user_id = $_SESSION['user_id'];
    
    $jk = $gender ?? '';
    $type = $user_type ?? '';
    
    if ($user_id) {
        // Cek Session dulu jika user yang login
        if (isset($_SESSION['user_photo']) && $_SESSION['user_id'] == $user_id && !empty($_SESSION['user_photo'])) {
            $photo_path = 'assets/img/profil/' . $_SESSION['user_photo'];
            if (file_exists(__DIR__ . '/../public/' . $photo_path)) {
                return BASE_URL . $photo_path;
            }
        }

        // Cek DB untuk user_id tersebut (ambil foto, jenis kelamin, & tipe guru/siswa)
        if ($pdo) {
            $stmt = $pdo->prepare("
                SELECT p.foto, COALESCE(s.jk, g.jk, '') AS jk,
                       (CASE WHEN g.id_guru IS NOT NULL THEN 'guru' 
                             WHEN s.id_siswa IS NOT NULL THEN 'siswa' 
                             ELSE 'staf' END) AS u_type
                FROM pengguna p
                LEFT JOIN siswa s ON p.id_pengguna = s.id_pengguna
                LEFT JOIN guru g ON p.id_pengguna = g.id_pengguna
                WHERE p.id_pengguna = ?
            ");
            $stmt->execute([$user_id]);
            $u = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($u) {
                if (!empty($u['foto'])) {
                    $photo_path = 'assets/img/profil/' . $u['foto'];
                    if (file_exists(__DIR__ . '/../public/' . $photo_path)) {
                        return BASE_URL . $photo_path;
                    }
                }
                if (empty($jk) && !empty($u['jk'])) {
                    $jk = $u['jk'];
                }
                if (empty($type) && !empty($u['u_type'])) {
                    $type = $u['u_type'];
                }
            }
        }
    }

    $is_female = !empty($jk) && strtoupper(substr(trim($jk), 0, 1)) === 'P';
    $is_siswa = ($type === 'siswa');

    if ($is_siswa) {
        return BASE_URL . ($is_female ? 'assets/img/avatar-female.jpg' : 'assets/img/avatar-male.jpg');
    } else {
        return BASE_URL . ($is_female ? 'assets/img/avatar-guru-female.jpg' : 'assets/img/avatar-guru-male.jpg');
    }
}

/**
 * [BARU] Helper khusus untuk merender avatar / foto profil siswa & guru langsung dari nama file foto atau jenis kelamin
 */
function get_user_avatar($photo = null, $gender = null, $user_type = 'siswa') {
    $base = defined('BASE_URL') ? BASE_URL : '/';
    if (!empty($photo)) {
        $photo_path = 'assets/img/profil/' . $photo;
        if (file_exists(__DIR__ . '/../public/' . $photo_path)) {
            return $base . $photo_path;
        }
    }
    $is_female = !empty($gender) && strtoupper(substr(trim($gender), 0, 1)) === 'P';
    $is_siswa = ($user_type === 'siswa');
    if ($is_siswa) {
        return $base . ($is_female ? 'assets/img/avatar-female.jpg' : 'assets/img/avatar-male.jpg');
    } else {
        return $base . ($is_female ? 'assets/img/avatar-guru-female.jpg' : 'assets/img/avatar-guru-male.jpg');
    }
}

// -----------------------------------------------------------------------------
// AUDIT LOG HELPER
// -----------------------------------------------------------------------------
/**
 * Helper cepat untuk mencatat Audit Log
 * Pastikan AuditLogModel sudah di-load atau di-autoload
 */
function audit_log($aksi, $deskripsi, $target_tabel = null, $id_pengguna = null) {
    global $pdo; // Asumsi $pdo global tersedia di scope ini (misal dari index.php)
    
    // Fallback jika $pdo tidak global (tergantung arsitektur)
    if (!isset($pdo) || !$pdo) {
         return false; 
    }
    
    // Pastikan Model sudah ada
    if (!class_exists('AuditLogModel')) {
        $modelPath = __DIR__ . '/../app/models/AuditLogModel.php';
        if (file_exists($modelPath)) {
            require_once $modelPath;
        } else {
            return false;
        }
    }
    
    return AuditLogModel::log($pdo, $aksi, $deskripsi, $target_tabel, $id_pengguna);
}

/**
 * Mendapatkan Real Client IP Address (Mendukung Cloudflare Tunnel, Nginx, Proxy)
 */
function get_client_ip() {
    $headers = [
        'HTTP_CF_CONNECTING_IP',     // Cloudflare Tunnel & Proxy (Paling Akurat)
        'HTTP_TRUE_CLIENT_IP',       // Cloudflare Enterprise / Akamai
        'HTTP_X_REAL_IP',            // Nginx Reverse Proxy
        'HTTP_X_FORWARDED_FOR',      // Standard Reverse Proxy (Multi-hop)
        'HTTP_CLIENT_IP',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'REMOTE_ADDR'                // Standard Web Server Fallback
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ipList = explode(',', $_SERVER[$header]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Parser User-Agent untuk Mendeteksi OS, Browser, Tipe Perangkat, dan Icon
 */
function parse_user_agent($userAgent) {
    if (empty($userAgent) || $userAgent === 'Unknown') {
        return [
            'os' => 'Unknown OS',
            'browser' => 'Unknown Browser',
            'device_type' => 'desktop',
            'icon' => 'fas fa-desktop',
            'label' => 'Perangkat Tidak Diketahui'
        ];
    }

    $os = 'Unknown OS';
    $icon = 'fas fa-desktop';
    $deviceType = 'desktop';

    // 1. Deteksi OS
    if (preg_match('/android/i', $userAgent)) {
        $os = 'Android';
        $icon = 'fab fa-android text-success';
        $deviceType = 'mobile';
        if (preg_match('/android\s+([0-9\.]+)/i', $userAgent, $matches)) {
            $os .= ' ' . $matches[1];
        }
    } elseif (preg_match('/iphone/i', $userAgent)) {
        $os = 'iPhone (iOS)';
        $icon = 'fab fa-apple text-dark';
        $deviceType = 'mobile';
    } elseif (preg_match('/ipad/i', $userAgent)) {
        $os = 'iPad (iPadOS)';
        $icon = 'fab fa-apple text-dark';
        $deviceType = 'tablet';
    } elseif (preg_match('/windows nt 10.0/i', $userAgent)) {
        $os = 'Windows 10/11';
        $icon = 'fab fa-windows text-primary';
    } elseif (preg_match('/windows nt 6.3/i', $userAgent)) {
        $os = 'Windows 8.1';
        $icon = 'fab fa-windows text-primary';
    } elseif (preg_match('/windows nt 6.1/i', $userAgent)) {
        $os = 'Windows 7';
        $icon = 'fab fa-windows text-primary';
    } elseif (preg_match('/windows/i', $userAgent)) {
        $os = 'Windows';
        $icon = 'fab fa-windows text-primary';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $os = 'macOS';
        $icon = 'fab fa-apple text-dark';
    } elseif (preg_match('/linux/i', $userAgent)) {
        $os = 'Linux';
        $icon = 'fab fa-linux text-warning';
    }

    // 2. Deteksi Browser
    $browser = 'Web Browser';
    if (preg_match('/edg/i', $userAgent)) {
        $browser = 'Microsoft Edge';
    } elseif (preg_match('/chrome|crios/i', $userAgent) && !preg_match('/opr|opera/i', $userAgent)) {
        $browser = 'Google Chrome';
    } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
        $browser = 'Mozilla Firefox';
    } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) {
        $browser = 'Apple Safari';
    } elseif (preg_match('/opr|opera/i', $userAgent)) {
        $browser = 'Opera';
    } elseif (preg_match('/samsungbrowser/i', $userAgent)) {
        $browser = 'Samsung Internet';
    }

    return [
        'os' => $os,
        'browser' => $browser,
        'device_type' => $deviceType,
        'icon' => $icon,
        'label' => "$os ($browser)"
    ];
}

/**
 * Format Waktu Relatif (Contoh: "2 menit yang lalu", "1 jam yang lalu")
 */
function time_elapsed_string($datetime, $full = false) {
    if (!$datetime) return '-';
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'tahun',
        'm' => 'bulan',
        'w' => 'minggu',
        'd' => 'hari',
        'h' => 'jam',
        'i' => 'menit',
        's' => 'detik',
    ];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' yang lalu' : 'Baru saja';
}

/**
 * [BARU] Memformat Nama dengan Gelar (Depan & Belakang)
 * Contoh: Drs. Wawan Setiawan, S.Pd.
 */
function format_nama_gelar($nama, $gelar_depan = '', $gelar_belakang = '') {
    $hasil = '';
    
    // Gelar Depan (diakhiri spasi jika ada)
    if (!empty($gelar_depan)) {
        $hasil .= trim($gelar_depan) . ' ';
    }
    
    // Nama Utama
    $hasil .= trim($nama);
    
    // Gelar Belakang (diawali koma dan spasi jika ada)
    if (!empty($gelar_belakang)) {
        $hasil .= ', ' . trim($gelar_belakang);
    }
    
    return $hasil;
}

/**
 * [BARU] Helper untuk Clean URLs
 * Mengubah index.php?mod=X&act=Y menjadi /X/Y
 */
function url($mod, $act = '', $params = []) {
    $url = '/' . ltrim($mod, '/');
    if ($act && $act !== 'index') {
        $url .= '/' . ltrim($act, '/');
    }
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

if (!function_exists('format_cbt_math_output')) {
    function format_cbt_math_output($text) {
        if ($text === null || $text === '') return '';

        $tokens = [];
        $token_idx = 0;

        $wrap_token = function($str) use (&$tokens, &$token_idx) {
            $key = '___MATH_TK_' . ($token_idx++) . '___';
            $tokens[$key] = '$' . trim($str, '$') . '$';
            return $key;
        };

        // 1. Amankan yang sudah memiliki $...$ atau $$...$$
        $text = preg_replace_callback('/(\$\$[^\$]+\$\$|\$[^\$]+\$)/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        // 2. Wrap complete LaTeX commands with braces
        $text = preg_replace_callback('/\\\\(?:frac|sqrt|left|right|sum|int|lim|prod|binom|over|underline|overline|mathbf|text)\s*(?:\[[^\]]*\])?(?:\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\})+/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        // 3. Wrap caret parentheses exponents
        $text = preg_replace_callback('/([a-zA-Z0-9\)\.]+|\w+)\^\(([^)]+)\)/', function($m) use ($wrap_token) {
            return $wrap_token($m[1] . '^{' . $m[2] . '}');
        }, $text);

        // 4. Wrap simple single-token exponents
        $text = preg_replace_callback('/(?<![a-zA-Z0-9\$\_\@])([a-zA-Z0-9]+)\^([0-9a-zA-Z]+)(?![a-zA-Z0-9\$\_\@])/', function($m) use ($wrap_token) {
            return $wrap_token($m[1] . '^{' . $m[2] . '}');
        }, $text);

        // 5. Wrap single LaTeX words
        $text = preg_replace_callback('/\\\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|cdot|times|div|pm|mp|le|ge|ne|neq|approx|infty|forall|exists|in|notin|subset|subseteq|cup|cap|to|leftarrow|rightarrow|Rightarrow|leftrightarrow)\b/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        if (!empty($tokens)) {
            $text = strtr($text, $tokens);
        }

        $text = preg_replace('/\$\s*\$/', ' ', $text);
        $text = preg_replace('/\$\s*([\=\+\-\*\/])\s*\$/', ' $1 ', $text);

        return $text;
    }
}