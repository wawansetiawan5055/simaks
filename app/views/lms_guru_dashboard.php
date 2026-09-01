<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .lms-wrapper {
        padding: 20px 0;
    }
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
        margin: 0;
    }
    .stats-box {
        border-radius: 12px !important;
        transition: all 0.3s ease;
        border: none !important;
    }
    .stats-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .btn-lms {
        border-radius: 10px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem !important;
    }
    .btn-lms-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%) !important;
        border: none !important;
        color: #fff !important;
    }
    .btn-lms-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #fff !important;
    }
    .btn-lms:hover {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Dashboard Pembelajaran Digital (LMS)
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">LMS Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card lms-card">
            <div class="lms-card-header d-flex align-items-center">
                <div class="icon-box mr-3 bg-light p-2 rounded">
                    <i class="fas fa-th-large text-primary"></i>
                </div>
                <h3 class="lms-card-title">Dashboard LMS Guru</h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <!-- Materi Ajar -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-gradient-info stats-box elevation-2">
                            <div class="inner">
                                <h3><?php echo $data['materi_count'] ?? 0; ?></h3>
                                <p class="font-weight-bold">Materi Ajar</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <a href="<?= BASE_URL ?>lms/materi_list" class="small-box-footer py-2">
                                Lihat Semua <i class="fas fa-arrow-circle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Bank Tugas -->
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-gradient-success stats-box elevation-2">
                            <div class="inner">
                                <h3><?php echo $data['tugas_count'] ?? 0; ?></h3>
                                <p class="font-weight-bold">Bank Tugas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <a href="<?= BASE_URL ?>lms/tugas_list" class="small-box-footer py-2">
                                Kelola Tugas <i class="fas fa-arrow-circle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Penilaian -->
                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-gradient-warning stats-box elevation-2 text-white">
                            <div class="inner text-white">
                                <h3><?php echo $data['pengumpulan_pending'] ?? 0; ?></h3>
                                <p class="font-weight-bold">Tugas Menunggu Penilaian</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <a href="<?= BASE_URL ?>lms/koreksi_list" class="small-box-footer py-2">
                                Koreksi Sekarang <i class="fas fa-arrow-circle-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <a href="<?= BASE_URL ?>lms/materi_upload" class="btn btn-lms btn-lms-primary btn-block py-3 shadow-sm">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Materi Baru
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="<?= BASE_URL ?>lms/tugas_create" class="btn btn-lms btn-lms-success btn-block py-3 shadow-sm">
                            <i class="fas fa-plus-circle mr-2"></i> Buat Tugas Baru
                        </a>
                    </div>
                </div>

                <!-- NEW: Monitoring Progres Tugas -->
                <div class="mt-4 pt-3 border-top">
                    <h5 class="font-weight-bold mb-3"><i class="fas fa-chart-pie mr-2 text-primary"></i> Progres Tugas Aktif</h5>
                    <div class="row">
                        <?php 
                        $active_tasks = $data['active_tasks_progress'] ?? [];
                        if(!empty($active_tasks) && is_array($active_tasks)): 
                            foreach($active_tasks as $tp): 
                                $percent = $tp['total_siswa'] > 0 ? round(($tp['total_submit'] / $tp['total_siswa']) * 100) : 0;
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded shadow-sm bg-white" style="border-left: 5px solid #6366f1 !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div style="max-width: 70%;">
                                            <h6 class="font-weight-bold mb-0 text-truncate"><?= htmlspecialchars($tp['judul_tugas']) ?></h6>
                                            <small class="text-muted">Kelas: <?= htmlspecialchars($tp['nama_kelas']) ?></small>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-primary px-2 py-1"><?= $percent ?>%</span>
                                        </div>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $percent ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted"><i class="fas fa-user-check mr-1"></i> <?= $tp['total_submit'] ?> / <?= $tp['total_siswa'] ?> Siswa</small>
                                        <a href="<?= BASE_URL ?>lms/tugas_detail?id=<?= $tp['id_tugas'] ?>" class="btn btn-xs btn-outline-primary px-2">Detail <i class="fas fa-arrow-right ml-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-3">
                                <p class="text-muted italic small">Belum ada tugas aktif yang sedang berjalan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
