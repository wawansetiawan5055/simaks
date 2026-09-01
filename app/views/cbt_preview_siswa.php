<?php
// app/views/cbt_preview_siswa.php
// Tampilan Simulasi Ujian CBT dari Sudut Pandang Siswa (Full Interactive Player)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Simulasi Ujian Siswa CBT') ?> - SIMAKS</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Noto+Naskh+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- KaTeX for LaTeX rendering -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #0f172a;
            --bg-canvas: #f1f5f9;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: #1e293b;
            user-select: none;
            overflow-x: hidden;
        }

        /* Arabic & Multilingual support */
        .arabic-text, .question-text, .stimulus-box, .opsi-item {
            unicode-bidi: plaintext;
        }
        .arabic-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Traditional Arabic', serif !important;
            font-size: 1.35rem !important;
            line-height: 2.1 !important;
        }

        .cbt-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: #fff;
            padding: 12px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .font-adjust-btn {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .font-adjust-btn:hover {
            background: rgba(255,255,255,0.3);
            color: #fff;
        }

        .timer-badge {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #fca5a5;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
        }

        .question-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            min-height: calc(100vh - 160px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stimulus-box {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .option-item {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 18px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #ffffff;
            display: flex;
            align-items: flex-start;
        }
        .option-item:hover {
            border-color: #6366f1;
            background: #f5f3ff;
            transform: translateY(-1px);
        }
        .option-item.selected {
            border-color: #4f46e5;
            background: #eef2ff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }

        .option-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            margin-right: 14px;
            flex-shrink: 0;
            border: 1px solid #cbd5e1;
            transition: all 0.2s;
        }
        .option-item.selected .option-circle {
            background: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
        }

        .grid-number-btn {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 4px;
            border: 2px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
        }
        .grid-number-btn:hover {
            transform: scale(1.08);
            border-color: #4f46e5;
        }
        .grid-number-btn.active {
            border-color: #312e81;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.35);
            background: #e0e7ff;
            color: #1e1b4b;
        }
        .grid-number-btn.answered {
            background: #10b981;
            border-color: #059669;
            color: #ffffff;
        }
        .grid-number-btn.doubt {
            background: #f59e0b;
            border-color: #d97706;
            color: #ffffff;
        }
        .grid-number-btn .ans-sub {
            font-size: 0.65rem;
            font-weight: 900;
            margin-top: -2px;
            text-transform: uppercase;
        }

        .font-size-sm .question-text, .font-size-sm .stimulus-box, .font-size-sm .option-text { font-size: 0.95rem !important; }
        .font-size-md .question-text, .font-size-md .stimulus-box, .font-size-md .option-text { font-size: 1.1rem !important; }
        .font-size-lg .question-text, .font-size-lg .stimulus-box, .font-size-lg .option-text { font-size: 1.28rem !important; }
    </style>
</head>
<body class="font-size-md">

<!-- TOPBAR SIMULASI SISWA -->
<header class="cbt-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <!-- INFO UJIAN -->
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="bg-primary text-white p-2 rounded-lg mr-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #6366f1 !important;">
                    <i class="fas fa-laptop-code fa-lg"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-warning font-weight-bold mr-2 text-dark px-2 py-1">
                            <i class="fas fa-eye mr-1"></i> MODE SIMULASI SISWA
                        </span>
                        <span class="badge badge-light font-weight-bold text-dark px-2 py-1">
                            <?= htmlspecialchars($paket['tingkat'] ?? 'SMA') ?>
                        </span>
                    </div>
                    <h6 class="font-weight-bold mb-0 text-white mt-1">
                        <?= htmlspecialchars($paket['nama_mapel'] ?? 'Mata Pelajaran') ?> - <?= htmlspecialchars($paket['nama_paket'] ?? 'Naskah Asesmen') ?>
                    </h6>
                </div>
            </div>

            <!-- CONTROLS & TIMER -->
            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                <!-- PINTASAN KEYBOARD INFO -->
                <button type="button" class="btn btn-outline-light btn-sm rounded-pill font-weight-bold px-3" onclick="showKeyboardHelpModal()" title="Panduan Pintasan Keyboard">
                    <i class="fas fa-keyboard mr-1 text-warning"></i> Pintasan Tombol
                </button>

                <!-- RESIZE FONT -->
                <div class="d-flex align-items-center bg-dark p-1 rounded-pill" style="background: rgba(0,0,0,0.3) !important;">
                    <span class="text-white-50 small mr-2 ml-2 font-weight-bold">Font:</span>
                    <button type="button" class="font-adjust-btn" onclick="setFontSize('sm')">A-</button>
                    <button type="button" class="font-adjust-btn active" onclick="setFontSize('md')">A</button>
                    <button type="button" class="font-adjust-btn" onclick="setFontSize('lg')">A+</button>
                </div>

                <!-- TIMER COUNTDOWN -->
                <div class="timer-badge">
                    <i class="fas fa-stopwatch mr-2 text-danger"></i>
                    <span id="countdownTimer">01:29:59</span>
                </div>

                <!-- TOMBOL KELUAR SIMULASI -->
                <button type="button" class="btn btn-outline-light btn-sm rounded-pill font-weight-bold px-3 ml-1" onclick="exitSimulation()">
                    <i class="fas fa-times-circle mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</header>

<?php if (empty($soal_list)): ?>
    <div class="container py-5 text-center">
        <div class="card p-5 shadow-sm border-0 rounded-xl" style="border-radius: 20px;">
            <i class="fas fa-clipboard-list fa-4x text-muted mb-3 opacity-50"></i>
            <h4 class="font-weight-bold text-dark">Naskah Asesmen Masih Kosong</h4>
            <p class="text-muted">Belum ada butir soal yang dirakit ke dalam naskah ini.</p>
            <div>
                <button onclick="exitSimulation()" class="btn btn-primary rounded-pill px-4 font-weight-bold">Kembali ke Studio Perakitan</button>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- MAIN SIMULATOR CONTAINER -->
    <div class="container-fluid py-4 px-lg-4">
        <div class="row">
            <!-- PANEL KIRI: KONTEN SOAL AKTIF -->
            <div class="col-lg-8 col-xl-9 mb-4">
                <div class="question-card p-4">
                    <div>
                        <!-- HEADER NOMOR & TIPE SOAL -->
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-dark font-weight-bold px-3 py-2 rounded-pill mr-2" style="font-size: 1rem; background: #1e1b4b;" id="lblNoSoal">
                                    SOAL NO. 1
                                </span>
                                <span class="badge badge-info font-weight-bold px-3 py-2 rounded-pill" id="lblTipeSoal">
                                    PILIHAN GANDA
                                </span>
                            </div>
                            <div class="text-muted small">
                                Total Soal: <strong><?= count($soal_list) ?> Butir</strong>
                            </div>
                        </div>

                        <!-- KONTEN BUTIR SOAL (DYNAMIC CAROUSEL CONTAINER) -->
                        <?php foreach ($soal_list as $idx => $s): ?>
                            <div class="soal-item-view <?= $idx === 0 ? '' : 'd-none' ?>" id="soalView_<?= $idx ?>" data-id="<?= $s['id_soal'] ?>" data-tipe="<?= $s['tipe_soal'] ?>">
                                
                                <!-- WACANA TEKS -->
                                <?php if (!empty($s['stimulus'])): ?>
                                    <div class="stimulus-box small text-dark shadow-sm">
                                        <strong class="d-block text-primary mb-2"><i class="fas fa-book-reader mr-1"></i> Wacana / Informasi Pendukung:</strong>
                                        <div class="stimulus-text"><?= nl2br(format_cbt_math_output($s['stimulus'])) ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- MULTIMEDIA: GAMBAR / AUDIO / VIDEO -->
                                <?php if (!empty($s['media_url']) || $s['media_tipe'] === 'audio'): ?>
                                    <div class="mb-4">
                                        <?php if ($s['media_tipe'] === 'gambar' && !empty($s['media_url'])): ?>
                                            <div class="text-center p-2 bg-light rounded border">
                                                <img src="<?= htmlspecialchars($s['media_url']) ?>" class="img-fluid rounded border shadow-sm" style="max-height: 380px; object-fit: contain;" alt="Ilustrasi Soal">
                                            </div>
                                        <?php elseif ($s['media_tipe'] === 'audio'): ?>
                                            <div class="p-3 bg-light rounded border">
                                                <span class="small font-weight-bold text-dark d-block mb-2"><i class="fas fa-headphones text-primary mr-1"></i> Rekaman Audio Percakapan:</span>
                                                <?php if (!empty($s['media_url'])): ?>
                                                    <audio controls class="w-100 mb-2">
                                                        <source src="<?= htmlspecialchars($s['media_url']) ?>">
                                                    </audio>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="playStudentTts(<?= htmlspecialchars(json_encode($s['stimulus'] ?: $s['pertanyaan'])) ?>)">
                                                    <i class="fas fa-volume-up mr-1"></i> Putar Audio Listening
                                                </button>
                                            </div>
                                        <?php elseif ($s['media_tipe'] === 'video' && !empty($s['media_url'])): ?>
                                            <div class="embed-responsive embed-responsive-16by9 rounded shadow-sm" style="max-height: 380px;">
                                                <iframe class="embed-responsive-item" src="<?= htmlspecialchars($s['media_url']) ?>" allowfullscreen></iframe>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- PERTANYAAN -->
                                <div class="question-text text-dark font-weight-normal mb-4" style="line-height: 1.7;">
                                    <?= format_cbt_math_output($s['pertanyaan']) ?>
                                </div>

                                <!-- OPSI JAWABAN INTERAKTIF -->
                                <div class="opsi-container">
                                    <?php if ($s['tipe_soal'] === 'pg' && !empty($s['opsi_list'])): ?>
                                        <div class="row">
                                            <?php foreach ($s['opsi_list'] as $op): ?>
                                                <div class="col-12">
                                                    <div class="option-item" onclick="selectOption(<?= $idx ?>, '<?= $op['label'] ?>', this)">
                                                        <div class="option-circle"><?= $op['label'] ?></div>
                                                        <div class="option-text flex-grow-1 pt-1">
                                                            <?= format_cbt_math_output($op['isi_opsi']) ?>
                                                            <?php if (!empty($op['gambar'])): ?>
                                                                <div class="mt-2"><img src="<?= htmlspecialchars($op['gambar']) ?>" class="img-fluid rounded border" style="max-height: 140px;"></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($s['tipe_soal'] === 'tf'): ?>
                                        <div class="row">
                                            <div class="col-md-6 col-12 mb-2">
                                                <div class="option-item" onclick="selectOption(<?= $idx ?>, 'B', this)">
                                                    <div class="option-circle">B</div>
                                                    <div class="option-text flex-grow-1 pt-1 font-weight-bold text-success">BENAR (TRUE)</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 mb-2">
                                                <div class="option-item" onclick="selectOption(<?= $idx ?>, 'S', this)">
                                                    <div class="option-circle">S</div>
                                                    <div class="option-text flex-grow-1 pt-1 font-weight-bold text-danger">SALAH (FALSE)</div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif ($s['tipe_soal'] === 'essay'): ?>
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold small text-muted"><i class="fas fa-pen mr-1"></i> Tuliskan Jawaban / Pembahasan Solusi Anda:</label>
                                            <textarea class="form-control rounded-lg" rows="6" placeholder="Ketikkan jawaban uraian siswa di sini..." oninput="recordEssay(<?= $idx ?>, this.value)"></textarea>
                                        </div>
                                    <?php elseif ($s['tipe_soal'] === 'matching' && !empty($s['opsi_list'])): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped bg-white">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th style="width: 50%;">Pernyataan / Premis</th>
                                                        <th style="width: 50%;">Pasangan Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($s['opsi_list'] as $mIdx => $op): ?>
                                                        <tr>
                                                            <td class="align-middle font-weight-bold"><?= htmlspecialchars($op['label']) ?></td>
                                                            <td>
                                                                <select class="form-control form-control-sm rounded-pill" onchange="recordMatching(<?= $idx ?>, <?= $mIdx ?>, this.value)">
                                                                    <option value="">-- Pilih Pasangan --</option>
                                                                    <?php foreach ($s['opsi_list'] as $mSub): ?>
                                                                        <option value="<?= htmlspecialchars($mSub['isi_opsi']) ?>"><?= htmlspecialchars($mSub['isi_opsi']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- FOOTER NAVIGASI SOAL -->
                    <div class="border-top pt-3 mt-4 d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold border shadow-sm" id="btnPrev" onclick="navigateSoal(-1)">
                            <i class="fas fa-chevron-left mr-1"></i> Soal Sebelumnya
                        </button>
                        
                        <button type="button" class="btn btn-warning text-dark font-weight-bold rounded-pill px-4 shadow-sm" id="btnDoubt" onclick="toggleDoubt()">
                            <i class="fas fa-question-circle mr-1"></i> Ragu - Ragu
                        </button>

                        <button type="button" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" style="background: #4f46e5;" id="btnNext" onclick="navigateSoal(1)">
                            Soal Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: GRID NOMOR SOAL & STATISTIK -->
            <div class="col-lg-4 col-xl-3">
                <div class="card p-3 shadow-sm border-0 rounded-xl sticky-top" style="border-radius: 16px; top: 90px;">
                    <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-th mr-1 text-primary"></i> Lembar Nomor Soal</span>
                        <span class="badge badge-light border text-muted" id="answeredCounter">0 / <?= count($soal_list) ?></span>
                    </h6>

                    <!-- INDIKATOR STATUS WARNA -->
                    <div class="d-flex justify-content-between small text-muted mb-3 px-1">
                        <div><span class="badge badge-success px-2 py-1 mr-1">●</span> Terjawab</div>
                        <div><span class="badge badge-warning px-2 py-1 mr-1">●</span> Ragu</div>
                        <div><span class="badge badge-secondary px-2 py-1 mr-1">●</span> Belum</div>
                    </div>

                    <!-- GRID BUTTONS -->
                    <div class="d-flex flex-wrap justify-content-start mb-4" style="max-height: 360px; overflow-y: auto;">
                        <?php foreach ($soal_list as $idx => $s): ?>
                            <button type="button" class="grid-number-btn <?= $idx === 0 ? 'active' : '' ?>" id="gridBtn_<?= $idx ?>" onclick="jumpToSoal(<?= $idx ?>)">
                                <span><?= $idx + 1 ?></span>
                                <span class="ans-sub" id="gridAns_<?= $idx ?>">-</span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- TOMBOL SELESAI UJIAN SIMULASI -->
                    <button type="button" class="btn btn-danger font-weight-bold rounded-pill btn-block shadow-sm py-2" onclick="finishSimulation()">
                        <i class="fas fa-check-double mr-1"></i> Selesai Ujian (Simulasi)
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- JAVASCRIPT LOGIC INTERAKTIF SISWA -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const totalSoal = <?= count($soal_list) ?>;
let currentIndex = 0;
let studentAnswers = {};
let doubtStatus = {};

// Ganti Soal
function jumpToSoal(idx) {
    if (idx < 0 || idx >= totalSoal) return;
    
    // Hide current
    $('#soalView_' + currentIndex).addClass('d-none');
    $('#gridBtn_' + currentIndex).removeClass('active');

    // Show target
    currentIndex = idx;
    $('#soalView_' + currentIndex).removeClass('d-none');
    $('#gridBtn_' + currentIndex).addClass('active');

    // Update Header
    $('#lblNoSoal').text('SOAL NO. ' + (currentIndex + 1));
    const tipe = $('#soalView_' + currentIndex).data('tipe');
    const tipeMap = { 'pg': 'PILIHAN GANDA', 'essay': 'ESAI / URAIAN', 'tf': 'BENAR / SALAH', 'matching': 'MENJODOHKAN' };
    $('#lblTipeSoal').text(tipeMap[tipe] || 'SOAL ASESMEN');

    // Update Nav Buttons
    $('#btnPrev').prop('disabled', currentIndex === 0);
    if (currentIndex === totalSoal - 1) {
        $('#btnNext').html('Selesai <i class="fas fa-check-double ml-1"></i>').removeClass('btn-primary').addClass('btn-success');
    } else {
        $('#btnNext').html('Soal Selanjutnya <i class="fas fa-chevron-right ml-1"></i>').removeClass('btn-success').addClass('btn-primary');
    }

    // Scroll to top of question smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function navigateSoal(step) {
    if (currentIndex + step >= totalSoal && step > 0) {
        finishSimulation();
    } else {
        jumpToSoal(currentIndex + step);
    }
}

// Pilih Opsi Pilihan Ganda / TF
function selectOption(soalIdx, label, elem) {
    studentAnswers[soalIdx] = label;
    
    // UI update options
    $('#soalView_' + soalIdx + ' .option-item').removeClass('selected');
    $(elem).addClass('selected');

    // UI update grid
    updateGridStatus(soalIdx);
}

function recordEssay(soalIdx, text) {
    if (text.trim().length > 0) {
        studentAnswers[soalIdx] = 'ISI';
    } else {
        delete studentAnswers[soalIdx];
    }
    updateGridStatus(soalIdx);
}

function recordMatching(soalIdx, mIdx, val) {
    if (!studentAnswers[soalIdx]) studentAnswers[soalIdx] = {};
    studentAnswers[soalIdx][mIdx] = val;
    updateGridStatus(soalIdx);
}

// Toggle Ragu-Ragu
function toggleDoubt() {
    doubtStatus[currentIndex] = !doubtStatus[currentIndex];
    updateGridStatus(currentIndex);
}

function updateGridStatus(idx) {
    const isAnswered = studentAnswers[idx] !== undefined;
    const isDoubt = doubtStatus[idx] === true;
    const gridBtn = $('#gridBtn_' + idx);
    const ansSub = $('#gridAns_' + idx);

    gridBtn.removeClass('answered doubt');

    if (isDoubt) {
        gridBtn.addClass('doubt');
    } else if (isAnswered) {
        gridBtn.addClass('answered');
    }

    if (typeof studentAnswers[idx] === 'string' && studentAnswers[idx] !== 'ISI') {
        ansSub.text(studentAnswers[idx]);
    } else if (isAnswered) {
        ansSub.text('✓');
    } else {
        ansSub.text('-');
    }

    // Update count
    const totalAnswered = Object.keys(studentAnswers).length;
    $('#answeredCounter').text(totalAnswered + ' / ' + totalSoal);
}

// Text to Speech
function playStudentTts(text) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const cleanText = text.replace(/<[^>]*>?/gm, '').replace(/\$[^$]*\$/g, '');
        const utter = new SpeechSynthesisUtterance(cleanText);
        utter.lang = 'id-ID';
        utter.rate = 0.95;
        window.speechSynthesis.speak(utter);
    } else {
        alert('Browser Anda tidak mendukung Web Speech API Audio.');
    }
}

// Ukuran Font
function setFontSize(size) {
    $('body').removeClass('font-size-sm font-size-md font-size-lg').addClass('font-size-size-' + size).addClass('font-size-' + size);
    $('.font-adjust-btn').removeClass('active');
    event.target.classList.add('active');
}

// Selesai Simulasi
function finishSimulation() {
    const totalAnswered = Object.keys(studentAnswers).length;
    const totalDoubt = Object.values(doubtStatus).filter(Boolean).length;
    const totalUnanswered = totalSoal - totalAnswered;

    Swal.fire({
        title: 'Konfirmasi Selesai Simulasi',
        html: `
            <div class="text-left small mb-3">
                <div class="p-3 bg-light rounded mb-2">
                    <div>✅ <strong>Soal Terjawab:</strong> ${totalAnswered} Butir</div>
                    <div>⚠️ <strong>Soal Ragu-ragu:</strong> ${totalDoubt} Butir</div>
                    <div>⚪ <strong>Belum Terjawab:</strong> ${totalUnanswered} Butir</div>
                </div>
                <p class="text-muted mb-0">Ini adalah mode simulasi guru/admin untuk menguji kenyamanan naskah soal sebelum dibagikan ke siswa.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Kembali ke Studio Perakitan',
        cancelButtonText: 'Lanjut Menguji Soal',
        confirmButtonColor: '#4f46e5'
    }).then((result) => {
        if (result.isConfirmed) {
            exitSimulation();
        }
    });
}

function exitSimulation() {
    window.history.back();
}

// Countdown Timer Simulasi
let timeInSeconds = 90 * 60;
setInterval(() => {
    if (timeInSeconds > 0) {
        timeInSeconds--;
        const hours = Math.floor(timeInSeconds / 3600);
        const mins  = Math.floor((timeInSeconds % 3600) / 60);
        const secs  = timeInSeconds % 60;
        $('#countdownTimer').text(
            String(hours).padStart(2, '0') + ':' +
            String(mins).padStart(2, '0') + ':' +
            String(secs).padStart(2, '0')
        );
    }
}, 1000);

// Modal Panduan Pintasan Keyboard
function showKeyboardHelpModal() {
    Swal.fire({
        title: '<i class="fas fa-keyboard text-primary mr-2"></i> Panduan Pintasan Keyboard CBT',
        html: `
            <div class="text-left small">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40%;">Tombol</th>
                            <th>Fungsi Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><kbd>A</kbd>, <kbd>B</kbd>, <kbd>C</kbd>, <kbd>D</kbd>, <kbd>E</kbd></td>
                            <td>Memilih opsi jawaban Pilihan Ganda (PG) secara instan</td>
                        </tr>
                        <tr>
                            <td><kbd>B</kbd> / <kbd>S</kbd></td>
                            <td>Memilih Benar / Salah pada soal tipe Benar/Salah</td>
                        </tr>
                        <tr>
                            <td><kbd>←</kbd> (Panah Kiri) / <kbd>P</kbd> / <kbd>PageUp</kbd></td>
                            <td>Pindah ke butir soal sebelumnya</td>
                        </tr>
                        <tr>
                            <td><kbd>→</kbd> (Panah Kanan) / <kbd>N</kbd> / <kbd>PageDown</kbd></td>
                            <td>Pindah ke butir soal berikutnya</td>
                        </tr>
                        <tr>
                            <td><kbd>R</kbd> atau <kbd>Space</kbd></td>
                            <td>Menandai / membatalkan status <strong>Ragu-Ragu</strong></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2 text-muted" style="font-size: 0.8rem;">
                    <em>Catatan: Ketika sedang mengetik jawaban esai pada kotak teks, fungsi tombol huruf otomatis fokus pada pengetikan teks.</em>
                </div>
            </div>
        `,
        confirmButtonText: 'Tutup Panduan',
        confirmButtonColor: '#4f46e5'
    });
}

// =====================================================
// ANTI-CHEAT & SHORTCUT HANDLER
// =====================================================
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

document.addEventListener('copy', function(e) {
    e.preventDefault();
});
document.addEventListener('cut', function(e) {
    e.preventDefault();
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

let previewViolationCount = 0;
function handleTabSwitch() {
    previewViolationCount++;
    Swal.fire({
        icon: 'warning',
        title: `Peringatan Pelanggaran (${previewViolationCount})`,
        html: `
            <div class="text-danger font-weight-bold mb-2">Terdeteksi Berpindah Tab / Membuka Aplikasi Lain!</div>
            <p class="small text-muted mb-0">Pada sistem ujian siswa, aksi berpindah tab akan tercatat sebagai pelanggaran dan otomatis mengunci/mengumpulkan ujian jika melewati batas toleransi proktor.</p>
        `,
        confirmButtonText: 'Saya Mengerti, Lanjutkan',
        confirmButtonColor: '#4f46e5'
    });
}

document.addEventListener('visibilitychange', function() {
    if (document.hidden || document.visibilityState === 'hidden') {
        handleTabSwitch();
    }
});

// Event Listener Keyboard
document.addEventListener('keydown', function(e) {
    // 1. Blokir DevTools (F12)
    if (e.key === 'F12' || e.keyCode === 123) {
        e.preventDefault();
        showCheatAlert('Akses Developer Tools (F12) dinonaktifkan.');
        return false;
    }

    // 2. Blokir Inspect Element (Ctrl + Shift + I / J / C)
    if (e.ctrlKey && e.shiftKey && ['I', 'i', 'J', 'j', 'C', 'c'].includes(e.key)) {
        e.preventDefault();
        showCheatAlert('Kombinasi Inspect Element dinonaktifkan.');
        return false;
    }

    // 3. Blokir View Source (Ctrl + U)
    if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
        e.preventDefault();
        showCheatAlert('Akses Page Source dinonaktifkan.');
        return false;
    }

    // 4. Blokir Copy/Paste/Print/Save terlarang
    if (e.ctrlKey && ['c', 'C', 'v', 'V', 'x', 'X', 'a', 'A', 'p', 'P', 's', 'S'].includes(e.key)) {
        const isTextarea = document.activeElement && document.activeElement.tagName.toLowerCase() === 'textarea';
        if ((e.key === 'v' || e.key === 'V' || e.key === 'a' || e.key === 'A') && isTextarea) {
            return true;
        }
        e.preventDefault();
        showCheatAlert('Fungsi Copy/Paste/Print dinonaktifkan selama ujian.');
        return false;
    }

    // 5. Blokir PrintScreen & Bersihkan Clipboard
    if (e.key === 'PrintScreen') {
        e.preventDefault();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('');
        }
        showCheatAlert('Tangkapan layar (Screenshot) dilarang selama ujian.');
        return false;
    }

    // =====================================================
    // PINTASAN MENJAWAB & NAVIGASI SOAL DENGAN KEYBOARD
    // =====================================================
    const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
    const isEditingText = (activeTag === 'textarea' || (activeTag === 'input' && document.activeElement.type === 'text'));

    if (!isEditingText) {
        const keyUpper = e.key.toUpperCase();
        const currentContainer = $('#soalView_' + currentIndex);
        const tipe = currentContainer.data('tipe');

        // A. Jawab Opsi PG (A, B, C, D, E)
        if (['A', 'B', 'C', 'D', 'E'].includes(keyUpper) && tipe === 'pg') {
            const targetOption = currentContainer.find(`.option-item:has(.option-circle:contains('${keyUpper}'))`);
            if (targetOption.length > 0) {
                e.preventDefault();
                targetOption.trigger('click');
            }
        }

        // B. Jawab Benar / Salah (B / S)
        else if (tipe === 'tf' && (keyUpper === 'B' || keyUpper === 'S')) {
            const targetTf = currentContainer.find(`.option-item:has(.option-circle:contains('${keyUpper}'))`);
            if (targetTf.length > 0) {
                e.preventDefault();
                targetTf.trigger('click');
            }
        }

        // C. Navigasi Soal Sebelumnya (Panah Kiri, PageUp, P)
        else if (e.key === 'ArrowLeft' || e.key === 'PageUp' || keyUpper === 'P') {
            e.preventDefault();
            if (currentIndex > 0) {
                navigateSoal(-1);
            }
        }

        // D. Navigasi Soal Selanjutnya (Panah Kanan, PageDown, N)
        else if (e.key === 'ArrowRight' || e.key === 'PageDown' || keyUpper === 'N') {
            e.preventDefault();
            if (currentIndex < totalSoal - 1) {
                navigateSoal(1);
            }
        }

        // E. Toggle Ragu-Ragu (R atau Space)
        else if (keyUpper === 'R' || e.key === ' ') {
            e.preventDefault();
            toggleDoubt();
        }
    }
});

document.addEventListener("DOMContentLoaded", function() {
    if (typeof renderMathInElement === 'function') {
        renderMathInElement(document.body, {
            delimiters: [
                {left: '$$', right: '$$', display: true},
                {left: '$', right: '$', display: false},
                {left: '\\(', right: '\\)', display: false},
                {left: '\\[', right: '\\]', display: true}
            ],
            throwOnError: false
        });
    }
});
</script>

</body>
</html>
