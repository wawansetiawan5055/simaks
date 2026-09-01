<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .lms-card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        background: #ffffff;
    }
    .lms-card-header {
        background: transparent !important;
        border-bottom: 1px solid #f0f0f0 !important;
        padding: 20px 25px !important;
    }
    .lms-card-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
    }
    .task-info-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #6366f1;
    }
    .form-control-lms {
        border-radius: 10px !important;
        padding: 12px 15px !important;
        border: 1px solid #e2e8f0 !important;
    }
    .btn-lms {
        border-radius: 10px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">LMS System</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card lms-card shadow-lg">
                    <div class="lms-card-header">
                        <h3 class="lms-card-title">Kumpulkan Tugas</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($tugas): ?>
                            <div class="task-info-box mb-4">
                                <h5 class="font-weight-bold text-dark mb-2"><?php echo htmlspecialchars($tugas['judul_tugas']); ?></h5>
                                <div class="row small text-muted">
                                    <div class="col-md-6 mb-1">
                                        <i class="fas fa-bookmark mr-1"></i> <?php echo htmlspecialchars($tugas['nama_mapel'] ?? ''); ?>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <i class="far fa-clock mr-1"></i> Deadline: <?php echo date('d/m/Y H:i', strtotime($tugas['deadline'])); ?>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="text-dark" style="font-size: 0.95rem; line-height: 1.6;">
                                    <strong>Instruksi:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($tugas['instruksi'])); ?>
                                </div>
                            </div>

                            <form action="<?= BASE_URL ?>lms/tugas_submit?id_tugas=<?php echo $tugas['id_tugas']; ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">File Jawaban</label>
                                    <div class="custom-file">
                                        <input type="file" name="file_tugas" class="custom-file-input" id="fileSubmit" required>
                                        <label class="custom-file-label" for="fileSubmit">Pilih file jawaban Anda...</label>
                                    </div>
                                    <small class="text-muted mt-2 d-block">Pilih file sesuai instruksi guru (PDF, Gambar, atau Dokumen).</small>
                                </div>

                                <div class="alert alert-info py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                    <i class="fas fa-info-circle mr-2"></i> Pastikan file yang Anda unggah sudah benar sebelum menekan tombol kirim.
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <a href="<?= BASE_URL ?>lms/tugas_list_siswa" class="text-muted font-weight-bold">Batal</a>
                                    <button type="submit" class="btn btn-lms btn-primary px-5 shadow-sm">
                                        <i class="fas fa-paper-plane mr-2"></i> Kirim Tugas
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h5 class="text-muted">Data tugas tidak ditemukan atau sudah berakhir.</h5>
                                <a href="<?= BASE_URL ?>lms/tugas_list_siswa" class="btn btn-secondary mt-3">Kembali ke Daftar Tugas</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('fileSubmit').addEventListener('change', function(e){
        var fileName = e.target.files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
