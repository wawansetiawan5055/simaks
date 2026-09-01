<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    .lms-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #ffffff; }
    .nav-pills .nav-link { border-radius: 10px; color: #64748b; font-weight: 600; padding: 12px 20px; transition: all 0.3s; margin-bottom: 5px; }
    .nav-pills .nav-link.active { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
    .nav-pills .nav-link i { margin-right: 10px; }
    .form-control-lms { border-radius: 10px; padding: 12px 15px; border: 1px solid #e2e8f0; }
    .form-control-lms:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
    .btn-lms-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border: none; color: #fff; border-radius: 10px; font-weight: 600; }
    .section-title { font-size: 0.9rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
    .btn-ai-sparkle { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff !important; border: none; font-weight: 600; transition: all 0.25s ease; }
    .btn-ai-sparkle:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.35); }
</style>

<?php
$init_id_mapel   = $_GET['id_mapel'] ?? '';
$init_id_bab     = $_GET['id_bab'] ?? '';
$init_id_sub_bab = $_GET['id_sub_bab'] ?? '';
$init_tingkat    = $_GET['tingkat'] ?? 'X';
?>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Upload &amp; Pembuatan Modul Ajar
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>lms/materi_list" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="<?= BASE_URL ?>lms/materi_upload" method="POST" enctype="multipart/form-data" id="formMateri">
            <div class="row">
                <!-- Sidebar Tabs -->
                <div class="col-lg-3">
                    <div class="card lms-card p-3 sticky-top" style="top: 20px; z-index: 100;">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="tab-identitas-tab" data-toggle="pill" href="#tab-identitas" role="tab"><i class="fas fa-id-card"></i> 1. Identitas, CP, TP</a>
                            <a class="nav-link" id="tab-diagnostik-tab" data-toggle="pill" href="#tab-diagnostik" role="tab"><i class="fas fa-stethoscope"></i> 2. Langkah & Diagnostik</a>
                            <a class="nav-link" id="tab-video-tab" data-toggle="pill" href="#tab-video" role="tab"><i class="fab fa-youtube"></i> 3. Materi Video & Q</a>
                            <a class="nav-link" id="tab-materi-tab" data-toggle="pill" href="#tab-materi" role="tab"><i class="fas fa-book-open"></i> 4. Materi Tertulis & Q</a>
                            <a class="nav-link" id="tab-formatif-tab" data-toggle="pill" href="#tab-formatif" role="tab"><i class="fas fa-tasks"></i> 5. Tes Formatif</a>
                            <a class="nav-link" id="tab-refleksi-tab" data-toggle="pill" href="#tab-refleksi" role="tab"><i class="fas fa-comments"></i> 6. Refleksi</a>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-lms-primary btn-block p-3 mt-2 shadow">
                            <i class="fas fa-save mr-2"></i> Publikasikan Modul
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="col-lg-9">
                    <div class="card lms-card">
                        <div class="card-body p-4">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger border-0 shadow-sm"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <div class="tab-content" id="v-pills-tabContent">
                                <!-- TAB 1: Identitas & Instruksi -->
                                <div class="tab-pane fade show active" id="tab-identitas" role="tabpanel">
                                    <h5 class="section-title">Informasi Dasar & Kurikulum</h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <label class="small font-weight-bold">JUDUL MODUL / TOPIK MATERI <span class="text-danger">*</span></label>
                                            <input type="text" name="judul_materi" id="judul_materi" class="form-control form-control-lms font-weight-bold" placeholder="Contoh: Sistem Persamaan Linear Tiga Variabel (SPLTV)" required>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold">MATA PELAJARAN <span class="text-danger">*</span></label>
                                            <select name="id_mapel" id="id_mapel" class="form-control form-control-lms select2" required>
                                                <option value="">Pilih Mata Pelajaran</option>
                                                <?php foreach ($mapel_list as $m): ?>
                                                    <option value="<?php echo $m['id_mapel']; ?>" data-nama="<?php echo htmlspecialchars($m['nama_mapel']); ?>" <?= ($init_id_mapel == $m['id_mapel']) ? 'selected' : '' ?>><?php echo htmlspecialchars($m['nama_mapel']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <label class="small font-weight-bold">TINGKAT (GRADE)</label>
                                            <select name="tingkat" id="tingkat" class="form-control form-control-lms" required>
                                                <option value="X" <?= ($init_tingkat == 'X') ? 'selected' : '' ?>>Kelas X</option>
                                                <option value="XI" <?= ($init_tingkat == 'XI') ? 'selected' : '' ?>>Kelas XI</option>
                                                <option value="XII" <?= ($init_tingkat == 'XII') ? 'selected' : '' ?>>Kelas XII</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <label class="small font-weight-bold">SEMESTER</label>
                                            <select name="semester" id="semester" class="form-control form-control-lms" onchange="loadBabList()">
                                                <option value="1">Semester 1 (Ganjil)</option>
                                                <option value="2">Semester 2 (Genap)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold"><i class="fas fa-folder text-primary mr-1"></i> BAB / UNIT BUKU (DAFTAR ISI)</label>
                                            <select name="id_bab" id="id_bab" class="form-control form-control-lms" onchange="loadSubBabList()">
                                                <option value="">-- Tanpa Bab / Materi Mandiri --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold"><i class="fas fa-bookmark text-info mr-1"></i> SUB-BAB</label>
                                            <select name="id_sub_bab" id="id_sub_bab" class="form-control form-control-lms">
                                                <option value="">-- Tanpa Sub-Bab --</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <h6 class="font-weight-bold text-muted small mb-3">CAPAIAN & TUJUAN PEMBELAJARAN</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold">PILIH CP</label>
                                            <select name="id_cp" id="id_cp" class="form-control form-control-lms">
                                                <option value="">-- Pilih CP --</option>
                                            </select>
                                            <div class="mt-2">
                                                <label class="small text-muted">Ketik CP manual (opsional):</label>
                                                <textarea name="cp_manual" id="cp_manual" class="form-control form-control-lms" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold">PILIH TP</label>
                                            <div id="tp_container" class="bg-light p-3 rounded border" style="max-height: 150px; overflow-y: auto;">
                                                <p class="text-muted small mb-0">Pilih CP terlebih dahulu.</p>
                                            </div>
                                            <div class="mt-2">
                                                <label class="small text-muted">Ketik TP manual (opsional):</label>
                                                <textarea name="tp_manual" id="tp_manual" class="form-control form-control-lms" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right mt-5">
                                        <button type="button" class="btn btn-primary px-4 py-2 shadow-sm" onclick="$('#tab-diagnostik-tab').tab('show')">Lanjutkan ke Tahap 2 <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <!-- TAB 2: Langkah & Diagnostik -->
                                <div class="tab-pane fade" id="tab-diagnostik" role="tabpanel">
                                    <h5 class="section-title">Langkah Pembelajaran & Tes Diagnostik</h5>
                                    <div class="mb-4">
                                        <label class="small font-weight-bold">INSTRUKSI / LANGKAH PEMBELAJARAN</label>
                                        
                                        <!-- Template Section -->
                                        <div class="mb-3 p-3 bg-light rounded border">
                                            <label class="small text-muted font-weight-bold mb-2 d-block"><i class="fas fa-magic mr-1"></i> GUNAKAN TEMPLATE LANGKAH (PILIH):</label>
                                            <div class="row">
                                                <?php 
                                                $step_templates = [
                                                    "Berdoa sebelum belajar",
                                                    "Kerjakan test diagnostik",
                                                    "Pelajari materi melalui video",
                                                    "Rangkum dan catat terkait materi yang penting",
                                                    "Download Pdf jika diperlukan",
                                                    "Jawab Pertanyaan dengan teliti",
                                                    "Kerjakan Soal latihan dengan sungguh-sungguh",
                                                    "Isi refleksi dengan jujur",
                                                    "Ucapkan hamdalah jika selesai"
                                                ];
                                                foreach($step_templates as $idx => $step): ?>
                                                <div class="col-md-4 mb-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input step-template-check" id="step_tmp_<?php echo $idx; ?>" value="<?php echo $step; ?>">
                                                        <label class="custom-control-label small font-weight-normal" for="step_tmp_<?php echo $idx; ?>"><?php echo $step; ?></label>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button" class="btn btn-xs btn-info mt-2 px-3 rounded-pill" onclick="applyStepTemplate()">
                                                <i class="fas fa-plus-circle mr-1"></i> Terapkan ke Editor
                                            </button>
                                        </div>

                                        <textarea name="instruksi" id="instruksi_editor" class="form-control form-control-lms summernote" rows="5" placeholder="Tuliskan urutan langkah belajar untuk siswa..."></textarea>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="font-weight-bold text-muted small mb-0">DAFTAR PERTANYAAN DIAGNOSTIK (OPSIONAL)</h6>
                                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill" onclick="addQuestion('diagnostik')">
                                            <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                        </button>
                                    </div>
                                    <div id="diagnostik_container">
                                        <!-- Dynamic inputs -->
                                    </div>

                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="$('#tab-identitas-tab').tab('show')"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                                        <button type="button" class="btn btn-primary px-4 py-2" onclick="$('#tab-video-tab').tab('show')">Lanjutkan ke Tahap 3 <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <!-- TAB 3: Materi Video & Q -->
                                <div class="tab-pane fade" id="tab-video" role="tabpanel">
                                    <h5 class="section-title">Materi Berbasis Video</h5>
                                    <div class="mb-4">
                                        <label class="small font-weight-bold">URL VIDEO (YOUTUBE)</label>
                                        <input type="url" name="video_url" class="form-control form-control-lms" placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="font-weight-bold text-muted small mb-0">PERTANYAAN KHUSUS VIDEO (ESSAY)</h6>
                                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill" onclick="addQuestion('video')">
                                            <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                        </button>
                                    </div>
                                    <div id="video_questions_container">
                                        <!-- Dynamic inputs -->
                                    </div>

                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="$('#tab-diagnostik-tab').tab('show')"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                                        <button type="button" class="btn btn-primary px-4 py-2" onclick="$('#tab-materi-tab').tab('show')">Lanjutkan ke Tahap 4 <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <!-- TAB 4: Materi Tertulis & Q -->
                                <div class="tab-pane fade" id="tab-materi" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                        <h5 class="section-title mb-0">Materi Tertulis (Teks / Artikel)</h5>
                                        <button type="button" class="btn btn-sm btn-ai-sparkle rounded-pill px-3 shadow-sm" id="btnAiGenerateMateri">
                                            <i class="fas fa-magic mr-1"></i> ✨ Susun Bahan Ajar Lengkap (AI)
                                        </button>
                                    </div>

                                    <div class="mb-4">
                                        <label class="small font-weight-bold">ISI MATERI (MENDUKUNG TEKS, GAMBAR &amp; RUMUS LATEX $\dots$)</label>
                                        <textarea name="deskripsi" id="deskripsi_materi" class="form-control form-control-lms summernote" rows="12"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold">UPLOAD FILE PDF / PPT</label>
                                            <input type="file" name="file_materi" class="form-control-file">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="small font-weight-bold">LINK EKSTERNAL</label>
                                            <input type="url" name="external_url" class="form-control form-control-lms" placeholder="https://...">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="font-weight-bold text-muted small mb-0">PERTANYAAN TERKAIT MATERI TERTULIS (ESSAY)</h6>
                                        <button type="button" class="btn btn-xs btn-outline-success rounded-pill" onclick="addQuestion('materi')">
                                            <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                        </button>
                                    </div>
                                    <div id="materi_questions_container">
                                        <!-- Dynamic inputs -->
                                    </div>

                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="$('#tab-video-tab').tab('show')"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                                        <button type="button" class="btn btn-primary px-4 py-2" onclick="$('#tab-formatif-tab').tab('show')">Lanjutkan ke Tahap 5 <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <!-- TAB 5: Tes Formatif -->
                                <div class="tab-pane fade" id="tab-formatif" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                        <div>
                                            <h5 class="section-title mb-0">Bank Soal (Tes Formatif / Kuis)</h5>
                                            <small class="text-muted">Mendukung rumus LaTeX matematika: gunakan $...$ untuk rumus sebaris</small>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-ai-sparkle rounded-pill px-3 shadow-sm mr-1" id="btnAiGenerateSoal">
                                                <i class="fas fa-robot mr-1"></i> ✨ Generate Soal Otomatis (AI)
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddSoal">
                                                <i class="fas fa-plus mr-1"></i> Tambah Butir Manual
                                            </button>
                                        </div>
                                    </div>
                                    <div id="soal_container">
                                        <!-- Dynamic Questions will appear here -->
                                    </div>

                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="$('#tab-materi-tab').tab('show')"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                                        <button type="button" class="btn btn-primary px-4 py-2" onclick="$('#tab-refleksi-tab').tab('show')">Lanjutkan ke Tahap 6 <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <!-- TAB 6: Refleksi -->
                                <div class="tab-pane fade" id="tab-refleksi" role="tabpanel">
                                    <h5 class="section-title">Refleksi Pembelajaran</h5>
                                    <label class="small font-weight-bold mb-3">PILIH TEMPLATE PERTANYAAN REFLEKSI SISWA</label>
                                    <div class="bg-light p-3 rounded border">
                                        <?php 
                                        $reflections = [
                                            "Apakah materi hari ini mudah dipahami?",
                                            "Bagian mana yang paling menarik dari materi ini?",
                                            "Apa kesulitan terbesar Anda dalam mempelajari materi ini?",
                                            "Apakah instruksi yang diberikan sudah cukup jelas?",
                                            "Apa yang ingin Anda pelajari lebih lanjut terkait topik ini?"
                                        ];
                                        foreach ($reflections as $idx => $ref): ?>
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" name="refleksi[]" value="<?php echo $ref; ?>" class="custom-control-input" id="ref_<?php echo $idx; ?>" checked>
                                                <label class="custom-control-label font-weight-normal" for="ref_<?php echo $idx; ?>"><?php echo $ref; ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="mt-3">
                                            <label class="small text-muted">Tambah Pertanyaan Kustom:</label>
                                            <input type="text" name="refleksi_custom" class="form-control form-control-sm" placeholder="Ketik pertanyaan lain...">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-5">
                                        <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="$('#tab-formatif-tab').tab('show')"><i class="fas fa-arrow-left mr-2"></i> Kembali</button>
                                        <button type="submit" class="btn btn-success px-5 py-2 font-weight-bold shadow-sm"><i class="fas fa-paper-plane mr-2"></i> Selesai &amp; Simpan Modul</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Modal Parameter AI Soal -->
<div class="modal fade" id="modalAiSoal" tabindex="-1" role="dialog" aria-labelledby="modalAiSoalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #4f46e5, #3730a3) !important;">
                <h5 class="modal-title font-weight-bold" id="modalAiSoalLabel">
                    <i class="fas fa-magic mr-2 text-warning"></i> Wizard AI Generator Soal Interaktif
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group mb-3">
                    <label class="small font-weight-bold text-dark text-uppercase">FOKUS TOPIK / SUB-MATERI</label>
                    <input type="text" id="aiFokusTopik" class="form-control" placeholder="Contoh: Sifat Eksponen Pangkat Pecahan dan Bentuk Akar">
                    <small class="text-muted">Biarkan kosong jika ingin mencakup seluruh judul modul materi.</small>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">BENTUK SOAL</label>
                        <select id="aiTipeSoal" class="form-control form-control-sm">
                            <option value="PG" selected>Pilihan Ganda (PG A-E)</option>
                            <option value="Essay">Essay / Uraian</option>
                            <option value="matching">Menjodohkan (Matching)</option>
                            <option value="tf">Benar / Salah (True/False)</option>
                            <option value="Campuran">Campuran (PG + Essay)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">TINGKAT KESULITAN</label>
                        <select id="aiTingkatKesulitan" class="form-control form-control-sm">
                            <option value="mudah">Mudah (C1-C2: Pemahaman Dasar)</option>
                            <option value="sedang" selected>Sedang (C3: Penerapan / Aplikasi)</option>
                            <option value="sulit">Sulit / HOTS (C4-C6: Analisis & Nalar)</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">JUMLAH BUTIR SOAL</label>
                        <select id="aiJumlahSoal" class="form-control form-control-sm">
                            <option value="5" selected>5 Butir Soal</option>
                            <option value="10">10 Butir Soal</option>
                            <option value="15">15 Butir Soal</option>
                            <option value="20">20 Butir Soal</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">JENIS ASESMEN / EVALUASI</label>
                        <select id="aiKategoriSoal" class="form-control form-control-sm">
                            <option value="Formatif" selected>Tes Formatif (Evaluasi Akhir Modul)</option>
                            <option value="Pretest">Pretest (Uji Kemampuan Awal)</option>
                            <option value="Diagnostik">Tes Diagnostik (Kesiapan &amp; Prasyarat)</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-light border small text-muted mb-0">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Soal otomatis dilengkapi kunci jawaban akurat dan rumus matematika rapi berformat <strong>MathJax LaTeX ($ ... $)</strong>.
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm" id="btnSubmitAiSoal" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                    <i class="fas fa-bolt mr-1"></i> Mulai Generate Soal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let soalIndex = 0;

    // AJAX: Load Bab based on Mapel & Tingkat
    window.loadBabList = function() {
        const idMapel = $('#id_mapel').val();
        const tingkat = $('#tingkat').val();
        if (!idMapel) {
            $('#id_bab').html('<option value="">-- Tanpa Bab / Materi Mandiri --</option>');
            $('#id_sub_bab').html('<option value="">-- Tanpa Sub-Bab --</option>');
            return;
        }

        $.getJSON(`<?= BASE_URL ?>index.php?mod=lms&act=get_bab_ajax&id_mapel=${idMapel}&tingkat=${tingkat}`, function(res) {
            let html = '<option value="">-- Tanpa Bab / Materi Mandiri --</option>';
            if (res.status === 'ok' && res.data && res.data.length > 0) {
                res.data.forEach(b => {
                    html += `<option value="${b.id_bab}">[Semester ${b.semester}] Bab ${b.urutan_bab}: ${b.judul_bab}</option>`;
                });
            }
            $('#id_bab').html(html);
            $('#id_sub_bab').html('<option value="">-- Tanpa Sub-Bab --</option>');
        });
    };

    // AJAX: Load Sub-Bab based on Bab
    window.loadSubBabList = function() {
        const idBab = $('#id_bab').val();
        if (!idBab) {
            $('#id_sub_bab').html('<option value="">-- Tanpa Sub-Bab --</option>');
            return;
        }

        $.getJSON(`<?= BASE_URL ?>index.php?mod=lms&act=get_sub_bab_ajax&id_bab=${idBab}`, function(res) {
            let html = '<option value="">-- Tanpa Sub-Bab --</option>';
            if (res.status === 'ok' && res.data && res.data.length > 0) {
                res.data.forEach(s => {
                    html += `<option value="${s.id_sub_bab}">Sub-Bab ${s.urutan_sub}: ${s.judul_sub_bab}</option>`;
                });
            }
            $('#id_sub_bab').html(html);
        });
    };

    $('#id_mapel, #tingkat, #semester').on('change', function() {
        loadBabList();
        loadCP();
    });

    // Inisialisasi otomatis dari parameter URL jika ada
    const initIdBab = '<?= $init_id_bab ?>';
    const initIdSubBab = '<?= $init_id_sub_bab ?>';
    
    if ($('#id_mapel').val()) {
        loadCP();
        const curMapel = $('#id_mapel').val();
        const curTingkat = $('#tingkat').val();
        const curSem = $('#semester').val() || 1;
        $.getJSON(`<?= BASE_URL ?>index.php?mod=lms&act=get_bab_ajax&id_mapel=${curMapel}&tingkat=${curTingkat}&semester=${curSem}`, function(res) {
            let html = '<option value="">-- Tanpa Bab / Materi Mandiri --</option>';
            if (res.status === 'ok' && res.data && res.data.length > 0) {
                res.data.forEach(b => {
                    let sel = (initIdBab == b.id_bab) ? 'selected' : '';
                    html += `<option value="${b.id_bab}" ${sel}>[Semester ${b.semester}] Bab ${b.urutan_bab}: ${b.judul_bab}</option>`;
                });
            }
            $('#id_bab').html(html);

            if (initIdBab) {
                $.getJSON(`<?= BASE_URL ?>index.php?mod=lms&act=get_sub_bab_ajax&id_bab=${initIdBab}`, function(res2) {
                    let html2 = '<option value="">-- Tanpa Sub-Bab --</option>';
                    if (res2.status === 'ok' && res2.data && res2.data.length > 0) {
                        res2.data.forEach(s => {
                            let sel2 = (initIdSubBab == s.id_sub_bab) ? 'selected' : '';
                            html2 += `<option value="${s.id_sub_bab}" ${sel2}>Sub-Bab ${s.urutan_sub}: ${s.judul_sub_bab}</option>`;
                        });
                    }
                    $('#id_sub_bab').html(html2);
                });
            }
        });
    }

    // AJAX: Load CP based on Mapel & Tingkat
    function loadCP() {
        const idMapel = $('#id_mapel').val();
        const tingkat = $('#tingkat').val();
        if (!idMapel) return;

        $.ajax({
            url: '<?= BASE_URL ?>index.php?mod=lms&act=get_cp_ajax',
            type: 'GET',
            data: { id_mapel: idMapel, tingkat: tingkat },
            dataType: 'json',
            success: function(data) {
                let html = '<option value="">-- Pilih CP --</option>';
                data.forEach(cp => {
                    let desc = (cp.deskripsi_cp.length > 80) ? cp.deskripsi_cp.substring(0, 80) + '...' : cp.deskripsi_cp;
                    html += `<option value="${cp.id_cp}" data-desc="${cp.deskripsi_cp}">${cp.fase} - ${desc}</option>`;
                });
                $('#id_cp').html(html);
                $('#tp_container').html('<p class="text-muted small mb-0">Pilih CP terlebih dahulu.</p>');
            }
        });
    }

    // AJAX: Load TP based on CP
    $('#id_cp').change(function() {
        const idCP = $(this).val();
        if (!idCP) {
            $('#tp_container').html('<p class="text-muted small mb-0">Pilih CP terlebih dahulu.</p>');
            return;
        }

        $.ajax({
            url: '<?= BASE_URL ?>index.php?mod=lms&act=get_tp_ajax',
            type: 'GET',
            data: { id_cp: idCP },
            dataType: 'json',
            success: function(data) {
                let html = '';
                if (data.length === 0) {
                    html = '<p class="text-danger small mb-0">Tidak ada TP untuk CP ini.</p>';
                } else {
                    data.forEach(tp => {
                        html += `
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input tp-checkbox-item" id="tp_${tp.id_tp}" name="id_tp[]" value="${tp.id_tp}" data-desc="${tp.deskripsi_tp}">
                                <label class="custom-control-label small text-dark" for="tp_${tp.id_tp}">
                                    <strong>${tp.kode_tp}</strong>: ${tp.deskripsi_tp}
                                </label>
                            </div>
                        `;
                    });
                }
                $('#tp_container').html(html);
            }
        });
    });

    // Step Template Helper (Terapkan ke Editor)
    window.applyStepTemplate = function() {
        let steps = [];
        $('.step-template-check:checked').each(function() {
            steps.push($(this).val());
        });
        
        if (steps.length === 0) {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Pilih Template', 
                text: 'Silakan centang minimal satu langkah pembelajaran template di atas.', 
                confirmButtonColor: '#6366f1' 
            });
            return;
        }
        
        let html = '<ol>';
        steps.forEach(s => html += '<li>' + s + '</li>');
        html += '</ol>';
        
        try {
            let current = $('#instruksi_editor').summernote('code');
            if (current && current !== '<p><br></p>' && current.trim() !== '') {
                $('#instruksi_editor').summernote('code', current + html);
            } else {
                $('#instruksi_editor').summernote('code', html);
            }
        } catch(e) {
            $('#instruksi_editor').val(html);
        }

        Swal.fire({
            icon: 'success',
            title: 'Berhasil Diterapkan!',
            text: 'Langkah pembelajaran template telah disisipkan ke dalam editor.',
            timer: 1500,
            showConfirmButton: false
        });
    };

    // Question Helpers for Diagnostik, Video, Materi
    window.addQuestion = function(type, val = '') {
        const container = $(`#${type}_questions_container`).length ? $(`#${type}_questions_container`) : $(`#${type}_container`);
        const name = type === 'diagnostik' ? 'tes_diagnostik_config[]' : `${type}_questions[]`;
        const html = `
            <div class="input-group mb-2">
                <input type="text" name="${name}" class="form-control form-control-sm" placeholder="Ketik pertanyaan di sini..." value="${val}" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="$(this).closest('.input-group').remove()"><i class="fas fa-times"></i></button>
                </div>
            </div>`;
        container.append(html);
    };

    // Add Soal Formativ / Latihan
    window.addSoalToContainer = function(qData = null) {
        const soal = qData || {};
        const tipe = soal.tipe || 'PG';
        let kategori = soal.kategori_soal || 'Formatif';
        if (kategori === 'Latihan') kategori = 'Formatif';
        const pertanyaan = soal.pertanyaan || '';
        const opsiA = soal.opsi_a || '';
        const opsiB = soal.opsi_b || '';
        const opsiC = soal.opsi_c || '';
        const opsiD = soal.opsi_d || '';
        const opsiE = soal.opsi_e || '';
        const kunci = soal.kunci_jawaban || 'A';

        const template = `
            <div class="card bg-light border mb-3 soal-item shadow-sm" id="soal_${soalIndex}" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold text-dark"><i class="fas fa-question-circle text-primary mr-1"></i> Soal #${soalIndex + 1}</span>
                    <div>
                        <select name="soal_data[${soalIndex}][tipe]" class="form-control form-control-sm d-inline-block w-auto mr-2" onchange="window.toggleTipeSoal(${soalIndex}, this.value)">
                            <option value="PG" ${tipe === 'PG' ? 'selected' : ''}>Pilihan Ganda</option>
                            <option value="Essay" ${tipe === 'Essay' ? 'selected' : ''}>Essay</option>
                        </select>
                        <select name="soal_data[${soalIndex}][kategori_soal]" class="form-control form-control-sm d-inline-block w-auto mr-2">
                            <option value="Formatif" ${kategori === 'Formatif' ? 'selected' : ''}>Tes Formatif</option>
                            <option value="Pretest" ${kategori === 'Pretest' ? 'selected' : ''}>Pretest</option>
                            <option value="Diagnostik" ${kategori === 'Diagnostik' ? 'selected' : ''}>Tes Diagnostik</option>
                        </select>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="window.removeSoal(${soalIndex})"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="form-group mb-3">
                        <textarea name="soal_data[${soalIndex}][pertanyaan]" class="form-control form-control-sm" rows="2" placeholder="Tuliskan butir pertanyaan di sini..." required>${pertanyaan}</textarea>
                    </div>
                    
                    <div class="options-container" id="options_${soalIndex}" style="${tipe === 'Essay' ? 'display: none;' : ''}">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">A</span></div>
                                    <input type="text" name="soal_data[${soalIndex}][opsi_a]" class="form-control" value="${opsiA}" placeholder="Opsi A">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">B</span></div>
                                    <input type="text" name="soal_data[${soalIndex}][opsi_b]" class="form-control" value="${opsiB}" placeholder="Opsi B">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">C</span></div>
                                    <input type="text" name="soal_data[${soalIndex}][opsi_c]" class="form-control" value="${opsiC}" placeholder="Opsi C">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">D</span></div>
                                    <input type="text" name="soal_data[${soalIndex}][opsi_d]" class="form-control" value="${opsiD}" placeholder="Opsi D">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">E</span></div>
                                    <input type="text" name="soal_data[${soalIndex}][opsi_e]" class="form-control" value="${opsiE}" placeholder="Opsi E">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text bg-success text-white font-weight-bold">Kunci</span></div>
                                    <select name="soal_data[${soalIndex}][kunci_jawaban]" class="form-control">
                                        <option value="A" ${kunci === 'A' ? 'selected' : ''}>A</option>
                                        <option value="B" ${kunci === 'B' ? 'selected' : ''}>B</option>
                                        <option value="C" ${kunci === 'C' ? 'selected' : ''}>C</option>
                                        <option value="D" ${kunci === 'D' ? 'selected' : ''}>D</option>
                                        <option value="E" ${kunci === 'E' ? 'selected' : ''}>E</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#soal_container').append(template);
        soalIndex++;
        if (window.renderMath) window.renderMath();
    };

    $('#btnAddSoal').click(function() {
        window.addSoalToContainer();
    });

    window.toggleTipeSoal = (idx, val) => $('#options_' + idx).toggle(val === 'PG');
    window.removeSoal = (idx) => $('#soal_' + idx).remove();

    // ============================================================
    // ✨ AI GENERATOR: KONTEN BAHAN AJAR (SUMMERNOTE)
    // ============================================================
    $('#btnAiGenerateMateri').on('click', function() {
        const judul = $('#judul_materi').val().trim();
        const mapelNama = $('#id_mapel option:selected').data('nama') || $('#id_mapel option:selected').text().trim();
        const tingkat = $('#tingkat').val();
        const cpDeskripsi = $('#id_cp option:selected').data('desc') || $('#cp_manual').val().trim();

        let tpList = [];
        $('.tp-checkbox-item:checked').each(function() {
            tpList.push($(this).data('desc'));
        });
        const tpDeskripsi = tpList.join('; ') || $('#tp_manual').val().trim();

        if (!judul) {
            Swal.fire({
                icon: 'warning',
                title: 'Judul Topik Kosong',
                text: 'Harap isi Judul Modul / Topik Materi terlebih dahulu pada Tab 1.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> AI Sedang Menyusun Bahan Ajar...');

        $.ajax({
            url: '<?= BASE_URL ?>index.php?mod=lms&act=ai_generate_materi',
            type: 'POST',
            data: {
                mapel_nama: mapelNama,
                tingkat: tingkat,
                topik: judul,
                cp_deskripsi: cpDeskripsi,
                tp_deskripsi: tpDeskripsi
            },
            dataType: 'json',
            timeout: 90000,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> ✨ Susun Bahan Ajar Lengkap (AI)');
                if (res.success && res.html) {
                    $('#deskripsi_materi').summernote('code', res.html);
                    if (window.renderMath) window.renderMath();
                    Swal.fire({
                        icon: 'success',
                        title: 'Bahan Ajar Berhasil Disusun!',
                        text: 'Materi lengkap beserta rumus dan contoh soal berhasil dibuat oleh AI.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menyusun materi.' });
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> ✨ Susun Bahan Ajar Lengkap (AI)');
                Swal.fire({ icon: 'error', title: 'Koneksi Error', text: 'Terjadi kesalahan jaringan atau waktu habis.' });
            }
        });
    });

    // ============================================================
    // ✨ AI GENERATOR: BUTIR SOAL INTERAKTIF (DENGAN INTERVENSI KHUSUS)
    // ============================================================
    $('#btnAiGenerateSoal').on('click', function() {
        const judul = $('#judul_materi').val().trim();
        if (judul) {
            $('#aiFokusTopik').val(judul);
        }
        $('#modalAiSoal').modal('show');
    });

    $('#btnSubmitAiSoal').on('click', function() {
        const judul = $('#judul_materi').val().trim();
        const fokusTopik = $('#aiFokusTopik').val().trim() || judul;
        let mapelNama = $('#id_mapel option:selected').data('nama') || $('#id_mapel option:selected').text().trim();
        if (mapelNama.includes('--') || mapelNama === 'Pilih Mata Pelajaran') mapelNama = '';
        const tingkat = $('#tingkat').val() || 'X';
        const jumlah = $('#aiJumlahSoal').val() || 5;
        const tipe = $('#aiTipeSoal').val() || 'PG';
        const kesulitan = $('#aiTingkatKesulitan').val() || 'sedang';
        const kategori = $('#aiKategoriSoal').val() || 'Formatif';

        if (!fokusTopik) {
            Swal.fire({
                icon: 'warning',
                title: 'Fokus Topik Kosong',
                text: 'Harap isi topik materi atau kisi-kisi soal yang ingin dibuat.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> AI Sedang Merumuskan Soal...');

        $.ajax({
            url: '<?= BASE_URL ?>index.php?mod=lms&act=ai_generate_soal',
            type: 'POST',
            data: {
                mapel_nama: mapelNama,
                tingkat: tingkat,
                topik: fokusTopik,
                jumlah: jumlah,
                tipe: tipe,
                tingkat_kesulitan: kesulitan,
                kategori: kategori,
                target: 'materi'
            },
            dataType: 'json',
            timeout: 90000,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Mulai Generate Soal');
                $('#modalAiSoal').modal('hide');

                if (res.success && res.questions && res.questions.length > 0) {
                    res.questions.forEach(q => {
                        window.addSoalToContainer(q);
                    });
                    
                    // Pastikan tab formatif aktif dan scroll ke soal
                    $('#tab-formatif-tab').tab('show');
                    try { if (window.renderMath) window.renderMath(); } catch(e) {}
                    
                    setTimeout(() => {
                        $('html, body').animate({
                            scrollTop: $('#soal_container').offset().top - 100
                        }, 500);
                    }, 300);

                    Swal.fire({
                        icon: 'success',
                        title: 'Soal Berhasil Dibuat!',
                        text: res.questions.length + ' butir soal otomatis berhasil ditambahkan ke lembar tes formatif.',
                        timer: 2500,
                        showConfirmButton: true
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menghasilkan butir soal dari AI.' });
                }
            },
            error: function(xhr, status, err) {
                btn.prop('disabled', false).html('<i class="fas fa-bolt mr-1"></i> Mulai Generate Soal');
                $('#modalAiSoal').modal('hide');
                Swal.fire({ icon: 'error', title: 'Koneksi Error', text: 'Terjadi kesalahan jaringan atau waktu habis: ' + (err || status) });
            }
        });
    });

});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
