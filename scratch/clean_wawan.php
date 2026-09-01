<?php
require_once __DIR__ . '/../config/db.php';
$pdo = connect_db();

$stmt = $pdo->prepare("DELETE ag FROM absensi_guru ag JOIN guru g ON ag.id_guru = g.id_guru WHERE g.nama LIKE ? AND ag.tanggal = ?");
$stmt->execute(['%Wawan Setiawan%', '2026-08-31']);
echo "Deleted rows: " . $stmt->rowCount() . "\n";
