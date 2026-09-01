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
    .btn-action {
        border-radius: 8px !important;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Koreksi &amp; Penilaian Tugas Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Koreksi Tugas</li>
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
                    <i class="fas fa-clipboard-check text-warning mr-2"></i> Koreksi Tugas Siswa
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Siswa</th>
                                <th class="border-0 py-3">Mata Pelajaran</th>
                                <th class="border-0 py-3">Judul Tugas</th>
                                <th class="border-0 py-3">Tanggal Upload</th>
                                <th class="border-0 py-3">File</th>
                                <th class="border-0 px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pengumpulan)): ?>
                                <?php foreach ($pengumpulan as $p): ?>
                                    <tr>
                                        <td class="px-4 py-3 align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-light text-primary rounded-circle p-2 mr-3" style="background-color: rgba(99, 102, 241, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user-graduate"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-dark d-block"><?php echo htmlspecialchars($p['nama_siswa']); ?></strong>
                                                    <span class="text-muted small">NIS: <?php echo htmlspecialchars($p['nis']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 align-middle"><?php echo htmlspecialchars($p['nama_mapel']); ?></td>
                                        <td class="py-3 align-middle">
                                            <span class="font-weight-bold text-muted"><?php echo htmlspecialchars($p['judul_tugas']); ?></span>
                                        </td>
                                        <td class="py-3 align-middle text-muted small">
                                            <i class="far fa-calendar-alt mr-1"></i> <?php echo date('d/m/Y H:i', strtotime($p['tgl_upload'])); ?>
                                        </td>
                                        <td class="py-3 align-middle">
                                            <?php if ($p['file_siswa']): ?>
                                                <a href="<?php echo BASE_URL . $p['file_siswa']; ?>" target="_blank" class="btn btn-sm btn-outline-info btn-action px-3">
                                                    <i class="fas fa-file-download mr-1"></i> File
                                                </a>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted">No File</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-center">
                                            <a href="<?= BASE_URL ?>lms/koreksi_detail?id=<?php echo $p['id_kumpul']; ?>" class="btn btn-sm btn-primary btn-action px-4 py-2 shadow-sm">
                                                <i class="fas fa-pen-nib mr-2"></i> Koreksi
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-check-double fa-3x mb-3 opacity-2"></i>
                                            <p class="h5">Semua tugas sudah dikoreksi!</p>
                                            <span class="small">Tidak ada tugas yang menunggu penilaian saat ini.</span>
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
