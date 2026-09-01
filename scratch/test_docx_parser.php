<?php
// Scratch test script for CbtDocxParser

require_once __DIR__ . '/../app/models/CbtDocxParser.php';

echo "=== 1. TEST CREATING SAMPLE DOCX FILE ===\n";

$sampleDocx = __DIR__ . '/sample_soal_ujian.docx';
$zip = new ZipArchive();
if ($zip->open($sampleDocx, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    // Add minimal docx structure
    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="xml" ContentType="application/xml"/>
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>');

    $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <w:p><w:r><w:t>NASKAH SOAL ASESMEN SUMATIF EKONOMI KELAS X</w:t></w:r></w:p>
        <w:p><w:r><w:t>1. Manakah dari berikut ini yang merupakan faktor utama penyebab kelangkaan sumber daya ekonomi?</w:t></w:r></w:p>
        <w:p><w:r><w:t>A. Kebutuhan manusia yang terbatas sedangkan alat pemuas tidak terbatas</w:t></w:r></w:p>
        <w:p><w:r><w:t>B. Kebutuhan manusia yang tidak terbatas sedangkan sumber daya pemuas terbatas</w:t></w:r></w:p>
        <w:p><w:r><w:t>C. Kemajuan teknologi yang terlalu cepat</w:t></w:r></w:p>
        <w:p><w:r><w:t>D. Bertambahnya jumlah produsen barang mewah</w:t></w:r></w:p>
        <w:p><w:r><w:t>E. Penurunan daya beli masyarakat secara drastis</w:t></w:r></w:p>
        <w:p><w:r><w:t>Kunci Jawaban: B</w:t></w:r></w:p>
        <w:p><w:r><w:t>Pembahasan: Kelangkaan terjadi karena kesenjangan antara kebutuhan yang tak terbatas dan alat pemuas yang terbatas.</w:t></w:r></w:p>
        <w:p><w:r><w:t>2. Jelaskan perbedaan antara kebutuhan primer, sekunder, dan tersier berikan masing-masing 2 contoh!</w:t></w:r></w:p>
        <w:p><w:r><w:t>Kunci: Primer adalah kebutuhan pokok (pangan, sandang), sekunder pelengkap (alat elektronik), tersier barang mewah (mobil mewah, perhiasan mahal).</w:t></w:r></w:p>
    </w:body>
</w:document>';

    $zip->addFromString('word/document.xml', $documentXml);
    $zip->close();
    echo "Sample .docx file created successfully: $sampleDocx\n";
} else {
    echo "Failed to create sample .docx file.\n";
    exit(1);
}

echo "\n=== 2. TEST CbtDocxParser::extractTextFromDocx ===\n";
$extract = CbtDocxParser::extractTextFromDocx($sampleDocx);
print_r($extract);

if ($extract['success'] && strpos($extract['text'], 'kelangkaan sumber daya ekonomi') !== false) {
    echo "\n>>> SUCCESS: Word XML Text extraction is WORKING perfectly! <<<\n";
} else {
    echo "\n>>> FAILED: Extraction text mismatch. <<<\n";
}
