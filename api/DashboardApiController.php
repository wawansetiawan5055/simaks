<?php
require_once __DIR__ . '/../app/models/DashboardModel.php'; 
require_once __DIR__ . '/../app/models/JadwalModel.php'; // Pastikan ini ada jika menggunakan jadwal

class DashboardApiController {
    
    public static function handle($pdo, $act) {
        
        // SESSION CHECK (Opsional, sesuaikan dengan keamanan Anda)
        // if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }

        try {
            switch ($act) {
                // --- 1. REKAP SISWA ---
                case 'rekap_siswa':
                    header('Content-Type: application/json');
                    $id_ta = $_GET['id_ta'] ?? 0;
                    try {
                        if (!$id_ta || $id_ta == 0) {
                            throw new Exception("ID Tahun Ajaran tidak valid");
                        }
                        $data = DashboardModel::getRekapSiswaPerKelas($pdo, $id_ta);
                        echo json_encode(['status' => 'ok', 'data' => $data]);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(['status' => 'error', 'msg' => 'Gagal memuat rekap siswa: ' . $e->getMessage()]);
                    }
                    break;

                // --- 2. ABSENSI GURU ---
                case 'absensi_guru':
                    header('Content-Type: application/json');
                    $params = [
                        'periode' => $_GET['periode'] ?? 'daily',
                        'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
                        'semester' => $_GET['semester'] ?? '1',
                        'id_ta' => $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0
                    ];
                    $res = DashboardModel::getAbsensiGuruDetail($pdo, $params);
                    echo json_encode(['status' => 'ok', 'data' => $res['table'], 'chart' => $res['chart']]);
                    break;

                // --- 3. ABSENSI SISWA ---
                case 'absensi_siswa':
                    header('Content-Type: application/json');
                    $params = [
                        'periode' => $_GET['periode'] ?? 'daily',
                        'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
                        'semester' => $_GET['semester'] ?? '1',
                        'id_ta' => $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0
                    ];
                    $res = DashboardModel::getAbsensiSiswaDetail($pdo, $params);
                    echo json_encode(['status' => 'ok', 'data' => $res['table'], 'chart' => $res['chart']]);
                    break;
                
                // --- 3b. DETAIL ABSENSI SISWA ---
                case 'absensi_siswa_detail':
                    header('Content-Type: application/json');
                    $id_kelas = $_GET['id_kelas'] ?? 0;
                    $params = [
                        'periode' => $_GET['periode'] ?? 'daily',
                        'tanggal' => $_GET['tanggal'] ?? date('Y-m-d'),
                        'semester' => $_GET['semester'] ?? '1',
                        'id_ta' => $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0
                    ];
                    $data = DashboardModel::getAbsensiSiswaPerSiswa($pdo, $id_kelas, $params);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;
                
                // --- 4. DROPDOWN API ---
                case 'list_ta':
                    header('Content-Type: application/json');
                    $sql = "SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC";
                    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;

                case 'list_kelas':
                    header('Content-Type: application/json');
                    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0;
                    $data = DashboardModel::getKelasList($pdo, $id_ta);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;

                case 'list_guru_aktif': /* <--- ENDPOINT BARU UNTUK FILTER GURU */
                    header('Content-Type: application/json');
                    $data = DashboardModel::getGuruAktifList($pdo);
                    echo json_encode(['status' => 'ok', 'data' => $data]);
                    break;
                
                // --- JADWAL GURU (MODAL) ---
            case 'jadwal_guru_html':
                header('Content-Type: text/html'); 
                
                // Ambil ID Guru yang TERKAIT (sudah di-lookup saat login)
                $id_guru = $_SESSION['id_guru_terkait'] ?? 0; 
                
                // --- PENCEGAHAN: Cek peran Guru ---
                $is_guru_role = in_array('Guru', $_SESSION['roles'] ?? []); // Asumsi peran Guru tersimpan di 'Guru'
                
                if ($id_guru == 0 || !$is_guru_role) {
                    echo '<div class="alert alert-danger text-center">Akses ditolak: ID Guru tidak terkait atau peran Guru tidak terdeteksi.</div>';
                    break;
                }

                // Tentukan Hari Ini
                $hari_inggris = date('l'); 
                $map_hari = [
                    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                ];
                $hari_indo = $map_hari[$hari_inggris] ?? 'Hari Tidak Dikenal';
                $tanggal_indo = date('d F Y'); // Menampilkan tanggal hari ini (Contoh: 04 Desember 2025)

                // Panggil Model
                $jadwal = DashboardModel::getJadwalGuruHariIni($pdo, $id_guru, $hari_indo);

                if (empty($jadwal)) {
                    echo '<h5 class="text-center">Jadwal Hari: ' . $hari_indo . ' (' . $tanggal_indo . ')</h5>';
                    echo '<div class="alert alert-info text-center">Tidak ada jadwal mengajar pada hari ' . $hari_indo . '.</div>';
                } else {
                    echo '<h5 class="text-center">Jadwal Mengajar Hari: ' . $hari_indo . ' (' . $tanggal_indo . ')</h5>';
                    echo '<div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Jam</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    foreach ($jadwal as $j) {
                        $waktu = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5);
                        echo "<tr>
                                <td class='text-center'>{$waktu}</td>
                                <td class='text-center font-weight-bold'>{$j['nama_kelas']}</td>
                                <td>{$j['nama_mapel']}</td>
                              </tr>";
                    }
                    echo '      </tbody>
                            </table>
                          </div>';
                }
                break;
            }
        } catch (Exception $e) {
            http_response_code(500);
            if (!headers_sent()) header('Content-Type: application/json');
            echo json_encode(['status'=>'error', 'msg'=> $e->getMessage()]);
        }
    }
}