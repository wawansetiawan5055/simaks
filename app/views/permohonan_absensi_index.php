<?php include __DIR__ . '/partials/header.php'; ?>

<style>
/* ===== PERMOHONAN ABSENSI — SISI PETUGAS ===== */
.page-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #1d6fa4 60%, #0f766e 100%);
    border-radius: 16px;
    color: white;
    padding: 22px 28px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.page-hero::after {
    content: '';
    position: absolute;
    right: -20px; top: -20px;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
}

/* Filter bar */
.filter-bar { background: #f8fafc; border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; }
.filter-bar .form-control, .filter-bar .btn { border-radius: 8px !important; font-size: 0.83rem; }

/* Status pill */
.sp { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:0.78rem; font-weight:600; }
.sp-menunggu  { background:#fef3c7; color:#92400e; border:1px solid #f59e0b; }
.sp-disetujui { background:#d1fae5; color:#065f46; border:1px solid #10b981; }
.sp-ditolak   { background:#fee2e2; color:#991b1b; border:1px solid #ef4444; }

/* Tabel */
.tbl-permohonan { border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
.tbl-permohonan thead th { background: #1d6fa4; color: white; font-size: 0.78rem; letter-spacing: 0.5px; text-transform: uppercase; border: none; padding: 12px 14px; }
.tbl-permohonan tbody tr { transition: background 0.15s; }
.tbl-permohonan tbody tr:hover { background: #f0f9ff; }
.tbl-permohonan tbody td { vertical-align: middle; font-size: 0.85rem; border-color: #f1f5f9; }

/* Jenis badge */
.jenis-sakit   { color: #ef4444; }
.jenis-izin    { color: #f59e0b; }
.jenis-lainnya { color: #8b5cf6; }

/* Count stats */
.stat-pill { border-radius:50px; padding:4px 14px; font-size:0.78rem; font-weight:700; }

/* Modal foto */
#fotoPreviewImg { max-width:100%; border-radius:10px; }

/* Aksi buttons */
.btn-approve { border-radius:8px; font-size:0.8rem; padding:5px 14px; }
.btn-reject  { border-radius:8px; font-size:0.8rem; padding:5px 14px; }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1><i class="fas fa-file-medical mr-2 text-info"></i> Permohonan Absensi Siswa</h1>
            </div>
            <div class="col-sm-6 text-right">
                <small class="text-muted">
                    <?php if ($can_approve): ?>
                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Anda dapat Menyetujui/Menolak</span>
                    <?php else: ?>
                        <span class="badge badge-secondary"><i class="fas fa-eye mr-1"></i> Mode Lihat Saja</span>
                    <?php endif; ?>
                </small>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
                <i class="fas fa-check-circle mr-2"></i> <?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Hero stats -->
        <div class="page-hero">
            <?php
                $total      = count($daftar_permohonan);
                $n_menunggu = count(array_filter($daftar_permohonan, fn($r) => $r['status'] === 'Menunggu'));
                $n_setuju   = count(array_filter($daftar_permohonan, fn($r) => $r['status'] === 'Disetujui'));
                $n_tolak    = count(array_filter($daftar_permohonan, fn($r) => $r['status'] === 'Ditolak'));
            ?>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="font-weight-bold mb-1"><i class="fas fa-clipboard-list mr-2"></i> Daftar Permohonan Siswa</h5>
                    <p class="mb-0" style="font-size:0.85rem; opacity:0.85;">Kelola permohonan izin dan sakit siswa secara terpusat.</p>
                </div>
                <div class="col-md-6 mt-2 mt-md-0">
                    <div class="d-flex flex-wrap justify-content-md-end" style="gap:8px;">
                        <span class="stat-pill bg-light text-dark">Total: <?= $total ?></span>
                        <span class="stat-pill bg-warning text-dark"><i class="fas fa-clock mr-1"></i> <?= $n_menunggu ?> Menunggu</span>
                        <span class="stat-pill bg-success text-white"><i class="fas fa-check mr-1"></i> <?= $n_setuju ?> Disetujui</span>
                        <span class="stat-pill bg-danger text-white"><i class="fas fa-times mr-1"></i> <?= $n_tolak ?> Ditolak</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="get" action="index.php" class="filter-bar">
            <input type="hidden" name="mod" value="permohonan_absensi">
            <div class="row align-items-end" style="gap: 0 0;">
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-muted mb-1" style="font-size:0.75rem; font-weight:600;">TANGGAL</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($filters['tanggal']) ?>">
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-muted mb-1" style="font-size:0.75rem; font-weight:600;">STATUS</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Menunggu"  <?= $filters['status']==='Menunggu'  ? 'selected':'' ?>>Menunggu</option>
                        <option value="Disetujui" <?= $filters['status']==='Disetujui' ? 'selected':'' ?>>Disetujui</option>
                        <option value="Ditolak"   <?= $filters['status']==='Ditolak'   ? 'selected':'' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-muted mb-1" style="font-size:0.75rem; font-weight:600;">JENIS</label>
                    <select name="jenis_absensi" class="form-control">
                        <option value="">Semua Jenis</option>
                        <option value="piket" <?= $filters['jenis_absensi']==='piket' ? 'selected':'' ?>>Kelas (Piket)</option>
                        <option value="mapel" <?= $filters['jenis_absensi']==='mapel' ? 'selected':'' ?>>Per Mapel</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <label class="text-muted mb-1" style="font-size:0.75rem; font-weight:600;">KELAS</label>
                    <select name="id_kelas" class="form-control">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= $filters['id_kelas']==$k['id_kelas'] ? 'selected':'' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Cari
                    </button>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <a href="<?= BASE_URL ?>permohonan_absensi" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabel Permohonan -->
        <div class="card tbl-permohonan">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Untuk</th>
                                <th>Keterangan</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <?php if ($can_approve): ?><th style="width:160px;">Aksi</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftar_permohonan)): ?>
                                <tr>
                                    <td colspan="<?= $can_approve ? 10 : 9 ?>" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox" style="font-size:2rem; opacity:0.3; display:block; margin-bottom:10px;"></i>
                                        Tidak ada data permohonan untuk filter yang dipilih.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftar_permohonan as $idx => $r):
                                    $st_cls = strtolower($r['status']);
                                    $jenis_cls = match($r['jenis_izin']) { 'Sakit' => 'jenis-sakit', 'Izin' => 'jenis-izin', default => 'jenis-lainnya' };
                                    $jenis_icon = match($r['jenis_izin']) { 'Sakit' => 'fa-thermometer-half', 'Izin' => 'fa-hand-paper', default => 'fa-ellipsis-h' };
                                ?>
                                <tr>
                                    <td class="text-muted" style="font-size:0.78rem;"><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="font-weight-bold" style="font-size:0.85rem;"><?= htmlspecialchars($r['nama_siswa']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($r['nisn']) ?></small>
                                    </td>
                                    <td style="font-size:0.83rem;"><?= htmlspecialchars($r['nama_kelas'] ?? '-') ?></td>
                                    <td>
                                        <span class="font-weight-bold" style="font-size:0.83rem;"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></span>
                                        <br><small class="text-muted"><?= date('H:i', strtotime($r['created_at'])) ?> WIB</small>
                                    </td>
                                    <td>
                                        <span class="<?= $jenis_cls ?> font-weight-bold" style="font-size:0.83rem;">
                                            <i class="fas <?= $jenis_icon ?> mr-1"></i><?= $r['jenis_izin'] ?>
                                        </span>
                                    </td>
                                    <td style="font-size:0.82rem;">
                                        <?php if ($r['jenis_absensi'] === 'piket'): ?>
                                            <span class="text-info"><i class="fas fa-school mr-1"></i>Kelas</span>
                                        <?php else: ?>
                                            <span class="text-purple"><i class="fas fa-book mr-1"></i><?= htmlspecialchars($r['nama_mapel'] ?? 'Mapel') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width:160px;">
                                        <small class="text-muted"><?= htmlspecialchars($r['keterangan'] ?? '-') ?></small>
                                        <?php if (!empty($r['catatan_petugas'])): ?>
                                            <br><small class="<?= $r['status']==='Disetujui' ? 'text-success' : 'text-danger' ?>">
                                                ↳ <?= htmlspecialchars($r['catatan_petugas']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($r['foto_bukti'])): ?>
                                            <button class="btn btn-sm btn-light" style="border-radius:8px;"
                                                onclick="lihatFoto('<?= BASE_URL ?>uploads/permohonan/<?= htmlspecialchars($r['foto_bukti']) ?>')">
                                                <i class="fas fa-image text-info"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="sp sp-<?= $st_cls ?>">
                                            <?php if ($r['status']==='Menunggu'): ?>
                                                <i class="fas fa-clock"></i>
                                            <?php elseif ($r['status']==='Disetujui'): ?>
                                                <i class="fas fa-check-circle"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle"></i>
                                            <?php endif; ?>
                                            <?= $r['status'] ?>
                                        </span>
                                        <?php if (!empty($r['nama_petugas'])): ?>
                                            <br><small class="text-muted">oleh <?= htmlspecialchars($r['nama_petugas']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($can_approve): ?>
                                    <td>
                                        <?php if ($r['status'] === 'Menunggu'): ?>
                                            <div class="d-flex" style="gap:5px;">
                                                <button class="btn btn-success btn-approve"
                                                    onclick="prosesPermohonan(<?= $r['id_permohonan'] ?>, 'setujui', '<?= htmlspecialchars($r['nama_siswa']) ?>', '<?= $r['jenis_izin'] ?>')">
                                                    <i class="fas fa-check mr-1"></i> Setujui
                                                </button>
                                                <button class="btn btn-danger btn-reject"
                                                    onclick="prosesPermohonan(<?= $r['id_permohonan'] ?>, 'tolak', '<?= htmlspecialchars($r['nama_siswa']) ?>', '<?= $r['jenis_izin'] ?>')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size:0.78rem;">Sudah diproses</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Modal Lihat Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header bg-dark text-white border-0">
                <h6 class="modal-title"><i class="fas fa-image mr-2"></i> Foto Bukti</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center p-3">
                <img src="" id="fotoPreviewImg" style="max-width:100%; border-radius:10px;">
            </div>
        </div>
    </div>
</div>

<!-- Modal Proses (Approve/Tolak) -->
<div class="modal fade" id="modalProses" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            <form action="<?= BASE_URL ?>permohonan_absensi/proses" method="post" id="formProses">
                <input type="hidden" name="id_permohonan" id="inp_id">
                <input type="hidden" name="action" id="inp_action">
                <div class="modal-header border-0" id="modalProsesHeader">
                    <h5 class="modal-title" id="modalProsesTitle">Konfirmasi</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="modalProsesDesc" class="mb-3"></p>
                    <div class="form-group">
                        <label class="font-weight-bold">Catatan Petugas <small class="text-muted font-weight-normal">(opsional)</small></label>
                        <textarea name="catatan_petugas" class="form-control" rows="3"
                            style="border-radius:8px;"
                            placeholder="Misal: Surat dokter diterima, absensi diperbarui..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:8px;">Batal</button>
                    <button type="submit" class="btn" id="btnProsesSubmit" style="border-radius:8px; min-width:120px;">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
<script>
function lihatFoto(url) {
    document.getElementById('fotoPreviewImg').src = url;
    $('#modalFoto').modal('show');
}

function prosesPermohonan(id, action, namaSiswa, jenisIzin) {
    document.getElementById('inp_id').value     = id;
    document.getElementById('inp_action').value = action;

    const isSetujui = action === 'setujui';
    const header    = document.getElementById('modalProsesHeader');
    const title     = document.getElementById('modalProsesTitle');
    const desc      = document.getElementById('modalProsesDesc');
    const btn       = document.getElementById('btnProsesSubmit');

    if (isSetujui) {
        header.className = 'modal-header bg-success text-white border-0';
        title.textContent = 'Setujui Permohonan';
        desc.innerHTML   = `Anda akan <strong>menyetujui</strong> permohonan <b>${jenisIzin}</b> dari siswa <b>${namaSiswa}</b>.<br>Status absensi siswa akan otomatis diperbarui.`;
        btn.className    = 'btn btn-success';
        btn.textContent  = '✓ Setujui';
    } else {
        header.className = 'modal-header bg-danger text-white border-0';
        title.textContent = 'Tolak Permohonan';
        desc.innerHTML   = `Anda akan <strong>menolak</strong> permohonan <b>${jenisIzin}</b> dari siswa <b>${namaSiswa}</b>.`;
        btn.className    = 'btn btn-danger';
        btn.textContent  = '✗ Tolak';
    }

    $('#modalProses').modal('show');
}
</script>
