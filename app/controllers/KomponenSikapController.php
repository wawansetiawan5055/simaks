<?php
// File: app/controllers/KomponenSikapController.php

require_once __DIR__.'/../models/KomponenSikapModel.php';

class KomponenSikapController {
    public static function index() {
        require_access('komponen_sikap');
        global $pdo;
        
        $komponen_list = KomponenSikapModel::getAll($pdo);
        
        // Cek aksi dari modal form
        $action = $_POST['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'create') {
                if (!can_do($pdo, 'komponen_sikap', 'create')) {
                    $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menambah data.";
                    header("Location: index.php?mod=komponen_sikap");
                    exit;
                }
                $data = [
                    'kategori' => $_POST['kategori'] ?? 'Sikap',
                    'nama_komponen' => $_POST['nama_komponen'] ?? '',
                    'deskripsi' => $_POST['deskripsi'] ?? ''
                ];
                if (KomponenSikapModel::create($pdo, $data)) {
                    $_SESSION['pesan_sukses'] = "Berhasil menambahkan komponen.";
                } else {
                    $_SESSION['pesan_error'] = "Gagal menambahkan komponen.";
                }
                header("Location: index.php?mod=komponen_sikap");
                exit;
            } elseif ($action === 'update') {
                if (!can_do($pdo, 'komponen_sikap', 'update')) {
                    $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengubah data.";
                    header("Location: index.php?mod=komponen_sikap");
                    exit;
                }
                $data = [
                    'id_komponen' => $_POST['id_komponen'] ?? 0,
                    'kategori' => $_POST['kategori'] ?? 'Sikap',
                    'nama_komponen' => $_POST['nama_komponen'] ?? '',
                    'deskripsi' => $_POST['deskripsi'] ?? ''
                ];
                if (KomponenSikapModel::update($pdo, $data)) {
                    $_SESSION['pesan_sukses'] = "Berhasil mengupdate komponen.";
                } else {
                    $_SESSION['pesan_error'] = "Gagal mengupdate komponen.";
                }
                header("Location: index.php?mod=komponen_sikap");
                exit;
            } elseif ($action === 'delete') {
                if (!can_do($pdo, 'komponen_sikap', 'delete')) {
                    $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk menghapus data.";
                    header("Location: index.php?mod=komponen_sikap");
                    exit;
                }
                $id = intval($_POST['id_komponen'] ?? 0);
                if (KomponenSikapModel::delete($pdo, $id)) {
                    $_SESSION['pesan_sukses'] = "Berhasil menghapus komponen.";
                } else {
                    $_SESSION['pesan_error'] = "Gagal menghapus komponen.";
                }
                header("Location: index.php?mod=komponen_sikap");
                exit;
            }
        }
        
        require __DIR__.'/../views/komponen_sikap_index.php';
    }
}
