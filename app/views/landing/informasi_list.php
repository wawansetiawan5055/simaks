<?php
// app/views/landing/informasi_list.php
$config = $data['config'] ?? [];
$informasi = $data['informasi'] ?? [];
$kategori_list = $data['kategori_list'] ?? [];
$kategori_filter = $data['kategori_filter'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi & Pengumuman - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
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
            padding: 5rem 0 3rem;
            text-align: center;
        }

        .info-card-list {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-light">

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Informasi & Pengumuman</h1>
            <p class="lead opacity-75">Update terbaru seputar kegiatan dan kebijakan sekolah</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Kategori</h6>
                            <div class="list-group list-group-flush border-0">
                                <a href="<?= BASE_URL ?>landing/informasi_list"
                                    class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center <?= !$kategori_filter ? 'text-primary fw-bold' : '' ?>">
                                    Semua Informasi <i class="fas fa-chevron-right small opacity-50"></i>
                                </a>
                                <?php foreach ($kategori_list as $kat): ?>
                                    <a href="<?= BASE_URL ?>landing/informasi_list?kategori=<?= urlencode($kat['kategori']) ?>"
                                        class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center <?= ($kategori_filter == $kat['kategori']) ? 'text-primary fw-bold' : '' ?>">
                                        <?= htmlspecialchars($kat['kategori']) ?> <i
                                            class="fas fa-chevron-right small opacity-50"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-primary text-white shadow-sm rounded-4">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-bell fa-3x mb-3 opacity-50"></i>
                            <h5>Butuh Bantuan?</h5>
                            <p class="small opacity-75 mb-0">Hubungi sekretariat sekolah untuk informasi lebih lanjut.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="info-card-list">
                        <?php if (empty($informasi)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-4x text-muted mb-3 opacity-25"></i>
                                <h4 class="text-muted">Belum ada informasi yang dipublikasikan</h4>
                            </div>
                        <?php else: ?>
                            <?php foreach ($informasi as $idx => $info): ?>
                                <div class="info-item p-4 mb-4 <?= ($info['is_featured'] ?? 0) ? 'featured' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold text-primary mb-0">
                                            <i
                                                class="fas fa-<?= htmlspecialchars($info['icon'] ?? 'info-circle') ?> me-2 text-warning"></i>
                                            <?= htmlspecialchars($info['judul']) ?>
                                        </h5>
                                        <?php if ($info['is_featured']): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-2 small">Penting</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted mb-3"><?= nl2br(htmlspecialchars($info['konten'])) ?></p>
                                    <div class="d-flex align-items-center gap-4 small text-muted">
                                        <span><i class="far fa-calendar-alt me-1 text-primary"></i>
                                            <?= date('d M Y', strtotime($info['tanggal_publikasi'] ?? 'now')) ?></span>
                                        <span><i class="far fa-folder-open me-1 text-primary"></i>
                                            <?= htmlspecialchars($info['kategori'] ?? 'Umum') ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/footer_premium.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>