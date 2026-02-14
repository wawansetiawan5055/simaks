<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h2 class="fw-bold m-0 text-dark"><i class="fas fa-file-export text-success mr-2"></i> Surat Keluar</h2>
                <p class="text-muted small mb-0">Kelola dan cetak surat resmi sekolah.</p>
            </div>
            <button type="button" class="btn btn-success shadow-sm" data-toggle="modal" data-target="#modalAddSurat">
                <i class="fas fa-plus mr-1"></i> Buat Surat Baru
            </button>
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
                                <th class="py-3">Nomor Surat / Tanggal</th>
                                <th class="py-3">Penerima & Perihal</th>
                                <th class="py-3">Kategori / Template</th>
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
                                        <div class="fw-bold"><?= $s['tujuan'] ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 300px;"><?= $s['perihal'] ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-outline-secondary"><?= $s['kode_kategori'] ?></span>
                                        <div class="small mt-1"><?= $s['nama_template'] ?? 'Ketik Manual' ?></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-<?= $s['status'] == 'Final' ? 'success' : 'warning' ?>">
                                            <?= $s['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="index.php?mod=surat&act=print_keluar&id=<?= $s['id_surat_keluar'] ?>" target="_blank" class="btn btn-sm btn-info" title="Preview/Print"><i class="fas fa-print"></i></a>
                                            <a href="#" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data surat keluar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Add Surat -->
<div class="modal fade" id="modalAddSurat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <form id="formSuratKeluar" method="POST" action="index.php?mod=surat&act=save_keluar">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-pencil-alt text-success mr-2"></i> Buat Surat Keluar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Kategori / Klasifikasi</label>
                            <select name="id_kategori" id="id_kategori" class="form-control" required onchange="generateNomor()">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= $k['kode_kategori'] ?> - <?= $k['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Nomor Surat</label>
                            <input type="text" name="nomor_surat" id="nomor_surat" class="form-control bg-light" placeholder="Pilih kategori dulu..." readonly required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Tanggal Surat</label>
                            <input type="date" name="tgl_surat" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Gunakan Template (Opsional)</label>
                            <select name="id_template" id="id_template" class="form-control" onchange="loadTemplate()">
                                <option value="">-- Ketik Manual --</option>
                                <?php foreach($templates as $t): ?>
                                    <option value="<?= $t['id_template'] ?>"><?= $t['nama_template'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Tujuan / Penerima</label>
                        <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Orang Tua Wali Murid Kelas X" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Perihal</label>
                        <input type="text" name="perihal" id="perihal" class="form-control" placeholder="Masukkan perihal surat" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Isi Surat</label>
                        <textarea name="isi_surat" id="isi_surat" class="form-control" rows="8" placeholder="Tuliskan isi surat di sini..."></textarea>
                        <div class="small text-muted mt-1">Gunakan editor ini untuk menyesuaikan isi surat sebelum dicetak.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan Sebagai Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generateNomor() {
    const idKat = $('#id_kategori').val();
    if(idKat) {
        $.get('index.php?mod=surat&act=get_nomor_otomatis&id_kategori=' + idKat, function(res) {
            $('#nomor_surat').val(res);
        });
    } else {
        $('#nomor_surat').val('');
    }
}

function loadTemplate() {
    const idTemplate = $('#id_template').val();
    if(idTemplate) {
        $.getJSON('index.php?mod=surat&act=get_template_content&id=' + idTemplate, function(res) {
            if(res) {
                $('#perihal').val(res.subjek_default);
                $('#isi_surat').val(res.isi_template);
            }
        });
    } else {
        $('#perihal').val('');
        $('#isi_surat').val('');
    }
}
</script>

<style>
.badge-outline-secondary {
    border: 1px solid #6c757d;
    color: #6c757d;
    background: transparent;
}
</style>

<?php include '../app/views/partials/footer.php'; ?>
