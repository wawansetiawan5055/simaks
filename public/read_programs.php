<?php
require_once __DIR__ . '/../config/db.php';
$pdo = connect_db();

$stmt = $pdo->query("SELECT * FROM landing_programs");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($rows);
