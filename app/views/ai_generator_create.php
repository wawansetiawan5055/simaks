<?php
// app/views/ai_generator_create.php
include __DIR__ . '/partials/header.php';
?>

<style>
/* ============================================================ */
/* WIZARD CONTAINER & PROGRESS BAR                               */
/* ============================================================ */
.wizard-wrapper {
    max-width: 1040px;
    margin: 0 auto;
    padding-bottom: 40px;
}

.wizard-progress-bar {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 0 20px;
}

.wizard-progress-track {
    position: absolute;
    top: 22px;
    left: 40px;
    right: 40px;
    height: 4px;
    background: #e2e8f0;
    z-index: 1;
    border-radius: 2px;
}

.wizard-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #2563eb, #3b82f6);
    border-radius: 2px;
    transition: width 0.4s ease;
    width: 0%;
}

.wizard-step-item {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    user-select: none;
}

.step-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 15px;
    color: #64748b;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.wizard-step-item.active .step-circle {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.2);
    transform: scale(1.08);
}

.wizard-step-item.completed .step-circle {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}

.step-label {
    margin-top: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-align: center;
    transition: color 0.3s;
}

.wizard-step-item.active .step-label {
    color: #2563eb;
    font-weight: 700;
}

.wizard-step-item.completed .step-label {
    color: #10b981;
}

/* ============================================================ */
/* WIZARD CARD & CONTENT                                         */
/* ============================================================ */
.wizard-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.wizard-card .card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 18px 24px;
}

.wizard-card .card-header h5 {
    margin: 0;
    font-weight: 700;
    font-size: 16px;
    color: #1e293b;
}

.wizard-card .card-body {
    padding: 24px;
}

.badge-step {
    background: #eff6ff;
    color: #2563eb;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #bfdbfe;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-label-custom {
    font-weight: 600;
    font-size: 13px;
    color: #334155;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.form-label-custom i {
    margin-right: 6px;
    color: #2563eb;
}

.form-control-custom {
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    transition: all 0.2s;
    background: #f8fafc;
}

.form-control-custom:focus {
    background: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

/* ============================================================ */
/* DOCUMENT TYPE RADIO CARDS                                     */
/* ============================================================ */
.doc-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.doc-type-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #f8fafc;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.doc-type-card:hover {
    border-color: #93c5fd;
    background: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
}

.doc-type-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 0 0 2px #2563eb;
}

.doc-type-card input[type="radio"] {
    margin-top: 4px;
}

.doc-type-info h6 {
    margin: 0 0 4px 0;
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
}

.doc-type-card.selected .doc-type-info h6 {
    color: #1d4ed8;
}

.doc-type-info p {
    margin: 0;
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.4;
}

/* ============================================================ */
/* QUICK SUGGESTION CHIPS                                        */
/* ============================================================ */
.chip-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
    margin-bottom: 12px;
}

.suggestion-chip {
    display: inline-flex;
    align-items: center;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 11.5px;
    color: #334155;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.suggestion-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
}

.suggestion-chip.active-chip {
    background: #dbeafe;
    border-color: #3b82f6;
    color: #1d4ed8;
    font-weight: 600;
}

.btn-ai-assist {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-ai-assist:hover {
    opacity: 0.92;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
}

/* ============================================================ */
/* SMART TP LIST                                                 */
/* ============================================================ */
.tp-item-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    transition: all 0.2s;
}

.tp-item-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.tp-item-card.checked {
    border-color: #3b82f6;
    background: #f0fdf4;
}

.tp-text-input {
    border: none;
    background: transparent;
    width: 100%;
    font-size: 13px;
    color: #1e293b;
    font-weight: 500;
    padding: 0;
}

.tp-text-input:focus {
    outline: none;
    background: #ffffff;
    box-shadow: 0 0 0 2px #93c5fd;
    border-radius: 4px;
}

/* ============================================================ */
/* MODEL PEMBELAJARAN GRID                                       */
/* ============================================================ */
.metode-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 10px;
}

.metode-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px;
    cursor: pointer;
    background: #f8fafc;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.metode-card:hover {
    background: #ffffff;
    border-color: #93c5fd;
}

.metode-card.selected {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 0 0 2px #2563eb;
}

/* ============================================================ */
/* WIZARD NAVIGATION BUTTONS                                     */
/* ============================================================ */
.wizard-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

.btn-wizard-next, .btn-wizard-generate {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    padding: 11px 24px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-wizard-next:hover, .btn-wizard-generate:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    color: #ffffff;
}

.btn-wizard-prev {
    background: #ffffff;
    color: #475569;
    font-weight: 600;
    font-size: 14px;
    padding: 10px 20px;
    border-radius: 10px;
    border: 1.5px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-wizard-prev:hover {
    background: #f1f5f9;
    color: #0f172a;
}

/* ============================================================ */
/* AI THINKING ANIMATION                                         */
/* ============================================================ */
.ai-thinking-box {
    text-align: center;
    padding: 50px 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
    border-radius: 16px;
    border: 2px dashed #93c5fd;
    margin-bottom: 24px;
}

.spinner-ring {
    display: inline-block;
    width: 56px;
    height: 56px;
    border: 5px solid rgba(37, 99, 235, 0.15);
    border-radius: 50%;
    border-top-color: #2563eb;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="m-0 font-weight-bold text-dark" style="font-size: 1.5rem;">
                    <i class="fas fa-magic text-primary mr-2"></i> Wizard Penulisan Perangkat Pembelajaran AI
                </h1>
                <p class="text-muted mb-0 small">Master Template Kurikulum Merdeka &amp; Deep Learning SMA Plus Al Manshuriyah</p>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="<?= BASE_URL ?>ai_generator" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold">
                    <i class="fas fa-list mr-1"></i> Riwayat Dokumen AI
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="wizard-wrapper">

            <!-- WIZARD PROGRESS STEPPER -->
            <div class="wizard-progress-bar">
                <div class="wizard-progress-track">
                    <div class="wizard-progress-fill" id="wizardProgress"></div>
                </div>

                <div class="wizard-step-item active" id="step-1" onclick="goToStep(1)">
                    <div class="step-circle"><i class="fas fa-book-open"></i></div>
                    <span class="step-label">1. Kurikulum &amp; TP</span>
                </div>

                <div class="wizard-step-item" id="step-2" onclick="goToStep(2)">
                    <div class="step-circle"><i class="fas fa-users"></i></div>
                    <span class="step-label">2. Profil Murid</span>
                </div>

                <div class="wizard-step-item" id="step-3" onclick="goToStep(3)">
                    <div class="step-circle"><i class="fas fa-cubes"></i></div>
                    <span class="step-label">3. Desain Pedagogi</span>
                </div>

                <div class="wizard-step-item" id="step-4" onclick="goToStep(4)">
                    <div class="step-circle"><i class="fas fa-file-signature"></i></div>
                    <span class="step-label">4. Hasil &amp; Editor</span>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- STEP 1: KURIKULUM, CAPAIAN & SMART TP                        -->
            <!-- ============================================================ -->
            <div class="wizard-panel" id="panel-1">
                <div class="wizard-card card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5><i class="fas fa-layer-group mr-2 text-primary"></i>Langkah 1: Format Dokumen &amp; Capaian Pembelajaran</h5>
                        <span class="badge-step">Kurikulum Merdeka</span>
                    </div>
                    <div class="card-body">

                        <!-- Pilihan Format Dokumen -->
                        <div class="form-group mb-4">
                            <label class="form-label-custom">
                                <span><i class="fas fa-file-alt"></i> Pilih Jenis Dokumen yang Dibuat</span>
                            </label>
                            <div class="doc-type-grid">
                                <label class="doc-type-card selected" data-value="Modul Ajar Deep Learning">
                                    <input type="radio" name="wizJenis" value="Modul Ajar Deep Learning" checked>
                                    <div class="doc-type-info">
                                        <h6>📘 Modul Ajar Deep Learning</h6>
                                        <p>Format Baku Lengkap: Identitas, Sintaks Per Pertemuan, Pengesahan, Lampiran LKPD &amp; Rubrik Asesmen 4 Skala.</p>
                                    </div>
                                </label>
                                <label class="doc-type-card" data-value="Alur Tujuan Pembelajaran (ATP)">
                                    <input type="radio" name="wizJenis" value="Alur Tujuan Pembelajaran (ATP)">
                                    <div class="doc-type-info">
                                        <h6>📊 Alur Tujuan Pembelajaran (ATP)</h6>
                                        <p>Tabel Alur TP, Alokasi JP, Dimensi Profil, &amp; Rekapitulasi Jam Efektif Semester.</p>
                                    </div>
                                </label>
                                <label class="doc-type-card" data-value="Lembar Kerja Peserta Didik (LKPD)">
                                    <input type="radio" name="wizJenis" value="Lembar Kerja Peserta Didik (LKPD)">
                                    <div class="doc-type-info">
                                        <h6>📝 Lembar Kerja Murid (LKPD)</h6>
                                        <p>Khusus LKPD Eksploratif: Stimulasi masalah nyata, tabel penyelidikan, &amp; soal latihan.</p>
                                    </div>
                                </label>
                                <label class="doc-type-card" data-value="Instrumen & Rubrik Asesmen">
                                    <input type="radio" name="wizJenis" value="Instrumen & Rubrik Asesmen">
                                    <div class="doc-type-info">
                                        <h6>📋 Instrumen &amp; Rubrik Asesmen</h6>
                                        <p>Diagnostik, Observasi Formatif, Kisi-kisi Sumatif, 5 Soal Kuis, Kunci &amp; Rubrik 4 Skala.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Mata Pelajaran -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-book"></i> Mata Pelajaran <span class="text-danger">*</span></span>
                                    </label>
                                    <select id="wizMapel" class="form-control-custom form-control select2" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        <?php foreach ($mapel_list as $m): ?>
                                            <option value="<?= $m['id_mapel'] ?>" data-nama="<?= htmlspecialchars($m['nama_mapel']) ?>">
                                                <?= htmlspecialchars($m['nama_mapel']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Kelas & Fase -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-graduation-cap"></i> Kelas <span class="text-danger">*</span></span>
                                    </label>
                                    <select id="wizKelas" class="form-control-custom form-control">
                                        <option value="X" data-fase="E" selected>Kelas X (Fase E)</option>
                                        <option value="XI" data-fase="F">Kelas XI (Fase F)</option>
                                        <option value="XII" data-fase="F">Kelas XII (Fase F)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Fase (Otomatis) -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-certificate"></i> Fase Kurikulum</span>
                                    </label>
                                    <input type="text" id="wizFaseDisplay" class="form-control-custom form-control font-weight-bold text-primary" value="Fase E" readonly>
                                    <input type="hidden" id="wizFase" value="E">
                                </div>
                            </div>
                        </div>

                        <!-- Capaian Pembelajaran (CP) -->
                        <div class="form-group mt-2">
                            <label class="form-label-custom">
                                <span><i class="fas fa-bullseye"></i> Capaian Pembelajaran (CP) Elemen</span>
                            </label>
                            <select id="wizCpSelect" class="form-control-custom form-control mb-2">
                                <option value="">-- Pilih dari Bank CP SIMAKS (atau ketik langsung di bawah) --</option>
                            </select>
                            <textarea id="wizCpDeskripsi" class="form-control-custom form-control" rows="3" 
                                      placeholder="Pilih CP di atas atau tuliskan Capaian Pembelajaran elemen materi di sini..."></textarea>
                        </div>

                        <!-- Smart TP Section -->
                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                <label class="form-label-custom mb-0">
                                    <span><i class="fas fa-tasks"></i> Tujuan Pembelajaran (TP) yang Dipilih</span>
                                </label>
                                <div class="d-flex gap-2 mt-1 mt-md-0">
                                    <button type="button" class="btn-ai-assist mr-1" id="btnGenerateTpAi">
                                        <i class="fas fa-magic"></i> ✨ Rumuskan TP Spesifik (AI)
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-xs font-weight-bold" id="btnAddManualTp" style="border-radius: 8px;">
                                        <i class="fas fa-plus"></i> Tambah TP
                                    </button>
                                </div>
                            </div>

                            <div id="tpContainer" style="max-height: 250px; overflow-y: auto; padding-right: 4px;">
                                <div class="text-muted small py-2 text-center" id="tpEmptyHint">
                                    <i class="fas fa-info-circle mr-1"></i> Pilih CP di atas, klik <strong>✨ Rumuskan TP Spesifik (AI)</strong>, atau tambahkan TP manual.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="wizard-nav">
                    <div></div>
                    <button type="button" class="btn-wizard-next" id="btnStep1Next">
                        Lanjut ke Profil Murid <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- STEP 2: PROFIL & KESIAPAN BELAJAR MURID                      -->
            <!-- ============================================================ -->
            <div class="wizard-panel" id="panel-2" style="display:none;">
                <div class="wizard-card card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5><i class="fas fa-users mr-2 text-success"></i>Langkah 2: Profil &amp; Kesiapan Murid (Diferensiasi)</h5>
                        <span class="badge-step">Diagnostik Awal</span>
                    </div>
                    <div class="card-body">

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label-custom mb-0">
                                    <span><i class="fas fa-user-check"></i> Deskripsi Kesiapan &amp; Kebutuhan Belajar Murid</span>
                                </label>
                                <button type="button" class="btn-ai-assist" id="btnGenerateProfilAi">
                                    <i class="fas fa-magic"></i> ✨ Isi Profil Otomatis oleh AI
                                </button>
                            </div>
                            
                            <!-- Pilihan Cepat (Suggestion Chips) -->
                            <div class="chip-container">
                                <span class="suggestion-chip" data-text="Kemampuan murid heterogen: sebagian sudah memahami konsep dasar di SMP, sebagian memerlukan penguatan konsep awal.">
                                    + Heterogen (Campuran Mahir &amp; Penguatan)
                                </span>
                                <span class="suggestion-chip" data-text="Gaya belajar dominan visual dan kinestetik: optimal dengan media tabel, diagram pola, serta aktivitas diskusi kelompok aktif.">
                                    + Dominan Visual &amp; Diskusi Kelompok
                                </span>
                                <span class="suggestion-chip" data-text="Murid memiliki minat tinggi terhadap pemanfaatan gawai, kalkulator saintifik, dan aplikasi belajar interaktif.">
                                    + Tertarik Pemanfaatan Teknologi / Gawai
                                </span>
                                <span class="suggestion-chip" data-text="Murid antusias menghubungkan materi pelajaran dengan pemecahan masalah kontekstual di kehidupan sehari-hari.">
                                    + Antusias Studi Kasus Nyata
                                </span>
                            </div>

                            <textarea id="wizKesiapan" class="form-control-custom form-control" rows="4"
                                      placeholder="Klik salah satu opsi cepat di atas, klik '✨ Isi Profil Otomatis oleh AI', atau tuliskan kondisi riil murid Anda di sini..."></textarea>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle mr-1"></i> AI akan menggunakan data ini untuk merancang diferensiasi proses, konten, dan rubrik asesmen yang tepat sasaran.
                            </small>
                        </div>

                    </div>
                </div>

                <div class="wizard-nav">
                    <button type="button" class="btn-wizard-prev" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                    <button type="button" class="btn-wizard-next" id="btnStep2Next">
                        Lanjut ke Desain Pedagogi <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- STEP 3: TOPIK KONTEKSTUAL, ALOKASI & METODE                  -->
            <!-- ============================================================ -->
            <div class="wizard-panel" id="panel-3" style="display:none;">
                <div class="wizard-card card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5><i class="fas fa-cubes mr-2 text-warning"></i>Langkah 3: Topik Kontekstual &amp; Model Pembelajaran</h5>
                        <span class="badge-step">Desain Pedagogi</span>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <!-- Topik / Bab Pembelajaran -->
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-bookmark"></i> Judul Bab / Topik Materi <span class="text-danger">*</span></span>
                                    </label>
                                    <input type="text" id="wizTopik" class="form-control-custom form-control font-weight-bold" 
                                           placeholder="Contoh: Bilangan Berpangkat (Eksponen)" required>
                                    
                                    <!-- Contoh Topik Cepat -->
                                    <div class="chip-container mt-2">
                                        <span class="suggestion-chip topic-chip" data-text="Bilangan Berpangkat (Eksponen)">Eksponen</span>
                                        <span class="suggestion-chip topic-chip" data-text="Sistem Persamaan Linear Tiga Variabel">SPLTV</span>
                                        <span class="suggestion-chip topic-chip" data-text="Trigonometri Dasar">Trigonometri</span>
                                        <span class="suggestion-chip topic-chip" data-text="Statistika & Penyajian Data">Statistika</span>
                                        <span class="suggestion-chip topic-chip" data-text="Vektor dan Operasinya">Vektor</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Alokasi Waktu -->
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-clock"></i> Alokasi Waktu <span class="text-danger">*</span></span>
                                    </label>
                                    <input type="text" id="wizAlokasi" class="form-control-custom form-control" 
                                           value="12 JP (6 Pertemuan x 2 JP @ 45 Menit)">
                                    
                                    <div class="chip-container mt-2">
                                        <span class="suggestion-chip alokasi-chip active-chip" data-text="12 JP (6 Pertemuan x 2 JP @ 45 Menit)">12 JP (6 Pertemuan)</span>
                                        <span class="suggestion-chip alokasi-chip" data-text="6 JP (3 Pertemuan x 2 JP @ 45 Menit)">6 JP (3 Pertemuan)</span>
                                        <span class="suggestion-chip alokasi-chip" data-text="4 JP (2 Pertemuan x 2 JP @ 45 Menit)">4 JP (2 Pertemuan)</span>
                                        <span class="suggestion-chip alokasi-chip" data-text="2 JP (1 Pertemuan @ 90 Menit)">2 JP (1 Pertemuan)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Model & Metode Pembelajaran -->
                        <div class="form-group mt-3">
                            <label class="form-label-custom mb-2">
                                <span><i class="fas fa-cogs"></i> Model Pembelajaran Berbasis Deep Learning <span class="text-danger">*</span></span>
                            </label>

                            <div class="metode-grid">
                                <label class="metode-card selected" data-value="Discovery Learning">
                                    <input type="radio" name="wizMetode" value="Discovery Learning" checked>
                                    <div>
                                        <strong class="d-block text-dark" style="font-size:13px;">Discovery Learning</strong>
                                        <small class="text-muted" style="font-size:11px;">Stimulasi, Identifikasi Masalah, Data, Verifikasi &amp; Simpulan.</small>
                                    </div>
                                </label>

                                <label class="metode-card" data-value="Problem Based Learning (PBL)">
                                    <input type="radio" name="wizMetode" value="Problem Based Learning (PBL)">
                                    <div>
                                        <strong class="d-block text-dark" style="font-size:13px;">Problem Based Learning</strong>
                                        <small class="text-muted" style="font-size:11px;">Orientasi Masalah Nyata, Penyelidikan &amp; Solusi.</small>
                                    </div>
                                </label>

                                <label class="metode-card" data-value="Project Based Learning (PjBL)">
                                    <input type="radio" name="wizMetode" value="Project Based Learning (PjBL)">
                                    <div>
                                        <strong class="d-block text-dark" style="font-size:13px;">Project Based Learning</strong>
                                        <small class="text-muted" style="font-size:11px;">Perancangan Proyek Kolaboratif &amp; Karya Nyata.</small>
                                    </div>
                                </label>

                                <label class="metode-card" data-value="Inquiry Learning">
                                    <input type="radio" name="wizMetode" value="Inquiry Learning">
                                    <div>
                                        <strong class="d-block text-dark" style="font-size:13px;">Inquiry Learning</strong>
                                        <small class="text-muted" style="font-size:11px;">Eksplorasi Mandiri, Uji Hipotesis &amp; Refleksi.</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="wizard-nav">
                    <button type="button" class="btn-wizard-prev" onclick="goToStep(2)">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                    <button type="button" class="btn-wizard-generate" id="btnGenerate">
                        <i class="fas fa-brain"></i> Generate Dokumen dengan AI
                    </button>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- STEP 4: HASIL GENERATE & EDITOR (SUMMERNOTE)                 -->
            <!-- ============================================================ -->
            <div class="wizard-panel" id="panel-4" style="display:none;">

                <!-- Loading State -->
                <div id="aiThinkingBox" class="ai-thinking-box" style="display:none;">
                    <div class="spinner-ring"></div>
                    <h5 class="font-weight-bold text-dark mb-2">AI Sedang Menyusun Dokumen Master...</h5>
                    <p class="text-muted mb-0" style="font-size: 13.5px;">
                        Pakar Kurikulum Deep Learning sedang menyusun Cover, Identifikasi Kesiapan Murid,<br>
                        Desain Ber-Sintaks Per Pertemuan, Pengesahan, Lampiran LKPD, dan Rubrik 4 Skala.<br>
                        <strong>Proses ini membutuhkan waktu sekitar 20 - 45 detik.</strong>
                    </p>
                    <div class="mt-3">
                        <span class="badge badge-light text-primary border px-3 py-2 font-weight-bold shadow-sm" id="thinkingStatus" style="border-radius: 8px;">
                            <i class="fas fa-cog fa-spin mr-1"></i> Merancang struktur dan tabel...
                        </span>
                    </div>
                </div>

                <!-- Preview Editor Card -->
                <div id="previewContent" style="display:none;">
                    <form action="<?= BASE_URL ?>ai_generator/save" method="post" id="formSaveDoc">
                        <input type="hidden" name="jenis" id="saveJenis">
                        <input type="hidden" name="mapel" id="saveMapel">
                        <input type="hidden" name="kelas" id="saveKelas">
                        <input type="hidden" name="fase" id="saveFase">
                        <input type="hidden" name="topik" id="saveTopik">

                        <div class="wizard-card card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <h5><i class="fas fa-file-signature mr-2 text-success"></i>Pratinjau &amp; Penyempurnaan Dokumen</h5>
                                    <small class="text-muted">Periksa dan sesuaikan dokumen sebelum disimpan ke bank perangkat Anda</small>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-3" style="border-radius: 8px;">
                                        <i class="fas fa-save mr-1"></i> Simpan ke Perangkat Pembelajaran
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-heading"></i> Judul Dokumen <span class="text-danger">*</span></span>
                                    </label>
                                    <input type="text" name="judul" id="docJudul" class="form-control-custom form-control font-weight-bold text-dark" style="font-size: 1.1rem;" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label-custom">
                                        <span><i class="fas fa-file-code"></i> Isi Dokumen Lengkap</span>
                                    </label>
                                    <textarea id="aiContent" name="konten_html" class="summernote"></textarea>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                                <button type="button" class="btn btn-outline-secondary" onclick="goToStep(3)">
                                    <i class="fas fa-redo mr-1"></i> Atur Ulang Parameter
                                </button>
                                <button type="submit" class="btn btn-success font-weight-bold px-4" style="border-radius: 8px;">
                                    <i class="fas fa-save mr-1"></i> Simpan Dokumen
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- Summernote & Scripts -->
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/AdminLTE/plugins/summernote/summernote-bs4.min.css">
<script src="<?= BASE_URL ?>public/assets/AdminLTE/plugins/summernote/summernote-bs4.min.js"></script>

<script>
var currentStep = 1;
var selectedMapelId = 0;
var selectedMapelNama = '';
var tpCounter = 0;

$(document).ready(function() {

    // Document Type Card Selector
    $('.doc-type-card').on('click', function() {
        $('.doc-type-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Metode Card Selector
    $('.metode-card').on('click', function() {
        $('.metode-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Kelas / Fase Sync
    $('#wizKelas').on('change', function() {
        var fase = $(this).find(':selected').data('fase') || 'E';
        $('#wizFase').val(fase);
        $('#wizFaseDisplay').val('Fase ' + fase);
        loadCpList();
    });

    // Mapel Change
    $('#wizMapel').on('change', function() {
        selectedMapelId = $(this).val();
        selectedMapelNama = $(this).find(':selected').data('nama') || '';
        loadCpList();
    });

    // CP Select Change
    $('#wizCpSelect').on('change', function() {
        var id_cp = $(this).val();
        var deskripsi = $(this).find(':selected').data('deskripsi') || '';
        $('#wizCpDeskripsi').val(deskripsi);
        if (id_cp) {
            loadTpList(id_cp);
        }
    });

    // Suggestion Chips Click (Kesiapan Murid)
    $('.suggestion-chip:not(.topic-chip):not(.alokasi-chip)').on('click', function() {
        var text = $(this).data('text');
        var cur = $('#wizKesiapan').val().trim();
        $(this).toggleClass('active-chip');
        if (cur === '') {
            $('#wizKesiapan').val(text);
        } else if (cur.indexOf(text) === -1) {
            $('#wizKesiapan').val(cur + "\n\n" + text);
        }
    });

    // Topic Chips Click
    $('.topic-chip').on('click', function() {
        var topic = $(this).data('text');
        $('#wizTopik').val(topic);
        $('.topic-chip').removeClass('active-chip');
        $(this).addClass('active-chip');
    });

    // Alokasi Chips Click
    $('.alokasi-chip').on('click', function() {
        var alokasi = $(this).data('text');
        $('#wizAlokasi').val(alokasi);
        $('.alokasi-chip').removeClass('active-chip');
        $(this).addClass('active-chip');
    });

    // Add Manual TP Button
    $('#btnAddManualTp').on('click', function() {
        addTpItem('', true);
    });

    // Generate TP via AI
    $('#btnGenerateTpAi').on('click', function() {
        var mapel = selectedMapelNama || $('#wizMapel').find(':selected').text().trim();
        var fase = $('#wizFase').val();
        var topik = $('#wizTopik').val().trim();
        var cpDeskripsi = $('#wizCpDeskripsi').val().trim();

        if (!mapel || mapel.indexOf('--') !== -1) {
            Swal.fire({ icon: 'warning', title: 'Pilih Mapel', text: 'Pilih Mata Pelajaran terlebih dahulu.', confirmButtonColor: '#2563eb' });
            return;
        }

        if (!topik) {
            Swal.fire({
                title: 'Ketik Topik / Bab',
                text: 'Masukkan topik materi untuk merumuskan TP spesifik (misal: Eksponen, SPLTV, dll):',
                input: 'text',
                inputValue: 'Bilangan Berpangkat (Eksponen)',
                showCancelButton: true,
                confirmButtonText: 'Rumuskan TP',
                confirmButtonColor: '#2563eb'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $('#wizTopik').val(result.value);
                    requestGenerateTp(mapel, fase, result.value, cpDeskripsi);
                }
            });
            return;
        }

        requestGenerateTp(mapel, fase, topik, cpDeskripsi);
    });

    // Generate Profil Murid via AI
    $('#btnGenerateProfilAi').on('click', function() {
        var mapel = selectedMapelNama || $('#wizMapel').find(':selected').text().trim();
        var kelas = $('#wizKelas').val();
        var fase = $('#wizFase').val();
        var topik = $('#wizTopik').val().trim() || 'Pembelajaran Tematik';

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Merumuskan...');

        $.ajax({
            url: '<?= BASE_URL ?>index.php?mod=ai_generator&act=generate_profil',
            type: 'POST',
            data: { mapel: mapel, kelas: kelas, fase: fase, topik: topik },
            dataType: 'json',
            timeout: 120000,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-magic"></i> ✨ Isi Profil Otomatis oleh AI');
                if (res.success && res.profil) {
                    $('#wizKesiapan').val(res.profil);
                    Swal.fire({ icon: 'success', title: 'Profil Terisi!', text: 'Profil kesiapan murid berhasil dibuat.', timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal merumuskan profil.' });
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-magic"></i> ✨ Isi Profil Otomatis oleh AI');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan jaringan.' });
            }
        });
    });

    // Step 1 Next
    $('#btnStep1Next').on('click', function() {
        if (!selectedMapelId) {
            Swal.fire({ icon: 'warning', title: 'Pilih Mapel', text: 'Harap pilih mata pelajaran terlebih dahulu.', confirmButtonColor: '#2563eb' });
            return;
        }
        goToStep(2);
    });

    // Step 2 Next
    $('#btnStep2Next').on('click', function() {
        var kesiapan = $('#wizKesiapan').val().trim();
        if (!kesiapan) {
            // Auto fill default if empty
            $('#wizKesiapan').val('Kemampuan murid heterogen dengan gaya belajar visual, auditori, dan kinestetik yang aktif.');
        }
        goToStep(3);
    });

    // Generate Button Click
    $('#btnGenerate').on('click', function() {
        var topik = $('#wizTopik').val().trim();
        if (!topik) {
            Swal.fire({ icon: 'warning', title: 'Topik Kosong', text: 'Harap isi Judul Bab / Topik Pembelajaran terlebih dahulu.', confirmButtonColor: '#2563eb' });
            return;
        }

        goToStep(4);
        $('#aiThinkingBox').show();
        $('#previewContent').hide();

        var jenis = $('input[name="wizJenis"]:checked').val() || 'Modul Ajar Deep Learning';
        var mapel = selectedMapelNama || $('#wizMapel').find(':selected').text().trim();
        var kelas = $('#wizKelas').val();
        var fase = $('#wizFase').val();
        var alokasi = $('#wizAlokasi').val();
        var metode = $('input[name="wizMetode"]:checked').val() || 'Discovery Learning';
        var kesiapan = $('#wizKesiapan').val().trim();
        var cpDeskripsi = $('#wizCpDeskripsi').val().trim();

        // Kumpulkan TP terpilih
        var tpList = [];
        $('.tp-checkbox:checked').each(function() {
            var text = $(this).closest('.tp-item-card').find('.tp-text-input').val().trim();
            if (text) tpList.push(text);
        });

        // Set Hidden Fields for Save Form
        $('#saveJenis').val(jenis);
        $('#saveMapel').val(mapel);
        $('#saveKelas').val(kelas);
        $('#saveFase').val(fase);
        $('#saveTopik').val(topik);
        $('#docJudul').val(jenis + ' ' + mapel + ' Kelas ' + kelas + ' - ' + topik);

        // Status updater messages
        var statuses = [
            'Menganalisis Capaian & Tujuan Pembelajaran...',
            'Menyusun Identifikasi Deep Learning & Diferensiasi...',
            'Merancang Sintaks Pembelajaran Per Pertemuan...',
            'Menyusun Pengesahan & Lampiran LKPD Interaktif...',
            'Menyusun Kisi-kisi Asesmen & Rubrik 4 Skala...',
            'Menyempurnakan tata letak dokumen...'
        ];
        var sIdx = 0;
        var statusTimer = setInterval(function() {
            sIdx = (sIdx + 1) % statuses.length;
            $('#thinkingStatus').html('<i class="fas fa-cog fa-spin mr-1"></i> ' + statuses[sIdx]);
        }, 4000);

        $.ajax({
            url: '<?= BASE_URL ?>index.php?mod=ai_generator&act=process',
            type: 'POST',
            data: {
                jenis: jenis,
                mapel: mapel,
                kelas: kelas,
                fase: fase,
                topik: topik,
                alokasi_waktu: alokasi,
                metode: metode,
                kesiapan_murid: kesiapan,
                cp_deskripsi: cpDeskripsi,
                tp_list: tpList
            },
            dataType: 'json',
            timeout: 120000,
            success: function(res) {
                clearInterval(statusTimer);
                $('#aiThinkingBox').hide();

                if (res.success && res.text) {
                    $('#previewContent').show();
                    initSummernote();
                    $('#aiContent').summernote('code', res.text);
                    Swal.fire({ icon: 'success', title: 'Dokumen Berhasil Dibuat!', text: 'Dokumen lengkap siap ditinjau dan disimpan.', timer: 2000, showConfirmButton: false });
                } else {
                    $('#previewContent').hide();
                    goToStep(3);
                    Swal.fire({ icon: 'error', title: 'Gagal Membuat Dokumen', text: res.message || 'Terjadi kesalahan pada AI server.' });
                }
            },
            error: function(xhr, status, error) {
                clearInterval(statusTimer);
                $('#aiThinkingBox').hide();
                goToStep(3);
                Swal.fire({ icon: 'error', title: 'Koneksi Terputus', text: 'Permintaan timeout atau gagal terhubung. Silakan coba kembali.' });
            }
        });
    });

    // Form Save Validation
    $('#formSaveDoc').on('submit', function(e) {
        var judul = $('#docJudul').val().trim();
        var konten = $('#aiContent').summernote('code');
        if (!judul) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Judul Kosong', text: 'Harap isi judul dokumen sebelum menyimpan.', confirmButtonColor: '#2563eb' });
            return;
        }
        if (!konten || konten.trim() === '<p><br></p>') {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Konten Kosong', text: 'Konten dokumen tidak boleh kosong.', confirmButtonColor: '#2563eb' });
            return;
        }
    });

});

function goToStep(step) {
    $('.wizard-panel').hide();
    $('#panel-' + step).show();

    for (var i = 1; i <= 4; i++) {
        var s = $('#step-' + i);
        s.removeClass('active completed');
        if (i < step) {
            s.addClass('completed').find('.step-circle').html('<i class="fas fa-check"></i>');
        } else if (i === step) {
            s.addClass('active');
        } else {
            var icons = { 1: 'fa-book-open', 2: 'fa-users', 3: 'fa-cubes', 4: 'fa-file-signature' };
            s.find('.step-circle').html('<i class="fas ' + icons[i] + '"></i>');
        }
    }

    var progressMap = { 1: '0%', 2: '33%', 3: '66%', 4: '100%' };
    $('#wizardProgress').css('width', progressMap[step] || '0%');
    currentStep = step;
    $('html, body').animate({ scrollTop: 0 }, 250);
}

function loadCpList() {
    if (!selectedMapelId) return;
    var fase = $('#wizFase').val();

    $('#wizCpSelect').html('<option value="">Memuat daftar CP...</option>');
    $.getJSON('<?= BASE_URL ?>index.php?mod=ai_generator&act=get_cp&id_mapel=' + selectedMapelId + '&fase=' + fase, function(res) {
        if (res.success && res.data && res.data.length > 0) {
            var opt = '<option value="">-- Pilih Capaian Pembelajaran (CP) --</option>';
            res.data.forEach(function(cp) {
                var ringkasan = cp.deskripsi_cp.length > 90 ? cp.deskripsi_cp.substring(0, 90) + '...' : cp.deskripsi_cp;
                opt += '<option value="' + cp.id_cp + '" data-deskripsi="' + htmlEntities(cp.deskripsi_cp) + '">' + htmlEntities((cp.elemen ? '[' + cp.elemen + '] ' : '') + ringkasan) + '</option>';
            });
            $('#wizCpSelect').html(opt);
        } else {
            $('#wizCpSelect').html('<option value="">-- Belum ada CP tersimpan (Ketik manual di bawah) --</option>');
        }
    });
}

function loadTpList(id_cp) {
    $.getJSON('<?= BASE_URL ?>index.php?mod=ai_generator&act=get_tp&id_cp=' + id_cp, function(res) {
        $('#tpContainer').empty();
        if (res.success && res.data && res.data.length > 0) {
            res.data.forEach(function(tp) {
                addTpItem(tp.deskripsi_tp, true);
            });
        } else {
            $('#tpContainer').html('<div class="text-muted small py-2 text-center" id="tpEmptyHint"><i class="fas fa-info-circle mr-1"></i> Belum ada TP tersimpan untuk CP ini. Klik tombol <strong>✨ Rumuskan TP Spesifik (AI)</strong> untuk membuat otomatis!</div>');
        }
    });
}

function requestGenerateTp(mapel, fase, topik, cpDeskripsi) {
    var btn = $('#btnGenerateTpAi');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Merumuskan...');

    $.ajax({
        url: '<?= BASE_URL ?>index.php?mod=ai_generator&act=generate_tp',
        type: 'POST',
        data: { mapel: mapel, fase: fase, topik: topik, cp_deskripsi: cpDeskripsi },
        dataType: 'json',
        timeout: 120000,
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-magic"></i> ✨ Rumuskan TP Spesifik (AI)');
            if (res.success && res.tp_list && res.tp_list.length > 0) {
                $('#tpContainer').empty();
                res.tp_list.forEach(function(tpText) {
                    addTpItem(tpText, true);
                });
                Swal.fire({ icon: 'success', title: 'TP Berhasil Dirumuskan!', text: res.tp_list.length + ' Tujuan Pembelajaran spesifik berhasil dibuat.', timer: 1800, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal merumuskan TP.' });
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fas fa-magic"></i> ✨ Rumuskan TP Spesifik (AI)');
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan jaringan saat merumuskan TP.' });
        }
    });
}

function addTpItem(text, isChecked) {
    $('#tpEmptyHint').remove();
    tpCounter++;
    var checkedAttr = isChecked ? 'checked' : '';
    var cardClass = isChecked ? 'tp-item-card checked' : 'tp-item-card';

    var html = `
        <div class="${cardClass}" id="tp_card_${tpCounter}">
            <input type="checkbox" class="tp-checkbox mt-1" ${checkedAttr} onchange="toggleTpCard(${tpCounter})">
            <div style="flex:1;">
                <input type="text" class="tp-text-input" value="${htmlEntities(text)}" placeholder="Ketik Tujuan Pembelajaran di sini...">
            </div>
            <button type="button" class="btn btn-link text-danger p-0 ml-1" onclick="$('#tp_card_${tpCounter}').remove()" title="Hapus TP">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    $('#tpContainer').append(html);
}

function toggleTpCard(id) {
    var card = $('#tp_card_' + id);
    if (card.find('.tp-checkbox').is(':checked')) {
        card.addClass('checked');
    } else {
        card.removeClass('checked');
    }
}

var summernoteInitialized = false;
function initSummernote() {
    if (summernoteInitialized) return;
    $('#aiContent').summernote({
        height: 650,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        placeholder: 'Hasil dokumen AI akan ditampilkan di sini...'
    });
    summernoteInitialized = true;
}

function htmlEntities(str) {
    return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
