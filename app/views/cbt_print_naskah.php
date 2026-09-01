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
    <title>Naskah Soal - <?= htmlspecialchars($paket['nama_paket']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        },
        options: {
            skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
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
            min-height: 297mm;
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
            width: 75px;
            height: auto;
            margin-right: 15px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h3 { margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: normal; }
        .kop-text h2 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .kop-text p { margin: 2px 0 0 0; font-size: 9pt; font-style: italic; }

        .exam-title-box {
            text-align: center;
            margin-bottom: 12px;
        }
        .exam-title-box h4 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
            margin-bottom: 15px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
        }
        .meta-table td {
            padding: 3px 6px;
            vertical-align: top;
        }

        .petunjuk-box {
            border: 1px dashed #000;
            padding: 8px 12px;
            font-size: 9pt;
            margin-bottom: 18px;
            background: #fafafa;
        }
        .petunjuk-box strong { display: block; margin-bottom: 3px; }
        .petunjuk-box ol { margin: 0; padding-left: 18px; }

        .section-header {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11pt;
            margin: 16px 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .soal-item {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .soal-header-row {
            display: flex;
            align-items: flex-start;
        }
        .soal-number {
            font-weight: bold;
            min-width: 25px;
        }
        .soal-text {
            flex: 1;
            text-align: justify;
        }
        .stimulus-box {
            border-left: 2px solid #000;
            padding: 4px 10px;
            margin: 4px 0 8px 0;
            font-size: 10pt;
            font-style: italic;
            background: #fafafa;
        }

        .opsi-grid {
            margin-top: 6px;
            margin-left: 25px;
        }
        .opsi-row {
            margin-bottom: 4px;
            display: flex;
            align-items: flex-start;
        }
        .opsi-label {
            font-weight: bold;
            min-width: 22px;
        }
        .opsi-content {
            flex: 1;
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
                <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="page-container">
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <?php if (!empty($sekolah['logo'])): ?>
                <img src="<?= htmlspecialchars($sekolah['logo']) ?>" class="kop-logo" alt="Logo Sekolah">
            <?php else: ?>
                <img src="uploads/logo_tutwuri.png" onerror="this.style.display='none'" class="kop-logo" alt="Logo">
            <?php endif; ?>
            <div class="kop-text">
                <h3><?= htmlspecialchars($sekolah['yayasan'] ?? 'PEMERINTAH DAERAH PROVINSI') ?></h3>
                <h2><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'SMA NEGERI / SWASTA') ?></h2>
                <p><?= htmlspecialchars($sekolah['alamat_lengkap'] ?? $sekolah['alamat'] ?? 'Jl. Pendidikan No. 1') ?> | Telp: <?= htmlspecialchars($sekolah['telepon'] ?? '-') ?> | Email: <?= htmlspecialchars($sekolah['email'] ?? '-') ?></p>
            </div>
        </div>

        <!-- JUDUL ASESMEN -->
        <div class="exam-title-box">
            <h4>NASKAH SOAL <?= htmlspecialchars(strtoupper($paket['jenis_asesmen'] ?? 'SUMATIF AKHIR SEMESTER')) ?></h4>
            <div style="font-size: 10.5pt; font-weight: bold;">TAHUN AJARAN <?= htmlspecialchars($paket['tahun_ajaran'] ?? date('Y') . '/' . (date('Y')+1)) ?></div>
        </div>

        <!-- TABEL IDENTITAS UJIAN -->
        <table class="meta-table">
            <tr>
                <td width="18%"><strong>Mata Pelajaran</strong></td>
                <td width="2%">:</td>
                <td width="38%"><?= htmlspecialchars($paket['nama_mapel'] ?? '-') ?></td>
                <td width="16%"><strong>Hari, Tanggal</strong></td>
                <td width="2%">:</td>
                <td width="24%"><?= date('l, d F Y') ?></td>
            </tr>
            <tr>
                <td><strong>Tingkat / Kelas</strong></td>
                <td>:</td>
                <td>Kelas <?= htmlspecialchars($paket['tingkat'] ?? 'X') ?></td>
                <td><strong>Alokasi Waktu</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($paket['alokasi_waktu'] ?? '90 Menit') ?></td>
            </tr>
            <tr>
                <td><strong>Program / Jurusan</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($paket['jurusan'] ?? 'Semua Program') ?></td>
                <td><strong>Guru Pengampu</strong></td>
                <td>:</td>
                <td><?= htmlspecialchars($paket['penyusun'] ?? $paket['nama_guru'] ?? 'Guru Mata Pelajaran') ?></td>
            </tr>
        </table>

        <!-- PETUNJUK PENGERJAAN -->
        <div class="petunjuk-box">
            <strong>PETUNJUK UMUM:</strong>
            <ol>
                <li>Periksa dan bacalah lembar soal dengan teliti sebelum Anda menjawabnya.</li>
                <li>Tuliskan identitas Anda (Nama, Kelas, Nomor Peserta) pada lembar jawaban yang tersedia.</li>
                <li>Laporkan kepada pengawas ujian jika terdapat tulisan yang kurang jelas, rusak, atau jumlah soal kurang.</li>
                <li>Dahulukan menjawab soal-soal yang Anda anggap mudah.</li>
                <li>Periksalah kembali seluruh pekerjaan Anda sebelum diserahkan kepada pengawas ujian.</li>
            </ol>
        </div>

        <!-- BUTIR-BUTIR SOAL -->
        <?php 
            $soal_pg = array_values(array_filter($soal_list, function($s) { return $s['tipe_soal'] === 'pg'; }));
            $soal_tf = array_values(array_filter($soal_list, function($s) { return $s['tipe_soal'] === 'tf'; }));
            $soal_essay = array_values(array_filter($soal_list, function($s) { return $s['tipe_soal'] === 'essay'; }));
            $global_no = 1;
        ?>

        <?php if (!empty($soal_pg)): ?>
            <div class="section-header">A. PILIHAN GANDA (Pilihlah salah satu jawaban yang paling tepat!)</div>
            <?php foreach ($soal_pg as $s): ?>
                <div class="soal-item">
                    <div class="soal-header-row">
                        <div class="soal-number"><?= $global_no++ ?>.</div>
                        <div class="soal-text">
                            <?php if (!empty($s['stimulus'])): ?>
                                <div class="stimulus-box"><?= nl2br(format_cbt_math_output($s['stimulus'])) ?></div>
                            <?php endif; ?>
                            
                            <div class="pertanyaan-text mb-2"><?= format_cbt_math_output($s['pertanyaan']) ?></div>

                            <?php if (!empty($s['media_url']) && $s['media_tipe'] === 'gambar'): ?>
                                <div style="margin: 6px 0;"><img src="<?= htmlspecialchars($s['media_url']) ?>" style="max-height: 180px; max-width: 100%;"></div>
                            <?php endif; ?>

                            <?php if (!empty($s['opsi_list'])): ?>
                                <div class="opsi-grid">
                                    <?php foreach ($s['opsi_list'] as $o): ?>
                                        <div class="opsi-row">
                                            <div class="opsi-label"><?= $o['label'] ?>.</div>
                                            <div class="opsi-content">
                                                <?= format_cbt_math_output($o['isi_opsi']) ?>
                                                <?php if (!empty($o['gambar'])): ?>
                                                    <div><img src="<?= htmlspecialchars($o['gambar']) ?>" style="max-height: 70px;"></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($soal_tf)): ?>
            <div class="section-header">B. BENAR / SALAH (Tentukan apakah pernyataan berikut Benar atau Salah!)</div>
            <?php foreach ($soal_tf as $s): ?>
                <div class="soal-item">
                    <div class="soal-header-row">
                        <div class="soal-number"><?= $global_no++ ?>.</div>
                        <div class="soal-text">
                            <?php if (!empty($s['stimulus'])): ?>
                                <div class="stimulus-box"><?= nl2br(format_cbt_math_output($s['stimulus'])) ?></div>
                            <?php endif; ?>
                            <div class="pertanyaan-text"><?= format_cbt_math_output($s['pertanyaan']) ?></div>
                            <div style="margin-left: 20px; margin-top: 6px; font-weight: bold;">
                                [ &nbsp;&nbsp;&nbsp; ] BENAR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ &nbsp;&nbsp;&nbsp; ] SALAH
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($soal_essay)): ?>
            <div class="section-header">C. ESAI / URAIAN (Jawablah pertanyaan-pertanyaan berikut dengan jelas dan terperinci!)</div>
            <?php foreach ($soal_essay as $s): ?>
                <div class="soal-item">
                    <div class="soal-header-row">
                        <div class="soal-number"><?= $global_no++ ?>.</div>
                        <div class="soal-text">
                            <?php if (!empty($s['stimulus'])): ?>
                                <div class="stimulus-box"><?= nl2br(format_cbt_math_output($s['stimulus'])) ?></div>
                            <?php endif; ?>
                            <div class="pertanyaan-text"><?= format_cbt_math_output($s['pertanyaan']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px; font-weight: bold; font-style: italic;">
            --== Selamat Mengerjakan &amp; Semoga Sukses ==--
        </div>
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
