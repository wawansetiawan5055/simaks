<?php
/**
 * SIMAKS - Landing Page - Berita Detail View
 * Menampilkan detail artikel berita lengkap dengan fitur sharing dan artikel terkait
 */

// Cek apakah data berita tersedia
if (!isset($berita) || empty($berita)) {
    header('Location: ' . BASE_URL . '?mod=landing&act=berita_list');
    exit;
}

// Ambil data terkait
$berita_id = $berita['id'];
$judul = $berita['judul'];
$konten = $berita['konten'];
$excerpt = $berita['excerpt'];
$gambar = $berita['gambar'];
$penulis = $berita['penulis'];
$tanggal = date('d M Y', strtotime($berita['tanggal'] ?? 'now'));
$kategori = $berita['kategori_nama'];
$slug = $berita['slug'];
$view_count = $berita['view_count'];

// URL untuk sharing
$current_url = BASE_URL . '?mod=landing&act=berita_detail&id=' . $berita_id;
$share_title = urlencode($judul);
$share_url = urlencode($current_url);

// Meta tags untuk SEO
$page_title = $judul . ' - ' . $config['school_name'];
$page_description = $excerpt ?: substr(strip_tags($konten), 0, 160);
$page_image = $gambar ? BASE_URL . 'uploads/landing/' . $gambar : BASE_URL . 'assets/images/default-news.jpg';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul) ?> - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
</head>
<body class="bg-light">
    <?php include __DIR__ . '/navbar_landing.php'; ?>
    
    <header class="page-header" style="background: #1a237e; color: white; padding: 4rem 0 2rem; text-align: center;">
        <div class="container">
            <h1 class="display-6 fw-bold mb-2">Detail Berita</h1>
            <p class="lead opacity-75">Baca informasi selengkapnya</p>
        </div>
    </header>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing&act=berita_list">Berita</a></li>
                    <?php if ($kategori): ?>
                        <li class="breadcrumb-item"><a
                                href="<?= BASE_URL ?>?mod=landing&act=berita_by_category&kategori=<?= urlencode($kategori) ?>"><?= htmlspecialchars($kategori) ?></a>
                        </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page">Detail Berita</li>
                </ol>
            </nav>

            <!-- Article Header -->
            <article class="news-detail">
                <?php if ($gambar): ?>
                    <div class="article-image mb-4">
                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($gambar) ?>"
                            alt="<?= htmlspecialchars($judul) ?>" class="img-fluid rounded shadow">
                    </div>
                <?php endif; ?>

                <header class="article-header mb-4">
                    <h1 class="article-title display-5 fw-bold text-primary mb-3">
                        <?= htmlspecialchars($judul) ?>
                    </h1>

                    <div class="article-meta d-flex flex-wrap align-items-center gap-3 mb-3">
                        <span class="meta-item">
                            <i class="fas fa-calendar-alt text-muted me-1"></i>
                            <?= $tanggal ?>
                        </span>
                        <?php if ($penulis): ?>
                            <span class="meta-item">
                                <i class="fas fa-user text-muted me-1"></i>
                                <?= htmlspecialchars($penulis) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($kategori): ?>
                            <span class="meta-item">
                                <i class="fas fa-tag text-muted me-1"></i>
                                <a href="<?= BASE_URL ?>?mod=landing&act=berita_by_category&kategori=<?= urlencode($kategori) ?>"
                                    class="text-decoration-none">
                                    <?= htmlspecialchars($kategori) ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        <span class="meta-item">
                            <i class="fas fa-eye text-muted me-1"></i>
                            <?= number_format($view_count) ?> views
                        </span>
                    </div>

                    <?php if ($excerpt): ?>
                        <div class="article-excerpt lead text-muted mb-4">
                            <?= htmlspecialchars($excerpt) ?>
                        </div>
                    <?php endif; ?>
                </header>

                <!-- Article Content -->
                <div class="article-content mb-5">
                    <div class="content-wrapper">
                        <?= $konten ?>
                    </div>
                </div>

                <!-- Article Footer -->
                <footer class="article-footer">
                    <!-- Tags -->
                    <?php if (!empty($berita['tags'])): ?>
                        <div class="article-tags mb-4">
                            <h6 class="text-muted mb-2">Tags:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (explode(',', $berita['tags']) as $tag): ?>
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars(trim($tag)) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Share Buttons -->
                    <div class="article-share mb-4">
                        <h6 class="text-muted mb-3">Bagikan Artikel:</h6>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" target="_blank"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f me-1"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= $share_title ?>&url=<?= $share_url ?>"
                                target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fab fa-twitter me-1"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?= $share_title ?>%20<?= $share_url ?>" target="_blank"
                                class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <button onclick="copyToClipboard('<?= $current_url ?>')"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-copy me-1"></i> Copy Link
                            </button>
                        </div>
                    </div>

                    <!-- Author Bio -->
                    <?php if ($penulis && !empty($berita['author_bio'])): ?>
                        <div class="author-bio bg-light p-3 rounded mb-4">
                            <div class="d-flex align-items-center">
                                <div class="author-avatar me-3">
                                    <i class="fas fa-user-circle fa-3x text-muted"></i>
                                </div>
                                <div class="author-info">
                                    <h6 class="mb-1">Tentang Penulis</h6>
                                    <p class="mb-0 text-muted small">
                                        <?= htmlspecialchars($berita['author_bio']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>

            <!-- Related Articles -->
            <?php if (!empty($related_articles)): ?>
                <div class="related-articles mt-5">
                    <h3 class="h4 mb-4">Artikel Terkait</h3>
                    <div class="row">
                        <?php foreach ($related_articles as $related): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <?php if ($related['gambar']): ?>
                                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($related['gambar']) ?>"
                                            class="card-img-top" alt="<?= htmlspecialchars($related['judul']) ?>"
                                            style="height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="<?= BASE_URL ?>?mod=landing&act=berita_detail&id=<?= $related['id'] ?>"
                                                class="text-decoration-none">
                                                <?= htmlspecialchars($related['judul']) ?>
                                            </a>
                                        </h6>
                                        <p class="card-text small text-muted">
                                            <?= htmlspecialchars(substr($related['excerpt'] ?: strip_tags($related['konten']), 0, 100)) ?>...
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?= date('d M Y', strtotime($related['tanggal'] ?? 'now')) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments Section (Optional) -->
            <div class="comments-section mt-5" id="comments">
                <h4 class="mb-4">Komentar</h4>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Fitur komentar akan segera hadir.
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Search Widget -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Cari Berita</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>?mod=landing&act=berita_search" method="GET">
                        <input type="hidden" name="mod" value="landing">
                        <input type="hidden" name="act" value="berita_search">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Kata kunci..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Categories Widget -->
            <?php if (!empty($categories)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Kategori</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($categories as $cat): ?>
                                <li class="list-group-item px-0">
                                    <a href="<?= BASE_URL ?>?mod=landing&act=berita_by_category&kategori=<?= urlencode($cat['nama']) ?>"
                                        class="text-decoration-none d-flex justify-content-between align-items-center">
                                        <span><?= htmlspecialchars($cat['nama']) ?></span>
                                        <span class="badge bg-primary rounded-pill"><?= $cat['total'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent News Widget -->
            <?php if (!empty($recent_news)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Berita Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recent_news as $news): ?>
                            <div class="d-flex mb-3">
                                <?php if ($news['gambar']): ?>
                                    <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($news['gambar']) ?>"
                                        alt="<?= htmlspecialchars($news['judul']) ?>" class="me-3 rounded"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="<?= BASE_URL ?>?mod=landing&act=berita_detail&id=<?= $news['id'] ?>"
                                            class="text-decoration-none small">
                                            <?= htmlspecialchars(substr($news['judul'], 0, 50)) ?>...
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('d M Y', strtotime($news['tanggal'] ?? 'now')) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Info Widget -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Hubungi Kami</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        <?= htmlspecialchars($config['school_address'] ?? 'Alamat Sekolah') ?>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <?= htmlspecialchars($config['school_phone'] ?? 'Telepon Sekolah') ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <?= htmlspecialchars($config['school_email'] ?? 'Email Sekolah') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            // Show success message
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');

            setTimeout(function () {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);
        }).catch(function (err) {
            console.error('Failed to copy: ', err);
            alert('Gagal menyalin link. Silakan copy manual: ' + text);
        });
    }

    // Auto-increment view count (optional - could be done server-side)
    document.addEventListener('DOMContentLoaded', function () {
        // Could add AJAX call here to increment view count if not done server-side
    });
</script>

<style>
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .article-content h2,
    .article-content h3,
    .article-content h4 {
        color: #2c3e50;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .article-content p {
        line-height: 1.7;
        margin-bottom: 1rem;
    }

    .article-content ul,
    .article-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }

    .article-content blockquote {
        border-left: 4px solid #007bff;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6c757d;
    }

    .meta-item {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .article-share .btn {
        border-radius: 20px;
    }

    @media (max-width: 768px) {
        .article-title {
            font-size: 2rem;
        }

        .article-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>