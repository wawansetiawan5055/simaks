<?php require "app/config/database.php"; $stmt = $pdo->query("SHOW COLUMNS FROM lms_pengumpulan"); print_r($stmt->fetchAll(PDO::FETCH_ASSOC)); ?>
