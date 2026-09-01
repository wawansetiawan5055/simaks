<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    /* Styling Dasar Kartu LMS Siswa */
    .page-siswa-materi .lms-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff;
    }
    
    /* Filter Bar Styling */
    .page-siswa-materi .custom-filter-select {
        height: 42px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #f8fafc !important;
        padding: 6px 16px !important;
        box-shadow: none !important;
        transition: all 0.2s ease;
    }
    .page-siswa-materi .custom-filter-select:focus {
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }
    .page-siswa-materi .semester-segmented-control {
        display: flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 50px;
        border: 1.5px solid #cbd5e1;
        height: 42px;
        width: 100%;
    }
    .page-siswa-materi .semester-segmented-control .sem-btn {
        flex: 1;
        border: none;
        background: transparent;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0 10px;
        white-space: nowrap;
    }
    .page-siswa-materi .semester-segmented-control .sem-btn.active {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
    }

    /* Bab Card */
    .page-siswa-materi .bab-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 14px;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        overflow: hidden;
        transition: border-color 0.2s ease;
    }
    .page-siswa-materi .bab-card:hover {
        border-color: #cbd5e1;
    }
    .page-siswa-materi .bab-card-header {
        background: #f8fafc;
        padding: 10px 16px;
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .page-siswa-materi .bab-card-header:hover {
        background: #f1f5f9;
    }
    .page-siswa-materi .bab-card.expanded .bab-card-header {
        border-bottom-color: #e2e8f0;
        background: #eef2ff;
    }
    .page-siswa-materi .bab-card.expanded .bab-title {
        color: #4338ca;
    }
    .page-siswa-materi .bab-badge-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #3730a3);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 10px;
    }

    /* Sub-Bab Header */
    .page-siswa-materi .sub-bab-item {
        background: #ffffff;
        border-left: 3.5px solid #6366f1;
        padding: 10px 14px;
        margin-bottom: 10px;
        border-radius: 0 8px 8px 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        border-top: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        width: 100%;
    }

    /* Sleek Modul Item Card */
    .page-siswa-materi .modul-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 10px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }
    .page-siswa-materi .modul-item-card:hover {
        border-color: #4f46e5;
        box-shadow: 0 3px 12px rgba(79, 70, 229, 0.08);
        transform: translateY(-1px);
    }
    .page-siswa-materi .modul-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .page-siswa-materi .modul-title-text {
        font-size: 0.90rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 3px;
        line-height: 1.35;
    }
    .page-siswa-materi .modul-feature-tag {
        font-size: 0.68rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-right: 4px;
        margin-bottom: 2px;
    }
    .page-siswa-materi .tag-video { background: #fee2e2; color: #b91c1c; }
    .page-siswa-materi .tag-text { background: #e0e7ff; color: #4338ca; }
    .page-siswa-materi .tag-quiz { background: #dcfce7; color: #15803d; }
    .page-siswa-materi .tag-doc { background: #fef3c7; color: #b45309; }

    .page-siswa-materi .btn-pelajari {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 6px 14px;
        border-radius: 50px;
        border: none;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);
        white-space: nowrap;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .page-siswa-materi .btn-pelajari:hover {
        color: #ffffff;
        background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
        box-shadow: 0 3px 10px rgba(79, 70, 229, 0.35);
        transform: translateX(2px);
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (ULTRA-COMPACT & SCALED FONT)       */
    /* ============================================================ */
    @media (max-width: 768px) {
        .page-siswa-materi {
            padding: 0 !important;
            margin: 0 !important;
        }
        .page-siswa-materi .container-fluid {
            padding: 4px !important;
        }
        .page-siswa-materi .content-header {
            padding: 6px 4px 2px !important;
        }
        .page-siswa-materi .content-header h1,
        .page-siswa-materi .content-header h4 {
            font-size: 0.88rem !important;
        }
        .page-siswa-materi .lms-card {
            border-radius: 8px !important;
            padding: 6px 8px !important;
            margin-bottom: 8px !important;
        }
        .page-siswa-materi .custom-filter-select,
        .page-siswa-materi .semester-segmented-control {
            height: 32px !important;
            font-size: 0.74rem !important;
        }
        .page-siswa-materi .semester-segmented-control .sem-btn {
            font-size: 0.70rem !important;
            padding: 0 4px;
        }
        .page-siswa-materi .bab-card {
            border-radius: 8px !important;
            margin-bottom: 6px !important;
        }
        .page-siswa-materi .bab-card-header {
            padding: 6px 8px !important;
        }
        .page-siswa-materi .bab-badge-num {
            width: 20px !important;
            height: 20px !important;
            font-size: 0.68rem !important;
            margin-right: 6px !important;
        }
        .page-siswa-materi .bab-title {
            font-size: 0.78rem !important;
        }
        .page-siswa-materi .bab-body > div {
            padding: 3px !important;
        }
        .page-siswa-materi .sub-bab-item {
            padding: 5px 4px !important;
            margin-bottom: 5px !important;
            border-left-width: 3px !important;
            border-radius: 0 6px 6px 0 !important;
        }
        .page-siswa-materi .modul-item-card {
            flex-direction: column;
            align-items: stretch;
            padding: 6px 6px !important;
            gap: 5px !important;
            border-radius: 6px !important;
            margin-bottom: 5px !important;
        }
        .page-siswa-materi .modul-icon-box {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.75rem !important;
            border-radius: 5px !important;
        }
        .page-siswa-materi .modul-title-text {
            font-size: 0.76rem !important;
            line-height: 1.3 !important;
        }
        .page-siswa-materi .modul-feature-tag {
            font-size: 0.58rem !important;
            padding: 1px 4px !important;
        }
        .page-siswa-materi .modul-actions,
        .page-siswa-materi .modul-item-card > div:last-child {
            display: flex !important;
            width: 100% !important;
            gap: 5px !important;
            margin-top: 4px !important;
        }
        .page-siswa-materi .modul-item-card .btn-toggle-lp,
        .page-siswa-materi .modul-item-card button[onclick*="toggleModulLP"] {
            flex: 1 1 50% !important;
            width: 50% !important;
            justify-content: center !important;
            display: inline-flex !important;
            align-items: center !important;
            font-size: 0.70rem !important;
            padding: 5px 4px !important;
            white-space: nowrap !important;
            text-align: center !important;
        }
        .page-siswa-materi .btn-pelajari {
            flex: 1 1 50% !important;
            width: 50% !important;
            justify-content: center !important;
            display: inline-flex !important;
            align-items: center !important;
            font-size: 0.70rem !important;
            padding: 5px 4px !important;
            white-space: nowrap !important;
            text-align: center !important;
        }
        /* Mobile Learning Path Typography & Layout Scaling */
        .page-siswa-materi .lp-rows-wrapper {
            padding: 4px 2px !important;
            border-radius: 6px !important;
            margin-top: 4px !important;
        }
        .page-siswa-materi .lp-main-row-card {
            padding: 6px 6px !important;
            border-radius: 6px !important;
            margin-bottom: 4px !important;
        }
        .page-siswa-materi .lp-main-badge-circle {
            width: 22px !important;
            height: 22px !important;
            font-size: 0.65rem !important;
            margin-right: 6px !important;
        }
        .page-siswa-materi .lp-main-row-title {
            font-size: 0.75rem !important;
            line-height: 1.25 !important;
        }
        .page-siswa-materi .lp-main-row-desc {
            font-size: 0.64rem !important;
            margin-top: 1px !important;
            line-height: 1.2 !important;
        }
        .page-siswa-materi .lp-main-chevron {
            font-size: 0.68rem !important;
        }
        .page-siswa-materi .lp-main-row-card .collapse {
            margin-top: 6px !important;
            padding-top: 6px !important;
        }
        .page-siswa-materi .lp-main-row-card .p-3,
        .page-siswa-materi .lp-main-row-card .p-4 {
            padding: 6px 6px !important;
            margin-bottom: 5px !important;
            border-radius: 5px !important;
        }
        .page-siswa-materi .lp-main-row-card h5,
        .page-siswa-materi .lp-main-row-card h6 {
            font-size: 0.74rem !important;
            margin-bottom: 3px !important;
        }
        .page-siswa-materi .lp-main-row-card p {
            font-size: 0.68rem !important;
            line-height: 1.4 !important;
        }
        .page-siswa-materi .article-content,
        .page-siswa-materi .prose-content,
        .page-siswa-materi .reading-box {
            font-size: 0.70rem !important;
            line-height: 1.4 !important;
            padding: 6px 6px !important;
        }
        .page-siswa-materi .article-content h1,
        .page-siswa-materi .article-content h2,
        .page-siswa-materi .article-content h3 {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            margin-top: 4px !important;
            margin-bottom: 2px !important;
        }
        .page-siswa-materi .article-content h4,
        .page-siswa-materi .article-content h5,
        .page-siswa-materi .article-content h6 {
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            margin-top: 3px !important;
            margin-bottom: 2px !important;
        }
        .page-siswa-materi .article-content p,
        .page-siswa-materi .article-content li,
        .page-siswa-materi .article-content td,
        .page-siswa-materi .article-content th,
        .page-siswa-materi .article-content span {
            font-size: 0.68rem !important;
        }
        .page-siswa-materi .lp-main-row-card .alert {
            padding: 6px 6px !important;
            font-size: 0.68rem !important;
            margin-bottom: 5px !important;
        }
        .page-siswa-materi .lp-main-row-card .btn,
        .page-siswa-materi .btn-sm {
            font-size: 0.68rem !important;
            padding: 4px 10px !important;
        }
        .page-siswa-materi small, .page-siswa-materi .small {
            font-size: 0.64rem !important;
        }
        .page-siswa-materi .badge {
            font-size: 0.60rem !important;
        }
    } /* END @media (max-width: 768px) */

    /* ============================================================ */
    /* 🛣️ LEARNING PATH ROWS — DESKTOP & GLOBAL STYLES             */
    /* Berlaku di PC/laptop (≥769px), tidak diubah di mobile        */
    /* ============================================================ */
    .page-siswa-materi .lp-main-row-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 22px;
        margin-bottom: 10px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    .page-siswa-materi .lp-main-row-card:hover {
        border-color: #a5b4fc;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.10);
        transform: translateY(-1px);
    }
    .page-siswa-materi .lp-main-row-card.completed {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-color: #10b981;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.10);
    }
    /* Badge Circle */
    .page-siswa-materi .lp-main-badge-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .page-siswa-materi .lp-main-badge-circle.completed {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.20);
    }
    .page-siswa-materi .lp-main-badge-circle.locked {
        background: #f8fafc;
        color: #94a3b8;
        border: 1.5px dashed #cbd5e1;
        font-size: 0.88rem;
    }
    .page-siswa-materi .lp-main-badge-circle.unlocked {
        background: #eef2ff;
        color: #4f46e5;
        border: 1.5px solid #a5b4fc;
        font-size: 0.88rem;
    }
    /* Titles & Descriptions */
    .page-siswa-materi .lp-main-row-title {
        font-size: 0.97rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.35;
    }
    .page-siswa-materi .lp-main-row-title.completed {
        color: #065f46;
    }
    .page-siswa-materi .lp-main-row-desc {
        font-size: 0.81rem;
        color: #64748b;
        margin-top: 2px;
        line-height: 1.45;
    }
    .page-siswa-materi .lp-main-chevron {
        font-size: 0.88rem;
        color: #94a3b8;
        transition: transform 0.25s ease;
        flex-shrink: 0;
    }
    /* Wrapper container LP rows */
    .page-siswa-materi .lp-rows-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        margin-top: 10px;
    }
</style>

<div class="content-header pt-3 mb-2 page-siswa-materi">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #3730a3); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Materi Pembelajaran Mandiri
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <a href="<?= BASE_URL ?>siswa_portal/tugas" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold shadow-sm px-3">
                    <i class="fas fa-tasks mr-1"></i> Buka Penugasan Mandiri
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content page-siswa-materi">
    <div class="container-fluid">

        <!-- FILTER TOOLBAR: MAPEL, TINGKAT & SEMESTER -->
        <div class="card lms-card p-3 mb-3 shadow-sm">
            <form method="GET" action="<?= BASE_URL ?>siswa_portal/materi" id="filterFormMateri" class="row align-items-end" style="row-gap: 10px;">
                <input type="hidden" name="semester" id="semesterFilter" value="<?= htmlspecialchars($semester_filter) ?>">

                <!-- MATA PELAJARAN -->
                <div class="col-lg-5 col-md-5 col-12">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                        <i class="fas fa-book text-primary mr-1"></i> Mata Pelajaran
                    </label>
                    <select name="id_mapel" class="form-control custom-filter-select rounded-pill" onchange="$('#filterFormMateri').submit()">
                        <?php foreach ($mapel_list as $mp): ?>
                            <option value="<?= $mp['id_mapel'] ?>" <?= ($id_mapel_filter == $mp['id_mapel']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mp['nama_mapel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- TINGKAT -->
                <div class="col-lg-3 col-md-3 col-6">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                        <i class="fas fa-layer-group text-primary mr-1"></i> Tingkat
                    </label>
                    <select name="tingkat" class="form-control custom-filter-select rounded-pill" onchange="$('#filterFormMateri').submit()">
                        <?php foreach (['X', 'XI', 'XII'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($tingkat_filter == $t) ? 'selected' : '' ?>>Kelas <?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SEMESTER SEGMENTED BUTTONS -->
                <div class="col-lg-4 col-md-4 col-12">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                        <i class="fas fa-calendar-alt text-primary mr-1"></i> Semester
                    </label>
                    <div class="semester-segmented-control">
                        <button type="button" class="sem-btn <?= ($semester_filter == 'Ganjil') ? 'active' : '' ?>" onclick="setSemester('Ganjil')">
                            <i class="fas fa-calendar-alt mr-1"></i> Semester 1 (Ganjil)
                        </button>
                        <button type="button" class="sem-btn <?= ($semester_filter == 'Genap') ? 'active' : '' ?>" onclick="setSemester('Genap')">
                            <i class="fas fa-calendar-check mr-1"></i> Semester 2 (Genap)
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ============================================================ -->
        <!-- 📖 STRUKTUR ALUR BELAJAR MANDIRI (FULL-WIDTH EXPANDABLE ROWS) -->
        <!-- ============================================================ -->
        <?php if (!empty($tree_data['bab_list']) || !empty($tree_data['standalone_materi'])): ?>
            <div class="curriculum-tree-container">
                <?php 
                $no_bab = 1;
                foreach ($tree_data['bab_list'] as $bab): 
                    $total_modul_bab = count($bab['materi_direct']) + array_sum(array_map(fn($s) => count($s['materi_list']), $bab['sub_bab_list']));
                ?>
                    <div class="bab-card expanded mb-3" id="bab_card_<?= $bab['id_bab'] ?>">
                        <!-- BAB HEADER -->
                        <div class="bab-card-header" onclick="toggleBab(<?= $bab['id_bab'] ?>)">
                            <div class="d-flex align-items-center flex-grow-1 mr-2">
                                <div class="bab-badge-num">
                                    <?= $no_bab ?>
                                </div>
                                <div>
                                    <h5 class="bab-title font-weight-bold mb-0" style="font-size: 0.92rem;">
                                        BAB <?= $bab['urutan_bab'] ?>: <?= htmlspecialchars($bab['judul_bab']) ?>
                                    </h5>
                                    <?php if ($bab['deskripsi']): ?>
                                        <p class="small text-muted mb-0 mt-1" style="font-size: 0.75rem;"><?= htmlspecialchars($bab['deskripsi']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light border text-muted mr-2" style="font-size: 0.68rem;">
                                    <?= count($bab['sub_bab_list']) ?> Sub-Bab &bull; <?= $total_modul_bab ?> Modul
                                </span>
                                <i class="fas fa-chevron-up toggle-icon text-muted" id="icon_bab_<?= $bab['id_bab'] ?>"></i>
                            </div>
                        </div>

                        <!-- BAB BODY (SUB-BAB & MATERI) -->
                        <div class="bab-body" id="body_bab_<?= $bab['id_bab'] ?>" style="display: block;">
                            <div class="p-2 p-md-3 bg-light">
                                <?php if (!empty($bab['sub_bab_list'])): ?>
                                    <?php foreach ($bab['sub_bab_list'] as $sub): ?>
                                        <div class="sub-bab-item mb-3">
                                            <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleSubBab(<?= $sub['id_sub_bab'] ?>)">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-bookmark text-primary mr-2" style="font-size: 0.82rem;"></i>
                                                    <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.85rem;">
                                                        Sub-Bab <?= $sub['urutan_sub'] ?? $sub['urutan_sub_bab'] ?? 1 ?>: <?= htmlspecialchars($sub['judul_sub_bab']) ?>
                                                        <span class="badge badge-light border ml-2 text-muted" style="font-size: 0.65rem;"><?= count($sub['materi_list']) ?> Modul</span>
                                                    </h6>
                                                </div>
                                                <div>
                                                    <i class="fas fa-chevron-up text-muted toggle-sub-icon" id="icon_sub_<?= $sub['id_sub_bab'] ?>"></i>
                                                </div>
                                            </div>
                                            <?php if ($sub['deskripsi']): ?>
                                                <p class="small text-muted mt-1 mb-2" style="font-size: 0.74rem;"><?= htmlspecialchars($sub['deskripsi']) ?></p>
                                            <?php endif; ?>

                                            <!-- MATERI DI DALAM SUB-BAB -->
                                            <div id="collapse_sub_<?= $sub['id_sub_bab'] ?>" class="collapse show mt-2">
                                                <?php if (!empty($sub['materi_list'])): ?>
                                                    <div>
                                                        <?php foreach ($sub['materi_list'] as $mat): ?>
                                                            <div class="mb-3">
                                                                <div class="modul-item-card mb-2" onclick="toggleModulLP(<?= $mat['id_materi'] ?>)" style="cursor: pointer;">
                                                                    <div class="d-flex align-items-center flex-grow-1" style="gap: 12px;">
                                                                        <div class="modul-icon-box">
                                                                            <i class="fas <?= $mat['video_url'] ? 'fa-play-circle text-danger' : 'fa-book-reader text-primary' ?>"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="modul-title-text">
                                                                                <?= htmlspecialchars($mat['judul_materi']) ?>
                                                                            </div>
                                                                            <div class="d-flex align-items-center flex-wrap">
                                                                                <span class="modul-feature-tag tag-text"><i class="fas fa-file-alt"></i> Literasi Teks</span>
                                                                                <?php if (!empty($mat['video_url'])): ?>
                                                                                    <span class="modul-feature-tag tag-video"><i class="fab fa-youtube"></i> Video Animasi</span>
                                                                                <?php endif; ?>
                                                                                <?php if (!empty($mat['file_path'])): ?>
                                                                                    <span class="modul-feature-tag tag-doc"><i class="fas fa-paperclip"></i> Dokumen PDF</span>
                                                                                <?php endif; ?>
                                                                                <span class="modul-feature-tag tag-quiz"><i class="fas fa-tasks"></i> Kuis Formatif</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modul-actions d-flex align-items-center" style="gap: 8px;">
                                                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 font-weight-bold text-primary shadow-sm btn-toggle-lp" onclick="event.stopPropagation(); toggleModulLP(<?= $mat['id_materi'] ?>);" style="font-size: 0.78rem;">
                                                                            <i class="fas fa-stream mr-1 text-info"></i> <span id="text_lp_toggle_<?= $mat['id_materi'] ?>">Alur Belajar</span> <i class="fas fa-chevron-down ml-1" id="icon_lp_toggle_<?= $mat['id_materi'] ?>"></i>
                                                                        </button>
                                                                        <a href="<?= BASE_URL ?>siswa_portal/materi_detail?id=<?= $mat['id_materi'] ?>" class="btn-pelajari" onclick="event.stopPropagation();">
                                                                            <span>Pelajari Modul</span> <i class="fas fa-arrow-right"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <!-- 🛣️ LEARNING PATH 6 EXPANDABLE ROWS (COLLAPSE/ELAPSE IN-PLACE, DEFAULT COLLAPSED) -->
                                                                <div class="lp-rows-wrapper collapse mt-2" id="wrapper_lp_<?= $mat['id_materi'] ?>">
                                                                    <?php
                                                                        $v_id = '';
                                                                        if (!empty($mat['video_url']) && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $mat['video_url'], $v_match)) {
                                                                            $v_id = $v_match[1];
                                                                        }
                                                                    ?>

                                                                    <!-- PATH 1: ORIENTASI & PANDUAN BELAJAR -->
                                                                    <div class="lp-main-row-card completed mb-2.5" id="card_lp_1_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 1)" style="cursor: pointer;">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="lp-main-badge-circle completed mr-3" id="badge_lp_1_<?= $mat['id_materi'] ?>">
                                                                                    <i class="fas fa-check"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="lp-main-row-title completed">Path 1 : Orientasi &amp; Panduan Belajar</h6>
                                                                                    <p class="lp-main-row-desc mb-0">Target Capaian Pembelajaran (CP), Tujuan (TP), dan petunjuk alur belajar</p>
                                                                                </div>
                                                                            </div>
                                                                            <div><i class="fas fa-chevron-down lp-main-chevron" style="color: #059669;" id="chevron_lp_1_<?= $mat['id_materi'] ?>"></i></div>
                                                                        </div>
                                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_1_<?= $mat['id_materi'] ?>">
                                                                            <div class="row">
                                                                                <?php if (!empty($mat['cp_manual'])): ?>
                                                                                    <div class="col-md-6 mb-3">
                                                                                        <div class="p-3 bg-light rounded border border-primary h-100" style="border-left-width: 4px !important;">
                                                                                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-award text-primary mr-1"></i> Capaian Pembelajaran (CP)</h6>
                                                                                            <p class="text-muted small mb-0" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($mat['cp_manual'])) ?></p>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                                <?php if (!empty($mat['tp_manual'])): ?>
                                                                                    <div class="col-md-6 mb-3">
                                                                                        <div class="p-3 bg-light rounded border border-success h-100" style="border-left-width: 4px !important;">
                                                                                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-bullseye text-success mr-1"></i> Tujuan Pembelajaran (TP)</h6>
                                                                                            <p class="text-muted small mb-0" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($mat['tp_manual'])) ?></p>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <?php if (!empty($mat['instruksi'])): ?>
                                                                                <div class="p-3 bg-light rounded border border-warning mb-2">
                                                                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-directions text-warning mr-1"></i> Petunjuk Belajar:</h6>
                                                                                    <div class="small text-muted article-content"><?= $mat['instruksi'] ?></div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div class="text-right mt-2">
                                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 2)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                                    Lanjut ke Path 2: Video <i class="fas fa-arrow-down ml-1"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- PATH 2: EKSPLORASI VIDEO PEMBELAJARAN -->
                                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_2_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 2)" style="cursor: pointer;">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_2_<?= $mat['id_materi'] ?>">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="lp-main-row-title">Path 2 : Eksplorasi Video Pembelajaran</h6>
                                                                                    <p class="lp-main-row-desc mb-0">Video animasi, demonstrasi, dan penjelasan konsep visual</p>
                                                                                </div>
                                                                            </div>
                                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_2_<?= $mat['id_materi'] ?>"></i></div>
                                                                        </div>
                                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_2_<?= $mat['id_materi'] ?>">
                                                                            <?php if ($v_id): ?>
                                                                                <div class="embed-responsive embed-responsive-16by9 rounded-lg shadow-sm mb-2" style="border-radius: 12px; overflow: hidden;">
                                                                                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?= $v_id ?>?rel=0" allowfullscreen></iframe>
                                                                                </div>
                                                                            <?php elseif (!empty($mat['video_url'])): ?>
                                                                                <div class="alert alert-light border p-3 rounded text-center">
                                                                                    <i class="fab fa-youtube text-danger fa-2x mb-2"></i><br>
                                                                                    <a href="<?= $mat['video_url'] ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill font-weight-bold">
                                                                                        Buka Video Eksternal <i class="fas fa-external-link-alt ml-1"></i>
                                                                                    </a>
                                                                                </div>
                                                                            <?php else: ?>
                                                                                <div class="alert alert-light border p-3 rounded text-center text-muted small">
                                                                                    <i class="fas fa-video-slash fa-2x mb-2 opacity-50"></i><br>
                                                                                    Tidak ada lampiran video pada materi ini. Silakan lanjutkan ke bahan bacaan teks.
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div class="text-right mt-2">
                                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 3)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                                    Lanjut ke Path 3: Literasi Teks <i class="fas fa-arrow-down ml-1"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- PATH 3: LITERASI TEKS & DOKUMEN DIGITAL -->
                                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_3_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 3)" style="cursor: pointer;">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_3_<?= $mat['id_materi'] ?>">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="lp-main-row-title">Path 3 : Literasi Teks &amp; Dokumen Digital</h6>
                                                                                    <p class="lp-main-row-desc mb-0">Uraian konsep mendalam, rumus LaTeX, tabel terstruktur, dan berkas lampiran</p>
                                                                                </div>
                                                                            </div>
                                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_3_<?= $mat['id_materi'] ?>"></i></div>
                                                                        </div>
                                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_3_<?= $mat['id_materi'] ?>">
                                                                            <?php if (!empty($mat['file_path'])): ?>
                                                                                <div class="p-3 bg-light rounded border mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <i class="fas fa-file-pdf text-danger fa-2x mr-3"></i>
                                                                                        <div>
                                                                                            <span class="font-weight-bold text-dark d-block small">Berkas Modul Ajar (PDF / Dokumen)</span>
                                                                                            <small class="text-muted">Unduh atau baca modul resmi dari guru</small>
                                                                                        </div>
                                                                                    </div>
                                                                                    <a href="<?= BASE_URL ?>uploads/materi/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-weight-bold">
                                                                                        <i class="fas fa-download mr-1"></i> Buka / Unduh Dokumen
                                                                                    </a>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <div class="p-3 bg-white rounded border article-content shadow-sm" style="font-size: 0.92rem; line-height: 1.6; max-height: 500px; overflow-y: auto;">
                                                                                <?= !empty($mat['deskripsi']) ? $mat['deskripsi'] : '<p class="text-muted font-italic mb-0">Tidak ada isi uraian teks mandiri.</p>' ?>
                                                                            </div>
                                                                            <div class="mt-2 text-muted small">
                                                                                <i class="fas fa-check-circle text-success mr-1"></i> Selesai membaca literasi teks &amp; dokumen mandiri.
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- PATH 4: ASESMEN FORMATIF (UJI PEMAHAMAN) -->
                                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_4_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 4)" style="cursor: pointer;">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_4_<?= $mat['id_materi'] ?>">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="lp-main-row-title">Path 4 : Asesmen Formatif (Uji Pemahaman)</h6>
                                                                                    <p class="lp-main-row-desc mb-0">Latihan soal pemahaman konsep dan uji ketercapaian Tujuan Pembelajaran (TP)</p>
                                                                                </div>
                                                                            </div>
                                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_4_<?= $mat['id_materi'] ?>"></i></div>
                                                                        </div>
                                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_4_<?= $mat['id_materi'] ?>">
                                                                            <div class="d-flex flex-column align-items-center text-center py-4 px-3 bg-light rounded">
                                                                                <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 0 8px rgba(251,191,36,0.15);">
                                                                                    <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.95rem;">
                                                                                    Path 4 : Asesmen Formatif — Akses Dikunci
                                                                                </h6>
                                                                                <p class="text-muted mb-0" style="font-size:0.83rem;max-width:380px;line-height:1.55;">
                                                                                    Akses belum dibuka. Menunggu penugasan resmi dari guru untuk materi ini.
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- PATH 5: RUANG DISKUSI & TANYA JAWAB -->
                                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_5_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 5)" style="cursor: pointer;">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_5_<?= $mat['id_materi'] ?>">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="lp-main-row-title">Path 5 : Ruang Diskusi &amp; Tanya Jawab</h6>
                                                                                    <p class="lp-main-row-desc mb-0">Forum kolaborasi interaktif antar-siswa dan verifikasi langsung guru</p>
                                                                                </div>
                                                                            </div>
                                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_5_<?= $mat['id_materi'] ?>"></i></div>
                                                                        </div>
                                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_5_<?= $mat['id_materi'] ?>">
                                                                            <div class="d-flex flex-column align-items-center text-center py-4 px-3 bg-light rounded">
                                                                                <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 0 8px rgba(251,191,36,0.15);">
                                                                                    <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.95rem;">
                                                                                    Path 5 : Ruang Diskusi &amp; Tanya Jawab — Akses Dikunci
                                                                                </h6>
                                                                                <p class="text-muted mb-0" style="font-size:0.83rem;max-width:380px;line-height:1.55;">
                                                                                    Akses belum dibuka. Menunggu penugasan resmi dari guru untuk materi ini.
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- PATH 6: REFLEKSI DIRI & TUNTASKAN MODUL -->
                                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_6_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 6)" style="cursor: pointer;">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_6_<?= $mat['id_materi'] ?>">
                                                                                    <i class="fas fa-lock"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h6 class="lp-main-row-title">Path 6 : Refleksi Diri &amp; Tuntaskan Modul</h6>
                                                                                    <p class="lp-main-row-desc mb-0">Umpan balik pemahaman belajar siswa dan penguncian status tuntas modul</p>
                                                                                </div>
                                                                            </div>
                                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_6_<?= $mat['id_materi'] ?>"></i></div>
                                                                        </div>
                                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_6_<?= $mat['id_materi'] ?>">
                                                                            <div class="d-flex flex-column align-items-center text-center py-4 px-3 bg-light rounded">
                                                                                <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 0 8px rgba(251,191,36,0.15);">
                                                                                    <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.95rem;">
                                                                                    Path 6 : Refleksi Diri &amp; Tuntaskan Modul — Akses Dikunci
                                                                                </h6>
                                                                                <p class="text-muted mb-0" style="font-size:0.83rem;max-width:380px;line-height:1.55;">
                                                                                    Akses belum dibuka. Menunggu penugasan resmi dari guru untuk materi ini.
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="small text-muted font-italic mb-0 p-2" style="font-size: 0.75rem;"><i class="fas fa-info-circle mr-1"></i> Belum ada modul materi di sub-bab ini.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- MATERI LANGSUNG DI BAWAH BAB (TANPA SUB-BAB) -->
                                <?php if (!empty($bab['materi_direct'])): ?>
                                    <div class="mt-2">
                                        <h6 class="small font-weight-bold text-muted text-uppercase mb-2"><i class="fas fa-folder-open mr-1 text-primary"></i> Modul Mandiri:</h6>
                                        <?php foreach ($bab['materi_direct'] as $mat): ?>
                                            <div class="mb-3">
                                                <div class="modul-item-card mb-2" onclick="toggleModulLP(<?= $mat['id_materi'] ?>)" style="cursor: pointer;">
                                                    <div class="d-flex align-items-center flex-grow-1" style="gap: 12px;">
                                                        <div class="modul-icon-box">
                                                            <i class="fas <?= $mat['video_url'] ? 'fa-play-circle text-danger' : 'fa-book-reader text-primary' ?>"></i>
                                                        </div>
                                                        <div>
                                                            <div class="modul-title-text">
                                                                <?= htmlspecialchars($mat['judul_materi']) ?>
                                                            </div>
                                                            <div class="d-flex align-items-center flex-wrap">
                                                                <span class="modul-feature-tag tag-text"><i class="fas fa-file-alt"></i> Literasi Teks</span>
                                                                <?php if (!empty($mat['video_url'])): ?>
                                                                    <span class="modul-feature-tag tag-video"><i class="fab fa-youtube"></i> Video Animasi</span>
                                                                <?php endif; ?>
                                                                <?php if (!empty($mat['file_path'])): ?>
                                                                    <span class="modul-feature-tag tag-doc"><i class="fas fa-paperclip"></i> Dokumen PDF</span>
                                                                <?php endif; ?>
                                                                <span class="modul-feature-tag tag-quiz"><i class="fas fa-tasks"></i> Kuis Formatif</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modul-actions d-flex align-items-center" style="gap: 8px;">
                                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 font-weight-bold text-primary shadow-sm btn-toggle-lp" onclick="event.stopPropagation(); toggleModulLP(<?= $mat['id_materi'] ?>);" style="font-size: 0.78rem;">
                                                            <i class="fas fa-stream mr-1 text-info"></i> <span id="text_lp_toggle_<?= $mat['id_materi'] ?>">Alur Belajar</span> <i class="fas fa-chevron-down ml-1" id="icon_lp_toggle_<?= $mat['id_materi'] ?>"></i>
                                                        </button>
                                                        <a href="<?= BASE_URL ?>siswa_portal/materi_detail?id=<?= $mat['id_materi'] ?>" class="btn-pelajari" onclick="event.stopPropagation();">
                                                            <span>Pelajari Modul</span> <i class="fas fa-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- 🛣️ LEARNING PATH 6 EXPANDABLE ROWS (COLLAPSE/ELAPSE IN-PLACE, DEFAULT COLLAPSED) -->
                                                <div class="lp-rows-wrapper collapse mt-2" id="wrapper_lp_<?= $mat['id_materi'] ?>">
                                                    <?php
                                                        $v_id = '';
                                                        if (!empty($mat['video_url']) && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $mat['video_url'], $v_match)) {
                                                            $v_id = $v_match[1];
                                                        }
                                                    ?>

                                                    <!-- PATH 1: ORIENTASI & PANDUAN BELAJAR -->
                                                    <div class="lp-main-row-card completed mb-2.5" id="card_lp_1_<?= $mat['id_materi'] ?>">
                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 1)" style="cursor: pointer;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lp-main-badge-circle completed mr-3" id="badge_lp_1_<?= $mat['id_materi'] ?>">
                                                                    <i class="fas fa-check"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="lp-main-row-title completed">Path 1 : Orientasi &amp; Panduan Belajar</h6>
                                                                    <p class="lp-main-row-desc mb-0">Target Capaian Pembelajaran (CP), Tujuan (TP), dan petunjuk alur belajar</p>
                                                                </div>
                                                            </div>
                                                            <div><i class="fas fa-chevron-down lp-main-chevron" style="color: #059669;" id="chevron_lp_1_<?= $mat['id_materi'] ?>"></i></div>
                                                        </div>
                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_1_<?= $mat['id_materi'] ?>">
                                                            <div class="row">
                                                                <?php if (!empty($mat['cp_manual'])): ?>
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="p-3 bg-light rounded border border-primary h-100" style="border-left-width: 4px !important;">
                                                                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-award text-primary mr-1"></i> Capaian Pembelajaran (CP)</h6>
                                                                            <p class="text-muted small mb-0" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($mat['cp_manual'])) ?></p>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($mat['tp_manual'])): ?>
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="p-3 bg-light rounded border border-success h-100" style="border-left-width: 4px !important;">
                                                                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-bullseye text-success mr-1"></i> Tujuan Pembelajaran (TP)</h6>
                                                                            <p class="text-muted small mb-0" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($mat['tp_manual'])) ?></p>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if (!empty($mat['instruksi'])): ?>
                                                                <div class="p-3 bg-light rounded border border-warning mb-2">
                                                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-directions text-warning mr-1"></i> Petunjuk Belajar:</h6>
                                                                    <div class="small text-muted article-content"><?= $mat['instruksi'] ?></div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="text-right mt-2">
                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 2)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                    Lanjut ke Path 2: Video <i class="fas fa-arrow-down ml-1"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- PATH 2: EKSPLORASI VIDEO PEMBELAJARAN -->
                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_2_<?= $mat['id_materi'] ?>">
                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 2)" style="cursor: pointer;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_2_<?= $mat['id_materi'] ?>">
                                                                    <i class="fas fa-lock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="lp-main-row-title">Path 2 : Eksplorasi Video Pembelajaran</h6>
                                                                    <p class="lp-main-row-desc mb-0">Video animasi, demonstrasi, dan penjelasan konsep visual</p>
                                                                </div>
                                                            </div>
                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_2_<?= $mat['id_materi'] ?>"></i></div>
                                                        </div>
                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_2_<?= $mat['id_materi'] ?>">
                                                            <?php if ($v_id): ?>
                                                                <div class="embed-responsive embed-responsive-16by9 rounded-lg shadow-sm mb-2" style="border-radius: 12px; overflow: hidden;">
                                                                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?= $v_id ?>?rel=0" allowfullscreen></iframe>
                                                                </div>
                                                            <?php elseif (!empty($mat['video_url'])): ?>
                                                                <div class="alert alert-light border p-3 rounded text-center">
                                                                    <i class="fab fa-youtube text-danger fa-2x mb-2"></i><br>
                                                                    <a href="<?= $mat['video_url'] ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill font-weight-bold">
                                                                        Buka Video Eksternal <i class="fas fa-external-link-alt ml-1"></i>
                                                                    </a>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="alert alert-light border p-3 rounded text-center text-muted small">
                                                                    <i class="fas fa-video-slash fa-2x mb-2 opacity-50"></i><br>
                                                                    Tidak ada lampiran video pada materi ini. Silakan lanjutkan ke bahan bacaan teks.
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="text-right mt-2">
                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 3)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                    Lanjut ke Path 3: Literasi Teks <i class="fas fa-arrow-down ml-1"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- PATH 3: LITERASI TEKS & DOKUMEN DIGITAL -->
                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_3_<?= $mat['id_materi'] ?>">
                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 3)" style="cursor: pointer;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_3_<?= $mat['id_materi'] ?>">
                                                                    <i class="fas fa-lock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="lp-main-row-title">Path 3 : Literasi Teks &amp; Dokumen Digital</h6>
                                                                    <p class="lp-main-row-desc mb-0">Uraian konsep mendalam, rumus LaTeX, tabel terstruktur, dan berkas lampiran</p>
                                                                </div>
                                                            </div>
                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_3_<?= $mat['id_materi'] ?>"></i></div>
                                                        </div>
                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_3_<?= $mat['id_materi'] ?>">
                                                            <?php if (!empty($mat['file_path'])): ?>
                                                                <div class="p-3 bg-light rounded border mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-pdf text-danger fa-2x mr-3"></i>
                                                                        <div>
                                                                            <span class="font-weight-bold text-dark d-block small">Berkas Modul Ajar (PDF / Dokumen)</span>
                                                                            <small class="text-muted">Unduh atau baca modul resmi dari guru</small>
                                                                        </div>
                                                                    </div>
                                                                    <a href="<?= BASE_URL ?>uploads/materi/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-weight-bold">
                                                                        <i class="fas fa-download mr-1"></i> Buka / Unduh Dokumen
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="p-3 bg-white rounded border article-content shadow-sm" style="font-size: 0.92rem; line-height: 1.6; max-height: 500px; overflow-y: auto;">
                                                                <?= !empty($mat['deskripsi']) ? $mat['deskripsi'] : '<p class="text-muted font-italic mb-0">Tidak ada isi uraian teks mandiri.</p>' ?>
                                                            </div>
                                                            <div class="mt-2 text-muted small">
                                                                <i class="fas fa-check-circle text-success mr-1"></i> Selesai membaca literasi teks &amp; dokumen mandiri.
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- PATH 4: ASESMEN FORMATIF (UJI PEMAHAMAN) -->
                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_4_<?= $mat['id_materi'] ?>">
                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 4)" style="cursor: pointer;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_4_<?= $mat['id_materi'] ?>">
                                                                    <i class="fas fa-lock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="lp-main-row-title">Path 4 : Asesmen Formatif (Uji Pemahaman)</h6>
                                                                    <p class="lp-main-row-desc mb-0">Latihan soal pemahaman konsep dan uji ketercapaian Tujuan Pembelajaran (TP)</p>
                                                                </div>
                                                            </div>
                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_4_<?= $mat['id_materi'] ?>"></i></div>
                                                        </div>
                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_4_<?= $mat['id_materi'] ?>">
                                                            <div class="d-flex flex-column align-items-center text-center py-4 px-3 bg-light rounded">
                                                                <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 0 8px rgba(251,191,36,0.15);">
                                                                    <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                                                </div>
                                                                <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.95rem;">
                                                                    Path 4 : Asesmen Formatif — Akses Dikunci
                                                                </h6>
                                                                <p class="text-muted mb-0" style="font-size:0.83rem;max-width:380px;line-height:1.55;">
                                                                    Akses belum dibuka. Menunggu penugasan resmi dari guru untuk materi ini.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- PATH 5: RUANG DISKUSI & TANYA JAWAB -->
                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_5_<?= $mat['id_materi'] ?>">
                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 5)" style="cursor: pointer;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_5_<?= $mat['id_materi'] ?>">
                                                                    <i class="fas fa-lock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="lp-main-row-title">Path 5 : Ruang Diskusi &amp; Tanya Jawab</h6>
                                                                    <p class="lp-main-row-desc mb-0">Forum kolaborasi interaktif antar-siswa dan verifikasi langsung guru</p>
                                                                </div>
                                                            </div>
                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_5_<?= $mat['id_materi'] ?>"></i></div>
                                                        </div>
                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_5_<?= $mat['id_materi'] ?>">
                                                            <div class="d-flex flex-column align-items-center text-center py-4 px-3 bg-light rounded">
                                                                <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 0 8px rgba(251,191,36,0.15);">
                                                                    <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                                                </div>
                                                                <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.95rem;">
                                                                    Path 5 : Ruang Diskusi &amp; Tanya Jawab — Akses Dikunci
                                                                </h6>
                                                                <p class="text-muted mb-0" style="font-size:0.83rem;max-width:380px;line-height:1.55;">
                                                                    Akses belum dibuka. Menunggu penugasan resmi dari guru untuk materi ini.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- PATH 6: REFLEKSI DIRI & TUNTASKAN MODUL -->
                                                    <div class="lp-main-row-card mb-2.5" id="card_lp_6_<?= $mat['id_materi'] ?>">
                                                        <div class="d-flex justify-content-between align-items-center" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 6)" style="cursor: pointer;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="lp-main-badge-circle locked mr-3" id="badge_lp_6_<?= $mat['id_materi'] ?>">
                                                                    <i class="fas fa-lock"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="lp-main-row-title">Path 6 : Refleksi Diri &amp; Tuntaskan Modul</h6>
                                                                    <p class="lp-main-row-desc mb-0">Umpan balik pemahaman belajar siswa dan penguncian status tuntas modul</p>
                                                                </div>
                                                            </div>
                                                            <div><i class="fas fa-chevron-down text-muted lp-main-chevron" id="chevron_lp_6_<?= $mat['id_materi'] ?>"></i></div>
                                                        </div>
                                                        <div class="collapse mt-3 pt-3 border-top" id="body_lp_6_<?= $mat['id_materi'] ?>">
                                                            <div class="d-flex flex-column align-items-center text-center py-4 px-3 bg-light rounded">
                                                                <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 0 8px rgba(251,191,36,0.15);">
                                                                    <i class="fas fa-lock" style="font-size:1.4rem;color:#d97706;"></i>
                                                                </div>
                                                                <h6 class="font-weight-bold mb-1" style="color:#1e293b;font-size:0.95rem;">
                                                                    Path 6 : Refleksi Diri &amp; Tuntaskan Modul — Akses Dikunci
                                                                </h6>
                                                                <p class="text-muted mb-0" style="font-size:0.83rem;max-width:380px;line-height:1.55;">
                                                                    Akses belum dibuka. Menunggu penugasan resmi dari guru untuk materi ini.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (empty($bab['sub_bab_list']) && empty($bab['materi_direct'])): ?>
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 text-muted opacity-50"></i>
                                        <p class="mb-0 small">Belum ada modul atau sub-bab yang ditambahkan di bab ini.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card lms-card shadow-sm border-0 py-5 text-center">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-book-reader fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-dark font-weight-bold">Tidak Ada Struktur Modul</h5>
                        <p class="text-muted small mb-0">Silakan pilih mata pelajaran lain atau hubungi guru Anda jika belum ada modul yang diterbitkan.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Map id_materi => true/false berdasarkan status penugasan per modul
// Path 4-6 hanya terbuka jika modul tersebut sudah ditugaskan oleh guru
const penugasanMateriIds = new Set(<?= json_encode($penugasan_materi_ids ?? []) ?>);

function isPenugasan(matId) {
    return penugasanMateriIds.has(parseInt(matId));
}

function applyFilter() {
    $('#filterFormMateri').submit();
}

function setSemester(sem) {
    $('#semesterFilter').val(sem);
    $('#filterFormMateri').submit();
}

function toggleBab(idBab) {
    const body = $('#body_bab_' + idBab);
    const icon = $('#icon_bab_' + idBab);
    const card = $('#bab_card_' + idBab);
    if (!body.length) return;
    
    if (body.is(':visible')) {
        body.slideUp(200);
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        card.removeClass('expanded');
    } else {
        body.slideDown(200);
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        card.addClass('expanded');
    }
}

function toggleSubBab(idSub) {
    const collapseEl = $('#collapse_sub_' + idSub);
    const icon = $('#icon_sub_' + idSub);
    collapseEl.collapse('toggle');
    if (collapseEl.hasClass('show')) {
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
}

function toggleModulLP(matId) {
    const wrapper = $('#wrapper_lp_' + matId);
    const icon = $('#icon_lp_toggle_' + matId);
    if (!wrapper.length) return;
    
    if (wrapper.is(':visible')) {
        wrapper.slideUp(220);
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    } else {
        // ── AUTO-CLOSE: Tutup semua learning path lain di sub-bab maupun modul lain ──
        $('.lp-rows-wrapper').not(wrapper).each(function() {
            if ($(this).is(':visible')) {
                $(this).slideUp(220);
                const otherId = $(this).attr('id').replace('wrapper_lp_', '');
                $('#icon_lp_toggle_' + otherId).removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });

        wrapper.slideDown(220);
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
}

function toggleLPRow(matId, pathNum) {
    const body    = $('#body_lp_'    + pathNum + '_' + matId);
    const chevron = $('#chevron_lp_' + pathNum + '_' + matId);
    const badge   = $('#badge_lp_'   + pathNum + '_' + matId);

    // ── TUTUP jika path ini sudah terbuka ─────────────────────────────
    if (body.is(':visible')) {
        body.slideUp(200);
        chevron.css('transform', 'rotate(0deg)');
        return;
    }

    // ── AUTO-CLOSE: Tutup semua path lain di modul ini ─────────────────
    for (let p = 1; p <= 6; p++) {
        if (p !== pathNum) {
            const otherBody    = $('#body_lp_'    + p + '_' + matId);
            const otherChevron = $('#chevron_lp_' + p + '_' + matId);
            if (otherBody.length && otherBody.is(':visible')) {
                otherBody.slideUp(200);
                otherChevron.css('transform', 'rotate(0deg)');
            }
        }
    }

    // ── BUKA path yang dipilih ────────────────────────────────────────
    body.slideDown(200);
    chevron.css('transform', 'rotate(180deg)');

    if (pathNum <= 3 && badge && badge.hasClass('locked')) {
        badge.removeClass('locked').addClass('unlocked').html('<i class="fas fa-folder-open"></i>');
    }

    if (pathNum === 3 && window.MathJax && MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
