<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
$tanggal_sekarang = date('Y-m-d');
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-qrcode text-primary mr-2"></i> Scan QR & Barcode Absensi
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <span class="badge badge-info p-2 font-weight-bold" style="font-size: 1rem;">
                    <i class="fas fa-clock mr-1"></i> <span id="liveClock">00:00:00</span> WIB
                </span>
                <span class="badge badge-warning p-2 font-weight-bold ml-1" style="font-size: 1rem;">
                    <i class="fas fa-hourglass-half mr-1"></i> Toleransi: <span id="cutoffDisplay">08:15</span>
                </span>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- PANEL KIRI: SCANNER INPUT -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card card-primary card-outline shadow-sm rounded-lg">
                    <div class="card-header bg-white py-3">
                        <ul class="nav nav-pills card-header-pills nav-justified" id="scannerModeTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold" id="tab-usb" data-toggle="tab" href="#mode-usb" role="tab">
                                    <i class="fas fa-barcode mr-1"></i> USB Scanner
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="tab-kamera" data-toggle="tab" href="#mode-kamera" role="tab">
                                    <i class="fas fa-camera mr-1"></i> Kamera Laptop / HP
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4 text-center">
                        <div class="tab-content" id="scannerModeContent">

                            <!-- MODE USB / HANDHELD SCANNER -->
                            <div class="tab-pane fade show active" id="mode-usb" role="tabpanel">
                                <div class="p-3 mb-3 bg-light rounded border border-dashed">
                                    <i class="fas fa-scanner fa-3x text-primary mb-3"></i>
                                    <h5 class="font-weight-bold text-dark">Alat Scan Portabel (USB)</h5>
                                    <p class="text-muted small mb-3">
                                        Arahkan scanner ke QR Code / Barcode kartu siswa atau guru. Sistem akan otomatis memproses tanpa mengkliknya.
                                    </p>
                                    
                                    <div class="form-group mb-2">
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary text-white"><i class="fas fa-barcode"></i></span>
                                            </div>
                                            <input type="text" id="barcodeInput" class="form-control form-control-lg text-center font-weight-bold" 
                                                   placeholder="Siap Scan Kartu / Ketik Kode..." autofocus autocomplete="off">
                                        </div>
                                    </div>
                                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Kursor otomatis terkunci di kolom di atas.</small>
                                </div>
                            </div>

                            <!-- MODE KAMERA WEBRTC -->
                            <div class="tab-pane fade" id="mode-kamera" role="tabpanel">
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-muted">PILIH KAMERA:</label>
                                    <select id="cameraSelect" class="form-control custom-select">
                                        <option value="">-- Memuat Kamera... --</option>
                                    </select>
                                </div>

                                <div id="cameraPreviewBox" class="position-relative mx-auto rounded overflow-hidden shadow-sm bg-dark" 
                                     style="max-width: 100%; min-height: 250px; display: flex; align-items: center; justify-content: center;">
                                    <div id="qr-reader" style="width: 100%;"></div>
                                    <div id="cameraPlaceholder" class="text-white p-4">
                                        <i class="fas fa-video-slash fa-3x mb-2 text-secondary"></i>
                                        <p class="mb-0 small">Kamera belum aktif. Klik tombol di bawah untuk memulai.</p>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button id="btnStartCamera" class="btn btn-success rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-video mr-1"></i> Aktifkan Kamera
                                    </button>
                                    <button id="btnStopCamera" class="btn btn-danger rounded-pill px-4 shadow-sm d-none">
                                        <i class="fas fa-stop-circle mr-1"></i> Matikan Kamera
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- TOLERANSI SETTING MINI -->
                        <hr class="my-3">
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                            <span class="small font-weight-bold text-muted"><i class="fas fa-clock mr-1"></i> Batas Tepat Waktu:</span>
                            <div class="d-flex align-items-center">
                                <input type="time" id="inputCutoff" class="form-control form-control-sm text-center font-weight-bold" 
                                       value="08:15" style="width: 100px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FEEDBACK POPUP / BANNER REALTIME -->
                <div id="scanFeedback" class="card shadow-sm border-0 d-none" style="border-radius: 12px; transition: all 0.3s;">
                    <div class="card-body p-3 text-center text-white" id="scanFeedbackBody">
                        <div id="feedbackIcon" class="mb-1" style="font-size: 2.5rem;"></div>
                        <h4 id="feedbackNama" class="font-weight-bold mb-1"></h4>
                        <div id="feedbackInfo" class="badge badge-light px-3 py-1 font-weight-bold mb-2"></div>
                        <p id="feedbackMsg" class="mb-0 font-italic small"></p>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: LIVE REKAP & LOG SCAN HARI INI -->
            <div class="col-lg-7 col-md-12">
                <!-- CARDS REKAP -->
                <div class="row mb-3">
                    <div class="col-4">
                        <div class="small-box bg-info shadow-sm rounded-lg mb-0 p-2 text-center">
                            <div class="inner">
                                <h3 id="countTotal" class="font-weight-bold mb-0">0</h3>
                                <p class="mb-0 small text-uppercase">Total Scan Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small-box bg-success shadow-sm rounded-lg mb-0 p-2 text-center">
                            <div class="inner">
                                <h3 id="countHadir" class="font-weight-bold mb-0">0</h3>
                                <p class="mb-0 small text-uppercase">Tepat Waktu (≤ 08:15)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="small-box bg-warning shadow-sm rounded-lg mb-0 p-2 text-center text-dark">
                            <div class="inner">
                                <h3 id="countTerlambat" class="font-weight-bold mb-0">0</h3>
                                <p class="mb-0 small text-uppercase">Terlambat (> 08:15)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE LOG SCAN -->
                <div class="card shadow-sm rounded-lg border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold m-0 text-dark">
                            <i class="fas fa-list-alt text-primary mr-2"></i> Log Absensi Real-Time Hari Ini
                        </h5>
                        <button id="btnRefreshLogs" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="thead-light sticky-top" style="z-index: 5;">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Kategori / Kelas</th>
                                        <th>Status Kehadiran</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="logTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4 font-italic">
                                            <i class="fas fa-spinner fa-spin mr-1"></i> Memuat log absensi...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HTML5 QRCode Scanner JS -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. LIVE CLOCK
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').textContent = `${hrs}:${mins}:${secs}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. AUDIO SYNTHESIZER (BEEP EFFECTS)
    function playAudioFeedback(type) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'success') {
                // High pitch short double beep
                osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.15);
            } else if (type === 'terlambat') {
                // Warning dual tone
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(440, ctx.currentTime + 0.15); // A4
                gain.gain.setValueAtTime(0.4, ctx.currentTime);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.35);
            } else {
                // Error buzz
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(150, ctx.currentTime);
                gain.gain.setValueAtTime(0.5, ctx.currentTime);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            }
        } catch (e) {
            console.log('Audio Context not allowed without user interaction:', e);
        }
    }

    // 3. BARCODE INPUT FOCUS CONTROL (USB SCANNER)
    const barcodeInput = document.getElementById('barcodeInput');
    const inputCutoff = document.getElementById('inputCutoff');

    // Keep input focused unless user is interacting with controls
    function keepFocus() {
        if (document.activeElement !== inputCutoff && document.activeElement.tagName !== 'SELECT' && document.activeElement.tagName !== 'BUTTON') {
            barcodeInput.focus();
        }
    }
    setInterval(keepFocus, 2000);
    barcodeInput.focus();

    inputCutoff.addEventListener('change', function() {
        document.getElementById('cutoffDisplay').textContent = this.value;
    });

    let isProcessing = false;

    // Handle USB Scanner Submit (Press Enter)
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = this.value.trim();
            if (code && !isProcessing) {
                processScanCode(code);
            }
            this.value = '';
        }
    });

    // 4. PROCESS SCAN CODE (AJAX POST TO API)
    function processScanCode(code) {
        isProcessing = true;
        const cutoffTime = inputCutoff.value;

        fetch('<?= BASE_URL ?>api.php?mod=absensi&act=scan_absensi', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `code=${encodeURIComponent(code)}&cutoff=${encodeURIComponent(cutoffTime)}`
        })
        .then(res => res.json())
        .then(result => {
            isProcessing = false;
            displayFeedback(result);
            if (result.status === 'ok') {
                loadTodayLogs();
            }
        })
        .catch(err => {
            isProcessing = false;
            console.error('Scan Error:', err);
            displayFeedback({
                status: 'error',
                msg: 'Gagal terhubung ke server. Periksa koneksi internet/jaringan.'
            });
        });
    }

    // 5. DISPLAY FEEDBACK BANNER
    const feedbackBox = document.getElementById('scanFeedback');
    const feedbackBody = document.getElementById('scanFeedbackBody');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const feedbackNama = document.getElementById('feedbackNama');
    const feedbackInfo = document.getElementById('feedbackInfo');
    const feedbackMsg = document.getElementById('feedbackMsg');

    function displayFeedback(res) {
        feedbackBox.classList.remove('d-none');

        if (res.status === 'ok') {
            if (res.is_terlambat) {
                // TERLAMBAT
                playAudioFeedback('terlambat');
                feedbackBody.className = 'card-body p-3 text-center text-white bg-warning';
                feedbackIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            } else {
                // TEPAT WAKTU
                playAudioFeedback('success');
                feedbackBody.className = 'card-body p-3 text-center text-white bg-success';
                feedbackIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            }

            feedbackNama.textContent = res.nama;
            feedbackInfo.textContent = `${res.info} | Jam ${res.waktu}`;
            feedbackMsg.textContent = res.msg;
        } else {
            // ERROR / NOT FOUND
            playAudioFeedback('error');
            feedbackBody.className = 'card-body p-3 text-center text-white bg-danger';
            feedbackIcon.innerHTML = '<i class="fas fa-times-circle"></i>';
            feedbackNama.textContent = 'Gagal!';
            feedbackInfo.textContent = 'Kode Tidak Terdaftar';
            feedbackMsg.textContent = res.msg;
        }

        // Auto hide banner after 6 seconds
        clearTimeout(window.feedbackTimer);
        window.feedbackTimer = setTimeout(() => {
            feedbackBox.classList.add('d-none');
        }, 6000);
    }

    // 6. WEBRTC CAMERA SCANNER (HTML5-QRCode)
    let html5QrCode = null;
    const btnStartCamera = document.getElementById('btnStartCamera');
    const btnStopCamera = document.getElementById('btnStopCamera');
    const cameraSelect = document.getElementById('cameraSelect');
    const cameraPlaceholder = document.getElementById('cameraPlaceholder');

    // Populate Camera Devices
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length > 0) {
            cameraSelect.innerHTML = '';
            devices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.id;
                option.text = device.label || `Kamera ${index + 1}`;
                cameraSelect.appendChild(option);
            });
        } else {
            cameraSelect.innerHTML = '<option value="">-- Kamera Tidak Ditemukan --</option>';
        }
    }).catch(err => {
        cameraSelect.innerHTML = '<option value="">-- Akses Kamera Ditolak / Tidak Didukung --</option>';
    });

    btnStartCamera.addEventListener('click', function() {
        const selectedCameraId = cameraSelect.value;
        if (!selectedCameraId) {
            alert('Pilih kamera terlebih dahulu!');
            return;
        }

        cameraPlaceholder.classList.add('d-none');
        html5QrCode = new Html5Qrcode("qr-reader");

        html5QrCode.start(
            selectedCameraId,
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                // On Success Scan
                if (!isProcessing) {
                    processScanCode(decodedText);
                }
            },
            (errorMessage) => {
                // Ignore parse errors per frame
            }
        ).then(() => {
            btnStartCamera.classList.add('d-none');
            btnStopCamera.classList.remove('d-none');
        }).catch(err => {
            alert("Gagal mengaktifkan kamera: " + err);
            cameraPlaceholder.classList.remove('d-none');
        });
    });

    btnStopCamera.addEventListener('click', function() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                btnStartCamera.classList.remove('d-none');
                btnStopCamera.classList.add('d-none');
                cameraPlaceholder.classList.remove('d-none');
            });
        }
    });

    // 7. LOAD RECENT LOGS & STATS
    const logTableBody = document.getElementById('logTableBody');
    const countTotal = document.getElementById('countTotal');
    const countHadir = document.getElementById('countHadir');
    const countTerlambat = document.getElementById('countTerlambat');
    const btnRefreshLogs = document.getElementById('btnRefreshLogs');

    function loadTodayLogs() {
        fetch('<?= BASE_URL ?>api.php?mod=absensi&act=get_today_scans')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'ok' && res.data) {
                    let totalHadir = 0;
                    let totalTerlambat = 0;

                    if (res.data.length === 0) {
                        logTableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 font-italic">
                                    Belum ada absensi yang dicatat hari ini.
                                </td>
                            </tr>
                        `;
                    } else {
                        let html = '';
                        res.data.forEach((row, idx) => {
                            const isTerlambat = row.keterangan && row.keterangan.toLowerCase().includes('terlambat');
                            if (isTerlambat) totalTerlambat++; else totalHadir++;

                            const badgeStatus = isTerlambat
                                ? '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Terlambat</span>'
                                : '<span class="badge badge-success"><i class="fas fa-check mr-1"></i> Hadir</span>';

                            const badgeType = row.type === 'guru'
                                ? '<span class="badge badge-info"><i class="fas fa-chalkboard-teacher mr-1"></i> Guru</span>'
                                : `<span class="badge badge-secondary"><i class="fas fa-user-graduate mr-1"></i> ${row.info}</span>`;

                            html += `
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td class="font-weight-bold">${row.nama}</td>
                                    <td>${badgeType}</td>
                                    <td>${badgeStatus}</td>
                                    <td><small class="text-muted">${row.keterangan || '-'}</small></td>
                                </tr>
                            `;
                        });
                        logTableBody.innerHTML = html;
                    }

                    countTotal.textContent = res.data.length;
                    countHadir.textContent = totalHadir;
                    countTerlambat.textContent = totalTerlambat;
                }
            })
            .catch(err => {
                console.error('Fetch Logs Error:', err);
            });
    }

    btnRefreshLogs.addEventListener('click', loadTodayLogs);
    loadTodayLogs();
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
