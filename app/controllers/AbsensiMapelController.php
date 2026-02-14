<?php
require_once __DIR__ . '/../models/AbsensiMapelModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php';

function absensi_mapel_index($pdo)
{
    if (!check_access('absensi_mapel', 'index'))
        redirect('index.php');

    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta) {
        die("Informasi Tahun Ajaran tidak ditemukan di sesi Anda. Silakan atur TA aktif.");
    }

    $kelas_diajar = [];
    // [LOGIKA DATA SCOPE] 
    // Admin bisa melihat semua. Guru hanya melihat yang diajar.
    // Kita gunakan in_array manual untuk logic "Scope" data, 
    // atau kita bisa buat permission khusus 'view_all_classes' nanti.
    // Untuk saat ini kita pertahankan logic bisnis based on Role Name untuk Data Scope,
    // tapi Access Control uses check_access.
    if (in_array('Admin', $_SESSION['roles'] ?? [])) {
        $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_ta]);
        $kelas_diajar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Asumsi selain Admin adalah Guru (atau role lain yang dibatasi)
        $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
        if (!$id_guru) {
            // Cek apakah user adalah 'Guru' tapi belum terhubung data guru?
            // Atau mungkin TU? TU biasanya Admin scope.
            if (in_array('TU', $_SESSION['roles'] ?? [])) {
                $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
                $stmt->execute([$id_ta]);
                $kelas_diajar = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                die("Informasi Guru tidak ditemukan di sesi Anda. Silakan login kembali.");
            }
        } else {
            $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru, $id_ta);
        }
    }

    include __DIR__ . '/../views/absensi_mapel_index.php';
}

function absensi_mapel_form($pdo)
{
    // Cek izin Create atau Update (Input Absensi)
    if (!can_do($pdo, 'absensi_mapel', 'create') && !can_do($pdo, 'absensi_mapel', 'update')) {
        redirect('index.php?mod=absensi_mapel');
    }

    $id_kelas = $_GET['id_kelas'] ?? 0;
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    if (!$id_kelas) {
        redirect('index.php?mod=absensi_mapel');
    }

    $siswa_list = AbsensiMapelModel::getSiswaByKelas($pdo, $id_kelas, $id_ta);

    $kelas_info = $pdo->prepare("SELECT nama_kelas, tingkat FROM kelas WHERE id_kelas = ?");
    $kelas_info->execute([$id_kelas]);
    $kelas = $kelas_info->fetch(PDO::FETCH_ASSOC);

    include __DIR__ . '/../views/absensi_mapel_form.php';
}

function absensi_mapel_save($pdo)
{
    if (!can_do($pdo, 'absensi_mapel', 'create') && !can_do($pdo, 'absensi_mapel', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan absensi.";
        redirect('index.php?mod=absensi_mapel');
        return;
    }

    if (empty($_POST['jam_mengajar']) || !is_array($_POST['jam_mengajar'])) {
        die("Gagal menyimpan: Tidak ada jam mengajar yang dipilih.");
    }

    $id_jadwal_mengajar_list = $_POST['jam_mengajar'];
    $placeholders = implode(',', array_fill(0, count($id_jadwal_mengajar_list), '?'));

    $stmt = $pdo->prepare("
            SELECT gm.id_guru_mapel, jp.jam_mulai, jp.jam_selesai 
            FROM jadwal_mengajar dm
            JOIN guru_mapel gm ON dm.id_guru_mapel = gm.id_guru_mapel
            JOIN jam_pelajaran jp ON dm.id_jam = jp.id_jam
            WHERE dm.id_jadwal_mengajar IN ($placeholders)
            ORDER BY jp.jam_mulai ASC
        ");
    $stmt->execute($id_jadwal_mengajar_list);
    $jadwal_info_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($jadwal_info_list)) {
        die("Gagal menyimpan: Detail jadwal tidak valid.");
    }

    $jam_mulai_sesi = $jadwal_info_list[0]['jam_mulai'];
    $jam_selesai_sesi = end($jadwal_info_list)['jam_selesai'];
    $id_guru_mapel = $jadwal_info_list[0]['id_guru_mapel'];
    $jam_ke_string = substr($jam_mulai_sesi, 0, 5) . ' - ' . substr($jam_selesai_sesi, 0, 5);

    // Kemungkinan besar error ada di dalam pendefinisian array ini
    $data_to_save = [
        'id_kelas' => $_POST['id_kelas'],
        'tanggal' => $_POST['tanggal'],
        'jam_ke' => $jam_ke_string,
        'id_ta' => $_SESSION['id_ta_aktif'],
        'id_guru_mapel' => $id_guru_mapel,
        'absensi' => $_POST['absensi']
    ];

    AbsensiMapelModel::save($pdo, $data_to_save);

    $_SESSION['pesan_sukses'] = "Absensi berhasil disimpan!";
    redirect('index.php?mod=absensi_mapel&act=form&id_kelas=' . $_POST['id_kelas'] . '&tanggal=' . $_POST['tanggal']);
}