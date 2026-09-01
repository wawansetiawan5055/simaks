<?php 
// app/views/cbt_dashboard.php
// Full Overhaul Dashboard Monitoring Asesmen CBT & Matriks Kesiapan Naskah

include __DIR__ . '/partials/header.php'; 
?>

<style>
    .cbt-dash-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
        flex-shrink: 0;
    }
    .cbt-hero-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
        color: #ffffff;
        border-radius: 18px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 8px 24px rgba(30, 27, 75, 0.2);
    }
    .cbt-hero-banner::after {
        content: '';
        position: absolute;
        bottom: -40px;
        right: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.3), transparent 70%);
        border-radius: 50%;
    }
    .cbt-stat-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .cbt-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.06);
    }
    .table-matrix-header th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
        border-top: none !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        padding: 12px 14px !important;
    }
    .btn-gradient-indigo {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }
    .btn-gradient-indigo:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }
</style>

<div class="content-header p-0 pt-3 mb-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4 flex-wrap" style="gap: 12px;">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5, #3730a3); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-desktop"></i>
                </div>
                <div>
                    <h2 class="m-0 font-weight-bold text-dark" style="font-size: 1.65rem; letter-spacing: -0.5px;">
                        Dashboard Asesmen CBT
                    </h2>
                    <p class="text-muted small mb-0 mt-0.5 font-weight-500">Pusat kendali ujian berbasis komputer, bank soal, dan live monitoring peserta.</p>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <a href="<?= BASE_URL ?>cbt_bank_soal" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold px-3 shadow-sm">
                    <i class="fas fa-database mr-1"></i> Master Bank Soal
                </a>
                <a href="<?= BASE_URL ?>cbt_paket" class="btn btn-outline-warning btn-sm rounded-pill font-weight-bold px-3 shadow-sm">
                    <i class="fas fa-boxes mr-1"></i> Paket Naskah
                </a>
                <a href="<?= BASE_URL ?>cbt_jadwal" class="btn btn-gradient-indigo btn-sm rounded-pill font-weight-bold px-3 shadow-sm">
                    <i class="fas fa-calendar-alt mr-1"></i> Agenda Ujian
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- HERO OVERVIEW BANNER -->
        <div class="card cbt-hero-banner mb-4">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-md-0">
                    <span class="badge text-white font-weight-bold px-2.5 py-1 mb-2 text-uppercase d-inline-block" style="background: rgba(255,255,255,0.15); letter-spacing: 0.5px; font-size: 0.72rem; border-radius: 6px;">
                        <i class="fas fa-shield-alt mr-1"></i> Computer Based Test Management System
                    </span>
                    <h4 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">
                        Pusat Kendali Asesmen &amp; Ujian Sekolah
                    </h4>
                    <p class="text-light small mb-0" style="opacity: 0.9;">
                        Memantau kesiapan naskah bank soal, perakitan paket terpadu, dan live monitoring pelaksanaan ujian serempak sekolah.
                    </p>
                </div>
                <div class="col-md-4 text-md-right">
                    <div class="d-inline-block text-left p-3 rounded-lg" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(6px); border-radius: 14px;">
                        <div class="small text-light" style="opacity: 0.85; font-size: 0.76rem;">Status Ujian Aktif:</div>
                        <div class="h3 font-weight-bold text-warning mb-0 mt-0.5" style="font-family: 'Poppins', sans-serif;">
                            <?= (int)($stats['ujian_aktif'] ?? 0) ?> <span class="small font-weight-normal text-white" style="font-size: 0.85rem;">Jadwal Berlangsung</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 STAT METRICS CARDS -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                <a href="index.php?mod=cbt_bank_soal" class="text-decoration-none">
                    <div class="cbt-stat-card h-100" style="border-left: 4px solid #4f46e5;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Koleksi Bank Soal</span>
                            <h3 class="font-weight-bold text-dark mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= (int)($stats['total_soal'] ?? 0) ?> <span class="small text-muted" style="font-size: 0.8rem;">Butir</span>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;"><?= (int)($stats['total_bank'] ?? 0) ?> Wadah Mapel Terdaftar</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                <a href="index.php?mod=cbt_paket" class="text-decoration-none">
                    <div class="cbt-stat-card h-100" style="border-left: 4px solid #d97706;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Paket Naskah Ujian</span>
                            <h3 class="font-weight-bold text-dark mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= (int)($stats['total_paket'] ?? 0) ?> <span class="small text-muted" style="font-size: 0.8rem;">Naskah</span>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Siap Digunakan Jadwal</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #fffbeb; color: #d97706; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                <a href="index.php?mod=cbt_jadwal" class="text-decoration-none">
                    <div class="cbt-stat-card h-100" style="border-left: 4px solid #16a34a;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Komposisi HOTS (L3)</span>
                            <h3 class="font-weight-bold text-dark mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= (int)($stats['total_hots'] ?? 0) ?> <span class="small text-muted" style="font-size: 0.8rem;">Butir</span>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Standar Penalaran Tinggi</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-brain"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <a href="index.php?mod=cbt_hasil" class="text-decoration-none">
                    <div class="cbt-stat-card h-100" style="border-left: 4px solid #8b5cf6;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Siswa Selesai Ujian</span>
                            <h3 class="font-weight-bold text-dark mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= (int)($stats['total_selesai'] ?? 0) ?> <span class="small text-muted" style="font-size: 0.8rem;">Siswa</span>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Nilai Terekam di Server</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #f5f3ff; color: #7c3aed; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <!-- MATRIKS KESIAPAN NASKAH SELURUH MATA PELAJARAN (KOLOM KIRI 8/12) -->
            <div class="col-lg-8 col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clipboard-check text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Matriks Kesiapan Naskah Mata Pelajaran Sekolah
                            </h6>
                        </div>
                        <input type="text" class="form-control form-control-sm" placeholder="Cari nama mapel..." style="border-radius: 20px; width: 180px;" oninput="filterMatrixTable(this.value)">
                    </div>

                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-hover mb-0" style="font-family: 'Poppins', sans-serif;">
                            <thead class="table-matrix-header sticky-top bg-white">
                                <tr>
                                    <th style="width: 35px;">#</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Kelas X</th>
                                    <th class="text-center">Kelas XI</th>
                                    <th class="text-center">Kelas XII</th>
                                    <th class="text-center">Total Soal</th>
                                    <th class="text-center">Paket</th>
                                    <th class="text-center">Status Kesiapan</th>
                                    <th class="text-center" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mapel_readiness_list)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">Belum ada data mata pelajaran.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mapel_readiness_list as $idx => $m): ?>
                                    <?php 
                                        $tot_s = (int)($m['total_soal'] ?? 0);
                                        $tot_p = (int)($m['total_paket'] ?? 0);
                                        $status_ready = ($tot_s >= 20 && $tot_p > 0);
                                        $status_draft = ($tot_s > 0 && !$status_ready);
                                    ?>
                                    <tr class="matrix-row" data-search="<?= strtolower(htmlspecialchars($m['nama_mapel'])) ?>">
                                        <td class="font-weight-bold text-muted align-middle"><?= $idx + 1 ?></td>
                                        <td class="align-middle">
                                            <strong class="text-dark d-block" style="font-size: 0.90rem;"><?= htmlspecialchars($m['nama_mapel']) ?></strong>
                                            <?php if ((int)$m['total_hots'] > 0): ?>
                                                <small class="badge badge-soft-purple px-1.5 py-0.5 rounded" style="background: #f5f3ff; color: #7c3aed; font-size: 0.70rem;">
                                                    🔥 <?= (int)$m['total_hots'] ?> HOTS
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle small font-weight-bold">
                                            <?= (int)$m['soal_x'] > 0 ? '<span class="text-dark">' . (int)$m['soal_x'] . '</span>' : '<span class="text-muted">0</span>' ?>
                                        </td>
                                        <td class="text-center align-middle small font-weight-bold">
                                            <?= (int)$m['soal_xi'] > 0 ? '<span class="text-dark">' . (int)$m['soal_xi'] . '</span>' : '<span class="text-muted">0</span>' ?>
                                        </td>
                                        <td class="text-center align-middle small font-weight-bold">
                                            <?= (int)$m['soal_xii'] > 0 ? '<span class="text-dark">' . (int)$m['soal_xii'] . '</span>' : '<span class="text-muted">0</span>' ?>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold text-primary">
                                            <?= $tot_s ?>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold text-dark">
                                            <?= $tot_p ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($status_ready): ?>
                                                <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                    <i class="fas fa-check-circle mr-1"></i> Siap Ujian
                                                </span>
                                            <?php elseif ($status_draft): ?>
                                                <span class="badge badge-warning px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                    <i class="fas fa-edit mr-1"></i> <?= $tot_p === 0 ? 'Belum Rakit' : 'Butir Kurang' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-light border text-muted px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.70rem;">
                                                    <i class="fas fa-clock mr-1"></i> Belum Ada Soal
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if (!empty($m['id_bank'])): ?>
                                                <a href="<?= BASE_URL ?>cbt_bank_soal/detail?id_bank=<?= $m['id_bank'] ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1 font-weight-bold" title="Kelola Butir Soal">
                                                    <i class="fas fa-folder-open"></i> Soal
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>cbt_bank_soal" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1" title="Buka Bank">
                                                    <i class="fas fa-plus"></i> Buat
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- AGENDA UJIAN TERBARU & MONITORING PELAKSANAAN (KOLOM KANAN 4/12) -->
            <div class="col-lg-4 col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-broadcast-tower text-success mr-2"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Monitoring Ujian
                            </h6>
                        </div>
                        <a href="<?= BASE_URL ?>cbt_jadwal" class="small font-weight-bold text-primary">Lihat Semua &rarr;</a>
                    </div>

                    <div class="card-body p-3">
                        <?php if (empty($jadwal_terbaru)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 text-muted opacity-50"></i>
                                <h6 class="font-weight-bold text-dark mb-1">Belum Ada Agenda Ujian</h6>
                                <p class="small text-muted mb-0">Klik tombol di atas untuk membuat jadwal agenda ujian serempak.</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column" style="gap: 12px;">
                                <?php foreach ($jadwal_terbaru as $jd): ?>
                                <?php 
                                    $is_active = ($jd['status'] === 'aktif');
                                    $total_p = (int)($jd['jml_peserta'] ?? 0);
                                    $selesai_p = (int)($jd['jml_selesai'] ?? 0);
                                    $progress = $total_p > 0 ? round(($selesai_p / $total_p) * 100) : 0;
                                ?>
                                <div class="p-3 rounded-lg border bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <strong class="text-dark d-block" style="font-size: 0.90rem;"><?= htmlspecialchars($jd['nama_ujian']) ?></strong>
                                        <span class="badge badge-<?= $is_active ? 'success' : 'secondary' ?> px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                                            <?= ucfirst($jd['status']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mb-2">
                                        <?= htmlspecialchars($jd['nama_mapel'] ?? '-') ?> &bull; Kelas <?= htmlspecialchars($jd['nama_kelas'] ?? '-') ?>
                                    </small>

                                    <!-- PROGRESS BAR -->
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1" style="font-size: 0.74rem;">
                                        <span>Progres Pengerjaan:</span>
                                        <strong><?= $selesai_p ?> / <?= $total_p ?> Siswa (<?= $progress ?>%)</strong>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress ?>%;"></div>
                                    </div>

                                    <div class="mt-2 text-right">
                                        <a href="<?= BASE_URL ?>cbt_peserta?id_jadwal=<?= $jd['id_jadwal'] ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 font-weight-bold">
                                            <i class="fas fa-users-cog mr-1"></i> Pantau Peserta
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
function filterMatrixTable(kw) {
    kw = (kw || '').toLowerCase().trim();
    $('.matrix-row').each(function() {
        const text = $(this).data('search') || '';
        if (text.includes(kw)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
