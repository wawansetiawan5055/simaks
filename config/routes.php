<?php
// =========================================================
// SIMAKS - CENTRALIZED MODULAR ROUTE DISPATCHER
// config/routes.php
// =========================================================

/**
 * Dispatch application request to corresponding controller
 *
 * @param PDO $pdo
 * @param string $mod
 * @param string $act
 * @return void
 */
function dispatch_route($pdo, $mod, $act) {
    // Inisialisasi global variables jika dibutuhkan oleh legacy view/controller
    global $user_menu, $app_config;

    // REQUIRE CONTROLLER UNTUK MODUL BARU (PENTING!)
    // KARENA MENGGUNAKAN SWITCH/CASE, CONTROLLER HARUS DI-REQUIRE DI SINI
    // JIKA ANDA INGIN CONTROLLER DI-REQUIRE HANYA KETIKA DIPERLUKAN (LAZY LOADING), 
    // PINDAHKAN REQUIRE KE DALAM BLOK CASE MASING-MASING.
    // =========================================================
    // LandingController removed // Landing Page Public
    require_once __DIR__ . '/../app/controllers/AuthController.php';
    require_once __DIR__ . '/../app/controllers/DashboardController.php';
    require_once __DIR__ . '/../app/controllers/SessionFilterController.php';
    
    // --- Modul Admin ---
    require_once __DIR__ . '/../app/controllers/PeranController.php';
    require_once __DIR__ . '/../app/controllers/AppMenuController.php';
    require_once __DIR__ . '/../app/controllers/HakAksesController.php';
    require_once __DIR__ . '/../app/controllers/ProfilSekolahController.php';
    require_once __DIR__ . '/../app/controllers/ManajemenPenggunaController.php';
    require_once __DIR__ . '/../app/controllers/UtilitasDbController.php';
    require_once __DIR__ . '/../app/controllers/LandingAdminController.php'; // Landing CMS
    require_once __DIR__ . '/../app/controllers/ProfilController.php'; // Profil Pengguna
    require_once __DIR__ . '/../app/controllers/ProfilGuruController.php'; // Profil Detail Guru
    require_once __DIR__ . '/../app/controllers/ProfilSiswaController.php'; // Profil Detail Siswa
    
    // --- Data Master ---
    require_once __DIR__ . '/../app/controllers/GuruController.php';
    require_once __DIR__ . '/../app/controllers/SiswaController.php';
    require_once __DIR__ . '/../app/controllers/KelasController.php';
    require_once __DIR__ . '/../app/controllers/MapelController.php';
    require_once __DIR__ . '/../app/controllers/StrukturKurikulumController.php';
    require_once __DIR__ . '/../app/controllers/MasterKegiatanController.php';
    require_once __DIR__ . '/../app/controllers/MasterJamController.php';
    require_once __DIR__ . '/../app/controllers/TahunAjaranController.php';
    
    // --- Akademik ---
    require_once __DIR__ . '/../app/controllers/PenempatanController.php';
    require_once __DIR__ . '/../app/controllers/PenugasanGuruController.php';
    require_once __DIR__ . '/../app/controllers/JadwalController.php';
    require_once __DIR__ . '/../app/controllers/CpTpController.php';
    require_once __DIR__ . '/../app/controllers/TugasTambahanController.php';
    require_once __DIR__ . '/../app/controllers/EkskulController.php';
    require_once __DIR__ . '/../app/controllers/KokulikulerController.php';
    require_once __DIR__ . '/../app/controllers/PembiasaanController.php';
    require_once __DIR__ . '/../app/controllers/KewirausahaanController.php';
    require_once __DIR__ . '/../app/controllers/TahfidzController.php';
    require_once __DIR__ . '/../app/controllers/PerangkatController.php';
    require_once __DIR__ . '/../app/controllers/PerangkatUploadController.php'; // NEW: File Upload Controller
    require_once __DIR__ . '/../app/controllers/TemplateDokumenController.php';
    require_once __DIR__ . '/../app/controllers/LabController.php';
    require_once __DIR__ . '/../app/controllers/PerpusController.php';
    require_once __DIR__ . '/../app/controllers/SarprasController.php';
    require_once __DIR__ . '/../app/controllers/UksController.php';
    require_once __DIR__ . '/../app/controllers/RekapNilaiController.php';
    require_once __DIR__ . '/../app/controllers/CetakRaporController.php';
    
    // --- Kegiatan KBM ---
    require_once __DIR__ . '/../app/controllers/JurnalKbmController.php';
    require_once __DIR__ . '/../app/controllers/AbsensiMapelController.php';
    require_once __DIR__ . '/../app/controllers/InputNilaiController.php';
    require_once __DIR__ . '/../app/controllers/PenilaianSumatifController.php';
    require_once __DIR__ . '/../app/controllers/AbsensiPiketController.php';
    require_once __DIR__ . '/../app/controllers/JadwalPiketController.php';
    require_once __DIR__ . '/../app/controllers/AbsensiGuruController.php';
    require_once __DIR__ . '/../app/controllers/CatatanKasusController.php';
    require_once __DIR__ . '/../app/controllers/CatatanKelasController.php';
    require_once __DIR__ . '/../app/controllers/PpdbController.php';
    require_once __DIR__ . '/../app/controllers/MutasiMasukController.php';
    require_once __DIR__ . '/../app/controllers/MutasiSiswaController.php';
    require_once __DIR__ . '/../app/controllers/LulusanController.php';
    require_once __DIR__ . '/../app/controllers/AppConfigController.php'; // NEW: Theme & Font Config
    require_once __DIR__ . '/../app/controllers/SuratController.php'; // NEW: Correspondence Module
    require_once __DIR__ . '/../app/controllers/ChatController.php'; // NEW: Internal Chat Module
    require_once __DIR__ . '/../app/controllers/SiswaPortalController.php'; // NEW: Siswa Portal Module
    require_once __DIR__ . '/../app/controllers/CbtController.php'; // CBT Terintegrasi
    
    // --- Laporan ---
    require_once __DIR__ . '/../app/controllers/LaporanController.php';
    
    // --- Keuangan ---
    require_once __DIR__ . '/../app/controllers/KeuanganController.php';
    require_once __DIR__ . '/../app/models/KeuanganTagihanModel.php';
    require_once __DIR__ . '/../app/controllers/KeuanganTagihanController.php';
    
    // =========================================================
    // ROUTER (DISPATCHER)
    // =========================================================
    switch ($mod) {
            // === SPECIAL ROUTING: API VIA INDEX.PHP ===
            // Ini memungkinkan AJAX requests mengakses API via index.php routing
            // Contoh: index.php?mod=api&type=dashboard&act=absensi_guru
            case 'api':
                header('Content-Type: application/json');
                $type = $_GET['type'] ?? ''; // API type: dashboard, guru, siswa, dll
                if (empty($type)) {
                    if (in_array($act, ['summary', 'rekap_siswa', 'absensi_guru', 'absensi_siswa', 'absensi_siswa_detail', 'list_ta', 'list_kelas', 'list_guru_aktif', 'jadwal_guru_html'])) {
                        $type = 'dashboard';
                    } elseif (in_array($act, ['get_daily'])) {
                        $type = 'jadwal';
                    } elseif (in_array($act, ['history', 'send', 'search', 'recent', 'unread_count', 'delete', 'clear'])) {
                        $type = 'chat';
                    }
                }
    
                try {
                    // Pastikan path dan file config
                    $configPath = dirname(__DIR__) . '/config';
                    if (!file_exists($configPath . '/helper.php') || !file_exists($configPath . '/db.php')) {
                        throw new Exception("Konfigurasi sistem tidak ditemukan");
                    }
    
                    // Load Model yang dibutuhkan API (sama seperti di api/api.php)
                    $modelPath = dirname(__DIR__) . '/app/models';
                    require_once $modelPath . '/AppMenuModel.php';
                    require_once $modelPath . '/HakAksesModel.php';
                    require_once $modelPath . '/PenggunaModel.php';
                    require_once $modelPath . '/PeranModel.php';
                    require_once $modelPath . '/TahunAjaranModel.php';
                    require_once $modelPath . '/GuruModel.php';
                    require_once $modelPath . '/KelasModel.php';
                    require_once $modelPath . '/SiswaModel.php';
                    require_once $modelPath . '/JadwalModel.php';
    
                    // Pastikan session punya Tahun Ajaran Aktif
                    if (empty($_SESSION['id_ta_aktif'])) {
                        $ta_aktif_db = $pdo->query("SELECT id_ta, nama_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                        if ($ta_aktif_db) {
                            $_SESSION['id_ta_aktif'] = $ta_aktif_db['id_ta'];
                            $_SESSION['nama_ta_aktif'] = $ta_aktif_db['nama_ta'];
                        }
                    }
    
                    // Router API berdasarkan type
                    $apiControllerPath = dirname(__DIR__) . '/api';
                    switch ($type) {
                        case 'dashboard':
                            require_once $apiControllerPath . '/DashboardApiController.php';
                            DashboardApiController::handle($pdo, $act);
                            break;
                        case 'guru':
                            require_once $apiControllerPath . '/GuruApiController.php';
                            GuruApiController::handle($pdo, $act);
                            break;
                        case 'siswa':
                            require_once $apiControllerPath . '/SiswaApiController.php';
                            SiswaApiController::handle($pdo, $act);
                            break;
                        case 'jadwal':
                            require_once $apiControllerPath . '/JadwalApiController.php';
                            JadwalApiController::handle($pdo, $act);
                            break;
                        case 'absensi':
                            require_once $apiControllerPath . '/AbsensiApiController.php';
                            AbsensiApiController::handle($pdo, $act);
                            break;
                        case 'auth':
                            require_once $apiControllerPath . '/AuthApiController.php';
                            AuthApiController::handle($pdo, $act);
                            break;
                        case 'sumatif':
                            require_once $apiControllerPath . '/SumatifApiController.php';
                            SumatifApiController::handle($pdo, $act);
                            break;
                        case 'keuangan':
                            require_once $apiControllerPath . '/KeuanganApiController.php';
                            KeuanganApiController::handle($pdo, $act);
                            break;
                        case 'cptp': // NEW: CP/TP API
                            require_once $apiControllerPath . '/CpTpApiController.php';
                            CpTpApiController::handle($pdo, $act);
                            break;
                        case 'chat': // NEW: Chat API
                            if ($act == 'history') api_chat_get_history($pdo);
                            elseif ($act == 'send') api_chat_send($pdo);
                            elseif ($act == 'search') api_chat_search_users($pdo);
                            elseif ($act == 'recent') api_chat_recent($pdo);
                            elseif ($act == 'unread_count') api_chat_unread_count($pdo);
                            elseif ($act == 'delete') api_chat_delete($pdo);
                            elseif ($act == 'clear') api_chat_clear($pdo);
                            break;
                        default:
                            http_response_code(400);
                            echo json_encode(['status' => 'error', 'msg' => 'Invalid API type: ' . $type]);
                            break;
                    }
                } catch (Throwable $e) {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'msg' => 'Server Error: ' . $e->getMessage()
                    ]);
                }
                exit; // Stop execution setelah API response
    
            // --- UTILITAS & DATABASE MANAGEMENT ---
            case 'utilitas_db':
                if ($act == 'run_sql')
                    utilitas_db_run_sql($pdo);
                elseif ($act == 'backup')
                    utilitas_db_backup($pdo);
                elseif ($act == 'restore')
                    utilitas_db_restore($pdo);
                elseif ($act == 'optimize')
                    utilitas_db_optimize($pdo);
                elseif ($act == 'hapus_histori')
                    utilitas_db_hapus_histori($pdo);
                elseif ($act == 'hapus_setup')
                    utilitas_db_hapus_setup($pdo);
                elseif ($act == 'truncate_selected')
                    utilitas_db_truncate_selected($pdo);
                elseif ($act == 'reset_aplikasi')
                    utilitas_db_reset_aplikasi($pdo);
                elseif ($act == 'run_patch')
                    utilitas_db_run_patch($pdo);
                else
                    utilitas_db_index($pdo);
                break;

            // --- KEUANGAN ---
            case 'keuangan_dashboard':
                if ($act == 'sync_saldo')
                    keuangan_sync_saldo($pdo);
                else
                    keuangan_dashboard($pdo);
                break;
    
            case 'keuangan_master':
                if ($act == 'group')
                    keuangan_master_group($pdo);
                elseif ($act == 'kategori')
                    keuangan_master_kategori($pdo);
                elseif ($act == 'coa')
                    keuangan_master_coa($pdo); // NEW: Unified View
                elseif ($act == 'save_kategori')
                    keuangan_kategori_save($pdo); // NEW: Save Level 1
                elseif ($act == 'jenis')
                    keuangan_master_jenis($pdo);
                elseif ($act == 'save_jenis')
                    keuangan_jenis_save($pdo); // New routing for save
                elseif ($act == 'save_rekening')
                    keuangan_rekening_save($pdo);
                elseif ($act == 'rekening')
                    keuangan_master_rekening($pdo);
                else
                    keuangan_dashboard($pdo);
                break;
    
            case 'keuangan_tarif':
                require_once __DIR__ . '/../app/controllers/KeuanganTarifController.php';
                $controller = new KeuanganTarifController($pdo);
                if ($act == 'create')
                    $controller->create();
                elseif ($act == 'store')
                    $controller->store();
                elseif ($act == 'delete')
                    $controller->delete();
                elseif ($act == 'matrix')
                    $controller->matrix(); // Added routing for matrix
                elseif ($act == 'save_matrix')
                    $controller->save_matrix(); // Added routing for save_matrix
                else
                    $controller->index();
                break;
    
            case 'keuangan_controller':
                $controller = new KeuanganController($pdo);
                $controller->handleRequest();
                break;
    
            case 'keuangan_masuk':
            case 'keuangan_transaksi_masuk':
                if ($act == 'index')
                    keuangan_masuk_index($pdo);
                elseif ($act == 'save')
                    keuangan_masuk_save($pdo);
                else
                    keuangan_masuk_index($pdo);
                break;
    
            case 'keuangan_keluar':
            case 'keuangan_transaksi_keluar':
                if ($act == 'index')
                    keuangan_keluar_index($pdo);
                elseif ($act == 'save')
                    keuangan_keluar_save($pdo);
                else
                    keuangan_keluar_index($pdo);
                break;
    
            case 'keuangan_jurnal':
                if ($act == 'index')
                    keuangan_jurnal($pdo);
                else
                    keuangan_jurnal($pdo);
                break;
    
            case 'keuangan_jurnal_pembantu':
                keuangan_jurnal_pembantu($pdo);
                break;
    
            case 'keuangan_bku':
                keuangan_bku($pdo);
                break;
    
            case 'keuangan_gaji':
                require_once __DIR__ . '/../app/controllers/KeuanganGajiController.php';
                $controller = new KeuanganGajiController($pdo);
                if ($act == 'index')
                    $controller->index();
                elseif ($act == 'setting')
                    $controller->setting();
                elseif ($act == 'save_setting')
                    $controller->save_setting();
                elseif ($act == 'config')
                    $controller->config();
                elseif ($act == 'save_config')
                    $controller->save_config();
                elseif ($act == 'create')
                    $controller->create();
                elseif ($act == 'generate')
                    $controller->generate();
                elseif ($act == 'detail')
                    $controller->detail();
                elseif ($act == 'delete')
                    $controller->delete();
                elseif ($act == 'print_slip')
                    $controller->print_slip();
                elseif ($act == 'print_rekap')
                    $controller->print_rekap(); // Forgot this too earlier
                else
                    $controller->index();
                break;
    
            case 'keuangan_laporan_pembayaran':
                keuangan_laporan_pembayaran($pdo);
                break;
    
            case 'keuangan_memorial':
                if ($act == 'save')
                    keuangan_memorial_save($pdo);
                break;
    
            case 'keuangan_masuk_print':
                keuangan_masuk_print($pdo);
                break;
    
            case 'keuangan_get_tagihan_siswa':
                keuangan_get_tagihan_siswa($pdo);
                break;
    
            case 'keuangan_get_siswa':
                keuangan_get_siswa_by_kelas($pdo);
                break;
    
            case 'keuangan_get_siswa_matrix':
                keuangan_get_siswa_matrix($pdo);
                break;
    
            case 'keuangan_master_jenis': // Direct route for specific request
                keuangan_master_jenis($pdo);
                break;
    
            case 'keuangan_tagihan':
                $controller = new KeuanganTagihanController($pdo);
                if ($act == 'index')
                    $controller->index();
                elseif ($act == 'create')
                    $controller->create();
                elseif ($act == 'store')
                    $controller->store(); // Changed from generate to store
                else
                    $controller->index();
                break;
    
            // =========================================================
            // LANDING PAGE PUBLIC (No Login Required)
            // =========================================================
            // =========================================================
            // LANDING PAGE PUBLIC - SMA PLUS AL-MANSHURIYAH (Bootstrap 5)
            // =========================================================
            case 'landing':
            case 'landing_sma':
                require_once dirname(__DIR__) . '/app/controllers/LandingControllerSMA.php';
                if ($act == 'guru_list')
                    guru_list($pdo);
                elseif ($act == 'siswa_list')
                    siswa_list($pdo);
                elseif ($act == 'ekstrakurikuler_list' || $act == 'program_list')
                    ekstrakurikuler_list($pdo);
                elseif ($act == 'ekstrakurikuler_detail')
                    ekstrakurikuler_detail($pdo);
                elseif ($act == 'video_list')
                    video_list($pdo);
                elseif ($act == 'gallery')
                    gallery_list($pdo);
                elseif ($act == 'informasi_list' || $act == 'berita_list')
                    informasi_list($pdo);
                elseif ($act == 'informasi_detail' || $act == 'berita_detail')
                    informasi_detail($pdo);
                elseif ($act == 'program_detail')
                    program_detail($pdo);
                elseif ($act == 'profil_sekolah')
                    profil_sekolah($pdo);
                elseif ($act == 'ppdb_form')
                    ppdb_public_form($pdo);
                elseif ($act == 'ppdb_save')
                    ppdb_public_save($pdo);
                elseif ($act == 'ppdb_status')
                    ppdb_check_status($pdo);
                elseif ($act == 'ppdb_success')
                    ppdb_success_page($pdo);
                else
                    landing_sma_index($pdo);
                break;
    
    
            case 'auth':
                if ($act == 'login')
                    login_form();
                elseif ($act == 'login_action')
                    login_action($pdo);
                elseif ($act == 'login_qr')
                    login_qr_action($pdo);
                elseif ($act == 'logout')
                    logout_action();
                else
                    login_form();
                break;
    
            case 'session_filter':
                if ($act == 'set_ta')
                    set_session_ta($pdo);
                break;
    
            case 'dashboard':
                if (in_array('Siswa', $_SESSION['roles'] ?? []) && !in_array('Admin', $_SESSION['roles'] ?? []) && !in_array('Guru', $_SESSION['roles'] ?? [])) {
                    siswa_portal_dashboard();
                } else {
                    dashboard_index($pdo);
                }
                break;
    
            case 'dashboard_guru':
                require_once __DIR__ . '/../app/controllers/DashboardGuruController.php';
                dashboard_guru();
                break;
    
    
            case 'lms':
                require_once __DIR__ . '/../app/controllers/LmsController.php';
                if ($act == 'dashboard' || $act == '')
                    lms_dashboard();
                elseif ($act == 'materi_list')
                    lms_materi_list();
                elseif ($act == 'materi_upload')
                    lms_materi_upload();
                elseif ($act == 'materi_edit')
                    lms_materi_edit();
                elseif ($act == 'materi_detail')
                    lms_materi_detail();
                elseif ($act == 'materi_delete')
                    lms_materi_delete();
                elseif ($act == 'materi_quiz_submit')
                    lms_materi_quiz_submit();
                elseif ($act == 'materi_quiz_template')
                    lms_materi_quiz_template();
                elseif ($act == 'tugas_create')
                    lms_tugas_create();
                elseif ($act == 'tugas_submit')
                    lms_tugas_submit();
                elseif ($act == 'tugas_list')
                    lms_tugas_list();
                elseif ($act == 'tugas_detail')
                    lms_tugas_detail();
                elseif ($act == 'tugas_edit')
                    lms_tugas_edit();
                elseif ($act == 'tugas_delete')
                    lms_tugas_delete();
                elseif ($act == 'koreksi_list')
                    lms_koreksi_list();
                elseif ($act == 'koreksi_detail')
                    lms_koreksi_detail();
                elseif ($act == 'tugas_student_detail')
                    lms_tugas_student_detail();
                elseif ($act == 'get_cp_ajax')
                    lms_get_cp_ajax();
                elseif ($act == 'get_tp_ajax')
                    lms_get_tp_ajax();
                elseif ($act == 'lp_mark_stage')
                    lms_lp_mark_stage();
                elseif ($act == 'lp_submit_text')
                    lms_lp_submit_text();
                elseif ($act == 'lp_submit_formatif')
                    lms_lp_submit_formatif();
                elseif ($act == 'lp_submit_refleksi')
                    lms_lp_submit_refleksi();
                elseif ($act == 'lp_submit_essay')
                    lms_lp_submit_essay();
                elseif ($act == 'ai_generate_materi')
                    lms_ai_generate_materi();
                elseif ($act == 'ai_generate_soal')
                    lms_ai_generate_soal();
                elseif ($act == 'bab_save')
                    lms_bab_save();
                elseif ($act == 'bab_delete')
                    lms_bab_delete();
                elseif ($act == 'sub_bab_save')
                    lms_sub_bab_save();
                elseif ($act == 'sub_bab_delete')
                    lms_sub_bab_delete();
                elseif ($act == 'get_bab_ajax')
                    lms_get_bab_ajax();
                elseif ($act == 'get_sub_bab_ajax')
                    lms_get_sub_bab_ajax();
                elseif ($act == 'diskusi_post')
                    lms_diskusi_post();
                elseif ($act == 'diskusi_verify')
                    lms_diskusi_verify();
                elseif ($act == 'diskusi_delete')
                    lms_diskusi_delete();
                elseif ($act == 'get_nilai_formatif_ajax')
                    lms_get_nilai_formatif_ajax();
                else
                    lms_dashboard();
                break;
    
            case 'tugas_tambahan':
                $known_actions = ['upload', 'delete', 'save_agenda', 'delete_agenda', 'save_inventaris', 'delete_inventaris', 'save_jurnal_bk', 'delete_jurnal_bk', 'save_galeri', 'delete_galeri'];
                if (in_array($act, $known_actions)) {
                    if ($act === 'upload') {
                        TugasTambahanController::upload($pdo);
                    } elseif ($act === 'delete') {
                        TugasTambahanController::delete($pdo);
                    } elseif ($act === 'save_agenda') {
                        TugasTambahanController::save_agenda($pdo);
                    } elseif ($act === 'delete_agenda') {
                        TugasTambahanController::delete_agenda($pdo);
                    } elseif ($act === 'save_inventaris') {
                        TugasTambahanController::save_inventaris($pdo);
                    } elseif ($act === 'delete_inventaris') {
                        TugasTambahanController::delete_inventaris($pdo);
                    } elseif ($act === 'save_jurnal_bk') {
                        TugasTambahanController::save_jurnal_bk($pdo);
                    } elseif ($act === 'delete_jurnal_bk') {
                        TugasTambahanController::delete_jurnal_bk($pdo);
                    } elseif ($act === 'save_galeri') {
                        TugasTambahanController::save_galeri($pdo);
                    } elseif ($act === 'delete_galeri') {
                        TugasTambahanController::delete_galeri($pdo);
                    }
                } else {
                    if ($act !== 'index' && !empty($act) && empty($_GET['jenis'])) {
                        $_GET['jenis'] = $act;
                    }
                    TugasTambahanController::index($pdo);
                }
                break;
    
            case 'sarpras':
                $controller = new SarprasController($pdo);
                $controller->dashboard();
                break;
    
            case 'sarpras_gedung':
                $controller = new SarprasController($pdo);
                if ($act == 'save') $controller->gedung_save();
                elseif ($act == 'delete') $controller->gedung_delete();
                else $controller->gedung_index();
                break;
    
            case 'sarpras_ruang':
                $controller = new SarprasController($pdo);
                if ($act == 'save') $controller->ruang_save();
                elseif ($act == 'delete') $controller->ruang_delete();
                else $controller->ruang_index();
                break;
    
            case 'sarpras_barang':
                $controller = new SarprasController($pdo);
                if ($act == 'save') $controller->barang_save();
                elseif ($act == 'delete') $controller->barang_delete();
                else $controller->barang_index();
                break;
    
            case 'manajemen_lab':
                if ($act === 'save_inventaris') {
                    LabController::save_inventaris($pdo);
                } elseif ($act === 'delete_inventaris') {
                    LabController::delete_inventaris($pdo);
                } elseif ($act === 'upload_foto') {
                    LabController::upload_foto($pdo);
                } else {
                    LabController::index($pdo);
                }
                break;
    
            case 'manajemen_perpus':
                if ($act === 'save_buku') {
                    PerpusController::save_buku($pdo);
                } elseif ($act === 'save_peminjaman') {
                    PerpusController::save_peminjaman($pdo);
                } elseif ($act === 'kembalikan') {
                    PerpusController::kembalikan($pdo);
                } elseif ($act === 'get_siswa_by_kelas') {
                    PerpusController::get_siswa_by_kelas($pdo);
                } else {
                    PerpusController::index($pdo);
                }
                break;
    
            case 'uks':
            case 'manajemen_uks':
                if ($act === 'save_kunjungan') {
                    UksController::save_kunjungan($pdo);
                } elseif ($act === 'delete_kunjungan') {
                    UksController::delete_kunjungan($pdo);
                } elseif ($act === 'save_obat') {
                    UksController::save_obat($pdo);
                } elseif ($act === 'delete_obat') {
                    UksController::delete_obat($pdo);
                } elseif ($act === 'save_agenda') {
                    UksController::save_agenda($pdo);
                } elseif ($act === 'delete_agenda') {
                    UksController::delete_agenda($pdo);
                } elseif ($act === 'cetak_surat_izin') {
                    UksController::cetak_surat_izin($pdo);
                } else {
                    UksController::index($pdo);
                }
                break;
    
            case 'profil_sekolah':
                if ($act == 'index')
                    profil_sekolah_index($pdo);
                elseif ($act == 'save')
                    profil_sekolah_save($pdo);
                break;
    
            case 'profil':
                if ($act == 'index')
                    profil_index($pdo);
                elseif ($act == 'save')
                    profil_save($pdo);
                break;
    
            case 'profil_guru':
                if ($act == 'index')
                    profil_guru_index($pdo);
                elseif ($act == 'detail')
                    profil_guru_detail($pdo);
                elseif ($act == 'save')
                    profil_guru_save($pdo);
                elseif ($act == 'upload')
                    profil_guru_upload($pdo);
                elseif ($act == 'print')
                    profil_guru_print($pdo);
                else
                    profil_guru_index($pdo);
                break;
    
            case 'profil_siswa':
                if ($act == 'index')
                    profil_siswa_index($pdo);
                elseif ($act == 'detail')
                    profil_siswa_detail($pdo);
                elseif ($act == 'save')
                    profil_siswa_save($pdo);
                elseif ($act == 'upload')
                    profil_siswa_upload($pdo);
                elseif ($act == 'ajukan')
                    profil_siswa_ajukan($pdo);
                elseif ($act == 'riwayat')
                    profil_siswa_riwayat($pdo);
                elseif ($act == 'print')
                    profil_siswa_print($pdo);
                else
                    profil_siswa_index($pdo);
                break;
    
            case 'portal_siswa':
            case 'siswa_portal':
                if ($act == 'jadwal')
                    siswa_portal_jadwal();
                elseif ($act == 'nilai')
                    siswa_portal_nilai();
                elseif ($act == 'absensi')
                    siswa_portal_absensi();
                elseif ($act == 'tagihan')
                    siswa_portal_tagihan();
                elseif ($act == 'progress')
                    siswa_portal_progress();
                elseif ($act == 'pembiasaan')
                    siswa_portal_pembiasaan();
                elseif ($act == 'tahfidz')
                    siswa_portal_tahfidz();
                elseif ($act == 'ekskul')
                    siswa_portal_ekskul();
                elseif ($act == 'kokulikuler')
                    siswa_portal_kokulikuler();
                elseif ($act == 'kewirausahaan')
                    siswa_portal_kewirausahaan();
                elseif ($act == 'kalender')
                    siswa_portal_kalender();
                elseif ($act == 'materi')
                    siswa_portal_materi();
                elseif ($act == 'tugas')
                    siswa_portal_tugas();
                elseif ($act == 'materi_detail')
                    siswa_portal_materi_detail();
                elseif ($act == 'tugas_submit')
                    siswa_portal_tugas_submit();
                elseif ($act == 'permohonan')
                    siswa_portal_permohonan();
                elseif ($act == 'permohonan_simpan')
                    siswa_portal_permohonan_simpan();
                elseif ($act == 'cbt')
                    siswa_portal_cbt();
                elseif ($act == 'cbt_konfirmasi')
                    siswa_portal_cbt_konfirmasi();
                elseif ($act == 'cbt_kerjakan' || $act == 'cbt_room')
                    siswa_portal_cbt_room();
                elseif ($act == 'cbt_save_jawaban')
                    siswa_portal_cbt_save_jawaban();
                elseif ($act == 'cbt_selesai')
                    siswa_portal_cbt_selesai();
                elseif ($act == 'dashboard')
                    siswa_portal_dashboard();
                else
                    siswa_portal_dashboard(); // default redirect to student portal dashboard
                break;
    
            case 'permohonan_absensi':
                require_once __DIR__ . '/../app/controllers/PermohonanAbsensiController.php';
                if ($act == 'index')
                    permohonan_absensi_index($pdo);
                elseif ($act == 'proses')
                    permohonan_absensi_proses($pdo);
                else
                    permohonan_absensi_index($pdo);
                break;
    
            case 'manajemen_pengguna':
                if ($act == 'index')
                    pengguna_index($pdo);
                elseif ($act == 'form')
                    pengguna_form($pdo, $_GET['id'] ?? null);
                elseif ($act == 'save')
                    pengguna_save($pdo);
                elseif ($act == 'delete')
                    pengguna_delete($pdo, $_GET['id']);
                elseif ($act == 'generate')
                    pengguna_generate($pdo);
                elseif ($act == 'cleanup')
                    pengguna_cleanup($pdo);
                elseif ($act == 'print_kartu')
                    pengguna_print_kartu($pdo);
                else
                    pengguna_index($pdo);
                break;
    
            // =========================================================
            // MODUL MANAJEMEN PERAN & AKSES
            // =========================================================
            case 'peran':
                if ($act == 'index')
                    peran_index($pdo);
                elseif ($act == 'form')
                    peran_form($pdo);
                elseif ($act == 'save_action')
                    peran_save_action($pdo);
                elseif ($act == 'delete_action')
                    peran_delete_action($pdo);
                else
                    peran_index($pdo);
                break;
    
            case 'audit_log':
                require_once __DIR__ . '/../app/controllers/AuditLogController.php';
                audit_log_index($pdo);
                break;
    
            case 'app_menu':
                if ($act == 'index')
                    app_menu_index($pdo);
                elseif ($act == 'form')
                    app_menu_form($pdo);
                elseif ($act == 'save_action')
                    app_menu_save_action($pdo);
                elseif ($act == 'save_order') // [BARU] Simpan urutan drag & drop
                    app_menu_save_order($pdo);
                elseif ($act == 'duplicate_action') // [BARU] Duplikat menu
                    app_menu_duplicate_action($pdo);
                elseif ($act == 'delete_action')
                    app_menu_delete_action($pdo);
                else
                    app_menu_index($pdo);
                break;
    
            case 'hak_akses':
                if ($act == 'index') {
                    hak_akses_index($pdo);
                } elseif ($act == 'map_form') {
                    hak_akses_map_form($pdo);
                } elseif ($act == 'save_action') {
    
                    hak_akses_save_action($pdo);
                } else {
                    hak_akses_index($pdo);
                }
                break;
            // =========================================================
    
    
            case 'landing_admin':
                if ($act == 'settings')
                    landing_admin_settings($pdo);
                elseif ($act == 'save_settings')
                    landing_admin_settings_save($pdo);
                elseif ($act == 'quotes')
                    landing_admin_quotes_index($pdo);
                elseif ($act == 'quote_form')
                    landing_admin_quote_form($pdo);
                elseif ($act == 'quote_save')
                    landing_admin_quote_save($pdo);
                elseif ($act == 'quote_delete')
                    landing_admin_quote_delete($pdo);
                elseif ($act == 'news')
                    landing_admin_news_index($pdo);
                elseif ($act == 'news_form')
                    landing_admin_news_form($pdo);
                elseif ($act == 'news_save')
                    landing_admin_news_save($pdo);
                elseif ($act == 'news_delete')
                    landing_admin_news_delete($pdo);
                elseif ($act == 'gallery')
                    landing_admin_gallery_index($pdo);
                elseif ($act == 'gallery_form')
                    landing_admin_gallery_form($pdo);
                elseif ($act == 'gallery_save')
                    landing_admin_gallery_save($pdo);
                elseif ($act == 'gallery_delete')
                    landing_admin_gallery_delete($pdo);
                elseif ($act == 'sambutan')
                    landing_admin_sambutan($pdo);
                elseif ($act == 'sambutan_form')
                    landing_admin_sambutan_form($pdo);
                elseif ($act == 'sambutan_save')
                    landing_admin_sambutan_save($pdo);
                elseif ($act == 'sambutan_delete')
                    landing_admin_sambutan_delete($pdo);
                elseif ($act == 'program')
                    landing_admin_program($pdo);
                elseif ($act == 'program_form')
                    landing_admin_program_form($pdo);
                elseif ($act == 'program_save')
                    landing_admin_program_save($pdo);
                elseif ($act == 'program_delete')
                    landing_admin_program_delete($pdo);
                elseif ($act == 'facilities')
                    landing_admin_facilities($pdo);
                elseif ($act == 'facilities_form')
                    landing_admin_facilities_form($pdo);
                elseif ($act == 'facilities_save')
                    landing_admin_facilities_save($pdo);
                elseif ($act == 'facilities_delete')
                    landing_admin_facilities_delete($pdo);
                elseif ($act == 'testimonials')
                    landing_admin_testimonials($pdo);
                elseif ($act == 'testimonials_form')
                    landing_admin_testimonials_form($pdo);
                elseif ($act == 'testimonials_save')
                    landing_admin_testimonials_save($pdo);
                elseif ($act == 'testimonials_delete')
                    landing_admin_testimonials_delete($pdo);
                elseif ($act == 'faq')
                    landing_admin_faqs($pdo);
                elseif ($act == 'faq_form')
                    landing_admin_faqs_form($pdo);
                elseif ($act == 'faq_save')
                    landing_admin_faqs_save($pdo);
                elseif ($act == 'faq_delete')
                    landing_admin_faqs_delete($pdo);
                elseif ($act == 'ekskul')
                    landing_admin_ekskul($pdo);
                elseif ($act == 'ekskul_form')
                    landing_admin_ekskul_form($pdo);
                elseif ($act == 'ekskul_save')
                    landing_admin_ekskul_save($pdo);
                elseif ($act == 'ekskul_delete')
                    landing_admin_ekskul_delete($pdo);
                elseif ($act == 'video')
                    landing_admin_video($pdo);
                elseif ($act == 'video_form')
                    landing_admin_video_form($pdo);
                elseif ($act == 'video_save')
                    landing_admin_video_save($pdo);
                elseif ($act == 'video_delete')
                    landing_admin_video_delete($pdo);
                else
                    landing_admin_settings($pdo);
                break;
    
            case 'utilitas_db':
                if ($act == 'index')
                    utilitas_db_index($pdo);
                elseif ($act == 'backup')
                    utilitas_db_backup($pdo);
                elseif ($act == 'restore')
                    utilitas_db_restore($pdo);
                elseif ($act == 'hapus_histori')
                    utilitas_db_hapus_histori($pdo);
                elseif ($act == 'hapus_setup')
                    utilitas_db_hapus_setup($pdo);
                elseif ($act == 'run_sql')
                    utilitas_db_run_sql($pdo);
                elseif ($act == 'truncate_selected')
                    utilitas_db_truncate_selected($pdo);
                elseif ($act == 'reset_aplikasi')
                    utilitas_db_reset_aplikasi($pdo);
                elseif ($act == 'run_patch')
                    utilitas_db_run_patch($pdo);
                else
                    utilitas_db_index($pdo);
                break;
    
            case 'app_config':
                if ($act == 'index')
                    app_config_index($pdo);
                elseif ($act == 'save')
                    app_config_save($pdo);
                elseif ($act == 'reset')
                    app_config_reset($pdo);
                else
                    app_config_index($pdo);
                break;
    
    
            // --- DATA MASTER ---
            case 'guru':
                if ($act == 'index')
                    guru_index($pdo);
                elseif ($act == 'form')
                    guru_form($pdo, $_GET['id'] ?? null);
                elseif ($act == 'save')
                    guru_save($pdo);
                elseif ($act == 'delete')
                    guru_delete($pdo, $_GET['id']);
                elseif ($act == 'export')
                    guru_export($pdo);
                elseif ($act == 'import')
                    guru_import($pdo);
                else
                    guru_index($pdo);
                break;
            case 'siswa':
                if ($act == 'index')
                    siswa_index($pdo);
                elseif ($act == 'form')
                    siswa_form($pdo, $_GET['id'] ?? null);
                elseif ($act == 'save')
                    siswa_save($pdo);
                elseif ($act == 'delete')
                    siswa_delete($pdo, $_GET['id']);
                elseif ($act == 'export')
                    siswa_export($pdo);
                elseif ($act == 'import')
                    siswa_import($pdo);
                elseif ($act == 'validasi_pengajuan')
                    siswa_validasi_pengajuan($pdo);
                elseif ($act == 'acc_pengajuan')
                    siswa_acc_pengajuan($pdo);
                elseif ($act == 'ajax_list')
                    // AJAX endpoint used by the siswa list page for live search
                    siswa_ajax_list($pdo);
                else
                    siswa_index($pdo);
                break;
            case 'kelas':
                if ($act == 'index')
                    kelas_index($pdo);
                elseif ($act == 'form')
                    kelas_form($pdo, $_GET['id'] ?? null);
                elseif ($act == 'save')
                    kelas_save($pdo);
                elseif ($act == 'toggle_jenis')
                    kelas_toggle_jenis($pdo);
                elseif ($act == 'import_previous')
                    kelas_import_from_previous($pdo);
                elseif ($act == 'delete')
                    kelas_delete($pdo, $_GET['id']);
                else
                    kelas_index($pdo);
                break;
            case 'mapel':
                if ($act == 'index')
                    mapel_index($pdo);
                elseif ($act == 'form')
                    mapel_form($pdo, $_GET['id'] ?? null);
                elseif ($act == 'save')
                    mapel_save($pdo);
                elseif ($act == 'delete')
                    mapel_delete($pdo, $_GET['id']);
                elseif ($act == 'export')
                    mapel_export($pdo);
                elseif ($act == 'import')
                    mapel_import($pdo);
                elseif ($act == 'update_urutan')
                    mapel_update_urutan($pdo);
                else
                    mapel_index($pdo);
                break;
            case 'struktur_kurikulum':
                if ($act == 'index')
                    struktur_kurikulum_index($pdo);
                elseif ($act == 'save')
                    struktur_kurikulum_save($pdo);
                elseif ($act == 'import_previous')
                    struktur_kurikulum_import_previous($pdo);
                elseif ($act == 'delete')
                    struktur_kurikulum_delete($pdo, $_GET['id']);
                else
                    struktur_kurikulum_index($pdo);
                break;
            case 'master_kegiatan':
                if ($act == 'index')
                    master_kegiatan_index($pdo);
                elseif ($act == 'save')
                    master_kegiatan_save($pdo);
                elseif ($act == 'delete')
                    master_kegiatan_delete($pdo, $_GET['id']);
                else
                    master_kegiatan_index($pdo);
                break;
            case 'master_jam':
                if ($act == 'index')
                    master_jam_index($pdo); // (Sudah mencakup form)
                elseif ($act == 'save')
                    master_jam_save($pdo);
                elseif ($act == 'delete')
                    master_jam_delete($pdo, $_GET['id']);
                elseif ($act == 'update_urutan')
                    master_jam_update_urutan($pdo);
                elseif ($act == 'copy_day')
                    master_jam_copy_day($pdo);
                else
                    master_jam_index($pdo);
                break;
            case 'kalender_akademik':
                require_once __DIR__ . '/../app/controllers/KalenderAkademikController.php';
                if ($act == 'index')
                    kalender_akademik_index($pdo);
                elseif ($act == 'save')
                    kalender_akademik_save($pdo);
                elseif ($act == 'delete')
                    kalender_akademik_delete($pdo);
                elseif ($act == 'api')
                    kalender_akademik_api($pdo);
                elseif ($act == 'import_holidays')
                    kalender_akademik_import_holidays($pdo);
                elseif ($act == 'export_pdf')
                    kalender_akademik_export_pdf($pdo);
                else
                    kalender_akademik_index($pdo);
                break;
            case 'ta':
                if ($act == 'index')
                    ta_index($pdo);
                elseif ($act == 'form')
                    ta_form($pdo, $_GET['id'] ?? null);
                elseif ($act == 'save')
                    ta_save($pdo, $_GET['id'] ?? null);
                elseif ($act == 'delete')
                    ta_delete($pdo, $_GET['id']);
                elseif ($act == 'set_aktif')
                    ta_set_aktif($pdo, $_GET['id']);
                else
                    ta_index($pdo);
                break;
    
            case 'surat':
                $controller = new SuratController($pdo);
                if ($act == 'index')
                    $controller->index();
                elseif ($act == 'masuk')
                    $controller->masuk();
                elseif ($act == 'keluar')
                    $controller->keluar();
                elseif ($act == 'template')
                    $controller->template();
                elseif ($act == 'get_nomor_otomatis')
                    $controller->get_nomor_otomatis();
                elseif ($act == 'get_template_content')
                    $controller->get_template_content();
                elseif ($act == 'save_masuk')
                    $controller->save_masuk();
                elseif ($act == 'save_keluar')
                    $controller->save_keluar();
                elseif ($act == 'save_template')
                    $controller->save_template();
                elseif ($act == 'print_keluar')
                    $controller->print_keluar();
                else
                    $controller->index();
                break;
    
    
            // --- AKADEMIK ---
            case 'penempatan':
                if ($act == 'index')
                    penempatan_index($pdo);
                elseif ($act == 'kelola')
                    penempatan_kelola($pdo);
                elseif ($act == 'copy_rombel')
                    penempatan_copy_rombel($pdo);
                elseif ($act == 'save')
                    penempatan_save($pdo);
                elseif ($act == 'delete')
                    penempatan_delete($pdo, $_GET['id']);
                elseif ($act == 'tambah_rombel')
                    penempatan_tambah_rombel($pdo);
                elseif ($act == 'hapus_rombel')
                    penempatan_hapus_rombel($pdo);
                else
                    penempatan_index($pdo);
                break;
            case 'penugasan_guru':
                if ($act == 'index')
                    penugasan_guru_index($pdo);
                elseif ($act == 'save_pembina')
                    penugasan_pembina_save($pdo);
                elseif ($act == 'delete_pembina')
                    penugasan_pembina_delete($pdo, $_GET['id']);
                elseif ($act == 'save_walas')
                    penugasan_walas_save($pdo);
                elseif ($act == 'save_guru_mapel')
                    penugasan_guru_mapel_save($pdo);
                elseif ($act == 'update_guru_mapel')
                    penugasan_guru_mapel_update($pdo);
                elseif ($act == 'delete_walas')
                    penugasan_walas_delete($pdo, $_GET['id']);
                elseif ($act == 'delete_guru_mapel')
                    penugasan_guru_mapel_delete($pdo, $_GET['id']);
                // [BARU] Routing untuk Jabatan
                elseif ($act == 'save_jabatan')
                    penugasan_jabatan_save($pdo);
                elseif ($act == 'delete_jabatan')
                    penugasan_jabatan_delete($pdo, $_GET['id']);
                else
                    penugasan_guru_index($pdo);
                break;
            case 'jadwal':
                if ($act == 'index')
                    jadwal_index($pdo);
                elseif ($act == 'save')
                    jadwal_save($pdo);
                elseif ($act == 'update')
                    jadwal_update($pdo);
                elseif ($act == 'delete')
                    jadwal_delete($pdo, $_GET['id']);
                else
                    jadwal_index($pdo);
                break;
            case 'manajemen_cp_tp':
                if ($act == 'index')
                    cp_tp_index($pdo);
                elseif ($act == 'cp_save')
                    cp_save($pdo);
                elseif ($act == 'cp_update')
                    cp_update($pdo);
                elseif ($act == 'tp_save')
                    tp_save($pdo);
                elseif ($act == 'tp_update')
                    tp_update($pdo);
                elseif ($act == 'cp_delete')
                    cp_delete($pdo, $_GET['id'] ?? 0);
                elseif ($act == 'tp_delete')
                    tp_delete($pdo, $_GET['id'] ?? 0);
                elseif ($act == 'tp_delete_bulk')
                    tp_delete_bulk($pdo);
                elseif ($act == 'tp_import')
                    tp_import($pdo);
                elseif ($act == 'cp_import')
                    cp_import($pdo);
                elseif ($act == 'download_template')
                    download_template_cp_tp($pdo);
                elseif ($act == 'ai_generate_tp')
                    cp_tp_ai_generate_tp($pdo);
                elseif ($act == 'ai_save_bulk')
                    cp_tp_ai_save_bulk($pdo);
                elseif ($act == 'ai_generate_missing_topics')
                    cp_tp_ai_generate_missing_topics($pdo);
                else
                    cp_tp_index($pdo);
                break;
    
            case 'ekskul':
                if ($act == 'index')
                    ekskul_index($pdo);
                elseif ($act == 'form')
                    ekskul_form($pdo);
                elseif ($act == 'save')
                    ekskul_save($pdo);
                elseif ($act == 'delete')
                    ekskul_delete($pdo);
                // Tab APIs
                elseif ($act == 'update_anggota')
                    ekskul_update_anggota($pdo);
                elseif ($act == 'search_students')
                    ekskul_search_students($pdo);
                // Jurnal
                elseif ($act == 'jurnal_form')
                    ekskul_jurnal_form($pdo);
                elseif ($act == 'jurnal_save')
                    ekskul_jurnal_save($pdo);
                elseif ($act == 'jurnal_delete')
                    ekskul_jurnal_delete($pdo);
                // Program Kerja
                elseif ($act == 'program_save')
                    ekskul_program_save($pdo);
                elseif ($act == 'program_delete')
                    ekskul_program_delete($pdo);
                elseif ($act == 'program_delete_file')
                    ekskul_program_delete_file($pdo);
                elseif ($act == 'program_upload')
                    ekskul_program_upload($pdo);
                // Galeri
                elseif ($act == 'galeri_save')
                    ekskul_galeri_save($pdo);
                elseif ($act == 'galeri_delete')
                    ekskul_galeri_delete($pdo);
                // Nilai
                elseif ($act == 'nilai_save')
                    ekskul_nilai_save($pdo);
                else
                    ekskul_index($pdo);
                break;
    
            case 'kokulikuler':
                if ($act == 'index')
                    kokulikuler_index($pdo);
                elseif ($act == 'form')
                    kokulikuler_form($pdo);
                elseif ($act == 'save')
                    kokulikuler_save($pdo);
                elseif ($act == 'delete')
                    kokulikuler_delete($pdo);
                // API & Sub-features
                elseif ($act == 'update_anggota')
                    kokulikuler_update_anggota($pdo);
                elseif ($act == 'search_students')
                    kokulikuler_search_students($pdo);
                elseif ($act == 'jurnal_form')
                    kokulikuler_jurnal_form($pdo);
                elseif ($act == 'jurnal_save')
                    kokulikuler_jurnal_save($pdo);
                elseif ($act == 'jurnal_delete')
                    kokulikuler_jurnal_delete($pdo);
                // Program Kerja & Agenda
                elseif ($act == 'program_upload')
                    kokulikuler_program_upload($pdo);
                elseif ($act == 'agenda_save')
                    kokulikuler_agenda_save($pdo);
                elseif ($act == 'agenda_delete')
                    kokulikuler_agenda_delete($pdo);
                // Galeri
                elseif ($act == 'galeri_save')
                    kokulikuler_galeri_save($pdo);
                elseif ($act == 'galeri_delete')
                    kokulikuler_galeri_delete($pdo);
                else
                    kokulikuler_index($pdo);
                break;
    
            case 'pembiasaan':
                if ($act == 'index')
                    pembiasaan_index($pdo);
                elseif ($act == 'form')
                    pembiasaan_form($pdo);
                elseif ($act == 'save')
                    pembiasaan_save($pdo);
                elseif ($act == 'delete')
                    pembiasaan_delete($pdo);
                // API
                elseif ($act == 'update_anggota')
                    pembiasaan_update_anggota($pdo);
                elseif ($act == 'search_students')
                    pembiasaan_search_students($pdo);
                // Jurnal
                elseif ($act == 'jurnal_form')
                    pembiasaan_jurnal_form($pdo);
                elseif ($act == 'jurnal_save')
                    pembiasaan_jurnal_save($pdo);
                elseif ($act == 'jurnal_delete')
                    pembiasaan_jurnal_delete($pdo);
                // Program Kerja & Agenda
                elseif ($act == 'program_upload')
                    pembiasaan_program_upload($pdo);
                elseif ($act == 'agenda_save')
                    pembiasaan_agenda_save($pdo);
                elseif ($act == 'agenda_delete')
                    pembiasaan_agenda_delete($pdo);
                // Galeri
                elseif ($act == 'galeri_upload')
                    pembiasaan_galeri_upload($pdo);
                elseif ($act == 'galeri_delete')
                    pembiasaan_galeri_delete($pdo);
                // Rekap Manual
                elseif ($act == 'rekap_form')
                    pembiasaan_rekap_form($pdo);
                elseif ($act == 'rekap_save')
                    pembiasaan_rekap_save($pdo);
                // Nilai
                elseif ($act == 'penilaian_save')
                    pembiasaan_penilaian_save($pdo);
                else
                    pembiasaan_index($pdo);
                break;
    
    
            // --- KEGIATAN KBM ---
            case 'jurnal_kbm':
                if ($act == 'index')
                    jurnal_kbm_index($pdo);
                elseif ($act == 'save')
                    jurnal_kbm_save($pdo);
                else
                    jurnal_kbm_index($pdo);
                break;
            case 'absensi_mapel':
                if ($act == 'index')
                    absensi_mapel_index($pdo);
                elseif ($act == 'form')
                    absensi_mapel_form($pdo);
                elseif ($act == 'save')
                    absensi_mapel_save($pdo);
                elseif ($act == 'sync_lms')
                    absensi_mapel_sync_lms($pdo);
                else
                    absensi_mapel_index($pdo);
                break;
            case 'input_nilai':
                if ($act == 'index')
                    input_nilai_index($pdo);
                elseif ($act == 'save')
                    input_nilai_save($pdo);
                elseif ($act == 'template')
                    input_nilai_template($pdo);
                elseif ($act == 'import')
                    input_nilai_import($pdo);
                else
                    input_nilai_index($pdo);
                break;
    
            case 'rekap_nilai':
                if ($act == 'simpan_bobot')
                    rekap_nilai_simpan_bobot($pdo);
                else
                    rekap_nilai_index($pdo);
                break;
            case 'cetak_rapor':
                if ($act == 'preview')          cetak_rapor_preview($pdo);
                elseif ($act == 'batch')        cetak_rapor_batch($pdo);
                elseif ($act == 'save_catatan') cetak_rapor_save_catatan($pdo);
                elseif ($act == 'generate')     cetak_rapor_generate_catatan($pdo);
                elseif ($act == 'get_catatan')  cetak_rapor_get_catatan($pdo);
                else                            cetak_rapor_index($pdo);
                break;
            case 'penilaian_sumatif':
                if ($act == 'index')
                    penilaian_sumatif_index($pdo);
                elseif ($act == 'form_agenda')
                    penilaian_sumatif_form_agenda($pdo);
                elseif ($act == 'save_agenda')
                    penilaian_sumatif_save_agenda($pdo);
                elseif ($act == 'form_nilai')
                    penilaian_sumatif_form_nilai($pdo);
                elseif ($act == 'save_nilai')
                    penilaian_sumatif_save_nilai($pdo);
                elseif ($act == 'template')
                    penilaian_sumatif_template($pdo);
                elseif ($act == 'import')
                    penilaian_sumatif_import($pdo);
                elseif ($act == 'delete_agenda')
                    penilaian_sumatif_delete_agenda($pdo);
                else
                    penilaian_sumatif_index($pdo);
                break;
    
            case 'komponen_sikap':
                require_once __DIR__ . '/../app/controllers/KomponenSikapController.php';
                KomponenSikapController::index();
                break;
    
            case 'penilaian_sikap':
                require_once __DIR__ . '/../app/controllers/PenilaianSikapController.php';
                if ($act == 'form_agenda')
                    PenilaianSikapController::form_agenda();
                elseif ($act == 'save_agenda')
                    PenilaianSikapController::save_agenda();
                elseif ($act == 'form_nilai')
                    PenilaianSikapController::form_nilai();
                elseif ($act == 'save_nilai')
                    PenilaianSikapController::save_nilai();
                elseif ($act == 'delete')
                    PenilaianSikapController::delete();
                else
                    PenilaianSikapController::index();
                break;
            case 'jadwal_piket':
                if ($act == 'save') {
                    jadwal_piket_save($pdo);
                } elseif ($act == 'save_non_kbm') {
                    jadwal_piket_save_non_kbm($pdo);
                } elseif ($act == 'delete') {
                    jadwal_piket_delete($pdo);
                } else {
                    jadwal_piket_index($pdo);
                }
                break;
            case 'absensi_piket':
                if ($act == 'index') {
                    absensi_piket_index($pdo);
                } elseif ($act == 'form') {
                    absensi_piket_form($pdo);
                } elseif ($act == 'save') {
                    absensi_piket_save($pdo);
                } else {
                    absensi_piket_index($pdo);
                }
                break;
            case 'absensi_scan':
                include __DIR__ . '/../app/views/absensi_scan_index.php';
                break;
            case 'absensi_guru':
                if ($act == 'index') {
                    absensi_guru_index($pdo);
                } elseif ($act == 'save') {
                    absensi_guru_save($pdo);
                } else {
                    absensi_guru_index($pdo);
                }
                break;
            case 'catatan_kasus':
                if ($act == 'index') {
                    catatan_kasus_index($pdo);
                } elseif ($act == 'save') {
                    catatan_kasus_save($pdo);
                } elseif ($act == 'delete') {
                    catatan_kasus_delete($pdo, $_GET['id']);
                } else {
                    catatan_kasus_index($pdo);
                }
                break;
            case 'catatan_kelas':
                if ($act == 'index') {
                    catatan_kelas_index($pdo);
                } elseif ($act == 'save') {
                    catatan_kelas_save($pdo);
                } else {
                    catatan_kelas_index($pdo);
                }
                break;
            case 'ppdb':
                if ($act == 'form')
                    ppdb_form($pdo);
                elseif ($act == 'save')
                    ppdb_save($pdo);
                elseif ($act == 'index')
                    ppdb_index($pdo);
                elseif ($act == 'detail')
                    ppdb_detail($pdo);  // BARU: Lihat detail & dokumen
                elseif ($act == 'update_status')
                    ppdb_update_status($pdo);
                elseif ($act == 'update_catatan')
                    ppdb_update_catatan($pdo);  // BARU: Update catatan
                elseif ($act == 'delete')
                    ppdb_delete($pdo);  // BARU: Hapus pendaftar
                elseif ($act == 'promote_massal')
                    ppdb_promote_massal($pdo);
                elseif ($act == 'get_template')
                    ppdb_get_template($pdo);
                elseif ($act == 'import')
                    ppdb_import($pdo);
                elseif ($act == 'regenerate_nipd')
                    ppdb_regenerate_nipd_form($pdo);    // BARU: Form & Preview Re-Generate NIPD
                elseif ($act == 'regenerate_nipd_exec')
                    ppdb_regenerate_nipd_exec($pdo);    // BARU: Eksekusi Re-Generate NIPD
                else
                    ppdb_index($pdo);
                break;
    
            case 'mutasi_masuk':
                if ($act == 'form')
                    mutasi_masuk_form($pdo);
                elseif ($act == 'save')
                    mutasi_masuk_save($pdo);
                elseif ($act == 'index')
                    mutasi_masuk_index($pdo);
                elseif ($act == 'export_excel')
                    mutasi_masuk_export_excel($pdo);
                elseif ($act == 'export_pdf')
                    mutasi_masuk_export_pdf($pdo);
                elseif ($act == 'detail')
                    mutasi_masuk_detail($pdo);
                elseif ($act == 'promote')
                    mutasi_masuk_promote($pdo);
                else
                    mutasi_masuk_index($pdo);
                break;
            case 'mutasi_siswa':
                if ($act == 'index') {
                    mutasi_siswa_index($pdo);
                } elseif ($act == 'save') {
                    mutasi_siswa_save($pdo);
                } elseif ($act == 'batal') {
                    mutasi_siswa_batal($pdo);
                } elseif ($act == 'get_siswa_api') {
                    mutasi_siswa_get_siswa_api($pdo);
                } elseif ($act == 'get_mutation_api') {
                    mutasi_siswa_get_mutation_api($pdo);
                } else
                    mutasi_siswa_index($pdo);
                break;
            case 'lulusan':
                if ($act == 'index') {
                    lulusan_index($pdo);
                } elseif ($act == 'proses') {
                    lulusan_proses($pdo);
                } elseif ($act == 'form') {
                    lulusan_form($pdo);
                } elseif ($act == 'save') {
                    lulusan_save($pdo);
                } elseif ($act == 'update_tracer') { // [BARU] Route untuk update tracer
                    lulusan_update_tracer($pdo);
                } elseif ($act == 'batal') { // Tambahkan route Batal juga
                    lulusan_batal($pdo);
                } elseif ($act == 'delete') {
                    lulusan_delete($pdo, $_GET['id']);
                } else {
                    lulusan_index($pdo);
                }
                break;
    
            case 'kewirausahaan':
                $act = $_GET['act'] ?? 'index';
                if ($act == 'index')
                    kewirausahaan_index($pdo);
                elseif ($act == 'form')
                    kewirausahaan_form($pdo);
                elseif ($act == 'save')
                    kewirausahaan_save($pdo);
                elseif ($act == 'delete')
                    kewirausahaan_delete($pdo);
                elseif ($act == 'update_anggota')
                    kewirausahaan_update_anggota($pdo);
                elseif ($act == 'search_students')
                    kewirausahaan_search_students($pdo);
                // Jurnal
                elseif ($act == 'jurnal_form')
                    kewirausahaan_jurnal_form($pdo);
                elseif ($act == 'jurnal_save')
                    kewirausahaan_jurnal_save($pdo);
                elseif ($act == 'jurnal_delete')
                    kewirausahaan_jurnal_delete($pdo);
                // Tahapan
                elseif ($act == 'tahapan_save')
                    kewirausahaan_tahapan_save($pdo);
                elseif ($act == 'tahapan_delete')
                    kewirausahaan_tahapan_delete($pdo);
                elseif ($act == 'tahapan_reorder')
                    kewirausahaan_tahapan_reorder($pdo);
                // Produk
                elseif ($act == 'produk_save')
                    kewirausahaan_produk_save($pdo);
                elseif ($act == 'produk_delete')
                    kewirausahaan_produk_delete($pdo);
                // Galeri
                elseif ($act == 'galeri_save')
                    kewirausahaan_galeri_save($pdo);
                elseif ($act == 'galeri_delete')
                    kewirausahaan_galeri_delete($pdo);
                elseif ($act == 'program_upload')
                    kewirausahaan_program_upload($pdo);
                // Keuangan
                elseif ($act == 'keuangan_save')
                    kewirausahaan_keuangan_save($pdo);
                elseif ($act == 'keuangan_delete')
                    kewirausahaan_keuangan_delete($pdo);
                // Program Kerja & Agenda
                elseif ($act == 'program_save')
                    kewirausahaan_program_save($pdo);
                elseif ($act == 'program_delete_file')
                    kewirausahaan_program_delete_file($pdo);
                elseif ($act == 'agenda_delete')
                    kewirausahaan_agenda_delete($pdo);
                else
                    kewirausahaan_index($pdo);
                break;
            case 'tahfidz':
                $act = $_GET['act'] ?? 'index';
                if ($act == 'index')
                    tahfidz_index($pdo);
                elseif ($act == 'form')
                    tahfidz_form($pdo);
                elseif ($act == 'save')
                    tahfidz_save($pdo);
                elseif ($act == 'delete')
                    tahfidz_delete($pdo);
                elseif ($act == 'update_anggota')
                    tahfidz_update_anggota($pdo);
                elseif ($act == 'search_students')
                    tahfidz_search_students($pdo);
                elseif ($act == 'jurnal_save')
                    tahfidz_jurnal_save($pdo);
                elseif ($act == 'jurnal_delete')
                    tahfidz_jurnal_delete($pdo);
                elseif ($act == 'jurnal_form')
                    tahfidz_jurnal_form($pdo);
                elseif ($act == 'save_setoran')
                    tahfidz_setoran_save($pdo);
                elseif ($act == 'delete_setoran')
                    tahfidz_setoran_delete($pdo);
                elseif ($act == 'program_upload')
                    tahfidz_program_upload($pdo);
                elseif ($act == 'agenda_save')
                    tahfidz_agenda_save($pdo);
                elseif ($act == 'agenda_delete')
                    tahfidz_agenda_delete($pdo);
                // elseif ($act == 'program_delete_file') tahfidz_proker_delete($pdo); // Deprecated
                else
                    tahfidz_index($pdo);
                break;
    
            case 'ai_generator':
                require_once __DIR__ . '/../app/controllers/AIGeneratorController.php';
                $act = $_GET['act'] ?? 'index';
                if ($act == 'index')
                    ai_generator_index($pdo);
                elseif ($act == 'create')
                    ai_generator_create($pdo);
                elseif ($act == 'process')
                    ai_generator_process($pdo);
                elseif ($act == 'get_cp')
                    ai_generator_get_cp($pdo);
                elseif ($act == 'get_tp')
                    ai_generator_get_tp($pdo);
                elseif ($act == 'generate_tp')
                    ai_generator_generate_tp($pdo);
                elseif ($act == 'generate_profil')
                    ai_generator_generate_profil($pdo);
                elseif ($act == 'upload_ref')
                    ai_generator_upload_ref($pdo);
                elseif ($act == 'set_manual_ref')
                    ai_generator_set_manual_ref($pdo);
                elseif ($act == 'clear_ref')
                    ai_generator_clear_ref($pdo);
                elseif ($act == 'save')
                    ai_generator_save($pdo);
                elseif ($act == 'delete')
                    ai_generator_delete($pdo);
                elseif ($act == 'export')
                    ai_generator_export($pdo);
                elseif ($act == 'preview')
                    ai_generator_preview($pdo);
                else
                    ai_generator_index($pdo);
                break;
    
            case 'perangkat_upload':
                if ($act == 'index')
                    perangkat_upload_index($pdo);
                elseif ($act == 'upload')
                    perangkat_upload_save($pdo);
                elseif ($act == 'update')
                    perangkat_upload_update($pdo); // New Edit Action
                elseif ($act == 'delete')
                    perangkat_upload_delete($pdo);
                else
                    perangkat_upload_index($pdo);
                break;
    
            case 'perangkat':
                $act = $_GET['act'] ?? 'index';
                if ($act == 'index')
                    perangkat_index($pdo);
                elseif ($act == 'form')
                    perangkat_form($pdo);
                elseif ($act == 'save')
                    perangkat_save($pdo);
                elseif ($act == 'delete')
                    perangkat_delete($pdo);
                elseif ($act == 'print')
                    perangkat_print($pdo);
                elseif ($act == 'get_template')
                    perangkat_get_template($pdo);
                else
                    perangkat_index($pdo);
                break;
    
            case 'template_dokumen':
                $act = $_GET['act'] ?? 'index';
                if ($act == 'index')
                    template_dokumen_index($pdo);
                elseif ($act == 'save')
                    template_dokumen_save($pdo);
                elseif ($act == 'delete')
                    template_dokumen_delete($pdo);
                else
                    template_dokumen_index($pdo);
                break;
    
            // --- LAPORAN ---
            case 'laporan':
                // Mendaftarkan SEMUA action Laporan
                if ($act == 'siswa')
                    laporan_siswa($pdo);
                elseif ($act == 'siswa_export_excel')
                    laporan_siswa_export_excel($pdo);
                elseif ($act == 'siswa_export_pdf')
                    laporan_siswa_export_pdf($pdo);
                elseif ($act == 'siswa_print')
                    laporan_siswa_print($pdo);
                elseif ($act == 'guru')
                    laporan_guru($pdo);
                elseif ($act == 'guru_export_excel')
                    laporan_guru_export_excel($pdo);
                elseif ($act == 'guru_export_pdf')
                    laporan_guru_export_pdf($pdo);
                elseif ($act == 'guru_print')
                    laporan_guru_print($pdo);
                elseif ($act == 'kelas')
                    laporan_kelas($pdo);
                elseif ($act == 'kelas_export_excel')
                    laporan_kelas_export_excel($pdo);
                elseif ($act == 'kelas_export_pdf')
                    laporan_kelas_export_pdf($pdo);
                elseif ($act == 'kelas_print')
                    laporan_kelas_print($pdo);
                elseif ($act == 'mapel')
                    laporan_mapel($pdo);
                elseif ($act == 'mapel_export_excel')
                    laporan_mapel_export_excel($pdo);
                elseif ($act == 'mapel_export_pdf')
                    laporan_mapel_export_pdf($pdo);
                elseif ($act == 'mapel_print')
                    laporan_mapel_print($pdo);
                elseif ($act == 'penempatan_siswa')
                    laporan_penempatan_siswa($pdo);
                elseif ($act == 'penempatan_siswa_export_excel')
                    laporan_penempatan_siswa_export_excel($pdo);
                elseif ($act == 'penempatan_siswa_export_pdf')
                    laporan_penempatan_siswa_export_pdf($pdo);
                elseif ($act == 'penempatan_siswa_print')
                    laporan_penempatan_siswa_print($pdo);
                elseif ($act == 'absensi_siswa_mapel')
                    laporan_absensi_siswa_mapel($pdo);
                elseif ($act == 'absensi_siswa_mapel_export_excel')
                    laporan_absensi_siswa_mapel_export_excel($pdo);
                elseif ($act == 'absensi_siswa_mapel_export_pdf')
                    laporan_absensi_siswa_mapel_export_pdf($pdo);
                elseif ($act == 'absensi_siswa_mapel_print')
                    laporan_absensi_siswa_mapel_print($pdo);
                elseif ($act == 'absensi_siswa_piket')
                    laporan_absensi_siswa_piket($pdo);
                elseif ($act == 'absensi_siswa_piket_export_excel')
                    laporan_absensi_siswa_piket_export_excel($pdo);
                elseif ($act == 'absensi_siswa_piket_export_pdf')
                    laporan_absensi_siswa_piket_export_pdf($pdo);
                elseif ($act == 'absensi_siswa_piket_print')
                    laporan_absensi_siswa_piket_print($pdo);
                elseif ($act == 'absensi_guru')
                    laporan_absensi_guru($pdo);
                elseif ($act == 'absensi_guru_export_excel')
                    laporan_absensi_guru_export_excel($pdo);
                elseif ($act == 'absensi_guru_export_pdf')
                    laporan_absensi_guru_export_pdf($pdo);
                elseif ($act == 'absensi_guru_print')
                    laporan_absensi_guru_print($pdo);
                elseif ($act == 'jurnal')
                    laporan_jurnal($pdo);
                elseif ($act == 'jurnal_export_excel')
                    laporan_jurnal_export_excel($pdo);
                elseif ($act == 'jurnal_export_pdf')
                    laporan_jurnal_export_pdf($pdo);
                elseif ($act == 'jurnal_print')
                    laporan_jurnal_print($pdo);
                elseif ($act == 'jadwal_pelajaran')
                    laporan_jadwal_pelajaran($pdo);
                elseif ($act == 'jadwal_pelajaran_export_excel')
                    laporan_jadwal_pelajaran_export_excel($pdo);
                elseif ($act == 'jadwal_pelajaran_export_pdf')
                    laporan_jadwal_pelajaran_export_pdf($pdo);
                elseif ($act == 'jadwal_pelajaran_print')
                    laporan_jadwal_pelajaran_print($pdo);
                elseif ($act == 'catatan_kasus')
                    laporan_catatan_kasus($pdo);
                elseif ($act == 'catatan_kasus_export_excel')
                    laporan_catatan_kasus_export_excel($pdo);
                elseif ($act == 'catatan_kasus_export_pdf')
                    laporan_catatan_kasus_export_pdf($pdo);
                elseif ($act == 'catatan_kasus_print')
                    laporan_catatan_kasus_print($pdo);
                elseif ($act == 'catatan_kelas')
                    laporan_catatan_kelas($pdo);
                elseif ($act == 'catatan_kelas_export_excel')
                    laporan_catatan_kelas_export_excel($pdo);
                elseif ($act == 'catatan_kelas_export_pdf')
                    laporan_catatan_kelas_export_pdf($pdo);
                elseif ($act == 'catatan_kelas_print')
                    laporan_catatan_kelas_print($pdo);
                elseif ($act == 'ppdb')
                    laporan_ppdb($pdo);
                elseif ($act == 'ppdb_export_excel')
                    laporan_ppdb_export_excel($pdo);
                elseif ($act == 'ppdb_export_pdf')
                    laporan_ppdb_export_pdf($pdo);
                elseif ($act == 'ppdb_print')
                    laporan_ppdb_print($pdo);
                elseif ($act == 'mutasi_masuk')
                    laporan_mutasi_masuk($pdo);
                elseif ($act == 'mutasi_masuk_export_excel')
                    laporan_mutasi_masuk_export_excel($pdo);
                elseif ($act == 'mutasi_masuk_export_pdf')
                    laporan_mutasi_masuk_export_pdf($pdo);
                elseif ($act == 'mutasi_masuk_print')
                    laporan_mutasi_masuk_print($pdo);
                elseif ($act == 'mutasi_keluar')
                    laporan_mutasi_keluar($pdo);
                elseif ($act == 'mutasi_keluar_export_excel')
                    laporan_mutasi_keluar_export_excel($pdo);
                elseif ($act == 'mutasi_keluar_export_pdf')
                    laporan_mutasi_keluar_export_pdf($pdo);
                elseif ($act == 'mutasi_keluar_print')
                    laporan_mutasi_keluar_print($pdo);
                else
                    laporan_siswa($pdo); // Default laporan jika act tidak ditemukan
                break;
    
            // ===== CBT TERINTEGRASI =====
            case 'cbt_dashboard':
            case 'cbt':
                require_once __DIR__ . '/../app/controllers/CbtController.php';
                CbtController::dashboard($pdo);
                break;
    
            case 'cbt_bank_soal':
                require_once __DIR__ . '/../app/controllers/CbtController.php';
                CbtController::bank_soal($pdo, $act);
                break;
    
            case 'cbt_paket':
                CbtController::paket($pdo, $act);
                break;
    
            case 'cbt_jadwal':
                CbtController::jadwal($pdo, $act);
                break;
    
            case 'cbt_peserta':
                CbtController::peserta($pdo, $act);
                break;
    
            case 'cbt_hasil':
                CbtController::hasil($pdo);
                break;
    
            case 'chat':
                chat_index($pdo);
                break;
    
            default:
                // Jika modul tidak ditemukan, redirect ke dashboard sesuai peran
                if (in_array('Siswa', $_SESSION['roles'] ?? []) && !in_array('Admin', $_SESSION['roles'] ?? []) && !in_array('Guru', $_SESSION['roles'] ?? [])) {
                    siswa_portal_dashboard();
                } else {
                    dashboard_index($pdo);
                }
                break;
        }
}
