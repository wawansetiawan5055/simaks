<?php 
// Logic Pre-render untuk Grid View dihapus agar tidak me-replace halaman utama.
// Grid View hanya akan muncul saat menekan tombol Cetak (Popup).

include __DIR__.'/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Laporan Jadwal Pelajaran
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Laporan Jadwal</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
<div class="container-fluid">
    
    <form method="GET">
        <input type="hidden" name="mod" value="laporan">
        <input type="hidden" name="act" value="jadwal_pelajaran">
        
        <div class="filter-box">
            <div class="row align-items-end">
                <div class="col-md-3 form-group">
                    <label>Filter Berdasarkan</label>
                    <select name="filter_type" id="filter_type" class="form-control" required>
                        <option value="keseluruhan" <?= ($filter_type == 'keseluruhan') ? 'selected' : '' ?>>Keseluruhan</option>
                        <option value="per_kelas" <?= ($filter_type == 'per_kelas') ? 'selected' : '' ?>>Per Kelas</option>
                        <option value="per_guru" <?= ($filter_type == 'per_guru') ? 'selected' : '' ?>>Per Guru</option>
                    </select>
                </div>
                
                <div class="col-md-3 form-group" id="container_filter_kelas">
                    <label>Pilih Kelas</label>
                    <select name="kelas" class="form-control">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 form-group" id="container_filter_guru" style="display: none;">
                    <label>Pilih Guru</label>
                    <select name="guru" class="form-control">
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($guru_list as $g): ?>
                            <option value="<?= $g['id_guru'] ?>" <?= ($guru == $g['id_guru']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nama']) ?> (<?= htmlspecialchars($g['kode_guru'] ?? $g['id_guru']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Hasil Laporan Jadwal (<?= htmlspecialchars($judul_laporan) ?>)</h3>
            <div class="card-tools">
                <a href="<?= BASE_URL ?>laporan/jadwal_pelajaran_export_excel?filter_type=<?= $filter_type ?>&kelas=<?= $kelas ?>&guru=<?= $guru ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="<?= BASE_URL ?>laporan/jadwal_pelajaran_export_pdf?filter_type=<?= $filter_type ?>&kelas=<?= $kelas ?>&guru=<?= $guru ?>" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <button type="button" onclick="showReportPreview('<?= BASE_URL ?>laporan/jadwal_pelajaran_print?filter_type=<?= $filter_type ?>&kelas=<?= $kelas ?>&guru=<?= $guru ?>', 'Laporan Jadwal Pelajaran')" class="btn btn-info btn-sm"><i class="fas fa-print"></i> Cetak</button>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <?php if ($filter_type == 'keseluruhan'): ?>
                <div class="alert alert-info m-3 text-center">
                    <h4><i class="fas fa-info-circle"></i> Mode Keseluruhan (Grid)</h4>
                    <p>Tampilan Jadwal Grid sangat lebar dan kompleks.</p>
                    <p>Silakan klik tombol <b><i class="fas fa-print"></i> Cetak</b> di pojok kanan atas untuk melihat pratinjau (Popup).</p>
                </div>
            <?php elseif (!empty($list)): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Hari</th>
                        <th>Jam Ke</th>
                        <th>Waktu</th>
                        <?php if ($filter_type != 'per_guru'): ?>
                            <th>Guru Pengajar</th>
                        <?php endif; ?>
                        <?php if ($filter_type != 'per_kelas'): ?>
                            <th>Kelas</th>
                        <?php endif; ?>
                        <th>Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($list as $l): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($l['hari_kbm']) ?></td>
                        <td><?= htmlspecialchars($l['jam_ke']) ?></td>
                        <td><?= htmlspecialchars(substr($l['jam_mulai'], 0, 5)) ?>-<?= htmlspecialchars(substr($l['jam_selesai'], 0, 5)) ?></td>
                        <?php if ($filter_type != 'per_guru'): ?>
                            <td><?= htmlspecialchars($l['nama_guru']) ?></td>
                        <?php endif; ?>
                        <?php if ($filter_type != 'per_kelas'): ?>
                            <td><?= htmlspecialchars($l['nama_kelas']) ?></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($l['nama_mapel']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-warning m-3">
                <?php if (isset($_GET['filter_type'])): ?>
                    Data tidak ditemukan untuk filter yang dipilih.
                <?php else: ?>
                    Silakan pilih filter dan klik "Tampilkan Data" untuk melihat jadwal.
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterType = document.getElementById('filter_type');
    const containerKelas = document.getElementById('container_filter_kelas');
    const containerGuru = document.getElementById('container_filter_guru');

    function toggleFilterInputs() {
        if (filterType.value === 'per_kelas') {
            containerKelas.style.display = 'block';
            containerGuru.style.display = 'none';
        } else if (filterType.value === 'per_guru') {
            containerKelas.style.display = 'none';
            containerGuru.style.display = 'block';
        } else { // Keseluruhan
            containerKelas.style.display = 'none';
            containerGuru.style.display = 'none';
        }
    }
    // Jalankan fungsi saat dropdown filter utama diubah
    filterType.addEventListener('change', toggleFilterInputs);
    // Jalankan fungsi saat halaman dimuat (untuk mencocokkan state)
    toggleFilterInputs(); 
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>