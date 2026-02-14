<?php include __DIR__.'/partials/header.php'; ?>
<div class="container-fluid">
  <h2 class="mt-4 mb-3" style="font-size: 1.75rem; font-weight: 600;">Laporan Data Guru</h2>
  
  <div class="filter-box">
    <div class="row align-items-end">
      <div class="col-md-12 text-right">
        <a href="index.php?mod=laporan&act=guru_export_excel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
        <a href="index.php?mod=laporan&act=guru_export_pdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> Export PDF</a>
        <button type="button" onclick="showReportPreview('index.php?mod=laporan&amp;act=guru_print', 'Laporan Data Guru')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
      </div>
    </div>
  </div>
  
  <table class="table table-bordered table-striped">
    <tr>
      <th>No</th><th>Nama</th><th>NUPTK</th><th>NIK</th><th>JK</th><th>Status</th>
    </tr>
    <?php $no=1; foreach ($guru_list as $g): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $g['nama'] ?></td>
      <td><?= $g['nuptk'] ?></td>
      <td><?= $g['nik'] ?></td>
      <td><?= $g['jk'] ?></td>
      <td><?= $g['status'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>