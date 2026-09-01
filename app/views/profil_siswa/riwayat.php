<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    /* ===== RIWAYAT PAGE STYLES ===== */
    .riwayat-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #7c3aed 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .riwayat-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .riwayat-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: -20px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    /* Status Badge Styles */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .status-menunggu {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    .status-disetujui {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border: 1px solid #10b981;
    }
    .status-ditolak {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    /* Timeline Card Style */
    .timeline-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .timeline-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    .timeline-card .card-left-bar {
        width: 5px;
        flex-shrink: 0;
        border-radius: 14px 0 0 14px;
    }
    .bar-menunggu { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
    .bar-disetujui { background: linear-gradient(180deg, #10b981, #34d399); }
    .bar-ditolak { background: linear-gradient(180deg, #ef4444, #f87171); }

    /* Category icon wrapper */
    .cat-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* Data changed list */
    .data-changed-list {
        list-style: none;
        padding: 0; margin: 0;
    }
    .data-changed-list li {
        display: flex;
        align-items: baseline;
        gap: 6px;
        padding: 3px 0;
        font-size: 0.85rem;
        border-bottom: 1px dashed #f1f5f9;
    }
    .data-changed-list li:last-child { border-bottom: none; }
    .data-changed-list .field-name {
        font-weight: 600;
        color: #475569;
        min-width: 130px;
        flex-shrink: 0;
    }
    .data-changed-list .field-value { color: #1e293b; }

    /* Summary stats */
    .stat-mini {
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-mini .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state .empty-icon {
        width: 80px; height: 80px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        color: #94a3b8;
        margin: 0 auto 20px;
    }

    /* Filter pill buttons */
    .filter-pills .btn {
        border-radius: 50px !important;
        padding: 5px 16px !important;
        font-size: 0.82rem !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .filter-pills .btn.active {
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (RIWAYAT PENGAJUAN SISWA)          */
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
        .riwayat-hero {
            padding: 14px 12px !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
        }
        .riwayat-hero h4 {
            font-size: 0.95rem !important;
        }
        .riwayat-hero p {
            font-size: 0.74rem !important;
        }
        .riwayat-hero .badge {
            font-size: 0.70rem !important;
            padding: 4px 8px !important;
        }
        .timeline-card {
            border-radius: 10px !important;
            margin-bottom: 10px !important;
        }
        .timeline-card .p-4 {
            padding: 10px 8px !important;
        }
        .cat-icon {
            width: 34px !important;
            height: 34px !important;
            font-size: 0.90rem !important;
            border-radius: 8px !important;
        }
        .status-badge {
            font-size: 0.68rem !important;
            padding: 3px 8px !important;
        }
        .data-changed-list li {
            font-size: 0.74rem !important;
            flex-direction: column !important;
            gap: 1px !important;
        }
        .data-changed-list .field-name {
            min-width: unset !important;
            font-size: 0.70rem !important;
        }
        .filter-pills .btn {
            font-size: 0.72rem !important;
            padding: 4px 10px !important;
        }
        .btn-sm {
            font-size: 0.72rem !important;
            padding: 5px 10px !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Riwayat Pengajuan Perubahan Data Siswa
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>profil_siswa/detail?id=<?= $id_siswa ?>" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3 shadow-sm mr-1">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Profil
                </a>
                <button type="button" class="btn btn-warning btn-sm font-weight-bold text-white rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#modalPengajuan">
                    <i class="fas fa-edit mr-1"></i> Ajukan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i> <?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- Hero Banner -->
        <div class="riwayat-hero">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="font-weight-bold mb-1">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Riwayat Pengajuan Perubahan Data
                    </h4>
                    <p class="mb-0" style="opacity:0.85; font-size:0.9rem;">
                        Pantau status semua pengajuan yang telah Anda kirimkan. Admin/TU akan memproses setiap pengajuan secara berkala.
                    </p>
                </div>
                <div class="col-md-4 mt-3 mt-md-0 text-md-right">
                    <?php
                        $total   = count($pengajuan_list);
                        $menunggu  = count(array_filter($pengajuan_list, fn($p) => $p['status'] === 'Menunggu'));
                        $disetujui = count(array_filter($pengajuan_list, fn($p) => $p['status'] === 'Disetujui'));
                        $ditolak   = count(array_filter($pengajuan_list, fn($p) => $p['status'] === 'Ditolak'));
                    ?>
                    <div class="d-flex justify-content-md-end gap-3 flex-wrap" style="gap: 8px;">
                        <span class="badge badge-light px-3 py-2" style="font-size:0.85rem; border-radius:50px;">
                            <i class="fas fa-list mr-1"></i> Total: <?= $total ?>
                        </span>
                        <span class="badge badge-warning px-3 py-2" style="font-size:0.85rem; border-radius:50px;">
                            <i class="fas fa-clock mr-1"></i> <?= $menunggu ?> Menunggu
                        </span>
                        <span class="badge badge-success px-3 py-2" style="font-size:0.85rem; border-radius:50px;">
                            <i class="fas fa-check mr-1"></i> <?= $disetujui ?> Disetujui
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Pill Buttons -->
        <?php if ($total > 0): ?>
        <div class="d-flex align-items-center mb-3 filter-pills flex-wrap" style="gap: 8px;">
            <span class="text-muted mr-2" style="font-size:0.85rem;"><i class="fas fa-filter mr-1"></i> Filter:</span>
            <button class="btn btn-sm btn-primary active" id="filter-all" onclick="filterRiwayat('all', this)">
                Semua (<?= $total ?>)
            </button>
            <button class="btn btn-sm btn-outline-warning" id="filter-menunggu" onclick="filterRiwayat('Menunggu', this)">
                <i class="fas fa-clock mr-1"></i> Menunggu (<?= $menunggu ?>)
            </button>
            <button class="btn btn-sm btn-outline-success" id="filter-disetujui" onclick="filterRiwayat('Disetujui', this)">
                <i class="fas fa-check-circle mr-1"></i> Disetujui (<?= $disetujui ?>)
            </button>
            <button class="btn btn-sm btn-outline-danger" id="filter-ditolak" onclick="filterRiwayat('Ditolak', this)">
                <i class="fas fa-times-circle mr-1"></i> Ditolak (<?= $ditolak ?>)
            </button>
        </div>
        <?php endif; ?>

        <!-- Riwayat List -->
        <div id="riwayat-list">
        <?php if (empty($pengajuan_list)): ?>
            <!-- Empty State -->
            <div class="card" style="border-radius:16px; border:none; box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <h5 class="font-weight-bold text-muted">Belum Ada Pengajuan</h5>
                        <p class="text-muted mb-4">Anda belum pernah mengajukan perubahan data. Jika ada data yang perlu diperbaiki, gunakan tombol di bawah ini.</p>
                        <button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#modalPengajuan">
                            <i class="fas fa-edit mr-2"></i> Ajukan Perubahan Data Sekarang
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($pengajuan_list as $idx => $p):
                $status_class = match($p['status']) {
                    'Disetujui' => 'disetujui',
                    'Ditolak'   => 'ditolak',
                    default     => 'menunggu'
                };
                $status_icon = match($p['status']) {
                    'Disetujui' => 'fa-check-circle',
                    'Ditolak'   => 'fa-times-circle',
                    default     => 'fa-clock'
                };
                $cat_icon = match(true) {
                    str_contains($p['kategori'], 'Berkas')    => ['fas fa-file-upload', 'bg-info', '#e0f2fe', '#0284c7'],
                    str_contains($p['kategori'], 'Orang Tua') => ['fas fa-users', 'bg-purple', '#ede9fe', '#7c3aed'],
                    str_contains($p['kategori'], 'Nama')      => ['fas fa-user-edit', 'bg-orange', '#fff7ed', '#ea580c'],
                    default                                    => ['fas fa-edit', 'bg-blue', '#eff6ff', '#2563eb']
                };
            ?>
            <div class="card timeline-card riwayat-item" data-status="<?= htmlspecialchars($p['status']) ?>">
                <div class="d-flex">
                    <div class="card-left-bar bar-<?= $status_class ?>"></div>
                    <div class="card-body p-4" style="flex:1;">
                        <!-- Header Row -->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                            <div class="d-flex align-items-center" style="gap:12px;">
                                <!-- Nomor urut -->
                                <span class="text-muted font-weight-bold" style="font-size:0.78rem; min-width:24px;">#<?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                <!-- Kategori Icon -->
                                <div class="cat-icon" style="background-color: <?= $cat_icon[2] ?>; color: <?= $cat_icon[3] ?>;">
                                    <i class="<?= $cat_icon[0] ?>"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-0"><?= htmlspecialchars($p['kategori']) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= date('d F Y', strtotime($p['created_at'])) ?>
                                        &nbsp;<i class="fas fa-clock mr-1 ml-1"></i>
                                        <?= date('H:i', strtotime($p['created_at'])) ?> WIB
                                    </small>
                                </div>
                            </div>
                            <!-- Status Badge -->
                            <span class="status-badge status-<?= $status_class ?> mt-1">
                                <i class="fas <?= $status_icon ?>"></i>
                                <?= htmlspecialchars($p['status']) ?>
                            </span>
                        </div>

                        <!-- Data yang Diajukan -->
                        <?php
                            $data_ubah = json_decode($p['data_perubahan'], true);
                            $field_labels = [
                                'nama'          => 'Nama Lengkap',
                                'nik'           => 'NIK',
                                'tempat_lahir'  => 'Tempat Lahir',
                                'tanggal_lahir' => 'Tanggal Lahir',
                                'nama_ayah'     => 'Nama Ayah',
                                'telp_ayah'     => 'Telp Ayah',
                                'nama_ibu'      => 'Nama Ibu',
                                'telp_ibu'      => 'Telp Ibu',
                                'jenis_berkas'  => 'Jenis Berkas',
                                'file_temp'     => 'File Temp',
                            ];
                        ?>
                        <?php if (is_array($data_ubah) && !empty($data_ubah)): ?>
                        <div class="p-3 rounded mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <p class="text-muted mb-2" style="font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-exchange-alt mr-1"></i> Data yang Diajukan
                            </p>
                            <ul class="data-changed-list">
                                <?php foreach ($data_ubah as $k => $v):
                                    if (empty($v) && $v !== '0') continue;
                                    $label = $field_labels[$k] ?? ucwords(str_replace('_', ' ', $k));
                                    // Format tanggal
                                    if ($k === 'tanggal_lahir' && $v) {
                                        $v = date('d F Y', strtotime($v));
                                    }
                                ?>
                                <li>
                                    <span class="field-name"><i class="fas fa-dot-circle mr-1" style="font-size:0.65rem; color:#94a3b8;"></i><?= $label ?></span>
                                    <span class="text-muted mx-1">:</span>
                                    <span class="field-value"><?= htmlspecialchars($v) ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Catatan Admin (jika ada) -->
                        <?php if (!empty($p['catatan_admin'])): ?>
                        <div class="p-3 rounded" style="background: <?= $p['status'] === 'Disetujui' ? '#f0fdf4' : '#fff7f7' ?>; border: 1px solid <?= $p['status'] === 'Disetujui' ? '#bbf7d0' : '#fecaca' ?>;">
                            <p class="mb-1" style="font-size:0.78rem; font-weight:700; color: <?= $p['status'] === 'Disetujui' ? '#065f46' : '#991b1b' ?>; text-transform:uppercase; letter-spacing:0.5px;">
                                <i class="fas fa-comment-alt mr-1"></i> Catatan Admin
                            </p>
                            <p class="mb-0" style="font-size:0.88rem; color: <?= $p['status'] === 'Disetujui' ? '#065f46' : '#7f1d1d' ?>;">
                                <?= htmlspecialchars($p['catatan_admin']) ?>
                            </p>
                        </div>
                        <?php elseif ($p['status'] === 'Menunggu'): ?>
                        <div class="d-flex align-items-center" style="gap:8px; color:#92400e; font-size:0.82rem;">
                            <div style="width:8px; height:8px; border-radius:50%; background:#f59e0b; animation: pulse-dot 1.5s infinite;"></div>
                            Pengajuan sedang menunggu ditinjau oleh Admin/TU...
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>

    </div>
</section>

<!-- Modal Pengajuan Perubahan Data -->
<div class="modal fade" id="modalPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b, #d97706); border:none;">
        <h5 class="modal-title text-white"><i class="fas fa-edit mr-2"></i> Formulir Pengajuan Perubahan Data</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= BASE_URL ?>profil_siswa/ajukan" method="post">
          <div class="modal-body">
              <div class="alert alert-info" style="border-radius:10px; font-size:0.88rem;">
                  <i class="fas fa-info-circle mr-2"></i>
                  Isi hanya kolom yang ingin Anda ubah. Kolom yang dikosongkan tidak akan diubah. Data tidak akan langsung berubah melainkan menunggu persetujuan Admin/TU.
              </div>
              <div class="form-group">
                  <label class="font-weight-bold">Kategori Perubahan</label>
                  <select name="kategori" class="form-control" required>
                      <option value="Perbaikan Nama/Identitas">Perbaikan Nama/Identitas</option>
                      <option value="Perubahan Data Orang Tua">Perubahan Data Orang Tua/Wali</option>
                      <option value="Lainnya">Lainnya</option>
                  </select>
              </div>
              <div class="row">
                  <div class="col-md-6">
                      <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                          <i class="fas fa-user mr-1"></i> Identitas Siswa
                      </h6>
                      <div class="form-group">
                          <label>Nama Lengkap Benar</label>
                          <input type="text" class="form-control" name="nama" placeholder="Contoh perbaikan ejaan...">
                      </div>
                      <div class="form-group">
                          <label>NIK Benar</label>
                          <input type="text" class="form-control" name="nik" maxlength="16">
                      </div>
                      <div class="form-group">
                          <label>Tempat Lahir Benar</label>
                          <input type="text" class="form-control" name="tempat_lahir">
                      </div>
                      <div class="form-group">
                          <label>Tanggal Lahir Benar</label>
                          <input type="date" class="form-control" name="tanggal_lahir">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">
                          <i class="fas fa-users mr-1"></i> Data Orang Tua
                      </h6>
                      <div class="form-group">
                          <label>Nama Ayah Benar</label>
                          <input type="text" class="form-control" name="nama_ayah">
                      </div>
                      <div class="form-group">
                          <label>No. Telp Ayah Aktif</label>
                          <input type="text" class="form-control" name="telp_ayah">
                      </div>
                      <div class="form-group">
                          <label>Nama Ibu Benar</label>
                          <input type="text" class="form-control" name="nama_ibu">
                      </div>
                      <div class="form-group">
                          <label>No. Telp Ibu Aktif</label>
                          <input type="text" class="form-control" name="telp_ibu">
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Batal</button>
            <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan</button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<style>
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }
</style>

<script>
    function filterRiwayat(status, btn) {
        // Update active button
        document.querySelectorAll('.filter-pills .btn').forEach(b => {
            b.classList.remove('active', 'btn-primary', 'btn-warning', 'btn-success', 'btn-danger');
            if (b.classList.contains('btn-outline-warning')) b.classList.remove('btn-warning');
            if (b.classList.contains('btn-outline-success')) b.classList.remove('btn-success');
            if (b.classList.contains('btn-outline-danger')) b.classList.remove('btn-danger');
        });
        btn.classList.add('active');

        // Show/hide cards
        document.querySelectorAll('.riwayat-item').forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = '';
                card.style.animation = 'fadeInUp 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
