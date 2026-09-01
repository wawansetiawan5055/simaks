<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>

<?php 
$is_manager = in_array(1, $_SESSION['role_ids'] ?? []) || 
              in_array('Kurikulum', $_SESSION['roles'] ?? []) || 
              $can_create || $can_update || $can_delete; 
?>
<?php if ($is_manager): ?>
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
<?php endif; ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  /* KALENDER PENDIDIKAN MODERN UI (DESKTOP & MOBILE) */
  .kalender-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);
  }

  .kalender-actions-grid {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
  }

  .btn-kalender-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.5rem 0.95rem;
    border-radius: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    border: none;
    line-height: 1.25;
    text-decoration: none !important;
  }
  .btn-kalender-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  }

  .kalender-filter-card {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 6px 24px rgba(0,0,0,0.06) !important;
    overflow: hidden;
  }
  .kalender-filter-header {
    background: #ffffff;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .kalender-select-clean {
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #f8fafc !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    height: 38px !important;
    transition: all 0.2s ease !important;
  }
  .kalender-select-clean:focus {
    background-color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
  }

  .fc-day-sun {
    background-color: #fff0f0 !important;
  }
  .fc-day-sun .fc-col-header-cell-cushion {
    color: #dc3545 !important;
  }
  .calendar-row:hover {
    background-color: #f1f5f9 !important;
  }

  /* RESPONSIVE MOBILE VIEW (MAX-WIDTH 768px) */
  @media (max-width: 768px) {
    .kalender-header-icon {
      width: 36px !important;
      height: 36px !important;
      font-size: 1.05rem !important;
      border-radius: 8px !important;
      margin-right: 8px !important;
    }
    .content-header h4 {
      font-size: 0.95rem !important;
      line-height: 1.25 !important;
    }
    .kalender-actions-grid {
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      gap: 6px !important;
      width: 100% !important;
      margin-top: 8px !important;
    }
    .btn-kalender-action {
      font-size: 0.74rem !important;
      padding: 0.45rem 0.5rem !important;
      border-radius: 6px !important;
      width: 100% !important;
      text-align: center !important;
    }
    .btn-kalender-action:first-child {
      grid-column: 1 / -1 !important;
    }

    .kalender-filter-card .card-body {
      padding: 0.85rem !important;
    }
    .kalender-filter-card .form-group {
      margin-bottom: 8px !important;
    }
    .kalender-select-clean {
      height: 36px !important;
      font-size: 0.78rem !important;
      border-radius: 6px !important;
    }

    /* FULLCALENDAR RESPONSIVE TWEAKS */
    .fc .fc-toolbar {
      flex-direction: column !important;
      gap: 8px !important;
      margin-bottom: 0.75rem !important;
    }
    .fc .fc-toolbar-title {
      font-size: 0.98rem !important;
      font-weight: 700 !important;
    }
    .fc .fc-button {
      font-size: 0.72rem !important;
      padding: 0.3rem 0.55rem !important;
      border-radius: 6px !important;
    }
    .fc .fc-col-header-cell-cushion {
      font-size: 0.68rem !important;
      padding: 4px 1px !important;
    }
    .fc .fc-daygrid-day-number {
      font-size: 0.70rem !important;
      padding: 2px 4px !important;
    }
    .fc .fc-daygrid-event {
      font-size: 0.64rem !important;
      padding: 1px 3px !important;
      border-radius: 4px !important;
      margin-bottom: 1px !important;
    }
  }

  /* --- UNIFIED PREVIEW STUDIO STYLES (GLOBAL) --- */
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

<div id="sectionMainKaldik">

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-md-6 col-12 d-flex align-items-center">
        <div class="mr-3 kalender-header-icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Kalender Pendidikan
          </h4>
          <small class="text-muted d-none d-sm-block">Kelola agenda kegiatan sekolah, hari libur, dan jadwal akademik</small>
        </div>
      </div>
      <div class="col-md-6 col-12 text-md-right mt-2 mt-md-0">
        <!-- TOP ACTIONS BAR -->
        <div class="kalender-actions-grid">
          <?php if ($can_create): ?>
            <button type="button" class="btn btn-warning btn-kalender-action text-white" data-toggle="modal" data-target="#modalTambahKegiatan">
              <i class="fas fa-plus"></i> Tambah Kegiatan
            </button>
            <button type="button" class="btn btn-info btn-kalender-action text-white" onclick="location.href='<?= BASE_URL ?>kalender_akademik/import_holidays?id_ta=<?= $filter_ta ?>'">
              <i class="fas fa-file-import"></i> Impor Libur
            </button>
          <?php endif; ?>
          <button type="button" class="btn btn-danger btn-kalender-action text-white" onclick="openFullscreenKaldikPreview('<?= BASE_URL ?>kalender_akademik/export_pdf?id_ta=<?= $filter_ta ?>')">
            <i class="fas fa-print mr-1"></i> Cetak / Pratinjau
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <!-- Filter Section -->
    <div class="card kalender-filter-card mb-4">
      <div class="kalender-filter-header">
        <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-filter mr-2 text-primary"></i> FILTER KEGIATAN & TAHUN AJARAN</h6>
      </div>
      <div class="card-body p-3">
        <form method="GET" id="filterForm">
          <input type="hidden" name="mod" value="kalender_akademik">
          <div class="row align-items-end">
            <div class="col-md-4 col-12 mb-2 mb-md-0">
              <div class="form-group mb-0">
                <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-calendar-alt text-primary mr-1"></i> Tahun Ajaran</label>
                <select name="id_ta" class="form-control kalender-select-clean font-weight-bold" onchange="this.form.submit()">
                  <option value="">-- Pilih Tahun Ajaran --</option>
                  <?php foreach ($ta_list as $ta): ?>
                    <option value="<?= $ta['id_ta'] ?>" <?= ($filter_ta == $ta['id_ta']) ? 'selected' : '' ?>>
                      TA <?= htmlspecialchars($ta['nama_ta']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-12 mb-2 mb-md-0">
              <div class="form-group mb-0">
                <label class="small font-weight-bold text-muted mb-1"><i class="fas fa-tags text-info mr-1"></i> Kategori Kegiatan</label>
                <select name="kategori" id="filterKategori" class="form-control kalender-select-clean font-weight-bold">
                  <option value="">Semua Kategori</option>
                  <?php 
                  $color_bullets = [
                      '#dc3545' => '🔴', '#fd7e14' => '🟠', '#ffc107' => '🟡', 
                      '#28a745' => '🟢', '#007bff' => '🔵', '#6f42c1' => '🟣', 
                      '#e83e8c' => '💖', '#6c757d' => '⚪', '#20c997' => '🟢'
                  ];
                  foreach ($kategori_list as $cat): 
                      $bullet = $color_bullets[$cat['warna']] ?? '●';
                  ?>
                      <option value="<?= $cat['nama_kategori'] ?>"><?= $bullet ?> <?= htmlspecialchars($cat['nama_kategori']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-12">
              <button type="button" class="btn btn-light border text-primary font-weight-bold btn-block"
                onclick="typeof calendar !== 'undefined' ? calendar.refetchEvents() : location.reload()" style="border-radius: 8px; height: 38px; font-size: 0.82rem;">
                <i class="fas fa-sync-alt mr-1"></i> Segarkan Kalender
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php if ($is_manager): ?>
    <!-- Calendar Card (Admin / Kurikulum View) -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
      <div class="card-body p-3 p-md-4">
        <div id="calendar"></div>
      </div>
      <div class="card-footer bg-light border-top p-3">
        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
          <span class="font-weight-bold text-muted mr-2 small text-uppercase" style="font-size: 0.72rem;">Keterangan:</span>
          <?php foreach ($kategori_list as $cat): ?>
            <span class="badge badge-pill badge-light border px-2.5 py-1" style="font-size: 0.72rem;">
                <span style="color: <?= $cat['warna'] ?>;">●</span> <?= htmlspecialchars($cat['nama_kategori']) ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php else: ?>
    <!-- Grouped Calendar Layout (Guru View) -->
    <div class="row" id="calendar-list-view">
        <?php if (empty($events)): ?>
            <div class="col-12">
                <div class="alert alert-info rounded-lg"><i class="fas fa-info-circle mr-2"></i>Belum ada kegiatan untuk filter yang dipilih.</div>
            </div>
        <?php else: ?>
            <?php
                // Group kegiatan by month
                $grouped = [];
                $bulan_ind = [
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                ];
                
                foreach ($events as $k) {
                    $m = date('m', strtotime($k['tanggal_mulai']));
                    $y = date('Y', strtotime($k['tanggal_mulai']));
                    $monthKey = $y . '-' . $m;
                    $monthName = $bulan_ind[$m] . ' ' . $y;
                    
                    if (!isset($grouped[$monthKey])) {
                        $grouped[$monthKey] = [
                            'name' => $monthName,
                            'events' => []
                        ];
                    }
                    $grouped[$monthKey]['events'][] = $k;
                }
            ?>
            <?php foreach ($grouped as $monthData): ?>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:14px; overflow:hidden;">
                    <div class="card-header border-0" style="background: linear-gradient(135deg, #1d6fa4 0%, #1e3a5f 100%); padding: 12px 20px;">
                        <h6 class="m-0 text-white font-weight-bold"><i class="far fa-calendar-alt mr-2"></i> <?= htmlspecialchars($monthData['name']) ?></h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background:#f8fafc;"><tr class="text-muted" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                                    <th class="py-2 pl-3" style="width:110px;">TGL</th>
                                    <th class="py-2">KEGIATAN</th>
                                </tr></thead>
                                <tbody>
                                    <?php foreach ($monthData['events'] as $k): ?>
                                    <tr onclick="showEventDetail(<?= $k['id_kalender'] ?>)" style="cursor:pointer;" class="calendar-row">
                                        <td class="pl-3 align-middle" style="font-size:0.8rem; font-weight: 600; color: #475569;">
                                            <?php
                                                $d1 = date('d', strtotime($k['tanggal_mulai']));
                                                $d2 = $k['tanggal_selesai'] && $k['tanggal_selesai'] !== $k['tanggal_mulai']
                                                    ? '-' . date('d', strtotime($k['tanggal_selesai'])) : '';
                                                echo $d1 . $d2;
                                            ?>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-dark" style="font-size:0.82rem; line-height: 1.3;">
                                                <?= htmlspecialchars($k['judul_kegiatan']) ?>
                                            </div>
                                            <div class="mt-1 d-flex flex-wrap align-items-center" style="gap: 5px;">
                                                <span class="badge" style="font-size:0.6rem; border-radius:4px; background:<?= htmlspecialchars($k['warna'] ?: '#e2e8f0') ?>; color:#fff; border: 1px solid rgba(0,0,0,0.1);">
                                                    <?= htmlspecialchars($k['kategori'] ?? 'Umum') ?>
                                                </span>
                                                <?php if ($k['deskripsi']): ?>
                                                    <span class="text-muted" style="font-size:0.7rem; display:inline-block; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fas fa-info-circle mr-1"></i><?= htmlspecialchars($k['deskripsi']) ?></span>
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
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- Modal Tambah/Edit Kegiatan -->
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="<?= BASE_URL ?>kalender_akademik/save" method="POST" id="formKegiatan">
        <input type="hidden" name="id_kalender" id="id_kalender" value="">
        <input type="hidden" name="id_ta" id="id_ta" value="<?= $filter_ta ?>">

        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Tambah Kegiatan Baru</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Judul Kegiatan <span class="text-danger">*</span></label>
            <input type="text" name="judul_kegiatan" id="judul_kegiatan" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kategori <span class="text-danger">*</span></label>
                <select name="kategori" id="kategori" class="form-control" required>
                  <?php 
                  foreach ($kategori_list as $cat): 
                    $bullet = $color_bullets[$cat['warna']] ?? '●';
                  ?>
                    <option value="<?= $cat['nama_kategori'] ?>"><?= $bullet ?> <?= htmlspecialchars($cat['nama_kategori']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Warna</label>
                <input type="color" name="warna" id="warna" class="form-control" value="#007bff">
                <small class="text-muted">Warna akan otomatis disesuaikan dengan kategori</small>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Kegiatan -->
<div class="modal fade" id="modalDetailKegiatan" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailTitle"></h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Kategori:</strong> <span id="detailKategori"></span></p>
        <p><strong>Tanggal:</strong> <span id="detailTanggal"></span></p>
        <p><strong>Deskripsi:</strong></p>
        <p id="detailDeskripsi" class="text-muted"></p>
      </div>
      <div class="modal-footer">
        <?php if ($can_update): ?>
          <button type="button" class="btn btn-warning btn-sm" id="btnEdit">
            <i class="fas fa-edit"></i> Edit
          </button>
        <?php endif; ?>
        <?php if ($can_delete): ?>
          <button type="button" class="btn btn-danger btn-sm" id="btnDelete">
            <i class="fas fa-trash"></i> Hapus
          </button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

</div> <!-- END #sectionMainKaldik -->

<!-- ================================================================= -->
<!-- SECTION PREVIEW STUDIO: KALENDER AKADEMIK -->
<!-- ================================================================= -->
<div id="sectionPreviewKaldik" style="display: none;" class="pt-2">
  <div class="container-fluid">
    <div class="preview-unified-card">
      <div class="preview-unified-header">
        <div class="d-flex align-items-center">
          <button type="button" onclick="closeFullscreenKaldikPreview()" class="btn-icon-studio mr-2" title="Kembali ke Kalender">
            <i class="fas fa-arrow-left"></i> <span>Kembali</span>
          </button>
          <button type="button" onclick="closeFullscreenKaldikPreview()" class="btn-icon-studio-danger mr-3" title="Tutup Pratinjau">
            <i class="fas fa-times"></i> <span>Tutup</span>
          </button>
          <div class="preview-unified-title d-none d-md-flex">
            <i class="fas fa-file-pdf text-info"></i>
            <span>Pratinjau: Kalender Pendidikan &amp; Jadwal Akademik</span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" onclick="printKaldikFrame()" class="btn-icon-studio mr-2" title="Cetak Dokumen">
            <i class="fas fa-print text-success"></i> <span>Cetak</span>
          </button>
          <a id="btnDownloadKaldikPdf" href="#" class="btn-icon-studio" target="_blank" title="Unduh File PDF">
            <i class="fas fa-download text-primary"></i> <span>Unduh PDF</span>
          </a>
        </div>
      </div>
      <div class="preview-unified-body">
        <!-- Loader Spinner -->
        <div id="kaldikStudioLoader" style="position: absolute; top:0; left:0; width:100%; height:100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #323639; color: #fff; z-index: 10;">
          <div class="spinner-border text-info mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
          <div class="font-weight-bold" style="letter-spacing: 0.5px; font-size: 1.1rem;">Menyiapkan Lembar Kalender Pendidikan...</div>
          <small class="text-muted mt-1">Sedang menyusun layout kalender, matriks HBE/ME &amp; agenda kegiatan</small>
        </div>
        <iframe id="kaldikStudioFrame" src="" class="preview-unified-frame" onload="var l = document.getElementById('kaldikStudioLoader'); if(l) l.style.display='none';" title="Pratinjau Cetak Kalender Pendidikan"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
var currentKaldikPdfUrl = '';

function openFullscreenKaldikPreview(url) {
    currentKaldikPdfUrl = url || '<?= BASE_URL ?>kalender_akademik/export_pdf?id_ta=<?= $filter_ta ?>';
    var frame = document.getElementById('kaldikStudioFrame');
    var btnDownload = document.getElementById('btnDownloadKaldikPdf');
    if (btnDownload) btnDownload.href = currentKaldikPdfUrl;
    
    var loader = document.getElementById('kaldikStudioLoader');
    if (loader) loader.style.display = 'flex';
    
    frame.src = currentKaldikPdfUrl + (currentKaldikPdfUrl.indexOf('?') !== -1 ? '&' : '?') + '_t=' + new Date().getTime();
    document.getElementById('sectionMainKaldik').style.display = 'none';
    document.getElementById('sectionPreviewKaldik').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function closeFullscreenKaldikPreview() {
    document.getElementById('sectionPreviewKaldik').style.display = 'none';
    document.getElementById('sectionMainKaldik').style.display = 'block';
}

function printKaldikFrame() {
    var iframe = document.getElementById('kaldikStudioFrame');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    } else {
        window.open(currentKaldikPdfUrl, '_blank');
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>

<?php if ($is_manager): ?>
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js'></script>
<?php endif; ?>

<style>
    .fc-day-sun {
        background-color: #fff0f0 !important;
    }
    .fc-day-sun .fc-col-header-cell-cushion {
        color: #dc3545 !important;
    }
    .calendar-row:hover { background-color: #f1f5f9 !important; }
</style>

<script>
  var calendar;
  const allEvents = <?= json_encode(array_values($events)) ?>;
  const is_manager = <?= $is_manager ? 'true' : 'false' ?>;

  $(document).ready(function () {
    const idTa = <?= json_encode($filter_ta) ?>;
    const categoryColors = <?= json_encode($kategori_colors) ?>;
    const canCreate = <?= json_encode($can_create) ?>;
    const canUpdate = <?= json_encode($can_update) ?>;
    const canDelete = <?= json_encode($can_delete) ?>;

    // Initialize FullCalendar (only for managers)
    if (is_manager) {
        var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
      locale: 'id',
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listMonth'
      },
      buttonText: {
        today: 'Hari Ini',
        month: 'Bulan',
        week: 'Minggu',
        list: 'Daftar'
      },
      height: 'auto',
      events: function (info, successCallback, failureCallback) {
        const filterKategori = $('#filterKategori').val();

        $.ajax({
          url: '<?= BASE_URL ?>kalender_akademik/api',
          data: {
            start: info.startStr,
            end: info.endStr,
            id_ta: idTa,
            kategori: filterKategori
          },
          success: function (data) {
            successCallback(data);
          },
          error: function () {
            failureCallback();
          }
        });
      },
      eventClick: function (info) {
        const event = info.event;
        $('#detailTitle').text(event.title);
        $('#detailKategori').html('<span class="badge" style="background-color: ' + event.backgroundColor + '">' + event.extendedProps.kategori + '</span>');
        $('#detailTanggal').text(formatDateRange(event.start, event.end));
        $('#detailDeskripsi').text(event.extendedProps.deskripsi || '-');

        // Set edit and delete buttons
        $('#btnEdit').off('click').on('click', function () {
          $('#modalDetailKegiatan').modal('hide');
          editEvent(event.id);
        });

        $('#btnDelete').off('click').on('click', function () {
          if (confirm('Yakin ingin menghapus kegiatan ini?')) {
            window.location.href = '<?= BASE_URL ?>kalender_akademik/delete?id=' + event.id + '&id_ta=' + idTa;
          }
        });

        $('#modalDetailKegiatan').modal('show');
      },
      dateClick: function (info) {
        if (!canCreate) return;

        // Block interaction on Sundays
        const date = new Date(info.dateStr);
        if (date.getDay() === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Akses Dibatasi',
                text: 'Input kegiatan pada hari Minggu tidak diperkenankan.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // [BARU] Validasi rentang Tahun Ajaran
        if (currentTa && currentTa.tanggal_mulai && currentTa.tanggal_selesai) {
            if (info.dateStr < currentTa.tanggal_mulai || info.dateStr > currentTa.tanggal_selesai) {
                Swal.fire({
                    icon: 'error',
                    title: 'Bukan Tahun Ajaran Ini',
                    text: 'Tanggal yang Anda pilih (' + info.dateStr + ') berada di luar rentang Tahun Ajaran ' + currentTa.nama_ta + '.',
                    footer: '<small>Ubah filter di atas jika ingin menginput ke semester lain.</small>',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
        }

        // Quick add - open modal with pre-filled date
        $('#id_kalender').val('');
        $('#judul_kegiatan').val('');
        $('#deskripsi').val('');
        $('#tanggal_mulai').val(info.dateStr);
        $('#tanggal_selesai').val(info.dateStr);
        $('#kategori').val('Kegiatan Sekolah');
        updateColorByCategory();
        $('#modalTitle').text('Tambah Kegiatan Baru');
        $('#modalTambahKegiatan').modal('show');
      }
    });

    calendar.render();
    } // end is_manager check

    // Auto-update color when category changes
    $('#kategori').on('change', updateColorByCategory);

    function updateColorByCategory() {
      const kategori = $('#kategori').val();
      const color = categoryColors[kategori] || '#3788d8';
      $('#warna').val(color);
    }

    const taList = <?= json_encode($ta_list) ?>;
    const currentTa = taList.find(t => t.id_ta == idTa);

    // Filter by category
    $('#filterKategori').on('change', function () {
      if (is_manager) {
        calendar.refetchEvents();
      } else {
        $('#filterForm').submit();
      }
    });

    // Handle Form Submit with Validation
    $('#formKegiatan').on('submit', function(e) {
      const valMulai = $('#tanggal_mulai').val();
      const valSelesai = $('#tanggal_selesai').val();

      if (currentTa && currentTa.tanggal_mulai && currentTa.tanggal_selesai) {
        if (valMulai < currentTa.tanggal_mulai || valSelesai > currentTa.tanggal_selesai) {
          e.preventDefault();
          Swal.fire({
            icon: 'error',
            title: 'Tanggal Tidak Sesuai',
            html: `Kegiatan ini berada di luar rentang Tahun Ajaran <b>${currentTa.nama_ta}</b><br><small>(${currentTa.tanggal_mulai} s/d ${currentTa.tanggal_selesai})</small><br><br>Harap sesuaikan tanggal atau ubah filter Tahun Ajaran di bagian atas.`,
            confirmButtonColor: '#d33'
          });
          return false;
        }
      }
    });

    // Reset form when modal closes
    $('#modalTambahKegiatan').on('hidden.bs.modal', function () {
      $('#formKegiatan')[0].reset();
      $('#id_kalender').val('');
      $('#modalTitle').text('Tambah Kegiatan Baru');
      updateColorByCategory();
    });

    // Format date range for display
    function formatDateRange(start, end) {
      const startDate = new Date(start);
      const endDate = new Date(end);
      if (is_manager) {
          endDate.setDate(endDate.getDate() - 1); // Adjust for FullCalendar exclusive end
      }

      const options = { day: 'numeric', month: 'long', year: 'numeric' };
      const startStr = startDate.toLocaleDateString('id-ID', options);

      if (startDate.toDateString() === endDate.toDateString()) {
        return startStr;
      } else {
        const endStr = endDate.toLocaleDateString('id-ID', options);
        return startStr + ' - ' + endStr;
      }
    }

    // Edit event function (Manager uses AJAX, Guru uses local JSON)
    function editEvent(eventId) {
      if (is_manager) {
          $.ajax({
            url: '<?= BASE_URL ?>kalender_akademik/api',
            data: {
              start: '2000-01-01',
              end: '2099-12-31',
              id_ta: idTa
            },
            success: function (events) {
              const event = events.find(e => e.id == eventId);
              if (event) {
                $('#id_kalender').val(event.id);
                $('#judul_kegiatan').val(event.title);
                $('#deskripsi').val(event.extendedProps.deskripsi || '');
                $('#tanggal_mulai').val(event.start);

                // Adjust end date (FullCalendar end is exclusive)
                const endDate = new Date(event.end);
                endDate.setDate(endDate.getDate() - 1);
                $('#tanggal_selesai').val(endDate.toISOString().split('T')[0]);

                $('#kategori').val(event.extendedProps.kategori);
                $('#warna').val(event.backgroundColor);
                $('#modalTitle').text('Edit Kegiatan');
                $('#modalTambahKegiatan').modal('show');
              }
            }
          });
      } else {
          // Guru edit logic from local array
          const event = allEvents.find(e => e.id_kalender == eventId);
          if (event) {
            $('#id_kalender').val(event.id_kalender);
            $('#judul_kegiatan').val(event.judul_kegiatan);
            $('#deskripsi').val(event.deskripsi || '');
            $('#tanggal_mulai').val(event.tanggal_mulai);
            $('#tanggal_selesai').val(event.tanggal_selesai);
            $('#kategori').val(event.kategori);
            $('#warna').val(event.warna || '#3788d8');
            $('#modalTitle').text('Edit Kegiatan');
            $('#modalTambahKegiatan').modal('show');
          }
      }
    }

    // Modal view for List Layout (Guru)
    window.showEventDetail = function(eventId) {
        const event = allEvents.find(e => e.id_kalender == eventId);
        if(event) {
            $('#detailTitle').text(event.judul_kegiatan);
            $('#detailKategori').html('<span class="badge" style="background-color: ' + (event.warna || '#e2e8f0') + '">' + event.kategori + '</span>');
            $('#detailTanggal').text(formatDateRange(event.tanggal_mulai, event.tanggal_selesai));
            $('#detailDeskripsi').text(event.deskripsi || '-');

            // Set edit and delete buttons
            $('#btnEdit').off('click').on('click', function () {
              $('#modalDetailKegiatan').modal('hide');
              editEvent(eventId);
            });

            $('#btnDelete').off('click').on('click', function () {
              if (confirm('Yakin ingin menghapus kegiatan ini?')) {
                window.location.href = '<?= BASE_URL ?>kalender_akademik/delete?id=' + event.id_kalender + '&id_ta=' + idTa;
              }
            });

            $('#modalDetailKegiatan').modal('show');
        }
    };

    // Hook Tambah Button for Guru (because dateClick is gone)
    if (!is_manager) {
        $('#modalTambahKegiatan').on('show.bs.modal', function (e) {
            if(!$('#id_kalender').val() && currentTa) {
                // If it's a new entry, default to current date or TA start date
                let today = new Date().toISOString().split('T')[0];
                if(today < currentTa.tanggal_mulai || today > currentTa.tanggal_selesai) {
                    today = currentTa.tanggal_mulai;
                }
                $('#tanggal_mulai').val(today);
                $('#tanggal_selesai').val(today);
            }
        });
    }
  });
</script>