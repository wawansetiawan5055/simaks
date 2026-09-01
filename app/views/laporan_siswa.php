<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Data Peserta Didik
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Siswa</li>
        </ol>
      </div>
    </div>
  </div>
</div>

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
              <a href="<?= BASE_URL ?>laporan/siswa_export_excel?<?= $new_query_string ?>" class="btn btn-success">
                <i class="fas fa-file-excel mr-1"></i> Excel
              </a>
              <a href="<?= BASE_URL ?>laporan/siswa_export_pdf?<?= $new_query_string ?>" class="btn btn-danger"
                target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> PDF
              </a>
              <button type="button"
                onclick="showReportPreview('<?= BASE_URL ?>laporan/siswa_print?<?= $new_query_string ?>', 'Laporan Data Siswa')"
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