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

        // 2. Handle File Upload (Foto) atau Live Camera Base64
        $filename = null;
        $uploadDir = __DIR__ . '/../../public/assets/img/profil/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // A. Cek apakah ada foto dari Live Camera
        if (!empty($data['foto_cam_data']) && preg_match('/^data:image\/(\w+);base64,/', $data['foto_cam_data'], $cam_match)) {
            $raw_base64 = substr($data['foto_cam_data'], strpos($data['foto_cam_data'], ',') + 1);
            $decoded = base64_decode($raw_base64);
            $cam_type = strtolower($cam_match[1]);
            $ext = ($cam_type === 'png') ? 'png' : 'jpg';
            if ($decoded) {
                $filename = 'user_' . $id . '_' . time() . '.' . $ext;
                file_put_contents($uploadDir . $filename, $decoded);
            }
        } 
        // B. Cek file upload biasa
        elseif ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowed)) {
                $filename = 'user_' . $id . '_' . time() . '.' . $ext;
                $destination = $uploadDir . $filename;
                move_uploaded_file($file['tmp_name'], $destination);
            }
        }

        // C. Simpan ke database jika foto baru berhasil disimpan
        if ($filename) {
            $stmt = $pdo->prepare("UPDATE pengguna SET foto = ? WHERE id_pengguna = ?");
            $stmt->execute([$filename, $id]);
            
            // Update Session foto pengguna
            $_SESSION['user_photo'] = $filename;

            // Sinkronkan ke tabel guru / siswa jika terkait
            try {
                $pdo->prepare("UPDATE guru SET foto = ? WHERE id_pengguna = ?")->execute([$filename, $id]);
                $pdo->prepare("UPDATE siswa SET foto = ? WHERE id_pengguna = ?")->execute([$filename, $id]);
            } catch (Exception $e) {
                // Ignore jika kolom/tabel belum ada
            }
        }
    }
}
