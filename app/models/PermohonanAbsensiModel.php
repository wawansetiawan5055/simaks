<?php
/**
 * PermohonanAbsensiModel.php
 * Model untuk tabel permohonan_absensi
 */
class PermohonanAbsensiModel
{
    // -------------------------------------------------------
    // SISI SISWA
    // -------------------------------------------------------

    /**
     * Insert permohonan baru
     */
    public static function ajukan(PDO $pdo, array $data): int
    {
        $sql = "INSERT INTO permohonan_absensi
                    (id_siswa, jenis_absensi, jenis_izin, tanggal,
                     id_mapel, id_kelas, keterangan, foto_bukti, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['id_siswa'],
            $data['jenis_absensi'],
            $data['jenis_izin'],
            $data['tanggal'],
            $data['id_mapel'] ?: null,
            $data['id_kelas'] ?: null,
            $data['keterangan'] ?: null,
            $data['foto_bukti'] ?: null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Ambil riwayat permohonan milik siswa tertentu
     */
    public static function getRiwayatSiswa(PDO $pdo, int $id_siswa): array
    {
        $sql = "SELECT p.*,
                       (SELECT GROUP_CONCAT(m.nama_mapel SEPARATOR ', ') FROM mapel m WHERE FIND_IN_SET(m.id_mapel, p.id_mapel)) AS nama_mapel
                FROM permohonan_absensi p
                WHERE p.id_siswa = ?
                ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cek apakah ada permohonan duplikat (same siswa + tanggal + jenis)
     */
    public static function isDuplikat(PDO $pdo, int $id_siswa, string $tanggal, string $jenis_absensi, ?string $id_mapel = null): bool
    {
        $sql = "SELECT COUNT(*) FROM permohonan_absensi
                WHERE id_siswa = ? AND tanggal = ? AND jenis_absensi = ?
                  AND status != 'Ditolak'";
        $params = [$id_siswa, $tanggal, $jenis_absensi];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    // -------------------------------------------------------
    // SISI PETUGAS (Guru Piket, Wali Kelas, TU, Admin)
    // -------------------------------------------------------

    /**
     * Ambil semua permohonan dengan filter
     * @param array $filters ['status', 'tanggal', 'id_kelas', 'jenis_absensi']
     */
    public static function getDaftar(PDO $pdo, array $filters = []): array
    {
        $where  = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = "p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['tanggal'])) {
            $where[]  = "p.tanggal = ?";
            $params[] = $filters['tanggal'];
        }
        if (!empty($filters['id_kelas'])) {
            $where[]  = "p.id_kelas = ?";
            $params[] = $filters['id_kelas'];
        }
        if (!empty($filters['jenis_absensi'])) {
            $where[]  = "p.jenis_absensi = ?";
            $params[] = $filters['jenis_absensi'];
        }

        $sql = "SELECT p.*,
                       s.nama        AS nama_siswa,
                       s.nisn,
                       k.nama_kelas,
                       (SELECT GROUP_CONCAT(m.nama_mapel SEPARATOR ', ') FROM mapel m WHERE FIND_IN_SET(m.id_mapel, p.id_mapel)) AS nama_mapel,
                       pg.nama_pengguna AS nama_petugas
                FROM permohonan_absensi p
                JOIN siswa s ON p.id_siswa = s.id_siswa
                LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
                LEFT JOIN pengguna pg ON p.id_petugas = pg.id_pengguna
                WHERE " . implode(' AND ', $where) . "
                ORDER BY
                  FIELD(p.status, 'Menunggu', 'Disetujui', 'Ditolak'),
                  p.tanggal DESC,
                  p.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil 1 permohonan by ID (dengan data join)
     */
    public static function getById(PDO $pdo, int $id): ?array
    {
        $sql = "SELECT p.*,
                       s.nama        AS nama_siswa,
                       s.nisn,
                       k.nama_kelas,
                       (SELECT GROUP_CONCAT(m.nama_mapel SEPARATOR ', ') FROM mapel m WHERE FIND_IN_SET(m.id_mapel, p.id_mapel)) AS nama_mapel,
                       pg.nama_pengguna AS nama_petugas
                FROM permohonan_absensi p
                JOIN siswa s ON p.id_siswa = s.id_siswa
                LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
                LEFT JOIN pengguna pg ON p.id_petugas = pg.id_pengguna
                WHERE p.id_permohonan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Update status permohonan (approve / tolak)
     */
    public static function updateStatus(PDO $pdo, int $id, string $status, string $catatan, int $id_petugas): bool
    {
        $sql = "UPDATE permohonan_absensi
                SET status = ?, catatan_petugas = ?, id_petugas = ?, updated_at = NOW()
                WHERE id_permohonan = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$status, $catatan, $id_petugas, $id]);
    }

    /**
     * Terapkan permohonan yang disetujui ke tabel absensi
     * Jika sudah ada record absensi di tanggal itu → UPDATE
     * Jika belum ada → INSERT
     */
    public static function applyToAbsensi(PDO $pdo, array $p): void
    {
        if ($p['jenis_absensi'] === 'piket') {
            // Cek existing record
            $check = $pdo->prepare(
                "SELECT id_absensi FROM absensi_siswa_piket
                 WHERE id_siswa = ? AND tanggal = ?"
            );
            $check->execute([$p['id_siswa'], $p['tanggal']]);
            $existing = $check->fetch();

            if ($existing) {
                $pdo->prepare(
                    "UPDATE absensi_siswa_piket
                     SET status = ?, keterangan = ?
                     WHERE id_absensi = ?"
                )->execute([$p['jenis_izin'], $p['keterangan'] ?? '', $existing['id_absensi']]);
            } else {
                $pdo->prepare(
                    "INSERT INTO absensi_siswa_piket
                         (id_siswa, id_kelas, id_ta, tanggal, status, keterangan)
                     VALUES (?, ?, ?, ?, ?, ?)"
                )->execute([
                    $p['id_siswa'],
                    $p['id_kelas'],
                    $_SESSION['id_ta_aktif'] ?? 0,
                    $p['tanggal'],
                    $p['jenis_izin'],
                    $p['keterangan'] ?? '',
                ]);
            }
        } else {
            // absensi mapel (bisa multiple)
            $mapel_ids = explode(',', $p['id_mapel']);
            foreach ($mapel_ids as $m_id) {
                $m_id = trim($m_id);
                if (!$m_id) continue;

                $check = $pdo->prepare(
                    "SELECT id_absensi FROM absensi_siswa_mapel
                     WHERE id_siswa = ? AND tanggal = ? AND id_mapel = ?"
                );
                $check->execute([$p['id_siswa'], $p['tanggal'], $m_id]);
                $existing = $check->fetch();

                if ($existing) {
                    $pdo->prepare(
                        "UPDATE absensi_siswa_mapel
                         SET status = ?, keterangan = ?
                         WHERE id_absensi = ?"
                    )->execute([$p['jenis_izin'], $p['keterangan'] ?? '', $existing['id_absensi']]);
                } else {
                    $pdo->prepare(
                        "INSERT INTO absensi_siswa_mapel
                             (id_siswa, id_kelas, id_ta, id_mapel, tanggal, status, keterangan)
                         VALUES (?, ?, ?, ?, ?, ?, ?)"
                    )->execute([
                        $p['id_siswa'],
                        $p['id_kelas'],
                        $_SESSION['id_ta_aktif'] ?? 0,
                        $m_id,
                        $p['tanggal'],
                        $p['jenis_izin'],
                        $p['keterangan'] ?? '',
                    ]);
                }
            }
        }
    }

    /**
     * Ambil daftar mapel milik siswa (untuk dropdown)
     */
    public static function getMapelSiswa(PDO $pdo, int $id_siswa, int $id_ta): array
    {
        $sql = "SELECT DISTINCT m.id_mapel, m.nama_mapel
                FROM jadwal_mengajar j
                JOIN guru_mapel gm ON j.id_guru_mapel = gm.id_guru_mapel
                JOIN mapel m ON gm.id_mapel = m.id_mapel
                JOIN penempatan_siswa ps ON j.id_kelas = ps.id_kelas
                WHERE ps.id_siswa = ? AND gm.id_ta = ?
                ORDER BY m.nama_mapel";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil daftar kelas untuk filter
     */
    public static function getKelasList(PDO $pdo): array
    {
        $stmt = $pdo->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
