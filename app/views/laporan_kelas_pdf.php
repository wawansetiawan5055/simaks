<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kelas</title>
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
<h2>Laporan Data Kelas</h2>
<table>
<tr>
  <th>No</th><th>Nama Kelas</th><th>Tingkat</th>
</tr>
<?php $no=1; foreach ($kelas_list as $k): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= $k['nama_kelas'] ?></td>
  <td><?= $k['tingkat'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>