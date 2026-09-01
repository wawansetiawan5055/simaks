<?php
$page_title = 'Kelola Kelas';
require_once CBT_ROOT . '/app/views/partials/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-door-open text-primary mr-2"></i>Kelola Kelas</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus mr-1"></i> Tambah Kelas
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-file-import mr-1"></i> Impor Data
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalImportSimaks">Dari
                                SIMAKS</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalImportExcel">Dari
                                Excel (.xlsx)</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">


        <div class="card">
            <div class="card-body p-0">
                <table class="table table-sm table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Jurusan</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kelas)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data kelas.</td>
                            </tr>
                        <?php else:
                            $no = 1;
                            foreach ($kelas as $k): ?>
                                <tr>
                                    <td class="text-center text-muted">
                                        <?= $no++ ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($k['nama_kelas']) ?></strong></td>
                                    <td><span class="badge badge-info"><?= htmlspecialchars($k['tingkat']) ?></span></td>
                                    <td><?= htmlspecialchars($k['jurusan'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $k['id_kelas'] ?>"
                                            data-nama="<?= $k['nama_kelas'] ?>" data-tingkat="<?= $k['tingkat'] ?>"
                                            data-jurusan="<?= $k['jurusan'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $k['id_kelas'] ?>"
                                            data-nama="<?= htmlspecialchars($k['nama_kelas']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_kelas&act=save" method="POST">
            <input type="hidden" name="id_kelas" id="edit_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama" class="form-control"
                            placeholder="Contoh: X RPL 1" required>
                    </div>
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat" id="edit_tingkat" class="form-control">
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jurusan <span class="text-muted small">(Opsional)</span></label>
                        <input type="text" name="jurusan" id="edit_jurusan" class="form-control"
                            placeholder="Contoh: RPL, TKJ, IPA, IPS">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import SIMAKS -->
<div class="modal fade" id="modalImportSimaks" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_kelas&act=import_simaks" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Import Kelas dari SIMAKS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="d-block">Pilih Kelas:</label>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="checkAllSimaksKelas">
                            <label class="custom-control-label font-weight-bold" for="checkAllSimaksKelas">Pilih Semua
                                Kelas</label>
                        </div>
                        <hr class="mt-1 mb-2">
                        <div
                            style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                            <?php foreach ($kelas_simaks as $ks): ?>
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" name="id_kelas[]" class="custom-control-input check-simaks-kelas"
                                        id="simaks_kelas_<?= $ks['id_kelas'] ?>" value="<?= $ks['id_kelas'] ?>">
                                    <label class="custom-control-label" for="simaks_kelas_<?= $ks['id_kelas'] ?>">
                                        <?= htmlspecialchars($ks['nama_kelas']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Impor</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_kelas&act=import_excel" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Import dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih File Excel (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                    </div>
                    <div class="alert alert-info py-2 px-3 small">
                        Format Excel: [Nama Kelas] | [Tingkat] | [Jurusan]
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload & Proses</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        // --- SweetAlert2 Notifications ---
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('ok')) {
            Swal.fire({ icon: 'success', title: 'Sukses!', text: 'Data kelas berhasil disimpan.', timer: 2000, showConfirmButton: false });
        }
        if (urlParams.has('imported')) {
            Swal.fire({ icon: 'info', title: 'Berhasil!', text: 'Berhasil mengimpor ' + urlParams.get('imported') + ' kelas dari SIMAKS.', timer: 3000, showConfirmButton: false });
        }
        if (urlParams.has('del')) {
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data kelas telah dihapus.', timer: 2000, showConfirmButton: false });
        }

        $('.btn-delete').click(function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Kelas?',
                text: "Anda akan menghapus kelas " + nama + ". Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e94560',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?mod=kelola_kelas&act=delete&id=' + id;
                }
            });
        });

        $('.btn-edit').click(function () {
            $('#modalTitle').text('Edit Kelas');
            $('#edit_id').val($(this).data('id'));
            $('#edit_nama').val($(this).data('nama'));
            $('#edit_tingkat').val($(this).data('tingkat'));
            $('#edit_jurusan').val($(this).data('jurusan'));
            $('#modalTambah').modal('show');
        });

        $('#modalTambah').on('hidden.bs.modal', function () {
            $('#modalTitle').text('Tambah Kelas');
            $('#edit_id').val(0);
            $('#edit_nama').val('');
            $('#edit_jurusan').val('');
        });

        // Handle Select All Kelas SIMAKS
        $('#checkAllSimaksKelas').change(function () {
            $('.check-simaks-kelas').prop('checked', $(this).prop('checked'));
        });

        $('.check-simaks-kelas').change(function () {
            if ($('.check-simaks-kelas:checked').length === $('.check-simaks-kelas').length) {
                $('#checkAllSimaksKelas').prop('checked', true);
            } else {
                $('#checkAllSimaksKelas').prop('checked', false);
            }
        });
    });
</script>

<?php require_once CBT_ROOT . '/app/views/partials/footer.php'; ?>