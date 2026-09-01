<?php
/**
 * SIMAKS - Landing Page - Berita by Category View
 * Menampilkan berita berdasarkan kategori tertentu
 */

// Default values
$category_name = $category_name ?? '';
$category_berita = $category_berita ?? [];
$total_berita = $total_berita ?? 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori: <?= htmlspecialchars($category_name) ?> - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
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
            <p class="lead opacity-75">Kategori: <?= htmlspecialchars($category_name) ?></p>
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
                        <i class="fas fa-tag me-2"></i>
                        Kategori: <?= htmlspecialchars($category_name) ?>
                    </h1>
                    <p class="text-muted mb-0">
                        Berita dalam kategori <?= htmlspecialchars($category_name) ?>
                    </p>
                </div>
                <div class="text-muted">
                    <small>Total: <?= number_format($total_berita) ?> berita</small>
                </div>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing&act=berita_list">Berita</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Kategori: <?= htmlspecialchars($category_name) ?>
                    </li>
                </ol>
            </nav>

            <!-- Category Info Card -->
            <div class="card mb-4 border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title text-primary mb-2">
                                <i class="fas fa-tag me-2"></i>
                                <?= htmlspecialchars($category_name) ?>
                            </h5>
                            <p class="card-text mb-0">
                                Kumpulan berita dan informasi terkait <?= htmlspecialchars($category_name) ?> di sekolah
                                kami.
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="category-icon mb-2">
                                <i class="fas fa-<?= getCategoryIcon($category_name) ?> fa-3x text-primary"></i>
                            </div>
                            <span class="badge bg-primary fs-6">
                                <?= number_format($total_berita) ?> Berita
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($category_berita)): ?>
                <!-- News List -->
                <div class="news-list">
                    <?php foreach ($category_berita as $berita): ?>
                        <article class="card mb-4 shadow-sm news-item">
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

            <?php else: ?>
                <!-- No News in Category -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-tag fa-4x text-muted"></i>
                    </div>
                    <h3 class="h5 text-muted mb-3">
                        Belum ada berita dalam kategori ini
                    </h3>
                    <p class="text-muted mb-4">
                        Kategori "<?= htmlspecialchars($category_name) ?>" belum memiliki berita.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= BASE_URL ?>?mod=landing&act=berita_list" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i>
                            Lihat Semua Berita
                        </a>
                        <a href="<?= BASE_URL ?>?mod=landing" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-1"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
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
                                        class="text-decoration-none d-flex justify-content-between align-items-center
                               <?= $cat['nama'] === $category_name ? 'bg-light' : '' ?>">
                                        <span>
                                            <i class="fas fa-tag me-2"></i>
                                            <?= htmlspecialchars($cat['nama']) ?>
                                            <?php if ($cat['nama'] === $category_name): ?>
                                                <i class="fas fa-check text-success ms-1"></i>
                                            <?php endif; ?>
                                        </span>
                                        <span class="badge bg-primary rounded-pill"><?= $cat['total'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Categories -->
            <?php
            $related_categories = array_filter($categories ?? [], fn($cat) => $cat['nama'] !== $category_name);
            if (!empty($related_categories)):
                ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i>Kategori Lain</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php foreach (array_slice($related_categories, 0, 6) as $cat): ?>
                                <div class="col-6">
                                    <a href="<?= BASE_URL ?>?mod=landing&act=berita_by_category&kategori=<?= urlencode($cat['nama']) ?>"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        <?= htmlspecialchars($cat['nama']) ?>
                                        <span class="badge bg-primary ms-1"><?= $cat['total'] ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent News Widget -->
            <?php if (!empty($recent_news ?? [])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
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

            <!-- Category Stats -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Kategori</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-<?= getCategoryIcon($category_name) ?> fa-3x text-primary"></i>
                    </div>
                    <h4 class="text-primary mb-1"><?= number_format($total_berita) ?></h4>
                    <p class="text-muted mb-0">Total berita di kategori ini</p>

                    <?php if (!empty($categories)): ?>
                        <hr class="my-3">
                        <small class="text-muted">
                            Dari total <?= number_format(array_sum(array_column($categories, 'total'))) ?> berita
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to get category icons
function getCategoryIcon($category)
{
    $icons = [
        'Pengumuman' => 'bullhorn',
        'Kegiatan' => 'calendar-alt',
        'Prestasi' => 'trophy',
        'Kurikulum' => 'book',
        'PPDB' => 'user-plus',
        'Ekstrakurikuler' => 'futbol',
        'Administrasi' => 'clipboard',
        'Umum' => 'info-circle',
        'Berita' => 'newspaper',
        'Event' => 'calendar-check',
    ];

    return $icons[$category] ?? 'tag';
}
?>

<style>
    .news-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .news-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .card-img-left {
        border-radius: 0;
    }

    .category-icon {
        opacity: 0.8;
    }

    .list-group-item.bg-light {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
    }

    .btn-outline-primary .badge {
        background-color: #007bff !important;
        color: white !important;
    }

    @media (max-width: 768px) {
        .news-item .row>div {
            margin-bottom: 1rem;
        }

        .card-img-left {
            min-height: 150px !important;
        }

        .btn-outline-primary {
            margin-bottom: 0.5rem;
        }
    }
</style>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>