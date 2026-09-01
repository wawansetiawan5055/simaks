<?php
// app/views/landing/ekstrakurikuler_list.php
$config = $data['config'] ?? [];
$ekskul_list = $data['ekskul'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekstrakurikuler - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
    <style>
        .page-header {
            background: #1a237e;
            color: white;
            padding: 5rem 0 4rem;
            text-align: center;
        }
    </style>
</head>

<body class="bg-light">

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Ekstrakurikuler</h1>
            <p class="lead opacity-75">Wadah pengembangan bakat, minat, dan kreativitas siswa</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <?php if (empty($ekskul_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-star-half-alt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Data ekstrakurikuler belum tersedia</h4>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($ekskul_list as $idx => $ek): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="ekskul-card h-100">
                                <?php
                                $colors = ['#e8f8f5', '#eaf2fb', '#fef9e7', '#fdedec', '#f4ecf7'];
                                $icon_colors = ['#1e8449', '#1a5276', '#f39c12', '#e74c3c', '#8e44ad'];
                                $bg = $colors[$idx % 5];
                                $ic = $icon_colors[$idx % 5];
                                ?>
                                <div class="ekskul-icon" style="background:<?= $bg ?>; color:<?= $ic ?>">
                                    <i class="fas fa-<?= htmlspecialchars($ek['icon'] ?? 'star') ?>"></i>
                                </div>
                                <h5 class="fw-bold mb-3"><?= htmlspecialchars($ek['nama']) ?></h5>
                                <p class="text-muted small mb-4"><?= htmlspecialchars($ek['deskripsi'] ?? '') ?></p>

                                <div class="border-top pt-3 text-start small">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-tie text-primary me-2" style="width:16px;"></i>
                                        <span><strong>Pembina:</strong> <?= htmlspecialchars($ek['pembina'] ?? '-') ?></span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2" style="width:16px;"></i>
                                        <span><strong>Jadwal:</strong> <?= htmlspecialchars($ek['jadwal'] ?? '-') ?></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-primary me-2" style="width:16px;"></i>
                                        <span><strong>Lokasi:</strong> <?= htmlspecialchars($ek['lokasi'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include __DIR__ . '/footer_premium.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>