<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="fas fa-file-alt mr-2"></i> Laporan Data Siswa</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Laporan Siswa</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <form method="get" class="mb-3">
      <input type="hidden" name="mod" value="laporan">
      <input type="hidden" name="act" value="siswa">

      <div class="filter-box">
        <div class="row align-items-end">
          <div class="col-md-4 form-group">
            <label for="kelasFilter">Pilih Kelas</label>
            <select name="kelas" id="kelasFilter" class="form-control" onchange="this.form.submit()">
              <option value="">-- Tampilkan Semua Kelas --</option>
              <?php foreach ($kelas_list as $k): ?>
                <option value="<?= $k['id_kelas'] ?>" <?= isset($_GET['kelas']) && $_GET['kelas'] == $k['id_kelas'] ? 'selected' : ''; ?>>
                  <?= $k['nama_kelas'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-8 form-group text-right">
            <?php
            $query_params = $_GET;
            unset($query_params['mod']);
            unset($query_params['act']);
            $new_query_string = http_build_query($query_params);
            ?>
            <div class="btn-group">
              <a href="index.php?mod=laporan&act=siswa_export_excel&<?= $new_query_string ?>" class="btn btn-success">
                <i class="fas fa-file-excel mr-1"></i> Excel
              </a>
              <a href="index.php?mod=laporan&act=siswa_export_pdf&<?= $new_query_string ?>" class="btn btn-danger"
                target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> PDF
              </a>
              <button type="button"
                onclick="showReportPreview('index.php?mod=laporan&amp;act=siswa_print&<?= $new_query_string ?>', 'Laporan Data Siswa')"
                class="btn btn-info btn-sm">
                <i class="fas fa-print mr-1"></i> Cetak
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>

    <div class="card">
      <div class="card-header border-0">
        <h3 class="card-title">Data Siswa</h3>
        <div class="card-tools">
          <span class="badge badge-info"><?= count($siswa_list) ?> Siswa</span>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
          <thead class="bg-light">
            <tr>
              <th width="5%">No</th>
              <th>Nama Lengkap</th>
              <th>NISN</th>
              <th>NIPD</th>
              <th>L/P</th>
              <th>Kelas</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($siswa_list)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i class="fas fa-user-slash fa-3x mb-3"></i><br>
                  Tidak ada data siswa ditemukan untuk filter ini.
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1;
              foreach ($siswa_list as $s): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="font-weight-bold"><?= htmlspecialchars($s['nama']) ?></td>
                  <td><?= htmlspecialchars($s['nisn']) ?></td>
                  <td><?= htmlspecialchars($s['nipd']) ?></td>
                  <td><?= htmlspecialchars($s['jk']) ?></td>
                  <td>
                    <span class="badge badge-light border">
                      <?= htmlspecialchars($s['nama_kelas']) ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($s['status_aktif'] == 'Aktif'): ?>
                      <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge badge-secondary"><?= $s['status_aktif'] ?></span>
                    <?php endif; ?>
                  </td>
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