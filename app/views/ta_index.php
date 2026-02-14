<?php 
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__.'/partials/header.php'; 
?>
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
        <div>
            <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-calendar-alt text-primary mr-2"></i> Data Tahun Ajaran</h2>
            <p class="text-muted small mb-0">Kelola periode kalender akademik dan tahun ajaran aktif sistem.</p>
        </div>
        <a href="index.php?mod=ta&act=form" class="btn btn-warning btn-sm px-3 shadow-none font-weight-bold text-white" style="border-radius: 8px;">
            <i class="fas fa-plus mr-1"></i> Tambah Tahun Ajaran
        </a>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
      <div class="card-header bg-white py-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 font-weight-bold text-muted small uppercase"><i class="fas fa-history mr-2 text-primary"></i> RIWAYAT TAHUN AJARAN</h6>
            <div>
                <span class="text-muted small mr-2">Status Aktif:</span>
                <?php if ($ta_aktif): ?>
                  <span class="badge badge-success px-3 py-2 shadow-none" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem;">
                      <i class="fas fa-check-circle mr-1"></i> <?= $ta_aktif['nama_ta'] ?>
                  </span>
                <?php else: ?>
                  <span class="badge badge-danger px-3 py-2 shadow-none" style="border-radius: 8px; font-weight: 600; font-size: 0.75rem;">BELEUM ADA YANG AKTIF</span>
                <?php endif; ?>
            </div>
        </div>
      </div>
      
      <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead style="background: #f8fafc;">
            <tr class="text-muted">
              <th class="text-center py-2 border-bottom" style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
              <th class="py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">TAHUN AJARAN</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">MULAI</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 140px;">SELESAI</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
              <th class="text-center py-2 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px; width: 150px;">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($ta_list)): ?>
                <?php $no = 1; foreach ($ta_list as $row): ?>
                  <tr class="<?= strtolower($row['status']) === 'aktif' ? 'bg-light' : '' ?>">
                    <td class="text-center align-middle font-weight-bold text-muted small"><?= $no++; ?></td>
                    <td class="align-middle">
                        <span class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($row['nama_ta']) ?></span>
                    </td>
                    <td class="text-center align-middle small text-muted">
                        <?= DateHelper::formatTanggal($row['tanggal_mulai'], 'short') ?>
                    </td>
                    <td class="text-center align-middle small text-muted">
                        <?= DateHelper::formatTanggal($row['tanggal_selesai'], 'short') ?>
                    </td>
                    <td class="text-center align-middle">
                      <?php if (strtolower($row['status']) === 'aktif'): ?>
                        <span class="badge badge-success px-2 py-1" style="font-size: 0.6rem; border-radius: 100px; font-weight: 600;">AKTIF</span>
                      <?php else: ?>
                        <span class="badge badge-light border px-2 py-1 text-muted" style="font-size: 0.6rem; border-radius: 100px; font-weight: 600;">NONAKTIF</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center align-middle">
                        <div class="btn-group">
                            <?php if (strtolower($row['status']) !== 'aktif'): ?>
                              <a href="index.php?mod=ta&act=set_aktif&id=<?= $row['id_ta'] ?>" 
                                 class="btn btn-xs btn-outline-success border-0 p-1 mr-1" 
                                 style="background: #f0fdf4; width: 28px; height: 28px; border-radius: 8px; color: #16a34a;" 
                                 title="Set Aktif" onclick="return confirm('Aktifkan Tahun Ajaran ini?')">
                                  <i class="fas fa-check-double" style="font-size: 0.8rem;"></i>
                              </a>
                            <?php endif; ?>
                            <a href="index.php?mod=ta&act=form&id=<?= $row['id_ta'] ?>" 
                               class="btn btn-xs btn-outline-warning border-0 p-1 mr-1" 
                               style="background: #fffbeb; width: 28px; height: 28px; border-radius: 8px; color: #d97706;" 
                               title="Edit"><i class="fas fa-pencil-alt" style="font-size: 0.8rem;"></i></a>
                            <a href="index.php?mod=ta&act=delete&id=<?= $row['id_ta'] ?>" 
                               class="btn btn-xs btn-outline-danger border-0 p-1" 
                               style="background: #fef2f2; width: 28px; height: 28px; border-radius: 8px; color: #dc2626;" 
                               title="Hapus" onclick="return confirmDelete(event)">
                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center py-5 text-muted small"><em>Belum ada data tahun ajaran.</em></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__.'/partials/footer.php'; ?>