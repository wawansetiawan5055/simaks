<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($judul ?? 'Laporan Jadwal') ?></title>
    <style>
        body,
        html {
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            background-color: #525659;
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

        body, html { width: 100%; margin: 0; padding: 0; }

        body {
            padding-top: 48px;
            background-color: #525659;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .a4-container {
            width: 28cm;
            max-width: 95%;
            margin: 20px auto;
            padding: 1cm;
            box-sizing: border-box;
            background-color: white;
            min-height: 21cm;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease;
        }

        /* HEADER INFO */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* SPLIT LAYOUT */
        .split-layout {
            display: flex;
            gap: 15px;
        }

        .split-column {
            flex: 1;
        }

        /* DATA TABLE */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .schedule-table th {
            background-color: #dce6f1;
            font-weight: bold;
            height: 30px;
        }

        /* COLUMN WIDTHS */
        .col-hari {
            width: 15%;
        }

        .col-waktu {
            width: 15%;
        }

        /* Dynamic widths based on context */
        .col-mid {
            width: 30%;
        }

        .col-end {
            width: 40%;
        }

        .text-left {
            text-align: left !important;
            padding-left: 5px;
        }

        /* SIGNATURE */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .sig-box {
            text-align: center;
            width: 250px;
        }

        .sig-space {
            height: 70px;
        }

        /* KOP SURAT RESTORED */
        .kop-surat {
            display: flex;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            flex: 0 0 80px;
            margin-right: 15px;
        }

        .kop-logo img {
            width: 100%;
            height: auto;
        }

        .kop-text {
            flex: 1;
            text-align: center;
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

        /* BUTTONS */
        .btn-container {
            text-align: center;
            margin-bottom: 20px;
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

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            color: white;
            text-decoration: none;
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

        @media print {
            .btn-container {
                display: none;
            }

            .a4-container {
                width: 100%;
                max-width: none;
                padding: 0;
                margin: 0;
                box-shadow: none;
                border: none;
            }

            body {
                background-color: white;
            }

            @page {
                margin: 1cm;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="btn-container no-print">
        <div class="toolbar-title"><i class="fas fa-calendar-alt mr-2"></i> <?= htmlspecialchars($judul ?? 'Laporan Jadwal') ?></div>
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

    <div class="a4-container">
        <!-- HEADER (KOP SURAT) -->
        <!-- HEADER (KOP SURAT STANDARD) -->
        <?php include __DIR__ . '/partials/kop_surat_laporan.php'; ?>

        <div class="report-header">
            <h2 class="report-title">JADWAL PELAJARAN</h2>
        </div>

        <div class="info-bar">
            <?php if ($filter_type == 'per_guru'): ?>
                <div>Nama Guru : <?= htmlspecialchars($info['nama_guru'] ?? '-') ?></div>
            <?php else: ?>
                <div>Kelas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($info['nama_kelas'] ?? '-') ?></div>
            <?php endif; ?>

            <div>Tahun Pelajaran : <?= htmlspecialchars($info['tahun_ajaran']) ?></div>
        </div>

        <div class="split-layout">
            <?php
            $column_groups = [
                ['Senin', 'Selasa', 'Rabu'],
                ['Kamis', 'Jumat', 'Sabtu']
            ];

            foreach ($column_groups as $days):
                ?>
                <div class="split-column">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th class="col-hari">Hari</th>
                                <th class="col-waktu">
                                    Waktu
                                </th>
                                <?php if ($filter_type == 'per_guru'): ?>
                                    <th class="col-mid">Kelas</th>
                                    <th class="col-end">Mata Pelajaran</th>
                                <?php else: ?>
                                    <th class="col-mid">Nama Guru</th>
                                    <th class="col-end">Mata Pelajaran</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($days as $day):
                                $day_key = $day;
                                if (!isset($list_grouped[$day_key]) && $day == 'Jumat')
                                    $day_key = "Jum'at";

                                // DYNAMIC ROWS LOGIC: Skip day if empty
                                if (!isset($list_grouped[$day_key]) || empty($list_grouped[$day_key])) {
                                    continue; // Skip this day entirely
                                }

                                $rows = $list_grouped[$day_key];
                                $row_count = count($rows);
                                $first = true;

                                foreach ($rows as $row):
                                    ?>
                                    <tr>
                                        <?php if ($first): ?>
                                            <td rowspan="<?= $row_count ?>"><?= $day == 'Jumat' ? "Jum'at" : $day ?></td>
                                            <?php $first = false; ?>
                                        <?php endif; ?>

                                        <?php if ($row): ?>
                                            <?php if ($filter_type == 'per_guru'): ?>
                                                <!-- FORMAT PER GURU: Waktu | Kelas | Mapel -->
                                                <td><?= substr($row['jam_mulai'], 0, 5) ?>-<?= substr($row['jam_selesai'], 0, 5) ?></td>
                                                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                                <td class="text-left"><?= htmlspecialchars($row['nama_mapel']) ?></td>
                                            <?php else: ?>
                                                <!-- FORMAT PER KELAS: Waktu | Guru | Mapel -->
                                                <td><?= substr($row['jam_mulai'], 0, 5) ?>-<?= substr($row['jam_selesai'], 0, 5) ?></td>
                                                <td class="text-left"><?= htmlspecialchars($row['nama_guru']) ?></td>
                                                <td class="text-left"><?= htmlspecialchars($row['nama_mapel']) ?></td>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; endforeach; ?>

                            <?php
                            // If NO days in this group had data, render one empty row to maintain layout width?
                            // Or just display empty table? CSS flex will handle collapsing if totally empty.
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- SIGNATURES -->
        <div class="signature-section">
            <div class="sig-box">
                Mengetahui,<br>
                Kepala Sekolah
                <div class="sig-space"></div>
                <b><u><?= htmlspecialchars($kop['nama_kepala_sekolah'] ?? '.......................') ?></u></b>
            </div>

            <div class="sig-box">
                Nagrak, <?= date('d F Y') ?><br>
                Waka Kurikulum
                <div class="sig-space"></div>
                <b><u><?= htmlspecialchars($kop['nama_waka_kurikulum']) ?></u></b>
            </div>
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