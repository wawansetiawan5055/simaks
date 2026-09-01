<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .tahfidz-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #3730a3);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
        flex-shrink: 0;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (TAHFIDZ QURAN)                     */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .card {
            border-radius: 10px !important;
            margin-bottom: 6px !important;
        }
        .card h2 {
            font-size: 1.15rem !important;
        }
        .card-header {
            padding: 10px 12px !important;
        }
        .card-header h6 {
            font-size: 0.82rem !important;
        }
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border: none;
        }
        .table th {
            padding: 6px 8px !important;
            font-size: 0.65rem !important;
            white-space: nowrap;
        }
        .table td {
            padding: 6px 8px !important;
            font-size: 0.72rem !important;
            white-space: nowrap;
        }
        .badge {
            font-size: 0.60rem !important;
            padding: 2px 6px !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="tahfidz-icon-box mr-3">
                    <i class="fas fa-quran"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Tahfidz Al-Qur'an
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Tahfidz Quran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-1">
    <div class="container-fluid">
        <!-- STATS BANNER -->
        <div class="row mb-3">
            <div class="col-md-6 col-12 mb-2">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px; border-left: 4px solid #4f46e5 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.72rem;">TOTAL SETORAN HAFALAN</small>
                            <h2 class="font-weight-bold text-dark mb-0 mt-1"><?= $tahfidz['summary']['total_setoran'] ?? 0 ?> <span class="text-muted" style="font-size: 0.85rem;">Kali</span></h2>
                        </div>
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-book-open"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12 mb-2">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px; border-left: 4px solid #10b981 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.72rem;">RATA-RATA NILAI TAJWID / KELANCARAN</small>
                            <h2 class="font-weight-bold text-success mb-0 mt-1"><?= round($tahfidz['summary']['rata_nilai'] ?? 0, 1) ?></h2>
                        </div>
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL JURNAL SETORAN -->
        <div class="card shadow-sm border-0" style="border-radius: 14px; overflow: hidden; background: #fff;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-history text-primary mr-2"></i> Log Riwayat Setoran Hafalan
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: #f8fafc;">
                            <tr class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <th class="py-3 pl-4">Tanggal</th>
                                <th class="py-3">Surah &amp; Ayat</th>
                                <th class="py-3 text-center">Nilai</th>
                                <th class="py-3">Musyrif / Pengampu</th>
                                <th class="py-3">Catatan / Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tahfidz['jurnal'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-quran fa-3x mb-3 text-muted opacity-50"></i>
                                        <p class="font-weight-bold mb-0">Belum Ada Catatan Setoran Tahfidz</p>
                                        <small class="text-muted">Setoran hafalan baru akan otomatis tercatat di sini setelah divalidasi oleh musyrif.</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tahfidz['jurnal'] as $tj): ?>
                                <tr>
                                    <td class="pl-4 align-middle text-muted small font-weight-bold">
                                        <i class="fas fa-calendar-day mr-1 text-primary"></i> <?= date('d M Y', strtotime($tj['tanggal'])) ?>
                                    </td>
                                    <td class="align-middle font-weight-bold text-dark">
                                        <?= htmlspecialchars($tj['nama_surah'] ?? '-') ?>
                                        <span class="badge badge-light border text-muted ml-1" style="font-size: 0.72rem;">
                                            Ayat <?= $tj['ayat_awal'] ?? '1' ?> - <?= $tj['ayat_akhir'] ?? '-' ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-success px-2.5 py-1 rounded-pill font-weight-bold" style="font-size: 0.85rem;">
                                            <?= $tj['nilai'] ?? '-' ?>
                                        </span>
                                    </td>
                                    <td class="align-middle small">
                                        <i class="fas fa-user-tie mr-1 text-muted"></i> <?= htmlspecialchars($tj['nama_guru'] ?? '-') ?>
                                    </td>
                                    <td class="align-middle text-muted small font-italic">
                                        <?= htmlspecialchars($tj['catatan'] ?? '-') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
