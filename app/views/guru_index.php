<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-id-badge"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Data Master GTK (Pendidik)
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
          <a href="<?= BASE_URL ?>guru/form" class="btn btn-warning btn-sm px-3 font-weight-bold text-white">
            <i class="fas fa-user-plus mr-1"></i> Tambah Guru
          </a>
          <button type="button" class="btn btn-light btn-sm px-3 border-left" data-toggle="modal"
            data-target="#modalImportGuru">
            <i class="fas fa-file-import mr-1 text-primary"></i> Impor Excel
          </button>
          <a href="<?= BASE_URL ?>guru/export" class="btn btn-success btn-sm px-3 border-left border-white">
            <i class="fas fa-file-excel mr-1"></i> Export
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <!-- 3 SEGMENTED FILTER TABS -->
    <div class="mb-3 px-2">
      <ul class="nav nav-pills custom-pills" style="gap: 6px;">
        <li class="nav-item">
          <a class="nav-link active font-weight-bold px-3 py-2 rounded-pill shadow-xs" href="javascript:void(0)" onclick="filterGuruTab('all', this)">
            <i class="fas fa-users mr-1"></i> Semua Data (<?= count($guru_list) ?>)
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold px-3 py-2 rounded-pill shadow-xs text-dark bg-white border" href="javascript:void(0)" onclick="filterGuruTab('reguler', this)">
            <i class="fas fa-chalkboard-teacher text-primary mr-1"></i> 👨‍🏫 Guru Reguler
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold px-3 py-2 rounded-pill shadow-xs text-dark bg-white border" href="javascript:void(0)" onclick="filterGuruTab('koordinator_pjj', this)">
            <i class="fas fa-globe text-success mr-1"></i> 🌐 Koordinator PJJ / Terbuka
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold px-3 py-2 rounded-pill shadow-xs text-dark bg-white border" href="javascript:void(0)" onclick="filterGuruTab('koordinator_menginduk', this)">
            <i class="fas fa-handshake text-warning mr-1"></i> 🤝 Koordinator Menginduk
          </a>
        </li>
      </ul>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header bg-white py-3 border-bottom">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-list-ul mr-2 text-primary"></i> DAFTAR GURU & KOORDINATOR</h6>
          </div>
          <div class="col-md-6 text-right">
            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.7rem; border-radius: 6px;">
              <i class="fas fa-info-circle mr-1"></i> Gunakan format .xlsx untuk impor data
            </span>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-sm align-middle mb-0" style="border-collapse: collapse;">
            <thead style="background: #f8fafc;">
              <tr class="text-muted">
                <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA LENGKAP & PERAN</th>
                <th class="text-center py-2 border-bottom d-none d-sm-table-cell" style="font-size: 0.7rem; letter-spacing: 1px; width: 80px;">KODE</th>
                <th class="text-center py-2 border-bottom d-none d-sm-table-cell" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">NUPTK</th>
                <th class="text-center py-2 border-bottom d-none d-sm-table-cell" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">NIK</th>
                <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
                <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($guru_list)): ?>
              <tr>
                <td colspan="7" class="text-center py-5 text-muted small"><em>Belum ada data guru yang terdaftar.</em>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($guru_list as $g): ?>
                <?php $jg = $g['jenis_guru'] ?? 'reguler'; ?>
                <tr class="guru-row" data-jenis="<?= $jg ?>">
                  <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++; ?></td>
                  <td class="align-middle py-2">
                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 6px;">
                      <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($g['nama']) ?></span>
                      <?php if ($jg === 'koordinator_pjj'): ?>
                        <span class="badge badge-success px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.65rem;">
                          🌐 PJJ <?= !empty($g['lokasi_tugas']) ? '('.htmlspecialchars($g['lokasi_tugas']).')' : '' ?>
                        </span>
                      <?php elseif ($jg === 'koordinator_menginduk'): ?>
                        <span class="badge badge-warning text-dark px-2 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.65rem;">
                          🤝 Menginduk <?= !empty($g['lokasi_tugas']) ? '('.htmlspecialchars($g['lokasi_tugas']).')' : '' ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td class="text-center align-middle d-none d-sm-table-cell">
                    <span class="badge badge-light border px-2 py-1 text-dark"
                      style="font-size: 0.75rem; border-radius: 6px;">
                      <?= htmlspecialchars($g['kode_guru']) ?>
                    </span>
                  </td>
                  <td class="text-center align-middle d-none d-sm-table-cell">
                    <code class="text-muted small"><?= htmlspecialchars($g['nuptk'] ?: '-') ?></code>
                  </td>
                  <td class="text-center align-middle d-none d-sm-table-cell">
                    <code class="text-muted small"><?= htmlspecialchars($g['nik'] ?: '-') ?></code>
                  </td>
                  <td class="text-center align-middle">
                    <?php if (trim($g['status']) == 'Aktif'): ?>
                      <span class="badge badge-success px-2 py-1 shadow-none"
                        style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">AKTIF</span>
                    <?php else: ?>
                      <span class="badge badge-danger px-2 py-1 shadow-none"
                        style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;"><?= strtoupper(htmlspecialchars($g['status'])) ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center align-middle">
                    <div class="btn-group">
                      <a href="<?= BASE_URL ?>profil_guru/detail?id=<?= $g['id_guru'] ?>"
                        class="btn btn-xs btn-outline-info border-0 p-1 mr-1"
                        style="background: #e0f2fe; width: 28px; height: 28px; border-radius: 8px; color: #0369a1;"
                        title="Profil & Dokumen">
                        <i class="fas fa-address-card" style="font-size: 0.85rem;"></i>
                      </a>
                      <a href="<?= BASE_URL ?>guru/form?id=<?= $g['id_guru'] ?>"
                        class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                        style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;"
                        title="Edit Data">
                        <i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i>
                      </a>
                      <a href="<?= BASE_URL ?>guru/delete?id=<?= $g['id_guru'] ?>"
                        class="btn btn-xs btn-outline-danger border-0 p-1"
                        style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;"
                        title="Hapus" onclick="return confirmDelete(event)">
                        <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
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

<!-- MODAL IMPORT GURU (Standardized) -->
<div class="modal fade" id="modalImportGuru" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="<?= BASE_URL ?>guru/import" method="POST" enctype="multipart/form-data"
      class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i> Import Data Guru</h5>
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
            <li>Data wajib: <strong>Nama</strong>, <strong>Kode Guru</strong>.</li>
          </ol>
        </div>

        <div class="form-group mb-4">
          <label class="font-weight-bold"><i class="fas fa-download mr-1"></i> 1. Download Template</label>
          <a href="<?= BASE_URL ?>guru/export?template=1" class="btn btn-outline-success btn-block">
            <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
          </a>
        </div>

        <div class="form-group mb-0">
          <label class="font-weight-bold"><i class="fas fa-upload mr-1"></i> 2. Pilih File Excel</label>
          <div class="custom-file">
            <input type="file" name="file_excel" class="custom-file-input" id="fileExcelGuru" accept=".xls,.xlsx"
              required>
            <label class="custom-file-label" for="fileExcelGuru">Pilih file...</label>
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

<style>
  /* Adjust custom file for modal */
  #modalImportGuru .custom-file-label::after {
    display: none;
  }
</style>

<?php include __DIR__ . '/partials/footer.php'; ?>


<script>
  function filterGuruTab(jenis, el) {
    $('.custom-pills .nav-link').removeClass('active').addClass('text-dark bg-white border');
    $(el).addClass('active').removeClass('text-dark bg-white border');

    if (jenis === 'all') {
      $('.guru-row').show();
    } else {
      $('.guru-row').each(function() {
        if ($(this).data('jenis') === jenis) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    }
  }

  $(function () {
    // Custom File Input Label
    $('.custom-file-input').on('change', function () {
      let fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
  });
</script>