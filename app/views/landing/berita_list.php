<?php
/**
 * SIMAKS - Landing Page - Berita List View
 * Menampilkan daftar berita dengan pagination, pencarian, dan filter kategori
 */

// Default values
$current_page = $current_page ?? 1;
$total_pages = $total_pages ?? 1;
$search_query = $search_query ?? '';
$category_filter = $category_filter ?? '';
$berita_list = $berita_list ?? [];
$categories = $categories ?? [];

// Meta tags untuk SEO
$page_title = 'Berita Terbaru - ' . $config['school_name'];
$page_description = 'Berita dan informasi terbaru dari ' . $config['school_name'];
$page_image = BASE_URL . 'assets/images/default-news.jpg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Berita' ?> - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
</head>
<body class="bg-light">
    <?php include __DIR__ . '/navbar_landing.php'; ?>
    
    <header class="page-header" style="background: #1a237e; color: white; padding: 4rem 0 2rem; text-align: center;">
        <div class="container">
            <h1 class="display-6 fw-bold mb-2">Berita & Informasi</h1>
            <p class="lead opacity-75">Kumpulan kabar terbaru dari lingkungan sekolah</p>
        </div>
    </header>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 fw-bold text-primary mb-1">
                        <i class="fas fa-newspaper me-2"></i>
                        Berita Terbaru
                    </h1>
                    <p class="text-muted mb-0">
                        Informasi terkini dan berita penting dari sekolah
                    </p>
                </div>
                <div class="text-muted">
                    <small>Total: <?= number_format($total_berita ?? 0) ?> berita</small>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>?mod=landing&act=berita_list" class="row g-3">
                        <input type="hidden" name="mod" value="landing">
                        <input type="hidden" name="act" value="berita_list">

                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="q" class="form-control"
                                       placeholder="Cari berita..."
                                       value="<?= htmlspecialchars($search_query) ?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <select name="kategori" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['nama']) ?>"
                                        <?= $category_filter === $cat['nama'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nama']) ?> (<?= $cat['total'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Filters Display -->
            <?php if ($search_query || $category_filter): ?>
            <div class="mb-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted">Filter aktif:</span>
                    <?php if ($search_query): ?>
                    <span class="badge bg-info">
                        Pencarian: "<?= htmlspecialchars($search_query) ?>"
                        <a href="<?= BASE_URL ?>?mod=landing&act=berita_list<?= $category_filter ? '&kategori=' . urlencode($category_filter) : '' ?>"
                           class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    <?php endif; ?>
                    <?php if ($category_filter): ?>
                    <span class="badge bg-success">
                        Kategori: <?= htmlspecialchars($category_filter) ?>
                        <a href="<?= BASE_URL ?>?mod=landing&act=berita_list<?= $search_query ? '&q=' . urlencode($search_query) : '' ?>"
                           class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>?mod=landing&act=berita_list" class="text-decoration-none small">
                        Hapus semua filter
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- News List -->
            <?php if (!empty($berita_list)): ?>
                <div class="news-list">
                    <?php foreach ($berita_list as $berita): ?>
                    <article class="card mb-4 shadow-sm news-item">
                        <div class="row g-0">
                            <?php if ($berita['gambar']): ?>
                            <div class="col-md-4">
                                <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($berita['gambar']) ?>"
                                     class="card-img-left h-100"
                                     alt="<?= htmlspecialchars($berita['judul']) ?>"
                                     style="object-fit: cover; min-height: 200px;">
                            </div>
                            <?php endif; ?>

                            <div class="col-md-<?= $berita['gambar'] ? '8' : '12' ?>">
                                <div class="card-body">
                                    <!-- Category Badge -->
                                    <?php if ($berita['kategori_nama']): ?>
                                    <span class="badge bg-primary mb-2">
                                        <?= htmlspecialchars($berita['kategori_nama']) ?>
                                    </span>
                                    <?php endif; ?>

                                    <!-- Title -->
                                    <h3 class="card-title h5 mb-2">
                                        <a href="<?= BASE_URL ?>?mod=landing&act=berita_detail&id=<?= $berita['id'] ?>"
                                           class="text-decoration-none text-dark fw-bold">
                                            <?= htmlspecialchars($berita['judul']) ?>
                                        </a>
                                    </h3>

                                    <!-- Excerpt -->
                                    <p class="card-text text-muted mb-2">
                                        <?= htmlspecialchars($berita['excerpt'] ?: substr(strip_tags($berita['konten']), 0, 150)) ?>...
                                    </p>

                                    <!-- Meta Information -->
                                    <div class="d-flex align-items-center gap-3 mb-2 text-muted small">
                                        <span>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?= date('d M Y', strtotime($berita['tanggal'] ?? 'now')) ?>
                                        </span>
                                        <?php if ($berita['penulis']): ?>
                                        <span>
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($berita['penulis']) ?>
                                        </span>
                                        <?php endif; ?>
                                        <span>
                                            <i class="fas fa-eye me-1"></i>
                                            <?= number_format($berita['view_count']) ?> views
                                        </span>
                                    </div>

                                    <!-- Read More Button -->
                                    <a href="<?= BASE_URL ?>?mod=landing&act=berita_detail&id=<?= $berita['id'] ?>"
                                       class="btn btn-outline-primary btn-sm">
                                        Baca Selengkapnya
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="News pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($current_page - 1, $search_query, $category_filter) ?>">
                                <i class="fas fa-chevron-left"></i> Sebelumnya
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i> Sebelumnya
                            </span>
                        </li>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl(1, $search_query, $category_filter) ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= buildPaginationUrl($i, $search_query, $category_filter) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($total_pages, $search_query, $category_filter) ?>">
                                <?= $total_pages ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= buildPaginationUrl($current_page + 1, $search_query, $category_filter) ?>">
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

            <?php else: ?>
                <!-- No News Found -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-newspaper fa-4x text-muted"></i>
                    </div>
                    <h3 class="h5 text-muted mb-3">
                        <?php if ($search_query || $category_filter): ?>
                        Tidak ada berita ditemukan
                        <?php else: ?>
                        Belum ada berita
                        <?php endif; ?>
                    </h3>
                    <p class="text-muted mb-4">
                        <?php if ($search_query || $category_filter): ?>
                        Coba ubah kata kunci pencarian atau filter kategori Anda.
                        <?php else: ?>
                        Berita akan segera dipublikasikan.
                        <?php endif; ?>
                    </p>
                    <?php if ($search_query || $category_filter): ?>
                    <a href="<?= BASE_URL ?>?mod=landing&act=berita_list" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i>
                        Lihat Semua Berita
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Search Widget -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
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
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Kategori Berita</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <a href="<?= BASE_URL ?>?mod=landing&act=berita_list"
                               class="text-decoration-none d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-list me-2"></i>Semua Kategori</span>
                                <span class="badge bg-secondary rounded-pill">
                                    <?= array_sum(array_column($categories, 'total')) ?>
                                </span>
                            </a>
                        </li>
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
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Berita Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($recent_news as $news): ?>
                    <div class="d-flex mb-3 pb-3 border-bottom">
                        <?php if ($news['gambar']): ?>
                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($news['gambar']) ?>"
                             alt="<?= htmlspecialchars($news['judul']) ?>"
                             class="me-3 rounded"
                             style="width: 60px; height: 60px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="<?= BASE_URL ?>?mod=landing&act=berita_detail&id=<?= $news['id'] ?>"
                                   class="text-decoration-none">
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

            <!-- Newsletter Signup -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Newsletter</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-3">
                        Dapatkan berita terbaru langsung ke email Anda.
                    </p>
                    <form>
                        <div class="input-group mb-2">
                            <input type="email" class="form-control" placeholder="Email Anda" required>
                            <button class="btn btn-warning" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Fitur akan segera hadir.
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function for pagination URLs
function buildPaginationUrl($page, $search = '', $category = '') {
    $url = BASE_URL . '?mod=landing&act=berita_list&page=' . $page;
    if ($search) {
        $url .= '&q=' . urlencode($search);
    }
    if ($category) {
        $url .= '&kategori=' . urlencode($category);
    }
    return $url;
}
?>

<style>
.news-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.news-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.card-img-left {
    border-radius: 0;
}

.pagination .page-link {
    color: #007bff;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .news-item .row > div {
        margin-bottom: 1rem;
    }

    .card-img-left {
        min-height: 150px !important;
    }
}
</style>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>