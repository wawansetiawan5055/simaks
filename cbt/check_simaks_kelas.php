<?php
require_once __DIR__ . '/config/bridge.php';
$pdo = cbt_bridge_connect();
if ($pdo) {
    $stmt = $pdo->query("SHOW COLUMNS FROM kelas");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "COLUMNS IN SIMAKS 'kelas':\n";
    foreach ($cols as $c) {
        echo "- " . $c['Field'] . "\n";
    }
} else {
    echo "FAILED TO CONNECT TO SIMAKS";
}
