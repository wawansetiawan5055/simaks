<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
require_once __DIR__ . '/../models/AbsensiPiketModel.php';
include __DIR__ . '/partials/header.php';
?>

<style>
    /* Styling agar tinggi card seimbang, adaptif terhadap banner, dan memiliki celah aman ke footer */
    @media (min-width: 768px) {
        .absensi-main-row {
            min-height: calc(100vh - 285px);
            align-items: stretch;
        }
        .absensi-card-left, .absensi-card-right {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .absensi-card-left .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .absensi-card-right .card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }
        .table-responsive-absensi {
            flex: 1;
            overflow-y: auto;
            max-height: calc(100vh - 410px);
            min-height: 280px;
        }
    }

    /* Jarak bawah yang terjamin ke footer */
    .absensi-wrapper-section {
        padding-bottom: 2.5rem !important;
        margin-bottom: 1.5rem !important;
    }

    /* Header tabel bersih & netral */
    .table-absensi thead th {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
        border-bottom: 2px solid #cbd5e1 !important;
        font-weight: 700 !important;
        font-size: 0.82rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    /* Tombol presensi */
    .btn-absensi.active {
        color: #fff !important;
    }
    .btn-outline-success.active { background-color: #16a34a !important; border-color: #16a34a !important; }
    .btn-outline-warning.active { background-color: #eab308 !important; border-color: #eab308 !important; color: #1e293b !important; }
    .btn-outline-info.active    { background-color: #0ea5e9 !important; border-color: #0ea5e9 !important; }
    .btn-outline-danger.active  { background-color: #ef4444 !important; border-color: #ef4444 !important; }

    tr.row-focused {
        background-color: rgba(59, 130, 246, 0.08) !important;
        outline: 2px solid #3b82f6;
    }

    /* Responsive label absensi: singkat di HP (H/S/I/A), panjang di desktop */
    .absen-label-short { display: none; }
    .absen-label-long  { display: inline; }

    @media (max-width: 768px) {
        .absen-label-long  { display: none; }
        .absen-label-short { display: inline; }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Presensi Siswa Harian (Piket)
                        <?php if (!empty($has_existing_data)): ?>
                            <span class="badge badge-warning ml-2" style="font-size:0.68rem; vertical-align:middle;">MODE EDIT</span>
                        <?php elseif (!empty($is_past_date)): ?>
                            <span class="badge badge-secondary ml-2" style="font-size:0.68rem; vertical-align:middle;">TANGGAL LALU</span>
                        <?php endif; ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>absensi_scan" class="btn btn-sm btn-success font-weight-bold rounded-pill px-3 shadow-sm">
                    <i class="fas fa-qrcode mr-1"></i> Scan QR / Barcode
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content absensi-wrapper-section">
    <div class="container-fluid">

        <!-- Pesan Notifikasi Sukses / Error -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible shadow-sm d-flex flex-wrap justify-content-between align-items-center py-2 px-3 mb-2" style="border-radius: 8px;">
                <div class="font-weight-bold my-1" style="font-size: 0.92rem;">
                    <i class="fas fa-check-circle mr-1 text-white"></i> <?= $_SESSION['pesan_sukses'] ?>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:white; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible shadow-sm py-2 px-3 mb-2" style="border-radius: 8px;">
                <div class="font-weight-bold my-1" style="font-size: 0.92rem;">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <?= $_SESSION['pesan_error'] ?>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <?php if (!empty($has_existing_data)): ?>
            <div class="alert alert-warning py-2 px-3 mb-2 bg-white border border-warning shadow-sm" style="border-radius: 8px; font-size: 0.85rem;">
                <i class="fas fa-info-circle text-warning mr-1"></i>
                Data absensi piket kelas <strong><?= htmlspecialchars($kelas['nama_kelas'] ?? '') ?></strong> pada tanggal <strong><?= DateHelper::formatTanggal($tanggal, 'long') ?></strong> sudah pernah disimpan. Menyimpan ulang akan <strong>memperbarui</strong> data presensi.
            </div>
        <?php endif; ?>

        <!-- FORM UTAMA TATA LETAK KIRI - KANAN BERSIH -->
        <form action="<?= BASE_URL ?>absensi_piket/save" method="POST" id="form-absensi-piket">
            <input type="hidden" name="id_kelas" id="id_kelas_hidden" value="<?= $id_kelas ?>">
            <input type="hidden" name="tanggal" id="tanggal_hidden" value="<?= $tanggal ?>">

            <div class="row absensi-main-row mb-3">
                <!-- ==================== KOLOM KIRI (PILIHAN KELAS & TANGGAL) ==================== -->
                <div class="col-lg-4 col-md-5 mb-3 mb-md-0 d-flex flex-column">
                    <div class="card shadow-sm border-0 absensi-card-left" style="border-radius: 10px; overflow: hidden; border-top: 3px solid #3b82f6;">
                        <div class="card-header bg-white py-3 px-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center mr-2 bg-light rounded" style="width:34px; height:34px; flex-shrink:0; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-sliders-h text-dark" style="font-size: 0.9rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">Pengaturan Presensi</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Pilih kelas dan tanggal piket</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div>
                                <!-- Pilihan Kelas -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-uppercase mb-2 text-dark">
                                        <i class="fas fa-chalkboard mr-1"></i> Kelas
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;">
                                                <i class="fas fa-chalkboard text-muted"></i>
                                            </span>
                                        </div>
                                        <select id="select_kelas" class="form-control border-left-0" style="border-radius: 0 8px 8px 0;" onchange="gantiFilterKelasTanggal()">
                                            <option value="">-- Pilih Kelas --</option>
                                            <?php foreach ($kelas_list as $k): ?>
                                                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas == $k['id_kelas']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Pilihan Tanggal -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-uppercase mb-2 text-dark">
                                        <i class="fas fa-calendar-day mr-1"></i> Tanggal
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0" style="border-radius: 8px 0 0 8px;">
                                                <i class="fas fa-calendar-alt text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="date" id="input_tanggal" class="form-control border-left-0" style="border-radius: 0 8px 8px 0;" value="<?= htmlspecialchars($tanggal) ?>" onchange="gantiFilterKelasTanggal()">
                                    </div>
                                </div>

                                <!-- Status Pengisian Kelas Terpilih -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-uppercase mb-2 text-dark">
                                        <i class="fas fa-info-circle mr-1"></i> Status Pengisian
                                    </label>
                                    <div class="p-2 rounded border <?= $status_sudah ? 'bg-light-success border-success' : 'bg-light border-secondary' ?>" style="<?= $status_sudah ? 'background-color: #f0fdf4;' : '' ?>">
                                        <div class="d-flex align-items-center">
                                            <?php if ($status_sudah): ?>
                                                <i class="fas fa-check-circle text-success mr-2" style="font-size: 1.1rem;"></i>
                                                <div>
                                                    <div class="font-weight-bold text-success" style="font-size: 0.85rem;">Sudah Pernah Disimpan</div>
                                                    <small class="text-muted" style="font-size: 0.72rem;">Data presensi sudah tersimpan di database</small>
                                                </div>
                                            <?php else: ?>
                                                <i class="fas fa-clock text-secondary mr-2" style="font-size: 1.1rem;"></i>
                                                <div>
                                                    <div class="font-weight-bold text-secondary" style="font-size: 0.85rem;">Belum Diisi / Belum Disimpan</div>
                                                    <small class="text-muted" style="font-size: 0.72rem;">Silakan isi presensi lalu klik Simpan</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Bawah: Ringkasan Kehadiran Saja (Shortcut pindah ke footer kanan) -->
                            <?php if ($id_kelas && !empty($siswa_list)): ?>
                                <div class="mt-auto pt-3 border-top">
                                    <label class="font-weight-bold small text-uppercase mb-2 text-dark d-block">
                                        <i class="fas fa-chart-pie mr-1"></i> Ringkasan Kehadiran
                                    </label>
                                    <div class="row no-gutters text-center mb-0" style="gap: 5px;">
                                        <div class="col p-2 rounded bg-light border">
                                            <div class="font-weight-bold text-success" id="count-hadir" style="font-size: 1.15rem;">0</div>
                                            <small class="text-muted" style="font-size: 0.72rem;">Hadir</small>
                                        </div>
                                        <div class="col p-2 rounded bg-light border">
                                            <div class="font-weight-bold text-warning" id="count-sakit" style="font-size: 1.15rem;">0</div>
                                            <small class="text-muted" style="font-size: 0.72rem;">Sakit</small>
                                        </div>
                                        <div class="col p-2 rounded bg-light border">
                                            <div class="font-weight-bold text-info" id="count-izin" style="font-size: 1.15rem;">0</div>
                                            <small class="text-muted" style="font-size: 0.72rem;">Izin</small>
                                        </div>
                                        <div class="col p-2 rounded bg-light border">
                                            <div class="font-weight-bold text-danger" id="count-alpa" style="font-size: 1.15rem;">0</div>
                                            <small class="text-muted" style="font-size: 0.72rem;">Alpa</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ==================== KOLOM KANAN (DAFTAR PRESENSI SISWA) ==================== -->
                <div class="col-lg-8 col-md-7 d-flex flex-column">
                    <?php if ($id_kelas && !empty($siswa_list)): ?>
                        <div class="card shadow-sm border-0 absensi-card-right" style="border-radius: 10px; overflow: hidden; border-top: 3px solid #10b981;">
                            <div class="card-header bg-white py-3 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center mr-2 bg-light rounded" style="width:34px; height:34px; flex-shrink:0; border: 1px solid #e2e8f0;">
                                        <i class="fas fa-user-check text-dark" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">
                                            <?= htmlspecialchars($kelas['nama_kelas'] ?? '') ?> 
                                            <span class="badge badge-secondary ml-1 font-weight-normal"><?= count($siswa_list) ?> Siswa</span>
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <?= DateHelper::formatTanggal($tanggal, 'long') ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-success font-weight-bold shadow-sm" style="border-radius: 6px;" onclick="setSemuaHadir()">
                                        <i class="fas fa-check-double mr-1"></i> Set Semua Hadir
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-0 d-flex flex-column flex-fill">
                                <div class="table-responsive table-responsive-absensi">
                                    <table class="table table-hover table-absensi mb-0" id="tabel-absensi">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 45px;">No</th>
                                                <th>Nama Siswa</th>
                                                <th class="text-center" style="width: 250px;">Kehadiran</th>
                                                <th style="width: 200px;">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $row_idx = 0;
                                            foreach ($siswa_list as $s): 
                                                $id_s = $s['id_siswa'];
                                                $existing = $absensi_existing[$id_s] ?? null;
                                                $status_saved = $existing['status'] ?? 'Hadir';
                                                $ket_saved = $existing['keterangan'] ?? '';
                                                $row_bg = ($status_saved !== 'Hadir' && $existing) ? 'table-warning' : '';
                                            ?>
                                                <tr data-row="<?= $row_idx ?>" class="<?= $row_bg ?>">
                                                    <td class="text-center font-weight-bold text-muted" style="vertical-align: middle;">
                                                        <?= $row_idx + 1 ?>
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <div class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                            <?= htmlspecialchars($s['nama']) ?>
                                                        </div>
                                                        <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                                            NISN: <?= htmlspecialchars($s['nisn'] ?: '-') ?>
                                                        </small>
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <div class="btn-group btn-group-toggle w-100 absensi-btn-group shadow-sm" data-toggle="buttons" style="border-radius: 6px; overflow: hidden;">
                                                            <label class="btn btn-outline-success btn-xs btn-absensi font-weight-bold <?= ($status_saved === 'Hadir') ? 'active' : '' ?>" style="flex: 1; padding: 5px 2px;">
                                                                <input type="radio" name="absensi[<?= $id_s ?>][status]" value="Hadir" <?= ($status_saved === 'Hadir') ? 'checked' : '' ?> onchange="updateLiveCounter()"><span class="absen-label-long"> Hadir</span><span class="absen-label-short"> H</span>
                                                            </label>
                                                            <label class="btn btn-outline-warning btn-xs btn-absensi font-weight-bold <?= ($status_saved === 'Sakit') ? 'active' : '' ?>" style="flex: 1; padding: 5px 2px;">
                                                                <input type="radio" name="absensi[<?= $id_s ?>][status]" value="Sakit" <?= ($status_saved === 'Sakit') ? 'checked' : '' ?> onchange="updateLiveCounter()"><span class="absen-label-long"> Sakit</span><span class="absen-label-short"> S</span>
                                                            </label>
                                                            <label class="btn btn-outline-info btn-xs btn-absensi font-weight-bold <?= ($status_saved === 'Izin') ? 'active' : '' ?>" style="flex: 1; padding: 5px 2px;">
                                                                <input type="radio" name="absensi[<?= $id_s ?>][status]" value="Izin" <?= ($status_saved === 'Izin') ? 'checked' : '' ?> onchange="updateLiveCounter()"><span class="absen-label-long"> Izin</span><span class="absen-label-short"> I</span>
                                                            </label>
                                                            <label class="btn btn-outline-danger btn-xs btn-absensi font-weight-bold <?= ($status_saved === 'Alpa') ? 'active' : '' ?>" style="flex: 1; padding: 5px 2px;">
                                                                <input type="radio" name="absensi[<?= $id_s ?>][status]" value="Alpa" <?= ($status_saved === 'Alpa') ? 'checked' : '' ?> onchange="updateLiveCounter()"><span class="absen-label-long"> Alpa</span><span class="absen-label-short"> A</span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        <input type="text" name="absensi[<?= $id_s ?>][keterangan]" 
                                                               class="form-control form-control-sm" 
                                                               value="<?= htmlspecialchars($ket_saved) ?>" 
                                                               placeholder="Opsional..." 
                                                               style="border-radius: 6px; font-size: 0.8rem;">
                                                    </td>
                                                </tr>
                                                <?php $row_idx++; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Footer Card Kanan: Shortcut di kiri & Tombol Simpan di kanan persis seperti absensi mapel -->
                            <div class="card-footer bg-white border-top py-3 px-3 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-keyboard mr-1"></i> Shortcut: <strong>H, S, I, A</strong> & panah atas/bawah.
                                    </small>
                                </div>
                                <div>
                                    <button type="submit" class="btn font-weight-bold px-4 py-2 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; font-size: 0.9rem;">
                                        <i class="fas fa-<?= !empty($has_existing_data) ? 'sync-alt' : 'save' ?> mr-1"></i>
                                        <?= !empty($has_existing_data) ? 'Perbarui Absensi' : 'Simpan Absensi' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($id_kelas): ?>
                        <div class="card shadow-sm border-0 absensi-card-right" style="border-radius: 10px; overflow: hidden;">
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-users-slash fa-3x mb-3 text-muted"></i>
                                <h6 class="font-weight-bold mb-1">Belum ada siswa aktif di kelas ini.</h6>
                                <small class="text-muted">Silakan periksa penempatan siswa pada Data Master.</small>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card shadow-sm border-0 absensi-card-right" style="border-radius: 10px; overflow: hidden;">
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-arrow-left fa-3x mb-3 text-primary"></i>
                                <h6 class="font-weight-bold mb-1">Silakan pilih kelas terlebih dahulu.</h6>
                                <small class="text-muted">Pilih kelas pada panel sebelah kiri untuk memuat daftar presensi siswa.</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </form>
    </div>
</section>

<script>
    function gantiFilterKelasTanggal() {
        const idKelas = document.getElementById('select_kelas').value;
        const tanggal = document.getElementById('input_tanggal').value;
        if (idKelas) {
            window.location.href = `index.php?mod=absensi_piket&id_kelas=${idKelas}&tanggal=${tanggal}`;
        }
    }

    function updateLiveCounter() {
        const counts = { Hadir: 0, Sakit: 0, Izin: 0, Alpa: 0 };
        document.querySelectorAll('#tabel-absensi tbody tr').forEach(tr => {
            const checkedRadio = tr.querySelector('input[type="radio"]:checked');
            if (checkedRadio) {
                const val = checkedRadio.value;
                if (counts[val] !== undefined) counts[val]++;
            }
        });
        const elHadir = document.getElementById('count-hadir');
        const elSakit = document.getElementById('count-sakit');
        const elIzin = document.getElementById('count-izin');
        const elAlpa = document.getElementById('count-alpa');
        if (elHadir) elHadir.textContent = counts.Hadir;
        if (elSakit) elSakit.textContent = counts.Sakit;
        if (elIzin) elIzin.textContent = counts.Izin;
        if (elAlpa) elAlpa.textContent = counts.Alpa;
    }

    function setSemuaHadir() {
        document.querySelectorAll('#tabel-absensi tbody tr').forEach(tr => {
            const radioHadir = tr.querySelector('input[type="radio"][value="Hadir"]');
            if (radioHadir) {
                radioHadir.checked = true;
                const labels = tr.querySelectorAll('.btn-absensi');
                labels.forEach(l => l.classList.remove('active'));
                const labelHadir = tr.querySelector('.btn-outline-success');
                if (labelHadir) labelHadir.classList.add('active');
            }
            tr.classList.remove('table-warning');
        });
        updateLiveCounter();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateLiveCounter();

        // KEYBOARD SHORTCUT NAVIGATION
        let focusedRow = 0;
        const rows = document.querySelectorAll('#tabel-absensi tbody tr');

        function focusRow(idx) {
            rows.forEach(tr => tr.classList.remove('row-focused'));
            if (rows[idx]) {
                rows[idx].classList.add('row-focused');
                rows[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                focusedRow = idx;
            }
        }

        if (rows.length > 0) {
            focusRow(0);

            document.addEventListener('keydown', function(e) {
                const activeTag = document.activeElement ? document.activeElement.tagName : '';
                // Jika sedang mengetik di input text keterangan, jangan cegah shortcut ketik
                if (activeTag === 'INPUT' && document.activeElement.type === 'text') {
                    return;
                }
                if (activeTag === 'SELECT' || activeTag === 'TEXTAREA') {
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (focusedRow < rows.length - 1) focusRow(focusedRow + 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (focusedRow > 0) focusRow(focusedRow - 1);
                } else {
                    const key = e.key.toUpperCase();
                    const keyMap = { 'H': 'Hadir', 'S': 'Sakit', 'I': 'Izin', 'A': 'Alpa' };
                    if (keyMap[key]) {
                        e.preventDefault();
                        const targetRow = rows[focusedRow];
                        if (targetRow) {
                            const val = keyMap[key];
                            const radio = targetRow.querySelector(`input[type="radio"][value="${val}"]`);
                            if (radio) {
                                radio.checked = true;
                                const labels = targetRow.querySelectorAll('.btn-absensi');
                                labels.forEach(l => l.classList.remove('active'));
                                const activeLabel = radio.closest('label');
                                if (activeLabel) activeLabel.classList.add('active');

                                if (val !== 'Hadir') {
                                    targetRow.classList.add('table-warning');
                                } else {
                                    targetRow.classList.remove('table-warning');
                                }
                                updateLiveCounter();
                            }
                            if (focusedRow < rows.length - 1) {
                                focusRow(focusedRow + 1);
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
