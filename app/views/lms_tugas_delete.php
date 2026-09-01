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
    .warning-box {
        background: #fff7ed;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #ffedd5;
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
            <div class="col-lg-7">
                <div class="card lms-card shadow-lg border-danger">
                    <div class="lms-card-header d-flex align-items-center">
                        <div class="icon-box mr-3 bg-light p-2 rounded">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </div>
                        <h3 class="lms-card-title">Konfirmasi Hapus Tugas</h3>
                    </div>
                    <div class="card-body p-4 text-center">
                        <?php if ($tugas): ?>
                            <div class="warning-box mb-4">
                                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                <h4 class="font-weight-bold text-dark mb-2">Apakah Anda Yakin?</h4>
                                <p class="text-muted">Tindakan ini akan menghapus tugas secara permanen beserta seluruh pengumpulan dari siswa. Data tidak dapat dipulihkan.</p>
                            </div>

                            <div class="text-left bg-light p-4 rounded mb-4" style="border-left: 4px solid #ef4444;">
                                <div class="small text-muted text-uppercase font-weight-bold mb-1">Tugas yang akan dihapus:</div>
                                <h5 class="font-weight-bold mb-2"><?php echo htmlspecialchars($tugas['judul_tugas']); ?></h5>
                                <div class="small text-muted">
                                    <i class="fas fa-bookmark mr-1"></i> <?php echo htmlspecialchars($tugas['nama_mapel'] ?? 'N/A'); ?><br>
                                    <i class="far fa-calendar-alt mr-1"></i> Deadline: <?php echo date('d/m/Y H:i', strtotime($tugas['deadline'])); ?>
                                </div>
                            </div>

                            <form method="POST" action="" class="mt-5">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="<?= BASE_URL ?>lms/tugas_list" class="btn btn-light px-4 py-2 mr-2 font-weight-bold" style="border-radius: 10px;">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-danger px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 10px;">
                                        <i class="fas fa-trash-alt mr-2"></i> Ya, Hapus Tugas Ini
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="py-5">
                                <i class="fas fa-search fa-3x text-light mb-3"></i>
                                <h5 class="text-muted">Tugas tidak ditemukan.</h5>
                                <a href="<?= BASE_URL ?>lms/tugas_list" class="btn btn-secondary mt-3 rounded-pill px-4">Kembali</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
