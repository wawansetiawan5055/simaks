<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Surat - <?= $surat['nomor_surat'] ?></title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
            line-height: 1.5;
        }
        .container {
            width: 210mm; /* A4 Width */
            margin: auto;
            padding: 20mm;
            box-sizing: border-box;
        }
        
        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 22pt;
            text-transform: uppercase;
            font-weight: bold;
        }
        .kop-text p {
            margin: 2px 0;
            font-size: 10pt;
            font-style: italic;
        }

        /* Isi Surat */
        .surat-info {
            margin-bottom: 30px;
        }
        .surat-info table {
            width: 100%;
        }
        .surat-info td {
            vertical-align: top;
            font-size: 12pt;
        }
        
        .isi-surat {
            font-size: 12pt;
            text-align: justify;
            min-height: 300px;
        }
        
        /* Signature */
        .signature-box {
            margin-top: 50px;
            float: right;
            width: 300px;
            text-align: center;
        }
        .signature-space {
            height: 80px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Optimization */
        @media print {
            body { background: none; }
            .container { padding: 0; width: 100%; }
            .no-print { display: none; }
        }
        
        /* Floating Button for Testing */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-family: sans-serif;
            font-weight: bold;
        }
        .no-print:hover { background: #0056b3; }
    </style>
</head>
<body onload="window.print()">

    <button class="no-print" onclick="window.print()">Cetak Sekarang</button>

    <div class="container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <?php if(!empty($sekolah['logo'])): ?>
                <img src="<?= BASE_URL ?>uploads/<?= $sekolah['logo'] ?>" class="kop-logo" alt="Logo">
            <?php else: ?>
                <div style="width: 80px;"></div>
            <?php endif; ?>
            <div class="kop-text">
                <h2><?= $sekolah['nama_yayasan'] ?? 'YAYASAN PENDIDIKAN' ?></h2>
                <h1><?= $sekolah['nama_sekolah'] ?></h1>
                <p><?= $sekolah['alamat'] ?>, Telp: <?= $sekolah['telepon'] ?? $sekolah['telp'] ?? '-' ?></p>
                <p>Email: <?= $sekolah['email'] ?> | Website: <?= $sekolah['website'] ?></p>
                <p>NPSN: <?= $sekolah['npsn'] ?></p>
            </div>
        </div>

        <!-- Nomor & Perihal -->
        <div class="surat-info">
            <table>
                <tr>
                    <td width="80">Nomor</td>
                    <td width="20">:</td>
                    <td><?= $surat['nomor_surat'] ?></td>
                    <td align="right"><?= date('d F Y', strtotime($surat['tgl_surat'])) ?></td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>-</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>:</td>
                    <td><strong><?= $surat['perihal'] ?></strong></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <!-- Tujuan -->
        <div class="surat-tujuan" style="margin-bottom: 30px;">
            <p>Kepada Yth,<br>
            <strong><?= $surat['tujuan'] ?></strong><br>
            di Tempat</p>
        </div>

        <!-- Isi -->
        <div class="isi-surat">
            <?= nl2br($surat['isi_surat']) ?>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-box">
            <p><?= date('d F Y') ?><br>
            Kepala Sekolah,</p>
            <div class="signature-space"></div>
            <p class="signature-name"><?= $sekolah['nama_kepala_sekolah'] ?? 'H. DADUN ABDUL MANAF, S.Pd., M.M.' ?></p>
            <p>NIP. <?= $sekolah['nip_kepala_sekolah'] ?? '-' ?></p>
        </div>

        <div style="clear: both;"></div>
    </div>

</body>
</html>
