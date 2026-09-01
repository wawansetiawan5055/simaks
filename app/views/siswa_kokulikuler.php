<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .koku-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(139, 92, 246, 0.25);
        flex-shrink: 0;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (KOKURIKULER P5 SISWA)              */
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
                <div class="koku-icon-box mr-3">
                    <i class="fas fa-shapes"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kegiatan Kokurikuler &amp; P5
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active font-weight-bold" style="color: #8b5cf6 !important;">Kokurikuler</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php if (empty($kokulikuler)): ?>
            <div class="card p-5 text-center shadow-sm border-0 bg-white" style="border-radius: 16px;">
                <div class="mb-3">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #e0f2fe; display: inline-flex; align-items: center; justify-content: center; color: #0284c7; font-size: 2rem;">
                        <i class="fas fa-shapes"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold text-dark mb-1">Belum Ada Program Kokurikuler Aktif</h5>
                <p class="text-muted small mb-0">Data kegiatan proyek kokurikuler belum ditugaskan untuk rombel kelas Anda semester ini.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($kokulikuler as $k): ?>
                <div class="col-lg-6 col-12 mb-3">
                    <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 14px; border-left: 5px solid #8b5cf6 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.1rem;">
                                    <?= htmlspecialchars($k['nama_kokulikuler']) ?>
                                </h5>
                                <?php if (!empty($k['nilai'])): ?>
                                    <span class="badge badge-primary px-3 py-1 font-weight-bold rounded-pill" style="font-size: 0.82rem;">
                                        Nilai: <?= htmlspecialchars($k['nilai']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-light border text-muted px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        Berjalan
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex flex-column" style="gap: 8px; font-size: 0.85rem;">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-day mr-2 text-info"></i>
                                    <strong>Jadwal:</strong> Hari <?= htmlspecialchars($k['hari'] ?? '-') ?> (<?= htmlspecialchars($k['jam_mulai'] ?? '') ?> - <?= htmlspecialchars($k['jam_selesai'] ?? '') ?>)
                                </div>
                                <div class="text-muted">
                                    <i class="fas fa-user-tie mr-2 text-primary"></i>
                                    <strong>Pembina / Fasilitator:</strong> <?= htmlspecialchars($k['nama_pembina'] ?? '-') ?>
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

<?php include __DIR__ . '/partials/footer.php'; ?>
