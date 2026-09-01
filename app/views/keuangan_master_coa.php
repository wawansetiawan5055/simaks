<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-list-ol mr-2"></i> Bagan Akun Standar (Chart of Accounts)</h1>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-warning btn-sm" onclick="showAddKategori()">
                    <i class="fas fa-folder-plus"></i> Tambah Kategori (Level 1)
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="showAddJenis()">
                    <i class="fas fa-plus"></i> Tambah Akun (Level 2)
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium COA Table Styling */
    .table-coa td,
    .table-coa th {
        vertical-align: middle !important;
        padding: 12px 15px !important;
    }

    /* Level 1 (Kategori) Accent */
    .row-level-1 {
        background-color: #f8fafc !important;
        border-left: 4px solid #64748b !important;
    }

    .row-level-1 td {
        font-size: 1rem;
        color: #1e293b;
    }

    /* Level 2 (Jenis) Indentation & Icon */
    .row-level-2 {
        border-left: 4px solid transparent;
    }

    .indent-level-2 {
        padding-left: 40px !important;
        color: #475569;
    }

    .bullet-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        margin-right: 10px;
    }

    .badge-pill-custom {
        border-radius: 50px;
        padding: 4px 12px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }

    .action-btns .btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin: 0 2px;
    }

    .code-block {
        background: #f1f5f9;
        color: #475569;
        padding: 2px 8px;
        border-radius: 6px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .card-coa {
        border-radius: 16px !important;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="card card-coa shadow-sm border-0">
            <div class="card-header bg-white py-3 border-0">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-sitemap mr-2 text-primary"></i> Struktur COA (4xxx & 5xxx)
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-coa table-hover m-0">
                    <thead class="bg-light text-muted small uppercase font-weight-bold">
                        <tr>
                            <th width="140" class="border-0 px-4">Kode Akun</th>
                            <th class="border-0">Nama Akun / Kategori</th>
                            <th width="120" class="text-center border-0">Tipe</th>
                            <th width="180" class="text-right border-0 px-4">Tarif Default</th>
                            <th width="120" class="text-center border-0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($coa)): ?>
                            <?php foreach ($coa as $cat): ?>
                                <!-- LEVEL 1 (KATEGORI) -->
                                <tr class="row-level-1">
                                    <td class="px-4">
                                        <span class="code-block" style="background: #334155; color: #fff;">
                                            <?= $cat['kode_akun'] ?>00
                                        </span>
                                    </td>
                                    <td class="font-weight-bold">
                                        <i class="fas fa-folder text-warning mr-2"></i> <?= $cat['nama_kategori'] ?>
                                        <span
                                            class="ml-2 text-muted font-weight-normal small">(<?= $cat['kode_kategori'] ?>)</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-pill-custom badge-<?= $cat['tipe'] == 'MASUK' ? 'success' : 'danger' ?>">
                                            <?= $cat['tipe'] ?>
                                        </span>
                                    </td>
                                    <td></td>
                                    <td class="text-center action-btns">
                                        <button class="btn btn-outline-secondary btn-sm" title="Edit Kategori"
                                            onclick='editKategori(<?= json_encode($cat) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- LEVEL 2 (JENIS) -->
                                <?php if (!empty($cat['items'])): ?>
                                    <?php foreach ($cat['items'] as $item): ?>
                                        <tr class="row-level-2">
                                            <td class="px-4 text-center">
                                                <span class="code-block"><?= $item['kode_akun'] ?></span>
                                            </td>
                                            <td class="indent-level-2">
                                                <span class="bullet-indicator"></span>
                                                <?= $item['nama_jenis'] ?>
                                                <?php if ($item['is_recurring']): ?>
                                                    <span class="badge badge-soft-info ml-2 px-2 rounded-pill"
                                                        style="font-size: 10px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">Rutin</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center text-muted small font-weight-bold">
                                                <?= $cat['tipe'] ?>
                                            </td>
                                            <td class="text-right px-4 font-weight-bold text-dark">
                                                <?php if ($item['harga_default'] > 0): ?>
                                                    Rp <?= number_format($item['harga_default'], 0, ',', '.') ?>
                                                <?php else: ?>
                                                    <span class="text-muted opacity-50">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center action-btns">
                                                <button class="btn btn-info text-white btn-sm" title="Edit Akun"
                                                    onclick='editJenis(<?= json_encode($item) ?>)'>
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-sitemap fa-3x mb-3 d-block opacity-25"></i>
                                    Belum ada data COA
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- MODAL KATEGORI (LEVEL 1) -->
<div class="modal fade" id="modal-kategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Form Kategori (Level 1)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>keuangan_master/save_kategori" method="post" id="form-kategori">
                <input type="hidden" name="id_kategori" id="kat_id_kategori">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe</label>
                        <select class="form-control" name="tipe" id="kat_tipe">
                            <option value="MASUK">PENDAPATAN</option>
                            <option value="KELUAR">PENGELUARAN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Akun (Awal)</label>
                        <input type="text" class="form-control" name="kode_akun" id="kat_kode_akun" placeholder="4100">
                    </div>
                    <div class="form-group">
                        <label>Kode Internal/Slug</label>
                        <input type="text" class="form-control" name="kode_kategori" id="kat_kode_kategori"
                            placeholder="PEND-AWAL">
                    </div>
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" class="form-control" name="nama_kategori" id="kat_nama_kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL JENIS (LEVEL 2) -->
<div class="modal fade" id="modal-jenis" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title">Form Akun (Level 2)</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>keuangan_master/save_jenis" method="post" id="form-jenis">
                <input type="hidden" name="id" id="jen_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Induk Kategori</label>
                        <select class="form-control" name="id_kategori" id="jen_id_kategori" required>
                            <?php foreach ($coa as $c): ?>
                                <option value="<?= $c['id_kategori'] ?>">[<?= $c['kode_akun'] ?>] <?= $c['nama_kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Akun</label>
                        <input type="text" class="form-control" name="kode_akun" id="jen_kode_akun" placeholder="4101">
                    </div>
                    <div class="form-group">
                        <label>Nama Akun</label>
                        <input type="text" class="form-control" name="nama_jenis" id="jen_nama_jenis" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Tarif Default (Rp)</label>
                                <input type="number" class="form-control" name="harga_default" id="jen_harga_default"
                                    value="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" name="is_recurring" value="1"
                                    id="jen_is_recurring">
                                <label class="form-check-label">Rutin (Bulanan)?</label>
                            </div>
                        </div>
                    </div>
                    <!-- Hidden fields required by controller saves -->
                    <input type="hidden" name="kode_jenis" id="jen_kode_jenis">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAddKategori() {
        $('#form-kategori')[0].reset();
        $('#kat_id_kategori').val('');
        $('#modal-kategori').modal('show');
    }
    function editKategori(data) {
        $('#kat_id_kategori').val(data.id_kategori);
        $('#kat_tipe').val(data.tipe);
        $('#kat_kode_akun').val(data.kode_akun);
        $('#kat_kode_kategori').val(data.kode_kategori);
        $('#kat_nama_kategori').val(data.nama_kategori);
        $('#modal-kategori').modal('show');
    }

    function showAddJenis() {
        $('#form-jenis')[0].reset();
        $('#jen_id').val('');
        $('#jen_kode_jenis').val('AUTO'); // Controller requires this
        $('#modal-jenis').modal('show');
    }

    function editJenis(data) {
        $('#jen_id').val(data.id_jenis);
        $('#jen_id_kategori').val(data.id_kategori);
        $('#jen_kode_akun').val(data.kode_akun);
        $('#jen_nama_jenis').val(data.nama_jenis);
        $('#jen_harga_default').val(parseInt(data.harga_default));
        $('#jen_kode_jenis').val(data.kode_jenis || 'AUTO');
        $('#jen_is_recurring').prop('checked', data.is_recurring == 1);
        $('#modal-jenis').modal('show');
    }

    // Ajax Submit Function
    function bindAjaxSubmit(formSelector) {
        const form = document.querySelector(formSelector);
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btnSubmit = this.querySelector('button[type="submit"]');
                const originalText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Try to parse JSON but also handle non-JSON responses just in case
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        return response.text().then(text => { throw new Error('Not JSON: ' + text) });
                    }
                })
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Berhasil', data.message || 'Data berhasil disimpan.', 'success').then(() => location.reload());
                        } else {
                            alert(data.message || 'Data berhasil disimpan.');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', data.message || 'Gagal menyimpan', 'error');
                        } else {
                            alert('Error: ' + (data.message || 'Gagal menyimpan'));
                        }
                    }
                })
                .catch(err => {
                    console.error('Submit Error:', err);
                    if (typeof Swal !== 'undefined') Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server', 'error');
                    else alert('Terjadi kesalahan sistem. Cek form untuk error.');
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                });
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        bindAjaxSubmit('#form-kategori');
        bindAjaxSubmit('#form-jenis');
    });
</script>

<?php include '../app/views/partials/footer.php'; ?>