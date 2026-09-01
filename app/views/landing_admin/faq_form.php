<?php
// app/views/landing_admin/faq_form.php
$title = "Form FAQ";
$is_edit = isset($faq);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-question-circle mr-2"></i> <?= $is_edit ? 'Edit FAQ' : 'Tambah FAQ' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit FAQ' : 'Tambah FAQ Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/faq_save" method="post">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $faq['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="form-group">
                <label>Pertanyaan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="pertanyaan"
                    value="<?= $is_edit ? htmlspecialchars($faq['pertanyaan']) : '' ?>" required>
                <small class="text-muted">Pertanyaan yang sering ditanyakan</small>
            </div>

            <div class="form-group">
                <label>Jawaban <span class="text-danger">*</span></label>
                <textarea class="form-control" name="jawaban" rows="8" required><?= $is_edit ? htmlspecialchars($faq['jawaban']) : '' ?></textarea>
                <small class="text-muted">Jawaban lengkap dan jelas. Bisa menggunakan HTML sederhana untuk formatting.</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="kategori">
                            <option value="">Pilih Kategori</option>
                            <option value="Pendaftaran" <?= ($is_edit && $faq['kategori'] == 'Pendaftaran') ? 'selected' : '' ?>>Pendaftaran</option>
                            <option value="Biaya" <?= ($is_edit && $faq['kategori'] == 'Biaya') ? 'selected' : '' ?>>Biaya</option>
                            <option value="Kurikulum" <?= ($is_edit && $faq['kategori'] == 'Kurikulum') ? 'selected' : '' ?>>Kurikulum</option>
                            <option value="Fasilitas" <?= ($is_edit && $faq['kategori'] == 'Fasilitas') ? 'selected' : '' ?>>Fasilitas</option>
                            <option value="Ekstrakurikuler" <?= ($is_edit && $faq['kategori'] == 'Ekstrakurikuler') ? 'selected' : '' ?>>Ekstrakurikuler</option>
                            <option value="Administrasi" <?= ($is_edit && $faq['kategori'] == 'Administrasi') ? 'selected' : '' ?>>Administrasi</option>
                            <option value="Akademik" <?= ($is_edit && $faq['kategori'] == 'Akademik') ? 'selected' : '' ?>>Akademik</option>
                            <option value="Umum" <?= ($is_edit && $faq['kategori'] == 'Umum') ? 'selected' : '' ?>>Umum</option>
                        </select>
                        <small class="text-muted">Kategorikan FAQ untuk memudah pencarian</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kontak Tambahan</label>
                        <input type="text" class="form-control" name="kontak"
                            value="<?= $is_edit ? htmlspecialchars($faq['kontak']) : '' ?>"
                            placeholder="Contoh: 08123456789 atau admin@sekolah.sch.id">
                        <small class="text-muted">Nomor telepon atau email untuk follow-up (opsional)</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        <?= (!$is_edit || $faq['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_active">Tampilkan FAQ</label>
                </div>
                <small class="text-muted">FAQ aktif akan ditampilkan di halaman publik</small>
            </div>

            <!-- Preview Section -->
            <div class="form-group">
                <label>Preview FAQ</label>
                <div class="card border-info">
                    <div class="card-body">
                        <div class="faq-preview">
                            <div class="accordion" id="previewAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="previewHeading">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#previewCollapse" aria-expanded="true" aria-controls="previewCollapse">
                                            <i class="fas fa-question-circle text-primary me-2"></i>
                                            <span id="preview-question">
                                                <?= $is_edit ? htmlspecialchars($faq['pertanyaan']) : 'Pertanyaan FAQ akan muncul di sini...' ?>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="previewCollapse" class="accordion-collapse collapse show"
                                         aria-labelledby="previewHeading" data-bs-parent="#previewAccordion">
                                        <div class="accordion-body">
                                            <div class="faq-answer">
                                                <span id="preview-answer">
                                                    <?= $is_edit ? $faq['jawaban'] : 'Jawaban FAQ akan muncul di sini...' ?>
                                                </span>
                                            </div>

                                            <?php if ($is_edit && $faq['kategori']): ?>
                                            <div class="mt-3 pt-3 border-top">
                                                <small class="text-muted">
                                                    <i class="fas fa-tag me-1"></i>
                                                    Kategori:
                                                    <span class="badge bg-light text-dark">
                                                        <?= htmlspecialchars($faq['kategori']) ?>
                                                    </span>
                                                </small>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($is_edit && $faq['kontak']): ?>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    Butuh bantuan lebih lanjut?
                                                    <a href="tel:<?= htmlspecialchars($faq['kontak']) ?>"
                                                       class="text-decoration-none">
                                                        Hubungi kami
                                                    </a>
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Preview bagaimana FAQ akan tampil di website</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan FAQ
            </button>
            <a href="<?= BASE_URL ?>landing_admin/faq" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Live preview updates
$('input[name="pertanyaan"], textarea[name="jawaban"], select[name="kategori"], input[name="kontak"]').on('input change', function() {
    updatePreview();
});

function updatePreview() {
    const pertanyaan = $('input[name="pertanyaan"]').val() || 'Pertanyaan FAQ akan muncul di sini...';
    const jawaban = $('textarea[name="jawaban"]').val() || 'Jawaban FAQ akan muncul di sini...';
    const kategori = $('select[name="kategori"]').val();
    const kontak = $('input[name="kontak"]').val();

    $('#preview-question').text(pertanyaan);
    $('#preview-answer').html(jawaban.replace(/\n/g, '<br>'));

    // Update kategori badge
    const kategoriContainer = $('#previewCollapse .border-top');
    if (kategori) {
        if (kategoriContainer.length === 0) {
            $('#previewCollapse .accordion-body').append(`
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <i class="fas fa-tag me-1"></i>
                        Kategori:
                        <span class="badge bg-light text-dark kategori-badge">${kategori}</span>
                    </small>
                </div>
            `);
        } else {
            kategoriContainer.find('.kategori-badge').text(kategori);
        }
    } else {
        kategoriContainer.remove();
    }

    // Update kontak info
    const kontakContainer = $('#previewCollapse .mt-2');
    if (kontak) {
        if (kontakContainer.length === 0) {
            $('#previewCollapse .accordion-body').append(`
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-phone me-1"></i>
                        Butuh bantuan lebih lanjut?
                        <a href="tel:${kontak}" class="text-decoration-none kontak-link">
                            Hubungi kami
                        </a>
                    </small>
                </div>
            `);
        } else {
            kontakContainer.find('.kontak-link').attr('href', `tel:${kontak}`);
        }
    } else {
        kontakContainer.remove();
    }
}

// Initialize preview on page load
$(document).ready(function() {
    updatePreview();
});
</script>

<style>
.faq-preview .accordion-item {
    border-radius: 8px !important;
    overflow: hidden;
}

.faq-preview .accordion-button {
    background-color: #f8f9fa;
    border: none;
    padding: 1rem;
    font-size: 0.9rem;
}

.faq-preview .accordion-button:not(.collapsed) {
    background-color: #e3f2fd;
    color: #1976d2;
}

.faq-preview .accordion-body {
    padding: 1rem;
    background-color: #fff;
}

.faq-answer {
    line-height: 1.6;
}
</style>

<?php
include __DIR__ . '/../partials/footer.php';
?>