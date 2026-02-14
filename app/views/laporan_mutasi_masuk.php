<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Laporan Mutasi Masuk</h1>
      </div>
      <div class="col-sm-6">
        <p class="float-sm-right text-muted">
            Menampilkan data untuk TA: <strong><?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'N/A') ?></strong>
        </p>
      </div>
    </div>
  </div>
</section>

<section class="content">
<div class="container-fluid">
    <?php if (!$id_ta_tampil): ?>
        <div class="alert alert-danger">Silakan pilih Tahun Ajaran di navbar untuk menampilkan data.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Siswa Diterima via Mutasi Masuk</h3>
            <div class="card-tools">
                <a href="index.php?mod=laporan&act=mutasi_masuk_export_excel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="index.php?mod=laporan&act=mutasi_masuk_export_pdf" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <button type="button" onclick="showReportPreview('index.php?mod=laporan&amp;act=mutasi_masuk_print', 'Laporan Mutasi Masuk')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">No</th>
                        <th>Tgl Mutasi</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>NIPD (Baru)</th>
                        <th>Sekolah Asal</th>
                        <th>Pindah Ke Tingkat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr><td colspan="7" class="text-center">Belum ada data mutasi masuk yang diterima pada TA ini.</td></tr>
                    <?php endif; ?>
                    
                    <?php $no = 1; foreach ($list as $data): ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= htmlspecialchars($data['tanggal_mutasi']); ?></td>
                        <td><?= htmlspecialchars($data['nama_lengkap']); ?></td>
                        <td><?= htmlspecialchars($data['nisn']); ?></td>
                        <td><strong><?= htmlspecialchars($data['nipd'] ?? 'Belum Digenerate'); ?></strong></td>
                        <td><?= htmlspecialchars($data['sekolah_asal']); ?></td>
                        <td><?= htmlspecialchars($data['pindah_ke_tingkat']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>