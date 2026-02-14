<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h1 class="m-0"><i class="fas fa-book-open mr-2"></i> Buku Kas Umum (BKU)</h1>
                <p class="text-muted small mb-0">Laporan arus kas lengkap dengan saldo berjalan.</p>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-default shadow-sm border" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm border-0" style="border-radius: 15px;">
                    <span class="info-box-icon bg-info elevation-0" style="border-radius: 12px;"><i
                            class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Saldo Awal</span>
                        <span class="info-box-number text-dark mt-1" style="font-size: 1.2rem;">
                            Rp <?= number_format($saldoAwal, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm border-0" style="border-radius: 15px;">
                    <span class="info-box-icon bg-success elevation-0" style="border-radius: 12px;"><i
                            class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Total Pemasukan</span>
                        <span class="info-box-number text-success mt-1" style="font-size: 1.2rem;">
                            Rp <?= number_format($totalMasuk, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm border-0" style="border-radius: 15px;">
                    <span class="info-box-icon bg-danger elevation-0" style="border-radius: 12px;"><i
                            class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Total Pengeluaran</span>
                        <span class="info-box-number text-danger mt-1" style="font-size: 1.2rem;">
                            Rp <?= number_format($totalKeluar, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box shadow-sm border-0"
                    style="border-radius: 15px; background: linear-gradient(135deg, #007bff, #0056b3);">
                    <span class="info-box-icon text-white elevation-0"
                        style="background: rgba(255,255,255,0.2); border-radius: 12px;"><i
                            class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-white">Saldo Akhir</span>
                        <span class="info-box-number text-white mt-1" style="font-size: 1.2rem;">
                            Rp <?= number_format($saldoAkhir, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Data -->
        <div class="card shadow-sm border-0 mb-4"
            style="border-radius: 15px; overflow: hidden; border-top: 4px solid #007bff;">
            <div class="card-header bg-white py-3 border-bottom">
                <form action="" method="get">
                    <input type="hidden" name="mod" value="keuangan_bku">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="font-weight-bold text-muted small text-uppercase mb-1">Dari Tanggal</label>
                            <input type="date" name="tanggal_dari" class="form-control" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="font-weight-bold text-muted small text-uppercase mb-1">Sampai Tanggal</label>
                            <input type="date" name="tanggal_sampai" class="form-control" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="font-weight-bold text-muted small text-uppercase mb-1">Filter Rekening</label>
                            <select name="id_rekening" class="form-control select2">
                                <option value="">Semua Rekening</option>
                                <?php foreach ($rekeningList as $r): ?>
                                    <option value="<?= $r['id_rekening'] ?>" <?= ($id_rekening == $r['id_rekening']) ? 'selected' : '' ?>>
                                        <?= $r['nama_rekening'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm" style="font-weight: 600;">
                                <i class="fas fa-filter mr-1"></i> Tampilkan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light text-uppercase text-muted small font-weight-bold">
                            <tr>
                                <th class="text-center py-3 border-top-0" style="width: 50px;">No</th>
                                <th class="text-center py-3 border-top-0" style="width: 120px;">Tanggal</th>
                                <th class="py-3 border-top-0" style="width: 150px;">No. Bukti</th>
                                <th class="py-3 border-top-0">Uraian Transaksi</th>
                                <th class="text-right py-3 border-top-0" style="width: 130px;">Debet (Masuk)</th>
                                <th class="text-right py-3 border-top-0" style="width: 130px;">Kredit (Keluar)</th>
                                <th class="text-right py-3 border-top-0 bg-light" style="width: 130px;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Saldo Awal Row -->
                            <tr style="background-color: #f8f9fa;">
                                <td></td>
                                <td class="text-center font-weight-bold"><?= date('d/m/Y', strtotime($startDate)) ?>
                                </td>
                                <td class="text-center">-</td>
                                <td class="font-weight-bold font-italic text-muted">Saldo Awal Periode</td>
                                <td class="text-right">-</td>
                                <td class="text-right">-</td>
                                <td class="text-right font-weight-bold text-dark" style="background-color: #e9ecef;">
                                    Rp <?= number_format($saldoAwal, 0, ',', '.') ?>
                                </td>
                            </tr>

                            <?php
                            $no = 1;
                            $runningBalance = $saldoAwal;

                            if (!empty($transaksi)):
                                foreach ($transaksi as $row):
                                    $bgClass = ($row['tipe'] == 'MASUK') ? 'text-success' : 'text-danger';
                                    $in = ($row['tipe'] == 'MASUK') ? $row['jumlah'] : 0;
                                    $out = ($row['tipe'] == 'KELUAR') ? $row['jumlah'] : 0;
                                    $runningBalance = $runningBalance + $in - $out;
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle text-muted small"><?= $no++ ?></td>
                                        <td class="text-center align-middle"><?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                        </td>
                                        <td class="align-middle code-font small font-weight-bold text-muted">
                                            <?= $row['no_bukti'] ?></td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-dark mb-0">
                                                <?= $row['nama_kategori'] ?? 'Lain-lain' ?> - <?= $row['nama_jenis'] ?>
                                            </div>
                                            <?php if ($row['referensi']): ?>
                                                <div class="small text-muted"><i class="fas fa-user-tag mr-1"></i>
                                                    <?= $row['referensi'] ?></div>
                                            <?php endif; ?>
                                            <?php if ($row['nama_siswa']): ?>
                                                <div class="small text-info"><i class="fas fa-user-graduate mr-1"></i>
                                                    <?= $row['nama_siswa'] ?></div>
                                            <?php endif; ?>
                                            <div class="small text-secondary font-italic"><?= $row['keterangan'] ?></div>
                                        </td>
                                        <td class="text-right align-middle text-success font-weight-bold">
                                            <?= ($in > 0) ? number_format($in, 0, ',', '.') : '-' ?>
                                        </td>
                                        <td class="text-right align-middle text-danger font-weight-bold">
                                            <?= ($out > 0) ? number_format($out, 0, ',', '.') : '-' ?>
                                        </td>
                                        <td class="text-right align-middle font-weight-bold text-dark"
                                            style="background-color: #f8f9fa;">
                                            <?= number_format($runningBalance, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <img src="assets/img/empty-state.svg" alt="No Data"
                                            style="height: 100px; opacity: 0.5;" class="mb-3 d-block mx-auto">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-white border-top">
                            <tr style="font-size: 1.1rem; border-top: 2px solid #dee2e6;">
                                <td colspan="4" class="text-right font-weight-bold text-uppercase text-secondary pt-3">
                                    Total Periode Ini</td>
                                <td class="text-right font-weight-bold text-success pt-3">Rp
                                    <?= number_format($totalMasuk, 0, ',', '.') ?></td>
                                <td class="text-right font-weight-bold text-danger pt-3">Rp
                                    <?= number_format($totalKeluar, 0, ',', '.') ?></td>
                                <td class="text-right font-weight-bold text-primary pt-3"
                                    style="background-color: #e9ecef;">
                                    Rp <?= number_format($saldoAkhir, 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include '../app/views/partials/footer.php'; ?>

<style>
    /* Print Styling */
    @media print {

        .btn,
        .sidebar,
        .navbar,
        .content-header p,
        .card-header form {
            display: none !important;
        }

        .content-header h2 {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .info-box {
            border: 1px solid #ddd !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10pt;
        }

        th,
        td {
            border: 1px solid #000 !important;
            padding: 5px !important;
        }

        .text-success,
        .text-danger,
        .text-info {
            color: #000 !important;
        }

        /* Force black for printers */
    }
</style>