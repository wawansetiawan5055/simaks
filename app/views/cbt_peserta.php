<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .peserta-icon-box {
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
    .custom-form-select {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 50px;
        height: 38px;
        padding-left: 16px;
        padding-right: 32px;
        transition: all 0.2s ease;
    }
    .custom-form-select:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    .toolbar-peserta-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        padding: 14px 20px;
        margin-bottom: 20px;
    }
    .stat-metric-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.15s ease;
    }
    .stat-metric-card:hover {
        transform: translateY(-2px);
    }
    .table-peserta-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .table-peserta-header th {
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
    .search-peserta-box {
        position: relative;
    }
    .search-peserta-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.88rem;
    }
    .search-peserta-input {
        padding-left: 36px;
        padding-right: 14px;
        height: 38px;
        border-radius: 50px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        font-family: 'Poppins', sans-serif !important;
        transition: all 0.2s ease;
    }
    .search-peserta-input:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .btn-soft-warning {
        background: #fefce8;
        color: #ca8a04;
        border: 1px solid #fef08a;
        font-weight: 700;
    }
    .btn-soft-warning:hover {
        background: #ca8a04;
        color: #ffffff;
    }
</style>

<div class="content-header p-0 pt-3 mb-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4 flex-wrap" style="gap: 12px;">
            <div class="d-flex align-items-center">
                <div class="mr-3" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h2 class="m-0 font-weight-bold text-dark" style="font-size: 1.65rem; letter-spacing: -0.5px;">
                        Manajemen Peserta Ujian
                    </h2>
                    <p class="text-muted small mb-0 mt-0.5 font-weight-500">Generate token peserta, kartu ujian, live monitoring, unlock &amp; reset login.</p>
                </div>
            </div>
            <div>
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>cbt_dashboard" class="text-muted font-weight-500"><i class="fas fa-tachometer-alt mr-1"></i> CBT</a></li>
                    <li class="breadcrumb-item active text-info font-weight-bold">Peserta Ujian</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <?php if (empty($jadwal_list)): ?>
            <!-- EMPTY STATE JIKA BELUM ADA JADWAL -->
            <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 16px; background: #ffffff;">
                <div class="mb-3">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 2rem;">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold text-dark mb-1">Belum Ada Agenda Ujian CBT</h5>
                <p class="text-muted small mb-3">Buat agenda jadwal ujian terlebih dahulu untuk dapat mengelola dan merilis token peserta.</p>
                <div>
                    <a href="<?= BASE_URL ?>cbt_jadwal" class="btn btn-gradient-indigo font-weight-bold rounded-pill px-4 py-2 shadow-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Buat Agenda Ujian Sekarang
                    </a>
                </div>
            </div>
        <?php else: ?>

            <?php 
                $count_total = count($peserta_list);
                $count_selesai = count(array_filter($peserta_list, fn($x) => strtolower($x['status'] ?? '') === 'selesai'));
                $count_mengerjakan = count(array_filter($peserta_list, fn($x) => in_array(strtolower($x['status'] ?? ''), ['mengerjakan', 'berlangsung'])));
                $count_belum = count(array_filter($peserta_list, fn($x) => strtolower($x['status'] ?? '') === 'belum' || empty($x['status'])));
            ?>

            <!-- TOOLBAR PEMILIH JADWAL UJIAN -->
            <div class="toolbar-peserta-card">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start" style="gap: 12px;">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                        <span class="small font-weight-bold text-muted text-uppercase d-none d-sm-inline">
                            <i class="fas fa-calendar-check text-primary mr-1"></i> Agenda Ujian:
                        </span>
                        <form method="GET" action="<?= BASE_URL ?>cbt_peserta" class="m-0" id="formPilihJadwal">
                            <select name="id_jadwal" class="form-control custom-form-select" style="min-width: 280px;" onchange="this.form.submit()">
                                <?php foreach ($jadwal_list as $j): ?>
                                    <option value="<?= $j['id_jadwal'] ?>" <?= ($id_jadwal == $j['id_jadwal']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($j['nama_ujian']) ?> (<?= htmlspecialchars($j['nama_kelas'] ?? 'Semua') ?> &bull; <?= $j['tanggal_mulai'] ? date('d M Y', strtotime($j['tanggal_mulai'])) : '-' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <?php if ($jadwal_aktif): ?>
                            <span class="badge text-white font-weight-bold px-2.5 py-1 rounded" style="background: #4f46e5; font-size: 0.76rem;">
                                <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($jadwal_aktif['nama_mapel'] ?? '-') ?>
                            </span>
                            <span class="badge badge-light border text-dark font-weight-bold px-2.5 py-1 rounded" style="font-size: 0.76rem;">
                                Kelas: <?= htmlspecialchars($jadwal_aktif['nama_kelas'] ?? '-') ?>
                            </span>
                            <span class="badge badge-dark px-2.5 py-1 font-weight-bold" style="font-family: monospace; letter-spacing: 1px; font-size: 0.82rem;">
                                PIN: <?= htmlspecialchars($jadwal_aktif['pin_proktor'] ?? '-') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($jadwal_aktif): ?>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <a href="<?= BASE_URL ?>cbt_peserta/live_proktor?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" 
                               class="btn btn-sm font-weight-bold rounded-pill px-3 py-1.5 shadow-sm" 
                               style="background: #0f172a; color: #38bdf8; border: 1px solid #334155;">
                                <i class="fas fa-satellite-dish mr-1 text-warning"></i> 📡 Live Proctor
                            </a>

                            <?php if ($count_belum > 0): ?>
                                <a href="<?= BASE_URL ?>cbt_peserta/create_susulan?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" 
                                   class="btn btn-sm btn-outline-danger font-weight-bold rounded-pill px-3 py-1.5 shadow-sm" 
                                   onclick="return confirm('Buat agenda Ujian Susulan khusus untuk <?= $count_belum ?> siswa yang absen / belum ujian?')">
                                    <i class="fas fa-bolt mr-1 text-warning"></i> ⚡ Ujian Susulan (<?= $count_belum ?>)
                                </a>
                            <?php endif; ?>

                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-light border font-weight-bold rounded-pill px-3 py-1.5 dropdown-toggle shadow-sm text-dark" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print mr-1 text-primary"></i> 🖨️ Dokumen Panitia
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 12px; font-family: 'Poppins', sans-serif;">
                                    <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_peserta/print_kartu?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" target="_blank">
                                        <i class="fas fa-id-badge text-primary mr-2"></i> 🗂️ Cetak Kartu Peserta (Foto/QR)
                                    </a>
                                    <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_peserta/print_hadir?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" target="_blank">
                                        <i class="fas fa-clipboard-check text-success mr-2"></i> 📋 Cetak Daftar Hadir Ruang
                                    </a>
                                    <a class="dropdown-item py-2 small" href="<?= BASE_URL ?>cbt_peserta/print_berita_acara?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" target="_blank">
                                        <i class="fas fa-file-signature text-info mr-2"></i> 📝 Cetak Berita Acara Ujian
                                    </a>
                                </div>
                            </div>

                            <a href="<?= BASE_URL ?>cbt_peserta/generate?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" 
                               class="btn btn-gradient-indigo btn-sm font-weight-bold rounded-pill px-3 py-1.5 shadow-sm" 
                               onclick="return confirm('Auto Generate seluruh siswa aktif dari kelas <?= htmlspecialchars($jadwal_aktif['nama_kelas'] ?? '-') ?> ke dalam ujian ini?')">
                                <i class="fas fa-magic mr-1"></i> Auto Generate
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($jadwal_aktif): ?>
                <!-- 4 METRIC STAT CARDS -->
                <div class="row mb-3" style="row-gap: 10px;">
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="stat-metric-card" style="border-left: 4px solid #4f46e5;">
                            <div>
                                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Total Peserta</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1" style="font-family: 'Poppins', sans-serif;"><?= $count_total ?> <span class="small text-muted" style="font-size: 0.8rem;">Siswa</span></h4>
                            </div>
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="stat-metric-card" style="border-left: 4px solid #16a34a;">
                            <div>
                                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Sudah Selesai</span>
                                <h4 class="font-weight-bold text-success mb-0 mt-1" style="font-family: 'Poppins', sans-serif;"><?= $count_selesai ?> <span class="small text-muted" style="font-size: 0.8rem;">Siswa</span></h4>
                            </div>
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="stat-metric-card" style="border-left: 4px solid #ca8a04;">
                            <div>
                                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Sedang Mengerjakan</span>
                                <h4 class="font-weight-bold text-warning mb-0 mt-1" style="font-family: 'Poppins', sans-serif;"><?= $count_mengerjakan ?> <span class="small text-muted" style="font-size: 0.8rem;">Siswa</span></h4>
                            </div>
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: #fefce8; color: #ca8a04; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="stat-metric-card" style="border-left: 4px solid #64748b;">
                            <div>
                                <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Belum Login / Mulai</span>
                                <h4 class="font-weight-bold text-secondary mb-0 mt-1" style="font-family: 'Poppins', sans-serif;"><?= $count_belum ?> <span class="small text-muted" style="font-size: 0.8rem;">Siswa</span></h4>
                            </div>
                            <div style="width: 42px; height: 42px; border-radius: 10px; background: #f1f5f9; color: #64748b; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fas fa-user-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL PESERTA CARD -->
                <div class="card table-peserta-card">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start" style="gap: 12px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-list-ol text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Daftar Peserta Siswa
                            </h6>
                        </div>

                        <!-- INSTANT SEARCH & FILTER -->
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px; width: 100%; max-width: 450px; justify-content: flex-end;">
                            <div class="search-peserta-box flex-grow-1" style="min-width: 220px;">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchPesertaInput" class="form-control search-peserta-input" placeholder="Cari nama siswa atau NISN..." oninput="filterPesertaTable(this.value)">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tablePeserta" style="font-family: 'Poppins', sans-serif;">
                            <thead class="table-peserta-header">
                                <tr>
                                    <th style="width: 35px;">#</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN / NIPD</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Token Siswa</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center" style="width: 110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($peserta_list)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="mb-3">
                                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                                                    <i class="fas fa-user-plus"></i>
                                                </div>
                                            </div>
                                            <h6 class="font-weight-bold text-dark mb-1">Belum Ada Peserta Terdaftar</h6>
                                            <p class="text-muted small mb-3">Klik tombol di bawah untuk memasukkan seluruh siswa aktif di kelas <strong><?= htmlspecialchars($jadwal_aktif['nama_kelas'] ?? '-') ?></strong>.</p>
                                            <a href="<?= BASE_URL ?>cbt_peserta/generate?id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" class="btn btn-gradient-indigo btn-sm font-weight-bold rounded-pill px-4 py-2 shadow-sm">
                                                <i class="fas fa-magic mr-1"></i> Auto Generate Peserta Kelas
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($peserta_list as $i => $p): ?>
                                    <?php 
                                        $st = strtolower($p['status'] ?? 'belum');
                                        $badge_cls = $st === 'selesai' ? 'success' : ($st === 'mengerjakan' || $st === 'berlangsung' ? 'warning' : 'secondary');
                                        $search_text = strtolower(trim(($p['nama_siswa'] ?? '') . ' ' . ($p['nisn'] ?? '') . ' ' . ($p['nipd'] ?? '')));
                                    ?>
                                    <tr class="peserta-row" data-search="<?= htmlspecialchars($search_text) ?>">
                                        <td class="font-weight-bold text-muted align-middle"><?= $i + 1 ?></td>
                                        <td class="align-middle">
                                            <strong class="text-dark d-block" style="font-size: 0.92rem;"><?= htmlspecialchars($p['nama_siswa']) ?></strong>
                                            <small class="text-muted"><?= ($p['jk'] === 'L' || $p['jk'] === 'Laki-laki') ? '👦 Laki-laki' : '👧 Perempuan' ?></small>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($p['nisn'] ?? '-') ?></span>
                                            <?php if (!empty($p['nipd'])): ?>
                                                <small class="text-muted d-block">NIPD: <?= htmlspecialchars($p['nipd']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-light border text-dark font-weight-bold px-2 py-0.5 rounded" style="font-size: 0.76rem;">
                                                <?= htmlspecialchars($p['nama_kelas'] ?? $jadwal_aktif['nama_kelas'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-dark px-2.5 py-1 font-weight-bold" style="letter-spacing: 1.5px; font-family: monospace; font-size: 0.88rem; border-radius: 6px;">
                                                <?= htmlspecialchars($p['token']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-<?= $badge_cls ?> px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                <?= ucfirst($p['status'] ?? 'belum') ?>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold">
                                            <?php if ($p['nilai_akhir'] !== null): ?>
                                                <span class="badge badge-light border font-weight-bold text-primary px-2 py-1" style="font-size: 0.88rem;">
                                                    <?= number_format((float)$p['nilai_akhir'], 1) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle text-nowrap">
                                            <?php if ($st !== 'belum'): ?>
                                                <a href="<?= BASE_URL ?>cbt_peserta/unlock?id_peserta=<?= $p['id_peserta'] ?>&id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" 
                                                   class="btn btn-xs btn-soft-primary rounded-pill px-2.5 py-1 font-weight-bold mr-1" 
                                                   onclick="return confirm('Buka kunci ujian <?= htmlspecialchars($p['nama_siswa']) ?> agar dapat melanjutkan pengerjaan soal (jawaban tersimpan tidak hilang)?')" 
                                                   title="Buka Kunci & Lanjutkan Ujian">
                                                    <i class="fas fa-lock-open mr-1"></i> Buka Kunci
                                                </a>
                                                <a href="<?= BASE_URL ?>cbt_peserta/reset?id_peserta=<?= $p['id_peserta'] ?>&id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" 
                                                   class="btn btn-xs btn-soft-warning rounded-pill px-2.5 py-1 font-weight-bold" 
                                                   onclick="return confirm('Reset TOTAL pengerjaan <?= htmlspecialchars($p['nama_siswa']) ?>? Semua jawaban akan dihapus dan siswa mengulang dari nomor 1.')" 
                                                   title="Reset Total / Ulang Dari Awal">
                                                    <i class="fas fa-redo mr-1"></i> Reset
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small" style="font-size: 0.75rem;">Siap Ujian</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<script>
function filterPesertaTable(kw) {
    kw = (kw || '').toLowerCase().trim();
    $('.peserta-row').each(function() {
        const text = $(this).data('search') || '';
        if (!kw || text.includes(kw)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
