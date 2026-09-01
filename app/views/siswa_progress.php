<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h4 class="m-0 font-weight-bold text-dark"><i class="fas fa-star text-warning mr-2"></i> Pengembangan Karakter</h4>
    </div>
</div>
<section class="content mt-3">
    <div class="container-fluid">
        <ul class="nav nav-pills mb-3 flex-wrap" id="progressTabs">
            <?php $tabs = [
                'pembiasaan'    => ['icon'=>'fa-mosque',      'label'=>'Pembiasaan'], 
                'tahfidz'       => ['icon'=>'fa-book-open',  'label'=>'Tahfidz'], 
                'ekskul'        => ['icon'=>'fa-trophy',     'label'=>'Ekskul'], 
                'kokulikuler'   => ['icon'=>'fa-flask',      'label'=>'Kokulikuler'],
                'kewirausahaan' => ['icon'=>'fa-store',      'label'=>'Kewirausahaan']
            ]; ?>
            <?php foreach ($tabs as $key => $t): ?>
            <li class="nav-item mr-1 mb-1">
                <a class="nav-link font-weight-bold <?= $tab === $key ? 'active' : '' ?>"
                   href="<?= BASE_URL ?>siswa_portal/progress?tab=<?= $key ?>"
                   style="border-radius:10px; font-size:0.85rem;">
                    <i class="fas <?= $t['icon'] ?> mr-1"></i> <?= $t['label'] ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($tab === 'pembiasaan'): ?>
        <div class="card shadow-sm border-0" style="border-radius:14px; overflow:hidden;">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8fafc;"><tr class="text-muted" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                        <th class="py-2 pl-4">Pembiasaan</th><th class="py-2 text-center">Pertemuan</th><th class="py-2 text-center">Hadir</th><th class="py-2 text-center">Terakhir</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($pembiasaan)): ?><tr><td colspan="4" class="text-center text-muted py-4"><em>Belum ada data pembiasaan.</em></td></tr>
                        <?php else: foreach ($pembiasaan as $p): ?>
                        <tr>
                            <td class="pl-4 align-middle font-weight-bold"><?= htmlspecialchars($p['nama_kegiatan']) ?></td>
                            <td class="text-center align-middle"><?= $p['total_pertemuan'] ?></td>
                            <td class="text-center align-middle text-success font-weight-bold"><?= $p['total_hadir'] ?></td>
                            <td class="text-center align-middle text-muted" style="font-size:0.8rem;"><?= $p['terakhir_hadir'] ? date('d M Y', strtotime($p['terakhir_hadir'])) : '-' ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($tab === 'tahfidz'): ?>
        <div class="row mb-3">
            <div class="col-md-6"><div class="card border-0 shadow-sm py-3 text-center" style="border-radius:12px;"><div class="text-muted" style="font-size:0.7rem;">TOTAL SETORAN</div><div class="font-weight-bold text-dark" style="font-size:1.6rem;"><?= $tahfidz['summary']['total_setoran'] ?? 0 ?></div></div></div>
            <div class="col-md-6"><div class="card border-0 shadow-sm py-3 text-center" style="border-radius:12px;"><div class="text-muted" style="font-size:0.7rem;">RATA-RATA NILAI</div><div class="font-weight-bold text-success" style="font-size:1.6rem;"><?= round($tahfidz['summary']['rata_nilai'] ?? 0, 1) ?></div></div></div>
        </div>
        <div class="card shadow-sm border-0" style="border-radius:14px; overflow:hidden;">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8fafc;"><tr class="text-muted" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                        <th class="py-2 pl-4">Tanggal</th><th class="py-2">Surah/Ayat</th><th class="py-2">Nilai</th><th class="py-2">Musyrif</th>
                    </tr></thead>
                    <tbody>
                        <?php if (empty($tahfidz['jurnal'])): ?><tr><td colspan="4" class="text-center text-muted py-4"><em>Belum ada jurnal tahfidz.</em></td></tr>
                        <?php else: foreach ($tahfidz['jurnal'] as $tj): ?>
                        <tr>
                            <td class="pl-4 align-middle" style="font-size:0.8rem;"><?= date('d M Y', strtotime($tj['tanggal'])) ?></td>
                            <td class="align-middle" style="font-size:0.82rem;"><?= htmlspecialchars($tj['nama_surah'] ?? '-') ?> (Ayat <?= $tj['ayat_awal'] ?? '' ?> - <?= $tj['ayat_akhir'] ?? '' ?>)</td>
                            <td class="align-middle"><span class="badge badge-success"><?= $tj['nilai'] ?? '-' ?></span></td>
                            <td class="align-middle text-muted" style="font-size:0.8rem;"><?= htmlspecialchars($tj['nama_guru'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($tab === 'ekskul'): ?>
        <?php if (empty($ekskul)): ?><div class="alert alert-info rounded-lg"><i class="fas fa-info-circle mr-2"></i>Belum terdaftar di ekskul manapun.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($ekskul as $e): ?>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #10b981 !important;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-dark mb-1"><?= htmlspecialchars($e['nama_ekskul']) ?></h6>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-calendar-day mr-1"></i><?= htmlspecialchars($e['hari'] ?? '-') ?> &bull; <?= htmlspecialchars($e['jam_mulai'] ?? '') ?> - <?= htmlspecialchars($e['jam_selesai'] ?? '') ?></div>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-user-tie mr-1"></i>Pembina: <?= htmlspecialchars($e['nama_pembina'] ?? '-') ?></div>
                        <?php if ($e['nilai']): ?><div class="mt-1"><span class="badge badge-success">Nilai: <?= $e['nilai'] ?></span></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php elseif ($tab === 'kokulikuler'): ?>
        <?php if (empty($kokulikuler)): ?><div class="alert alert-info rounded-lg"><i class="fas fa-info-circle mr-2"></i>Belum terdaftar di kokulikuler manapun.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($kokulikuler as $k): ?>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #8b5cf6 !important;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-dark mb-1"><?= htmlspecialchars($k['nama_kokulikuler']) ?></h6>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-calendar-day mr-1"></i><?= htmlspecialchars($k['hari'] ?? '-') ?> &bull; <?= htmlspecialchars($k['jam_mulai'] ?? '') ?> - <?= htmlspecialchars($k['jam_selesai'] ?? '') ?></div>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-user-tie mr-1"></i>Pembina: <?= htmlspecialchars($k['nama_pembina'] ?? '-') ?></div>
                        <?php if ($k['nilai']): ?><div class="mt-1"><span class="badge badge-primary">Nilai: <?= $k['nilai'] ?></span></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php elseif ($tab === 'kewirausahaan'): ?>
        <?php if (empty($kewirausahaan)): ?><div class="alert alert-info rounded-lg"><i class="fas fa-info-circle mr-2"></i>Belum terdaftar di program kewirausahaan manapun.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($kewirausahaan as $kw): ?>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm" style="border-radius:12px; border-left:4px solid #f59e0b !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-bold text-dark mb-1"><?= htmlspecialchars($kw['nama_kegiatan']) ?></h6>
                            <span class="badge badge-warning text-white"><?= htmlspecialchars($kw['kelompok'] ?? 'Individu') ?></span>
                        </div>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-calendar-day mr-1"></i><?= htmlspecialchars($kw['hari'] ?? '-') ?> &bull; <?= htmlspecialchars($kw['jam'] ?? '-') ?></div>
                        <div class="text-muted" style="font-size:0.78rem;"><i class="fas fa-user-tie mr-1"></i>Pembina: <?= htmlspecialchars($kw['nama_pembina'] ?? '-') ?></div>
                        <div class="mt-2 d-flex gap-2">
                            <small class="text-muted mr-3"><i class="fas fa-chalkboard-teacher mr-1"></i><?= $kw['total_pertemuan'] ?> Sesi</small>
                            <small class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i><?= $kw['total_hadir'] ?> Hadir</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
