<?php
if (!function_exists('format_cbt_math_output')) {
    function format_cbt_math_output($text) {
        if ($text === null || $text === '') return '';

        $tokens = [];
        $token_idx = 0;

        $wrap_token = function($str) use (&$tokens, &$token_idx) {
            $key = '___MATH_TK_' . ($token_idx++) . '___';
            $tokens[$key] = '$' . trim($str, '$') . '$';
            return $key;
        };

        // 1. Amankan yang sudah memiliki $...$ atau $$...$$
        $text = preg_replace_callback('/(\$\$[^\$]+\$\$|\$[^\$]+\$)/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        // 2. Wrap complete LaTeX commands with braces
        $text = preg_replace_callback('/\\\\(?:frac|sqrt|left|right|sum|int|lim|prod|binom|over|underline|overline|mathbf|text)\s*(?:\[[^\]]*\])?(?:\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\})+/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        // 3. Wrap caret parentheses exponents
        $text = preg_replace_callback('/([a-zA-Z0-9\)\.]+|\w+)\^\(([^)]+)\)/', function($m) use ($wrap_token) {
            return $wrap_token($m[1] . '^{' . $m[2] . '}');
        }, $text);

        // 4. Wrap simple single-token exponents
        $text = preg_replace_callback('/(?<![a-zA-Z0-9\$\_\@])([a-zA-Z0-9]+)\^([0-9a-zA-Z]+)(?![a-zA-Z0-9\$\_\@])/', function($m) use ($wrap_token) {
            return $wrap_token($m[1] . '^{' . $m[2] . '}');
        }, $text);

        // 5. Wrap single LaTeX words
        $text = preg_replace_callback('/\\\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|cdot|times|div|pm|mp|le|ge|ne|neq|approx|infty|forall|exists|in|notin|subset|subseteq|cup|cap|to|leftarrow|rightarrow|Rightarrow|leftrightarrow)\b/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        if (!empty($tokens)) {
            $text = strtr($text, $tokens);
        }

        $text = preg_replace('/\$\s*\$/', ' ', $text);
        $text = preg_replace('/\$\s*([\=\+\-\*\/])\s*\$/', ' $1 ', $text);

        return $text;
    }
}
include __DIR__ . '/partials/header.php'; 

$active_tingkat = strtoupper($_GET['tingkat'] ?? '');
$default_fase = ($active_tingkat === 'X') ? 'E' : (($active_tingkat === 'XI' || $active_tingkat === 'XII') ? 'F' : '');
?>

<!-- Math Engine: KaTeX & MathJax 3 for Crisp Mathematical Formulas -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
<!-- Arabic Fonts & Typography Support -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Noto+Naskh+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
<script>
window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']],
        processEscapes: true,
        processEnvironments: true
    }
};
</script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<style>
    /* Arabic & Multilingual Typography */
    .arabic-text, .soal-pertanyaan-box, .opsi-box, .wacana-stimulus-box, .form-control {
        unicode-bidi: plaintext;
    }
    .arabic-text {
        font-family: 'Amiri', 'Noto Naskh Arabic', 'Traditional Arabic', serif !important;
        font-size: 1.25rem !important;
        line-height: 2 !important;
    }

    /* Top Header */
    .bank-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
        flex-shrink: 0;
    }

    /* Segmented Grade Nav Tabs */
    .segmented-nav-wrap {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
    }
    .btn-grade-pill {
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 6px 16px;
        border: none;
        background: transparent;
        color: #64748b;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
    }
    .btn-grade-pill:hover {
        color: #1e293b;
        background: rgba(255, 255, 255, 0.7);
    }
    .btn-grade-pill.active {
        background: #ffffff !important;
        color: #4f46e5 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* Filter Card */
    .filter-panel-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        margin-bottom: 22px;
        overflow: hidden;
    }
    .filter-panel-header {
        padding: 12px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
    .filter-panel-body {
        padding: 16px 18px;
    }
    .custom-filter-input {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.86rem;
        color: #334155;
        transition: all 0.2s ease;
        height: 36px;
    }
    .custom-filter-input:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    /* Soal Card Items */
    .soal-card-item {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        overflow: hidden;
        border-left: 5px solid #cbd5e1;
    }
    .soal-card-item:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .soal-card-item.level-l1 {
        border-left-color: #10b981;
    }
    .soal-card-item.level-l2 {
        border-left-color: #0ea5e9;
    }
    .soal-card-item.level-l3 {
        border-left-color: #8b5cf6;
    }

    /* Question Number & Badges */
    .soal-num-badge {
        background: #0f172a;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.85rem;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
    }
    .soal-tag-pill {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }

    /* Option Boxes */
    .opsi-list-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 16px;
    }
    .opsi-box {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: #334155;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }
    .opsi-box:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .opsi-box.kunci-jawaban {
        background: #f0fdf4 !important;
        border-color: #22c55e !important;
        color: #15803d !important;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.12);
    }
    .opsi-label-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.88rem;
        background: #f1f5f9;
        color: #475569;
        margin-right: 14px;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .opsi-box.kunci-jawaban .opsi-label-badge {
        background: #22c55e;
        color: #ffffff;
        border-color: #22c55e;
    }

    /* Soft Buttons */
    .btn-soft-primary {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
        font-weight: 700;
    }
    .btn-soft-primary:hover {
        background: #2563eb;
        color: #ffffff;
    }
    .btn-soft-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        font-weight: 700;
    }
    .btn-soft-danger:hover {
        background: #dc2626;
        color: #ffffff;
    }
    .btn-soft-info {
        background: #f0fdfa;
        color: #0d9488;
        border: 1.5px solid #99f6e4;
        font-weight: 700;
    }
    .btn-soft-info:hover {
        background: #0d9488;
        color: #ffffff;
    }
    .btn-gradient-ai {
        background: linear-gradient(135deg, #4338ca, #6366f1);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        transition: all 0.2s ease;
    }
    .btn-gradient-ai:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <!-- TOP HEADER: TITLE LEFT + KEMBALI BUTTON RIGHT -->
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="bank-icon-box mr-3">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h4 class="m-0 font-weight-bold text-dark">
                    <?= htmlspecialchars($bank['nama_bank']) ?>
                </h4>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="index.php?mod=cbt_bank_soal" class="btn btn-sm shadow-sm font-weight-bold px-3 py-1.5 rounded-pill" style="background: #ffffff; border: 1.5px solid #94a3b8; color: #1e293b;">
                    <i class="fas fa-arrow-left text-primary mr-1" style="margin-right: 6px;"></i> Kembali ke Master Bank Soal
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- TOOLBAR: GRADE TABS (LEFT) + ACTION BUTTONS (RIGHT) -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center align-items-start mb-3" style="gap: 12px;">
            <!-- SEGMENTED GRADE TABS (LEFT) -->
            <div class="segmented-nav-wrap mb-0">
                <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $bank['id_bank'] ?>" class="btn-grade-pill <?= empty($active_tingkat) ? 'active' : '' ?>">
                    <i class="fas fa-globe mr-2" style="margin-right: 8px;"></i> Semua Tingkat
                </a>
                <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $bank['id_bank'] ?>&tingkat=X" class="btn-grade-pill <?= ($active_tingkat === 'X') ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard mr-2" style="margin-right: 8px;"></i> Kelas X (Fase E)
                </a>
                <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $bank['id_bank'] ?>&tingkat=XI" class="btn-grade-pill <?= ($active_tingkat === 'XI') ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard mr-2" style="margin-right: 8px;"></i> Kelas XI (Fase F)
                </a>
                <a href="index.php?mod=cbt_bank_soal&act=detail&id_bank=<?= $bank['id_bank'] ?>&tingkat=XII" class="btn-grade-pill <?= ($active_tingkat === 'XII') ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard mr-2" style="margin-right: 8px;"></i> Kelas XII (Fase F)
                </a>
            </div>

            <!-- ACTION BUTTONS (RIGHT) -->
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <a href="index.php?mod=cbt_bank_soal&act=preview_siswa&id_bank=<?= $bank['id_bank'] ?><?= !empty($active_tingkat) ? '&tingkat=' . htmlspecialchars($active_tingkat) : '' ?>" target="_blank" class="btn btn-soft-info btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-desktop mr-1"></i> 👁️ Simulasi Siswa
                </a>
                <?php if ($can_edit): ?>
                    <button type="button" class="btn btn-sm rounded-pill px-3 shadow-sm font-weight-bold" style="background: #eff6ff; border: 1.5px solid #93c5fd; color: #2563eb;" data-toggle="modal" data-target="#modalImportWord">
                        <i class="fas fa-file-word mr-1 text-primary"></i> 📄 Import Word (.docx AI)
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 shadow-sm font-weight-bold" style="background: #f0fdf4; border: 1.5px solid #86efac; color: #16a34a;" data-toggle="modal" data-target="#modalImportExcel">
                        <i class="fas fa-file-excel mr-1 text-success"></i> 📥 Import Excel
                    </button>
                    <button type="button" class="btn btn-gradient-ai btn-sm rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#modalAiGenerator">
                        <i class="fas fa-magic mr-1"></i> ✨ AI Generator Soal
                    </button>
                    <a href="index.php?mod=cbt_bank_soal&act=create_soal&id_bank=<?= $bank['id_bank'] ?>" class="btn btn-primary btn-sm font-weight-bold rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Butir Manual
                    </a>
                <?php else: ?>
                    <span class="badge badge-light border text-muted px-3 py-2 rounded-pill font-weight-bold">
                        <i class="fas fa-eye mr-1"></i> Read-Only Mode
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- MULTI-LEVEL DYNAMIC CASCADING FILTER BAR (1 ROW CLEAN) -->
        <div class="card filter-panel-card">
            <div class="filter-panel-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-filter text-primary mr-2"></i>
                    <h6 class="font-weight-bold text-dark mb-0 small text-uppercase" style="letter-spacing: 0.5px;">
                        Filter Hierarki Kurikulum &amp; Karakteristik Soal
                    </h6>
                </div>
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <span class="badge badge-light border text-muted px-2.5 py-1.5 rounded-pill font-weight-bold small">
                        Menampilkan <strong id="filtered_count" class="text-primary"><?= count($soal_list) ?></strong> dari <?= count($soal_list) ?> Soal
                    </span>
                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="resetAllFilters()">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
            </div>

            <div class="filter-panel-body">
                <div class="row" style="row-gap: 10px;">
                    <!-- Filter CP -->
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-graduation-cap mr-1 text-secondary"></i> Capaian Pembelajaran (CP)</label>
                        <select id="filter_cp" class="form-control custom-filter-input" onchange="onCpFilterChange()">
                            <option value="">Semua CP</option>
                            <?php foreach ($cp_list as $cp): ?>
                                <option value="<?= $cp['id_cp'] ?>" data-fase="<?= htmlspecialchars($cp['fase'] ?? '') ?>">
                                    <?= htmlspecialchars((!empty($cp['fase']) ? '[Fase ' . $cp['fase'] . '] ' : '') . mb_strimwidth($cp['deskripsi_cp'], 0, 45, '...')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter TP -->
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-bullseye mr-1 text-secondary"></i> Tujuan Pembelajaran (TP)</label>
                        <select id="filter_tp" class="form-control custom-filter-input" onchange="applyCascadingFilter()">
                            <option value="">Semua TP</option>
                            <?php foreach ($tp_list as $tp): ?>
                                <option value="<?= $tp['id_tp'] ?>" data-cp="<?= $tp['id_cp'] ?>">
                                    <?= htmlspecialchars(($tp['kode_tp'] ? $tp['kode_tp'] . ': ' : '') . mb_strimwidth($tp['deskripsi_tp'], 0, 40, '...')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Topik Materi -->
                    <div class="col-lg-2 col-md-4 col-12">
                        <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-book-open mr-1 text-secondary"></i> Topik / Materi</label>
                        <select id="filter_materi" class="form-control custom-filter-input" onchange="applyCascadingFilter()">
                            <option value="">Semua Topik</option>
                            <?php foreach ($materi_list as $mat): ?>
                                <option value="<?= htmlspecialchars($mat) ?>"><?= htmlspecialchars($mat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Bentuk Soal -->
                    <div class="col-lg-2 col-md-4 col-12">
                        <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-tasks mr-1 text-secondary"></i> Bentuk Soal</label>
                        <select id="filter_tipe" class="form-control custom-filter-input" onchange="applyCascadingFilter()">
                            <option value="">Semua Bentuk</option>
                            <option value="pg">Pilihan Ganda (PG)</option>
                            <option value="essay">Esai / Uraian</option>
                            <option value="tf">Benar / Salah (B/S)</option>
                            <option value="matching">Menjodohkan</option>
                        </select>
                    </div>

                    <!-- Filter Level Kognitif -->
                    <div class="col-lg-2 col-md-4 col-12">
                        <label class="small text-muted font-weight-bold mb-1"><i class="fas fa-brain mr-1 text-secondary"></i> Level Kognitif</label>
                        <select id="filter_level" class="form-control custom-filter-input" onchange="applyCascadingFilter()">
                            <option value="">Semua Level</option>
                            <option value="L1">L1 - Pengetahuan (LOTS)</option>
                            <option value="L2">L2 - Aplikasi (MOTS)</option>
                            <option value="L3">L3 - Penalaran / HOTS</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR BUTIR SOAL -->
        <?php if (empty($soal_list)): ?>
            <div class="card p-5 text-center shadow-sm border-0" style="border-radius: 18px; background: #ffffff;">
                <div class="mb-3">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 2rem;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold text-dark">Belum Ada Butir Soal</h5>
                <p class="text-muted small mb-3">Gunakan <strong>AI Generator Soal</strong> atau tombol <strong>Tambah Manual</strong> untuk mulai mengisi bank soal ini.</p>
                <?php if ($can_edit): ?>
                    <div>
                        <button type="button" class="btn btn-gradient-ai btn-sm rounded-pill px-4 shadow-sm mr-2" data-toggle="modal" data-target="#modalAiGenerator">
                            <i class="fas fa-magic mr-1"></i> Generate Soal AI
                        </button>
                        <a href="index.php?mod=cbt_bank_soal&act=create_soal&id_bank=<?= $bank['id_bank'] ?>" class="btn btn-primary btn-sm font-weight-bold rounded-pill px-4 shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Tambah Manual
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div id="soalContainer">
                <?php foreach ($soal_list as $i => $s): ?>
                    <?php 
                        $lvl = strtoupper($s['level_kognitif'] ?? 'L2');
                        $is_hots = ($lvl === 'L3' || strpos($lvl, 'HOTS') !== false);
                        $lvl_class = $is_hots ? 'level-l3' : (($lvl === 'L1') ? 'level-l1' : 'level-l2');
                        $lvl_display = $is_hots ? 'L3 (HOTS)' : (($lvl === 'L1') ? 'L1 (LOTS)' : 'L2 (MOTS)');
                        $lvl_badge_bg = $is_hots ? 'background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;' : (($lvl === 'L1') ? 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;' : 'background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;');
                        $fase_val = $s['fase_cp'] ?? (in_array($bank['tingkat'], ['X', '10']) ? 'E' : 'F');
                    ?>
                    <div class="soal-card-item card-item-filter <?= $lvl_class ?>"
                         id="soal_box_<?= $s['id_soal'] ?>"
                         data-fase="<?= htmlspecialchars($fase_val) ?>"
                         data-cp="<?= (int)($s['id_cp'] ?? 0) ?>"
                         data-tp="<?= (int)($s['id_tp'] ?? 0) ?>"
                         data-materi="<?= htmlspecialchars($s['lingkup_materi'] ?? '') ?>"
                         data-tipe="<?= htmlspecialchars($s['tipe_soal']) ?>"
                         data-level="<?= htmlspecialchars($lvl) ?>">

                        <div class="card-body p-4">
                            <!-- HEADER BUTIR SOAL -->
                            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                    <span class="soal-num-badge">
                                        No. <?= $i + 1 ?>
                                    </span>
                                    <span class="soal-tag-pill" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                                        <?= ($s['tipe_soal'] === 'pg') ? 'Pilihan Ganda' : (($s['tipe_soal'] === 'essay') ? 'Esai / Uraian' : (($s['tipe_soal'] === 'tf') ? 'Benar / Salah' : 'Menjodohkan')) ?>
                                    </span>
                                    <span class="soal-tag-pill" style="<?= $lvl_badge_bg ?>">
                                        <?= $lvl_display ?>
                                    </span>
                                    <?php if (!empty($fase_val)): ?>
                                        <span class="soal-tag-pill" style="background: #fdf4ff; color: #c026d3; border: 1px solid #f5d0fe;">
                                            Fase <?= htmlspecialchars($fase_val) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($s['lingkup_materi'])): ?>
                                        <span class="soal-tag-pill" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                                            📖 <?= htmlspecialchars($s['lingkup_materi']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($can_edit): ?>
                                    <div class="d-flex align-items-center" style="gap: 6px;">
                                        <a href="index.php?mod=cbt_bank_soal&act=edit_soal&id=<?= $s['id_soal'] ?>&id_bank=<?= $bank['id_bank'] ?>" class="btn btn-xs btn-soft-primary rounded-pill px-3 py-1 font-weight-bold">
                                            <i class="fas fa-pen mr-1"></i> Edit
                                        </a>
                                        <a href="index.php?mod=cbt_bank_soal&act=delete_soal&id=<?= $s['id_soal'] ?>&id_bank=<?= $bank['id_bank'] ?>" class="btn btn-xs btn-soft-danger rounded-pill px-3 py-1 font-weight-bold" onclick="return confirm('Apakah Anda yakin ingin menghapus butir soal ini?')">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- INDIKATOR KISI-KISI SOAL -->
                            <?php if (!empty($s['indikator_soal'])): ?>
                                <div class="p-3 mb-3 rounded-lg small shadow-sm" style="background: #f5f3ff; border: 1px solid #e0e7ff; border-left: 4px solid #6366f1; color: #3730a3;">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-bullseye mt-1 mr-2" style="color: #6366f1;"></i>
                                        <div>
                                            <strong style="color: #4338ca;">Indikator Asesmen:</strong>
                                            <span class="indikator-text ml-1" style="color: #1e1b4b;"><?= format_cbt_math_output($s['indikator_soal']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- BACAAN / WACANA TEKS PENDUKUNG (STIMULUS) -->
                            <?php if (!empty($s['stimulus'])): ?>
                                <div class="p-3 mb-3 rounded-lg small text-dark shadow-sm stimulus-text" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0284c7; line-height: 1.7;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-book-reader mr-2 text-info"></i>
                                        <strong class="text-info">Teks Wacana / Informasi Pendukung:</strong>
                                    </div>
                                    <div class="pl-4">
                                        <?= nl2br(format_cbt_math_output($s['stimulus'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- MEDIA MULTIMEDIA (AUDIO / VIDEO / GAMBAR) -->
                            <?php if (!empty($s['media_url']) || $s['media_tipe'] === 'audio'): ?>
                                <div class="mb-3">
                                    <?php if ($s['media_tipe'] === 'gambar' && !empty($s['media_url'])): ?>
                                        <div class="mb-2 p-3 bg-light rounded-lg border text-center position-relative">
                                            <span class="small font-weight-bold text-dark d-block mb-2 text-left"><i class="fas fa-image text-primary mr-1"></i> Ilustrasi / Diagram Pendukung:</span>
                                            <img src="<?= htmlspecialchars($s['media_url']) ?>" 
                                                 class="img-fluid rounded border shadow-sm cbt-stimulus-img" 
                                                 style="max-height: 360px; object-fit: contain; min-height: 120px; background: #ffffff;" 
                                                 loading="lazy"
                                                 onerror="let img=this; setTimeout(function(){ img.src = img.src.split('&t=')[0] + '&t=' + new Date().getTime(); }, 2500);" 
                                                 alt="Ilustrasi Pendukung">
                                        </div>
                                    <?php elseif ($s['media_tipe'] === 'audio'): ?>
                                        <div class="p-3 bg-light rounded-lg border">
                                            <span class="small font-weight-bold text-dark d-block mb-2"><i class="fas fa-headphones text-primary mr-1"></i> Rekaman Audio / Percakapan:</span>
                                            <?php if (!empty($s['media_url'])): ?>
                                                <audio controls class="w-100 mb-2">
                                                    <source src="<?= htmlspecialchars($s['media_url']) ?>">
                                                </audio>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 font-weight-bold" onclick="playTts(<?= htmlspecialchars(json_encode($s['stimulus'] ?: $s['pertanyaan'])) ?>)">
                                                <i class="fas fa-volume-up mr-1"></i> 🔊 Putar Suara Audio (AI Voice TTS)
                                            </button>
                                        </div>
                                    <?php elseif ($s['media_tipe'] === 'video' && !empty($s['media_url'])): ?>
                                        <div class="p-2 bg-light rounded-lg border mb-2">
                                            <span class="small font-weight-bold text-dark d-block mb-2"><i class="fas fa-video text-danger mr-1"></i> Observasi Video Fenomena:</span>
                                            <div class="embed-responsive embed-responsive-16by9 rounded shadow-sm" style="max-height: 340px;">
                                                <iframe class="embed-responsive-item" src="<?= htmlspecialchars($s['media_url']) ?>" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- TEKS PERTANYAAN -->
                            <div class="pertanyaan-content text-dark mb-3.5 pertanyaan-text-target" style="font-size: 1.05rem; font-weight: 500; line-height: 1.7; color: #0f172a;">
                                <?= format_cbt_math_output($s['pertanyaan']) ?>
                            </div>

                            <!-- OPSI PILIHAN GANDA (PG) - FULL WIDTH CLEAN STACK -->
                            <?php if ($s['tipe_soal'] === 'pg' && !empty($s['opsi_list'])): ?>
                                <div class="opsi-list-wrapper">
                                    <?php foreach ($s['opsi_list'] as $o): ?>
                                        <div class="opsi-box <?= $o['is_benar'] ? 'kunci-jawaban' : '' ?>">
                                            <span class="opsi-label-badge"><?= $o['label'] ?></span>
                                            <div class="flex-grow-1">
                                                <?= format_cbt_math_output($o['isi_opsi']) ?>
                                                <?php if (!empty($o['gambar'])): ?>
                                                    <div class="mt-2">
                                                        <img src="<?= htmlspecialchars($o['gambar']) ?>" style="max-height: 80px;" class="img-thumbnail" alt="Opsi Gambar">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($o['is_benar']): ?>
                                                <span class="badge badge-success px-3 py-1.5 rounded-pill font-weight-bold ml-3 flex-shrink-0">
                                                    <i class="fas fa-check-circle mr-1"></i> Kunci Benar
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- OPSI BENAR / SALAH (TF) -->
                            <?php if ($s['tipe_soal'] === 'tf'): ?>
                                <div class="p-3 rounded-lg border mb-3 small font-weight-bold text-dark d-flex align-items-center" style="background: #f8fafc;">
                                    <span class="mr-2">Kunci Jawaban Valid:</span>
                                    <span class="badge badge-success px-3 py-1.5 rounded-pill font-weight-bold"><?= ($s['kunci_jawaban'] === 'B' || $s['kunci_jawaban'] === 'BENAR') ? 'BENAR' : 'SALAH' ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- MENJODOHKAN (MATCHING) -->
                            <?php if ($s['tipe_soal'] === 'matching' && !empty($s['opsi_list'])): ?>
                                <div class="p-3 bg-light rounded-lg border mb-3">
                                    <span class="small font-weight-bold text-dark d-block mb-2"><i class="fas fa-exchange-alt text-primary mr-1"></i> Pasangan Kunci Menjodohkan:</span>
                                    <div class="row">
                                        <?php foreach ($s['opsi_list'] as $mi => $mo): ?>
                                            <div class="col-md-6 col-12 mb-1.5 small">
                                                <strong><?= $mi + 1 ?>. <?= htmlspecialchars($mo['label']) ?></strong> &rarr; <span class="badge badge-light border text-dark"><?= htmlspecialchars($mo['isi_opsi']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- PEMBAHASAN & RUBRIK PENSKORAN (ACCORDION STYLE / CLEAN CALLOUT) -->
                            <?php if (!empty($s['pembahasan']) || !empty($s['rubrik_penilaian'])): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <div class="d-flex align-items-center mb-2">
                                        <button class="btn btn-xs btn-light border rounded-pill px-3 py-1 text-muted font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseSolusi_<?= $s['id_soal'] ?>" aria-expanded="true">
                                            <i class="fas fa-lightbulb text-warning mr-1"></i> Pembahasan &amp; Pedoman Penskoran <i class="fas fa-chevron-down ml-1 small"></i>
                                        </button>
                                    </div>
                                    <div class="collapse show" id="collapseSolusi_<?= $s['id_soal'] ?>">
                                        <div class="p-3 rounded-lg small" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <?php if (!empty($s['pembahasan'])): ?>
                                                <div class="mb-2">
                                                    <span class="font-weight-bold text-info d-block mb-1">
                                                        <i class="fas fa-lightbulb mr-1"></i> Pembahasan &amp; Solusi:
                                                    </span>
                                                    <div class="text-secondary pl-3" style="line-height: 1.6;">
                                                        <?= nl2br(format_cbt_math_output($s['pembahasan'])) ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($s['rubrik_penilaian'])): ?>
                                                <div>
                                                    <span class="font-weight-bold text-success d-block mb-1">
                                                        <i class="fas fa-clipboard-check mr-1"></i> Pedoman Penskoran:
                                                    </span>
                                                    <div class="text-secondary pl-3" style="line-height: 1.6;">
                                                        <?= nl2br(format_cbt_math_output($s['rubrik_penilaian'])) ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- MODAL SMART AI GENERATOR SOAL (KURIKULUM MERDEKA) -->
<div class="modal fade" id="modalAiGenerator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #312e81, #4f46e5); border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <div>
                    <h5 class="modal-title font-weight-bold mb-1">
                        <i class="fas fa-robot mr-2"></i> ✨ AI Generator Soal Asesmen Pintar
                    </h5>
                    <p class="small text-light mb-0 opacity-75">
                        Produksi otomatis butir soal berstandar Kurikulum Merdeka (Stimulus, Kisi-Kisi, Kartu Soal, Rubrik &amp; Kunci Jawaban)
                    </p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <form id="formAiGen" onsubmit="submitAiGenerator(event)">
                <input type="hidden" name="id_bank" value="<?= $bank['id_bank'] ?>">
                <div class="modal-body p-4" style="max-height: 72vh; overflow-y: auto;">
                    <!-- BAGIAN 1: TARGET KURIKULUM -->
                    <div class="p-3 bg-light rounded-lg border mb-3">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-graduation-cap text-primary mr-1"></i> 1. Penyelarasan Target Kurikulum
                        </h6>
                        <div class="row" style="row-gap: 12px;">
                            <div class="col-md-4 col-12">
                                <label class="font-weight-bold small text-dark">Tingkat Kelas / Fase</label>
                                <select name="fase_tingkat" id="ai_fase_tingkat" class="form-control" onchange="filterAiCpDropdown()">
                                    <option value="X" <?= ($active_tingkat === 'X' || $bank['tingkat'] === 'X') ? 'selected' : '' ?>>Kelas X (Fase E)</option>
                                    <option value="XI" <?= ($active_tingkat === 'XI' || $bank['tingkat'] === 'XI') ? 'selected' : '' ?>>Kelas XI (Fase F)</option>
                                    <option value="XII" <?= ($active_tingkat === 'XII' || $bank['tingkat'] === 'XII') ? 'selected' : '' ?>>Kelas XII (Fase F)</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-12">
                                <label class="font-weight-bold small text-dark">Capaian Pembelajaran (CP Target)</label>
                                <select name="id_cp" id="ai_id_cp" class="form-control" onchange="filterAiTpDropdown()">
                                    <option value="">-- Pilih Capaian Pembelajaran (CP) --</option>
                                    <?php foreach ($cp_list as $cp): ?>
                                        <option value="<?= $cp['id_cp'] ?>" data-fase="<?= htmlspecialchars($cp['fase'] ?? '') ?>">
                                            <?= htmlspecialchars((!empty($cp['fase']) ? '[Fase ' . $cp['fase'] . '] ' : '') . mb_strimwidth($cp['deskripsi_cp'], 0, 75, '...')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="font-weight-bold small text-dark">Tujuan Pembelajaran (TP Rujukan)</label>
                                <select name="id_tp" id="ai_id_tp" class="form-control" onchange="autoFillAiMateri(this)">
                                    <option value="">-- Pilih Tujuan Pembelajaran (TP) --</option>
                                    <?php foreach ($tp_list as $tp): ?>
                                        <option value="<?= $tp['id_tp'] ?>" data-cp="<?= $tp['id_cp'] ?>" data-materi="<?= htmlspecialchars($tp['materi_pokok'] ?? '') ?>">
                                            <?= htmlspecialchars(($tp['kode_tp'] ? $tp['kode_tp'] . ': ' : '') . mb_strimwidth($tp['deskripsi_tp'], 0, 50, '...')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="font-weight-bold small text-dark">Topik / Lingkup Materi Pokok</label>
                                <input type="text" name="topik" id="ai_topik" class="form-control" placeholder="Contoh: Barisan dan Deret Geometri..." required>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: KARAKTERISTIK SOAL & LEVEL KOGNITIF -->
                    <div class="p-3 bg-light rounded-lg border mb-3">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-sliders-h text-primary mr-1"></i> 2. Karakteristik Butir Soal Asesmen
                        </h6>
                        <div class="row" style="row-gap: 12px;">
                            <div class="col-md-4 col-12">
                                <label class="font-weight-bold small text-dark">Bentuk Soal</label>
                                <select name="tipe_soal" class="form-control">
                                    <option value="pg" selected>Pilihan Ganda (PG 5 Opsi)</option>
                                    <option value="essay">Esai / Uraian Terbuka</option>
                                    <option value="tf">Benar / Salah (B/S)</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="font-weight-bold small text-dark">Level Kognitif &amp; Tingkat Berpikir</label>
                                <select name="level_kognitif" class="form-control">
                                    <option value="L2">L2 - MOTS (Aplikasi &amp; Pemahaman)</option>
                                    <option value="L3" selected>L3 - HOTS (Analisis, Evaluasi &amp; Penalaran)</option>
                                    <option value="L1">L1 - LOTS (Mengingat &amp; Mengingat Kembali)</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="font-weight-bold small text-dark">Jumlah Soal</label>
                                <select name="jumlah_soal" class="form-control">
                                    <option value="1">1 Butir Soal</option>
                                    <option value="3">3 Butir Soal Sekaligus</option>
                                    <option value="5" selected>5 Butir Soal Sekaligus</option>
                                    <option value="10">10 Butir Soal Sekaligus</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="font-weight-bold small text-dark">
                                    🎯 Intervensi Khusus / Fokus Kategori Soal (Opsional)
                                </label>
                                <textarea name="fokus_khusus" class="form-control" rows="2" placeholder="Contoh: Fokus pada studi kasus kehidupan siswa SMA, tekankan perhitungan grafik, sertakan analisis perbandingan data numerasi..."></textarea>
                                <small class="text-muted">Ketikkan instruksi spesifik apa pun agar AI merumuskan soal sesuai gaya dan target pengujian Anda.</small>
                            </div>
                        </div>
                    </div>

                    <!-- LOADING STATE -->
                    <div id="aiLoadingIndicator" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h6 class="font-weight-bold text-dark">Sedang Merumuskan Butir Soal Asesmen...</h6>
                        <p class="small text-muted mb-0">AI sedang menyusun wacana stimulus, butir pertanyaan, indikator kisi-kisi, kartu soal, rubrik penskoran, dan memformat notasi matematika LaTeX.</p>
                    </div>
                </div>

                <div class="modal-footer bg-light" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitAi" class="btn btn-gradient-ai font-weight-bold rounded-pill px-4 shadow-sm">
                        <i class="fas fa-magic mr-1"></i> Mulai Generate Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL / CSV -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" role="dialog" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
                <div class="d-flex align-items-center">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.2); display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-right: 12px;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalImportExcelLabel">Import Butir Soal dari Excel</h5>
                        <small class="text-white-50">Unggah butir soal secara massal dari format spreadsheet</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form method="POST" action="index.php?mod=cbt_bank_soal&act=import_excel" enctype="multipart/form-data">
                <input type="hidden" name="id_bank" value="<?= (int)$bank['id_bank'] ?>">
                
                <div class="modal-body p-4">
                    <!-- DOWNLOAD TEMPLATE BOX -->
                    <div class="p-3 rounded-lg border mb-3" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-success mr-2" style="font-size: 1.2rem;"></i>
                                <div class="small">
                                    <strong class="text-dark d-block">Gunakan Template Resmi</strong>
                                    <span class="text-muted">Unduh file template Excel terstandarisasi sebelum mengunggah.</span>
                                </div>
                            </div>
                            <a href="index.php?mod=cbt_bank_soal&act=template_excel" class="btn btn-sm btn-success rounded-pill font-weight-bold px-3 shadow-sm">
                                <i class="fas fa-download mr-1"></i> Download Template (.xlsx)
                            </a>
                        </div>
                    </div>

                    <!-- FILE INPUT -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Pilih File Spreadsheet (.xlsx / .xls / .csv):</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file_excel" name="file_excel" accept=".xlsx, .xls, .csv" required onchange="$(this).next('.custom-file-label').html(this.files[0].name)">
                            <label class="custom-file-label" for="file_excel">Pilih file spreadsheet...</label>
                        </div>
                        <small class="text-muted d-block mt-1">Maksimal ukuran file: 5MB.</small>
                    </div>

                    <!-- PETUNJUK RINGKAS -->
                    <div class="p-3 bg-light rounded border small text-muted">
                        <strong class="text-dark d-block mb-1"><i class="fas fa-lightbulb text-warning mr-1"></i> Petunjuk Pengisian:</strong>
                        <ul class="pl-3 mb-0" style="line-height: 1.6;">
                            <li>Kolom <strong>Tipe Soal</strong> dapat diisi: <code>pg</code> (Pilihan Ganda), <code>essay</code> (Esai), atau <code>tf</code> (Benar/Salah).</li>
                            <li>Untuk rumus matematika atau simbol kimia, gunakan format LaTeX dengan tanda dollar (contoh: <code>$\frac{a}{b}$</code> atau <code>$x^2$</code>).</li>
                            <li>Kolom <strong>Kunci Jawaban</strong> diisi huruf (<code>A</code>, <code>B</code>, <code>C</code>, <code>D</code>, atau <code>E</code>) untuk PG, atau <code>B</code>/<code>S</code> untuk Benar/Salah.</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Mulai Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT WORD (.DOCX) DENGAN AI PARSER -->
<div class="modal fade" id="modalImportWord" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255,255,255,0.2); display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-right: 12px;">
                        <i class="fas fa-file-word"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0">Import Naskah Soal dari Microsoft Word (.docx)</h5>
                        <small class="text-white-50">Ekstraksi cerdas berbasis AI: mendeteksi butir soal, opsi A-E, kunci jawaban, dan level kognitif</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <form id="formImportWord" onsubmit="submitWordImportPreview(event)" enctype="multipart/form-data">
                <input type="hidden" name="id_bank" value="<?= (int)$bank['id_bank'] ?>">

                <div class="modal-body p-4">
                    <!-- TARGET KURIKULUM (OPSIONAL) -->
                    <div class="p-3 bg-light rounded-lg border mb-3">
                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2 small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="fas fa-tags text-primary mr-1"></i> Penyelarasan CP &amp; TP (Opsional)
                        </h6>
                        <div class="row" style="row-gap: 10px;">
                            <div class="col-md-6 col-12">
                                <label class="small font-weight-bold text-dark mb-1">Capaian Pembelajaran (CP)</label>
                                <select name="id_cp" id="word_id_cp" class="form-control form-control-sm custom-filter-input" onchange="filterWordTpDropdown()">
                                    <option value="">-- Otomatis / Sesuai Dokumen --</option>
                                    <?php foreach ($cp_list as $cp): ?>
                                        <option value="<?= $cp['id_cp'] ?>" data-fase="<?= htmlspecialchars($cp['fase'] ?? '') ?>">
                                            <?= htmlspecialchars((!empty($cp['fase']) ? '[Fase ' . $cp['fase'] . '] ' : '') . mb_strimwidth($cp['deskripsi_cp'], 0, 50, '...')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="small font-weight-bold text-dark mb-1">Tujuan Pembelajaran (TP)</label>
                                <select name="id_tp" id="word_id_tp" class="form-control form-control-sm custom-filter-input">
                                    <option value="">-- Otomatis / Sesuai Dokumen --</option>
                                    <?php foreach ($tp_list as $tp): ?>
                                        <option value="<?= $tp['id_tp'] ?>" data-cp="<?= $tp['id_cp'] ?>">
                                            <?= htmlspecialchars(($tp['kode_tp'] ? $tp['kode_tp'] . ': ' : '') . mb_strimwidth($tp['deskripsi_tp'], 0, 45, '...')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- DROPZONE / FILE INPUT -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Pilih File Dokumen Microsoft Word (.docx) <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file_docx" name="file_docx" accept=".docx" required onchange="$(this).next('.custom-file-label').html(this.files[0].name)">
                            <label class="custom-file-label" for="file_docx">Pilih file naskah soal .docx...</label>
                        </div>
                        <small class="text-muted d-block mt-1">Mendukung file dokumen Word berekstensi <code>.docx</code> standar (Maksimal 15 MB).</small>
                    </div>

                    <!-- FITUR UNGGULAN BOX -->
                    <div class="p-3 rounded-lg border mb-0" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                        <strong class="text-dark d-block mb-1 font-weight-bold small"><i class="fas fa-sparkles text-warning mr-1"></i> Kemampuan AI Parser Word:</strong>
                        <ul class="pl-3 mb-0 small text-muted" style="line-height: 1.6;">
                            <li>Otomatis mengenali <strong>Pilihan Ganda (Opsi A–E)</strong>, <strong>Esai/Uraian</strong>, dan <strong>Benar/Salah</strong>.</li>
                            <li>Mendeteksi <strong>Kunci Jawaban</strong> (kunci di bawah soal, tulisan tebal/bold, atau tabel kunci di akhir dokumen).</li>
                            <li>Memisahkan <strong>Wacana/Stimulus Cerita</strong> dan pertanyaan inti secara presisi.</li>
                            <li>Hasil ekstraksi akan ditampilkan di <strong>Modal Review</strong> untuk Anda periksa dan edit sebelum disimpan ke database.</li>
                        </ul>
                    </div>

                    <!-- LOADING STATE -->
                    <div id="wordLoadingIndicator" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h6 class="font-weight-bold text-dark">AI Sedang Membaca &amp; Mengekstrak Naskah Soal Word...</h6>
                        <p class="small text-muted mb-0">Memproses paragraf, tabel opsi, kunci jawaban, dan klasifikasi level kognitif. Mohon tunggu beberapa detik.</p>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitWord" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" style="background: #2563eb; border-color: #2563eb;">
                        <i class="fas fa-magic mr-1"></i> Ekstrak &amp; Preview Soal AI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL REVIEW & EDIT INTERAKTIF HASIL EKSTRAKSI AI -->
<div class="modal fade" id="modalReviewWord" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 95%;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; max-height: 92vh; display: flex; flex-direction: column;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e1b4b, #4338ca); border-top-left-radius: 20px; border-top-right-radius: 20px; flex-shrink: 0;">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255,255,255,0.2); display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-right: 12px;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0">Review &amp; Edit Hasil Ekstraksi AI Soal Word</h5>
                        <small class="text-light opacity-75">Periksa, ubah teks pertanyaan/opsi/kunci, atau hapus butir sebelum disimpan ke Bank Soal</small>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <span class="badge badge-warning text-dark font-weight-bold px-3 py-1.5 rounded-pill" id="reviewBadgeCount">
                        0 Butir Soal Ditemukan
                    </span>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
            </div>

            <div class="modal-body p-4" style="overflow-y: auto; background: #f8fafc;">
                <div class="alert alert-info border-0 shadow-sm rounded-lg mb-3 py-2.5 px-3 d-flex align-items-center justify-content-between">
                    <div class="small">
                        <i class="fas fa-info-circle mr-1"></i> <strong>Tips Verifikasi:</strong> Silakan koreksi butir pertanyaan, opsi A-E, kunci jawaban, dan level kognitif jika terdapat penyesuaian. Klik tombol <strong>Hapus (🗑️)</strong> pada butir yang tidak ingin dimasukkan.
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold rounded-pill px-2.5" onclick="clearAllReviewItems()">
                        <i class="fas fa-trash-alt mr-1"></i> Bersihkan Semua
                    </button>
                </div>

                <!-- CONTAINER LIST SOAL DINAMIS -->
                <div id="reviewSoalContainer" class="d-flex flex-column" style="gap: 16px;">
                    <!-- Diisi secara dinamis oleh JavaScript -->
                </div>
            </div>

            <div class="modal-footer bg-white px-4 py-3 border-top d-flex justify-content-between" style="flex-shrink: 0; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-dismiss="modal">Batal / Tutup</button>
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <button type="button" id="btnSaveReviewedWord" class="btn btn-success font-weight-bold rounded-pill px-5 shadow-sm" onclick="submitWordImportSave()">
                        <i class="fas fa-save mr-1"></i> Simpan Semua Butir ke Bank Soal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function autoFillAiMateri(select) {
    const opt = select.options[select.selectedIndex];
    const mat = opt ? opt.getAttribute('data-materi') : '';
    if (mat) {
        $('#ai_topik').val(mat);
    }
}

function filterAiCpDropdown() {
    const fase = $('#ai_fase_tingkat').val() === 'X' ? 'E' : 'F';
    $('#ai_id_cp option').each(function() {
        const cpFase = $(this).data('fase');
        if (!cpFase || cpFase === fase) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function filterAiTpDropdown() {
    const cpId = $('#ai_id_cp').val();
    $('#ai_id_tp option').each(function() {
        const tpCp = $(this).data('cp');
        if (!cpId || !tpCp || tpCp == cpId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function onCpFilterChange() {
    const cpId = $('#filter_cp').val();
    $('#filter_tp option').each(function() {
        const tpCp = $(this).data('cp');
        if (!cpId || !tpCp || tpCp == cpId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    applyCascadingFilter();
}

function resetAllFilters() {
    $('#filter_cp').val('');
    $('#filter_tp').val('');
    $('#filter_materi').val('');
    $('#filter_tipe').val('');
    $('#filter_level').val('');
    applyCascadingFilter();
}

function applyCascadingFilter() {
    const cp = $('#filter_cp').val();
    const tp = $('#filter_tp').val();
    const materi = ($('#filter_materi').val() || '').toLowerCase();
    const tipe = $('#filter_tipe').val();
    const level = $('#filter_level').val();

    let visibleCount = 0;

    $('.card-item-filter').each(function() {
        const el = $(this);
        const elCp = el.data('cp') || 0;
        const elTp = el.data('tp') || 0;
        const elMateri = (el.data('materi') || '').toLowerCase();
        const elTipe = el.data('tipe') || '';
        const elLevel = el.data('level') || '';

        let match = true;

        if (cp && elCp != cp) match = false;
        if (tp && elTp != tp) match = false;
        if (materi && !elMateri.includes(materi)) match = false;
        if (tipe && elTipe !== tipe) match = false;
        if (level && !elLevel.includes(level)) match = false;

        if (match) {
            el.fadeIn(150);
            visibleCount++;
        } else {
            el.fadeOut(150);
        }
    });

    $('#filtered_count').text(visibleCount);
}

function submitAiGenerator(e) {
    e.preventDefault();
    const form = document.getElementById('formAiGen');
    const formData = new FormData(form);

    $('#aiLoadingIndicator').removeClass('d-none');
    $('#btnSubmitAi').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Memproses...');

    fetch('index.php?mod=cbt_bank_soal&act=generate_ai', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        $('#aiLoadingIndicator').addClass('d-none');
        $('#btnSubmitAi').prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> Mulai Generate Soal');
        
        if (data.status === 'success' || data.status === 'ok') {
            $('#modalAiGenerator').modal('hide');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Memproduksi Soal!',
                    text: data.message || 'Butir soal berstandar Kurikulum Merdeka berhasil disimpan ke Bank Soal.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                alert(data.message || 'Soal berhasil digenerate dan disimpan ke Bank Soal!');
                window.location.reload();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memproduksi Soal',
                    text: data.message || 'Terjadi kesalahan sistem saat memproses AI.'
                });
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem saat memproses AI.'));
            }
        }
    })
    .catch(err => {
        $('#aiLoadingIndicator').addClass('d-none');
        $('#btnSubmitAi').prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> Mulai Generate Soal');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Jaringan / Server',
                text: 'Terjadi kesalahan jaringan atau server timeout: ' + err
            });
        } else {
            alert('Terjadi kesalahan jaringan atau server timeout: ' + err);
        }
    });
}

function playTts(text) {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const cleanText = text.replace(/<[^>]*>?/gm, '').replace(/\$[^$]*\$/g, '');
        const utter = new SpeechSynthesisUtterance(cleanText);
        utter.lang = 'id-ID';
        utter.rate = 0.95;
        window.speechSynthesis.speak(utter);
    } else {
        alert('Browser Anda tidak mendukung Web Speech API Text-to-Speech.');
    }
}

// Global variable penampung hasil review Word AI
let currentExtractedQuestions = [];

function filterWordTpDropdown() {
    const cpId = $('#word_id_cp').val();
    $('#word_id_tp option').each(function() {
        const tpCp = $(this).data('cp');
        if (!cpId || !tpCp || tpCp == cpId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function submitWordImportPreview(e) {
    e.preventDefault();
    const form = document.getElementById('formImportWord');
    const formData = new FormData(form);

    $('#wordLoadingIndicator').removeClass('d-none');
    $('#btnSubmitWord').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Memproses...');

    fetch('index.php?mod=cbt_bank_soal&act=import_word_preview', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        $('#wordLoadingIndicator').addClass('d-none');
        $('#btnSubmitWord').prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> Ekstrak &amp; Preview Soal AI');

        if (data.status === 'success' && data.soal && data.soal.length > 0) {
            $('#modalImportWord').modal('hide');
            currentExtractedQuestions = data.soal;
            renderReviewQuestions();
            $('#modalReviewWord').modal('show');
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengekstrak Word',
                    text: data.message || 'Tidak ada butir soal yang berhasil diekstrak.'
                });
            } else {
                alert('Gagal: ' + (data.message || 'Tidak ada butir soal yang berhasil diekstrak.'));
            }
        }
    })
    .catch(err => {
        $('#wordLoadingIndicator').addClass('d-none');
        $('#btnSubmitWord').prop('disabled', false).html('<i class="fas fa-magic mr-1"></i> Ekstrak &amp; Preview Soal AI');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Server / Timeout',
                text: 'Terjadi kesalahan jaringan atau waktu habis: ' + err
            });
        } else {
            alert('Kesalahan: ' + err);
        }
    });
}

function renderReviewQuestions() {
    const container = document.getElementById('reviewSoalContainer');
    container.innerHTML = '';
    $('#reviewBadgeCount').text(currentExtractedQuestions.length + ' Butir Soal Terdeteksi');

    if (currentExtractedQuestions.length === 0) {
        container.innerHTML = '<div class="alert alert-warning text-center">Seluruh butir soal telah dihapus. Silakan unggah dokumen kembali jika perlu.</div>';
        return;
    }

    currentExtractedQuestions.forEach((s, idx) => {
        const itemCard = document.createElement('div');
        itemCard.className = 'card shadow-sm border rounded-lg p-3 bg-white';
        itemCard.id = 'reviewItem_' + idx;

        let opsiHtml = '';
        if (s.tipe_soal === 'pg' && s.opsi && s.opsi.length > 0) {
            opsiHtml = '<div class="mt-3"><label class="small font-weight-bold text-dark mb-1">Opsi Jawaban &amp; Kunci:</label><div class="row" style="row-gap: 8px;">';
            s.opsi.forEach((op, opIdx) => {
                const isChecked = (op.label === s.kunci_jawaban || op.is_benar == 1) ? 'checked' : '';
                opsiHtml += `
                    <div class="col-12 col-md-6">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold" style="min-width: 40px; justify-content: center;">
                                    <input type="radio" name="kunci_${idx}" value="${op.label}" ${isChecked} onchange="updateReviewKunci(${idx}, '${op.label}')" class="mr-1"> ${op.label}
                                </span>
                            </div>
                            <input type="text" class="form-control" value="${escapeHtml(op.teks)}" oninput="updateReviewOpsiTeks(${idx}, ${opIdx}, this.value)" placeholder="Isi opsi ${op.label}...">
                        </div>
                    </div>
                `;
            });
            opsiHtml += '</div></div>';
        } else if (s.tipe_soal === 'tf') {
            const isB = (s.kunci_jawaban === 'B' || s.kunci_jawaban === 'BENAR') ? 'checked' : '';
            const isS = (s.kunci_jawaban === 'S' || s.kunci_jawaban === 'SALAH') ? 'checked' : '';
            opsiHtml = `
                <div class="mt-3">
                    <label class="small font-weight-bold text-dark mb-1">Kunci Jawaban Benar / Salah:</label>
                    <div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="tf_b_${idx}" name="kunci_${idx}" value="B" ${isB} onchange="updateReviewKunci(${idx}, 'B')" class="custom-control-input">
                            <label class="custom-control-label font-weight-bold text-success" for="tf_b_${idx}">BENAR</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="tf_s_${idx}" name="kunci_${idx}" value="S" ${isS} onchange="updateReviewKunci(${idx}, 'S')" class="custom-control-input">
                            <label class="custom-control-label font-weight-bold text-danger" for="tf_s_${idx}">SALAH</label>
                        </div>
                    </div>
                </div>
            `;
        }

        itemCard.innerHTML = `
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <span class="badge badge-dark px-2.5 py-1 font-weight-bold">#${idx + 1}</span>
                    <select class="form-control form-control-sm custom-filter-input font-weight-bold" style="width: auto; height: 30px;" onchange="updateReviewField(${idx}, 'tipe_soal', this.value); renderReviewQuestions();">
                        <option value="pg" ${s.tipe_soal === 'pg' ? 'selected' : ''}>Pilihan Ganda (PG)</option>
                        <option value="essay" ${s.tipe_soal === 'essay' ? 'selected' : ''}>Esai / Uraian</option>
                        <option value="tf" ${s.tipe_soal === 'tf' ? 'selected' : ''}>Benar / Salah (B/S)</option>
                    </select>
                    <select class="form-control form-control-sm custom-filter-input font-weight-bold" style="width: auto; height: 30px;" onchange="updateReviewField(${idx}, 'level_kognitif', this.value)">
                        <option value="L1" ${s.level_kognitif === 'L1' ? 'selected' : ''}>L1 - LOTS</option>
                        <option value="L2" ${s.level_kognitif === 'L2' ? 'selected' : ''}>L2 - MOTS</option>
                        <option value="L3" ${s.level_kognitif === 'L3' ? 'selected' : ''}>L3 - HOTS</option>
                    </select>
                    <select class="form-control form-control-sm custom-filter-input font-weight-bold" style="width: auto; height: 30px;" onchange="updateReviewField(${idx}, 'tingkat_kesulitan', this.value)">
                        <option value="mudah" ${s.tingkat_kesulitan === 'mudah' ? 'selected' : ''}>Mudah</option>
                        <option value="sedang" ${s.tingkat_kesulitan === 'sedang' ? 'selected' : ''}>Sedang</option>
                        <option value="sulit" ${s.tingkat_kesulitan === 'sulit' ? 'selected' : ''}>Sulit</option>
                    </select>
                </div>
                <div>
                    <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold rounded-pill px-2.5" onclick="removeReviewItem(${idx})">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Butir
                    </button>
                </div>
            </div>

            <!-- STIMULUS (OPSIONAL) -->
            <div class="mb-2">
                <label class="small font-weight-bold text-muted mb-0"><i class="fas fa-paragraph mr-1"></i> Stimulus / Wacana Cerita (Opsional):</label>
                <textarea class="form-control form-control-sm" rows="2" placeholder="Wacana pengantar stimulus..." oninput="updateReviewField(${idx}, 'stimulus', this.value)">${escapeHtml(s.stimulus || '')}</textarea>
            </div>

            <!-- PERTANYAAN INTI -->
            <div class="mb-2">
                <label class="small font-weight-bold text-dark mb-0"><i class="fas fa-question-circle mr-1 text-primary"></i> Teks Pertanyaan Soal:</label>
                <textarea class="form-control form-control-sm" rows="3" placeholder="Tuliskan pertanyaan soal..." oninput="updateReviewField(${idx}, 'pertanyaan', this.value)" required>${escapeHtml(s.pertanyaan || '')}</textarea>
            </div>

            ${opsiHtml}

            <!-- PEMBAHASAN / KUNCI ESAI -->
            <div class="mt-2">
                <label class="small font-weight-bold text-muted mb-0"><i class="fas fa-lightbulb text-warning mr-1"></i> Kunci / Pembahasan / Rubrik Penilaian:</label>
                <input type="text" class="form-control form-control-sm" value="${escapeHtml(s.pembahasan || s.kunci_jawaban || '')}" oninput="updateReviewField(${idx}, 'pembahasan', this.value); if ('${s.tipe_soal}' === 'essay') updateReviewField(${idx}, 'kunci_jawaban', this.value);" placeholder="Penjelasan kunci jawaban atau pedoman penskoran...">
            </div>
        `;

        container.appendChild(itemCard);
    });
}

function updateReviewField(idx, field, val) {
    if (currentExtractedQuestions[idx]) {
        currentExtractedQuestions[idx][field] = val;
    }
}

function updateReviewKunci(idx, kunci) {
    if (currentExtractedQuestions[idx]) {
        currentExtractedQuestions[idx].kunci_jawaban = kunci;
        if (currentExtractedQuestions[idx].opsi) {
            currentExtractedQuestions[idx].opsi.forEach(op => {
                op.is_benar = (op.label === kunci) ? 1 : 0;
            });
        }
    }
}

function updateReviewOpsiTeks(idx, opIdx, val) {
    if (currentExtractedQuestions[idx] && currentExtractedQuestions[idx].opsi && currentExtractedQuestions[idx].opsi[opIdx]) {
        currentExtractedQuestions[idx].opsi[opIdx].teks = val;
    }
}

function removeReviewItem(idx) {
    currentExtractedQuestions.splice(idx, 1);
    renderReviewQuestions();
}

function clearAllReviewItems() {
    if (confirm('Apakah Anda yakin ingin mengosongkan seluruh butir soal hasil ekstraksi ini?')) {
        currentExtractedQuestions = [];
        renderReviewQuestions();
    }
}

function submitWordImportSave() {
    if (!currentExtractedQuestions || currentExtractedQuestions.length === 0) {
        alert('Tidak ada butir soal untuk disimpan.');
        return;
    }

    const btn = document.getElementById('btnSaveReviewedWord');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Menyimpan ke Database...';

    fetch('index.php?mod=cbt_bank_soal&act=import_word_save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            id_bank: <?= (int)$bank['id_bank'] ?>,
            soal: currentExtractedQuestions
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Semua Butir ke Bank Soal';

        if (data.status === 'success') {
            $('#modalReviewWord').modal('hide');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Menyimpan!',
                    text: data.message || 'Butir soal hasil import Word berhasil disimpan ke Bank Soal.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                alert(data.message || 'Berhasil disimpan!');
                window.location.reload();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: data.message || 'Terjadi kesalahan saat menyimpan soal.'
                });
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
            }
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Semua Butir ke Bank Soal';
        alert('Kesalahan jaringan: ' + err);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.addEventListener("DOMContentLoaded", function() {
    filterAiCpDropdown();
    filterAiTpDropdown();
    filterWordTpDropdown();
    applyCascadingFilter();

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

<?php include __DIR__ . '/partials/footer.php'; ?>
