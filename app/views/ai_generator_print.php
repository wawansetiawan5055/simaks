<?php
// File: app/views/ai_generator_print.php
// Template PDF untuk Modul Ajar Deep Learning
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($log['judul']) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2.5cm 2cm 2cm 2.5cm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
            line-height: 1.6;
            color: #111;
            margin: 0;
            padding: 0;
        }

        /* ========================
           KOP SURAT
        ======================== */
        .kop-container {
            display: table;
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            text-align: center;
        }
        .kop-logo img {
            width: 70px;
            height: 70px;
        }
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
        }
        .kop-sekolah {
            font-size: 17pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .kop-info {
            font-size: 10pt;
            margin: 2px 0 0 0;
            color: #333;
        }
        .kop-info-small {
            font-size: 9pt;
            color: #555;
            margin: 1px 0 0 0;
        }

        /* ========================
           JUDUL DOKUMEN
        ======================== */
        .doc-title-box {
            text-align: center;
            margin: 18px 0 16px 0;
            background: #f5f5f5;
            padding: 10px 0;
            border: 1px solid #ddd;
        }
        .doc-title-box .doc-type {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0;
        }
        .doc-title-box .doc-subtitle {
            font-size: 10pt;
            color: #555;
            margin: 2px 0 0 0;
        }

        /* ========================
           TABEL IDENTITAS
        ======================== */
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 11pt;
        }
        .identity-table td {
            padding: 4px 8px;
            vertical-align: top;
            border: 1px solid #bbb;
        }
        .identity-table td:first-child {
            width: 35%;
            font-weight: bold;
            background: #f9f9f9;
        }
        .identity-table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }

        /* ========================
           CONTENT AREA
        ======================== */
        .content-area {
            text-align: justify;
            hyphens: auto;
        }
        .content-area h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background: #e8e8e8;
            border-left: 5px solid #333;
            padding: 6px 10px;
            margin: 20px 0 10px 0;
            page-break-after: avoid;
        }
        .content-area h3 {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 14px 0 6px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
            page-break-after: avoid;
        }
        .content-area h4 {
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 4px 0;
            page-break-after: avoid;
        }
        .content-area p {
            margin: 0 0 8px 0;
        }
        .content-area ul, .content-area ol {
            margin: 0 0 8px 20px;
            padding: 0;
        }
        .content-area li {
            margin-bottom: 3px;
        }
        .content-area strong {
            font-weight: bold;
        }
        .content-area em {
            font-style: italic;
        }

        /* ========================
           TABEL dalam konten
        ======================== */
        .content-area table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px 0;
            font-size: 10.5pt;
            page-break-inside: avoid;
        }
        .content-area table, .content-area th, .content-area td {
            border: 1px solid #555;
        }
        .content-area th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            padding: 6px 8px;
        }
        .content-area td {
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
        }
        .content-area tr:nth-child(even) td {
            background-color: #fafafa;
        }

        /* ========================
           TANDA TANGAN
        ======================== */
        .footer-sign {
            margin-top: 40px;
            width: 100%;
            border-collapse: collapse;
        }
        .footer-sign td {
            border: none;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 5px;
            font-size: 11pt;
        }
        .footer-sign .ttd-space {
            height: 60px;
            display: block;
        }
        .footer-sign strong {
            text-decoration: underline;
        }

        /* ========================
           PAGE BREAK
        ======================== */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title-box">
        <p class="doc-type">MODUL AJAR DEEP LEARNING</p>
        <p class="doc-subtitle">(Mindful &bull; Meaningful &bull; Joyful)</p>
    </div>

    <!-- ISI KONTEN AI (BAGIAN I - IV) -->
    <div class="content-area">
        <?= str_ireplace('Fase/Kelas', 'Kelas/Fase', preg_replace('/<h2[^>]*>.*?<\/h2>/is', '', $log['konten_html'])) ?>
    </div>

    <!-- TANDA TANGAN -->
    <table class="footer-sign">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah
                <span class="ttd-space"></span>
                <strong><u><?= htmlspecialchars($sekolah['nama_kepala_sekolah'] ?? '..........................') ?></u></strong><br>
                NIP. ..........................
            </td>
            <td>
                &nbsp;
            </td>
            <td>
                <?= htmlspecialchars($sekolah['kota'] ?? '...........') ?>, <?= date('d F Y') ?><br>
                Guru Mata Pelajaran
                <span class="ttd-space"></span>
                <strong><u><?= htmlspecialchars($nama_guru ?? '..........................') ?></u></strong><br>
                NIP. ..........................
            </td>
        </tr>
    </table>

</body>
</html>
