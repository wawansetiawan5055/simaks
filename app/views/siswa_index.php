<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
        <div>
            <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-user-graduate text-primary mr-2"></i> Data Master Siswa</h2>
            <p class="text-muted small mb-0">Kelola dan pantau data profil, NISN, NIPD, serta riwayat kesiswaan.</p>
        </div>
        <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
            <a href="index.php?mod=siswa&act=form" class="btn btn-warning btn-sm px-3 font-weight-bold text-white">
                <i class="fas fa-user-plus mr-1"></i> Tambah Siswa
            </a>
            <button type="button" class="btn btn-light btn-sm px-3 border-left" data-toggle="modal" data-target="#modalImportSiswa">
                <i class="fas fa-file-import mr-1 text-primary"></i> Impor Excel
            </button>
            <a href="index.php?mod=siswa&act=export" class="btn btn-success btn-sm px-3 border-left border-white">
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
                <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-users mr-2 text-primary"></i> DAFTAR SISWA AKTIF</h6>
            </div>
            <div class="col-md-6 text-right">
              <form class="form-inline justify-content-end" method="GET" action="index.php">
                <input type="hidden" name="mod" value="siswa">
                <div class="input-group input-group-sm mr-2" style="max-width: 360px;">
                  <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" class="form-control" placeholder="Cari nama / NISN / NIPD" />
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                  </div>
                </div>
                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.7rem; border-radius: 6px;">
                  <i class="fas fa-info-circle mr-1"></i> Gunakan format .xlsx untuk impor data
                </span>
              </form>
            </div>
        </div>
      </div>
      
      <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0" style="border-collapse: collapse;">
          <thead style="background: #f8fafc;">
            <tr class="text-muted">
              <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
              <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA LENGKAP PENGGUNA</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">NISN</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">NIPD</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 60px;">JK</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">TTL</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 90px;">STATUS</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
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
                    <td class="align-middle py-1">
                        <div class="d-flex align-items-center ml-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 border bg-light text-primary font-weight-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                <?= strtoupper(substr($s['nama'], 0, 1)) ?>
                            </div>
                            <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($s['nama']) ?></span>
                        </div>
                    </td>
                    <td class="text-center align-middle"><code class="text-muted small"><?= htmlspecialchars($s['nisn'] ?: '-') ?></code></td>
                    <td class="text-center align-middle"><code class="text-muted small"><?= htmlspecialchars($s['nipd'] ?: '-') ?></code></td>
                    <td class="text-center align-middle"><span class="small"><?= (strtoupper($s['jk']) == 'L' || strtoupper($s['jk']) == 'LAKI-LAKI') ? 'Laki-laki' : 'Perempuan' ?></span></td>
                    <td class="text-center align-middle small">
                        <?= htmlspecialchars($s['tempat_lahir']) ?>, <?= htmlspecialchars($s['tanggal_lahir']) ?>
                    </td>
                    <td class="text-center align-middle">
                      <?php if (trim($s['status_aktif']) == 'Aktif'): ?>
                        <span class="badge badge-success px-2 py-1" style="font-size: 0.6rem; border-radius: 100px; font-weight: 600;">AKTIF</span>
                      <?php else: ?>
                        <span class="badge badge-danger px-2 py-1" style="font-size: 0.6rem; border-radius: 100px; font-weight: 600;"><?= strtoupper(htmlspecialchars($s['status_aktif'])) ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center align-middle">
                        <div class="btn-group">
                            <a href="index.php?mod=profil_siswa&act=detail&id=<?= $s['id_siswa'] ?>" 
                               class="btn btn-xs btn-outline-info border-0 p-1 mr-1" 
                               style="background: #e0f2fe; width: 28px; height: 28px; border-radius: 8px; color: #0369a1;" title="Profil & Dokumen">
                                <i class="fas fa-user-circle" style="font-size: 0.85rem;"></i>
                            </a>
                            <a href="index.php?mod=siswa&act=form&id=<?= $s['id_siswa'] ?>" 
                               class="btn btn-xs btn-outline-warning border-0 p-1 mr-1" 
                               style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;" title="Edit Data">
                                <i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i>
                            </a>
                            <a href="index.php?mod=siswa&act=delete&id=<?= $s['id_siswa'] ?>" 
                               class="btn btn-xs btn-outline-danger border-0 p-1" 
                               style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;" 
                               title="Hapus" onclick="return confirmDelete(event, 'Hapus data siswa ini?')">
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

<!-- MODAL IMPORT SISWA (Standardized) -->
<div class="modal fade" id="modalImportSiswa" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="index.php?mod=siswa&act=import" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i> Import Data Siswa</h5>
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
                        <li>Data wajib: <strong>Nama</strong>, <strong>NISN</strong>.</li>
                    </ol>
                </div>
                
                <div class="form-group mb-4">
                    <label class="font-weight-bold"><i class="fas fa-download mr-1"></i> 1. Download Template</label>
                    <a href="index.php?mod=siswa&act=export&template=1" class="btn btn-outline-success btn-block">
                        <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
                    </a>
                </div>
                
                <div class="form-group mb-0">
                    <label class="font-weight-bold"><i class="fas fa-upload mr-1"></i> 2. Pilih File Excel</label>
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="custom-file-input" id="fileExcelSiswa" accept=".xls,.xlsx" required>
                        <label class="custom-file-label" for="fileExcelSiswa">Pilih file...</label>
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
  // Ensure jQuery is available before running the siswa search/init script.
  (function() {
    function initSiswaSearch() {
      $(function () {
        // Custom File Input Label
        $('.custom-file-input').on('change', function() {
          let fileName = $(this).val().split('\\').pop();
          $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // AJAX search with debounce
        const $searchInput = $('input[name="q"]');
        const $form = $searchInput.closest('form');
        let debounceTimer = null;

        function renderRows(data) {
          const $tbody = $('#siswa-table-body');
          if (!data || data.length === 0) {
            $tbody.html('<tr><td colspan="8" class="text-center py-5 text-muted small"><em>Belum ada data siswa yang ditemukan.</em></td></tr>');
            return;
          }
          let no = 1;
          let rows = '';
          data.forEach(function(s) {
            const jk = (String(s.jk).toUpperCase() === 'L' || String(s.jk).toUpperCase() === 'LAKI-LAKI') ? 'Laki-laki' : 'Perempuan';
            rows += '<tr>' +
                '<td class="text-center align-middle font-weight-bold text-muted small">' + (no++) + '</td>' +
                '<td class="align-middle py-1"><div class="d-flex align-items-center ml-2"><div class="rounded-circle d-flex align-items-center justify-content-center mr-2 border bg-light text-primary font-weight-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">' + (s.nama ? s.nama.charAt(0).toUpperCase() : '') + '</div><span class="font-weight-bold text-dark" style="font-size: 0.85rem;">' + (s.nama ? escapeHtml(s.nama) : '') + '</span></div></td>' +
                '<td class="text-center align-middle"><code class="text-muted small">' + (s.nisn ? escapeHtml(s.nisn) : '-') + '</code></td>' +
                '<td class="text-center align-middle"><code class="text-muted small">' + (s.nipd ? escapeHtml(s.nipd) : '-') + '</code></td>' +
                '<td class="text-center align-middle"><span class="small">' + jk + '</span></td>' +
                '<td class="text-center align-middle small">' + (s.tempat_lahir ? escapeHtml(s.tempat_lahir) : '') + ', ' + (s.tanggal_lahir ? escapeHtml(s.tanggal_lahir) : '') + '</td>' +
                '<td class="text-center align-middle">' + (s.status_aktif && s.status_aktif.trim() === 'Aktif' ? '<span class="badge badge-success px-2 py-1" style="font-size: 0.6rem; border-radius: 100px; font-weight: 600;">AKTIF</span>' : ('<span class="badge badge-danger px-2 py-1" style="font-size: 0.6rem; border-radius: 100px; font-weight: 600;">' + (s.status_aktif ? escapeHtml(s.status_aktif).toUpperCase() : '') + '</span>')) + '</td>' +
                '<td class="text-center align-middle"><div class="btn-group">' +
                '<a href="index.php?mod=profil_siswa&act=detail&id=' + s.id_siswa + '" class="btn btn-xs btn-outline-info border-0 p-1 mr-1" style="background: #e0f2fe; width: 28px; height: 28px; border-radius: 8px; color: #0369a1;" title="Profil & Dokumen"><i class="fas fa-user-circle" style="font-size: 0.85rem;"></i></a>' +
                '<a href="index.php?mod=siswa&act=form&id=' + s.id_siswa + '" class="btn btn-xs btn-outline-warning border-0 p-1 mr-1" style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;" title="Edit Data"><i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i></a>' +
                '<a href="index.php?mod=siswa&act=delete&id=' + s.id_siswa + '" class="btn btn-xs btn-outline-danger border-0 p-1" style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;" title="Hapus" onclick="return confirmDelete(event, \'Hapus data siswa ini?\')"><i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i></a>' +
                '</div></td>' +
                '</tr>';
          });
          $tbody.html(rows);
        }

        function escapeHtml(text) {
          return $('<div>').text(text).html();
        }

        function fetchAndRender(q) {
          $.ajax({
            url: 'index.php?mod=siswa&act=ajax_list',
            data: { q: q },
            dataType: 'json',
            success: function(res) {
              if (res && res.status === 'ok') {
                renderRows(res.data);
              }
            }
          });
        }

        // Intercept form submit to use AJAX
        $form.on('submit', function(e) {
          e.preventDefault();
          const val = $searchInput.val();
          fetchAndRender(val);
        });

        // Debounced keyup
        $searchInput.on('keyup', function() {
          clearTimeout(debounceTimer);
          const val = $(this).val();
          debounceTimer = setTimeout(function() { fetchAndRender(val); }, 400);
        });

        // Initial fetch if q has value
        const initialQ = $searchInput.val();
        if (initialQ && initialQ.trim() !== '') {
          fetchAndRender(initialQ);
        }
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