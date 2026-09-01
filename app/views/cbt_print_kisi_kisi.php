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
    <title>Kisi-Kisi Asesmen - <?= htmlspecialchars($paket['nama_paket']) ?></title>
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
            width: 297mm; /* Landscape A4 */
            min-height: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 12mm 15mm;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .no-print-bar {
            width: 297mm;
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
            margin-bottom: 12px;
        }
        .kop-logo {
            width: 65px;
            height: auto;
            margin-right: 15px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: normal; }
        .kop-text h2 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .kop-text p { margin: 2px 0 0 0; font-size: 8.5pt; font-style: italic; }

        .title-box {
            text-align: center;
            margin-bottom: 10px;
        }
        .title-box h4 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .kisi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .kisi-table th, .kisi-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        .kisi-table th {
            background-color: #f2f2f2;
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
            @page {
                size: A4 landscape;
                margin: 10mm;
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
                <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF (Landscape)
            </button>
        </div>
    </div>

    <div class="page-container">
        <!-- KOP SURAT RESMI STANDAR UNIVERSAL -->
        <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

        <div class="title-box">
            <h4>KISI-KISI PENULISAN SOAL ASESMEN STANDAR KURIKULUM MERDEKA</h4>
            <div style="font-size: 10pt; font-weight: bold;">JENIS ASESMEN: <?= htmlspecialchars(strtoupper($paket['jenis_asesmen'] ?? 'SUMATIF AKHIR SEMESTER')) ?></div>
        </div>

        <!-- IDENTITAS MATA PELAJARAN -->
        <table class="meta-table">
            <tr>
                <td width="15%"><strong>Satuan Pendidikan</strong></td>
                <td width="2%">:</td>
                <td width="33%"><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'SMA') ?></td>
                <td width="15%"><strong>Alokasi Waktu</strong></td>
                <td width="2%">:</td>
                <td width="33%"><?= htmlspecialchars($paket['alokasi_waktu'] ?? '90 Menit') ?></td>
            </tr>
            <tr>
                <td><strong>Mata Pelajaran</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($paket['nama_mapel'] ?? '-') ?></td>
                <td><strong>Jumlah Soal</strong></td>
                <td>:</td>
                <td><?= count($soal_list) ?> Butir Soal</td>
            </tr>
            <tr>
                <td><strong>Kelas / Fase</strong></td>
                <td>:</td>
                <td>Kelas <?= htmlspecialchars($paket['tingkat'] ?? 'X') ?></td>
                <td><strong>Tahun Pelajaran</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($paket['tahun_ajaran'] ?? date('Y').'/'.(date('Y')+1)) ?> (Semester <?= htmlspecialchars($paket['semester'] ?? 'Ganjil') ?>)</td>
            </tr>
        </table>

        <!-- TABEL KISI-KISI -->
        <table class="kisi-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="18%">Capaian Pembelajaran (CP)</th>
                    <th width="20%">Tujuan Pembelajaran (TP)</th>
                    <th width="14%">Lingkup Materi</th>
                    <th width="24%">Indikator Soal</th>
                    <th width="6%">Bentuk</th>
                    <th width="7%">Level</th>
                    <th width="7%">No. Soal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($soal_list)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">Belum ada butir soal pada paket ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($soal_list as $i => $s): ?>
                        <?php 
                            $lvl = strtoupper($s['level_kognitif'] ?? 'L2');
                            $is_hots = ($lvl === 'L3' || strpos($lvl, 'HOTS') !== false);
                            $lvl_display = $is_hots ? 'L3 (HOTS)' : (($lvl === 'L1') ? 'L1 (Lots)' : 'L2 (Mots)');
                            $bentuk = ($s['tipe_soal'] === 'pg') ? 'PG' : (($s['tipe_soal'] === 'essay') ? 'Uraian' : 'B/S');
                        ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($s['deskripsi_cp'] ?? $s['kode_cp'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($s['deskripsi_tp'] ?? $s['kode_tp'] ?? '-') ?></td>
                            <td><strong><?= format_cbt_math_output($s['lingkup_materi'] ?: '-') ?></strong></td>
                            <td><?= format_cbt_math_output($s['indikator_soal'] ?: ('Disajikan stimulus, peserta didik dapat menyelesaikan permasalahan terkait ' . ($s['lingkup_materi'] ?? 'materi.'))) ?></td>
                            <td style="text-align: center;"><?= $bentuk ?></td>
                            <td style="text-align: center; font-weight: bold;"><?= $lvl_display ?></td>
                            <td style="text-align: center; font-weight: bold;"><?= $i + 1 ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

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
