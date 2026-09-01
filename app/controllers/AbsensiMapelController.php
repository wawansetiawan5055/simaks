<?php
require_once __DIR__ . '/../models/AbsensiMapelModel.php';
require_once __DIR__ . '/../models/JurnalKbmModel.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../helpers/DateHelper.php';

function absensi_mapel_index($pdo)
{
    if (!check_access('absensi_mapel', 'index'))
        redirect('index.php');

    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta) {
        die("Informasi Tahun Ajaran tidak ditemukan di sesi Anda. Silakan atur TA aktif.");
    }

    $kelas_diajar = [];
    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array(1, $_SESSION['role_ids'] ?? [])) {
        $stmt = $pdo->prepare("SELECT id_kelas, nama_kelas, tingkat FROM kelas WHERE id_ta = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_ta]);
        $kelas_diajar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
        if (!$id_guru) {
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

    // Ambil parameter filter dari URL / GET
    $id_kelas = $_GET['id_kelas'] ?? ($kelas_diajar[0]['id_kelas'] ?? 0);
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

    $siswa_list = [];
    $absensi_existing = [];
    $has_existing_data = false;
    $is_past_date = ($tanggal < date('Y-m-d'));
    $is_edit_mode = false;
    $kelas = null;

    // id_guru_mapel diambil dari GET jika sudah diketahui (dikirim saat POST/redirect)
    // atau dari session guru yang login
    $id_guru_mapel_filter = (int)($_GET['id_guru_mapel'] ?? 0);
    if (!$id_guru_mapel_filter) {
        // Coba auto-detect dari sesi guru + kelas + tanggal (ambil jadwal pertama hari ini)
        $id_guru_session = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_session && $id_kelas) {
            // Ambil id_guru_mapel pertama yang sesuai guru + kelas pada hari itu
            $hari_ini = date('l', strtotime($tanggal)); // 'Monday', 'Tuesday', dst
            $hari_indo_map = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $hari_indo = $hari_indo_map[$hari_ini] ?? $hari_ini;
            $stmt_gm = $pdo->prepare("
                SELECT gm.id_guru_mapel FROM jadwal_mengajar jm
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                WHERE jm.id_kelas = ? AND gm.id_guru = ? AND jm.hari_kbm = ?
                LIMIT 1
            ");
            $stmt_gm->execute([$id_kelas, $id_guru_session, $hari_indo]);
            $id_guru_mapel_filter = (int)($stmt_gm->fetchColumn() ?: 0);
        }
    }

    if ($id_kelas) {
        $siswa_list = AbsensiMapelModel::getSiswaByKelas($pdo, $id_kelas, $id_ta);

        // [FIX] Filter absensi berdasarkan id_guru_mapel jika diketahui,
        // sehingga tidak menampilkan data dari guru/mapel lain
        if ($id_guru_mapel_filter) {
            $absensi_existing = AbsensiMapelModel::getAbsensiByKelasAndTanggal($pdo, $id_kelas, $tanggal, $id_guru_mapel_filter);
        } else {
            // Tidak ada filter guru_mapel: kosongkan saja supaya tidak salah tampil
            $absensi_existing = [];
        }

        $has_existing_data = !empty($absensi_existing);
        $is_edit_mode = ($has_existing_data || $is_past_date);

        $kelas_info = $pdo->prepare("SELECT nama_kelas, tingkat FROM kelas WHERE id_kelas = ?");
        $kelas_info->execute([$id_kelas]);
        $kelas = $kelas_info->fetch(PDO::FETCH_ASSOC);

        // Ambil daftar materi LMS untuk kelas/guru ini (untuk dropdown Sinkronisasi LMS)
        $id_guru_session = $_SESSION['id_guru_terkait'] ?? 0;
        $stmt_mat = $pdo->prepare("
            SELECT m.id_materi, m.judul_materi, mp.nama_mapel 
            FROM lms_materi m 
            JOIN mapel mp ON m.id_mapel = mp.id_mapel
            WHERE (m.id_guru = ? OR ? IS NULL OR ? = 0)
            ORDER BY m.created_at DESC LIMIT 25
        ");
        $stmt_mat->execute([$id_guru_session, $id_guru_session, $id_guru_session]);
        $materi_lms_list = $stmt_mat->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $materi_lms_list = [];
    }

    extract(compact('kelas_diajar', 'id_kelas', 'tanggal', 'siswa_list', 'kelas', 'absensi_existing', 'has_existing_data', 'is_past_date', 'is_edit_mode', 'id_guru_mapel_filter', 'materi_lms_list'));
    include __DIR__ . '/../views/absensi_mapel_index.php';
}

function absensi_mapel_form($pdo)
{
    // Redirect link lama / referal eksternal langsung ke halaman index dengan parameter
    $id_kelas = $_GET['id_kelas'] ?? 0;
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    redirect("index.php?mod=absensi_mapel&id_kelas={$id_kelas}&tanggal={$tanggal}");
}

/**
 * AJAX: Tarik status kehadiran siswa dari aktivitas belajar LMS & cross-check Piket
 */
function absensi_mapel_sync_lms($pdo) {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../models/LmsModel.php';

    $id_materi = (int)($_GET['id_materi'] ?? $_POST['id_materi'] ?? 0);
    $id_kelas  = (int)($_GET['id_kelas'] ?? $_POST['id_kelas'] ?? 0);
    $tanggal   = $_GET['tanggal'] ?? $_POST['tanggal'] ?? date('Y-m-d');

    if (!$id_materi || !$id_kelas) {
        echo json_encode(['status' => 'error', 'message' => 'Silakan pilih Modul Materi LMS terlebih dahulu.']);
        exit;
    }

    $result = LmsModel::getLmsAttendanceByMateriKelas($pdo, $id_materi, $id_kelas, $tanggal);
    echo json_encode($result);
    exit;
}

function absensi_mapel_save($pdo)
{
    if (!can_do($pdo, 'absensi_mapel', 'create') && !can_do($pdo, 'absensi_mapel', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan absensi.";
        redirect('index.php?mod=absensi_mapel');
        return;
    }

    if (empty($_POST['jam_mengajar']) || !is_array($_POST['jam_mengajar'])) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: Tidak ada jam mengajar yang dipilih.";
        redirect('index.php?mod=absensi_mapel&id_kelas=' . ($_POST['id_kelas'] ?? 0) . '&tanggal=' . ($_POST['tanggal'] ?? date('Y-m-d')));
        return;
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
        $_SESSION['pesan_error'] = "Gagal menyimpan: Detail jadwal tidak valid.";
        redirect('index.php?mod=absensi_mapel&id_kelas=' . ($_POST['id_kelas'] ?? 0) . '&tanggal=' . ($_POST['tanggal'] ?? date('Y-m-d')));
        return;
    }

    $jam_mulai_sesi = $jadwal_info_list[0]['jam_mulai'];
    $jam_selesai_sesi = end($jadwal_info_list)['jam_selesai'];
    $id_guru_mapel = $jadwal_info_list[0]['id_guru_mapel'];
    $jam_ke_string = substr($jam_mulai_sesi, 0, 5) . ' - ' . substr($jam_selesai_sesi, 0, 5);

    $data_to_save = [
        'id_kelas' => $_POST['id_kelas'],
        'tanggal' => $_POST['tanggal'],
        'jam_ke' => $jam_ke_string,
        'id_ta' => $_SESSION['id_ta_aktif'],
        'id_guru_mapel' => $id_guru_mapel,
        'absensi' => $_POST['absensi'] ?? []
    ];

    AbsensiMapelModel::save($pdo, $data_to_save);

    $_SESSION['pesan_sukses'] = "Absensi berhasil disimpan!";
    redirect('index.php?mod=absensi_mapel&id_kelas=' . $_POST['id_kelas'] . '&tanggal=' . $_POST['tanggal']);
}
