<?php
// Pastikan path ke model benar
require_once __DIR__ . '/../models/MutasiMasukModel.php';
require_once __DIR__ . '/../models/ProfilSekolahModel.php'; // Untuk KOP
require_once __DIR__ . '/LaporanController.php'; // Untuk fungsi helper export

require_once __DIR__ . '/../models/KelasModel.php';

function mutasi_masuk_form($pdo) {
    // [REFACTOR] Redirect to index as the form is now a modal there
    redirect('index.php?mod=mutasi_masuk&act=index');
}

function mutasi_masuk_save($pdo) {
    // FIX: Pastikan TA aktif terdeteksi dengan benar
    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? $_SESSION['id_ta_viewing'] ?? 0;
    
    if (!$id_ta_aktif) {
        // Fallback: ambil dari database
        $stmt = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1");
        $ta_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_ta_aktif = $ta_row['id_ta'] ?? 1;
    }
    
    // Ambil data dari POST
    $data = [
        'id_ta' => $id_ta_aktif, // FIX: Gunakan TA aktif yang sudah di-validasi
        'nama_lengkap' => $_POST['nama_lengkap'] ?? null,
        'nisn' => $_POST['nisn'] ?? null,
        'nik' => $_POST['nik'] ?? null,
        'jk' => $_POST['jk'] ?? null,
        'tempat_lahir' => $_POST['tempat_lahir'] ?? null,
        'tanggal_lahir' => $_POST['tanggal_lahir'] ?? null,
        'sekolah_asal' => $_POST['sekolah_asal'] ?? null,
        'tingkat_sebelumnya' => $_POST['tingkat_sebelumnya'] ?? null,
        'pindah_ke_tingkat' => $_POST['pindah_ke_tingkat'] ?? null,
        'tanggal_mutasi' => $_POST['tanggal_mutasi'] ?? null,
        'alasan_mutasi' => $_POST['alasan_mutasi'] ?? null,
        'id_kelas_tujuan' => $_POST['id_kelas_tujuan'] ?? null, // NEW: Kelas tujuan
    ];
    
    try {
        $result = MutasiMasukModel::save($pdo, $data);
        if ($result) {
            $_SESSION['pesan_sukses'] = "Data mutasi masuk " . htmlspecialchars($data['nama_lengkap']) . " berhasil disimpan ke TA " . $id_ta_aktif . ".";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan data.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Terjadi error: " . $e->getMessage();
    }
    
    // Kembali ke halaman index (Daftar Mutasi)
    redirect('index.php?mod=mutasi_masuk&act=index');
}

/**
 * 🛠️ FUNGSI BARU (SEHARUSNYA DI LUAR FUNGSI SAVE)
 */
function mutasi_masuk_index($pdo) {
    // 1. Ambil TA Aktif
    $id_ta = $_SESSION['id_ta_aktif'] ?? $_SESSION['id_ta_viewing'] ?? 0;
    
    // 2. Load daftar kelas untuk dropdown modal
    $daftar_kelas = KelasModel::all($pdo, $id_ta);
    
    // 3. Ambil data list mutasi
    $list_mutasi = MutasiMasukModel::all($pdo);
    
    extract(compact('list_mutasi', 'daftar_kelas'));
    include __DIR__ . '/../views/mutasi_masuk_index.php';
}

/**
 * 🛠️ FUNGSI BARU (SEHARUSNYA DI LUAR FUNGSI SAVE)
 * Logika Export Excel
 */
function mutasi_masuk_export_excel($pdo) {
    $data = MutasiMasukModel::all($pdo);
    $judul = "Laporan Data Mutasi Masuk";
    $kolom = ['No', 'Tgl Pengajuan', 'Nama Siswa', 'NISN', 'JK', 'Sekolah Asal', 'Pindah Ke Tingkat', 'Status'];
    $rows = []; $no=1;
    foreach($data as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_pengajuan'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['jk'],
            $d['sekolah_asal'],
            $d['pindah_ke_tingkat'],
            $d['status_penerimaan']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_excel_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_masuk");
}

/**
 * 🛠️ FUNGSI BARU (SEHARUSNYA DI LUAR FUNGSI SAVE)
 * Logika Export PDF
 */
function mutasi_masuk_export_pdf($pdo) {
    $data = MutasiMasukModel::all($pdo);
    $judul = "Laporan Data Mutasi Masuk";
    $kolom = ['No', 'Tgl Pengajuan', 'Nama Siswa', 'NISN', 'JK', 'Sekolah Asal', 'Pindah Ke Tingkat', 'Status'];
    $rows = []; $no=1;
    foreach($data as $d) {
        $rows[] = [
            $no++,
            $d['tanggal_pengajuan'],
            $d['nama_lengkap'],
            $d['nisn'],
            $d['jk'],
            $d['sekolah_asal'],
            $d['pindah_ke_tingkat'],
            $d['status_penerimaan']
        ];
    }
    $kop = get_kop_laporan($pdo);
    laporan_export_pdf_render(['judul' => $judul, 'kolom' => $kolom, 'rows' => $rows, 'kop_nama' => $kop['kop_nama'], 'kop_alamat' => $kop['kop_alamat'], 'kop_npsn' => $kop['kop_npsn']], "laporan_mutasi_masuk");
}

/**
 * BARU: Menampilkan halaman detail
 */
function mutasi_masuk_detail($pdo) {
    $id_mutasi = $_GET['id'] ?? 0;
    $data_mutasi = MutasiMasukModel::getById($pdo, $id_mutasi);
    
    if (!$data_mutasi) {
        $_SESSION['pesan_error'] = "Data tidak ditemukan.";
        redirect('index.php?mod=mutasi_masuk&act=index');
    }
    
    extract(compact('data_mutasi'));
    // Kita akan buat file view ini di Langkah 5
    include __DIR__ . '/../views/mutasi_masuk_detail.php';
}

/**
 * BARU: Memproses promosi siswa
 */
function mutasi_masuk_promote($pdo) {
    $id_mutasi = $_GET['id'] ?? 0;
    
    try {
        $result = MutasiMasukModel::promoteToMaster($pdo, $id_mutasi);
        if ($result) {
            $_SESSION['pesan_sukses'] = "Siswa berhasil diterima dan dipromosikan ke Data Master Siswa Aktif.";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal mempromosikan siswa: " . $e->getMessage();
    }
    
    redirect('index.php?mod=mutasi_masuk&act=index');
}
?>