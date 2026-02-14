<?php
// tahfidz_form.php
include __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <?= $tahfidz ? 'Edit' : 'Tambah' ?> Kelompok Tahfidz
                </h3>
            </div>
            <form action="index.php?mod=tahfidz&act=save" method="post">
                <?php if ($tahfidz): ?>
                    <input type="hidden" name="id_tahfidz" value="<?= $tahfidz['id_tahfidz'] ?>">
                <?php endif; ?>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="nama_kelompok" class="form-label">Nama Kelompok / Halaqah (Sesuai Penugasan)</label>
                        <select name="nama_kelompok" id="nama_kelompok" class="form-select select2" required>
                            <option value="">-- Pilih Kegiatan Tahfidz --</option>
                            <?php foreach ($assigned_activities as $act): ?>
                                <option value="<?= htmlspecialchars($act['nama_kegiatan']) ?>" 
                                    data-guru-id="<?= $act['id_guru'] ?>"
                                    data-guru-nama="<?= htmlspecialchars($act['nama_guru']) ?>"
                                    <?= ($tahfidz['nama_kelompok'] ?? '') == $act['nama_kegiatan'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($act['nama_kegiatan']) ?> (Musyrif: <?= htmlspecialchars($act['nama_guru']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Jika kegiatan tidak muncul, tambahkan dulu di menu Penugasan Guru.</small>
                    </div>

                    <div class="mb-3">
                        <label for="id_guru_pembina" class="form-label">Pembimbing (Musyrif) - Otomatis</label>
                        <input type="text" id="nama_pembina_display" class="form-control" readonly placeholder="Otomatis terisi...">
                        <input type="hidden" name="id_guru_pembina" id="id_guru_pembina" value="<?= $tahfidz['id_guru_pembina'] ?? '' ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hari" class="form-label">Hari</label>
                                <select class="form-select" id="hari" name="hari">
                                    <option value="">-- Pilih Hari --</option>
                                    <?php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                    foreach ($days as $d): ?>
                                        <option value="<?= $d ?>" <?= ($tahfidz['hari'] ?? '') == $d ? 'selected' : '' ?>><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jam" class="form-label">Jam</label>
                                <input type="time" class="form-control" id="jam" name="jam" value="<?= $tahfidz['jam'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tingkat_target" class="form-label">Target Hafalan (Opsional)</label>
                         <input type="text" class="form-control" id="tingkat_target" name="tingkat_target" value="<?= htmlspecialchars($tahfidz['tingkat_target'] ?? '') ?>" placeholder="Contoh: Juz 30">
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="Aktif" <?= ($tahfidz['status'] ?? 'Aktif') == 'Aktif' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="index.php?mod=tahfidz" class="btn btn-secondary">Batal</a>
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
        $('#nama_kelompok').on('change', function() {
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
        
        if($('#nama_kelompok').val()) {
            $('#nama_kelompok').trigger('change');
        }
    });
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>
