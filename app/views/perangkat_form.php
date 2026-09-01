<?php
// app/views/perangkat_form.php
include __DIR__ . '/partials/header.php';

$is_edit = !empty($dokumen);
$page_title = $is_edit ? "Edit Dokumen" : "Buat Dokumen Baru";
?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> <?= $page_title ?> - <?= $jenis ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>perangkat/index?type=<?= $type ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <form action="<?= BASE_URL ?>perangkat/save" method="post" id="formPerangkat">
                <input type="hidden" name="id_perangkat" value="<?= $dokumen['id_perangkat'] ?? '' ?>">
                <input type="hidden" name="type" value="<?= $type ?>">
                <input type="hidden" name="jenis" value="<?= $jenis ?>">
                
                <div class="card-body">
                    <!-- Meta Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Judul Dokumen <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" 
                                       value="<?= htmlspecialchars($dokumen['judul'] ?? '') ?>" 
                                       placeholder="Contoh: Modul Ajar Matematika Bab 1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Mata Pelajaran</label>
                                <input type="text" name="mapel" class="form-control" 
                                       value="<?= htmlspecialchars($dokumen['mapel'] ?? '') ?>" 
                                       placeholder="Matematika">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Kelas</label>
                                <input type="text" name="kelas" class="form-control" 
                                       value="<?= htmlspecialchars($dokumen['kelas'] ?? '') ?>" 
                                       placeholder="X IPA 1">
                            </div>
                        </div>
                    </div>

                    <!-- Template Selector (Only for New Document) -->
                    <?php if (!$is_edit && !empty($templates)): ?>
                    <div class="form-group">
                        <label>Pilih Template (Opsional)</label>
                        <select id="template_selector" class="form-control">
                            <option value="">-- Mulai dari Kosong --</option>
                            <?php foreach ($templates as $tpl): ?>
                                <option value="<?= $tpl['id_template'] ?>">
                                    <?= htmlspecialchars($tpl['nama_template']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Pilih template untuk memulai dengan format yang sudah ada.</small>
                    </div>
                    <?php endif; ?>

                    <!-- Rich Text Editor -->
                    <div class="form-group">
                        <label>Konten Dokumen</label>
                        <textarea name="konten_html" id="editor"><?= $dokumen['konten_html'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="<?= BASE_URL ?>perangkat/index?type=<?= $type ?>" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.tox-notifications-container { display: none !important; }</style>
<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
$(document).ready(function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#editor',
        height: 600,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | ' +
                 'bullist numlist outdent indent | table | removeformat | help',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 12pt; }' +
                       'table { border-collapse: collapse; width: 100%; }' +
                       'table td, table th { border: 1px solid #000; padding: 8px; }',
        toolbar_mode: 'sliding',
        language: 'id_ID',
        branding: false
    });

    // Template Selector Logic
    $('#template_selector').on('change', function() {
        const id_template = $(this).val();
        if (!id_template) return;

        $.getJSON('<?= BASE_URL ?>perangkat/get_template?id_template=' + id_template, function(response) {
            if (response.status === 'success') {
                tinymce.get('editor').setContent(response.konten);
            } else {
                alert('Gagal memuat template: ' + response.message);
            }
        });
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
