<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <h1>Input Nilai Sumatif: <?= htmlspecialchars($agenda['nama_penilaian']) ?></h1>
    <p>Kelas: <?=$agenda['nama_kelas'] ?> | Mapel: <?= $agenda['nama_mapel'] ?></p>
  </div>
</section>
<section class="content">
<div class="container-fluid">
    <?php // Session messages now handled by toast notifications in footer.php ?>
<form action="index.php?mod=penilaian_sumatif&act=save_nilai" method="POST">
    <input type="hidden" name="id_sumatif" value="<?= $agenda['id_sumatif'] ?>">
    <input type="hidden" name="id_guru_mapel" value="<?= $agenda['id_guru_mapel'] ?>"> <div class="card card-outline card-secondary">
        <div class="card-header"><h3 class="card-title">Tujuan Pembelajaran (TP) yang Dinilai</h3></div>
        <div class="card-body row">
            <?php if (empty($tp_list)): ?>
                <p class="text-danger col-12">Belum ada TP yang dipilih untuk agenda penilaian ini.</p>
            <?php else: ?>
                <?php foreach($tp_list as $tp): ?>
                    <div class="col-md-6 form-check">
                        <input class="form-check-input" type="checkbox" name="selected_tps[]" value="<?= $tp['id_tp'] ?>" id="tp_display_<?= $tp['id_tp'] ?>" 
                               <?= in_array($tp['id_tp'], $selected_tps_ids) ? 'checked' : '' ?> >
                        <label class="form-check-label" for="tp_display_<?= $tp['id_tp'] ?>">
                            <strong><?= htmlspecialchars($tp['kode_tp']) ?>:</strong> <?= htmlspecialchars($tp['deskripsi_tp']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Input Skor Akhir Siswa</h3></div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center">
                    <tr><th>No</th><th>Nama Siswa</th><th>Nilai (0-100)</th><th>Deskripsi Capaian (Otomatis)</th></tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($siswa_nilai as $s): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($s['nama']) ?><br><small class="text-muted">NISN: <?= htmlspecialchars($s['nisn']) ?></small></td>
                        <td><input type="number" name="nilai[<?= $s['id_penempatan'] ?>][nilai]" class="form-control form-control-sm" min="0" max="100" step="0.01" value="<?= $s['nilai'] ?? '' ?>"></td>
                        <td><textarea name="nilai[<?= $s['id_penempatan'] ?>][deskripsi_capaian]" rows="2" class="form-control form-control-sm" readonly><?= htmlspecialchars($s['deskripsi_capaian'] ?? 'Akan digenerate otomatis...') ?></textarea></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Nilai Sumatif</button>
            <a href="index.php?mod=penilaian_sumatif&id_kelas=<?= $agenda['id_kelas'] ?>&id_guru_mapel=<?= $agenda['id_guru_mapel'] ?>" class="btn btn-secondary">Kembali ke Daftar Agenda</a>
        </div>
    </div>
</form>
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>