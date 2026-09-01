<?php
// PERBAIKAN: Path diubah dari ../../ menjadi ../
require_once __DIR__ . '/../app/models/NilaiModel.php';
require_once __DIR__ . '/../app/models/CpTpModel.php';

class SumatifApiController {

    public static function handle($pdo, $act) {
        if ($act == 'get_mapel_by_kelas') {
            self::getMapelByKelas($pdo);
        } elseif ($act == 'get_cp_by_mapel') {
            self::getCpByMapel($pdo);
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
        $roles = $_SESSION['roles'] ?? [];

        if (!$id_kelas || !$id_ta) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap (id_kelas atau id_ta kosong)']);
            return;
        }

        $isAdmin = in_array('Admin', $roles) || in_array('TU', $roles) || in_array('Kepala Sekolah', $roles) || in_array('Kurikulum', $roles) || !$id_guru;

        if ($isAdmin) {
            $stmt = $pdo->prepare("
                SELECT gm.id_guru_mapel, CONCAT(m.nama_mapel, ' - ', g.nama) AS nama_mapel
                FROM jadwal_mengajar jm
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN guru g ON gm.id_guru = g.id_guru
                WHERE jm.id_kelas = ? AND gm.id_ta = ?
                GROUP BY gm.id_guru_mapel
                ORDER BY m.nama_mapel ASC
            ");
            $stmt->execute([$id_kelas, $id_ta]);
            $mapel_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Jika belum ada di jadwal_mengajar, ambil semua guru_mapel aktif
            if (empty($mapel_list)) {
                $stmtFallback = $pdo->prepare("
                    SELECT gm.id_guru_mapel, CONCAT(m.nama_mapel, ' - ', g.nama) AS nama_mapel
                    FROM guru_mapel gm
                    JOIN mapel m ON gm.id_mapel = m.id_mapel
                    JOIN guru g ON gm.id_guru = g.id_guru
                    WHERE gm.id_ta = ?
                    ORDER BY m.nama_mapel ASC
                ");
                $stmtFallback->execute([$id_ta]);
                $mapel_list = $stmtFallback->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $mapel_list = NilaiModel::getMapelDiajarByKelas($pdo, $id_guru, $id_kelas, $id_ta);

            // Fallback jika belum diatur di jadwal mengajar
            if (empty($mapel_list)) {
                $stmtFallback = $pdo->prepare("
                    SELECT gm.id_guru_mapel, m.nama_mapel
                    FROM guru_mapel gm
                    JOIN mapel m ON gm.id_mapel = m.id_mapel
                    WHERE gm.id_guru = ? AND gm.id_ta = ?
                    ORDER BY m.nama_mapel ASC
                ");
                $stmtFallback->execute([$id_guru, $id_ta]);
                $mapel_list = $stmtFallback->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        echo json_encode(['status' => 'ok', 'data' => $mapel_list]);
    }

    private static function getTpByMapel($pdo) {
        $id_guru_mapel = $_GET['id_guru_mapel'] ?? 0;
        $id_cp = $_GET['id_cp'] ?? 0;
        
        if (!$id_guru_mapel) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter id_guru_mapel tidak ada']);
            return;
        }

        if ($id_cp) {
            if (is_array($id_cp)) {
                $tp_list = CpTpModel::getAllTpByCps($pdo, $id_cp);
            } else if (strpos($id_cp, ',') !== false) {
                $ids = explode(',', $id_cp);
                $tp_list = CpTpModel::getAllTpByCps($pdo, $ids);
            } else {
                $tp_list = CpTpModel::getAllTpByCp($pdo, $id_cp);
            }
        } else {
            $stmtMapel = $pdo->prepare("SELECT id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
            $stmtMapel->execute([$id_guru_mapel]);
            $id_mapel_asli = $stmtMapel->fetchColumn();

            if (!$id_mapel_asli) {
                echo json_encode(['status' => 'error', 'msg' => 'Mapel tidak ditemukan']);
                return;
            }
            $tp_list = CpTpModel::getTpByMapel($pdo, $id_mapel_asli);
        }
        
        echo json_encode(['status' => 'ok', 'data' => $tp_list]);
    }

    private static function getCpByMapel($pdo) {
        $id_guru_mapel = $_GET['id_guru_mapel'] ?? 0;
        $id_kelas = $_GET['id_kelas'] ?? 0;
        
        if (!$id_guru_mapel) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter id_guru_mapel tidak ada']);
            return;
        }

        if ($id_kelas) {
            $stmt = $pdo->prepare("
                SELECT gm.id_mapel, k.tingkat 
                FROM guru_mapel gm
                JOIN kelas k ON k.id_kelas = ?
                WHERE gm.id_guru_mapel = ?
            ");
            $stmt->execute([$id_kelas, $id_guru_mapel]);
        } else {
             $stmt = $pdo->prepare("SELECT id_mapel FROM guru_mapel WHERE id_guru_mapel = ?");
             $stmt->execute([$id_guru_mapel]);
        }
        
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_mapel = $info['id_mapel'] ?? 0;
        $tingkat = $info['tingkat'] ?? '';
        $fase = ($tingkat == 'X') ? 'E' : (($tingkat == 'XI' || $tingkat == 'XII') ? 'F' : '');

        if (!$id_mapel) {
            echo json_encode(['status' => 'error', 'msg' => 'Mapel tidak ditemukan']);
            return;
        }

        if ($fase) {
            $cp_list = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel, $fase);
        } else {
            $stmtCp = $pdo->prepare("SELECT id_cp, deskripsi_cp FROM capaian_pembelajaran WHERE id_mapel = ?");
            $stmtCp->execute([$id_mapel]);
            $cp_list = $stmtCp->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode(['status' => 'ok', 'data' => $cp_list]);
    }
}
