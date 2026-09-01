<?php
require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . '/../models/MutasiSiswaModel.php';
require_once __DIR__ . '/../models/KelasModel.php';

function mutasi_siswa_index($pdo)
{
    if (!check_access('mutasi_siswa'))
        redirect('index.php');

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Error: Tahun Ajaran aktif tidak ditemukan.");

    // [BARU] Filter TA untuk History
    require_once __DIR__ . '/../models/TahunAjaranModel.php';
    $ta_list = TahunAjaranModel::all($pdo);
    $id_ta_filter = $_GET['id_ta'] ?? $_SESSION['id_ta_viewing'] ?? $id_ta_aktif;

    // Ambil data untuk formulir (Tetap pakai TA Aktif)
    $kelas_list = KelasModel::all($pdo, $id_ta_aktif);

    // Ambil data untuk tabel riwayat (Pakai TA Filter)
    $riwayat_mutasi = MutasiSiswaModel::getRiwayatMutasi($pdo, $id_ta_filter);

    extract(compact('kelas_list', 'riwayat_mutasi', 'ta_list', 'id_ta_filter'));
    include __DIR__ . '/../views/mutasi_siswa_index.php';
}

function mutasi_siswa_save($pdo)
{
    if (!check_access('mutasi_siswa', 'save'))
        redirect('index.php');

    // FIX: Pastikan TA aktif terdeteksi dengan benar
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? $_SESSION['id_ta_viewing'] ?? 0;

    if (!$id_ta_aktif) {
        // Fallback: ambil dari database
        $stmt = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1");
        $ta_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_ta_aktif = $ta_row['id_ta'] ?? 1;
    }

    $data_to_save = [
        'id_siswa' => $_POST['id_siswa'],
        'id_ta_mutasi' => $id_ta_aktif, // FIX: Column name is id_ta_mutasi, not id_ta
        'tanggal_mutasi' => $_POST['tanggal_mutasi'],
        'jenis_mutasi' => $_POST['jenis_mutasi'],
        'alasan' => $_POST['alasan'],
        'id_kelas_asal' => $_POST['id_kelas_asal'] ?? null, // NEW: Kelas asal
        'id_pengguna' => $_SESSION['user_id']
    ];

    try {
        if (!empty($_POST['is_edit']) && $_POST['is_edit'] == '1') {
            MutasiSiswaModel::update($pdo, $data_to_save);
            $_SESSION['pesan_sukses'] = "Data mutasi siswa berhasil diperbarui.";
        } else {
            MutasiSiswaModel::saveMutasiKeluar($pdo, $data_to_save);
            $_SESSION['pesan_sukses'] = "Data mutasi siswa berhasil disimpan ke TA " . $id_ta_aktif . ".";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan data mutasi: " . $e->getMessage();
    }

    redirect('index.php?mod=mutasi_siswa');
}

// Endpoint API untuk mengambil siswa per kelas
function mutasi_siswa_get_siswa_api($pdo)
{
    header('Content-Type: application/json');
    if (ob_get_length() > 0) ob_clean();
    if (!is_logged_in()) {
        echo json_encode([]);
        exit;
    }

    $id_kelas = (int)($_GET['id_kelas'] ?? 0);
    $id_ta = (int)($_SESSION['id_ta_aktif'] ?? $_SESSION['id_ta_viewing'] ?? 0);

    $siswa = MutasiSiswaModel::getSiswaAktifByKelas($pdo, $id_kelas, $id_ta);
    echo json_encode($siswa ?: []);
    exit;
}

function mutasi_siswa_get_mutation_api($pdo)
{
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    $id_siswa = $_GET['id_siswa'] ?? 0;
    $mutasi = MutasiSiswaModel::find($pdo, $id_siswa);

    if ($mutasi) {
        echo json_encode(['status' => 'success', 'data' => $mutasi]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
    exit;
}

function mutasi_siswa_batal($pdo)
{
    if (!check_access('mutasi_siswa', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=mutasi_siswa');
    }

    $id_siswa = $_GET['id_siswa'] ?? 0;
    try {
        MutasiSiswaModel::restoreMutasi($pdo, $id_siswa);
        $_SESSION['pesan_sukses'] = "Mutasi siswa berhasil dibatalkan. Siswa telah dikembalikan ke daftar aktif dan kelas asalnya.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal membatalkan mutasi: " . $e->getMessage();
    }

    redirect('index.php?mod=mutasi_siswa');
}