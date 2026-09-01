<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($title ?? 'Data Gedung & Bangunan') ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalTambahGedung">
                    <i class="fas fa-plus mr-1"></i> Tambah Gedung
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>
        
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Daftar Gedung & Prasarana</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form" id="btn-tambah">
                        <i class="fas fa-plus"></i> Tambah Gedung
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped table-hover text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Kode Gedung</th>
                            <th>Nama Gedung</th>
                            <th>Kondisi</th>
                            <th>Keterangan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($gedung as $g): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($g['kode_gedung'] ?? '-') ?></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($g['nama_gedung']) ?></td>
                            <td>
                                <?php if ($g['kondisi'] == 'Baik'): ?>
                                    <span class="badge badge-success">Baik</span>
                                <?php elseif ($g['kondisi'] == 'Rusak Ringan'): ?>
                                    <span class="badge badge-warning">Rusak Ringan</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rusak Berat</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($g['keterangan'] ?? '-') ?></td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-info btn-edit" 
                                    data-id="<?= $g['id_gedung'] ?>"
                                    data-nama="<?= htmlspecialchars($g['nama_gedung'], ENT_QUOTES) ?>"
                                    data-kode="<?= htmlspecialchars($g['kode_gedung'] ?? '', ENT_QUOTES) ?>"
                                    data-kondisi="<?= $g['kondisi'] ?>"
                                    data-keterangan="<?= htmlspecialchars($g['keterangan'] ?? '', ENT_QUOTES) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= BASE_URL ?>sarpras_gedung/delete?id=<?= $g['id_gedung'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Yakin ingin menghapus gedung ini? Semua ruangan dan barang di dalamnya akan ikut terhapus!')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Form -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title-text">Tambah Gedung</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>sarpras_gedung/save" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_gedung" id="id_gedung">
                    <div class="form-group">
                        <label>Kode Gedung</label>
                        <input type="text" class="form-control" name="kode_gedung" id="kode_gedung" placeholder="Contoh: A, B, GOR">
                    </div>
                    <div class="form-group">
                        <label>Nama Gedung/Prasarana <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_gedung" id="nama_gedung" required placeholder="Contoh: Gedung Utama, Lapangan Basket">
                    </div>
                    <div class="form-group">
                        <label>Kondisi</label>
                        <select class="form-control" name="kondisi" id="kondisi">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
$(function () {
    $("#example1").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    $('#btn-tambah, [data-target="#modal-form"]').on('click', function() {
        $('#modal-title-text').text('Tambah Gedung');
        $('#id_gedung').val('');
        $('#nama_gedung').val('');
        $('#kode_gedung').val('');
        $('#kondisi').val('Baik');
        $('#keterangan').val('');
        $('#modal-form').modal('show');
    });

    $('.btn-edit').on('click', function() {
        $('#modal-title-text').text('Edit Gedung');
        $('#id_gedung').val($(this).data('id'));
        $('#nama_gedung').val($(this).data('nama'));
        $('#kode_gedung').val($(this).data('kode'));
        $('#kondisi').val($(this).data('kondisi'));
        $('#keterangan').val($(this).data('keterangan'));
        $('#modal-form').modal('show');
    });
});
</script>
