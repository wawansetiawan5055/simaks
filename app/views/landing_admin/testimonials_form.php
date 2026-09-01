<?php
// app/views/landing_admin/testimonials_form.php
$title = "Form Testimonial";
$is_edit = isset($testimonial);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-comments mr-2"></i> <?= $is_edit ? 'Edit Testimonial' : 'Tambah Testimonial' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Testimonial' : 'Tambah Testimonial Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/testimonials_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $testimonial['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama"
                            value="<?= $is_edit ? htmlspecialchars($testimonial['nama']) : '' ?>" required>
                        <small class="text-muted">Nama pemberi testimonial</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jabatan <span class="text-danger">*</span></label>
                        <select class="form-control" name="jabatan" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="Siswa" <?= ($is_edit && $testimonial['jabatan'] == 'Siswa') ? 'selected' : '' ?>>Siswa</option>
                            <option value="Alumni" <?= ($is_edit && $testimonial['jabatan'] == 'Alumni') ? 'selected' : '' ?>>Alumni</option>
                            <option value="Orang Tua" <?= ($is_edit && $testimonial['jabatan'] == 'Orang Tua') ? 'selected' : '' ?>>Orang Tua</option>
                            <option value="Guru" <?= ($is_edit && $testimonial['jabatan'] == 'Guru') ? 'selected' : '' ?>>Guru</option>
                            <option value="Staff" <?= ($is_edit && $testimonial['jabatan'] == 'Staff') ? 'selected' : '' ?>>Staff</option>
                            <option value="Lainnya" <?= ($is_edit && $testimonial['jabatan'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Institusi</label>
                <input type="text" class="form-control" name="institusi"
                    value="<?= $is_edit ? htmlspecialchars($testimonial['institusi']) : '' ?>"
                    placeholder="Contoh: SMA Negeri 1 Jakarta, Universitas Indonesia, dll">
                <small class="text-muted">Institusi atau sekolah terkait (opsional)</small>
            </div>

            <div class="form-group">
                <label>Testimonial <span class="text-danger">*</span></label>
                <textarea class="form-control" name="testimonial" rows="5" required
                    placeholder="Tulis testimonial di sini..."><?= $is_edit ? htmlspecialchars($testimonial['testimonial']) : '' ?></textarea>
                <small class="text-muted">Isi testimonial yang akan ditampilkan</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Rating <span class="text-danger">*</span></label>
                        <select class="form-control" name="rating" required>
                            <option value="">Pilih Rating</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= ($is_edit && $testimonial['rating'] == $i) ? 'selected' : '' ?>>
                                    <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
                                    <?php for ($j = 1; $j <= $i; $j++) echo '⭐'; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <small class="text-muted">Rating kepuasan (1-5 bintang)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Testimonial</label>
                        <input type="date" class="form-control" name="tanggal"
                            value="<?= $is_edit ? date('Y-m-d', strtotime($testimonial['tanggal'])) : date('Y-m-d') ?>">
                        <small class="text-muted">Tanggal pemberian testimonial</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Foto Pemberi Testimonial</label>
                <?php if ($is_edit && !empty($testimonial['foto'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($testimonial['foto']) ?>"
                             alt="Foto Testimonial" style="max-height: 150px; border-radius: 8px;">
                        <small class="text-muted d-block mt-1">Foto saat ini</small>
                    </div>
                <?php endif; ?>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                    <label class="custom-file-label" for="foto">Pilih foto testimonial...</label>
                </div>
                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB. Rekomendasi ukuran: 200x200px</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        <?= (!$is_edit || $testimonial['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_active">Tampilkan Testimonial</label>
                </div>
                <small class="text-muted">Testimonial aktif akan ditampilkan di halaman publik</small>
            </div>

            <!-- Preview Section -->
            <div class="form-group">
                <label>Preview Testimonial</label>
                <div class="card border-info">
                    <div class="card-body">
                        <div class="testimonial-preview">
                            <div class="d-flex align-items-start">
                                <div class="avatar-preview mr-3">
                                    <?php if ($is_edit && !empty($testimonial['foto'])): ?>
                                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($testimonial['foto']) ?>"
                                             alt="Preview" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="content-preview flex-grow-1">
                                    <div class="rating-preview mb-2">
                                        <span id="rating-preview">
                                            <?php if ($is_edit): ?>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            <?php else: ?>
                                                <i class="fas fa-star text-muted"></i>
                                                <i class="fas fa-star text-muted"></i>
                                                <i class="fas fa-star text-muted"></i>
                                                <i class="fas fa-star text-muted"></i>
                                                <i class="fas fa-star text-muted"></i>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <blockquote class="mb-2">
                                        <p id="testimonial-preview" class="mb-1">
                                            <?= $is_edit ? htmlspecialchars($testimonial['testimonial']) : 'Isi testimonial akan muncul di sini...' ?>
                                        </p>
                                    </blockquote>
                                    <cite id="author-preview" class="text-muted">
                                        <strong>
                                            <?= $is_edit ? htmlspecialchars($testimonial['nama']) : 'Nama Pemberi Testimonial' ?>
                                        </strong>
                                        <?php if ($is_edit && $testimonial['jabatan']): ?>
                                            - <?= htmlspecialchars($testimonial['jabatan']) ?>
                                            <?php if ($testimonial['institusi']): ?>
                                                , <?= htmlspecialchars($testimonial['institusi']) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </cite>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Preview bagaimana testimonial akan tampil di website</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Testimonial
            </button>
            <a href="<?= BASE_URL ?>landing_admin/testimonials" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Update file input label
$('#foto').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName || 'Pilih foto testimonial...');
});

// Live preview updates
$('input[name="nama"], select[name="jabatan"], input[name="institusi"], textarea[name="testimonial"], select[name="rating"]').on('input change', function() {
    updatePreview();
});

function updatePreview() {
    const nama = $('input[name="nama"]').val() || 'Nama Pemberi Testimonial';
    const jabatan = $('select[name="jabatan"]').val();
    const institusi = $('input[name="institusi"]').val();
    const testimonial = $('textarea[name="testimonial"]').val() || 'Isi testimonial akan muncul di sini...';
    const rating = $('select[name="rating"]').val() || 0;

    $('#testimonial-preview').text(testimonial);

    let authorText = '<strong>' + nama + '</strong>';
    if (jabatan) {
        authorText += ' - ' + jabatan;
        if (institusi) {
            authorText += ', ' + institusi;
        }
    }
    $('#author-preview').html(authorText);

    // Update rating stars
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += '<i class="fas fa-star ' + (i <= rating ? 'text-warning' : 'text-muted') + '"></i>';
    }
    $('#rating-preview').html(starsHtml);
}

// Initialize preview on page load
$(document).ready(function() {
    updatePreview();
});
</script>

<style>
.testimonial-preview blockquote {
    font-style: italic;
    border-left: 4px solid #17a2b8;
    padding-left: 1rem;
    margin: 0;
}

.testimonial-preview blockquote p {
    margin-bottom: 0.5rem;
}

.avatar-preview {
    flex-shrink: 0;
}
</style>

<?php
include __DIR__ . '/../partials/footer.php';
?>