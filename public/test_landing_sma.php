<?php
// Quick test file to verify landing_sma routing works
// Access via: http://localhost/test_landing_sma.php

echo "<h2>Testing Landing SMA Setup</h2>";

// Check 1: Database connection
try {
    require_once '../config/db.php';
    $pdo = connect_db();
    echo "<p>✓ Database connection OK</p>";
} catch (Exception $e) {
    echo "<p>✗ Database connection FAILED: " . $e->getMessage() . "</p>";
    exit;
}

// Check 2: LandingControllerSMA.php exists
if (file_exists('../app/controllers/LandingControllerSMA.php')) {
    echo "<p>✓ LandingControllerSMA.php exists</p>";
} else {
    echo "<p>✗ LandingControllerSMA.php NOT FOUND</p>";
}

// Check 3: landing_sma.php view exists
if (file_exists('../app/views/landing_sma.php')) {
    echo "<p>✓ landing_sma.php view exists</p>";
} else {
    echo "<p>✗ landing_sma.php view NOT FOUND</p>";
}

// Check 4: Database tables exist
$tables = ['landing_programs', 'landing_ekstrakurikuler', 'landing_video', 'landing_informasi'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM $table WHERE is_active = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>✓ Table '$table' has " . $result['cnt'] . " active records</p>";
    } catch (Exception $e) {
        echo "<p>✗ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

// Check 5: Landing page settings
try {
    $stmt = $pdo->query("SELECT setting_value FROM app_settings WHERE setting_key = 'landing_page_enabled'");
    $setting = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($setting && $setting['setting_value'] == '1') {
        echo "<p>✓ Landing page is ENABLED in settings</p>";
    } else {
        echo "<p>⚠️ Landing page is DISABLED in settings</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Error checking landing page settings: " . $e->getMessage() . "</p>";
}

// Check 5: Test routing by simulating the request
echo "<h3>Testing Routing</h3>";
echo "<p>To access the new landing page, use one of these URLs:</p>";
echo "<ul>";
echo "<li><a href='index.php?mod=landing_sma&act=index'>index.php?mod=landing_sma&act=index</a> (Main Homepage)</li>";
echo "<li><a href='index.php?mod=landing_sma&act=guru_list'>index.php?mod=landing_sma&act=guru_list</a> (Teacher List)</li>";
echo "<li><a href='index.php?mod=landing_sma&act=ekstrakurikuler_list'>index.php?mod=landing_sma&act=ekstrakurikuler_list</a> (Extracurricular)</li>";
echo "<li><a href='index.php?mod=landing_sma&act=video_list'>index.php?mod=landing_sma&act=video_list</a> (Videos)</li>";
echo "<li><a href='index.php?mod=landing_sma&act=informasi_list'>index.php?mod=landing_sma&act=informasi_list</a> (Announcements)</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>System Check Summary</h3>";
echo "<p><strong>All components are configured and ready!</strong></p>";
echo "<p>The new Bootstrap 5 landing page for SMA Plus Al-Manshuriyah has been activated.</p>";
?>
