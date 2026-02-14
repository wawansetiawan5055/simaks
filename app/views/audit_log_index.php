<?php include __DIR__.'/partials/header.php'; ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-history mr-2"></i> Audit Log Aktivitas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Manajemen Sistem</a></li>
                    <li class="breadcrumb-item active">Audit Log</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        <!-- CARD FILTER -->
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filter Data Log</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <form action="index.php" method="GET">
                    <input type="hidden" name="mod" value="audit_log">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user" class="form-control select2">
                                    <option value="">-- Semua User --</option>
                                    <?php foreach($list_users as $u): ?>
                                        <option value="<?= $u['id_pengguna'] ?>" <?= ($filters['id_pengguna'] == $u['id_pengguna']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($u['nama_pengguna']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jenis Aksi</label>
                                <select name="aksi" class="form-control">
                                    <option value="">-- Semua Aksi --</option>
                                    <?php foreach($list_aksi as $ax): ?>
                                        <option value="<?= $ax ?>" <?= ($filters['aksi'] == $ax) ? 'selected' : '' ?>>
                                            <?= $ax ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Dari Tanggal</label>
                                <input type="date" name="start" class="form-control" value="<?= $filters['tanggal_mulai'] ?>">
                            </div>
                        </div>
                         <div class="col-md-2">
                            <div class="form-group">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end" class="form-control" value="<?= $filters['tanggal_akhir'] ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- CARD TABEL LOG -->
        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped text-nowrap">
                    <thead class="bg-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Deskripsi / Detail</th>
                            <th>Tabel</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data log aktivitas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($logs as $log): ?>
                            <tr>
                                <td style="width: 150px;">
                                    <small><i class="far fa-clock text-muted"></i> <?= date('d M Y H:i', strtotime($log['waktu'])) ?></small>
                                </td>
                                <td>
                                    <?php if($log['id_pengguna'] == 0): ?>
                                        <span class="badge badge-secondary">System / Guest</span>
                                    <?php else: ?>
                                        <strong><?= htmlspecialchars($log['nama_pengguna'] ?? 'Unknown ID:'.$log['id_pengguna']) ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $badges = [
                                            'LOGIN' => 'success',
                                            'LOGOUT' => 'secondary',
                                            'CREATE' => 'primary',
                                            'UPDATE' => 'info',
                                            'DELETE' => 'danger',
                                            'ACCESS_DENIED' => 'warning'
                                        ];
                                        $bg = $badges[$log['aksi']] ?? 'light';
                                    ?>
                                    <span class="badge badge-<?= $bg ?>"><?= $log['aksi'] ?></span>
                                </td>
                                <td style="white-space: normal; max-width: 400px;">
                                    <?= htmlspecialchars($log['deskripsi']) ?>
                                </td>
                                <td>
                                    <?php if($log['target_tabel']): ?>
                                        <code><?= $log['target_tabel'] ?></code>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= $log['ip_address'] ?></small><br>
                                    <!-- Tooltip User Agent -->
                                    <span class="text-xs text-muted" title="<?= htmlspecialchars($log['user_agent']) ?>">
                                        <i class="fas fa-desktop"></i> Info Device
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer clearfix">
                <div class="float-left">
                    Total: <strong><?= $total_logs ?></strong> aktivitas
                </div>
                <ul class="pagination pagination-sm m-0 float-right">
                    <?php if($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?mod=audit_log&page=<?= $page-1 ?>&user=<?= $filters['id_pengguna'] ?>&aksi=<?= $filters['aksi'] ?>">&laquo;</a></li>
                    <?php endif; ?>
                    
                    <li class="page-item active"><a class="page-link" href="#"><?= $page ?></a></li>
                    
                    <?php if($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?mod=audit_log&page=<?= $page+1 ?>&user=<?= $filters['id_pengguna'] ?>&aksi=<?= $filters['aksi'] ?>">&raquo;</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
    </div>
</section>

<?php include __DIR__.'/partials/footer.php'; ?>
