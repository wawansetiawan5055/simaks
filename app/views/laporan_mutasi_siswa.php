<?php include __DIR__.'/partials/header.php'; ?>
<div class="container-fluid">
  <h2>Laporan Mutasi Siswa</h2>
  <h3 class="text-muted mb-2 small">TA: <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Belum Dipilih') ?></h3>
  <form method="get" class="mb-2 row">
    <input type="hidden" name="mod" value="laporan">
    <input type="hidden" name="act" value="mutasi_siswa">
    <div class="col-md-2"><input type="date" name="tanggal1" value="<?= $_GET['tanggal1']??'' ?>" class="form-control" required></div>
    <div class="col-md-2"><input type="date" name="tanggal2" value="<?= $_GET['tanggal2']??'' ?>" class="form-control" required></div>
    <div class="col-md-2"><button class="btn btn-info">Tampilkan</button></div>
  </form>
  <div class="mb-2">
    <a href="index.php?mod=laporan&act=mutasi_siswa_export_excel&<?= http_build_query($_GET) ?>" class="btn btn-success btn-sm">Export Excel</a>
    <a href="index.php?mod=laporan&act=mutasi_siswa_export_pdf&<?= http_build_query($_GET) ?>" class="btn btn-danger btn-sm">Export PDF</a>
    <button type="button" onclick="showReportPreview('index.php?mod=laporan&amp;act=mutasi_siswa_print&tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>', 'Laporan Mutasi Siswa')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
  </div>
  <table class="table table-bordered table-striped">
    <tr>
      <th>No</th><th>Tanggal Mutasi</th><th>Nama Siswa</th><th>NISN</th><th>Kelas</th><th>Jenis Mutasi</th><th>Alasan</th>
    </tr>
    <?php $no=1; foreach ($list as $d): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $d['tanggal_mutasi'] ?></td>
      <td><?= $d['nama'] ?></td>
      <td><?= $d['nisn'] ?></td>
      <td><?= $d['nama_kelas'] ?></td>
      <td><?= $d['jenis_mutasi'] ?></td>
      <td><?= $d['alasan'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>