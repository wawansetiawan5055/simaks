<?php include __DIR__ . '/partials/header.php'; ?>

<style>
  /* MASTER MAPEL MODERN STYLING (DESKTOP & MOBILE) */
  .mapel-header-icon {
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

  /* TOP ACTION BUTTONS */
  .mapel-actions-grid {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
  }

  .btn-mapel-action {
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
  .btn-mapel-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  }

  /* FILTER CARD & TOOLBAR */
  .mapel-filter-card {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 6px 24px rgba(0,0,0,0.06) !important;
    overflow: hidden;
  }
  
  .mapel-filter-header {
    background: #ffffff;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 320px;
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
  .mapel-search-clean {
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
  .mapel-search-clean:focus {
    background-color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
  }

  /* DRAG AND DROP STYLING */
  .drag-handle {
    cursor: grab;
    color: #94a3b8;
    transition: color 0.2s, transform 0.2s;
    font-size: 0.95rem;
  }
  .drag-handle:hover {
    color: #0284c7;
    transform: scale(1.15);
  }
  .drag-handle:active {
    cursor: grabbing;
  }

  .ui-sortable-placeholder {
    height: 48px;
    background: #f1f5f9;
    border: 2px dashed #94a3b8;
    visibility: visible !important;
    border-radius: 8px;
  }

  .ui-sortable-helper {
    background: #ffffff !important;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    border-radius: 8px;
    display: table !important;
  }

  /* ACTION BUTTONS ROW IN TABLE */
  .mapel-action-btn-row {
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
  .mapel-action-btn-row:hover {
    transform: translateY(-1px);
  }

  /* RESPONSIVE MOBILE VIEW (MAX-WIDTH 768px) */
  @media (max-width: 768px) {
    .mapel-header-icon {
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

    /* Top Action Buttons: Compact Grid di HP */
    .mapel-actions-grid {
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      gap: 6px !important;
      width: 100% !important;
      margin-top: 8px !important;
    }
    .btn-mapel-action {
      font-size: 0.74rem !important;
      padding: 0.45rem 0.5rem !important;
      border-radius: 6px !important;
      width: 100% !important;
      text-align: center !important;
    }
    .btn-mapel-action:first-child {
      grid-column: 1 / -1 !important;
    }

    /* Toolbar Filter & Search di HP */
    .mapel-filter-header {
      padding: 0.75rem 0.85rem !important;
    }
    .search-input-wrapper {
      max-width: 100% !important;
    }
    .mapel-search-clean {
      height: 36px !important;
      font-size: 0.78rem !important;
      border-radius: 6px !important;
      padding-left: 38px !important;
    }
    .search-input-wrapper .search-icon {
      left: 12px !important;
      font-size: 0.80rem !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
    }

    /* Table styling di HP */
    #mapel-table thead th {
      font-size: 0.68rem !important;
      padding: 6px 4px !important;
    }
    #mapel-table tbody td {
      font-size: 0.74rem !important;
      padding: 6px 4px !important;
    }
    .mapel-action-btn-row {
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
      <div class="col-md-6 col-12 d-flex align-items-center">
        <div class="mr-3 mapel-header-icon">
          <i class="fas fa-book"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Data Master Mata Pelajaran
          </h4>
          <small class="text-muted d-none d-sm-block">Kelola kurikulum, urutan mapel, dan KKTP</small>
        </div>
      </div>
      <div class="col-md-6 col-12 text-md-right mt-2 mt-md-0">
        <!-- TOP ACTIONS BAR -->
        <div class="mapel-actions-grid">
          <a href="<?= BASE_URL ?>mapel/form" class="btn btn-warning btn-mapel-action text-white">
            <i class="fas fa-plus"></i> Tambah Mapel
          </a>
          <button type="button" class="btn btn-light btn-mapel-action border text-dark" data-toggle="modal" data-target="#modalImportMapel">
            <i class="fas fa-file-import text-primary"></i> Impor Excel
          </button>
          <a href="<?= BASE_URL ?>mapel/export" class="btn btn-success btn-mapel-action text-white">
            <i class="fas fa-file-excel"></i> Export Excel
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card mapel-filter-card">
      <div class="mapel-filter-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
          <!-- TOTAL BADGE & DRAG INFO -->
          <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="badge badge-primary px-2.5 py-1 font-weight-bold" style="font-size: 0.74rem; border-radius: 6px;">
              <i class="fas fa-book-open mr-1"></i> <?= count($mapel_list ?? []) ?> Mata Pelajaran
            </span>
            <span class="badge badge-light border text-muted px-2 py-1 font-weight-bold" style="font-size: 0.70rem; border-radius: 6px;">
              <i class="fas fa-arrows-alt-v mr-1 text-primary"></i> Geser/Drag untuk Urutan
            </span>
          </div>

          <!-- LIVE SEARCH -->
          <div class="search-input-wrapper" style="position: relative; width: 100%; max-width: 320px;">
            <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; z-index: 5;"></i>
            <input type="text" id="search-mapel" class="form-control mapel-search-clean" style="padding-left: 46px !important;" placeholder="Cari nama mapel..." />
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="mapel-table" style="border-collapse: collapse;">
            <thead style="background: #f8fafc;">
              <tr class="text-muted">
                <th style="width: 36px;" class="border-bottom text-center"></th>
                <th class="text-center py-2.5 border-bottom" style="width: 65px; font-size: 0.72rem; letter-spacing: 0.5px;">URUT</th>
                <th class="py-2.5 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px;">NAMA MATA PELAJARAN</th>
                <th class="text-center py-2.5 border-bottom d-none d-sm-table-cell" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 90px;">KODE</th>
                <th class="text-center py-2.5 border-bottom d-none d-sm-table-cell" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 140px;">KATEGORI</th>
                <th class="text-center py-2.5 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 75px;">KKTP</th>
                <th class="text-center py-2.5 border-bottom" style="font-size: 0.72rem; letter-spacing: 0.5px; width: 95px;">AKSI</th>
              </tr>
            </thead>
            <tbody id="sortable-mapel">
              <?php if (empty($mapel_list)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted small"><em>Belum ada data mata pelajaran.</em></td>
                </tr>
              <?php else: ?>
                <?php foreach ($mapel_list as $m): ?>
                  <tr data-id="<?= $m['id_mapel'] ?>" class="bg-white">
                    <td class="text-center align-middle"><i class="fas fa-grip-vertical drag-handle" title="Geser untuk mengubah urutan"></i></td>
                    <td class="text-center align-middle">
                      <span class="badge badge-light border font-weight-bold" style="font-size: 0.72rem; border-radius: 6px;"><?= $m['urutan'] ?></span>
                    </td>
                    <td class="align-middle py-2">
                      <span class="font-weight-bold text-dark d-block" style="font-size: 0.84rem;"><?= htmlspecialchars($m['nama_mapel']) ?></span>
                    </td>
                    <td class="text-center align-middle d-none d-sm-table-cell">
                      <span class="badge badge-primary px-2 py-0.5 font-weight-bold" style="font-size: 0.68rem; border-radius: 6px;"><?= htmlspecialchars($m['kode_mapel']) ?></span>
                    </td>
                    <td class="text-center align-middle d-none d-sm-table-cell">
                      <span class="badge bg-light text-muted border px-2 py-0.5 font-weight-bold" style="font-size: 0.65rem; border-radius: 4px;"><?= htmlspecialchars($m['kategori_mapel']) ?></span>
                    </td>
                    <td class="text-center align-middle font-weight-bold text-dark" style="font-size: 0.80rem;">
                      <?= htmlspecialchars($m['kktp'] ?? '-') ?>
                    </td>
                    <td class="text-center align-middle">
                      <div class="d-inline-flex align-items-center">
                        <a href="<?= BASE_URL ?>mapel/form?id=<?= $m['id_mapel'] ?>"
                          class="mapel-action-btn-row"
                          style="background: #fffbeb; color: #d97706;"
                          title="Edit Data"><i class="fas fa-pencil-alt"></i></a>
                        <a href="<?= BASE_URL ?>mapel/delete?id=<?= $m['id_mapel'] ?>"
                          class="mapel-action-btn-row"
                          style="background: #fef2f2; color: #dc2626;"
                          title="Hapus" onclick="return confirmDelete(event, 'Hapus mapel ini?')"><i class="fas fa-trash-alt"></i></a>
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
  </div>
</section>

<!-- MODAL IMPORT MAPEL (Standardized) -->
<div class="modal fade" id="modalImportMapel" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="<?= BASE_URL ?>mapel/import" method="POST" enctype="multipart/form-data"
      class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title font-weight-bold" style="font-size: 1rem;"><i class="fas fa-file-excel mr-2"></i> Import Data Mata Pelajaran</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px;">
          <h6 class="font-weight-bold mb-1" style="font-size: 0.85rem;"><i class="fas fa-info-circle mr-1"></i> Petunjuk Import:</h6>
          <ol class="small mb-0 pl-3" style="font-size: 0.78rem;">
            <li>Gunakan file dengan format <strong>.xls</strong> atau <strong>.xlsx</strong>.</li>
            <li>Pastikan urutan kolom sesuai dengan template yang tersedia.</li>
            <li>Data wajib: <strong>Nama Mapel</strong>, <strong>Kode Mapel</strong>.</li>
          </ol>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark small"><i class="fas fa-download mr-1 text-success"></i> 1. Download Template</label>
          <a href="<?= BASE_URL ?>mapel/export?template=1" class="btn btn-outline-success btn-block font-weight-bold" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
          </a>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark small"><i class="fas fa-upload mr-1 text-primary"></i> 2. Pilih File Excel</label>
          <div class="custom-file">
            <input type="file" name="file_excel" class="custom-file-input" id="fileExcelMapel" accept=".xls,.xlsx" required>
            <label class="custom-file-label text-truncate" for="fileExcelMapel" style="border-radius: 8px; font-size: 0.80rem;">Pilih file...</label>
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
    function initMapelScripts() {
      $(function () {
        // Custom File Input Label
        $('.custom-file-input').on('change', function () {
          let fileName = $(this).val().split('\\').pop();
          $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Client-side live search
        $('#search-mapel').on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#sortable-mapel tr").filter(function() {
            var rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(value) > -1);
          });
        });

        // Sortable drag and drop
        if (typeof $.ui !== 'undefined') {
          $("#sortable-mapel").sortable({
            handle: ".drag-handle",
            placeholder: "ui-sortable-placeholder",
            axis: "y",
            update: function (event, ui) {
              var urutanIds = [];
              $(this).children('tr').each(function (index, element) {
                urutanIds.push($(element).attr('data-id'));
              });

              $.ajax({
                url: '<?= BASE_URL ?>mapel/update_urutan',
                type: 'POST',
                data: { urutan: urutanIds },
                dataType: 'json',
                success: function (response) {
                  if (response.status === 'success') {
                    if (window.Notify) {
                      window.Notify.success('Berhasil!', response.message + ' Halaman akan dimuat ulang.');
                    }
                    setTimeout(function () {
                      location.reload();
                    }, 1200);
                  } else {
                    if (window.Notify) {
                      window.Notify.error('Error!', response.message);
                    } else {
                      alert('Error: ' + response.message);
                    }
                  }
                },
                error: function () {
                  if (window.Notify) {
                    window.Notify.error('Error!', 'Terjadi kesalahan koneksi.');
                  } else {
                    alert('Terjadi kesalahan koneksi.');
                  }
                }
              });
            }
          });
        }
      });
    }

    if (typeof jQuery === 'undefined') {
      var script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js';
      script.onload = initMapelScripts;
      document.head.appendChild(script);
    } else {
      initMapelScripts();
    }
  })();
</script>