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
</style>

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
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_naskah&id_paket=<?= $p['id_paket'] ?>" target="_blank" class="btn btn-outline-primary font-weight-bold" title="Cetak Naskah Soal Ujian">
                                                        <i class="fas fa-print mr-1"></i> Naskah
                                                    </a>
                                                    <!-- CETAK KISI-KISI -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_kisi_kisi&id_paket=<?= $p['id_paket'] ?>" target="_blank" class="btn btn-outline-info font-weight-bold" title="Cetak Kisi-Kisi Ujian">
                                                        <i class="fas fa-th-list mr-1"></i> Kisi-Kisi
                                                    </a>
                                                    <!-- CETAK KARTU SOAL -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_kartu_soal&id_paket=<?= $p['id_paket'] ?>" target="_blank" class="btn btn-outline-success font-weight-bold" title="Cetak Kartu Telaah Soal">
                                                        <i class="fas fa-id-card-alt mr-1"></i> Kartu Soal
                                                    </a>
                                                    <!-- CETAK KUNCI & SKOR -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_paket&act=print_kunci&id_paket=<?= $p['id_paket'] ?>" target="_blank" class="btn btn-outline-secondary font-weight-bold" title="Cetak Kunci Jawaban & Bobot">
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
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=print_kartu&id_jadwal=<?= $j['id_jadwal'] ?>" target="_blank" class="btn btn-outline-primary font-weight-bold" title="Cetak Kartu Peserta Ujian">
                                                        <i class="fas fa-id-card mr-1"></i> Kartu
                                                    </a>
                                                    <!-- DAFTAR HADIR -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=print_hadir&id_jadwal=<?= $j['id_jadwal'] ?>" target="_blank" class="btn btn-outline-success font-weight-bold" title="Cetak Daftar Hadir Peserta">
                                                        <i class="fas fa-list-alt mr-1"></i> Absen
                                                    </a>
                                                    <!-- BERITA ACARA -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=print_berita_acara&id_jadwal=<?= $j['id_jadwal'] ?>" target="_blank" class="btn btn-outline-danger font-weight-bold" title="Cetak Berita Acara Pelaksanaan Ujian">
                                                        <i class="fas fa-file-contract mr-1"></i> Berita Acara
                                                    </a>
                                                    <!-- MONITOR PROKTOR -->
                                                    <a href="<?= BASE_URL ?>index.php?mod=cbt_peserta&act=live_proktor&id_jadwal=<?= $j['id_jadwal'] ?>" target="_blank" class="btn btn-outline-info font-weight-bold" title="Buka Ruang Monitoring Proktor">
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

<?php include __DIR__ . '/partials/footer.php'; ?>
