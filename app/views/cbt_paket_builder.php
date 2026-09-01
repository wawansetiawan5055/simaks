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
include __DIR__ . '/partials/header.php'; ?>

<!-- Math Engine: KaTeX & MathJax 3 for Crisp Mathematical Formulas -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.css">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/contrib/auto-render.min.js"></script>

<style>
    .builder-icon-box {
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
    .panel-gudang, .panel-naskah {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.02);
        padding: 20px;
        min-height: 650px;
    }
    .item-soal-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: all 0.15s ease;
        position: relative;
    }
    .item-soal-card:hover {
        border-color: #6366f1;
        background: #ffffff;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.08);
    }
    .item-soal-card.in-packet {
        background: #f0fdf4;
        border-color: #bbf7d0;
        opacity: 0.85;
    }
    .drag-handle {
        cursor: move;
        color: #94a3b8;
    }
    .drag-handle:hover {
        color: #4f46e5;
    }
    .stat-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .btn-gradient-indigo {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }
    .btn-gradient-indigo:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }
    .custom-filter-select {
        font-family: 'Poppins', sans-serif !important;
        font-size: 0.82rem;
        font-weight: 500;
        color: #1e293b;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 50px;
        height: 34px;
        padding: 0 12px;
        transition: all 0.2s ease;
    }
    .custom-filter-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    .search-filter-input {
        font-family: 'Poppins', sans-serif !important;
        font-size: 0.84rem;
        font-weight: 500;
        color: #1e293b;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 50px;
        height: 36px;
        padding-left: 36px;
        padding-right: 14px;
        transition: all 0.2s ease;
    }
    .search-filter-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        outline: none;
    }
    .btn-soft-info {
        background: #f0fdfa;
        color: #0d9488;
        border: 1px solid #99f6e4;
        font-weight: 700;
    }
    .btn-soft-info:hover {
        background: #0d9488;
        color: #ffffff;
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <!-- TOP HEADER: TITLE LEFT + ACTIONS RIGHT -->
        <div class="row align-items-center">
            <div class="col-lg-7 col-12 d-flex align-items-center">
                <div class="builder-icon-box mr-3">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Studio Perakitan Naskah
                    </h4>
                    <div class="d-flex align-items-center flex-wrap mt-1" style="gap: 6px;">
                        <span class="badge text-white font-weight-bold px-2 py-0.5 rounded" style="background: #4f46e5; font-size: 0.74rem;">
                            <?= htmlspecialchars($paket['nama_mapel'] ?? '-') ?>
                        </span>
                        <span class="badge badge-light border text-dark font-weight-bold px-2 py-0.5 rounded" style="font-size: 0.74rem;">
                            Kelas <?= htmlspecialchars($paket['tingkat'] ?? '-') ?>
                        </span>
                        <span class="badge badge-light border text-muted px-2 py-0.5 rounded" style="font-size: 0.74rem;">
                            <?= htmlspecialchars($paket['jenis_asesmen'] ?? 'Asesmen') ?>
                        </span>
                        <span class="badge badge-light border text-muted px-2 py-0.5 rounded" style="font-size: 0.74rem;">
                            <i class="fas fa-stopwatch mr-1"></i> <?= htmlspecialchars($paket['alokasi_waktu'] ?? '90 Menit') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-12 text-lg-right mt-3 mt-lg-0 d-flex justify-content-lg-end align-items-center flex-wrap" style="gap: 8px;">
                <a href="index.php?mod=cbt_paket&act=preview_siswa&id_paket=<?= $paket['id_paket'] ?>" target="_blank" class="btn btn-sm btn-soft-info rounded-pill px-3 font-weight-bold shadow-sm" title="Simulasi Tampilan Siswa (CBT Player)">
                    <i class="fas fa-desktop mr-1"></i> Simulasi Siswa
                </a>
                
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-light border font-weight-bold rounded-pill px-3 dropdown-toggle shadow-sm text-secondary" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-print mr-1 text-primary"></i> Cetak Dokumen
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 12px; font-family: 'Poppins', sans-serif;">
                        <a class="dropdown-item py-2 small" href="index.php?mod=cbt_paket&act=print_naskah&id_paket=<?= $paket['id_paket'] ?>" target="_blank">
                            <i class="fas fa-file-alt text-primary mr-2"></i> 📄 Naskah Soal Ujian
                        </a>
                        <a class="dropdown-item py-2 small" href="index.php?mod=cbt_paket&act=print_kisi_kisi&id_paket=<?= $paket['id_paket'] ?>" target="_blank">
                            <i class="fas fa-th-list text-info mr-2"></i> 📊 Format Kisi-Kisi Standar
                        </a>
                        <a class="dropdown-item py-2 small" href="index.php?mod=cbt_paket&act=print_kartu_soal&id_paket=<?= $paket['id_paket'] ?>" target="_blank">
                            <i class="fas fa-id-card text-warning mr-2"></i> 🗂️ Kartu Soal Akreditasi
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item py-2 small" href="index.php?mod=cbt_paket&act=print_kunci&id_paket=<?= $paket['id_paket'] ?>" target="_blank">
                            <i class="fas fa-key text-success mr-2"></i> 🔑 Kunci &amp; Rubrik Penskoran
                        </a>
                    </div>
                </div>

                <a href="index.php?mod=cbt_paket" class="btn btn-sm btn-outline-secondary rounded-pill px-3 font-weight-bold shadow-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content mt-2">
    <div class="container-fluid">
        <?php include __DIR__ . '/partials/flash_message.php'; ?>

        <!-- STATISTIK KOMPOSISI SOAL (SMART BALANCE BAR) -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #1e1b4b, #312e81); color: #fff;">
            <div class="card-body p-3 px-md-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                    <div>
                        <span class="text-white-50 small text-uppercase font-weight-bold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Status Perakitan Naskah</span>
                        <h4 class="font-weight-bold text-white mb-0" id="stat_total_soal" style="font-family: 'Poppins', sans-serif;">0 Butir Soal Terpilih</h4>
                    </div>
                    <div class="d-flex flex-wrap" style="gap: 6px;">
                        <span class="stat-badge">
                            <i class="fas fa-check-circle text-info"></i> PG: <span id="stat_pg">0</span>
                        </span>
                        <span class="stat-badge">
                            <i class="fas fa-pen text-warning"></i> Esai: <span id="stat_essay">0</span>
                        </span>
                        <span class="stat-badge">
                            <i class="fas fa-brain text-danger"></i> HOTS (L3): <span id="stat_l3">0</span>
                        </span>
                        <span class="stat-badge">
                            <i class="fas fa-cogs text-primary"></i> L2: <span id="stat_l2">0</span>
                        </span>
                        <span class="stat-badge">
                            <i class="fas fa-book text-success"></i> L1: <span id="stat_l1">0</span>
                        </span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success btn-sm rounded-pill px-4 font-weight-bold shadow py-2" onclick="savePacketComposition()" id="btnSavePacket">
                            <i class="fas fa-check-circle mr-1"></i> Simpan Naskah Paket
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- PANEL KIRI: GUDANG BANK SOAL -->
            <div class="col-lg-6 col-12 mb-4">
                <div class="panel-gudang h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-database text-primary mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Gudang Koleksi Soal (<span id="count_gudang"><?= count($gudang_soal) ?></span>)
                            </h6>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3 font-weight-bold" onclick="addAllFiltered()">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Semua yang Tampil
                        </button>
                    </div>

                    <!-- MULTI-LEVEL FILTER GUDANG SOAL -->
                    <div class="p-3 bg-light rounded-lg border mb-3">
                        <div class="row" style="row-gap: 8px;">
                            <!-- Filter Tingkat/Kelas -->
                            <div class="col-md-4 col-6">
                                <label class="small text-muted font-weight-bold mb-1">Tingkat / Fase</label>
                                <select id="filterFase" class="form-control custom-filter-select" onchange="onBuilderFaseChange()">
                                    <option value="">Semua Tingkat</option>
                                    <option value="E">Kelas X (Fase E)</option>
                                    <option value="F">Kelas XI - XII (Fase F)</option>
                                </select>
                            </div>

                            <!-- Filter CP -->
                            <div class="col-md-8 col-6">
                                <label class="small text-muted font-weight-bold mb-1">Capaian Pembelajaran (CP)</label>
                                <select id="filterCp" class="form-control custom-filter-select" onchange="onBuilderCpChange()">
                                    <option value="">Semua CP</option>
                                    <?php foreach ($cp_list as $cp): ?>
                                        <option value="<?= $cp['id_cp'] ?>" data-fase="<?= htmlspecialchars($cp['fase'] ?? '') ?>">
                                            <?= htmlspecialchars((!empty($cp['fase']) ? '[Fase ' . $cp['fase'] . '] ' : '') . mb_strimwidth($cp['deskripsi_cp'], 0, 45, '...')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter TP -->
                            <div class="col-md-6 col-12">
                                <label class="small text-muted font-weight-bold mb-1">Tujuan Pembelajaran (TP)</label>
                                <select id="filterTp" class="form-control custom-filter-select" onchange="filterGudang()">
                                    <option value="">Semua TP</option>
                                    <?php foreach ($tp_list as $tp): ?>
                                        <option value="<?= $tp['id_tp'] ?>" data-cp="<?= $tp['id_cp'] ?>">
                                            <?= htmlspecialchars(($tp['kode_tp'] ? $tp['kode_tp'] . ': ' : '') . mb_strimwidth($tp['deskripsi_tp'], 0, 38, '...')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Tipe & Level -->
                            <div class="col-md-3 col-6">
                                <label class="small text-muted font-weight-bold mb-1">Bentuk Soal</label>
                                <select id="filterTipe" class="form-control custom-filter-select" onchange="filterGudang()">
                                    <option value="">Semua Bentuk</option>
                                    <option value="pg">Pilihan Ganda</option>
                                    <option value="essay">Esai</option>
                                    <option value="tf">Benar/Salah</option>
                                    <option value="matching">Menjodohkan</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-6">
                                <label class="small text-muted font-weight-bold mb-1">Level Kognitif</label>
                                <select id="filterLevel" class="form-control custom-filter-select" onchange="filterGudang()">
                                    <option value="">Semua Level</option>
                                    <option value="L1">L1 - Pengetahuan</option>
                                    <option value="L2">L2 - Aplikasi</option>
                                    <option value="L3">L3 - HOTS</option>
                                </select>
                            </div>

                            <!-- Cari Teks -->
                            <div class="col-12 position-relative">
                                <i class="fas fa-search position-absolute" style="left: 26px; top: 11px; color: #94a3b8; font-size: 0.84rem;"></i>
                                <input type="text" id="filterKeyword" class="form-control search-filter-input" placeholder="Cari teks soal, materi, atau indikator kisi-kisi..." oninput="filterGudang()">
                            </div>
                        </div>
                    </div>

                    <!-- DAFTAR GUDANG SOAL -->
                    <div id="containerGudang" style="max-height: 520px; overflow-y: auto; padding-right: 4px;">
                        <?php foreach ($gudang_soal as $s): ?>
                            <?php 
                                $lvl = strtoupper($s['level_kognitif'] ?? 'L2');
                                $is_hots = ($lvl === 'L3' || strpos($lvl, 'HOTS') !== false);
                                $lvl_clean = $is_hots ? 'L3' : (($lvl === 'L1') ? 'L1' : 'L2');
                                $fase_val = $s['fase_cp'] ?? (($s['bank_tingkat'] === 'X' || $s['bank_tingkat'] === '10') ? 'E' : 'F');
                            ?>
                            <div class="item-soal-card card-gudang" id="gudang_item_<?= $s['id_soal'] ?>" 
                                 data-id="<?= $s['id_soal'] ?>" 
                                 data-fase="<?= htmlspecialchars($fase_val) ?>"
                                 data-cp="<?= (int)($s['id_cp'] ?? 0) ?>"
                                 data-tp="<?= (int)($s['id_tp'] ?? 0) ?>"
                                 data-tipe="<?= htmlspecialchars($s['tipe_soal']) ?>" 
                                 data-level="<?= $lvl_clean ?>" 
                                 data-kesulitan="<?= htmlspecialchars($s['tingkat_kesulitan'] ?? 'sedang') ?>"
                                 data-materi="<?= htmlspecialchars($s['lingkup_materi'] ?? '') ?>"
                                 data-indikator="<?= htmlspecialchars($s['indikator_soal'] ?? '') ?>"
                                 data-bobot="<?= (float)($s['bobot'] ?? 1) ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                        <span class="badge badge-<?= $s['tipe_soal']==='pg'?'primary':($s['tipe_soal']==='essay'?'warning':'info') ?> text-uppercase font-weight-bold px-2 py-0.5" style="font-size: 0.72rem;">
                                            <?= $s['tipe_soal'] ?>
                                        </span>
                                        <span class="badge badge-<?= $is_hots ? 'danger' : ($lvl_clean==='L1'?'secondary':'info') ?> font-weight-bold px-2 py-0.5" style="font-size: 0.72rem;">
                                            <?= $is_hots ? 'L3 (HOTS)' : $lvl_clean ?>
                                        </span>
                                        <?php if (!empty($fase_val)): ?>
                                            <span class="badge badge-light border text-indigo font-weight-bold px-2 py-0.5" style="color: #4f46e5; font-size: 0.72rem;">
                                                Fase <?= htmlspecialchars($fase_val) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($s['lingkup_materi'])): ?>
                                            <span class="badge badge-light border text-truncate px-2 py-0.5 text-muted" style="max-width: 140px; font-size: 0.72rem;">
                                                <?= htmlspecialchars($s['lingkup_materi']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-success rounded-pill px-3 font-weight-bold btn-add-soal shadow-sm" onclick="addToPacket(<?= $s['id_soal'] ?>)">
                                        <i class="fas fa-plus mr-1"></i> Pilih
                                    </button>
                                </div>

                                <?php if (!empty($s['indikator_soal'])): ?>
                                    <div class="small text-primary mb-1 font-weight-bold" style="font-size: 0.82rem;">
                                        🎯 <?= format_cbt_math_output(mb_strimwidth($s['indikator_soal'], 0, 90, '...')) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="small text-dark mb-0 text-soal" style="line-height: 1.55; font-size: 0.86rem;">
                                    <?= format_cbt_math_output(mb_strimwidth(strip_tags($s['pertanyaan']), 0, 140, '...')) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: NASKAH PAKET SOAL TERPILIH -->
            <div class="col-lg-6 col-12 mb-4">
                <div class="panel-naskah h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-invoice text-success mr-2" style="font-size: 1.1rem;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Poppins', sans-serif;">
                                Naskah Paket Soal Terpilih (<span id="count_packet">0</span>)
                            </h6>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-3 font-weight-bold" onclick="clearPacket()">
                            <i class="fas fa-trash-alt mr-1"></i> Kosongkan
                        </button>
                    </div>

                    <div id="containerPacket" style="max-height: 600px; overflow-y: auto; padding-right: 4px;">
                        <!-- Items rendered via Javascript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Data gudang soal awal dari backend
const allGudangSoal = <?= json_encode($gudang_soal) ?>;
// Data soal yang sudah dipilih di paket
let packetList = <?= json_encode($selected_soal) ?>;

function onBuilderFaseChange() {
    const fase = $('#filterFase').val();
    $('#filterCp option').each(function() {
        const cpFase = $(this).data('fase');
        if (!fase || !cpFase || cpFase === fase) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    filterGudang();
}

function onBuilderCpChange() {
    const cpId = $('#filterCp').val();
    $('#filterTp option').each(function() {
        const tpCp = $(this).data('cp');
        if (!cpId || !tpCp || tpCp == cpId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    filterGudang();
}

function filterGudang() {
    const kw = $('#filterKeyword').val().toLowerCase().trim();
    const fase = $('#filterFase').val();
    const cp = $('#filterCp').val();
    const tp = $('#filterTp').val();
    const lvl = $('#filterLevel').val();
    const tipe = $('#filterTipe').val();

    $('.card-gudang').each(function() {
        const itemFase = $(this).data('fase') || '';
        const itemCp = $(this).data('cp') || 0;
        const itemTp = $(this).data('tp') || 0;
        const itemTipe = $(this).data('tipe') || '';
        const itemLvl = $(this).data('level') || '';
        const text = $(this).text().toLowerCase();

        let show = true;
        if (fase && itemFase !== fase) show = false;
        if (cp && itemCp != cp) show = false;
        if (tp && itemTp != tp) show = false;
        if (lvl && itemLvl !== lvl) show = false;
        if (tipe && itemTipe !== tipe) show = false;
        if (kw && !text.includes(kw)) show = false;

        $(this).toggle(show);
    });

    $('#count_gudang').text($('.card-gudang:visible').length);
}

function renderPacketList() {
    const container = $('#containerPacket');
    container.empty();

    if (packetList.length === 0) {
        container.html(`
            <div class="text-center p-5 text-muted">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.8rem;">
                        <i class="fas fa-inbox"></i>
                    </div>
                </div>
                <h6 class="font-weight-bold text-dark mb-1">Naskah Paket Masih Kosong</h6>
                <p class="small text-muted mb-0">Silakan pilih butir soal dari Gudang Koleksi di sebelah kiri.</p>
            </div>
        `);
    } else {
        packetList.forEach((s, idx) => {
            const lvl = ('' + (s.level_kognitif || 'L2')).toUpperCase();
            const is_hots = (lvl === 'L3' || lvl.includes('HOTS'));
            const lvl_display = is_hots ? 'L3 (HOTS)' : (lvl === 'L1' ? 'L1' : 'L2');

            const itemHtml = `
                <div class="item-soal-card" id="packet_item_${s.id_soal}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                            <span class="drag-handle mr-1"><i class="fas fa-grip-vertical"></i></span>
                            <span class="badge badge-dark font-weight-bold px-2 py-0.5" style="font-size: 0.72rem; border-radius: 4px;">No. ${idx + 1}</span>
                            <span class="badge badge-${s.tipe_soal==='pg'?'primary':(s.tipe_soal==='essay'?'warning':'info')} text-uppercase font-weight-bold px-2 py-0.5" style="font-size: 0.72rem;">
                                ${s.tipe_soal}
                            </span>
                            <span class="badge badge-${is_hots ? 'danger' : 'info'} font-weight-bold px-2 py-0.5" style="font-size: 0.72rem;">
                                ${lvl_display}
                            </span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-3 font-weight-bold" onclick="removeFromPacket(${s.id_soal})">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                    </div>

                    ${s.indikator_soal ? `<div class="small text-primary mb-1 font-weight-bold" style="font-size: 0.82rem;">🎯 ${s.indikator_soal.substring(0, 90)}...</div>` : ''}

                    <div class="small text-dark mb-0" style="line-height: 1.55; font-size: 0.86rem;">
                        ${s.pertanyaan.replace(/<[^>]*>?/gm, '').substring(0, 140)}...
                    </div>
                </div>
            `;
            container.append(itemHtml);
        });
    }

    // Tandai status di gudang soal
    $('.card-gudang').each(function() {
        const id = $(this).data('id');
        const exists = packetList.some(p => p.id_soal == id);
        if (exists) {
            $(this).addClass('in-packet');
            $(this).find('.btn-add-soal').removeClass('btn-success').addClass('btn-secondary').prop('disabled', true).html('<i class="fas fa-check mr-1"></i> Terpilih');
        } else {
            $(this).removeClass('in-packet');
            $(this).find('.btn-add-soal').removeClass('btn-secondary').addClass('btn-success').prop('disabled', false).html('<i class="fas fa-plus mr-1"></i> Pilih');
        }
    });

    updateSmartStats();
    renderMathInDOM();
}

function addToPacket(idSoal) {
    const item = allGudangSoal.find(s => s.id_soal == idSoal);
    if (item && !packetList.some(p => p.id_soal == idSoal)) {
        packetList.push(item);
        renderPacketList();
    }
}

function addAllFiltered() {
    $('.card-gudang:visible').each(function() {
        const id = $(this).data('id');
        const item = allGudangSoal.find(s => s.id_soal == id);
        if (item && !packetList.some(p => p.id_soal == id)) {
            packetList.push(item);
        }
    });
    renderPacketList();
}

function removeFromPacket(idSoal) {
    packetList = packetList.filter(s => s.id_soal != idSoal);
    renderPacketList();
}

function clearPacket() {
    if (confirm('Kosongkan seluruh butir soal dalam naskah ini?')) {
        packetList = [];
        renderPacketList();
    }
}

function updateSmartStats() {
    const total = packetList.length;
    $('#stat_total_soal').text(total + ' Butir Soal Terpilih');
    $('#count_packet').text(total);

    let pg = 0, essay = 0, l1 = 0, l2 = 0, l3 = 0;
    packetList.forEach(item => {
        if (item.tipe_soal === 'pg') pg++;
        else if (item.tipe_soal === 'essay') essay++;

        const lvl = ('' + (item.level_kognitif || '')).toUpperCase();
        if (lvl === 'L3' || lvl.includes('HOTS')) l3++;
        else if (lvl === 'L1') l1++;
        else l2++;
    });

    $('#stat_pg').text(pg);
    $('#stat_essay').text(essay);
    $('#stat_l1').text(l1);
    $('#stat_l2').text(l2);
    $('#stat_l3').text(l3);
}

function savePacketComposition() {
    const btn = $('#btnSavePacket');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan Naskah...');

    const payload = {
        id_paket: <?= (int)$paket['id_paket'] ?>,
        items: packetList.map((item, idx) => ({
            id_soal: item.id_soal,
            nomor_urut: idx + 1,
            bobot_soal: item.bobot
        }))
    };

    $.ajax({
        url: 'index.php?mod=cbt_paket&act=save_builder',
        type: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        success: function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Simpan Naskah Paket');
            if (res.status === 'ok') {
                alert(res.message || 'Naskah paket berhasil disimpan!');
            } else {
                alert('Gagal: ' + (res.message || 'Terjadi kesalahan saat menyimpan naskah.'));
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Simpan Naskah Paket');
            alert('Terjadi kesalahan koneksi saat menyimpan naskah.');
        }
    });
}

function renderMathInDOM() {
    if (typeof renderMathInElement === "function") {
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
}

document.addEventListener("DOMContentLoaded", function() {
    renderPacketList();
    filterGudang();
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
