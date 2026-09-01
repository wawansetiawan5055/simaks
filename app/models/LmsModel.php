<?php
// app/models/LmsModel.php

class LmsModel {
    
    // === MATERI ===
    public static function uploadMateri($pdo, $data, $files, $user_id) {
        // Validasi input
        if (empty($data['judul_materi']) || empty($data['id_mapel']) || empty($data['tingkat'])) {
            throw new Exception("Judul materi, mata pelajaran, dan tingkat wajib diisi.");
        }
        
        // Cek jika terintegrasi dengan perangkat
        $file_path = $data['existing_file_path'] ?? null;
        if (!empty($data['id_perangkat']) && isset($data['include_perangkat_file'])) {
            require_once __DIR__ . '/PerangkatModel.php';
            $perangkat = PerangkatModel::findDocument($pdo, $data['id_perangkat']);
            if ($perangkat && !empty($perangkat['file_path'])) {
                $file_path = $perangkat['file_path']; // Link ke file perangkat jika ada
            }
        }

        // Upload file baru jika ada (overwrites the link if provided)
        if (!empty($files['file_materi']['name'])) {
            require_once __DIR__ . '/../../config/secure_upload.php';
            $upload_dir = __DIR__ . '/../../uploads/lms_materi';
            $file_path = SecureFileUpload::upload($files['file_materi'], $upload_dir, 'document');
        }
        
        // Insert ke database
        $id_bab = !empty($data['id_bab']) ? (int)$data['id_bab'] : null;
        $id_sub_bab = !empty($data['id_sub_bab']) ? (int)$data['id_sub_bab'] : null;

        $sql = "INSERT INTO lms_materi (id_mapel, id_guru, id_bab, id_sub_bab, tingkat, judul_materi, deskripsi, materi_questions, file_path, video_url, video_questions, external_url, id_perangkat, created_at, 
                semester, tahun_pelajaran, instruksi, tes_diagnostik_config, id_cp, id_tp, cp_manual, tp_manual, refleksi_config, essay_config) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_mapel'],
            $user_id,
            $id_bab,
            $id_sub_bab,
            $data['tingkat'],
            $data['judul_materi'],
            $data['deskripsi'] ?? '',
            isset($data['materi_questions']) ? json_encode($data['materi_questions']) : null,
            $file_path,
            empty($data['video_url']) ? null : $data['video_url'],
            isset($data['video_questions']) ? json_encode($data['video_questions']) : null,
            empty($data['external_url']) ? null : $data['external_url'],
            empty($data['id_perangkat']) ? null : $data['id_perangkat'],
            $data['semester'] ?? '1',
            empty($data['tahun_pelajaran']) ? null : $data['tahun_pelajaran'],
            empty($data['instruksi']) ? null : $data['instruksi'],
            isset($data['tes_diagnostik_config']) && is_array($data['tes_diagnostik_config']) ? json_encode($data['tes_diagnostik_config']) : ($data['tes_diagnostik_config'] ?? null),
            empty($data['id_cp']) ? null : $data['id_cp'],
            empty($data['id_tp']) ? null : $data['id_tp'],
            empty($data['cp_manual']) ? null : $data['cp_manual'],
            empty($data['tp_manual']) ? null : $data['tp_manual'],
            isset($data['refleksi']) ? json_encode($data['refleksi']) : null,
            empty($data['essay_config']) ? null : $data['essay_config']
        ]);
        
        return $pdo->lastInsertId();
    }
    
    public static function getMateriByGuru($pdo, $guru_id) {
        $sql = "SELECT m.*, mp.nama_mapel, g.nama as nama_guru FROM lms_materi m 
                JOIN mapel mp ON m.id_mapel = mp.id_mapel 
                LEFT JOIN guru g ON m.id_guru = g.id_guru
                WHERE m.id_guru = ? OR ? IS NULL ORDER BY m.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function deleteMateri($pdo, $id_materi) {
        // Hapus soal dan jawaban terkait otomatis via foreign key cascade
        $sql = "DELETE FROM lms_materi WHERE id_materi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_materi]);
        return $stmt->rowCount() > 0;
    }
    
    public static function updateMateri($pdo, $id_materi, $data, $files) {
        if (empty($data['judul_materi']) || empty($data['id_mapel']) || empty($data['tingkat'])) {
            throw new Exception("Judul materi, mata pelajaran, dan tingkat wajib diisi.");
        }

        // Ambil file lama jika ada
        $current = self::getMateriById($pdo, $id_materi);
        $file_path = $current['file_path'];

        // Cek jika terintegrasi dengan perangkat baru
        if (!empty($data['id_perangkat']) && isset($data['include_perangkat_file'])) {
            require_once __DIR__ . '/PerangkatModel.php';
            $perangkat = PerangkatModel::findDocument($pdo, $data['id_perangkat']);
            if ($perangkat && !empty($perangkat['file_path'])) {
                $file_path = $perangkat['file_path'];
            }
        }

        // Upload file baru jika ada
        if (!empty($files['file_materi']['name'])) {
            require_once __DIR__ . '/../../config/secure_upload.php';
            $upload_dir = __DIR__ . '/../../uploads/lms_materi';
            $file_path = SecureFileUpload::upload($files['file_materi'], $upload_dir, 'document');
        }

        $id_bab = !empty($data['id_bab']) ? (int)$data['id_bab'] : null;
        $id_sub_bab = !empty($data['id_sub_bab']) ? (int)$data['id_sub_bab'] : null;

        $sql = "UPDATE lms_materi SET id_mapel = ?, id_bab = ?, id_sub_bab = ?, tingkat = ?, judul_materi = ?, deskripsi = ?, materi_questions = ?, file_path = ?, video_url = ?, video_questions = ?, external_url = ?, id_perangkat = ?, 
                semester = ?, tahun_pelajaran = ?, instruksi = ?, id_cp = ?, id_tp = ?, cp_manual = ?, tp_manual = ?, refleksi_config = ?, tes_diagnostik_config = ?, essay_config = ?
                WHERE id_materi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_mapel'],
            $id_bab,
            $id_sub_bab,
            $data['tingkat'],
            $data['judul_materi'],
            $data['deskripsi'] ?? '',
            isset($data['materi_questions']) ? json_encode($data['materi_questions']) : null,
            $file_path,
            empty($data['video_url']) ? null : $data['video_url'],
            isset($data['video_questions']) ? json_encode($data['video_questions']) : null,
            empty($data['external_url']) ? null : $data['external_url'],
            empty($data['id_perangkat']) ? null : $data['id_perangkat'],
            $data['semester'] ?? '1',
            empty($data['tahun_pelajaran']) ? null : $data['tahun_pelajaran'],
            empty($data['instruksi']) ? null : $data['instruksi'],
            empty($data['id_cp']) ? null : $data['id_cp'],
            empty($data['id_tp']) ? null : $data['id_tp'],
            empty($data['cp_manual']) ? null : $data['cp_manual'],
            empty($data['tp_manual']) ? null : $data['tp_manual'],
            isset($data['refleksi']) ? json_encode($data['refleksi']) : null,
            isset($data['tes_diagnostik_config']) && is_array($data['tes_diagnostik_config']) ? json_encode($data['tes_diagnostik_config']) : ($data['tes_diagnostik_config'] ?? null),
            empty($data['essay_config']) ? null : $data['essay_config'],
            $id_materi
        ]);

        return true;
    }

    public static function getMateriForSiswa($pdo, $siswa_id, $filters = [], $id_ta = null) {
        $params = [];
        
        // Ambil tingkat default siswa jika tidak difilter
        $tingkat_filter = $filters['tingkat'] ?? null;
        $id_mapel = $filters['id_mapel'] ?? null;
        $search = $filters['search'] ?? null;

        $sql = "SELECT m.*, mp.nama_mapel, g.nama as nama_guru 
                FROM lms_materi m 
                JOIN mapel mp ON m.id_mapel = mp.id_mapel 
                JOIN guru g ON m.id_guru = g.id_guru
                WHERE 1=1";

        if ($tingkat_filter) {
            $sql .= " AND m.tingkat = ?";
            $params[] = $tingkat_filter;
        } else {
            $subquery = "SELECT tingkat FROM kelas k JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas WHERE ps.id_siswa = ? AND ps.status_penempatan = 'Aktif'";
            $params[] = $siswa_id;
            
            if ($id_ta) {
                $subquery .= " AND ps.id_ta = ?";
                $params[] = $id_ta;
            }
            
            $subquery .= " ORDER BY ps.id_penempatan DESC LIMIT 1";
            $sql .= " AND m.tingkat = ($subquery)";
        }

        if ($id_mapel) {
            $sql .= " AND m.id_mapel = ?";
            $params[] = $id_mapel;
        }

        if ($search) {
            $sql .= " AND (m.judul_materi LIKE ? OR m.deskripsi LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY m.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function countMateriByGuru($pdo, $guru_id) {
        $sql = "SELECT COUNT(*) FROM lms_materi WHERE id_guru = ? OR ? IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        return $stmt->fetchColumn();
    }
    
    public static function getMateriById($pdo, $id_materi) {
        $sql = "SELECT m.*, mp.nama_mapel, g.nama as nama_guru FROM lms_materi m 
                JOIN mapel mp ON m.id_mapel = mp.id_mapel 
                LEFT JOIN guru g ON m.id_guru = g.id_guru
                WHERE m.id_materi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_materi]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getTugasByMateri($pdo, $id_materi) {
        $sql = "SELECT * FROM lms_tugas WHERE id_materi = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_materi]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getMapelByGuru($pdo, $guru_id) {
        if (!$guru_id) return self::getAllMapel($pdo);
        $sql = "SELECT DISTINCT mp.id_mapel, mp.nama_mapel 
                FROM mapel mp 
                WHERE mp.id_mapel IN (SELECT id_mapel FROM guru_mapel WHERE id_guru = ?)
                   OR mp.id_mapel IN (SELECT gm.id_mapel FROM jadwal_mengajar jm JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel WHERE gm.id_guru = ?)
                ORDER BY mp.nama_mapel ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public static function getAllMapel($pdo) {
        $sql = "SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRombelByGuru($pdo, $guru_id = null) {
        if ($guru_id) {
            $sql = "SELECT DISTINCT k.id_kelas, k.nama_kelas, k.tingkat
                    FROM kelas k 
                    WHERE k.id_kelas IN (SELECT gm.id_kelas FROM guru_mapel gm WHERE gm.id_guru = ? AND gm.id_kelas IS NOT NULL)
                       OR k.id_kelas IN (SELECT jm.id_kelas FROM jadwal_mengajar jm JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel WHERE gm.id_guru = ?)
                    ORDER BY k.tingkat ASC, k.nama_kelas ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$guru_id, $guru_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($res)) return $res;
        }
        return $pdo->query("SELECT k.id_kelas, k.nama_kelas, k.tingkat FROM kelas k JOIN tahun_ajaran ta ON k.id_ta = ta.id_ta WHERE ta.status='Aktif' ORDER BY k.tingkat ASC, k.nama_kelas ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRombelByTingkat($pdo, $tingkat, $guru_id = null) {
        $sql = "SELECT id_kelas, nama_kelas FROM kelas WHERE tingkat = ?";
        $params = [$tingkat];
        
        if ($guru_id) {
            $sql = "SELECT DISTINCT k.id_kelas, k.nama_kelas 
                    FROM kelas k 
                    JOIN jadwal_mengajar jm ON k.id_kelas = jm.id_kelas
                    JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                    WHERE k.tingkat = ? AND gm.id_guru = ?";
            $params = [$tingkat, $guru_id];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPerangkatByGuru($pdo, $guru_id, $id_mapel = null, $tingkat = null) {
        $sql = "SELECT id_perangkat, judul, jenis, kelas, mapel FROM perangkat_pembelajaran WHERE 1=1";
        $params = [];
        
        if ($guru_id) {
            $sql .= " AND id_guru = ?";
            $params[] = $guru_id;
        }
        
        if ($id_mapel) {
            // Ambil nama mapel dari id_mapel
            $stmt_mp = $pdo->prepare("SELECT nama_mapel FROM mapel WHERE id_mapel = ?");
            $stmt_mp->execute([$id_mapel]);
            $nama_mapel = $stmt_mp->fetchColumn();
            if ($nama_mapel) {
                $sql .= " AND mapel = ?";
                $params[] = $nama_mapel;
            }
        }
        
        if ($tingkat) {
            // Mapping X -> 10, XI -> 11, XII -> 12
            $mapping = ['X' => '10', 'XI' => '11', 'XII' => '12'];
            $mapped_tingkat = $mapping[$tingkat] ?? $tingkat;
            $sql .= " AND (kelas = ? OR kelas = ?)";
            $params[] = $mapped_tingkat;
            $params[] = "Kelas " . $mapped_tingkat;
        }
        
        $sql .= " ORDER BY judul ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // === TUGAS ===
    public static function createTugas($pdo, $data, $guru_id) {
        if (empty($data['judul_tugas']) || empty($data['id_kelas']) || empty($data['deadline'])) {
            throw new Exception("Judul tugas, kelas (rombel), dan deadline wajib diisi.");
        }
        
        $sql = "INSERT INTO lms_tugas (id_mapel, id_guru, id_materi, id_kelas, judul_tugas, instruksi, deadline, bobot_nilai, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Aktif')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_mapel'] ?? null,
            $guru_id,
            $data['id_materi'] ?? null,
            $data['id_kelas'],
            $data['judul_tugas'],
            $data['instruksi'] ?? '',
            $data['deadline'],
            $data['bobot_nilai'] ?? 100
        ]);
        
        return $pdo->lastInsertId();
    }
    
    public static function getTugasByGuru($pdo, $guru_id) {
        $sql = "SELECT t.*, mp.nama_mapel, g.nama as nama_guru FROM lms_tugas t 
                JOIN mapel mp ON t.id_mapel = mp.id_mapel 
                LEFT JOIN guru g ON t.id_guru = g.id_guru
                WHERE t.id_guru = ? OR ? IS NULL ORDER BY t.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getTugasForSiswa($pdo, $siswa_id) {
        $sql = "SELECT t.*, mp.nama_mapel, g.nama as nama_guru, p.tgl_upload as tgl_kumpul 
                FROM lms_tugas t 
                LEFT JOIN mapel mp ON t.id_mapel = mp.id_mapel 
                LEFT JOIN guru g ON t.id_guru = g.id_guru
                LEFT JOIN lms_pengumpulan p ON t.id_tugas = p.id_tugas AND p.id_siswa = ?
                WHERE t.id_kelas IN (SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif')
                AND t.status = 'Aktif' ORDER BY t.deadline ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$siswa_id, $siswa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getTugasPendingForSiswa($pdo, $siswa_id) {
        $sql = "SELECT t.* FROM lms_tugas t 
                WHERE t.id_kelas IN (SELECT id_kelas FROM penempatan_siswa WHERE id_siswa = ? AND status_penempatan = 'Aktif')
                AND t.status = 'Aktif' AND t.deadline > NOW()
                AND t.id_tugas NOT IN (SELECT id_tugas FROM lms_pengumpulan WHERE id_siswa = ?)
                ORDER BY t.deadline ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$siswa_id, $siswa_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getTugasById($pdo, $id_tugas) {
        $sql = "SELECT t.*, mp.nama_mapel, k.nama_kelas, 
                       m.tes_diagnostik_config, m.essay_config, m.refleksi_config, 
                       m.materi_questions, m.video_url, m.judul_materi
                FROM lms_tugas t 
                LEFT JOIN mapel mp ON t.id_mapel = mp.id_mapel 
                LEFT JOIN kelas k ON t.id_kelas = k.id_kelas
                LEFT JOIN lms_materi m ON t.id_materi = m.id_materi
                WHERE t.id_tugas = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function countTugasByGuru($pdo, $guru_id) {
        $sql = "SELECT COUNT(*) FROM lms_tugas WHERE id_guru = ? OR ? IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        return $stmt->fetchColumn();
    }

    public static function countMapelForSiswa($pdo, $siswa_id, $id_ta = null) {
        $sql = "SELECT COUNT(DISTINCT gm.id_mapel) 
                FROM jadwal_mengajar jm
                JOIN guru_mapel gm ON jm.id_guru_mapel = gm.id_guru_mapel
                JOIN penempatan_siswa ps ON jm.id_kelas = ps.id_kelas
                WHERE ps.id_siswa = ? AND ps.status_penempatan = 'Aktif'";
        
        $params = [$siswa_id];
        if ($id_ta) {
            $sql .= " AND ps.id_ta = ?";
            $params[] = $id_ta;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function countTugasSelesaiForSiswa($pdo, $siswa_id) {
        $sql = "SELECT COUNT(*) FROM lms_pengumpulan WHERE id_siswa = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$siswa_id]);
        return $stmt->fetchColumn();
    }

    public static function getSiswaDetail($pdo, $siswa_id, $id_ta) {
        $sql = "SELECT s.*, k.nama_kelas, p.foto as foto_pengguna 
                FROM siswa s
                JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                LEFT JOIN pengguna p ON s.id_pengguna = p.id_pengguna
                WHERE s.id_siswa = ? AND ps.status_penempatan = 'Aktif' AND ps.id_ta = ?
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$siswa_id, $id_ta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // === PENGUMPULAN ===
    public static function submitTugas($pdo, $data, $files, $siswa_id) {
        $id_tugas = $data['id_tugas'] ?? 0;
        if (!$id_tugas) throw new Exception("ID tugas tidak valid.");
        
        // Cek apakah sudah submit sebelumnya
        $existing = self::getPengumpulanByTugasSiswa($pdo, $id_tugas, $siswa_id);
        if ($existing) throw new Exception("Tugas sudah dikumpulkan sebelumnya.");
        
        // Upload file jika ada
        $file_path = null;
        if (!empty($files['file_tugas']['name'])) {
            require_once __DIR__ . '/../../config/secure_upload.php';
            $upload_dir = __DIR__ . '/../../uploads/lms_tugas';
            $file_path = SecureFileUpload::upload($files['file_tugas'], $upload_dir, 'document');
        }
        
        $sql = "INSERT INTO lms_pengumpulan (id_tugas, id_siswa, file_siswa, tgl_upload) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas, $siswa_id, $file_path]);
        
        return $pdo->lastInsertId();
    }
    
    public static function getPengumpulanByTugasSiswa($pdo, $id_tugas, $siswa_id) {
        $sql = "SELECT * FROM lms_pengumpulan WHERE id_tugas = ? AND id_siswa = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas, $siswa_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function countPengumpulanPending($pdo, $guru_id) {
        $sql = "SELECT COUNT(*) FROM lms_pengumpulan p 
                JOIN lms_tugas t ON p.id_tugas = t.id_tugas 
                WHERE (t.id_guru = ? OR ? IS NULL) AND p.nilai IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        return $stmt->fetchColumn();
    }
    
    public static function getPengumpulanPendingByGuru($pdo, $guru_id) {
        $sql = "SELECT p.*, t.judul_tugas, s.nama as nama_siswa, s.nipd as nis, mp.nama_mapel
                FROM lms_pengumpulan p 
                JOIN lms_tugas t ON p.id_tugas = t.id_tugas 
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN mapel mp ON t.id_mapel = mp.id_mapel
                WHERE t.id_guru = ? AND p.nilai IS NULL
                ORDER BY p.tgl_upload ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getPengumpulanById($pdo, $id_pengumpulan) {
        $sql = "SELECT p.*, p.updated_at as tgl_nilai, t.judul_tugas, t.instruksi, s.nama as nama_siswa, s.nipd as nis, mp.nama_mapel, g.nama as nama_guru
                FROM lms_pengumpulan p 
                JOIN lms_tugas t ON p.id_tugas = t.id_tugas 
                JOIN siswa s ON p.id_siswa = s.id_siswa
                JOIN mapel mp ON t.id_mapel = mp.id_mapel
                JOIN guru g ON t.id_guru = g.id_guru
                WHERE p.id_kumpul = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_pengumpulan]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function nilaiPengumpulan($pdo, $id_pengumpulan, $nilai, $komentar = '') {
        $sql = "UPDATE lms_pengumpulan SET nilai = ?, catatan_guru = ? WHERE id_kumpul = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nilai, $komentar, $id_pengumpulan]);
        return $stmt->rowCount() > 0;
    }
    
    public static function updateTugas($pdo, $id_tugas, $data) {
        if (empty($data['judul_tugas']) || empty($data['deadline'])) {
            throw new Exception("Judul tugas dan deadline wajib diisi.");
        }
        
        $sql = "UPDATE lms_tugas SET judul_tugas = ?, instruksi = ?, deadline = ?, bobot_nilai = ?, status = ? WHERE id_tugas = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['judul_tugas'],
            $data['instruksi'] ?? '',
            $data['deadline'],
            $data['bobot_nilai'] ?? 100,
            $data['status'] ?? 'Aktif',
            $id_tugas
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    public static function deleteTugas($pdo, $id_tugas) {
        // Delete pengumpulan dulu
        $sql = "DELETE FROM lms_pengumpulan WHERE id_tugas = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas]);
        
        // Delete tugas
        $sql = "DELETE FROM lms_tugas WHERE id_tugas = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas]);
        
        return $stmt->rowCount() > 0;
    }

    // === KUIS MATERI (ENGAGEMENT CHECK) ===
    public static function saveMateriSoal($pdo, $id_materi, $questions, $files = []) {
        require_once __DIR__ . '/../../config/secure_upload.php';
        $upload_dir = __DIR__ . '/../../uploads/lms_quiz';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        // Ambil ID soal yang ada untuk deteksi yang harus dihapus (jika ada yang dikurangi di UI)
        $existing_ids = [];
        $stmt_ids = $pdo->prepare("SELECT id_soal FROM lms_materi_soal WHERE id_materi = ?");
        $stmt_ids->execute([$id_materi]);
        $all_existing_ids = $stmt_ids->fetchAll(PDO::FETCH_COLUMN);

        $processed_ids = [];

        foreach ($questions as $index => $q) {
            if (empty($q['pertanyaan'])) continue;
            
            $id_soal = $q['id_soal'] ?? null;
            $nomor_urut = $index + 1;

            // Ambil data lama jika update untuk mempertahankan file lama
            $current_soal = null;
            if ($id_soal) {
                $stmt_curr = $pdo->prepare("SELECT * FROM lms_materi_soal WHERE id_soal = ?");
                $stmt_curr->execute([$id_soal]);
                $current_soal = $stmt_curr->fetch();
            }

            // Handle File Uploads
            $media_fields = ['file_pertanyaan', 'file_a', 'file_b', 'file_c', 'file_d', 'file_e'];
            $file_data = [];
            foreach ($media_fields as $field) {
                $file_data[$field] = $current_soal[$field] ?? null;
                
                // Cek if there is a new file for this question & field
                // Format di $_FILES: questions_media[index][field]
                if (!empty($files['questions_media']['name'][$index][$field])) {
                    $tmp_file = [
                        'name' => $files['questions_media']['name'][$index][$field],
                        'type' => $files['questions_media']['type'][$index][$field],
                        'tmp_name' => $files['questions_media']['tmp_name'][$index][$field],
                        'error' => $files['questions_media']['error'][$index][$field],
                        'size' => $files['questions_media']['size'][$index][$field]
                    ];
                    
                    // Deteksi tipe media (audio atau image)
                    $type = strpos($tmp_file['type'], 'audio') !== false ? 'audio' : 'image';
                    $file_data[$field] = SecureFileUpload::upload($tmp_file, $upload_dir, $type);
                }
            }

            if ($id_soal && in_array($id_soal, $all_existing_ids)) {
                // UPDATE
                $sql = "UPDATE lms_materi_soal SET tipe = ?, pertanyaan = ?, opsi_a = ?, opsi_b = ?, opsi_c = ?, opsi_d = ?, opsi_e = ?, kunci_jawaban = ?, nomor_urut = ?, 
                        file_pertanyaan = ?, file_a = ?, file_b = ?, file_c = ?, file_d = ?, file_e = ?, kategori_soal = ?
                        WHERE id_soal = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $q['tipe'], $q['pertanyaan'], 
                    $q['opsi_a'] ?? null, $q['opsi_b'] ?? null, $q['opsi_c'] ?? null, $q['opsi_d'] ?? null, $q['opsi_e'] ?? null, 
                    $q['kunci_jawaban'] ?? null, $nomor_urut,
                    $file_data['file_pertanyaan'], $file_data['file_a'], $file_data['file_b'], $file_data['file_c'], $file_data['file_d'], $file_data['file_e'],
                    $q['kategori_soal'] ?? 'Latihan',
                    $id_soal
                ]);
                $processed_ids[] = $id_soal;
            } else {
                // INSERT
                $sql = "INSERT INTO lms_materi_soal (id_materi, tipe, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban, nomor_urut, 
                        file_pertanyaan, file_a, file_b, file_c, file_d, file_e, kategori_soal) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $id_materi, $q['tipe'], $q['pertanyaan'], 
                    $q['opsi_a'] ?? null, $q['opsi_b'] ?? null, $q['opsi_c'] ?? null, $q['opsi_d'] ?? null, $q['opsi_e'] ?? null, 
                    $q['kunci_jawaban'] ?? null, $nomor_urut,
                    $file_data['file_pertanyaan'], $file_data['file_a'], $file_data['file_b'], $file_data['file_c'], $file_data['file_d'], $file_data['file_e'],
                    $q['kategori_soal'] ?? 'Latihan'
                ]);
            }
        }

        // Hapus soal yang tidak ada lagi di form (dihapus guru)
        $to_delete = array_diff($all_existing_ids, $processed_ids);
        if (!empty($to_delete)) {
            $placeholders = implode(',', array_fill(0, count($to_delete), '?'));
            $pdo->prepare("DELETE FROM lms_materi_soal WHERE id_soal IN ($placeholders)")->execute(array_values($to_delete));
        }
    }

    public static function getSoalByMateri($pdo, $id_materi) {
        $stmt = $pdo->prepare("SELECT * FROM lms_materi_soal WHERE id_materi = ? ORDER BY nomor_urut ASC");
        $stmt->execute([$id_materi]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function submitJawabanMateri($pdo, $id_siswa, $id_materi, $jawaban, $refleksi = []) {
        $sql = "INSERT INTO lms_materi_jawaban (id_soal, id_siswa, jawaban, is_correct) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($jawaban as $id_soal => $isi) {
            // Cek kunci jika PG
            $stmt_soal = $pdo->prepare("SELECT tipe, kunci_jawaban FROM lms_materi_soal WHERE id_soal = ?");
            $stmt_soal->execute([$id_soal]);
            $soal = $stmt_soal->fetch();
            
            $is_correct = 0;
            if ($soal && $soal['tipe'] == 'PG' && strtoupper($isi) == strtoupper($soal['kunci_jawaban'])) {
                $is_correct = 1;
            }
            
            $stmt->execute([$id_soal, $id_siswa, $isi, $is_correct]);
        }

        // Simpan refleksi jika ada
        if (!empty($refleksi)) {
            $sql_ref = "INSERT INTO lms_materi_refleksi (id_materi, id_siswa, pertanyaan, jawaban) VALUES (?, ?, ?, ?)";
            $stmt_ref = $pdo->prepare($sql_ref);
            foreach ($refleksi as $r) {
                $stmt_ref->execute([$id_materi, $id_siswa, $r['pertanyaan'], $r['jawaban']]);
            }
        }
    }

    public static function hasSubmittedQuiz($pdo, $id_materi, $id_siswa) {
        $sql = "SELECT COUNT(*) FROM lms_materi_jawaban j 
                JOIN lms_materi_soal s ON j.id_soal = s.id_soal 
                WHERE s.id_materi = ? AND j.id_siswa = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_materi, $id_siswa]);
        return $stmt->fetchColumn() > 0;
    }

    public static function parseQuizExcel($file_path) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        $questions = [];
        // Skip header
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[1]) || empty($row[2])) continue; // Skip if Tipe or Pertanyaan is empty
            
            $questions[] = [
                'tipe' => strtoupper(trim($row[1])),
                'pertanyaan' => trim($row[2]),
                'opsi_a' => $row[3] ?? null,
                'opsi_b' => $row[4] ?? null,
                'opsi_c' => $row[5] ?? null,
                'opsi_d' => $row[6] ?? null,
                'opsi_e' => $row[7] ?? null,
                'kunci_jawaban' => strtoupper(trim($row[8] ?? ''))
            ];
        }
        return $questions;
    }

    // === INTEGRASI CP & TP ===
    public static function getCPByMapel($pdo, $id_mapel, $tingkat) {
        $fase = ($tingkat == 'X') ? 'E' : 'F';
        $sql = "SELECT * FROM capaian_pembelajaran WHERE id_mapel = ? AND fase = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_mapel, $fase]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTPByCP($pdo, $id_cp) {
        $sql = "SELECT * FROM tujuan_pembelajaran WHERE id_cp = ? ORDER BY kode_tp ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_cp]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTaskSubmissions($pdo, $id_tugas) {
        $sql = "SELECT s.id_siswa, s.nama, k.nama_kelas,
                       p.id_kumpul, p.nilai, p.catatan_guru, p.tgl_upload,
                       prog.stage_instruksi, prog.stage_diagnostik, prog.stage_materi, 
                       prog.stage_essay, prog.stage_formatif, prog.stage_refleksi,
                       prog.score_diagnostik, prog.score_materi, prog.score_tugas_materi,
                       prog.score_formatif, prog.score_refleksi,
                       prog.updated_at as last_active
                FROM lms_tugas t
                JOIN penempatan_siswa ps ON t.id_kelas = ps.id_kelas
                JOIN siswa s ON ps.id_siswa = s.id_siswa
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                LEFT JOIN lms_pengumpulan p ON t.id_tugas = p.id_tugas AND s.id_siswa = p.id_siswa
                LEFT JOIN lms_tugas_progress prog ON t.id_tugas = prog.id_tugas AND s.id_siswa = prog.id_siswa
                WHERE t.id_tugas = ?
                ORDER BY s.nama ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTaskSubmissionDetail($pdo, $id_tugas, $id_siswa) {
        $sql = "SELECT t.id_tugas, s.id_siswa, s.nama, s.nipd as nis, k.nama_kelas, mp.nama_mapel, t.judul_tugas, t.instruksi, t.deadline, t.bobot_nilai,
                       m.tes_diagnostik_config, m.essay_config, m.materi_questions, m.refleksi_config,
                       p.id_kumpul, p.file_siswa, p.nilai, p.catatan_guru, p.tgl_upload, p.updated_at as tgl_nilai,
                       prog.stage_instruksi, prog.stage_diagnostik, prog.stage_materi,
                       prog.stage_essay, prog.stage_formatif, prog.stage_refleksi,
                       prog.score_diagnostik, prog.score_materi, prog.score_tugas_materi,
                       prog.score_formatif, prog.score_refleksi, prog.file_materi_siswa,
                       prog.updated_at as last_active
                FROM lms_tugas t
                JOIN penempatan_siswa ps ON t.id_kelas = ps.id_kelas
                JOIN siswa s ON ps.id_siswa = s.id_siswa
                JOIN kelas k ON ps.id_kelas = k.id_kelas
                LEFT JOIN mapel mp ON t.id_mapel = mp.id_mapel
                LEFT JOIN lms_materi m ON t.id_materi = m.id_materi
                LEFT JOIN lms_pengumpulan p ON t.id_tugas = p.id_tugas AND s.id_siswa = p.id_siswa
                LEFT JOIN lms_tugas_progress prog ON t.id_tugas = prog.id_tugas AND s.id_siswa = prog.id_siswa
                WHERE t.id_tugas = ? AND s.id_siswa = ?
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas, $id_siswa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getActiveTasksProgress($pdo, $guru_id = null) {
        $sql = "SELECT t.id_tugas, t.judul_tugas, t.deadline, k.nama_kelas,
                       (SELECT COUNT(*) FROM penempatan_siswa ps WHERE ps.id_kelas = t.id_kelas) as total_siswa,
                       (SELECT COUNT(*) FROM lms_pengumpulan p WHERE p.id_tugas = t.id_tugas) as total_submit
                FROM lms_tugas t
                JOIN kelas k ON t.id_kelas = k.id_kelas
                WHERE (t.id_guru = ? OR ? IS NULL) AND t.status = 'Aktif'
                ORDER BY t.created_at DESC LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$guru_id, $guru_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- HELPER UNTUK KOREKSI GURU ---

    public static function getStudentDiagnostikAnswers($pdo, $id_tugas, $id_siswa) {
        $sql = "SELECT * FROM lms_tugas_jawaban_text WHERE id_tugas = ? AND id_siswa = ? AND tipe = 'diagnostik' ORDER BY created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas, $id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStudentEssayAnswers($pdo, $id_tugas, $id_siswa) {
        $sql = "SELECT * FROM lms_tugas_jawaban_essay WHERE id_tugas = ? AND id_siswa = ? ORDER BY stage ASC, created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tugas, $id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStudentFormatifResults($pdo, $id_materi, $id_siswa) {
        $sql = "SELECT j.*, s.pertanyaan, s.kunci_jawaban, s.tipe
                FROM lms_materi_jawaban j
                JOIN lms_materi_soal s ON j.id_soal = s.id_soal
                WHERE s.id_materi = ? AND j.id_siswa = ?
                ORDER BY s.nomor_urut ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_materi, $id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStudentRefleksiAnswers($pdo, $id_materi, $id_siswa) {
        $sql = "SELECT * FROM lms_materi_refleksi WHERE id_materi = ? AND id_siswa = ? ORDER BY id_refleksi ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_materi, $id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveDetailedScores($pdo, $id_tugas, $id_siswa, $scores) {
        $sql = "UPDATE lms_tugas_progress 
                SET score_diagnostik = ?, score_materi = ?, score_tugas_materi = ?, score_formatif = ?, score_refleksi = ?, updated_at = NOW() 
                WHERE id_tugas = ? AND id_siswa = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $scores['score_diagnostik'] ?? 0,
            $scores['score_materi'] ?? 0,
            $scores['score_tugas_materi'] ?? 0,
            $scores['score_formatif'] ?? 0,
            $scores['score_refleksi'] ?? 0,
            $id_tugas,
            $id_siswa
        ]);
        return $stmt->rowCount() > 0;
    }

    // ============================================================
    // ⚡ INTEGRASI PRESENSI LMS & AKTIVITAS BELAJAR SISWA
    // ============================================================

    /**
     * Merekam Check-in siswa saat membuka modul materi / tugas LMS
     */
    public static function recordStudentCheckin($pdo, $id_materi, $id_siswa, $id_kelas = null) {
        if (!$id_materi || !$id_siswa) return false;
        
        $today = date('Y-m-d');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Cek apakah sudah pernah check-in hari ini
        $stmt = $pdo->prepare("SELECT id_akses FROM lms_materi_akses WHERE id_materi = ? AND id_siswa = ? AND tanggal = ? LIMIT 1");
        $stmt->execute([$id_materi, $id_siswa, $today]);
        $existing_id = $stmt->fetchColumn();

        if ($existing_id) {
            // Update progress minimal
            $pdo->prepare("UPDATE lms_materi_akses SET progress_persen = GREATEST(progress_persen, 30) WHERE id_akses = ?")
                ->execute([$existing_id]);
            return $existing_id;
        } else {
            // Insert Check-in baru
            $stmt_ins = $pdo->prepare("INSERT INTO lms_materi_akses (id_materi, id_siswa, id_kelas, tanggal, checkin_at, status_belajar, progress_persen, ip_address) 
                                       VALUES (?, ?, ?, ?, NOW(), 'Sedang Belajar', 30, ?)");
            $stmt_ins->execute([$id_materi, $id_siswa, $id_kelas, $today, $ip]);
            return $pdo->lastInsertId();
        }
    }

    /**
     * Merekam Check-out siswa saat menuntaskan kuis / refleksi / tugas LMS
     */
    public static function recordStudentCheckout($pdo, $id_materi, $id_siswa) {
        if (!$id_materi || !$id_siswa) return false;

        $today = date('Y-m-d');
        $stmt = $pdo->prepare("
            UPDATE lms_materi_akses 
            SET checkout_at = NOW(),
                durasi_menit = GREATEST(1, TIMESTAMPDIFF(MINUTE, checkin_at, NOW())),
                status_belajar = 'Tuntas',
                progress_persen = 100
            WHERE id_materi = ? AND id_siswa = ? AND tanggal = ?
        ");
        $stmt->execute([$id_materi, $id_siswa, $today]);

        // Jika siswa submit kuis tapi belum tercatat check-in, buatkan record tuntas langsung
        if ($stmt->rowCount() == 0) {
            $stmt_ins = $pdo->prepare("INSERT INTO lms_materi_akses (id_materi, id_siswa, tanggal, checkin_at, checkout_at, durasi_menit, status_belajar, progress_persen) 
                                       VALUES (?, ?, ?, NOW() - INTERVAL 15 MINUTE, NOW(), 15, 'Tuntas', 100)");
            $stmt_ins->execute([$id_materi, $id_siswa, $today]);
        }
        return true;
    }

    /**
     * Mengambil status presensi LMS semua siswa di suatu kelas pada materi & tanggal tertentu
     * Dilengkapi cross-check surat izin/sakit dari data Guru Piket
     */
    public static function getLmsAttendanceByMateriKelas($pdo, $id_materi, $id_kelas, $tanggal) {
        // 1. Ambil seluruh siswa aktif di kelas
        $stmt_s = $pdo->prepare("
            SELECT s.id_siswa, s.nama, s.nisn, s.nipd 
            FROM siswa s 
            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
            WHERE ps.id_kelas = ? AND ps.status_penempatan = 'Aktif'
            ORDER BY s.nama ASC
        ");
        $stmt_s->execute([$id_kelas]);
        $siswa_list = $stmt_s->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil catatan akses LMS untuk materi & tanggal ini
        $stmt_a = $pdo->prepare("
            SELECT id_siswa, checkin_at, checkout_at, durasi_menit, status_belajar, progress_persen 
            FROM lms_materi_akses 
            WHERE id_materi = ? AND tanggal = ?
        ");
        $stmt_a->execute([$id_materi, $tanggal]);
        $lms_map = [];
        foreach ($stmt_a->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $lms_map[$row['id_siswa']] = $row;
        }

        // 3. Ambil catatan sakit/izin dari data Guru Piket pada tanggal ini
        $stmt_p = $pdo->prepare("
            SELECT id_siswa, status, keterangan 
            FROM absensi_siswa_piket 
            WHERE id_kelas = ? AND tanggal = ? AND status IN ('Sakit', 'Izin')
        ");
        $stmt_p->execute([$id_kelas, $tanggal]);
        $piket_map = [];
        foreach ($stmt_p->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $piket_map[$row['id_siswa']] = $row;
        }

        $result = [];
        $summary = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];

        foreach ($siswa_list as $s) {
            $id_s = $s['id_siswa'];
            $lms = $lms_map[$id_s] ?? null;
            $piket = $piket_map[$id_s] ?? null;

            if ($piket) {
                // Ada catatan Sakit / Izin dari Piket
                $status = $piket['status'];
                $label = "Piket: " . $piket['status'] . ($piket['keterangan'] ? " ({$piket['keterangan']})" : "");
                $badge_type = ($status == 'Sakit') ? 'warning' : 'info';
            } elseif ($lms) {
                // Siswa mengakses LMS
                $status = 'Hadir';
                $checkin_time = date('H:i', strtotime($lms['checkin_at']));
                $durasi = $lms['durasi_menit'] ?: 1;
                if ($lms['status_belajar'] == 'Tuntas') {
                    $label = "LMS: Hadir (Check-in {$checkin_time} • Tuntas {$durasi}m)";
                    $badge_type = 'success';
                } else {
                    $label = "LMS: Hadir (Check-in {$checkin_time} • Sedang Belajar)";
                    $badge_type = 'primary';
                }
            } else {
                // Tidak ada akses LMS dan tidak izin di piket
                $status = 'Alpa';
                $label = "Tidak Mengakses Materi LMS";
                $badge_type = 'danger';
            }

            if ($status == 'Hadir') $summary['hadir']++;
            elseif ($status == 'Sakit') $summary['sakit']++;
            elseif ($status == 'Izin') $summary['izin']++;
            else $summary['alpa']++;

            $result[$id_s] = [
                'id_siswa' => $id_s,
                'nama' => $s['nama'],
                'status' => $status,
                'label' => $label,
                'badge_type' => $badge_type,
                'lms_data' => $lms
            ];
        }

        return [
            'status' => 'ok',
            'data' => $result,
            'summary' => $summary
        ];
    }

    // ============================================================
    // 📖 1. HIERARKI MODUL & BAHAN AJAR (DAFTAR ISI BUKU TEKS)
    // ============================================================

    /**
     * Mengambil daftar Bab, Sub-Bab, dan Materi dalam bentuk Pohon Struktur Buku
     */
    public static function getStrukturBuku($pdo, $id_mapel, $tingkat = null, $semester = null, $id_guru = null) {
        return self::getCurriculumTree($pdo, $id_mapel, $tingkat, $semester, $id_guru);
    }

    public static function getCurriculumTree($pdo, $id_mapel, $tingkat = null, $semester = null, $id_guru = null) {
        $params = [$id_mapel];
        $where_clauses = ["b.id_mapel = ?"];

        if ($tingkat) {
            $where_clauses[] = "b.tingkat = ?";
            $params[] = $tingkat;
        }
        if ($semester) {
            $where_clauses[] = "(b.semester = ? OR b.semester = ?)";
            $sem_alias = ($semester === 'Ganjil' || $semester === '1') ? ['Ganjil', '1'] : ['Genap', '2'];
            $params[] = $sem_alias[0];
            $params[] = $sem_alias[1];
        }
        if ($id_guru) {
            $where_clauses[] = "(b.id_guru = ? OR b.id_guru IS NULL OR b.id_guru = 0)";
            $params[] = $id_guru;
        }

        $where_sql = implode(" AND ", $where_clauses);

        // 1. Ambil Bab
        $stmt_bab = $pdo->prepare("
            SELECT b.*, m.nama_mapel 
            FROM lms_bab b 
            JOIN mapel m ON b.id_mapel = m.id_mapel
            WHERE {$where_sql}
            ORDER BY b.urutan_bab ASC, b.id_bab ASC
        ");
        $stmt_bab->execute($params);
        $bab_list = $stmt_bab->fetchAll(PDO::FETCH_ASSOC);

        // Standalone materi (yang belum punya bab / id_bab = 0 / NULL) khusus mapel & semester ini
        $sem_cond = "";
        $p_standalone = [$id_mapel];
        if ($tingkat) {
            $sem_cond .= " AND m.tingkat = ?";
            $p_standalone[] = $tingkat;
        }
        if ($semester) {
            $sem_alias = ($semester === 'Ganjil' || $semester === '1') ? ['Ganjil', '1'] : ['Genap', '2'];
            $sem_cond .= " AND (m.semester = ? OR m.semester = ?)";
            $p_standalone[] = $sem_alias[0];
            $p_standalone[] = $sem_alias[1];
        }

        if (empty($bab_list)) {
            // Cek apakah ada materi yang belum dikelompokkan ke dalam bab (Uncategorized)
            $stmt_mat_standalone = $pdo->prepare("
                SELECT m.*, cp.deskripsi_cp 
                FROM lms_materi m
                LEFT JOIN capaian_pembelajaran cp ON m.id_cp = cp.id_cp
                WHERE m.id_mapel = ? AND (m.id_bab IS NULL OR m.id_bab = 0) {$sem_cond}
                ORDER BY m.created_at DESC
            ");
            $stmt_mat_standalone->execute($p_standalone);
            $standalone_materi = $stmt_mat_standalone->fetchAll(PDO::FETCH_ASSOC);

            return [
                'bab_list' => [],
                'standalone_materi' => $standalone_materi
            ];
        }

        $bab_ids = array_column($bab_list, 'id_bab');
        $placeholders = implode(',', array_fill(0, count($bab_ids), '?'));

        // 2. Ambil Sub-Bab
        $stmt_sub = $pdo->prepare("
            SELECT * FROM lms_sub_bab 
            WHERE id_bab IN ({$placeholders})
            ORDER BY urutan_sub ASC, id_sub_bab ASC
        ");
        $stmt_sub->execute($bab_ids);
        $sub_list = $stmt_sub->fetchAll(PDO::FETCH_ASSOC);

        $sub_by_bab = [];
        foreach ($sub_list as $s) {
            $sub_by_bab[$s['id_bab']][] = $s;
        }

        // 3. Ambil Materi terkait
        $stmt_mat = $pdo->prepare("
            SELECT m.*, cp.deskripsi_cp 
            FROM lms_materi m 
            LEFT JOIN capaian_pembelajaran cp ON m.id_cp = cp.id_cp
            WHERE m.id_bab IN ({$placeholders}) OR (m.id_mapel = ? AND (m.id_bab IS NULL OR m.id_bab = 0) {$sem_cond})
            ORDER BY m.urutan_materi ASC, m.id_materi ASC
        ");
        $p_mat = array_merge($bab_ids, $p_standalone);
        $stmt_mat->execute($p_mat);
        $all_materi = $stmt_mat->fetchAll(PDO::FETCH_ASSOC);

        // Resolve TP IDs jika tp_manual kosong
        $all_tp_ids = [];
        foreach ($all_materi as $m) {
            if (!empty($m['id_tp'])) {
                foreach (explode(',', $m['id_tp']) as $tid) {
                    $tid = trim($tid);
                    if (is_numeric($tid)) $all_tp_ids[$tid] = $tid;
                }
            }
        }
        $tp_map = [];
        if (!empty($all_tp_ids)) {
            $tp_placeholders = implode(',', array_fill(0, count($all_tp_ids), '?'));
            $stmt_tps = $pdo->prepare("SELECT id_tp, kode_tp, deskripsi_tp FROM tujuan_pembelajaran WHERE id_tp IN ($tp_placeholders)");
            $stmt_tps->execute(array_values($all_tp_ids));
            foreach ($stmt_tps->fetchAll(PDO::FETCH_ASSOC) as $row_tp) {
                $tp_map[$row_tp['id_tp']] = ($row_tp['kode_tp'] ? $row_tp['kode_tp'] . '. ' : '') . $row_tp['deskripsi_tp'];
            }
        }

        $materi_by_sub = [];
        $materi_by_bab_only = [];
        $standalone_materi = [];

        foreach ($all_materi as &$m) {
            if (empty($m['cp_manual']) && !empty($m['deskripsi_cp'])) {
                $m['cp_manual'] = $m['deskripsi_cp'];
            }
            if (empty($m['tp_manual']) && !empty($m['id_tp'])) {
                $t_texts = [];
                foreach (explode(',', $m['id_tp']) as $tid) {
                    $tid = trim($tid);
                    if (isset($tp_map[$tid])) $t_texts[] = $tp_map[$tid];
                }
                if (!empty($t_texts)) {
                    $m['tp_manual'] = implode("\n", $t_texts);
                }
            }

            if ($m['id_sub_bab']) {
                $materi_by_sub[$m['id_sub_bab']][] = $m;
            } elseif ($m['id_bab']) {
                $materi_by_bab_only[$m['id_bab']][] = $m;
            } else {
                $standalone_materi[] = $m;
            }
        }
        unset($m);

        // Assembling tree
        foreach ($bab_list as &$bab) {
            $id_b = $bab['id_bab'];
            $bab['sub_bab_list'] = $sub_by_bab[$id_b] ?? [];
            $bab['materi_direct'] = $materi_by_bab_only[$id_b] ?? [];

            foreach ($bab['sub_bab_list'] as &$sub) {
                $id_sub = $sub['id_sub_bab'];
                $sub['materi_list'] = $materi_by_sub[$id_sub] ?? [];
            }
        }

        return [
            'bab_list' => $bab_list,
            'standalone_materi' => $standalone_materi
        ];
    }

    public static function saveBab($pdo, $data) {
        if (!empty($data['id_bab'])) {
            $stmt = $pdo->prepare("
                UPDATE lms_bab 
                SET id_mapel = ?, tingkat = ?, semester = ?, urutan_bab = ?, judul_bab = ?, deskripsi = ?
                WHERE id_bab = ?
            ");
            $stmt->execute([
                $data['id_mapel'], $data['tingkat'], $data['semester'] ?? 'Ganjil', 
                $data['urutan_bab'] ?? 1, $data['judul_bab'], $data['deskripsi'] ?? null, 
                $data['id_bab']
            ]);
            return $data['id_bab'];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO lms_bab (id_mapel, tingkat, semester, urutan_bab, judul_bab, deskripsi, id_guru)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['id_mapel'], $data['tingkat'], $data['semester'] ?? 'Ganjil', 
                $data['urutan_bab'] ?? 1, $data['judul_bab'], $data['deskripsi'] ?? null, 
                $data['id_guru'] ?? null
            ]);
            return $pdo->lastInsertId();
        }
    }

    public static function deleteBab($pdo, $id_bab) {
        // Unlink materi
        $pdo->prepare("UPDATE lms_materi SET id_bab = NULL, id_sub_bab = NULL WHERE id_bab = ?")->execute([$id_bab]);
        // Delete sub bab
        $pdo->prepare("DELETE FROM lms_sub_bab WHERE id_bab = ?")->execute([$id_bab]);
        // Delete bab
        $stmt = $pdo->prepare("DELETE FROM lms_bab WHERE id_bab = ?");
        return $stmt->execute([$id_bab]);
    }

    public static function saveSubBab($pdo, $data) {
        if (!empty($data['id_sub_bab'])) {
            $stmt = $pdo->prepare("
                UPDATE lms_sub_bab 
                SET id_bab = ?, urutan_sub = ?, judul_sub_bab = ?, deskripsi = ?
                WHERE id_sub_bab = ?
            ");
            $stmt->execute([
                $data['id_bab'], $data['urutan_sub'] ?? 1, $data['judul_sub_bab'], 
                $data['deskripsi'] ?? null, $data['id_sub_bab']
            ]);
            return $data['id_sub_bab'];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO lms_sub_bab (id_bab, urutan_sub, judul_sub_bab, deskripsi)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['id_bab'], $data['urutan_sub'] ?? 1, $data['judul_sub_bab'], $data['deskripsi'] ?? null
            ]);
            return $pdo->lastInsertId();
        }
    }

    public static function deleteSubBab($pdo, $id_sub_bab) {
        $pdo->prepare("UPDATE lms_materi SET id_sub_bab = NULL WHERE id_sub_bab = ?")->execute([$id_sub_bab]);
        $stmt = $pdo->prepare("DELETE FROM lms_sub_bab WHERE id_sub_bab = ?");
        return $stmt->execute([$id_sub_bab]);
    }

    public static function getBabSimpleByMapel($pdo, $id_mapel, $tingkat = null) {
        $sql = "SELECT id_bab, judul_bab, urutan_bab, semester FROM lms_bab WHERE id_mapel = ? ";
        $params = [$id_mapel];
        if ($tingkat) {
            $sql .= "AND tingkat = ? ";
            $params[] = $tingkat;
        }
        $sql .= "ORDER BY semester ASC, urutan_bab ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSubBabByBab($pdo, $id_bab) {
        $stmt = $pdo->prepare("SELECT id_sub_bab, judul_sub_bab, urutan_sub FROM lms_sub_bab WHERE id_bab = ? ORDER BY urutan_sub ASC");
        $stmt->execute([$id_bab]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 💬 2. FORUM DISKUSI & TANYA JAWAB PER MATERI
    // ============================================================

    public static function getDiskusiByMateri($pdo, $id_materi) {
        $stmt = $pdo->prepare("
            SELECT d.*, p.username, p.nama_pengguna,
                   s.nama AS nama_siswa, g.nama AS nama_guru,
                   IF(g.id_guru IS NOT NULL, 'Guru', IF(s.id_siswa IS NOT NULL, 'Siswa', 'Admin')) AS peran
            FROM lms_materi_diskusi d
            JOIN pengguna p ON d.id_user = p.id_pengguna
            LEFT JOIN siswa s ON p.id_pengguna = s.id_pengguna
            LEFT JOIN guru g ON p.id_pengguna = g.id_pengguna
            WHERE d.id_materi = ?
            ORDER BY d.created_at ASC
        ");
        $stmt->execute([$id_materi]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group into parents and replies
        $parents = [];
        $replies = [];
        foreach ($rows as $r) {
            $displayName = $r['nama_guru'] ?: ($r['nama_siswa'] ?: ($r['nama_pengguna'] ?: $r['username']));
            $r['display_name'] = $displayName;
            if ($r['parent_id']) {
                $replies[$r['parent_id']][] = $r;
            } else {
                $parents[] = $r;
            }
        }

        foreach ($parents as &$p) {
            $p['replies'] = $replies[$p['id_diskusi']] ?? [];
        }

        return $parents;
    }

    public static function postDiskusi($pdo, $id_materi, $id_user, $pesan, $parent_id = null) {
        $stmt = $pdo->prepare("
            INSERT INTO lms_materi_diskusi (id_materi, id_user, parent_id, pesan)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$id_materi, $id_user, $parent_id ?: null, $pesan]);
        return $pdo->lastInsertId();
    }

    public static function toggleVerifyDiskusi($pdo, $id_diskusi) {
        $stmt = $pdo->prepare("UPDATE lms_materi_diskusi SET is_guru_verified = IF(is_guru_verified = 1, 0, 1) WHERE id_diskusi = ?");
        return $stmt->execute([$id_diskusi]);
    }

    public static function deleteDiskusi($pdo, $id_diskusi) {
        // Delete child replies
        $pdo->prepare("DELETE FROM lms_materi_diskusi WHERE parent_id = ?")->execute([$id_diskusi]);
        $stmt = $pdo->prepare("DELETE FROM lms_materi_diskusi WHERE id_diskusi = ?");
        return $stmt->execute([$id_diskusi]);
    }

    // ============================================================
    // ⚡ 3. SINKRONISASI NILAI FORMATIF LMS KE BUKU NILAI GURU
    // ============================================================

    public static function getNilaiFormatifByMateriKelas($pdo, $id_materi, $id_kelas) {
        // 1. Ambil daftar siswa aktif di kelas
        $stmt_s = $pdo->prepare("
            SELECT s.id_siswa, s.nama, s.nisn, s.nipd 
            FROM siswa s 
            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
            WHERE ps.id_kelas = ? AND ps.status_penempatan = 'Aktif'
            ORDER BY s.nama ASC
        ");
        $stmt_s->execute([$id_kelas]);
        $siswa_list = $stmt_s->fetchAll(PDO::FETCH_ASSOC);

        // 2. Ambil nilai tes formatif / kuis dari lms_materi_jawaban
        $stmt_j = $pdo->prepare("
            SELECT j.id_siswa, j.skor_akhir, j.created_at, j.status_koreksi
            FROM lms_materi_jawaban j
            WHERE j.id_materi = ?
        ");
        $stmt_j->execute([$id_materi]);
        $skor_map = [];
        foreach ($stmt_j->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $skor_map[$row['id_siswa']] = $row;
        }

        $result = [];
        foreach ($siswa_list as $s) {
            $id_s = $s['id_siswa'];
            $jawaban = $skor_map[$id_s] ?? null;
            $result[$id_s] = [
                'id_siswa' => $id_s,
                'nama' => $s['nama'],
                'nisn' => $s['nisn'],
                'nilai' => $jawaban ? round($jawaban['skor_akhir'], 1) : null,
                'sudah_mengerjakan' => $jawaban !== null,
                'status_koreksi' => $jawaban['status_koreksi'] ?? 'Belum Mengerjakan'
            ];
        }

        return [
            'status' => 'ok',
            'data' => $result
        ];
    }

    // ============================================================
    // 🎯 4. INTEGRASI AI GENERATOR SOAL LANGSUNG KE BANK SOAL CBT (SUMATIF)
    // ============================================================

    public static function saveAiGeneratedSoalToCbt($pdo, $id_bank, $soal_list) {
        if (!$id_bank || empty($soal_list) || !is_array($soal_list)) {
            throw new Exception("ID Bank Soal atau data soal tidak valid.");
        }

        $stmt_max_urut = $pdo->prepare("SELECT COALESCE(MAX(nomor_urut), 0) FROM cbt_soal WHERE id_bank = ?");
        $stmt_max_urut->execute([$id_bank]);
        $last_urut = (int)$stmt_max_urut->fetchColumn();

        $stmt_ins_soal = $pdo->prepare("
            INSERT INTO cbt_soal (id_bank, nomor_urut, tipe_soal, pertanyaan, is_acak_soal, is_acak_opsi, bobot, tingkat_kesulitan)
            VALUES (?, ?, ?, ?, 1, 1, ?, ?)
        ");

        $stmt_ins_opsi = $pdo->prepare("
            INSERT INTO cbt_soal_opsi (id_soal, label, isi_opsi, is_benar)
            VALUES (?, ?, ?, ?)
        ");

        $pdo->beginTransaction();
        try {
            $count_pg = 0;
            $count_esai = 0;

            foreach ($soal_list as $item) {
                $last_urut++;
                $tipe_soal = strtolower($item['tipe_soal'] ?? 'pg'); // pg, essay, tf, matching
                if ($tipe_soal == 'pilihan_ganda') $tipe_soal = 'pg';
                if ($tipe_soal == 'uraian') $tipe_soal = 'essay';

                $pertanyaan = $item['pertanyaan'] ?? $item['soal'] ?? '';
                $bobot = (int)($item['bobot'] ?? 10);
                $kesulitan = strtolower($item['tingkat_kesulitan'] ?? 'sedang');
                if (!in_array($kesulitan, ['mudah', 'sedang', 'sulit'])) $kesulitan = 'sedang';

                $stmt_ins_soal->execute([
                    $id_bank,
                    $last_urut,
                    $tipe_soal,
                    $pertanyaan,
                    $bobot,
                    $kesulitan
                ]);
                $id_soal = $pdo->lastInsertId();

                if ($tipe_soal === 'pg' || $tipe_soal === 'tf') {
                    $count_pg++;
                    $opsi_list = $item['opsi'] ?? $item['pilihan'] ?? [];
                    $kunci = strtoupper(trim($item['kunci_jawaban'] ?? $item['jawaban_benar'] ?? 'A'));

                    foreach ($opsi_list as $idx => $isi_opsi) {
                        $label = is_string($idx) && strlen($idx) === 1 ? strtoupper($idx) : chr(65 + (int)$idx);
                        $is_benar = ($label === $kunci) ? 1 : 0;

                        $stmt_ins_opsi->execute([
                            $id_soal,
                            $label,
                            $isi_opsi,
                            $is_benar
                        ]);
                    }
                } elseif ($tipe_soal === 'matching') {
                    $count_pg++;
                    // Menyimpan pasangan premis-respon sebagai opsi
                    $pairs = $item['pasangan'] ?? $item['pairs'] ?? [];
                    foreach ($pairs as $idx => $pair) {
                        $label = chr(65 + $idx);
                        $isi = (is_array($pair) ? ($pair['premis'] . ' === ' . $pair['respon']) : $pair);
                        $stmt_ins_opsi->execute([$id_soal, $label, $isi, 1]);
                    }
                } else {
                    $count_esai++;
                    // Jika ada kunci pembahasan essay, simpan sebagai opsi referensi
                    if (!empty($item['kunci_jawaban']) || !empty($item['rubrik'])) {
                        $rubrik = $item['rubrik'] ?? $item['kunci_jawaban'];
                        $stmt_ins_opsi->execute([$id_soal, 'RUBRIK', $rubrik, 1]);
                    }
                }
            }

            // Update statistik bank soal
            $pdo->prepare("
                UPDATE cbt_bank_soal 
                SET jml_pg = jml_pg + ?, jml_esai = jml_esai + ?
                WHERE id_bank = ?
            ")->execute([$count_pg, $count_esai, $id_bank]);

            $pdo->commit();
            return [
                'status' => 'ok',
                'inserted' => count($soal_list),
                'pg_count' => $count_pg,
                'esai_count' => $count_esai
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // ============================================================
    // 🪜 5. LEARNING PATH (TITIAN TANGGA) PROGRES SISWA
    // ============================================================

    public static function getMateriProgress($pdo, $id_materi, $id_siswa, $id_tugas = 0) {
        if (!$id_siswa || !$id_materi) {
            return [
                'stage_1_orientasi' => 1,
                'stage_2_video' => 0,
                'stage_3_materi' => 0,
                'stage_4_formatif' => 0,
                'stage_5_diskusi' => 0,
                'stage_6_refleksi' => 0,
                'is_completed' => 0,
                'current_stage' => 1,
                'percent' => 16
            ];
        }

        $stmt = $pdo->prepare("
            SELECT * FROM lms_materi_progress 
            WHERE id_materi = ? AND id_siswa = ? AND id_tugas = ?
            LIMIT 1
        ");
        $stmt->execute([$id_materi, $id_siswa, (int)$id_tugas]);
        $prog = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prog) {
            $pdo->prepare("
                INSERT IGNORE INTO lms_materi_progress (id_materi, id_siswa, id_tugas, stage_1_orientasi, current_stage)
                VALUES (?, ?, ?, 1, 1)
            ")->execute([$id_materi, $id_siswa, (int)$id_tugas]);
            
            $prog = [
                'stage_1_orientasi' => 1,
                'stage_2_video' => 0,
                'stage_3_materi' => 0,
                'stage_4_formatif' => 0,
                'stage_5_diskusi' => 0,
                'stage_6_refleksi' => 0,
                'is_completed' => 0,
                'current_stage' => 1
            ];
        }

        $completedCount = 0;
        $keys = ['stage_1_orientasi','stage_2_video','stage_3_materi','stage_4_formatif','stage_5_diskusi','stage_6_refleksi'];
        foreach ($keys as $k) {
            if (!empty($prog[$k])) $completedCount++;
        }
        
        if ((int)$id_tugas === 0) {
            // Mode Mandiri: Berfokus pada 3 Path Inti Belajar (Orientasi, Video, Literasi Teks)
            $completedMandiri = 0;
            foreach (['stage_1_orientasi','stage_2_video','stage_3_materi'] as $k) {
                if (!empty($prog[$k])) $completedMandiri++;
            }
            $prog['percent'] = round((min(3, $completedMandiri) / 3) * 100);
        } else {
            // Mode Penugasan Resmi: 6 Path Lengkap Terbimbing
            $prog['percent'] = round(($completedCount / 6) * 100);
        }

        return $prog;
    }

    public static function markMateriStage($pdo, $id_materi, $id_siswa, $stage, $id_tugas = 0, $nilai_formatif = null, $foto_presensi = null) {
        $stageNames = [
            1 => 'stage_1_orientasi',
            2 => 'stage_2_video',
            3 => 'stage_3_materi',
            4 => 'stage_4_formatif',
            5 => 'stage_5_diskusi',
            6 => 'stage_6_refleksi'
        ];
        
        $stageCol = $stageNames[(int)$stage] ?? 'stage_1_orientasi';
        $nextStage = min(6, (int)$stage + 1);
        $isCompleted = ((int)$id_tugas === 0 && (int)$stage >= 3) || ((int)$stage >= 6) ? 1 : 0;

        $stmt = $pdo->prepare("
            INSERT INTO lms_materi_progress (id_materi, id_siswa, id_tugas, {$stageCol}, current_stage, is_completed, nilai_formatif, foto_presensi)
            VALUES (?, ?, ?, 1, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                {$stageCol} = 1,
                current_stage = GREATEST(current_stage, ?),
                is_completed = IF(is_completed = 1 OR ? = 1, 1, 0),
                nilai_formatif = COALESCE(?, nilai_formatif),
                foto_presensi = COALESCE(?, foto_presensi),
                updated_at = NOW()
        ");
        $stmt->execute([
            $id_materi, $id_siswa, (int)$id_tugas, $nextStage, $isCompleted, $nilai_formatif, $foto_presensi,
            $nextStage, $isCompleted, $nilai_formatif, $foto_presensi
        ]);

        return self::getMateriProgress($pdo, $id_materi, $id_siswa, $id_tugas);
    }
}

