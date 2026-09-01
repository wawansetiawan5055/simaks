<?php
/**
 * CBT - Admin Login Page
 */
define('CBT_ROOT', dirname(__DIR__));
define('CBT_BASE_URL', '/simaks/cbt');

require_once CBT_ROOT . '/config/db.php';
require_once CBT_ROOT . '/config/session.php';

// Jika sudah login, redirect ke dashboard
if (!empty($_SESSION['cbt_user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = cbt_connect_db();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM cbt_users WHERE username = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['cbt_user_id'] = $user['id_user'];
            $_SESSION['cbt_user_nama'] = $user['nama'];
            $_SESSION['cbt_role'] = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Lengkapi semua field!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panel CBT | SIMAKS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background shapes */
        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
        }

        body::before {
            width: 600px;
            height: 600px;
            background: #e94560;
            top: -200px;
            right: -200px;
        }

        body::after {
            width: 500px;
            height: 500px;
            background: #4a90e2;
            bottom: -200px;
            left: -200px;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            gap: 0;
            width: 900px;
            max-width: 95vw;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, .6);
        }

        /* === LEFT PANEL === */
        .login-brand {
            flex: 1;
            padding: 60px 50px;
            background: linear-gradient(180deg, rgba(233, 69, 96, 0.3) 0%, rgba(74, 144, 226, 0.2) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .login-brand .icon-wrap {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #e94560, #c0392b);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #fff;
            margin-bottom: 28px;
            box-shadow: 0 10px 30px rgba(233, 69, 96, .5);
        }

        .login-brand h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
        }

        .login-brand h1 span {
            color: #e94560;
        }

        .login-brand p {
            color: rgba(255, 255, 255, 0.55);
            font-size: .9rem;
            margin-top: 14px;
            line-height: 1.7;
        }

        .feature-list {
            margin-top: 30px;
            list-style: none;
        }

        .feature-list li {
            color: rgba(255, 255, 255, 0.7);
            font-size: .85rem;
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-list li i {
            color: #4eda88;
            width: 16px;
        }

        /* === RIGHT PANEL === */
        .login-form-wrap {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-wrap h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .login-form-wrap p {
            color: rgba(255, 255, 255, 0.45);
            font-size: .85rem;
            margin-bottom: 34px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: .9rem;
        }

        .input-wrap input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #fff;
            font-size: .95rem;
            transition: all .2s;
            outline: none;
            font-family: 'Inter', sans-serif;
        }

        .input-wrap input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .input-wrap input:focus {
            border-color: #e94560;
            background: rgba(233, 69, 96, 0.08);
        }

        .alert-error {
            background: rgba(233, 69, 96, 0.15);
            border: 1px solid rgba(233, 69, 96, 0.4);
            color: #ff8093;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: .85rem;
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #e94560, #c0392b);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all .3s;
            letter-spacing: .03em;
            box-shadow: 0 6px 20px rgba(233, 69, 96, .4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(233, 69, 96, .5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 28px;
            text-align: center;
            color: rgba(255, 255, 255, 0.3);
            font-size: .78rem;
        }

        .login-footer a {
            color: #e94560;
            text-decoration: none;
        }

        @media (max-width: 700px) {
            .login-brand {
                display: none;
            }

            .login-form-wrap {
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Brand Panel -->
        <div class="login-brand">
            <div class="icon-wrap"><i class="fas fa-laptop-code"></i></div>
            <h1>Panel <span>CBT</span><br>Administrator</h1>
            <p>Kelola bank soal, jadwal ujian, dan pantau semua peserta secara realtime.</p>
            <ul class="feature-list">
                <li><i class="fas fa-check-circle"></i> Bank Soal Multi-Tipe (PG, Essay, TF)</li>
                <li><i class="fas fa-check-circle"></i> Ujian dengan Acak Soal & Opsi</li>
                <li><i class="fas fa-check-circle"></i> Monitor Peserta Realtime</li>
                <li><i class="fas fa-check-circle"></i> Analisis Butir Soal & Rekap Nilai</li>
                <li><i class="fas fa-check-circle"></i> Cetak Kartu, Berita Acara, & Nilai</li>
            </ul>
        </div>

        <!-- Right Form Panel -->
        <div class="login-form-wrap">
            <h2>Selamat Datang 👋</h2>
            <p>Masuk ke panel administrasi CBT</p>

            <?php if ($error): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Masukkan username" autocomplete="username"
                            required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Masukkan password"
                            autocomplete="current-password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> &nbsp; Masuk ke Panel
                </button>
            </form>

            <div class="login-footer">
                <p>Akses untuk siswa? <a href="ujian.php">Login Ujian Siswa →</a></p>
                <br>
                <p>CBT &copy;
                    <?= date('Y') ?> · SIMAKS &mdash; SMA Plus Al Manshuriyah
                </p>
            </div>
        </div>
    </div>
</body>

</html>