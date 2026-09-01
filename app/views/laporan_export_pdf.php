<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($judul ?? 'Laporan') ?></title>
    
    <?php
    $margin_top    = !empty($kop['margin_atas']) ? (int)$kop['margin_atas'] : 15;
    $margin_bottom = !empty($kop['margin_bawah']) ? (int)$kop['margin_bawah'] : 15;
    $margin_left   = !empty($kop['margin_kiri']) ? (int)$kop['margin_kiri'] : 20;
    $margin_right  = !empty($kop['margin_kanan']) ? (int)$kop['margin_kanan'] : 15;
    ?>
    
    <style>
        @page {
            margin-top: <?= $margin_top ?>mm;
            margin-bottom: <?= $margin_bottom ?>mm;
            margin-left: <?= $margin_left ?>mm;
            margin-right: <?= $margin_right ?>mm;
        }
        
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 9pt;
            color: #111;
        }
        
        /* Judul Laporan */
        .report-title {
            text-align: center;
            font-size: 12.5pt;
            font-weight: bold;
            margin: 12px 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- TABEL DATA UTAMA --- */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: middle;
            font-size: 9pt;
        }
        .main-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .main-table thead {
            display: table-header-group;
        }
        .main-table tbody tr {
            page-break-inside: avoid;
        }

        /* --- TANDA TANGAN --- */
        .signature-section {
            margin-top: 25px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 260px; 
            text-align: center;
            line-height: 1.35;
            float: right;
            font-size: 9.5pt;
        }
        .signature-box .signature-placeholder {
            height: 55px; 
        }
        .signature-box .nama-kepsek {
            font-weight: bold;
            text-decoration: underline;
        }

        /* --- FOOTER HALAMAN (Dompdf) --- */
        footer {
            position: fixed; 
            bottom: -10mm;
            left: 0px; 
            right: 0px;
            height: 8mm; 
            border-top: 0.5px solid #ccc;
            padding-top: 2px;
            text-align: center;
            font-size: 7.5pt;
            color: #666;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT UNIVERSAL RESMI -->
    <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

    <footer>
        Dokumen ini dicetak melalui SIMAKS - Sistem Informasi Manajemen Akademik Sekolah
    </footer>

    <main>
        <h2 class="report-title"><?= htmlspecialchars($judul ?? 'Laporan') ?></h2>

        <table class="main-table">
            <thead>
                <tr>
                    <?php foreach($kolom as $k): ?>
                        <th><?= htmlspecialchars($k) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?= count($kolom) ?>" style="text-align: center; color: #777;">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <?php foreach($row as $cell): ?>
                            <td style="vertical-align: middle; text-align: <?= (strpos($cell, '<img') !== false) ? 'center' : 'left' ?>;">
                                <?php if (strpos($cell, '<img') !== false): ?>
                                    <?= $cell ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($cell) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="signature-section">
            <tr>
                <td>
                    <div class="signature-box">
                        <?= htmlspecialchars($profil['kota'] ?? $kop['kota'] ?? $sekolah['kota'] ?? 'Sukabumi') ?>, <?= tgl_indo() ?><br>
                        Kepala Sekolah
                        <div class="signature-placeholder"></div>
                        <div class="nama-kepsek"><b><u><?= htmlspecialchars($kop['nama_kepala_sekolah'] ?? $kop['nama_kepsek'] ?? '.......................................') ?></u></b></div>
                        <?php if (!empty($kop['nip_kepala_sekolah'])): ?>
                            <div style="font-size: 8.5pt;">NIP. <?= htmlspecialchars($kop['nip_kepala_sekolah']) ?></div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>
    </main>

</body>
</html>