<?php
class PenilaianSumatifModel {

    // (Fungsi getAgendaSumatifList, findAgendaSumatif, saveAgendaSumatif, getSelectedTpIdsForSumatif TIDAK BERUBAH)
    public static function getAgendasByGuru($pdo, $id_guru, $id_ta, $id_kelas = null, $id_guru_mapel = null) {
        $sql = "SELECT ps.*, k.nama_kelas, m.nama_mapel 
                FROM penilaian_sumatif ps
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE ps.id_ta = ? AND gm.id_guru = ?";
        
        $params = [$id_ta, $id_guru];

        if ($id_kelas) {
            $sql .= " AND ps.id_kelas = ?";
            $params[] = $id_kelas;
        }

        if ($id_guru_mapel) {
            $sql .= " AND ps.id_guru_mapel = ?";
            $params[] = $id_guru_mapel;
        }

        $sql .= " ORDER BY ps.tanggal_penilaian DESC, ps.nama_penilaian ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function findAgendaSumatif($pdo, $id_sumatif) {
        $stmt = $pdo->prepare("SELECT ps.*, m.nama_mapel, k.nama_kelas, k.tingkat, gm.id_guru_mapel FROM penilaian_sumatif ps JOIN guru_mapel gm ON ps.id_guru_mapel = gm.id_guru_mapel JOIN mapel m ON gm.id_mapel = m.id_mapel JOIN kelas k ON ps.id_kelas = k.id_kelas WHERE ps.id_sumatif = ?");
        $stmt->execute([$id_sumatif]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function saveAgendaSumatif($pdo, $data, $tp_ids = []) {
        $sql = "INSERT INTO penilaian_sumatif (id_guru_mapel, id_kelas, id_ta, nama_penilaian, jenis_sumatif, tanggal_penilaian, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ $data['id_guru_mapel'], $data['id_kelas'], $data['id_ta'], $data['nama_penilaian'], $data['jenis_sumatif'], $data['tanggal_penilaian'], $data['keterangan'] ]);
        $id_sumatif = $pdo->lastInsertId();
        if ($id_sumatif && !empty($tp_ids)) {
            self::saveAgendaTp($pdo, $id_sumatif, $tp_ids);
        }
        return $id_sumatif;
    }
    public static function updateAgendaSumatif($pdo, $data, $tp_ids = []) {
        $sql = "UPDATE penilaian_sumatif SET id_guru_mapel = ?, id_kelas = ?, nama_penilaian = ?, jenis_sumatif = ?, tanggal_penilaian = ?, keterangan = ? WHERE id_sumatif = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([ $data['id_guru_mapel'], $data['id_kelas'], $data['nama_penilaian'], $data['jenis_sumatif'], $data['tanggal_penilaian'], $data['keterangan'], $data['id_sumatif'] ]);
        if ($result && !empty($tp_ids)) {
            self::saveAgendaTp($pdo, $data['id_sumatif'], $tp_ids);
        }
        return $result;
    }
    private static function saveAgendaTp($pdo, $id_sumatif, $tp_ids) {
        $pdo->prepare("DELETE FROM penilaian_sumatif_tp WHERE id_sumatif = ?")->execute([$id_sumatif]);
        $stmt = $pdo->prepare("INSERT INTO penilaian_sumatif_tp (id_sumatif, id_tp) VALUES (?, ?)");
        foreach ($tp_ids as $id_tp) {
            $stmt->execute([$id_sumatif, $id_tp]);
        }
    }
    public static function getSelectedTpIdsForAgenda($pdo, $id_sumatif) {
        $stmt = $pdo->prepare("SELECT id_tp FROM penilaian_sumatif_tp WHERE id_sumatif = ?");
        $stmt->execute([$id_sumatif]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public static function deleteAgendaSumatif($pdo, $id_sumatif) {
        $pdo->beginTransaction();
        try {
            // Delete nilai_sumatif_tp relations (linked via nilai_sumatif)
            $pdo->prepare("DELETE FROM nilai_sumatif_tp WHERE id_nilai_sumatif IN (SELECT id_nilai_sumatif FROM nilai_sumatif WHERE id_sumatif = ?)")->execute([$id_sumatif]);
            // Delete nilai_sumatif
            $pdo->prepare("DELETE FROM nilai_sumatif WHERE id_sumatif = ?")->execute([$id_sumatif]);
            // Delete agenda
            $pdo->prepare("DELETE FROM penilaian_sumatif WHERE id_sumatif = ?")->execute([$id_sumatif]);
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
     public static function getSelectedTpIdsForSumatif($pdo, $id_sumatif) {
        return self::getSelectedTpIdsForAgenda($pdo, $id_sumatif);
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

            // Upsert capaian per-TP
            $upsertCapaianStmt = $pdo->prepare(
                "INSERT INTO nilai_sumatif_tp_capaian (id_sumatif, id_penempatan, id_tp, capaian)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE capaian = VALUES(capaian)"
            );

            // Ambil detail TP yang dipilih SEKALI SAJA di luar loop siswa
            $selected_tps_details = [];
            if (!empty($data['selected_tps']) && is_array($data['selected_tps'])) {
                $placeholders = implode(',', array_fill(0, count($data['selected_tps']), '?'));
                $tpStmt = $pdo->prepare("SELECT id_tp, deskripsi_tp FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders) ORDER BY kode_tp");
                $tpStmt->execute($data['selected_tps']);
                $selected_tps_details = $tpStmt->fetchAll(PDO::FETCH_KEY_PAIR);
            }

            // Ambil Nama Siswa
            $id_penempatans = array_keys($data['nilai']);
            $student_names = [];
            if (!empty($id_penempatans)) {
                $placeholdersSiswa = implode(',', array_fill(0, count($id_penempatans), '?'));
                $nameStmt = $pdo->prepare("SELECT ps.id_penempatan, s.nama FROM penempatan_siswa ps JOIN siswa s ON ps.id_siswa = s.id_siswa WHERE ps.id_penempatan IN ($placeholdersSiswa)");
                $nameStmt->execute($id_penempatans);
                $student_names = $nameStmt->fetchAll(PDO::FETCH_KEY_PAIR);
            }

            foreach ($data['nilai'] as $id_penempatan => $nilai_data) {
                if (isset($nilai_data['nilai']) && $nilai_data['nilai'] !== '') {
                    $nilai_angka = (float)$nilai_data['nilai'];
                    $nama_siswa = $student_names[$id_penempatan] ?? 'Siswa';

                    // Ambil capaian per-TP untuk siswa ini
                    $capaian_per_tp = $data['capaian_tp'][$id_penempatan] ?? [];

                    // Generate deskripsi dari capaian per-TP
                    $deskripsi = self::generateDeskripsiDariCapaianTP(
                        $capaian_per_tp,
                        $selected_tps_details,
                        $nama_siswa,
                        $data['selected_tps']
                    );

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

                    // Simpan capaian per-TP
                    foreach ($capaian_per_tp as $id_tp => $capaian) {
                        if (in_array($capaian, ['A', 'B', 'C'])) {
                            $upsertCapaianStmt->execute([$data['id_sumatif'], $id_penempatan, $id_tp, $capaian]);
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
            error_log("Error saving sumatif score: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getCapaianSiswaForSumatif($pdo, $id_sumatif, $id_kelas, $id_ta) {
        $sql = "SELECT nstc.id_penempatan, nstc.id_tp, nstc.capaian
                FROM nilai_sumatif_tp_capaian nstc
                JOIN penempatan_siswa ps ON nstc.id_penempatan = ps.id_penempatan
                WHERE nstc.id_sumatif = ? AND ps.id_kelas = ? AND ps.id_ta = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_sumatif, $id_kelas, $id_ta]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Reformat: [id_penempatan][id_tp] = capaian
        $result = [];
        foreach ($rows as $row) {
            $result[$row['id_penempatan']][$row['id_tp']] = $row['capaian'];
        }
        return $result;
    }

    private static function cleanTpDescription($text) {
        $prefixes = [
            '/^Peserta didik dapat\s+/i',
            '/^Peserta didik mampu\s+/i',
            '/^Siswa dapat\s+/i',
            '/^Siswa mampu\s+/i',
            '/^Dapat\s+/i',
            '/^Mampu\s+/i',
        ];
        $text = preg_replace($prefixes, '', trim($text));
        return lcfirst($text);
    }

    private static function generateDeskripsiSumatifOtomatis($pdo, $nilai_sumatif, $selected_tp_ids, $selected_tps_details, $id_guru_mapel, $nama_siswa = 'Ananda') {
         $kktpStmt = $pdo->prepare("SELECT m.kktp FROM mapel m JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel WHERE gm.id_guru_mapel = ?");
         $kktpStmt->execute([$id_guru_mapel]);
         $kktp = (int) $kktpStmt->fetchColumn();
         if (!$kktp) $kktp = 75;

         $tp_descriptions = [];
         if(!empty($selected_tp_ids)){
             foreach($selected_tp_ids as $id_tp) {
                 if (isset($selected_tps_details[$id_tp])) {
                     $tp_descriptions[] = self::cleanTpDescription($selected_tps_details[$id_tp]);
                 }
             }
         }
         
         $kompetensi_text = !empty($tp_descriptions) ? implode(', ', $tp_descriptions) : 'materi yang dinilai';
         
         $nama_panggilan = explode(' ', trim($nama_siswa))[0];
         $prefix_nama = "Ananda " . $nama_panggilan;

         if ($nilai_sumatif >= 90) {
             return $prefix_nama . " memiliki kemampuan yang sangat baik dalam " . $kompetensi_text . ".";
         } elseif ($nilai_sumatif >= $kktp) {
             return $prefix_nama . " memiliki kemampuan yang baik dalam " . $kompetensi_text . ".";
         } else {
             return $prefix_nama . " perlu meningkatkan kemampuan dalam " . $kompetensi_text . ".";
         }
    }

    /**
     * Generate deskripsi berdasarkan capaian per-TP (A/B/C)
     * A = Sangat Baik, B = Baik, C = Perlu Bimbingan
     */
    private static function generateDeskripsiDariCapaianTP($capaian_per_tp, $selected_tps_details, $nama_siswa, $selected_tp_ids) {
        $nama_panggilan = explode(' ', trim($nama_siswa))[0];
        $prefix = "Ananda " . $nama_panggilan;

        // Kelompokkan TP berdasarkan capaian
        $baik_sekali = []; // A
        $baik = [];        // B
        $perlu_bimbingan = []; // C

        // Urutkan sesuai id_tp yang ada di selected_tp_ids agar konsisten
        foreach ($selected_tp_ids as $id_tp) {
            $capaian = $capaian_per_tp[$id_tp] ?? null;
            $deskripsi_tp = isset($selected_tps_details[$id_tp])
                ? self::cleanTpDescription($selected_tps_details[$id_tp])
                : null;
            if (!$deskripsi_tp) continue;

            if ($capaian === 'A') {
                $baik_sekali[] = $deskripsi_tp;
            } elseif ($capaian === 'B') {
                $baik[] = $deskripsi_tp;
            } elseif ($capaian === 'C') {
                $perlu_bimbingan[] = $deskripsi_tp;
            }
        }

        // Jika belum ada capaian yang diisi
        if (empty($baik_sekali) && empty($baik) && empty($perlu_bimbingan)) {
            return "Akan digenerate otomatis setelah capaian TP diisi.";
        }

        $parts = [];
        if (!empty($baik_sekali)) {
            $parts[] = "menunjukkan pemahaman yang sangat baik dalam " . implode(', ', $baik_sekali);
        }
        if (!empty($baik)) {
            $prefix_baik = empty($baik_sekali) ? "menunjukkan pemahaman yang baik dalam " : "baik dalam ";
            $parts[] = $prefix_baik . implode(', ', $baik);
        }
        if (!empty($perlu_bimbingan)) {
            $parts[] = "masih perlu bimbingan dalam " . implode(', ', $perlu_bimbingan);
        }

        $last_part = array_pop($parts);
        if (empty($parts)) {
            return $prefix . " " . $last_part . ".";
        } else {
            return $prefix . " " . implode(', ', $parts) . " dan " . $last_part . ".";
        }
    }
} // Akhir Class