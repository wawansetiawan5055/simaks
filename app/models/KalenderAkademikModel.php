<?php
/**
 * KalenderAkademikModel.php
 * Model for managing academic calendar events
 */

class KalenderAkademikModel
{
    /**
     * Get all events for a specific academic year
     */
    public static function getAll($pdo, $id_ta)
    {
        $stmt = $pdo->prepare("
            SELECT * FROM kalender_akademik 
            WHERE id_ta = ? 
            ORDER BY tanggal_mulai ASC
        ");
        $stmt->execute([$id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get events within a date range (for calendar view)
     */
    public static function getByDateRange($pdo, $start, $end, $id_ta = null)
    {
        $sql = "SELECT * FROM kalender_akademik 
                WHERE (tanggal_mulai BETWEEN ? AND ?) 
                   OR (tanggal_selesai BETWEEN ? AND ?)
                   OR (tanggal_mulai <= ? AND tanggal_selesai >= ?)";
        
        $params = [$start, $end, $start, $end, $start, $end];
        
        if ($id_ta) {
            $sql .= " AND id_ta = ?";
            $params[] = $id_ta;
        }
        
        $sql .= " ORDER BY tanggal_mulai ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single event by ID
     */
    public static function find($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM kalender_akademik WHERE id_kalender = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Save (create or update) event
     */
    public static function save($pdo, $data)
    {
        $id = $data['id_kalender'] ?? null;
        
        if ($id) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE kalender_akademik SET
                    id_ta = ?,
                    judul_kegiatan = ?,
                    deskripsi = ?,
                    tanggal_mulai = ?,
                    tanggal_selesai = ?,
                    kategori = ?,
                    warna = ?,
                    is_recurring = ?,
                    recurring_type = ?
                WHERE id_kalender = ?
            ");
            $stmt->execute([
                $data['id_ta'],
                $data['judul_kegiatan'],
                $data['deskripsi'] ?? null,
                $data['tanggal_mulai'],
                $data['tanggal_selesai'],
                $data['kategori'],
                $data['warna'] ?? '#3788d8',
                $data['is_recurring'] ?? 0,
                $data['recurring_type'] ?? null,
                $id
            ]);
            return $id;
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO kalender_akademik 
                (id_ta, judul_kegiatan, deskripsi, tanggal_mulai, tanggal_selesai, 
                 kategori, warna, is_recurring, recurring_type, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['id_ta'],
                $data['judul_kegiatan'],
                $data['deskripsi'] ?? null,
                $data['tanggal_mulai'],
                $data['tanggal_selesai'],
                $data['kategori'],
                $data['warna'] ?? '#3788d8',
                $data['is_recurring'] ?? 0,
                $data['recurring_type'] ?? null,
                $_SESSION['id_user'] ?? null
            ]);
            return $pdo->lastInsertId();
        }
    }

    /**
     * Delete event
     */
    public static function delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM kalender_akademik WHERE id_kalender = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get events by category
     */
    public static function getByCategory($pdo, $kategori, $id_ta)
    {
        $stmt = $pdo->prepare("
            SELECT * FROM kalender_akademik 
            WHERE kategori = ? AND id_ta = ?
            ORDER BY tanggal_mulai ASC
        ");
        $stmt->execute([$kategori, $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all categories from database
     */
    public static function getCategories($pdo)
    {
        return $pdo->query("SELECT * FROM kalender_kategori ORDER BY id_kategori ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get category color mapping from database
     */
    public static function getCategoryColors($pdo)
    {
        $categories = self::getCategories($pdo);
        $colors = [];
        foreach ($categories as $cat) {
            $colors[$cat['nama_kategori']] = $cat['warna'];
        }
        return $colors;
    }
}
