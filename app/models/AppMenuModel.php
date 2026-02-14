<?php
// app/models/AppMenuModel.php

class AppMenuModel {
    
    // Mengambil semua menu aktif, diurutkan berdasarkan urutan
    public static function getAllActive(PDO $pdo) {
        $sql = "SELECT * FROM app_menu WHERE status = 'Aktif' ORDER BY urutan ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil menu berdasarkan ID (Untuk keperluan edit nanti)
    public static function findById(PDO $pdo, $id_menu) {
        $stmt = $pdo->prepare("SELECT * FROM app_menu WHERE id_menu = ?");
        $stmt->execute([$id_menu]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // C/U: Menyimpan atau Mengubah menu
    public static function save(PDO $pdo, $data) {
        if (!empty($data['id_menu'])) {
            // Validate: Prevent Self-Parenting
            if ($data['id_menu'] == $data['parent_id']) {
                // Option: Force to Root (0) or Fail. Here we Fail to alert user.
                error_log("Attempt to set parent_id same as id_menu for ID " . $data['id_menu']);
                return false; 
            }

            // Update
            $sql = "UPDATE app_menu SET nama_menu=?, link=?, icon=?, parent_id=?, urutan=?, status=? WHERE id_menu=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$data['nama_menu'], $data['link'], $data['icon'], $data['parent_id'], $data['urutan'], $data['status'], $data['id_menu']]);
        } else {
            // Create
            $sql = "INSERT INTO app_menu (nama_menu, link, icon, parent_id, urutan, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$data['nama_menu'], $data['link'], $data['icon'], $data['parent_id'], $data['urutan'], $data['status']]);
        }
    }

    // D: Menghapus menu
    public static function delete(PDO $pdo, $id_menu) {
        $pdo->beginTransaction();
        try {
            // 1. Hapus Hak Akses yang terkait dengan menu ini
            $stmt1 = $pdo->prepare("DELETE FROM hak_akses WHERE id_menu = ?");
            $stmt1->execute([$id_menu]);

            // 2. Hapus Menu itu sendiri
            $stmt2 = $pdo->prepare("DELETE FROM app_menu WHERE id_menu = ?");
            $stmt2->execute([$id_menu]);
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Gagal hapus menu: " . $e->getMessage());
            return false;
        }
    }
    
    // ------------------------------------------------------------------
    // [FUNGSI REVISI] MENGAMBIL MENU BERDASARKAN HAK AKSES USER
    // ------------------------------------------------------------------
    /**
     * Mengambil struktur menu (Parent dan Submenu) yang diizinkan untuk Role ID tertentu.
     * Menggunakan dua langkah query untuk mengakomodasi Header/Divider (parent_id=0) 
     * yang tidak memiliki hak akses langsung, tetapi memiliki submenu yang diizinkan.
     * * @param PDO $pdo Koneksi PDO.
     * @param array $user_role_ids Array ID Peran pengguna (dari $_SESSION['role_ids']).
     * @return array Struktur menu Treeview.
     */


    public static function getUserMenu(PDO $pdo, array $user_role_ids) {
        if (empty($user_role_ids)) return [];

        // 1. Ambil SEMUA Menu Aktif (Raw Data)
        // Kita ambil semua dulu, nanti difilter via PHP. 
        // Ini lebih aman daripada mencoba logic OR parent_id=0 di SQL yang bikin bocor.
        $stmt_all = $pdo->prepare("SELECT * FROM app_menu WHERE status='Aktif' ORDER BY urutan ASC");
        $stmt_all->execute();
        $all_menus = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

        // Map ID => Menu untuk lookup cepat
        $menu_map = [];
        foreach ($all_menus as $m) {
            $menu_map[$m['id_menu']] = $m;
        }

        // 2. Ambil ID Menu yang DIIZINKAN (Explicit Permissions)
        $placeholders = implode(',', array_fill(0, count($user_role_ids), '?'));
        
        // ADMIN BYPASS (Role ID 1)
        if (in_array(1, $user_role_ids)) {
            // Admin sees everything
            $allowed_menu_ids = array_keys($menu_map);
        } else {
            // Normal Roles logic
            $sql_allowed = "
                SELECT DISTINCT id_menu 
                FROM hak_akses 
                WHERE can_read = 1 
                AND id_peran IN ({$placeholders})
            ";
            $stmt = $pdo->prepare($sql_allowed);
            $stmt->execute($user_role_ids);
            $allowed_menu_ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        // 3. Tentukan Menu Mana Saja yang Visible (Explicit + Ancestors)
        $visible_ids = [];
        
        // Helper untuk Recursive Parent Lookup
        $add_ancestors = function($menu_id) use (&$visible_ids, $menu_map, &$add_ancestors) {
            if (!isset($menu_map[$menu_id])) return;
            
            // Parent ID
            $parent_id = $menu_map[$menu_id]['parent_id'];
            
            // Jika punya parent dan parentnya belum ditandai visible
            if ($parent_id > 0 && !isset($visible_ids[$parent_id])) {
                $visible_ids[$parent_id] = true; // Mark visible
                $add_ancestors($parent_id); // Recurse ke atas
            }
        };

        foreach ($allowed_menu_ids as $mid) {
            $visible_ids[$mid] = true; // Menu itu sendiri visible
            $add_ancestors($mid);      // Parent-nya juga harus visible
        }

        // 4. Build Structure HANYA dari menu yang visible
        $user_menu_by_id = [];
        $user_menu_tree = [];

        // Phase 1: Siapkan Node (IdOnly filter)
        foreach ($all_menus as $m) {
            if (isset($visible_ids[$m['id_menu']])) {
                $m['children'] = [];
                $user_menu_by_id[$m['id_menu']] = $m;
            }
        }

        // Phase 2: Build Tree
        foreach ($user_menu_by_id as $id => &$item) {
            $parent_id = (int)$item['parent_id'];
            
            if ($parent_id !== 0 && isset($user_menu_by_id[$parent_id])) {
                $user_menu_by_id[$parent_id]['children'][] = &$item;
            } else {
                // Jika parent tidak visible atau 0, jadikan root
                // Note: Jika parent tidak visible (misal dihapus/nonaktif), anak jadi yatim (root)
                // Tapi logika visible_ids menjamin parent visible JIKA dia aktif.
                $user_menu_tree[] = &$item;
            }
        }
        unset($item);

        return $user_menu_tree;
    }
    
    // ------------------------------------------------------------------
    // [NEW] UPDATE ORDER & PARENT (Drag & Drop)
    // ------------------------------------------------------------------
    public static function updateOrder(PDO $pdo, $updates) {
        $pdo->beginTransaction();
        try {
            $sql = "UPDATE app_menu SET urutan = ?, parent_id = ? WHERE id_menu = ?";
            $stmt = $pdo->prepare($sql);
            
            foreach ($updates as $update) {
                // Ensure IDs are integers
                $id = (int)$update['id_menu'];
                $order = (int)$update['urutan'];
                $parent = (int)$update['parent_id'];
                
                // CRITICAL FIX: Prevent Self-Parenting Loop
                // If a menu tries to be its own parent, force parent to 0 (root)
                if ($id === $parent) {
                    $parent = 0; 
                }
                
                $stmt->execute([$order, $parent, $id]);
            }
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            // error_log("Failed to update menu order: " . $e->getMessage());
            return false;
        }
    }
    
    // ------------------------------------------------------------------
    // [NEW] DUPLICATE MENU
    // ------------------------------------------------------------------
    public static function duplicate(PDO $pdo, $id_menu) {
        // 1. Get Original Menu
        $original = self::findById($pdo, $id_menu);
        if (!$original) return false;
        
        // 2. Prepare New Data
        // Generate new ID (simple approach: max + 1 or let auto_increment handle it if not strictly mapped)
        // Since we insert without ID, database handles auto_increment.
        
        $newData = [
            'nama_menu' => $original['nama_menu'] . ' (Copy)',
            'link'      => $original['link'],
            'icon'      => $original['icon'],
            'parent_id' => $original['parent_id'], // Same parent
            'urutan'    => $original['urutan'] + 1, // Place after original
            'status'    => $original['status']
        ];
        
        // 3. Insert Copy
        try {
            // Shift other items down to make space
            $stmt_shift = $pdo->prepare("UPDATE app_menu SET urutan = urutan + 1 WHERE parent_id = ? AND urutan > ?");
            $stmt_shift->execute([$original['parent_id'], $original['urutan']]);
            
            // Insert new menu
            $stmt_insert = $pdo->prepare("INSERT INTO app_menu (nama_menu, link, icon, parent_id, urutan, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert->execute([
                $newData['nama_menu'], 
                $newData['link'], 
                $newData['icon'], 
                $newData['parent_id'], 
                $newData['urutan'], 
                $newData['status']
            ]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}