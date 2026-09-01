<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-university mr-2"></i> Manajemen Rekening Kas & Bank</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-primary btn-sm" onclick="showAddModal()">
                        <i class="fas fa-plus"></i> Tambah Rekening
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
                            <h3 class="card-title"><i class="fas fa-university mr-1"></i> Daftar Rekening</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped" id="table-data">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="30">No</th>
                                        <th>Kode</th>
                                        <th>Nama Rekening</th>
                                        <th>Tipe</th>
                                        <th>Informasi Bank</th>
                                        <th>Saldo Akhir</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($rekening)): ?>
                                        <?php $no = 1; foreach ($rekening as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><code><?= $row['kode_rekening'] ?></code></td>
                                            <td><div class="font-weight-bold"><?= $row['nama_rekening'] ?></div></td>
                                            <td>
                                                <span class="badge badge-<?= $row['tipe'] == 'BANK' ? 'info' : 'primary' ?>">
                                                    <?= $row['tipe'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($row['tipe'] == 'BANK'): ?>
                                                    <?= $row['nama_bank'] ?> - <?= $row['nomor_rekening'] ?><br>
                                                    <small class="text-muted">a.n <?= $row['atas_nama'] ?></small>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                Rp <?= number_format($row['saldo_akhir'], 0, ',', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $row['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-warning">Non-Aktif</span>' ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-info" onclick='edit(<?= json_encode($row) ?>)' title="Edit"><i class="fas fa-pencil-alt"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center">Data kosong</td></tr>
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
<div class="modal fade" id="modal-rekening" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="modal-title">Tambah Rekening</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="form-rekening">
                <input type="hidden" name="id_rekening" id="id_rekening">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Rekening</label>
                        <select class="form-control" name="tipe" id="tipe" required>
                            <option value="KAS">KAS / TUNAI</option>
                            <option value="BANK">TRANSFER / BANK</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Rekening</label>
                        <input type="text" class="form-control" name="kode_rekening" id="kode_rekening" required placeholder="Contoh: KAS01, BNI-01">
                    </div>
                    <div class="form-group">
                        <label>Nama Rekening</label>
                        <input type="text" class="form-control" name="nama_rekening" id="nama_rekening" required placeholder="Contoh: Kas Utama, Rek. Operasional">
                    </div>
                    <div id="div-bank" style="display:none;">
                        <hr>
                        <div class="form-group">
                            <label>Nama Bank</label>
                            <input type="text" class="form-control" name="nama_bank" id="nama_bank" placeholder="Contoh: BNI, BRI, BSI">
                        </div>
                        <div class="form-group">
                            <label>Nomor Rekening</label>
                            <input type="text" class="form-control" name="nomor_rekening" id="nomor_rekening">
                        </div>
                        <div class="form-group">
                            <label>Atas Nama</label>
                            <input type="text" class="form-control" name="atas_nama" id="atas_nama">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Saldo Awal (Rp)</label>
                        <input type="number" class="form-control font-weight-bold" name="saldo_awal" id="saldo_awal" value="0">
                        <small class="text-danger">* Saldo awal hanya bisa diisi saat pembuatan rekening baru.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tipeSelect = document.getElementById('tipe');
    var divBank = document.getElementById('div-bank');
    
    if (tipeSelect) {
        tipeSelect.addEventListener('change', function() {
            divBank.style.display = (this.value === 'BANK') ? 'block' : 'none';
        });
    }

    // Modal submit via fetch
    const form = document.getElementById('form-rekening');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const btnSubmit = this.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            fetch('<?= BASE_URL ?>keuangan_master/save_rekening', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                    } else {
                        alert(data.message);
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
                console.error(err);
                if (typeof Swal !== 'undefined') Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                else alert('Terjadi kesalahan sistem');
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalText;
            });
        });
    }
});

function showAddModal() {
    document.getElementById('form-rekening').reset();
    document.getElementById('id_rekening').value = '';
    document.getElementById('modal-title').innerText = 'Tambah Rekening';
    document.getElementById('div-bank').style.display = 'none';
    document.getElementById('saldo_awal').readOnly = false;
    if (typeof $ !== 'undefined') $('#modal-rekening').modal('show');
}

function edit(data) {
    document.getElementById('modal-title').innerText = 'Edit Rekening';
    document.getElementById('id_rekening').value = data.id_rekening;
    document.getElementById('tipe').value = data.tipe;
    document.getElementById('kode_rekening').value = data.kode_rekening;
    document.getElementById('nama_rekening').value = data.nama_rekening;
    document.getElementById('nama_bank').value = data.nama_bank || '';
    document.getElementById('nomor_rekening').value = data.nomor_rekening || '';
    document.getElementById('atas_nama').value = data.atas_nama || '';
    document.getElementById('saldo_awal').value = parseInt(data.saldo_awal);
    document.getElementById('saldo_awal').readOnly = true;
    
    document.getElementById('div-bank').style.display = (data.tipe === 'BANK') ? 'block' : 'none';
    
    if (typeof $ !== 'undefined') $('#modal-rekening').modal('show');
}
</script>

<?php include '../app/views/partials/footer.php'; ?>
