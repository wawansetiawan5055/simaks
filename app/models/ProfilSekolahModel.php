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
     * Menyimpan atau memperbarui data profil sekolah
     */
    public static function save($pdo, $data) {
        // Jika logo baru tidak di-upload (kosong), gunakan logo lama dari database
        if (empty($data['logo'])) {
            $profil_lama = self::getProfil($pdo);
            // [FIX] Typo diperbaiki: $profil_lam menjadi $profil_lama
            $data['logo'] = ($profil_lama) ? $profil_lama['logo'] : null;
        }

        // Menggunakan positional placeholders (?) untuk keamanan
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
                    logo = ? 
                WHERE id = 1";
        
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $data['nama_sekolah'], 
            $data['npsn'], 
            $data['bentuk_pendidikan'], 
            $data['kurikulum'],
            $data['nama_kepala_sekolah'], 
            $data['alamat'], 
            $data['koordinat'], 
            $data['telepon'],
            $data['email'], 
            $data['website'], 
            $data['status_sekolah'], 
            $data['nama_yayasan'],
            $data['sk_izin_operasional'], 
            $data['sk_akreditasi'], 
            $data['moto'], 
            $data['logo']
        ]);
    }
}
?>