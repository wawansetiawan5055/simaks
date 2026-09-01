<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-user-clock mr-2"></i> Absensi Siswa Harian (Piket)
                    <?php if (!empty($has_existing_data)): ?>
                        <span class="badge badge-warning ml-2" style="font-size:0.65rem;vertical-align:middle;">MODE EDIT</span>
                    <?php elseif (!empty($is_past_date)): ?>
                        <span class="badge badge-warning ml-2" style="font-size:0.65rem;vertical-align:middle;">MODE EDIT (TANGGAL LALU)</span>
                    <?php endif; ?>
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= BASE_URL ?>absensi_piket" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Ganti Kelas / Tanggal
                </a>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">

        <?php if (!empty($has_existing_data)): ?>
        <!-- Banner Edit Mode - Data Tersimpan -->
        <div class="callout callout-warning mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-edit fa-2x text-warning mr-3"></i>
                <div>
                    <strong>Mode Edit — Data Sudah Tersimpan</strong><br>
                    <small class="text-muted">Data absensi piket untuk kelas ini pada tanggal ini sudah pernah disimpan. Menyimpan ulang akan <strong>memperbarui</strong> data yang lama.</small>
                </div>
            </div>
        </div>
        <?php elseif (!empty($is_past_date)): ?>
        <!-- Banner Edit Mode - Tanggal Lalu -->
        <div class="callout callout-warning mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-history fa-2x text-warning mr-3"></i>
                <div>
                    <strong>Mode Edit — Absensi Tanggal Lalu</strong><br>
                    <small class="text-muted">Anda sedang menginput/mengubah absensi piket untuk tanggal yang telah lalu (<?= DateHelper::formatTanggal($tanggal, 'long') ?>).</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>absensi_piket/save" method="POST">
            <input type="hidden" name="id_kelas" value="<?= $id_kelas ?>">
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chalkboard mr-2"></i>
                        Kelas: <strong><?= htmlspecialchars($kelas['nama_kelas']) ?></strong> &nbsp;|&nbsp;
                        <i class="fas fa-calendar-day mr-1"></i>
                        <?= DateHelper::formatTanggal($tanggal, 'long') ?>
                    </h3>
                    <?php if ($is_edit_mode):
                        $jumlah_hadir = count(array_filter($absensi_existing, fn($a) => $a['status'] === 'Hadir'));
                        $jumlah_tidak = count($absensi_existing) - $jumlah_hadir;
                    ?>
                    <div class="d-flex gap-2">
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i><?= $jumlah_hadir ?> Hadir</span>
                        <span class="badge badge-danger px-2 py-1 ml-1"><i class="fas fa-times mr-1"></i><?= $jumlah_tidak ?> Tidak Hadir</span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama Siswa</th>
                                    <th width="320">Kehadiran</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($siswa_list as $s):
                                    $existing = $absensi_existing[$s['id_siswa']] ?? null;
                                    $status_saved = $existing['status'] ?? 'Hadir';
                                    $ket_saved = $existing['keterangan'] ?? '';
                                    $is_hadir = $status_saved === 'Hadir';
                                    $row_class = '';
                                    if ($existing && $status_saved !== 'Hadir') {
                                        $row_class = 'table-warning';
                                    }
                                ?>
                                <tr class="<?= $row_class ?>">
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <span class="font-weight-bold"><?= htmlspecialchars($s['nama']) ?></span><br>
                                        <small class="text-muted">NISN: <?= $s['nisn'] ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-success btn-sm <?= $status_saved === 'Hadir' ? 'active' : '' ?>">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Hadir" <?= $status_saved === 'Hadir' ? 'checked' : '' ?>> Hadir
                                            </label>
                                            <label class="btn btn-outline-info btn-sm <?= $status_saved === 'Izin' ? 'active' : '' ?>">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Izin" <?= $status_saved === 'Izin' ? 'checked' : '' ?>> Izin
                                            </label>
                                            <label class="btn btn-outline-warning btn-sm <?= $status_saved === 'Sakit' ? 'active' : '' ?>">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Sakit" <?= $status_saved === 'Sakit' ? 'checked' : '' ?>> Sakit
                                            </label>
                                            <label class="btn btn-outline-danger btn-sm <?= $status_saved === 'Alpa' ? 'active' : '' ?>">
                                                <input type="radio" name="absensi[<?= $s['id_siswa'] ?>][status]" value="Alpa" <?= $status_saved === 'Alpa' ? 'checked' : '' ?>> Alpa
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="absensi[<?= $s['id_siswa'] ?>][keterangan]"
                                            class="form-control form-control-sm"
                                            value="<?= htmlspecialchars($ket_saved) ?>"
                                            placeholder="Opsional...">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-<?= !empty($has_existing_data) ? 'warning' : 'success' ?> px-4">
                        <i class="fas fa-<?= !empty($has_existing_data) ? 'sync-alt' : 'save' ?> mr-1"></i>
                        <?= !empty($has_existing_data) ? 'Update Absensi' : 'Simpan Absensi' ?>
                    </button>
                    <a href="<?= BASE_URL ?>absensi_piket" class="btn btn-secondary ml-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <?php if (!empty($has_existing_data)): ?>
                    <small class="text-muted ml-3">
                        <i class="fas fa-info-circle"></i> Menyimpan ulang akan memperbarui data absensi sebelumnya.
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>