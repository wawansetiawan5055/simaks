<?php
/**
 * SIMAKS - Landing Page - Sambutan Kepala Sekolah View
 * Menampilkan sambutan resmi dari kepala sekolah
 */

// Cek apakah data sambutan tersedia
if (!isset($sambutan) || empty($sambutan)) {
    // Jika tidak ada sambutan, redirect ke beranda
    header('Location: ' . BASE_URL . '?mod=landing');
    exit;
}

// Ambil data sambutan
$sambutan_id = $sambutan['id'];
$judul = $sambutan['judul'];
$konten = $sambutan['konten'];
$nama_kepala = $sambutan['nama_kepala'];
$jabatan = $sambutan['jabatan'];
$foto = $sambutan['foto'];
$tanggal = date('d F Y', strtotime($sambutan['tanggal_update'] ?? 'now'));

// Meta tags untuk SEO
$page_title = $judul . ' - ' . $config['school_name'];
$page_description = substr(strip_tags($konten), 0, 160);
$page_image = $foto ? BASE_URL . 'uploads/landing/' . $foto : BASE_URL . 'assets/images/default-headmaster.jpg';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sambutan Kepala Sekolah - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
</head>
<body class="bg-light">
    <?php include __DIR__ . '/navbar_landing.php'; ?>
    
    <header class="page-header" style="background: #1a237e; color: white; padding: 4rem 0 2rem; text-align: center;">
        <div class="container">
            <h1 class="display-6 fw-bold mb-2">Sambutan Kepala Sekolah</h1>
            <p class="lead opacity-75">Visi dan harapan untuk kemajuan pendidikan</p>
        </div>
    </header>

<div class="container-fluid py-5 bg-light">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-user-tie me-3"></i>
                    Sambutan Kepala Sekolah
                </h1>
                <p class="lead text-muted">
                    Pesan dan harapan dari pimpinan sekolah untuk seluruh warga sekolah
                </p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sambutan Kepala Sekolah</li>
                </ol>
            </nav>

            <!-- Main Content Card -->
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Headmaster Info -->
                    <div class="row mb-5">
                        <div class="col-md-4 text-center">
                            <div class="headmaster-photo mb-3">
                                <?php if ($foto): ?>
                                    <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($foto) ?>"
                                        alt="Foto <?= htmlspecialchars($nama_kepala) ?>"
                                        class="img-fluid rounded-circle shadow"
                                        style="width: 200px; height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow"
                                        style="width: 200px; height: 200px;">
                                        <i class="fas fa-user-tie fa-4x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3 class="h4 fw-bold text-primary mb-1">
                                <?= htmlspecialchars($nama_kepala) ?>
                            </h3>
                            <p class="text-muted mb-0">
                                <?= htmlspecialchars($jabatan) ?>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Diperbarui: <?= $tanggal ?>
                            </small>
                        </div>

                        <div class="col-md-8">
                            <!-- Title -->
                            <h2 class="h1 fw-bold text-dark mb-4">
                                <?= htmlspecialchars($judul) ?>
                            </h2>

                            <!-- Content -->
                            <div class="sambutan-content">
                                <div class="content-wrapper">
                                    <?= $konten ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signature Section -->
                    <div class="text-end mt-5 pt-4 border-top">
                        <div class="signature">
                            <p class="mb-4 fw-bold">
                                Hormat kami,<br>
                                <span class="text-primary h4 mb-0">
                                    <?= htmlspecialchars($nama_kepala) ?>
                                </span>
                            </p>
                            <p class="text-muted mb-0">
                                <?= htmlspecialchars($jabatan) ?><br>
                                <strong><?= htmlspecialchars($config['school_name'] ?? 'Sekolah') ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="row mt-5">
                <!-- School Vision -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-primary">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-eye fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title text-primary">Visi Sekolah</h5>
                            <p class="card-text">
                                Terwujudnya generasi yang beriman, cerdas, dan berakhlak mulia
                            </p>
                        </div>
                    </div>
                </div>

                <!-- School Mission -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-success">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-bullseye fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title text-success">Misi Sekolah</h5>
                            <p class="card-text">
                                Menyelenggarakan pendidikan berkualitas dengan pendekatan holistik
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center mt-5">
                <div class="bg-primary text-white rounded p-4">
                    <h4 class="mb-3">Bergabunglah dengan Kami!</h4>
                    <p class="mb-4">
                        Jadilah bagian dari komunitas sekolah yang berkualitas dan berprestasi
                    </p>
                    <a href="<?= BASE_URL ?>?mod=landing&act=ppdb_form" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sambutan-content {
        font-size: 1.1rem;
        line-height: 1.8;
    }

    .sambutan-content h2,
    .sambutan-content h3,
    .sambutan-content h4 {
        color: #2c3e50;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .sambutan-content p {
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .sambutan-content ul,
    .sambutan-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }

    .sambutan-content blockquote {
        border-left: 4px solid #007bff;
        padding-left: 1rem;
        margin: 2rem 0;
        font-style: italic;
        color: #6c757d;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
    }

    .headmaster-photo {
        position: relative;
    }

    .headmaster-photo::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 20px;
        background: rgba(0, 123, 255, 0.2);
        border-radius: 50%;
        z-index: -1;
    }

    .signature {
        border-top: 2px solid #007bff;
        padding-top: 2rem;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 2rem !important;
        }

        .row.mb-5>.col-md-4 {
            margin-bottom: 2rem;
        }

        .display-4 {
            font-size: 2.5rem;
        }
    }
</style>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>