<?php
/**
 * SIMAKS - Landing Page - Fasilitas Sekolah View
 * Menampilkan berbagai fasilitas yang tersedia di sekolah
 */

// Default values
$facilities = $facilities ?? [];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas Sekolah - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
</head>
<body class="bg-light">
    <?php include __DIR__ . '/navbar_landing.php'; ?>
    
    <header class="page-header" style="background: #1a237e; color: white; padding: 4rem 0 2rem; text-align: center;">
        <div class="container">
            <h1 class="display-6 fw-bold mb-2">Fasilitas Sekolah</h1>
            <p class="lead opacity-75">Sarana dan prasarana penunjang kualitas belajar</p>
        </div>
    </header>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-building me-3"></i>
                    Fasilitas Sekolah
                </h1>
                <p class="lead text-muted">
                    Fasilitas modern dan lengkap untuk mendukung kegiatan belajar mengajar yang optimal
                </p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fasilitas Sekolah</li>
                </ol>
            </nav>

            <?php if (!empty($facilities)): ?>
                <!-- Facilities Grid -->
                <div class="row g-4">
                    <?php foreach ($facilities as $facility): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="card h-100 shadow-sm facility-card">
                            <!-- Facility Image -->
                            <?php if ($facility['gambar']): ?>
                            <div class="card-img-top-container position-relative">
                                <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($facility['gambar']) ?>"
                                     class="card-img-top"
                                     alt="<?= htmlspecialchars($facility['nama_fasilitas']) ?>"
                                     style="height: 200px; object-fit: cover;">
                                <?php if ($facility['status']): ?>
                                <div class="status-badge">
                                    <span class="badge bg-<?= $facility['status'] === 'Tersedia' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($facility['status']) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <div class="card-img-top-container bg-secondary text-white d-flex align-items-center justify-content-center position-relative"
                                 style="height: 200px;">
                                <i class="fas fa-building fa-4x"></i>
                                <?php if ($facility['status']): ?>
                                <div class="status-badge">
                                    <span class="badge bg-<?= $facility['status'] === 'Tersedia' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($facility['status']) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <!-- Facility Title -->
                                <h5 class="card-title fw-bold text-primary mb-3">
                                    <i class="fas fa-<?= getFacilityIcon($facility['kategori']) ?> me-2"></i>
                                    <?= htmlspecialchars($facility['nama_fasilitas']) ?>
                                </h5>

                                <!-- Facility Category -->
                                <?php if ($facility['kategori']): ?>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-tag me-1"></i>
                                    <?= htmlspecialchars($facility['kategori']) ?>
                                </p>
                                <?php endif; ?>

                                <!-- Facility Description -->
                                <p class="card-text text-muted flex-grow-1">
                                    <?= htmlspecialchars($facility['deskripsi']) ?>
                                </p>

                                <!-- Facility Features -->
                                <?php if (!empty($facility['fasilitas'])): ?>
                                <div class="facility-features mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-list text-primary me-1"></i>
                                        Spesifikasi:
                                    </h6>
                                    <div class="row g-1">
                                        <?php
                                        $fasilitas_list = explode("\n", $facility['fasilitas']);
                                        foreach ($fasilitas_list as $item):
                                            if (trim($item)):
                                        ?>
                                        <div class="col-6">
                                            <small class="text-muted">
                                                <i class="fas fa-check text-success me-1"></i>
                                                <?= htmlspecialchars(trim($item)) ?>
                                            </small>
                                        </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Capacity & Location -->
                                <div class="facility-info mt-auto">
                                    <div class="row g-2">
                                        <?php if ($facility['kapasitas']): ?>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded text-center">
                                                <small class="text-muted d-block">Kapasitas</small>
                                                <strong class="text-primary">
                                                    <?= htmlspecialchars($facility['kapasitas']) ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($facility['lokasi']): ?>
                                        <div class="col-6">
                                            <div class="p-2 bg-light rounded text-center">
                                                <small class="text-muted d-block">Lokasi</small>
                                                <strong class="text-info">
                                                    <?= htmlspecialchars($facility['lokasi']) ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 pt-0">
                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary flex-fill"
                                            onclick="showFacilityDetail(<?= $facility['id'] ?>)">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Detail
                                    </button>

                                    <?php if ($facility['link_booking']): ?>
                                    <a href="<?= htmlspecialchars($facility['link_booking']) ?>"
                                       class="btn btn-primary flex-fill"
                                       target="_blank">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        Booking
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Facilities by Category -->
                <?php
                $categories = array_unique(array_column($facilities, 'kategori'));
                if (!empty($categories)):
                ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 class="h4 mb-4 text-center">Kategori Fasilitas</h3>
                        <div class="row g-3">
                            <?php foreach ($categories as $category): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="category-card text-center p-3 bg-light rounded">
                                    <i class="fas fa-<?= getFacilityIcon($category) ?> fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-1"><?= htmlspecialchars($category) ?></h6>
                                    <small class="text-muted">
                                        <?= count(array_filter($facilities, fn($f) => $f['kategori'] === $category)) ?> fasilitas
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Facilities Found -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-building fa-5x text-muted"></i>
                    </div>
                    <h3 class="h4 text-muted mb-3">Belum ada fasilitas terdaftar</h3>
                    <p class="text-muted mb-4">
                        Informasi fasilitas sekolah akan segera diperbarui.
                    </p>
                    <a href="<?= BASE_URL ?>?mod=landing" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>

            <!-- Virtual Tour Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body text-center py-5">
                            <h4 class="mb-3">
                                <i class="fas fa-street-view me-2"></i>
                                Virtual Tour Sekolah
                            </h4>
                            <p class="mb-4 opacity-75">
                                Jelajahi fasilitas sekolah kami secara virtual
                            </p>
                            <button class="btn btn-light btn-lg px-4" disabled>
                                <i class="fas fa-play-circle me-2"></i>
                                Mulai Virtual Tour
                                <small class="d-block text-muted mt-1">(Segera Hadir)</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Facility Detail Modal -->
<div class="modal fade" id="facilityDetailModal" tabindex="-1" aria-labelledby="facilityDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="facilityDetailModalLabel">Detail Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="facilityDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to get facility icons
function getFacilityIcon($category) {
    $icons = [
        'Ruang Kelas' => 'chalkboard',
        'Laboratorium' => 'flask',
        'Perpustakaan' => 'book',
        'Lapangan Olahraga' => 'futbol',
        'Aula' => 'theater-masks',
        'Kantin' => 'utensils',
        'Musholla' => 'mosque',
        'Parkir' => 'car',
        'UKS' => 'medkit',
        'Gudang' => 'warehouse',
        'Toilet' => 'restroom',
        'Ruang Guru' => 'user-graduate',
        'Ruang Kepala Sekolah' => 'user-tie',
        'Administrasi' => 'clipboard',
    ];

    return $icons[$category] ?? 'building';
}
?>

<script>
function showFacilityDetail(facilityId) {
    // Load facility detail via AJAX (placeholder for now)
    const modal = new bootstrap.Modal(document.getElementById('facilityDetailModal'));
    const content = document.getElementById('facilityDetailContent');

    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail fasilitas...</p>
        </div>
    `;

    modal.show();

    // In a real implementation, you would make an AJAX call here
    // For now, just show a placeholder message
    setTimeout(() => {
        content.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Fitur detail fasilitas lengkap akan segera hadir.
            </div>
            <p>Untuk saat ini, silakan hubungi administrasi sekolah untuk informasi lebih detail tentang fasilitas ini.</p>
        `;
    }, 1000);
}
</script>

<style>
.facility-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.facility-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}

.card-img-top-container {
    position: relative;
    overflow: hidden;
}

.card-img-top-container::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,123,255,0.1), rgba(0,123,255,0.05));
    pointer-events: none;
}

.facility-features .col-6 {
    margin-bottom: 0.25rem;
}

.category-card {
    transition: transform 0.2s ease;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.opacity-75 {
    opacity: 0.75;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }

    .facility-card {
        margin-bottom: 2rem;
    }

    .category-card {
        margin-bottom: 1rem;
    }
}
</style>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>