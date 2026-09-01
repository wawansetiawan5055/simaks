<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Data Guru &amp; GTK
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Guru</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<section class="content">
<div class="container-fluid">
  
  <div class="filter-box">
    <div class="row align-items-end">
      <div class="col-md-12 text-right">
        <a href="<?= BASE_URL ?>laporan/guru_export_excel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
        <a href="<?= BASE_URL ?>laporan/guru_export_pdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> Export PDF</a>
        <button type="button" onclick="showReportPreview('<?= BASE_URL ?>laporan/guru_print', 'Laporan Data Guru')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
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
</section>
<?php include __DIR__.'/partials/footer.php'; ?>