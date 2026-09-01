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
                <h1><i class="fas fa-running mr-2"></i> Ekstrakurikuler: <strong><?= htmlspecialchars($ekskul['nama_ekskul']) ?></strong></h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="<?= BASE_URL ?>ekskul" class="btn btn-secondary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
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
                        <a class="nav-link <?= $tab == 'program' ? 'active' : '' ?>" href="<?= BASE_URL ?>ekskul/index/<?= $ekskul['id_ekskul'] ?>/program">Program Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'anggota' || $tab == 'kegiatan' ? 'active' : '' ?>" href="<?= BASE_URL ?>ekskul/index/<?= $ekskul['id_ekskul'] ?>/anggota">Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'jurnal' ? 'active' : '' ?>" href="<?= BASE_URL ?>ekskul/index/<?= $ekskul['id_ekskul'] ?>/jurnal">Jurnal & Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'galeri' ? 'active' : '' ?>" href="<?= BASE_URL ?>ekskul/index/<?= $ekskul['id_ekskul'] ?>/galeri">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab == 'nilai' ? 'active' : '' ?>" href="<?= BASE_URL ?>ekskul/index/<?= $ekskul['id_ekskul'] ?>/nilai">Penilaian</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
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
                            <a href="<?= BASE_URL ?>ekskul/jurnal_form?id_ekskul=<?= $ekskul['id_ekskul'] ?>" class="btn btn-primary">
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
                                    $presensi = EkskulModel::getPresensi($pdo, $j['id_jurnal']);
                                    $hadir_count = 0;
                                    foreach($presensi as $status) if($status == 'H') $hadir_count++;
                                ?>
                                <tr>
                                    <td><?= DateHelper::formatTanggal($j['tanggal'], 'short') ?></td>
                                    <td><?= htmlspecialchars($j['materi']) ?></td>
                                    <td><?= $hadir_count ?> Siswa</td>
                                    <td>
                                        <a href="<?= BASE_URL ?>ekskul/jurnal_form?id_ekskul=<?= $ekskul['id_ekskul'] ?>&id_jurnal=<?= $j['id_jurnal'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                        <a href="<?= BASE_URL ?>ekskul/jurnal_delete?id_ekskul=<?= $ekskul['id_ekskul'] ?>&id_jurnal=<?= $j['id_jurnal'] ?>" class="btn btn-sm btn-danger btn-delete-confirm"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <!-- TAB PROGRAM KERJA -->
                    <?php if ($tab == 'program'): ?>
                    <div class="tab-pane fade show active">
                         <div class="mb-3">
                             <button class="btn btn-outline-primary mr-2" data-toggle="modal" data-target="#modalProgramUpload">
                                <i class="fas fa-upload"></i> Upload Program Kerja
                            </button>
                             <button class="btn btn-primary" data-toggle="modal" data-target="#modalProgramKerja">
                                <i class="fas fa-calendar-plus"></i> Tambah Agenda
                            </button>
                         </div>
                        
                        <div class="row">
                            <!-- Col Left: Agenda Table -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title m-0">Tabel Agenda</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
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
                                                $has_agenda = false;
                                                $no = 1;
                                                foreach ($program_list as $p): 
                                                    if (($p['tipe'] ?? 'agenda') == 'agenda'):
                                                        $has_agenda = true;
                                                ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td class="text-center"><?= DateHelper::formatTanggal($p['tanggal'], 'short') ?></td>
                                                        <td><?= htmlspecialchars($p['nama_kegiatan']) ?></td>
                                                        <td><?= htmlspecialchars($p['lokasi']) ?></td>
                                                        <td class="text-center"><?= htmlspecialchars($p['keterangan'] ?? '-') ?></td>
                                                        <td class="text-center">
                                                            <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                                <?php if (!empty($p['file_path'])): ?>
                                                                    <button class="btn btn-xs btn-info p-1" onclick="previewFile('<?= $p['file_path'] ?>', 'pdf', '<?= htmlspecialchars($p['nama_kegiatan']) ?>')" title="Lihat Laporan" style="line-height: 1;">
                                                                        <i class="fas fa-eye fa-xs"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                                <a href="<?= BASE_URL ?>ekskul/program_delete?id_ekskul=<?= $ekskul['id_ekskul'] ?>&id_program=<?= $p['id_program'] ?>" 
                                                                   class="btn btn-danger btn-xs btn-delete-confirm p-1" title="Hapus" style="line-height: 1;"><i class="fas fa-trash fa-xs"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                
                                                <?php if (!$has_agenda): ?>
                                                    <tr><td colspan="6" class="text-center text-muted">Belum ada agenda</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Col Right: Files List -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title m-0">Tabel File Program Kerja</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="40" class="text-center">NO</th>
                                                    <th class="text-center">NAMA FILE</th>
                                                    <th width="100" class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $has_file = false;
                                                $no_file = 1;
                                                foreach ($program_list as $p): 
                                                    if (($p['tipe'] ?? '') == 'program'): 
                                                        $has_file = true;
                                                ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no_file++ ?></td>
                                                        <td><?= htmlspecialchars($p['nama_kegiatan']) ?></td>
                                                        <td class="text-center">
                                                            <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                                                <button class="btn btn-xs btn-info p-1" onclick="previewFile('<?= $p['file_path'] ?>', 'pdf', '<?= htmlspecialchars($p['nama_kegiatan']) ?>')" title="Lihat File" style="line-height: 1;">
                                                                    <i class="fas fa-eye fa-xs"></i>
                                                                </button>
                                                                <a href="<?= BASE_URL ?>ekskul/program_delete_file?id_ekskul=<?= $ekskul['id_ekskul'] ?>&id_program=<?= $p['id_program'] ?>" 
                                                                   class="btn btn-xs btn-danger p-1" onclick="return confirmDelete(event)" title="Hapus File" style="line-height: 1;">
                                                                    <i class="fas fa-trash fa-xs"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                
                                                <?php if (!$has_file): ?>
                                                    <tr><td colspan="3" class="text-center text-muted">Belum ada file</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                        <div style="height: 180px; overflow: hidden; cursor: pointer; position: relative;" class="img-wrapper" onclick="previewFile('<?= $g['file_path'] ?>', 'image', '<?= htmlspecialchars($g['judul'] ?? 'Galeri') ?>')">
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
                                                    <a href="<?= BASE_URL ?>ekskul/galeri_delete?id_ekskul=<?= $ekskul['id_ekskul'] ?>&id_galeri=<?= $g['id_galeri'] ?>" 
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
                    <div class="tab-pane fade show active">
                        <form action="<?= BASE_URL ?>ekskul/nilai_save" method="post">
                            <input type="hidden" name="id_ekskul" value="<?= $ekskul['id_ekskul'] ?>">
                            
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Input Nilai Anggota</h5>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Penilaian</button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">NIS/NISN</th>
                                            <th>Nama Siswa</th>
                                            <th width="15%">Kelas</th>
                                            <th width="15%">Nilai / Predikat</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($nilai_list)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada anggota di ekskul ini.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($nilai_list as $i => $n): ?>
                                                <tr>
                                                    <td class="text-center"><?= $i + 1 ?></td>
                                                    <td><?= $n['nipd'] ?> / <br><small class="text-muted"><?= $n['nisn'] ?></small></td>
                                                    <td class="fw-bold"><?= htmlspecialchars($n['nama_siswa']) ?></td>
                                                    <td><?= $n['nama_kelas'] ?? '-' ?></td>
                                                    <td>
                                                        <input type="text" name="nilai[<?= $n['id_siswa'] ?>][nilai]" class="form-control" 
                                                               value="<?= htmlspecialchars($n['nilai'] ?? '') ?>" placeholder="A, B, C...">
                                                    </td>
                                                    <td>
                                                        <textarea name="nilai[<?= $n['id_siswa'] ?>][deskripsi]" class="form-control" rows="1" 
                                                                  placeholder="Catatan perkembangan..."><?= htmlspecialchars($n['deskripsi'] ?? '') ?></textarea>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (!empty($nilai_list)): ?>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Penilaian</button>
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





<!-- Modal Program Kerja (Agenda Only) -->
<div class="modal fade" id="modalProgramKerja" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>ekskul/program_save" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Agenda Program</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_ekskul" value="<?= $ekskul['id_ekskul'] ?>">
                    
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Lokasi/Tempat</label>
                        <input type="text" name="lokasi" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan kegiatan..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Laporan Kegiatan <small class="text-muted">(Opsional, PDF/DOC)</small></label>
                        <input type="file" name="file_upload" class="form-control" accept=".pdf,.doc,.docx">
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

<!-- Modal Program Upload (Work Program Only) -->
<div class="modal fade" id="modalProgramUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>ekskul/program_upload" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Program Kerja</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_ekskul" value="<?= $ekskul['id_ekskul'] ?>">
                    
                    <div class="form-group">
                        <label>Nama Program Kerja</label>
                        <input type="text" name="nama_kegiatan_baru" class="form-control" placeholder="Contoh: Program Kerja Semester 1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>File (PDF/DOC)</label>
                        <input type="file" name="file_upload" class="form-control" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Galeri -->
<div class="modal fade" id="modalGaleri" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>ekskul/galeri_save" method="post" enctype="multipart/form-data" id="formGaleriEkskul">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-camera text-primary mr-1"></i> Dokumentasi Foto Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="stopEkskulCamera()">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_ekskul" value="<?= $ekskul['id_ekskul'] ?>">
                    
                    <div class="form-group">
                        <label class="small font-weight-bold">Judul / Keterangan Foto <small class="text-muted">(Opsional)</small></label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Latihan Rutin Ekskul">
                    </div>
                    
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Foto Dokumentasi</label>
                        <ul class="nav nav-pills mb-2" role="tablist" style="display: flex; gap: 4px;">
                            <li class="nav-item" style="flex: 1;">
                                <a class="nav-link active text-center font-weight-bold" id="tab-ekskul-upload-link" data-toggle="pill" href="#tab-ekskul-upload" role="tab" onclick="stopEkskulCamera()" style="font-size: 0.78rem; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px;">
                                    <i class="fas fa-folder-open mr-1"></i> Unggah
                                </a>
                            </li>
                            <li class="nav-item" style="flex: 1;">
                                <a class="nav-link text-center font-weight-bold" id="tab-ekskul-camera-link" data-toggle="pill" href="#tab-ekskul-camera" role="tab" onclick="startEkskulCamera()" style="font-size: 0.78rem; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px;">
                                    <i class="fas fa-camera mr-1"></i> Kamera
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content border rounded p-2 bg-light" style="border-radius: 10px;">
                            <!-- Tab Upload -->
                            <div class="tab-pane fade show active" id="tab-ekskul-upload" role="tabpanel">
                                <div class="custom-file">
                                    <input type="file" name="file_upload" class="custom-file-input" id="ekskulFileUpload" accept="image/*" onchange="previewEkskulFile(this)">
                                    <label class="custom-file-label" for="ekskulFileUpload" style="font-size:0.8rem;">Pilih file gambar...</label>
                                </div>
                                <img id="previewEkskulImg" class="img-fluid rounded mt-2 shadow-sm" style="display:none; max-height:160px; width:100%; object-fit:cover;">
                            </div>

                            <!-- Tab Camera -->
                            <div class="tab-pane fade" id="tab-ekskul-camera" role="tabpanel">
                                <div style="background:#0f172a; border-radius:8px; overflow:hidden; position:relative; text-align:center;">
                                    <video id="ekskulVideo" autoplay playsinline muted style="width:100%; max-height:180px; object-fit:cover; display:none; background:#000;"></video>
                                    <canvas id="ekskulCanvas" style="display:none;"></canvas>

                                    <div id="ekskulControls" class="p-2 d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px; background: rgba(15, 23, 42, 0.9); display: none !important;">
                                        <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 font-weight-bold" id="btnSnapEkskul" onclick="takeEkskulSnapshot()">
                                            <i class="fas fa-camera mr-1"></i> Jepret
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-light rounded-pill px-2.5 font-weight-bold" onclick="switchEkskulFacing()">
                                            <i class="fas fa-sync-alt mr-1"></i> Balik
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 font-weight-bold" id="btnRetakeEkskul" onclick="retakeEkskulSnapshot()" style="display:none;">
                                            <i class="fas fa-redo mr-1"></i> Ulangi
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-1.5 text-center">
                                    <input type="file" id="nativeEkskulInput" accept="image/*" capture="environment" style="display:none;" onchange="previewNativeEkskul(this)">
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-3 font-weight-bold btn-block" onclick="document.getElementById('nativeEkskulInput').click()">
                                        <i class="fas fa-camera-retro mr-1"></i> Buka Aplikasi Kamera
                                    </button>
                                </div>

                                <div id="ekskulCapturedBox" class="mt-2 text-center" style="display:none;">
                                    <small class="text-success font-weight-bold d-block mb-1"><i class="fas fa-check-circle mr-1"></i> Foto Siap Disimpan:</small>
                                    <img id="ekskulCapturedPreview" class="img-fluid rounded shadow-sm" style="max-height:140px; width:100%; object-fit:cover;" src="">
                                </div>
                                <input type="hidden" name="foto_cam_data" id="fotoEkskulCamData" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="stopEkskulCamera()">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<?php if ($tab == 'anggota' || $tab == 'kegiatan'): ?>
<script>
$(document).ready(function() {
    const id_ekskul = <?= $ekskul['id_ekskul'] ?>;
    
    // Sortable Logic
    $("#list-left, #list-right").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-sortable-placeholder student-item",
        receive: function(event, ui) {
            const targetList = $(this).attr('id');
            const studentId = ui.item.data('id');
            let action = (targetList === 'list-right') ? 'add' : 'remove';
            
            $.post('<?= BASE_URL ?>ekskul/update_anggota', {
                action: action, id_ekskul: id_ekskul, student_ids: [studentId]
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
        $.getJSON('<?= BASE_URL ?>ekskul/search_students', {id_ekskul: id_ekskul, q: q, id_kelas: k}, function(res){
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
    // Call Global Preview Modal (defined in footer.php)
    showGlobalPreview(path, type, title);
}

// ============================================================
// 📸 LIVE CAMERA HANDLER UNTUK MODAL GALERI EKSKUL
// ============================================================
let ekskulStream = null;
let ekskulFacing = "environment";

function previewEkskulFile(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => {
            $('#previewEkskulImg').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function startEkskulCamera() {
    stopEkskulCamera();
    const video = document.getElementById('ekskulVideo');
    const controls = document.getElementById('ekskulControls');
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        document.getElementById('nativeEkskulInput').click();
        return;
    }

    video.muted = true;
    video.setAttribute('playsinline', '');
    video.setAttribute('autoplay', '');
    video.setAttribute('muted', '');

    try {
        ekskulStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: ekskulFacing, width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false
        });
    } catch (e) {
        try {
            ekskulStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        } catch (err) {
            console.warn("Camera stream failed", err);
            document.getElementById('nativeEkskulInput').click();
            return;
        }
    }

    if (ekskulStream) {
        video.srcObject = ekskulStream;
        video.onloadedmetadata = () => {
            video.play();
            video.style.display = 'block';
            controls.style.setProperty('display', 'flex', 'important');
        };
    }
}

function stopEkskulCamera() {
    if (ekskulStream) {
        ekskulStream.getTracks().forEach(t => t.stop());
        ekskulStream = null;
    }
    const video = document.getElementById('ekskulVideo');
    if (video) {
        video.srcObject = null;
        video.style.display = 'none';
    }
    const controls = document.getElementById('ekskulControls');
    if (controls) controls.style.setProperty('display', 'none', 'important');
}

function switchEkskulFacing() {
    ekskulFacing = (ekskulFacing === "environment") ? "user" : "environment";
    startEkskulCamera();
}

function takeEkskulSnapshot() {
    const video = document.getElementById('ekskulVideo');
    const canvas = document.getElementById('ekskulCanvas');
    const preview = document.getElementById('ekskulCapturedPreview');
    const previewBox = document.getElementById('ekskulCapturedBox');
    const hiddenInput = document.getElementById('fotoEkskulCamData');
    const btnSnap = document.getElementById('btnSnapEkskul');
    const btnRetake = document.getElementById('btnRetakeEkskul');

    const width = video.videoWidth || 640;
    const height = video.videoHeight || 480;
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, width, height);

    const base64 = canvas.toDataURL('image/jpeg', 0.88);
    hiddenInput.value = base64;
    preview.src = base64;
    previewBox.style.display = 'block';

    video.pause();
    btnSnap.style.display = 'none';
    btnRetake.style.display = 'inline-block';
}

function retakeEkskulSnapshot() {
    const video = document.getElementById('ekskulVideo');
    const previewBox = document.getElementById('ekskulCapturedBox');
    const hiddenInput = document.getElementById('fotoEkskulCamData');
    const btnSnap = document.getElementById('btnSnapEkskul');
    const btnRetake = document.getElementById('btnRetakeEkskul');

    hiddenInput.value = '';
    previewBox.style.display = 'none';
    btnSnap.style.display = 'inline-block';
    btnRetake.style.display = 'none';
    video.play();
}

function previewNativeEkskul(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => {
            let base64 = e.target.result;
            document.getElementById('fotoEkskulCamData').value = base64;
            document.getElementById('ekskulCapturedPreview').src = base64;
            document.getElementById('ekskulCapturedBox').style.display = 'block';
            stopEkskulCamera();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$('#modalGaleri').on('hidden.bs.modal', function () {
    stopEkskulCamera();
});
</script>
