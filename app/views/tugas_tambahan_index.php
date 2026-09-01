<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-archive"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        <?= htmlspecialchars($title ?? 'Arsip & Tugas Tambahan GTK') ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Tugas Tambahan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Tab Navigation -->
        <div class="card card-primary card-outline card-outline-tabs shadow-sm">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-arsip-link" data-toggle="pill" href="#tab-arsip" role="tab" aria-controls="tab-arsip" aria-selected="true">
                            <i class="fas fa-folder-open mr-2"></i>Arsip Dokumentasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-agenda-link" data-toggle="pill" href="#tab-agenda" role="tab" aria-controls="tab-agenda" aria-selected="false">
                            <i class="fas fa-calendar-check mr-2"></i>Agenda Kegiatan
                        </a>
                    </li>
                    <?php if ($jenis === 'walas'): ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-inventaris-link" data-toggle="pill" href="#tab-inventaris" role="tab" aria-controls="tab-inventaris" aria-selected="false">
                            <i class="fas fa-boxes mr-2"></i>Inventaris Kelas
                        </a>
                    </li>
                    <?php if (isset($walas_kelas) && $walas_kelas): ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-absensi-link" data-toggle="pill" href="#tab-absensi" role="tab" aria-controls="tab-absensi" aria-selected="false">
                            <i class="fas fa-user-check mr-2"></i>Rekap Absensi
                        </a>
                    </li>
                    <?php endif; endif; ?>
                    
                    <?php if ($jenis === 'bk'): ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-jurnal-bk-link" data-toggle="pill" href="#tab-jurnal-bk" role="tab" aria-controls="tab-jurnal-bk" aria-selected="false">
                            <i class="fas fa-journal-whills mr-2"></i>Jurnal BK
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (in_array($jenis, ['humas', 'bk'])): ?>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-galeri-link" data-toggle="pill" href="#tab-galeri" role="tab" aria-controls="tab-galeri" aria-selected="false">
                            <i class="fas fa-images mr-2"></i>Galeri
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    
                    <!-- TAB 1: ARSIP DOKUMENTASI -->
                    <div class="tab-pane fade show active" id="tab-arsip" role="tabpanel" aria-labelledby="tab-arsip-link">
                        <div class="row">
                            <!-- Form Upload -->
                            <div class="col-md-4">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Unggah Dokumen Baru</h3>
                                    </div>
                                    <form action="<?= BASE_URL ?>tugas_tambahan/upload" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="jenis" value="<?= htmlspecialchars($jenis) ?>">
                                        <div class="card-body">
                                            <div class="form-group text-sm">
                                                <label>Tahun Ajaran</label>
                                                <select name="id_ta" class="form-control" required>
                                                    <?php foreach ($tahun_ajaran as $ta): ?>
                                                        <option value="<?= $ta['id_ta'] ?>" <?= $ta['id_ta'] == $id_ta ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($ta['nama_ta']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Kategori Dokumen</label>
                                                <select name="kategori_dokumen" class="form-control" required>
                                                    <?php foreach ($list_kategori as $kat): ?>
                                                        <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Nama Dokumen</label>
                                                <input type="text" name="nama_dokumen" class="form-control" placeholder="Isi deskripsi berkas" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>File Berkas</label>
                                                <div class="custom-file">
                                                    <input type="file" name="file_dokumen" class="custom-file-input" id="customFile" required>
                                                    <label class="custom-file-label" for="customFile">Pilih file...</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0">
                                            <button type="submit" class="btn btn-primary btn-block text-sm">
                                                <i class="fas fa-cloud-upload-alt mr-2"></i>Simpan Dokumen
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- List Dokumen -->
                            <div class="col-md-8">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Daftar Arsip Digital</h3>
                                        <div class="card-tools">
                                            <select class="form-control form-control-sm" id="filter_ta_select" onchange="window.location.href='<?= BASE_URL ?>tugas_tambahan/index/<?= htmlspecialchars($jenis) ?>?id_ta='+this.value">
                                                <?php foreach ($tahun_ajaran as $ta): ?>
                                                    <option value="<?= $ta['id_ta'] ?>" <?= $ta['id_ta'] == $id_ta ? 'selected' : '' ?>>TA: <?= htmlspecialchars($ta['nama_ta']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover text-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Nama Berkas</th>
                                                    <th>Tanggal</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($dokumen)): ?>
                                                    <tr><td colspan="4" class="text-center py-4 text-muted small italic">Belum ada dokumen.</td></tr>
                                                <?php else: foreach($dokumen as $doc): ?>
                                                    <tr>
                                                        <td><span class="badge badge-secondary"><?= htmlspecialchars($doc['kategori_dokumen']) ?></span></td>
                                                        <td class="font-weight-bold"><?= htmlspecialchars($doc['nama_dokumen']) ?></td>
                                                        <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-xs btn-info btn-preview-doc" data-path="<?= htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8') ?>" data-title="Pratinjau <?= htmlspecialchars($doc['nama_dokumen'], ENT_QUOTES, 'UTF-8') ?>" title="Pratinjau Berkas">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-xs btn-primary" title="Unduh Berkas"><i class="fas fa-download"></i></a>
                                                                <button class="btn btn-xs btn-danger" onclick="confirmDelete(<?= $doc['id_dokumen'] ?>, 'doc')" title="Hapus Berkas"><i class="fas fa-trash"></i></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: AGENDA KEGIATAN -->
                    <div class="tab-pane fade" id="tab-agenda" role="tabpanel" aria-labelledby="tab-agenda-link">
                        <div class="row">
                            <!-- Form Agenda -->
                            <div class="col-md-4">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Tambah Agenda Baru</h3>
                                    </div>
                                    <form action="<?= BASE_URL ?>tugas_tambahan/save_agenda" method="POST">
                                        <input type="hidden" name="jenis" value="<?= htmlspecialchars($jenis) ?>">
                                        <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                                        <div class="card-body">
                                            <div class="form-group text-sm">
                                                <label>Nama Kegiatan</label>
                                                <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Rapat Koordinasi" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Tanggal Pelaksanaan</label>
                                                <input type="date" name="tanggal" class="form-control" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Keterangan</label>
                                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0">
                                            <button type="submit" class="btn btn-success btn-block text-sm">
                                                <i class="fas fa-save mr-2"></i>Simpan Agenda
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- List Agenda -->
                            <div class="col-md-8">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Daftar Agenda Kegiatan</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover text-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width:120px">Tanggal</th>
                                                    <th>Nama Kegiatan</th>
                                                    <th>Keterangan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($agendas)): ?>
                                                    <tr><td colspan="4" class="text-center py-4 text-muted small italic">Belum ada agenda terencana.</td></tr>
                                                <?php else: foreach($agendas as $ag): ?>
                                                    <tr>
                                                        <td class="font-weight-bold text-primary"><?= date('d M Y', strtotime($ag['tanggal'])) ?></td>
                                                        <td class="font-weight-bold"><?= htmlspecialchars($ag['nama_kegiatan']) ?></td>
                                                        <td><?= htmlspecialchars($ag['keterangan']) ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-xs btn-danger" onclick="confirmDelete(<?= $ag['id_agenda'] ?>, 'agenda')"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($jenis === 'walas'): ?>
                    <!-- TAB 3: INVENTARIS KELAS -->
                    <div class="tab-pane fade" id="tab-inventaris" role="tabpanel" aria-labelledby="tab-inventaris-link">
                        <div class="row">
                            <!-- Form Inventaris -->
                            <div class="col-md-4">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Tambah Barang Inventaris</h3>
                                    </div>
                                    <form action="<?= BASE_URL ?>tugas_tambahan/save_inventaris" method="POST">
                                        <input type="hidden" name="jenis" value="walas">
                                        <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                                        <div class="card-body">
                                            <div class="form-group text-sm">
                                                <label>Nama Barang / Fasilitas</label>
                                                <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Meja Guru" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Jumlah</label>
                                                <input type="number" name="jumlah" class="form-control" min="1" value="1" required>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Kondisi</label>
                                                <select name="kondisi" class="form-control" required>
                                                    <option value="Baik">Baik</option>
                                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                                    <option value="Rusak Berat">Rusak Berat</option>
                                                </select>
                                            </div>
                                            <div class="form-group text-sm">
                                                <label>Keterangan</label>
                                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0">
                                            <button type="submit" class="btn btn-primary btn-block text-sm">
                                                <i class="fas fa-save mr-2"></i>Simpan Inventaris
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- List Inventaris -->
                            <div class="col-md-8">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Daftar Inventaris Sarana Kelas</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover text-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Nama Barang</th>
                                                    <th class="text-center">Jumlah</th>
                                                    <th class="text-center">Kondisi</th>
                                                    <th>Keterangan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($inventaris)): ?>
                                                    <tr><td colspan="5" class="text-center py-4 text-muted small italic">Belum ada barang inventaris terdata.</td></tr>
                                                <?php else: foreach($inventaris as $inv): ?>
                                                    <tr>
                                                        <td class="font-weight-bold"><?= htmlspecialchars($inv['nama_barang']) ?></td>
                                                        <td class="text-center"><?= $inv['jumlah'] ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                                if($inv['kondisi'] == 'Baik') echo '<span class="badge badge-success">Baik</span>';
                                                                elseif($inv['kondisi'] == 'Rusak Ringan') echo '<span class="badge badge-warning">Rusak Ringan</span>';
                                                                else echo '<span class="badge badge-danger">Rusak Berat</span>';
                                                            ?>
                                                        </td>
                                                        <td class="text-muted"><?= htmlspecialchars($inv['keterangan']) ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-xs btn-danger" onclick="confirmDelete(<?= $inv['id_inventaris'] ?>, 'inv')"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($walas_kelas) && $walas_kelas): ?>
                    <!-- TAB 4: REKAP ABSENSI -->
                    <div class="tab-pane fade" id="tab-absensi" role="tabpanel" aria-labelledby="tab-absensi-link">
                        <div class="row">
                            <!-- Tabel Detail Absensi -->
                            <div class="col-md-8">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Detail Absensi Siswa - <?= htmlspecialchars($walas_kelas['nama_kelas']) ?></h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <!-- Filter Form -->
                                        <div class="filter-block p-3 bg-warning text-dark border-bottom">
                                            <h5 class="font-weight-bold mb-3"><i class="fas fa-filter mr-1"></i> Filter Absensi Siswa</h5>
                                            <div class="row">
                                                <div class="col-md-6 form-group mb-0">
                                                    <label>Periode</label>
                                                    <select id="walas-filter-periode" class="form-control form-control-sm">
                                                        <option value="daily" selected>Harian</option>
                                                        <option value="monthly">Bulanan</option>
                                                        <option value="semester">Semester</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group mb-0" id="walas-date-input-group">
                                                    <label>Pilih Waktu</label>
                                                    <input type="date" id="walas-date-daily" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive p-3">
                                            <table class="table table-sm table-bordered table-striped" id="walas-absensi-table">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th class="text-center" style="width: 50px;">No</th>
                                                        <th class="text-center">NIS</th>
                                                        <th>Nama Siswa</th>
                                                        <th class="text-center">H</th>
                                                        <th class="text-center">S</th>
                                                        <th class="text-center">I</th>
                                                        <th class="text-center">A</th>
                                                        <th class="text-center text-primary font-weight-bold">% Hadir</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="8" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Grafik Absensi -->
                            <div class="col-md-4">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Grafik Kehadiran</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center font-weight-bold mb-3 border-bottom pb-3">
                                            <div class="col-3 text-success border-right"><h4 id="tot-h" class="mb-0">0</h4><small>Hadir</small></div>
                                            <div class="col-3 text-warning border-right"><h4 id="tot-s" class="mb-0">0</h4><small>Sakit</small></div>
                                            <div class="col-3 text-info border-right"><h4 id="tot-i" class="mb-0">0</h4><small>Izin</small></div>
                                            <div class="col-3 text-danger"><h4 id="tot-a" class="mb-0">0</h4><small>Alpa</small></div>
                                        </div>
                                        <div style="height: 300px;"><canvas id="walasAbsensiChart"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($jenis === 'bk'): ?>
                    <!-- TAB JURNAL BK -->
                    <div class="tab-pane fade" id="tab-jurnal-bk" role="tabpanel" aria-labelledby="tab-jurnal-bk-link">
                        <div class="card shadow-none border">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h3 class="card-title text-sm font-weight-bold">Jurnal Bimbingan dan Konseling</h3>
                                <button class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#modalAddJurnalBK">
                                    <i class="fas fa-plus mr-1"></i> Tambah Jurnal
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped" id="table-jurnal-bk">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">No</th>
                                                <th class="text-center">Tanggal</th>
                                                <th>Nama Siswa (Kelas)</th>
                                                <th>Kategori Layanan</th>
                                                <th>Kegiatan Bimbingan/Konseling</th>
                                                <th>Tindak Lanjut</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center" style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($jurnal_bk)): ?>
                                                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data jurnal.</td></tr>
                                            <?php else: $no=1; foreach($jurnal_bk as $j): ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td class="text-center"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($j['nama_siswa']) ?></strong><br>
                                                        <small class="text-muted">(<?= htmlspecialchars($j['nama_kelas']) ?>)</small>
                                                    </td>
                                                    <td><?= htmlspecialchars($j['kategori_layanan']) ?></td>
                                                    <td><?= nl2br(htmlspecialchars($j['uraian_kegiatan'])) ?></td>
                                                    <td><?= nl2br(htmlspecialchars($j['tindak_lanjut'])) ?></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-<?= $j['status'] == 'Selesai' ? 'success' : 'warning' ?>">
                                                            <?= $j['status'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <button class="btn btn-xs btn-info" onclick="editJurnalBK(<?= $j['id_jurnal'] ?>)"><i class="fas fa-edit"></i></button>
                                                            <button class="btn btn-xs btn-danger" onclick="confirmDelete(<?= $j['id_jurnal'] ?>, 'bk')"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>

                    <?php if (in_array($jenis, ['humas', 'bk'])): ?>
                    <!-- TAB GALERI -->
                    <div class="tab-pane fade" id="tab-galeri" role="tabpanel" aria-labelledby="tab-galeri-link">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Unggah Foto Galeri</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= BASE_URL ?>tugas_tambahan/save_galeri" method="POST" enctype="multipart/form-data" id="formGaleriTugas">
                                            <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                                            <input type="hidden" name="jenis_tugas_tambahan" value="<?= $jenis ?>">
                                            
                                            <!-- Dual-mode Tab: Unggah File & Kamera Langsung -->
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small mb-1">Ambil / Unggah Foto Dokumentasi</label>
                                                <ul class="nav nav-pills mb-2" role="tablist" style="display: flex; gap: 4px;">
                                                    <li class="nav-item" style="flex: 1;">
                                                        <a class="nav-link active" id="tab-gal-upload-link" data-toggle="pill" href="#tab-gal-upload" role="tab" onclick="stopGaleriCamera()" style="width: 100%; text-align: center; font-size: 0.76rem; font-weight: 700; border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 6px 4px;">
                                                            <i class="fas fa-folder-open mr-1"></i> Unggah
                                                        </a>
                                                    </li>
                                                    <li class="nav-item" style="flex: 1;">
                                                        <a class="nav-link" id="tab-gal-camera-link" data-toggle="pill" href="#tab-gal-camera" role="tab" onclick="startGaleriCamera()" style="width: 100%; text-align: center; font-size: 0.76rem; font-weight: 700; border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 6px 4px;">
                                                            <i class="fas fa-camera mr-1"></i> Kamera
                                                        </a>
                                                    </li>
                                                </ul>

                                                <div class="tab-content border rounded p-2 bg-light" style="border-radius: 10px;">
                                                    <!-- Mode Upload File -->
                                                    <div class="tab-pane fade show active" id="tab-gal-upload" role="tabpanel">
                                                        <div class="custom-file mb-1">
                                                            <input type="file" name="foto" class="custom-file-input" id="foto-galeri" accept="image/*" onchange="previewGaleriFile(this)">
                                                            <label class="custom-file-label" for="foto-galeri" style="font-size:0.78rem;">Pilih file...</label>
                                                        </div>
                                                        <img id="previewGaleriImg" class="img-fluid rounded mt-2 shadow-sm" style="display:none; max-height:160px; width:100%; object-fit:cover;">
                                                    </div>

                                                    <!-- Mode Live Camera -->
                                                    <div class="tab-pane fade" id="tab-gal-camera" role="tabpanel">
                                                        <div style="background:#0f172a; border-radius:8px; overflow:hidden; position:relative; text-align:center;">
                                                            <video id="galeriVideo" autoplay playsinline muted style="width:100%; max-height:180px; object-fit:cover; display:none; background:#000;"></video>
                                                            <canvas id="galeriCanvas" style="display:none;"></canvas>

                                                            <!-- Controls Bar -->
                                                            <div id="galeriCameraControls" class="p-2 d-flex justify-content-center align-items-center flex-wrap" style="gap: 6px; background: rgba(15, 23, 42, 0.9); display: none !important;">
                                                                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 font-weight-bold" id="btnSnapGaleri" onclick="takeGaleriSnapshot()">
                                                                    <i class="fas fa-camera mr-1"></i> Jepret
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-outline-light rounded-pill px-2.5 font-weight-bold" onclick="switchGaleriFacing()">
                                                                    <i class="fas fa-sync-alt mr-1"></i> Balik
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 font-weight-bold" id="btnRetakeGaleri" onclick="retakeGaleriSnapshot()" style="display:none;">
                                                                    <i class="fas fa-redo mr-1"></i> Ulangi
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Native fallback -->
                                                        <div class="mt-1.5 text-center">
                                                            <input type="file" id="nativeGaleriInput" accept="image/*" capture="environment" style="display:none;" onchange="previewNativeGaleri(this)">
                                                            <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-3 font-weight-bold btn-block" onclick="document.getElementById('nativeGaleriInput').click()">
                                                                <i class="fas fa-camera-retro mr-1"></i> Buka Aplikasi Kamera
                                                            </button>
                                                        </div>

                                                        <div id="galeriCapturedBox" class="mt-2 text-center" style="display:none;">
                                                            <small class="text-success font-weight-bold d-block mb-1"><i class="fas fa-check-circle mr-1"></i> Foto Siap Diunggah:</small>
                                                            <img id="galeriCapturedPreview" class="img-fluid rounded shadow-sm" style="max-height:140px; width:100%; object-fit:cover;" src="">
                                                        </div>
                                                        <input type="hidden" name="foto_cam_data" id="fotoGaleriCamData" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="small font-weight-bold text-dark">Caption / Keterangan Foto</label>
                                                <textarea name="caption" class="form-control" rows="2" placeholder="Tulis keterangan singkat kegiatan..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" style="border-radius:10px;"><i class="fas fa-upload mr-1"></i> Unggah Foto</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card shadow-none border">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title text-sm font-weight-bold">Galeri Foto</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($galeri)): ?>
                                            <div class="text-center py-5 text-muted">
                                                <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                                                <p>Belum ada foto di galeri.</p>
                                            </div>
                                        <?php else: ?>
                                            <div class="row">
                                                <?php foreach($galeri as $g): ?>
                                                    <div class="col-md-4 col-6 mb-4">
                                                        <div class="galeri-item position-relative border rounded overflow-hidden shadow-sm h-100 bg-light">
                                                            <a href="<?= $g['file_path'] ?>" data-toggle="lightbox" data-title="<?= htmlspecialchars($g['caption']) ?>" data-gallery="tugas-galeri">
                                                                <img src="<?= $g['file_path'] ?>" class="img-fluid" style="height: 150px; width: 100%; object-fit: cover;">
                                                            </a>
                                                            <div class="p-2 bg-white">
                                                                <p class="small text-dark mb-1 text-truncate" title="<?= htmlspecialchars($g['caption']) ?>">
                                                                    <?= $g['caption'] ?: '<span class="text-muted italic">Tanpa keterangan</span>' ?>
                                                                </p>
                                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                                    <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i><?= date('d/m/y', strtotime($g['created_at'])) ?></small>
                                                                    <button class="btn btn-xs btn-outline-danger" onclick="confirmDelete(<?= $g['id_galeri'] ?>, 'galeri')"><i class="fas fa-trash"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($jenis === 'bk'): ?>
<!-- Modal Add Jurnal BK -->
<div class="modal fade" id="modalAddJurnalBK" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= BASE_URL ?>tugas_tambahan/save_jurnal_bk" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Jurnal Bimbingan/Konseling</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status Layanan</label>
                            <select name="status" class="form-control">
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Pilih Kelas</label>
                            <select id="bk-pilih-kelas" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Kelas --</option>
                                <option value="23">X.1</option><option value="24">X.2</option><option value="25">X.3</option><option value="26">X.4</option><option value="27">XI.1</option><option value="28">XI.2</option><option value="29">XII.1</option><option value="30">XII.2</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Pilih Siswa</label>
                            <select name="id_siswa" id="bk-pilih-siswa" class="form-control select2" style="width: 100%;" required disabled>
                                <option value="">-- Pilih Siswa --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kategori Layanan</label>
                        <select name="kategori_layanan" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Bimbingan Pribadi">Bimbingan Pribadi</option>
                            <option value="Bimbingan Sosial">Bimbingan Sosial</option>
                            <option value="Bimbingan Belajar">Bimbingan Belajar</option>
                            <option value="Bimbingan Karir">Bimbingan Karir</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Uraian Kegiatan Bimbingan/Konseling</label>
                        <textarea name="uraian_kegiatan" class="form-control" rows="4" placeholder="Jelaskan masalah atau topik bimbingan..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Tindak Lanjut</label>
                        <textarea name="tindak_lanjut" class="form-control" rows="3" placeholder="Apa rencana atau hasil tindak lanjutnya?"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Jurnal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
    $(document).ready(function () {
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        // Remember last active tab
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('#custom-tabs-four-tab a[href="' + activeTab + '"]').tab('show');
        }

        // Preview Document Event Handler
        $(document).on('click', '.btn-preview-doc', function () {
            var path = $(this).data('path');
            var title = $(this).data('title');
            if (typeof showGlobalPreview === 'function') {
                showGlobalPreview(path, 'iframe', title);
            } else {
                window.open(path, '_blank');
            }
        });
    });

    function confirmDelete(id, type) {
        let url = '';
        if (type === 'doc') url = '<?= BASE_URL ?>tugas_tambahan/delete?id=' + id;
        else if (type === 'agenda') url = '<?= BASE_URL ?>tugas_tambahan/delete_agenda?id=' + id;
        else if (type === 'inv') url = '<?= BASE_URL ?>tugas_tambahan/delete_inventaris?id=' + id;
        else if (type === 'bk') url = '<?= BASE_URL ?>tugas_tambahan/delete_jurnal_bk?id=' + id;
        else if (type === 'galeri') url = '<?= BASE_URL ?>tugas_tambahan/delete_galeri?id=' + id;
        Swal.fire({
            title: 'Hapus data?',
            text: "Data akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }

    <?php if ($jenis === 'walas' && isset($walas_kelas) && $walas_kelas): ?>
    let walasChart = null;
    let id_kelas_walas = <?= $walas_kelas['id_kelas'] ?>;
    let current_ta_walas = <?= $id_ta ?>;

    function renderWalasChart(data) {
        var canvas = document.getElementById('walasAbsensiChart');
        if (!canvas) return;
        if (walasChart) walasChart.destroy();
        let ctx = canvas.getContext('2d');
        walasChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
                datasets: [{
                    data: [data.H, data.S, data.I, data.A],
                    backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    function loadWalasAbsensi() {
        let mode = $('#walas-filter-periode').val();
        let startDate = '', semester = '';

        if (mode === 'daily') startDate = $('#walas-date-daily').val();
        else if (mode === 'monthly') startDate = $('#walas-date-month').val();
        else if (mode === 'semester') semester = $('#walas-date-semester').val();

        let params = {
            periode: mode,
            tanggal: startDate,
            semester: semester,
            id_kelas: id_kelas_walas,
            id_ta: current_ta_walas
        };

        let queryString = $.param(params);
        let apiUrl = '<?= BASE_URL ?>api?type=dashboard&act=absensi_siswa_detail&' + queryString;

        $('#walas-absensi-table tbody').html('<tr><td colspan="8" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

        $.getJSON(apiUrl, function(res) {
            if (res.status === 'ok') {
                let html = '';
                let chartData = { H: 0, S: 0, I: 0, A: 0 };
                
                if (res.data && res.data.length > 0) {
                    res.data.forEach((s, i) => {
                        let total = parseInt(s.H) + parseInt(s.S) + parseInt(s.I) + parseInt(s.A);
                        let persen = total > 0 ? Math.round((parseInt(s.H) / total) * 100) : 0;
                        html += `<tr>
                            <td class="text-center">${i + 1}</td>
                            <td class="text-center">${s.nipd || '-'}</td>
                            <td>${s.nama}</td>
                            <td class="text-center text-success">${s.H}</td>
                            <td class="text-center text-warning">${s.S}</td>
                            <td class="text-center text-info">${s.I}</td>
                            <td class="text-center text-danger">${s.A}</td>
                            <td class="text-center font-weight-bold text-primary">${persen}%</td>
                        </tr>`;
                        chartData.H += parseInt(s.H);
                        chartData.S += parseInt(s.S);
                        chartData.I += parseInt(s.I);
                        chartData.A += parseInt(s.A);
                    });
                } else {
                    html = '<tr><td colspan="8" class="text-center py-4 text-muted small italic">Tidak ada data kehadiran.</td></tr>';
                }
                
                $('#walas-absensi-table tbody').html(html);
                $('#tot-h').text(chartData.H);
                $('#tot-s').text(chartData.S);
                $('#tot-i').text(chartData.I);
                $('#tot-a').text(chartData.A);
                
                renderWalasChart(chartData);
            } else {
                $('#walas-absensi-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
            }
        });
    }

    $(document).ready(function() {
        $('#walas-filter-periode').change(function() {
            let val = $(this).val();
            let html = '';
            let today = new Date();
            let y = today.getFullYear();
            let m = String(today.getMonth() + 1).padStart(2, '0');
            let d = String(today.getDate()).padStart(2, '0');
            let currentDate = `${y}-${m}-${d}`;
            let currentMonth = `${y}-${m}`;

            if (val === 'daily') {
                html = `<input type="date" id="walas-date-daily" class="form-control form-control-sm" value="${currentDate}">`;
            } else if (val === 'monthly') {
                html = `<input type="month" id="walas-date-month" class="form-control form-control-sm" value="${currentMonth}">`;
            } else if (val === 'semester') {
                let currentSmt = (parseInt(m) >= 7) ? '1' : '2';
                html = `<select id="walas-date-semester" class="form-control form-control-sm">
                            <option value="1" ${currentSmt == '1' ? 'selected' : ''}>Semester 1 (Ganjil)</option>
                            <option value="2" ${currentSmt == '2' ? 'selected' : ''}>Semester 2 (Genap)</option>
                        </select>`;
            }

            $('#walas-date-input-group').html(`<label>Pilih Waktu</label>` + html);
            $('#walas-date-input-group').find('input, select').on('change', loadWalasAbsensi);
            loadWalasAbsensi();
        });

        // Trigger load initial on tab shown
        $('a[data-toggle="pill"][href="#tab-absensi"]').on('shown.bs.tab', function (e) {
            // Check if table is empty or loading
            let txt = $('#walas-absensi-table tbody tr td').text();
            if (txt.includes('Memuat')) {
                loadWalasAbsensi();
            }
        });
        
        // Initial attach event
        $('#walas-date-input-group').find('input').on('change', loadWalasAbsensi);
    });
    <?php endif; ?>

    <?php if ($jenis === 'bk'): ?>
    const allSiswa = [{id:111, nama:"AGIS MUTIARA", kelas:23}, {id:112, nama:"AGUS RAMDANI", kelas:24}, {id:113, nama:"AIRA PUTRI ADITIYA", kelas:25}, {id:114, nama:"ALDI SAPUTRA", kelas:24}, {id:115, nama:"AMELDA", kelas:25}, {id:116, nama:"AMELIA", kelas:26}, {id:117, nama:"ANDIKA MAULANA", kelas:25}, {id:1, nama:"ANDRIAN SYACHRUDIN", kelas:29}, {id:48, nama:"ANGGA HERDIANA", kelas:27}, {id:228, nama:"ANGGI SAPUTRA", kelas:28}, {id:118, nama:"ANISA", kelas:26}, {id:119, nama:"ANISA PITRI", kelas:23}, {id:120, nama:"ANISA RAHMA MUSTIKA", kelas:25}, {id:121, nama:"ANISA SAFITRI", kelas:25}, {id:2, nama:"ARIA MAULANA MALIK IBRAHIM", kelas:29}, {id:49, nama:"ARIS RAMADAN", kelas:27}, {id:50, nama:"ARISA SITI NURAZIZAH", kelas:27}, {id:122, nama:"ARYASATYA FIRMANSYAH", kelas:25}, {id:227, nama:"ASEP MURDANI", kelas:28}, {id:123, nama:"AULIA DEWI SRI WULANDARI", kelas:25}, {id:124, nama:"AWALIAH", kelas:26}, {id:125, nama:"BAYHAQI ALKAFARO", kelas:24}, {id:126, nama:"BINTANG FURQON", kelas:24}, {id:3, nama:"DAPID SUNARYA", kelas:29}, {id:51, nama:"DEDE RAHMAN MAULANA", kelas:27}, {id:226, nama:"DEDE SUHENDA", kelas:28}, {id:74, nama:"DELPA", kelas:28}, {id:25, nama:"DESI SRI MULYANI", kelas:30}, {id:127, nama:"DESTI ANJANI", kelas:25}, {id:52, nama:"DETI RAHMAWATI", kelas:27}, {id:75, nama:"DEWI MILANI", kelas:28}, {id:26, nama:"DEWI YANTI", kelas:30}, {id:128, nama:"DIKI AGUSTIAN", kelas:26}, {id:27, nama:"DINI AMINARTI", kelas:30}, {id:129, nama:"DINI PUTRI ANDRIANI", kelas:26}, {id:4, nama:"E. WILDA SASCIA", kelas:29}, {id:130, nama:"EDWAR GUPRIYAN", kelas:25}, {id:231, nama:"EGA SUMYATI", kelas:24}, {id:232, nama:"ELISA RIANTO", kelas:24}, {id:131, nama:"ELIYA YULIANI", kelas:24}, {id:132, nama:"ELSI", kelas:25}, {id:133, nama:"FABIAN YUSUF", kelas:25}, {id:134, nama:"FADHIL ABDILLAH", kelas:24}, {id:28, nama:"FAHRI RAMADANI", kelas:30}, {id:135, nama:"FAIRUS MUTIARAHIM", kelas:24}, {id:136, nama:"FAUZIAH", kelas:25}, {id:29, nama:"FAZLA TUNNISA", kelas:30}, {id:137, nama:"FERA JULIANTI", kelas:26}, {id:5, nama:"GANJAR", kelas:29}, {id:6, nama:"GEBI", kelas:29}, {id:138, nama:"GHEA ANANDA AVRIANTY", kelas:23}, {id:139, nama:"GRESIA SUMAROU", kelas:25}, {id:140, nama:"HABIBAH", kelas:26}, {id:141, nama:"HAIKAL GALIH MULYANA", kelas:26}, {id:76, nama:"HASANUDIN", kelas:28}, {id:142, nama:"HERA IDA", kelas:24}, {id:143, nama:"HERDI", kelas:26}, {id:144, nama:"HILDA MUTIARA ZULFA", kelas:24}, {id:7, nama:"IBRAHIM NURMAN", kelas:29}, {id:145, nama:"ICA", kelas:25}, {id:146, nama:"INDAH ANJANI", kelas:26}, {id:77, nama:"INDRI", kelas:28}, {id:147, nama:"INDRI YULIANTI", kelas:24}, {id:148, nama:"INDRIYANI", kelas:26}, {id:78, nama:"INTAN AWALIAH", kelas:28}, {id:30, nama:"INTAN PUSPITASARI", kelas:30}, {id:149, nama:"IRMAN MAULANA", kelas:23}, {id:150, nama:"ISMA", kelas:23}, {id:31, nama:"JALIYAH DWI HANDAYANI", kelas:30}, {id:151, nama:"JELITA SURYA SABRINA PUTRI", kelas:24}, {id:152, nama:"JIAN BAAMI HABTI", kelas:24}, {id:54, nama:"KARINA ADISTI", kelas:27}, {id:153, nama:"KASANDRA AQUINI", kelas:23}, {id:154, nama:"KESYA PUTRI NATAPLAWIRA", kelas:23}, {id:8, nama:"KHOIRUNNISA MUTMAINAH", kelas:29}, {id:155, nama:"LUSI WIDIA MAULIDA", kelas:24}, {id:156, nama:"LUTFI ALFIANTI", kelas:24}, {id:157, nama:"LUTHVIANI ULFA", kelas:24}, {id:9, nama:"M. ALIP MAULA PH", kelas:29}, {id:158, nama:"M. DZUBIAN SYAFIQ ABDILLAH", kelas:25}, {id:159, nama:"M. FAHRI SUGANDA", kelas:26}, {id:79, nama:"M. RAFLI MAULANA", kelas:28}, {id:160, nama:"M. RIPAL ALHUSAERI", kelas:25}, {id:161, nama:"MAEDASARI", kelas:26}, {id:162, nama:"MARWAN SETIAWAN", kelas:24}, {id:32, nama:"MAULANA", kelas:30}, {id:163, nama:"MELISA APRILLIANI", kelas:26}, {id:11, nama:"MILA RAHMAWATI", kelas:29}, {id:33, nama:"MOCH ZAKI ABDUL LATIP", kelas:30}, {id:80, nama:"MOCH. FACHRI PRATAMA", kelas:28}, {id:164, nama:"MOH. RIFKI RIZKY ARRAHMAN", kelas:26}, {id:165, nama:"MONA MUTIARA", kelas:23}, {id:166, nama:"MUCHAMMAD FAISAL", kelas:24}, {id:56, nama:"MUHAMAD ALI RAMDAN", kelas:27}, {id:12, nama:"MUHAMAD ILHAM MAULANA FIRDAUS", kelas:29}, {id:34, nama:"MUHAMAD IRFAN ABDILAH", kelas:30}, {id:13, nama:"MUHAMAD MUTTAHID AL FAHMI", kelas:29}, {id:81, nama:"MUHAMAD RAHMADHANI", kelas:28}, {id:57, nama:"MUHAMAD RIFAL", kelas:27}, {id:35, nama:"MUHAMMAD ABDUL FATAH", kelas:30}, {id:167, nama:"MUHAMMAD FATHURROHMAN", kelas:24}, {id:36, nama:"MUHAMMAD GOLIB MURSIDI", kelas:30}, {id:168, nama:"MUHAMMAD IBNU MUBAROK AZZEIN", kelas:24}, {id:82, nama:"MUHAMMAD MISBAHUL MUNIR", kelas:28}, {id:169, nama:"MUHAMMAD RAFLI AL AZHARI", kelas:26}, {id:58, nama:"MUHAMMAD RAIHAN NAFIS", kelas:27}, {id:170, nama:"MUHAMMAD RISKY", kelas:26}, {id:172, nama:"MUHKLISIHIN", kelas:26}, {id:173, nama:"MUTIARA LAILA PUTRI", kelas:25}, {id:174, nama:"NABILLAH MEGA FIKRIANI", kelas:24}, {id:175, nama:"NADIA MARDIANA", kelas:23}, {id:176, nama:"NAILA FITRI RAHMADHANI", kelas:23}, {id:14, nama:"NAJILAH", kelas:29}, {id:84, nama:"Nana Firmansyah", kelas:28}, {id:59, nama:"NENG MIRNASARI", kelas:27}, {id:37, nama:"NENG SITI NURANI", kelas:30}, {id:85, nama:"NENG SRI NURCAHYATI", kelas:28}, {id:60, nama:"NENTI NOVIANTI", kelas:27}, {id:177, nama:"NITA MAULANI", kelas:26}, {id:178, nama:"NOVITA ILMIRA DWI PURNAMA", kelas:24}, {id:179, nama:"NUR WAHID SALIM", kelas:24}, {id:180, nama:"NURAENI", kelas:24}, {id:181, nama:"NURHAIFA", kelas:24}, {id:182, nama:"NURIL YAHDIK", kelas:23}, {id:183, nama:"NURUL AZMI", kelas:23}, {id:184, nama:"PAHMI AJIDIN", kelas:26}, {id:185, nama:"PAHRI RAMADHAN", kelas:26}, {id:186, nama:"PANDI", kelas:26}, {id:187, nama:"PIONA ELDI OKTAVIA", kelas:25}, {id:61, nama:"PIRESTA PUTRI CALISHA", kelas:27}, {id:188, nama:"PUTRI AMELIA", kelas:26}, {id:62, nama:"PUTRI TRISMAYANTI", kelas:27}, {id:189, nama:"RAIHAN CAHYA MAULID", kelas:24}, {id:38, nama:"RAISA RAHIM", kelas:30}, {id:39, nama:"RAMLAN", kelas:30}, {id:190, nama:"RANDI", kelas:24}, {id:63, nama:"Ranti Sari Dewi", kelas:27}, {id:191, nama:"RAPLI MAULANA", kelas:24}, {id:192, nama:"RASNAMILA", kelas:24}, {id:193, nama:"RATIH MAULIDA", kelas:26}, {id:16, nama:"RAZ AGIS LEOBOY", kelas:29}, {id:17, nama:"RD. RAZZY MUSLIM EL KHURASANI", kelas:29}, {id:194, nama:"REHAN NURJAELANI", kelas:25}, {id:195, nama:"REHAN SOMANTRI", kelas:23}, {id:196, nama:"RENDIYAWAN", kelas:26}, {id:40, nama:"REVA AYUNINGTIAS PUTRI EKA PURNAMA", kelas:30}, {id:64, nama:"RIFKI HIDAYATULLAH", kelas:27}, {id:65, nama:"RINDIYANI", kelas:27}, {id:197, nama:"RISA FITRIANI", kelas:25}, {id:198, nama:"RISMA JUNITA", kelas:26}, {id:66, nama:"RIYA RISMA APRIYANA", kelas:27}, {id:199, nama:"ROBY ARDIANSYAH", kelas:26}, {id:200, nama:"SAEPURROHIM KARIM", kelas:26}, {id:67, nama:"SAHRIL GUNAWAN", kelas:27}, {id:19, nama:"SALMAN ALFANDY", kelas:29}, {id:201, nama:"SALMAN ALFARISI SYA`AR", kelas:26}, {id:202, nama:"SIFA SILFIANA", kelas:24}, {id:203, nama:"SILVIA WAVIQ RAMADHANI", kelas:26}, {id:68, nama:"SINTA YUSNIANTI", kelas:27}, {id:41, nama:"SIPA KODARIAH", kelas:30}, {id:69, nama:"SISKA AULIA SAPITRI", kelas:27}, {id:42, nama:"SITI ANISA", kelas:30}, {id:204, nama:"SITI FATIMAH AZ-ZAHRA", kelas:24}, {id:225, nama:"SITI HALIPAH", kelas:28}, {id:70, nama:"SITI HARDIYANI", kelas:27}, {id:224, nama:"SITI LATIPAH", kelas:28}, {id:205, nama:"SITI MASNONEH", kelas:25}, {id:206, nama:"SITI MUNIFAH SIRIN", kelas:23}, {id:86, nama:"SITI NURASITA", kelas:28}, {id:207, nama:"SITI NURHALISA", kelas:26}, {id:87, nama:"SITI NURLAELA", kelas:28}, {id:208, nama:"SITI PATIMAH", kelas:26}, {id:209, nama:"SITI PATIMAH", kelas:24}, {id:210, nama:"SITI SALMA", kelas:25}, {id:211, nama:"SITI SHOPIA ULFA", kelas:26}, {id:88, nama:"SITI SUWANSAH", kelas:28}, {id:212, nama:"SITI SYARIFAH MARDHOTILAH", kelas:24}, {id:89, nama:"SITI ZAHRA QURATULAINI", kelas:28}, {id:20, nama:"SITI ZAHWA NUR ALIPIA", kelas:29}, {id:223, nama:"SRI IMAS YUNENGSIH", kelas:28}, {id:213, nama:"SRY MARLINA", kelas:25}, {id:43, nama:"SURYA MANDALA PUTRA N", kelas:30}, {id:214, nama:"SUSAN MEILANI", kelas:23}, {id:215, nama:"SYIFA RACHMAWATI AWALIYAH", kelas:23}, {id:45, nama:"TRIA AMANDA", kelas:30}, {id:216, nama:"WANGI", kelas:26}, {id:217, nama:"WILDAN", kelas:26}, {id:218, nama:"WILDANSYAH DWI KUSUMA", kelas:25}, {id:219, nama:"WINDI RAMADANI", kelas:26}, {id:21, nama:"WISMA", kelas:29}, {id:229, nama:"WISNA ALI MUNAWAR", kelas:30}, {id:71, nama:"YOHANA", kelas:27}, {id:46, nama:"YULIA RAHMA", kelas:30}, {id:22, nama:"YULIA SRI PURNAMA", kelas:29}, {id:220, nama:"YUNI", kelas:24}, {id:47, nama:"ZAHRA TUNISYA", kelas:30}, {id:72, nama:"ZAKARIA", kelas:27}, {id:221, nama:"ZIDAN SYAHRIL ARYANSA", kelas:26}, {id:222, nama:"ZIRA PUSPITA", kelas:26}];

    $(document).ready(function() {
        $('#bk-pilih-kelas').on('change', function() {
            let idKelas = $(this).val();
            let $selectSiswa = $('#bk-pilih-siswa');
            
            $selectSiswa.empty().append('<option value="">-- Pilih Siswa --</option>');
            
            if (idKelas) {
                let filtered = allSiswa.filter(s => s.kelas == idKelas);
                filtered.forEach(s => {
                    $selectSiswa.append(`<option value="${s.id}">${s.nama}</option>`);
                });
                $selectSiswa.prop('disabled', false);
            } else {
                $selectSiswa.prop('disabled', true);
            }
            $selectSiswa.trigger('change');
        });
    });

    function editJurnalBK(id) {
        // Implementasi edit jika diperlukan
        Swal.fire('Fitur Edit', 'Masih dalam pengembangan. Gunakan hapus & tambah ulang sementara.', 'info');
    }
    <?php endif; ?>

    // ============================================================
    // 📸 LIVE CAMERA HANDLER UNTUK DOKUMENTASI GALERI TUGAS
    // ============================================================
    let galeriStream = null;
    let galeriFacing = "environment"; // default kamera belakang untuk dokumentasi

    function previewGaleriFile(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = e => {
                $('#previewGaleriImg').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function startGaleriCamera() {
        stopGaleriCamera();
        const video = document.getElementById('galeriVideo');
        const controls = document.getElementById('galeriCameraControls');
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            document.getElementById('nativeGaleriInput').click();
            return;
        }

        video.muted = true;
        video.setAttribute('playsinline', '');
        video.setAttribute('autoplay', '');
        video.setAttribute('muted', '');

        try {
            galeriStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: galeriFacing, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
        } catch (e) {
            try {
                galeriStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            } catch (err) {
                console.warn("Camera stream failed", err);
                document.getElementById('nativeGaleriInput').click();
                return;
            }
        }

        if (galeriStream) {
            video.srcObject = galeriStream;
            video.onloadedmetadata = () => {
                video.play();
                video.style.display = 'block';
                controls.style.setProperty('display', 'flex', 'important');
            };
        }
    }

    function stopGaleriCamera() {
        if (galeriStream) {
            galeriStream.getTracks().forEach(t => t.stop());
            galeriStream = null;
        }
        const video = document.getElementById('galeriVideo');
        if (video) {
            video.srcObject = null;
            video.style.display = 'none';
        }
        const controls = document.getElementById('galeriCameraControls');
        if (controls) controls.style.setProperty('display', 'none', 'important');
    }

    function switchGaleriFacing() {
        galeriFacing = (galeriFacing === "environment") ? "user" : "environment";
        startGaleriCamera();
    }

    function takeGaleriSnapshot() {
        const video = document.getElementById('galeriVideo');
        const canvas = document.getElementById('galeriCanvas');
        const preview = document.getElementById('galeriCapturedPreview');
        const previewBox = document.getElementById('galeriCapturedBox');
        const hiddenInput = document.getElementById('fotoGaleriCamData');
        const btnSnap = document.getElementById('btnSnapGaleri');
        const btnRetake = document.getElementById('btnRetakeGaleri');

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

    function retakeGaleriSnapshot() {
        const video = document.getElementById('galeriVideo');
        const previewBox = document.getElementById('galeriCapturedBox');
        const hiddenInput = document.getElementById('fotoGaleriCamData');
        const btnSnap = document.getElementById('btnSnapGaleri');
        const btnRetake = document.getElementById('btnRetakeGaleri');

        hiddenInput.value = '';
        previewBox.style.display = 'none';
        btnSnap.style.display = 'inline-block';
        btnRetake.style.display = 'none';
        video.play();
    }

    function previewNativeGaleri(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = e => {
                let base64 = e.target.result;
                document.getElementById('fotoGaleriCamData').value = base64;
                document.getElementById('galeriCapturedPreview').src = base64;
                document.getElementById('galeriCapturedBox').style.display = 'block';
                stopGaleriCamera();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>