<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($dokumen['judul']) ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            margin: 20mm;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
        }
        table td, table th {
            border: 1px solid #000;
            padding: 8px;
        }
        h1, h2, h3 {
            margin-top: 20px;
        }
        .header-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #ccc;
        }
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print" 
            style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">
        <span style="font-size: 14px;">🖨️ Cetak</span>
    </button>

    <div class="header-info no-print">
        <strong>Judul:</strong> <?= htmlspecialchars($dokumen['judul']) ?><br>
        <strong>Jenis:</strong> <?= htmlspecialchars($dokumen['jenis']) ?><br>
        <strong>Mata Pelajaran:</strong> <?= htmlspecialchars($dokumen['mapel']) ?><br>
        <strong>Kelas:</strong> <?= htmlspecialchars($dokumen['kelas']) ?>
    </div>

    <div class="content">
        <?= $dokumen['konten_html'] ?>
    </div>
</body>
</html>
