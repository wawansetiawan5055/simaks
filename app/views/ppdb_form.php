<?php include __DIR__ . '/partials/header.php'; ?>

<style>
/* =====================================================
   PPDB FORM + SCAN DOKUMEN STYLES
   ===================================================== */

/* --- Scan Dock (floating card di atas form) --- */
.scan-dock {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(79,70,229,0.25);
    position: relative;
    overflow: hidden;
}
.scan-dock::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}
.scan-dock-title {
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 4px 0;
}
.scan-dock-sub {
    color: rgba(255,255,255,0.7);
    font-size: 0.82rem;
    margin: 0;
}

/* --- Doc Type Buttons --- */
.doc-type-btn {
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 10px;
    color: white;
    padding: 10px 14px;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    min-width: 90px;
    user-select: none;
}
.doc-type-btn:hover, .doc-type-btn.active {
    background: rgba(255,255,255,0.22);
    border-color: #a5f3fc;
    color: #a5f3fc;
    transform: translateY(-2px);
}
.doc-type-btn i { font-size: 1.4rem; }

/* --- Scan Trigger Buttons --- */
.btn-scan-cam {
    background: linear-gradient(135deg,#10b981,#059669);
    border: none;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    padding: 10px 20px;
    cursor: pointer;
    transition: all .2s;
}
.btn-scan-cam:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,185,129,.4); }
.btn-scan-file {
    background: rgba(255,255,255,.12);
    border: 2px solid rgba(255,255,255,.3);
    border-radius: 10px;
    color: white;
    font-weight: 600;
    padding: 9px 20px;
    cursor: pointer;
    transition: all .2s;
}
.btn-scan-file:hover { background: rgba(255,255,255,.2); }

/* --- Modal Scan --- */
#modalScanDokumen .modal-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(0,0,0,.3);
}
#modalScanDokumen .modal-header {
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    color: white;
    border: none;
    padding: 18px 24px;
}
#modalScanDokumen .modal-header .close { color: white; opacity: .8; }

/* --- Camera Preview --- */
#cameraContainer {
    background: #0f172a;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
}
#videoFeed {
    width: 100%;
    max-height: 320px;
    object-fit: cover;
    border-radius: 12px;
    display: none;
}
#capturedCanvas {
    width: 100%;
    max-height: 320px;
    object-fit: contain;
    border-radius: 12px;
    display: none;
}
#imgPreview {
    max-width: 100%;
    max-height: 320px;
    object-fit: contain;
    border-radius: 12px;
    display: none;
}
.cam-placeholder {
    color: rgba(255,255,255,.3);
    text-align: center;
    padding: 40px;
}
.cam-placeholder i { font-size: 3rem; display: block; margin-bottom: 8px; }

/* Corner scan guides */
.scan-guides {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    pointer-events: none;
    display: none;
}
.scan-guides.active { display: block; }
.scan-guide-corner {
    position: absolute;
    width: 24px; height: 24px;
    border-color: #a5f3fc;
    border-style: solid;
}
.scan-guide-corner.tl { top:10px; left:10px; border-width: 3px 0 0 3px; }
.scan-guide-corner.tr { top:10px; right:10px; border-width: 3px 3px 0 0; }
.scan-guide-corner.bl { bottom:10px; left:10px; border-width: 0 0 3px 3px; }
.scan-guide-corner.br { bottom:10px; right:10px; border-width: 0 3px 3px 0; }

/* Scanning animation */
.scan-line {
    position: absolute;
    left: 10px; right: 10px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #a5f3fc, transparent);
    animation: scanMove 1.5s ease-in-out infinite;
    display: none;
}
.scan-line.active { display: block; }
@keyframes scanMove {
    0% { top: 10%; }
    50% { top: 85%; }
    100% { top: 10%; }
}

/* --- OCR Progress --- */
#ocrProgressWrap {
    display: none;
    margin-top: 12px;
}
.ocr-progress-bar {
    height: 6px;
    border-radius: 3px;
    background: #e2e8f0;
    overflow: hidden;
}
.ocr-progress-fill {
    height: 100%;
    background: linear-gradient(90deg,#4f46e5,#a78bfa);
    border-radius: 3px;
    width: 0%;
    transition: width .3s;
}
.ocr-status-text { font-size: 0.8rem; color: #64748b; margin-top: 4px; text-align: center; }

/* --- Results Panel --- */
#resultsPanel {
    display: none;
    margin-top: 12px;
}
.result-field-row {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 6px;
    background: #f8fafc;
    gap: 10px;
    font-size: 0.85rem;
    border: 1px solid #e2e8f0;
    transition: background .2s;
}
.result-field-row.detected {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.result-field-row .field-icon { width: 28px; text-align: center; }
.result-field-row .field-label { font-weight: 600; color: #475569; min-width: 130px; }
.result-field-row .field-value { flex: 1; color: #1e293b; font-weight: 500; }
.result-field-row .field-check { color: #10b981; }
.result-field-row.not-detected { opacity: .5; }

/* --- Form Card Improvements --- */
.form-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
}
.form-card .card-header {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    border-radius: 14px 14px 0 0 !important;
    padding: 16px 24px;
}
.form-card .card-header h3 {
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    font-size: 1rem;
}
.form-card .card-body { padding: 24px; }
.form-card .card-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 14px 14px !important;
    padding: 16px 24px;
}
.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,.12);
}
.form-label-custom {
    font-weight: 600;
    font-size: 0.85rem;
    color: #374151;
    margin-bottom: 6px;
}
/* Highlight saat field di-isi otomatis */
.auto-filled {
    animation: flashFill 1.5s ease-out;
    border-color: #10b981 !important;
}
@keyframes flashFill {
    0%   { background: #d1fae5; }
    100% { background: white; }
}

/* Raw text area */
#rawOcrText {
    font-size: 0.75rem;
    font-family: monospace;
    max-height: 140px;
    overflow-y: auto;
    background: #0f172a;
    color: #94a3b8;
    border: none;
    border-radius: 8px;
    padding: 10px;
    resize: none;
    display: none;
}
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-plus mr-2"></i> Formulir Pendaftaran Siswa Baru (PPDB)</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>ppdb">PPDB</a></li>
                    <li class="breadcrumb-item active">Formulir Pendaftaran</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <!-- ============================================================
             SCAN DOKUMEN DOCK
             ============================================================ -->
        <div class="scan-dock">
            <div class="row align-items-center">
                <div class="col-md-5 mb-3 mb-md-0">
                    <p class="scan-dock-title"><i class="fas fa-magic mr-2"></i> Scan Dokumen — Isi Formulir Otomatis</p>
                    <p class="scan-dock-sub">Pilih jenis dokumen, lalu foto/upload — data akan otomatis masuk ke form</p>
                </div>
                <div class="col-md-7">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end" id="docTypeButtons" style="gap:8px;">
                        <button class="doc-type-btn active" data-type="ktp" onclick="selectDocType(this,'ktp')">
                            <i class="fas fa-id-card"></i> KTP/NIK
                        </button>
                        <button class="doc-type-btn" data-type="kk" onclick="selectDocType(this,'kk')">
                            <i class="fas fa-home"></i> Kartu Keluarga
                        </button>
                        <button class="doc-type-btn" data-type="ijazah" onclick="selectDocType(this,'ijazah')">
                            <i class="fas fa-scroll"></i> Ijazah/SKHUN
                        </button>
                        <button class="doc-type-btn" data-type="akte" onclick="selectDocType(this,'akte')">
                            <i class="fas fa-baby"></i> Akte Lahir
                        </button>
                        <button class="btn-scan-cam mr-2" onclick="openScanModal('camera')">
                            <i class="fas fa-camera mr-2"></i> Buka Kamera
                        </button>
                        <label class="btn-scan-file mb-0" style="cursor:pointer;" onclick="openScanModal('file')">
                            <i class="fas fa-file-image mr-2"></i> Upload Foto
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             MODAL SCAN DOKUMEN
             ============================================================ -->
        <div class="modal fade" id="modalScanDokumen" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0" id="scanModalTitle">
                                <i class="fas fa-magic mr-2"></i> Scan Dokumen OCR
                            </h5>
                            <small style="opacity:.7" id="scanModalSub">Arahkan kamera ke dokumen dengan pencahayaan yang baik</small>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" onclick="stopCamera()">
                            <span style="color:white;">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- LEFT: Camera / Image Preview -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div id="cameraContainer">
                                    <div class="cam-placeholder" id="camPlaceholder">
                                        <i class="fas fa-camera-retro"></i>
                                        <span>Kamera / Foto belum aktif</span>
                                    </div>
                                    <video id="videoFeed" autoplay playsinline></video>
                                    <canvas id="capturedCanvas" style="display:none;"></canvas>
                                    <img id="imgPreview" alt="Preview" />
                                    <!-- Scan guides overlay -->
                                    <div class="scan-guides" id="scanGuides">
                                        <div class="scan-guide-corner tl"></div>
                                        <div class="scan-guide-corner tr"></div>
                                        <div class="scan-guide-corner bl"></div>
                                        <div class="scan-guide-corner br"></div>
                                        <div class="scan-line" id="scanLine"></div>
                                    </div>
                                </div>

                                <!-- Camera/File Controls -->
                                <div class="mt-3 d-flex gap-2 justify-content-center flex-wrap" style="gap:8px;">
                                    <button class="btn btn-success btn-sm mr-1" id="btnCaptureShot" onclick="capturePhoto()" style="display:none;">
                                        <i class="fas fa-camera mr-1"></i> Ambil Foto
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm mr-1" id="btnRetakePhoto" onclick="retakePhoto()" style="display:none;">
                                        <i class="fas fa-redo mr-1"></i> Ulangi
                                    </button>
                                    <label class="btn btn-outline-primary btn-sm mb-0" id="btnUploadFile">
                                        <i class="fas fa-upload mr-1"></i> Upload Foto
                                        <input type="file" id="fileInputScan" accept="image/*" style="display:none;" onchange="handleFileUpload(event)">
                                    </label>
                                    <button class="btn btn-primary btn-sm" id="btnProcessOcr" onclick="processOcr()" style="display:none;">
                                        <i class="fas fa-magic mr-1"></i> Proses OCR
                                    </button>
                                </div>

                                <!-- OCR Progress -->
                                <div id="ocrProgressWrap">
                                    <div class="ocr-progress-bar mt-2">
                                        <div class="ocr-progress-fill" id="ocrProgressFill"></div>
                                    </div>
                                    <div class="ocr-status-text" id="ocrStatusText">Memproses gambar...</div>
                                </div>
                            </div>

                            <!-- RIGHT: Results -->
                            <div class="col-md-6">
                                <div style="background:#f8fafc;border-radius:12px;padding:16px;min-height:280px;">
                                    <div id="resultsPlaceholder" class="text-center text-muted py-5">
                                        <i class="fas fa-file-alt fa-3x mb-3" style="opacity:.2;"></i>
                                        <p class="small">Hasil ekstraksi data akan muncul di sini setelah proses OCR selesai</p>
                                    </div>
                                    <div id="resultsPanel" style="display:none;">
                                        <h6 class="font-weight-bold text-success mb-3">
                                            <i class="fas fa-check-circle mr-1"></i> Data Terdeteksi
                                        </h6>
                                        <div id="resultFieldList"></div>
                                        <hr>
                                        <!-- Raw text toggle -->
                                        <button class="btn btn-outline-secondary btn-xs btn-sm" onclick="toggleRaw()">
                                            <i class="fas fa-code mr-1"></i> Lihat Teks Mentah
                                        </button>
                                        <textarea id="rawOcrText" class="w-100 mt-2" rows="5" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light justify-content-between">
                        <small class="text-muted" id="docTypeLabel">
                            <i class="fas fa-info-circle mr-1"></i> Jenis Dokumen: <strong id="currentDocTypeText">KTP/NIK</strong>
                        </small>
                        <div>
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal" onclick="stopCamera()">Tutup</button>
                            <button type="button" class="btn btn-success" id="btnApplyToForm" onclick="applyToForm()" style="display:none;">
                                <i class="fas fa-check mr-1"></i> Terapkan ke Formulir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden file input trigger dari dock -->
        <input type="file" id="dockFileInput" accept="image/*" style="display:none;" onchange="handleDockFileUpload(event)">

        <!-- ============================================================
             FORM PENDAFTARAN
             ============================================================ -->
        <div class="card form-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit mr-2 text-primary"></i> Input Data Calon Siswa</h3>
                <div class="card-tools">
                    <span class="badge badge-light text-muted" id="autoFillBadge" style="display:none;">
                        <i class="fas fa-magic mr-1 text-success"></i> <span id="autoFillCount">0</span> field terisi otomatis
                    </span>
                </div>
            </div>
            <form action="<?= BASE_URL ?>ppdb/save" method="POST" id="ppdbForm">
                <div class="card-body">
                    <div class="row">
                        <!-- KOLOM KIRI -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" id="f_nama_lengkap" class="form-control" required
                                       placeholder="Nama sesuai dokumen">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">NISN <small class="text-muted font-weight-normal">(10 digit)</small></label>
                                <input type="text" name="nisn" id="f_nisn" class="form-control" maxlength="10"
                                       placeholder="Nomor Induk Siswa Nasional">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">NIK <small class="text-muted font-weight-normal">(16 digit)</small></label>
                                <input type="text" name="nik" id="f_nik" class="form-control" maxlength="16"
                                       placeholder="Nomor Induk Kependudukan">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jk" id="f_jk" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="f_tempat_lahir" class="form-control"
                                       placeholder="Kota/Kabupaten tempat lahir">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="f_tanggal_lahir" class="form-control">
                            </div>
                        </div>

                        <!-- KOLOM KANAN -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Sekolah Asal (SD/SMP) <span class="text-danger">*</span></label>
                                <input type="text" name="sekolah_asal" id="f_sekolah_asal" class="form-control"
                                       placeholder="Contoh: SMPN 1 CIBADAK" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Jalur Pendaftaran</label>
                                <input type="text" name="jalur_pendaftaran" id="f_jalur_pendaftaran" class="form-control"
                                       placeholder="Contoh: Zonasi, Prestasi, Afirmasi">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Nilai Seleksi <small class="text-muted font-weight-normal">(Rata-rata Rapor/Ujian)</small></label>
                                <input type="number" step="0.01" name="nilai_seleksi" id="f_nilai_seleksi" class="form-control"
                                       placeholder="Contoh: 85.50">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">Nama Wali <small class="text-muted font-weight-normal">(Ayah/Ibu/Wali)</small></label>
                                <input type="text" name="nama_wali" id="f_nama_wali" class="form-control"
                                       placeholder="Nama orang tua / wali">
                            </div>
                            <div class="form-group">
                                <label class="form-label-custom">No. Telepon Wali <small class="text-muted font-weight-normal">(Aktif WA)</small></label>
                                <input type="text" name="telp_wali" id="f_telp_wali" class="form-control"
                                       placeholder="Contoh: 0812-XXXX-XXXX">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="<?= BASE_URL ?>ppdb" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-2"></i> Simpan Pendaftaran
                    </button>
                </div>
            </form>
        </div>

    </div><!-- /container-fluid -->
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<!-- Tesseract.js OCR Library (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<script>
/* ================================================================
   SCAN DOKUMEN SYSTEM - PPDB FORM
   Menggunakan Tesseract.js untuk OCR client-side
   ================================================================ */

// ---- STATE ----
let selectedDocType = 'ktp';
let scanMode        = 'camera'; // 'camera' | 'file'
let cameraStream    = null;
let imageReady      = false;    // Ada gambar siap diproses?
let detectedData    = {};       // Data hasil parsing OCR

// Doc type config
const docTypes = {
    ktp: {
        label: 'KTP / NIK',
        icon:  'fas fa-id-card',
        hint:  'Pastikan foto NIK/KTP siswa jelas dan tidak berbayang',
        fields: ['nik', 'nama', 'jk', 'tempat_lahir', 'tanggal_lahir'],
    },
    kk: {
        label: 'Kartu Keluarga',
        icon:  'fas fa-home',
        hint:  'Scan halaman KK yang memuat data anggota keluarga siswa',
        fields: ['nik', 'nama', 'jk', 'tempat_lahir', 'tanggal_lahir', 'nama_wali'],
    },
    ijazah: {
        label: 'Ijazah / SKHUN',
        icon:  'fas fa-scroll',
        hint:  'Scan halaman ijazah / SKHUN SMP/MTs yang memuat data siswa',
        fields: ['nisn', 'nama', 'tempat_lahir', 'tanggal_lahir', 'sekolah_asal'],
    },
    akte: {
        label: 'Akte Kelahiran',
        icon:  'fas fa-baby',
        hint:  'Scan akte kelahiran siswa yang memuat nama dan tanggal lahir',
        fields: ['nama', 'tempat_lahir', 'tanggal_lahir', 'nama_wali'],
    },
};

// ---- DOC TYPE SELECTION ----
function selectDocType(btn, type) {
    document.querySelectorAll('.doc-type-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedDocType = type;
    document.getElementById('currentDocTypeText').textContent = docTypes[type].label;
}

// ---- OPEN MODAL ----
function openScanModal(mode) {
    scanMode = mode;
    resetModalState();

    const dt = docTypes[selectedDocType];
    document.getElementById('scanModalTitle').innerHTML = `<i class="${dt.icon} mr-2"></i> Scan ${dt.label}`;
    document.getElementById('scanModalSub').textContent = dt.hint;
    document.getElementById('currentDocTypeText').textContent = dt.label;

    $('#modalScanDokumen').modal('show');

    if (mode === 'camera') {
        startCamera();
    }
}

function openScanModal_File(inputEl) {
    // Dipanggil dari dock "Upload Foto"
    document.getElementById('dockFileInput').click();
}

// Trigger dari dock file label
function handleDockFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    resetModalState();
    $('#modalScanDokumen').modal('show');
    displayUploadedFile(file);
}

// ---- CAMERA ----
async function startCamera() {
    const video = document.getElementById('videoFeed');
    const placeholder = document.getElementById('camPlaceholder');
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1920 }, height: { ideal: 1080 } }
        });
        video.srcObject = cameraStream;
        video.style.display = 'block';
        placeholder.style.display = 'none';
        document.getElementById('btnCaptureShot').style.display = 'inline-flex';
        document.getElementById('scanGuides').classList.add('active');
    } catch(e) {
        placeholder.innerHTML = `<i class="fas fa-exclamation-triangle" style="color:#f59e0b;font-size:2rem;display:block;margin-bottom:8px;"></i><span style="color:#f59e0b;font-size:.85rem;">Kamera tidak tersedia.<br>Gunakan tombol "Upload Foto".</span>`;
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
}

function capturePhoto() {
    const video  = document.getElementById('videoFeed');
    const canvas = document.getElementById('capturedCanvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    // Show captured image
    video.style.display = 'none';
    canvas.style.display = 'block';
    stopCamera();

    document.getElementById('btnCaptureShot').style.display = 'none';
    document.getElementById('btnRetakePhoto').style.display = 'inline-flex';
    document.getElementById('btnProcessOcr').style.display = 'inline-flex';
    document.getElementById('scanGuides').classList.remove('active');
    imageReady = true;
}

function retakePhoto() {
    const video  = document.getElementById('videoFeed');
    const canvas = document.getElementById('capturedCanvas');
    canvas.style.display = 'none';
    video.style.display = 'block';
    document.getElementById('btnRetakePhoto').style.display = 'none';
    document.getElementById('btnProcessOcr').style.display = 'none';
    document.getElementById('btnCaptureShot').style.display = 'inline-flex';
    document.getElementById('scanGuides').classList.add('active');
    document.getElementById('resultsPanel').style.display = 'none';
    document.getElementById('resultsPlaceholder').style.display = 'block';
    document.getElementById('btnApplyToForm').style.display = 'none';
    imageReady = false;
    detectedData = {};
    startCamera();
}

// ---- FILE UPLOAD ----
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    displayUploadedFile(file);
}

function displayUploadedFile(file) {
    stopCamera();
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('imgPreview');
        img.src = e.target.result;
        img.style.display = 'block';
        document.getElementById('videoFeed').style.display = 'none';
        document.getElementById('capturedCanvas').style.display = 'none';
        document.getElementById('camPlaceholder').style.display = 'none';
        document.getElementById('btnProcessOcr').style.display = 'inline-flex';
        document.getElementById('btnRetakePhoto').style.display = 'inline-flex';
        document.getElementById('scanGuides').classList.remove('active');
        imageReady = true;
    };
    reader.readAsDataURL(file);
}

// ---- OCR PROCESSING ----
async function processOcr() {
    if (!imageReady) return;

    // Tentukan sumber gambar
    let imageSource;
    const canvas  = document.getElementById('capturedCanvas');
    const imgPrev = document.getElementById('imgPreview');
    if (canvas.style.display !== 'none' && canvas.width > 0) {
        imageSource = canvas;
    } else if (imgPrev.style.display !== 'none' && imgPrev.src) {
        imageSource = imgPrev.src;
    } else {
        alert('Tidak ada gambar. Ambil foto atau upload file terlebih dahulu.');
        return;
    }

    // Show progress
    document.getElementById('ocrProgressWrap').style.display = 'block';
    document.getElementById('btnProcessOcr').disabled = true;
    document.getElementById('scanLine').classList.add('active');
    document.getElementById('resultsPanel').style.display = 'none';
    document.getElementById('resultsPlaceholder').style.display = 'block';
    document.getElementById('btnApplyToForm').style.display = 'none';

    try {
        const worker = await Tesseract.createWorker('ind+eng', 1, {
            logger: m => {
                if (m.status === 'recognizing text') {
                    const pct = Math.round(m.progress * 100);
                    document.getElementById('ocrProgressFill').style.width = pct + '%';
                    document.getElementById('ocrStatusText').textContent = `Membaca teks... ${pct}%`;
                } else if (m.status) {
                    document.getElementById('ocrStatusText').textContent = m.status;
                }
            }
        });

        const { data: { text } } = await worker.recognize(imageSource);
        await worker.terminate();

        document.getElementById('rawOcrText').value = text;
        document.getElementById('scanLine').classList.remove('active');
        document.getElementById('ocrStatusText').textContent = '✅ OCR selesai!';

        // Parse result berdasarkan jenis dokumen
        detectedData = parseOcrText(text, selectedDocType);
        renderResults(detectedData);

    } catch(err) {
        document.getElementById('ocrStatusText').textContent = '❌ Gagal: ' + err.message;
        document.getElementById('scanLine').classList.remove('active');
        console.error('OCR Error:', err);
    }

    document.getElementById('btnProcessOcr').disabled = false;
}

// ---- PARSING OCR TEXT ----
function parseOcrText(text, docType) {
    const result = {};
    const lines  = text.split('\n').map(l => l.trim()).filter(l => l.length > 0);
    const full   = text.toUpperCase();

    // Helper: Cari teks di baris berikutnya setelah keyword
    function getAfter(keyword, lines) {
        for (let i = 0; i < lines.length - 1; i++) {
            if (lines[i].toUpperCase().includes(keyword)) {
                const next = lines[i+1].trim();
                if (next && next.length > 1) return next;
            }
        }
        return null;
    }
    // Helper: Cari nilai di baris yang sama setelah ':' atau keyword
    function getInline(keyword, lines) {
        for (const line of lines) {
            const up = line.toUpperCase();
            if (up.includes(keyword)) {
                const parts = line.split(/[:]/);
                if (parts.length > 1) {
                    const val = parts.slice(1).join(':').trim();
                    if (val) return val;
                }
            }
        }
        return null;
    }

    // --- NIK (16 digit angka) ---
    const nikMatch = text.match(/\b(\d{16})\b/);
    if (nikMatch) result.nik = nikMatch[1];

    // --- NISN (10 digit angka, hanya untuk ijazah) ---
    if (docType === 'ijazah') {
        const nisnMatch = text.match(/\b(\d{10})\b/);
        if (nisnMatch) result.nisn = nisnMatch[1];
    }

    // --- NAMA ---
    // Coba inline dulu
    let nama = getInline('NAMA', lines) || getAfter('NAMA', lines);
    // Bersihkan kata-kata bukan nama
    if (nama) {
        nama = nama.replace(/\b(LENGKAP|SISWA|PESERTA|KELAMIN|NIK|WARGA)\b/gi, '').trim();
        // Hanya huruf, spasi, titik, tanda petik
        const cleaned = nama.replace(/[^a-zA-Z\s.,'-]/g, '').trim();
        if (cleaned.length >= 2) result.nama = cleaned;
    }

    // --- JENIS KELAMIN ---
    if (/LAKI[\s\-]+LAKI|LAKI-LAKI|\bL\b/.test(full)) {
        result.jk = 'L';
    } else if (/PEREMPUAN|WANITA|PR|\bP\b/.test(full)) {
        result.jk = 'P';
    }

    // --- TEMPAT & TANGGAL LAHIR ---
    // Format: "TEMPAT/TGL LAHIR : BANDUNG, 15-05-2009"
    // atau dua baris: "TEMPAT LAHIR" → baris berikut, "TANGGAL LAHIR" → baris berikut
    const ttlPatterns = [
        /(?:TEMPAT|TGL|TANGGAL)\s*[:/]\s*([A-Z\s]+)[,]\s*(\d{1,2}[-/]\d{1,2}[-/]\d{2,4})/i,
        /([A-Z\s]+),\s*(\d{2}\s+\w+\s+\d{4})/i, // BANDUNG, 15 Mei 2009
        /([A-Z\s]+),\s*(\d{2}-\d{2}-\d{4})/i,
    ];

    for (const pat of ttlPatterns) {
        const m = text.match(pat);
        if (m) {
            const place = m[1].trim().replace(/[^a-zA-Z\s]/g, '').trim();
            if (place.length >= 2) result.tempat_lahir = place;
            result.tanggal_lahir_raw = m[2].trim();
            break;
        }
    }

    // Coba parse tempat lahir dari inline/after keyword
    if (!result.tempat_lahir) {
        const tp = getInline('TEMPAT LAHIR', lines) || getAfter('TEMPAT LAHIR', lines);
        if (tp) result.tempat_lahir = tp.replace(/[^a-zA-Z\s]/g, '').trim();
    }

    // Coba parse tanggal lahir
    if (!result.tanggal_lahir_raw) {
        const tglLine = getInline('TANGGAL LAHIR', lines) || getInline('TGL LAHIR', lines)
                      || getAfter('TANGGAL LAHIR', lines) || getAfter('TGL LAHIR', lines);
        if (tglLine) result.tanggal_lahir_raw = tglLine.trim();
    }

    // Konversi tanggal ke format YYYY-MM-DD
    if (result.tanggal_lahir_raw) {
        result.tanggal_lahir = parseTanggal(result.tanggal_lahir_raw);
    }

    // --- NAMA SEKOLAH (untuk ijazah) ---
    if (docType === 'ijazah') {
        const skolahKeywords = ['SMP', 'MTS', 'MTsN', 'SEKOLAH MENENGAH PERTAMA', 'SD ', 'MIN '];
        for (const line of lines) {
            const up = line.toUpperCase();
            if (skolahKeywords.some(k => up.includes(k)) && line.length > 4) {
                result.sekolah_asal = line.trim();
                break;
            }
        }
    }

    // --- NAMA ORANG TUA / WALI (dari KK) ---
    if (docType === 'kk' || docType === 'akte') {
        const waliKeys = ['NAMA AYAH', 'NAMA IBU', 'AYAH', 'IBU', 'WALI'];
        for (const key of waliKeys) {
            const val = getInline(key, lines) || getAfter(key, lines);
            if (val && val.length >= 2) {
                result.nama_wali = val.replace(/[^a-zA-Z\s.,'-]/g, '').trim();
                break;
            }
        }
    }

    return result;
}

// Konversi berbagai format tanggal ke YYYY-MM-DD
function parseTanggal(raw) {
    const bulanIndo = {
        'JAN':1,'JANUARI':1,'FEB':2,'FEBRUARI':2,'MAR':3,'MARET':3,
        'APR':4,'APRIL':4,'MEI':5,'MAY':5,'JUN':6,'JUNI':6,
        'JUL':7,'JULI':7,'AGU':8,'AGUSTUS':8,'SEP':9,'SEPTEMBER':9,
        'OKT':10,'OKTOBER':10,'NOV':11,'NOVEMBER':11,'DES':12,'DESEMBER':12,
        'OCT':10,'DEC':12,'AUG':8
    };
    raw = raw.trim();

    // Format: 15-05-2009 atau 15/05/2009
    let m = raw.match(/^(\d{1,2})[-/](\d{1,2})[-/](\d{4})$/);
    if (m) {
        return `${m[3]}-${m[2].padStart(2,'0')}-${m[1].padStart(2,'0')}`;
    }
    // Format: 15-05-09
    m = raw.match(/^(\d{1,2})[-/](\d{1,2})[-/](\d{2})$/);
    if (m) {
        const yr = parseInt(m[3]) < 30 ? '20' + m[3] : '19' + m[3];
        return `${yr}-${m[2].padStart(2,'0')}-${m[1].padStart(2,'0')}`;
    }
    // Format: 15 Mei 2009 / 15 MEI 2009
    m = raw.match(/^(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})$/);
    if (m) {
        const bln = bulanIndo[m[2].toUpperCase()];
        if (bln) return `${m[3]}-${String(bln).padStart(2,'0')}-${m[1].padStart(2,'0')}`;
    }
    return null;
}

// ---- RENDER RESULTS ----
const fieldMeta = {
    nik:          { label: 'NIK',          icon: 'fas fa-fingerprint', target: 'f_nik' },
    nisn:         { label: 'NISN',         icon: 'fas fa-hashtag',     target: 'f_nisn' },
    nama:         { label: 'Nama Lengkap', icon: 'fas fa-user',        target: 'f_nama_lengkap' },
    jk:           { label: 'Jenis Kelamin',icon: 'fas fa-venus-mars',  target: 'f_jk', isSelect: true },
    tempat_lahir: { label: 'Tempat Lahir', icon: 'fas fa-map-marker-alt', target: 'f_tempat_lahir' },
    tanggal_lahir:{ label: 'Tgl Lahir',    icon: 'fas fa-calendar',    target: 'f_tanggal_lahir' },
    sekolah_asal: { label: 'Sekolah Asal', icon: 'fas fa-school',      target: 'f_sekolah_asal' },
    nama_wali:    { label: 'Nama Wali',    icon: 'fas fa-users',       target: 'f_nama_wali' },
};

function renderResults(data) {
    const list = document.getElementById('resultFieldList');
    list.innerHTML = '';

    let countDetected = 0;

    for (const [key, meta] of Object.entries(fieldMeta)) {
        if (!data[key] && key !== 'tanggal_lahir') continue; // Skip undetected (unless tgl lahir)
        const val = data[key];
        const detected = !!val;
        if (detected) countDetected++;

        const row = document.createElement('div');
        row.className = 'result-field-row ' + (detected ? 'detected' : 'not-detected');
        row.innerHTML = `
            <span class="field-icon"><i class="${meta.icon} text-muted"></i></span>
            <span class="field-label">${meta.label}</span>
            <span class="field-value">${detected ? htmlEsc(val) : '<em style="color:#94a3b8">Tidak terdeteksi</em>'}</span>
            ${detected ? '<span class="field-check"><i class="fas fa-check-circle"></i></span>' : ''}
        `;
        list.appendChild(row);
    }

    document.getElementById('resultsPlaceholder').style.display = 'none';
    document.getElementById('resultsPanel').style.display = 'block';

    if (countDetected > 0) {
        document.getElementById('btnApplyToForm').style.display = 'inline-flex';
    }
}

// ---- APPLY TO FORM ----
let totalAutoFilled = 0;

function applyToForm() {
    let filled = 0;
    const fieldMap = {
        nik:           'f_nik',
        nisn:          'f_nisn',
        nama:          'f_nama_lengkap',
        tempat_lahir:  'f_tempat_lahir',
        tanggal_lahir: 'f_tanggal_lahir',
        sekolah_asal:  'f_sekolah_asal',
        nama_wali:     'f_nama_wali',
    };

    for (const [key, fieldId] of Object.entries(fieldMap)) {
        if (detectedData[key]) {
            const el = document.getElementById(fieldId);
            if (el) {
                el.value = detectedData[key];
                el.classList.add('auto-filled');
                setTimeout(() => el.classList.remove('auto-filled'), 2500);
                filled++;
            }
        }
    }

    // Jenis kelamin (select)
    if (detectedData.jk) {
        const jkEl = document.getElementById('f_jk');
        if (jkEl) {
            jkEl.value = detectedData.jk;
            jkEl.classList.add('auto-filled');
            setTimeout(() => jkEl.classList.remove('auto-filled'), 2500);
            filled++;
        }
    }

    totalAutoFilled += filled;

    // Close modal
    $('#modalScanDokumen').modal('hide');
    stopCamera();

    // Show badge
    document.getElementById('autoFillCount').textContent = totalAutoFilled;
    document.getElementById('autoFillBadge').style.display = 'inline-block';

    // Toast notification
    showToast(`✅ ${filled} field berhasil diisi dari dokumen ${docTypes[selectedDocType].label}`, 'success');
}

// ---- HELPERS ----
function resetModalState() {
    stopCamera();
    document.getElementById('videoFeed').style.display = 'none';
    document.getElementById('capturedCanvas').style.display = 'none';
    document.getElementById('imgPreview').style.display = 'none';
    document.getElementById('camPlaceholder').style.display = 'flex';
    document.getElementById('btnCaptureShot').style.display = 'none';
    document.getElementById('btnRetakePhoto').style.display = 'none';
    document.getElementById('btnProcessOcr').style.display = 'none';
    document.getElementById('btnApplyToForm').style.display = 'none';
    document.getElementById('ocrProgressWrap').style.display = 'none';
    document.getElementById('ocrProgressFill').style.width = '0%';
    document.getElementById('ocrStatusText').textContent = '';
    document.getElementById('resultsPanel').style.display = 'none';
    document.getElementById('resultsPlaceholder').style.display = 'block';
    document.getElementById('scanGuides').classList.remove('active');
    document.getElementById('scanLine').classList.remove('active');
    document.getElementById('rawOcrText').value = '';
    document.getElementById('rawOcrText').style.display = 'none';
    imageReady = false;
    detectedData = {};
    document.getElementById('camPlaceholder').innerHTML = `<div style="color:rgba(255,255,255,.3);text-align:center;padding:40px"><i class="fas fa-camera-retro" style="font-size:3rem;display:block;margin-bottom:8px;"></i><span>Kamera / Foto belum aktif</span></div>`;
}

function toggleRaw() {
    const el = document.getElementById('rawOcrText');
    el.style.display = (el.style.display === 'none' ? 'block' : 'none');
}

function htmlEsc(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function showToast(msg, type) {
    // Coba pakai toastr jika ada
    if (typeof toastr !== 'undefined') {
        toastr[type](msg);
    } else {
        const div = document.createElement('div');
        div.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;background:#10b981;color:white;padding:14px 22px;border-radius:10px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.2);';
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 4000);
    }
}

// Stop camera on modal close
$('#modalScanDokumen').on('hidden.bs.modal', function () {
    stopCamera();
});

// Dock "Upload Foto" button — open modal then pick file
document.querySelector('.btn-scan-file').addEventListener('click', function(e) {
    e.preventDefault();
    openScanModal('file');
    // Tunggu modal terbuka lalu trigger file picker
    setTimeout(function() {
        document.getElementById('fileInputScan').click();
    }, 500);
});
</script>