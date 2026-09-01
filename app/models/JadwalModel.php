<?php
class JadwalModel {
    
    public static function all_guru_mapel($pdo, $id_ta) {
        $stmt = $pdo->prepare(
            "SELECT gm.id_guru_mapel, g.nama AS nama_guru, m.nama_mapel, gm.id_kelas
                FROM guru_mapel gm
                JOIN guru g ON gm.id_guru=g.id_guru
                JOIN mapel m ON gm.id_mapel=m.id_mapel
                WHERE gm.id_ta=?
                ORDER BY g.nama, m.nama_mapel ASC");
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all_guru_unique($pdo, $id_ta) {
        $stmt = $pdo->prepare(
            "SELECT DISTINCT g.id_guru, g.nama AS nama_guru
             FROM guru g
             JOIN guru_mapel gm ON g.id_guru = gm.id_guru
             WHERE gm.id_ta = ?
             ORDER BY g.nama ASC"
        );
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function all_kelas($pdo, $id_ta = null) {
        if ($id_ta) {
            $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas ASC");
            $stmt->execute([$id_ta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pdo->query("SELECT * FROM kelas ORDER BY tingkat, nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all_jam($pdo) {
        return $pdo->query("SELECT jp.*, mk.nama_kegiatan, 
                            COALESCE(mk.jenis_kegiatan, jp.jenis_kegiatan) as jenis_kegiatan, 
                            jp.hari_pelaksanaan 
                            FROM jam_pelajaran jp 
                            LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan 
                            ORDER BY jp.urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * REVISI: getKbmJamSlots()
     * Memastikan 'label_jam_ke' diambil untuk dropdown form.
     */
    public static function getKbmJamSlots($pdo, $day = null) {
        $sql = "SELECT jp.id_jam, jp.urutan, jp.jam_mulai, jp.jam_selesai, jp.label_jam_ke, 
                       mk.nama_kegiatan, mk.jenis_kegiatan, jp.hari_pelaksanaan
                FROM jam_pelajaran jp 
                LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan 
                WHERE (jp.jenis_kegiatan = 'KBM' OR mk.jenis_kegiatan = 'KBM')";
        
        if ($day) {
            $sql .= " AND FIND_IN_SET(?, jp.hari_pelaksanaan)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$day]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ⭐ REVISI BESAR: getJadwalLengkap()
     * 1. MENAMBAHKAN 'jp.label_jam_ke' ke SELECT.
     * 2. Filter 'guru' menggunakan gm.id_guru (menampilkan seluruh jadwal guru di semua kelas).
     */
    public static function getJadwalLengkap($pdo, $id_ta, $tipe_tampilan = 'kelas', $filter_kelas = null, $filter_guru = null) {
        $hari_urutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jadwal_lengkap = [];

        $filter_kelas_jenis = null;
        if ($tipe_tampilan == 'kelas' && $filter_kelas) {
            $stmt_jk = $pdo->prepare("SELECT jenis_kelas FROM kelas WHERE id_kelas = ?");
            $stmt_jk->execute([$filter_kelas]);
            $filter_kelas_jenis = $stmt_jk->fetchColumn();
        }

        foreach ($hari_urutan as $hari) {
            $jadwal_lengkap[$hari] = [];

            $sql = "SELECT 
                        jp.urutan, jp.jam_mulai, jp.jam_selesai, jp.label_jam_ke,
                        jp.jenis_kegiatan AS jenis_jam_pelajaran,
                        mk.nama_kegiatan, mk.jenis_kegiatan AS jenis_master_kegiatan, mk.hari_pelaksanaan,
                        dm.id_jadwal_mengajar, dm.hari_kbm, dm.mode_kbm,
                        k.tingkat, k.nama_kelas, k.jenis_kelas,
                        m.nama_mapel,
                        g.nama AS nama_guru, g.jenis_guru,
                        NULLIF(jp.nama_kegiatan_custom, '') AS nama_kegiatan_custom
                    FROM jam_pelajaran jp
                    LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan
                    LEFT JOIN jadwal_mengajar dm ON jp.id_jam = dm.id_jam AND dm.hari_kbm = ?
                    LEFT JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel AND gm.id_ta = ?
                    LEFT JOIN guru g ON gm.id_guru = g.id_guru
                    LEFT JOIN mapel m ON gm.id_mapel = m.id_mapel
                    LEFT JOIN kelas k ON dm.id_kelas = k.id_kelas
                    WHERE FIND_IN_SET(?, jp.hari_pelaksanaan) ";
            
            $params = [$hari, $id_ta, $hari];

            if ($tipe_tampilan == 'kelas' && $filter_kelas) {
                $sql .= " AND (dm.id_kelas = ? OR jp.jenis_kegiatan != 'KBM')";
                $params[] = $filter_kelas;
            } elseif ($tipe_tampilan == 'guru' && $filter_guru) {
                $sql .= " AND (gm.id_guru = ? OR jp.jenis_kegiatan != 'KBM')";
                $params[] = $filter_guru;
            }

            $sql .= " ORDER BY jp.urutan ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw_jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Logika untuk menampilkan KBM atau Non-KBM + Merge jam berurutan
            $merged_slots = [];
            $current = null;

            foreach ($raw_jadwal as $slot) {
                $is_kbm_slot = ($slot['jenis_jam_pelajaran'] == 'KBM' || $slot['jenis_master_kegiatan'] == 'KBM');
                if ($is_kbm_slot) {
                    if (!$slot['id_jadwal_mengajar']) continue;
                } else {
                    // Jika sedang memfilter kelas PJJ, sembunyikan Non-KBM reguler sekolah induk (Dhuha, Dzuhur, dsb)
                    if ($filter_kelas_jenis === 'pjj') {
                        continue;
                    }
                    $hari_pelaksanaan_arr = explode(',', $slot['hari_pelaksanaan'] ?? '');
                    if (!empty($slot['hari_pelaksanaan']) && !in_array($hari, $hari_pelaksanaan_arr)) {
                        continue;
                    }
                }

                $signature = $is_kbm_slot
                    ? ('KBM|' . ($slot['nama_mapel'] ?? '') . '|' . ($slot['nama_kelas'] ?? '') . ($tipe_tampilan == 'guru' ? ('|' . ($slot['nama_guru'] ?? '')) : ''))
                    : ('NON_KBM|' . ($slot['nama_kegiatan'] ?? '') . '|' . ($slot['jenis_master_kegiatan'] ?? '') . '|' . ($slot['nama_kegiatan_custom'] ?? ''));

                if ($current === null) {
                    $current = $slot;
                    $current['signature'] = $signature;
                    $current['jp_count'] = 1;
                    $current['jam_mulai'] = $slot['jam_mulai'];
                    $current['jam_selesai'] = $slot['jam_selesai'];
                    $current['ids_jadwal_mengajar'] = [$slot['id_jadwal_mengajar']];
                    $current['guru_list'] = $slot['nama_guru'] ? [$slot['nama_guru']] : [];
                } else {
                    if ($current['signature'] === $signature) {
                        $current['jam_selesai'] = $slot['jam_selesai'];
                        $current['jp_count']++;
                        $current['ids_jadwal_mengajar'][] = $slot['id_jadwal_mengajar'];
                        if ($slot['nama_guru'] && !in_array($slot['nama_guru'], $current['guru_list'])) {
                            $current['guru_list'][] = $slot['nama_guru'];
                        }
                    } else {
                        $current['nama_guru'] = !empty($current['guru_list']) ? implode(', ', $current['guru_list']) : ($current['nama_guru'] ?? '-');
                        $merged_slots[] = $current;
                        $current = $slot;
                        $current['signature'] = $signature;
                        $current['jp_count'] = 1;
                        $current['jam_mulai'] = $slot['jam_mulai'];
                        $current['jam_selesai'] = $slot['jam_selesai'];
                        $current['ids_jadwal_mengajar'] = [$slot['id_jadwal_mengajar']];
                        $current['guru_list'] = $slot['nama_guru'] ? [$slot['nama_guru']] : [];
                    }
                }
            }

            if ($current !== null) {
                $current['nama_guru'] = !empty($current['guru_list']) ? implode(', ', $current['guru_list']) : ($current['nama_guru'] ?? '-');
                $merged_slots[] = $current;
            }

            $jadwal_lengkap[$hari] = $merged_slots;
        }

        return $jadwal_lengkap;
    }

    /**
     * REVISI TOTAL: Fungsi Save Jadwal dengan Mode KBM (Online/Offline) & Validasi Cerdas
     */
    public static function jadwal_save($pdo, $data) {
        $id_guru_mapel = (int)$data['id_guru_mapel'];
        $id_kelas = (int)$data['id_kelas'];
        $hari_kbm = $data['hari_kbm'];
        $mode_kbm = $data['mode_kbm'] ?? 'offline';
        if (!in_array($mode_kbm, ['offline', 'online'])) {
            $mode_kbm = 'offline';
        }

        // id_jam may be a single value or an array (from jam[]). Normalize to array.
        $selected_jams = is_array($data['id_jam']) ? $data['id_jam'] : (empty($data['id_jam']) ? [] : [$data['id_jam']]);
        $id_ta = $data['id_ta'];

        if (empty($selected_jams)) {
            throw new Exception("Pilih minimal 1 jam untuk menyimpan jadwal.");
        }

        // --- 1. AMBIL DATA PENDUKUNG ---
        $stmt_gm = $pdo->prepare("SELECT gm.id_guru, gm.id_mapel, g.jenis_guru FROM guru_mapel gm JOIN guru g ON gm.id_guru = g.id_guru WHERE gm.id_guru_mapel = ?");
        $stmt_gm->execute([$id_guru_mapel]);
        $gm_data = $stmt_gm->fetch(PDO::FETCH_ASSOC);
        
        if (!$gm_data) {
            throw new Exception("Data Penugasan Guru Mapel tidak valid.");
        }
        $id_guru = $gm_data['id_guru'];
        $id_mapel = $gm_data['id_mapel'];
        $jenis_guru = $gm_data['jenis_guru'] ?? 'reguler';

        $stmt_k = $pdo->prepare("SELECT tingkat, jenis_kelas FROM kelas WHERE id_kelas = ?");
        $stmt_k->execute([$id_kelas]);
        $k_info = $stmt_k->fetch(PDO::FETCH_ASSOC);
        $tingkat_kelas = $k_info['tingkat'] ?? '';

        // --- 2. VALIDASI JJM (STRUKTUR KURIKULUM) ---
        $stmt_max_jjm = $pdo->prepare("SELECT alokasi_jp_minggu FROM struktur_kurikulum WHERE id_mapel = ? AND tingkat = ? AND id_ta = ?");
        $stmt_max_jjm->execute([$id_mapel, $tingkat_kelas, $id_ta]);
        $max_jjm = $stmt_max_jjm->fetchColumn();

        if ($max_jjm !== false) {
            $stmt_terjadwal = $pdo->prepare(
                 "SELECT COUNT(d.id_jadwal_mengajar) 
                 FROM jadwal_mengajar d
                 JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                 WHERE d.id_kelas = ? AND gm.id_mapel = ? AND gm.id_ta = ?"
            );
            $stmt_terjadwal->execute([$id_kelas, $id_mapel, $id_ta]);
            $jjm_terjadwal = (int) $stmt_terjadwal->fetchColumn();

            $to_add = count($selected_jams);
            if (($jjm_terjadwal + $to_add) > (int)$max_jjm) {
                throw new Exception("JJM Penuh! Alokasi jam untuk mapel ini di kelas ini sudah terpenuhi (terjadwal: $jjm_terjadwal, alokasi: $max_jjm). Tambahan $to_add jam akan melebihi alokasi.");
            }
        }
        
        // --- 3. VALIDASI BENTROK & SIMPAN DATA ---
        $stmt_insert = $pdo->prepare("INSERT INTO jadwal_mengajar (id_guru_mapel, id_kelas, hari_kbm, id_jam, mode_kbm) VALUES (?,?,?,?,?)");

        $sql_cek_guru = "SELECT k.nama_kelas 
                 FROM jadwal_mengajar d
                 JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                 JOIN kelas k ON d.id_kelas = k.id_kelas
                 WHERE gm.id_guru = ? AND d.hari_kbm = ? AND d.id_jam = ? AND gm.id_ta = ? AND d.mode_kbm = 'offline'";
        $stmt_cek_guru = $pdo->prepare($sql_cek_guru);

        $sql_cek_kelas = "SELECT g.nama 
                  FROM jadwal_mengajar d
                  JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                  JOIN guru g ON gm.id_guru = g.id_guru
                  WHERE d.id_kelas = ? AND d.hari_kbm = ? AND d.id_jam = ? AND gm.id_ta = ?";
        $stmt_cek_kelas = $pdo->prepare($sql_cek_kelas);

        // Bypass bentrok guru jika mode Online LMS atau akun Koordinator
        $bypass_bentrok_guru = ($mode_kbm === 'online') || ($jenis_guru === 'koordinator_pjj') || ($jenis_guru === 'koordinator_menginduk');

        foreach ($selected_jams as $jam_to_insert) {
            $jam_to_insert = (int)$jam_to_insert;

            // Re-check teacher clash jika bukan online/koordinator
            if (!$bypass_bentrok_guru) {
                $stmt_cek_guru->execute([$id_guru, $hari_kbm, $jam_to_insert, $id_ta]);
                $bentrok_guru = $stmt_cek_guru->fetch(PDO::FETCH_ASSOC);
                if ($bentrok_guru) {
                    throw new Exception("Bentrok Jadwal Guru! Guru sudah mengajar di kelas " . $bentrok_guru['nama_kelas'] . " pada hari dan jam yang sama (jam id: $jam_to_insert).");
                }
            }

            // Re-check kelas clash for this jam
            $stmt_cek_kelas->execute([$id_kelas, $hari_kbm, $jam_to_insert, $id_ta]);
            $bentrok_kelas = $stmt_cek_kelas->fetch(PDO::FETCH_ASSOC);
            if ($bentrok_kelas) {
                throw new Exception("Bentrok Jadwal Kelas! Kelas ini sudah diisi oleh " . $bentrok_kelas['nama'] . " pada hari dan jam yang sama (jam id: $jam_to_insert).");
            }

            // Insert row
            $stmt_insert->execute([$id_guru_mapel, $id_kelas, $hari_kbm, $jam_to_insert, $mode_kbm]);
        }
    }


    public static function jadwal_delete($pdo, $id_detail) {
        if (is_string($id_detail) && strpos($id_detail, ',') !== false) {
            $ids = array_filter(array_map('intval', explode(',', $id_detail)));
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("DELETE FROM jadwal_mengajar WHERE id_jadwal_mengajar IN ($placeholders)");
                $stmt->execute($ids);
                return;
            }
        }
        $stmt = $pdo->prepare("DELETE FROM jadwal_mengajar WHERE id_jadwal_mengajar=?");
        $stmt->execute([(int)$id_detail]);
    }

    /**
     * Mengambil ID Jam yang sudah terisi untuk kelas dan hari tertentu
     */
    public static function getOccupiedSlots($pdo, $id_kelas, $hari_kbm, $id_ta) {
        $stmt = $pdo->prepare("SELECT id_jam FROM jadwal_mengajar 
                               WHERE id_kelas = ? AND hari_kbm = ? 
                               AND id_guru_mapel IN (SELECT id_guru_mapel FROM guru_mapel WHERE id_ta = ?)");
        $stmt->execute([$id_kelas, $hari_kbm, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    /* ================================================================
     FUNGSI UNTUK LAPORAN CETAK GRID (KESELURUHAN)
    ================================================================
    */
    public static function getJadwalGridData($pdo, $id_ta) {
        $sql = "SELECT 
                    d.hari_kbm, d.id_jam, d.id_kelas,
                    g.kode_guru, m.kode_mapel
            FROM jadwal_mengajar d
                JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                WHERE gm.id_ta = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $grid = [];
        foreach ($data as $row) {
            $grid[$row['hari_kbm']][$row['id_jam']][$row['id_kelas']] = [
                'kode_guru' => $row['kode_guru'],
                'kode_mapel' => $row['kode_mapel']
            ];
        }
        return $grid;
    }

    public static function getJadwalLegends($pdo, $id_ta) {
        $sql_guru = "SELECT DISTINCT g.id_guru, g.nama, g.kode_guru 
                     FROM guru g
                     JOIN guru_mapel gm ON g.id_guru = gm.id_guru
                     WHERE gm.id_ta = ? AND g.kode_guru IS NOT NULL AND g.kode_guru != ''
                     ORDER BY CAST(g.kode_guru AS UNSIGNED) ASC, g.nama ASC"; 
        $stmt_guru = $pdo->prepare($sql_guru);
        $stmt_guru->execute([$id_ta]);
        $guru_legend = $stmt_guru->fetchAll(PDO::FETCH_ASSOC);

        $sql_mapel = "SELECT DISTINCT m.id_mapel, m.nama_mapel, m.kode_mapel
                      FROM mapel m
                      JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel
                      WHERE gm.id_ta = ? AND m.kode_mapel IS NOT NULL AND m.kode_mapel != ''
                      ORDER BY m.kode_mapel ASC"; 
        $stmt_mapel = $pdo->prepare($sql_mapel);
        $stmt_mapel->execute([$id_ta]);
        $mapel_legend = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'guru' => $guru_legend,
            'mapel' => $mapel_legend
        ];
    }
}
?>