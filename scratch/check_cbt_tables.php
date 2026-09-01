<?php
require_once __DIR__ . '/../config/db.php';
$pdo = connect_db();
foreach (['cbt_jadwal', 'cbt_paket'] as $t) {
    echo "\n--- Structure of $t ---\n";
    $cols = $pdo->query("DESCRIBE $t")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['Field']} | {$c['Type']} | Null: {$c['Null']} | Key: {$c['Key']} | Default: {$c['Default']}\n";
    }
}
