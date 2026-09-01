<?php

class RekapNilaiModel {
    private static $defaultBobotConfig = null;

    public static function getKelasDiajar($pdo, $id_guru, $id_ta) {
        $sql = "SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat
                FROM guru_mapel gm
                JOIN kelas k ON gm.id_kelas = k.id_kelas
                WHERE gm.id_guru = ? AND gm.id_ta = ?
                ORDER BY k.tingkat, k.nama_kelas";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMapelDiajarByKelas($pdo, $id_guru, $id_kelas, $id_ta) {
        $sql = "SELECT gm.id_guru_mapel, m.nama_mapel
                FROM guru_mapel gm
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE gm.id_guru = ? AND gm.id_kelas = ? AND gm.id_ta = ?
                ORDER BY m.nama_mapel";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru, $id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getBobotConfig($pdo, $id_guru_mapel, $id_kelas) {
        $stmt = $pdo->prepare("SELECT * FROM rekap_bobot_guru WHERE id_guru_mapel = ? AND id_kelas = ?");
        $stmt->execute([$id_guru_mapel, $id_kelas]);
        $custom = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch global defaults (Cached)
        if (self::$defaultBobotConfig === null) {
            $stmtDef = $pdo->query("SELECT config_key, config_value FROM app_config WHERE config_key LIKE 'default_%'");
            $defaults = [];
            while ($row = $stmtDef->fetch(PDO::FETCH_ASSOC)) {
                $defaults[$row['config_key']] = $row['config_value'];
            }
            self::$defaultBobotConfig = $defaults;
        }
        $defaults = self::$defaultBobotConfig;

        if ($custom) {
            return [
                'sikap' => (float)$custom['bobot_sikap'],
                'lms' => (float)$custom['bobot_lms'],
                'formatif' => (float)$custom['bobot_formatif'],
                'sumatif_lm' => (float)$custom['bobot_sumatif_lm'],
                'sts' => (float)$custom['bobot_sts'],
                'sas' => (float)$custom['bobot_sas'],
                'limit_tp_tinggi' => (int)$custom['limit_tp_tinggi'],
                'limit_tp_rendah' => (int)$custom['limit_tp_rendah'],
                'is_custom' => true
            ];
        }

        return [
            'sikap' => (float)($defaults['default_bobot_sikap'] ?? 0),
            'lms' => (float)($defaults['default_bobot_lms'] ?? 0),
            'formatif' => (float)($defaults['default_bobot_formatif'] ?? 0),
            'sumatif_lm' => (float)($defaults['default_bobot_sumatif_lm'] ?? 0),
            'sts' => (float)($defaults['default_bobot_sts'] ?? 0),
            'sas' => (float)($defaults['default_bobot_sas'] ?? 0),
            'limit_tp_tinggi' => (int)($defaults['default_limit_tp_tinggi'] ?? 3),
            'limit_tp_rendah' => (int)($defaults['default_limit_tp_rendah'] ?? 2),
            'is_custom' => false
        ];
    }

    public static function saveBobotConfig($pdo, $data) {
        $stmt = $pdo->prepare("SELECT id_bobot FROM rekap_bobot_guru WHERE id_guru_mapel = ? AND id_kelas = ?");
        $stmt->execute([$data['id_guru_mapel'], $data['id_kelas']]);
        
        if ($stmt->fetch()) {
            $sql = "UPDATE rekap_bobot_guru SET 
                    bobot_sikap = ?, bobot_lms = ?, bobot_formatif = ?, 
                    bobot_sumatif_lm = ?, bobot_sts = ?, bobot_sas = ?,
                    limit_tp_tinggi = ?, limit_tp_rendah = ?
                    WHERE id_guru_mapel = ? AND id_kelas = ?";
            $pdo->prepare($sql)->execute([
                $data['bobot_sikap'], $data['bobot_lms'], $data['bobot_formatif'],
                $data['bobot_sumatif_lm'], $data['bobot_sts'], $data['bobot_sas'],
                $data['limit_tp_tinggi'], $data['limit_tp_rendah'],
                $data['id_guru_mapel'], $data['id_kelas']
            ]);
        } else {
            $sql = "INSERT INTO rekap_bobot_guru (id_guru_mapel, id_kelas, bobot_sikap, bobot_lms, bobot_formatif, bobot_sumatif_lm, bobot_sts, bobot_sas, limit_tp_tinggi, limit_tp_rendah) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([
                $data['id_guru_mapel'], $data['id_kelas'], 
                $data['bobot_sikap'], $data['bobot_lms'], $data['bobot_formatif'],
                $data['bobot_sumatif_lm'], $data['bobot_sts'], $data['bobot_sas'],
                $data['limit_tp_tinggi'], $data['limit_tp_rendah']
            ]);
        }
    }

    public static function resetBobotConfig($pdo, $id_guru_mapel, $id_kelas) {
        $stmt = $pdo->prepare("DELETE FROM rekap_bobot_guru WHERE id_guru_mapel = ? AND id_kelas = ?");
        $stmt->execute([$id_guru_mapel, $id_kelas]);
    }

    public static function getRekapData($pdo, $id_kelas, $id_guru_mapel, $id_ta, $limits) {
        $stmtGM = $pdo->prepare("SELECT id_guru, id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
        $stmtGM->execute([$id_guru_mapel]);
        $gm = $stmtGM->fetch(PDO::FETCH_ASSOC);
        $id_guru = $gm['id_guru'] ?? 0;
        $id_mapel = $gm['id_mapel'] ?? 0;

        $stmtSiswa = $pdo->prepare("SELECT p.id_penempatan, s.id_siswa, s.nama, s.nisn 
            FROM penempatan_siswa p 
            JOIN siswa s ON p.id_siswa = s.id_siswa 
            WHERE p.id_kelas = ? AND p.id_ta = ? AND s.status_aktif = 'Aktif'
            ORDER BY s.nama ASC");
        $stmtSiswa->execute([$id_kelas, $id_ta]);
        $siswa_list = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

        $rekap = [];
        $ids_penempatan = [];
        $id_siswa_to_penempatan = [];
        foreach ($siswa_list as $s) {
            $rekap[$s['id_penempatan']] = [
                'id_penempatan' => $s['id_penempatan'],
                'id_siswa' => $s['id_siswa'],
                'nama' => $s['nama'],
                'nisn' => $s['nisn'],
                'sikap' => null, 
                'lms' => null, 
                'formatif' => null, 
                'sumatif_lm' => null, 
                'sts' => null, 
                'sas' => null, 
                'deskripsi_rapor' => ''
            ];
            $ids_penempatan[] = $s['id_penempatan'];
            $id_siswa_to_penempatan[$s['id_siswa']] = $s['id_penempatan'];
        }

        if (empty($ids_penempatan)) return [];
        $placeholders = implode(',', array_fill(0, count($ids_penempatan), '?'));

        // 2. Sikap
        if ($id_guru && $id_mapel) {
            $stmtSikap = $pdo->prepare("
                SELECT ns.id_penempatan, AVG(ns.nilai_angka) as avg_sikap
                FROM nilai_sikap ns
                JOIN agenda_penilaian_sikap a ON ns.id_agenda = a.id_agenda
                WHERE a.id_kelas = ? AND a.id_ta = ? AND a.id_guru = ? AND a.id_mapel = ? AND a.is_nilai_tambahan = 1
                GROUP BY ns.id_penempatan
            ");
            $stmtSikap->execute([$id_kelas, $id_ta, $id_guru, $id_mapel]);
            while ($row = $stmtSikap->fetch(PDO::FETCH_ASSOC)) {
                if (isset($rekap[$row['id_penempatan']])) {
                    $rekap[$row['id_penempatan']]['sikap'] = round($row['avg_sikap'], 2);
                }
            }
        }

        // 3. LMS
        if ($id_guru && $id_mapel) {
            $stmtLms = $pdo->prepare("
                SELECT p.id_siswa, AVG(p.nilai) as avg_lms
                FROM lms_pengumpulan p
                JOIN lms_tugas t ON p.id_tugas = t.id_tugas
                WHERE t.id_kelas = ? AND t.id_guru = ? AND t.id_mapel = ?
                GROUP BY p.id_siswa
            ");
            $stmtLms->execute([$id_kelas, $id_guru, $id_mapel]);
            while ($row = $stmtLms->fetch(PDO::FETCH_ASSOC)) {
                $id_p = $id_siswa_to_penempatan[$row['id_siswa']] ?? null;
                if ($id_p && isset($rekap[$id_p])) {
                    if ($row['avg_lms'] !== null) {
                        $rekap[$id_p]['lms'] = round($row['avg_lms'], 2);
                    }
                }
            }
        }

        // 4. Formatif (Basic Average)
        $stmtF = $pdo->prepare("SELECT id_penempatan, AVG(nilai) as avg_f FROM nilai WHERE id_guru_mapel = ? AND id_penempatan IN ($placeholders) GROUP BY id_penempatan");
        $stmtF->execute(array_merge([$id_guru_mapel], $ids_penempatan));
        while ($row = $stmtF->fetch(PDO::FETCH_ASSOC)) {
            if ($row['avg_f'] !== null) {
                $rekap[$row['id_penempatan']]['formatif'] = round($row['avg_f'], 2);
            }
        }

        // 5. Sumatif (STS & SAS/SAT)
        $stmtS = $pdo->prepare("
            SELECT ns.id_penempatan, ps.jenis_sumatif, AVG(ns.nilai) as avg_s
            FROM nilai_sumatif ns
            JOIN penilaian_sumatif ps ON ns.id_sumatif = ps.id_sumatif
            WHERE ps.id_guru_mapel = ? AND ns.id_penempatan IN ($placeholders)
            GROUP BY ns.id_penempatan, ps.jenis_sumatif
        ");
        $stmtS->execute(array_merge([$id_guru_mapel], $ids_penempatan));
        while ($row = $stmtS->fetch(PDO::FETCH_ASSOC)) {
            $id_p = $row['id_penempatan'];
            if ($row['jenis_sumatif'] == 'Sumatif Lingkup Materi') {
                $rekap[$id_p]['sumatif_lm'] = round($row['avg_s'], 2);
            } else if ($row['jenis_sumatif'] == 'Sumatif Tengah Semester') {
                $rekap[$id_p]['sts'] = round($row['avg_s'], 2);
            } else {
                $rekap[$id_p]['sas'] = round($row['avg_s'], 2);
            }
        }

        // 6. Intelligent Description Logic (TP Based)
        // Ambil SEMUA capaian TP siswa untuk mata pelajaran ini di semester ini
        $stmtTp = $pdo->prepare("
            SELECT ntc.id_penempatan, ntc.capaian, tp.deskripsi_tp
            FROM nilai_sumatif_tp_capaian ntc
            JOIN penilaian_sumatif ps ON ntc.id_sumatif = ps.id_sumatif
            JOIN tujuan_pembelajaran tp ON ntc.id_tp = tp.id_tp
            WHERE ps.id_guru_mapel = ? AND ntc.id_penempatan IN ($placeholders)
            ORDER BY ntc.capaian ASC, tp.deskripsi_tp ASC
        ");
        $stmtTp->execute(array_merge([$id_guru_mapel], $ids_penempatan));
        
        $siswa_tps = [];
        while ($row = $stmtTp->fetch(PDO::FETCH_ASSOC)) {
            $cleaned_desc = self::cleanTpDescription($row['deskripsi_tp']);
            $siswa_tps[$row['id_penempatan']][$row['capaian']][] = $cleaned_desc;
        }

        foreach ($rekap as $id_p => &$r) {
            $tps = $siswa_tps[$id_p] ?? [];
            if (empty($tps)) {
                $r['deskripsi_rapor'] = "Belum ada data capaian TP yang diinputkan.";
                continue;
            }

            $listA = $tps['A'] ?? [];
            $listB = $tps['B'] ?? [];
            $listC = $tps['C'] ?? [];

            $txtTinggi = "";
            $txtRendah = "";

            // Menentukan Capaian Tertinggi
            if (!empty($listA)) {
                $sliced = array_slice($listA, 0, $limits['limit_tp_tinggi']);
                $txtTinggi = "Menunjukkan penguasaan yang sangat baik dalam " . self::joinWords($sliced);
            } elseif (!empty($listB)) {
                $sliced = array_slice($listB, 0, $limits['limit_tp_tinggi']);
                $txtTinggi = "Menunjukkan penguasaan yang baik dalam " . self::joinWords($sliced);
            }

            // Menentukan Capaian Terendah / Perlu Bimbingan
            if (!empty($listC)) {
                $sliced = array_slice($listC, 0, $limits['limit_tp_rendah']);
                $txtRendah = "perlu bimbingan dalam " . self::joinWords($sliced);
            } elseif (empty($listA) && !empty($listB)) {
                // Jika tidak ada A sama sekali, mungkin ada B yang paling rendah dibanding B lainnya? 
                // Tapi biasanya di rapor hanya mencantumkan yang Perlu Bimbingan (C).
                // Jika tidak ada C, kita bisa sebutkan perkembangan baik di TP lainnya.
            }

            // Gabungkan kalimat
            $finalTxt = $txtTinggi;
            if ($txtTinggi && $txtRendah) {
                $finalTxt .= ", namun " . $txtRendah;
            } elseif (!$txtTinggi && $txtRendah) {
                $finalTxt = "Masih " . $txtRendah;
            } elseif ($txtTinggi && !$txtRendah && !empty($listB) && empty($listA)) {
                // Kasus hanya ada B
            } elseif ($txtTinggi && !$txtRendah) {
                $finalTxt .= ".";
            }

            $r['deskripsi_rapor'] = $finalTxt;
        }

        return $rekap;
    }

    private static function joinWords($words) {
        if (count($words) <= 1) return implode("", $words);
        $last = array_pop($words);
        return implode(", ", $words) . " serta " . $last;
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

    public static function isWaliKelas($pdo, $id_guru, $id_kelas, $id_ta) {
        $stmt = $pdo->prepare("SELECT id_penugasan_wali_kelas FROM penugasan_wali_kelas WHERE id_guru = ? AND id_kelas = ? AND id_ta = ? AND jenis_tugas = 'Wali Kelas'");
        $stmt->execute([$id_guru, $id_kelas, $id_ta]);
        return (bool)$stmt->fetch();
    }

    public static function getSubjectsInClass($pdo, $id_kelas, $id_ta) {
        $sql = "SELECT gm.id_guru_mapel, m.nama_mapel, m.kode_mapel, g.nama as nama_guru
                FROM guru_mapel gm
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                WHERE gm.id_kelas = ? AND gm.id_ta = ?
                ORDER BY m.nama_mapel";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSikapRekap($pdo, $id_kelas, $id_ta) {
        // Ambil nilai sikap terakhir/terbaru untuk setiap siswa di kelas ini
        $sql = "SELECT ns.id_penempatan, ns.predikat, ns.deskripsi_sikap, ns.nilai_angka
                FROM nilai_sikap ns
                JOIN agenda_penilaian_sikap a ON ns.id_agenda = a.id_agenda
                WHERE a.id_kelas = ? AND a.id_ta = ?
                ORDER BY a.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kelas, $id_ta]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $res = [];
        foreach($rows as $row) {
            if (!isset($res[$row['id_penempatan']])) {
                $res[$row['id_penempatan']] = $row;
            }
        }
        return $res;
    }

    public static function getAbsensiRekap($pdo, $id_kelas, $id_ta) {
        $sql = "SELECT p.id_siswa, 
                       SUM(CASE WHEN a.status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                       SUM(CASE WHEN a.status = 'Izin' THEN 1 ELSE 0 END) as izin,
                       SUM(CASE WHEN a.status = 'Alpa' THEN 1 ELSE 0 END) as alpa
                FROM penempatan_siswa p
                LEFT JOIN absensi_siswa_piket a ON p.id_siswa = a.id_siswa AND a.id_ta = p.id_ta
                WHERE p.id_kelas = ? AND p.id_ta = ?
                GROUP BY p.id_siswa";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_kelas, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    public static function getAllNaForClass($pdo, $id_kelas, $id_ta) {
        $subjects = self::getSubjectsInClass($pdo, $id_kelas, $id_ta);
        $na_data = [];
        $desc_data = [];

        foreach ($subjects as $sub) {
            $id_gm = $sub['id_guru_mapel'];
            $bobot = self::getBobotConfig($pdo, $id_gm, $id_kelas);
            $limits = ['limit_tp_tinggi' => 1, 'limit_tp_rendah' => 1]; // Minimal for ledger
            $rekap_sub = self::getRekapData($pdo, $id_kelas, $id_gm, $id_ta, $limits);

            foreach ($rekap_sub as $id_p => $r) {
                $val_sikap = $r['sikap'] ?? 0;
                $val_lms = $r['lms'] ?? 0;
                $val_formatif = $r['formatif'] ?? 0;
                $val_lm = $r['sumatif_lm'] ?? 0;
                $val_sts = $r['sts'] ?? 0;
                $val_sas = $r['sas'] ?? 0;

                $na = ($val_sikap * ($bobot['sikap']/100)) + 
                      ($val_lms * ($bobot['lms']/100)) + 
                      ($val_formatif * ($bobot['formatif']/100)) + 
                      ($val_lm * ($bobot['sumatif_lm']/100)) + 
                      ($val_sts * ($bobot['sts']/100)) + 
                      ($val_sas * ($bobot['sas']/100));

                $na_data[$id_p][$id_gm] = round($na, 2);
                $desc_data[$id_p][$id_gm] = $r['deskripsi_rapor'];
            }
        }
        return ['na' => $na_data, 'desc' => $desc_data];
    }
}
