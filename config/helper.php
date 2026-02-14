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
    header("Location: $url"); 
    exit;
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

    // 2. Cari Menu ID berdasarkan Link
    // [FIX] Gunakan query yang lebih smart: prioritaskan menu yang punya permission untuk role user
    // Ini mengatasi masalah duplicate menu entries
    
    $placeholders = implode(',', array_fill(0, count($user_role_ids), '?'));
    
    // Jika ada act param yang spesifik selain index, coba cari match yang lebih spesifik dulu
    if ($act_param !== 'index') {
         // Cari menu dengan act parameter spesifik, prioritaskan yang punya permission
         $sql_spec = "
             SELECT am.id_menu 
             FROM app_menu am
             LEFT JOIN hak_akses ha ON am.id_menu = ha.id_menu AND ha.id_peran IN ($placeholders)
             WHERE am.link LIKE ? AND am.status = 'Aktif'
             ORDER BY 
                 (ha.can_read IS NOT NULL AND ha.can_read = 1) DESC,
                 CHAR_LENGTH(am.link) DESC
             LIMIT 1
         ";
         $params_spec = array_merge($user_role_ids, ["%mod=$mod_param&act=$act_param%"]);
         $stmt_spec = $pdo->prepare($sql_spec);
         $stmt_spec->execute($params_spec);
         $menu_id = $stmt_spec->fetchColumn();
         
         if ($menu_id) {
             return _check_permission_by_id($pdo, $menu_id, $user_role_ids);
         }
    }
    
    // Fallback: cari berdasarkan mod saja
    // [FIX] Prioritaskan menu yang memiliki permission untuk role user
    // Ini mencegah matching ke menu duplicate yang tidak punya permission
    $sql = "
        SELECT am.id_menu 
        FROM app_menu am
        LEFT JOIN hak_akses ha ON am.id_menu = ha.id_menu AND ha.id_peran IN ($placeholders)
        WHERE am.link LIKE ? AND am.status = 'Aktif'
        ORDER BY 
            (ha.can_read IS NOT NULL AND ha.can_read = 1) DESC,
            CHAR_LENGTH(am.link) DESC
        LIMIT 1
    ";
    
    $params = array_merge($user_role_ids, ["%mod=$mod_param%"]);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $menu_id = $stmt->fetchColumn();

    if (!$menu_id) {
        // Jika menu tidak terdaftar di database (misal dashboard), anggap PUBLIC atau Restricted default?
        // Untuk aman, dashboard biasanya public untuk yang login.
        if ($mod_param == 'dashboard') return true;

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

    // 3. Cari Menu ID
    // [FIX] Gunakan query yang sama seperti check_access: prioritaskan menu dengan permission
    $placeholders = implode(',', array_fill(0, count($user_role_ids), '?'));
    
    $sql = "
        SELECT am.id_menu 
        FROM app_menu am
        LEFT JOIN hak_akses ha ON am.id_menu = ha.id_menu AND ha.id_peran IN ($placeholders)
        WHERE am.link LIKE ? AND am.status = 'Aktif'
        ORDER BY 
            (ha.can_read IS NOT NULL AND ha.can_read = 1) DESC,
            CHAR_LENGTH(am.link) DESC
        LIMIT 1
    ";
    
    $params = array_merge($user_role_ids, ["%mod=$mod_param%"]);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $menu_id = $stmt->fetchColumn();
    
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
        // FIX: Gunakan key yang benar (can_create, can_update)
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
 */
function get_user_photo($user_id = null) {
    global $pdo;
    if (!$user_id && isset($_SESSION['user_id'])) $user_id = $_SESSION['user_id'];
    
    // Default photo
    $default_photo = 'assets/img/user.jpg';
    
    if ($user_id) {
        // Cek Session dulu biar cepat (jika update profil, session harus diupdate juga)
        if (isset($_SESSION['user_photo']) && $_SESSION['user_id'] == $user_id) {
             $photo_path = 'assets/img/profil/' . $_SESSION['user_photo'];
             if (file_exists(__DIR__ . '/../public/' . $photo_path)) return $photo_path;
        }

        // Kalau di session gak ada, cek DB
        $stmt = $pdo->prepare("SELECT foto FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$user_id]);
        $foto = $stmt->fetchColumn();
        
        if ($foto) {
            $photo_path = 'assets/img/profil/' . $foto;
            if (file_exists(__DIR__ . '/../public/' . $photo_path)) return $photo_path;
        }
    }
    

    return $default_photo;
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