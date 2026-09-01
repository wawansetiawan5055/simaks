<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
<?php
$user_roles = $user_roles ?? ($_SESSION['roles'] ?? ['Siswa']);
$is_siswa = in_array('Siswa', $user_roles);
$id_tugas = (int)($_GET['tugas'] ?? ($_GET['id_tugas'] ?? ($id_tugas ?? 0)));
$is_penugasan = !$is_siswa || ($id_tugas > 0);

if (empty($materi_progress) || !is_array($materi_progress)) {
    $materi_progress = [
        'stage_1_orientasi' => 1,
        'stage_2_video' => 0,
        'stage_3_materi' => 0,
        'stage_4_formatif' => 0,
        'stage_5_diskusi' => 0,
        'stage_6_refleksi' => 0,
        'is_completed' => 0,
        'current_stage' => 1,
        'percent' => 16
    ];
} else {
    $materi_progress = array_merge([
        'stage_1_orientasi' => 1,
        'stage_2_video' => 0,
        'stage_3_materi' => 0,
        'stage_4_formatif' => 0,
        'stage_5_diskusi' => 0,
        'stage_6_refleksi' => 0,
        'is_completed' => 0,
        'current_stage' => 1,
        'percent' => 16
    ], $materi_progress);
}
$diskusi_list = $diskusi_list ?? [];
$has_submitted = $has_submitted ?? false;
$soal_list = $soal_list ?? [];
?>

<style>
    /* ============================================================ */
    /* CONTAINER & BASE STYLES                                      */
    /* ============================================================ */
    .modul-container { 
        width: 100%; 
        background: #ffffff; 
        border-radius: 20px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); 
        margin-bottom: 40px; 
        overflow: hidden; 
    }
    .modul-inner { 
        max-width: 1050px; 
        margin: 0 auto; 
        width: 100%; 
    }
    .modul-banner { 
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); 
        padding: 35px 30px 25px; 
        color: #ffffff; 
        position: relative; 
    }
    .modul-title { 
        font-size: 1.75rem; 
        font-weight: 800; 
        line-height: 1.3; 
        margin: 0; 
        color: #ffffff; 
        word-break: break-word;
    }
    
    .identity-card { 
        background: rgba(255, 255, 255, 0.12); 
        backdrop-filter: blur(12px); 
        -webkit-backdrop-filter: blur(12px);
        border-radius: 14px; 
        padding: 16px 20px; 
        border: 1px solid rgba(255, 255, 255, 0.2); 
        margin-top: 15px; 
    }
    .identity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px 16px;
    }
    .identity-item { 
        display: flex; 
        flex-direction: column;
        gap: 2px;
    }
    .id-label { 
        font-size: 0.72rem; 
        font-weight: 600; 
        opacity: 0.8; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        color: #e0e7ff;
    }
    .id-value { 
        font-size: 0.92rem; 
        font-weight: 700; 
        color: #ffffff;
    }
    
    /* ============================================================ */
    /* 🪜 TITIAN TANGGA PER-BARIS (COLLAPSIBLE STEPPER ROWS)        */
    /* ============================================================ */
    .lp-row-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        margin-bottom: 16px;
        overflow: hidden;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .lp-row-card:hover {
        border-color: #cbd5e1;
    }
    .lp-row-card.completed {
        border-color: #10b981;
        background: #ffffff;
    }
    .lp-row-card.active {
        border-color: #4f46e5;
        box-shadow: 0 4px 20px rgba(79, 70, 229, 0.08);
    }

    .lp-row-header {
        padding: 18px 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        user-select: none;
        background: #ffffff;
        transition: background 0.2s ease;
    }
    .lp-row-header:hover {
        background: #f8fafc;
    }
    .lp-row-card.active .lp-row-header {
        background: #f8fafc;
    }
    .lp-row-card.completed .lp-row-header {
        background: #f0fdf4;
    }

    .lp-badge-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        flex-shrink: 0;
        margin-right: 16px;
        transition: all 0.3s ease;
    }
    .lp-row-card.completed .lp-badge-circle {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }
    .lp-row-card.active .lp-badge-circle {
        background: linear-gradient(135deg, #4f46e5, #3730a3);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.25);
    }
    .lp-row-card.locked .lp-badge-circle {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px dashed #cbd5e1;
    }

    .lp-row-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .lp-row-card.completed .lp-row-title {
        color: #065f46;
    }
    .lp-row-card.active .lp-row-title {
        color: #3730a3;
    }
    .lp-row-subtitle {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 2px;
    }

    .lp-chevron-icon {
        font-size: 0.9rem;
        color: #64748b;
        transition: transform 0.25s ease;
    }
    .lp-row-card.expanded .lp-chevron-icon {
        transform: rotate(180deg);
    }

    .lp-row-body {
        padding: 24px 28px 30px;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .lp-row-card.completed .lp-row-body {
        border-top-color: #dcfce7;
    }

    /* Article & Tables */
    .article-content { 
        font-size: 0.98rem; 
        line-height: 1.8; 
        color: #334155; 
        word-break: break-word;
    }
    .article-content h1, .article-content h2, .article-content h3 {
        color: #1e293b;
        font-weight: 700;
        margin-top: 16px;
        margin-bottom: 10px;
    }
    .article-content img { 
        max-width: 100%; 
        height: auto;
        border-radius: 10px; 
        margin: 12px 0; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.06); 
    }
    .article-content table {
        min-width: 560px !important;
        width: 100% !important;
        margin: 12px 0 !important;
        border-collapse: collapse !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 0.92rem;
    }
    .article-content table th,
    .article-content table td {
        padding: 8px 12px !important;
        vertical-align: middle !important;
        line-height: 1.4 !important;
        border: 1px solid #e2e8f0 !important;
        height: auto !important;
    }
    .article-content table th {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
        font-weight: 700 !important;
        text-align: center;
        padding: 10px 12px !important;
        white-space: nowrap;
    }
    .article-table-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 15px 0;
        border-radius: 8px;
        border: 1px solid #edf2f7;
    }

    /* Video Embed */
    .video-wrapper { 
        position: relative; 
        padding-bottom: 56.25%; 
        height: 0; 
        overflow: hidden; 
        border-radius: 12px; 
        background: #000; 
        margin: 15px 0; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .video-wrapper iframe { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        border: 0; 
    }

    /* Quiz Cards */
    .quiz-card { 
        background: #ffffff; 
        border: 1.5px solid #e2e8f0; 
        border-radius: 12px; 
        padding: 20px; 
        margin-top: 15px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .option-card { 
        border: 1.5px solid #e2e8f0; 
        border-radius: 8px; 
        padding: 10px 14px; 
        margin-bottom: 8px; 
        cursor: pointer; 
        transition: all 0.2s ease; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        background: #f8fafc;
        width: 100%;
    }
    .option-card:hover { 
        border-color: #6366f1; 
        background: #eff6ff; 
    }
    .option-letter-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #334155;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .path-step-footer {
        margin-top: 25px;
        padding-top: 18px;
        border-top: 1px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* ============================================================ */
    /* 📱 ULTRA-COMPACT MOBILE RESPONSIVENESS (ISOLATED TO MATERI)  */
    /* ============================================================ */
    @media (max-width: 768px) {
        .page-materi-detail {
            padding: 0 !important;
            margin: 0 !important;
        }
        .page-materi-detail .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .page-materi-detail .modul-container { 
            border-radius: 0 !important; 
            margin-bottom: 12px !important; 
            border: none !important;
            box-shadow: none !important;
        }
        .page-materi-detail .modul-banner { 
            padding: 10px 10px 8px !important; 
            border-radius: 0 !important;
        }
        .page-materi-detail .modul-title { 
            font-size: 0.88rem !important; 
            line-height: 1.25 !important;
        }
        
        .page-materi-detail .identity-card { 
            padding: 5px 8px !important; 
            margin-top: 5px !important;
            border-radius: 6px !important;
        }
        .page-materi-detail .identity-grid {
            grid-template-columns: 1fr !important;
            gap: 2px !important;
        }
        .page-materi-detail .id-label {
            font-size: 0.55rem !important;
            letter-spacing: 0.2px;
        }
        .page-materi-detail .id-value {
            font-size: 0.68rem !important;
        }
        
        /* Stepper Rows */
        .page-materi-detail .lp-row-card {
            border-radius: 6px !important;
            margin-bottom: 5px !important;
            box-shadow: none !important;
        }
        .page-materi-detail .lp-row-header {
            padding: 6px 8px !important;
        }
        .page-materi-detail .lp-badge-circle {
            width: 20px !important;
            height: 20px !important;
            font-size: 0.65rem !important;
            margin-right: 6px !important;
        }
        .page-materi-detail .lp-row-title {
            font-size: 0.75rem !important;
            font-weight: 700 !important;
        }
        .page-materi-detail .lp-row-subtitle {
            font-size: 0.60rem !important;
            line-height: 1.15 !important;
            margin-top: 1px !important;
        }
        .page-materi-detail .lp-row-body {
            padding: 6px 6px 8px !important;
        }
        
        /* Typography Content & Headers Scaling */
        .page-materi-detail .cp-description-text {
            font-size: 0.70rem !important;
            line-height: 1.4 !important;
        }
        .page-materi-detail .article-content,
        .page-materi-detail .prose-content,
        .page-materi-detail .reading-box {
            font-size: 0.70rem !important;
            line-height: 1.4 !important;
            padding: 6px 6px !important;
        }
        .page-materi-detail .article-content h1,
        .page-materi-detail .article-content h2,
        .page-materi-detail .article-content h3 {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            margin-top: 4px !important;
            margin-bottom: 2px !important;
        }
        .page-materi-detail .article-content h4,
        .page-materi-detail .article-content h5,
        .page-materi-detail .article-content h6 {
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            margin-top: 3px !important;
            margin-bottom: 2px !important;
        }
        .page-materi-detail .article-content p,
        .page-materi-detail .article-content li,
        .page-materi-detail .article-content td,
        .page-materi-detail .article-content th,
        .page-materi-detail .article-content span {
            font-size: 0.70rem !important;
        }
        .page-materi-detail h6 {
            font-size: 0.72rem !important;
        }
        .page-materi-detail small, .page-materi-detail .small {
            font-size: 0.64rem !important;
        }
        
        /* Quiz & Formatif */
        .page-materi-detail .quiz-card {
            padding: 6px !important;
            border-radius: 6px !important;
        }
        .page-materi-detail .quiz-soal-box {
            padding: 6px 8px !important;
            font-size: 0.70rem !important;
            margin-bottom: 5px !important;
            border-radius: 6px !important;
        }
        .page-materi-detail .option-card {
            padding: 4px 6px !important;
            font-size: 0.68rem !important;
            margin-bottom: 3px !important;
            border-radius: 5px !important;
            gap: 5px !important;
        }
        .page-materi-detail .option-content {
            font-size: 0.68rem !important;
        }
        .page-materi-detail .option-letter-badge {
            width: 18px !important;
            height: 18px !important;
            font-size: 0.58rem !important;
        }
        .page-materi-detail #main-quiz-form button[type="submit"] {
            font-size: 0.72rem !important;
            padding: 6px 10px !important;
            width: 100% !important;
            border-radius: 20px !important;
        }
        
        /* Footer Nav Buttons */
        .page-materi-detail .path-step-footer {
            flex-direction: column;
            align-items: stretch;
            gap: 5px !important;
            padding-top: 6px !important;
            margin-top: 8px !important;
        }
        .page-materi-detail .path-step-footer .btn {
            width: 100%;
            font-size: 0.74rem !important;
            padding: 5px 8px !important;
        }
        
        /* Discussion Thread Cards & Buttons */
        .page-materi-detail #formPostDiskusiUtama textarea {
            font-size: 0.74rem !important;
            padding: 6px 8px !important;
        }
        .page-materi-detail #btnKirimDiskusiUtama {
            font-size: 0.74rem !important;
            padding: 6px 12px !important;
            width: 100% !important;
        }
        .page-materi-detail #containerDaftarDiskusi .card {
            border-radius: 6px !important;
            margin-bottom: 5px !important;
        }
        .page-materi-detail #containerDaftarDiskusi .card-body {
            padding: 6px 6px !important;
        }
        .page-materi-detail #containerDaftarDiskusi p {
            font-size: 0.74rem !important;
        }
        .page-materi-detail .input-group-sm input {
            font-size: 0.72rem !important;
        }
        .page-materi-detail .input-group-sm button {
            font-size: 0.72rem !important;
        }
        
        /* Refleksi Diri & Tuntaskan Modul */
        .page-materi-detail #formRefleksiFinal textarea {
            font-size: 0.74rem !important;
            padding: 6px 8px !important;
        }
        .page-materi-detail #btnSubmitRefleksi {
            font-size: 0.76rem !important;
            padding: 8px 12px !important;
            width: 100% !important;
            line-height: 1.3 !important;
            border-radius: 20px !important;
            white-space: normal !important;
        }
        
        .page-materi-detail .alert {
            padding: 5px 6px !important;
            font-size: 0.72rem !important;
            border-radius: 5px !important;
        }
    }
</style>

<section class="content page-materi-detail">
    <div class="container-fluid py-0 py-md-4">
        <div class="modul-container">

            <!-- BANNER IDENTITAS MATERI -->
            <div class="modul-banner">
                <div class="modul-inner">
                    <?php 
                        $fase = ($materi['tingkat'] == 'X') ? 'E' : 'F';
                        $ta_aktif = $_SESSION['nama_ta'] ?? '2025/2026';
                        $smt_materi = ($materi['semester'] == '2') ? 'Genap' : 'Ganjil';
                        
                        if ($tugas_terkait) {
                            $deadline = new DateTime($tugas_terkait['deadline']);
                            $now = new DateTime();
                            $interval = $now->diff($deadline);
                            $is_past = $now > $deadline;
                            $badge_class = 'bg-success';
                            if ($is_past) {
                                $badge_class = 'bg-danger';
                                $time_text = 'Sudah Lewat';
                            } else {
                                if ($interval->days < 1) { $badge_class = 'bg-danger text-white'; $time_text = 'Sisa ' . ($interval->h ?: '0') . ' jam lagi'; }
                                elseif ($interval->days < 3) { $badge_class = 'bg-warning text-dark'; $time_text = 'Sisa ' . $interval->days . ' hari lagi'; }
                                else { $badge_class = 'bg-success text-white'; $time_text = 'Sisa ' . $interval->days . ' hari lagi'; }
                            }
                            ?>
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                                <span class="badge <?= $badge_class ?> px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.8rem;">
                                    <i class="fas fa-clock mr-1"></i> <?= date('d M Y H:i', strtotime($tugas_terkait['deadline'])) ?> 
                                    <span class="ml-2 border-left pl-2" style="border-color: rgba(255,255,255,0.3) !important;"><?= $time_text ?></span>
                                </span>
                                <a href="<?= BASE_URL ?>lms/tugas_detail?id=<?= $tugas_terkait['id_tugas'] ?>" class="btn btn-xs btn-outline-light rounded-pill px-3" style="font-size: 0.75rem; background: rgba(255,255,255,0.15);">
                                    Tugas <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </div>
                            <?php
                        }
                    ?>

                    <div class="identity-card shadow-sm">
                        <div class="identity-grid">
                            <div class="identity-item">
                                <span class="id-label">Mata Pelajaran</span>
                                <span class="id-value"><i class="fas fa-bookmark text-warning mr-1"></i> <?= htmlspecialchars($materi['nama_mapel']) ?></span>
                            </div>
                            <div class="identity-item">
                                <span class="id-label">Kelas / Fase</span>
                                <span class="id-value"><i class="fas fa-graduation-cap mr-1"></i> Kelas <?= htmlspecialchars($materi['tingkat']) ?> (Fase <?= $fase ?>)</span>
                            </div>
                            <div class="identity-item">
                                <span class="id-label">Guru Pengampu</span>
                                <span class="id-value"><i class="fas fa-user-tie mr-1"></i> <?= htmlspecialchars($materi['nama_guru'] ?? 'Administrator') ?></span>
                            </div>
                            <div class="identity-item">
                                <span class="id-label">Tahun Ajaran / Semester</span>
                                <span class="id-value"><i class="fas fa-calendar-alt mr-1"></i> <?= $ta_aktif . ' ' . $smt_materi ?></span>
                            </div>
                        </div>

                        <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.18);">
                            <span class="id-label">Topik Pembelajaran:</span>
                            <h2 class="modul-title text-warning mt-1"><?= htmlspecialchars($materi['judul_materi']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HEADER STATUS PROGRES TITIAN TANGGA -->
            <div class="p-3 px-md-4 bg-light border-bottom">
                <div class="modul-inner d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                    <div>
                        <span class="badge <?= $is_penugasan ? 'badge-primary' : 'badge-info' ?> px-3 py-1 rounded-pill text-uppercase font-weight-bold" style="font-size: 0.75rem;">
                            <i class="fas <?= $is_penugasan ? 'fa-tasks' : 'fa-book-reader' ?> mr-1"></i>
                            <?= $is_penugasan ? 'Mode Penugasan Resmi' : 'Mode Pembelajaran Mandiri' ?>
                        </span>
                        <span class="text-muted small ml-2 font-weight-bold d-none d-md-inline">
                            Titian Tangga Pembelajaran (Buka &amp; Pelajari Baris demi Baris)
                        </span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="font-weight-bold text-dark small mr-2" id="lpPercentText"><?= $materi_progress['percent'] ?>% Tuntas</span>
                        <div class="progress" style="width: 130px; height: 8px; border-radius: 10px; background: #e2e8f0;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="lpProgressBar" style="width: <?= $materi_progress['percent'] ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- 🪜 DAFTAR 6 BARIS TITIAN TANGGA (COLLAPSIBLE ROWS)           -->
            <!-- ============================================================ -->
            <div class="p-1 p-md-4">
                <div class="modul-inner">

                    <!-- ======================================================== -->
                    <!-- BARIS 1: ORIENTASI, CP, TP, & PETUNJUK                  -->
                    <!-- ======================================================== -->
                    <div class="lp-row-card <?= !empty($materi_progress['stage_1_orientasi']) ? 'completed' : 'active' ?> expanded" id="row_card_1">
                        <div class="lp-row-header" onclick="toggleRow(1)">
                            <div class="d-flex align-items-center">
                                <div class="lp-badge-circle" id="circle_1">
                                    <?= !empty($materi_progress['stage_1_orientasi']) ? '<i class="fas fa-check"></i>' : '1' ?>
                                </div>
                                <div>
                                    <h5 class="lp-row-title">Path 1 : Orientasi &amp; Panduan Belajar</h5>
                                    <div class="lp-row-subtitle">Target Capaian Pembelajaran (CP), Tujuan (TP), dan petunjuk alur belajar</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down lp-chevron-icon"></i>
                            </div>
                        </div>

                        <div class="lp-row-body collapse show" id="collapse_row_1">
                            <?php if ($cp_data || $materi['cp_manual'] || !empty($tp_data) || $materi['tp_manual']): ?>
                                <div class="row mb-3">
                                    <div class="col-lg-6 mb-3">
                                        <div class="p-3 bg-light rounded border border-primary h-100" style="border-left-width: 4px !important;">
                                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-award text-primary mr-1"></i> Capaian Pembelajaran (CP)</h6>
                                            <p class="text-muted mb-0 cp-description-text" style="line-height: 1.5;">
                                                <?= nl2br(htmlspecialchars($cp_data['deskripsi_cp'] ?? $materi['cp_manual'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="p-3 bg-light rounded border border-success h-100" style="border-left-width: 4px !important;">
                                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-bullseye text-success mr-1"></i> Tujuan Pembelajaran (TP)</h6>
                                            <?php if (!empty($tp_data)): ?>
                                                <?php foreach ($tp_data as $tp): ?>
                                                    <div class="d-flex gap-2 mb-2 align-items-start">
                                                        <i class="fas fa-check-circle text-success mt-1 mr-2 flex-shrink-0"></i>
                                                        <span class="small text-muted"><strong><?= $tp['kode_tp'] ?></strong>: <?= htmlspecialchars($tp['deskripsi_tp']) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <?php if ($materi['tp_manual']): ?>
                                                <p class="small text-muted mb-0"><?= nl2br(htmlspecialchars($materi['tp_manual'])) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($materi['instruksi']): ?>
                                <div class="p-3 bg-light rounded border border-warning mb-3">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-directions text-warning mr-1"></i> Langkah &amp; Petunjuk Pembelajaran:</h6>
                                    <div class="article-content" style="font-size: 0.94rem;">
                                        <?= $materi['instruksi'] ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="path-step-footer">
                                <span class="text-muted small"><i class="fas fa-info-circle text-primary mr-1"></i> Pahami target pembelajaran sebelum membuka video.</span>
                                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" onclick="selesaikanDanBuka(1, 2)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                    <i class="fas fa-check mr-1"></i> Selesai Tahap 1 &amp; Buka Video <i class="fas fa-arrow-down ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- BARIS 2: EKSPLORASI VIDEO PEMBELAJARAN                   -->
                    <!-- ======================================================== -->
                    <div class="lp-row-card <?= !empty($materi_progress['stage_2_video']) ? 'completed' : ($materi_progress['current_stage'] >= 2 ? 'active' : 'locked') ?>" id="row_card_2">
                        <div class="lp-row-header" onclick="toggleRow(2)">
                            <div class="d-flex align-items-center">
                                <div class="lp-badge-circle" id="circle_2">
                                    <?= !empty($materi_progress['stage_2_video']) ? '<i class="fas fa-check"></i>' : ($materi_progress['current_stage'] >= 2 ? '2' : '<i class="fas fa-lock"></i>') ?>
                                </div>
                                <div>
                                    <h5 class="lp-row-title">Path 2 : Eksplorasi Video Pembelajaran</h5>
                                    <div class="lp-row-subtitle">Video animasi, demonstrasi, dan penjelasan konsep visual</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down lp-chevron-icon"></i>
                            </div>
                        </div>

                        <div class="lp-row-body collapse" id="collapse_row_2">
                            <?php if ($materi['video_url']): ?>
                                <?php 
                                $video_id = '';
                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $materi['video_url'], $match)) {
                                    $video_id = $match[1];
                                }
                                ?>
                                <?php if ($video_id): ?>
                                    <div class="video-wrapper shadow">
                                        <iframe src="https://www.youtube.com/embed/<?= $video_id ?>" allowfullscreen></iframe>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light border mb-4">
                                        <i class="fab fa-youtube text-danger mr-2"></i> <a href="<?= $materi['video_url'] ?>" target="_blank" class="font-weight-bold">Tonton Video Pembelajaran (Tautan Eksternal)</a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-light border p-4 text-center rounded-lg text-muted">
                                    <i class="fas fa-video-slash fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-0 small">Materi ini tidak memiliki lampiran video. Anda dapat langsung membuka bahan bacaan teks.</p>
                                </div>
                            <?php endif; ?>

                            <div class="path-step-footer">
                                <span class="text-muted small"><i class="fas fa-lightbulb text-warning mr-1"></i> Tonton video sampai tuntas untuk memahami gambaran praktis konsep.</span>
                                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" onclick="selesaikanDanBuka(2, 3)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                    <i class="fas fa-check mr-1"></i> Selesai Nonton &amp; Buka Bahan Bacaan <i class="fas fa-arrow-down ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- BARIS 3: LITERASI TEKS, RUMUS & DOKUMEN DIGITAL          -->
                    <!-- ======================================================== -->
                    <div class="lp-row-card <?= !empty($materi_progress['stage_3_materi']) ? 'completed' : ($materi_progress['current_stage'] >= 3 ? 'active' : 'locked') ?>" id="row_card_3">
                        <div class="lp-row-header" onclick="toggleRow(3)">
                            <div class="d-flex align-items-center">
                                <div class="lp-badge-circle" id="circle_3">
                                    <?= !empty($materi_progress['stage_3_materi']) ? '<i class="fas fa-check"></i>' : ($materi_progress['current_stage'] >= 3 ? '3' : '<i class="fas fa-lock"></i>') ?>
                                </div>
                                <div>
                                    <h5 class="lp-row-title">Path 3 : Literasi Teks &amp; Dokumen Digital</h5>
                                    <div class="lp-row-subtitle">Uraian konsep mendalam, rumus LaTeX, tabel terstruktur, dan berkas lampiran</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down lp-chevron-icon"></i>
                            </div>
                        </div>

                        <div class="lp-row-body collapse" id="collapse_row_3">
                            <!-- Artikel / Teks Materi -->
                            <div class="article-content p-3 bg-light rounded border mb-4">
                                <?= $materi['deskripsi'] ?>
                            </div>

                            <?php if ($materi['file_path']): ?>
                                <?php 
                                $ext = strtolower(pathinfo($materi['file_path'], PATHINFO_EXTENSION));
                                $file_url = BASE_URL . 'uploads/lms_materi/' . $materi['file_path'];
                                $is_pdf = ($ext == 'pdf');
                                $is_doc = in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
                                ?>

                                <?php if ($is_pdf): ?>
                                    <div class="embed-responsive embed-responsive-16by9 mb-4 shadow-sm rounded-lg overflow-hidden" style="height: 65vh;">
                                        <iframe src="<?= $file_url ?>" class="embed-responsive-item" allowfullscreen></iframe>
                                    </div>
                                <?php elseif ($is_doc): ?>
                                    <div class="embed-responsive embed-responsive-16by9 mb-4 shadow-sm rounded-lg overflow-hidden" style="height: 65vh;">
                                        <iframe src="https://docs.google.com/viewer?url=<?= urlencode(BASE_URL . 'uploads/lms_materi/' . $materi['file_path']) ?>&embedded=true" class="embed-responsive-item" allowfullscreen></iframe>
                                    </div>
                                <?php endif; ?>

                                <div class="file-attachment p-3 bg-white rounded border d-flex align-items-center justify-content-between shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon mr-3 text-primary"><i class="fas fa-file-pdf fa-2x"></i></div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold text-dark"><?= basename($materi['file_path']) ?></h6>
                                            <span class="small text-muted text-uppercase"><?= $ext ?> File • Dokumen Pelengkap</span>
                                        </div>
                                    </div>
                                    <a href="<?= $file_url ?>" target="_blank" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm">
                                        <i class="fas fa-download mr-1"></i> Download Berkas
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="path-step-footer">
                                <?php if ($is_penugasan): ?>
                                    <span class="text-muted small"><i class="fas fa-check-double text-success mr-1"></i> Selesai membaca dan memahami materi? Lanjut ke tes formatif.</span>
                                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" onclick="selesaikanDanBuka(3, 4)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                        <i class="fas fa-check mr-1"></i> Selesai Membaca &amp; Buka Tes Formatif <i class="fas fa-arrow-down ml-1"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small"><i class="fas fa-info-circle text-primary mr-1"></i> Anda telah menyelesaikan materi belajar mandiri (Path 1 s.d. 3).</span>
                                    <button type="button" class="btn btn-success rounded-pill px-4 py-2 font-weight-bold shadow-sm" onclick="selesaiMandiri()" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                        <i class="fas fa-check-circle mr-1"></i> Modul Mandiri Selesai Dipelajari
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- BARIS 4: ASESMEN FORMATIF (KUIS EVALUASI)                -->
                    <!-- ======================================================== -->
                    <div class="lp-row-card <?= ($is_penugasan && !empty($materi_progress['stage_4_formatif'])) ? 'completed' : (($is_penugasan && $materi_progress['current_stage'] >= 4) ? 'active' : 'locked') ?>" id="row_card_4">
                        <div class="lp-row-header" onclick="toggleRow(4)">
                            <div class="d-flex align-items-center">
                                <div class="lp-badge-circle" id="circle_4">
                                    <?= ($is_penugasan && !empty($materi_progress['stage_4_formatif'])) ? '<i class="fas fa-check"></i>' : (($is_penugasan && $materi_progress['current_stage'] >= 4) ? '4' : '<i class="fas fa-lock"></i>') ?>
                                </div>
                                <div>
                                    <h5 class="lp-row-title">Path 4 : Asesmen Formatif (Uji Pemahaman)</h5>
                                    <div class="lp-row-subtitle">Latihan soal pemahaman konsep dan uji ketercapaian Tujuan Pembelajaran (TP)</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down lp-chevron-icon"></i>
                            </div>
                        </div>

                        <div class="lp-row-body collapse" id="collapse_row_4">
                            <?php if (!$is_penugasan): ?>
                                <div class="lp-inline-lock d-flex flex-column align-items-center text-center py-5 px-3">
                                    <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 0 0 8px rgba(251,191,36,0.12);">
                                        <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                    </div>
                                    <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:1rem;">
                                        Path 4 : Asesmen Formatif — Akses Dikunci
                                    </h6>
                                    <p class="text-muted mb-3" style="font-size:0.85rem;max-width:420px;line-height:1.55;">
                                        Tahap ini hanya terbuka apabila guru telah memberikan <strong>penugasan resmi</strong> untuk materi ini. Pada <strong>Mode Belajar Mandiri</strong>, Anda berfokus menyelesaikan Path 1 s.d. 3.
                                    </p>
                                    <a href="<?= BASE_URL ?>siswa_portal/tugas" class="btn btn-sm btn-outline-warning rounded-pill px-4 font-weight-bold" style="font-size:0.82rem;">
                                        <i class="fas fa-tasks mr-1"></i> Buka Halaman Penugasan
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php if (!empty($soal_list)): ?>
                                    <?php if ($has_submitted): ?>
                                        <div class="alert alert-success p-4 rounded-lg border-0 shadow-sm text-center mb-3">
                                            <i class="fas fa-check-circle fa-3x mb-2 text-success"></i>
                                            <h5 class="font-weight-bold">Asesmen Formatif Telah Dikerjakan</h5>
                                            <p class="mb-0 text-muted small">Jawaban Anda tersimpan rapi dan terhubung ke buku nilai guru.</p>
                                        </div>
                                    <?php else: ?>
                                        <form action="<?= BASE_URL ?>lms/materi_quiz_submit" method="POST" id="main-quiz-form">
                                            <input type="hidden" name="id_materi" value="<?= $materi['id_materi'] ?>">
                                            <input type="hidden" name="id_tugas" value="<?= $id_tugas ?>">
                                                
                                                <?php 
                                                // Kelompokkan soal: Diagnostik dan Formatif / Latihan
                                                $groups = [];
                                                $diagnostik = array_values(array_filter($soal_list, function($s) {
                                                    return strcasecmp($s['kategori_soal'] ?? '', 'Diagnostik') === 0;
                                                }));
                                                $formatif = array_values(array_filter($soal_list, function($s) {
                                                    return strcasecmp($s['kategori_soal'] ?? '', 'Diagnostik') !== 0;
                                                }));

                                                if (!empty($diagnostik)) {
                                                    $groups['Tes Diagnostik Awal'] = $diagnostik;
                                                }
                                                if (!empty($formatif)) {
                                                    $groups['Latihan Soal Pemahaman (Formatif)'] = $formatif;
                                                }
                                                if (empty($groups)) {
                                                    $groups['Daftar Soal Asesmen'] = $soal_list;
                                                }

                                                $global_soal_num = 1;
                                                foreach ($groups as $group_label => $grouped_soal):
                                                ?>
                                                    <div class="mb-4">
                                                        <h6 class="font-weight-bold text-dark mb-3 border-left pl-3 border-primary" style="border-width: 4px !important;"><?= $group_label ?></h6>
                                                        <?php foreach ($grouped_soal as $s): ?>
                                                            <div class="quiz-card shadow-sm mb-3">
                                                                <div class="d-flex align-items-start mb-3">
                                                                    <span class="badge badge-primary mr-2 px-2 py-1" style="font-size: 0.85rem; border-radius: 6px;"><?= $global_soal_num++ ?></span>
                                                                    <div class="font-weight-bold text-dark mb-0" style="font-size: 0.98rem; line-height: 1.6;"><?= nl2br(htmlspecialchars($s['pertanyaan'])) ?></div>
                                                                </div>

                                                                <?php if (!empty($s['file_pertanyaan'])): ?>
                                                                    <div class="mb-3">
                                                                        <?php if (strpos($s['file_pertanyaan'], '.mp3') !== false || strpos($s['file_pertanyaan'], '.wav') !== false): ?>
                                                                            <audio controls class="w-100 shadow-sm rounded-pill"><source src="<?= BASE_URL . 'uploads/lms_soal/' . $s['file_pertanyaan'] ?>" type="audio/mpeg"></audio>
                                                                        <?php else: ?>
                                                                            <img src="<?= BASE_URL . 'uploads/lms_soal/' . $s['file_pertanyaan'] ?>" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if ($s['tipe'] == 'PG'): ?>
                                                                    <div class="row mt-2">
                                                                        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
                                                                            <?php if (!empty($s['opsi_'.$opt])): ?>
                                                                                <div class="col-md-6 mb-2">
                                                                                    <label class="option-card" for="q_<?= $s['id_soal'] ?>_<?= $opt ?>">
                                                                                        <input type="radio" name="jawaban[<?= $s['id_soal'] ?>]" value="<?= strtoupper($opt) ?>" id="q_<?= $s['id_soal'] ?>_<?= $opt ?>" required>
                                                                                        <span class="option-letter-badge"><?= strtoupper($opt) ?></span>
                                                                                        <div class="option-content" style="flex:1; font-size: 0.92rem;">
                                                                                            <?= htmlspecialchars($s['opsi_'.$opt]) ?>
                                                                                            <?php if (!empty($s['file_'.$opt])): ?>
                                                                                                <img src="<?= BASE_URL . 'uploads/lms_soal/' . $s['file_'.$opt] ?>" class="mt-2 img-fluid" style="max-height: 100px;">
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    </label>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <textarea name="jawaban[<?= $s['id_soal'] ?>]" class="form-control" rows="3" placeholder="Tuliskan jawaban Anda di sini..." required></textarea>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endforeach; ?>

                                                <div class="mt-3 text-center">
                                                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 shadow font-weight-bold" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                                        <i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban Kuis Formatif
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="alert alert-light border p-4 text-center rounded-lg text-muted">
                                            <i class="fas fa-file-signature fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0 small">Belum ada butir soal tes formatif pada modul ini. Anda dapat langsung membuka ruang diskusi kelas.</p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="path-step-footer">
                                        <span class="text-muted small"><i class="fas fa-comments text-primary mr-1"></i> Ingin berdiskusi atau bertanya ke guru? Lanjut ke ruang diskusi.</span>
                                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" onclick="selesaikanDanBuka(4, 5)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                            <i class="fas fa-check mr-1"></i> Buka Ruang Diskusi Kelas <i class="fas fa-arrow-down ml-1"></i>
                                        </button>
                                    </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- BARIS 5: RUANG DISKUSI & TANYA JAWAB                    -->
                    <!-- ======================================================== -->
                    <div class="lp-row-card <?= ($is_penugasan && !empty($materi_progress['stage_5_diskusi'])) ? 'completed' : (($is_penugasan && $materi_progress['current_stage'] >= 5) ? 'active' : 'locked') ?>" id="row_card_5">
                        <div class="lp-row-header" onclick="toggleRow(5)">
                            <div class="d-flex align-items-center">
                                <div class="lp-badge-circle" id="circle_5">
                                    <?= ($is_penugasan && !empty($materi_progress['stage_5_diskusi'])) ? '<i class="fas fa-check"></i>' : (($is_penugasan && $materi_progress['current_stage'] >= 5) ? '5' : '<i class="fas fa-lock"></i>') ?>
                                </div>
                                <div>
                                    <h5 class="lp-row-title">Path 5 : Ruang Diskusi &amp; Tanya Jawab</h5>
                                    <div class="lp-row-subtitle">Forum kolaborasi interaktif antar-siswa dan verifikasi langsung guru</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down lp-chevron-icon"></i>
                            </div>
                        </div>

                        <div class="lp-row-body collapse" id="collapse_row_5">
                            <?php if (!$is_penugasan): ?>
                                <div class="lp-inline-lock d-flex flex-column align-items-center text-center py-5 px-3">
                                    <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 0 0 8px rgba(251,191,36,0.12);">
                                        <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                    </div>
                                    <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:1rem;">
                                        Path 5 : Ruang Diskusi — Akses Dikunci
                                    </h6>
                                    <p class="text-muted mb-3" style="font-size:0.85rem;max-width:420px;line-height:1.55;">
                                        Ruang diskusi terstruktur hanya aktif saat ada <strong>penugasan resmi</strong> dari guru untuk materi ini.
                                    </p>
                                    <a href="<?= BASE_URL ?>siswa_portal/tugas" class="btn btn-sm btn-outline-warning rounded-pill px-4 font-weight-bold" style="font-size:0.82rem;">
                                        <i class="fas fa-tasks mr-1"></i> Buka Halaman Penugasan
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- FORM POST DISKUSI -->
                                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
                                    <div class="card-body p-3 p-md-4">
                                        <form id="formPostDiskusiUtama" onsubmit="kirimDiskusi(event, null)">
                                            <div class="form-group mb-3">
                                                <label class="small font-weight-bold text-dark text-uppercase"><i class="fas fa-edit mr-1 text-primary"></i> Tulis pertanyaan atau bagikan pemahaman Anda:</label>
                                                <textarea id="pesanDiskusiUtama" class="form-control" rows="3" placeholder="Tuliskan pesan atau pertanyaan terkait materi ini..." required style="border-radius: 8px;"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" id="btnKirimDiskusiUtama" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                    <i class="fas fa-paper-plane mr-1"></i> Kirim Diskusi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- DAFTAR DISKUSI -->
                                <div id="containerDaftarDiskusi">
                                    <?php if (!empty($diskusi_list)): ?>
                                        <?php foreach ($diskusi_list as $d): ?>
                                            <div class="card border shadow-sm mb-3" style="border-radius: 12px;" id="thread_<?= $d['id_diskusi'] ?>">
                                                <div class="card-body p-3 p-md-4">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold text-white shadow-sm" style="width: 38px; height: 38px; background: <?= (($d['peran'] ?? '') === 'Guru' || ($d['peran'] ?? '') === 'Admin') ? 'linear-gradient(135deg, #4f46e5, #3730a3)' : 'linear-gradient(135deg, #059669, #10b981)' ?>; font-size: 0.95rem;">
                                                                <?= strtoupper(substr($d['display_name'], 0, 1)) ?>
                                                            </div>
                                                            <div>
                                                                <h6 class="font-weight-bold mb-0 text-dark" style="font-size: 0.95rem;">
                                                                    <?= htmlspecialchars($d['display_name']) ?>
                                                                    <?php if (($d['peran'] ?? '') === 'Guru' || ($d['peran'] ?? '') === 'Admin'): ?>
                                                                        <span class="badge badge-primary px-2 py-1 ml-1" style="font-size: 0.65rem;">Guru</span>
                                                                    <?php else: ?>
                                                                        <span class="badge badge-light border text-muted px-2 py-1 ml-1" style="font-size: 0.65rem;">Siswa</span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($d['is_guru_verified'])): ?>
                                                                        <span class="badge badge-warning px-2 py-1 ml-1 text-dark font-weight-bold" style="font-size: 0.65rem;"><i class="fas fa-check-double mr-1"></i>Terverifikasi Guru</span>
                                                                    <?php endif; ?>
                                                                </h6>
                                                                <small class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($d['created_at'])) ?></small>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <?php if (in_array('Guru', $user_roles) || in_array('Admin', $user_roles)): ?>
                                                                <button type="button" class="btn btn-xs <?= !empty($d['is_guru_verified']) ? 'btn-warning' : 'btn-outline-warning' ?> mr-1" onclick="toggleVerifikasi(<?= $d['id_diskusi'] ?>)">
                                                                    <i class="fas fa-check"></i> <?= !empty($d['is_guru_verified']) ? 'Terverifikasi' : 'Verifikasi' ?>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if ($d['id_user'] == ($_SESSION['user_id'] ?? 0) || in_array('Admin', $user_roles) || in_array('Guru', $user_roles)): ?>
                                                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="hapusDiskusi(<?= $d['id_diskusi'] ?>)">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <p class="text-dark mt-2 mb-2" style="font-size: 0.92rem; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($d['pesan']) ?></p>

                                                    <button class="btn btn-sm btn-link text-primary p-0 font-weight-bold" type="button" onclick="$('#reply_box_<?= $d['id_diskusi'] ?>').slideToggle()">
                                                        <i class="fas fa-reply mr-1"></i> Balas (<?= count($d['replies']) ?>)
                                                    </button>

                                                    <div class="mt-3" id="reply_box_<?= $d['id_diskusi'] ?>" style="display: none;">
                                                        <?php if (!empty($d['replies'])): ?>
                                                            <div class="pl-3 border-left mb-2">
                                                                <?php foreach ($d['replies'] as $rep): ?>
                                                                    <div class="bg-light p-2 rounded mb-2" id="reply_<?= $rep['id_diskusi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                                            <span class="font-weight-bold small text-dark">
                                                                                <?= htmlspecialchars($rep['display_name']) ?>
                                                                                <?php if (($rep['peran'] ?? '') === 'Guru' || ($rep['peran'] ?? '') === 'Admin'): ?>
                                                                                    <span class="badge badge-primary px-1" style="font-size: 0.6rem;">Guru</span>
                                                                                <?php endif; ?>
                                                                            </span>
                                                                            <small class="text-muted" style="font-size: 0.7rem;"><?= date('d M, H:i', strtotime($rep['created_at'])) ?></small>
                                                                        </div>  </div>
                                                                        <p class="small text-dark mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($rep['pesan']) ?></p>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="input-group input-group-sm">
                                                            <input type="text" id="input_reply_<?= $d['id_diskusi'] ?>" class="form-control" placeholder="Tulis balasan...">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-primary font-weight-bold" type="button" onclick="kirimBalasan(<?= $d['id_diskusi'] ?>)">
                                                                    <i class="fas fa-paper-plane mr-1"></i> Balas
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="card border-0 bg-light p-4 text-center text-muted rounded-lg">
                                            <p class="small mb-0"><i class="fas fa-comments mr-1"></i> Belum ada diskusi pada modul ini. Jadilah yang pertama mengajukan tanggapan!</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="path-step-footer">
                                    <span class="text-muted small"><i class="fas fa-award text-success mr-1"></i> Tahap terakhir: Lengkapi refleksi diri untuk menyelesaikan 100% modul.</span>
                                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" onclick="selesaikanDanBuka(5, 6)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                        <i class="fas fa-check mr-1"></i> Buka Refleksi Akhir <i class="fas fa-arrow-down ml-1"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- BARIS 6: REFLEKSI DIRI & TUNTASKAN MODUL (100%)          -->
                    <!-- ======================================================== -->
                    <div class="lp-row-card <?= ($is_penugasan && !empty($materi_progress['stage_6_refleksi'])) ? 'completed' : (($is_penugasan && $materi_progress['current_stage'] >= 6) ? 'active' : 'locked') ?>" id="row_card_6">
                        <div class="lp-row-header" onclick="toggleRow(6)">
                            <div class="d-flex align-items-center">
                                <div class="lp-badge-circle" id="circle_6">
                                    <?= ($is_penugasan && !empty($materi_progress['stage_6_refleksi'])) ? '<i class="fas fa-check"></i>' : (($is_penugasan && $materi_progress['current_stage'] >= 6) ? '6' : '<i class="fas fa-lock"></i>') ?>
                                </div>
                                <div>
                                    <h5 class="lp-row-title">Path 6 : Refleksi Diri &amp; Tuntaskan Modul</h5>
                                    <div class="lp-row-subtitle">Umpan balik pemahaman belajar siswa dan penguncian status tuntas modul</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chevron-down lp-chevron-icon"></i>
                            </div>
                        </div>

                        <div class="lp-row-body collapse" id="collapse_row_6">
                            <?php if (!$is_penugasan): ?>
                                <div class="lp-inline-lock d-flex flex-column align-items-center text-center py-5 px-3">
                                    <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 0 0 8px rgba(251,191,36,0.12);">
                                        <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                    </div>
                                    <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:1rem;">
                                        Path 6 : Refleksi Diri — Akses Dikunci
                                    </h6>
                                    <p class="text-muted mb-3" style="font-size:0.85rem;max-width:420px;line-height:1.55;">
                                        Pengisian refleksi dan penuntasan 100% modul ditujukan untuk <strong>Mode Penugasan</strong>.
                                    </p>
                                    <a href="<?= BASE_URL ?>siswa_portal/tugas" class="btn btn-sm btn-outline-warning rounded-pill px-4 font-weight-bold" style="font-size:0.82rem;">
                                        <i class="fas fa-tasks mr-1"></i> Buka Halaman Penugasan
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php 
                                $ref_questions = json_decode($materi['refleksi_config'] ?? '[]', true);
                                if (empty($ref_questions)) {
                                    $ref_questions = [
                                        'Bagian konsep mana dari materi ini yang paling Anda pahami dengan baik?',
                                        'Hal apa yang masih menjadi tantangan dan ingin Anda pelajari lebih dalam?',
                                        'Bagaimana Anda akan menerapkan ilmu materi ini dalam kehidupan sehari-hari?'
                                    ];
                                }
                                ?>
                                <div class="p-3 bg-light rounded border border-warning mb-4">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-user-check text-success mr-2"></i> Refleksi &amp; Umpan Balik Siswa</h6>
                                    <p class="text-muted small mb-3">Tuliskan refleksi belajar Anda dan ambil foto presensi belajar untuk mengunci portofolio pembelajaran tuntas 100%.</p>
                                    
                                    <form id="formRefleksiFinal" onsubmit="submitRefleksiFinal(event)">
                                        <?php foreach ($ref_questions as $r_idx => $q_text): ?>
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark small"><?= ($r_idx + 1) . '. ' . htmlspecialchars($q_text) ?></label>
                                                <textarea name="refleksi_siswa[<?= $r_idx ?>]" class="form-control bg-white border shadow-sm" rows="2" required placeholder="Tulis refleksi Anda di sini..."></textarea>
                                                <input type="hidden" name="refleksi_soal[<?= $r_idx ?>]" value="<?= htmlspecialchars($q_text) ?>">
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- Widget Verifikasi Kamera Presensi (Live Cam / Selfie) -->
                                        <div class="card border-0 shadow-sm rounded-lg my-4" style="background: #f8fafc; border: 1.5px dashed #cbd5e1 !important; border-radius: 16px;">
                                            <div class="card-body p-3 p-md-4 text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px; border-radius: 50%; background: #e0f2fe; color: #0284c7; box-shadow: 0 0 0 5px rgba(2,132,199,0.12);">
                                                    <i class="fas fa-camera font-weight-bold" style="font-size: 1.25rem;"></i>
                                                </div>
                                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 1rem;">
                                                    Verifikasi Kehadiran Belajar (Selfie Cam)
                                                </h6>
                                                <p class="text-muted small mb-3" style="max-width: 460px; margin: 0 auto; line-height: 1.45; font-size: 0.82rem;">
                                                    Ambil foto selfie di tempat belajar Anda sebagai bukti kehadiran otentik bahwa Anda telah hadir dan menuntaskan pembelajaran.
                                                </p>

                                                <!-- Camera Viewfinder & Preview Box -->
                                                <div class="position-relative mx-auto mb-3" style="max-width: 320px; aspect-ratio: 4/3; background: #0f172a; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.15);">
                                                    <!-- Live Video Stream -->
                                                    <video id="videoPresensi" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); display: none;"></video>
                                                    
                                                    <!-- Snapshot Preview (Shown after capture) -->
                                                    <img id="previewFotoPresensi" src="" alt="Foto Presensi" style="width: 100%; height: 100%; object-fit: cover; display: none;">

                                                    <!-- Standby Overlay (Before camera starts) -->
                                                    <div id="standbyOverlay" class="position-absolute d-flex flex-column align-items-center justify-content-center text-white" style="top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.85); backdrop-filter: blur(3px);">
                                                        <i class="fas fa-video fa-2x mb-2 text-white-50"></i>
                                                        <span class="small font-weight-bold text-white">Kamera Belum Aktif</span>
                                                        <span class="text-white-50" style="font-size: 0.72rem;">Klik tombol di bawah untuk menyalakan kamera</span>
                                                    </div>

                                                    <!-- Focus Guide Border Overlay -->
                                                    <div id="cameraGuideOverlay" class="position-absolute" style="top: 8%; left: 18%; right: 18%; bottom: 8%; border: 2px dashed rgba(255,255,255,0.45); border-radius: 50%; pointer-events: none; display: none;"></div>
                                                </div>

                                                <!-- Hidden canvas for capturing frame -->
                                                <canvas id="canvasPresensi" style="display: none;"></canvas>
                                                <input type="hidden" name="foto_presensi" id="inputFotoPresensi" value="">

                                                <!-- Camera Control Buttons -->
                                                <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 8px;">
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 font-weight-bold shadow-sm" id="btnStartCamera" onclick="startPresensiCamera()">
                                                        <i class="fas fa-video mr-1"></i> Nyalakan Kamera
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 font-weight-bold shadow-sm" id="btnCapturePhoto" onclick="capturePresensiPhoto()" style="display: none; background: linear-gradient(135deg, #0284c7, #0369a1); border: none;">
                                                        <i class="fas fa-camera mr-1"></i> Ambil Foto Kehadiran
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 font-weight-bold shadow-sm" id="btnRetakePhoto" onclick="retakePresensiPhoto()" style="display: none;">
                                                        <i class="fas fa-redo-alt mr-1 text-warning"></i> Foto Ulang
                                                    </button>
                                                </div>
                                                
                                                <div id="fotoSuccessNotice" class="mt-2 text-success font-weight-bold small" style="display: none; font-size: 0.82rem;">
                                                    <i class="fas fa-check-circle mr-1"></i> Foto kehadiran berhasil diambil &amp; siap dikirim!
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 py-3 shadow font-weight-bold" id="btnSubmitRefleksi" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                                <i class="fas fa-check-circle mr-2"></i> Kirim Refleksi &amp; Tuntaskan Modul Pembelajaran (100%)
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
$(document).ready(function() {
    // Auto-wrap tables
    $('.article-content table').each(function() {
        if (!$(this).parent().hasClass('article-table-wrapper')) {
            $(this).wrap('<div class="article-table-wrapper"></div>');
        }
    });

    // Typeset MathJax
    if (window.renderMath) {
        window.renderMath();
    } else if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
});

const isPenugasan = <?= $is_penugasan ? 'true' : 'false' ?>;

function toggleRow(stepNum) {
    const card = $('#row_card_' + stepNum);
    const body = $('#collapse_row_' + stepNum);

    // Jika card sudah expand → tutup
    if (body.hasClass('show')) {
        body.collapse('hide');
        card.removeClass('expanded');
        return;
    }

    // ── Guard: Path 4-6 hanya boleh dibuka jika ada penugasan ─────────
    if (!isPenugasan && stepNum > 3) {
        const lockNames = {4: 'Asesmen Formatif', 5: 'Ruang Diskusi', 6: 'Refleksi Tuntas'};
        const name = lockNames[stepNum] || 'Path ini';

        // Render pesan inline (idempoten)
        const bodyEl = document.getElementById('collapse_row_' + stepNum);
        if (bodyEl && !bodyEl.querySelector('.lp-inline-lock')) {
            bodyEl.innerHTML = `
                <div class="lp-inline-lock d-flex flex-column align-items-center text-center py-4 px-3">
                    <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 0 0 8px rgba(251,191,36,0.12);">
                        <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                    </div>
                    <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.98rem;">
                        Path ${stepNum}: ${name} — Akses Dikunci
                    </h6>
                    <p class="text-muted mb-3" style="font-size:0.83rem;max-width:360px;line-height:1.55;">
                        Tahap ini hanya terbuka apabila guru telah memberikan <strong>penugasan resmi</strong> pada modul ini.
                        Pada <strong>Mode Belajar Mandiri</strong>, selesaikan <strong>Path 1 s.d. 3</strong> terlebih dahulu.
                    </p>
                    <a href="<?= BASE_URL ?>siswa_portal/tugas"
                       class="btn btn-sm btn-outline-warning rounded-pill px-3 font-weight-bold"
                       style="font-size:0.8rem;">
                        <i class="fas fa-tasks mr-1"></i> Lihat Daftar Penugasan
                    </a>
                </div>
            `;
        }
        body.collapse('show');
        card.addClass('expanded');
        return;
    }

    // ── Buka normal ───────────────────────────────────────────────────
    body.collapse('show');
    card.addClass('expanded');
}

function updateStepperUI(prog) {
    if (!prog) return;
    
    $('#lpPercentText').text(prog.percent + '% Tuntas');
    $('#lpProgressBar').css('width', prog.percent + '%');

    const keys = ['stage_1_orientasi', 'stage_2_video', 'stage_3_materi', 'stage_4_formatif', 'stage_5_diskusi', 'stage_6_refleksi'];
    
    keys.forEach((k, idx) => {
        const stepNum = idx + 1;
        const card = $('#row_card_' + stepNum);
        const circle = $('#circle_' + stepNum);

        card.removeClass('completed active locked');

        if (!isPenugasan && stepNum > 3) {
            card.addClass('locked');
            circle.html('<i class="fas fa-lock"></i>');
            return;
        }

        if (prog[k] == 1) {
            card.addClass('completed');
            circle.html('<i class="fas fa-check"></i>');
        } else if (prog.current_stage >= stepNum) {
            card.addClass('active');
            circle.html(stepNum);
        } else {
            card.addClass('locked');
            circle.html('<i class="fas fa-lock"></i>');
        }
    });
}

function selesaiMandiri() {
    const idMateri = <?= (int)$materi['id_materi'] ?>;
    $.post('<?= BASE_URL ?>index.php?mod=lms&act=lp_mark_stage', {
        id_materi: idMateri,
        id_tugas: 0,
        stage: 3
    }, function(res) {
        if (res.status === 'ok' && res.progress) {
            updateStepperUI(res.progress);
        }
        Swal.fire({
            icon: 'success',
            title: 'Bagus Sekali! 🎓',
            text: 'Anda telah menyelesaikan seluruh materi belajar mandiri (Path 1 s.d. 3) untuk modul ini.',
            confirmButtonColor: '#4f46e5'
        });
    }, 'json');
}

function selesaikanDanBuka(currentStage, nextStage) {
    const idMateri = <?= (int)$materi['id_materi'] ?>;
    const idTugas  = <?= (int)$id_tugas ?>;

    $.post('<?= BASE_URL ?>index.php?mod=lms&act=lp_mark_stage', {
        id_materi: idMateri,
        id_tugas: idTugas,
        stage: currentStage
    }, function(res) {
        if (res.status === 'ok' && res.progress) {
            updateStepperUI(res.progress);
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Path ' + currentStage + ' Selesai! ✅',
            text: 'Membuka Path ' + nextStage + '...',
            showConfirmButton: false,
            timer: 1500
        });

        // Tutup baris sekarang, buka baris berikutnya secara mulus
        $('#collapse_row_' + currentStage).collapse('hide');
        $('#row_card_' + currentStage).removeClass('expanded');

        $('#collapse_row_' + nextStage).collapse('show');
        $('#row_card_' + nextStage).addClass('expanded');

        // Scroll ke baris berikutnya
        setTimeout(() => {
            const nextCard = document.getElementById('row_card_' + nextStage);
            if (nextCard) {
                nextCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 300);
    }, 'json').fail(function() {
        $('#collapse_row_' + currentStage).collapse('hide');
        $('#collapse_row_' + nextStage).collapse('show');
    });
}

let presensiStream = null;

function startPresensiCamera() {
    const video = document.getElementById('videoPresensi');
    const standby = document.getElementById('standbyOverlay');
    const guide = document.getElementById('cameraGuideOverlay');
    const btnStart = document.getElementById('btnStartCamera');
    const btnCapture = document.getElementById('btnCapturePhoto');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        Swal.fire({
            icon: 'warning',
            title: 'Kamera Tidak Didukung',
            text: 'Perangkat atau browser Anda tidak mendukung akses kamera langsung. Anda tetap dapat melanjutkan pengiriman refleksi.'
        });
        return;
    }

    navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
        audio: false
    }).then(function(stream) {
        presensiStream = stream;
        video.srcObject = stream;
        video.style.display = 'block';
        video.play();
        standby.style.display = 'none';
        guide.style.display = 'block';
        btnStart.style.display = 'none';
        btnCapture.style.display = 'inline-block';
    }).catch(function(err) {
        console.error('Camera error:', err);
        Swal.fire({
            icon: 'error',
            title: 'Izin Kamera Ditolak',
            text: 'Mohon izinkan akses kamera di browser Anda untuk verifikasi kehadiran.'
        });
    });
}

function capturePresensiPhoto() {
    const video = document.getElementById('videoPresensi');
    const canvas = document.getElementById('canvasPresensi');
    const preview = document.getElementById('previewFotoPresensi');
    const input = document.getElementById('inputFotoPresensi');
    const guide = document.getElementById('cameraGuideOverlay');
    const btnCapture = document.getElementById('btnCapturePhoto');
    const btnRetake = document.getElementById('btnRetakePhoto');
    const notice = document.getElementById('fotoSuccessNotice');

    if (!video.videoWidth) {
        Swal.fire({ icon: 'warning', text: 'Kamera sedang memuat, silakan tunggu sebentar...' });
        return;
    }

    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    
    // Mirror horizontally to match selfie view
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Add timestamp watermark
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.fillStyle = 'rgba(0, 0, 0, 0.55)';
    ctx.fillRect(0, canvas.height - 34, canvas.width, 34);
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 13px sans-serif';
    const now = new Date();
    const timeStr = 'SIMAKS PRESENSI • ' + now.toLocaleDateString('id-ID') + ' ' + now.toLocaleTimeString('id-ID');
    ctx.fillText(timeStr, 12, canvas.height - 12);

    const base64Data = canvas.toDataURL('image/jpeg', 0.85);
    input.value = base64Data;
    preview.src = base64Data;
    preview.style.display = 'block';
    video.style.display = 'none';
    guide.style.display = 'none';

    btnCapture.style.display = 'none';
    btnRetake.style.display = 'inline-block';
    notice.style.display = 'block';

    // Stop camera stream to save battery
    if (presensiStream) {
        presensiStream.getTracks().forEach(track => track.stop());
        presensiStream = null;
    }
}

function retakePresensiPhoto() {
    const video = document.getElementById('videoPresensi');
    const preview = document.getElementById('previewFotoPresensi');
    const input = document.getElementById('inputFotoPresensi');
    const btnRetake = document.getElementById('btnRetakePhoto');
    const notice = document.getElementById('fotoSuccessNotice');

    preview.style.display = 'none';
    video.style.display = 'block';
    input.value = '';
    notice.style.display = 'none';
    btnRetake.style.display = 'none';

    startPresensiCamera();
}

function submitRefleksiFinal(e) {
    e.preventDefault();
    const foto = $('#inputFotoPresensi').val();

    if (!foto) {
        Swal.fire({
            title: 'Foto Kehadiran Belum Diambil',
            text: 'Disarankan mengambil foto selfie kehadiran di tempat belajar Anda. Apakah ingin ambil foto sekarang atau lanjutkan?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-camera mr-1"></i> Ambil Foto Sekarang',
            cancelButtonText: 'Lanjut Tanpa Foto',
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#64748b'
        }).then((result) => {
            if (result.isConfirmed) {
                const target = document.getElementById('videoPresensi');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                if (!presensiStream) startPresensiCamera();
            } else {
                doExecuteSubmitRefleksi();
            }
        });
        return;
    }

    doExecuteSubmitRefleksi();
}

function doExecuteSubmitRefleksi() {
    const btn = $('#btnSubmitRefleksi');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan Refleksi & Foto Presensi...');

    const idMateri = <?= (int)$materi['id_materi'] ?>;
    const idTugas  = <?= (int)$id_tugas ?>;

    const formData = $('#formRefleksiFinal').serializeArray();
    formData.push({ name: 'id_materi', value: idMateri });
    formData.push({ name: 'id_tugas', value: idTugas });
    formData.push({ name: 'stage', value: 6 });

    $.post('<?= BASE_URL ?>index.php?mod=lms&act=lp_mark_stage', formData, function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i> Kirim Refleksi & Tuntaskan Modul Pembelajaran (100%)');
        if (res.status === 'ok') {
            if (res.progress) updateStepperUI(res.progress);
            Swal.fire({
                icon: 'success',
                title: '🎉 Selamat! Modul Selesai 100%',
                text: 'Seluruh tahapan titian belajar, refleksi, dan bukti kehadiran Anda telah tuntas tersimpan!',
                confirmButtonColor: '#10b981'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Gagal menyimpan progres.' });
        }
    }, 'json').fail(function() {
        btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i> Kirim Refleksi & Tuntaskan Modul Pembelajaran (100%)');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    });
}

function kirimDiskusi(e, parentId) {
    if (e) e.preventDefault();
    const idMateri = <?= (int)$materi['id_materi'] ?>;
    const pesan = $('#pesanDiskusiUtama').val().trim();
    if (!pesan) return;

    const btn = $('#btnKirimDiskusiUtama');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...');

    $.post('<?= BASE_URL ?>index.php?mod=lms&act=diskusi_post', {
        id_materi: idMateri,
        pesan: pesan,
        parent_id: parentId
    }, function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Kirim Diskusi');
        if (res.status === 'ok') {
            $('#pesanDiskusiUtama').val('');
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json').fail(function() {
        btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Kirim Diskusi');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengirim pesan.' });
    });
}

function kirimBalasan(parentId) {
    const idMateri = <?= (int)$materi['id_materi'] ?>;
    const input = $('#input_reply_' + parentId);
    const pesan = input.val().trim();
    if (!pesan) return;

    $.post('<?= BASE_URL ?>index.php?mod=lms&act=diskusi_post', {
        id_materi: idMateri,
        pesan: pesan,
        parent_id: parentId
    }, function(res) {
        if (res.status === 'ok') {
            input.val('');
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json');
}

function toggleVerifikasi(idDiskusi) {
    $.post('<?= BASE_URL ?>index.php?mod=lms&act=diskusi_verify', { id_diskusi: idDiskusi }, function(res) {
        if (res.status === 'ok') {
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json');
}

function hapusDiskusi(idDiskusi) {
    Swal.fire({
        title: 'Hapus pesan diskusi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= BASE_URL ?>index.php?mod=lms&act=diskusi_delete', { id_diskusi: idDiskusi }, function(res) {
                if (res.status === 'ok') {
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            }, 'json');
        }
    });
}
</script>
