<?php
// app/views/landing/tentang.php
$config = $data['config'] ?? [];
$stats = $data['stats'] ?? [];
$identitas = $data['identitas'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sekolah - <?= $config['website_name'] ?? '-' ?></title>
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

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .principal-img {
            max-width: 300px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-light">

    <!-- NAVBAR (Minimal version or same as main) -->
    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Tentang Sekolah Kami</h1>
            <p class="lead opacity-75">Mengenal lebih dekat
                <?= htmlspecialchars($config['website_name'] ?? '-') ?>
            </p>
        </div>
    </header>

    <!-- IDENTITAS & STATISTIK SECTION -->
    <section id="profil" class="py-5 bg-white border-bottom">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="bg-primary text-white px-4 py-3">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-id-card me-2"></i> Identitas Sekolah</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody class="small">
                                    <tr>
                                        <th class="bg-light ps-4 py-3" style="width: 35%;">Nama Sekolah</th>
                                        <td class="py-3">
                                            <?= htmlspecialchars($identitas['nama_sekolah'] ?? $config['school_name'] ?? '-') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">NPSN</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['npsn'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">Bentuk Pendidikan</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['bentuk_pendidikan'] ?? '-') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">Status Sekolah</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['status_sekolah'] ?? '-') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">Kurikulum</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['kurikulum'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">Penyelenggara</th>
                                        <td class="py-3"><?= htmlspecialchars($identitas['nama_yayasan'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light ps-4 py-3">Alamat</th>
                                        <td class="py-3 line-height-base">
                                            <?= htmlspecialchars($identitas['alamat'] ?? $config['school_address'] ?? '-') ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="stat-card">
                                <i class="fas fa-award stat-icon"></i>
                                <h2 class="display-6 fw-bold mb-0 text-dark">
                                    <?= htmlspecialchars($config['school_accreditation'] ?? 'A') ?></h2>
                                <p class="text-muted mb-0">Akreditasi Sekolah</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <i class="fas fa-chalkboard-teacher stat-icon text-success"></i>
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['total_guru'] ?? 0) ?></h3>
                                <p class="text-muted small mb-0">Guru & Staff</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <i class="fas fa-user-graduate stat-icon text-info"></i>
                                <h3 class="fw-bold mb-0 text-dark"><?= number_format($stats['total_siswa'] ?? 0) ?></h3>
                                <p class="text-muted small mb-0">Siswa Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SAMBUTAN KEPALA SEKOLAH -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-4 text-center">
                    <?php if (!empty($config['headmaster_photo'])): ?>
                        <img src="<?= BASE_URL . $config['headmaster_photo'] ?>"
                            alt="<?= htmlspecialchars($config['headmaster_name'] ?? '') ?>"
                            class="img-fluid principal-img mb-3">
                    <?php else: ?>
                        <div class="bg-white p-4 rounded-4 shadow-sm mb-3">
                            <i class="fas fa-user-tie fa-8x text-light"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($config['headmaster_name'] ?? 'Kepala Sekolah') ?>
                    </h5>
                    <p class="text-primary small fw-bold text-uppercase">Kepala
                        <?= htmlspecialchars($identitas['nama_sekolah'] ?? 'Sekolah') ?></p>
                </div>
                <div class="col-lg-8">
                    <div class="ps-lg-4">
                        <h6 class="text-primary fw-bold text-uppercase mb-2">Sambutan</h6>
                        <h2 class="display-6 fw-bold mb-4">Membangun Masa Depan Gemilang</h2>
                        <div class="opacity-75 lead" style="line-height: 1.8; font-size: 1rem;">
                            <?php if (!empty($config['headmaster_message'])): ?>
                                <?= nl2br(htmlspecialchars($config['headmaster_message'])) ?>
                            <?php else: ?>
                                <p>Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
                                <p>Selamat datang di portal informasi
                                    <?= htmlspecialchars($identitas['nama_sekolah'] ?? 'sekolah kami') ?>. Kami berkomitmen
                                    untuk menyelenggarakan pendidikan yang holistik, mengintegrasikan kecerdasan intelektual
                                    dengan kekuatan karakter dan nilai-nilai keagamaan.</p>
                                <p>Melalui website ini, kami berharap dapat menjalin silaturahmi yang erat dengan seluruh
                                    wali murid dan masyarakat untuk bersama-sama mencetak generasi unggul yang siap
                                    mendedikasikan diri bagi nusa dan bangsa.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VISI, MISI & TUJUAN -->
    <section id="visi-misi" class="py-5 bg-white">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-12 text-center mb-4">
                    <h2 class="fw-bold">Visi, Misi & Tujuan</h2>
                    <div class="mx-auto bg-primary" style="width: 80px; height: 4px; border-radius: 2px;"></div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light rounded-4 h-100 p-4">
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <h4 class="fw-bold">Visi</h4>
                        </div>
                        <div class="text-muted text-center" style="font-size: 0.95rem;">
                            <?= nl2br(htmlspecialchars($config['school_vision'] ?? 'Menjadi sekolah unggul dalam prestasi dan luhur dalam budi pekerti.')) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light rounded-4 h-100 p-4">
                        <div class="text-center mb-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-bullseye fa-2x"></i>
                            </div>
                            <h4 class="fw-bold">Misi</h4>
                        </div>
                        <div class="text-muted small">
                            <?= nl2br(htmlspecialchars($config['school_mission'] ?? '- Melaksanakan pembelajaran efektif.')) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-light rounded-4 h-100 p-4">
                        <div class="text-center mb-4">
                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-flag fa-2x"></i>
                            </div>
                            <h4 class="fw-bold">Tujuan</h4>
                        </div>
                        <div class="text-muted small">
                            <?= nl2br(htmlspecialchars($config['school_goals'] ?? '- Menghasilkan lulusan yang kompeten.')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEJARAH SECTION -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="card border-0 shadow-sm rounded-4 p-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-12">
                        <h6 class="text-primary fw-bold text-uppercase mb-2">Profil Lengkap</h6>
                        <h3 class="fw-bold mb-4">Sejarah & Latar Belakang</h3>
                        <div class="opacity-75 lead" style="line-height: 2; font-size: 1.1rem;">
                            <?= nl2br(htmlspecialchars($config['school_description'] ?? $config['website_history'] ?? 'Sekolah ini didirikan dengan dedikasi tinggi untuk memberikan pendidikan berkualitas bagi masyarakat. Perjalanan panjang kami telah membentuk karakter lembaga yang kuat dan dipercaya oleh publik.')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include __DIR__ . '/footer_landing.php'; ?>
</body>

</html>