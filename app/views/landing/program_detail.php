<?php
// app/views/landing/program_detail.php
$config = $data['config'] ?? [];
$program = $data['program'] ?? [];
$logoPath = !empty($config['school_logo']) ? BASE_URL . $config['school_logo'] : BASE_URL . 'assets/img/logo.png';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($program['title']) ?> - <?= $config['school_name'] ?? '-' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
    <style>
        .detail-hero {
            background: #1a237e; /* Navy Blue Utama */
            padding: 5rem 0 3rem;
            color: white;
            text-align: center;
        }

        .content-card {
            margin-top: -50px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-light">

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <section class="detail-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($program['title']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>landing_sma" class="text-white">Beranda</a></li>
                    <li class="breadcrumb-item text-white opacity-75 active" aria-current="page">Program</li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card content-card p-4 p-md-5">
                    <?php if (!empty($program['icon'])): ?>
                        <div class="text-center mb-4">
                            <span class="bg-primary bg-opacity-10 p-4 rounded-circle d-inline-block">
                                <i class="<?= htmlspecialchars($program['icon']) ?> fa-4x text-primary"></i>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="program-content mb-5">
                        <h3 class="fw-bold mb-4 text-center">Deskripsi Program</h3>
                        <div class="lead text-muted mb-4" style="line-height: 1.8; text-align: justify;">
                            <?= nl2br(htmlspecialchars($program['description'])) ?>
                        </div>
                    </div>

                    <?php if (!empty($data['pembina'])): ?>
                        <hr class="my-5 opacity-25">
                        <div class="row g-4 align-items-center mb-5">
                            <div class="col-md-6">
                                <h4 class="fw-bold mb-4"><i class="fas fa-user-tie text-primary me-2"></i> Pembina Program</h4>
                                <div class="d-flex align-items-center p-4 border rounded-4 bg-white shadow-sm">
                                    <img src="<?= !empty($data['pembina']['foto']) ? BASE_URL . $data['pembina']['foto'] : BASE_URL . 'assets/img/avatar.png' ?>"
                                        class="rounded-circle me-3"
                                        style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #f8f9fa;">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($data['pembina']['nama']) ?></h5>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                            <?= htmlspecialchars($data['pembina']['jabatan'] ?? 'Pembina Utama') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($data['jadwal'])): ?>
                            <div class="col-md-6">
                                <h4 class="fw-bold mb-4"><i class="fas fa-calendar-alt text-primary me-2"></i> Jadwal Kegiatan</h4>
                                <div class="p-4 border rounded-4 bg-light shadow-sm d-flex align-items-center">
                                    <div class="bg-white p-3 rounded-circle me-3 text-primary">
                                        <i class="fas fa-clock fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 small text-uppercase fw-bold">Waktu Pelaksanaan</h6>
                                        <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($data['jadwal']) ?></h5>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4 mb-4">
                        <?php if (!empty($data['agenda'])): ?>
                        <div class="col-md-6">
                            <h4 class="fw-bold mb-4"><i class="fas fa-list-check text-primary me-2"></i> Agenda & Materi</h4>
                            <div class="list-group list-group-flush rounded-4 overflow-hidden border shadow-sm">
                                <?php foreach ($data['agenda'] as $ag): ?>
                                    <div class="list-group-item p-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <span class="text-dark"><?= htmlspecialchars($ag) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($data['peserta'])): ?>
                        <div class="col-md-6">
                            <h4 class="fw-bold mb-4"><i class="fas fa-users text-primary me-2"></i> Daftar Peserta</h4>
                            <div class="p-4 border rounded-4 bg-white shadow-sm">
                                <small class="text-muted d-block mb-3">Peserta aktif tahun ajaran ini:</small>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($data['peserta'] as $p): ?>
                                        <span class="badge bg-light text-dark border fw-normal py-2 px-3 rounded-pill" style="font-size:0.85rem">
                                            <i class="fas fa-user-graduate me-1 text-primary opacity-50"></i> <?= htmlspecialchars($p) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <span class="badge bg-light text-muted border fw-normal py-2 px-3 rounded-pill italic">...dan lainnya</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($data['galeri'])): ?>
                        <hr class="my-5 opacity-25">
                        <div class="galeri-section mb-4">
                            <h4 class="fw-bold mb-4"><i class="fas fa-images text-primary me-2"></i> Galeri Kegiatan</h4>
                            <div class="row g-3">
                                <?php foreach ($data['galeri'] as $gl): ?>
                                    <div class="col-md-3 col-6">
                                        <div class="ratio ratio-1x1 overflow-hidden rounded-4 shadow-sm border">
                                            <img src="<?= BASE_URL . ($gl['file_path'] ?? $gl['foto_url'] ?? '') ?>"
                                                class="img-fluid object-fit-cover hover-zoom" alt="Kegiatan Program">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-5 text-center">
                        <a href="<?= BASE_URL ?>landing_sma#program"
                            class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- FOOTER -->
    <?php include __DIR__ . '/footer_landing.php'; ?>
</body>

</html>