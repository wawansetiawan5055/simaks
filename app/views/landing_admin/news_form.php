<?php
// app/views/landing_admin/news_form.php
$title = "Form Berita";
$is_edit = isset($news);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-edit mr-2"></i> <?= $is_edit ? 'Edit Berita' : 'Tambah Berita' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Berita' : 'Tambah Berita Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/news_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $news['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="form-group">
                <label>Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title"
                    value="<?= $is_edit ? htmlspecialchars($news['judul']) : '' ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Publikasi</label>
                        <input type="date" class="form-control" name="publish_date"
                            value="<?= $is_edit ? $news['tanggal_publikasi'] : date('Y-m-d') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Konten</label>
                <textarea class="form-control" name="content" rows="10"
                    required><?= $is_edit ? htmlspecialchars($news['konten']) : '' ?></textarea>
                <small class="text-muted">Bisa menggunakan HTML sederhana.</small>
            </div>

            <div class="form-group">
                <label>Gambar</label>
                <?php if ($is_edit && !empty($news['gambar'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL . $news['gambar'] ?>" alt="Current Image" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="featured_image" name="featured_image">
                    <label class="custom-file-label" for="featured_image">Pilih file...</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" value="1"
                        <?= (!$is_edit || $news['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_published">Langsung Aktifkan/Terbitkan</label>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="<?= BASE_URL ?>landing_admin/news" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>