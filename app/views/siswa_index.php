<?php include __DIR__ . '/partials/header.php'; ?>

<style>
  /* MASTER SISWA MODERN UI (DESKTOP & MOBILE) */
  .siswa-header-icon {
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

  /* TOP ACTIONS BAR */
  .siswa-actions-grid {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
  }

  .btn-siswa-action {
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
  .btn-siswa-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  }

  /* FILTER CARD & TOOLBAR */
  .siswa-filter-card {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 6px 24px rgba(0,0,0,0.06) !important;
    overflow: hidden;
  }
  
  .siswa-filter-header {
    background: #ffffff;
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .siswa-filter-row {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    width: 100%;
  }

  .filter-field-item {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 180px;
  }

  .filter-field-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #475569;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
  }

  .siswa-select-clean {
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #f8fafc !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    height: 38px !important;
    padding: 0.35rem 0.75rem !important;
    transition: all 0.2s ease !important;
    width: 100% !important;
  }
  .siswa-select-clean:focus {
    background-color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
  }

  .search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }
  .search-input-wrapper .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
    z-index: 2;
  }
  .siswa-search-clean {
    padding-left: 40px !important;
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #f8fafc !important;
    font-size: 0.82rem !important;
    color: #1e293b !important;
    height: 38px !important;
    width: 100% !important;
    transition: all 0.2s ease !important;
  }
  .siswa-search-clean:focus {
    background-color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
  }

  /* ACTION BUTTONS ROW IN TABLE */
  .siswa-action-btn-row {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s ease;
    margin: 0 2px;
    text-decoration: none !important;
  }
  .siswa-action-btn-row:hover {
    transform: translateY(-1px);
  }

  /* RESPONSIVE MOBILE VIEW (MAX-WIDTH 768px) */
  @media (max-width: 768px) {
    .siswa-header-icon {
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

    /* Top Action Buttons: 2x2 Grid Seimbang di HP */
    .siswa-actions-grid {
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      gap: 6px !important;
      width: 100% !important;
      margin-top: 8px !important;
    }
    .btn-siswa-action {
      font-size: 0.74rem !important;
      padding: 0.45rem 0.5rem !important;
      border-radius: 6px !important;
      width: 100% !important;
      text-align: center !important;
    }

    /* Filter Toolbar: Rapi & Terstruktur Vertikal */
    .siswa-filter-header {
      padding: 0.85rem !important;
    }
    .siswa-filter-row {
      flex-direction: column !important;
      gap: 8px !important;
      align-items: stretch !important;
    }
    .filter-field-item {
      width: 100% !important;
      min-width: 100% !important;
    }
    .filter-field-label {
      font-size: 0.68rem !important;
      margin-bottom: 2px !important;
    }
    .siswa-select-clean,
    .siswa-search-clean {
      height: 36px !important;
      font-size: 0.78rem !important;
      border-radius: 6px !important;
    }
    .search-input-wrapper .search-icon {
      left: 12px !important;
      font-size: 0.80rem !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
    }
    .siswa-search-clean {
      padding-left: 38px !important;
    }

    /* Table styling di HP */
    #siswa-table thead th {
      font-size: 0.68rem !important;
      padding: 6px 4px !important;
    }
    #siswa-table tbody td {
      font-size: 0.74rem !important;
      padding: 6px 4px !important;
    }
    .siswa-action-btn-row {
      width: 26px !important;
      height: 26px !important;
      font-size: 0.72rem !important;
      border-radius: 6px !important;
    }
  }
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-md-5 col-12 d-flex align-items-center">
        <div class="mr-3 siswa-header-icon">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Data Master Siswa
          </h4>
          <small class="text-muted d-none d-sm-block">Kelola data induk, rombel, dan berkas siswa</small>
        </div>
      </div>
      <div class="col-md-7 col-12 text-md-right mt-2 mt-md-0">
        <!-- TOP ACTIONS (2x2 di Mobile, 1 baris di Desktop) -->
        <div class="siswa-actions-grid">
          <a href="<?= BASE_URL ?>siswa/validasi_pengajuan" class="btn btn-info btn-siswa-action text-white">
            <i class="fas fa-clipboard-check"></i> Validasi 
            <?php if(isset($jml_pengajuan) && $jml_pengajuan > 0): ?>
              <span class="badge badge-danger ml-1 font-weight-bold"><?= $jml_pengajuan ?></span>
            <?php endif; ?>
          </a>
          <a href="<?= BASE_URL ?>siswa/form" class="btn btn-warning btn-siswa-action text-white">
            <i class="fas fa-user-plus"></i> Tambah Siswa
          </a>
          <button type="button" class="btn btn-light btn-siswa-action border text-dark" data-toggle="modal" data-target="#modalImportSiswa">
            <i class="fas fa-file-import text-primary"></i> Impor Excel
          </button>
          <a href="<?= BASE_URL ?>siswa/export" class="btn btn-success btn-siswa-action text-white">
            <i class="fas fa-file-excel"></i> Export Excel
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card siswa-filter-card">
      <div class="siswa-filter-header">
        <form action="index.php" method="GET" class="mb-0">
            <input type="hidden" name="mod" value="siswa">
            <input type="hidden" name="act" value="index">
            
            <!-- HEADER INFO STATUS & TOTAL -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <span class="badge badge-primary px-2.5 py-1 mr-2 font-weight-bold" style="font-size: 0.74rem; border-radius: 6px;">
                        <i class="fas fa-users mr-1"></i> <?= count($siswa_list ?? []) ?> Siswa Terdata
                    </span>
                    <span class="text-muted font-weight-bold small text-uppercase d-none d-sm-inline" style="letter-spacing: 0.5px;">
                        Status: <strong class="text-dark"><?= strtoupper($status) ?></strong>
                    </span>
                </div>
            </div>

            <!-- CONTROLS ROW -->
            <div class="siswa-filter-row">
                <!-- 1. Filter Tahun Ajaran -->
                <div class="filter-field-item">
                    <label class="filter-field-label">
                        <i class="fas fa-calendar-alt text-primary mr-1"></i> Tahun Ajaran
                    </label>
                    <select name="id_ta" class="form-control siswa-select-clean" onchange="this.form.submit()">
                        <option value="">Semua Tahun (Master)</option>
                        <?php foreach($ta_list as $ta): ?>
                          <option value="<?= $ta['id_ta'] ?>" <?= $id_ta_view == $ta['id_ta'] ? 'selected' : '' ?>>
                            TA <?= htmlspecialchars($ta['nama_ta']) ?>
                          </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Filter Status -->
                <div class="filter-field-item">
                    <label class="filter-field-label">
                        <i class="fas fa-filter text-info mr-1"></i> Status Siswa
                    </label>
                    <select name="status" class="form-control siswa-select-clean" onchange="this.form.submit()">
                        <option value="Semua" <?= $status == 'Semua' ? 'selected' : '' ?>>Semua Status</option>
                        <option value="Aktif" <?= $status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Pindahan" <?= $status == 'Pindahan' ? 'selected' : '' ?>>Pindahan / Mutasi</option>
                        <option value="Lulus" <?= $status == 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                        <option value="Keluar" <?= $status == 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                    </select>
                </div>

                <!-- 3. Live Search Field -->
                <div class="filter-field-item">
                    <label class="filter-field-label">
                        <i class="fas fa-search text-warning mr-1"></i> Pencarian
                    </label>
                    <div class="search-input-wrapper" style="position: relative; width: 100%;">
                        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; z-index: 5;"></i>
                        <input type="text" id="search-siswa" class="form-control siswa-search-clean" style="padding-left: 46px !important;" placeholder="Cari nama / NISN / Rombel..." />
                    </div>
                </div>
            </div>
        </form>
      </div>
      
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="siswa-table" class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
            <thead style="background: #f8fafc;">
              <tr class="text-muted">
                <th class="text-center py-2.5 border-bottom" style="width: 45px; font-size: 0.72rem; letter-spacing: 0.5px;">NO</th>
                <th class="py-2.5 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px;">NAMA LENGKAP</th>
                <th class="text-center py-2.5 border-bottom d-none d-sm-table-cell" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 110px;">NISN</th>
                <th class="text-center py-2.5 border-bottom d-none d-sm-table-cell" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 110px;">NIPD</th>
                <th class="text-center py-2.5 border-bottom d-none d-sm-table-cell" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 60px;">JK</th>
                <th class="text-center py-2.5 border-bottom d-none d-sm-table-cell" style="font-size: 0.72rem; letter-spacing: 0.5px;">TTL</th>
                <th class="text-center py-2.5 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 85px;">STATUS</th>
                <th class="text-center py-2.5 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 110px;">AKSI</th>
              </tr>
            </thead>
            <tbody id="siswa-table-body">
              <?php if (empty($siswa_list)): ?>
              <tr>
                <td colspan="8" class="text-center py-5 text-muted small"><em>Belum ada data siswa yang ditemukan.</em></td>
              </tr>
            <?php else: ?>
                <?php $no = 1; ?>
                <?php foreach ($siswa_list as $s): ?>
                  <tr>
                    <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++; ?></td>
                    <td class="align-middle py-2">
                        <span class="font-weight-bold text-dark d-block" style="font-size: 0.84rem;"><?= htmlspecialchars($s['nama']) ?></span>
                        <?php if (!empty($s['nama_kelas'])): ?>
                            <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 0.64rem; border-radius: 4px;"><i class="fas fa-school mr-1"></i><?= htmlspecialchars($s['nama_kelas']) ?></span>
                        <?php else: ?>
                            <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 0.64rem; border-radius: 4px;"><i class="fas fa-exclamation-circle mr-1"></i>Belum Ada Rombel</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center align-middle d-none d-sm-table-cell"><code class="text-muted small"><?= htmlspecialchars($s['nisn'] ?: '-') ?></code></td>
                    <td class="text-center align-middle d-none d-sm-table-cell"><code class="text-muted small"><?= htmlspecialchars($s['nipd'] ?: '-') ?></code></td>
                    <td class="text-center align-middle d-none d-sm-table-cell"><span class="small"><?= (strtoupper($s['jk']) == 'L' || strtoupper($s['jk']) == 'LAKI-LAKI') ? 'L' : 'P' ?></span></td>
                    <td class="text-center align-middle small d-none d-sm-table-cell">
                        <?= htmlspecialchars($s['tempat_lahir'] ?? '') ?><?= !empty($s['tanggal_lahir']) ? ', ' . htmlspecialchars($s['tanggal_lahir']) : '' ?>
                    </td>
                    <td class="text-center align-middle">
                      <?php 
                      $disp_status = $s['status_aktif_relatif'] ?? $s['status_aktif'];
                      if (trim($disp_status) == 'Aktif'): ?>
                        <span class="badge badge-success px-2 py-0.5" style="font-size: 0.62rem; border-radius: 6px; font-weight: 700;">AKTIF</span>
                      <?php else: ?>
                        <span class="badge badge-danger px-2 py-0.5" style="font-size: 0.62rem; border-radius: 6px; font-weight: 700;"><?= strtoupper(htmlspecialchars($disp_status)) ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center align-middle">
                        <div class="d-inline-flex align-items-center">
                            <a href="<?= BASE_URL ?>profil_siswa/detail?id=<?= $s['id_siswa'] ?>" 
                               class="siswa-action-btn-row" 
                               style="background: #e0f2fe; color: #0369a1;" title="Profil & Dokumen">
                                <i class="fas fa-user-circle"></i>
                            </a>
                            <a href="<?= BASE_URL ?>siswa/form?id=<?= $s['id_siswa'] ?>" 
                               class="siswa-action-btn-row" 
                               style="background: #fffbeb; color: #d97706;" title="Edit Data">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a href="<?= BASE_URL ?>siswa/delete?id=<?= $s['id_siswa'] ?>" 
                               class="siswa-action-btn-row" 
                               style="background: #fef2f2; color: #dc2626;" 
                               title="Hapus" onclick="return confirmDelete(event, 'Hapus data siswa ini?')">
                                <i class="fas fa-trash-alt"></i>
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
  </div>
</section>

<!-- MODAL IMPORT SISWA (Standardized) -->
<div class="modal fade" id="modalImportSiswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?= BASE_URL ?>siswa/import" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" style="font-size: 1rem;"><i class="fas fa-file-excel mr-2"></i> Import Data Siswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px;">
                    <h6 class="font-weight-bold mb-1" style="font-size: 0.85rem;"><i class="fas fa-info-circle mr-1"></i> Petunjuk Import:</h6>
                    <ol class="small mb-0 pl-3" style="font-size: 0.78rem;">
                        <li>Gunakan file format <strong>.xls</strong> atau <strong>.xlsx</strong>.</li>
                        <li>Pastikan urutan kolom sesuai template yang tersedia.</li>
                        <li>Kolom wajib diisi: <strong>Nama</strong>, <strong>NISN</strong>.</li>
                    </ol>
                </div>
                
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark small"><i class="fas fa-download mr-1 text-success"></i> 1. Download Template</label>
                    <a href="<?= BASE_URL ?>siswa/export?template=1" class="btn btn-outline-success btn-block font-weight-bold" style="border-radius: 8px; font-size: 0.82rem;">
                        <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
                    </a>
                </div>
                
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-dark small"><i class="fas fa-upload mr-1 text-primary"></i> 2. Pilih File Excel</label>
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="custom-file-input" id="fileExcelSiswa" accept=".xls,.xlsx" required>
                        <label class="custom-file-label text-truncate" for="fileExcelSiswa" style="border-radius: 8px; font-size: 0.80rem;">Pilih file...</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 6px;" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold" style="border-radius: 6px;">
                    <i class="fas fa-upload mr-1"></i> Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
  (function() {
    function initSiswaSearch() {
      $(function () {
        // Custom File Input Label
        $('.custom-file-input').on('change', function() {
          let fileName = $(this).val().split('\\').pop();
          $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Client-side live search
        $('#search-siswa').on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#siswa-table tbody tr").filter(function() {
            var rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(value) > -1);
          });
        });
      });
    }

    if (typeof jQuery === 'undefined') {
      var script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js';
      script.onload = initSiswaSearch;
      document.head.appendChild(script);
    } else {
      initSiswaSearch();
    }
  })();
</script>