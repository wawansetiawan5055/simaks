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

    public static function getAllDocuments($pdo, $id_guru, $id_ta, $jenis = null)
    {
        $sql = "SELECT * FROM perangkat_pembelajaran WHERE id_ta = ?";
        $params = [$id_ta];

        if ($id_guru) {
            $sql .= " AND id_guru = ?";
            $params[] = $id_guru;
        }

        if ($jenis) {
            $sql .= " AND jenis = ?";
            $params[] = $jenis;
        }

        $sql .= " ORDER BY updated_at DESC";
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
        $sql = "INSERT INTO perangkat_pembelajaran (id_guru, id_ta, jenis, mapel, kelas, judul, file_path, file_name, tipe_file, ukuran_file) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
            $data['ukuran_file']
        ]);
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

    public static function getAllUploads($pdo, $id_guru, $id_ta, $jenis = null)
    {
        // Reuse getAllDocumentslogic but specifically for uploads (where file_path is not null)
        // Or just return everything for now
        return self::getAllDocuments($pdo, $id_guru, $id_ta, $jenis);
    }
}
