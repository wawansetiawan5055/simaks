<?php
// app/views/landing/informasi_detail.php
$config = $data['config'] ?? [];
$info = $data['info'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($info['judul'] ?? 'Detail Informasi') ?> - <?= $config['school_name'] ?? '-' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/landing.css?v=1.0.7">
    <style>
        :root {
            --primary: #1a237e;
            --accent: #ffd600;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .detail-header {
            background: var(--primary);
            color: white;
            padding: 10rem 0 6rem;
            margin-bottom: -4rem;
        }

        .content-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
            border: none;
            position: relative;
            z-index: 10;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="detail-header">
        <div class="container text-center">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-3 text-uppercase fw-bold"><?= htmlspecialchars($info['kategori'] ?? 'Pengumuman') ?></span>
            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($info['judul'] ?? 'Informasi') ?></h1>
            <div class="d-flex justify-content-center gap-3 opacity-75">
                <span><i class="far fa-calendar-alt me-1"></i> <?= tgl_indo($info['tanggal_publikasi'] ?? null) ?></span>
                <span><i class="far fa-user me-1"></i> Admin Sekolah</span>
            </div>
        </div>
    </header>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="content-card mb-5">
                    <div class="mb-5 pb-4 border-bottom">
                       <i class="fas fa-quote-left fa-3x text-primary opacity-25 float-start me-4 mb-3"></i>
                       <p class="lead text-dark fs-4 lh-base" style="font-weight: 500;">
                           <?= nl2br(htmlspecialchars($info['konten'] ?? 'Isi informasi tidak ditemukan.')) ?>
                       </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="<?= BASE_URL ?>landing/informasi_list" class="btn btn-outline-primary px-4 rounded-pill">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Informasi
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <?php include __DIR__ . '/footer_landing.php'; ?>
</body>
</html>
