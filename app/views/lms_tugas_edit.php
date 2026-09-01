<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .lms-card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        overflow: hidden;
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
        margin: 0;
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
                    <div class="lms-card-header d-flex align-items-center">
                        <div class="icon-box mr-3 bg-light p-2 rounded">
                            <i class="fas fa-edit text-warning"></i>
                        </div>
                        <h3 class="lms-card-title">Edit Tugas</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($tugas): ?>
                            <form action="<?= BASE_URL ?>lms/tugas_edit?id=<?php echo $tugas['id_tugas']; ?>" method="POST">
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <label class="font-weight-bold text-dark small text-uppercase">Judul Tugas</label>
                                        <input type="text" name="judul_tugas" class="form-control form-control-lms" value="<?php echo htmlspecialchars($tugas['judul_tugas']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark small text-uppercase">Bobot Nilai (%)</label>
                                        <input type="number" name="bobot_nilai" class="form-control form-control-lms" value="<?php echo $tugas['bobot_nilai']; ?>" min="1" max="100" required>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark small text-uppercase">Status</label>
                                        <select name="status" class="form-control form-control-lms" required>
                                            <option value="Aktif" <?php echo $tugas['status'] == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="Nonaktif" <?php echo $tugas['status'] == 'Nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <label class="font-weight-bold text-dark small text-uppercase">Deadline Pengumpulan</label>
                                        <input type="datetime-local" name="deadline" class="form-control form-control-lms" value="<?php echo date('Y-m-d\TH:i', strtotime($tugas['deadline'])); ?>" required>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <label class="font-weight-bold text-dark small text-uppercase">Instruksi Pengerjaan</label>
                                        <textarea name="instruksi" class="form-control form-control-lms" rows="6"><?php echo htmlspecialchars($tugas['instruksi']); ?></textarea>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <hr>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="<?= BASE_URL ?>lms/tugas_list" class="text-muted font-weight-bold">Batal</a>
                                            <button type="submit" class="btn btn-lms btn-warning px-5 shadow-sm text-white">
                                                <i class="fas fa-save mr-2"></i> Update Tugas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h5 class="text-muted">Data tugas tidak ditemukan.</h5>
                                <a href="<?= BASE_URL ?>lms/tugas_list" class="btn btn-secondary mt-3">Kembali</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
