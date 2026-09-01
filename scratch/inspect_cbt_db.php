<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/db.php';

$pdo = connect_db();

echo "=== CBT TABLES ===\n";
$tables = $pdo->query("SHOW TABLES LIKE 'cbt_%'")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

foreach ($tables as $table) {
    echo "\n=== SCHEMA FOR $table ===\n";
    $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "  - {$col['Field']} ({$col['Type']}) Null:{$col['Null']} Key:{$col['Key']} Default:{$col['Default']}\n";
    }
}
