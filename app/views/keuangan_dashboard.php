<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<style>
    /* Premium Finance Dashboard Adjustments */
    .finance-stats .info-box {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        border-radius: 15px !important;
        transition: all 0.3s ease;
    }

    .finance-stats .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .stat-gradient-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
    }

    .stat-gradient-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
    }

    .stat-gradient-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
    }

    .stat-gradient-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
    }

    .finance-stats .info-box-icon {
        border-radius: 12px !important;
        width: 60px !important;
        height: 60px !important;
        margin: 10px !important;
    }

    .card-rekening .item {
        border-bottom: 1px solid #f1f5f9;
        padding: 12px !important;
        transition: background 0.2s;
    }

    .card-rekening .item:hover {
        background: #f8fafc;
    }

    .card-rekening .item:last-child {
        border-bottom: none;
    }

    .rekening-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .bg-soft-blue {
        background: #e0f2fe;
        color: #0369a1;
    }

    .bg-soft-green {
        background: #dcfce7;
        color: #15803d;
    }

    .sync-btn {
        transition: all 0.5s;
    }

    .sync-btn:active i {
        transform: rotate(360deg);
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-chart-line mr-2"></i> Dashboard Keuangan</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="index.php?mod=keuangan_dashboard&act=sync_saldo"
                    class="btn btn-outline-primary btn-sm rounded-pill px-3 sync-btn">
                    <i class="fas fa-sync-alt mr-1"></i> Sinkronkan Saldo
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Dashboard Stats -->
        <div class="row finance-stats">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon stat-gradient-blue elevation-1"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted small uppercase font-weight-bold">Total Saldo</span>
                        <span class="info-box-number h4 font-weight-bold mb-0 text-dark">
                            Rp <?= number_format($data['total_saldo'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon stat-gradient-green elevation-1"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted small uppercase font-weight-bold">Pemasukan (Bulan
                            Ini)</span>
                        <span class="info-box-number h4 font-weight-bold mb-0 text-dark">
                            Rp <?= number_format($data['pendapatan_bulan_ini'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon stat-gradient-red elevation-1"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted small uppercase font-weight-bold">Pengeluaran (Bulan
                            Ini)</span>
                        <span class="info-box-number h4 font-weight-bold mb-0 text-dark">
                            Rp <?= number_format($data['pengeluaran_bulan_ini'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon stat-gradient-orange elevation-1"><i
                            class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted small uppercase font-weight-bold">Tunggakan Siswa</span>
                        <span class="info-box-number h4 font-weight-bold mb-0 text-dark">
                            Rp <?= number_format($data['total_tunggakan'] ?? 0, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Balances List -->
            <div class="col-md-4">
                <div class="card card-rekening h-100 shadow-none border">
                    <div class="card-header bg-white border-0 py-3">
                        <h3 class="card-title font-weight-bold text-dark"><i
                                class="fas fa-university mr-2 text-info"></i> Saldo Rekening</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card">
                            <?php if (!empty($data['rekening_list'])): ?>
                                <?php foreach ($data['rekening_list'] as $rek): ?>
                                    <li class="item d-flex align-items-center">
                                        <div
                                            class="rekening-icon <?= $rek['tipe'] == 'KAS' ? 'bg-soft-blue' : 'bg-soft-green' ?>">
                                            <i
                                                class="fas <?= $rek['tipe'] == 'KAS' ? 'fa-cash-register' : 'fa-building-columns' ?>"></i>
                                        </div>
                                        <div class="product-info ml-0 flex-grow-1">
                                            <a href="javascript:void(0)" class="product-title font-weight-bold text-dark"
                                                style="font-size: 0.95rem;">
                                                <?= htmlspecialchars($rek['nama_rekening']) ?>
                                                <span
                                                    class="badge badge-<?= $rek['tipe'] == 'KAS' ? 'primary' : 'success' ?> float-right font-weight-normal rounded-pill px-2"
                                                    style="font-size: 0.65rem;">
                                                    <?= $rek['tipe'] ?>
                                                </span>
                                            </a>
                                            <span class="product-description text-primary font-weight-bold"
                                                style="font-size: 1.1rem;">
                                                Rp <?= number_format($rek['saldo_akhir'], 0, ',', '.') ?>
                                            </span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="item p-4 text-center text-muted"> <i
                                        class="fas fa-info-circle mb-2 d-block fa-2x"></i> Belum ada data rekening</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 text-center pb-4">
                        <a href="index.php?mod=keuangan_master&act=rekening"
                            class="btn btn-light btn-block btn-sm rounded-pill text-muted font-weight-bold">
                            <i class="fas fa-cog mr-1"></i> Kelola Rekening
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-8">
                <div class="card h-100 shadow-none border">
                    <div class="card-header bg-white border-0 py-3">
                        <h3 class="card-title font-weight-bold text-dark"><i
                                class="fas fa-history mr-2 text-warning"></i> Transaksi Terbaru</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="border-0 px-4">No Bukti</th>
                                        <th class="border-0">Tanggal</th>
                                        <th class="border-0">Tipe</th>
                                        <th class="border-0">Keterangan</th>
                                        <th class="border-0 text-right px-4">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($data['transaksi_terbaru'])): ?>
                                        <?php foreach ($data['transaksi_terbaru'] as $trx): ?>
                                            <tr>
                                                <td class="px-4 font-weight-bold text-primary small"><?= $trx['no_bukti'] ?>
                                                </td>
                                                <td class="text-muted small"><?= date('d M Y', strtotime($trx['tanggal'])) ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-<?= $trx['tipe'] == 'MASUK' ? 'success' : 'danger' ?> rounded-pill px-3 py-1"
                                                        style="font-size: 0.7rem;">
                                                        <i
                                                            class="fas fa-arrow-<?= $trx['tipe'] == 'MASUK' ? 'down' : 'up' ?> mr-1"></i>
                                                        <?= $trx['tipe'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="font-weight-bold text-dark small">
                                                        <?= htmlspecialchars($trx['nama_jenis']) ?></div>
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <?= htmlspecialchars($trx['keterangan']) ?>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-right px-4 font-weight-bold <?= $trx['tipe'] == 'MASUK' ? 'text-success' : 'text-danger' ?>">
                                                    Rp <?= number_format($trx['jumlah'], 0, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-exchange-alt fa-3x mb-3 d-block opacity-25"></i>
                                                Belum ada transaksi
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end">
                        <a href="index.php?mod=keuangan_transaksi_masuk"
                            class="btn btn-success btn-sm rounded-pill px-4 shadow-sm shadow-success mr-2">
                            <i class="fas fa-plus-circle mr-1"></i> Input Pemasukan
                        </a>
                        <a href="index.php?mod=keuangan_transaksi_keluar"
                            class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm shadow-danger">
                            <i class="fas fa-minus-circle mr-1"></i> Input Pengeluaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../app/views/partials/footer.php'; ?>