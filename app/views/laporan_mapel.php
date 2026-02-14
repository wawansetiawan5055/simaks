<?php include __DIR__.'/partials/header.php'; ?>
<div class="container-fluid">
  <h2 class="mt-4 mb-3" style="font-size: 1.75rem; font-weight: 600;">Laporan Data Mata Pelajaran</h2>
  <div class="mb-2">
    <a href="index.php?mod=laporan&act=mapel_export_excel" class="btn btn-success btn-sm">Export Excel</a>
    <a href="index.php?mod=laporan&act=mapel_export_pdf" class="btn btn-danger btn-sm">Export PDF</a>
    <button type="button" onclick="showReportPreview('index.php?mod=laporan&amp;act=mapel_print', 'Laporan Mata Pelajaran')" class="btn btn-info btn-sm"> <i class="fas fa-print"></i> Cetak</button>
  </div>
  <table class="table table-bordered table-striped">
    <tr>
      <th>No</th><th>Nama Mapel</th><th>Kategori</th><th>KKTP</th>
    </tr>
    <?php $no=1; foreach ($mapel_list as $m): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $m['nama_mapel'] ?></td>
      <td><?= $m['kategori_mapel'] ?></td>
      <td><?= $m['kktp'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>