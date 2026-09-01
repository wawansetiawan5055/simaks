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
    .kanan-style { background-color: #c8e6c9 !important; border-color: #a5d6a7 !important; }

     /* Preview Modal Styles */
    .modal-preview-content { height: 80vh; }
    iframe.preview-frame { width: 100%; height: 100%; border: none; }
    
    .img-gallery {
        height: 180px; 
        object-fit: cover; 
        width: 100%;
        border-radius: 4px;
        cursor: pointer;
        transition: transform .2s;
    }
    .img-gallery:hover { transform: scale(1.05); }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-pray mr-2"></i> Pembiasaan: <strong><?= htmlspecialchars($pembiasaan['nama_kegiatan']) ?></strong></h1>
            </div>
            <div class="col-sm-6 text-end">
                <a href="<?= BASE_URL ?>pembiasaan" class="btn btn-secondary btn-sm float-right">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-success card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="pembiasaanTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'program' || $tab == '' || $tab == 'kegiatan') ? 'active' : '' ?>" href="<?= BASE_URL ?>pembiasaan/index/<?= $id ?>/program">
                            <i class="fas fa-file-signature"></i> Program Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'anggota') ? 'active' : '' ?>" href="<?= BASE_URL ?>pembiasaan/index/<?= $id ?>/anggota">
                            <i class="fas fa-users"></i> Anggota
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'jurnal') ? 'active' : '' ?>" href="<?= BASE_URL ?>pembiasaan/index/<?= $id ?>/jurnal">
                            <i class="fas fa-book"></i> Jurnal & Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'galeri') ? 'active' : '' ?>" href="<?= BASE_URL ?>pembiasaan/index/<?= $id ?>/galeri">
                            <i class="fas fa-images"></i> Galeri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($tab == 'penilaian' || $tab == 'nilai') ? 'active' : '' ?>" href="<?= BASE_URL ?>pembiasaan/index/<?= $id ?>/penilaian">
                            <i class="fas fa-star"></i> Penilaian
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: PROGRAM KERJA -->
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
                                                                        <a href="<?= BASE_URL ?>pembiasaan/agenda_delete?id_pembiasaan=<?= $id ?>&id_agenda=<?= $ag['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus" style="line-height: 1;"><i class="fas fa-trash fa-xs"></i></a>
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
                                                                        <a href="<?= BASE_URL ?>pembiasaan/agenda_delete?id_pembiasaan=<?= $id ?>&id_agenda=<?= $pr['id_agenda'] ?>" class="btn btn-xs btn-danger btn-delete-confirm p-1" title="Hapus File" style="line-height: 1;">
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
                            
                            <!-- Right: Current Members -->
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
                             <h5>Jurnal & Absensi Kegiatan</h5>
                             <div>
                                 <a href="<?= BASE_URL ?>pembiasaan/rekap_form?id_pembiasaan=<?= $id ?>" class="btn btn-outline-primary btn-sm mr-1">
                                    <i class="fas fa-table"></i> Rekap Absensi Manual
                                </a>
                                 <a href="<?= BASE_URL ?>pembiasaan/jurnal_form?id_pembiasaan=<?= $id ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Isi Jurnal & Absensi Harian
                                </a>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Materi / Kegiatan</th>
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
                                            $presensi = PembiasaanModel::getPresensi($pdo, $ji['id_jurnal']);
                                            $hadir_count = 0;
                                            foreach($presensi as $status) if($status == 'H') $hadir_count++;
                                        ?>
                                            <tr>
                                                <td><?= DateHelper::formatTanggal($ji['tanggal'], 'short') ?></td>
                                                <td><?= htmlspecialchars($ji['materi']) ?></td>
                                                <td><?= htmlspecialchars($ji['keterangan']) ?></td>
                                                <td><?= $hadir_count ?> Siswa</td>
                                                <td>
                                                    <a href="<?= BASE_URL ?>pembiasaan/jurnal_form?id_pembiasaan=<?= $id ?>&id_jurnal=<?= $ji['id_jurnal'] ?>" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                                    <a href="<?= BASE_URL ?>pembiasaan/jurnal_delete?id_pembiasaan=<?= $id ?>&id_jurnal=<?= $ji['id_jurnal'] ?>" class="btn btn-xs btn-danger" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- TAB 4: GALERI -->
                    <?php if ($tab == 'galeri'): ?>
                    <div class="tab-pane fade show active" id="galeri" role="tabpanel">
                        <div class="mb-3">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalGaleri">
                                <i class="fas fa-camera"></i> Upload Foto
                            </button>
                        </div>
                        <div class="row">
                            <?php if (empty($galeri_list)): ?>
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="fas fa-images fa-3x mb-2"></i><br>Belum ada foto galeri.
                                </div>
                            <?php else: ?>
                                <?php foreach ($galeri_list as $g): ?>
                                    <div class="col-md-3 col-sm-6 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="position-relative">
                                                <img src="<?= $g['file_path'] ?>" class="card-img-top img-gallery" alt="<?= htmlspecialchars($g['judul']) ?>" onclick="previewFile('<?= $g['file_path'] ?>', 'image', '<?= htmlspecialchars($g['judul']) ?>')">
                                                <a href="<?= BASE_URL ?>pembiasaan/galeri_delete?id_pembiasaan=<?= $id ?>&id_galeri=<?= $g['id_galeri'] ?>" class="btn btn-danger btn-xs position-absolute" style="top:5px; right:5px; opacity:0.8;" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></a>
                                            </div>
                                            <div class="card-body p-2 text-center">
                                                <small class="text-muted d-block"><?= DateHelper::formatTanggal($g['created_at'], 'short') ?></small>
                                                <strong><?= htmlspecialchars($g['judul']) ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- TAB 5: PENILAIAN -->
                    <?php if ($tab == 'penilaian'): 
                        // Helper function to calculate grade
                        function hitungNilai($persen) {
                            if ($persen >= 90) return 'A';
                            if ($persen >= 80) return 'B';
                            if ($persen >= 70) return 'C';
                            return 'D';
                        }
                        
                        function getDeskripsiNilai($nilai) {
                            $desk = [
                                'A' => 'Sangat Baik - Kehadiran sangat konsisten dan menunjukkan komitmen tinggi',
                                'B' => 'Baik - Kehadiran baik dan menunjukkan komitmen yang cukup',
                                'C' => 'Cukup - Kehadiran cukup namun perlu ditingkatkan',
                                'D' => 'Kurang - Kehadiran kurang dan perlu perhatian khusus'
                            ];
                            return $desk[$nilai] ?? '';
                        }
                    ?>
                    <div class="tab-pane fade show active" id="penilaian">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <form action="" method="GET" class="form-inline">
                                    <input type="hidden" name="mod" value="pembiasaan">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="tab" value="penilaian">
                                    
                                    <label class="mr-2">Bulan:</label>
                                    <select name="bulan" class="form-control mr-3" onchange="this.form.submit()">
                                        <?php 
                                        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                                        foreach($months as $k=>$v): ?>
                                        <option value="<?= $k ?>" <?= $bulan==$k?'selected':'' ?>><?= $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <label class="mr-2">Tahun:</label>
                                    <select name="tahun" class="form-control mr-3" onchange="this.form.submit()">
                                        <?php for($y=date('Y'); $y>=2020; $y--): ?>
                                        <option value="<?= $y ?>" <?= $tahun==$y?'selected':'' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                            </div>
                            
                            <form action="<?= BASE_URL ?>pembiasaan/penilaian_save" method="POST">
                                <input type="hidden" name="id_pembiasaan" value="<?= $id ?>">
                                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                                
                                <div class="card-body p-0 table-responsive">
                                    <?php
                                    // Ekstrak list kelas unik untuk filter
                                    $kelas_list_penilaian = [];
                                    if (!empty($anggota_list)) {
                                        foreach ($anggota_list as $a) {
                                            if (!empty($a['nama_kelas']) && !in_array($a['nama_kelas'], $kelas_list_penilaian)) {
                                                $kelas_list_penilaian[] = $a['nama_kelas'];
                                            }
                                        }
                                        sort($kelas_list_penilaian);
                                    }
                                    ?>
                                    <?php if (!empty($kelas_list_penilaian)): ?>
                                    <div class="p-3 bg-light border-bottom">
                                        <label class="mb-0 mr-2">Filter Kelas:</label>
                                        <select id="filter_kelas_penilaian" class="form-control form-control-sm d-inline-block w-auto" onchange="filterKelasPenilaian()">
                                            <option value="all">Semua Kelas</option>
                                            <?php foreach ($kelas_list_penilaian as $k): ?>
                                                <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($k) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                    <table class="table table-bordered table-striped table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Siswa</th>
                                                <th width="12%">NIPD/NISN</th>
                                                <th width="10%">% Kehadiran</th>
                                                <th width="8%">Nilai</th>
                                                <th width="25%">Deskripsi</th>
                                                <th width="15%">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($anggota_list)): ?>
                                                <tr><td colspan="7" class="text-center p-3">Belum ada anggota terdaftar.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($anggota_list as $i => $a): 
                                                    $id_siswa = $a['id_siswa'];
                                                    $rekap = $rekap_hybrid[$id_siswa] ?? ['jml_H'=>0, 'jml_S'=>0, 'jml_I'=>0, 'jml_A'=>0];
                                                    $total = $rekap['jml_H'] + $rekap['jml_S'] + $rekap['jml_I'] + $rekap['jml_A'];
                                                    $persen = $total > 0 ? round(($rekap['jml_H'] / $total) * 100, 1) : 0;
                                                    
                                                    // Check if stored penilaian exists
                                                    $stored = $penilaian_stored[$id_siswa] ?? null;
                                                    $nilai = $stored ? $stored['nilai'] : hitungNilai($persen);
                                                    $deskripsi = $stored ? $stored['deskripsi'] : getDeskripsiNilai($nilai);
                                                    $catatan = $stored ? $stored['catatan'] : '';
                                                    
                                                    // Color coding
                                                    $color_class = '';
                                                    if ($persen >= 90) $color_class = 'text-success font-weight-bold';
                                                    elseif ($persen >= 80) $color_class = 'text-success font-weight-bold';
                                                    elseif ($persen >= 70) $color_class = 'text-warning font-weight-bold';
                                                    else $color_class = 'text-danger font-weight-bold';
                                                ?>
                                                <tr class="penilaian-row" data-kelas="<?= htmlspecialchars($a['nama_kelas'] ?? '') ?>">
                                                    <td class="text-center nomor-urut"><?= $i + 1 ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($a['nama_siswa']) ?></strong><br>
                                                        <small class="text-muted"><?= htmlspecialchars($a['nama_kelas'] ?? '-') ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <small><?= htmlspecialchars($a['nisn'] ?? '-') ?></small>
                                                    </td>
                                                    <td class="text-center align-middle <?= $color_class ?>">
                                                        <?= $persen ?>%
                                                        <input type="hidden" name="penilaian[<?= $id_siswa ?>][persentase]" value="<?= $persen ?>">
                                                    </td>
                                                    <td class="text-center">
                                                        <select name="penilaian[<?= $id_siswa ?>][nilai]" class="form-control form-control-sm nilai-select" data-siswa="<?= $id_siswa ?>" required>
                                                            <option value="A" <?= $nilai=='A'?'selected':'' ?>>A</option>
                                                            <option value="B" <?= $nilai=='B'?'selected':'' ?>>B</option>
                                                            <option value="C" <?= $nilai=='C'?'selected':'' ?>>C</option>
                                                            <option value="D" <?= $nilai=='D'?'selected':'' ?>>D</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <textarea name="penilaian[<?= $id_siswa ?>][deskripsi]" class="form-control form-control-sm deskripsi-field" data-siswa="<?= $id_siswa ?>" rows="2" required><?= htmlspecialchars($deskripsi) ?></textarea>
                                                    </td>
                                                    <td>
                                                        <textarea name="penilaian[<?= $id_siswa ?>][catatan]" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($catatan) ?></textarea>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-save"></i> Simpan Penilaian Bulan <?= $months[$bulan] ?> <?= $tahun ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <script>
                    $(document).ready(function() {
                        const deskripsiMap = {
                            'A': 'Sangat Baik - Kehadiran sangat konsisten dan menunjukkan komitmen tinggi',
                            'B': 'Baik - Kehadiran baik dan menunjukkan komitmen yang cukup',
                            'C': 'Cukup - Kehadiran cukup namun perlu ditingkatkan',
                            'D': 'Kurang - Kehadiran kurang dan perlu perhatian khusus'
                        };
                        
                        $('.nilai-select').on('change', function() {
                            const siswaId = $(this).data('siswa');
                            const nilai = $(this).val();
                            const deskField = $(`.deskripsi-field[data-siswa="${siswaId}"]`);
                            
                            // Only update if current value matches default description
                            const currentDesc = deskField.val();
                            const isDefaultDesc = Object.values(deskripsiMap).includes(currentDesc);
                            
                            if (isDefaultDesc || currentDesc === '') {
                                deskField.val(deskripsiMap[nilai]);
                            }
                        });
                    });

                    function filterKelasPenilaian() {
                        var selected = document.getElementById('filter_kelas_penilaian').value;
                        var rows = document.querySelectorAll('.penilaian-row');
                        var visibleCount = 0;
                        
                        rows.forEach(function(row) {
                            if (selected === 'all' || row.getAttribute('data-kelas') === selected) {
                                row.style.display = '';
                                visibleCount++;
                                // Update nomor urut agar tetap rapi saat di-filter
                                row.querySelector('.nomor-urut').textContent = visibleCount;
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }
                    </script>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODALS -->

<!-- Modal Upload Proker (Work Program Only) -->
<div class="modal fade" id="modalUploadProker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>pembiasaan/program_upload" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_pembiasaan" value="<?= $id ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Program Kerja</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Program Kerja</label>
                        <input type="text" name="nama_kegiatan_baru" class="form-control" placeholder="Contoh: Program Semester Genap" required>
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
            <form action="<?= BASE_URL ?>pembiasaan/agenda_save" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_pembiasaan" value="<?= $id ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Agenda Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="edit_id_agenda_wrapper">
                        <!-- ID Agenda for editing -->
                        <input type="hidden" name="id_agenda" id="id_agenda">
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Nama Agenda / Kegiatan</label>
                        <input type="text" name="nama_agenda" class="form-control" required placeholder="Contoh: Kajian Kitab Bulanan">
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
<div class="modal fade" id="modalGaleri" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>pembiasaan/galeri_upload" method="post" enctype="multipart/form-data" id="formGaleriPem">
                <input type="hidden" name="id_pembiasaan" value="<?= $id ?>">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-camera text-primary mr-1"></i> Dokumentasi Foto Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="stopPemCamera()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">Judul / Caption Kegiatan</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Kegiatan Jumat Berkah / Sholat Berjamaah">
                    </div>
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Foto Dokumentasi</label>
                        <ul class="nav nav-pills mb-2" role="tablist" style="display: flex; gap: 4px;">
                            <li class="nav-item" style="flex: 1;">
                                <a class="nav-link active text-center font-weight-bold" id="tab-pem-upload-link" data-toggle="pill" href="#tab-pem-upload" role="tab" onclick="stopPemCamera()" style="font-size: 0.78rem; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px;">
                                    <i class="fas fa-folder-open mr-1"></i> Unggah
                                </a>
                            </li>
                            <li class="nav-item" style="flex: 1;">
                                <a class="nav-link text-center font-weight-bold" id="tab-pem-camera-link" data-toggle="pill" href="#tab-pem-camera" role="tab" onclick="startPemCamera()" style="font-size: 0.78rem; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px;">
                                    <i class="fas fa-camera mr-1"></i> Kamera
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content border rounded p-2 bg-light" style="border-radius: 10px;">
                            <!-- Tab Upload -->
                            <div class="tab-pane fade show active" id="tab-pem-upload" role="tabpanel">
                                <div class="custom-file">
                                    <input type="file" name="file_upload" class="custom-file-input" id="pemFileUpload" accept="image/*" onchange="previewPemFile(this)">
                                    <label class="custom-file-label" for="pemFileUpload" style="font-size:0.8rem;">Pilih file gambar...</label>
                                </div>
                                <img id="previewPemImg" class="img-fluid rounded mt-2 shadow-sm" style="display:none; max-height:160px; width:100%; object-fit:cover;">
                            </div>

                            <!-- Tab Camera -->
                            <div class="tab-pane fade" id="tab-pem-camera" role="tabpanel">
                                <div style="background:#0f172a; border-radius:8px; overflow:hidden; position:relative; text-align:center;">
                                    <video id="pemVideo" autoplay playsinline muted style="width:100%; max-height:180px; object-fit:cover; display:none; background:#000;"></video>
                                    <canvas id="pemCanvas" style="display:none;"></canvas>

                                    <div id="pemControls" class="p-2 d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px; background: rgba(15, 23, 42, 0.9); display: none !important;">
                                        <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 font-weight-bold" id="btnSnapPem" onclick="takePemSnapshot()">
                                            <i class="fas fa-camera mr-1"></i> Jepret
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-light rounded-pill px-2.5 font-weight-bold" onclick="switchPemFacing()">
                                            <i class="fas fa-sync-alt mr-1"></i> Balik
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 font-weight-bold" id="btnRetakePem" onclick="retakePemSnapshot()" style="display:none;">
                                            <i class="fas fa-redo mr-1"></i> Ulangi
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-1.5 text-center">
                                    <input type="file" id="nativePemInput" accept="image/*" capture="environment" style="display:none;" onchange="previewNativePem(this)">
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-3 font-weight-bold btn-block" onclick="document.getElementById('nativePemInput').click()">
                                        <i class="fas fa-camera-retro mr-1"></i> Buka Aplikasi Kamera
                                    </button>
                                </div>

                                <div id="pemCapturedBox" class="mt-2 text-center" style="display:none;">
                                    <small class="text-success font-weight-bold d-block mb-1"><i class="fas fa-check-circle mr-1"></i> Foto Siap Disimpan:</small>
                                    <img id="pemCapturedPreview" class="img-fluid rounded shadow-sm" style="max-height:140px; width:100%; object-fit:cover;" src="">
                                </div>
                                <input type="hidden" name="foto_cam_data" id="fotoPemCamData" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="stopPemCamera()">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content modal-preview-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Preview File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="previewBody">
                <!-- Content here -->
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- SCRIPTS -->
<?php if ($tab == 'anggota'): ?>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    const id_pem = <?= $pembiasaan['id_pembiasaan'] ?>;
    
    // Sortable Logic
    $("#list-left, #list-right").sortable({
        connectWith: ".connectedSortable",
        placeholder: "ui-sortable-placeholder student-item",
        receive: function(event, ui) {
            const targetList = $(this).attr('id');
            const studentId = ui.item.data('id');
            let action = (targetList === 'list-right') ? 'add' : 'remove';
            
            $.post('<?= BASE_URL ?>pembiasaan/update_anggota', {
                action: action, id_pembiasaan: id_pem, student_ids: [studentId]
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
        $.getJSON('<?= BASE_URL ?>pembiasaan/search_students', {id_pembiasaan: id_pem, q: q, id_kelas: k}, function(res){
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
function previewFile(url, type, title) {
    $('#previewTitle').text(title);
    if(type === 'image') {
        $('#previewBody').html(`<div class="text-center p-3"><img src="${url}" class="img-fluid" style="max-height: 75vh;"></div>`);
    } else {
        $('#previewBody').html(`<iframe class="preview-frame" src="${url}"></iframe>`);
    }
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

function confirmDelete(e) {
    if(!confirm('Apakah anda yakin ingin menghapus data ini?')) e.preventDefault();
}

// ============================================================
// 📸 LIVE CAMERA HANDLER UNTUK MODAL GALERI PEMBIASAAN
// ============================================================
let pemStream = null;
let pemFacing = "environment";

function previewPemFile(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => {
            $('#previewPemImg').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function startPemCamera() {
    stopPemCamera();
    const video = document.getElementById('pemVideo');
    const controls = document.getElementById('pemControls');
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        document.getElementById('nativePemInput').click();
        return;
    }

    video.muted = true;
    video.setAttribute('playsinline', '');
    video.setAttribute('autoplay', '');
    video.setAttribute('muted', '');

    try {
        pemStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: pemFacing, width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false
        });
    } catch (e) {
        try {
            pemStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        } catch (err) {
            console.warn("Camera stream failed", err);
            document.getElementById('nativePemInput').click();
            return;
        }
    }

    if (pemStream) {
        video.srcObject = pemStream;
        video.onloadedmetadata = () => {
            video.play();
            video.style.display = 'block';
            controls.style.setProperty('display', 'flex', 'important');
        };
    }
}

function stopPemCamera() {
    if (pemStream) {
        pemStream.getTracks().forEach(t => t.stop());
        pemStream = null;
    }
    const video = document.getElementById('pemVideo');
    if (video) {
        video.srcObject = null;
        video.style.display = 'none';
    }
    const controls = document.getElementById('pemControls');
    if (controls) controls.style.setProperty('display', 'none', 'important');
}

function switchPemFacing() {
    pemFacing = (pemFacing === "environment") ? "user" : "environment";
    startPemCamera();
}

function takePemSnapshot() {
    const video = document.getElementById('pemVideo');
    const canvas = document.getElementById('pemCanvas');
    const preview = document.getElementById('pemCapturedPreview');
    const previewBox = document.getElementById('pemCapturedBox');
    const hiddenInput = document.getElementById('fotoPemCamData');
    const btnSnap = document.getElementById('btnSnapPem');
    const btnRetake = document.getElementById('btnRetakePem');

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

function retakePemSnapshot() {
    const video = document.getElementById('pemVideo');
    const previewBox = document.getElementById('pemCapturedBox');
    const hiddenInput = document.getElementById('fotoPemCamData');
    const btnSnap = document.getElementById('btnSnapPem');
    const btnRetake = document.getElementById('btnRetakePem');

    hiddenInput.value = '';
    previewBox.style.display = 'none';
    btnSnap.style.display = 'inline-block';
    btnRetake.style.display = 'none';
    video.play();
}

function previewNativePem(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = e => {
            let base64 = e.target.result;
            document.getElementById('fotoPemCamData').value = base64;
            document.getElementById('pemCapturedPreview').src = base64;
            document.getElementById('pemCapturedBox').style.display = 'block';
            stopPemCamera();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$('#modalGaleri').on('hidden.bs.modal', function () {
    stopPemCamera();
});
</script>
