<?php
/**
 * CBT - Session Manager
 * Menggunakan prefix 'cbt_' agar tidak konflik dengan session SIMAKS.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_name('CBTSESSID'); // Nama session berbeda dari SIMAKS
    session_start();
}

/**
 * Cek apakah user sudah login sebagai Admin/Guru CBT
 */
function cbt_require_admin()
{
    if (empty($_SESSION['cbt_user_id']) || !in_array($_SESSION['cbt_role'], ['superadmin', 'admin', 'guru'])) {
        header('Location: ' . CBT_BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Cek apakah user sudah login sebagai Siswa CBT
 */
function cbt_require_siswa()
{
    if (empty($_SESSION['cbt_siswa_id'])) {
        header('Location: ' . CBT_BASE_URL . '/ujian.php');
        exit;
    }
}


/**
 * Logout Admin
 */
function cbt_logout_admin()
{
    unset($_SESSION['cbt_user_id'], $_SESSION['cbt_user_nama'], $_SESSION['cbt_role']);
}

/**
 * Logout Siswa
 */
function cbt_logout_siswa()
{
    unset($_SESSION['cbt_siswa_id'], $_SESSION['cbt_siswa_nama'], $_SESSION['cbt_jadwal_id']);
}
