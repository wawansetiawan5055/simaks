<?php
// app/views/cbt_print_analisis_butir.php
// Laporan Analisis Kuantitatif Butir Soal Asesmen CBT (Tingkat Kesukaran P, Daya Pembeda D, Distribusi Pengecoh)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Butir Soal CBT - <?= htmlspecialchars($jadwal['nama_ujian'] ?? 'CBT SIMAKS') ?></title>
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
            max-width: 297mm; /* Landscape A4 */
            margin: 0 auto;
            background: #fff;
            padding: 12mm 15mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .kop-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 16px;
        }
        .kop-text {
            text-align: center;
            flex-grow: 1;
        }
        .kop-text h2 {
            font-size: 1.1rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .kop-text h3 {
            font-size: 0.9rem;
            font-weight: 700;
            margin-top: 2px;
        }
        .kop-text p {
            font-size: 0.72rem;
            color: #475569;
            margin-top: 2px;
        }
        .judul-dokumen {
            text-align: center;
            margin-bottom: 14px;
        }
        .judul-dokumen h4 {
            font-size: 0.98rem;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px 16px;
            font-size: 0.78rem;
            margin-bottom: 12px;
            background: #f8fafc;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .table-analisis {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.74rem;
            margin-bottom: 18px;
        }
        .table-analisis th, .table-analisis td {
            border: 1px solid #334155;
            padding: 5px 6px;
        }
        .table-analisis th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: 700;
            font-size: 0.7rem;
            vertical-align: middle;
        }
        .table-analisis td.center {
            text-align: center;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.68rem;
        }
        .badge-mudah { background: #dcfce7; color: #15803d; }
        .badge-sedang { background: #fef9c3; color: #854d0e; }
        .badge-sukar { background: #fee2e2; color: #b91c1c; }
        .badge-baik { background: #e0f2fe; color: #0369a1; }
        .badge-cukup { background: #fef3c7; color: #b45309; }
        .badge-jelek { background: #f3f4f6; color: #4b5563; }
        
        .keterangan-rumus {
            font-size: 0.72rem;
            color: #475569;
            margin-top: 10px;
            line-height: 1.5;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
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
            max-width: 297mm;
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
            <strong>📊 Laporan Analisis Kuantitatif Butir Soal CBT</strong>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Cetak Analisis Butir Soal</button>
        </div>
    </div>

    <div class="page">
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <?php if (!empty($sekolah['logo'])): ?>
                <img src="<?= htmlspecialchars($sekolah['logo']) ?>" class="kop-logo" alt="Logo">
            <?php endif; ?>
            <div class="kop-text">
                <h2><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'SMA PLUS AL-MANSHURIYAH') ?></h2>
                <h3>LAPORAN ANALISIS KUANTITATIF BUTIR SOAL ASESMEN</h3>
                <p><?= htmlspecialchars($sekolah['alamat'] ?? 'Jl. Raya Pendidikan No. 1') ?> &bull; Tahun Ajaran 2026/2027</p>
            </div>
        </div>

        <div class="judul-dokumen">
            <h4>ANALISIS TINGKAT KESUKARAN, DAYA PEMBEDA &amp; DISTRIBUSI PENGECOH</h4>
        </div>

        <!-- INFO JADWAL -->
        <div class="info-grid">
            <div>
                <strong>Agenda Ujian:</strong> <?= htmlspecialchars($jadwal['nama_ujian']) ?><br>
                <strong>Mata Pelajaran:</strong> <?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?>
            </div>
            <div>
                <strong>Kelas / Rombel:</strong> <?= htmlspecialchars($jadwal['nama_kelas'] ?? '-') ?><br>
                <strong>Paket Naskah:</strong> <?= htmlspecialchars($jadwal['nama_paket'] ?? '-') ?>
            </div>
            <div>
                <strong>Total Peserta Ujian:</strong> <?= $total_peserta ?> Siswa<br>
                <strong>Peserta Selesai (Sampel):</strong> <?= $total_sampel ?> Siswa
            </div>
        </div>

        <!-- TABEL ANALISIS BUTIR -->
        <table class="table-analisis">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 30px;">No</th>
                    <th rowspan="2" style="width: 45px;">Bentuk</th>
                    <th rowspan="2" style="width: 50px;">Kunci</th>
                    <th colspan="5">Distribusi Jawaban Siswa (Pengecoh)</th>
                    <th colspan="2">Tingkat Kesukaran (P)</th>
                    <th colspan="2">Daya Pembeda (D)</th>
                    <th rowspan="2" style="width: 85px;">Rekomendasi</th>
                </tr>
                <tr>
                    <th style="width: 30px;">A</th>
                    <th style="width: 30px;">B</th>
                    <th style="width: 30px;">C</th>
                    <th style="width: 30px;">D</th>
                    <th style="width: 30px;">E</th>
                    <th style="width: 50px;">Indeks</th>
                    <th style="width: 65px;">Kategori</th>
                    <th style="width: 50px;">Indeks</th>
                    <th style="width: 70px;">Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($analisis_data)): ?>
                    <tr>
                        <td colspan="13" style="text-align: center; padding: 25px;">Belum ada data pengerjaan siswa yang cukup untuk menghitung analisis butir soal.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($analisis_data as $i => $row): ?>
                        <tr>
                            <td class="center font-weight-bold"><?= $row['nomor_urut'] ?></td>
                            <td class="center uppercase"><?= strtoupper($row['tipe_soal']) ?></td>
                            <td class="center font-weight-bold" style="color: #2563eb;"><?= $row['kunci_jawaban'] ?></td>
                            
                            <!-- Distribusi Pengecoh -->
                            <td class="center <?= ($row['kunci_jawaban']=='A'?'font-weight-bold text-success':'') ?>"><?= $row['dist_a'] ?></td>
                            <td class="center <?= ($row['kunci_jawaban']=='B'?'font-weight-bold text-success':'') ?>"><?= $row['dist_b'] ?></td>
                            <td class="center <?= ($row['kunci_jawaban']=='C'?'font-weight-bold text-success':'') ?>"><?= $row['dist_c'] ?></td>
                            <td class="center <?= ($row['kunci_jawaban']=='D'?'font-weight-bold text-success':'') ?>"><?= $row['dist_d'] ?></td>
                            <td class="center <?= ($row['kunci_jawaban']=='E'?'font-weight-bold text-success':'') ?>"><?= $row['dist_e'] ?></td>
                            
                            <!-- Tingkat Kesukaran P -->
                            <td class="center font-weight-bold"><?= number_format($row['p_index'], 2) ?></td>
                            <td class="center">
                                <span class="badge-status badge-<?= strtolower($row['p_kategori']) ?>">
                                    <?= $row['p_kategori'] ?>
                                </span>
                            </td>

                            <!-- Daya Pembeda D -->
                            <td class="center font-weight-bold"><?= number_format($row['d_index'], 2) ?></td>
                            <td class="center">
                                <span class="badge-status badge-<?= strtolower($row['d_kategori']) ?>">
                                    <?= $row['d_kategori'] ?>
                                </span>
                            </td>

                            <!-- Rekomendasi -->
                            <td class="center font-weight-bold" style="color: <?= ($row['rekomendasi']=='Diterima'?'#16a34a':($row['rekomendasi']=='Direvisi'?'#d97706':'#dc2626')) ?>;">
                                <?= $row['rekomendasi'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- KETERANGAN RUMUS & KRITERIA STANDAR -->
        <div class="keterangan-rumus">
            <strong>Pedoman Kriteria Evaluasi Butir Soal (Standar Kemendikbud):</strong><br>
            &bull; <strong>Tingkat Kesukaran (P):</strong> $P > 0.70$ (Mudah) &bull; $0.30 \le P \le 0.70$ (Sedang / Ideal) &bull; $P < 0.30$ (Sukar/Sulit).<br>
            &bull; <strong>Daya Pembeda (D):</strong> $D \ge 0.40$ (Sangat Baik) &bull; $0.30 \le D < 0.40$ (Baik) &bull; $0.20 \le D < 0.30$ (Cukup / Perlu Revisi) &bull; $D < 0.20$ (Jelek / Dibuang).<br>
            &bull; <strong>Rekomendasi:</strong> Butir soal diterima jika memiliki daya pembeda yang baik dan efektivitas pengecoh yang berfungsi merata.
        </div>
    </div>

</body>
</html>
