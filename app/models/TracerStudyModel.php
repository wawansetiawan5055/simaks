<?php
/**
 * TracerStudyModel
 * Model untuk mengelola data Study Tracer Alumni
 * Melacak status alumni setelah lulus (PTN/PTS, Bekerja, Wirausaha, Lain-lain)
 */
class TracerStudyModel {
    
    /**
     * Mengambil semua data tracer study
     * @param PDO $pdo
     * @param array $filters - Optional filters (tahun_lulus, status)
     * @return array
     */
    public static function getAll($pdo, $filters = []) {
        $sql = "SELECT 
                    t.*,
                    sa.nama,
                    sa.nisn,
                    sa.jk
                FROM tracer_study t
                JOIN siswa_alumni sa ON t.id_siswa = sa.id_siswa
                WHERE 1=1";
        
        $params = [];
        
        // Filter by tahun lulus
        if (!empty($filters['tahun_lulus'])) {
            $sql .= " AND t.tahun_lulus = ?";
            $params[] = $filters['tahun_lulus'];
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $sql .= " AND t.status_setelah_lulus = ?";
            $params[] = $filters['status'];
        }
        
        $sql .= " ORDER BY t.tahun_lulus DESC, sa.nama ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mengambil data tracer by ID
     * @param PDO $pdo
     * @param int $id
     * @return array|false
     */
    public static function getById($pdo, $id) {
        $sql = "SELECT 
                    t.*,
                    sa.nama,
                    sa.nisn,
                    sa.jk
                FROM tracer_study t
                JOIN siswa_alumni sa ON t.id_siswa = sa.id_siswa
                WHERE t.id_tracer = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Simpan data tracer (Insert atau Update)
     * @param PDO $pdo
     * @param array $data
     * @return bool
     */
    public static function save($pdo, $data) {
        if (!empty($data['id_tracer'])) {
            // Update
            $sql = "UPDATE tracer_study SET
                        id_siswa = ?,
                        tahun_lulus = ?,
                        status_setelah_lulus = ?,
                        nama_institusi = ?,
                        jurusan_pekerjaan = ?,
                        kota = ?,
                        keterangan = ?
                    WHERE id_tracer = ?";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_siswa'],
                $data['tahun_lulus'],
                $data['status_setelah_lulus'],
                $data['nama_institusi'] ?? null,
                $data['jurusan_pekerjaan'] ?? null,
                $data['kota'] ?? null,
                $data['keterangan'] ?? null,
                $data['id_tracer']
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO tracer_study 
                        (id_siswa, tahun_lulus, status_setelah_lulus, nama_institusi, jurusan_pekerjaan, kota, keterangan)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_siswa'],
                $data['tahun_lulus'],
                $data['status_setelah_lulus'],
                $data['nama_institusi'] ?? null,
                $data['jurusan_pekerjaan'] ?? null,
                $data['kota'] ?? null,
                $data['keterangan'] ?? null
            ]);
        }
    }
    
    /**
     * Hapus data tracer
     * @param PDO $pdo
     * @param int $id
     * @return bool
     */
    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM tracer_study WHERE id_tracer = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Mengambil statistik lulusan untuk dashboard (5 tahun terakhir)
     * @param PDO $pdo
     * @param int $limit - Jumlah tahun yang ditampilkan
     * @return array
     */
    public static function getStatisticsByYear($pdo, $limit = 5) {
        $sql = "SELECT 
                    sa.tahun_lulus,
                    COUNT(DISTINCT sa.id_siswa) as total_lulus,
                    SUM(CASE WHEN sa.jk = 'Laki-laki' THEN 1 ELSE 0 END) as laki_laki,
                    SUM(CASE WHEN sa.jk = 'Perempuan' THEN 1 ELSE 0 END) as perempuan,
                    SUM(CASE WHEN t.status_setelah_lulus = 'PTN/PTS' THEN 1 ELSE 0 END) as ptn_pts,
                    SUM(CASE WHEN t.status_setelah_lulus = 'Bekerja' THEN 1 ELSE 0 END) as bekerja,
                    SUM(CASE WHEN t.status_setelah_lulus = 'Wirausaha' THEN 1 ELSE 0 END) as wirausaha,
                    SUM(CASE WHEN t.status_setelah_lulus = 'Lain-lain' THEN 1 ELSE 0 END) as lain_lain
                FROM siswa_alumni sa
                LEFT JOIN tracer_study t ON sa.id_siswa = t.id_siswa
                WHERE sa.tahun_lulus IS NOT NULL
                GROUP BY sa.tahun_lulus
                ORDER BY sa.tahun_lulus DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mengambil daftar alumni yang belum ada data tracer
     * @param PDO $pdo
     * @param int $tahun_lulus - Optional filter by tahun
     * @return array
     */
    public static function getAlumniWithoutTracer($pdo, $tahun_lulus = null) {
        $sql = "SELECT sa.id_siswa, sa.nama, sa.nisn, sa.jk, sa.tahun_lulus
                FROM siswa_alumni sa
                LEFT JOIN tracer_study t ON sa.id_siswa = t.id_siswa
                WHERE t.id_tracer IS NULL";
        
        $params = [];
        if ($tahun_lulus) {
            $sql .= " AND sa.tahun_lulus = ?";
            $params[] = $tahun_lulus;
        }
        
        $sql .= " ORDER BY sa.tahun_lulus DESC, sa.nama ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mengambil data tracer untuk alumni tertentu
     * @param PDO $pdo
     * @param int $id_siswa
     * @return array|false
     */
    public static function getTracerByAlumni($pdo, $id_siswa) {
        $sql = "SELECT t.*, sa.nama, sa.nisn, sa.jk, sa.tahun_lulus
                FROM tracer_study t
                JOIN siswa_alumni sa ON t.id_siswa = sa.id_siswa
                WHERE t.id_siswa = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_siswa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cek apakah alumni sudah memiliki data tracer
     * @param PDO $pdo
     * @param int $id_siswa
     * @return bool
     */
    public static function hasTracer($pdo, $id_siswa) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tracer_study WHERE id_siswa = ?");
        $stmt->execute([$id_siswa]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Mengambil daftar tahun lulus yang tersedia
     * @param PDO $pdo
     * @return array
     */
    public static function getAvailableYears($pdo) {
        $sql = "SELECT DISTINCT tahun_lulus 
                FROM siswa_alumni 
                WHERE tahun_lulus IS NOT NULL 
                ORDER BY tahun_lulus DESC";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Mengambil statistik per status untuk tahun tertentu
     * @param PDO $pdo
     * @param int $tahun_lulus
     * @return array
     */
    public static function getStatusStatistics($pdo, $tahun_lulus) {
        $sql = "SELECT 
                    status_setelah_lulus,
                    COUNT(*) as jumlah
                FROM tracer_study
                WHERE tahun_lulus = ?
                GROUP BY status_setelah_lulus";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tahun_lulus]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
