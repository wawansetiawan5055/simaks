<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Jurnal Pembantu</h2>
                <p class="text-muted small mb-0">Laporan detail penerimaan kas per siswa dan jenis pembayaran.</p>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- FILTER CARD -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden; border-top: 4px solid #3b82f6;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-filter mr-2 text-primary"></i> Filter Laporan</h6>
            </div>
            <div class="card-body">
                <form action="" method="get">
                    <input type="hidden" name="mod" value="keuangan_jurnal_pembantu">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Kelas <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-chalkboard text-muted"></i></span>
                                    </div>
                                    <select name="id_kelas" id="filter_kelas" class="form-control border-left-0" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($kelasList as $k): ?>
                                            <option value="<?= $k['id_kelas'] ?>" <?= ($filters['id_kelas'] == $k['id_kelas']) ? 'selected' : '' ?>>
                                                <?= $k['nama_kelas'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- Debug: Filter Kelas = '<?= $filters['id_kelas'] ?>' -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Jenis Transaksi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-tags text-muted"></i></span>
                                    </div>
                                    <select name="id_jenis" class="form-control border-left-0" required>
                                        <option value="">-- Pilih Transaksi --</option>
                                        <?php 
                                        $current_cat = "";
                                        foreach ($jenisList as $j): 
                                            if ($current_cat != $j['nama_kategori']) {
                                                if ($current_cat != "") echo "</optgroup>";
                                                echo "<optgroup label='" . $j['nama_kategori'] . "'>";
                                                $current_cat = $j['nama_kategori'];
                                            }
                                        ?>
                                            <option value="<?= $j['id_jenis'] ?>" <?= ($filters['id_jenis'] == $j['id_jenis']) ? 'selected' : '' ?>>
                                                [<?= $j['kode_akun'] ?>] <?= $j['nama_jenis'] ?>
                                            </option>
                                        <?php endforeach; if ($current_cat != "") echo "</optgroup>"; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Tipe Rekap</label>
                                <select name="tipe_rekap" class="form-control" id="tipe_rekap">
                                    <option value="bulanan" <?= ($filters['tipe_rekap'] == 'bulanan') ? 'selected' : '' ?>>Bulanan</option>
                                    <option value="semester1" <?= ($filters['tipe_rekap'] == 'semester1') ? 'selected' : '' ?>>Semester 1 (Ganjil)</option>
                                    <option value="semester2" <?= ($filters['tipe_rekap'] == 'semester2') ? 'selected' : '' ?>>Semester 2 (Genap)</option>
                                    <option value="tahunan" <?= ($filters['tipe_rekap'] == 'tahunan') ? 'selected' : '' ?>>Tahunan (1 Thn)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" id="filter_bulan_container" style="<?= ($filters['tipe_rekap'] == 'bulanan') ? '' : 'display:none;' ?>">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Bulan</label>
                                <select name="bulan" id="filter_bulan" class="form-control">
                                    <?php 
                                    $bulanIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                                    '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                                    foreach ($bulanIndo as $code => $nama): ?>
                                        <option value="<?= $code ?>" <?= ($filters['bulan'] == $code) ? 'selected' : '' ?>><?= $nama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="form-group mb-3 w-100">
                                <button type="submit" class="btn btn-warning btn-block shadow-sm font-weight-bold text-white"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($filters['id_kelas']) && !empty($filters['id_jenis']) && $jenisInfo): ?>
            <!-- RESULT CARD -->
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1">
                            <?= strtoupper($jenisInfo['nama_jenis']) ?>
                        </h5>
                        <p class="text-muted small mb-0">
                            Kelas: <strong><?= $kelasInfo['nama_kelas'] ?? '-' ?></strong> | 
                            TA: <strong><?= $taInfo['nama_ta'] ?? '-' ?></strong> |
                            Periode: <strong><?= ucfirst($filters['tipe_rekap']) ?></strong>
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="window.print()">
                            <i class="fas fa-print mr-1"></i> Cetak Laporan
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    
                    <?php if ($reportType == 'matrix'): ?>
                        <!-- MATRIX FORMAT (for SPP & Recurring) -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" style="min-width: 1200px;">
                                <thead class="bg-light">
                                    <tr class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                        <th rowspan="2" class="align-middle text-center border-top-0" width="40">No</th>
                                        <th rowspan="2" class="align-middle border-top-0" style="min-width: 200px;">Nama Siswa</th>
                                        <th colspan="<?= count($periods) ?>" class="text-center border-top-0 font-weight-bold text-dark">BULAN</th>
                                        <th rowspan="2" class="align-middle text-right border-top-0" style="width: 120px;">Total Bayar</th>
                                        <th rowspan="2" class="align-middle text-right border-top-0" style="width: 120px;">Tunggakan</th>
                                        <th rowspan="2" class="align-middle text-center border-top-0" style="width: 100px;">Status</th>
                                    </tr>
                                    <tr class="text-muted small">
                                        <?php 
                                        $bulanNames = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
                                        '07'=>'Jul','08'=>'Ags','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
                                        foreach ($periods as $p): 
                                            $m = substr($p, 5, 2);
                                        ?>
                                            <th class="text-center border-top-0 py-2"><?= $bulanNames[$m] ?? $m ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1; 
                                    $colTotals = array_fill_keys($periods, ['bayar' => 0, 'tunggakan' => 0]);
                                    $grandTotalBayar = 0;
                                    $grandTotalTunggakan = 0;

                                    foreach ($reportData as $sid => $row): 
                                        $grandTotalBayar += $row['total_bayar'];
                                        $grandTotalTunggakan += $row['tunggakan'];
                                    ?>
                                        <tr>
                                            <td class="text-center align-middle text-muted"><?= $no++ ?></td>
                                            <td class="align-middle font-weight-bold text-dark"><?= $row['nama'] ?></td>
                                            <?php foreach ($periods as $p): 
                                                $val = $row['months'][$p] ?? ['bayar'=>0, 'sisa'=>0];
                                                $colTotals[$p]['bayar'] += $val['bayar'];
                                                $colTotals[$p]['tunggakan'] += $val['sisa'];
                                            ?>
                                                <td class="text-right align-middle">
                                                    <?php if($val['bayar'] > 0): ?>
                                                        <span class="text-success small font-weight-bold" style="font-size: 0.85rem;"><?= number_format($val['bayar'], 0, ',', '.') ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="text-right align-middle font-weight-bold text-success"><?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                            <td class="text-right align-middle font-weight-bold text-danger"><?= number_format($row['tunggakan'], 0, ',', '.') ?></td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-pill badge-<?= ($row['tunggakan'] == 0) ? 'success' : 'danger' ?> px-3">
                                                    <?= ($row['tunggakan'] == 0) ? 'LUNAS' : 'BELUM' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right text-uppercase small text-muted">Total Bayar</td>
                                        <?php foreach ($periods as $p): ?>
                                            <td class="text-right text-success small"><?= number_format($colTotals[$p]['bayar'], 0, ',', '.') ?></td>
                                        <?php endforeach; ?>
                                        <td class="text-right text-success"><?= number_format($grandTotalBayar, 0, ',', '.') ?></td>
                                        <td rowspan="2" class="text-right align-middle text-danger"><?= number_format($grandTotalTunggakan, 0, ',', '.') ?></td>
                                        <td rowspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right text-uppercase small text-muted">Total Tunggakan</td>
                                        <?php foreach ($periods as $p): ?>
                                            <td class="text-right text-danger small"><?= number_format($colTotals[$p]['tunggakan'], 0, ',', '.') ?></td>
                                        <?php endforeach; ?>
                                        <td class="text-right text-danger"><?= number_format($grandTotalTunggakan, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    <?php else: ?>
                        <!-- LIST FORMAT (for DSP, Ujian, etc.) -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="bg-light">
                                    <tr class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                        <th width="5%" class="text-center border-top-0">No</th>
                                        <th class="border-top-0">Nama Siswa</th>
                                        <th class="text-right border-top-0">Tagihan</th>
                                        <th class="text-right border-top-0">Sudah Bayar</th>
                                        <th class="text-right border-top-0">Sisa</th>
                                        <th class="text-center border-top-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1; 
                                    $tTagihan = 0; $tBayar = 0; $tSisa = 0;
                                    if(!empty($reportData)):
                                        foreach ($reportData as $row): 
                                            $tagihan = $row['jumlah_tagihan'] ?? 0;
                                            $bayar = $row['sudah_bayar'] ?? 0;
                                            $sisa = $row['sisa_tagihan'] ?? $tagihan;
                                            $tTagihan += $tagihan;
                                            $tBayar += $bayar;
                                            $tSisa += $sisa;
                                    ?>
                                        <tr>
                                            <td class="text-center section-middle text-muted"><?= $no++ ?></td>
                                            <td class="align-middle font-weight-bold text-dark"><?= $row['nama'] ?></td>
                                            <td class="text-right align-middle"><?= number_format($tagihan, 0, ',', '.') ?></td>
                                            <td class="text-right align-middle text-success font-weight-bold"><?= number_format($bayar, 0, ',', '.') ?></td>
                                            <td class="text-right align-middle text-danger"><?= number_format($sisa, 0, ',', '.') ?></td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-pill badge-<?= ($row['status'] == 'LUNAS') ? 'success' : 'danger' ?> px-3">
                                                    <?= $row['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr><td colspan="6" class="text-center py-5 text-muted font-italic">Tidak ada data siswa atau belum ada tagihan untuk jenis ini.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="2" class="text-right text-uppercase small text-muted">Total Keseluruhan</td>
                                        <td class="text-right"><?= number_format($tTagihan, 0, ',', '.') ?></td>
                                        <td class="text-right text-success"><?= number_format($tBayar, 0, ',', '.') ?></td>
                                        <td class="text-right text-danger"><?= number_format($tSisa, 0, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-light border shadow-sm text-center py-5" style="border-radius: 15px;">
                <img src="<?= BASE_URL ?>assets/img/search_illustration.svg" alt="Search" style="height: 150px; opacity: 0.5;" class="mb-3">
                <h5 class="text-muted">Laporan Belum Ditampilkan</h5>
                <p class="text-muted small">Silakan pilih <strong>Kelas</strong> dan <strong>Jenis Transaksi</strong> pada filter di atas untuk melihat data.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include '../app/views/partials/footer.php'; ?>

<script>
$(document).ready(function() {
    function toggleBulan() {
        if ($('#tipe_rekap').val() == 'bulanan') {
            $('#filter_bulan_container').fadeIn(200);
        } else {
            $('#filter_bulan_container').hide();
        }
    }

    // Trigger on change
    $('#tipe_rekap').on('change', toggleBulan);
    
    // Auto-Reload on Class Change
    $('#filter_kelas').on('change', function() {
        var id_kelas = $(this).val();
        if(id_kelas) {
            var tipe = $('#tipe_rekap').val() || 'bulanan';
            // Show loading indicator text on the select itself (optional refactoring, but keeping it simple)
            window.location.href = '<?= BASE_URL ?>keuangan_jurnal_pembantu?id_kelas=' + id_kelas + '&tipe_rekap=' + tipe;
        }
    });

    // Trigger on load
    toggleBulan(); 
});
</script>
