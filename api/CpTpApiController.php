<?php

require_once __DIR__ . '/../app/models/CpTpModel.php';

class CpTpApiController
{
    public static function handle($pdo, $act)
    {
        if ($act == 'get_tp_by_mapel_tingkat') {
            self::getTpByMapelAndTingkat($pdo);
        } elseif ($act == 'get_cp_by_mapel_tingkat') {
            self::getCpByMapelAndTingkat($pdo);
        } elseif ($act == 'get_tp_by_cp') {
            self::getTpByCp($pdo);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
        }
    }

    private static function getCpByMapelAndTingkat($pdo)
    {
        $id_mapel = $_GET['id_mapel'] ?? 0;
        $tingkat = $_GET['tingkat'] ?? 0;

        if (!$id_mapel || !$tingkat) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter id_mapel dan tingkat wajib diisi']);
            return;
        }

        $fase = 'F';
        if ($tingkat == 'X' || $tingkat == 10) {
            $fase = 'E';
        }

        ob_clean();
        header('Content-Type: application/json');

        try {
            $cps = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel, $fase);
            echo json_encode(['status' => 'ok', 'data' => $cps]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    private static function getTpByCp($pdo)
    {
        $id_cp = $_GET['id_cp'] ?? 0;
        $id_mapel = $_GET['id_mapel'] ?? 0;

        if (!$id_cp || !$id_mapel) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter id_cp dan id_mapel wajib diisi']);
            return;
        }

        ob_clean();
        header('Content-Type: application/json');

        try {
            $tps = CpTpModel::getAllTpByCp($pdo, $id_cp);
            echo json_encode(['status' => 'ok', 'data' => $tps]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    private static function getTpByMapelAndTingkat($pdo)
    {
        $id_mapel = $_GET['id_mapel'] ?? 0;
        $tingkat = $_GET['tingkat'] ?? 0;

        if (!$id_mapel || !$tingkat) {
            echo json_encode(['status' => 'error', 'msg' => 'Parameter id_mapel dan tingkat wajib diisi']);
            return;
        }

        // Mapping Tingkat ke Fase
        // Tingkat is ENUM('X','XI','XII') or sometimes int? 
        // Handle both cases

        $fase = 'F'; // Default (Kelas 11, 12 / XI, XII)

        if ($tingkat == 'X' || $tingkat == 10) {
            $fase = 'E';
        }

        // Prevent extra output breaking JSON
        ob_clean();
        header('Content-Type: application/json');

        try {
            // 1. Ambil CP berdasarkan Mapel dan Fase
            $cps = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel, $fase);

            $all_tps = [];

            // 2. Loop setiap CP untuk mengambil TP-nya
            foreach ($cps as $cp) {
                $tps = CpTpModel::getAllTpByCp($pdo, $cp['id_cp']);
                if (!empty($tps)) {
                    foreach ($tps as $tp) {
                        $all_tps[] = [
                            'id_tp' => $tp['id_tp'],
                            'kode_tp' => $tp['kode_tp'],
                            'deskripsi_tp' => $tp['deskripsi_tp'],
                            'id_cp' => $cp['id_cp'],
                            'deskripsi_cp' => $cp['deskripsi_cp'] // Optional: Include CP context
                        ];
                    }
                }
            }

            echo json_encode(['status' => 'ok', 'data' => $all_tps]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
