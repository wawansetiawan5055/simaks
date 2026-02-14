<?php 
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php'; 
?>
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
                <h1>Kewirausahaan: <strong><?= htmlspecialchars($kewirausahaan['nama_kegiatan']) ?></strong></h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="index.php?mod=kewirausahaan" class="btn btn-secondary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="kewirausahaanTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'program') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=program">
                            <i class="fas fa-file-signature"></i> Program Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'kegiatan' || $tab == 'anggota') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=anggota">
                            <i class="fas fa-users"></i> Anggota
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'tahapan') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=tahapan">
                            <i class="fas fa-tasks"></i> Tahapan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'produk') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=produk">
                            <i class="fas fa-box"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'keuangan') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=keuangan">
                            <i class="fas fa-money-bill-wave"></i> Keuangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'jurnal') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=jurnal">
                            <i class="fas fa-book-reader"></i> Jurnal & Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'galeri') ? 'active' : '' ?>" href="index.php?mod=kewirausahaan&act=index&id=<?= $id ?>&tab=galeri">
                            <i class="fas fa-images"></i> Galeri
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB PROGRAM KERJA -->
                    <?php if ($tab == 'program'): ?>
                    <div class="tab-pane fade show active">
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
                                                                    <strong><?= DateHelper::formatTanggal($ag['tanggal'], 'short') ?></strong>
                                                                </td>
                                                                <td><?= htmlspecialchars($ag['nama_kegiatan']) ?></td>
                                                                <td class="text-center"><?= htmlspecialchars($ag['lokasi'] ?? '-') ?></td>
                                                                <td class="text-center"><?= htmlspecialchars($ag['keterangan'] ?? '-') ?></td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                                        <?php if (!empty($ag['file_path'])): ?>
                                                                            <button class="btn btn-xs btn-info p-1" onclick="previewFile('<?= $ag['file_path'] ?>', 'pdf', '<?= htmlspecialchars($ag['nama_kegiatan']) ?>')" title="Lihat Laporan" style="line-height: 1;">
                                                                                <i class="fas fa-eye fa-xs"></i>
                                                                            </button>
                                                                        <?php endif; ?>
                                                                        <button class="btn btn-xs btn-warning p-1" onclick='editAgenda(<?= json_encode($ag) ?>)' title="Edit" style="line-height: 1;"><i class="fas fa-edit fa-xs"></i></button>
                                                                        <a href="index.php?mod=kewirausahaan&act=agenda_delete&id_kewirausahaan=<?= $id ?>&id_agenda=<?= $ag['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus" style="line-height: 1;"><i class="fas fa-trash fa-xs"></i></a>
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
                                                                <td><?= htmlspecialchars($pr['nama_kegiatan']) ?></td>
                                                                <td>
                                                                    <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                                        <button class="btn btn-xs btn-info p-1" onclick="previewFile('<?= $pr['file_path'] ?>', 'pdf', '<?= htmlspecialchars($pr['nama_kegiatan']) ?>')" title="Lihat File" style="line-height: 1;">
                                                                            <i class="fas fa-eye fa-xs"></i>
                                                                        </button>
                                                                        <a href="index.php?mod=kewirausahaan&act=agenda_delete&id_kewirausahaan=<?= $id ?>&id_agenda=<?= $pr['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus File" style="line-height: 1;">
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

                    <!-- TAB TAHAPAN -->
                    <?php if ($tab == 'tahapan'): ?>
                    <div class="tab-pane fade show active">
                        <div class="mb-3">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalTahapan">
                                <i class="fas fa-plus"></i> Tambah Tahapan
                            </button>
                        </div>
                        
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Urutan</th>
                                    <th>Nama Tahapan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tahapan_list)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">Belum ada tahapan.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($tahapan_list as $t): ?>
                                    <tr>
                                        <td><?= $t['urutan'] ?></td>
                                        <td><?= htmlspecialchars($t['nama_tahapan']) ?></td>
                                        <td><?= $t['tanggal_mulai'] ? DateHelper::formatTanggal($t['tanggal_mulai'], 'short') : '-' ?></td>
                                        <td><?= $t['tanggal_selesai'] ? DateHelper::formatTanggal($t['tanggal_selesai'], 'short') : '-' ?></td>
                                        <td><span class="badge badge-<?= $t['status'] == 'Selesai' ? 'success' : ($t['status'] == 'Proses' ? 'warning' : 'secondary') ?>"><?= $t['status'] ?></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?mod=kewirausahaan&act=jurnal_form&id_kewirausahaan=<?= $id ?>&id_tahapan=<?= $t['id_tahapan'] ?>" class="btn btn-info btn-xs mr-1" title="Isi Jurnal & Absensi">
                                                    <i class="fas fa-book-reader"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning btn-xs mr-1" onclick='editTahapan(<?= json_encode($t) ?>)'><i class="fas fa-edit"></i></button>
                                                <a href="index.php?mod=kewirausahaan&act=tahapan_delete&id_kewirausahaan=<?= $id ?>&id_tahapan=<?= $t['id_tahapan'] ?>" class="btn btn-danger btn-xs" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <!-- TAB PRODUK -->
                    <?php if ($tab == 'produk'): ?>
                    <div class="tab-pane fade show active">
                         <div class="mb-3">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalProduk">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </button>
                        </div>
                        
                        <div class="row">
                            <?php if (empty($produk_list)): ?>
                                <div class="col-12"><p class="text-center text-muted">Belum ada produk.</p></div>
                            <?php else: ?>
                                <?php foreach ($produk_list as $p): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <?php if ($p['foto_produk']): ?>
                                            <div style="height:200px; overflow:hidden;">
                                                <img src="<?= $p['foto_produk'] ?>" class="card-img-top" style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                                <i class="fas fa-box fa-3x text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title font-weight-bold"><?= htmlspecialchars($p['nama_produk']) ?></h5>
                                            <p class="card-text text-muted small"><?= htmlspecialchars($p['deskripsi']) ?></p>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-success font-weight-bold">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></span>
                                                <span class="badge badge-info">Stok: <?= $p['stok'] ?></span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <a href="index.php?mod=kewirausahaan&act=produk_delete&id_kewirausahaan=<?= $id ?>&id_produk=<?= $p['id_produk'] ?>" class="btn btn-danger btn-block btn-sm" onclick="return confirmDelete(event)">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- TAB KEUANGAN -->
                    <?php if ($tab == 'keuangan'): ?>
                    <div class="tab-pane fade show active">
                        <div class="alert alert-info">Fitur Keuangan dalam pengembangan.</div>
                    </div>
                    <?php endif; ?>

                    <!-- TAB JURNAL -->
                    <?php if ($tab == 'jurnal'): ?>
                    <div class="tab-pane fade show active">
                        <div class="mb-3">
                            <a href="index.php?mod=kewirausahaan&act=jurnal_form&id_kewirausahaan=<?= $kewirausahaan['id_kewirausahaan'] ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Isi Jurnal & Absensi
                            </a>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tahapan</th>
                                    <th>Materi</th>
                                    <th>Hadir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jurnal_list as $j): 
                                    $presensi = KewirausahaanModel::getPresensi($pdo, $j['id_jurnal']);
                                    $hadir_count = 0;
                                    foreach($presensi as $status) if($status == 'H') $hadir_count++;
                                ?>
                                <tr>
                                    <td><?= DateHelper::formatTanggal($j['tanggal'], 'short') ?></td>
                                    <td>
                                        <?php if ($j['nama_tahapan']): ?>
                                            <span class="badge badge-info"><?= htmlspecialchars($j['nama_tahapan']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Umum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($j['materi']) ?></td>
                                    <td><?= $hadir_count ?> Siswa</td>
                                    <td>
                                        <a href="index.php?mod=kewirausahaan&act=jurnal_form&id_kewirausahaan=<?= $kewirausahaan['id_kewirausahaan'] ?>&id_jurnal=<?= $j['id_jurnal'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <a href="index.php?mod=kewirausahaan&act=jurnal_delete&id_kewirausahaan=<?= $kewirausahaan['id_kewirausahaan'] ?>&id_jurnal=<?= $j['id_jurnal'] ?>" class="btn btn-sm btn-danger btn-delete-confirm"><i class="fas fa-trash"></i></a>
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
                                                    <a href="index.php?mod=kewirausahaan&act=galeri_delete&id_kewirausahaan=<?= $id ?>&id_galeri=<?= $g['id_galeri'] ?>" 
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

                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

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

<!-- Modal Tahapan -->
<div class="modal fade" id="modalTahapan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?mod=kewirausahaan&act=tahapan_save" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahapan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="0" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Tahapan</label>
                        <input type="text" name="nama_tahapan" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Tgl Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Tgl Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Belum Mulai">Belum Mulai</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
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

<!-- Modal Produk -->
<div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?mod=kewirausahaan&act=produk_save" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Stok</label>
                                <input type="number" name="stok" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Produk</label>
                        <input type="file" name="file_upload" class="form-control" accept="image/*">
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

<!-- Modal Galeri -->
<div class="modal fade" id="modalGaleri" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?mod=kewirausahaan&act=galeri_save" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Foto Galeri</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    
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

<!-- Modal Upload Proker (Work Program Only) -->
<div class="modal fade" id="modalUploadProker" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?mod=kewirausahaan&act=program_upload" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Program Kerja</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    
                    <div class="form-group">
                        <label>Nama Program Kerja</label>
                        <input type="text" name="nama_kegiatan_baru" class="form-control" placeholder="Contoh: Program Kerja Semester 2" required>
                    </div>

                    <div class="form-group">
                        <label>Pilih File (PDF/DOC)</label>
                        <input type="file" name="file_upload" class="form-control" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload Program</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Agenda -->
<div class="modal fade" id="modalAgenda" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?mod=kewirausahaan&act=program_save" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Agenda Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_kewirausahaan" value="<?= $id ?>">
                    <input type="hidden" name="save_type" value="agenda">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
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

<?php if ($tab == 'anggota' || $tab == 'kegiatan'): ?>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    const id_kew = <?= (int)($id ?? 0) ?>;
    
    // Sortable Logic
    $("#list-left, #list-right").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-sortable-placeholder student-item",
        receive: function(event, ui) {
            const targetList = $(this).attr('id');
            const studentId = ui.item.data('id');
            let action = (targetList === 'list-right') ? 'add' : 'remove';
            
            $.post('index.php?mod=kewirausahaan&act=update_anggota', {
                action: action, id_kew: id_kew, student_ids: [studentId]
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
        $.getJSON('index.php?mod=kewirausahaan&act=search_students', {id_kewirausahaan: id_kew, q: q, id_kelas: k}, function(res){
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

function editTahapan(data) {
    $('#modalTahapan .modal-title').text('Edit Tahapan');
    $('#modalTahapan form').append('<input type="hidden" name="id_tahapan" value="'+data.id_tahapan+'">');
    $('#modalTahapan [name="nama_tahapan"]').val(data.nama_tahapan);
    $('#modalTahapan [name="urutan"]').val(data.urutan);
    $('#modalTahapan [name="tanggal_mulai"]').val(data.tanggal_mulai);
    $('#modalTahapan [name="tanggal_selesai"]').val(data.tanggal_selesai);
    $('#modalTahapan [name="status"]').val(data.status);
    $('#modalTahapan [name="keterangan"]').val(data.keterangan);
    $('#modalTahapan').modal('show');
}

function editAgenda(data) {
    $('#modalAgenda .modal-title').text('Edit Agenda Kegiatan');
    $('#modalAgenda form').append('<input type="hidden" name="id_agenda" value="'+data.id_agenda+'">');
    $('#modalAgenda [name="tanggal"]').val(data.tanggal);
    $('#modalAgenda [name="nama_kegiatan"]').val(data.nama_kegiatan);
    $('#modalAgenda [name="lokasi"]').val(data.lokasi);
    $('#modalAgenda [name="keterangan"]').val(data.keterangan);
    $('#modalAgenda').modal('show');
}

$('#modalAgenda').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $(this).find('.modal-title').text('Tambah Agenda Kegiatan');
    $(this).find('input[name="id_agenda"]').remove();
});

$('#modalTahapan').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $(this).find('.modal-title').text('Tambah Tahapan');
    $(this).find('input[name="id_tahapan"]').remove();
});
</script>
