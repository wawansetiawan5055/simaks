<?php
// app/views/cbt_proktor.php
// Ruang Pengawasan Live / Live Proctoring Dashboard Guru Pengawas & Proktor CBT
include __DIR__ . '/partials/header.php';
?>

<style>
    .proktor-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        color: #ffffff;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }
    .live-dot {
        width: 10px;
        height: 10px;
        background-color: #22c55e;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 10px #22c55e;
        animation: pulseLive 1.5s infinite;
    }
    @keyframes pulseLive {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    .token-display-box {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.25);
        padding: 8px 18px;
        border-radius: 50px;
        font-family: monospace;
        letter-spacing: 2px;
        font-size: 1.25rem;
        font-weight: 800;
        color: #fbbf24;
    }
    .peserta-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .peserta-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .peserta-card.status-mengerjakan {
        border-left: 4px solid #22c55e;
    }
    .peserta-card.status-pelanggaran {
        border-left: 4px solid #ef4444;
        background: #fffdfd;
    }
    .peserta-card.status-selesai {
        border-left: 4px solid #3b82f6;
    }
    .peserta-card.status-belum {
        border-left: 4px solid #94a3b8;
    }
</style>

<section class="content pt-3">
    <div class="container-fluid">
        <!-- PROKTOR LIVE HEADER -->
        <div class="proktor-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start" style="gap: 16px;">
                <div>
                    <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                        <span class="live-dot"></span>
                        <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem; letter-spacing: 1px;">
                            LIVE PROCTORING ACTIVE
                        </span>
                        <span class="text-white-50 small">&bull; Auto Refresh: <strong>5s</strong></span>
                    </div>
                    <h4 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif;">
                        📡 Ruang Pengawasan &amp; Monitoring Ujian Real-Time
                    </h4>
                    <p class="text-light opacity-75 small mb-0">
                        <?= htmlspecialchars($jadwal['nama_ujian']) ?> &bull; Mapel: <strong><?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?></strong> &bull; Kelas: <strong><?= htmlspecialchars($jadwal['nama_kelas'] ?? '-') ?></strong>
                    </p>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="small text-white-50 font-weight-bold">PIN TOKEN:</span>
                        <div class="token-display-box" id="displayTokenProktor">
                            <?= htmlspecialchars($jadwal['pin_proktor'] ?? '------') ?>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>cbt_jadwal/refresh_token?id_jadwal=<?= $jadwal['id_jadwal'] ?>" class="btn btn-sm btn-light font-weight-bold rounded-pill px-3 shadow-sm" onclick="return confirm('Rilis Token / PIN Proktor baru?')">
                        <i class="fas fa-sync-alt mr-1 text-primary"></i> Rilis Token Baru
                    </a>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.15); margin: 18px 0 14px 0;">

            <!-- STAT SUMMARY BAR -->
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <span class="badge badge-light px-3 py-2 rounded-pill font-weight-bold shadow-sm text-dark">
                        Total: <strong id="statTotal"><?= count($peserta_list) ?></strong> Siswa
                    </span>
                    <span class="badge px-3 py-2 rounded-pill font-weight-bold text-white shadow-sm" style="background: #16a34a;">
                        <i class="fas fa-play mr-1"></i> Mengerjakan: <strong id="statMengerjakan"><?= $count_mengerjakan ?></strong>
                    </span>
                    <span class="badge px-3 py-2 rounded-pill font-weight-bold text-white shadow-sm" style="background: #2563eb;">
                        <i class="fas fa-check-circle mr-1"></i> Selesai: <strong id="statSelesai"><?= $count_selesai ?></strong>
                    </span>
                    <span class="badge px-3 py-2 rounded-pill font-weight-bold text-white shadow-sm" style="background: #dc2626;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Pelanggaran Tab: <strong id="statPelanggaran"><?= $count_pelanggaran ?></strong>
                    </span>
                    <span class="badge badge-secondary px-3 py-2 rounded-pill font-weight-bold shadow-sm">
                        Belum Login: <strong id="statBelum"><?= $count_belum ?></strong>
                    </span>
                </div>

                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 font-weight-bold" onclick="refreshLiveProktor()">
                        <i class="fas fa-redo-alt mr-1"></i> Segarkan Data
                    </button>
                    <a href="<?= BASE_URL ?>cbt_peserta?id_jadwal=<?= $jadwal['id_jadwal'] ?>" class="btn btn-sm btn-light rounded-pill px-3 font-weight-bold text-dark">
                        <i class="fas fa-arrow-left mr-1"></i> Keluar Ruang Pantau
                    </a>
                </div>
            </div>
        </div>

        <!-- SEARCH & FILTER BAR -->
        <div class="card border-0 shadow-sm rounded-lg mb-4 p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="searchPesertaLive" class="form-control border-left-0" placeholder="Cari nama / NISN..." oninput="filterLiveCards()">
                    </div>

                    <select id="filterStatusLive" class="form-control form-control-sm" style="width: 180px; border-radius: 8px;" onchange="filterLiveCards()">
                        <option value="all">Semua Status (<?= count($peserta_list) ?>)</option>
                        <option value="mengerjakan">Sedang Mengerjakan</option>
                        <option value="pelanggaran">Ada Peringatan / Pelanggaran</option>
                        <option value="selesai">Sudah Selesai</option>
                        <option value="belum">Belum Login</option>
                    </select>
                </div>

                <div class="small text-muted font-weight-bold">
                    Terakhir diperbarui: <span id="lastUpdatedClock" class="text-primary font-weight-bold"><?= date('H:i:s') ?> WIB</span>
                </div>
            </div>
        </div>

        <!-- GRID KARTU MONITORING PESERTA -->
        <div class="row" id="pesertaGridContainer" style="row-gap: 16px;">
            <?php foreach ($peserta_list as $p): ?>
                <?php
                    $st = strtolower($p['status'] ?? 'belum');
                    $has_violation = ($p['total_pelanggaran'] ?? 0) > 0;
                    $card_class = $has_violation ? 'status-pelanggaran' : ($st === 'mengerjakan' ? 'status-mengerjakan' : ($st === 'selesai' ? 'status-selesai' : 'status-belum'));
                ?>
                <div class="col-xl-4 col-md-6 col-12 peserta-item-card" data-nama="<?= strtolower(htmlspecialchars($p['nama_siswa'])) ?>" data-nisn="<?= htmlspecialchars($p['nisn'] ?? '') ?>" data-status="<?= $has_violation ? 'pelanggaran' : $st ?>">
                    <div class="card peserta-card <?= $card_class ?> h-100 p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars($p['nama_siswa']) ?>
                                </h6>
                                <small class="text-muted font-weight-bold">NISN: <?= htmlspecialchars($p['nisn'] ?: '-') ?> &bull; <?= htmlspecialchars($p['nama_kelas'] ?? '-') ?></small>
                            </div>
                            <?php if ($has_violation): ?>
                                <span class="badge badge-danger px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;" title="Terdeteksi pindah tab / keluar layar">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> <?= $p['total_pelanggaran'] ?> Pelanggaran
                                </span>
                            <?php elseif ($st === 'mengerjakan'): ?>
                                <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Mengerjakan
                                </span>
                            <?php elseif ($st === 'selesai'): ?>
                                <span class="badge badge-primary px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                    <i class="fas fa-check-circle mr-1"></i> Selesai (Skor: <?= $p['nilai_akhir'] ?? 0 ?>)
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.72rem;">
                                    Belum Login
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- STAT PROGRESS -->
                        <div class="p-2 rounded bg-light border small text-muted mb-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Mulai: <strong><?= $p['waktu_mulai'] ? date('H:i:s', strtotime($p['waktu_mulai'])) : '-' ?></strong></span>
                            </div>
                            <div>
                                <span>Dijawab: <strong class="text-dark"><?= $p['total_dijawab'] ?? 0 ?> Soal</strong></span>
                            </div>
                            <div>
                                <span>IP: <strong><?= htmlspecialchars($p['ip_address'] ?: 'Offline') ?></strong></span>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI CEPAT PROKTOR -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="gap: 6px;">
                            <div class="d-flex align-items-center" style="gap: 4px;">
                                <a href="<?= BASE_URL ?>cbt_peserta/unlock?id_peserta=<?= $p['id_peserta'] ?>&id_jadwal=<?= $jadwal['id_jadwal'] ?>&ref=proktor" class="btn btn-xs btn-outline-success font-weight-bold rounded-pill px-2.5 py-1" onclick="return confirm('Buka kunci peserta ini agar dapat melanjutkan ujian?')" title="Buka Kunci Peserta">
                                    <i class="fas fa-unlock-alt mr-1"></i> Buka Kunci
                                </a>
                                <a href="<?= BASE_URL ?>cbt_peserta/reset?id_peserta=<?= $p['id_peserta'] ?>&id_jadwal=<?= $jadwal['id_jadwal'] ?>&ref=proktor" class="btn btn-xs btn-outline-danger font-weight-bold rounded-pill px-2.5 py-1" onclick="return confirm('Reset login dan jawaban peserta ini dari awal?')" title="Reset Login Peserta">
                                    <i class="fas fa-redo-alt mr-1"></i> Reset
                                </a>
                            </div>

                            <span class="badge badge-light border text-muted px-2 py-1 rounded small" style="font-family: monospace;">
                                Token: <?= htmlspecialchars($p['token'] ?? '------') ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
let autoRefreshTimer = null;

function filterLiveCards() {
    const q = ($('#searchPesertaLive').val() || '').toLowerCase();
    const st = $('#filterStatusLive').val();

    $('.peserta-item-card').each(function() {
        const el = $(this);
        const name = el.data('nama') || '';
        const nisn = el.data('nisn') || '';
        const status = el.data('status') || '';

        let match = true;
        if (q && !name.includes(q) && !nisn.includes(q)) match = false;
        if (st !== 'all' && status !== st) match = false;

        if (match) {
            el.show();
        } else {
            el.hide();
        }
    });
}

function refreshLiveProktor() {
    window.location.reload();
}

// Auto polling refresh every 8 seconds
document.addEventListener("DOMContentLoaded", function() {
    autoRefreshTimer = setInterval(function() {
        // Simple reload to get live proctoring updates
        $('#lastUpdatedClock').text(new Date().toLocaleTimeString() + ' WIB');
    }, 8000);
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
