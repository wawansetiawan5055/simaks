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
    <title>Kunci & Pedoman Penskoran - <?= htmlspecialchars($paket['nama_paket']) ?></title>
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
            font-size: 11pt;
            line-height: 1.4;
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
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }
        .kop-logo {
            width: 70px;
            height: auto;
            margin-right: 15px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .kop-text h2 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .kop-text p { margin: 2px 0 0 0; font-size: 9pt; font-style: italic; }

        .key-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .key-grid-table th, .key-grid-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }
        .key-grid-table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .rubrik-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .rubrik-table th, .rubrik-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .rubrik-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            font-size: 10pt;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
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
                <i class="fas fa-print mr-1"></i> Cetak Kunci & Pedoman
            </button>
        </div>
    </div>

    <div class="page-container">
        <!-- KOP SURAT RESMI STANDAR UNIVERSAL -->
        <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

        <div style="text-align: center; margin-bottom: 15px;">
            <h4 style="margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold;">KUNCI JAWABAN &amp; PEDOMAN PENSKORAN ASESMEN</h4>
            <div style="font-size: 10.5pt; font-weight: bold;"><?= htmlspecialchars($paket['nama_paket']) ?> - KELAS <?= htmlspecialchars($paket['tingkat'] ?? 'X') ?></div>
            <div style="font-size: 9.5pt; font-style: italic;">Mata Pelajaran: <?= htmlspecialchars($paket['nama_mapel'] ?? '-') ?> &bull; Tahun Ajaran <?= htmlspecialchars($paket['tahun_ajaran'] ?? date('Y').'/'.(date('Y')+1)) ?></div>
        </div>

        <!-- MATRIKS KUNCI PILIHAN GANDA -->
        <?php 
            $pg_items = array_values(array_filter($soal_list, function($s) { return $s['tipe_soal'] === 'pg' || $s['tipe_soal'] === 'tf'; }));
            $essay_items = array_values(array_filter($soal_list, function($s) { return $s['tipe_soal'] === 'essay'; }));
        ?>

        <?php if (!empty($pg_items)): ?>
            <div style="font-weight: bold; margin-bottom: 8px; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 2px;">
                I. KUNCI JAWABAN PILIHAN GANDA &amp; BENAR/SALAH
            </div>
            
            <table class="key-grid-table">
                <thead>
                    <tr>
                        <?php for ($col = 1; $col <= 5; $col++): ?>
                            <th width="10%">No.</th>
                            <th width="10%">Kunci</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $total_pg = count($pg_items);
                        $rows = ceil($total_pg / 5);
                        for ($r = 0; $r < $rows; $r++):
                    ?>
                        <tr>
                            <?php for ($c = 0; $c < 5; $c++): ?>
                                <?php 
                                    $idx = $r + ($c * $rows);
                                    if ($idx < $total_pg):
                                        $it = $pg_items[$idx];
                                        $kc = $it['kunci_pg'] ?? $it['kunci_jawaban'] ?? '-';
                                ?>
                                    <td style="font-weight: bold; background: #fafafa;"><?= $idx + 1 ?></td>
                                    <td style="font-weight: bold; font-size: 11pt; color: #1e293b;"><?= htmlspecialchars($kc) ?></td>
                                <?php else: ?>
                                    <td>-</td>
                                    <td>-</td>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- PEDOMAN PENSKORAN & PEMBAHASAN ESAI -->
        <?php if (!empty($essay_items)): ?>
            <div style="font-weight: bold; margin-top: 15px; margin-bottom: 8px; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 2px;">
                II. KUNCI JAWABAN &amp; RUBRIK PENSKORAN ESAI / URAIAN
            </div>

            <table class="rubrik-table">
                <thead>
                    <tr>
                        <th width="8%">No.</th>
                        <th width="62%">Kunci Jawaban / Alternatif Solusi</th>
                        <th width="15%">Kriteria Skor</th>
                        <th width="15%">Skor Maks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($essay_items as $ei => $es): ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?= $ei + 1 ?></td>
                            <td>
                                <div style="font-weight: bold; margin-bottom: 3px; font-size: 9pt; color: #475569;">Pertanyaan: <?= format_cbt_math_output(mb_strimwidth(strip_tags($es['pertanyaan']), 0, 90, '...')) ?></div>
                                <div><?= nl2br(format_cbt_math_output($es['pembahasan'] ?: ($es['kunci_jawaban'] ?: 'Jawaban kontekstual sesuai pemahaman konsep.'))) ?></div>
                            </td>
                            <td>
                                <?= nl2br(format_cbt_math_output($es['rubrik_penilaian'] ?: 'Lengkap = Skor Maksimal; Sebagian = Setengah; Tidak Menjawab = 0')) ?>
                            </td>
                            <td style="text-align: center; font-weight: bold;"><?= (float)($es['bobot_soal'] ?? $es['bobot'] ?? 10) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- RUMUS PERHITUNGAN NILAI AKHIR -->
        <div style="border: 1px solid #000; padding: 10px 14px; background: #f8fafc; font-size: 9.5pt; margin-top: 15px;">
            <strong>PEDOMAN KONVERSI NILAI AKHIR (NA):</strong>
            <div style="margin-top: 4px;">
                $$\text{Nilai Akhir (NA)} = \left( \frac{\text{Total Skor Perolehan Peserta Didik}}{\text{Total Skor Maksimal Naskah}} \right) \times 100$$
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <table class="signature-table">
            <tr>
                <td width="50%">
                    Mengetahui,<br>
                    Kepala Sekolah
                    <br><br><br><br>
                    <strong><u><?= htmlspecialchars($sekolah['kepala_sekolah'] ?? 'Nama Kepala Sekolah, M.Pd') ?></u></strong><br>
                    NIP. <?= htmlspecialchars($sekolah['nip_kepala_sekolah'] ?? '19700101XXXXXXXXXX') ?>
                </td>
                <td width="50%">
                    <?= htmlspecialchars($sekolah['kota'] ?? 'Kota') ?>, <?= date('d F Y') ?><br>
                    Guru Mata Pelajaran
                    <br><br><br><br>
                    <strong><u><?= htmlspecialchars($paket['penyusun'] ?? $paket['nama_guru'] ?? 'Guru Pengampu, S.Pd') ?></u></strong><br>
                    NIP. <?= htmlspecialchars($paket['nip_guru'] ?? '-') ?>
                </td>
            </tr>
        </table>
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
