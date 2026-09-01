<?php
// Placeholder - will be implemented in Phase 3
class BankSoalController
{
    public static function index($pdo, $act)
    {
        if ($act == 'save_bank') {
            self::save_bank($pdo);
        } elseif ($act == 'delete_bank') {
            self::delete_bank($pdo);
        }

        $page_title = 'Bank Soal';
        $mapel_list = cbt_get_mapel($pdo);

        // Fetch Bank Soal data
        $stmt = $pdo->query("SELECT b.*, m.nama_mapel, 
                             (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = b.id_bank) as jumlah_soal
                             FROM cbt_bank_soal b
                             JOIN cbt_mapel m ON b.id_mapel = m.id_mapel
                             ORDER BY b.created_at DESC");
        $banks = $stmt->fetchAll();

        require_once CBT_ROOT . '/app/views/partials/header.php';
        require_once CBT_ROOT . '/app/views/admin/bank_soal.php';
        require_once CBT_ROOT . '/app/views/partials/footer.php';
    }

    private static function save_bank($pdo)
    {
        $id = (int) ($_POST['id_bank'] ?? 0);
        $nama = trim($_POST['nama_bank'] ?? '');
        $kode = trim($_POST['kode_bank'] ?? '');
        $id_mapel = (int) ($_POST['id_mapel'] ?? 0);
        $tingkat = trim($_POST['tingkat'] ?? '');
        $id_jurusan = trim($_POST['id_jurusan'] ?? '');
        $opsi_pg = (int) ($_POST['opsi_pg'] ?? 5);
        $jml_pg = (int) ($_POST['jml_pg'] ?? 0);
        $bobot_pg = (float) ($_POST['bobot_pg'] ?? 1.0);
        $jml_esai = (int) ($_POST['jml_esai'] ?? 0);
        $bobot_esai = (float) ($_POST['bobot_esai'] ?? 1.0);
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $id_user = $_SESSION['cbt_user_id'] ?? 1;

        if (!$nama || !$id_mapel) {
            header('Location: index.php?mod=bank_soal&err=empty');
            exit;
        }

        if ($id) {
            $pdo->prepare("UPDATE cbt_bank_soal SET nama_bank=?, kode_bank=?, id_mapel=?, tingkat=?, id_jurusan=?, opsi_pg=?, jml_pg=?, bobot_pg=?, jml_esai=?, bobot_esai=?, deskripsi=? WHERE id_bank=?")
                ->execute([$nama, $kode, $id_mapel, $tingkat, $id_jurusan, $opsi_pg, $jml_pg, $bobot_pg, $jml_esai, $bobot_esai, $deskripsi, $id]);
        } else {
            $pdo->prepare("INSERT INTO cbt_bank_soal (nama_bank, kode_bank, id_mapel, tingkat, id_jurusan, opsi_pg, jml_pg, bobot_pg, jml_esai, bobot_esai, id_user, deskripsi) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
                ->execute([$nama, $kode, $id_mapel, $tingkat, $id_jurusan, $opsi_pg, $jml_pg, $bobot_pg, $jml_esai, $bobot_esai, $id_user, $deskripsi]);
        }
        header('Location: index.php?mod=bank_soal&ok=1');
        exit;
    }

    private static function delete_bank($pdo)
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            // Should potentially delete soal inside too, but database might have foreign keys or we do it manually
            $pdo->prepare("DELETE FROM cbt_bank_soal WHERE id_bank=?")->execute([$id]);
        }
        header('Location: index.php?mod=bank_soal&del=1');
        exit;
    }

    public static function input_soal($pdo, $act)
    {
        $id_bank = (int) ($_GET['id_bank'] ?? 0);
        if (!$id_bank) {
            header('Location: index.php?mod=bank_soal');
            exit;
        }

        if ($act == 'save_soal') {
            self::save_soal($pdo);
        } elseif ($act == 'delete_soal') {
            self::delete_soal($pdo);
        } elseif ($act == 'import') {
            self::import_soal($pdo);
        } elseif ($act == 'download_template') {
            self::download_template_soal($pdo);
        } elseif ($act == 'upload_media') {
            self::upload_zip_media($pdo);
        }

        // Fetch Bank Soal info
        $stmt = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b 
                               JOIN cbt_mapel m ON b.id_mapel = m.id_mapel 
                               WHERE b.id_bank = ?");
        $stmt->execute([$id_bank]);
        $bank = $stmt->fetch();

        if (!$bank) {
            header('Location: index.php?mod=bank_soal');
            exit;
        }

        // Fetch Questions
        $stmt = $pdo->prepare("SELECT * FROM cbt_soal WHERE id_bank = ? ORDER BY nomor_urut ASC");
        $stmt->execute([$id_bank]);
        $soal_list = $stmt->fetchAll();

        // Fetch Options for PG/TF
        // Fetch Options and Media for each question
        foreach ($soal_list as &$s) {
            $stmt = $pdo->prepare("SELECT * FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC");
            $stmt->execute([$s['id_soal']]);
            $s['opsi'] = $stmt->fetchAll();

            $stmt = $pdo->prepare("SELECT * FROM cbt_soal_media WHERE id_soal = ?");
            $stmt->execute([$s['id_soal']]);
            $s['media'] = $stmt->fetchAll();
        }

        $page_title = 'Input Soal: ' . htmlspecialchars($bank['nama_bank']);
        require_once CBT_ROOT . '/app/views/partials/header.php';
        require_once CBT_ROOT . '/app/views/admin/input_soal.php';
        require_once CBT_ROOT . '/app/views/partials/footer.php';
    }

    private static function import_soal($pdo)
    {
        $id_bank = (int) ($_POST['id_bank'] ?? 0);
        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            header("Location: index.php?mod=input_soal&id_bank=$id_bank&err=upload");
            exit;
        }

        require_once dirname(CBT_ROOT) . '/vendor/autoload.php';
        $filePath = $_FILES['file_excel']['tmp_name'];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $count = 0;
            // Get next nomor_urut
            $stmt = $pdo->prepare("SELECT MAX(nomor_urut) FROM cbt_soal WHERE id_bank = ?");
            $stmt->execute([$id_bank]);
            $next_no = (int) $stmt->fetchColumn() + 1;

            // === 20-COLUMN PROFESSIONAL FORMAT ===
            // A=NO, B=JENIS(1-4), C=KATEGORI, D=ACAK(A/T), E=SOAL
            // F=JAWAB1, G=FILEJAWAB1, H=JAWAB2, I=FILEJAWAB2,
            // J=JAWAB3, K=FILEJAWAB3, L=JAWAB4, M=FILEJAWAB4,
            // N=JAWAB5, O=FILEJAWAB5, P=AUDIO, Q=VIDEO, R=GAMBAR,
            // S=KUNCI, T=ACAK OPSI(A/T)
            for ($i = 1; $i < count($data); $i++) {
                $jenis_raw = trim($data[$i][1] ?? ''); // Column B
                $kesulitan = strtolower(trim($data[$i][2] ?? 'sedang')); // Column C
                $acak_raw = strtoupper(trim($data[$i][3] ?? 'A'));       // Column D
                $pertanyaan = trim($data[$i][4] ?? '');                   // Column E (SOAL)

                // Map Jenis to enum
                $tipe_map = ['1' => 'pg', '2' => 'essay', '3' => 'tf', '4' => 'matching'];
                $tipe = $tipe_map[$jenis_raw] ?? 'pg';

                // Randomization
                $is_acak_soal = ($acak_raw === 'A') ? 1 : 0;
                $acak_opsi_raw = strtoupper(trim($data[$i][19] ?? 'A')); // Column T
                $is_acak_opsi = ($acak_opsi_raw === 'A') ? 1 : 0;

                // Paired answers: (F,G)=jawab1/file1, (H,I)=jawab2/file2, (J,K)=3, (L,M)=4, (N,O)=5
                $answers = [
                    ['text' => trim($data[$i][5] ?? ''), 'file' => trim($data[$i][6] ?? '')],
                    ['text' => trim($data[$i][7] ?? ''), 'file' => trim($data[$i][8] ?? '')],
                    ['text' => trim($data[$i][9] ?? ''), 'file' => trim($data[$i][10] ?? '')],
                    ['text' => trim($data[$i][11] ?? ''), 'file' => trim($data[$i][12] ?? '')],
                    ['text' => trim($data[$i][13] ?? ''), 'file' => trim($data[$i][14] ?? '')],
                ];
                $file_aud = trim($data[$i][15] ?? ''); // Column P
                $file_vid = trim($data[$i][16] ?? ''); // Column Q
                $file_img = trim($data[$i][17] ?? ''); // Column R (gambar soal)
                $kunci_raw = trim($data[$i][18] ?? ''); // Column S

                // Row is valid if it has a question text OR a soal image
                if (!$pertanyaan && !$file_img)
                    continue;

                // For image-only questions, use placeholder text
                if (!$pertanyaan && $file_img) {
                    $pertanyaan = 'Lihat gambar';
                }

                // Insert Soal
                $stmt = $pdo->prepare("INSERT INTO cbt_soal (id_bank, nomor_urut, tipe_soal, pertanyaan, is_acak_soal, is_acak_opsi, tingkat_kesulitan) VALUES (?,?,?,?,?,?,?)");
                $stmt->execute([$id_bank, $next_no++, $tipe, $pertanyaan, $is_acak_soal, $is_acak_opsi, $kesulitan]);
                $id_soal = $pdo->lastInsertId();

                // Handle Question Media
                $media_path = 'uploads/soal/bank_' . $id_bank . '/';
                if ($file_img) {
                    $pdo->prepare("INSERT INTO cbt_soal_media (id_soal, tipe_media, nama_file, path_file) VALUES (?, 'gambar', ?, ?)")
                        ->execute([$id_soal, $file_img, $media_path . $file_img]);
                }
                if ($file_aud) {
                    $pdo->prepare("INSERT INTO cbt_soal_media (id_soal, tipe_media, nama_file, path_file) VALUES (?, 'audio', ?, ?)")
                        ->execute([$id_soal, $file_aud, $media_path . $file_aud]);
                }
                if ($file_vid) {
                    $pdo->prepare("INSERT INTO cbt_soal_media (id_soal, tipe_media, nama_file, path_file) VALUES (?, 'video', ?, ?)")
                        ->execute([$id_soal, $file_vid, $media_path . $file_vid]);
                }

                if ($tipe == 'pg') {
                    // kunci_raw: 1=A, 2=B, 3=C, 4=D, 5=E
                    $kunci_idx = (int) $kunci_raw - 1;
                    $opsi_labels = ['A', 'B', 'C', 'D', 'E'];
                    foreach ($opsi_labels as $idx => $label) {
                        $isi = $answers[$idx]['text'];
                        $gambar = $answers[$idx]['file'];
                        $is_benar = ($idx === $kunci_idx) ? 1 : 0;
                        $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, gambar, is_benar) VALUES (?,?,?,?,?)")
                            ->execute([$id_soal, $label, $isi, $gambar ?: null, $is_benar]);
                    }
                } elseif ($tipe == 'tf') {
                    // kunci_raw: 1=Benar, 2=Salah
                    foreach ([['B', 'Benar'], ['S', 'Salah']] as $idx => [$label, $teks]) {
                        $is_benar = ($kunci_raw == ($idx + 1)) ? 1 : 0;
                        $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?,?,?,?)")
                            ->execute([$id_soal, $label, $teks, $is_benar]);
                    }
                } elseif ($tipe == 'matching') {
                    // Pasangan: "Premis | Respon" di JAWAB1-5
                    for ($idx = 0; $idx < 5; $idx++) {
                        $raw = $answers[$idx]['text'];
                        $gambar = $answers[$idx]['file'];
                        if ($raw && strpos($raw, '|') !== false) {
                            list($p, $r) = explode('|', $raw, 2);
                            $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, gambar, is_benar) VALUES (?,?,?,?,1)")
                                ->execute([$id_soal, trim($p), trim($r), $gambar ?: null]);
                        }
                    }
                }
                $count++;
            }

            header("Location: index.php?mod=input_soal&id_bank=$id_bank&ok=1&imported=$count");
        } catch (\Exception $e) {
            header("Location: index.php?mod=input_soal&id_bank=$id_bank&err=excel_parse");
        }
        exit;
    }

    private static function upload_zip_media($pdo)
    {
        $id_bank = (int) ($_POST['id_bank'] ?? 0);
        if (!isset($_FILES['file_zip']) || $_FILES['file_zip']['error'] !== UPLOAD_ERR_OK) {
            header("Location: index.php?mod=input_soal&id_bank=$id_bank&err=upload");
            exit;
        }

        $upload_dir = CBT_ROOT . '/uploads/soal/bank_' . $id_bank . '/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $zip = new \ZipArchive();
        if ($zip->open($_FILES['file_zip']['tmp_name']) === TRUE) {
            $count = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                // Skip directories and macOS metadata
                if (substr($filename, -1) === '/' || strpos(basename($filename), '.') === 0)
                    continue;
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'mp3', 'wav', 'mp4', 'webm'])) {
                    // Extract flat (by basename only) to avoid subdirectory nesting
                    $dest = $upload_dir . basename($filename);
                    $fp = $zip->getStream($filename);
                    if ($fp) {
                        file_put_contents($dest, stream_get_contents($fp));
                        fclose($fp);
                        $count++;
                    }
                }
            }
            $zip->close();
            header("Location: index.php?mod=input_soal&id_bank=$id_bank&ok=1&media_uploaded=$count");
        } else {
            header("Location: index.php?mod=input_soal&id_bank=$id_bank&err=zip");
        }
        exit;
    }

    private static function download_template_soal($pdo)
    {
        require_once dirname(CBT_ROOT) . '/vendor/autoload.php';

        // Clear any previous output to prevent corruption
        if (ob_get_length())
            ob_end_clean();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'NO',
            'JENIS SOAL (1-PG, 2-Essay, 3-TF, 4-Matching)',
            'KATEGORI (mudah/sedang/sulit)',
            'ACAK (A/T)',
            'SOAL',
            'JAWAB1',
            'FILEJAWAB1',
            'JAWAB2',
            'FILEJAWAB2',
            'JAWAB3',
            'FILEJAWAB3',
            'JAWAB4',
            'FILEJAWAB4',
            'JAWAB5',
            'FILEJAWAB5',
            'AUDIO',
            'VIDEO',
            'GAMBAR',
            'KUNCI JAWABAN',
            'ACAK OPSI (A/T)'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // Example row 1: PG
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', '1'); // PG
        $sheet->setCellValue('C2', 'mudah');
        $sheet->setCellValue('D2', 'A'); // Acak Soal
        $sheet->setCellValue('E2', 'Ibukota Indonesia adalah...');
        $sheet->setCellValue('F2', 'Jakarta');
        $sheet->setCellValue('G2', 'jkt.jpg');
        $sheet->setCellValue('H2', 'Bandung');
        $sheet->setCellValue('I2', '');
        $sheet->setCellValue('J2', 'Surabaya');
        $sheet->setCellValue('K2', '');
        $sheet->setCellValue('L2', 'Medan');
        $sheet->setCellValue('M2', '');
        $sheet->setCellValue('N2', 'Makassar');
        $sheet->setCellValue('O2', '');
        $sheet->setCellValue('P2', ''); // Audio
        $sheet->setCellValue('Q2', ''); // Video
        $sheet->setCellValue('R2', 'soal_peta.jpg'); // Gambar Soal
        $sheet->setCellValue('S2', '1'); // Kunci A
        $sheet->setCellValue('T2', 'A'); // Acak Opsi

        // Example row 2: Matching
        $sheet->setCellValue('A3', '2');
        $sheet->setCellValue('B3', '4'); // Matching
        $sheet->setCellValue('C3', 'sedang');
        $sheet->setCellValue('D3', 'T');
        $sheet->setCellValue('E3', 'Pasangkan Bendera dengan Negaranya');
        $sheet->setCellValue('F3', 'Indonesia | Merah Putih');
        $sheet->setCellValue('G3', 'flag_id.png');
        $sheet->setCellValue('H3', 'Jepang | Matahari Terbit');
        $sheet->setCellValue('I3', 'flag_jp.png');
        $sheet->setCellValue('J3', 'Prancis | Tiga Warna');
        $sheet->setCellValue('K3', 'flag_fr.png');
        $sheet->setCellValue('L3', '');
        $sheet->setCellValue('M3', '');
        $sheet->setCellValue('N3', '');
        $sheet->setCellValue('O3', '');
        $sheet->setCellValue('P3', '');
        $sheet->setCellValue('Q3', '');
        $sheet->setCellValue('R3', '');
        $sheet->setCellValue('S3', '');
        $sheet->setCellValue('T3', 'T');

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_soal.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private static function save_soal($pdo)
    {
        $id_bank = (int) ($_POST['id_bank'] ?? 0);
        $id_soal = (int) ($_POST['id_soal'] ?? 0);
        $tipe = $_POST['tipe_soal'] ?? 'pg';
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $bobot = (int) ($_POST['bobot'] ?? 1);
        $kesulitan = $_POST['tingkat_kesulitan'] ?? 'sedang';
        $is_acak_soal = (int) ($_POST['is_acak_soal'] ?? 1);
        $is_acak_opsi = (int) ($_POST['is_acak_opsi'] ?? 1);

        if (!$id_bank || !$pertanyaan) {
            header("Location: index.php?mod=input_soal&id_bank=$id_bank&err=empty");
            exit;
        }

        if ($id_soal) {
            $pdo->prepare("UPDATE cbt_soal SET tipe_soal=?, pertanyaan=?, bobot=?, tingkat_kesulitan=?, is_acak_soal=?, is_acak_opsi=? WHERE id_soal=? AND id_bank=?")
                ->execute([$tipe, $pertanyaan, $bobot, $kesulitan, $is_acak_soal, $is_acak_opsi, $id_soal, $id_bank]);
        } else {
            // Get next nomor_urut
            $stmt = $pdo->prepare("SELECT MAX(nomor_urut) FROM cbt_soal WHERE id_bank = ?");
            $stmt->execute([$id_bank]);
            $next_no = (int) $stmt->fetchColumn() + 1;

            $pdo->prepare("INSERT INTO cbt_soal (id_bank, nomor_urut, tipe_soal, pertanyaan, bobot, tingkat_kesulitan, is_acak_soal, is_acak_opsi) VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$id_bank, $next_no, $tipe, $pertanyaan, $bobot, $kesulitan, $is_acak_soal, $is_acak_opsi]);
            $id_soal = $pdo->lastInsertId();
        }

        // Clear existing options for this question to ensure clean state
        $pdo->prepare("DELETE FROM cbt_soal_opsi WHERE id_soal = ?")->execute([$id_soal]);

        // Handle Options based on type
        if ($tipe == 'pg') {
            $opsi_labels = ['A', 'B', 'C', 'D', 'E'];
            $kunci = $_POST['kunci_jawaban'] ?? '';
            foreach ($opsi_labels as $label) {
                $isi = trim($_POST['opsi_' . $label] ?? '');
                if ($isi === '' && $label !== 'A')
                    continue; // Allow empty options except A, but better to save all if needed

                $is_benar = ($kunci === $label) ? 1 : 0;
                $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?,?,?,?)")
                    ->execute([$id_soal, $label, $isi, $is_benar]);
            }
        } elseif ($tipe == 'tf') {
            $kunci = $_POST['kunci_tf'] ?? ''; // 'B' or 'S'
            $tf_labels = ['B', 'S'];
            foreach ($tf_labels as $label) {
                $isi = ($label == 'B') ? 'Benar' : 'Salah';
                $is_benar = ($kunci === $label) ? 1 : 0;
                $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?,?,?,?)")
                    ->execute([$id_soal, $label, $isi, $is_benar]);
            }
        } elseif ($tipe == 'matching') {
            $premis = $_POST['match_p'] ?? [];
            $respon = $_POST['match_r'] ?? [];
            foreach ($premis as $idx => $p) {
                $r = $respon[$idx] ?? '';
                if (trim($p) !== '' || trim($r) !== '') {
                    $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?,?,?,1)")
                        ->execute([$id_soal, trim($p), trim($r)]);
                }
            }
        }

        header("Location: index.php?mod=input_soal&id_bank=$id_bank&ok=1");
        exit;
    }

    private static function delete_soal($pdo)
    {
        $id_bank = (int) ($_GET['id_bank'] ?? 0);
        $id_soal = (int) ($_GET['id'] ?? 0);
        if ($id_soal) {
            $pdo->prepare("DELETE FROM cbt_soal WHERE id_soal=? AND id_bank=?")->execute([$id_soal, $id_bank]);
            // Options are deleted via CASCADE or manually
            $pdo->prepare("DELETE FROM cbt_soal_opsi WHERE id_soal=?")->execute([$id_soal]);
        }
        header("Location: index.php?mod=input_soal&id_bank=$id_bank&del=1");
        exit;
    }
}
