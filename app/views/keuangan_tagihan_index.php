<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Daftar Tagihan &amp; Pembayaran Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>keuangan_tagihan/create" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
                    <i class="fas fa-magic mr-1"></i> Generate Tagihan Massal
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Data Tagihan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filter Form -->
                <form action="" method="get" class="form-inline mb-3">
                    <input type="hidden" name="mod" value="keuangan_tagihan">
                    <input type="hidden" name="act" value="index">
                    
                    <div class="form-group mr-2">
                        <select name="id_kelas" class="form-control form-control-sm">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($filters['id_kelas'] == $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= $k['nama_kelas'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mr-2">
                        <select name="id_jenis" class="form-control form-control-sm">
                            <option value="">-- Jenis Tagihan --</option>
                            <?php foreach ($jenisList as $j): ?>
                                <option value="<?= $j['id_jenis'] ?>" <?= ($filters['id_jenis'] == $j['id_jenis']) ? 'selected' : '' ?>>
                                    <?= $j['nama_jenis'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mr-2">
                        <select name="id_ta" class="form-control form-control-sm">
                            <option value="all" <?= ($filters['id_ta'] == 'all') ? 'selected' : '' ?>>-- Semua Tahun Ajaran --</option>
                            <option value="" <?= ($filters['id_ta'] === '') ? 'selected' : '' ?>>-- Pilih TA --</option>
                            <?php 
                            $stmtTa = connect_db()->query("SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC");
                            while($ta = $stmtTa->fetch()): ?>
                                <option value="<?= $ta['id_ta'] ?>" <?= ($filters['id_ta'] == $ta['id_ta']) ? 'selected' : '' ?>>
                                    <?= $ta['nama_ta'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group mr-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="BELUM_BAYAR" <?= ($filters['status'] == 'BELUM_BAYAR') ? 'selected' : '' ?>>Belum Bayar</option>
                            <option value="DICICIL" <?= ($filters['status'] == 'DICICIL') ? 'selected' : '' ?>>Dicicil</option>
                            <option value="LUNAS" <?= ($filters['status'] == 'LUNAS') ? 'selected' : '' ?>>Lunas</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="<?= BASE_URL ?>keuangan_tagihan/index" class="btn btn-sm btn-default ml-1"><i class="fas fa-sync"></i> Reset</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-nowrap" id="table-tagihan">
                        <thead class="bg-light">
                            <tr>
                                <th width="10">No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Tagihan</th>
                                <th>Periode</th>
                                <th>Jatuh Tempo</th>
                                <th>Jumlah</th>
                                <th>Terbayar</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tagihan)): ?>
                                <?php $no = 1; foreach ($tagihan as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="font-weight-bold"><?= $row['nama_siswa'] ?></div>
                                        <small class="text-muted"><?= $row['nisn'] ?></small>
                                    </td>
                                    <td><?= $row['nama_kelas'] ?></td>
                                    <td><?= $row['nama_jenis'] ?></td>
                                    <td><?= $row['periode'] ?></td>
                                    <td>
                                        <?php 
                                            $jatuh_tempo = strtotime($row['tanggal_jatuh_tempo']);
                                            $now = time();
                                            $is_late = ($jatuh_tempo < $now && $row['status'] != 'LUNAS');
                                        ?>
                                        <span class="<?= $is_late ? 'text-danger font-weight-bold' : '' ?>">
                                            <?= date('d/m/Y', $jatuh_tempo) ?>
                                        </span>
                                    </td>
                                    <td class="text-right">Rp <?= number_format($row['jumlah_tagihan'], 0, ',', '.') ?></td>
                                    <td class="text-right text-success">Rp <?= number_format($row['jumlah_terbayar'], 0, ',', '.') ?></td>
                                    <td class="text-right text-danger font-weight-bold">Rp <?= number_format($row['sisa_tagihan'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php if($row['status'] == 'LUNAS'): ?>
                                            <span class="badge badge-success">LUNAS</span>
                                        <?php elseif($row['status'] == 'CICIL'): ?>
                                            <span class="badge badge-warning">CICIL</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">BELUM</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-info" title="Detail"><i class="fas fa-eye"></i></button>
                                        <!-- Add Quick Pay button later -->
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="11" class="text-center">Data tidak ditemukan</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../app/views/partials/footer.php'; ?>
