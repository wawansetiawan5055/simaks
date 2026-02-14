<?php include __DIR__ . '/partials/header.php'; ?>
<style>
  .drag-handle {
    cursor: move;
    color: #cbd5e1;
    transition: color 0.2s;
  }

  .drag-handle:hover {
    color: #64748b;
  }

  .ui-sortable-placeholder {
    height: 45px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    visibility: visible !important;
    border-radius: 8px;
  }

  .ui-sortable-helper {
    background: #fff !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    display: table !important;
  }
</style>

<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
      <div>
        <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-book text-primary mr-2"></i> Data Master Mata
          Pelajaran</h2>
        <p class="text-muted small mb-0">Kelola kurikulum, kategori mata pelajaran, dan urutan tampil di raport.</p>
      </div>
      <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <a href="index.php?mod=mapel&act=form" class="btn btn-warning btn-sm px-3 font-weight-bold text-white">
          <i class="fas fa-plus mr-1"></i> Tambah Mapel
        </a>
        <button type="button" class="btn btn-light btn-sm px-3 border-left" data-toggle="modal"
          data-target="#modalImportMapel">
          <i class="fas fa-file-import mr-1 text-primary"></i> Impor Excel
        </button>
        <a href="index.php?mod=mapel&act=export" class="btn btn-success btn-sm px-3 border-left border-white">
          <i class="fas fa-file-excel mr-1"></i> Export
        </a>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header bg-white py-3 border-bottom">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h6 class="mb-0 font-weight-bold text-muted small"><i class="fas fa-arrows-alt-v mr-2 text-primary"></i>
              DRAG & DROP UNTUK MENGURUTKAN</h6>
          </div>
          <div class="col-md-6 text-right">
            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.75rem; border-radius: 6px;">
              <i class="fas fa-info-circle mr-1"></i> Gunakan format .xlsx untuk impor data
            </span>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead style="background: #f8fafc;">
            <tr class="text-muted">
              <th style="width: 40px;" class="border-bottom"></th>
              <th class="text-center py-2 border-bottom" style="width: 70px; font-size: 0.7rem; letter-spacing: 1px;">
                URUTAN</th>
              <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA MATA PELAJARAN</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">
                KODE</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">
                KATEGORI</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 80px;">
                KKTP</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">
                AKSI</th>
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
                  <td class="text-center align-middle"><i class="fas fa-grip-vertical drag-handle"></i></td>
                  <td class="text-center align-middle">
                    <span class="badge badge-light border font-weight-bold"
                      style="font-size: 0.75rem; border-radius: 6px;"><?= $m['urutan'] ?></span>
                  </td>
                  <td class="align-middle py-1">
                    <span class="font-weight-bold text-dark"
                      style="font-size: 0.85rem;"><?= htmlspecialchars($m['nama_mapel']) ?></span>
                  </td>
                  <td class="text-center align-middle"><code
                      class="text-primary small"><?= htmlspecialchars($m['kode_mapel']) ?></code></td>
                  <td class="text-center align-middle"><span class="badge bg-light text-muted border px-2 py-1"
                      style="font-size: 0.65rem; border-radius: 4px;"><?= htmlspecialchars($m['kategori_mapel']) ?></span>
                  </td>
                  <td class="text-center align-middle font-weight-bold" style="font-size: 0.8rem;">
                    <?= htmlspecialchars($m['kktp']) ?></td>
                  <td class="text-center align-middle">
                    <div class="btn-group">
                      <a href="index.php?mod=mapel&act=form&id=<?= $m['id_mapel'] ?>"
                        class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                        style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;"
                        title="Edit"><i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i></a>
                      <a href="index.php?mod=mapel&act=delete&id=<?= $m['id_mapel'] ?>"
                        class="btn btn-xs btn-outline-danger border-0 p-1"
                        style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;"
                        title="Hapus" onclick="return confirmDelete(event, 'Hapus mapel ini?')"><i class="fas fa-trash-alt"
                          style="font-size: 0.8rem;"></i></a>
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



<!-- MODAL IMPORT MAPEL (Standardized) -->
<div class="modal fade" id="modalImportMapel" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="index.php?mod=mapel&act=import" method="POST" enctype="multipart/form-data"
      class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i> Import Data Mata Pelajaran</h5>
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
            <li>Data wajib: <strong>Nama Mapel</strong>, <strong>Kode Mapel</strong>.</li>
          </ol>
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold"><i class="fas fa-download mr-1"></i> 1. Download Template</label>
          <a href="index.php?mod=mapel&act=export&template=1" class="btn btn-outline-success btn-block">
            <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
          </a>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold"><i class="fas fa-upload mr-1"></i> 2. Pilih File Excel</label>
          <div class="custom-file">
            <input type="file" name="file_excel" class="custom-file-input" id="fileExcelMapel" accept=".xls,.xlsx"
              required>
            <label class="custom-file-label" for="fileExcelMapel">Pilih file...</label>
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


<?php include __DIR__ . '/partials/footer.php'; ?>


<script>
  $(function () {
    // Custom File Input Label
    $('.custom-file-input').on('change', function () {
      let fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
  });
</script>

<script>
  $(document).ready(function () {
    if (typeof bsCustomFileInput !== 'undefined') {
      bsCustomFileInput.init();
    }

    // Cek apakah jQuery UI sudah dimuat
    if (typeof $.ui === 'undefined') {
      console.error("jQuery UI belum dimuat! Fitur Drag & Drop dinonaktifkan.");
      return;
    }

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
          url: 'index.php?mod=mapel&act=update_urutan',
          type: 'POST',
          data: { urutan: urutanIds },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              if (window.Notify) {
                window.Notify.success('Berhasil!', response.message + ' Halaman akan dimuat ulang.');
              } else {
                alert(response.message);
              }
              setTimeout(function () {
                location.reload();
              }, 1500);

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
  });
</script>