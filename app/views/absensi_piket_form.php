<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-user-clock mr-2"></i> Formulir Absensi Piket</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?php // Session messages now handled by toast notifications in footer.php ?>
        <form action="index.php?mod=absensi_piket&act=save" method="POST">
            <input type="hidden" name="id_kelas" value="<?= $id_kelas ?>">
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelas: <strong><?= $kelas['tingkat'] . ' - ' . $kelas['nama_kelas'] ?></strong> |
                        Tanggal: <strong><?= DateHelper::formatTanggal($tanggal, 'long') ?></strong></h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kehadiran</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($siswa_list as $s): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $s['nama'] ?><br><small class="text-muted">NISN: <?= $s['nisn'] ?></small></td>
                                    <td>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-success active"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Hadir" checked>
                                                Hadir</label>
                                            <label class="btn btn-outline-warning"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Sakit">
                                                Sakit</label>
                                            <label class="btn btn-outline-info"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Izin"> Izin</label>
                                            <label class="btn btn-outline-danger"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Alpa"> Alpa</label>
                                        </div>
                                    </td>
                                    <td><input type="text" name="absensi[<?= $s['id_siswa'] ?>][keterangan]"
                                            class="form-control form-control-sm"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Simpan Absensi Piket</button>
                    <a href="index.php?mod=absensi_piket" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>