<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Login SIMAKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Screen Only Toolbar */
        @media screen {
            body {
                background-color: #525659;
                margin: 0;
                padding-top: 55px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
            }

            .btn-container {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 10000;
                background: #323639;
                height: 48px;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 20px;
                box-sizing: border-box;
                border-bottom: 1px solid rgba(0, 0, 0, 0.2);
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                color: white;
            }

            .toolbar-title {
                font-size: 14px;
                font-weight: 500;
                color: #f1f1f1;
            }

            .chrome-btn {
                background: transparent;
                border: none;
                color: #f1f1f1;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.2s;
            }
            .chrome-btn:hover { background: rgba(255, 255, 255, 0.1); }

            .btn-print-main {
                background-color: #28a745;
                color: white;
                border: none;
                padding: 6px 16px;
                border-radius: 6px;
                font-weight: 600;
                font-size: 13px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-print-main:hover { background-color: #218838; }

            .page-sheet {
                background: white;
                width: 210mm;
                min-height: 297mm;
                padding: 15mm 10mm;
                margin: 20px auto;
                box-shadow: 0 0 20px rgba(0,0,0,0.4);
                box-sizing: border-box;
            }
        }

        /* Print Media */
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .page-sheet { width: 100%; margin: 0; padding: 5mm; box-shadow: none; }
            @page { size: A4 portrait; margin: 8mm; }
        }

        /* Card Layout Grid */
        .cards-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12mm 8mm;
            justify-content: flex-start;
        }

        /* ID Card Box (CR80 Standard Specs: 86mm x 54mm) */
        .id-card {
            width: 86mm;
            height: 54mm;
            border: 1.5px solid #0f172a;
            border-radius: 8px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .card-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 2px solid #38bdf8;
        }

        .card-header img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .header-text {
            line-height: 1.1;
        }

        .header-text .sch-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #ffffff;
        }

        .header-text .sch-sub {
            font-size: 6.5px;
            color: #38bdf8;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-body {
            display: flex;
            padding: 6px 8px;
            gap: 8px;
            flex: 1;
            align-items: center;
            background: #f8fafc;
        }

        .photo-box {
            width: 24mm;
            height: 32mm;
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            overflow: hidden;
            background: #e2e8f0;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            gap: 2px;
        }

        .user-nama {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .role-badge {
            display: inline-block;
            background: #0284c7;
            color: white;
            font-size: 6.5px;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
            text-transform: uppercase;
            width: fit-content;
            margin-bottom: 2px;
        }

        .info-detail {
            font-size: 7.5px;
            color: #334155;
            line-height: 1.35;
        }

        .info-detail strong {
            color: #0f172a;
        }

        .qr-box {
            width: 22mm;
            height: 22mm;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 2px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-footer {
            background: #0f172a;
            color: #94a3b8;
            font-size: 6px;
            text-align: center;
            padding: 2px 4px;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <!-- Screen Toolbar -->
    <div class="btn-container no-print">
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div class="toolbar-title"><i class="fas fa-id-card mr-2"></i> Cetak Kartu Akses Login SIMAKS</div>
            <?php if (!$id_pengguna && $type !== 'guru' && !empty($kelas_list)): ?>
                <div style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                    <span style="color: #94a3b8;"><i class="fas fa-filter"></i> Filter Kelas:</span>
                    <select onchange="window.location.href='<?= BASE_URL ?>manajemen_pengguna/print_kartu?type=siswa&id_kelas='+this.value" 
                            style="background: #1e293b; color: #f8fafc; border: 1px solid #475569; padding: 4px 10px; border-radius: 6px; font-size: 12px; cursor: pointer; outline: none;">
                        <option value="">-- Seluruh Siswa (Semua Kelas) --</option>
                        <?php foreach ($kelas_list as $kls): ?>
                            <option value="<?= $kls['id_kelas'] ?>" <?= ($id_kelas == $kls['id_kelas']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kls['nama_kelas']) ?> (Tingkat <?= $kls['tingkat'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button onclick="window.print()" class="btn-print-main"><i class="fas fa-print"></i> Cetak Kartu</button>
            <button onclick="window.close()" class="chrome-btn" title="Tutup"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- Printable Sheet -->
    <div class="page-sheet">
        <?php if (empty($users_data)): ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                <p>Tidak ada data pengguna yang dipilih untuk dicetak.</p>
            </div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($users_data as $u): 
                        $photo_url = get_user_photo($u['id_pengguna'], $u['nama_pengguna'], $u['jk'] ?? null);
                        $qr_token = !empty($u['qr_token']) ? $u['qr_token'] : ($u['username'] ?? '');
                        $qr_url = !empty($qr_token) ? 'https://quickchart.io/qr?text=' . urlencode($qr_token) . '&size=150&margin=1' : '';
                        
                        $is_siswa = !empty($u['id_siswa']);
                        $is_guru = !empty($u['id_guru']);
                        $role_label = $is_siswa ? 'SISWA' : ($is_guru ? 'GURU' : 'STAF');
                        if (strpos(strtolower($u['roles'] ?? ''), 'admin') !== false) {
                            $role_label = 'ADMIN';
                        }
                    ?>
                    <div class="id-card">
                        <!-- Header Kop -->
                        <div class="card-header">
                            <img src="<?= BASE_URL ?>assets/img/logoapk.png" alt="Logo">
                            <div class="header-text">
                                <div class="sch-title"><?= htmlspecialchars($kop['kop_nama'] ?? 'SIMAKS ACADEMIC') ?></div>
                                <div class="sch-sub">KARTU LOGIN / AKSES SIMAKS</div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Foto -->
                            <div class="photo-box">
                                <img src="<?= $photo_url ?>" alt="Foto User">
                            </div>

                            <!-- Info Pengguna -->
                            <div class="info-box">
                                <div class="role-badge"><?= $role_label ?></div>
                                <div class="user-nama" title="<?= htmlspecialchars($u['nama_pengguna']) ?>">
                                    <?= htmlspecialchars($u['nama_pengguna']) ?>
                                </div>
                                <div class="info-detail">
                                    <strong>User:</strong> <?= htmlspecialchars($u['username']) ?><br>
                                    <?php if ($is_siswa): ?>
                                        <strong>NISN:</strong> <?= htmlspecialchars($u['nisn'] ?: '-') ?><br>
                                        <strong>Kelas:</strong> <?= htmlspecialchars($u['nama_kelas'] ?: '-') ?>
                                    <?php elseif ($is_guru): ?>
                                        <strong>NIP/NIK:</strong> <?= htmlspecialchars($u['nik'] ?: ($u['nuptk'] ?: '-')) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- QR Code -->
                            <?php if ($qr_url): ?>
                            <div class="qr-box">
                                <img src="<?= $qr_url ?>" alt="QR Code Login">
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer">
                            Pindai QR Code pada kamera Login SIMAKS untuk masuk tanpa ketik password
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
