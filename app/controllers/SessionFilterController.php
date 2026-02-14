<?php
// ⭐ REVISI 1: Pastikan Sesi dimulai di file ini
// Jika session_start() tidak ada di sini, $_SESSION tidak akan tersimpan.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi ini harus dipanggil dari index.php, yang sudah memuat $pdo.
function set_session_ta($pdo) {
    
    // Pastikan helper.php (jika diperlukan) sudah di-load oleh index.php
    // Kita cek login untuk keamanan
    if (!function_exists('is_logged_in') || !is_logged_in()) {
        // Jika fungsi helper tidak ada, kita redirect manual
        header("Location: index.php?mod=auth&act=login");
        exit;
    }

    $id_ta_baru = $_POST['id_ta_filter'] ?? 0;
    
    if ($id_ta_baru) {
        // Ambil detail TA dari database
        $stmt = $pdo->prepare("SELECT * FROM tahun_ajaran WHERE id_ta = ?");
        $stmt->execute([$id_ta_baru]);
        $ta_baru = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ta_baru) {
            // Simpan ID dan Nama TA yang ingin DILIHAT ke session
            $_SESSION['id_ta_viewing'] = $ta_baru['id_ta'];
            $_SESSION['nama_ta_viewing'] = $ta_baru['nama_ta'];
        }
    }

    // ⭐ REVISI 2: Paksa PHP untuk menyimpan data Sesi SEKARANG
    session_write_close();

    // Arahkan kembali ke halaman asal
    $previous_page = $_SERVER['HTTP_REFERER'] ?? 'index.php?mod=dashboard';
    
    // ⭐ REVISI 3: Gunakan redirect PHP bawaan, bukan helper
    header("Location: " . $previous_page);
    exit;
}