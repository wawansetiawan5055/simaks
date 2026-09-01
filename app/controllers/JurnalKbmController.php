<?php
require_once __DIR__ . '/../models/JurnalKbmModel.php';
require_once __DIR__ . '/../models/KelasModel.php'; // Diperlukan untuk Admin

function jurnal_kbm_index($pdo)
{
    if (!check_access('jurnal_kbm', 'index'))
        redirect('index.php');

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    if (!$id_ta_aktif)
        die("Error: Tidak ada Tahun Ajaran aktif. Silakan atur di menu Data Master.");

    $kelas_diajar = [];
    // Data Scope Logic
    if (in_array(1, $_SESSION['role_ids'] ?? []) || in_array('TU', $_SESSION['roles'] ?? [])) {
        // [REVISI] Admin/TU bisa melihat SEMUA kelas pada TA Aktif
        $kelas_diajar = KelasModel::all($pdo, $id_ta_aktif);
    } else {
        // Jika Guru, tampilkan hanya kelas yang diajar
        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        if ($id_guru_login > 0) {
            $kelas_diajar = JurnalKbmModel::getKelasDiajar($pdo, $id_guru_login, $id_ta_aktif);
        }
    }

    // ... rest of code ...
    // Note: I will only replace the guard block and data scope block which is inside the replace target

    // [REVISI BARU] Ambil id_kelas dan tanggal dari URL untuk pre-fill
    $id_kelas_prefill = $_GET['id_kelas'] ?? null;
    $tanggal_prefill = $_GET['tanggal'] ?? date('Y-m-d'); // Default ke hari ini

    // [REVISI] Memuat file view baru
    include __DIR__ . '/../views/jurnal_kbm_index.php';
}

function jurnal_kbm_save($pdo)
{
    if (!can_do($pdo, 'jurnal_kbm', 'create')) { // Asumsi isi jurnal = create
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk mengisi jurnal.";
        redirect('index.php?mod=jurnal_kbm');
        return;
    }

    $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    // Jika Admin, dan tidak punya id_guru_terkait, ambil guru pertama saja agar tidak error
    if (in_array(1, $_SESSION['role_ids'] ?? []) && $id_guru_login == 0) {
        $id_guru_login = $pdo->query("SELECT id_guru FROM guru LIMIT 1")->fetchColumn();
    }

    if (!$id_guru_login || !$id_ta_aktif) {
        die("Gagal menyimpan: Informasi Guru atau TA tidak valid.");
    }

    // --- [PERBAIKAN KRUSIAL] SIMPAN LABEL JAM (Bukan ID Jadwal) ---
    $label_jam_array = [];
    if (!empty($_POST['jam_mengajar'])) {
        foreach ($_POST['jam_mengajar'] as $id_jadwal_mengajar) {
            // Ambil label jam (misal "1", "2") dari tabel jam_pelajaran via jadwal_mengajar
            $stmt = $pdo->prepare("
                SELECT jp.label_jam_ke 
                FROM jadwal_mengajar dm 
                JOIN jam_pelajaran jp ON dm.id_jam = jp.id_jam 
                WHERE dm.id_jadwal_mengajar = ?
            ");
            $stmt->execute([$id_jadwal_mengajar]);
            $label = $stmt->fetchColumn();

            if ($label) {
                $label_jam_array[] = $label;
            }
        }
    }
    // Hasil: "1, 2" (disimpan sebagai string)
    $jam_ke_string = implode(', ', $label_jam_array);
    // -------------------------------------------------------------

    // --- [TAMBAHAN] PENANGANAN UPLOAD FOTO ---
    $nama_file_foto = null;
    if (isset($_FILES['foto_kegiatan']) && $_FILES['foto_kegiatan']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['foto_kegiatan']['tmp_name'];
        $fileName = $_FILES['foto_kegiatan']['name'];
        $fileSize = $_FILES['foto_kegiatan']['size'];
        $fileType = mime_content_type($fileTmp);
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            // Buat nama file unik
            $nama_file_foto = 'jurnal_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            $uploadPath = __DIR__ . '/../../public/uploads/jurnal/' . $nama_file_foto;
            
            if (!move_uploaded_file($fileTmp, $uploadPath)) {
                $nama_file_foto = null; // Gagal upload
            }
        } else {
            $_SESSION['pesan_error'] = "Gagal menyimpan foto. Pastikan format gambar JPG/PNG dan maksimal 2MB.";
            // Tetap lanjut simpan jurnal tanpa foto jika foto gagal divalidasi
        }
    }
    // ------------------------------------------

    $data = [
        'id_guru' => $id_guru_login,
        'id_kelas' => $_POST['id_kelas'],
        'id_ta' => $id_ta_aktif,
        'tanggal' => $_POST['tanggal'],
        'jam_ke' => $jam_ke_string, // Simpan "1, 2"
        'tujuan_pembelajaran' => $_POST['tujuan_pembelajaran'],
        'tagihan' => $_POST['tagihan'],
        'catatan_absensi' => $_POST['catatan_absensi'],
        'keterangan' => $_POST['keterangan'] ?? '',
        'foto_kegiatan' => $nama_file_foto
    ];

    JurnalKbmModel::save($pdo, $data);

    $_SESSION['pesan_sukses'] = "Jurnal KBM berhasil disimpan!";
    redirect('index.php?mod=jurnal_kbm');
}