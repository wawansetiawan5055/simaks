<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class="fas fa-flask mr-2 text-indigo"></i><?= $title ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <form action="index.php" method="GET" class="form-inline d-inline-block">
                    <input type="hidden" name="mod" value="manajemen_lab">
                    <select name="id_ta" class="form-control form-control-sm" onchange="this.form.submit()">
                        <?php foreach ($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id_ta'] ?>" <?= $ta['id_ta'] == $id_ta ? 'selected' : '' ?>>TA: <?= htmlspecialchars($ta['nama_ta']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-indigo card-outline card-tabs shadow-sm">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="labTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="inventaris-tab" data-toggle="pill" href="#tab-inventaris" role="tab"><i class="fas fa-tools mr-2"></i>Inventaris Sarana</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="jadwal-tab" data-toggle="pill" href="#tab-jadwal" role="tab"><i class="fas fa-calendar-alt mr-2"></i>Jadwal Penggunaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="galeri-tab" data-toggle="pill" href="#tab-galeri" role="tab"><i class="fas fa-images mr-2"></i>Galeri Foto</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: INVENTARIS -->
                    <div class="tab-pane fade show active" id="tab-inventaris" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalInventaris"><i class="fas fa-plus mr-1"></i>Tambah Barang</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-sm">
                                <thead class="bg-indigo text-white">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Merek / Tipe</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center text-success">Baik</th>
                                        <th class="text-center text-danger">Rusak</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($items)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data inventaris.</td></tr>
                                    <?php else: foreach($items as $item): ?>
                                        <tr>
                                            <td class="font-weight-bold"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                            <td><?= htmlspecialchars($item['merek_tipe']) ?></td>
                                            <td class="text-center font-weight-bold"><?= $item['jumlah_total'] ?></td>
                                            <td class="text-center"><?= $item['kondisi_baik'] ?></td>
                                            <td class="text-center"><?= $item['kondisi_rusak'] ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-info edit-item" data-json='<?= json_encode($item) ?>'><i class="fas fa-edit"></i></button>
                                                <a href="<?= BASE_URL ?>manajemen_lab/delete_inventaris?id=<?= $item['id_inventaris'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: JADWAL -->
                    <div class="tab-pane fade" id="tab-jadwal" role="tabpanel">
                         <div class="row">
                            <div class="col-md-4">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-light"><h3 class="card-title text-sm font-weight-bold">Input Jadwal/Agenda Lab</h3></div>
                                    <form action="<?= BASE_URL ?>tugas_tambahan/save_agenda" method="POST">
                                        <input type="hidden" name="jenis" value="manajemen_lab">
                                        <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                                        <div class="card-body">
                                            <div class="form-group text-sm">
                                                <label>Nama Kegiatan / Penggunaan</label>
                                                <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Praktikum Kelas X-A" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Keterangan</label>
                                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white"><button type="submit" class="btn btn-indigo btn-block btn-sm">Simpan Jadwal</button></div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-sm table-hover text-sm">
                                    <thead><tr><th>Tanggal</th><th>Kegiatan</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($agendas)): ?>
                                            <tr><td colspan="4" class="text-center py-4 text-muted Italics">Belum ada jadwal.</td></tr>
                                        <?php else: foreach($agendas as $ag): ?>
                                            <tr>
                                                <td class="font-weight-bold"><?= date('d M Y', strtotime($ag['tanggal'])) ?></td>
                                                <td><?= htmlspecialchars($ag['nama_kegiatan']) ?></td>
                                                <td><?= htmlspecialchars($ag['keterangan']) ?></td>
                                                <td><a href="<?= BASE_URL ?>tugas_tambahan/delete_agenda?id=<?= $ag['id_agenda'] ?>" class="text-danger"><i class="fas fa-trash"></i></a></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                         </div>
                    </div>

                    <!-- TAB 3: GALERI -->
                    <div class="tab-pane fade" id="tab-galeri" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form action="<?= BASE_URL ?>manajemen_lab/upload_foto" method="POST" enctype="multipart/form-data" class="form-inline">
                                    <input type="file" name="foto" class="form-control form-control-sm mr-2" required>
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-upload mr-1"></i>Upload Foto</button>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <?php if(empty($photos)): ?>
                                <div class="col-12 text-center py-5 text-muted">Belum ada foto galeri.</div>
                            <?php else: foreach($photos as $photo): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card shadow-sm h-100">
                                        <img src="<?= $photo ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Inventaris -->
<div class="modal fade" id="modalInventaris" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= BASE_URL ?>manajemen_lab/save_inventaris" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-indigo text-white"><h5 class="modal-title">Data Inventaris</h5><button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button></div>
                <div class="modal-body">
                    <input type="hidden" name="id_inventaris" id="form-id">
                    <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                    <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" id="form-nama" class="form-control" placeholder="Server, PC, Router, dll" required></div>
                    <div class="form-group"><label>Merek / Tipe</label><input type="text" name="merek_tipe" id="form-merek" class="form-control" placeholder="Asus, Cisco, dll"></div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label>Kondisi Baik</label><input type="number" name="kondisi_baik" id="form-baik" class="form-control" value="0" min="0"></div></div>
                        <div class="col-6"><div class="form-group"><label>Kondisi Rusak</label><input type="number" name="kondisi_rusak" id="form-rusak" class="form-control" value="0" min="0"></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan Data</button></div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.edit-item').click(function(){
        let data = $(this).data('json');
        $('#form-id').val(data.id_inventaris);
        $('#form-nama').val(data.nama_barang);
        $('#form-merek').val(data.merek_tipe);
        $('#form-baik').val(data.kondisi_baik);
        $('#form-rusak').val(data.kondisi_rusak);
        $('#modalInventaris').modal('show');
    });

    $('#modalInventaris').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#form-id').val('');
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
