<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Transaksi Pengeluaran Kas
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold" onclick="showAddModal()">
                    <i class="fas fa-plus-circle mr-1"></i> Input Pengeluaran
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-danger shadow">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-arrow-up mr-1"></i> Riwayat Pengeluaran (5xxx)</h3>
                <div class="card-tools">
                    <form action="" method="get" class="form-inline">
                        <input type="hidden" name="mod" value="keuangan_transaksi_keluar">
                        <div class="input-group input-group-sm">
                            <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                value="<?= $_GET['tanggal_dari'] ?? date('Y-m-01') ?>">
                            <div class="input-group-append"><span class="input-group-text">-</span></div>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                value="<?= $_GET['tanggal_sampai'] ?? date('Y-m-t') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-filter"></i>
                                    Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped table-hover text-nowrap" id="table-transaksi"
                    style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th class="text-center">No Bukti</th>
                            <th>Tanggal</th>
                            <th width="10%" class="text-center">Kode</th>
                            <th>Jenis Pengeluaran</th>
                            <th>Penerima (Nama)</th>
                            <th>Sumber Dana</th>
                            <th>Jumlah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transaksi)): ?>
                            <?php $no = 1;
                            foreach ($transaksi as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="font-weight-bold text-danger"><?= $row['no_bukti'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-center font-weight-bold"><?= $row['kode_akun'] ?></td>
                                    <td>
                                        <span class="badge badge-warning text-white"><?= $row['nama_jenis'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['referensi']): ?>
                                            <div class="font-weight-bold"><i class="fas fa-user-tag text-muted"></i>
                                                <?= $row['referensi'] ?></div>
                                        <?php endif; ?>
                                        <small class="text-muted"><?= $row['keterangan'] ?></small>
                                        <?php if ($row['bukti_file']): ?>
                                            <br><a href="uploads/keuangan/<?= $row['bukti_file'] ?>" target="_blank"
                                                class="badge badge-secondary"><i class="fas fa-paperclip"></i> Bukti</a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row['nama_rekening'] ?></td>
                                    <td class="text-danger font-weight-bold text-right">Rp
                                        <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-default mb-1" title="Cetak"><i
                                                class="fas fa-print"></i></button>
                                        <button class="btn btn-xs btn-warning mb-1" title="Edit Transaksi"
                                            onclick='editTransaksi(<?= json_encode($row) ?>)'><i
                                                class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-3">Belum ada data periode ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- MODAL INPUT PENGELUARAN -->
<div class="modal fade" id="modal-transaksi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Form Pengeluaran Kas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="form-transaksi" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_transaksi">
                    <input type="hidden" name="tipe" value="KELUAR">
                    <input type="hidden" name="id_transaksi" id="id_transaksi">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Transaksi</label>
                                <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Akun Pengeluaran (COA)</label>
                                <select class="form-control select2" name="id_jenis" id="id_jenis" required
                                    style="width:100%">
                                    <option value="">-- Pilih Akun --</option>
                                    <?php
                                    $current_cat = "";
                                    foreach ($jenis_list as $j):
                                        if ($current_cat != $j['nama_kategori']) {
                                            if ($current_cat != "")
                                                echo "</optgroup>";
                                            echo "<optgroup label='" . $j['nama_kategori'] . "'>";
                                            $current_cat = $j['nama_kategori'];
                                        }
                                        ?>
                                        <option value="<?= $j['id_jenis'] ?>">
                                            [<?= $j['kode_akun'] ?>] <?= $j['nama_jenis'] ?>
                                        </option>
                                    <?php endforeach;
                                    if ($current_cat != "")
                                        echo "</optgroup>"; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Sumber Dana (Kredit)</label>
                                <select class="form-control" name="id_rekening" id="id_rekening" required>
                                    <?php foreach ($rekening_list as $r): ?>
                                        <option value="<?= $r['id_rekening'] ?>"><?= $r['nama_rekening'] ?> (Saldo:
                                            <?= number_format($r['saldo_akhir'], 0) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Upload Bukti (Nota/Kuitansi)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="bukti_file" id="bukti_file">
                                    <label class="custom-file-label" for="bukti_file">Pilih file...</label>
                                </div>
                                <small class="text-muted">Format: JPG, PNG, PDF. Maks 2MB.</small>
                            </div>
                        </div>

                        <div class="col-md-6 bg-light p-3 rounded">
                            <div class="form-group">
                                <label>Dibayarkan Kepada (Penerima)</label>
                                <input type="text" class="form-control" name="referensi" id="referensi"
                                    placeholder="Nama Toko / Guru / Staf / Instansi">
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Nominal (Rp)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control font-weight-bold form-control-lg"
                                        name="jumlah" id="jumlah" min="0" required placeholder="0">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Uraian / Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan" rows="3"
                                    placeholder="Contoh: Belanja ATK, Bayar Listrik..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('form-transaksi');
        var jumlahInput = document.getElementById('jumlah');

        // Init components
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modal-transaksi')
            });
        }

        // Custom file input
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (jumlahInput.value <= 0) {
                    alert('Jumlah harus lebih dari 0');
                    return;
                }

                var btn = form.querySelector('button[type="submit"]');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                var formData = new FormData(form);

                fetch('<?= BASE_URL ?>keuangan_keluar/save', {
                    method: 'POST',
                    body: formData
                })
                    .then(function (response) { return response.json(); })
                    .then(function (res) {
                        if (res.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                            } else {
                                alert('Berhasil: ' + res.message);
                                location.reload();
                            }
                        } else {
                            alert('Gagal: ' + res.message);
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        }
                    })
                    .catch(function (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan sistem.');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            });
        }
    });

    function showAddModal() {
        $('#form-transaksi')[0].reset();
        $('#id_transaksi').val('');
        $('#id_jenis').val('').trigger('change');
        $('.modal-title').html('<i class="fas fa-edit"></i> Form Pengeluaran Kas');
        if (typeof $ !== 'undefined') $('#modal-transaksi').modal('show');
    }

    function editTransaksi(data) {
        $('#form-transaksi')[0].reset();
        $('#id_transaksi').val(data.id_transaksi);
        $('input[name="tanggal"]').val(data.tanggal);
        $('#id_jenis').val(data.id_jenis).trigger('change');
        $('#id_rekening').val(data.id_rekening);
        $('#referensi').val(data.referensi); // Referensi = Penerima
        $('#jumlah').val(parseInt(data.jumlah));
        $('#keterangan').val(data.keterangan);

        $('.modal-title').html('<i class="fas fa-edit"></i> Edit Pengeluaran');
        $('#modal-transaksi').modal('show');
    }
</script>

<?php include '../app/views/partials/footer.php'; ?>