<?php
/**
 * PerangkatModel.php
 * Model for managing Perangkat Pembelajaran (ATP, Modul Ajar, Prosem, Prota)
 */

class PerangkatModel
{
    // ==========================================
    // TEMPLATE MANAGEMENT (Admin)
    // ==========================================

    public static function getAllTemplates($pdo, $jenis = null)
    {
        $sql = "SELECT * FROM master_template_dokumen WHERE is_active = 1";
        if ($jenis) {
            $sql .= " AND jenis = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$jenis]);
        } else {
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findTemplate($pdo, $id_template)
    {
        $stmt = $pdo->prepare("SELECT * FROM master_template_dokumen WHERE id_template = ?");
        $stmt->execute([$id_template]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveTemplate($pdo, $data)
    {
        if (!empty($data['id_template'])) {
            // Update
            $sql = "UPDATE master_template_dokumen SET jenis=?, nama_template=?, konten_html=?, is_active=? WHERE id_template=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['jenis'],
                $data['nama_template'],
                $data['konten_html'],
                $data['is_active'] ?? 1,
                $data['id_template']
            ]);
        } else {
            // Create
            $sql = "INSERT INTO master_template_dokumen (jenis, nama_template, konten_html, is_active) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['jenis'],
                $data['nama_template'],
                $data['konten_html'],
                $data['is_active'] ?? 1
            ]);
        }
    }

    public static function deleteTemplate($pdo, $id_template)
    {
        $stmt = $pdo->prepare("DELETE FROM master_template_dokumen WHERE id_template = ?");
        return $stmt->execute([$id_template]);
    }

    // ==========================================
    // PERANGKAT (Teacher Documents)
    // ==========================================

    public static function getAllDocuments($pdo, $id_guru, $id_ta, $jenis = null, $mapel = null)
    {
        $sql = "SELECT p.*, ta.nama_ta 
                FROM perangkat_pembelajaran p 
                LEFT JOIN tahun_ajaran ta ON p.id_ta = ta.id_ta
                WHERE 1=1";
        $params = [];

        if ($id_ta) {
            $sql .= " AND p.id_ta = ?";
            $params[] = $id_ta;
        }

        if ($id_guru) {
            $sql .= " AND p.id_guru = ?";
            $params[] = $id_guru;
        }

        if ($jenis) {
            $sql .= " AND p.jenis = ?";
            $params[] = $jenis;
        }

        if ($mapel) {
            $sql .= " AND p.mapel = ?";
            $params[] = $mapel;
        }

        $sql .= " ORDER BY p.updated_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findDocument($pdo, $id_perangkat)
    {
        $stmt = $pdo->prepare("SELECT * FROM perangkat_pembelajaran WHERE id_perangkat = ?");
        $stmt->execute([$id_perangkat]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function saveDocument($pdo, $data)
    {
        if (!empty($data['id_perangkat'])) {
            // Update
            $sql = "UPDATE perangkat_pembelajaran SET judul=?, mapel=?, kelas=?, konten_html=? WHERE id_perangkat=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['judul'],
                $data['mapel'],
                $data['kelas'],
                $data['konten_html'],
                $data['id_perangkat']
            ]);
        } else {
            // Create
            $sql = "INSERT INTO perangkat_pembelajaran (id_guru, id_ta, jenis, mapel, kelas, judul, konten_html) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $data['id_guru'],
                $data['id_ta'],
                $data['jenis'],
                $data['mapel'],
                $data['kelas'],
                $data['judul'],
                $data['konten_html']
            ]);
        }
    }

    public static function deleteDocument($pdo, $id_perangkat)
    {
        $stmt = $pdo->prepare("DELETE FROM perangkat_pembelajaran WHERE id_perangkat = ?");
        return $stmt->execute([$id_perangkat]);
    }
    public static function saveUpload($pdo, $data)
    {
        $sql = "INSERT INTO perangkat_pembelajaran (id_guru, id_ta, jenis, mapel, kelas, judul, file_path, file_name, tipe_file, ukuran_file, is_reused, source_perangkat_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['id_guru'],
            $data['id_ta'],
            $data['jenis'],
            $data['mapel'],
            $data['kelas'],
            $data['judul'],
            $data['file_path'],
            $data['file_name'],
            $data['tipe_file'],
            $data['ukuran_file'],
            $data['is_reused'] ?? 0,
            $data['source_perangkat_id'] ?? null
        ]);
    }

    public static function duplicateDocument($pdo, $id_perangkat, $target_id_ta)
    {
        $doc = self::findDocument($pdo, $id_perangkat);
        if (!$doc) return false;

        $doc['id_ta'] = $target_id_ta;
        $doc['is_reused'] = 1;
        $doc['source_perangkat_id'] = $id_perangkat;
        
        // Remove primary key to insert as new record
        unset($doc['id_perangkat']);
        unset($doc['created_at']);
        unset($doc['updated_at']);

        $columns = implode(", ", array_keys($doc));
        $placeholders = implode(", ", array_fill(0, count($doc), "?"));
        
        $sql = "INSERT INTO perangkat_pembelajaran ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(array_values($doc));
    }

    public static function getRelatedTA($pdo, $id_ta)
    {
        // Find TA in the same year (e.g. 2025/2026 Ganjil and 2025/2026 Genap)
        $current = $pdo->prepare("SELECT nama_ta FROM tahun_ajaran WHERE id_ta = ?");
        $current->execute([$id_ta]);
        $name = $current->fetchColumn();
        
        if (!$name) return [];

        // Extract year part (e.g. "2025/2026")
        $year_part = substr($name, 0, 9);
        
        $stmt = $pdo->prepare("SELECT id_ta, nama_ta FROM tahun_ajaran WHERE nama_ta LIKE ? AND id_ta != ?");
        $stmt->execute([$year_part . "%", $id_ta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateUpload($pdo, $data)
    {
        $sql = "UPDATE perangkat_pembelajaran SET judul=?, mapel=?, kelas=?, jenis=?";
        $params = [
            $data['judul'],
            $data['mapel'],
            $data['kelas'],
            $data['jenis']
        ];

        // Conditional update for file
        if (!empty($data['file_path'])) {
            $sql .= ", file_path=?, file_name=?, tipe_file=?, ukuran_file=?";
            array_push($params, $data['file_path'], $data['file_name'], $data['tipe_file'], $data['ukuran_file']);
        }

        $sql .= " WHERE id_perangkat=?";
        array_push($params, $data['id_perangkat']);

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function getAllUploads($pdo, $id_guru, $id_ta, $jenis = null, $mapel = null)
    {
        // Reuse getAllDocumentslogic but specifically for uploads (where file_path is not null)
        // Or just return everything for now
        return self::getAllDocuments($pdo, $id_guru, $id_ta, $jenis, $mapel);
    }
}
