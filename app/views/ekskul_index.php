<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-running mr-2"></i> Manajemen Ekstrakurikuler</h1>
            </div>
            <div class="col-sm-6 text-end">
                <div class="float-sm-right">
                    <button type="button" class="btn btn-primary" onclick="showAddModal()">
                        <i class="fas fa-plus"></i> Tambah Kegiatan
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover" id="tableEkskul">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kegiatan</th>
                            <th>Pembina</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th width="15%" class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ekskul_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data ekstrakurikuler.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ekskul_list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_ekskul']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pembina'] ?? '-') ?></td>
                                    <td><?= $row['hari'] ? htmlspecialchars($row['hari']) . ' (' . substr($row['jam_mulai'], 0, 5) . '-' . substr($row['jam_selesai'], 0, 5) . ')' : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="index.php?mod=ekskul&id=<?= $row['id_ekskul'] ?>&tab=program"
                                            class="btn btn-sm btn-info" title="Kelola">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" title="Edit"
                                            onclick='editEkskul(<?= json_encode($row) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="index.php?mod=ekskul&act=delete&id=<?= $row['id_ekskul'] ?>"
                                            class="btn btn-sm btn-danger" onclick="return confirmDelete(event)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
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

<!-- MODAL TAMBAH/EDIT -->
<div class="modal fade" id="modalEkskul" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?mod=ekskul&act=save" method="post" id="formEkskul">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Ekskul</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_ekskul" id="id_ekskul">

                    <div class="form-group">
                        <label>Nama Kegiatan (Sesuai Penugasan)</label>
                        <select name="id_penugasan" id="select_penugasan" class="form-control" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            <?php foreach ($assigned_activities_list as $act): ?>
                                <option value="<?= $act['id_penugasan_pembina'] ?>" data-guru-id="<?= $act['id_guru'] ?>"
                                    data-guru-nama="<?= htmlspecialchars($act['nama_guru']) ?>"
                                    data-nama-kegiatan="<?= htmlspecialchars($act['nama_kegiatan']) ?>">
                                    <?= htmlspecialchars($act['nama_kegiatan']) ?> (Pembina:
                                    <?= htmlspecialchars($act['nama_guru']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Kegiatan diambil dari data Penugasan Guru.</small>
                    </div>

                    <div class="form-group">
                        <label>Pembina (Guru)</label>
                        <input type="text" id="nama_pembina_display" class="form-control" readonly
                            placeholder="Otomatis terisi...">
                        <input type="hidden" name="id_guru_pembina" id="id_guru_pembina">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari" id="hari" class="form-control">
                                    <option value="">-- Pilih Hari --</option>
                                    <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h): ?>
                                        <option value="<?= $h ?>"><?= $h ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
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
    $(document).ready(function () {
        $('#tableEkskul').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    function showAddModal() {
        $('#formEkskul')[0].reset();
        $('#id_ekskul').val('');
        $('#id_guru_pembina').val('');
        $('#nama_pembina_display').val('');
        $('#select_penugasan').val('').trigger('change');
        $('#modalTitle').text('Tambah Ekskul');
        $('#modalEkskul').modal('show');
    }

    function editEkskul(data) {
        $('#formEkskul')[0].reset();
        $('#id_ekskul').val(data.id_ekskul);
        $('#modalTitle').text('Edit Ekskul');

        // Match Penugasan by Name & Guru ID (Legacy data mapping)
        var found = false;
        $('#select_penugasan option').each(function () {
            var gId = $(this).data('guru-id');
            var name = $(this).data('nama-kegiatan');

            // Loose comparison
            if (gId == data.id_guru_pembina && name == data.nama_ekskul) {
                $('#select_penugasan').val($(this).val()).trigger('change');
                found = true;
                return false;
            }
        });

        if (!found) {
            // If not found in dropdown list (e.g. inactive assignment), set display field manually
            // But we can't select a dropdown item.
            $('#nama_pembina_display').val(data.nama_pembina);
            $('#id_guru_pembina').val(data.id_guru_pembina);
        }

        $('#hari').val(data.hari);
        $('#jam_mulai').val(data.jam_mulai);
        $('#jam_selesai').val(data.jam_selesai);
        $('#status').val(data.status);

        $('#modalEkskul').modal('show');
    }

    $('#select_penugasan').change(function () {
        var selected = $(this).find('option:selected');
        var guruNama = selected.data('guru-nama');
        var guruId = selected.data('guru-id');

        if (guruNama) {
            $('#nama_pembina_display').val(guruNama);
            $('#id_guru_pembina').val(guruId);
        } else {
            $('#nama_pembina_display').val('');
            $('#id_guru_pembina').val('');
        }
    });
</script>