<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-user-tag mr-2"></i> Manajemen Peran</h1>
                <p class="text-muted small mb-0">Kelola definisi peran pengguna untuk mengontrol akses dan tanggung
                    jawab sistem.</p>
            </div>
            <a href="<?= BASE_URL ?>peran/form" class="btn btn-primary btn-sm px-3 shadow-none"
                style="border-radius: 8px;">
                <i class="fas fa-plus mr-1"></i> Tambah Peran Baru
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Notifikasi Pesan Sukses / Error -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;">
                <div class="font-weight-bold my-1" style="font-size: 0.9rem;">
                    <i class="fas fa-check-circle mr-1"></i> <?= $_SESSION['pesan_sukses'] ?>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;">
                <div class="font-weight-bold my-1" style="font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <?= $_SESSION['pesan_error'] ?>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <?php
        $total_peran = count($list_peran);
        $mid_point = ceil($total_peran / 2);
        $chunks = array_chunk($list_peran, $mid_point > 0 ? $mid_point : 1);
        $left_col = $chunks[0] ?? [];
        $right_col = $chunks[1] ?? [];
        ?>

        <div class="row">
            <!-- KARTU KIRI -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden; border-top: 5px solid #6366f1 !important;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-list-ol mr-2 text-primary"></i> DAFTAR PERAN (1)</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc;">
                                <tr class="text-muted">
                                    <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                                    <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA PERAN</th>
                                    <th class="text-center py-2 border-bottom" style="width: 100px; font-size: 0.7rem; letter-spacing: 1px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (!empty($left_col)): ?>
                                    <?php foreach ($left_col as $p): ?>
                                        <?php include __DIR__ . '/peran_row_item.php'; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted italic small">Belum ada data.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- KARTU KANAN -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden; border-top: 5px solid #a855f7 !important;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-list-ol mr-2 text-purple"></i> DAFTAR PERAN (2)</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc;">
                                <tr class="text-muted">
                                    <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                                    <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA PERAN</th>
                                    <th class="text-center py-2 border-bottom" style="width: 100px; font-size: 0.7rem; letter-spacing: 1px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($right_col)): ?>
                                    <?php foreach ($right_col as $p): ?>
                                        <?php include __DIR__ . '/peran_row_item.php'; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-4 text-muted italic small">Belum ada data tambahan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
