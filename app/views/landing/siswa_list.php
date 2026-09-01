<?php
// app/views/landing/siswa_list.php
$config = $data['config'] ?? [];
$siswa_list = $data['siswa'] ?? [];
$kelas_list = $data['kelas_list'] ?? [];
$kelas_filter = $data['kelas_filter'] ?? '';
$search = $data['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa - <?= $config['website_name'] ?? 'SMA Plus Al-Manshuriyah' ?></title>
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

    <?php include __DIR__ . '/navbar_landing.php'; ?>

    <header class="page-header">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Daftar Siswa</h1>
            <p class="lead opacity-75">Siswa-siswa berprestasi kebanggaan sekolah</p>
        </div>
    </header>

    <div class="container mt-n5">
        <div class="filter-card p-4 mb-5">
            <form action="" method="GET" class="row g-3">
                <input type="hidden" name="mod" value="landing_sma">
                <input type="hidden" name="act" value="siswa_list">

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Pilih Tahun Ajaran</label>
                    <select name="ta" class="form-select border-0 bg-light fw-bold" style="border-radius: 10px;"
                        onchange="this.form.submit()">
                        <?php foreach ($ta_list as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>" <?= ($ta_filter == $t) ? 'selected' : '' ?>>
                                Tahun Pelajaran <?= htmlspecialchars($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Pilih Kelas</label>
                    <select name="kelas" class="form-select border-0 bg-light fw-bold" style="border-radius: 10px;"
                        onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= htmlspecialchars($k['nama_kelas']) ?>" <?= ($kelas_filter == $k['nama_kelas']) ? 'selected' : '' ?>>
                                Kelas <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <section class="pb-5">
        <div class="container">
            <?php if (empty($siswa_list)): ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Data siswa tidak ditemukan</h4>
                    <p>Tidak ada data siswa untuk Tahun Pelajaran <?= htmlspecialchars($ta_filter) ?>.</p>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="py-3 ps-4">No</th>
                                    <th class="py-3">Nama Lengkap</th>
                                    <th class="py-3">Kelas</th>
                                    <th class="py-3">Tahun Pelajaran</th>
                                    <th class="py-3">NISN</th>
                                    <th class="py-3">JK</th>
                                    <th class="py-3 pe-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($siswa_list as $idx => $s): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 36px; height: 36px;">
                                                    <i class="fas fa-user-graduate"></i>
                                                </div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($s['nama']) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                                <?= htmlspecialchars($s['kelas'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?= htmlspecialchars($s['nama_ta'] ?? '-') ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($s['nisn'] ?? '-') ?></td>
                                        <td><?= ($s['jk'] == 'Laki-laki') ? 'L' : 'P' ?></td>
                                        <td class="pe-4 text-center">
                                            <span class="badge bg-success px-3 py-2 rounded-pill small">
                                                <i class="fas fa-check-circle me-1"></i> <?= $s['status_aktif'] ?? 'Aktif' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <small class="text-muted">Menampilkan <?= count($siswa_list) ?> data siswa</small>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include __DIR__ . '/footer_premium.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>