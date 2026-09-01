<?php
// File: app/models/PenilaianSikapModel.php

class PenilaianSikapModel {
    
    public static function getAgendas($pdo, $id_guru, $id_ta) {
        $sql = "SELECT a.*, k.nama_kelas, m.nama_mapel, g.nama as nama_guru
                FROM agenda_penilaian_sikap a
                JOIN kelas k ON a.id_kelas = k.id_kelas
                LEFT JOIN mapel m ON a.id_mapel = m.id_mapel
                LEFT JOIN guru g ON a.id_guru = g.id_guru
                WHERE a.id_ta = ?";
        $params = [$id_ta];
        
        if ($id_guru) {
            $sql .= " AND a.id_guru = ?";
            $params[] = $id_guru;
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAgendaById($pdo, $id_agenda) {
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kelas, m.nama_mapel 
                                FROM agenda_penilaian_sikap a
                                JOIN kelas k ON a.id_kelas = k.id_kelas
                                LEFT JOIN mapel m ON a.id_mapel = m.id_mapel
                                WHERE a.id_agenda = ?");
        $stmt->execute([$id_agenda]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getSelectedKomponen($pdo, $id_agenda) {
        $stmt = $pdo->prepare("SELECT k.* FROM komponen_sikap k
                                JOIN agenda_sikap_komponen ak ON k.id_komponen = ak.id_komponen
                                WHERE ak.id_agenda = ?");
        $stmt->execute([$id_agenda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveAgenda($pdo, $data) {
        if (!empty($data['id_agenda'])) {
            // Update
            $stmt = $pdo->prepare("UPDATE agenda_penilaian_sikap SET 
                                    id_kelas = ?, periode = ?, kategori_penilai = ?, 
                                    id_mapel = ?, is_nilai_tambahan = ?, bobot_tambahan = ? 
                                    WHERE id_agenda = ?");
            $stmt->execute([
                $data['id_kelas'], $data['periode'], $data['kategori_penilai'],
                $data['id_mapel'], $data['is_nilai_tambahan'], $data['bobot_tambahan'],
                $data['id_agenda']
            ]);
            $id_agenda = $data['id_agenda'];
        } else {
            // Create
            $stmt = $pdo->prepare("INSERT INTO agenda_penilaian_sikap 
                                    (id_guru, id_kelas, id_ta, periode, kategori_penilai, id_mapel, is_nilai_tambahan, bobot_tambahan)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['id_guru'], $data['id_kelas'], $data['id_ta'], $data['periode'], 
                $data['kategori_penilai'], $data['id_mapel'], $data['is_nilai_tambahan'], $data['bobot_tambahan']
            ]);
            $id_agenda = $pdo->lastInsertId();
        }

        // Sync Komponen
        if ($id_agenda && !empty($data['komponen_ids'])) {
            $pdo->prepare("DELETE FROM agenda_sikap_komponen WHERE id_agenda = ?")->execute([$id_agenda]);
            $stmtIns = $pdo->prepare("INSERT INTO agenda_sikap_komponen (id_agenda, id_komponen) VALUES (?, ?)");
            foreach ($data['komponen_ids'] as $id_k) {
                $stmtIns->execute([$id_agenda, $id_k]);
            }
        }

        return $id_agenda;
    }

    public static function getSiswaWithNilai($pdo, $id_agenda, $id_kelas, $id_ta) {
        // Ambil siswa di kelas tersebut
        $sql = "SELECT s.id_siswa, s.nama, s.nisn, ps.id_penempatan, 
                       ns.id_nilai_sikap, ns.rata_rata, ns.predikat, ns.nilai_angka
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                LEFT JOIN nilai_sikap ns ON ps.id_penempatan = ns.id_penempatan AND ns.id_agenda = ?
                WHERE ps.id_kelas = ? AND ps.id_ta = ? AND s.status_aktif = 'Aktif'
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_agenda, $id_kelas, $id_ta]);
        $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ambil detail nilai per komponen
        foreach ($siswa as &$s) {
            if ($s['id_nilai_sikap']) {
                $stmtDet = $pdo->prepare("SELECT id_komponen, nilai_predikat, nilai_angka 
                                            FROM nilai_sikap_detail WHERE id_nilai_sikap = ?");
                $stmtDet->execute([$s['id_nilai_sikap']]);
                $details = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
                $s['details'] = array_column($details, null, 'id_komponen');
            } else {
                $s['details'] = [];
            }
        }
        return $siswa;
    }

    public static function saveNilai($pdo, $id_agenda, $id_penempatan, $scores) {
        // scores: [id_komponen => 'A'/'B'/'C'/'D']
        $predikat_map = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        
        $total_points = 0;
        $count = 0;
        $details_to_save = [];

        foreach ($scores as $id_k => $p) {
            if (isset($predikat_map[$p])) {
                $points = $predikat_map[$p];
                $total_points += $points;
                $count++;
                $details_to_save[] = [
                    'id_komponen' => $id_k,
                    'nilai_predikat' => $p,
                    'nilai_angka' => $points
                ];
            }
        }

        if ($count === 0) return false;

        $rata_rata = $total_points / $count; // Skala 1-4
        $nilai_100 = ($rata_rata / 4) * 100; // Skala 1-100

        // Tentukan predikat akhir
        if ($rata_rata >= 3.5) $predikat = 'A';
        elseif ($rata_rata >= 2.5) $predikat = 'B';
        elseif ($rata_rata >= 1.5) $predikat = 'C';
        else $predikat = 'D';

        // Generate Deskripsi
        $desc = self::generateDeskripsiSikap($pdo, $id_penempatan, $details_to_save);

        $pdo->beginTransaction();
        try {
            // Upsert nilai_sikap
            $stmt = $pdo->prepare("INSERT INTO nilai_sikap (id_agenda, id_penempatan, rata_rata, predikat, nilai_angka, deskripsi_sikap)
                                   VALUES (?, ?, ?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE rata_rata=VALUES(rata_rata), predikat=VALUES(predikat), nilai_angka=VALUES(nilai_angka), deskripsi_sikap=VALUES(deskripsi_sikap)");
            $stmt->execute([$id_agenda, $id_penempatan, $rata_rata, $predikat, $nilai_100, $desc]);
            
            $id_nilai_sikap = $pdo->lastInsertId();
            if (!$id_nilai_sikap) {
                $stmtGet = $pdo->prepare("SELECT id_nilai_sikap FROM nilai_sikap WHERE id_agenda = ? AND id_penempatan = ?");
                $stmtGet->execute([$id_agenda, $id_penempatan]);
                $id_nilai_sikap = $stmtGet->fetchColumn();
            }

            // Sync Details
            $pdo->prepare("DELETE FROM nilai_sikap_detail WHERE id_nilai_sikap = ?")->execute([$id_nilai_sikap]);
            $stmtIns = $pdo->prepare("INSERT INTO nilai_sikap_detail (id_nilai_sikap, id_komponen, nilai_predikat, nilai_angka) VALUES (?, ?, ?, ?)");
            foreach ($details_to_save as $det) {
                $stmtIns->execute([$id_nilai_sikap, $det['id_komponen'], $det['nilai_predikat'], $det['nilai_angka']]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public static function deleteAgenda($pdo, $id_agenda) {
        $pdo->prepare("DELETE FROM agenda_sikap_komponen WHERE id_agenda = ?")->execute([$id_agenda]);
        $pdo->prepare("DELETE FROM nilai_sikap_detail WHERE id_nilai_sikap IN (SELECT id_nilai_sikap FROM nilai_sikap WHERE id_agenda = ?)")->execute([$id_agenda]);
        $pdo->prepare("DELETE FROM nilai_sikap WHERE id_agenda = ?")->execute([$id_agenda]);
        return $pdo->prepare("DELETE FROM agenda_penilaian_sikap WHERE id_agenda = ?")->execute([$id_agenda]);
    }

    private static function generateDeskripsiSikap($pdo, $id_penempatan, $details) {
        $stmtSiswa = $pdo->prepare("SELECT s.nama FROM siswa s JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa WHERE ps.id_penempatan = ?");
        $stmtSiswa->execute([$id_penempatan]);
        $nama = explode(' ', trim($stmtSiswa->fetchColumn()))[0];

        $baik_sekali = [];
        $baik = [];
        $cukup = [];
        $perlu_bimbingan = [];

        foreach ($details as $d) {
            $stmtK = $pdo->prepare("SELECT nama_komponen FROM komponen_sikap WHERE id_komponen = ?");
            $stmtK->execute([$d['id_komponen']]);
            $nama_k = strtolower($stmtK->fetchColumn());

            if ($d['nilai_predikat'] == 'A') $baik_sekali[] = $nama_k;
            elseif ($d['nilai_predikat'] == 'B') $baik[] = $nama_k;
            elseif ($d['nilai_predikat'] == 'C') $cukup[] = $nama_k;
            elseif ($d['nilai_predikat'] == 'D') $perlu_bimbingan[] = $nama_k;
        }

        $parts = [];
        if (!empty($baik_sekali)) $parts[] = "menunjukkan sikap yang sangat baik dalam " . self::joinSikap($baik_sekali);
        if (!empty($baik)) $parts[] = "menunjukkan perkembangan baik dalam " . self::joinSikap($baik);
        if (!empty($cukup)) $parts[] = "cukup dalam " . self::joinSikap($cukup);
        if (!empty($perlu_bimbingan)) $parts[] = "perlu bimbingan intensif dalam " . self::joinSikap($perlu_bimbingan);

        if (empty($parts)) return "Ananda $nama telah mengikuti penilaian sikap.";

        $last = array_pop($parts);
        if (empty($parts)) return "Ananda $nama " . $last . ".";
        
        return "Ananda $nama " . implode(", serta ", $parts) . " dan " . $last . ".";
    }

    private static function joinSikap($words) {
        if (count($words) <= 1) return implode("", $words);
        $last = array_pop($words);
        return implode(", ", $words) . " serta " . $last;
    }
}
