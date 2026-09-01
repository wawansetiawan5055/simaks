<?php
class ManajemenPenggunaModel {
    public static function getAllUsers($pdo) {
        $sql = "SELECT p.*, GROUP_CONCAT(pr.nama_peran SEPARATOR ', ') as roles
                FROM pengguna p
                LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
                LEFT JOIN peran pr ON pp.id_peran = pr.id_peran
                GROUP BY p.id_pengguna ORDER BY p.nama_pengguna ASC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findUser($pdo, $id) {
        // Ambil data user
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $data['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ambil peran user
        $stmt = $pdo->prepare("SELECT id_peran FROM pengguna_peran WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $data['user_roles'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // PERBAIKAN: Ambil data guru yang terhubung dengan user ini
        $stmt = $pdo->prepare("SELECT id_guru FROM guru WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $data['linked_guru_id'] = $stmt->fetchColumn();

        // Tambahkan ini: Ambil data siswa yang terhubung
        $stmt = $pdo->prepare("SELECT id_siswa FROM siswa WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        $data['linked_siswa_id'] = $stmt->fetchColumn();
        
        return $data;
    }

    public static function saveUser($pdo, $data) {
        $pdo->beginTransaction();
        try {
            $id_pengguna = $data['id_pengguna'] ?? null;
            $id_guru_terpilih = $data['id_guru'] ?? null;
            $id_siswa_terpilih = $data['id_siswa'] ?? null; // Tambahkan ini

            // Ambil nama pengguna berdasarkan pilihan (guru atau siswa)
            $nama_pengguna = '';
            if ($id_guru_terpilih) {
                $stmt = $pdo->prepare("SELECT nama FROM guru WHERE id_guru = ?");
                $stmt->execute([$id_guru_terpilih]);
                $nama_pengguna = $stmt->fetchColumn();
            } elseif ($id_siswa_terpilih) {
                $stmt = $pdo->prepare("SELECT nama FROM siswa WHERE id_siswa = ?");
                $stmt->execute([$id_siswa_terpilih]);
                $nama_pengguna = $stmt->fetchColumn();
            }


            // Bagian 1: Simpan ke tabel 'pengguna'
            if ($id_pengguna) { // Update
                $qr_token = 'SIMAKS-QR-' . bin2hex(random_bytes(16));
                $sql = "UPDATE pengguna SET username = ?, nama_pengguna = ?, email = ?, qr_token = IFNULL(NULLIF(qr_token, ''), ?) WHERE id_pengguna = ?";
                $params = [$data['username'], $nama_pengguna, $data['email'], $qr_token, $id_pengguna];
                if (!empty($data['password'])) {
                    $sql = "UPDATE pengguna SET username = ?, nama_pengguna = ?, email = ?, password = ?, qr_token = IFNULL(NULLIF(qr_token, ''), ?) WHERE id_pengguna = ?";
                    $params = [$data['username'], $nama_pengguna, $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $qr_token, $id_pengguna];
                }
            } else { // Insert
                $qr_token = 'SIMAKS-QR-' . bin2hex(random_bytes(16));
                $sql = "INSERT INTO pengguna (username, password, nama_pengguna, email, qr_token) VALUES (?, ?, ?, ?, ?)";
                $params = [$data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $nama_pengguna, $data['email'], $qr_token];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            if (!$id_pengguna) {
                $id_pengguna = $pdo->lastInsertId();
            }

            // Bagian 2: Hubungkan/Putuskan pengguna dengan guru
            // Putuskan hubungan lama jika ada
            $stmt = $pdo->prepare("UPDATE guru SET id_pengguna = NULL WHERE id_pengguna = ?");
            $stmt->execute([$id_pengguna]);
            // Buat hubungan baru jika guru dipilih
            if ($id_guru_terpilih) {
                $stmt = $pdo->prepare("UPDATE guru SET id_pengguna = ? WHERE id_guru = ?");
                $stmt->execute([$id_pengguna, $id_guru_terpilih]);
            }

            // Tambahkan ini: Hubungkan/Putuskan pengguna dengan siswa
            // Putuskan hubungan lama jika ada
            $stmt = $pdo->prepare("UPDATE siswa SET id_pengguna = NULL WHERE id_pengguna = ?");
            $stmt->execute([$id_pengguna]);
            // Buat hubungan baru jika siswa dipilih
            if ($id_siswa_terpilih) {
                $stmt = $pdo->prepare("UPDATE siswa SET id_pengguna = ? WHERE id_siswa = ?");
                $stmt->execute([$id_pengguna, $id_siswa_terpilih]);
            }

            // Bagian 3: Atur peran
            $stmt = $pdo->prepare("DELETE FROM pengguna_peran WHERE id_pengguna = ?");
            $stmt->execute([$id_pengguna]);
            if (!empty($data['roles'])) {
                $stmt = $pdo->prepare("INSERT INTO pengguna_peran (id_pengguna, id_peran) VALUES (?, ?)");
                foreach ($data['roles'] as $id_peran) {
                    $stmt->execute([$id_pengguna, $id_peran]);
                }
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Database error: " . $e->getMessage());
        }
    }

    public static function deleteUser($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id]);
    }

    public static function getAllRoles($pdo) {
        return $pdo->query("SELECT * FROM peran ORDER BY nama_peran")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUsersByType($pdo, $type) {
        $sql = "SELECT p.*, GROUP_CONCAT(pr.nama_peran SEPARATOR ', ') as roles, 
                       g.id_guru, s.id_siswa
                FROM pengguna p
                LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
                LEFT JOIN peran pr ON pp.id_peran = pr.id_peran
                LEFT JOIN guru g ON p.id_pengguna = g.id_pengguna
                LEFT JOIN siswa s ON p.id_pengguna = s.id_pengguna";
        
        if ($type == 'guru') {
            $sql .= " WHERE g.id_guru IS NOT NULL";
        } elseif ($type == 'siswa') {
            $sql .= " WHERE s.id_siswa IS NOT NULL";
        } else {
            // Sistem/Lainnya: Pengguna yang tidak terhubung ke guru maupun siswa
            $sql .= " WHERE g.id_guru IS NULL AND s.id_siswa IS NULL";
        }
        
        $sql .= " GROUP BY p.id_pengguna ORDER BY p.nama_pengguna ASC";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUsersForCard($pdo, $type = 'all', $id_kelas = null) {
        $sql = "SELECT p.id_pengguna, p.username, p.nama_pengguna, p.email, p.foto as foto_pengguna, p.qr_token,
                       GROUP_CONCAT(DISTINCT pr.nama_peran SEPARATOR ', ') as roles,
                       g.id_guru, g.nuptk, g.nik, g.kode_guru,
                       s.id_siswa, s.nisn, s.nipd,
                       COALESCE(s.jk, g.jk, '') AS jk,
                       (SELECT k.nama_kelas 
                        FROM penempatan_siswa ps 
                        JOIN kelas k ON ps.id_kelas = k.id_kelas 
                        WHERE ps.id_siswa = s.id_siswa 
                        ORDER BY ps.id_penempatan DESC LIMIT 1) AS nama_kelas
                FROM pengguna p
                LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
                LEFT JOIN peran pr ON pp.id_peran = pr.id_peran
                LEFT JOIN guru g ON p.id_pengguna = g.id_pengguna
                LEFT JOIN siswa s ON p.id_pengguna = s.id_pengguna";

        $where = [];
        $params = [];

        if ($type == 'guru') {
            $where[] = "g.id_guru IS NOT NULL";
        } elseif ($type == 'siswa') {
            $where[] = "s.id_siswa IS NOT NULL";
            if ($id_kelas) {
                $where[] = "s.id_siswa IN (SELECT id_siswa FROM penempatan_siswa WHERE id_kelas = ?)";
                $params[] = $id_kelas;
            }
        } elseif ($type == 'sistem') {
            $where[] = "g.id_guru IS NULL AND s.id_siswa IS NULL";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY p.id_pengguna ORDER BY p.nama_pengguna ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserDetailForCard($pdo, $id_pengguna) {
        $sql = "SELECT p.id_pengguna, p.username, p.nama_pengguna, p.email, p.foto as foto_pengguna, p.qr_token,
                       GROUP_CONCAT(DISTINCT pr.nama_peran SEPARATOR ', ') as roles,
                       g.id_guru, g.nuptk, g.nik, g.kode_guru,
                       s.id_siswa, s.nisn, s.nipd,
                       COALESCE(s.jk, g.jk, '') AS jk,
                       (SELECT k.nama_kelas 
                        FROM penempatan_siswa ps 
                        JOIN kelas k ON ps.id_kelas = k.id_kelas 
                        WHERE ps.id_siswa = s.id_siswa 
                        ORDER BY ps.id_penempatan DESC LIMIT 1) AS nama_kelas
                FROM pengguna p
                LEFT JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
                LEFT JOIN guru g ON p.id_pengguna = g.id_pengguna
                LEFT JOIN siswa s ON p.id_pengguna = s.id_pengguna
                WHERE p.id_pengguna = ?
                GROUP BY p.id_pengguna";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_pengguna]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function generateAccounts($pdo, $target, $password) {
        $pdo->beginTransaction();
        try {
            $default_pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $count = 0;

            if ($target == 'guru') {
                // Ambil guru yang tidak punya id_pengguna
                // Gunakan NIK, jika kosong pakai NUPTK, jika kosong pakai kode_guru sebagai username
                $sql = "SELECT id_guru, nama, nik, nuptk, kode_guru FROM guru WHERE id_pengguna IS NULL";
                $list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                
                // Ambil ID peran 'Guru'
                $role_stmt = $pdo->prepare("SELECT id_peran FROM peran WHERE nama_peran = 'Guru'");
                $role_stmt->execute();
                $id_peran_guru = $role_stmt->fetchColumn();

                foreach ($list as $g) {
                    $username = $g['nik'] ?: ($g['nuptk'] ?: $g['kode_guru']);
                    if (!$username) continue; // Skip jika tidak ada data unik

                    // Cek jika username sudah ada di tabel pengguna
                    $stmt_check = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE username = ?");
                    $stmt_check->execute([$username]);
                    if ($stmt_check->fetch()) continue;

                    // Insert ke pengguna
                    $qr_token = 'SIMAKS-QR-' . bin2hex(random_bytes(16));
                    $stmt_in = $pdo->prepare("INSERT INTO pengguna (username, password, nama_pengguna, qr_token) VALUES (?, ?, ?, ?)");
                    $stmt_in->execute([$username, $default_pass_hash, $g['nama'], $qr_token]);
                    $id_new_user = $pdo->lastInsertId();

                    // Hubungkan ke guru
                    $stmt_up = $pdo->prepare("UPDATE guru SET id_pengguna = ? WHERE id_guru = ?");
                    $stmt_up->execute([$id_new_user, $g['id_guru']]);

                    // Assign role Guru
                    if ($id_peran_guru) {
                        $stmt_role = $pdo->prepare("INSERT INTO pengguna_peran (id_pengguna, id_peran) VALUES (?, ?)");
                        $stmt_role->execute([$id_new_user, $id_peran_guru]);
                    }
                    $count++;
                }
            } elseif ($target == 'siswa') {
                // Ambil siswa yang tidak punya id_pengguna dan statusnya AKTIF
                // Gunakan NISN, jika kosong pakai NIPD
                $sql = "SELECT id_siswa, nama, nisn, nipd FROM siswa WHERE id_pengguna IS NULL AND status_aktif = 'Aktif'";
                $list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                // Ambil ID peran 'Siswa'
                $role_stmt = $pdo->prepare("SELECT id_peran FROM peran WHERE nama_peran = 'Siswa'");
                $role_stmt->execute();
                $id_peran_siswa = $role_stmt->fetchColumn();

                foreach ($list as $s) {
                    $username = $s['nisn'] ?: $s['nipd'];
                    if (!$username) continue;

                    // Cek jika username sudah ada
                    $stmt_check = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE username = ?");
                    $stmt_check->execute([$username]);
                    if ($stmt_check->fetch()) continue;

                    $qr_token = 'SIMAKS-QR-' . bin2hex(random_bytes(16));
                    $stmt_in = $pdo->prepare("INSERT INTO pengguna (username, password, nama_pengguna, qr_token) VALUES (?, ?, ?, ?)");
                    $stmt_in->execute([$username, $default_pass_hash, $s['nama'], $qr_token]);
                    $id_new_user = $pdo->lastInsertId();

                    $stmt_up = $pdo->prepare("UPDATE siswa SET id_pengguna = ? WHERE id_siswa = ?");
                    $stmt_up->execute([$id_new_user, $s['id_siswa']]);

                    if ($id_peran_siswa) {
                        $stmt_role = $pdo->prepare("INSERT INTO pengguna_peran (id_pengguna, id_peran) VALUES (?, ?)");
                        $stmt_role->execute([$id_new_user, $id_peran_siswa]);
                    }
                    $count++;
                }
            }

            $pdo->commit();
            return $count;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function cleanupTrialAccounts($pdo) {
        $pdo->beginTransaction();
        try {
            // Lindungi user 'admin' atau user dengan ID 1
            // Putuskan hubungan di guru & siswa dulu
            $stmt = $pdo->prepare("UPDATE guru SET id_pengguna = NULL WHERE id_pengguna != 1");
            $stmt->execute();
            $stmt = $pdo->prepare("UPDATE siswa SET id_pengguna = NULL WHERE id_pengguna != 1");
            $stmt->execute();

            // Hapus peran pengguna selain admin
            $stmt = $pdo->prepare("DELETE FROM pengguna_peran WHERE id_pengguna != 1");
            $stmt->execute();

            // Hapus pengguna selain admin
            $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna != 1");
            $stmt->execute();

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Mengambil guru yang belum punya akun ATAU guru yang saat ini sedang diedit
    public static function getAvailableGuru($pdo, $current_user_id = 0) {
        $sql = "SELECT id_guru, nama FROM guru WHERE id_pengguna IS NULL OR id_pengguna = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$current_user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tambahkan fungsi ini
    public static function getAvailableSiswa($pdo, $current_user_id = 0) {
        $sql = "SELECT id_siswa, nama FROM siswa WHERE id_pengguna IS NULL OR id_pengguna = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$current_user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
