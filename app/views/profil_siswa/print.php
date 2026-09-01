<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Biodata Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            background-color: #f0f0f0;
        }

        .page-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px solid #d3d3d3;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: center;
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        .label {
            width: 35%;
            font-weight: bold;
        }

        .sub-header {
            background: #e0e0e0;
            padding: 5px;
            font-weight: bold;
            border: 1px solid #000;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .foto-siswa {
            width: 3cm;
            height: 4cm;
            object-fit: cover;
            border: 1px solid #999;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: white;
            }

            .page-container {
                border: none;
                width: 100%;
                margin: 0;
                box-shadow: none;
                padding: 0;
            }

            .no-print { display: none; }

            .sub-header {
                -webkit-print-color-adjust: exact;
                background: #e0e0e0 !important;
            }

            .btn-container { display: none; }
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 0 5px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print { background-color: #28a745; }
        .btn-print:hover { background-color: #218838; }
        .btn-close { background-color: #dc3545; }
        .btn-close:hover { background-color: #c82333; }

        .btn-container {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #eee;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="btn-container no-print">
        <button onclick="window.print()" class="btn btn-print">Cetak</button>
        <button onclick="parent.$('#filePreviewModal').modal('hide'); window.close();" class="btn btn-close">Tutup</button>
    </div>

    <div class="page-container">
        <!-- KOP SURAT DINAMIS STANDAR UNIVERSAL -->
        <div class="header">
            <?php include __DIR__ . '/../partials/kop_surat_universal.php'; ?>
            <div style="margin-top:4px;">
                <h2 style="font-size:16pt; margin:0; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">BIODATA SISWA</h2>
            </div>
        </div>

        <!-- A. DATA PRIBADI dengan foto di pojok kanan -->
        <div class="sub-header">A. DATA PRIBADI SISWA</div>
        <table>
            <tr>
                <td style="vertical-align:top;">
                    <table style="margin-bottom:0; width:100%;">
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td>: <?= htmlspecialchars($siswa['nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="label">NISN</td>
                            <td>: <?= htmlspecialchars($siswa['nisn']) ?></td>
                        </tr>
                        <tr>
                            <td class="label">NIPD</td>
                            <td>: <?= htmlspecialchars($siswa['nipd'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">NIK</td>
                            <td>: <?= htmlspecialchars($siswa['nik'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Tempat/Tanggal Lahir</td>
                            <td>: <?= htmlspecialchars($siswa['tempat_lahir'] ?? '') ?>, <?= htmlspecialchars($siswa['tanggal_lahir'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td>: <?= htmlspecialchars($siswa['jk'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Sekolah Asal</td>
                            <td>: <?= htmlspecialchars($siswa['sekolah_asal'] ?? '-') ?></td>
                        </tr>
                    </table>
                </td>
                <!-- Foto Siswa -->
                <td style="width:3.5cm; text-align:center; vertical-align:top; padding-left:12px;">
                    <img src="<?= $avatar_src ?>" class="foto-siswa" alt="Foto Siswa">
                    <div style="font-size:9pt; margin-top:3px; color:#555;">Foto Siswa</div>
                </td>
            </tr>
        </table>

        <!-- B. DATA ORANG TUA / WALI -->
        <div class="sub-header">B. DATA ORANG TUA / WALI</div>
        <table>
            <tr>
                <td class="label">Nama Ayah</td>
                <td>: <?= htmlspecialchars($profil['nama_ayah'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Pekerjaan Ayah</td>
                <td>: <?= htmlspecialchars($profil['pekerjaan_ayah'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">No. Telp Ayah</td>
                <td>: <?= htmlspecialchars($profil['telp_ayah'] ?? '-') ?></td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr>
                <td class="label">Nama Ibu</td>
                <td>: <?= htmlspecialchars($profil['nama_ibu'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Pekerjaan Ibu</td>
                <td>: <?= htmlspecialchars($profil['pekerjaan_ibu'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">No. Telp Ibu</td>
                <td>: <?= htmlspecialchars($profil['telp_ibu'] ?? '-') ?></td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr>
                <td class="label">Nama Wali</td>
                <td>: <?= htmlspecialchars($profil['nama_wali'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Pekerjaan Wali</td>
                <td>: <?= htmlspecialchars($profil['pekerjaan_wali'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Alamat Wali</td>
                <td>: <?= nl2br(htmlspecialchars($profil['alamat_wali'] ?? '-')) ?></td>
            </tr>
        </table>

        <!-- C. KELENGKAPAN BERKAS -->
        <div class="sub-header">C. KELENGKAPAN BERKAS</div>
        <table>
            <?php
            $files = [
                'file_ijazah'         => 'Ijazah Terakhir',
                'file_kartu_keluarga' => 'Kartu Keluarga',
                'file_akte_lahir'     => 'Akte Kelahiran',
                'file_ktp_ortu'       => 'KTP Orang Tua',
                'file_kip'            => 'Kartu Indonesia Pintar',
            ];
            foreach ($files as $col => $label):
                $stat = !empty($profil[$col]) ? '✓ Ada / Terlampir' : '✗ Belum Ada';
            ?>
            <tr>
                <td class="label"><?= $label ?></td>
                <td>: <?= $stat ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div style="margin-top:50px; text-align:right; width:90%;">
            <p>Sukabumi, <?= date('d F Y') ?></p>
            <br><br><br>
            <p>( <?= htmlspecialchars($siswa['nama']) ?> )</p>
        </div>
    </div>
</body>

</html>