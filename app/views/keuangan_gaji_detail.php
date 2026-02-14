<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>
<?php
$bulanName = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h2 class="fw-bold m-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary mr-2"></i> Detail Gaji</h2>
                <p class="text-muted small mb-0">
                    Periode: <strong><?= $bulanName[$gaji['bulan']] ?> <?= $gaji['tahun'] ?></strong> | 
                    Status: <span class="badge badge-<?= $gaji['status'] == 'FINAL' ? 'success' : 'warning' ?>"><?= $gaji['status'] ?></span>
                </p>
            </div>
            <div class="text-end">
                <a href="index.php?mod=keuangan_gaji&act=print_rekap&id=<?= $gaji['id_gaji'] ?>" target="_blank" class="btn btn-warning shadow-sm border mr-2">
                    <i class="fas fa-print mr-1"></i> Cetak Rekap
                </a>
                <a href="index.php?mod=keuangan_gaji" class="btn btn-default shadow-sm border mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <?php if($gaji['status'] == 'DRAFT'): ?>
                <a href="index.php?mod=keuangan_gaji&act=recalculate&id=<?= $gaji['id_gaji'] ?>" class="btn btn-primary shadow-sm border mr-2" onclick="return confirm('Hitung ulang semua gaji berdasarkan data penugasan dan matrix terbaru?')">
                    <i class="fas fa-sync-alt mr-1"></i> Hitung Ulang
                </a>
                <a href="index.php?mod=keuangan_gaji&act=finalize&id=<?= $gaji['id_gaji'] ?>" class="btn btn-success shadow-sm border mr-2" onclick="return confirm('Apakah Anda yakin ingin melakukan finalisasi gaji ini? Setelah finalisasi, rincian tidak dapat diubah kembali.')">
                    <i class="fas fa-check-double mr-1"></i> Finalisasi Gaji
                </a>
                <a href="index.php?mod=keuangan_gaji&act=delete&id=<?= $gaji['id_gaji'] ?>" class="btn btn-danger shadow-sm" onclick="return confirm('Hapus periode gaji ini?')">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content px-3">
    <div class="container-fluid">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'regenerated'): ?>
        <div class="alert alert-success shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-1"></i> Berhasil menghitung ulang semua rincian gaji guru/staff.
        </div>
        <?php endif; ?>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'finalized'): ?>
        <div class="alert alert-success shadow-sm border-0 mb-4" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-1"></i> Periode gaji telah berhasil difinalisasi.
        </div>
        <?php endif; ?>
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                            <tr>
                                <th class="text-center py-3" width="40">No</th>
                                <th class="py-3">Nama Pegawai</th>
                                <th class="py-3" style="min-width: 200px;">Honor Mengajar</th>
                                <th class="py-3" style="min-width: 250px;">Tunjangan Jabatan/Tugas Tambahan</th>
                                <th class="py-3 text-danger" style="min-width: 150px;">Potongan</th>
                                <th class="text-right py-3 bg-light font-weight-bold text-dark" width="150">Total Diterima</th>
                                <th class="text-center py-3" width="80">Slip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($details)): 
                                $no = 1;
                                $totalAll = 0;
                                foreach($details as $row): 
                                    $sub_honor = $row['subtotal_jjm'] + $row['subtotal_transport'] + $row['subtotal_kinerja'] + $row['tunj_ekskul'];
                                    
                                    $sub_tunjangan = $row['tunj_kepsek'] + $row['tunj_tas'] + $row['tunj_plk'] + $row['tunj_penjaga'] + $row['tunj_satpam'] + $row['tunj_sopir'] +
                                                     $row['tunj_kurikulum'] + $row['tunj_kesiswaan'] + $row['tunj_sarpras'] + $row['tunj_humas'] + 
                                                     $row['tunj_walas'] + 
                                                     $row['tunj_kepala_lab'] + $row['tunj_kepala_perpus'] + $row['tunj_operator'] + $row['tunj_pembina_keagamaan'] + $row['tunj_pengelola_smater'] +
                                                     $row['tunj_pembina'] + $row['tunjangan_lain'];
                                                     
                                    $sub_potongan = $row['potongan_bpjs_kes'] + $row['potongan_bpjs_tk'] + $row['potongan_kasbon'] + $row['potongan_lain'];
                                    $totalAll += $row['total_diterima'];
                            ?>
                            <tr>
                                <td class="text-center align-middle small bg-light"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-dark mb-0"><?= $row['nama_guru'] ?></div>
                                    <div class="small text-muted"><?= $row['nip'] ?? '-' ?></div>
                                </td>
                                
                                <!-- Honorarium (Berdasarkan Absensi) -->
                                <td class="align-middle small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>JJM (<?= $row['jml_jjm'] ?>):</span> 
                                        <span><?= number_format($row['subtotal_jjm'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Transport (<?= $row['jml_hadir'] ?>):</span> 
                                        <span><?= number_format($row['subtotal_transport'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Insentif KBM (<?= $row['jml_kbm'] ?>):</span> 
                                        <span><?= number_format($row['subtotal_kinerja'], 0, ',', '.') ?></span>
                                    </div>
                                    <?php if($row['tunj_ekskul'] > 0 || $row['jml_ekskul'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Ekskul (<?= $row['jml_ekskul'] ?>):</span> 
                                        <span><?= number_format($row['tunj_ekskul'], 0, ',', '.') ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="border-top mt-1 pt-1 font-weight-bold d-flex justify-content-between text-primary">
                                        <span>Total Honor:</span> <span><?= number_format($sub_honor, 0, ',', '.') ?></span>
                                    </div>
                                </td>

                                <!-- Tunjangan (Tetap/Jabatan) -->
                                <td class="align-middle small border-left">
                                    <?php 
                                    $tunjArray = [
                                        'Kepsek' => $row['tunj_kepsek'],
                                        'TAS' => $row['tunj_tas'],
                                        'PLK' => $row['tunj_plk'],
                                        'Penjaga' => $row['tunj_penjaga'],
                                        'Satpam' => $row['tunj_satpam'],
                                        'Sopir' => $row['tunj_sopir'],
                                        'Kurikulum' => $row['tunj_kurikulum'],
                                        'Kesiswaan' => $row['tunj_kesiswaan'],
                                        'Sarpras' => $row['tunj_sarpras'],
                                        'Humas' => $row['tunj_humas'],
                                        'Walas' => $row['tunj_walas'],
                                        'K. Lab' => $row['tunj_kepala_lab'],
                                        'K. Perpus' => $row['tunj_kepala_perpus'],
                                        'Operator' => $row['tunj_operator'],
                                        'Pembina Keag.' => $row['tunj_pembina_keagamaan'],
                                        'Pengelola SMATER' => $row['tunj_pengelola_smater'],
                                        'Pembina Lain' => $row['tunj_pembina'],
                                        'Lainnya' => $row['tunjangan_lain']
                                    ];
                                    foreach($tunjArray as $label => $val):
                                        if($val > 0): ?>
                                            <div class="d-flex justify-content-between mb-0" style="font-size: 0.75rem;">
                                                <span class="text-muted"><?= $label ?>:</span> 
                                                <span><?= number_format($val, 0, ',', '.') ?></span>
                                            </div>
                                        <?php endif; 
                                    endforeach; ?>
                                    
                                    <div class="border-top mt-1 pt-1 font-weight-bold d-flex justify-content-between text-success">
                                        <span>Total Tunjangan:</span> <span><?= number_format($sub_tunjangan, 0, ',', '.') ?></span>
                                    </div>
                                </td>

                                <!-- Potongan -->
                                <td class="align-middle small text-danger border-left">
                                    <?php if($row['potongan_bpjs_kes'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-1"><span>BPJS Kes:</span> <span><?= number_format($row['potongan_bpjs_kes'], 0, ',', '.') ?></span></div>
                                    <?php endif; ?>
                                    <?php if($row['potongan_bpjs_tk'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-1"><span>BPJS TK:</span> <span><?= number_format($row['potongan_bpjs_tk'], 0, ',', '.') ?></span></div>
                                    <?php endif; ?>
                                    <?php if($row['potongan_kasbon'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-1"><span>Pot. Kasbon:</span> <span><?= number_format($row['potongan_kasbon'], 0, ',', '.') ?></span></div>
                                    <?php endif; ?>
                                    <?php if($row['potongan_lain'] > 0): ?>
                                    <div class="d-flex justify-content-between mb-1"><span>Pot. Lain:</span> <span><?= number_format($row['potongan_lain'], 0, ',', '.') ?></span></div>
                                    <?php endif; ?>
                                    
                                    <div class="border-top mt-1 pt-1 font-weight-bold d-flex justify-content-between">
                                        <span>Total Potongan:</span> <span><?= number_format($sub_potongan, 0, ',', '.') ?></span>
                                    </div>
                                </td>

                                <td class="text-right align-middle font-weight-bold bg-light" style="font-size: 1.1rem;">
                                    <?= number_format($row['total_diterima'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="index.php?mod=keuangan_gaji&act=print_slip&id_detail=<?= $row['id_detail'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Slip">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-white border-top">
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold pt-3 text-uppercase">Total Pengeluaran Gaji</td>
                                <td class="text-right font-weight-bold text-primary pt-3" style="font-size: 1.2rem;">
                                    Rp <?= number_format($totalAll, 0, ',', '.') ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5">Detail kosong.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../app/views/partials/footer.php'; ?>
