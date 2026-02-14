<?php
// PERBAIKAN: Path diubah dari ../../ menjadi ../
require_once __DIR__ . '/../app/models/NilaiModel.php';
require_once __DIR__ . '/../app/models/CpTpModel.php';

class SumatifApiController {

    public static function handle($pdo, $act) {
        if ($act == 'get_mapel_by_kelas') {
            self::getMapelByKelas($pdo);
        } elseif ($act == 'get_tp_by_mapel') {
            self::getTpByMapel($pdo);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Aksi API tidak valid']);
        }
    }

    private static function getMapelByKelas($pdo) {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

        if (has_role('Admin')) {
             $id_guru = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
        }

        if (!$id_kelas || !$id_guru || !$id_ta) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap (kelas/guru/TA)']);
            return;
        }

        $mapel_list = NilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas, $id_ta);
        echo json_encode(['status' => 'ok', 'data' => $mapel_list]);
    }

    private static function getTpByMapel($pdo) {
        $id_guru_mapel = $_GET['id_guru_mapel'] ?? 0;
        if (!$id_guru_mapel) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter id_guru_mapel tidak ada']);
            return;
        }

        $stmtMapel = $pdo->prepare("SELECT id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
        $stmtMapel->execute([$id_guru_mapel]);
        $id_mapel_asli = $stmtMapel->fetchColumn();

        if (!$id_mapel_asli) {
            echo json_encode(['status' => 'error', 'msg' => 'Mapel tidak ditemukan']);
            return;
        }

        $tp_list = CpTpModel::getTpByMapel($pdo, $id_mapel_asli);
        echo json_encode(['status' => 'ok', 'data' => $tp_list]);
    }
}