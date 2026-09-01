<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Laporan Pembayaran Siswa</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- FILTER CARD -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form action="" method="get">
                    <input type="hidden" name="mod" value="keuangan_laporan_pembayaran">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="id_kelas" class="form-control select2">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelasList as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>" <?= ($filters['id_kelas'] == $k['id_kelas']) ? 'selected' : '' ?>>
                                            <?= $k['nama_kelas'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kategori Transaksi</label>
                                <select name="id_jenis" class="form-control select2">
                                    <option value="">-- Semua Kategori --</option>
                                    <?php foreach ($jenisList as $j): ?>
                                        <option value="<?= $j['id_jenis'] ?>" <?= ($filters['id_jenis'] == $j['id_jenis']) ? 'selected' : '' ?>>
                                            [<?= $j['kode_akun'] ?>] <?= $j['nama_jenis'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Siswa (Opsional)</label>
                                <select name="id_siswa" id="id_siswa" class="form-control select2">
                                    <option value="">-- Semua Siswa --</option>
                                    <?php if(!empty($siswaList)): ?>
                                        <?php foreach ($siswaList as $s): ?>
                                            <option value="<?= $s['id_siswa'] ?>" <?= ($filters['id_siswa'] == $s['id_siswa']) ? 'selected' : '' ?>>
                                                <?= $s['nama'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Tampilkan Laporan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($filters['id_kelas']) || !empty($filters['id_siswa'])): ?>
            <!-- RESULT AREA -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Hasil Laporan</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" onclick="window.print()"><i class="fas fa-print"></i></button>
                    </div>
                </div>
                <div class="card-body p-0">
                    
                    <?php if (!empty($filters['id_siswa'])): ?>
                        <!-- SIMULASI 3: PER SISWA -->
                        <div class="p-3">
                            <h5>Riwayat Tagihan & Pembayaran: <strong><?= $siswaHeader['nama'] ?? 'Siswa' ?></strong></h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mt-2">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Periode/Tgl</th>
                                            <th>Item / Kategori</th>
                                            <th class="text-right">Tagihan</th>
                                            <th class="text-right">Bayar</th>
                                            <th class="text-right">Sisa</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalTagihan = 0; $totalBayar = 0;
                                        foreach ($reportData['bills'] as $b): 
                                            $totalTagihan += $b['jumlah_tagihan'];
                                        ?>
                                            <tr class="table-secondary">
                                                <td><?= $b['periode'] ?></td>
                                                <td><strong>[Tagihan]</strong> <?= $b['nama_jenis'] ?></td>
                                                <td class="text-right"><?= number_format($b['jumlah_tagihan'], 0, ',', '.') ?></td>
                                                <td class="text-right">-</td>
                                                <td class="text-right"><?= number_format($b['sisa_tagihan'], 0, ',', '.') ?></td>
                                                <td class="text-center"><span class="badge badge-<?= ($b['status'] == 'LUNAS') ? 'success' : 'danger' ?>"><?= $b['status'] ?></span></td>
                                            </tr>
                                            <?php 
                                            // Nested payments for this SPECIFIC bill if linked
                                            foreach ($reportData['payments'] as $p): 
                                                if ($p['id_tagihan'] == $b['id_tagihan']):
                                                    $totalBayar += $p['jumlah'];
                                            ?>
                                                <tr>
                                                    <td><small><?= date('d/m/y', strtotime($p['tanggal'])) ?></small></td>
                                                    <td><i class="fas fa-reply fa-rotate-180 ml-2 text-muted"></i> <small>Bayar: <?= $p['no_bukti'] ?></small></td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right"><small><?= number_format($p['jumlah'], 0, ',', '.') ?></small></td>
                                                    <td class="text-right">-</td>
                                                    <td></td>
                                                </tr>
                                            <?php 
                                                endif; 
                                            endforeach; ?>
                                        <?php endforeach; ?>
                                        
                                        <!-- Unlinked Payments -->
                                        <?php foreach ($reportData['payments'] as $p): 
                                            if (empty($p['id_tagihan'])): 
                                                $totalBayar += $p['jumlah'];
                                        ?>
                                            <tr>
                                                <td><?= date('d/m/y', strtotime($p['tanggal'])) ?></td>
                                                <td><strong>[Bayar Non-Tagihan]</strong> <?= $p['nama_jenis'] ?></td>
                                                <td class="text-right">-</td>
                                                <td class="text-right"><?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                                                <td class="text-right">-</td>
                                                <td class="text-center"><span class="badge badge-info">Pemasukan</span></td>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                    </tbody>
                                    <tfoot class="font-weight-bold bg-light">
                                        <tr>
                                            <td colspan="2" class="text-right">TOTAL</td>
                                            <td class="text-right"><?= number_format($totalTagihan, 0, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($totalBayar, 0, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($totalTagihan - $totalBayar, 0, ',', '.') ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                    <?php elseif (!empty($filters['id_jenis'])): ?>
                        <!-- SIMULASI 1: PER KATEGORI (LIST TUNGGAKAN) -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm mb-0">
                                <thead class="bg-primary">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Siswa</th>
                                        <th>Periode</th>
                                        <th class="text-right">Biaya/Target</th>
                                        <th class="text-right">Sudah Bayar</th>
                                        <th class="text-right">Sisa (Tunggakan)</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1; 
                                    $tTgh = 0; $tByr = 0; $tSisa = 0;
                                    if(!empty($reportData)):
                                        foreach ($reportData as $row): 
                                            $tByr_row = $row['jumlah_tagihan'] - $row['sisa_tagihan'];
                                            $tTgh += $row['jumlah_tagihan'];
                                            $tByr += $tByr_row;
                                            $tSisa += $row['sisa_tagihan'];
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= $row['nama'] ?></td>
                                            <td><?= $row['periode'] ?></td>
                                            <td class="text-right"><?= number_format($row['jumlah_tagihan'], 0, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tByr_row, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-danger"><?= number_format($row['sisa_tagihan'], 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= ($row['status'] == 'LUNAS') ? 'success' : (($row['status'] == 'DICICIL') ? 'warning' : 'danger') ?>">
                                                    <?= $row['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="7" class="text-center py-4">Tidak ada tagihan ditemukan untuk kategori ini.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                                        <td class="text-right"><?= number_format($tTgh, 0, ',', '.') ?></td>
                                        <td class="text-right text-success"><?= number_format($tByr, 0, ',', '.') ?></td>
                                        <td class="text-right text-danger"><?= number_format($tSisa, 0, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    <?php else: ?>
                        <!-- SIMULASI 2: MATRIX (SELURUH KATEGORI) -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-xs mb-0" style="min-width: 1000px;">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th rowspan="2" class="align-middle text-center" width="30">No</th>
                                        <th rowspan="2" class="align-middle">Nama Siswa</th>
                                        <th colspan="<?= count($reportData['categories']) ?>" class="text-center">Kategori & Periode Tagihan</th>
                                        <th rowspan="2" class="align-middle text-right">Total Bayar</th>
                                        <th rowspan="2" class="align-middle text-right">Tunggakan</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($reportData['categories'] as $cat): ?>
                                            <th class="text-center"><small><?= $cat ?></small></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1; 
                                    $colTotals = array_fill_keys($reportData['categories'], 0);
                                    $colArrears = array_fill_keys($reportData['categories'], 0);
                                    $grandTotalPay = 0;
                                    $grandTotalSisa = 0;

                                    foreach ($reportData['matrix'] as $sid => $row): 
                                        $rowTotalPay = 0;
                                        $rowTotalSisa = 0;
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="font-weight-bold"><?= $row['nama'] ?></td>
                                            <?php foreach ($reportData['categories'] as $cat): 
                                                $val = $row['data'][$cat] ?? ['pay'=>0, 'sisa'=>0];
                                                $rowTotalPay += $val['pay'];
                                                $rowTotalSisa += $val['sisa'];
                                                $colTotals[$cat] += $val['pay'];
                                                $colArrears[$cat] += $val['sisa'];
                                            ?>
                                                <td class="text-right">
                                                    <?php if($val['pay'] > 0 || $val['sisa'] > 0): ?>
                                                        <span class="text-success"><?= $val['pay'] > 0 ? number_format($val['pay'], 0, ',', '.') : '-' ?></span>
                                                        <?php if($val['sisa'] > 0): ?>
                                                            <br><small class="text-danger">(-<?= number_format($val['sisa'], 0, ',', '.') ?>)</small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="text-right font-weight-bold text-success"><?= number_format($rowTotalPay, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-danger"><?= number_format($rowTotalSisa, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php 
                                    $grandTotalPay += $rowTotalPay;
                                    $grandTotalSisa += $rowTotalSisa;
                                    endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right">TOTAL BAYAR</td>
                                        <?php foreach ($reportData['categories'] as $cat): ?>
                                            <td class="text-right text-success"><?= number_format($colTotals[$cat], 0, ',', '.') ?></td>
                                        <?php endforeach; ?>
                                        <td class="text-right text-success"><?= number_format($grandTotalPay, 0, ',', '.') ?></td>
                                        <td rowspan="2" class="text-right align-middle text-danger"><?= number_format($grandTotalSisa, 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">TOTAL TUNGGAKAN</td>
                                        <?php foreach ($reportData['categories'] as $cat): ?>
                                            <td class="text-right text-danger"><?= number_format($colArrears[$cat], 0, ',', '.') ?></td>
                                        <?php endforeach; ?>
                                        <td class="text-right text-danger"><?= number_format($grandTotalSisa, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Silakan pilih <strong>Kelas</strong> terlebih dahulu untuk melihat data rekapitulasi.
            </div>
        <?php endif; ?>

    </div>
</section>

<script>
$(document).ready(function() {
    // When class changes, reload student list (for filter dropdown)
    $('select[name="id_kelas"]').on('change', function() {
        var id_kelas = $(this).val();
        if (id_kelas) {
            fetch('<?= BASE_URL ?>keuangan_get_siswa?id_kelas=' + id_kelas)
            .then(res => res.json())
            .then(res => {
                var html = '<option value="">-- Semua Siswa --</option>';
                res.data.forEach(s => {
                    html += '<option value="'+s.id_siswa+'">'+s.nama+'</option>';
                });
                $('#id_siswa').html(html);
            });
        }
    });
});
</script>

<?php include '../app/views/partials/footer.php'; ?>
