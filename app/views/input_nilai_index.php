<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-edit mr-2"></i> Input Nilai Siswa per Tujuan Pembelajaran (TP)</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Alerts handled by toast -->

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Pilih Kelas, Mapel, CP, dan TP</h3>
            </div>
            <form method="GET" id="filterForm">
                <input type="hidden" name="mod" value="input_nilai">
                <div class="card-body row">
                    <div class="form-group col-md-3">
                        <label>Kelas</label>
                        <select name="id_kelas" id="id_kelas" class="form-control" onchange="submitFilter()">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_diajar as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= $k['nama_kelas'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($id_kelas_filter && !empty($mapel_diajar)): ?>
                        <div class="form-group col-md-3">
                            <label>Mata Pelajaran</label>
                            <select name="id_guru_mapel" id="id_guru_mapel" class="form-control" onchange="submitFilter()">
                                <option value="">-- Pilih Mapel --</option>
                                <?php foreach ($mapel_diajar as $m): ?>
                                    <option value="<?= $m['id_guru_mapel'] ?>" <?= ($id_guru_mapel_filter == $m['id_guru_mapel']) ? 'selected' : '' ?>>
                                        <?= $m['nama_mapel'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($id_guru_mapel_filter && !empty($cp_list)): ?>
                        <div class="form-group col-md-3">
                            <label>Capaian Pembelajaran (CP)</label>
                            <select name="id_cp" id="id_cp" class="form-control" onchange="submitFilter()">
                                <option value="">-- Pilih CP --</option>
                                <?php foreach ($cp_list as $cp): ?>
                                    <option value="<?= $cp['id_cp'] ?>" <?= ($id_cp_filter == $cp['id_cp']) ? 'selected' : '' ?>>
                                        <?= substr(strip_tags($cp['deskripsi_cp']), 0, 50) ?>...
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($id_cp_filter && !empty($tp_list)): ?>
                        <div class="form-group col-md-3">
                            <label>Tujuan Pembelajaran (TP)</label>
                            <select name="id_tp" id="id_tp" class="form-control" onchange="submitFilter()">
                                <option value="">-- Pilih TP --</option>
                                <?php foreach ($tp_list as $tp): ?>
                                    <option value="<?= $tp['id_tp'] ?>" <?= ($id_tp_filter == $tp['id_tp']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tp['kode_tp']) ?> -
                                        <?= substr(strip_tags($tp['deskripsi_tp']), 0, 40) ?>...
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($id_kelas_filter && $id_guru_mapel_filter && $id_cp_filter && $id_tp_filter): ?>
            <?php if (!empty($siswa_nilai)): ?>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Input Nilai Siswa untuk TP Terpilih
                            (<?= htmlspecialchars($nama_mapel_terpilih) ?>)</h3>
                    </div>
                    <form action="index.php?mod=input_nilai&act=save" method="POST">
                        <input type="hidden" name="id_kelas" value="<?= $id_kelas_filter ?>">
                        <input type="hidden" name="id_guru_mapel" value="<?= $id_guru_mapel_filter ?>">
                        <input type="hidden" name="id_cp" value="<?= $id_cp_filter ?>">
                        <input type="hidden" name="id_tp" value="<?= $id_tp_filter ?>">
                        <div class="card-body">
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-secondary" disabled><i class="fa fa-upload"></i>
                                    Impor Nilai (Segera Hadir)</button>
                            </div>
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Siswa</th>
                                        <th width="15%">Nilai (0-100)</th>
                                        <th>Keterangan (Opsional)</th>
                                        <th>Deskripsi Otomatis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($siswa_nilai as $s): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <?= htmlspecialchars($s['nama']) ?><br>
                                                <small class="text-muted">NISN: <?= htmlspecialchars($s['nisn']) ?></small>
                                            </td>
                                            <td><input type="number" name="nilai[<?= $s['id_penempatan'] ?>][nilai]"
                                                    class="form-control form-control-sm" min="0" max="100" step="0.01"
                                                    value="<?= $s['nilai'] ?? '' ?>"></td>
                                            <td><input type="text" name="nilai[<?= $s['id_penempatan'] ?>][keterangan]"
                                                    class="form-control form-control-sm"
                                                    value="<?= htmlspecialchars($s['keterangan'] ?? '') ?>"></td>
                                            <td><small
                                                    class="text-muted"><?= htmlspecialchars($s['deskripsi'] ?? 'Belum Dinilai') ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Tidak ada siswa yang ditempatkan di kelas ini pada Tahun Ajaran aktif atau
                    filter belum lengkap.</div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</section>

<script>
    // Fungsi untuk submit form filter saat dropdown berubah
    function submitFilter() {
        document.getElementById('filterForm').submit();
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>