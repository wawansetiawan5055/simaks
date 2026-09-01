<?php
class PenggunaModel {
    
    // Mencari user berdasarkan username (Login)
    public static function findByUsername($pdo, $username) {
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mencari user berdasarkan QR Token / Username / NISN / NUPTK (QR Code Login Multi-Format)
    public static function findByQrToken($pdo, $raw_input) {
        $raw_input = trim((string)$raw_input);
        if ($raw_input === '') return false;

        $search_tokens = [];
        $search_tokens[] = $raw_input;

        // 1. Jika input adalah format JSON, decode dan ambil nilai token/username/nisn
        if (str_starts_with($raw_input, '{') && str_ends_with($raw_input, '}')) {
            $json = json_decode($raw_input, true);
            if (is_array($json)) {
                if (!empty($json['qr_token'])) $search_tokens[] = trim((string)$json['qr_token']);
                if (!empty($json['token'])) $search_tokens[] = trim((string)$json['token']);
                if (!empty($json['username'])) $search_tokens[] = trim((string)$json['username']);
                if (!empty($json['nisn'])) $search_tokens[] = trim((string)$json['nisn']);
                if (!empty($json['nuptk'])) $search_tokens[] = trim((string)$json['nuptk']);
                if (!empty($json['nipd'])) $search_tokens[] = trim((string)$json['nipd']);
            }
        }

        // 2. Jika input adalah URL, parse query param qr_token / username
        if (filter_var($raw_input, FILTER_VALIDATE_URL) || str_contains($raw_input, '?')) {
            $parts = parse_url($raw_input);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query_params);
                if (!empty($query_params['qr_token'])) $search_tokens[] = trim((string)$query_params['qr_token']);
                if (!empty($query_params['token'])) $search_tokens[] = trim((string)$query_params['token']);
                if (!empty($query_params['username'])) $search_tokens[] = trim((string)$query_params['username']);
                if (!empty($query_params['user'])) $search_tokens[] = trim((string)$query_params['user']);
            }
        }

        $search_tokens = array_unique(array_filter($search_tokens));

        foreach ($search_tokens as $token) {
            // A. Cari via kolom qr_token di tabel pengguna
            $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE qr_token = ? AND qr_token IS NOT NULL AND qr_token != '' LIMIT 1");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) return $user;

            // B. Cari via username di tabel pengguna
            $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ? LIMIT 1");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                // Jika user belum punya qr_token, generate otomatis
                if (empty($user['qr_token'])) {
                    $newToken = self::generateQrToken();
                    $stmt_up = $pdo->prepare("UPDATE pengguna SET qr_token = ? WHERE id_pengguna = ?");
                    $stmt_up->execute([$newToken, $user['id_pengguna']]);
                    $user['qr_token'] = $newToken;
                }
                return $user;
            }

            // C. Cari via NISN atau NIPD di tabel siswa
            $stmt = $pdo->prepare("
                SELECT p.* FROM pengguna p
                JOIN siswa s ON p.id_pengguna = s.id_pengguna
                WHERE s.nisn = ? OR s.nipd = ?
                LIMIT 1
            ");
            $stmt->execute([$token, $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) return $user;

            // D. Cari via NUPTK, NIK, atau Kode Guru di tabel guru
            $stmt = $pdo->prepare("
                SELECT p.* FROM pengguna p
                JOIN guru g ON p.id_pengguna = g.id_pengguna
                WHERE g.nuptk = ? OR g.nik = ? OR g.kode_guru = ?
                LIMIT 1
            ");
            $stmt->execute([$token, $token, $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) return $user;
        }

        return false;
    }

    // Helper untuk membuat QR token baru yang unik
    public static function generateQrToken() {
        return 'SIMAKS-QR-' . bin2hex(random_bytes(16));
    }

    // [REVISI] Mengambil ID Peran DAN Nama Peran
    public static function getRoles($pdo, $user_id) {
        $stmt = $pdo->prepare(
            "SELECT T2.id_peran, T2.nama_peran 
             FROM pengguna_peran AS T1 
             JOIN peran AS T2 ON T1.id_peran = T2.id_peran
             WHERE T1.id_pengguna = ?"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}
