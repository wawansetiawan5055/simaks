<?php
// app/controllers/LmsController.php

require_once __DIR__ . '/../models/LmsModel.php';

function lms_dashboard() {
    global $pdo;
    require_access('lms', 'dashboard');
    
    $user_id = $_SESSION['user_id'];
    $user_roles = user_roles();
    
    $data = [];
    
    $is_siswa = in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles) && !in_array('Kepala Sekolah', $user_roles);
    
    if (!$is_siswa) {
        $id_guru_login = $_SESSION['id_guru_terkait'] ?? null;
        // Manajemen (Admin, Kepala Sekolah, Kurikulum, TU) atau user tanpa tautan guru dapat memonitor semua materi
        $is_management = in_array('Admin', $user_roles) || in_array('Kepala Sekolah', $user_roles) || in_array('Kurikulum', $user_roles) || in_array('TU', $user_roles) || empty($id_guru_login);
        if ($is_management) {
            $id_guru_login = null; // Lihat semua materi & tugas sekolah
        }
        // Dashboard Guru / Manajemen LMS
        $data['materi_count'] = LmsModel::countMateriByGuru($pdo, $id_guru_login);
        $data['tugas_count'] = LmsModel::countTugasByGuru($pdo, $id_guru_login);
        $data['pengumpulan_pending'] = LmsModel::countPengumpulanPending($pdo, $id_guru_login);
        $data['active_tasks_progress'] = LmsModel::getActiveTasksProgress($pdo, $id_guru_login);
        include __DIR__ . '/../views/lms_guru_dashboard.php';
    } else {
        // Dashboard Siswa LMS
        $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
        
        // Fallback jika session belum terisi
        if (!$id_siswa_login) {
            $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
            $stmt_s->execute([$user_id]);
            $id_siswa_login = $stmt_s->fetchColumn() ?: 0;
            if ($id_siswa_login) $_SESSION['id_siswa_terkait'] = $id_siswa_login;
        }
        
        $id_ta_aktif = $_SESSION['id_ta_aktif'] ?? 0;
        $data['materi_available'] = LmsModel::getMateriForSiswa($pdo, $id_siswa_login);
        $data['tugas_pending'] = LmsModel::getTugasPendingForSiswa($pdo, $id_siswa_login);
        $data['mapel_count'] = LmsModel::countMapelForSiswa($pdo, $id_siswa_login, $id_ta_aktif);
        $data['tugas_done_count'] = LmsModel::countTugasSelesaiForSiswa($pdo, $id_siswa_login);
        $data['siswa'] = LmsModel::getSiswaDetail($pdo, $id_siswa_login, $id_ta_aktif);
        
        // --- Inject Portal Dashboard Summary ---
        require_once __DIR__ . '/../models/SiswaPortalModel.php';
        $data['portal_summary'] = SiswaPortalModel::getDashboardSummary($pdo, $id_siswa_login, $id_ta_aktif);
        
        include __DIR__ . '/../views/lms_siswa_dashboard.php';
    }
}

function lms_materi_list() {
    global $pdo;
    require_access('lms', 'materi_list'); // Gunakan dynamic RBAC
    
    $user_id = $_SESSION['user_id'];
    $user_roles = user_roles();
    
    // Nilai default untuk view
    $current_guru_id = 0;
    $can_manage_all   = false;
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? null;
    $is_siswa = in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles) && !in_array('Kepala Sekolah', $user_roles);
    $is_management = in_array('Admin', $user_roles) || in_array('Kepala Sekolah', $user_roles) || in_array('Kurikulum', $user_roles) || in_array('TU', $user_roles) || empty($id_guru_login);

    if (!$is_siswa) {
        if ($is_management) {
            $id_guru_login  = null;  // Manajemen lihat semua
            $can_manage_all = true;
            $stmt_mapel = $pdo->query("SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC");
            $mapel_list = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $current_guru_id = (int)$id_guru_login;
            $stmt_mapel = $pdo->prepare("
                SELECT DISTINCT m.id_mapel, m.nama_mapel 
                FROM mapel m 
                JOIN guru_mapel gm ON m.id_mapel = gm.id_mapel 
                WHERE gm.id_guru = ?
                ORDER BY m.nama_mapel ASC
            ");
            $stmt_mapel->execute([$current_guru_id]);
            $mapel_list = $stmt_mapel->fetchAll(PDO::FETCH_ASSOC);
        }
        $materi = LmsModel::getMateriByGuru($pdo, $id_guru_login);
    } else {
        $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
        if (!$id_siswa_login) {
            $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
            $stmt_s->execute([$user_id]);
            $id_siswa_login = $stmt_s->fetchColumn() ?: 0;
            if ($id_siswa_login) $_SESSION['id_siswa_terkait'] = $id_siswa_login;
        }
        $materi = LmsModel::getMateriForSiswa($pdo, $id_siswa_login);
        
        // Ambil mapel unik dari materi siswa
        $stmt_m_s = $pdo->prepare("
            SELECT DISTINCT m.id_mapel, m.nama_mapel 
            FROM lms_materi lm
            JOIN mapel m ON lm.id_mapel = m.id_mapel
            ORDER BY m.nama_mapel ASC
        ");
        $stmt_m_s->execute();
        $mapel_list = $stmt_m_s->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filter parameters
    $id_mapel_filter = (int)($_GET['id_mapel'] ?? ($mapel_list[0]['id_mapel'] ?? 0));
    $tingkat_filter  = $_GET['tingkat'] ?? 'X';
    $semester_filter = $_GET['semester'] ?? 'Ganjil';
    $view_mode       = $_GET['view'] ?? 'tree'; // 'tree' (Daftar Isi Buku) atau 'grid' (Card)

    // Ambil struktur kurikulum pohon (Bab -> Sub-Bab -> Materi)
    $curriculum_tree = ['bab_list' => [], 'standalone_materi' => []];
    if ($id_mapel_filter) {
        $curriculum_tree = LmsModel::getCurriculumTree($pdo, $id_mapel_filter, $tingkat_filter, $semester_filter, $current_guru_id);
    }
    
    include __DIR__ . '/../views/lms_materi_list.php';
}

function lms_materi_delete() {
    global $pdo;
    require_access('lms', 'materi_list');
    
    $id = $_GET['id'] ?? 0;
    if ($id) {
        try {
            // Periksa kepemilikan materi sebelum menghapus
            $materi_to_delete = LmsModel::getMateriById($pdo, $id);
            if (!$materi_to_delete) {
                $_SESSION['error'] = "Materi tidak ditemukan.";
                redirect('index.php?mod=lms&act=materi_list');
            }
            $user_roles      = user_roles();
            $id_guru_login   = $_SESSION['id_guru_terkait'] ?? null;
            $is_pure_admin   = in_array('Admin', $user_roles) && ((int)$id_guru_login === 0 || $id_guru_login === null);
            // Hanya pemilik materi atau Admin murni yang boleh menghapus
            if (!$is_pure_admin && (int)$materi_to_delete['id_guru'] !== (int)$id_guru_login) {
                $_SESSION['error'] = "Anda tidak memiliki akses untuk menghapus materi milik guru lain.";
                redirect('index.php?mod=lms&act=materi_list');
            }
            LmsModel::deleteMateri($pdo, $id);
            $_SESSION['success'] = "Materi berhasil dihapus.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    redirect('index.php?mod=lms&act=materi_list');
}

function lms_materi_edit() {
    global $pdo;
    require_access('lms', 'materi_list');
    
    $id = $_GET['id'] ?? 0;

    // Periksa kepemilikan materi sebelum memperbolehkan edit
    $materi_to_check = LmsModel::getMateriById($pdo, $id);
    if (!$materi_to_check) {
        $_SESSION['error'] = "Materi tidak ditemukan.";
        redirect('index.php?mod=lms&act=materi_list');
    }
    $user_roles_edit = user_roles();
    $id_guru_edit    = $_SESSION['id_guru_terkait'] ?? null;
    $is_pure_admin   = in_array('Admin', $user_roles_edit) && ((int)$id_guru_edit === 0 || $id_guru_edit === null);
    if (!$is_pure_admin && (int)$materi_to_check['id_guru'] !== (int)$id_guru_edit) {
        $_SESSION['error'] = "Anda tidak memiliki akses untuk mengubah materi milik guru lain.";
        redirect('index.php?mod=lms&act=materi_list');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Handle multiple TP IDs
            if (isset($_POST['id_tp']) && is_array($_POST['id_tp'])) {
                $_POST['id_tp'] = implode(',', $_POST['id_tp']);
            }
            
            LmsModel::updateMateri($pdo, $id, $_POST, $_FILES);
            
            // Update soal juga
            $post_questions = $_POST['questions'] ?? ($_POST['soal_data'] ?? []);
            if (!empty($post_questions)) {
                LmsModel::saveMateriSoal($pdo, $id, $post_questions, $_FILES);
            }

            $_SESSION['success'] = "Materi berhasil diperbarui.";
            redirect('index.php?mod=lms&act=materi_list');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    $materi = LmsModel::getMateriById($pdo, $id);
    $soal_list = LmsModel::getSoalByMateri($pdo, $id);
    
    // Data untuk dropdown
    $user_id = $_SESSION['user_id'];
    $id_guru = $_SESSION['id_guru_terkait'] ?? null;
    $mapel_list = LmsModel::getMapelByGuru($pdo, $id_guru);
    $perangkat_list = LmsModel::getPerangkatByGuru($pdo, $id_guru);
    
    include __DIR__ . '/../views/lms_materi_edit.php';
}

function lms_materi_detail() {
    global $pdo;
    $id_materi = $_GET['id'] ?? 0;
    if (!$id_materi) {
        header("Location: index.php?mod=lms&act=materi_list");
        exit;
    }
    
    $materi = LmsModel::getMateriById($pdo, $id_materi);
    if (!$materi) {
        die("Materi tidak ditemukan.");
    }
    
    $user_roles = user_roles();
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? null;
    $is_pure_admin = in_array('Admin', $user_roles) && ((int)$id_guru_login === 0 || $id_guru_login === null);

    // Cek kepemilikan bagi Guru/Kurikulum
    if ((in_array('Guru', $user_roles) || in_array('Kurikulum', $user_roles)) && !in_array('Siswa', $user_roles)) {
        if (!$is_pure_admin && (int)$materi['id_guru'] !== (int)$id_guru_login) {
            die("Anda tidak memiliki akses ke materi ini.");
        }
    }
    
    // Jika terintegrasi dengan perangkat, ambil kontennya
    $perangkat = null;
    // Ambil data CP & TP jika ada
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

    // Ambil tugas terkait jika ada
    $tugas_terkait = LmsModel::getTugasByMateri($pdo, $id_materi);

    $user_roles = user_roles();
    $soal_list = LmsModel::getSoalByMateri($pdo, $id_materi);
    $has_submitted = false;
    if (in_array('Siswa', $user_roles)) {
        $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
        if (!$id_siswa) {
            $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
            $stmt_s->execute([$_SESSION['user_id']]);
            $id_siswa = $stmt_s->fetchColumn() ?: 0;
        }
        if ($id_siswa) {
            $has_submitted = LmsModel::hasSubmittedQuiz($pdo, $id_materi, $id_siswa);
            $stmt_k = $pdo->prepare("SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif' ORDER BY id_penempatan DESC LIMIT 1");
            $stmt_k->execute([$id_siswa]);
            $id_kelas_siswa = $stmt_k->fetchColumn() ?: null;
            LmsModel::recordStudentCheckin($pdo, $id_materi, $id_siswa, $id_kelas_siswa);
        }
    }
    
    // Ambil daftar diskusi & tanya jawab
    $diskusi_list = LmsModel::getDiskusiByMateri($pdo, $id_materi);

    // Ambil parameter mode penugasan & progres Learning Path (Titian Tangga)
    $id_tugas = (int)($_GET['tugas'] ?? ($_GET['id_tugas'] ?? 0));
    $id_siswa_current = $id_siswa ?? 0;
    $is_penugasan = !in_array('Siswa', $user_roles) || ($id_tugas > 0);
    
    // Ambil data progres siswa
    $materi_progress = LmsModel::getMateriProgress($pdo, $id_materi, $id_siswa_current, $id_tugas);
    
    include __DIR__ . '/../views/lms_materi_detail.php';
}

function lms_materi_quiz_submit() {
    global $pdo;
    require_access('lms', 'materi_list');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $id_materi = $_POST['id_materi'];
            $jawaban = $_POST['jawaban'] ?? [];
            
            $refleksi_data = [];
            if (isset($_POST['refleksi_siswa'])) {
                foreach ($_POST['refleksi_siswa'] as $idx => $isi) {
                    $refleksi_data[] = [
                        'pertanyaan' => $_POST['refleksi_soal'][$idx],
                        'jawaban' => $isi
                    ];
                }
            }

            $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
            if (!$id_siswa) {
                $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
                $stmt_s->execute([$_SESSION['user_id']]);
                $id_siswa = $stmt_s->fetchColumn() ?: 0;
            }
            
            LmsModel::submitJawabanMateri($pdo, $id_siswa, $id_materi, $jawaban, $refleksi_data);
            LmsModel::recordStudentCheckout($pdo, $id_materi, $id_siswa);
            
            $id_tugas = (int)($_POST['id_tugas'] ?? 0);
            LmsModel::markMateriStage($pdo, $id_materi, $id_siswa, 4, $id_tugas);
            
            $_SESSION['pesan_sukses'] = 'Asesmen formatif berhasil diselesaikan!';
            if ($id_tugas > 0) {
                header('Location: ' . BASE_URL . 'siswa_portal/tugas_submit?id_tugas=' . $id_tugas);
            } elseif (in_array('Siswa', $_SESSION['roles'] ?? [])) {
                header('Location: ' . BASE_URL . 'siswa_portal/materi_detail?id=' . $id_materi);
            } else {
                header('Location: ' . BASE_URL . 'lms/materi_detail?id=' . $id_materi);
            }
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Gagal menyimpan jawaban: ' . $e->getMessage();
            $id_tugas = (int)($_POST['id_tugas'] ?? 0);
            if ($id_tugas > 0) {
                header('Location: ' . BASE_URL . 'siswa_portal/tugas_submit?id_tugas=' . $id_tugas);
            } else {
                header('Location: ' . BASE_URL . 'lms/materi_detail?id=' . $id_materi);
            }
            exit;
        }
    }
}

function lms_materi_quiz_template() {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Tipe (PG/Essay)');
    $sheet->setCellValue('C1', 'Pertanyaan');
    $sheet->setCellValue('D1', 'Opsi A');
    $sheet->setCellValue('E1', 'Opsi B');
    $sheet->setCellValue('F1', 'Opsi C');
    $sheet->setCellValue('G1', 'Opsi D');
    $sheet->setCellValue('H1', 'Opsi E');
    $sheet->setCellValue('I1', 'Kunci (A/B/C/D/E)');
    
    // Contoh Data
    $sheet->setCellValue('A2', '1');
    $sheet->setCellValue('B2', 'PG');
    $sheet->setCellValue('C2', 'Apa warna bendera Indonesia?');
    $sheet->setCellValue('D2', 'Merah Putih');
    $sheet->setCellValue('E2', 'Biru');
    $sheet->setCellValue('F2', 'Hijau');
    $sheet->setCellValue('G2', 'Kuning');
    $sheet->setCellValue('H2', 'Hitam');
    $sheet->setCellValue('I2', 'A');
    
    $sheet->setCellValue('A3', '2');
    $sheet->setCellValue('B3', 'Essay');
    $sheet->setCellValue('C3', 'Jelaskan makna kemerdekaan bagi Anda!');

    // Instruksi Media
    $sheet->setCellValue('A5', 'CATATAN PENTING:');
    $sheet->setCellValue('A6', '1. Gunakan file ini untuk upload teks soal dalam jumlah banyak.');
    $sheet->setCellValue('A7', '2. Untuk menambahkan GAMBAR atau AUDIO, silakan gunakan fitur EDIT di aplikasi setelah materi berhasil diupload.');
    $sheet->mergeCells('A5:I5');
    $sheet->mergeCells('A6:I6');
    $sheet->mergeCells('A7:I7');
    
    // Style header
    $headerRange = 'A1:I1';
    $sheet->getStyle($headerRange)->getFont()->setBold(true);
    $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDEEAF6');
    
    foreach (range('A','I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Template_Soal_Materi.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}

function lms_materi_upload() {
    global $pdo;
    require_access('lms', 'materi_list');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $id_guru = $_SESSION['id_guru_terkait'] ?? null;
            if ($id_guru == 0) $id_guru = null;
            
            // Handle multiple TP IDs
            if (isset($_POST['id_tp']) && is_array($_POST['id_tp'])) {
                $_POST['id_tp'] = implode(',', $_POST['id_tp']);
            }

            $result = LmsModel::uploadMateri($pdo, $_POST, $_FILES, $id_guru);
            
            // Simpan soal dari form manual jika ada
            $all_questions = $_POST['questions'] ?? ($_POST['soal_data'] ?? []);
            
            // Simpan soal dari Excel jika diupload
            if (!empty($_FILES['quiz_excel']['name'])) {
                $excel_questions = LmsModel::parseQuizExcel($_FILES['quiz_excel']['tmp_name']);
                $all_questions = array_merge($all_questions, $excel_questions);
            }

            if (!empty($all_questions)) {
                LmsModel::saveMateriSoal($pdo, $result, $all_questions, $_FILES);
            }

            $_SESSION['success'] = "Materi berhasil diupload.";
            redirect('index.php?mod=lms&act=materi_list');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? null;
    $user_roles = user_roles();
    if (in_array('Admin', $user_roles)) {
        $mapel_list = LmsModel::getAllMapel($pdo);
    } else {
        $mapel_list = LmsModel::getMapelByGuru($pdo, $id_guru_login);
    }
    $id_guru_lms = $id_guru_login;
    if ($id_guru_lms == 0) $id_guru_lms = null;
    $perangkat_list = LmsModel::getPerangkatByGuru($pdo, $id_guru_lms);
    include __DIR__ . '/../views/lms_materi_upload.php';
}

function lms_tugas_create() {
    global $pdo;
    require_access('lms', 'tugas_list');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $id_guru = $_SESSION['id_guru_terkait'] ?? null;
            if ($id_guru == 0) $id_guru = null;
            $result = LmsModel::createTugas($pdo, $_POST, $id_guru);
            $_SESSION['success'] = "Tugas berhasil dibuat.";
            redirect('index.php?mod=lms&act=tugas_list');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    $user_roles = user_roles();
    if (in_array('Admin', $user_roles)) {
        $materi_list = LmsModel::getMateriByGuru($pdo, null); // Admin see all? or maybe need new method
        // For admin, we might need all materi.
        $sql = "SELECT m.*, mp.nama_mapel FROM lms_materi m JOIN mapel mp ON m.id_mapel = mp.id_mapel ORDER BY m.created_at DESC";
        $materi_list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        $rombel_list = LmsModel::getRombelByTingkat($pdo, 'X'); // Default X, JS will handle? 
        // Better fetch all rombels for admin
        $rombel_list = $pdo->query("SELECT id_kelas, nama_kelas, tingkat FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
        $materi_list = LmsModel::getMateriByGuru($pdo, $id_guru_login);
        // Fetch all rombels taught by this guru
        $sql = "SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat 
                FROM kelas k 
                JOIN jadwal_mengajar jm ON k.id_kelas = jm.id_kelas
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                WHERE gm.id_guru = ? ORDER BY k.tingkat ASC, k.nama_kelas ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_guru_login]);
        $rombel_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    include __DIR__ . '/../views/lms_tugas_create.php';
}

function lms_tugas_submit() {
    global $pdo;
    require_access('lms', 'tugas_list');
    
    // Ambil ID Siswa terkait - diperlukan untuk POST dan GET
    $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa_login) {
        $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
        $stmt_s->execute([$_SESSION['user_id']]);
        $id_siswa_login = $stmt_s->fetchColumn() ?: 0;
        if ($id_siswa_login) $_SESSION['id_siswa_terkait'] = $id_siswa_login;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = LmsModel::submitTugas($pdo, $_POST, $_FILES, $id_siswa_login);
            $_SESSION['success'] = "Tugas berhasil dikumpulkan.";
            redirect('index.php?mod=lms&act=tugas_list');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    $id_tugas = $_GET['id_tugas'] ?? 0;
    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    
    if ($tugas && !empty($tugas['id_materi'])) {
        // Ini adalah tugas berbasis Modul (Learning Path)
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

        // Ambil progress saat ini
        $stmt_prog = $pdo->prepare("SELECT * FROM lms_tugas_progress WHERE id_tugas = ? AND id_siswa = ?");
        $stmt_prog->execute([$id_tugas, $id_siswa_login]);
        $progress = $stmt_prog->fetch(PDO::FETCH_ASSOC);
        
        if (!$progress) {
            $pdo->prepare("INSERT INTO lms_tugas_progress (id_tugas, id_siswa) VALUES (?, ?)")->execute([$id_tugas, $id_siswa_login]);
            $progress = ['stage_instruksi'=>0, 'stage_diagnostik'=>0, 'stage_materi'=>0, 'stage_essay'=>0, 'stage_formatif'=>0, 'stage_refleksi'=>0];
        }

        include __DIR__ . '/../views/lms_tugas_learning_path.php';
    } else {
        // Ini adalah tugas biasa (Upload File)
        include __DIR__ . '/../views/lms_tugas_submit.php';
    }
}

function lms_tugas_detail() {
    global $pdo;
    require_access('lms', 'tugas_list');

    $id_tugas = $_GET['id'] ?? 0;
    if (!$id_tugas) redirect('index.php?mod=lms&act=tugas_list');

    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    if (!$tugas) {
        $_SESSION['error'] = 'Tugas tidak ditemukan.';
        redirect('index.php?mod=lms&act=tugas_list');
    }

    $user_roles = user_roles();
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? null;
    $is_pure_admin = in_array('Admin', $user_roles) && ((int)$id_guru_login === 0 || $id_guru_login === null);

    // Cek kepemilikan bagi Guru/Kurikulum
    if ((in_array('Guru', $user_roles) || in_array('Kurikulum', $user_roles)) && !in_array('Siswa', $user_roles)) {
        if (!$is_pure_admin && (int)$tugas['id_guru'] !== (int)$id_guru_login) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke tugas ini.';
            redirect('index.php?mod=lms&act=tugas_list');
        }
    }

    $submissions = LmsModel::getTaskSubmissions($pdo, $id_tugas);

    include __DIR__ . '/../views/lms_tugas_detail.php';
}

function lms_tugas_student_detail() {
    global $pdo;
    require_access('lms', 'tugas_list');

    $id_tugas = $_GET['id'] ?? 0;
    $id_siswa = $_GET['id_siswa'] ?? 0;
    if (!$id_tugas || !$id_siswa) redirect('index.php?mod=lms&act=tugas_list');

    $detail = LmsModel::getTaskSubmissionDetail($pdo, $id_tugas, $id_siswa);
    if (!$detail) {
        $_SESSION['error'] = 'Detail siswa tidak ditemukan untuk tugas ini.';
        redirect('index.php?mod=lms&act=tugas_detail&id=' . $id_tugas);
    }

    include __DIR__ . '/../views/lms_tugas_student_detail.php';
}

function lms_tugas_list() {
    global $pdo;
    require_access('lms', 'tugas_list');

    $user_id = $_SESSION['user_id'];
    $user_roles = user_roles();

    $is_siswa = in_array('Siswa', $user_roles) && !in_array('Admin', $user_roles) && !in_array('Guru', $user_roles) && !in_array('Kepala Sekolah', $user_roles);

    if (!$is_siswa) {
        $current_guru_id = 0;
        $can_manage_all   = false;
        
        $id_guru_login = $_SESSION['id_guru_terkait'] ?? null;
        $is_management = in_array('Admin', $user_roles) || in_array('Kepala Sekolah', $user_roles) || in_array('Kurikulum', $user_roles) || in_array('TU', $user_roles) || empty($id_guru_login);
        if ($is_management) {
            $id_guru_login = null;
            $can_manage_all = true;
        } elseif ((int)$id_guru_login === 0) {
            $id_guru_login = null;
        }
        $current_guru_id = (int)$id_guru_login;
        $tugas = LmsModel::getTugasByGuru($pdo, $id_guru_login);
        include __DIR__ . '/../views/lms_tugas_list_guru.php';
    } else {
        // Gunakan id_siswa (bukan id_pengguna) untuk query penempatan_siswa
        $id_siswa_login = $_SESSION['id_siswa_terkait'] ?? 0;
        if (!$id_siswa_login) {
            $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
            $stmt_s->execute([$user_id]);
            $id_siswa_login = $stmt_s->fetchColumn() ?: 0;
            if ($id_siswa_login) $_SESSION['id_siswa_terkait'] = $id_siswa_login;
        }
        $tugas = LmsModel::getTugasForSiswa($pdo, $id_siswa_login);
        include __DIR__ . '/../views/lms_tugas_list_siswa.php';
    }
}

function lms_koreksi_list() {
    global $pdo;
    require_access('lms', 'tugas_list'); // Asumsi koreksi bagian dari tugas
    
    $id_guru_login = $_SESSION['id_guru_terkait'] ?? 0;
    $pengumpulan = LmsModel::getPengumpulanPendingByGuru($pdo, $id_guru_login);
    include __DIR__ . '/../views/lms_koreksi_list.php';
}

function lms_koreksi_detail() {
    global $pdo;
    require_access('lms', 'tugas_list');
    
    $id_pengumpulan = $_GET['id'] ?? 0;
    $pengumpulan = LmsModel::getPengumpulanById($pdo, $id_pengumpulan);

    $student_work = [
        'diagnostik' => [],
        'essay' => [],
        'formatif' => [],
        'refleksi' => []
    ];

    if ($pengumpulan) {
        $id_tugas = $pengumpulan['id_tugas'];
        $id_siswa = $pengumpulan['id_siswa'];
        $tugas_detail = LmsModel::getTugasById($pdo, $id_tugas);
        $id_materi = $tugas_detail['id_materi'] ?? null;

        $student_work['diagnostik'] = LmsModel::getStudentDiagnostikAnswers($pdo, $id_tugas, $id_siswa);
        $student_work['essay'] = LmsModel::getStudentEssayAnswers($pdo, $id_tugas, $id_siswa);
        $student_work['refleksi'] = LmsModel::getStudentRefleksiAnswers($pdo, $id_materi, $id_siswa);
        $student_work['formatif'] = LmsModel::getStudentFormatifResults($pdo, $id_materi, $id_siswa);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $id_tugas = $pengumpulan['id_tugas'];
            $id_siswa = $pengumpulan['id_siswa'];

            if (isset($_POST['detailed_grading'])) {
                $scores = [
                    'score_diagnostik' => $_POST['score_diagnostik'] ?? 0,
                    'score_materi' => $_POST['score_materi'] ?? 0,
                    'score_tugas_materi' => $_POST['score_tugas_materi'] ?? 0,
                    'score_formatif' => $_POST['score_formatif'] ?? 0,
                    'score_refleksi' => $_POST['score_refleksi'] ?? 0,
                ];
                LmsModel::saveDetailedScores($pdo, $id_tugas, $id_siswa, $scores);
                
                // Jika Guru mengisi 'nilai', gunakan itu. Jika tidak, biarkan p.nilai diupdate manual atau otomatis
                $final_nilai = $_POST['nilai'];
            } else {
                $final_nilai = $_POST['nilai'];
            }

            LmsModel::nilaiPengumpulan($pdo, $id_pengumpulan, $final_nilai, $_POST['catatan_guru'] ?? '');
            $_SESSION['success'] = "Penilaian berhasil disimpan.";
            redirect('index.php?mod=lms&act=tugas_detail&id=' . $id_tugas);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    include __DIR__ . '/../views/lms_koreksi_detail.php';
}

function lms_tugas_edit() {
    global $pdo;
    require_access('lms', 'tugas_list');
    
    $id_tugas = $_GET['id'] ?? 0;
    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    
    if (!$tugas) {
        $_SESSION['error'] = "Tugas tidak ditemukan.";
        redirect('index.php?mod=lms&act=tugas_list');
    }
    
    $user_roles_edit = user_roles();
    $id_guru_edit    = $_SESSION['id_guru_terkait'] ?? null;
    $is_pure_admin   = in_array('Admin', $user_roles_edit) && ((int)$id_guru_edit === 0 || $id_guru_edit === null);
    if (!$is_pure_admin && (int)$tugas['id_guru'] !== (int)$id_guru_edit) {
        $_SESSION['error'] = "Anda tidak memiliki akses untuk mengubah tugas milik guru lain.";
        redirect('index.php?mod=lms&act=tugas_list');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            LmsModel::updateTugas($pdo, $id_tugas, $_POST);
            $_SESSION['success'] = "Tugas berhasil diperbarui.";
            redirect('index.php?mod=lms&act=tugas_list');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    include __DIR__ . '/../views/lms_tugas_create.php'; // Reuse create form
}

function lms_tugas_delete() {
    global $pdo;
    require_access('lms', 'tugas_list');
    
    $id_tugas = $_GET['id'] ?? 0;
    
    // Periksa kepemilikan tugas
    $tugas_to_delete = LmsModel::getTugasById($pdo, $id_tugas);
    if (!$tugas_to_delete) {
        $_SESSION['error'] = "Tugas tidak ditemukan.";
        redirect('index.php?mod=lms&act=tugas_list');
    }
    
    $user_roles_del = user_roles();
    $id_guru_del    = $_SESSION['id_guru_terkait'] ?? null;
    $is_pure_admin_del = in_array('Admin', $user_roles_del) && ((int)$id_guru_del === 0 || $id_guru_del === null);
    if (!$is_pure_admin_del && (int)$tugas_to_delete['id_guru'] !== (int)$id_guru_del) {
        $_SESSION['error'] = "Anda tidak memiliki akses untuk menghapus tugas milik guru lain.";
        redirect('index.php?mod=lms&act=tugas_list');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            LmsModel::deleteTugas($pdo, $id_tugas);
            $_SESSION['success'] = "Tugas berhasil dihapus.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        redirect('index.php?mod=lms&act=tugas_list');
    }
    
    // Show confirmation page
    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    include __DIR__ . '/../views/lms_tugas_delete.php';
}
function lms_get_cp_ajax() {
    global $pdo;
    $id_mapel = $_GET['id_mapel'] ?? 0;
    $tingkat = $_GET['tingkat'] ?? '';
    $cp_list = LmsModel::getCPByMapel($pdo, $id_mapel, $tingkat);
    echo json_encode($cp_list);
    exit;
}

function lms_get_tp_ajax() {
    global $pdo;
    $id_cp = $_GET['id_cp'] ?? 0;
    $tp_list = LmsModel::getTPByCP($pdo, $id_cp);
    echo json_encode($tp_list);
    exit;
}

// ==========================================
// LEARNING PATH AJAX ENDPOINTS
// ==========================================

function lms_lp_mark_stage() {
    global $pdo;
    header('Content-Type: application/json');
    $id_materi = (int)($_POST['id_materi'] ?? 0);
    $id_tugas  = (int)($_POST['id_tugas'] ?? 0);
    $stage     = (int)($_POST['stage'] ?? 1);
    
    // Ambil ID Siswa terkait
    $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa && isset($_SESSION['user_id'])) {
        $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
        $stmt_s->execute([$_SESSION['user_id']]);
        $id_siswa = $stmt_s->fetchColumn() ?: 0;
    }
    
    if ($id_materi && $id_siswa) {
        $fotoFilename = null;
        if (!empty($_POST['foto_presensi'])) {
            $data = $_POST['foto_presensi'];
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $fotoFilename = 'presensi_' . $id_siswa . '_' . $id_materi . '_' . time() . '.' . ($type === 'png' ? 'png' : 'jpg');
                    
                    $dirs = [
                        __DIR__ . '/../../uploads/presensi_materi/',
                        'D:/BtSoft/wwwroot/simaks.app/uploads/presensi_materi/'
                    ];
                    foreach ($dirs as $dir) {
                        if (!file_exists($dir)) {
                            @mkdir($dir, 0777, true);
                        }
                        @file_put_contents($dir . $fotoFilename, $data);
                    }
                }
            }
        }

        // Simpan refleksi jika ada
        if (!empty($_POST['refleksi_siswa']) && is_array($_POST['refleksi_siswa'])) {
            $pdo->prepare("DELETE FROM lms_materi_refleksi WHERE id_materi = ? AND id_siswa = ?")->execute([$id_materi, $id_siswa]);
            $stmt_ref = $pdo->prepare("INSERT INTO lms_materi_refleksi (id_materi, id_siswa, pertanyaan, jawaban, foto_presensi, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            foreach ($_POST['refleksi_siswa'] as $idx => $ans) {
                $q = $_POST['refleksi_soal'][$idx] ?? ("Pertanyaan " . ($idx + 1));
                $stmt_ref->execute([$id_materi, $id_siswa, $q, $ans, $fotoFilename]);
            }
        }

        $prog = LmsModel::markMateriStage($pdo, $id_materi, $id_siswa, $stage, $id_tugas, null, $fotoFilename);
        
        // Tandai juga di lms_tugas_progress jika ada id_tugas
        if ($id_tugas > 0) {
            $stmt_check = $pdo->prepare("SELECT id_progress FROM lms_tugas_progress WHERE id_tugas = ? AND id_siswa = ?");
            $stmt_check->execute([$id_tugas, $id_siswa]);
            if (!$stmt_check->fetch()) {
                $pdo->prepare("INSERT INTO lms_tugas_progress (id_tugas, id_siswa) VALUES (?, ?)")->execute([$id_tugas, $id_siswa]);
            }
            $colMap = [
                1 => 'stage_instruksi = 1, stage_diagnostik = 1',
                2 => 'stage_materi = 1',
                3 => 'stage_materi = 1',
                4 => 'stage_formatif = 1',
                5 => 'stage_essay = 1',
                6 => 'stage_refleksi = 1'
            ];
            if (isset($colMap[$stage])) {
                $extraSql = ($fotoFilename && $stage == 6) ? ", foto_presensi = " . $pdo->quote($fotoFilename) : "";
                $pdo->prepare("UPDATE lms_tugas_progress SET {$colMap[$stage]} {$extraSql}, updated_at = NOW() WHERE id_tugas = ? AND id_siswa = ?")
                    ->execute([$id_tugas, $id_siswa]);
            }
        }

        echo json_encode(['status' => 'ok', 'progress' => $prog, 'foto_presensi' => $fotoFilename]);
    } else {
        echo json_encode(['status' => 'ok', 'message' => 'Preview / non-student mode']);
    }
    exit;
}

function lms_lp_submit_text() {
    global $pdo;
    $id_tugas = $_POST['id_tugas'] ?? 0;
    $type = $_POST['type'] ?? ''; // diagnostik / essay
    $jawaban = $_POST['jawaban'] ?? '';
    
    // Log for debugging
    $logFile = __DIR__ . '/../../lms_debug.log';
    $logMsg = date('[Y-m-d H:i:s]') . " LMS_DEBUG: lp_submit_text - Tugas: $id_tugas, Type: $type, User: " . ($_SESSION['user_id'] ?? 'NONE') . "\n";
    file_put_contents($logFile, $logMsg, FILE_APPEND);
    
    // Ambil ID Siswa terkait
    $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa) {
        $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
        $stmt_s->execute([$_SESSION['user_id']]);
        $id_siswa = $stmt_s->fetchColumn() ?: 0;
    }
    
    // Simpan jawaban text (diagnostik atau essay) ke tabel lms_tugas_jawaban_text
    // Note: Karena sebelumnya menggunakan LmsModel::submitJawabanMateri, kita buat tabel baru atau simpan di table yang ada.
    // Untuk kesederhanaan, mari simpan di json lms_tugas_progress jika belum ada tabel khusus, atau buat tabel lms_tugas_jawaban_text
    $pdo->prepare("CREATE TABLE IF NOT EXISTS lms_tugas_jawaban_text (
        id_jawaban INT AUTO_INCREMENT PRIMARY KEY,
        id_tugas INT NOT NULL,
        id_siswa INT NOT NULL,
        tipe VARCHAR(20) NOT NULL,
        jawaban TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )")->execute();

    $pdo->prepare("INSERT INTO lms_tugas_jawaban_text (id_tugas, id_siswa, tipe, jawaban) VALUES (?, ?, ?, ?)")
        ->execute([$id_tugas, $id_siswa, $type, $jawaban]);
        
    echo json_encode(['status' => 'success']);
    exit;
}

function lms_lp_submit_formatif() {
    global $pdo;
    $id_tugas = $_POST['id_tugas'] ?? 0;
    $formatif = $_POST['formatif'] ?? [];
    
    // Log for debugging
    $logFile = __DIR__ . '/../../lms_debug.log';
    $logMsg = date('[Y-m-d H:i:s]') . " LMS_DEBUG: lp_submit_formatif - Tugas: $id_tugas, User: " . ($_SESSION['user_id'] ?? 'NONE') . "\n";
    file_put_contents($logFile, $logMsg, FILE_APPEND);
    
    // Ambil ID Siswa terkait
    $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa) {
        $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
        $stmt_s->execute([$_SESSION['user_id']]);
        $id_siswa = $stmt_s->fetchColumn() ?: 0;
    }
    
    // Get id_materi from tugas
    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    if ($tugas) {
        LmsModel::submitJawabanMateri($pdo, $id_siswa, $tugas['id_materi'], $formatif, []);
    }
    
    echo json_encode(['status' => 'success']);
    exit;
}

function lms_lp_submit_refleksi() {
    global $pdo;
    $id_tugas = $_POST['id_tugas'] ?? 0;
    $refleksi_siswa = $_POST['refleksi'] ?? []; // Array index numerik
    
    // Log for debugging
    $logFile = __DIR__ . '/../../lms_debug.log';
    $logMsg = date('[Y-m-d H:i:s]') . " LMS_DEBUG: lp_submit_refleksi - Tugas: $id_tugas, User: " . ($_SESSION['user_id'] ?? 'NONE') . "\n";
    file_put_contents($logFile, $logMsg, FILE_APPEND);
    
    // Ambil ID Siswa terkait
    $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa) {
        $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
        $stmt_s->execute([$_SESSION['user_id']]);
        $id_siswa = $stmt_s->fetchColumn() ?: 0;
    }
    
    $fotoFilename = null;
    if (!empty($_POST['foto_presensi'])) {
        $data = $_POST['foto_presensi'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);
            $data = base64_decode($data);
            if ($data !== false) {
                $fotoFilename = 'presensi_' . $id_siswa . '_' . $id_tugas . '_' . time() . '.' . ($type === 'png' ? 'png' : 'jpg');
                $dirs = [
                    __DIR__ . '/../../uploads/presensi_materi/',
                    'D:/BtSoft/wwwroot/simaks.app/uploads/presensi_materi/'
                ];
                foreach ($dirs as $dir) {
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0777, true);
                    }
                    @file_put_contents($dir . $fotoFilename, $data);
                }
            }
        }
    }

    $tugas = LmsModel::getTugasById($pdo, $id_tugas);
    if ($tugas) {
        $materi = LmsModel::getMateriById($pdo, $tugas['id_materi']);
        $ref_questions = json_decode($materi['refleksi_config'] ?? '[]', true);
        
        $refleksi_data = [];
        foreach ($refleksi_siswa as $idx => $isi) {
            $refleksi_data[] = [
                'pertanyaan' => $ref_questions[$idx] ?? 'Refleksi ' . ($idx + 1),
                'jawaban' => $isi,
                'foto_presensi' => $fotoFilename
            ];
        }
        LmsModel::submitJawabanMateri($pdo, $id_siswa, $tugas['id_materi'], [], $refleksi_data);

        // Update foto_presensi in lms_materi_refleksi if exists
        if ($fotoFilename) {
            $pdo->prepare("UPDATE lms_materi_refleksi SET foto_presensi = ? WHERE id_materi = ? AND id_siswa = ?")
                ->execute([$fotoFilename, $tugas['id_materi'], $id_siswa]);
            $pdo->prepare("UPDATE lms_tugas_progress SET foto_presensi = ? WHERE id_tugas = ? AND id_siswa = ?")
                ->execute([$fotoFilename, $id_tugas, $id_siswa]);
        }
        
        // Simpan ke lms_pengumpulan agar Admin bisa melihat status SELESAI
        $stmt_check = $pdo->prepare("SELECT id_kumpul FROM lms_pengumpulan WHERE id_tugas = ? AND id_siswa = ?");
        $stmt_check->execute([$id_tugas, $id_siswa]);
        if (!$stmt_check->fetch()) {
            $pdo->prepare("INSERT INTO lms_pengumpulan (id_tugas, id_siswa, file_siswa, tgl_upload) VALUES (?, ?, 'Learning Path Completed', NOW())")
                ->execute([$id_tugas, $id_siswa]);
        }

        // Tandai tugas sebagai 'Selesai' di tabel tugas_siswa_status jika diperlukan
        $pdo->prepare("UPDATE tugas_siswa_status SET status = 'Selesai', submitted_at = NOW() WHERE id_tugas = ? AND id_siswa = ?")->execute([$id_tugas, $id_siswa]);
    }
    
    echo json_encode(['status' => 'success', 'foto_presensi' => $fotoFilename]);
    exit;
}
function lms_lp_submit_essay() {
    global $pdo;
    $id_tugas = $_POST['id_tugas'] ?? 0;
    $stage = $_POST['stage'] ?? 0;
    $pertanyaan = $_POST['pertanyaan'] ?? [];
    $jawaban = $_POST['jawaban'] ?? [];
    
    // Log for debugging
    $logFile = __DIR__ . '/../../lms_debug.log';
    $logMsg = date('[Y-m-d H:i:s]') . " LMS_DEBUG: lp_submit_essay - Tugas: $id_tugas, Stage: $stage, User: " . ($_SESSION['user_id'] ?? 'NONE') . "\n";
    file_put_contents($logFile, $logMsg, FILE_APPEND);
    
    // Ambil ID Siswa terkait
    $id_siswa = $_SESSION['id_siswa_terkait'] ?? 0;
    if (!$id_siswa) {
        $stmt_s = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ? LIMIT 1");
        $stmt_s->execute([$_SESSION['user_id']]);
        $id_siswa = $stmt_s->fetchColumn() ?: 0;
    }
    
    if (!empty($pertanyaan)) {
        $sql = "INSERT INTO lms_tugas_jawaban_essay (id_tugas, id_siswa, stage, pertanyaan, jawaban) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        foreach ($pertanyaan as $idx => $q) {
            $ans = $jawaban[$idx] ?? '';
            $stmt->execute([$id_tugas, $id_siswa, $stage, $q, $ans]);
        }
    }

    // Handle File Upload (Foto buku catatan)
    if (!empty($_FILES['file_materi_siswa']['name'])) {
        require_once __DIR__ . '/../../config/secure_upload.php';
        $upload_dir = __DIR__ . '/../../uploads/lms_siswa';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $file_path = SecureFileUpload::upload($_FILES['file_materi_siswa'], $upload_dir, 'image');
        if ($file_path) {
            $pdo->prepare("UPDATE lms_tugas_progress SET file_materi_siswa = ? WHERE id_tugas = ? AND id_siswa = ?")
                ->execute([$file_path, $id_tugas, $id_siswa]);
        }
    }
    
    echo json_encode(['status' => 'success']);
    exit;
}

/**
 * AJAX: AI Generator untuk Rangkuman & Konten Bahan Ajar Lengkap
 */
function lms_ai_generate_materi() {
    global $pdo;
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    require_once __DIR__ . '/../models/AIModel.php';

    $mapel_nama   = trim($_POST['mapel_nama'] ?? '');
    $tingkat      = trim($_POST['tingkat'] ?? 'X');
    $topik        = trim($_POST['topik'] ?? '');
    $cp_deskripsi = trim($_POST['cp_deskripsi'] ?? '');
    $tp_deskripsi = trim($_POST['tp_deskripsi'] ?? '');

    if (empty($topik)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Harap isi Judul Modul / Topik Materi terlebih dahulu.']);
        exit;
    }

    $system_instruction = "Anda adalah Guru Ahli Kurikulum Merdeka yang menyusun bahan ajar digital interaktif untuk SMA Plus Al Manshuriyah. "
        . "Tugas Anda adalah membuat modul bacaan / artikel materi pelajaran yang mendalam, menarik, runtut, dan mudah dipahami murid. "
        . "Jika mata pelajaran mengandung rumus matematika atau sains, WAJIB menggunakan format LaTeX: "
        . "gunakan '$...$' untuk rumus sebaris (inline) dan '$$...$$' untuk persamaan matematika terpisah (block). "
        . "Format keluaran HARUS berupa HTML bersih menggunakan tag: <h3>, <h4>, <p>, <ul>, <ol>, <li>, <strong>, <em>, <div style='...'>, <table border='1' cellpadding='8' style='width:100%; border-collapse:collapse; margin-bottom:15px;'>. "
        . "DILARANG menyertakan tag <html>, <head>, <body>, <style>, atau markdown backticks di luar HTML.";

    $prompt = "Susunlah BAHAN AJAR / RANGKUMAN MATERI PEMBELAJARAN LENGKAP untuk data berikut:\n\n"
        . "Mata Pelajaran: {$mapel_nama}\n"
        . "Tingkat/Kelas : Kelas {$tingkat}\n"
        . "Topik / Bab   : {$topik}\n"
        . "Capaian Pembelajaran (CP): {$cp_deskripsi}\n"
        . "Tujuan Pembelajaran (TP): {$tp_deskripsi}\n\n"
        . "=== STRUKTUR KONTEN YANG HARUS DIBUAT ===\n"
        . "1. APERSEPSI & KONSEP UTAMA: Pengantar menarik dan keterkaitan topik ini dengan kehidupan sehari-hari.\n"
        . "2. URAIAN MATERI MENDALAM: Definisi, konsep kunci, sifat-sifat/teori, serta tabel ringkasan konsep (Gunakan LaTeX $...$ untuk semua simbol/rumus matematika).\n"
        . "3. CONTOH SOAL KONTEKSTUAL & PEMBAHASAN LENGKAP: Berikan 2 contoh soal bertahap beserta langkah-langkah penyelesaiannya secara runtut.\n"
        . "4. RANGKUMAN INTI MATERI: Poin-poin penting yang wajib dikuasai dan dicatat oleh murid.\n\n"
        . "Hasilkan HANYA kode HTML isi materi yang siap ditampilkan di editor LMS.";

    $res = AIModel::generate($pdo, $prompt, $system_instruction);

    ob_end_clean();
    if ($res['success']) {
        echo json_encode(['success' => true, 'html' => $res['text']]);
    } else {
        echo json_encode(['success' => false, 'message' => $res['message']]);
    }
    exit;
}

/**
 * AJAX: AI Generator untuk Soal Latihan & Kuis Interaktif
 */
function lms_ai_generate_soal() {
    global $pdo;
    ob_start();
    header('Content-Type: application/json');

    if (!is_logged_in()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    require_once __DIR__ . '/../models/AIModel.php';

    $mapel_nama = trim($_POST['mapel_nama'] ?? '');
    $tingkat    = trim($_POST['tingkat'] ?? 'X');
    $topik      = trim($_POST['topik'] ?? '');
    $jumlah     = intval($_POST['jumlah'] ?? 5);
    $tipe       = trim($_POST['tipe'] ?? 'PG'); // 'PG', 'Essay', 'matching', 'tf', 'Campuran'
    $kategori   = trim($_POST['kategori'] ?? 'Formatif'); // 'Pretest', 'Diagnostik', 'Formatif', 'Latihan'
    $kesulitan  = trim($_POST['tingkat_kesulitan'] ?? 'sedang'); // 'mudah', 'sedang', 'sulit'

    if ($jumlah < 1) $jumlah = 5;
    if ($jumlah > 20) $jumlah = 20;

    if (empty($topik)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Harap isi Judul Modul / Topik Materi terlebih dahulu.']);
        exit;
    }

    $kesulitan_map = [
        'mudah' => 'Mudah (Level Kognitif C1-C2: Pemahaman dan Pengetahuan Dasar)',
        'sedang' => 'Sedang (Level Kognitif C3: Penerapan dan Aplikasi Konsep)',
        'sulit' => 'Sulit / HOTS (Level Kognitif C4-C6: Analisis, Penalaran Kritis, dan Pemecahan Masalah Komprehensif)'
    ];
    $kesulitan_label = $kesulitan_map[$kesulitan] ?? $kesulitan_map['sedang'];

    $kategori_map = [
        'Pretest' => 'Pretest (Asesmen Awal untuk mengukur pemahaman awal murid sebelum materi diajarkan)',
        'Diagnostik' => 'Tes Diagnostik (Memetakan kompetensi prasyarat, kesiapan belajar, dan potensi miskonsepsi)',
        'Formatif' => 'Tes Formatif (Evaluasi pemahaman konsep di akhir modul pembelajaran)',
        'Latihan' => 'Latihan Soal Pemahaman'
    ];
    $kategori_label = $kategori_map[$kategori] ?? $kategori_map['Formatif'];

    $system_instruction = "Anda adalah Ahli Evaluasi Pendidikan & Pakar Pembuat Butir Soal Ujian Kurikulum Merdeka. "
        . "Tugas Anda adalah merumuskan TEPAT {$jumlah} BUTIR SOAL BERBEDA (bukan 1 soal saja, melainkan lengkap {$jumlah} butir) untuk evaluasi pembelajaran. "
        . "Jika mata pelajaran eksak/matematika/IPA/teknik, gunakan format LaTeX '$...$' untuk semua simbol, persamaan, pecahan, dan rumus matematika. "
        . "PENTING: Output HARUS berupa JSON Array MURNI (dimulai dengan '[' dan diakhiri dengan ']') yang berisi TEPAT {$jumlah} elemen objek soal.\n\n"
        . "Contoh format JSON (Array harus berisi {$jumlah} item objek):\n"
        . "[\n"
        . "  {\"tipe\": \"{$tipe}\", \"kategori_soal\": \"{$kategori}\", \"pertanyaan\": \"Teks butir soal 1...\", \"opsi_a\": \"Pilihan A\", \"opsi_b\": \"Pilihan B\", \"opsi_c\": \"Pilihan C\", \"opsi_d\": \"Pilihan D\", \"opsi_e\": \"Pilihan E\", \"kunci_jawaban\": \"A\"},\n"
        . "  {\"tipe\": \"{$tipe}\", \"kategori_soal\": \"{$kategori}\", \"pertanyaan\": \"Teks butir soal 2...\", \"opsi_a\": \"Pilihan A\", \"opsi_b\": \"Pilihan B\", \"opsi_c\": \"Pilihan C\", \"opsi_d\": \"Pilihan D\", \"opsi_e\": \"Pilihan E\", \"kunci_jawaban\": \"B\"},\n"
        . "  {\"tipe\": \"{$tipe}\", \"kategori_soal\": \"{$kategori}\", \"pertanyaan\": \"Teks butir soal 3...\", \"opsi_a\": \"Pilihan A\", \"opsi_b\": \"Pilihan B\", \"opsi_c\": \"Pilihan C\", \"opsi_d\": \"Pilihan D\", \"opsi_e\": \"Pilihan E\", \"kunci_jawaban\": \"C\"}\n"
        . "  ... dst berlanjut sampai persis butir ke-{$jumlah}\n"
        . "]";

    $prompt = "Buatlah SEKARANG LENGKAP {$jumlah} BUTIR SOAL (dari nomor 1 sampai {$jumlah}) untuk:\n"
        . "- Mata Pelajaran : {$mapel_nama}\n"
        . "- Tingkat / Kelas: Kelas {$tingkat}\n"
        . "- Topik / Fokus  : {$topik}\n"
        . "- Jenis Evaluasi : {$kategori_label}\n"
        . "- Bentuk Soal    : {$tipe}\n"
        . "- Tingkat Nalar  : {$kesulitan_label}\n"
        . "- JUMLAH BUTIR   : WAJIB {$jumlah} BUTIR SOAL LENGKAP DI DALAM ARRAY JSON (array.length == {$jumlah}). DILARANG BERHENTI DI 1 SOAL!\n\n"
        . "Pastikan setiap butir soal unik, memiliki opsi A sampai E yang masuk akal (jika PG), dan kunci jawaban yang 100% akurat. Kembalikan HANYA JSON array valid.";

    $res = AIModel::generate($pdo, $prompt, $system_instruction, true);

    ob_end_clean();
    if ($res['success']) {
        $text = trim($res['text']);
        
        // 1. Ekstrak dari blok markdown ```json ... ``` jika ada
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $text, $matches)) {
            $text = trim($matches[1]);
        }
        
        $json_data = json_decode($text, true);
        
        // 2. Fallback: cari substring antara '[' dan ']'
        if (!is_array($json_data)) {
            $start = strpos($text, '[');
            $end = strrpos($text, ']');
            if ($start !== false && $end !== false && $end > $start) {
                $sub = substr($text, $start, $end - $start + 1);
                $json_data = json_decode($sub, true);
            }
        }
        
        // 3. Fallback: cari substring antara '{' dan '}'
        if (!is_array($json_data)) {
            $start = strpos($text, '{');
            $end = strrpos($text, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $sub = substr($text, $start, $end - $start + 1);
                $json_data = json_decode($sub, true);
            }
        }

        if (is_array($json_data) && !empty($json_data)) {
            // Normalisasi struktur array jika AI membungkus dengan key objek
            if (isset($json_data['questions']) && is_array($json_data['questions'])) {
                $json_data = $json_data['questions'];
            } elseif (isset($json_data['soal']) && is_array($json_data['soal'])) {
                $json_data = $json_data['soal'];
            } elseif (isset($json_data['data']) && is_array($json_data['data'])) {
                $json_data = $json_data['data'];
            } elseif (isset($json_data['pertanyaan'])) {
                $json_data = [$json_data];
            }
            
            // Re-index array numerik 0, 1, 2, ...
            $json_data = array_values($json_data);

            // Cek jika target langsung ke Bank Soal CBT (Sumatif)
            $target = trim($_POST['target'] ?? 'materi');
            $id_bank = intval($_POST['id_bank'] ?? 0);

            if ($target === 'cbt' && $id_bank > 0) {
                try {
                    $cbt_res = LmsModel::saveAiGeneratedSoalToCbt($pdo, $id_bank, $json_data);
                    echo json_encode([
                        'success' => true, 
                        'is_cbt' => true, 
                        'message' => "Berhasil menambahkan {$cbt_res['inserted']} butir soal ke Bank Soal CBT!",
                        'cbt_data' => $cbt_res,
                        'questions' => $json_data
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke Bank Soal CBT: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => true, 'questions' => $json_data]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memproses format JSON dari AI. Coba ulangi kembali.', 'raw' => $text]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => $res['message']]);
    }
    exit;
}

// ============================================================
// 📖 ENDPOINT BAB & SUB-BAB (DAFTAR ISI BUKU)
// ============================================================

function lms_bab_save() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    try {
        $id_guru_session = $_SESSION['id_guru_terkait'] ?? null;
        $data = [
            'id_bab' => !empty($_POST['id_bab']) ? (int)$_POST['id_bab'] : null,
            'id_mapel' => (int)($_POST['id_mapel'] ?? 0),
            'tingkat' => trim($_POST['tingkat'] ?? 'X'),
            'semester' => trim($_POST['semester'] ?? 'Ganjil'),
            'urutan_bab' => (int)($_POST['urutan_bab'] ?? 1),
            'judul_bab' => trim($_POST['judul_bab'] ?? ''),
            'deskripsi' => trim($_POST['deskripsi'] ?? ''),
            'id_guru' => $id_guru_session
        ];

        if (empty($data['judul_bab']) || empty($data['id_mapel'])) {
            echo json_encode(['status' => 'error', 'message' => 'Judul Bab dan Mata Pelajaran wajib diisi.']);
            exit;
        }

        $id_bab = LmsModel::saveBab($pdo, $data);
        echo json_encode(['status' => 'ok', 'id_bab' => $id_bab, 'message' => 'Bab materi berhasil disimpan.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function lms_bab_delete() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $id_bab = (int)($_POST['id_bab'] ?? $_GET['id_bab'] ?? 0);
    if (!$id_bab) {
        echo json_encode(['status' => 'error', 'message' => 'ID Bab tidak valid.']);
        exit;
    }

    try {
        LmsModel::deleteBab($pdo, $id_bab);
        echo json_encode(['status' => 'ok', 'message' => 'Bab berhasil dihapus.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function lms_sub_bab_save() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    try {
        $data = [
            'id_sub_bab' => !empty($_POST['id_sub_bab']) ? (int)$_POST['id_sub_bab'] : null,
            'id_bab' => (int)($_POST['id_bab'] ?? 0),
            'urutan_sub' => (int)($_POST['urutan_sub'] ?? 1),
            'judul_sub_bab' => trim($_POST['judul_sub_bab'] ?? ''),
            'deskripsi' => trim($_POST['deskripsi'] ?? '')
        ];

        if (empty($data['judul_sub_bab']) || empty($data['id_bab'])) {
            echo json_encode(['status' => 'error', 'message' => 'Judul Sub-Bab dan Bab wajib diisi.']);
            exit;
        }

        $id_sub = LmsModel::saveSubBab($pdo, $data);
        echo json_encode(['status' => 'ok', 'id_sub_bab' => $id_sub, 'message' => 'Sub-Bab berhasil disimpan.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function lms_sub_bab_delete() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $id_sub_bab = (int)($_POST['id_sub_bab'] ?? $_GET['id_sub_bab'] ?? 0);
    if (!$id_sub_bab) {
        echo json_encode(['status' => 'error', 'message' => 'ID Sub-Bab tidak valid.']);
        exit;
    }

    try {
        LmsModel::deleteSubBab($pdo, $id_sub_bab);
        echo json_encode(['status' => 'ok', 'message' => 'Sub-Bab berhasil dihapus.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function lms_get_bab_ajax() {
    global $pdo;
    header('Content-Type: application/json');
    $id_mapel = (int)($_GET['id_mapel'] ?? 0);
    $tingkat = $_GET['tingkat'] ?? null;
    $babs = $id_mapel ? LmsModel::getBabSimpleByMapel($pdo, $id_mapel, $tingkat) : [];
    echo json_encode(['status' => 'ok', 'data' => $babs]);
    exit;
}

function lms_get_sub_bab_ajax() {
    global $pdo;
    header('Content-Type: application/json');
    $id_bab = (int)($_GET['id_bab'] ?? 0);
    $subs = $id_bab ? LmsModel::getSubBabByBab($pdo, $id_bab) : [];
    echo json_encode(['status' => 'ok', 'data' => $subs]);
    exit;
}

// ============================================================
// 💬 ENDPOINT DISKUSI / FORUM PER MATERI
// ============================================================

function lms_diskusi_post() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $id_materi = (int)($_POST['id_materi'] ?? 0);
    $pesan = trim($_POST['pesan'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $id_user = (int)($_SESSION['user_id'] ?? 0);

    if (!$id_materi || empty($pesan) || !$id_user) {
        echo json_encode(['status' => 'error', 'message' => 'Pesan diskusi tidak boleh kosong.']);
        exit;
    }

    try {
        $id_diskusi = LmsModel::postDiskusi($pdo, $id_materi, $id_user, $pesan, $parent_id);
        $diskusi_list = LmsModel::getDiskusiByMateri($pdo, $id_materi);
        echo json_encode(['status' => 'ok', 'message' => 'Pesan terkirim.', 'data' => $diskusi_list]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function lms_diskusi_verify() {
    global $pdo;
    header('Content-Type: application/json');
    $roles = user_roles();
    if (!in_array('Guru', $roles) && !in_array('Admin', $roles)) {
        echo json_encode(['status' => 'error', 'message' => 'Hanya guru yang dapat memverifikasi jawaban.']);
        exit;
    }

    $id_diskusi = (int)($_POST['id_diskusi'] ?? 0);
    if (!$id_diskusi) {
        echo json_encode(['status' => 'error', 'message' => 'ID Diskusi tidak valid.']);
        exit;
    }

    try {
        LmsModel::toggleVerifyDiskusi($pdo, $id_diskusi);
        echo json_encode(['status' => 'ok', 'message' => 'Status verifikasi diperbarui.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

function lms_diskusi_delete() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $id_diskusi = (int)($_POST['id_diskusi'] ?? 0);
    if (!$id_diskusi) {
        echo json_encode(['status' => 'error', 'message' => 'ID Diskusi tidak valid.']);
        exit;
    }

    try {
        LmsModel::deleteDiskusi($pdo, $id_diskusi);
        echo json_encode(['status' => 'ok', 'message' => 'Pesan diskusi berhasil dihapus.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================================
// ⚡ ENDPOINT SINKRONISASI NILAI FORMATIF KE BUKU NILAI
// ============================================================

function lms_get_nilai_formatif_ajax() {
    global $pdo;
    header('Content-Type: application/json');
    if (!is_logged_in()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $id_materi = (int)($_GET['id_materi'] ?? $_POST['id_materi'] ?? 0);
    $id_kelas  = (int)($_GET['id_kelas'] ?? $_POST['id_kelas'] ?? 0);

    if (!$id_materi || !$id_kelas) {
        echo json_encode(['status' => 'error', 'message' => 'ID Materi dan ID Kelas wajib diisi.']);
        exit;
    }

    $res = LmsModel::getNilaiFormatifByMateriKelas($pdo, $id_materi, $id_kelas);
    echo json_encode($res);
    exit;
}
?>