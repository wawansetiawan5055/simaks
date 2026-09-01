<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt"></i> Dashboard Portal Guru
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Tahun Ajaran: <?php echo $_SESSION['nama_ta_aktif'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Statistik Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $data['stats']['total_mapel'] ?? 0; ?></h3>
                                    <p>Mata Pelajaran</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo $data['stats']['total_siswa'] ?? 0; ?></h3>
                                    <p>Total Siswa</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $data['stats']['absensi_hari_ini'] ?? 0; ?></h3>
                                    <p>Absensi Hari Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?php echo $data['tugas_pending'] ?? 0; ?></h3>
                                    <p>Tugas Menunggu Penilaian</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <a href="<?= BASE_URL ?>lms/koreksi_list" class="small-box-footer">
                                    Koreksi Sekarang <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Widget Row -->
                    <div class="row">
                        <!-- Jadwal Hari Ini -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-calendar-day"></i> Jadwal Hari Ini
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($data['jadwal_hari_ini'])): ?>
                                        <div class="list-group">
                                             <?php foreach ($data['jadwal_hari_ini'] as $jadwal): ?>
                                                <div class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                        <h6 class="mb-0 font-weight-bold text-dark"><?php echo htmlspecialchars($jadwal['nama_mapel']); ?></h6>
                                                        <span class="badge badge-primary font-weight-bold px-2 py-1" style="font-size: 0.75rem;">
                                                            <?php echo substr($jadwal['jam_mulai'], 0, 5) . ' - ' . substr($jadwal['jam_selesai'], 0, 5); ?>
                                                            <?php if (($jadwal['jp_count'] ?? 1) > 1): ?>
                                                                (<?php echo $jadwal['jp_count']; ?> JP)
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <p class="mb-0 small text-muted"><i class="fas fa-chalkboard mr-1 text-primary"></i> Kelas: <strong><?php echo htmlspecialchars($jadwal['nama_kelas']); ?></strong></p>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada jadwal mengajar hari ini.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Permohonan Izin -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-user-check"></i> Permohonan Izin Siswa
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($data['permohonan_izin'])): ?>
                                        <div class="list-group">
                                            <?php foreach ($data['permohonan_izin'] as $izin): ?>
                                                <div class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($izin['nama_siswa']); ?></h6>
                                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($izin['tgl_izin'])); ?></small>
                                                    </div>
                                                    <p class="mb-1">
                                                        NIS: <?php echo htmlspecialchars($izin['nis']); ?><br>
                                                        Kategori: <?php echo htmlspecialchars($izin['kategori']); ?><br>
                                                        Alasan: <?php echo htmlspecialchars(substr($izin['alasan'], 0, 50)); ?>...
                                                    </p>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= BASE_URL ?>izin/approve?id=<?php echo $izin['id_izin']; ?>" class="btn btn-success">Setujui</a>
                                                        <a href="<?= BASE_URL ?>izin/reject?id=<?php echo $izin['id_izin']; ?>" class="btn btn-danger">Tolak</a>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada permohonan izin pending.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Aksi Cepat</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?= BASE_URL ?>lms/dashboard" class="btn btn-primary btn-block">
                                                <i class="fas fa-graduation-cap"></i><br>LMS Dashboard
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= BASE_URL ?>jurnal_kbm" class="btn btn-info btn-block">
                                                <i class="fas fa-book-open"></i><br>Jurnal KBM
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= BASE_URL ?>absensi_mapel" class="btn btn-warning btn-block">
                                                <i class="fas fa-clipboard-list"></i><br>Absensi Siswa
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= BASE_URL ?>input_nilai" class="btn btn-success btn-block">
                                                <i class="fas fa-chart-line"></i><br>Input Nilai
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>