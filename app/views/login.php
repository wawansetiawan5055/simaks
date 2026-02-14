<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIMAKS</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AdminLTE CSS (Optional, mainly for reset/utils if needed, but we write custom mainly) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- Toast Notification CSS -->
    <link rel="stylesheet" href="assets/css/notification.css">

    <style>
        :root {
            /* Color Palette */
            --color-primary: #0f172a;
            /* Deep Navy */
            --color-primary-light: #1e293b;
            /* Lighter Navy */
            --color-accent: #3b82f6;
            /* Bright Blue */
            --color-accent-hover: #2563eb;
            /* Darker Blue */
            --color-bg-light: #f8fafc;
            /* Off-white background */
            --color-text-main: #334155;
            /* Slate 700 */
            --color-text-muted: #64748b;
            /* Slate 500 */
            --color-white: #ffffff;

            /* Dimensions */
            --border-radius: 12px;
            --input-height: 50px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-bg-light);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            /* Prevent scroll on desktop */
            display: flex;
        }

        /* --- LAYOUT --- */

        /* Left Side: Branding (Desktop) */
        .brand-section {
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(30, 41, 59, 0.8)),
                url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=1740&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 80px;
            color: var(--color-white);
            position: relative;
        }

        .brand-section::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(to right, transparent, rgba(15, 23, 42, 0.5));
            pointer-events: none;
        }

        .brand-content {
            z-index: 10;
            max-width: 600px;
        }

        .brand-logo-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 10px;
            object-fit: contain;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .brand-title {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .brand-subtitle {
            font-size: 1.1rem;
            font-weight: 300;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* Right Side: Login Form */
        .login-section {
            width: 50%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: var(--color-bg-light);
            position: relative;
        }

        /* Decorative circle behind form */
        .login-bg-circle {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: transparent;
            /* Removed card shadow/bg for a cleaner "floating" look on the light bg, 
               or we can add a subtle one if preferred. */
            z-index: 10;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--color-text-muted);
            font-size: 0.95rem;
        }

        /* --- FORM ELEMENTS --- */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-text-main);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            height: var(--input-height);
            padding: 0 45px 0 15px;
            /* Space for icon right */
            font-size: 0.95rem;
            color: var(--color-text-main);
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: var(--color-accent);
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .form-control:focus+.input-icon {
            color: var(--color-accent);
        }

        .toggle-password {
            cursor: pointer;
            pointer-events: auto;
            /* ensure clickable */
        }

        .toggle-password:hover {
            color: var(--color-text-main);
        }

        /* Checkbox & Forgot Password */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            color: var(--color-text-muted);
        }

        .custom-checkbox input {
            display: none;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 5px;
            margin-right: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: #fff;
        }

        .custom-checkbox input:checked+.checkmark {
            background: var(--color-accent);
            border-color: var(--color-accent);
        }

        .checkmark::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 10px;
            color: white;
            display: none;
        }

        .custom-checkbox input:checked+.checkmark::after {
            display: block;
        }

        /* Button */
        .btn-login {
            width: 100%;
            height: var(--input-height);
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1), 0 2px 4px -1px rgba(15, 23, 42, 0.06);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.15), 0 4px 6px -2px rgba(15, 23, 42, 0.1);
            background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary));
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Alerts */
        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Button Home Top Right */
        .btn-back-home {
            position: absolute;
            top: 30px;
            right: 30px;
            background: white;
            color: var(--color-text-muted);
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }

        .btn-back-home:hover {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 23, 42, 0.1);
        }

        /* --- RESPONSIVE --- */

        /* TABLET & MOBILE */
        @media (max-width: 992px) {
            .brand-section {
                display: none;
                /* Hide brand section to focus on form */
            }

            .login-section {
                width: 100%;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            }

            /* Add a subtle mobile header branding if needed inside the form area */
            .mobile-brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 20px;
                text-align: center;
            }

            .mobile-brand img {
                width: 70px;
                height: 70px;
                margin-bottom: 10px;
                border-radius: 15px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .mobile-brand h3 {
                color: var(--color-primary);
                font-size: 1.5rem;
                font-weight: 700;
            }
        }

        /* SMALL MOBILE */
        @media (max-width: 480px) {
            .login-section {
                padding: 20px;
            }

            .login-header h2 {
                font-size: 1.7rem;
            }
        }
    </style>
</head>

<body>

    <!-- LEFT SIDE: BRANDING (Desktop Only) -->
    <div class="brand-section">
        <div class="brand-content">
            <div class="brand-logo-wrapper">
                <img src="assets/img/AdminLTELogo.png" class="brand-logo" alt="Logo">
                <!-- If you have a second logo -->
                <img src="<?= htmlspecialchars(get_app_logo()) ?>" class="brand-logo" alt="Logo Sekolah">
            </div>
            <h1 class="brand-title">SIMAKS</h1>
            <p class="brand-subtitle">
                Sistem Informasi Manajemen Akademik Sekolah<br>
                <strong>SMA PLUS AL MANSHURIYAH</strong>
            </p>
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <span
                    style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem;">V.2.0</span>
                <span
                    style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem;">Terintegrasi</span>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: FORM -->
    <div class="login-section">
        <a href="index.php" class="btn-back-home">
            <i class="fas fa-home"></i>
            <span>Beranda</span>
        </a>
        <div class="login-bg-circle"></div>

        <div class="login-card">

            <!-- Mobile Branding (Visible only on < 992px) -->
            <div class="mobile-brand d-lg-none d-block hidden-lg" style="display: none;">
                <img src="<?= htmlspecialchars(get_app_logo()) ?>" alt="Logo">
                <h3>SIMAKS</h3>
            </div>

            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan login ke akun Anda untuk melanjutkan.</p>
            </div>

            <!-- Old Alert Removed -->

            <form action="index.php?mod=auth&act=login_action" method="post">

                <!-- SECURITY: CSRF Protection Token -->
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="username" id="username"
                            placeholder="Masukkan username" required autofocus autocomplete="off">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="Masukkan password" required>
                        <i class="fas fa-eye-slash input-icon toggle-password" id="toggle-password"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <label class="custom-checkbox">
                        <input type="checkbox" id="remember">
                        <span class="checkmark"></span>
                        Ingatkan Saya
                    </label>
                    <!-- <a href="#" style="text-decoration: none; color: var(--color-accent); font-weight: 500;">Lupa Password?</a> -->
                </div>

                <button type="submit" class="btn-login">
                    Masuk <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                </button>

            </form>

            <div style="margin-top: 30px; text-align: center; font-size: 0.8rem; color: #94a3b8;">
                &copy; <?= date('Y') ?> SIMAKS - SMA Plus Al Manshuriyah
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <!-- Toast Notification System -->
    <script src="assets/js/notification.js"></script>
    <script>
    // Bridge login_error to generic error for toast
    <?php
    if (isset($_SESSION['login_error'])) {
        $_SESSION['pesan_error'] = $_SESSION['login_error'];
        unset($_SESSION['login_error']);
    }
    
    // Pass PHP session messages to JavaScript for toast notifications
    $hasMessages = isset($_SESSION['pesan_sukses']) || isset($_SESSION['pesan_error']) 
        || isset($_SESSION['pesan_warning']) || isset($_SESSION['pesan_info']);
    
    if ($hasMessages):
    ?>
    window.phpSessionMessages = {
        success: <?= isset($_SESSION['pesan_sukses']) ? json_encode($_SESSION['pesan_sukses']) : 'null' ?>,
        error: <?= isset($_SESSION['pesan_error']) ? json_encode($_SESSION['pesan_error']) : 'null' ?>,
        warning: <?= isset($_SESSION['pesan_warning']) ? json_encode($_SESSION['pesan_warning']) : 'null' ?>,
        info: <?= isset($_SESSION['pesan_info']) ? json_encode($_SESSION['pesan_info']) : 'null' ?>
    };
    <?php
        // Clear session messages after passing to JavaScript
        unset($_SESSION['pesan_sukses']);
        unset($_SESSION['pesan_error']);
        unset($_SESSION['pesan_warning']);
        unset($_SESSION['pesan_info']);
    endif;
    ?>
    </script>

    <script>
        $(document).ready(function () {
            // Logic to show mobile brand correctly via JS if CSS media query fails slightly or for specific overrides
            if ($(window).width() < 992) {
                $('.mobile-brand').show();
            }
            $(window).resize(function () {
                if ($(window).width() < 992) {
                    $('.mobile-brand').show();
                } else {
                    $('.mobile-brand').hide();
                }
            });

            // Password Toggle
            $('#toggle-password').click(function () {
                var input = $('#password');
                var icon = $(this);

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    icon.css('color', 'var(--color-accent)');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    icon.css('color', '');
                }
            });
        });
    </script>
</body>

</html>