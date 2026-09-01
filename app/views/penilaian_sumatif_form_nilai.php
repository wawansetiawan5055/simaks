<?php include __DIR__.'/partials/header.php'; ?>

<style>
    .page-sumatif-nilai .lms-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff;
        margin-bottom: 20px;
    }

    .input-nilai-sumatif:focus {
        background-color: #fff8e1 !important;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important;
    }
    tr.row-focused {
        background-color: rgba(79, 70, 229, 0.05) !important;
    }

    /* Button Capaian Styling */
    .btn-capaian {
        font-weight: 700 !important;
        padding: 4px 10px !important;
        font-size: 0.8rem !important;
        transition: all 0.15s ease !important;
    }
    .btn-capaian.active-A, .btn-capaian.btn-success {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3) !important;
    }
    .btn-capaian.active-B, .btn-capaian.btn-primary {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3) !important;
    }
    .btn-capaian.active-C, .btn-capaian.btn-warning {
        background-color: #f59e0b !important;
        border-color: #f59e0b !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3) !important;
    }

    /* Student Card Item for Mobile */
    .student-sumatif-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .student-sumatif-card:focus-within {
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

    .tp-capaian-box-mobile {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 10px;
        margin-top: 8px;
    }

    /* Floating / Sticky Mobile Save Bar */
    .mobile-sticky-save-bar {
        display: none;
    }

    @media (max-width: 768px) {
        .page-sumatif-nilai .content-header h1 {
            font-size: 1.15rem !important;
        }
        .page-sumatif-nilai .lms-card {
            border-radius: 12px !important;
            padding: 12px 14px !important;
        }
        .mobile-header-btns {
            display: flex;
            gap: 8px;
            width: 100%;
            margin-top: 8px;
        }
        .mobile-header-btns .btn {
            flex: 1;
            font-size: 0.8rem !important;
            padding: 7px 10px !important;
            justify-content: center;
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

<div class="content-header pt-3 mb-2 page-sumatif-nilai">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Input Nilai Sumatif
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0 mobile-header-btns">
                <a href="<?= BASE_URL ?>penilaian_sumatif?id_kelas=<?= $agenda['id_kelas'] ?>&id_guru_mapel=<?= $agenda['id_guru_mapel'] ?>" 
                   class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm font-weight-bold mr-1">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalImportSumatif">
                    <i class="fas fa-file-excel mr-1"></i> Impor Excel
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content page-sumatif-nilai">
    <div class="container-fluid">

        <form action="<?= BASE_URL ?>penilaian_sumatif/save_nilai" method="POST" id="formNilaiSumatif">
            <input type="hidden" name="id_sumatif" value="<?= $agenda['id_sumatif'] ?>">
            <input type="hidden" name="id_guru_mapel" value="<?= $agenda['id_guru_mapel'] ?>">

            <!-- INFO CARDS: IDENTITAS & TP -->
            <div class="row mb-3" style="row-gap: 14px;">
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="card lms-card p-3 shadow-sm h-100 mb-0">
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                            <div class="bg-light p-2 rounded mr-2 text-primary font-weight-bold" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.92rem;">Identitas Penilaian</h6>
                        </div>
                        <div class="small">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Nama Agenda</span>
                                <strong class="text-dark text-right"><?= htmlspecialchars($agenda['nama_penilaian']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Jenis Penilaian</span>
                                <span class="badge badge-primary px-2 py-1" style="border-radius: 50px;"><?= htmlspecialchars($agenda['jenis_sumatif']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Kelas / Mapel</span>
                                <strong class="text-dark"><?= htmlspecialchars($agenda['nama_kelas']) ?> • <?= htmlspecialchars($agenda['nama_mapel']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Tanggal Pelaksanaan</span>
                                <span class="text-muted"><?= $agenda['tanggal_penilaian'] ? date('d/m/Y', strtotime($agenda['tanggal_penilaian'])) : '-' ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 col-md-6 col-12">
                    <div class="card lms-card p-3 shadow-sm h-100 mb-0">
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                            <div class="bg-light p-2 rounded mr-2 text-success font-weight-bold" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.92rem;">Tujuan Pembelajaran (TP) yang Dicakup</h6>
                        </div>
                        <div style="max-height: 140px; overflow-y: auto;">
                            <?php 
                            $selected_tps_count = 0;
                            if (!empty($tp_list)): 
                            ?>
                                <ul class="list-group list-group-flush small">
                                <?php foreach($tp_list as $tp): ?>
                                    <?php if(in_array($tp['id_tp'], $selected_tps_ids)): $selected_tps_count++; ?>
                                        <li class="list-group-item py-1 px-0 border-0 d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success mr-2 mt-1"></i>
                                            <div>
                                                <strong class="text-dark">[<?= htmlspecialchars($tp['kode_tp']) ?>]</strong> <?= htmlspecialchars($tp['deskripsi_tp']) ?>
                                                <input type="hidden" name="selected_tps[]" value="<?= $tp['id_tp'] ?>">
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <?php if ($selected_tps_count === 0): ?>
                                <div class="alert alert-warning py-2 mb-0 small">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada TP yang dipilih untuk agenda ini. Silakan edit agenda untuk memilih TP.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM NILAI CARD -->
            <div class="card lms-card shadow-sm">
                <div class="card-header bg-white border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.05rem;">
                            <i class="fas fa-users text-primary mr-1"></i> Input Skor &amp; Capaian Siswa
                        </h5>
                        <small class="text-muted">Pilih capaian A (Sangat Baik), B (Baik), atau C (Perlu Bimbingan) pada masing-masing TP.</small>
                    </div>
                    <?php if (!empty($selected_tps_ids)): ?>
                        <span class="badge badge-light border text-muted d-none d-md-inline-block px-3 py-2" style="border-radius: 50px; font-size: 0.78rem;">
                            <i class="fas fa-keyboard mr-1 text-primary"></i> Navigasi: <kbd>A</kbd> <kbd>B</kbd> <kbd>C</kbd> • <kbd>↑</kbd><kbd>↓</kbd><kbd>Enter</kbd>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="card-body p-3 p-md-4">
                    <?php if (empty($siswa_nilai)): ?>
                        <div class="text-center py-5 text-warning">
                            <i class="fas fa-exclamation-circle fa-3x mb-3 d-block"></i>
                            <h6 class="font-weight-bold">Tidak Ada Siswa</h6>
                            <p class="small text-muted mb-0">Tidak ada siswa yang ditempatkan di kelas ini pada Tahun Ajaran aktif.</p>
                        </div>
                    <?php else: ?>

                        <!-- 1. DESKTOP VIEW (TABLE) -->
                        <div class="table-responsive desktop-table-container">
                            <table class="table table-bordered table-hover align-middle mb-0" id="tabel-nilai-sumatif">
                                <thead class="thead-light text-center" style="font-size: 0.85rem;">
                                    <tr>
                                        <th width="4%" class="py-2">No</th>
                                        <th class="py-2 text-left">Nama Siswa</th>
                                        <th width="12%" class="py-2">Nilai (0-100)</th>
                                        <?php foreach ($selected_tps_ids as $id_tp):
                                            $tp_data = $selected_tps_details[$id_tp] ?? null;
                                            $tp_kode = $selected_tps_kodes[$id_tp] ?? 'TP';
                                            if (!$tp_data) continue;
                                        ?>
                                        <th class="py-2 text-center" style="min-width: 120px;" title="<?= htmlspecialchars($tp_data) ?>">
                                            <span class="badge badge-primary px-2 py-1 mb-1 d-block" style="border-radius: 50px;"><?= htmlspecialchars($tp_kode) ?></span>
                                            <small class="text-muted font-weight-bold">Capaian TP</small>
                                        </th>
                                        <?php endforeach; ?>
                                        <th width="28%" class="py-2 text-left">Deskripsi Capaian (Otomatis)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $row_idx = 0; foreach($siswa_nilai as $s):
                                        $id_pen = $s['id_penempatan'];
                                    ?>
                                    <tr data-siswa-row="<?= $row_idx ?>" id="row_desk_<?= $s['id_siswa'] ?>">
                                        <td class="text-center font-weight-bold text-muted"><?= $row_idx + 1 ?></td>
                                        <td>
                                            <strong class="text-dark d-block nama-siswa"><?= htmlspecialchars($s['nama']) ?></strong>
                                            <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> NISN: <?= htmlspecialchars($s['nisn']) ?></small>
                                        </td>
                                        <td>
                                            <input type="number" name="nilai[<?= $id_pen ?>][nilai]"
                                                   class="form-control form-control-sm text-center font-weight-bold rounded-pill input-nilai-sumatif"
                                                   min="0" max="100" step="0.01"
                                                   placeholder="0.00"
                                                   data-row="<?= $row_idx ?>"
                                                   data-id-siswa="<?= $s['id_siswa'] ?>"
                                                   style="font-size: 1rem; height: 38px;"
                                                   value="<?= $s['nilai'] !== null ? $s['nilai'] : '' ?>">
                                        </td>
                                        <?php foreach ($selected_tps_ids as $col_idx => $id_tp):
                                            if (!isset($selected_tps_details[$id_tp])) continue;
                                            $existing_capaian = $capaian_tp_siswa[$id_pen][$id_tp] ?? null;
                                        ?>
                                        <td class="text-center capaian-tp-cell" data-id-tp="<?= $id_tp ?>" data-id-penempatan="<?= $id_pen ?>">
                                            <input type="hidden" name="capaian_tp[<?= $id_pen ?>][<?= $id_tp ?>]" class="capaian-hidden-input" value="<?= $existing_capaian ?? '' ?>">
                                            <div class="btn-group btn-group-sm capaian-btn-group" role="group"
                                                 data-row="<?= $row_idx ?>" data-col="<?= $col_idx ?>">
                                                <button type="button" class="btn btn-capaian <?= $existing_capaian === 'A' ? 'btn-success' : 'btn-outline-success' ?>" data-val="A">A</button>
                                                <button type="button" class="btn btn-capaian <?= $existing_capaian === 'B' ? 'btn-primary' : 'btn-outline-primary' ?>" data-val="B">B</button>
                                                <button type="button" class="btn btn-capaian <?= $existing_capaian === 'C' ? 'btn-warning' : 'btn-outline-warning' ?>" data-val="C">C</button>
                                            </div>
                                        </td>
                                        <?php endforeach; ?>
                                        <td>
                                            <textarea name="nilai[<?= $id_pen ?>][deskripsi_capaian]"
                                                      rows="2" class="form-control form-control-sm bg-light deskripsi-auto rounded"
                                                      style="resize: none; font-size: 0.8rem;"
                                                      readonly><?= htmlspecialchars($s['deskripsi_capaian'] ?? 'Akan digenerate otomatis...') ?></textarea>
                                        </td>
                                    </tr>
                                    <?php $row_idx++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- 2. MOBILE VIEW (STUDENT SUMATIF CARDS) -->
                        <div class="mobile-cards-container">
                            <?php $no_m = 1; $row_m_idx = 0; foreach($siswa_nilai as $s):
                                $id_pen = $s['id_penempatan'];
                            ?>
                                <div class="student-sumatif-card" id="card_siswa_<?= $s['id_siswa'] ?>" data-siswa-row="<?= $row_m_idx ?>">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center" style="min-width: 0;">
                                            <div class="student-avatar-num mr-2">
                                                <?= $no_m++ ?>
                                            </div>
                                            <div style="min-width: 0;">
                                                <h6 class="font-weight-bold text-dark mb-0 text-truncate nama-siswa" style="font-size: 0.92rem;">
                                                    <?= htmlspecialchars($s['nama']) ?>
                                                </h6>
                                                <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> NISN: <?= htmlspecialchars($s['nisn']) ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- NILAI INPUT BOX -->
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-muted mb-1" style="font-size: 0.72rem;">NILAI SUMATIF (0-100)</label>
                                        <input type="number" name="nilai[<?= $id_pen ?>][nilai]"
                                            class="form-control text-center font-weight-bold rounded-pill input-nilai-sumatif"
                                            min="0" max="100" step="0.01"
                                            placeholder="0.00"
                                            data-row="<?= $row_m_idx ?>"
                                            data-id-siswa="<?= $s['id_siswa'] ?>"
                                            style="font-size: 1.1rem; height: 42px;"
                                            value="<?= $s['nilai'] !== null ? $s['nilai'] : '' ?>">
                                    </div>

                                    <!-- CAPAIAN PER-TP BUTTONS -->
                                    <?php if (!empty($selected_tps_ids)): ?>
                                        <label class="small font-weight-bold text-muted mb-1 d-block" style="font-size: 0.72rem;">CAPAIAN PER TUJUAN PEMBELAJARAN (TP)</label>
                                        <?php foreach ($selected_tps_ids as $col_m_idx => $id_tp):
                                            if (!isset($selected_tps_details[$id_tp])) continue;
                                            $tp_data = $selected_tps_details[$id_tp];
                                            $tp_kode = $selected_tps_kodes[$id_tp] ?? 'TP';
                                            $existing_capaian = $capaian_tp_siswa[$id_pen][$id_tp] ?? null;
                                        ?>
                                            <div class="tp-capaian-box-mobile capaian-tp-cell" data-id-tp="<?= $id_tp ?>" data-id-penempatan="<?= $id_pen ?>">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge badge-primary px-2 py-1 font-weight-bold" style="border-radius: 50px; font-size: 0.72rem;">
                                                        <?= htmlspecialchars($tp_kode) ?>
                                                    </span>
                                                    <div class="btn-group btn-group-sm capaian-btn-group" role="group"
                                                         data-row="<?= $row_m_idx ?>" data-col="<?= $col_m_idx ?>">
                                                        <button type="button" class="btn btn-capaian <?= $existing_capaian === 'A' ? 'btn-success' : 'btn-outline-success' ?>" data-val="A">A</button>
                                                        <button type="button" class="btn btn-capaian <?= $existing_capaian === 'B' ? 'btn-primary' : 'btn-outline-primary' ?>" data-val="B">B</button>
                                                        <button type="button" class="btn btn-capaian <?= $existing_capaian === 'C' ? 'btn-warning' : 'btn-outline-warning' ?>" data-val="C">C</button>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.3;">
                                                    <?= htmlspecialchars($tp_data) ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- AUTO DESCRIPTION -->
                                    <div class="mt-2 pt-2 border-top">
                                        <label class="small font-weight-bold text-muted mb-1" style="font-size: 0.72rem;">DESKRIPSI CAPAIAN OTOMATIS</label>
                                        <textarea name="nilai[<?= $id_pen ?>][deskripsi_capaian]"
                                                  rows="2" class="form-control form-control-sm bg-light deskripsi-auto rounded"
                                                  style="resize: none; font-size: 0.76rem;"
                                                  readonly><?= htmlspecialchars($s['deskripsi_capaian'] ?? 'Akan digenerate otomatis...') ?></textarea>
                                    </div>
                                </div>
                            <?php $row_m_idx++; endforeach; ?>
                        </div>

                    <?php endif; ?>
                </div>

                <?php if (!empty($siswa_nilai)): ?>
                    <div class="card-footer bg-light p-3 text-right d-none d-md-block">
                        <a href="<?= BASE_URL ?>penilaian_sumatif?id_kelas=<?= $agenda['id_kelas'] ?>&id_guru_mapel=<?= $agenda['id_guru_mapel'] ?>"
                           class="btn btn-outline-secondary rounded-pill px-4 mr-2 font-weight-bold">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold rounded-pill shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                            <i class="fas fa-save mr-1"></i> Simpan Nilai Sumatif
                        </button>
                    </div>

                    <!-- Sticky Mobile Save Bar -->
                    <div class="mobile-sticky-save-bar">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold rounded-pill shadow" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none; padding: 10px;">
                            <i class="fas fa-save mr-1"></i> Simpan Nilai Sumatif
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </form>

    </div>
</section>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImportSumatif" tabindex="-1" aria-labelledby="modalImportSumatifLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-success text-white p-3">
                <h5 class="modal-title font-weight-bold" id="modalImportSumatifLabel" style="font-size: 1.05rem;">
                    <i class="fas fa-file-excel mr-2"></i> Impor Nilai Sumatif dari Excel
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form action="<?= BASE_URL ?>penilaian_sumatif/import" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="alert alert-info small mb-3">
                        <strong><i class="fas fa-info-circle mr-1"></i> Panduan Impor:</strong><br>
                        1. Download template Excel terlebih dahulu.<br>
                        2. Isi nilai (0-100) dan capaian TP (A/B/C) pada kolom yang disediakan.<br>
                        3. Unggah kembali file Excel yang telah diisi.
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">PILIH FILE EXCEL (.XLSX / .XLS)</label>
                        <input type="file" name="file_excel" class="form-control-file" accept=".xlsx, .xls" required>
                    </div>
                    <hr>
                    <a href="<?= BASE_URL ?>penilaian_sumatif/template?id_sumatif=<?= $agenda['id_sumatif'] ?>" class="btn btn-outline-success btn-block rounded-pill font-weight-bold">
                        <i class="fas fa-download mr-1"></i> Download Template Excel
                    </a>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-upload mr-1"></i> Unggah &amp; Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.input-nilai-sumatif');
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
                const targetInput = document.querySelector(`.input-nilai-sumatif[data-row="${targetRow}"]`);
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

    // Auto focus first input on desktop
    if (inputs.length > 0 && window.innerWidth > 768) {
        inputs[0].focus();
        inputs[0].select();
    }

    // Synchronize inputs between desktop and mobile if value changes
    $('.input-nilai-sumatif').on('input', function() {
        const idSiswa = $(this).data('id-siswa');
        const val = $(this).val();
        $(`.input-nilai-sumatif[data-id-siswa="${idSiswa}"]`).not(this).val(val);
    });

    // Real-time Description Generation based on Capaian per-TP
    const tpDetails = <?= json_encode($selected_tps_details ?? []) ?>;
    
    function cleanTpDescription(text) {
        const prefixes = [/Peserta didik dapat /i, /Peserta didik mampu /i, /Siswa dapat /i, /Siswa mampu /i, /Dapat /i, /Mampu /i];
        let cleaned = text.trim();
        prefixes.forEach(p => {
            cleaned = cleaned.replace(p, '');
        });
        return cleaned.charAt(0).toLowerCase() + cleaned.slice(1);
    }

    function updateDescription(container) {
        const nameEl = container.querySelector('.nama-siswa');
        if (!nameEl) return;
        const name = nameEl.textContent.trim();
        const firstName = name.split(' ')[0];
        const prefix = "Ananda " + firstName;
        const textareas = container.querySelectorAll('.deskripsi-auto');
        
        const capaianInputs = container.querySelectorAll('.capaian-hidden-input');
        
        let baik_sekali = [];
        let baik = [];
        let perlu_bimbingan = [];
        
        capaianInputs.forEach(input => {
            const cell = input.closest('.capaian-tp-cell');
            if (!cell) return;
            const idTp = cell.getAttribute('data-id-tp');
            const val = input.value;
            const desc = tpDetails[idTp] ? cleanTpDescription(tpDetails[idTp]) : null;
            
            if (desc && val) {
                if (val === 'A') baik_sekali.push(desc);
                else if (val === 'B') baik.push(desc);
                else if (val === 'C') perlu_bimbingan.push(desc);
            }
        });
        
        let finalDesc = "";
        if (baik_sekali.length === 0 && baik.length === 0 && perlu_bimbingan.length === 0) {
            finalDesc = "Akan digenerate otomatis setelah capaian TP diisi.";
        } else {
            let parts = [];
            if (baik_sekali.length > 0) {
                parts.push("menunjukkan pemahaman yang sangat baik dalam " + baik_sekali.join(', '));
            }
            if (baik.length > 0) {
                const prefix_baik = baik_sekali.length === 0 ? "menunjukkan pemahaman yang baik dalam " : "baik dalam ";
                parts.push(prefix_baik + baik.join(', '));
            }
            if (perlu_bimbingan.length > 0) {
                parts.push("masih perlu bimbingan dalam " + perlu_bimbingan.join(', '));
            }
            
            const last_part = parts.pop();
            if (parts.length === 0) {
                finalDesc = prefix + " " + last_part + ".";
            } else {
                finalDesc = prefix + " " + parts.join(', ') + " dan " + last_part + ".";
            }
        }

        textareas.forEach(t => t.value = finalDesc);
    }

    // Initialize descriptions for rows with existing capaian
    document.querySelectorAll('#tabel-nilai-sumatif tbody tr, .student-sumatif-card').forEach(container => {
        const hasCapaian = Array.from(container.querySelectorAll('.capaian-hidden-input')).some(i => i.value !== '');
        if (hasCapaian) {
            updateDescription(container);
        }
    });

    // Handle Button Clicks
    $(document).on('click', '.btn-capaian', function() {
        const val = $(this).attr('data-val');
        const cell = $(this).closest('.capaian-tp-cell');
        const idTp = cell.attr('data-id-tp');
        const idPen = cell.attr('data-id-penempatan');

        // Update all cells matching idTp and idPen (both desktop row & mobile card)
        $(`.capaian-tp-cell[data-id-tp="${idTp}"][data-id-penempatan="${idPen}"]`).each(function() {
            const hiddenInput = $(this).find('.capaian-hidden-input');
            const group = $(this).find('.capaian-btn-group');
            
            // Reset buttons
            group.find('.btn-capaian').each(function() {
                const v = $(this).attr('data-val');
                $(this).removeClass('btn-success btn-primary btn-warning');
                $(this).addClass(v === 'A' ? 'btn-outline-success' : (v === 'B' ? 'btn-outline-primary' : 'btn-outline-warning'));
            });

            // Set active
            const targetBtn = group.find(`[data-val="${val}"]`);
            targetBtn.removeClass('btn-outline-success btn-outline-primary btn-outline-warning');
            targetBtn.addClass(val === 'A' ? 'btn-success' : (val === 'B' ? 'btn-primary' : 'btn-warning'));
            hiddenInput.val(val);

            // Update description
            const parentRow = $(this).closest('tr, .student-sumatif-card');
            if (parentRow.length) {
                updateDescription(parentRow[0]);
            }
        });
    });

    // Handle Keyboard Navigation for Capaian on Desktop
    document.addEventListener('keydown', function(e) {
        const activeEl = document.activeElement;
        if (!activeEl) return;
        
        let rowIdx = -1;
        let isScoreInput = false;
        
        if (activeEl.classList.contains('input-nilai-sumatif')) {
            rowIdx = parseInt(activeEl.getAttribute('data-row'));
            isScoreInput = true;
        } else if (activeEl.classList.contains('btn-capaian')) {
            const group = activeEl.closest('.capaian-btn-group');
            rowIdx = parseInt(group.getAttribute('data-row'));
        }

        if (rowIdx === -1) return;

        const key = e.key.toUpperCase();
        
        // Handle A/B/C typing
        if (['A', 'B', 'C'].includes(key)) {
            let group;
            if (activeEl.classList.contains('btn-capaian')) {
                group = activeEl.closest('.capaian-btn-group');
                e.preventDefault();
            } else if (isScoreInput) {
                return;
            }
            
            if (group) {
                const btn = group.querySelector(`[data-val="${key}"]`);
                if (btn) $(btn).trigger('click');
                
                const currentCol = parseInt(group.getAttribute('data-col'));
                const maxCol = <?= count($selected_tps_ids ?? []) ?> - 1;
                
                if (currentCol < maxCol) {
                    const nextGroup = document.querySelector(`.desktop-table-container .capaian-btn-group[data-row="${rowIdx}"][data-col="${currentCol + 1}"]`);
                    if (nextGroup) nextGroup.querySelector('button').focus();
                } else {
                    const nextGroup = document.querySelector(`.desktop-table-container .capaian-btn-group[data-row="${rowIdx + 1}"][data-col="0"]`);
                    if (nextGroup) nextGroup.querySelector('button').focus();
                }
            }
        }
    });
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>
