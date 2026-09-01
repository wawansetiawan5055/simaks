<?php
// Pastikan semua Model yang dibutuhkan di-require di sini
require_once __DIR__ . '/../models/DashboardModel.php';
require_once __DIR__ . '/../models/JadwalPiketModel.php';
require_once __DIR__ . '/../models/ProfilSekolahModel.php';
require_once __DIR__ . '/../models/JadwalModel.php'; // Diperlukan untuk jadwal pop-up
require_once __DIR__ . '/../models/TracerStudyModel.php'; // Untuk widget tracer study

// ==========================================================
// FUNGSI CONTROLLER UTAMA
// ==========================================================

function dashboard_index($pdo)
{
    // Tentukan TA mana yang akan ditampilkan
    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $user_id = $_SESSION['user_id'] ?? 0;
    $user_roles = user_roles();
    
    /*
    // [REVISI] Sembunyikan Dashboard Utama dari Siswa, arahkan ke Dashboard LMS
    if (in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles)) {
        header("Location: index.php?mod=lms");
        exit;
    }
    */
    

    // [PERFORMANCE] Close session setelah baca data - mencegah blocking concurrent requests
    close_session_early();

    // Data untuk Info Card (Temporal)
    $info_card = DashboardModel::summary($pdo, $user_id, $user_roles, $id_ta_tampil);

    // Data Profil Sekolah
    $profil_sekolah = ProfilSekolahModel::getProfil($pdo);

    // [BARU] Data Guru Piket Hari Ini untuk Slide Carousel Dashboard
    $map_hari = [
        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
    ];
    $hari_ini = $map_hari[date('l')] ?? 'Senin';
    $guru_piket_hari_ini = JadwalPiketModel::getPiketHariIni($pdo, $id_ta_tampil, $hari_ini);

    // [BARU] Status Tugas Harian Guru & Piket untuk Smart Banner 2-Slide & Smart Alert
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $tugas_status = DashboardModel::getTugasHarianStatus($pdo, $id_guru, $user_id, $user_roles, $id_ta_tampil, $hari_ini, date('Y-m-d'));

    // [BARU] Data Tracer Study (5 Tahun Terakhir) untuk Widget Dashboard
    $tracer_stats = TracerStudyModel::getStatisticsByYear($pdo, 5);

    // Data dummy absensi (Akan diabaikan karena diganti AJAX)
    $rekap_absensi_harian = [];

    // Variables required by footer.php
    $current_ta_id = $id_ta_tampil;
    // API URL: gunakan index.php routing dengan parameter type=api
    // Format: index.php?type=api&mod={api_type}&act={action}
    $api_url = 'index.php';

    extract(compact('info_card', 'profil_sekolah', 'rekap_absensi_harian', 'tracer_stats', 'api_url', 'current_ta_id', 'guru_piket_hari_ini', 'hari_ini', 'tugas_status'));
    include __DIR__ . '/../views/dashboard.php';
}

// ==========================================================
// FUNGSI API CONTROLLER (DIPANGGIL VIA AJAX)
// ==========================================================

/**
 * Mengambil ringkasan statistik (Guru, Siswa, Kelas, Mapel).
 */
function api_dashboard_get_summary($pdo)
{
    header('Content-Type: application/json');
    $id_ta = $_GET['id_ta'] ?? 0;
    try {
        $data = DashboardModel::summary($pdo, $_SESSION['user_id'] ?? 0, user_roles(), $id_ta);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat ringkasan: ' . $e->getMessage()]);
    }
}

/**
 * Mengambil rekapitulasi siswa per kelas (termasuk mutasi).
 */
function api_dashboard_get_rekap_siswa($pdo)
{
    header('Content-Type: application/json');
    $id_ta = $_GET['id_ta'] ?? 0;
    try {
        $data = DashboardModel::getRekapSiswaPerKelas($pdo, $id_ta);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat rekap siswa: ' . $e->getMessage()]);
    }
}

/**
 * Mengambil jadwal mengajar guru yang sedang login untuk hari ini.
 */
function api_dashboard_get_jadwal_guru_html($pdo)
{
    header('Content-Type: text/html');

    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    if ($id_guru == 0 || $id_ta_aktif == 0) {
        echo "<div class='alert alert-warning'>Anda tidak memiliki peran Guru yang terkait atau Tahun Ajaran belum aktif.</div>";
        return;
    }

    try {
        // Asumsi JadwalModel::getJadwalByGuruAndDay sudah tersedia
        $jadwal = JadwalModel::getJadwalByGuruAndDay($pdo, $id_guru, $id_ta_aktif, $tanggal);

        if (empty($jadwal)) {
            echo "<div class='alert alert-info'>Tidak ada jadwal mengajar pada tanggal **" . date('d F Y', strtotime($tanggal)) . "**</div>";
            return;
        }

        // Render HTML Tabel Jadwal
        echo "<div class='table-responsive'><table class='table table-bordered table-striped'>";
        echo "<thead class='bg-primary text-white'><tr><th>Jam Ke</th><th>Waktu</th><th>Kelas</th><th>Mapel</th></tr></thead>";
        echo "<tbody>";
        foreach ($jadwal as $j) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($j['jam_ke'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars(substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5)) . "</td>";
            echo "<td>" . htmlspecialchars($j['nama_kelas'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($j['nama_mapel'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table></div>";

    } catch (\Exception $e) {
        echo "<div class='alert alert-danger'>Terjadi kesalahan saat memuat jadwal: " . $e->getMessage() . "</div>";
    }
}


/**
 * Mengambil daftar Tahun Ajaran.
 */
function api_dashboard_get_ta_list($pdo)
{
    header('Content-Type: application/json');
    try {
        $sql = "SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC";
        $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat TA: ' . $e->getMessage()]);
    }
}

/**
 * Mengambil daftar Kelas.
 */
function api_dashboard_get_kelas_list($pdo)
{
    header('Content-Type: application/json');
    $id_ta = $_GET['id_ta'] ?? 0;
    try {
        if ($id_ta) {
            $sql = "SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_ta]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT id_kelas, nama_kelas, tingkat FROM kelas ORDER BY tingkat, nama_kelas";
            $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat Kelas: ' . $e->getMessage()]);
    }
}

/**
 * Mengambil rekap absensi guru dan siswa berdasarkan filter.
 * Mengembalikan data detail untuk tabel dan data total untuk grafik.
 */
/**
 * Mengambil rekap absensi guru dan siswa berdasarkan filter rentang tanggal.
 */
function api_dashboard_get_absensi_rekap($pdo)
{
    header('Content-Type: application/json');

    // Ambil parameter filter
    // Frontend sekarang mengirimkan start_date dan end_date
    $start_date = $_GET['start_date'] ?? date('Y-m-01');
    $end_date = $_GET['end_date'] ?? date('Y-m-d');

    $id_kelas = $_GET['id_kelas'] ?? null;
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;

    $results = [
        'status' => 'ok',
        'data_siswa_chart' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0],
        'data_guru_chart' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0],
        'data_siswa_detail' => [],
        'data_guru_detail' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0],
    ];

    try {
        // --- 1. FILTER SISWA ---
        // Penempatan Siswa harus sesuai Tahun Ajaran Aktif
        $where_ta = $id_ta_aktif ? " AND ps.id_ta = :id_ta " : "";
        $where_kelas = $id_kelas ? " AND ps.id_kelas = :id_kelas " : "";

        // Filter Tanggal
        $date_condition_siswa = " AND asw.tanggal BETWEEN :start_date AND :end_date ";

        $params_siswa = [
            ':id_ta' => $id_ta_aktif,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ];
        if ($id_kelas)
            $params_siswa[':id_kelas'] = $id_kelas;

        // SQL DETAIL SISWA (Per Kelas) - Gunakan `status` ('Hadir','Sakit'...)
        // CATATAN: Menggunakan absensi_siswa_piket jika absensi_siswa tidak ada/view
        $table_siswa = "absensi_siswa_piket";

        $sql_siswa_detail = "SELECT k.nama_kelas,
                          SUM(CASE WHEN asw.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                          SUM(CASE WHEN asw.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                          SUM(CASE WHEN asw.status = 'Izin' THEN 1 ELSE 0 END) AS I,
                          SUM(CASE WHEN asw.status = 'Alpa' THEN 1 ELSE 0 END) AS A
                   FROM $table_siswa asw
                   JOIN penempatan_siswa ps ON asw.id_siswa = ps.id_siswa
                   JOIN kelas k ON ps.id_kelas = k.id_kelas
                   WHERE asw.status IN ('Hadir', 'Sakit', 'Izin', 'Alpa') $where_ta $where_kelas $date_condition_siswa
                   GROUP BY k.nama_kelas
                   ORDER BY k.tingkat, k.nama_kelas";

        $stmt = $pdo->prepare($sql_siswa_detail);
        $stmt->execute(array_filter($params_siswa));
        $results['data_siswa_detail'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // SQL CHART SISWA (Total)
        $sql_siswa_chart = "SELECT status as keterangan, COUNT(*) as count 
                          FROM $table_siswa asw
                          JOIN penempatan_siswa ps ON asw.id_siswa = ps.id_siswa
                          WHERE asw.status IN ('Hadir', 'Sakit', 'Izin', 'Alpa') $where_ta $where_kelas $date_condition_siswa
                          GROUP BY status";
        $stmt = $pdo->prepare($sql_siswa_chart);
        $stmt->execute(array_filter($params_siswa));
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            // Map output ke kode singkat untuk frontend (H, S, I, A)
            $code = strtoupper(substr($row['keterangan'], 0, 1)); // Hadir -> H
            $results['data_siswa_chart'][$code] = (int) $row['count'];
        }

        // --- 2. FILTER GURU (TATAP MUKA VS ONLINE) ---
        $mode_kbm = $_GET['mode_kbm'] ?? 'tatap_muka';
        $day_map = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

        if ($mode_kbm === 'online') {
            // MODE ONLINE: Hanya tampilkan guru yang memiliki jadwal online
            if ($start_date == $end_date) {
                // Harian: Hanya guru yang punya jadwal online pada HARI INI
                $day_name = $day_map[date('w', strtotime($start_date))] ?? 'Senin';
                $sql_guru_online = "
                    SELECT DISTINCT g.id_guru, g.nama
                    FROM guru g
                    JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = ?
                    JOIN jadwal_mengajar jm ON gm.id_guru_mapel = jm.id_guru_mapel AND jm.hari_kbm = ?
                    JOIN kelas k ON jm.id_kelas = k.id_kelas AND (jm.mode_kbm = 'online' OR k.jenis_kelas = 'pjj')
                    WHERE g.status = 'Aktif'
                    ORDER BY g.nama
                ";
                $stmt_go = $pdo->prepare($sql_guru_online);
                $stmt_go->execute([$id_ta_aktif, $day_name]);
            } else {
                // Rentang Bulanan / Semester: Guru yang punya jadwal online di TA aktif
                $sql_guru_online = "
                    SELECT DISTINCT g.id_guru, g.nama
                    FROM guru g
                    JOIN guru_mapel gm ON g.id_guru = gm.id_guru AND gm.id_ta = ?
                    JOIN jadwal_mengajar jm ON gm.id_guru_mapel = jm.id_guru_mapel
                    JOIN kelas k ON jm.id_kelas = k.id_kelas AND (jm.mode_kbm = 'online' OR k.jenis_kelas = 'pjj')
                    WHERE g.status = 'Aktif'
                    ORDER BY g.nama
                ";
                $stmt_go = $pdo->prepare($sql_guru_online);
                $stmt_go->execute([$id_ta_aktif]);
            }
            $guru_online_rows = $stmt_go->fetchAll(PDO::FETCH_ASSOC);

            $data_online_detail = [];
            foreach ($guru_online_rows as $gor) {
                $id_g = $gor['id_guru'];
                // Hitung jurnal KBM online yang terisi pada rentang tanggal
                $sql_j = "
                    SELECT COUNT(DISTINCT CONCAT(j.tanggal, '_', j.id_kelas)) 
                    FROM jurnal_kbm j
                    JOIN kelas k ON j.id_kelas = k.id_kelas AND k.jenis_kelas = 'pjj'
                    WHERE j.id_guru = ? AND j.tanggal BETWEEN ? AND ? AND j.id_ta = ?
                ";
                $stmt_j = $pdo->prepare($sql_j);
                $stmt_j->execute([$id_g, $start_date, $end_date, $id_ta_aktif]);
                $h_count = (int)$stmt_j->fetchColumn();

                // Hitung Sakit / Izin dari absensi_guru
                $stmt_si = $pdo->prepare("SELECT SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) AS S, SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END) AS I FROM absensi_guru WHERE id_guru = ? AND tanggal BETWEEN ? AND ?");
                $stmt_si->execute([$id_g, $start_date, $end_date]);
                $si_row = $stmt_si->fetch(PDO::FETCH_ASSOC);
                $s_count = (int)($si_row['S'] ?? 0);
                $i_count = (int)($si_row['I'] ?? 0);

                // Untuk KBM Online yang belum berjalan, Alpa tidak diisi (A = 0)
                $a_count = 0;

                $data_online_detail[] = [
                    'nama' => $gor['nama'],
                    'H' => $h_count,
                    'S' => $s_count,
                    'I' => $i_count,
                    'A' => $a_count
                ];
            }
            $results['data_guru_detail'] = $data_online_detail;
        } else {
            // MODE TATAP MUKA: Hanya GTK yang memiliki kewajiban fisik (KBM Offline, Piket, Non-KBM, Tendik)
            if ($start_date == $end_date) {
                // Harian: Ambil GTK yang wajib hadir fisik pada tanggal tersebut
                $guru_fisik = AbsensiGuruModel::getGuruWithScheduleOnDate($pdo, $start_date, $id_ta_aktif);
                $absensi_map = AbsensiGuruModel::getAbsensiByTanggal($pdo, $start_date, $id_ta_aktif);

                $data_fisik_detail = [];
                foreach ($guru_fisik as $gf) {
                    $id_g = $gf['id_guru'];
                    $status = $absensi_map[$id_g]['status'] ?? 'Hadir';
                    $data_fisik_detail[] = [
                        'nama' => $gf['nama'],
                        'H' => ($status === 'Hadir' ? 1 : 0),
                        'S' => ($status === 'Sakit' ? 1 : 0),
                        'I' => ($status === 'Izin' ? 1 : 0),
                        'A' => ($status === 'Alpa' ? 1 : 0),
                    ];
                }
                $results['data_guru_detail'] = $data_fisik_detail;
            } else {
                // Bulanan / Semester
                $params_guru = [
                    ':start_date' => $start_date,
                    ':end_date' => $end_date,
                    ':id_ta1' => $id_ta_aktif,
                    ':id_ta2' => $id_ta_aktif,
                    ':id_ta3' => $id_ta_aktif
                ];

                $sql_guru_rekap = "
                    SELECT g.nama,
                           SUM(CASE WHEN ag.status = 'Hadir' THEN 1 ELSE 0 END) AS H,
                           SUM(CASE WHEN ag.status = 'Sakit' THEN 1 ELSE 0 END) AS S,
                           SUM(CASE WHEN ag.status = 'Izin' THEN 1 ELSE 0 END) AS I,
                           SUM(CASE WHEN ag.status = 'Alpa' THEN 1 ELSE 0 END) AS A
                    FROM absensi_guru ag
                    JOIN guru g ON ag.id_guru = g.id_guru
                    WHERE ag.status IN ('Hadir', 'Sakit', 'Izin', 'Alpa') 
                      AND ag.tanggal BETWEEN :start_date AND :end_date
                      AND g.id_guru IN (
                          SELECT gm.id_guru FROM guru_mapel gm JOIN jadwal_mengajar jm ON gm.id_guru_mapel = jm.id_guru_mapel JOIN kelas k ON jm.id_kelas = k.id_kelas WHERE gm.id_ta = :id_ta1 AND (jm.mode_kbm = 'offline' OR (jm.mode_kbm IS NULL AND (k.jenis_kelas != 'pjj' OR k.jenis_kelas IS NULL)))
                          UNION
                          SELECT id_guru FROM jadwal_guru_piket WHERE id_ta = :id_ta2
                          UNION
                          SELECT id_guru FROM jadwal_guru_non_kbm WHERE id_ta = :id_ta3
                          UNION
                          SELECT g2.id_guru FROM guru g2 JOIN pengguna p ON g2.id_pengguna = p.id_pengguna JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna JOIN peran pr ON pp.id_peran = pr.id_peran WHERE pr.nama_peran IN ('TU', 'Tenaga Kependidikan')
                      )
                    GROUP BY g.nama
                    ORDER BY g.nama
                ";

                $stmt = $pdo->prepare($sql_guru_rekap);
                $stmt->execute($params_guru);
                $results['data_guru_detail'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        // Hitung Total untuk Chart Guru
        foreach ($results['data_guru_detail'] as $row) {
            $results['data_guru_chart']['H'] += (int)$row['H'];
            $results['data_guru_chart']['S'] += (int)$row['S'];
            $results['data_guru_chart']['I'] += (int)$row['I'];
            $results['data_guru_chart']['A'] += (int)$row['A'];
        }

        // --- MAPPING OUTPUT SESUAI FRONTEND ---
        $act = $_GET['act'] ?? '';
        $results['status'] = 'ok';

        if ($act === 'absensi_guru') {
            $results['data'] = $results['data_guru_detail'] ?? [];
            $results['chart'] = $results['data_guru_chart'] ?? ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        } else {
            // Default: absensi_siswa
            $results['data'] = $results['data_siswa_detail'] ?? [];
            $results['chart'] = $results['data_siswa_chart'] ?? ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        }

        echo json_encode($results);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat absensi: ' . $e->getMessage()]);
    }
}

/**
 * Mengambil statistik tracer study untuk dashboard widget
 * Mengembalikan data 5 tahun terakhir untuk tabel dan grafik
 */
function api_dashboard_get_tracer_statistics($pdo)
{
    header('Content-Type: application/json');
    try {
        $data = TracerStudyModel::getStatisticsByYear($pdo, 5);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat statistik tracer: ' . $e->getMessage()]);
    }
}