<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>
    /* Styling Dasar Kartu LMS Guru (Aligned with Siswa LMS) */
    .page-guru-materi .lms-card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
        background: #ffffff;
    }
    
    /* Filter Bar Styling */
    .page-guru-materi .custom-filter-select {
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
    .page-guru-materi .custom-filter-select:focus {
        border-color: #4f46e5 !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
    }
    .page-guru-materi .semester-segmented-control {
        display: flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 50px;
        border: 1.5px solid #cbd5e1;
        height: 42px;
        width: 100%;
    }
    .page-guru-materi .semester-segmented-control .sem-btn {
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
    .page-guru-materi .semester-segmented-control .sem-btn.active {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
    }
    .page-guru-materi .view-toggle-group {
        height: 42px;
    }
    .page-guru-materi .view-toggle-group .btn-view {
        height: 42px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    .page-guru-materi .view-toggle-group .btn-view.active {
        background: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: #fff !important;
    }

    /* Bab Card */
    .page-guru-materi .bab-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 14px;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        overflow: hidden;
        transition: border-color 0.2s ease;
    }
    .page-guru-materi .bab-card:hover {
        border-color: #cbd5e1;
    }
    .page-guru-materi .bab-card-header {
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
    .page-guru-materi .bab-card-header:hover {
        background: #f1f5f9;
    }
    .page-guru-materi .bab-card.expanded .bab-card-header {
        border-bottom-color: #e2e8f0;
        background: #eef2ff;
    }
    .page-guru-materi .bab-card.expanded .bab-title {
        color: #4338ca;
    }
    .page-guru-materi .bab-badge-num {
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
    .page-guru-materi .sub-bab-item {
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

    /* Sleek Modul Item Card (Identical to Student Portal) */
    .page-guru-materi .modul-item-card {
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
    .page-guru-materi .modul-item-card:hover {
        border-color: #4f46e5;
        box-shadow: 0 3px 12px rgba(79, 70, 229, 0.08);
        transform: translateY(-1px);
    }
    .page-guru-materi .modul-icon-box {
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
    .page-guru-materi .modul-title-text {
        font-size: 0.90rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 3px;
        line-height: 1.35;
    }
    .page-guru-materi .modul-feature-tag {
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
    .page-guru-materi .tag-video { background: #fee2e2; color: #b91c1c; }
    .page-guru-materi .tag-text { background: #e0e7ff; color: #4338ca; }
    .page-guru-materi .tag-quiz { background: #dcfce7; color: #15803d; }
    .page-guru-materi .tag-doc { background: #fef3c7; color: #b45309; }

    .page-guru-materi .btn-pelajari {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
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
    .page-guru-materi .btn-pelajari:hover {
        color: #ffffff !important;
        background: linear-gradient(135deg, #4338ca 0%, #312e81 100%);
        box-shadow: 0 3px 10px rgba(79, 70, 229, 0.35);
        transform: translateX(2px);
    }
    .page-guru-materi .btn-edit-modul {
        background: #f8fafc;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 5px 12px;
        border-radius: 50px;
        border: 1px solid #cbd5e1;
        white-space: nowrap;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .page-guru-materi .btn-edit-modul:hover {
        background: #e2e8f0;
        color: #1e293b !important;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (ULTRA-COMPACT)                     */
    /* ============================================================ */
    @media (max-width: 768px) {
        .page-guru-materi {
            padding: 0 !important;
            margin: 0 !important;
        }
        .page-guru-materi .container-fluid {
            padding: 4px !important;
        }
        .page-guru-materi .content-header {
            padding: 8px 4px 4px !important;
        }
        .page-guru-materi .content-header h1,
        .page-guru-materi .content-header h4 {
            font-size: 0.88rem !important;
        }
        .page-guru-materi .header-actions {
            display: flex;
            gap: 6px;
            width: 100%;
            margin-top: 6px;
        }
        .page-guru-materi .header-actions .btn {
            flex: 1 1 50%;
            font-size: 0.70rem !important;
            padding: 5px 8px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        .page-guru-materi .lms-card {
            border-radius: 8px !important;
            padding: 6px 8px !important;
            margin-bottom: 8px !important;
        }
        .page-guru-materi .custom-filter-select,
        .page-guru-materi .semester-segmented-control {
            height: 32px !important;
            font-size: 0.74rem !important;
        }
        .page-guru-materi .semester-segmented-control .sem-btn {
            font-size: 0.70rem !important;
            padding: 0 4px;
        }
        .page-guru-materi .view-toggle-group, 
        .page-guru-materi .view-toggle-group .btn-view {
            height: 32px !important;
            font-size: 0.72rem !important;
        }
        .page-guru-materi .bab-card {
            border-radius: 8px !important;
            margin-bottom: 6px !important;
        }
        .page-guru-materi .bab-card-header {
            padding: 6px 8px !important;
        }
        .page-guru-materi .bab-badge-num {
            width: 20px !important;
            height: 20px !important;
            font-size: 0.68rem !important;
            margin-right: 6px !important;
        }
        .page-guru-materi .bab-title {
            font-size: 0.78rem !important;
        }
        .page-guru-materi .bab-body > div {
            padding: 3px !important;
        }
        .page-guru-materi .sub-bab-item {
            padding: 5px 4px !important;
            margin-bottom: 5px !important;
            border-left-width: 3px !important;
            border-radius: 0 6px 6px 0 !important;
        }
        .page-guru-materi .sub-bab-item h6 {
            font-size: 0.76rem !important;
        }
        .page-guru-materi .sub-bab-item p {
            font-size: 0.68rem !important;
        }
        .page-guru-materi .modul-item-card {
            flex-direction: column;
            align-items: stretch;
            padding: 6px 6px !important;
            gap: 5px !important;
            border-radius: 6px !important;
            margin-bottom: 5px !important;
        }
        .page-guru-materi .modul-icon-box {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.75rem !important;
            border-radius: 5px !important;
        }
        .page-guru-materi .modul-title-text {
            font-size: 0.76rem !important;
            line-height: 1.3 !important;
        }
        .page-guru-materi .modul-feature-tag {
            font-size: 0.58rem !important;
            padding: 1px 4px !important;
        }
        .page-guru-materi .modul-btn-group-mobile {
            display: flex;
            gap: 5px;
            width: 100%;
            margin-top: 4px;
        }
        .page-guru-materi .modul-btn-group-mobile .btn,
        .page-guru-materi .modul-btn-group-mobile .btn-edit-modul,
        .page-guru-materi .modul-btn-group-mobile .btn-pelajari {
            flex: 1;
            font-size: 0.68rem !important;
            padding: 5px 4px !important;
            justify-content: center;
            display: inline-flex;
            align-items: center;
            text-align: center;
            white-space: nowrap;
        }
        /* Mobile Learning Path Optimization */
        .page-guru-materi .lp-rows-wrapper {
            padding: 4px 2px !important;
            border-radius: 6px !important;
            margin-top: 4px !important;
        }
        .page-guru-materi .lp-main-row-card {
            padding: 6px 6px !important;
            border-radius: 6px !important;
            margin-bottom: 4px !important;
        }
        .page-guru-materi .lp-main-badge-circle {
            width: 22px !important;
            height: 22px !important;
            font-size: 0.65rem !important;
            margin-right: 6px !important;
        }
        .page-guru-materi .lp-main-row-title {
            font-size: 0.75rem !important;
            line-height: 1.25 !important;
        }
        .page-guru-materi .lp-main-row-desc {
            font-size: 0.64rem !important;
            margin-top: 1px !important;
            line-height: 1.2 !important;
        }
        .page-guru-materi .lp-main-chevron {
            font-size: 0.68rem !important;
        }
        .page-guru-materi .lp-main-row-card .collapse {
            margin-top: 6px !important;
            padding-top: 6px !important;
        }
        .page-guru-materi .lp-main-row-card .p-3,
        .page-guru-materi .lp-main-row-card .p-4 {
            padding: 6px 6px !important;
            margin-bottom: 5px !important;
            border-radius: 5px !important;
        }
        .page-guru-materi .lp-main-row-card h5,
        .page-guru-materi .lp-main-row-card h6 {
            font-size: 0.74rem !important;
            margin-bottom: 3px !important;
        }
        .page-guru-materi .lp-main-row-card p {
            font-size: 0.68rem !important;
            line-height: 1.4 !important;
        }
        .page-guru-materi .article-content,
        .page-guru-materi .prose-content,
        .page-guru-materi .reading-box {
            font-size: 0.70rem !important;
            line-height: 1.4 !important;
            padding: 6px 6px !important;
        }
        .page-guru-materi .article-content h1,
        .page-guru-materi .article-content h2,
        .page-guru-materi .article-content h3 {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            margin-top: 4px !important;
            margin-bottom: 2px !important;
        }
        .page-guru-materi .article-content h4,
        .page-guru-materi .article-content h5,
        .page-guru-materi .article-content h6 {
            font-size: 0.74rem !important;
            font-weight: 700 !important;
            margin-top: 3px !important;
            margin-bottom: 2px !important;
        }
        .page-guru-materi .article-content p,
        .page-guru-materi .article-content li,
        .page-guru-materi .article-content td,
        .page-guru-materi .article-content th,
        .page-guru-materi .article-content span {
            font-size: 0.68rem !important;
        }
        .page-guru-materi .lp-main-row-card .alert {
            padding: 6px 6px !important;
            font-size: 0.68rem !important;
            margin-bottom: 5px !important;
        }
        .page-guru-materi .lp-main-row-card .btn,
        .page-guru-materi .btn-sm {
            font-size: 0.68rem !important;
            padding: 4px 10px !important;
        }
        .page-guru-materi small, .page-guru-materi .small {
            font-size: 0.64rem !important;
        }
        .page-guru-materi .badge {
            font-size: 0.60rem !important;
        }
    }
    /* Learning Path Rows on Main Page (Exact Match to User UI Reference) */
    .page-guru-materi .lp-main-row-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px 20px;
        margin-bottom: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .page-guru-materi .lp-main-row-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.08);
        transform: translateY(-1px);
    }
    .page-guru-materi .lp-main-row-card.completed {
        background: #f0fdf4;
        border-color: #10b981;
    }
    .page-guru-materi .lp-main-badge-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .page-guru-materi .lp-main-badge-circle.completed {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
    }
    .page-guru-materi .lp-main-badge-circle.locked {
        background: #f8fafc;
        color: #94a3b8;
        border: 1.5px dashed #cbd5e1;
        font-size: 0.85rem;
    }
    .page-guru-materi .lp-main-badge-circle.unlocked {
        background: #eef2ff;
        color: #4f46e5;
        border: 1.5px solid #a5b4fc;
        font-size: 0.85rem;
    }
    .page-guru-materi .lp-main-row-title {
        font-size: 0.98rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        line-height: 1.3;
    }
    .page-guru-materi .lp-main-row-title.completed {
        color: #065f46;
    }
    .page-guru-materi .lp-main-row-desc {
        font-size: 0.82rem;
        color: #64748b;
        margin-top: 3px;
        line-height: 1.4;
    }
    .page-guru-materi .lp-main-chevron {
        font-size: 0.90rem;
        transition: transform 0.2s ease;
    }
</style>

<div class="content-header pt-3 mb-2 page-guru-materi">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Bahan Ajar &amp; Modul Digital
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <?php if (!in_array('Siswa', user_roles())): ?>
                    <div class="header-actions d-inline-flex">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalTambahBab">
                            <i class="fas fa-folder-plus mr-1"></i> + Bab Baru
                        </button>
                        <a href="<?= BASE_URL ?>lms/materi_upload" class="btn btn-primary btn-sm rounded-pill font-weight-bold shadow-sm">
                            <i class="fas fa-plus mr-1"></i> + Buat Modul
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="content page-guru-materi">
    <div class="container-fluid">

        <!-- FILTER TOOLBAR: MAPEL, TINGKAT, SEMESTER & TAMPILAN -->
        <div class="card lms-card p-3 mb-3 shadow-sm">
            <form method="GET" action="<?= BASE_URL ?>index.php" id="filterFormMateri" class="row align-items-end" style="row-gap: 10px;">
                <input type="hidden" name="mod" value="lms">
                <input type="hidden" name="act" value="materi_list">
                <input type="hidden" name="semester" id="input_semester" value="<?= htmlspecialchars($semester_filter) ?>">

                <!-- MATA PELAJARAN -->
                <div class="col-lg-4 col-md-5 col-12">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1 d-flex align-items-center" style="font-size: 0.75rem;">
                        <i class="fas fa-book text-primary mr-1"></i> Mata Pelajaran
                    </label>
                    <select name="id_mapel" class="form-control custom-filter-select rounded-pill" onchange="$('#filterFormMateri').submit()">
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?= $m['id_mapel'] ?>" <?= ($id_mapel_filter == $m['id_mapel']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nama_mapel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- TINGKAT -->
                <div class="col-lg-2 col-md-3 col-6">
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

                <!-- TAMPILAN VIEW MODE -->
                <div class="col-lg-2 col-md-12 col-6">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1 d-block text-lg-right" style="font-size: 0.75rem;">Tampilan</label>
                    <div class="btn-group w-100 view-toggle-group">
                        <button type="button" class="btn btn-outline-secondary btn-view <?= ($view_mode === 'tree') ? 'active' : '' ?>" onclick="setViewMode('tree')" title="Mode Daftar Isi Buku">
                            <i class="fas fa-list-ol mr-1"></i> Buku
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-view <?= ($view_mode === 'grid') ? 'active' : '' ?>" onclick="setViewMode('grid')" title="Mode Kartu Grid">
                            <i class="fas fa-th-large mr-1"></i> Grid
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($view_mode === 'tree'): ?>
            <!-- ========================================== -->
            <!-- 📖 MODE 1: DAFTAR ISI BUKU TEKS (TREE VIEW) -->
            <!-- ========================================== -->
            <?php if (!empty($curriculum_tree['bab_list']) || !empty($curriculum_tree['standalone_materi'])): ?>
                <div id="accordionBab" class="curriculum-tree-container">
                    <?php 
                    $no_bab = 1; 
                    foreach ($curriculum_tree['bab_list'] as $bab): 
                        $total_modul_bab = count($bab['materi_direct']) + array_sum(array_map(fn($s) => count($s['materi_list']), $bab['sub_bab_list']));
                    ?>
                        <div class="bab-card expanded mb-3" id="card_bab_<?= $bab['id_bab'] ?>">
                            <!-- BAB HEADER -->
                            <div class="bab-card-header" onclick="toggleBab(<?= $bab['id_bab'] ?>)">
                                <div class="d-flex align-items-center flex-grow-1 mr-2" style="min-width: 0;">
                                    <div class="bab-badge-num">
                                        <?= $bab['urutan_bab'] ?: $no_bab ?>
                                    </div>
                                    <div style="min-width: 0;">
                                        <h5 class="bab-title font-weight-bold mb-0 text-truncate" style="font-size: 0.92rem;" title="BAB <?= $bab['urutan_bab'] ?: $no_bab ?>: <?= htmlspecialchars($bab['judul_bab']) ?>">
                                            BAB <?= $bab['urutan_bab'] ?: $no_bab ?>: <?= htmlspecialchars($bab['judul_bab']) ?>
                                        </h5>
                                        <?php if ($bab['deskripsi']): ?>
                                            <p class="small text-muted mb-0 mt-1 text-truncate" style="font-size: 0.75rem;"><?= htmlspecialchars($bab['deskripsi']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center" onclick="event.stopPropagation();">
                                    <span class="badge badge-light border text-muted mr-2" style="font-size: 0.68rem;">
                                        <?= count($bab['sub_bab_list']) ?> Sub-Bab &bull; <?= $total_modul_bab ?> Modul
                                    </span>
                                    <?php if (!in_array('Siswa', user_roles())): ?>
                                        <button type="button" class="btn btn-xs btn-outline-primary mr-1" onclick="bukaModalSubBab(<?= $bab['id_bab'] ?>, '<?= htmlspecialchars(addslashes($bab['judul_bab'])) ?>')" title="Tambah Sub-Bab" style="font-size: 0.72rem; padding: 2px 7px; border-radius: 4px;">
                                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Sub-Bab</span>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-danger mr-2" onclick="hapusBab(<?= $bab['id_bab'] ?>)" title="Hapus Bab" style="font-size: 0.72rem; padding: 2px 7px; border-radius: 4px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php endif; ?>
                                    <i class="fas fa-chevron-up toggle-icon text-muted" id="icon_bab_<?= $bab['id_bab'] ?>" onclick="toggleBab(<?= $bab['id_bab'] ?>)" style="cursor: pointer;"></i>
                                </div>
                            </div>

                            <!-- BAB BODY (SUB-BAB & MATERI) -->
                            <div class="bab-collapse-body" id="collapse_bab_<?= $bab['id_bab'] ?>" style="display: block;">
                                <div class="p-2 p-md-3 bg-light">
                                    
                                    <!-- DAFTAR SUB-BAB -->
                                    <?php if (!empty($bab['sub_bab_list'])): ?>
                                        <?php foreach ($bab['sub_bab_list'] as $sub): ?>
                                            <div class="sub-bab-item mb-3" id="sub_box_<?= $sub['id_sub_bab'] ?>">
                                                <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleSubBab(<?= $sub['id_sub_bab'] ?>)">
                                                    <div class="d-flex align-items-center" style="min-width: 0;">
                                                        <i class="fas fa-bookmark text-primary mr-2 flex-shrink-0" style="font-size: 0.82rem;"></i>
                                                        <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="font-size: 0.85rem;" title="<?= htmlspecialchars($sub['judul_sub_bab']) ?>">
                                                            Sub-Bab <?= $sub['urutan_sub'] ?? $sub['urutan_sub_bab'] ?? 1 ?>: <?= htmlspecialchars($sub['judul_sub_bab']) ?>
                                                            <span class="badge badge-light border ml-2 text-muted font-weight-normal" style="font-size: 0.65rem;"><?= count($sub['materi_list']) ?> Modul</span>
                                                        </h6>
                                                    </div>
                                                    <div class="d-flex align-items-center" onclick="event.stopPropagation();">
                                                        <?php if (!in_array('Siswa', user_roles())): ?>
                                                            <a href="<?= BASE_URL ?>lms/materi_upload?id_mapel=<?= $id_mapel_filter ?>&id_bab=<?= $bab['id_bab'] ?>&id_sub_bab=<?= $sub['id_sub_bab'] ?>&tingkat=<?= $tingkat_filter ?>" class="btn btn-xs btn-light border text-primary mr-1" title="Tambah Modul di Sub-Bab ini" style="font-size: 0.70rem; padding: 2px 6px; border-radius: 4px;">
                                                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Modul</span>
                                                            </a>
                                                            <button type="button" class="btn btn-xs btn-light border text-danger mr-2" onclick="hapusSubBab(<?= $sub['id_sub_bab'] ?>)" title="Hapus Sub-Bab" style="font-size: 0.70rem; padding: 2px 6px; border-radius: 4px;">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <i class="fas fa-chevron-up text-muted toggle-sub-icon" id="icon_sub_<?= $sub['id_sub_bab'] ?>" onclick="toggleSubBab(<?= $sub['id_sub_bab'] ?>)"></i>
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
                                                                    <div class="modul-item-card mb-2" id="mat_card_<?= $mat['id_materi'] ?>">
                                                                        <div class="d-flex align-items-center flex-grow-1" style="gap: 12px; min-width: 0;">
                                                                            <div class="modul-icon-box">
                                                                                <i class="fas <?= $mat['video_url'] ? 'fa-play-circle text-danger' : 'fa-book-reader text-primary' ?>"></i>
                                                                            </div>
                                                                            <div style="min-width: 0;">
                                                                                <div class="modul-title-text text-truncate" title="<?= htmlspecialchars($mat['judul_materi']) ?>">
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
                                                                        <div class="modul-btn-group-mobile d-flex align-items-center" style="gap: 6px;">
                                                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 font-weight-bold text-primary shadow-sm" onclick="event.stopPropagation(); toggleModulLP(<?= $mat['id_materi'] ?>);" style="font-size: 0.78rem;">
                                                                                <i class="fas fa-stream mr-1 text-info"></i> <span id="text_lp_toggle_<?= $mat['id_materi'] ?>">Titian Belajar</span> <i class="fas fa-chevron-down ml-1" id="icon_lp_toggle_<?= $mat['id_materi'] ?>"></i>
                                                                            </button>
                                                                            <?php if (!in_array('Siswa', user_roles())): ?>
                                                                                <a href="<?= BASE_URL ?>lms/materi_edit?id=<?= $mat['id_materi'] ?>" class="btn-edit-modul" title="Edit Modul">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </a>
                                                                            <?php endif; ?>
                                                                            <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>" class="btn-pelajari" title="Buka Modul Pembelajaran">
                                                                                <span>Buka Modul</span> <i class="fas fa-arrow-right"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                    <!-- 🛣️ LEARNING PATH 6 EXPANDABLE ROWS (COLLAPSE/ELAPSE IN-PLACE - TEACHER VIEW, DEFAULT COLLAPSED) -->
                                                                    <div class="lp-rows-wrapper mt-2 mb-3 pl-2" id="wrapper_lp_<?= $mat['id_materi'] ?>" style="display: none;">
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
                                                                                        Lihat Path 2: Video <i class="fas fa-arrow-down ml-1"></i>
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
                                                                                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?= $v_id ?>" allowfullscreen></iframe>
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
                                                                                        Tidak ada lampiran video pada modul ini.
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                                <div class="text-right mt-2">
                                                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 3)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                                        Lihat Path 3: Literasi Teks <i class="fas fa-arrow-down ml-1"></i>
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
                                                                                                <small class="text-muted">Berkas lampiran digital materi</small>
                                                                                            </div>
                                                                                        </div>
                                                                                        <a href="<?= BASE_URL ?>uploads/materi/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-weight-bold">
                                                                                            <i class="fas fa-download mr-1"></i> Buka / Unduh Dokumen
                                                                                        </a>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                                <div class="p-3 bg-white rounded border article-content shadow-sm" style="font-size: 0.92rem; line-height: 1.6; max-height: 500px; overflow-y: auto;">
                                                                                    <?= ($mat['deskripsi'] ?? ($mat['isi_materi'] ?? '')) ?: '<p class="text-muted font-italic mb-0">Tidak ada isi uraian teks.</p>' ?>
                                                                                </div>
                                                                                <div class="text-right mt-2">
                                                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 4)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                                        Lihat Path 4: Kuis Formatif <i class="fas fa-arrow-down ml-1"></i>
                                                                                    </button>
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
                                                                                <div class="p-4 bg-light rounded text-center">
                                                                                    <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                                                                                    <h6 class="font-weight-bold text-dark">Kuis &amp; Asesmen Formatif</h6>
                                                                                    <p class="text-muted small mb-3">Pratinjau butir soal atau pantau hasil capaian latihan formatif siswa.</p>
                                                                                    <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>#stage-4" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                                        <i class="fas fa-eye mr-1"></i> Buka Asesmen Formatif
                                                                                    </a>
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
                                                                                <div class="p-4 bg-light rounded text-center">
                                                                                    <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                                                                                    <h6 class="font-weight-bold text-dark">Forum Kolaborasi &amp; Tanya Jawab</h6>
                                                                                    <p class="text-muted small mb-3">Berikan respon, klarifikasi konsep, dan verifikasi pertanyaan siswa.</p>
                                                                                    <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>#stage-5" class="btn btn-info rounded-pill px-4 font-weight-bold shadow-sm">
                                                                                        <i class="fas fa-comment-dots mr-1"></i> Masuk ke Ruang Diskusi
                                                                                    </a>
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
                                                                                <div class="p-4 bg-light rounded text-center">
                                                                                    <i class="fas fa-medal fa-3x text-success mb-3"></i>
                                                                                    <h6 class="font-weight-bold text-dark">Refleksi Belajar &amp; Kunci Penuntasan</h6>
                                                                                    <p class="text-muted small mb-3">Lihat refleksi yang dikirimkan oleh siswa dan rekapitulasi ketuntasan materi.</p>
                                                                                    <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>#stage-6" class="btn btn-success rounded-pill px-4 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                                                                        <i class="fas fa-check mr-1"></i> Tinjau Refleksi Siswa
                                                                                    </a>
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
                                        <h6 class="small font-weight-bold text-muted text-uppercase mb-2" style="font-size: 0.72rem;"><i class="fas fa-folder-open mr-1 text-primary"></i> Modul Mandiri (Tanpa Sub-Bab):</h6>
                                        <?php foreach ($bab['materi_direct'] as $mat): ?>
                                            <div class="mb-3">
                                                <div class="modul-item-card mb-2" id="mat_card_<?= $mat['id_materi'] ?>">
                                                    <div class="d-flex align-items-center flex-grow-1" style="gap: 12px; min-width: 0;">
                                                        <div class="modul-icon-box">
                                                            <i class="fas <?= $mat['video_url'] ? 'fa-play-circle text-danger' : 'fa-book-reader text-primary' ?>"></i>
                                                        </div>
                                                        <div style="min-width: 0;">
                                                            <div class="modul-title-text text-truncate" title="<?= htmlspecialchars($mat['judul_materi']) ?>">
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
                                                    <div class="modul-btn-group-mobile d-flex align-items-center" style="gap: 6px;">
                                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 font-weight-bold text-primary shadow-sm" onclick="event.stopPropagation(); toggleModulLP(<?= $mat['id_materi'] ?>);" style="font-size: 0.78rem;">
                                                            <i class="fas fa-stream mr-1 text-info"></i> <span id="text_lp_toggle_<?= $mat['id_materi'] ?>">Titian Belajar</span> <i class="fas fa-chevron-down ml-1" id="icon_lp_toggle_<?= $mat['id_materi'] ?>"></i>
                                                        </button>
                                                        <?php if (!in_array('Siswa', user_roles())): ?>
                                                            <a href="<?= BASE_URL ?>lms/materi_edit?id=<?= $mat['id_materi'] ?>" class="btn-edit-modul" title="Edit Modul">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>" class="btn-pelajari" title="Buka Modul Pembelajaran">
                                                            <span>Buka Modul</span> <i class="fas fa-arrow-right"></i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- 🛣️ LEARNING PATH 6 EXPANDABLE ROWS (COLLAPSE/ELAPSE IN-PLACE - TEACHER VIEW, DEFAULT COLLAPSED) -->
                                                <div class="lp-rows-wrapper mt-2 mb-3 pl-2" id="wrapper_lp_<?= $mat['id_materi'] ?>" style="display: none;">
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
                                                                    Lihat Path 2: Video <i class="fas fa-arrow-down ml-1"></i>
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
                                                                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?= $v_id ?>" allowfullscreen></iframe>
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
                                                                    Tidak ada lampiran video pada modul ini.
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="text-right mt-2">
                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 3)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                    Lihat Path 3: Literasi Teks <i class="fas fa-arrow-down ml-1"></i>
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
                                                                            <small class="text-muted">Berkas lampiran digital materi</small>
                                                                        </div>
                                                                    </div>
                                                                    <a href="<?= BASE_URL ?>uploads/materi/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 font-weight-bold">
                                                                        <i class="fas fa-download mr-1"></i> Buka / Unduh Dokumen
                                                                    </a>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="p-3 bg-white rounded border article-content shadow-sm" style="font-size: 0.92rem; line-height: 1.6; max-height: 500px; overflow-y: auto;">
                                                                <?= ($mat['deskripsi'] ?? ($mat['isi_materi'] ?? '')) ?: '<p class="text-muted font-italic mb-0">Tidak ada isi uraian teks.</p>' ?>
                                                            </div>
                                                            <div class="text-right mt-2">
                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold" onclick="toggleLPRow(<?= $mat['id_materi'] ?>, 4)" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                    Lihat Path 4: Kuis Formatif <i class="fas fa-arrow-down ml-1"></i>
                                                                </button>
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
                                                            <div class="p-4 bg-light rounded text-center">
                                                                <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                                                                <h6 class="font-weight-bold text-dark">Kuis &amp; Asesmen Formatif</h6>
                                                                <p class="text-muted small mb-3">Pratinjau butir soal atau pantau hasil capaian latihan formatif siswa.</p>
                                                                <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>#stage-4" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                                                    <i class="fas fa-eye mr-1"></i> Buka Asesmen Formatif
                                                                </a>
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
                                                            <div class="p-4 bg-light rounded text-center">
                                                                <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                                                                <h6 class="font-weight-bold text-dark">Forum Kolaborasi &amp; Tanya Jawab</h6>
                                                                <p class="text-muted small mb-3">Berikan respon, klarifikasi konsep, dan verifikasi pertanyaan siswa.</p>
                                                                <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>#stage-5" class="btn btn-info rounded-pill px-4 font-weight-bold shadow-sm">
                                                                    <i class="fas fa-comment-dots mr-1"></i> Masuk ke Ruang Diskusi
                                                                </a>
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
                                                            <div class="p-4 bg-light rounded text-center">
                                                                <i class="fas fa-medal fa-3x text-success mb-3"></i>
                                                                <h6 class="font-weight-bold text-dark">Refleksi Belajar &amp; Kunci Penuntasan</h6>
                                                                <p class="text-muted small mb-3">Lihat refleksi yang dikirimkan oleh siswa dan rekapitulasi ketuntasan materi.</p>
                                                                <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $mat['id_materi'] ?>#stage-6" class="btn btn-success rounded-pill px-4 font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                                                    <i class="fas fa-check mr-1"></i> Tinjau Refleksi Siswa
                                                                </a>
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
                                            <i class="fas fa-folder-open fa-2x mb-2 text-muted opacity-50"></i>
                                            <p class="small mb-2" style="font-size: 0.78rem;">Bab ini belum memiliki Sub-Bab atau Materi.</p>
                                            <?php if (!in_array('Siswa', user_roles())): ?>
                                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-3" onclick="bukaModalSubBab(<?= $bab['id_bab'] ?>, '<?= htmlspecialchars(addslashes($bab['judul_bab'])) ?>')">
                                                    <i class="fas fa-plus mr-1"></i> Tambah Sub-Bab Sekarang
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php $no_bab++; endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card lms-card p-5 text-center shadow-sm">
                    <i class="fas fa-book-open text-muted fa-4x mb-3 opacity-50"></i>
                    <h5 class="font-weight-bold text-dark">Belum Ada Struktur Bab pada Semester Ini</h5>
                    <p class="text-muted small mb-3">Buat Bab pertama untuk menyusun daftar isi buku mata pelajaran ini.</p>
                    <?php if (!in_array('Siswa', user_roles())): ?>
                        <div>
                            <button type="button" class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalTambahBab" style="background: linear-gradient(135deg, #4f46e5, #3730a3); border: none;">
                                <i class="fas fa-folder-plus mr-1"></i> + Buat Bab Baru
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- MATERI MANDIRI / BELUM DIKELOMPOKKAN KE BAB -->
            <?php if (!empty($curriculum_tree['standalone_materi'])): ?>
                <div class="card lms-card mt-3">
                    <div class="card-header bg-light border-0 py-2 px-3">
                        <h6 class="font-weight-bold text-dark mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-layer-group text-warning mr-2"></i> Modul Mandiri (Belum Dikelompokkan ke Bab)
                        </h6>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="row">
                            <?php foreach ($curriculum_tree['standalone_materi'] as $m): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="modul-item-card mb-0">
                                        <div class="d-flex align-items-center flex-grow-1" style="gap: 10px; min-width: 0;">
                                            <div class="modul-icon-box">
                                                <i class="fas fa-book-reader text-primary"></i>
                                            </div>
                                            <div style="min-width: 0;">
                                                <div class="modul-title-text text-truncate" title="<?= htmlspecialchars($m['judul_materi']) ?>">
                                                    <?= htmlspecialchars($m['judul_materi']) ?>
                                                </div>
                                                <small class="text-muted"><i class="fas fa-graduation-cap mr-1 text-primary"></i> Kelas <?= htmlspecialchars($m['tingkat']) ?></small>
                                            </div>
                                        </div>
                                        <div class="modul-btn-group-mobile d-flex align-items-center" style="gap: 6px;">
                                            <?php if (!in_array('Siswa', user_roles())): ?>
                                                <a href="<?= BASE_URL ?>lms/materi_edit?id=<?= $m['id_materi'] ?>" class="btn-edit-modul" title="Edit Modul">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $m['id_materi'] ?>" class="btn-pelajari" title="Buka Modul">
                                                <span>Buka</span> <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- ========================================== -->
            <!-- 🗂️ MODE 2: KARTU GRID TRADISIONAL -->
            <!-- ========================================== -->
            <div class="row">
                <?php if (!empty($materi)): ?>
                    <?php foreach ($materi as $m): ?>
                        <div class="col-xl-4 col-md-6 mb-3">
                            <div class="card lms-card h-100 p-3 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge badge-primary px-2 py-1" style="border-radius: 50px; font-size: 0.72rem;">
                                        <?= htmlspecialchars($m['nama_mapel']) ?>
                                    </span>
                                    <span class="badge badge-light border text-muted" style="font-size: 0.72rem;">Kelas <?= htmlspecialchars($m['tingkat']) ?></span>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.95rem;"><?= htmlspecialchars($m['judul_materi']) ?></h6>
                                <p class="text-muted small mb-3" style="font-size: 0.78rem;"><?= htmlspecialchars(substr(strip_tags($m['deskripsi'] ?? ''), 0, 90)) ?>...</p>
                                <div class="mt-auto d-flex align-items-center" style="gap: 6px;">
                                    <?php if (!in_array('Siswa', user_roles())): ?>
                                        <a href="<?= BASE_URL ?>lms/materi_edit?id=<?= $m['id_materi'] ?>" class="btn-edit-modul flex-grow-1 text-center justify-content-center">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>lms/materi_detail?id=<?= $m['id_materi'] ?>" class="btn-pelajari flex-grow-1 text-center justify-content-center">
                                        <i class="fas fa-book-reader mr-1"></i> Buka Modul
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 text-muted">Belum ada materi tersedia.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- MODAL TAMBAH BAB BARU -->
<div class="modal fade" id="modalTambahBab" tabindex="-1" role="dialog" aria-labelledby="modalTambahBabLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #4f46e5, #3730a3) !important;">
                <h5 class="modal-title font-weight-bold" id="modalTambahBabLabel">
                    <i class="fas fa-folder-plus mr-2 text-warning"></i> Tambah Bab Pelajaran Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSimpanBab" onsubmit="simpanBabBaru(event)">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
                    <input type="hidden" name="tingkat" value="<?= htmlspecialchars($tingkat_filter) ?>">
                    <input type="hidden" name="semester" value="<?= htmlspecialchars($semester_filter) ?>">

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">NOMOR / URUTAN BAB</label>
                        <input type="number" name="urutan_bab" class="form-control form-control-sm" value="<?= count($curriculum_tree['bab_list']) + 1 ?>" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">JUDUL BAB <span class="text-danger">*</span></label>
                        <input type="text" name="judul_bab" class="form-control" placeholder="Contoh: Eksponen dan Logaritma" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-dark text-uppercase">DESKRIPSI SINGKAT BAB (OPSIONAL)</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ringkasan ruang lingkup materi pada bab ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm" id="btnSubmitBab">
                        <i class="fas fa-save mr-1"></i> Simpan Bab
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH SUB-BAB -->
<div class="modal fade" id="modalTambahSubBab" tabindex="-1" role="dialog" aria-labelledby="modalTambahSubBabLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #6366f1, #4f46e5) !important;">
                <h5 class="modal-title font-weight-bold" id="modalTambahSubBabLabel">
                    <i class="fas fa-bookmark mr-2 text-warning"></i> Tambah Sub-Bab
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSimpanSubBab" onsubmit="simpanSubBabBaru(event)">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_bab" id="sub_id_bab" value="">
                    
                    <div class="alert alert-light border small text-muted mb-3">
                        Bab: <strong id="nama_bab_target" class="text-primary">-</strong>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">NOMOR / URUTAN SUB-BAB</label>
                        <input type="number" name="urutan_sub" class="form-control form-control-sm" value="1" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-dark text-uppercase">JUDUL SUB-BAB <span class="text-danger">*</span></label>
                        <input type="text" name="judul_sub_bab" class="form-control" placeholder="Contoh: Sifat-sifat Operasi Eksponen" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-dark text-uppercase">DESKRIPSI (OPSIONAL)</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 font-weight-bold shadow-sm" id="btnSubmitSubBab">
                        <i class="fas fa-save mr-1"></i> Simpan Sub-Bab
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setSemester(sem) {
    $('#input_semester').val(sem);
    $('#filterFormMateri').submit();
}

function setViewMode(v) {
    const url = new URL(window.location.href);
    url.searchParams.set('view', v);
    window.location.href = url.toString();
}

function simpanBabBaru(e) {
    e.preventDefault();
    const btn = $('#btnSubmitBab');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.post('<?= BASE_URL ?>index.php?mod=lms&act=bab_save', $('#formSimpanBab').serialize(), function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Bab');
        if (res.status === 'ok') {
            $('#modalTambahBab').modal('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json').fail(function() {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Bab');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    });
}

function bukaModalSubBab(idBab, judulBab) {
    $('#sub_id_bab').val(idBab);
    $('#nama_bab_target').text(judulBab);
    $('#modalTambahSubBab').modal('show');
}

function simpanSubBabBaru(e) {
    e.preventDefault();
    const btn = $('#btnSubmitSubBab');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.post('<?= BASE_URL ?>index.php?mod=lms&act=sub_bab_save', $('#formSimpanSubBab').serialize(), function(res) {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Sub-Bab');
        if (res.status === 'ok') {
            $('#modalTambahSubBab').modal('hide');
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
    }, 'json').fail(function() {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Sub-Bab');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    });
}

function hapusBab(idBab) {
    Swal.fire({
        title: 'Hapus Bab ini?',
        text: 'Sub-bab di dalamnya akan ikut terhapus, dan materi akan tetap aman.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= BASE_URL ?>index.php?mod=lms&act=bab_delete', { id_bab: idBab }, function(res) {
                if (res.status === 'ok') {
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            }, 'json');
        }
    });
}

function hapusSubBab(idSub) {
    Swal.fire({
        title: 'Hapus Sub-Bab ini?',
        text: 'Materi di dalamnya tidak akan terhapus, hanya status pengelompokannya yang dilepas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= BASE_URL ?>index.php?mod=lms&act=sub_bab_delete', { id_sub_bab: idSub }, function(res) {
                if (res.status === 'ok') {
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                }
            }, 'json');
        }
    });
}

function toggleBab(idBab) {
    const body = $('#collapse_bab_' + idBab);
    const icon = $('#icon_bab_' + idBab);
    const card = $('#card_bab_' + idBab);
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
}

$(document).on('shown.bs.collapse', '.sub-bab-item .collapse', function () {
    const id = $(this).attr('id').replace('collapse_sub_', '');
    $('#icon_sub_' + id).removeClass('fa-chevron-down').addClass('fa-chevron-up');
});
$(document).on('hidden.bs.collapse', '.sub-bab-item .collapse', function () {
    const id = $(this).attr('id').replace('collapse_sub_', '');
    $('#icon_sub_' + id).removeClass('fa-chevron-up').addClass('fa-chevron-down');
});

function toggleAllBabs(open) {
    if (open) {
        $('.bab-collapse-body').slideDown(200);
        $('.toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        $('.bab-card').addClass('expanded');
    } else {
        $('.bab-collapse-body').slideUp(200);
        $('.toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        $('.bab-card').removeClass('expanded');
    }
}

function toggleAllSubBabs(open) {
    if (open) {
        $('.sub-bab-item .collapse').collapse('show');
    } else {
        $('.sub-bab-item .collapse').collapse('hide');
    }
}

function toggleModulLP(matId) {
    const wrapper = $('#wrapper_lp_' + matId);
    const icon = $('#icon_lp_toggle_' + matId);
    const text = $('#text_lp_toggle_' + matId);
    if (!wrapper.length) return;
    
    if (wrapper.is(':visible')) {
        wrapper.slideUp(220);
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        if (text.length) text.text('Titian Belajar');
    } else {
        // ── AUTO-CLOSE: Tutup semua learning path lain di sub-bab maupun modul lain ──
        $('.lp-rows-wrapper').not(wrapper).each(function() {
            if ($(this).is(':visible')) {
                $(this).slideUp(220);
                const otherId = $(this).attr('id').replace('wrapper_lp_', '');
                $('#icon_lp_toggle_' + otherId).removeClass('fa-chevron-up').addClass('fa-chevron-down');
                const otherText = $('#text_lp_toggle_' + otherId);
                if (otherText.length) otherText.text('Titian Belajar');
            }
        });

        wrapper.slideDown(220);
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        if (text.length) text.text('Tutup Titian');
    }
}

function toggleLPRow(matId, pathNum) {
    const body = $('#body_lp_' + pathNum + '_' + matId);
    const chevron = $('#chevron_lp_' + pathNum + '_' + matId);
    const badge = $('#badge_lp_' + pathNum + '_' + matId);
    
    // ── TUTUP jika path ini sudah terbuka ─────────────────────────────
    if (body.is(':visible')) {
        body.slideUp(200);
        chevron.css('transform', 'rotate(0deg)');
        return;
    }

    // ── AUTO-CLOSE: Tutup semua path lain di modul ini ─────────────────
    for (let p = 1; p <= 6; p++) {
        if (p !== pathNum) {
            const otherBody = $('#body_lp_' + p + '_' + matId);
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
    if (badge && badge.hasClass('locked')) {
        badge.removeClass('locked').addClass('unlocked').html('<i class="fas fa-folder-open text-primary"></i>');
    }
    if (pathNum === 3 && window.MathJax && MathJax.typesetPromise) {
        MathJax.typesetPromise();
    }
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
