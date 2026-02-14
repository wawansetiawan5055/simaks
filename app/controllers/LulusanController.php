<?php
require_once __DIR__ . '/../models/LulusanModel.php';

// Halaman 1: Proses Kelulusan (Daftar Kelas XII)
function lulusan_proses($pdo) {
    // Hanya Admin dan TU yang boleh akses
    if (!check_access('lulusan', 'proses')) redirect('index.php');

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    
    // Ambil data calon lulusan (Kelas XII)
    $calon_lulusan = LulusanModel::getCalonLulusan($pdo, $id_ta_aktif);
    
    // Load View
    include __DIR__ . '/../views/lulusan_proses.php';
}

// Aksi: Simpan Proses Lulus
function lulusan_save($pdo) {
    if (!check_access('lulusan', 'save')) redirect('index.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids_siswa = $_POST['pilih_siswa'] ?? [];

        if (!empty($ids_siswa)) {
            try {
                // Determine Graduation Year from Active Academic Year Session
                // Format example: "2024/2025 Ganjil" or "2024/2025"
                $nama_ta = $_SESSION['nama_ta_aktif'] ?? '';
                $tahun_lulus = date('Y'); // Default fallback

                if (preg_match('/(\d{4})[\/\-](\d{4})/', $nama_ta, $matches)) {
                    // Logic: Academic Year 2024/2025 -> Graduation is usually in 2025 (the second year)
                    $tahun_lulus = (int)$matches[2];
                }

                LulusanModel::luluskanSiswa($pdo, $ids_siswa, $tahun_lulus);
                $_SESSION['pesan_sukses'] = count($ids_siswa) . " Siswa berhasil diluluskan di Tahun $tahun_lulus!";
            } catch (Exception $e) {
                $_SESSION['pesan_error'] = "Terjadi kesalahan: " . $e->getMessage();
            }
        } else {
            $_SESSION['pesan_error'] = "Tidak ada siswa yang dipilih.";
        }
    }
    redirect('index.php?mod=lulusan&act=proses');
}

// Halaman 2: Data Alumni (Daftar Siswa Lulus)
function lulusan_index($pdo) { // act=index mapping ke Data Alumni sesuai sidebar
    if (!check_access('lulusan', 'index')) redirect('index.php');

    require_once __DIR__ . '/../models/TracerStudyModel.php';

    // 1. Ambil data alumni (detail list)
    $alumni_list = LulusanModel::getAlumni($pdo);

    // 1. Ambil data alumni (detail list)
    $alumni_list = LulusanModel::getAlumni($pdo);

    include __DIR__ . '/../views/lulusan_alumni.php';
}

// Aksi: Simpan Data Tracer Study
function lulusan_update_tracer($pdo) {
    if (!check_access('lulusan', 'update')) redirect('index.php');

    require_once __DIR__ . '/../models/TracerStudyModel.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'id_tracer' => $_POST['id_tracer'] ?? '',
            'id_siswa' => $_POST['id_siswa'],
            'tahun_lulus' => $_POST['tahun_lulus'],
            'status_setelah_lulus' => $_POST['status_setelah_lulus'],
            'nama_institusi' => $_POST['nama_institusi'] ?? '',
            'jurusan_pekerjaan' => $_POST['jurusan_pekerjaan'] ?? '',
            'kota' => $_POST['kota'] ?? '',
            'keterangan' => $_POST['keterangan'] ?? ''
        ];

        try {
            if (TracerStudyModel::save($pdo, $data)) {
                $_SESSION['pesan_sukses'] = "Data tracer study berhasil disimpan.";
            } else {
                $_SESSION['pesan_error'] = "Gagal menyimpan data.";
            }
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
        }
    }
    
    // Redirect back
    redirect('index.php?mod=lulusan&act=index');
}

// Aksi: Batalkan Lulus (Jika salah klik)
function lulusan_batal($pdo) {
    if (!check_access('lulusan', 'delete')) redirect('index.php'); // Batal is effectively delete/undo
    
    $id = $_GET['id'] ?? 0;
    if ($id) {
        LulusanModel::batalkanKelulusan($pdo, $id);
        $_SESSION['pesan_sukses'] = "Status kelulusan siswa dibatalkan (Kembali Aktif).";
    }
    redirect('index.php?mod=lulusan&act=index');
}
?>