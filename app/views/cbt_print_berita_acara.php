<?php
// app/views/cbt_print_berita_acara.php
// Cetak Berita Acara Pelaksanaan Ujian CBT Resmi
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Ujian - <?= htmlspecialchars($jadwal['nama_ujian'] ?? 'CBT SIMAKS') ?></title>
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
            padding: 15mm 20mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            line-height: 1.6;
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
            margin-bottom: 20px;
        }
        .judul-dokumen h4 {
            font-size: 1.05rem;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .judul-dokumen p {
            font-size: 0.82rem;
            color: #334155;
            margin-top: 2px;
            font-weight: 600;
        }
        .narasi-pembuka {
            font-size: 0.86rem;
            text-align: justify;
            margin-bottom: 14px;
        }
        .data-table {
            width: 100%;
            font-size: 0.86rem;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .data-table td.lbl {
            width: 220px;
            font-weight: 600;
        }
        .data-table td.val {
            font-weight: 700;
        }
        .catatan-box {
            border: 1px solid #334155;
            padding: 10px 14px;
            min-height: 80px;
            font-size: 0.82rem;
            border-radius: 4px;
            margin-top: 6px;
            margin-bottom: 16px;
            background: #fafafa;
        }
        .ttd-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 25px;
            font-size: 0.86rem;
            page-break-inside: avoid;
        }
        .ttd-card {
            text-align: center;
        }
        .ttd-space {
            height: 65px;
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
            <strong>🖨️ Cetak Berita Acara Pelaksanaan Ujian CBT</strong>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">Cetak Berita Acara Sekarang</button>
        </div>
    </div>

    <div class="page">
        <!-- KOP SURAT RESMI STANDAR UNIVERSAL -->
        <?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>

        <div class="judul-dokumen">
            <h4>BERITA ACARA PELAKSANAAN ASESMEN</h4>
            <p>NOMOR: ..... / CBT-SMK / <?= date('m/Y') ?></p>
        </div>

        <?php
            $hari_array = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
            $bulan_array = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            
            $ts = strtotime($jadwal['tanggal_mulai'] ?? date('Y-m-d H:i'));
            $hari = $hari_array[date('l', $ts)] ?? 'Hari ini';
            $tgl = date('j', $ts);
            $bln = $bulan_array[(int)date('n', $ts)] ?? date('F', $ts);
            $thn = date('Y', $ts);

            $total_peserta = count($peserta_list);
            $hadir = 0;
            $absen = 0;
            $nama_absen = [];

            foreach ($peserta_list as $p) {
                if (in_array($p['status'], ['mengerjakan', 'selesai'])) {
                    $hadir++;
                } else {
                    $absen++;
                    $nama_absen[] = $p['nama_siswa'];
                }
            }
        ?>

        <p class="narasi-pembuka">
            Pada hari ini <strong><?= $hari ?></strong> tanggal <strong><?= $tgl ?></strong> bulan <strong><?= $bln ?></strong> tahun <strong><?= $thn ?></strong>, di <strong><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'SMA PLUS AL-MANSHURIYAH') ?></strong> telah diselenggarakan Asesmen Berbasis Komputer (CBT) untuk:
        </p>

        <table class="data-table">
            <tr>
                <td class="lbl">1. Nama Agenda Asesmen</td>
                <td style="width: 15px;">:</td>
                <td class="val"><?= htmlspecialchars($jadwal['nama_ujian']) ?></td>
            </tr>
            <tr>
                <td class="lbl">2. Mata Pelajaran</td>
                <td>:</td>
                <td class="val"><?= htmlspecialchars($jadwal['nama_mapel'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="lbl">3. Tingkat / Kelas</td>
                <td>:</td>
                <td class="val">Kelas <?= htmlspecialchars($jadwal['nama_kelas'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="lbl">4. Waktu Pelaksanaan</td>
                <td>:</td>
                <td class="val"><?= date('H:i', strtotime($jadwal['tanggal_mulai'])) ?> s.d. <?= date('H:i', strtotime($jadwal['tanggal_selesai'])) ?> WIB (Durasi: <?= $jadwal['durasi_menit'] ?? 90 ?> Menit)</td>
            </tr>
            <tr>
                <td class="lbl">5. Ruang &amp; Sesi</td>
                <td>:</td>
                <td class="val">Ruang 01 &bull; Sesi 1</td>
            </tr>
            <tr>
                <td class="lbl">6. Jumlah Peserta Terdaftar</td>
                <td>:</td>
                <td class="val"><?= $total_peserta ?> Orang</td>
            </tr>
            <tr>
                <td class="lbl">7. Jumlah Peserta Hadir</td>
                <td>:</td>
                <td class="val" style="color: #16a34a;"><?= $hadir ?> Orang</td>
            </tr>
            <tr>
                <td class="lbl">8. Jumlah Peserta Tidak Hadir</td>
                <td>:</td>
                <td class="val" style="color: #dc2626;"><?= $absen ?> Orang</td>
            </tr>
            <?php if (!empty($nama_absen)): ?>
            <tr>
                <td class="lbl">9. Daftar Siswa Tidak Hadir</td>
                <td>:</td>
                <td class="val" style="font-size: 0.8rem; font-weight: normal;">
                    <?= implode(', ', array_slice($nama_absen, 0, 10)) ?>
                    <?= count($nama_absen) > 10 ? ' (dan ' . (count($nama_absen) - 10) . ' lainnya)' : '' ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <div style="font-size: 0.86rem; font-weight: bold; margin-top: 10px;">
            Catatan Selama Pelaksanaan Asesmen:
        </div>
        <div class="catatan-box">
            Pelaksanaan Asesmen CBT berlangsung dengan tertib, aman, dan lancar tanpa kendala teknis jaringan atau server yang berarti. Seluruh siswa mengerjakan soal sesuai dengan tata tertib yang berlaku.
        </div>

        <p class="narasi-pembuka" style="margin-top: 15px;">
            Demikian Berita Acara ini dibuat dengan sesungguhnya dan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <!-- TANDA TANGAN -->
        <div class="ttd-grid">
            <div class="ttd-card">
                <p>Proktor CBT,</p>
                <div class="ttd-space"></div>
                <div class="ttd-name">...................................................</div>
                <p style="font-size: 0.78rem; color: #64748b;">NIP. -</p>
            </div>
            <div class="ttd-card">
                <p>Pengawas Ruang Ujian,</p>
                <div class="ttd-space"></div>
                <div class="ttd-name">...................................................</div>
                <p style="font-size: 0.78rem; color: #64748b;">NIP. -</p>
            </div>
        </div>
    </div>

</body>
</html>
