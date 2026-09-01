<?php
// app/views/laporan_mapel.php - Clean, Fast & Standard Laporan Mapel
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$pdf_url = BASE_URL . 'laporan/mapel_export_pdf';
$excel_url = BASE_URL . 'laporan/mapel_export_excel';
?>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-book"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Data Mata Pelajaran
          </h4>
          <p class="text-muted small m-0">Tahun Ajaran <?= htmlspecialchars($nama_ta) ?></p>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Mapel</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap">
        <div class="font-weight-bold text-dark">
          <i class="fas fa-graduation-cap text-primary mr-1"></i> Kurikulum Mata Pelajaran
        </div>
        <div class="btn-group shadow-sm mt-2 mt-md-0">
          <a href="<?= $excel_url ?>" class="btn btn-success font-weight-bold px-3">
            <i class="fas fa-file-excel mr-1"></i> Excel
          </a>
          <a href="<?= $pdf_url ?>" class="btn btn-danger font-weight-bold px-3" target="_blank">
            <i class="fas fa-file-pdf mr-1"></i> PDF
          </a>
          <a href="<?= $pdf_url ?>" class="btn btn-info font-weight-bold px-3" target="_blank">
            <i class="fas fa-print mr-1"></i> Cetak
          </a>
        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-dark">
          <i class="fas fa-list text-primary mr-1"></i> Daftar Mata Pelajaran
        </h3>
        <span class="badge badge-primary px-2 py-1"><?= count($mapel_list) ?> Mapel</span>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap m-0">
          <thead class="bg-light">
            <tr>
              <th width="5%" class="text-center">No</th>
              <th>Nama Mata Pelajaran</th>
              <th width="25%">Kategori / Kelompok</th>
              <th width="15%" class="text-center">KKTP / KKM</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($mapel_list)): ?>
              <tr>
                <td colspan="4" class="text-center text-muted py-5">
                  <i class="fas fa-book-open fa-3x mb-3 text-muted"></i><br>
                  Tidak ada data mata pelajaran ditemukan.
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; foreach ($mapel_list as $m): ?>
                <tr>
                  <td class="text-center font-weight-bold"><?= $no++ ?></td>
                  <td class="font-weight-bold text-dark"><?= htmlspecialchars($m['nama_mapel']) ?></td>
                  <td>
                    <span class="badge badge-light border px-2 py-1 font-weight-bold">
                      <?= htmlspecialchars($m['kategori_mapel'] ?: 'Umum') ?>
                    </span>
                  </td>
                  <td class="text-center font-weight-bold text-primary"><?= htmlspecialchars($m['kktp'] ?: '75') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>