<?php
// Tahfidz Detail Tabs
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
                <h1>Tahfidz Qur'an: <strong><?= htmlspecialchars($tahfidz['nama_kelompok']) ?></strong></h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="<?= BASE_URL ?>tahfidz" class="btn btn-secondary btn-sm float-right">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="tahfidzTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'program' || $tab == '') ? 'active' : '' ?>" href="<?= BASE_URL ?>tahfidz/index/<?= $id ?>/program">
                            <i class="fas fa-file-signature"></i> Program Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'anggota') ? 'active' : '' ?>" href="<?= BASE_URL ?>tahfidz/index/<?= $id ?>/anggota">
                            <i class="fas fa-users"></i> Anggota
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'jurnal') ? 'active' : '' ?>" href="<?= BASE_URL ?>tahfidz/index/<?= $id ?>/jurnal">
                            <i class="fas fa-book"></i> Jurnal Umum
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'setoran') ? 'active' : '' ?>" href="<?= BASE_URL ?>tahfidz/index/<?= $id ?>/setoran">
                            <i class="fas fa-microphone-alt"></i> Setoran Hafalan
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: PROGRAM KERJA -->
                    <?php if ($tab == 'program' || $tab == ''): ?>
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
                                                                    <strong><?= DateHelper::formatTanggal($ag['tanggal'], 'short') ?></strong><br>
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
                                                                        <a href="<?= BASE_URL ?>tahfidz/agenda_delete?id_tahfidz=<?= $id ?>&id_agenda=<?= $ag['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus" style="line-height: 1;"><i class="fas fa-trash fa-xs"></i></a>
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
                                                                        <a href="<?= BASE_URL ?>tahfidz/agenda_delete?id_tahfidz=<?= $id ?>&id_agenda=<?= $pr['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus File" style="line-height: 1;">
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

                    <!-- TAB 2: ANGGOTA (Drag & Drop) -->
                     <?php if ($tab == 'anggota'): ?>
                    <div class="tab-pane fade show active" id="anggota" role="tabpanel">
                         <div class="row">
                            <!-- Left: Available Students -->
                            <div class="col-md-6">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Daftar Siswa (Tersedia)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <select id="filter-kelas" class="form-control form-control-sm">
                                                    <option value="">Semua Kelas</option>
                                                    <?php foreach ($kelas_list as $k): ?>
                                                        <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" id="search-left" class="form-control form-control-sm" placeholder="Cari Nama...">
                                            </div>
                                        </div>
                                        <div id="sourceList" class="student-list kiri-style connectedSortable">
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
                            
                            <!-- Right: Current Members -->
                            <div class="col-md-6">
                                <div class="card card-success card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Anggota Tahfidz (<span id="countAnggota"><?= count($anggota_list) ?></span>)</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="input-group mb-2">
                                            <input type="text" id="search-right" class="form-control" placeholder="Cari Anggota...">
                                        </div>
                                        <div id="targetList" class="student-list kanan-style connectedSortable">
                                            <?php foreach ($anggota_list as $m): ?>
                                                <div class="student-item" data-id="<?= $m['id_siswa'] ?>">
                                                    <div>
                                                        <strong><?= $m['nama_siswa'] ?></strong><br>
                                                        <small class="text-muted"><?= $m['nama_kelas'] ?? 'Tanpa Kelas' ?></small>
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

                    <!-- TAB 3: JURNAL UMUM -->
                      <?php if ($tab == 'jurnal'): ?>
                    <div class="tab-pane fade show active" id="jurnal" role="tabpanel">
                        <div class="mb-3 d-flex justify-content-between">
                             <h5>Jurnal Kegiatan / Murajaah Bersama</h5>
                             <a href="<?= BASE_URL ?>tahfidz/jurnal_form?id_tahfidz=<?= $id ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Isi Jurnal & Absensi
                            </a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Materi Global</th>
                                        <th>Keterangan</th>
                                        <th>Hadir</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($jurnal_list)): ?>
                                        <tr><td colspan="5" class="text-center">Belum ada jurnal.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($jurnal_list as $ji): 
                                            // Get Presensi Count
                                            $presensi = TahfidzModel::getPresensi($pdo, $ji['id_jurnal']);
                                            $hadir_count = 0;
                                            foreach($presensi as $status) if($status == 'H') $hadir_count++;
                                        ?>
                                            <tr>
                                                <td><?= DateHelper::formatTanggal($ji['tanggal'], 'short') ?></td>
                                                <td><?= htmlspecialchars($ji['materi']) ?></td>
                                                <td><?= htmlspecialchars($ji['keterangan']) ?></td>
                                                <td><?= $hadir_count ?> Siswa</td>
                                                <td>
                                                    <a href="<?= BASE_URL ?>tahfidz/jurnal_form?id_tahfidz=<?= $id ?>&id_jurnal=<?= $ji['id_jurnal'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                                    <a href="<?= BASE_URL ?>tahfidz/jurnal_delete?id_tahfidz=<?= $id ?>&id_jurnal=<?= $ji['id_jurnal'] ?>" class="btn btn-xs btn-danger" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>



                    <!-- TAB 4: SETORAN HAFALAN -->
                     <?php if ($tab == 'setoran'): ?>
                    <div class="tab-pane fade show active" id="setoran" role="tabpanel">
                        <div class="row">
                            <!-- Left: List Siswa (Collapsible) -->
                            <div class="col-md-3 border-right collapse show" id="listSiswaCol">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-muted mb-0">Pilih Siswa</h6>
                                    <button class="btn btn-xs btn-outline-secondary" onclick="$('#listSiswaCol').collapse('hide'); $('#showListBtn').show();"><i class="fas fa-angle-left"></i></button>
                                </div>
                                
                                <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                                    <?php foreach($anggota_list as $s): 
                                        $isActive = (isset($_GET['id_siswa']) && $_GET['id_siswa'] == $s['id_siswa']);
                                    ?>
                                        <a href="<?= BASE_URL ?>tahfidz/index?id=<?= $id ?>&tab=setoran&id_siswa=<?= $s['id_siswa'] ?>" 
                                            class="list-group-item list-group-item-action <?= $isActive?'active':'' ?>">
                                            <?= $s['nama_siswa'] ?>
                                            <br><small><?= $s['nama_kelas'] ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- Right: Content -->
                            <div class="col-md-9" id="contentCol">
                                <button id="showListBtn" class="btn btn-sm btn-outline-secondary mb-3" style="display: none;" onclick="$('#listSiswaCol').collapse('show'); $(this).hide();">
                                    <i class="fas fa-users"></i> Tampilkan Siswa
                                </button>

                                <?php if(isset($_GET['id_siswa'])): 
                                    // Find student name for header
                                    $curr_siswa_name = '';
                                    foreach($anggota_list as $s) if($s['id_siswa'] == $_GET['id_siswa']) $curr_siswa_name = $s['nama_siswa'];
                                    
                                    // Filter Jenis Setoran from URL
                                    $curr_jenis = $_GET['jenis'] ?? 'Harian';
                                ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Riwayat: <strong><?= $curr_siswa_name ?></strong></h5>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalSetoran">
                                            <i class="fas fa-plus"></i> Tambah Setoran
                                        </button>
                                    </div>
                                    
                                    <!-- TAB NAV FOR JENIS SETORAN -->
                                    <ul class="nav nav-tabs mb-3">
                                        <li class="nav-item">
                                            <a class="nav-link <?= $curr_jenis == 'Harian' ? 'active' : '' ?>" href="<?= BASE_URL ?>tahfidz/index?id=<?= $id ?>&tab=setoran&id_siswa=<?= $_GET['id_siswa'] ?>&jenis=Harian">Hafalan Harian</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?= $curr_jenis == 'Ujian' ? 'active' : '' ?>" href="<?= BASE_URL ?>tahfidz/index?id=<?= $id ?>&tab=setoran&id_siswa=<?= $_GET['id_siswa'] ?>&jenis=Ujian">Ujian / Evaluasi</a>
                                        </li>
                                    </ul>
        
                                    <!-- TABLE RIWAYAT -->
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead class="bg-light text-center">
                                                <tr>
                                                    <th rowspan="2" class="align-middle">Tanggal</th>
                                                    <th rowspan="2" class="align-middle">Surah (Juz)</th>
                                                    <th rowspan="2" class="align-middle">Ayat</th>
                                                    <th colspan="4">Penilaian</th>
                                                    <th rowspan="2" class="align-middle">Keterangan / Catatan</th>
                                                    <th rowspan="2" class="align-middle" width="70">Aksi</th>
                                                </tr>
                                                <tr>
                                                    <th width="30" title="Hafalan">H</th>
                                                    <th width="30" title="Tajwid">T</th>
                                                    <th width="30" title="Makhroj">M</th>
                                                    <th width="30" title="Naghom">N</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($setoran_list)): ?>
                                                    <tr><td colspan="9" class="text-center text-muted">Belum ada data setoran (<?= $curr_jenis ?>).</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($setoran_list as $st): ?>
                                                        <tr>
                                                            <td class="text-center"><?= date('d/m/y', strtotime($st['tanggal'])) ?></td>
                                                            <td><?= $st['nama_surah'] ?> <small class="text-muted">(Juz <?= $st['juz'] ?>)</small></td>
                                                            <td class="text-center"><?= $st['ayat_awal'] ?> - <?= $st['ayat_akhir'] ?></td>
                                                            <td class="text-center"><b><?= $st['nilai_hafal'] ?? '-' ?></b></td>
                                                            <td class="text-center"><b><?= $st['nilai_tajwid'] ?? '-' ?></b></td>
                                                            <td class="text-center"><b><?= $st['nilai_makhroj'] ?? '-' ?></b></td>
                                                            <td class="text-center"><b><?= $st['nilai_naghom'] ?? '-' ?></b></td>
                                                            <td class="small">
                                                                <?php if($st['keterangan']): ?>
                                                                    <div class="text-muted" style="border-bottom: 1px dashed #eee; margin-bottom: 2px;"><?= htmlspecialchars($st['keterangan']) ?></div>
                                                                <?php endif; ?>
                                                                <?php if($st['catatan_guru']): ?>
                                                                    <div class="text-primary font-italic"><i class="fas fa-comment-dots"></i> <?= htmlspecialchars($st['catatan_guru']) ?></div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php 
                                                                    // FIX: Escape JSON correctly manually to avoid quotes breaking HTML
                                                                    $jsonData = htmlspecialchars(json_encode($st), ENT_QUOTES, 'UTF-8');
                                                                ?>
                                                                <button class="btn btn-xs btn-info" onclick="editSetoran(<?= $jsonData ?>)"><i class="fas fa-edit"></i></button>
                                                                <a href="<?= BASE_URL ?>tahfidz/delete_setoran?id_tahfidz=<?= $id ?>&id_setoran=<?= $st['id_setoran'] ?>&id_siswa=<?= $_GET['id_siswa'] ?>&jenis=<?= $curr_jenis ?>" class="btn btn-xs btn-danger" onclick="return confirmDelete(event)"><i class="fas fa-trash-alt"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
        
                                <?php else: ?>
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-arrow-left fa-2x mb-2"></i><br>
                                        Pilih siswa di sebelah kiri untuk melihat dan input setoran hafalan.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        // Simple script to adjust column width when collapsed
                        document.addEventListener("DOMContentLoaded", function(){
                            $('#listSiswaCol').on('hidden.bs.collapse', function () {
                                $('#contentCol').removeClass('col-md-9').addClass('col-md-12');
                            });
                            $('#listSiswaCol').on('shown.bs.collapse', function () {
                                $('#contentCol').removeClass('col-md-12').addClass('col-md-9');
                            });
                        });
                    </script>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Upload Proker (Work Program Only) -->
<div class="modal fade" id="modalUploadProker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>tahfidz/program_upload" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_tahfidz" value="<?= $id ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Program Kerja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Program Kerja</label>
                        <input type="text" name="nama_kegiatan_baru" class="form-control" placeholder="Contoh: Program Kerja Tahfidz 2024" required>
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

<!-- Modal Agenda (Shared add & edit) -->
<div class="modal fade" id="modalAgenda" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>tahfidz/agenda_save" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_tahfidz" value="<?= $id ?>">
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
                        <input type="text" name="nama_agenda" class="form-control" required placeholder="Misal: Ujian Kenaikan Juz...">
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
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

<!-- Modal Preview -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 modal-preview-content" id="previewBody">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Jurnal -->
<div class="modal fade" id="modalSetoran" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-top-primary">
            <form action="<?= BASE_URL ?>tahfidz/save_setoran" method="post">
                <input type="hidden" name="id_tahfidz" value="<?= $id ?>">
                <input type="hidden" name="id_siswa" value="<?= $_GET['id_siswa'] ?? '' ?>">
                <input type="hidden" name="id_setoran" id="setoran_id">
                <div class="modal-header">
                    <h5 class="modal-title">Input Setoran Hafalan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- JENIS SETORAN -->
                    <div class="form-group text-center bg-light p-2 rounded mb-3">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="jenisHarian" name="jenis_setoran" class="custom-control-input" value="Harian" checked>
                            <label class="custom-control-label" for="jenisHarian">Hafalan Harian</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="jenisUjian" name="jenis_setoran" class="custom-control-input" value="Ujian">
                            <label class="custom-control-label font-weight-bold text-primary" for="jenisUjian">Ujian / Evaluasi</label>
                        </div>
                    </div>
                    
                    <!-- ROW 1: Tanggal & Juz -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Juz</label>
                                <select id="filter_juz" class="form-control" onchange="filterSurahByJuz()">
                                    <option value="">- Pilih Juz -</option>
                                    <?php for($i=30; $i>=1; $i--): ?>
                                        <option value="<?= $i ?>">Juz <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ROW 2: Surah & Ayat -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Surah</label>
                                <select name="id_surah" id="select_surah" class="form-control select2" required style="width: 100%;">
                                    <option value="">-Pilih Juz Terlebih Dahulu-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ayat Awal - Akhir</label>
                                <div class="input-group">
                                    <input type="number" name="ayat_awal" class="form-control" placeholder="Awal">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent border-0 font-weight-bold">s/d</span>
                                    </div>
                                    <input type="number" name="ayat_akhir" class="form-control" placeholder="Akhir">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden Status/Nilai Global (Not in requested layout, hiding or removing?) -->
                    <!-- Keeping as hidden input if needed for database compatibility, or just make it secondary -->
                    <input type="hidden" name="nilai" value="">

                    <hr>
                    
                    <!-- ROW 3: Kategori Penilaian -->
                    <h6>Kategori Penilaian <small class="text-primary">(A = Sangat Baik, D = Kurang)</small></h6>
                    <div class="row">
                        <?php 
                        $categories = [
                            'nilai_hafal' => 'Hafal',
                            'nilai_tajwid' => 'Tajwid',
                            'nilai_makhroj' => 'Makhroj',
                            'nilai_naghom' => 'Naghom'
                        ];
                        foreach ($categories as $key => $label): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= $label ?></label>
                                <select name="<?= $key ?>" class="form-control cat-score" data-label="<?= $label ?>">
                                    <option value="">-</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- ROW 4: Deskripsi -->
                    <div class="form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Deskripsi / Keterangan</label>
                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="generateAutoDesc()">
                                <i class="fas fa-magic"></i> Generate
                            </button>
                        </div>
                        <textarea name="keterangan" id="setoran_keterangan" class="form-control" rows="2" placeholder="Otomatis atau manual..."></textarea>
                    </div>

                    <!-- ROW 5: Catatan Guru -->
                    <div class="form-group mb-0">
                        <label>Catatan Guru (Private)</label>
                        <textarea name="catatan_guru" id="setoran_catatan_guru" class="form-control" rows="2" placeholder="Tulis catatan khusus jika ada..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Setoran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Jurnal -->
<div class="modal fade" id="modalJurnal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>tahfidz/jurnal_save" method="post">
                <input type="hidden" name="id_tahfidz" value="<?= $id ?>">
                <input type="hidden" name="id_jurnal" id="id_jurnal">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalJurnalTitle">Isi Jurnal Tahfidz</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="jurnal_tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Materi Global / Kegiatan</label>
                        <input type="text" name="materi" id="jurnal_materi" class="form-control" required placeholder="Misal: Murajaah Surat An-Naba...">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="jurnal_keterangan" class="form-control" rows="3"></textarea>
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
    // Store all surahs as JSON for client-side filtering
    var allSurahs = <?= json_encode($surah_list) ?>;

    function filterSurahByJuz(preselectedSurahId = null) {
        var selectedJuz = $('#filter_juz').val();
        var surahSelect = $('#select_surah');
        surahSelect.empty();
        
        if (!selectedJuz) {
            surahSelect.append('<option value="">-Pilih Juz Terlebih Dahulu-</option>');
            return;
        }

        surahSelect.append('<option value="">-Pilih Surah-</option>');
        var found = false;
        
        // Filter and populate
        allSurahs.forEach(function(surah) {
            if (surah.juz == selectedJuz) {
                var selected = (preselectedSurahId && preselectedSurahId == surah.id_surah) ? 'selected' : '';
                surahSelect.append(`<option value="${surah.id_surah}" ${selected}>${surah.nama_surah} (Ayat ${surah.jumlah_ayat})</option>`);
                if(selected) found = true;
            }
        });

        // Trigger Select2 update
        surahSelect.trigger('change');
    }

    function addJurnal() {
        $('#modalJurnalTitle').text('Isi Jurnal Tahfidz');
        $('#id_jurnal').val('');
        $('#jurnal_tanggal').val('<?= date('Y-m-d') ?>');
        $('#jurnal_materi').val('');
        $('#jurnal_keterangan').val('');
    }

    function previewFile(path, type, title = 'Preview') {
        $('#previewTitle').text(title);
        var content = '';
        if (type === 'image') {
            content = '<div class="text-center w-100 h-100 p-3"><img src="' + path + '" style="max-width: 100%; max-height: 100%; object-fit: contain;"></div>';
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

    function editSetoran(data) {
        $('#modalSetoran .modal-title').text('Edit Setoran Hafalan');
        $('#setoran_id').val(data.id_setoran);
        
        // Handle Radio Button
        if (data.jenis_setoran === 'Ujian') {
            $('#jenisUjian').prop('checked', true);
        } else {
            $('#jenisHarian').prop('checked', true);
        }

        $('#modalSetoran [name="tanggal"]').val(data.tanggal);
        
        // Handle Juz & Surah Preselection
        // We know the surah ID, but we need to find its Juz first
        var surahId = data.id_surah;
        var associatedJuz = '';
        
        for(var i=0; i<allSurahs.length; i++) {
            if(allSurahs[i].id_surah == surahId) {
                associatedJuz = allSurahs[i].juz;
                break;
            }
        }
        
        if(associatedJuz) {
            $('#filter_juz').val(associatedJuz);
            filterSurahByJuz(surahId); // This will also populate the surah dropdown
        }

        $('#modalSetoran [name="ayat_awal"]').val(data.ayat_awal);
        $('#modalSetoran [name="ayat_akhir"]').val(data.ayat_akhir);
        // $('#modalSetoran [name="nilai"]').val(data.nilai); // Hidden now
        $('#modalSetoran [name="nilai_hafal"]').val(data.nilai_hafal);
        $('#modalSetoran [name="nilai_tajwid"]').val(data.nilai_tajwid);
        $('#modalSetoran [name="nilai_makhroj"]').val(data.nilai_makhroj);
        $('#modalSetoran [name="nilai_naghom"]').val(data.nilai_naghom);
        $('#setoran_keterangan').val(data.keterangan);
        $('#setoran_catatan_guru').val(data.catatan_guru);
        $('#modalSetoran').modal('show');
    }

    $('#modalSetoran').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $('#setoran_id').val('');
        $('#jenisHarian').prop('checked', true);
        $('#filter_juz').val('').trigger('change'); // Reset Juz
        filterSurahByJuz(); // Reset Surah
        $('#modalSetoran .modal-title').text('Input Setoran Hafalan');
    });

    function generateAutoDesc() {
        var desc = [];
        $('.cat-score').each(function() {
            var val = $(this).val();
            var label = $(this).data('label');
            if (val) {
                var quality = '';
                if (val === 'A') quality = 'Sangat Baik';
                else if (val === 'B') quality = 'Baik';
                else if (val === 'C') quality = 'Cukup';
                else if (val === 'D') quality = 'Kurang';
                desc.push(label + ': ' + quality + ' (' + val + ')');
            }
        });
        
        var surahText = $('#modalSetoran [name="id_surah"] option:selected').text();
        var ayatAwal = $('#modalSetoran [name="ayat_awal"]').val();
        var ayatAkhir = $('#modalSetoran [name="ayat_akhir"]').val();
        
        var prefix = "";
        if(surahText && surahText != "-Pilih Surah-") {
            prefix = surahText + " (Ayat " + ayatAwal + "-" + ayatAkhir + "). ";
        }

        $('#setoran_keterangan').val(prefix + desc.join(', '));
    }
</script>

<?php if ($tab == 'anggota'): ?>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    var id_tah = "<?= $id ?>";

    $("#sourceList, #targetList").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-sortable-placeholder student-item",
        receive: function(event, ui) {
            var studentId = ui.item.data('id');
            var action = $(this).attr('id') == 'targetList' ? 'add' : 'remove';
            
            $.post('<?= BASE_URL ?>tahfidz/update_anggota', {
                action: action,
                id_tahfidz: id_tah,
                student_ids: [studentId]
            }, function(response) {
                if(response.status === 'success') {
                    if(action === 'add') ui.item.find('i').attr('class', 'fas fa-check text-success');
                    else ui.item.find('i').attr('class', 'fas fa-arrows-alt text-muted');
                    $("#countAnggota").text($("#targetList .student-item").length);
                } else {
                    alert('Gagal: ' + response.message);
                    $(ui.sender).sortable('cancel');
                }
            }, 'json');
        }
    }).disableSelection();

    // Search Left
    function searchStudents() {
        var q = $("#search-left").val();
        var id_kelas = $("#filter-kelas").val();
        $.get('<?= BASE_URL ?>tahfidz/search_students', { id_tahfidz: id_tah, q: q, id_kelas: id_kelas }, function(response) {
            if(response.status === 'success') {
                $("#sourceList").empty();
                $.each(response.data, function(i, s) {
                   $("#sourceList").append(`<div class="student-item" data-id="${s.id_siswa}"><div><strong>${s.nama_siswa}</strong><br><small class="text-muted">${s.nama_kelas||''}</small></div><i class="fas fa-arrows-alt text-muted"></i></div>`);
                });
            }
        }, 'json');
    }

    $("#search-left").on('keyup', function() { searchStudents(); });
    $("#filter-kelas").on('change', function() { searchStudents(); });

    // Search Right
    $('#search-right').on('keyup', function() {
        var v = $(this).val().toLowerCase();
        $("#targetList .student-item").filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1) });
    });
});
</script>
<?php endif; ?>

<?php if ($tab == 'setoran'): ?>
<script>
$(function() {
    $('.select2').select2({ theme: 'bootstrap4' });
});
</script>
<?php endif; ?>
