<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['config']['seo']['meta_title'] ?></title>
    <meta name="description" content="<?= $data['config']['seo']['meta_description'] ?>">
    <meta name="keywords" content="<?= $data['config']['seo']['meta_keywords'] ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Toast Notification CSS -->
    <link rel="stylesheet" href="assets/css/notification.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary:
                <?= $data['config']['theme']['primary_color'] ?>
            ;
            --secondary:
                <?= $data['config']['theme']['secondary_color'] ?>
            ;
            --accent:
                <?= $data['config']['theme']['accent_color'] ?>
            ;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --text-dark: #2d3748;
            --text-light: #718096;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        /* ============================================
           NAVBAR
        ============================================ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .logo-text h2 {
            font-size: 1.3rem;
            color: var(--primary);
            margin: 0;
        }

        .logo-text p {
            font-size: 0.75rem;
            color: var(--text-light);
            margin: 0;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        .nav-menu a:hover {
            color: var(--primary);
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white !important;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-login::after {
            display: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(196, 30, 58, 0.3);
        }

        /* Mobile Menu */
        .mobile-toggle {
            display: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
        }

        /* ============================================
           HERO SLIDER
        ============================================ */
        .hero-slider {
            margin-top: 80px;
            height: 600px;
            position: relative;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .slide.active {
            opacity: 1;
        }

        .slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(196, 30, 58, 0.8), rgba(45, 138, 78, 0.6));
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .slide-content {
            max-width: 800px;
            padding: 2rem;
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        }

        .slide-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .slide-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-block;
            position: relative;
            will-change: margin, box-shadow;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            margin-top: -3px;
            margin-bottom: 3px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
            background: transparent;
        }

        .btn-outline:hover {
            background: white;
            color: var(--primary);
            margin-top: -3px;
            margin-bottom: 3px;
        }

        /* Ensure PPDB buttons are always clickable */
        .btn-primary,
        .btn-outline,
        a[href*="ppdb"] {
            pointer-events: auto !important;
            cursor: pointer !important;
            opacity: 1 !important;
        }

        .btn-primary:active,
        .btn-outline:active {
            transform: scale(0.98);
        }

        .slider-nav {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s;
        }

        .slider-dot.active {
            width: 40px;
            border-radius: 10px;
            background: white;
        }

        /* ============================================
           INFO PPDB
        ============================================ */
        .ppdb-banner {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .ppdb-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .ppdb-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .ppdb-container h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .ppdb-container p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .ppdb-info-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            transition: transform 0.3s;
        }

        .info-box:hover {
            transform: translateY(-10px);
        }

        .info-box i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .info-box h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        /* ============================================
           ABOUT SECTION
        ============================================ */
        .about-section {
            padding: 5rem 2rem;
            background: var(--light);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .section-title p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .about-text h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }

        .about-text p {
            margin-bottom: 1rem;
            line-height: 1.8;
            color: var(--text-light);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-box {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* ============================================
           NEWS SECTION
        ============================================ */
        .news-section {
            padding: 5rem 2rem;
            background: white;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .news-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .news-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .news-content {
            padding: 1.5rem;
        }

        .news-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            background: var(--primary);
            color: white;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        .news-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .news-excerpt {
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .news-date {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* ============================================
           GALLERY SECTION
        ============================================ */
        .gallery-section {
            padding: 5rem 2rem;
            background: var(--light);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            height: 250px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 1rem;
            color: white;
            transform: translateY(100%);
            transition: transform 0.3s;
        }

        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }

        /* ============================================
           FOOTER
        ============================================ */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 2rem 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: var(--accent);
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--accent);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }

            .nav-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: white;
                padding: 2rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                gap: 1.5rem;
                text-align: center;
            }

            .nav-menu.active {
                display: flex;
                animation: slideDown 0.3s ease forwards;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .slide-content h1 {
                font-size: 2rem;
            }

            .slide-content p {
                font-size: 1rem;
            }

            .about-content {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-section">
                <img src="<?= BASE_URL ?>assets/img/<?= $data['profil']['logo'] ?? 'logo_sekolah.png' ?>" alt="Logo"
                    class="logo-img">
                <div class="logo-text">
                    <h2><?= $data['profil']['nama_sekolah'] ?? 'SIMAKS' ?></h2>
                    <p><?= $data['profil']['npsn'] ?? '' ?></p>
                </div>
            </div>

            <ul class="nav-menu">
                <li><a href="#home">Beranda</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#news">Berita</a></li>
                <li><a href="#gallery">Galeri</a></li>
                <li><a href="index.php?mod=landing&act=ppdb_form" class="btn-ppdb">PPDB</a></li>
                <li><a href="index.php?mod=auth&act=login" class="btn-login">Login</a></li>
            </ul>

            <div class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- HERO SLIDER -->
    <section class="hero-slider" id="home">
        <?php foreach ($data['slider_images'] as $index => $slide): ?>
            <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                <img src="<?= BASE_URL . ($slide['image_path'] ?? 'assets/img/default-slide.jpg') ?>"
                    alt="<?= $slide['title'] ?>" class="slide-image">
                <div class="slide-overlay">
                    <div class="slide-content">
                        <h1><?= $slide['title'] ?? 'Selamat Datang' ?></h1>
                        <p><?= $slide['description'] ?? $data['config']['school']['tagline'] ?></p>
                        <div class="slide-buttons">
                            <a href="index.php?mod=landing&act=ppdb_form" class="btn btn-primary">
                                <i class="fas fa-user-graduate"></i> Daftar PPDB
                            </a>
                            <a href="#about" class="btn btn-outline">
                                <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="slider-nav">
            <?php foreach ($data['slider_images'] as $index => $slide): ?>
                <div class="slider-dot <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>"></div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- PPDB BANNER -->
    <?php if ($data['config']['ppdb']['enabled']): ?>
        <section class="ppdb-banner">
            <div class="ppdb-container">
                <h2><i class="fas fa-graduation-cap"></i> Pendaftaran Peserta Didik Baru</h2>
                <p>Tahun Ajaran <?= $data['config']['ppdb']['year'] ?></p>

                <div class="ppdb-info-boxes">
                    <div class="info-box">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Periode Pendaftaran</h3>
                        <p><?= date('d M Y', strtotime($data['config']['ppdb']['start_date'])) ?> -
                            <?= date('d M Y', strtotime($data['config']['ppdb']['end_date'])) ?>
                        </p>
                    </div>
                    <div class="info-box">
                        <i class="fas fa-file-alt"></i>
                        <h3>Syarat Mudah</h3>
                        <p>Dokumen lengkap & formulir online</p>
                    </div>
                    <div class="info-box">
                        <i class="fas fa-laptop"></i>
                        <h3>Pendaftaran Online</h3>
                        <p>Daftar dari rumah, praktis & cepat</p>
                    </div>
                    <div class="info-box">
                        <i class="fas fa-check-circle"></i>
                        <h3>Cek Status</h3>
                        <p>Pantau status pendaftaran real-time</p>
                    </div>
                </div>

                <a href="index.php?mod=landing&act=ppdb_form" class="btn btn-primary"
                    style="margin-top: 2rem; display: inline-block;">
                    <i class="fas fa-edit"></i> Daftar Sekarang
                </a>
                <a href="index.php?mod=landing&act=ppdb_status" class="btn btn-outline"
                    style="margin-top: 2rem; margin-left: 1rem; display: inline-block;">
                    <i class="fas fa-search"></i> Cek Status Pendaftaran
                </a>
            </div>
        </section>
    <?php endif; ?>

    <!-- ABOUT SECTION -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="section-title">
                <h2>Tentang Kami</h2>
                <p>Mengenal lebih dekat <?= $data['profil']['nama_sekolah'] ?? 'sekolah kami' ?></p>
            </div>

            <div class="about-content">
                <div class="about-text">
                    <h3>Visi</h3>
                    <p><?= nl2br($data['profil']['visi'] ?? 'Menjadi lembaga pendidikan yang unggul dan berakhlak mulia.') ?>
                    </p>

                    <h3>Misi</h3>
                    <p><?= nl2br($data['profil']['misi'] ?? 'Memberikan pendidikan berkualitas dengan nilai-nilai karakter.') ?>
                    </p>
                </div>

                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-number" data-target="500">0</div>
                        <div class="stat-label">Siswa Aktif</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" data-target="45">0</div>
                        <div class="stat-label">Guru Profesional</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" data-target="120">0</div>
                        <div class="stat-label">Prestasi</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" data-target="15">0</div>
                        <div class="stat-label">Tahun Berdiri</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWS SECTION -->
    <section class="news-section" id="news">
        <div class="container">
            <div class="section-title">
                <h2>Berita & Pengumuman</h2>
                <p>Update terbaru dari kami</p>
            </div>

            <div class="news-grid">
                <?php foreach ($data['featured_news'] as $news): ?>
                    <div class="news-card">
                        <img src="<?= BASE_URL . ($news['featured_image'] ?? 'assets/img/default-news.jpg') ?>"
                            alt="<?= $news['title'] ?>" class="news-image">
                        <div class="news-content">
                            <span class="news-badge"><?= ucfirst($news['type']) ?></span>
                            <h3 class="news-title"><?= $news['title'] ?></h3>
                            <p class="news-excerpt"><?= $news['excerpt'] ?></p>
                            <p class="news-date">
                                <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($news['publish_date'])) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION -->
    <section class="gallery-section" id="gallery">
        <div class="container">
            <div class="section-title">
                <h2>Galeri Kegiatan</h2>
                <p>Momen berharga kami</p>
            </div>

            <div class="gallery-grid">
                <?php foreach ($data['gallery'] as $item): ?>
                    <div class="gallery-item">
                        <img src="<?= BASE_URL . ($item['image_path'] ?? 'assets/img/default-gallery.jpg') ?>"
                            alt="<?= $item['title'] ?>">
                        <div class="gallery-overlay">
                            <h4><?= $item['title'] ?></h4>
                            <p><?= $item['category'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Tentang</h3>
                <p><?= $data['profil']['nama_sekolah'] ?? 'SIMAKS' ?></p>
                <p><?= $data['profil']['alamat'] ?? '' ?></p>
            </div>

            <div class="footer-section">
                <h3>Link Cepat</h3>
                <a href="#home">Beranda</a>
                <a href="#about">Tentang</a>
                <a href="index.php?mod=landing&act=ppdb_form">PPDB</a>
                <a href="index.php?mod=auth&act=login">Login</a>
            </div>

            <div class="footer-section">
                <h3>Kontak</h3>
                <p><i class="fas fa-phone"></i> <?= $data['profil']['telepon'] ?? '-' ?></p>
                <p><i class="fas fa-envelope"></i> <?= $data['profil']['email'] ?? '-' ?></p>
                <p><i class="fas fa-globe"></i>
                    <?php
                    $website = $data['config']['school']['website'] ?? $data['profil']['website'] ?? '-';
                    if ($website != '-' && filter_var($website, FILTER_VALIDATE_URL)) {
                        echo '<a href="' . $website . '" target="_blank" class="text-white">' . parse_url($website, PHP_URL_HOST) . '</a>';
                    } else {
                        echo $website;
                    }
                    ?>
                </p>
            </div>

            <div class="footer-section">
                <h3>Ikuti Kami</h3>
                <div class="social-links">
                    <?php if (!empty($data['config']['social_media']['facebook'])): ?>
                        <a href="<?= $data['config']['social_media']['facebook'] ?>" target="_blank"><i
                                class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($data['config']['social_media']['instagram'])): ?>
                        <a href="<?= $data['config']['social_media']['instagram'] ?>" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($data['config']['social_media']['youtube'])): ?>
                        <a href="<?= $data['config']['social_media']['youtube'] ?>" target="_blank"><i
                                class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($data['config']['social_media']['twitter'])): ?>
                        <a href="<?= $data['config']['social_media']['twitter'] ?>" target="_blank"><i
                                class="fab fa-twitter"></i></a>
                    <?php endif; ?>

                    <?php if (empty($data['config']['social_media']['facebook']) && empty($data['config']['social_media']['instagram']) && empty($data['config']['social_media']['youtube']) && empty($data['config']['social_media']['twitter'])): ?>
                        <span class="text-white-50">-</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= $data['profil']['nama_sekolah'] ?? 'SIMAKS' ?>. All rights reserved. Powered
                by SIMAKS.</p>
        </div>
    </footer>

    <script>
        // Auto slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        // Auto play
        <?php if ($data['config']['landing_page']['slider_autoplay']): ?>
            setInterval(nextSlide, <?= $data['config']['landing_page']['slider_interval'] ?>);
        <?php endif; ?>

        // Dot click
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Counter animation
        const statNumbers = document.querySelectorAll('.stat-number');
        let animated = false;

        function animateCounters() {
            statNumbers.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target + '+';
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 16);
            });
        }

        window.addEventListener('scroll', () => {
            const statsSection = document.querySelector('.stats-grid');
            if (!animated && statsSection) {
                const rect = statsSection.getBoundingClientRect();
                if (rect.top < window.innerHeight - 100) {
                    animated = true;
                    animateCounters();
                }
            }
        });

        // Mobile Menu Toggle
        const mobileToggle = document.querySelector('.mobile-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (mobileToggle && navMenu) {
            mobileToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                
                // Optional: Icon animation
                const icon = mobileToggle.querySelector('i');
                if (navMenu.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target) && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    const icon = mobileToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });

            // Close menu when clicking a link
            navMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    navMenu.classList.remove('active');
                    const icon = mobileToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                });
            });
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    <!-- Toast Notification System -->
    <script src="assets/js/notification.js"></script>
    <script>
    // Pass PHP session messages to JavaScript for toast notifications
    <?php
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
</body>

</html>