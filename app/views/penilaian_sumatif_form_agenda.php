<?php include __DIR__ . '/partials/header.php'; ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .page-sumatif-agenda .lms-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff;
        margin-bottom: 20px;
    }

    .page-sumatif-agenda .custom-filter-select,
    .page-sumatif-agenda .form-control {
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #f8fafc !important;
        font-size: 0.9rem !important;
        transition: all 0.2s ease;
    }
    .page-sumatif-agenda .custom-filter-select:focus,
    .page-sumatif-agenda .form-control:focus {
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 10px !important;
        min-height: 42px !important;
        background-color: #f8fafc !important;
        padding: 4px 8px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }

    .tp-checkbox-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
        transition: all 0.15s ease;
    }
    .tp-checkbox-item:hover {
        border-color: #4f46e5;
        background: #f8fafc;
    }

    @media (max-width: 768px) {
        .page-sumatif-agenda .content-header h1 {
            font-size: 1.15rem !important;
        }
        .page-sumatif-agenda .lms-card {
            border-radius: 12px !important;
            padding: 14px !important;
        }
        .page-sumatif-agenda .form-control {
            font-size: 0.85rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2 page-sumatif-agenda">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        <?= $agenda ? 'Edit' : 'Buat' ?> Agenda Penilaian Sumatif
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>penilaian_sumatif" class="btn btn-sm btn-outline-secondary rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content page-sumatif-agenda">
    <div class="container-fluid">
        <div class="card lms-card shadow-sm p-3 p-md-4">
            <form action="<?= BASE_URL ?>penilaian_sumatif/save_agenda" method="POST">
                <input type="hidden" name="id_sumatif" value="<?= $agenda['id_sumatif'] ?? '' ?>">
                
                <div class="row" style="row-gap: 14px;">
                    <!-- KOLOM KIRI -->
                    <div class="col-md-6 col-12">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                <i class="fas fa-chalkboard text-primary mr-1"></i> Kelas <span class="text-danger">*</span>
                            </label>
                            <select name="id_kelas" id="id_kelas" class="form-control custom-filter-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_diajar as $k): ?>
                                    <option value="<?= $k['id_kelas'] ?>" <?= $agenda && $agenda['id_kelas'] == $k['id_kelas'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                <i class="fas fa-book text-primary mr-1"></i> Mata Pelajaran <span class="text-danger">*</span>
                            </label>
                            <select name="id_guru_mapel" id="id_guru_mapel" class="form-control custom-filter-select" required>
                                <option value="">-- Pilih Kelas Dahulu --</option>
                                <?php if($agenda): ?>
                                    <option value="<?= $agenda['id_guru_mapel'] ?>" selected><?= htmlspecialchars($agenda['nama_mapel']) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                <i class="fas fa-tag text-primary mr-1"></i> Jenis Penilaian Sumatif <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_sumatif" id="jenis_sumatif" class="form-control custom-filter-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php 
                                $jenis_list = ["Sumatif Lingkup Materi", "Sumatif Tengah Semester", "Sumatif Akhir Semester", "Sumatif Akhir Tahun", "Sumatif Akhir Jenjang"];
                                foreach($jenis_list as $j):
                                ?>
                                    <option value="<?= $j ?>" <?= $agenda && $agenda['jenis_sumatif'] == $j ? 'selected' : '' ?>><?= $j ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- KOLOM KANAN -->
                    <div class="col-md-6 col-12">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                <i class="fas fa-bullseye text-primary mr-1"></i> Capaian Pembelajaran (CP) <span class="text-danger">*</span>
                            </label>
                            <select id="id_cp" class="form-control select2" style="width: 100%;">
                                <option value="">-- Pilih Mapel Dahulu --</option>
                            </select>
                            <small class="text-info mt-1 d-block" id="cp_info" style="display:none; font-size: 0.75rem;">
                                <i class="fas fa-info-circle mr-1"></i> Khusus SAS/SAT/SAJ Anda dapat memilih lebih dari satu CP.
                            </small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                <i class="fas fa-heading text-primary mr-1"></i> Nama Penilaian <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_penilaian" class="form-control"
                                placeholder="Contoh: Sumatif Lingkup Materi 1 atau Sumatif Tengah Semester" required value="<?= $agenda ? htmlspecialchars($agenda['nama_penilaian']) : '' ?>">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                        <i class="far fa-calendar-alt text-primary mr-1"></i> Tanggal
                                    </label>
                                    <input type="date" name="tanggal_penilaian" class="form-control" value="<?= $agenda ? $agenda['tanggal_penilaian'] : date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted text-uppercase mb-1" style="font-size: 0.75rem;">
                                        <i class="fas fa-comment-alt text-primary mr-1"></i> Keterangan
                                    </label>
                                    <input type="text" name="keterangan" class="form-control" placeholder="Opsional..." value="<?= $agenda ? htmlspecialchars($agenda['keterangan']) : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TUJUAN PEMBELAJARAN (TP) CHECKLIST CONTAINER -->
                <div class="form-group mt-3 pt-3 border-top mb-4">
                    <label class="small font-weight-bold text-dark text-uppercase mb-2 d-flex align-items-center" style="font-size: 0.82rem;">
                        <i class="fas fa-check-square text-success mr-2"></i> Pilih Tujuan Pembelajaran (TP) yang Dicakup Penilaian Ini:
                    </label>
                    <div id="tp_container" class="row p-3 rounded"
                        style="max-height: 280px; overflow-y: auto; border: 1.5px solid #cbd5e1; background: #f8fafc;">
                        <p class="text-muted small mb-0">-- Silakan pilih CP terlebih dahulu untuk memuat daftar TP --</p>
                    </div>
                </div>

                <!-- SUBMIT BUTTONS -->
                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <a href="<?= BASE_URL ?>penilaian_sumatif" class="btn btn-secondary btn-sm rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none; padding: 8px 20px;">
                        <i class="fas fa-save mr-1"></i> Simpan Agenda Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kelasSelect = document.getElementById('id_kelas');
        const mapelSelect = document.getElementById('id_guru_mapel');
        const jenisSelect = document.getElementById('jenis_sumatif');
        const cpSelect = $('#id_cp');
        const tpContainer = document.getElementById('tp_container');
        const selectedTpIds = <?= json_encode($selected_tp_ids ?? []) ?>.map(String);

        // Initialize Select2
        cpSelect.select2({
            placeholder: "-- Pilih CP --",
            allowClear: true
        });

        function updateCpMode() {
            const jenis = jenisSelect.value;
            const isMulti = ["Sumatif Akhir Semester", "Sumatif Akhir Tahun", "Sumatif Akhir Jenjang"].includes(jenis);
            
            if (isMulti) {
                cpSelect.attr('multiple', 'multiple');
                document.getElementById('cp_info').style.display = 'block';
            } else {
                cpSelect.removeAttr('multiple');
                document.getElementById('cp_info').style.display = 'none';
            }
            
            cpSelect.select2('destroy').select2({
                placeholder: isMulti ? "-- Pilih Satu atau Lebih CP --" : "-- Pilih Satu CP --",
                allowClear: true
            });
        }

        function fetchMapel() {
            const idKelas = kelasSelect.value;
            if (!idKelas) {
                mapelSelect.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
                cpSelect.empty().append('<option value="">-- Pilih Mapel Dahulu --</option>').trigger('change');
                tpContainer.innerHTML = '<p class="text-muted small mb-0">-- Silakan pilih CP terlebih dahulu untuk memuat daftar TP --</p>';
                return;
            }

            mapelSelect.innerHTML = '<option value="">Memuat Mata Pelajaran...</option>';

            return fetch(`<?= BASE_URL ?>api.php?mod=sumatif&act=get_mapel_by_kelas&id_kelas=${idKelas}`)
                .then(response => response.json())
                .then(result => {
                    const currentVal = mapelSelect.value;
                    mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
                    if (result.status === 'ok' && result.data.length > 0) {
                        result.data.forEach(mapel => {
                            const option = new Option(mapel.nama_mapel, mapel.id_guru_mapel);
                            if(mapel.id_guru_mapel == currentVal) option.selected = true;
                            mapelSelect.appendChild(option);
                        });
                    } else {
                        mapelSelect.innerHTML = '<option value="">-- Tidak ada mapel diajar di kelas ini --</option>';
                    }
                    cpSelect.empty().append('<option value="">-- Pilih Mapel Dahulu --</option>').trigger('change');
                }).catch(err => {
                    console.error('Fetch mapel error:', err);
                    mapelSelect.innerHTML = '<option value="">-- Gagal memuat mapel --</option>';
                });
        }

        function fetchCp() {
            const idGuruMapel = mapelSelect.value;
            const idKelas = kelasSelect.value;
            if (!idGuruMapel) {
                cpSelect.empty().append('<option value="">-- Pilih Mapel Dahulu --</option>').trigger('change');
                return;
            }

            return fetch(`<?= BASE_URL ?>api.php?mod=sumatif&act=get_cp_by_mapel&id_guru_mapel=${idGuruMapel}&id_kelas=${idKelas}`)
                .then(response => response.json())
                .then(result => {
                    cpSelect.empty();
                    if (result.status === 'ok' && result.data.length > 0) {
                        result.data.forEach(cp => {
                            const option = new Option(cp.deskripsi_cp, cp.id_cp);
                            cpSelect.append(option);
                        });
                    } else {
                        cpSelect.append(new Option('-- Belum ada CP untuk Mapel/Fase ini --', ''));
                    }
                    cpSelect.trigger('change');
                }).catch(err => {
                    console.error('Fetch CP error:', err);
                });
        }

        function fetchTp() {
            const idGuruMapel = mapelSelect.value;
            const selectedCps = cpSelect.val();
            
            if (!selectedCps || (Array.isArray(selectedCps) && selectedCps.length === 0)) {
                tpContainer.innerHTML = '<p class="text-muted small mb-0">-- Pilih CP Dahulu --</p>';
                return;
            }

            const idCpParam = Array.isArray(selectedCps) ? selectedCps.join(',') : selectedCps;

            tpContainer.innerHTML = '<p class="text-muted small mb-0"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat daftar TP...</p>';

            fetch(`<?= BASE_URL ?>api.php?mod=sumatif&act=get_tp_by_mapel&id_guru_mapel=${idGuruMapel}&id_cp=${idCpParam}`)
                .then(response => response.json())
                .then(result => {
                    tpContainer.innerHTML = '';
                    if (result.status === 'ok' && result.data.length > 0) {
                        result.data.forEach(tp => {
                            const isChecked = selectedTpIds.includes(tp.id_tp.toString()) ? 'checked' : '';
                            const checkboxDiv = document.createElement('div');
                            checkboxDiv.className = 'col-md-6 col-12 mb-2';
                            checkboxDiv.innerHTML = `
                                <div class="tp-checkbox-item">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="selected_tps[]" value="${tp.id_tp}" id="tp_${tp.id_tp}" ${isChecked}>
                                        <label class="custom-control-label font-weight-normal small text-dark" for="tp_${tp.id_tp}" style="cursor:pointer; line-height: 1.4;">
                                            <span class="badge badge-primary mr-1" style="border-radius: 50px;">${tp.kode_tp}</span> ${tp.deskripsi_tp}
                                        </label>
                                    </div>
                                </div>
                            `;
                            tpContainer.appendChild(checkboxDiv);
                        });
                    } else {
                        tpContainer.innerHTML = '<p class="text-danger small font-italic p-2 mb-0"><i class="fas fa-exclamation-circle mr-1"></i> Tidak ada TP ditemukan untuk CP terpilih</p>';
                    }
                }).catch(err => {
                    console.error('Fetch TP error:', err);
                    tpContainer.innerHTML = '<p class="text-danger small font-italic p-2 mb-0">Gagal memuat TP.</p>';
                });
        }

        kelasSelect.addEventListener('change', fetchMapel);
        mapelSelect.addEventListener('change', fetchCp);
        jenisSelect.addEventListener('change', updateCpMode);
        cpSelect.on('change', fetchTp);

        // Init mode
        updateCpMode();

        <?php if($agenda): ?>
        fetchCp().then(() => {
            <?php if(isset($selected_cp_ids)): ?>
            cpSelect.val(<?= json_encode($selected_cp_ids) ?>).trigger('change');
            <?php endif; ?>
        });
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>