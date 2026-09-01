<?php
require_once __DIR__ . '/../models/AbsensiPiketModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../helpers/DateHelper.php';

function absensi_piket_index($pdo)
{
    // [RBAC DINAMIS] Cek akses berdasarkan modul 'absensi_piket' index
    if (!check_access('absensi_piket', 'index')) {
        redirect('index.php');
    }

    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta) {
        die("Informasi Tahun Ajaran tidak ditemukan di sesi Anda. Silakan atur TA aktif.");
    }

    // Ambil hanya kelas yang memiliki siswa aktif pada TA aktif
    $stmt_k = $pdo->prepare("
        SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat
        FROM kelas k
        JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas
        JOIN siswa s ON ps.id_siswa = s.id_siswa
        WHERE ps.id_ta = ? AND s.status_aktif = 'Aktif'
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_k->execute([$id_ta]);
    $kelas_list = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    // Ambil parameter filter dari URL / GET
    $id_kelas = $_GET['id_kelas'] ?? ($kelas_list[0]['id_kelas'] ?? 0);
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $siswa_list = [];
    $absensi_existing = [];
    $has_existing_data = false;
    $is_past_date = ($tanggal < date('Y-m-d'));
    $is_edit_mode = false;
    $kelas = null;
    $status_sudah = false;

    if ($id_kelas) {
        $siswa_list = AbsensiPiketModel::getSiswaByKelas($pdo, $id_kelas, $id_ta);
        $absensi_existing = AbsensiPiketModel::getAbsensiByKelasAndTanggal($pdo, $id_kelas, $tanggal);
        $has_existing_data = !empty($absensi_existing);
        $is_edit_mode = ($has_existing_data || $is_past_date);
        $status_sudah = AbsensiPiketModel::sudahDiisi($pdo, $id_kelas, $tanggal);

        $kelas_info = $pdo->prepare("SELECT nama_kelas, tingkat FROM kelas WHERE id_kelas = ?");
        $kelas_info->execute([$id_kelas]);
        $kelas = $kelas_info->fetch(PDO::FETCH_ASSOC);
    }

    extract(compact('kelas_list', 'id_kelas', 'tanggal', 'siswa_list', 'kelas', 'absensi_existing', 'has_existing_data', 'is_past_date', 'is_edit_mode', 'status_sudah'));
    include __DIR__ . '/../views/absensi_piket_index.php';
}

function absensi_piket_form($pdo)
{
    // Redirect link lama / referal langsung ke index single-page dengan parameter
    $id_kelas = $_GET['id_kelas'] ?? 0;
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    redirect("index.php?mod=absensi_piket&id_kelas={$id_kelas}&tanggal={$tanggal}");
}

function absensi_piket_save($pdo)
{
    // [RBAC DINAMIS]
    if (!can_do($pdo, 'absensi_piket', 'create') && !can_do($pdo, 'absensi_piket', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan absensi.";
        redirect('index.php?mod=absensi_piket');
        return;
    }

    $id_guru_piket = $_SESSION['id_guru_terkait'] ?? 0;
    if (has_role('Admin') && !$id_guru_piket) {
        $id_guru_piket = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }

    $id_kelas_redirect = $_POST['id_kelas'] ?? 0;
    $tanggal_redirect = $_POST['tanggal'] ?? date('Y-m-d');
    $redirect_url = 'index.php?mod=absensi_piket&id_kelas=' . $id_kelas_redirect . '&tanggal=' . $tanggal_redirect;

    if (!$id_guru_piket) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: Informasi Guru Piket tidak valid.";
        redirect($redirect_url);
        return;
    }

    $data_to_save = [
        'id_kelas' => $_POST['id_kelas'],
        'tanggal' => $_POST['tanggal'],
        'id_ta' => $_SESSION['id_ta_aktif'] ?? 0,
        'id_guru_piket' => $id_guru_piket,
        'absensi' => $_POST['absensi'] ?? []
    ];

    // Panggil Model dan tangkap hasilnya
    $result = AbsensiPiketModel::save($pdo, $data_to_save);

    if ($result) {
        if (function_exists('audit_log')) {
            audit_log('CREATE', "Menyimpan/Memperbarui Absensi Harian Piket Kelas ID: {$_POST['id_kelas']} Tanggal: {$_POST['tanggal']}", 'absensi_piket');
        }
        $_SESSION['pesan_sukses'] = "Absensi piket berhasil disimpan!";
    } else {
        $_SESSION['pesan_error'] = "Gagal menyimpan absensi piket. Silakan cek log server.";
    }

    redirect($redirect_url);
}
