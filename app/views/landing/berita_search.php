<?php
/**
 * SIMAKS - Landing Page - Berita Search View
 * Menampilkan hasil pencarian berita
 */

// Default values
$search_query = $search_query ?? '';
$search_results = $search_results ?? [];
$total_results = $total_results ?? 0;
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
            <h1 class="display-6 fw-bold mb-2">Pencarian Berita</h1>
            <p class="lead opacity-75">Temukan informasi yang Anda butuhkan</p>
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
                        <i class="fas fa-search me-2"></i>
                        Hasil Pencarian
                    </h1>
                    <p class="text-muted mb-0">
                        <?php if ($search_query): ?>
                            Menampilkan hasil untuk: "<strong><?= htmlspecialchars($search_query) ?></strong>"
                        <?php else: ?>
                            Silakan masukkan kata kunci pencarian
                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-muted">
                    <small>
                        <?php if ($total_results > 0): ?>
                            Ditemukan <?= number_format($total_results) ?> berita
                        <?php else: ?>
                            Tidak ada hasil
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing&act=berita_list">Berita</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pencarian</li>
                </ol>
            </nav>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>?mod=landing&act=berita_search">
                        <input type="hidden" name="mod" value="landing">
                        <input type="hidden" name="act" value="berita_search">
                        <div class="row g-3">
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="q" class="form-control form-control-lg"
                                        placeholder="Cari berita..." value="<?= htmlspecialchars($search_query) ?>"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-1"></i>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($search_query): ?>
                <?php if (!empty($search_results)): ?>
                    <!-- Search Results -->
                    <div class="search-results">
                        <?php foreach ($search_results as $berita): ?>
                            <article class="card mb-4 shadow-sm search-result-item">
                                <div class="row g-0">
                                    <?php if ($berita['gambar']): ?>
                                        <div class="col-md-4">
                                            <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($berita['gambar']) ?>"
                                                class="card-img-left h-100" alt="<?= htmlspecialchars($berita['judul']) ?>"
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

                                            <!-- Title with highlighting -->
                                            <h3 class="card-title h5 mb-2">
                                                <a href="<?= BASE_URL ?>?mod=landing&act=berita_detail&id=<?= $berita['id'] ?>"
                                                    class="text-decoration-none text-dark fw-bold">
                                                    <?= highlightSearchTerm($berita['judul'], $search_query) ?>
                                                </a>
                                            </h3>

                                            <!-- Excerpt with highlighting -->
                                            <p class="card-text text-muted mb-2">
                                                <?= highlightSearchTerm($berita['excerpt'] ?: substr(strip_tags($berita['konten']), 0, 200), $search_query) ?>...
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

                    <!-- Search Stats -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Ditemukan <strong><?= number_format($total_results) ?></strong> berita yang sesuai dengan kata kunci
                        "<strong><?= htmlspecialchars($search_query) ?></strong>".
                        <?php if ($total_results > count($search_results)): ?>
                            Menampilkan <?= count($search_results) ?> hasil pertama.
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <!-- No Results Found -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search fa-4x text-muted"></i>
                        </div>
                        <h3 class="h5 text-muted mb-3">
                            Tidak ada berita ditemukan
                        </h3>
                        <p class="text-muted mb-4">
                            Tidak ada berita yang sesuai dengan kata kunci
                            "<strong><?= htmlspecialchars($search_query) ?></strong>".
                        </p>

                        <!-- Suggestions -->
                        <div class="suggestions mb-4">
                            <h6 class="text-muted mb-3">Coba saran berikut:</h6>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <a href="<?= BASE_URL ?>?mod=landing&act=berita_list" class="btn btn-outline-primary btn-sm">
                                    Lihat Semua Berita
                                </a>
                                <a href="<?= BASE_URL ?>?mod=landing&act=berita_by_category&kategori=Pengumuman"
                                    class="btn btn-outline-secondary btn-sm">
                                    Kategori Pengumuman
                                </a>
                                <a href="<?= BASE_URL ?>?mod=landing&act=berita_by_category&kategori=Kegiatan"
                                    class="btn btn-outline-secondary btn-sm">
                                    Kategori Kegiatan
                                </a>
                            </div>
                        </div>

                        <!-- Alternative Search -->
                        <div class="alternative-search">
                            <p class="text-muted mb-2">Atau coba kata kunci lain:</p>
                            <form method="GET" action="<?= BASE_URL ?>?mod=landing&act=berita_search" class="d-inline">
                                <input type="hidden" name="mod" value="landing">
                                <input type="hidden" name="act" value="berita_search">
                                <div class="input-group" style="max-width: 300px; margin: 0 auto;">
                                    <input type="text" name="q" class="form-control" placeholder="Kata kunci baru..." required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Search Query -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-keyboard fa-4x text-muted"></i>
                    </div>
                    <h3 class="h5 text-muted mb-3">
                        Masukkan kata kunci pencarian
                    </h3>
                    <p class="text-muted mb-4">
                        Silakan masukkan kata kunci untuk mencari berita.
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Popular Keywords -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Kata Kunci Populer</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        $popular_keywords = ['PPDB', 'Pengumuman', 'Kegiatan', 'Prestasi', 'Kurikulum', 'Ekstrakurikuler'];
                        foreach ($popular_keywords as $keyword):
                            ?>
                            <a href="<?= BASE_URL ?>?mod=landing&act=berita_search&q=<?= urlencode($keyword) ?>"
                                class="badge bg-light text-dark text-decoration-none">
                                <?= htmlspecialchars($keyword) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Categories Widget -->
            <?php if (!empty($categories ?? [])): ?>
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
            <?php if (!empty($recent_news ?? [])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Berita Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recent_news as $news): ?>
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <?php if ($news['gambar']): ?>
                                    <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($news['gambar']) ?>"
                                        alt="<?= htmlspecialchars($news['judul']) ?>" class="me-3 rounded"
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

            <!-- Search Tips -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips Pencarian</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Gunakan kata kunci spesifik
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Coba variasi kata (contoh: "PPDB" atau "pendaftaran")
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Periksa ejaan kata kunci
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Gunakan kategori untuk hasil lebih tepat
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to highlight search terms
function highlightSearchTerm($text, $searchTerm)
{
    if (empty($searchTerm)) {
        return htmlspecialchars($text);
    }

    $searchTerm = preg_quote($searchTerm, '/');
    $highlighted = preg_replace('/(' . $searchTerm . ')/i', '<mark class="bg-warning">$1</mark>', htmlspecialchars($text));

    return $highlighted;
}
?>

<style>
    .search-result-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .search-result-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .card-img-left {
        border-radius: 0;
    }

    mark.bg-warning {
        background-color: #fff3cd !important;
        color: #856404 !important;
        padding: 2px 4px;
        border-radius: 3px;
    }

    .suggestions .badge {
        transition: all 0.2s ease;
    }

    .suggestions .badge:hover {
        background-color: #007bff !important;
        color: white !important;
    }

    @media (max-width: 768px) {
        .search-result-item .row>div {
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