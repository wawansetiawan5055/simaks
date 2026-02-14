<?php
require_once __DIR__ . '/../models/CatatanKelasModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php'; // Kita pakai ulang model Jurnal KBM
require_once __DIR__ . '/../models/KelasModel.php';     // Kita pakai ulang model Kelas

function catatan_kelas_index($pdo)
{
    if (!has_role(['Admin', 'Guru']))
        redirect('index.php');

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Error: Tidak ada Tahun Ajaran aktif.");

    $kelas_diajar = [];
    if (has_role('Admin')) {
        $kelas_diajar = KelasModel::all($pdo, $id_ta_aktif);
    } elseif (has_role('Guru')) {
        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_login > 0) {
            $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru_login, $id_ta_aktif);
        }
    }

    // ⭐ INI ADALAH BAGIAN PENTING YANG MENGAMBIL DATA
    $riwayat_catatan = CatatanKelasModel::getAllByTA($pdo, $id_ta_aktif);

    // Kirim $riwayat_catatan ke view
    extract(compact('kelas_diajar', 'riwayat_catatan'));

    include __DIR__ . '/../views/catatan_kelas_index.php';
}

function catatan_kelas_save($pdo)
{
    if (!has_role(['Admin', 'Guru']))
        redirect('index.php');

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif || empty($_POST['jam_mengajar'])) {
        die("Gagal menyimpan: Informasi TA tidak valid atau tidak ada jam mengajar yang dipilih.");
    }

    $data = [
        'id_ta' => $id_ta_aktif,
        'tanggal' => $_POST['tanggal'],
        'jam_mengajar' => $_POST['jam_mengajar'], // Ini adalah array ID
        'catatan_kejadian' => $_POST['catatan_kejadian']
    ];

    CatatanKelasModel::save($pdo, $data);

    $_SESSION['pesan_sukses'] = "Catatan kejadian kelas berhasil disimpan!";
    redirect('index.php?mod=catatan_kelas');
}
?>