<?php include __DIR__.'/partials/header.php'; ?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Master Komponen Penilaian</h1>
      </div>
      <div class="col-sm-6 text-right">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAdd">
          <i class="fas fa-plus mr-1"></i> Tambah Komponen
        </button>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    
    <div class="card card-outline card-primary shadow">
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th width="50">No</th>
              <th width="150">Kategori</th>
              <th>Nama Komponen</th>
              <th>Deskripsi</th>
              <th width="150" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no=1; foreach($komponen_list as $row): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <span class="badge badge-<?= $row['kategori'] == 'Sikap' ? 'info' : 'success' ?>">
                  <?= $row['kategori'] ?>
                </span>
              </td>
              <td class="font-weight-bold"><?= htmlspecialchars($row['nama_komponen']) ?></td>
              <td class="text-muted"><small><?= htmlspecialchars($row['deskripsi']) ?></small></td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-edit" 
                        data-id="<?= $row['id_komponen'] ?>"
                        data-kategori="<?= $row['kategori'] ?>"
                        data-nama="<?= htmlspecialchars($row['nama_komponen']) ?>"
                        data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['id_komponen'] ?>" data-nama="<?= htmlspecialchars($row['nama_komponen']) ?>">
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

<!-- Modal Add -->
<div class="modal fade" id="modalAdd">
  <div class="modal-dialog">
    <form action="<?= BASE_URL ?>komponen_sikap" method="POST">
      <input type="hidden" name="action" value="create">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Komponen</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Kategori</label>
            <select name="kategori" class="form-control" required>
              <option value="Sikap">Sikap (Afektif)</option>
              <option value="Keaktifan Belajar">Keaktifan Belajar</option>
              <option value="Profil Lulusan">Profil Lulusan</option>
            </select>
          </div>
          <div class="form-group">
            <label>Nama Komponen</label>
            <input type="text" name="nama_komponen" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit">
  <div class="modal-dialog">
    <form action="<?= BASE_URL ?>komponen_sikap" method="POST">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id_komponen" id="edit-id">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h4 class="modal-title">Edit Komponen</h4>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Kategori</label>
            <select name="kategori" id="edit-kategori" class="form-control" required>
              <option value="Sikap">Sikap (Afektif)</option>
              <option value="Keaktifan Belajar">Keaktifan Belajar</option>
              <option value="Profil Lulusan">Profil Lulusan</option>
            </select>
          </div>
          <div class="form-group">
            <label>Nama Komponen</label>
            <input type="text" name="nama_komponen" id="edit-nama" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete">
  <div class="modal-dialog">
    <form action="<?= BASE_URL ?>komponen_sikap" method="POST">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id_komponen" id="delete-id">
      <div class="modal-content">
        <div class="modal-body text-center py-4">
          <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
          <h5>Apakah Anda yakin?</h5>
          <p>Anda akan menghapus komponen <strong id="delete-nama"></strong>. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer justify-content-center border-0">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
  $('.btn-edit').on('click', function(){
    const data = $(this).data();
    $('#edit-id').val(data.id);
    $('#edit-kategori').val(data.kategori);
    $('#edit-nama').val(data.nama);
    $('#edit-deskripsi').val(data.deskripsi);
    $('#modalEdit').modal('show');
  });

  $('.btn-delete').on('click', function(){
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    $('#delete-id').val(id);
    $('#delete-nama').text(nama);
    $('#modalDelete').modal('show');
  });
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>
