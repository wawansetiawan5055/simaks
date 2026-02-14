<?php
class JadwalApiController
{
    public static function handle($pdo, $act)
    {
        if ($act == 'get_by_kelas_dan_tanggal') {
            self::getJadwalByKelasDanTanggal($pdo);
        } elseif ($act == 'get_daily') {
            self::getDailySchedule($pdo);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
        }
    }

    private static function getJadwalByKelasDanTanggal($pdo)
    {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? '';
        $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;

        if (!$id_kelas || !$tanggal || !$id_ta_aktif) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap.']);
            return;
        }

        $dayOfWeek = date('w', strtotime($tanggal));
        $hari_map = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari_kbm = $hari_map[$dayOfWeek];

        $sql = "SELECT dm.id_jadwal_mengajar, jp.jam_mulai, jp.jam_selesai, m.nama_mapel, m.id_mapel, k.tingkat, g.nama
            FROM jadwal_mengajar dm
                JOIN jam_pelajaran jp ON dm.id_jam = jp.id_jam
                JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                JOIN guru g ON gm.id_guru = g.id_guru
                WHERE dm.id_kelas = ? AND dm.hari_kbm = ? AND gm.id_ta = ?";

        $params = [$id_kelas, $hari_kbm, $id_ta_aktif];

        // PERBAIKAN: Filter berdasarkan ID guru jika pengguna memiliki peran 'Guru'
        // KECUALI jika juga memiliki peran Admin, GuruPiket, atau TU
        $user_roles = $_SESSION['roles'] ?? [];
        $is_admin = in_array('Admin', $user_roles);
        $is_piket = in_array('GuruPiket', $user_roles);
        $is_tu = in_array('TU', $user_roles);
        $is_guru = in_array('Guru', $user_roles);

        if ($is_guru && !$is_admin && !$is_piket && !$is_tu) {
            $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
            if ($id_guru_login) {
                $sql .= " AND gm.id_guru = ?";
                $params[] = $id_guru_login;
            }
        }

        $sql .= " ORDER BY jp.jam_mulai ASC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_clean(); // Ensure no previous output
            echo json_encode(['status' => 'ok', 'data' => $jadwal]);
        } catch (Exception $e) {
            http_response_code(500);
            ob_clean();
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    private static function getDailySchedule($pdo)
    {
        $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
        if (!$id_ta_aktif) {
            echo json_encode(['status' => 'error', 'msg' => 'TA Belum Aktif']);
            return;
        }

        $hari_map = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari_ini = $hari_map[date('w')];

        // QUERY DASAR
        $sql = "SELECT 
                    jp.jam_mulai, 
                    jp.jam_selesai, 
                    m.nama_mapel AS mapel, 
                    k.nama_kelas AS kelas,
                    g.nama AS guru
                FROM jadwal_mengajar jm
                JOIN jam_pelajaran jp ON jm.id_jam = jp.id_jam
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN kelas k ON jm.id_kelas = k.id_kelas
                JOIN guru g ON gm.id_guru = g.id_guru
                WHERE jm.hari_kbm = ? AND gm.id_ta = ?";

        $params = [$hari_ini, $id_ta_aktif];

        // LOGIKA HAK AKSES
        $user_roles = $_SESSION['roles'] ?? [];

        // Jika Admin, Guru Piket, atau TU, Ambil SEMUA
        if (in_array('Admin', $user_roles) || in_array('GuruPiket', $user_roles) || in_array('TU', $user_roles)) {
            // No additional filter needed
        }
        // Jika Guru Biasa (bukan piket), Ambil HANYA jadwal dia
        elseif (in_array('Guru', $user_roles)) {
            $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
            $sql .= " AND gm.id_guru = ?";
            $params[] = $id_guru;
        } else {
            ob_clean();
            echo json_encode(['status' => 'success', 'data' => []]);
            return;
        }

        $sql .= " ORDER BY jp.jam_mulai ASC, k.nama_kelas ASC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}