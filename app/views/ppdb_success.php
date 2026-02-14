<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - PPDB</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .success-container {
            background: white;
            border-radius: 30px;
            padding: 3rem;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #11998e, #38ef7d);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease-out 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .success-icon i {
            font-size: 3rem;
            color: white;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .registration-number {
            background: linear-gradient(135deg, #C41E3A, #2D8A4E);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .info-text {
            color: #666;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            margin: 0.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #C41E3A, #2D8A4E);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(196, 30, 58, 0.3);
        }

        .btn-outline {
            border: 2px solid #C41E3A;
            color: #C41E3A;
        }

        .btn-outline:hover {
            background: #C41E3A;
            color: white;
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1><i class="fas fa-check-circle mr-2"></i> Pendaftaran Berhasil!</h1>
        <p class="info-text">
            Terima kasih telah mendaftar. Berikut adalah nomor pendaftaran Anda:
        </p>

        <div class="registration-number">
            <?= $data['no_pendaftaran'] ?? 'N/A' ?>
        </div>

        <p class="info-text">
            <strong>SIMPAN nomor pendaftaran ini!</strong><br>
            Anda dapat menggunakan nomor ini untuk mengecek status pendaftaran.<br>
            Kami akan menghubungi Anda melalui kontak yang telah didaftarkan.
        </p>

        <div>
            <a href="index.php?mod=landing&act=ppdb_status&no=<?= $data['no_pendaftaran'] ?>" class="btn btn-primary">
                <i class="fas fa-search"></i> Cek Status Pendaftaran
            </a>
            <a href="index.php?mod=landing" class="btn btn-outline">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>