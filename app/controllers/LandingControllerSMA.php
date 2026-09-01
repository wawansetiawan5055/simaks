<?php
// LandingControllerSMA.php – Controller Landing Page SMA Plus Al-Manshuriyah

/**
 * Helper untuk mengambil data common landing (navbar, config, programs, dll)
 */
function get_landing_common_data($pdo)
{
    $config = require '../config/app.php';

    // Ambil setting dari database
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM app_settings");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $config[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) {
    }

    // Identitas Sekolah
    $identitas = [];
    try {
        $stmt = $pdo->query("SELECT * FROM sekolah LIMIT 1");
        $identitas = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        $identitas = [
            'nama_sekolah' => $config['nama_sekolah'] ?? 'SMA Plus Al-Manshuriyah',
            'alamat' => $config['alamat'] ?? '',
            'telepon' => $config['telepon'] ?? '',
            'email' => $config['email'] ?? ''
        ];
    }

    // Program unggulan (untuk navbar & home)
    $programs = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_programs WHERE is_active=1 ORDER BY order_display ASC");
        $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // Tautan Penting
    $tautan_penting = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_links WHERE is_active=1 ORDER BY display_order ASC");
        $tautan_penting = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // Quotes
    $quotes = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_quotes WHERE is_active=1 ORDER BY id DESC");
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    return [
        'config' => $config,
        'identitas' => $identitas,
        'programs' => $programs,
        'tautan_penting' => $tautan_penting,
        'quotes' => $quotes
    ];
}

/**
 * Homepage utama landing page
 */
function landing_sma_index($pdo)
{
    $common = get_landing_common_data($pdo);
    $config = $common['config'];
    $identitas = $common['identitas'];
    $programs = $common['programs'];
    $tautan_penting = $common['tautan_penting'];
    $quotes = $common['quotes'];

    // Cek landing page aktif
    if (!empty($config['landing_page_enabled']) && $config['landing_page_enabled'] != '1') {
        header('Location: index.php?mod=auth&act=login');
        exit;
    }

    // Hero Slider
    $hero_sliders = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_gallery WHERE is_slider=1 AND is_active=1 ORDER BY display_order ASC");
        $hero_sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }


    // Ekstrakurikuler
    $ekskul = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_ekstrakurikuler WHERE is_active=1 ORDER BY display_order ASC LIMIT 5");
        $ekskul = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // Gallery
    $gallery = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_gallery WHERE is_active=1 ORDER BY display_order ASC LIMIT 9");
        $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // Video
    $videos = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_video WHERE is_active=1 ORDER BY display_order ASC LIMIT 3");
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // Informasi
    $informasi = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_informasi WHERE is_active=1 ORDER BY tanggal_publikasi DESC LIMIT 5");
        $informasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // GTK preview – ambil dari tabel guru SIMAKS (tanpa NIK)
    $gtk = [];
    try {
        $stmt = $pdo->query("
            SELECT g.id_guru, g.nama, g.jk, g.status_kepegawaian,
                   pg.jabatan, pg.bidang_studi, pg.pendidikan_terakhir,
                   pg.foto, pg.display_order
            FROM guru g
            LEFT JOIN profil_guru pg ON g.id_guru = pg.id_guru
            WHERE g.status = 'Aktif'
            ORDER BY COALESCE(pg.display_order,999), g.nama ASC
            LIMIT 4
        ");
        $gtk = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback ke landing_guru_profil
        try {
            $stmt = $pdo->query("SELECT * FROM landing_guru_profil WHERE is_display=1 ORDER BY display_order ASC LIMIT 4");
            $gtk = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e2) {
        }
    }

    // Siswa preview – dari tabel siswa SIMAKS (tanpa NIK)
    $siswa_preview = [];
    try {
        $stmt = $pdo->query("
            SELECT s.id_siswa, s.nama, s.jk, s.status_aktif,
                   k.nama_kelas AS kelas
            FROM siswa s
            LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.status_penempatan = 'Aktif'
            LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas
            WHERE s.status_aktif = 'Aktif'
            ORDER BY s.nama ASC
            LIMIT 8
        ");
        $siswa_preview = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback ke landing_siswa_profil
        try {
            $stmt = $pdo->query("SELECT * FROM landing_siswa_profil WHERE is_display=1 ORDER BY nama ASC LIMIT 8");
            $siswa_preview = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e2) {
        }
    }

    // Stats counter
    $stats = [];
    try {
        $r = $pdo->query("
            SELECT COUNT(*) 
            FROM penempatan_siswa ps 
            JOIN tahun_ajaran ta ON ps.id_ta = ta.id_ta 
            JOIN siswa s ON ps.id_siswa = s.id_siswa
            WHERE ps.status_penempatan = 'Aktif' 
              AND ta.status = 'Aktif'
              AND s.status_aktif = 'Aktif'
        ")->fetchColumn();
        $stats['total_siswa'] = (int) $r;
    } catch (Exception $e) {
        $stats['total_siswa'] = 0;
    }
    try {
        $r = $pdo->query("SELECT COUNT(*) FROM guru WHERE status='Aktif'")->fetchColumn();
        $stats['total_gtk'] = (int) $r;
    } catch (Exception $e) {
        $stats['total_gtk'] = 0;
    }
    try {
        $r = $pdo->query("SELECT COUNT(*) FROM landing_programs")->fetchColumn();
        $stats['total_programs'] = (int) $r;
    } catch (Exception $e) {
        $stats['total_programs'] = 2; // Default
    }


    $data = [
        'config' => $config,
        'identitas' => $identitas,
        'programs' => $programs,
        'ekskul' => $ekskul,
        'gallery' => $gallery,
        'videos' => $videos,
        'informasi' => $informasi,
        'gtk' => $gtk,
        'siswa_preview' => $siswa_preview,
        'stats' => $stats,
        'hero_sliders' => $hero_sliders,
        'tautan_penting' => $tautan_penting,
        'quotes' => $quotes
    ];

    require '../app/views/landing_sma.php';
}

/**
 * Profil GTK – data dari tabel guru SIMAKS (tanpa NIK)
 */
function guru_list($pdo)
{
    $search = trim($_GET['search'] ?? '');

    $sql = "
        SELECT 
            g.id_guru, g.nama, g.jk, g.status_kepegawaian, g.kode_guru,
            g.nik, g.nuptk, g.tempat_lahir, g.tanggal_lahir,
            pg.pendidikan_terakhir, pg.file_ijazah_s1 AS sertifikasi, 
            pg.email_pribadi AS email, pg.no_hp,
            p.foto,
            (SELECT GROUP_CONCAT(pj.jenis_jabatan SEPARATOR ', ') FROM penugasan_jabatan pj WHERE pj.id_guru = g.id_guru) AS jabatan,
            (SELECT GROUP_CONCAT(DISTINCT m.nama_mapel SEPARATOR ', ') FROM guru_mapel gm JOIN mapel m ON gm.id_mapel = m.id_mapel WHERE gm.id_guru = g.id_guru) AS bidang_studi
        FROM guru g
        LEFT JOIN profil_guru pg ON g.id_guru = pg.id_guru
        LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
        WHERE g.status = 'Aktif'
    ";

    $params = [];
    if ($search) {
        $sql .= " AND (g.nama LIKE ? OR 
                       EXISTS(SELECT 1 FROM penugasan_jabatan pj WHERE pj.id_guru = g.id_guru AND pj.jenis_jabatan LIKE ?) OR 
                       EXISTS(SELECT 1 FROM guru_mapel gm JOIN mapel m ON gm.id_mapel = m.id_mapel WHERE gm.id_guru = g.id_guru AND m.nama_mapel LIKE ?))";
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    $sql .= " ORDER BY g.nama ASC";

    $guru = [];
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $guru = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['guru' => $guru, 'search' => $search]);
    require '../app/views/landing/guru_list.php';
}

/**
 * Daftar siswa – data dari tabel siswa SIMAKS (tanpa NIK)
 */
function siswa_list($pdo)
{
    $kelas_filter = trim($_GET['kelas'] ?? '');
    $search = trim($_GET['search'] ?? '');
    $ta_filter = trim($_GET['ta'] ?? '2025/2026'); // Default ke tapel terbaru atau 2025/2026 jika tidak ada input

    $sql = "
        SELECT s.id_siswa, s.nama, s.nisn, s.nipd, s.jk,
               s.tempat_lahir, s.status_aktif,
               MAX(k.nama_kelas) AS kelas,
               MAX(ta.nama_ta) AS nama_ta
        FROM siswa s
        INNER JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.status_penempatan = 'Aktif'
        INNER JOIN kelas k ON ps.id_kelas = k.id_kelas
        INNER JOIN tahun_ajaran ta ON ps.id_ta = ta.id_ta
        WHERE s.status_aktif = 'Aktif' 
          AND (ta.nama_ta LIKE '2023/2024%' OR ta.nama_ta LIKE '2024/2025%' OR ta.nama_ta LIKE '2025/2026%')
    ";

    $params = [];
    if ($kelas_filter) {
        $sql .= " AND k.nama_kelas = ?";
        $params[] = $kelas_filter;
    }
    if ($ta_filter) {
        $sql .= " AND ta.nama_ta LIKE ?";
        $params[] = $ta_filter . '%';
    }
    if ($search) {
        $sql .= " AND s.nama LIKE ?";
        $params[] = "%$search%";
    }
    $sql .= " GROUP BY s.id_siswa ORDER BY MAX(ta.nama_ta) DESC, MAX(k.nama_kelas) ASC, s.nama ASC";

    $siswa = [];
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback ke landing_siswa_profil
        try {
            $stmt = $pdo->query("SELECT * FROM landing_siswa_profil WHERE is_display=1 ORDER BY nama ASC");
            $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e2) {
        }
    }

    // Daftar kelas untuk filter dropdown
    $kelas_list = [];
    try {
        $stmt = $pdo->query("SELECT DISTINCT k.nama_kelas FROM kelas k JOIN penempatan_siswa ps ON k.id_kelas=ps.id_kelas WHERE ps.status_penempatan='Aktif' ORDER BY k.nama_kelas");
        $kelas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    // Opsi Tahun Ajaran
    $ta_list = ['2025/2026', '2024/2025', '2023/2024'];

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, [
        'siswa' => $siswa,
        'kelas_list' => $kelas_list,
        'kelas_filter' => $kelas_filter,
        'search' => $search,
        'ta_filter' => $ta_filter,
        'ta_list' => $ta_list
    ]);
    require '../app/views/landing/siswa_list.php';
}

/**
 * Ekstrakurikuler list
 */
function ekstrakurikuler_list($pdo)
{
    $ekskul = [];
    try {
        $stmt = $pdo->query("SELECT * FROM landing_ekstrakurikuler WHERE is_active=1 ORDER BY display_order ASC");
        $ekskul = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['ekskul' => $ekskul]);
    require '../app/views/landing/ekstrakurikuler_list.php';
}

/**
 * Detail ekstrakurikuler
 */
function ekstrakurikuler_detail($pdo)
{
    $id = (int) ($_GET['id'] ?? 0);
    $ekskul = null;
    try {
        $stmt = $pdo->prepare("SELECT * FROM landing_ekstrakurikuler WHERE id=?");
        $stmt->execute([$id]);
        $ekskul = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    if (!$ekskul) {
        header('Location: index.php?mod=landing_sma&act=ekstrakurikuler_list');
        exit;
    }
    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['ekskul' => $ekskul]);
    require '../app/views/landing/ekstrakurikuler_detail.php';
}

/**
 * Video list
 */
function video_list($pdo)
{
    $kategori = trim($_GET['kategori'] ?? '');
    $sql = "SELECT * FROM landing_video WHERE is_active=1";
    $params = [];
    if ($kategori) {
        $sql .= " AND kategori=?";
        $params[] = $kategori;
    }
    $sql .= " ORDER BY display_order ASC";

    $videos = [];
    $kategori_list = [];
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt2 = $pdo->query("SELECT DISTINCT kategori FROM landing_video WHERE is_active=1 ORDER BY kategori");
        $kategori_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['videos' => $videos, 'kategori_list' => $kategori_list, 'kategori_filter' => $kategori]);
    require '../app/views/landing/video_list.php';
}

/**
 * Gallery list
 */
function gallery_list($pdo)
{
    $kategori = trim($_GET['kategori'] ?? '');
    $sql = "SELECT * FROM landing_gallery WHERE is_active=1";
    $params = [];
    if ($kategori) {
        $sql .= " AND category=?";
        $params[] = $kategori;
    }
    $sql .= " ORDER BY display_order ASC";

    $gallery = [];
    $kategori_list = [];
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $pdo->query("SELECT DISTINCT category FROM landing_gallery WHERE is_active=1 AND category != '' ORDER BY category");
        $kategori_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['gallery' => $gallery, 'kategori_list' => $kategori_list, 'kategori_filter' => $kategori]);
    require '../app/views/landing/gallery_list.php';
}

/**
 * Informasi / pengumuman list
 */
function informasi_list($pdo)
{
    $kategori = trim($_GET['kategori'] ?? '');
    $sql = "SELECT * FROM landing_informasi WHERE is_active=1";
    $params = [];
    if ($kategori) {
        $sql .= " AND kategori=?";
        $params[] = $kategori;
    }
    $sql .= " ORDER BY is_featured DESC, tanggal_publikasi DESC";

    $informasi = [];
    $kategori_list = [];
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $informasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt2 = $pdo->query("SELECT DISTINCT kategori FROM landing_informasi WHERE is_active=1 ORDER BY kategori");
        $kategori_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['informasi' => $informasi, 'kategori_list' => $kategori_list, 'kategori_filter' => $kategori]);
    require '../app/views/landing/informasi_list.php';
}

/**
 * Detail informasi
 */
function informasi_detail($pdo)
{
    $id = (int) ($_GET['id'] ?? 0);
    $info = null;
    try {
        $stmt = $pdo->prepare("SELECT * FROM landing_informasi WHERE id=?");
        $stmt->execute([$id]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    if (!$info) {
        header('Location: index.php?mod=landing_sma&act=informasi_list');
        exit;
    }
    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['info' => $info]);
    require '../app/views/landing/informasi_detail.php';
}

/**
 * Halaman Profil Sekolah terintegrasi (Identitas, Sejarah, Visi Misi, Kepala Sekolah, Statistik)
 */
function profil_sekolah($pdo)
{
    $stats = [
        'total_siswa' => 0,
        'total_guru' => 0
    ];

    try {
        $stmt = $pdo->query("
            SELECT COUNT(*) AS total 
            FROM penempatan_siswa ps 
            JOIN tahun_ajaran ta ON ps.id_ta = ta.id_ta 
            JOIN siswa s ON ps.id_siswa = s.id_siswa
            WHERE ps.status_penempatan = 'Aktif' 
              AND ta.status = 'Aktif'
              AND s.status_aktif = 'Aktif'
        ");
        $stats['total_siswa'] = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM guru WHERE status = 'Aktif'");
        $stats['total_guru'] = $stmt->fetchColumn();
    } catch (Exception $e) {
    }

    $identitas = [];
    try {
        $stmt = $pdo->query("SELECT * FROM profil_sekolah LIMIT 1");
        $identitas = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
    }

    $common = get_landing_common_data($pdo);
    $data = array_merge($common, ['stats' => $stats, 'identitas' => $identitas]);
    require '../app/views/landing/profil_sekolah.php';
}

/**
 * Detail Program Unggulan
 */
function program_detail($pdo)
{
    $id = (int) ($_GET['id'] ?? 0);
    $program = null;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM landing_programs WHERE id = ?");
        $stmt->execute([$id]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    if (!$program) {
        header('Location: index.php?mod=landing_sma#program');
        exit;
    }

    $common = get_landing_common_data($pdo);

    // Sync Data dari Modul Administrasi (Jika tertaut)
    $pembina = [];
    $peserta = [];
    $agenda = [];
    $jadwal = "";

    if (!empty($program['ref_module']) && !empty($program['ref_id'])) {
        $ref_id = $program['ref_id'];
        
        switch ($program['ref_module']) {
            case 'tahfidz':
                // Data Dasar & Jadwal
                $stmt = $pdo->prepare("SELECT t.*, g.nama as nama_guru, p.foto as foto_guru 
                                     FROM tahfidz t 
                                     LEFT JOIN guru g ON t.id_guru_pembina = g.id_guru 
                                     LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
                                     WHERE t.id_tahfidz = ?");
                $stmt->execute([$ref_id]);
                $op = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($op) {
                    $pembina = ['nama' => $op['nama_guru'], 'foto' => $op['foto_guru'], 'jabatan' => 'Pembina Tahfidz'];
                    $jadwal = ($op['hari'] ?? '') . " (" . ($op['jam'] ?? '') . ")";
                    
                    // Agenda
                    $agenda = $pdo->prepare("SELECT nama_agenda FROM tahfidz_agenda WHERE id_tahfidz = ? ORDER BY id_agenda ASC");
                    $agenda->execute([$ref_id]);
                    $agenda = $agenda->fetchAll(PDO::FETCH_COLUMN);

                    // Peserta
                    $stmt_p = $pdo->prepare("SELECT s.nama FROM anggota_tahfidz a JOIN siswa s ON a.id_siswa = s.id_siswa WHERE a.id_tahfidz = ? LIMIT 20");
                    $stmt_p->execute([$ref_id]);
                    $peserta = $stmt_p->fetchAll(PDO::FETCH_COLUMN);
                }
                break;

            case 'ekskul':
                $stmt = $pdo->prepare("SELECT e.*, g.nama as nama_guru, p.foto as foto_guru 
                                     FROM ekstrakurikuler e 
                                     LEFT JOIN guru g ON e.id_guru_pembina = g.id_guru 
                                     LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
                                     WHERE e.id_ekskul = ?");
                $stmt->execute([$ref_id]);
                $op = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($op) {
                    $pembina = ['nama' => $op['nama_guru'], 'foto' => $op['foto_guru'], 'jabatan' => 'Pembina ' . $op['nama_ekskul']];
                    $jadwal = ($op['hari'] ?? '') . " (" . substr($op['jam_mulai'],0,5) . " - " . substr($op['jam_selesai'],0,5) . ")";
                    
                    // Agenda (Proker)
                    $agenda = $pdo->prepare("SELECT nama_kegiatan FROM ekskul_program_kerja WHERE id_ekskul = ? ORDER BY id_proker ASC");
                    $agenda->execute([$ref_id]);
                    $agenda = $agenda->fetchAll(PDO::FETCH_COLUMN);

                    // Peserta
                    $stmt_p = $pdo->prepare("SELECT s.nama FROM anggota_ekskul a JOIN siswa s ON a.id_siswa = s.id_siswa WHERE a.id_ekskul = ? LIMIT 20");
                    $stmt_p->execute([$ref_id]);
                    $peserta = $stmt_p->fetchAll(PDO::FETCH_COLUMN);
                }
                break;

            case 'wirausaha':
                $stmt = $pdo->prepare("SELECT w.*, g.nama as nama_guru, p.foto as foto_guru 
                                     FROM kewirausahaan w 
                                     LEFT JOIN guru g ON w.id_guru_pembina = g.id_guru 
                                     LEFT JOIN pengguna p ON g.id_pengguna = p.id_pengguna
                                     WHERE w.id_kewirausahaan = ?");
                $stmt->execute([$ref_id]);
                $op = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($op) {
                    $pembina = ['nama' => $op['nama_guru'], 'foto' => $op['foto_guru'], 'jabatan' => 'Pembina Kewirausahaan'];
                    $jadwal = ($op['hari'] ?? '') . " (" . ($op['jam'] ?? '') . ")";
                    
                    // Agenda
                    $agenda = $pdo->prepare("SELECT nama_kegiatan FROM kewirausahaan_agenda WHERE id_kewirausahaan = ? ORDER BY id_agenda ASC");
                    $agenda->execute([$ref_id]);
                    $agenda = $agenda->fetchAll(PDO::FETCH_COLUMN);

                    // Peserta (Kewirausahaan uses anggota_kewirausahaan?)
                    $stmt_p = $pdo->prepare("SELECT s.nama FROM anggota_kewirausahaan a JOIN siswa s ON a.id_siswa = s.id_siswa WHERE a.id_kewirausahaan = ? LIMIT 20");
                    $stmt_p->execute([$ref_id]);
                    $peserta = $stmt_p->fetchAll(PDO::FETCH_COLUMN);
                }
                break;
        }
    }
    
    // Siapkan data untuk view detail program
    $data = array_merge($common, [
        'program' => $program,
        'pembina' => $pembina,
        'peserta' => $peserta,
        'agenda'  => $agenda,
        'jadwal'  => $jadwal,
        'galeri'  => [] 
    ]);

    require '../app/views/landing/program_detail.php';
}


/**
 * Form PPDB Public (Tidak perlu login)
 */
function ppdb_public_form($pdo)
{
    $config = require '../config/app.php';

    if (!empty($config['ppdb']['enabled']) && !$config['ppdb']['enabled']) {
        $_SESSION['pesan_error'] = 'Pendaftaran PPDB belum dibuka.';
        redirect(BASE_URL . 'landing');
        return;
    }

    $data = [
        'config' => $config,
    ];

    require '../app/views/ppdb_public_form.php';
}

/**
 * Save PPDB dari Form Public
 */
function ppdb_public_save($pdo)
{
    $config = require '../config/app.php';

    if (!empty($config['ppdb']['enabled']) && !$config['ppdb']['enabled']) {
        echo json_encode(['success' => false, 'message' => 'Pendaftaran PPDB belum dibuka.']);
        return;
    }

    try {
        $year = date('Y');
        $stmt_count = $pdo->query("SELECT COUNT(*) FROM ppdb_pendaftaran WHERE YEAR(created_at) = $year");
        $count = $stmt_count->fetchColumn() + 1;
        $no_pendaftaran = "PPDB{$year}" . str_pad($count, 4, '0', STR_PAD_LEFT);

        $foto_siswa = handle_file_upload('foto_siswa', 'uploads/ppdb/');
        $foto_kk = handle_file_upload('foto_kk', 'uploads/ppdb/');
        $foto_akta = handle_file_upload('foto_akta', 'uploads/ppdb/');
        $foto_ijazah = handle_file_upload('foto_ijazah', 'uploads/ppdb/');
        $foto_raport = handle_file_upload('foto_raport', 'uploads/ppdb/');

        $sql = "INSERT INTO ppdb_pendaftaran (
            no_pendaftaran, nama_lengkap, nik, nisn, tempat_lahir, tanggal_lahir,
            jenis_kelamin, agama, alamat, rt, rw, kelurahan, kecamatan, kota, provinsi,
            kode_pos, no_hp_siswa, email_siswa,
            nama_ayah, pekerjaan_ayah, penghasilan_ayah, no_hp_ayah,
            nama_ibu, pekerjaan_ibu, penghasilan_ibu, no_hp_ibu,
            nama_wali, pekerjaan_wali, no_hp_wali,
            asal_sekolah, alamat_sekolah, npsn_sekolah,
            foto_siswa, foto_kk, foto_akta, foto_ijazah, foto_raport,
            jalur_pendaftaran, status
        ) VALUES (
            :no_pendaftaran, :nama_lengkap, :nik, :nisn, :tempat_lahir, :tanggal_lahir,
            :jenis_kelamin, :agama, :alamat, :rt, :rw, :kelurahan, :kecamatan, :kota, :provinsi,
            :kode_pos, :no_hp_siswa, :email_siswa,
            :nama_ayah, :pekerjaan_ayah, :penghasilan_ayah, :no_hp_ayah,
            :nama_ibu, :pekerjaan_ibu, :penghasilan_ibu, :no_hp_ibu,
            :nama_wali, :pekerjaan_wali, :no_hp_wali,
            :asal_sekolah, :alamat_sekolah, :npsn_sekolah,
            :foto_siswa, :foto_kk, :foto_akta, :foto_ijazah, :foto_raport,
            :jalur_pendaftaran, 'pending'
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':no_pendaftaran' => $no_pendaftaran,
            ':nama_lengkap' => $_POST['nama_lengkap'] ?? '',
            ':nik' => $_POST['nik'] ?? '',
            ':nisn' => $_POST['nisn'] ?? '',
            ':tempat_lahir' => $_POST['tempat_lahir'] ?? '',
            ':tanggal_lahir' => $_POST['tanggal_lahir'] ?? null,
            ':jenis_kelamin' => $_POST['jenis_kelamin'] ?? '',
            ':agama' => $_POST['agama'] ?? '',
            ':alamat' => $_POST['alamat'] ?? '',
            ':rt' => $_POST['rt'] ?? '',
            ':rw' => $_POST['rw'] ?? '',
            ':kelurahan' => $_POST['kelurahan'] ?? '',
            ':kecamatan' => $_POST['kecamatan'] ?? '',
            ':kota' => $_POST['kota'] ?? '',
            ':provinsi' => $_POST['provinsi'] ?? '',
            ':kode_pos' => $_POST['kode_pos'] ?? '',
            ':no_hp_siswa' => $_POST['no_hp_siswa'] ?? '',
            ':email_siswa' => $_POST['email_siswa'] ?? '',
            ':nama_ayah' => $_POST['nama_ayah'] ?? '',
            ':pekerjaan_ayah' => $_POST['pekerjaan_ayah'] ?? '',
            ':penghasilan_ayah' => $_POST['penghasilan_ayah'] ?? '',
            ':no_hp_ayah' => $_POST['no_hp_ayah'] ?? '',
            ':nama_ibu' => $_POST['nama_ibu'] ?? '',
            ':pekerjaan_ibu' => $_POST['pekerjaan_ibu'] ?? '',
            ':penghasilan_ibu' => $_POST['penghasilan_ibu'] ?? '',
            ':no_hp_ibu' => $_POST['no_hp_ibu'] ?? '',
            ':nama_wali' => $_POST['nama_wali'] ?? '',
            ':pekerjaan_wali' => $_POST['pekerjaan_wali'] ?? '',
            ':no_hp_wali' => $_POST['no_hp_wali'] ?? '',
            ':asal_sekolah' => $_POST['asal_sekolah'] ?? '',
            ':alamat_sekolah' => $_POST['alamat_sekolah'] ?? '',
            ':npsn_sekolah' => $_POST['npsn_sekolah'] ?? '',
            ':foto_siswa' => $foto_siswa,
            ':foto_kk' => $foto_kk,
            ':foto_akta' => $foto_akta,
            ':foto_ijazah' => $foto_ijazah,
            ':foto_raport' => $foto_raport,
            ':jalur_pendaftaran' => $_POST['jalur_pendaftaran'] ?? 'Zonasi',
        ]);

        $_SESSION['pesan_sukses'] = "Pendaftaran berhasil! Nomor pendaftaran Anda: <strong>{$no_pendaftaran}</strong>. Silakan simpan nomor ini untuk pengecekan status.";
        $_SESSION['ppdb_no_pendaftaran'] = $no_pendaftaran;

        redirect(BASE_URL . 'landing/ppdb_success');

    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        redirect(BASE_URL . 'landing/ppdb_form');
    }
}

if (!function_exists('handle_file_upload')) {
    function handle_file_upload($field_name, $upload_dir)
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] == UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $upload_path = __DIR__ . '/../../public/' . $upload_dir;
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $file = $_FILES[$field_name];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $destination = $upload_path . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $upload_dir . $filename;
        }

        return null;
    }
}

function ppdb_success_page($pdo)
{
    $no_pendaftaran = $_SESSION['ppdb_no_pendaftaran'] ?? null;
    $data = [
        'no_pendaftaran' => $no_pendaftaran,
    ];
    require '../app/views/ppdb_success.php';
}

function ppdb_check_status($pdo)
{
    $no_pendaftaran = $_GET['no'] ?? $_POST['no_pendaftaran'] ?? null;
    $result = null;

    if ($no_pendaftaran) {
        $stmt = $pdo->prepare("SELECT * FROM ppdb_pendaftaran WHERE no_pendaftaran = ?");
        $stmt->execute([$no_pendaftaran]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $data = [
        'result' => $result,
        'no_pendaftaran' => $no_pendaftaran,
    ];

    require '../app/views/ppdb_check_status.php';
}