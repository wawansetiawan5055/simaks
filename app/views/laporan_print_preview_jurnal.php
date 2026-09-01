<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($judul ?? 'Laporan Jurnal KBM') ?></title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        body,
        html {
            width: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .btn-container {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10000;
            background: #323639;
            height: 48px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(0, 0, 0, 0.2);
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            color: white;
        }

        .toolbar-title {
            font-size: 14px;
            font-weight: 500;
            color: #f1f1f1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 50%;
            display: flex;
            align-items: center;
        }

        .toolbar-title i {
            margin-right: 12px;
            font-size: 16px;
            opacity: 0.9;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .chrome-btn {
            background: transparent;
            border: none;
            color: #f1f1f1;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            outline: none;
            padding: 0;
        }

        .chrome-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .chrome-btn i {
            font-size: 16px;
        }

        .zoom-info {
            font-size: 12px;
            min-width: 45px;
            text-align: center;
            color: #ccc;
            user-select: none;
            font-weight: normal;
        }

        .divider {
            width: 1px;
            height: 24px;
            background: rgba(255, 255, 255, 0.15);
            margin: 0 10px;
        }

        body {
            padding-top: 48px; /* Offset for fixed toolbar */
            background-color: #525659;
            margin: 0;
            padding-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .a4-container {
            width: 28cm;
            max-width: 95%; /* Better responsiveness */
            min-height: 19cm;
            margin: 20px auto;
            background: white;
            padding: 1cm;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        /* KOP SURAT */
        .kop-surat {
            display: flex;
            align-items: flex-start;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            flex: 0 0 100px;
            margin-right: 20px;
            text-align: center;
        }

        .kop-logo img {
            width: 80px;
            height: auto;
        }

        .kop-text {
            flex: 1;
            text-align: center;
            line-height: 1.3;
        }

        .kop-text h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
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

        /* JUDUL KHUSUS JURNAL */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .report-title-main {
            font-size: 12pt;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .report-info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 11pt;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        /* TABEL */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }

        .main-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .col-center {
            text-align: center;
        }

        /* TANDA TANGAN (Kiri & Kanan) */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            .btn-container {
                display: none;
            }

            body {
                background-color: white;
            }

            .a4-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            @page {
                margin: 1cm;
            }
        }

        /* Gold Standard Button Styling - Proportionalized */
        .btn {
            height: 38px !important;
            width: 125px !important;
            padding: 0 15px !important;
            border: none !important;
            border-radius: 6px !important;
            cursor: pointer;
            font-size: 13px !important;
            font-weight: 600;
            margin: 0 10px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        @media screen {
            body { background-color: #525659; margin: 0; padding: 0; display: flex; flex-direction: column; align-items: center; }
            .page-sheet { background: white; width: 297mm; min-height: 210mm; padding: 15mm; margin: 20px auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); }
            .action-bar {
                position: sticky;
                top: 0;
                z-index: 1000;
                background: rgba(45, 45, 45, 0.8);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                padding: 12px 0;
                width: 100%;
                text-align: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            }
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            color: white;
        }

        .btn-print {
            background-color: #28a745;
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .btn-close-standard {
            background-color: #6c757d;
            color: white;
        }

        .btn-close-standard:hover {
            background-color: #5a6268;
            color: white;
        }

        .btn-container {
            /* Handled by sticky header logic */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

</head>

</head>

<body>
    <div class="btn-container no-print">
        <div class="toolbar-title"><i class="fas fa-file-alt mr-2"></i> <?= htmlspecialchars($judul ?? 'Laporan Jurnal KBM') ?></div>
        <div class="toolbar-actions">
            <button onclick="zoomOut()" class="chrome-btn" title="Perkecil"><i class="fas fa-search-minus"></i></button>
            <span class="zoom-info" id="zoom-percent">100%</span>
            <button onclick="zoomIn()" class="chrome-btn" title="Perbesar"><i class="fas fa-search-plus"></i></button>
            
            <div class="divider"></div>
            
            <button onclick="window.print()" class="chrome-btn" title="Cetak Laporan"><i class="fas fa-print"></i></button>
            <button onclick="window.print()" class="chrome-btn" title="Simpan sebagai PDF"><i class="fas fa-download"></i></button>
            
            <div class="divider"></div>
            
            <button onclick="window.parent.$('#modalGlobalPreview').modal('hide')" class="chrome-btn" title="Tutup"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="a4-container" id="print-container">
        <!-- Kop Surat Standard -->
        <?php include __DIR__ . '/partials/kop_surat_laporan.php'; ?>

        <div class="report-header">
            <div class="report-title-main">LAPORAN</div>
            <div class="report-title-main">JURNAL KEGIATAN BELAJAR MENGAJAR (KBM)</div>
            <div class="report-info">
                <span>KELAS : <?= htmlspecialchars($info_kelas) ?></span>
                <span><?= htmlspecialchars($info_periode) ?></span>
                <span>TAHUN AJARAN : <?= htmlspecialchars($info_ta) ?></span>
            </div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="4%">NO</th>
                    <th width="11%">HARI, TANGGAL</th>
                    <th width="9%">JAM / WAKTU</th>
                    <th width="14%">NAMA GURU & MAPEL</th>
                    <th width="23%">CAPAIAN & TUJUAN PEMBELAJARAN</th>
                    <th width="9%">TAGIHAN/TUGAS</th>
                    <th width="18%">REKAP ABSENSI</th>
                    <th width="12%">DOKUMENTASI</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $prev_date = '';
                // Menghitung rowspan untuk tanggal yang sama
                $date_counts = array_count_values(array_column($rows, 'tanggal_raw'));
                $printed_dates = [];
                ?>

                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="8" class="col-center">Tidak ada data jurnal.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="col-center"><?= $no++ ?></td>

                        <?php if (!in_array($row['tanggal_raw'], $printed_dates)): ?>
                            <td rowspan="<?= $date_counts[$row['tanggal_raw']] ?>" class="col-center">
                                <strong><?= htmlspecialchars($row['hari']) ?></strong><br>
                                <?= htmlspecialchars($row['tanggal_indo']) ?>
                            </td>
                            <?php $printed_dates[] = $row['tanggal_raw']; ?>
                        <?php endif; ?>

                        <td class="col-center"><?= htmlspecialchars($row['waktu']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['guru']) ?></strong><br>
                            <span style="font-size: 8pt; color: #555;">Mapel: <?= htmlspecialchars($row['mapel']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['tujuan']) ?></td>
                        <td><?= htmlspecialchars($row['tagihan']) ?></td>
                        <td><?= htmlspecialchars($row['absensi']) ?></td>
                        <td class="col-center" style="vertical-align: middle;">
                            <?php if (!empty($row['foto_kegiatan'])): ?>
                                <img src="<?= BASE_URL ?>uploads/jurnal/<?= htmlspecialchars($row['foto_kegiatan']) ?>" 
                                     alt="Foto KBM" 
                                     style="max-width: 80px; max-height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; display: block; margin: 0 auto;">
                            <?php else: ?>
                                <span style="font-size: 8pt; color: #888;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                Mengetahui,<br>
                Kepala Sekolah
                <div class="signature-space"></div>
                <div class="signature-name"><?= htmlspecialchars($info_kepsek) ?></div>
            </div>
            <div class="signature-box">
                <?= htmlspecialchars($sekolah['kota'] ?? 'Sukabumi') ?>, <?= tgl_indo() ?><br>
                <?= htmlspecialchars($right_sig_label) ?>
                <div class="signature-space"></div>
                <div class="signature-name"><?= htmlspecialchars($right_sig_name) ?></div>
            </div>
        </div>

         <!-- FOOTER TEKS -->
         <div style="margin-top: 30px; font-style: italic; font-size: 8pt; width: 100%; border-top: 1px solid #ccc; padding-top: 5px;">
            Dokumen ini dicetak melalui SIMAKS - Sistem Informasi Manajemen Akademik Sekolah
        </div>
    </div>
    <script>
        let currentRotation = 0;
        let currentZoom = 1;

        function rotatePage() {
            currentRotation = (currentRotation + 90) % 360;
            const container = document.querySelector('.a4-container');
            if (!container) return;
            
            container.classList.remove('rotate-90', 'rotate-180', 'rotate-270');
            if (currentRotation > 0) {
                container.classList.add('rotate-' + currentRotation);
            }
        }

        function updateZoom() {
            const container = document.querySelector('.a4-container');
            if (!container) return;
            container.style.transform = `scale(${currentZoom})`;
            container.style.transformOrigin = 'top center';
            document.getElementById('zoom-percent').innerText = Math.round(currentZoom * 100) + '%';
        }

        function zoomIn() {
            if (currentZoom < 2) {
                currentZoom += 0.1;
                updateZoom();
            }
        }

        function zoomOut() {
            if (currentZoom > 0.5) {
                currentZoom -= 0.1;
                updateZoom();
            }
        }
    </script>
</body>

</html>