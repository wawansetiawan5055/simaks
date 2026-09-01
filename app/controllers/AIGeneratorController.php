<?php
/**
 * AIGeneratorController.php
 * Handles AI-based teaching document generation with Deep Learning Master Templates
 */

require_once __DIR__ . '/../models/AIGeneratorModel.php';
require_once __DIR__ . '/../models/AIModel.php';
require_once __DIR__ . '/../models/TahunAjaranModel.php';
require_once __DIR__ . '/../models/PenugasanModel.php';
require_once __DIR__ . '/../models/DocExtractorModel.php';
require_once __DIR__ . '/../models/CpTpModel.php';
require_once __DIR__ . '/../models/MapelModel.php';

function ai_generator_index($pdo) {
    if (!is_logged_in()) redirect('index.php');
    
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $logs = AIGeneratorModel::getLogsByGuru($pdo, $id_guru);
    
    include __DIR__ . '/../views/ai_generator_index.php';
}

function ai_generator_create($pdo) {
    if (!is_logged_in()) redirect('index.php');
    
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;
    
    // Get Mata Pelajaran for dropdown
    if ($id_guru) {
        $mapel_list = PenugasanModel::getMapelDiajarGuru($pdo, $id_guru, $id_ta);
    } else {
        $mapel_list = MapelModel::all($pdo);
    }
    
    include __DIR__ . '/../views/ai_generator_create.php';
}

/**
 * AJAX: Ambil daftar Capaian Pembelajaran (CP) berdasarkan id_mapel dan fase
 */
function ai_generator_get_cp($pdo) {
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $id_mapel = intval($_GET['id_mapel'] ?? 0);
    $fase     = trim($_GET['fase'] ?? '');

    if (!$id_mapel || !$fase) {
        ob_end_clean();
        echo json_encode(['success' => false, 'data' => [], 'message' => 'Parameter tidak lengkap']);
        return;
    }

    $cp_list = CpTpModel::getAllCpByMapelAndFase($pdo, $id_mapel, $fase);

    ob_end_clean();
    echo json_encode(['success' => true, 'data' => $cp_list]);
}

/**
 * AJAX: Ambil daftar Tujuan Pembelajaran (TP) berdasarkan id_cp
 */
function ai_generator_get_tp($pdo) {
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $id_cp = intval($_GET['id_cp'] ?? 0);

    if (!$id_cp) {
        ob_end_clean();
        echo json_encode(['success' => false, 'data' => [], 'message' => 'id_cp tidak valid']);
        return;
    }

    $tp_list = CpTpModel::getAllTpByCp($pdo, $id_cp);

    ob_end_clean();
    echo json_encode(['success' => true, 'data' => $tp_list]);
}

/**
 * AJAX: AI merumuskan Tujuan Pembelajaran (TP) spesifik dari Topik & CP
 */
function ai_generator_generate_tp($pdo) {
    @set_time_limit(180);
    @ini_set('max_execution_time', 180);
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $mapel_name   = trim($_POST['mapel'] ?? '');
    $fase         = trim($_POST['fase'] ?? 'E');
    $topik        = trim($_POST['topik'] ?? '');
    $cp_deskripsi = trim($_POST['cp_deskripsi'] ?? '');

    if (empty($topik)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Harap isi Topik Pembelajaran terlebih dahulu.']);
        return;
    }

    $system_instruction = "Anda adalah Pakar Kurikulum Merdeka. Tugas Anda adalah merumuskan 3 sampai 5 Tujuan Pembelajaran (TP) yang operasional, terukur (menggunakan Kata Kerja Operasional Taksonomi Bloom/Marzano), kontekstual, dan berprinsip Deep Learning. Kembalikan HANYA teks daftar TP, satu TP per baris, tanpa nomor atau simbol pembuka (misal: 'Murid dapat menggeneralisasi sifat-sifat...').";

    $prompt = "Rumuskan 3-5 Tujuan Pembelajaran (TP) spesifik untuk:\n"
            . "Mata Pelajaran: {$mapel_name}\n"
            . "Fase/Kelas: Fase {$fase}\n"
            . "Topik Materi: {$topik}\n"
            . "Capaian Pembelajaran (CP):\n{$cp_deskripsi}\n\n"
            . "Format: Hasilkan persis 3-5 baris kalimat TP siap pakai.";

    $res = AIModel::generate($pdo, $prompt, $system_instruction);

    ob_end_clean();
    if ($res['success']) {
        $lines = array_filter(array_map('trim', explode("\n", strip_tags($res['text']))));
        $clean_tp = [];
        foreach ($lines as $line) {
            $line = preg_replace('/^[\d\.\-\*\•\)\s]+/', '', $line);
            if (!empty($line)) $clean_tp[] = $line;
        }
        echo json_encode(['success' => true, 'tp_list' => array_values($clean_tp)]);
    } else {
        echo json_encode(['success' => false, 'message' => $res['message']]);
    }
}

/**
 * AJAX: AI merumuskan profil kesiapan murid otomatis
 */
function ai_generator_generate_profil($pdo) {
    @set_time_limit(180);
    @ini_set('max_execution_time', 180);
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $mapel_name = trim($_POST['mapel'] ?? '');
    $kelas      = trim($_POST['kelas'] ?? '');
    $fase       = trim($_POST['fase'] ?? 'E');
    $topik      = trim($_POST['topik'] ?? '');

    $system_instruction = "Anda adalah Guru Ahli Kurikulum Merdeka. Tuliskan deskripsi ringkas 1-2 paragraf padat tentang 'Kesiapan & Profil Belajar Murid' untuk topik pembelajaran tertentu. Deskripsi harus mencakup: Pengetahuan Awal yang relevan, Minat & Latar Belakang murid, serta Kebutuhan Belajar Berdiferensiasi (Visual, Auditori, Kinestetik). Kembalikan HANYA teks polos tanpa format markdown atau HTML.";

    $prompt = "Tuliskan profil kesiapan awal dan kebutuhan belajar murid untuk:\nMata Pelajaran: {$mapel_name}\nKelas/Fase: {$kelas} / Fase {$fase}\nTopik: {$topik}";

    $res = AIModel::generate($pdo, $prompt, $system_instruction);

    ob_end_clean();
    if ($res['success']) {
        echo json_encode(['success' => true, 'profil' => strip_tags($res['text'])]);
    } else {
        echo json_encode(['success' => false, 'message' => $res['message']]);
    }
}

function ai_generator_upload_ref($pdo) {
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Sesi habis. Silakan login ulang.']);
        return;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_FILES['doc_referensi']) || $_FILES['doc_referensi']['error'] !== UPLOAD_ERR_OK) {
        $err_code = isset($_FILES['doc_referensi']) ? ($_FILES['doc_referensi']['error'] ?? -1) : -1;
        $err_msg = 'Gagal mengupload file (kode: ' . $err_code . '). Coba lagi.';
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => $err_msg]);
        return;
    }

    $file = $_FILES['doc_referensi'];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['docx', 'txt', 'pdf'];

    if (!in_array($ext, $allowed)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Format file tidak didukung. Gunakan DOCX, PDF, atau TXT.']);
        return;
    }

    $temp_path = DocExtractorModel::saveUploadTemp($file);
    if (!$temp_path) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file sementara.']);
        return;
    }

    $result = DocExtractorModel::extract($temp_path, $ext);

    if (!$result['success']) {
        DocExtractorModel::cleanupTempFile($temp_path);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => $result['message']]);
        return;
    }

    $_SESSION['ai_ref_skeleton'] = $result['skeleton'];
    $_SESSION['ai_ref_filename'] = $file['name'];
    $_SESSION['ai_ref_mode']     = 'file';
    $_SESSION['ai_ref_temppath'] = $temp_path;
    session_write_close();

    $preview = mb_substr($result['skeleton'], 0, 300);
    if (mb_strlen($result['skeleton']) > 300) $preview .= '...';

    ob_end_clean();
    echo json_encode([
        'success'  => true,
        'message'  => $result['message'],
        'filename' => $file['name'],
        'preview'  => nl2br(htmlspecialchars($preview))
    ]);
}

function ai_generator_set_manual_ref($pdo) {
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Sesi habis.']);
        return;
    }

    $teks = $_POST['teks'] ?? '';
    if (empty(trim($teks))) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Teks manual tidak boleh kosong.']);
        return;
    }

    if (mb_strlen($teks) > 10000) {
        $teks = mb_substr($teks, 0, 10000);
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $skeleton = DocExtractorModel::buildSkeletonText($teks);

    $_SESSION['ai_ref_skeleton'] = $skeleton;
    $_SESSION['ai_ref_filename'] = 'Manual Paste';
    $_SESSION['ai_ref_mode']     = 'manual';
    session_write_close();

    $preview = mb_substr($skeleton, 0, 300);
    if (mb_strlen($skeleton) > 300) $preview .= '...';

    ob_end_clean();
    echo json_encode([
        'success'  => true,
        'message'  => 'Struktur berhasil disimpan manual.',
        'filename' => 'Manual Paste',
        'preview'  => nl2br(htmlspecialchars($preview))
    ]);
}

function ai_generator_clear_ref($pdo) {
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false]);
        return;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $old_path = $_SESSION['ai_ref_temppath'] ?? '';
    if ($old_path && file_exists($old_path)) DocExtractorModel::cleanupTempFile($old_path);

    unset($_SESSION['ai_ref_skeleton'], $_SESSION['ai_ref_filename'], $_SESSION['ai_ref_mode'], $_SESSION['ai_ref_temppath']);
    session_write_close();

    ob_end_clean();
    echo json_encode(['success' => true]);
}

/**
 * PROSES GENERATE UTAMA DENGAN MASTER TEMPLATE BAKU
 */
function ai_generator_process($pdo) {
    @set_time_limit(180);
    @ini_set('max_execution_time', 180);
    if (!is_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    header('Content-Type: application/json');

    $mapel_name     = trim($_POST['mapel'] ?? '');
    $kelas          = trim($_POST['kelas'] ?? '');
    $fase           = trim($_POST['fase'] ?? 'E');
    $topik          = trim($_POST['topik'] ?? '');
    $jenis          = trim($_POST['jenis'] ?? 'Modul Ajar Deep Learning');
    $alokasi_waktu  = trim($_POST['alokasi_waktu'] ?? '12 JP (6 Pertemuan x 2 JP @ 45 Menit)');
    $metode         = trim($_POST['metode'] ?? 'Discovery Learning');
    $kesiapan_murid = trim($_POST['kesiapan_murid'] ?? '');
    $cp_deskripsi   = trim($_POST['cp_deskripsi'] ?? '');
    $tp_list_raw    = $_POST['tp_list'] ?? [];

    // Profil Sekolah
    require_once __DIR__ . '/../models/ProfilSekolahModel.php';
    $sekolah = ProfilSekolahModel::getProfil($pdo);
    $nama_sekolah = $sekolah['nama_sekolah'] ?? 'SMA PLUS AL MANSHURIYAH';
    $nama_kepsek  = $sekolah['nama_kepsek'] ?? 'Dadun Abdul Manaf, S.E., M.Pd.';

    // Profil Guru
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $nama_guru = $_SESSION['nama_lengkap']
        ?? $_SESSION['nama_guru_terkait']
        ?? $_SESSION['nama_pengguna']
        ?? 'Guru Mata Pelajaran';
        
    if ($id_guru) {
        require_once __DIR__ . '/../models/GuruModel.php';
        require_once __DIR__ . '/../models/ProfilGuruModel.php';
        $guru = GuruModel::find($pdo, $id_guru);
        if ($guru && !empty($guru['nama'])) {
            $nama_guru = $guru['nama'];
            $profil_guru = ProfilGuruModel::getByGuruId($pdo, $id_guru);
            if ($profil_guru) {
                $gelar_depan = trim($profil_guru['gelar_depan'] ?? '');
                $gelar_belakang = trim($profil_guru['gelar_belakang'] ?? '');
                if ($gelar_depan !== '') $nama_guru = $gelar_depan . ' ' . $nama_guru;
                if ($gelar_belakang !== '') $nama_guru = $nama_guru . ', ' . $gelar_belakang;
            }
        }
    }

    $tahun_ajaran = $_SESSION['nama_ta_aktif'] ?? date('Y') . '/' . (date('Y') + 1);

    if (empty($mapel_name) || empty($topik)) {
        echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap. Pastikan Mapel dan Topik terisi.']);
        return;
    }

    // Format daftar TP
    $tp_formatted = '';
    if (!empty($tp_list_raw) && is_array($tp_list_raw)) {
        foreach ($tp_list_raw as $idx => $tp_desc) {
            $no = $idx + 1;
            $tp_formatted .= "   TP-{$no}: " . trim($tp_desc) . "\n";
        }
    } else {
        $tp_formatted = "   TP-1: Murid dapat menggeneralisasi konsep dasar {$topik} melalui pengamatan pola.\n"
                      . "   TP-2: Murid dapat menyelesaikan masalah kontekstual menggunakan sifat {$topik}.\n";
    }

    // Reference skeleton jika ada
    $ref_skeleton = $_SESSION['ai_ref_skeleton'] ?? '';

    // =========================================================================
    // SYSTEM INSTRUCTION MASTER: Kurikulum Merdeka & Pendekatan Deep Learning
    // =========================================================================
    $system_instruction =
        "Anda adalah Pakar Pengembang Kurikulum Merdeka & Desain Pembelajaran Mendalam (Deep Learning) untuk SMA Plus Al Manshuriyah. "
        . "Tugas Anda adalah membuat dokumen perencanaan pembelajaran yang sangat rapi, terstruktur, komprehensif, dan siap cetak/publikasi. "
        . "Prinsip Deep Learning yang WAJIB diterapkan: MINDFUL (kesadaran penuh & refleksi), MEANINGFUL (bermakna, kontekstual dunia nyata), dan JOYFUL (menyenangkan & kolaboratif). "
        . "Gunakan Bahasa Indonesia formal edukatif baku. "
        . "Output HARUS berupa HTML bersih menggunakan elemen: <h2>, <h3>, <h4>, <p>, <ul>, <ol>, <li>, <table border='1' cellpadding='6' cellspacing='0' style='width:100%; border-collapse:collapse; margin-bottom:15px;'>, <thead>, <tbody>, <tr>, <th>, <td>, <strong>, <em>. "
        . "DILARANG menyertakan tag <html>, <head>, <body>, <style>, atau markdown backticks di luar HTML.";

    // =========================================================================
    // PROMPT BRANCHING BERDASARKAN JENIS DOKUMEN YANG DIPILIH GURU
    // =========================================================================
    if (stripos($jenis, 'ATP') !== false || stripos($jenis, 'Alur') !== false) {
        // === TEMPLATE 1: ALUR TUJUAN PEMBELAJARAN (ATP) ===
        $prompt = "Buatlah dokumen ALUR TUJUAN PEMBELAJARAN (ATP) KURIKULUM MERDEKA dengan struktur persis berikut:\n\n"
            . "<div style='text-align:center; margin-bottom:20px;'>"
            . "<h2>ALUR TUJUAN PEMBELAJARAN (ATP)<br>KURIKULUM MERDEKA</h2>"
            . "</div>\n\n"
            . "1. TABEL IDENTITAS ATP:\n"
            . "<table border='1'>\n"
            . "<tr><td width='20%'><strong>Nama Sekolah</strong></td><td width='30%'>: {$nama_sekolah}</td><td width='20%'><strong>Fase / Kelas</strong></td><td width='30%'>: {$fase} / {$kelas}</td></tr>\n"
            . "<tr><td><strong>Mata Pelajaran</strong></td><td>: {$mapel_name}</td><td><strong>Semester</strong></td><td>: Ganjil</td></tr>\n"
            . "<tr><td><strong>Penyusun</strong></td><td>: {$nama_guru}</td><td><strong>Tahun Pelajaran</strong></td><td>: {$tahun_ajaran}</td></tr>\n"
            . "</table>\n\n"
            . "2. CAPAIAN PEMBELAJARAN (CP):\n"
            . "<p><strong>Capaian Pembelajaran (Elemen Materi):</strong><br>{$cp_deskripsi}</p>\n\n"
            . "3. TABEL ALUR TUJUAN PEMBELAJARAN:\n"
            . "Buat tabel komprehensif memuat kolom: [No | Tujuan Pembelajaran (TP) | Kata Kunci / Topik | Alokasi Waktu (JP) | Dimensi Profil Lulusan] untuk topik {$topik}.\n\n"
            . "4. TABEL REKAPITULASI ALOKASI WAKTU SEMESTER:\n"
            . "Buat tabel Rekapitulasi Alokasi Waktu Semester (Jumlah JP per minggu, Jumlah minggu efektif semester, Total JP tersedia, Total JP terpakai bab ini, Sisa JP untuk bab lain).\n\n"
            . "5. LEMBAR PENGESAHAN:\n"
            . "Tabel tanda tangan Mengetahui Kepala Sekolah ({$nama_kepsek}) dan Guru Mata Pelajaran ({$nama_guru}).";

    } elseif (stripos($jenis, 'LKPD') !== false || stripos($jenis, 'Lembar Kerja') !== false) {
        // === TEMPLATE 2: LEMBAR KERJA PESERTA DIDIK (LKPD) ===
        $prompt = "Buatlah LEMBAR KERJA PESERTA DIDIK (LKPD) INTERAKTIF & EKSPLORATIF untuk data berikut:\n\n"
            . "<div style='text-align:center; margin-bottom:15px; border-bottom:2px solid #000; padding-bottom:10px;'>"
            . "<h2>LEMBAR KERJA PESERTA DIDIK (LKPD)</h2>"
            . "<h3>BAB: {$topik}</h3>"
            . "</div>\n\n"
            . "A. TABEL IDENTITAS LKPD (Sekolah: {$nama_sekolah}, Mapel: {$mapel_name}, Kelas/Fase: {$kelas}/{$fase}, Alokasi Waktu: {$alokasi_waktu}, Kelompok/Anggota: titik-titik).\n"
            . "B. TUJUAN PEMBELAJARAN\n"
            . "C. PETUNJUK PENGERJAAN (Langkah kerja berkelompok 4-5 murid, alokasi waktu).\n"
            . "D. KEGIATAN 1 — STIMULATION (Pemberian Rangsangan: Masalah kontekstual dunia nyata + Pertanyaan Pemantik).\n"
            . "E. KEGIATAN 2 — PROBLEM STATEMENT (Identifikasi Masalah & Rumusan Pertanyaan Penyelidikan).\n"
            . "F. KEGIATAN 3 — DATA COLLECTION (Tabel Eksplorasi Pola/Data 1, 2, dan 3 yang harus dilengkapi murid).\n"
            . "G. KEGIATAN 4 — DATA PROCESSING (Pertanyaan Penuntun Analisis Pengolahan Data).\n"
            . "H. KEGIATAN 5 — VERIFICATION (Uji Pembuktian Mandiri).\n"
            . "I. KEGIATAN 6 — GENERALIZATION (Tabel Kesimpulan Sifat/Konsep Umum).\n"
            . "J. LATIHAN PENERAPAN (MENGAPLIKASI) (5 Soal Latihan Mandiri & Kontekstual Dunia Nyata).";

    } elseif (stripos($jenis, 'Asesmen') !== false || stripos($jenis, 'Rubrik') !== false) {
        // === TEMPLATE 3: INSTRUMEN & RUBRIK ASESMEN LENGKAP ===
        $prompt = "Buatlah INSTRUMEN & RUBRIK ASESMEN PEMBELAJARAN LENGKAP untuk topik {$topik} ({$mapel_name} Kelas {$kelas}):\n\n"
            . "A. ASESMEN DIAGNOSTIK (Sebelum Pembelajaran: Daftar Pertanyaan Pemantik Apersepsi & Panduan Tindak Lanjut).\n"
            . "B. ASESMEN FORMATIF (Selama Pembelajaran: Lembar Observasi Diskusi Kelompok dengan kolom Partisipasi, Ketepatan Konsep, Kerja Sama, Skor).\n"
            . "C. ASESMEN SUMATIF (Akhir Pembelajaran):"
            . "   - Tabel Kisi-Kisi Soal (Tujuan Pembelajaran, Indikator Soal, Nomor Soal)\n"
            . "   - 5 Butir Soal Kuis / Penilaian Harian (Tingkat Mudah, Sedang, HOTS)\n"
            . "   - Tabel Kunci Jawaban & Pedoman Penskoran (Skor per nomor total 100)\n"
            . "D. RUBRIK PENILAIAN 4 SKALA (Sangat Baik [4], Baik [3], Cukup [2], Kurang [1]):"
            . "   - Rubrik Observasi Diskusi Kelompok / LKPD\n"
            . "   - Rubrik Presentasi Hasil Diskusi (Kejelasan, Penguasaan Materi)\n"
            . "   - Rubrik Sikap Profil Lulusan (Bernalar Kritis, Kreatif, Mandiri)\n"
            . "   - Rubrik Penilaian Tes Tertulis & Tabel Kriteria Ketuntasan (Rentang Nilai, Predikat, Keterangan Tindak Lanjut).";

    } else {
        // === TEMPLATE MASTER UTAMA: MODUL AJAR DEEP LEARNING LENGKAP (10 HALAMAN LENGKAP BESERTA LAMPIRAN) ===
        $prompt = "Buatlah DOKUMEN MODUL AJAR DEEP LEARNING (KURIKULUM MERDEKA) LENGKAP BESERTA SELURUH LAMPIRANNYA untuk data berikut:\n\n"
            . "=== DATA IDENTITAS ===\n"
            . "Nama Sekolah      : {$nama_sekolah}\n"
            . "Nama Penyusun     : {$nama_guru}\n"
            . "Mata Pelajaran    : {$mapel_name}\n"
            . "Kelas / Fase / Sem: {$kelas} / Fase {$fase} / Ganjil\n"
            . "Bab / Topik       : {$topik}\n"
            . "Alokasi Waktu     : {$alokasi_waktu}\n"
            . "Tahun Pelajaran   : {$tahun_ajaran}\n"
            . "Metode & Model    : {$metode}\n\n"
            . "=== DATA KURIKULUM & KESIAPAN ===\n"
            . "Capaian Pembelajaran (CP):\n{$cp_deskripsi}\n\n"
            . "Tujuan Pembelajaran (TP):\n{$tp_formatted}\n"
            . "Kesiapan Belajar Murid:\n{$kesiapan_murid}\n\n"
            . "=== INSTRUKSI STRUKTUR DOKUMEN LENGKAP (WAJIB PERSIS BERIKUT) ===\n\n"
            . "1. COVER & JUDUL BESAR:\n"
            . "   <div style='text-align:center; padding:20px; border:2px solid #333; margin-bottom:25px;'>"
            . "   <h1>PERENCANAAN PEMBELAJARAN MENDALAM<br>KURIKULUM MERDEKA</h1>"
            . "   <h3>MATA PELAJARAN: {$mapel_name} | FASE/KELAS: {$fase}/{$kelas}</h3>"
            . "   <h2>BAB: {$topik}</h2>"
            . "   <p><strong>Disusun Oleh:</strong> {$nama_guru}<br><strong>Sekolah:</strong> {$nama_sekolah}<br>Tahun Pelajaran {$tahun_ajaran}</p>"
            . "   </div>\n\n"
            . "2. TABEL IDENTITAS MODUL:\n"
            . "   Tabel rapi memuat Nama Sekolah, Nama Penyusun, Mata Pelajaran, Kelas/Fase/Semester, Alokasi Waktu, Tahun Pelajaran, Sumber Referensi Buku Kemendikbudristek.\n\n"
            . "3. IDENTIFIKASI MENDALAM (DEEP LEARNING):\n"
            . "   - IDENTIFIKASI MURID: Uraikan Aspek Pengetahuan Awal, Aspek Minat, Aspek Latar Belakang, Aspek Kebutuhan Belajar Berdiferensiasi (Visual, Auditori, Kinestetik).\n"
            . "   - IDENTIFIKASI MATERI PELAJARAN: Jenis Pengetahuan (Konseptual, Prosedural [langkah-langkah], Metakognitif [strategi evaluasi]), Kaitan Nyata Kehidupan Sehari-hari.\n"
            . "   - DIMENSI PROFIL LULUSAN: Bernalar Kritis, Kreatif, Mandiri (uraikan deskripsi capaiannya).\n\n"
            . "4. DESAIN & PENGALAMAN BELAJAR PER PERTEMUAN:\n"
            . "   Bagi rencana pembelajaran secara bertahap (misal Pertemuan 1 s/d 6):\n"
            . "   Setiap Pertemuan WAJIB memuat:\n"
            . "   - KOTAK DESAIN PEMBELAJARAN: Tujuan Pembelajaran, Dimensi Profil, Topik, Model, Metode, Media/Alat.\n"
            . "   - PENGALAMAN BELAJAR:\n"
            . "     * Kegiatan Awal (10 Menit): Salam & Doa, Presensi, Apersepsi bermakna, Penyampaian Tujuan & Manfaat, Motivasi Masalah Nyata.\n"
            . "     * Kegiatan Inti (70 Menit): Sintaks {$metode} dengan 3 Pilar Deep Learning: MEMAHAMI (Mindful), MENGAPLIKASI (Meaningful), MEREFLEKSI (Mindful & Joyful).\n"
            . "     * Kegiatan Penutup (10 Menit): Kesimpulan bersama, Evaluasi singkat/kuis lisan, Umpan balik, Rencana pertemuan berikutnya, Doa & Salam.\n\n"
            . "5. ASESMEN PEMBELAJARAN (Ringkasan Diagnostik, Formatif, Sumatif).\n\n"
            . "6. DAFTAR PUSTAKA (Buku Guru & Siswa Kemendikbudristek Edisi Revisi).\n\n"
            . "7. LEMBAR PENGESAHAN:\n"
            . "   Tabel tanda tangan Mengetahui Kepala Sekolah ({$nama_kepsek}) dan Guru Mata Pelajaran ({$nama_guru}).\n\n"
            . "8. LAMPIRAN I — LEMBAR KERJA MURID (LKPD):\n"
            . "   LKPD Lengkap untuk eksplorasi kelompok (Tabel penyelidikan data, pertanyaan pemantik, penarikan kesimpulan, dan soal latihan penerapan).\n\n"
            . "9. LAMPIRAN II — ASESMEN PEMBELAJARAN:\n"
            . "   Asesmen Diagnostik, Lembar Observasi Formatif Diskusi, Kisi-kisi Sumatif, 5 Butir Soal Kuis, Kunci Jawaban & Pedoman Penskoran.\n\n"
            . "10. LAMPIRAN III — RUBRIK PENILAIAN LENGKAP:\n"
            . "   Rubrik Diskusi Kelompok, Rubrik Presentasi, Rubrik Sikap Profil Lulusan, Rubrik Tes Tertulis, dan Tabel Konversi Nilai & Kriteria Ketuntasan.";
    }

    if (!empty($ref_skeleton)) {
        $prompt .= "\n\n=== CONTOH STRUKTUR REFERENSI DOKUMEN PENGGUNA ===\n"
                 . "Gunakan gaya bahasa dan susunan berikut sebagai acuan tambahan:\n"
                 . mb_substr($ref_skeleton, 0, 3000);
    }

    $response = AIModel::generate($pdo, $prompt, $system_instruction);

    echo json_encode($response);
}

function ai_generator_save($pdo) {
    if (!is_logged_in()) redirect('index.php');

    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $id_ta = $_SESSION['id_ta_aktif'] ?? 0;

    $data = [
        'id_guru' => $id_guru,
        'id_ta' => $id_ta,
        'jenis_perangkat' => $_POST['jenis'] ?? 'Modul Ajar Deep Learning',
        'judul' => $_POST['judul'] ?? 'Modul Ajar',
        'konten_html' => $_POST['konten_html'] ?? '',
        'input_metadata' => [
            'mapel' => $_POST['mapel'] ?? '',
            'kelas' => $_POST['kelas'] ?? '',
            'fase' => $_POST['fase'] ?? '',
            'topik' => $_POST['topik'] ?? ''
        ]
    ];

    if (AIGeneratorModel::saveLog($pdo, $data)) {
        $_SESSION['pesan_sukses'] = "Dokumen AI berhasil disimpan.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menyimpan dokumen.";
    }
    
    redirect('index.php?mod=ai_generator');
}

function ai_generator_delete($pdo) {
    if (!is_logged_in()) redirect('index.php');

    $id_log = $_GET['id'] ?? 0;
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;

    if (AIGeneratorModel::deleteLog($pdo, $id_log, $id_guru)) {
        $_SESSION['pesan_sukses'] = "Dokumen berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus dokumen.";
    }
    
    redirect('index.php?mod=ai_generator');
}

function ai_generator_export($pdo) {
    if (!is_logged_in()) redirect('index.php');
    
    $id_log = $_GET['id'] ?? 0;
    $log = AIGeneratorModel::getLogById($pdo, $id_log);
    
    if (!$log) {
        $_SESSION['pesan_error'] = "Dokumen tidak ditemukan.";
        redirect('index.php?mod=ai_generator');
    }

    require_once __DIR__ . '/../models/ProfilSekolahModel.php';
    $sekolah = ProfilSekolahModel::getProfil($pdo);

    require_once __DIR__ . '/../../dompdf_lib/dompdf/autoload.inc.php';
    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf\Dompdf($options);

    ob_start();
    include __DIR__ . '/../views/ai_generator_print.php';
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = "Perangkat_AI_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $log['judul']) . ".pdf";
    $dompdf->stream($filename, ["Attachment" => false]);
    exit;
}

function ai_generator_preview($pdo) {
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $id_log = intval($_GET['id'] ?? 0);
    $log = AIGeneratorModel::getLogById($pdo, $id_log);

    if (!$log) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Dokumen tidak ditemukan.']);
        return;
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'data' => [
            'id_log' => $log['id_log'],
            'judul' => $log['judul'],
            'jenis_perangkat' => $log['jenis_perangkat'],
            'konten_html' => $log['konten_html'],
            'created_at' => $log['created_at']
        ]
    ]);
}

function ai_generator_edit($pdo) {
    if (!is_logged_in()) redirect('index.php');
    
    $id_log = intval($_GET['id'] ?? 0);
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    
    $log = AIGeneratorModel::getLogById($pdo, $id_log);
    
    if (!$log || $log['id_guru'] != $id_guru) {
        $_SESSION['pesan_error'] = "Dokumen tidak ditemukan atau Anda tidak memiliki akses.";
        redirect('index.php?mod=ai_generator');
    }
    
    include __DIR__ . '/../views/ai_generator_edit.php';
}

function ai_generator_update($pdo) {
    if (!is_logged_in()) redirect('index.php');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php?mod=ai_generator');
    }
    
    $id_log = intval($_POST['id_log'] ?? 0);
    $id_guru = $_SESSION['id_guru_terkait'] ?? 0;
    $judul = trim($_POST['judul'] ?? '');
    $konten_html = $_POST['konten_html'] ?? '';
    
    if (empty($judul)) {
        $_SESSION['pesan_error'] = "Judul dokumen tidak boleh kosong.";
        redirect("index.php?mod=ai_generator&act=edit&id=" . $id_log);
        return;
    }
    
    if (AIGeneratorModel::updateLog($pdo, $id_log, $id_guru, $judul, $konten_html)) {
        $_SESSION['pesan_sukses'] = "Dokumen berhasil diperbarui.";
    } else {
        $_SESSION['pesan_error'] = "Gagal memperbarui dokumen.";
    }
    
    redirect('index.php?mod=ai_generator');
}
?>
