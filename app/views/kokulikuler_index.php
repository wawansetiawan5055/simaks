<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="fw-bold">Manajemen Kokurikuler</h1>
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
        <div class="card card-outline card-info shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover" id="tableKokul">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kegiatan</th>
                            <th>Tema</th>
                            <th>Koordinator</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kokul_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data kegiatan kokulikuler.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kokul_list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['tema'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nama_pembina'] ?? '-') ?></td>
                                    <td><?= $row['hari'] ? htmlspecialchars($row['hari']) . ' (' . substr($row['jam_mulai'], 0, 5) . '-' . substr($row['jam_selesai'], 0, 5) . ')' : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="index.php?mod=kokulikuler&id=<?= $row['id_kokulikuler'] ?>&tab=program" 
                                           class="btn btn-info btn-sm" title="Kelola">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" title="Edit" 
                                                onclick='editKokul(<?= json_encode($row) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="index.php?mod=kokulikuler&act=delete&id=<?= $row['id_kokulikuler'] ?>"
                                            class="btn btn-danger btn-sm" onclick="return confirmDelete(event)" title="Hapus">
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
<div class="modal fade" id="modalKokul" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?mod=kokulikuler&act=save" method="post" id="formKokul">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kokurikuler</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kokulikuler" id="id_kokulikuler">
                    
                    <div class="form-group">
                        <label>Nama Kegiatan (Sesuai Penugasan)</label>
                        <select name="id_penugasan" id="select_penugasan" class="form-control" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            <?php foreach ($assigned_activities_list as $act): ?>
                                <option value="<?= $act['id_penugasan_pembina'] ?>"
                                        data-guru-id="<?= $act['id_guru'] ?>"
                                        data-guru-nama="<?= htmlspecialchars($act['nama_guru']) ?>"
                                        data-nama-kegiatan="<?= htmlspecialchars($act['nama_kegiatan']) ?>">
                                    <?= htmlspecialchars($act['nama_kegiatan']) ?> (Pembina: <?= htmlspecialchars($act['nama_guru']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <small class="text-muted">Kegiatan diambil dari data Penugasan Guru.</small>
                    </div>

                    <div class="form-group">
                        <label>Tema Kegiatan (Projek P5)</label>
                        <input type="text" name="tema" id="tema" class="form-control" placeholder="Contoh: Gaya Hidup Berkelanjutan">
                    </div>

                    <div class="form-group">
                        <label>Koordinator (Guru)</label>
                        <input type="text" id="nama_pembina_display" class="form-control" readonly placeholder="Otomatis terisi...">
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

                    <hr>
                    <div class="form-group">
                        <label class="d-block mb-2">Target Profil Lulusan (Bisa pilih lebih dari satu)</label>
                        <div class="row">
                            <?php foreach ($profil_master as $p): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-checkbox border rounded p-2" style="background: #fcfcfc;">
                                        <input class="custom-control-input chk-profil" type="checkbox" 
                                               name="id_profil[]" id="profil_<?= $p['id_profil'] ?>" 
                                               value="<?= $p['id_profil'] ?>">
                                        <label for="profil_<?= $p['id_profil'] ?>" class="custom-control-label font-weight-normal" style="cursor: pointer;">
                                            <strong><?= htmlspecialchars($p['nama_dimensi']) ?></strong>
                                            <div class="text-xs text-muted" style="line-height: 1.2;"><?= htmlspecialchars($p['deskripsi']) ?></div>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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
    $(document).ready(function() {
        $('#tableKokul').DataTable({
             "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    function showAddModal() {
        $('#formKokul')[0].reset();
        $('#id_kokulikuler').val('');
        $('#id_guru_pembina').val('');
        $('#nama_pembina_display').val('');
        $('#select_penugasan').val('').trigger('change');
        $('#modalTitle').text('Tambah Kokurikuler');
        $('.chk-profil').prop('checked', false);
        $('#modalKokul').modal('show');
    }

    function editKokul(data) {
        $('#formKokul')[0].reset();
        $('#id_kokulikuler').val(data.id_kokulikuler);
        $('#modalTitle').text('Edit Kokurikuler');
        
        var found = false;
        $('#select_penugasan option').each(function() {
            var gId = $(this).data('guru-id');
            var name = $(this).data('nama-kegiatan');
            
            if(gId == data.id_guru_pembina && name == data.nama_kegiatan) {
                $('#select_penugasan').val($(this).val()).trigger('change');
                found = true;
                return false; 
            }
        });

        if(!found) {
             $('#nama_pembina_display').val(data.nama_pembina);
             $('#id_guru_pembina').val(data.id_guru_pembina);
        }

        $('#hari').val(data.hari);
        $('#tema').val(data.tema);
        $('#jam_mulai').val(data.jam_mulai);
        $('#jam_selesai').val(data.jam_selesai);
        $('#status').val(data.status);

        // Set Profil Lulusan
        $('.chk-profil').prop('checked', false);
        if (data.selected_profil) {
            data.selected_profil.forEach(function(id) {
                $('#profil_' + id).prop('checked', true);
            });
        }
        
        $('#modalKokul').modal('show');
    }

    $('#select_penugasan').change(function() {
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
