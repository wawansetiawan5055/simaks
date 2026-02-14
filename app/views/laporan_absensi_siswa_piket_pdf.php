<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Siswa Piket</title>
    <style>
    body { font-family: Arial, sans-serif; font-size: 11px;}
    table {border-collapse:collapse;width:100%;}
    th,td {border:1px solid #333;padding:3px;}
    th {background:#eee;}
    h2 {text-align:center; margin-bottom: 5px;}
    .kop-sekolah {text-align:center; border-bottom:2px solid #333; margin-bottom:10px;}
    .kop-sekolah h3 {margin:0;}
    .kop-sekolah p {margin:0; font-size:11px;}
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    </style>
</head>
<body>
<div class="kop-sekolah">
    <h3><?= $kop_nama ?? 'SD/SMP/SMA NEGERI CONTOH' ?></h3>
    <p><?= $kop_alamat ?? 'Jl. Pendidikan No. 1, Kota Contoh, Telp. 021-12345678' ?></p>
    <p><?= $kop_npsn ? 'NPSN: '.$kop_npsn : 'NPSN: 12345678' ?></p>
</div>
<h2>Laporan Absensi Siswa Piket</h2>
<p style="text-align:center; margin-top:0;">
    <?php if ($jenis_laporan == 'bulanan'): ?>
        Rekap Bulanan: <?= $header_info['judul_bulan'] ?? '' ?>
    <?php elseif ($jenis_laporan == 'semester'): ?>
        Rekap Semester
    <?php endif; ?>
</p>

<?php if ($jenis_laporan == 'harian'): ?>
    <table>
    <thead>
        <tr>
          <th width="30">No</th>
          <th>Nama</th>
          <th>NISN</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)): ?>
            <?php $no=1; foreach ($list as $d): ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><?= $d['nama'] ?></td>
              <td class="text-center"><?= $d['nisn'] ?></td>
              <td class="text-center"><?= $d['tanggal'] ?></td>
              <td class="text-center"><?= $d['status'] ?></td>
              <td><?= $d['keterangan'] ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
        <?php endif; ?>
    </tbody>
    </table>

<?php elseif ($jenis_laporan == 'bulanan'): ?>
    <table style="font-size: 9px;">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" class="text-left">Nama Siswa</th>
                <th colspan="<?= count($data['dates']) ?>">Tanggal</th>
                <th colspan="4">Rekap</th>
                <th rowspan="2">%</th>
            </tr>
            <tr>
                <?php foreach ($data['dates'] as $dt): ?>
                    <th width="15"><?= date('d', strtotime($dt)) ?></th>
                <?php endforeach; ?>
                <th width="20">H</th>
                <th width="20">S</th>
                <th width="20">I</th>
                <th width="20">A</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data['students'] as $s): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $s['nama'] ?></td>
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

<?php elseif ($jenis_laporan == 'semester'): ?>
    <table style="font-size: 9px;">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" class="text-left">Nama Siswa</th>
                <?php foreach ($data['months'] as $m): ?>
                    <th colspan="4"><?= date('F', strtotime($m . "-01")) ?></th>
                <?php endforeach; ?>
                <th colspan="4">Total</th>
                <th rowspan="2">%</th>
            </tr>
            <tr>
                <?php foreach ($data['months'] as $m): ?>
                    <th>H</th><th>S</th><th>I</th><th>A</th>
                <?php endforeach; ?>
                <th>H</th><th>S</th><th>I</th><th>A</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data['students'] as $s): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $s['nama'] ?></td>
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
</body>
</html>