<?php
/**
 * CBT - Main Router
 * Pola identik dengan public/index.php di SIMAKS
 */

define('CBT_ROOT', dirname(__DIR__));
define('CBT_BASE_URL', '/simaks/cbt');

require_once CBT_ROOT . '/config/db.php';
require_once CBT_ROOT . '/config/bridge.php';
require_once CBT_ROOT . '/config/session.php';

// Controllers
require_once CBT_ROOT . '/app/controllers/AuthController.php';
require_once CBT_ROOT . '/app/controllers/DashboardController.php';
require_once CBT_ROOT . '/app/controllers/AdminController.php';
require_once CBT_ROOT . '/app/controllers/BankSoalController.php';
require_once CBT_ROOT . '/app/controllers/UjianController.php';

$pdo = cbt_connect_db();
$mod = $_GET['mod'] ?? 'dashboard';
$act = $_GET['act'] ?? 'index';

// ============================================================
// ROUTING ADMIN PANEL
// ============================================================
switch ($mod) {

    case 'login':
        AuthController::login($pdo);
        break;

    case 'logout':
        AuthController::logout();
        break;

    case 'dashboard':
        cbt_require_admin();
        DashboardController::index($pdo);
        break;

    // --- Administrasi ---
    case 'kelola_kelas':
        cbt_require_admin();
        AdminController::kelas($pdo, $act);
        break;

    case 'kelola_mapel':
        cbt_require_admin();
        AdminController::mapel($pdo, $act);
        break;

    case 'kelola_siswa':
        cbt_require_admin();
        AdminController::siswa($pdo, $act);
        break;

    // --- Bank Soal ---
    case 'bank_soal':
        cbt_require_admin();
        BankSoalController::index($pdo, $act);
        break;

    case 'input_soal':
        cbt_require_admin();
        BankSoalController::input_soal($pdo, $act);
        break;

    case 'import_soal':
        cbt_require_admin();
        BankSoalController::import($pdo);
        break;

    // --- Manajemen Ujian ---
    case 'kelola_ujian':
        cbt_require_admin();
        UjianController::index($pdo, $act);
        break;

    case 'peserta':
        cbt_require_admin();
        UjianController::peserta($pdo, $act);
        break;

    case 'hasil_ujian':
        cbt_require_admin();
        UjianController::hasil($pdo, $act);
        break;

    // --- API Endpoints (AJAX) ---
    case 'api':
        header('Content-Type: application/json');
        // Akan di-handle controller masing-masing
        break;

    default:
        if (!empty($_SESSION['cbt_user_id'])) {
            DashboardController::index($pdo);
        } else {
            header('Location: ' . CBT_BASE_URL . '/login.php');
            exit;
        }
}
