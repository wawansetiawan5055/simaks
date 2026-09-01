<?php
// app/views/laporan_ppdb.php - Clean, Fast & Standard Laporan PPDB
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$pdf_url = BASE_URL . 'laporan/ppdb_export_pdf';
$excel_url = BASE_URL . 'laporan/ppdb_export_excel';
?>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-user-plus"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Pendaftaran Siswa Baru (PPDB)
          </h4>
          <p class="text-muted small m-0">Tahun Ajaran <?= htmlspecialchars($nama_ta) ?></p>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan PPDB</li>
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
          <i class="fas fa-calendar-check text-primary mr-1"></i> Data Calon Siswa Diterima (PPDB)
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
          <i class="fas fa-list text-primary mr-1"></i> Daftar Calon Peserta Didik Diterima
        </h3>
        <span class="badge badge-primary px-2 py-1"><?= count($list ?? []) ?> Calon Siswa</span>
      </div>
      <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped text-nowrap m-0">
          <thead class="bg-light">
            <tr>
              <th width="4%" class="text-center">No</th>
              <th width="16%">No Pendaftaran</th>
              <th>Nama Lengkap Siswa</th>
              <th width="14%">NISN</th>
              <th width="14%">NIPD Baru</th>
              <th width="8%" class="text-center">L/P</th>
              <th>Sekolah Asal</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i class="fas fa-info-circle fa-3x mb-3 text-muted"></i><br>
                  Belum ada data pendaftar yang diterima pada Tahun Ajaran ini.
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; foreach ($list as $data): ?>
                <tr>
                  <td class="text-center font-weight-bold"><?= $no++ ?></td>
                  <td><code><?= htmlspecialchars($data['nomor_pendaftaran'] ?? '-') ?></code></td>
                  <td class="font-weight-bold text-dark"><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                  <td><code><?= htmlspecialchars($data['nisn'] ?: '-') ?></code></td>
                  <td><code><?= htmlspecialchars($data['nipd'] ?: '-') ?></code></td>
                  <td class="text-center"><?= htmlspecialchars($data['jenis_kelamin']) ?></td>
                  <td><?= htmlspecialchars($data['sekolah_asal'] ?: '-') ?></td>
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