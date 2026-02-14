<?php include __DIR__.'/partials/header.php'; ?>
<div class="container-fluid">
  <h2>Laporan Penilaian Sumatif</h2>
  <h3 class="text-muted mb-2 small">TA: <?= htmlspecialchars($_SESSION['nama_ta_viewing'] ?? $_SESSION['nama_ta_aktif'] ?? 'Belum Dipilih') ?></h3>
  <form method="get" class="mb-2 row">
    <input type="hidden" name="mod" value="laporan">
    <input type="hidden" name="act" value="penilaian_sumatif">
    <div class="col-md-3">
      <select name="kelas" class="form-control" required>
        <option value="">Pilih Kelas</option>
        <?php foreach($kelas_list as $k): ?>
        <option value="<?= $k['id_kelas'] ?>" <?= isset($_GET['kelas'])&&$_GET['kelas']==$k['id_kelas']?'selected':''; ?>>
          <?= $k['nama_kelas'] ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="mapel" class="form-control" required>
        <option value="">Pilih Mata Pelajaran</option>
        <?php foreach($mapel_list as $m): ?>
        <option value="<?= $m['id_mapel'] ?>" <?= isset($_GET['mapel'])&&$_GET['mapel']==$m['id_mapel']?'selected':''; ?>>
          <?= $m['nama_mapel'] ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="jenis" class="form-control" required>
        <option value="">Pilih Jenis Sumatif</option>
        <?php
        $jenis_sumatif_options = [
            'Sumatif Lingkup Materi',
            'Sumatif Tengah Semester',
            'Sumatif Akhir Semester',
            'Sumatif Akhir Tahun',
            'Sumatif Akhir Jenjang'
        ];
        foreach($jenis_sumatif_options as $j):
        ?>
        <option value="<?= $j ?>" <?= isset($_GET['jenis'])&&$_GET['jenis']==$j?'selected':''; ?>>
          <?= $j ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><button class="btn btn-info">Tampilkan</button></div>
  </form>
  <div class="mb-2">
    <a href="index.php?mod=laporan&act=penilaian_sumatif_export_excel&<?= http_build_query($_GET) ?>" class="btn btn-success btn-sm">Export Excel</a>
    <a href="index.php?mod=laporan&act=penilaian_sumatif_export_pdf&<?= http_build_query($_GET) ?>" class="btn btn-danger btn-sm">Export PDF</a>
    <button type="button" onclick="showReportPreview('index.php?mod=laporan&amp;act=penilaian_sumatif_print&<?= http_build_query(array_diff_key($_GET, ['mod' => '', 'act' => ''])) ?>', 'Laporan Penilaian Sumatif')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
  </div>
  <table class="table table-bordered table-striped">
    <tr>
      <th>No</th><th>Nama Siswa</th><th>NISN</th><th>Kelas</th><th>Mata Pelajaran</th><th>Nama Penilaian</th><th>Jenis Sumatif</th><th>Nilai</th><th>Deskripsi Capaian</th>
    </tr>
    <?php $no=1; foreach ($list as $d): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $d['nama_siswa'] ?></td>
      <td><?= $d['nisn'] ?></td>
      <td><?= $d['nama_kelas'] ?></td>
      <td><?= $d['nama_mapel'] ?></td>
      <td><?= $d['nama_penilaian'] ?></td>
      <td><?= $d['jenis_sumatif'] ?></td>
      <td><?= $d['nilai'] ?></td>
      <td><?= $d['deskripsi_capaian'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>