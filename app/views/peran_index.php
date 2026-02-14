<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-user-tag mr-2"></i> Manajemen Peran</h1>
                <p class="text-muted small mb-0">Kelola definisi peran pengguna untuk mengontrol akses dan tanggung
                    jawab sistem.</p>
            </div>
            <a href="index.php?mod=peran&act=form" class="btn btn-primary btn-sm px-3 shadow-none"
                style="border-radius: 8px;">
                <i class="fas fa-plus mr-1"></i> Tambah Peran Baru
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-list mr-2 text-primary"></i> DAFTAR
                    PERAN PENGGUNA</h6>
            </div>
            <div class="card-body p-0">
                <?php
                $total_peran = count($list_peran);
                $mid_point = ceil($total_peran / 2);
                $chunks = array_chunk($list_peran, $mid_point > 0 ? $mid_point : 1);
                $left_col = $chunks[0] ?? [];
                $right_col = $chunks[1] ?? [];
                ?>

                <div class="row no-gutters">
                    <!-- KOLOM KIRI -->
                    <div class="col-md-6 border-right">
                        <table class="table table-bordered table-hover align-middle mb-0" style="border: none;">
                            <thead style="background: #f8fafc;">
                                <tr class="text-muted">
                                    <th class="text-center py-2 border-bottom"
                                        style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                                    <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA
                                        PERAN</th>
                                    <th class="text-center py-2 border-bottom"
                                        style="width: 100px; font-size: 0.7rem; letter-spacing: 1px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (!empty($left_col)): ?>
                                    <?php foreach ($left_col as $p): ?>
                                        <?php include __DIR__ . '/peran_row_item.php'; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small italic">Belum ada data.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- KOLOM KANAN -->
                    <div class="col-md-6">
                        <table class="table table-bordered table-hover align-middle mb-0" style="border: none;">
                            <thead style="background: #f8fafc;">
                                <tr class="text-muted">
                                    <th class="text-center py-2 border-bottom"
                                        style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                                    <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA
                                        PERAN</th>
                                    <th class="text-center py-2 border-bottom"
                                        style="width: 100px; font-size: 0.7rem; letter-spacing: 1px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($right_col)): ?>
                                    <?php foreach ($right_col as $p): ?>
                                        <?php include __DIR__ . '/peran_row_item.php'; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small italic">Belum ada data
                                            tambahan.</td>
                                    </tr>
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