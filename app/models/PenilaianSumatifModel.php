<?php
class PenilaianSumatifModel {

    // (Fungsi getAgendaSumatifList, findAgendaSumatif, saveAgendaSumatif, getSelectedTpIdsForSumatif TIDAK BERUBAH)
    public static function getAgendaSumatifList($pdo, $id_guru_mapel, $id_kelas, $id_ta) {
        $sql = "SELECT * FROM penilaian_sumatif WHERE id_guru_mapel = ? AND id_kelas = ? AND id_ta = ? ORDER BY tanggal_penilaian DESC, nama_penilaian ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru_mapel, $id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function findAgendaSumatif($pdo, $id_sumatif) {
        $stmt = $pdo->prepare("SELECT ps.*, m.nama_mapel, k.nama_kelas, k.tingkat, gm.id_guru_mapel FROM penilaian_sumatif ps JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel JOIN mapel m ON gm.id_mapel = m.id_mapel JOIN kelas k ON ps.id_kelas = k.id_kelas WHERE ps.id_sumatif = ?");
        $stmt->execute([$id_sumatif]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function saveAgendaSumatif($pdo, $data) {
        $sql = "INSERT INTO penilaian_sumatif (id_guru_mapel, id_kelas, id_ta, nama_penilaian, jenis_sumatif, tanggal_penilaian, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ $data['id_guru_mapel'], $data['id_kelas'], $data['id_ta'], $data['nama_penilaian'], $data['jenis_sumatif'], $data['tanggal_penilaian'], $data['keterangan'] ]);
        return $pdo->lastInsertId();
    }
     public static function getSelectedTpIdsForSumatif($pdo, $id_sumatif) {
         $nilaiSumatifIdStmt = $pdo->prepare("SELECT id_nilai_sumatif FROM nilai_sumatif WHERE id_sumatif = ? LIMIT 1");
         $nilaiSumatifIdStmt->execute([$id_sumatif]);
         $id_nilai_sumatif = $nilaiSumatifIdStmt->fetchColumn();
         if (!$id_nilai_sumatif) return [];
         $sql = "SELECT id_tp FROM nilai_sumatif_tp WHERE id_nilai_sumatif = ?";
         $stmt = $pdo->prepare($sql);
         $stmt->execute([$id_nilai_sumatif]);
         return $stmt->fetchAll(PDO::FETCH_COLUMN);
     }
    public static function getSiswaWithNilaiSumatif($pdo, $id_kelas, $id_sumatif, $id_ta) {
         $sql = "SELECT s.id_siswa, s.nama, s.nisn, ps.id_penempatan, ns.id_nilai_sumatif, ns.nilai, ns.deskripsi_capaian FROM siswa s JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa LEFT JOIN nilai_sumatif ns ON ps.id_penempatan = ns.id_penempatan AND ns.id_sumatif = ? WHERE ps.id_kelas = ? AND ps.id_ta = ? AND s.status_aktif = 'Aktif' ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_sumatif, $id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menyimpan nilai sumatif untuk semua siswa dalam satu agenda,
     * dan menghubungkannya ke TP yang dipilih.
     * Menggunakan ON DUPLICATE KEY UPDATE untuk efisiensi.
     */
    public static function saveNilaiSumatif($pdo, $data) {
        $pdo->beginTransaction();
        try {
            $upsertNilaiSql = "INSERT INTO nilai_sumatif (id_sumatif, id_penempatan, nilai, deskripsi_capaian)
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE nilai = VALUES(nilai), deskripsi_capaian = VALUES(deskripsi_capaian)";
            $stmtNilai = $pdo->prepare($upsertNilaiSql);
            $deleteTpStmt = $pdo->prepare("DELETE FROM nilai_sumatif_tp WHERE id_nilai_sumatif = ?");
            $insertTpStmt = $pdo->prepare("INSERT INTO nilai_sumatif_tp (id_nilai_sumatif, id_tp) VALUES (?, ?)");
            $getIdStmt = $pdo->prepare("SELECT id_nilai_sumatif FROM nilai_sumatif WHERE id_sumatif = ? AND id_penempatan = ?");

            // Ambil detail TP yang dipilih SEKALI SAJA di luar loop siswa
            $selected_tps_details = [];
            if (!empty($data['selected_tps']) && is_array($data['selected_tps'])) {
                $placeholders = implode(',', array_fill(0, count($data['selected_tps']), '?'));
                $tpStmt = $pdo->prepare("SELECT id_tp, deskripsi_tp FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders)");
                $tpStmt->execute($data['selected_tps']);
                // Buat array asosiatif [id_tp => deskripsi_tp]
                $selected_tps_details = $tpStmt->fetchAll(PDO::FETCH_KEY_PAIR);
            }

            foreach ($data['nilai'] as $id_penempatan => $nilai_data) {
                if (isset($nilai_data['nilai']) && $nilai_data['nilai'] !== '') {
                    $nilai_angka = (float)$nilai_data['nilai'];

                    // =============================================
                    // PERBAIKAN: Hapus "??" agar deskripsi SELALU digenerate ulang
                    // =============================================
                    $deskripsi = self::generateDeskripsiSumatifOtomatis(
                        $pdo,
                        $nilai_angka,
                        $data['selected_tps'], // Kirim ID TP
                        $selected_tps_details, // Kirim detail TP (deskripsi teks)
                        $data['id_guru_mapel'] // Kirim ID Guru Mapel (untuk KKTP)
                    );
                    // =============================================

                    $stmtNilai->execute([$data['id_sumatif'], $id_penempatan, $nilai_angka, $deskripsi]);

                    $getIdStmt->execute([$data['id_sumatif'], $id_penempatan]);
                    $id_nilai_sumatif = $getIdStmt->fetchColumn();
                    if ($id_nilai_sumatif) {
                        $deleteTpStmt->execute([$id_nilai_sumatif]);
                        if (!empty($data['selected_tps']) && is_array($data['selected_tps'])) {
                            foreach ($data['selected_tps'] as $id_tp) {
                                $insertTpStmt->execute([$id_nilai_sumatif, $id_tp]);
                            }
                        }
                    }
                } else {
                    $stmtDeleteNilai = $pdo->prepare("DELETE FROM nilai_sumatif WHERE id_sumatif = ? AND id_penempatan = ?");
                    $stmtDeleteNilai->execute([$data['id_sumatif'], $id_penempatan]);
                }
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error saving sumatif score: " . $e->getMessage()); // Log error
            throw $e; // Lemparkan kembali error agar bisa ditangkap oleh controller
        }
    }

    /**
     * FUNGSI GENERATE DESKRIPSI YANG DIPERBARUI
     */
    private static function generateDeskripsiSumatifOtomatis($pdo, $nilai_sumatif, $selected_tp_ids, $selected_tps_details, $id_guru_mapel) {
         $kktpStmt = $pdo->prepare("SELECT m.kktp FROM mapel m JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel WHERE gm.id_guru_mapel = ?");
         $kktpStmt->execute([$id_guru_mapel]);
         $kktp = (int) $kktpStmt->fetchColumn();
         if (!$kktp) $kktp = 75; // Default KKTP jika tidak diatur

         // Ambil deskripsi teks dari semua TP yang dipilih
         $tp_descriptions = [];
         if(!empty($selected_tp_ids)){
             foreach($selected_tp_ids as $id_tp) {
                 if (isset($selected_tps_details[$id_tp])) {
                     // Ambil bagian inti deskripsi (opsional, bisa disempurnakan)
                     $tp_descriptions[] = $selected_tps_details[$id_tp];
                 }
             }
         }
         $kompetensi_text = !empty($tp_descriptions) ? implode(', ', $tp_descriptions) : 'lingkup materi yang dinilai';

         // Buat kalimat berdasarkan perbandingan nilai
         if ($nilai_sumatif >= 90) {
             return "Menunjukkan penguasaan yang sangat baik pada kompetensi: " . $kompetensi_text . ".";
         } elseif ($nilai_sumatif >= $kktp) {
             return "Telah mencapai ketuntasan pada kompetensi: " . $kompetensi_text . ".";
         } else {
             return "Masih memerlukan bimbingan pada kompetensi: " . $kompetensi_text . ".";
         }
    }
} // Akhir Class