<?php include __DIR__.'/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-user-check"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Penilaian Sikap &amp; Partisipasi Siswa
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <a href="<?= BASE_URL ?>penilaian_sikap/form_agenda" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
          <i class="fas fa-calendar-plus mr-1"></i> Buat Agenda Baru
        </a>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    
    <div class="card card-outline card-primary shadow">
      <div class="card-header">
        <h3 class="card-title">Daftar Agenda Penilaian</h3>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th width="50">No</th>
              <?php if(is_admin()): ?><th>Guru</th><?php endif; ?>
              <th>Kelas / Mapel</th>
              <th>Periode</th>
              <th>Jenis Penilai</th>
              <th class="text-center">Nilai Tambah?</th>
              <th width="220" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($agendas)): ?>
              <tr>
                <td colspan="6" class="text-center py-4 text-muted italic">Belum ada agenda penilaian. Silakan buat agenda baru.</td>
              </tr>
            <?php endif; ?>

            <?php $no=1; foreach($agendas as $row): ?>
            <tr>
              <td><?= $no++ ?></td>
              <?php if(is_admin()): ?>
              <td><span class="font-weight-bold text-primary"><?= $row['nama_guru'] ?></span></td>
              <?php endif; ?>
              <td>
                <div class="d-flex flex-column">
                  <span class="font-weight-bold"><?= $row['nama_kelas'] ?></span>
                  <small class="text-muted"><?= $row['nama_mapel'] ?: 'Umum (Wali Kelas)' ?></small>
                </div>
              </td>
              <td><?= $row['periode'] ?></td>
              <td>
                <span class="badge badge-<?= $row['kategori_penilai'] == 'Wali Kelas' ? 'purple' : 'info' ?>" style="<?= $row['kategori_penilai'] == 'Wali Kelas' ? 'background-color: #6f42c1; color: white;' : '' ?>">
                  <?= $row['kategori_penilai'] ?>
                </span>
              </td>
              <td class="text-center">
                <?php if ($row['is_nilai_tambahan']): ?>
                  <span class="badge badge-success">Ya (<?= $row['bobot_tambahan'] ?>%)</span>
                <?php else: ?>
                  <span class="badge badge-secondary">Tidak</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a href="<?= BASE_URL ?>penilaian_sikap/form_nilai?id=<?= $row['id_agenda'] ?>" class="btn btn-sm btn-success" title="Input Nilai">
                  <i class="fas fa-list-check"></i>
                </a>
                <a href="<?= BASE_URL ?>penilaian_sikap/form_agenda?id=<?= $row['id_agenda'] ?>" class="btn btn-sm btn-info">
                  <i class="fas fa-cog"></i>
                </a>
                <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['id_agenda'] ?>" data-name="<?= $row['nama_kelas'] ?> - <?= $row['periode'] ?>">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete">
  <div class="modal-dialog">
    <form action="<?= BASE_URL ?>penilaian_sikap/delete" method="POST">
      <input type="hidden" name="id_agenda" id="delete-id">
      <div class="modal-content">
        <div class="modal-body text-center py-4">
          <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
          <h5>Hapus Agenda?</h5>
          <p>Anda akan menghapus agenda penilaian untuk <strong id="delete-name"></strong>.<br>Semua nilai siswa dalam agenda ini juga akan terhapus.</p>
        </div>
        <div class="modal-footer justify-content-center border-0">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger px-4">Ya, Hapus Tetap</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
  $('.btn-delete').on('click', function(){
    $('#delete-id').val($(this).data('id'));
    $('#delete-name').text($(this).data('name'));
    $('#modalDelete').modal('show');
  });
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>
