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
<div class="kop-sekolah">
    <h3><?= $kop_nama ?? 'SD/SMP/SMA NEGERI CONTOH' ?></h3>
    <p><?= $kop_alamat ?? 'Jl. Pendidikan No. 1, Kota Contoh, Telp. 021-12345678' ?></p>
    <p><?= $kop_npsn ? 'NPSN: '.$kop_npsn : 'NPSN: 12345678' ?></p>
</div>
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