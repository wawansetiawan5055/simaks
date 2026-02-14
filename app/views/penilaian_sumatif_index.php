<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-poll mr-2"></i> Penilaian Sumatif</h1>
            </div>
            <div class="col-sm-6 text-right">
                <?php if ($id_kelas_filter && $id_guru_mapel_filter): ?>
                    <a href="index.php?mod=penilaian_sumatif&act=form_agenda&id_kelas=<?= $id_kelas_filter ?>&id_guru_mapel=<?= $id_guru_mapel_filter ?>"
                        class="btn btn-primary"><i class="fa fa-plus"></i> Buat Agenda Penilaian Baru</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <?php // Session messages now handled by toast notifications in footer.php ?>
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Filter Data Agenda Penilaian</h3>
            </div>
            <form method="GET" id="filterFormIndex">
                <input type="hidden" name="mod" value="penilaian_sumatif">
                <div class="card-body row">
                    <div class="form-group col-md-6">
                        <label>Kelas</label>
                        <select name="id_kelas" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_diajar as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if ($id_kelas_filter && !empty($mapel_diajar)): ?>
                        <div class="form-group col-md-6">
                            <label>Mata Pelajaran</label>
                            <select name="id_guru_mapel" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Pilih Mapel --</option>
                                <?php foreach ($mapel_diajar as $m): ?>
                                    <option value="<?= $m['id_guru_mapel'] ?>" <?= ($id_guru_mapel_filter == $m['id_guru_mapel']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($id_kelas_filter && $id_guru_mapel_filter): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Agenda Penilaian Sumatif</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama Penilaian</th>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($agenda_list)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada agenda penilaian sumatif untuk kelas dan mapel
                                        ini.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($agenda_list as $agenda): ?>
                                <tr>
                                    <td><?= htmlspecialchars($agenda['nama_penilaian']) ?></td>
                                    <td><?= htmlspecialchars($agenda['jenis_sumatif']) ?></td>
                                    <td><?= $agenda['tanggal_penilaian'] ? DateHelper::formatTanggal($agenda['tanggal_penilaian'], 'short') : '-' ?>
                                    </td>
                                    <td>
                                        <a href="index.php?mod=penilaian_sumatif&act=form_nilai&id_sumatif=<?= $agenda['id_sumatif'] ?>"
                                            class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Input/Edit Nilai</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>