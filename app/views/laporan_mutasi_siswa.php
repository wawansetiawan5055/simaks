<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-exchange-alt"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Mutasi Siswa (Masuk &amp; Keluar)
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Mutasi</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<section class="content">
<div class="container-fluid">
  <form method="get" class="mb-2 row">
    <input type="hidden" name="mod" value="laporan">
    <input type="hidden" name="act" value="mutasi_siswa">
    <div class="col-md-2"><input type="date" name="tanggal1" value="<?= $_GET['tanggal1']??'' ?>" class="form-control" required></div>
    <div class="col-md-2"><input type="date" name="tanggal2" value="<?= $_GET['tanggal2']??'' ?>" class="form-control" required></div>
    <div class="col-md-2"><button class="btn btn-info">Tampilkan</button></div>
  </form>
  <div class="mb-2">
    <a href="<?= BASE_URL ?>laporan/mutasi_siswa_export_excel?<?= http_build_query($_GET) ?>" class="btn btn-success btn-sm">Export Excel</a>
    <a href="<?= BASE_URL ?>laporan/mutasi_siswa_export_pdf?<?= http_build_query($_GET) ?>" class="btn btn-danger btn-sm">Export PDF</a>
    <button type="button" onclick="showReportPreview('<?= BASE_URL ?>laporan/mutasi_siswa_print?tanggal1=<?= $tanggal1 ?>&tanggal2=<?= $tanggal2 ?>', 'Laporan Mutasi Siswa')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
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
</section>
<?php include __DIR__.'/partials/footer.php'; ?>