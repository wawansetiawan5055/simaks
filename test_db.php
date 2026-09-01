<?php
require 'config/db.php';
$stmt = $pdo->query("SHOW TABLES LIKE '%penugasan%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
