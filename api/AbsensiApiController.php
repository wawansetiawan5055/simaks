<?php
class AbsensiApiController {
    public static function handle($pdo, $act) {
        switch ($act) {
            case 'get_status_for_jurnal':
                self::getStatusForJurnal($pdo);
                break;
            case 'scan_absensi':
                self::scanAbsensi($pdo);
                break;
            case 'get_today_scans':
                self::getTodayScans($pdo);
                break;
            default:
                echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
                break;
        }
    }

    private static function getStatusForJurnal($pdo) {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? '';
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        // [FIX] Terima filter id_guru_mapel agar rekap hanya dari 1 mapel
        $id_guru_mapel = (int)($_GET['id_guru_mapel'] ?? 0);

        if (!$id_kelas || !$tanggal || !$id_ta) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap']);
            return;
        }

        // Cek apakah absensi sudah diisi (dengan filter guru_mapel jika ada)
        if ($id_guru_mapel) {
            $stmt_check = $pdo->prepare(
                "SELECT COUNT(*) FROM absensi_siswa_mapel 
                 WHERE id_kelas = ? AND tanggal = ? AND id_ta = ? AND id_guru_mapel = ?"
            );
            $stmt_check->execute([$id_kelas, $tanggal, $id_ta, $id_guru_mapel]);
        } else {
            $stmt_check = $pdo->prepare(
                "SELECT COUNT(*) FROM absensi_siswa_mapel 
                 WHERE id_kelas = ? AND tanggal = ? AND id_ta = ?"
            );
            $stmt_check->execute([$id_kelas, $tanggal, $id_ta]);
        }
        $absensi_exists = $stmt_check->fetchColumn() > 0;

        $rekap_string = "Belum mengisi absensi.";

        if ($absensi_exists) {
            // [FIX] Rekap juga difilter per guru_mapel agar tidak campur dari mapel lain
            if ($id_guru_mapel) {
                $stmt_rekap = $pdo->prepare(
                    "SELECT s.nama, a.status
                     FROM absensi_siswa_mapel a
                     JOIN siswa s ON a.id_siswa = s.id_siswa
                     WHERE a.id_kelas = ? AND a.tanggal = ? AND a.id_ta = ? AND a.id_guru_mapel = ?
                       AND a.status IS NOT NULL AND a.status != '' AND a.status != 'Hadir'
                     ORDER BY s.nama ASC"
                );
                $stmt_rekap->execute([$id_kelas, $tanggal, $id_ta, $id_guru_mapel]);
            } else {
                $stmt_rekap = $pdo->prepare(
                    "SELECT s.nama, GROUP_CONCAT(DISTINCT a.status ORDER BY a.status SEPARATOR ', ') AS status 
                     FROM absensi_siswa_mapel a
                     JOIN siswa s ON a.id_siswa = s.id_siswa
                     WHERE a.id_kelas = ? AND a.tanggal = ? AND a.id_ta = ?
                       AND a.status IS NOT NULL AND a.status != '' AND a.status != 'Hadir'
                     GROUP BY a.id_siswa
                     ORDER BY s.nama ASC"
                );
                $stmt_rekap->execute([$id_kelas, $tanggal, $id_ta]);
            }
            $results = $stmt_rekap->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                $rekap_string = "Semua siswa hadir.";
            } else {
                $summary = [];
                foreach ($results as $row) {
                    $status_text = !empty($row['status']) ? $row['status'] : 'Tanpa Keterangan';
                    $summary[] = $row['nama'] . ' (' . $status_text . ')';
                }
                $rekap_string = implode(', ', $summary);
            }
        }

        echo json_encode([
            'status' => 'ok',
            'absensi_diisi' => $absensi_exists,
            'rekap_absensi' => $rekap_string
        ]);
    }

    /**
     * API SCAN QR / BARCODE ABSENSI (SISWA & GURU)
     */
    private static function scanAbsensi($pdo) {
        $code = trim($_REQUEST['code'] ?? '');
        $tanggal = $_REQUEST['tanggal'] ?? date('Y-m-d');
        $cutoff = $_REQUEST['cutoff'] ?? '08:15:00';
        if (strlen($cutoff) === 5) $cutoff .= ':00';

        if (empty($code)) {
            echo json_encode(['status' => 'error', 'msg' => 'Kode QR / Barcode tidak boleh kosong']);
            return;
        }

        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        if (!$id_ta) {
            $id_ta = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE aktif = 1 LIMIT 1")->fetchColumn() ?: 7;
        }

        $id_guru_piket = $_SESSION['id_guru_terkait'] ?? 0;
        if (!$id_guru_piket) {
            $id_guru_piket = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn() ?: 1;
        }

        $waktu_sekarang = date('H:i:s');
        $is_terlambat = ($waktu_sekarang > $cutoff);
        $status = 'Hadir';
        $keterangan = $is_terlambat 
            ? 'Terlambat (Jam ' . date('H:i') . ')' 
            : 'Scan QR (' . date('H:i') . ')';

        // 1. CARI SISWA
        $stmt_siswa = $pdo->prepare("
            SELECT s.id_siswa, s.nama, s.nisn, k.id_kelas, k.nama_kelas
            FROM siswa s
            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ?
            JOIN kelas k ON ps.id_kelas = k.id_kelas
            LEFT JOIN pengguna p ON s.id_pengguna = p.id_pengguna
            WHERE s.nisn = ? OR s.nipd = ? OR s.nik = ? 
               OR p.qr_token = ? OR p.username = ? OR s.id_siswa = ?
            LIMIT 1
        ");
        $stmt_siswa->execute([$id_ta, $code, $code, $code, $code, $code, $code]);
        $siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

        if ($siswa) {
            // SIMPAN / UPDATE ABSENSI SISWA PIKET
            $stmt_check = $pdo->prepare(
                "SELECT id_absensi FROM absensi_siswa_piket WHERE id_siswa = ? AND tanggal = ?"
            );
            $stmt_check->execute([$siswa['id_siswa'], $tanggal]);
            $existing_id = $stmt_check->fetchColumn();

            if ($existing_id) {
                $stmt_up = $pdo->prepare(
                    "UPDATE absensi_siswa_piket SET status = ?, keterangan = ?, id_guru_piket = ? WHERE id_absensi = ?"
                );
                $stmt_up->execute([$status, $keterangan, $id_guru_piket, $existing_id]);
            } else {
                $stmt_in = $pdo->prepare(
                    "INSERT INTO absensi_siswa_piket (id_siswa, id_kelas, id_ta, tanggal, status, keterangan, id_guru_piket)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt_in->execute([
                    $siswa['id_siswa'], $siswa['id_kelas'], $id_ta, $tanggal,
                    $status, $keterangan, $id_guru_piket
                ]);
            }

            echo json_encode([
                'status' => 'ok',
                'person_type' => 'siswa',
                'nama' => $siswa['nama'],
                'info' => 'Kelas ' . $siswa['nama_kelas'],
                'waktu' => date('H:i:s'),
                'is_terlambat' => $is_terlambat,
                'kehadiran' => $status,
                'keterangan' => $keterangan,
                'msg' => 'Absensi Siswa ' . ($is_terlambat ? '(Terlambat)' : '(Hadir)') . ' berhasil dicatat!'
            ]);
            return;
        }

        // 2. CARI GURU
        $stmt_guru = $pdo->prepare("
            SELECT g.id_guru, g.nama, g.nuptk, g.nik
            FROM guru g
            LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
            WHERE g.nuptk = ? OR g.nik = ? OR g.kode_guru = ? 
               OR p.qr_token = ? OR p.username = ? OR g.id_guru = ?
            LIMIT 1
        ");
        $stmt_guru->execute([$code, $code, $code, $code, $code, $code]);
        $guru = $stmt_guru->fetch(PDO::FETCH_ASSOC);

        if ($guru) {
            // SIMPAN / UPDATE ABSENSI GURU
            $stmt_check = $pdo->prepare(
                "SELECT id_absensi FROM absensi_guru WHERE id_guru = ? AND tanggal = ?"
            );
            $stmt_check->execute([$guru['id_guru'], $tanggal]);
            $existing_id = $stmt_check->fetchColumn();

            if ($existing_id) {
                $stmt_up = $pdo->prepare(
                    "UPDATE absensi_guru SET status = ?, keterangan = ?, id_guru_piket = ? WHERE id_absensi = ?"
                );
                $stmt_up->execute([$status, $keterangan, $id_guru_piket, $existing_id]);
            } else {
                $stmt_in = $pdo->prepare(
                    "INSERT INTO absensi_guru (id_guru, id_ta, tanggal, status, keterangan, id_guru_piket)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt_in->execute([
                    $guru['id_guru'], $id_ta, $tanggal,
                    $status, $keterangan, $id_guru_piket
                ]);
            }

            echo json_encode([
                'status' => 'ok',
                'person_type' => 'guru',
                'nama' => $guru['nama'],
                'info' => 'Guru',
                'waktu' => date('H:i:s'),
                'is_terlambat' => $is_terlambat,
                'kehadiran' => $status,
                'keterangan' => $keterangan,
                'msg' => 'Absensi Guru ' . ($is_terlambat ? '(Terlambat)' : '(Hadir)') . ' berhasil dicatat!'
            ]);
            return;
        }

        // KODE TIDAK DITEMUKAN
        echo json_encode([
            'status' => 'error',
            'msg' => 'Kode QR / Barcode "' . htmlspecialchars($code) . '" tidak ditemukan di data Siswa maupun Guru!'
        ]);
    }

    /**
     * RETRIEVE RECENT SCAN LOGS FOR TODAY
     */
    private static function getTodayScans($pdo) {
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        if (!$id_ta) {
            $id_ta = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE aktif = 1 LIMIT 1")->fetchColumn() ?: 7;
        }

        // Siswa Piket
        $stmt_s = $pdo->prepare("
            SELECT a.id_absensi, s.nama, k.nama_kelas as info, 'siswa' as type, a.status, a.keterangan, a.id_absensi as sort_id
            FROM absensi_siswa_piket a
            JOIN siswa s ON a.id_siswa = s.id_siswa
            JOIN kelas k ON a.id_kelas = k.id_kelas
            WHERE a.tanggal = ? AND a.id_ta = ?
        ");
        $stmt_s->execute([$tanggal, $id_ta]);
        $siswa_scans = $stmt_s->fetchAll(PDO::FETCH_ASSOC);

        // Guru
        $stmt_g = $pdo->prepare("
            SELECT a.id_absensi, g.nama, 'Guru' as info, 'guru' as type, a.status, a.keterangan, a.id_absensi as sort_id
            FROM absensi_guru a
            JOIN guru g ON a.id_guru = g.id_guru
            WHERE a.tanggal = ? AND a.id_ta = ?
        ");
        $stmt_g->execute([$tanggal, $id_ta]);
        $guru_scans = $stmt_g->fetchAll(PDO::FETCH_ASSOC);

        $merged = array_merge($siswa_scans, $guru_scans);
        usort($merged, function($a, $b) {
            return $b['sort_id'] <=> $a['sort_id'];
        });

        echo json_encode([
            'status' => 'ok',
            'data' => $merged,
            'total' => count($merged)
        ]);
    }
}