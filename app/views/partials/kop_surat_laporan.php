<?php
/**
 * Standardized Kop Surat for Reports (Delegates to Universal Kop Component)
 */
if (isset($kop) && is_array($kop)) {
    $profil_sekolah_kop = [
        'nama_yayasan' => $kop['nama_yayasan'] ?? '',
        'nama_sekolah' => $kop['kop_nama'] ?? '',
        'npsn'         => $kop['kop_npsn'] ?? '',
        'alamat'       => $kop['kop_alamat'] ?? '',
        'logo'         => $kop['logo'] ?? '',
    ];
}
include __DIR__ . '/kop_surat_universal.php';