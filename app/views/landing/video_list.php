<?php
// app/views/landing/video_list.php
$config = $data['config'] ?? [];
$video_list = $data['videos'] ?? [];
$kategori_list = $data['kategori_list'] ?? [];
$kategori_filter = $data['kategori_filter'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Video - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
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

        .video-iframe-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
        }

        .video-iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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
            <h1 class="display-5 fw-bold mb-2">Galeri Video</h1>
            <p class="lead opacity-75">Dokumentasi kegiatan dan profil dalam bentuk audio visual</p>
        </div>
    </header>

    <div class="container mt-n5">
        <div class="filter-card p-4 mb-5">
            <div class="text-center">
                <label class="form-label small fw-bold text-muted d-block mb-3">Filter Berdasarkan Kategori</label>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="<?= BASE_URL ?>landing/video_list"
                        class="btn <?= !$kategori_filter ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4">Semua</a>
                    <?php foreach ($kategori_list as $kat): ?>
                        <a href="<?= BASE_URL ?>landing/video_list?kategori=<?= urlencode($kat['kategori']) ?>"
                            class="btn <?= ($kategori_filter == $kat['kategori']) ? 'btn-primary' : 'btn-outline-primary' ?> rounded-pill px-4">
                            <?= htmlspecialchars($kat['kategori']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="pb-5">
        <div class="container">

            <?php if (empty($video_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-video-slash fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Video tidak ditemukan</h4>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($video_list as $v):
                        $yt_id = '';
                        if (!empty($v['youtube_id'])) {
                            $yt_id = $v['youtube_id'];
                        } elseif (!empty($v['video_url']) && strpos($v['video_url'], 'youtube') !== false || strpos($v['video_url'], 'youtu.be') !== false) {
                            // Mencari ID Youtube dari berbagai format (v=, embed/, youtu.be/)
                            if (preg_match('/(?:v=|youtu\.be\/|embed\/)([^&\s\?\/]+)/', $v['video_url'], $matches)) {
                                $yt_id = $matches[1];
                            }
                        }
                        ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="video-card h-100 bg-white shadow-sm border-0"
                                style="border-radius: 12px; overflow: hidden;">
                                <div class="video-iframe-container" style="background: #000;">
                                    <?php if ($v['tipe'] == 'youtube' && $yt_id): ?>
                                        <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($yt_id) ?>?rel=0"
                                            title="<?= htmlspecialchars($v['judul']) ?>" allowfullscreen
                                            class="w-100 h-100 border-0"></iframe>
                                    <?php elseif ($v['tipe'] == 'upload' && !empty($v['video_url'])): ?>
                                        <video controls class="w-100 h-100" style="object-fit: contain;">
                                            <source src="<?= BASE_URL . 'assets/video/' . $v['video_url'] ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                            <i class="fas fa-play-circle fa-4x opacity-25"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="video-info p-3">
                                    <span
                                        class="badge bg-danger-subtle text-danger mb-2 small"><?= htmlspecialchars($v['kategori'] ?? 'Umum') ?></span>
                                    <h6 class="fw-bold mb-1 lh-base"><?= htmlspecialchars($v['judul']) ?></h6>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($v['deskripsi'] ?? '') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer mt-auto py-4 bg-white border-top">
        <div class="container text-center">
            <p class="mb-0 small text-muted">&copy; <?= date('Y') ?>
                <?= htmlspecialchars($config['school_name'] ?? 'SMA Plus Al-Manshuriyah') ?>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>