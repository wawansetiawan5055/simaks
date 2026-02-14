<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Laporan Catatan Kejadian Kelas</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
<div class="container-fluid">
    
    <form method="GET">
        <input type="hidden" name="mod" value="laporan">
        <input type="hidden" name="act" value="catatan_kelas">
        
        <div class="filter-box">
            <div class="row align-items-end">
                <div class="col-md-3 form-group">
                    <label>Pilih Kelas</label>
                    <select name="kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal1" class="form-control" value="<?= htmlspecialchars($tanggal1) ?>" required>
                </div>
                <div class="col-md-3 form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal2" class="form-control" value="<?= htmlspecialchars($tanggal2) ?>" required>
                </div>
                <div class="col-md-3 form-group">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Hasil Laporan</h3>
            <div class="card-tools">
                <a href="index.php?mod=laporan&act=catatan_kelas_export_excel&kelas=<?= $kelas ?>&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="index.php?mod=laporan&act=catatan_kelas_export_pdf&kelas=<?= $kelas ?>&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <button type="button" onclick="showReportPreview('index.php?mod=laporan&amp;act=catatan_kelas_print&kelas=<?= $kelas ?>&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>', 'Laporan Catatan Kelas')" class="btn btn-info btn-sm" title="Cetak"><i class="fas fa-print"></i> Cetak</button>
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
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Catatan Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($list as $l): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($l['tanggal']) ?></td>
                        <td><?= htmlspecialchars($l['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($l['nama_mapel']) ?></td>
                        <td><?= htmlspecialchars($l['nama_guru_mapel']) ?></td>
                        <td><?= htmlspecialchars($l['catatan_kejadian']) ?></td>
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
<?php include __DIR__.'/partials/footer.php'; ?>