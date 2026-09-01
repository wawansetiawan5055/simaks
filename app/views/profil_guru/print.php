<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Profil Guru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 12pt;
            color: #000;
            background-color: #f0f0f0;
            /* Default screen bg */
        }

        /* Paper Container */
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
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14pt;
        }

        .photo {
            width: 3cm;
            height: 4cm;
            background: #eee;
            border: 1px solid #000;
            position: absolute;
            top: 0;
            right: 0;
            object-fit: cover;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            background: #e0e0e0;
            padding: 5px 10px;
            border: 1px solid #000;
            margin: 15px 0 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table td {
            padding: 4px;
            vertical-align: top;
        }

        .label {
            width: 35%;
            font-weight: bold;
        }

        .value {
            width: 65%;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }

        /* Button Styling */
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

        .btn-print {
            background-color: #28a745;
        }

        .btn-print:hover {
            background-color: #218838;
        }

        .btn-close {
            background-color: #dc3545;
        }

        .btn-close:hover {
            background-color: #c82333;
        }

        .btn-container {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #eee;
            border-radius: 5px;
        }

        @page {
            size: A4;
            margin: 10mm;
            /* Printer margin */
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: white;
            }

            .page-container {
                width: 100%;
                margin: 0;
                padding: 0;
                /* Let @page margin handle it or set specific padding */
                border: none;
                border-radius: 0;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            .section-title {
                background-color: #ddd !important;
                -webkit-print-color-adjust: exact;
            }

            .btn-container {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="btn-container no-print">
        <button onclick="window.print()" class="btn btn-print"><i class="fa fa-print"></i> Cetak </button>
        <button onclick="parent.$('#modalPreview').modal('hide'); window.close();" class="btn btn-close">Tutup</button>
    </div>

    <div class="page-container">
        <!-- HEADER STANDARD -->
        <div class="header">
            <?php
            // Prepare Data for Partial
            $kop = [
                'logo' => get_app_logo() ?? 'logo.png', // Fallback if not set
                'kop_nama' => 'SMA PLUS AL MANSHURIYAH', // Hardcoded as per original file or need dynamic? Original was hardcoded.
                'kop_npsn' => '20247166', // Hardcoded in original
                'kop_alamat' => 'Jl. Kalaparea KM. 5 RT 03 RW 09 Desa Kalaparea Kec. Nagrak Kab. Sukabumi' // Hardcoded based on image/context
            ];
            // Wait, the partial expects $kop variable. 
            // In profil_guru, $profil_sekolah might be available?
            // Let's check controller. ProfilGuruController?
            // The partial uses $kop['kop_nama'] etc. 
            // I should ideally pass $profil_sekolah data if available.
            // But looking at the file context, it used hardcoded text "SMA Plus Al Manshuriyah".
            // To be safe and "Standardized", I should use the partial.
            // I will inject the partial include. 
            // But I need to ensure $kop is populated.
            
            // Re-reading file: it has no PHP logic for generic school profile.
            // It just says <p>SMA Plus Al Manshuriyah</p>.
            // I will construct $kop manually here to match the standard.
            $kop = [
                'logo' => 'logo.png', // Default
                'kop_nama' => 'SMA PLUS AL MANSHURIYAH',
                'kop_npsn' => '20247166',
                'kop_alamat' => 'Jl. Kalaparea KM. 5 RT 03 RW 09 Desa Kalaparea Kec. Nagrak Kab. Sukabumi'
            ];
            // Better: Check if we can get it from database? 
            // The view has access to $pdo via controller usually?
            // Actually, let's look at `profil_guru/print.php` again. It has `$guru` and `$profil` (guru profile).
            // It does NOT seem to have school profile data passed to it.
            // I will use sensible defaults matching the other reports.
            ?>
            <?php include __DIR__ . '/../partials/kop_surat_laporan.php'; ?>
        </div>

        <div style="position:relative;">
            <img src="../public/assets/img/avatar.png" class="photo" alt="Foto Guru"
                onerror="this.style.display='none'">

            <table style="width: 70%;">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td>: <?= htmlspecialchars($guru['nama']) ?></td>
                </tr>
                <tr>
                    <td class="label">NIP / NUPTK</td>
                    <td>: <?= htmlspecialchars($guru['nuptk'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td>: <?= htmlspecialchars($guru['nik'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tanggal Lahir</td>
                    <td>: <?= htmlspecialchars($guru['tempat_lahir'] ?? '-') ?>,
                        <?= htmlspecialchars($guru['tanggal_lahir'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td>: <?= htmlspecialchars($guru['jk'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Status Kepegawaian</td>
                    <td>: <?= htmlspecialchars($guru['status_kepegawaian'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <div class="section-title">A. DATA PRIBADI & KONTAK</div>
        <table>
            <tr>
                <td class="label">Gelar Depan</td>
                <td class="value">: <?= htmlspecialchars($profil['gelar_depan'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Gelar Belakang</td>
                <td class="value">: <?= htmlspecialchars($profil['gelar_belakang'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Pendidikan Terakhir</td>
                <td class="value">: <?= htmlspecialchars($profil['pendidikan_terakhir'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td class="value">: <?= nl2br(htmlspecialchars($profil['alamat_lengkap'] ?? '-')) ?></td>
            </tr>
            <tr>
                <td class="label">No. Handphone</td>
                <td class="value">: <?= htmlspecialchars($profil['no_hp'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Email Pribadi</td>
                <td class="value">: <?= htmlspecialchars($profil['email_pribadi'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Nama Ibu Kandung</td>
                <td class="value">: <?= htmlspecialchars($profil['nama_ibu_kandung'] ?? '-') ?></td>
            </tr>
        </table>

        <div class="section-title">B. KELENGKAPAN BERKAS</div>
        <table>
            <?php
            $files = [
                'file_ijazah_s1' => 'Ijazah S1 / Terakhir',
                'file_serdik' => 'Sertifikat Pendidik',
                'file_kk' => 'Kartu Keluarga',
                'file_ktp' => 'KTP',
                'file_akte' => 'Akte Kelahiran',
                'file_npwp' => 'NPWP'
            ];
            foreach ($files as $col => $label):
                $is_uploaded = !empty($profil[$col]);
                ?>
                <tr>
                    <td class="label"><?= $label ?></td>
                    <td class="value">: <?= $is_uploaded ? '[ v ] Terlampir' : '[ - ] Belum ada' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="footer">
            <p><?= htmlspecialchars($sekolah['kota'] ?? 'Sukabumi') ?>, <?= tgl_indo() ?></p>
            <br><br><br>
            <p><b><?= htmlspecialchars($guru['nama']) ?></b></p>
        </div>
    </div>

</body>

</html>