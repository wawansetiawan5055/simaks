<?php
// app/views/landing/profil_sekolah.php
$config = $data['config'] ?? [];
$stats = $data['stats'] ?? [];
$identitas = $data['identitas'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah - <?= $config['school_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
    <style>
        :root {
            --primary-dark: #1a237e;
            --accent: #f59e0b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .page-header {
            background: var(--primary-dark);
            color: white;
            padding: 5rem 0 3rem;
            text-align: center;
        }

        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
        }

        .section-title.centered::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 15px;
            background: #000;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Portrait Support */
        .video-wrapper.portrait {
            padding-bottom: 177.77%; /* 9:16 */
            max-width: 340px;
            margin: 0 auto;
        }

        .stat-icon-box {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .principal-img-profile {
            width: 100%;
            max-width: 280px;
            border-radius: 20px;
            border: 8px solid white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .description-content {
            line-height: 1.9;
            color: #4b5563;
        }
    </style>
</head>

<body class="bg-light">

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-4 fw-extrabold mb-3">Profil Sekolah</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Profil</li>
                </ol>
            </nav>
        </div>
    </header>

    <!-- SECTION 1: SAMBUTAN KEPALA SEKOLAH (ATAS) -->
    <section class="py-5 bg-white overflow-hidden">
        <div class="container py-lg-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h6 class="text-accent fw-bold text-uppercase ls-1 mb-3">Sambutan Pimpinan</h6>
                    <h2 class="section-title">Membangun Masa Depan Gemilang</h2>
                    <div class="description-content fst-italic mb-4">
                        <?php if (!empty($config['headmaster_message'])): ?>
                            <?= nl2br(htmlspecialchars($config['headmaster_message'])) ?>
                        <?php else: ?>
                            <p>"Selamat datang di Website Resmi <?= htmlspecialchars($config['school_name'] ?? 'Sekolah Kami') ?>. Kami berkomitmen untuk terus berinovasi dalam mencetak generasi yang unggul dalam Imtaq dan terampil dalam Iptek."</p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-5">
                        <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 2px solid var(--accent)">
                             <?php $kepsekPath = !empty($config['headmaster_photo']) ? BASE_URL . $config['headmaster_photo'] : BASE_URL . 'assets/img/kepsek.jpg'; ?>
                             <img src="<?= $kepsekPath ?>" class="w-100 h-100 object-fit-cover" alt="Kepsek">
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($config['headmaster_name'] ?? 'Kepala Sekolah') ?></h6>
                            <small class="text-muted">Kepala <?= htmlspecialchars($config['school_name'] ?? 'Sekolah') ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <!-- Video Sambutan (Facebook SDK & Enhanced Embed) -->
                        <div id="fb-root"></div>
                        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/id_ID/sdk.js#xfbml=1&version=v18.0"></script>
                        
                        <div class="video-wrapper shadow-lg" style="background:#000; border-radius:16px; overflow:hidden;">
                            <iframe 
                                src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2F100004559353837%2Fvideos%2F1263887895726462%2F&show_text=0&width=560&autoplay=0" 
                                width="100%" 
                                height="100%" 
                                style="border:none;overflow:hidden;position:absolute;top:0;left:0;width:100%;height:100%;" 
                                scrolling="no" 
                                frameborder="0" 
                                allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" 
                                allowFullScreen="true">
                            </iframe>
                        </div>
                        <div class="position-absolute end-0 bottom-0 translate-middle-y me-n4 d-none d-lg-block">
                             <div class="bg-accent rounded-circle" style="width: 80px; height: 80px; opacity: 0.1;"></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-3 px-1">
                        <p class="mb-0 small text-muted"><i class="fas fa-play-circle me-1 text-primary"></i> Video Sambutan Kepala Sekolah</p>
                        <a href="https://www.facebook.com/100004559353837/videos/1263887895726462/" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:0.75rem;">
                            <i class="fab fa-facebook me-1"></i> Tonton di Facebook
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: DESKRIPSI LENGKAP SEKOLAH & VIDEO PROFIL -->
    <section class="py-5 bg-light">
        <div class="container py-lg-4">
            <div class="card card-custom p-4 p-md-5">
                <div class="row g-5">
                    <div class="col-lg-12">
                        <h4 class="fw-bold mb-4"><i class="fas fa-university text-accent me-2"></i> Tentang <?= htmlspecialchars($config['school_name'] ?? 'Sekolah Kami') ?></h4>
                        <div class="description-content mb-5">
                            <?= nl2br(htmlspecialchars($config['school_description'] ?? 'Sekolah kami adalah institusi pendidikan yang berdedikasi tinggi untuk memberikan pelayanan pendidikan terbaik bagi seluruh lapisan masyarakat.')) ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-8 mx-auto">
                        <div class="text-center mb-4">
                            <h5 class="fw-bold">Video Profil Sekolah</h5>
                            <hr class="mx-auto" style="width: 50px; border-top: 3px solid var(--accent)">
                        </div>
                        <!-- Video Profil Placeholder -->
                        <div class="video-wrapper portrait shadow-lg">
                            <iframe 
                                src="https://www.instagram.com/reel/DUCh7JoEdHy/embed" 
                                title="Video Profil Sekolah" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION: SEJARAH & LATAR BELAKANG (RESTORED) -->
            <div class="card card-custom p-4 p-md-5 mt-5">
                <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-history text-accent me-2"></i> Sejarah & Latar Belakang</h4>
                <div class="description-content">
                    <?= nl2br(htmlspecialchars($config['website_history'] ?? 'Sekolah ini didirikan dengan dedikasi tinggi untuk memberikan pendidikan berkualitas bagi masyarakat. Perjalanan panjang kami telah membentuk karakter lembaga yang kuat dan dipercaya oleh publik.')) ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: IDENTITAS & STATISTIK (BAWAH DESKRIPSI) -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <!-- Identitas -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="bg-primary-dark text-white px-4 py-3 rounded-top-4">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i> Identitas Sekolah</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <tbody class="small">
                                    <tr class="border-bottom">
                                        <th class="bg-light ps-4 py-3" style="width: 35%;">Nama Sekolah</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['nama_sekolah'] ?? $config['school_name'] ?? '-') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="bg-light ps-4 py-3">NPSN</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['npsn'] ?? '-') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="bg-light ps-4 py-3">Bentuk Pendidikan</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['bentuk_pendidikan'] ?? '-') ?></td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <th class="bg-light ps-4 py-3">Kurikulum</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['kurikulum'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">Alamat</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['alamat'] ?? $config['school_address'] ?? '-') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Statistik -->
                <div class="col-lg-5">
                    <div class="row g-3 h-100">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center gap-4">
                                <div class="stat-icon-box text-primary"><i class="fas fa-award"></i></div>
                                <div>
                                    <h4 class="fw-bold mb-0"><?= htmlspecialchars($config['school_accreditation'] ?? 'A (Unggul)') ?></h4>
                                    <p class="text-muted small mb-0">Akreditasi Sekolah</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                                <div class="stat-icon-box text-success mx-auto"><i class="fas fa-users"></i></div>
                                <h4 class="fw-bold mb-0"><?= number_format($stats['total_guru'] ?? 0) ?></h4>
                                <p class="text-muted small mb-0">Guru & Staff</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                                <div class="stat-icon-box text-info mx-auto"><i class="fas fa-user-graduate"></i></div>
                                <h4 class="fw-bold mb-0"><?= number_format($stats['total_siswa'] ?? 0) ?></h4>
                                <p class="text-muted small mb-0">Siswa Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: VISI & MISI -->
    <section class="py-5 bg-light">
        <div class="container py-lg-4">
            <div class="text-center mb-5">
                <h2 class="section-title centered fw-bold">Visi, Misi & Tujuan</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card card-custom h-100 p-4 border-top border-primary border-4">
                        <h4 class="fw-bold mb-3">Visi</h4>
                        <div class="text-muted small">
                            <?= nl2br(htmlspecialchars($config['school_vision'] ?? 'Menjadi sekolah unggul dalam prestasi dan luhur dalam budi pekerti.')) ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom h-100 p-4 border-top border-success border-4">
                        <h4 class="fw-bold mb-3">Misi</h4>
                        <div class="text-muted small">
                            <?= nl2br(htmlspecialchars($config['school_mission'] ?? '- Melaksanakan pembelajaran efektif.')) ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom h-100 p-4 border-top border-info border-4">
                        <h4 class="fw-bold mb-3">Tujuan</h4>
                        <div class="text-muted small">
                            <?= nl2br(htmlspecialchars($config['school_goals'] ?? '- Menghasilkan lulusan yang kompeten.')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/footer_premium.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>