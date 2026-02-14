<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status PPDB - SIMAKS</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #C41E3A;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #C41E3A;
        }

        .btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #C41E3A, #2D8A4E);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(196, 30, 58, 0.3);
        }

        .status-result {
            margin-top: 2rem;
            padding: 2rem;
            border-radius: 15px;
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-diverifikasi {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-diterima {
            background: #d4edda;
            color: #155724;
        }

        .status-ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            color: #333;
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link a {
            color: #C41E3A;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1><i class="fas fa-search mr-2"></i> Cek Status Pendaftaran PPDB</h1>

            <form method="POST" action="index.php?mod=landing&act=ppdb_status">
                <div class="form-group">
                    <label>Nomor Pendaftaran</label>
                    <input type="text" name="no_pendaftaran"
                        placeholder="Masukkan nomor pendaftaran (contoh: PPDB20250001)"
                        value="<?= $data['no_pendaftaran'] ?? '' ?>" required>
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-search"></i> Cek Status
                </button>
            </form>

            <?php if (isset($data['result'])): ?>
                <?php if ($data['result']): ?>
                    <div class="status-result">
                        <h3>Informasi Pendaftaran</h3>

                        <div style="text-align: center; margin: 1.5rem 0;">
                            <?php
                            $status = $data['result']['status'];
                            $statusClass = 'status-' . $status;
                            $statusText = [
                                'pending' => 'Menunggu Verifikasi',
                                'diverifikasi' => 'Sedang Diverifikasi',
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                                'diproses_jadi_siswa' => 'Diproses Jadi Siswa'
                            ];
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= $statusText[$status] ?? $status ?>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">No. Pendaftaran:</span>
                            <span class="info-value"><strong><?= $data['result']['no_pendaftaran'] ?></strong></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nama:</span>
                            <span class="info-value"><?= $data['result']['nama_lengkap'] ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal Daftar:</span>
                            <span class="info-value"><?= date('d M Y H:i', strtotime($data['result']['created_at'])) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jalur:</span>
                            <span class="info-value"><?= $data['result']['jalur_pendaftaran'] ?></span>
                        </div>

                        <?php if ($data['result']['catatan_verifikasi']): ?>
                            <div
                                style="margin-top: 1.5rem; padding: 1rem; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                                <strong>Catatan:</strong><br>
                                <?= nl2br($data['result']['catatan_verifikasi']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="status-result" style="background: #f8d7da; color: #721c24;">
                        <i class="fas fa-exclamation-circle"></i>
                        Nomor pendaftaran tidak ditemukan. Pastikan nomor yang Anda masukkan benar.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="index.php?mod=landing">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>

</html>