<?php include __DIR__.'/partials/header.php'; ?>
<style>
    .gradient-card-1 { background: #4f46e5; color: white; }
    .gradient-card-2 { background: #10b981; color: white; }
    .gradient-card-3 { background: #f59e0b; color: white; }
    .gradient-card-4 { background: #ef4444; color: white; }
    
    .card-modern {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 20px;
    }
    .card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .icon-large {
        font-size: 3rem;
        opacity: 0.2;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    .stat-value { font-size: 2.2rem; font-weight: 700; line-height: 1.2; }
    .stat-label { font-size: 0.9rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .table-modern thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 15px;
    }
    .table-modern tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    /* Toolbar Styling */
    .toolbar-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 10px;
    }
</style>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="fas fa-user-graduate mr-2"></i> Dashboard PPDB</h1>
        <p class="text-muted mb-0">Manajemen Penerimaan Peserta Didik Baru</p>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="index.php?mod=dashboard">Home</a></li>
            <li class="breadcrumb-item active">PPDB</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
<div class="container-fluid">

    <!-- STATS ROW -->
    <div class="row">
        <?php 
            $total = array_sum(array_column($statistics, 'jumlah'));
            $online = array_sum(array_column(array_filter($statistics, fn($s) => $s['sumber_pendaftaran'] == 'online'), 'jumlah'));
            $manual = array_sum(array_column(array_filter($statistics, fn($s) => $s['sumber_pendaftaran'] == 'manual'), 'jumlah'));
            // Diterima = status 'diterima' + 'diproses_jadi_siswa' (sudah dipromosikan)
            $diterima = array_sum(array_column(array_filter($statistics, fn($s) => $s['status'] == 'diterima' || $s['status'] == 'diproses_jadi_siswa'), 'jumlah'));
            $pending = array_sum(array_column(array_filter($statistics, fn($s) => $s['status'] == 'pending'), 'jumlah'));
            $ditolak = array_sum(array_column(array_filter($statistics, fn($s) => $s['status'] == 'ditolak'), 'jumlah'));
        ?>
        
        <div class="col-lg-3 col-6">
            <div class="card card-modern gradient-card-1 p-3">
                <div class="inner position-relative">
                    <div class="stat-value"><?= $total ?></div>
                    <div class="stat-label">Total Pendaftar</div>
                    <div class="icon-large"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
             <div class="card card-modern gradient-card-2 p-3">
                <div class="inner position-relative">
                    <div class="stat-value"><?= $diterima ?></div>
                    <div class="stat-label">Diterima</div>
                    <div class="icon-large"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
             <div class="card card-modern gradient-card-3 p-3">
                <div class="inner position-relative">
                    <div class="stat-value"><?= $pending ?></div>
                    <div class="stat-label">Menunggu Verifikasi</div>
                    <div class="icon-large"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
             <div class="card card-modern gradient-card-4 p-3">
                <div class="inner position-relative">
                    <div class="stat-value"><?= $ditolak ?></div>
                    <div class="stat-label">Ditolak</div>
                    <div class="icon-large"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT ROW -->
    <div class="row">
        <!-- LEFT COLUMN: TABLE -->
        <div class="col-lg-8">
            
            <!-- Toolbar & Actions -->
            <div class="toolbar-container">
                <h4 class="font-weight-bold text-dark mb-0">Data Pendaftar</h4>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success shadow-sm mr-2" data-toggle="modal" data-target="#modalImport">
                        <i class="fas fa-file-excel mr-1"></i> Import Excel
                    </button>
                    <a href="index.php?mod=ppdb&act=form" class="btn btn-primary shadow-sm mr-2">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Manual
                    </a>
                    <a href="index.php?mod=ppdb&act=promote_massal" 
                       class="btn btn-warning shadow-sm text-white" 
                       onclick="return confirm('PERINGATAN: Aksi ini akan mengunci NIPD dan memindahkan siswa DITERIMA ke Data Master Siswa. Lanjutkan?')">
                        <i class="fas fa-rocket mr-1"></i> Generate NIPD Massal
                    </a>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="card card-modern shadow-sm">
                <!-- Filter Section -->
                <div class="card-body bg-light border-bottom pt-3 pb-3">
                     <form method="GET" action="index.php" class="form-row align-items-end">
                        <input type="hidden" name="mod" value="ppdb">
                        <input type="hidden" name="act" value="index">
                        
                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                            <label class="small text-muted font-weight-bold mb-1">SUMBER PENDAFTARAN</label>
                            <select name="filter_sumber" class="form-control custom-select">
                                <option value="all" <?= $filter_sumber == 'all' ? 'selected' : '' ?>>Semua Sumber</option>
                                <option value="online" <?= $filter_sumber == 'online' ? 'selected' : '' ?>>Online (Website)</option>
                                <option value="manual" <?= $filter_sumber == 'manual' ? 'selected' : '' ?>>Manual (Admin)</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                             <label class="small text-muted font-weight-bold mb-1">STATUS</label>
                            <select name="filter_status" class="form-control custom-select">
                                <option value="all" <?= $filter_status == 'all' ? 'selected' : '' ?>>Semua Status</option>
                                <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="diverifikasi" <?= $filter_status == 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                                <option value="diterima" <?= $filter_status == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                <option value="ditolak" <?= $filter_status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                <option value="diproses_jadi_siswa" <?= $filter_status == 'diproses_jadi_siswa' ? 'selected' : '' ?>>Sudah Jadi Siswa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary flex-fill mr-2"><i class="fas fa-filter"></i> Filter</button>
                                <a href="index.php?mod=ppdb&act=index" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Pendaftar</th>
                                    <th>Asal Sekolah</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($list_pendaftar)): ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data pendaftar yang sesuai filter.</td></tr>
                                <?php endif; ?>
                                
                                <?php $no = 1; foreach ($list_pendaftar as $data): ?>
                                <tr>
                                    <td class="align-middle text-center text-muted"><?= $no++; ?></td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mr-3 border" style="width:40px; height:40px;">
                                                <i class="fas fa-user text-secondary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 font-weight-bold text-dark"><?= htmlspecialchars($data['nama_lengkap']); ?></h6>
                                                <small class="text-muted d-block">
                                                    <?= htmlspecialchars($data['no_pendaftaran']); ?> &bull; 
                                                    <?php if ($data['sumber_pendaftaran'] == 'online'): ?>
                                                        <span class="text-info font-weight-bold">Online</span>
                                                    <?php else: ?>
                                                        <span class="text-warning font-weight-bold">Manual</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-muted">
                                        <?= htmlspecialchars($data['asal_sekolah'] ?? '-'); ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php 
                                            $status = $data['status'];
                                            $badgeClass = 'badge-secondary';
                                            $badgeIcon = 'fa-minus';
                                            
                                            if ($status == 'diverifikasi') { $badgeClass = 'badge-info'; $badgeIcon = 'fa-search'; }
                                            elseif ($status == 'diterima') { $badgeClass = 'badge-success'; $badgeIcon = 'fa-check'; }
                                            elseif ($status == 'ditolak') { $badgeClass = 'badge-danger'; $badgeIcon = 'fa-times'; }
                                            elseif ($status == 'diproses_jadi_siswa') { $badgeClass = 'badge-primary'; $badgeIcon = 'fa-user-graduate'; }
                                            elseif ($status == 'pending') { $badgeClass = 'badge-warning'; $badgeIcon = 'fa-clock'; }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-verify mb-1"
                                                data-id="<?= $data['id'] ?>"
                                                data-nama="<?= htmlspecialchars($data['nama_lengkap']) ?>"
                                                data-nopend="<?= htmlspecialchars($data['no_pendaftaran']) ?>"
                                                data-nisn="<?= htmlspecialchars($data['nisn']) ?>"
                                                data-sekolah="<?= htmlspecialchars($data['asal_sekolah']) ?>"
                                                data-status="<?= $status ?>"
                                                data-link-detail="index.php?mod=ppdb&act=detail&id=<?= $data['id']; ?>">
                                                <i class="fas fa-check-circle mr-1"></i> Verifikasi
                                            </button>
                                            
                                            <?php if (empty($data['id_siswa']) || $data['id_siswa'] == 0): ?>
                                                <a href="index.php?mod=ppdb&act=delete&id=<?= $data['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-delete-confirm">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                </a>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Sudah dipromosikan ke siswa">
                                                    <i class="fas fa-lock mr-1"></i> Terkunci
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: CHART & INFO -->
        <div class="col-lg-4">
            
            <!-- GRAFIK -->
            <div class="card card-modern shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title font-weight-bold mb-0">Statistik Pendaftar</h5>
                </div>
                <div class="card-body pt-0">
                    <div style="position: relative; height: 250px;">
                        <canvas id="ppdbChart"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Distribusi status pendaftaran saat ini.</small>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info -->
            <div class="card card-modern shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3"><i class="fas fa-info-circle mr-2"></i> Informasi Sistem</h6>
                    <ul class="list-unstyled small mb-0" style="line-height: 1.6;">
                        <li class="mb-2">&bull; Pendaftaran <strong>Online</strong> masuk otomatis dengan status 'Pending'.</li>
                        <li class="mb-2">&bull; Gunakan tombol <strong>Verifikasi</strong> pada tabel untuk memeriksa data dan dokumen siswa.</li>
                        <li class="mb-2">&bull; <strong>Generate NIPD Massal</strong> hanya untuk siswa berstatus 'Diterima' yang belum masuk Data Master.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
</section>

<!-- MODAL VERIFIKASI (Modernized) -->
<div class="modal fade" id="modalVerifikasi" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-gradient-light border-bottom-0">
        <h5 class="modal-title font-weight-bold text-dark">Verifikasi Pendaftar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center mb-4">
            <div class="avatar-circle mx-auto mb-3 bg-light d-flex align-items-center justify-content-center border shadow-sm" style="width: 80px; height: 80px; border-radius: 50%;">
                <i class="fas fa-user-graduate fa-3x text-muted"></i>
            </div>
            <h4 id="v-nama" class="font-weight-bold mb-1">Nama Siswa</h4>
            <span class="badge badge-light border px-3 py-1 text-muted" id="v-nopend">NO. PENDAFTARAN</span>
        </div>
        
        <div class="row text-center mb-4 bg-light rounded py-3 mx-0">
            <div class="col-6 border-right">
                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.7rem;">NISN</small>
                <div id="v-nisn" class="font-weight-bold text-dark">-</div>
            </div>
            <div class="col-6">
                 <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.7rem;">Asal Sekolah</small>
                <div id="v-sekolah" class="font-weight-bold text-dark">-</div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4 px-2">
            <span class="text-muted">Status Saat Ini:</span>
            <span id="v-status-badge" class="badge badge-warning px-3 py-2">PENDING</span>
        </div>

        <a href="#" id="btn-lihat-detail" class="btn btn-outline-info btn-block mb-3 rounded-pill">
            <i class="fas fa-external-link-alt mr-2"></i> Lihat Dokumen & Detail Lengkap
        </a>
        
        <hr class="my-4">
        <p class="text-center text-muted small mb-3">Tentukan Keputusan Verifikasi:</p>

        <div class="row">
           <div class="col-6">
                 <a href="#" id="btn-terima" class="btn btn-success btn-block shadow-sm py-2">
                    <i class="fas fa-check-circle mr-1"></i> TERIMA
                </a>
           </div>
           <div class="col-6">
                 <a href="#" id="btn-tolak" class="btn btn-danger btn-block shadow-sm py-2">
                    <i class="fas fa-times-circle mr-1"></i> TOLAK
                </a>
           </div>
        </div>
        <div class="mt-3">
             <a href="#" id="btn-reset" class="btn btn-light btn-block text-muted border">
                <i class="fas fa-undo mr-1"></i> Reset ke Status Pending
            </a>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- MODAL IMPORT (BARU) -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="index.php?mod=ppdb&act=import" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i> Import Data PPDB</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <h6><i class="fas fa-info-circle mr-2"></i> Petunjuk Import:</h6>
                    <ol class="small mb-0 pl-3">
                        <li>Gunakan file dengan format <strong>.xls</strong> atau <strong>.xlsx</strong>.</li>
                        <li>Pastikan urutan kolom sesuai dengan template yang tersedia.</li>
                        <li>Data yang wajib diisi adalah <strong>Nama Lengkap</strong>.</li>
                    </ol>
                </div>
                
                <div class="form-group mb-4">
                    <label class="font-weight-bold"><i class="fas fa-download mr-1"></i> 1. Download Template</label>
                    <a href="index.php?mod=ppdb&act=get_template" class="btn btn-outline-success btn-block">
                        <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
                    </a>
                </div>
                
                <div class="form-group mb-0">
                    <label class="font-weight-bold"><i class="fas fa-upload mr-1"></i> 2. Pilih File Excel</label>
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="custom-file-input" id="fileExcel" accept=".xls,.xlsx" required>
                        <label class="custom-file-label" for="fileExcel">Pilih file...</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-upload mr-2"></i> Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__.'/partials/footer.php'; ?>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // Modal Logic
        // Gunakan event delegation agar aman jika tabel di-reload atau di-manipulasi
        $(document).on('click', '.btn-verify', function(e) {
            e.preventDefault(); // Mencegah fail safe
            
            var btn = $(this);
            var id = btn.data('id');
            var nama = btn.data('nama');
            var nopend = btn.data('nopend');
            var nisn = btn.data('nisn');
            var sekolah = btn.data('sekolah');
            var status = btn.data('status');
            var detailSrc = btn.data('link-detail');

            // 1. Populate Data
            $('#v-nama').text(nama);
            $('#v-nopend').text(nopend);
            $('#v-nisn').text(nisn);
            $('#v-sekolah').text(sekolah);
            
            // 2. Update Badge Status
            var badgeClass = 'badge-secondary';
            if(status === 'diterima') badgeClass = 'badge-success';
            else if(status === 'ditolak') badgeClass = 'badge-danger';
            else if(status === 'pending') badgeClass = 'badge-warning';
            else if(status === 'diproses_jadi_siswa') badgeClass = 'badge-primary';
            
            $('#v-status-badge')
                .removeClass('badge-secondary badge-success badge-danger badge-warning badge-primary')
                .addClass(badgeClass)
                .text(status.toUpperCase());

            $('#btn-lihat-detail').attr('href', detailSrc);

            // 3. Construct URLs & Logic
            var baseUrl = 'index.php?mod=ppdb&act=update_status&id=' + id;
            $('#btn-terima').attr('href', baseUrl + '&status=diterima');
            $('#btn-tolak').attr('href', baseUrl + '&status=ditolak');
            $('#btn-reset').attr('href', baseUrl + '&status=pending');

            // 4. Show Modal (Manual Trigger)
            $('#modalVerifikasi').modal('show');
        });

        // Chart Logic
        if (typeof Chart !== 'undefined') {
            var ctx = document.getElementById('ppdbChart');
            if (ctx) {
                var ppdbChart = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Diterima', 'Pending', 'Ditolak'],
                        datasets: [{
                            data: [
                                <?= $diterima ?>, 
                                <?= $pending ?>, 
                                <?= $ditolak ?>
                            ],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        cutoutPercentage: 65,
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }
        } else {
            console.warn('Chart.js not loaded');
        }

        // Custom File Input Label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>