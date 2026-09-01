<?php
/**
 * DocExtractorModel.php
 * Mengekstrak struktur/skeleton dari dokumen referensi yang di-upload user.
 * Mendukung: .docx (via unzip CLI), .txt (langsung)
 */

class DocExtractorModel
{
    /**
     * Entry point: ekstrak teks dari file yang sudah di-upload.
     * @param string $filepath Path absolut ke file
     * @param string $ext      Ekstensi file (docx|txt)
     * @return array ['success' => bool, 'skeleton' => string, 'message' => string]
     */
    public static function extract(string $filepath, string $ext): array
    {
        switch ($ext) {
            case 'docx':
                return self::extractFromDocx($filepath);
            case 'txt':
                return self::extractFromTxt($filepath);
            case 'pdf':
                return self::extractFromPdf($filepath);
            default:
                return ['success' => false, 'skeleton' => '', 'message' => 'Format file tidak didukung.'];
        }
    }

    /**
     * Ekstrak teks dari .docx menggunakan unzip CLI (karena ZipArchive tidak tersedia).
     * File .docx adalah ZIP yang berisi word/document.xml
     */
    private static function extractFromDocx(string $filepath): array
    {
        // Cek apakah shell_exec tersedia
        $disabled_functions = explode(',', ini_get('disable_functions'));
        $shell_exec_disabled = in_array('shell_exec', array_map('trim', $disabled_functions));

        if ($shell_exec_disabled) {
            return [
                'success' => false,
                'skeleton' => '',
                'message' => 'Server ini tidak mendukung pembentukan file Word (.docx) karena fungsi shell_exec dinonaktifkan. Silakan gunakan file .txt atau tempel teks manual.'
            ];
        }

        // Escape path untuk shell
        $escaped = escapeshellarg($filepath);

        // Ekstrak word/document.xml ke stdout (tanpa menulis file)
        $xml_content = @shell_exec("unzip -p {$escaped} word/document.xml 2>/dev/null");

        if (empty($xml_content)) {
            return [
                'success' => false,
                'skeleton' => '',
                'message' => 'Gagal mengekstrak isi file .docx. Hal ini mungkin karena library "unzip" tidak terinstal atau file korup. Gunakan manual paste jika perlu.'
            ];
        }

        // Parse XML dengan DOMDocument
        $raw_text = self::parseWordXml($xml_content);

        if (empty(trim($raw_text))) {
            return ['success' => false, 'skeleton' => '', 'message' => 'Dokumen terlihat kosong atau tidak memiliki teks.'];
        }

        $skeleton = self::buildSkeletonText($raw_text);

        return ['success' => true, 'skeleton' => $skeleton, 'message' => 'Berhasil membaca format dokumen.'];
    }

    /**
     * Parse XML dari word/document.xml dan ekstrak teks dengan mempertahankan
     * struktur heading dan paragraf.
     */
    private static function parseWordXml(string $xml_content): string
    {
        // Sembunyikan error parsing (beberapa docx punya namespace non-standard)
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($xml_content, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $result = '';
        $xpath = new DOMXPath($dom);

        // Daftarkan namespace Word XML yang umum
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        // Ambil semua paragraf
        $paragraphs = $xpath->query('//w:p');
        if (!$paragraphs || $paragraphs->length === 0) {
            // Fallback: strip XML tags saja
            return preg_replace('/<[^>]+>/', ' ', $xml_content);
        }

        foreach ($paragraphs as $para) {
            // Cek apakah ini heading (memiliki style pHeading atau Heading)
            $styleNodes = $xpath->query('.//w:pStyle', $para);
            $isHeading = false;
            $headingLevel = 0;

            if ($styleNodes && $styleNodes->length > 0) {
                $styleVal = $styleNodes->item(0)->getAttribute('w:val');
                if (stripos($styleVal, 'Heading') !== false || stripos($styleVal, 'heading') !== false) {
                    $isHeading = true;
                    // Coba ambil level (Heading1 = 1, dst)
                    preg_match('/\d+/', $styleVal, $m);
                    $headingLevel = intval($m[0] ?? 1);
                }
            }

            // Ambil semua teks dalam paragraf ini
            $textNodes = $xpath->query('.//w:t', $para);
            $paraText = '';
            foreach ($textNodes as $t) {
                $paraText .= $t->textContent;
            }
            $paraText = trim($paraText);

            if (empty($paraText)) {
                continue;
            }

            // Format berdasarkan tipe
            if ($isHeading) {
                $prefix = str_repeat('#', max(1, $headingLevel));
                $result .= "\n{$prefix} {$paraText}\n";
            } else {
                $result .= $paraText . "\n";
            }
        }

        return $result;
    }

    /**
     * Ekstrak teks dari file .txt biasa
     */
    private static function extractFromTxt(string $filepath): array
    {
        $content = file_get_contents($filepath);
        if ($content === false) {
            return ['success' => false, 'skeleton' => '', 'message' => 'Gagal membaca file teks.'];
        }

        // Deteksi encoding dan convert ke UTF-8 jika perlu
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        $skeleton = self::buildSkeletonText($content);
        return ['success' => true, 'skeleton' => $skeleton, 'message' => 'Berhasil membaca format dokumen.'];
    }

    /**
     * Ekstrak teks dari file PDF menggunakan pdftotext.
     */
    private static function extractFromPdf(string $filepath): array
    {
        $disabled_functions = explode(',', ini_get('disable_functions'));
        if (in_array('shell_exec', array_map('trim', $disabled_functions))) {
            return [
                'success' => false,
                'skeleton' => '',
                'message' => 'Server ini tidak mendukung pemrosesan PDF. Gunakan teks manual atau .txt.'
            ];
        }

        $escaped = escapeshellarg($filepath);
        $pdftext = @shell_exec("pdftotext -layout {$escaped} - 2>/dev/null");

        if ($pdftext === null || trim($pdftext) === '') {
            return [
                'success' => false,
                'skeleton' => '',
                'message' => 'Gagal mengekstrak isi file PDF. Pastikan pdftotext terinstal dan file PDF tidak terlindungi.'
            ];
        }

        $raw_text = trim($pdftext);
        if (empty($raw_text)) {
            return ['success' => false, 'skeleton' => '', 'message' => 'Dokumen PDF terlihat kosong atau tidak dapat diekstrak.'];
        }

        $skeleton = self::buildSkeletonText($raw_text);
        return ['success' => true, 'skeleton' => $skeleton, 'message' => 'Berhasil membaca format dokumen PDF.'];
    }

    /**
     * Ringkas teks mentah menjadi skeleton yang efisien untuk dikirim ke AI.
     * - Pertahankan semua baris heading/judul (baris pendek yang diawali #, nomor, atau HURUF BESAR)
     * - Potong konten panjang per-section menjadi max 80 karakter
     * - Total output max ~2500 karakter (hemat token API)
     *
     * @param string $raw_text Teks mentah dari dokumen
     * @return string Skeleton teks ringkas
     */
    public static function buildSkeletonText(string $raw_text): string
    {
        $lines = explode("\n", $raw_text);
        $skeleton_lines = [];
        $content_budget = 2000; // karakter total untuk konten non-heading
        $used = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Identifikasi apakah ini baris "heading/judul"
            $is_heading = (
                str_starts_with($line, '#') ||                        // Markdown heading
                preg_match('/^[A-Z\s]{5,}$/', $line) ||              // HURUF BESAR semua
                preg_match('/^(\d+[\.\)]|[A-Z]\.|BAB|BAGIAN)\s/i', $line) || // Penomoran/BAB
                preg_match('/^(Nama|Mata Pelajaran|Kelas|Fase|Tujuan|Identitas|Langkah|Kegiatan|Penutup|Asesmen|Refleksi|Alur|Capaian)/i', $line)
            );

            if ($is_heading) {
                // Heading selalu masuk penuh
                $skeleton_lines[] = $line;
            } else {
                // Konten: masuk jika budget masih ada, potong jika terlalu panjang
                if ($used < $content_budget) {
                    $chunk = mb_substr($line, 0, 120); // max 120 char per baris
                    if (mb_strlen($line) > 120) {
                        $chunk .= '...';
                    }
                    $skeleton_lines[] = $chunk;
                    $used += mb_strlen($chunk);
                }
            }
        }

        $result = implode("\n", $skeleton_lines);

        // Hard limit total
        if (mb_strlen($result) > 3000) {
            $result = mb_substr($result, 0, 3000) . "\n[...dokumen dipotong untuk efisiensi...]";
        }

        return $result;
    }

    /**
     * Hapus file temporary dari uploads/ai_temp/
     */
    public static function cleanupTempFile(string $filepath): void
    {
        if (file_exists($filepath) && strpos($filepath, 'uploads/ai_temp/') !== false) {
            @unlink($filepath);
        }
    }
}
?>
