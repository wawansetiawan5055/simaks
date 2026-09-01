<?php
// Adapter to allow legacy login pages to use new AuthController
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AuthController.php';

use App\Controllers\AuthController;

$action = $_REQUEST['action'] ?? 'show';
$auth = new AuthController();
if ($action === 'process' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->processLogin();
} elseif ($action === 'logout') {
    $auth->logout();
} else {
    $auth->showLogin();
}
