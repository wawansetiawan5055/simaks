<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
        <div>
            <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-id-badge text-primary mr-2"></i> Data Master Guru & GTK</h2>
            <p class="text-muted small mb-0">Kelola informasi profil, kode guru, NUPTK, dan dokumen administratif pendidik.</p>
        </div>
        <div class="btn-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
            <a href="index.php?mod=guru&act=form" class="btn btn-warning btn-sm px-3 font-weight-bold text-white">
                <i class="fas fa-user-plus mr-1"></i> Tambah Guru
            </a>
            <button type="button" class="btn btn-light btn-sm px-3 border-left" data-toggle="modal" data-target="#modalImportGuru">
                <i class="fas fa-file-import mr-1 text-primary"></i> Impor Excel
            </button>
            <a href="index.php?mod=guru&act=export" class="btn btn-success btn-sm px-3 border-left border-white">
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
                <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-list-ul mr-2 text-primary"></i> DAFTAR GURU & STAFF</h6>
            </div>
            <div class="col-md-6 text-right">
                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.7rem; border-radius: 6px;">
                    <i class="fas fa-info-circle mr-1"></i> Gunakan format .xlsx untuk impor data
                </span>
            </div>
        </div>
      </div>
      
      <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0" style="border-collapse: collapse;">
          <thead style="background: #f8fafc;">
            <tr class="text-muted">
              <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
              <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">NAMA LENGKAP & DETAIL</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 80px;">KODE</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">NUPTK</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">NIK</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($guru_list)): ?>
              <tr>
                <td colspan="7" class="text-center py-5 text-muted small"><em>Belum ada data guru yang terdaftar.</em></td>
              </tr>
            <?php else: ?>
                <?php $no = 1; ?>
                <?php foreach ($guru_list as $g): ?>
                  <tr>
                    <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++; ?></td>
                    <td class="align-middle py-1">
                        <div class="d-flex align-items-center ml-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 border bg-light text-primary font-weight-bold shadow-none" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                <?= strtoupper(substr($g['nama'], 0, 1)) ?>
                            </div>
                            <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($g['nama']) ?></span>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge badge-light border px-2 py-1 text-dark" style="font-size: 0.75rem; border-radius: 6px;">
                            <?= htmlspecialchars($g['kode_guru']) ?>
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <code class="text-muted small"><?= htmlspecialchars($g['nuptk'] ?: '-') ?></code>
                    </td>
                    <td class="text-center align-middle">
                        <code class="text-muted small"><?= htmlspecialchars($g['nik'] ?: '-') ?></code>
                    </td>
                    <td class="text-center align-middle">
                      <?php if (trim($g['status']) == 'Aktif'): ?>
                        <span class="badge badge-success px-2 py-1 shadow-none" style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">AKTIF</span>
                      <?php else: ?>
                        <span class="badge badge-danger px-2 py-1 shadow-none" style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;"><?= strtoupper(htmlspecialchars($g['status'])) ?></span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center align-middle">
                        <div class="btn-group">
                            <a href="index.php?mod=profil_guru&act=detail&id=<?= $g['id_guru'] ?>" 
                               class="btn btn-xs btn-outline-info border-0 p-1 mr-1" 
                               style="background: #e0f2fe; width: 28px; height: 28px; border-radius: 8px; color: #0369a1;" title="Profil & Dokumen">
                                <i class="fas fa-address-card" style="font-size: 0.85rem;"></i>
                            </a>
                            <a href="index.php?mod=guru&act=form&id=<?= $g['id_guru'] ?>" 
                               class="btn btn-xs btn-outline-warning border-0 p-1 mr-1" 
                               style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;" title="Edit Data">
                                <i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i>
                            </a>
                            <a href="index.php?mod=guru&act=delete&id=<?= $g['id_guru'] ?>" 
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
        <form action="index.php?mod=guru&act=import" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
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
                    <a href="index.php?mod=guru&act=export&template=1" class="btn btn-outline-success btn-block">
                        <i class="fas fa-file-download mr-2"></i> Unduh Template Excel
                    </a>
                </div>
                
                <div class="form-group mb-0">
                    <label class="font-weight-bold"><i class="fas fa-upload mr-1"></i> 2. Pilih File Excel</label>
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="custom-file-input" id="fileExcelGuru" accept=".xls,.xlsx" required>
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
  $(function () {
    // Custom File Input Label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
  });
</script>