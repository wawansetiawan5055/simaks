<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Manajemen Gaji &amp; Payroll GTK
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>keuangan_gaji/setting" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm mr-1">
                    <i class="fas fa-cog mr-1"></i> Setting Tarif
                </a>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalGenerate">
                    <i class="fas fa-plus mr-1"></i> Buat Gaji Baru
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0" style="border-radius: 15px; border-top: 4px solid #007bff;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3" width="50">No</th>
                                <th class="py-3">Periode Gaji</th>
                                <th class="py-3">Tanggal Generate</th>
                                <th class="py-3 text-right">Total Pengeluaran</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $bulanName = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                if(!empty($list_gaji)): 
                                $no = 1;
                                foreach($list_gaji as $row): 
                            ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle font-weight-bold text-primary">
                                    <?= $bulanName[$row['bulan']] ?> <?= $row['tahun'] ?>
                                </td>
                                <td class="align-middle text-muted">
                                    <i class="far fa-clock mr-1"></i> <?= date('d/m/Y', strtotime($row['tgl_generate'])) ?>
                                </td>
                                <td class="align-middle text-right font-weight-bold text-success">
                                    Rp <?= number_format($row['total_pengeluaran'], 0, ',', '.') ?>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-<?= $row['status'] == 'FINAL' ? 'success' : 'warning' ?> px-3 py-2" style="border-radius: 10px;">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="<?= BASE_URL ?>keuangan_gaji/detail?id=<?= $row['id_gaji'] ?>" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data gaji. Silakan buat baru.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Generate -->
<div class="modal fade" id="modalGenerate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Buat Hitungan Gaji Baru</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?= BASE_URL ?>keuangan_gaji/generate" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control" required>
                            <?php 
                            $currentMonth = date('n');
                            for($i=1; $i<=12; $i++) {
                                $sel = ($i == $currentMonth) ? 'selected' : '';
                                echo "<option value='$i' $sel>" . $bulanName[$i] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle mr-1"></i> Sistem akan menghitung otomatis:
                        <ul class="mb-0 pl-3">
                            <li>JJM dari Jadwal Pelajaran (x 4 minggu)</li>
                            <li>Kehadiran dari Absensi Guru</li>
                            <li>Kinerja dari Jurnal KBM</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Proses Hitung</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../app/views/partials/footer.php'; ?>
