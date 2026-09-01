<?php 
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__.'/partials/header.php'; 
?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-user-check"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Presensi Guru &amp; Tenaga Kependidikan
            <?php if (!empty($has_existing_data)): ?>
              <span class="badge badge-warning ml-2" style="font-size:0.65rem;vertical-align:middle;">MODE EDIT</span>
            <?php elseif (!empty($is_past_date)): ?>
              <span class="badge badge-warning ml-2" style="font-size:0.65rem;vertical-align:middle;">MODE EDIT (TANGGAL LALU)</span>
            <?php endif; ?>
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <a href="<?= BASE_URL ?>absensi_scan" class="btn btn-success btn-sm font-weight-bold rounded-pill px-3 shadow-sm">
          <i class="fas fa-qrcode mr-1"></i> Scan QR / Barcode
        </a>
      </div>
    </div>
  </div>
</div>

<section class="content">
<div class="container-fluid">

    <?php if (!empty($has_existing_data)): 
        $jml_hadir = count(array_filter($absensi_hari_ini, fn($a) => $a['status'] === 'Hadir'));
        $jml_tidak = count($absensi_hari_ini) - $jml_hadir;
    ?>
    <!-- Banner Edit Mode - Data Tersimpan -->
    <div class="callout callout-warning mb-3 shadow-sm" style="border-radius: 10px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <i class="fas fa-edit fa-2x text-warning mr-3"></i>
                <div>
                    <strong>Mode Edit — Data Presensi Telah Tersimpan</strong><br>
                    <small class="text-muted">Presensi tanggal ini sudah pernah diisi. Menyimpan ulang akan memperbarui status kehadiran GTK.</small>
                </div>
            </div>
            <div class="d-flex mt-2 mt-md-0">
                <span class="badge badge-success px-3 py-2 mr-2 font-weight-bold" style="font-size: 0.82rem;"><i class="fas fa-check mr-1"></i><?= $jml_hadir ?> Hadir</span>
                <span class="badge badge-danger px-3 py-2 font-weight-bold" style="font-size: 0.82rem;"><i class="fas fa-times mr-1"></i><?= $jml_tidak ?> Tidak Hadir</span>
            </div>
        </div>
    </div>
    <?php elseif (!empty($is_past_date)): ?>
    <!-- Banner Edit Mode - Tanggal Lalu -->
    <div class="callout callout-warning mb-3 shadow-sm" style="border-radius: 10px;">
        <div class="d-flex align-items-center">
            <i class="fas fa-history fa-2x text-warning mr-3"></i>
            <div>
                <strong>Mode Edit — Presensi Tanggal Lalu</strong><br>
                <small class="text-muted">Anda sedang menginput/mengubah presensi GTK untuk tanggal yang telah lalu (<?= DateHelper::formatTanggal($tanggal, 'long') ?>).</small>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card card-info card-outline shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white"><h3 class="card-title font-weight-bold text-dark"><i class="fas fa-calendar-alt mr-2 text-info"></i> Pilih Tanggal Presensi</h3></div>
        <form method="GET">
            <input type="hidden" name="mod" value="absensi_guru">
            <div class="card-body row align-items-center py-3">
                <div class="form-group col-md-3 mb-0">
                    <label class="font-weight-bold text-secondary text-sm">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= htmlspecialchars($tanggal) ?>" onchange="this.form.submit()" style="border-radius: 8px;">
                </div>
                <div class="form-group col-md-9 mb-0 d-flex align-items-end">
                    <span class="text-muted text-sm">
                        <i class="fas fa-info-circle text-primary mr-1"></i> Menampilkan GTK yang wajib hadir hari ini berdasarkan <strong>Jadwal Mengajar KBM</strong>, <strong>Tugas Piket</strong>, <strong>Hari Ngantor</strong>, serta <strong>Staf TU/Tendik</strong>.
                    </span>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB NAVIGASI: PRESENSI FISIK VS MONITORING KBM ONLINE -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #fff;">
        <div class="card-body py-2 px-3">
            <ul class="nav nav-pills" id="tabPresensiGuru" role="tablist" style="gap: 8px;">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold active text-white" id="fisik-tab" data-toggle="pill" href="#tab-fisik" role="tab" style="border-radius: 8px; font-size: 0.85rem;">
                        <i class="fas fa-school mr-1.5"></i> Presensi GTK Fisik / Tatap Muka
                        <span class="badge badge-light ml-1 text-primary"><?= count($guru_list) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-dark bg-white border" id="online-tab" data-toggle="pill" href="#tab-online" role="tab" style="border-radius: 8px; font-size: 0.85rem;">
                        <i class="fas fa-globe text-success mr-1.5"></i> Monitoring KBM Daring (Online LMS)
                        <span class="badge badge-success ml-1"><?= count($guru_online_list) ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content" id="tabPresensiGuruContent">
        <!-- TAB 1: PRESENSI GTK FISIK DI SEKOLAH -->
        <div class="tab-pane fade show active" id="tab-fisik" role="tabpanel" aria-labelledby="fisik-tab">
            <form action="<?= BASE_URL ?>absensi_guru/save" method="POST">
                <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-list-check mr-2 text-primary"></i> Daftar Presensi GTK Fisik &bull; <?= DateHelper::formatTanggal($tanggal, 'long') ?>
                        </h5>
                        <span class="badge badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.78rem;">
                            <?= count($guru_list) ?> GTK Wajib Hadir di Sekolah
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" style="font-size: 0.85rem;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th style="min-width: 200px;">Nama GTK & Jabatan</th>
                                        <th style="min-width: 240px;">Kewajiban / Penugasan Hari Ini</th>
                                        <th style="min-width: 230px;" class="text-center">Status Kehadiran</th>
                                        <th style="min-width: 170px;">Tugas Mandiri (Jika Berhalangan)</th>
                                        <th style="min-width: 160px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($guru_list)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted font-italic">
                                                <i class="fas fa-coffee fa-2x mb-2 d-block text-secondary" style="opacity: 0.4;"></i>
                                                Tidak ada jadwal KBM tatap muka, tugas piket, atau hari kerja GTK fisik pada hari ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                    <?php $no=1; foreach($guru_list as $g): 
                                        $absensi = $absensi_hari_ini[$g['id_guru']] ?? null;
                                        $status = $absensi['status'] ?? 'Hadir';
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-muted"><?= $no++ ?></td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-dark"><?= htmlspecialchars($g['nama']) ?></div>
                                            <div class="d-flex align-items-center mt-1" style="gap: 4px;">
                                                <span class="badge badge-light border text-secondary" style="font-size: 0.68rem;"><?= htmlspecialchars($g['peran_list'] ?: 'Guru') ?></span>
                                                <?php if (!empty($g['kode_guru'])): ?>
                                                    <span class="badge badge-secondary" style="font-size: 0.65rem;"><?= htmlspecialchars($g['kode_guru']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex flex-column" style="gap: 4px;">
                                                <?php if (!empty($g['is_tendik'])): ?>
                                                    <div>
                                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.70rem;">
                                                            <i class="fas fa-building mr-1"></i> Staf TU / Tendik
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($g['jadwal_hari_ini'])): ?>
                                                    <div>
                                                        <span class="badge badge-primary px-2 py-0 font-weight-bold" style="font-size: 0.68rem;">
                                                            <i class="fas fa-chalkboard mr-1"></i> KBM Tatap Muka:
                                                        </span>
                                                        <div class="text-muted mt-1" style="font-size: 0.74rem; line-height: 1.35;">
                                                            <?= $g['jadwal_hari_ini'] ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($g['is_piket'])): ?>
                                                    <div>
                                                        <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 0.68rem;">
                                                            <i class="fas fa-user-shield mr-1"></i> Petugas Piket <?= !empty($g['keterangan_piket']) ? '('.htmlspecialchars($g['keterangan_piket']).')' : '' ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($g['is_non_kbm'])): ?>
                                                    <div>
                                                        <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.68rem;">
                                                            <i class="fas fa-clipboard-list mr-1"></i> <?= htmlspecialchars($g['jenis_tugas_non_kbm'] ?: 'Ngantor / Standby') ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group btn-group-toggle shadow-none" data-toggle="buttons">
                                                <label class="btn btn-sm btn-outline-success <?= $status == 'Hadir' ? 'active' : '' ?>" style="border-radius: 6px 0 0 6px;">
                                                    <input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Hadir" <?= $status == 'Hadir' ? 'checked' : '' ?>> Hadir
                                                </label>
                                                <label class="btn btn-sm btn-outline-warning <?= $status == 'Sakit' ? 'active' : '' ?>">
                                                    <input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Sakit" <?= $status == 'Sakit' ? 'checked' : '' ?>> Sakit
                                                </label>
                                                <label class="btn btn-sm btn-outline-info <?= $status == 'Izin' ? 'active' : '' ?>">
                                                    <input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Izin" <?= $status == 'Izin' ? 'checked' : '' ?>> Izin
                                                </label>
                                                <label class="btn btn-sm btn-outline-danger <?= $status == 'Alpa' ? 'active' : '' ?>" style="border-radius: 0 6px 6px 0;">
                                                    <input type="radio" name="absensi[<?= $g['id_guru'] ?>][status]" value="Alpa" <?= $status == 'Alpa' ? 'checked' : '' ?>> Alpa
                                                </label>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" name="absensi[<?= $g['id_guru'] ?>][tugas]" class="form-control form-control-sm" placeholder="Tugas / Guru Pengganti..." value="<?= htmlspecialchars($absensi['tugas'] ?? '') ?>" style="border-radius: 6px;">
                                        </td>
                                        <td class="align-middle">
                                            <input type="text" name="absensi[<?= $g['id_guru'] ?>][keterangan]" class="form-control form-control-sm" placeholder="Catatan..." value="<?= htmlspecialchars($absensi['keterangan'] ?? '') ?>" style="border-radius: 6px;">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
                        <?php if (!empty($guru_list)): ?>
                        <button type="submit" class="btn btn-<?= !empty($has_existing_data) ? 'warning' : 'primary' ?> px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                            <i class="fas fa-<?= !empty($has_existing_data) ? 'sync-alt' : 'save' ?> mr-1"></i>
                            <?= !empty($has_existing_data) ? 'Update Presensi GTK' : 'Simpan Presensi GTK' ?>
                        </button>
                        <?php if (!empty($has_existing_data)): ?>
                        <small class="text-muted">
                            <i class="fas fa-info-circle text-info"></i> Menyimpan ulang akan memperbarui data presensi sebelumnya.
                        </small>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 2: MONITORING KBM DARING / ONLINE LMS -->
        <div class="tab-pane fade" id="tab-online" role="tabpanel" aria-labelledby="online-tab">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-globe mr-2 text-success"></i> Monitoring KBM Daring (Online LMS) &bull; <?= DateHelper::formatTanggal($tanggal, 'long') ?>
                    </h5>
                    <span class="badge badge-success px-3 py-1 font-weight-bold" style="font-size: 0.78rem;">
                        <?= count($guru_online_list) ?> Sesi Daring Terjadwal
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0" style="font-size: 0.85rem;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th style="min-width: 180px;">Guru Pengampu</th>
                                    <th style="min-width: 140px;">Rombel / Kelas</th>
                                    <th style="min-width: 180px;">Mata Pelajaran</th>
                                    <th style="min-width: 110px;" class="text-center">Waktu KBM</th>
                                    <th style="min-width: 160px;" class="text-center">Jurnal KBM (LMS)</th>
                                    <th style="min-width: 150px;" class="text-center">Presensi Siswa</th>
                                    <th style="min-width: 120px;" class="text-center">Aksi / Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($guru_online_list)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted font-italic">
                                            <i class="fas fa-laptop-house fa-2x mb-2 d-block text-secondary" style="opacity: 0.4;"></i>
                                            Tidak ada jadwal KBM Daring / Online LMS pada hari ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php $no_on=1; foreach($guru_online_list as $on): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted"><?= $no_on++ ?></td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark"><?= htmlspecialchars($on['nama_guru']) ?></div>
                                        <?php if (!empty($on['kode_guru'])): ?>
                                            <span class="badge badge-secondary" style="font-size: 0.65rem;"><?= htmlspecialchars($on['kode_guru']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-primary" style="font-size: 0.85rem;">
                                            <?= htmlspecialchars($on['nama_kelas_gabung']) ?>
                                        </div>
                                        <span class="badge badge-success px-1.5 py-0.5 mt-1 font-weight-bold" style="font-size: 0.60rem;">
                                            <i class="fas fa-layer-group mr-1"></i><?= $on['total_kelas'] > 1 ? $on['total_kelas'].' Kelas Merger' : 'Kelas Terbuka' ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark"><?= htmlspecialchars($on['nama_mapel']) ?></div>
                                        <span class="badge badge-info px-1 py-0.5 rounded-pill font-weight-bold" style="font-size: 0.58rem;">🌐 ONLINE</span>
                                    </td>
                                    <td class="align-middle text-center" style="font-size: 0.76rem;">
                                        <span class="font-weight-bold text-dark"><?= substr($on['jam_mulai'], 0, 5) ?></span> - 
                                        <span class="font-weight-bold text-dark"><?= substr($on['jam_selesai'], 0, 5) ?></span>
                                        <?php if (($on['jp_count'] ?? 1) > 1): ?>
                                            <div><span class="badge badge-light border text-primary"><?= $on['jp_count'] ?> JP</span></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($on['all_jurnal_done']): ?>
                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-check-circle mr-1"></i> Jurnal Terisi (<?= $on['total_jurnal_terisi'] ?>/<?= $on['total_kelas'] ?>)
                                            </span>
                                        <?php elseif ($on['total_jurnal_terisi'] > 0): ?>
                                            <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-clock mr-1"></i> Sebagian (<?= $on['total_jurnal_terisi'] ?>/<?= $on['total_kelas'] ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-times-circle mr-1"></i> Belum Mengisi
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($on['all_absen_done']): ?>
                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-check-double mr-1"></i> Terabsen (<?= $on['total_absen_terisi'] ?>/<?= $on['total_kelas'] ?>)
                                            </span>
                                        <?php elseif ($on['total_absen_terisi'] > 0): ?>
                                            <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Sebagian (<?= $on['total_absen_terisi'] ?>/<?= $on['total_kelas'] ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="fas fa-minus mr-1"></i> Belum Terdata
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if (!empty($on['kelas_list'])): ?>
                                            <?php if (count($on['kelas_list']) == 1): ?>
                                                <a href="<?= BASE_URL ?>jurnal_kbm?id_kelas=<?= $on['kelas_list'][0]['id_kelas'] ?>&tanggal=<?= $tanggal ?>" class="btn btn-xs btn-outline-info font-weight-bold shadow-xs px-2 py-1" style="border-radius: 6px;">
                                                    <i class="fas fa-eye mr-1"></i> Jurnal KBM
                                                </a>
                                            <?php else: ?>
                                                <div class="d-flex flex-column" style="gap: 3px;">
                                                    <?php foreach ($on['kelas_list'] as $ck): ?>
                                                        <a href="<?= BASE_URL ?>jurnal_kbm?id_kelas=<?= $ck['id_kelas'] ?>&tanggal=<?= $tanggal ?>" class="btn btn-xs btn-<?= $ck['has_jurnal'] ? 'success' : 'outline-info' ?> font-weight-bold px-1.5 py-0.5 text-left text-truncate" style="border-radius: 4px; font-size: 0.65rem;">
                                                            <i class="fas fa-<?= $ck['has_jurnal'] ? 'check' : 'pen' ?> mr-1"></i> Jurnal <?= htmlspecialchars($ck['nama_kelas']) ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
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
</div>
</section>

<script>
$('#tabPresensiGuru a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
    $('#tabPresensiGuru a').removeClass('active text-white').addClass('text-dark bg-white border');
    $(e.target).addClass('active text-white').removeClass('text-dark bg-white border');
});
</script>
<?php include __DIR__.'/partials/footer.php'; ?>