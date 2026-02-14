<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Laporan Jurnal/Agenda KBM</h1>
      </div>
    </div>
  </div>
</section>

<section class="content">
<div class="container-fluid">
    
    <form method="GET">
        <input type="hidden" name="mod" value="laporan">
        <input type="hidden" name="act" value="jurnal">
        
        <div class="filter-box">
            <div class="row align-items-end">
                <div class="col-md-2 form-group">
                    <label>Jenis Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="form-control" onchange="toggleFilterInputs()">
                        <option value="bulanan" <?= ($_GET['jenis_laporan'] ?? 'bulanan') == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                        <option value="semester" <?= ($_GET['jenis_laporan'] ?? '') == 'semester' ? 'selected' : '' ?>>Semester</option>
                    </select>
                </div>

                <!-- Filter Bulanan: Pilih Bulan & Tahun -->
                <div class="col-md-2 form-group filter-bulanan" style="display:none;">
                    <label>Pilih Bulan</label>
                    <select name="bulan" class="form-control">
                        <?php
                        $months = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        $curr_bulan = $_GET['bulan'] ?? date('m');
                        foreach ($months as $m_val => $m_name): ?>
                            <option value="<?= $m_val ?>" <?= ($curr_bulan == $m_val) ? 'selected' : '' ?>><?= $m_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Semester: Rentang Bulan -->
                <div class="col-md-2 form-group filter-semester" style="display:none;">
                    <label>Bulan Mulai</label>
                    <select name="bulan_awal" class="form-control">
                        <?php foreach ($months as $m_val => $m_name): ?>
                            <option value="<?= $m_val ?>" <?= (($_GET['bulan_awal'] ?? '07') == $m_val) ? 'selected' : '' ?>><?= $m_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 form-group filter-semester" style="display:none;">
                    <label>Bulan Selesai</label>
                    <select name="bulan_akhir" class="form-control">
                        <?php foreach ($months as $m_val => $m_name): ?>
                            <option value="<?= $m_val ?>" <?= (($_GET['bulan_akhir'] ?? '12') == $m_val) ? 'selected' : '' ?>><?= $m_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tahun untuk Bulanan & Semester -->
                <div class="col-md-2 form-group filter-tahun" style="display:none;">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="<?= $_GET['tahun'] ?? date('Y') ?>">
                </div>

                <div class="col-md-2 form-group">
                    <label>Pilih Kelas</label>
                    <select name="kelas" class="form-control">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 form-group">
                    <label>Pilih Guru</label>
                    <select name="guru" class="form-control">
                        <option value="">-- Semua Guru --</option>
                        <?php foreach($guru_list as $g): ?>
                            <option value="<?= $g['id_guru'] ?>" <?= ($guru == $g['id_guru']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 form-group">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Tombol Export & Cetak (Selalu Tampil) -->
    <div class="mb-3">
        <?php
        // Prepare current filter params for export
        $base_params = [
            'mod' => 'laporan',
            'jenis_laporan' => $_GET['jenis_laporan'] ?? 'bulanan',
            'kelas' => $kelas,
            'guru' => $guru,
            'tanggal1' => $tanggal1,
            'tanggal2' => $tanggal2,
            'bulan' => $_GET['bulan'] ?? '',
            'bulan_awal' => $_GET['bulan_awal'] ?? '',
            'bulan_akhir' => $_GET['bulan_akhir'] ?? '',
            'tahun' => $_GET['tahun'] ?? ''
        ];
        
        $q_excel = http_build_query(array_merge($base_params, ['act' => 'jurnal_export_excel']));
        $q_pdf   = http_build_query(array_merge($base_params, ['act' => 'jurnal_export_pdf']));
        $q_print = http_build_query(array_merge($base_params, ['act' => 'jurnal_print']));
        ?>
        <a href="index.php?<?= $q_excel ?>" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="index.php?<?= $q_pdf ?>" class="btn btn-danger btn-sm" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <button type="button" onclick="showReportPreview('index.php?<?= $q_print ?>', 'Laporan Jurnal Mengajar')" class="btn btn-info btn-sm">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>

    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">Hasil Laporan</h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <?php if (!empty($list)): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 10%">Tanggal</th>
                        <th style="width: 10%">Jam / Waktu</th>
                        <th style="width: 10%">Kelas</th>
                        <th style="width: 15%">Guru</th>                      
                        <th style="width: 20%">Capaian/Tujuan Pembelajaran</th>
                        <th style="width: 15%">Tagihan/Tugas</th>
                        <th style="width: 15%">Rekap Absensi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($list as $l): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        
                        <td><?= date('d-m-Y', strtotime($l['tanggal'])) ?></td>
                        
                        <td class="text-center" style="white-space: nowrap;">
                            <?= htmlspecialchars($l['jam_ke']) ?>
                        </td>
                        
                        <td><?= htmlspecialchars($l['nama_kelas'] ?? '-') ?></td>
                        
                        <td><?= htmlspecialchars($l['guru']) ?></td>
                        
                        <td><?= htmlspecialchars($l['tujuan_pembelajaran']) ?></td>
                        
                        <td><?= htmlspecialchars($l['tagihan']) ?></td>
                        
                        <td>
                            <?= htmlspecialchars($l['catatan_absensi']) ?>
                            <?php if(!empty($l['keterangan'])): ?>
                                <br><small class="text-muted">Ket: <?= htmlspecialchars($l['keterangan']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="alert alert-warning m-3">
                <?php if (isset($_GET['tanggal1'])): ?>
                    Data tidak ditemukan untuk filter yang dipilih.
                <?php else: ?>
                    Silakan isi filter dan klik "Tampilkan Data" untuk melihat laporan.
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>

<script>
function toggleFilterInputs() {
    const jenis = document.getElementById('jenis_laporan').value;
    
    // Hide all
    document.querySelectorAll('.filter-harian, .filter-bulanan, .filter-semester, .filter-tahun').forEach(el => {
        el.style.display = 'none';
        // Remove required from hidden inputs if any (optional)
    });

    if (jenis === 'bulanan') {
        document.querySelectorAll('.filter-bulanan, .filter-tahun').forEach(el => el.style.display = 'block');
    } else if (jenis === 'semester') {
        document.querySelectorAll('.filter-semester, .filter-tahun').forEach(el => el.style.display = 'block');
    }
}

// Initial call
document.addEventListener('DOMContentLoaded', toggleFilterInputs);
</script>
```