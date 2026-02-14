<?php
class AbsensiApiController {
    public static function handle($pdo, $act) {
        if ($act == 'get_status_for_jurnal') {
            self::getStatusForJurnal($pdo);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
        }
    }

    private static function getStatusForJurnal($pdo) {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        $tanggal = $_GET['tanggal'] ?? '';
        $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
        
        if (has_role('Admin')) {
            // Jika admin, ambil guru pertama untuk testing
            $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
        }

        if (!$id_kelas || !$tanggal || !$id_guru) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap (ID Kelas, Tanggal, atau ID Guru)']);
            return;
        }

        // =================================================================
        // [REVISI TOTAL LOGIKA API]
        // =================================================================

        // 1. Ambil SEMUA id_guru_mapel yang DIAJAR oleh guru ini
        $stmt_guru_mapel = $pdo->prepare("SELECT id_guru_mapel FROM guru_mapel WHERE id_guru = ?");
        $stmt_guru_mapel->execute([$id_guru]);
        $guru_mapel_ids = $stmt_guru_mapel->fetchAll(PDO::FETCH_COLUMN);

        if (empty($guru_mapel_ids)) {
            // Guru ini tidak mengajar mapel apapun
            echo json_encode([
                'status' => 'ok',
                'absensi_diisi' => false, // Dianggap belum diisi
                'rekap_absensi' => 'Guru tidak terdaftar mengajar mapel apapun.'
            ]);
            return;
        }
        
        // Buat placeholder untuk query IN (...)
        $placeholders = implode(',', array_fill(0, count($guru_mapel_ids), '?'));

        // 2. Cek apakah ada data absensi
        // Query dicek berdasarkan id_kelas, tanggal, DAN id_guru_mapel milik guru ybs
        $stmt_check = $pdo->prepare(
            "SELECT COUNT(*) FROM absensi_siswa_mapel 
             WHERE id_kelas = ? AND tanggal = ? AND id_guru_mapel IN ($placeholders)"
        );
        $params_check = array_merge([$id_kelas, $tanggal], $guru_mapel_ids);
        $stmt_check->execute($params_check);
        $absensi_exists = $stmt_check->fetchColumn() > 0;
        
        $rekap_string = "Belum mengisi absensi.";
        
        if ($absensi_exists) {
            // 3. Ambil data rekap
            // Query rekap JUGA difilter berdasarkan id_guru_mapel milik guru ybs
            $stmt_rekap = $pdo->prepare(
                "SELECT s.nama, a.status 
                 FROM absensi_siswa_mapel a
                 JOIN siswa s ON a.id_siswa = s.id_siswa
                 WHERE a.id_kelas = ? AND a.tanggal = ? AND a.status != 'Hadir' AND a.status IS NOT NULL AND a.status != ''
                 AND a.id_guru_mapel IN ($placeholders)
                 ORDER BY s.nama ASC"
            );
            $params_rekap = array_merge([$id_kelas, $tanggal], $guru_mapel_ids);
            $stmt_rekap->execute($params_rekap);
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
        // =================================================================
        // [AKHIR REVISI]
        // =================================================================

        echo json_encode([
            'status' => 'ok',
            'absensi_diisi' => $absensi_exists,
            'rekap_absensi' => $rekap_string
        ]);
    }
}