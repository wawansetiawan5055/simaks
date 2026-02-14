<?php include __DIR__ . '/partials/header.php'; ?>

<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-store mr-2"></i> <?= $kewirausahaan ? 'Edit' : 'Tambah' ?> Kewirausahaan</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <?php
        // kewirausahaan_form.php
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <?= $kewirausahaan ? 'Edit' : 'Tambah' ?> Kewirausahaan
                        </h3>
                    </div>
                    <form action="index.php?mod=kewirausahaan&act=save" method="post">
                        <?php if ($kewirausahaan): ?>
                            <input type="hidden" name="id_kewirausahaan" value="<?= $kewirausahaan['id_kewirausahaan'] ?>">
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nama_kegiatan" class="form-label">Nama Kegiatan (Sesuai Penugasan)</label>
                                <select name="nama_kegiatan" id="nama_kegiatan" class="form-select select2" required>
                                    <option value="">-- Pilih Kegiatan --</option>
                                    <?php foreach ($assigned_activities as $act): ?>
                                        <option value="<?= htmlspecialchars($act['nama_kegiatan']) ?>"
                                            data-guru-id="<?= $act['id_guru'] ?>"
                                            data-guru-nama="<?= htmlspecialchars($act['nama_guru']) ?>"
                                            <?= ($kewirausahaan['nama_kegiatan'] ?? '') == $act['nama_kegiatan'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($act['nama_kegiatan']) ?> (Pembina:
                                            <?= htmlspecialchars($act['nama_guru']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Jika kegiatan tidak muncul, tambahkan dulu di menu Penugasan
                                    Guru.</small>
                            </div>

                            <div class="mb-3">
                                <label for="kelompok" class="form-label">Kelompok / Kategori</label>
                                <input type="text" name="kelompok" id="kelompok" class="form-control"
                                    list="listKelompok" placeholder="Contoh: Tata Boga, Agency, Pertanian..."
                                    value="<?= htmlspecialchars($kewirausahaan['kelompok'] ?? '') ?>">
                                <datalist id="listKelompok">
                                    <option value="Tata Boga">
                                    <option value="Agency">
                                    <option value="Pertanian">
                                    <option value="Kerajinan">
                                    <option value="Teknologi">
                                </datalist>
                            </div>

                            <div class="mb-3">
                                <label for="id_guru_pembina" class="form-label">Pembina (Guru)</label>
                                <input type="text" id="nama_pembina_display" class="form-control" readonly
                                    placeholder="Otomatis terisi...">
                                <input type="hidden" name="id_guru_pembina" id="id_guru_pembina"
                                    value="<?= $kewirausahaan['id_guru_pembina'] ?? '' ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hari" class="form-label">Hari</label>
                                        <select class="form-select" id="hari" name="hari">
                                            <option value="">-- Pilih Hari --</option>
                                            <?php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                            foreach ($days as $d): ?>
                                                <option value="<?= $d ?>" <?= ($kewirausahaan['hari'] ?? '') == $d ? 'selected' : '' ?>><?= $d ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam" class="form-label">Jam</label>
                                        <input type="time" class="form-control" id="jam" name="jam"
                                            value="<?= $kewirausahaan['jam'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                    placeholder="Opsional"><?= htmlspecialchars($kewirausahaan['keterangan'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="Aktif" <?= ($kewirausahaan['status'] ?? 'Aktif') == 'Aktif' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="status">Aktif</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="index.php?mod=kewirausahaan" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            if ($('.select2').length) {
                $('.select2').select2({ theme: 'bootstrap4' });
            }

            // Auto-fill Pembina
            $('#nama_kegiatan').on('change', function () {
                var selected = $(this).find(':selected');
                var guruId = selected.data('guru-id');
                var guruNama = selected.data('guru-nama');

                if (guruId) {
                    $('#id_guru_pembina').val(guruId);
                    $('#nama_pembina_display').val(guruNama);
                } else {
                    $('#id_guru_pembina').val('');
                    $('#nama_pembina_display').val('');
                }
            });

            if ($('#nama_kegiatan').val()) {
                $('#nama_kegiatan').trigger('change');
            }
        });
    </script>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>