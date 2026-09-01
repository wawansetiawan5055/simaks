<?php
$slides = [];
$quotes_array = [];
global $pdo;
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT config_key, config_value FROM app_config WHERE config_key IN ('login_bg_image_1', 'login_bg_image_2', 'login_bg_image_3', 'login_bg_image', 'login_quote_1', 'login_quote_2', 'login_quote_3')");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['config_key']] = $row['config_value'];
        }

        // Slides
        $slides[] = $settings['login_bg_image_1'] ?? ($settings['login_bg_image'] ?? 'hero-1.webp');
        $slides[] = $settings['login_bg_image_2'] ?? 'hero-2.webp';
        $slides[] = $settings['login_bg_image_3'] ?? 'hero-3.webp';

        // Quotes
        for ($i=1; $i<=3; $i++) {
            $raw_quote = $settings["login_quote_$i"] ?? '';
            if (trim($raw_quote) !== '') {
                $parts = preg_split('/( — | - )/', $raw_quote);
                $author = count($parts) > 1 ? array_pop($parts) : '';
                $text = implode(" — ", $parts);
                $quotes_array[] = [
                    'text' => trim($text),
                    'author' => trim($author)
                ];
            } else {
                $quotes_array[] = null;
            }
        }
    } catch (Exception $e) {}
}

// Fallback slides if empty (should have 3 from above anyway)
if (empty($slides)) {
    $slides = ['hero-1.webp', 'hero-2.webp', 'hero-3.webp'];
}

// Fallback quote if ALL are empty
if (empty(array_filter($quotes_array))) {
    $quotes_array = [
        ['text' => '"Barangsiapa yang menempuh jalan untuk mencari ilmu, maka Allah akan mudahkan baginya jalan menuju surga."', 'author' => 'HR. Muslim'],
        null,
        null
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIMAKS</title>
    <meta name="description" content="Login ke SIMAKS - Sistem Informasi Manajemen Akademik Sekolah">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Toast Notification CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/notification.css">

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        :root {
            --primary: #0f172a;      /* Dark sleek primary */
            --primary-hover: #334155;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --input-bg: #f8fafc;
            --radius-lg: 24px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        /* Login Mode Tabs */
        .login-tabs {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            gap: 4px;
        }
        .btn-tab {
            flex: 1;
            padding: 10px 14px;
            border: none;
            background: transparent;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-tab.active {
            background: white;
            color: var(--text-main);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        .btn-tab:hover:not(.active) {
            color: var(--text-main);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-layout {
            display: flex;
            width: 100%;
            height: 100vh;
            background: var(--surface);
        }

        /* -----------------------
           LEFT PANEL (SLIDER)
           ----------------------- */
        .image-panel {
            flex: 1;
            position: relative;
            background-color: #0f172a;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 50px 60px;
        }

        .slider-container {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease-in-out, transform 7s linear;
            transform: scale(1.05);
        }

        .slide.active {
            opacity: 1;
            transform: scale(1);
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15,23,42,0.1) 0%, rgba(15,23,42,0.9) 100%);
            z-index: 2;
        }

        /* Quote Box */
        .quote-box {
            position: relative;
            z-index: 3;
            max-width: 450px;
            animation: fadeIn 1s ease-out 0.5s both;
            transition: opacity 0.5s ease-in-out;
        }
        
        .quote-icon {
            font-size: 2.2rem;
            color: rgba(255, 255, 255, 0.25);
            margin-bottom: 16px;
        }

        .quote-text {
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 400;
            line-height: 1.6;
            margin-bottom: 16px;
            font-style: italic;
        }

        .quote-author {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        /* -----------------------
           RIGHT PANEL (FORM)
           ----------------------- */
        .form-panel {
            width: 520px;
            display: flex;
            flex-direction: column;
            padding: 60px 56px;
            position: relative;
            background: var(--surface);
            box-shadow: -20px 0 60px rgba(0,0,0,0.05);
            z-index: 10;
        }

        .btn-home {
            position: absolute;
            top: 40px;
            right: 40px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--input-bg);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 100px;
            transition: all 0.2s ease;
            border: 1px solid var(--border);
        }
        .btn-home:hover {
            background: #f1f5f9;
            color: var(--text-main);
            border-color: #cbd5e1;
        }

        .form-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 380px;
            width: 100%;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .brand-logo {
            width: 72px;
            height: auto;
            margin-bottom: 36px;
        }

        .header-text {
            margin-bottom: 40px;
        }
        .header-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }
        .header-text p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Inputs */
        .form-group {
            margin-bottom: 22px;
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 10px;
        }
        .input-group {
            position: relative;
        }
        .input-group i.icon-left {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.2s ease;
            pointer-events: none;
        }
        .form-control {
            width: 100%;
            height: 56px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0 16px 0 50px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-main);
            transition: all 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
        .form-control:focus + i.icon-left {
            color: var(--primary);
        }
        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        
        .btn-toggle-pass {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }
        .btn-toggle-pass:hover {
            color: var(--text-main);
        }

        /* Checkbox */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 32px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            user-select: none;
        }
        .checkbox-label input {
            display: none;
        }
        .custom-checkbox {
            width: 22px;
            height: 22px;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            background: var(--surface);
        }
        .checkbox-label input:checked + .custom-checkbox {
            background: var(--primary);
            border-color: var(--primary);
        }
        .custom-checkbox i {
            color: white;
            font-size: 10px;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }
        .checkbox-label input:checked + .custom-checkbox i {
            opacity: 1;
            transform: scale(1);
        }

        /* Button */
        .btn-submit {
            width: 100%;
            height: 56px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -10px rgba(15, 23, 42, 0.4);
        }
        .btn-submit:active {
            transform: translateY(0);
            box-shadow: none;
        }
        .btn-submit i {
            font-size: 0.9rem;
            transition: transform 0.2s ease;
        }
        .btn-submit:hover i {
            transform: translateX(4px);
        }

        /* Alert */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }
        .alert i { margin-top: 2px; }
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .footer-text {
            text-align: center;
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: auto;
            padding-top: 40px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .image-panel {
                display: none;
            }
            .form-panel {
                width: 100%;
                max-width: 500px;
                margin: auto;
                height: auto;
                border-radius: var(--radius-lg);
                box-shadow: 0 20px 40px rgba(0,0,0,0.08);
                padding: 48px 40px;
            }
            .login-layout {
                padding: 24px;
                background: var(--background);
                align-items: center;
                justify-content: center;
            }
            .btn-home {
                top: 24px;
                right: 24px;
            }
        }
        @media (max-width: 480px) {
            .form-panel {
                padding: 40px 24px;
                box-shadow: none;
                border-radius: 0;
            }
            .header-text h1 {
                font-size: 1.75rem;
            }
            .login-layout {
                padding: 0;
                background: var(--surface);
            }
            .btn-home {
                border: none;
                background: transparent;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>

    <div class="login-layout">
        
        <!-- Left Panel: Image Slider -->
        <div class="image-panel">
            <div class="slider-container">
                <?php foreach($slides as $index => $slide_img): ?>
                <img src="<?= BASE_URL ?>assets/img/<?= htmlspecialchars($slide_img) ?>" alt="Login Slide <?= $index+1 ?>" class="slide <?= $index === 0 ? 'active' : '' ?>">
                <?php endforeach; ?>
            </div>
            <div class="overlay"></div>

            <!-- Quote / Hadits -->
            <div class="quote-box" <?= empty($quotes_array[0]) ? 'style="display: none;"' : '' ?>>
                <i class="fas fa-quote-left quote-icon"></i>
                <div class="quote-text" id="quoteText">
                    <?= !empty($quotes_array[0]) ? htmlspecialchars($quotes_array[0]['text']) : '' ?>
                </div>
                <div class="quote-author" id="quoteAuthor">
                    <?php if(!empty($quotes_array[0]['author'])): ?>
                    — <?= htmlspecialchars($quotes_array[0]['author']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="form-panel">
            <a href="<?= BASE_URL ?>landing" class="btn-home">
                <i class="fas fa-arrow-left"></i> Beranda
            </a>

            <div class="form-wrapper">
                <!-- App Logo -->
                <img src="<?= BASE_URL ?>assets/img/logoapk.png" alt="SIMAKS Logo" class="brand-logo">

                <div class="header-text">
                    <h1>Masuk ke Akun Anda</h1>
                    <p>Silakan masukkan kredensial Anda untuk mengakses sistem manajemen sekolah.</p>
                </div>

                <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['login_error']) ?></span>
                </div>
                <?php unset($_SESSION['login_error']); endif; ?>

                <!-- Login Method Switcher -->
                <div class="login-tabs">
                    <button type="button" class="btn-tab active" id="tabStandard" onclick="switchLoginTab('standard')">
                        <i class="fas fa-key"></i> Username & Password
                    </button>
                    <button type="button" class="btn-tab" id="tabQr" onclick="switchLoginTab('qr')">
                        <i class="fas fa-qrcode"></i> Scan QR Code
                    </button>
                </div>

                <form action="<?= BASE_URL ?>auth/login_action" method="post" id="loginForm" autocomplete="off">
                    
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus autocomplete="username">
                            <i class="fas fa-user icon-left"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                            <i class="fas fa-lock icon-left"></i>
                            <button type="button" class="btn-toggle-pass" id="togglePassword" tabindex="-1">
                                <i class="fas fa-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember">
                            <div class="custom-checkbox">
                                <i class="fas fa-check"></i>
                            </div>
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-submit" id="btnLogin">
                        Masuk Sekarang <i class="fas fa-arrow-right"></i>
                    </button>

                </form>

                <!-- Section Login Scan QR Code -->
                <div id="qrSection" style="display: none;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 15px;">
                            <i class="fas fa-info-circle text-info mr-1"></i> Arahkan QR Code Kartu Akun ke kamera Anda:
                        </p>
                        
                        <div id="qrReader" style="width: 100%; max-width: 320px; margin: 0 auto; border-radius: 16px; overflow: hidden; border: 2px dashed #cbd5e1; background: #0f172a; min-height: 240px; display: flex; align-items: center; justify-content: center;"></div>
                        
                        <div id="qrStatus" style="margin-top: 14px; font-size: 0.875rem; font-weight: 500; min-height: 24px; color: #475569;">
                            Sistem siap memindai QR Code...
                        </div>
                    </div>
                    
                    <div style="border-top: 1.5px dashed #e2e8f0; padding-top: 16px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-bottom: 10px;">
                            <button type="button" onclick="toggleCameraMode()" style="background: #f1f5f9; color: #334155; border: 1.5px solid #cbd5e1; cursor: pointer; border-radius: 12px; padding: 10px 16px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-sync-alt text-primary"></i> Ganti Kamera (Depan/Belakang)
                            </button>
                            <label style="background: #f8fafc; color: var(--text-main); border: 1.5px solid #cbd5e1; cursor: pointer; border-radius: 12px; padding: 10px 16px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; margin: 0;">
                                <i class="fas fa-upload text-muted"></i> Upload File QR Code
                                <input type="file" id="qrFileInput" accept="image/*" style="display: none;">
                            </label>
                        </div>

                        <div class="mt-2" style="font-size: 0.75rem; color: var(--text-muted);">
                            Kartu QR Code dapat diperoleh melalui Admin Sekolah di menu Manajemen Pengguna.
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="footer-text">
                &copy; <?= date('Y') ?> SIMAKS. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </div>

    <!-- Toast Notification System -->
    <script src="<?= BASE_URL ?>assets/js/notification.js"></script>
    <script>
    <?php
    $hasMessages = isset($_SESSION['pesan_sukses']) || isset($_SESSION['pesan_error'])
        || isset($_SESSION['pesan_warning']) || isset($_SESSION['pesan_info']);

    if ($hasMessages):
    ?>
    window.phpSessionMessages = {
        success: <?= isset($_SESSION['pesan_sukses']) ? json_encode($_SESSION['pesan_sukses']) : 'null' ?>,
        error:   <?= isset($_SESSION['pesan_error'])  ? json_encode($_SESSION['pesan_error'])  : 'null' ?>,
        warning: <?= isset($_SESSION['pesan_warning'])? json_encode($_SESSION['pesan_warning']): 'null' ?>,
        info:    <?= isset($_SESSION['pesan_info'])   ? json_encode($_SESSION['pesan_info'])   : 'null' ?>
    };
    <?php
        unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error'],
              $_SESSION['pesan_warning'], $_SESSION['pesan_info']);
    endif;
    ?>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Visibility Toggle
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function () {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    toggleIcon.className = type === 'password' ? 'fas fa-eye-slash' : 'fas fa-eye';
                    
                    if (type === 'text') {
                        toggleIcon.style.color = 'var(--primary)';
                    } else {
                        toggleIcon.style.color = '#94a3b8';
                    }
                });
            }

            // Loading state on form submit
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('btnLogin');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
                    btn.style.opacity = '0.9';
                    btn.style.pointerEvents = 'none';
                });
            }

            // Slider & Quote Logic
            const slides = document.querySelectorAll('.slide');
            const quotes = <?= json_encode($quotes_array) ?>;
            const quoteText = document.getElementById('quoteText');
            const quoteAuthor = document.getElementById('quoteAuthor');
            const quoteBox = document.querySelector('.quote-box');
            
            // Set initial state
            if (quotes.length > 0 && quotes[0]) {
                quoteText.textContent = quotes[0].text;
                quoteAuthor.textContent = quotes[0].author ? '— ' + quotes[0].author : '';
                quoteBox.style.display = 'block';
            } else {
                quoteBox.style.display = 'none';
            }

            if (slides.length > 1) {
                let currentSlide = 0;
                setInterval(() => {
                    // Change Image
                    slides[currentSlide].classList.remove('active');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');

                    // Change Quote (sync with slide)
                    if (quotes.length > 0) {
                        let quoteIndex = currentSlide % quotes.length;
                        
                        // Fade out quote box slightly
                        quoteBox.style.opacity = 0;
                        
                        setTimeout(() => {
                            if (quotes[quoteIndex]) {
                                quoteBox.style.display = 'block';
                                quoteText.textContent = quotes[quoteIndex].text;
                                if (quotes[quoteIndex].author) {
                                    quoteAuthor.textContent = '— ' + quotes[quoteIndex].author;
                                } else {
                                    quoteAuthor.textContent = '';
                                }
                            } else {
                                quoteBox.style.display = 'none';
                            }
                            
                            // Fade back in
                            quoteBox.style.opacity = 1;
                        }, 500); // Wait for fade out
                    }
                }, 5000); // Ganti slide setiap 5 detik
            }
        });

        // --- QR CODE LOGIN LOGIC ---
        let html5QrCode = null;
        let currentFacingMode = "environment"; // Default kamera belakang (fallback ke depan jika tidak ada)

        window.switchLoginTab = function(mode) {
            const tabStandard = document.getElementById('tabStandard');
            const tabQr = document.getElementById('tabQr');
            const formStandard = document.getElementById('loginForm');
            const qrSection = document.getElementById('qrSection');

            if (mode === 'qr') {
                tabStandard.classList.remove('active');
                tabQr.classList.add('active');
                formStandard.style.display = 'none';
                qrSection.style.display = 'block';
                startQrScanner();
            } else {
                tabQr.classList.remove('active');
                tabStandard.classList.add('active');
                qrSection.style.display = 'none';
                formStandard.style.display = 'block';
                stopQrScanner();
            }
        };

        window.toggleCameraMode = function() {
            currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
            startQrScannerWithMode(currentFacingMode);
        };

        function startQrScanner() {
            startQrScannerWithMode(currentFacingMode);
        }

        function startQrScannerWithMode(mode) {
            const statusDiv = document.getElementById('qrStatus');
            const modeText = (mode === 'user') ? 'kamera depan' : 'kamera belakang';
            statusDiv.innerHTML = `<span style="color: #0284c7;"><i class="fas fa-circle-notch fa-spin mr-1"></i> Membuka ${modeText}...</span>`;

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qrReader");
            }

            if (html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    doStartScan(mode);
                }).catch(() => {
                    doStartScan(mode);
                });
            } else {
                doStartScan(mode);
            }
        }

        function doStartScan(mode) {
            const statusDiv = document.getElementById('qrStatus');
            html5QrCode.start(
                { facingMode: mode },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                onQrCodeScanned,
                onQrCodeError
            ).catch(err => {
                const altMode = (mode === 'environment') ? 'user' : 'environment';
                const altText = (altMode === 'user') ? 'kamera depan' : 'kamera belakang';
                console.warn(`Kamera ${mode} gagal dibuka, mencoba ${altText}...`, err);
                
                html5QrCode.start(
                    { facingMode: altMode },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    onQrCodeScanned,
                    onQrCodeError
                ).catch(err2 => {
                    statusDiv.innerHTML = '<span style="color: #ef4444;"><i class="fas fa-exclamation-triangle mr-1"></i> Kamera tidak terdeteksi. Silakan gunakan tombol Upload File QR Code.</span>';
                });
            });
        }

        function stopQrScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().catch(err => console.error(err));
            }
        }

        function onQrCodeScanned(qrCodeMessage) {
            stopQrScanner();
            const statusDiv = document.getElementById('qrStatus');
            statusDiv.innerHTML = '<span style="color: #16a34a;"><i class="fas fa-spinner fa-spin mr-1"></i> QR Code terdeteksi! Memverifikasi...</span>';

            fetch('<?= BASE_URL ?>auth/login_qr', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr_token=' + encodeURIComponent(qrCodeMessage)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    statusDiv.innerHTML = `<span style="color: #16a34a; font-weight: 600;"><i class="fas fa-user-check mr-1"></i> Selamat Datang, ${data.nama}!</span>`;
                    setTimeout(() => { window.location.href = data.redirect; }, 600);
                } else {
                    statusDiv.innerHTML = `<span style="color: #ef4444;"><i class="fas fa-times-circle mr-1"></i> ${data.message}</span>`;
                    setTimeout(() => { startQrScanner(); }, 2500);
                }
            })
            .catch(err => {
                statusDiv.innerHTML = '<span style="color: #ef4444;"><i class="fas fa-times-circle mr-1"></i> Terjadi kesalahan koneksi server.</span>';
                setTimeout(() => { startQrScanner(); }, 2500);
            });
        }

        function onQrCodeError(errorMessage) {
            // Frame miss, silent
        }

        document.getElementById('qrFileInput')?.addEventListener('change', function(e) {
            if (e.target.files.length === 0) return;
            const imageFile = e.target.files[0];
            const statusDiv = document.getElementById('qrStatus');
            statusDiv.innerHTML = '<span style="color: #0284c7;"><i class="fas fa-spinner fa-spin mr-1"></i> Membaca file gambar QR Code...</span>';

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qrReader");
            }

            html5QrCode.scanFile(imageFile, true)
            .then(qrCodeMessage => {
                onQrCodeScanned(qrCodeMessage);
            })
            .catch(err => {
                statusDiv.innerHTML = '<span style="color: #ef4444;"><i class="fas fa-exclamation-triangle mr-1"></i> QR Code tidak ditemukan pada gambar.</span>';
            });
        });
    </script>
</body>
</html>