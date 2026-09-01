<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-dolly-flatbed"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($title ?? 'Inventaris Sarana & Barang') ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalTambahBarang">
                    <i class="fas fa-plus mr-1"></i> Tambah Barang
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
                <h3 class="card-title">Daftar Inventaris Barang</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-form" id="btn-tambah">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped table-hover text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2" width="5%" class="text-center align-middle">No</th>
                            <th rowspan="2" class="align-middle">Nama Barang</th>
                            <th rowspan="2" class="align-middle">Ruangan (Gedung)</th>
                            <th rowspan="2" class="align-middle">Kode/Merk</th>
                            <th colspan="3" class="text-center">Kondisi</th>
                            <th rowspan="2" class="text-center align-middle">Total</th>
                            <th rowspan="2" width="10%" class="text-center align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th class="text-center text-success">Baik</th>
                            <th class="text-center text-warning">R. Ringan</th>
                            <th class="text-center text-danger">R. Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($barang as $b): 
                            $total = $b['kondisi_baik'] + $b['kondisi_rusak_ringan'] + $b['kondisi_rusak_berat'];
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="font-weight-bold"><?= htmlspecialchars($b['nama_barang']) ?></td>
                            <td>
                                <?= htmlspecialchars($b['nama_ruang'] ?? 'Tanpa Ruang') ?>
                                <br><small class="text-muted"><?= htmlspecialchars($b['nama_gedung'] ?? '-') ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($b['kode_barang'] ?? '-') ?><br>
                                <small class="text-muted">Merk: <?= htmlspecialchars($b['merk'] ?? '-') ?></small>
                            </td>
                            <td class="text-center font-weight-bold text-success"><?= $b['kondisi_baik'] ?></td>
                            <td class="text-center font-weight-bold text-warning"><?= $b['kondisi_rusak_ringan'] ?></td>
                            <td class="text-center font-weight-bold text-danger"><?= $b['kondisi_rusak_berat'] ?></td>
                            <td class="text-center font-weight-bold bg-light"><?= $total ?></td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-info btn-edit" 
                                    data-id="<?= $b['id_barang'] ?>"
                                    data-ruang="<?= $b['id_ruang'] ?>"
                                    data-nama="<?= htmlspecialchars($b['nama_barang'], ENT_QUOTES) ?>"
                                    data-kode="<?= htmlspecialchars($b['kode_barang'] ?? '', ENT_QUOTES) ?>"
                                    data-merk="<?= htmlspecialchars($b['merk'] ?? '', ENT_QUOTES) ?>"
                                    data-tahun="<?= $b['tahun_pengadaan'] ?>"
                                    data-sumber="<?= htmlspecialchars($b['sumber_dana'] ?? '', ENT_QUOTES) ?>"
                                    data-baik="<?= $b['kondisi_baik'] ?>"
                                    data-ringan="<?= $b['kondisi_rusak_ringan'] ?>"
                                    data-berat="<?= $b['kondisi_rusak_berat'] ?>"
                                    data-keterangan="<?= htmlspecialchars($b['keterangan'] ?? '', ENT_QUOTES) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= BASE_URL ?>sarpras_barang/delete?id=<?= $b['id_barang'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Yakin ingin menghapus barang ini?')">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title-text">Tambah Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>sarpras_barang/save" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_barang" id="id_barang">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ruangan Tempat Barang <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_ruang" id="id_ruang" required style="width: 100%;">
                                    <option value="">-- Pilih Ruangan --</option>
                                    <?php foreach ($ruang_list as $r): ?>
                                        <option value="<?= $r['id_ruang'] ?>">
                                            <?= htmlspecialchars($r['nama_ruang']) ?> 
                                            (<?= htmlspecialchars($r['nama_gedung'] ?? 'Tanpa Gedung') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_barang" id="nama_barang" required placeholder="Cth: Meja Siswa, Proyektor">
                            </div>
                            <div class="form-group">
                                <label>Kode/No Inventaris</label>
                                <input type="text" class="form-control" name="kode_barang" id="kode_barang">
                            </div>
                            <div class="form-group">
                                <label>Merk/Spesifikasi</label>
                                <input type="text" class="form-control" name="merk" id="merk">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Thn Pengadaan</label>
                                        <input type="number" min="1900" max="<?= date('Y') ?>" class="form-control" name="tahun_pengadaan" id="tahun_pengadaan" placeholder="Cth: 2023">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Sumber Dana</label>
                                        <input type="text" class="form-control" name="sumber_dana" id="sumber_dana" placeholder="Cth: BOS, Yayasan">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card bg-light mt-2">
                                <div class="card-body py-2">
                                    <label class="d-block border-bottom pb-1 mb-2">Kondisi & Jumlah Barang</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group mb-0">
                                                <label class="text-success small">Baik</label>
                                                <input type="number" class="form-control form-control-sm" name="kondisi_baik" id="kondisi_baik" value="0" min="0">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group mb-0">
                                                <label class="text-warning small">R. Ringan</label>
                                                <input type="number" class="form-control form-control-sm" name="kondisi_rusak_ringan" id="kondisi_rusak_ringan" value="0" min="0">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group mb-0">
                                                <label class="text-danger small">R. Berat</label>
                                                <input type="number" class="form-control form-control-sm" name="kondisi_rusak_berat" id="kondisi_rusak_berat" value="0" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-2">
                                <label>Keterangan Tambahan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
                            </div>
                        </div>
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

<!-- Select2 -->
<link rel="stylesheet" href="<?= BASE_URL ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<script src="<?= BASE_URL ?>plugins/select2/js/select2.full.min.js"></script>

<script>
$(function () {
    // Initialize Select2
    $('#id_ruang').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#modal-form')
    });
    
    $("#example1").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    $('#btn-tambah, [data-target="#modal-form"]').on('click', function() {
        $('#modal-title-text').text('Tambah Inventaris Barang');
        $('#id_barang').val('');
        $('#id_ruang').val('').trigger('change');
        $('#nama_barang').val('');
        $('#kode_barang').val('');
        $('#merk').val('');
        $('#tahun_pengadaan').val('');
        $('#sumber_dana').val('');
        $('#kondisi_baik').val('0');
        $('#kondisi_rusak_ringan').val('0');
        $('#kondisi_rusak_berat').val('0');
        $('#keterangan').val('');
        $('#modal-form').modal('show');
    });

    $('.btn-edit').on('click', function() {
        $('#modal-title-text').text('Edit Inventaris Barang');
        $('#id_barang').val($(this).data('id'));
        $('#id_ruang').val($(this).data('ruang')).trigger('change');
        $('#nama_barang').val($(this).data('nama'));
        $('#kode_barang').val($(this).data('kode'));
        $('#merk').val($(this).data('merk'));
        $('#tahun_pengadaan').val($(this).data('tahun'));
        $('#sumber_dana').val($(this).data('sumber'));
        $('#kondisi_baik').val($(this).data('baik'));
        $('#kondisi_rusak_ringan').val($(this).data('ringan'));
        $('#kondisi_rusak_berat').val($(this).data('berat'));
        $('#keterangan').val($(this).data('keterangan'));
        $('#modal-form').modal('show');
    });
});
</script>
