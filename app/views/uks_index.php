<?php
// app/views/uks_index.php

// 1. INCLUDE HEADER
$path_header = __DIR__ . '/partials/header.php';
if (file_exists($path_header))
    include $path_header;

$active_tab = $_GET['tab'] ?? 'rekam_medis';
?>

<style>
    /* Styling Nav Tabs UKS */
    .uks-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .uks-nav-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        color: #64748b;
        font-weight: 600;
        font-size: 0.86rem;
        padding: 10px 18px;
        background: transparent;
        transition: all 0.2s;
    }
    .uks-nav-tabs .nav-link:hover {
        color: #0d9488;
        background: #f0fdfa;
    }
    .uks-nav-tabs .nav-link.active {
        color: #0d9488;
        background: #ffffff;
        border-bottom: 3px solid #0d9488;
        font-weight: 700;
    }

    /* Stat Card UKS */
    .uks-stat-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }
    .uks-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: 14px;
        flex-shrink: 0;
    }
</style>

<div class="content-header pt-3 pb-2">
    <div class="container-fluid">
        <!-- HEADER UTAMA UKS -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); color: #ffffff;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 12px;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; font-size: 1.45rem; color: #5eead4; flex-shrink: 0;">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div>
                            <h4 class="font-weight-bold text-white mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.2rem;">
                                Unit Kesehatan Sekolah (UKS)
                            </h4>
                            <p class="mb-0 text-white-50 small">
                                Pelayanan kesehatan siswa &amp; GTK, rekam medis harian, inventaris obat &amp; P3K, serta administrasi program kesehatan sekolah.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <form method="GET" action="<?= BASE_URL ?>uks" class="d-inline-flex align-items-center">
                            <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                            <select name="id_ta" class="form-control form-control-sm text-sm border-0 font-weight-bold" onchange="this.form.submit()" style="border-radius: 8px; background: rgba(255,255,255,0.9); color: #0f766e;">
                                <?php foreach ($tahun_ajaran as $ta): ?>
                                    <option value="<?= $ta['id_ta'] ?>" <?= ($ta['id_ta'] == $id_ta) ? 'selected' : '' ?>>
                                        TA: <?= htmlspecialchars($ta['nama_ta']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- STAT MINI WIDGETS -->
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <div class="uks-stat-card">
                    <div class="uks-stat-icon" style="background: #f0fdfa; color: #0d9488; border: 1px solid #99f6e4;">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div>
                        <div class="text-muted small" style="font-size: 0.72rem;">Hari Ini</div>
                        <div class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?= $stats['kunjungan_hari_ini'] ?> <small class="text-muted font-weight-normal" style="font-size: 0.75rem;">Pasien</small></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <div class="uks-stat-card">
                    <div class="uks-stat-icon" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <div class="text-muted small" style="font-size: 0.72rem;">Total Istirahat</div>
                        <div class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?= $stats['total_istirahat'] ?> <small class="text-muted font-weight-normal" style="font-size: 0.75rem;">Kali</small></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <div class="uks-stat-card">
                    <div class="uks-stat-icon" style="background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;">
                        <i class="fas fa-ambulance"></i>
                    </div>
                    <div>
                        <div class="text-muted small" style="font-size: 0.72rem;">Rujuk RS/Puskesmas</div>
                        <div class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?= $stats['total_rujuk'] ?> <small class="text-muted font-weight-normal" style="font-size: 0.75rem;">Kali</small></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                <div class="uks-stat-card">
                    <div class="uks-stat-icon" style="background: #fdf4ff; color: #a855f7; border: 1px solid #f0abfc;">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div>
                        <div class="text-muted small" style="font-size: 0.72rem;">Stok Obat &amp; Alkes</div>
                        <div class="font-weight-bold text-dark" style="font-size: 1.15rem;"><?= $stats['total_obat_tersedia'] ?> <small class="text-muted font-weight-normal" style="font-size: 0.75rem;">Item</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAV TABS -->
        <ul class="nav uks-nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= ($active_tab === 'rekam_medis') ? 'active' : '' ?>" href="<?= BASE_URL ?>uks?tab=rekam_medis&id_ta=<?= $id_ta ?>">
                    <i class="fas fa-notes-medical mr-1"></i> Rekam Medis &amp; Pelayanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($active_tab === 'obat') ? 'active' : '' ?>" href="<?= BASE_URL ?>uks?tab=obat&id_ta=<?= $id_ta ?>">
                    <i class="fas fa-capsules mr-1"></i> Stok Obat &amp; P3K
                    <?php if ($stats['obat_hampir_habis'] > 0): ?>
                        <span class="badge badge-warning ml-1" style="font-size: 0.65rem;"><?= $stats['obat_hampir_habis'] ?> Menipis</span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($active_tab === 'administrasi') ? 'active' : '' ?>" href="<?= BASE_URL ?>uks?tab=administrasi&id_ta=<?= $id_ta ?>">
                    <i class="fas fa-clipboard-list mr-1"></i> Program Kerja &amp; Agenda
                </a>
            </li>
        </ul>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <?php if ($active_tab === 'rekam_medis'): ?>
            <!-- TAB 1: REKAM MEDIS & PASIEN BEROBAT -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 10px;">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-stethoscope text-teal mr-1"></i> Catatan Pasien &amp; Rekam Medis Berobat</h6>
                        <small class="text-muted">Daftar riwayat keluhan, tindakan medis ringan, dan pemberian obat di UKS.</small>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <button type="button" class="btn btn-teal btn-sm font-weight-bold px-3 shadow-xs" data-toggle="modal" data-target="#modalTambahPasien" style="border-radius: 8px; background: #0d9488; color: #fff;">
                            <i class="fas fa-user-plus mr-1"></i> + Catat Pasien Berobat
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-sm">
                            <thead class="bg-light text-muted" style="font-size: 0.76rem; text-transform: uppercase;">
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th style="width: 120px;">Tgl &amp; Jam</th>
                                    <th>Identitas Pasien</th>
                                    <th>Keluhan &amp; Tanda Vital</th>
                                    <th>Tindakan &amp; Obat</th>
                                    <th>Tindak Lanjut</th>
                                    <th>Petugas</th>
                                    <th class="text-center" style="width: 110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kunjungan_list)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-notes-medical fa-2x mb-2 text-muted opacity-50"></i>
                                            <p class="mb-0">Belum ada catatan rekam medis pasien di periode ini.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($kunjungan_list as $row): 
                                        $badge_class = 'badge-success';
                                        if ($row['status_tindak_lanjut'] === 'Istirahat di UKS') $badge_class = 'badge-warning';
                                        elseif ($row['status_tindak_lanjut'] === 'Rujuk ke Puskesmas/RS') $badge_class = 'badge-danger';
                                        elseif ($row['status_tindak_lanjut'] === 'Pulang ke Rumah') $badge_class = 'badge-secondary';
                                    ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <div class="font-weight-bold text-dark"><?= $row['tgl_indo'] ?></div>
                                                <div class="small text-muted"><i class="far fa-clock"></i> <?= substr($row['jam_masuk'], 0, 5) ?><?= $row['jam_keluar'] ? ' - ' . substr($row['jam_keluar'], 0, 5) : '' ?></div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($row['nama_pasien']) ?></div>
                                                <div class="small text-muted">
                                                    <span class="badge badge-light border"><?= $row['tipe_pasien'] ?></span> <?= htmlspecialchars($row['kelas_unit']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-danger"><i class="fas fa-head-side-cough mr-1"></i> <?= htmlspecialchars($row['keluhan']) ?></div>
                                                <div class="small text-muted mt-1">
                                                    <?php if (!empty($row['suhu_tubuh'])): ?>
                                                        <span class="mr-2"><i class="fas fa-thermometer-half text-warning"></i> <?= htmlspecialchars($row['suhu_tubuh']) ?> &deg;C</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($row['tekanan_darah'])): ?>
                                                        <span><i class="fas fa-heartbeat text-danger"></i> <?= htmlspecialchars($row['tekanan_darah']) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($row['diagnosa_awal'])): ?>
                                                        <div><span class="font-weight-bold">Diag:</span> <?= htmlspecialchars($row['diagnosa_awal']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark"><?= htmlspecialchars($row['tindakan'] ?: '-') ?></div>
                                                <?php if (!empty($row['obat_diberikan'])): ?>
                                                    <div class="small text-teal mt-1 font-weight-bold"><i class="fas fa-pills mr-1"></i> <?= htmlspecialchars($row['obat_diberikan']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badge_class ?> px-2 py-1" style="font-size: 0.72rem; border-radius: 6px;">
                                                    <?= $row['status_tindak_lanjut'] ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?= htmlspecialchars($row['petugas_jaga'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <a href="<?= BASE_URL ?>uks/cetak_surat_izin?id=<?= $row['id_kunjungan'] ?>" target="_blank" class="btn btn-xs btn-outline-info" title="Cetak Surat Keterangan Sakit/Izin" style="border-radius: 6px;">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>uks/delete_kunjungan?id=<?= $row['id_kunjungan'] ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Hapus catatan rekam medis ini?')" title="Hapus" style="border-radius: 6px;">
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

        <?php elseif ($active_tab === 'obat'): ?>
            <!-- TAB 2: STOK OBAT & ALKES -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 10px;">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-pills text-purple mr-1"></i> Inventaris Obat, Vitamin, &amp; Alat Kesehatan (Alkes)</h6>
                        <small class="text-muted">Monitoring ketersediaan stok obat, tanggal kadaluarsa, dan perlengkapan P3K UKS.</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm font-weight-bold px-3" data-toggle="modal" data-target="#modalTambahObat" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-1"></i> + Tambah Data Obat / Alkes
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-sm">
                            <thead class="bg-light text-muted" style="font-size: 0.76rem; text-transform: uppercase;">
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th>Kode / Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Sisa Stok</th>
                                    <th>Satuan</th>
                                    <th>Tgl Kadaluarsa</th>
                                    <th>Indikasi / Kegunaan</th>
                                    <th class="text-center" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($obat_list)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-capsules fa-2x mb-2 text-muted opacity-50"></i>
                                            <p class="mb-0">Belum ada inventaris obat atau alkes yang terdaftar.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($obat_list as $ob): 
                                        $is_low = $ob['stok'] <= $ob['stok_minimum'];
                                        $is_exp = !empty($ob['tgl_kadaluarsa']) && strtotime($ob['tgl_kadaluarsa']) <= time();
                                    ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <div class="font-weight-bold text-dark"><?= htmlspecialchars($ob['nama_obat']) ?></div>
                                                <?php if (!empty($ob['kode_obat'])): ?>
                                                    <span class="small text-muted font-monospace"><?= htmlspecialchars($ob['kode_obat']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border text-muted"><?= htmlspecialchars($ob['kategori']) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($is_low): ?>
                                                    <span class="badge badge-warning px-2 py-1 font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i> <?= $ob['stok'] ?> (Menipis)</span>
                                                <?php else: ?>
                                                    <span class="font-weight-bold text-dark"><?= $ob['stok'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($ob['satuan']) ?></td>
                                            <td>
                                                <?php if (empty($ob['tgl_kadaluarsa'])): ?>
                                                    <span class="text-muted">-</span>
                                                <?php elseif ($is_exp): ?>
                                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-calendar-times mr-1"></i> <?= date('d/m/Y', strtotime($ob['tgl_kadaluarsa'])) ?> (Expired)</span>
                                                <?php else: ?>
                                                    <span><?= date('d/m/Y', strtotime($ob['tgl_kadaluarsa'])) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-muted"><?= htmlspecialchars($ob['kegunaan_indikasi'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <a href="<?= BASE_URL ?>uks/delete_obat?id=<?= $ob['id_obat'] ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Hapus data obat ini?')" style="border-radius: 6px;">
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

        <?php elseif ($active_tab === 'administrasi'): ?>
            <!-- TAB 3: ADMINISTRASI & PROGRAM KERJA -->
            <div class="row">
                <div class="col-md-5 mb-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-calendar-plus text-teal mr-1"></i> Tambah Agenda Kegiatan UKS</h6>
                        </div>
                        <form action="<?= BASE_URL ?>uks/save_agenda" method="POST">
                            <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
                            <div class="card-body">
                                <div class="form-group text-sm">
                                    <label class="font-weight-bold">Nama Kegiatan / Program</label>
                                    <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Pemeriksaan Kesehatan Berkala / Skrining TB" required>
                                </div>
                                <div class="form-group text-sm">
                                    <label class="font-weight-bold">Tanggal Pelaksanaan</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="form-group text-sm">
                                    <label class="font-weight-bold">Tempat</label>
                                    <input type="text" name="tempat" class="form-control" value="Ruang UKS / Aula Sekolah">
                                </div>
                                <div class="form-group text-sm">
                                    <label class="font-weight-bold">Keterangan / Sasaran Peserta</label>
                                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Peserta: Siswa Kelas X / Kerjasama Puskesmas..."></textarea>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top text-right">
                                <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-4" style="background: #0d9488; color: #fff; border-radius: 8px;">
                                    <i class="fas fa-save mr-1"></i> Simpan Agenda
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-7 mb-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-tasks text-primary mr-1"></i> Daftar Agenda &amp; Program Kerja UKS</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 text-sm">
                                    <thead class="bg-light text-muted" style="font-size: 0.76rem; text-transform: uppercase;">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kegiatan</th>
                                            <th>Tempat</th>
                                            <th class="text-center" style="width: 50px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($agendas)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Belum ada agenda UKS yang dicatat.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($agendas as $ag): ?>
                                                <tr>
                                                    <td class="font-weight-bold text-teal" style="width: 110px;">
                                                        <?= date('d/m/Y', strtotime($ag['tanggal'])) ?>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold text-dark"><?= htmlspecialchars($ag['nama_kegiatan']) ?></div>
                                                        <div class="small text-muted"><?= htmlspecialchars($ag['keterangan'] ?: '') ?></div>
                                                    </td>
                                                    <td class="text-muted small"><?= htmlspecialchars($ag['tempat'] ?: '-') ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= BASE_URL ?>uks/delete_agenda?id=<?= $ag['id_agenda'] ?>" class="text-danger" onclick="return confirm('Hapus agenda ini?')" title="Hapus">
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
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- MODAL TAMBAH PASIEN BEROBAT -->
<div class="modal fade" id="modalTambahPasien" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= BASE_URL ?>uks/save_kunjungan" method="POST">
            <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
            <input type="hidden" name="id_pasien" id="input_id_pasien" value="">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header bg-teal text-white" style="background: #0d9488;">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-user-injured mr-2"></i> Form Pasien Berobat / UKS</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Tipe Pasien</label>
                            <select name="tipe_pasien" id="tipe_pasien_select" class="form-control" onchange="togglePasienSelector()">
                                <option value="Siswa">Siswa</option>
                                <option value="Guru">Guru</option>
                                <option value="Tendik">Tenaga Kependidikan / TU</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Jam Masuk UKS</label>
                            <input type="time" name="jam_masuk" class="form-control" value="<?= date('H:i') ?>" required>
                        </div>
                    </div>

                    <!-- SELECTOR SISWA -->
                    <div id="selector_siswa_box" class="form-group text-sm">
                        <label class="font-weight-bold">Pilih Siswa (Cari Nama / NIS)</label>
                        <select id="select_siswa" class="form-control" onchange="pilihSiswa(this)">
                            <option value="">-- Pilih Siswa atau Ketik Manual di Bawah --</option>
                            <?php foreach ($siswa_list as $sw): ?>
                                <option value="<?= $sw['id_siswa'] ?>" data-nama="<?= htmlspecialchars($sw['nama']) ?>" data-kelas="<?= htmlspecialchars($sw['nama_kelas'] ?? '') ?>">
                                    <?= htmlspecialchars($sw['nama']) ?> (<?= htmlspecialchars($sw['nama_kelas'] ?? 'Belum ada kelas') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- SELECTOR GURU/TENDIK (Hidden default) -->
                    <div id="selector_guru_box" class="form-group text-sm" style="display: none;">
                        <label class="font-weight-bold">Pilih Guru / Tendik</label>
                        <select id="select_guru" class="form-control" onchange="pilihGuru(this)">
                            <option value="">-- Pilih Guru / Tendik --</option>
                            <?php foreach ($guru_list as $gr): ?>
                                <option value="<?= $gr['id_guru'] ?>" data-nama="<?= htmlspecialchars($gr['nama']) ?>" data-jabatan="<?= htmlspecialchars($gr['jabatan'] ?? 'GTK') ?>">
                                    <?= htmlspecialchars($gr['nama']) ?> (<?= htmlspecialchars($gr['jabatan'] ?? 'GTK') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-7 form-group text-sm">
                            <label class="font-weight-bold">Nama Pasien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pasien" id="input_nama_pasien" class="form-control" placeholder="Nama lengkap pasien" required>
                        </div>
                        <div class="col-md-5 form-group text-sm">
                            <label class="font-weight-bold">Kelas / Unit / Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="kelas_unit" id="input_kelas_unit" class="form-control" placeholder="Contoh: X.1 / Guru PJOK" required>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row">
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold text-danger">Keluhan Utama <span class="text-danger">*</span></label>
                            <textarea name="keluhan" class="form-control" rows="2" placeholder="Pusing, demam, luka lecet di lutut, sakit perut..." required></textarea>
                        </div>
                        <div class="col-md-3 form-group text-sm">
                            <label class="font-weight-bold">Suhu Tubuh (&deg;C)</label>
                            <input type="text" name="suhu_tubuh" class="form-control" placeholder="37.2">
                        </div>
                        <div class="col-md-3 form-group text-sm">
                            <label class="font-weight-bold">Tekanan Darah</label>
                            <input type="text" name="tekanan_darah" class="form-control" placeholder="120/80">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Diagnosa Awal</label>
                            <input type="text" name="diagnosa_awal" class="form-control" placeholder="Contoh: Cephalea (Sakit Kepala) / Dispepsia">
                        </div>
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Tindakan Medis / Pertolongan</label>
                            <input type="text" name="tindakan" class="form-control" placeholder="Kompres hangat, istirahat berbaring, betadine...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Obat yang Diberikan</label>
                            <input type="text" name="obat_diberikan" class="form-control" placeholder="Paracetamol 500mg (1 tab), Antasida (1 tab)...">
                        </div>
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold text-teal">Status / Tindak Lanjut</label>
                            <select name="status_tindak_lanjut" class="form-control font-weight-bold">
                                <option value="Kembali ke Kelas">Kembali ke Kelas</option>
                                <option value="Istirahat di UKS">Istirahat di UKS</option>
                                <option value="Rujuk ke Puskesmas/RS">Rujuk ke Puskesmas / RS</option>
                                <option value="Pulang ke Rumah">Pulang ke Rumah</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Petugas UKS</label>
                            <input type="text" name="petugas_jaga" class="form-control" value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas UKS') ?>">
                        </div>
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Keterangan Tambahan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Catatan walas / orang tua sudah dihubungi...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-4" style="background: #0d9488; color: #fff; border-radius: 8px;">
                        <i class="fas fa-save mr-1"></i> Simpan Rekam Medis
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH OBAT -->
<div class="modal fade" id="modalTambahObat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= BASE_URL ?>uks/save_obat" method="POST">
            <input type="hidden" name="id_ta" value="<?= $id_ta ?>">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-capsules mr-2"></i> Tambah Data Obat / Alkes</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Kode Obat</label>
                            <input type="text" name="kode_obat" class="form-control" placeholder="OBT-001">
                        </div>
                        <div class="col-md-8 form-group text-sm">
                            <label class="font-weight-bold">Nama Obat / Alkes <span class="text-danger">*</span></label>
                            <input type="text" name="nama_obat" class="form-control" placeholder="Contoh: Paracetamol 500mg" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="Obat Bebas">Obat Bebas</option>
                                <option value="Obat Keras/Resep">Obat Keras/Resep</option>
                                <option value="P3K & Alkes">P3K &amp; Alkes</option>
                                <option value="Vitamin & Suplemen">Vitamin &amp; Suplemen</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group text-sm">
                            <label class="font-weight-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="Tablet / Strip / Botol / Pcs" value="Tablet">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Jumlah Stok</label>
                            <input type="number" name="stok" class="form-control" value="10" min="0">
                        </div>
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Stok Minimum</label>
                            <input type="number" name="stok_minimum" class="form-control" value="5" min="1">
                        </div>
                        <div class="col-md-4 form-group text-sm">
                            <label class="font-weight-bold">Tgl Kadaluarsa</label>
                            <input type="date" name="tgl_kadaluarsa" class="form-control">
                        </div>
                    </div>
                    <div class="form-group text-sm">
                        <label class="font-weight-bold">Indikasi / Khasiat</label>
                        <input type="text" name="kegunaan_indikasi" class="form-control" placeholder="Pereda demam dan sakit kepala">
                    </div>
                    <div class="form-group text-sm">
                        <label class="font-weight-bold">Catatan / Dosis</label>
                        <input type="text" name="catatan" class="form-control" placeholder="Dewasa 1 tab 3x sehari setelah makan">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-4" style="border-radius: 8px;">
                        <i class="fas fa-save mr-1"></i> Simpan Obat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasienSelector() {
    const tipe = document.getElementById('tipe_pasien_select').value;
    const boxSiswa = document.getElementById('selector_siswa_box');
    const boxGuru = document.getElementById('selector_guru_box');
    if (tipe === 'Siswa') {
        boxSiswa.style.display = 'block';
        boxGuru.style.display = 'none';
    } else {
        boxSiswa.style.display = 'none';
        boxGuru.style.display = 'block';
    }
}

function pilihSiswa(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('input_id_pasien').value = opt.value;
        document.getElementById('input_nama_pasien').value = opt.getAttribute('data-nama') || '';
        document.getElementById('input_kelas_unit').value = opt.getAttribute('data-kelas') || '';
    }
}

function pilihGuru(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('input_id_pasien').value = opt.value;
        document.getElementById('input_nama_pasien').value = opt.getAttribute('data-nama') || '';
        document.getElementById('input_kelas_unit').value = opt.getAttribute('data-jabatan') || 'Guru';
    }
}
</script>

<?php
// INCLUDE FOOTER
$path_footer = __DIR__ . '/partials/footer.php';
if (file_exists($path_footer))
    include $path_footer;
?>
