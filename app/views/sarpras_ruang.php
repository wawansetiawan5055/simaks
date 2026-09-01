<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($title ?? 'Data Ruangan & Sarana') ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalTambahRuang">
                    <i class="fas fa-plus mr-1"></i> Tambah Ruang
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
                <h3 class="card-title">Daftar Ruangan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form" id="btn-tambah">
                        <i class="fas fa-plus"></i> Tambah Ruangan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped table-hover text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>Gedung</th>
                            <th>Kode Ruang</th>
                            <th>Nama Ruangan</th>
                            <th class="text-center">Kapasitas</th>
                            <th class="text-center">Lantai</th>
                            <th>Kondisi</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($ruang as $r): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['nama_gedung'] ?? 'Tidak Diketahui') ?></td>
                            <td><?= htmlspecialchars($r['kode_ruang'] ?? '-') ?></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($r['nama_ruang']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($r['kapasitas']) ?> Orang</td>
                            <td class="text-center"><?= htmlspecialchars($r['lantai']) ?></td>
                            <td>
                                <?php if ($r['kondisi'] == 'Baik'): ?>
                                    <span class="badge badge-success">Baik</span>
                                <?php elseif ($r['kondisi'] == 'Rusak Ringan'): ?>
                                    <span class="badge badge-warning">Rusak Ringan</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Rusak Berat</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-info btn-edit" 
                                    data-id="<?= $r['id_ruang'] ?>"
                                    data-gedung="<?= $r['id_gedung'] ?>"
                                    data-nama="<?= htmlspecialchars($r['nama_ruang'], ENT_QUOTES) ?>"
                                    data-kode="<?= htmlspecialchars($r['kode_ruang'] ?? '', ENT_QUOTES) ?>"
                                    data-kapasitas="<?= $r['kapasitas'] ?>"
                                    data-lantai="<?= $r['lantai'] ?>"
                                    data-kondisi="<?= $r['kondisi'] ?>"
                                    data-keterangan="<?= htmlspecialchars($r['keterangan'] ?? '', ENT_QUOTES) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= BASE_URL ?>sarpras_ruang/delete?id=<?= $r['id_ruang'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Yakin ingin menghapus ruangan ini? Semua inventaris barang di dalamnya akan ikut terhapus!')">
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
                <h4 class="modal-title" id="modal-title-text">Tambah Ruangan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>sarpras_ruang/save" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_ruang" id="id_ruang">
                    <div class="form-group">
                        <label>Gedung <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_gedung" id="id_gedung" required>
                            <option value="">-- Pilih Gedung --</option>
                            <?php foreach ($gedung_list as $g): ?>
                                <option value="<?= $g['id_gedung'] ?>"><?= htmlspecialchars($g['nama_gedung']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kode Ruang</label>
                                <input type="text" class="form-control" name="kode_ruang" id="kode_ruang" placeholder="Cth: R-101">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_ruang" id="nama_ruang" required placeholder="Cth: Kelas 10A, Lab Komputer">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kapasitas (Orang)</label>
                                <input type="number" class="form-control" name="kapasitas" id="kapasitas" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lantai</label>
                                <input type="number" class="form-control" name="lantai" id="lantai" value="1">
                            </div>
                        </div>
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
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
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
        $('#modal-title-text').text('Tambah Ruangan');
        $('#id_ruang').val('');
        $('#id_gedung').val('');
        $('#nama_ruang').val('');
        $('#kode_ruang').val('');
        $('#kapasitas').val('0');
        $('#lantai').val('1');
        $('#kondisi').val('Baik');
        $('#keterangan').val('');
        $('#modal-form').modal('show');
    });

    $('.btn-edit').on('click', function() {
        $('#modal-title-text').text('Edit Ruangan');
        $('#id_ruang').val($(this).data('id'));
        $('#id_gedung').val($(this).data('gedung'));
        $('#nama_ruang').val($(this).data('nama'));
        $('#kode_ruang').val($(this).data('kode'));
        $('#kapasitas').val($(this).data('kapasitas'));
        $('#lantai').val($(this).data('lantai'));
        $('#kondisi').val($(this).data('kondisi'));
        $('#keterangan').val($(this).data('keterangan'));
        $('#modal-form').modal('show');
    });
});
</script>
