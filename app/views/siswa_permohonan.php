<?php 
// app/views/siswa_permohonan.php
// Redesign Halaman Pengajuan Izin/Sakit Siswa: Clean Professional Banner & Fitur Kamera Langsung (Live Camera)

include __DIR__ . '/partials/header.php'; 

$ta_nama_display = $_SESSION['nama_ta_aktif'] ?? '2026/2027 Ganjil';

// Hitung statistik riwayat
$total_menunggu  = 0;
$total_disetujui = 0;
$total_ditolak   = 0;

foreach ($riwayat ?? [] as $r) {
    if ($r['status'] === 'Disetujui') $total_disetujui++;
    elseif ($r['status'] === 'Ditolak') $total_ditolak++;
    else $total_menunggu++;
}
?>

<style>
/* ===== CLEAN PROFESSIONAL BANNER (1 BARIS) ===== */
.perm-banner-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    position: relative;
    overflow: hidden;
}
.perm-banner-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #0284c7 0%, #38bdf8 100%);
    border-radius: 14px 0 0 14px;
}
.step-guide-pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 5px 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.step-num {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #0284c7;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 800;
    flex-shrink: 0;
}

/* Form Card */
.form-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    background: #ffffff;
}
.form-card .card-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    border-radius: 16px 16px 0 0 !important;
    padding: 16px 22px;
}
.form-card .card-header h6 {
    color: #0f172a;
    font-weight: 800;
    margin: 0;
    font-family: 'Poppins', sans-serif;
}

/* Jenis Izin Selector */
.izin-selector {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.izin-option {
    flex: 1;
    min-width: 95px;
}
.izin-option input[type="radio"] { display: none; }
.izin-option label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 12px 10px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.84rem;
    font-weight: 700;
    color: #64748b;
    background: #f8fafc;
    text-align: center;
    margin: 0;
}
.izin-option label i { font-size: 1.35rem; }
.izin-option:nth-child(1) input:checked + label {
    border-color: #ef4444; background: #fef2f2; color: #dc2626; box-shadow: 0 3px 10px rgba(239,68,68,0.15);
}
.izin-option:nth-child(2) input:checked + label {
    border-color: #f59e0b; background: #fffbeb; color: #d97706; box-shadow: 0 3px 10px rgba(245,158,11,0.15);
}
.izin-option:nth-child(3) input:checked + label {
    border-color: #8b5cf6; background: #f5f3ff; color: #7c3aed; box-shadow: 0 3px 10px rgba(139,92,246,0.15);
}

/* Jenis Absensi Toggle */
.absensi-toggle {
    display: flex;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    background: #f8fafc;
}
.absensi-toggle input[type="radio"] { display: none; }
.absensi-toggle label {
    flex: 1;
    text-align: center;
    padding: 9px 14px;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
    margin: 0;
}
.absensi-toggle input:checked + label {
    background: #0284c7;
    color: #ffffff;
}

/* Upload & Live Camera Zone (1 Baris) */
.camera-tab-nav {
    display: flex !important;
    width: 100% !important;
    gap: 6px !important;
    margin-bottom: 8px !important;
}
.camera-tab-nav .nav-item {
    flex: 1 1 50% !important;
    margin: 0 !important;
}
.camera-tab-nav .nav-link {
    width: 100% !important;
    text-align: center !important;
    justify-content: center !important;
    display: inline-flex !important;
    align-items: center !important;
    font-size: 0.78rem !important;
    font-weight: 700;
    padding: 8px 10px !important;
    border-radius: 10px !important;
    color: #64748b;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    margin-right: 0 !important;
    transition: all 0.2s ease;
}
.camera-tab-nav .nav-link.active {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 3px 10px rgba(2, 132, 199, 0.25);
}
.upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 16px 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
    position: relative;
}
.upload-zone:hover { border-color: #0284c7; background: #eff6ff; }
.upload-zone.has-file { border-color: #10b981; background: #f0fdf4; }
.upload-zone input[type="file"] {
    position: absolute; inset: 0;
    opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.camera-box-wrapper {
    background: #0f172a;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    text-align: center;
}
#cameraVideo {
    width: 100%;
    max-height: 240px;
    object-fit: cover;
    display: block;
    background: #000;
}
.captured-preview-img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    display: block;
    margin: 8px auto 0;
    object-fit: cover;
}

/* Status Badges */
.badge-menunggu  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; padding: 4px 12px; border-radius: 50px; font-size: 0.74rem; font-weight: 700; }
.badge-disetujui { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; padding: 4px 12px; border-radius: 50px; font-size: 0.74rem; font-weight: 700; }
.badge-ditolak   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 4px 12px; border-radius: 50px; font-size: 0.74rem; font-weight: 700; }

/* Riwayat Card */
.riwayat-card { border: none; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,0.06); background: #ffffff; }
.riwayat-row { border-left: 4px solid #e2e8f0; transition: background 0.15s ease; }
.riwayat-row.menunggu  { border-left-color: #f59e0b !important; }
.riwayat-row.disetujui { border-left-color: #10b981 !important; }
.riwayat-row.ditolak   { border-left-color: #ef4444 !important; }

/* ============================================================ */
/* 📱 MOBILE RESPONSIVENESS (PERMOHONAN IZIN/SAKIT)            */
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
    .perm-banner-card {
        padding: 8px 10px !important;
        border-radius: 10px !important;
        margin-bottom: 10px !important;
    }
    .step-guide-pill {
        padding: 4px 7px !important;
        font-size: 0.66rem !important;
        gap: 4px !important;
    }
    .step-num {
        width: 18px !important;
        height: 18px !important;
        font-size: 0.60rem !important;
    }
    .form-card, .riwayat-card {
        border-radius: 10px !important;
        margin-bottom: 10px !important;
    }
    .form-card .card-header, .riwayat-card .card-header {
        padding: 10px 12px !important;
    }
    .form-card .card-header h6, .riwayat-card .card-header h6 {
        font-size: 0.82rem !important;
    }
    .form-card .card-body, .riwayat-card .card-body {
        padding: 10px 8px !important;
    }
    .izin-option label {
        padding: 8px 4px !important;
        font-size: 0.70rem !important;
        border-radius: 8px !important;
        gap: 4px !important;
    }
    .izin-option label i {
        font-size: 1.05rem !important;
    }
    .absensi-toggle label {
        padding: 6px 8px !important;
        font-size: 0.70rem !important;
    }
    .camera-tab-nav .nav-link {
        font-size: 0.70rem !important;
        padding: 6px 6px !important;
    }
    .upload-zone {
        padding: 12px 8px !important;
        font-size: 0.70rem !important;
    }
    .upload-zone i {
        font-size: 1.3rem !important;
    }
    .table-responsive {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        border: none;
    }
    .table th {
        padding: 6px 6px !important;
        font-size: 0.65rem !important;
        white-space: nowrap;
    }
    .table td {
        padding: 6px 6px !important;
        font-size: 0.70rem !important;
    }
    .badge-menunggu, .badge-disetujui, .badge-ditolak {
        font-size: 0.62rem !important;
        padding: 2px 8px !important;
    }
    .btn-block {
        font-size: 0.75rem !important;
        padding: 7px 12px !important;
    }
}
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Pengajuan Izin &amp; Sakit Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>siswa_portal/absensi" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Rekap Presensi
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">

        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px;">
                <i class="fas fa-check-circle mr-2"></i> <?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius:12px;">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- CLEAN & COMPACT 1 BARIS BANNER ALUR PENGGUNAAN -->
        <div class="perm-banner-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center flex-nowrap" style="gap: 8px; overflow-x: auto; -webkit-overflow-scrolling: touch; max-width: 100%;">
                    <span class="badge badge-primary px-2.5 py-1 rounded-pill font-weight-bold flex-shrink-0" style="font-size: 0.68rem;">
                        <i class="fas fa-info-circle mr-1"></i> Alur:
                    </span>
                    <div class="step-guide-pill flex-shrink-0">
                        <span class="step-num">1</span>
                        <span class="small font-weight-bold text-dark" style="font-size: 0.74rem;">Pilih Jenis &amp; Waktu</span>
                    </div>
                    <i class="fas fa-chevron-right text-muted small opacity-50 flex-shrink-0" style="font-size: 0.65rem;"></i>
                    <div class="step-guide-pill flex-shrink-0">
                        <span class="step-num">2</span>
                        <span class="small font-weight-bold text-dark" style="font-size: 0.74rem;">Ambil Foto / Upload</span>
                    </div>
                    <i class="fas fa-chevron-right text-muted small opacity-50 flex-shrink-0" style="font-size: 0.65rem;"></i>
                    <div class="step-guide-pill flex-shrink-0">
                        <span class="step-num">3</span>
                        <span class="small font-weight-bold text-dark" style="font-size: 0.74rem;">Verifikasi Guru</span>
                    </div>
                </div>

                <!-- Status Counter Badges in Same Line -->
                <div class="d-flex align-items-center flex-shrink-0 ml-auto" style="gap: 6px;">
                    <span class="badge badge-warning px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                        Menunggu: <strong><?= $total_menunggu ?></strong>
                    </span>
                    <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                        Disetujui: <strong><?= $total_disetujui ?></strong>
                    </span>
                    <span class="badge badge-danger px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.68rem;">
                        Ditolak: <strong><?= $total_ditolak ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ======================================================== -->
            <!-- 1. FORM PENGAJUAN IZIN / SAKIT                           -->
            <!-- ======================================================== -->
            <div class="col-lg-5 col-12 mb-4">
                <div class="card form-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6><i class="fas fa-edit text-primary mr-2"></i> Formulir Pengajuan Baru</h6>
                        <span class="badge badge-light border text-muted" style="font-size: 0.72rem;">Wajib Valid</span>
                    </div>
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>siswa_portal/permohonan_simpan" method="post" enctype="multipart/form-data" id="formPermohonan">

                            <!-- Jenis Izin -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small mb-2">Jenis Permohonan <span class="text-danger">*</span></label>
                                <div class="izin-selector">
                                    <div class="izin-option">
                                        <input type="radio" id="jenis_sakit" name="jenis_izin" value="Sakit" required>
                                        <label for="jenis_sakit"><i class="fas fa-thermometer-half"></i> Sakit</label>
                                    </div>
                                    <div class="izin-option">
                                        <input type="radio" id="jenis_izin" name="jenis_izin" value="Izin">
                                        <label for="jenis_izin"><i class="fas fa-hand-paper"></i> Izin</label>
                                    </div>
                                    <div class="izin-option">
                                        <input type="radio" id="jenis_lain" name="jenis_izin" value="Lainnya">
                                        <label for="jenis_lain"><i class="fas fa-ellipsis-h"></i> Lainnya</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Jenis Absensi -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small mb-2">Target Absensi <span class="text-danger">*</span></label>
                                <div class="absensi-toggle">
                                    <input type="radio" id="ab_piket" name="jenis_absensi" value="piket" checked>
                                    <label for="ab_piket"><i class="fas fa-school mr-1"></i> Kelas (Piket)</label>
                                    <input type="radio" id="ab_mapel" name="jenis_absensi" value="mapel">
                                    <label for="ab_mapel"><i class="fas fa-book mr-1"></i> Mata Pelajaran</label>
                                </div>
                            </div>

                            <!-- Dropdown Mapel (muncul jika pilih mapel) -->
                            <div class="form-group mb-3" id="group_mapel" style="display:none;">
                                <label class="font-weight-bold text-dark small">Pilih Mata Pelajaran <small class="text-muted font-weight-normal">(Bisa lebih dari satu)</small></label>
                                <div class="row" style="background:#f8fafc; border-radius:10px; padding:12px; border:1px solid #e2e8f0; margin:0; max-height: 180px; overflow-y: auto;">
                                    <?php foreach ($mapel_list as $m): ?>
                                    <div class="col-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input mapel-checkbox" name="id_mapel[]" id="mapel_<?= $m['id_mapel'] ?>" value="<?= $m['id_mapel'] ?>">
                                            <label class="custom-control-label" for="mapel_<?= $m['id_mapel'] ?>" style="font-weight:600; font-size:0.80rem; color:#475569;"><?= htmlspecialchars($m['nama_mapel']) ?></label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Tanggal (H atau H+1) -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small">Tanggal Ketidakhadiran <span class="text-danger">*</span></label>
                                <?php
                                    $today    = date('Y-m-d');
                                    $tomorrow = date('Y-m-d', strtotime('+1 day'));
                                ?>
                                <div class="d-flex" style="gap:10px;">
                                    <div class="flex-fill">
                                        <input type="radio" class="d-none" id="tgl_hari" name="tanggal" value="<?= $today ?>" required checked>
                                        <label for="tgl_hari" class="btn btn-sm btn-primary w-100 font-weight-bold py-2" style="border-radius:10px;">
                                            <i class="fas fa-calendar-day mr-1"></i> Hari Ini
                                            <small class="d-block" style="font-size:0.72rem;"><?= date('d/m/Y') ?></small>
                                        </label>
                                    </div>
                                    <div class="flex-fill">
                                        <input type="radio" class="d-none" id="tgl_besok" name="tanggal" value="<?= $tomorrow ?>">
                                        <label for="tgl_besok" class="btn btn-sm btn-outline-secondary w-100 font-weight-bold py-2" style="border-radius:10px;">
                                            <i class="fas fa-calendar-plus mr-1"></i> Besok
                                            <small class="d-block" style="font-size:0.72rem;"><?= date('d/m/Y', strtotime('+1 day')) ?></small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark small">Alasan / Keterangan Lengkap</label>
                                <textarea name="keterangan" class="form-control" rows="2"
                                    style="border-radius:10px; font-size: 0.85rem;"
                                    placeholder="Jelaskan alasan ketidakhadiran secara singkat dan jelas..."></textarea>
                            </div>

                            <!-- DUAL MODE BUKTI: UNGGAH BERKAS & LIVE CAMERA (1 BARIS TAB) -->
                            <div class="form-group mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="font-weight-bold text-dark small mb-0">
                                        Foto Bukti Surat / Keterangan
                                        <span class="text-danger" id="foto_required_label">*</span>
                                        <small class="text-muted ml-1">(Wajib jika Sakit)</small>
                                    </label>
                                </div>

                                <!-- Tab Switcher: Unggah & Kamera (1 Baris) -->
                                <ul class="nav nav-pills camera-tab-nav" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tab-upload-mode-tab" data-toggle="pill" href="#tab-upload-mode" role="tab" onclick="stopCameraStream()">
                                            <i class="fas fa-folder-open mr-1.5"></i> Unggah
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="tab-camera-mode-tab" data-toggle="pill" href="#tab-camera-mode" role="tab" onclick="startCameraStream()">
                                            <i class="fas fa-camera mr-1.5"></i> Kamera
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content border rounded-lg p-2.5 bg-light" style="border-radius: 12px;">
                                    
                                    <!-- 1. Mode Unggah Berkas -->
                                    <div class="tab-pane fade show active" id="tab-upload-mode" role="tabpanel">
                                        <div class="upload-zone" id="uploadZone">
                                            <input type="file" name="foto_bukti" id="fotoBukti"
                                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                                   onchange="previewFoto(this)">
                                            <i class="fas fa-cloud-upload-alt text-primary" style="font-size:2rem;"></i>
                                            <p class="mb-0 mt-2 text-muted" style="font-size:0.80rem;" id="uploadText">
                                                Klik atau seret foto surat bukti ke sini<br>
                                                <small class="text-secondary">Format JPG, PNG, WEBP (Maks. 5MB)</small>
                                            </p>
                                            <img src="" id="previewImg" class="captured-preview-img" style="display:none;">
                                        </div>
                                    </div>

                                    <!-- 2. Mode Live Camera Capture (Tanpa teks instruksi atas & tanpa animasi loading) -->
                                    <div class="tab-pane fade" id="tab-camera-mode" role="tabpanel">
                                        <div class="camera-box-wrapper" style="min-height: 180px; background: #0f172a; position: relative;">
                                            <video id="cameraVideo" autoplay playsinline muted style="width: 100%; max-height: 260px; object-fit: cover; display: none;"></video>
                                            <canvas id="cameraCanvas" style="display:none;"></canvas>
                                            
                                            <!-- Error / Fallback Box (Hanya muncul jika kamera gagal diakses) -->
                                            <div id="cameraErrorBox" class="p-4 text-center text-white" style="display:none; min-height: 180px;">
                                                <i class="fas fa-video-slash text-warning fa-2x mb-2"></i>
                                                <p class="small text-light mb-2" id="cameraStatusText">Kamera tidak dapat diakses.</p>
                                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 font-weight-bold" onclick="document.getElementById('nativeCameraInput').click()">
                                                    <i class="fas fa-camera-retro mr-1"></i> Buka Kamera HP
                                                </button>
                                            </div>

                                            <!-- Overlay Camera Controls -->
                                            <div id="cameraControlsBar" class="p-2 d-flex justify-content-center align-items-center flex-wrap" style="gap: 8px; background: rgba(15, 23, 42, 0.9); display: none !important;">
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold shadow-sm" id="btnSnapPhoto" onclick="takeSnapshot()">
                                                    <i class="fas fa-camera mr-1"></i> Jepret Foto
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 font-weight-bold" id="btnSwitchCamera" onclick="switchCameraFacing()">
                                                    <i class="fas fa-sync-alt mr-1"></i> Balik Kamera
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 font-weight-bold" id="btnRetakePhoto" onclick="retakeSnapshot()" style="display:none;">
                                                    <i class="fas fa-redo mr-1"></i> Ulangi
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Direct Mobile Camera Fallback Trigger -->
                                        <div class="mt-2 text-center">
                                            <input type="file" id="nativeCameraInput" accept="image/*" capture="environment" style="display:none;" onchange="previewNativeCamera(this)">
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 font-weight-bold btn-block" onclick="document.getElementById('nativeCameraInput').click()">
                                                <i class="fas fa-camera-retro mr-1"></i> Buka Aplikasi Kamera HP
                                            </button>
                                        </div>

                                        <div id="cameraCapturedBox" class="mt-2 text-center" style="display:none;">
                                            <small class="text-success font-weight-bold d-block mb-1"><i class="fas fa-check-circle mr-1"></i> Foto Berhasil Diambil:</small>
                                            <img id="cameraCapturedPreview" class="captured-preview-img" src="">
                                        </div>
                                        
                                        <!-- Hidden Input to store Base64 Live Camera photo -->
                                        <input type="hidden" name="foto_bukti_cam" id="fotoBuktiCam" value="">
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2.5 shadow" style="border-radius:12px; font-size: 0.92rem;">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Permohonan Izin / Sakit
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 2. RIWAYAT PERMOHONAN SISWA                              -->
            <!-- ======================================================== -->
            <div class="col-lg-7 col-12 mb-4">
                <div class="card riwayat-card">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center" style="padding:16px 20px;">
                        <h6 class="mb-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                            <i class="fas fa-history text-secondary mr-2"></i> Riwayat Pengajuan Izin / Sakit
                        </h6>
                        <span class="badge badge-light border font-weight-bold text-muted" style="font-size: 0.72rem;">
                            Total: <?= count($riwayat ?? []) ?>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($riwayat)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <h6 class="font-weight-bold text-dark mb-1">Belum Ada Permohonan</h6>
                                <p class="small text-muted mb-0">Anda belum pernah mengajukan izin atau sakit pada semester ini.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($riwayat as $r):
                                    $st_class = match($r['status']) {
                                        'Disetujui' => 'disetujui',
                                        'Ditolak'   => 'ditolak',
                                        default     => 'menunggu'
                                    };
                                    $izin_icon = match($r['jenis_izin']) {
                                        'Sakit'   => ['fas fa-thermometer-half', '#ef4444'],
                                        'Izin'    => ['fas fa-hand-paper', '#f59e0b'],
                                        default   => ['fas fa-ellipsis-h', '#8b5cf6']
                                    };
                                ?>
                                <div class="list-group-item riwayat-row <?= $st_class ?> py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 10px;">
                                        <div class="d-flex align-items-start" style="gap:12px;">
                                            <div style="width:38px;height:38px;border-radius:10px;background:<?= $izin_icon[1] ?>1a;color:<?= $izin_icon[1] ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0; font-size: 1.1rem;">
                                                <i class="<?= $izin_icon[0] ?>"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size:0.90rem;">
                                                    <?= htmlspecialchars($r['jenis_izin']) ?>
                                                    <span class="text-muted font-weight-normal small">·
                                                        <?= $r['jenis_absensi'] === 'piket' ? 'Kelas (Piket)' : 'Mapel: ' . htmlspecialchars($r['nama_mapel'] ?? '-') ?>
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    <strong><?= date('d M Y', strtotime($r['tanggal'])) ?></strong>
                                                    &nbsp;·&nbsp;
                                                    Diajukan <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                                                </small>
                                                <?php if (!empty($r['keterangan'])): ?>
                                                    <small class="text-secondary d-block mt-0.5"><i class="fas fa-comment-dots mr-1"></i><?= htmlspecialchars($r['keterangan']) ?></small>
                                                <?php endif; ?>
                                                <?php if (!empty($r['catatan_petugas'])): ?>
                                                    <small class="<?= $r['status'] === 'Disetujui' ? 'text-success' : 'text-danger' ?> d-block font-weight-bold mt-0.5">
                                                        <i class="fas fa-user-check mr-1"></i>Catatan: <?= htmlspecialchars($r['catatan_petugas']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end" style="gap:6px;">
                                            <span class="badge-<?= $st_class ?>">
                                                <?php if ($r['status'] === 'Menunggu'): ?>
                                                    <i class="fas fa-clock mr-1"></i>
                                                <?php elseif ($r['status'] === 'Disetujui'): ?>
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                <?php endif; ?>
                                                <?= $r['status'] ?>
                                            </span>
                                            <?php if (!empty($r['foto_bukti'])): ?>
                                                <a href="<?= BASE_URL ?>uploads/permohonan/<?= htmlspecialchars($r['foto_bukti']) ?>"
                                                   target="_blank"
                                                   class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-0.5 mt-1 font-weight-bold shadow-sm" style="font-size:0.72rem;">
                                                    <i class="fas fa-image mr-1"></i> Lihat Bukti
                                                </a>
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
        </div>

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- LIVE CAMERA CAPTURE & FORM SCRIPTS -->
<script>
let stream = null;
let currentFacingMode = "user"; // start with default user camera, allow switch

// Start Live Camera Stream with multiple fallbacks
async function startCameraStream() {
    stopCameraStream();
    const video = document.getElementById('cameraVideo');
    const errorBox = document.getElementById('cameraErrorBox');
    const statusText = document.getElementById('cameraStatusText');
    const controlsBar = document.getElementById('cameraControlsBar');

    if (errorBox) errorBox.style.display = 'none';
    if (video) video.style.display = 'none';
    if (controlsBar) controlsBar.style.setProperty('display', 'none', 'important');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (statusText) statusText.innerText = 'Browser tidak mendukung WebRTC kamera langsung.';
        if (errorBox) errorBox.style.display = 'block';
        return;
    }

    // Set attributes for autoplay without block
    video.muted = true;
    video.setAttribute('playsinline', '');
    video.setAttribute('autoplay', '');
    video.setAttribute('muted', '');

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: currentFacingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });
    } catch (err) {
        console.warn("Primary camera constraint failed, trying generic video...", err);
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        } catch (err2) {
            console.error("Camera access error: ", err2);
            if (statusText) statusText.innerText = 'Kamera tidak dapat diakses atau izin ditolak.';
            if (errorBox) errorBox.style.display = 'block';
            return;
        }
    }

    if (stream) {
        video.srcObject = stream;
        video.onloadedmetadata = function() {
            video.play().then(() => {
                video.style.display = 'block';
                if (controlsBar) controlsBar.style.setProperty('display', 'flex', 'important');
            }).catch(e => {
                console.error("Video play error: ", e);
                video.style.display = 'block';
                if (controlsBar) controlsBar.style.setProperty('display', 'flex', 'important');
            });
        };
    }
}

// Stop Live Camera Stream
function stopCameraStream() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    const video = document.getElementById('cameraVideo');
    if (video) {
        video.srcObject = null;
        video.style.display = 'none';
    }
    const controlsBar = document.getElementById('cameraControlsBar');
    if (controlsBar) {
        controlsBar.style.setProperty('display', 'none', 'important');
    }
}

// Switch front/back camera
function switchCameraFacing() {
    currentFacingMode = (currentFacingMode === "user") ? "environment" : "user";
    startCameraStream();
}

// Take snapshot from video to canvas
function takeSnapshot() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const preview = document.getElementById('cameraCapturedPreview');
    const previewBox = document.getElementById('cameraCapturedBox');
    const hiddenInput = document.getElementById('fotoBuktiCam');
    const btnSnap = document.getElementById('btnSnapPhoto');
    const btnRetake = document.getElementById('btnRetakePhoto');

    const width = video.videoWidth || 640;
    const height = video.videoHeight || 480;

    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, width, height);

    const base64Image = canvas.toDataURL('image/jpeg', 0.85);
    hiddenInput.value = base64Image;
    preview.src = base64Image;
    previewBox.style.display = 'block';

    // Pause video & update buttons
    video.pause();
    btnSnap.style.display = 'none';
    btnRetake.style.display = 'inline-block';
}

// Retake snapshot
function retakeSnapshot() {
    const video = document.getElementById('cameraVideo');
    const previewBox = document.getElementById('cameraCapturedBox');
    const hiddenInput = document.getElementById('fotoBuktiCam');
    const btnSnap = document.getElementById('btnSnapPhoto');
    const btnRetake = document.getElementById('btnRetakePhoto');

    hiddenInput.value = '';
    previewBox.style.display = 'none';
    btnSnap.style.display = 'inline-block';
    btnRetake.style.display = 'none';
    video.play();
}

// Handle native mobile camera file trigger
function previewNativeCamera(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const base64Data = e.target.result;
            document.getElementById('fotoBuktiCam').value = base64Data;
            document.getElementById('cameraCapturedPreview').src = base64Data;
            document.getElementById('cameraCapturedBox').style.display = 'block';
            stopCameraStream();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle mapel dropdown (checkboxes)
document.querySelectorAll('input[name="jenis_absensi"]').forEach(r => {
    r.addEventListener('change', function() {
        const grp = document.getElementById('group_mapel');
        const checkboxes = document.querySelectorAll('.mapel-checkbox');
        if (this.value === 'mapel') {
            grp.style.display = '';
        } else {
            grp.style.display = 'none';
            checkboxes.forEach(cb => cb.checked = false);
        }
    });
});

// Toggle foto wajib jika Sakit
document.querySelectorAll('input[name="jenis_izin"]').forEach(r => {
    r.addEventListener('change', function() {
        const isSakit = (this.value === 'Sakit');
        const reqLabel = document.getElementById('foto_required_label');
        if (reqLabel) reqLabel.style.display = isSakit ? 'inline' : 'none';
    });
});

// Tanggal radio button styling
document.querySelectorAll('input[name="tanggal"]').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll('input[name="tanggal"]').forEach(x => {
            const lbl = document.querySelector(`label[for="${x.id}"]`);
            if (lbl) {
                lbl.classList.remove('btn-primary', 'btn-outline-secondary');
                lbl.classList.add(x.value === this.value ? 'btn-primary' : 'btn-outline-secondary');
            }
        });
    });
});

// Preview foto file upload
function previewFoto(input) {
    const zone  = document.getElementById('uploadZone');
    const img   = document.getElementById('previewImg');
    const text  = document.getElementById('uploadText');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            text.style.display = 'none';
            zone.classList.add('has-file');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Validasi form submission
document.getElementById('formPermohonan').addEventListener('submit', function(e) {
    const jenis = document.querySelector('input[name="jenis_izin"]:checked');
    const jenisAbsen = document.querySelector('input[name="jenis_absensi"]:checked');
    const fileUpload = document.getElementById('fotoBukti').value;
    const cameraCapture = document.getElementById('fotoBuktiCam').value;
    
    if (!jenis) {
        e.preventDefault();
        alert('Pilih jenis permohonan terlebih dahulu.');
        return;
    }
    if (jenis.value === 'Sakit' && !fileUpload && !cameraCapture) {
        e.preventDefault();
        alert('Foto bukti surat dokter/keterangan wajib dilampirkan untuk permohonan Sakit (Bisa Unggah File atau gunakan Kamera Langsung).');
        return;
    }
    if (jenisAbsen.value === 'mapel') {
        const checkedMapel = document.querySelectorAll('.mapel-checkbox:checked');
        if (checkedMapel.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu mata pelajaran.');
            return;
        }
    }
});
</script>

