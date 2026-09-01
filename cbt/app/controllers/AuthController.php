<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Database;

class AuthController
{
    public function showLogin(): void
    {
        require_once dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function processLogin(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $userModel = new User(Database::connect());
        } catch (\Throwable $e) {
            $error = 'Database unavailable';
            require_once dirname(__DIR__, 2) . '/views/auth/login.php';
            return;
        }

        $user = $userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE)
                session_start();
            $_SESSION['user'] = [
                'id' => $user['id'] ?? $user['userid'] ?? null,
                'username' => $user['username'] ?? $username,
                'name' => $user['name'] ?? ($user['Nama'] ?? null),
            ];

            $legacyToken = $this->generateLegacyToken($_SESSION['user']);
            $_SESSION['user']['legacy_token'] = $legacyToken;

            $params = session_get_cookie_params();
            $secure = !empty($_SERVER['HTTPS']);
            if (!headers_sent()) {
                setcookie('beeuser', $_SESSION['user']['username'], time() + 604800, $params['path'], $params['domain'] ?? '', $secure, true);
                setcookie('beelogin', $legacyToken, time() + 604800, $params['path'], $params['domain'] ?? '', $secure, true);
            }

            header('Location: /');
            exit;
        }

        $error = 'Invalid credentials';
        require_once dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        session_unset();
        session_destroy();
        if (!headers_sent()) {
            setcookie('beeuser', '', time() - 3600, '/');
            setcookie('beelogin', '', time() - 3600, '/');
        }
        header('Location: /login');
        exit;
    }

    private function generateLegacyToken(array $sessionUser): string
    {
        try {
            $rnd = bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            $rnd = md5(uniqid((string) ($sessionUser['username'] ?? ''), true));
        }
        return hash('sha256', ($sessionUser['username'] ?? '') . '|' . $rnd);
    }
}

// Minimal legacy-compatible class for older includes that expect a global AuthController
class Legacy_AuthController
{
    public static function login($pdo)
    {
        header('Location: ' . (defined('CBT_BASE_URL') ? CBT_BASE_URL : '') . '/login.php');
        exit;
    }

    public static function student_login($pdo)
    {
        header('Location: ' . (defined('CBT_BASE_URL') ? CBT_BASE_URL : '') . '/ujian.php');
        exit;
    }

    public static function logout()
    {
        if (function_exists('cbt_logout_admin'))
            cbt_logout_admin();
        if (function_exists('cbt_logout_siswa'))
            cbt_logout_siswa();
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        session_destroy();
        header('Location: ' . (defined('CBT_BASE_URL') ? CBT_BASE_URL : '') . '/login.php');
        exit;
    }
}

