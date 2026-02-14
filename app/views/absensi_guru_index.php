<?php 
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__.'/partials/header.php'; 
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6"><h1><i class="fas fa-chalkboard-teacher mr-2"></i> Absensi Guru (Filter per Jadwal KBM)</h1></div>
    </div>
  </div>
</section>
<section class="content">
<div class="container-fluid">
    <?php // Session messages now handled by toast notifications in footer.php ?>
<div class="card card-info">
        <div class="card-header"><h3 class="card-title">Pilih Tanggal</h3></div>
        <form method="GET">
            <input type="hidden" name="mod" value="absensi_guru">
            <div class="card-body row">
                <div class="form-group col-md-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>" onchange="this.form.submit()">
                </div>
                <div class="form-group col-md-9 d-flex align-items-end">
                    <p class="text-muted">Hanya akan menampilkan guru yang memiliki jadwal mengajar pada hari yang dipilih.</p>
                </div>
            </div>
        </form>
    </div>

    <form action="index.php?mod=absensi_guru&act=save" method="POST">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Formulir Absensi Guru - Tanggal: <strong><?= DateHelper::formatTanggal($tanggal, 'long') ?></strong></h3></div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Guru</th>
                            <th>Jadwal Hari Ini</th> <th>Kehadiran</th>
                            <th>Tugas (jika digantikan)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($guru_list)): ?>
                            <tr><td colspan="6" class="text-center font-italic">Tidak ada guru yang memiliki jadwal KBM pada hari ini.</td></tr>
                        <?php endif; ?>
                        
                        <?php $no=1; foreach($guru_list as $g): 
                            $absensi = $absensi_hari_ini[$g['id_guru']] ?? null;
                            $status = $absensi['status'] ?? 'Hadir';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($g['nama']) ?></td>
                            <td><small><?= nl2br(htmlspecialchars($g['jadwal_hari_ini'] ?? '...')) ?></small></td>
                            <td>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success <?= $status == 'Hadir' ? 'active' : '' ?>"><input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Hadir" <?= $status == 'Hadir' ? 'checked' : '' ?>> Hadir</label>
                                    <label class="btn btn-outline-warning <?= $status == 'Sakit' ? 'active' : '' ?>"><input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Sakit" <?= $status == 'Sakit' ? 'checked' : '' ?>> Sakit</label>
                                    <label class="btn btn-outline-info <?= $status == 'Izin' ? 'active' : '' ?>"><input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Izin" <?= $status == 'Izin' ? 'checked' : '' ?>> Izin</label>
                                    <label class="btn btn-outline-danger <?= $status == 'Alpa' ? 'active' : '' ?>"><input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Alpa" <?= $status == 'Alpa' ? 'checked' : '' ?>> Alpa</label>
                                </div>
                            </td>
                            <td><input type="text" name="absensi[<?= $g['id_guru'] ?>][tugas]" class="form-control form-control-sm" placeholder="Diisi guru pengganti..." value="<?= htmlspecialchars($absensi['tugas'] ?? '') ?>"></td>
                            <td><input type="text" name="absensi[<?= $g['id_guru'] ?>][keterangan]" class="form-control form-control-sm" value="<?= htmlspecialchars($absensi['keterangan'] ?? '') ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <?php if (!empty($guru_list)): // Hanya tampilkan tombol simpan jika ada guru ?>
                <button type="submit" class="btn btn-success">Simpan Absensi Guru</button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>