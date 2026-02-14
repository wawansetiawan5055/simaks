<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 px-4">
        <div>
            <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-calendar-alt text-primary mr-2"></i> Jadwal Pelajaran</h2>
            <p class="text-muted small mb-0">Kelola jadwal pelajaran sekolah, guru, dan kelas secara terpusat.</p>
        </div>
    </div>
  </div>
</div>
<section class="content">
<div class="container-fluid">

    <!-- Alerts handled by toast -->

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-3">
            <div class="card-body py-2 px-3">
                <form method="GET" class="row align-items-center">
                    <input type="hidden" name="mod" value="jadwal">
                    <div class="col-auto">
                        <h6 class="mb-0 font-weight-bold text-muted mr-3"><i class="fas fa-filter mr-1"></i> FILTER:</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0">Tipe</span>
                            </div>
                            <select name="view" class="form-control form-control-sm border-0 bg-light" onchange="this.form.submit()">
                                <option value="sekolah" <?= ($view_type == 'sekolah') ? 'selected' : ''; ?>>Jadwal Sekolah</option>
                                <option value="guru" <?= ($view_type == 'guru') ? 'selected' : ''; ?>>Per Guru Mapel</option>
                                <option value="kelas" <?= ($view_type == 'kelas') ? 'selected' : ''; ?>>Per Kelas</option>
                            </select>
                        </div>
                    </div>
                    <?php if ($view_type == 'guru'): ?>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <select name="id_guru_mapel" class="form-control form-control-sm border-0 bg-light" onchange="this.form.submit()">
                                    <option value="">-- Pilih Guru Mapel --</option>
                                    <?php foreach ($guru_mapel_list as $g): ?>
                                        <option value="<?= $g['id_guru_mapel'] ?>" <?= ($id_guru_mapel_filter == $g['id_guru_mapel']) ? 'selected' : ''; ?>>
                                            <?= $g['nama_guru'] ?> - <?= $g['nama_mapel'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php elseif ($view_type == 'kelas'): ?>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <select name="id_kelas" class="form-control form-control-sm border-0 bg-light" onchange="this.form.submit()">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas_list as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($k['nama_kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH JADWAL -->
    <?php if (can_do($pdo, 'jadwal', 'create')): ?>
        <div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="modalTambahJadwalLabel text-white"><i class="fas fa-plus-circle mr-2"></i>Tambah Jadwal Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="index.php?mod=jadwal&act=save" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Guru & Mapel (dari Penugasan)</label>
                                <select name="id_guru_mapel" class="form-control" required>
                                    <option value="">-- Pilih Guru & Mapel --</option>
                                    <?php foreach ($guru_mapel_list as $gm): ?>
                                        <option value="<?= $gm['id_guru_mapel'] ?>"><?= $gm['nama_guru'] ?> (<?= $gm['nama_mapel'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="id_kelas" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas_list as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Hari</label>
                                <select name="hari_kbm" class="form-control" required>
                                    <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jam</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle w-100 text-left" type="button" id="jamDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        -- Pilih Jam --
                                    </button>
                                    <div class="dropdown-menu p-3" aria-labelledby="jamDropdownButton" style="max-height:320px; overflow:auto; width:100%">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">Pilih satu atau beberapa jam</small>
                                            <div>
                                                <a href="#" class="select-all-jam mr-2">Pilih semua</a>
                                                <a href="#" class="clear-all-jam">Bersihkan</a>
                                            </div>
                                        </div>
                                        <?php foreach ($jam_list as $j): ?>
                                            <div class="form-check">
                                                <input class="form-check-input jam-checkbox" type="checkbox" id="jam_<?= $j['id_jam'] ?>" data-value="<?= $j['id_jam'] ?>">
                                                <label class="form-check-label" for="jam_<?= $j['id_jam'] ?>">Jam ke-<?= htmlspecialchars($j['label_jam_ke'] ?? $j['urutan']) ?> (<?= substr($j['jam_mulai'], 0, 5) ?> - <?= substr($j['jam_selesai'], 0, 5) ?>)</label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div id="selected-jam-inputs"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-dark"><i class="fas fa-calendar-alt mr-2"></i>Jadwal Tampil</h4>
            <?php if (can_do($pdo, 'jadwal', 'create')): ?>
                <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahJadwal">
                    <i class="fas fa-plus mr-1"></i> Tambah Jadwal Baru
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- TABEL JADWAL SPLIT -->
    <?php 
    $groups = [
        0 => ['Senin', 'Selasa', 'Rabu'],
        1 => ['Kamis', 'Jumat', 'Sabtu']
    ];
    // Define group colors: 0 -> Blue, 1 -> Green
    $group_colors = [0 => '#3b82f6', 1 => '#10b981'];
    $group_bg_headers = [0 => '#eff6ff', 1 => '#f0fdf4'];
    ?>
    <div class="col-md-12">
        <div class="row">
            <?php foreach($groups as $idx => $days): 
                $accent = $group_colors[$idx];
                $bg_header = $group_bg_headers[$idx];
            ?>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; overflow: hidden; border-top: 4px solid <?= $accent ?> !important;">
                    <div class="card-header py-3" style="background: <?= $bg_header ?>; border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="font-weight-bold mb-0" style="color: <?= $accent ?>; letter-spacing: 0.5px; text-transform: uppercase;">
                                <i class="fas fa-calendar-week mr-2"></i> 
                                <?php 
                                if($view_type == 'guru' && $id_guru_mapel_filter) {
                                    $find = array_filter($guru_mapel_list, function($x) use($id_guru_mapel_filter) { return $x['id_guru_mapel'] == $id_guru_mapel_filter; });
                                    $label = !empty($find) ? reset($find) : null;
                                    echo $label ? htmlspecialchars($label['nama_guru'] . ' - ' . $label['nama_mapel']) : 'DATA JADWAL';
                                } elseif($view_type == 'kelas' && $id_kelas_filter) {
                                    $find = array_filter($kelas_list, function($x) use($id_kelas_filter) { return $x['id_kelas'] == $id_kelas_filter; });
                                    $label = !empty($find) ? reset($find) : null;
                                    echo $label ? 'JADWAL KELAS ' . htmlspecialchars($label['nama_kelas']) : 'DATA JADWAL';
                                } else {
                                    echo 'HARI ' . strtoupper(implode(' • ', $days));
                                }
                                ?>
                            </h6>
                            <span class="badge badge-pill badge-light border" style="font-size: 0.7rem; color: #64748b;"><?= count($days) ?> HARI</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        <th class="text-center align-middle py-2 border-top-0" style="width: 50px;">HARI</th>
                                        <th class="text-center align-middle border-top-0" style="width: 90px;">WAKTU</th>
                                        <?php if($view_type=='sekolah'): ?>
                                            <th class="text-center align-middle border-top-0" style="width: 70px;">KELAS</th>
                                            <th class="text-center align-middle text-left pl-3 border-top-0">MAPEL / GURU</th>
                                        <?php elseif($view_type=='guru'): ?>
                                            <th class="text-center align-middle border-top-0">MAPEL</th>
                                            <th class="text-center align-middle border-top-0" style="width: 100px;">KELAS</th>
                                        <?php elseif($view_type=='kelas'): ?>
                                            <th class="text-center align-middle text-left pl-3 border-top-0">MAPEL / GURU</th>
                                        <?php endif; ?>
                                        <?php if(has_role(['Admin', 'TU', 'Kurikulum'])): ?>
                                            <th class="text-center align-middle border-top-0" style="width: 40px;"><i class="fas fa-cog"></i></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $has_data = false;
                                    foreach($days as $hari_ini): 
                                        if (isset($result[$hari_ini]) && !empty($result[$hari_ini])): 
                                            $has_data = true;
                                            $rowspan_count = count($result[$hari_ini]);
                                            foreach($result[$hari_ini] as $index => $row): ?>
                                            <tr>
                                                <?php if($index == 0): ?>
                                                    <td rowspan="<?= $rowspan_count ?>" class="align-middle text-center bg-white border-right p-1" style="width: 50px;">
                                                        <span class="badge px-1 py-1" style="font-size: 0.65rem; writing-mode: vertical-lr; transform: rotate(180deg); letter-spacing: 2px; color: #fff; background-color: <?= $accent ?>; border-radius: 4px; box-shadow: 1px 1px 3px rgba(0,0,0,0.1);"><?= strtoupper($hari_ini) ?></span>
                                                    </td>
                                                <?php endif; ?>
                                                
                                                <td class="align-middle text-center p-2" style="font-size: 0.75rem; white-space: nowrap; color: #475569; background: #fafafa; border-right: 1px dashed #f1f5f9;">
                                                    <span class="font-weight-bold" style="color: #1e293b;"><?= substr($row['jam_mulai'],0,5) ?></span>
                                                    <div class="small text-muted" style="font-size: 0.65rem;">s.d</div>
                                                    <span class="font-weight-bold" style="color: #64748b;"><?= substr($row['jam_selesai'],0,5) ?></span>
                                                </td>
                                                
                                                <?php if($view_type=='sekolah'): ?>
                                                    <td class="align-middle text-center small font-weight-bold" style="color: #334155;"><?= htmlspecialchars($row['nama_kelas'] ?? '-') ?></td>
                                                    <td class="align-middle pl-3 py-2">
                                                        <div class="text-dark font-weight-bold mb-0" style="font-size: 0.85rem; line-height: 1.3;"><?= htmlspecialchars($row['nama_mapel'] ?? $row['nama_kegiatan_custom'] ?? $row['nama_kegiatan'] ?? '') ?></div>
                                                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;"><i class="fas fa-chalkboard-teacher mr-1 text-primary opacity-50"></i><?= htmlspecialchars($row['nama_guru'] ?? '-') ?></div>
                                                    </td>
                                                <?php elseif($view_type=='guru'): ?>
                                                    <td class="align-middle small font-weight-bold"><?= htmlspecialchars($row['nama_mapel'] ?? $row['nama_kegiatan_custom'] ?? $row['nama_kegiatan'] ?? '') ?></td>
                                                    <td class="align-middle text-center small font-weight-bold"><?= htmlspecialchars($row['nama_kelas'] ?? '-') ?></td>
                                                <?php elseif($view_type=='kelas'): ?>
                                                    <td class="align-middle pl-3 py-2">
                                                        <div class="text-dark font-weight-bold mb-0" style="font-size: 0.85rem; line-height: 1.3;"><?= htmlspecialchars($row['nama_mapel'] ?? $row['nama_kegiatan_custom'] ?? $row['nama_kegiatan'] ?? '') ?></div>
                                                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;"><i class="fas fa-chalkboard-teacher mr-1 text-primary opacity-50"></i><?= htmlspecialchars($row['nama_guru'] ?? '-') ?></div>
                                                    </td>
                                                <?php endif; ?>

                                                <?php if (can_do($pdo, 'jadwal', 'delete')): ?>
                                                <td class="text-center align-middle p-1">
                                                    <?php if(!empty($row['id_jadwal_mengajar'])): ?>
                                                        <a href="index.php?mod=jadwal&act=delete&id=<?= $row['id_jadwal_mengajar'] ?>" onclick="return confirmDelete(event)" class="btn btn-xs btn-outline-danger border-0 rounded-circle" style="width: 24px; height: 24px; display: inline-flex; justify-content: center; align-items: center;"><i class="fas fa-times"></i></a>
                                                    <?php endif; ?>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if(!$has_data): ?>
                                        <tr><td colspan="6" class="text-center font-italic text-muted py-5 small bg-light">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486777.png" width="48" class="mb-2 opacity-50" style="filter: grayscale(100%);"><br>
                                            Tidak ada jadwal untuk <?= implode(', ', $days) ?>
                                        </td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>
</section>

<style>
.modal-header .close { margin: -1rem -1rem -1rem auto; }
.badge-primary { background-color: var(--theme-accent, #3b82f6); }
.table thead th { border-bottom: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
.card-title { font-size: 1rem; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    function updateJamButton() {
        var checked = document.querySelectorAll('.jam-checkbox:checked');
        var btn = document.getElementById('jamDropdownButton');
        if (!btn) return;
        if (checked.length === 0) {
            btn.textContent = '-- Pilih Jam --';
        } else if (checked.length === 1) {
            var lbl = checked[0].closest('.form-check').querySelector('label').textContent.trim();
            btn.textContent = lbl;
        } else {
            btn.textContent = checked.length + ' jam dipilih';
        }
    }

    function syncHiddenInputs() {
        var container = document.getElementById('selected-jam-inputs');
        if (!container) return;
        container.innerHTML = '';
        document.querySelectorAll('.jam-checkbox:checked').forEach(function(cb){
            var val = cb.getAttribute('data-value');
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'jam[]';
            inp.value = val;
            container.appendChild(inp);
        });
    }

    // Use event delegation for checkboxes inside modal
    document.addEventListener('change', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('jam-checkbox')) {
            updateJamButton();
            syncHiddenInputs();
        }
    });

    document.addEventListener('click', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('select-all-jam')) {
            e.preventDefault();
            document.querySelectorAll('.jam-checkbox').forEach(function(cb){ cb.checked = true; });
            updateJamButton(); syncHiddenInputs();
        }
        if (e.target && e.target.classList && e.target.classList.contains('clear-all-jam')) {
            e.preventDefault();
            document.querySelectorAll('.jam-checkbox').forEach(function(cb){ cb.checked = false; });
            updateJamButton(); syncHiddenInputs();
        }
    });

    // Initialize on DOMContentLoaded and when modal is shown
    // The existing DOMContentLoaded listener will handle initial setup.
    // For modal, Bootstrap's 'shown.bs.modal' event can be used if needed,
    // but current event delegation should cover it.
    updateJamButton();
    syncHiddenInputs();
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>