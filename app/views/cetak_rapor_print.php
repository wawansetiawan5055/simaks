<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor — <?= htmlspecialchars($data['biodata']['nama']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        /* ── Page Setup ── */
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 20mm 25mm;
        }

        .page {
            width: 170mm;
            min-height: 257mm;
            margin: 0 auto;
            padding: 0;
            position: relative;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* ── Header Biodata ── */
        .biodata-header {
            width: 100%;
            margin-bottom: 12pt;
        }
        .biodata-header table {
            width: 100%;
            border-collapse: collapse;
        }
        .biodata-header td {
            padding: 1.5pt 4pt;
            vertical-align: top;
            font-size: 10.5pt;
        }
        .biodata-label { width: 28%; font-weight: normal; }
        .biodata-sep   { width: 3%; }
        .biodata-val   { width: 36%; }
        hr.bio-line {
            border: none;
            border-top: 1.5px solid #000;
            margin: 8pt 0;
        }

        /* ── Page Title ── */
        .page-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 10pt 0 12pt;
            letter-spacing: 1px;
        }

        /* ── Nilai Table ── */
        .table-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        .table-nilai th, .table-nilai td {
            border: 1px solid #000;
            padding: 3pt 5pt;
            vertical-align: top;
        }
        .table-nilai thead th {
            text-align: center;
            font-size: 10.5pt;
            background: #f0f0f0;
        }
        .table-nilai td.no    { text-align: center; width: 28pt; }
        .table-nilai td.mapel { width: 110pt; font-weight: bold; }
        .table-nilai td.nilai { text-align: center; width: 42pt; font-weight: bold; }
        .table-nilai td.deskripsi { font-size: 10pt; text-align: justify; line-height: 1.45; }

        .row-group td {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 10.5pt;
            padding: 3pt 5pt;
        }

        /* ── Kokurikuler Box ── */
        .box-section {
            border: 1px solid #000;
            margin-bottom: 10pt;
        }
        .box-section .box-title {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
            padding: 3pt 5pt;
            border-bottom: 1px solid #000;
            font-size: 10.5pt;
        }
        .box-section .box-body {
            padding: 6pt 8pt;
            font-size: 10pt;
            line-height: 1.5;
            text-align: justify;
        }

        /* ── Ekskul Table ── */
        .table-ekskul {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        .table-ekskul th, .table-ekskul td {
            border: 1px solid #000;
            padding: 3pt 5pt;
        }
        .table-ekskul thead th {
            text-align: center;
            background: #f0f0f0;
            font-size: 10.5pt;
        }
        .table-ekskul td.no-col   { text-align: center; width: 28pt; }
        .table-ekskul td.ket-col  { font-size: 10pt; }

        /* ── Ketidakhadiran + Catatan ── */
        .row-flex {
            display: flex;
            gap: 8pt;
            margin-bottom: 10pt;
        }
        .box-kehadiran {
            border: 1px solid #000;
            width: 45%;
        }
        .box-kehadiran .box-title,
        .box-catatan .box-title {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
            padding: 3pt 5pt;
            border-bottom: 1px solid #000;
            font-size: 10.5pt;
        }
        .box-kehadiran table { width: 100%; border-collapse: collapse; }
        .box-kehadiran td { padding: 3pt 8pt; font-size: 10pt; border: none; }
        .box-kehadiran td:first-child { width: 60%; }

        .box-catatan {
            border: 1px solid #000;
            flex: 1;
        }
        .box-catatan .box-body {
            padding: 5pt 8pt;
            font-size: 10pt;
            line-height: 1.5;
            min-height: 60pt;
        }

        /* ── Tanggapan Ortu ── */
        .box-ortu {
            border: 1px solid #000;
            margin-bottom: 14pt;
        }
        .box-ortu .box-body {
            min-height: 50pt;
            padding: 5pt 8pt;
        }

        /* ── Tanda Tangan ── */
        .ttd-section {
            width: 100%;
            margin-top: 14pt;
        }
        .ttd-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-section td {
            text-align: center;
            vertical-align: top;
            padding: 0 5pt;
            font-size: 10.5pt;
            width: 33.33%;
        }
        .ttd-line {
            border-top: 1px solid #000;
            margin-top: 42pt;
            padding-top: 3pt;
        }
        .ttd-name { font-weight: bold; }

        /* ── Footer setiap halaman ── */
        .page-footer {
            position: fixed;
            bottom: 5mm;
            left: 0; right: 0;
            text-align: center;
            font-size: 9pt;
            color: #444;
            border-top: 1px solid #ccc;
            padding-top: 3pt;
        }

        /* ── Print: auto print if ?print=1 ── */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
        }

        /* Screen only */
        @media screen {
            body { background: #ccc; padding: 20px; }
            .page {
                background: #fff;
                box-shadow: 0 0 15px rgba(0,0,0,0.3);
                padding: 15mm 20mm 20mm 25mm;
                margin-bottom: 20px;
            }
            .no-print {
                position: fixed;
                top: 15px; right: 15px;
                z-index: 9999;
            }
        }
    </style>
</head>
<body>

<?php
$biodata  = $data['biodata'];
$sekolah  = $data['sekolah'];
$fase     = $data['fase'];
$semester = $data['semester'];
$nilai_grouped = $data['nilai_grouped'];
$sikap    = $data['sikap'];
$ekskul   = $data['ekskul'];
$kokurikuler = $data['kokurikuler'];
$kehadiran = $data['kehadiran'];
$catatan  = $data['catatan'];

$nama_ta  = $biodata['nama_ta'] ?? '-';
$sem_label = $semester == 1 ? 'Ganjil' : 'Genap';

// City & date for signature
$kota = explode(',', $sekolah['alamat'] ?? '')[0] ?? 'Nagrak';
$bulan_label = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$bln = (int)date('n');
$tgl_ttd = date('j') . ' ' . $bulan_label[$bln] . ' ' . date('Y');

// Row counter per group
$no_counters = [];

// Build kokurikuler narasi
$kok_narasi = '';
if (!empty($kokurikuler)) {
    $names = array_column($kokurikuler, 'nama_ekskul');
    $kok_narasi = "Pada semester ini, ananda menunjukkan capaian yang baik dalam penguatan profil lulusan, yang ditunjukkan melalui kegiatan kokurikuler " . implode(', ', $names) . ".";
}

// Biodata partial (reused on each page)
function renderBiodata($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta) {
    $alamat = $sekolah['alamat'] ?? '-';
    ?>
    <div class="biodata-header">
        <table>
            <tr>
                <td class="biodata-label">Nama Murid</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><strong><?= htmlspecialchars($biodata['nama']) ?></strong></td>
                <td class="biodata-label">Kelas</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($biodata['nama_kelas']) ?></td>
            </tr>
            <tr>
                <td class="biodata-label">NIS/NISN</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($biodata['nipd']) ?> / <?= htmlspecialchars($biodata['nisn']) ?></td>
                <td class="biodata-label">Fase</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($fase) ?></td>
            </tr>
            <tr>
                <td class="biodata-label">Sekolah</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($sekolah['nama_sekolah'] ?? '-') ?></td>
                <td class="biodata-label">Semester</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= $semester ?> (<?= $sem_label ?>)</td>
            </tr>
            <tr>
                <td class="biodata-label">Alamat</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($alamat) ?></td>
                <td class="biodata-label">Tahun Ajaran</td>
                <td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($nama_ta) ?></td>
            </tr>
        </table>
    </div>
    <hr class="bio-line">
    <?php
}
?>

<!-- ═══════════════════════════════════ -->
<!--  HALAMAN 1: LAPORAN HASIL BELAJAR  -->
<!-- ═══════════════════════════════════ -->
<div class="page">
    <?php renderBiodata($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta); ?>

    <div class="page-title">LAPORAN HASIL BELAJAR</div>

    <table class="table-nilai">
        <thead>
            <tr>
                <th style="width:28pt;">No</th>
                <th style="width:110pt;">Mata Pelajaran</th>
                <th style="width:42pt;">Nilai Akhir</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no_per_group = [];
            foreach ($nilai_grouped as $kategori => $mapel_list):
            ?>
            <tr class="row-group">
                <td colspan="4"><?= htmlspecialchars($kategori) ?></td>
            </tr>
            <?php
            $no = 1;
            foreach ($mapel_list as $mapel):
                $na  = $mapel['nilai_akhir'];
                $des = $mapel['deskripsi_rapor'] ?: 'Belum ada data capaian untuk mata pelajaran ini.';
            ?>
            <tr>
                <td class="no"><?= $no++ ?></td>
                <td class="mapel"><?= htmlspecialchars($mapel['nama_mapel']) ?></td>
                <td class="nilai"><?= $na !== null ? number_format($na, 0) : '-' ?></td>
                <td class="deskripsi"><?= htmlspecialchars($des) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ═══════════════════════════════════════ -->
<!--  HALAMAN 2: KOKURIKULER & EKSKUL       -->
<!-- ═══════════════════════════════════════ -->
<div class="page page-break" style="padding-top: 5mm;">
    <?php renderBiodata($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta); ?>

    <!-- Kokurikuler -->
    <div class="box-section">
        <div class="box-title">Kokurikuler</div>
        <div class="box-body">
            <?= $kok_narasi ?: 'Tidak ada data kokurikuler pada semester ini.' ?>
        </div>
    </div>

    <!-- Ekstrakurikuler -->
    <table class="table-ekskul">
        <thead>
            <tr>
                <th style="width:28pt;">No</th>
                <th>Ekstrakurikuler</th>
                <th style="width:120pt;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ekskul)): ?>
                <?php foreach ($ekskul as $i => $e): ?>
                <tr>
                    <td class="no-col"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($e['nama_ekskul']) ?></td>
                    <td class="ket-col"><?= htmlspecialchars($e['deskripsi'] ?: ($e['predikat'] ?: '-')) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td class="no-col">1</td><td></td><td></td>
                </tr>
                <tr>
                    <td class="no-col">2</td><td></td><td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ═══════════════════════════════════════════ -->
<!--  HALAMAN 3: KEHADIRAN, CATATAN, TTD        -->
<!-- ═══════════════════════════════════════════ -->
<div class="page page-break" style="padding-top: 5mm;">
    <?php renderBiodata($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta); ?>

    <!-- Kehadiran + Catatan Wali Kelas -->
    <div class="row-flex">
        <div class="box-kehadiran">
            <div class="box-title">Ketidakhadiran</div>
            <table>
                <tr>
                    <td>Sakit</td>
                    <td>: <?= (int)($kehadiran['sakit'] ?? 0) ?> hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td>: <?= (int)($kehadiran['izin'] ?? 0) ?> hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td>: <?= (int)($kehadiran['alpa'] ?? 0) ?> hari</td>
                </tr>
            </table>
        </div>

        <div class="box-catatan">
            <div class="box-title">Catatan Wali Kelas</div>
            <div class="box-body">
                <?= htmlspecialchars($catatan['catatan'] ?? '') ?>
            </div>
        </div>
    </div>

    <!-- Tanggapan Orang Tua -->
    <div class="box-ortu">
        <div class="box-title">Tanggapan Orang Tua/Wali Murid</div>
        <div class="box-body"></div>
    </div>

    <!-- Tanda Tangan -->
    <div class="ttd-section">
        <table>
            <tr>
                <td>Orang Tua Murid</td>
                <td><?= htmlspecialchars($kota) ?>, <?= $tgl_ttd ?><br>Kepala <?= htmlspecialchars($sekolah['nama_sekolah'] ?? '') ?></td>
                <td>Wali Kelas</td>
            </tr>
            <tr>
                <td>
                    <div class="ttd-line">
                        <div class="ttd-name">..................................</div>
                    </div>
                </td>
                <td>
                    <div class="ttd-line">
                        <div class="ttd-name"><?= htmlspecialchars($sekolah['nama_kepala_sekolah'] ?? '') ?></div>
                        <div>NIY.</div>
                    </div>
                </td>
                <td>
                    <div class="ttd-line">
                        <div class="ttd-name"><?= htmlspecialchars($biodata['nama_wali_kelas'] ?? '') ?></div>
                        <div>NIY</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Footer tiap halaman (print) -->
<div class="page-footer">
    <?= htmlspecialchars($biodata['nama_kelas']) ?> &nbsp;|&nbsp;
    <?= htmlspecialchars($biodata['nama']) ?> &nbsp;|&nbsp;
    <?= htmlspecialchars($biodata['nipd']) ?>
</div>

<!-- Print Button (screen only) -->
<div class="no-print">
    <button onclick="window.print()" class="btn btn-primary mr-2" style="background:#007bff;color:#fff;border:none;padding:8px 18px;border-radius:4px;cursor:pointer;font-size:14px;">
        🖨️ Cetak / Simpan PDF
    </button>
    <button onclick="window.close()" style="background:#6c757d;color:#fff;border:none;padding:8px 18px;border-radius:4px;cursor:pointer;font-size:14px;">
        ✕ Tutup
    </button>
</div>

<?php if (isset($_GET['print']) && $_GET['print'] == 1): ?>
<script>window.onload = function() { window.print(); };</script>
<?php endif; ?>

</body>
</html>
