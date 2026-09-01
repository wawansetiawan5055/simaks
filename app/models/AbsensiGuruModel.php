<?php
class AbsensiGuruModel {

    /**
     * Mengambil GTK yang memiliki kewajiban hadir FISIK pada tanggal tertentu
     * (KBM Tatap Muka/Offline, Guru Piket, Hari Ngantor / Non-KBM, atau Staf TU / Tendik)
     */
    public static function getGuruWithScheduleOnDate($pdo, $tanggal, $id_ta) {
        $dayOfWeek = date('w', strtotime($tanggal));
        $hari_map = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari_kbm = $hari_map[$dayOfWeek];

        $sql = "
            SELECT 
                g.id_guru, 
                g.nama,
                g.kode_guru,
                g.nuptk,
                GROUP_CONCAT(DISTINCT pr.nama_peran SEPARATOR ', ') as peran_list,
                GROUP_CONCAT(
                    DISTINCT CASE 
                        WHEN dm.id_jadwal_mengajar IS NOT NULL 
                             AND (dm.mode_kbm = 'offline' OR dm.mode_kbm IS NULL) 
                             AND (k.jenis_kelas != 'pjj' OR k.jenis_kelas IS NULL)
                        THEN CONCAT(TIME_FORMAT(jp.jam_mulai, '%H:%i'), '-', TIME_FORMAT(jp.jam_selesai, '%H:%i'), ' (', k.nama_kelas, ' - ', m.nama_mapel, ')') 
                        ELSE NULL 
                    END
                    ORDER BY jp.jam_mulai
                    SEPARATOR ' <br> '
                ) AS jadwal_hari_ini,
                MAX(CASE WHEN jgp.id_jadwal_piket IS NOT NULL THEN 1 ELSE 0 END) AS is_piket,
                MAX(jgp.keterangan) AS keterangan_piket,
                MAX(CASE WHEN jnk.id_jadwal_non_kbm IS NOT NULL THEN 1 ELSE 0 END) AS is_non_kbm,
                MAX(jnk.jenis_tugas) AS jenis_tugas_non_kbm,
                MAX(CASE WHEN pr.nama_peran IN ('TU', 'Tenaga Kependidikan') AND pr.nama_peran NOT IN ('Guru', 'Admin') THEN 1 ELSE 0 END) AS is_tendik
            FROM guru g
            LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
            LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
            LEFT JOIN peran pr ON pp.id_peran = pr.id_peran
            LEFT JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = ?
            LEFT JOIN jadwal_mengajar dm ON gm.id_guru_mapel = dm.id_guru_mapel AND dm.hari_kbm = ? AND (dm.mode_kbm = 'offline' OR dm.mode_kbm IS NULL)
            LEFT JOIN kelas k ON dm.id_kelas = k.id_kelas AND (k.jenis_kelas != 'pjj' OR k.jenis_kelas IS NULL)
            LEFT JOIN mapel m ON gm.id_mapel = m.id_mapel
            LEFT JOIN jam_pelajaran jp ON dm.id_jam = jp.id_jam
            LEFT JOIN jadwal_guru_piket jgp ON g.id_guru = jgp.id_guru AND jgp.id_ta = ? AND jgp.hari = ?
            LEFT JOIN jadwal_guru_non_kbm jnk ON g.id_guru = jnk.id_guru AND jnk.id_ta = ? AND jnk.hari = ?
            WHERE g.status = 'Aktif'
            GROUP BY g.id_guru
            HAVING (jadwal_hari_ini IS NOT NULL OR is_piket = 1 OR is_non_kbm = 1 OR is_tendik = 1)
            ORDER BY is_tendik ASC, g.nama ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $hari_kbm, $id_ta, $hari_kbm, $id_ta, $hari_kbm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil daftar Guru yang memiliki jadwal KBM DARING / ONLINE LMS pada tanggal tertentu
     * Menggabungkan kelas online yang diajar pada slot jam yang sama menjadi 1 baris (merger KBM)
     */
    public static function getGuruOnlineScheduleOnDate($pdo, $tanggal, $id_ta) {
        $dayOfWeek = date('w', strtotime($tanggal));
        $hari_map = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari_kbm = $hari_map[$dayOfWeek];

        $sql = "
            SELECT 
                g.id_guru, 
                g.nama AS nama_guru,
                g.kode_guru,
                k.id_kelas,
                k.nama_kelas,
                k.jenis_kelas,
                m.id_mapel,
                m.nama_mapel,
                gm.id_guru_mapel,
                jp.jam_mulai,
                jp.jam_selesai,
                dm.id_jam
            FROM guru g
            JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = ?
            JOIN jadwal_mengajar dm ON gm.id_guru_mapel = dm.id_guru_mapel AND dm.hari_kbm = ?
            JOIN kelas k ON dm.id_kelas = k.id_kelas AND (dm.mode_kbm = 'online' OR k.jenis_kelas = 'pjj')
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            JOIN jam_pelajaran jp ON dm.id_jam = jp.id_jam
            WHERE g.status = 'Aktif'
            ORDER BY jp.jam_mulai ASC, g.nama ASC, k.nama_kelas ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $hari_kbm]);
        $raw_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by [id_guru]_[id_mapel]_[jam_mulai]_[jam_selesai]
        $grouped = [];
        foreach ($raw_rows as $r) {
            $key = $r['id_guru'] . '_' . $r['id_mapel'] . '_' . $r['jam_mulai'] . '_' . $r['jam_selesai'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id_guru' => $r['id_guru'],
                    'nama_guru' => $r['nama_guru'],
                    'kode_guru' => $r['kode_guru'],
                    'id_mapel' => $r['id_mapel'],
                    'nama_mapel' => $r['nama_mapel'],
                    'jam_mulai' => $r['jam_mulai'],
                    'jam_selesai' => $r['jam_selesai'],
                    'jp_count' => 0,
                    'kelas_map' => []
                ];
            }
            $id_k = $r['id_kelas'];
            if (!isset($grouped[$key]['kelas_map'][$id_k])) {
                // Cek Jurnal KBM
                $stmt_j = $pdo->prepare("SELECT id_jurnal, tujuan_pembelajaran FROM jurnal_kbm WHERE id_guru = ? AND id_kelas = ? AND tanggal = ? AND id_ta = ? LIMIT 1");
                $stmt_j->execute([$r['id_guru'], $id_k, $tanggal, $id_ta]);
                $jurnal = $stmt_j->fetch(PDO::FETCH_ASSOC);

                // Cek Absensi Siswa
                $stmt_a = $pdo->prepare("SELECT COUNT(*) FROM absensi_siswa_mapel WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ? AND id_ta = ?");
                $stmt_a->execute([$id_k, $tanggal, $r['id_guru_mapel'], $id_ta]);
                $has_absen = ($stmt_a->fetchColumn() > 0);

                $grouped[$key]['kelas_map'][$id_k] = [
                    'id_kelas' => $id_k,
                    'nama_kelas' => $r['nama_kelas'],
                    'id_guru_mapel' => $r['id_guru_mapel'],
                    'has_jurnal' => !empty($jurnal),
                    'jurnal_materi' => $jurnal['tujuan_pembelajaran'] ?? '',
                    'has_absen_siswa' => $has_absen
                ];
            }
            $grouped[$key]['jp_count']++;
        }

        // Finalize list
        $result = [];
        foreach ($grouped as $g) {
            $kelasList = array_values($g['kelas_map']);
            $namaKelasArray = array_map(function($c) { return $c['nama_kelas']; }, $kelasList);
            $totalJurnal = count(array_filter($kelasList, function($c) { return $c['has_jurnal']; }));
            $totalAbsen = count(array_filter($kelasList, function($c) { return $c['has_absen_siswa']; }));
            
            $g['kelas_list'] = $kelasList;
            $g['nama_kelas_gabung'] = implode(', ', $namaKelasArray);
            $g['total_kelas'] = count($kelasList);
            $g['total_jurnal_terisi'] = $totalJurnal;
            $g['total_absen_terisi'] = $totalAbsen;
            $g['all_jurnal_done'] = ($totalJurnal === count($kelasList) && count($kelasList) > 0);
            $g['all_absen_done'] = ($totalAbsen === count($kelasList) && count($kelasList) > 0);

            $result[] = $g;
        }

        return $result;
    }

    /**
     * Mengambil data absensi guru yang sudah ada pada tanggal tertentu.
     * (Fungsi ini tidak berubah)
     */
    public static function getAbsensiByTanggal($pdo, $tanggal, $id_ta) {
        $sql = "SELECT * FROM absensi_guru WHERE tanggal = ? AND id_ta = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal, $id_ta]);
        
        $absensi_data = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $absensi_data[$row['id_guru']] = $row;
        }
        return $absensi_data;
    }

    /**
     * Menyimpan data absensi guru.
     * Logika disempurnakan: Hapus-lalu-Sisipkan HANYA untuk guru yang ada di form.
     */
    public static function save($pdo, $data) {
        $pdo->beginTransaction();
        try {
            // Siapkan statement di luar loop
            $deleteStmt = $pdo->prepare("DELETE FROM absensi_guru WHERE tanggal = ? AND id_ta = ? AND id_guru = ?");
            $insertStmt = $pdo->prepare(
                "INSERT INTO absensi_guru (id_guru, id_ta, tanggal, status, keterangan, tugas, id_guru_piket) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            // Loop dan masukkan data baru HANYA untuk guru yang ada di form
            foreach ($data['absensi'] as $id_guru => $absensi_data) {
                // 1. Hapus data lama untuk guru ini di hari ini
                $deleteStmt->execute([$data['tanggal'], $data['id_ta'], $id_guru]);

                // 2. Masukkan data baru
                $status = $absensi_data['status'] ?? 'Hadir';
                $keterangan = $absensi_data['keterangan'] ?? '';
                $tugas = $absensi_data['tugas'] ?? '';

                $insertStmt->execute([
                    $id_guru, $data['id_ta'], $data['tanggal'], 
                    $status, $keterangan, $tugas, $data['id_guru_piket']
                ]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Gagal menyimpan absensi guru: " . $e->getMessage());
        }
    }
}