<?php
// app/views/landing_admin/program_form.php
$title = "Form Program Sekolah";
$is_edit = isset($program);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-graduation-cap mr-2"></i>
            <?= $is_edit ? 'Edit Program' : 'Tambah Program' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Program Sekolah' : 'Tambah Program Sekolah Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/program_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $program['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="form-group">
                <label>Nama Program <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title"
                    value="<?= $is_edit ? htmlspecialchars($program['title']) : '' ?>" required>
                <small class="text-muted">Nama program yang akan ditampilkan</small>
            </div>

            <div class="form-group">
                <label>Deskripsi Program <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="4"
                    required><?= $is_edit ? htmlspecialchars($program['description']) : '' ?></textarea>
                <small class="text-muted">Deskripsi singkat tentang program ini</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ikon FontAwesome</label>
                        <input type="text" class="form-control" name="icon"
                            value="<?= $is_edit ? htmlspecialchars($program['icon']) : '' ?>"
                            placeholder="fas fa-graduation-cap">
                        <small class="text-muted">Misalnya: <code>fas fa-graduation-cap</code></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Urutan Tampilan</label>
                        <input type="number" class="form-control" name="order_display"
                            value="<?= $is_edit ? $program['order_display'] : '1' ?>">
                        <small class="text-muted">Angka untuk mengurutkan tampilan program</small>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label>Gambar Program</label>
                <?php if ($is_edit && !empty($program['image'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL ?>uploads/program/<?= htmlspecialchars($program['image']) ?>"
                            alt="Gambar Program" style="max-height: 150px; border-radius: 8px;">
                        <small class="text-muted d-block mt-1">Gambar saat ini</small>
                    </div>
                <?php endif; ?>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                    <label class="custom-file-label" for="image">Pilih gambar program...</label>
                </div>
                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB. Rekomendasi ukuran: 400x300px</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        <?= (!$is_edit || $program['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_active">Aktifkan Program</label>
                </div>
                <small class="text-muted">Program aktif akan ditampilkan di halaman publik</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Program
            </button>
            <a href="<?= BASE_URL ?>landing_admin/program" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
    // Update file input label
    $('#image').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih gambar program...');
    });
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>