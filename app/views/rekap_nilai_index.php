<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Rekapitulasi Nilai &amp; Leger Nilai
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Rekap Nilai</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <div class="card card-outline card-primary shadow-sm mb-3">
            <div class="card-body p-2">
                <form action="<?= BASE_URL ?>rekap_nilai" method="GET" id="filterForm" class="form-inline d-flex justify-content-center align-items-center flex-wrap">
                    <input type="hidden" name="mod" value="rekap_nilai">
                    
                    <span class="mr-3 font-weight-bold"><i class="fas fa-filter text-primary mr-1"></i> Pilih Kelas dan Mata Pelajaran:</span>

                    <div class="form-group mx-2">
                        <select name="id_kelas" id="id_kelas" class="form-control form-control-sm select2" onchange="document.getElementById('filterForm').submit()" style="min-width: 180px;">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_diajar ?? [] as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= $k['nama_kelas'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mx-2">
                        <select name="id_guru_mapel" id="id_guru_mapel" class="form-control form-control-sm select2" onchange="document.getElementById('filterForm').submit()" style="min-width: 220px;" <?= empty($mapel_diajar) ? 'disabled' : '' ?>>
                            <option value="">-- Semua / Pilih Mapel --</option>
                            <?php foreach ($mapel_diajar ?? [] as $m): ?>
                                <option value="<?= $m['id_guru_mapel'] ?>" <?= ($id_guru_mapel_filter == $m['id_guru_mapel']) ? 'selected' : '' ?>>
                                    <?= $m['nama_mapel'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($id_kelas_filter): ?>
            
            <?php if ($is_wali): ?>
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs mb-3" id="rekapTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?= $id_guru_mapel_filter ? 'active' : '' ?>" id="mapel-tab" data-toggle="tab" href="#mapel-content" role="tab"><i class="fas fa-book-open mr-1"></i> Rekap Per Mata Pelajaran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= !$id_guru_mapel_filter ? 'active' : '' ?>" id="kolektif-tab" data-toggle="tab" href="#kolektif-content" role="tab"><i class="fas fa-layer-group mr-1"></i> Rekap Kolektif (Wali Kelas)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="deskripsi-tab" data-toggle="tab" href="#deskripsi-content" role="tab"><i class="fas fa-file-alt mr-1"></i> Rekap Deskripsi Kolektif</a>
                </li>
            </ul>
            <?php endif; ?>

            <div class="tab-content" id="rekapTabContent">
                <!-- Tab 1: Rekap Per Mapel -->
                <div class="tab-pane fade <?= ($id_guru_mapel_filter || !$is_wali) ? 'show active' : '' ?>" id="mapel-content" role="tabpanel">
                    <?php if ($id_guru_mapel_filter): ?>
                        <!-- Panel Bobot -->
                        <div class="card card-<?= $bobot['is_custom'] ? 'warning' : 'primary' ?>">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-balance-scale"></i> Konfigurasi Bobot Penilaian 
                                    <?= $bobot['is_custom'] ? '(Khusus Mapel Ini)' : '(Menggunakan Default Sekolah)' ?>
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="<?= BASE_URL ?>rekap_nilai/simpan_bobot" method="POST">
                                    <input type="hidden" name="id_kelas" value="<?= $id_kelas_filter ?>">
                                    <input type="hidden" name="id_guru_mapel" value="<?= $id_guru_mapel_filter ?>">
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-md-2">
                                            <label>Sikap (%)</label>
                                            <input type="number" class="form-control text-center bobot-input" name="bobot_sikap" value="<?= $bobot['sikap'] ?>" min="0" max="100" step="0.1">
                                            <small class="text-muted">Opsional</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label>LMS (%)</label>
                                            <input type="number" class="form-control text-center bobot-input" name="bobot_lms" value="<?= $bobot['lms'] ?>" min="0" max="100" step="0.1">
                                            <small class="text-muted">Penugasan LMS</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Formatif (%)</label>
                                            <input type="number" class="form-control text-center bobot-input" name="bobot_formatif" value="<?= $bobot['formatif'] ?>" min="0" max="100" step="0.1">
                                            <small class="text-muted">Rata-rata</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Sumatif LM (%)</label>
                                            <input type="number" class="form-control text-center bobot-input" name="bobot_sumatif_lm" value="<?= $bobot['sumatif_lm'] ?>" min="0" max="100" step="0.1">
                                            <small class="text-muted">Per TP / Lingkup</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label>STS (%)</label>
                                            <input type="number" class="form-control text-center bobot-input" name="bobot_sts" value="<?= $bobot['sts'] ?>" min="0" max="100" step="0.1">
                                            <small class="text-muted">Tengah Semester</small>
                                        </div>
                                        <div class="col-md-2">
                                            <label>SAS/SAT (%)</label>
                                            <input type="number" class="form-control text-center bobot-input" name="bobot_sas" value="<?= $bobot['sas'] ?>" min="0" max="100" step="0.1">
                                            <small class="text-muted">Akhir Semester</small>
                                        </div>
                                    </div>

                                    <div class="row bg-light p-3 mb-3 border rounded">
                                        <div class="col-md-12 mb-2">
                                            <strong><i class="fas fa-info-circle"></i> Pengaturan Deskripsi Rapor</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row mb-0">
                                                <label class="col-sm-8 col-form-label">Jumlah TP Maksimum untuk <b>Capaian Tertinggi</b>:</label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control" name="limit_tp_tinggi" value="<?= $bobot['limit_tp_tinggi'] ?>" min="1" max="10">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row mb-0">
                                                <label class="col-sm-8 col-form-label">Jumlah TP Maksimum untuk <b>Perlu Bimbingan</b>:</label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control" name="limit_tp_rendah" value="<?= $bobot['limit_tp_rendah'] ?>" min="1" max="10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            Total Bobot: <strong id="total_bobot" class="text-lg">0</strong>%
                                            <span id="bobot_warning" class="text-danger ml-2" style="display:none;"><i class="fas fa-exclamation-triangle"></i> Total harus 100%</span>
                                        </div>
                                        <div>
                                            <?php if ($bobot['is_custom']): ?>
                                                <button type="submit" name="reset_default" value="1" class="btn btn-outline-danger mr-2" onclick="return confirm('Kembalikan ke persentase default sekolah?')">
                                                    <i class="fas fa-undo"></i> Reset Default
                                                </button>
                                            <?php endif; ?>
                                            <button type="submit" id="btn_simpan_bobot" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Terapkan Bobot
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Rekapitulasi Nilai Akhir - <?= htmlspecialchars($nama_mapel_terpilih) ?></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead class="thead-light text-center">
                                            <tr>
                                                <th rowspan="2" class="align-middle">No</th>
                                                <th rowspan="2" class="align-middle">Nama Siswa</th>
                                                <th colspan="6">Rincian Nilai Berdasarkan Bobot</th>
                                                <th rowspan="2" class="align-middle bg-success text-white">Nilai Akhir (NA)</th>
                                                <th rowspan="2" class="align-middle">Deskripsi Rapor</th>
                                            </tr>
                                            <tr>
                                                <th title="Nilai Sikap">Sikap<br><small><?= $bobot['sikap'] ?>%</small></th>
                                                <th title="Nilai Penugasan LMS">LMS<br><small><?= $bobot['lms'] ?>%</small></th>
                                                <th title="Rata-rata Formatif">Formatif<br><small><?= $bobot['formatif'] ?>%</small></th>
                                                <th title="Rata-rata Sumatif Lingkup Materi">Sum. LM<br><small><?= $bobot['sumatif_lm'] ?>%</small></th>
                                                <th title="Sumatif Tengah Semester">STS<br><small><?= $bobot['sts'] ?>%</small></th>
                                                <th title="Sumatif Akhir Semester">SAS<br><small><?= $bobot['sas'] ?>%</small></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($rekap_data)): ?>
                                                <tr><td colspan="10" class="text-center">Belum ada data siswa / nilai.</td></tr>
                                            <?php else: ?>
                                                <?php $no=1; foreach($rekap_data as $r): ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td>
                                                        <?= htmlspecialchars($r['nama']) ?><br>
                                                        <small class="text-muted"><?= htmlspecialchars($r['nisn']) ?></small>
                                                    </td>
                                                    <td class="text-center"><?= $r['sikap'] ?? '-' ?></td>
                                                    <td class="text-center"><?= $r['lms'] ?? '-' ?></td>
                                                    <td class="text-center"><?= $r['formatif'] ?? '-' ?></td>
                                                    <td class="text-center"><?= $r['sumatif_lm'] ?? '-' ?></td>
                                                    <td class="text-center"><?= $r['sts'] ?? '-' ?></td>
                                                    <td class="text-center"><?= $r['sas'] ?? '-' ?></td>
                                                    
                                                    <td class="text-center font-weight-bold bg-light h5 mb-0 align-middle text-success">
                                                        <?= number_format($r['nilai_akhir'], 2) ?>
                                                    </td>
                                                    
                                                    <td>
                                                        <small class="text-muted" style="line-height: 1.2; display:block;">
                                                            <?= htmlspecialchars($r['deskripsi_rapor'] ?: 'Belum ada data deskripsi dari nilai Formatif/Sumatif LM yang diinputkan.') ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Silakan pilih Mata Pelajaran untuk melihat rekapitulasi nilai spesifik mapel tersebut.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab 2: Rekap Kolektif (Wali Kelas) -->
                <?php if ($is_wali): ?>
                <div class="tab-pane fade <?= (!$id_guru_mapel_filter) ? 'show active' : '' ?>" id="kolektif-content" role="tabpanel">
                    <style>
                        .table-ledger {
                            font-size: 0.85rem;
                        }
                        .vertical-header {
                            height: 150px;
                            vertical-align: bottom !important;
                            padding-bottom: 15px !important;
                            text-align: center;
                            min-width: 35px;
                        }
                        .vertical-header span {
                            writing-mode: vertical-rl;
                            transform: rotate(180deg);
                            white-space: nowrap;
                            display: inline-block;
                            font-weight: bold;
                            text-align: left;
                            text-transform: uppercase;
                            color: #495057;
                        }
                        .table-ledger td {
                            vertical-align: middle !important;
                        }
                    </style>
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-table"></i> Ledger Nilai Akhir & Rekapitulasi Rapor</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm table-ledger">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th rowspan="2" class="align-middle">No</th>
                                            <th rowspan="2" class="align-middle" style="min-width: 200px;">Nama Siswa</th>
                                            <th colspan="<?= count($collective_data['subjects']) ?>" class="align-middle bg-light">Nilai Akhir (NA) Mata Pelajaran</th>
                                            <th rowspan="2" class="align-middle bg-info text-white">Rata2</th>
                                            <th colspan="2" class="align-middle bg-warning text-dark">Sikap</th>
                                            <th colspan="3" class="align-middle bg-danger text-white">Absensi</th>
                                        </tr>
                                        <tr>
                                            <?php foreach ($collective_data['subjects'] as $sub): ?>
                                                <th class="vertical-header" title="<?= htmlspecialchars($sub['nama_mapel']) ?>">
                                                    <span><?= htmlspecialchars($sub['nama_mapel']) ?></span>
                                                </th>
                                            <?php endforeach; ?>
                                            <th title="Predikat Sikap">Pred</th>
                                            <th title="Deskripsi Sikap">Deskripsi</th>
                                            <th title="Sakit">S</th>
                                            <th title="Izin">I</th>
                                            <th title="Alpa">A</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($collective_data['siswa'] as $s): 
                                            $id_p = $s['id_penempatan'];
                                            $id_s = $s['id_siswa'];
                                            $total_na = 0;
                                            $count_na = 0;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="font-weight-bold"><?= htmlspecialchars($s['nama']) ?></td>
                                            <?php foreach ($collective_data['subjects'] as $sub): 
                                                $na = $collective_data['ledger']['na'][$id_p][$sub['id_guru_mapel']] ?? null;
                                                if ($na !== null) {
                                                    $total_na += $na;
                                                    $count_na++;
                                                }
                                            ?>
                                                <td class="text-center <?= $na < 75 && $na !== null ? 'text-danger font-weight-bold' : '' ?>">
                                                    <?= $na !== null ? number_format($na, 0) : '-' ?>
                                                </td>
                                            <?php endforeach; ?>
                                            
                                            <td class="text-center font-weight-bold text-primary">
                                                <?= $count_na > 0 ? number_format($total_na / $count_na, 2) : '-' ?>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge badge-<?= ($collective_data['sikap'][$id_p]['predikat'] ?? '') == 'A' ? 'success' : 'primary' ?>">
                                                    <?= $collective_data['sikap'][$id_p]['predikat'] ?? '-' ?>
                                                </span>
                                            </td>
                                            <td class="small" style="max-width: 250px;">
                                                <div style="max-height: 50px; overflow-y: auto; line-height: 1.1;">
                                                    <?= htmlspecialchars($collective_data['sikap'][$id_p]['deskripsi_sikap'] ?? '-') ?>
                                                </div>
                                            </td>

                                            <td class="text-center"><?= $collective_data['absensi'][$id_s]['sakit'] ?? 0 ?></td>
                                            <td class="text-center"><?= $collective_data['absensi'][$id_s]['izin'] ?? 0 ?></td>
                                            <td class="text-center"><?= $collective_data['absensi'][$id_s]['alpa'] ?? 0 ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                * Nilai mapel berwarna merah berarti di bawah KKM (75). <br>
                                * Nilai sikap diambil dari agenda penilaian sikap terakhir.
                            </small>
                        </div>
                    </div> <!-- close card Tab 2 -->
                </div> <!-- close tab-pane Tab 2 -->
                <?php endif; ?>

                <!-- Tab 3: Rekap Deskripsi Kolektif (Slider Mode) -->
                <?php if ($is_wali): ?>
                <div class="tab-pane fade" id="deskripsi-content" role="tabpanel">
                    <div class="alert alert-info d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <i class="fas fa-info-circle"></i> Mode <b>Fokus Siswa</b>: Memudahkan pengecekan deskripsi rapor satu per satu.
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="mr-2">Pilih Siswa:</span>
                            <select id="studentSelector" class="form-control form-control-sm" style="width: 200px;" onchange="showStudent(this.value)">
                                <?php foreach ($collective_data['siswa'] as $idx => $s): ?>
                                    <option value="<?= $idx ?>"><?= ($idx+1) ?>. <?= htmlspecialchars($s['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div id="studentContainer">
                        <?php foreach ($collective_data['siswa'] as $idx => $s): $id_p = $s['id_penempatan']; ?>
                        <div class="student-card" id="student-card-<?= $idx ?>" style="display: <?= $idx === 0 ? 'block' : 'none' ?>;">
                            <div class="card card-outline card-primary shadow">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-user-graduate mr-2"></i> 
                                        <?= htmlspecialchars($s['nama']) ?> 
                                        <span class="badge badge-light ml-2"><?= htmlspecialchars($s['nisn']) ?></span>
                                    </h3>
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-light mr-1" onclick="prevStudent()" <?= $idx === 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-chevron-left"></i> Sebelumnya
                                        </button>
                                        <span class="badge badge-dark p-2"><?= ($idx + 1) ?> / <?= count($collective_data['siswa']) ?></span>
                                        <button class="btn btn-sm btn-light ml-1" onclick="nextStudent()" <?= $idx === count($collective_data['siswa']) - 1 ? 'disabled' : '' ?>>
                                            Berikutnya <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="width: 250px;">Mata Pelajaran</th>
                                                <th style="width: 80px;" class="text-center">Nilai</th>
                                                <th>Deskripsi Rapor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($collective_data['subjects'] as $sub): 
                                                $id_gm = $sub['id_guru_mapel'];
                                                $na = $collective_data['ledger']['na'][$id_p][$id_gm] ?? null;
                                                $desc = $collective_data['ledger']['desc'][$id_p][$id_gm] ?? null;
                                            ?>
                                            <tr>
                                                <td class="font-weight-bold"><?= htmlspecialchars($sub['nama_mapel']) ?></td>
                                                <td class="text-center">
                                                    <span class="h5 mb-0 font-weight-bold <?= $na < 75 && $na !== null ? 'text-danger' : 'text-success' ?>">
                                                        <?= $na !== null ? number_format($na, 0) : '-' ?>
                                                    </span>
                                                </td>
                                                <td style="line-height: 1.4;">
                                                    <?= htmlspecialchars($desc ?: 'Belum ada deskripsi yang diinputkan oleh guru mapel.') ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr class="bg-warning">
                                                <td class="font-weight-bold text-dark"><i class="fas fa-star mr-1"></i> Sikap & Karakter (P3)</td>
                                                <td class="text-center font-weight-bold text-dark h5 mb-0"><?= $collective_data['sikap'][$id_p]['predikat'] ?? '-' ?></td>
                                                <td class="text-dark font-italic">
                                                    <?= htmlspecialchars($collective_data['sikap'][$id_p]['deskripsi_sikap'] ?? '-') ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer bg-light d-flex justify-content-between">
                                    <button class="btn btn-outline-secondary" onclick="prevStudent()" <?= $idx === 0 ? 'disabled' : '' ?>>
                                        <i class="fas fa-arrow-left"></i> Siswa Sebelumnya
                                    </button>
                                    <button class="btn btn-primary px-4" onclick="nextStudent()" <?= $idx === count($collective_data['siswa']) - 1 ? 'disabled' : '' ?>>
                                        Siswa Berikutnya <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div> <!-- close tab-pane Tab 3 -->
                <?php endif; ?>
            </div> <!-- close tab-content -->

        <?php endif; ?>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.bobot-input');
    const totalEl = document.getElementById('total_bobot');
    const warningEl = document.getElementById('bobot_warning');
    const btnSimpan = document.getElementById('btn_simpan_bobot');

    function calculateTotal() {
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value || 0);
        });
        // Handle floating point precision issues
        total = Math.round(total * 100) / 100;
        totalEl.textContent = total;

        if (total !== 100) {
            totalEl.classList.add('text-danger');
            warningEl.style.display = 'inline';
            btnSimpan.disabled = true;
        } else {
            totalEl.classList.remove('text-danger');
            warningEl.style.display = 'none';
            btnSimpan.disabled = false;
        }
    }

    inputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Initial calculation
    calculateTotal();
});

// Student Slider Logic
let currentStudentIndex = 0;
const totalStudents = <?= count($collective_data['siswa'] ?? []) ?>;

function showStudent(index) {
    index = parseInt(index);
    if (index < 0 || index >= totalStudents) return;
    
    // Hide all
    document.querySelectorAll('.student-card').forEach(card => {
        card.style.display = 'none';
    });
    
    // Show selected
    const selectedCard = document.getElementById('student-card-' + index);
    if (selectedCard) {
        selectedCard.style.display = 'block';
        currentStudentIndex = index;
        
        // Sync selector
        const selector = document.getElementById('studentSelector');
        if (selector) selector.value = index;

        // Scroll to top of card if needed
        selectedCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function nextStudent() {
    if (currentStudentIndex < totalStudents - 1) {
        showStudent(currentStudentIndex + 1);
    }
}

function prevStudent() {
    if (currentStudentIndex > 0) {
        showStudent(currentStudentIndex - 1);
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
