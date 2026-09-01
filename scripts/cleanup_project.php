<?php
/**
 * SIMAKS Safe Project Cleanup & Organizer Script
 */

$rootDir = realpath(__DIR__ . '/..');

echo "Memulai proses pembersihan di: " . $rootDir . "\n";

// 1. Buat folder arsip & docs jika belum ada
$archiveDir = $rootDir . '/archive';
$docsDir = $rootDir . '/docs';

if (!is_dir($archiveDir)) mkdir($archiveDir, 0755, true);
if (!is_dir($docsDir)) mkdir($docsDir, 0755, true);

// 2. Daftar file sampah murni untuk dihapus
$junkFiles = [
    $rootDir . '/lms_debug.log',
    $rootDir . '/cookie.txt',
    $rootDir . '/ftp_test.txt',
    $rootDir . '/.~lock.template_import_siswa.xlsx#',
    $rootDir . '/app/views/dashboard.php.bak',
    $rootDir . '/app/views/lms_materi_upload.php.bak',
    $rootDir . '/app/views/partials/header.php.bak',
    $rootDir . '/app/views/partials/sidebar.php.bak',
    $rootDir . '/check_data.php',
    $rootDir . '/test.php',
    $rootDir . '/test_db.php',
    $rootDir . '/test_rekap_final.php',
    $rootDir . '/test_session.php',
    $rootDir . '/public/test.php',
    $rootDir . '/public/test_landing_sma.php',
    $rootDir . '/public/test_slug_helper.php',
    $rootDir . '/public/scratch_list_models.php',
    $rootDir . '/public/reset_cache.php',
    $rootDir . '/public/read_programs.php',
    $rootDir . '/public/update_programs.php'
];

$deletedCount = 0;
foreach ($junkFiles as $file) {
    if (file_exists($file)) {
        if (@unlink($file)) {
            echo "🗑️ Dihapus: " . basename($file) . "\n";
            $deletedCount++;
        }
    }
}

// 3. Daftar file untuk diarsipkan ke archive/
$archiveFiles = [
    $rootDir . '/fix_links.php',
    $rootDir . '/fix_links_v2.php',
    $rootDir . '/fix_lms_views.sh',
    $rootDir . '/migrate_docs.php',
    $rootDir . '/patch_db.php',
    $rootDir . '/temp_migration.php',
    $rootDir . '/reconstruct_ganjil.php',
    $rootDir . '/reconstruct_placements.php',
    $rootDir . '/restore_students.php',
    $rootDir . '/scratch_list_models.php'
];

$archivedCount = 0;
foreach ($archiveFiles as $file) {
    if (file_exists($file)) {
        $target = $archiveDir . '/' . basename($file);
        if (@rename($file, $target)) {
            echo "📦 Diarsipkan ke archive/: " . basename($file) . "\n";
            $archivedCount++;
        }
    }
}

// 4. Daftar dokumen lama untuk dipindahkan ke docs/
$docFiles = [
    $rootDir . '/DATABASE_AUDIT.md',
    $rootDir . '/DEVELOPER_REFERENCE.php',
    $rootDir . '/FEATURE_FLAGS.md',
    $rootDir . '/IMPLEMENTATION_ROADMAP.md',
    $rootDir . '/PERBAIKAN_DASHBOARD.md',
    $rootDir . '/SETUP_CHECKLIST.md',
    $rootDir . '/SETUP_DATABASE_APANEL.md',
    $rootDir . '/SETUP_GUIDE.md',
    $rootDir . '/STATUS.md',
    $rootDir . '/WEBSITE_SMA_PLUS_INTEGRATION_STATUS.md',
    $rootDir . '/WEBSITE_SMA_PLUS_QUICK_START.md'
];

$movedDocCount = 0;
foreach ($docFiles as $file) {
    if (file_exists($file)) {
        $target = $docsDir . '/' . basename($file);
        if (@rename($file, $target)) {
            echo "📄 Dirapikan ke docs/: " . basename($file) . "\n";
            $movedDocCount++;
        }
    }
}

echo "\n============================================\n";
echo "PEMBERSIHAN SELESAI!\n";
echo "Total Sampah Dihapus: {$deletedCount} file\n";
echo "Total Diarsipkan: {$archivedCount} file\n";
echo "Total Dokumen Dirapikan: {$movedDocCount} file\n";
echo "============================================\n";
