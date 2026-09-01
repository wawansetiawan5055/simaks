<?php
// app/views/landing/gallery_list.php
$config = $data['config'] ?? [];
$gallery_list = $data['gallery'] ?? [];
$kategori_list = $data['kategori_list'] ?? [];
$kategori_filter = $data['kategori_filter'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto -
        <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?>
    </title>
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

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            cursor: pointer;
        }

        .gallery-item img {
            transition: transform 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            padding: 20px;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .filter-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-top: -2.5rem;
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body class="bg-light">

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Galeri Foto</h1>
            <p class="lead opacity-75">Dokumentasi kegiatan dan momen penting di sekolah</p>
        </div>
    </header>

    <div class="container mt-n5">
        <div class="filter-card p-4 mb-5">
            <div class="text-center">
                <label class="form-label small fw-bold text-muted d-block mb-3">Filter Berdasarkan Kategori</label>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="<?= BASE_URL ?>landing/gallery"
                        class="btn <?= !$kategori_filter ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4">Semua</a>
                    <?php foreach ($kategori_list as $kat): ?>
                        <a href="<?= BASE_URL ?>landing/gallery?kategori=<?= urlencode($kat['category']) ?>"
                            class="btn <?= ($kategori_filter == $kat['category']) ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4">
                            <?= htmlspecialchars($kat['category']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="pb-5">
        <div class="container">

            <?php if (empty($gallery_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Foto tidak ditemukan</h4>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($gallery_list as $gal): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item h-100 shadow-sm" style="min-height: 250px;">
                                <a href="<?= BASE_URL . $gal['image_path'] ?>" target="_blank" class="d-block w-100 h-100">
                                    <img src="<?= BASE_URL . $gal['image_path'] ?>" alt="<?= htmlspecialchars($gal['title']) ?>"
                                        class="w-100 h-100 object-fit-cover">
                                    <div class="gallery-overlay">
                                        <span class="badge bg-primary mb-2">
                                            <?= htmlspecialchars($gal['category']) ?>
                                        </span>
                                        <h5 class="fw-bold mb-0 text-truncate">
                                            <?= htmlspecialchars($gal['title']) ?>
                                        </h5>
                                    </div>
                                </a>
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