<?php
// Simple test helper to set a session user and legacy cookies for manual testing.
// Usage: visit this script in browser or via curl to verify cookies are sent.

if (session_status() === PHP_SESSION_NONE) session_start();

$username = $_GET['u'] ?? 'testuser';
$name = $_GET['n'] ?? 'Test User';

$_SESSION['user'] = [
    'id' => 0,
    'username' => $username,
    'name' => $name,
    'legacy_token' => bin2hex(random_bytes(16)),
];

$params = session_get_cookie_params();
$secure = !empty($_SERVER['HTTPS']);
setcookie('beeuser', $username, time() + 604800, $params['path'], $params['domain'] ?? '', $secure, true);
setcookie('beelogin', $_SESSION['user']['legacy_token'], time() + 604800, $params['path'], $params['domain'] ?? '', $secure, true);

header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'user' => $_SESSION['user']]);
