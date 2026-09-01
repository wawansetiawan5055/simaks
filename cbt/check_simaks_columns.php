<?php
require_once __DIR__ . '/config/bridge.php';
$pdo = cbt_bridge_connect();
if ($pdo) {
    $stmt = $pdo->query("DESCRIBE siswa");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($columns, JSON_PRETTY_PRINT);
} else {
    echo "Gagal koneksi ke SIMAKS";
}
