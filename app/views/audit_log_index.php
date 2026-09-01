<?php include __DIR__.'/partials/header.php'; ?>

<style>
    .audit-stat-card {
        border-radius: 14px;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .audit-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .audit-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .badge-device {
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }
    .ip-badge {
        font-family: monospace;
        font-size: 0.76rem;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 2px 6px;
        border-radius: 4px;
        color: #0f172a;
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #4338ca); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Audit Log &amp; Riwayat Aktivitas
                    </h4>
                    <small class="text-muted">Pantau autentikasi login, perubahan data, perangkat (device), dan IP pengguna secara transparan</small>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-indigo font-weight-bold">Audit Log</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- 4 STATISTIK RINGKASAN LOG -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6 mb-2">
                <div class="card audit-stat-card shadow-sm h-100 bg-white">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="audit-stat-icon bg-indigo text-white mr-3">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">Total Aktivitas</div>
                            <h4 class="font-weight-bold text-dark mb-0"><?= number_format($summary_stats['total_all'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <div class="card audit-stat-card shadow-sm h-100 bg-white">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="audit-stat-icon bg-success text-white mr-3">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">Login Hari Ini</div>
                            <h4 class="font-weight-bold text-success mb-0"><?= number_format($summary_stats['login_today'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <div class="card audit-stat-card shadow-sm h-100 bg-white">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="audit-stat-icon bg-warning text-white mr-3">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">Operasi Data Hari Ini</div>
                            <h4 class="font-weight-bold text-warning mb-0"><?= number_format($summary_stats['data_changes_today'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <div class="card audit-stat-card shadow-sm h-100 bg-white">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="audit-stat-icon bg-info text-white mr-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">User Aktif Hari Ini</div>
                            <h4 class="font-weight-bold text-info mb-0"><?= number_format($summary_stats['unique_users_today'] ?? 0) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD FILTER & PENCARIAN -->
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px; background: #ffffff;">
            <div class="card-body py-3 px-3">
                <form action="<?= BASE_URL ?>index.php" method="GET" class="m-0">
                    <input type="hidden" name="mod" value="audit_log">
                    
                    <div class="row align-items-end">
                        
                        <!-- PENCARIAN KATA KUNCI -->
                        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                            <label class="small font-weight-bold text-dark mb-1"><i class="fas fa-search mr-1 text-muted"></i> Kata Kunci</label>
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama, username, aktivitas, IP..." value="<?= htmlspecialchars($filters['q']) ?>">
                        </div>

                        <!-- FILTER USER -->
                        <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                            <label class="small font-weight-bold text-dark mb-1"><i class="fas fa-user mr-1 text-muted"></i> Pengguna</label>
                            <select name="user" class="form-control form-control-sm select2">
                                <option value="">-- Semua Pengguna --</option>
                                <?php foreach($list_users as $u): ?>
                                    <?php $r_label = !empty($u['roles']) ? ' (' . $u['roles'] . ')' : ''; ?>
                                    <option value="<?= $u['id_pengguna'] ?>" <?= ($filters['id_pengguna'] == $u['id_pengguna']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['nama_lengkap']) ?><?= $r_label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- FILTER AKSI -->
                        <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                            <label class="small font-weight-bold text-dark mb-1"><i class="fas fa-bolt mr-1 text-muted"></i> Jenis Aksi</label>
                            <select name="aksi" class="form-control form-control-sm">
                                <option value="">-- Semua Aksi --</option>
                                <?php foreach($list_aksi as $ax): ?>
                                    <option value="<?= $ax ?>" <?= ($filters['aksi'] == $ax) ? 'selected' : '' ?>>
                                        <?= $ax ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- FILTER TANGGAL MULAI -->
                        <div class="col-lg-2 col-md-4 mb-2 mb-lg-0">
                            <label class="small font-weight-bold text-dark mb-1"><i class="fas fa-calendar mr-1 text-muted"></i> Dari Tanggal</label>
                            <input type="date" name="start" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['tanggal_mulai']) ?>">
                        </div>

                        <!-- TOMBOL FILTER -->
                        <div class="col-lg-2 col-md-4 mb-2 mb-lg-0 d-flex" style="gap: 5px;">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill font-weight-bold shadow-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <a href="<?= BASE_URL ?>index.php?mod=audit_log" class="btn btn-light btn-sm border" title="Reset Filter">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- CARD TABEL LOG AKTIVITAS -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="font-weight-bold text-dark small">
                    <i class="fas fa-list-alt text-indigo mr-1.5"></i>
                    Daftar Log Aktivitas Sistem
                    <span class="badge badge-light border ml-2"><?= number_format($total_logs) ?> Baris Ditemukan</span>
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold px-2 py-1" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Cetak Log
                    </button>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-nowrap align-middle" style="font-size: 0.82rem;">
                    <thead class="bg-light">
                        <tr class="text-muted" style="font-size: 0.73rem; letter-spacing: 0.5px;">
                            <th style="width: 140px;" class="pl-3">WAKTU</th>
                            <th style="width: 180px;">PENGGUNA &amp; PERAN</th>
                            <th style="width: 110px;" class="text-center">AKSI</th>
                            <th>AKTIVITAS &amp; DESKRIPSI</th>
                            <th style="width: 100px;">MODUL / TABEL</th>
                            <th style="width: 220px;" class="pr-3">IP &amp; PERANGKAT (DEVICE)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-3x text-muted mb-3 d-block opacity-40"></i>
                                    <strong>Tidak ada data log yang sesuai dengan filter.</strong>
                                    <div class="small mt-1">Coba ubah kata kunci atau rentang tanggal pencarian Anda.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($logs as $log): 
                                $uaInfo = function_exists('parse_user_agent') ? parse_user_agent($log['user_agent']) : ['label' => $log['user_agent'], 'icon' => 'fas fa-desktop'];
                                $aksi = strtoupper($log['aksi'] ?? '');
                                
                                // Badge & Icon Aksi
                                $badgeClass = 'badge-secondary';
                                $aksiIcon = 'fas fa-circle';
                                if ($aksi === 'LOGIN') {
                                    $badgeClass = 'badge-success';
                                    $aksiIcon = 'fas fa-sign-in-alt';
                                } elseif ($aksi === 'LOGIN_QR') {
                                    $badgeClass = 'badge-info';
                                    $aksiIcon = 'fas fa-qrcode';
                                } elseif ($aksi === 'LOGOUT') {
                                    $badgeClass = 'badge-secondary';
                                    $aksiIcon = 'fas fa-sign-out-alt';
                                } elseif ($aksi === 'CREATE' || $aksi === 'INSERT') {
                                    $badgeClass = 'badge-primary';
                                    $aksiIcon = 'fas fa-plus-circle';
                                } elseif ($aksi === 'UPDATE') {
                                    $badgeClass = 'badge-warning text-dark';
                                    $aksiIcon = 'fas fa-edit';
                                } elseif ($aksi === 'DELETE') {
                                    $badgeClass = 'badge-danger';
                                    $aksiIcon = 'fas fa-trash';
                                } elseif ($aksi === 'IMPORT') {
                                    $badgeClass = 'badge-purple text-white';
                                    $aksiIcon = 'fas fa-file-import';
                                } elseif ($aksi === 'ACCESS_DENIED' || $aksi === 'LOGIN_FAILED') {
                                    $badgeClass = 'badge-danger';
                                    $aksiIcon = 'fas fa-shield-alt';
                                }
                            ?>
                            <tr>
                                <!-- KOLOM 1: WAKTU -->
                                <td class="align-middle pl-3">
                                    <div class="font-weight-bold text-dark" style="font-size: 0.80rem;">
                                        <?= date('d M Y H:i:s', strtotime($log['waktu'])) ?>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="far fa-clock mr-1"></i><?= function_exists('time_elapsed_string') ? time_elapsed_string($log['waktu']) : '' ?>
                                    </small>
                                </td>

                                <!-- KOLOM 2: PENGGUNA & PERAN -->
                                <td class="align-middle">
                                    <?php if ($log['id_pengguna'] == 0): ?>
                                        <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-robot mr-1"></i> System / Guest</span>
                                    <?php else: ?>
                                        <div class="font-weight-bold text-dark" style="font-size: 0.82rem;">
                                            <?= htmlspecialchars($log['nama_lengkap'] ?? $log['nama_pengguna']) ?>
                                        </div>
                                        <div class="d-flex align-items-center mt-0.5" style="gap: 4px;">
                                            <?php if (!empty($log['username'])): ?>
                                                <small class="text-muted font-italic">@<?= htmlspecialchars($log['username']) ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($log['roles'])): ?>
                                                <span class="badge badge-light border text-primary" style="font-size: 0.65rem;"><?= htmlspecialchars($log['roles']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- KOLOM 3: JENIS AKSI -->
                                <td class="align-middle text-center">
                                    <span class="badge <?= $badgeClass ?> px-2.5 py-1 font-weight-bold shadow-xs" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                        <i class="<?= $aksiIcon ?> mr-1"></i> <?= $aksi ?>
                                    </span>
                                </td>

                                <!-- KOLOM 4: DESKRIPSI -->
                                <td class="align-middle" style="white-space: normal; min-width: 250px; max-width: 450px;">
                                    <div class="text-dark" style="line-height: 1.35;">
                                        <?= htmlspecialchars($log['deskripsi']) ?>
                                    </div>
                                </td>

                                <!-- KOLOM 5: TARGET TABEL -->
                                <td class="align-middle">
                                    <?php if (!empty($log['target_tabel'])): ?>
                                        <code class="px-1.5 py-0.5 bg-light border rounded text-indigo font-weight-bold" style="font-size: 0.74rem;">
                                            <?= htmlspecialchars($log['target_tabel']) ?>
                                        </code>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- KOLOM 6: IP & PERANGKAT (DEVICE) -->
                                <td class="align-middle pr-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="ip-badge mr-1.5" title="Alamat IP Client">
                                            <i class="fas fa-network-wired text-muted mr-1"></i><?= htmlspecialchars($log['ip_address'] ?? '0.0.0.0') ?>
                                        </span>
                                    </div>
                                    <div class="badge-device" title="User Agent: <?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                                        <i class="<?= $uaInfo['icon'] ?>"></i>
                                        <span><?= htmlspecialchars($uaInfo['label']) ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- PAGINATION -->
            <div class="card-footer bg-white py-2.5 px-3 border-top clearfix">
                <div class="float-left small text-muted pt-1">
                    Menampilkan halaman <strong><?= $page ?></strong> dari <strong><?= max(1, $total_pages) ?></strong> (Total: <strong><?= number_format($total_logs) ?></strong> data)
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php 
                        $queryParams = http_build_query([
                            'mod' => 'audit_log',
                            'q' => $filters['q'],
                            'user' => $filters['id_pengguna'],
                            'aksi' => $filters['aksi'],
                            'start' => $filters['tanggal_mulai'],
                            'end' => $filters['tanggal_akhir']
                        ]);
                        ?>
                        
                        <?php if($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?<?= $queryParams ?>&page=1" title="Awal">&laquo;&laquo;</a></li>
                            <li class="page-item"><a class="page-link" href="?<?= $queryParams ?>&page=<?= $page-1 ?>">&laquo; Prev</a></li>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($total_pages, $page + 2);
                        for($p = $startPage; $p <= $endPage; $p++):
                        ?>
                            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= $queryParams ?>&page=<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?<?= $queryParams ?>&page=<?= $page+1 ?>">Next &raquo;</a></li>
                            <li class="page-item"><a class="page-link" href="?<?= $queryParams ?>&page=<?= $total_pages ?>" title="Akhir">&raquo;&raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</section>

<?php include __DIR__.'/partials/footer.php'; ?>

