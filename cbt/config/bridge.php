<?php
/**
 * CBT - Bridge ke Database SIMAKS
 * Digunakan untuk mengambil data Siswa, Kelas, Mapel dari DB utama SIMAKS.
 * Jika CBT berjalan standalone (tanpa SIMAKS), fungsi ini akan mengembalikan data dari tabel lokal CBT.
 */
if (!function_exists('cbt_bridge_connect')) {
    function cbt_bridge_connect()
    {
        // Coba koneksi ke db_simaks (mode terintegrasi)
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'db_simaks';
        $user = getenv('DB_USER') ?: 'administrator';
        $pass = getenv('DB_PASS') ?: '20247166';

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            return null; // Jika gagal, CBT berjalan standalone
        }
    }
}

/**
 * Ambil daftar kelas.
 * Mode Terintegrasi: Dari tabel `kelas` di db_simaks, hanya yang ada di TA Aktif.
 * Mode Standalone: Dari tabel `cbt_kelas` di db_simaks_cbt.
 */
function cbt_get_kelas($pdo_cbt, $id_kelas = null)
{
    $pdo_simaks = cbt_bridge_connect();
    if ($pdo_simaks) {
        $sql = "SELECT k.id_kelas, k.nama_kelas, k.tingkat 
                FROM kelas k 
                WHERE k.id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1)";

        if (is_array($id_kelas)) {
            $placeholders = implode(',', array_fill(0, count($id_kelas), '?'));
            $stmt = $pdo_simaks->prepare($sql . " AND k.id_kelas IN ($placeholders) ORDER BY k.tingkat, k.nama_kelas");
            $stmt->execute($id_kelas);
        } elseif ($id_kelas) {
            $stmt = $pdo_simaks->prepare($sql . " AND k.id_kelas = ? ORDER BY k.tingkat, k.nama_kelas");
            $stmt->execute([$id_kelas]);
        } else {
            $stmt = $pdo_simaks->query($sql . " ORDER BY k.tingkat, k.nama_kelas");
        }
        return $stmt->fetchAll();
    }
    // Fallback standalone
    $sql = "SELECT id_kelas, nama_kelas, tingkat FROM cbt_kelas";
    if (is_array($id_kelas)) {
        $placeholders = implode(',', array_fill(0, count($id_kelas), '?'));
        $stmt = $pdo_cbt->prepare($sql . " WHERE id_kelas IN ($placeholders) ORDER BY tingkat, nama_kelas");
        $stmt->execute($id_kelas);
    } elseif ($id_kelas) {
        $stmt = $pdo_cbt->prepare($sql . " WHERE id_kelas = ? ORDER BY tingkat, nama_kelas");
        $stmt->execute([$id_kelas]);
    } else {
        $stmt = $pdo_cbt->query($sql . " ORDER BY tingkat, nama_kelas");
    }
    return $stmt->fetchAll();
}

/**
 * Ambil daftar siswa berdasarkan kelas - SELALU dari local cbt_siswa.
 * Data SIMAKS hanya diambil saat proses import eksplisit (lihat import_siswa_simaks).
 */
function cbt_get_siswa_by_kelas($pdo_cbt, $id_kelas = null)
{
    // Always use local cbt_siswa. SIMAKS bridge is only used during import.
    $sql = "SELECT s.id_siswa, s.nisn, s.nipd, s.nama_siswa, s.tempat_lahir, s.tanggal_lahir,
                   IFNULL(s.no_peserta, '') as no_peserta, IFNULL(s.ruang, '') as ruang,
                   IFNULL(s.sesi, '') as sesi, IFNULL(s.jurusan, '') as jurusan,
                   IFNULL(s.foto, '') as foto, IFNULL(k.nama_kelas, '') as nama_kelas,
                   s.id_kelas
            FROM cbt_siswa s
            LEFT JOIN cbt_kelas k ON s.id_kelas = k.id_kelas";
    if (is_array($id_kelas)) {
        $placeholders = implode(',', array_fill(0, count($id_kelas), '?'));
        $stmt = $pdo_cbt->prepare($sql . " WHERE s.id_kelas IN ($placeholders) ORDER BY k.nama_kelas, s.nama_siswa");
        $stmt->execute($id_kelas);
    } elseif ($id_kelas) {
        $stmt = $pdo_cbt->prepare($sql . " WHERE s.id_kelas = ? ORDER BY s.nama_siswa");
        $stmt->execute([$id_kelas]);
    } else {
        $stmt = $pdo_cbt->query($sql . " ORDER BY k.nama_kelas, s.nama_siswa");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Ambil daftar siswa DARI SIMAKS (khusus untuk import).
 */
function cbt_get_siswa_from_simaks($id_kelas = null)
{
    $pdo_simaks = cbt_bridge_connect();
    if (!$pdo_simaks)
        return [];

    $sql = "SELECT s.id_siswa, s.nisn, s.nipd, s.nama as nama_siswa, s.tempat_lahir, s.tanggal_lahir, k.nama_kelas, k.id_kelas
            FROM siswa s 
            JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
            JOIN kelas k ON ps.id_kelas = k.id_kelas
            WHERE ps.id_ta = (SELECT id_ta FROM tahun_ajaran WHERE status = 'Aktif' LIMIT 1)
            AND ps.status_penempatan = 'Aktif'";

    if (is_array($id_kelas)) {
        $placeholders = implode(',', array_fill(0, count($id_kelas), '?'));
        $stmt = $pdo_simaks->prepare($sql . " AND ps.id_kelas IN ($placeholders) ORDER BY k.nama_kelas, s.nama");
        $stmt->execute($id_kelas);
    } elseif ($id_kelas) {
        $stmt = $pdo_simaks->prepare($sql . " AND ps.id_kelas = ? ORDER BY s.nama");
        $stmt->execute([$id_kelas]);
    } else {
        $stmt = $pdo_simaks->query($sql . " ORDER BY k.nama_kelas, s.nama");
    }

    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as &$r) {
        $r['jurusan'] = '';
        $r['no_peserta'] = '';
        $r['ruang'] = '';
        $r['sesi'] = '';
        $r['foto'] = '';
    }
    return $res;
}


/**
 * Ambil daftar mapel.
 */
function cbt_get_mapel($pdo_cbt)
{
    $pdo_simaks = cbt_bridge_connect();
    if ($pdo_simaks) {
        // Mengikuti urutan di SIMAKS
        return $pdo_simaks->query("SELECT id_mapel, nama_mapel, kode_mapel FROM mapel ORDER BY urutan, nama_mapel")->fetchAll();
    }
    return $pdo_cbt->query("SELECT id_mapel, nama_mapel, kode_mapel FROM cbt_mapel ORDER BY nama_mapel")->fetchAll();
}
