<?php
// File: app/models/AppConfigModel.php

class AppConfigModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all configuration as associative array [key => value]
     */
    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT config_key, config_value FROM app_config");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = [];
        foreach ($results as $row) {
            $config[$row['config_key']] = $row['config_value'];
        }
        return $config;
    }

    /**
     * Get a single configuration value by key
     */
    public static function get($pdo, $key, $default = null) {
        $stmt = $pdo->prepare("SELECT config_value FROM app_config WHERE config_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['config_value'] : $default;
    }

    /**
     * Update configuration value
     */
    public static function update($pdo, $key, $value) {
        $stmt = $pdo->prepare("UPDATE app_config SET config_value = ? WHERE config_key = ?");
        return $stmt->execute([$value, $key]);
    }
    
    /**
     * Bulk update multiple settings
     */
    public static function updateBulk($pdo, $data) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO app_config (config_key, config_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)");
            foreach ($data as $key => $value) {
                $stmt->execute([$key, $value]);
            }
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Delete all theme-related configurations
     */
    public static function deleteThemeConfig($pdo) {
        $stmt = $pdo->prepare("DELETE FROM app_config WHERE config_key LIKE 'theme_%'");
        return $stmt->execute();
    }
}
?>
