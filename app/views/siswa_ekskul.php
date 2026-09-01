<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .ekskul-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);
        flex-shrink: 0;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (EKSTRAKURIKULER SISWA)             */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .card {
            border-radius: 10px !important;
            margin-bottom: 6px !important;
        }
        .card-body {
            padding: 10px 10px !important;
        }
        .card-body h4 {
            font-size: 0.92rem !important;
        }
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border: none;
        }
        .table th {
            padding: 6px 8px !important;
            font-size: 0.65rem !important;
            white-space: nowrap;
        }
        .table td {
            padding: 6px 8px !important;
            font-size: 0.70rem !important;
            white-space: nowrap;
        }
        .badge {
            font-size: 0.60rem !important;
            padding: 2px 6px !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="ekskul-icon-box mr-3">
                    <i class="fas fa-futbol"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kegiatan Ekstrakurikuler
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-warning font-weight-bold">Ekstrakurikuler</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php if (empty($ekskul)): ?>
            <div class="card p-5 text-center shadow-sm border-0 bg-white" style="border-radius: 16px;">
                <div class="mb-3">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #fef3c7; display: inline-flex; align-items: center; justify-content: center; color: #d97706; font-size: 2rem;">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold text-dark mb-1">Belum Terdaftar di Ekstrakurikuler</h5>
                <p class="text-muted small mb-0">Anda belum dimasukkan ke dalam daftar anggota ekstrakurikuler semester ini. Hubungi pembina ekskul atau waka kesiswaan.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($ekskul as $e): ?>
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 14px; border-left: 5px solid #f59e0b !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.1rem;">
                                    <?= htmlspecialchars($e['nama_ekskul']) ?>
                                </h5>
                                <?php if (!empty($e['nilai'])): ?>
                                    <span class="badge badge-success px-3 py-1 font-weight-bold rounded-pill" style="font-size: 0.82rem;">
                                        Nilai: <?= htmlspecialchars($e['nilai']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-light border text-muted px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        Aktif
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-column" style="gap: 8px; font-size: 0.85rem;">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-day mr-2 text-warning"></i>
                                    <strong>Jadwal:</strong> Hari <?= htmlspecialchars($e['hari'] ?? '-') ?> (<?= htmlspecialchars($e['jam_mulai'] ?? '') ?> - <?= htmlspecialchars($e['jam_selesai'] ?? '') ?>)
                                </div>
                                <div class="text-muted">
                                    <i class="fas fa-user-tie mr-2 text-primary"></i>
                                    <strong>Pembina:</strong> <?= htmlspecialchars($e['nama_pembina'] ?? '-') ?>
                                </div>
                                <?php if (!empty($e['deskripsi'])): ?>
                                    <div class="text-muted small mt-1 font-italic bg-light p-2 rounded">
                                        <?= htmlspecialchars($e['deskripsi']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
