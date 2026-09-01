<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-edit mr-2"></i> <?= $pembiasaan ? 'Edit' : 'Tambah' ?> Pembiasaan</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-success">
            <form action="<?= BASE_URL ?>pembiasaan/save" method="POST">
                <?php if ($pembiasaan): ?>
                    <input type="hidden" name="id_pembiasaan" value="<?= $pembiasaan['id_pembiasaan'] ?>">
                <?php endif; ?>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                            value="<?= htmlspecialchars($pembiasaan['nama_kegiatan'] ?? '') ?>"
                            placeholder="Contoh: Sholat Dhuha" required>
                    </div>

                    <div class="mb-3">
                        <label for="id_guru_pembina" class="form-label">Pembina (Guru)</label>
                        <select class="form-select select2" id="id_guru_pembina" name="id_guru_pembina">
                            <option value="">-- Pilih Pembina --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>" <?= ($pembiasaan['id_guru_pembina'] ?? '') == $g['id_guru'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['nama_guru']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari" class="form-control">
                                    <option value="">-- Pilih Hari --</option>
                                    <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Setiap Hari'] as $h): ?>
                                        <option value="<?= $h ?>" <?= ($pembiasaan['hari'] ?? '') == $h ? 'selected' : '' ?>>
                                            <?= $h ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam</label>
                                <input type="time" name="jam" class="form-control"
                                    value="<?= $pembiasaan['jam'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Opsional"><?= $pembiasaan['keterangan'] ?? '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status"
                                value="Aktif" <?= ($pembiasaan['status'] ?? 'Aktif') == 'Aktif' ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="statusSwitch">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="<?= BASE_URL ?>pembiasaan" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>