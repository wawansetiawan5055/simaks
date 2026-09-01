<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-scroll"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Master Template Surat
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-info btn-sm rounded-pill px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddTemplate">
                    <i class="fas fa-plus mr-1"></i> Tambah Template
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content px-3">
    <div class="container-fluid">
        <div class="row">
            <?php if(!empty($list)): 
                foreach($list as $t): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; transition: 0.3s;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-light p-3 rounded" style="border-radius: 10px;">
                                    <i class="fas fa-file-alt fa-2x text-info"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                        <a class="dropdown-item" href="#"><i class="fas fa-edit mr-2 text-warning"></i> Edit</a>
                                        <a class="dropdown-item" href="#"><i class="fas fa-trash mr-2 text-danger"></i> Hapus</a>
                                    </div>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark mb-1"><?= $t['nama_template'] ?></h6>
                            <div class="badge badge-info small mb-3"><?= $t['nama_kategori'] ?></div>
                            <p class="small text-muted mb-4 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?= strip_tags($t['isi_template']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Variabel: <span class="badge badge-light border"><?= count(explode(',', $t['variabel_tersedia'])) ?> Item</span></small>
                                <a href="#" class="btn btn-sm btn-outline-info rounded-pill px-3">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" style="width: 150px; opacity: 0.3;" class="mb-3">
                    <h5 class="text-muted mt-3">Belum ada template surat.</h5>
                    <p class="text-muted small">Mulai buat template pertama Anda untuk mempercepat pekerjaan TU.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal Add Template -->
<div class="modal fade" id="modalAddTemplate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <form action="<?= BASE_URL ?>surat/save_template" method="POST">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-info"><i class="fas fa-plus mr-2"></i> Tambah Template Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted">Nama Template</label>
                            <input type="text" name="nama_template" class="form-control" placeholder="Contoh: Surat Mutasi Keluar" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted">Kategori</label>
                            <select name="id_kategori" class="form-control" required>
                                <?php foreach($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Subjek / Perihal Default</label>
                        <input type="text" name="subjek_default" class="form-control" placeholder="Isi perihal default surat ini">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted d-flex justify-content-between">
                            Isi Template Surat
                            <span class="text-info small cursor-pointer" onclick="$('#helpVar').toggle()">Lihat Variabel <i class="fas fa-question-circle"></i></span>
                        </label>
                        <div id="helpVar" class="alert alert-info py-2 px-3 small mb-2" style="display:none; border-radius: 10px;">
                            Bisa gunakan variabel: <code>{{nama_siswa}}</code>, <code>{{nisn}}</code>, <code>{{kelas}}</code>, <code>{{nama_guru}}</code>, <code>{{nip}}</code>, <code>{{nomor_surat}}</code>, <code>{{tgl_sekarang}}</code>
                        </div>
                        <textarea name="isi_template" class="form-control summernote" placeholder="Tuliskan format isi surat di sini. Gunakan variabel di dalam kurung kurawal ganda {{...}}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info px-4">Simpan Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../app/views/partials/footer.php'; ?>
