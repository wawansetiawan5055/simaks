<?php
// app/models/JadwalPiketModel.php

class JadwalPiketModel
{
    /**
     * Mengambil daftar jadwal piket per hari (Senin s/d Jumat - 5 Hari Kerja) untuk Tahun Ajaran tertentu
     */
    public static function getJadwalWeekly($pdo, $id_ta)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $result = [];
        foreach ($days as $day) {
            $result[$day] = [];
        }

        $sql = "SELECT j.*, g.nama as nama_guru, g.kode_guru, g.nuptk, p.foto
                FROM jadwal_guru_piket j
                JOIN guru g ON j.id_guru = g.id_guru
                LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
                WHERE j.id_ta = ?
                ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), g.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            if (isset($result[$row['hari']])) {
                $result[$row['hari']][] = $row;
            }
        }

        return $result;
    }

    /**
     * Mengambil daftar guru yang bertugas piket pada hari tertentu di Tahun Ajaran tertentu
     */
    public static function getPiketHariIni($pdo, $id_ta, $hari)
    {
        $sql = "SELECT j.*, g.nama as nama_guru, g.kode_guru, g.nuptk, p.foto
                FROM jadwal_guru_piket j
                JOIN guru g ON j.id_guru = g.id_guru
                LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
                WHERE j.id_ta = ? AND j.hari = ?
                ORDER BY g.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $hari]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menyimpan penugasan guru piket baru
     */
    public static function save($pdo, $data)
    {
        $stmt_chk = $pdo->prepare("SELECT COUNT(*) FROM jadwal_guru_piket WHERE id_ta = ? AND hari = ? AND id_guru = ?");
        $stmt_chk->execute([$data['id_ta'], $data['hari'], $data['id_guru']]);
        if ($stmt_chk->fetchColumn() > 0) {
            throw new Exception("Guru tersebut sudah dijadwalkan piket pada hari " . $data['hari'] . ".");
        }

        $sql = "INSERT INTO jadwal_guru_piket (id_ta, hari, id_guru, keterangan) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['id_ta'],
            $data['hari'],
            $data['id_guru'],
            $data['keterangan'] ?? null
        ]);
    }

    /**
     * Menghapus jadwal piket
     */
    public static function delete($pdo, $id_jadwal_piket)
    {
        $stmt = $pdo->prepare("DELETE FROM jadwal_guru_piket WHERE id_jadwal_piket = ?");
        return $stmt->execute([$id_jadwal_piket]);
    }

    /**
     * Mengambil matriks jadwal kerja GTK (KBM + Piket + Non-KBM / Ngantor)
     */
    public static function getGtkWorkMatrix($pdo, $id_ta)
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // 1. Ambil semua GTK aktif
        $sql_gtk = "
            SELECT g.id_guru, g.id_pengguna, g.nama, g.kode_guru, g.nuptk, g.status_kepegawaian,
                   GROUP_CONCAT(DISTINCT pr.nama_peran SEPARATOR ', ') as peran_list
            FROM guru g
            LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
            LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
            LEFT JOIN peran pr ON pp.id_peran = pr.id_peran
            WHERE g.status = 'Aktif'
            GROUP BY g.id_guru
            ORDER BY g.nama ASC
        ";
        $gtk_list = $pdo->query($sql_gtk)->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil hari mengajar KBM per guru di TA ini
        $sql_kbm = "
            SELECT gm.id_guru, jm.hari_kbm, COUNT(DISTINCT jm.id_kelas) as total_kelas,
                   GROUP_CONCAT(DISTINCT m.nama_mapel SEPARATOR ', ') as mapel_list,
                   GROUP_CONCAT(DISTINCT k.nama_kelas SEPARATOR ', ') as kelas_list
            FROM jadwal_mengajar jm
            JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            JOIN kelas k ON jm.id_kelas = k.id_kelas
            WHERE gm.id_ta = ?
            GROUP BY gm.id_guru, jm.hari_kbm
        ";
        $stmt = $pdo->prepare($sql_kbm);
        $stmt->execute([$id_ta]);
        $kbm_map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $kbm_map[$r['id_guru']][$r['hari_kbm']] = $r;
        }

        // 3. Ambil hari piket per guru di TA ini
        $sql_piket = "
            SELECT id_jadwal_piket, id_guru, hari, keterangan
            FROM jadwal_guru_piket
            WHERE id_ta = ?
        ";
        $stmt = $pdo->prepare($sql_piket);
        $stmt->execute([$id_ta]);
        $piket_map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $piket_map[$r['id_guru']][$r['hari']] = $r;
        }

        // 4. Ambil hari non-KBM / ngantor per guru di TA ini
        $sql_non_kbm = "
            SELECT id_jadwal_non_kbm, id_guru, hari, jenis_tugas, keterangan
            FROM jadwal_guru_non_kbm
            WHERE id_ta = ?
        ";
        $stmt = $pdo->prepare($sql_non_kbm);
        $stmt->execute([$id_ta]);
        $non_kbm_map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $non_kbm_map[$r['id_guru']][$r['hari']] = $r;
        }

        // Susun matrix
        $matrix = [];
        foreach ($gtk_list as $g) {
            $row = [
                'id_guru' => $g['id_guru'],
                'nama' => $g['nama'],
                'kode_guru' => $g['kode_guru'],
                'nuptk' => $g['nuptk'],
                'peran' => $g['peran_list'] ?: 'Guru',
                'is_tendik' => (strpos($g['peran_list'] ?? '', 'TU') !== false || strpos($g['peran_list'] ?? '', 'Tenaga Kependidikan') !== false),
                'days' => []
            ];

            foreach ($days as $day) {
                $has_kbm = isset($kbm_map[$g['id_guru']][$day]);
                $has_piket = isset($piket_map[$g['id_guru']][$day]);
                $has_non_kbm = isset($non_kbm_map[$g['id_guru']][$day]);

                $row['days'][$day] = [
                    'kbm' => $has_kbm ? $kbm_map[$g['id_guru']][$day] : null,
                    'piket' => $has_piket ? $piket_map[$g['id_guru']][$day] : null,
                    'non_kbm' => $has_non_kbm ? $non_kbm_map[$g['id_guru']][$day] : null,
                    'is_wajib' => ($has_kbm || $has_piket || $has_non_kbm || $row['is_tendik'])
                ];
            }
            $matrix[] = $row;
        }

        return $matrix;
    }

    /**
     * Menyimpan batch jadwal non-KBM / ngantor GTK
     */
    public static function saveNonKbmBatch($pdo, $id_ta, $non_kbm_entries)
    {
        $pdo->beginTransaction();
        try {
            // Hapus jadwal non-KBM sebelumnya untuk TA ini
            $stmt_del = $pdo->prepare("DELETE FROM jadwal_guru_non_kbm WHERE id_ta = ?");
            $stmt_del->execute([$id_ta]);

            // Insert baru
            if (!empty($non_kbm_entries)) {
                $sql_ins = "INSERT INTO jadwal_guru_non_kbm (id_ta, id_guru, hari, jenis_tugas, keterangan) VALUES (?, ?, ?, ?, ?)";
                $stmt_ins = $pdo->prepare($sql_ins);
                foreach ($non_kbm_entries as $entry) {
                    $stmt_ins->execute([
                        $id_ta,
                        $entry['id_guru'],
                        $entry['hari'],
                        $entry['jenis_tugas'] ?? 'Ngantor / Standby',
                        $entry['keterangan'] ?? null
                    ]);
                }
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
