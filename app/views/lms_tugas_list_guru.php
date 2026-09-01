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

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kelola Tugas &amp; Tagihan Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>lms/tugas_create" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
                    <i class="fas fa-plus mr-1"></i> Buat Tugas Baru
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card lms-card">
            <div class="lms-card-header d-flex justify-content-between align-items-center">
                <h3 class="lms-card-title">
                    <i class="fas fa-tasks text-success mr-2"></i> Bank Tugas
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= BASE_URL ?>lms/tugas_create" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Buat Tugas Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Judul Tugas</th>
                                <th class="border-0 py-3">Mata Pelajaran</th>
                                <th class="border-0 py-3">Dibuat Oleh</th>
                                <th class="border-0 py-3">Deadline</th>
                                <th class="border-0 py-3 text-center">Bobot</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tugas)): ?>
                                <?php foreach ($tugas as $t): ?>
                                    <tr>
                                        <td class="px-4 py-3 align-middle font-weight-bold"><?php echo htmlspecialchars($t['judul_tugas']); ?></td>
                                        <td class="py-3 align-middle"><?php echo htmlspecialchars($t['nama_mapel']); ?></td>
                                        <td class="py-3 align-middle small italic"><?php echo htmlspecialchars($t['nama_guru'] ?? 'Admin'); ?></td>
                                        <td class="py-3 align-middle">
                                            <span class="text-muted small">
                                                <i class="far fa-clock mr-1"></i> <?php echo date('d/m/Y H:i', strtotime($t['deadline'])); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 align-middle text-center">
                                            <span class="badge badge-light border text-primary px-2"><?php echo $t['bobot_nilai']; ?>%</span>
                                        </td>
                                        <td class="py-3 align-middle">
                                            <?php if ($t['status'] == 'Aktif'): ?>
                                                <span class="badge badge-success px-2 py-1" style="border-radius: 4px;">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-1" style="border-radius: 4px;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-center">
                                            <?php
                                            // Action buttons only for owner or pure admin
                                            $can_act = isset($can_manage_all) && $can_manage_all;
                                            if (!$can_act && isset($current_guru_id) && $current_guru_id > 0) {
                                                $can_act = ((int)$t['id_guru'] === (int)$current_guru_id);
                                            }
                                            ?>
                                            <?php if ($can_act): ?>
                                            <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                <a href="<?= BASE_URL ?>lms/tugas_detail?id=<?php echo $t['id_tugas']; ?>" class="btn btn-sm btn-outline-info border-0" title="Detail Progres">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>lms/tugas_edit?id=<?php echo $t['id_tugas']; ?>" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>lms/tugas_delete?id=<?php echo $t['id_tugas']; ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Yakin hapus tugas ini?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-lock"></i></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-tasks fa-3x mb-3 opacity-2"></i>
                                            <p>Belum ada tugas yang dibuat.</p>
                                            <a href="<?= BASE_URL ?>lms/tugas_create" class="btn btn-success btn-sm rounded-pill mt-2">Buat tugas pertama</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
