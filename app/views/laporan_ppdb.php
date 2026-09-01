<?php include __DIR__.'/partials/header.php'; ?>
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
    <?php if (!$id_ta_tampil): ?>
        <div class="alert alert-danger">Silakan pilih Tahun Ajaran di navbar untuk menampilkan data.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Siswa Diterima via PPDB (Urut Alfabet)</h3>
            <div class="card-tools">
                <a href="<?= BASE_URL ?>laporan/ppdb_export_excel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="<?= BASE_URL ?>laporan/ppdb_export_pdf" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <button type="button" onclick="showReportPreview('<?= BASE_URL ?>laporan/ppdb_print', 'Laporan PPDB')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">No</th>
                        <th>No Pendaftaran</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>NIPD (Baru)</th>
                        <th>JK</th>
                        <th>Sekolah Asal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr><td colspan="7" class="text-center">Belum ada data pendaftar yang diterima pada TA ini.</td></tr>
                    <?php endif; ?>
                    
                    <?php $no = 1; foreach ($list as $data): ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= htmlspecialchars($data['no_pendaftaran']); ?></td>
                        <td><?= htmlspecialchars($data['nama_lengkap']); ?></td>
                        <td><?= htmlspecialchars($data['nisn']); ?></td>
                        <td><strong><?= htmlspecialchars($data['nipd'] ?? 'Belum Digenerate'); ?></strong></td>
                        <td><?= htmlspecialchars($data['jk']); ?></td>
                        <td><?= htmlspecialchars($data['sekolah_asal']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>