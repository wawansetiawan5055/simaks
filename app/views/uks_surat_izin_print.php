<?php
// app/views/uks_surat_izin_print.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Sakit UKS - <?= htmlspecialchars($kunjungan['nama_pasien']) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 20px;
            margin: 0;
        }
        .page {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 30px 40px;
            box-sizing: border-box;
        }
        .kop-header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        .kop-header h2 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-header h3 {
            margin: 2px 0;
            font-size: 13pt;
            text-transform: uppercase;
        }
        .kop-header p {
            margin: 0;
            font-size: 9.5pt;
        }
        .surat-title {
            text-align: center;
            margin: 18px 0;
        }
        .surat-title h4 {
            margin: 0;
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .surat-title p {
            margin: 2px 0 0 0;
            font-size: 10pt;
        }
        .table-data {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .table-data td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .table-data td.label {
            width: 28%;
        }
        .table-data td.sep {
            width: 3%;
        }
        .table-data td.val {
            width: 69%;
        }
        .ttd-box {
            margin-top: 35px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .ttd-col {
            text-align: center;
            width: 45%;
        }
        .ttd-space {
            height: 70px;
        }
        @media print {
            body { padding: 0; }
            .page { border: none; padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 20px; font-weight: bold; background: #0d9488; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; margin-left: 8px; border: 1px solid #ccc; background: #f8fafc; border-radius: 6px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="page">
        <!-- KOP SURAT -->
        <div class="kop-header">
            <h2><?= strtoupper(htmlspecialchars($sekolah['nama_sekolah'] ?? 'SEKOLAH MENENGAH')) ?></h2>
            <h3>UNIT KESEHATAN SEKOLAH (UKS)</h3>
            <p><?= htmlspecialchars($sekolah['alamat'] ?? '') ?> <?= !empty($sekolah['telepon']) ? ' | Telp: ' . htmlspecialchars($sekolah['telepon']) : '' ?></p>
        </div>

        <!-- JUDUL SURAT -->
        <div class="surat-title">
            <h4>SURAT KETERANGAN PEMERIKSAAN KESEHATAN UKS</h4>
            <p>Nomor: UKS/<?= date('Ym', strtotime($kunjungan['tanggal'])) ?>/<?= str_pad($kunjungan['id_kunjungan'], 4, '0', STR_PAD_LEFT) ?></p>
        </div>

        <p>Yang bertanda tangan di bawah ini, Petugas Unit Kesehatan Sekolah (UKS), menerangkan bahwa:</p>

        <table class="table-data">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="sep">:</td>
                <td class="val"><strong><?= htmlspecialchars($kunjungan['nama_pasien']) ?></strong></td>
            </tr>
            <tr>
                <td class="label">Tipe Pasien / Kelas</td>
                <td class="sep">:</td>
                <td class="val"><?= htmlspecialchars($kunjungan['tipe_pasien']) ?> / <?= htmlspecialchars($kunjungan['kelas_unit']) ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal &amp; Waktu Masuk</td>
                <td class="sep">:</td>
                <td class="val"><?= date('d F Y', strtotime($kunjungan['tanggal'])) ?>, Pukul <?= substr($kunjungan['jam_masuk'], 0, 5) ?> WIB</td>
            </tr>
            <tr>
                <td class="label">Keluhan / Gejala</td>
                <td class="sep">:</td>
                <td class="val"><?= htmlspecialchars($kunjungan['keluhan']) ?></td>
            </tr>
            <?php if (!empty($kunjungan['suhu_tubuh']) || !empty($kunjungan['tekanan_darah'])): ?>
            <tr>
                <td class="label">Tanda Vital</td>
                <td class="sep">:</td>
                <td class="val">
                    <?= !empty($kunjungan['suhu_tubuh']) ? 'Suhu: ' . htmlspecialchars($kunjungan['suhu_tubuh']) . ' °C' : '' ?>
                    <?= !empty($kunjungan['tekanan_darah']) ? ' | Tensi: ' . htmlspecialchars($kunjungan['tekanan_darah']) : '' ?>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label">Diagnosa Awal</td>
                <td class="sep">:</td>
                <td class="val"><?= htmlspecialchars($kunjungan['diagnosa_awal'] ?: 'Pemeriksaan umum / observasi awal') ?></td>
            </tr>
            <tr>
                <td class="label">Tindakan / Pertolongan</td>
                <td class="sep">:</td>
                <td class="val"><?= htmlspecialchars($kunjungan['tindakan'] ?: 'Istirahat dan penanganan pertama') ?></td>
            </tr>
            <?php if (!empty($kunjungan['obat_diberikan'])): ?>
            <tr>
                <td class="label">Obat yang Diberikan</td>
                <td class="sep">:</td>
                <td class="val"><?= htmlspecialchars($kunjungan['obat_diberikan']) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label">Rekomendasi / Tindak Lanjut</td>
                <td class="sep">:</td>
                <td class="val"><strong><?= htmlspecialchars($kunjungan['status_tindak_lanjut']) ?></strong></td>
            </tr>
            <?php if (!empty($kunjungan['keterangan'])): ?>
            <tr>
                <td class="label">Keterangan Tambahan</td>
                <td class="sep">:</td>
                <td class="val"><?= htmlspecialchars($kunjungan['keterangan']) ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk diketahui oleh Guru Pengajar / Wali Kelas / Orang Tua / Pihak Terkait.</p>

        <!-- TANDA TANGAN -->
        <div class="ttd-box">
            <div class="ttd-col">
                <p>Mengetahui,<br>Guru Piket / Wali Kelas</p>
                <div class="ttd-space"></div>
                <p>( ..................................................... )</p>
            </div>
            <div class="ttd-col">
                <p><?= htmlspecialchars($sekolah['kota'] ?? 'Tempat') ?>, <?= date('d F Y', strtotime($kunjungan['tanggal'])) ?><br>Petugas UKS / Pemeriksa</p>
                <div class="ttd-space"></div>
                <p><strong><?= htmlspecialchars($kunjungan['petugas_jaga'] ?: 'Petugas UKS') ?></strong></p>
            </div>
        </div>
    </div>

</body>
</html>
