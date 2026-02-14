<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-user-graduate"></i> Study Tracer Alumni</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php // Session messages handled by toast notifications ?>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Study Tracer Alumni</h3>
                <div class="card-tools">
                    <?php if(can_do($pdo, 'tracer_study', 'create')): ?>
                    <a href="index.php?mod=tracer_study&act=form" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Data Tracer
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filter Section -->
                <form method="GET" action="index.php" class="mb-3">
                    <input type="hidden" name="mod" value="tracer_study">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tahun Lulus</label>
                                <select name="tahun_lulus" class="form-control">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php foreach($available_years as $year): ?>
                                        <option value="<?= $year ?>" <?= ($filters['tahun_lulus'] ?? '') == $year ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">-- Semua Status --</option>
                                    <option value="PTN/PTS" <?= ($filters['status'] ?? '') == 'PTN/PTS' ? 'selected' : '' ?>>PTN/PTS</option>
                                    <option value="Bekerja" <?= ($filters['status'] ?? '') == 'Bekerja' ? 'selected' : '' ?>>Bekerja</option>
                                    <option value="Wirausaha" <?= ($filters['status'] ?? '') == 'Wirausaha' ? 'selected' : '' ?>>Wirausaha</option>
                                    <option value="Lain-lain" <?= ($filters['status'] ?? '') == 'Lain-lain' ? 'selected' : '' ?>>Lain-lain</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="index.php?mod=tracer_study" class="btn btn-secondary btn-block">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table_tracer">
                        <thead>
                            <tr>
                                <th style="width: 10px">No</th>
                                <th>Nama Alumni</th>
                                <th>NISN</th>
                                <th>JK</th>
                                <th>Tahun Lulus</th>
                                <th>Status</th>
                                <th>Institusi/Perusahaan</th>
                                <th>Jurusan/Bidang</th>
                                <th>Kota</th>
                                <?php if(can_do($pdo, 'tracer_study', 'update') || can_do($pdo, 'tracer_study', 'delete')): ?>
                                <th style="width: 100px">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($tracer_list)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada data tracer study</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach($tracer_list as $tracer): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($tracer['nama']) ?></td>
                                    <td><?= htmlspecialchars($tracer['nisn']) ?></td>
                                    <td><?= $tracer['jk'] == 'Laki-laki' ? 'L' : 'P' ?></td>
                                    <td><?= $tracer['tahun_lulus'] ?></td>
                                    <td>
                                        <?php
                                        $badge_class = [
                                            'PTN/PTS' => 'success',
                                            'Bekerja' => 'warning',
                                            'Wirausaha' => 'info',
                                            'Lain-lain' => 'secondary'
                                        ];
                                        $class = $badge_class[$tracer['status_setelah_lulus']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $class ?>">
                                            <?= htmlspecialchars($tracer['status_setelah_lulus']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($tracer['nama_institusi'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($tracer['jurusan_pekerjaan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($tracer['kota'] ?? '-') ?></td>
                                    <?php if(can_do($pdo, 'tracer_study', 'update') || can_do($pdo, 'tracer_study', 'delete')): ?>
                                    <td>
                                        <?php if(can_do($pdo, 'tracer_study', 'update')): ?>
                                        <a href="index.php?mod=tracer_study&act=form&id=<?= $tracer['id_tracer'] ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if(can_do($pdo, 'tracer_study', 'delete')): ?>
                                        <a href="index.php?mod=tracer_study&act=delete&id=<?= $tracer['id_tracer'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirmDelete(event)"
                                           title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
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

<script>
$(function () {
    $('#table_tracer').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[4, 'desc'], [1, 'asc']] // Sort by tahun lulus DESC, nama ASC
    });
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>
