<?php 
// app/views/cbt_hasil.php
// Full Overhaul Halaman Rekapitulasi Hasil & Nilai CBT

include __DIR__ . '/partials/header.php'; 

// Hitung metrik statistik jika ada data
$nilai_arr = [];
$total_peserta_selesai = 0;
$total_tuntas = 0;
$kkm = (float)($jadwal_aktif['passing_grade'] ?? 75);

if (!empty($hasil_list)) {
    foreach ($hasil_list as $h) {
        if ($h['status'] === 'selesai' && $h['nilai_akhir'] !== null) {
            $val = (float)$h['nilai_akhir'];
            $nilai_arr[] = $val;
            $total_peserta_selesai++;
            if ($val >= $kkm) {
                $total_tuntas++;
            }
        }
    }
}

$rata2 = !empty($nilai_arr) ? array_sum($nilai_arr) / count($nilai_arr) : 0;
$tertinggi = !empty($nilai_arr) ? max($nilai_arr) : 0;
$terendah = !empty($nilai_arr) ? min($nilai_arr) : 0;
$persen_tuntas = $total_peserta_selesai > 0 ? ($total_tuntas / $total_peserta_selesai) * 100 : 0;
?>

<style>
    .hasil-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
        flex-shrink: 0;
    }
    .stat-metric-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }
    .table-hasil-header th {
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
    .rank-pill {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.82rem;
    }
    .rank-1 { background: #fef08a; color: #854d0e; border: 1px solid #fde047; }
    .rank-2 { background: #e2e8f0; color: #334155; border: 1px solid #cbd5e1; }
    .rank-3 { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
    .rank-normal { background: #f1f5f9; color: #64748b; }

    @media print {
        .no-print, .main-sidebar, .main-header, .content-header, .btn, .card-header, .filter-section {
            display: none !important;
        }
        .print-only {
            display: block !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt;
        }
        .table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table td, .table th {
            border: 1px solid #333 !important;
            padding: 6px 8px !important;
        }
    }
    .print-only { display: none; }
</style>

<div class="content-header pt-3 mb-2 no-print">
    <div class="container-fluid">
        <!-- TOP HEADER: TITLE LEFT + ACTION BUTTONS RIGHT -->
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="hasil-icon-box mr-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Hasil &amp; Rekap Nilai CBT
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <?php if ($jadwal_aktif): ?>
                    <a href="index.php?mod=cbt_hasil&act=export_excel&id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" class="btn btn-success btn-sm rounded-pill font-weight-bold px-3 shadow-sm mr-1">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel (.xlsx)
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill font-weight-bold px-3 shadow-sm" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Cetak Berita Acara &amp; Nilai
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- PILIH AGENDA JADWAL UJIAN -->
        <div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 16px; background: #ffffff;">
            <div class="card-body p-3 p-md-4">
                <form method="GET" action="index.php">
                    <input type="hidden" name="mod" value="cbt_hasil">
                    <div class="row align-items-center" style="row-gap: 8px;">
                        <div class="col-md-3 col-12">
                            <label class="small font-weight-bold text-muted text-uppercase mb-0 d-flex align-items-center">
                                <i class="fas fa-calendar-alt text-primary mr-1"></i> Pilih Agenda Ujian:
                            </label>
                        </div>
                        <div class="col-md-9 col-12">
                            <select name="id_jadwal" class="form-control font-weight-bold" style="border-radius: 10px; height: 42px; border-color: #cbd5e1;" onchange="this.form.submit()">
                                <?php if (empty($jadwal_list)): ?>
                                    <option value="">-- Belum ada agenda ujian yang tersimpan --</option>
                                <?php else: ?>
                                    <?php foreach ($jadwal_list as $j): ?>
                                    <option value="<?= $j['id_jadwal'] ?>" <?= (isset($jadwal_aktif['id_jadwal']) && $jadwal_aktif['id_jadwal'] == $j['id_jadwal']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($j['nama_ujian']) ?> &bull; <?= htmlspecialchars($j['nama_mapel'] ?? '-') ?> (Kelas <?= htmlspecialchars($j['nama_kelas'] ?? '-') ?>) &mdash; <?= $j['total_selesai'] ?> / <?= $j['total_peserta'] ?> Siswa Selesai
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- PRINT HEADER (HANYA MUNCUL SAAT DI-PRINT) -->
        <div class="print-only mb-4">
            <div class="text-center border-bottom pb-3 mb-3">
                <h3 class="font-weight-bold mb-1">SMA PLUS AL-MANSHURIYAH</h3>
                <h5 class="mb-1 text-uppercase font-weight-bold">BERITA ACARA &amp; DAFTAR HASIL ASESMEN BERBASIS KOMPUTER (CBT)</h5>
                <p class="small mb-0">Tahun Ajaran 2026/2027 &bull; Sistem Informasi Manajemen Akademik Sekolah (SIMAKS)</p>
            </div>
            <?php if ($jadwal_aktif): ?>
                <table class="table table-borderless table-sm mb-3" style="width: 100%;">
                    <tr>
                        <td style="width: 20%;"><strong>Nama Ujian</strong></td>
                        <td style="width: 30%;">: <?= htmlspecialchars($jadwal_aktif['nama_ujian']) ?></td>
                        <td style="width: 20%;"><strong>Kelas / Rombel</strong></td>
                        <td style="width: 30%;">: <?= htmlspecialchars($jadwal_aktif['nama_kelas'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Mata Pelajaran</strong></td>
                        <td>: <?= htmlspecialchars($jadwal_aktif['nama_mapel'] ?? '-') ?></td>
                        <td><strong>KKM / Passing Grade</strong></td>
                        <td>: <?= $kkm ?></td>
                    </tr>
                    <tr>
                        <td><strong>Waktu Pelaksanaan</strong></td>
                        <td>: <?= date('d M Y, H:i', strtotime($jadwal_aktif['tanggal_mulai'])) ?> WIB</td>
                        <td><strong>Total Peserta Selesai</strong></td>
                        <td>: <?= $total_peserta_selesai ?> Siswa</td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>

        <?php if ($jadwal_aktif): ?>
            <!-- 4 STAT METRICS CARDS -->
            <div class="row mb-4 no-print">
                <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                    <div class="stat-metric-card" style="border-left: 4px solid #3b82f6;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Rata-Rata Nilai</span>
                            <h3 class="font-weight-bold text-primary mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= number_format($rata2, 1) ?>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Dari <?= $total_peserta_selesai ?> siswa selesai</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                    <div class="stat-metric-card" style="border-left: 4px solid #16a34a;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Nilai Tertinggi</span>
                            <h3 class="font-weight-bold text-success mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= number_format($tertinggi, 1) ?>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Skor maksimum kelas</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                    <div class="stat-metric-card" style="border-left: 4px solid #ef4444;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Nilai Terendah</span>
                            <h3 class="font-weight-bold text-danger mb-0 mt-1" style="font-family: 'Poppins', sans-serif;">
                                <?= number_format($terendah, 1) ?>
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Skor minimum kelas</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef2f2; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="stat-metric-card" style="border-left: 4px solid #8b5cf6;">
                        <div>
                            <span class="small font-weight-bold text-muted text-uppercase d-block" style="font-size: 0.74rem;">Ketuntasan Belajar</span>
                            <h3 class="font-weight-bold text-purple mb-0 mt-1" style="color: #7c3aed; font-family: 'Poppins', sans-serif;">
                                <?= number_format($persen_tuntas, 1) ?>%
                            </h3>
                            <small class="text-muted" style="font-size: 0.75rem;"><?= $total_tuntas ?> / <?= $total_peserta_selesai ?> Tuntas KKM (<?= $kkm ?>)</small>
                        </div>
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #f5f3ff; color: #7c3aed; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL HASIL & NILAI PESERTA -->
            <div class="card shadow-sm border-0 mb-5" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap no-print" style="gap: 12px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-list-ol text-primary mr-2"></i>
                        <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                            Daftar Nilai Peserta Didik (<?= count($hasil_list) ?> Siswa)
                        </h6>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari nama / NISN..." style="border-radius: 20px; width: 180px;" oninput="filterHasilTable(this.value)">

                        <?php 
                            $count_belum_tuntas = $total_peserta_selesai - $total_tuntas;
                            if ($count_belum_tuntas > 0): 
                        ?>
                            <a href="index.php?mod=cbt_hasil&act=create_remedial&id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" 
                               class="btn btn-xs btn-outline-warning font-weight-bold rounded-pill px-3 py-1.5 shadow-sm"
                               onclick="return confirm('Buka sesi Ujian Remedial khusus untuk <?= $count_belum_tuntas ?> siswa yang nilainya di bawah KKM (<?= $kkm ?>)?')">
                                <i class="fas fa-graduation-cap mr-1"></i> ⚡ Buka Remedial (<?= $count_belum_tuntas ?>)
                            </a>
                        <?php endif; ?>

                        <a href="index.php?mod=cbt_hasil&act=analisis_butir&id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" target="_blank" class="btn btn-xs btn-outline-info font-weight-bold rounded-pill px-3 py-1.5 shadow-sm">
                            <i class="fas fa-chart-pie mr-1"></i> 📊 Analisis Butir Soal
                        </a>

                        <a href="index.php?mod=cbt_hasil&act=export_excel&id_jadwal=<?= $jadwal_aktif['id_jadwal'] ?>" class="btn btn-xs btn-success font-weight-bold rounded-pill px-3 py-1.5 shadow-sm">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </a>

                        <button onclick="window.print()" class="btn btn-xs btn-secondary font-weight-bold rounded-pill px-3 py-1.5 shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak Leger
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-family: 'Poppins', sans-serif;">
                        <thead class="table-hasil-header">
                            <tr>
                                <th style="width: 45px;" class="text-center">Rank</th>
                                <th>Nama Peserta Didik</th>
                                <th>NISN / NIPD</th>
                                <th>Kelas</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Nilai PG</th>
                                <th class="text-center">Nilai Esai</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center" style="width: 130px;">Ketuntasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hasil_list)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
                                        <h6 class="font-weight-bold text-dark mb-1">Belum Ada Hasil Ujian</h6>
                                        <p class="small text-muted mb-0">Belum ada siswa yang terdaftar atau menyelesaikan ujian pada jadwal ini.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                    $rank = 1;
                                    foreach ($hasil_list as $h): 
                                        $st = strtolower($h['status'] ?? 'belum');
                                        $badge_cls = $st === 'selesai' ? 'success' : ($st === 'mengerjakan' || $st === 'berlangsung' ? 'warning' : 'secondary');
                                        $nilai_akhir = $h['nilai_akhir'] !== null ? (float)$h['nilai_akhir'] : null;
                                        $is_tuntas = ($nilai_akhir !== null && $nilai_akhir >= $kkm);
                                        $search_text = strtolower(trim(($h['nama_siswa'] ?? '') . ' ' . ($h['nisn'] ?? '') . ' ' . ($h['nipd'] ?? '')));
                                ?>
                                <tr class="hasil-row" data-search="<?= htmlspecialchars($search_text) ?>">
                                    <td class="text-center align-middle">
                                        <?php if ($st === 'selesai'): ?>
                                            <?php if ($rank === 1): ?>
                                                <span class="rank-pill rank-1" title="Peringkat 1">🥇</span>
                                            <?php elseif ($rank === 2): ?>
                                                <span class="rank-pill rank-2" title="Peringkat 2">🥈</span>
                                            <?php elseif ($rank === 3): ?>
                                                <span class="rank-pill rank-3" title="Peringkat 3">🥉</span>
                                            <?php else: ?>
                                                <span class="rank-pill rank-normal"><?= $rank ?></span>
                                            <?php endif; ?>
                                            <?php $rank++; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <strong class="text-dark d-block" style="font-size: 0.92rem;"><?= htmlspecialchars($h['nama_siswa']) ?></strong>
                                        <small class="text-muted"><?= ($h['jk'] === 'L' || $h['jk'] === 'Laki-laki') ? '👦 Laki-laki' : '👧 Perempuan' ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark"><?= htmlspecialchars($h['nisn'] ?? '-') ?></span>
                                        <?php if (!empty($h['nipd'])): ?>
                                            <small class="text-muted d-block">NIPD: <?= htmlspecialchars($h['nipd']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light border text-dark font-weight-bold px-2 py-0.5 rounded" style="font-size: 0.76rem;">
                                            <?= htmlspecialchars($h['nama_kelas'] ?? $jadwal_aktif['nama_kelas'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-<?= $badge_cls ?> px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                            <?= ucfirst($st) ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">
                                        <?= $h['nilai_pg'] !== null ? number_format((float)$h['nilai_pg'], 1) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">
                                        <?= $h['nilai_essay'] !== null ? number_format((float)$h['nilai_essay'], 1) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold" style="font-size: 1.05rem;">
                                        <?php if ($nilai_akhir !== null): ?>
                                            <span class="text-<?= $is_tuntas ? 'success' : 'danger' ?>">
                                                <?= number_format($nilai_akhir, 1) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($nilai_akhir !== null): ?>
                                            <?php if ($is_tuntas): ?>
                                                <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                    <i class="fas fa-check mr-1"></i> Tuntas
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-danger px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                                    <i class="fas fa-times mr-1"></i> Belum Tuntas
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-light border text-muted px-2 py-0.5 small">Belum Ujian</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TANDA TANGAN BERITA ACARA PRINT ONLY -->
            <div class="print-only mt-5">
                <div class="row text-center">
                    <div class="col-4">
                        <p class="mb-5">Pengawas I,</p>
                        <p class="font-weight-bold mb-0">_________________________</p>
                        <small>NIP. ........................................</small>
                    </div>
                    <div class="col-4">
                        <p class="mb-5">Pengawas II,</p>
                        <p class="font-weight-bold mb-0">_________________________</p>
                        <small>NIP. ........................................</small>
                    </div>
                    <div class="col-4">
                        <p class="mb-5">Mengetahui,<br>Kepala Sekolah,</p>
                        <p class="font-weight-bold mb-0">H. Asep Saepul Anwar, S.Pd., M.M.</p>
                        <small>NIP. ........................................</small>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</section>

<script>
function filterHasilTable(kw) {
    kw = (kw || '').toLowerCase().trim();
    $('.hasil-row').each(function() {
        const text = $(this).data('search') || '';
        if (text.includes(kw)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
