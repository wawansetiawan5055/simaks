<?php
class DashboardModel
{

    // --- 1. INFO CARD ---
    public static function summary($pdo, $user_id, $roles)
    {
        // Get fresh data from database every time (no caching)
        // SimpleCache is not persistent across requests, so we skip it for real-time data
        
        // KOREKSI: status guru menggunakan kolom 'status'
        $tot_guru = $pdo->query("SELECT COUNT(*) FROM guru WHERE status='Aktif'")->fetchColumn();
        // KOREKSI: status siswa menggunakan kolom 'status_aktif'
        $tot_siswa = $pdo->query("SELECT COUNT(*) FROM siswa WHERE status_aktif='Aktif'")->fetchColumn();
        $tot_kelas = $pdo->query("SELECT COUNT(*) FROM kelas WHERE id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1)")->fetchColumn();
        $tot_mapel = $pdo->query("SELECT COUNT(*) FROM mapel")->fetchColumn();

        return [
            'total_guru' => $tot_guru,
            'total_siswa' => $tot_siswa,
            'total_kelas' => $tot_kelas,
            'total_mapel' => $tot_mapel
        ];
    }

    // --- 2. REKAP SISWA PER KELAS ---
    public static function getRekapSiswaPerKelas($pdo, $id_ta)
    {
        if (!$id_ta)
            return [];

        // SIMPLIFIED QUERY - Get student counts from penempatan_siswa only
        // This is more efficient than the complex UNION query
        $sql = "
            SELECT 
                k.id_kelas,
                k.nama_kelas,
                k.tingkat,
                COUNT(ps.id_siswa) AS total, 
                SUM(CASE WHEN s.jk IN ('Laki-laki', 'L') THEN 1 ELSE 0 END) AS laki,
                SUM(CASE WHEN s.jk IN ('Perempuan', 'P') THEN 1 ELSE 0 END) AS perempuan
            FROM kelas k
            LEFT JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas AND ps.id_ta = ?
            LEFT JOIN siswa s ON ps.id_siswa = s.id_siswa
            WHERE k.id_ta = ?
            GROUP BY k.id_kelas, k.nama_kelas, k.tingkat
            ORDER BY k.tingkat, k.nama_kelas
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_ta, $id_ta]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        try {
            // [REFINED MERGE] 
            // We want to count alumni not just for their graduation TA, 
            // but for both semesters of their final school year.

            // 1. Get current TA name to identify the school year
            $stmt_ta = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
            $stmt_ta->execute([$id_ta]);
            $curr_ta_name = $stmt_ta->fetchColumn();
            $year_prefix = substr($curr_ta_name, 0, 9); // e.g., "2024/2025"

            // 2. Find alumni who graduated in this same year
            // Match based on Class Name because id_kelas might differ between Ganjil/Genap records
            $sql_alumni = "
                SELECT 
                    k_akhir.nama_kelas, 
                    COUNT(*) as total, 
                    SUM(CASE WHEN sa.jk = 'Laki-laki' THEN 1 ELSE 0 END) AS laki,
                    SUM(CASE WHEN sa.jk = 'Perempuan' THEN 1 ELSE 0 END) AS perempuan
                FROM siswa_alumni sa
                JOIN tahun_ajaran ta_lulus ON sa.id_ta_lulus = ta_lulus.id_ta
                JOIN kelas k_akhir ON sa.id_kelas_akhir = k_akhir.id_kelas
                WHERE ta_lulus.nama_ta LIKE ?
                AND sa.id_siswa NOT IN (SELECT id_siswa FROM penempatan_siswa WHERE id_ta = ?)
                GROUP BY k_akhir.nama_kelas
            ";
            $stmtAlumni = $pdo->prepare($sql_alumni);
            $stmtAlumni->execute([$year_prefix . '%', $id_ta]);
            $alumniData = $stmtAlumni->fetchAll(PDO::FETCH_ASSOC);

            // Merge by Class Name
            if (!empty($alumniData)) {
                $alumniMapByName = [];
                foreach ($alumniData as $al) {
                    $alumniMapByName[$al['nama_kelas']] = $al;
                }

                foreach ($result as &$row) {
                    if (isset($alumniMapByName[$row['nama_kelas']])) {
                        $al = $alumniMapByName[$row['nama_kelas']];
                        $row['total'] += $al['total'];
                        $row['laki'] += $al['laki'];
                        $row['perempuan'] += $al['perempuan'];
                    }
                }
                unset($row); // [FIX] Putuskan referensi terakhir
            }
        } catch (Exception $e) {
            // Ignore if column doesn't exist yet
        }

        // Add mutation data separately using NEW id_kelas columns
        $kelas_ids = array_column($result, 'id_kelas');

        if (!empty($kelas_ids)) {
            $placeholders = implode(',', array_fill(0, count($kelas_ids), '?'));

            // Count mutasi masuk per kelas (DIRECT from id_kelas_tujuan)
            $sql_masuk = "SELECT id_kelas_tujuan, COUNT(*) as count_masuk
                          FROM mutasi_masuk
                          WHERE id_kelas_tujuan IN ($placeholders) AND id_ta = ?
                          GROUP BY id_kelas_tujuan";
            $stmt = $pdo->prepare($sql_masuk);
            $stmt->execute(array_merge($kelas_ids, [$id_ta]));
            $masuk_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // Count mutasi keluar per kelas (DIRECT from id_kelas_asal)
            $sql_keluar = "SELECT id_kelas_asal, COUNT(*) as count_keluar
                           FROM siswa_mutasi
                           WHERE id_kelas_asal IN ($placeholders) AND id_ta_mutasi = ?
                           GROUP BY id_kelas_asal";
            $stmt = $pdo->prepare($sql_keluar);
            $stmt->execute(array_merge($kelas_ids, [$id_ta]));
            $keluar_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // Merge mutation data into result
            foreach ($result as &$row) {
                $row['mutasi_masuk'] = $masuk_data[$row['id_kelas']] ?? 0;
                $row['mutasi_keluar'] = $keluar_data[$row['id_kelas']] ?? 0;
            }
            unset($row); // [FIX] Putuskan referensi terakhir
        } else {
            // No classes, add default mutation columns
            foreach ($result as &$row) {
                $row['mutasi_masuk'] = 0;
                $row['mutasi_keluar'] = 0;
            }
            unset($row);
        }

        return $result;
    }

    // --- 3. ABSENSI GURU ---
    public static function getAbsensiGuruDetail($pdo, $params)
    {
        $periode = $params['periode'] ?? 'daily';
        $id_ta = $params['id_ta'] ?? 0;
        list($where_date, $bind_params) = self::buildDateFilter('ag.tanggal', $periode, $params);

        $sql = "
            SELECT 
                g.nama,
                SUM(CASE WHEN ag.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                SUM(CASE WHEN ag.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                SUM(CASE WHEN ag.status = 'Izin' THEN 1 ELSE 0 END) AS I,
                SUM(CASE WHEN ag.status = 'Alpa' THEN 1 ELSE 0 END) AS A
            FROM guru g
            LEFT JOIN absensi_guru ag ON g.id_guru = ag.id_guru 
                AND ag.id_ta = ? 
                {$where_date}
            WHERE g.status = 'Aktif'
            GROUP BY g.id_guru, g.nama
            ORDER BY g.nama ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$id_ta], $bind_params));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $chart = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($data as $d) {
            $chart['H'] += $d['H'];
            $chart['S'] += $d['S'];
            $chart['I'] += $d['I'];
            $chart['A'] += $d['A'];
        }

        return ['table' => $data, 'chart' => $chart];
    }

    // --- 4. ABSENSI SISWA ---
    public static function getAbsensiSiswaDetail($pdo, $params)
    {
        $periode = $params['periode'] ?? 'daily';
        $id_ta = $params['id_ta'] ?? 0;
        list($where_date, $bind_params) = self::buildDateFilter('asp.tanggal', $periode, $params);

        $sql = "
            SELECT 
                k.id_kelas,
                k.nama_kelas,
                SUM(CASE WHEN asp.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                SUM(CASE WHEN asp.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                SUM(CASE WHEN asp.status = 'Izin' THEN 1 ELSE 0 END) AS I,
                SUM(CASE WHEN asp.status = 'Alpa' THEN 1 ELSE 0 END) AS A
            FROM kelas k
            LEFT JOIN absensi_siswa_piket asp ON k.id_kelas = asp.id_kelas 
                AND asp.id_ta = ?
                {$where_date}
            WHERE k.id_ta = ?
            GROUP BY k.id_kelas, k.nama_kelas
            ORDER BY k.tingkat, k.nama_kelas
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$id_ta], $bind_params, [$id_ta]));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $chart = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($data as $d) {
            $chart['H'] += $d['H'];
            $chart['S'] += $d['S'];
            $chart['I'] += $d['I'];
            $chart['A'] += $d['A'];
        }

        return ['table' => $data, 'chart' => $chart];
    }

    // --- 5. HELPER FILTER TANGGAL (KOREKSI KRUSIAL) ---
    private static function buildDateFilter($col_name, $periode, $params)
    {
        $where_clause = "";
        $bind_params = [];
        $tanggal_input = $params['tanggal'] ?? date('Y-m-d');
        $semester_input = $params['semester'] ?? '1';

        if ($periode === 'daily') {
            $where_clause = " AND {$col_name} = ?";
            $bind_params[] = $tanggal_input;
        } elseif ($periode === 'monthly') {
            $where_clause = " AND DATE_FORMAT({$col_name}, '%Y-%m') = ?";
            $bind_params[] = substr($tanggal_input, 0, 7);
        } elseif ($periode === 'semester') {
            if ($semester_input == '1') {
                $where_clause = " AND MONTH({$col_name}) BETWEEN 7 AND 12";
            } else {
                $where_clause = " AND MONTH({$col_name}) BETWEEN 1 AND 6";
            }
        }

        return [$where_clause, $bind_params];
    }

    // --- 6. DETAIL ABSENSI SISWA PER SISWA ---
    public static function getAbsensiSiswaPerSiswa($pdo, $id_kelas, $params)
    {
        $periode = $params['periode'] ?? 'daily';
        $id_ta = $params['id_ta'] ?? 0;

        list($where_date, $bind_params) = self::buildDateFilter('asp.tanggal', $periode, $params);

        $sql = "
            SELECT 
                s.nipd,
                s.nama,
                SUM(CASE WHEN asp.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                SUM(CASE WHEN asp.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                SUM(CASE WHEN asp.status = 'Izin' THEN 1 ELSE 0 END) AS I,
                SUM(CASE WHEN asp.status = 'Alpa' THEN 1 ELSE 0 END) AS A
            FROM siswa s
            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
            LEFT JOIN absensi_siswa_piket asp ON s.id_siswa = asp.id_siswa 
                AND asp.id_ta = ?
                {$where_date}
            WHERE ps.id_kelas = ?
            AND ps.id_ta = ?
            AND s.status_aktif = 'Aktif'
            GROUP BY s.id_siswa, s.nipd, s.nama
            ORDER BY s.nama ASC
        ";

        $all_params = array_merge([$id_ta], $bind_params, [$id_kelas, $id_ta]);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($all_params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // --- 6. LIST DROPDOWN API ---
    public static function getKelasList($pdo, $id_ta = null)
    {
        if ($id_ta) {
            $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
            $stmt->execute([$id_ta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pdo->query("SELECT id_kelas, nama_kelas, tingkat FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTahunAjaranList($pdo)
    {
        return $pdo->query("SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGuruAktifList($pdo)
    {
        return $pdo->query("SELECT id_guru, nama FROM guru WHERE status='Aktif' ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 7. JADWAL MENGAJAR (TAMBAHAN BARU) ---
    public static function getJadwalGuruHariIni($pdo, $id_guru, $hari)
    {
        // Ambil ID TA View atau Aktif dari Sesi (Ini biasanya ada di Controller, tapi kita ambil di sini)
        $id_ta_viewing = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        if ($id_ta_viewing === 0)
            return [];

        $sql = "
            SELECT 
                m.nama_mapel,
                k.nama_kelas,
                jp.jam_mulai,
                jp.jam_selesai
            FROM jadwal_mengajar jm
            JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            JOIN kelas k ON jm.id_kelas = k.id_kelas
            JOIN jam_pelajaran jp ON jm.id_jam = jp.id_jam
            WHERE gm.id_guru = ? 
            AND jm.hari_kbm = ?
            AND gm.id_ta = ?  /* <--- FILTER TA DITAMBAHKAN */
            ORDER BY jp.urutan ASC
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_guru, $hari, $id_ta_viewing]); // Tambahkan id_ta_viewing di sini
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}