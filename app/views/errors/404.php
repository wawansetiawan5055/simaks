<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | SIMAKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>assets/css/adminlte.v3.2.0.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 520px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .error-code {
            font-size: 5.5rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
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
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.35);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">404</div>
        <h4 class="font-weight-bold text-dark mb-2">Halaman Tidak Ditemukan</h4>
        <p class="text-muted small mb-4">
            Mohon maaf, halaman atau modul yang Anda cari tidak tersedia, telah dipindahkan, atau tautan yang dimasukkan kurang tepat.
        </p>
        <div class="d-flex justify-content-center gap-2">
            <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="btn btn-primary btn-home shadow-sm mr-2">
                <i class="fas fa-home mr-2"></i> Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-home">
                <i class="fas fa-arrow-left mr-2"></i> Halaman Sebelumnya
            </button>
        </div>
    </div>
</body>
</html>
