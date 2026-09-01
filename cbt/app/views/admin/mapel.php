<?php
$page_title = 'Kelola Mata Pelajaran';
require_once CBT_ROOT . '/app/views/partials/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-book text-success mr-2"></i>Kelola Mata Pelajaran</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus mr-1"></i> Tambah Mapel
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-file-import mr-1"></i> Impor Data
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item btn-import-simaks" href="#">Dari SIMAKS</a>
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
                            <th>Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mapel)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data mata pelajaran.</td>
                            </tr>
                        <?php else:
                            $no = 1;
                            foreach ($mapel as $m): ?>
                                <tr>
                                    <td class="text-center text-muted">
                                        <?= $no++ ?>
                                    </td>
                                    <td><code><?= htmlspecialchars($m['kode_mapel'] ?? '-') ?></code></td>
                                    <td><strong>
                                            <?= htmlspecialchars($m['nama_mapel']) ?>
                                        </strong></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $m['id_mapel'] ?>"
                                            data-nama="<?= $m['nama_mapel'] ?>" data-kode="<?= $m['kode_mapel'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $m['id_mapel'] ?>"
                                            data-nama="<?= htmlspecialchars($m['nama_mapel']) ?>">
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
        <form action="index.php?mod=kelola_mapel&act=save" method="POST">
            <input type="hidden" name="id_mapel" id="edit_id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Mapel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" id="edit_nama" class="form-control"
                            placeholder="Contoh: Matematika" required>
                    </div>
                    <div class="form-group">
                        <label>Kode Mapel (Opsional)</label>
                        <input type="text" name="kode_mapel" id="edit_kode" class="form-control"
                            placeholder="Contoh: MAT-X">
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

<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_mapel&act=import_excel" method="POST" enctype="multipart/form-data">
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
                        Format Excel: [Nama Mapel] | [Kode Mapel]
                    </div>
                    <a href="#" class="text-primary small"><i class="fas fa-download mr-1"></i> Download Template</a>
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
            Swal.fire({ icon: 'success', title: 'Sukses!', text: 'Data mapel berhasil disimpan.', timer: 2000, showConfirmButton: false });
        }
        if (urlParams.has('imported')) {
            Swal.fire({ icon: 'info', title: 'Berhasil!', text: 'Berhasil mengimpor ' + urlParams.get('imported') + ' mapel dari SIMAKS.', timer: 3000, showConfirmButton: false });
        }
        if (urlParams.has('del')) {
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Data mapel telah dihapus.', timer: 2000, showConfirmButton: false });
        }

        $('.btn-import-simaks').click(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Impor dari SIMAKS?',
                text: "Data mata pelajaran akan ditarik dari database utama SIMAKS.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Impor!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?mod=kelola_mapel&act=import_simaks';
                }
            });
        });

        $('.btn-delete').click(function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Mapel?',
                text: "Anda akan menghapus mata pelajaran " + nama + ". Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e94560',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?mod=kelola_mapel&act=delete&id=' + id;
                }
            });
        });

        $('.btn-edit').click(function () {
            $('#modalTitle').text('Edit Mapel');
            $('#edit_id').val($(this).data('id'));
            $('#edit_nama').val($(this).data('nama'));
            $('#edit_kode').val($(this).data('kode'));
            $('#modalTambah').modal('show');
        });

        $('#modalTambah').on('hidden.bs.modal', function () {
            $('#modalTitle').text('Tambah Mapel');
            $('#edit_id').val(0);
            $('#edit_nama').val('');
            $('#edit_kode').val('');
        });
    });
</script>

<?php require_once CBT_ROOT . '/app/views/partials/footer.php'; ?>