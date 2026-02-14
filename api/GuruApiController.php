<?php
require_once '../app/models/GuruModel.php';

class GuruApiController {
    public static function handle($pdo, $act) {
        switch ($act) {
            case 'list':
                $data = GuruModel::all($pdo);
                echo json_encode($data);
                break;
            case 'detail':
                $id = $_GET['id'] ?? 0;
                $data = GuruModel::find($pdo, $id);
                echo json_encode($data);
                break;
            case 'create':
                $input = json_decode(file_get_contents('php://input'), true);
                GuruModel::save($pdo, $input);
                echo json_encode(['status'=>'ok']);
                break;
            case 'update':
                $id = $_GET['id'] ?? 0;
                $input = json_decode(file_get_contents('php://input'), true);
                $input['id_guru'] = $id;
                GuruModel::save($pdo, $input);
                echo json_encode(['status'=>'ok']);
                break;
            case 'delete':
                $id = $_GET['id'] ?? 0;
                GuruModel::delete($pdo, $id);
                echo json_encode(['status'=>'ok']);
                break;
            default:
                echo json_encode(['status'=>'error','msg'=>'Invalid action']);
        }
    }
}