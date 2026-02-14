<?php
/**
 * keuangan_masuk_print.php
 * Template Kwitansi Penjualan / Pemasukan (Support Multi-Item)
 */
require_once '../app/helpers/DateHelper.php';
require_once '../app/helpers/NumberHelper.php';

$total_bayar = 0;
foreach ($rows as $r) {
    $total_bayar += $r['jumlah'];
}

$is_multiple = count($rows) > 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - <?= $is_multiple ? 'Gabungan' : $data['no_bukti'] ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11pt;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .kwitansi-container {
            width: 210mm; /* A4 Width */
            background-color: #fff;
            margin: 0 auto;
            padding: 15px;
            border: 2px solid #000;
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .logo {
            flex: 0 0 80px;
            margin-right: 15px;
        }

        .logo img {
            width: 100%;
            height: auto;
        }

        .school-info {
            flex: 1;
        }

        .school-info h1 {
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
        }

        .school-info p {
            font-size: 9pt;
            margin: 2px 0;
        }

        .receipt-title {
            text-align: right;
            border-left: 2px solid #000;
            padding-left: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .receipt-title h2 {
            margin: 0;
            font-size: 16pt;
            text-decoration: underline;
        }

        .receipt-title p {
            margin: 5px 0 0 0;
            font-weight: bold;
        }

        .content {
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .row {
            display: flex;
            margin-bottom: 12px;
            align-items: baseline;
        }

        .label {
            flex: 0 0 180px;
            font-weight: bold;
        }

        .colon {
            flex: 0 0 20px;
        }

        .value {
            flex: 1;
            border-bottom: 1px dashed #666;
            min-height: 20px;
        }

        .amount-box {
            font-size: 18pt;
            font-weight: bold;
            border: 3px double #000;
            padding: 10px 20px;
            display: inline-block;
            margin-top: 20px;
            background-color: #eee;
        }

        .arrears-info {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            font-size: 10pt;
            display: inline-block;
            background: #fff;
        }

        .arrears-info.warning {
            border-left: 5px solid #d33;
            color: #b00;
        }

        .arrears-info.success {
            border-left: 5px solid #28a745;
            color: #155724;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }

        .footer-left {
            font-size: 9pt;
            color: #777;
            max-width: 300px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-space {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .item-table th, .item-table td {
            text-align: left;
            padding: 5px 0;
        }

        .item-table th { border-bottom: 1px solid #000; }

        @media print {
            body { background-color: #fff; padding: 0; }
            .kwitansi-container { box-shadow: none; border: 1px solid #000; margin: 0; }
            .no-print { display: none; }
        }

        .toolbar {
            background: #333; color: #fff; padding: 10px;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; border-radius: 4px;
        }

        .btn-print-action {
            background: #28a745; color: #fff; border: none;
            padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="toolbar no-print">
        <span>Pratinjau Kwitansi: <strong><?= $is_multiple ? 'Gabungan (' . count($rows) . ' Item)' : $data['no_bukti'] ?></strong></span>
        <button onclick="window.print()" class="btn-print-action">
            <i class="fas fa-print"></i> Cetak Kwitansi
        </button>
    </div>

    <div class="kwitansi-container">
        <!-- Header / Kop -->
        <div class="header">
            <div class="logo">
                <?php if (!empty($kop['logo'])): ?>
                    <img src="assets/img/<?= $kop['logo'] ?>" alt="Logo">
                <?php else: ?>
                    <div style="width:100%; height:80px; background:#eee; display:flex; align-items:center; justify-content:center; font-size:10px;">LOGO</div>
                <?php endif; ?>
            </div>
            <div class="school-info">
                <h1><?= $kop['nama_sekolah'] ?? 'SIMAKS ACADEMY' ?></h1>
                <p><?= $kop['alamat'] ?? '-' ?></p>
                <p>Telp: <?= $kop['telepon'] ?? '-' ?> | Email: <?= $kop['email'] ?? '-' ?></p>
                <p>NPSN: <?= $kop['npsn'] ?? '-' ?></p>
            </div>
            <div class="receipt-title">
                <h2>KWITANSI</h2>
                <p style="font-size: 10pt;">No: <?= $is_multiple ? implode(', ', array_column($rows, 'no_bukti')) : $data['no_bukti'] ?></p>
            </div>
        </div>

        <div class="content">
            <div class="row">
                <div class="label">Telah Terima Dari</div>
                <div class="colon">:</div>
                <div class="value">
                    <strong><?= $data['nama_siswa'] ?: $data['referensi'] ?: '-' ?></strong>
                    <?= !empty($data['nama_kelas']) ? " (Kelas: " . $data['nama_kelas'] . ")" : "" ?>
                </div>
            </div>

            <div class="row">
                <div class="label">Uang Sejumlah</div>
                <div class="colon">:</div>
                <div class="value">
                    <i>--- <?= ucwords(NumberHelper::terbilang($total_bayar)) ?> Rupiah ---</i>
                </div>
            </div>

            <div class="row">
                <div class="label">Untuk Pembayaran</div>
                <div class="colon">:</div>
                <div class="value">
                    <?php if ($is_multiple): ?>
                        <table class="item-table">
                            <thead>
                                <tr>
                                    <th>Keterangan Item</th>
                                    <th style="text-align: right;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td>
                                        <?= $r['nama_jenis'] ?>
                                        <?= !empty($r['periode']) ? " (". DateHelper::getNamaBulan((int)substr($r['periode'], 5, 2)) . " " . substr($r['periode'], 0, 4) . ")" : "" ?>
                                    </td>
                                    <td style="text-align: right;"><?= number_format($r['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <?= $data['nama_jenis'] ?>
                        <?= !empty($data['periode']) ? " Periode " . DateHelper::getNamaBulan((int)substr($data['periode'], 5, 2)) . " " . substr($data['periode'], 0, 4) : "" ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$is_multiple && !empty($data['keterangan'])): ?>
            <div class="row">
                <div class="label">Catatan</div>
                <div class="colon">:</div>
                <div class="value"><?= $data['keterangan'] ?></div>
            </div>
            <?php endif; ?>

            <div class="row" style="margin-top: 20px;">
                <div class="label">Metode Pembayaran</div>
                <div class="colon">:</div>
                <div class="value"><?= $data['metode_pembayaran'] ?></div>
            </div>

            <div class="amount-box">
                <?= $is_multiple ? 'TOTAL: ' : '' ?><?= NumberHelper::formatRupiah($total_bayar) ?>,-
            </div>

            <br>
            <div class="arrears-info <?= ($sisa_tunggakan > 0) ? 'warning' : 'success' ?>">
                <strong>Informasi Tunggakan:</strong><br>
                <?php if ($sisa_tunggakan > 0): ?>
                    Sisa kewajiban pembayaran (Tagihan Belum Lunas) saat ini: 
                    <strong><?= NumberHelper::formatRupiah($sisa_tunggakan) ?>,-</strong>
                <?php else: ?>
                    <span class="text-success"><i class="fas fa-check-circle"></i> Status: Lunas / Tidak ada tunggakan.</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">
                <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
                <p style="font-style: italic;">Note: Simpanlah kwitansi ini sebagai bukti pembayaran yang sah.</p>
            </div>
            <div class="signature-box">
                <p><?= $kop['kota'] ?? 'Unit Sekolah' ?>, <?= DateHelper::formatTanggal(date('Y-m-d'), 'long') ?></p>
                <p>Kasir/Bendahara,</p>
                <div class="signature-space">
                    ( <?= $_SESSION['nama_lengkap'] ?? 'Petugas Sekolah' ?> )
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</body>
</html>
