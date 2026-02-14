<!DOCTYPE html>
<html>
<head>
    <title>Laporan Jurnal KBM</title>
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
<h2>Laporan Jurnal/Agenda KBM</h2>
<table>
<tr>
  <th>No</th><th>Guru</th><th>Kelas</th><th>Tanggal</th><th>Jam Ke</th><th>Tujuan Pembelajaran</th><th>Tagihan</th><th>Rekap Absensi</th><th>Keterangan</th>
</tr>
<?php $no=1; foreach ($list as $d): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= $d['guru'] ?></td>
  <td><?= $d['nama_kelas'] ?></td>
  <td><?= $d['tanggal'] ?></td>
  <td><?= $d['jam_ke'] ?></td>
  <td><?= $d['tujuan_pembelajaran'] ?></td>
  <td><?= $d['tagihan'] ?></td>
  <td><?= $d['catatan_absensi'] ?></td>
  <td><?= $d['keterangan'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>