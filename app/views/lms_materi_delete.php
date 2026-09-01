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
            <div class="col-lg-6">
                <div class="card lms-card shadow-lg">
                    <div class="lms-card-header d-flex align-items-center">
                        <div class="icon-box mr-3 bg-light p-2 rounded">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </div>
                        <h3 class="lms-card-title">Hapus Materi</h3>
                    </div>
                    <div class="card-body p-4 text-center">
                        <?php if ($materi): ?>
                            <div class="mb-4">
                                <i class="fas fa-exclamation-circle text-warning fa-3x mb-3"></i>
                                <h5 class="font-weight-bold">Hapus Materi Ini?</h5>
                                <p class="text-muted">Materi "<strong><?php echo htmlspecialchars($materi['judul_materi']); ?></strong>" akan dihapus secara permanen.</p>
                            </div>

                            <form method="POST" action="">
                                <div class="d-flex justify-content-center">
                                    <a href="<?= BASE_URL ?>lms/materi_list" class="btn btn-light px-4 mr-2" style="border-radius: 10px;">Batal</a>
                                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 10px;">
                                        Ya, Hapus
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="py-4">
                                <p class="text-muted">Data materi tidak ditemukan.</p>
                                <a href="<?= BASE_URL ?>lms/materi_list" class="btn btn-secondary rounded-pill">Kembali</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
