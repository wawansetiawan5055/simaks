<?php
// app/controllers/CbtController.php
// CBT terintegrasi ke SIMAKS - Menyesuaikan skema tabel asli cbt_* dengan isolasi hak akses Guru & Mapel

require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . '/../models/LmsModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class CbtController
{
    /**
     * Helper untuk mengambil data hak akses Guru dan penugasan Mapel/Kelas
     */
    private static function getAccessInfo($pdo)
    {
        $roles = user_roles();
        $user_id = (int)($_SESSION['user_id'] ?? 0);
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        $is_admin = (in_array('Admin', $roles) || in_array('Kurikulum', $roles) || in_array('Kepala Sekolah', $roles)) && empty($id_guru);

        $mapel_ids = [];
        $kelas_ids = [];

        if ($id_guru > 0) {
            $stmt_m = $pdo->prepare("SELECT DISTINCT id_mapel FROM guru_mapel WHERE id_guru = ?
                                    UNION
                                    SELECT DISTINCT gm.id_mapel FROM jadwal_mengajar jm JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel WHERE gm.id_guru = ?");
            $stmt_m->execute([$id_guru, $id_guru]);
            $mapel_ids = array_map('intval', $stmt_m->fetchAll(PDO::FETCH_COLUMN) ?: []);

            $stmt_k = $pdo->prepare("SELECT DISTINCT id_kelas FROM guru_mapel WHERE id_guru = ? AND id_kelas IS NOT NULL
                                    UNION
                                    SELECT DISTINCT jm.id_kelas FROM jadwal_mengajar jm JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel WHERE gm.id_guru = ?");
            $stmt_k->execute([$id_guru, $id_guru]);
            $kelas_ids = array_map('intval', $stmt_k->fetchAll(PDO::FETCH_COLUMN) ?: []);
        }

        return [
            'is_admin'  => $is_admin,
            'user_id'   => $user_id,
            'id_guru'   => $id_guru,
            'mapel_ids' => $mapel_ids,
            'kelas_ids' => $kelas_ids
        ];
    }

    /**
     * Helper SQL clause untuk membatasi query berdasarkan id_mapel / id_user
     */
    private static function buildMapelFilter($info, $prefix = 'b')
    {
        if ($info['is_admin']) {
            return ["clause" => "1=1", "params" => []];
        }

        $user_id = $info['user_id'];
        $mapel_ids = $info['mapel_ids'];

        if (!empty($mapel_ids)) {
            $placeholders = implode(',', array_fill(0, count($mapel_ids), '?'));
            return [
                "clause" => "({$prefix}.id_user = ? OR {$prefix}.id_mapel IN ($placeholders))",
                "params" => array_merge([$user_id], $mapel_ids)
            ];
        }

        return [
            "clause" => "{$prefix}.id_user = ?",
            "params" => [$user_id]
        ];
    }

    public static function formatVideoEmbedUrl($url)
    {
        $url = trim($url);
        if (empty($url)) return '';
        if (strpos($url, 'youtube.com/embed/') !== false) return $url;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        return $url;
    }

    // ====================================================
    // DASHBOARD
    // ====================================================
    public static function dashboard($pdo)
    {
        $info = self::getAccessInfo($pdo);
        $title = "Dashboard CBT";

        if ($info['is_admin']) {
            $stats = [
                'total_bank'    => (int)($pdo->query("SELECT COUNT(*) FROM cbt_bank_soal")->fetchColumn() ?: 0),
                'total_soal'    => (int)($pdo->query("SELECT COUNT(*) FROM cbt_soal")->fetchColumn() ?: 0),
                'total_pg'      => (int)($pdo->query("SELECT COUNT(*) FROM cbt_soal WHERE tipe_soal='pg'")->fetchColumn() ?: 0),
                'total_essay'   => (int)($pdo->query("SELECT COUNT(*) FROM cbt_soal WHERE tipe_soal='essay'")->fetchColumn() ?: 0),
                'total_hots'    => (int)($pdo->query("SELECT COUNT(*) FROM cbt_soal WHERE level_kognitif='L3'")->fetchColumn() ?: 0),
                'total_paket'   => (int)($pdo->query("SELECT COUNT(*) FROM cbt_paket")->fetchColumn() ?: 0),
                'ujian_aktif'   => (int)($pdo->query("SELECT COUNT(*) FROM cbt_jadwal WHERE status='aktif'")->fetchColumn() ?: 0),
                'total_peserta' => (int)($pdo->query("SELECT COUNT(*) FROM cbt_peserta")->fetchColumn() ?: 0),
                'total_selesai' => (int)($pdo->query("SELECT COUNT(*) FROM cbt_peserta WHERE status='selesai'")->fetchColumn() ?: 0),
            ];

            $jadwal_terbaru = $pdo->query("
                SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal) as jml_peserta,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal AND status = 'selesai') as jml_selesai
                FROM cbt_jadwal j
                LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                ORDER BY j.created_at DESC LIMIT 6
            ")->fetchAll(PDO::FETCH_ASSOC);

            // Matriks Kesiapan Naskah Seluruh Mata Pelajaran Sekolah
            $stmt_matrix = $pdo->query("
                SELECT m.id_mapel, m.nama_mapel,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel) as total_soal,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                        WHERE b.id_mapel = m.id_mapel AND (cp.fase = 'E' OR (s.id_cp IS NULL AND b.tingkat IN ('X', '10', 'Semua')))) as soal_x,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                        WHERE b.id_mapel = m.id_mapel AND (cp.fase = 'F' OR (s.id_cp IS NULL AND b.tingkat IN ('XI', '11', 'Semua')))) as soal_xi,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                        WHERE b.id_mapel = m.id_mapel AND (cp.fase = 'F' OR (s.id_cp IS NULL AND b.tingkat IN ('XII', '12', 'Semua')))) as soal_xii,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel AND s.level_kognitif = 'L3') as total_hots,
                       (SELECT COUNT(*) FROM cbt_paket p WHERE p.id_mapel = m.id_mapel) as total_paket,
                       (SELECT id_bank FROM cbt_bank_soal b WHERE b.id_mapel = m.id_mapel LIMIT 1) as id_bank
                FROM mapel m
                ORDER BY m.nama_mapel ASC
            ");
            $mapel_readiness_list = $stmt_matrix->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $filter_b = self::buildMapelFilter($info, 'b');
            $tot_bank = (int)($pdo->prepare("SELECT COUNT(*) FROM cbt_bank_soal b WHERE {$filter_b['clause']}")->execute($filter_b['params']) ? $pdo->query("SELECT 1")->fetchColumn() : 0);

            $stmt_b_cnt = $pdo->prepare("SELECT COUNT(*) FROM cbt_bank_soal b WHERE {$filter_b['clause']}");
            $stmt_b_cnt->execute($filter_b['params']);
            $tot_bank = (int)$stmt_b_cnt->fetchColumn();

            $stmt_soal = $pdo->prepare("SELECT COUNT(*) FROM cbt_soal s JOIN cbt_bank_soal b ON s.id_bank = b.id_bank WHERE {$filter_b['clause']}");
            $stmt_soal->execute($filter_b['params']);
            $tot_soal = (int)$stmt_soal->fetchColumn();

            $stmt_pg = $pdo->prepare("SELECT COUNT(*) FROM cbt_soal s JOIN cbt_bank_soal b ON s.id_bank = b.id_bank WHERE s.tipe_soal='pg' AND {$filter_b['clause']}");
            $stmt_pg->execute($filter_b['params']);
            $tot_pg = (int)$stmt_pg->fetchColumn();

            $stmt_essay = $pdo->prepare("SELECT COUNT(*) FROM cbt_soal s JOIN cbt_bank_soal b ON s.id_bank = b.id_bank WHERE s.tipe_soal='essay' AND {$filter_b['clause']}");
            $stmt_essay->execute($filter_b['params']);
            $tot_essay = (int)$stmt_essay->fetchColumn();

            $stmt_hots = $pdo->prepare("SELECT COUNT(*) FROM cbt_soal s JOIN cbt_bank_soal b ON s.id_bank = b.id_bank WHERE s.level_kognitif='L3' AND {$filter_b['clause']}");
            $stmt_hots->execute($filter_b['params']);
            $tot_hots = (int)$stmt_hots->fetchColumn();

            $filter_p = self::buildMapelFilter($info, 'p');
            $stmt_paket = $pdo->prepare("SELECT COUNT(*) FROM cbt_paket p WHERE {$filter_p['clause']}");
            $stmt_paket->execute($filter_p['params']);
            $tot_paket = (int)$stmt_paket->fetchColumn();

            $filter_j = self::buildMapelFilter($info, 'p');
            $stmt_jadwal_count = $pdo->prepare("SELECT COUNT(*) FROM cbt_jadwal j JOIN cbt_paket p ON j.id_paket = p.id_paket WHERE j.status='aktif' AND {$filter_j['clause']}");
            $stmt_jadwal_count->execute($filter_j['params']);
            $tot_ujian = (int)$stmt_jadwal_count->fetchColumn();

            $stats = [
                'total_bank'    => $tot_bank,
                'total_soal'    => $tot_soal,
                'total_pg'      => $tot_pg,
                'total_essay'   => $tot_essay,
                'total_hots'    => $tot_hots,
                'total_paket'   => $tot_paket,
                'ujian_aktif'   => $tot_ujian,
                'total_peserta' => 0,
                'total_selesai' => 0,
            ];

            $stmt_j = $pdo->prepare("
                SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal) as jml_peserta,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal AND status = 'selesai') as jml_selesai
                FROM cbt_jadwal j
                LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                WHERE {$filter_j['clause']}
                ORDER BY j.created_at DESC LIMIT 6
            ");
            $stmt_j->execute($filter_j['params']);
            $jadwal_terbaru = $stmt_j->fetchAll(PDO::FETCH_ASSOC);

            // Matriks untuk mapel guru
            $in_mapel = !empty($info['mapel_ids']) ? implode(',', array_map('intval', $info['mapel_ids'])) : '0';
            $stmt_matrix = $pdo->query("
                SELECT m.id_mapel, m.nama_mapel,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel) as total_soal,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel AND b.tingkat IN ('X', '10', 'Semua')) as soal_x,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel AND b.tingkat IN ('XI', '11', 'Semua')) as soal_xi,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel AND b.tingkat IN ('XII', '12', 'Semua')) as soal_xii,
                       (SELECT COUNT(*) FROM cbt_bank_soal b JOIN cbt_soal s ON b.id_bank = s.id_bank WHERE b.id_mapel = m.id_mapel AND s.level_kognitif = 'L3') as total_hots,
                       (SELECT COUNT(*) FROM cbt_paket p WHERE p.id_mapel = m.id_mapel) as total_paket,
                       (SELECT id_bank FROM cbt_bank_soal b WHERE b.id_mapel = m.id_mapel LIMIT 1) as id_bank
                FROM mapel m
                WHERE m.id_mapel IN ($in_mapel)
                ORDER BY m.nama_mapel ASC
            ");
            $mapel_readiness_list = $stmt_matrix ? $stmt_matrix->fetchAll(PDO::FETCH_ASSOC) : [];
        }

        require_once __DIR__ . '/../views/cbt_dashboard.php';
    }

    // ====================================================
    // BANK SOAL (WADAH & BUTIR SOAL)
    // ====================================================
    public static function bank_soal($pdo, $act)
    {
        $info = self::getAccessInfo($pdo);

        if ($act === 'store_bank') {
            $id_bank    = (int)($_POST['id_bank'] ?? 0);
            $nama_bank  = trim($_POST['nama_bank'] ?? '');
            $kode_bank  = trim($_POST['kode_bank'] ?? '') ?: strtoupper(substr(md5(rand()), 0, 8));
            $id_mapel   = (int)($_POST['id_mapel'] ?? 0);
            $tingkat    = trim($_POST['tingkat'] ?? 'Semua');
            $opsi_pg    = (int)($_POST['opsi_pg'] ?? 5);
            $jml_pg     = (int)($_POST['jml_pg'] ?? 0);
            $bobot_pg   = (float)($_POST['bobot_pg'] ?? 70);
            $jml_esai   = (int)($_POST['jml_esai'] ?? 0);
            $bobot_esai = (float)($_POST['bobot_esai'] ?? 30);
            $jml_tf     = (int)($_POST['jml_tf'] ?? 0);
            $bobot_tf   = (float)($_POST['bobot_tf'] ?? 0);
            $jml_matching   = (int)($_POST['jml_matching'] ?? 0);
            $bobot_matching = (float)($_POST['bobot_matching'] ?? 0);
            $deskripsi  = trim($_POST['deskripsi'] ?? '');
            $id_user    = $info['user_id'];

            // Validasi hak akses Guru atas Mapel
            if (!$info['is_admin'] && !empty($info['mapel_ids']) && !in_array($id_mapel, $info['mapel_ids'])) {
                $_SESSION['pesan_error'] = "Anda tidak memiliki hak akses penugasan pada mata pelajaran ini.";
                redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
                return;
            }

            if ($id_bank > 0) {
                // Update Bank Soal
                $stmt = $pdo->prepare("
                    UPDATE cbt_bank_soal 
                    SET nama_bank = ?, kode_bank = ?, id_mapel = ?, tingkat = ?, opsi_pg = ?, jml_pg = ?, bobot_pg = ?, jml_esai = ?, bobot_esai = ?, jml_tf = ?, bobot_tf = ?, jml_matching = ?, bobot_matching = ?, deskripsi = ?
                    WHERE id_bank = ?
                ");
                $stmt->execute([$nama_bank, $kode_bank, $id_mapel, $tingkat, $opsi_pg, $jml_pg, $bobot_pg, $jml_esai, $bobot_esai, $jml_tf, $bobot_tf, $jml_matching, $bobot_matching, $deskripsi, $id_bank]);
                $_SESSION['pesan_sukses'] = "Kriteria Bank Soal berhasil diperbarui.";
            } else {
                // Insert Bank Soal Baru
                $stmt = $pdo->prepare("
                    INSERT INTO cbt_bank_soal 
                    (nama_bank, kode_bank, id_mapel, tingkat, opsi_pg, jml_pg, bobot_pg, jml_esai, bobot_esai, jml_tf, bobot_tf, jml_matching, bobot_matching, deskripsi, id_user) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$nama_bank, $kode_bank, $id_mapel, $tingkat, $opsi_pg, $jml_pg, $bobot_pg, $jml_esai, $bobot_esai, $jml_tf, $bobot_tf, $jml_matching, $bobot_matching, $deskripsi, $id_user]);
                $_SESSION['pesan_sukses'] = "Bank Soal berhasil dibuat.";
            }

            redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
        } elseif ($act === 'delete_bank') {
            $id_bank = (int)($_GET['id_bank'] ?? 0);

            // Verifikasi kepemilikan
            if (!$info['is_admin']) {
                $check = $pdo->prepare("SELECT id_user, id_mapel FROM cbt_bank_soal WHERE id_bank = ?");
                $check->execute([$id_bank]);
                $b = $check->fetch(PDO::FETCH_ASSOC);
                if (!$b || ($b['id_user'] != $info['user_id'] && !in_array((int)$b['id_mapel'], $info['mapel_ids']))) {
                    $_SESSION['pesan_error'] = "Akses ditolak: Anda bukan pemilik bank soal ini.";
                    redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
                    return;
                }
            }

            // Hapus relasi cbt_paket_soal, opsi, media, dan soal terkait
            $stmt_sids = $pdo->prepare("SELECT id_soal FROM cbt_soal WHERE id_bank = ?");
            $stmt_sids->execute([$id_bank]);
            $soal_ids = $stmt_sids->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($soal_ids)) {
                $in = implode(',', array_map('intval', $soal_ids));
                $pdo->exec("DELETE FROM cbt_paket_soal WHERE id_soal IN ($in)");
                $pdo->exec("DELETE FROM cbt_soal_opsi WHERE id_soal IN ($in)");
                $pdo->exec("DELETE FROM cbt_soal_media WHERE id_soal IN ($in)");
                $pdo->exec("DELETE FROM cbt_soal WHERE id_bank = $id_bank");
            }
            $pdo->prepare("DELETE FROM cbt_bank_soal WHERE id_bank = ?")->execute([$id_bank]);
            $_SESSION['pesan_sukses'] = "Bank soal dan seluruh butir soalnya berhasil dihapus.";
            redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
        } elseif ($act === 'detail') {
            self::soalList($pdo);
        } elseif ($act === 'preview_siswa') {
            $id_bank = (int)($_GET['id_bank'] ?? 0);
            $stmt_bank = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
            $stmt_bank->execute([$id_bank]);
            $bank = $stmt_bank->fetch(PDO::FETCH_ASSOC);

            if (!$bank) {
                $_SESSION['pesan_error'] = "Bank soal tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
                return;
            }

            $where = ["s.id_bank = ?"];
            $params = [$id_bank];
            if (!empty($_GET['tingkat'])) {
                $tingkat_filter = trim($_GET['tingkat']);
                if ($tingkat_filter === 'X') {
                    $where[] = "(cp.fase = 'E' OR (s.id_cp IS NULL AND b.tingkat IN ('X', '10', 'Fase E')))";
                } elseif ($tingkat_filter === 'XI' || $tingkat_filter === 'XII') {
                    $where[] = "(cp.fase = 'F' OR (s.id_cp IS NULL AND b.tingkat = ?))";
                    $params[] = $tingkat_filter;
                }
            }
            $where_sql = implode(' AND ', $where);

            $stmt_soal = $pdo->prepare("
                SELECT s.*, 
                       cp.fase as fase_cp, cp.deskripsi_cp,
                       tp.kode_tp, tp.deskripsi_tp,
                       (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                FROM cbt_soal s
                JOIN cbt_bank_soal b ON s.id_bank = b.id_bank
                LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
                WHERE $where_sql
                ORDER BY s.nomor_urut ASC, s.id_soal ASC
            ");
            $stmt_soal->execute($params);
            $soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

            foreach ($soal_list as &$s) {
                $stmt_o = $pdo->prepare("SELECT * FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC, id_opsi ASC");
                $stmt_o->execute([$s['id_soal']]);
                $s['opsi_list'] = $stmt_o->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($s);

            $paket = [
                'nama_paket' => $bank['nama_bank'] . (!empty($_GET['tingkat']) ? ' (Kelas ' . htmlspecialchars($_GET['tingkat']) . ')' : ''),
                'nama_mapel' => $bank['nama_mapel'] ?? 'Mata Pelajaran',
                'tingkat'    => !empty($_GET['tingkat']) ? 'Kelas ' . htmlspecialchars($_GET['tingkat']) : ($bank['tingkat'] ?? 'Semua Tingkat'),
                'id_bank'    => $bank['id_bank']
            ];
            $title = "Simulasi Tampilan Siswa - " . htmlspecialchars($paket['nama_paket']);

            require_once __DIR__ . '/../views/cbt_preview_siswa.php';
            exit;
        } elseif ($act === 'create_soal') {
            self::soalForm($pdo, null);
        } elseif ($act === 'edit_soal') {
            $id_soal = (int)($_GET['id'] ?? 0);
            $stmt = $pdo->prepare("SELECT s.*, b.nama_bank, b.id_user, b.id_mapel FROM cbt_soal s JOIN cbt_bank_soal b ON s.id_bank = b.id_bank WHERE s.id_soal = ?");
            $stmt->execute([$id_soal]);
            $soal = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$soal) {
                $_SESSION['pesan_error'] = "Soal tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
                return;
            }
            if (!$info['is_admin'] && $soal['id_user'] != $info['user_id'] && !in_array((int)$soal['id_mapel'], $info['mapel_ids'])) {
                $_SESSION['pesan_error'] = "Akses ditolak: Anda tidak memiliki wewenang mengedit soal ini.";
                redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
                return;
            }
            self::soalForm($pdo, $soal);
        } elseif ($act === 'store_soal') {
            self::soalSave($pdo, false);
        } elseif ($act === 'update_soal') {
            self::soalSave($pdo, true);
        } elseif ($act === 'delete_soal') {
            $id_soal = (int)($_GET['id'] ?? 0);
            $id_bank = (int)($_GET['id_bank'] ?? 0);

            if (!$info['is_admin']) {
                $check = $pdo->prepare("SELECT b.id_user, b.id_mapel FROM cbt_soal s JOIN cbt_bank_soal b ON s.id_bank = b.id_bank WHERE s.id_soal = ?");
                $check->execute([$id_soal]);
                $b = $check->fetch(PDO::FETCH_ASSOC);
                if (!$b || ($b['id_user'] != $info['user_id'] && !in_array((int)$b['id_mapel'], $info['mapel_ids']))) {
                    $_SESSION['pesan_error'] = "Akses ditolak.";
                    redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
                    return;
                }
            }

            $pdo->prepare("DELETE FROM cbt_paket_soal WHERE id_soal = ?")->execute([$id_soal]);
            $pdo->prepare("DELETE FROM cbt_soal_opsi WHERE id_soal = ?")->execute([$id_soal]);
            $pdo->prepare("DELETE FROM cbt_soal_media WHERE id_soal = ?")->execute([$id_soal]);
            $pdo->prepare("DELETE FROM cbt_soal WHERE id_soal = ?")->execute([$id_soal]);
            $_SESSION['pesan_sukses'] = "Butir soal berhasil dihapus.";
            redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
        } elseif ($act === 'generate_ai') {
            self::generateAi($pdo);
        } elseif ($act === 'import_word_preview') {
            self::importWordPreview($pdo);
        } elseif ($act === 'import_word_save') {
            self::importWordSave($pdo);
        } elseif ($act === 'template_excel') {
            self::downloadTemplateExcel($pdo);
        } elseif ($act === 'import_excel') {
            self::importExcelSoal($pdo);
        } else {
            // Auto-populate / sinkronisasi wadah Bank Soal untuk seluruh mata pelajaran sekolah
            try {
                $stmt_auto = $pdo->prepare("
                    INSERT INTO cbt_bank_soal (nama_bank, kode_bank, id_mapel, tingkat, opsi_pg, jml_pg, bobot_pg, jml_esai, bobot_esai, id_user)
                    SELECT 
                        CONCAT('Bank Soal ', m.nama_mapel),
                        UPPER(SUBSTRING(MD5(CONCAT(m.id_mapel, m.nama_mapel)), 1, 8)),
                        m.id_mapel,
                        'Semua',
                        5,
                        0,
                        70,
                        0,
                        30,
                        ?
                    FROM mapel m
                    WHERE m.id_mapel NOT IN (SELECT DISTINCT id_mapel FROM cbt_bank_soal WHERE id_mapel IS NOT NULL)
                ");
                $stmt_auto->execute([$info['user_id']]);
            } catch (Exception $e) {
                // Abaikan jika sudah ada
            }

            $title = "Master Bank Soal";
            $filter_mapel = isset($_GET['id_mapel']) ? (int)$_GET['id_mapel'] : 0;
            $active_tab = $_GET['tab'] ?? ($info['is_admin'] ? 'all_mapel' : 'my_mapel');

            $where_clauses = ["1=1"];
            $params = [];

            if ($active_tab === 'my_mapel' && !$info['is_admin']) {
                if (!empty($info['mapel_ids'])) {
                    $in_ids = implode(',', array_map('intval', $info['mapel_ids']));
                    $where_clauses[] = "(b.id_mapel IN ($in_ids) OR b.id_user = ?)";
                    $params[] = $info['user_id'];
                } else {
                    $where_clauses[] = "b.id_user = ?";
                    $params[] = $info['user_id'];
                }
            }

            if ($filter_mapel > 0) {
                $where_clauses[] = "b.id_mapel = ?";
                $params[] = $filter_mapel;
            }

            if (!empty($_GET['tingkat'])) {
                $where_clauses[] = "b.tingkat = ?";
                $params[] = trim($_GET['tingkat']);
            }

            $where_sql = "WHERE " . implode(' AND ', $where_clauses);

            $stmt = $pdo->prepare("
                SELECT b.*, m.nama_mapel, 
                       (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = b.id_bank) as total_soal,
                       (SELECT COUNT(*) FROM cbt_soal s 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp 
                        WHERE s.id_bank = b.id_bank 
                          AND (cp.fase = 'E' OR (s.id_cp IS NULL AND b.tingkat IN ('X', '10', 'Fase E')))
                       ) as jml_kelas_x,
                       (SELECT COUNT(*) FROM cbt_soal s 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp 
                        WHERE s.id_bank = b.id_bank 
                          AND (cp.fase = 'F' AND b.tingkat = 'XI')
                       ) as jml_kelas_xi,
                       (SELECT COUNT(*) FROM cbt_soal s 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp 
                        WHERE s.id_bank = b.id_bank 
                          AND (cp.fase = 'F' AND b.tingkat = 'XII')
                       ) as jml_kelas_xii,
                       (SELECT COUNT(*) FROM cbt_soal s 
                        LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp 
                        WHERE s.id_bank = b.id_bank 
                          AND (cp.fase = 'F' OR (s.id_cp IS NULL AND b.tingkat IN ('XI', 'XII', '11', '12', 'Fase F')))
                       ) as jml_fase_f,
                       (SELECT COUNT(DISTINCT id_tp) FROM cbt_soal WHERE id_bank = b.id_bank AND id_tp > 0) as total_tp_terisi,
                       (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = b.id_bank AND tipe_soal = 'pg') as total_pg,
                       (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = b.id_bank AND tipe_soal = 'essay') as total_essay,
                       (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = b.id_bank AND tipe_soal = 'tf') as total_tf,
                       (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = b.id_bank AND tipe_soal = 'matching') as total_matching
                FROM cbt_bank_soal b
                LEFT JOIN mapel m ON b.id_mapel = m.id_mapel
                $where_sql
                ORDER BY b.id_bank DESC
            ");
            $stmt->execute($params);
            $bank_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $all_mapel_list = LmsModel::getAllMapel($pdo);
            $my_mapel_list = $info['is_admin'] ? $all_mapel_list : LmsModel::getMapelByGuru($pdo, $info['id_guru']);
            $mapel_list = ($active_tab === 'my_mapel' && !$info['is_admin']) ? $my_mapel_list : $all_mapel_list;

            require_once __DIR__ . '/../views/cbt_bank_soal.php';
        }
    }

    /**
     * Halaman Rincian Wadah Bank Soal & Daftar Butir Soal di Dalamnya
     */
    private static function soalList($pdo)
    {
        $info = self::getAccessInfo($pdo);
        $id_bank = (int)($_GET['id_bank'] ?? 0);
        $stmt_bank = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
        $stmt_bank->execute([$id_bank]);
        $bank = $stmt_bank->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            $_SESSION['pesan_error'] = "Wadah Bank Soal tidak ditemukan.";
            redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
            return;
        }

        // Tentukan apakah user memiliki hak kelola (tambah/edit/hapus) atau hanya mode jelajah (read-only)
        $can_edit = $info['is_admin'] || in_array((int)$bank['id_mapel'], $info['mapel_ids']) || ($bank['id_user'] == $info['user_id']);

        $title = "Master Bank Soal - " . htmlspecialchars($bank['nama_mapel'] ?? $bank['nama_bank']);
        
        $active_tingkat = strtoupper(trim($_GET['tingkat'] ?? ''));
        $where = ["s.id_bank = ?"];
        $params = [$id_bank];

        if ($active_tingkat === 'X') {
            $where[] = "(cp.fase = 'E' OR (s.id_cp IS NULL AND b.tingkat IN ('X', '10', 'Fase E')))";
        } elseif ($active_tingkat === 'XI' || $active_tingkat === 'XII') {
            $where[] = "(cp.fase = 'F' OR (s.id_cp IS NULL AND b.tingkat IN ('XI', 'XII', '11', '12', 'Fase F')))";
        }
        $where_sql = implode(' AND ', $where);

        $stmt_soal = $pdo->prepare("
            SELECT s.*, 
                   cp.fase as fase_cp, cp.deskripsi_cp,
                   tp.kode_tp, tp.deskripsi_tp, tp.materi as tp_materi,
                   (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
            FROM cbt_soal s
            JOIN cbt_bank_soal b ON s.id_bank = b.id_bank
            LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
            LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
            WHERE $where_sql
            ORDER BY s.nomor_urut ASC, s.id_soal ASC
        ");
        $stmt_soal->execute($params);
        $soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

        // Ambil Opsi untuk masing-masing soal
        foreach ($soal_list as &$s) {
            $stmt_o = $pdo->prepare("SELECT * FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC, id_opsi ASC");
            $stmt_o->execute([$s['id_soal']]);
            $s['opsi_list'] = $stmt_o->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($s);

        // Ambil Daftar CP untuk mapel bank soal (disesuaikan dengan tingkat jika dipilih)
        if ($active_tingkat === 'X') {
            $stmt_cp = $pdo->prepare("SELECT id_cp, deskripsi_cp, fase FROM capaian_pembelajaran WHERE id_mapel = ? AND fase = 'E' ORDER BY id_cp ASC");
            $stmt_cp->execute([(int)$bank['id_mapel']]);
        } elseif ($active_tingkat === 'XI' || $active_tingkat === 'XII') {
            $stmt_cp = $pdo->prepare("SELECT id_cp, deskripsi_cp, fase FROM capaian_pembelajaran WHERE id_mapel = ? AND fase = 'F' ORDER BY id_cp ASC");
            $stmt_cp->execute([(int)$bank['id_mapel']]);
        } else {
            $stmt_cp = $pdo->prepare("SELECT id_cp, deskripsi_cp, fase FROM capaian_pembelajaran WHERE id_mapel = ? ORDER BY fase ASC, id_cp ASC");
            $stmt_cp->execute([(int)$bank['id_mapel']]);
        }
        $cp_list = $stmt_cp->fetchAll(PDO::FETCH_ASSOC);

        // Ambil Daftar TP untuk mapel bank soal
        $cp_ids = array_column($cp_list, 'id_cp');
        if (!empty($cp_ids)) {
            $in_cp = implode(',', array_map('intval', $cp_ids));
            $stmt_tp = $pdo->prepare("SELECT id_tp, id_cp, kode_tp, deskripsi_tp, materi FROM tujuan_pembelajaran WHERE id_mapel = ? AND id_cp IN ($in_cp) ORDER BY kode_tp ASC");
            $stmt_tp->execute([(int)$bank['id_mapel']]);
        } else {
            $stmt_tp = $pdo->prepare("SELECT id_tp, id_cp, kode_tp, deskripsi_tp, materi FROM tujuan_pembelajaran WHERE id_mapel = ? ORDER BY kode_tp ASC");
            $stmt_tp->execute([(int)$bank['id_mapel']]);
        }
        $tp_list = $stmt_tp->fetchAll(PDO::FETCH_ASSOC);

        // Kumpulkan Daftar Topik Materi Unik
        $materi_list = [];
        foreach ($tp_list as $tp_item) {
            if (!empty($tp_item['materi']) && !in_array($tp_item['materi'], $materi_list)) {
                $materi_list[] = $tp_item['materi'];
            }
        }
        foreach ($soal_list as $sl) {
            if (!empty($sl['lingkup_materi']) && !in_array($sl['lingkup_materi'], $materi_list)) {
                $materi_list[] = $sl['lingkup_materi'];
            }
        }

        require_once __DIR__ . '/../views/cbt_bank_soal_detail.php';
    }

    /**
     * Form Input / Edit Butir Soal Lengkap
     */
    private static function soalForm($pdo, $soal = null)
    {
        $info = self::getAccessInfo($pdo);
        $id_bank = $soal ? (int)$soal['id_bank'] : (int)($_GET['id_bank'] ?? 0);
        $stmt_bank = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
        $stmt_bank->execute([$id_bank]);
        $bank = $stmt_bank->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            $_SESSION['pesan_error'] = "Wadah Bank Soal tidak ditemukan.";
            redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
            return;
        }

        $can_edit = $info['is_admin'] || in_array((int)$bank['id_mapel'], $info['mapel_ids']) || ($bank['id_user'] == $info['user_id']);
        if (!$can_edit) {
            $_SESSION['pesan_error'] = "Akses ditolak: Anda tidak memiliki wewenang mengelola bank soal ini.";
            redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
            return;
        }

        $title = ($soal ? "Edit Butir Soal" : "Tambah Butir Soal Manual") . " - " . htmlspecialchars($bank['nama_bank']);

        $stmt_cp = $pdo->prepare("SELECT id_cp, deskripsi_cp, fase FROM capaian_pembelajaran WHERE id_mapel = ? ORDER BY fase ASC, id_cp ASC");
        $stmt_cp->execute([(int)$bank['id_mapel']]);
        $cp_list = $stmt_cp->fetchAll(PDO::FETCH_ASSOC);

        $stmt_tp = $pdo->prepare("SELECT id_tp, id_cp, kode_tp, deskripsi_tp, materi FROM tujuan_pembelajaran WHERE id_mapel = ? ORDER BY kode_tp ASC");
        $stmt_tp->execute([(int)$bank['id_mapel']]);
        $tp_list = $stmt_tp->fetchAll(PDO::FETCH_ASSOC);

        $opsi_list = [];
        if ($soal) {
            $stmt_o = $pdo->prepare("SELECT * FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC");
            $stmt_o->execute([(int)$soal['id_soal']]);
            $opsi_list = $stmt_o->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../views/cbt_soal_form.php';
    }

    /**
     * Simpan / Update Butir Soal (Mendukung Multimedia & Metadata Kurikulum)
     */
    private static function soalSave($pdo, $is_update)
    {
        $id_bank        = (int)($_POST['id_bank'] ?? 0);
        $id_soal        = (int)($_POST['id_soal'] ?? 0);
        $id_cp          = !empty($_POST['id_cp']) ? (int)$_POST['id_cp'] : null;
        $id_tp          = !empty($_POST['id_tp']) ? (int)$_POST['id_tp'] : null;
        $lingkup_materi = trim($_POST['lingkup_materi'] ?? '');
        $indikator_soal = trim($_POST['indikator_soal'] ?? '');
        $level_kognitif = trim($_POST['level_kognitif'] ?? 'L2');
        $stimulus       = trim($_POST['stimulus'] ?? '');
        $tipe_soal      = $_POST['tipe_soal'] ?? 'pg';
        $pertanyaan     = $_POST['pertanyaan'] ?? '';
        $bobot          = (int)($_POST['bobot'] ?? 1);
        $kesulitan      = $_POST['tingkat_kesulitan'] ?? 'sedang';
        $pembahasan     = trim($_POST['pembahasan'] ?? '');
        $rubrik         = trim($_POST['rubrik_penilaian'] ?? '');
        $media_tipe     = $_POST['media_tipe'] ?? 'none';
        $media_url      = trim($_POST['media_url'] ?? '');

        // Upload Media Stimulus Soal jika ada
        if (!empty($_FILES['media_file']['name'])) {
            $upload_dir = __DIR__ . '/../../uploads/cbt/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
            $filename = 'media_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $target = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target)) {
                $media_url = 'uploads/cbt/' . $filename;
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) $media_tipe = 'gambar';
                elseif (in_array($ext, ['mp3','wav','ogg','m4a'])) $media_tipe = 'audio';
                elseif (in_array($ext, ['mp4','webm','mkv'])) $media_tipe = 'video';
            }
        }

        // Auto-convert YouTube URL ke Embed URL
        if (!empty($media_url)) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $media_url, $yt_match)) {
                $media_url = 'https://www.youtube.com/embed/' . $yt_match[1];
                $media_tipe = 'video';
            }
        }

        $kunci_val = null;
        if ($tipe_soal === 'pg') {
            $kunci_val = strtoupper($_POST['kunci_pg'] ?? 'A');
        } elseif ($tipe_soal === 'tf') {
            $kunci_val = strtoupper($_POST['kunci_tf'] ?? 'B');
        }

        if ($is_update) {
            $stmt = $pdo->prepare("
                UPDATE cbt_soal 
                SET id_cp = ?, id_tp = ?, lingkup_materi = ?, indikator_soal = ?, level_kognitif = ?, stimulus = ?, 
                    tipe_soal = ?, pertanyaan = ?, bobot = ?, tingkat_kesulitan = ?, pembahasan = ?, rubrik_penilaian = ?, kunci_jawaban = ?, media_tipe = ?, media_url = ?
                WHERE id_soal = ?
            ");
            $stmt->execute([$id_cp, $id_tp, $lingkup_materi, $indikator_soal, $level_kognitif, $stimulus, $tipe_soal, $pertanyaan, $bobot, $kesulitan, $pembahasan, $rubrik, $kunci_val, $media_tipe, $media_url, $id_soal]);
        } else {
            $stmt_no = $pdo->prepare("SELECT IFNULL(MAX(nomor_urut), 0) + 1 FROM cbt_soal WHERE id_bank = ?");
            $stmt_no->execute([$id_bank]);
            $nomor_urut = $stmt_no->fetchColumn();

            $stmt = $pdo->prepare("
                INSERT INTO cbt_soal (id_bank, id_cp, id_tp, lingkup_materi, indikator_soal, level_kognitif, stimulus, nomor_urut, tipe_soal, pertanyaan, bobot, tingkat_kesulitan, pembahasan, rubrik_penilaian, kunci_jawaban, media_tipe, media_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_bank, $id_cp, $id_tp, $lingkup_materi, $indikator_soal, $level_kognitif, $stimulus, $nomor_urut, $tipe_soal, $pertanyaan, $bobot, $kesulitan, $pembahasan, $rubrik, $kunci_val, $media_tipe, $media_url]);
            $id_soal = $pdo->lastInsertId();
        }

        // 1. Simpan Opsi Pilihan Ganda (PG)
        if ($tipe_soal === 'pg') {
            $kunci_pg = $kunci_val ?: 'A';
            $pdo->prepare("DELETE FROM cbt_soal_opsi WHERE id_soal = ?")->execute([$id_soal]);

            $upload_dir = __DIR__ . '/../../uploads/cbt/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            foreach (['A','B','C','D','E'] as $label) {
                if (!isset($_POST['opsi'][$label])) continue;
                $isi = trim($_POST['opsi'][$label]);
                $gambar_opsi = '';

                // Handle upload gambar opsi
                $file_key = 'gambar_opsi_' . $label;
                if (!empty($_FILES[$file_key]['name'])) {
                    $ext = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
                    $filename = 'opsi_' . $label . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $filename)) {
                        $gambar_opsi = 'uploads/cbt/' . $filename;
                    }
                }

                if ($isi !== '' || $gambar_opsi !== '') {
                    $is_benar = ($label === $kunci_pg) ? 1 : 0;
                    $stmt_o = $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, gambar, is_benar) VALUES (?, ?, ?, ?, ?)");
                    $stmt_o->execute([$id_soal, $label, $isi, $gambar_opsi, $is_benar]);
                }
            }
        }

        // 2. Simpan Benar / Salah (TF)
        elseif ($tipe_soal === 'tf') {
            $kunci_tf = $kunci_val ?: 'B';
            $pdo->prepare("DELETE FROM cbt_soal_opsi WHERE id_soal = ?")->execute([$id_soal]);

            $stmt_b = $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, 'B', 'BENAR', ?)");
            $stmt_b->execute([$id_soal, ($kunci_tf === 'B' ? 1 : 0)]);

            $stmt_s = $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, 'S', 'SALAH', ?)");
            $stmt_s->execute([$id_soal, ($kunci_tf === 'S' ? 1 : 0)]);
        }

        // 3. Simpan Menjodohkan (Matching)
        elseif ($tipe_soal === 'matching') {
            $pdo->prepare("DELETE FROM cbt_soal_opsi WHERE id_soal = ?")->execute([$id_soal]);
            if (!empty($_POST['matching_kiri']) && is_array($_POST['matching_kiri'])) {
                foreach ($_POST['matching_kiri'] as $k => $premis) {
                    $premis = trim($premis);
                    $pasangan = trim($_POST['matching_kanan'][$k] ?? '');
                    if ($premis !== '' && $pasangan !== '') {
                        $stmt_m = $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, ?, ?, 1)");
                        $stmt_m->execute([$id_soal, $premis, $pasangan]);
                    }
                }
            }
        }

        $_SESSION['pesan_sukses'] = $is_update ? "Butir soal berhasil diperbarui." : "Butir soal berhasil ditambahkan.";
        redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
    }

    /**
     * AI Generator Soal Asesmen CBT (Lengkap dengan Indikator, Kartu Soal, Level Kognitif & Kunci)
     */
    private static function generateAi($pdo)
    {
        set_time_limit(180);
        header('Content-Type: application/json');
        require_once __DIR__ . '/../models/AIModel.php';

        $id_bank        = (int)($_POST['id_bank'] ?? 0);
        $id_cp          = !empty($_POST['id_cp']) ? (int)$_POST['id_cp'] : null;
        $id_tp          = !empty($_POST['id_tp']) ? (int)$_POST['id_tp'] : null;
        $fase_tingkat   = trim($_POST['fase_tingkat'] ?? '');
        $tipe_soal      = $_POST['tipe_soal'] ?? 'pg';
        $level_kognitif = trim($_POST['level_kognitif'] ?? 'L2');
        $jml_soal       = max(1, min(10, (int)($_POST['jumlah_soal'] ?? 5)));
        $kesulitan      = $_POST['kesulitan'] ?? 'sedang';
        $topik          = trim($_POST['topik'] ?? '');
        $stimulus_mode  = trim($_POST['stimulus_mode'] ?? 'wacana_teks');
        $fokus_khusus   = trim($_POST['fokus_khusus'] ?? ($_POST['instruksi_tambahan'] ?? ''));

        // Ambil info bank soal & mapel
        $stmt_b = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
        $stmt_b->execute([$id_bank]);
        $bank = $stmt_b->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            echo json_encode(['status' => 'error', 'message' => 'Wadah Bank Soal tidak ditemukan.']);
            exit;
        }

        $mapel = $bank['nama_mapel'] ?? 'Mata Pelajaran';
        $tingkat = !empty($fase_tingkat) ? "Kelas $fase_tingkat" : ($bank['tingkat'] ?? 'SMA');

        // Ambil teks CP & TP jika dipilih
        $cp_text = "";
        $tp_text = "";
        $lingkup_materi = $topik;
        if ($id_cp) {
            $stmt_cp = $pdo->prepare("SELECT deskripsi_cp, fase FROM capaian_pembelajaran WHERE id_cp = ?");
            $stmt_cp->execute([$id_cp]);
            $cp_row = $stmt_cp->fetch(PDO::FETCH_ASSOC);
            if ($cp_row) {
                $cp_text = (!empty($cp_row['fase']) ? "[Fase {$cp_row['fase']}] " : "") . $cp_row['deskripsi_cp'];
            }
        }
        if ($id_tp) {
            $stmt_tp = $pdo->prepare("SELECT kode_tp, deskripsi_tp, materi FROM tujuan_pembelajaran WHERE id_tp = ?");
            $stmt_tp->execute([$id_tp]);
            $tp_row = $stmt_tp->fetch(PDO::FETCH_ASSOC);
            if ($tp_row) {
                $tp_text = ($tp_row['kode_tp'] ? $tp_row['kode_tp'] . ': ' : '') . $tp_row['deskripsi_tp'];
                if (!empty($tp_row['materi']) && empty($topik)) {
                    $lingkup_materi = $tp_row['materi'];
                }
            }
        }

        if (empty($topik) && empty($tp_text) && empty($cp_text)) {
            echo json_encode(['status' => 'error', 'message' => 'Silakan pilih TP/CP atau ketikkan topik materi yang ingin digenerate.']);
            exit;
        }

        $system_instruction = "Anda adalah Asesor Pendidikan & Pakar Pembuat Soal Asesmen Standar Nasional Kurikulum Merdeka Indonesia tingkat SMA. Tugas Anda membuat butir soal asesmen yang kontekstual, menarik, valid, bernalar kritis (HOTS/MOTS/LOTS), lengkap dengan wacana/ilustrasi pendukung yang relevan, indikator kisi-kisi operasional standar Kemdikbud, kunci jawaban, dan pembahasan mendalam.";

        $prompt = "Buatlah sebanyak {$jml_soal} butir soal asesmen berkualitas tinggi untuk mata pelajaran '{$mapel}' {$tingkat}.\n";
        if (!empty($cp_text)) $prompt .= "Capaian Pembelajaran (CP): {$cp_text}\n";
        if (!empty($tp_text)) $prompt .= "Tujuan Pembelajaran (TP): {$tp_text}\n";
        if (!empty($lingkup_materi)) $prompt .= "Topik / Lingkup Materi: {$lingkup_materi}\n";
        $prompt .= "Target Level Kognitif: " . $level_kognitif . " (" . ($level_kognitif==='L3' ? 'Penalaran / Analisis Kritis HOTS' : ($level_kognitif==='L1' ? 'Pengetahuan / Pemahaman LOTS' : 'Aplikasi / Penerapan Konsep MOTS')) . ")\n";
        $prompt .= "Tipe Soal: " . strtoupper($tipe_soal) . " (" . ($tipe_soal === 'pg' ? 'Pilihan Ganda 5 opsi A, B, C, D, E' : ($tipe_soal === 'essay' ? 'Esai / Uraian Pemecahan Masalah' : ($tipe_soal === 'tf' ? 'Benar / Salah' : 'Menjodohkan 4 Pasangan'))) . ")\n";
        $prompt .= "Tingkat Kesulitan: {$kesulitan}\n";
        
        // Kriteria Wacana / Ilustrasi
        if ($stimulus_mode === 'none' || $stimulus_mode === 'tanpa_stimulus') {
            $prompt .= "Kriteria Wacana/Konteks: TANPA WACANA (Pertanyaan Langsung / Direct Question). Biarkan field 'stimulus' bernilai string kosong \"\", dan langsung rumuskan pertanyaan inti secara to the point.\n";
        } elseif ($stimulus_mode === 'audio_listening') {
            $prompt .= "Kriteria Wacana/Konteks: AUDIO LISTENING / PERCAKAPAN. Tuliskan teks skenario dialog percakapan yang jelas pada field 'stimulus' agar dapat langsung diperdengarkan sebagai audio listening kepada siswa.\n";
        } elseif ($stimulus_mode === 'deskripsi_gambar') {
            $prompt .= "Kriteria Wacana/Konteks: GAMBAR / DIAGRAM / GRAFIK VISUAL. Wajib sertakan field 'image_prompt' berupa prompt bahasa Inggris deskriptif dan detail untuk meng-generate gambar/diagram visual edukatif beresolusi tinggi yang sesuai dengan pertanyaan (misal: 'Clean scientific diagram of plant cell structure with labeled chloroplast and cell wall', 'Cartesian coordinate graph of exponential curve y = 2^x with points', 'Anatomy illustration of human heart diagram', dll).\n";
        } elseif ($stimulus_mode === 'video_observasi') {
            $prompt .= "Kriteria Wacana/Konteks: OBSERVASI VIDEO / FENOMENA. Sertakan narasi fenomena dan wajib sertakan field 'media_url' berupa tautan video YouTube edukatif atau ID video YouTube yang relevan dengan topik eksperimen/fenomena tersebut.\n";
        } else {
            $prompt .= "Kriteria Wacana/Konteks: Teks wacana studi kasus kontekstual kehidupan nyata yang relevan bagi siswa SMA.\n";
        }

        if (!empty($fokus_khusus)) {
            $prompt .= "🎯 Intervensi Khusus / Fokus Kategori Soal: {$fokus_khusus}\n";
        }

        $prompt .= "\nKetentuan Bahasa & Redaksi (PENTING):\n";
        $prompt .= "- BAHASA ARAB & INGGRIS: Jika mata pelajaran atau topik adalah Bahasa Arab (atau materi PAI/Qur'an/Hadis), rumuskan wacana, pertanyaan, dan opsi dalam Bahasa Arab Fusha lengkap dengan harakat/tanda baca yang tepat dan rapi. Jika mata pelajaran adalah Bahasa Inggris (English), rumuskan reading passages, dialog, dan butir soal seluruhnya dalam Bahasa Inggris baku.\n";
        $prompt .= "- ATURAN UTAMA KATA: DILARANG KERAS menggunakan kata kaku 'stimulus' di dalam teks pertanyaan maupun indikator soal (JANGAN menulis 'Berdasarkan stimulus di atas...').\n";
        $prompt .= "- Gunakan frasa alami yang tepat dan kontekstual, misalnya: 'Berdasarkan teks di atas...', 'Berdasarkan wacana tersebut...', 'Berdasarkan data pada tabel di atas...', 'Berdasarkan grafik tersebut...', 'Perhatikan gambar berikut...', 'Berdasarkan dialog di atas...', atau untuk pertanyaan langsung: 'Bentuk sederhana dari...', 'Tentukan nilai dari...', 'Himpunan penyelesaian dari...'.\n";
        $prompt .= "- Indikator kisi-kisi juga gunakan frasa alami, misal: 'Disajikan teks wacana tentang..., peserta didik dapat...', 'Disajikan data tabel..., peserta didik dapat menganalisis...'.\n";
        $prompt .= "- Rumus matematika/sains wajib ditulis dalam notasi LaTeX standar berpenutup dollar ($...$ atau $$...$$).\n";
        $prompt .= "\nFormat output HARUS berupa JSON murni dengan skema berikut:\n";
        $prompt .= "{\n";
        $prompt .= '  "items": [' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "pertanyaan": "Teks pertanyaan (sertakan konteks jika perlu, rumus gunakan LaTeX $...$)",' . "\n";
        $prompt .= '      "stimulus": "Teks wacana atau konteks pendukung jika ada",' . "\n";
        $prompt .= '      "media_tipe": "' . ($stimulus_mode==='deskripsi_gambar'?'gambar':($stimulus_mode==='video_observasi'?'video':($stimulus_mode==='audio_listening'?'audio':'none'))) . '",' . "\n";
        $prompt .= '      "image_prompt": "Prompt visual deskriptif bahasa Inggris untuk generate gambar diagram (hanya jika mode gambar)",' . "\n";
        $prompt .= '      "media_url": "URL video YouTube edukatif (hanya jika mode video)",' . "\n";
        $prompt .= '      "indikator_soal": "Kalimat indikator operasional kisi-kisi (contoh: Disajikan ..., peserta didik dapat ...)",' . "\n";
        $prompt .= '      "level_kognitif": "' . $level_kognitif . '",' . "\n";
        $prompt .= '      "tingkat_kesulitan": "' . $kesulitan . '",' . "\n";
        $prompt .= '      "tipe_soal": "' . $tipe_soal . '",' . "\n";
        $prompt .= '      "bobot": ' . ($tipe_soal === 'essay' ? 10 : 1) . ",\n";
        if ($tipe_soal === 'pg') {
            $prompt .= '      "opsi": [' . "\n";
            $prompt .= '        {"label": "A", "teks": "Teks opsi A", "is_benar": 0},' . "\n";
            $prompt .= '        {"label": "B", "teks": "Teks opsi B", "is_benar": 1},' . "\n";
            $prompt .= '        {"label": "C", "teks": "Teks opsi C", "is_benar": 0},' . "\n";
            $prompt .= '        {"label": "D", "teks": "Teks opsi D", "is_benar": 0},' . "\n";
            $prompt .= '        {"label": "E", "teks": "Teks opsi E", "is_benar": 0}' . "\n";
            $prompt .= '      ],' . "\n";
            $prompt .= '      "kunci_jawaban": "B",' . "\n";
        } elseif ($tipe_soal === 'tf') {
            $prompt .= '      "opsi": [' . "\n";
            $prompt .= '        {"label": "B", "teks": "BENAR", "is_benar": 1},' . "\n";
            $prompt .= '        {"label": "S", "teks": "SALAH", "is_benar": 0}' . "\n";
            $prompt .= '      ],' . "\n";
            $prompt .= '      "kunci_jawaban": "B",' . "\n";
        } elseif ($tipe_soal === 'matching') {
            $prompt .= '      "opsi": [' . "\n";
            $prompt .= '        {"label": "Premis 1", "teks": "Pasangan Cocok 1", "is_benar": 1},' . "\n";
            $prompt .= '        {"label": "Premis 2", "teks": "Pasangan Cocok 2", "is_benar": 1},' . "\n";
            $prompt .= '        {"label": "Premis 3", "teks": "Pasangan Cocok 3", "is_benar": 1},' . "\n";
            $prompt .= '        {"label": "Premis 4", "teks": "Pasangan Cocok 4", "is_benar": 1}' . "\n";
            $prompt .= '      ],' . "\n";
            $prompt .= '      "kunci_jawaban": "Pasangan Menjodohkan",' . "\n";
        } else {
            $prompt .= '      "kunci_jawaban": "Inti jawaban esai kontekstual",' . "\n";
        }
        $prompt .= '      "pembahasan": "Penjelasan langkah penyelesaian secara terperinci",' . "\n";
        $prompt .= '      "rubrik_penilaian": "Pedoman penskoran skor penuh, sebagian, dan 0"' . "\n";
        $prompt .= '    }' . "\n";
        $prompt .= '  ]' . "\n";
        $prompt .= "}\n";

        $res = AIModel::generate($pdo, $prompt, $system_instruction, true);

        if (!$res['success']) {
            echo json_encode(['status' => 'error', 'message' => $res['message'] ?? 'Gagal memproses ke Gemini API.']);
            exit;
        }

        $raw_text = $res['text'] ?? '';
        $clean_json = preg_replace('/^```json\s*|\s*```$/m', '', trim($raw_text));
        $data = json_decode($clean_json, true);

        $items = $data['items'] ?? ($data['soal_list'] ?? []);
        if (!$data || !is_array($items) || empty($items)) {
            echo json_encode(['status' => 'error', 'message' => 'Format balasan AI tidak valid atau kosong.', 'raw' => $raw_text]);
            exit;
        }

        // Ambil nomor urut tertinggi
        $stmt_no = $pdo->prepare("SELECT IFNULL(MAX(nomor_urut), 0) FROM cbt_soal WHERE id_bank = ?");
        $stmt_no->execute([$id_bank]);
        $last_no = (int)$stmt_no->fetchColumn();

        $saved_count = 0;
        foreach ($items as $it) {
            $last_no++;
            $pertanyaan_it  = trim($it['pertanyaan'] ?? '');
            $stimulus_it    = trim($it['stimulus'] ?? '');
            $indikator_it   = trim($it['indikator_soal'] ?? '');
            $lvl_it         = strtoupper($it['level_kognitif'] ?? $level_kognitif);
            $kesulitan_it   = $it['tingkat_kesulitan'] ?? $kesulitan;
            $tipe_it        = $it['tipe_soal'] ?? $tipe_soal;
            $bobot_it       = (int)($it['bobot'] ?? ($tipe_it === 'essay' ? 10 : 1));
            $kunci_it       = trim($it['kunci_jawaban'] ?? '');
            $pembahasan_it  = trim($it['pembahasan'] ?? '');
            $rubrik_it      = trim($it['rubrik_penilaian'] ?? '');

            // Multimedia Auto Generation
            $media_tipe_it  = trim($it['media_tipe'] ?? 'none');
            $media_url_it   = trim($it['media_url'] ?? '');
            $img_prompt_it  = trim($it['image_prompt'] ?? '');

            if ($stimulus_mode === 'deskripsi_gambar' || $media_tipe_it === 'gambar' || !empty($img_prompt_it)) {
                $media_tipe_it = 'gambar';
                $p_desc = !empty($img_prompt_it) ? $img_prompt_it : ($lingkup_materi . " " . $mapel . " educational diagram illustration high quality");
                $p_desc .= ", clean sharp lines, high definition educational textbook illustration, sharp focus, 4k resolution, no blur";
                
                $raw_img_url = "https://image.pollinations.ai/prompt/" . rawurlencode($p_desc) . "?width=1024&height=640&model=flux&nologo=true&seed=" . rand(1000, 99999);
                
                // Download and save locally to server to guarantee 100% instant display & prevent broken image icons
                $upload_dir = __DIR__ . '/../../uploads/cbt/';
                if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777, true);
                
                $filename = 'ai_img_' . time() . '_' . rand(100, 999) . '.jpg';
                $target = $upload_dir . $filename;
                
                $ctx = stream_context_create([
                    'http' => ['timeout' => 12, 'ignore_errors' => true],
                    'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
                ]);
                $img_data = @file_get_contents($raw_img_url, false, $ctx);
                if ($img_data && strlen($img_data) > 1000) {
                    @file_put_contents($target, $img_data);
                    $media_url_it = 'uploads/cbt/' . $filename;
                } else {
                    $media_url_it = $raw_img_url;
                }
            } elseif ($stimulus_mode === 'video_observasi' || $media_tipe_it === 'video') {
                $media_tipe_it = 'video';
                if (!empty($media_url_it)) {
                    $media_url_it = self::formatVideoEmbedUrl($media_url_it);
                } else {
                    $media_url_it = 'https://www.youtube.com/embed?listType=search&list=' . rawurlencode($lingkup_materi . ' ' . $mapel);
                }
            } elseif ($stimulus_mode === 'audio_listening' || $media_tipe_it === 'audio') {
                $media_tipe_it = 'audio';
            }

            if (empty($pertanyaan_it)) continue;

            $stmt_ins = $pdo->prepare("
                INSERT INTO cbt_soal (id_bank, id_cp, id_tp, lingkup_materi, indikator_soal, level_kognitif, stimulus, nomor_urut, tipe_soal, pertanyaan, bobot, tingkat_kesulitan, pembahasan, rubrik_penilaian, kunci_jawaban, media_tipe, media_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_ins->execute([
                $id_bank, $id_cp, $id_tp, $lingkup_materi, $indikator_it, $lvl_it, $stimulus_it, $last_no, $tipe_it, $pertanyaan_it, $bobot_it, $kesulitan_it, $pembahasan_it, $rubrik_it, $kunci_it, $media_tipe_it, $media_url_it
            ]);
            $inserted_id = $pdo->lastInsertId();

            // Simpan Opsi
            if (!empty($it['opsi']) && is_array($it['opsi'])) {
                foreach ($it['opsi'] as $op) {
                    $op_label = trim($op['label'] ?? '');
                    $op_teks  = trim($op['teks'] ?? ($op['isi_opsi'] ?? ''));
                    $op_benar = !empty($op['is_benar']) ? 1 : 0;
                    if ($op_label !== '' && $op_teks !== '') {
                        $stmt_op = $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, ?, ?, ?)");
                        $stmt_op->execute([$inserted_id, $op_label, $op_teks, $op_benar]);
                    }
                }
            }
            $saved_count++;
        }

        $_SESSION['pesan_sukses'] = "Berhasil memproduksi dan menyimpan {$saved_count} butir soal berstandar Kurikulum Merdeka ke dalam Bank Soal.";

        echo json_encode([
            'status' => 'success',
            'message' => "Berhasil memproduksi dan menyimpan {$saved_count} butir soal berstandar Kurikulum Merdeka ke dalam Bank Soal.",
            'count' => $saved_count
        ]);
        exit;
    }

    /**
     * Download Template Excel Resmi untuk Import Butir Soal CBT
     */
    public static function downloadTemplateExcel($pdo)
    {
        if (ob_get_level()) ob_end_clean();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Template Soal CBT");

        $headers = [
            'A1' => 'No',
            'B1' => 'Tipe Soal (pg/essay/tf)',
            'C1' => 'Teks Pertanyaan / Soal',
            'D1' => 'Opsi A',
            'E1' => 'Opsi B',
            'F1' => 'Opsi C',
            'G1' => 'Opsi D',
            'H1' => 'Opsi E',
            'I1' => 'Kunci Jawaban',
            'J1' => 'Level Kognitif (L1/L2/L3)',
            'K1' => 'Tingkat Kesulitan (mudah/sedang/sulit)',
            'L1' => 'Topik / Lingkup Materi',
            'M1' => 'Pembahasan / Rubrik'
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $samples = [
            [1, 'pg', 'Bentuk sederhana dari $\\frac{5^8}{5^3}$ adalah ....', '$5^{11}$', '$5^5$', '$5^{24}$', '$1^5$', '$5^{\\frac{8}{3}}$', 'B', 'L2', 'sedang', 'Eksponen & Logaritma', 'Gunakan sifat pembagian eksponen: a^m / a^n = a^(m-n)'],
            [2, 'pg', 'Ibukota Nusantara (IKN) berlokasi di provinsi ....', 'Kalimantan Timur', 'Kalimantan Selatan', 'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Utara', 'A', 'L1', 'mudah', 'Wawasan Nusantara', 'IKN terletak di Kab. Penajam Paser Utara & Kutai Kartanegara, Kaltim.'],
            [3, 'essay', 'Jelaskan tahapan utama dalam proses respirasi aerob pada sel makhluk hidup!', '', '', '', '', '', 'Glikolisis, Dekarboksilasi Oksidatif, Siklus Krebs, dan Rantai Transpor Elektron.', 'L3', 'sulit', 'Biologi Sel & Metabolisme', 'Skor penuh jika menyebutkan 4 tahapan dan lokasi terjadinya.'],
            [4, 'tf', 'Pancasila disahkan sebagai dasar negara Indonesia pada tanggal 18 Agustus 1945.', '', '', '', '', '', 'B', 'L1', 'mudah', 'Pendidikan Pancasila', 'Disahkan oleh PPKI pada tanggal 18 Agustus 1945.']
        ];

        $r = 2;
        foreach ($samples as $row) {
            $col = 'A';
            foreach ($row as $val) {
                $sheet->setCellValue($col . $r, $val);
                $col++;
            }
            $r++;
        }

        foreach (range('A', 'M') as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Import_Soal_CBT.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import Butir Soal dari File Excel (.xlsx / .csv) ke Wadah Bank Soal
     */
    public static function importExcelSoal($pdo)
    {
        $info = self::getAccessInfo($pdo);
        $id_bank = (int)($_POST['id_bank'] ?? 0);

        $stmt_bank = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
        $stmt_bank->execute([$id_bank]);
        $bank = $stmt_bank->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            $_SESSION['pesan_error'] = "Wadah Bank Soal tidak ditemukan.";
            redirect(BASE_URL . 'index.php?mod=cbt_bank_soal');
            return;
        }

        $can_edit = $info['is_admin'] || in_array((int)$bank['id_mapel'], $info['mapel_ids']) || ($bank['id_user'] == $info['user_id']);
        if (!$can_edit) {
            $_SESSION['pesan_error'] = "Akses ditolak: Anda tidak memiliki wewenang mengelola bank soal ini.";
            redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
            return;
        }

        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['pesan_error'] = "Silakan pilih file Excel (.xlsx / .csv) yang valid untuk diunggah.";
            redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
            return;
        }

        $fileTmp = $_FILES['file_excel']['tmp_name'];
        $fileName = $_FILES['file_excel']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            $_SESSION['pesan_error'] = "Format file tidak didukung. Harap unggah file dengan format .xlsx, .xls, atau .csv.";
            redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
            return;
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmp);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $stmt_max = $pdo->prepare("SELECT COALESCE(MAX(nomor_urut), 0) FROM cbt_soal WHERE id_bank = ?");
            $stmt_max->execute([$id_bank]);
            $nomor_urut = (int)$stmt_max->fetchColumn();

            $imported_count = 0;
            $pdo->beginTransaction();

            foreach ($rows as $rowIndex => $r) {
                if ($rowIndex === 1) continue; // Skip header
                $pertanyaan = trim((string)($r['C'] ?? ''));
                if (empty($pertanyaan)) continue;

                $nomor_urut++;
                $tipe = strtolower(trim((string)($r['B'] ?? 'pg')));
                if (!in_array($tipe, ['pg', 'essay', 'tf', 'matching'])) $tipe = 'pg';

                $kunci = trim((string)($r['I'] ?? ''));
                $level = strtoupper(trim((string)($r['J'] ?? 'L2')));
                if (!in_array($level, ['L1', 'L2', 'L3'])) $level = 'L2';

                $kesulitan = strtolower(trim((string)($r['K'] ?? 'sedang')));
                if (!in_array($kesulitan, ['mudah', 'sedang', 'sulit'])) $kesulitan = 'sedang';

                $topik = trim((string)($r['L'] ?? ''));
                $pembahasan = trim((string)($r['M'] ?? ''));
                $bobot = ($tipe === 'essay') ? 10 : 1;

                $stmt_s = $pdo->prepare("
                    INSERT INTO cbt_soal 
                    (id_bank, lingkup_materi, level_kognitif, nomor_urut, tipe_soal, pertanyaan, is_acak_soal, is_acak_opsi, bobot, kunci_jawaban, tingkat_kesulitan, pembahasan, media_tipe) 
                    VALUES (?, ?, ?, ?, ?, ?, 1, 1, ?, ?, ?, ?, 'none')
                ");
                $stmt_s->execute([
                    $id_bank, $topik, $level, $nomor_urut, $tipe, $pertanyaan, $bobot, $kunci, $kesulitan, $pembahasan
                ]);
                $id_soal = $pdo->lastInsertId();

                if ($tipe === 'pg') {
                    $kunci_upper = strtoupper($kunci);
                    foreach (['A' => 'D', 'B' => 'E', 'C' => 'F', 'D' => 'G', 'E' => 'H'] as $lbl => $col) {
                        $val = trim((string)($r[$col] ?? ''));
                        if ($val !== '') {
                            $is_benar = ($lbl === $kunci_upper) ? 1 : 0;
                            $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, ?, ?, ?)")
                                ->execute([$id_soal, $lbl, $val, $is_benar]);
                        }
                    }
                } elseif ($tipe === 'tf') {
                    $kunci_tf = strtoupper($kunci) === 'S' ? 'S' : 'B';
                    $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, 'B', 'BENAR', ?)")
                        ->execute([$id_soal, ($kunci_tf === 'B' ? 1 : 0)]);
                    $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, 'S', 'SALAH', ?)")
                        ->execute([$id_soal, ($kunci_tf === 'S' ? 1 : 0)]);
                }

                $imported_count++;
            }

            $pdo->commit();
            $_SESSION['pesan_sukses'] = "Sukses! Sebanyak $imported_count butir soal berhasil diimport dari file Excel ke dalam Bank Soal.";
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $_SESSION['pesan_error'] = "Gagal memproses file Excel: " . $e->getMessage();
        }

        redirect(BASE_URL . "index.php?mod=cbt_bank_soal&act=detail&id_bank=$id_bank");
    }

    /**
     * Preview Ekstraksi Soal Dokumen Word (.docx) via Gemini AI
     */
    private static function importWordPreview($pdo)
    {
        set_time_limit(180);
        header('Content-Type: application/json');
        require_once __DIR__ . '/../models/CbtDocxParser.php';

        $info = self::getAccessInfo($pdo);
        $id_bank = (int)($_POST['id_bank'] ?? 0);
        $id_cp   = !empty($_POST['id_cp']) ? (int)$_POST['id_cp'] : null;
        $id_tp   = !empty($_POST['id_tp']) ? (int)$_POST['id_tp'] : null;

        $stmt_bank = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
        $stmt_bank->execute([$id_bank]);
        $bank = $stmt_bank->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            echo json_encode(['status' => 'error', 'message' => 'Wadah Bank Soal tidak ditemukan.']);
            exit;
        }

        $can_edit = $info['is_admin'] || in_array((int)$bank['id_mapel'], $info['mapel_ids']) || ($bank['id_user'] == $info['user_id']);
        if (!$can_edit) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: Anda tidak memiliki wewenang mengelola bank soal ini.']);
            exit;
        }

        if (!isset($_FILES['file_docx']) || $_FILES['file_docx']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Silakan pilih file Word (.docx) yang valid untuk diunggah.']);
            exit;
        }

        $fileTmp = $_FILES['file_docx']['tmp_name'];
        $fileName = $_FILES['file_docx']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($ext !== 'docx') {
            echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung. Harap unggah file Microsoft Word berformat .docx.']);
            exit;
        }

        $contextData = [
            'nama_mapel' => $bank['nama_mapel'] ?? 'Mata Pelajaran',
            'tingkat'    => $bank['tingkat'] ?? 'SMA',
            'id_cp'      => $id_cp,
            'id_tp'      => $id_tp
        ];

        $result = CbtDocxParser::parseDocxWithAI($pdo, $fileTmp, $contextData);

        if (!$result['success']) {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
            exit;
        }

        echo json_encode([
            'status'     => 'success',
            'message'    => $result['message'],
            'total_soal' => $result['total_soal'],
            'soal'       => $result['soal']
        ]);
        exit;
    }

    /**
     * Simpan Hasil Review Butir Soal Dokumen Word ke Database
     */
    private static function importWordSave($pdo)
    {
        header('Content-Type: application/json');
        $info = self::getAccessInfo($pdo);

        $input = json_decode(file_get_contents('php://input'), true);
        $id_bank = (int)($input['id_bank'] ?? 0);
        $soal_list = $input['soal'] ?? [];

        if (!$id_bank || empty($soal_list) || !is_array($soal_list)) {
            echo json_encode(['status' => 'error', 'message' => 'Data butir soal kosong atau format tidak valid.']);
            exit;
        }

        $stmt_bank = $pdo->prepare("SELECT b.*, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE b.id_bank = ?");
        $stmt_bank->execute([$id_bank]);
        $bank = $stmt_bank->fetch(PDO::FETCH_ASSOC);

        if (!$bank) {
            echo json_encode(['status' => 'error', 'message' => 'Wadah Bank Soal tidak ditemukan.']);
            exit;
        }

        $can_edit = $info['is_admin'] || in_array((int)$bank['id_mapel'], $info['mapel_ids']) || ($bank['id_user'] == $info['user_id']);
        if (!$can_edit) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt_max = $pdo->prepare("SELECT COALESCE(MAX(nomor_urut), 0) FROM cbt_soal WHERE id_bank = ?");
            $stmt_max->execute([$id_bank]);
            $nomor_urut = (int)$stmt_max->fetchColumn();

            $saved_count = 0;

            foreach ($soal_list as $item) {
                $pertanyaan = trim($item['pertanyaan'] ?? '');
                if (empty($pertanyaan)) continue;

                $nomor_urut++;
                $tipe = strtolower(trim($item['tipe_soal'] ?? 'pg'));
                if (!in_array($tipe, ['pg', 'essay', 'tf', 'matching'])) $tipe = 'pg';

                $kunci = trim((string)($item['kunci_jawaban'] ?? ''));
                $level = strtoupper(trim($item['level_kognitif'] ?? 'L2'));
                if (!in_array($level, ['L1', 'L2', 'L3'])) $level = 'L2';

                $kesulitan = strtolower(trim($item['tingkat_kesulitan'] ?? 'sedang'));
                if (!in_array($kesulitan, ['mudah', 'sedang', 'sulit'])) $kesulitan = 'sedang';

                $stimulus = trim($item['stimulus'] ?? '');
                $lingkup_materi = trim($item['lingkup_materi'] ?? '');
                $indikator_soal = trim($item['indikator_soal'] ?? '');
                $pembahasan = trim($item['pembahasan'] ?? '');
                $id_cp = !empty($item['id_cp']) ? (int)$item['id_cp'] : null;
                $id_tp = !empty($item['id_tp']) ? (int)$item['id_tp'] : null;
                $bobot = ($tipe === 'essay') ? 10 : 1;

                $stmt_s = $pdo->prepare("
                    INSERT INTO cbt_soal 
                    (id_bank, id_cp, id_tp, lingkup_materi, indikator_soal, level_kognitif, stimulus, nomor_urut, tipe_soal, pertanyaan, is_acak_soal, is_acak_opsi, bobot, kunci_jawaban, tingkat_kesulitan, pembahasan, media_tipe) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, ?, ?, ?, ?, 'none')
                ");
                $stmt_s->execute([
                    $id_bank, $id_cp, $id_tp, $lingkup_materi, $indikator_soal, $level, $stimulus, $nomor_urut, $tipe, $pertanyaan, $bobot, $kunci, $kesulitan, $pembahasan
                ]);
                $id_soal = $pdo->lastInsertId();

                if ($tipe === 'pg' && !empty($item['opsi']) && is_array($item['opsi'])) {
                    $kunci_upper = strtoupper($kunci);
                    foreach ($item['opsi'] as $op) {
                        $lbl = strtoupper(trim($op['label'] ?? ''));
                        $txt = trim($op['teks'] ?? ($op['isi_opsi'] ?? ''));
                        if ($lbl !== '' && $txt !== '') {
                            $is_benar = (!empty($op['is_benar']) || $lbl === $kunci_upper) ? 1 : 0;
                            $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, ?, ?, ?)")
                                ->execute([$id_soal, $lbl, $txt, $is_benar]);
                        }
                    }
                } elseif ($tipe === 'tf') {
                    $kunci_tf = (strtoupper($kunci) === 'S' || strtoupper($kunci) === 'SALAH') ? 'S' : 'B';
                    $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, 'B', 'BENAR', ?)")
                        ->execute([$id_soal, ($kunci_tf === 'B' ? 1 : 0)]);
                    $pdo->prepare("INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar) VALUES (?, 'S', 'SALAH', ?)")
                        ->execute([$id_soal, ($kunci_tf === 'S' ? 1 : 0)]);
                }

                $saved_count++;
            }

            $pdo->commit();
            $_SESSION['pesan_sukses'] = "Sukses! Sebanyak $saved_count butir soal hasil ekstraksi Word berhasil disimpan ke Bank Soal.";
            echo json_encode([
                'status'  => 'success',
                'message' => "Berhasil menyimpan $saved_count butir soal ke dalam Bank Soal.",
                'count'   => $saved_count
            ]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan butir soal: ' . $e->getMessage()]);
        }
        exit;
    }

    // ====================================================
    // PAKET SOAL & STUDIO PERAKITAN NASKAH
    // ====================================================
    public static function paket($pdo, $act)
    {
        $info = self::getAccessInfo($pdo);

        if ($act === 'store') {
            $nama_paket     = trim($_POST['nama_paket'] ?? '');
            $id_bank        = (int)($_POST['id_bank'] ?? 0);
            $id_mapel       = (int)($_POST['id_mapel'] ?? 0);
            $id_ta          = (int)($_POST['id_ta'] ?? 0);
            $jenis_asesmen  = trim($_POST['jenis_asesmen'] ?? 'Sumatif Akhir Semester (SAS)');
            $semester       = trim($_POST['semester'] ?? 'Ganjil');
            $tingkat        = trim($_POST['tingkat'] ?? 'X');
            $alokasi_waktu  = trim($_POST['alokasi_waktu'] ?? '90 Menit');
            $petunjuk_umum  = trim($_POST['petunjuk_umum'] ?? '');
            $penyusun       = trim($_POST['penyusun'] ?? '');
            $jml_pg         = (int)($_POST['jml_soal_pg'] ?? 0);
            $jml_essay      = (int)($_POST['jml_soal_essay'] ?? 0);
            $acak_soal      = isset($_POST['acak_soal']) ? 1 : 0;
            $acak_opsi      = isset($_POST['acak_opsi']) ? 1 : 0;
            $keterangan     = trim($_POST['keterangan'] ?? '');
            $id_user        = $info['user_id'];

            if (!$info['is_admin'] && !empty($info['mapel_ids']) && !in_array($id_mapel, $info['mapel_ids'])) {
                $_SESSION['pesan_error'] = "Anda tidak memiliki wewenang pada mapel paket ini.";
                redirect(BASE_URL . 'index.php?mod=cbt_paket');
                return;
            }

            if (!$id_ta) {
                $stmt_ta = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1");
                $id_ta = (int)$stmt_ta->fetchColumn();
            }

            $stmt = $pdo->prepare("
                INSERT INTO cbt_paket 
                (nama_paket, id_bank, id_mapel, id_ta, jenis_asesmen, semester, tingkat, alokasi_waktu, petunjuk_umum, penyusun, jml_soal_pg, jml_soal_essay, acak_soal, acak_opsi, keterangan, id_user) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $nama_paket, $id_bank, $id_mapel, $id_ta, $jenis_asesmen, $semester, $tingkat, $alokasi_waktu, $petunjuk_umum, $penyusun, $jml_pg, $jml_essay, $acak_soal, $acak_opsi, $keterangan, $id_user
            ]);
            $new_id = $pdo->lastInsertId();

            // Otomatis masukkan butir soal dari bank soal ke dalam cbt_paket_soal jika tersedia
            if ($id_bank > 0) {
                $stmt_soal = $pdo->prepare("SELECT id_soal, bobot FROM cbt_soal WHERE id_bank = ? ORDER BY nomor_urut ASC, id_soal ASC");
                $stmt_soal->execute([$id_bank]);
                $all_soal = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);
                
                $stmt_ins = $pdo->prepare("INSERT INTO cbt_paket_soal (id_paket, id_soal, nomor_urut, bobot_soal) VALUES (?, ?, ?, ?)");
                foreach ($all_soal as $idx => $s) {
                    $stmt_ins->execute([$new_id, $s['id_soal'], $idx + 1, $s['bobot'] ?? 1]);
                }
            }

            $_SESSION['pesan_sukses'] = "Paket soal berhasil dibuat. Silakan kelola naskah di Studio Perakitan Soal.";
            redirect(BASE_URL . 'index.php?mod=cbt_paket&act=builder&id_paket=' . $new_id);
        } elseif ($act === 'builder') {
            $id_paket = (int)($_GET['id_paket'] ?? 0);
            $stmt_p = $pdo->prepare("
                SELECT p.*, m.nama_mapel, b.nama_bank, ta.nama_ta as tahun_ajaran
                FROM cbt_paket p
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN cbt_bank_soal b ON p.id_bank = b.id_bank
                LEFT JOIN tahun_ajaran ta ON p.id_ta = ta.id_ta
                WHERE p.id_paket = ?
            ");
            $stmt_p->execute([$id_paket]);
            $paket = $stmt_p->fetch(PDO::FETCH_ASSOC);

            if (!$paket) {
                $_SESSION['pesan_error'] = "Paket soal tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_paket');
                return;
            }

            $title = "Studio Perakitan Naskah - " . htmlspecialchars($paket['nama_paket']);

            // 1. Ambil Gudang Soal (Koleksi Soal dari Bank Soal sumber atau dari Mapel yang sama)
            $stmt_gudang = $pdo->prepare("
                SELECT s.*, 
                       b.tingkat as bank_tingkat,
                       cp.fase as fase_cp, cp.deskripsi_cp,
                       tp.kode_tp, tp.deskripsi_tp, tp.materi as tp_materi,
                       (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                FROM cbt_soal s
                JOIN cbt_bank_soal b ON s.id_bank = b.id_bank
                LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
                WHERE s.id_bank = ? OR s.id_bank IN (SELECT id_bank FROM cbt_bank_soal WHERE id_mapel = ?)
                ORDER BY s.id_soal DESC
            ");
            $stmt_gudang->execute([(int)$paket['id_bank'], (int)$paket['id_mapel']]);
            $gudang_soal = $stmt_gudang->fetchAll(PDO::FETCH_ASSOC);

            // Ambil Daftar CP & TP untuk mapel paket ini
            $stmt_cp = $pdo->prepare("SELECT id_cp, deskripsi_cp, fase FROM capaian_pembelajaran WHERE id_mapel = ? ORDER BY fase ASC, id_cp ASC");
            $stmt_cp->execute([(int)$paket['id_mapel']]);
            $cp_list = $stmt_cp->fetchAll(PDO::FETCH_ASSOC);

            $stmt_tp = $pdo->prepare("SELECT id_tp, id_cp, kode_tp, deskripsi_tp, materi FROM tujuan_pembelajaran WHERE id_mapel = ? ORDER BY kode_tp ASC");
            $stmt_tp->execute([(int)$paket['id_mapel']]);
            $tp_list = $stmt_tp->fetchAll(PDO::FETCH_ASSOC);

            // 2. Ambil Soal yang sudah terpilih di Naskah Paket Soal
            $stmt_selected = $pdo->prepare("
                SELECT ps.id_paket_soal, ps.nomor_urut, ps.bobot_soal,
                       s.id_soal, s.tipe_soal, s.tingkat_kesulitan, s.level_kognitif, s.lingkup_materi, s.indikator_soal, s.pertanyaan, s.bobot,
                       (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                FROM cbt_paket_soal ps
                JOIN cbt_soal s ON ps.id_soal = s.id_soal
                WHERE ps.id_paket = ?
                ORDER BY ps.nomor_urut ASC
            ");
            $stmt_selected->execute([$id_paket]);
            $selected_soal = $stmt_selected->fetchAll(PDO::FETCH_ASSOC);

            // Jika cbt_paket_soal masih kosong tapi id_bank punya soal, gunakan soal dari id_bank sebagai initial
            if (empty($selected_soal) && !empty($gudang_soal)) {
                foreach ($gudang_soal as $idx => $gs) {
                    if ($gs['id_bank'] == $paket['id_bank']) {
                        $selected_soal[] = [
                            'id_soal' => $gs['id_soal'],
                            'nomor_urut' => $idx + 1,
                            'bobot_soal' => $gs['bobot'],
                            'tipe_soal' => $gs['tipe_soal'],
                            'tingkat_kesulitan' => $gs['tingkat_kesulitan'],
                            'level_kognitif' => $gs['level_kognitif'],
                            'lingkup_materi' => $gs['lingkup_materi'],
                            'indikator_soal' => $gs['indikator_soal'],
                            'pertanyaan' => $gs['pertanyaan'],
                            'kunci_pg' => $gs['kunci_pg']
                        ];
                    }
                }
            }

            require_once __DIR__ . '/../views/cbt_paket_builder.php';
        } elseif ($act === 'save_builder') {
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            $id_paket = (int)($input['id_paket'] ?? 0);
            $items = $input['items'] ?? [];

            if (!$id_paket) {
                echo json_encode(['status' => 'error', 'message' => 'ID Paket tidak valid.']);
                exit;
            }

            try {
                $pdo->beginTransaction();

                // Hapus data perakitan lama
                $stmt_del = $pdo->prepare("DELETE FROM cbt_paket_soal WHERE id_paket = ?");
                $stmt_del->execute([$id_paket]);

                // Masukkan item-item baru dengan nomor urut
                $stmt_ins = $pdo->prepare("INSERT INTO cbt_paket_soal (id_paket, id_soal, nomor_urut, bobot_soal) VALUES (?, ?, ?, ?)");
                $pg_count = 0;
                $essay_count = 0;

                foreach ($items as $it) {
                    $id_s = (int)($it['id_soal'] ?? 0);
                    $no   = (int)($it['nomor_urut'] ?? 1);
                    $bbt  = (float)($it['bobot_soal'] ?? 1);

                    if ($id_s > 0) {
                        $stmt_ins->execute([$id_paket, $id_s, $no, $bbt]);

                        // Hitung tipe
                        $stmt_tp = $pdo->prepare("SELECT tipe_soal FROM cbt_soal WHERE id_soal = ?");
                        $stmt_tp->execute([$id_s]);
                        $t_val = $stmt_tp->fetchColumn();
                        if ($t_val === 'pg') $pg_count++;
                        elseif ($t_val === 'essay') $essay_count++;
                    }
                }

                // Update jumlah pada cbt_paket
                $stmt_upd = $pdo->prepare("UPDATE cbt_paket SET jml_soal_pg = ?, jml_soal_essay = ? WHERE id_paket = ?");
                $stmt_upd->execute([$pg_count, $essay_count, $id_paket]);

                $pdo->commit();
                echo json_encode(['status' => 'ok', 'message' => 'Naskah paket berhasil disimpan dengan ' . count($items) . ' butir soal.']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
            }
            exit;
        } elseif (in_array($act, ['print_naskah', 'print_kisi_kisi', 'print_kartu_soal', 'print_kunci'])) {
            $id_paket = (int)($_GET['id_paket'] ?? 0);
            $stmt_p = $pdo->prepare("
                SELECT p.*, m.nama_mapel, b.nama_bank, ta.nama_ta as tahun_ajaran,
                       COALESCE(NULLIF(p.penyusun, ''), g.nama, 'Guru Mata Pelajaran') as nama_guru,
                       COALESCE(g.nuptk, g.nik, '-') as nip_guru
                FROM cbt_paket p
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN cbt_bank_soal b ON p.id_bank = b.id_bank
                LEFT JOIN tahun_ajaran ta ON p.id_ta = ta.id_ta
                LEFT JOIN guru g ON p.id_user = g.id_pengguna
                WHERE p.id_paket = ?
            ");
            $stmt_p->execute([$id_paket]);
            $paket = $stmt_p->fetch(PDO::FETCH_ASSOC);

            if (!$paket) {
                die("Paket soal tidak ditemukan.");
            }

            // Data Sekolah
            $sekolah = ProfilSekolahModel::getProfil($pdo) ?: [];

            // Ambil daftar butir soal naskah
            $stmt_soal = $pdo->prepare("
                SELECT s.*, ps.nomor_urut as urut_paket, ps.bobot_soal,
                       cp.fase as fase_cp, cp.deskripsi_cp,
                       tp.kode_tp, tp.deskripsi_tp,
                       (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                FROM cbt_paket_soal ps
                JOIN cbt_soal s ON ps.id_soal = s.id_soal
                LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
                WHERE ps.id_paket = ?
                ORDER BY ps.nomor_urut ASC
            ");
            $stmt_soal->execute([$id_paket]);
            $soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

            // Jika di cbt_paket_soal belum ada item, fallback ambil langsung dari cbt_soal bank
            if (empty($soal_list) && !empty($paket['id_bank'])) {
                $stmt_fallback = $pdo->prepare("
                    SELECT s.*, s.nomor_urut as urut_paket, s.bobot as bobot_soal,
                           cp.fase as fase_cp, cp.deskripsi_cp,
                           tp.kode_tp, tp.deskripsi_tp,
                           (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                    FROM cbt_soal s
                    LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                    LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
                    WHERE s.id_bank = ?
                    ORDER BY s.nomor_urut ASC, s.id_soal ASC
                ");
                $stmt_fallback->execute([(int)$paket['id_bank']]);
                $soal_list = $stmt_fallback->fetchAll(PDO::FETCH_ASSOC);
            }

            // Ambil opsi untuk tiap soal
            foreach ($soal_list as &$s) {
                $stmt_ops = $pdo->prepare("SELECT * FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC");
                $stmt_ops->execute([$s['id_soal']]);
                $s['opsi_list'] = $stmt_ops->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($s);

            if ($act === 'print_naskah') {
                require_once __DIR__ . '/../views/cbt_print_naskah.php';
            } elseif ($act === 'print_kisi_kisi') {
                require_once __DIR__ . '/../views/cbt_print_kisi_kisi.php';
            } elseif ($act === 'print_kartu_soal') {
                require_once __DIR__ . '/../views/cbt_print_kartu.php';
            } elseif ($act === 'print_kunci') {
                require_once __DIR__ . '/../views/cbt_print_kunci.php';
            }
            exit;
        } elseif ($act === 'preview_siswa') {
            $id_paket = (int)($_GET['id_paket'] ?? 0);
            $stmt_p = $pdo->prepare("
                SELECT p.*, m.nama_mapel, b.nama_bank, ta.nama_ta as tahun_ajaran
                FROM cbt_paket p
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN cbt_bank_soal b ON p.id_bank = b.id_bank
                LEFT JOIN tahun_ajaran ta ON p.id_ta = ta.id_ta
                WHERE p.id_paket = ?
            ");
            $stmt_p->execute([$id_paket]);
            $paket = $stmt_p->fetch(PDO::FETCH_ASSOC);

            if (!$paket) {
                $_SESSION['pesan_error'] = "Paket soal tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_paket');
                return;
            }

            // Ambil daftar butir soal naskah
            $stmt_soal = $pdo->prepare("
                SELECT s.*, ps.nomor_urut as urut_paket, ps.bobot_soal,
                       cp.fase as fase_cp, cp.deskripsi_cp,
                       tp.kode_tp, tp.deskripsi_tp,
                       (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                FROM cbt_paket_soal ps
                JOIN cbt_soal s ON ps.id_soal = s.id_soal
                LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
                WHERE ps.id_paket = ?
                ORDER BY ps.nomor_urut ASC
            ");
            $stmt_soal->execute([$id_paket]);
            $soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

            // Jika di cbt_paket_soal belum ada item, fallback ambil langsung dari cbt_soal bank
            if (empty($soal_list) && !empty($paket['id_bank'])) {
                $stmt_fallback = $pdo->prepare("
                    SELECT s.*, s.nomor_urut as urut_paket, s.bobot as bobot_soal,
                           cp.fase as fase_cp, cp.deskripsi_cp,
                           tp.kode_tp, tp.deskripsi_tp,
                           (SELECT label FROM cbt_soal_opsi WHERE id_soal = s.id_soal AND is_benar = 1 LIMIT 1) as kunci_pg
                    FROM cbt_soal s
                    LEFT JOIN capaian_pembelajaran cp ON s.id_cp = cp.id_cp
                    LEFT JOIN tujuan_pembelajaran tp ON s.id_tp = tp.id_tp
                    WHERE s.id_bank = ?
                    ORDER BY s.nomor_urut ASC, s.id_soal ASC
                ");
                $stmt_fallback->execute([(int)$paket['id_bank']]);
                $soal_list = $stmt_fallback->fetchAll(PDO::FETCH_ASSOC);
            }

            foreach ($soal_list as &$s) {
                $stmt_ops = $pdo->prepare("SELECT * FROM cbt_soal_opsi WHERE id_soal = ? ORDER BY label ASC");
                $stmt_ops->execute([$s['id_soal']]);
                $s['opsi_list'] = $stmt_ops->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($s);

            $title = "Simulasi Tampilan Siswa - " . htmlspecialchars($paket['nama_paket']);
            require_once __DIR__ . '/../views/cbt_preview_siswa.php';
            exit;
        } elseif ($act === 'toggle_serentak') {
            $id_paket = (int)($_GET['id_paket'] ?? 0);
            $stmt_p = $pdo->prepare("SELECT id_paket, id_user, id_mapel, is_siap_serentak, status_verifikasi FROM cbt_paket WHERE id_paket = ?");
            $stmt_p->execute([$id_paket]);
            $p = $stmt_p->fetch(PDO::FETCH_ASSOC);

            if (!$p) {
                $_SESSION['pesan_error'] = "Paket soal tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_paket');
                return;
            }

            if (!$info['is_admin'] && $p['id_user'] != $info['user_id'] && !in_array((int)$p['id_mapel'], $info['mapel_ids'])) {
                $_SESSION['pesan_error'] = "Akses ditolak.";
                redirect(BASE_URL . 'index.php?mod=cbt_paket');
                return;
            }

            $current = (int)($p['is_siap_serentak'] ?? 0);
            $new_val = ($current === 1) ? 0 : 1;
            $new_status = ($new_val === 1) ? 'siap' : 'draft';

            $stmt_upd = $pdo->prepare("UPDATE cbt_paket SET is_siap_serentak = ?, status_verifikasi = ? WHERE id_paket = ?");
            $stmt_upd->execute([$new_val, $new_status, $id_paket]);

            $_SESSION['pesan_sukses'] = ($new_val === 1) 
                ? "Naskah paket berhasil ditandai SIAP DIGUNAKAN UJIAN SERENTAK (SAS/SAT/STS)." 
                : "Status naskah paket dikembalikan menjadi DRAFT MANDIRI GURU.";

            redirect(BASE_URL . 'index.php?mod=cbt_paket');
            return;
        } elseif ($act === 'delete') {
            $id_paket = (int)($_GET['id_paket'] ?? 0);

            if (!$info['is_admin']) {
                $check = $pdo->prepare("SELECT id_user, id_mapel FROM cbt_paket WHERE id_paket = ?");
                $check->execute([$id_paket]);
                $p = $check->fetch(PDO::FETCH_ASSOC);
                if (!$p || ($p['id_user'] != $info['user_id'] && !in_array((int)$p['id_mapel'], $info['mapel_ids']))) {
                    $_SESSION['pesan_error'] = "Akses ditolak.";
                    redirect(BASE_URL . 'index.php?mod=cbt_paket');
                    return;
                }
            }

            $pdo->prepare("DELETE FROM cbt_paket_soal WHERE id_paket = ?")->execute([$id_paket]);
            $pdo->prepare("DELETE FROM cbt_paket WHERE id_paket = ?")->execute([$id_paket]);
            $_SESSION['pesan_sukses'] = "Paket soal berhasil dihapus.";
            redirect(BASE_URL . 'index.php?mod=cbt_paket');
        } else {
            $title = "Paket Soal CBT";
            $filter_p = self::buildMapelFilter($info, 'p');
            $active_tab = $_GET['tab'] ?? ($info['is_admin'] ? 'all' : 'my_paket');

            $where_extra = "";
            if ($active_tab === 'serentak') {
                $where_extra = " AND (p.is_siap_serentak = 1 OR p.status_verifikasi IN ('siap', 'terverifikasi'))";
            } elseif ($active_tab === 'mandiri') {
                $where_extra = " AND (p.is_siap_serentak = 0 OR p.is_siap_serentak IS NULL)";
            } elseif ($active_tab === 'my_paket' && !$info['is_admin']) {
                $where_extra = " AND p.id_user = " . (int)$info['user_id'];
            }

            $stmt_p = $pdo->prepare("
                SELECT p.*, m.nama_mapel, b.nama_bank,
                       (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = p.id_bank) as total_tersedia,
                       (SELECT COUNT(*) FROM cbt_paket_soal WHERE id_paket = p.id_paket) as total_dirakit
                FROM cbt_paket p
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN cbt_bank_soal b ON p.id_bank = b.id_bank
                WHERE {$filter_p['clause']} $where_extra
                ORDER BY p.id_paket DESC
            ");
            $stmt_p->execute($filter_p['params']);
            $paket_list = $stmt_p->fetchAll(PDO::FETCH_ASSOC);

            // Bank list & Mapel list terisolasi
            $filter_b = self::buildMapelFilter($info, 'b');
            $stmt_b = $pdo->prepare("SELECT b.id_bank, b.nama_bank, b.id_mapel, b.tingkat, m.nama_mapel FROM cbt_bank_soal b LEFT JOIN mapel m ON b.id_mapel = m.id_mapel WHERE {$filter_b['clause']} ORDER BY b.nama_bank");
            $stmt_b->execute($filter_b['params']);
            $bank_list = $stmt_b->fetchAll(PDO::FETCH_ASSOC);

            $mapel_list = $info['is_admin'] ? LmsModel::getAllMapel($pdo) : LmsModel::getMapelByGuru($pdo, $info['id_guru']);

            require_once __DIR__ . '/../views/cbt_paket.php';
        }
    }

    // ====================================================
    // AGENDA UJIAN
    // ====================================================
    public static function jadwal($pdo, $act)
    {
        $info = self::getAccessInfo($pdo);

        if ($act === 'store') {
            $nama_ujian      = trim($_POST['nama_ujian'] ?? '');
            $id_paket        = (int)($_POST['id_paket'] ?? 0);
            $id_kelas        = (int)($_POST['id_kelas'] ?? 0);
            $id_kelas_multi  = $_POST['id_kelas_multi'] ?? [];
            $tgl_mulai       = $_POST['tanggal_mulai'] ?? date('Y-m-d H:i:s');
            $tgl_selesai     = $_POST['tanggal_selesai'] ?? date('Y-m-d H:i:s', strtotime('+2 hours'));
            $durasi_menit    = (int)($_POST['durasi_menit'] ?? 60);
            $pin_proktor     = strtoupper(trim($_POST['pin_proktor'] ?? ''));
            if (empty($pin_proktor)) {
                $pin_proktor = strtoupper(substr(md5(rand()), 0, 6));
            }
            $status          = $_POST['status'] ?? 'draft';
            $passing_grade   = (int)($_POST['passing_grade'] ?? 75);
            $tampilkan_nilai = isset($_POST['tampilkan_nilai']) ? (int)$_POST['tampilkan_nilai'] : 1;
            $id_user         = $info['user_id'];

            // Jika multi-kelas dipilih (Admin Mode)
            if (!empty($id_kelas_multi) && is_array($id_kelas_multi)) {
                $created_count = 0;
                foreach ($id_kelas_multi as $k_id) {
                    $k_id = (int)$k_id;
                    if ($k_id > 0) {
                        $stmt = $pdo->prepare("INSERT INTO cbt_jadwal (nama_ujian, id_paket, id_kelas, tanggal_mulai, tanggal_selesai, durasi_menit, pin_proktor, status, passing_grade, tampilkan_nilai, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$nama_ujian, $id_paket, $k_id, $tgl_mulai, $tgl_selesai, $durasi_menit, $pin_proktor, $status, $passing_grade, $tampilkan_nilai, $id_user]);
                        $new_jadwal_id = $pdo->lastInsertId();

                        // Auto-generate peserta untuk kelas tersebut
                        $siswa_list = $pdo->prepare("
                            SELECT s.id_siswa 
                            FROM siswa s 
                            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa 
                            JOIN tahun_ajaran ta ON ps.id_ta = ta.id_ta 
                            WHERE ta.status = 'Aktif' AND ps.id_kelas = ? AND ps.status_penempatan = 'Aktif'
                        ");
                        $siswa_list->execute([$k_id]);
                        foreach ($siswa_list->fetchAll(PDO::FETCH_ASSOC) as $s) {
                            $token = strtoupper(substr(md5(rand() . $s['id_siswa']), 0, 6));
                            $pdo->prepare("INSERT INTO cbt_peserta (id_jadwal, id_siswa, token, status) VALUES (?, ?, ?, 'belum')")
                                ->execute([$new_jadwal_id, $s['id_siswa'], $token]);
                        }
                        $created_count++;
                    }
                }
                $_SESSION['pesan_sukses'] = "Berhasil membuat agenda ujian serentak untuk $created_count kelas sekaligus!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO cbt_jadwal (nama_ujian, id_paket, id_kelas, tanggal_mulai, tanggal_selesai, durasi_menit, pin_proktor, status, passing_grade, tampilkan_nilai, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama_ujian, $id_paket, $id_kelas, $tgl_mulai, $tgl_selesai, $durasi_menit, $pin_proktor, $status, $passing_grade, $tampilkan_nilai, $id_user]);
                $new_jadwal_id = $pdo->lastInsertId();

                // Auto-generate peserta
                if ($id_kelas > 0) {
                    $siswa_list = $pdo->prepare("
                        SELECT s.id_siswa 
                        FROM siswa s 
                        JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa 
                        JOIN tahun_ajaran ta ON ps.id_ta = ta.id_ta 
                        WHERE ta.status = 'Aktif' AND ps.id_kelas = ? AND ps.status_penempatan = 'Aktif'
                    ");
                    $siswa_list->execute([$id_kelas]);
                    foreach ($siswa_list->fetchAll(PDO::FETCH_ASSOC) as $s) {
                        $token = strtoupper(substr(md5(rand() . $s['id_siswa']), 0, 6));
                        $pdo->prepare("INSERT INTO cbt_peserta (id_jadwal, id_siswa, token, status) VALUES (?, ?, ?, 'belum')")
                            ->execute([$new_jadwal_id, $s['id_siswa'], $token]);
                    }
                }

                $_SESSION['pesan_sukses'] = "Jadwal ujian berhasil dibuat. Token/PIN: $pin_proktor";
            }

            redirect(BASE_URL . 'index.php?mod=cbt_jadwal');
        } elseif ($act === 'toggle') {
            $id = (int)($_GET['id_jadwal'] ?? 0);
            $s = $pdo->prepare("SELECT status FROM cbt_jadwal WHERE id_jadwal = ?");
            $s->execute([$id]);
            $cur = $s->fetchColumn();
            $new = ($cur === 'aktif') ? 'selesai' : 'aktif';
            $pdo->prepare("UPDATE cbt_jadwal SET status = ? WHERE id_jadwal = ?")->execute([$new, $id]);
            redirect(BASE_URL . 'index.php?mod=cbt_jadwal');
        } elseif ($act === 'toggle_nilai') {
            $id = (int)($_GET['id_jadwal'] ?? 0);
            $s = $pdo->prepare("SELECT tampilkan_nilai FROM cbt_jadwal WHERE id_jadwal = ?");
            $s->execute([$id]);
            $cur = (int)$s->fetchColumn();
            $new = ($cur === 1) ? 0 : 1;
            $pdo->prepare("UPDATE cbt_jadwal SET tampilkan_nilai = ? WHERE id_jadwal = ?")->execute([$new, $id]);
            $_SESSION['pesan_sukses'] = ($new === 1) ? "Nilai ujian sekarang DITAMPILKAN ke siswa." : "Nilai ujian sekarang DISEMBUNYIKAN dari siswa.";
            redirect(BASE_URL . 'index.php?mod=cbt_jadwal');
            return;
        } elseif ($act === 'refresh_token') {
            $id = (int)($_GET['id_jadwal'] ?? 0);
            $new_pin = strtoupper(substr(md5(rand()), 0, 6));
            $pdo->prepare("UPDATE cbt_jadwal SET pin_proktor = ? WHERE id_jadwal = ?")->execute([$new_pin, $id]);
            $_SESSION['pesan_sukses'] = "Token Proktor baru berhasil dirilis: $new_pin";
            if (!empty($_GET['ref']) && $_GET['ref'] === 'proktor') {
                redirect(BASE_URL . "index.php?mod=cbt_peserta&act=live_proktor&id_jadwal=$id");
            } else {
                redirect(BASE_URL . 'index.php?mod=cbt_jadwal');
            }
            return;
        } elseif ($act === 'delete') {
            $id = (int)($_GET['id_jadwal'] ?? 0);

            if (!$info['is_admin']) {
                $check = $pdo->prepare("SELECT j.id_user, p.id_mapel FROM cbt_jadwal j JOIN cbt_paket p ON j.id_paket = p.id_paket WHERE j.id_jadwal = ?");
                $check->execute([$id]);
                $j = $check->fetch(PDO::FETCH_ASSOC);
                if (!$j || ($j['id_user'] != $info['user_id'] && !in_array((int)$j['id_mapel'], $info['mapel_ids']))) {
                    $_SESSION['pesan_error'] = "Akses ditolak.";
                    redirect(BASE_URL . 'index.php?mod=cbt_jadwal');
                    return;
                }
            }

            $pdo->prepare("DELETE FROM cbt_nilai WHERE id_jadwal = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM cbt_jawaban WHERE id_jadwal = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM cbt_peserta WHERE id_jadwal = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM cbt_jadwal WHERE id_jadwal = ?")->execute([$id]);
            $_SESSION['pesan_sukses'] = "Jadwal ujian berhasil dihapus.";
            redirect(BASE_URL . 'index.php?mod=cbt_jadwal');
        } else {
            $title = "Agenda Ujian CBT";
            $filter_j = self::buildMapelFilter($info, 'p');

            $stmt_j = $pdo->prepare("
                SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal) as total_peserta
                FROM cbt_jadwal j
                LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                WHERE {$filter_j['clause']}
                ORDER BY j.id_jadwal DESC
            ");
            $stmt_j->execute($filter_j['params']);
            $jadwal_list = $stmt_j->fetchAll(PDO::FETCH_ASSOC);

            // Paket list terisolasi
            $stmt_p = $pdo->prepare("SELECT p.id_paket, p.nama_paket, m.nama_mapel, p.is_siap_serentak, p.status_verifikasi FROM cbt_paket p LEFT JOIN mapel m ON p.id_mapel = m.id_mapel WHERE {$filter_j['clause']} ORDER BY p.is_siap_serentak DESC, p.nama_paket ASC");
            $stmt_p->execute($filter_j['params']);
            $paket_list = $stmt_p->fetchAll(PDO::FETCH_ASSOC);

            // Kelas list terisolasi
            $kelas_list = $info['is_admin']
                ? $pdo->query("SELECT k.id_kelas, k.nama_kelas FROM kelas k JOIN tahun_ajaran ta ON k.id_ta = ta.id_ta WHERE ta.status='Aktif' ORDER BY k.nama_kelas")->fetchAll(PDO::FETCH_ASSOC)
                : LmsModel::getRombelByGuru($pdo, $info['id_guru']);

            require_once __DIR__ . '/../views/cbt_jadwal.php';
        }
    }

    // ====================================================
    // PESERTA UJIAN & ADMINISTRASI PANITIA
    // ====================================================
    public static function peserta($pdo, $act)
    {
        $info = self::getAccessInfo($pdo);
        $title = "Peserta Ujian CBT";
        $id_jadwal = (int)($_GET['id_jadwal'] ?? 0);

        if ($act === 'generate') {
            $jadwal = $pdo->prepare("SELECT id_kelas FROM cbt_jadwal WHERE id_jadwal = ?");
            $jadwal->execute([$id_jadwal]);
            $jadwal = $jadwal->fetch(PDO::FETCH_ASSOC);

            if ($jadwal && !empty($jadwal['id_kelas'])) {
                $siswa_list = $pdo->prepare("
                    SELECT s.id_siswa 
                    FROM siswa s 
                    JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa 
                    JOIN tahun_ajaran ta ON ps.id_ta = ta.id_ta 
                    WHERE ta.status = 'Aktif' AND ps.id_kelas = ? AND ps.status_penempatan = 'Aktif'
                ");
                $siswa_list->execute([$jadwal['id_kelas']]);
                $siswa_list = $siswa_list->fetchAll(PDO::FETCH_ASSOC);

                $inserted = 0;
                foreach ($siswa_list as $s) {
                    $chk = $pdo->prepare("SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = ? AND id_siswa = ?");
                    $chk->execute([$id_jadwal, $s['id_siswa']]);
                    if ($chk->fetchColumn() == 0) {
                        $token = strtoupper(substr(md5(rand() . $s['id_siswa']), 0, 6));
                        $pdo->prepare("INSERT INTO cbt_peserta (id_jadwal, id_siswa, token, status) VALUES (?, ?, ?, 'belum')")
                            ->execute([$id_jadwal, $s['id_siswa'], $token]);
                        $inserted++;
                    }
                }
                $_SESSION['pesan_sukses'] = "$inserted peserta berhasil digenerate.";
            } else {
                $_SESSION['pesan_error'] = "Jadwal ujian belum memilih rombel/kelas spesifik.";
            }
            redirect(BASE_URL . "index.php?mod=cbt_peserta&id_jadwal=$id_jadwal");
        } elseif ($act === 'unlock') {
            $id = (int)($_GET['id_peserta'] ?? 0);
            $pdo->prepare("UPDATE cbt_peserta SET status = 'mengerjakan', waktu_selesai = NULL WHERE id_peserta = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM cbt_nilai WHERE id_peserta = ?")->execute([$id]);
            $_SESSION['pesan_sukses'] = "Akses ujian peserta dibuka kembali. Siswa dapat login dan melanjutkan pengerjaan soal.";
            if (!empty($_GET['ref']) && $_GET['ref'] === 'proktor') {
                redirect(BASE_URL . "index.php?mod=cbt_peserta&act=live_proktor&id_jadwal=$id_jadwal");
            } else {
                redirect(BASE_URL . "index.php?mod=cbt_peserta&id_jadwal=$id_jadwal");
            }
        } elseif ($act === 'reset') {
            $id = (int)($_GET['id_peserta'] ?? 0);
            $pdo->prepare("UPDATE cbt_peserta SET status = 'belum', waktu_mulai = NULL, waktu_selesai = NULL WHERE id_peserta = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM cbt_jawaban WHERE id_peserta = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM cbt_nilai WHERE id_peserta = ?")->execute([$id]);
            $_SESSION['pesan_sukses'] = "Status pengerjaan peserta berhasil direset total (mulai dari awal).";
            if (!empty($_GET['ref']) && $_GET['ref'] === 'proktor') {
                redirect(BASE_URL . "index.php?mod=cbt_peserta&act=live_proktor&id_jadwal=$id_jadwal");
            } else {
                redirect(BASE_URL . "index.php?mod=cbt_peserta&id_jadwal=$id_jadwal");
            }
        } elseif ($act === 'create_susulan') {
            // 1-Click Generate Ujian Susulan bagi siswa yang absen / belum ujian
            $stj = $pdo->prepare("SELECT * FROM cbt_jadwal WHERE id_jadwal = ?");
            $stj->execute([$id_jadwal]);
            $parent_jadwal = $stj->fetch(PDO::FETCH_ASSOC);

            if (!$parent_jadwal) {
                $_SESSION['pesan_error'] = "Jadwal utama tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_peserta');
                return;
            }

            // Cari peserta yang statusnya belum ujian / absen
            $stmt_absen = $pdo->prepare("SELECT id_siswa FROM cbt_peserta WHERE id_jadwal = ? AND status = 'belum'");
            $stmt_absen->execute([$id_jadwal]);
            $siswa_absen = $stmt_absen->fetchAll(PDO::FETCH_COLUMN);

            if (empty($siswa_absen)) {
                $_SESSION['pesan_error'] = "Tidak ada siswa yang absen / belum ujian pada agenda ini.";
                redirect(BASE_URL . "index.php?mod=cbt_peserta&id_jadwal=$id_jadwal");
                return;
            }

            $nama_susulan = "[SUSULAN] " . $parent_jadwal['nama_ujian'];
            $new_pin = strtoupper(substr(md5(rand()), 0, 6));
            $tgl_mulai = date('Y-m-d H:i:s', strtotime('+1 day 08:00:00'));
            $tgl_selesai = date('Y-m-d H:i:s', strtotime('+1 day 10:00:00'));

            $stmt_ins = $pdo->prepare("
                INSERT INTO cbt_jadwal (nama_ujian, id_paket, id_kelas, tanggal_mulai, tanggal_selesai, durasi_menit, pin_proktor, status, passing_grade, tampilkan_nilai, catatan, id_user)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'aktif', ?, ?, 'Ujian Susulan Resmi Panitia', ?)
            ");
            $stmt_ins->execute([
                $nama_susulan, $parent_jadwal['id_paket'], $parent_jadwal['id_kelas'], $tgl_mulai, $tgl_selesai,
                $parent_jadwal['durasi_menit'], $new_pin, $parent_jadwal['passing_grade'], $parent_jadwal['tampilkan_nilai'], $info['user_id']
            ]);
            $new_jadwal_id = $pdo->lastInsertId();

            foreach ($siswa_absen as $s_id) {
                $token = strtoupper(substr(md5(rand() . $s_id), 0, 6));
                $pdo->prepare("INSERT INTO cbt_peserta (id_jadwal, id_siswa, token, status) VALUES (?, ?, ?, 'belum')")
                    ->execute([$new_jadwal_id, $s_id, $token]);
            }

            $_SESSION['pesan_sukses'] = "Agenda Ujian Susulan berhasil dibuat untuk " . count($siswa_absen) . " siswa yang absen. Token: $new_pin";
            redirect(BASE_URL . "index.php?mod=cbt_peserta&id_jadwal=$new_jadwal_id");
            return;
        } elseif (in_array($act, ['print_kartu', 'print_hadir', 'print_berita_acara', 'live_proktor'])) {
            if (!$id_jadwal) {
                die("Jadwal ujian belum dipilih.");
            }

            $stj = $pdo->prepare("
                SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas, p.semester
                FROM cbt_jadwal j 
                LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket 
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas 
                WHERE j.id_jadwal = ?
            ");
            $stj->execute([$id_jadwal]);
            $jadwal = $stj->fetch(PDO::FETCH_ASSOC);

            if (!$jadwal) {
                die("Jadwal tidak ditemukan.");
            }

            $sekolah = ProfilSekolahModel::getProfil($pdo) ?: [];

            $stp = $pdo->prepare("
                SELECT cp.*, s.nama as nama_siswa, s.nisn, s.nipd, s.jk, s.foto, k.nama_kelas,
                       n.nilai_pg, n.nilai_essay, n.nilai_akhir,
                       (SELECT COUNT(*) FROM cbt_log_aktivitas WHERE id_peserta = cp.id_peserta AND jenis_log IN ('pindah_tab', 'fullscreen_exit')) as total_pelanggaran,
                       (SELECT COUNT(*) FROM cbt_jawaban WHERE id_peserta = cp.id_peserta) as total_dijawab
                FROM cbt_peserta cp
                JOIN siswa s ON cp.id_siswa = s.id_siswa
                LEFT JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                LEFT JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
                WHERE cp.id_jadwal = ?
                ORDER BY s.nama ASC
            ");
            $stp->execute([$id_jadwal]);
            $peserta_list = $stp->fetchAll(PDO::FETCH_ASSOC);

            if ($act === 'print_kartu') {
                require_once __DIR__ . '/../views/cbt_print_kartu_peserta.php';
            } elseif ($act === 'print_hadir') {
                require_once __DIR__ . '/../views/cbt_print_daftar_hadir.php';
            } elseif ($act === 'print_berita_acara') {
                require_once __DIR__ . '/../views/cbt_print_berita_acara.php';
            } elseif ($act === 'live_proktor') {
                $count_mengerjakan = 0;
                $count_selesai = 0;
                $count_pelanggaran = 0;
                $count_belum = 0;

                foreach ($peserta_list as $p) {
                    $st = strtolower($p['status'] ?? 'belum');
                    if ($p['total_pelanggaran'] > 0) $count_pelanggaran++;
                    if ($st === 'mengerjakan') $count_mengerjakan++;
                    elseif ($st === 'selesai') $count_selesai++;
                    else $count_belum++;
                }

                require_once __DIR__ . '/../views/cbt_proktor.php';
            }
            exit;
        } else {
            $filter_j = self::buildMapelFilter($info, 'p');
            $stmt_jl = $pdo->prepare("
                SELECT j.id_jadwal, j.nama_ujian, j.tanggal_mulai, j.tanggal_selesai, j.durasi_menit, j.pin_proktor, j.status,
                       p.nama_paket, m.nama_mapel, k.nama_kelas,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal) as total_peserta,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal AND status = 'selesai') as total_selesai,
                       (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal AND status IN ('mengerjakan', 'berlangsung')) as total_mengerjakan
                FROM cbt_jadwal j 
                LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket 
                LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                WHERE {$filter_j['clause']} 
                ORDER BY j.id_jadwal DESC
            ");
            $stmt_jl->execute($filter_j['params']);
            $jadwal_list = $stmt_jl->fetchAll(PDO::FETCH_ASSOC);

            // Auto-select jadwal pertama jika id_jadwal belum dipilih agar halaman tidak kosong
            if (!$id_jadwal && !empty($jadwal_list)) {
                $id_jadwal = (int)$jadwal_list[0]['id_jadwal'];
            }

            $jadwal_aktif = null;
            $peserta_list = [];

            if ($id_jadwal) {
                $stj = $pdo->prepare("
                    SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas 
                    FROM cbt_jadwal j 
                    LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket 
                    LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
                    LEFT JOIN kelas k ON j.id_kelas = k.id_kelas 
                    WHERE j.id_jadwal = ?
                ");
                $stj->execute([$id_jadwal]);
                $jadwal_aktif = $stj->fetch(PDO::FETCH_ASSOC);

                $stp = $pdo->prepare("
                    SELECT cp.*, s.nama as nama_siswa, s.nisn, s.nipd, s.jk, k.nama_kelas, n.nilai_pg, n.nilai_essay, n.nilai_akhir
                    FROM cbt_peserta cp
                    JOIN siswa s ON cp.id_siswa = s.id_siswa
                    LEFT JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
                    LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                    LEFT JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
                    WHERE cp.id_jadwal = ?
                    ORDER BY s.nama ASC
                ");
                $stp->execute([$id_jadwal]);
                $peserta_list = $stp->fetchAll(PDO::FETCH_ASSOC);
            }

            require_once __DIR__ . '/../views/cbt_peserta.php';
        }
    }

    // ====================================================
    // HASIL UJIAN, REMEDIAL & ANALISIS BUTIR
    // ====================================================
    public static function hasil($pdo)
    {
        $info = self::getAccessInfo($pdo);
        $title = "Hasil & Nilai CBT";
        $id_jadwal = (int)($_GET['id_jadwal'] ?? 0);
        $act = $_GET['act'] ?? 'index';

        if ($act === 'create_remedial' && $id_jadwal > 0) {
            // 1-Click Generate Ujian Remedial bagi siswa dengan nilai < KKM
            $stj = $pdo->prepare("SELECT * FROM cbt_jadwal WHERE id_jadwal = ?");
            $stj->execute([$id_jadwal]);
            $parent_jadwal = $stj->fetch(PDO::FETCH_ASSOC);

            if (!$parent_jadwal) {
                $_SESSION['pesan_error'] = "Jadwal utama tidak ditemukan.";
                redirect(BASE_URL . 'index.php?mod=cbt_hasil');
                return;
            }

            $kkm = (float)($parent_jadwal['passing_grade'] ?? 75);

            // Ambil siswa yang nilai akhirnya di bawah KKM atau belum tuntas
            $stmt_rem = $pdo->prepare("
                SELECT cp.id_siswa
                FROM cbt_peserta cp
                JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
                WHERE cp.id_jadwal = ? AND (n.nilai_akhir < ? OR n.nilai_akhir IS NULL)
            ");
            $stmt_rem->execute([$id_jadwal, $kkm]);
            $siswa_remedial = $stmt_rem->fetchAll(PDO::FETCH_COLUMN);

            if (empty($siswa_remedial)) {
                $_SESSION['pesan_error'] = "Seluruh siswa telah mencapai KKM / tuntas. Tidak ada peserta remedial.";
                redirect(BASE_URL . "index.php?mod=cbt_hasil&id_jadwal=$id_jadwal");
                return;
            }

            $nama_remedial = "[REMEDIAL] " . $parent_jadwal['nama_ujian'];
            $new_pin = strtoupper(substr(md5(rand()), 0, 6));
            $tgl_mulai = date('Y-m-d H:i:s');
            $tgl_selesai = date('Y-m-d H:i:s', strtotime('+2 days'));

            $stmt_ins = $pdo->prepare("
                INSERT INTO cbt_jadwal (nama_ujian, id_paket, id_kelas, tanggal_mulai, tanggal_selesai, durasi_menit, pin_proktor, status, passing_grade, tampilkan_nilai, catatan, id_user)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'aktif', ?, ?, 'Sesi Pembelajaran Remedial Guru', ?)
            ");
            $stmt_ins->execute([
                $nama_remedial, $parent_jadwal['id_paket'], $parent_jadwal['id_kelas'], $tgl_mulai, $tgl_selesai,
                $parent_jadwal['durasi_menit'], $new_pin, $parent_jadwal['passing_grade'], $parent_jadwal['tampilkan_nilai'], $info['user_id']
            ]);
            $new_jadwal_id = $pdo->lastInsertId();

            foreach ($siswa_remedial as $s_id) {
                $token = strtoupper(substr(md5(rand() . $s_id), 0, 6));
                $pdo->prepare("INSERT INTO cbt_peserta (id_jadwal, id_siswa, token, status) VALUES (?, ?, ?, 'belum')")
                    ->execute([$new_jadwal_id, $s_id, $token]);
            }

            $_SESSION['pesan_sukses'] = "Sesi Ujian Remedial berhasil dibuka untuk " . count($siswa_remedial) . " siswa di bawah KKM. Token: $new_pin";
            redirect(BASE_URL . "index.php?mod=cbt_peserta&id_jadwal=$new_jadwal_id");
            return;
        } elseif ($act === 'analisis_butir' || $act === 'print_analisis_butir') {
            if (!$id_jadwal) {
                die("Jadwal ujian belum dipilih.");
            }

            $stj = $pdo->prepare("SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas FROM cbt_jadwal j LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket LEFT JOIN mapel m ON p.id_mapel = m.id_mapel LEFT JOIN kelas k ON j.id_kelas = k.id_kelas WHERE j.id_jadwal = ?");
            $stj->execute([$id_jadwal]);
            $jadwal = $stj->fetch(PDO::FETCH_ASSOC);

            if (!$jadwal) {
                die("Jadwal tidak ditemukan.");
            }

            $sekolah = ProfilSekolahModel::getProfil($pdo) ?: [];

            // Ambil seluruh butir soal dari paket
            $stmt_soal = $pdo->prepare("
                SELECT s.id_soal, ps.nomor_urut, s.tipe_soal, s.kunci_jawaban, s.pertanyaan
                FROM cbt_paket_soal ps
                JOIN cbt_soal s ON ps.id_soal = s.id_soal
                WHERE ps.id_paket = ?
                ORDER BY ps.nomor_urut ASC
            ");
            $stmt_soal->execute([(int)$jadwal['id_paket']]);
            $soal_list = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

            // Ambil seluruh peserta yang sudah selesai (sampel analisis)
            $stmt_peserta = $pdo->prepare("
                SELECT cp.id_peserta, n.nilai_akhir
                FROM cbt_peserta cp
                JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
                WHERE cp.id_jadwal = ? AND cp.status = 'selesai'
                ORDER BY n.nilai_akhir DESC
            ");
            $stmt_peserta->execute([$id_jadwal]);
            $sampel_peserta = $stmt_peserta->fetchAll(PDO::FETCH_ASSOC);

            $total_peserta = (int)($pdo->query("SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = $id_jadwal")->fetchColumn() ?: 0);
            $total_sampel = count($sampel_peserta);

            // Bagi kelompok atas 27% dan bawah 27%
            $n_kelompok = max(1, (int)round($total_sampel * 0.27));
            $kelompok_atas = array_slice(array_column($sampel_peserta, 'id_peserta'), 0, $n_kelompok);
            $kelompok_bawah = array_slice(array_column($sampel_peserta, 'id_peserta'), -$n_kelompok);

            $analisis_data = [];

            foreach ($soal_list as $s) {
                $id_soal = (int)$s['id_soal'];
                $kunci = strtoupper(trim($s['kunci_jawaban'] ?? 'A'));

                // Hitung sebaran jawaban A, B, C, D, E
                $stmt_jwb = $pdo->prepare("
                    SELECT UPPER(TRIM(jawaban_pg)) as jwb, COUNT(*) as cnt
                    FROM cbt_jawaban
                    WHERE id_jadwal = ? AND id_soal = ?
                    GROUP BY UPPER(TRIM(jawaban_pg))
                ");
                $stmt_jwb->execute([$id_jadwal, $id_soal]);
                $dist_map = [];
                foreach ($stmt_jwb->fetchAll(PDO::FETCH_ASSOC) as $d) {
                    $dist_map[$d['jwb']] = (int)$d['cnt'];
                }

                // Hitung benar total
                $stmt_benar = $pdo->prepare("SELECT COUNT(*) FROM cbt_jawaban WHERE id_jadwal = ? AND id_soal = ? AND is_benar = 1");
                $stmt_benar->execute([$id_jadwal, $id_soal]);
                $jml_benar = (int)$stmt_benar->fetchColumn();

                // Tingkat Kesukaran P = B / N
                $p_index = $total_sampel > 0 ? ($jml_benar / $total_sampel) : 0;
                $p_kategori = $p_index > 0.70 ? 'Mudah' : ($p_index >= 0.30 ? 'Sedang' : 'Sukar');

                // Daya Pembeda D = (B_A - B_B) / n
                $b_atas = 0;
                $b_bawah = 0;
                if (!empty($kelompok_atas)) {
                    $in_atas = implode(',', array_map('intval', $kelompok_atas));
                    $b_atas = (int)($pdo->query("SELECT COUNT(*) FROM cbt_jawaban WHERE id_soal = $id_soal AND id_peserta IN ($in_atas) AND is_benar = 1")->fetchColumn() ?: 0);
                }
                if (!empty($kelompok_bawah)) {
                    $in_bawah = implode(',', array_map('intval', $kelompok_bawah));
                    $b_bawah = (int)($pdo->query("SELECT COUNT(*) FROM cbt_jawaban WHERE id_soal = $id_soal AND id_peserta IN ($in_bawah) AND is_benar = 1")->fetchColumn() ?: 0);
                }

                $d_index = $n_kelompok > 0 ? (($b_atas - $b_bawah) / $n_kelompok) : 0;
                $d_kategori = $d_index >= 0.40 ? 'Sangat Baik' : ($d_index >= 0.30 ? 'Baik' : ($d_index >= 0.20 ? 'Cukup' : 'Jelek'));

                $rekomendasi = ($d_index >= 0.30) ? 'Diterima' : (($d_index >= 0.20 || $p_index >= 0.30) ? 'Direvisi' : 'Dibuang');

                $analisis_data[] = [
                    'nomor_urut'     => $s['nomor_urut'],
                    'tipe_soal'      => $s['tipe_soal'],
                    'kunci_jawaban'  => $kunci,
                    'dist_a'         => $dist_map['A'] ?? 0,
                    'dist_b'         => $dist_map['B'] ?? 0,
                    'dist_c'         => $dist_map['C'] ?? 0,
                    'dist_d'         => $dist_map['D'] ?? 0,
                    'dist_e'         => $dist_map['E'] ?? 0,
                    'p_index'        => $p_index,
                    'p_kategori'     => $p_kategori,
                    'd_index'        => $d_index,
                    'd_kategori'     => $d_kategori,
                    'rekomendasi'    => $rekomendasi
                ];
            }

            require_once __DIR__ . '/../views/cbt_print_analisis_butir.php';
            exit;
        }

        $filter_j = self::buildMapelFilter($info, 'p');
        $stmt_jl = $pdo->prepare("
            SELECT j.id_jadwal, j.nama_ujian, j.tanggal_mulai, m.nama_mapel, k.nama_kelas, j.passing_grade,
                   (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal) as total_peserta,
                   (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal AND status = 'selesai') as total_selesai
            FROM cbt_jadwal j
            LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket
            LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
            LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
            WHERE {$filter_j['clause']}
            ORDER BY j.id_jadwal DESC
        ");
        $stmt_jl->execute($filter_j['params']);
        $jadwal_list = $stmt_jl->fetchAll(PDO::FETCH_ASSOC);

        if (!$id_jadwal && !empty($jadwal_list)) {
            $id_jadwal = (int)$jadwal_list[0]['id_jadwal'];
        }

        if ($act === 'export_excel' && $id_jadwal > 0) {
            self::exportExcelHasil($pdo, $id_jadwal);
            return;
        }

        $hasil_list = [];
        $jadwal_aktif = null;

        if ($id_jadwal) {
            $stj = $pdo->prepare("SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas FROM cbt_jadwal j LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket LEFT JOIN mapel m ON p.id_mapel = m.id_mapel LEFT JOIN kelas k ON j.id_kelas = k.id_kelas WHERE j.id_jadwal = ?");
            $stj->execute([$id_jadwal]);
            $jadwal_aktif = $stj->fetch(PDO::FETCH_ASSOC);

            $sth = $pdo->prepare("
                SELECT cp.*, s.nama as nama_siswa, s.nisn, s.nipd, s.jk, k.nama_kelas,
                       n.nilai_pg, n.nilai_essay, n.nilai_akhir, n.status_lulus
                FROM cbt_peserta cp
                JOIN siswa s ON cp.id_siswa = s.id_siswa
                LEFT JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
                LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
                LEFT JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
                WHERE cp.id_jadwal = ?
                ORDER BY (n.nilai_akhir IS NULL), n.nilai_akhir DESC, s.nama ASC
            ");
            $sth->execute([$id_jadwal]);
            $hasil_list = $sth->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once __DIR__ . '/../views/cbt_hasil.php';
    }

    /**
     * Export Rekapitulasi Nilai Asesmen CBT ke Format Excel (.xlsx)
     */
    public static function exportExcelHasil($pdo, $id_jadwal)
    {
        if (ob_get_level()) ob_end_clean();

        $stj = $pdo->prepare("SELECT j.*, p.nama_paket, m.nama_mapel, k.nama_kelas FROM cbt_jadwal j LEFT JOIN cbt_paket p ON j.id_paket = p.id_paket LEFT JOIN mapel m ON p.id_mapel = m.id_mapel LEFT JOIN kelas k ON j.id_kelas = k.id_kelas WHERE j.id_jadwal = ?");
        $stj->execute([$id_jadwal]);
        $jadwal = $stj->fetch(PDO::FETCH_ASSOC);

        if (!$jadwal) {
            die("Jadwal ujian tidak ditemukan.");
        }

        $sth = $pdo->prepare("
            SELECT cp.*, s.nama as nama_siswa, s.nisn, s.nipd, s.jk, k.nama_kelas,
                   n.nilai_pg, n.nilai_essay, n.nilai_akhir, n.status_lulus
            FROM cbt_peserta cp
            JOIN siswa s ON cp.id_siswa = s.id_siswa
            LEFT JOIN cbt_jadwal j ON cp.id_jadwal = j.id_jadwal
            LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
            LEFT JOIN cbt_nilai n ON cp.id_peserta = n.id_peserta
            WHERE cp.id_jadwal = ?
            ORDER BY (n.nilai_akhir IS NULL), n.nilai_akhir DESC, s.nama ASC
        ");
        $sth->execute([$id_jadwal]);
        $hasil_list = $sth->fetchAll(PDO::FETCH_ASSOC);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Rekap Nilai CBT");

        // Title Block
        $sheet->setCellValue('A1', 'REKAPITULASI HASIL ASESMEN BERBASIS KOMPUTER (CBT)');
        $sheet->setCellValue('A2', 'SMA PLUS AL-MANSHURIYAH');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);

        $sheet->setCellValue('A4', 'Nama Ujian: ' . $jadwal['nama_ujian']);
        $sheet->setCellValue('A5', 'Mata Pelajaran: ' . ($jadwal['nama_mapel'] ?? '-'));
        $sheet->setCellValue('E4', 'Kelas / Rombel: ' . ($jadwal['nama_kelas'] ?? '-'));
        $sheet->setCellValue('E5', 'KKM / Passing Grade: ' . ($jadwal['passing_grade'] ?? 75));
        $sheet->getStyle('A4:E5')->getFont()->setBold(true)->setSize(10);

        // Header Table
        $tableHeaders = [
            'A7' => 'Rank',
            'B7' => 'NISN',
            'C7' => 'NIPD',
            'D7' => 'Nama Peserta Didik',
            'E7' => 'L/P',
            'F7' => 'Status Pengerjaan',
            'G7' => 'Nilai PG',
            'H7' => 'Nilai Esai',
            'I7' => 'Nilai Akhir',
            'J7' => 'Ketuntasan (KKM ' . ($jadwal['passing_grade'] ?? 75) . ')'
        ];
        foreach ($tableHeaders as $cell => $txt) {
            $sheet->setCellValue($cell, $txt);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E1B4B']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A7:J7')->applyFromArray($headerStyle);
        $sheet->getRowDimension(7)->setRowHeight(26);

        $kkm = (float)($jadwal['passing_grade'] ?? 75);
        $r = 8;
        $rank = 1;
        foreach ($hasil_list as $h) {
            $st = strtolower($h['status'] ?? 'belum');
            $nilai_akhir = $h['nilai_akhir'] !== null ? (float)$h['nilai_akhir'] : null;
            $tuntas = ($nilai_akhir !== null && $nilai_akhir >= $kkm) ? 'TUNTAS' : ($nilai_akhir !== null ? 'BELUM TUNTAS' : '-');

            $sheet->setCellValue('A' . $r, $st === 'selesai' ? $rank++ : '-');
            $sheet->setCellValueExplicit('B' . $r, (string)($h['nisn'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $r, (string)($h['nipd'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $r, $h['nama_siswa']);
            $sheet->setCellValue('E' . $r, ($h['jk'] === 'L' || $h['jk'] === 'Laki-laki') ? 'L' : 'P');
            $sheet->setCellValue('F' . $r, ucfirst($st));
            $sheet->setCellValue('G' . $r, $h['nilai_pg'] !== null ? number_format((float)$h['nilai_pg'], 1) : '-');
            $sheet->setCellValue('H' . $r, $h['nilai_essay'] !== null ? number_format((float)$h['nilai_essay'], 1) : '-');
            $sheet->setCellValue('I' . $r, $nilai_akhir !== null ? number_format($nilai_akhir, 1) : '-');
            $sheet->setCellValue('J' . $r, $tuntas);

            if ($tuntas === 'TUNTAS') {
                $sheet->getStyle('J' . $r)->getFont()->getColor()->setRGB('16A34A');
                $sheet->getStyle('J' . $r)->getFont()->setBold(true);
            } elseif ($tuntas === 'BELUM TUNTAS') {
                $sheet->getStyle('J' . $r)->getFont()->getColor()->setRGB('DC2626');
                $sheet->getStyle('J' . $r)->getFont()->setBold(true);
            }
            $r++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_]/', '_', $jadwal['nama_ujian'] . '_' . ($jadwal['nama_kelas'] ?? ''));
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Rekap_Nilai_' . $safeName . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // ====================================================
    // PUSAT ADMINISTRASI & DOKUMEN UJIAN CBT
    // ====================================================
    public static function administrasi($pdo, $act = '')
    {
        $info = self::getAccessInfo($pdo);

        // 1. AJAX AI KISI-KISI GENERATOR
        if ($act === 'ai_kisi_kisi') {
            header('Content-Type: application/json');
            require_once __DIR__ . '/../models/AIModel.php';

            $mapel = trim($_POST['mapel'] ?? '');
            $jenis_ujian = trim($_POST['jenis_ujian'] ?? 'Sumatif Akhir Semester (SAS)');
            $komposisi = trim($_POST['komposisi'] ?? '30 PG, 5 Uraian');
            $materi = trim($_POST['materi'] ?? '');

            if (empty($mapel) || empty($materi)) {
                echo json_encode(['success' => false, 'message' => 'Mata pelajaran dan materi harus diisi.']);
                exit;
            }

            $prompt = "Buatkan Kisi-Kisi Soal Ujian format tabel / teks rapi untuk:\n"
                    . "- Mata Pelajaran: $mapel\n"
                    . "- Jenis Ujian: $jenis_ujian\n"
                    . "- Komposisi Soal: $komposisi\n"
                    . "- Topik / Lingkup Materi: $materi\n\n"
                    . "Format kolom kisi-kisi: No | Capaian Pembelajaran / TP | Materi Pokok | Indikator Soal | Level Kognitif (L1/L2/L3) | Bentuk Soal (PG/Uraian) | No Soal.\n"
                    . "Sertakan juga pedoman rubrik penskoran singkat.";

            $system_instruction = "Anda adalah Ahli Evaluasi & Asesmen Kurikulum Pendidikan Indonesia. Berikan hasil kisi-kisi yang terstruktur, rapi, dan siap digunakan oleh guru.";

            $res = AIModel::generate($pdo, $prompt, $system_instruction);
            if (isset($res['success']) && $res['success'] === false) {
                echo json_encode($res);
            } else {
                echo json_encode(['success' => true, 'result' => $res['text'] ?? $res['content'] ?? '']);
            }
            exit;
        }

        // 2. DOKUMEN CETAK KEPANITIAAN UMUM
        if (in_array($act, ['print_kartu_pengawas', 'print_kartu_panitia', 'print_hadir_pengawas', 'print_hadir_panitia', 'print_tata_tertib', 'print_label_meja'])) {
            require_once __DIR__ . '/../views/cbt_print_panitia_pengawas.php';
            exit;
        }

        // 3. AMBIL DATA UNTUK HUB VIEW
        $mapelFilter = self::buildMapelFilter($info, 'p');
        $stmt_pkt = $pdo->prepare("
            SELECT p.*, m.nama_mapel, b.nama_bank, g.nama as nama_guru,
                   (SELECT COUNT(*) FROM cbt_soal WHERE id_bank = p.id_bank) as total_soal
            FROM cbt_paket p
            LEFT JOIN mapel m ON p.id_mapel = m.id_mapel
            LEFT JOIN cbt_bank_soal b ON p.id_bank = b.id_bank
            LEFT JOIN guru g ON p.id_guru = g.id_guru
            WHERE {$mapelFilter['clause']}
            ORDER BY p.id_paket DESC
        ");
        $stmt_pkt->execute($mapelFilter['params']);
        $paket_list = $stmt_pkt->fetchAll(PDO::FETCH_ASSOC);

        // List Agenda / Jadwal Ujian
        $jadwalFilter = self::buildMapelFilter($info, 'j');
        $stmt_jdw = $pdo->prepare("
            SELECT j.*, m.nama_mapel, k.nama_kelas,
                   (SELECT COUNT(*) FROM cbt_peserta WHERE id_jadwal = j.id_jadwal) as total_peserta
            FROM cbt_jadwal j
            LEFT JOIN mapel m ON j.id_mapel = m.id_mapel
            LEFT JOIN kelas k ON j.id_kelas = k.id_kelas
            WHERE {$jadwalFilter['clause']}
            ORDER BY j.tgl_mulai DESC, j.id_jadwal DESC
        ");
        $stmt_jdw->execute($jadwalFilter['params']);
        $jadwal_list = $stmt_jdw->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/cbt_administrasi.php';
    }
}

