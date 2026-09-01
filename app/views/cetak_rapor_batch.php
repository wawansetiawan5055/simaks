<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rapor Semua Siswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; background: #fff; }

        @page { size: A4 portrait; margin: 15mm 20mm 20mm 25mm; }

        .page { width: 170mm; min-height: 257mm; margin: 0 auto; position: relative; }
        .page-break { page-break-after: always; break-after: page; }
        .section-break { page-break-before: always; break-before: page; }

        .biodata-header table { width: 100%; border-collapse: collapse; }
        .biodata-header td { padding: 1.5pt 4pt; font-size: 10.5pt; vertical-align: top; }
        .biodata-label { width: 28%; }
        .biodata-sep { width: 3%; }
        .biodata-val { width: 36%; }
        hr.bio-line { border: none; border-top: 1.5px solid #000; margin: 8pt 0; }

        .page-title { text-align: center; font-size: 14pt; font-weight: bold; margin: 10pt 0 12pt; letter-spacing: 1px; }

        .table-nilai { width: 100%; border-collapse: collapse; margin-bottom: 10pt; }
        .table-nilai th, .table-nilai td { border: 1px solid #000; padding: 3pt 5pt; vertical-align: top; }
        .table-nilai thead th { text-align: center; font-size: 10.5pt; background: #f0f0f0; }
        .table-nilai td.no { text-align: center; width: 28pt; }
        .table-nilai td.mapel { width: 110pt; font-weight: bold; }
        .table-nilai td.nilai { text-align: center; width: 42pt; font-weight: bold; }
        .table-nilai td.deskripsi { font-size: 10pt; text-align: justify; line-height: 1.45; }
        .row-group td { background: #e8e8e8; font-weight: bold; padding: 3pt 5pt; }

        .box-section { border: 1px solid #000; margin-bottom: 10pt; }
        .box-section .box-title { background: #f0f0f0; text-align: center; font-weight: bold; padding: 3pt 5pt; border-bottom: 1px solid #000; font-size: 10.5pt; }
        .box-section .box-body { padding: 6pt 8pt; font-size: 10pt; line-height: 1.5; text-align: justify; }

        .table-ekskul { width: 100%; border-collapse: collapse; margin-bottom: 10pt; }
        .table-ekskul th, .table-ekskul td { border: 1px solid #000; padding: 3pt 5pt; }
        .table-ekskul thead th { text-align: center; background: #f0f0f0; }

        .row-flex { display: flex; gap: 8pt; margin-bottom: 10pt; }
        .box-kehadiran { border: 1px solid #000; width: 45%; }
        .box-kehadiran .box-title, .box-catatan .box-title { background: #f0f0f0; text-align: center; font-weight: bold; padding: 3pt 5pt; border-bottom: 1px solid #000; font-size: 10.5pt; }
        .box-kehadiran table { width: 100%; border-collapse: collapse; }
        .box-kehadiran td { padding: 3pt 8pt; font-size: 10pt; }
        .box-catatan { border: 1px solid #000; flex: 1; }
        .box-catatan .box-body { padding: 5pt 8pt; font-size: 10pt; line-height: 1.5; min-height: 60pt; }
        .box-ortu { border: 1px solid #000; margin-bottom: 14pt; }
        .box-ortu .box-body { min-height: 50pt; padding: 5pt 8pt; }

        .ttd-section table { width: 100%; border-collapse: collapse; }
        .ttd-section td { text-align: center; vertical-align: top; padding: 0 5pt; font-size: 10.5pt; width: 33.33%; }
        .ttd-line { border-top: 1px solid #000; margin-top: 42pt; padding-top: 3pt; }
        .ttd-name { font-weight: bold; }

        .page-footer { text-align: center; font-size: 9pt; color: #444; border-top: 1px solid #ccc; padding-top: 3pt; margin-top: 10pt; }

        @media screen {
            body { background: #ccc; padding: 20px; }
            .page { background: #fff; box-shadow: 0 0 15px rgba(0,0,0,0.3); padding: 15mm 20mm 20mm 25mm; margin-bottom: 20px; }
            .no-print { position: fixed; top: 15px; right: 15px; z-index: 9999; }
        }
        @media print { body { background: #fff; } .no-print { display: none !important; } }
    </style>
</head>
<body>

<?php
$bulan_label = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$bln = (int)date('n');
$tgl_ttd = date('j') . ' ' . $bulan_label[$bln] . ' ' . date('Y');

function renderBiodataBatch($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta) {
    ?>
    <div class="biodata-header">
        <table>
            <tr>
                <td class="biodata-label">Nama Murid</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><strong><?= htmlspecialchars($biodata['nama']) ?></strong></td>
                <td class="biodata-label">Kelas</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($biodata['nama_kelas']) ?></td>
            </tr>
            <tr>
                <td class="biodata-label">NIS/NISN</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($biodata['nipd']) ?> / <?= htmlspecialchars($biodata['nisn']) ?></td>
                <td class="biodata-label">Fase</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($fase) ?></td>
            </tr>
            <tr>
                <td class="biodata-label">Sekolah</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($sekolah['nama_sekolah'] ?? '-') ?></td>
                <td class="biodata-label">Semester</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= $semester ?> (<?= $sem_label ?>)</td>
            </tr>
            <tr>
                <td class="biodata-label">Alamat</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($sekolah['alamat'] ?? '-') ?></td>
                <td class="biodata-label">Tahun Ajaran</td><td class="biodata-sep">:</td>
                <td class="biodata-val"><?= htmlspecialchars($nama_ta) ?></td>
            </tr>
        </table>
    </div>
    <hr class="bio-line">
    <?php
}

$sem_label = ($all_data[0]['semester'] ?? 1) == 1 ? 'Ganjil' : 'Genap';
$siswa_count = count($all_data);

foreach ($all_data as $idx => $data):
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
    $kota = explode(',', $sekolah['alamat'] ?? 'Nagrak')[0];
    $kok_narasi = '';
    if (!empty($kokurikuler)) {
        $names = array_column($kokurikuler, 'nama_ekskul');
        $kok_narasi = "Pada semester ini, ananda menunjukkan capaian yang baik dalam penguatan profil lulusan, melalui kegiatan kokurikuler " . implode(', ', $names) . ".";
    }
?>

<!-- PAGE 1: NILAI -->
<div class="page <?= $idx > 0 ? 'section-break' : '' ?>">
    <?php renderBiodataBatch($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta); ?>
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
        <?php foreach ($nilai_grouped as $kategori => $mapel_list): ?>
            <tr class="row-group"><td colspan="4"><?= htmlspecialchars($kategori) ?></td></tr>
            <?php $no = 1; foreach ($mapel_list as $mapel): ?>
            <tr>
                <td class="no"><?= $no++ ?></td>
                <td class="mapel"><?= htmlspecialchars($mapel['nama_mapel']) ?></td>
                <td class="nilai"><?= number_format($mapel['nilai_akhir'] ?? 0, 0) ?></td>
                <td class="deskripsi"><?= htmlspecialchars($mapel['deskripsi_rapor'] ?: 'Belum ada data.') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="page-footer"><?= htmlspecialchars($biodata['nama_kelas']) ?> | <?= htmlspecialchars($biodata['nama']) ?> | <?= htmlspecialchars($biodata['nipd']) ?></div>
</div>

<!-- PAGE 2: EKSKUL -->
<div class="page page-break">
    <?php renderBiodataBatch($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta); ?>
    <div class="box-section">
        <div class="box-title">Kokurikuler</div>
        <div class="box-body"><?= $kok_narasi ?: 'Tidak ada data kokurikuler.' ?></div>
    </div>
    <table class="table-ekskul">
        <thead><tr><th style="width:28pt;">No</th><th>Ekstrakurikuler</th><th style="width:120pt;">Keterangan</th></tr></thead>
        <tbody>
        <?php if (!empty($ekskul)): foreach ($ekskul as $i => $e): ?>
            <tr><td style="text-align:center;"><?= $i+1 ?></td><td><?= htmlspecialchars($e['nama_ekskul']) ?></td><td><?= htmlspecialchars($e['deskripsi'] ?: ($e['predikat'] ?: '-')) ?></td></tr>
        <?php endforeach; else: ?>
            <tr><td style="text-align:center;">1</td><td></td><td></td></tr>
            <tr><td style="text-align:center;">2</td><td></td><td></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="page-footer"><?= htmlspecialchars($biodata['nama_kelas']) ?> | <?= htmlspecialchars($biodata['nama']) ?> | <?= htmlspecialchars($biodata['nipd']) ?></div>
</div>

<!-- PAGE 3: KEHADIRAN + TTD -->
<div class="page page-break">
    <?php renderBiodataBatch($biodata, $sekolah, $fase, $semester, $sem_label, $nama_ta); ?>
    <div class="row-flex">
        <div class="box-kehadiran">
            <div class="box-title">Ketidakhadiran</div>
            <table>
                <tr><td>Sakit</td><td>: <?= (int)($kehadiran['sakit']??0) ?> hari</td></tr>
                <tr><td>Izin</td><td>: <?= (int)($kehadiran['izin']??0) ?> hari</td></tr>
                <tr><td>Tanpa Keterangan</td><td>: <?= (int)($kehadiran['alpa']??0) ?> hari</td></tr>
            </table>
        </div>
        <div class="box-catatan">
            <div class="box-title">Catatan Wali Kelas</div>
            <div class="box-body"><?= htmlspecialchars($catatan['catatan'] ?? '') ?></div>
        </div>
    </div>
    <div class="box-ortu">
        <div class="box-title">Tanggapan Orang Tua/Wali Murid</div>
        <div class="box-body"></div>
    </div>
    <div class="ttd-section">
        <table>
            <tr>
                <td>Orang Tua Murid</td>
                <td><?= htmlspecialchars($kota) ?>, <?= $tgl_ttd ?><br>Kepala <?= htmlspecialchars($sekolah['nama_sekolah']??'') ?></td>
                <td>Wali Kelas</td>
            </tr>
            <tr>
                <td><div class="ttd-line"><div class="ttd-name">..................................</div></div></td>
                <td><div class="ttd-line"><div class="ttd-name"><?= htmlspecialchars($sekolah['nama_kepala_sekolah']??'') ?></div><div>NIY.</div></div></td>
                <td><div class="ttd-line"><div class="ttd-name"><?= htmlspecialchars($biodata['nama_wali_kelas']??'') ?></div><div>NIY</div></div></td>
            </tr>
        </table>
    </div>
    <div class="page-footer"><?= htmlspecialchars($biodata['nama_kelas']) ?> | <?= htmlspecialchars($biodata['nama']) ?> | <?= htmlspecialchars($biodata['nipd']) ?></div>
</div>

<?php endforeach; ?>

<div class="no-print">
    <button onclick="window.print()" style="background:#007bff;color:#fff;border:none;padding:8px 18px;border-radius:4px;cursor:pointer;font-size:14px;">🖨️ Cetak Semua</button>
    <button onclick="window.close()" style="background:#6c757d;color:#fff;border:none;padding:8px 18px;border-radius:4px;cursor:pointer;font-size:14px;margin-left:8px;">✕ Tutup</button>
</div>

<script>window.onload = function() { window.print(); };</script>
</body>
</html>
