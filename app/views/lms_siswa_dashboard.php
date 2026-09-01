<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    :root {
        --lms-main-bg: #f0f2f5;
        --lms-glass: rgba(255, 255, 255, 0.85);
        --lms-indigo: #6366f1;
        --lms-indigo-dark: #4338ca;
    }

    .content-wrapper {
        background-color: var(--lms-main-bg) !important;
    }

    .lms-content {
        padding: 15px !important;
    }

    /* COMPACT BANNER */
    .lms-welcome-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
        border-radius: 24px;
        padding: 25px 35px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(79, 70, 229, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .lms-welcome-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .lms-welcome-card::after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: 10%;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 30px;
        transform: rotate(45deg);
    }

    .welcome-text h1 {
        font-size: 2.0rem;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -1.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .welcome-text p {
        font-size: 1rem;
        opacity: 0.95;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* TIME CARD STYLE (Adopted from Admin Dashboard) */
    .info-card-clock {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 15px;
        position: relative;
        overflow: hidden;
    }

    .digital-clock {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 5px;
        letter-spacing: -1px;
    }

    .date-display {
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748b;
    }

    .time-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #6366f1;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .time-label i {
        font-size: 0.9rem;
    }

    /* COMPACT STATS */
    .glass-stat-card {
        background: var(--lms-glass);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 25px 25px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 20px;
        height: 100%;
        min-height: 100px;
    }

    .glass-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }

    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .stat-info h3 { font-size: 1.8rem; font-weight: 800; margin: 0; color: #1e293b; line-height: 1.2; }
    .stat-info p { font-size: 0.85rem; font-weight: 600; color: #64748b; margin: 0; white-space: nowrap; }

    /* PROFILE CARD */
    .profile-glass-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        height: 100%;
        text-align: center;
    }

    .profile-photo-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 20px;
        padding: 5px;
        background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
    }

    .profile-info-list {
        text-align: left;
        margin-top: 25px;
    }

    .profile-info-item {
        margin-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 8px;
    }

    .profile-info-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .profile-info-value {
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 600;
    }

    .profile-quote {
        margin-top: 25px;
        font-style: italic;
        color: #64748b;
        background: #f8fafc;
        padding: 15px;
        border-radius: 16px;
        font-size: 0.9rem;
    }

    /* SECTION PLACEHOLDER */
    .content-placeholder {
        background: rgba(255, 255, 255, 0.4);
        border: 2px dashed #cbd5e1;
        border-radius: 24px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        min-height: 400px;
    }

    .empty-illustration { text-align: center; padding: 40px 0; }
    .empty-illustration i { color: #e2e8f0; font-size: 4rem; margin-bottom: 15px; }

</style>

    <section class="content lms-content">
        <div class="container-fluid">
        
        <!-- BANNER UTAMA & INFO WAKTU -->
        <div class="row mb-3">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="lms-welcome-card h-100 d-flex align-items-center justify-content-between">
                    <div class="welcome-text">
                        <p class="mb-2 font-weight-bold" style="letter-spacing: 1px; text-transform: uppercase; font-size: 0.8rem; opacity: 0.8;">Selamat Datang Kembali,</p>
                        <h1><?php echo htmlspecialchars($_SESSION['nama_pengguna'] ?? 'Siswa'); ?>!</h1>
                        <p>Siapkan dirimu untuk petualangan belajar hari ini. Setiap materi yang kamu baca adalah satu langkah lebih dekat ke cita-citamu.</p>
                    </div>
                    <div class="welcome-button ml-3">
                        <a href="<?= BASE_URL ?>lms/materi_list" class="btn btn-light rounded-pill px-4 py-2 font-weight-bold shadow-sm" style="color: #4f46e5;">
                            Mulai Belajar <i class="fas fa-rocket ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="info-card-clock">
                    <div class="time-label">
                        <i class="fas fa-clock"></i> Waktu Server
                    </div>
                    <div class="digital-clock" id="lms-realtime-clock">--:--:--</div>
                    <div class="date-display">
                        <?php
                        $hari_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                        $bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        echo $hari_indo[date('l')] . ', ' . date('d') . ' ' . $bulan_indo[(int) date('m')] . ' ' . date('Y');
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS BAR -->
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="glass-stat-card">
                    <div class="icon-box" style="background: #eef2ff; color: #4f46e5;">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo (int)($data['mapel_count'] ?? 0); ?></h3>
                        <p>Mata Pelajaran</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="glass-stat-card">
                    <div class="icon-box" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($data['materi_available'] ?? []); ?></h3>
                        <p>Modul Ajar</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="glass-stat-card">
                    <div class="icon-box" style="background: #fefce8; color: #ca8a04;">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($data['tugas_pending'] ?? []); ?></h3>
                        <p>Tugas Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="glass-stat-card">
                    <div class="icon-box" style="background: #fff1f2; color: #e11d48;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo (int)($data['tugas_done_count'] ?? 0); ?></h3>
                        <p>Tugas Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- PROFILE SISWA (KIRI) -->
            <div class="col-lg-4 mb-4">
                <div class="profile-glass-card">
                    <?php 
                    $siswa = $data['siswa'] ?? [];
                    $foto_path = !empty($siswa['foto_pengguna']) ? 'assets/img/profil/' . $siswa['foto_pengguna'] : 'assets/img/avatar5.png';
                    ?>
                    <div class="profile-photo-wrapper">
                        <img src="<?php echo $foto_path; ?>" alt="Profile" class="profile-photo">
                    </div>
                    
                    <h4 class="font-weight-bold mb-1" style="color: #1e293b;"><?php echo htmlspecialchars($siswa['nama'] ?? 'Nama Siswa'); ?></h4>
                    <span class="badge badge-primary px-3 py-2 rounded-pill">Siswa Aktif</span>
                    
                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <div class="profile-info-label">NIPD</div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($siswa['nipd'] ?? '-'); ?></div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">NISN</div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($siswa['nisn'] ?? '-'); ?></div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">Tempat, Tanggal Lahir</div>
                            <div class="profile-info-value"><?php echo htmlspecialchars(($siswa['tempat_lahir'] ?? '-') . ', ' . (isset($siswa['tanggal_lahir']) ? date('d F Y', strtotime($siswa['tanggal_lahir'])) : '-')); ?></div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">Kelas Saat Ini</div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($siswa['nama_kelas'] ?? '-'); ?></div>
                        </div>
                    </div>
                    
                    <div class="profile-quote">
                        <i class="fas fa-quote-left mr-2 opacity-50"></i>
                        Belajar hari ini untuk masa depan yang lebih cerah. Terus semangat mengejar cita-citamu!
                        <i class="fas fa-quote-right ml-2 opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- AREA KANAN (WIDGETS) -->
            <div class="col-lg-8 mb-4">
                <div class="row">
                    <?php $ps = $data['portal_summary'] ?? []; ?>
                    
                    <!-- Widget: Jadwal Hari Ini -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-calendar-day text-primary mr-2"></i> Jadwal Hari Ini
                                    <span class="badge badge-light ml-2 border"><?= $ps['hari_ini'] ?? 'Hari Ini' ?></span>
                                </h6>
                            </div>
                            <div class="card-body px-4 pt-3 pb-4">
                                <?php if (empty($ps['jadwal_hari_ini'])): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-mug-hot fa-2x mb-2" style="opacity: 0.3;"></i>
                                        <p class="mb-0" style="font-size: 0.85rem;">Tidak ada jadwal hari ini.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($ps['jadwal_hari_ini'] as $j): 
                                            $j = (array) $j;
                                            $is_kbm = (($j['jenis'] ?? '') === 'KBM' || ($j['nama_kegiatan'] ?? '') === 'KBM');
                                            $nama = $is_kbm ? $j['nama_mapel'] : $j['nama_kegiatan'];
                                            $icon = $is_kbm ? 'fa-book text-success' : 'fa-flag text-secondary';
                                        ?>
                                        <div class="list-group-item px-0 py-2 border-0">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 60px; font-size: 0.75rem;" class="font-weight-bold text-dark">
                                                    <?= htmlspecialchars($j['jam_mulai']) ?>
                                                </div>
                                                <div class="mx-2"><i class="fas <?= $icon ?>" style="font-size: 0.8rem; opacity:0.8;"></i></div>
                                                <div class="flex-grow-1 ml-1" style="line-height: 1.2;">
                                                    <div class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($nama) ?></div>
                                                    <?php if ($j['nama_guru']): ?>
                                                    <div class="text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($j['nama_guru']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Widget: Info Akademik & Keuangan -->
                    <div class="col-md-6 mb-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: 4px solid #f59e0b !important;">
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">RATA-RATA NILAI</div>
                                            <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $ps['rata_nilai'] ?? 0 ?></h4>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; opacity: 0.8;">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: 4px solid #ef4444 !important;">
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">TAGIHAN SPP BELUM LUNAS</div>
                                            <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $ps['tagihan_belum_lunas'] ?? 0 ?> <span style="font-size:0.8rem; font-weight:normal;" class="text-muted">Tagihan</span></h4>
                                        </div>
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; opacity: 0.8;">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: 4px solid #64748b !important;">
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">ALPA BULAN INI</div>
                                            <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $ps['alpa_bulan_ini'] ?? 0 ?> <span style="font-size:0.8rem; font-weight:normal;" class="text-muted">Hari</span></h4>
                                        </div>
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; opacity: 0.8;">
                                            <i class="fas fa-user-times"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div><!-- /.container-fluid -->
    </section><!-- /.content -->

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateLmsTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElement = document.getElementById('lms-realtime-clock');
            if (clockElement) {
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        
        setInterval(updateLmsTime, 1000);
        updateLmsTime(); // Initial call
    });
</script>
