<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($session['nama_ujian']) ?> - CBT SIMAKS (Secured)</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Font: Inter & Arabic Support -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Noto+Naskh+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 4.6 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- KaTeX for LaTeX Math Rendering -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
    <!-- MathJax 3 as Robust Fallback -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true,
                processEnvironments: true
            },
            options: {
                skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <style>
        /* ========================================================= */
        /* ANTI-CHEAT CSS PROTECTIONS                                */
        /* ========================================================= */
        * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }
        input, textarea {
            -webkit-user-select: auto !important;
            -moz-user-select: auto !important;
            -ms-user-select: auto !important;
            user-select: auto !important;
        }
        @media print {
            body { display: none !important; }
            html { display: none !important; }
        }

        /* Arabic & Multilingual bidirectional typography */
        .arabic-text, .soal-pertanyaan, .soal-stimulus, .opsi-item-label, .opsi-item-text {
            unicode-bidi: plaintext;
        }
        .arabic-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Traditional Arabic', serif !important;
            font-size: 1.35rem !important;
            line-height: 2.1 !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }
        .cbt-navbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .timer-box {
            background: #ef4444;
            color: #ffffff;
            font-weight: 800;
            font-size: 1.25rem;
            padding: 6px 18px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
            font-family: 'Courier New', monospace;
            display: inline-flex;
            align-items: center;
            letter-spacing: 1px;
        }
        .timer-box.warning-time {
            background: #dc2626;
            animation: pulse 1s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.04); }
            100% { transform: scale(1); }
        }
        .security-badge {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
            font-weight: 700;
            font-size: 0.76rem;
            padding: 4px 12px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
        }
        .soal-container-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.04);
            min-height: 540px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .opsi-item-label {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .opsi-item-label:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .opsi-item-label.selected {
            background: #eff6ff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        .opsi-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            background: #e2e8f0;
            color: #334155;
            margin-right: 14px;
            flex-shrink: 0;
        }
        .opsi-item-label.selected .opsi-badge {
            background: #3b82f6;
            color: #ffffff;
        }
        .matrix-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        .matrix-btn {
            height: 42px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .matrix-btn.answered {
            background: #10b981 !important;
            color: #ffffff !important;
            border-color: #10b981 !important;
        }
        .matrix-btn.ragu {
            background: #f59e0b !important;
            color: #ffffff !important;
            border-color: #f59e0b !important;
        }
        .matrix-btn.active-soal {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3) !important;
            font-weight: 800;
        }
        .cheat-warning-banner {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 16px;
        }
    </style>
</head>
<body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">

    <!-- NAVBAR HEADER CBT -->
    <nav class="cbt-navbar d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
        <div>
            <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                <span class="badge badge-danger font-weight-bold px-2 py-1">CBT EXAM ROOM</span>
                <span class="security-badge"><i class="fas fa-shield-alt mr-1"></i> Anti-Cheat Aktif</span>
            </div>
            <h5 class="font-weight-bold mb-0 text-white"><?= htmlspecialchars($session['nama_ujian']) ?></h5>
            <small class="text-light opacity-75">
                <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($session['nama_mapel'] ?? '-') ?> &bull; 
                <i class="fas fa-user-graduate mr-1"></i> <?= htmlspecialchars($session['nama_siswa']) ?> (<?= htmlspecialchars($session['nisn']) ?>)
            </small>
        </div>
        <div class="d-flex align-items-center" style="gap: 14px;">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 font-weight-bold d-none d-sm-inline-block" id="btnFullscreen" onclick="toggleFullscreen()">
                <i class="fas fa-expand mr-1"></i> Layar Penuh
            </button>
            <div class="text-right">
                <div class="timer-box" id="timerDisplay">
                    <i class="fas fa-stopwatch mr-2"></i> <span id="countdownText">00:00:00</span>
                </div>
            </div>
            <button type="button" class="btn btn-warning btn-sm rounded-pill font-weight-bold px-3 shadow-sm" onclick="konfirmasiSelesai()">
                <i class="fas fa-check-circle mr-1"></i> Selesai Ujian
            </button>
        </div>
    </nav>

    <div class="container-fluid py-3 px-md-4">
        <!-- BANNER PERINGATAN KEAMANAN -->
        <div class="cheat-warning-banner d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>
                <strong>Integritas Ujian Dipantau:</strong> Dilarang berpindah tab browser, membuka aplikasi lain, mengambil tangkapan layar, atau menyalin teks. Pelanggaran tercatat otomatis.
            </div>
            <div id="violationBadge" class="badge badge-light border text-danger font-weight-bold ml-2">
                Pelanggaran: <span id="violationCount">0</span> / 3
            </div>
        </div>

        <div class="row">
            <!-- AREA UTAMA BUTIR SOAL -->
            <div class="col-lg-8 col-12 mb-4">
                <div class="card soal-container-card p-4">
                    <div id="soalContentArea">
                        <!-- HEADER BUTIR SOAL -->
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap" style="gap: 8px;">
                            <div>
                                <span class="badge badge-dark px-3 py-2 font-weight-bold rounded-pill" style="font-size: 0.95rem;">
                                    Soal Nomor <span id="labelNomorSoal">1</span> dari <?= count($session['soal_list']) ?>
                                </span>
                                <span class="badge badge-light border text-primary ml-2 font-weight-bold" id="labelTipeSoal">PILIHAN GANDA</span>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 font-weight-bold" id="btnRaguToggle" onclick="toggleRagu()">
                                    <i class="far fa-square mr-1" id="iconRagu"></i> Ragu-Ragu
                                </button>
                                <span class="badge badge-light border text-muted small px-2 py-1" id="saveStatusIndicator">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Tersimpan
                                </span>
                            </div>
                        </div>

                        <!-- STIMULUS MEDIA -->
                        <div id="mediaArea" class="mb-3 text-center" style="display: none;"></div>

                        <!-- TEKS PERTANYAAN -->
                        <div class="pertanyaan-teks text-dark mb-4" id="teksPertanyaan" style="font-size: 1.05rem; line-height: 1.7;"></div>

                        <!-- AREA OPSI PILIHAN GANDA -->
                        <div id="opsiContainerArea" class="mb-3"></div>

                        <!-- AREA INPUT ESAI -->
                        <div id="esaiContainerArea" class="mb-3" style="display: none;">
                            <label class="font-weight-bold small text-muted mb-2">Tuliskan Jawaban Uraian Anda di Bawah Ini:</label>
                            <textarea id="inputJawabanEsai" class="form-control" rows="6" placeholder="Ketik jawaban esai di sini... Jawaban otomatis tersimpan saat Anda mengetik." oninput="saveJawabanEsaiDebounce()"></textarea>
                        </div>
                    </div>

                    <!-- FOOTER NAVIGASI SOAL -->
                    <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-4 flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 font-weight-bold" id="btnPrev" onclick="navigateSoal(-1)">
                            <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                        </button>
                        <span class="text-muted small font-weight-bold d-none d-sm-inline">Gunakan nomor di samping untuk berpindah soal</span>
                        <button type="button" class="btn btn-primary rounded-pill px-4 font-weight-bold" id="btnNext" onclick="navigateSoal(1)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                            Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SIDEBAR NAVIGASI NOMOR SOAL -->
            <div class="col-lg-4 col-12">
                <div class="card shadow-sm border-0 mb-3" style="border-radius: 18px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-th mr-2 text-primary"></i> Navigasi Nomor Soal
                        </h6>
                        <span class="badge badge-light border text-muted" id="statTerjawabBadge">0 / <?= count($session['soal_list']) ?> Terjawab</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="matrix-grid mb-4" id="matrixGridContainer"></div>

                        <!-- KETERANGAN WARNA (LEGEND) -->
                        <div class="p-3 bg-light rounded-lg border small">
                            <h6 class="font-weight-bold text-dark small text-uppercase mb-2">Keterangan:</h6>
                            <div class="d-flex align-items-center mb-1">
                                <span class="d-inline-block rounded-circle bg-success mr-2" style="width: 12px; height: 12px;"></span>
                                <span>Hijau : Sudah Dijawab</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="d-inline-block rounded-circle bg-warning mr-2" style="width: 12px; height: 12px;"></span>
                                <span>Kuning : Ragu-Ragu</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="d-inline-block rounded-circle bg-white border mr-2" style="width: 12px; height: 12px;"></span>
                                <span>Putih : Belum Dijawab</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-danger btn-block rounded-pill font-weight-bold py-2 shadow-sm" onclick="konfirmasiSelesai()">
                                <i class="fas fa-check-circle mr-1"></i> Kumpulkan &amp; Selesai Ujian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT DATA & LOGIC CBT ENGINE WITH ANTI-CHEAT -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const CBT_DATA = {
            id_peserta: <?= (int)$session['id_peserta'] ?>,
            id_jadwal: <?= (int)$session['id_jadwal'] ?>,
            sisa_detik: <?= (int)$session['sisa_detik'] ?>,
            soal_list: <?= json_encode($session['soal_list']) ?>
        };

        let currentIndex = 0;
        let esaiTimeout = null;
        let violationCount = 0;
        const MAX_VIOLATIONS = 3;
        let isExamFinished = false;

        // =========================================================
        // 1. ANTI-CHEAT ENGINE & EVENT INTERCEPTORS
        // =========================================================
        
        // Blokir Shortcut Keyboard Terlarang
        document.addEventListener('keydown', function(e) {
            // F12 (Developer Tools)
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                showCheatAlert('Akses Developer Tools (F12) dinonaktifkan.');
                return false;
            }

            // Ctrl + Shift + I / J / C (Inspect Element)
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
                e.preventDefault();
                showCheatAlert('Kombinasi Inspect Element dinonaktifkan.');
                return false;
            }

            // Ctrl + U (View Page Source)
            if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
                e.preventDefault();
                showCheatAlert('Akses Page Source dinonaktifkan.');
                return false;
            }

            // Ctrl + C, Ctrl + V, Ctrl + X, Ctrl + A, Ctrl + P, Ctrl + S
            if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || e.key === 'v' || e.key === 'V' || e.key === 'x' || e.key === 'X' || e.key === 'a' || e.key === 'A' || e.key === 'p' || e.key === 'P' || e.key === 's' || e.key === 'S')) {
                // Izinkan Ctrl+V dan Ctrl+A hanya di dalam textarea esai
                if ((e.key === 'v' || e.key === 'V' || e.key === 'a' || e.key === 'A') && e.target.id === 'inputJawabanEsai') {
                    return true;
                }
                e.preventDefault();
                showCheatAlert('Fungsi Copy/Paste/Print dinonaktifkan selama ujian.');
                return false;
            }

            // PrintScreen Button
            if (e.key === 'PrintScreen') {
                e.preventDefault();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText('');
                }
                showCheatAlert('Tangkapan layar (Screenshot) dilarang selama ujian.');
                return false;
            }

            // =====================================================
            // KEYBOARD SHORTCUTS UNTUK MENJAWAB & NAVIGASI SOAL
            // =====================================================
            const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
            const isEditingText = (activeTag === 'textarea' || (activeTag === 'input' && document.activeElement.type === 'text'));

            if (!isEditingText && !isExamFinished) {
                const keyUpper = e.key.toUpperCase();

                // 1. Pilih Opsi A, B, C, D, E
                if (['A', 'B', 'C', 'D', 'E'].includes(keyUpper)) {
                    const targetBtn = $(`.opsi-item-label:has(.opsi-badge:contains('${keyUpper}'))`);
                    if (targetBtn.length > 0) {
                        e.preventDefault();
                        selectOpsi(keyUpper);
                    }
                }

                // 2. Navigasi Soal Sebelumnya (Panah Kiri, PageUp, P)
                else if (e.key === 'ArrowLeft' || e.key === 'PageUp' || keyUpper === 'P') {
                    e.preventDefault();
                    if (currentIndex > 0) {
                        navigateSoal(-1);
                    }
                }

                // 3. Navigasi Soal Selanjutnya (Panah Kanan, PageDown, N, Enter)
                else if (e.key === 'ArrowRight' || e.key === 'PageDown' || keyUpper === 'N') {
                    e.preventDefault();
                    if (currentIndex < CBT_DATA.soal_list.length - 1) {
                        navigateSoal(1);
                    }
                }

                // 4. Toggle Ragu-Ragu (R atau Space)
                else if (keyUpper === 'R' || e.key === ' ') {
                    e.preventDefault();
                    toggleRagu();
                }
            }
        });

        // Blokir Copy, Cut, Paste Global
        document.addEventListener('copy', function(e) {
            e.preventDefault();
        });
        document.addEventListener('cut', function(e) {
            e.preventDefault();
        });

        // 2. DETEKSI PINDAH TAB / BLUR WINDOW
        function handleTabSwitch() {
            if (isExamFinished) return;

            violationCount++;
            $('#violationCount').text(violationCount);

            if (violationCount >= MAX_VIOLATIONS) {
                isExamFinished = true;
                Swal.fire({
                    icon: 'error',
                    title: 'Ujian Dihentikan!',
                    html: `
                        <div class="text-danger font-weight-bold mb-2">Batas Pelanggaran Terlampaui (${violationCount}/${MAX_VIOLATIONS})</div>
                        <p class="small text-muted">Anda terdeteksi meninggalkan halaman ujian sebanyak 3 kali. Seluruh jawaban yang telah Anda isi otomatis dikumpulkan.</p>
                    `,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: 3500
                }).then(() => {
                    submitFinalCbt(true);
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: `Peringatan Pelanggaran (${violationCount}/${MAX_VIOLATIONS})`,
                    html: `
                        <div class="text-danger font-weight-bold mb-2">Terdeteksi Berpindah Tab / Membuka Aplikasi Lain!</div>
                        <p class="small text-muted">Harap tetap berada di halaman ujian. Jika Anda berpindah tab sebanyak <strong>3 kali</strong>, sistem akan langsung mengumpulkan ujian Anda.</p>
                    `,
                    confirmButtonText: 'Saya Mengerti, Lanjutkan Ujian',
                    confirmButtonColor: '#ef4444',
                    allowOutsideClick: false
                });
            }
        }

        // Event Listener Visibility Change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden || document.visibilityState === 'hidden') {
                handleTabSwitch();
            }
        });

        window.addEventListener('blur', function() {
            // Memberi jeda kecil agar tidak terpicu oleh alert internal
            setTimeout(function() {
                if (document.hidden) {
                    handleTabSwitch();
                }
            }, 300);
        });

        function showCheatAlert(msg) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'error',
                title: msg
            });
        }

        // 3. FULLSCREEN MODE TOGGLE
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen request error: ' + err.message);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        document.addEventListener('fullscreenchange', function() {
            if (document.fullscreenElement) {
                $('#btnFullscreen').html('<i class="fas fa-compress mr-1"></i> Perkecil Layar');
            } else {
                $('#btnFullscreen').html('<i class="fas fa-expand mr-1"></i> Layar Penuh');
            }
        });


        // =========================================================
        // 2. CBT ENGINE TIMER & QUESTIONS
        // =========================================================

        // COUNTDOWN TIMER ENGINE
        function initTimer() {
            let secondsLeft = CBT_DATA.sisa_detik;

            function updateTimerDisplay() {
                if (secondsLeft <= 0) {
                    $('#countdownText').text('00:00:00');
                    clearInterval(timerInterval);
                    autoSubmitTimeout();
                    return;
                }

                const h = Math.floor(secondsLeft / 3600);
                const m = Math.floor((secondsLeft % 3600) / 60);
                const s = secondsLeft % 60;

                const formatted = [
                    h.toString().padStart(2, '0'),
                    m.toString().padStart(2, '0'),
                    s.toString().padStart(2, '0')
                ].join(':');

                $('#countdownText').text(formatted);

                if (secondsLeft <= 300) {
                    $('#timerDisplay').addClass('warning-time');
                }

                secondsLeft--;
            }

            updateTimerDisplay();
            const timerInterval = setInterval(updateTimerDisplay, 1000);
        }

        // RENDER NOMOR MATRIX GRID
        function renderMatrix() {
            const container = $('#matrixGridContainer');
            container.empty();
            let answeredCount = 0;

            CBT_DATA.soal_list.forEach((s, idx) => {
                let statusCls = '';
                const hasAnswer = (s.jawaban_siswa !== null && s.jawaban_siswa !== '') || (s.jawaban_essay !== null && s.jawaban_essay !== '');
                if (hasAnswer) answeredCount++;

                if (s.is_ragu == 1) {
                    statusCls = 'ragu';
                } else if (hasAnswer) {
                    statusCls = 'answered';
                }

                const activeCls = (idx === currentIndex) ? 'active-soal' : '';

                const btn = $(`<div class="matrix-btn ${statusCls} ${activeCls}" onclick="jumpToSoal(${idx})">${idx + 1}</div>`);
                container.append(btn);
            });

            $('#statTerjawabBadge').text(`${answeredCount} / ${CBT_DATA.soal_list.length} Terjawab`);
        }

        // LOAD & TAMPILKAN SOAL AKTIF
        function loadSoal(index) {
            if (index < 0 || index >= CBT_DATA.soal_list.length) return;
            currentIndex = index;
            const soal = CBT_DATA.soal_list[currentIndex];

            $('#labelNomorSoal').text(currentIndex + 1);
            $('#teksPertanyaan').html(soal.pertanyaan);

            // Label Tipe Soal
            let tipeLabel = 'PILIHAN GANDA';
            if (soal.tipe_soal === 'essay') tipeLabel = 'ESAI / URAIAN';
            else if (soal.tipe_soal === 'tf') tipeLabel = 'BENAR / SALAH';
            else if (soal.tipe_soal === 'matching') tipeLabel = 'MENJODOHKAN';
            $('#labelTipeSoal').text(tipeLabel);

            // Stimulus Media
            const mediaArea = $('#mediaArea');
            mediaArea.empty().hide();
            if (soal.media_url && soal.media_tipe !== 'none') {
                if (soal.media_tipe === 'gambar') {
                    mediaArea.html(`<img src="${soal.media_url}" class="img-fluid rounded border shadow-sm mb-3" style="max-height: 320px;" alt="Stimulus">`).show();
                } else if (soal.media_tipe === 'audio') {
                    mediaArea.html(`<audio controls class="w-100 mb-3"><source src="${soal.media_url}"></audio>`).show();
                } else if (soal.media_tipe === 'video') {
                    mediaArea.html(`<div class="embed-responsive embed-responsive-16by9 rounded shadow-sm mb-3" style="max-height: 320px;"><iframe class="embed-responsive-item" src="${soal.media_url}" allowfullscreen></iframe></div>`).show();
                }
            }

            // Opsi PG / TF
            const opsiContainer = $('#opsiContainerArea');
            const esaiContainer = $('#esaiContainerArea');

            if (soal.tipe_soal === 'essay') {
                opsiContainer.hide();
                esaiContainer.show();
                $('#inputJawabanEsai').val(soal.jawaban_essay || '');
            } else {
                esaiContainer.hide();
                opsiContainer.empty().show();

                const opsiList = (soal.tipe_soal === 'tf') ? [
                    { label: 'B', isi_opsi: 'BENAR (TRUE)' },
                    { label: 'S', isi_opsi: 'SALAH (FALSE)' }
                ] : (soal.opsi_list || []);

                opsiList.forEach(o => {
                    const isSelected = (soal.jawaban_siswa === o.label);
                    const opsiHtml = `
                        <div class="opsi-item-label ${isSelected ? 'selected' : ''}" onclick="selectOpsi('${o.label}')">
                            <span class="opsi-badge">${o.label}</span>
                            <div class="flex-grow-1 font-weight-500">
                                ${o.isi_opsi}
                                ${o.gambar ? `<div class="mt-2"><img src="${o.gambar}" style="max-height: 90px;" class="img-thumbnail"></div>` : ''}
                            </div>
                        </div>
                    `;
                    opsiContainer.append(opsiHtml);
                });
            }

            // Tombol Ragu-Ragu
            updateRaguBtnUI(soal.is_ragu == 1);

            // Navigasi Prev/Next
            $('#btnPrev').prop('disabled', currentIndex === 0);
            if (currentIndex === CBT_DATA.soal_list.length - 1) {
                $('#btnNext').html('<i class="fas fa-check mr-1"></i> Selesai').removeClass('btn-primary').addClass('btn-success');
            } else {
                $('#btnNext').html('Selanjutnya <i class="fas fa-chevron-right ml-1"></i>').removeClass('btn-success').addClass('btn-primary');
            }

            renderMatrix();
            renderMathInDOM();
        }

        // RENDER RUMUS MATEMATIKA (KATEX & MATHJAX AUTO-RENDER)
        function renderMathInDOM() {
            setTimeout(function() {
                const targetArea = document.getElementById('soalContentArea') || document.body;
                if (typeof renderMathInElement === 'function') {
                    renderMathInElement(targetArea, {
                        delimiters: [
                            {left: '$$', right: '$$', display: true},
                            {left: '$', right: '$', display: false},
                            {left: '\\(', right: '\\)', display: false},
                            {left: '\\[', right: '\\]', display: true}
                        ],
                        throwOnError: false
                    });
                }
                if (window.MathJax && window.MathJax.typesetPromise) {
                    window.MathJax.typesetPromise([targetArea]).catch(function(err) {
                        console.log('MathJax typeset error:', err);
                    });
                }
            }, 60);
        }

        // PILIH OPSI JAWABAN (AJAX AUTO-SAVE)
        function selectOpsi(label) {
            const soal = CBT_DATA.soal_list[currentIndex];
            soal.jawaban_siswa = label;

            $('.opsi-item-label').removeClass('selected');
            $(`.opsi-item-label:has(.opsi-badge:contains('${label}'))`).addClass('selected');

            saveJawabanAjax(label, null, soal.is_ragu);
            renderMatrix();
        }

        // SIMPAN JAWABAN ESAI DEBOUNCE
        function saveJawabanEsaiDebounce() {
            clearTimeout(esaiTimeout);
            const val = $('#inputJawabanEsai').val();
            const soal = CBT_DATA.soal_list[currentIndex];
            soal.jawaban_essay = val;

            esaiTimeout = setTimeout(() => {
                saveJawabanAjax(null, val, soal.is_ragu);
                renderMatrix();
            }, 500);
        }

        // TOGGLE STATUS RAGU-RAGU
        function toggleRagu() {
            const soal = CBT_DATA.soal_list[currentIndex];
            soal.is_ragu = (soal.is_ragu == 1) ? 0 : 1;
            updateRaguBtnUI(soal.is_ragu == 1);

            saveJawabanAjax(soal.jawaban_siswa, soal.jawaban_essay, soal.is_ragu);
            renderMatrix();
        }

        function updateRaguBtnUI(isRagu) {
            if (isRagu) {
                $('#btnRaguToggle').removeClass('btn-outline-warning').addClass('btn-warning text-dark');
                $('#iconRagu').removeClass('fa-square').addClass('fa-check-square');
            } else {
                $('#btnRaguToggle').removeClass('btn-warning text-dark').addClass('btn-outline-warning');
                $('#iconRagu').removeClass('fa-check-square').addClass('fa-square');
            }
        }

        // AJAX POST AUTO-SAVE
        function saveJawabanAjax(jawaban_pg, jawaban_essay, is_ragu) {
            const soal = CBT_DATA.soal_list[currentIndex];
            $('#saveStatusIndicator').html('<i class="fas fa-spinner fa-spin text-warning mr-1"></i> Menyimpan...');

            $.post('<?= BASE_URL ?>siswa_portal/cbt_save_jawaban', {
                id_peserta: CBT_DATA.id_peserta,
                id_jadwal: CBT_DATA.id_jadwal,
                id_soal: soal.id_soal,
                jawaban_pg: jawaban_pg,
                jawaban_essay: jawaban_essay,
                is_ragu: is_ragu
            }, function(res) {
                if (res.status === 'ok') {
                    $('#saveStatusIndicator').html('<i class="fas fa-check-circle text-success mr-1"></i> Tersimpan');
                } else {
                    $('#saveStatusIndicator').html('<i class="fas fa-exclamation-circle text-danger mr-1"></i> Gagal Simpan');
                }
            }, 'json').fail(function() {
                $('#saveStatusIndicator').html('<i class="fas fa-wifi text-danger mr-1"></i> Gangguan Koneksi');
            });
        }

        // NAVIGASI PREV, NEXT, JUMP
        function navigateSoal(delta) {
            if (currentIndex === CBT_DATA.soal_list.length - 1 && delta === 1) {
                konfirmasiSelesai();
                return;
            }
            loadSoal(currentIndex + delta);
        }

        function jumpToSoal(index) {
            loadSoal(index);
        }

        // KONFIRMASI SELESAI UJIAN
        function konfirmasiSelesai() {
            let answered = 0;
            let ragu = 0;
            const total = CBT_DATA.soal_list.length;

            CBT_DATA.soal_list.forEach(s => {
                if ((s.jawaban_siswa !== null && s.jawaban_siswa !== '') || (s.jawaban_essay !== null && s.jawaban_essay !== '')) {
                    answered++;
                }
                if (s.is_ragu == 1) ragu++;
            });

            const belum = total - answered;

            Swal.fire({
                title: 'Kumpulkan Ujian Sekarang?',
                html: `
                    <div class="text-left p-3 bg-light rounded-lg border small mb-3">
                        <div><i class="fas fa-check-circle text-success mr-2"></i> Sudah Dijawab: <strong>${answered} Soal</strong></div>
                        <div><i class="fas fa-question-circle text-danger mr-2"></i> Belum Dijawab: <strong>${belum} Soal</strong></div>
                        <div><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Ragu-Ragu: <strong>${ragu} Soal</strong></div>
                    </div>
                    <p class="text-muted small">Setelah mengumpulkan, Anda tidak dapat mengubah jawaban lagi.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Selesai Ujian!',
                cancelButtonText: 'Kembali Periksa'
            }).then((result) => {
                if (result.isConfirmed) {
                    isExamFinished = true;
                    submitFinalCbt(false);
                }
            });
        }

        function autoSubmitTimeout() {
            isExamFinished = true;
            Swal.fire({
                title: 'Waktu Ujian Telah Habis!',
                text: 'Sistem sedang menyimpan dan mengumpulkan seluruh jawaban Anda otomatis.',
                icon: 'warning',
                showConfirmButton: false,
                allowOutsideClick: false
            });
            submitFinalCbt(false);
        }

        function submitFinalCbt(isViolated) {
            Swal.fire({
                title: 'Memproses Penilaian...',
                text: 'Mohon tunggu sebentar, sistem sedang menghitung skor nilai Anda.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.post('<?= BASE_URL ?>siswa_portal/cbt_selesai', {
                id_peserta: CBT_DATA.id_peserta
            }, function(res) {
                if (res.status === 'ok') {
                    const showScore = (res.tampilkan_nilai !== 0 && res.tampilkan_nilai !== '0');
                    let finishHtml = '';

                    if (showScore) {
                        finishHtml = `
                            <div class="h3 font-weight-bold text-success my-3">Nilai: ${res.nilai_akhir}</div>
                            <p class="text-muted small">Jawaban benar: <strong>${res.benar_pg}</strong> dari ${res.total_pg} butir soal pilihan ganda.</p>
                        `;
                    } else {
                        finishHtml = `
                            <div class="alert alert-info text-left small my-3 border-0 shadow-sm" style="background: #e0e7ff; color: #1e1b4b; border-radius: 12px;">
                                <div class="font-weight-bold mb-1"><i class="fas fa-lock text-primary mr-1"></i> Nilai Ditutup oleh Pengawas / Guru</div>
                                Seluruh lembar jawaban ujian Anda telah berhasil disimpan dan diamankan di server. Pengumuman hasil asesmen akan dirilis kemudian.
                            </div>
                        `;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: isViolated ? 'Ujian Dikumpulkan!' : 'Ujian Berhasil Dikumpulkan!',
                        html: finishHtml,
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: '<i class="fas fa-list-ul mr-1"></i> Kembali ke Daftar Ujian'
                    }).then(() => {
                        window.location.href = '<?= BASE_URL ?>siswa_portal/cbt';
                    });
                } else {
                    Swal.fire('Error', res.message || 'Terjadi kendala saat submit.', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        }

        // Inisialisasi awal saat halaman selesai dimuat
        $(document).ready(function() {
            initTimer();
            loadSoal(0);
        });
    </script>
</body>
</html>
