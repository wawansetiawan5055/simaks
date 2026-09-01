<?php
// --- PREPARATION LOGIC ---

$kelas_by_tingkat = [];
foreach ($kelas_list as $k) {
    $t = $k['tingkat'] ?? 'Lainnya';
    $kelas_by_tingkat[$t][] = $k;
}

$day_groups = [
    ['Senin', 'Selasa', 'Rabu'],
    ['Kamis', 'Jumat', 'Sabtu']
];

// Helper: Check for KBM override in a specific slot
function has_kbm_override($jadwal_grid, $day, $jam_id)
{
    if (!$jam_id || !isset($jadwal_grid[$day][$jam_id]))
        return false;
    foreach ($jadwal_grid[$day][$jam_id] as $cls_data) {
        if (!empty($cls_data['kode_guru']) || !empty($cls_data['kode_mapel']))
            return true;
    }
    return false;
}

// Helper: Get Cell Data for Logic
function get_cell_logic_data($row, $day, $jadwal_grid)
{
    $slot = $row['slots_per_day'][$day] ?? null;
    if (!$slot)
        return ['type' => 'EMPTY', 'rowspan' => 1];
    if (($slot['status'] ?? 'EMPTY') == 'SKIP')
        return ['type' => 'SKIP'];
    if (($slot['status'] ?? 'EMPTY') == 'EMPTY')
        return ['type' => 'EMPTY', 'rowspan' => 1];

    $primary = $slot['jam_data'] ?? null;
    $kbm_jam = $slot['kbm_jam_data'] ?? null;
    $rowspan = $slot['rowspan'] ?? 1;

    // Check KBM Override
    $is_kbm = false;
    $target_jam_id = null;

    // 1. Check if we have an explicit KBM alternative that has actual data in the grid
    if ($kbm_jam && has_kbm_override($jadwal_grid, $day, $kbm_jam['id_jam'])) {
        $is_kbm = true;
        $target_jam_id = $kbm_jam['id_jam'];
    }
    // 2. Check if the primary slot itself is KBM (Standard Lesson)
    elseif ($primary && ($primary['jenis_kegiatan'] == 'KBM')) {
        $is_kbm = true;
        $target_jam_id = $primary['id_jam'];
    }

    if ($is_kbm) {
        return ['type' => 'KBM', 'jam_id' => $target_jam_id, 'rowspan' => $rowspan];
    } else {
        // Not KBM (or KBM overridden by nothingness?), so check if it's a Special Event
        if ($primary && $primary['jenis_kegiatan'] != 'KBM') {
            return [
                'type' => 'SPECIAL',
                'name' => $primary['nama_kegiatan_custom'] ?? $primary['nama_kegiatan'],
                'jam_mulai' => $primary['jam_mulai'],
                'rowspan' => $rowspan
            ];
        } else {
            return ['type' => 'EMPTY', 'rowspan' => 1];
        }
    }
}

?>
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
            font-family: 'Arial Narrow', Arial, sans-serif;
            font-size: 8pt;
            background-color: #525659;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            -webkit-print-color-adjust: exact;
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
            padding-top: 48px;
            background-color: #525659;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .sheet {
            width: 28cm;
            max-width: 95%;
            min-height: 21cm;
            margin: 20px auto;
            background-color: white;
            padding: 1cm;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        :root {
            /* Theme color kept for table headers, but removed from page header */
            --theme-color: #00994d;
            --theme-text: white;
            --border-color: #000;
        }

        /* KOP SURAT SYNCED WITH GENERIC STYLE */
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

        /* HEADER BOX REVISION */
        .header-box {
            border: 2px solid black;
            padding: 5px;
            margin-bottom: 20px;
            font-family: 'Times New Roman', serif;
        }

        .header-box-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .header-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.4;
        }

        .header-col {
            /* width handled inline */
        }

        .header-label {
            display: inline-block;
            width: 160px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid var(--border-color);
            padding: 1px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
            line-height: 1.1;
        }

        .schedule-table th {
            background-color: var(--theme-color);
            color: var(--theme-text);
            font-weight: bold;
            text-transform: uppercase;
            height: 20px;
        }

        .col-jam {
            width: 60px;
        }

        .cell-special {
            background-color: white;
            color: red;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .cell-guru {
            font-weight: bold;
            color: black;
            font-size: 7pt;
            margin-right: 2px;
        }

        .cell-mapel {
            font-size: 7pt;
            color: #333;
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

        .btn-print {
            background-color: #28a745;
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .btn-close {
            background-color: #dc3545;
        }

        .btn-close:hover {
            background-color: #c82333;
        }

        .btn-container {
            /* Handled by sticky logic */
        }
        
        .btn-close-standard {
            background-color: #6c757d;
        }
        .btn-close-standard:hover {
            background-color: #5a6268;
        }

        @media print {
            .btn-container {
                display: none;
            }

            body {
                background-color: white;
            }

            .sheet {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }

            @page {
                margin: 5mm;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

</head>

</head>

<body>

    <div class="btn-container no-print">
        <div class="toolbar-title"><i class="fas fa-th mr-2"></i> <?= htmlspecialchars($judul ?? 'Jadwal Pelajaran') ?></div>
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

    <div class="sheet" id="print-container">
        <!-- HEADER (STANDARD KOP) -->
        <!-- HEADER (STANDARD KOP) -->
        <?php include __DIR__ . '/partials/kop_surat_laporan.php'; ?>

        <!-- NEW BOXED HEADER FOR GRID REPORT -->
        <div class="header-box">
            <div class="header-box-title">JADWAL PELAJARAN</div>
            <div class="header-info-row">
                <div class="header-col" style="width: 65%;">
                    <div><span class="header-label">Nama Satuan Pendidikan</span> :
                        <?= htmlspecialchars($kop['kop_nama'] ?? 'SMA PLUS AL MANSHURIYAH') ?>
                    </div>
                    <div><span class="header-label">Alamat</span> :
                        <?= htmlspecialchars($kop['kop_alamat'] ?? 'Alamat Sekolah') ?>
                    </div>
                    <div>Sukabumi</div>
                </div>
                <div class="header-col" style="width: 34%;">
                    <div><span class="header-label">Tahun Pelajaran (Semester)</span> :
                        <?= htmlspecialchars($info['tahun_ajaran']) ?>
                    </div>
                    <div><span class="header-label">Kurikulum</span> : Kurikulum Merdeka</div>
                </div>
            </div>
        </div>

        <?php
        foreach ($day_groups as $group_index => $current_days):
            ?>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th rowspan="3" class="col-jam">JAM</th>
                        <?php foreach ($current_days as $day): ?>
                            <th colspan="<?= count($kelas_list) ?>"><?= strtoupper($day) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($current_days as $day): ?>
                            <?php foreach ($kelas_by_tingkat as $tingkat => $kelases): ?>
                                <th colspan="<?= count($kelases) ?>"><?= $tingkat ?></th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($current_days as $day): ?>
                            <?php foreach ($kelas_by_tingkat as $tingkat => $kelases): ?>
                                <?php foreach ($kelases as $k): ?>
                                    <th><?= str_replace($tingkat . '.', '', $k['nama_kelas']) ?></th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($final_schedule_rows as $row):
                        ?>
                        <tr>
                            <td style="font-weight: bold; background: #e0e0e0;">
                                <?= $row['start'] ?> - <?= $row['end'] ?>
                            </td>

                            <?php
                            // PRE-CALCULATE LOGIC FOR EACH DAY IN THIS ROW
                            $logic_map = [];
                            foreach ($current_days as $day) {
                                $logic_map[$day] = get_cell_logic_data($row, $day, $jadwal_grid);
                            }

                            // RENDER LOOP WITH SEQUENTIAL MERGE
                            $i = 0;
                            while ($i < count($current_days)) {
                                $day = $current_days[$i];
                                $data = $logic_map[$day];

                                if ($data['type'] == 'SKIP') {
                                    // Just SKIP for this day col block.
                                    $i++;
                                    continue;
                                }

                                if ($data['type'] == 'SPECIAL') {
                                    // TRY MERGE RIGHT
                                    $merge_count = 1;
                                    for ($j = $i + 1; $j < count($current_days); $j++) {
                                        $next_day = $current_days[$j];
                                        $next_data = $logic_map[$next_day];

                                        // Check Identity match
                                        if (
                                            $next_data['type'] == 'SPECIAL' &&
                                            $next_data['name'] === $data['name'] &&
                                            $next_data['rowspan'] === $data['rowspan']
                                        ) {
                                            $merge_count++;
                                        } else {
                                            break;
                                        }
                                    }

                                    // RENDER MERGED CELL
                                    $total_colspan = $merge_count * count($kelas_list);
                                    ?>
                                    <td colspan="<?= $total_colspan ?>" rowspan="<?= $data['rowspan'] ?>" class="cell-special">
                                        <?= $data['name'] ?>
                                    </td>
                                    <?php
                                    $i += $merge_count;

                                } elseif ($data['type'] == 'KBM') {
                                    // RENDER KBM (Per Class)
                                    foreach ($kelas_by_tingkat as $tingkat => $kelases) {
                                        foreach ($kelases as $kelas) {
                                            $cell = $jadwal_grid[$day][$data['jam_id']][$kelas['id_kelas']] ?? null;
                                            ?>
                                            <td rowspan="<?= $data['rowspan'] ?>">
                                                <?php if ($cell): ?>
                                                    <span class="cell-guru"><?= $cell['kode_guru'] ?></span><span
                                                        class="cell-mapel"><?= $cell['kode_mapel'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <?php
                                        }
                                    }
                                    $i++;

                                } else {
                                    // EMPTY - Render individual cells for grid lines
                                    for ($k = 0; $k < count($kelas_list); $k++)
                                        echo "<td></td>";
                                    $i++;
                                }
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($group_index == 0): ?>
                <div style="height: 10px;"></div>
            <?php endif; ?>

        <?php endforeach; ?>

        <!-- FOOTER: LEGEND & SIGNATURE -->
        <table style="width: 100%; margin-top: 10px; page-break-inside: avoid;">
            <tr>
                <td width="35%" valign="top">
                    <div style="border: 1px solid black; padding: 2px;">
                        <div
                            style="background: var(--theme-color); color: white; text-align: center; font-weight: bold; font-size: 8pt;">
                            KODE GURU</div>
                        <div
                            style="font-size: 7pt; display: grid; grid-template-columns: 1fr 1fr; gap: 2px; padding: 2px;">
                            <?php foreach ($guru_legend as $g): ?>
                                <div><b><?= $g['kode_guru'] ?></b> : <?= substr($g['nama'], 0, 12) ?>..</div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </td>
                <td width="35%" valign="top">
                    <div style="border: 1px solid black; padding: 2px; margin-left: 5px;">
                        <div
                            style="background: var(--theme-color); color: white; text-align: center; font-weight: bold; font-size: 8pt;">
                            KODE MAPEL</div>
                        <div
                            style="font-size: 7pt; display: grid; grid-template-columns: 1fr 1fr; gap: 2px; padding: 2px;">
                            <?php foreach ($mapel_legend as $m): ?>
                                <div><b><?= $m['kode_mapel'] ?></b> : <?= $m['nama_mapel'] ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </td>
                <td width="30%" valign="top" align="center" style="font-size: 9pt;">
                    <?= htmlspecialchars($sekolah['kota'] ?? 'Sukabumi') ?>, <?= tgl_indo() ?><br>
                    Waka Kurikulum
                    <br><br><br>
                    <b><u><?= htmlspecialchars($kop['nama_waka_kurikulum']) ?></u></b>
                    <br><br>
                    Mengetahui,<br>
                    Kepala Sekolah
                    <br><br><br>
                    <b><u><?= htmlspecialchars($kop['nama_kepala_sekolah'] ?? '.......................') ?></u></b>
                </td>
            </tr>
        </table>
        
        <!-- FOOTER TEKS -->
         <div style="margin-top: 20px; font-style: italic; font-size: 8pt; width: 100%; border-top: 1px solid #ccc; padding-top: 5px;">
            Dokumen ini dicetak melalui SIMAKS - Sistem Informasi Manajemen Akademik Sekolah
        </div>
    </div>
    <script>
        let currentRotation = 0;
        let currentZoom = 1;

        function rotatePage() {
            currentRotation = (currentRotation + 90) % 360;
            const container = document.querySelector('.sheet');
            if (!container) return;
            
            container.classList.remove('rotate-90', 'rotate-180', 'rotate-270');
            if (currentRotation > 0) {
                container.classList.add('rotate-' + currentRotation);
            }
        }

        function updateZoom() {
            const container = document.querySelector('.sheet');
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