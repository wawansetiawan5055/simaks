<?php
require_once __DIR__ . '/../app/models/DashboardModel.php'; 
require_once __DIR__ . '/../app/models/JadwalModel.php';

class DashboardApiController {
    
    public static function handle($pdo, $act) {
        if (ob_get_length() > 0) { ob_clean(); }
        try {
            switch ($act) {
                // --- 0. SUMMARY ---
                case 'summary':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
                    if (!$id_ta || $id_ta == 0) {
                        $id_ta = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetchColumn() ?: 0;
                    }
                    try {
                        $user_id = $_SESSION['user_id'] ?? 0;
                        $user_roles = $_SESSION['roles'] ?? [];
                        $data = DashboardModel::summary($pdo, $user_id, $user_roles, (int)$id_ta);
                        echo json_encode(['status' => 'ok', 'data' => $data]);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat summary: ' . $e->getMessage()]);
                    }
                    break;

                // --- 1. REKAP SISWA ---
                case 'rekap_siswa':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
                    if (!$id_ta || $id_ta == 0) {
                        $id_ta = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetchColumn() ?: 0;
                    }
                    try {
                        if (!$id_ta || $id_ta == 0) {
                            throw new Exception("ID Tahun Ajaran tidak valid");
                        }
                        $data = DashboardModel::getRekapSiswaPerKelas($pdo, (int)$id_ta);
                        echo json_encode(['status' => 'ok', 'data' => $data]);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat rekap siswa: ' . $e->getMessage()]);
                    }
                    break;

                // --- 2. ABSENSI GURU ---
                case 'absensi_guru':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $id_ta = ($_GET['id_ta'] ?? 0) ?: ($_SESSION['id_ta_aktif'] ?? 0) ?: ($pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetchColumn() ?: 0);
                    $params = [
                        'periode' => $_GET['periode'] ?? 'daily',
                        'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
                        'semester' => $_GET['semester'] ?? '1',
                        'mode_kbm' => $_GET['mode_kbm'] ?? 'tatap_muka',
                        'id_ta' => (int)$id_ta
                    ];
                    $res = DashboardModel::getAbsensiGuruDetail($pdo, $params);
                    echo json_encode(['status' => 'ok', 'data' => $res['table'], 'chart' => $res['chart']]);
                    break;

                // --- 3. ABSENSI SISWA ---
                case 'absensi_siswa':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $id_ta = ($_GET['id_ta'] ?? 0) ?: ($_SESSION['id_ta_aktif'] ?? 0) ?: ($pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetchColumn() ?: 0);
                    $params = [
                        'periode' => $_GET['periode'] ?? 'daily',
                        'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
                        'semester' => $_GET['semester'] ?? '1',
                        'id_ta' => (int)$id_ta
                    ];
                    $res = DashboardModel::getAbsensiSiswaDetail($pdo, $params);
                    echo json_encode(['status' => 'ok', 'data' => $res['table'], 'chart' => $res['chart']]);
                    break;
                
                // --- 3b. DETAIL ABSENSI SISWA ---
                case 'absensi_siswa_detail':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $id_kelas = $_GET['id_kelas'] ?? 0;
                    $id_ta = ($_GET['id_ta'] ?? 0) ?: ($_SESSION['id_ta_aktif'] ?? 0) ?: ($pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetchColumn() ?: 0);
                    $params = [
                        'periode' => $_GET['periode'] ?? 'daily',
                        'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
                        'semester' => $_GET['semester'] ?? '1',
                        'id_ta' => (int)$id_ta
                    ];
                    $data = DashboardModel::getAbsensiSiswaPerSiswa($pdo, (int)$id_kelas, $params);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;
                
                // --- 4. DROPDOWN API ---
                case 'list_ta':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $sql = "SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC";
                    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;

                case 'list_kelas':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $id_ta = ($_GET['id_ta'] ?? 0) ?: ($_SESSION['id_ta_aktif'] ?? 0) ?: ($pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetchColumn() ?: 0);
                    $data = DashboardModel::getKelasList($pdo, (int)$id_ta);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;

                case 'list_guru_aktif':
                    if (!headers_sent()) header('Content-Type: application/json');
                    $data = DashboardModel::getGuruAktifList($pdo);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;
                
                // --- JADWAL GURU (MODAL) ---
                case 'jadwal_guru_html':
                    if (!headers_sent()) header('Content-Type: text/html'); 
                    $id_guru = $_SESSION['id_guru_terkait'] ?? 0; 
                    $is_guru_role = in_array('Guru', $_SESSION['roles'] ?? []);
                    
                    if ($id_guru == 0 || !$is_guru_role) {
                        echo '<div class="alert alert-danger text-center">Akses ditolak: ID Guru tidak terkait atau peran Guru tidak terdeteksi.</div>';
                        break;
                    }

                    $hari_inggris = date('l'); 
                    $map_hari = [
                        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                    ];
                    $hari_indo = $map_hari[$hari_inggris] ?? 'Hari Tidak Dikenal';
                    $tanggal_indo = date('d F Y');

                    $jadwal = DashboardModel::getJadwalGuruHariIni($pdo, $id_guru, $hari_indo);

                    if (empty($jadwal)) {
                        echo '<h5 class="text-center">Jadwal Hari: ' . $hari_indo . ' (' . $tanggal_indo . ')</h5>';
                        echo '<div class="alert alert-info text-center">Tidak ada jadwal mengajar pada hari ' . $hari_indo . '.</div>';
                        break;
                    }

                    echo '<h5 class="mb-3 font-weight-bold text-center">Jadwal Hari: ' . $hari_indo . ' (' . $tanggal_indo . ')</h5>';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered table-striped table-hover">';
                    echo '<thead class="thead-dark">
                            <tr>
                                <th class="text-center" style="width: 5%">No</th>
                                <th style="width: 15%">Jam Ke</th>
                                <th style="width: 25%">Waktu</th>
                                <th style="width: 20%">Kelas</th>
                                <th style="width: 35%">Mata Pelajaran</th>
                            </tr>
                        </thead>';
                    echo '<tbody>';
                    
                    $no = 1;
                    foreach ($jadwal as $j) {
                        $waktu_mulai = date('H:i', strtotime($j['jam_mulai']));
                        $waktu_selesai = date('H:i', strtotime($j['jam_selesai']));
                        
                        echo '<tr>';
                        echo '<td class="text-center">' . $no++ . '</td>';
                        echo '<td class="text-center font-weight-bold">Jam Ke-' . htmlspecialchars($j['label_jam_ke'] ?? $j['jam_ke']) . '</td>';
                        echo '<td>' . $waktu_mulai . ' - ' . $waktu_selesai . ' WIB</td>';
                        echo '<td><span class="badge badge-primary">' . htmlspecialchars($j['nama_kelas']) . '</span></td>';
                        echo '<td class="font-weight-bold">' . htmlspecialchars($j['nama_mapel']) . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'msg' => 'Invalid action: ' . $act]);
                    break;
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Server Error: ' . $e->getMessage()]);
        }
    }
}
