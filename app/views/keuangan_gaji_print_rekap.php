<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Gaji - <?= $gaji['bulan'] ?>/<?= $gaji['tahun'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; text-transform: uppercase; font-weight: bold; }
        .table-rekap { width: 100%; border-collapse: collapse; }
        .table-rekap th, .table-rekap td { border: 1px solid #000; padding: 4px; }
        .table-rekap th { background-color: #eee; text-align: center; font-size: 8pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signature { margin-top: 40px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sig-box { text-align: center; width: 250px; }
        
        @media print {
            @page { size: landscape; margin: 10mm; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            REKAPITULASI HONORARIUM & TUNJANGAN GURU/PEGAWAI<br>
            <?= $_SESSION['nama_sekolah'] ?? 'SMK TELKOM LAMPUNG' ?><br>
            PERIODE: <?= date('F', mktime(0,0,0,$gaji['bulan'],1)) ?> <?= $gaji['tahun'] ?>
        </div>

        <table class="table-rekap">
            <thead>
                <tr>
                    <th rowspan="2" width="30">NO</th>
                    <th rowspan="2">NAMA PEGAWAI</th>
                    <th colspan="4">HONOR (VAR)</th>
                    <th colspan="3">TUNJANGAN (TETAP)</th>
                    <th rowspan="2">TOTAL<br>KOTOR</th>
                    <th rowspan="2">TOTAL<br>POT</th>
                    <th rowspan="2">DITERIMA<br>(NET)</th>
                    <th rowspan="2" width="100">TANDA TANGAN</th>
                </tr>
                <tr>
                    <th>JJM</th>
                    <th>TRANS</th>
                    <th>KBM</th>
                    <th>EKS</th>
                    <th>JABATAN</th>
                    <th>TUGAS</th>
                    <th>LAIN</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                $grand_total = 0;
                $grand_pot = 0;
                $grand_net = 0;
                
                foreach($details as $row): 
                    $honor = $row['subtotal_jjm'] + $row['subtotal_transport'] + $row['subtotal_kinerja'] + $row['tunj_ekskul'];
                    
                    $tunj_jab = $row['tunj_kepsek'] + $row['tunj_tas'] + $row['tunj_plk'] + $row['tunj_penjaga'] + $row['tunj_satpam'] + $row['tunj_sopir'];
                    
                    $tunj_tugas = $row['tunj_kurikulum'] + $row['tunj_kesiswaan'] + $row['tunj_sarpras'] + $row['tunj_humas'] + 
                                  $row['tunj_walas'] + 
                                  $row['tunj_kepala_lab'] + $row['tunj_kepala_perpus'] + $row['tunj_operator'] + $row['tunj_pembina_keagamaan'] + $row['tunj_pengelola_smater'] +
                                  $row['tunj_pembina'];
                                  
                    $tunj_lain = $row['tunjangan_lain'];
                    
                    $pendapatan = $honor + $tunj_jab + $tunj_tugas + $tunj_lain;
                    
                    $potongan = $row['potongan_bpjs_kes'] + $row['potongan_bpjs_tk'] + $row['potongan_kasbon'] + $row['potongan_lain'];
                    $net = $row['total_diterima'];
                    
                    $grand_total += $pendapatan;
                    $grand_pot += $potongan;
                    $grand_net += $net;
                ?>
                <tr>
                    <td class="text-center small"><?= $no++ ?></td>
                    <td class="small"><?= $row['nama_guru'] ?></td>
                    <td class="text-right small"><?= number_format($row['subtotal_jjm'], 0, ',', '.') ?></td>
                    <td class="text-right small"><?= number_format($row['subtotal_transport'], 0, ',', '.') ?></td>
                    <td class="text-right small"><?= number_format($row['subtotal_kinerja'], 0, ',', '.') ?></td>
                    <td class="text-right small"><?= number_format($row['tunj_ekskul'], 0, ',', '.') ?></td>
                    
                    <td class="text-right small"><?= number_format($tunj_jab, 0, ',', '.') ?></td>
                    <td class="text-right small"><?= number_format($tunj_tugas, 0, ',', '.') ?></td>
                    <td class="text-right small"><?= number_format($tunj_lain, 0, ',', '.') ?></td>
                    
                    <td class="text-right small" style="background:#f9f9f9;font-weight:bold;"><?= number_format($pendapatan, 0, ',', '.') ?></td>
                    <td class="text-right small text-danger"><?= number_format($potongan, 0, ',', '.') ?></td>
                    <td class="text-right" style="background:#eee;font-weight:bold;"><?= number_format($net, 0, ',', '.') ?></td>
                    <td class="text-center" style="vertical-align:bottom;height:35px;font-size:7pt;color:#ccc;">( <?= $row['nama_guru'] ?> )</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#ddd; font-weight:bold;">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td colspan="4"></td>
                    <td colspan="3"></td>
                    <td class="text-right"><?= number_format($grand_total, 0, ',', '.') ?></td>
                    <td class="text-right text-danger"><?= number_format($grand_pot, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($grand_net, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="signature">
            <div class="sig-box">
                <p>Mengetahui,<br>Kepala Sekolah</p>
                <br><br><br>
                <strong>( ............................ )</strong>
            </div>
            <div class="sig-box">
                <p>Pringsewu, <?= date('d F Y') ?><br>Bendahara</p>
                <br><br><br>
                <strong>( ............................ )</strong>
            </div>
        </div>
    </div>
</body>
</html>
