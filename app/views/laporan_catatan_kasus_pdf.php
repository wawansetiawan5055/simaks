<!DOCTYPE html>
<html>
<head>
    <title>Laporan Catatan Kasus</title>
    <style>
    body { font-family: Arial, sans-serif; font-size: 12px;}
    table {border-collapse:collapse;width:100%;}
    th,td {border:1px solid #333;padding:4px;}
    th {background:#eee;}
    h2 {text-align:center;}
    .kop-sekolah {text-align:center; border-bottom:2px solid #333; margin-bottom:10px;}
    .kop-sekolah h3 {margin:0;}
    .kop-sekolah p {margin:0; font-size:11px;}
    </style>
</head>
<body>
<?php include __DIR__ . '/partials/kop_surat_universal.php'; ?>
<h2>Laporan Catatan Kasus/Kejadian Siswa</h2>
<table>
<tr>
  <th>No</th><th>Nama Siswa</th><th>Kelas</th><th>Tanggal</th><th>Catatan</th><th>Tindak Lanjut</th><th>Keterangan</th>
</tr>
<?php $no=1; foreach ($list as $d): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= $d['nama'] ?></td>
  <td><?= $d['nama_kelas'] ?></td>
  <td><?= $d['tanggal'] ?></td>
  <td><?= $d['catatan'] ?></td>
  <td><?= $d['tindak_lanjut'] ?></td>
  <td><?= $d['keterangan'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>