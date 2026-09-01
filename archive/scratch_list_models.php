<?php
// Scratch script to debug available models
require_once __DIR__ . '/app/models/AppConfigModel.php';
require_once __DIR__ . '/config/database.php'; // I'll check the DB connection file

$api_key = AppConfigModel::get($pdo, 'gemini_api_key');
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $api_key;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
