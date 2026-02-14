<?php
require_once __DIR__ . '/../models/ProfilModel.php';

function profil_index($pdo) {
    if (!is_logged_in()) redirect('index.php');

    $user = ProfilModel::getProfil($pdo, $_SESSION['user_id']);
    include __DIR__ . '/../views/profil_index.php';
}

function profil_save($pdo) {
    if (!is_logged_in()) redirect('index.php');

    try {
        ProfilModel::updateProfil($pdo, $_SESSION['user_id'], $_POST, $_FILES['foto'] ?? null);
        
        // Update Session Names if changed
        $_SESSION['nama_pengguna'] = $_POST['nama_pengguna'];
        
        $_SESSION['pesan_sukses'] = "Profil berhasil diperbarui.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal memperbarui profil: " . $e->getMessage();
    }
    
    redirect('index.php?mod=profil');
}
