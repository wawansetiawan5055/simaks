<?php
// File: app/models/KomponenSikapModel.php

class KomponenSikapModel {
    public static function getAll($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM komponen_sikap ORDER BY kategori ASC, id_komponen ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id_komponen) {
        $stmt = $pdo->prepare("SELECT * FROM komponen_sikap WHERE id_komponen = ?");
        $stmt->execute([$id_komponen]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $data) {
        $stmt = $pdo->prepare("INSERT INTO komponen_sikap (kategori, nama_komponen, deskripsi) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['kategori'],
            $data['nama_komponen'],
            $data['deskripsi']
        ]);
    }

    public static function update($pdo, $data) {
        $stmt = $pdo->prepare("UPDATE komponen_sikap SET kategori = ?, nama_komponen = ?, deskripsi = ? WHERE id_komponen = ?");
        return $stmt->execute([
            $data['kategori'],
            $data['nama_komponen'],
            $data['deskripsi'],
            $data['id_komponen']
        ]);
    }

    public static function delete($pdo, $id_komponen) {
        $stmt = $pdo->prepare("DELETE FROM komponen_sikap WHERE id_komponen = ?");
        return $stmt->execute([$id_komponen]);
    }
}
