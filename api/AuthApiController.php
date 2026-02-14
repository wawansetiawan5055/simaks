<?php
require_once '../app/models/PenggunaModel.php';

class AuthApiController {
    public static function handle($pdo, $act) {
        if ($act == 'login') {
            $input = json_decode(file_get_contents('php://input'), true);
            $user = PenggunaModel::findByUsername($pdo, $input['username']);
            if ($user && password_verify($input['password'], $user['password'])) {
                // Dummy JWT, tambahkan implementasi JWT jika ingin
                echo json_encode(['status'=>'ok','token'=>'dummy-jwt-token','user'=>$user]);
            } else {
                echo json_encode(['status'=>'fail','msg'=>'Invalid credentials']);
            }
        }
    }
}