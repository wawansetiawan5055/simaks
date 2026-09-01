<?php
/**
 * Universal Kop Surat Component for SIMAKS
 * Supports: Standar Nasional, Provinsi, Kabupaten/Kota, Yayasan/Swasta, and Kustom.
 * Clean, compact line-spacing with dual logos (Kiri & Kanan) and standard government/foundation border.
 */

if (!isset($profil_sekolah_kop) || empty($profil_sekolah_kop['nama_sekolah'])) {
    if (isset($profil) && is_array($profil) && !empty($profil['nama_sekolah'])) {
        $profil_sekolah_kop = $profil;
    } elseif (isset($sekolah) && is_array($sekolah) && !empty($sekolah['nama_sekolah'])) {
        $profil_sekolah_kop = $sekolah;
    } elseif (isset($kop) && is_array($kop) && !empty($kop['nama_sekolah'])) {
        $profil_sekolah_kop = $kop;
    } else {
        global $pdo;
        if (isset($pdo)) {
            require_once __DIR__ . '/../../models/ProfilSekolahModel.php';
            $profil_sekolah_kop = ProfilSekolahModel::getProfil($pdo);
        } else {
            $profil_sekolah_kop = [];
        }
    }
}

// Extract Kop Settings with safe fallbacks
$model_kop       = $profil_sekolah_kop['model_kop'] ?? 'yayasan';
$nama_yayasan    = $profil_sekolah_kop['nama_yayasan'] ?? '';
$nama_sekolah    = $profil_sekolah_kop['nama_sekolah'] ?? 'NAMA SEKOLAH';
$npsn            = $profil_sekolah_kop['npsn'] ?? '';
$akreditasi      = $profil_sekolah_kop['sk_akreditasi'] ?? '';
$alamat          = $profil_sekolah_kop['alamat'] ?? '';
$telepon         = $profil_sekolah_kop['telepon'] ?? '';
$email           = $profil_sekolah_kop['email'] ?? '';
$website         = $profil_sekolah_kop['website'] ?? '';

// Baris Teks Kop
$baris_1 = !empty($profil_sekolah_kop['kop_baris_1']) ? $profil_sekolah_kop['kop_baris_1'] : ($nama_yayasan ?: 'YAYASAN TARBIYATUSSHIBYAN INDONESIA');
$baris_2 = !empty($profil_sekolah_kop['kop_baris_2']) ? $profil_sekolah_kop['kop_baris_2'] : '';
$baris_3 = !empty($profil_sekolah_kop['kop_baris_3']) ? $profil_sekolah_kop['kop_baris_3'] : $nama_sekolah;
$baris_4 = !empty($profil_sekolah_kop['kop_baris_4']) ? $profil_sekolah_kop['kop_baris_4'] : ('NPSN: ' . $npsn . ($akreditasi ? ' | AKREDITASI: ' . $akreditasi : ''));
$baris_5 = !empty($profil_sekolah_kop['kop_baris_5']) ? $profil_sekolah_kop['kop_baris_5'] : ($alamat . ($telepon ? ' Telp: ' . $telepon : '') . ($email ? ' Email: ' . $email : ''));

// Logos & Visibility
$show_kiri  = isset($profil_sekolah_kop['show_logo_kiri']) ? (bool)$profil_sekolah_kop['show_logo_kiri'] : true;
$show_kanan = isset($profil_sekolah_kop['show_logo_kanan']) ? (bool)$profil_sekolah_kop['show_logo_kanan'] : true;

$logo_utama = $profil_sekolah_kop['logo'] ?? '';
$logo_kiri  = !empty($profil_sekolah_kop['logo_kiri']) ? $profil_sekolah_kop['logo_kiri'] : $logo_utama;
$logo_kanan = !empty($profil_sekolah_kop['logo_kanan']) ? $profil_sekolah_kop['logo_kanan'] : $logo_utama;

// Resolve Image Paths (Automatic Base64 embedding for 100% compatibility with Dompdf & Browser Prints)
$path_base = defined('BASE_URL') ? BASE_URL : '';
$resolve_kop_img = function($logo_file) use ($path_base) {
    if (empty($logo_file)) return '';
    $possible_paths = [
        __DIR__ . '/../../../public/assets/img/' . $logo_file,
        __DIR__ . '/../../../public/' . $logo_file,
        __DIR__ . '/../../../public/uploads/' . $logo_file
    ];
    foreach ($possible_paths as $p) {
        if (file_exists($p) && is_file($p)) {
            $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            $mime = ($ext === 'svg') ? 'image/svg+xml' : (($ext === 'png') ? 'image/png' : 'image/jpeg');
            $data = @file_get_contents($p);
            if ($data !== false && strlen($data) > 0) {
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }
    }
    return $path_base . 'assets/img/' . $logo_file;
};

$src_kiri  = !empty($logo_kiri) ? $resolve_kop_img($logo_kiri) : '';
$src_kanan = !empty($logo_kanan) ? $resolve_kop_img($logo_kanan) : '';

$style_garis = $profil_sekolah_kop['style_garis'] ?? 'double';
?>

<style>
    .simaks-kop-container {
        width: 100%;
        margin-bottom: 12px;
        font-family: 'Times New Roman', Times, serif, Arial, sans-serif;
        color: #000000;
        background: transparent;
    }
    .simaks-kop-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        margin: 0;
        padding: 0;
    }
    .simaks-kop-table td {
        border: none;
        padding: 0;
        vertical-align: middle;
    }
    .simaks-kop-logo-left {
        width: 95px;
        text-align: left;
        padding-right: 10px;
    }
    .simaks-kop-logo-right {
        width: 95px;
        text-align: right;
        padding-left: 10px;
    }
    .simaks-kop-logo-img {
        max-width: 85px;
        max-height: 85px;
        height: auto;
        object-fit: contain;
        display: inline-block;
    }
    .simaks-kop-center {
        text-align: center;
        line-height: 1.15;
    }
    .simaks-kop-b1 {
        font-size: 11.5pt;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0 0 1px 0;
        letter-spacing: 0.5px;
        line-height: 1.15;
    }
    .simaks-kop-b2 {
        font-size: 11pt;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0 0 1px 0;
        letter-spacing: 0.3px;
        line-height: 1.15;
    }
    .simaks-kop-b3 {
        font-size: 15pt;
        font-weight: 900;
        text-transform: uppercase;
        margin: 1px 0 2px 0;
        letter-spacing: 1px;
        line-height: 1.15;
    }
    .simaks-kop-b4 {
        font-size: 9pt;
        font-weight: bold;
        margin: 0 0 1px 0;
        line-height: 1.15;
    }
    .simaks-kop-b5 {
        font-size: 8.5pt;
        font-weight: normal;
        margin: 0;
        line-height: 1.15;
    }
    .simaks-kop-divider-double {
        border-top: 3px double #000000;
        margin-top: 6px;
        margin-bottom: 12px;
        height: 0;
    }
    .simaks-kop-divider-single {
        border-top: 1.5px solid #000000;
        margin-top: 6px;
        margin-bottom: 12px;
        height: 0;
    }
    .simaks-kop-divider-thick {
        border-top: 2.5px solid #000000;
        margin-top: 6px;
        margin-bottom: 12px;
        height: 0;
    }

    @media print {
        .simaks-kop-container {
            margin-bottom: 8px;
        }
        .simaks-kop-logo-img {
            max-width: 80px;
            max-height: 80px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .simaks-kop-divider-double,
        .simaks-kop-divider-single,
        .simaks-kop-divider-thick {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="simaks-kop-container">
    <table class="simaks-kop-table">
        <tr>
            <!-- Logo Kiri -->
            <?php if ($show_kiri && !empty($src_kiri)): ?>
                <td class="simaks-kop-logo-left">
                    <img src="<?= $src_kiri ?>" alt="Logo Instansi" class="simaks-kop-logo-img" onerror="this.style.display='none'">
                </td>
            <?php else: ?>
                <td style="width: 15px;"></td>
            <?php endif; ?>

            <!-- Teks Kop Tengah (Rapat Line Spacing 1.15) -->
            <td class="simaks-kop-center">
                <?php if (!empty($baris_1)): ?>
                    <div class="simaks-kop-b1"><?= htmlspecialchars($baris_1) ?></div>
                <?php endif; ?>

                <?php if (!empty($baris_2)): ?>
                    <div class="simaks-kop-b2"><?= htmlspecialchars($baris_2) ?></div>
                <?php endif; ?>

                <?php if (!empty($baris_3)): ?>
                    <div class="simaks-kop-b3"><?= htmlspecialchars($baris_3) ?></div>
                <?php endif; ?>

                <?php if (!empty($baris_4)): ?>
                    <div class="simaks-kop-b4"><?= htmlspecialchars($baris_4) ?></div>
                <?php endif; ?>

                <?php if (!empty($baris_5)): ?>
                    <div class="simaks-kop-b5"><?= htmlspecialchars($baris_5) ?></div>
                <?php endif; ?>
            </td>

            <!-- Logo Kanan -->
            <?php if ($show_kanan && !empty($src_kanan)): ?>
                <td class="simaks-kop-logo-right">
                    <img src="<?= $src_kanan ?>" alt="Logo Sekolah" class="simaks-kop-logo-img" onerror="this.style.display='none'">
                </td>
            <?php else: ?>
                <td style="width: 15px;"></td>
            <?php endif; ?>
        </tr>
    </table>

    <!-- Garis Pembatas Kop -->
    <?php if ($style_garis === 'double'): ?>
        <div class="simaks-kop-divider-double"></div>
    <?php elseif ($style_garis === 'thick'): ?>
        <div class="simaks-kop-divider-thick"></div>
    <?php elseif ($style_garis === 'single'): ?>
        <div class="simaks-kop-divider-single"></div>
    <?php endif; ?>
</div>
