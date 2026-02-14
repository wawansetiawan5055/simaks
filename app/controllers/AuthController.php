<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../models/PenggunaModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';
require_once __DIR__ . '/../models/AppMenuModel.php'; 

function login_form() {
    // Jika user sudah login, langsung redirect ke dashboard
    if (isset($_SESSION['user_id'])) {
        redirect('index.php');
        return;
    }
    include __DIR__ . '/../views/login.php';
}

function login_action($pdo) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $username = trim($_POST['username'] ?? '');
    
    // Initialize rate limiter
    $rateLimiter = new RateLimiter($pdo);
    
    // SECURITY: Check if IP is blocked
    if ($rateLimiter->isBlocked($ip)) {
        $seconds = $rateLimiter->getTimeUntilUnblock($ip);
        $minutes = ceil($seconds / 60);
        $_SESSION['login_error'] = "Too many failed attempts. Please try again in $minutes minutes.";
        redirect('index.php?mod=auth&act=login');
        return;
    }
    
    // CSRF Protection
    if (!csrf_verify()) {
        $_SESSION['login_error'] = "Invalid security token. Please try again.";
        redirect('index.php?mod=auth&act=login');
        return;
    }
    
    // 1. Validasi Input
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $rateLimiter->recordAttempt($ip, $username); // Record as failed attempt
        $_SESSION['login_error'] = "Username dan password wajib diisi!";
        redirect('index.php?mod=auth&act=login');
        return;
    }
    
    $password = $_POST['password'];
    
    // 2. Cari Pengguna di Database
    $user = PenggunaModel::findByUsername($pdo, $username);

    // 3. Verifikasi Password (SECURE - Only Hashed Passwords)
    $is_password_valid = false;
    
    if ($user && password_verify($password, $user['password'])) {
        // Password cocok dengan Hash - SECURE ✓
        $is_password_valid = true;
    }

    // 4. Proses Login
    if ($user && $is_password_valid) {
        // Clear failed attempts on successful login
        $rateLimiter->clearAttempts($ip);
        
        session_regenerate_id(true); // Keamanan Session
        csrf_regenerate(); // Regenerate CSRF token after login
        
        // Simpan Data Dasar User
        $_SESSION['user_id'] = $user['id_pengguna'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_pengguna'] = $user['nama_pengguna'];
        
        // Simpan Role/Peran (PENTING UNTUK SIDEBAR)
        $roles_data = PenggunaModel::getRoles($pdo, $user['id_pengguna']);
        $user_role_ids = [];
        $user_role_names = [];

        foreach ($roles_data as $role) {
            $user_role_ids[] = (int)$role['id_peran'];
            $user_role_names[] = $role['nama_peran'];
        }

        $_SESSION['role_ids'] = $user_role_ids;
        $_SESSION['roles'] = $user_role_names;
        
        // AUDIT LOG: LOGIN
        if (function_exists('audit_log')) {
            audit_log('LOGIN', 'User masuk ke sistem (Login Sukses)', null, $user['id_pengguna']);
        }
        
        // Reset cache izin RBAC agar sidebar langsung update
        if (function_exists('reset_permission_cache')) {
            reset_permission_cache();
        }

        // Simpan Tahun Ajaran Aktif
        $ta_aktif = TahunAjaranModel::aktif($pdo);
        if ($ta_aktif) {
            $_SESSION['id_ta_aktif'] = $ta_aktif['id_ta'];
            $_SESSION['nama_ta_aktif'] = $ta_aktif['nama_ta'];
        }

        // --- [FIX] SIMPAN SESSION GURU JIKA USER ADALAH GURU ---
        // Cek apakah user memiliki role 'Guru' atau 'Guru Piket' atau 'Wali Kelas'
        // Kita cek apakah ID user ini ada di tabel guru
        $stmt_guru = $pdo->prepare("SELECT id_guru, nama FROM guru WHERE id_pengguna = ? LIMIT 1");
        $stmt_guru->execute([$user['id_pengguna']]);
        $data_guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);

        if ($data_guru) {
            $_SESSION['id_guru_terkait'] = $data_guru['id_guru'];
            $_SESSION['nama_guru_terkait'] = $data_guru['nama'];
        }
        // -------------------------------------------------------
        
        // Redirect Sukses ke Dashboard
        redirect('index.php');
        exit;
        
    } else {
        // Record failed login attempt
        $rateLimiter->recordAttempt($ip, $username);
        
        // AUDIT LOG: FAILED LOGIN
        // Kita log user_id = 0 (system) tapi deskripsinya mencatat username yang dicoba
        if (function_exists('audit_log')) {
             // Opsional: Jika username ada di DB, kita bisa log ID-nya. 
             // Tapi demi keamanan (user enumeraton), lebih baik log usename text-nya saja di deskripsi.
             audit_log('LOGIN_FAILED', "Gagal login dengan username: $username", null, 0);
        }
        
        // Calculate remaining attempts
        $remaining = $rateLimiter->getRemainingAttempts($ip);
        
        // Jika Login Gagal
        if ($remaining > 0) {
            $_SESSION['login_error'] = "Username atau Password salah! ($remaining attempts remaining)";
        } else {
            $_SESSION['login_error'] = "Too many failed login attempts. Account temporarily locked.";
        }
        
        redirect('index.php?mod=auth&act=login');
        exit;
    }
}

function logout_action() {
    // AUDIT LOG: LOGOUT
    // Log sebelum session di-destroy agar masih dapat user_id
    if (function_exists('audit_log') && isset($_SESSION['user_id'])) {
        audit_log('LOGOUT', 'User keluar dari sistem', null, $_SESSION['user_id']);
    }

    session_unset();
    session_destroy();
    redirect('index.php?mod=auth&act=login');
}
?>