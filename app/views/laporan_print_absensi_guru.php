<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Absensi Guru</title>
    <link rel="stylesheet" href="assets/AdminLTE/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media screen {
            body,
            html { 
                width: 100%;
                background-color: #525659; 
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
                padding-top: 48px;
                background-color: #525659;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
            }

            .page-sheet { 
                background: white; 
                width: 280mm; 
                max-width: 95%;
                min-height: 210mm; 
                padding: 15mm; 
                margin: 20px auto; 
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.4); 
                box-sizing: border-box;
                transition: transform 0.3s ease;
            }
        }
        @media print {
            .no-print { display: none !important; }
            body, .page-sheet { background: white; margin: 0; padding: 0; width: 100%; box-shadow: none; }
            @page { margin: 10mm; }
        }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        .table td, .table th { padding: 0.25rem; vertical-align: middle; }
        .header-table td { padding: 2px; border: none !important; }
        .text-left { text-align: left !important; }



        /* Gold Standard Button Styling - Proportionalized */
        .btn {
            height: 38px !important;
            width: 125px !important;
            padding: 0 15px !important;
            border: none !important;
            border-radius: 6px !important;
            cursor: pointer;
            font-size: 13px !important;
            font-weight: 600 !important;
            margin: 0 10px !important;
            color: white !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease !important;
        }

        .btn i {
            margin-right: 8px !important;
        }

        .btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
            color: white !important;
            text-decoration: none !important;
        }

        .btn-print {
            background-color: #28a745 !important;
        }
        .btn-print:hover {
            background-color: #218838 !important;
        }

        .btn-close-standard {
            background-color: #6c757d !important;
            color: white !important;
        }
        .btn-close-standard:hover {
            background-color: #5a6268 !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="btn-container no-print">
        <div class="toolbar-title"><i class="fas fa-calendar-check mr-2"></i> Laporan Absensi Guru</div>
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

    <div class="page-sheet">

    <?php include __DIR__ . '/partials/kop_surat_laporan.php'; ?>

    <h4 class="text-center mt-3 font-weight-bold">REKAP KEHADIRAN GURU</h4>

    <table style="width: 100%; margin-bottom: 20px;" class="header-table">
        <tr>
            <td width="150" style="text-align: left;">Periode</td>
            <td width="10">:</td>
            <td style="text-align: left;">
                <?php if ($jenis_laporan == 'harian'): ?>
                    <?= date('d F Y', strtotime($tanggal ?? date('Y-m-d'))) ?>
                <?php elseif ($jenis_laporan == 'bulanan'): ?>
                    <?= $header_info['judul_bulan'] ?? '' ?>
                <?php elseif ($jenis_laporan == 'semester'): ?>
                    <?php 
                        $bulan1_nama = date('F', mktime(0, 0, 0, intval($bulan1 ?? 1), 1));
                        $bulan2_nama = date('F', mktime(0, 0, 0, intval($bulan2 ?? 12), 1));
                        echo "$bulan1_nama - $bulan2_nama " . ($tahun_semester ?? date('Y'));
                    ?>
                <?php endif; ?>
            </td>
            <td width="150" style="text-align: left;">Tahun Pelajaran</td>
            <td width="10">:</td>
            <td style="text-align: left;"><?= $header_info['ta'] ?? '' ?></td>
        </tr>
    </table>

    <!-- A. HARIAN -->
    <?php if ($jenis_laporan == 'harian'): ?>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="50" style="text-align: center;">No</th>
                <th style="text-align: center;">Nama Guru</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Keterangan</th>
                <th style="text-align: center;">Tugas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($list)): ?>
                <?php $no = 1; foreach ($list as $d): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-left" style="text-align: left !important;"><?= htmlspecialchars($d['nama']) ?></td>
                    <td class="text-center"><?= $d['status'] ?></td>
                    <td><?= htmlspecialchars($d['keterangan']) ?></td>
                    <td><?= htmlspecialchars($d['tugas']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- B. BULANAN -->
    <?php elseif ($jenis_laporan == 'bulanan'): ?>
    <?php if (!empty($data['teachers'])): ?>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle" style="text-align: center;">No</th>
                <th rowspan="2" class="align-middle" style="min-width: 200px; text-align: center;">Nama Guru</th>
                <th colspan="<?= count($data['dates']) ?>" style="text-align: center;">Tanggal</th>
                <th colspan="4" style="text-align: center;">Rekap</th>
                <th rowspan="2" class="align-middle" style="text-align: center;">%</th>
            </tr>
            <tr>
                <?php foreach ($data['dates'] as $dt): ?>
                    <th style="font-size: 10px; text-align: center;"><?= date('d', strtotime($dt)) ?></th>
                <?php endforeach; ?>
                <th style="text-align: center;">H</th><th style="text-align: center;">S</th><th style="text-align: center;">I</th><th style="text-align: center;">A</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data['teachers'] as $s): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-left" style="text-align: left !important;"><?= $s['nama'] ?></td>
                <?php foreach ($data['dates'] as $dt): 
                    $st = $s['attendance'][$dt] ?? '-';
                    $val = ($st != '-') ? strtoupper(substr($st, 0, 1)) : '';
                ?>
                    <td class="text-center"><?= $val ?></td>
                <?php endforeach; ?>
                <td class="text-center"><?= $s['summary']['H'] ?></td>
                <td class="text-center"><?= $s['summary']['S'] ?></td>
                <td class="text-center"><?= $s['summary']['I'] ?></td>
                <td class="text-center"><?= $s['summary']['A'] ?></td>
                <?php 
                    $total = $s['summary']['H'] + $s['summary']['S'] + $s['summary']['I'] + $s['summary']['A'];
                    $persen = ($total > 0) ? round(($s['summary']['H'] / $total) * 100, 1) : 0;
                ?>
                <td class="text-center"><b><?= $persen ?>%</b></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">Tidak ada data untuk ditampilkan.</div>
    <?php endif; ?>

    <!-- C. SEMESTER -->
    <?php elseif ($jenis_laporan == 'semester'): ?>
    <?php if (!empty($data['teachers'])): ?>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle" style="text-align: center;">No</th>
                <th rowspan="2" class="align-middle" style="min-width: 200px; text-align: center;">Nama Guru</th>
                <?php foreach ($data['months'] as $m): ?>
                    <th colspan="4" style="text-align: center;"><?= date('F', strtotime($m . "-01")) ?></th>
                <?php endforeach; ?>
                <th colspan="4" style="text-align: center;">Total</th>
                <th rowspan="2" class="align-middle" style="text-align: center;">%</th>
            </tr>
            <tr>
                <?php foreach ($data['months'] as $m): ?>
                    <th style="text-align: center;">H</th><th style="text-align: center;">S</th><th style="text-align: center;">I</th><th style="text-align: center;">A</th>
                <?php endforeach; ?>
                <th style="text-align: center;">H</th><th style="text-align: center;">S</th><th style="text-align: center;">I</th><th style="text-align: center;">A</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data['teachers'] as $s): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-left" style="text-align: left !important;"><?= $s['nama'] ?></td>
                <?php foreach ($data['months'] as $m):
                    $md = $s['months'][$m] ?? ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                ?>
                    <td class="text-center"><?= $md['H'] ?: '-' ?></td>
                    <td class="text-center"><?= $md['S'] ?: '-' ?></td>
                    <td class="text-center"><?= $md['I'] ?: '-' ?></td>
                    <td class="text-center"><?= $md['A'] ?: '-' ?></td>
                <?php endforeach; ?>
                <td class="text-center" style="background-color:#eee;"><?= $s['total']['H'] ?></td>
                <td class="text-center" style="background-color:#eee;"><?= $s['total']['S'] ?></td>
                <td class="text-center" style="background-color:#eee;"><?= $s['total']['I'] ?></td>
                <td class="text-center" style="background-color:#eee;"><?= $s['total']['A'] ?></td>
                <?php 
                    $total = $s['total']['H'] + $s['total']['S'] + $s['total']['I'] + $s['total']['A'];
                    $persen = ($total > 0) ? round(($s['total']['H'] / $total) * 100, 1) : 0;
                ?>
                <td class="text-center"><b><?= $persen ?>%</b></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <?php endif; ?>

    <div style="margin-top: 50px; text-align: center; float: right; width: 30%;">
        <p>Sukabumi, <?= date('d F Y') ?></p>
        <p>Kepala Sekolah,</p>
        <br><br><br><br>
        <p><b><?= $kop['nama_kepala_sekolah'] ?? '.......................' ?></b></p>
        <p>NIP. <?= $kop['nip_kepala_sekolah'] ?? '-' ?></p>
    </div>

    </div>
    </div>


<script>
    // Auto print if desired
    // window.onload = function() { window.print(); }
</script>
    <script>
        let currentRotation = 0;
        let currentZoom = 1;

        function rotatePage() {
            currentRotation = (currentRotation + 90) % 360;
            const container = document.querySelector('.page-sheet');
            if (!container) return;
            
            container.classList.remove('rotate-90', 'rotate-180', 'rotate-270');
            if (currentRotation > 0) {
                container.classList.add('rotate-' + currentRotation);
            }
        }

        function updateZoom() {
            const container = document.querySelector('.page-sheet');
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
