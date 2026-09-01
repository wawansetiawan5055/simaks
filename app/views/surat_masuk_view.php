<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-file-import"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Arsip Surat Masuk
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold rounded-pill px-3" data-toggle="modal" data-target="#modalAddSuratMasuk">
                    <i class="fas fa-plus mr-1"></i> Catat Surat Masuk
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content px-3">
    <div class="container-fluid">
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                            <tr>
                                <th class="text-center py-3" width="50">No</th>
                                <th class="py-3">Nomor / Tgl Surat</th>
                                <th class="py-3">Asal Surat & Perihal</th>
                                <th class="py-3" width="120">Tgl Terima</th>
                                <th class="text-center py-3" width="100">Status</th>
                                <th class="text-center py-3" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($list)): 
                                $no = 1;
                                foreach($list as $s): ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-dark"><?= $s['nomor_surat'] ?></div>
                                        <div class="small text-muted"><?= date('d/m/Y', strtotime($s['tgl_surat'])) ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-bold"><?= $s['asal_surat'] ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 300px;"><?= $s['perihal'] ?></div>
                                    </td>
                                    <td class="align-middle small">
                                        <?= date('d/m/Y', strtotime($s['tgl_terima'])) ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php 
                                        $badgeClass = 'primary';
                                        if($s['status'] == 'Diproses') $badgeClass = 'warning';
                                        if($s['status'] == 'Selesai') $badgeClass = 'success';
                                        ?>
                                        <span class="badge badge-<?= $badgeClass ?>">
                                            <?= $s['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-outline-primary" title="Detail/Disposisi"><i class="fas fa-eye"></i></a>
                                            <?php if($s['file_scan']): ?>
                                                <a href="<?= $s['file_scan'] ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Lihat Scan"><i class="fas fa-file-pdf"></i></a>
                                            <?php endif; ?>
                                            <a href="#" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data surat masuk.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Add Surat Masuk -->
<div class="modal fade" id="modalAddSuratMasuk" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <form action="<?= BASE_URL ?>surat/save_masuk" method="POST" enctype="multipart/form-data">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-file-import mr-2"></i> Catat Surat Masuk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted">Nomor Surat Asal</label>
                            <input type="text" name="nomor_surat" class="form-control" placeholder="Masukkan nomor surat dari pengirim" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted">Asal Surat</label>
                            <input type="text" name="asal_surat" class="form-control" placeholder="Contoh: Dinas Pendidikan Kab. Bandung" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted">Tanggal Surat Asal</label>
                            <input type="date" name="tgl_surat" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted">Tanggal Diterima</label>
                            <input type="date" name="tgl_terima" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Perihal</label>
                        <input type="text" name="perihal" class="form-control" placeholder="Tuliskan perihal surat" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">File Scan Surat (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" name="file_scan" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Pilih file PDF/Gambar...</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../app/views/partials/footer.php'; ?>
