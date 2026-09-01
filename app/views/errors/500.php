<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Terjadi Kendala Sistem | SIMAKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>assets/css/adminlte.v3.2.0.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 12px 32px rgba(220, 38, 38, 0.08);
            max-width: 600px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
            border: 1px solid #fecaca;
        }
        .error-icon {
            width: 72px;
            height: 72px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 20px;
        }
        .btn-home {
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(220, 38, 38, 0.3);
        }
        .debug-box {
            text-align: left;
            background: #1e293b;
            color: #f8fafc;
            padding: 16px;
            border-radius: 12px;
            font-family: monospace;
            font-size: 0.8rem;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h4 class="font-weight-bold text-dark mb-2">Terjadi Kendala Sistem</h4>
        <p class="text-muted small mb-4">
            Sistem mendeteksi adanya kendala saat memproses permintaan Anda. Detail error telah dicatat secara otomatis untuk ditinjau oleh administrator.
        </p>

        <div class="d-flex justify-content-center gap-2">
            <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="btn btn-danger btn-home shadow-sm mr-2">
                <i class="fas fa-home mr-2"></i> Kembali ke Beranda
            </a>
            <button onclick="location.reload()" class="btn btn-outline-secondary btn-home">
                <i class="fas fa-redo mr-2"></i> Coba Muat Ulang
            </button>
        </div>

        <?php if (!empty($errorMessage) && (isset($_SESSION['roles']) && in_array('Admin', $_SESSION['roles']))): ?>
            <div class="debug-box mt-4">
                <strong><i class="fas fa-bug text-warning mr-1"></i> Mode Pengembang (Admin Only):</strong><br>
                <?= htmlspecialchars($errorMessage) ?><br>
                <?php if (!empty($errorFile)): ?>
                    <small class="text-muted">Di: <?= htmlspecialchars($errorFile) ?>:<?= $errorLine ?? '' ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
