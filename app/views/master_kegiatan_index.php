<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="m-0 font-weight-bold text-dark"><i class="fas fa-tasks text-primary mr-2"></i> Master Kegiatan</h4>
            <p class="text-muted small mb-0">Pengaturan kegiatan rutin sekolah dan alokasi waktunya.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-none mr-2" style="border-radius: 8px;" data-toggle="modal" data-target="#modal-akademik">
                <i class="fas fa-plus mr-1"></i> Akademik
            </button>
            <button type="button" class="btn btn-success btn-sm px-3 shadow-none" style="border-radius: 8px;" data-toggle="modal" data-target="#modal-non-akademik">
                <i class="fas fa-plus mr-1"></i> Non-Akademik
            </button>
        </div>
    </div>
  </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- BARIS 2: TABEL OUTPUT -->
        <div class="row">
            <!-- TABEL AKADEMIK -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; overflow: hidden; border-top: 4px solid #0ea5e9 !important;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 font-weight-bold text-info small uppercase"><i class="fas fa-graduation-cap mr-2"></i> KEGIATAN AKADEMIK <span class="text-muted font-weight-normal">(SETUP JADWAL)</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <th class="py-2">NAMA</th>
                                    <th class="text-center py-2" style="width: 100px;">JENIS</th>
                                    <th class="text-center py-2" style="width: 80px;">DURASI</th>
                                    <th class="text-center py-2">HARI</th>
                                    <th class="text-center py-2" style="width: 60px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($kegiatan_akademik as $k): ?>
                                    <tr class="align-middle">
                                        <td class="align-middle"><span class="font-weight-bold text-dark small"><?= htmlspecialchars($k['nama_kegiatan']) ?></span></td>
                                        <td class="text-center align-middle"><span class="badge bg-light text-primary border px-2 py-1" style="font-size: 0.6rem; border-radius: 4px;"><?= $k['jenis_kegiatan'] ?></span></td>
                                        <td class="text-center align-middle"><code class="small text-muted"><?= $k['durasi_menit'] ?>m</code></td>
                                        <td class="text-center align-middle small text-muted">
                                            <?php 
                                                $hari_arr = $k['hari_pelaksanaan'] ? explode(',', $k['hari_pelaksanaan']) : [];
                                                echo implode(', ', array_map('trim', $hari_arr));
                                            ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="index.php?mod=master_kegiatan&act=delete&id=<?= $k['id_kegiatan'] ?>" 
                                               class="btn btn-xs btn-outline-danger border-0 p-1" 
                                               style="background: #fef2f2; width: 26px; height: 26px; border-radius: 8px; color: #dc2626;" 
                                               title="Hapus" onclick="return confirmDelete(event)">
                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TABEL NON-AKADEMIK -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; overflow: hidden; border-top: 4px solid #10b981 !important;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 font-weight-bold text-success small uppercase"><i class="fas fa-futbol mr-2"></i> KEGIATAN NON-AKADEMIK <span class="text-muted font-weight-normal">(DATA PENUGASAN)</span></h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <th class="py-2">NAMA</th>
                                    <th class="text-center py-2" style="width: 120px;">JENIS</th>
                                    <th class="text-center py-2">HARI</th>
                                    <th class="text-center py-2" style="width: 60px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($kegiatan_non_akademik as $k): ?>
                                    <tr class="align-middle">
                                        <td class="align-middle"><span class="font-weight-bold text-dark small"><?= htmlspecialchars($k['nama_kegiatan']) ?></span></td>
                                        <td class="text-center align-middle"><span class="badge bg-light text-success border px-2 py-1" style="font-size: 0.6rem; border-radius: 4px;"><?= $k['jenis_kegiatan'] ?></span></td>
                                        <td class="text-center align-middle small text-muted">
                                            <?php 
                                                $hari_arr = $k['hari_pelaksanaan'] ? explode(',', $k['hari_pelaksanaan']) : [];
                                                echo implode(', ', array_map('trim', $hari_arr));
                                            ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="index.php?mod=master_kegiatan&act=delete&id=<?= $k['id_kegiatan'] ?>" 
                                               class="btn btn-xs btn-outline-danger border-0 p-1" 
                                               style="background: #fef2f2; width: 26px; height: 26px; border-radius: 8px; color: #dc2626;" 
                                               title="Hapus" onclick="return confirmDelete(event)">
                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL AKADEMIK -->
<div class="modal fade" id="modal-akademik" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-book mr-2"></i> Tambah Kegiatan Akademik</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?mod=master_kegiatan&act=save" method="POST">
                <input type="hidden" name="kategori" value="Akademik">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold mb-1">Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control form-control-sm" style="border-radius: 6px;" placeholder="Contoh: KBM, Istirahat Pagi, Upacara" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold mb-1">Jenis Kegiatan</label>
                        <select name="jenis_kegiatan" class="form-control form-control-sm" style="border-radius: 6px;" required>
                            <option value="KBM">KBM (Kegiatan Belajar Mengajar)</option>
                            <option value="Istirahat">Istirahat</option>
                            <option value="Pembiasaan">Pembiasaan (Tadarus, Sholat, dll)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold mb-1">Durasi (menit)</label>
                        <input type="number" name="durasi_menit" class="form-control form-control-sm" style="border-radius: 6px;" required placeholder="Contoh: 45">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-muted small font-weight-bold mb-2 d-block">Hari Pelaksanaan</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari): ?>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="hari_pelaksanaan[]" value="<?= $hari ?>" id="akd_<?= $hari ?>">
                                    <label class="custom-control-label text-sm font-weight-normal" for="akd_<?= $hari ?>"><?= $hari ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 6px;" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-none" style="border-radius: 6px;">Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL NON-AKADEMIK -->
<div class="modal fade" id="modal-non-akademik" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-futbol mr-2"></i> Tambah Kegiatan Non-Akademik</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?mod=master_kegiatan&act=save" method="POST">
                <input type="hidden" name="kategori" value="Non-Akademik">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold mb-1">Nama Kegiatan</label>
                        <input type="text" name="nama_kegiatan" class="form-control form-control-sm" style="border-radius: 6px;" placeholder="Contoh: Basket, Tahfidz Juz 30, Market Day" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold mb-1">Jenis Kegiatan</label>
                        <select name="jenis_kegiatan" class="form-control form-control-sm" style="border-radius: 6px;" required>
                            <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                            <option value="Kokulikuler">Kokulikuler</option>
                            <option value="Kewirausahaan">Kewirausahaan</option>
                            <option value="Tahfidz">Tahfidz Qur'an</option>
                            <option value="Pembiasaan">Pembiasaan Akhlak Mulia (Tadarus, Sholat)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold mb-1">Durasi / Estimasi (menit)</label>
                        <input type="number" name="durasi_menit" class="form-control form-control-sm" style="border-radius: 6px;" value="0" required placeholder="0 jika tidak tentu">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-muted small font-weight-bold mb-2 d-block">Hari Pelaksanaan</label>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari): ?>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="hari_pelaksanaan[]" value="<?= $hari ?>" id="non_<?= $hari ?>">
                                    <label class="custom-control-label text-sm font-weight-normal" for="non_<?= $hari ?>"><?= $hari ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary btn-sm px-3" style="border-radius: 6px;" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success btn-sm px-3 shadow-none" style="border-radius: 6px;">Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__.'/partials/footer.php'; ?>