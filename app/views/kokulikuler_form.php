<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><?= $kokul ? 'Edit' : 'Tambah' ?> Kegiatan Kokulikuler</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <form action="<?= BASE_URL ?>kokulikuler/save" method="POST">
                <?php if ($kokul): ?>
                    <input type="hidden" name="id_kokulikuler" value="<?= $kokul['id_kokulikuler'] ?>">
                <?php endif; ?>

                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Kegiatan (Sesuai Penugasan)</label>
                        <!-- CHANGED: Use id_penugasan for robust backend lookup -->
                        <select name="id_penugasan" id="nama_kegiatan" class="form-control select2" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            <?php foreach ($assigned_activities as $act): ?>
                                <!-- VALUE is the Unique Assignment ID. -->
                                <option value="<?= $act['id_penugasan_pembina'] ?>" 
                                    data-guru-id="<?= $act['id_guru'] ?>"
                                    data-guru-nama="<?= htmlspecialchars($act['nama_guru']) ?>"
                                    data-nama-kegiatan="<?= htmlspecialchars($act['nama_kegiatan']) ?>"
                                    <?= ($kokul['nama_kegiatan'] ?? '') == $act['nama_kegiatan'] && ($kokul['id_guru_pembina'] ?? '') == $act['id_guru'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($act['nama_kegiatan']) ?> (Pembina: <?= htmlspecialchars($act['nama_guru']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Jika kegiatan tidak muncul, tambahkan dulu di menu Penugasan Guru.</small>
                    </div>

                    <div class="form-group">
                        <label>Guru Pembina (Otomatis)</label>
                        <input type="text" id="nama_pembina_display" class="form-control" readonly placeholder="Otomatis terisi...">
                        <input type="hidden" name="id_guru_pembina" id="id_guru_pembina" value="<?= $kokul['id_guru_pembina'] ?? '' ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hari Pelaksanaan</label>
                                <select name="hari" class="form-control">
                                    <option value="">-- Pilih Hari --</option>
                                    <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h): ?>
                                        <option value="<?= $h ?>" <?= ($kokul['hari'] ?? '') == $h ? 'selected' : '' ?>>
                                            <?= $h ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control"
                                    value="<?= $kokul['jam_mulai'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control"
                                    value="<?= $kokul['jam_selesai'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status"
                                value="Aktif" <?= ($kokul['status'] ?? 'Aktif') == 'Aktif' ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="statusSwitch">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="<?= BASE_URL ?>kokulikuler" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
<script>
    $(document).ready(function () {
        if ($('.select2').length) {
            $('.select2').select2({ theme: 'bootstrap4' });
        }
        
        // Auto-fill Pembina
        $('#nama_kegiatan').on('change', function() {
            var selected = $(this).find(':selected');
            var guruId = selected.data('guru-id');
            var guruNama = selected.data('guru-nama');
            
            if(guruId) {
                $('#id_guru_pembina').val(guruId);
                $('#nama_pembina_display').val(guruNama);
            } else {
                $('#id_guru_pembina').val('');
                $('#nama_pembina_display').val('');
            }
        });
        
        if($('#nama_kegiatan').val()) {
            $('#nama_kegiatan').trigger('change');
        }
    });
</script>
