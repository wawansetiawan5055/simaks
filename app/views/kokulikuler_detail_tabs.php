<?php include __DIR__ . '/partials/header.php'; ?>
<style>
    .student-list {
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 5px;
        padding: 10px;
    }
    .student-item {
        cursor: grab;
        margin-bottom: 5px;
        padding: 8px 12px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9em;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .student-item:hover { background: #e9ecef; }
    .kiri-style { background-color: #fff3e0 !important; border-color: #ffe0b2 !important; }
    .kanan-style { background-color: #e8f5e9 !important; border-color: #c8e6c9 !important; }
    
     /* Preview Modal Styles */
    .modal-preview-content {
        height: 80vh;
    }
    iframe.preview-frame {
        width: 100%;
        height: 100%;
        border: none;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-futbol mr-2"></i> <strong><?= htmlspecialchars($kokul['nama_kegiatan']) ?></strong></h1>
                <?php if (!empty($kokul['tema'])): ?>
                    <h5 class="text-muted"><i class="fas fa-tag"></i> Tema: <?= htmlspecialchars($kokul['tema']) ?></h5>
                <?php endif; ?>
                <div class="mt-2">
                    <?php if (!empty($profil_terpilih)): ?>
                        <?php foreach ($profil_terpilih as $pid): 
                            $dimensi = array_filter($profil_master, function($p) use ($pid) { return $p['id_profil'] == $pid; });
                            $dimensi = reset($dimensi);
                        ?>
                            <span class="badge badge-info shadow-sm mr-1 p-2" title="<?= htmlspecialchars($dimensi['deskripsi']) ?>">
                                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($dimensi['nama_dimensi']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <small class="text-muted italic"><i class="fas fa-info-circle"></i> Belum ada target profil yang dipilih.</small>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            // Prepare dimensions for JS generator
            $profil_terpilih_names = [];
            if (!empty($profil_terpilih)) {
                foreach ($profil_terpilih as $pid) {
                    $dim = array_filter($profil_master, function($p) use ($pid) { return $p['id_profil'] == $pid; });
                    $dim = reset($dim);
                    if ($dim) $profil_terpilih_names[] = $dim['nama_dimensi'];
                }
            }
            ?>
            <div class="col-sm-6 text-end">
                <a href="<?= BASE_URL ?>kokulikuler" class="btn btn-secondary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'program' || $tab == '' || $tab == 'kegiatan' ? 'active' : '' ?>" href="<?= BASE_URL ?>kokulikuler/index/<?= $kokul['id_kokulikuler'] ?>/program"><i class="fas fa-file-signature"></i> Program Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'anggota' ? 'active' : '' ?>" href="<?= BASE_URL ?>kokulikuler/index/<?= $kokul['id_kokulikuler'] ?>/anggota"><i class="fas fa-users"></i> Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'jurnal' ? 'active' : '' ?>" href="<?= BASE_URL ?>kokulikuler/index/<?= $kokul['id_kokulikuler'] ?>/jurnal"><i class="fas fa-book"></i> Jurnal & Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'galeri' ? 'active' : '' ?>" href="<?= BASE_URL ?>kokulikuler/index/<?= $kokul['id_kokulikuler'] ?>/galeri"><i class="fas fa-images"></i> Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'nilai' ? 'active' : '' ?>" href="<?= BASE_URL ?>kokulikuler/index/<?= $kokul['id_kokulikuler'] ?>/nilai"><i class="fas fa-award"></i> Penilaian</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">

                    <!-- TAB PROGRAM KERJA -->
                    <?php if ($tab == 'program' || $tab == '' || $tab == 'kegiatan'): ?>
                    <div class="tab-pane fade show active" id="program" role="tabpanel">
                        <div class="mb-3">
                            <button class="btn btn-outline-primary mr-2" data-toggle="modal" data-target="#modalUploadProker">
                                <i class="fas fa-upload"></i> Upload Program Kerja
                            </button>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgenda">
                                <i class="fas fa-calendar-plus"></i> Tambah Agenda
                            </button>
                        </div>

                        <div class="row">
                            <!-- Left Column: Tabel Agenda -->
                            <div class="col-md-8">
                                <div class="card card-outline card-primary shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title m-0">Tabel Agenda</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th width="40" class="text-center">NO</th>
                                                        <th width="100" class="text-center">TGL</th>
                                                        <th class="text-center">NAMA KEGIATAN</th>
                                                        <th width="120" class="text-center">LOKASI</th>
                                                        <th width="100" class="text-center">KETERANGAN</th>
                                                        <th width="80" class="text-center">AKSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $agenda_only = array_filter($agenda_list, function($a) { return ($a['tipe'] == 'agenda'); });
                                                    if (empty($agenda_only)): 
                                                    ?>
                                                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada agenda kegiatan.</td></tr>
                                                    <?php else: ?>
                                                        <?php $no = 1; foreach ($agenda_only as $ag): ?>
                                                            <tr>
                                                                <td class="text-center"><?= $no++ ?></td>
                                                                <td class="text-center">
                                                                    <strong><?= date('d/m/Y', strtotime($ag['tanggal'])) ?></strong><br>
                                                                    <small class="text-muted"><?= htmlspecialchars($ag['created_at'] ?? '') ?></small>
                                                                </td>
                                                                <td><?= htmlspecialchars($ag['nama_agenda']) ?></td>
                                                                <td class="text-center"><?= htmlspecialchars($ag['lokasi'] ?? '-') ?></td>
                                                                <td class="text-center"><?= htmlspecialchars($ag['keterangan'] ?? '-') ?></td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                                        <?php if (!empty($ag['file_path'])): ?>
                                                                            <button class="btn btn-xs btn-info p-1" onclick="previewFile('<?= $ag['file_path'] ?>', 'pdf', '<?= htmlspecialchars($ag['nama_agenda']) ?>')" title="Lihat Laporan" style="line-height: 1;">
                                                                                <i class="fas fa-eye fa-xs"></i>
                                                                            </button>
                                                                        <?php endif; ?>
                                                                        <button class="btn btn-xs btn-warning p-1" onclick='editAgenda(<?= json_encode($ag) ?>)' title="Edit" style="line-height: 1;"><i class="fas fa-edit fa-xs"></i></button>
                                                                        <a href="<?= BASE_URL ?>kokulikuler/agenda_delete?id_kokulikuler=<?= $id ?>&id_agenda=<?= $ag['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus" style="line-height: 1;"><i class="fas fa-trash fa-xs"></i></a>
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

                            <!-- Right Column: Tabel File Program Kerja -->
                            <div class="col-md-4">
                                <div class="card card-outline card-info shadow-sm">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title m-0">Tabel File Program Kerja</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th width="40" class="text-center">NO</th>
                                                        <th class="text-center">NAMA FILE</th>
                                                        <th width="80" class="text-center">AKSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $program_only = array_filter($agenda_list, function($a) { return ($a['tipe'] == 'program'); });
                                                    if (empty($program_only)): 
                                                    ?>
                                                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada file program.</td></tr>
                                                    <?php else: ?>
                                                        <?php $no = 1; foreach ($program_only as $pr): ?>
                                                            <tr>
                                                                <td class="text-center"><?= $no++ ?></td>
                                                                <td><?= htmlspecialchars($pr['nama_agenda']) ?></td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                                        <button class="btn btn-xs btn-info p-1" onclick="previewFile('<?= $pr['file_path'] ?>', 'pdf', '<?= htmlspecialchars($pr['nama_agenda']) ?>')" title="Lihat File" style="line-height: 1;">
                                                                            <i class="fas fa-eye fa-xs"></i>
                                                                        </button>
                                                                        <a href="<?= BASE_URL ?>kokulikuler/agenda_delete?id_kokulikuler=<?= $id ?>&id_agenda=<?= $pr['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus File" style="line-height: 1;">
                                                                            <i class="fas fa-trash fa-xs"></i>
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
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- TAB ANGGOTA -->
                    <?php if ($tab == 'anggota' || $tab == 'kegiatan'): ?>
                    <div class="tab-pane fade show active">
                         <div class="row">
                            <!-- Available Students -->
                            <div class="col-md-6">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Daftar Siswa (Tersedia)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <select id="filter-kelas" class="form-control">
                                                    <option value="">Semua Kelas</option>
                                                    <?php foreach ($kelas_list as $k): ?>
                                                        <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" id="search-left" class="form-control" placeholder="Cari Nama...">
                                            </div>
                                        </div>
                                        <div id="list-left" class="student-list kiri-style connectedSortable">
                                            <?php foreach ($available_students as $s): ?>
                                                <div class="student-item" data-id="<?= $s['id_siswa'] ?>">
                                                    <div>
                                                        <strong><?= $s['nama_siswa'] ?></strong><br>
                                                        <small class="text-muted"><?= $s['nama_kelas'] ?? 'Tanpa Kelas' ?></small>
                                                    </div>
                                                    <i class="fas fa-arrows-alt text-muted"></i>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Members -->
                            <div class="col-md-6">
                                <div class="card card-success card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Anggota Terdaftar (<span id="count-right"><?= count($anggota_list) ?></span>)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="input-group mb-2">
                                            <input type="text" id="search-right" class="form-control" placeholder="Cari Anggota...">
                                        </div>
                                        <div id="list-right" class="student-list kanan-style connectedSortable">
                                            <?php foreach ($anggota_list as $a): ?>
                                                <div class="student-item" data-id="<?= $a['id_siswa'] ?>">
                                                    <div>
                                                        <strong><?= $a['nama_siswa'] ?></strong><br>
                                                        <small class="text-muted"><?= $a['nama_kelas'] ?? 'Tanpa Kelas' ?></small>
                                                    </div>
                                                    <i class="fas fa-check text-success"></i>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- TAB JURNAL -->
                    <?php if ($tab == 'jurnal'): ?>
                    <div class="tab-pane fade show active">
                        <div class="mb-3">
                            <a href="<?= BASE_URL ?>kokulikuler/jurnal_form?id_kokulikuler=<?= $kokul['id_kokulikuler'] ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Isi Jurnal & Absensi
                            </a>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Materi</th>
                                    <th>Hadir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jurnal_list as $j): 
                                    $presensi = KokulikulerModel::getPresensi($pdo, $j['id_jurnal']);
                                    $hadir_count = 0;
                                    foreach($presensi as $status) if($status == 'H') $hadir_count++;
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($j['materi']) ?></td>
                                    <td><?= $hadir_count ?> Siswa</td>
                                    <td>
                                        <a href="<?= BASE_URL ?>kokulikuler/jurnal_form?id_kokulikuler=<?= $kokul['id_kokulikuler'] ?>&id_jurnal=<?= $j['id_jurnal'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <a href="<?= BASE_URL ?>kokulikuler/jurnal_delete?id_kokulikuler=<?= $kokul['id_kokulikuler'] ?>&id_jurnal=<?= $j['id_jurnal'] ?>" class="btn btn-sm btn-danger btn-delete-confirm"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <!-- TAB GALERI -->
                    <?php if ($tab == 'galeri'): ?>
                    <div class="tab-pane fade show active">
                        <div class="mb-3">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalGaleri">
                                <i class="fas fa-upload"></i> Upload Foto
                            </button>
                        </div>
                        
                        <div class="row">
                            <?php if (empty($galeri_list)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Belum ada foto di galeri.
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($galeri_list as $g): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100 shadow-sm">
                                        <div style="height: 180px; overflow: hidden; cursor: pointer;" onclick="previewFile('<?= $g['file_path'] ?>', 'image', '<?= htmlspecialchars($g['judul'] ?? 'Galeri') ?>')">
                                            <img src="<?= $g['file_path'] ?>" class="card-img-top" alt="Galeri" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <?php if ($g['judul']): ?>
                                                <p class="card-text text-sm mb-1 font-weight-bold text-truncate"><?= htmlspecialchars($g['judul']) ?></p>
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted" style="font-size: 0.75rem"><i class="far fa-calendar-alt"></i> <?= date('d/m/y', strtotime($g['created_at'])) ?></small>
                                                
                                                <div class="btn-group">
                                                    <button class="btn btn-info btn-xs" onclick="previewFile('<?= $g['file_path'] ?>', 'image', '<?= htmlspecialchars($g['judul'] ?? 'Galeri') ?>')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= BASE_URL ?>kokulikuler/galeri_delete?id_kokulikuler=<?= $kokul['id_kokulikuler'] ?>&id_galeri=<?= $g['id_galeri'] ?>" 
                                                       class="btn btn-danger btn-xs btn-delete-confirm">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- TAB PENILAIAN -->
                    <?php if ($tab == 'nilai'): ?>
                    <div class="tab-pane fade show active" id="nilai" role="tabpanel">
                        <div class="alert alert-info d-flex align-items-center" style="gap:10px;">
                            <i class="fas fa-keyboard fa-lg"></i>
                            <div>
                                <strong>Pintasan Keyboard:</strong> Klik baris siswa untuk fokus, lalu tekan
                                <kbd>A</kbd> = <span class="badge badge-success">Sangat Baik</span> &nbsp;
                                <kbd>B</kbd> = <span class="badge badge-primary">Baik</span> &nbsp;
                                <kbd>Enter</kbd> / <kbd>↓</kbd> = Pindah Baris ke Bawah
                                <br><small><i>Deskripsi akan otomatis terisi seketika saat capaian dipilih.</i></small>
                            </div>
                        </div>
                        <form action="<?= BASE_URL ?>kokulikuler/nilai_save" method="POST">
                            <input type="hidden" name="id_kokulikuler" value="<?= $kokul['id_kokulikuler'] ?>">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tableNilai">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="4%" class="text-center">No</th>
                                            <th width="15%">Nama Siswa</th>
                                            <th width="18%" class="text-center">Capaian Profil</th>
                                            <th width="63%">Deskripsi Capaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($penilaian_list)): ?>
                                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada anggota terdaftar.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($penilaian_list as $i => $n): ?>
                                            <tr class="nilai-row" data-id="<?= $n['id_siswa'] ?>" 
                                                data-nama="<?= htmlspecialchars($n['nama_siswa']) ?>"
                                                tabindex="0"
                                                onclick="setFocusRow(this)"
                                                style="cursor:pointer; outline:none;">
                                                <td class="text-center align-middle"><?= $i + 1 ?></td>
                                                <td class="align-middle font-weight-bold">
                                                    <?= htmlspecialchars($n['nama_siswa']) ?>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <!-- Hidden input untuk form submit -->
                                                    <input type="hidden" 
                                                           name="nilai_data[<?= $n['id_siswa'] ?>][nilai]" 
                                                           id="val_<?= $n['id_siswa'] ?>"
                                                           value="<?= htmlspecialchars($n['nilai'] ?? '') ?>">
                                                    <!-- Toggle Buttons -->
                                                    <div class="btn-group w-100" role="group">
                                                        <button type="button" 
                                                                class="btn btn-capaian btn-sb <?= ($n['nilai'] ?? '') == 'SB' ? 'btn-success active-capaian' : 'btn-outline-success' ?>"
                                                                onclick="setCapaian(<?= $n['id_siswa'] ?>, 'SB', this); event.stopPropagation();"
                                                                title="Sangat Baik (A)">
                                                            <i class="fas fa-star"></i> SB
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-capaian btn-b <?= ($n['nilai'] ?? '') == 'B' ? 'btn-primary active-capaian' : 'btn-outline-primary' ?>"
                                                                onclick="setCapaian(<?= $n['id_siswa'] ?>, 'B', this); event.stopPropagation();"
                                                                title="Baik (B)">
                                                            <i class="fas fa-thumbs-up"></i> B
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea name="nilai_data[<?= $n['id_siswa'] ?>][deskripsi]" 
                                                              id="desc_<?= $n['id_siswa'] ?>" 
                                                              class="form-control form-control-sm" 
                                                              rows="2" 
                                                              placeholder="Otomatis terisi saat capaian dipilih atau ketik manual..."
                                                              onclick="setFocusRow($(this).closest('tr')[0]); event.stopPropagation();"><?= htmlspecialchars($n['deskripsi'] ?? '') ?></textarea>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (!empty($penilaian_list)): ?>
                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Simpan Semua Penilaian
                                </button>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- SCRIPTS -->
<?php if ($tab == 'anggota' || $tab == 'kegiatan' || $tab == ''): ?>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    const id_kokul = <?= $kokul['id_kokulikuler'] ?>;
    
    // Sortable Logic
    $("#list-left, #list-right").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-sortable-placeholder student-item",
        receive: function(event, ui) {
            const targetList = $(this).attr('id');
            const studentId = ui.item.data('id');
            let action = (targetList === 'list-right') ? 'add' : 'remove';
            
            $.post('<?= BASE_URL ?>kokulikuler/update_anggota', {
                action: action, id_kokul: id_kokul, student_ids: [studentId]
            }, function(res) {
                 if(res.status === 'success') {
                     if(action === 'add') ui.item.find('i').attr('class', 'fas fa-check text-success');
                     else ui.item.find('i').attr('class', 'fas fa-arrows-alt text-muted');
                     $('#count-right').text($('#list-right .student-item').length);
                 } else {
                     $(ui.sender).sortable('cancel');
                     alert(res.message);
                 }
            }, 'json');
        }
    }).disableSelection();

    // Search Logic
    function loadStudents() {
        var q = $('#search-left').val();
        var k = $('#filter-kelas').val();
        $.getJSON('<?= BASE_URL ?>kokulikuler/search_students', {id_kokul: id_kokul, q: q, id_kelas: k}, function(res){
            if(res.status === 'success'){
                $('#list-left').empty();
                res.data.forEach(s => {
                    $('#list-left').append(`<div class="student-item" data-id="${s.id_siswa}"><div><strong>${s.nama_siswa}</strong><br><small class="text-muted">${s.nama_kelas||''}</small></div><i class="fas fa-arrows-alt text-muted"></i></div>`);
                });
            }
        });
    }
    $('#search-left').on('keyup', function() { setTimeout(loadStudents, 300); });
    $('#filter-kelas').on('change', loadStudents);

    // Filter Right
    $('#search-right').on('keyup', function() {
        var v = $(this).val().toLowerCase();
        $("#list-right .student-item").filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1) });
    });
});
</script>
<?php endif; ?>

<script>
function previewFile(path, type, title = 'Preview') {
    $('#previewTitle').text(title);
    var content = '';
    
    if (type === 'image') {
        content = '<img src="' + path + '" style="max-width: 100%; max-height: 100%; object-fit: contain;">';
    } else {
        content = '<iframe src="' + path + '" class="preview-frame"></iframe>';
    }
    
    $('#previewBody').html(content);
    $('#modalPreview').modal('show');
}

function editAgenda(data) {
    $('#modalAgenda .modal-title').text('Edit Agenda Kegiatan');
    $('#id_agenda').val(data.id_agenda);
    $('#modalAgenda [name="tanggal"]').val(data.tanggal);
    $('#modalAgenda [name="nama_agenda"]').val(data.nama_agenda);
    $('#modalAgenda [name="lokasi"]').val(data.lokasi);
    $('#modalAgenda [name="keterangan"]').val(data.keterangan);
    $('#modalAgenda').modal('show');
}

// Reset modal on hide
$('#modalAgenda').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $('#id_agenda').val('');
    $('#modalAgenda .modal-title').text('Tambah Agenda Kegiatan');
});

// =============================================
// PENILAIAN: Capaian & Deskripsi Otomatis
// =============================================

var focusedRow = null;

function setFocusRow(tr) {
    // Hilangkan highlight baris sebelumnya
    $('.nilai-row').removeClass('table-warning');
    focusedRow = tr;
    $(tr).addClass('table-warning');
}

function setCapaian(idSiswa, nilai, btnEl) {
    // Update hidden input
    $('#val_' + idSiswa).val(nilai);

    // Update tombol visual dalam baris yang sama
    var $row = $(btnEl).closest('tr');
    $row.find('.btn-sb').removeClass('btn-success active-capaian').addClass('btn-outline-success');
    $row.find('.btn-b').removeClass('btn-primary active-capaian').addClass('btn-outline-primary');

    if (nilai === 'SB') {
        $row.find('.btn-sb').removeClass('btn-outline-success').addClass('btn-success active-capaian');
    } else {
        $row.find('.btn-b').removeClass('btn-outline-primary').addClass('btn-primary active-capaian');
    }

    // Auto-generate deskripsi
    var namaSiswa = $row.data('nama');
    generateDeskripsi(idSiswa, namaSiswa);
}

function generateDeskripsi(idSiswa, namaSiswa) {
    const tema    = "<?= addslashes($kokul['tema'] ?? $kokul['nama_kegiatan'] ?? '') ?>";
    const dimensi = <?= json_encode($profil_terpilih_names) ?>;

    // Baca nilai capaian saat ini
    const nilaiVal = $('#val_' + idSiswa).val();
    const capaianText = (nilaiVal === 'SB') ? 'Sangat Baik' : (nilaiVal === 'B' ? 'Baik' : 'Sangat Baik');

    // Gabung nama dimensi
    let dimensiStr = 'profil lulusan';
    if (dimensi.length > 0) {
        const tmp = [...dimensi];
        if (tmp.length === 1) {
            dimensiStr = tmp[0];
        } else {
            const last = tmp.pop();
            dimensiStr = tmp.join(', ') + ' dan ' + last;
        }
    }

    // Format deskripsi sesuai permintaan
    const text = `Ananda ${namaSiswa} menunjukan pencapaian yang ${capaianText} pada dimensi profil lulusan ${dimensiStr} melalui kegiatan ${tema}.`;
    $('#desc_' + idSiswa).val(text);
}

// Keyboard shortcut: A = Sangat Baik, B = Baik
$(document).on('keydown', function(e) {
    // Hanya aktif di tab nilai & baris terfokus
    if (!focusedRow) return;
    // Jangan aktif jika user sedang mengetik di textarea/input
    if ($(document.activeElement).is('textarea, input, select')) return;

    var idSiswa = $(focusedRow).data('id');
    var nama    = $(focusedRow).data('nama');
    var key     = e.key.toUpperCase();

    if (key === 'A') {
        e.preventDefault();
        setCapaian(idSiswa, 'SB', focusedRow.querySelector('.btn-sb'));
    } else if (key === 'B') {
        e.preventDefault();
        setCapaian(idSiswa, 'B', focusedRow.querySelector('.btn-b'));
    } else if (e.key === 'ArrowDown' || e.key === 'Enter') {
        e.preventDefault();
        var $next = $(focusedRow).next('.nilai-row');
        if ($next.length) setFocusRow($next[0]);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        var $prev = $(focusedRow).prev('.nilai-row');
        if ($prev.length) setFocusRow($prev[0]);
    }
});
</script>

<!-- MODALS -->

<!-- PREVIEW MODAL -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content modal-preview-content">
            <div class="modal-header bg-dark text-white p-2">
                <h5 class="modal-title" id="previewTitle" style="font-size: 1rem;">Preview</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="$('#modalPreview').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="previewBody" style="background: #e9ecef; display: flex; align-items: center; justify-content: center; height: 100%;">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Program Kerja -->
<div class="modal fade" id="modalUploadProker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kokulikuler/program_upload" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_kokulikuler" value="<?= $kokul['id_kokulikuler'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Program Kerja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Program Kerja</label>
                        <input type="text" name="nama_kegiatan_baru" class="form-control" placeholder="Contoh: Program Kerja P5 2024" required>
                    </div>
                    
                    <div class="form-group">
                        <label>File Program Kerja (PDF/DOC)</label>
                        <input type="file" name="file_upload" class="form-control" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Unggah Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Agenda -->
<div class="modal fade" id="modalAgenda" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kokulikuler/agenda_save" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_kokulikuler" value="<?= $kokul['id_kokulikuler'] ?>">
                <input type="hidden" name="id_agenda" id="id_agenda">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Agenda Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" name="nama_agenda" class="form-control" required placeholder="Contoh: Workshop P5...">
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Laporan Kegiatan <small class="text-muted">(Opsional, PDF/DOC/Gambar)</small></label>
                        <input type="file" name="file_upload" class="form-control" accept=".pdf,.doc,.docx,image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Galeri -->
<div class="modal fade" id="modalGaleri" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>kokulikuler/galeri_save" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Foto Galeri</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kokulikuler" value="<?= $kokul['id_kokulikuler'] ?>">
                    
                    <div class="form-group">
                        <label>Judul Foto <small class="text-muted">(Opsional)</small></label>
                        <input type="text" name="judul" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>File Gambar</label>
                        <input type="file" name="file_upload" class="form-control" accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
