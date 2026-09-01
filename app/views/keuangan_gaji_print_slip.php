<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?= $data['nama_guru'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #000; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; vertical-align: top; }
        .rincian-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .rincian-table th, .rincian-table td { border: 1px solid #000; padding: 5px; }
        .rincian-table th { background-color: #eee; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #eee; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .sig-box { text-align: center; width: 200px; }
        .sig-box .name { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        @media print {
            .container { border: none; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <?php
    $bulanName = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    ?>
    <div class="container">
        <div class="header">
            <h2>SLIP GAJI GURU &amp; KARYAWAN</h2>
            <p style="font-weight: bold; font-size: 11pt;"><?= htmlspecialchars($sekolah['nama_sekolah'] ?? $_SESSION['nama_sekolah'] ?? 'SIMAKS ACADEMY') ?></p>
            <p>Periode: <strong><?= bulan_indo($data['bulan']) ?> <?= $data['tahun'] ?></strong></p>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%">Nama</td>
                <td width="2%">:</td>
                <td width="33%"><strong><?= htmlspecialchars($data['nama_guru']) ?></strong></td>
                <td width="15%">ID Pegawai</td>
                <td width="2%">:</td>
                <td width="33%"><?= htmlspecialchars($data['id_guru']) ?> / <?= htmlspecialchars($data['nip'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Guru / Staff</td>
                <td>Tanggal Cetak</td>
                <td>:</td>
                <td><?= tgl_indo() ?></td>
            </tr>
        </table>

        <table class="rincian-table">
            <thead>
                <tr>
                    <th colspan="3">A. PENDAPATAN</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. HONORARIUM (BERDASARKAN ABSENSI) -->
                <tr>
                    <td colspan="3" style="background-color: #f9f9f9; font-weight: bold; font-size: 9pt;">1. HONORARIUM (Berdasarkan Beban Kerja & Absensi)</td>
                </tr>
                <?php if($data['subtotal_jjm'] > 0): ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp; - Honor Jam Mengajar (JJM)</td>
                    <td class="text-center small"><?= $data['jml_jjm'] ?> Jam x Rp <?= number_format($data['tarif_jjm'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($data['subtotal_jjm'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if($data['subtotal_transport'] > 0): ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp; - Tunjangan Transport (Kehadiran)</td>
                    <td class="text-center small"><?= $data['jml_hadir'] ?> Hari x Rp <?= number_format($data['tarif_transport'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($data['subtotal_transport'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if($data['subtotal_kinerja'] > 0): ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp; - Insentif Kinerja (KBM)</td>
                    <td class="text-center small"><?= $data['jml_kbm'] ?> Kali x Rp <?= number_format($data['tarif_kinerja'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($data['subtotal_kinerja'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>

                <?php if($data['tunj_ekskul'] > 0): ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp; - Tunjangan Ekstrakurikuler (<?= $data['jml_ekskul'] ?>)</td>
                    <td class="text-center small">-</td>
                    <td class="text-right">Rp <?= number_format($data['tunj_ekskul'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>

                <!-- 2. TUNJANGAN & TUGAS TAMBAHAN -->
                <tr>
                    <td colspan="3" style="background-color: #f9f9f9; font-weight: bold; font-size: 9pt;">2. TUNJANGAN JABATAN & TUGAS TAMBAHAN</td>
                </tr>
                <?php 
                $tunjList = [
                    'Kepala Sekolah' => $data['tunj_kepsek'],
                    'Tenaga Administrasi Sekolah (TAS)' => $data['tunj_tas'],
                    'Petugas Layanan Khusus (PLK)' => $data['tunj_plk'],
                    'Penjaga Sekolah' => $data['tunj_penjaga'],
                    'Satpam' => $data['tunj_satpam'],
                    'Sopir' => $data['tunj_sopir'],
                    'Waka Kurikulum' => $data['tunj_kurikulum'],
                    'Waka Kesiswaan' => $data['tunj_kesiswaan'],
                    'Waka Sarpras' => $data['tunj_sarpras'],
                    'Waka Humas' => $data['tunj_humas'],
                    'Kepala Laboratorium' => $data['tunj_kepala_lab'],
                    'Kepala Perpustakaan' => $data['tunj_kepala_perpus'],
                    'Operator Sekolah' => $data['tunj_operator'],
                    'Pembina Keagamaan' => $data['tunj_pembina_keagamaan'],
                    'Pengelola SMATER' => $data['tunj_pengelola_smater'],
                    'Wali Kelas' => $data['tunj_walas'],
                    'Pembina Lainnya' => $data['tunj_pembina'],
                    'Tunjangan Lainnya' => $data['tunjangan_lain']
                ];
                $noT = 1;
                foreach($tunjList as $lbl => $val):
                    if($val > 0): ?>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp; <?= $lbl ?></td>
                        <td class="text-center small">-</td>
                        <td class="text-right">Rp <?= number_format($val, 0, ',', '.') ?></td>
                    </tr>
                <?php endif; endforeach; ?>

                <tr class="total-row">
                    <td colspan="2" class="text-right">Total Pendapatan Kotor</td>
                    <?php 
                    $totPendapatan = $data['subtotal_jjm'] + $data['subtotal_transport'] + $data['subtotal_kinerja'] + 
                                     $data['tunj_ekskul'] + 
                                     $data['tunj_kepsek'] + $data['tunj_tas'] + $data['tunj_plk'] + $data['tunj_penjaga'] + $data['tunj_satpam'] + $data['tunj_sopir'] +
                                     $data['tunj_kurikulum'] + $data['tunj_kesiswaan'] + $data['tunj_sarpras'] + $data['tunj_humas'] + 
                                     $data['tunj_kepala_lab'] + $data['tunj_kepala_perpus'] + $data['tunj_operator'] + $data['tunj_pembina_keagamaan'] + $data['tunj_pengelola_smater'] + $data['tunj_walas'] +
                                     $data['tunj_pembina'] + $data['tunjangan_lain']; 
                    ?>
                    <td class="text-right">Rp <?= number_format($totPendapatan, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <table class="rincian-table">
            <thead>
                <tr>
                    <th colspan="3">B. POTONGAN</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php if($data['potongan_bpjs_kes'] > 0): ?>
                <tr>
                    <td><?= $no++ ?>. BPJS Kesehatan</td>
                    <td class="text-center">-</td>
                    <td class="text-right">Rp <?= number_format($data['potongan_bpjs_kes'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if($data['potongan_bpjs_tk'] > 0): ?>
                <tr>
                    <td><?= $no++ ?>. BPJS Ketenagakerjaan</td>
                    <td class="text-center">-</td>
                    <td class="text-right">Rp <?= number_format($data['potongan_bpjs_tk'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if($data['potongan_kasbon'] > 0): ?>
                <tr>
                    <td><?= $no++ ?>. Kasbon / Pinjaman</td>
                    <td class="text-center">-</td>
                    <td class="text-right">Rp <?= number_format($data['potongan_kasbon'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>

                <?php if($data['potongan_lain'] > 0): ?>
                <tr>
                    <td><?= $no++ ?>. Lain-lain</td>
                    <td class="text-center">-</td>
                    <td class="text-right">Rp <?= number_format($data['potongan_lain'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                
                <tr class="total-row">
                    <td colspan="2" class="text-right">Total Potongan</td>
                    <?php $totPotongan = $data['potongan_bpjs_kes'] + $data['potongan_bpjs_tk'] + $data['potongan_kasbon'] + $data['potongan_lain']; ?>
                    <td class="text-right">Rp <?= number_format($totPotongan, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <table class="rincian-table" style="border: 2px solid #000;">
            <tr style="background-color: #ddd;">
                <td class="text-center" style="font-size: 12pt; font-weight: bold;">TOTAL DITERIMA (TAKE HOME PAY)</td>
                <td class="text-right" style="font-size: 14pt; font-weight: bold;">Rp <?= number_format($data['total_diterima'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td colspan="2" style="font-style: italic;">Terbilang: # <?= isset($terbilang) ? $terbilang($data['total_diterima']) : ((function_exists('terbilang') ? terbilang($data['total_diterima']) : '')) ?> Rupiah #</td>
            </tr>
        </table>

        <div class="signature">
            <div class="sig-box">
                <p>Penerima,</p>
                <div class="name"><?= $data['nama_guru'] ?></div>
            </div>
            <div class="sig-box">
                <p>Bendahara,</p>
                <div class="name">Administrator</div>
            </div>
        </div>
    </div>
</body>
</html>
