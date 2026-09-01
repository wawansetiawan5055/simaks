<?php
class ProfilSekolahModel {
    /**
     * Mengambil data profil sekolah (selalu dari id=1)
     */
    public static function getProfil($pdo) {
        return $pdo->query("SELECT * FROM profil_sekolah WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil data bentuk pendidikan saja.
     */
    public static function getBentukPendidikan($pdo) {
        $stmt = $pdo->query("SELECT bentuk_pendidikan FROM profil_sekolah WHERE id = 1");
        return $stmt->fetchColumn(); 
    }

    /**
     * Menyimpan atau memperbarui data profil sekolah beserta konfigurasi kop surat
     */
    public static function save($pdo, $data) {
        $profil_lama = self::getProfil($pdo);

        // Pertahankan logo lama jika tidak ada upload baru
        $logo = !empty($data['logo']) ? $data['logo'] : ($profil_lama['logo'] ?? null);
        $logo_kiri = !empty($data['logo_kiri']) ? $data['logo_kiri'] : ($profil_lama['logo_kiri'] ?? $logo);
        $logo_kanan = !empty($data['logo_kanan']) ? $data['logo_kanan'] : ($profil_lama['logo_kanan'] ?? $logo);

        $sql = "UPDATE profil_sekolah SET 
                    nama_sekolah = ?, 
                    npsn = ?, 
                    bentuk_pendidikan = ?, 
                    kurikulum = ?, 
                    nama_kepala_sekolah = ?, 
                    alamat = ?, 
                    koordinat = ?, 
                    telepon = ?, 
                    email = ?, 
                    website = ?, 
                    status_sekolah = ?, 
                    nama_yayasan = ?, 
                    sk_izin_operasional = ?, 
                    sk_akreditasi = ?, 
                    moto = ?, 
                    logo = ?,
                    model_kop = ?,
                    kop_baris_1 = ?,
                    kop_baris_2 = ?,
                    kop_baris_3 = ?,
                    kop_baris_4 = ?,
                    kop_baris_5 = ?,
                    logo_kiri = ?,
                    logo_kanan = ?,
                    show_logo_kiri = ?,
                    show_logo_kanan = ?,
                    style_garis = ?
                WHERE id = 1";
        
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $data['nama_sekolah'] ?? '', 
            $data['npsn'] ?? '', 
            $data['bentuk_pendidikan'] ?? 'SMA', 
            $data['kurikulum'] ?? 'Kurikulum Merdeka',
            $data['nama_kepala_sekolah'] ?? '', 
            $data['alamat'] ?? '', 
            $data['koordinat'] ?? '', 
            $data['telepon'] ?? '',
            $data['email'] ?? '', 
            $data['website'] ?? '', 
            $data['status_sekolah'] ?? 'Swasta', 
            $data['nama_yayasan'] ?? '',
            $data['sk_izin_operasional'] ?? '', 
            $data['sk_akreditasi'] ?? '', 
            $data['moto'] ?? '', 
            $logo,
            $data['model_kop'] ?? 'yayasan',
            $data['kop_baris_1'] ?? '',
            $data['kop_baris_2'] ?? '',
            $data['kop_baris_3'] ?? '',
            $data['kop_baris_4'] ?? '',
            $data['kop_baris_5'] ?? '',
            $logo_kiri,
            $logo_kanan,
            isset($data['show_logo_kiri']) ? (int)$data['show_logo_kiri'] : 1,
            isset($data['show_logo_kanan']) ? (int)$data['show_logo_kanan'] : 1,
            $data['style_garis'] ?? 'double'
        ]);
    }
}