<?php
$page_title = 'Kelola Siswa';
require_once CBT_ROOT . '/app/views/partials/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-graduate text-warning mr-2"></i>Kelola Siswa</h1>
            </div>
            <div class="col-sm-6 text-right d-flex justify-content-end align-items-center">
                <button class="btn btn-sm btn-primary mr-1" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-user-plus mr-1"></i> Tambah
                </button>
                <div class="btn-group mr-1">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-file-import mr-1"></i> Impor
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalImportSimaks">Dari
                            SIMAKS</a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalImportExcel">Dari Excel
                            (.xlsx)</a>
                    </div>
                </div>
                <button class="btn btn-sm btn-warning mr-1" data-toggle="modal" data-target="#modalBulkFoto">
                    <i class="fas fa-images mr-1"></i> Upload Foto
                </button>
                <button id="btnDeleteSelected" class="btn btn-sm btn-danger" style="display: none;"
                    onclick="deleteSelected()">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Filter Kelas -->
        <div class="card mb-3">
            <div class="card-body p-3">
                <form action="index.php" method="GET" class="form-inline">
                    <input type="hidden" name="mod" value="kelola_siswa">
                    <label class="mr-2">Filter Berdasarkan Kelas:</label>
                    <select name="id_kelas" class="form-control mr-3" onchange="this.form.submit()">
                        <option value="">-- Semua Siswa --</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= (isset($_GET['id_kelas']) && $_GET['id_kelas'] == $k['id_kelas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>



        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header py-2">
                <h3 class="card-title mt-1"><i class="fas fa-users mr-2"></i>Data Siswa</h3>
                <div class="card-tools">
                    <!-- Tombol dipindahkan ke header utama untuk kebersihan UI -->
                </div>
            </div>
            <div class="card-body p-0">
                <style>
                    .table-compact td,
                    .table-compact th {
                        padding: 0.35rem 0.5rem !important;
                        vertical-align: middle !important;
                        font-size: 0.9rem;
                    }

                    .btn-action-xs {
                        padding: 0.1rem 0.3rem !important;
                        font-size: 0.75rem !important;
                        line-height: 1.2;
                    }

                    .img-student-list {
                        width: 25px;
                        height: 25px;
                        object-fit: cover;
                    }
                </style>
                <table class="table table-sm table-striped table-hover mb-0 table-compact">
                    <thead>
                        <tr>
                            <th width="30" class="text-center">
                                <div class="custom-control custom-checkbox" style="padding-left: 1.5rem;">
                                    <input type="checkbox" class="custom-control-input" id="checkAllSiswa">
                                    <label class="custom-control-label" for="checkAllSiswa"></label>
                                </div>
                            </th>
                            <th width="40" class="text-center">#</th>
                            <th width="40">Foto</th>
                            <th width="120">NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>TTL</th>
                            <th width="110" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($siswa)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tidak ada data siswa ditemukan.</td>
                            </tr>
                        <?php else:
                            $no = 1;
                            foreach ($siswa as $s):
                                $foto = !empty($s['foto']) ? 'public/uploads/siswa/' . $s['foto'] : 'assets/img/user.png';
                                if (!file_exists(CBT_ROOT . '/' . $foto))
                                    $foto = 'assets/img/user.png';
                                ?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <div class="custom-control custom-checkbox" style="padding-left: 1.5rem;">
                                            <input type="checkbox" class="custom-control-input check-item"
                                                id="chk_<?= $s['id_siswa'] ?>" value="<?= $s['id_siswa'] ?>">
                                            <label class="custom-control-label" for="chk_<?= $s['id_siswa'] ?>"></label>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted align-middle"><?= $no++ ?></td>
                                    <td class="text-center align-middle">
                                        <img src="<?= $foto ?>" class="img-circle img-student-list elevation-1" alt="Foto">
                                    </td>
                                    <td class="align-middle"><code><?= htmlspecialchars($s['nisn']) ?></code></td>
                                    <td class="align-middle"><strong><?= htmlspecialchars($s['nama_siswa']) ?></strong></td>
                                    <td class="align-middle"><?= htmlspecialchars($s['nama_kelas'] ?? '-') ?></td>
                                    <td class="align-middle"><span
                                            class="badge badge-light border font-weight-normal"><?= htmlspecialchars($s['jurusan'] ?: '-') ?></span>
                                    </td>
                                    <td class="small align-middle text-muted">
                                        <?= htmlspecialchars($s['tempat_lahir'] ?: '-') ?>,
                                        <?= !empty($s['tanggal_lahir']) ? date('d/m/Y', strtotime($s['tanggal_lahir'])) : '-' ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <button class="btn btn-info btn-action-xs btn-reset" data-id="<?= $s['id_siswa'] ?>"
                                                title="Reset Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-warning btn-action-xs btn-edit"
                                                data-id="<?= $s['id_siswa'] ?>" data-nisn="<?= htmlspecialchars($s['nisn']) ?>"
                                                data-nipd="<?= htmlspecialchars($s['nipd'] ?? '') ?>"
                                                data-nama="<?= htmlspecialchars($s['nama_siswa']) ?>"
                                                data-kelas="<?= htmlspecialchars($s['id_kelas'] ?? '') ?>"
                                                data-jurusan="<?= htmlspecialchars($s['jurusan'] ?? '') ?>"
                                                data-tempat="<?= htmlspecialchars($s['tempat_lahir'] ?? '') ?>"
                                                data-tanggal="<?= htmlspecialchars($s['tanggal_lahir'] ?? '') ?>"
                                                data-no_peserta="<?= htmlspecialchars($s['no_peserta'] ?? '') ?>"
                                                data-ruang="<?= htmlspecialchars($s['ruang'] ?? '') ?>"
                                                data-sesi="<?= htmlspecialchars($s['sesi'] ?? '') ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-action-xs btn-delete-single"
                                                data-id="<?= $s['id_siswa'] ?>"
                                                data-nama="<?= htmlspecialchars($s['nama_siswa']) ?>" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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
    <div class="modal-dialog modal-lg" role="document">
        <form action="index.php?mod=kelola_siswa&act=save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_siswa" id="edit_id" value="0">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="modalTitle">Tambah Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold border-bottom pb-1 mb-3 text-primary">Biodata
                                Dasar</h6>
                            <div class="form-group row mb-2 align-items-center">
                                <label class="col-sm-4 col-form-label py-0">Foto</label>
                                <div class="col-sm-8">
                                    <input type="file" name="foto" class="form-control-file" accept="image/*">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">NISN</label>
                                <div class="col-sm-8">
                                    <input type="text" name="nisn" id="edit_nisn" class="form-control form-control-sm"
                                        required>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">NIPD</label>
                                <div class="col-sm-8">
                                    <input type="text" name="nipd" id="edit_nipd" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Nama Siswa</label>
                                <div class="col-sm-8">
                                    <input type="text" name="nama_siswa" id="edit_nama"
                                        class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Kelas</label>
                                <div class="col-sm-8">
                                    <select name="id_kelas" id="edit_kelas" class="form-control form-control-sm">
                                        <?php foreach ($kelas as $k): ?>
                                            <option value="<?= $k['id_kelas'] ?>">
                                                <?= htmlspecialchars($k['nama_kelas']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Jurusan</label>
                                <div class="col-sm-8">
                                    <input type="text" name="jurusan" id="edit_jurusan"
                                        class="form-control form-control-sm" placeholder="Contoh: RPL, TKJ">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Tpt. Lahir</label>
                                <div class="col-sm-8">
                                    <input type="text" name="tempat_lahir" id="edit_tempat"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Tgl. Lahir</label>
                                <div class="col-sm-8">
                                    <input type="date" name="tanggal_lahir" id="edit_tanggal"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 border-left">
                            <h6 class="font-weight-bold border-bottom pb-1 mb-3 text-primary">Data Ujian
                                (Opsional)</h6>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">No. Peserta</label>
                                <div class="col-sm-8">
                                    <input type="text" name="no_peserta" id="edit_no_peserta"
                                        class="form-control form-control-sm" placeholder="01-001-001-9">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Ruang</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ruang" id="edit_ruang" class="form-control form-control-sm"
                                        placeholder="LAB-01">
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-sm-4 col-form-label py-1">Sesi</label>
                                <div class="col-sm-8">
                                    <input type="text" name="sesi" id="edit_sesi" class="form-control form-control-sm"
                                        placeholder="1">
                                </div>
                            </div>
                            <div class="alert alert-light border small mt-4 py-2 px-3">
                                <i class="fas fa-info-circle mr-1 text-info"></i> Password default siswa
                                adalah
                                <strong>NISN</strong>.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Bulk Foto -->
<div class="modal fade" id="modalBulkFoto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_siswa&act=bulk_upload_foto" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-warning py-2">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-images mr-1"></i> Bulk Upload Foto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Pilih File ZIP Foto</label>
                        <div class="input-group">
                            <div class="custom-file border shadow-sm">
                                <input type="file" name="file_zip" class="custom-file-input" id="customFileZIP"
                                    accept=".zip" required>
                                <label class="custom-file-label" for="customFileZIP">Pilih file ZIP...</label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 px-3 small mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Instruksi:</strong>
                        <ul class="pl-3 mb-0 mt-1">
                            <li>File harus berformat <strong>.zip</strong>.</li>
                            <li>Nama file di dalam zip disarankan menggunakan <strong>NISN</strong> (contoh:
                                <code>1234567890.jpg</code>).
                            </li>
                            <li>Pastikan file foto berada langsung di dalam ZIP (bukan di dalam folder lagi).</li>
                            <li>Format yang didukung: <strong>JPG, JPEG, PNG</strong>.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning">Mulai Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import SIMAKS -->
<div class="modal fade" id="modalImportSimaks" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_siswa&act=import_simaks" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title">Import Siswa dari SIMAKS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-3">
                    <div class="form-group mb-0">
                        <label class="d-block mb-2">Pilih Kelas dari SIMAKS:</label>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="checkAllKelas">
                            <label class="custom-control-label font-weight-bold" for="checkAllKelas">Pilih Semua
                                Kelas</label>
                        </div>
                        <hr class="mt-1 mb-2">
                        <div
                            style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; padding: 12px; border-radius: 4px; background: #f8f9fa;">
                            <?php foreach ($kelas as $k): ?>
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" name="id_kelas[]" class="custom-control-input check-kelas"
                                        id="kelas_<?= $k['id_kelas'] ?>" value="<?= $k['id_kelas'] ?>">
                                    <label class="custom-control-label" for="kelas_<?= $k['id_kelas'] ?>">
                                        <?= htmlspecialchars($k['nama_kelas']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="small text-muted mt-2 mb-0"><i class="fas fa-info-circle mr-1"></i>
                            Data NISN, NIPD, dan TTL akan disinkronkan otomatis.</p>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Mulai Impor</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=kelola_siswa&act=import_excel" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title">Import dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Pilih File Excel (.xlsx)</label>
                        <div class="input-group">
                            <div class="custom-file border shadow-sm">
                                <input type="file" name="file_excel" class="custom-file-input" id="customFileExcel"
                                    accept=".xlsx" required>
                                <label class="custom-file-label" for="customFileExcel">Pilih file excel...</label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <p class="mb-1"><strong>Instruksi:</strong></p>
                        <ol class="pl-3 mb-2">
                            <li>Unduh template di bawah ini.</li>
                            <li>Isi data (NISN dan Nama wajib).</li>
                            <li>Format Tanggal: <code>YYYY-MM-DD</code>.</li>
                        </ol>
                        <a href="index.php?mod=kelola_siswa&act=download_template"
                            class="btn btn-white btn-block shadow-sm font-weight-bold mt-2" style="color: #0c5460;">
                            <i class="fas fa-download mr-1"></i> Unduh Template Siswa (.xlsx)
                        </a>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success">Upload & Proses</button>
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
            let msg = 'Operasi berhasil dilakukan.';
            if (urlParams.has('imported')) msg += ' ' + urlParams.get('imported') + ' data diimpor.';
            if (urlParams.has('bulk_foto')) msg += ' ' + urlParams.get('bulk_foto') + ' foto diupload.';
            Swal.fire({ icon: 'success', title: 'Sukses!', text: msg, timer: 3000, showConfirmButton: false });
        }
        if (urlParams.has('del')) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data siswa telah dihapus.', timer: 2000, showConfirmButton: false });
        }
        if (urlParams.has('err')) {
            let err = urlParams.get('err');
            let msg = 'Terjadi kesalahan sistem.';
            if (err === 'empty') msg = 'Data wajib (NISN/Nama) tidak boleh kosong.';
            else if (err === 'excel') msg = 'Gagal memproses file Excel.';
            else if (err === 'zip') msg = 'Gagal membuka file ZIP.';
            Swal.fire({ icon: 'error', title: 'Kesalahan!', text: msg });
        }

        // --- Siswa Actions ---
        $('.btn-delete-single').click(function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            Swal.fire({
                title: 'Hapus Siswa?',
                text: "Anda akan menghapus " + nama + ". Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e94560',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?mod=kelola_siswa&act=delete&id=' + id;
                }
            });
        });

        $('.btn-edit').click(function () {
            $('#modalTitle').text('Edit Siswa');
            $('#edit_id').val($(this).data('id'));
            $('#edit_nisn').val($(this).data('nisn'));
            $('#edit_nipd').val($(this).data('nipd'));
            $('#edit_nama').val($(this).data('nama'));
            $('#edit_kelas').val($(this).data('kelas'));
            $('#edit_jurusan').val($(this).data('jurusan'));
            $('#edit_tempat').val($(this).data('tempat'));
            $('#edit_tanggal').val($(this).data('tanggal'));
            $('#edit_no_peserta').val($(this).data('no_peserta'));
            $('#edit_ruang').val($(this).data('ruang'));
            $('#edit_sesi').val($(this).data('sesi'));
            $('#modalTambah').modal('show');
        });

        $('.btn-reset').click(function () {
            if (confirm('Reset password siswa ini kembali ke NISN?')) {
                window.location.href = 'index.php?mod=kelola_siswa&act=reset_password&id=' + $(this).data('id');
            }
        });

        $('#modalTambah').on('hidden.bs.modal', function () {
            $('#modalTitle').text('Tambah Siswa');
            $('#edit_id').val(0);
            $('#edit_nisn').val('');
            $('#edit_nipd').val('');
            $('#edit_nama').val('');
            $('#edit_jurusan').val('');
            $('#edit_tempat').val('');
            $('#edit_tanggal').val('');
            $('#edit_no_peserta').val('');
            $('#edit_ruang').val('');
            $('#edit_sesi').val('');
        });

        // --- Multi-Select & Delete ---
        $('#checkAllSiswa').change(function () {
            $('.check-item').prop('checked', $(this).prop('checked'));
            toggleDeleteButton();
        });

        $('.check-item').change(function () {
            if ($('.check-item:checked').length === $('.check-item').length) {
                $('#checkAllSiswa').prop('checked', true);
            } else {
                $('#checkAllSiswa').prop('checked', false);
            }
            toggleDeleteButton();
        });

        function toggleDeleteButton() {
            var count = $('.check-item:checked').length;
            if (count > 0) {
                $('#btnDeleteSelected').show();
                $('#selectedCount').text(count);
            } else {
                $('#btnDeleteSelected').hide();
            }
        }

        window.deleteSelected = function () {
            var ids = [];
            $('.check-item:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                Swal.fire({
                    title: 'Hapus Terpilih?',
                    text: "Anda akan menghapus " + ids.length + " siswa terpilih. Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e94560',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('<form action="index.php?mod=kelola_siswa&act=delete_selected" method="POST"></form>');
                        $.each(ids, function (i, id) {
                            form.append('<input type="hidden" name="ids[]" value="' + id + '">');
                        });
                        $('body').append(form);
                        form.submit();
                    }
                });
            }
        };

        // --- Import Modals ---
        $('#checkAllKelas').change(function () {
            $('.check-kelas').prop('checked', $(this).prop('checked'));
        });

        $('.check-kelas').change(function () {
            if ($('.check-kelas:checked').length === $('.check-kelas').length) {
                $('#checkAllKelas').prop('checked', true);
            } else {
                $('#checkAllKelas').prop('checked', false);
            }
        });
        // --- Custom File Input Handler ---
        $('.custom-file-input').on('change', func tion () {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>

<?php require_once CBT_ROOT . '/app/views/partials/footer.php'; ?>