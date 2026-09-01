<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Laporan Catatan Kasus Siswa (BK)
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Kasus BK</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <form method="GET">
            <input type="hidden" name="mod" value="laporan">
            <input type="hidden" name="act" value="catatan_kasus">

            <div class="filter-box">
                <div class="row align-items-end">
                    <div class="col-md-3 form-group">
                        <label>Pilih Kelas</label>
                        <select name="kelas" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal1" class="form-control"
                            value="<?= htmlspecialchars($tanggal1) ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal2" class="form-control"
                            value="<?= htmlspecialchars($tanggal2) ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i>
                            Tampilkan</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Hasil Laporan</h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>laporan/catatan_kasus_export_excel?kelas=<?= $kelas ?>&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>"
                        class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
                    <a href="<?= BASE_URL ?>laporan/catatan_kasus_export_pdf?kelas=<?= $kelas ?>&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>"
                        class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
                    <button type="button"
                        onclick="showReportPreview('<?= BASE_URL ?>laporan/catatan_kasus_print?kelas=<?= $kelas ?>&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>', 'Laporan Catatan Kasus')"
                        class="btn btn-info btn-sm" title="Cetak"><i class="fas fa-print"></i> Cetak</button>
                </div>
            </div>
            <div class="card-body p-0 table-responsive">
                <?php if (!empty($list)): ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kelas</th>
                                <th>Nama Siswa</th>
                                <th>Kasus / Catatan</th>
                                <th>Tindak Lanjut</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($list as $l): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($l['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($l['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($l['nama']) ?></td>

                                    <td><?= htmlspecialchars($l['catatan'] ?? 'N/A') ?></td>

                                    <td><?= htmlspecialchars($l['tindak_lanjut']) ?></td>

                                    <td><?= htmlspecialchars($l['keterangan'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning m-3">
                        <?php if (isset($_GET['kelas'])): ?>
                            Data tidak ditemukan untuk filter yang dipilih. Silakan coba rentang tanggal atau kelas lain.
                        <?php else: ?>
                            Silakan isi filter dan klik "Tampilkan Data" untuk melihat laporan.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>