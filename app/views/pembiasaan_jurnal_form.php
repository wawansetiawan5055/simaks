<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-pray mr-2"></i> Jurnal & Absensi Harian:
            <strong><?= htmlspecialchars($pembiasaan['nama_kegiatan']) ?></strong></h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <form action="index.php?mod=pembiasaan&act=jurnal_save" method="POST">
            <input type="hidden" name="id_pembiasaan" value="<?= $pembiasaan['id_pembiasaan'] ?>">
            <?php if ($jurnal): ?>
                <input type="hidden" name="id_jurnal" value="<?= $jurnal['id_jurnal'] ?>">
            <?php endif; ?>

            <div class="row">
                <!-- FORM JURNAL -->
                <div class="col-md-4">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Data Kegiatan</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required
                                    value="<?= $jurnal['tanggal'] ?? date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label>Materi / Kegiatan</label>
                                <textarea name="materi" class="form-control" rows="3" required
                                    placeholder="Contoh: Sholat Dhuha Berjamaah"><?= $jurnal['materi'] ?? $pembiasaan['nama_kegiatan'] ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Catatan Tambahan</label>
                                <textarea name="keterangan" class="form-control"
                                    rows="2"><?= $jurnal['keterangan'] ?? '' ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Guru Pendamping</label>
                                <input type="hidden" name="id_guru" value="<?= $_SESSION['user']['id_guru'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORM ABSENSI -->
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Absensi Anggota</h3>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center" width="250">Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($anggota)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center p-3">Belum ada anggota terdaftar.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($anggota as $i => $a):
                                            $s_val = $presensi[$a['id_siswa']] ?? 'H';
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $i + 1 ?></td>
                                                <td>
                                                    <strong><?= $a['nama_siswa'] ?></strong><br>
                                                    <small class="text-muted"><?= $a['nama_kelas'] ?></small>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label
                                                            class="btn btn-outline-success btn-sm <?= $s_val == 'H' ? 'active' : '' ?>">
                                                            <input type="radio" name="presensi[<?= $a['id_siswa'] ?>]" value="H"
                                                                <?= $s_val == 'H' ? 'checked' : '' ?>> H
                                                        </label>
                                                        <label
                                                            class="btn btn-outline-warning btn-sm <?= $s_val == 'S' ? 'active' : '' ?>">
                                                            <input type="radio" name="presensi[<?= $a['id_siswa'] ?>]" value="S"
                                                                <?= $s_val == 'S' ? 'checked' : '' ?>> S
                                                        </label>
                                                        <label
                                                            class="btn btn-outline-info btn-sm <?= $s_val == 'I' ? 'active' : '' ?>">
                                                            <input type="radio" name="presensi[<?= $a['id_siswa'] ?>]" value="I"
                                                                <?= $s_val == 'I' ? 'checked' : '' ?>> I
                                                        </label>
                                                        <label
                                                            class="btn btn-outline-danger btn-sm <?= $s_val == 'A' ? 'active' : '' ?>">
                                                            <input type="radio" name="presensi[<?= $a['id_siswa'] ?>]" value="A"
                                                                <?= $s_val == 'A' ? 'checked' : '' ?>> A
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-right">
                            <a href="index.php?mod=pembiasaan&id=<?= $pembiasaan['id_pembiasaan'] ?>&tab=jurnal"
                                class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>