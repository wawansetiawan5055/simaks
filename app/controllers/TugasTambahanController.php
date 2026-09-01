<?php
// app/controllers/TugasTambahanController.php

require_once __DIR__ . '/../models/TahunAjaranModel.php';

class TugasTambahanController
{
    // Mapping nama resmi jabatan
    private static function get_jabatan_title($jenis)
    {
        $map = [
            'kurikulum'     => 'Waka Kurikulum',
            'kesiswaan'     => 'Waka Kesiswaan',
            'walas'         => 'Wali Kelas',
            'humas'         => 'Waka Humas',
            'bk'            => 'Bimbingan Konseling',
            'kepala_lab'    => 'Kepala Laboratorium',
            'kepala_perpus' => 'Kepala Perpustakaan',
            'sarpras'       => 'Waka Sarpras',
            'uks'           => 'Pembina UKS',
            'pembina_uks'   => 'Pembina UKS'
        ];
        return $map[$jenis] ?? ucfirst(str_replace('_', ' ', $jenis));
    }

    // Mapping kategori per jabatan sesuai kebutuhan
    private static function get_categories($jenis)
    {
        $categories = [
            'kurikulum'     => ['Program Kerja', 'KOSP', 'Kalender Pendidikan', 'SK Pembagian Tugas', 'Lainnya'],
            'kesiswaan'     => ['Program Kerja', 'Tata Tertib', 'Lainnya'],
            'walas'         => ['Program Kerja', 'Inventaris Kelas', 'Lainnya'],
            'humas'         => ['Program Kerja', 'MoU', 'Lainnya'],
            'bk'            => ['Program Kerja', 'Lainnya'],
            'kepala_lab'    => ['Program Kerja', 'SK Petugas Lab', 'Lainnya'],
            'kepala_perpus' => ['Program Kerja', 'SK Petugas Perpus', 'Lainnya'],
            'sarpras'       => ['Program Kerja', 'Inventaris Barang', 'Laporan', 'Lainnya'],
            'uks'           => ['Program Kerja', 'SK Petugas UKS', 'Laporan', 'Lainnya'],
            'pembina_uks'   => ['Program Kerja', 'SK Petugas UKS', 'Laporan', 'Lainnya']
        ];

        return $categories[$jenis] ?? ['Program Kerja', 'Laporan', 'Lainnya'];
    }

    public static function index($pdo)
    {
        $jenis = $_GET['jenis'] ?? ($_GET['act'] !== 'index' && !empty($_GET['act']) ? $_GET['act'] : '');
        $id_ta = (int)($_GET['id_ta'] ?? $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0);
        if (!$id_ta) {
            $ta_row = $pdo->query("SELECT id_ta FROM tahun_ajaran WHERE status='Aktif' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            $id_ta = (int)($ta_row['id_ta'] ?? 7);
        }

        if (empty($jenis)) {
            $_SESSION['pesan_error'] = "Jenis tugas tambahan tidak ditentukan.";
            redirect('dashboard');
            return;
        }

        $is_admin = in_array('Admin', user_roles());
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        $jabatan_name = self::get_jabatan_title($jenis);

        // --- CEK HAK AKSES ---
        // Admin selalu boleh. Untuk Guru/GTK, harus memiliki hak akses atau penugasan pada jabatan ini
        if (!$is_admin) {
            $has_access = can_do($pdo, 'tugas_tambahan/' . $jenis, 'read') 
                       || can_do($pdo, 'tugas_tambahan', 'read')
                       || in_array($jabatan_name, user_roles())
                       || in_array(str_replace('Waka ', '', $jabatan_name), user_roles());

            // Cek di tabel penugasan_jabatan atau penugasan_wali_kelas jika ada
            if (!$has_access && $id_guru > 0) {
                if ($jenis === 'walas') {
                    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM penugasan_wali_kelas WHERE id_guru = ? AND id_ta = ?");
                    $stmt_check->execute([$id_guru, $id_ta]);
                    $has_access = ($stmt_check->fetchColumn() > 0);
                } else {
                    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM penugasan_jabatan WHERE id_guru = ? AND id_ta = ? AND (jenis_jabatan LIKE ? OR jenis_jabatan LIKE ?)");
                    $stmt_check->execute([$id_guru, $id_ta, '%' . $jabatan_name . '%', '%' . $jenis . '%']);
                    $has_access = ($stmt_check->fetchColumn() > 0);
                }
            }

            if (!$has_access) {
                $_SESSION['pesan_error'] = "Akses ditolak: Anda tidak memiliki wewenang untuk membuka modul " . $jabatan_name . ".";
                redirect('dashboard');
                return;
            }
        }

        // --- AMBIL DATA DOKUMEN ---
        if ($is_admin) {
            $stmt = $pdo->prepare("SELECT d.*, g.nama as nama_guru 
                                   FROM dokumen_tugas_tambahan d 
                                   LEFT JOIN guru g ON d.id_guru = g.id_guru 
                                   WHERE d.jenis_tugas_tambahan = ? AND d.id_ta = ? 
                                   ORDER BY d.created_at DESC");
            $stmt->execute([$jenis, $id_ta]);
        } else {
            $stmt = $pdo->prepare("SELECT d.*, g.nama as nama_guru 
                                   FROM dokumen_tugas_tambahan d 
                                   LEFT JOIN guru g ON d.id_guru = g.id_guru 
                                   WHERE d.jenis_tugas_tambahan = ? AND (d.id_guru = ? OR d.id_guru = 0) AND d.id_ta = ? 
                                   ORDER BY d.created_at DESC");
            $stmt->execute([$jenis, $id_guru, $id_ta]);
        }
        $dokumen = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- AMBIL DATA AGENDA ---
        if ($is_admin) {
            $stmt_agenda = $pdo->prepare("SELECT a.*, g.nama as nama_guru 
                                          FROM agenda_tugas_tambahan a 
                                          LEFT JOIN guru g ON a.id_guru = g.id_guru 
                                          WHERE a.jenis_tugas_tambahan = ? AND a.id_ta = ? 
                                          ORDER BY a.tanggal ASC");
            $stmt_agenda->execute([$jenis, $id_ta]);
        } else {
            $stmt_agenda = $pdo->prepare("SELECT a.*, g.nama as nama_guru 
                                          FROM agenda_tugas_tambahan a 
                                          LEFT JOIN guru g ON a.id_guru = g.id_guru 
                                          WHERE a.jenis_tugas_tambahan = ? AND (a.id_guru = ? OR a.id_guru = 0) AND a.id_ta = ? 
                                          ORDER BY a.tanggal ASC");
            $stmt_agenda->execute([$jenis, $id_guru, $id_ta]);
        }
        $agendas = $stmt_agenda->fetchAll(PDO::FETCH_ASSOC);

        // --- AMBIL DATA GALERI ---
        if ($is_admin) {
            $stmt_galeri = $pdo->prepare("SELECT gal.*, g.nama as nama_guru 
                                          FROM galeri_tugas_tambahan gal 
                                          LEFT JOIN guru g ON gal.id_guru = g.id_guru 
                                          WHERE gal.jenis_tugas_tambahan = ? AND gal.id_ta = ? 
                                          ORDER BY gal.created_at DESC");
            $stmt_galeri->execute([$jenis, $id_ta]);
        } else {
            $stmt_galeri = $pdo->prepare("SELECT gal.*, g.nama as nama_guru 
                                          FROM galeri_tugas_tambahan gal 
                                          LEFT JOIN guru g ON gal.id_guru = g.id_guru 
                                          WHERE gal.jenis_tugas_tambahan = ? AND (gal.id_guru = ? OR gal.id_guru = 0) AND gal.id_ta = ? 
                                          ORDER BY gal.created_at DESC");
            $stmt_galeri->execute([$jenis, $id_guru, $id_ta]);
        }
        $galeri = $stmt_galeri->fetchAll(PDO::FETCH_ASSOC);

        // --- KHUSUS WALI KELAS: INVENTARIS ---
        $inventaris = [];
        $walas_kelas = null;
        if ($jenis === 'walas') {
            if ($is_admin) {
                $stmt_inv = $pdo->prepare("SELECT wi.*, g.nama as nama_guru 
                                           FROM walas_inventaris wi 
                                           LEFT JOIN guru g ON wi.id_guru = g.id_guru 
                                           WHERE wi.id_ta = ? ORDER BY wi.created_at DESC");
                $stmt_inv->execute([$id_ta]);
            } else {
                $stmt_inv = $pdo->prepare("SELECT * FROM walas_inventaris WHERE id_guru = ? AND id_ta = ? ORDER BY created_at DESC");
                $stmt_inv->execute([$id_guru, $id_ta]);
            }
            $inventaris = $stmt_inv->fetchAll(PDO::FETCH_ASSOC);

            // Ambil id_kelas untuk wali kelas ini
            $stmt_kelas = $pdo->prepare("SELECT k.id_kelas, k.nama_kelas 
                                         FROM kelas k 
                                         JOIN penugasan_wali_kelas pwk ON k.id_kelas = pwk.id_kelas 
                                         WHERE pwk.id_guru = ? AND k.id_ta = ?");
            $stmt_kelas->execute([$id_guru, $id_ta]);
            $walas_kelas = $stmt_kelas->fetch(PDO::FETCH_ASSOC);
        }

        // --- KHUSUS BK: JURNAL BK ---
        $jurnal_bk = [];
        if ($jenis === 'bk') {
            if ($is_admin) {
                $sql = "SELECT j.*, s.nama as nama_siswa, k.nama_kelas, g.nama as nama_guru 
                        FROM bk_jurnal j 
                        JOIN siswa s ON j.id_siswa = s.id_siswa 
                        LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = j.id_ta
                        LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas 
                        LEFT JOIN guru g ON j.id_guru = g.id_guru
                        WHERE j.id_ta = ?
                        ORDER BY j.tanggal DESC";
                $stmt_bk = $pdo->prepare($sql);
                $stmt_bk->execute([$id_ta]);
            } else {
                $sql = "SELECT j.*, s.nama as nama_siswa, k.nama_kelas 
                        FROM bk_jurnal j 
                        JOIN siswa s ON j.id_siswa = s.id_siswa 
                        LEFT JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa AND ps.id_ta = j.id_ta
                        LEFT JOIN kelas k ON ps.id_kelas = k.id_kelas 
                        WHERE j.id_guru = ? AND j.id_ta = ?
                        ORDER BY j.tanggal DESC";
                $stmt_bk = $pdo->prepare($sql);
                $stmt_bk->execute([$id_guru, $id_ta]);
            }
            $jurnal_bk = $stmt_bk->fetchAll(PDO::FETCH_ASSOC);
        }

        // Ambil kategori dinamis
        $list_kategori = self::get_categories($jenis);

        // Ambil semua tahun ajaran untuk filter
        $tahun_ajaran = $pdo->query("SELECT * FROM tahun_ajaran ORDER BY id_ta DESC")->fetchAll(PDO::FETCH_ASSOC);

        $title = "Administrasi " . $jabatan_name;
        require_once __DIR__ . '/../views/tugas_tambahan_index.php';
    }

    public static function upload($pdo)
    {
        $jenis = $_POST['jenis'] ?? '';
        $id_ta = (int)($_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0);
        $kategori = $_POST['kategori_dokumen'] ?? 'Lainnya';
        $nama_dokumen = trim($_POST['nama_dokumen'] ?? '');

        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        if ($id_guru <= 0) {
            $jabatan_name = self::get_jabatan_title($jenis);
            $stmt_g = $pdo->prepare("SELECT id_guru FROM penugasan_jabatan WHERE id_ta = ? AND (jenis_jabatan LIKE ? OR jenis_jabatan LIKE ?) LIMIT 1");
            $stmt_g->execute([$id_ta, '%' . $jabatan_name . '%', '%' . $jenis . '%']);
            $id_guru = (int)$stmt_g->fetchColumn();
            if (!$id_guru) $id_guru = 0;
        }

        if (empty($jenis)) {
            $_SESSION['pesan_error'] = "Gagal: Jenis jabatan tidak valid.";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'dashboard'));
            exit;
        }

        if (isset($_FILES['file_dokumen']) && $_FILES['file_dokumen']['error'] == 0) {
            $upload_base_dir = __DIR__ . '/../../public/uploads/administrasi/';
            $upload_dir = $upload_base_dir . $jenis . '/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES['file_dokumen']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

            if (!in_array($file_ext, $allowed_ext)) {
                $_SESSION['pesan_error'] = "Format file tidak didukung. Harap unggah PDF, Dokumen Office, atau Gambar.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $new_filename = $jenis . '_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['file_dokumen']['tmp_name'], $target_file)) {
                $file_path = 'uploads/administrasi/' . $jenis . '/' . $new_filename;

                $sql = "INSERT INTO dokumen_tugas_tambahan (id_guru, jenis_tugas_tambahan, kategori_dokumen, nama_dokumen, file_path, id_ta) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_guru, $jenis, $kategori, $nama_dokumen ?: $_FILES['file_dokumen']['name'], $file_path, $id_ta]);

                $_SESSION['pesan_sukses'] = "Dokumen berhasil diunggah.";
            } else {
                $_SESSION['pesan_error'] = "Gagal memindahkan file ke server.";
            }
        } else {
            $_SESSION['pesan_error'] = "Silakan pilih berkas dokumen yang valid.";
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/' . $jenis));
        exit;
    }

    public static function save_agenda($pdo)
    {
        $jenis = $_POST['jenis'] ?? '';
        $id_ta = (int)($_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0);
        $nama_kegiatan = trim($_POST['nama_kegiatan'] ?? '');
        $tanggal = $_POST['tanggal'] ?? '';
        $keterangan = $_POST['keterangan'] ?? '';

        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        if ($id_guru <= 0) {
            $jabatan_name = self::get_jabatan_title($jenis);
            $stmt_g = $pdo->prepare("SELECT id_guru FROM penugasan_jabatan WHERE id_ta = ? AND (jenis_jabatan LIKE ? OR jenis_jabatan LIKE ?) LIMIT 1");
            $stmt_g->execute([$id_ta, '%' . $jabatan_name . '%', '%' . $jenis . '%']);
            $id_guru = (int)$stmt_g->fetchColumn();
            if (!$id_guru) $id_guru = 0;
        }

        if (!empty($nama_kegiatan) && !empty($tanggal)) {
            $sql = "INSERT INTO agenda_tugas_tambahan (id_guru, jenis_tugas_tambahan, nama_kegiatan, tanggal, keterangan, id_ta) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_guru, $jenis, $nama_kegiatan, $tanggal, $keterangan, $id_ta]);
            $_SESSION['pesan_sukses'] = "Agenda berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Nama kegiatan dan tanggal wajib diisi.";
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/' . $jenis));
        exit;
    }

    public static function delete_agenda($pdo)
    {
        $id = (int)($_GET['id'] ?? 0);
        $is_admin = in_array('Admin', user_roles());
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);

        if ($is_admin) {
            $stmt = $pdo->prepare("DELETE FROM agenda_tugas_tambahan WHERE id_agenda = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM agenda_tugas_tambahan WHERE id_agenda = ? AND id_guru = ?");
            $stmt->execute([$id, $id_guru]);
        }

        $_SESSION['pesan_sukses'] = "Agenda berhasil dihapus.";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'dashboard'));
        exit;
    }

    public static function delete($pdo)
    {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM dokumen_tugas_tambahan WHERE id_dokumen = ?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doc) {
            $is_admin = in_array('Admin', user_roles());
            $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);

            if ($is_admin || $doc['id_guru'] == $id_guru || $doc['id_guru'] == 0) {
                $physical_path = __DIR__ . '/../../public/' . $doc['file_path'];
                if (file_exists($physical_path)) {
                    @unlink($physical_path);
                }
                $stmt = $pdo->prepare("DELETE FROM dokumen_tugas_tambahan WHERE id_dokumen = ?");
                $stmt->execute([$id]);
                $_SESSION['pesan_sukses'] = "Dokumen berhasil dihapus.";
            } else {
                $_SESSION['pesan_error'] = "Anda tidak berhak menghapus dokumen ini.";
            }
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'dashboard'));
        exit;
    }

    public static function save_inventaris($pdo)
    {
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        $id_ta = (int)($_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0);
        $nama_barang = trim($_POST['nama_barang'] ?? '');
        $jumlah = (int)($_POST['jumlah'] ?? 1);
        $kondisi = $_POST['kondisi'] ?? 'Baik';
        $keterangan = $_POST['keterangan'] ?? '';

        if (!empty($nama_barang) && $jumlah > 0) {
            $sql = "INSERT INTO walas_inventaris (id_guru, id_ta, nama_barang, jumlah, kondisi, keterangan) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_guru, $id_ta, $nama_barang, $jumlah, $kondisi, $keterangan]);
            $_SESSION['pesan_sukses'] = "Barang inventaris berhasil ditambahkan.";
        } else {
            $_SESSION['pesan_error'] = "Nama barang dan jumlah tidak valid.";
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/walas'));
        exit;
    }

    public static function delete_inventaris($pdo)
    {
        $id = (int)($_GET['id'] ?? 0);
        $is_admin = in_array('Admin', user_roles());
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);

        if ($is_admin) {
            $stmt = $pdo->prepare("DELETE FROM walas_inventaris WHERE id_inventaris = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM walas_inventaris WHERE id_inventaris = ? AND id_guru = ?");
            $stmt->execute([$id, $id_guru]);
        }

        $_SESSION['pesan_sukses'] = "Barang inventaris berhasil dihapus.";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/walas'));
        exit;
    }

    public static function save_jurnal_bk($pdo)
    {
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        $id_ta = (int)($_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $id_siswa = (int)($_POST['id_siswa'] ?? 0);
        $kategori = $_POST['kategori_layanan'] ?? '';
        $uraian = $_POST['uraian_kegiatan'] ?? '';
        $tindak_lanjut = $_POST['tindak_lanjut'] ?? '';
        $status = $_POST['status'] ?? 'Proses';

        if ($id_siswa > 0 && !empty($kategori) && !empty($uraian)) {
            $sql = "INSERT INTO bk_jurnal (id_guru, id_ta, tanggal, id_siswa, kategori_layanan, uraian_kegiatan, tindak_lanjut, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_guru, $id_ta, $tanggal, $id_siswa, $kategori, $uraian, $tindak_lanjut, $status]);
            $_SESSION['pesan_sukses'] = "Jurnal BK berhasil disimpan.";
        } else {
            $_SESSION['pesan_error'] = "Data jurnal tidak lengkap.";
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/bk'));
        exit;
    }

    public static function delete_jurnal_bk($pdo)
    {
        $id = (int)($_GET['id'] ?? 0);
        $is_admin = in_array('Admin', user_roles());
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);

        if ($is_admin) {
            $stmt = $pdo->prepare("DELETE FROM bk_jurnal WHERE id_jurnal = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM bk_jurnal WHERE id_jurnal = ? AND id_guru = ?");
            $stmt->execute([$id, $id_guru]);
        }

        $_SESSION['pesan_sukses'] = "Jurnal BK berhasil dihapus.";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/bk'));
        exit;
    }

    public static function save_galeri($pdo)
    {
        $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);
        $id_ta = (int)($_POST['id_ta'] ?? $_SESSION['id_ta_aktif'] ?? 0);
        $jenis = $_POST['jenis_tugas_tambahan'] ?? '';
        $caption = $_POST['caption'] ?? '';
        $foto_cam_data = $_POST['foto_cam_data'] ?? '';

        $upload_dir = __DIR__ . '/../../public/uploads/galeri_tugas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $saved_file_name = null;

        // A. Cek Foto dari Live Camera
        if (!empty($foto_cam_data) && preg_match('/^data:image\/(\w+);base64,/', $foto_cam_data, $cam_match)) {
            $raw_base64 = substr($foto_cam_data, strpos($foto_cam_data, ',') + 1);
            $decoded = base64_decode($raw_base64);
            $cam_type = strtolower($cam_match[1]);
            $ext = ($cam_type === 'png') ? 'png' : 'jpg';
            if ($decoded) {
                $saved_file_name = 'galeri_' . $jenis . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                file_put_contents($upload_dir . $saved_file_name, $decoded);
            }
        }
        // B. Cek Foto dari Unggah File Biasa
        elseif (!empty($jenis) && isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $saved_file_name = 'galeri_' . $jenis . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $target_file = $upload_dir . $saved_file_name;
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $saved_file_name = null;
            }
        }

        if ($saved_file_name) {
            $db_path = 'uploads/galeri_tugas/' . $saved_file_name;
            $sql = "INSERT INTO galeri_tugas_tambahan (id_guru, id_ta, jenis_tugas_tambahan, file_path, caption) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_guru, $id_ta, $jenis, $db_path, $caption]);
            $_SESSION['pesan_sukses'] = "Foto dokumentasi kegiatan berhasil diunggah ke galeri.";
        } else {
            $_SESSION['pesan_error'] = "Silakan ambil foto dengan kamera atau pilih file foto yang valid.";
        }

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'tugas_tambahan/' . $jenis));
        exit;
    }

    public static function delete_galeri($pdo)
    {
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $pdo->prepare("SELECT * FROM galeri_tugas_tambahan WHERE id_galeri = ?");
        $stmt->execute([$id]);
        $gal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gal) {
            $is_admin = in_array('Admin', user_roles());
            $id_guru = (int)($_SESSION['id_guru_terkait'] ?? 0);

            if ($is_admin || $gal['id_guru'] == $id_guru || $gal['id_guru'] == 0) {
                $physical_path = __DIR__ . '/../../public/' . $gal['file_path'];
                if (file_exists($physical_path)) {
                    @unlink($physical_path);
                }
                $stmt = $pdo->prepare("DELETE FROM galeri_tugas_tambahan WHERE id_galeri = ?");
                $stmt->execute([$id]);
                $_SESSION['pesan_sukses'] = "Foto galeri berhasil dihapus.";
            } else {
                $_SESSION['pesan_error'] = "Anda tidak berhak menghapus foto ini.";
            }
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'dashboard'));
        exit;
    }
}