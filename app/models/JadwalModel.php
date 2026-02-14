<?php
class JadwalModel {
    
    public static function all_guru_mapel($pdo, $id_ta) {
        $stmt = $pdo->prepare(
            "SELECT gm.id_guru_mapel, g.nama AS nama_guru, m.nama_mapel
                FROM guru_mapel gm
                JOIN guru g ON gm.id_guru=g.id_guru
                JOIN mapel m ON gm.id_mapel=m.id_mapel
                WHERE gm.id_ta=?
                ORDER BY g.nama, m.nama_mapel ASC");
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
        // FIX: Use COALESCE to fallback to jp.jenis_kegiatan if mk.jenis_kegiatan is NULL
        // This prevents standard KBM slots (where id_kegiatan is NULL) from having NULL tipo.
        return $pdo->query("SELECT jp.*, mk.nama_kegiatan, 
                            COALESCE(mk.jenis_kegiatan, jp.jenis_kegiatan) as jenis_kegiatan, 
                            mk.hari_pelaksanaan 
                            FROM jam_pelajaran jp 
                            LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan 
                            ORDER BY jp.urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * REVISI: getKbmJamSlots()
     * Memastikan 'label_jam_ke' diambil untuk dropdown form.
     */
    public static function getKbmJamSlots($pdo) {
        return $pdo->query("SELECT jp.id_jam, jp.urutan, jp.jam_mulai, jp.jam_selesai, jp.label_jam_ke, 
                                  mk.nama_kegiatan, mk.jenis_kegiatan 
                            FROM jam_pelajaran jp 
                            LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan 
                            WHERE jp.jenis_kegiatan = 'KBM' OR mk.jenis_kegiatan = 'KBM'
                            ORDER BY jp.urutan ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ⭐ REVISI BESAR: getJadwalLengkap()
     * 1. MENAMBAHKAN 'jp.label_jam_ke' ke SELECT.
     * 2. Memperbaiki logika query SQL untuk filter 'guru' (menggunakan dm.id_guru_mapel).
     */
    public static function getJadwalLengkap($pdo, $id_ta, $tipe_tampilan = 'sekolah', $filter_kelas = null, $filter_guru_mapel = null) {
        $hari_urutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwal_lengkap = [];

        foreach ($hari_urutan as $hari) {
            $jadwal_lengkap[$hari] = [];

            $sql = "SELECT 
                        jp.urutan, jp.jam_mulai, jp.jam_selesai, jp.label_jam_ke, /* <-- DITAMBAHKAN */
                        jp.jenis_kegiatan AS jenis_jam_pelajaran,
                        mk.nama_kegiatan, mk.jenis_kegiatan AS jenis_master_kegiatan, mk.hari_pelaksanaan,
                        dm.id_jadwal_mengajar, dm.hari_kbm,
                        k.tingkat, k.nama_kelas,
                        m.nama_mapel,
                        g.nama AS nama_guru,
                        jp.nama_kegiatan_custom
                    FROM jam_pelajaran jp
                    LEFT JOIN master_kegiatan mk ON jp.id_kegiatan = mk.id_kegiatan
                    LEFT JOIN jadwal_mengajar dm ON jp.id_jam = dm.id_jam AND dm.hari_kbm = ?
                    LEFT JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel AND gm.id_ta = ?
                    LEFT JOIN guru g ON gm.id_guru = g.id_guru
                    LEFT JOIN mapel m ON gm.id_mapel = m.id_mapel
                    LEFT JOIN kelas k ON dm.id_kelas = k.id_kelas
                    WHERE 1=1 ";
            
            $params = [$hari, $id_ta];

            if ($tipe_tampilan == 'kelas' && $filter_kelas) {
                $sql .= " AND (dm.id_kelas = ? OR jp.jenis_kegiatan != 'KBM')";
                $params[] = $filter_kelas;
            } elseif ($tipe_tampilan == 'guru' && $filter_guru_mapel) {
                $sql .= " AND (dm.id_guru_mapel = ? OR jp.jenis_kegiatan != 'KBM')";
                $params[] = $filter_guru_mapel;
            }

            $sql .= " ORDER BY jp.urutan ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $raw_jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Logika untuk menampilkan KBM atau Non-KBM
            foreach ($raw_jadwal as $slot) {
                $is_kbm_slot = ($slot['jenis_jam_pelajaran'] == 'KBM' || $slot['jenis_master_kegiatan'] == 'KBM');
                $is_non_kbm_slot = !$is_kbm_slot;

                if ($is_kbm_slot) {
                    if ($slot['id_jadwal_mengajar']) {
                            $jadwal_lengkap[$hari][] = $slot;
                        }
                } else {
                    $hari_pelaksanaan_arr = explode(',', $slot['hari_pelaksanaan'] ?? '');
                    if (empty($slot['hari_pelaksanaan']) || in_array($hari, $hari_pelaksanaan_arr)) {
                        $jadwal_lengkap[$hari][] = $slot;
                    }
                }
            }
        }
        return $jadwal_lengkap;
    }
    
    
    /**
     * REVISI TOTAL: Fungsi Save Jadwal dengan Validasi Bentrok & JJM
     */
    public static function jadwal_save($pdo, $data) {
        
        $id_guru_mapel = $data['id_guru_mapel'];
        $id_kelas = $data['id_kelas'];
        $hari_kbm = $data['hari_kbm'];
        // id_jam may be a single value or an array (from jam[]). Normalize to array.
        $selected_jams = is_array($data['id_jam']) ? $data['id_jam'] : (empty($data['id_jam']) ? [] : [$data['id_jam']]);
        $id_ta = $data['id_ta'];

        if (empty($selected_jams)) {
            throw new Exception("Pilih minimal 1 jam untuk menyimpan jadwal.");
        }

        // --- 1. AMBIL DATA PENDUKUNG ---
        $stmt_gm = $pdo->prepare("SELECT id_guru, id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
        $stmt_gm->execute([$id_guru_mapel]);
        $gm_data = $stmt_gm->fetch(PDO::FETCH_ASSOC);
        
        if (!$gm_data) {
            throw new Exception("Data Penugasan Guru Mapel tidak valid.");
        }
        $id_guru = $gm_data['id_guru'];
        $id_mapel = $gm_data['id_mapel'];

        $stmt_k = $pdo->prepare("SELECT tingkat FROM kelas WHERE id_kelas = ?");
        $stmt_k->execute([$id_kelas]);
        $tingkat_kelas = $stmt_k->fetchColumn();

        // --- 2. VALIDASI BENTROK (CLASH DETECTION) ---
        
        // A. Cek Bentrok Guru
        $sql_cek_guru = "SELECT k.nama_kelas 
                 FROM jadwal_mengajar d
                 JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                 JOIN kelas k ON d.id_kelas = k.id_kelas
                 WHERE gm.id_guru = ? AND d.hari_kbm = ? AND d.id_jam = ? AND gm.id_ta = ?";
        $stmt_cek_guru = $pdo->prepare($sql_cek_guru);
        $stmt_cek_guru->execute([$id_guru, $hari_kbm, $id_jam, $id_ta]);
        $bentrok_guru = $stmt_cek_guru->fetch(PDO::FETCH_ASSOC);
        
        if ($bentrok_guru) {
            throw new Exception("Bentrok Jadwal Guru! Guru tersebut sudah mengajar di kelas " . $bentrok_guru['nama_kelas'] . " pada hari dan jam yang sama.");
        }

        // B. Cek Bentrok Kelas
        $sql_cek_kelas = "SELECT g.nama 
                  FROM jadwal_mengajar d
                  JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                  JOIN guru g ON gm.id_guru = g.id_guru
                  WHERE d.id_kelas = ? AND d.hari_kbm = ? AND d.id_jam = ? AND gm.id_ta = ?";
        $stmt_cek_kelas = $pdo->prepare($sql_cek_kelas);
        $stmt_cek_kelas->execute([$id_kelas, $hari_kbm, $id_jam, $id_ta]);
        $bentrok_kelas = $stmt_cek_kelas->fetch(PDO::FETCH_ASSOC);

        if ($bentrok_kelas) {
            throw new Exception("Bentrok Jadwal Kelas! Kelas ini sudah diisi oleh " . $bentrok_kelas['nama'] . " pada hari dan jam yang sama.");
        }

        // --- 3. VALIDASI JJM (STRUKTUR KURIKULUM) ---
        
        // A. Cek JJM Maksimal
        $stmt_max_jjm = $pdo->prepare("SELECT alokasi_jp_minggu FROM struktur_kurikulum WHERE id_mapel = ? AND tingkat = ? AND id_ta = ?");
        $stmt_max_jjm->execute([$id_mapel, $tingkat_kelas, $id_ta]);
        $max_jjm = $stmt_max_jjm->fetchColumn();

        if ($max_jjm === false) {
            throw new Exception("JJM Penuh! Mapel ini belum diatur di Struktur Kurikulum untuk tingkat $tingkat_kelas.");
        } else {
            // B. Cek JJM Terjadwal (existing count)
            $stmt_terjadwal = $pdo->prepare(
                 "SELECT COUNT(d.id_jadwal_mengajar) 
                 FROM jadwal_mengajar d
                 JOIN guru_mapel gm ON d.id_guru_mapel = gm.id_guru_mapel
                 WHERE d.id_kelas = ? AND gm.id_mapel = ? AND gm.id_ta = ?"
            );
            $stmt_terjadwal->execute([$id_kelas, $id_mapel, $id_ta]);
            $jjm_terjadwal = (int) $stmt_terjadwal->fetchColumn();

            // If adding multiple jams, ensure total will not exceed allocation
            $to_add = count($selected_jams);
            if (($jjm_terjadwal + $to_add) > (int)$max_jjm) {
                throw new Exception("JJM Penuh! Alokasi jam untuk mapel ini di kelas ini sudah terpenuhi (terjadwal: $jjm_terjadwal, alokasi: $max_jjm). Tambahan $to_add jam akan melebihi alokasi.");
            }
        }
        
        // --- 4. LOLOS SEMUA VALIDASI, SIMPAN DATA ---
        $stmt_insert = $pdo->prepare("INSERT INTO jadwal_mengajar (id_guru_mapel, id_kelas, hari_kbm, id_jam) VALUES (?,?,?,?)");

        // For each selected jam, re-check clashes per jam (teacher & class), then insert
        foreach ($selected_jams as $jam_to_insert) {
            // Normalize to integer
            $jam_to_insert = (int)$jam_to_insert;

            // Re-check teacher clash for this jam
            $stmt_cek_guru->execute([$id_guru, $hari_kbm, $jam_to_insert, $id_ta]);
            $bentrok_guru = $stmt_cek_guru->fetch(PDO::FETCH_ASSOC);
            if ($bentrok_guru) {
                throw new Exception("Bentrok Jadwal Guru! Guru sudah mengajar di kelas " . $bentrok_guru['nama_kelas'] . " pada hari dan jam yang sama (jam id: $jam_to_insert).");
            }

            // Re-check kelas clash for this jam
            $stmt_cek_kelas->execute([$id_kelas, $hari_kbm, $jam_to_insert, $id_ta]);
            $bentrok_kelas = $stmt_cek_kelas->fetch(PDO::FETCH_ASSOC);
            if ($bentrok_kelas) {
                throw new Exception("Bentrok Jadwal Kelas! Kelas ini sudah diisi oleh " . $bentrok_kelas['nama'] . " pada hari dan jam yang sama (jam id: $jam_to_insert).");
            }

            // Insert row
            $stmt_insert->execute([$id_guru_mapel, $id_kelas, $hari_kbm, $jam_to_insert]);
        }
    }


    public static function jadwal_delete($pdo, $id_detail) {
        $stmt = $pdo->prepare("DELETE FROM jadwal_mengajar WHERE id_jadwal_mengajar=?");
        $stmt->execute([$id_detail]);
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