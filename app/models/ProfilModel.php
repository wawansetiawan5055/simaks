<?php
class ProfilModel {
    public static function getProfil($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateProfil($pdo, $id, $data, $file = null) {
        // 1. Update Password (jika diisi) & Nama
        if (!empty($data['password'])) {
            $sql = "UPDATE pengguna SET nama_pengguna = ?, email = ?, password = ? WHERE id_pengguna = ?";
            $params = [$data['nama_pengguna'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), $id];
        } else {
            $sql = "UPDATE pengguna SET nama_pengguna = ?, email = ? WHERE id_pengguna = ?";
            $params = [$data['nama_pengguna'], $data['email'], $id];
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // 2. Handle File Upload (Foto)
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/img/profil/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'user_' . $id . '_' . time() . '.' . $ext;
                $destination = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Update field foto di DB
                    $stmt = $pdo->prepare("UPDATE pengguna SET foto = ? WHERE id_pengguna = ?");
                    $stmt->execute([$filename, $id]);
                    
                    // Update Session juga biar langsung berubah di header
                    $_SESSION['user_photo'] = $filename;
                }
            }
        }
    }
}
