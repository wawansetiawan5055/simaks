<?php
// tahfidz_index.php
include __DIR__ . '/partials/header.php'; 
?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-quran"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kelola Program Tahfidz Qur'an
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold rounded-pill px-3" data-toggle="modal" data-target="#modalTahfidz">
                    <i class="fas fa-plus mr-1"></i> Tambah Kelompok
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header d-none">
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table table-hover table-striped" id="tableTahfidz">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="5%" class="align-middle">No</th>
                            <th class="align-middle">Nama Kegiatan</th>
                            <th class="align-middle">Kelompok</th>
                            <th class="align-middle">Level</th>
                            <th class="align-middle">Pembina</th>
                            <th class="align-middle">Jadwal</th>
                            <th class="align-middle">Target</th>
                            <th class="align-middle">Status</th>
                            <th width="15%" class="align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tahfidz_list)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data kelompok tahfidz.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tahfidz_list as $i => $row): ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $i + 1 ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($row['nama_kegiatan'] ?? 'Tahfidz Qur\'an') ?></td>
                                    <td class="fw-bold align-middle"><?= htmlspecialchars($row['nama_kelompok']) ?></td>
                                    <td class="align-middle text-center"><?= htmlspecialchars($row['tingkat'] ?? '-') ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($row['nama_guru'] ?? $row['nama_pembina'] ?? '-') ?></td>
                                    <td class="align-middle"><?= $row['hari'] ? htmlspecialchars($row['hari']) . ' (' . substr($row['jam'], 0, 5) . ')' : '-' ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($row['tingkat_target'] ?? '-') ?></td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-<?= $row['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= BASE_URL ?>tahfidz/index/<?= $row['id_tahfidz'] ?>/program" 
                                               class="btn btn-sm btn-info" title="Kelola Program & Penilaian">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <button type="button" class="btn btn-warning" 
                                                onclick="editTahfidz(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>)"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>tahfidz/delete?id=<?= $row['id_tahfidz'] ?>" 
                                               class="btn btn-danger" onclick="return confirmDelete(event)" title="Hapus">
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
</div>

<!-- Modal Tahfidz -->
<div class="modal fade" id="modalTahfidz" tabindex="-1" role="dialog" aria-labelledby="modalTahfidzLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>tahfidz/save" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTahfidzLabel">Tambah Kelompok Tahfidz</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_tahfidz" id="id_tahfidz">
                    
                    <div class="form-group">
                        <label for="nama_kegiatan">Nama Kegiatan</label>
                        <!-- Using datalist or select if preferred, but plain text for flexibility as requested -->
                        <select name="nama_kegiatan" id="nama_kegiatan" class="form-control select2" style="width: 100%;">
                             <option value="Tahfidz Qur'an">Tahfidz Qur'an</option>
                             <option value="Tahsin Qur'an">Tahsin Qur'an</option>
                             <?php
                             // Unique existing kegiatan names
                             $unique_kegiatan = array_unique(array_column($tahfidz_list ?? [], 'nama_kegiatan'));
                             foreach($unique_kegiatan as $uk) {
                                 if($uk && $uk != 'Tahfidz Qur\'an' && $uk != 'Tahsin Qur\'an') echo "<option value='$uk'>$uk</option>";
                             }
                             ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_kelompok">Nama Kelompok / Halaqah</label>
                        <input type="text" class="form-control" id="nama_kelompok" name="nama_kelompok" placeholder="Contoh: Kelompok A / Halaqah 1" required>
                    </div>

                    <div class="form-group">
                        <label for="tingkat">Tingkat / Level</label>
                        <select class="form-control" id="tingkat" name="tingkat">
                            <option value="">-- Pilih Level --</option>
                            <option value="Pemula">Pemula</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Lanjutan">Lanjutan</option>
                            <option value="Juz 30">Juz 30</option>
                            <option value="Juz 29">Juz 29</option>
                            <option value="Juz 1-5">Juz 1-5</option>
                             <!-- Add others as needed -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_guru_pembina">Pembimbing (Musyrif)</label>
                        <select name="id_guru_pembina" id="id_guru_pembina" class="form-control select2" style="width: 100%;">
                            <option value="">-- Pilih Pembina --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= $g['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="hari">Hari</label>
                                <select class="form-control" id="hari" name="hari">
                                    <option value="">-- Pilih Hari --</option>
                                    <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d): ?>
                                        <option value="<?= $d ?>"><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="jam">Jam</label>
                                <input type="time" class="form-control" id="jam" name="jam">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tingkat_target">Target Hafalan</label>
                         <input type="text" class="form-control" id="tingkat_target" name="tingkat_target" placeholder="Contoh: Juz 30 Selesai">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
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

<script>
    function editTahfidz(data) {
        $('#modalTahfidz .modal-title').text('Edit Kelompok Tahfidz');
        $('#modalTahfidz').modal('show');
        
        $('#id_tahfidz').val(data.id_tahfidz);
        $('#nama_kelompok').val(data.nama_kelompok);
        $('#tingkat').val(data.tingkat);
        $('#hari').val(data.hari);
        $('#jam').val(data.jam);
        $('#tingkat_target').val(data.tingkat_target);
        $('#status').val(data.status);

        // Select2 fields need trigger change
        if(data.nama_kegiatan) {
            $('#nama_kegiatan').val(data.nama_kegiatan).trigger('change');
        } else {
            $('#nama_kegiatan').val("Tahfidz Qur'an").trigger('change');
        }

        if(data.id_guru_pembina) {
            $('#id_guru_pembina').val(data.id_guru_pembina).trigger('change');
        }
    }

    $(document).ready(function() {
        $('#tableTahfidz').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });

        // Initialize Select2 in Modal
        $('#modalTahfidz').on('shown.bs.modal', function () {
            $('.select2').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modalTahfidz')
            });
        });

        // Auto-fill Pembina
        $('#nama_kelompok').on('change', function() {
            var selected = $(this).find(':selected');
            var guruId = selected.data('guru-id');
            var guruNama = selected.data('guru-nama');
            
            if(guruId) {
                $('#id_guru_pembina').val(guruId);
                $('#nama_pembina_display').val(guruNama);
            } else {
                
            }
        });
        
        // Modal Reset Logic
        $('#modalTahfidz').on('hidden.bs.modal', function () {
            var form = $(this).find('form')[0];
            form.reset();
            $(this).find('.modal-title').text('Tambah Kelompok Tahfidz');
            $('#id_tahfidz').val('');
            $('#nama_kegiatan').val("Tahfidz Qur'an").trigger('change');
            $('#nama_kelompok').val('');
            $('#tingkat').val('');
            $('#id_guru_pembina').val('').trigger('change');
        });
    });
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>
