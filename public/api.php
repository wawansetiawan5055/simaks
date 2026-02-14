<?php
/**
 * API Entry Point
 * Menerima request dari frontend (fetch/ajax) dan meneruskannya ke controller yang sesuai.
 * Format URL: api/api.php?mod={modul}&act={aksi}&...
 */

// 1. Load Environment & Database
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/db.php';

// 2. Start Session (Penting untuk cek login & hak akses)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 3. Set Header JSON (Default)
header('Content-Type: application/json; charset=utf-8');

// 3. Koneksi Database
$pdo = connect_db();

// 4. Ambil Parameter
$mod = $_GET['mod'] ?? '';
$act = $_GET['act'] ?? '';

// 5. Routing ke Controller API
// Sesuaikan dengan logic di public/index.php namun khusus API
switch ($mod) {
    case 'jadwal':
        require_once __DIR__ . '/../api/JadwalApiController.php';
        JadwalApiController::handle($pdo, $act);
        break;

    case 'absensi':
        require_once __DIR__ . '/../api/AbsensiApiController.php';
        AbsensiApiController::handle($pdo, $act);
        break;

    case 'cptp':
        require_once __DIR__ . '/../api/CpTpApiController.php';
        CpTpApiController::handle($pdo, $act);
        break;

    case 'siswa':
        require_once __DIR__ . '/../api/SiswaApiController.php';
        SiswaApiController::handle($pdo, $act);
        break;

    // Tambahkan modul lain jika diperlukan

    default:
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid API module: ' . $mod]);
        break;
}