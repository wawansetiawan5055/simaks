<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($judul ?? 'Laporan') ?></title>
    
    <style>
        /* CSS Reset Sederhana */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', Arial, sans-serif; /* Dompdf lebih baik dengan font dasar */
            font-size: 10pt; 
        }
        
        /* --- KOP SURAT (Menggunakan data $kop Anda) --- */
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            width: 100%;
        }
        /* Menggunakan tabel untuk layout kop surat agar kompatibel dengan Dompdf */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table td {
            padding: 0;
            vertical-align: top;
        }
        .kop-logo {
            width: 100px;
            text-align: left;
        }
        .kop-logo img {
            width: 90px; /* Sesuaikan ukuran logo */
            height: auto;
        }
        .kop-text {
            text-align: center;
            line-height: 1.4;
        }
        .kop-text h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }
        .kop-text h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .kop-text p {
            font-size: 9pt;
            margin: 0;
        }
        
        /* Judul Laporan */
        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* --- TABEL DATA UTAMA --- */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px 6px; /* Padding lebih kecil untuk PDF */
            text-align: left;
            word-wrap: break-word; 
        }
        .main-table th {
            background-color: #f2f2f2; 
            font-weight: bold;
            text-align: center;
        }
        
        /* Fix untuk Dompdf agar tidak memotong baris tabel di antara halaman */
        .main-table tbody tr {
            page-break-inside: avoid;
        }

        /* --- TANDA TANGAN --- */
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 250px; 
            text-align: center;
            line-height: 1.5;
            float: right; /* Pindahkan ke kanan */
        }
        .signature-box .signature-placeholder {
            height: 60px; 
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .signature-box .nama-kepsek {
            font-weight: bold;
            text-decoration: underline;
        }

        /* --- FOOTER HALAMAN (Dompdf) --- */
        footer {
            position: fixed; 
            bottom: -20px; /* Sesuaikan jika terpotong */
            left: 0px; 
            right: 0px;
            height: 50px; 
            
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
            font-size: 8pt;
            color: #555;
        }
    </style>
</head>
<body>

    <header class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="<?= realpath(__DIR__ . '/../../public/' . get_app_logo()) ?>" alt="Logo">
                </td>
                <td class="kop-text">
                    <h1>YAYASAN TARBIYATUSHIBYAN INDONESIA</h1>
                    <h2><?= htmlspecialchars($kop_nama ?? 'NAMA SEKOLAH') ?></h2>
                    <p>NPSN: <?= htmlspecialchars($kop_npsn ?? 'N/A') ?></p>
                    <p><?= htmlspecialchars($kop_alamat ?? 'Alamat Sekolah') ?></p>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Dokumen ini digenerate melalui SIMAKS - Sistem Informasi Manajemen Akademik Sekolah
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
                            <td><?= htmlspecialchars($cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="signature-section">
            <tr>
                <td>
                    <div class="signature-box">
                        Mengetahui,<br>
                        Kepala Sekolah
                        <div class="signature-placeholder"></div>
                        <div class="nama-kepsek">( ....................................... )</div> 
                    </div>
                </td>
            </tr>
        </table>
    </main>

</body>
</html>