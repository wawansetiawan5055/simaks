<?php
require_once __DIR__ . '/../models/SiswaModel.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Pastikan path ini benar

// Pastikan use statement ada
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

function siswa_index($pdo) {
    if (!check_access('siswa', 'index')) redirect('index.php');
    $q = trim($_GET['q'] ?? '');
    $status = $_GET['status'] ?? 'Aktif'; // Default ke Aktif
    $id_ta_view = $_GET['id_ta'] ?? $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? null;
    
    // Ambil daftar TA untuk dropdown
    require_once __DIR__ . '/../models/TahunAjaranModel.php';
    $ta_list = TahunAjaranModel::all($pdo);
    
    // [BARU] Cek jumlah pengajuan perubahan data
    require_once __DIR__ . '/../models/ProfilSiswaModel.php';
    $pengajuan_menunggu = ProfilSiswaModel::getPengajuanMenunggu($pdo);
    $jml_pengajuan = count($pengajuan_menunggu);

    $siswa_list = SiswaModel::all($pdo, $id_ta_view, $status); 
    include __DIR__ . '/../views/siswa_index.php'; 
}

// [BARU] Halaman Validasi Pengajuan
function siswa_validasi_pengajuan($pdo) {
    if (!check_access('siswa', 'update')) redirect('index.php');
    require_once __DIR__ . '/../models/ProfilSiswaModel.php';
    $pengajuan_menunggu = ProfilSiswaModel::getPengajuanMenunggu($pdo);
    include __DIR__ . '/../views/siswa_validasi.php';
}

// [BARU] Proses ACC/Tolak Pengajuan
function siswa_acc_pengajuan($pdo) {
    if (!check_access('siswa', 'update')) redirect('index.php');
    require_once __DIR__ . '/../models/ProfilSiswaModel.php';

    $id_pengajuan = $_POST['id_pengajuan'] ?? null;
    $action = $_POST['action'] ?? null;
    $catatan = $_POST['catatan_admin'] ?? '';

    if (!$id_pengajuan || !$action) {
        $_SESSION['pesan_error'] = "Data tidak lengkap.";
        redirect('index.php?mod=siswa&act=validasi_pengajuan');
        return;
    }

    try {
        $pengajuan = ProfilSiswaModel::getPengajuanById($pdo, $id_pengajuan);
        if (!$pengajuan) throw new Exception("Pengajuan tidak ditemukan.");

        if ($action == 'setuju') {
            // Update status ke Disetujui
            ProfilSiswaModel::updateStatusPengajuan($pdo, $id_pengajuan, 'Disetujui', $catatan);

            // Update tabel terkait
            $data_ubah = json_decode($pengajuan['data_perubahan'], true);
            
            if (isset($data_ubah['jenis_berkas']) && isset($data_ubah['file_temp'])) {
                // INI ADALAH PENGAJUAN UPLOAD BERKAS
                $jenis_berkas = $data_ubah['jenis_berkas'];
                $file_temp = $data_ubah['file_temp'];
                $file_final = str_replace('temp_', '', $file_temp);
                
                $dir = __DIR__ . '/../../public/uploads/siswa/';
                if (file_exists($dir . $file_temp)) {
                    rename($dir . $file_temp, $dir . $file_final);
                }
                
                // Pastikan record profil ada
                $stmt = $pdo->prepare("SELECT id_profil FROM profil_siswa WHERE id_siswa = ?");
                $stmt->execute([$pengajuan['id_siswa']]);
                if (!$stmt->fetch()) {
                    $pdo->prepare("INSERT INTO profil_siswa (id_siswa) VALUES (?)")->execute([$pengajuan['id_siswa']]);
                }
                
                // Update database
                $sql = "UPDATE profil_siswa SET $jenis_berkas = ? WHERE id_siswa = ?";
                $pdo->prepare($sql)->execute([$file_final, $pengajuan['id_siswa']]);

            } else {
                // INI ADALAH PENGAJUAN PERUBAHAN DATA TEKS
                // Pisahkan data untuk tabel 'siswa' dan tabel 'profil_siswa'
                $kolom_siswa = ['nama', 'nik', 'tempat_lahir', 'tanggal_lahir'];
                $update_siswa = [];
                $update_profil = [];

                foreach ($data_ubah as $k => $v) {
                    if (empty($v)) continue;
                    if (in_array($k, $kolom_siswa)) {
                        $update_siswa[$k] = $v;
                    } else {
                        $update_profil[$k] = $v;
                    }
                }

                // Eksekusi update tabel siswa
                if (!empty($update_siswa)) {
                    $set_parts = [];
                    $params = [];
                    foreach ($update_siswa as $k => $v) {
                        $set_parts[] = "$k = ?";
                        $params[] = $v;
                    }
                    $params[] = $pengajuan['id_siswa'];
                    $sql = "UPDATE siswa SET " . implode(", ", $set_parts) . " WHERE id_siswa = ?";
                    $pdo->prepare($sql)->execute($params);
                }

                // Eksekusi update tabel profil_siswa
                if (!empty($update_profil)) {
                    // Pastikan record profil ada
                    $stmt = $pdo->prepare("SELECT id_profil FROM profil_siswa WHERE id_siswa = ?");
                    $stmt->execute([$pengajuan['id_siswa']]);
                    if (!$stmt->fetch()) {
                        $pdo->prepare("INSERT INTO profil_siswa (id_siswa) VALUES (?)")->execute([$pengajuan['id_siswa']]);
                    }

                    $set_parts = [];
                    $params = [];
                    foreach ($update_profil as $k => $v) {
                        $set_parts[] = "$k = ?";
                        $params[] = $v;
                    }
                    $params[] = $pengajuan['id_siswa'];
                    $sql = "UPDATE profil_siswa SET " . implode(", ", $set_parts) . " WHERE id_siswa = ?";
                    $pdo->prepare($sql)->execute($params);
                }
            }

            $_SESSION['pesan_sukses'] = "Pengajuan berhasil disetujui dan data diperbarui.";

        } else if ($action == 'tolak') {
            ProfilSiswaModel::updateStatusPengajuan($pdo, $id_pengajuan, 'Ditolak', $catatan);
            $_SESSION['pesan_sukses'] = "Pengajuan telah ditolak.";
        }

    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal memproses pengajuan: " . $e->getMessage();
    }

    redirect('index.php?mod=siswa&act=validasi_pengajuan');
}

function siswa_form($pdo, $id=null) {
    $permission = $id ? 'update' : 'create';
    if (!can_do($pdo, 'siswa', $permission)) redirect('index.php?mod=siswa');

    require_once __DIR__ . '/../models/DashboardModel.php';
    $siswa = $id ? SiswaModel::find($pdo, $id) : null;
    $ta_list = DashboardModel::getTahunAjaranList($pdo);
    include __DIR__ . '/../views/siswa_form.php';
}

function siswa_save($pdo) {
    $id = $_POST['id_siswa'] ?? null;
    $permission = $id ? 'update' : 'create';
    
    if (!can_do($pdo, 'siswa', $permission)) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk " . ($id ? "mengubah" : "menambah") . " data.";
        redirect('index.php?mod=siswa');
        return;
    }

    try {
        SiswaModel::save($pdo, $_POST);
        $_SESSION['pesan_sukses'] = "Data siswa berhasil disimpan.";
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
    }
    redirect('index.php?mod=siswa');
}

/**
 * [REVISI DIMULAI]
 * Fungsi ini diubah untuk menangani pesan error.
 */
function siswa_delete($pdo, $id) {
    if (!can_do($pdo, 'siswa', 'delete')) {
        $_SESSION['pesan_error'] = "Akses ditolak. Anda tidak memiliki izin untuk menghapus data.";
        redirect('index.php?mod=siswa');
        return;
    }
    
    try {
        // Panggil model delete
        $result = SiswaModel::delete($pdo, $id);
        
        // Cek result jika model mengembalikan false (tapi biasanya PDO throw exception)
        if ($result) {
            $_SESSION['pesan_sukses'] = "Data siswa berhasil dihapus.";
        } else {
             // Fallback jika model return false tanpa exception
             $_SESSION['pesan_error'] = "Gagal menghapus: Siswa ini mungkin masih terikat dengan data lain.";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
             $_SESSION['pesan_error'] = "Gagal menghapus: Data siswa ini tidak bisa dihapus karena masih terikat dengan data lain (Absensi/Nilai/Kelas). Silakan hapus data terkait terlebih dahulu.";
        } else {
             $_SESSION['pesan_error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
    
    redirect('index.php?mod=siswa');
}
/**
 * [REVISI SELESAI]
 */

function siswa_export($pdo) {
    if (!is_logged_in() || !can_do($pdo, 'siswa', 'create')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki izin untuk mengimpor data siswa.";
        redirect('index.php?mod=siswa');
    }
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Header Columns
    $headers = ['ID Siswa','Nama','NISN','NIPD','NIK','JK','Tempat Lahir','Tanggal Lahir','Sekolah Asal','Status'];
    $sheet->fromArray($headers, null, 'A1');
    
    // Style headers (Bold + Auto width)
    foreach (range('A', 'J') as $col) {
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $is_template = isset($_GET['template']) && $_GET['template'] == '1';
    
    if (!$is_template) {
        $data = SiswaModel::all($pdo);
        $row = 2;
        foreach ($data as $s) {
            $sheet->fromArray([
                $s['id_siswa'], $s['nama'], $s['nisn'], $s['nipd'], $s['nik'], 
                $s['jk'], $s['tempat_lahir'], $s['tanggal_lahir'], $s['sekolah_asal'], $s['status_aktif']
            ], null, 'A'.$row++);
        }
        $filename = "siswa_export_" . date('Y-m-d') . ".xlsx";
    } else {
        // Example Row for Template
        $sheet->fromArray([
            '', 'Ahmad Siswa', '0012345678', '212210001', '3201010101010001', 
            'L', 'Bandung', '2008-01-01', 'SMP N 1 Bandung', 'Aktif'
        ], null, 'A2');
        $filename = "template_siswa.xlsx";
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


function siswa_import($pdo) {
    if (!is_logged_in() || !check_access('siswa')) {
        $_SESSION['pesan_error'] = "Anda tidak memiliki akses ke fitur ini.";
        redirect('index.php?mod=siswa');
    }
    
    if (isset($_FILES['file_excel']['tmp_name']) && $_FILES['file_excel']['tmp_name']) {
         try {
             $inputFileName = $_FILES['file_excel']['tmp_name'];
             $spreadsheet = IOFactory::load($inputFileName);
             $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

             for ($i = 2; $i <= count($sheetData); $i++) { 
                 $row = $sheetData[$i];

                 // -----------------------------------------------------------------
                 // [REVISI DIMULAI] Baris 83
                 // Logika baru untuk parsing Tanggal Lahir (Kolom H)
                 // Ini menangani format 'dd/mm/yyyy' (string) ATAU format Excel (angka)
                 
                 $tanggal_lahir_db = null;
                 if (!empty($row['H'])) { // Kolom H adalah Tanggal Lahir
                     if (is_numeric($row['H'])) {
                         // Jika formatnya angka (Excel timestamp)
                         $tanggal_lahir_db = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['H'])->format('Y-m-d');
                     } else if (strpos($row['H'], '/') !== false) {
                         // Jika formatnya string 'dd/mm/yyyy'
                         $dateObj = \DateTime::createFromFormat('d/m/Y', $row['H']);
                         if ($dateObj) {
                             $tanggal_lahir_db = $dateObj->format('Y-m-d');
                         }
                     }
                     // (Opsional) Jika formatnya string 'yyyy-mm-dd'
                     else if (strpos($row['H'], '-') !== false) {
                        $dateObj = \DateTime::createFromFormat('Y-m-d', $row['H']);
                        if ($dateObj) {
                            $tanggal_lahir_db = $dateObj->format('Y-m-d');
                        }
                     }
                 }
                 // [REVISI SELESAI]
                 // -----------------------------------------------------------------

                  // [REVISI] Hubungkan dengan Tahun Ajaran Masuk
                  // Default menggunakan TA Aktif jika tidak diisi di Excel
                  $id_ta_masuk = $row['K'] ?? $_SESSION['id_ta_aktif'] ?? 0;

                  SiswaModel::save($pdo, [
                      'nama' => $row['B'] ?? '', 
                      'nisn' => $row['C'] ?? '', 
                      'nipd' => $row['D'] ?? '', 
                      'nik' => $row['E'] ?? '', 
                      // Normalize Gender
                      'jk' => (strtoupper($row['F'] ?? '') == 'L' || strtoupper($row['F'] ?? '') == 'LAKI-LAKI') ? 'Laki-laki' : 
                             ((strtoupper($row['F'] ?? '') == 'P' || strtoupper($row['F'] ?? '') == 'PEREMPUAN') ? 'Perempuan' : ($row['F'] ?? '')), 
                      'tempat_lahir' => $row['G'] ?? '', 
                      'tanggal_lahir' => $tanggal_lahir_db, 
                      'sekolah_asal' => $row['I'] ?? '', 
                      'status_aktif' => $row['J'] ?? 'Aktif',
                      'id_ta_masuk' => $id_ta_masuk
                  ]);
             }
             $_SESSION['pesan_sukses'] = "Data siswa berhasil diimpor.";
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
             $_SESSION['pesan_error'] = 'Error loading file: '.$e->getMessage();
        } catch (Exception $e) {
             $_SESSION['pesan_error'] = 'Error importing data: '.$e->getMessage();
        }
    } else {
        $_SESSION['pesan_error'] = 'Tidak ada file yang diunggah atau terjadi kesalahan.';
    }
    redirect('index.php?mod=siswa');
}

// AJAX: return siswa list as JSON for frontend rendering
function siswa_ajax_list($pdo) {
    if (!check_access('siswa', 'index')) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        return;
    }
    try {
        $q = trim($_GET['q'] ?? '');
        $status = $_GET['status'] ?? 'Semua';
        $id_ta_view = $_GET['id_ta'] ?? $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? null;
        $siswa_list = SiswaModel::all($pdo, $id_ta_view, $status, $q);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'data' => $siswa_list]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}