<?php
/**
 * KalenderAkademikController.php
 * Controller for Academic Calendar management
 */

require_once __DIR__ . '/../models/KalenderAkademikModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';
require_once __DIR__ . '/../models/ProfilSekolahModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Manual include Dompdf if not autoloaded
if (!class_exists('Dompdf\Dompdf')) {
    if (file_exists(__DIR__ . '/../../dompdf_lib/dompdf/autoload.inc.php')) {
        require_once __DIR__ . '/../../dompdf_lib/dompdf/autoload.inc.php';
    }
}

use Dompdf\Dompdf;

function kalender_akademik_index($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    if (!check_access('kalender_akademik')) redirect('index.php');
    
    $id_ta = $_SESSION['id_ta_aktif'] ?? null;
    
    $ta_list = TahunAjaranModel::all($pdo);
    
    // Get filter parameters
    $filter_ta = $_GET['id_ta'] ?? $id_ta;
    $filter_kategori = $_GET['kategori'] ?? '';
    
    // Get events for selected TA
    $events = [];
    if ($filter_ta) {
        $events = KalenderAkademikModel::getAll($pdo, $filter_ta);
        if (!empty($filter_kategori)) {
            $filtered = [];
            foreach ($events as $e) {
                if ($e['kategori'] == $filter_kategori) {
                    $filtered[] = $e;
                }
            }
            $events = $filtered;
        }
    }
    
    $kategori_colors = KalenderAkademikModel::getCategoryColors($pdo);
    $kategori_list = KalenderAkademikModel::getCategories($pdo);
    
    // Check permissions for view logic
    $can_create = can_do($pdo, 'kalender_akademik', 'create');
    $can_update = can_do($pdo, 'kalender_akademik', 'update');
    $can_delete = can_do($pdo, 'kalender_akademik', 'delete');
    
    include __DIR__ . '/../views/kalender_akademik_index.php';
}

function kalender_akademik_save($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    
    $id_kalender = $_POST['id_kalender'] ?? null;
    
    // RBAC Check
    if ($id_kalender) {
        if (!can_do($pdo, 'kalender_akademik', 'update')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki akses untuk mengubah kegiatan.";
            redirect('index.php?mod=kalender_akademik');
        }
    } else {
        if (!can_do($pdo, 'kalender_akademik', 'create')) {
            $_SESSION['pesan_error'] = "Anda tidak memiliki akses untuk menambah kegiatan.";
            redirect('index.php?mod=kalender_akademik');
        }
    }
    
    try {
        $id_ta = $_POST['id_ta'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];

        // [VALIDASI] Cek apakah tanggal berada dalam rentang Tahun Ajaran yang dipilih
        $ta = TahunAjaranModel::find($pdo, $id_ta);
        if ($ta && !empty($ta['tanggal_mulai']) && !empty($ta['tanggal_selesai'])) {
            if ($tanggal_mulai < $ta['tanggal_mulai'] || $tanggal_selesai > $ta['tanggal_selesai']) {
                throw new Exception("Gagal menyimpan: Tanggal kegiatan (" . $tanggal_mulai . " s/d " . $tanggal_selesai . ") berada di luar rentang Tahun Ajaran " . htmlspecialchars($ta['nama_ta']) . " [" . $ta['tanggal_mulai'] . " s/d " . $ta['tanggal_selesai'] . "]. Silakan periksa kembali filter Tahun Ajaran di atas.");
            }
        }

        $data = [
            'id_kalender' => $id_kalender,
            'id_ta' => $id_ta,
            'judul_kegiatan' => $_POST['judul_kegiatan'],
            'deskripsi' => $_POST['deskripsi'] ?? null,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'kategori' => $_POST['kategori'],
            'warna' => $_POST['warna'] ?? null,
            'is_recurring' => isset($_POST['is_recurring']) ? 1 : 0,
            'recurring_type' => $_POST['recurring_type'] ?? null
        ];
        
        // Set default color based on category if not provided
        if (empty($data['warna'])) {
            $colors = KalenderAkademikModel::getCategoryColors($pdo);
            $data['warna'] = $colors[$data['kategori']] ?? '#3788d8';
        }
        
        KalenderAkademikModel::save($pdo, $data);
        $_SESSION['pesan_sukses'] = "Kegiatan berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    
    redirect('index.php?mod=kalender_akademik&id_ta=' . $_POST['id_ta']);
}

function kalender_akademik_delete($pdo)
{
    if (!is_logged_in()) redirect('index.php');
    if (!can_do($pdo, 'kalender_akademik', 'delete')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses untuk menghapus kegiatan.";
        redirect('index.php?mod=kalender_akademik');
    }
    
    $id = $_GET['id'] ?? null;
    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'];
    
    if ($id) {
        try {
            KalenderAkademikModel::delete($pdo, $id);
            $_SESSION['pesan_sukses'] = "Kegiatan berhasil dihapus.";
        } catch (Exception $e) {
            $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
        }
    }
    
    redirect('index.php?mod=kalender_akademik&id_ta=' . $id_ta);
}

function kalender_akademik_api($pdo)
{
    if (!is_logged_in()) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;
    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'];
    
    if (!$start || !$end) {
        echo json_encode(['error' => 'Missing parameters']);
        exit;
    }
    
    $events = KalenderAkademikModel::getByDateRange($pdo, $start, $end, $id_ta);
    
    // Format for FullCalendar with Sunday splitting
    $formatted_events = [];
    foreach ($events as $event) {
        $segments = DateHelper::splitDateRangeBySunday($event['tanggal_mulai'], $event['tanggal_selesai']);
        foreach ($segments as $seg) {
            $formatted_events[] = [
                'id' => $event['id_kalender'],
                'title' => $event['judul_kegiatan'],
                'start' => $seg['start'],
                'end' => date('Y-m-d', strtotime($seg['end'] . ' +1 day')), // FullCalendar end is exclusive
                'backgroundColor' => $event['warna'],
                'borderColor' => $event['warna'],
                'extendedProps' => [
                    'deskripsi' => $event['deskripsi'],
                    'kategori' => $event['kategori'],
                    'is_recurring' => $event['is_recurring']
                ]
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($formatted_events);
    exit;
}

function kalender_akademik_import_holidays($pdo) {
    if (!is_logged_in()) redirect('index.php');
    if (!can_do($pdo, 'kalender_akademik', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=kalender_akademik');
    }

    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'];
    // Fetch TA details to get years
    $stmt = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
    $stmt->execute([$id_ta]);
    $ta = $stmt->fetch();
    
    if (!$ta) redirect('index.php?mod=kalender_akademik');

    // Extract years from "2025/2026 Ganjil" or similar
    preg_match_all('/\d{4}/', $ta['nama_ta'], $matches);
    $years = array_unique($matches[0] ?? [date('Y')]);

    $count = 0;
    foreach ($years as $year) {
        $url = "https://dayoffapi.vercel.app/api?year=" . $year;
        $json = @file_get_contents($url);
        if ($json) {
            $holidays = json_decode($json, true);
            if (is_array($holidays)) {
                foreach ($holidays as $h) {
                    // Check if already exists
                    $stmtCheck = $pdo->prepare("SELECT id_kalender FROM kalender_akademik WHERE id_ta = ? AND judul_kegiatan = ? AND tanggal_mulai = ?");
                    $stmtCheck->execute([$id_ta, $h['keterangan'], $h['tanggal']]);
                    if (!$stmtCheck->fetch()) {
                        KalenderAkademikModel::save($pdo, [
                            'id_ta' => $id_ta,
                            'judul_kegiatan' => $h['keterangan'],
                            'deskripsi' => 'Libur Nasional',
                            'tanggal_mulai' => $h['tanggal'],
                            'tanggal_selesai' => $h['tanggal'],
                            'kategori' => 'Libur',
                            'warna' => '#dc3545'
                        ]);
                        $count++;
                    }
                }
            }
        }
    }

    $_SESSION['pesan_sukses'] = "Berhasil mengimpor $count hari libur.";
    redirect('index.php?mod=kalender_akademik&id_ta=' . $id_ta);
}

function kalender_akademik_export_pdf($pdo) {
    if (!is_logged_in()) redirect('index.php');
    
    $id_ta = $_GET['id_ta'] ?? $_SESSION['id_ta_aktif'];
    $raw_events = KalenderAkademikModel::getAll($pdo, $id_ta);
    
    // Process events for the split activity list
    $events = [];
    foreach ($raw_events as $ev) {
        if ($ev['tanggal_mulai'] != $ev['tanggal_selesai']) {
            $segments = DateHelper::splitDateRangeBySunday($ev['tanggal_mulai'], $ev['tanggal_selesai']);
            foreach ($segments as $seg) {
                $new_ev = $ev;
                $new_ev['tanggal_mulai'] = $seg['start'];
                $new_ev['tanggal_selesai'] = $seg['end'];
                $events[] = $new_ev;
            }
        } else {
            // Check if single day is Sunday
            if (date('w', strtotime($ev['tanggal_mulai'])) != 0) {
                $events[] = $ev;
            }
        }
    }

    // Still need raw events for the calendar grid coloring
    $eventsForGrid = $raw_events;
    
    // Fetch TA info
    $stmt = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
    $stmt->execute([$id_ta]);
    $ta = $stmt->fetch();

    $profil = ProfilSekolahModel::getProfil($pdo);
    
    // Group events by date (for grid highlighting)
    $eventsByDate = [];
    foreach ($eventsForGrid as $event) {
        $start = new DateTime($event['tanggal_mulai']);
        $end = new DateTime($event['tanggal_selesai']);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            // Skip Sunday for grid highlighting (so it stays red)
            if ($date->format('w') == 0) continue;

            if (!isset($eventsByDate[$dateStr])) {
                $eventsByDate[$dateStr] = [];
            }
            $eventsByDate[$dateStr][] = $event;
        }
    }

    // Determine relevant months from TA name (e.g. "2025/2026 Ganjil")
    preg_match_all('/\d{4}/', $ta['nama_ta'], $matches);
    $years = $matches[0] ?? [date('Y')];
    $startYear = $years[0];
    $endYear = $years[1] ?? ($startYear + 1);
    
    $months = [];
    if (stripos($ta['nama_ta'], 'Ganjil') !== false) {
        for ($i = 7; $i <= 12; $i++) $months[] = ['year' => $startYear, 'month' => $i];
    } elseif (stripos($ta['nama_ta'], 'Genap') !== false) {
        for ($i = 1; $i <= 6; $i++) $months[] = ['year' => $endYear, 'month' => $i];
    } else {
        // Full year: July to June of next year
        for ($i = 7; $i <= 12; $i++) $months[] = ['year' => $startYear, 'month' => $i];
        for ($i = 1; $i <= 6; $i++) $months[] = ['year' => $endYear, 'month' => $i];
    }

    // Headers for PDF Kop - Unified with other reports
    $kop = [
        'nama_yayasan' => $profil['nama_yayasan'] ?? 'YAYASAN TARBIYATUSHIBYAN INDONESIA',
        'nama_sekolah' => $profil['nama_sekolah'] ?? 'SIMAKS',
        'alamat' => $profil['alamat'] ?? 'Alamat Sekolah',
        'npsn' => $profil['npsn'] ?? 'NPSN',
        'logo' => !empty($profil['logo']) ? 'assets/img/' . $profil['logo'] : '',
        'nama_kepsek' => $profil['nama_kepala_sekolah'] ?? '.......................',
        'nip_kepsek' => $profil['nip_kepala_sekolah'] ?? ''
    ];

    // Determine school days (5 or 6)
    $isSixDays = true; // Default to 6
    $hasAnySchedule = $pdo->query("SELECT id_jam FROM jam_pelajaran LIMIT 1")->fetch();
    if ($hasAnySchedule) {
        $checkSabtu = $pdo->query("SELECT id_jam FROM jam_pelajaran WHERE hari_pelaksanaan = 'Sabtu' LIMIT 1")->fetch();
        if (!$checkSabtu) $isSixDays = false;
    }

    // Calculation for Effective Days (HBE) and Effective Weeks (ME)
    $nonEffectiveDates = [];
    $nonEffectiveKeywords = ['SAS', 'SAT', 'SAJ', 'TKA', 'ANBK', 'LIBUR', 'SUMATIF', 'UJIAN', 'REMIDIAL', 'CLASS MEETING', 'RAPOR', 'KENAIKAN', 'PEMBAGIAN', 'PSAT', 'PAS', 'PAT', 'PTS', 'AKHIR TAHUN', 'AKHIR JENJANG'];
    foreach ($raw_events as $ev) {
        $isNonEffective = false;
        if (strcasecmp($ev['kategori'], 'Libur') == 0) {
            $isNonEffective = true;
        } else {
            foreach ($nonEffectiveKeywords as $kw) {
                if (stripos($ev['judul_kegiatan'], $kw) !== false) {
                    $isNonEffective = true;
                    break;
                }
            }
        }
        
        if ($isNonEffective) {
            $curStart = new DateTime($ev['tanggal_mulai']);
            $curEnd = new DateTime($ev['tanggal_selesai']);
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($curStart, $interval, $curEnd->modify('+1 day'));
            foreach ($period as $date) {
                $nonEffectiveDates[$date->format('Y-m-d')] = $ev['judul_kegiatan'];
            }
        }
    }

    $hbe_data = [];
    foreach ($months as $m) {
        $daysInMonth = (int)date('t', strtotime($m['year'] . '-' . $m['month'] . '-01'));
        $effectiveDays = 0;
        $weeks = [];
        
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $m['year'], $m['month'], $d);
            $timestamp = strtotime($dateStr);
            $dayOfWeek = date('w', $timestamp); // 0 (Sun) to 6 (Sat)
            $weekNum = date('W', $timestamp);
            
            $isEffective = true;
            if ($dayOfWeek == 0) $isEffective = false; // Sunday always non-effective
            if (!$isSixDays && $dayOfWeek == 6) $isEffective = false; // Saturday if 5-day school
            if (isset($nonEffectiveDates[$dateStr])) $isEffective = false; // Holiday or Exam
            
            if ($isEffective) {
                $effectiveDays++;
                if (!isset($weeks[$weekNum])) $weeks[$weekNum] = 0;
                $weeks[$weekNum]++;
            }
        }
        
        $effectiveWeeks = 0;
        foreach ($weeks as $count) {
            // A week is counted as effective if it has at least 3 effective days
            if ($count >= 3) $effectiveWeeks++;
        }
        
        $hbe_data[] = [
            'month_name' => DateHelper::getNamaBulan($m['month']),
            'year' => $m['year'],
            'hbe' => $effectiveDays,
            'me' => $effectiveWeeks
        ];
    }

    ob_start();
    include __DIR__ . '/../views/kalender_akademik_pdf.php';
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Kalender_Akademik_" . str_replace(['/', ' '], '_', $ta['nama_ta']) . ".pdf", ["Attachment" => false]);
    exit;
}
