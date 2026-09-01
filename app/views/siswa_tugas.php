<?php include __DIR__ . '/partials/header.php'; ?>

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
    .task-item-card {
        border-radius: 12px !important;
        border: 1px solid #edf2f7 !important;
        transition: all 0.3s ease;
    }
    .task-item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .badge-status {
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (TUGAS MANDIRI SISWA)              */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .lms-card {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .lms-card-header {
            padding: 10px 12px !important;
        }
        .lms-card-title {
            font-size: 0.88rem !important;
        }
        .card-body {
            padding: 8px 6px !important;
        }
        .task-item-card {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .task-item-card .card-header {
            padding: 8px 10px 4px !important;
        }
        .task-item-card h5 {
            font-size: 0.82rem !important;
        }
        .badge-status {
            padding: 2px 7px !important;
            font-size: 0.62rem !important;
        }
        .task-item-card .card-body {
            padding: 6px 10px 10px !important;
        }
        .task-item-card .small {
            font-size: 0.68rem !important;
            margin-bottom: 4px !important;
        }
        .task-item-card .btn {
            padding: 6px 10px !important;
            font-size: 0.72rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #3730a3); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Penugasan Mandiri Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Tugas Mandiri</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card lms-card">
            <div class="lms-card-header">
                <h3 class="lms-card-title">
                    <i class="fas fa-tasks text-primary mr-2"></i> Bank Tugas
                </h3>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($tugas)): ?>
                    <div class="row">
                        <?php foreach ($tugas as $t): ?>
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card task-item-card h-100 shadow-sm">
                                    <div class="card-header bg-white border-0 pt-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="font-weight-bold text-dark mb-0"><?php echo htmlspecialchars($t['judul_tugas']); ?></h5>
                                            <?php
                                            $deadline = strtotime($t['deadline']);
                                            $now = time();
                                            $is_overdue = $deadline < $now;
                                            ?>
                                            <?php if ($t['tgl_kumpul']): ?>
                                                <span class="badge-status bg-success text-white shadow-sm">Selesai</span>
                                            <?php elseif ($is_overdue): ?>
                                                <span class="badge-status bg-danger text-white shadow-sm">Terlambat</span>
                                            <?php else: ?>
                                                <span class="badge-status bg-warning text-white shadow-sm">Aktif</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div class="mb-3">
                                            <span class="badge bg-light text-primary border px-2 py-1" style="font-size: 0.8rem;">
                                                <i class="fas fa-bookmark mr-1"></i> <?php echo htmlspecialchars($t['nama_mapel']); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="small text-muted mb-2">
                                            <i class="fas fa-user-tie mr-1"></i> <strong>Guru:</strong> <?php echo htmlspecialchars($t['nama_guru']); ?>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <i class="far fa-calendar-times mr-1"></i> <strong>Deadline:</strong> <?php echo date('d/m/Y H:i', strtotime($t['deadline'])); ?>
                                        </div>
                                        <div class="small text-muted mb-3">
                                            <i class="fas fa-percentage mr-1"></i> <strong>Bobot Nilai:</strong> <?php echo $t['bobot_nilai']; ?>%
                                        </div>

                                        <?php if ($t['instruksi']): ?>
                                            <div class="bg-light p-2 rounded mb-4" style="font-size: 0.8rem; border-left: 3px solid #f59e0b;">
                                                <?php echo nl2br(htmlspecialchars(substr($t['instruksi'], 0, 120))); ?>
                                                <?php if (strlen($t['instruksi']) > 120): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-auto">
                                            <a href="<?= BASE_URL ?>siswa_portal/tugas_submit?id_tugas=<?php echo $t['id_tugas']; ?>" class="btn btn-primary btn-block rounded-pill shadow-sm" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border: none;">
                                                <i class="fas fa-cloud-upload-alt mr-1"></i> Buka / Kerjakan Tugas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tasks fa-4x text-light mb-3"></i>
                        <h5 class="text-muted">Tidak ada tugas yang tersedia saat ini.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
