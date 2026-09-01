<?php
// app/views/landing_admin/sambutan_form.php
$title = "Form Sambutan Kepala Sekolah";
$is_edit = isset($sambutan);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-user-tie mr-2"></i> <?= $is_edit ? 'Edit Sambutan' : 'Tambah Sambutan' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Sambutan Kepala Sekolah' : 'Tambah Sambutan Kepala Sekolah Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/sambutan_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $sambutan['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Kepala Sekolah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kepala"
                            value="<?= $is_edit ? htmlspecialchars($sambutan['nama_kepala']) : '' ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="jabatan"
                            value="<?= $is_edit ? htmlspecialchars($sambutan['jabatan']) : 'Kepala Sekolah' ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Judul Sambutan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="judul"
                    value="<?= $is_edit ? htmlspecialchars($sambutan['judul']) : 'Sambutan Kepala Sekolah' ?>" required>
                <small class="text-muted">Judul yang akan ditampilkan di halaman sambutan</small>
            </div>

            <div class="form-group">
                <label>Konten Sambutan <span class="text-danger">*</span></label>
                <textarea class="form-control" name="konten" rows="15" required><?= $is_edit ? htmlspecialchars($konten ?? $sambutan['konten']) : '' ?></textarea>
                <small class="text-muted">Tulis sambutan lengkap kepala sekolah. Bisa menggunakan HTML sederhana untuk formatting.</small>
            </div>

            <div class="form-group">
                <label>Foto Kepala Sekolah</label>
                <?php if ($is_edit && !empty($sambutan['foto'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($sambutan['foto']) ?>"
                             alt="Foto Kepala Sekolah" style="max-height: 150px; border-radius: 8px;">
                        <small class="text-muted d-block mt-1">Foto saat ini</small>
                    </div>
                <?php endif; ?>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                    <label class="custom-file-label" for="foto">Pilih foto kepala sekolah...</label>
                </div>
                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB. Rekomendasi ukuran: 400x400px</small>
            </div>

            <div class="form-group">
                <label>Tanggal Update</label>
                <input type="date" class="form-control" name="tanggal_update"
                    value="<?= $is_edit ? date('Y-m-d', strtotime($sambutan['tanggal_update'])) : date('Y-m-d') ?>">
                <small class="text-muted">Tanggal terakhir update sambutan</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        <?= (!$is_edit || $sambutan['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_active">Aktifkan Sambutan</label>
                </div>
                <small class="text-muted">Hanya satu sambutan yang bisa aktif dalam satu waktu</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Sambutan
            </button>
            <a href="<?= BASE_URL ?>landing_admin/sambutan" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Update file input label
$('#foto').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName || 'Pilih foto kepala sekolah...');
});
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>