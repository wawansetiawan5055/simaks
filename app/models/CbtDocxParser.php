<?php
// File: app/models/CbtDocxParser.php
// Parser Dokumen Microsoft Word (.docx) & Integrasi Gemini AI untuk Ekstraksi Soal CBT

require_once __DIR__ . '/AIModel.php';

class CbtDocxParser
{
    /**
     * Ekstrak teks terstruktur dari file .docx menggunakan ZipArchive bawaan PHP
     * @param string $filepath Path absolut ke file .docx
     * @return array ['success' => bool, 'text' => string, 'message' => string]
     */
    public static function extractTextFromDocx(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return ['success' => false, 'text' => '', 'message' => 'File dokumen tidak ditemukan.'];
        }

        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'text' => '', 'message' => 'Ekstensi PHP ZipArchive tidak aktif pada server ini.'];
        }

        $zip = new ZipArchive();
        $res = $zip->open($filepath);
        if ($res !== true) {
            return ['success' => false, 'text' => '', 'message' => 'Gagal membuka file .docx (File korup atau bukan arsip Word yang valid).'];
        }

        // Ambil isi word/document.xml
        $xmlContent = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xmlContent === false || trim($xmlContent) === '') {
            return ['success' => false, 'text' => '', 'message' => 'Gagal membaca word/document.xml dari file dokumen Word.'];
        }

        // Parsing XML menggunakan DOMDocument & DOMXPath
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($xmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        // Query semua paragraf dan baris tabel
        $paragraphs = $xpath->query('//w:p | //w:tr');
        if (!$paragraphs || $paragraphs->length === 0) {
            // Fallback strip tags jika node tidak ditemukan
            $cleanText = strip_tags(str_replace(['<w:p', '</w:p>'], ["\n<w:p", "\n"], $xmlContent));
            return ['success' => true, 'text' => trim($cleanText), 'message' => 'Berhasil mengekstrak teks (fallback).'];
        }

        $extractedLines = [];

        foreach ($paragraphs as $node) {
            $lineText = '';
            
            // Ambil semua elemen teks (w:t), spasi (w:tab), dan ganti baris (w:br)
            $textNodes = $xpath->query('.//w:t | .//w:tab | .//w:br', $node);
            foreach ($textNodes as $t) {
                if ($t->nodeName === 'w:tab') {
                    $lineText .= "\t";
                } elseif ($t->nodeName === 'w:br') {
                    $lineText .= "\n";
                } else {
                    $lineText .= $t->textContent;
                }
            }

            $lineText = trim($lineText);
            if ($lineText !== '') {
                $extractedLines[] = $lineText;
            }
        }

        $fullText = implode("\n", $extractedLines);

        if (trim($fullText) === '') {
            return ['success' => false, 'text' => '', 'message' => 'Dokumen Word terbaca tetapi tidak memiliki teks yang dapat diproses.'];
        }

        return [
            'success' => true,
            'text'    => $fullText,
            'message' => 'Berhasil mengekstrak teks dokumen Word.'
        ];
    }

    /**
     * Memproses teks dokumen Word dengan Gemini AI untuk mengekstrak butir-butir soal CBT ke format JSON
     * @param PDO $pdo Koneksi database
     * @param string $filepath Path file .docx
     * @param array $contextData Informasi tambahan seperti nama mapel, tingkat, id_cp, id_tp
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public static function parseDocxWithAI($pdo, string $filepath, array $contextData = []): array
    {
        $extractResult = self::extractTextFromDocx($filepath);
        if (!$extractResult['success']) {
            return $extractResult;
        }

        $rawText = $extractResult['text'];

        $mapel = $contextData['nama_mapel'] ?? 'Mata Pelajaran';
        $tingkat = $contextData['tingkat'] ?? 'SMA';

        $systemInstruction = "Anda adalah Asesor Pendidikan dan Pakar CBT Indonesia yang bertugas mengekstrak naskah soal ujian dari dokumen teks mentah menjadi data butir soal terstruktur (JSON).
Tugas Anda:
1. Identifikasi setiap butir soal (Pilihan Ganda, Esai/Uraian, Benar/Salah, Menjodohkan).
2. DUKUNGAN MULTI-BAHASA LENGKAP: Mendukung naskah dalam Bahasa Indonesia, Bahasa Inggris (English), maupun Bahasa Arab (العربية). Jika naskah berbahasa Arab (misal: Nahwu, Shorof, Qiro'ah, Tarjamah, PAI), pertahankan teks huruf Arab asli beserta harakat/syakal secara sempurna dan tepat. Jika naskah berbahasa Inggris, pertahankan teks reading/grammar dalam bahasa Inggris.
3. Pisahkan wacana/stimulus (jika ada) dan teks pertanyaan inti.
4. Ekstrak opsi jawaban (A, B, C, D, E) untuk Pilihan Ganda secara rapi dan bersih dari label penomoran opsi (huruf A./B. di awal teks opsi dihilangkan dari field teks).
5. KUNCI JAWABAN OTOMATIS:
   - Jika dokumen Word sudah memiliki kunci jawaban (di bawah soal, ditebalkan/bold, memiliki asteris (*), atau di tabel kunci di akhir dokumen), ekstrak kunci tersebut.
   - JIKA DOKUMEN WORD BELUM MEMILIKI KUNCI JAWABAN, TUGAS ANDA WAJIB MENGANALISIS DAN MEMBUATKAN KUNCI JAWABAN YANG PALING BENAR DAN AKURAT SECARA AKADEMIS BESERTA PEMBAHASANNYA.
6. Klasifikasikan Level Kognitif secara akurat: 'L1' (Pengetahuan/Pemahaman LOTS), 'L2' (Aplikasi/Penerapan MOTS), atau 'L3' (Penalaran/Analisis HOTS).
7. Klasifikasikan Tingkat Kesulitan: 'mudah', 'sedang', atau 'sulit'.
8. Berikan lingkup materi/topik dan pembahasan singkat untuk setiap butir soal.

Kembalikan respon WAJIB berupa JSON murni dengan format schema persis seperti berikut:
{
  \"total_soal\": 5,
  \"soal\": [
    {
      \"nomor_urut\": 1,
      \"tipe_soal\": \"pg\",
      \"stimulus\": \"Teks stimulus atau wacana pendukung jika ada (kosongkan jika tidak ada)\",
      \"pertanyaan\": \"Teks pertanyaan soal secara lengkap\",
      \"opsi\": [
        {\"label\": \"A\", \"teks\": \"Isi teks opsi A\", \"is_benar\": 1},
        {\"label\": \"B\", \"teks\": \"Isi teks opsi B\", \"is_benar\": 0},
        {\"label\": \"C\", \"teks\": \"Isi teks opsi C\", \"is_benar\": 0},
        {\"label\": \"D\", \"teks\": \"Isi teks opsi D\", \"is_benar\": 0},
        {\"label\": \"E\", \"teks\": \"Isi teks opsi E\", \"is_benar\": 0}
      ],
      \"kunci_jawaban\": \"A\",
      \"level_kognitif\": \"L2\",
      \"tingkat_kesulitan\": \"sedang\",
      \"lingkup_materi\": \"Topik materi pokok\",
      \"indikator_soal\": \"Indikator capaian soal\",
      \"pembahasan\": \"Penjelasan kunci jawaban\"
    }
  ]
}";

        $prompt = "Berikut adalah naskah dokumen soal ujian mata pelajaran {$mapel} tingkat {$tingkat}.
Ekstrak seluruh butir soal yang terdapat dalam teks berikut ke dalam format JSON sesuai instruksi:

--- AWAL NASKAH DOKUMEN ---
{$rawText}
--- AKHIR NASKAH DOKUMEN ---";

        $aiResponse = AIModel::generate($pdo, $prompt, $systemInstruction, true);

        if (!$aiResponse['success']) {
            return [
                'success' => false,
                'data'    => [],
                'message' => 'Gagal memproses dokumen dengan AI: ' . ($aiResponse['message'] ?? 'Respon AI kosong.')
            ];
        }

        $jsonText = trim($aiResponse['text']);
        // Bersihkan markdown wrap jika ada
        $jsonText = preg_replace('/^```(?:json)?\s*/i', '', $jsonText);
        $jsonText = preg_replace('/\s*```$/', '', $jsonText);

        $parsedData = json_decode($jsonText, true);

        if (!is_array($parsedData) || empty($parsedData['soal'])) {
            return [
                'success' => false,
                'data'    => [],
                'message' => 'AI berhasil merespon, namun format data yang dihasilkan tidak sesuai struktur butir soal.'
            ];
        }

        // Normalisasi data butir soal
        $sanitizedSoal = [];
        $noUrut = 1;

        foreach ($parsedData['soal'] as $item) {
            $tipe = strtolower(trim($item['tipe_soal'] ?? 'pg'));
            if (!in_array($tipe, ['pg', 'essay', 'tf', 'matching'])) {
                $tipe = 'pg';
            }

            $level = strtoupper(trim($item['level_kognitif'] ?? 'L2'));
            if (!in_array($level, ['L1', 'L2', 'L3'])) {
                $level = 'L2';
            }

            $kesulitan = strtolower(trim($item['tingkat_kesulitan'] ?? 'sedang'));
            if (!in_array($kesulitan, ['mudah', 'sedang', 'sulit'])) {
                $kesulitan = 'sedang';
            }

            $kunci = trim((string)($item['kunci_jawaban'] ?? ''));
            $opsiList = [];

            if ($tipe === 'pg' && !empty($item['opsi']) && is_array($item['opsi'])) {
                $kunciUpper = strtoupper($kunci);
                foreach ($item['opsi'] as $op) {
                    $lbl = strtoupper(trim($op['label'] ?? ''));
                    $txt = trim($op['teks'] ?? ($op['isi_opsi'] ?? ''));
                    if ($lbl !== '' && $txt !== '') {
                        $isBenar = (!empty($op['is_benar']) || $lbl === $kunciUpper) ? 1 : 0;
                        $opsiList[] = [
                            'label'    => $lbl,
                            'teks'     => $txt,
                            'is_benar' => $isBenar
                        ];
                    }
                }
            } elseif ($tipe === 'tf') {
                $kunciTf = (strtoupper($kunci) === 'S' || strtoupper($kunci) === 'SALAH') ? 'S' : 'B';
                $opsiList = [
                    ['label' => 'B', 'teks' => 'BENAR', 'is_benar' => ($kunciTf === 'B' ? 1 : 0)],
                    ['label' => 'S', 'teks' => 'SALAH', 'is_benar' => ($kunciTf === 'S' ? 1 : 0)]
                ];
            }

            $sanitizedSoal[] = [
                'nomor_urut'        => $noUrut++,
                'tipe_soal'         => $tipe,
                'stimulus'          => trim($item['stimulus'] ?? ''),
                'pertanyaan'        => trim($item['pertanyaan'] ?? ''),
                'opsi'              => $opsiList,
                'kunci_jawaban'     => $kunci,
                'level_kognitif'    => $level,
                'tingkat_kesulitan' => $kesulitan,
                'lingkup_materi'    => trim($item['lingkup_materi'] ?? ''),
                'indikator_soal'    => trim($item['indikator_soal'] ?? ''),
                'pembahasan'        => trim($item['pembahasan'] ?? ''),
                'id_cp'             => $contextData['id_cp'] ?? null,
                'id_tp'             => $contextData['id_tp'] ?? null
            ];
        }

        return [
            'success'    => true,
            'total_soal' => count($sanitizedSoal),
            'soal'       => $sanitizedSoal,
            'message'    => 'Berhasil mengekstrak ' . count($sanitizedSoal) . ' butir soal dari dokumen Word.'
        ];
    }
}
