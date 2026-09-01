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

        // 2. Wrap complete LaTeX commands with braces: \frac{...}{...}, \sqrt[3]{...}, \sqrt{...}
        $text = preg_replace_callback('/\\\\(?:frac|sqrt|left|right|sum|int|lim|prod|binom|over|underline|overline|mathbf|text)\s*(?:\[[^\]]*\])?(?:\s*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\})+/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        // 3. Wrap caret parentheses exponents: 2^(t + 1), x^(5/6), 5^(x^2 - 3x - 4)
        $text = preg_replace_callback('/([a-zA-Z0-9\)\.]+|\w+)\^\(([^)]+)\)/', function($m) use ($wrap_token) {
            return $wrap_token($m[1] . '^{' . $m[2] . '}');
        }, $text);

        // 4. Wrap simple single-token exponents like x^2, 3^2, y^3 if not in tag
        $text = preg_replace_callback('/(?<![a-zA-Z0-9\$\_\@])([a-zA-Z0-9]+)\^([0-9a-zA-Z]+)(?![a-zA-Z0-9\$\_\@])/', function($m) use ($wrap_token) {
            return $wrap_token($m[1] . '^{' . $m[2] . '}');
        }, $text);

        // 5. Wrap single LaTeX words: \alpha, \beta, \cdot, \times, \pm, \le, \ge, \ne, \pi, \infty
        $text = preg_replace_callback('/\\\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|cdot|times|div|pm|mp|le|ge|ne|neq|approx|infty|forall|exists|in|notin|subset|subseteq|cup|cap|to|leftarrow|rightarrow|Rightarrow|leftrightarrow)\b/', function($m) use ($wrap_token) {
            return $wrap_token($m[0]);
        }, $text);

        // Kembalikan semua tokens
        if (!empty($tokens)) {
            $text = strtr($text, $tokens);
        }

        // Gabungkan $ yang bersebelahan atau dipisah spasi/operator dasar
        $text = preg_replace('/\$\s*\$/', ' ', $text);
        $text = preg_replace('/\$\s*([\=\+\-\*\/])\s*\$/', ' $1 ', $text);

        return $text;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Soal Asesmen - <?= htmlspecialchars($paket['nama_paket']) ?></title>
    <!-- Math Engine: KaTeX & MathJax 3 for Crisp Mathematical Formulas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
    
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
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10.5pt;
            line-height: 1.3;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }
        .page-container {
            width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 15mm 20mm;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .no-print-bar {
            width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .kartu-card {
            border: 2px solid #000;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .kartu-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 8px;
            background-color: #f8fafc;
        }
        .kartu-header h4 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .kartu-header div { font-size: 10pt; font-weight: bold; }

        .kartu-meta-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #000;
            font-size: 9.5pt;
        }
        .kartu-meta-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .kartu-content {
            padding: 10px 12px;
            font-size: 10pt;
        }
        .stimulus-box {
            border-left: 2px solid #000;
            padding: 4px 8px;
            background: #f9f9f9;
            margin-bottom: 8px;
            font-style: italic;
        }
        .opsi-row {
            margin-bottom: 3px;
        }

        .kartu-footer {
            border-top: 1px solid #000;
            background: #f8fafc;
            padding: 8px 12px;
            font-size: 9.5pt;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .page-container {
                width: 100%;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <a href="<?= BASE_URL ?>cbt_paket/builder?id_paket=<?= $paket['id_paket'] ?>" style="text-decoration: none; font-weight: bold; color: #4f46e5;">
                <i class="fas fa-arrow-left"></i> Kembali ke Studio
            </a>
        </div>
        <div>
            <button onclick="printDocument()" style="background: #10b981; color: #fff; border: none; padding: 8px 20px; border-radius: 20px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                <i class="fas fa-print mr-1"></i> Cetak Kartu Soal
            </button>
        </div>
    </div>

    <div class="page-container">
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin: 0; text-transform: uppercase; font-size: 14pt;"><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'SIMAKS ACADEMY') ?></h3>
            <h4 style="margin: 3px 0 0 0; text-transform: uppercase; font-size: 12pt;">KARTU SOAL ASESMEN (STANDAR AKREDITASI)</h4>
            <div style="font-size: 10pt; font-style: italic;"><?= htmlspecialchars($paket['nama_paket']) ?> - Tahun Pelajaran <?= htmlspecialchars($paket['tahun_ajaran'] ?? date('Y').'/'.(date('Y')+1)) ?></div>
        </div>

        <?php if (empty($soal_list)): ?>
            <div style="text-align: center; padding: 40px; border: 1px dashed #ccc;">Belum ada butir soal pada naskah ini.</div>
        <?php else: ?>
            <?php foreach ($soal_list as $i => $s): ?>
                <?php 
                    $lvl = strtoupper($s['level_kognitif'] ?? 'L2');
                    $is_hots = ($lvl === 'L3' || strpos($lvl, 'HOTS') !== false);
                    $lvl_display = $is_hots ? 'Level 3 (Penalaran / HOTS)' : (($lvl === 'L1') ? 'Level 1 (Pengetahuan/Pemahaman)' : 'Level 2 (Aplikasi)');
                    $bentuk = ($s['tipe_soal'] === 'pg') ? 'Pilihan Ganda' : (($s['tipe_soal'] === 'essay') ? 'Uraian / Esai' : 'Benar / Salah');
                ?>
                <div class="kartu-card">
                    <div class="kartu-header">
                        <h4>KARTU SOAL NOMOR <?= $i + 1 ?></h4>
                        <div>Bentuk Soal: <?= $bentuk ?> &bull; Bobot: <?= (float)($s['bobot_soal'] ?? $s['bobot'] ?? 1) ?></div>
                    </div>

                    <table class="kartu-meta-table">
                        <tr>
                            <td width="20%"><strong>Mata Pelajaran</strong></td>
                            <td width="30%"><?= htmlspecialchars($paket['nama_mapel'] ?? '-') ?></td>
                            <td width="20%"><strong>Penyusun Soal</strong></td>
                            <td width="30%"><?= htmlspecialchars($paket['penyusun'] ?? $paket['nama_guru'] ?? 'Guru Pengampu') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kelas / Semester</strong></td>
                            <td>Kelas <?= htmlspecialchars($paket['tingkat'] ?? 'X') ?> / <?= htmlspecialchars($paket['semester'] ?? 'Ganjil') ?></td>
                            <td><strong>Tahun Pelajaran</strong></td>
                            <td><?= htmlspecialchars($paket['tahun_ajaran'] ?? date('Y').'/'.(date('Y')+1)) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Capaian Pembelajaran (CP)</strong></td>
                            <td colspan="3"><?= htmlspecialchars($s['deskripsi_cp'] ?? $s['kode_cp'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tujuan Pembelajaran (TP)</strong></td>
                            <td colspan="3"><?= htmlspecialchars($s['deskripsi_tp'] ?? $s['kode_tp'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lingkup Materi</strong></td>
                            <td><?= format_cbt_math_output($s['lingkup_materi'] ?: '-') ?></td>
                            <td><strong>Level Kognitif</strong></td>
                            <td><strong><?= $lvl_display ?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Indikator Soal</strong></td>
                            <td colspan="3"><?= format_cbt_math_output($s['indikator_soal'] ?: '-') ?></td>
                        </tr>
                    </table>

                    <div class="kartu-content">
                        <strong>RUMUSAN BUTIR SOAL:</strong>
                        <?php if (!empty($s['stimulus'])): ?>
                            <div class="stimulus-box"><?= nl2br(format_cbt_math_output($s['stimulus'])) ?></div>
                        <?php endif; ?>

                        <div style="margin: 6px 0;">
                            <?= format_cbt_math_output($s['pertanyaan']) ?>
                        </div>

                        <?php if ($s['tipe_soal'] === 'pg' && !empty($s['opsi_list'])): ?>
                            <div style="margin-left: 15px; margin-top: 6px;">
                                <?php foreach ($s['opsi_list'] as $o): ?>
                                    <div class="opsi-row" style="margin-bottom: 3px;">
                                        <strong><?= $o['label'] ?>.</strong> <?= format_cbt_math_output($o['isi_opsi']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="kartu-footer">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <strong>Kunci Jawaban: </strong>
                                <span style="background: #e2e8f0; padding: 2px 8px; font-weight: bold; border-radius: 4px;">
                                    <?= format_cbt_math_output($s['kunci_pg'] ?? $s['kunci_jawaban'] ?? '-') ?>
                                </span>
                            </div>
                            <div>
                                <strong>Buku Sumber: </strong> Buku Siswa / Modul Kemdikbudristek
                            </div>
                        </div>

                        <?php if (!empty($s['pembahasan']) || !empty($s['rubrik_penilaian'])): ?>
                            <div style="margin-top: 6px; font-size: 9pt; color: #475569;">
                                <strong>Pedoman Penskoran / Pembahasan:</strong><br>
                                <?= nl2br(format_cbt_math_output($s['pembahasan'] ?: $s['rubrik_penilaian'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
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
        if (window.MathJax && window.MathJax.typesetPromise) {
            window.MathJax.typesetPromise();
        }
    });

    function printDocument() {
        const doPrint = () => { window.print(); };
        if (window.MathJax && window.MathJax.typesetPromise) {
            window.MathJax.typesetPromise().then(() => {
                setTimeout(doPrint, 150);
            }).catch(() => {
                setTimeout(doPrint, 150);
            });
        } else {
            setTimeout(doPrint, 150);
        }
    }
    </script>
</body>
</html>
