<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Manajemen Persuratan &amp; Arsip Digital
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Persuratan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content px-3">
    <div class="container-fluid">
        <!-- Dashboard Stats -->
        <div class="row mb-4 px-2">
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 card-gradient-1 shadow-sm h-100">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="stat-icon-bg"><i class="fas fa-inbox"></i></div>
                            <span class="badge bg-white bg-opacity-25 rounded-pill px-3">Masuk</span>
                        </div>
                        <h2 class="fw-bold mb-1"><?= $totalMasuk ?></h2>
                        <div class="small opacity-75">Total Surat Diterima</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 card-gradient-2 shadow-sm h-100">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="stat-icon-bg"><i class="fas fa-paper-plane"></i></div>
                            <span class="badge bg-white bg-opacity-25 rounded-pill px-3">Keluar</span>
                        </div>
                        <h2 class="fw-bold mb-1"><?= $totalKeluar ?></h2>
                        <div class="small opacity-75">Telah Terbit Final</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 card-gradient-3 shadow-sm h-100">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="stat-icon-bg"><i class="fas fa-layer-group"></i></div>
                            <span class="badge bg-white bg-opacity-25 rounded-pill px-3">Template</span>
                        </div>
                        <h2 class="fw-bold mb-1"><?= count($templates) ?></h2>
                        <div class="small opacity-75">Format Siap Pakai</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card border-0 card-gradient-4 shadow-sm h-100">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="stat-icon-bg"><i class="fas fa-file-signature"></i></div>
                            <span class="badge bg-white bg-opacity-25 rounded-pill px-3">Draft</span>
                        </div>
                        <h2 class="fw-bold mb-1"><?= $totalDraft ?? 0 ?></h2>
                        <div class="small opacity-75">Menunggu Finalisasi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row px-2">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Action Grid -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4"><i class="fas fa-th-large text-primary mr-2"></i> Akses Cepat Navigasi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="<?= BASE_URL ?>surat/keluar" class="action-tile p-4 border rounded d-block text-center text-decoration-none">
                                    <div class="tile-icon bg-soft-primary mx-auto mb-3"><i class="fas fa-plus text-primary"></i></div>
                                    <h6 class="fw-bold text-dark mb-1">Buat Surat</h6>
                                    <p class="small text-muted mb-0">Generate surat keluar baru</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= BASE_URL ?>surat/masuk" class="action-tile p-4 border rounded d-block text-center text-decoration-none">
                                    <div class="tile-icon bg-soft-success mx-auto mb-3"><i class="fas fa-file-import text-success"></i></div>
                                    <h6 class="fw-bold text-dark mb-1">Arsip Masuk</h6>
                                    <p class="small text-muted mb-0">Catat dan scan surat masuk</p>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= BASE_URL ?>surat/template" class="action-tile p-4 border rounded d-block text-center text-decoration-none">
                                    <div class="tile-icon bg-soft-info mx-auto mb-3"><i class="fas fa-sliders-h text-info"></i></div>
                                    <h6 class="fw-bold text-dark mb-1">Pengaturan</h6>
                                    <p class="small text-muted mb-0">Kelola master template</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline / Log -->
                <div class="card shadow-sm border-0" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4"><i class="fas fa-stream text-secondary mr-2"></i> Aktivitas Sistem</h6>
                        <div class="timeline-compact">
                            <div class="timeline-item pb-3 position-relative">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content ps-4">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0 fw-bold small">Sistem Inisialisasi</p>
                                        <span class="text-muted" style="font-size: 10px;">Hari ini</span>
                                    </div>
                                    <p class="small text-muted mb-0">Seluruh tabel database persuratan berhasil diverifikasi dan siap digunakan.</p>
                                </div>
                            </div>
                            <div class="timeline-item pb-3 position-relative">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content ps-4">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0 fw-bold small">Kategori Siap</p>
                                        <span class="text-muted" style="font-size: 10px;">Kemarin</span>
                                    </div>
                                    <p class="small text-muted mb-0">Kategori klasifikasi surat (Kesiswaan, Kurikulum, Kepegawaian) telah diatur.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4 bg-gradient-dark text-white overflow-hidden" style="border-radius: 20px;">
                    <div class="card-body p-4 position-relative">
                        <div class="position-relative" style="z-index: 2;">
                            <h2 class="fw-bold mb-0" id="live-clock"><?= date('H:i') ?></h2>
                            <div class="opacity-75"><?= tgl_indo(null, true) ?></div>
                        </div>
                        <i class="fas fa-clock position-absolute" style="font-size: 100px; color: rgba(255,255,255,0.05); bottom: -20px; right: -20px;"></i>
                    </div>
                </div>

                <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 20px; background-color: #f8f9fa;">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-shield-alt text-warning mr-2"></i> Tips Cepat</h6>
                    <div class="d-flex mb-3">
                        <div class="me-3 mt-1"><i class="fas fa-check-circle text-success small"></i></div>
                        <div class="small text-muted"><strong>Template</strong> mendukung variabel <code>{{nama_siswa}}</code>, <code>{{nisn}}</code>, dll.</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="me-3 mt-1"><i class="fas fa-check-circle text-success small"></i></div>
                        <div class="small text-muted">Selalu unggah <strong>file scan</strong> surat masuk untuk arsip yang sah secara digital.</div>
                    </div>
                    <div class="d-flex">
                        <div class="me-3 mt-1"><i class="fas fa-check-circle text-success small"></i></div>
                        <div class="small text-muted">Gunakan fitur <strong>Mail Merge</strong> untuk mencetak banyak surat sekaligus.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Stat Cards Gradients */
.card-gradient-1 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.card-gradient-2 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.card-gradient-3 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.card-gradient-4 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }

.stat-card { transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }

.stat-icon-bg {
    width: 45px; height: 45px;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    border-radius: 12px; font-size: 1.2rem;
}

/* Action Tiles */
.action-tile {
    background: #fff;
    border: 1px solid #edf2f9 !important;
    transition: all 0.2s;
}
.action-tile:hover {
    background: #f8fbff;
    border-color: #cfe2ff !important;
    transform: scale(1.02);
}
.tile-icon {
    width: 55px; height: 55px;
    border-radius: 15px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.bg-soft-primary { background-color: #e7f1ff; }
.bg-soft-success { background-color: #e1f7ef; }
.bg-soft-info { background-color: #e0faff; }

/* Timeline */
.timeline-compact::before {
    content: ''; position: absolute; left: 6px; top: 0; bottom: 0;
    width: 2px; background: #e9ecef;
}
.timeline-marker {
    position: absolute; left: 0; top: 5px;
    width: 14px; height: 14px; border-radius: 50%;
    border: 3px solid #fff; box-shadow: 0 0 0 2px #e9ecef;
    z-index: 2;
}
.bg-gradient-dark { background: linear-gradient(135deg, #2d3436 0%, #000000 100%); }

#live-clock { font-size: 2.5rem; }
</style>

<script>
// Live Clock
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('live-clock').textContent = `${hours}:${minutes}`;
}
setInterval(updateClock, 1000);
</script>

<?php include '../app/views/partials/footer.php'; ?>
