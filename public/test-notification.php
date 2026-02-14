<?php
/**
 * Test Page for Toast Notification System
 * This page demonstrates all 4 notification types
 */

// Start session
session_start();

// Define BASE_URL BEFORE including header
define('BASE_URL', '/simaks/public/');

// Initialize database connection and required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helper.php';
$pdo = connect_db();

// Handle form submissions to trigger notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['test_success'])) {
        $_SESSION['pesan_sukses'] = 'Data berhasil disimpan! Ini adalah contoh notifikasi sukses.';
    } elseif (isset($_POST['test_error'])) {
        $_SESSION['pesan_error'] = 'Terjadi kesalahan! Ini adalah contoh notifikasi error.';
    } elseif (isset($_POST['test_warning'])) {
        $_SESSION['pesan_warning'] = 'Perhatian! Ini adalah contoh notifikasi peringatan.';
    } elseif (isset($_POST['test_info'])) {
        $_SESSION['pesan_info'] = 'Informasi penting untuk Anda. Ini adalah contoh notifikasi info.';
    } elseif (isset($_POST['test_multiple'])) {
        $_SESSION['pesan_sukses'] = 'Operasi pertama berhasil';
        $_SESSION['pesan_warning'] = 'Ada beberapa peringatan yang perlu diperhatikan';
        $_SESSION['pesan_info'] = 'Silakan cek kembali data Anda';
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

include __DIR__ . '/../app/views/partials/header.php';
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-bell mr-2"></i> Test Sistem Notifikasi</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        <!-- Info Card -->
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info-circle"></i> Petunjuk Testing</h5>
            Klik tombol di bawah untuk menguji berbagai tipe notifikasi toast. Notifikasi akan muncul di pojok kanan atas dengan animasi smooth.
        </div>

        <!-- Test Buttons Card -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-vial mr-2"></i> Test Notifikasi Individual</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card card-success">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-check-circle mr-2"></i> Success Notification</h5>
                            </div>
                            <div class="card-body">
                                <p>Auto-hide setelah 3 detik. Hijau dengan ikon checkmark.</p>
                                <form method="POST" class="d-inline">
                                    <button type="submit" name="test_success" class="btn btn-success">
                                        <i class="fas fa-check mr-1"></i> Test Success
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                <div class="card card-danger">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-times-circle mr-2"></i> Error Notification</h5>
                            </div>
                            <div class="card-body">
                                <p>Tidak auto-hide, harus diklik close. Merah dengan ikon X.</p>
                                <form method="POST" class="d-inline">
                                    <button type="submit" name="test_error" class="btn btn-danger">
                                        <i class="fas fa-times mr-1"></i> Test Error
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i> Warning Notification</h5>
                            </div>
                            <div class="card-body">
                                <p>Auto-hide setelah 5 detik. Kuning dengan ikon warning.</p>
                                <form method="POST" class="d-inline">
                                    <button type="submit" name="test_warning" class="btn btn-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Test Warning
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card card-info">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-info-circle mr-2"></i> Info Notification</h5>
                            </div>
                            <div class="card-body">
                                <p>Auto-hide setelah 4 detik. Biru dengan ikon info.</p>
                                <form method="POST" class="d-inline">
                                    <button type="submit" name="test_info" class="btn btn-info">
                                        <i class="fas fa-info-circle mr-1"></i> Test Info
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Multiple Notifications -->
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-layer-group mr-2"></i> Test Multiple Notifications</h3>
            </div>
            <div class="card-body">
                <p>Test stacking beberapa notifikasi sekaligus:</p>
                <form method="POST" class="d-inline">
                    <button type="submit" name="test_multiple" class="btn btn-secondary btn-lg">
                        <i class="fas fa-copy mr-1"></i> Test 3 Notifikasi Sekaligus
                    </button>
                </form>
            </div>
        </div>

        <!-- JavaScript Manual Test -->
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-code mr-2"></i> Manual JavaScript Test</h3>
            </div>
            <div class="card-body">
                <p>Gunakan JavaScript console untuk testing manual:</p>
                <div class="bg-dark p-3 rounded">
                    <code class="text-white">
                        // Test individual notifications<br>
                        Notify.success('Judul', 'Pesan sukses');<br>
                        Notify.error('Judul', 'Pesan error');<br>
                        Notify.warning('Judul', 'Pesan warning');<br>
                        Notify.info('Judul', 'Pesan info');<br>
                        <br>
                        // Or use the show method directly<br>
                        Notify.show('success', 'Judul', 'Pesan', {duration: 5000});
                    </code>
                </div>
                <button class="btn btn-dark mt-3" onclick="testManualNotifications()">
                    <i class="fas fa-play mr-1"></i> Run Manual Test
                </button>
            </div>
        </div>

        <!-- Features List -->
        <div class="card">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title"><i class="fas fa-star mr-2"></i> Fitur Sistem Notifikasi</h3>
            </div>
            <div class="card-body">
                <ul class="fa-ul">
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>4 Tipe Notifikasi:</strong> Success, Error, Warning, Info</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>Auto-hide:</strong> Success (3s), Warning (5s), Info (4s), Error (manual)</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>Stacking:</strong> Menampilkan hingga 5 notifikasi sekaligus</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>Smooth Animation:</strong> Slide in/out dengan cubic-bezier</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>Glassmorphism Design:</strong> Modern dengan backdrop blur</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>Responsive:</strong> Menyesuaikan dengan ukuran layar</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>Accessible:</strong> Support keyboard, screen readers, reduced motion</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>XSS Protection:</strong> Auto-escape HTML dalam pesan</li>
                    <li><span class="fa-li"><i class="fas fa-check text-success"></i></span> <strong>PHP Integration:</strong> Auto-detect session messages</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<script>
function testManualNotifications() {
    setTimeout(() => Notify.success('Test 1', 'Notifikasi sukses dari JavaScript'), 100);
    setTimeout(() => Notify.error('Test 2', 'Notifikasi error dari JavaScript'), 600);
    setTimeout(() => Notify.warning('Test 3', 'Notifikasi warning dari JavaScript'), 1100);
    setTimeout(() => Notify.info('Test 4', 'Notifikasi info dari JavaScript'), 1600);
}
</script>

<?php include __DIR__ . '/../app/views/partials/footer.php'; ?>
