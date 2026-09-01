<?php
// app/views/cbt_print_kartu_peserta.php
// Cetak Kartu Peserta Ujian CBT (Grid 6 per lembar A4 siap cetak)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta Ujian - <?= htmlspecialchars($jadwal['nama_ujian'] ?? 'CBT SIMAKS') ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 15px;
        }
        .page {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 10mm;
        }
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8mm;
        }
        .kartu-item {
            border: 1.5px solid #1e293b;
            border-radius: 8px;
            padding: 10px 12px;
            page-break-inside: avoid;
            background: #fff;
            position: relative;
        }
        .kartu-header {
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #1e293b;
            padding-bottom: 6px;
            margin-bottom: 8px;
            gap: 10px;
        }
        .kartu-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }
        .kartu-header-text h4 {
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            line-height: 1.2;
        }
        .kartu-header-text h5 {
            font-size: 0.68rem;
            font-weight: 700;
            color: #2563eb;
            margin-top: 1px;
            line-height: 1.2;
        }
        .kartu-header-text p {
            font-size: 0.58rem;
            color: #64748b;
            margin-top: 1px;
        }
        .kartu-body {
            display: flex;
            gap: 10px;
        }
        .kartu-table {
            width: 100%;
            font-size: 0.68rem;
            border-collapse: collapse;
        }
        .kartu-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .kartu-table td.label {
            width: 80px;
            font-weight: 600;
            color: #334155;
        }
        .kartu-table td.val {
            font-weight: 700;
            color: #0f172a;
        }
        .kartu-foto-box {
            width: 60px;
            height: 75px;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            color: #94a3b8;
            background: #f8fafc;
            flex-shrink: 0;
            text-align: center;
        }
        .kartu-footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 0.62rem;
        }
        .account-box {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 3px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.68rem;
            font-weight: 700;
        }
        .ttd-box {
            text-align: center;
            font-size: 0.6rem;
        }
        .ttd-line {
            margin-top: 24px;
            border-bottom: 1px solid #1e293b;
            font-weight: 700;
        }
        
        .no-print-bar {
            background: #1e1b4b;
            color: #fff;
            padding: 12px 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .page {
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .kartu-item {
                border-color: #000;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>🖨️ Cetak Kartu Peserta Ujian CBT</strong> &bull; Total: <?= count($peserta_list) ?> Siswa
            <div style="font-size: 0.8rem; opacity: 0.8;">Format Layout: 6 - 8 Kartu per lembar kertas A4</div>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Cetak Kartu Peserta Sekarang</button>
        </div>
    </div>

    <div class="page">
        <div class="grid-cards">
            <?php foreach ($peserta_list as $p): ?>
                <?php
                    $nisn = $p['nisn'] ?: '-';
                    $no_peserta = !empty($p['no_peserta']) ? $p['no_peserta'] : ('CBT-' . str_pad($p['id_siswa'], 5, '0', STR_PAD_LEFT));
                    $ruang = $p['ruang'] ?: 'Ruang 01';
                    $sesi = $p['sesi'] ?: 'Sesi 1';
                ?>
                <div class="kartu-item">
                    <div class="kartu-header">
                        <?php if (!empty($sekolah['logo'])): ?>
                            <img src="<?= htmlspecialchars($sekolah['logo']) ?>" class="kartu-logo" alt="Logo">
                        <?php else: ?>
                            <div style="width: 38px; height: 38px; border-radius: 6px; background: #3b82f6; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                                CBT
                            </div>
                        <?php endif; ?>
                        <div class="kartu-header-text">
                            <h4><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'SMA PLUS AL-MANSHURIYAH') ?></h4>
                            <h5>KARTU PESERTA ASESMEN CBT</h5>
                            <p><?= htmlspecialchars($jadwal['nama_ujian'] ?? 'UJIAN AKADEMIK') ?> &bull; TA 2026/2027</p>
                        </div>
                    </div>

                    <div class="kartu-body">
                        <table class="kartu-table">
                            <tr>
                                <td class="label">No. Peserta</td>
                                <td>:</td>
                                <td class="val" style="color: #2563eb;"><?= htmlspecialchars($no_peserta) ?></td>
                            </tr>
                            <tr>
                                <td class="label">Nama Lengkap</td>
                                <td>:</td>
                                <td class="val"><?= htmlspecialchars($p['nama_siswa']) ?></td>
                            </tr>
                            <tr>
                                <td class="label">NISN / NIPD</td>
                                <td>:</td>
                                <td><?= htmlspecialchars($nisn) ?> / <?= htmlspecialchars($p['nipd'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="label">Kelas / Rombel</td>
                                <td>:</td>
                                <td class="val"><?= htmlspecialchars($p['nama_kelas'] ?? $jadwal['nama_kelas'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="label">Ruang &amp; Sesi</td>
                                <td>:</td>
                                <td class="val"><?= htmlspecialchars($ruang) ?> &bull; <?= htmlspecialchars($sesi) ?></td>
                            </tr>
                        </table>

                        <div class="kartu-foto-box">
                            <?php if (!empty($p['foto'])): ?>
                                <img src="<?= htmlspecialchars($p['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;" alt="Foto">
                            <?php else: ?>
                                <span>FOTO<br>2 x 3</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="kartu-footer">
                        <div>
                            <span style="display: block; font-size: 0.58rem; color: #64748b; margin-bottom: 2px;">Akun Login Siswa:</span>
                            <div class="account-box">
                                User: <strong><?= htmlspecialchars($nisn) ?></strong> &bull; PIN: <strong><?= htmlspecialchars($p['token'] ?? '123456') ?></strong>
                            </div>
                        </div>
                        <div class="ttd-box">
                            <span>Panitia Pelaksana,</span>
                            <div class="ttd-line">Kepala Sekolah</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
