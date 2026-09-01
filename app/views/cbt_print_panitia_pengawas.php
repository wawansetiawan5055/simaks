<?php
// app/views/cbt_print_panitia_pengawas.php
// Dokumen Cetak Kepanitiaan Ujian CBT (Kartu Panitia, Kartu Pengawas, Daftar Hadir Pengawas/Panitia, Tata Tertib, Label Meja)

require_once __DIR__ . '/../models/ProfilSekolahModel.php';
$profil = ProfilSekolahModel::getProfil($pdo);

$mode = $_GET['act'] ?? $_GET['doc'] ?? 'tata_tertib';
$nama_ujian = $_GET['nama_ujian'] ?? 'PENILAIAN AKHIR SEMESTER (SAS)';
$tahun_ajaran = $_SESSION['nama_ta_aktif'] ?? date('Y') . '/' . (date('Y') + 1);
$tanggal_ujian = $_GET['tanggal'] ?? date('d F Y');
$ruang = $_GET['ruang'] ?? 'Ruang 01';
$sesi = $_GET['sesi'] ?? 'Sesi 1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Administrasi Ujian - <?= htmlspecialchars($nama_ujian) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Arial', sans-serif; }
        body { background-color: #f1f5f9; color: #0f172a; padding: 20px; }
        .page { max-width: 210mm; margin: 0 auto 20px auto; background: #fff; padding: 15mm; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-radius: 4px; }
        
        .no-print-bar {
            max-width: 210mm; margin: 0 auto 15px auto; display: flex; justify-content: space-between; align-items: center;
            background: #1e293b; color: #fff; padding: 10px 18px; border-radius: 8px; font-size: 0.88rem;
        }
        .btn-print { background: #3b82f6; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print:hover { background: #2563eb; }

        .kop-surat { display: flex; align-items: center; border-bottom: 2.5px double #000; padding-bottom: 10px; margin-bottom: 16px; }
        .kop-logo { width: 68px; height: 68px; object-fit: contain; margin-right: 15px; }
        .kop-text { text-align: center; flex-grow: 1; }
        .kop-text h2 { font-size: 1.15rem; font-weight: 800; text-transform: uppercase; }
        .kop-text h3 { font-size: 0.95rem; font-weight: 700; }
        .kop-text p { font-size: 0.76rem; color: #334155; }

        .doc-title { text-align: center; margin: 12px 0 16px 0; }
        .doc-title h3 { font-size: 1.05rem; font-weight: 800; text-transform: uppercase; text-decoration: underline; }
        .doc-title p { font-size: 0.85rem; font-weight: 600; margin-top: 3px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.82rem; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 7px 9px; }
        table.data-table th { background: #f1f5f9; text-align: center; font-weight: 700; text-transform: uppercase; }

        .ttd-box { display: flex; justify-content: space-between; margin-top: 35px; font-size: 0.84rem; page-break-inside: avoid; }
        .ttd-col { text-align: center; width: 200px; }

        /* KARTU GRID (4 KARTU PER HALAMAN) */
        .card-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .id-card {
            border: 2px solid #1e293b; border-radius: 8px; padding: 12px; background: #fff;
            position: relative; overflow: hidden; page-break-inside: avoid;
        }
        .id-card-header {
            display: flex; align-items: center; border-bottom: 1.5px solid #1e293b; padding-bottom: 8px; margin-bottom: 10px;
        }
        .id-card-header img { width: 42px; height: 42px; object-fit: contain; margin-right: 10px; }
        .id-card-header .title { font-size: 0.74rem; font-weight: 800; text-transform: uppercase; line-height: 1.2; }
        .id-card-body { text-align: center; padding: 10px 0; }
        .id-card-name { font-size: 1.05rem; font-weight: 800; margin-bottom: 4px; }
        .id-card-badge {
            display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
        }
        .badge-pengawas { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        .badge-panitia { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .id-card-footer { font-size: 0.70rem; color: #475569; text-align: center; border-top: 1px dashed #cbd5e1; padding-top: 6px; }

        /* LABEL MEJA */
        .label-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .label-box {
            border: 2px dashed #0f172a; border-radius: 10px; padding: 14px; text-align: center;
            background: #fff; page-break-inside: avoid;
        }
        .label-box .seat-no { font-size: 2.2rem; font-weight: 900; color: #1e3a8a; }
        .label-box .student-name { font-size: 1rem; font-weight: 800; margin: 4px 0; }
        .label-box .nisn { font-size: 0.82rem; color: #475569; font-family: monospace; }

        @media print {
            body { background: transparent; padding: 0; }
            .no-print-bar { display: none !important; }
            .page { box-shadow: none; padding: 0; margin: 0; max-width: 100%; }
            @page { size: A4 portrait; margin: 12mm; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div>
        <i class="fas fa-print mr-2"></i> <strong>Mode Cetak Dokumen Administrasi Ujian</strong>
        <span class="ml-2 text-muted">| <?= htmlspecialchars($profil['nama_sekolah'] ?? 'SIMAKS') ?></span>
    </div>
    <div style="display: flex; gap: 8px;">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Dokumen (Ctrl+P)
        </button>
        <button class="btn-print" style="background: #475569;" onclick="window.close()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
</div>

<div class="page">
    
    <!-- KOP SURAT RESMI -->
    <div class="kop-surat">
        <?php $logoUrl = !empty($profil['logo']) ? BASE_URL . 'assets/img/' . $profil['logo'] : BASE_URL . 'assets/img/logo.png'; ?>
        <img src="<?= $logoUrl ?>" alt="Logo Sekolah" class="kop-logo" onerror="this.src='https://placehold.co/80x80?text=LOGO'">
        <div class="kop-text">
            <h2><?= htmlspecialchars($profil['nama_yayasan'] ?? 'YAYASAN PENDIDIKAN') ?></h2>
            <h2><?= htmlspecialchars($profil['nama_sekolah'] ?? 'SEKOLAH INDONESIA') ?></h2>
            <p><?= htmlspecialchars($profil['alamat'] ?? 'Jl. Pendidikan No. 1') ?> | Telp: <?= htmlspecialchars($profil['telepon'] ?? '-') ?> | Website: <?= htmlspecialchars($profil['website'] ?? '-') ?></p>
        </div>
    </div>

    <!-- 1. TATA TERTIB PESERTA & PENGAWAS -->
    <?php if ($mode === 'print_tata_tertib' || $mode === 'tata_tertib'): ?>
        <div class="doc-title">
            <h3>TATA TERTIB PESERTA UJIAN</h3>
            <p><?= htmlspecialchars($nama_ujian) ?> TAHUN AJARAN <?= htmlspecialchars($tahun_ajaran) ?></p>
        </div>

        <div style="font-size: 0.83rem; line-height: 1.6; margin-bottom: 20px;">
            <ol style="padding-left: 20px;">
                <li>Peserta memasuki ruangan setelah tanda masuk dibunyikan, yakni 15 (lima belas) menit sebelum ujian dimulai.</li>
                <li>Peserta dilarang membawa catatan, buku, atau perangkat komunikasi elektronik lain selain gawai/komputer yang telah ditentukan.</li>
                <li>Peserta wajib membawa <strong>Kartu Peserta Ujian</strong> dan meletakkannya di sudut kiri atas meja masing-masing.</li>
                <li>Peserta mengisi daftar hadir dengan menggunakan pulpen yang telah disediakan.</li>
                <li>Peserta login ke aplikasi CBT menggunakan <strong>Username</strong> dan <strong>Password</strong> yang tertera pada Kartu Peserta.</li>
                <li>Peserta yang memerlukan penjelasan materi soal dilarang bertanya ke sesama peserta, melainkan bertanya kepada pengawas ruang dengan mengacungkan tangan.</li>
                <li>Peserta yang telah selesai mengerjakan sebelum waktu habis dapat meninggalkan ruangan dengan tertib setelah menekan tombol <strong>Selesai Ujian</strong> dan melapor ke Pengawas Ruang.</li>
                <li>Peserta dilarang membuka tab lain, melakukan browsing ilegal, atau meminjamkan akun ujian kepada orang lain. Pelanggaran akan terekam oleh sistem pengawas digital (Anti-Cheat).</li>
            </ol>
        </div>

        <div class="doc-title" style="margin-top: 25px;">
            <h3>TATA TERTIB PENGAWAS RUANG UJIAN</h3>
        </div>
        <div style="font-size: 0.83rem; line-height: 1.6;">
            <ol style="padding-left: 20px;">
                <li>Pengawas ruang hadir di ruang sekretariat panitia 30 menit sebelum ujian dimulai untuk menerima pengarahan dan berkas.</li>
                <li>Pengawas memeriksa kelayakan ruang ujian, kebersihan, dan nomor meja peserta.</li>
                <li>Pengawas mempersilakan peserta memasuki ruangan 15 menit sebelum ujian dimulai.</li>
                <li>Pengawas memeriksa kesesuaian antara <strong>Kartu Peserta</strong> dengan identitas fisik peserta.</li>
                <li>Pengawas membacakan petunjuk pengerjaan dan membagikan token ujian jika menggunakan sistem token.</li>
                <li>Pengawas mengedarkan <strong>Daftar Hadir</strong> dan mengisi <strong>Berita Acara Pelaksanaan Ujian</strong> secara jujur dan cermat.</li>
                <li>Pengawas mengawasi ketertiban ruang dan melaporkan ke Proktor jika terdapat kendala teknis pada gawai peserta.</li>
            </ol>
        </div>

    <!-- 2. DAFTAR HADIR PENGAWAS RUANG -->
    <?php elseif ($mode === 'print_hadir_pengawas' || $mode === 'hadir_pengawas'): ?>
        <div class="doc-title">
            <h3>DAFTAR HADIR PENGAWAS RUANG UJIAN</h3>
            <p><?= htmlspecialchars($nama_ujian) ?> TAHUN AJARAN <?= htmlspecialchars($tahun_ajaran) ?></p>
        </div>

        <table style="width: 100%; font-size: 0.83rem; margin-bottom: 12px;">
            <tr>
                <td style="width: 120px; font-weight: bold;">Hari, Tanggal</td>
                <td style="width: 10px;">:</td>
                <td><?= htmlspecialchars($tanggal_ujian) ?></td>
                <td style="width: 100px; font-weight: bold;">Ruang / Sesi</td>
                <td style="width: 10px;">:</td>
                <td><?= htmlspecialchars($ruang) ?> / <?= htmlspecialchars($sesi) ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 180px;">Nama Pengawas</th>
                    <th style="width: 130px;">NIP / NUPTK</th>
                    <th>Asal Satuan Pendidikan</th>
                    <th style="width: 100px;">Jam Hadir</th>
                    <th style="width: 120px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                <tr>
                    <td style="text-align: center; height: 38px;"><?= $i ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 0.75rem; vertical-align: top;"><?= $i ?>.</td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

    <!-- 3. DAFTAR HADIR PANITIA PELAKSANA -->
    <?php elseif ($mode === 'print_hadir_panitia' || $mode === 'hadir_panitia'): ?>
        <div class="doc-title">
            <h3>DAFTAR HADIR PANITIA PELAKSANA UJIAN</h3>
            <p><?= htmlspecialchars($nama_ujian) ?> TAHUN AJARAN <?= htmlspecialchars($tahun_ajaran) ?></p>
        </div>

        <table style="width: 100%; font-size: 0.83rem; margin-bottom: 12px;">
            <tr>
                <td style="width: 120px; font-weight: bold;">Hari, Tanggal</td>
                <td style="width: 10px;">:</td>
                <td><?= htmlspecialchars($tanggal_ujian) ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Panitia</th>
                    <th style="width: 140px;">Jabatan Kepanitiaan</th>
                    <th style="width: 100px;">Jam Datang</th>
                    <th style="width: 100px;">Jam Pulang</th>
                    <th style="width: 120px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $jabatan_panitia = ['Ketua Panitia', 'Sekretaris', 'Bendahara', 'Koordinator Proktor', 'Teknisi CBT', 'Seksi Konsumsi', 'Seksi Perlengkapan', 'Pengawas Umum'];
                foreach ($jabatan_panitia as $idx => $jbt): 
                ?>
                <tr>
                    <td style="text-align: center; height: 38px;"><?= $idx + 1 ?></td>
                    <td></td>
                    <td><strong><?= $jbt ?></strong></td>
                    <td></td>
                    <td></td>
                    <td style="font-size: 0.75rem; vertical-align: top;"><?= $idx + 1 ?>.</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <!-- 4. KARTU PENGAWAS / KARTU PANITIA -->
    <?php elseif ($mode === 'print_kartu_pengawas' || $mode === 'print_kartu_panitia' || $mode === 'kartu_pengawas' || $mode === 'kartu_panitia'): 
        $isPanitia = ($mode === 'print_kartu_panitia' || $mode === 'kartu_panitia');
        $badgeText = $isPanitia ? 'PANITIA UJIAN' : 'PENGAWAS RUANG';
        $badgeClass = $isPanitia ? 'badge-panitia' : 'badge-pengawas';
    ?>
        <div class="doc-title" style="margin-bottom: 20px;">
            <h3>TANDA PENGENAL <?= $badgeText ?></h3>
            <p><?= htmlspecialchars($nama_ujian) ?></p>
        </div>

        <div class="card-grid">
            <?php 
            $sample_cards = [
                ['nama' => 'Dadan Silahudin, S.Pd.', 'nip' => '198203122008011005', 'tugas' => $isPanitia ? 'Ketua Panitia' : 'Pengawas Ruang 01'],
                ['nama' => 'Euis Sobariah, M.Pd.', 'nip' => '198505142010012012', 'tugas' => $isPanitia ? 'Sekretaris' : 'Pengawas Ruang 02'],
                ['nama' => 'Awaludin Hardiana, S.T.', 'nip' => '198901202014021003', 'tugas' => $isPanitia ? 'Koordinator Proktor' : 'Pengawas Ruang 03'],
                ['nama' => 'Komariah, S.Ag.', 'nip' => '197911052006042008', 'tugas' => $isPanitia ? 'Bendahara' : 'Pengawas Ruang 04']
            ];
            foreach ($sample_cards as $c): 
            ?>
            <div class="id-card">
                <div class="id-card-header">
                    <img src="<?= $logoUrl ?>" alt="Logo" onerror="this.src='https://placehold.co/50x50?text=LOGO'">
                    <div class="title">
                        <div><?= htmlspecialchars($profil['nama_sekolah'] ?? 'SIMAKS') ?></div>
                        <small class="text-muted"><?= htmlspecialchars($nama_ujian) ?></small>
                    </div>
                </div>
                <div class="id-card-body">
                    <span class="id-card-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                    <div class="id-card-name"><?= htmlspecialchars($c['nama']) ?></div>
                    <div style="font-size: 0.80rem; color: #334155;">NIP. <?= htmlspecialchars($c['nip']) ?></div>
                    <div style="font-size: 0.82rem; font-weight: bold; color: #1e3a8a; margin-top: 4px;">
                        Tugas: <?= htmlspecialchars($c['tugas']) ?>
                    </div>
                </div>
                <div class="id-card-footer">
                    Tahun Ajaran <?= htmlspecialchars($tahun_ajaran) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <!-- 5. LABEL MEJA UJIAN PESERTA -->
    <?php elseif ($mode === 'print_label_meja' || $mode === 'label_meja'): ?>
        <div class="doc-title" style="margin-bottom: 20px;">
            <h3>KARTU / LABEL MEJA PESERTA UJIAN</h3>
            <p><?= htmlspecialchars($ruang) ?> - <?= htmlspecialchars($nama_ujian) ?></p>
        </div>

        <div class="label-grid">
            <?php for ($s = 1; $s <= 6; $s++): ?>
            <div class="label-box">
                <div style="font-size: 0.76rem; font-weight: 800; text-transform: uppercase; color: #475569;">
                    <?= htmlspecialchars($profil['nama_sekolah'] ?? 'SIMAKS') ?> | <?= htmlspecialchars($ruang) ?>
                </div>
                <div class="seat-no">MEJA <?= sprintf('%02d', $s) ?></div>
                <div class="student-name">Peserta Ujian <?= sprintf('%02d', $s) ?></div>
                <div class="nisn">NISN: 008765432<?= $s ?> | <?= htmlspecialchars($sesi) ?></div>
            </div>
            <?php endfor; ?>
        </div>

    <?php endif; ?>

    <!-- TANDA TANGAN KEPALA SEKOLAH & KETUA PANITIA -->
    <div class="ttd-box">
        <div class="ttd-col">
            <div>Mengetahui,</div>
            <div>Kepala Sekolah</div>
            <div style="margin-top: 55px; font-weight: bold; text-decoration: underline;">
                <?= htmlspecialchars($profil['nama_kepala_sekolah'] ?? 'Nama Kepala Sekolah, M.Pd.') ?>
            </div>
            <div>NIP. <?= htmlspecialchars($profil['nip_kepala_sekolah'] ?? '...........................') ?></div>
        </div>

        <div class="ttd-col">
            <div><?= htmlspecialchars($profil['kota'] ?? 'Sukabumi') ?>, <?= htmlspecialchars($tanggal_ujian) ?></div>
            <div>Ketua Panitia Ujian</div>
            <div style="margin-top: 55px; font-weight: bold; text-decoration: underline;">
                ..........................................
            </div>
            <div>NIP. ..........................................</div>
        </div>
    </div>

</div>

</body>
</html>
