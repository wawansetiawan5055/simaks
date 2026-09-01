<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .cbt-icon-box {
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
    .cbt-student-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
        color: #ffffff;
        border-radius: 18px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 8px 24px rgba(30, 27, 75, 0.2);
    }
    .cbt-student-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        right: -30px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.3), transparent 70%);
        border-radius: 50%;
    }
    .cbt-exam-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .cbt-exam-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
        border-color: #cbd5e1;
    }
    .segmented-tab-nav {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        gap: 4px;
    }
    .segmented-tab-nav .nav-link {
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.84rem;
        color: #64748b;
        text-decoration: none !important;
        transition: all 0.2s ease;
        border: none;
    }
    .segmented-tab-nav .nav-link:hover {
        color: #1e293b;
    }
    .segmented-tab-nav .nav-link.active {
        background: #4f46e5 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
    }
    .token-badge {
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
        background: #1e293b;
        color: #38bdf8;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.92rem;
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
    .table-history-header th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px;
        border-top: none !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        padding: 12px 16px !important;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (UJIAN CBT SISWA)                   */
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
        .cbt-student-hero {
            padding: 12px 14px !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .cbt-student-hero h3 {
            font-size: 0.95rem !important;
            margin-bottom: 3px !important;
        }
        .cbt-student-hero p {
            font-size: 0.70rem !important;
        }
        .segmented-tab-nav {
            display: flex !important;
            width: 100% !important;
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
            -webkit-overflow-scrolling: touch;
            padding: 3px !important;
            margin-bottom: 8px !important;
            border-radius: 12px !important;
            gap: 4px !important;
        }
        .segmented-tab-nav .nav-link {
            padding: 5px 12px !important;
            font-size: 0.72rem !important;
            white-space: nowrap !important;
            flex-shrink: 0;
        }
        .cbt-exam-card {
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .cbt-exam-card .card-header, .cbt-exam-card .card-body {
            padding: 8px 10px !important;
        }
        .cbt-exam-card h5 {
            font-size: 0.82rem !important;
        }
        .token-badge {
            font-size: 0.76rem !important;
            padding: 2px 6px !important;
        }
        .btn-gradient-indigo, .cbt-exam-card .btn {
            padding: 6px 10px !important;
            font-size: 0.72rem !important;
        }
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border: none;
        }
        .table-history-header th {
            padding: 6px 8px !important;
            font-size: 0.65rem !important;
            white-space: nowrap;
        }
        .table td {
            padding: 6px 8px !important;
            font-size: 0.70rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="cbt-icon-box mr-3">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Ujian &amp; Asesmen Online (CBT)
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Ujian CBT</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- HERO INFO SISWA -->
        <div class="card cbt-student-hero shadow-sm mb-4">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-md-0">
                    <span class="badge text-white font-weight-bold px-2.5 py-1 mb-2 text-uppercase d-inline-block" style="background: rgba(255,255,255,0.15); letter-spacing: 0.5px; font-size: 0.72rem; border-radius: 6px;">
                        <i class="fas fa-shield-alt mr-1"></i> Portal Asesmen Terpadu
                    </span>
                    <h4 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">
                        Selamat Datang di Ruang Ujian Online
                    </h4>
                    <p class="text-light small mb-0" style="opacity: 0.9;">
                        Kelas: <strong><?= htmlspecialchars($kelas['nama_kelas'] ?? '-') ?></strong> &bull; 
                        Pastikan koneksi internet stabil dan siapkan Token Ujian sebelum memulai pengerjaan.
                    </p>
                </div>
                <div class="col-md-4 text-md-right">
                    <div class="d-inline-block text-left p-3 rounded-lg" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(6px); border-radius: 14px;">
                        <div class="small text-light" style="opacity: 0.85; font-size: 0.78rem;">Ujian Siap Dikerjakan:</div>
                        <div class="h3 font-weight-bold text-warning mb-0 mt-0.5" style="font-family: 'Poppins', sans-serif;">
                            <?= count($aktif) ?> <span class="small font-weight-normal text-white" style="font-size: 0.85rem;">Ujian Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAV TABS (SEGMENTED PILL NAV) -->
        <div class="mb-4">
            <div class="segmented-tab-nav" id="cbtTabs" role="tablist">
                <a class="nav-link active" id="tab-aktif" data-toggle="pill" href="#pane-aktif" role="tab">
                    <i class="fas fa-play-circle mr-1"></i> Ujian Aktif (<?= count($aktif) ?>)
                </a>
                <a class="nav-link" id="tab-mendatang" data-toggle="pill" href="#pane-mendatang" role="tab">
                    <i class="fas fa-calendar-alt mr-1"></i> Jadwal Mendatang (<?= count($mendatang) ?>)
                </a>
                <a class="nav-link" id="tab-selesai" data-toggle="pill" href="#pane-selesai" role="tab">
                    <i class="fas fa-check-circle mr-1"></i> Riwayat &amp; Nilai (<?= count($selesai) ?>)
                </a>
            </div>
        </div>

        <div class="tab-content" id="cbtTabsContent">
            <!-- TAB 1: UJIAN AKTIF -->
            <div class="tab-pane fade show active" id="pane-aktif" role="tabpanel">
                <?php if (empty($aktif)): ?>
                    <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 16px; background: #ffffff;">
                        <div class="mb-3">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                                <i class="fas fa-coffee"></i>
                            </div>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Tidak Ada Ujian Aktif Saat Ini</h6>
                        <p class="text-muted small mb-0">Saat ini belum ada agenda ujian yang dijadwalkan untuk kelas Anda atau waktu pengerjaan belum dimulai.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($aktif as $u): ?>
                        <div class="col-lg-6 col-12 mb-4">
                            <div class="card cbt-exam-card shadow-sm h-100">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                                <?php
                                                    $ju = strtolower($u['jenis_ujian'] ?? 'ph');
                                                    if ($ju === 'sas') {
                                                        echo '<span class="badge badge-danger px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #ef4444;"><i class="fas fa-star mr-1"></i> SAS</span>';
                                                    } elseif ($ju === 'sat') {
                                                        echo '<span class="badge badge-warning px-2 py-0.5 font-weight-bold text-dark shadow-xs" style="font-size: 0.68rem; background: #f59e0b;"><i class="fas fa-trophy mr-1"></i> SAT</span>';
                                                    } elseif ($ju === 'saj') {
                                                        echo '<span class="badge px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #8b5cf6;"><i class="fas fa-graduation-cap mr-1"></i> SAJ</span>';
                                                    } elseif ($ju === 'sts') {
                                                        echo '<span class="badge badge-primary px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #3b82f6;"><i class="fas fa-bookmark mr-1"></i> STS</span>';
                                                    } elseif ($ju === 'tryout') {
                                                        echo '<span class="badge badge-dark px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #475569;"><i class="fas fa-flask mr-1"></i> Tryout</span>';
                                                    } else {
                                                        echo '<span class="badge badge-info px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #06b6d4;"><i class="fas fa-pen-nib mr-1"></i> PH</span>';
                                                    }
                                                ?>
                                                <span class="badge text-white font-weight-bold px-2.5 py-1 rounded" style="background: #4f46e5; font-size: 0.74rem;">
                                                    <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($u['nama_mapel'] ?? '-') ?>
                                                </span>
                                            </div>
                                            <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-bolt mr-1"></i> Sedang Dibuka
                                            </span>
                                        </div>

                                        <h5 class="font-weight-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.15rem;">
                                            <?= htmlspecialchars($u['nama_ujian']) ?>
                                        </h5>
                                        <p class="text-muted small mb-3">
                                            Paket: <strong><?= htmlspecialchars($u['nama_paket'] ?? '-') ?></strong>
                                        </p>

                                        <div class="p-3 bg-light rounded-lg border mb-3">
                                            <div class="row small text-dark" style="row-gap: 8px;">
                                                <div class="col-6">
                                                    <span class="text-muted d-block font-weight-bold" style="font-size: 0.76rem;">Durasi Waktu:</span>
                                                    <strong class="text-dark"><i class="fas fa-stopwatch text-primary mr-1"></i> <?= $u['durasi_menit'] ?? 60 ?> Menit</strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block font-weight-bold" style="font-size: 0.76rem;">KKM / Passing:</span>
                                                    <strong class="text-dark"><i class="fas fa-award text-warning mr-1"></i> <?= $u['passing_grade'] ?? 75 ?></strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block font-weight-bold" style="font-size: 0.76rem;">Batas Selesai:</span>
                                                    <span class="text-danger font-weight-bold"><?= $u['tanggal_selesai'] ? date('d M Y H:i', strtotime($u['tanggal_selesai'])) : '-' ?></span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted d-block font-weight-bold" style="font-size: 0.76rem;">Token Ujian Anda:</span>
                                                    <span class="token-badge"><?= htmlspecialchars($u['token'] ?? '-') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                        <small class="text-muted font-weight-bold">
                                            Status: <span class="badge badge-light border text-secondary px-2 py-0.5"><?= ucfirst($u['status_peserta']) ?></span>
                                        </small>
                                        <a href="<?= BASE_URL ?>siswa_portal/cbt_konfirmasi?id_peserta=<?= $u['id_peserta'] ?>" class="btn btn-gradient-indigo btn-sm font-weight-bold rounded-pill px-4 py-2 shadow-sm">
                                            <i class="fas fa-clipboard-check mr-1"></i> Konfirmasi &amp; Mulai &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 2: JADWAL MENDATANG -->
            <div class="tab-pane fade" id="pane-mendatang" role="tabpanel">
                <?php if (empty($mendatang)): ?>
                    <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 16px; background: #ffffff;">
                        <div class="mb-3">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">Tidak Ada Jadwal Ujian Mendatang</h6>
                        <p class="text-muted small mb-0">Semua agenda ujian yang dijadwalkan sudah terlaksana atau belum ditambahkan oleh guru.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($mendatang as $u): ?>
                        <div class="col-lg-6 col-12 mb-4">
                            <div class="card cbt-exam-card shadow-sm h-100">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                                <?php
                                                    $ju = strtolower($u['jenis_ujian'] ?? 'ph');
                                                    if ($ju === 'sas') {
                                                        echo '<span class="badge badge-danger px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #ef4444;"><i class="fas fa-star mr-1"></i> SAS</span>';
                                                    } elseif ($ju === 'sat') {
                                                        echo '<span class="badge badge-warning px-2 py-0.5 font-weight-bold text-dark shadow-xs" style="font-size: 0.68rem; background: #f59e0b;"><i class="fas fa-trophy mr-1"></i> SAT</span>';
                                                    } elseif ($ju === 'saj') {
                                                        echo '<span class="badge px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #8b5cf6;"><i class="fas fa-graduation-cap mr-1"></i> SAJ</span>';
                                                    } elseif ($ju === 'sts') {
                                                        echo '<span class="badge badge-primary px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #3b82f6;"><i class="fas fa-bookmark mr-1"></i> STS</span>';
                                                    } elseif ($ju === 'tryout') {
                                                        echo '<span class="badge badge-dark px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #475569;"><i class="fas fa-flask mr-1"></i> Tryout</span>';
                                                    } else {
                                                        echo '<span class="badge badge-info px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #06b6d4;"><i class="fas fa-pen-nib mr-1"></i> PH</span>';
                                                    }
                                                ?>
                                                <span class="badge text-white font-weight-bold px-2.5 py-1 rounded" style="background: #4f46e5; font-size: 0.74rem;">
                                                    <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($u['nama_mapel'] ?? '-') ?>
                                                </span>
                                            </div>
                                            <span class="badge badge-warning px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-clock mr-1"></i> Terjadwal
                                            </span>
                                        </div>
                                        <h5 class="font-weight-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.15rem;"><?= htmlspecialchars($u['nama_ujian']) ?></h5>
                                        <p class="text-muted small mb-3">Paket: <strong><?= htmlspecialchars($u['nama_paket'] ?? '-') ?></strong></p>

                                        <div class="p-3 bg-light rounded-lg border small text-dark mb-3">
                                            <div class="mb-1"><i class="fas fa-calendar-day text-primary mr-1"></i> Waktu Mulai: <strong><?= date('d M Y, H:i', strtotime($u['tanggal_mulai'])) ?> WIB</strong></div>
                                            <div><i class="fas fa-stopwatch text-muted mr-1"></i> Durasi Ujian: <strong><?= $u['durasi_menit'] ?? 60 ?> Menit</strong></div>
                                        </div>
                                    </div>

                                    <button class="btn btn-light border btn-sm btn-block rounded-pill font-weight-bold text-muted" disabled>
                                        <i class="fas fa-lock mr-1"></i> Belum Dapat Dikerjakan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 3: RIWAYAT & NILAI SELESAI -->
            <div class="tab-pane fade" id="pane-selesai" role="tabpanel">
                <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-history text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Transkrip Riwayat Ujian CBT Siswa
                            </h6>
                        </div>
                        <span class="badge badge-light border text-muted px-3 py-1.5 rounded-pill font-weight-bold small">
                            Total: <strong class="text-primary font-weight-bold"><?= count($selesai) ?></strong> Ujian
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-family: 'Poppins', sans-serif;">
                            <thead class="table-history-header">
                                <tr>
                                    <th style="width: 35px;">#</th>
                                    <th>Nama Ujian &amp; Paket</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Waktu Pengerjaan</th>
                                    <th class="text-center">Nilai PG</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($selesai)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="mb-3">
                                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                            </div>
                                            <h6 class="font-weight-bold text-dark mb-1">Belum Ada Riwayat Ujian</h6>
                                            <p class="text-muted small mb-0">Ujian yang telah Anda selesaikan akan tercatat transkrip dan nilainya di sini.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($selesai as $i => $s): ?>
                                    <?php 
                                        $tampilkan = isset($s['tampilkan_nilai']) ? (int)$s['tampilkan_nilai'] : 1; 
                                    ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted align-middle"><?= $i + 1 ?></td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 4px;">
                                                <?php
                                                    $sju = strtolower($s['jenis_ujian'] ?? 'ph');
                                                    if ($sju === 'sas') {
                                                        echo '<span class="badge badge-danger px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #ef4444;"><i class="fas fa-star mr-1"></i> SAS</span>';
                                                    } elseif ($sju === 'sat') {
                                                        echo '<span class="badge badge-warning px-2 py-0.5 font-weight-bold text-dark shadow-xs" style="font-size: 0.68rem; background: #f59e0b;"><i class="fas fa-trophy mr-1"></i> SAT</span>';
                                                    } elseif ($sju === 'saj') {
                                                        echo '<span class="badge px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #8b5cf6;"><i class="fas fa-graduation-cap mr-1"></i> SAJ</span>';
                                                    } elseif ($sju === 'sts') {
                                                        echo '<span class="badge badge-primary px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #3b82f6;"><i class="fas fa-bookmark mr-1"></i> STS</span>';
                                                    } elseif ($sju === 'tryout') {
                                                        echo '<span class="badge badge-dark px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #475569;"><i class="fas fa-flask mr-1"></i> Tryout</span>';
                                                    } else {
                                                        echo '<span class="badge badge-info px-2 py-0.5 font-weight-bold text-white shadow-xs" style="font-size: 0.68rem; background: #06b6d4;"><i class="fas fa-pen-nib mr-1"></i> PH</span>';
                                                    }
                                                ?>
                                                <strong class="text-dark font-weight-bold" style="font-size: 0.92rem;"><?= htmlspecialchars($s['nama_ujian']) ?></strong>
                                            </div>
                                            <small class="text-muted"><?= htmlspecialchars($s['nama_paket'] ?? '-') ?></small>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge text-white font-weight-bold px-2 py-0.5 rounded" style="background: #4f46e5; font-size: 0.72rem;">
                                                <?= htmlspecialchars($s['nama_mapel'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted align-middle">
                                            <?= $s['waktu_selesai'] ? date('d M Y H:i', strtotime($s['waktu_selesai'])) : ($s['waktu_mulai'] ? date('d M Y H:i', strtotime($s['waktu_mulai'])) : '-') ?>
                                        </td>
                                        <td class="text-center font-weight-bold align-middle">
                                            <?php if ($tampilkan == 1): ?>
                                                <?= $s['nilai_pg'] !== null ? number_format($s['nilai_pg'], 1) : '-' ?>
                                            <?php else: ?>
                                                <span class="text-muted" title="Nilai ditutup guru"><i class="fas fa-eye-slash"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center font-weight-bold align-middle" style="font-size: 1rem;">
                                            <?php if ($tampilkan == 1): ?>
                                                <span class="text-<?= ($s['nilai_akhir'] ?? 0) >= ($s['passing_grade'] ?? 75) ? 'success' : 'danger' ?>">
                                                    <?= $s['nilai_akhir'] !== null ? number_format($s['nilai_akhir'], 1) : '-' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-light border text-muted small px-2 py-1"><i class="fas fa-lock mr-1"></i> Ditutup</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($tampilkan == 1 && $s['status_lulus'] == 1): ?>
                                                <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                    <i class="fas fa-check mr-1"></i> Tuntas
                                                </span>
                                            <?php elseif ($tampilkan == 1 && $s['nilai_akhir'] !== null): ?>
                                                <span class="badge badge-danger px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                    <i class="fas fa-times mr-1"></i> Belum Tuntas
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-info px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                    <i class="fas fa-check-circle mr-1"></i> Selesai
                                                </span>
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
        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
