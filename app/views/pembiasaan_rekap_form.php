<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1>Rekap Absensi Manual: <strong><?= htmlspecialchars($pembiasaan['nama_kegiatan']) ?></strong></h1>
            </div>
            <div class="col-sm-4 text-end">
                <a href="<?= BASE_URL ?>pembiasaan?id=<?= $pembiasaan['id_pembiasaan'] ?>&tab=jurnal" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <form action="index.php" method="GET" class="form-inline">
                    <input type="hidden" name="mod" value="pembiasaan">
                    <input type="hidden" name="act" value="rekap_form">
                    <input type="hidden" name="id_pembiasaan" value="<?= $id_pem ?>">
                    
                    <label class="mr-2">Bulan:</label>
                    <select name="bulan" class="form-control mr-3" onchange="this.form.submit()">
                        <?php 
                        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        foreach($months as $k=>$v): ?>
                        <option value="<?= $k ?>" <?= $bulan==$k?'selected':'' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label class="mr-2">Tahun:</label>
                    <select name="tahun" class="form-control mr-3" onchange="this.form.submit()">
                        <?php for($y=date('Y'); $y>=2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun==$y?'selected':'' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>
            
            <form action="<?= BASE_URL ?>pembiasaan/rekap_save" method="POST">
                <input type="hidden" name="id_pembiasaan" value="<?= $id_pem ?>">
                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                
                <div class="card-body p-0 table-responsive">
                    <?php
                    // Ekstrak list kelas unik untuk filter
                    $kelas_list = [];
                    if (!empty($anggota)) {
                        foreach ($anggota as $a) {
                            if (!empty($a['nama_kelas']) && !in_array($a['nama_kelas'], $kelas_list)) {
                                $kelas_list[] = $a['nama_kelas'];
                            }
                        }
                        sort($kelas_list);
                    }
                    ?>
                    <?php if (!empty($kelas_list)): ?>
                    <div class="p-3 bg-light border-bottom">
                        <label class="mb-0 mr-2">Filter Kelas:</label>
                        <select id="filter_kelas" class="form-control form-control-sm d-inline-block w-auto" onchange="filterKelasRekap()">
                            <option value="all">Semua Kelas</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <table class="table table-bordered table-striped table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th width="5%" rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle text-left">Nama Siswa</th>
                                <th colspan="4">Jumlah Kehadiran (Bulan Ini)</th>
                                <th width="10%" rowspan="2" class="align-middle">% Kehadiran</th>
                            </tr>
                            <tr>
                                <th width="10%" class="text-success">Hadir</th>
                                <th width="10%" class="text-warning">Sakit</th>
                                <th width="10%" class="text-info">Izin</th>
                                <th width="10%" class="text-danger">Alfa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($anggota)): ?>
                                <tr><td colspan="7" class="p-3">Belum ada anggota terdaftar.</td></tr>
                            <?php else: ?>
                                <?php foreach ($anggota as $i => $a): 
                                    $r = $rekap_data[$a['id_siswa']] ?? ['jml_H'=>0, 'jml_S'=>0, 'jml_I'=>0, 'jml_A'=>0];
                                    $total = $r['jml_H'] + $r['jml_S'] + $r['jml_I'] + $r['jml_A'];
                                    $persen = $total > 0 ? round(($r['jml_H'] / $total) * 100, 1) : 0;
                                ?>
                                <tr class="siswa-row" data-id="<?= $a['id_siswa'] ?>" data-kelas="<?= htmlspecialchars($a['nama_kelas'] ?? '') ?>">
                                    <td class="nomor-urut"><?= $i + 1 ?></td>
                                    <td class="text-left font-weight-bold">
                                        <?= $a['nama_siswa'] ?><br>
                                        <small class="text-muted"><?= $a['nama_kelas'] ?></small>
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="rekap[<?= $a['id_siswa'] ?>][H]" class="form-control text-center input-h" value="<?= $r['jml_H'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="rekap[<?= $a['id_siswa'] ?>][S]" class="form-control text-center input-s" value="<?= $r['jml_S'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="rekap[<?= $a['id_siswa'] ?>][I]" class="form-control text-center input-i" value="<?= $r['jml_I'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="rekap[<?= $a['id_siswa'] ?>][A]" class="form-control text-center input-a" value="<?= $r['jml_A'] ?>">
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        <span class="persentase"><?= $persen ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Simpan Rekap Absensi</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.siswa-row');
    
    rows.forEach(row => {
        const inputs = row.querySelectorAll('input[type="number"]');
        const persenSpan = row.querySelector('.persentase');
        
        inputs.forEach(input => {
            input.addEventListener('input', calculatePercentage);
        });

        function calculatePercentage() {
            let h = parseInt(row.querySelector('.input-h').value) || 0;
            let s = parseInt(row.querySelector('.input-s').value) || 0;
            let i = parseInt(row.querySelector('.input-i').value) || 0;
            let a = parseInt(row.querySelector('.input-a').value) || 0;
            
            let total = h + s + i + a;
            let persen = 0;
            
            if (total > 0) {
                persen = (h / total) * 100;
            }
            
            // Format to 1 decimal place if needed, or integer
            // e.g. 95.5% or 100%
            persenSpan.textContent = parseFloat(persen.toFixed(1)) + '%';
            
            // Logic Warna & Predikat
            // 90%+ (warna hijau) A
            // 80%+ (warna hijau) B
            // 70%+ (kuning warning) C
            // dibawah 70% merah D
            
            persenSpan.className = 'persentase font-weight-bold'; // Reset base class
            
            if (persen >= 90) {
                persenSpan.classList.add('text-success'); // A
            } else if (persen >= 80) {
                persenSpan.classList.add('text-success'); // B (User requested green for >80 too)
            } else if (persen >= 70) {
                persenSpan.classList.add('text-warning'); // C
            } else {
                persenSpan.classList.add('text-danger'); // D
            }
        }
    });
});

function filterKelasRekap() {
    var selected = document.getElementById('filter_kelas').value;
    var rows = document.querySelectorAll('.siswa-row');
    var visibleCount = 0;
    
    rows.forEach(function(row) {
        if (selected === 'all' || row.getAttribute('data-kelas') === selected) {
            row.style.display = '';
            visibleCount++;
            // Update nomor urut agar tetap rapi saat di-filter
            row.querySelector('.nomor-urut').textContent = visibleCount;
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
