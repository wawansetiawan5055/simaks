<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Leaflet CSS & JS for Interactive Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    .profil-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .profil-nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 0.92rem;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease;
        background: transparent;
    }
    .profil-nav-tabs .nav-link:hover {
        color: #0284c7;
        background: #f8fafc;
    }
    .profil-nav-tabs .nav-link.active {
        color: #0284c7 !important;
        border-bottom-color: #0284c7 !important;
        background: #ffffff !important;
        font-weight: 700;
    }
    .card-profil-section {
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.1rem;
        padding-bottom: 0.6rem;
        border-bottom: 1.5px solid #f1f5f9;
        display: flex;
        align-items: center;
    }
    .section-title i {
        margin-right: 8px;
        color: #0284c7;
    }
    .map-container-wrapper {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
    }
    #mapSchool {
        height: 380px;
        width: 100%;
        z-index: 1;
    }
    .map-search-bar {
        position: absolute;
        top: 10px;
        left: 50px;
        z-index: 1000;
        width: calc(100% - 60px);
        max-width: 380px;
    }
    .map-badge-coords {
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 1000;
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.76rem;
        backdrop-filter: blur(4px);
        font-family: monospace;
    }
    /* PRESET CARDS */
    .preset-card-btn {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .preset-card-btn:hover {
        border-color: #0284c7;
        background: #f0f9ff;
        transform: translateY(-1px);
    }
    .preset-card-btn.active {
        border-color: #0284c7;
        background: #e0f2fe;
        box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
    }
    /* PAPER SHEET PREVIEW */
    .paper-sheet-preview {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        padding: 24px 30px;
        max-width: 860px;
        margin: 0 auto;
    }
    .input-line-badge {
        font-size: 0.72rem;
        font-weight: 700;
        background: #e2e8f0;
        color: #334155;
        padding: 2px 7px;
        border-radius: 4px;
        margin-right: 6px;
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif; line-height: 1.25;">
                        Pengaturan Profil &amp; Lokasi Sekolah
                    </h4>
                    <span class="text-muted small" style="display: block; margin-top: 2px;">Kelola identitas resmi, logo ganda, kop surat dinas, serta koordinat peta satelit</span>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Profil Sekolah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <?php if (!empty($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= $_SESSION['pesan_sukses'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= $_SESSION['pesan_error'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>profil_sekolah/save" method="POST" enctype="multipart/form-data">
            
            <!-- NAV TABS UTAMA -->
            <ul class="nav profil-nav-tabs mb-3" id="profilSekolahTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-profil-link" data-toggle="pill" href="#tab-profil" role="tab" aria-controls="tab-profil" aria-selected="true">
                        <i class="fas fa-school mr-2 text-primary"></i> 1. Identitas &amp; Lokasi Sekolah
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-kop-link" data-toggle="pill" href="#tab-kop" role="tab" aria-controls="tab-kop" aria-selected="false">
                        <i class="fas fa-file-invoice mr-2 text-success"></i> 2. Format Kop Surat &amp; Dual Logo
                        <span class="badge badge-success ml-1.5 font-weight-normal" style="font-size: 0.72rem; padding: 3px 6px;">Live Editor</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="profilSekolahTabsContent">
                
                <!-- ======================================================== -->
                <!-- TAB 1: IDENTITAS, LEGALITAS & LOKASI PETA SATELIT        -->
                <!-- ======================================================== -->
                <div class="tab-pane fade show active" id="tab-profil" role="tabpanel" aria-labelledby="tab-profil-link">
                    <div class="row">
                        
                        <!-- KOLOM KIRI: IDENTITAS, LEGALITAS & LOGO -->
                        <div class="col-lg-6 mb-4">
                            
                            <!-- CARD 1: IDENTITAS DASAR -->
                            <div class="card card-profil-section mb-4">
                                <div class="card-body">
                                    <div class="section-title">
                                        <i class="fas fa-info-circle"></i> Identitas Pokok Sekolah
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small">Nama Sekolah <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_sekolah" class="form-control" value="<?= htmlspecialchars($profil['nama_sekolah'] ?? '') ?>" required placeholder="Contoh: SMA PLUS AL MANSHURIYAH">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small">NPSN <span class="text-danger">*</span></label>
                                                <input type="text" name="npsn" class="form-control" value="<?= htmlspecialchars($profil['npsn'] ?? '') ?>" required placeholder="8 Digit NPSN">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small">Status Sekolah</label>
                                                <select name="status_sekolah" class="form-control">
                                                    <option value="Swasta" <?= ($profil['status_sekolah'] ?? '') == 'Swasta' ? 'selected' : '' ?>>Swasta</option>
                                                    <option value="Negeri" <?= ($profil['status_sekolah'] ?? '') == 'Negeri' ? 'selected' : '' ?>>Negeri</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small">Bentuk Pendidikan</label>
                                                <select name="bentuk_pendidikan" class="form-control">
                                                    <option value="SMA/MA" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SMA/MA' ? 'selected' : '' ?>>SMA/MA</option>
                                                    <option value="SMK" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SMK' ? 'selected' : '' ?>>SMK</option>
                                                    <option value="SMP/MTs" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SMP/MTs' ? 'selected' : '' ?>>SMP/MTs</option>
                                                    <option value="SD/MI" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SD/MI' ? 'selected' : '' ?>>SD/MI</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small">Kurikulum Utama</label>
                                                <select name="kurikulum" class="form-control">
                                                    <option value="Kurikulum Merdeka" <?= ($profil['kurikulum'] ?? '') == 'Kurikulum Merdeka' ? 'selected' : '' ?>>Kurikulum Merdeka</option>
                                                    <option value="Kurikulum 2013" <?= ($profil['kurikulum'] ?? '') == 'Kurikulum 2013' ? 'selected' : '' ?>>Kurikulum 2013</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small">Nama Kepala Sekolah</label>
                                        <input type="text" name="nama_kepala_sekolah" class="form-control" value="<?= htmlspecialchars($profil['nama_kepala_sekolah'] ?? '') ?>" placeholder="Nama Lengkap beserta Gelar">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small">Yayasan / Penyelenggara</label>
                                        <input type="text" name="nama_yayasan" class="form-control" value="<?= htmlspecialchars($profil['nama_yayasan'] ?? '') ?>" placeholder="Nama Yayasan / Dinas Pendidikan">
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark small">Moto / Slogan Sekolah</label>
                                        <input type="text" name="moto" class="form-control" value="<?= htmlspecialchars($profil['moto'] ?? '') ?>" placeholder="Contoh: Religius, Unggul, dan Berakhlak Mulia">
                                    </div>

                                </div>
                            </div>

                            <!-- CARD 2: KONTAK & LEGALITAS -->
                            <div class="card card-profil-section mb-0">
                                <div class="card-body">
                                    <div class="section-title">
                                        <i class="fas fa-address-book"></i> Kontak &amp; Legalitas
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small"><i class="fas fa-phone-alt mr-1 text-muted"></i> Nomor Telepon / WA</label>
                                                <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($profil['telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small"><i class="fas fa-envelope mr-1 text-muted"></i> Email Sekolah</label>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profil['email'] ?? '') ?>" placeholder="email@sekolah.sch.id">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small"><i class="fas fa-globe mr-1 text-muted"></i> Alamat Website Resmi</label>
                                        <input type="text" name="website" class="form-control" value="<?= htmlspecialchars($profil['website'] ?? '') ?>" placeholder="https://smaplusalmanshuriyah.sch.id">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small">SK Izin Operasional</label>
                                                <input type="text" name="sk_izin_operasional" class="form-control" value="<?= htmlspecialchars($profil['sk_izin_operasional'] ?? '') ?>" placeholder="Nomor SK Izin">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small">SK Akreditasi</label>
                                                <input type="text" name="sk_akreditasi" class="form-control" value="<?= htmlspecialchars($profil['sk_akreditasi'] ?? '') ?>" placeholder="Nomor SK / Status Akreditasi">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark small"><i class="fas fa-image mr-1 text-muted"></i> Logo Utama Sekolah</label>
                                        <div class="custom-file mb-2">
                                            <input type="file" class="custom-file-input" id="logo_sekolah" name="logo_sekolah" accept="image/*">
                                            <label class="custom-file-label" for="logo_sekolah">Pilih berkas gambar logo...</label>
                                        </div>
                                        <?php if (!empty($profil['logo'])): ?>
                                            <div class="d-flex align-items-center p-2 bg-light rounded border mt-2">
                                                <img src="assets/img/<?= htmlspecialchars($profil['logo']) ?>" alt="Logo Sekolah" id="preview_logo" class="img-thumbnail mr-3" style="max-height: 50px; max-width: 50px; object-fit: contain;">
                                                <div>
                                                    <div class="font-weight-bold small text-dark"><?= htmlspecialchars($profil['logo']) ?></div>
                                                    <small class="text-muted">Logo saat ini aktif di sistem, rapor, &amp; portal</small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- KOLOM KANAN: LOKASI & PETA SATELIT INTERAKTIF -->
                        <div class="col-lg-6 mb-4">
                            <div class="card card-profil-section h-100">
                                <div class="card-body d-flex flex-column">
                                    
                                    <div class="section-title">
                                        <i class="fas fa-map-marked-alt"></i> Lokasi &amp; Peta Satelit Interaktif
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small">Alamat Lengkap Sekolah <span class="text-danger">*</span></label>
                                        <textarea name="alamat" id="alamat_sekolah" class="form-control" rows="2" placeholder="Nama Jalan, RT/RW, Dusun/Desa, Kecamatan, Kabupaten/Kota, Provinsi"><?= htmlspecialchars($profil['alamat'] ?? '') ?></textarea>
                                    </div>

                                    <!-- INPUT KOORDINAT & LAT / LNG -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="font-weight-bold text-dark small">Latitude (Garis Lintang)</label>
                                                <input type="text" id="input_latitude" class="form-control form-control-sm font-weight-bold text-primary" placeholder="-6.917464" style="font-family: monospace;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label class="font-weight-bold text-dark small">Longitude (Garis Bujur)</label>
                                                <input type="text" id="input_longitude" class="form-control form-control-sm font-weight-bold text-primary" placeholder="106.929384" style="font-family: monospace;">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- KOORDINAT GABUNGAN (SIMPAN KE DB) -->
                                    <div class="form-group mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="font-weight-bold text-dark small m-0">Titik Koordinat (Lat, Lng)</label>
                                            <small class="text-muted font-italic">Format: <code>latitude, longitude</code></small>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light"><i class="fas fa-crosshairs text-danger"></i></span>
                                            </div>
                                            <input type="text" name="koordinat" id="input_koordinat" class="form-control font-weight-bold" value="<?= htmlspecialchars($profil['koordinat'] ?? '') ?>" placeholder="-6.917464, 106.929384" style="font-family: monospace;">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-primary" id="btn_my_location" title="Dapatkan Lokasi GPS Perangkat Ini">
                                                    <i class="fas fa-location-arrow mr-1"></i> Lokasi Saya (GPS)
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PETA INTERAKTIF SATELIT & JALAN -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small font-weight-bold text-dark">
                                            <i class="fas fa-satellite mr-1 text-info"></i> Pilih Titik Pin di Peta:
                                        </span>
                                        <small class="text-muted">Geser pin (drag) atau klik langsung pada peta</small>
                                    </div>

                                    <div class="map-container-wrapper flex-grow-1 mb-3" style="min-height: 300px;">
                                        <!-- SEARCH BAR ATAS PETA -->
                                        <div class="map-search-bar">
                                            <div class="input-group input-group-sm shadow-sm">
                                                <input type="text" id="map_search_query" class="form-control border-0" placeholder="Ketik nama tempat/kecamatan/sekolah...">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary px-3" id="btn_map_search" title="Cari Lokasi">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- MAP ELEMENT -->
                                        <div id="mapSchool"></div>

                                        <!-- BADGE REALTIME KOORDINAT -->
                                        <div class="map-badge-coords shadow-sm">
                                            <span id="badge_coord_display"><i class="fas fa-map-marker-alt text-danger mr-1"></i> Titik: Belum Dipilih</span>
                                        </div>
                                    </div>

                                    <div class="p-2.5 bg-light rounded border small text-muted">
                                        <i class="fas fa-lightbulb text-warning mr-1"></i>
                                        <strong>Tips:</strong> Anda bisa mengganti tampilan antara <strong>Peta Satelit Realistis (ESRI Imagery)</strong> dan <strong>Peta Jalan (OpenStreetMap)</strong> melalui ikon lapisan di pojok kanan atas peta.
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ======================================================== -->
                <!-- TAB 2: FORMAT KOP SURAT RESMI & LIVE PREVIEW             -->
                <!-- ======================================================== -->
                <div class="tab-pane fade" id="tab-kop" role="tabpanel" aria-labelledby="tab-kop-link">
                    
                    <!-- 1. ATAS: LEMBAR PREVIEW KOP SURAT (FULL PROPORTION) -->
                    <div class="card card-profil-section mb-4" style="background: #f8fafc; border: 1.5px solid #cbd5e1;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="font-weight-bold text-dark m-0">
                                        <i class="fas fa-eye text-primary mr-1.5"></i> Lembar Pratinjau Kertas Kop Surat (Realtime Preview)
                                    </h6>
                                    <small class="text-muted">Pratinjau proporsional tata letak kop surat sesuai ukuran cetak dokumen naskah</small>
                                </div>
                                <span class="badge badge-success px-3 py-1.5 font-weight-normal mt-2 mt-md-0" style="font-size: 0.8rem; border-radius: 6px;">
                                    <i class="fas fa-sync-alt fa-spin mr-1"></i> Line-Height 1.15 Rapat &amp; Presisi
                                </span>
                            </div>

                            <!-- KERTAS PUTIH KOP SURAT -->
                            <div class="paper-sheet-preview">
                                
                                <div id="live_kop_container" class="simaks-kop-container" style="font-family: 'Times New Roman', Times, serif; color: #000000;">
                                    <table class="simaks-kop-table" style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <!-- Logo Kiri Preview -->
                                            <td id="live_logo_left_td" class="simaks-kop-logo-left" style="width: 85px; text-align: left; vertical-align: middle; padding-right: 12px;">
                                                <img id="live_logo_left_img" src="<?= !empty($profil['logo_kiri']) ? 'assets/img/' . htmlspecialchars($profil['logo_kiri']) : (!empty($profil['logo']) ? 'assets/img/' . htmlspecialchars($profil['logo']) : '') ?>" alt="Logo Kiri" class="simaks-kop-logo-img" style="max-width: 78px; max-height: 78px; object-fit: contain;">
                                            </td>

                                            <!-- Teks Tengah Preview -->
                                            <td class="simaks-kop-center" style="text-align: center; line-height: 1.15; vertical-align: middle;">
                                                <div id="live_kop_b1" class="simaks-kop-b1" style="font-size: 11.5pt; font-weight: bold; text-transform: uppercase; margin: 0 0 1px 0; line-height: 1.15;">
                                                    <?= htmlspecialchars($profil['kop_baris_1'] ?? ($profil['nama_yayasan'] ?: 'YAYASAN TARBIYATUSSHIBYAN INDONESIA')) ?>
                                                </div>
                                                <div id="live_kop_b2" class="simaks-kop-b2" style="font-size: 11pt; font-weight: bold; text-transform: uppercase; margin: 0 0 1px 0; line-height: 1.15; <?= empty($profil['kop_baris_2']) ? 'display: none;' : '' ?>">
                                                    <?= htmlspecialchars($profil['kop_baris_2'] ?? '') ?>
                                                </div>
                                                <div id="live_kop_b3" class="simaks-kop-b3" style="font-size: 14.5pt; font-weight: 900; text-transform: uppercase; margin: 1px 0 2px 0; line-height: 1.15; color: #000;">
                                                    <?= htmlspecialchars($profil['kop_baris_3'] ?? ($profil['nama_sekolah'] ?: 'SMA PLUS AL MANSHURIYAH')) ?>
                                                </div>
                                                <div id="live_kop_b4" class="simaks-kop-b4" style="font-size: 8.5pt; font-weight: bold; margin: 0 0 1px 0; line-height: 1.15;">
                                                    <?= htmlspecialchars($profil['kop_baris_4'] ?? ('STATUS AKREDITASI: ' . ($profil['sk_akreditasi'] ?: 'A') . ' | NPSN: ' . ($profil['npsn'] ?: '20247166'))) ?>
                                                </div>
                                                <div id="live_kop_b5" class="simaks-kop-b5" style="font-size: 8pt; margin: 0; line-height: 1.15;">
                                                    <?= htmlspecialchars($profil['kop_baris_5'] ?? (($profil['alamat'] ?? '') . ' | Telp: ' . ($profil['telepon'] ?? '-') . ' | Email: ' . ($profil['email'] ?? '-'))) ?>
                                                </div>
                                            </td>

                                            <!-- Logo Kanan Preview -->
                                            <td id="live_logo_right_td" class="simaks-kop-logo-right" style="width: 85px; text-align: right; vertical-align: middle; padding-left: 12px;">
                                                <img id="live_logo_right_img" src="<?= !empty($profil['logo_kanan']) ? 'assets/img/' . htmlspecialchars($profil['logo_kanan']) : (!empty($profil['logo']) ? 'assets/img/' . htmlspecialchars($profil['logo']) : '') ?>" alt="Logo Kanan" class="simaks-kop-logo-img" style="max-width: 78px; max-height: 78px; object-fit: contain;">
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Divider Line Preview -->
                                    <div id="live_kop_divider" class="simaks-kop-divider-double" style="border-top: 3px double #000000; margin-top: 6px; margin-bottom: 10px;"></div>
                                </div>

                                <!-- SIMULASI DOKUMEN -->
                                <div class="text-center text-muted py-2 px-2 rounded" style="font-size: 0.78rem; font-family: sans-serif; background: #f8fafc; border: 1px dashed #e2e8f0;">
                                    <div class="font-weight-bold text-dark">SURAT KETERANGAN / NASKAH SOAL CBT / LAPORAN RESMI</div>
                                    <div>Nomor: 421.3 / 108 / SMA-AM / IX / <?= date('Y') ?></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 2. BAWAH: FORM KONTROL (PRESET & DUAL LOGO + EDITOR TEKS 5 BARIS) -->
                    <div class="row">
                        
                        <!-- KOLOM KIRI: PRESET & DUAL LOGO -->
                        <div class="col-lg-5 mb-4 mb-lg-0">
                            
                            <!-- CARD A: PRESET MODEL KOP -->
                            <div class="card card-profil-section mb-4">
                                <div class="card-body">
                                    <div class="section-title">
                                        <i class="fas fa-magic"></i> Template Standar Dinas Cepat
                                    </div>
                                    <p class="text-muted small mb-3">Pilih salah satu template untuk mengisi susunan baris teks kop secara otomatis:</p>
                                    
                                    <div class="d-flex flex-column" style="gap: 8px;">
                                        <button type="button" class="preset-card-btn btn-preset-kop" data-preset="yayasan">
                                            <div class="mr-2 text-primary font-weight-bold" style="font-size: 1.1rem;"><i class="fas fa-landmark"></i></div>
                                            <div>
                                                <div class="font-weight-bold text-dark small">Standar Yayasan / Swasta</div>
                                                <small class="text-muted">Nama Yayasan &bull; Satuan Pendidikan &bull; Akreditasi &bull; Alamat</small>
                                            </div>
                                        </button>

                                        <button type="button" class="preset-card-btn btn-preset-kop" data-preset="provinsi">
                                            <div class="mr-2 text-info font-weight-bold" style="font-size: 1.1rem;"><i class="fas fa-building"></i></div>
                                            <div>
                                                <div class="font-weight-bold text-dark small">Standar Dinas Provinsi (SMA/SMK)</div>
                                                <small class="text-muted">Pemda Prov &bull; Cabang Dinas &bull; Sekolah &bull; Legalitas</small>
                                            </div>
                                        </button>

                                        <button type="button" class="preset-card-btn btn-preset-kop" data-preset="kabupaten">
                                            <div class="mr-2 text-secondary font-weight-bold" style="font-size: 1.1rem;"><i class="fas fa-city"></i></div>
                                            <div>
                                                <div class="font-weight-bold text-dark small">Standar Kab/Kota (SMP/SD)</div>
                                                <small class="text-muted">Pemkab / Pemkot &bull; Dinas Pendidikan &bull; Sekolah</small>
                                            </div>
                                        </button>

                                        <button type="button" class="preset-card-btn btn-preset-kop" data-preset="nasional">
                                            <div class="mr-2 text-danger font-weight-bold" style="font-size: 1.1rem;"><i class="fas fa-flag"></i></div>
                                            <div>
                                                <div class="font-weight-bold text-dark small">Standar Nasional (Kementerian)</div>
                                                <small class="text-muted">Kemendikbudristek / Kemenag &bull; Ditjen &bull; Satuan</small>
                                            </div>
                                        </button>
                                    </div>
                                    <input type="hidden" name="model_kop" id="input_model_kop" value="<?= htmlspecialchars($profil['model_kop'] ?? 'yayasan') ?>">
                                </div>
                            </div>

                            <!-- CARD B: DUAL LOGO SETTINGS -->
                            <div class="card card-profil-section mb-0">
                                <div class="card-body">
                                    <div class="section-title">
                                        <i class="fas fa-images"></i> Pengaturan Logo Kiri &amp; Kanan
                                    </div>

                                    <!-- Logo Kiri -->
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small font-weight-bold text-dark"><i class="fas fa-shield-alt text-primary mr-1"></i> Logo Kiri (Yayasan / Pemda)</span>
                                            <div class="custom-control custom-switch custom-switch-sm">
                                                <input type="checkbox" class="custom-control-input" id="switch_logo_kiri" name="show_logo_kiri" value="1" <?= (!isset($profil['show_logo_kiri']) || $profil['show_logo_kiri']) ? 'checked' : '' ?>>
                                                <label class="custom-control-label small text-muted" for="switch_logo_kiri">Tampil</label>
                                            </div>
                                        </div>
                                        <div class="custom-file custom-file-sm mb-1">
                                            <input type="file" class="custom-file-input" id="input_logo_kiri" name="logo_kiri_file" accept="image/*">
                                            <label class="custom-file-label small" for="input_logo_kiri">Pilih berkas Logo Kiri...</label>
                                        </div>
                                        <?php if (!empty($profil['logo_kiri'])): ?>
                                            <small class="text-muted font-italic d-block text-truncate">Aktif: <?= htmlspecialchars($profil['logo_kiri']) ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Logo Kanan -->
                                    <div class="p-3 bg-light rounded border mb-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small font-weight-bold text-dark"><i class="fas fa-school text-info mr-1"></i> Logo Kanan (Sekolah)</span>
                                            <div class="custom-control custom-switch custom-switch-sm">
                                                <input type="checkbox" class="custom-control-input" id="switch_logo_kanan" name="show_logo_kanan" value="1" <?= (!isset($profil['show_logo_kanan']) || $profil['show_logo_kanan']) ? 'checked' : '' ?>>
                                                <label class="custom-control-label small text-muted" for="switch_logo_kanan">Tampil</label>
                                            </div>
                                        </div>
                                        <div class="custom-file custom-file-sm mb-1">
                                            <input type="file" class="custom-file-input" id="input_logo_kanan" name="logo_kanan_file" accept="image/*">
                                            <label class="custom-file-label small" for="input_logo_kanan">Pilih berkas Logo Kanan...</label>
                                        </div>
                                        <?php if (!empty($profil['logo_kanan'])): ?>
                                            <small class="text-muted font-italic d-block text-truncate">Aktif: <?= htmlspecialchars($profil['logo_kanan']) ?></small>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- KOLOM KANAN: EDITOR 5 BARIS TEKS KOP & GAYA GARIS -->
                        <div class="col-lg-7">
                            <div class="card card-profil-section h-100">
                                <div class="card-body">
                                    <div class="section-title">
                                        <i class="fas fa-edit"></i> Susunan Teks Baris Kop Surat (1 s/d 5)
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">
                                            <span class="input-line-badge">Baris 1</span> Instansi Induk / Nama Yayasan / Pemda
                                        </label>
                                        <input type="text" name="kop_baris_1" id="input_kop_b1" class="form-control font-weight-bold" value="<?= htmlspecialchars($profil['kop_baris_1'] ?? ($profil['nama_yayasan'] ?: 'YAYASAN TARBIYATUSSHIBYAN INDONESIA')) ?>" placeholder="Contoh: YAYASAN TARBIYATUSSHIBYAN INDONESIA">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">
                                            <span class="input-line-badge">Baris 2</span> Cabang Dinas / Pengelola (Opsional)
                                        </label>
                                        <input type="text" name="kop_baris_2" id="input_kop_b2" class="form-control font-weight-bold" value="<?= htmlspecialchars($profil['kop_baris_2'] ?? '') ?>" placeholder="Contoh: DINAS PENDIDIKAN - CABANG DINAS WILAYAH V (kosongkan jika tidak ada)">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">
                                            <span class="input-line-badge bg-primary text-white">Baris 3</span> Nama Satuan Pendidikan (Sekolah / Madrasah) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kop_baris_3" id="input_kop_b3" class="form-control font-weight-bold text-primary" style="font-size: 1.05rem;" value="<?= htmlspecialchars($profil['kop_baris_3'] ?? ($profil['nama_sekolah'] ?: 'SMA PLUS AL MANSHURIYAH')) ?>" placeholder="Contoh: SMA PLUS AL MANSHURIYAH">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">
                                            <span class="input-line-badge">Baris 4</span> NPSN, Akreditasi &amp; Legalitas
                                        </label>
                                        <input type="text" name="kop_baris_4" id="input_kop_b4" class="form-control" value="<?= htmlspecialchars($profil['kop_baris_4'] ?? ('STATUS AKREDITASI: ' . ($profil['sk_akreditasi'] ?: 'A') . ' | NPSN: ' . ($profil['npsn'] ?: '20247166'))) ?>" placeholder="Contoh: STATUS AKREDITASI: A | NPSN: 20247166">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold text-dark small mb-1">
                                            <span class="input-line-badge">Baris 5</span> Alamat, Telepon, Email, Website &amp; Medsos
                                        </label>
                                        <textarea name="kop_baris_5" id="input_kop_b5" class="form-control" rows="2" placeholder="Contoh: Jl. Kalaparea KM. 5 RT 03 RW 09 Desa Kalaparea Kec. Nagrak Kab. Sukabumi 43356 | Telp: 0812-3456-7890 | Email: sekolah@gmail.com"><?= htmlspecialchars($profil['kop_baris_5'] ?? (($profil['alamat'] ?? '') . ' | Telp: ' . ($profil['telepon'] ?? '-') . ' | Email: ' . ($profil['email'] ?? '-'))) ?></textarea>
                                    </div>

                                    <!-- GAYA GARIS PEMBATAS -->
                                    <div class="form-group mb-3 pt-2 border-top">
                                        <label class="font-weight-bold text-dark small mb-1">
                                            <i class="fas fa-minus text-dark mr-1"></i> Gaya Garis Pembatas Kop:
                                        </label>
                                        <select name="style_garis" id="select_style_garis" class="form-control">
                                            <option value="double" <?= ($profil['style_garis'] ?? 'double') === 'double' ? 'selected' : '' ?>>Garis Ganda Dinas Standar (Tebal-Tipis 3px)</option>
                                            <option value="thick" <?= ($profil['style_garis'] ?? '') === 'thick' ? 'selected' : '' ?>>Garis Tunggal Tebal (2.5px)</option>
                                            <option value="single" <?= ($profil['style_garis'] ?? '') === 'single' ? 'selected' : '' ?>>Garis Tunggal Sedang (1.5px)</option>
                                            <option value="none" <?= ($profil['style_garis'] ?? '') === 'none' ? 'selected' : '' ?>>Tanpa Garis</option>
                                        </select>
                                    </div>

                                    <!-- MARGIN DOKUMEN CETAK DINAMIS -->
                                    <div class="form-group mb-0 pt-2 border-top">
                                        <label class="font-weight-bold text-dark small mb-2 d-block">
                                            <i class="fas fa-ruler-combined text-primary mr-1"></i> Pengaturan Margin Kertas Dokumen Cetak (PDF):
                                        </label>
                                        <div class="row">
                                            <div class="col-6 col-sm-3 mb-2">
                                                <label class="small text-muted mb-0">Atas (mm)</label>
                                                <input type="number" name="margin_atas" class="form-control font-weight-bold text-center" value="<?= htmlspecialchars($profil['margin_atas'] ?? 15) ?>" min="5" max="50">
                                            </div>
                                            <div class="col-6 col-sm-3 mb-2">
                                                <label class="small text-muted mb-0">Bawah (mm)</label>
                                                <input type="number" name="margin_bawah" class="form-control font-weight-bold text-center" value="<?= htmlspecialchars($profil['margin_bawah'] ?? 15) ?>" min="5" max="50">
                                            </div>
                                            <div class="col-6 col-sm-3 mb-2">
                                                <label class="small text-muted mb-0">Kiri (mm)</label>
                                                <input type="number" name="margin_kiri" class="form-control font-weight-bold text-center" value="<?= htmlspecialchars($profil['margin_kiri'] ?? 20) ?>" min="5" max="50">
                                            </div>
                                            <div class="col-6 col-sm-3 mb-2">
                                                <label class="small text-muted mb-0">Kanan (mm)</label>
                                                <input type="number" name="margin_kanan" class="form-control font-weight-bold text-center" value="<?= htmlspecialchars($profil['margin_kanan'] ?? 15) ?>" min="5" max="50">
                                            </div>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Standar dinas: Atas 15mm, Bawah 15mm, Kiri 20mm (ruang jilid), Kanan 15mm.</small>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <!-- TOMBOL SUBMIT FIXED / STICKY BOTTOM CARD -->
            <div class="card shadow-sm border-0 mt-4 mb-4" style="border-radius: 12px; background: #ffffff;">
                <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div class="text-muted small mb-2 mb-md-0">
                        <i class="fas fa-shield-alt text-success mr-1"></i> Data pada <strong>Tab 1 (Profil &amp; Lokasi)</strong> serta <strong>Tab 2 (Format Kop Surat)</strong> akan otomatis tersimpan bersamaan.
                    </div>
                    <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm rounded-pill">
                        <i class="fas fa-save mr-1.5"></i> Simpan Profil &amp; Format Kop Surat
                    </button>
                </div>
            </div>

        </form>
    </div>
</section>

<!-- JAVASCRIPT LEAFLET & KOORDINAT HANDLER -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Koordinat Default (Sukabumi / Jawa Barat Default jika kosong)
    const defaultLat = -6.917464;
    const defaultLng = 106.929384;

    const inputKoordinat = document.getElementById('input_koordinat');
    const inputLat = document.getElementById('input_latitude');
    const inputLng = document.getElementById('input_longitude');
    const badgeCoord = document.getElementById('badge_coord_display');

    let initialLat = defaultLat;
    let initialLng = defaultLng;
    let hasSavedCoord = false;

    // Parse koordinat yang ada di database
    if (inputKoordinat && inputKoordinat.value.trim() !== '') {
        const parts = inputKoordinat.value.split(',');
        if (parts.length === 2) {
            const pLat = parseFloat(parts[0].trim());
            const pLng = parseFloat(parts[1].trim());
            if (!isNaN(pLat) && !isNaN(pLng)) {
                initialLat = pLat;
                initialLng = pLng;
                hasSavedCoord = true;
            }
        }
    }

    // Set initial field values
    if (inputLat) inputLat.value = initialLat.toFixed(6);
    if (inputLng) inputLng.value = initialLng.toFixed(6);
    if (badgeCoord) {
        badgeCoord.innerHTML = '<i class="fas fa-map-marker-alt text-danger mr-1"></i> Titik: ' + initialLat.toFixed(6) + ', ' + initialLng.toFixed(6);
    }

    // 2. Buat Peta Leaflet
    const map = L.map('mapSchool', {
        center: [initialLat, initialLng],
        zoom: hasSavedCoord ? 17 : 14,
        zoomControl: true
    });

    // 3. Tile Layers: OpenStreetMap & ESRI Satellite
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    });

    // Default gunakan Satellite Layer untuk kemewahan tampilan
    satelliteLayer.addTo(map);

    // Layer Control
    const baseMaps = {
        "🛰️ Satelit (Realistis)": satelliteLayer,
        "🗺️ Peta Jalan (OSM)": osmLayer
    };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

    // 4. Marker dengan Pin Merah & Drag
    const schoolIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    const marker = L.marker([initialLat, initialLng], {
        draggable: true,
        icon: schoolIcon
    }).addTo(map);

    marker.bindPopup("<b>Lokasi Sekolah</b><br>Geser pin ini untuk menyesuaikan titik tepat bangunan sekolah.").openPopup();

    // 5. Update functions
    function updateCoordinates(lat, lng, panMap = false) {
        if (inputLat) inputLat.value = lat.toFixed(6);
        if (inputLng) inputLng.value = lng.toFixed(6);
        if (inputKoordinat) inputKoordinat.value = lat.toFixed(6) + ', ' + lng.toFixed(6);
        if (badgeCoord) {
            badgeCoord.innerHTML = '<i class="fas fa-map-marker-alt text-danger mr-1"></i> Titik: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
        }
        marker.setLatLng([lat, lng]);
        if (panMap) {
            map.setView([lat, lng], 17);
        }
    }

    // Marker Dragged
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        updateCoordinates(position.lat, position.lng, false);
    });

    // Map Clicked
    map.on('click', function(e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng, false);
    });

    // Manual Input Listeners
    if (inputLat && inputLng) {
        const onManualChange = function() {
            const mLat = parseFloat(inputLat.value.trim());
            const mLng = parseFloat(inputLng.value.trim());
            if (!isNaN(mLat) && !isNaN(mLng)) {
                updateCoordinates(mLat, mLng, true);
            }
        };
        inputLat.addEventListener('change', onManualChange);
        inputLng.addEventListener('change', onManualChange);
    }

    // 6. Tombol "Lokasi Saya (GPS Browser)"
    const btnMyLocation = document.getElementById('btn_my_location');
    if (btnMyLocation) {
        btnMyLocation.addEventListener('click', function() {
            if ("geolocation" in navigator) {
                const origHtml = btnMyLocation.innerHTML;
                btnMyLocation.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mendeteksi GPS...';
                btnMyLocation.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        btnMyLocation.innerHTML = origHtml;
                        btnMyLocation.disabled = false;
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        updateCoordinates(userLat, userLng, true);
                    },
                    function(error) {
                        btnMyLocation.innerHTML = origHtml;
                        btnMyLocation.disabled = false;
                        alert('Tidak dapat mendeteksi lokasi GPS: ' + error.message + '\nSilakan geser pin manual pada peta.');
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                alert('Browser Anda tidak mendukung fitur Geolocation.');
            }
        });
    }

    // 7. Search Bar Lokasi via Nominatim OpenStreetMap
    const btnSearch = document.getElementById('btn_map_search');
    const inputSearch = document.getElementById('map_search_query');

    function searchPlace() {
        const q = inputSearch.value.trim();
        if (!q) return;

        const origHtml = btnSearch.innerHTML;
        btnSearch.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btnSearch.disabled = true;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&countrycodes=id&limit=1`)
            .then(res => res.json())
            .then(data => {
                btnSearch.innerHTML = origHtml;
                btnSearch.disabled = false;

                if (data && data.length > 0) {
                    const result = data[0];
                    updateCoordinates(parseFloat(result.lat), parseFloat(result.lon), true);
                } else {
                    alert('Lokasi tidak ditemukan.');
                }
            })
            .catch(err => {
                btnSearch.innerHTML = origHtml;
                btnSearch.disabled = false;
                alert('Gagal mencari lokasi: ' + err.message);
            });
    }

    if (btnSearch) btnSearch.addEventListener('click', searchPlace);
    if (inputSearch) {
        inputSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); searchPlace(); }
        });
    }

    // =========================================================
    // JAVASCRIPT KOP SURAT LIVE EDITOR & DUAL LOGO HANDLER
    // =========================================================
    const b1Input = document.getElementById('input_kop_b1');
    const b2Input = document.getElementById('input_kop_b2');
    const b3Input = document.getElementById('input_kop_b3');
    const b4Input = document.getElementById('input_kop_b4');
    const b5Input = document.getElementById('input_kop_b5');
    const styleGarisSelect = document.getElementById('select_style_garis');

    const liveB1 = document.getElementById('live_kop_b1');
    const liveB2 = document.getElementById('live_kop_b2');
    const liveB3 = document.getElementById('live_kop_b3');
    const liveB4 = document.getElementById('live_kop_b4');
    const liveB5 = document.getElementById('live_kop_b5');
    const liveDivider = document.getElementById('live_kop_divider');

    const switchLogoLeft = document.getElementById('switch_logo_kiri');
    const switchLogoRight = document.getElementById('switch_logo_kanan');
    const tdLogoLeft = document.getElementById('live_logo_left_td');
    const tdLogoRight = document.getElementById('live_logo_right_td');
    const imgLogoLeft = document.getElementById('live_logo_left_img');
    const imgLogoRight = document.getElementById('live_logo_right_img');

    const inputLogoLeftFile = document.getElementById('input_logo_kiri');
    const inputLogoRightFile = document.getElementById('input_logo_kanan');

    function syncLiveKop() {
        if (liveB1 && b1Input) { liveB1.innerText = b1Input.value.trim(); liveB1.style.display = b1Input.value.trim() ? 'block' : 'none'; }
        if (liveB2 && b2Input) { liveB2.innerText = b2Input.value.trim(); liveB2.style.display = b2Input.value.trim() ? 'block' : 'none'; }
        if (liveB3 && b3Input) { liveB3.innerText = b3Input.value.trim(); liveB3.style.display = b3Input.value.trim() ? 'block' : 'none'; }
        if (liveB4 && b4Input) { liveB4.innerText = b4Input.value.trim(); liveB4.style.display = b4Input.value.trim() ? 'block' : 'none'; }
        if (liveB5 && b5Input) { liveB5.innerText = b5Input.value.trim(); liveB5.style.display = b5Input.value.trim() ? 'block' : 'none'; }

        // Garis Pembatas
        if (liveDivider && styleGarisSelect) {
            const st = styleGarisSelect.value;
            if (st === 'double') { liveDivider.style.display = 'block'; liveDivider.style.borderTop = '3px double #000000'; }
            else if (st === 'thick') { liveDivider.style.display = 'block'; liveDivider.style.borderTop = '2.5px solid #000000'; }
            else if (st === 'single') { liveDivider.style.display = 'block'; liveDivider.style.borderTop = '1.5px solid #000000'; }
            else { liveDivider.style.display = 'none'; }
        }

        // Toggles Logo
        if (tdLogoLeft && switchLogoLeft) tdLogoLeft.style.display = switchLogoLeft.checked ? 'table-cell' : 'none';
        if (tdLogoRight && switchLogoRight) tdLogoRight.style.display = switchLogoRight.checked ? 'table-cell' : 'none';
    }

    [b1Input, b2Input, b3Input, b4Input, b5Input].forEach(inp => { if (inp) inp.addEventListener('input', syncLiveKop); });
    if (styleGarisSelect) styleGarisSelect.addEventListener('change', syncLiveKop);
    if (switchLogoLeft) switchLogoLeft.addEventListener('change', syncLiveKop);
    if (switchLogoRight) switchLogoRight.addEventListener('change', syncLiveKop);

    // Preset Buttons
    document.querySelectorAll('.btn-preset-kop').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-preset-kop').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const preset = this.getAttribute('data-preset');
            const namaSekolah = document.querySelector('input[name="nama_sekolah"]').value.trim() || 'SMA PLUS AL MANSHURIYAH';
            const namaYayasan = document.querySelector('input[name="nama_yayasan"]').value.trim() || 'YAYASAN TARBIYATUSSHIBYAN INDONESIA';
            const npsnVal = document.querySelector('input[name="npsn"]').value.trim() || '20247166';
            const akreditasiVal = document.querySelector('input[name="sk_akreditasi"]').value.trim() || 'A';
            const alamatVal = document.getElementById('alamat_sekolah').value.trim() || 'Jl. Kalaparea KM. 5 Desa Kalaparea Kec. Nagrak Kab. Sukabumi';
            const telpVal = document.querySelector('input[name="telepon"]').value.trim() || '0812-3456-7890';
            const emailVal = document.querySelector('input[name="email"]').value.trim() || 'sekolah@sch.id';

            document.getElementById('input_model_kop').value = preset;

            if (preset === 'yayasan') {
                b1Input.value = namaYayasan.toUpperCase(); b2Input.value = ''; b3Input.value = namaSekolah.toUpperCase();
                b4Input.value = `STATUS AKREDITASI: ${akreditasiVal} | NPSN: ${npsnVal}`; b5Input.value = `${alamatVal} | Telp: ${telpVal} | Email: ${emailVal}`;
            } else if (preset === 'provinsi') {
                b1Input.value = 'PEMERINTAH DAERAH PROVINSI JAWA BARAT'; b2Input.value = 'DINAS PENDIDIKAN - CABANG DINAS PENDIDIKAN WILAYAH V';
                b3Input.value = namaSekolah.toUpperCase(); b4Input.value = `NPSN: ${npsnVal} | STATUS AKREDITASI: ${akreditasiVal}`; b5Input.value = `${alamatVal} | Email: ${emailVal}`;
            } else if (preset === 'kabupaten') {
                b1Input.value = 'PEMERINTAH KABUPATEN SUKABUMI'; b2Input.value = 'DINAS PENDIDIKAN DAN KEBUDAYAAN';
                b3Input.value = namaSekolah.toUpperCase(); b4Input.value = `NPSN: ${npsnVal} | AKREDITASI: ${akreditasiVal}`; b5Input.value = `${alamatVal} | Telp: ${telpVal} | Email: ${emailVal}`;
            } else if (preset === 'nasional') {
                b1Input.value = 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI'; b2Input.value = 'DIREKTORAT JENDERAL PENDIDIKAN DASAR DAN MENENGAH';
                b3Input.value = namaSekolah.toUpperCase(); b4Input.value = `NPSN: ${npsnVal} | STATUS AKREDITASI: ${akreditasiVal}`; b5Input.value = `${alamatVal} | Email: ${emailVal}`;
            }
            syncLiveKop();
        });
    });

    // File Preview for Dual Logos
    function setupLogoFileInput(inputEl, imgEl) {
        if (!inputEl) return;
        inputEl.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih berkas logo...';
            let label = this.nextElementSibling;
            if (label) label.innerText = fileName;
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(evt) { if (imgEl) imgEl.src = evt.target.result; };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
    setupLogoFileInput(inputLogoLeftFile, imgLogoLeft);
    setupLogoFileInput(inputLogoRightFile, imgLogoRight);

    // Initial sync & Invalidate Map
    syncLiveKop();
    setTimeout(() => { if (map) map.invalidateSize(); }, 400);
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>