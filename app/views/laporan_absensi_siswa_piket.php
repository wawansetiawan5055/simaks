<?php include __DIR__.'/partials/header.php'; ?>
<div class="container-fluid">
  <h2 class="mt-4 mb-3" style="font-size: 1.75rem; font-weight: 600;">Laporan Absensi Siswa (Piket)</h2>
  
  <form method="get" class="mb-3">
    <input type="hidden" name="mod" value="laporan">
    <input type="hidden" name="act" value="absensi_siswa_piket">
    
    <div class="filter-box">
      <div class="row align-items-end">
        <!-- Jenis Laporan -->
        <div class="col-md-2 form-group">
            <label>Jenis Laporan</label>
            <select name="jenis_laporan" class="form-control" onchange="toggleTimeInputs()" id="jenis_laporan_select">
                <option value="harian" <?= ($jenis_laporan == 'harian') ? 'selected' : '' ?>>Harian</option>
                <option value="bulanan" <?= ($jenis_laporan == 'bulanan') ? 'selected' : '' ?>>Rekap Bulanan</option>
                <option value="semester" <?= ($jenis_laporan == 'semester') ? 'selected' : '' ?>>Rekap Semester</option>
            </select>
        </div>

        <!-- Pilih Kelas -->
        <div class="col-md-2 form-group">
            <label>Pilih Kelas</label>
            <select name="kelas" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelas_list as $k): ?>
                <option value="<?= $k['id_kelas'] ?>" <?= ($kelas_id == $k['id_kelas'])?'selected':''; ?>>
                  <?= $k['nama_kelas'] ?>
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
                <?php
                $months = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                foreach ($months as $k => $v) {
                    $sel = ($k == $bulan_filter) ? 'selected' : '';
                    echo "<option value='$k' $sel>$v</option>";
                }
                ?>
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
            <input type="number" name="tahun_semester" value="<?= $tahun_semester ?>" class="form-control">
        </div>

        <div class="col-md-2 form-group">
             <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan</button>
        </div>
      </div>
    </div>
  </form>

  <div class="mb-3">
    <?php
    $queryParamsArray = array_diff_key($_GET, ['mod' => '', 'act' => '']);
    $query_params = http_build_query($queryParamsArray);
    ?>
    <a href="index.php?mod=laporan&act=absensi_siswa_piket_export_excel&<?= $query_params ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
    <a href="index.php?mod=laporan&act=absensi_siswa_piket_export_pdf&<?= $query_params ?>" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <button type="button" onclick="showReportPreview('index.php?mod=laporan&act=absensi_siswa_piket_print&<?= $query_params ?>', 'Laporan Absensi Siswa (Piket)')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
  </div>

  <?php if (empty($data)): ?>
      <div class="alert alert-warning">Tidak ada data untuk ditampilkan.</div>
  <?php else: ?>

    <!-- A. HARIAN -->
    <?php if ($jenis_laporan == 'harian'): ?>
      <table class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th><th>Nama</th><th>NISN</th><th>NIPD</th><th>Tanggal</th><th>Status</th><th>Keterangan</th>
        </tr>
        </thead>
        <tbody>
        <?php $no=1; foreach ($data as $d): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td class="text-left"><?= $d['nama'] ?></td>
          <td><?= $d['nisn'] ?></td>
          <td><?= $d['nipd'] ?></td>
          <td><?= $d['tanggal'] ?></td>
          <td><?= $d['status'] ?></td>
          <td><?= $d['keterangan'] ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

    <!-- B. BULANAN -->
    <?php elseif ($jenis_laporan == 'bulanan'): ?>
        <div class="table-responsive">
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
                    if ($val == 'H') $bg = 'bg-success';
                    if ($val == 'S') $bg = 'bg-warning';
                    if ($val == 'I') $bg = 'bg-info';
                    if ($val == 'A') $bg = 'bg-danger';
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
        </div>

    <!-- C. SEMESTER -->
    <?php elseif ($jenis_laporan == 'semester'): ?>
        <div class="table-responsive">
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
        </div>
    <?php endif; ?>

  <?php endif; ?>
</div>

<script>
function toggleTimeInputs() {
    var jenis = document.getElementById('jenis_laporan_select').value;
    
    // Hide all first
    document.querySelectorAll('.input-harian, .input-bulanan, .input-semester').forEach(function(el) {
        el.style.display = 'none';
    });
    
    // Show selected
    if(jenis === 'harian') {
        document.querySelectorAll('.input-harian').forEach(el=>el.style.display='block');
    } else if(jenis === 'bulanan') {
        document.querySelectorAll('.input-bulanan').forEach(el=>el.style.display='block');
    } else if(jenis === 'semester') {
        document.querySelectorAll('.input-semester').forEach(el=>el.style.display='block');
    }
}
// Run on load
toggleTimeInputs();
</script>
<?php include __DIR__.'/partials/footer.php'; ?>