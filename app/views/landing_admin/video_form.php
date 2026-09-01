<?php
// app/views/landing_admin/video_form.php
$title = "Form Video";
$is_edit = isset($video);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-video mr-2"></i>
            <?= $is_edit ? 'Edit Video' : 'Tambah Video' ?>
        </h1>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="mb-0 font-weight-bold text-muted">
            <?= $is_edit ? 'FORM EDIT DATA' : 'FORM TAMBAH DATA' ?>
        </h6>
    </div>

    <form action="<?= BASE_URL ?>landing_admin/video_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $video['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">JUDUL VIDEO <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul"
                            value="<?= $is_edit ? htmlspecialchars($video['judul']) : '' ?>" required
                            placeholder="Contoh: Kegiatan Porseni 2024">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">URL VIDEO (Youtube/Vimeo) <span
                                class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="video_url"
                            value="<?= $is_edit ? htmlspecialchars($video['video_url']) : '' ?>" required
                            placeholder="Contoh: https://www.youtube.com/watch?v=...">
                        <small class="text-muted d-block mt-1">Masukkan URL lengkap dari video.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold text-muted small">KATEGORI</label>
                            <input type="text" class="form-control" name="kategori"
                                value="<?= $is_edit ? htmlspecialchars($video['kategori']) : '' ?>"
                                placeholder="Contoh: Akademik, Ekstrakurikuler">
                        </div>
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold text-muted small">TIPE PROVIDER</label>
                            <select class="form-control" name="tipe">
                                <option value="youtube" <?= ($is_edit && $video['tipe'] == 'youtube') ? 'selected' : '' ?>
                                    >YouTube</option>
                                <option value="vimeo" <?= ($is_edit && $video['tipe'] == 'vimeo') ? 'selected' : '' ?>
                                    >Vimeo</option>
                                <option value="upload" <?= ($is_edit && $video['tipe'] == 'upload') ? 'selected' : '' ?>
                                    >URL Langsung (MP4)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">DESKRIPSI (Opsional)</label>
                        <textarea class="form-control" name="deskripsi" rows="4"
                            placeholder="Deskripsi singkat mengenai video ini..."><?= $is_edit ? htmlspecialchars($video['deskripsi']) : '' ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-light p-3 rounded mb-4" style="border: 1px dashed #ced4da;">
                        <label class="font-weight-bold text-muted small">CUSTOM THUMBNAIL (Opsional)</label>
                        <?php if ($is_edit && !empty($video['thumbnail'])): ?>
                            <div class="mb-3 text-center">
                                <img src="<?= BASE_URL . $video['thumbnail'] ?>" alt="Preview"
                                    class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="thumbnail" name="thumbnail">
                            <label class="custom-file-label" for="thumbnail" data-browse="Pilih">Cari file...</label>
                        </div>
                        <small class="text-muted d-block mt-2"><i class="fas fa-info-circle mr-1"></i> Biarkan kosong
                            untuk menggunakan thumbnail default YouTube.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">DURASI (Opsional)</label>
                        <input type="text" class="form-control" name="durasi"
                            value="<?= $is_edit ? htmlspecialchars($video['durasi']) : '' ?>"
                            placeholder="Contoh: 05:30">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">URUTAN TAMPIL</label>
                        <input type="number" class="form-control" name="display_order"
                            value="<?= $is_edit ? $video['display_order'] : 1 ?>" min="1">
                    </div>

                    <div class="form-group mt-4">
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                value="1" <?= (!$is_edit || $video['is_active']) ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-bold text-muted small"
                                style="padding-top:2px;" for="is_active">Aktifkan Video?</label>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured"
                                value="1" <?= ($is_edit && $video['is_featured']) ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-bold text-muted small"
                                style="padding-top:2px;" for="is_featured">Jadikan Featured Video?</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-top py-3">
            <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold"><i
                    class="fas fa-save mr-2"></i> SIMPAN</button>
            <a href="<?= BASE_URL ?>landing_admin/video" class="btn btn-light border px-4 ml-2">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>