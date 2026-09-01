<?php
require_once __DIR__ . '/../models/CpTpModel.php';
require_once __DIR__ . '/../models/MapelModel.php';
require_once __DIR__ . '/../models/PenugasanModel.php'; // Untuk mengambil mapel yg diajar guru
require_once __DIR__ . '/../models/AIModel.php'; // Integrasi Google Gemini AI
require_once __DIR__ . '/../../vendor/autoload.php'; // Untuk Excel

use PhpOffice\PhpSpreadsheet\IOFactory;

function cp_tp_index($pdo)
{
    if (!check_access('manajemen_cp_tp', 'index'))
        redirect('index.php');

    $mapel_list = [];
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;

    if (in_array('Admin', $_SESSION['roles'] ?? []) || in_array('Kurikulum', $_SESSION['roles'] ?? [])) {
        // Admin & Kurikulum bisa lihat semua mapel
        $mapel_list = MapelModel::all($pdo);
    } elseif (in_array('Guru', $_SESSION['roles'] ?? []) && $id_guru_login) {
        // Guru hanya lihat mapel yang diajar
        $mapel_list = PenugasanModel::getMapelDiajarGuru($pdo, $id_guru_login, $_SESSION['id_ta_aktif']);
    }

    $id_mapel_filter = $_GET['id_mapel'] ?? ($mapel_list[0]['id_mapel'] ?? 0);
    $fase_filter = $_GET['fase'] ?? 'E';

    $cp_list = [];
    $tp_data = [];

    if ($id_mapel_filter) {
        $cp_list = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel_filter, $fase_filter);
        foreach ($cp_list as $cp) {
            $tp_data[$cp['id_cp']] = CpTpModel::getAllTpByCp($pdo, $cp['id_cp']);
        }
    }

    $data_for_view = compact('mapel_list', 'id_mapel_filter', 'fase_filter', 'cp_list', 'tp_data');
    extract($data_for_view);

    include __DIR__ . '/../views/manajemen_cp_tp_index.php';
}

function cp_save($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan CP.";
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
        return;
    }
    // Tambahkan validasi di sini: pastikan guru hanya bisa save untuk mapel yg diajar
    CpTpModel::saveCp($pdo, $_POST);
    $_SESSION['pesan_sukses'] = 'CP berhasil disimpan.';
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
}

function tp_save($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menyimpan TP.";
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
        return;
    }
    // Tambahkan validasi di sini: pastikan guru hanya bisa save untuk mapel yg diajar
    CpTpModel::saveTp($pdo, $_POST);
    $_SESSION['pesan_sukses'] = 'TP berhasil disimpan.';
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
}

function cp_delete($pdo, $id)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus CP.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    // Tambahkan validasi di sini: pastikan guru hanya bisa delete untuk mapel yg diajar
    CpTpModel::deleteCp($pdo, $id);
    $_SESSION['pesan_sukses'] = 'CP berhasil dihapus.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

function tp_delete($pdo, $id)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus TP.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    // Tambahkan validasi di sini: pastikan guru hanya bisa delete untuk mapel yg diajar
    CpTpModel::deleteTp($pdo, $id);
    $_SESSION['pesan_sukses'] = 'TP berhasil dihapus.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

function tp_delete_bulk($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus TP.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $tp_ids = $_POST['tp_ids'] ?? [];
    if (empty($tp_ids)) {
        $_SESSION['pesan_error'] = "Pilih minimal satu TP untuk dihapus.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $count = 0;
    foreach ($tp_ids as $id) {
        CpTpModel::deleteTp($pdo, $id);
        $count++;
    }

    $_SESSION['pesan_sukses'] = "$count TP berhasil dihapus.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

function tp_import($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk import TP.";
        redirect('index.php?mod=manajemen_cp_tp');
        return;
    }

    $id_mapel = $_POST['id_mapel'] ?? 0;
    $id_cp = $_POST['id_cp'] ?? 0;
    $fase = $_POST['fase'] ?? 'E';

    if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['pesan_error'] = 'File tidak valid atau tidak diunggah.';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }

    $file = $_FILES['file_excel']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['xls', 'xlsx'])) {
        $_SESSION['pesan_error'] = 'Format file harus .xls atau .xlsx';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported = 0;
        foreach ($rows as $index => $row) {
            // Skip header row if exists
            if ($index === 0 && (strtolower($row[0]) === 'kode tp' || strtolower($row[0]) === 'kode')) {
                continue;
            }

            $kode_tp = trim($row[0] ?? '');
            $deskripsi_tp = trim($row[1] ?? '');

            if (empty($kode_tp) || empty($deskripsi_tp)) {
                continue; // Skip empty rows
            }

            $data = [
                'id_cp' => $id_cp,
                'id_mapel' => $id_mapel,
                'kode_tp' => $kode_tp,
                'deskripsi_tp' => $deskripsi_tp
            ];

            CpTpModel::saveTp($pdo, $data);
            $imported++;
        }

        $_SESSION['pesan_sukses'] = "Berhasil mengimpor {$imported} TP dari Excel.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = 'Gagal membaca file Excel: ' . $e->getMessage();
    }

    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
}

function cp_import($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk import CP.";
        redirect('index.php?mod=manajemen_cp_tp');
        return;
    }

    $id_mapel = $_POST['id_mapel'] ?? 0;
    $fase = $_POST['fase'] ?? 'E';

    if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['pesan_error'] = 'File tidak valid atau tidak diunggah.';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }

    $file = $_FILES['file_excel']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['xls', 'xlsx'])) {
        $_SESSION['pesan_error'] = 'Format file harus .xls atau .xlsx';
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
        return;
    }

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $imported_cp = 0;
        $imported_tp = 0;
        $current_cp_id = null;
        $start_row = 0;

        // Find the header row (DESKRIPSI CP, KODE TP, DESKRIPSI TP)
        foreach ($rows as $index => $row) {
            $col_a = strtolower(trim($row[0] ?? ''));
            $col_b = strtolower(trim($row[1] ?? ''));
            $col_c = strtolower(trim($row[2] ?? ''));

            // Check if this is the header row
            if (
                strpos($col_a, 'deskripsi cp') !== false ||
                strpos($col_b, 'kode tp') !== false ||
                strpos($col_c, 'deskripsi tp') !== false
            ) {
                $start_row = $index + 1;
                break;
            }
        }

        // Process data rows
        for ($i = $start_row; $i < count($rows); $i++) {
            $row = $rows[$i];

            $deskripsi_cp = trim($row[0] ?? '');
            $kode_tp = trim($row[1] ?? '');
            $deskripsi_tp = trim($row[2] ?? '');

            // Skip completely empty rows
            if (empty($deskripsi_cp) && empty($kode_tp) && empty($deskripsi_tp)) {
                continue;
            }

            // Skip rows that look like notes/instructions (e.g., "Keterangan Kode:")
            if (stripos($deskripsi_cp, 'keterangan') !== false) {
                break; // Stop processing when we hit the notes section
            }

            // If Column A (Deskripsi CP) has content, this is a new CP
            if (!empty($deskripsi_cp)) {
                $data_cp = [
                    'id_mapel' => $id_mapel,
                    'fase' => $fase,
                    'deskripsi_cp' => $deskripsi_cp
                ];

                $current_cp_id = CpTpModel::saveCp($pdo, $data_cp);
                $imported_cp++;

                // If this row also has TP data (Kode TP and Deskripsi TP), save it
                if (!empty($kode_tp) && !empty($deskripsi_tp)) {
                    $data_tp = [
                        'id_cp' => $current_cp_id,
                        'id_mapel' => $id_mapel,
                        'kode_tp' => $kode_tp,
                        'deskripsi_tp' => $deskripsi_tp
                    ];

                    CpTpModel::saveTp($pdo, $data_tp);
                    $imported_tp++;
                }
            }
            // If Column A is empty but Columns B & C have content, this is a TP for the current CP
            elseif (!empty($kode_tp) && !empty($deskripsi_tp)) {
                if ($current_cp_id === null) {
                    $_SESSION['pesan_error'] = 'Format file salah: TP ditemukan sebelum CP. Pastikan setiap TP memiliki CP di atasnya.';
                    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
                    return;
                }

                $data_tp = [
                    'id_cp' => $current_cp_id,
                    'id_mapel' => $id_mapel,
                    'kode_tp' => $kode_tp,
                    'deskripsi_tp' => $deskripsi_tp
                ];

                CpTpModel::saveTp($pdo, $data_tp);
                $imported_tp++;
            }
        }

        $_SESSION['pesan_sukses'] = "Berhasil mengimpor {$imported_cp} CP dan {$imported_tp} TP dari Excel.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = 'Gagal membaca file Excel: ' . $e->getMessage();
    }

    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $id_mapel . '&fase=' . $fase);
}

function download_template_cp_tp($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'create')) {
        $_SESSION['pesan_error'] = "Akses ditolak.";
        redirect('index.php?mod=manajemen_cp_tp');
        return;
    }

    $file_path = __DIR__ . '/../../template/template_cp_tp.xlsx';
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Impor_CP_TP.xlsx"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        if (ob_get_length())
            ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        $_SESSION['pesan_error'] = "File template tidak ditemukan.";
        redirect('index.php?mod=manajemen_cp_tp');
    }
}

function cp_update($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk mengubah CP.";
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
        return;
    }
    
    $id_cp = $_POST['id_cp'] ?? 0;
    $deskripsi_cp = trim($_POST['deskripsi_cp'] ?? '');
    
    if ($id_cp && $deskripsi_cp) {
        CpTpModel::updateCp($pdo, $id_cp, $deskripsi_cp);
        $_SESSION['pesan_sukses'] = 'Capaian Pembelajaran (CP) berhasil diubah.';
    } else {
        $_SESSION['pesan_error'] = 'Data tidak lengkap.';
    }
    
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
}

function tp_update($pdo)
{
    if (!can_do($pdo, 'manajemen_cp_tp', 'update')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk mengubah TP.";
        redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
        return;
    }
    
    $id_tp = $_POST['id_tp'] ?? 0;
    $kode_tp = trim($_POST['kode_tp'] ?? '');
    $materi = trim($_POST['materi'] ?? '');
    $deskripsi_tp = trim($_POST['deskripsi_tp'] ?? '');
    
    if ($id_tp && $kode_tp && $deskripsi_tp) {
        CpTpModel::updateTp($pdo, $id_tp, $kode_tp, $deskripsi_tp, $materi);
        $_SESSION['pesan_sukses'] = 'Tujuan Pembelajaran (TP) berhasil diubah.';
    } else {
        $_SESSION['pesan_error'] = 'Data tidak lengkap.';
    }
    
    redirect('index.php?mod=manajemen_cp_tp&id_mapel=' . $_POST['id_mapel'] . '&fase=' . $_POST['fase']);
}

/**
 * AJAX: AI merumuskan Tujuan Pembelajaran (TP) dan Topik Materi dari Capaian Pembelajaran (CP) berdasarkan Taksonomi Bloom
 */
function cp_tp_ai_generate_tp($pdo)
{
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in() || (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update'))) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin untuk membuat TP.']);
        return;
    }

    $id_cp          = intval($_POST['id_cp'] ?? 0);
    $id_mapel       = intval($_POST['id_mapel'] ?? 0);
    $fase           = trim($_POST['fase'] ?? 'E');
    $kriteria_bloom = trim($_POST['kriteria_bloom'] ?? 'berjenjang'); // c1_c2, c3_c4, c5_c6, berjenjang
    $jumlah_tp      = max(1, min(8, intval($_POST['jumlah_tp'] ?? 3)));
    $prefix_input   = trim($_POST['prefix_kode'] ?? '');

    $cp = CpTpModel::getCpById($pdo, $id_cp);
    if (!$cp) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Data Capaian Pembelajaran (CP) tidak ditemukan.']);
        return;
    }

    // Ambil nama mapel
    $mapel = MapelModel::find($pdo, $id_mapel);
    $nama_mapel = $mapel['nama_mapel'] ?? 'Mata Pelajaran';

    // Cek TP yang sudah ada untuk penomoran otomatis
    $existing_tps = CpTpModel::getAllTpByCp($pdo, $id_cp);
    $next_index = count($existing_tps) + 1;

    // Tentukan default prefix kode (misal Fase E -> E.1.)
    if (empty($prefix_input)) {
        $prefix_kode = "{$fase}.1.";
    } else {
        $prefix_kode = rtrim($prefix_input, '.') . '.';
    }

    // Kriteria Bloom setup
    $bloom_desc = "";
    switch ($kriteria_bloom) {
        case 'c1_c2':
            $bloom_desc = "FOKUS TINGKAT KOGNITIF: C1 (Mengingat) dan C2 (Memahami) - LOTS (Lower Order Thinking Skills).\n"
                        . "Gunakan Kata Kerja Operasional (KKO) seperti: Mengidentifikasi, Menyebutkan, Menjelaskan, Mendeskripsikan, Mengelompokkan, Menguraikan, Menerangkan, Mengklasifikasikan.";
            break;
        case 'c3_c4':
            $bloom_desc = "FOKUS TINGKAT KOGNITIF: C3 (Menerapkan) dan C4 (Menganalisis) - MOTS (Middle Order Thinking Skills).\n"
                        . "Gunakan Kata Kerja Operasional (KKO) seperti: Menerapkan, Mengimplementasikan, Membandingkan, Menganalisis, Memecahkan masalah, Membedakan, Mengkorelasikan, Membuktikan.";
            break;
        case 'c5_c6':
            $bloom_desc = "FOKUS TINGKAT KOGNITIF: C5 (Mengevaluasi) dan C6 (Mencipta/Mengkreasi) - HOTS (Higher Order Thinking Skills).\n"
                        . "Gunakan Kata Kerja Operasional (KKO) seperti: Mengevaluasi, Menilai, Mengkritisi, Menyimpulkan, Merancang, Mengkonstruksi, Memproduksi, Mengembangkan gagasan/karya orisinal.";
            break;
        case 'berjenjang':
        default:
            $bloom_desc = "FOKUS TINGKAT KOGNITIF: Berjenjang Seimbang dan Bertahap (dari C1/C2 Pemahaman Dasar -> C3/C4 Penerapan & Analisis -> C5/C6 Evaluasi & Kreasi/HOTS).\n"
                        . "Susun TP secara progresif dari tahap penguasaan konsep dasar hingga aplikasi dan pemikiran kritis tingkat tinggi.";
            break;
    }

    $system_instruction = "Anda adalah Pakar Kurikulum Merdeka dan Ahli Taksonomi Bloom Terkemuka di Indonesia.\n"
        . "Tugas Anda adalah membedah Capaian Pembelajaran (CP) menjadi persis {$jumlah_tp} butir Tujuan Pembelajaran (TP) terukur beserta mengekstrak **Topik / Lingkup Materi Pokok** untuk masing-masing TP.\n"
        . "KRITERIA WAJIB:\n"
        . "1. Ekstrak nama 'materi' (topik bahasan spesifik, singkat & padat, misal: 'Pendapatan Nasional', 'Lembaga Keuangan', 'Hukum Newton').\n"
        . "2. Setiap TP diawali dengan Kata Kerja Operasional (KKO) yang tepat sesuai Taksonomi Bloom.\n"
        . "3. Kalimat TP harus jelas, operasional, dan relevan dengan peserta didik.\n"
        . "4. Kembalikan output HANYA dalam format JSON valid tanpa markdown lain.";

    $prompt = "Tolong rumuskan persis {$jumlah_tp} butir Tujuan Pembelajaran (TP) beserta Topik Materinya dari data berikut:\n\n"
        . "MATA PELAJARAN: {$nama_mapel}\n"
        . "FASE / KELAS: Fase {$fase}\n"
        . "CAPAIAN PEMBELAJARAN (CP):\n" . $cp['deskripsi_cp'] . "\n\n"
        . "KRITERIA TAKSONOMI BLOOM:\n{$bloom_desc}\n\n"
        . "PENOMORAN KODE TP:\n"
        . "Awali dari kode: '{$prefix_kode}{$next_index}', '{$prefix_kode}" . ($next_index + 1) . "', dst.\n\n"
        . "KEMBALIKAN DALAM FORMAT JSON SEPERTI BERIKUT:\n"
        . "{\n"
        . "  \"tujuan_pembelajaran\": [\n"
        . "    {\n"
        . "      \"kode_tp\": \"{$prefix_kode}{$next_index}\",\n"
        . "      \"materi\": \"Topik / Lingkup Materi Pokok\",\n"
        . "      \"level_bloom\": \"C2 - Memahami\",\n"
        . "      \"kko\": \"Menjelaskan\",\n"
        . "      \"deskripsi_tp\": \"Menjelaskan konsep ...\"\n"
        . "    }\n"
        . "  ]\n"
        . "}";

    $res = AIModel::generate($pdo, $prompt, $system_instruction, true);

    ob_end_clean();
    if (!$res['success']) {
        echo json_encode(['success' => false, 'message' => $res['message'] ?? 'Gagal memproses ke AI Model.']);
        return;
    }

    $json_str = $res['text'];
    $data = json_decode($json_str, true);

    // Fallback regex jika JSON dibungkus teks
    if (!$data || !isset($data['tujuan_pembelajaran'])) {
        if (preg_match('/\{[\s\S]*"tujuan_pembelajaran"[\s\S]*\}/', $json_str, $matches)) {
            $data = json_decode($matches[0], true);
        }
    }

    $tp_list = [];
    if (isset($data['tujuan_pembelajaran']) && is_array($data['tujuan_pembelajaran'])) {
        $idx = $next_index;
        foreach ($data['tujuan_pembelajaran'] as $item) {
            $kode = trim($item['kode_tp'] ?? "{$prefix_kode}{$idx}");
            $materi = trim($item['materi'] ?? '');
            $deskripsi = trim($item['deskripsi_tp'] ?? '');
            $level = trim($item['level_bloom'] ?? '');
            $kko = trim($item['kko'] ?? '');
            if (!empty($deskripsi)) {
                $tp_list[] = [
                    'kode_tp' => $kode,
                    'materi' => $materi,
                    'level_bloom' => $level,
                    'kko' => $kko,
                    'deskripsi_tp' => $deskripsi
                ];
                $idx++;
            }
        }
    }

    // Fallback jika format JSON tidak terpenuhi tapi ada teks baris
    if (empty($tp_list)) {
        $lines = array_filter(array_map('trim', explode("\n", strip_tags($json_str))));
        $idx = $next_index;
        foreach ($lines as $line) {
            $line = preg_replace('/^[\d\.\-\*\•\)\s]+/', '', $line);
            if (!empty($line) && strlen($line) > 10 && strpos($line, '{') === false) {
                $tp_list[] = [
                    'kode_tp' => "{$prefix_kode}{$idx}",
                    'materi' => '',
                    'level_bloom' => 'Bloom',
                    'kko' => '',
                    'deskripsi_tp' => $line
                ];
                $idx++;
            }
        }
    }

    if (empty($tp_list)) {
        echo json_encode(['success' => false, 'message' => 'AI tidak menghasilkan daftar TP yang valid. Silakan coba lagi.']);
        return;
    }

    echo json_encode([
        'success' => true,
        'cp_info' => [
            'id_cp' => $id_cp,
            'deskripsi_cp' => $cp['deskripsi_cp'],
            'mapel' => $nama_mapel,
            'fase' => $fase
        ],
        'tp_list' => $tp_list
    ]);
}

/**
 * AJAX: Simpan TP yang digenerate oleh AI secara massal
 */
function cp_tp_ai_save_bulk($pdo)
{
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in() || (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update'))) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin untuk menyimpan TP.']);
        return;
    }

    $id_cp    = intval($_POST['id_cp'] ?? 0);
    $id_mapel = intval($_POST['id_mapel'] ?? 0);
    $tp_items = $_POST['tp_items'] ?? [];

    if (!$id_cp || !$id_mapel || empty($tp_items) || !is_array($tp_items)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Data TP yang akan disimpan tidak valid atau kosong.']);
        return;
    }

    $saved_count = CpTpModel::bulkSaveTp($pdo, $id_cp, $id_mapel, $tp_items);

    ob_end_clean();
    if ($saved_count > 0) {
        $_SESSION['pesan_sukses'] = "Berhasil menambahkan {$saved_count} Tujuan Pembelajaran (TP) beserta Topik Materi dari AI.";
        echo json_encode(['success' => true, 'count' => $saved_count, 'message' => "Berhasil menambahkan {$saved_count} TP."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada TP yang berhasil disimpan.']);
    }
}

/**
 * AJAX: AI mengisi / melengkapi topik materi secara otomatis untuk TP lama yang belum memiliki topik
 */
function cp_tp_ai_generate_missing_topics($pdo)
{
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in() || (!can_do($pdo, 'manajemen_cp_tp', 'create') && !can_do($pdo, 'manajemen_cp_tp', 'update'))) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin.']);
        return;
    }

    $id_cp = intval($_POST['id_cp'] ?? 0);
    $cp = CpTpModel::getCpById($pdo, $id_cp);
    if (!$cp) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Data CP tidak ditemukan.']);
        return;
    }

    $empty_tps = CpTpModel::getEmptyTopicTpsByCp($pdo, $id_cp);
    if (empty($empty_tps)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Semua TP pada CP ini sudah memiliki topik materi.']);
        return;
    }

    $mapel = MapelModel::find($pdo, $cp['id_mapel']);
    $nama_mapel = $mapel['nama_mapel'] ?? 'Mata Pelajaran';

    $tp_text_list = "";
    foreach ($empty_tps as $tp) {
        $tp_text_list .= "- [ID: {$tp['id_tp']}] (Kode: {$tp['kode_tp']}) Deskripsi: {$tp['deskripsi_tp']}\n";
    }

    $system_instruction = "Anda adalah Pakar Kurikulum Merdeka di Indonesia.\n"
        . "Tugas Anda adalah membaca daftar Tujuan Pembelajaran (TP) yang diberikan dan mengekstrak nama 'Topik / Lingkup Materi Pokok' (singkat, padat, jelas, 2-5 kata, misal: 'Pendapatan Nasional', 'Lembaga Keuangan', 'Hukum Gravitasi') untuk masing-masing TP.\n"
        . "Kembalikan HANYA format JSON valid tanpa format markdown lain.";

    $prompt = "MATA PELAJARAN: {$nama_mapel}\n"
        . "CAPAIAN PEMBELAJARAN (CP):\n" . $cp['deskripsi_cp'] . "\n\n"
        . "DAFTAR TUJUAN PEMBELAJARAN (TP) YANG PERLU DILENGKAPI TOPIK MATERINYA:\n"
        . $tp_text_list . "\n"
        . "KEMBALIKAN DALAM FORMAT JSON SEPERTI BERIKUT:\n"
        . "{\n"
        . "  \"topics\": [\n"
        . "    {\n"
        . "      \"id_tp\": " . $empty_tps[0]['id_tp'] . ",\n"
        . "      \"materi\": \"Nama Topik Pokok Singkat\"\n"
        . "    }\n"
        . "  ]\n"
        . "}";

    $res = AIModel::generate($pdo, $prompt, $system_instruction, true);

    ob_end_clean();
    if (!$res['success']) {
        echo json_encode(['success' => false, 'message' => $res['message'] ?? 'Gagal memproses ke AI Model.']);
        return;
    }

    $json_str = $res['text'];
    $data = json_decode($json_str, true);
    if (!$data || !isset($data['topics'])) {
        if (preg_match('/\{[\s\S]*"topics"[\s\S]*\}/', $json_str, $matches)) {
            $data = json_decode($matches[0], true);
        }
    }

    $updated_count = 0;
    if (isset($data['topics']) && is_array($data['topics'])) {
        foreach ($data['topics'] as $item) {
            $id_tp = intval($item['id_tp'] ?? 0);
            $materi = trim($item['materi'] ?? '');
            if ($id_tp && !empty($materi)) {
                CpTpModel::updateTpMateri($pdo, $id_tp, $materi);
                $updated_count++;
            }
        }
    }

    if ($updated_count > 0) {
        $_SESSION['pesan_sukses'] = "Berhasil melengkapi {$updated_count} Topik Materi secara otomatis dengan AI.";
        echo json_encode(['success' => true, 'updated_count' => $updated_count, 'message' => "Berhasil melengkapi {$updated_count} topik materi."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada topik materi yang berhasil diperbarui. Silakan coba lagi.']);
    }
}