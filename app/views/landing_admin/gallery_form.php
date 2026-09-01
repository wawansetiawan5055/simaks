<?php
// app/views/landing_admin/gallery_form.php
$title = "Form Galeri";
$is_edit = isset($gallery);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-image mr-2"></i> <?= $is_edit ? 'Edit Foto' : 'Upload Foto' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Foto' : 'Upload Foto Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/gallery_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $gallery['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="form-group">
                <label>Judul Foto <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title"
                    value="<?= $is_edit ? htmlspecialchars($gallery['title']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select class="form-control" name="category">
                    <option value="Kegiatan" <?= ($is_edit && $gallery['category'] == 'Kegiatan') ? 'selected' : '' ?>>
                        Kegiatan</option>
                    <option value="Fasilitas" <?= ($is_edit && $gallery['category'] == 'Fasilitas') ? 'selected' : '' ?>>
                        Fasilitas</option>
                    <option value="Prestasi" <?= ($is_edit && $gallery['category'] == 'Prestasi') ? 'selected' : '' ?>>
                        Prestasi</option>
                    <option value="Ekstrakurikuler" <?= ($is_edit && $gallery['category'] == 'Ekstrakurikuler') ? 'selected' : '' ?>>Ekstrakurikuler</option>
                    <option value="Lainnya" <?= ($is_edit && $gallery['category'] == 'Lainnya') ? 'selected' : '' ?>>
                        Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea class="form-control" name="description"
                    rows="3"><?= $is_edit ? htmlspecialchars($gallery['description']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label>File Foto</label>
                <?php if ($is_edit && !empty($gallery['image_path'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL . $gallery['image_path'] ?>" alt="Current Image" style="max-height: 150px;">
                    </div>
                <?php endif; ?>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="image_path" name="image_path" <?= $is_edit ? '' : 'required' ?>>
                    <label class="custom-file-label" for="image_path">Pilih foto...</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_slider" name="is_slider" value="1"
                        <?= ($is_edit && $gallery['is_slider']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_slider">Jadikan Slider di Halaman Beranda</label>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="<?= BASE_URL ?>landing_admin/gallery" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>