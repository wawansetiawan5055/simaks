<?php
// app/views/laporan_mutasi_siswa.php - Clean, Fast & Standard Laporan Mutasi Siswa
include __DIR__ . '/partials/header.php'; 

$nama_ta = $_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Tahun Ajaran Aktif';
$tanggal1 = $_GET['tanggal1'] ?? '';
$tanggal2 = $_GET['tanggal2'] ?? '';
$query_string_custom = "tanggal1=" . urlencode($tanggal1) . "&tanggal2=" . urlencode($tanggal2);
$pdf_url = BASE_URL . 'laporan/mutasi_siswa_export_pdf?' . $query_string_custom;
$excel_url = BASE_URL . 'laporan/mutasi_siswa_export_excel?' . $query_string_custom;
?>

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
          <p class="text-muted small m-0">Tahun Ajaran <?= htmlspecialchars($nama_ta) ?></p>
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
    
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body p-3">
        <form method="get" class="m-0">
          <input type="hidden" name="mod" value="laporan">
          <input type="hidden" name="act" value="mutasi_siswa">
          
          <div class="row align-items-end">
            <div class="col-md-3 form-group mb-2 mb-md-0">
              <label class="font-weight-bold small text-muted text-uppercase mb-1">Tanggal Mulai</label>
              <input type="date" name="tanggal1" value="<?= htmlspecialchars($tanggal1) ?>" class="form-control font-weight-bold" required>
            </div>
            <div class="col-md-3 form-group mb-2 mb-md-0">
              <label class="font-weight-bold small text-muted text-uppercase mb-1">Tanggal Selesai</label>
              <input type="date" name="tanggal2" value="<?= htmlspecialchars($tanggal2) ?>" class="form-control font-weight-bold" required>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                <i class="fas fa-search mr-1"></i> Tampilkan
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-bottom-0 pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap">
        <h3 class="card-title font-weight-bold text-dark mb-2 mb-md-0">
          <i class="fas fa-list text-primary mr-1"></i> Riwayat Mutasi Siswa
        </h3>
        <div class="btn-group shadow-sm">
          <a href="<?= $excel_url ?>" class="btn btn-success btn-sm font-weight-bold px-3">
            <i class="fas fa-file-excel mr-1"></i> Excel
          </a>
          <a href="<?= $pdf_url ?>" class="btn btn-danger btn-sm font-weight-bold px-3" target="_blank">
            <i class="fas fa-file-pdf mr-1"></i> PDF
          </a>
          <a href="<?= $pdf_url ?>" class="btn btn-info btn-sm font-weight-bold px-3" target="_blank">
            <i class="fas fa-print mr-1"></i> Cetak
          </a>
        </div>
      </div>
      <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped text-nowrap m-0">
          <thead class="bg-light">
            <tr>
              <th width="4%" class="text-center">No</th>
              <th width="14%">Tanggal Mutasi</th>
              <th>Nama Siswa</th>
              <th width="14%">NISN</th>
              <th width="12%" class="text-center">Kelas</th>
              <th width="12%" class="text-center">Jenis Mutasi</th>
              <th>Alasan / Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($list)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i class="fas fa-info-circle fa-3x mb-3 text-muted"></i><br>
                  Tidak ada data mutasi siswa ditemukan untuk rentang tanggal ini.
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; foreach ($list as $d): ?>
                <tr>
                  <td class="text-center font-weight-bold"><?= $no++ ?></td>
                  <td><?= tgl_indo($d['tanggal_mutasi']) ?></td>
                  <td class="font-weight-bold text-dark"><?= htmlspecialchars($d['nama']) ?></td>
                  <td><code><?= htmlspecialchars($d['nisn'] ?: '-') ?></code></td>
                  <td class="text-center"><span class="badge badge-light border">Kelas <?= htmlspecialchars($d['nama_kelas'] ?? '-') ?></span></td>
                  <td class="text-center">
                    <?php if (stripos($d['jenis_mutasi'], 'Masuk') !== false): ?>
                      <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-down mr-1"></i>Masuk</span>
                    <?php else: ?>
                      <span class="badge badge-warning px-2 py-1"><i class="fas fa-arrow-up mr-1"></i>Keluar</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($d['alasan'] ?: '-') ?></td>
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