<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Absensi Piket</title>
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
        }
        @media print {
            .no-print { display: none !important; }
            body, .page-sheet { background: white; margin: 0; padding: 0; width: 100%; box-shadow: none; }
            @page { margin: 10mm; }
        }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        .table td, .table th { padding: 3px 2px !important; vertical-align: middle; font-size: 10px; }
        .header-table td { padding: 2px; border: none !important; font-size: 12px; }
    </style>
</head>
<body>
    <div class="btn-container no-print">
        <div class="toolbar-title"><i class="fas fa-clipboard-list mr-2"></i> Laporan Absensi Siswa Piket</div>
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

    <div class="page-sheet" id="print-container">
        <?php include __DIR__ . '/partials/kop_surat_laporan.php'; ?>
        
        <h4 style="text-align: center; font-weight: bold; margin-bottom: 10px; margin-top: 0;">
            LAPORAN ABSENSI SISWA (PIKET) <br>
            <?= !empty($header_info['judul_bulan']) ? strtoupper($header_info['judul_bulan']) : '' ?>
        </h4>

        <table style="width: 100%; margin-bottom: 10px; font-size: 13px;">
            <tr>
                <td style="width: 100%; vertical-align: top;">
                    <table class="table header-table mb-0" style="width: 100%;">
                        <tr>
                            <td width="150" style="padding: 1px 0;">Kelas / Fase</td>
                            <td style="padding: 1px 0;">: <strong><?= htmlspecialchars($header_info['kelas'] ?? '-') ?></strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">Tahun Pelajaran</td>
                            <td style="padding: 1px 0;">: <strong><?= htmlspecialchars($header_info['ta']) ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- A. HARIAN -->
        <?php if ($jenis_laporan == 'harian'): ?>
            <?php if (!empty($data)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-light">
                        <th class="text-center">No</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">NIPD</th>
                        <th class="text-center">NISN</th>
                        <th class="text-center">Nama Siswa</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data as $d): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><?= date('d-m-Y', strtotime($d['tanggal'])) ?></td>
                        <td class="text-center"><?= htmlspecialchars($d['nipd']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($d['nisn']) ?></td>
                        <td class="text-left" style="text-align: left !important;"><?= htmlspecialchars($d['nama']) ?></td>
                        <td class="text-center"><?= $d['status'] ?></td>
                        <td><?= htmlspecialchars($d['keterangan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="text-center p-3"><em>Tidak ada data untuk ditampilkan.</em></div>
            <?php endif; ?>

        <!-- B. BULANAN -->
        <?php elseif ($jenis_laporan == 'bulanan'): ?>
            <?php if (!empty($data['students']) && !empty($data['dates'])): ?>
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="bg-light">
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">NIPD</th>
                        <th rowspan="2" class="align-middle">NISN</th>
                        <th rowspan="2" class="align-middle text-left" style="min-width: 150px; text-align: left !important;">Nama Siswa</th>
                        <th colspan="<?= count($data['dates']) ?>">Tanggal</th>
                        <th colspan="4">Rekap</th>
                        <th rowspan="2" class="align-middle">%</th>
                    </tr>
                    <tr class="bg-light">
                        <?php foreach ($data['dates'] as $dt): ?>
                            <th style="font-size: 10px; width: 20px;"><?= date('d', strtotime($dt)) ?></th>
                        <?php endforeach; ?>
                        <th width="30">H</th>
                        <th width="30">S</th>
                        <th width="30">I</th>
                        <th width="30">A</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data['students'] as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $s['nipd'] ?></td>
                            <td><?= $s['nisn'] ?></td>
                            <td class="text-left" style="text-align: left !important;"><?= $s['nama'] ?></td>
                            <?php foreach ($data['dates'] as $dt): 
                                $st = $s['attendance'][$dt] ?? '-';
                                $val = ($st != '-') ? strtoupper(substr($st, 0, 1)) : '';
                            ?>
                                <td><?= $val ?></td>
                            <?php endforeach; ?>
                            <td><?= $s['summary']['H'] ?></td>
                            <td><?= $s['summary']['S'] ?></td>
                            <td><?= $s['summary']['I'] ?></td>
                            <td><?= $s['summary']['A'] ?></td>
                            <?php 
                                $total = $s['summary']['H'] + $s['summary']['S'] + $s['summary']['I'] + $s['summary']['A'];
                                $persen = ($total > 0) ? round(($s['summary']['H'] / $total) * 100, 1) : 0;
                            ?>
                            <td class="font-weight-bold"><?= $persen ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="text-center p-3"><em>Tidak ada data untuk ditampilkan.</em></div>
            <?php endif; ?>

        <!-- C. SEMESTER -->
        <?php elseif ($jenis_laporan == 'semester'): ?>
            <?php if (!empty($data['students']) && !empty($data['months'])): ?>
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="bg-light">
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">NIPD</th>
                        <th rowspan="2" class="align-middle">NISN</th>
                        <th rowspan="2" class="align-middle text-left" style="min-width: 200px; text-align: left !important;">Nama Siswa</th>
                        <?php foreach ($data['months'] as $m): ?>
                            <th colspan="4"><?= date('F', strtotime($m . "-01")) ?></th>
                        <?php endforeach; ?>
                        <th colspan="4" class="bg-light">Total</th>
                        <th rowspan="2" class="align-middle">%</th>
                    </tr>
                    <tr class="bg-light">
                        <?php foreach ($data['months'] as $m): ?>
                            <th>H</th><th>S</th><th>I</th><th>A</th>
                        <?php endforeach; ?>
                        <th>H</th><th>S</th><th>I</th><th>A</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data['students'] as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $s['nipd'] ?></td>
                            <td><?= $s['nisn'] ?></td>
                            <td class="text-left" style="text-align: left !important;"><?= $s['nama'] ?></td>
                            <?php foreach ($data['months'] as $m):
                                $md = $s['months'][$m] ?? ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
                            ?>
                                <td><?= $md['H'] ?: '-' ?></td>
                                <td><?= $md['S'] ?: '-' ?></td>
                                <td><?= $md['I'] ?: '-' ?></td>
                                <td><?= $md['A'] ?: '-' ?></td>
                            <?php endforeach; ?>
                            <td class="bg-light font-weight-bold"><?= $s['total']['H'] ?></td>
                            <td class="bg-light font-weight-bold"><?= $s['total']['S'] ?></td>
                            <td class="bg-light font-weight-bold"><?= $s['total']['I'] ?></td>
                            <td class="bg-light font-weight-bold"><?= $s['total']['A'] ?></td>
                            <?php 
                                $total = $s['total']['H'] + $s['total']['S'] + $s['total']['I'] + $s['total']['A'];
                                $persen = ($total > 0) ? round(($s['total']['H'] / $total) * 100, 1) : 0;
                            ?>
                            <td class="bg-light font-weight-bold"><?= $persen ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
             <?php else: ?>
                <div class="text-center p-3"><em>Tidak ada data untuk ditampilkan.</em></div>
            <?php endif; ?>
        <?php endif; ?>

        <div style="margin-top: 50px; text-align: center; float: left; width: 30%;">
            <p>&nbsp;</p>
            <p>Mengetahui,</p>
            <p style="margin-bottom: 80px;">Kepala Sekolah</p>
            <p class="font-weight-bold"><u><?= htmlspecialchars($kop['nama_kepala_sekolah'] ?? '.........................') ?></u></p>
        </div>

        <div style="margin-top: 50px; text-align: center; float: right; width: 30%;">
            <p>Sukabumi, <?= date('d F Y') ?></p>
            <p>Mengetahui,</p>
            <p style="margin-bottom: 80px;"><?= $header_info['signatory_label'] ?? 'Waka Kurikulum' ?></p>
            <p class="font-weight-bold"><u><?= htmlspecialchars($header_info['signatory_name'] ?? $kop['nama_waka_kurikulum'] ?? '.........................') ?></u></p>
        </div>
        <div style="clear: both;"></div>
        <div style="margin-top: 30px; font-style: italic; font-size: 8pt; width: 100%; border-top: 1px solid #ccc; padding-top: 5px;">
            Dokumen ini dicetak melalui SIMAKS - Sistem Informasi Manajemen Akademik Sekolah
        </div>
    </div>
    <script>
        let currentRotation = 0;
        let currentZoom = 1;

        function rotatePage() {
            // Rotation is now removed to focus on sticky toolbar stability
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
