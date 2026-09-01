<?php
// File: app/models/AIGeneratorModel.php

class AIGeneratorModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Save AI generated document log
     */
    public static function saveLog($pdo, $data) {
        $sql = "INSERT INTO ai_perangkat_logs (id_guru, id_ta, jenis_perangkat, judul, konten_html, input_metadata) 
                VALUES (:id_guru, :id_ta, :jenis_perangkat, :judul, :konten_html, :input_metadata)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id_guru' => $data['id_guru'],
            ':id_ta' => $data['id_ta'],
            ':jenis_perangkat' => $data['jenis_perangkat'],
            ':judul' => $data['judul'],
            ':konten_html' => $data['konten_html'],
            ':input_metadata' => json_encode($data['input_metadata'])
        ]);
    }

    /**
     * Get logs for a specific teacher
     */
    public static function getLogsByGuru($pdo, $id_guru) {
        $sql = "SELECT l.*, ta.nama_ta 
                FROM ai_perangkat_logs l
                LEFT JOIN tahun_ajaran ta ON l.id_ta = ta.id_ta
                WHERE l.id_guru = :id_guru 
                ORDER BY l.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_guru' => $id_guru]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single log by ID
     */
    public static function getLogById($pdo, $id_log) {
        $sql = "SELECT * FROM ai_perangkat_logs WHERE id_log = :id_log";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_log' => $id_log]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a log
     */
    public static function deleteLog($pdo, $id_log, $id_guru) {
        $sql = "DELETE FROM ai_perangkat_logs WHERE id_log = :id_log AND id_guru = :id_guru";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id_log' => $id_log, ':id_guru' => $id_guru]);
    }

    /**
     * Update a log
     */
    public static function updateLog($pdo, $id_log, $id_guru, $judul, $konten_html) {
        $sql = "UPDATE ai_perangkat_logs 
                SET judul = :judul, konten_html = :konten_html 
                WHERE id_log = :id_log AND id_guru = :id_guru";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id_log' => $id_log,
            ':id_guru' => $id_guru,
            ':judul' => $judul,
            ':konten_html' => $konten_html
        ]);
    }
}
?>
