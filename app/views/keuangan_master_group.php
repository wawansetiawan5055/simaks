<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelompok Besar Keuangan (Group)</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-primary btn-sm" onclick="showAddModal()">
                        <i class="fas fa-plus"></i> Tambah Group
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-folder mr-1"></i> Pengelompokan Utama</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped" id="table-data">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="30">No</th>
                                        <th>Kode</th>
                                        <th>Nama Group</th>
                                        <th>Tipe Dasar</th>
                                        <th>Kategori Terkait</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($groups)): ?>
                                        <?php $no = 1; foreach ($groups as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><code><?= $row['kode_group'] ?></code></td>
                                            <td><div class="font-weight-bold"><?= $row['nama_group'] ?></div></td>
                                            <td>
                                                <span class="badge badge-<?= $row['tipe'] == 'MASUK' ? 'success' : 'danger' ?>">
                                                    <?= $row['tipe'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $row['jumlah_kategori'] ?> Pos</span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-info" onclick='edit(<?= json_encode($row) ?>)' title="Edit"><i class="fas fa-pencil-alt"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center">Data kosong</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modal-group" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="modal-title">Tambah Group</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="form-group">
                <input type="hidden" name="id_group" id="id_group">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Group</label>
                        <select class="form-control" name="tipe" id="tipe" required>
                            <option value="MASUK">MASUK (PENDAPATAN)</option>
                            <option value="KELUAR">KELUAR (BIAYA/PENGELUARAN)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Group</label>
                        <input type="text" class="form-control" name="kode_group" id="kode_group" required placeholder="Contoh: G-PEND, G-BIAYA">
                    </div>
                    <div class="form-group">
                        <label>Nama Group</label>
                        <input type="text" class="form-control" name="nama_group" id="nama_group" required placeholder="Contoh: Operasional, Pembangunan">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('form-group').reset();
    document.getElementById('id_group').value = '';
    document.getElementById('modal-title').innerText = 'Tambah Group';
    if (typeof $ !== 'undefined') $('#modal-group').modal('show');
}

function edit(data) {
    document.getElementById('modal-title').innerText = 'Edit Group';
    document.getElementById('id_group').value = data.id_group;
    document.getElementById('tipe').value = data.tipe;
    document.getElementById('kode_group').value = data.kode_group;
    document.getElementById('nama_group').value = data.nama_group;
    document.getElementById('keterangan').value = data.keterangan || '';
    
    if (typeof $ !== 'undefined') $('#modal-group').modal('show');
}
</script>

<?php include '../app/views/partials/footer.php'; ?>
