<?php 
// app/views/cbt_administrasi.php
// Pusat Pengelolaan Administrasi & Cetak Dokumen Ujian CBT (STS, SAS, SAT, SAJ, Try Out)
include __DIR__ . '/partials/header.php'; 
?>

<style>
    .cbt-adm-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }
    .cbt-adm-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.07);
    }
    .adm-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .nav-pills-cbt .nav-link {
        border-radius: 8px;
        font-weight: 600;
        color: #475569;
        padding: 10px 18px;
        margin-right: 6px;
        transition: all 0.2s ease;
    }
    .nav-pills-cbt .nav-link.active {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .badge-exam-type {
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* PREVIEW STUDIO STYLES */
    .preview-unified-card {
        border-radius: 12px;
        overflow: hidden;
        background-color: #323639;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        border: 1px solid #45494d;
        margin-bottom: 20px;
    }
    .preview-unified-header {
        background: #2a2e33;
        padding: 10px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #3f4448;
    }
    .preview-unified-title {
        color: #f1f5f9;
        font-size: 0.95rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-icon-studio {
        color: #cbd5e1;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.88rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-icon-studio:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.25);
    }
    .btn-icon-studio-danger {
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.88rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-icon-studio-danger:hover {
        color: #ffffff;
        background: #ef4444;
        border-color: #ef4444;
    }
    .preview-unified-body {
        height: calc(100vh - 120px);
        min-height: 680px;
        width: 100%;
        position: relative;
        background-color: #525659;
    }
    .preview-unified-frame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }

</style>

<div id="sectionMainCbtAdm">
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #4338ca); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Pusat Administrasi &amp; Dokumen Ujian CBT
                    </h4>
                    <small class="text-muted">Kelola dan cetak kelengkapan asesmen STS, SAS, SAT, SAJ, TO, Berita Acara, Daftar Hadir, hingga Kartu Peserta dalam 1 pintu</small>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?mod=cbt_dashboard" class="text-muted">CBT</a></li>
                    <li class="breadcrumb-item active text-indigo font-weight-bold">Administrasi Ujian</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- 4 WIDGET RINGKASAN DATA UJIAN -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6 mb-2">
                <div class="card cbt-adm-card shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="adm-icon-box bg-indigo text-white mr-3">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">Paket Soal Ujian</div>
                            <h4 class="font-weight-bold text-dark mb-0"><?= count($paket_list ?? []) ?> Paket</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <div class="card cbt-adm-card shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="adm-icon-box bg-success text-white mr-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">Agenda Sesi Ujian</div>
                            <h4 class="font-weight-bold text-success mb-0"><?= count($jadwal_list ?? []) ?> Jadwal</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <div class="card cbt-adm-card shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="adm-icon-box bg-warning text-white mr-3">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">AI Asesmen Assistant</div>
                            <h4 class="font-weight-bold text-warning mb-0">Aktif &amp; Siap</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <div class="card cbt-adm-card shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="adm-icon-box bg-info text-white mr-3">
                            <i class="fas fa-print"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase small font-weight-bold" style="font-size: 0.70rem;">Format Cetak Resmi</div>
                            <h4 class="font-weight-bold text-info mb-0">12 Dokumen</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAVIGATION TABS -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white p-2 border-bottom">
                <ul class="nav nav-pills nav-pills-cbt" id="cbtAdmTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-naskah-tab" data-toggle="pill" href="#tab-naskah" role="tab">
                            <i class="fas fa-file-signature mr-1.5"></i> 1. Administrasi Naskah &amp; Soal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-pelaksanaan-tab" data-toggle="pill" href="#tab-pelaksanaan" role="tab">
                            <i class="fas fa-users-cog mr-1.5"></i> 2. Pelaksanaan &amp; Kepanitiaan Ujian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-hasil-tab" data-toggle="pill" href="#tab-hasil" role="tab">
                            <i class="fas fa-chart-pie mr-1.5"></i> 3. Hasil, Rekap Nilai &amp; Analisis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-purple" id="tab-ai-tab" data-toggle="pill" href="#tab-ai" role="tab">
                            <i class="fas fa-magic mr-1.5"></i> 4. AI Generator Dokumen Asesmen
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-3 p-md-4">
                <div class="tab-content" id="cbtAdmTabContent">
                    
                    <!-- ======================================================== -->
                    <!-- TAB 1: ADMINISTRASI NASKAH & SOAL (PER MAPEL / PAKET) -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade show active" id="tab-naskah" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-file-alt text-indigo mr-1.5"></i> Berkas Naskah, Kisi-Kisi &amp; Kartu Soal
                                </h5>
                                <p class="text-muted small mb-0">Cetak naskah soal lengkap dengan Kop Sekolah, petunjuk, opsi jawaban, kunci, kisi-kisi CP/TP, dan kartu telaah soal.</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-nowrap" style="font-size: 0.84rem;">
                                <thead class="bg-light text-muted" style="font-size: 0.74rem;">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th>MATA PELAJARAN / PAKET SOAL</th>
                                        <th style="width: 130px;" class="text-center">KURIKULUM &amp; TINGKAT</th>
                                        <th style="width: 100px;" class="text-center">JML SOAL</th>
                                        <th style="width: 150px;">GURU / PEMBUAT</th>
                                        <th style="width: 320px;" class="text-center">DOKUMEN CETAK ADMINISTRASI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($paket_list)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                Belum ada data paket soal ujian yang dibuat.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($paket_list as $idx => $p): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $idx + 1 ?></td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                                <small class="text-muted"><i class="fas fa-book mr-1"></i> <?= htmlspecialchars($p['nama_mapel'] ?? '-') ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border"><?= htmlspecialchars($p['kurikulum'] ?? 'K. Merdeka') ?></span>
                                                <span class="badge badge-primary">Kls <?= htmlspecialchars($p['tingkat'] ?? 'Semua') ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <strong><?= (int)($p['total_soal'] ?? 0) ?></strong> Soal
                                            </td>
                                            <td class="align-middle">
                                                <small class="text-muted"><i class="fas fa-user-edit mr-1"></i> <?= htmlspecialchars($p['nama_guru'] ?? 'Guru Pengampu') ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- CETAK NASKAH -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_naskah&id_paket=<?= $p['id_paket'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Naskah Soal Ujian'); return false;" class="btn btn-outline-primary font-weight-bold" title="Cetak Naskah Soal Ujian">
                                                        <i class="fas fa-print mr-1"></i> Naskah
                                                    </a>
                                                    <!-- CETAK KISI-KISI -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_kisi_kisi&id_paket=<?= $p['id_paket'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Kisi-Kisi Ujian'); return false;" class="btn btn-outline-info font-weight-bold" title="Cetak Kisi-Kisi Ujian">
                                                        <i class="fas fa-th-list mr-1"></i> Kisi-Kisi
                                                    </a>
                                                    <!-- CETAK KARTU SOAL -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_kartu_soal&id_paket=<?= $p['id_paket'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Kartu Telaah Soal'); return false;" class="btn btn-outline-success font-weight-bold" title="Cetak Kartu Telaah Soal">
                                                        <i class="fas fa-id-card-alt mr-1"></i> Kartu Soal
                                                    </a>
                                                    <!-- CETAK KUNCI & SKOR -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_kunci&id_paket=<?= $p['id_paket'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Kunci Jawaban & Bobot'); return false;" class="btn btn-outline-secondary font-weight-bold" title="Cetak Kunci Jawaban & Bobot">
                                                        <i class="fas fa-key mr-1"></i> Kunci
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 2: ADMINISTRASI PELAKSANAAN & KEPANITIAAN UJIAN -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="tab-pelaksanaan" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-clipboard-check text-success mr-1.5"></i> Administrasi Ruang, Peserta, Pengawas &amp; Panitia
                                </h5>
                                <p class="text-muted small mb-0">Cetak kelengkapan serentak untuk STS / SAS / SAT / SAJ: Daftar Hadir, Berita Acara, Kartu Peserta, Kartu Pengawas, dan Tata Tertib.</p>
                            </div>
                        </div>

                        <!-- 4 KOTAK DOKUMEN CEPAT KEPANITIAAN UMUM -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border p-3 text-center h-100 shadow-xs hover-shadow" style="border-radius: 10px;">
                                    <i class="fas fa-id-badge fa-2x text-primary mb-2"></i>
                                    <div class="font-weight-bold text-dark small mb-2">Kartu Pengawas &amp; Panitia</div>
                                    <div class="btn-group btn-group-xs w-100 mt-auto">
                                        <a href="<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=print_kartu_pengawas" target="_blank" class="btn btn-primary btn-xs font-weight-bold">Pengawas</a>
                                        <a href="<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=print_kartu_panitia" target="_blank" class="btn btn-warning btn-xs font-weight-bold">Panitia</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border p-3 text-center h-100 shadow-xs hover-shadow" style="border-radius: 10px;">
                                    <i class="fas fa-user-clock fa-2x text-success mb-2"></i>
                                    <div class="font-weight-bold text-dark small mb-2">Daftar Hadir Pengawas/Panitia</div>
                                    <div class="btn-group btn-group-xs w-100 mt-auto">
                                        <a href="<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=print_hadir_pengawas" target="_blank" class="btn btn-success btn-xs font-weight-bold">Daftar Pengawas</a>
                                        <a href="<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=print_hadir_panitia" target="_blank" class="btn btn-secondary btn-xs font-weight-bold">Daftar Panitia</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border p-3 text-center h-100 shadow-xs hover-shadow" style="border-radius: 10px;">
                                    <i class="fas fa-scroll fa-2x text-warning mb-2"></i>
                                    <div class="font-weight-bold text-dark small mb-2">Tata Tertib Ruang Ujian</div>
                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=print_tata_tertib" target="_blank" class="btn btn-warning btn-xs font-weight-bold w-100 mt-auto text-dark">
                                        <i class="fas fa-print mr-1"></i> Cetak Tata Tertib
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-3 col-6 mb-2">
                                <div class="card border p-3 text-center h-100 shadow-xs hover-shadow" style="border-radius: 10px;">
                                    <i class="fas fa-tags fa-2x text-indigo mb-2"></i>
                                    <div class="font-weight-bold text-dark small mb-2">Label Meja / Nomor Meja</div>
                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=print_label_meja" target="_blank" class="btn btn-indigo btn-xs font-weight-bold w-100 mt-auto text-white">
                                        <i class="fas fa-print mr-1"></i> Cetak Label Meja
                                    </a>
                                </div>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-list-ol mr-1"></i> Dokumen Per Jadwal / Sesi Ujian:</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-nowrap" style="font-size: 0.84rem;">
                                <thead class="bg-light text-muted" style="font-size: 0.74rem;">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th>NAMA UJIAN &amp; MAPEL</th>
                                        <th style="width: 140px;" class="text-center">KELAS / RUANG</th>
                                        <th style="width: 140px;" class="text-center">TANGGAL &amp; SESI</th>
                                        <th style="width: 90px;" class="text-center">STATUS</th>
                                        <th style="width: 320px;" class="text-center">CETAK DOKUMEN RUANG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($jadwal_list)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                Belum ada agenda jadwal ujian yang terdaftar.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($jadwal_list as $idx => $j): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $idx + 1 ?></td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($j['nama_ujian']) ?></div>
                                                <small class="text-muted"><i class="fas fa-book mr-1"></i> <?= htmlspecialchars($j['nama_mapel'] ?? '-') ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border font-weight-bold">
                                                    <?= htmlspecialchars($j['nama_kelas'] ?? 'Semua Kelas') ?>
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div><strong><?= date('d/m/Y', strtotime($j['tgl_mulai'] ?? date('Y-m-d'))) ?></strong></div>
                                                <small class="text-muted"><?= substr($j['jam_mulai'] ?? '08:00', 0, 5) ?> - <?= substr($j['jam_selesai'] ?? '09:30', 0, 5) ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <?php if(($j['status'] ?? '') === 'aktif'): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php elseif(($j['status'] ?? '') === 'selesai'): ?>
                                                    <span class="badge badge-secondary">Selesai</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning text-dark">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- KARTU PESERTA -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=print_kartu&id_jadwal=<?= $j['id_jadwal'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Kartu Peserta Ujian'); return false;" class="btn btn-outline-primary font-weight-bold" title="Cetak Kartu Peserta Ujian">
                                                        <i class="fas fa-id-card mr-1"></i> Kartu
                                                    </a>
                                                    <!-- DAFTAR HADIR -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=print_hadir&id_jadwal=<?= $j['id_jadwal'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Daftar Hadir Peserta'); return false;" class="btn btn-outline-success font-weight-bold" title="Cetak Daftar Hadir Peserta">
                                                        <i class="fas fa-list-alt mr-1"></i> Absen
                                                    </a>
                                                    <!-- BERITA ACARA -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=print_berita_acara&id_jadwal=<?= $j['id_jadwal'] ?>" onclick="openCbtPrintStudio(this.href, 'Cetak Berita Acara Pelaksanaan Ujian'); return false;" class="btn btn-outline-danger font-weight-bold" title="Cetak Berita Acara Pelaksanaan Ujian">
                                                        <i class="fas fa-file-contract mr-1"></i> Berita Acara
                                                    </a>
                                                    <!-- MONITOR PROKTOR -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=live_proktor&id_jadwal=<?= $j['id_jadwal'] ?>" onclick="openCbtPrintStudio(this.href, 'Buka Ruang Monitoring Proktor'); return false;" class="btn btn-outline-info font-weight-bold" title="Buka Ruang Monitoring Proktor">
                                                        <i class="fas fa-desktop"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 3: HASIL, REKAP NILAI & ANALISIS BUTIR SOAL -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="tab-hasil" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-chart-line text-warning mr-1.5"></i> Administrasi Hasil &amp; Analisis Asesmen
                                </h5>
                                <p class="text-muted small mb-0">Cetak rekap nilai resmi per kelas/mapel, analisis butir soal (daya pembeda, tingkat kesukaran), dan export ke Excel.</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-nowrap" style="font-size: 0.84rem;">
                                <thead class="bg-light text-muted" style="font-size: 0.74rem;">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th>NAMA SESI UJIAN</th>
                                        <th style="width: 140px;" class="text-center">KELAS</th>
                                        <th style="width: 120px;" class="text-center">TOTAL PESERTA</th>
                                        <th style="width: 320px;" class="text-center">DOKUMEN HASIL &amp; ANALISIS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($jadwal_list)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Belum ada data jadwal ujian.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($jadwal_list as $idx => $jh): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $idx + 1 ?></td>
                                            <td class="align-middle">
                                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($jh['nama_ujian']) ?></div>
                                                <small class="text-muted"><i class="fas fa-book mr-1"></i> <?= htmlspecialchars($jh['nama_mapel'] ?? '-') ?></small>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-light border"><?= htmlspecialchars($jh['nama_kelas'] ?? '-') ?></span>
                                            </td>
                                            <td class="text-center align-middle font-weight-bold">
                                                <i class="fas fa-users text-muted mr-1"></i> <?= (int)($jh['total_peserta'] ?? 0) ?> Siswa
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- ANALISIS BUTIR SOAL -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_hasil&act=analisis_butir&id_jadwal=<?= $jh['id_jadwal'] ?>" target="_blank" class="btn btn-outline-purple font-weight-bold" title="Cetak Analisis Butir Soal">
                                                        <i class="fas fa-chart-pie mr-1"></i> Analisis Butir
                                                    </a>
                                                    <!-- DETAIL REKAP NILAI -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_hasil&id_jadwal=<?= $jh['id_jadwal'] ?>" class="btn btn-outline-primary font-weight-bold" title="Lihat Rekap Nilai Siswa">
                                                        <i class="fas fa-eye mr-1"></i> Nilai
                                                    </a>
                                                    <!-- EXCEL EXPORT -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_hasil&act=export_excel&id_jadwal=<?= $jh['id_jadwal'] ?>" class="btn btn-outline-success font-weight-bold" title="Download Excel Nilai">
                                                        <i class="fas fa-file-excel mr-1"></i> Excel
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 4: AI GENERATOR DOKUMEN ASESMEN -->
                    <!-- ======================================================== -->
                    <div class="tab-pane fade" id="tab-ai" role="tabpanel">
                        <div class="row align-items-center mb-4">
                            <div class="col-md-8">
                                <h5 class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-magic text-purple mr-1.5"></i> AI Asesmen &amp; Kisi-Kisi Assistant
                                </h5>
                                <p class="text-muted small mb-0">Hasilkan <strong>Kisi-Kisi Soal</strong> dan <strong>Rubrik Penilaian</strong> secara otomatis menggunakan kecerdasan buatan (Gemini AI) berdasarkan Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP).</p>
                            </div>
                            <div class="col-md-4 text-md-right">
                                <span class="badge badge-purple px-3 py-2 text-white font-weight-bold" style="font-size: 0.82rem;">
                                    <i class="fas fa-sparkles mr-1"></i> AI Powered by Gemini
                                </span>
                            </div>
                        </div>

                        <div class="card border shadow-xs bg-light p-4" style="border-radius: 12px;">
                            <form id="formAiKisiKisi" onsubmit="generateAiKisiKisi(event)">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="small font-weight-bold text-dark">Mata Pelajaran &amp; Jenjang</label>
                                        <input type="text" id="ai_mapel" class="form-control form-control-sm" placeholder="Contoh: Matematika Kelas X Fase E" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small font-weight-bold text-dark">Jenis Asesmen</label>
                                        <select id="ai_jenis_ujian" class="form-control form-control-sm">
                                            <option value="Sumatif Akhir Semester (SAS)">Sumatif Akhir Semester (SAS)</option>
                                            <option value="Sumatif Tengah Semester (STS)">Sumatif Tengah Semester (STS)</option>
                                            <option value="Sumatif Akhir Jenjang (SAJ)">Sumatif Akhir Jenjang (SAJ)</option>
                                            <option value="Try Out Ujian">Try Out Ujian</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small font-weight-bold text-dark">Jumlah Soal &amp; Komposisi</label>
                                        <input type="text" id="ai_komposisi" class="form-control form-control-sm" value="30 PG (L1:20%, L2:50%, L3:30%), 5 Uraian">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="small font-weight-bold text-dark">Topik / Materi Pokok / Lingkup Capaian Pembelajaran</label>
                                        <textarea id="ai_materi" class="form-control form-control-sm" rows="3" placeholder="Masukkan materi atau copy paste CP/TP di sini..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" id="btnAiSubmit" class="btn btn-purple font-weight-bold shadow-sm px-4">
                                            <i class="fas fa-wand-magic-sparkles mr-1.5"></i> Buat Kisi-Kisi Otomatis dengan AI
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- HASIL GENERASI AI -->
                            <div id="aiResultBox" class="mt-4 d-none">
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-clipboard-list text-purple mr-1"></i> Hasil Rekomendasi Kisi-Kisi AI:</h6>
                                    <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold" onclick="copyAiResult()">
                                        <i class="fas fa-copy mr-1"></i> Salin Teks
                                    </button>
                                </div>
                                <div id="aiResultContent" class="bg-white p-3 border rounded" style="white-space: pre-wrap; font-family: monospace; font-size: 0.82rem; max-height: 400px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>

<script>
function generateAiKisiKisi(e) {
    e.preventDefault();
    const btn = document.getElementById('btnAiSubmit');
    const resultBox = document.getElementById('aiResultBox');
    const resultContent = document.getElementById('aiResultContent');

    const mapel = document.getElementById('ai_mapel').value;
    const jenis = document.getElementById('ai_jenis_ujian').value;
    const komposisi = document.getElementById('ai_komposisi').value;
    const materi = document.getElementById('ai_materi').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Sedang Membuat Kisi-Kisi...';

    fetch('<?= BASE_URL ?>index.php?mod=cbt_administrasi&act=ai_kisi_kisi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            mapel: mapel,
            jenis_ujian: jenis,
            komposisi: komposisi,
            materi: materi
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-wand-magic-sparkles mr-1.5"></i> Buat Kisi-Kisi Otomatis dengan AI';
        if (data.success) {
            resultContent.textContent = data.result;
            resultBox.classList.remove('d-none');
            resultBox.scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Gagal menghasilkan kisi-kisi: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-wand-magic-sparkles mr-1.5"></i> Buat Kisi-Kisi Otomatis dengan AI';
        alert('Terjadi kesalahan jaringan/server: ' + err);
    });
}

function copyAiResult() {
    const text = document.getElementById('aiResultContent').textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Kisi-kisi berhasil disalin ke clipboard!');
    });
}
</script>


</div> <!-- END #sectionMainCbtAdm -->

<!-- ================================================================= -->
<!-- SECTION PREVIEW STUDIO: ADMINISTRASI DOKUMEN CBT -->
<!-- ================================================================= -->
<div id="sectionPreviewCbtStudio" style="display: none;" class="pt-2">
  <div class="container-fluid">
    <div class="preview-unified-card">
      <div class="preview-unified-header">
        <div class="d-flex align-items-center">
          <button type="button" onclick="closeCbtPrintStudio()" class="btn-icon-studio mr-2" title="Kembali ke Menu Administrasi CBT">
            <i class="fas fa-arrow-left"></i> <span>Kembali</span>
          </button>
          <button type="button" onclick="closeCbtPrintStudio()" class="btn-icon-studio-danger mr-3" title="Tutup Pratinjau">
            <i class="fas fa-times"></i> <span>Tutup</span>
          </button>
          <div class="preview-unified-title d-none d-md-flex">
            <i class="fas fa-file-pdf text-info"></i>
            <span id="cbtStudioDocTitle">Pratinjau: Dokumen Administrasi Ujian CBT</span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" onclick="printCbtFrame()" class="btn-icon-studio mr-2" title="Cetak Dokumen">
            <i class="fas fa-print text-success"></i> <span>Cetak</span>
          </button>
          <a id="btnCbtOpenTab" href="#" class="btn-icon-studio" target="_blank" title="Buka di Tab Baru / Download">
            <i class="fas fa-external-link-alt text-primary"></i> <span>Tab Baru</span>
          </a>
        </div>
      </div>
      <div class="preview-unified-body">
        <!-- Loader Spinner -->
        <div id="cbtStudioLoader" style="position: absolute; top:0; left:0; width:100%; height:100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #323639; color: #fff; z-index: 10;">
          <div class="spinner-border text-info mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
          <div class="font-weight-bold" style="letter-spacing: 0.5px; font-size: 1.1rem;">Menyiapkan Dokumen Ujian CBT...</div>
          <small class="text-muted mt-1">Sedang merender lembar dokumen &amp; kop surat resmi</small>
        </div>
        <iframe id="cbtStudioFrame" src="" class="preview-unified-frame" onload="var l = document.getElementById('cbtStudioLoader'); if(l) l.style.display='none';" title="Pratinjau Cetak Administrasi Ujian"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
var currentCbtDocUrl = '';

function openCbtPrintStudio(url, title) {
    currentCbtDocUrl = url;
    var frame = document.getElementById('cbtStudioFrame');
    var docTitleEl = document.getElementById('cbtStudioDocTitle');
    var btnTab = document.getElementById('btnCbtOpenTab');
    
    if (docTitleEl && title) {
        docTitleEl.textContent = 'Pratinjau: ' + title;
    }
    if (btnTab) {
        btnTab.href = url;
    }
    
    var loader = document.getElementById('cbtStudioLoader');
    if (loader) loader.style.display = 'flex';
    
    frame.src = url + (url.indexOf('?') !== -1 ? '&' : '?') + '_t=' + new Date().getTime();
    document.getElementById('sectionMainCbtAdm').style.display = 'none';
    document.getElementById('sectionPreviewCbtStudio').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function closeCbtPrintStudio() {
    document.getElementById('sectionPreviewCbtStudio').style.display = 'none';
    document.getElementById('sectionMainCbtAdm').style.display = 'block';
}

function printCbtFrame() {
    var iframe = document.getElementById('cbtStudioFrame');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    } else {
        window.open(currentCbtDocUrl, '_blank');
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
