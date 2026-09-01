<?php
class DashboardModel
{

    // --- 1. INFO CARD ---
    public static function summary($pdo, $user_id, $roles)
    {
        $tot_guru = $pdo->query("SELECT COUNT(*) FROM guru WHERE status='Aktif'")->fetchColumn();
        $tot_siswa = $pdo->query("SELECT COUNT(*) FROM siswa WHERE status_aktif='Aktif'")->fetchColumn();
        $tot_kelas = $pdo->query("SELECT COUNT(*) FROM kelas WHERE id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1)")->fetchColumn();
        $tot_mapel = $pdo->query("SELECT COUNT(*) FROM mapel")->fetchColumn();

        return [
            'total_guru'  => $tot_guru,
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

        // [LOGIKA HISTORIS TEPAT]
        // Siswa aktif di kelas pada TA tertentu dihitung dari penempatan_siswa
        // DIKURANGI siswa yang mutasi keluar PADA TA TERSEBUT (id_ta_mutasi = id_ta).
        // Siswa yang mutasi keluar di TA berikutnya tetap dihitung aktif di TA ini.
        $sql = "
            SELECT
                k.id_kelas,
                k.nama_kelas,
                k.tingkat,
                COALESCE(g_wali.nama, '-') AS nama_wali,
                COUNT(CASE WHEN s.id_siswa IS NOT NULL AND sm.id_siswa IS NULL THEN ps.id_siswa ELSE NULL END) AS total,
                SUM(CASE WHEN s.id_siswa IS NOT NULL AND s.jk IN ('Laki-laki', 'L') AND sm.id_siswa IS NULL THEN 1 ELSE 0 END) AS laki,
                SUM(CASE WHEN s.id_siswa IS NOT NULL AND s.jk IN ('Perempuan', 'P') AND sm.id_siswa IS NULL THEN 1 ELSE 0 END) AS perempuan
            FROM kelas k
            LEFT JOIN penugasan_wali_kelas pwk ON pwk.id_kelas = k.id_kelas AND pwk.id_ta = :ta_wali AND pwk.jenis_tugas = 'Wali Kelas'
            LEFT JOIN guru g_wali ON pwk.id_guru = g_wali.id_guru
            LEFT JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas AND ps.id_ta = :ta1
            LEFT JOIN siswa s ON ps.id_siswa = s.id_siswa
            LEFT JOIN siswa_mutasi sm ON sm.id_siswa = ps.id_siswa 
                AND sm.id_kelas_asal = ps.id_kelas 
                AND sm.id_ta_mutasi = :ta2
            WHERE k.id_ta = :ta3
            GROUP BY k.id_kelas, k.nama_kelas, k.tingkat, g_wali.nama
            ORDER BY k.tingkat, k.nama_kelas
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ta1' => $id_ta, ':ta2' => $id_ta, ':ta3' => $id_ta, ':ta_wali' => $id_ta]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        try {
            $stmt_ta = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
            $stmt_ta->execute([$id_ta]);
            $curr_ta_name = $stmt_ta->fetchColumn();
            $year_prefix  = substr($curr_ta_name, 0, 9);

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

            if (!empty($alumniData)) {
                $alumniMapByName = [];
                foreach ($alumniData as $al) {
                    $alumniMapByName[$al['nama_kelas']] = $al;
                }
                foreach ($result as &$row) {
                    if (isset($alumniMapByName[$row['nama_kelas']])) {
                        $al = $alumniMapByName[$row['nama_kelas']];
                        $row['total']    += $al['total'];
                        $row['laki']     += $al['laki'];
                        $row['perempuan']+= $al['perempuan'];
                    }
                }
                unset($row);
            }
        } catch (Exception $e) {
            // Ignore
        }

        $kelas_ids = array_column($result, 'id_kelas');

        if (!empty($kelas_ids)) {
            $placeholders = implode(',', array_fill(0, count($kelas_ids), '?'));

            $sql_masuk = "SELECT id_kelas_tujuan, COUNT(*) as count_masuk
                          FROM mutasi_masuk
                          WHERE id_kelas_tujuan IN ($placeholders) AND id_ta = ?
                          GROUP BY id_kelas_tujuan";
            $stmt = $pdo->prepare($sql_masuk);
            $stmt->execute(array_merge($kelas_ids, [$id_ta]));
            $masuk_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            $sql_keluar = "SELECT id_kelas_asal, COUNT(*) as count_keluar
                           FROM siswa_mutasi
                           WHERE id_kelas_asal IN ($placeholders) AND id_ta_mutasi = ?
                           GROUP BY id_kelas_asal";
            $stmt = $pdo->prepare($sql_keluar);
            $stmt->execute(array_merge($kelas_ids, [$id_ta]));
            $keluar_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            foreach ($result as &$row) {
                $row['mutasi_masuk']  = $masuk_data[$row['id_kelas']]  ?? 0;
                $row['mutasi_keluar'] = $keluar_data[$row['id_kelas']] ?? 0;
            }
            unset($row);
        } else {
            foreach ($result as &$row) {
                $row['mutasi_masuk']  = 0;
                $row['mutasi_keluar'] = 0;
            }
            unset($row);
        }

        return $result;
    }

    // --- 3. ABSENSI GURU ---
    public static function getAbsensiGuruDetail($pdo, $params)
    {
        require_once __DIR__ . '/AbsensiGuruModel.php';

        $periode  = $params['periode'] ?? 'daily';
        $id_ta    = (int)($params['id_ta'] ?? 0);
        $mode_kbm = $params['mode_kbm'] ?? 'tatap_muka';
        $tanggal  = $params['tanggal'] ?? date('Y-m-d');

        if ($mode_kbm === 'online') {
            if ($periode === 'daily') {
                $online_rows = AbsensiGuruModel::getGuruOnlineScheduleOnDate($pdo, $tanggal, $id_ta);
                $data = [];
                foreach ($online_rows as $or) {
                    $isHadir = $or['total_jurnal_terisi'] > 0 ? 1 : 0;
                    $data[] = [
                        'nama' => $or['nama_guru'],
                        'H' => $isHadir,
                        'S' => 0,
                        'I' => 0,
                        'A' => 0 // Belum diluncurkan / tidak dicatat Alpa
                    ];
                }
            } else {
                list($where_date, $bind_params) = self::buildDateFilter('j.tanggal', $periode, $params);
                $sql = "
                    SELECT g.nama,
                           COUNT(DISTINCT CONCAT(j.tanggal, '_', j.id_kelas)) AS H,
                           0 AS S,
                           0 AS I,
                           0 AS A
                    FROM guru g
                    JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = ?
                    JOIN jadwal_mengajar jm ON gm.id_guru_mapel = jm.id_guru_mapel
                    JOIN kelas k ON jm.id_kelas = k.id_kelas AND (jm.mode_kbm = 'online' OR k.jenis_kelas = 'pjj')
                    LEFT JOIN jurnal_kbm j ON j.id_guru = g.id_guru AND j.id_kelas = jm.id_kelas AND j.id_ta = gm.id_ta {$where_date}
                    WHERE g.status = 'Aktif'
                    GROUP BY g.id_guru, g.nama
                    ORDER BY g.nama ASC
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge([$id_ta], $bind_params));
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // TATAP MUKA (FISIK)
            if ($periode === 'daily') {
                $guru_fisik = AbsensiGuruModel::getGuruWithScheduleOnDate($pdo, $tanggal, $id_ta);
                $absensi_map = AbsensiGuruModel::getAbsensiByTanggal($pdo, $tanggal, $id_ta);

                $data = [];
                foreach ($guru_fisik as $gf) {
                    $id_g = $gf['id_guru'];
                    $abs = $absensi_map[$id_g] ?? null;
                    if ($abs) {
                        $st = $abs['status'];
                        $data[] = [
                            'nama' => $gf['nama'],
                            'H' => ($st === 'Hadir' ? 1 : 0),
                            'S' => ($st === 'Sakit' ? 1 : 0),
                            'I' => ($st === 'Izin' ? 1 : 0),
                            'A' => ($st === 'Alpa' ? 1 : 0),
                        ];
                    } else {
                        // Belum diabsen
                        $data[] = [
                            'nama' => $gf['nama'],
                            'H' => 0,
                            'S' => 0,
                            'I' => 0,
                            'A' => 0
                        ];
                    }
                }
            } else {
                list($where_date, $bind_params) = self::buildDateFilter('ag.tanggal', $periode, $params);
                $sql = "
                    SELECT
                        g.nama,
                        SUM(CASE WHEN ag.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                        SUM(CASE WHEN ag.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                        SUM(CASE WHEN ag.status = 'Izin'  THEN 1 ELSE 0 END) AS I,
                        SUM(CASE WHEN ag.status = 'Alpa'  THEN 1 ELSE 0 END) AS A
                    FROM guru g
                    JOIN absensi_guru ag ON g.id_guru = ag.id_guru AND ag.id_ta = ? {$where_date}
                    WHERE g.status = 'Aktif'
                    GROUP BY g.id_guru, g.nama
                    ORDER BY g.nama ASC
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge([$id_ta], $bind_params));
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        $chart = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        foreach ($data as $d) {
            $chart['H'] += (int)$d['H'];
            $chart['S'] += (int)$d['S'];
            $chart['I'] += (int)$d['I'];
            $chart['A'] += (int)$d['A'];
        }

        return ['table' => $data, 'chart' => $chart];
    }

    // --- 4. ABSENSI SISWA ---
    public static function getAbsensiSiswaDetail($pdo, $params)
    {
        $periode = $params['periode'] ?? 'daily';
        $id_ta   = $params['id_ta']   ?? 0;
        list($where_date, $bind_params) = self::buildDateFilter('asp.tanggal', $periode, $params);

        $sql = "
            SELECT
                k.id_kelas,
                k.nama_kelas,
                SUM(CASE WHEN asp.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                SUM(CASE WHEN asp.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                SUM(CASE WHEN asp.status = 'Izin'  THEN 1 ELSE 0 END) AS I,
                SUM(CASE WHEN asp.status = 'Alpa'  THEN 1 ELSE 0 END) AS A
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

    // --- 5. HELPER FILTER TANGGAL ---
    private static function buildDateFilter($col_name, $periode, $params)
    {
        $where_clause   = "";
        $bind_params    = [];
        $tanggal_input  = $params['tanggal']  ?? date('Y-m-d');
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
        $id_ta   = $params['id_ta']   ?? 0;
        list($where_date, $bind_params) = self::buildDateFilter('asp.tanggal', $periode, $params);

        $sql = "
            SELECT
                s.nipd,
                s.nama,
                SUM(CASE WHEN asp.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                SUM(CASE WHEN asp.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                SUM(CASE WHEN asp.status = 'Izin'  THEN 1 ELSE 0 END) AS I,
                SUM(CASE WHEN asp.status = 'Alpa'  THEN 1 ELSE 0 END) AS A
            FROM siswa s
            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
            LEFT JOIN absensi_siswa_piket asp ON s.id_siswa = asp.id_siswa
                AND asp.id_ta = ?
                {$where_date}
            WHERE ps.id_kelas = ?
            AND ps.id_ta = ?
            AND s.status_aktif != 'Keluar'
            GROUP BY s.id_siswa, s.nipd, s.nama
            ORDER BY s.nama ASC
        ";

        $all_params = array_merge([$id_ta], $bind_params, [$id_kelas, $id_ta]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($all_params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 7. LIST DROPDOWN API ---
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

    // --- 8. JADWAL MENGAJAR GURU HARI INI (MERGED INTERVAL) ---
    public static function getJadwalGuruHariIni($pdo, $id_guru, $hari)
    {
        $id_ta_viewing = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
        if ($id_ta_viewing === 0)
            return [];

        $sql = "
            SELECT
                m.nama_mapel,
                k.nama_kelas,
                MIN(jp.jam_mulai) AS jam_mulai,
                MAX(jp.jam_selesai) AS jam_selesai,
                COUNT(jm.id_jam) AS jp_count
            FROM jadwal_mengajar jm
            JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
            JOIN mapel m ON gm.id_mapel = m.id_mapel
            JOIN kelas k ON jm.id_kelas = k.id_kelas
            JOIN jam_pelajaran jp ON jm.id_jam = jp.id_jam
            WHERE gm.id_guru = ?
            AND jm.hari_kbm = ?
            AND gm.id_ta = ?
            GROUP BY jm.id_kelas, gm.id_mapel, m.nama_mapel, k.nama_kelas
            ORDER BY MIN(jp.jam_mulai) ASC
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_guru, $hari, $id_ta_viewing]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getJadwalHariIni($pdo, $id_guru, $id_ta = null)
    {
        $id_ta_aktif = $id_ta ?? $_SESSION['id_ta_aktif'] ?? 0;
        $hari_map = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hari_ini = $hari_map[date('l')] ?? 'Senin';

        return self::getJadwalGuruHariIni($pdo, $id_guru, $hari_ini);
    }

    // --- 9. STATUS TUGAS HARIAN GURU & GURU PIKET (UNTUK BANNER & POPUP SMART REMINDER) ---
    public static function getTugasHarianStatus($pdo, $id_guru, $user_id, $user_roles, $id_ta_aktif, $hari_ini, $tanggal)
    {
        $res = [
            'is_guru' => false,
            'is_piket_today' => false,
            'total_jadwal_hari_ini' => 0,
            'absen_mapel_selesai' => 0,
            'absen_mapel_total' => 0,
            'jurnal_kbm_selesai' => 0,
            'jurnal_kbm_total' => 0,
            'detail_jadwal' => [],
            'piket' => [
                'is_active' => false,
                'absen_guru_selesai' => false,
                'absen_guru_count' => 0,
                'absen_siswa_selesai' => false,
                'absen_siswa_kelas_count' => 0,
                'total_kelas' => 0
            ],
            'pending_count' => 0,
            'pending_tasks' => []
        ];

        // 1. Cek Tugas Guru Mapel
        if ($id_guru > 0 && (in_array('Guru', $user_roles) || in_array('Admin', $user_roles))) {
            $res['is_guru'] = true;

            $sql_jadwal = "
                SELECT jm.id_kelas, k.nama_kelas, m.nama_mapel, gm.id_guru_mapel,
                       MIN(jp.jam_mulai) AS jam_mulai, MAX(jp.jam_selesai) AS jam_selesai
                FROM jadwal_mengajar jm
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON jm.id_kelas = k.id_kelas
                JOIN jam_pelajaran jp ON jm.id_jam = jp.id_jam
                WHERE gm.id_guru = ? AND jm.hari_kbm = ? AND gm.id_ta = ?
                GROUP BY jm.id_kelas, gm.id_guru_mapel, k.nama_kelas, m.nama_mapel
                ORDER BY MIN(jp.jam_mulai) ASC
            ";
            $stmt_j = $pdo->prepare($sql_jadwal);
            $stmt_j->execute([$id_guru, $hari_ini, $id_ta_aktif]);
            $jadwal_list = $stmt_j->fetchAll(PDO::FETCH_ASSOC);

            $res['total_jadwal_hari_ini'] = count($jadwal_list);
            $res['absen_mapel_total'] = count($jadwal_list);
            $res['jurnal_kbm_total'] = count($jadwal_list);

            foreach ($jadwal_list as $j) {
                // Cek status absensi mapel
                $stmt_a = $pdo->prepare("
                    SELECT COUNT(*) FROM absensi_siswa_mapel 
                    WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ? AND id_ta = ?
                ");
                $stmt_a->execute([$j['id_kelas'], $tanggal, $j['id_guru_mapel'], $id_ta_aktif]);
                $sudah_absen = ($stmt_a->fetchColumn() > 0);
                if ($sudah_absen) $res['absen_mapel_selesai']++;
                else {
                    $res['pending_count']++;
                    $res['pending_tasks'][] = [
                        'type' => 'absensi_mapel',
                        'title' => "Presensi Mapel: {$j['nama_mapel']} ({$j['nama_kelas']})",
                        'url' => "index.php?mod=absensi_mapel&id_kelas={$j['id_kelas']}&tanggal={$tanggal}"
                    ];
                }

                // Cek status jurnal KBM
                $stmt_jk = $pdo->prepare("
                    SELECT COUNT(*) FROM jurnal_kbm 
                    WHERE id_guru = ? AND id_kelas = ? AND tanggal = ? AND id_ta = ?
                ");
                $stmt_jk->execute([$id_guru, $j['id_kelas'], $tanggal, $id_ta_aktif]);
                $sudah_jurnal = ($stmt_jk->fetchColumn() > 0);
                if ($sudah_jurnal) $res['jurnal_kbm_selesai']++;
                else {
                    $res['pending_count']++;
                    $res['pending_tasks'][] = [
                        'type' => 'jurnal_kbm',
                        'title' => "Jurnal KBM: {$j['nama_mapel']} ({$j['nama_kelas']})",
                        'url' => "index.php?mod=jurnal_kbm&id_kelas={$j['id_kelas']}&tanggal={$tanggal}"
                    ];
                }

                $res['detail_jadwal'][] = [
                    'id_kelas' => $j['id_kelas'],
                    'nama_kelas' => $j['nama_kelas'],
                    'nama_mapel' => $j['nama_mapel'],
                    'id_guru_mapel' => $j['id_guru_mapel'],
                    'jam_mulai' => substr($j['jam_mulai'], 0, 5),
                    'jam_selesai' => substr($j['jam_selesai'], 0, 5),
                    'sudah_absen' => $sudah_absen,
                    'sudah_jurnal' => $sudah_jurnal
                ];
            }
        }

        // 2. Cek Tugas Guru Piket
        if ($id_guru > 0) {
            $stmt_p = $pdo->prepare("
                SELECT COUNT(*) FROM jadwal_guru_piket 
                WHERE id_guru = ? AND id_ta = ? AND hari = ?
            ");
            $stmt_p->execute([$id_guru, $id_ta_aktif, $hari_ini]);
            $is_piket = ($stmt_p->fetchColumn() > 0) || in_array('GuruPiket', $user_roles);

            if ($is_piket) {
                $res['is_piket_today'] = true;
                $res['piket']['is_active'] = true;

                // Cek Absen Guru
                $stmt_ag = $pdo->prepare("SELECT COUNT(*) FROM absensi_guru WHERE tanggal = ? AND id_ta = ?");
                $stmt_ag->execute([$tanggal, $id_ta_aktif]);
                $count_ag = (int)$stmt_ag->fetchColumn();
                $res['piket']['absen_guru_count'] = $count_ag;
                $res['piket']['absen_guru_selesai'] = ($count_ag > 0);
                if (!$res['piket']['absen_guru_selesai']) {
                    $res['pending_count']++;
                    $res['pending_tasks'][] = [
                        'type' => 'absensi_guru',
                        'title' => "Presensi Kehadiran Guru (Piket)",
                        'url' => "index.php?mod=absensi_guru&tanggal={$tanggal}"
                    ];
                }

                // Cek Absen Siswa Piket
                $stmt_as = $pdo->prepare("SELECT COUNT(DISTINCT id_kelas) FROM absensi_siswa_piket WHERE tanggal = ? AND id_ta = ?");
                $stmt_as->execute([$tanggal, $id_ta_aktif]);
                $count_as = (int)$stmt_as->fetchColumn();

                $stmt_tk = $pdo->prepare("SELECT COUNT(*) FROM kelas WHERE id_ta = ?");
                $stmt_tk->execute([$id_ta_aktif]);
                $tot_kelas = (int)$stmt_tk->fetchColumn() ?: 1;

                $res['piket']['absen_siswa_kelas_count'] = $count_as;
                $res['piket']['total_kelas'] = $tot_kelas;
                $res['piket']['absen_siswa_selesai'] = ($count_as > 0);
                if (!$res['piket']['absen_siswa_selesai']) {
                    $res['pending_count']++;
                    $res['pending_tasks'][] = [
                        'type' => 'absensi_piket',
                        'title' => "Presensi Siswa Harian (Piket)",
                        'url' => "index.php?mod=absensi_piket&tanggal={$tanggal}"
                    ];
                }
            }
        }

        // 3. Data Monitoring Keterlaksanaan KBM & Piket Seluruh Sekolah (Untuk Admin / Kepala Sekolah / TU)
        $res['is_admin'] = in_array('Admin', $user_roles) || in_array('KepalaSekolah', $user_roles) || in_array('TU', $user_roles);
        
        try {
            // A. METRIK KBM TATAP MUKA (OFFLINE)
            $sql_offline = "
                SELECT jm.id_kelas, gm.id_guru_mapel, gm.id_guru
                FROM jadwal_mengajar jm
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                WHERE jm.hari_kbm = ? AND gm.id_ta = ? AND (jm.mode_kbm = 'offline' OR jm.mode_kbm IS NULL)
                GROUP BY jm.id_kelas, gm.id_guru_mapel, gm.id_guru
            ";
            $stmt_off = $pdo->prepare($sql_offline);
            $stmt_off->execute([$hari_ini, $id_ta_aktif]);
            $list_offline = $stmt_off->fetchAll(PDO::FETCH_ASSOC);

            $tot_pertemuan_offline = count($list_offline);
            $jurnal_terisi_offline = 0;
            $absen_terisi_offline = 0;

            foreach ($list_offline as $p) {
                $stmt_j = $pdo->prepare("SELECT COUNT(*) FROM jurnal_kbm WHERE id_guru = ? AND id_kelas = ? AND tanggal = ? AND id_ta = ?");
                $stmt_j->execute([$p['id_guru'], $p['id_kelas'], $tanggal, $id_ta_aktif]);
                if ($stmt_j->fetchColumn() > 0) $jurnal_terisi_offline++;

                $stmt_a = $pdo->prepare("SELECT COUNT(*) FROM absensi_siswa_mapel WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ? AND id_ta = ?");
                $stmt_a->execute([$p['id_kelas'], $tanggal, $p['id_guru_mapel'], $id_ta_aktif]);
                if ($stmt_a->fetchColumn() > 0) $absen_terisi_offline++;
            }

            // Presensi GTK Hadir Fisik
            $stmt_gh = $pdo->prepare("SELECT COUNT(DISTINCT id_guru) FROM absensi_guru WHERE tanggal = ? AND status = 'Hadir'");
            $stmt_gh->execute([$tanggal]);
            $guru_hadir_fisik = (int)$stmt_gh->fetchColumn();

            // Total GTK Wajib Hadir Fisik Hari Ini (KBM Offline + Piket + Non-KBM + Tendik)
            $sql_wajib_fisik = "
                SELECT COUNT(DISTINCT g.id_guru)
                FROM guru g
                LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
                LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
                LEFT JOIN peran pr ON pp.id_peran = pr.id_peran
                LEFT JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = ?
                LEFT JOIN jadwal_mengajar dm ON gm.id_guru_mapel = dm.id_guru_mapel AND dm.hari_kbm = ? AND (dm.mode_kbm = 'offline' OR dm.mode_kbm IS NULL)
                LEFT JOIN jadwal_guru_piket jgp ON g.id_guru = jgp.id_guru AND jgp.id_ta = ? AND jgp.hari = ?
                LEFT JOIN jadwal_guru_non_kbm jnk ON g.id_guru = jnk.id_guru AND jnk.id_ta = ? AND jnk.hari = ?
                WHERE g.status = 'Aktif' AND (dm.id_jadwal_mengajar IS NOT NULL OR jgp.id_jadwal_piket IS NOT NULL OR jnk.id_jadwal_non_kbm IS NOT NULL OR pr.nama_peran IN ('TU', 'Tenaga Kependidikan', 'Admin'))
            ";
            $stmt_wf = $pdo->prepare($sql_wajib_fisik);
            $stmt_wf->execute([$id_ta_aktif, $hari_ini, $id_ta_aktif, $hari_ini, $id_ta_aktif, $hari_ini]);
            $total_gtk_wajib_fisik = (int)$stmt_wf->fetchColumn() ?: 1;

            // Piket Siswa Terdata (Kelas Reguler/Offline)
            $stmt_pk = $pdo->prepare("SELECT COUNT(DISTINCT id_kelas) FROM absensi_siswa_piket WHERE tanggal = ? AND id_ta = ?");
            $stmt_pk->execute([$tanggal, $id_ta_aktif]);
            $piket_kelas_count = (int)$stmt_pk->fetchColumn();

            $stmt_tk = $pdo->prepare("SELECT COUNT(*) FROM kelas WHERE id_ta = ? AND (jenis_kelas = 'reguler' OR jenis_kelas = 'menginduk' OR jenis_kelas IS NULL)");
            $stmt_tk->execute([$id_ta_aktif]);
            $total_kelas_offline = (int)$stmt_tk->fetchColumn();

            // B. METRIK KBM DARING (ONLINE LMS)
            $sql_online = "
                SELECT jm.id_kelas, gm.id_guru_mapel, gm.id_guru
                FROM jadwal_mengajar jm
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                WHERE jm.hari_kbm = ? AND gm.id_ta = ? AND jm.mode_kbm = 'online'
                GROUP BY jm.id_kelas, gm.id_guru_mapel, gm.id_guru
            ";
            $stmt_on = $pdo->prepare($sql_online);
            $stmt_on->execute([$hari_ini, $id_ta_aktif]);
            $list_online = $stmt_on->fetchAll(PDO::FETCH_ASSOC);

            $tot_pertemuan_online = count($list_online);
            $jurnal_terisi_online = 0;
            $absen_terisi_online = 0;
            $guru_online_map = [];
            $guru_online_aktif = [];

            foreach ($list_online as $p) {
                $guru_online_map[$p['id_guru']] = true;
                $stmt_j = $pdo->prepare("SELECT COUNT(*) FROM jurnal_kbm WHERE id_guru = ? AND id_kelas = ? AND tanggal = ? AND id_ta = ?");
                $stmt_j->execute([$p['id_guru'], $p['id_kelas'], $tanggal, $id_ta_aktif]);
                if ($stmt_j->fetchColumn() > 0) {
                    $jurnal_terisi_online++;
                    $guru_online_aktif[$p['id_guru']] = true;
                }

                $stmt_a = $pdo->prepare("SELECT COUNT(*) FROM absensi_siswa_mapel WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel = ? AND id_ta = ?");
                $stmt_a->execute([$p['id_kelas'], $tanggal, $p['id_guru_mapel'], $id_ta_aktif]);
                if ($stmt_a->fetchColumn() > 0) $absen_terisi_online++;
            }

            $total_guru_online = count($guru_online_map);
            $guru_hadir_online = count($guru_online_aktif);

            $stmt_tko = $pdo->prepare("SELECT COUNT(*) FROM kelas WHERE id_ta = ? AND jenis_kelas = 'pjj'");
            $stmt_tko->execute([$id_ta_aktif]);
            $total_kelas_online = (int)$stmt_tko->fetchColumn();

            $res['admin_monitoring'] = [
                // Default / Offline
                'total_pertemuan' => $tot_pertemuan_offline,
                'jurnal_terisi' => $jurnal_terisi_offline,
                'absen_mapel_terisi' => $absen_terisi_offline,
                'guru_hadir' => $guru_hadir_fisik,
                'total_guru' => $total_gtk_wajib_fisik,
                'piket_kelas_terdata' => $piket_kelas_count,
                'total_kelas' => $total_kelas_offline,

                // Data Spesifik Offline
                'offline' => [
                    'total_pertemuan' => $tot_pertemuan_offline,
                    'jurnal_terisi' => $jurnal_terisi_offline,
                    'absen_mapel_terisi' => $absen_terisi_offline,
                    'guru_hadir' => $guru_hadir_fisik,
                    'total_guru' => $total_gtk_wajib_fisik,
                    'piket_kelas_terdata' => $piket_kelas_count,
                    'total_kelas' => $total_kelas_offline,
                ],

                // Data Spesifik Online
                'online' => [
                    'total_pertemuan' => $tot_pertemuan_online,
                    'jurnal_terisi' => $jurnal_terisi_online,
                    'absen_mapel_terisi' => $absen_terisi_online,
                    'guru_hadir' => $guru_hadir_online,
                    'total_guru' => $total_guru_online,
                    'piket_kelas_terdata' => $absen_terisi_online,
                    'total_kelas' => $total_kelas_online,
                ]
            ];
        } catch (Exception $e) {
            $res['admin_monitoring'] = [
                'total_pertemuan' => 0,
                'jurnal_terisi' => 0,
                'absen_mapel_terisi' => 0,
                'guru_hadir' => 0,
                'total_guru' => 0,
                'piket_kelas_terdata' => 0,
                'total_kelas' => 0,
                'offline' => ['total_pertemuan' => 0, 'jurnal_terisi' => 0, 'guru_hadir' => 0, 'total_guru' => 0, 'piket_kelas_terdata' => 0, 'total_kelas' => 0],
                'online' => ['total_pertemuan' => 0, 'jurnal_terisi' => 0, 'guru_hadir' => 0, 'total_guru' => 0, 'piket_kelas_terdata' => 0, 'total_kelas' => 0]
            ];
        }

        return $res;
    }
}