<?php
/**
 * View: Bank Soal
 */
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-layer-group text-primary mr-2"></i>Kelola Bank Soal</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus mr-1"></i> Tambah Bank Soal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-list mr-2"></i>Daftar Wadah Soal</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped table-hover mb-0">
                    <thead class="bg-light text-uppercase font-size-12">
                        <tr>
                            <th width="50" class="text-center py-2">#</th>
                            <th class="py-2">Info Bank Soal</th>
                            <th class="py-2">Mata Pelajaran</th>
                            <th class="text-center py-2">Tingkat / Jurusan</th>
                            <th class="text-center py-2">Opsi (PG)</th>
                            <th class="text-right py-2">Target (PG/E)</th>
                            <th width="180" class="text-center py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($banks)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="fas fa-folder-open fa-3x opacity-25"></i></div>
                                    Belum ada data bank soal.
                                </td>
                            </tr>
                        <?php else:
                            $no = 1;
                            foreach ($banks as $b): ?>
                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        <?= $no++ ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-primary">
                                            <?= htmlspecialchars($b['nama_bank']) ?>
                                        </div>
                                        <div class="small">
                                            <span
                                                class="badge badge-secondary"><?= htmlspecialchars($b['kode_bank'] ?: '-') ?></span>
                                            <span class="text-muted ml-2 font-size-11"><i class="far fa-calendar-alt mr-1"></i>
                                                <?= date('d/m/y H:i', strtotime($b['created_at'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark"><?= htmlspecialchars($b['nama_mapel']) ?></div>
                                        <div class="small text-muted italic"><?= htmlspecialchars($b['deskripsi'] ?: '-') ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="font-weight-bold"><?= htmlspecialchars($b['tingkat'] ?: '-') ?></div>
                                        <div class="small text-muted">
                                            <?= htmlspecialchars($b['id_jurusan'] ?: 'Semua Jurusan') ?></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-pill badge-info px-3"><?= $b['opsi_pg'] ?> Opsi</span>
                                    </td>
                                    <td class="text-right align-middle">
                                        <div class="small">PG: <b><?= $b['jumlah_soal'] ?></b> / <?= $b['jml_pg'] ?></div>
                                        <div class="small text-muted">Esai: <?= $b['jml_esai'] ?></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="index.php?mod=input_soal&id_bank=<?= $b['id_bank'] ?>"
                                                class="btn btn-sm btn-info shadow-sm" title="Kelola Soal">
                                                <i class="fas fa-tasks mr-1"></i> Soal
                                            </a>
                                            <button class="btn btn-sm btn-warning shadow-sm btn-edit"
                                                data-id="<?= $b['id_bank'] ?>"
                                                data-nama="<?= htmlspecialchars($b['nama_bank']) ?>"
                                                data-kode="<?= htmlspecialchars($b['kode_bank']) ?>"
                                                data-mapel="<?= $b['id_mapel'] ?>"
                                                data-tingkat="<?= htmlspecialchars($b['tingkat']) ?>"
                                                data-jurusan="<?= htmlspecialchars($b['id_jurusan']) ?>"
                                                data-opsi="<?= $b['opsi_pg'] ?>" data-jmlpg="<?= $b['jml_pg'] ?>"
                                                data-bobotpg="<?= $b['bobot_pg'] ?>" data-jmlesai="<?= $b['jml_esai'] ?>"
                                                data-bobotesai="<?= $b['bobot_esai'] ?>"
                                                data-deskripsi="<?= htmlspecialchars($b['deskripsi']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger shadow-sm btn-delete text-white"
                                                data-id="<?= $b['id_bank'] ?>"
                                                data-nama="<?= htmlspecialchars($b['nama_bank']) ?>">
                                                <i class="fas fa-trash text-white"></i>
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
    <div class="modal-dialog" role="document">
        <form action="index.php?mod=bank_soal&act=save_bank" method="POST">
            <input type="hidden" name="id_bank" id="edit_id" value="0">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus-circle mr-2"></i>Tambah Bank Soal</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1 font-size-14">Nama Bank Soal <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama_bank" id="edit_nama" class="form-control"
                                    placeholder="Contoh: PAS Ganjil 2024" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1 font-size-14">Kode Bank</label>
                                <input type="text" name="kode_bank" id="edit_kode" class="form-control"
                                    placeholder="KODE-01">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1 font-size-14">Mata Pelajaran <span
                                        class="text-danger">*</span></label>
                                <select name="id_mapel" id="edit_mapel" class="form-control" required>
                                    <option value="">-- Pilih Mapel --</option>
                                    <?php foreach ($mapel_list as $m): ?>
                                        <option value="<?= $m['id_mapel'] ?>"><?= htmlspecialchars($m['nama_mapel']) ?>
                                            (<?= $m['kode_mapel'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1 font-size-14">Tingkat Kelas</label>
                                <select name="tingkat" id="edit_tingkat" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="X">Kelas X</option>
                                    <option value="XI">Kelas XI</option>
                                    <option value="XII">Kelas XII</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1 font-size-14">Jurusan</label>
                                <input type="text" name="id_jurusan" id="edit_jurusan" class="form-control"
                                    placeholder="IPA/IPS/Semua">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold mb-1 font-size-14">Opsi PG</label>
                                <select name="opsi_pg" id="edit_opsi" class="form-control">
                                    <option value="5">5 Opsi (A-E)</option>
                                    <option value="4">4 Opsi (A-D)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="small font-weight-bold text-muted text-uppercase mb-2"><i
                                class="fas fa-calculator mr-1"></i> Target & Bobot (Default Paket)</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="small mb-1">Jml PG</label>
                                    <input type="number" name="jml_pg" id="edit_jmlpg"
                                        class="form-control form-control-sm" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="small mb-1">Bobot PG</label>
                                    <input type="number" step="0.01" name="bobot_pg" id="edit_bobotpg"
                                        class="form-control form-control-sm" value="1.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="small mb-1">Jml Esai</label>
                                    <input type="number" name="jml_esai" id="edit_jmlesai"
                                        class="form-control form-control-sm" value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="small mb-1">Bobot Esai</label>
                                    <input type="number" step="0.01" name="bobot_esai" id="edit_bobotesai"
                                        class="form-control form-control-sm" value="1.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold mb-1 font-size-14">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"
                            placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Edit Button Handler
        $(document).on('click', '.btn-edit', function () {
            const data = $(this).data();
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Bank Soal');
            $('#edit_id').val(data.id);
            $('#edit_nama').val(data.nama);
            $('#edit_kode').val(data.kode);
            $('#edit_mapel').val(data.mapel);
            $('#edit_tingkat').val(data.tingkat);
            $('#edit_jurusan').val(data.jurusan);
            $('#edit_opsi').val(data.opsi);
            $('#edit_jmlpg').val(data.jmlpg);
            $('#edit_bobotpg').val(data.bobotpg);
            $('#edit_jmlesai').val(data.jmlesai);
            $('#edit_bobotesai').val(data.bobotesai);
            $('#edit_deskripsi').val(data.deskripsi);
            $('#modalTambah').modal('show');
        });

        // Reset Modal on hide
        $('#modalTambah').on('hidden.bs.modal', function () {
            $('#modalTitle').html('<i class="fas fa-plus-circle mr-2"></i>Tambah Bank Soal');
            $('#edit_id').val(0);
            $('#edit_nama').val('');
            $('#edit_kode').val('');
            $('#edit_mapel').val('');
            $('#edit_tingkat').val('');
            $('#edit_jurusan').val('');
            $('#edit_opsi').val(5);
            $('#edit_jmlpg').val(0);
            $('#edit_bobotpg').val('1.00');
            $('#edit_jmlesai').val(0);
            $('#edit_bobotesai').val('1.00');
            $('#edit_deskripsi').val('');
        });

        // Delete Confirmation
        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Bank Soal?',
                text: "Seluruh soal di dalam bank soal [" + nama + "] akan ikut terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?mod=bank_soal&act=delete_bank&id=' + id;
                }
            });
        });

        // Notifications from URL Params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('ok')) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data bank soal telah disimpan.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.has('del')) {
            Swal.fire({ icon: 'success', title: 'Terhapus!', text: 'Bank soal telah berhasil dihapus.', timer: 2000, showConfirmButton: false });
        } else if (urlParams.has('err')) {
            const errType = urlParams.get('err');
            let errMsg = 'Terjadi kesalahan sistem.';
            if (errType === 'empty') errMsg = 'Pastikan seluruh input wajib terisi.';
            Swal.fire({ icon: 'error', title: 'Gagal!', text: errMsg });
        }
    });
</script>