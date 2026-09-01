<?php
/**
 * CBT - Student Login Page (Ujian)
 */
define('CBT_ROOT', dirname(__DIR__));
define('CBT_BASE_URL', '/simaks/cbt');

require_once CBT_ROOT . '/config/db.php';
require_once CBT_ROOT . '/config/session.php';

// Jika sudah login sebagai siswa, redirect ke ruang ujian?
if (!empty($_SESSION['cbt_siswa_id'])) {
    header('Location: index.php?mod=pilih_ujian');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = cbt_connect_db();
    $nis = trim($_POST['nis'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nis && $password) {
        $stmt = $pdo->prepare("SELECT * FROM cbt_siswa WHERE nis = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$nis]);
        $siswa = $stmt->fetch();

        if ($siswa && password_verify($password, $siswa['password'] ?? '')) {
            $_SESSION['cbt_siswa_id'] = $siswa['id_siswa'];
            $_SESSION['cbt_siswa_nama'] = $siswa['nama_siswa'];
            $_SESSION['cbt_siswa_nis'] = $siswa['nis'];
            $_SESSION['cbt_role'] = 'siswa';

            header('Location: index.php?mod=pilih_ujian');
            exit;
        } else {
            $error = 'NIS atau password salah!';
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
    <title>Login Ujian Siswa | CBT SIMAKS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #1e293b;
            padding: 40px;
            border-radius: 20px;
            width: 400px;
            max-width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .icon {
            width: 70px;
            height: 70px;
            background: #3b82f6;
            margin: 0 auto 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #fff;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        h2 {
            color: #fff;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        p {
            color: #94a3b8;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            color: #cbd5e1;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus {
            border-color: #3b82f6;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #3b82f6;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 30px;
            color: #64748b;
            font-size: 0.8rem;
        }

        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="icon"><i class="fas fa-graduation-cap"></i></div>
        <h2>Login Siswa</h2>
        <p>Gunakan NIS dan Password untuk mulai ujian</p>

        <?php if ($error): ?>
            <div class="error-box"><i class="fas fa-exclamation-circle mr-1"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" placeholder="Masukkan NIS" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-login">Mulai Ujian</button>
        </form>

        <div class="footer">
            Bukan siswa? <a href="login.php">Login Admin →</a>
        </div>
    </div>
</body>

</html>