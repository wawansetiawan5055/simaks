<?php
// File: /www/wwwroot/simaks.app/public/scratch_list_models.php
require_once '../config/env.php';
require_once '../config/db.php';
require_once '../app/models/AppConfigModel.php';

$pdo = connect_db();
$api_key = AppConfigModel::get($pdo, 'gemini_api_key');

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $api_key;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;
?>
