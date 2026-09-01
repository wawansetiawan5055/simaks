<?php
$host = 'localhost';
$user = 'administrator';
$pass = '20247166';
$db = 'db_simaks_cbt';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SHOW COLUMNS FROM cbt_kelas");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "COLUMNS IN CBT 'cbt_kelas':\n";
    foreach ($cols as $c) {
        echo "- " . $c['Field'] . "\n";
    }
} catch (PDOException $e) {
    echo "CBT DB FAILED: " . $e->getMessage();
}
