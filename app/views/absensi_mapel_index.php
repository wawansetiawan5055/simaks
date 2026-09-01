<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
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

    /* Responsive label absensi: singkat di HP, panjang di desktop */
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
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Presensi Siswa Mata Pelajaran
                        <?php if (!empty($has_existing_data)): ?>
                            <span class="badge badge-warning ml-2" style="font-size:0.68rem; vertical-align:middle;">MODE EDIT</span>
                        <?php elseif (!empty($is_past_date)): ?>
                            <span class="badge badge-secondary ml-2" style="font-size:0.68rem; vertical-align:middle;">TANGGAL LALU</span>
                        <?php endif; ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Presensi Mapel</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content absensi-wrapper-section">
    <div class="container-fluid">

        <!-- Pesan Sukses & Tombol Lanjut ke Jurnal KBM (Ramping & Proporsional) -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible shadow-sm d-flex flex-wrap justify-content-between align-items-center py-2 px-3 mb-2" style="border-radius: 8px;">
                <div class="font-weight-bold my-1" style="font-size: 0.92rem;">
                    <i class="fas fa-check-circle mr-1 text-white"></i> <?= $_SESSION['pesan_sukses'] ?>
                </div>
                <div class="my-1">
                    <a href="<?= BASE_URL ?>jurnal_kbm?id_kelas=<?= $id_kelas ?>&tanggal=<?= $tanggal ?>" class="btn btn-light btn-sm font-weight-bold shadow-sm py-1 px-3" style="border-radius: 6px; color: #15803d; font-size: 0.85rem;">
                        <i class="fas fa-book-reader mr-1"></i> Lanjut Isi Jurnal KBM <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php endif; ?>

        <?php if (!empty($has_existing_data)): ?>
            <div class="alert alert-warning py-1 px-3 mb-2 bg-white border border-warning shadow-sm" style="border-radius: 8px; font-size: 0.82rem;">
                <i class="fas fa-info-circle text-warning mr-1"></i>
                Data absensi kelas <strong><?= htmlspecialchars($kelas['nama_kelas'] ?? '') ?></strong> pada tanggal ini sudah ada. Menyimpan ulang akan <strong>memperbarui</strong> data presensi.
            </div>
        <?php endif; ?>

        <!-- FORM UTAMA TATA LETAK KIRI - KANAN BERSIH SAMPAI BAWAH -->
        <form action="<?= BASE_URL ?>absensi_mapel/save" method="POST" id="form-absensi-mapel">
            <input type="hidden" name="id_kelas" id="id_kelas_hidden" value="<?= $id_kelas ?>">
            <input type="hidden" name="tanggal" id="tanggal_hidden" value="<?= $tanggal ?>">

            <div class="row absensi-main-row mb-3">
                <!-- ==================== KOLOM KIRI (PILIHAN KELAS, TANGGAL & JAM MENGAJAR) ==================== -->
                <div class="col-lg-4 col-md-5 mb-3 mb-md-0 d-flex flex-column">
                    <div class="card shadow-sm border-0 absensi-card-left" style="border-radius: 10px; overflow: hidden; border-top: 3px solid #3b82f6;">
                        <div class="card-header bg-white py-3 px-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center mr-2 bg-light rounded" style="width:34px; height:34px; flex-shrink:0; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-sliders-h text-dark" style="font-size: 0.9rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">Waktu & Kelas</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Pengaturan jadwal presensi</small>
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
                                            <?php foreach ($kelas_diajar as $k): ?>
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

                                <!-- Pilihan Jam Mengajar -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-uppercase mb-2 text-dark">
                                        <i class="fas fa-clock mr-1"></i> Jam Mengajar <span class="badge badge-light border ml-1">Wajib dipilih</span>
                                    </label>
                                    <div id="jam_mengajar_container" class="p-2 rounded border bg-light" style="min-height: 50px;">
                                        <p class="text-muted small mb-0"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat jadwal...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Bawah: Ringkasan & Tombol Simpan -->
                            <?php if ($id_kelas && !empty($siswa_list)): ?>
                                <div class="mt-auto pt-3 border-top">
                                    <label class="font-weight-bold small text-uppercase mb-2 text-dark d-block">
                                        <i class="fas fa-chart-pie mr-1"></i> Ringkasan Kehadiran
                                    </label>
                                    <div class="row no-gutters text-center mb-3" style="gap: 5px;">
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
                                        <small class="text-muted" style="font-size: 0.75rem;">Pilih status kehadiran setiap siswa</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                    <?php if (!empty($materi_lms_list)): ?>
                                    <button type="button" class="btn btn-sm btn-primary font-weight-bold shadow-sm rounded-pill px-3" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;" data-toggle="modal" data-target="#modalSyncLms">
                                        <i class="fas fa-bolt mr-1 text-warning"></i> ⚡ Tarik Presensi LMS
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-success font-weight-bold shadow-sm" style="border-radius: 6px;" onclick="setSemuaHadir()">
                                        <i class="fas fa-check-double mr-1"></i> Set Semua Hadir
                                    </button>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive table-responsive-absensi">
                                    <table class="table table-hover table-striped table-absensi mb-0 align-middle">
                                        <thead class="position-sticky" style="top: 0; z-index: 2;">
                                            <tr>
                                                <th class="text-center" style="width: 45px;">No</th>
                                                <th>Nama Siswa</th>
                                                <th class="text-center" style="width: 280px;">Kehadiran</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabel-absensi">
                                            <?php $no = 1; $row_idx = 0; ?>
                                            <?php foreach ($siswa_list as $s): ?>
                                                <?php 
                                                    $id_s = $s['id_siswa'];
                                                    $existing = $absensi_existing[$id_s] ?? null;
                                                    $status_saved = $existing['status'] ?? 'Hadir';
                                                    $row_bg = ($status_saved !== 'Hadir' && $existing) ? 'table-warning' : '';
                                                ?>
                                                <tr data-row="<?= $row_idx ?>" class="<?= $row_bg ?>">
                                                    <td class="text-center font-weight-bold text-muted align-middle py-2"><?= $no++ ?></td>
                                                    <td class="align-middle py-2" id="siswa_col_<?= $id_s ?>">
                                                        <span class="font-weight-bold text-dark d-block" style="font-size: 0.9rem;"><?= htmlspecialchars($s['nama']) ?></span>
                                                        <small class="text-muted"><i class="fas fa-id-card-alt mr-1"></i>NISN: <?= htmlspecialchars($s['nisn']) ?></small>
                                                        <div id="lms_badge_container_<?= $id_s ?>"></div>
                                                    </td>
                                                    <td class="text-center align-middle py-2">
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
                                                </tr>
                                                <?php $row_idx++; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-top py-3 px-3 d-flex justify-content-between align-items-center flex-wrap">
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
            window.location.href = `index.php?mod=absensi_mapel&id_kelas=${idKelas}&tanggal=${tanggal}`;
        }
    }

    function updateLiveCounter() {
        const counts = { Hadir: 0, Sakit: 0, Izin: 0, Alpa: 0 };
        document.querySelectorAll('#tabel-absensi tr').forEach(tr => {
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
        document.querySelectorAll('#tabel-absensi tr').forEach(tr => {
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
        const idKelas = "<?= $id_kelas ?>";
        const tanggal = "<?= $tanggal ?>";
        const jamContainer = document.getElementById('jam_mengajar_container');

        updateLiveCounter();

        if (idKelas && tanggal) {
            fetch(`api.php?mod=jadwal&act=get_by_kelas_dan_tanggal&id_kelas=${idKelas}&tanggal=${tanggal}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'ok' && result.data.length > 0) {
                        jamContainer.innerHTML = '';
                        result.data.forEach((item, index) => {
                            const jamMulai = item.jam_mulai.substring(0, 5);
                            const jamSelesai = item.jam_selesai.substring(0, 5);
                            const optionText = `${jamMulai} - ${jamSelesai} | ${item.nama_mapel}`;

                            const checkboxDiv = document.createElement('div');
                            checkboxDiv.className = 'custom-control custom-checkbox my-1';
                            const isChecked = (result.data.length === 1 || index === 0) ? 'checked' : '';
                            checkboxDiv.innerHTML = `
                                <input class="custom-control-input" type="checkbox" name="jam_mengajar[]" value="${item.id_jadwal_mengajar}" id="jam_${item.id_jadwal_mengajar}" ${isChecked} required>
                                <label class="custom-control-label small font-weight-bold text-dark" for="jam_${item.id_jadwal_mengajar}">${optionText}</label>
                            `;
                            jamContainer.appendChild(checkboxDiv);
                        });

                        const checkboxes = jamContainer.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(cb => {
                            cb.addEventListener('change', function() {
                                const anyChecked = Array.from(checkboxes).some(c => c.checked);
                                checkboxes.forEach(c => c.required = !anyChecked);
                            });
                        });
                        const anyCheckedInit = Array.from(checkboxes).some(c => c.checked);
                        checkboxes.forEach(c => c.required = !anyCheckedInit);

                    } else {
                        jamContainer.innerHTML = '<p class="text-danger small mb-0 font-italic"><i class="fas fa-exclamation-circle mr-1"></i> Tidak ada jadwal di hari ini</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching schedule:', error);
                    jamContainer.innerHTML = '<p class="text-danger small mb-0 font-italic"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat jadwal</p>';
                });
        } else {
            jamContainer.innerHTML = '<p class="text-muted small mb-0">Pilih kelas dahulu</p>';
        }

        // KEYBOARD SHORTCUT NAVIGATION
        let focusedRow = 0;
        const rows = document.querySelectorAll('#tabel-absensi tr');

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
                if (activeTag === 'INPUT' || activeTag === 'SELECT' || activeTag === 'TEXTAREA') {
                    if (document.activeElement.type !== 'radio') return;
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
    // ============================================================
    // ⚡ EKSEKUSI SINKRONISASI PRESENSI LMS
    // ============================================================
    window.eksekusiSyncLms = function() {
        const idMateri = $('#select_materi_lms').val();
        const idKelas = $('#id_kelas_hidden').val();
        const tanggal = $('#tanggal_hidden').val();

        if (!idMateri) {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Pilih Modul Materi', 
                text: 'Silakan pilih modul pembelajaran LMS yang diajarkan terlebih dahulu.', 
                confirmButtonColor: '#4f46e5' 
            });
            return;
        }

        const btn = $('#btnEksekusiSyncLms');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menganalisis Data LMS...');

        $.getJSON(`<?= BASE_URL ?>index.php?mod=absensi_mapel&act=sync_lms&id_materi=${idMateri}&id_kelas=${idKelas}&tanggal=${tanggal}`, function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Terapkan Presensi LMS');
            $('#modalSyncLms').modal('hide');

            if (res.status === 'ok' && res.data) {
                Object.keys(res.data).forEach(id_siswa => {
                    const item = res.data[id_siswa];
                    const status = item.status;
                    
                    // Update input radio status
                    const $radio = $(`input[name="absensi[${id_siswa}][status]"][value="${status}"]`);
                    if ($radio.length) {
                        $radio.prop('checked', true);
                        const $btnGroup = $radio.closest('.absensi-btn-group');
                        $btnGroup.find('label').removeClass('active');
                        $radio.closest('label').addClass('active');

                        // Update warna baris
                        const $tr = $radio.closest('tr');
                        $tr.removeClass('table-warning table-danger table-success');
                        if (status === 'Alpa') {
                            $tr.addClass('table-danger');
                        } else if (status === 'Sakit' || status === 'Izin') {
                            $tr.addClass('table-warning');
                        }
                    }

                    // Render status badge di bawah nama siswa
                    $(`#lms_badge_container_${id_siswa}`).html(`
                        <div class="mt-1">
                            <span class="badge badge-${item.badge_type} px-2 py-1 shadow-sm" style="font-size: 0.72rem; border-radius: 4px;">
                                <i class="fas fa-bolt mr-1"></i>${item.label}
                            </span>
                        </div>
                    `);
                });

                updateLiveCounter();

                Swal.fire({
                    icon: 'success',
                    title: 'Presensi LMS Berhasil Ditarik!',
                    html: `
                        <div class="text-left p-2">
                            <p class="mb-2">Aktivitas pembelajaran digital berhasil disinkronkan:</p>
                            <ul class="mb-0 small">
                                <li class="text-success font-weight-bold">${res.summary.hadir} Siswa Hadir (LMS)</li>
                                <li class="text-danger font-weight-bold">${res.summary.alpa} Siswa Alpa (Tidak Akses)</li>
                                <li class="text-warning font-weight-bold">${res.summary.sakit + res.summary.izin} Siswa Izin / Sakit (Piket)</li>
                            </ul>
                            <p class="mt-2 mb-0 small text-muted">Silakan periksa kembali dan klik <strong>Simpan Absensi</strong>.</p>
                        </div>
                    `,
                    confirmButtonColor: '#4f46e5'
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal memproses data LMS.' });
            }
        }).fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Terapkan Presensi LMS');
            Swal.fire({ icon: 'error', title: 'Koneksi Terputus', text: 'Terjadi kesalahan saat memproses data ke server.' });
        });
    };
});
</script>

<!-- MODAL SINKRONISASI PRESENSI LMS -->
<div class="modal fade" id="modalSyncLms" tabindex="-1" role="dialog" aria-labelledby="modalSyncLmsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #4f46e5, #3730a3) !important;">
                <h5 class="modal-title font-weight-bold" id="modalSyncLmsLabel" style="font-size: 1.05rem;">
                    <i class="fas fa-bolt text-warning mr-2"></i> Tarik Presensi dari Modul LMS
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border small text-muted mb-3" style="border-radius: 10px;">
                    <i class="fas fa-info-circle text-primary mr-1"></i> <strong>Sistem Otomatis Cerdas:</strong> Siswa yang sudah <strong>Check-in</strong> atau <strong>Menyelesaikan materi</strong> di LMS otomatis ditandai <strong>Hadir</strong>. Siswa yang sama sekali tidak membuka modul otomatis ditandai <strong>Alpa</strong> (kecuali memiliki surat izin piket).
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-dark text-uppercase">PILIH MODUL PEMBELAJARAN LMS</label>
                    <select id="select_materi_lms" class="form-control form-control-sm select2" style="border-radius: 8px;">
                        <option value="">-- Pilih Modul Pembelajaran --</option>
                        <?php foreach ($materi_lms_list as $mat): ?>
                            <option value="<?= $mat['id_materi'] ?>">
                                [<?= htmlspecialchars($mat['nama_mapel']) ?>] <?= htmlspecialchars($mat['judul_materi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm" id="btnEksekusiSyncLms" onclick="eksekusiSyncLms()" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                    <i class="fas fa-bolt mr-1"></i> Terapkan Presensi LMS
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
