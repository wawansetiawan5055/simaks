<?php
require_once '../app/models/SiswaModel.php';
require_once '../app/models/CatatanKasusModel.php';

class SiswaApiController {
    public static function handle($pdo, $act) {
        switch ($act) {
            case 'get_ta_list': self::getTaList($pdo); break;
            case 'get_rekap_by_ta': self::getRekapByTa($pdo); break;
            case 'get_by_kelas': self::getSiswaByKelas($pdo); break;
            default: echo json_encode(['status'=>'error','msg'=>'Invalid action']);
        }
    }
    
    private static function getTaList($pdo) {
        $stmt = $pdo->query("SELECT id_ta, nama_ta FROM tahun_ajaran ORDER BY id_ta DESC");
        echo json_encode(['status' => 'ok', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    private static function getRekapByTa($pdo) {
        $id_ta = $_GET['id_ta'] ?? 0;
        
        // [FIX] Query yang lebih aman untuk mode strict
        // Menggunakan 'L'/'P' atau 'Laki-laki'/'Perempuan' dengan LIKE agar aman
        $sql = "SELECT k.nama_kelas,
                SUM(CASE WHEN s.jk LIKE 'L%' THEN 1 ELSE 0 END) laki,
                SUM(CASE WHEN s.jk LIKE 'P%' THEN 1 ELSE 0 END) perempuan,
                COUNT(ps.id_penempatan) total_siswa,
                0 mutasi_masuk, 0 mutasi_keluar, 0 lulus
                FROM kelas k
                LEFT JOIN penempatan_siswa ps ON k.id_kelas=ps.id_kelas AND ps.id_ta=?
                LEFT JOIN siswa s ON ps.id_siswa=s.id_siswa AND s.status_aktif='Aktif'
                GROUP BY k.id_kelas, k.nama_kelas, k.tingkat 
                ORDER BY k.tingkat, k.nama_kelas";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_ta]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    private static function getSiswaByKelas($pdo) {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        
        if (!$id_kelas || !$id_ta) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap']);
            return;
        }
        
        try {
            $data = CatatanKasusModel::getSiswaByKelas($pdo, $id_kelas, $id_ta);
            echo json_encode(['status' => 'ok', 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
?>