<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-praying-hands"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kelola Pembiasaan Karakter &amp; Ibadah
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold rounded-pill px-3" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus mr-1"></i> Tambah Kegiatan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-success shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kegiatan</th>
                            <th>Pembina</th>
                            <th>Jadwal</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pembiasaan_list)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data kegiatan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pembiasaan_list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pembina'] ?? '-') ?></td>
                                    <td><?= $row['hari'] ? htmlspecialchars($row['hari']) . ' (' . DateHelper::formatWaktu($row['jam']) . ')' : '-' ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= BASE_URL ?>pembiasaan/index/<?= $row['id_pembiasaan'] ?>/program"
                                                class="btn btn-info" title="Kelola Program & Anggota">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <button type="button" class="btn btn-warning btn-edit"
                                                data-id="<?= $row['id_pembiasaan'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_kegiatan']) ?>"
                                                data-pembina="<?= $row['id_guru_pembina'] ?>" data-hari="<?= $row['hari'] ?>"
                                                data-jam="<?= $row['jam'] ?>"
                                                data-keterangan="<?= htmlspecialchars($row['keterangan'] ?? '') ?>"
                                                data-status="<?= $row['status'] ?>" title="Edit Data Kegiatan">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>pembiasaan/delete?id=<?= $row['id_pembiasaan'] ?>"
                                                class="btn btn-danger btn-delete-confirm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>pembiasaan/save" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kegiatan Pembiasaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" required
                            placeholder="Contoh: Sholat Dhuha">
                    </div>
                    <div class="form-group">
                        <label>Guru Pembina</label>
                        <select name="id_guru_pembina" class="form-control" required>
                            <option value="">-- Pilih Pembina --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari" class="form-control" required>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Ahad">Ahad</option>
                                    <option value="Setiap Hari">Setiap Hari</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam</label>
                                <input type="time" name="jam" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Non-Aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>pembiasaan/save" method="post">
                <input type="hidden" name="id_pembiasaan" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kegiatan Pembiasaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Guru Pembina</label>
                        <select name="id_guru_pembina" id="edit_pembina" class="form-control" required>
                            <option value="">-- Pilih Pembina --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari" id="edit_hari" class="form-control" required>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Ahad">Ahad</option>
                                    <option value="Setiap Hari">Setiap Hari</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam</label>
                                <input type="time" name="jam" id="edit_jam" class="form-control" step="60" required>
                                <small class="text-muted">Format 24 jam (contoh: 14:30)</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Non-Aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    $(function () {
        // Edit button handler
        $('.btn-edit').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            $('#edit_id').val(btn.attr('data-id'));
            $('#edit_nama').val(btn.attr('data-nama'));
            $('#edit_pembina').val(btn.attr('data-pembina'));
            $('#edit_hari').val(btn.attr('data-hari'));
            $('#edit_jam').val(btn.attr('data-jam'));
            $('#edit_keterangan').val(btn.attr('data-keterangan'));
            $('#edit_status').val(btn.attr('data-status'));
            $('#modalEdit').modal('show');
        });
    });
</script>