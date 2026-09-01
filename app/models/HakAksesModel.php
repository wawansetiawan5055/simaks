<?php
// app/models/HakAksesModel.php

class HakAksesModel {
    
    /**
     * MENGAMBIL IZIN (MULTI-ROLE SUPPORT)
     * Menggunakan logika "Smart Link Matching" dan "Max Permission"
     */
    public static function getPermissions($pdo, $user_role_ids, $module_link) {
        // 1. Jika user tidak punya peran, tolak semua
        if (empty($user_role_ids)) {
            return ['can_read' => 0, 'can_create' => 0, 'can_update' => 0, 'can_delete' => 0];
        }
        
        // 2. Siapkan placeholder IN (?,?,?)
        $in_placeholders = str_repeat('?,', count($user_role_ids) - 1) . '?';
        
        // 3. QUERY SUPER: 
        // - Mencari menu berdasarkan Link (Persis atau Query String)
        // - Mengambil MAX() dari semua role user (Jika Admin=1 dan Guru=0, hasilnya 1)
        $sql = "SELECT 
                    COALESCE(MAX(ha.can_create), 0) as can_create,
                    COALESCE(MAX(ha.can_read), 0) as can_read,
                    COALESCE(MAX(ha.can_update), 0) as can_update,
                    COALESCE(MAX(ha.can_delete), 0) as can_delete
                FROM hak_akses ha
                JOIN app_menu am ON ha.id_menu = am.id_menu
                WHERE (
                    am.link = ? 
                    OR am.link LIKE CONCAT('%mod=', ?, '%')
                    OR am.link LIKE CONCAT(?, '/%')
                )
                AND ha.id_peran IN ({$in_placeholders})";
                
        // Parameter: [link_raw, link_raw, link_raw, role_id_1, role_id_2...]
        $params = array_merge([$module_link, $module_link, $module_link], $user_role_ids);
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['can_read' => 0, 'can_create' => 0, 'can_update' => 0, 'can_delete' => 0];
        }

        return [
            'can_create' => (int)$result['can_create'],
            'can_read'   => (int)$result['can_read'],
            'can_update' => (int)$result['can_update'],
            'can_delete' => (int)$result['can_delete'],
        ];
    }

    // --- FUNGSI MAPPING (TETAP SAMA) ---
    public static function getMappingByPeran($pdo, $id_peran) {
        $stmt = $pdo->prepare("SELECT id_menu, can_create, can_read, can_update, can_delete FROM hak_akses WHERE id_peran = ?");
        $stmt->execute([$id_peran]);
        $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapping = [];
        foreach ($raw_data as $row) {
            $id_menu = $row['id_menu'];
            unset($row['id_menu']);
            $mapping[$id_menu] = [
                'can_create' => (int)$row['can_create'],
                'can_read' => (int)$row['can_read'],
                'can_update' => (int)$row['can_update'],
                'can_delete' => (int)$row['can_delete']
            ];
        }
        return $mapping;
    }

    // --- FUNGSI SIMPAN (TETAP SAMA) ---
    public static function saveMapping($pdo, $id_peran, $permissions) {
        $pdo->beginTransaction();
        try {
            $stmt_delete = $pdo->prepare("DELETE FROM hak_akses WHERE id_peran = ?");
            $stmt_delete->execute([$id_peran]);

            if (!empty($permissions)) {
                $sql = "INSERT INTO hak_akses (id_peran, id_menu, can_create, can_read, can_update, can_delete) VALUES ";
                $values = [];
                $params = [];

                foreach ($permissions as $id_menu => $aksi) {
                    $values[] = "(?, ?, ?, ?, ?, ?)";
                    $params[] = $id_peran;
                    $params[] = $id_menu;
                    $params[] = (int)($aksi['create'] ?? 0);
                    $params[] = (int)($aksi['read'] ?? 0);
                    $params[] = (int)($aksi['update'] ?? 0);
                    $params[] = (int)($aksi['delete'] ?? 0);
                }
                
                $sql .= implode(', ', $values);
                $stmt_insert = $pdo->prepare($sql);
                $stmt_insert->execute($params);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}