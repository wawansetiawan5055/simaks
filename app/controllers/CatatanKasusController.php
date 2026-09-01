<?php
require_once __DIR__ . '/../models/CatatanKasusModel.php';
require_once __DIR__ . '/../models/KelasModel.php';

function catatan_kasus_index($pdo)
{
    if (!check_access('catatan_kasus'))
        redirect('index.php');

    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta)
        die("Error: Tahun Ajaran aktif tidak ditemukan.");

    // Ambil hanya kelas yang memiliki siswa aktif pada TA aktif
    $stmt_kelas = $pdo->prepare("
        SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat
        FROM kelas k
        JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas
        JOIN siswa s ON ps.id_siswa = s.id_siswa
        WHERE ps.id_ta = ? AND s.status_aktif = 'Aktif'
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_kelas->execute([$id_ta]);
    $kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

    // Panggil model untuk mengambil data kasus dengan filter TA aktif
    $kasus_list = CatatanKasusModel::getAllKasus($pdo, $id_ta);

    $siswa_list = [];

    extract(compact('kelas_list', 'kasus_list', 'siswa_list'));
    include __DIR__ . '/../views/catatan_kasus_index.php';
}

function catatan_kasus_save($pdo)
{
    if (!check_access('catatan_kasus', 'save'))
        redirect('index.php');

    $id_ta_aktif = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru_piket = $_SESSION['id_guru_terkait'] ?? 0;
    if (!$id_guru_piket) {
        $id_guru_piket = (int)$pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn() ?: 1;
    }
    if (!$id_guru_piket || !$id_ta_aktif) {
        die("Gagal menyimpan: Informasi guru piket atau TA tidak valid.");
    }

    // Ambil id_kelas dari siswa yang dipilih
    $stmt = $pdo->prepare("SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND id_ta = ? LIMIT 1");
    $stmt->execute([$_POST['id_siswa'], $id_ta_aktif]);
    $id_kelas = $stmt->fetchColumn();

    if (!$id_kelas) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: Siswa tidak ditemukan dalam penempatan kelas di TA aktif.";
        redirect('index.php?mod=catatan_kasus');
        return; // Hentikan eksekusi
    }

    $data_to_save = [
        'id_siswa' => $_POST['id_siswa'],
        'id_kelas' => $id_kelas,
        'id_guru_piket' => $id_guru_piket,
        // PERBAIKAN: 'id_ta' dihapus dari sini
        'tanggal' => $_POST['tanggal'],
        'catatan' => $_POST['catatan'],
        'tindak_lanjut' => $_POST['tindak_lanjut'],
        'keterangan' => $_POST['keterangan'] ?? ''
    ];

    CatatanKasusModel::save($pdo, $data_to_save);

    $_SESSION['pesan_sukses'] = "Catatan kasus berhasil disimpan!";
    redirect('index.php?mod=catatan_kasus');
}

function catatan_kasus_delete($pdo, $id)
{
    if (!check_access('catatan_kasus', 'delete'))
        redirect('index.php');

    CatatanKasusModel::delete($pdo, $id);
    $_SESSION['pesan_sukses'] = "Catatan kasus berhasil dihapus!";
    redirect('index.php?mod=catatan_kasus');
}