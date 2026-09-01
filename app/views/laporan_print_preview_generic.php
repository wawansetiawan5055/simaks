<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($judul ?? 'Laporan') ?></title>

    <style>
        /* CSS Reset Sederhana */
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 10pt;
            /* Ukuran font standar untuk dokumen */
        }
        /* Kontainer utama seukuran A4 Landscape */
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
            padding-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .a4-container {
            width: 28cm;
            max-width: 95%;
            min-height: 19cm;
            margin: 20px auto;
            background: white;
            padding: 1cm;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        /* --- KOP SURAT (Menggunakan data $kop Anda) --- */
        .kop-surat {
            display: flex;
            align-items: flex-start;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            flex: 0 0 100px;
            /* Lebar tetap untuk logo */
            margin-right: 20px;
        }

        .kop-logo img {
            width: 100%;
            height: auto;
        }

        .kop-text {
            flex: 1;
            /* Ambil sisa ruang */
            text-align: center;
            line-height: 1.4;
        }

        .kop-text h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
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

        /* Judul Laporan */
        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* --- TABEL DATA UTAMA --- */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            word-wrap: break-word;
            /* Agar teks panjang pindah baris */
        }

        .main-table th {
            background-color: #f2f2f2;
            /* Latar header tabel */
            font-weight: bold;
            text-align: center;
        }

        /* --- TANDA TANGAN --- */
        .signature-section {
            display: flex;
            justify-content: flex-end;
            /* Pindahkan ke kanan */
            margin-top: 40px;
        }

        .signature-box {
            width: 250px;
            /* Lebar area tanda tangan */
            text-align: center;
            line-height: 1.5;
        }

        .signature-box .signature-placeholder {
            height: 60px;
            /* Ruang untuk TTD */
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .signature-box .nama-kepsek {
            font-weight: bold;
            text-decoration: underline;
        }

        /* --- FOOTER HALAMAN --- */
        .page-footer {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #555;
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
            padding: 0; /* Remove padding since height/width are fixed */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .btn i {
            margin-right: 8px; /* Space between icon and text */
        }

        .btn:hover {
            transform: translateY(-2px); /* Lift effect */
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .btn-print {
            background-color: #28a745;
        }
        .btn-print:hover {
            background-color: #218838;
        }

        .btn-close-standard {
            background-color: #6c757d; /* Grey is more neutral for 'Close' than Red */
            color: white;
        }
        .btn-close-standard:hover {
            background-color: #5a6268;
            color: white;
        }

        .btn-container {
            /* Handled in sticky logic above */
        }

        /* Removed fixed positioning to put it at top of modal content */

        @media print {
            .btn-container {
                display: none;
            }

            .a4-container {
                margin: 0;
                border: none;
                padding: 0;
                width: 100%;
                box-shadow: none;
            }

            body {
                background-color: white;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <div class="btn-container no-print">
        <div class="toolbar-title"><i class="fas fa-file-alt mr-2"></i> <?= htmlspecialchars($judul ?? 'Laporan') ?></div>
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
        <!-- Kop Surat Standard -->
        <?php include __DIR__ . '/partials/kop_surat_laporan.php'; ?>

        <main>
            <h2 class="report-title"><?= htmlspecialchars($judul ?? 'Laporan') ?></h2>

            <table class="main-table">
                <thead>
                    <tr>
                        <?php foreach ($kolom as $k): ?>
                            <th><?= htmlspecialchars($k) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= count($kolom) ?>" style="text-align: center; color: #777;">Tidak ada data untuk
                                ditampilkan.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($row as $cell): ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="signature-section">
                <div class="signature-box">
                    <?= htmlspecialchars($kop['kota'] ?? $sekolah['kota'] ?? 'Sukabumi') ?>, <?= tgl_indo() ?><br>
                    Kepala Sekolah
                    <div class="signature-placeholder"></div>
                    <div class="nama-kepsek">
                        <b><u><?= htmlspecialchars($kop['nama_kepala_sekolah'] ?? $kop['nama_kepsek'] ?? '.......................................') ?></u></b>
                        <?php if (!empty($kop['nip_kepala_sekolah'])): ?>
                            <div style="font-size: 8.5pt; font-weight: normal;">NIP. <?= htmlspecialchars($kop['nip_kepala_sekolah']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <footer class="page-footer">
            Dokumen ini dicetak melalui SIMAKS - Sistem Informasi Manajemen Akademik Sekolah
        </footer>

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