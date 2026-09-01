<?php
// app/views/laporan_kelas.php - Clean, Fast & Standard Laporan Kelas
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$pdf_url = BASE_URL . 'laporan/kelas_export_pdf';
$excel_url = BASE_URL . 'laporan/kelas_export_excel';
?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Laporan Rekapitulasi Rombel &amp; Kelas
                    </h4>
                    <p class="text-muted small m-0">Tahun Ajaran <?= htmlspecialchars($nama_ta) ?></p>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Kelas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
                <div class="font-weight-bold text-dark">
                    <i class="fas fa-calendar-check text-primary mr-1"></i> Rekapitulasi Rombel &amp; Wali Kelas
                </div>
                <div class="btn-group shadow-sm mt-2 mt-md-0">
                    <a href="<?= $excel_url ?>" class="btn btn-success font-weight-bold px-3">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="<?= $pdf_url ?>" class="btn btn-danger font-weight-bold px-3" target="_blank">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                    <a href="<?= $pdf_url ?>" class="btn btn-info font-weight-bold px-3" target="_blank">
                        <i class="fas fa-print mr-1"></i> Cetak
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-list text-primary mr-1"></i> Daftar Rombongan Belajar
                </h3>
                <span class="badge badge-primary px-2 py-1"><?= count($kelas_list) ?> Kelas</span>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover table-bordered text-center m-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 4%" rowspan="2" class="align-middle">No</th>
                            <th style="width: 28%" rowspan="2" class="align-middle text-left">Nama Rombel / Kelas</th>
                            <th style="width: 36%" rowspan="2" class="align-middle text-left">Wali Kelas</th>
                            <th colspan="3" class="align-middle">Jumlah Siswa</th>
                        </tr>
                        <tr>
                            <th style="width: 11%">L</th>
                            <th style="width: 11%">P</th>
                            <th style="width: 10%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kelas_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-info-circle fa-3x mb-3 text-muted"></i><br>
                                    Data kelas belum tersedia.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $no = 1;
                            $tot_l = 0;
                            $tot_p = 0;
                            $tot_all = 0;
                            foreach ($kelas_list as $d):
                                $total_kelas = $d['jml_l'] + $d['jml_p'];
                                $tot_l += $d['jml_l'];
                                $tot_p += $d['jml_p'];
                                $tot_all += $total_kelas;
                            ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $no++ ?></td>
                                    <td class="text-left font-weight-bold text-dark">
                                        Kelas <?= htmlspecialchars($d['nama_kelas']) ?>
                                    </td>
                                    <td class="text-left">
                                        <?= htmlspecialchars($d['nama_walas'] ?? '- Belum Ditentukan -') ?>
                                    </td>
                                    <td><?= $d['jml_l'] ?></td>
                                    <td><?= $d['jml_p'] ?></td>
                                    <td class="font-weight-bold text-primary"><?= $total_kelas ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr class="bg-light font-weight-bold text-dark" style="font-size: 1rem;">
                                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                                <td class="text-info"><?= $tot_l ?></td>
                                <td class="text-pink"><?= $tot_p ?></td>
                                <td class="text-success"><?= $tot_all ?> Siswa</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>