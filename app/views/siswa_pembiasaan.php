<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .pembiasaan-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
        flex-shrink: 0;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (PEMBIASAAN IBADAH)                 */
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
        .card h2, .card h3 {
            font-size: 1.15rem !important;
        }
        .card-header {
            padding: 10px 12px !important;
        }
        .card-header h6 {
            font-size: 0.82rem !important;
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
            font-size: 0.72rem !important;
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
                <div class="pembiasaan-icon-box mr-3">
                    <i class="fas fa-praying-hands"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Pembiasaan Ibadah &amp; Karakter
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-success font-weight-bold">Pembiasaan Ibadah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php 
        $tot_kegiatan = count($pembiasaan ?? []);
        $tot_hadir = array_sum(array_column($pembiasaan ?? [], 'total_hadir'));
        $tot_sesi = array_sum(array_column($pembiasaan ?? [], 'total_pertemuan'));
        $persen = $tot_sesi > 0 ? round(($tot_hadir / $tot_sesi) * 100) : 0;
        ?>

        <!-- KPI SUMMARY CARDS -->
        <div class="row mb-3">
            <div class="col-md-4 col-12 mb-2">
                <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: #fff;">
                    <small class="text-uppercase font-weight-bold opacity-75" style="letter-spacing: 0.5px;">Tingkat Kehadiran</small>
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <h2 class="font-weight-bold mb-0"><?= $persen ?>%</h2>
                        <i class="fas fa-chart-pie fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-2">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px;">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.72rem;">Total Kehadiran</small>
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <h3 class="font-weight-bold text-success mb-0"><?= $tot_hadir ?> <span class="text-muted" style="font-size: 0.85rem;">/ <?= $tot_sesi ?> Sesi</span></h3>
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6 mb-2">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px;">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.72rem;">Program Diikuti</small>
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <h3 class="font-weight-bold text-dark mb-0"><?= $tot_kegiatan ?> <span class="text-muted" style="font-size: 0.85rem;">Kegiatan</span></h3>
                        <i class="fas fa-mosque fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL RINCIAN PEMBIASAAN -->
        <div class="card shadow-sm border-0" style="border-radius: 14px; overflow: hidden; background: #fff;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-list-alt text-success mr-2"></i> Rekapitulasi Presensi Pembiasaan Ibadah
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #f8fafc;">
                            <tr class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <th class="py-3 pl-4">Nama Kegiatan Pembiasaan</th>
                                <th class="py-3 text-center">Total Pertemuan</th>
                                <th class="py-3 text-center">Total Hadir</th>
                                <th class="py-3 text-center">Persentase</th>
                                <th class="py-3 text-center">Terakhir Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pembiasaan)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-mosque fa-3x mb-3 text-muted opacity-50"></i>
                                        <p class="font-weight-bold mb-0">Belum Ada Data Pembiasaan Ibadah</p>
                                        <small class="text-muted">Data presensi pembiasaan akan tampil setelah dicatat oleh petugas/guru.</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pembiasaan as $p): ?>
                                <?php 
                                    $p_sesi = (int)($p['total_pertemuan'] ?? 0);
                                    $p_hadir = (int)($p['total_hadir'] ?? 0);
                                    $p_pct = $p_sesi > 0 ? round(($p_hadir / $p_sesi) * 100) : 0;
                                ?>
                                <tr>
                                    <td class="pl-4 align-middle">
                                        <strong class="text-dark d-block" style="font-size: 0.95rem;"><?= htmlspecialchars($p['nama_kegiatan']) ?></strong>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold text-muted"><?= $p_sesi ?> Sesi</td>
                                    <td class="text-center align-middle font-weight-bold text-success"><?= $p_hadir ?> Hadir</td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-<?= $p_pct >= 80 ? 'success' : ($p_pct >= 60 ? 'warning' : 'danger') ?> px-2.5 py-1 rounded-pill font-weight-bold">
                                            <?= $p_pct ?>%
                                        </span>
                                    </td>
                                    <td class="text-center align-middle text-muted small">
                                        <?= $p['terakhir_hadir'] ? date('d M Y', strtotime($p['terakhir_hadir'])) : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
