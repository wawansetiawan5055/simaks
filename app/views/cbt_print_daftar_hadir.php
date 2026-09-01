<?php
// app/views/cbt_print_daftar_hadir.php
// Cetak Daftar Hadir Peserta Ujian CBT Resmi per Ruang / Sesi
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir Ujian - <?= htmlspecialchars($jadwal['nama_ujian'] ?? 'CBT SIMAKS') ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #f8fafc;
            color: #1e293b;
            padding: 20px;
        }
        .page {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 15mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 18px;
        }
        .kop-text {
            text-align: center;
            flex-grow: 1;
        }
        .kop-text h2 {
            font-size: 1.15rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-top: 2px;
        }
        .kop-text p {
            font-size: 0.75rem;
            color: #475569;
            margin-top: 3px;
        }
        .judul-dokumen {
            text-align: center;
            margin-bottom: 18px;
        }
        .judul-dokumen h4 {
            font-size: 1rem;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .judul-dokumen p {
            font-size: 0.8rem;
            color: #334155;
            margin-top: 3px;
            font-weight: 600;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            font-size: 0.82rem;
            margin-bottom: 16px;
        }
        .info-grid table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-grid table td.lbl {
            width: 130px;
            font-weight: 600;
            color: #334155;
        }
        .info-grid table td.val {
            font-weight: 700;
        }
        .table-peserta {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            margin-bottom: 24px;
        }
        .table-peserta th, .table-peserta td {
            border: 1px solid #334155;
            padding: 6px 8px;
        }
        .table-peserta th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .ttd-box-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
            font-size: 0.82rem;
            page-break-inside: avoid;
        }
        .ttd-card {
            text-align: center;
        }
        .ttd-space {
            height: 60px;
        }
        .ttd-name {
            font-weight: 700;
            text-decoration: underline;
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
            max-width: 210mm;
            margin-left: auto;
            margin-right: auto;
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
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>🖨️ Cetak Daftar Hadir Peserta Ujian CBT</strong> &bull; Total: <?= count($peserta_list) ?> Siswa
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Cetak Daftar Hadir Sekarang</button>
        </div>
    </div>

    <div class="page">
        <!-- KOP SURAT RESMI STANDAR UNIVERSAL -->
        <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

        <div class="judul-dokumen">
            <h4>DAFTAR HADIR PESERTA UJIAN</h4>
            <p>TAHUN AJARAN 2026/2027 &mdash; SEMESTER <?= strtoupper($jadwal['semester'] ?? 'GANJIL') ?></p>
        </div>

        <!-- INFO JADWAL -->
        <div class="info-grid">
            <table>
                <tr>
                    <td class="lbl">Nama Asesmen</td>
                    <td>:</td>
                    <td class="val"><?= htmlspecialchars($jadwal['nama_ujian']) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Mata Pelajaran</td>
                    <td>:</td>
                    <td class="val"><?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">Tingkat / Rombel</td>
                    <td>:</td>
                    <td class="val">Kelas <?= htmlspecialchars($jadwal['nama_kelas'] ?? '-') ?></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="lbl">Hari, Tanggal</td>
                    <td>:</td>
                    <td class="val"><?= date('l, d F Y', strtotime($jadwal['tanggal_mulai'] ?? date('Y-m-d'))) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Waktu / Durasi</td>
                    <td>:</td>
                    <td class="val"><?= date('H:i', strtotime($jadwal['tanggal_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['tanggal_selesai'])) ?> WIB (<?= $jadwal['durasi_menit'] ?? 90 ?> Menit)</td>
                </tr>
                <tr>
                    <td class="lbl">Ruang / Sesi</td>
                    <td>:</td>
                    <td class="val">Ruang 01 &bull; Sesi 1</td>
                </tr>
            </table>
        </div>

        <!-- TABEL PESERTA DENGAN TTD ZIG-ZAG -->
        <table class="table-peserta">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th style="width: 100px;">No. Peserta</th>
                    <th style="width: 90px;">NISN</th>
                    <th>Nama Peserta Didik</th>
                    <th style="width: 60px;">L/P</th>
                    <th style="width: 80px;">Kelas</th>
                    <th colspan="2" style="width: 160px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($peserta_list)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">Belum ada peserta yang terdaftar pada jadwal ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($peserta_list as $idx => $p): ?>
                        <?php 
                            $no = $idx + 1;
                            $no_peserta = !empty($p['no_peserta']) ? $p['no_peserta'] : ('CBT-' . str_pad($p['id_siswa'], 5, '0', STR_PAD_LEFT));
                        ?>
                        <tr>
                            <td style="text-align: center; font-weight: bold;"><?= $no ?></td>
                            <td style="text-align: center; font-family: monospace; font-weight: bold;"><?= htmlspecialchars($no_peserta) ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($p['nisn'] ?: '-') ?></td>
                            <td><strong><?= htmlspecialchars($p['nama_siswa']) ?></strong></td>
                            <td style="text-align: center;"><?= htmlspecialchars($p['jk'] ?? 'L') ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($p['nama_kelas'] ?? $jadwal['nama_kelas'] ?? '-') ?></td>
                            <?php if ($no % 2 !== 0): ?>
                                <td style="width: 80px; vertical-align: top; font-size: 0.7rem; color: #64748b;"><?= $no ?>. ...........</td>
                                <td style="width: 80px; background: #fafafa;"></td>
                            <?php else: ?>
                                <td style="width: 80px; background: #fafafa;"></td>
                                <td style="width: 80px; vertical-align: top; font-size: 0.7rem; color: #64748b;"><?= $no ?>. ...........</td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TANDA TANGAN PENGAWAS & PROKTOR -->
        <div class="ttd-box-grid">
            <div class="ttd-card">
                <p>Mengetahui,</p>
                <p><strong>Proktor Ruang CBT</strong></p>
                <div class="ttd-space"></div>
                <div class="ttd-name">...................................................</div>
                <p style="font-size: 0.75rem; color: #64748b;">NIP. -</p>
            </div>
            <div class="ttd-card">
                <p><?= htmlspecialchars($sekolah['kota'] ?? 'Bandung') ?>, <?= date('d F Y') ?></p>
                <p><strong>Pengawas Ruang Ujian</strong></p>
                <div class="ttd-space"></div>
                <div class="ttd-name">...................................................</div>
                <p style="font-size: 0.75rem; color: #64748b;">NIP. -</p>
            </div>
        </div>
    </div>

</body>
</html>
