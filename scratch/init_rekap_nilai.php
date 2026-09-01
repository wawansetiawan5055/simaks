<?php
require "config/db.php";
$pdo = connect_db();

try {
    // 1. Create table rekap_bobot_guru
    $sql_table = "CREATE TABLE IF NOT EXISTS rekap_bobot_guru (
        id_bobot INT AUTO_INCREMENT PRIMARY KEY,
        id_guru_mapel INT NOT NULL,
        id_kelas INT NOT NULL,
        bobot_sikap DECIMAL(5,2) DEFAULT 0.00,
        bobot_lms DECIMAL(5,2) DEFAULT 0.00,
        bobot_formatif DECIMAL(5,2) DEFAULT 0.00,
        bobot_sumatif_lm DECIMAL(5,2) DEFAULT 0.00,
        bobot_sts DECIMAL(5,2) DEFAULT 0.00,
        bobot_sas DECIMAL(5,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY(id_guru_mapel, id_kelas)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_table);
    echo "Table rekap_bobot_guru created/verified.\n";

    // 2. Insert defaults to app_config
    $defaults = [
        ['config_key' => 'default_bobot_sikap', 'config_value' => '0'],
        ['config_key' => 'default_bobot_lms', 'config_value' => '10'],
        ['config_key' => 'default_bobot_formatif', 'config_value' => '15'],
        ['config_key' => 'default_bobot_sumatif_lm', 'config_value' => '30'],
        ['config_key' => 'default_bobot_sts', 'config_value' => '20'],
        ['config_key' => 'default_bobot_sas', 'config_value' => '25']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO app_config (config_key, config_value) VALUES (:config_key, :config_value)");
    foreach ($defaults as $d) {
        $stmt->execute($d);
    }
    echo "Defaults inserted into app_config.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
