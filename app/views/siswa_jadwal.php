<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    /* Hero Header */
    .jadwal-hero-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 18px;
        color: #ffffff;
        padding: 22px 26px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        border: none;
        position: relative;
        overflow: hidden;
    }
    .jadwal-hero-card::after {
        content: '';
        position: absolute;
        bottom: -40px;
        right: -20px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent 70%);
        border-radius: 50%;
    }

    /* Segmented Filter Pills */
    .filter-pills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        background: #e2e8f0;
        padding: 6px;
        border-radius: 50px;
        display: inline-flex;
    }
    .filter-pill-btn {
        border: none;
        background: transparent;
        color: #475569;
        font-weight: 700;
        font-size: 0.84rem;
        padding: 8px 20px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
    }
    .filter-pill-btn:hover {
        color: #0f172a;
    }
    .filter-pill-btn.active {
        background: #ffffff;
        color: #2563eb;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Modern Day Card */
    .day-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .day-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }
    .day-card-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .day-card-header.is-today {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-bottom-color: #bfdbfe;
    }
    .day-card-body {
        padding: 16px;
        flex-grow: 1;
    }

    /* Timeline Slot Item */
    .slot-item {
        display: flex;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 14px;
        margin-bottom: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
        position: relative;
    }
    .slot-item:last-child {
        margin-bottom: 0;
    }
    .slot-item:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    /* Left Time Pill in Slot */
    .slot-time-col {
        min-width: 105px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-right: 12px;
        border-right: 2px dashed #e2e8f0;
        text-align: center;
    }
    .slot-time-text {
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.2px;
    }
    .slot-jp-badge {
        font-size: 0.65rem;
        font-weight: 800;
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
        border-radius: 20px;
        padding: 1px 7px;
        margin-top: 4px;
    }

    /* Right Detail in Slot */
    .slot-detail-col {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .slot-badge-type {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 6px;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 4px;
    }
    .slot-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
    }
    .slot-teacher {
        font-size: 0.76rem;
        color: #64748b;
        margin-top: 3px;
        display: flex;
        align-items: center;
    }

    /* Slot Color Variants */
    .slot-kbm {
        background: #ffffff;
        border-left: 4px solid #3b82f6;
    }
    .slot-pembiasaan {
        background: #f0fdf4;
        border-left: 4px solid #10b981;
    }
    .slot-istirahat {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
    }
    .slot-kegiatan {
        background: #f8fafc;
        border-left: 4px solid #8b5cf6;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (JADWAL PELAJARAN)                 */
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
        .jadwal-hero-card {
            padding: 12px 14px !important;
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .jadwal-hero-card h3 {
            font-size: 0.98rem !important;
            margin-bottom: 4px !important;
        }
        .jadwal-hero-card p {
            font-size: 0.72rem !important;
            line-height: 1.35 !important;
        }
        .jadwal-hero-card .badge {
            font-size: 0.65rem !important;
            padding: 3px 8px !important;
        }
        .filter-pills-container {
            display: flex !important;
            width: 100% !important;
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
            -webkit-overflow-scrolling: touch;
            padding: 4px !important;
            margin-bottom: 8px !important;
            gap: 4px !important;
            border-radius: 12px !important;
        }
        .filter-pill-btn {
            padding: 5px 12px !important;
            font-size: 0.72rem !important;
            white-space: nowrap !important;
            flex-shrink: 0;
        }
        .day-card {
            border-radius: 10px !important;
            margin-bottom: 10px !important;
        }
        .day-card-header {
            padding: 8px 12px !important;
        }
        .day-card-header h5 {
            font-size: 0.82rem !important;
        }
        .day-card-header .badge {
            font-size: 0.62rem !important;
            padding: 2px 6px !important;
        }
        .day-card-body {
            padding: 8px 6px !important;
        }
        .slot-item {
            padding: 7px 8px !important;
            gap: 8px !important;
            border-radius: 8px !important;
            margin-bottom: 6px !important;
        }
        .slot-time-col {
            min-width: 72px !important;
            padding-right: 6px !important;
            border-right-width: 1.5px !important;
        }
        .slot-time-text {
            font-size: 0.68rem !important;
        }
        .slot-jp-badge {
            font-size: 0.56rem !important;
            padding: 1px 4px !important;
            margin-top: 2px !important;
        }
        .slot-title {
            font-size: 0.76rem !important;
            line-height: 1.25 !important;
        }
        .slot-teacher {
            font-size: 0.66rem !important;
            margin-top: 2px !important;
        }
        .slot-badge-type {
            font-size: 0.56rem !important;
            padding: 1px 4px !important;
            margin-bottom: 2px !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Jadwal Pelajaran KBM
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>siswa_portal/dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Portal Siswa</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Jadwal Pelajaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content mt-2">
    <div class="container-fluid">

        <?php if (!$kelas): ?>
            <div class="alert alert-warning rounded-lg shadow-sm border-0 p-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Data kelas tidak ditemukan. Pastikan penempatan siswa sudah diatur oleh Admin.
            </div>
        <?php elseif (empty($jadwal)): ?>
            <div class="alert alert-info rounded-lg shadow-sm border-0 p-4">
                <i class="fas fa-info-circle mr-2"></i>
                Jadwal pelajaran belum tersedia untuk kelas Anda.
            </div>
        <?php else: ?>

        <?php 
            $is_pjj = (($kelas['jenis_kelas'] ?? '') === 'pjj');
        ?>

        <!-- HERO INFO -->
        <div class="card jadwal-hero-card mb-4">
            <div class="row align-items-center">
                <div class="col-md-7 mb-3 mb-md-0">
                    <?php if ($is_pjj): ?>
                        <span class="badge badge-success font-weight-bold px-3 py-1 mb-2 text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px; background: #10b981;">
                            <i class="fas fa-globe mr-1"></i> Kelas <?= htmlspecialchars($kelas['nama_kelas'] ?? '-') ?> &bull; PJJ / Sekolah Terbuka
                        </span>
                        <h4 class="font-weight-bold text-white mb-1">
                            Jadwal Pembelajaran Hybrid PJJ
                        </h4>
                        <p class="text-light small mb-0 opacity-80">
                            Hari Ini: <strong><?= $hari_aktif ?></strong> &bull; Daring (Online LMS) Senin&ndash;Kamis Malam &amp; Tatap Muka Akhir Pekan.
                        </p>
                    <?php else: ?>
                        <span class="badge badge-primary font-weight-bold px-3 py-1 mb-2 text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">
                            <i class="fas fa-chalkboard mr-1"></i> Kelas <?= htmlspecialchars($kelas['nama_kelas'] ?? '-') ?>
                        </span>
                        <h4 class="font-weight-bold text-white mb-1">
                            Jadwal Belajar &amp; Pembiasaan
                        </h4>
                        <p class="text-light small mb-0 opacity-80">
                            Hari Ini: <strong><?= $hari_aktif ?></strong> &bull; Periksa jadwal harian dan persiapkan perlengkapan belajar Anda.
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-5 text-md-right">
                    <div class="d-inline-block text-left p-3 rounded-lg" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(4px);">
                        <div class="small text-light opacity-75">Tahun Ajaran:</div>
                        <div class="h6 font-weight-bold text-warning mb-0">
                            <?= htmlspecialchars($_SESSION['nama_ta_aktif'] ?? '2026/2027 Ganjil') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Logika Menentukan Default Tab Aktif Otomatis Berdasarkan Hari Ini & Jenis Kelas
        if ($is_pjj) {
            if (in_array($hari_aktif, ['Senin', 'Selasa', 'Rabu', 'Kamis'])) {
                $default_tab = 'online-lms';
            } else {
                $default_tab = 'tatap-muka';
            }
        } else {
            $default_tab = 'senin-selasa';
            if (in_array($hari_aktif, ['Senin', 'Selasa'])) {
                $default_tab = 'senin-selasa';
            } elseif (in_array($hari_aktif, ['Rabu', 'Kamis'])) {
                $default_tab = 'rabu-kamis';
            } elseif (in_array($hari_aktif, ['Jumat', 'Sabtu', 'Minggu'])) {
                $default_tab = 'jumat-sabtu';
            }
        }
        ?>

        <!-- SEGMENTED FILTER BUTTONS -->
        <div class="d-flex justify-content-center justify-content-md-start mb-4">
            <div class="filter-pills-container shadow-sm">
                <?php if ($is_pjj): ?>
                    <button type="button" class="filter-pill-btn <?= ($default_tab === 'online-lms') ? 'active' : '' ?>" onclick="switchJadwalTab('online-lms', this)">
                        <i class="fas fa-laptop-code mr-2 text-primary"></i> 🌐 Senin &ndash; Kamis (Online LMS)
                    </button>
                    <button type="button" class="filter-pill-btn <?= ($default_tab === 'tatap-muka') ? 'active' : '' ?>" onclick="switchJadwalTab('tatap-muka', this)">
                        <i class="fas fa-users-class mr-2 text-success"></i> 🏫 Sabtu &ndash; Minggu (Tatap Muka Sentra)
                    </button>
                    <button type="button" class="filter-pill-btn" onclick="switchJadwalTab('semua', this)">
                        <i class="fas fa-th-large mr-2 text-warning"></i> Semua Hari
                    </button>
                <?php else: ?>
                    <button type="button" class="filter-pill-btn <?= ($default_tab === 'senin-selasa') ? 'active' : '' ?>" onclick="switchJadwalTab('senin-selasa', this)">
                        <i class="fas fa-calendar-day mr-2 text-primary"></i> Senin &ndash; Selasa
                    </button>
                    <button type="button" class="filter-pill-btn <?= ($default_tab === 'rabu-kamis') ? 'active' : '' ?>" onclick="switchJadwalTab('rabu-kamis', this)">
                        <i class="fas fa-calendar-day mr-2 text-info"></i> Rabu &ndash; Kamis
                    </button>
                    <button type="button" class="filter-pill-btn <?= ($default_tab === 'jumat-sabtu') ? 'active' : '' ?>" onclick="switchJadwalTab('jumat-sabtu', this)">
                        <i class="fas fa-calendar-check mr-2 text-success"></i> Jumat &ndash; Sabtu
                    </button>
                    <button type="button" class="filter-pill-btn" onclick="switchJadwalTab('semua', this)">
                        <i class="fas fa-th-large mr-2 text-warning"></i> Semua Hari
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- GRID KARTU JADWAL PER HARI -->
        <div class="row" id="jadwalCardsGrid">
            <?php
            if ($is_pjj) {
                $daftar_hari = [
                    'Senin'  => ['group' => 'online-lms',  'icon' => 'fas fa-laptop-code', 'accent' => '#0284c7', 'sub' => '🌐 Daring / Online LMS (Malam)'],
                    'Selasa' => ['group' => 'online-lms',  'icon' => 'fas fa-laptop-code', 'accent' => '#6366f1', 'sub' => '🌐 Daring / Online LMS (Malam)'],
                    'Rabu'   => ['group' => 'online-lms',  'icon' => 'fas fa-laptop-code', 'accent' => '#0ea5e9', 'sub' => '🌐 Daring / Online LMS (Malam)'],
                    'Kamis'  => ['group' => 'online-lms',  'icon' => 'fas fa-laptop-code', 'accent' => '#14b8a6', 'sub' => '🌐 Daring / Online LMS (Malam)'],
                    'Sabtu'  => ['group' => 'tatap-muka',  'icon' => 'fas fa-school',      'accent' => '#10b981', 'sub' => '🏫 Tatap Muka / Tutorial Sentra'],
                    'Minggu' => ['group' => 'tatap-muka',  'icon' => 'fas fa-school',      'accent' => '#f59e0b', 'sub' => '🏫 Tatap Muka / Tutorial Sentra'],
                ];
            } else {
                $daftar_hari = [
                    'Senin'  => ['group' => 'senin-selasa', 'icon' => 'fas fa-calendar-alt', 'accent' => '#3b82f6', 'sub' => 'Awal Pekan'],
                    'Selasa' => ['group' => 'senin-selasa', 'icon' => 'fas fa-calendar-alt', 'accent' => '#6366f1', 'sub' => 'Awal Pekan'],
                    'Rabu'   => ['group' => 'rabu-kamis',   'icon' => 'fas fa-calendar-alt', 'accent' => '#0ea5e9', 'sub' => 'Tengah Pekan'],
                    'Kamis'  => ['group' => 'rabu-kamis',   'icon' => 'fas fa-calendar-alt', 'accent' => '#14b8a6', 'sub' => 'Tengah Pekan'],
                    'Jumat'  => ['group' => 'jumat-sabtu',  'icon' => 'fas fa-calendar-check', 'accent' => '#10b981', 'sub' => 'Akhir Pekan'],
                    'Sabtu'  => ['group' => 'jumat-sabtu',  'icon' => 'fas fa-calendar-check', 'accent' => '#f59e0b', 'sub' => 'Akhir Pekan'],
                    'Minggu' => ['group' => 'jumat-sabtu',  'icon' => 'fas fa-calendar-check', 'accent' => '#ea580c', 'sub' => 'Akhir Pekan'],
                ];
            }

            foreach ($daftar_hari as $hari => $meta):
                $slots = $jadwal[$hari] ?? [];
                $is_today = ($hari === $hari_aktif);
                $is_hidden = ($default_tab !== 'semua' && $meta['group'] !== $default_tab);
            ?>
            <div class="col-lg-6 col-12 mb-4 day-card-wrapper" data-group="<?= $meta['group'] ?>" style="<?= $is_hidden ? 'display: none;' : '' ?>">
                <div class="day-card">
                    <!-- HEADER KARTU HARI -->
                    <div class="day-card-header <?= $is_today ? 'is-today' : '' ?>">
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: <?= $is_today ? '#2563eb' : '#e2e8f0' ?>; color: <?= $is_today ? '#fff' : '#475569' ?>;">
                                <i class="<?= $meta['icon'] ?>"></i>
                            </span>
                            <div>
                                <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 1.05rem;">
                                    Hari <?= $hari ?>
                                </h6>
                                <small class="text-muted"><?= count($slots) ?> Sesi Terjadwal</small>
                            </div>
                        </div>

                        <div>
                            <?php if ($is_today): ?>
                                <span class="badge badge-primary px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 50px; font-size: 0.72rem;">
                                    <i class="fas fa-dot-circle mr-1"></i> HARI INI
                                </span>
                            <?php else: ?>
                                <span class="badge badge-light border text-muted px-2 py-1 font-weight-bold" style="border-radius: 50px; font-size: 0.7rem;">
                                    <?= htmlspecialchars($meta['sub']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BODY KARTU / TIMELINE SLOTS -->
                    <div class="day-card-body">
                        <?php if (empty($slots)): ?>
                            <div class="p-4 text-center text-muted font-italic">
                                <i class="fas fa-mug-hot fa-2x mb-2 text-muted opacity-50"></i>
                                <p class="mb-0 small">Tidak ada jadwal KBM / kegiatan pada hari <?= $hari ?>.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($slots as $slot): 
                                $is_kbm = (($slot['jenis_jam'] ?? '') === 'KBM' || ($slot['jenis_kegiatan'] ?? '') === 'KBM');
                                $jenis_raw = strtolower($slot['jenis_kegiatan'] ?? '');
                                $mode_kbm = $slot['mode_kbm'] ?? 'offline';
                                
                                // Logika Cerdas: Jika kelas PJJ di hari Senin-Kamis atau mode_kbm online -> Daring
                                $is_online = ($mode_kbm === 'online' || ($is_pjj && in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis'])));

                                // Variant Class & Badge
                                $slot_class = 'slot-kbm';
                                if ($is_online) {
                                    $badge_bg = 'background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;';
                                    $badge_label = '🌐 Daring / Online LMS';
                                } else {
                                    $badge_bg = 'background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;';
                                    $badge_label = $is_pjj ? '🏫 Tatap Muka (Sentra)' : '🏫 Tatap Muka (KBM)';
                                }

                                if (!$is_kbm) {
                                    if (strpos($jenis_raw, 'istirahat') !== false) {
                                        $slot_class = 'slot-istirahat';
                                        $badge_bg = 'background: #fef3c7; color: #d97706; border: 1px solid #fde68a;';
                                        $badge_label = 'Istirahat';
                                    } elseif (strpos($jenis_raw, 'pembiasaan') !== false || strpos($jenis_raw, 'upacara') !== false || strpos($jenis_raw, 'tadarus') !== false) {
                                        $slot_class = 'slot-pembiasaan';
                                        $badge_bg = 'background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;';
                                        $badge_label = $slot['jenis_kegiatan'] ?? 'Pembiasaan';
                                    } else {
                                        $slot_class = 'slot-kegiatan';
                                        $badge_bg = 'background: #f3e8ff; color: #9333ea; border: 1px solid #e9d5ff;';
                                        $badge_label = $slot['jenis_kegiatan'] ?? 'Kegiatan';
                                    }
                                }

                                $nama_display = $is_kbm ? ($slot['nama_mapel'] ?: ($slot['nama_kegiatan'] ?: 'KBM')) : ($slot['nama_kegiatan'] ?: '-');
                                $jp = (int)($slot['jp_count'] ?? 1);
                            ?>
                            <div class="slot-item <?= $slot_class ?>">
                                <!-- WAKTU & JP -->
                                <div class="slot-time-col">
                                    <span class="slot-time-text">
                                        <?= htmlspecialchars(substr($slot['jam_mulai'] ?? '', 0, 5)) ?>
                                    </span>
                                    <span class="text-muted" style="font-size: 0.65rem; line-height: 1;">s.d</span>
                                    <span class="slot-time-text">
                                        <?= htmlspecialchars(substr($slot['jam_selesai'] ?? '', 0, 5)) ?>
                                    </span>
                                    <?php if ($jp > 1): ?>
                                        <span class="slot-jp-badge"><?= $jp ?> JP</span>
                                    <?php endif; ?>
                                </div>

                                <!-- DETAIL MAPEL & GURU -->
                                <div class="slot-detail-col">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 4px;">
                                        <span class="slot-badge-type" style="<?= $badge_bg ?>"><?= htmlspecialchars($badge_label) ?></span>
                                        <?php if ($is_online): ?>
                                            <a href="<?= BASE_URL ?>siswa_portal/materi<?= !empty($slot['id_mapel']) ? '?id_mapel=' . (int)$slot['id_mapel'] : '' ?>" class="btn btn-xs btn-outline-info font-weight-bold px-2 py-0.5 rounded-pill shadow-none" style="font-size: 0.65rem;">
                                                <i class="fas fa-book-reader mr-1"></i> Buka Modul
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="slot-title">
                                        <?= htmlspecialchars($nama_display) ?>
                                    </div>
                                    <?php if (!empty($slot['nama_guru']) && $slot['nama_guru'] !== '-'): ?>
                                        <div class="slot-teacher">
                                            <i class="fas fa-chalkboard-teacher mr-1 text-primary"></i> <?= htmlspecialchars($slot['nama_guru']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<!-- SCRIPT UNTUK FILTER SEGMENTED TAB JADWAL -->
<script>
    function switchJadwalTab(targetGroup, btnElement) {
        // Update active class on filter buttons
        document.querySelectorAll('.filter-pill-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        btnElement.classList.add('active');

        // Filter cards visibility
        const cards = document.querySelectorAll('.day-card-wrapper');
        cards.forEach(card => {
            const cardGroup = card.getAttribute('data-group');
            if (targetGroup === 'semua' || cardGroup === targetGroup) {
                card.style.display = '';
                // Animasi fade in lembut
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.25s ease';
                    card.style.opacity = '1';
                }, 10);
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
