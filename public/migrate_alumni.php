<?php
require_once '../config/db.php';

$pdo = connect_db();

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check columns
    $stmt = $pdo->query("SHOW COLUMNS FROM siswa_alumni LIKE 'id_kelas_akhir'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE siswa_alumni ADD COLUMN id_kelas_akhir INT(11) NULL DEFAULT 0 AFTER tahun_lulus");
        echo "Column id_kelas_akhir added.<br>";
    } else {
        echo "Column id_kelas_akhir exists.<br>";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM siswa_alumni LIKE 'id_ta_lulus'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE siswa_alumni ADD COLUMN id_ta_lulus INT(11) NULL DEFAULT 0 AFTER id_kelas_akhir");
        echo "Column id_ta_lulus added.<br>";
    } else {
        echo "Column id_ta_lulus exists.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>