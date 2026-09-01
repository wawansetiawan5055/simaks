<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Akademik - <?= htmlspecialchars($ta['nama_ta']) ?></title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* --- KOP SURAT UNIFIED --- */
        .kop-surat {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            width: 100%;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table td {
            padding: 0;
            vertical-align: top;
        }
        .kop-logo {
            width: 70px;
            text-align: left;
        }
        .kop-logo img {
            width: 60px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            line-height: 1.2;
        }
        .kop-text h1 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }
        .kop-text h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .kop-text p {
            font-size: 8pt;
            margin: 0;
        }

        .report-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        
        .calendar-container {
            width: 100%;
            margin-bottom: 5px;
        }
        .month-wrapper {
            display: inline-block;
            width: 32%;
            margin-right: 1%;
            margin-bottom: 5px;
            vertical-align: top;
        }
        .month-wrapper:nth-child(3n) {
            margin-right: 0;
        }
        
        table.month-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
        }
        table.month-table th {
            background-color: #f8f9fa;
            font-size: 6pt;
            padding: 2px 0;
            border: 1px solid #eee;
            text-align: center;
        }
        table.month-table td {
            height: 20px;
            text-align: center;
            border: 1px solid #eee;
            font-size: 6pt;
            position: relative;
        }
        .month-header {
            background-color: #343a40 !important;
            color: #fff !important;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 0 !important;
        }
        .day-num {
            position: relative;
            z-index: 2;
        }
        .event-bg {
            position: absolute;
            top: 1px;
            bottom: 1px;
            left: 1px;
            right: 1px;
            border-radius: 2px;
            opacity: 0.7;
            z-index: 1;
        }
        .sunday {
            color: #dc3545;
            background-color: #fff5f5;
            font-weight: bold;
        }
        .sunday-bg {
            background-color: #ffebee !important;
        }
        
        .legend-section {
            margin-top: 2px;
            padding-top: 2px;
            border-top: 1px dashed #ccc;
        }
        .legend-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 9pt;
        }
        
        .activity-list {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .activity-list th, .activity-list td {
            border: 1px solid #ddd;
            padding: 1px 3px;
            text-align: left;
            font-size: 6pt;
            line-height: 1.1;
            word-wrap: break-word;
        }
        .activity-list th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .activity-no {
            text-align: center !important;
        }
        .color-box {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 5px;
            border-radius: 1px;
            border: 0.5px solid #999;
            vertical-align: middle;
        }
        
        .effective-section {
            page-break-before: always;
            margin-top: 10px;
        }
        .effective-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .effective-table th, .effective-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
            font-size: 9pt;
        }
        .effective-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .effective-table td.text-left {
            text-align: left;
        }
        .calculation-note {
            margin-top: 10px;
            font-size: 8pt;
            font-style: italic;
            color: #555;
        }

        .footer {
            margin-top: 20px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-box {
            text-align: center;
            width: 250px;
            line-height: 1.5;
            float: right;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Universal Centralized Kop Surat -->
    <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

    <div class="report-title">
        KALENDER AKADEMIK <?= htmlspecialchars($ta['nama_ta']) ?>
    </div>

    <div class="calendar-container">
        <?php foreach ($months as $m): ?>
            <div class="month-wrapper">
                <table class="month-table">
                    <thead>
                        <tr>
                            <th colspan="7" class="month-header">
                                <?= DateHelper::getNamaBulan($m['month']) ?> <?= $m['year'] ?>
                            </th>
                        </tr>
                        <tr>
                            <th style="color: #dc3545;">M</th>
                            <th>S</th>
                            <th>S</th>
                            <th>R</th>
                            <th>K</th>
                            <th>J</th>
                            <th>S</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // First day of month
                        $firstDay = new DateTime($m['year'] . '-' . $m['month'] . '-01');
                        $lastDay = new DateTime($m['year'] . '-' . $m['month'] . '-' . $firstDay->format('t'));
                        
                        // day of week (0 for Sun, 6 for Sat)
                        $startDayOfWeek = (int)$firstDay->format('w');
                        $daysInMonth = (int)$firstDay->format('t');
                        
                        $currentDay = 1;
                        for ($row = 0; $row < 6; $row++): 
                            if ($currentDay > $daysInMonth) break;
                        ?>
                            <tr>
                                <?php for ($col = 0; $col < 7; $col++): ?>
                                    <td class="<?= ($col == 0) ? 'sunday' : '' ?>">
                                        <?php 
                                        if (($row == 0 && $col < $startDayOfWeek) || $currentDay > $daysInMonth) {
                                            echo "";
                                        } else {
                                            $dateStr = sprintf('%04d-%02d-%02d', $m['year'], $m['month'], $currentDay);
                                            
                                            // Automatic red background for Sundays
                                            if ($col == 0) {
                                                echo '<div class="event-bg sunday-bg" style="opacity: 1;"></div>';
                                            }
                                            
                                            // Check for events
                                            if (isset($eventsByDate[$dateStr])) {
                                                // Prioritize holiday or first event color
                                                $color = $eventsByDate[$dateStr][0]['warna'];
                                                foreach($eventsByDate[$dateStr] as $ev) {
                                                    if ($ev['kategori'] == 'Libur') {
                                                        $color = $ev['warna'];
                                                        break;
                                                    }
                                                }
                                                echo '<div class="event-bg" style="background-color: '.$color.'"></div>';
                                            }
                                            
                                            echo '<span class="day-num">' . $currentDay . '</span>';
                                            $currentDay++;
                                        }
                                        ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="legend-section">
        <div class="legend-title">KETERANGAN & DETAIL KEGIATAN</div>
        <?php 
        if (empty($events)): 
            echo '<p style="text-align: center; font-size: 7pt;">Tidak ada agenda kegiatan.</p>';
        else:
            $totalEvents = count($events);
            $half = ceil($totalEvents / 2);
            $chunks = array_chunk($events, $half);
        ?>
            <table style="width: 100%; border-collapse: collapse; border: none; table-layout: fixed;">
                <tr>
                    <?php foreach ($chunks as $chunkIndex => $chunkEvents): ?>
                        <td style="width: 50%; vertical-align: top; padding: <?= ($chunkIndex == 0) ? '0 5px 0 0' : '0 0 0 5px' ?>;">
                            <table class="activity-list" style="margin-top: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="20">No</th>
                                        <th width="85">Tgl.</th>
                                        <th>Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach ($chunkEvents as $index => $event): 
                                        $start = DateHelper::formatTanggal($event['tanggal_mulai'], 'short');
                                        $end = DateHelper::formatTanggal($event['tanggal_selesai'], 'short');
                                        $dateRange = ($event['tanggal_mulai'] == $event['tanggal_selesai']) ? $start : $start . " - " . $end;
                                        $globalNo = ($chunkIndex * $half) + $index + 1;
                                    ?>
                                        <tr>
                                            <td class="activity-no"><?= $globalNo ?></td>
                                            <td class="activity-tanggal" style="font-size: 5.5pt;"><?= $dateRange ?></td>
                                            <td>
                                                <span class="color-box" style="background-color: <?= $event['warna'] ?>;"></span>
                                                <strong style="font-size: 6pt;"><?= htmlspecialchars($event['judul_kegiatan']) ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        <?php endif; ?>
    </div>

    <div class="effective-section">
        <div class="legend-title">PERHITUNGAN MINGGU DAN HARI EFEKTIF BELAJAR</div>
        <p style="font-size: 9pt; margin-bottom: 5px;">Sistem Sekolah: <strong><?= $isSixDays ? '6 Hari Kerja' : '5 Hari Kerja' ?></strong></p>
        
        <table class="effective-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Bulan / Tahun</th>
                    <th>Hari Efektif (HBE)</th>
                    <th>Minggu Efektif (ME)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalHbe = 0;
                $totalMe = 0;
                foreach ($hbe_data as $index => $row): 
                    $totalHbe += $row['hbe'];
                    $totalMe += $row['me'];
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td class="text-left"><?= $row['month_name'] ?> <?= $row['year'] ?></td>
                        <td><?= $row['hbe'] ?> hari</td>
                        <td><?= $row['me'] ?> minggu</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="2">JUMLAH TOTAL</td>
                    <td><?= $totalHbe ?> hari</td>
                    <td><?= $totalMe ?> minggu</td>
                </tr>
            </tfoot>
        </table>

        <div class="calculation-note">
            * Keterangan:<br>
            - Hari Efektif Belajar (HBE) adalah hari operasional sekolah dikurangi hari Minggu<?php if(!$isSixDays) echo ', Sabtu,'; ?> Libur Nasional, dan Agenda Non-KBM (SAS, SAT, SAJ, TKA, ANBK).<br>
            - Minggu Efektif (ME) dihitung jika dalam satu minggu terdapat minimal 3 hari efektif belajar.
        </div>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;"></td>
            <td>
                <div class="signature-box">
                    <?= htmlspecialchars($sekolah['kota'] ?? $kop['kota'] ?? 'Sukabumi') ?>, <?= tgl_indo() ?><br>
                    Kepala Sekolah
                    <div class="signature-space"></div>
                    <strong><?= htmlspecialchars($kop['nama_kepsek'] ?? $sekolah['kepala_sekolah'] ?? '-') ?></strong>
                    <?php if (!empty($kop['nip_kepsek'])): ?>
                        <br>NIP. <?= htmlspecialchars($kop['nip_kepsek']) ?>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
