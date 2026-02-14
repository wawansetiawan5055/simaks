<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-file-alt mr-2"></i> Laporan Rekapitulasi Kelas</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Data Kelas & Wali Kelas (TA:
                    <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? '-') ?>)
                </h3>

                <div class="card-tools">
                    <a href="index.php?mod=laporan&act=kelas_export_excel" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="index.php?mod=laporan&act=kelas_export_pdf" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <button type="button"
                        onclick="showReportPreview('index.php?mod=laporan&amp;act=kelas_print', 'Laporan Rekapitulasi Kelas')"
                        class="btn btn-info btn-sm">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                </div>
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover table-bordered text-center">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 2%" rowspan="2" class="align-middle">No</th>
                            <th style="width: 25%" rowspan="2" class="align-middle text-left">Nama Kelas</th>
                            <th style="width: 30%" rowspan="2" class="align-middle text-left">Wali Kelas</th>
                            <th colspan="3" class="align-middle">Jumlah Siswa</th>
                        </tr>
                        <tr>
                            <th style="width: 10%">L</th>
                            <th style="width: 10%">P</th>
                            <th style="width: 10%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kelas_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Data kelas belum tersedia.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $no = 1;
                            $tot_l = 0;
                            $tot_p = 0;
                            $tot_all = 0;
                            foreach ($kelas_list as $d):
                                $total_kelas = $d['jml_l'] + $d['jml_p'];
                                // Hitung Grand Total
                                $tot_l += $d['jml_l'];
                                $tot_p += $d['jml_p'];
                                $tot_all += $total_kelas;
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="text-left font-weight-bold">
                                        <?= htmlspecialchars($d['nama_kelas']) ?>
                                    </td>
                                    <td class="text-left">
                                        <?= htmlspecialchars($d['nama_walas'] ?? '- Belum Ada -') ?>
                                    </td>
                                    <td><?= $d['jml_l'] ?></td>
                                    <td><?= $d['jml_p'] ?></td>
                                    <td class="font-weight-bold"><?= $total_kelas ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr class="bg-secondary font-weight-bold">
                                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                                <td><?= $tot_l ?></td>
                                <td><?= $tot_p ?></td>
                                <td><?= $tot_all ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>