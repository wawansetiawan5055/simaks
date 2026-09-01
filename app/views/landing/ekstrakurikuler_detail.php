<?php
// app/views/landing/ekstrakurikuler_detail.php
$config = $data['config'] ?? [];
$ekskul = $data['ekskul'] ?? [];
$logoPath = !empty($config['school_logo']) ? BASE_URL . $config['school_logo'] : BASE_URL . 'assets/img/logo.png';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ekskul['nama']) ?> - <?= $config['school_name'] ?? '-' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
    <style>
        .ekskul-hero {
            background: linear-gradient(rgba(30, 41, 59, 0.8), rgba(30, 41, 59, 0.8)),
                url('<?= !empty($ekskul['gambar']) ? BASE_URL . $ekskul['gambar'] : "https://source.unsplash.com/1200x500/?sports,club" ?>');
            background-size: cover;
            background-position: center;
            padding: 120px 0;
            color: white;
            text-align: center;
        }
    </style>
</head>

<body class="bg-light">

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <section class="ekskul-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($ekskul['nama']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>landing_sma" class="text-white">Beranda</a></li>
                    <li class="breadcrumb-item text-white opacity-75 active" aria-current="page">Ekstrakurikuler</li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container mt-n5 mb-5 px-4 h-100">
        <div class="row h-100 g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-md-5 h-100" style="border-radius: 20px;">
                    <h3 class="fw-bold mb-4">Mengenai <?= htmlspecialchars($ekskul['nama']) ?></h3>
                    <div class="lead text-muted" style="line-height: 1.8; text-align: justify;">
                        <?= nl2br(htmlspecialchars($ekskul['deskripsi'])) ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 h-100 text-center" style="border-radius: 20px; background: #f8fafc;">
                    <div class="mb-4">
                         <span class="bg-accent bg-opacity-10 p-4 rounded-circle d-inline-block">
                                <i class="<?= htmlspecialchars($ekskul['icon'] ?? 'fas fa-star') ?> fa-3x text-accent"></i>
                        </span>
                    </div>
                    <ul class="list-unstyled text-start mb-0">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-user-tie text-primary me-3 fa-fw"></i>
                            <div>
                                <small class="text-muted d-block">Pembina</small>
                                <span class="fw-bold"><?= htmlspecialchars($ekskul['pembina'] ?? 'TBA') ?></span>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-primary me-3 fa-fw"></i>
                            <div>
                                <small class="text-muted d-block">Jadwal</small>
                                <span class="fw-bold"><?= htmlspecialchars($ekskul['jadwal'] ?? 'TBA') ?></span>
                            </div>
                        </li>
                        <li class="mb-0 d-flex align-items-center">
                            <i class="fas fa-map-marker-alt text-primary me-3 fa-fw"></i>
                            <div>
                                <small class="text-muted d-block">Lokasi</small>
                                <span class="fw-bold"><?= htmlspecialchars($ekskul['lokasi'] ?? 'Area Sekolah') ?></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center h-100">
            <a href="<?= BASE_URL ?>landing_sma#ekskul" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
            </a>
        </div>
    </div>


    <!-- FOOTER -->
    <?php include __DIR__ . '/footer_landing.php'; ?>
</body>
</html>
