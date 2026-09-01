<?php
// File: app/views/ai_generator_edit.php
include __DIR__ . '/partials/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-edit mr-2 text-primary"></i> Edit Dokumen Pembelajaran
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>ai_generator">AI Generator</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <form action="<?= BASE_URL ?>ai_generator/update" method="POST" id="formEdit">
                <input type="hidden" name="id_log" value="<?= $log['id_log'] ?>">
                
                <div class="card-header">
                    <h3 class="card-title">Edit Draft Perangkat: <?= htmlspecialchars($log['jenis_perangkat']) ?></h3>
                </div>
                
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="docJudul" class="font-weight-bold">Judul Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="docJudul" class="form-control font-weight-bold" 
                               value="<?= htmlspecialchars($log['judul']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="aiContent" class="font-weight-bold">Konten Dokumen</label>
                        <textarea id="aiContent" name="konten_html" class="summernote"><?= htmlspecialchars($log['konten_html']) ?></textarea>
                    </div>
                </div>
                
                <div class="card-footer text-right">
                    <a href="<?= BASE_URL ?>ai_generator" class="btn btn-secondary mr-2">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Summernote Script -->
<script>
$(document).ready(function() {
    $('#aiContent').summernote({
        height: 600,
        placeholder: 'Tulis konten dokumen di sini...',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    $('#formEdit').on('submit', function(e) {
        var judul = $('#docJudul').val().trim();
        var konten = $('#aiContent').summernote('code');
        if (!judul) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Judul Kosong', text: 'Harap isi judul dokumen.', confirmButtonColor: '#0d6efd' });
            return;
        }
        if (!konten || konten.trim() === '<p><br></p>') {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Konten Kosong', text: 'Dokumen tidak dapat disimpan karena konten kosong.', confirmButtonColor: '#0d6efd' });
            return;
        }
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
