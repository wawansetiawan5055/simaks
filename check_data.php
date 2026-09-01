<?php
require_once 'config/db.php';
$stmt = $pdo->query("SELECT * FROM lms_tugas ORDER BY id_tugas DESC LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $pdo->query("SELECT * FROM kelas LIMIT 20");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
