<?php
class KeuanganApiController {
    public static function handle($pdo, $act) {
        switch ($act) {
            case 'students_by_class':
                self::getStudentsByClass($pdo);
                break;
            case 'active_kinds_by_class':
                self::getActiveKindsByClass($pdo);
                break;
            default:
                echo json_encode(['status' => 'error', 'msg' => 'Invalid finance action']);
                break;
        }
    }

    private static function getActiveKindsByClass($pdo) {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        if (!$id_kelas) {
            echo json_encode([]);
            return;
        }

        if (!class_exists('KeuanganJenisModel')) {
            require_once __DIR__ . '/../app/models/KeuanganJenisModel.php';
        }

        $jenisModel = new KeuanganJenisModel($pdo);
        $kinds = $jenisModel->getActiveKindsByClass($id_kelas);
        echo json_encode($kinds);
    }

    private static function getStudentsByClass($pdo) {
        $id_kelas = $_GET['id_kelas'] ?? 0;
        if (!$id_kelas) {
            echo json_encode([]);
            return;
        }

        // Require PenempatanModel if not already
        if (!class_exists('PenempatanModel')) {
            require_once __DIR__ . '/../app/models/PenempatanModel.php';
        }

        $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
        $students = PenempatanModel::getAssignedStudents($pdo, $id_kelas, $id_ta, true);
        echo json_encode($students);
    }
}
