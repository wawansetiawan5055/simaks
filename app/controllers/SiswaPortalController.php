<?php
/**
 * SiswaPortalController.php
 * Controller untuk semua fitur portal siswa.
 * Setiap fungsi otomatis memverifikasi bahwa user adalah Siswa.
 */

require_once __DIR__ . '/../models/SiswaPortalModel.php';

// -----------------------------------------------------------------------
// HELPER: pastikan user adalah siswa dan ambil data session
// -----------------------------------------------------------------------
function _siswa_portal_guard(): array
{
    if (!in_array('Siswa', $_SESSION['roles'] ?? [])) {
        $_SESSION['pesan_error'] = 'Akses ditolak.';
        redirect('index.php?mod=auth&act=login');
        exit;
    }
    return [
        'id_siswa' => (int)($_SESSION['id_siswa_terkait'] ?? 0),
        'id_ta'    => (int)($_SESSION['id_ta_aktif'] ?? 0),
        'user_id'  => (int)($_SESSION['user_id'] ?? 0),
    ];
}

// -----------------------------------------------------------------------
// 1. JADWAL PELAJARAN
// -----------------------------------------------------------------------
function siswa_portal_jadwal(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $kelas = SiswaPortalModel::getKelasSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $jadwal = [];
    if ($kelas) {
        $jadwal = SiswaPortalModel::getJadwalByKelas($pdo, (int)$kelas['id_kelas'], $s['id_ta']);
    }

    $hari_urutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    // Hari aktif hari ini
    $hari_map = [
        'Monday'   => 'Senin',   'Tuesday'  => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',   'Friday'   => 'Jumat',  'Saturday'  => 'Sabtu',
        'Sunday'   => 'Minggu'
    ];
    $hari_aktif = $hari_map[date('l')] ?? 'Senin';

    include __DIR__ . '/../views/siswa_jadwal.php';
}

// -----------------------------------------------------------------------
// 2. NILAI SAYA
// -----------------------------------------------------------------------
function siswa_portal_nilai(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $komprehensif  = SiswaPortalModel::getNilaiKomprehensif($pdo, $s['id_siswa'], $s['id_ta']);
    $nilai_grouped = $komprehensif['mapel_list'];
    $summary       = $komprehensif['summary'];
    $rows_formatif = $komprehensif['rows_formatif'];
    $rows_sumatif  = $komprehensif['rows_sumatif'];
    $rows_lms      = $komprehensif['rows_lms'];
    $rows_cbt      = $komprehensif['rows_cbt'];
    $kelas         = SiswaPortalModel::getKelasSiswa($pdo, $s['id_siswa'], $s['id_ta']);

    // Ambil profil lengkap siswa (Nama, NISN, NIPD, Foto, dsb)
    $stmt_s = $pdo->prepare("
        SELECT s.*, k.nama_kelas, k.tingkat, ps.id_penempatan, g.nama AS nama_wali_kelas
        FROM siswa s 
        LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = ? AND ps.status_penempatan = 'Aktif' 
        LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas 
        LEFT JOIN penugasan_wali_kelas pwk ON pwk.id_kelas = k.id_kelas AND pwk.id_ta = ? AND pwk.jenis_tugas = 'Wali Kelas'
        LEFT JOIN guru g ON pwk.id_guru = g.id_guru
        WHERE s.id_siswa = ?
    ");
    $stmt_s->execute([$s['id_ta'], $s['id_ta'], $s['id_siswa']]);
    $siswa_data = $stmt_s->fetch(PDO::FETCH_ASSOC) ?: [];

    // Ambil rekap kehadiran / absensi siswa
    $absensi_piket = SiswaPortalModel::getAbsensiPiketSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $hadir_count   = array_sum(array_column($absensi_piket, 'hadir'));
    $sakit_count   = array_sum(array_column($absensi_piket, 'sakit'));
    $izin_count    = array_sum(array_column($absensi_piket, 'izin'));
    $alpa_count    = array_sum(array_column($absensi_piket, 'alpa'));
    $total_presensi = $hadir_count + $sakit_count + $izin_count + $alpa_count;
    $pct_kehadiran  = $total_presensi > 0 ? round(($hadir_count / $total_presensi) * 100, 1) : 100;

    $kehadiran = [
        'hadir' => $hadir_count,
        'sakit' => $sakit_count,
        'izin'  => $izin_count,
        'alpa'  => $alpa_count,
        'total' => $total_presensi,
        'persentase' => $pct_kehadiran
    ];

    // Ambil catatan wali kelas jika ada
    $catatan_wali = null;
    if (!empty($siswa_data['id_penempatan'])) {
        $stmt_c = $pdo->prepare("SELECT catatan FROM catatan_wali_kelas WHERE id_penempatan = ? AND id_ta = ? ORDER BY id DESC LIMIT 1");
        $stmt_c->execute([$siswa_data['id_penempatan'], $s['id_ta']]);
        $catatan_wali = $stmt_c->fetchColumn() ?: null;
    }

    include __DIR__ . '/../views/siswa_nilai.php';
}

// -----------------------------------------------------------------------
// 3. ABSENSI
// -----------------------------------------------------------------------
function siswa_portal_absensi(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $tab              = $_GET['tab'] ?? 'kelas'; // 'kelas' atau 'mapel'
    $absensi_piket    = SiswaPortalModel::getAbsensiPiketSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $absensi_mapel    = SiswaPortalModel::getAbsensiMapelSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $riwayat_piket    = SiswaPortalModel::getRiwayatHarianPiket($pdo, $s['id_siswa'], $s['id_ta'], 50);

    // Hitung total per status untuk summary
    $total_hadir = array_sum(array_column($absensi_piket, 'hadir'));
    $total_sakit = array_sum(array_column($absensi_piket, 'sakit'));
    $total_izin  = array_sum(array_column($absensi_piket, 'izin'));
    $total_alpa  = array_sum(array_column($absensi_piket, 'alpa'));
    $total_semua = $total_hadir + $total_sakit + $total_izin + $total_alpa;
    $persentase_kehadiran = $total_semua > 0 ? round(($total_hadir / $total_semua) * 100, 1) : 100;

    include __DIR__ . '/../views/siswa_absensi.php';
}

// -----------------------------------------------------------------------
// 4. TAGIHAN SPP
// -----------------------------------------------------------------------
function siswa_portal_tagihan(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $tagihan_list  = SiswaPortalModel::getTagihanSiswa($pdo, $s['id_siswa']);
    $total_tagihan = array_sum(array_column($tagihan_list, 'jumlah_tagihan'));
    $total_dibayar = array_sum(array_column($tagihan_list, 'total_dibayar'));
    $total_sisa    = $total_tagihan - $total_dibayar;

    include __DIR__ . '/../views/siswa_tagihan.php';
}

// -----------------------------------------------------------------------
// 5. PENGEMBANGAN KARAKTER (HALAMAN TERPISAH MANDIRI)
// -----------------------------------------------------------------------
function siswa_portal_pembiasaan(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    $tab = 'pembiasaan';
    $pembiasaan = SiswaPortalModel::getPembiasaanSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    include __DIR__ . '/../views/siswa_pembiasaan.php';
}

function siswa_portal_tahfidz(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    $tab = 'tahfidz';
    $tahfidz = SiswaPortalModel::getTahfidzSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    include __DIR__ . '/../views/siswa_tahfidz.php';
}

function siswa_portal_ekskul(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    $tab = 'ekskul';
    $ekskul = SiswaPortalModel::getEkskulSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    include __DIR__ . '/../views/siswa_ekskul.php';
}

function siswa_portal_kokulikuler(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    $tab = 'kokulikuler';
    $kokulikuler = SiswaPortalModel::getKokulikulerSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    include __DIR__ . '/../views/siswa_kokulikuler.php';
}

function siswa_portal_kewirausahaan(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    $tab = 'kewirausahaan';
    $kewirausahaan = SiswaPortalModel::getKewirausahaanSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    include __DIR__ . '/../views/siswa_kewirausahaan.php';
}

function siswa_portal_progress(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $tab = $_GET['tab'] ?? 'pembiasaan';
    if ($tab === 'tahfidz') {
        siswa_portal_tahfidz();
    } elseif ($tab === 'ekskul') {
        siswa_portal_ekskul();
    } elseif ($tab === 'kokulikuler') {
        siswa_portal_kokulikuler();
    } elseif ($tab === 'kewirausahaan') {
        siswa_portal_kewirausahaan();
    } else {
        siswa_portal_pembiasaan();
    }
}

// -----------------------------------------------------------------------
// 6. KALENDER AKADEMIK
// -----------------------------------------------------------------------
function siswa_portal_kalender(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $kegiatan = SiswaPortalModel::getKalenderAkademik($pdo, $s['id_ta']);

    include __DIR__ . '/../views/siswa_kalender.php';
}

// -----------------------------------------------------------------------
// 7. MATERI AJAR (INTEGRASI LMS & STRUKTUR BUKU)
// -----------------------------------------------------------------------
function siswa_portal_materi(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    require_once __DIR__ . '/../models/LmsModel.php';
    
    // Ambil tingkat default siswa
    $stmt_tingkat = $pdo->prepare("SELECT tingkat FROM kelas k JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas WHERE ps.id_siswa = ? AND ps.id_ta = ? AND ps.status_penempatan = 'Aktif' ORDER BY ps.id_penempatan DESC LIMIT 1");
    $stmt_tingkat->execute([$s['id_siswa'], $s['id_ta']]);
    $tingkat_default = $stmt_tingkat->fetchColumn() ?: 'X';

    // Daftar Mata Pelajaran
    $stmt_mapel = $pdo->query("SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC");
    $mapel_list = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);

    $id_mapel_filter = $_GET['id_mapel'] ?? ($mapel_list[0]['id_mapel'] ?? null);
    $tingkat_filter = $_GET['tingkat'] ?? $tingkat_default;
    $semester_filter = $_GET['semester'] ?? 'Ganjil';
    $search = $_GET['search'] ?? null;

    $tree_data = [];
    if ($id_mapel_filter && $tingkat_filter) {
        $tree_data = LmsModel::getStrukturBuku($pdo, $id_mapel_filter, $tingkat_filter, $semester_filter);
    }

    // Ambil daftar id_materi yang sudah ditugaskan ke siswa ini (status Aktif)
    // Digunakan untuk menentukan apakah Learning Path 4-6 bisa dibuka per modul
    $stmt_penugasan = $pdo->prepare(
        "SELECT DISTINCT t.id_materi FROM lms_tugas t
         WHERE t.id_materi IS NOT NULL AND t.id_materi > 0 AND t.status = 'Aktif'
         AND t.id_kelas IN (SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif')"
    );
    $stmt_penugasan->execute([$s['id_siswa']]);
    $penugasan_materi_ids = $stmt_penugasan->fetchAll(PDO::FETCH_COLUMN, 0);
    $penugasan_materi_ids = array_map('intval', $penugasan_materi_ids);

    include __DIR__ . '/../views/siswa_materi.php';
}

// -----------------------------------------------------------------------
// 8. BANK TUGAS (INTEGRASI LMS)
// -----------------------------------------------------------------------
function siswa_portal_tugas(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    require_once __DIR__ . '/../models/LmsModel.php';
    $tugas = LmsModel::getTugasForSiswa($pdo, $s['id_siswa']);

    include __DIR__ . '/../views/siswa_tugas.php';
}

// -----------------------------------------------------------------------
// 9. DETAIL MATERI (INTEGRASI LMS TITIAN TANGGA)
// -----------------------------------------------------------------------
function siswa_portal_materi_detail(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    $id_materi = (int)($_GET['id'] ?? 0);

    require_once __DIR__ . '/../models/LmsModel.php';
    $materi = LmsModel::getMateriById($pdo, $id_materi);
    if (!$materi) {
        die("Materi tidak ditemukan.");
    }
    
    // Ambil data pendukung
    $cp_data = null;
    $tp_data = [];
    if ($materi['id_cp']) {
        $stmt_cp = $pdo->prepare("SELECT * FROM capaian_pembelajaran WHERE id_cp = ?");
        $stmt_cp->execute([$materi['id_cp']]);
        $cp_data = $stmt_cp->fetch();
    }
    if ($materi['id_tp']) {
        $tp_ids = explode(',', $materi['id_tp']);
        $placeholders = implode(',', array_fill(0, count($tp_ids), '?'));
        $stmt_tp = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders)");
        $stmt_tp->execute($tp_ids);
        $tp_data = $stmt_tp->fetchAll();
    }
    
    $soal_list = LmsModel::getSoalByMateri($pdo, $id_materi);
    $id_tugas = (int)($_GET['tugas'] ?? ($_GET['id_tugas'] ?? 0));
    $id_siswa = (int)($s['id_siswa'] ?? 0);
    $tugas_terkait = ($id_tugas > 0) ? LmsModel::getTugasById($pdo, $id_tugas) : null;
    $is_penugasan = !empty($tugas_terkait) && ($id_tugas > 0);

    if ($id_siswa) {
        $has_submitted = LmsModel::hasSubmittedQuiz($pdo, $id_materi, $id_siswa);
        $stmt_k = $pdo->prepare("SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif' ORDER BY id_penempatan DESC LIMIT 1");
        $stmt_k->execute([$id_siswa]);
        $id_kelas_siswa = $stmt_k->fetchColumn() ?: null;
        LmsModel::recordStudentCheckin($pdo, $id_materi, $id_siswa, $id_kelas_siswa);
    }

    $diskusi_list = LmsModel::getDiskusiByMateri($pdo, $id_materi);
    $materi_progress = LmsModel::getMateriProgress($pdo, $id_materi, $id_siswa, $id_tugas);

    include __DIR__ . '/../views/lms_materi_detail.php';
}

// -----------------------------------------------------------------------
// 10. SUBMIT TUGAS (INTEGRASI LMS)
// -----------------------------------------------------------------------
function siswa_portal_tugas_submit(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    
    require_once __DIR__ . '/../models/LmsModel.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = LmsModel::submitTugas($pdo, $_POST, $_FILES, $s['id_siswa']);
            $_SESSION['success'] = "Tugas berhasil dikumpulkan.";
            redirect('index.php?mod=siswa_portal&act=tugas');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    $id_tugas = $_GET['id_tugas'] ?? 0;
    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    if (!$tugas) {
        die("Tugas tidak ditemukan.");
    }
    
    if (!empty($tugas['id_materi'])) {
        // --- LEARNING PATH (WIZARD) ---
        $materi = LmsModel::getMateriById($pdo, $tugas['id_materi']);
        
        // Ambil data CP/TP
        $cp_data = null;
        $tp_data = [];
        if ($materi['id_cp']) {
            $stmt_cp = $pdo->prepare("SELECT * FROM capaian_pembelajaran WHERE id_cp = ?");
            $stmt_cp->execute([$materi['id_cp']]);
            $cp_data = $stmt_cp->fetch();
        }
        if ($materi['id_tp']) {
            $tp_ids = explode(',', $materi['id_tp']);
            $placeholders = implode(',', array_fill(0, count($tp_ids), '?'));
            $stmt_tp = $pdo->prepare("SELECT * FROM tujuan_pembelajaran WHERE id_tp IN ($placeholders)");
            $stmt_tp->execute($tp_ids);
            $tp_data = $stmt_tp->fetchAll();
        }
        
        $soal_list = LmsModel::getSoalByMateri($pdo, $tugas['id_materi']);
        $diskusi_list = LmsModel::getDiskusiByMateri($pdo, $tugas['id_materi']);
        
        $tugas_terkait = $tugas;
        $id_siswa = (int)($s['id_siswa'] ?? 0);
        $is_penugasan = true;
        $materi_progress = LmsModel::getMateriProgress($pdo, $tugas['id_materi'], $id_siswa, $id_tugas);
        $has_submitted = LmsModel::hasSubmittedQuiz($pdo, $tugas['id_materi'], $id_siswa);
        $user_roles = user_roles();

        include __DIR__ . '/../views/lms_materi_detail.php';
    } else {
        // --- TUGAS BIASA (UPLOAD) ---
        // Cek pengumpulan sebelumnya
        $sql = "SELECT * FROM lms_pengumpulan WHERE id_tugas = ? AND id_siswa = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas, $s['id_siswa']]);
        $pengumpulan = $stmt->fetch();

        include __DIR__ . '/../views/lms_tugas_submit.php';
    }
}

// -----------------------------------------------------------------------
// 11. DASHBOARD PORTAL SISWA (MENGGANTIKAN DASHBOARD LMS)
// -----------------------------------------------------------------------
function siswa_portal_dashboard(): void
{
    global $pdo;
    $s = _siswa_portal_guard();
    
    require_once __DIR__ . '/../models/LmsModel.php';
    require_once __DIR__ . '/../models/SiswaPortalModel.php';
    
    $id_siswa = $s['id_siswa'];
    $id_ta = $s['id_ta'];

    // 1. Data Detail Siswa & Kelas Aktif
    $data['siswa'] = LmsModel::getSiswaDetail($pdo, $id_siswa, $id_ta);
    $data['kelas'] = SiswaPortalModel::getKelasSiswa($pdo, $id_siswa, $id_ta);
    
    // Ambil Wali Kelas jika ada
    $data['wali_kelas'] = '-';
    if (!empty($data['kelas']['id_kelas'])) {
        $stmt_walas = $pdo->prepare("
            SELECT g.nama 
            FROM penugasan_wali_kelas pw 
            JOIN guru g ON pw.id_guru = g.id_guru 
            WHERE pw.id_kelas = ? AND pw.id_ta = ? LIMIT 1
        ");
        $stmt_walas->execute([$data['kelas']['id_kelas'], $id_ta]);
        $data['wali_kelas'] = $stmt_walas->fetchColumn() ?: '-';
    }

    // 2. Data Card 1: Mata Pelajaran, Tugas Tersedia, Tugas Selesai, Nilai Rata-rata
    $data['mapel_count'] = LmsModel::countMapelForSiswa($pdo, $id_siswa, $id_ta);
    
    $tugas_pending = LmsModel::getTugasPendingForSiswa($pdo, $id_siswa);
    $data['tugas_pending_count'] = count($tugas_pending);
    $data['tugas_pending_list']  = array_slice($tugas_pending, 0, 5);
    
    $data['tugas_done_count'] = LmsModel::countTugasSelesaiForSiswa($pdo, $id_siswa);
    
    // Rata-rata Nilai Terpadu (Formatif, Sumatif, LMS, CBT)
    $kompre = SiswaPortalModel::getNilaiKomprehensif($pdo, $id_siswa, $id_ta);
    $data['rata_nilai'] = (float)($kompre['summary']['global_avg'] ?? 0);

    // 3. Data Card 2: Kehadiran (Hadir, Sakit, Izin, Alpa)
    $absensi_piket = SiswaPortalModel::getAbsensiPiketSiswa($pdo, $id_siswa, $id_ta);
    $data['absensi'] = [
        'hadir' => (int)array_sum(array_column($absensi_piket, 'hadir')),
        'sakit' => (int)array_sum(array_column($absensi_piket, 'sakit')),
        'izin'  => (int)array_sum(array_column($absensi_piket, 'izin')),
        'alpa'  => (int)array_sum(array_column($absensi_piket, 'alpa')),
    ];

    // 4. Jadwal Hari Ini (Otomatis Merged Sesuai Hari Ini)
    $hari_map = [
        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
    ];
    $hari_ini = $hari_map[date('l')] ?? 'Senin';
    $data['hari_ini'] = $hari_ini;
    $data['jadwal_hari_ini'] = [];

    if (!empty($data['kelas']['id_kelas'])) {
        $all_jadwal = SiswaPortalModel::getJadwalByKelas($pdo, (int)$data['kelas']['id_kelas'], $id_ta);
        $data['jadwal_hari_ini'] = $all_jadwal[$hari_ini] ?? [];
    }

    // 5. Materi Terbaru
    $data['materi_terbaru'] = array_slice(LmsModel::getMateriForSiswa($pdo, $id_siswa), 0, 4);

    // 6. Ujian CBT Aktif
    $data['cbt_aktif'] = $pdo->query("
        SELECT j.id_jadwal, j.nama_ujian, j.durasi_menit, j.tanggal_mulai, j.tanggal_selesai, m.nama_mapel
        FROM cbt_jadwal j
        LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
        LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
        WHERE j.status = 'aktif' AND (j.id_kelas = 0 OR j.id_kelas = " . (int)($data['kelas']['id_kelas'] ?? 0) . ")
        ORDER BY j.id_jadwal DESC LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 7. Pengumuman Terakhir
    $data['pengumuman'] = $pdo->query("SELECT * FROM pengumuman ORDER BY id_pengumuman DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

    include __DIR__ . '/../views/siswa_dashboard_portal.php';
}

// -----------------------------------------------------------------------
// 12. PERMOHONAN IZIN / SAKIT â€” Form + Riwayat
// -----------------------------------------------------------------------
function siswa_portal_permohonan(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    require_once __DIR__ . '/../models/PermohonanAbsensiModel.php';
    require_once __DIR__ . '/../models/SiswaPortalModel.php';

    $riwayat   = PermohonanAbsensiModel::getRiwayatSiswa($pdo, $s['id_siswa']);
    $mapel_list = PermohonanAbsensiModel::getMapelSiswa($pdo, $s['id_siswa'], $s['id_ta']);

    // Ambil id_kelas aktif siswa (untuk disimpan ke permohonan)
    $kelas = SiswaPortalModel::getKelasSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $id_kelas_aktif = $kelas['id_kelas'] ?? null;

    include __DIR__ . '/../views/siswa_permohonan.php';
}

// -----------------------------------------------------------------------
// 13. PERMOHONAN â€” SIMPAN (POST)
// -----------------------------------------------------------------------
function siswa_portal_permohonan_simpan(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    require_once __DIR__ . '/../models/PermohonanAbsensiModel.php';
    require_once __DIR__ . '/../models/SiswaPortalModel.php';

    // --- Validasi tanggal: hanya H dan H+1 (Besok) ---
    $tanggal  = $_POST['tanggal'] ?? '';
    $today    = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));

    if (!in_array($tanggal, [$today, $tomorrow])) {
        $_SESSION['pesan_error'] = 'Tanggal permohonan hanya boleh untuk hari ini atau besok.';
        redirect('index.php?mod=siswa_portal&act=permohonan');
        return;
    }

    $jenis_absensi = $_POST['jenis_absensi'] ?? 'piket';
    $jenis_izin    = $_POST['jenis_izin']    ?? '';
    
    // Handle array mapel
    $id_mapel = null;
    if ($jenis_absensi === 'mapel') {
        $mapel_arr = $_POST['id_mapel'] ?? [];
        if (!is_array($mapel_arr)) {
            $mapel_arr = [$mapel_arr];
        }
        $mapel_arr = array_filter(array_map('intval', $mapel_arr));
        if (empty($mapel_arr)) {
            $_SESSION['pesan_error'] = 'Pilih minimal satu mata pelajaran.';
            redirect('index.php?mod=siswa_portal&act=permohonan');
            return;
        }
        $id_mapel = implode(',', $mapel_arr);
    }

    $keterangan    = trim($_POST['keterangan'] ?? '');

    // Validasi jenis_izin
    if (!in_array($jenis_izin, ['Sakit', 'Izin', 'Lainnya'])) {
        $_SESSION['pesan_error'] = 'Jenis permohonan tidak valid.';
        redirect('index.php?mod=siswa_portal&act=permohonan');
        return;
    }

    // Cek duplikat
    if (PermohonanAbsensiModel::isDuplikat($pdo, $s['id_siswa'], $tanggal, $jenis_absensi, $id_mapel)) {
        $_SESSION['pesan_error'] = 'Anda sudah mengajukan permohonan untuk tanggal dan jenis yang sama. Tunggu hasil validasi.';
        redirect('index.php?mod=siswa_portal&act=permohonan');
        return;
    }

    // Ambil id_kelas aktif
    $kelas = SiswaPortalModel::getKelasSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $id_kelas = $kelas['id_kelas'] ?? null;

    // --- Upload foto (Bisa via File Upload atau Live Camera Base64) ---
    $foto_bukti = null;
    $foto_cam_data = $_POST['foto_bukti_cam'] ?? '';

    if (!empty($foto_cam_data) && preg_match('/^data:image\/(\w+);base64,/', $foto_cam_data, $cam_match)) {
        $raw_base64 = substr($foto_cam_data, strpos($foto_cam_data, ',') + 1);
        $decoded_image = base64_decode($raw_base64);
        if ($decoded_image !== false) {
            $cam_ext = strtolower($cam_match[1]);
            if ($cam_ext === 'jpeg') $cam_ext = 'jpg';
            if (!in_array($cam_ext, ['jpg', 'jpeg', 'png', 'webp'])) $cam_ext = 'jpg';

            $dir = __DIR__ . '/../../public/uploads/permohonan/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $foto_bukti = 'perm_' . $s['id_siswa'] . '_' . time() . '.' . $cam_ext;
            file_put_contents($dir . $foto_bukti, $decoded_image);
        }
    } elseif (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
        $file  = $_FILES['foto_bukti'];
        $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allow = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allow)) {
            $_SESSION['pesan_error'] = 'Format foto harus JPG, PNG, atau WEBP.';
            redirect('index.php?mod=siswa_portal&act=permohonan');
            return;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['pesan_error'] = 'Ukuran foto maksimal 5MB.';
            redirect('index.php?mod=siswa_portal&act=permohonan');
            return;
        }

        $dir = __DIR__ . '/../../public/uploads/permohonan/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $foto_bukti = 'perm_' . $s['id_siswa'] . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . $foto_bukti)) {
            $_SESSION['pesan_error'] = 'Gagal menyimpan foto bukti.';
            redirect('index.php?mod=siswa_portal&act=permohonan');
            return;
        }
    } elseif ($jenis_izin === 'Sakit') {
        // Sakit wajib melampirkan bukti foto (baik upload atau live camera)
        $_SESSION['pesan_error'] = 'Foto bukti surat dokter/keterangan wajib dilampirkan untuk permohonan Sakit.';
        redirect('index.php?mod=siswa_portal&act=permohonan');
        return;
    }

    try {
        PermohonanAbsensiModel::ajukan($pdo, [
            'id_siswa'      => $s['id_siswa'],
            'jenis_absensi' => $jenis_absensi,
            'jenis_izin'    => $jenis_izin,
            'tanggal'       => $tanggal,
            'id_mapel'      => $id_mapel,
            'id_kelas'      => $id_kelas,
            'keterangan'    => $keterangan,
            'foto_bukti'    => $foto_bukti,
        ]);

        $_SESSION['pesan_sukses'] = "Permohonan <b>{$jenis_izin}</b> berhasil diajukan untuk tanggal " . date('d/m/Y', strtotime($tanggal)) . ". Menunggu verifikasi Guru Piket / Wali Kelas.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = 'Gagal menyimpan permohonan: ' . $e->getMessage();
    }

    redirect('index.php?mod=siswa_portal&act=permohonan');
}

// -----------------------------------------------------------------------
// 10. UJIAN ONLINE CBT SISWA
// -----------------------------------------------------------------------
function siswa_portal_cbt(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $cbt_data = SiswaPortalModel::getCbtListSiswa($pdo, $s['id_siswa'], $s['id_ta']);
    $kelas    = SiswaPortalModel::getKelasSiswa($pdo, $s['id_siswa'], $s['id_ta']);

    $aktif     = $cbt_data['aktif'];
    $mendatang = $cbt_data['mendatang'];
    $selesai   = $cbt_data['selesai'];

    include __DIR__ . '/../views/siswa_cbt_list.php';
}

/**
 * Halaman Konfirmasi Data Peserta & Masukkan Token Ujian (ANBK Style).
 */
function siswa_portal_cbt_konfirmasi(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $id_peserta = (int)($_GET['id_peserta'] ?? 0);
    $id_jadwal  = (int)($_GET['id_jadwal'] ?? 0);

    if ($id_peserta <= 0 && $id_jadwal > 0) {
        $st = $pdo->prepare("SELECT id_peserta FROM cbt_peserta WHERE id_jadwal = ? AND id_siswa = ?");
        $st->execute([$id_jadwal, $s['id_siswa']]);
        $id_peserta = (int)$st->fetchColumn();
    }

    if ($id_peserta <= 0) {
        $_SESSION['pesan_error'] = "Anda tidak terdaftar sebagai peserta pada ujian ini.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    $info = SiswaPortalModel::getCbtKonfirmasiData($pdo, $id_peserta, $s['id_siswa']);

    if (!$info) {
        $_SESSION['pesan_error'] = "Data peserta atau jadwal ujian tidak ditemukan.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    if ($info['status_peserta'] === 'selesai') {
        $_SESSION['pesan_sukses'] = "Anda sudah menyelesaikan ujian ini.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    // Jika POST: Validasi Token & Mulai Ujian
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input_token   = strtoupper(trim($_POST['token'] ?? ''));
        $expected_pin  = strtoupper(trim($info['pin_proktor'] ?? ''));
        $peserta_token = strtoupper(trim($info['token'] ?? ''));

        $is_valid = false;
        if (!empty($expected_pin) && $input_token === $expected_pin) {
            $is_valid = true;
        }
        if (!empty($peserta_token) && $input_token === $peserta_token) {
            $is_valid = true;
        }
        if (empty($expected_pin) && empty($peserta_token)) {
            $is_valid = true;
        }

        if (!$is_valid) {
            $_SESSION['pesan_error'] = "Token yang Anda masukkan salah. Silakan periksa Token Ujian Anda atau tanyakan PIN Proktor kepada Pengawas.";
            redirect(BASE_URL . "siswa_portal/cbt_konfirmasi?id_peserta=$id_peserta");
            return;
        }

        // Simpan token yang diisi siswa
        if (!empty($input_token)) {
            $pdo->prepare("UPDATE cbt_peserta SET token = ? WHERE id_peserta = ?")->execute([$input_token, $id_peserta]);
        }

        // Langsung arahkan ke ruang pengerjaan soal
        redirect(BASE_URL . "siswa_portal/cbt_room?id_peserta=$id_peserta");
        return;
    }

    $title = "Konfirmasi Peserta - " . ($info['nama_ujian'] ?? 'Asesmen CBT');
    include __DIR__ . '/../views/siswa_cbt_konfirmasi.php';
}

/**
 * Ruang pengerjaan ujian CBT siswa.
 */
function siswa_portal_cbt_room(): void
{
    global $pdo;
    $s = _siswa_portal_guard();

    $id_peserta = (int)($_GET['id_peserta'] ?? 0);
    $id_jadwal  = (int)($_GET['id_jadwal'] ?? 0);

    // Cari id_peserta jika yang dikirimkan adalah id_jadwal
    if ($id_peserta <= 0 && $id_jadwal > 0) {
        $st = $pdo->prepare("SELECT id_peserta FROM cbt_peserta WHERE id_jadwal = ? AND id_siswa = ?");
        $st->execute([$id_jadwal, $s['id_siswa']]);
        $id_peserta = (int)$st->fetchColumn();
    }

    if ($id_peserta <= 0) {
        $_SESSION['pesan_error'] = "Anda tidak terdaftar sebagai peserta pada ujian ini.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    $session = SiswaPortalModel::getCbtSession($pdo, $id_peserta, $s['id_siswa']);

    if (!$session) {
        $_SESSION['pesan_error'] = "Data sesi ujian tidak ditemukan atau akses ditolak.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    // Jika status ujian sudah selesai, arahkan kembali
    if ($session['status'] === 'selesai') {
        $_SESSION['pesan_sukses'] = "Anda sudah menyelesaikan ujian ini.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    // Jika waktu habis
    if ($session['sisa_detik'] <= 0) {
        SiswaPortalModel::finishCbtExam($pdo, $id_peserta, $s['id_siswa']);
        $_SESSION['pesan_error'] = "Waktu pengerjaan ujian telah berakhir. Jawaban Anda telah tersimpan otomatis.";
        redirect(BASE_URL . 'siswa_portal/cbt');
        return;
    }

    include __DIR__ . '/../views/siswa_cbt_room.php';
}

/**
 * Simpan butir jawaban siswa secara realtime via AJAX.
 */
function siswa_portal_cbt_save_jawaban(): void
{
    global $pdo;
    header('Content-Type: application/json');
    $s = _siswa_portal_guard();

    $id_peserta    = (int)($_POST['id_peserta'] ?? 0);
    $id_soal       = (int)($_POST['id_soal'] ?? 0);
    $id_jadwal     = (int)($_POST['id_jadwal'] ?? 0);
    $jawaban_pg    = !empty($_POST['jawaban_pg']) ? trim($_POST['jawaban_pg']) : null;
    $jawaban_essay = !empty($_POST['jawaban_essay']) ? trim($_POST['jawaban_essay']) : null;
    $is_ragu       = (int)($_POST['is_ragu'] ?? 0);

    // Validasi peserta milik siswa
    $chk = $pdo->prepare("SELECT id_peserta, status FROM cbt_peserta WHERE id_peserta = ? AND id_siswa = ?");
    $chk->execute([$id_peserta, $s['id_siswa']]);
    $p = $chk->fetch(PDO::FETCH_ASSOC);

    if (!$p || $p['status'] === 'selesai') {
        echo json_encode(['status' => 'error', 'message' => 'Sesi ujian telah selesai atau tidak valid.']);
        exit;
    }

    $ok = SiswaPortalModel::saveCbtJawaban($pdo, $id_peserta, $id_soal, $id_jadwal, $jawaban_pg, $jawaban_essay, $is_ragu);

    echo json_encode([
        'status'  => $ok ? 'ok' : 'error',
        'id_soal' => $id_soal,
        'saved_at'=> date('H:i:s')
    ]);
    exit;
}

/**
 * Konfirmasi selesai ujian & hitung nilai instan.
 */
function siswa_portal_cbt_selesai(): void
{
    global $pdo;
    header('Content-Type: application/json');
    $s = _siswa_portal_guard();

    $id_peserta = (int)($_POST['id_peserta'] ?? 0);

    $result = SiswaPortalModel::finishCbtExam($pdo, $id_peserta, $s['id_siswa']);
    echo json_encode($result);
    exit;
}




