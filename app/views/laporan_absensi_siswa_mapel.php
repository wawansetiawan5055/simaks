<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-user-check"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Presensi Siswa Mapel
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Presensi</li>
        </ol>
      </div>
    </div>
  </div>
</div>

  <!-- Form Filter -->
  <form method="get" action="<?= BASE_URL ?>laporan/absensi_siswa_mapel" id="filterForm" class="mb-3">

    <div class="filter-box">
      <div class="row align-items-end">
        <!-- Jenis Laporan -->
        <div class="col-md-2 form-group">
          <label>Jenis Laporan</label>
          <select name="jenis_laporan" id="jenis_laporan" class="form-control" onchange="toggleTimeInputs()">
            <option value="harian" <?= $jenis_laporan == 'harian' ? 'selected' : '' ?>>Laporan Harian</option>
            <option value="bulanan" <?= $jenis_laporan == 'bulanan' ? 'selected' : '' ?>>Rekap Bulanan</option>
            <option value="semester" <?= $jenis_laporan == 'semester' ? 'selected' : '' ?>>Rekap Semester</option>
          </select>
        </div>

        <!-- Filter Kelas -->
        <div class="col-md-2 form-group">
          <label>Pilih Kelas</label>
          <select name="kelas" class="form-control" required>
            <option value="">-- Semua Kelas --</option>
            <?php foreach ($kelas_list as $k): ?>
              <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : ''; ?>>
                <?= $k['nama_kelas'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Filter Guru (Admin Only) -->
        <?php if (has_role(['Admin', 'TU', 'Kepala Sekolah'])): ?>
          <div class="col-md-2 form-group">
            <label>Pilih Guru</label>
            <select name="guru" class="form-control">
              <option value="">-- Semua Guru --</option>
              <?php foreach ($guru_list as $g): ?>
                <option value="<?= $g['id_guru'] ?>" <?= ($id_guru_filter == $g['id_guru']) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($g['nama']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

        <!-- Filter Mapel -->
        <div class="col-md-2 form-group">
          <label>Pilih Mapel</label>
          <select name="mapel" class="form-control">
            <option value="">-- Semua Mapel --</option>
            <?php foreach ($mapel_list as $m): ?>
              <option value="<?= $m['id_mapel'] ?>" <?= ($id_mapel_filter == $m['id_mapel']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($m['nama_mapel']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Input Waktu: Harian -->
        <div class="col-md-2 form-group input-harian">
          <label>Tanggal</label>
          <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" class="form-control">
        </div>

        <!-- Input Waktu: Bulanan -->
        <div class="col-md-2 form-group input-bulanan" style="display:none;">
          <label>Bulan</label>
          <select name="bulan" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan_filter == $m ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-1 form-group input-bulanan" style="display:none;">
          <label>Tahun</label>
          <input type="number" name="tahun" value="<?= $tahun_filter ?>" class="form-control">
        </div>

        <!-- Input Waktu: Semester -->
        <div class="col-md-2 form-group input-semester" style="display:none;">
          <label>Dari Bulan</label>
          <select name="bulan1" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= ($bulan1 ?? '07') == $m ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-2 form-group input-semester" style="display:none;">
          <label>Sampai Bulan</label>
          <select name="bulan2" class="form-control">
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= ($bulan2 ?? '12') == $m ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-1 form-group input-semester" style="display:none;">
          <label>Tahun</label>
          <input type="number" name="tahun_semester" value="<?= $tahun_semester ?? date('Y') ?>" class="form-control">
        </div>

        <div class="col-md-2 form-group">
          <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan</button>
        </div>
      </div>
    </div>
  </form>

  <!-- Tombol Export -->
  <div class="mb-3">
    <?php
    // FIX: Filter 'mod' and 'act' out to avoid duplication/override in generated URLs
    $queryParamsArray = array_diff_key($_GET, ['mod' => '', 'act' => '']);
    $query_params = http_build_query($queryParamsArray);
    ?>
    <a href="<?= BASE_URL ?>laporan/absensi_siswa_mapel_export_excel?<?= $query_params ?>" class="btn btn-success btn-sm"><i
        class="fas fa-file-excel"></i> Export Excel</a>
    <a href="<?= BASE_URL ?>laporan/absensi_siswa_mapel_export_pdf?<?= $query_params ?>" class="btn btn-danger btn-sm"><i
        class="fas fa-file-pdf"></i> Export PDF</a>
    <button type="button"
      onclick="showReportPreview('<?= BASE_URL ?>laporan/absensi_siswa_mapel_print?<?= $query_params ?>', 'Laporan Absensi')"
      class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
  </div>

  <!-- REPORT CONTENT -->
  <?php if (empty($data)): ?>
    <div class="alert alert-info">Silakan pilih filter untuk menampilkan data.</div>
  <?php else: ?>

    <!-- Custom Header Info (As Requested) -->
    <div class="card p-3 mb-3">
      <h4 class="text-center font-weight-bold mb-4">LAPORAN ABSENSI SISWA</h4>
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless table-sm">
            <tr>
              <td width="150">Nama Guru</td>
              <td>: <strong><?= htmlspecialchars($header_info['guru'] ?? '-') ?></strong></td>
            </tr>
            <tr>
              <td>Nama Mata Pelajaran</td>
              <td>: <strong><?= htmlspecialchars($header_info['mapel'] ?? '-') ?></strong></td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-borderless table-sm">
            <tr>
              <td width="150">Kelas / Fase</td>
              <td>: <strong><?= htmlspecialchars($header_info['kelas'] ?? '-') ?></strong> | -</td>
            </tr>
            <tr>
              <td>Tahun Pelajaran</td>
              <td>: <strong><?= htmlspecialchars($header_info['ta']) ?></strong></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="card-body table-responsive p-0">

        <!-- A. HARIAN -->
        <?php if ($jenis_laporan == 'harian'): ?>
          <table class="table table-bordered table-head-fixed text-nowrap">
            <thead>
              <tr>
                <th style="width: 10px">No</th>
                <th>Tanggal</th>
                <th>NIPD</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              // Logic untuk rowspan tanggal
              // Kita perlu prepare data dulu agar tahu rowspan
              $grouped_dates = [];
              foreach ($data as $d) {
                $grouped_dates[$d['tanggal']][] = $d;
              }

              foreach ($grouped_dates as $tgl => $items):
                $first = true;
                foreach ($items as $d):
                  ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <?php if ($first): ?>
                      <td rowspan="<?= count($items) ?>" class="align-middle bg-light font-weight-bold">
                        <?= date('d-m-Y', strtotime($tgl)) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($d['nipd']) ?></td>
                    <td><?= htmlspecialchars($d['nisn']) ?></td>
                    <td><?= htmlspecialchars($d['nama']) ?></td>
                    <td>
                      <?php
                      $badge = 'secondary';
                      if ($d['status'] == 'Hadir')
                        $badge = 'success';
                      elseif ($d['status'] == 'Sakit')
                        $badge = 'warning';
                      elseif ($d['status'] == 'Ijin')
                        $badge = 'info';
                      elseif ($d['status'] == 'Alpa')
                        $badge = 'danger';
                      ?>
                      <span class="badge badge-<?= $badge ?>"><?= $d['status'] ?></span>
                    </td>
                  </tr>
                  <?php
                  $first = false;
                endforeach;
              endforeach;
              ?>
            </tbody>
          </table>

          <!-- B. BULANAN -->
        <?php elseif ($jenis_laporan == 'bulanan'): ?>
          <table class="table table-bordered table-sm text-center">
            <thead>
              <tr>
                <th rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle">NIPD</th>
                <th rowspan="2" class="align-middle">NISN</th>
                <th rowspan="2" class="align-middle text-left">Nama Siswa</th>
                <th colspan="<?= count($data['dates']) ?>">Tanggal</th>
                <th colspan="4">Rekap</th>
                <th rowspan="2" class="align-middle">%</th>
              </tr>
              <tr>
                <?php foreach ($data['dates'] as $dt): ?>
                  <th style="font-size: 10px; width: 25px;"><?= date('d', strtotime($dt)) ?></th>
                <?php endforeach; ?>
                <th>H</th>
                <th>S</th>
                <th>I</th>
                <th>A</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              foreach ($data['students'] as $s): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= $s['nipd'] ?></td>
                  <td><?= $s['nisn'] ?></td>
                  <td class="text-left"><?= $s['nama'] ?></td>

                  <?php foreach ($data['dates'] as $dt):
                    $st = $s['attendance'][$dt] ?? '-';
                    $val = ($st != '-') ? strtoupper(substr($st, 0, 1)) : '';
                    $bg = '';
                    if ($val == 'H')
                      $bg = 'bg-success';
                    if ($val == 'S')
                      $bg = 'bg-warning';
                    if ($val == 'I')
                      $bg = 'bg-info';
                    if ($val == 'A')
                      $bg = 'bg-danger';
                    ?>
                    <td class="<?= $bg ?> p-0" style="vertical-align: middle;"><?= $val ?></td>
                  <?php endforeach; ?>

                  <!-- Totals -->
                  <td><?= $s['summary']['H'] ?></td>
                  <td><?= $s['summary']['S'] ?></td>
                  <td><?= $s['summary']['I'] ?></td>
                  <td><?= $s['summary']['A'] ?></td>
                  <?php 
                    $total_hsia = $s['summary']['H'] + $s['summary']['S'] + $s['summary']['I'] + $s['summary']['A'];
                    $persen = ($total_hsia > 0) ? round(($s['summary']['H'] / $total_hsia) * 100, 1) : 0;
                  ?>
                  <td class="font-weight-bold"><?= $persen ?>%</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- C. SEMESTER -->
        <?php elseif ($jenis_laporan == 'semester'): ?>
          <table class="table table-bordered table-sm text-center">
            <thead>
              <tr>
                <th rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle">NIPD</th>
                <th rowspan="2" class="align-middle">NISN</th>
                <th rowspan="2" class="align-middle text-left">Nama Siswa</th>
                <?php foreach ($data['months'] as $m): ?>
                  <th colspan="4"><?= date('F Y', strtotime($m . "-01")) ?></th>
                <?php endforeach; ?>
                <th colspan="4" class="bg-light">Total Semester</th>
                <th rowspan="2" class="align-middle">%</th>
              </tr>
              <tr>
                <?php foreach ($data['months'] as $m): ?>
                  <th>H</th>
                  <th>S</th>
                  <th>I</th>
                  <th>A</th>
                <?php endforeach; ?>
                <th>H</th>
                <th>S</th>
                <th>I</th>
                <th>A</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              foreach ($data['students'] as $s): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= $s['nipd'] ?></td>
                  <td><?= $s['nisn'] ?></td>
                  <td class="text-left"><?= $s['nama'] ?></td>

                  <?php foreach ($data['months'] as $m):
                    $month_data = $s['months'][$m] ?? ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                    ?>
                    <td><?= $month_data['H'] ?: '-' ?></td>
                    <td><?= $month_data['S'] ?: '-' ?></td>
                    <td><?= $month_data['I'] ?: '-' ?></td>
                    <td><?= $month_data['A'] ?: '-' ?></td>
                  <?php endforeach; ?>

                  <td class="bg-light font-weight-bold"><?= $s['total']['H'] ?></td>
                  <td class="bg-light font-weight-bold"><?= $s['total']['S'] ?></td>
                  <td class="bg-light font-weight-bold"><?= $s['total']['I'] ?></td>
                  <td class="bg-light font-weight-bold"><?= $s['total']['A'] ?></td>
                  <?php 
                    $total_sem = $s['total']['H'] + $s['total']['S'] + $s['total']['I'] + $s['total']['A'];
                    $persen_sem = ($total_sem > 0) ? round(($s['total']['H'] / $total_sem) * 100, 1) : 0;
                  ?>
                  <td class="bg-light font-weight-bold"><?= $persen_sem ?>%</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

      </div>
    </div>
  <?php endif; ?>
</div>

<script>
  function toggleTimeInputs() {
    var type = document.getElementById('jenis_laporan').value;
    var bulananInputs = document.querySelectorAll('.input-bulanan');
    var harianInputs = document.querySelectorAll('.input-harian');
    var semesterInputs = document.querySelectorAll('.input-semester');

    // Reset all
    bulananInputs.forEach(el => el.style.display = 'none');
    harianInputs.forEach(el => el.style.display = 'none');
    semesterInputs.forEach(el => el.style.display = 'none');

    if (type === 'bulanan') {
      bulananInputs.forEach(el => el.style.display = 'block');
    } else if (type === 'semester') {
      semesterInputs.forEach(el => el.style.display = 'block');
    } else {
      harianInputs.forEach(el => el.style.display = 'block');
    }
  }
  // Init on load
  document.addEventListener("DOMContentLoaded", function () {
    toggleTimeInputs();
  });
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>