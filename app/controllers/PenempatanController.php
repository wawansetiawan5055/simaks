<?php
require_once __DIR__ . '/../models/PenempatanModel.php';

/**
 * Menampilkan halaman Drag & Drop
 */
require_once __DIR__ . '/../models/DashboardModel.php'; // Load DashboardModel for statistics

/**
 * Halaman Utama: List Daftar Kelas (Rombel)
 */
function penempatan_index($pdo)
{
    if (!check_access('penempatan', 'index')) {
        redirect('index.php?mod=dashboard&error=access_denied');
        return;
    }

    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) {
        die("Error: Silakan pilih Tahun Ajaran yang aktif/ditampilkan terlebih dahulu.");
    }

    // Ambil Data Rekap Kelas (Menggunakan logic Dashboard Model yang sudah lengkap dengan count L/P)
    // Kita gunakan DashboardModel::getRekapSiswaPerKelas karena isinya lengkap (Id, Nama, Tingkat, Total, L, P, Wali Kelas via Join)
    // Note: DashboardModel::getRekapSiswaPerKelas sudah difilter by id_ta di update sebelumnya.
    $rekap_kelas = DashboardModel::getRekapSiswaPerKelas($pdo, $id_ta_tampil);

    // Get Walas Info per kelas (DashboardModel might not return Walas Name directly in getRekapSiswaPerKelas depending on version)
    // Let's check DashboardModel again. Step 668 showed getRekapSiswaPerKelas joins with penempatan_siswa but NOT penugasan_wali_kelas.
    // LaporanController::get_data_rekap_kelas DID join with Walas.
    // To be efficient, let's fetch Walas separately or update query here.

    // Efficient approach: Fetch Walas assignments for this TA
    $walas_map = [];
    $stmt_walas = $pdo->prepare("SELECT pw.id_kelas, g.nama FROM penugasan_wali_kelas pw JOIN guru g ON pw.id_guru = g.id_guru WHERE pw.id_ta = ?");
    $stmt_walas->execute([$id_ta_tampil]);
    $walas_rows = $stmt_walas->fetchAll(PDO::FETCH_KEY_PAIR); // [id_kelas => nama_guru]

    // Inject Walas to rekap
    foreach ($rekap_kelas as &$row) {
        $row['nama_walas'] = $walas_rows[$row['id_kelas']] ?? '-';
    }
    unset($row);

    // For "Tambah Rombel" Modal
    require_once __DIR__ . '/../models/PenugasanModel.php';
    $guru_list = PenugasanModel::all_guru($pdo);
    $standard_classes = ['X.1', 'X.2', 'X.3', 'XI.1', 'XI.2', 'XI.3', 'XII.1', 'XII.2', 'XII.3'];

    include __DIR__ . '/../views/penempatan_list.php';
}

/**
 * Aksi: Tambah Rombel (Kelas + Walas sekaligus)
 */
function penempatan_tambah_rombel($pdo)
{
    if (!check_access('penempatan', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penempatan');
        return;
    }

    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $nama_kelas = $_POST['nama_kelas'] ?? '';
    $tingkat = $_POST['tingkat'] ?? '';
    $id_guru = $_POST['id_guru'] ?? 0;

    if (!$id_ta || !$nama_kelas || !$tingkat) {
        $_SESSION['pesan_error'] = "Data tidak lengkap.";
        redirect('index.php?mod=penempatan');
        return;
    }

    try {
        $pdo->beginTransaction();

        // 1. Simpan ke tabel kelas
        $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, tingkat, id_ta) VALUES (?, ?, ?)");
        $stmt->execute([$nama_kelas, $tingkat, $id_ta]);
        $id_kelas = $pdo->lastInsertId();

        // 2. Simpan ke tabel penugasan_wali_kelas (Otomatis sebaris)
        if ($id_guru) {
            $stmt = $pdo->prepare("INSERT INTO penugasan_wali_kelas (id_guru, id_kelas, id_ta, jenis_tugas) VALUES (?, ?, ?, 'Wali Kelas')");
            $stmt->execute([$id_guru, $id_kelas, $id_ta]);
        }

        $pdo->commit();
        $_SESSION['pesan_sukses'] = "Rombel $nama_kelas berhasil ditambahkan.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['pesan_error'] = "Gagal menambah rombel: " . $e->getMessage();
    }

    redirect('index.php?mod=penempatan');
}

/**
 * Aksi: Hapus Rombel (Hapus Kelas)
 */
function penempatan_hapus_rombel($pdo)
{
    if (!can_do($pdo, 'penempatan', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penempatan');
        return;
    }

    $id_kelas = $_GET['id_kelas'] ?? 0;
    if (!$id_kelas) {
        $_SESSION['pesan_error'] = "ID Kelas tidak valid.";
        redirect('index.php?mod=penempatan');
        return;
    }

    try {
        // Karena ada Foreign Key ON DELETE CASCADE:
        // Menghapus kelas akan otomatis menghapus penempatan_siswa dan penugasan_wali_kelas terkait.
        $stmt = $pdo->prepare("DELETE FROM kelas WHERE id_kelas = ?");
        $stmt->execute([$id_kelas]);

        $_SESSION['pesan_sukses'] = "Rombel berhasil dihapus beserta seluruh riwayat penempatannya.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus rombel: " . $e->getMessage();
    }

    redirect('index.php?mod=penempatan');
}

/**
 * Halaman Kelola: Drag & Drop Siswa
 */
function penempatan_kelola($pdo)
{
    if (!check_access('penempatan', 'index')) { // 'index' access is enough to view
        redirect('index.php?mod=dashboard&error=access_denied');
        return;
    }

    $id_ta_tampil = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_tampil) {
        die("Error: Silakan pilih Tahun Ajaran yang aktif/ditampilkan terlebih dahulu.");
    }

    // Ambil ID kelas yang dipilih dari URL (WAJIB ADA)
    $id_kelas_filter = $_GET['id_kelas'] ?? null;
    if (!$id_kelas_filter) {
        // Jika tidak ada kelas dipilih, kembalikan ke list view
        redirect('index.php?mod=penempatan');
        return;
    }

    // Ambil data untuk dropdown (Opsional, untuk pindah kelas lain langsung?)
    $kelas_list = PenempatanModel::getKelasList($pdo, $id_ta_tampil);
    $ta_list = PenempatanModel::getTahunAjaranList($pdo);

    // --- FILTER SUMBER (COPY ROMBEL) ---
    $id_ta_sumber = $_GET['source_ta'] ?? null;
    $id_kelas_sumber = $_GET['source_kelas'] ?? null;

    $source_kelas_list = [];
    $source_students = [];

    if ($id_ta_sumber) {
        $source_kelas_list = PenempatanModel::getKelasList($pdo, $id_ta_sumber);
        if ($id_kelas_sumber) {
            $source_students_raw = PenempatanModel::getAssignedStudents($pdo, $id_kelas_sumber, $id_ta_sumber, true);

            // [UPDATE] Filter: Hanya tampilkan siswa yang BELUM ditempatkan di TA Target (TA Tampil)
            $stmt_cek = $pdo->prepare("SELECT id_siswa FROM penempatan_siswa WHERE id_ta = ?");
            $stmt_cek->execute([$id_ta_tampil]);
            $already_assigned_ids = $stmt_cek->fetchAll(PDO::FETCH_COLUMN);

            $source_students = array_filter($source_students_raw, function ($s) use ($already_assigned_ids) {
                return !in_array($s['id_siswa'], $already_assigned_ids);
            });
        }
    }

    // Default: Ambil daftar siswa yang BELUM ditempatkan
    $unassigned_students = PenempatanModel::getUnassignedStudents($pdo, $id_ta_tampil);

    // Ambil siswa di kelas target
    $assigned_students = PenempatanModel::getAssignedStudents($pdo, $id_kelas_filter, $id_ta_tampil);

    // Cari info kelas
    $info_kelas = null;
    foreach ($kelas_list as $k) {
        if ($k['id_kelas'] == $id_kelas_filter) {
            $info_kelas = $k;
            break;
        }
    }

    extract(compact('id_ta_tampil', 'id_kelas_filter', 'kelas_list', 'ta_list', 'unassigned_students', 'assigned_students', 'info_kelas', 'id_ta_sumber', 'id_kelas_sumber', 'source_kelas_list', 'source_students'));

    // View sekarang bernama penempatan_kelola.php (akan kita rename dari penempatan_index.php)
    include __DIR__ . '/../views/penempatan_kelola.php';
}

/**
 * Menyalin Rombel (Bulk Copy)
 */
function penempatan_copy_rombel($pdo)
{
    if (!check_access('penempatan', 'create')) { // Asumsi butuh create access
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=penempatan');
        return;
    }

    $id_ta_target = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_kelas_target = $_POST['target_kelas'] ?? 0;
    $id_ta_sumber = $_POST['source_ta'] ?? 0;
    $id_kelas_sumber = $_POST['source_kelas'] ?? 0;

    if (!$id_ta_target || !$id_kelas_target || !$id_ta_sumber || !$id_kelas_sumber) {
        $_SESSION['pesan_error'] = "Data tidak lengkap. Pastikan Tahun Ajaran dan Kelas Sumber & Target dipilih.";
        redirect("index.php?mod=penempatan&id_kelas=$id_kelas_target&source_ta=$id_ta_sumber&source_kelas=$id_kelas_sumber");
        return;
    }

    try {
        $count = PenempatanModel::copyRombel($pdo, $id_kelas_sumber, $id_ta_sumber, $id_kelas_target, $id_ta_target);
        if ($count > 0) {
            $_SESSION['pesan_sukses'] = "Berhasil menyalin $count siswa ke kelas ini.";
        } else {
            $_SESSION['pesan_warning'] = "Tidak ada siswa yang disalin (Mungkin kelas sumber kosong).";
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyalin: " . $e->getMessage();
    }

    redirect("index.php?mod=penempatan&id_kelas=$id_kelas_target&source_ta=$id_ta_sumber&source_kelas=$id_kelas_sumber");
}

/**
 * Menyimpan data (dipanggil oleh AJAX drag-and-drop)
 */
function penempatan_save($pdo)
{
    if (!can_do($pdo, 'penempatan', 'update')) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak (Butuh Izin Update).']);
        exit;
    }

    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;
    $id_siswa = $_POST['id_siswa'] ?? 0;
    $id_kelas_baru = $_POST['id_kelas'] ?? 0;

    if (!$id_ta || !$id_siswa || !$id_kelas_baru) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    try {
        PenempatanModel::assignStudent($pdo, $id_siswa, $id_kelas_baru, $id_ta);
        echo json_encode(['status' => 'success', 'message' => 'Siswa berhasil ditempatkan.']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

/**
 * Menghapus data (dipanggil oleh AJAX drag-and-drop kembali)
 */
function penempatan_delete($pdo, $id_siswa)
{
    if (!can_do($pdo, 'penempatan', 'delete')) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak (Butuh Izin Delete).']);
        exit;
    }

    $id_ta = $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0;

    if (!$id_ta || !$id_siswa) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    try {
        PenempatanModel::unassignStudent($pdo, $id_siswa, $id_ta);
        echo json_encode(['status' => 'success', 'message' => 'Siswa berhasil dikeluarkan dari kelas.']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}