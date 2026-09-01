<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    /* Dasar Halaman Input Nilai */
    .page-input-nilai .lms-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff;
        margin-bottom: 20px;
    }
    
    .page-input-nilai .custom-filter-select {
        height: 42px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #f8fafc !important;
        padding: 6px 14px !important;
        box-shadow: none !important;
        border-radius: 10px !important;
        transition: all 0.2s ease;
    }
    .page-input-nilai .custom-filter-select:focus {
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    .input-nilai-formatif:focus {
        background-color: #fff8e1 !important;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important;
    }
    tr.row-focused {
        background-color: rgba(79, 70, 229, 0.05) !important;
    }

    /* Student Card Item for Mobile */
    .student-score-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .student-score-card:focus-within {
        border-color: #4f46e5;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.1);
    }
    .student-avatar-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #3730a3);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.82rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Floating / Sticky Mobile Save Bar */
    .mobile-sticky-save-bar {
        display: none;
    }

    @media (max-width: 768px) {
        .page-input-nilai {
            padding: 0 !important;
        }
        .page-input-nilai .content-header h1 {
            font-size: 1.1rem !important;
        }
        .page-input-nilai .lms-card {
            border-radius: 12px !important;
            padding: 12px 14px !important;
        }
        .page-input-nilai .custom-filter-select {
            height: 38px !important;
            font-size: 0.82rem !important;
            padding: 4px 10px !important;
        }
        .mobile-action-btns {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        .mobile-action-btns .btn {
            width: 100% !important;
            justify-content: center;
            font-size: 0.8rem !important;
            padding: 8px 12px !important;
        }
        .desktop-table-container {
            display: none !important;
        }
        .mobile-cards-container {
            display: block !important;
        }
        .mobile-sticky-save-bar {
            display: block;
            position: fixed;
            bottom: 60px;
            left: 0;
            right: 0;
            z-index: 1020;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.06);
        }
    }

    @media (min-width: 769px) {
        .desktop-table-container {
            display: block !important;
        }
        .mobile-cards-container {
            display: none !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2 page-input-nilai">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Input Nilai Siswa (Formatif / TP)
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <span class="badge badge-light border text-muted px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.78rem;">
                    <i class="fas fa-calendar-check text-primary mr-1"></i> TA: <?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? '-') ?>
                </span>
            </div>
        </div>
    </div>
</div>

<section class="content page-input-nilai">
    <div class="container-fluid">

        <!-- FILTER TOOLBAR: KELAS, MAPEL, CP, TP -->
        <div class="card lms-card p-3 mb-3 shadow-sm">
            <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                <div class="bg-light p-2 rounded mr-2 text-primary font-weight-bold" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-filter"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">Pilih Kelas, Mapel, CP, dan TP</h6>
            </div>

            <form method="GET" id="filterForm">
                <input type="hidden" name="mod" value="input_nilai">
                <div class="row" style="row-gap: 12px;">
                    <!-- KELAS -->
                    <div class="form-group col-lg-3 col-md-6 col-12 mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                            <i class="fas fa-chalkboard text-primary mr-1"></i> Kelas
                        </label>
                        <select name="id_kelas" id="id_kelas" class="form-control custom-filter-select" onchange="submitFilter()">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_diajar ?? [] as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>" <?= ($id_kelas_filter == $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- MATA PELAJARAN -->
                    <div class="form-group col-lg-3 col-md-6 col-12 mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                            <i class="fas fa-book text-primary mr-1"></i> Mata Pelajaran
                        </label>
                        <select name="id_guru_mapel" id="id_guru_mapel" class="form-control custom-filter-select" onchange="submitFilter()" <?= empty($mapel_diajar) ? 'disabled' : '' ?>>
                            <option value="">-- Pilih Mapel --</option>
                            <?php foreach ($mapel_diajar ?? [] as $m): ?>
                                <option value="<?= $m['id_guru_mapel'] ?>" <?= ($id_guru_mapel_filter == $m['id_guru_mapel']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama_mapel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- CAPAIAN PEMBELAJARAN (CP) -->
                    <div class="form-group col-lg-3 col-md-6 col-12 mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                            <i class="fas fa-bullseye text-primary mr-1"></i> Capaian Pembelajaran (CP)
                        </label>
                        <select name="id_cp" id="id_cp" class="form-control custom-filter-select" onchange="submitFilter()" <?= empty($cp_list) ? 'disabled' : '' ?>>
                            <option value="">-- Pilih CP --</option>
                            <?php foreach ($cp_list ?? [] as $cp): ?>
                                <option value="<?= $cp['id_cp'] ?>" <?= ($id_cp_filter == $cp['id_cp']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(substr(strip_tags($cp['deskripsi_cp'] ?? ''), 0, 45)) ?>...
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TUJUAN PEMBELAJARAN (TP) -->
                    <div class="form-group col-lg-3 col-md-6 col-12 mb-0">
                        <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                            <i class="fas fa-check-circle text-primary mr-1"></i> Tujuan Pembelajaran (TP)
                        </label>
                        <select name="id_tp" id="id_tp" class="form-control custom-filter-select" onchange="submitFilter()" <?= empty($tp_list) ? 'disabled' : '' ?>>
                            <option value="">-- Pilih TP --</option>
                            <?php foreach ($tp_list ?? [] as $tp): ?>
                                <option value="<?= $tp['id_tp'] ?>" <?= ($id_tp_filter == $tp['id_tp']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tp['kode_tp']) ?> - <?= htmlspecialchars(substr(strip_tags($tp['deskripsi_tp'] ?? ''), 0, 35)) ?>...
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <?php 
        $is_ready = ($id_kelas_filter && $id_guru_mapel_filter && $id_cp_filter && $id_tp_filter); 
        ?>

        <!-- FORM INPUT NILAI -->
        <div class="card lms-card shadow-sm">
            <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div>
                    <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.05rem;">
                        <i class="fas fa-list-ol text-primary mr-1"></i> Daftar Nilai Siswa
                        <?php if (!empty($nama_mapel_terpilih)): ?>
                            <span class="text-primary">(<?= htmlspecialchars($nama_mapel_terpilih) ?>)</span>
                        <?php endif; ?>
                    </h5>
                    <small class="text-muted">Masukkan nilai formatif rentang 0 s.d. 100 untuk setiap siswa.</small>
                </div>
            </div>
            
            <form action="<?= BASE_URL ?>input_nilai/save" method="POST" id="formInputNilai">
                <?php if ($is_ready): ?>
                    <input type="hidden" name="id_kelas" value="<?= $id_kelas_filter ?>">
                    <input type="hidden" name="id_guru_mapel" value="<?= $id_guru_mapel_filter ?>">
                    <input type="hidden" name="id_cp" value="<?= $id_cp_filter ?>">
                    <input type="hidden" name="id_tp" value="<?= $id_tp_filter ?>">
                <?php endif; ?>
                
                <div class="card-body p-3 p-md-4">
                    <?php if ($is_ready && !empty($siswa_nilai)): ?>
                        <!-- TOOLBAR ACTION BUTTONS -->
                        <div class="mb-3 mobile-action-btns d-flex flex-wrap" style="gap: 8px;">
                            <?php
                            $template_url = BASE_URL . "input_nilai/template?id_kelas={$id_kelas_filter}&id_guru_mapel={$id_guru_mapel_filter}&id_cp={$id_cp_filter}&id_tp={$id_tp_filter}";
                            ?>
                            <button type="button" class="btn btn-sm btn-primary font-weight-bold rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#modalSyncNilaiLms" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                                <i class="fas fa-bolt mr-1 text-warning"></i> ⚡ Tarik Nilai dari Tes Formatif LMS
                            </button>
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalImportNilai">
                                <i class="fa fa-upload mr-1"></i> Impor Nilai Excel
                            </button>
                            <a href="<?= $template_url ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm font-weight-bold">
                                <i class="fa fa-download mr-1"></i> Download Template
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!$is_ready): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-hand-pointer fa-3x mb-3 d-block opacity-50"></i>
                            <h6 class="font-weight-bold text-dark">Lengkapi Pilihan Filter</h6>
                            <p class="small text-muted mb-0">Silakan pilih Kelas, Mata Pelajaran, CP, dan TP terlebih dahulu untuk menampilkan daftar siswa.</p>
                        </div>
                    <?php elseif (empty($siswa_nilai)): ?>
                        <div class="text-center py-5 text-warning">
                            <i class="fas fa-exclamation-circle fa-3x mb-3 d-block"></i>
                            <h6 class="font-weight-bold">Tidak Ada Siswa Terdaftar</h6>
                            <p class="small text-muted mb-0">Tidak ada siswa yang ditempatkan di kelas ini pada Tahun Ajaran aktif.</p>
                        </div>
                    <?php else: ?>

                        <!-- 1. DESKTOP VIEW (TABLE) -->
                        <div class="table-responsive desktop-table-container">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="thead-light text-center" style="font-size: 0.85rem;">
                                    <tr>
                                        <th width="5%" class="py-2">No</th>
                                        <th class="py-2 text-left">Nama Siswa</th>
                                        <th width="15%" class="py-2">Nilai (0-100)</th>
                                        <th width="30%" class="py-2 text-left">Keterangan (Opsional)</th>
                                        <th width="25%" class="py-2 text-left">Deskripsi Otomatis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; $row_idx = 0; foreach ($siswa_nilai as $s): ?>
                                        <tr id="row_siswa_<?= $s['id_siswa'] ?>">
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <strong class="text-dark d-block"><?= htmlspecialchars($s['nama']) ?></strong>
                                                <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> NISN: <?= htmlspecialchars($s['nisn']) ?></small>
                                            </td>
                                            <td>
                                                <input type="number" name="nilai[<?= $s['id_penempatan'] ?>][nilai]"
                                                    class="form-control form-control-sm text-center input-nilai-formatif font-weight-bold rounded-pill" 
                                                    min="0" max="100" step="0.01" data-row="<?= $row_idx ?>"
                                                    data-id-siswa="<?= $s['id_siswa'] ?>"
                                                    data-nisn="<?= $s['nisn'] ?>"
                                                    style="font-size: 1rem; height: 38px;"
                                                    value="<?= $s['nilai'] ?? '' ?>">
                                            </td>
                                            <td>
                                                <input type="text" name="nilai[<?= $s['id_penempatan'] ?>][keterangan]"
                                                    class="form-control form-control-sm rounded-pill"
                                                    placeholder="Catatan guru..."
                                                    value="<?= htmlspecialchars($s['keterangan'] ?? '') ?>">
                                            </td>
                                            <td>
                                                <small class="text-muted d-block"><?= htmlspecialchars($s['deskripsi'] ?? 'Belum Dinilai') ?></small>
                                            </td>
                                        </tr>
                                    <?php $row_idx++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- 2. MOBILE VIEW (STUDENT SCORE CARDS) -->
                        <div class="mobile-cards-container">
                            <?php $no_m = 1; $row_m_idx = 0; foreach ($siswa_nilai as $s): ?>
                                <div class="student-score-card" id="card_siswa_<?= $s['id_siswa'] ?>">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center" style="min-width: 0;">
                                            <div class="student-avatar-num mr-2">
                                                <?= $no_m++ ?>
                                            </div>
                                            <div style="min-width: 0;">
                                                <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="font-size: 0.92rem;">
                                                    <?= htmlspecialchars($s['nama']) ?>
                                                </h6>
                                                <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> NISN: <?= htmlspecialchars($s['nisn']) ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row align-items-center mt-2" style="row-gap: 8px;">
                                        <div class="col-5">
                                            <label class="small font-weight-bold text-muted mb-1" style="font-size: 0.72rem;">NILAI (0-100)</label>
                                            <input type="number" name="nilai[<?= $s['id_penempatan'] ?>][nilai]"
                                                class="form-control text-center input-nilai-formatif font-weight-bold rounded-pill" 
                                                min="0" max="100" step="0.01" data-row="<?= $row_m_idx ?>"
                                                data-id-siswa="<?= $s['id_siswa'] ?>"
                                                data-nisn="<?= $s['nisn'] ?>"
                                                placeholder="0"
                                                style="font-size: 1.1rem; height: 42px;"
                                                value="<?= $s['nilai'] ?? '' ?>">
                                        </div>
                                        <div class="col-7">
                                            <label class="small font-weight-bold text-muted mb-1" style="font-size: 0.72rem;">KETERANGAN</label>
                                            <input type="text" name="nilai[<?= $s['id_penempatan'] ?>][keterangan]"
                                                class="form-control rounded-pill"
                                                placeholder="Opsional..."
                                                style="font-size: 0.85rem; height: 42px;"
                                                value="<?= htmlspecialchars($s['keterangan'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <?php if (!empty($s['deskripsi'])): ?>
                                        <div class="mt-2 pt-1 border-top">
                                            <small class="text-muted font-italic" style="font-size: 0.72rem;"><i class="fas fa-info-circle text-primary mr-1"></i> <?= htmlspecialchars($s['deskripsi']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php $row_m_idx++; endforeach; ?>
                        </div>

                    <?php endif; ?>
                </div>
                
                <?php if ($is_ready && !empty($siswa_nilai)): ?>
                    <div class="card-footer bg-light p-3 text-right d-none d-md-block">
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold rounded-pill shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                            <i class="fas fa-save mr-1"></i> Simpan Nilai Formatif
                        </button>
                    </div>

                    <!-- Sticky Mobile Save Bar -->
                    <div class="mobile-sticky-save-bar">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold rounded-pill shadow" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none; padding: 10px;">
                            <i class="fas fa-save mr-1"></i> Simpan Semua Nilai
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>

    </div>
</section>

<!-- Modal Tarik Nilai Formatif LMS -->
<?php if (isset($id_kelas_filter) && $id_kelas_filter): ?>
<div class="modal fade" id="modalSyncNilaiLms" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #4f46e5, #3730a3) !important;">
                <h5 class="modal-title font-weight-bold" style="font-size: 1.05rem;">
                    <i class="fas fa-bolt text-warning mr-2"></i> Tarik Nilai Tes Formatif LMS
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border small text-muted mb-3">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Nilai akhir kuis / evaluasi formatif siswa pada modul LMS yang dipilih akan otomatis disalin ke form nilai TP siswa di kelas ini.
                </div>

                <div class="form-group mb-0">
                    <label class="small font-weight-bold text-dark text-uppercase">PILIH MODUL MATERI / TES FORMATIF</label>
                    <select id="sync_id_materi" class="form-control custom-filter-select" style="width: 100%;">
                        <option value="">-- Pilih Modul Materi Pembelajaran --</option>
                        <?php foreach ($materi_lms_list ?? [] as $mat): ?>
                            <option value="<?= $mat['id_materi'] ?>"><?= htmlspecialchars($mat['judul_materi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm" id="btnProsesSyncLms" onclick="tarikNilaiLms()" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                    <i class="fas fa-bolt mr-1"></i> Terapkan Nilai ke Form
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Import Nilai -->
<?php if (isset($id_kelas_filter) && $id_kelas_filter && isset($id_guru_mapel_filter) && $id_guru_mapel_filter && isset($id_cp_filter) && $id_cp_filter && isset($id_tp_filter) && $id_tp_filter): ?>
<div class="modal fade" id="modalImportNilai" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header bg-success text-white p-3">
                <h5 class="modal-title font-weight-bold" style="font-size: 1.05rem;"><i class="fa fa-upload mr-2"></i>Impor Nilai dari Excel</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>input_nilai/import" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_kelas" value="<?= $id_kelas_filter ?>">
                <input type="hidden" name="id_guru_mapel" value="<?= $id_guru_mapel_filter ?>">
                <input type="hidden" name="id_cp" value="<?= $id_cp_filter ?>">
                <input type="hidden" name="id_tp" value="<?= $id_tp_filter ?>">
                <div class="modal-body p-4">
                    <div class="alert alert-info small mb-3">
                        <strong><i class="fa fa-info-circle"></i> Panduan Impor:</strong><br>
                        1. Download template terlebih dahulu dengan tombol <strong>"Download Template"</strong>.<br>
                        2. Isi kolom <strong>NILAI</strong> untuk setiap siswa (0–100).<br>
                        3. Upload kembali file yang sudah diisi di bawah ini.<br>
                        <small class="text-white-50">Jangan mengubah kolom ID_PENEMPATAN dan nama siswa.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-dark text-uppercase">PILIH FILE EXCEL (.XLSX / .XLS)</label>
                        <input type="file" name="file_excel" class="form-control-file" accept=".xlsx,.xls" required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 font-weight-bold shadow-sm">
                        <i class="fa fa-upload mr-1"></i> Upload &amp; Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    function submitFilter() {
        document.getElementById('filterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.input-nilai-formatif');
        const totalRows = inputs.length;

        inputs.forEach(input => {
            input.addEventListener('keydown', function(e) {
                const currentRow = parseInt(this.getAttribute('data-row'));
                let targetRow = -1;

                if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    targetRow = currentRow + 1;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    targetRow = currentRow - 1;
                }

                if (targetRow >= 0 && targetRow < totalRows) {
                    const targetInput = document.querySelector(`.input-nilai-formatif[data-row="${targetRow}"]`);
                    if (targetInput) {
                        targetInput.focus();
                        targetInput.select();
                    }
                }
            });

            input.addEventListener('focus', function() {
                document.querySelectorAll('tr').forEach(tr => tr.classList.remove('row-focused'));
                const tr = this.closest('tr');
                if (tr) tr.classList.add('row-focused');
            });
        });

        // Sync inputs between desktop and mobile if value changes
        $('.input-nilai-formatif').on('input', function() {
            const idSiswa = $(this).data('id-siswa');
            const val = $(this).val();
            $(`.input-nilai-formatif[data-id-siswa="${idSiswa}"]`).not(this).val(val);
        });

        if (inputs.length > 0 && window.innerWidth > 768) {
            inputs[0].focus();
            inputs[0].select();
        }
    });

    // ============================================================
    // ⚡ TARIK NILAI TES FORMATIF LMS KE FORM INPUT NILAI TP
    // ============================================================
    window.tarikNilaiLms = function() {
        const idMateri = $('#sync_id_materi').val();
        const idKelas = '<?= $id_kelas_filter ?>';

        if (!idMateri) {
            Swal.fire({ icon: 'warning', title: 'Pilih Modul', text: 'Silakan pilih modul pembelajaran / tes formatif LMS terlebih dahulu.' });
            return;
        }

        const btn = $('#btnProsesSyncLms');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengambil Data LMS...');

        $.getJSON(`<?= BASE_URL ?>lms/get_nilai_formatif_ajax?id_materi=${idMateri}&id_kelas=${idKelas}`, function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Terapkan Nilai ke Form');
            if (res.status === 'ok' && res.data) {
                let syncedCount = 0;
                const dataSiswa = res.data;

                $('.input-nilai-formatif').each(function() {
                    const idSiswa = $(this).data('id-siswa');
                    if (dataSiswa[idSiswa] && dataSiswa[idSiswa].nilai !== null) {
                        $(this).val(dataSiswa[idSiswa].nilai);
                        syncedCount++;
                    }
                });

                $('#modalSyncNilaiLms').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Sinkronisasi Berhasil!',
                    text: `Berhasil menarik ${syncedCount} nilai tes formatif siswa dari LMS. Silakan periksa dan klik Simpan Nilai.`,
                    timer: 2500,
                    showConfirmButton: true
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Tidak ada data nilai pada modul ini.' });
            }
        }).fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Terapkan Nilai ke Form');
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server LMS.' });
        });
    };
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
