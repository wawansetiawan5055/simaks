<!DOCTYPE html>
<html>
<head>
    <title>Laporan Mapel</title>
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
<h2>Laporan Data Mata Pelajaran</h2>
<table>
<tr>
  <th>No</th><th>Nama Mapel</th><th>Kategori</th><th>KKM</th>
</tr>
<?php $no=1; foreach ($mapel_list as $m): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= $m['nama_mapel'] ?></td>
  <td><?= $m['kategori_mapel'] ?></td>
  <td><?= $m['kkm'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>