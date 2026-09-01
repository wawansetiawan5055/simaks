<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Siswa Mapel</title>
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
<h2>Laporan Absensi Siswa Mapel</h2>
<?php if ($jenis_laporan == 'harian'): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">NIPD</th>
                <th width="15%">NISN</th>
                <th>Nama Siswa</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Group logic for dates (duplicated from view)
            $grouped_dates = [];
            foreach ($data as $d) {
                // Determine structure: 
                // Checks if input is from $list variable (export function) or $data (view function)
                // Export helper usually passes $list. Let's adapt.
                $item = is_array($d) ? $d : []; 
                if(!empty($item['tanggal'])) {
                   $grouped_dates[$item['tanggal']][] = $item;
                }
            }
            // If $list passed directly from controller (which is flat array for harian)
            if(isset($rows) && !isset($data['dates'])) { 
                 // Handle specific structure passed by laporan_export_pdf_render
                 // Actually the controller for PDF export logic passes 'rows' which is indexed array.
                 // We should follow the standard PDF export helper or Custom View?
                 // The controller logic uses `laporan_export_pdf_render` which uses `laporan_export_pdf.php`.
                 // BUT `laporan_absensi_siswa_mapel_pdf.php` seems to be intended for custom PDF view if not using generic.
                 // Let's stick to the structure passed in `laporan_absensi_siswa_mapel_export_pdf` which passes $list
            }
            
            // Logic controller `laporan_absensi_siswa_mapel_export_pdf` passes `['judul', 'kolom', 'rows' ...]` to `laporan_export_pdf_render`.
            // Wait, the controller calls `laporan_export_pdf_render` which uses `laporan_export_pdf.php`.
            // The file `laporan_absensi_siswa_mapel_pdf.php` is NOT used by the current controller logic for PDF export!
            // The controller uses the GENERIC `laporan_export_pdf_render`.
            
            // IF we want to customize the PDF to support Matrix (Bulanan/Semester), 
            // we must change the Controller to load THIS view instead of using the generic renderer.
            // OR we update the generic renderer? No, better use custom view.
            
            // However, the current task is to update `laporan_absensi_siswa_mapel_pdf.php`. 
            // Let's assume the controller MIGHT use this in future or we are preparing it. 
            // BUT, looking at the Controller, it calls `laporan_export_pdf_render` with `$rows`.
            // It does NOT use this specific file `laporan_absensi_siswa_mapel_pdf.php`.
            
            // TO FIX THIS PROPERLY:
            // 1. I need to modify `LaporanController.php` `laporan_absensi_siswa_mapel_export_pdf` 
            //    to extract data similar to `laporan_absensi_siswa_mapel` (matrix) if Bulanan/Semester.
            //    AND load THIS view `laporan_absensi_siswa_mapel_pdf.php` directly using DomPDF.
            
            // FOR NOW, I will update this file assuming it receives `$data` (matrix) and `$jenis_laporan`.
            ?>
            <!-- Fallback for Harian flat list -->
             <?php foreach ($list as $d): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $d['tanggal'] ?></td>
              <td><?= $d['nipd'] ?? '-' ?></td>
              <td><?= $d['nisn'] ?? '-' ?></td>
              <td><?= $d['nama'] ?></td>
              <td><?= $d['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($jenis_laporan == 'bulanan'): ?>
    <table border="1" cellpadding="3" cellspacing="0" style="font-size: 10px;">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Siswa</th>
                <th colspan="<?= count($data['dates']) ?>">Tanggal</th>
                <th colspan="4">Rekap</th>
                <th rowspan="2">%</th>
            </tr>
            <tr>
                <?php foreach ($data['dates'] as $dt): ?>
                    <th width="15"><?= date('d', strtotime($dt)) ?></th>
                <?php endforeach; ?>
                <th width="20">I</th>
                <th width="20">A</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($data['students'] as $s): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $s['nama'] ?></td>
                <?php foreach ($data['dates'] as $dt): 
                     $st = $s['attendance'][$dt] ?? '-';
                     $val = ($st != '-') ? strtoupper(substr($st, 0, 1)) : '';
                ?>
                    <td align="center"><?= $val ?></td>
                <?php endforeach; ?>
                <td align="center"><?= $s['summary']['H'] ?></td>
                <td align="center"><?= $s['summary']['S'] ?></td>
                <td align="center"><?= $s['summary']['I'] ?></td>
                <td align="center"><?= $s['summary']['A'] ?></td>
                <?php 
                    $total = $s['summary']['H'] + $s['summary']['S'] + $s['summary']['I'] + $s['summary']['A'];
                    $persen = ($total > 0) ? round(($s['summary']['H'] / $total) * 100, 1) : 0;
                ?>
                <td align="center"><b><?= $persen ?>%</b></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($jenis_laporan == 'semester'): ?>
    <table border="1" cellpadding="3" cellspacing="0" style="font-size: 10px;">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Siswa</th>
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
                <td><?= $no++ ?></td>
                <td><?= $s['nama'] ?></td>
                <?php foreach ($data['months'] as $m):
                    $md = $s['months'][$m] ?? ['H'=>0,'S'=>0,'I'=>0,'A'=>0];
                ?>
                    <td align="center"><?= $md['H'] ?: '-' ?></td>
                    <td align="center"><?= $md['S'] ?: '-' ?></td>
                    <td align="center"><?= $md['I'] ?: '-' ?></td>
                    <td align="center"><?= $md['A'] ?: '-' ?></td>
                <?php endforeach; ?>
                <td align="center" style="background-color:#eee;"><?= $s['total']['H'] ?></td>
                <td align="center" style="background-color:#eee;"><?= $s['total']['S'] ?></td>
                <td align="center" style="background-color:#eee;"><?= $s['total']['I'] ?></td>
                <td align="center" style="background-color:#eee;"><?= $s['total']['A'] ?></td>
                 <?php 
                    $total = $s['total']['H'] + $s['total']['S'] + $s['total']['I'] + $s['total']['A'];
                    $persen = ($total > 0) ? round(($s['total']['H'] / $total) * 100, 1) : 0;
                ?>
                <td align="center"><b><?= $persen ?>%</b></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>