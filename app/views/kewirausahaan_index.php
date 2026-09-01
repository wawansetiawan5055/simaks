<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Kelola Kegiatan Kewirausahaan
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold rounded-pill px-3" onclick="showAddModal()">
                    <i class="fas fa-plus mr-1"></i> Tambah Kegiatan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

<?php
// kewirausahaan_index.php
?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase me-2"></i> Daftar Kegiatan Kewirausahaan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showAddModal()">
                            <i class="fas fa-plus"></i> Tambah Kegiatan
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-3">
                <table class="table table-hover table-striped" id="tableKewirausahaan">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kegiatan</th>
                            <th>Kelompok</th>
                            <th>Pembina</th>
                            <th>Progress</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kewirausahaan_list)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data kegiatan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kewirausahaan_list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['kelompok'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nama_pembina'] ?? '-') ?></td>
                                    <td>
                                        <?php 
                                        $total = $row['total_tahapan'] ?? 0;
                                        $selesai = $row['selesai_tahapan'] ?? 0;
                                        $percent = $total > 0 ? round(($selesai / $total) * 100) : 0;
                                        ?>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-success" style="width: <?= $percent ?>%"></div>
                                        </div>
                                        <small><?= $selesai ?>/<?= $total ?> Selesai</small>
                                    </td>
                                    <td><?= $row['hari'] ? htmlspecialchars($row['hari']) . ' (' . substr($row['jam'], 0, 5) . ')' : '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="<?= BASE_URL ?>kewirausahaan/index/<?= $row['id_kewirausahaan'] ?>/program" 
                                           class="btn btn-sm btn-info text-white" title="Kelola Program & Penilaian">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" title="Edit" 
                                                onclick='editKewirausahaan(<?= json_encode($row) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= BASE_URL ?>kewirausahaan/delete?id=<?= $row['id_kewirausahaan'] ?>" 
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
</div>
<script>
    $(document).ready(function() {
        $('#tableKewirausahaan').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    function showAddModal() {
        $('#formKewirausahaan')[0].reset();
        $('#id_kewirausahaan').val('');
        $('#id_guru_pembina').val('');
        $('#nama_pembina_display').val('');
        $('#select_penugasan').val('').trigger('change'); 
        $('#modalTitle').text('Tambah Kewirausahaan');
        $('#modalKewirausahaan').modal('show');
    }

    function editKewirausahaan(data) {
        $('#formKewirausahaan')[0].reset();
        $('#id_kewirausahaan').val(data.id_kewirausahaan);
        $('#modalTitle').text('Edit Kewirausahaan');
        
        // Attempt to select the correct assignment by Name and Guru ID
        // Since we now use id_penugasan as value, we need to find the option that matches the data
        var found = false;
        $('#select_penugasan option').each(function() {
            var gId = $(this).data('guru-id');
            var name = $(this).data('nama-kegiatan');
            
            // Loose comparison for data coming from mixed types (int/string)
            if(gId == data.id_guru_pembina && name == data.nama_kegiatan) {
                $('#select_penugasan').val($(this).val()).trigger('change');
                found = true;
                return false; // break
            }
        });

        if(!found) {
             // If not found in dropdown (legacy data?), manually set display
             $('#nama_pembina_display').val(data.nama_pembina);
             // We can't easily set the dropdown if it doesn't exist, but we assume it does for active assignment
        }

        $('#kelompok').val(data.kelompok);
        $('#hari').val(data.hari);
        $('#jam').val(data.jam);
        $('#keterangan').val(data.keterangan);
        $('#status').val(data.status);
        
        $('#modalKewirausahaan').modal('show');
    }

    // Update Display on Change
    $('#select_penugasan').change(function() {
        var selected = $(this).find('option:selected');
        var guruNama = selected.data('guru-nama');
        
        if (guruNama) {
            $('#nama_pembina_display').val(guruNama);
        } else {
             $('#nama_pembina_display').val('');
        }
    });

    // Simple validation
    $('#formKewirausahaan').on('submit', function(e) {
        // Backend handles lookup, we just ensure a valid option is picked if it's a new entry
        // For old entries, id_penugasan might be empty if we didn't match it, 
        // but 'save' method logic should handle updates gracefully or we might need hidden fallbacks.
        // Actually, for this fix, we prioritize the new flow.
        var penugasan = $('#select_penugasan').val();
         if (!penugasan && !$('#id_kewirausahaan').val()) { // Only force for new data
            e.preventDefault();
            alert("Silakan pilih kegiatan.");
        }
    });
</script>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal fade" id="modalKewirausahaan" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kewirausahaan/save" method="post" id="formKewirausahaan">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kewirausahaan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" id="id_kewirausahaan">
                    <!-- REMOVED Duplicate/Separate hidden nama_kegiatan. The SELECT below handles it directly, mirroring Ekskul. -->
                    
                    <div class="form-group">
                        <label>Nama Kegiatan (Sesuai Penugasan)</label>
                        <!-- CHANGED: Use id_penugasan as the primary value carrier -->
                        <select name="id_penugasan" id="select_penugasan" class="form-control" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            <?php foreach ($assigned_activities_list as $act): ?>
                                <!-- VALUE is the Unique Assignment ID. -->
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
                        <label>Kelompok / Kategori</label>
                        <input type="text" name="kelompok" id="kelompok" class="form-control" list="listKelompok" placeholder="Contoh: Tata Boga, Agency, Pertanian...">
                        <datalist id="listKelompok">
                            <option value="Tata Boga">
                            <option value="Agency">
                            <option value="Pertanian">
                            <option value="Kerajinan">
                            <option value="Teknologi">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label>Pembina (Guru)</label>
                        <input type="text" id="nama_pembina_display" class="form-control" readonly placeholder="Otomatis terisi...">
                        <!-- Primary Hidden Input for Pembina ID -->
                        <input type="hidden" name="id_guru_pembina" id="id_guru_pembina">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari" id="hari" class="form-control" required>
                                    <option value="">-- Pilih Hari --</option>
                                    <?php
                                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                    foreach ($days as $day) {
                                        echo "<option value=\"$day\">$day</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jam</label>
                                <input type="time" name="jam" id="jam" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2"></textarea>
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

    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
