<?php
/**
 * SIMAKS - Landing Page - Program Sekolah View
 * Menampilkan berbagai program pendidikan yang ditawarkan sekolah
 */

// Default values
$programs = $programs ?? [];

include '../app/views/templates/landing_header.php';
?>

<div class="container-fluid py-5 bg-light">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-graduation-cap me-3"></i>
                    Program Sekolah
                </h1>
                <p class="lead text-muted">
                    Berbagai program pendidikan unggulan yang kami tawarkan untuk mengembangkan potensi siswa
                </p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?mod=landing">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Program Sekolah</li>
                </ol>
            </nav>

            <?php if (!empty($programs)): ?>
                <!-- Programs Grid -->
                <div class="row g-4">
                    <?php foreach ($programs as $program): ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card h-100 shadow-sm program-card">
                                <!-- Program Image -->
                                <?php if ($program['image']): ?>
                                    <div class="card-img-top-container">
                                        <img src="<?= BASE_URL ?>uploads/program/<?= htmlspecialchars($program['image']) ?>"
                                            class="card-img-top" alt="<?= htmlspecialchars($program['title']) ?>"
                                            style="height: 200px; object-fit: cover;">
                                    </div>
                                <?php else: ?>
                                    <div class="card-img-top-container bg-primary text-white d-flex align-items-center justify-content-center"
                                        style="height: 200px;">
                                        <i class="<?= htmlspecialchars($program['icon'] ?: 'fas fa-graduation-cap') ?> fa-4x"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body d-flex flex-column">
                                    <!-- Program Title -->
                                    <h5 class="card-title fw-bold text-primary mb-3">
                                        <?php if ($program['icon']): ?>
                                            <i class="<?= htmlspecialchars($program['icon']) ?> me-2"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($program['title']) ?>
                                    </h5>

                                    <!-- Program Description -->
                                    <p class="card-text text-muted flex-grow-1">
                                        <?= htmlspecialchars($program['description']) ?>
                                    </p>

                                </div>

                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">

                                        <button class="btn btn-outline-primary flex-fill"
                                            onclick="showProgramDetail(<?= $program['id'] ?>)">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Program Statistics -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center py-4">
                                <h4 class="mb-3">Mengapa Memilih Program Kami?</h4>
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <h5 class="mb-0">1000+</h5>
                                            <small>Siswa Aktif</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <i class="fas fa-trophy fa-2x mb-2"></i>
                                            <h5 class="mb-0">95%</h5>
                                            <small>Kelulusan</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                                            <h5 class="mb-0">50+</h5>
                                            <small>Guru Profesional</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <i class="fas fa-certificate fa-2x mb-2"></i>
                                            <h5 class="mb-0">10+</h5>
                                            <small>Tahun Pengalaman</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- No Programs Found -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-graduation-cap fa-5x text-muted"></i>
                    </div>
                    <h3 class="h4 text-muted mb-3">Belum ada program tersedia</h3>
                    <p class="text-muted mb-4">
                        Program pendidikan akan segera diumumkan.
                    </p>
                    <a href="<?= BASE_URL ?>?mod=landing" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>

            <!-- Call to Action -->
            <div class="text-center mt-5">
                <div class="bg-success text-white rounded p-4">
                    <h4 class="mb-3">Siap Bergabung?</h4>
                    <p class="mb-4">
                        Daftar sekarang dan jadilah bagian dari komunitas pendidikan berkualitas
                    </p>
                    <a href="<?= BASE_URL ?>?mod=landing&act=ppdb_form" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>
                        Daftar PPDB
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Program Detail Modal -->
<div class="modal fade" id="programDetailModal" tabindex="-1" aria-labelledby="programDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="programDetailModalLabel">Detail Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="programDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showProgramDetail(programId) {
        // Load program detail via AJAX (placeholder for now)
        const modal = new bootstrap.Modal(document.getElementById('programDetailModal'));
        const content = document.getElementById('programDetailContent');

        content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail program...</p>
        </div>
    `;

        modal.show();

        // In a real implementation, you would make an AJAX call here
        // For now, just show a placeholder message
        setTimeout(() => {
            content.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Fitur detail program lengkap akan segera hadir.
            </div>
            <p>Untuk saat ini, silakan hubungi administrasi sekolah untuk informasi lebih detail tentang program ini.</p>
        `;
        }, 1000);
    }
</script>

<style>
    .program-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
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
        background: linear-gradient(45deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.05));
        pointer-events: none;
    }

    .program-features ul li {
        line-height: 1.4;
    }

    .stat-item {
        padding: 1rem;
    }

    .stat-item i {
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .display-4 {
            font-size: 2.5rem;
        }

        .program-card {
            margin-bottom: 2rem;
        }

        .stat-item {
            margin-bottom: 1rem;
        }
    }
</style>

<?php include __DIR__ . '/footer_premium.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>