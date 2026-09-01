<?php
// app/views/landing/guru_list.php
$config = $data['config'] ?? [];
$guru_list = $data['guru'] ?? [];
$search = $data['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil GTK - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
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

    <!-- NAVBAR (Minimal version or same as main) -->
    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Profil Guru & Tenaga Kependidikan</h1>
            <p class="lead opacity-75">Tim profesional yang mendidik dengan sepenuh hati</p>
        </div>
    </header>

    <div class="container mt-n5">
        <div class="filter-card p-4 mb-5">
            <form action="" method="GET" class="row justify-content-center">
                <input type="hidden" name="mod" value="landing_sma">
                <input type="hidden" name="act" value="guru_list">
                <div class="col-md-8">
                    <label class="form-label small fw-bold text-muted d-block text-center mb-2">Cari Guru atau Mata
                        Pelajaran</label>
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <input type="text" name="search" class="form-control border-0 py-2 ps-4"
                            placeholder="Ketik nama atau mata pelajaran..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-warning px-4" type="submit">
                            <i class="fas fa-search me-2"></i>Cari
                        </button>
                    </div>
                    <?php if ($search): ?>
                        <div class="text-center mt-2 small">
                            <a href="<?= BASE_URL ?>landing/guru_list" class="text-danger text-decoration-none">
                                <i class="fas fa-times me-1"></i> Reset Pencarian
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <?php if (empty($guru_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Data guru tidak ditemukan</h4>
                    <p>Silakan coba kata kunci pencarian lain atau kembali nanti.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php foreach ($guru_list as $guru): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm text-center p-3"
                                style="border-radius: 15px; background: #fff;">
                                <!-- Foto di Atas dengan Rasio 3:4 -->
                                <div class="mb-3 w-100" style="overflow: hidden;">
                                    <?php if (!empty($guru['foto'])): ?>
                                        <img src="<?= BASE_URL . 'assets/img/profil/' . $guru['foto'] ?>"
                                            alt="<?= htmlspecialchars($guru['nama']) ?>" class="w-100"
                                            style="aspect-ratio: 3/4; object-fit: cover; border-radius: 8px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.08);"
                                            onerror="this.src='https://placehold.co/300x400?text=3x4'">
                                    <?php else: ?>
                                        <div class="w-100 d-flex align-items-center justify-content-center bg-light text-muted"
                                            style="aspect-ratio: 3/4; border-radius: 8px;">
                                            <i class="fas fa-user-tie fa-4x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body p-0 d-flex flex-column">
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">
                                        <?= htmlspecialchars($guru['nama']) ?>
                                    </h6>
                                    <div class="mb-3">
                                        <span class="badge bg-primary-subtle text-primary px-3 rounded-pill fw-semibold"
                                            style="font-size: 0.65rem;">
                                            <?= htmlspecialchars($guru['jabatan'] ?? 'Guru / Staff') ?>
                                        </span>
                                    </div>

                                    <div class="border-top pt-3 mt-auto text-start">
                                        <div class="mb-2">
                                            <div class="text-muted small mb-0"
                                                style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                NUPTK
                                            </div>
                                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                <?= htmlspecialchars(!empty($guru['nuptk']) ? $guru['nuptk'] : '-') ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-muted small mb-0"
                                                style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                Mata
                                                Pelajaran</div>
                                            <div class="fw-bold text-dark" style="font-size: 0.8rem; line-height: 1.2;">
                                                <?= htmlspecialchars(!empty($guru['bidang_studi']) ? $guru['bidang_studi'] : '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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