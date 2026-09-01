<?php
// File: app/views/ai_generator_index.php
include __DIR__ . '/partials/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-robot mr-2 text-primary"></i> Penulisan Perangkat AI
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">AI Generator</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Welcome / Alert -->
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-magic"></i> Halo, Guru Hebat!</h5>
            Gunakan fitur AI ini untuk membantu Anda menyusun draf perangkat pembelajaran (ATP, Modul Ajar, dll) secara otomatis sesuai Kurikulum Merdeka.
        </div>

        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Riwayat Generasi Dokumen</h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>ai_generator/create" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Buat Dokumen Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Jenis Perangkat</th>
                                <th>Judul / Topik</th>
                                <th>Semester</th>
                                <th>Tanggal Buat</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                            <p>Belum ada riwayat dokumen AI. Mari mulai buat sekarang!</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($logs as $l): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <span class="badge badge-info px-2 py-1">
                                                <?= htmlspecialchars($l['jenis_perangkat']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($l['judul']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($l['nama_ta'] ?? '-') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($l['created_at'])) ?></td>
                                        <td class="text-center">
                                             <div class="btn-group">
                                                 <button class="btn btn-sm btn-outline-info btn-preview" 
                                                         data-id="<?= $l['id_log'] ?>" title="Pratinjau Dokumen">
                                                     <i class="fas fa-eye"></i>
                                                 </button>
                                                 <a href="<?= BASE_URL ?>ai_generator/publish_lms?id=<?= $l['id_log'] ?>" 
                                                    class="btn btn-sm btn-outline-success" title="Jadikan Materi LMS">
                                                      <i class="fas fa-graduation-cap"></i>
                                                  </a>
                                                  <a href="<?= BASE_URL ?>ai_generator/export?id=<?= $l['id_log'] ?>" 
                                                     class="btn btn-sm btn-outline-danger" title="Ekspor ke PDF">
                                                      <i class="fas fa-file-pdf"></i>
                                                  </a>
                                                  <a href="<?= BASE_URL ?>ai_generator/delete?id=<?= $l['id_log'] ?>" 
                                                     class="btn btn-sm btn-outline-secondary" 
                                                     onclick="return confirm('Hapus riwayat ini?')" title="Hapus">
                                                      <i class="fas fa-trash"></i>
                                                  </a>
                                             </div>
                                         </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PREVIEW MODAL -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalPreviewTitle">Pratinjau Dokumen</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 720px; overflow: hidden;">
                <div id="modalPreviewBody" style="width: 100%; height: 100%;">
                    <!-- Iframe will be injected here -->
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
                <a id="btnPreviewPdf" href="#" class="btn btn-danger font-weight-bold" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-preview').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var title = $(this).closest('tr').find('strong').text() || 'Pratinjau PDF';
        var pdfUrl = '<?= BASE_URL ?>ai_generator/export?id=' + id + '&inline=1';
        
        $('#modalPreviewTitle').text(title);
        $('#modalPreviewBody').html('<iframe src="' + pdfUrl + '" style="width: 100%; height: 100%; border: none;"></iframe>');
        $('#btnPreviewPdf').attr('href', '<?= BASE_URL ?>ai_generator/export?id=' + id);
        $('#modalPreview').modal('show');
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
