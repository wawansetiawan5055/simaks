<?php
/**
 * CBT - AdminController
 * Mengelola data master: Kelas, Mapel, Siswa
 */
class AdminController
{

    public static function kelas($pdo, $act)
    {
        if ($act === 'save') {
            self::save_kelas($pdo);
        } elseif ($act === 'delete') {
            self::delete_kelas($pdo);
        } elseif ($act === 'import_simaks') {
            self::import_kelas_simaks($pdo);
        } elseif ($act === 'import_excel') {
            self::import_kelas_excel($pdo);
        } else {
            $kelas = $pdo->query("SELECT * FROM cbt_kelas ORDER BY tingkat, nama_kelas")->fetchAll();
            $kelas_simaks = cbt_get_kelas($pdo); // Fetch all from SIMAKS (via bridge)
            require_once CBT_ROOT . '/app/views/admin/kelas.php';
        }
    }

    public static function mapel($pdo, $act)
    {
        if ($act === 'save') {
            self::save_mapel($pdo);
        } elseif ($act === 'delete') {
            self::delete_mapel($pdo);
        } elseif ($act === 'import_simaks') {
            self::import_mapel_simaks($pdo);
        } elseif ($act === 'import_excel') {
            self::import_mapel_excel($pdo);
        } else {
            $mapel = $pdo->query("SELECT * FROM cbt_mapel ORDER BY nama_mapel")->fetchAll();
            require_once CBT_ROOT . '/app/views/admin/mapel.php';
        }
    }

    public static function siswa($pdo, $act)
    {
        if ($act === 'save') {
            self::save_siswa($pdo);
        } elseif ($act === 'delete') {
            self::delete_siswa($pdo);
        } elseif ($act === 'import_excel') {
            self::import_siswa_excel($pdo);
        } elseif ($act === 'import_simaks') {
            self::import_siswa_simaks($pdo);
        } elseif ($act === 'reset_password') {
            self::reset_password($pdo);
        } elseif ($act === 'download_template') {
            self::download_template_siswa($pdo);
        } elseif ($act === 'bulk_upload_foto') {
            self::bulk_upload_foto($pdo);
        } elseif ($act === 'delete_all') {
            self::delete_all_siswa($pdo);
        } elseif ($act === 'delete_selected') {
            self::delete_selected_siswa($pdo);
        } else {
            $id_kelas = (int) ($_GET['id_kelas'] ?? 0);
            $kelas = cbt_get_kelas($pdo);
            $siswa = cbt_get_siswa_by_kelas($pdo, $id_kelas ?: null);
            require_once CBT_ROOT . '/app/views/admin/siswa.php';
        }
    }

    // ---- CRUD Helpers ----

    private static function save_kelas($pdo)
    {
        $id = (int) ($_POST['id_kelas'] ?? 0);
        $nama = trim($_POST['nama_kelas'] ?? '');
        $tingkat = trim($_POST['tingkat'] ?? '');
        $jurusan = trim($_POST['jurusan'] ?? '');
        if (!$nama) {
            header('Location: index.php?mod=kelola_kelas&err=empty');
            exit;
        }

        if ($id) {
            $pdo->prepare("UPDATE cbt_kelas SET nama_kelas=?, tingkat=?, jurusan=? WHERE id_kelas=?")->execute([$nama, $tingkat, $jurusan, $id]);
        } else {
            $pdo->prepare("INSERT INTO cbt_kelas (nama_kelas, tingkat, jurusan) VALUES (?,?,?)")->execute([$nama, $tingkat, $jurusan]);
        }
        header('Location: index.php?mod=kelola_kelas&ok=1');
        exit;
    }

    private static function delete_kelas($pdo)
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id)
            $pdo->prepare("DELETE FROM cbt_kelas WHERE id_kelas=?")->execute([$id]);
        header('Location: index.php?mod=kelola_kelas&del=1');
        exit;
    }

    private static function save_mapel($pdo)
    {
        $id = (int) ($_POST['id_mapel'] ?? 0);
        $nama = trim($_POST['nama_mapel'] ?? '');
        $kode = trim($_POST['kode_mapel'] ?? '');
        if (!$nama) {
            header('Location: index.php?mod=kelola_mapel&err=empty');
            exit;
        }

        if ($id) {
            $pdo->prepare("UPDATE cbt_mapel SET nama_mapel=?, kode_mapel=? WHERE id_mapel=?")->execute([$nama, $kode, $id]);
        } else {
            $pdo->prepare("INSERT INTO cbt_mapel (nama_mapel, kode_mapel) VALUES (?,?)")->execute([$nama, $kode]);
        }
        header('Location: index.php?mod=kelola_mapel&ok=1');
        exit;
    }

    private static function delete_mapel($pdo)
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id)
            $pdo->prepare("DELETE FROM cbt_mapel WHERE id_mapel=?")->execute([$id]);
        header('Location: index.php?mod=kelola_mapel&del=1');
        exit;
    }

    private static function save_siswa($pdo)
    {
        $id = (int) ($_POST['id_siswa'] ?? 0);
        $nisn = trim($_POST['nisn'] ?? '');
        $nipd = trim($_POST['nipd'] ?? '');
        $nama = trim($_POST['nama_siswa'] ?? '');
        $id_kelas = (int) ($_POST['id_kelas'] ?? 0);
        $jurusan = trim($_POST['jurusan'] ?? '');
        $t_lahir = trim($_POST['tempat_lahir'] ?? '');
        $d_lahir = trim($_POST['tanggal_lahir'] ?? null);
        $no_peserta = trim($_POST['no_peserta'] ?? '');
        $ruang = trim($_POST['ruang'] ?? '');
        $sesi = trim($_POST['sesi'] ?? '');

        if (!$nisn || !$nama) {
            header('Location: index.php?mod=kelola_siswa&err=empty');
            exit;
        }

        // Handle Foto Upload
        $foto_name = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_name = $nisn . '_' . time() . '.' . $ext;
            $target = CBT_ROOT . '/public/uploads/siswa/' . $foto_name;
            if (!is_dir(CBT_ROOT . '/public/uploads/siswa/')) {
                mkdir(CBT_ROOT . '/public/uploads/siswa/', 0777, true);
            }
            move_uploaded_file($_FILES['foto']['tmp_name'], $target);
        }

        if ($id) {
            $sql = "UPDATE cbt_siswa SET nisn=?, nipd=?, nama_siswa=?, id_kelas=?, jurusan=?, tempat_lahir=?, tanggal_lahir=?, no_peserta=?, ruang=?, sesi=?";
            $params = [$nisn, $nipd, $nama, $id_kelas, $jurusan, $t_lahir, $d_lahir ?: null, $no_peserta, $ruang, $sesi];

            if ($foto_name) {
                $sql .= ", foto=?";
                $params[] = $foto_name;
            }

            $sql .= " WHERE id_siswa=?";
            $params[] = $id;

            $pdo->prepare($sql)->execute($params);
        } else {
            $pass = password_hash($nisn, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO cbt_siswa (nisn, nipd, nama_siswa, id_kelas, jurusan, tempat_lahir, tanggal_lahir, no_peserta, ruang, sesi, password, foto) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$nisn, $nipd, $nama, $id_kelas, $jurusan, $t_lahir, $d_lahir ?: null, $no_peserta, $ruang, $sesi, $pass, $foto_name]);
        }
        header('Location: index.php?mod=kelola_siswa&ok=1');
        exit;
    }

    private static function delete_siswa($pdo)
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id)
            $pdo->prepare("DELETE FROM cbt_siswa WHERE id_siswa=?")->execute([$id]);
        header('Location: index.php?mod=kelola_siswa&del=1');
        exit;
    }

    private static function reset_password($pdo)
    {
        $id = (int) ($_GET['id'] ?? 0);
        $row = $pdo->prepare("SELECT nisn FROM cbt_siswa WHERE id_siswa=?");
        $row->execute([$id]);
        $siswa = $row->fetch();
        if ($siswa) {
            $pdo->prepare("UPDATE cbt_siswa SET password=? WHERE id_siswa=?")
                ->execute([password_hash($siswa['nisn'], PASSWORD_DEFAULT), $id]);
        }
        header('Location: index.php?mod=kelola_siswa&reset=1');
        exit;
    }

    private static function import_kelas_simaks($pdo)
    {
        $id_raw = $_REQUEST['id_kelas'] ?? 0;
        if (is_array($id_raw)) {
            $id_kelas = array_map('intval', $id_raw);
        } else {
            $id_kelas = ($id_raw === 'all' || $id_raw === '') ? null : (int) $id_raw;
        }

        $kelas_simaks = cbt_get_kelas($pdo, $id_kelas);
        $count = 0;
        foreach ($kelas_simaks as $k) {
            $pdo->prepare("INSERT INTO cbt_kelas (id_kelas, nama_kelas, tingkat) VALUES (?,?,?) 
                          ON DUPLICATE KEY UPDATE nama_kelas=VALUES(nama_kelas), tingkat=VALUES(tingkat)")
                ->execute([$k['id_kelas'], $k['nama_kelas'], $k['tingkat'] ?? '']);
            $count++;
        }
        header("Location: index.php?mod=kelola_kelas&imported=$count");
        exit;
    }

    private static function import_mapel_simaks($pdo)
    {
        $mapel_simaks = cbt_get_mapel($pdo);
        $count = 0;
        foreach ($mapel_simaks as $m) {
            $pdo->prepare("INSERT INTO cbt_mapel (id_mapel, nama_mapel, kode_mapel) VALUES (?,?,?) 
                          ON DUPLICATE KEY UPDATE nama_mapel=VALUES(nama_mapel), kode_mapel=VALUES(kode_mapel)")
                ->execute([$m['id_mapel'], $m['nama_mapel'], $m['kode_mapel'] ?? '']);
            $count++;
        }
        header("Location: index.php?mod=kelola_mapel&imported=$count");
        exit;
    }


    private static function import_siswa_simaks($pdo)
    {
        $id_kelas_raw = $_REQUEST['id_kelas'] ?? 0;

        // Handle both single value and array
        if (is_array($id_kelas_raw)) {
            $id_kelas = array_map('intval', $id_kelas_raw);
        } else {
            $id_kelas = ($id_kelas_raw === 'all' || $id_kelas_raw === '') ? null : (int) $id_kelas_raw;
        }

        $siswa = cbt_get_siswa_from_simaks($id_kelas);
        $count = 0;
        foreach ($siswa as $s) {
            $pass = password_hash($s['nisn'], PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO cbt_siswa (id_siswa, nisn, nipd, nama_siswa, id_kelas, jurusan, tempat_lahir, tanggal_lahir, password) VALUES (?,?,?,?,?,?,?,?,?) 
                          ON DUPLICATE KEY UPDATE nisn=VALUES(nisn), nipd=VALUES(nipd), nama_siswa=VALUES(nama_siswa), 
                          id_kelas=VALUES(id_kelas), jurusan=VALUES(jurusan), tempat_lahir=VALUES(tempat_lahir), tanggal_lahir=VALUES(tanggal_lahir)")
                ->execute([$s['id_siswa'], $s['nisn'], $s['nipd'], $s['nama_siswa'], $s['id_kelas'], $s['jurusan'], $s['tempat_lahir'], $s['tanggal_lahir'], $pass]);
            $count++;
        }
        header("Location: index.php?mod=kelola_siswa&ok=1&imported=$count");
        exit;
    }

    private static function import_siswa_excel($pdo)
    {
        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            header('Location: index.php?mod=kelola_siswa&err=upload');
            exit;
        }

        $filePath = $_FILES['file_excel']['tmp_name'];

        // Load autoloader from root SIMAKS
        require_once dirname(CBT_ROOT) . '/vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $count = 0;
            // Skip header row
            for ($i = 1; $i < count($data); $i++) {
                $nisn = trim($data[$i][0] ?? '');
                $nipd = trim($data[$i][1] ?? '');
                $nama = trim($data[$i][2] ?? '');
                $nama_kelas = trim($data[$i][3] ?? '');
                $jurusan = trim($data[$i][4] ?? '');
                $tempat = trim($data[$i][5] ?? '');
                $tanggal = trim($data[$i][6] ?? '');

                if (!$nisn || !$nama)
                    continue;

                // Cari id_kelas berdasarkan nama_kelas
                $stmt_kelas = $pdo->prepare("SELECT id_kelas FROM cbt_kelas WHERE nama_kelas = ? LIMIT 1");
                $stmt_kelas->execute([$nama_kelas]);
                $id_kelas = $stmt_kelas->fetchColumn() ?: null;
                // Cari id_kelas berdasarkan nama_kelas dari map
                $id_kelas = $kelas_map[$nama_kelas] ?? null;
                if (!$id_kelas)
                    continue; // Skip if class not found

                $jurusan = trim($row[4] ?? '');
                $tempat = trim($row[5] ?? '');
                $tanggal = trim($row[6] ?? '');
                $foto = trim($row[7] ?? '');
                $no_peserta = trim($row[8] ?? '');
                $username = trim($row[9] ?? '');
                $plain_pass = trim($row[10] ?? '');
                $ruang = trim($row[11] ?? '');
                $sesi = trim($row[12] ?? '');

                // Use NISN as default password if Excel field is empty
                $pass = password_hash($plain_pass ?: $nisn, PASSWORD_DEFAULT);

                $stmt_insert = $pdo->prepare("INSERT INTO cbt_siswa (nisn, nipd, nama_siswa, id_kelas, jurusan, tempat_lahir, tanggal_lahir, password, foto, no_peserta, username, ruang, sesi) 
                                              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?) 
                                              ON DUPLICATE KEY UPDATE nipd=VALUES(nipd), nama_siswa=VALUES(nama_siswa), 
                                              id_kelas=VALUES(id_kelas), jurusan=VALUES(jurusan), tempat_lahir=VALUES(tempat_lahir), tanggal_lahir=VALUES(tanggal_lahir), 
                                              foto=VALUES(foto), no_peserta=VALUES(no_peserta), username=VALUES(username), password=VALUES(password), ruang=VALUES(ruang), sesi=VALUES(sesi)");
                $stmt_insert->execute([$nisn, $nipd, $nama, $id_kelas, $jurusan, $tempat, $tanggal ?: null, $pass, $foto, $no_peserta, $username, $ruang, $sesi]);
                $count++;
            }

            header("Location: index.php?mod=kelola_siswa&ok=1&imported=$count");
            exit;
        } catch (\Exception $e) {
            header('Location: index.php?mod=kelola_siswa&err=excel');
            exit;
        }
    }

    private static function download_template_siswa($pdo)
    {
        require_once dirname(CBT_ROOT) . '/vendor/autoload.php';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['NISN', 'NIPD', 'Nama Siswa', 'Nama Kelas', 'Jurusan', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)', 'Foto (Nama File)', 'No Peserta', 'Username', 'Password', 'Ruang', 'Sesi'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Sample Row
        $sheet->setCellValue('A2', '1234567890');
        $sheet->setCellValue('B2', '00987654');
        $sheet->setCellValue('C2', 'Contoh Nama Siswa');
        $sheet->setCellValue('D2', 'X RPL 1');
        $sheet->setCellValue('E2', 'RPL');
        $sheet->setCellValue('F2', 'Jakarta');
        $sheet->setCellValue('G2', '2008-01-01');
        $sheet->setCellValue('H2', '1234567890.jpg');
        $sheet->setCellValue('I2', 'P-001');
        $sheet->setCellValue('J2', 'siswa01');
        $sheet->setCellValue('K2', 'pass123');
        $sheet->setCellValue('L2', 'LAB-1');
        $sheet->setCellValue('M2', 'SESI-1');

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_siswa.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private static function bulk_upload_foto($pdo)
    {
        if (!isset($_FILES['file_zip']) || $_FILES['file_zip']['error'] !== UPLOAD_ERR_OK) {
            header('Location: index.php?mod=kelola_siswa&err=upload');
            exit;
        }

        $upload_dir = CBT_ROOT . '/public/uploads/siswa/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $zip = new \ZipArchive();
        if ($zip->open($_FILES['file_zip']['tmp_name']) === TRUE) {
            $count = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $new_filename = basename($filename);
                    copy("zip://" . $_FILES['file_zip']['tmp_name'] . "#" . $filename, $upload_dir . $new_filename);
                    $count++;
                }
            }
            $zip->close();
            header("Location: index.php?mod=kelola_siswa&ok=1&bulk_foto=$count");
        } else {
            header('Location: index.php?mod=kelola_siswa&err=zip');
        }
        exit;
    }

    private static function import_kelas_excel($pdo)
    {
        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            header('Location: index.php?mod=kelola_kelas&err=upload');
            exit;
        }

        $filePath = $_FILES['file_excel']['tmp_name'];
        require_once dirname(CBT_ROOT) . '/vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $count = 0;
            for ($i = 1; $i < count($data); $i++) {
                $nama = trim($data[$i][0] ?? '');
                $tingkat = trim($data[$i][1] ?? '');
                $jurusan = trim($data[$i][2] ?? '');

                if (!$nama)
                    continue;

                $stmt = $pdo->prepare("INSERT INTO cbt_kelas (nama_kelas, tingkat, jurusan) VALUES (?,?,?) 
                                      ON DUPLICATE KEY UPDATE tingkat=VALUES(tingkat), jurusan=VALUES(jurusan)");
                $stmt->execute([$nama, $tingkat, $jurusan]);
                $count++;
            }

            header("Location: index.php?mod=kelola_kelas&ok=1&imported=$count");
            exit;
        } catch (Exception $e) {
            header('Location: index.php?mod=kelola_kelas&err=excel_parse');
            exit;
        }
    }

    private static function import_mapel_excel($pdo)
    {
        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            header('Location: index.php?mod=kelola_mapel&err=upload');
            exit;
        }

        $filePath = $_FILES['file_excel']['tmp_name'];
        require_once dirname(CBT_ROOT) . '/vendor/autoload.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $count = 0;
            for ($i = 1; $i < count($data); $i++) {
                $nama = trim($data[$i][0] ?? '');
                $kode = trim($data[$i][1] ?? '');

                if (!$nama)
                    continue;

                $stmt = $pdo->prepare("INSERT INTO cbt_mapel (nama_mapel, kode_mapel) VALUES (?,?) 
                                      ON DUPLICATE KEY UPDATE kode_mapel=VALUES(kode_mapel)");
                $stmt->execute([$nama, $kode]);
                $count++;
            }

            header("Location: index.php?mod=kelola_mapel&ok=1&imported=$count");
            exit;
        } catch (Exception $e) {
            header('Location: index.php?mod=kelola_mapel&err=excel_parse');
            exit;
        }
    }
    private static function delete_all_siswa($pdo)
    {
        $pdo->query("DELETE FROM cbt_siswa");
        header('Location: index.php?mod=kelola_siswa&ok=1');
        exit;
    }

    private static function delete_selected_siswa($pdo)
    {
        $ids = $_POST['ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM cbt_siswa WHERE id_siswa IN ($placeholders)");
            $stmt->execute($ids);
        }
        header('Location: index.php?mod=kelola_siswa&ok=1');
        exit;
    }
}

