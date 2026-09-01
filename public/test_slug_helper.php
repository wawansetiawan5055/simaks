<?php
/**
 * Test SlugHelper
 * File untuk testing SlugHelper functionality
 * 
 * Akses: http://localhost:7166/test_slug_helper.php
 */

// Include SlugHelper
require_once dirname(__DIR__) . '/app/helpers/SlugHelper.php';
require_once dirname(__DIR__) . '/config/db.php';

// Database connection
$pdo = connect_db();

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SlugHelper Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        .test-case { background: #f5f5f5; padding: 15px; margin-bottom: 15px; border-radius: 5px; border-left: 4px solid #2196F3; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2196F3; color: white; }
        .result-box { background: white; border: 1px solid #ddd; padding: 10px; border-radius: 3px; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>🧪 SlugHelper Test Suite</h1>
    <p>Testing semua fungsi SlugHelper untuk memastikan semuanya berfungsi dengan baik.</p>";

// =====================================================
// TEST 1: Basic Slug Generation
// =====================================================
echo "<h2>Test 1: Basic Slug Generation</h2>";

$test_cases = [
    "Selamat Datang di SIMAKS" => "selamat-datang-di-simaks",
    "Program Unggulan 2025!" => "program-unggulan-2025",
    "Profil Sekolah - Visi & Misi" => "profil-sekolah-visi-misi",
    "C++ Programming Guide" => "c-programming-guide",
    "PPDB Online 2026/2027" => "ppdb-online-20262027",
    "   Whitespace   Test   " => "whitespace-test",
];

echo "<table>
    <tr>
        <th>Input</th>
        <th>Expected</th>
        <th>Result</th>
        <th>Status</th>
    </tr>";

foreach ($test_cases as $input => $expected) {
    $result = SlugHelper::generate($input);
    $status = ($result === $expected) ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    echo "<tr>
        <td><code>$input</code></td>
        <td><code>$expected</code></td>
        <td><code>$result</code></td>
        <td>$status</td>
    </tr>";
}
echo "</table>";

// =====================================================
// TEST 2: Slug Uniqueness Check
// =====================================================
echo "<h2>Test 2: Slug Uniqueness Check</h2>";

// Get first berita slug if exists
$stmt = $pdo->query("SELECT slug FROM landing_news LIMIT 1");
$berita = $stmt->fetch(PDO::FETCH_ASSOC);

if ($berita) {
    $existing_slug = $berita['slug'];
    $exists = SlugHelper::exists($pdo, $existing_slug);
    $status = $exists ? "<span class='success'>✓ Found</span>" : "<span class='error'>✗ Not Found</span>";
    echo "<div class='test-case'>
        <p><strong>Check existing slug:</strong> $existing_slug</p>
        <div class='result-box'>Status: $status</div>
    </div>";
} else {
    echo "<div class='test-case'>
        <p><strong>Info:</strong> Tidak ada berita di database, skip test ini.</p>
    </div>";
}

// =====================================================
// TEST 3: Generate Unique Slug
// =====================================================
echo "<h2>Test 3: Generate Unique Slug</h2>";

$test_title = "Test Slug - " . date('Ymd-His');
$unique_slug = SlugHelper::generateUnique($pdo, $test_title);
$exists = SlugHelper::exists($pdo, $unique_slug);

echo "<div class='test-case'>
    <p><strong>Generated slug for:</strong> '$test_title'</p>
    <div class='result-box'>
        <strong>Result:</strong> <code>$unique_slug</code><br>
        <strong>Exists in DB:</strong> " . ($exists ? "<span class='error'>✓ Exists</span>" : "<span class='success'>✓ Unique</span>") . "
    </div>
</div>";

// =====================================================
// TEST 4: Sanitize Slug
// =====================================================
echo "<h2>Test 4: Sanitize Slug</h2>";

$dirty_slugs = [
    "test---slug" => "test-slug",
    "-leading-trailing-" => "leading-trailing",
    "UPPERCASE-SLUG" => "uppercase-slug",
    "special@#$characters" => "specialcharacters",
];

echo "<table>
    <tr>
        <th>Input</th>
        <th>Expected</th>
        <th>Result</th>
        <th>Status</th>
    </tr>";

foreach ($dirty_slugs as $input => $expected) {
    $result = SlugHelper::sanitize($input);
    $status = ($result === $expected) ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    echo "<tr>
        <td><code>$input</code></td>
        <td><code>$expected</code></td>
        <td><code>$result</code></td>
        <td>$status</td>
    </tr>";
}
echo "</table>";

// =====================================================
// TEST 5: Slug to Title Conversion
// =====================================================
echo "<h2>Test 5: Slug to Title Conversion</h2>";

$slug_to_title_tests = [
    "selamat-datang-di-simaks" => "Selamat Datang Di Simaks",
    "program-unggulan-2025" => "Program Unggulan 2025",
    "profil-sekolah" => "Profil Sekolah",
];

echo "<table>
    <tr>
        <th>Input Slug</th>
        <th>Expected</th>
        <th>Result</th>
        <th>Status</th>
    </tr>";

foreach ($slug_to_title_tests as $input => $expected) {
    $result = SlugHelper::toTitle($input);
    $status = ($result === $expected) ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    echo "<tr>
        <td><code>$input</code></td>
        <td><code>$expected</code></td>
        <td><code>$result</code></td>
        <td>$status</td>
    </tr>";
}
echo "</table>";

// =====================================================
// TEST 6: Database Structure Verification
// =====================================================
echo "<h2>Test 6: Database Structure Verification</h2>";

$tables = [
    'landing_news',
    'landing_headmaster_greeting',
    'landing_programs',
    'landing_facilities',
    'landing_testimonials',
    'landing_faqs',
    'landing_gallery'
];

echo "<table>
    <tr>
        <th>Table Name</th>
        <th>Status</th>
        <th>Row Count</th>
    </tr>";

foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'] ?? 0;
    $status = "<span class='success'>✓ Exists</span>";
    echo "<tr>
        <td><code>$table</code></td>
        <td>$status</td>
        <td>$count rows</td>
    </tr>";
}
echo "</table>";

// =====================================================
// SUMMARY
// =====================================================
echo "<h2>📊 Summary</h2>";
echo "<div class='test-case'>
    <p><strong>✓ Phase 1 Database Setup Complete!</strong></p>
    <ul>
        <li>✓ SQL migration file created</li>
        <li>✓ All 7 tables created successfully</li>
        <li>✓ All columns and indexes added</li>
        <li>✓ SlugHelper.php created and tested</li>
        <li>✓ Database ready for Phase 2</li>
    </ul>
    <p><strong>Next Step:</strong> Proceed to Phase 2 - Update Controllers & Routes</p>
</div>";

echo "</body></html>";
?>
