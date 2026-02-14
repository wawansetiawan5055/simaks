<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h1 class="m-0"><i class="fas fa-school mr-2"></i> Data Master Kelas</h1>
                <p class="text-muted small mb-0">Pengaturan rombongan belajar (Rombel) berdasarkan tingkat (X, XI, XII).
                </p>
            </div>
            <a href="index.php?mod=kelas&act=form"
                class="btn btn-warning btn-sm px-3 shadow-none font-weight-bold text-white" style="border-radius: 8px;">
                <i class="fas fa-plus mr-1"></i> Tambah Kelas
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php
            $grouped_kelas = [];
            $levels = ['X', 'XI', 'XII'];
            $accent_colors = ['X' => '#3b82f6', 'XI' => '#10b981', 'XII' => '#f59e0b'];
            $bg_colors = ['X' => '#eff6ff', 'XI' => '#f0fdf4', 'XII' => '#fffbeb'];

            foreach ($kelas_list as $k) {
                $grouped_kelas[$k['tingkat']][] = $k;
            }

            foreach ($levels as $tingkat):
                $data_kelas = $grouped_kelas[$tingkat] ?? [];
                $color = $accent_colors[$tingkat];
                $bg = $bg_colors[$tingkat];
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0"
                        style="border-radius: 15px; overflow: hidden; border-top: 4px solid <?= $color ?> !important;">
                        <div class="card-header border-bottom py-3" style="background: <?= $bg ?>;">
                            <h6 class="mb-0 font-weight-bold" style="color: <?= $color ?>; letter-spacing: 1px;">
                                TINGKAT <?= htmlspecialchars($tingkat) ?>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-light">
                                    <tr class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <th class="text-center py-2" style="width: 50px;">NO</th>
                                        <th class="py-2">NAMA KELAS</th>
                                        <th class="text-center py-2" style="width: 90px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($data_kelas)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small"><em>Belum ada data.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($data_kelas as $k): ?>
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++; ?>
                                                </td>
                                                <td class="align-middle font-weight-bold text-dark" style="font-size: 0.85rem;">
                                                    <?= htmlspecialchars($k['nama_kelas']) ?></td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group">
                                                        <a href="index.php?mod=kelas&act=form&id=<?= $k['id_kelas'] ?>"
                                                            class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                            style="background: #fffbeb; width: 26px; height: 26px; border-radius: 6px; color: #d97706;"
                                                            title="Edit"><i class="fas fa-pencil-alt"
                                                                style="font-size: 0.75rem;"></i></a>
                                                        <a href="index.php?mod=kelas&act=delete&id=<?= $k['id_kelas'] ?>"
                                                            class="btn btn-xs btn-outline-danger border-0 p-1"
                                                            style="background: #fef2f2; width: 26px; height: 26px; border-radius: 6px; color: #dc2626;"
                                                            title="Hapus"
                                                            onclick="return confirmDelete(event, 'Hapus kelas ini?')"><i
                                                                class="fas fa-trash-alt" style="font-size: 0.75rem;"></i></a>
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
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>