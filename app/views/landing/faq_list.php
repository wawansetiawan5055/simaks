<?php
/**
 * SIMAKS - Landing Page - FAQ View
 * Menampilkan pertanyaan yang sering ditanyakan beserta jawabannya
 */

// Default values
$faqs = $faqs ?? [];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
</head>
<body class="bg-light">
    <?php include __DIR__ . '/navbar_landing.php'; ?>
    
    <header class="page-header" style="background: #1a237e; color: white; padding: 4rem 0 2rem; text-align: center;">
        <div class="container">
            <h1 class="display-6 fw-bold mb-2">Pusat Bantuan (FAQ)</h1>
            <p class="lead opacity-75">Jawaban atas pertanyaan yang sering diajukan</p>
        </div>
    </header>

<div class="container-fluid py-5 bg-light">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-question-circle me-3"></i>
                    Pertanyaan Umum
                </h1>
                <p class="lead text-muted">
                    Temukan jawaban untuk pertanyaan yang sering ditanyakan tentang sekolah kami
                </p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </nav>

            <?php if (!empty($faqs)): ?>
                <!-- FAQ Accordion -->
                <div class="accordion shadow-sm" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?> fw-bold"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?= $index ?>"
                                    aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                    aria-controls="collapse<?= $index ?>">
                                <i class="fas fa-question-circle text-primary me-3"></i>
                                <?= htmlspecialchars($faq['pertanyaan']) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $index ?>"
                             class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                             aria-labelledby="heading<?= $index ?>"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <div class="faq-answer">
                                    <?= $faq['jawaban'] ?>
                                </div>

                                <!-- Additional Info -->
                                <?php if (!empty($faq['kategori'])): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>
                                        Kategori: <span class="badge bg-light text-dark">
                                            <?= htmlspecialchars($faq['kategori']) ?>
                                        </span>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <!-- Contact Info -->
                                <?php if (!empty($faq['kontak'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>
                                        Butuh bantuan lebih lanjut?
                                        <a href="tel:<?= htmlspecialchars($faq['kontak']) ?>"
                                           class="text-decoration-none">
                                            Hubungi kami
                                        </a>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- FAQ Categories -->
                <?php
                $categories = array_unique(array_filter(array_column($faqs, 'kategori')));
                if (!empty($categories)):
                ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 class="h4 mb-4 text-center">Kategori FAQ</h3>
                        <div class="row g-3 justify-content-center">
                            <?php foreach ($categories as $category): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="category-card text-center p-3 bg-white rounded shadow-sm">
                                    <i class="fas fa-<?= getCategoryIcon($category) ?> fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-1"><?= htmlspecialchars($category) ?></h6>
                                    <small class="text-muted">
                                        <?= count(array_filter($faqs, fn($f) => $f['kategori'] === $category)) ?> pertanyaan
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No FAQs Found -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-question-circle fa-5x text-muted"></i>
                    </div>
                    <h3 class="h4 text-muted mb-3">Belum ada FAQ tersedia</h3>
                    <p class="text-muted mb-4">
                        Pertanyaan umum akan segera diperbarui.
                    </p>
                    <a href="<?= BASE_URL ?>?mod=landing" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>

            <!-- Contact Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-primary text-white text-center">
                        <div class="card-body py-5">
                            <h4 class="mb-3">
                                <i class="fas fa-comments me-2"></i>
                                Masih Ada Pertanyaan?
                            </h4>
                            <p class="mb-4 opacity-75">
                                Tim kami siap membantu menjawab pertanyaan Anda
                            </p>
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <a href="tel:<?= htmlspecialchars($config['school_phone'] ?? '+62123456789') ?>"
                                               class="btn btn-light btn-lg w-100">
                                                <i class="fas fa-phone me-2"></i>
                                                Telepon
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="mailto:<?= htmlspecialchars($config['school_email'] ?? 'info@sekolah.sch.id') ?>"
                                               class="btn btn-outline-light btn-lg w-100">
                                                <i class="fas fa-envelope me-2"></i>
                                                Email
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="text-center">
                        <h5 class="mb-3">Informasi Lainnya</h5>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="<?= BASE_URL ?>?mod=landing&act=program_list"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Program Sekolah
                            </a>
                            <a href="<?= BASE_URL ?>?mod=landing&act=facilities_list"
                               class="btn btn-outline-success btn-sm">
                                <i class="fas fa-building me-1"></i>
                                Fasilitas
                            </a>
                            <a href="<?= BASE_URL ?>?mod=landing&act=berita_list"
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-newspaper me-1"></i>
                                Berita
                            </a>
                            <a href="<?= BASE_URL ?>?mod=landing&act=ppdb_form"
                               class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-user-plus me-1"></i>
                                PPDB
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to get category icons
function getCategoryIcon($category) {
    $icons = [
        'Pendaftaran' => 'user-plus',
        'Biaya' => 'money-bill',
        'Kurikulum' => 'book',
        'Fasilitas' => 'building',
        'Ekstrakurikuler' => 'futbol',
        'Administrasi' => 'clipboard',
        'Akademik' => 'graduation-cap',
        'Umum' => 'question-circle',
    ];

    return $icons[$category] ?? 'question-circle';
}
?>

<style>
.accordion-item {
    border-radius: 10px !important;
    overflow: hidden;
}

.accordion-button {
    background-color: #f8f9fa;
    border: none;
    padding: 1.5rem;
    font-size: 1.1rem;
}

.accordion-button:not(.collapsed) {
    background-color: #e3f2fd;
    color: #1976d2;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border: none;
}

.accordion-body {
    padding: 1.5rem;
    background-color: #fff;
}

.faq-answer {
    line-height: 1.7;
}

.faq-answer h4, .faq-answer h5 {
    color: #2c3e50;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.faq-answer p {
    margin-bottom: 1rem;
}

.faq-answer ul, .faq-answer ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.category-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.opacity-75 {
    opacity: 0.75;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }

    .accordion-button {
        padding: 1rem;
        font-size: 1rem;
    }

    .accordion-body {
        padding: 1rem;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}
</style>

<script>
// Auto-expand first FAQ item
document.addEventListener('DOMContentLoaded', function() {
    const firstAccordionButton = document.querySelector('.accordion-button');
    if (firstAccordionButton) {
        firstAccordionButton.click();
    }
});
</script>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>