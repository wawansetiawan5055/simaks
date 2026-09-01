<?php
// app/views/template_dokumen_index.php
include __DIR__ . '/partials/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-code"></i> Manajemen Template Dokumen
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTemplate">
                        <i class="fas fa-plus"></i> Tambah Template
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($template_list)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                        <p>Belum ada template.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover" id="tableTemplate">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Nama Template</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($template_list as $i => $tpl): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($tpl['jenis']) ?></span></td>
                                    <td><strong><?= htmlspecialchars($tpl['nama_template']) ?></strong></td>
                                    <td>
                                        <?php if ($tpl['is_active']): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning btn-edit" 
                                                    data-id="<?= $tpl['id_template'] ?>"
                                                    data-jenis="<?= htmlspecialchars($tpl['jenis']) ?>"
                                                    data-nama="<?= htmlspecialchars($tpl['nama_template']) ?>"
                                                    data-active="<?= $tpl['is_active'] ?>"
                                                    data-konten="<?= htmlspecialchars($tpl['konten_html']) ?>"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>template_dokumen/delete?id=<?= $tpl['id_template'] ?>" 
                                               class="btn btn-danger" 
                                               onclick="return confirmDelete(event)" 
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Template -->
<div class="modal fade" id="modalTemplate" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>template_dokumen/save" method="post" id="formTemplate">
                <input type="hidden" name="id_template" id="id_template">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTemplateLabel">Tambah Template</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jenis Dokumen <span class="text-danger">*</span></label>
                                <select name="jenis" id="jenis" class="form-control" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="ATP">ATP</option>
                                    <option value="Modul Ajar">Modul Ajar</option>
                                    <option value="Prosem">Prosem</option>
                                    <option value="Prota">Prota</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Template <span class="text-danger">*</span></label>
                                <input type="text" name="nama_template" id="nama_template" class="form-control" 
                                       placeholder="Contoh: Template Modul Ajar Kurikulum Merdeka" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Konten Template</label>
                        <textarea name="konten_html" id="editor_template"></textarea>
                        <small class="text-muted">Desain format dokumen dasar. Gunakan [ISI] sebagai placeholder untuk guru.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.tox-notifications-container { display: none !important; }</style>
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
$(document).ready(function() {
    $('#tableTemplate').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Initialize TinyMCE when modal opens
    $('#modalTemplate').on('shown.bs.modal', function () {
        if (!tinymce.get('editor_template')) {
            tinymce.init({
                selector: '#editor_template',
                height: 400,
                menubar: true,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'charmap',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | ' +
                         'bullist numlist | table | code | removeformat',
                content_style: 'body { font-family: Arial, sans-serif; font-size: 12pt; }' +
                               'table { border-collapse: collapse; width: 100%; }' +
                               'table td, table th { border: 1px solid #000; padding: 8px; }',
                branding: false
            });
        }
    });

    // Reset modal on close
    $('#modalTemplate').on('hidden.bs.modal', function () {
        var form = $(this).find('form')[0];
        form.reset();
        $('#modalTemplateLabel').text('Tambah Template');
        $('#id_template').val('');
        if (tinymce.get('editor_template')) {
            tinymce.get('editor_template').setContent('');
        }
    });

    // Edit button
    $(document).on('click', '.btn-edit', function() {
        var btn = $(this);
        $('#modalTemplateLabel').text('Edit Template');
        $('#id_template').val(btn.data('id'));
        $('#jenis').val(btn.data('jenis'));
        $('#nama_template').val(btn.data('nama'));
        $('#is_active').val(btn.data('active'));
        
        // Wait for TinyMCE to be ready
        setTimeout(function() {
            if (tinymce.get('editor_template')) {
                tinymce.get('editor_template').setContent(btn.data('konten'));
            }
        }, 300);
        
        $('#modalTemplate').modal('show');
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
