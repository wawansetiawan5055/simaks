<?php
/**
 * Standardized Kop Surat for Reports (Delegates to Universal Kop Component)
 */
if (isset($kop) && is_array($kop)) {
    $profil_sekolah_kop = $kop;
    if (empty($profil_sekolah_kop['nama_sekolah']) && !empty($kop['kop_nama'])) {
        $profil_sekolah_kop['nama_sekolah'] = $kop['kop_nama'];
    }
}
include __DIR__ . '/kop_surat_universal.php';