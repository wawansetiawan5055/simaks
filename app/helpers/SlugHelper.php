<?php
/**
 * SlugHelper.php
 * Helper untuk generate dan manage URL-friendly slugs
 * 
 * Version: 1.0.0
 * Date: 10 Maret 2026
 */

class SlugHelper
{
    /**
     * Generate URL-friendly slug dari string
     * 
     * Contoh:
     * "Selamat Datang di SIMAKS" → "selamat-datang-di-simaks"
     * "Program Unggulan 2025!" → "program-unggulan-2025"
     * 
     * @param string $text String yang akan diubah menjadi slug
     * @return string URL-friendly slug
     */
    public static function generate($text)
    {
        if (empty($text)) {
            return '';
        }

        // Trim whitespace
        $text = trim($text);

        // Convert to lowercase
        $text = strtolower($text);

        // Replace special characters dengan space
        // Contoh: "C++" menjadi "c plus"
        $replacements = [
            'á' => 'a', 'à' => 'a', 'ă' => 'a', 'â' => 'a', 'å' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
            'ñ' => 'n',
        ];
        $text = strtr($text, $replacements);

        // Replace spaces & underscores dengan hyphen
        $text = preg_replace('/[\s_]+/', '-', $text);

        // Remove special characters (keep hanya a-z, 0-9, dan hyphen)
        $text = preg_replace('/[^a-z0-9\-]/', '', $text);

        // Replace multiple hyphens dengan single hyphen
        $text = preg_replace('/-+/', '-', $text);

        // Trim hyphens dari awal & akhir
        $text = trim($text, '-');

        return $text;
    }

    /**
     * Check apakah slug sudah ada di database
     * 
     * @param PDO $pdo Database connection
     * @param string $slug Slug yang dicek
     * @param int|null $exclude_id ID untuk exclude (saat edit)
     * @return bool TRUE jika slug sudah ada, FALSE jika belum
     */
    public static function exists($pdo, $slug, $exclude_id = null)
    {
        if (empty($slug)) {
            return false;
        }

        try {
            $query = "SELECT COUNT(*) as count FROM landing_news WHERE slug = ?";
            $params = [$slug];

            if ($exclude_id) {
                $query .= " AND id != ?";
                $params[] = $exclude_id;
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("SlugHelper::exists() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique slug
     * Jika slug sudah ada, tambahkan suffix -1, -2, dst
     * 
     * @param PDO $pdo Database connection
     * @param string $text String untuk generate slug
     * @param int|null $exclude_id ID untuk exclude (saat edit)
     * @return string Unique slug
     */
    public static function generateUnique($pdo, $text, $exclude_id = null)
    {
        $slug = self::generate($text);

        if (!self::exists($pdo, $slug, $exclude_id)) {
            return $slug;
        }

        // Jika sudah ada, tambahkan suffix
        $counter = 1;
        $original_slug = $slug;

        while (self::exists($pdo, $slug, $exclude_id)) {
            $slug = $original_slug . '-' . $counter;
            $counter++;

            // Safety check (max 100 attempts)
            if ($counter > 100) {
                // Fallback: append timestamp
                $slug = $original_slug . '-' . time();
                break;
            }
        }

        return $slug;
    }

    /**
     * Sanitize slug (validate dan clean)
     * 
     * @param string $slug Slug yang akan disanitasi
     * @return string Sanitized slug
     */
    public static function sanitize($slug)
    {
        if (empty($slug)) {
            return '';
        }

        // Ensure lowercase
        $slug = strtolower($slug);

        // Remove invalid characters
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim hyphens
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Convert slug back to readable text
     * Contoh: "selamat-datang-di-simaks" → "Selamat Datang Di Simaks"
     * 
     * @param string $slug Slug yang akan dikonversi
     * @return string Readable text
     */
    public static function toTitle($slug)
    {
        // Replace hyphens dengan spaces
        $text = str_replace('-', ' ', $slug);

        // Capitalize first letter dari setiap kata
        $text = ucwords($text);

        return $text;
    }
}
?>
