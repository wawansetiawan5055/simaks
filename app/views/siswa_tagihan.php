<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .tagihan-icon-box {
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
    /* 📱 MOBILE RESPONSIVENESS (TAGIHAN SPP SISWA)                 */
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
        .card .font-weight-bold {
            font-size: 1.15rem !important;
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
                <div class="tagihan-icon-box mr-3">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Tagihan &amp; Riwayat Pembayaran SPP
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-warning font-weight-bold">Tagihan SPP</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm py-3 text-center" style="border-radius:12px; border-top:3px solid #3b82f6 !important;">
                    <div class="text-muted" style="font-size:0.7rem;">TOTAL TAGIHAN</div>
                    <div class="font-weight-bold text-dark" style="font-size:1.4rem;">Rp <?= number_format($total_tagihan, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm py-3 text-center" style="border-radius:12px; border-top:3px solid #10b981 !important;">
                    <div class="text-muted" style="font-size:0.7rem;">SUDAH DIBAYAR</div>
                    <div class="font-weight-bold text-success" style="font-size:1.4rem;">Rp <?= number_format($total_dibayar, 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm py-3 text-center" style="border-radius:12px; border-top:3px solid <?= $total_sisa > 0 ? '#ef4444' : '#10b981' ?> !important;">
                    <div class="text-muted" style="font-size:0.7rem;">SISA TAGIHAN</div>
                    <div class="font-weight-bold <?= $total_sisa > 0 ? 'text-danger' : 'text-success' ?>" style="font-size:1.4rem;">Rp <?= number_format($total_sisa, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0" style="border-radius:14px; overflow:hidden;">
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8fafc;">
                        <tr class="text-muted" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                            <th class="py-2 pl-4">Nama Tagihan</th>
                            <th class="py-2 text-right">Jumlah</th>
                            <th class="py-2 text-right">Dibayar</th>
                            <th class="py-2 text-right">Sisa</th>
                            <th class="py-2 text-center">Jatuh Tempo</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tagihan_list)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4"><em>Tidak ada tagihan aktif.</em></td></tr>
                        <?php else: foreach ($tagihan_list as $t): ?>
                        <tr>
                            <td class="pl-4 align-middle font-weight-bold"><?= htmlspecialchars($t['nama_pos'] ?? $t['nama_tarif'] ?? 'SPP') ?></td>
                            <td class="text-right align-middle">Rp <?= number_format($t['jumlah_tagihan'], 0, ',', '.') ?></td>
                            <td class="text-right align-middle text-success font-weight-bold">Rp <?= number_format($t['total_dibayar'], 0, ',', '.') ?></td>
                            <td class="text-right align-middle <?= $t['sisa_tagihan'] > 0 ? 'text-danger' : 'text-muted' ?> font-weight-bold">
                                Rp <?= number_format($t['sisa_tagihan'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center align-middle text-muted" style="font-size:0.8rem;">
                                <?= $t['jatuh_tempo'] ? date('d M Y', strtotime($t['jatuh_tempo'])) : '-' ?>
                            </td>
                            <td class="text-center align-middle">
                                <?php if ($t['status_bayar'] === 'Lunas'): ?>
                                    <span class="badge badge-success px-2 py-1">Lunas</span>
                                <?php elseif ($t['status_bayar'] === 'Sebagian'): ?>
                                    <span class="badge badge-warning text-white px-2 py-1">Sebagian</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-2 py-1">Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
