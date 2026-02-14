<?php
$header_path = __DIR__ . '/partials/header.php';
if (file_exists($header_path)) {
    include $header_path;
} else {
    echo "<!-- ERROR: Header not found at $header_path -->";
}
?>
<style>
    /* Custom Sidebar Gallery Style */
    .sidebar-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .sidebar-item {
        cursor: pointer;
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        padding-top: 60%;
        /* Ratio 5:3 */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .sidebar-item input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .sidebar-item.selected {
        border-color: var(--theme-accent, #3b82f6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    .sidebar-item .label-text {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 0.75rem;
        padding: 5px;
        text-align: center;
        backdrop-filter: blur(4px);
    }

    .sidebar-item .check-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        color: var(--theme-accent, #3b82f6);
        background: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }

    .sidebar-item.selected .check-icon {
        display: flex;
    }

    /* Gradients matching header.php logic */
    .bg-midnight_blue {
        background: linear-gradient(180deg, #020617 0%, #0f172a 100%);
    }

    .bg-glass_blue {
        background-color: rgba(10, 35, 70, 0.85);
    }

    .bg-royal_blue {
        background: linear-gradient(180deg, rgba(20, 60, 120, 0.9) 0%, rgba(10, 30, 70, 0.9) 100%);
    }

    .bg-slate_matte {
        background: linear-gradient(180deg, #3f576c 0%, #2c3e50 100%);
    }

    .bg-emerald_forest {
        background: linear-gradient(180deg, #064e3b 0%, #065f46 100%);
    }

    .bg-indigo_violet {
        background: linear-gradient(180deg, #312e81 0%, #4c1d95 100%);
    }

    .bg-deep_carbon {
        background: linear-gradient(180deg, #111827 0%, #000000 100%);
    }

    .bg-warm_gold {
        background: linear-gradient(180deg, #451a03 0%, #78350f 100%);
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-magic mr-2"></i> Custom Visual Aplikasi</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="index.php?mod=dashboard" class="btn btn-outline-secondary btn-sm"><i
                        class="fas fa-times mr-1"></i> Tutup</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <form action="index.php?mod=app_config&act=save" method="POST">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="theme-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="theme-home-tab" data-toggle="pill" href="#theme-colors"
                                role="tab" aria-controls="theme-colors" aria-selected="true">
                                <i class="fas fa-palette mr-2"></i>Warna & Sidebar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="theme-fonts-tab" data-toggle="pill" href="#theme-typography"
                                role="tab" aria-controls="theme-typography" aria-selected="false">
                                <i class="fas fa-font mr-2"></i>Tipografi
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="theme-tabContent">

                        <!-- TAB 1: WARNA & SIDEBAR -->
                        <div class="tab-pane fade show active" id="theme-colors" role="tabpanel"
                            aria-labelledby="theme-home-tab">
                            <div class="row">
                                <div class="col-md-5">
                                    <h5 class="mb-4 text-primary font-weight-bold border-bottom pb-2">Pengaturan Warna
                                    </h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label>Warna Aksen Utama</label>
                                                <div class="input-group">
                                                    <input type="color" name="theme_accent_color"
                                                        class="form-control form-control-sm"
                                                        style="height: 40px; border-radius: 8px 0 0 8px !important;"
                                                        value="<?= $config['theme_accent_color'] ?? '#3b82f6' ?>"
                                                        oninput="updateLiveColor(this, 'pv-accent')">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-white small px-2"
                                                            style="border-radius: 0 8px 8px 0 !important; font-size: 0.75rem;"
                                                            id="label-accent-color"><?= $config['theme_accent_color'] ?? '#3b82f6' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label>Menu Aktif (Sidebar)</label>
                                                <div class="input-group">
                                                    <input type="color" name="theme_menu_active_bg"
                                                        class="form-control form-control-sm"
                                                        style="height: 40px; border-radius: 8px 0 0 8px !important;"
                                                        value="<?= $config['theme_menu_active_bg'] ?? '#3b82f6' ?>">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-white small px-2"
                                                            style="border-radius: 0 8px 8px 0 !important; font-size: 0.75rem;"><?= $config['theme_menu_active_bg'] ?? '#3b82f6' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="small">Navigasi Atas</label>
                                                <input type="color" name="theme_navbar_bg" class="form-control"
                                                    value="<?= $config['theme_navbar_bg'] ?? '#ffffff' ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="small">Latar Belakang</label>
                                                <input type="color" name="theme_body_bg" class="form-control"
                                                    value="<?= $config['theme_body_bg'] ?? '#f8fafc' ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="small">Header Tabel</label>
                                                <input type="color" name="theme_table_header_bg" class="form-control"
                                                    value="<?= $config['theme_table_header_bg'] ?? '#f8f9fa' ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="small">Kaki Halaman</label>
                                                <input type="color" name="theme_footer_bg" class="form-control"
                                                    value="<?= $config['theme_footer_bg'] ?? '#ffffff' ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2 px-3 mt-4 small">
                                        <i class="fas fa-lightbulb mr-2"></i> Klik kotak warna untuk memilih dari palet
                                        warna sistem Anda.
                                    </div>
                                </div>

                                <div class="col-md-7 border-left pl-md-5">
                                    <h5 class="mb-4 text-primary font-weight-bold border-bottom pb-2">Galeri Sidebar
                                    </h5>

                                    <div class="sidebar-gallery">
                                        <?php
                                        $sidebar_options = [
                                            ['id' => 'midnight_blue', 'name' => 'Midnight Navy', 'class' => 'bg-midnight_blue'],
                                            ['id' => 'glass_blue', 'name' => 'Glassmorphism', 'class' => 'bg-glass_blue'],
                                            ['id' => 'royal_blue', 'name' => 'Royal Gradient', 'class' => 'bg-royal_blue'],
                                            ['id' => 'slate_matte', 'name' => 'Slate Matte', 'class' => 'bg-slate_matte'],
                                            ['id' => 'emerald_forest', 'name' => 'Emerald Forest', 'class' => 'bg-emerald_forest'],
                                            ['id' => 'indigo_violet', 'name' => 'Indigo Violet', 'class' => 'bg-indigo_violet'],
                                            ['id' => 'deep_carbon', 'name' => 'Deep Carbon', 'class' => 'bg-deep_carbon'],
                                            ['id' => 'warm_gold', 'name' => 'Warm Gold', 'class' => 'bg-warm_gold'],
                                        ];
                                        $current_sidebar = $config['theme_sidebar_bg'] ?? 'midnight_blue';

                                        foreach ($sidebar_options as $opt):
                                            $isChecked = ($current_sidebar == $opt['id']);
                                            ?>
                                            <label
                                                class="sidebar-item <?= $opt['class'] ?> <?= $isChecked ? 'selected' : '' ?>"
                                                onclick="selectSidebar(this)">
                                                <input type="radio" name="theme_sidebar_bg" value="<?= $opt['id'] ?>"
                                                    <?= $isChecked ? 'checked' : '' ?>>
                                                <span class="check-icon"><i class="fas fa-check"></i></span>
                                                <span class="label-text"><?= $opt['name'] ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: TIPOGRAFI -->
                        <div class="tab-pane fade" id="theme-typography" role="tabpanel"
                            aria-labelledby="theme-fonts-tab">
                            <div class="row justify-content-center">
                                <div class="col-md-9">
                                    <h5 class="mb-4 text-primary font-weight-bold border-bottom pb-2">Skala & Ukuran
                                        Tipografi</h5>

                                    <div class="card bg-light border-0 shadow-none mb-5">
                                        <div class="card-body p-4">
                                            <label class="font-weight-bold"><i
                                                    class="fas fa-expand-arrows-alt mr-2 text-primary"></i> Baseline
                                                Font Size (Global Scale)</label>
                                            <div class="d-flex align-items-center mt-2">
                                                <span class="mr-3 small text-muted">A</span>
                                                <input type="range" class="custom-range" min="0.70" max="1" step="0.01"
                                                    value="<?= str_replace('rem', '', ($config['theme_font_size'] ?? '0.85rem')) ?>"
                                                    oninput="updateFontRange(this, 'label-font-size', 'input-font-size')">
                                                <span class="ml-3 font-weight-bold">A</span>
                                                <input type="hidden" name="theme_font_size" id="input-font-size"
                                                    value="<?= $config['theme_font_size'] ?? '0.85rem' ?>">
                                                <span class="badge badge-primary ml-4 px-3 py-2" id="label-font-size"
                                                    style="width: 70px; font-size: 0.9rem;"><?= $config['theme_font_size'] ?? '0.85rem' ?></span>
                                            </div>
                                            <p class="small text-muted mt-2 mb-0">Geser untuk mengubah skala seluruh
                                                elemen aplikasi secara proporsional. (Standard: 0.85rem)</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- KOLOM KIRI: HEADERS -->
                                        <div class="col-md-6 border-right pr-md-4">
                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Judul Halaman (H1)</span>
                                                    <span class="badge badge-info"
                                                        id="label-font-header"><?= $config['theme_font_header'] ?? '1.5rem' ?></span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="range" class="custom-range mr-3" min="1" max="2.5"
                                                        step="0.05"
                                                        value="<?= str_replace('rem', '', ($config['theme_font_header'] ?? '1.5rem')) ?>"
                                                        oninput="updateFontRange(this, 'label-font-header', 'input-font-header')">
                                                    <input type="color" name="theme_color_header" class="ml-2"
                                                        value="<?= $config['theme_color_header'] ?? '#3b82f6' ?>"
                                                        oninput="updateLiveColor(this, 'pv-h1')">
                                                </div>
                                                <input type="hidden" name="theme_font_header" id="input-font-header"
                                                    value="<?= $config['theme_font_header'] ?? '1.5rem' ?>">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Sub Judul / Card Title</span>
                                                    <span class="badge badge-info"
                                                        id="label-font-subtitle"><?= $config['theme_font_subtitle'] ?? '1.1rem' ?></span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="range" class="custom-range mr-3" min="0.8" max="1.8"
                                                        step="0.05"
                                                        value="<?= str_replace('rem', '', ($config['theme_font_subtitle'] ?? '1.1rem')) ?>"
                                                        oninput="updateFontRange(this, 'label-font-subtitle', 'input-font-subtitle')">
                                                    <input type="color" name="theme_color_subtitle" class="ml-2"
                                                        value="<?= $config['theme_color_subtitle'] ?? '#1e293b' ?>"
                                                        oninput="updateLiveColor(this, 'pv-subtitle')">
                                                </div>
                                                <input type="hidden" name="theme_font_subtitle" id="input-font-subtitle"
                                                    value="<?= $config['theme_font_subtitle'] ?? '1.1rem' ?>">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Teks Header Tabel</span>
                                                    <span class="badge badge-info"
                                                        id="label-font-table-header"><?= $config['theme_font_table_header'] ?? '0.875rem' ?></span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="range" class="custom-range mr-3" min="0.7" max="1.2"
                                                        step="0.025"
                                                        value="<?= str_replace('rem', '', ($config['theme_font_table_header'] ?? '0.875rem')) ?>"
                                                        oninput="updateFontRange(this, 'label-font-table-header', 'input-font-table-header')">
                                                    <input type="color" name="theme_color_table_header" class="ml-2"
                                                        value="<?= $config['theme_color_table_header'] ?? '#475569' ?>"
                                                        oninput="updateLiveColor(this, 'pv-thead')">
                                                </div>
                                                <input type="hidden" name="theme_font_table_header"
                                                    id="input-font-table-header"
                                                    value="<?= $config['theme_font_table_header'] ?? '0.875rem' ?>">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Teks Konten Tabel</span>
                                                    <span class="badge badge-info"
                                                        id="label-font-table-content"><?= $config['theme_font_table_content'] ?? '0.85rem' ?></span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="range" class="custom-range mr-3" min="0.7" max="1.2"
                                                        step="0.025"
                                                        value="<?= str_replace('rem', '', ($config['theme_font_table_content'] ?? '0.85rem')) ?>"
                                                        oninput="updateFontRange(this, 'label-font-table-content', 'input-font-table-content')">
                                                    <input type="color" name="theme_color_table_content" class="ml-2"
                                                        value="<?= $config['theme_color_table_content'] ?? '#334155' ?>"
                                                        oninput="updateLiveColor(this, 'pv-td')">
                                                </div>
                                                <input type="hidden" name="theme_font_table_content"
                                                    id="input-font-table-content"
                                                    value="<?= $config['theme_font_table_content'] ?? '0.85rem' ?>">
                                            </div>
                                        </div>

                                        <!-- KOLOM KANAN: BODY & SMALL -->
                                        <div class="col-md-6 pl-md-4">
                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Teks Utama (Body)</span>
                                                    <span class="badge badge-info"
                                                        id="label-font-body"><?= $config['theme_font_body'] ?? '1rem' ?></span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="range" class="custom-range mr-3" min="0.75" max="1.25"
                                                        step="0.025"
                                                        value="<?= str_replace('rem', '', ($config['theme_font_body'] ?? '1rem')) ?>"
                                                        oninput="updateFontRange(this, 'label-font-body', 'input-font-body')">
                                                    <input type="color" name="theme_color_body" class="ml-2"
                                                        value="<?= $config['theme_color_body'] ?? '#334155' ?>"
                                                        oninput="updateLiveColor(this, 'pv-body')">
                                                </div>
                                                <input type="hidden" name="theme_font_body" id="input-font-body"
                                                    value="<?= $config['theme_font_body'] ?? '1rem' ?>">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Teks Catatan / Info Field</span>
                                                    <span class="badge badge-info"
                                                        id="label-font-small"><?= $config['theme_font_small'] ?? '0.8rem' ?></span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="range" class="custom-range mr-3" min="0.6" max="1.1"
                                                        step="0.025"
                                                        value="<?= str_replace('rem', '', ($config['theme_font_small'] ?? '0.8rem')) ?>"
                                                        oninput="updateFontRange(this, 'label-font-small', 'input-font-small')">
                                                    <input type="color" name="theme_color_small" class="ml-2"
                                                        value="<?= $config['theme_color_small'] ?? '#64748b' ?>"
                                                        oninput="updateLiveColor(this, 'pv-small')">
                                                </div>
                                                <input type="hidden" name="theme_font_small" id="input-font-small"
                                                    value="<?= $config['theme_font_small'] ?? '0.8rem' ?>">
                                            </div>

                                            <div class="form-group mb-4">
                                                <label class="d-flex justify-content-between">
                                                    <span>Warna Teks Sidebar</span>
                                                    <span class="small font-weight-bold text-muted">Fixed Size?
                                                        No!</span>
                                                </label>
                                                <div class="d-flex align-items-center">
                                                    <input type="color" name="theme_color_sidebar_text"
                                                        class="form-control form-control-sm mr-2" style="width: 50px;"
                                                        value="<?= $config['theme_color_sidebar_text'] ?? '#cbd5e1' ?>">
                                                    <small class="text-muted">Mempengaruhi seluruh label menu di
                                                        sidebar.</small>
                                                </div>
                                            </div>

                                            <div class="alert alert-warning py-2 small mt-2">
                                                <i class="fas fa-exclamation-triangle mr-2"></i> Ukuran font sidebar
                                                otomatis mengikuti skala <b>Teks Catatan / Small</b>.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PREVIEW SECTION -->
                                    <div class="mt-5 p-4 border rounded-lg bg-white shadow-sm"
                                        style="border: 2px dashed #dee2e6 !important;">
                                        <p
                                            class="text-primary mb-3 small font-weight-bold text-uppercase letter-spacing-1">
                                            <i class="fas fa-eye mr-2"></i>Live Preview (Simulasi):</p>

                                        <div class="preview-box p-3 border rounded bg-light">
                                            <h1 class="mb-1" id="pv-h1"
                                                style="font-size: <?= $config['theme_font_header'] ?? '1.5rem' ?> !important; color: <?= $config['theme_color_header'] ?? '#3b82f6' ?>; font-weight: 700;">
                                                Contoh Judul H1</h1>
                                            <p class="text-muted mb-4 small">Navigasi / Sub-folder / Halaman</p>

                                            <div class="card mb-3">
                                                <div class="card-header bg-white">
                                                    <h5 class="card-title mb-0" id="pv-subtitle"
                                                        style="font-size: <?= $config['theme_font_subtitle'] ?? '1.1rem' ?> !important; color: <?= $config['theme_color_subtitle'] ?? '#1e293b' ?>; font-weight: 600;">
                                                        <i class="fas fa-list mr-2"></i> Contoh Sub Judul / Card Title
                                                    </h5>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-sm mb-0">
                                                        <thead id="pv-thead"
                                                            style="background: <?= $config['theme_table_header_bg'] ?? '#f4f6f9' ?>; color: <?= $config['theme_color_table_header'] ?? '#475569' ?>;">
                                                            <tr>
                                                                <th
                                                                    style="font-size: <?= $config['theme_font_table_header'] ?? '0.875rem' ?> !important; color: inherit;">
                                                                    NO</th>
                                                                <th
                                                                    style="font-size: <?= $config['theme_font_table_header'] ?? '0.875rem' ?> !important; color: inherit;">
                                                                    NAMA SISWA</th>
                                                                <th
                                                                    style="font-size: <?= $config['theme_font_table_header'] ?? '0.875rem' ?> !important; color: inherit;">
                                                                    STATUS</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td id="pv-td1"
                                                                    style="font-size: <?= $config['theme_font_table_content'] ?? '0.85rem' ?> !important; color: <?= $config['theme_color_table_content'] ?? '#334155' ?>;">
                                                                    1</td>
                                                                <td id="pv-td2"
                                                                    style="font-size: <?= $config['theme_font_table_content'] ?? '0.85rem' ?> !important; color: <?= $config['theme_color_table_content'] ?? '#334155' ?>;">
                                                                    ABDUL AZIZ</td>
                                                                <td><span class="badge badge-success"
                                                                        style="font-size: 0.75rem;">Aktif</span></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <p class="mb-2" id="pv-body"
                                                style="font-size: <?= $config['theme_font_body'] ?? '1rem' ?> !important; color: <?= $config['theme_color_body'] ?? '#334155' ?>;">
                                                Ini adalah teks body utama yang Bapak baca sekarang.</p>
                                            <small class="text-muted d-block" id="pv-small"
                                                style="font-size: <?= $config['theme_font_small'] ?? '0.8rem' ?> !important; color: <?= $config['theme_color_small'] ?? '#64748b' ?>;">*
                                                Catatan: Ini adalah teks kecil informasi lapangan.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 p-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill">
                        <i class="fas fa-save mr-2"></i> Terapkan & Simpan Perubahan
                    </button>
                    <div class="mt-3">
                        <button type="button" onclick="resetTheme()" class="btn btn-link text-muted btn-sm">
                            <i class="fas fa-undo mr-1"></i> Reset ke Pengaturan Awal
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    function selectSidebar(element) {
        // Remove selected class from all
        $('.sidebar-item').removeClass('selected').find('input').prop('checked', false);
        // Add to clicked
        $(element).addClass('selected').find('input').prop('checked', true);
    }

    function updateFontRange(input, labelId, inputId) {
        const val = input.value + 'rem';
        document.getElementById(labelId).innerText = val;
        document.getElementById(inputId).value = val;

        // Live Preview Mapping
        if (inputId === 'input-font-header') document.getElementById('pv-h1').style.fontSize = val;
        if (inputId === 'input-font-subtitle') document.getElementById('pv-subtitle').style.fontSize = val;
        if (inputId === 'input-font-table-header') {
            const ths = document.querySelectorAll('#pv-thead th');
            ths.forEach(th => th.style.fontSize = val);
        }
        if (inputId === 'input-font-table-content') {
            document.getElementById('pv-td1').style.fontSize = val;
            document.getElementById('pv-td2').style.fontSize = val;
        }
        if (inputId === 'input-font-body') document.getElementById('pv-body').style.fontSize = val;
        if (inputId === 'input-font-small') document.getElementById('pv-small').style.fontSize = val;
    }

    function updateLiveColor(input, targetId) {
        const color = input.value;

        // Update HEX label if exists
        if (targetId === 'pv-accent') document.getElementById('label-accent-color').innerText = color;
        if (targetId === 'pv-thead-bg') document.getElementById('label-table-header-bg').innerText = color;

        const target = document.getElementById(targetId === 'pv-thead-bg' ? 'pv-thead' : targetId);
        if (target) {
            if (targetId === 'pv-thead') {
                target.style.color = color;
            } else if (targetId === 'pv-thead-bg') {
                target.style.backgroundColor = color;
            } else if (targetId === 'pv-td') {
                const tds = document.querySelectorAll('#pv-td1, #pv-td2');
                tds.forEach(td => td.style.color = color);
            } else if (targetId === 'pv-accent') {
                // Accent color in preview could affect icons or borders or buttons
                // For now let's apply to the H1 as secondary influence if it's primary text
                document.getElementById('pv-h1').style.color = color;
            } else {
                target.style.color = color;
            }
        }
    }

    function resetTheme() {
        if (confirm('Apakah Bapak yakin ingin mengembalikan semua pengaturan tampilan ke standar awal? Semua kustomisasi warna dan ukuran font akan dihapus.')) {
            window.location.href = 'index.php?mod=app_config&act=reset';
        }
    }
</script>

<?php
$footer_path = __DIR__ . '/partials/footer.php';
if (file_exists($footer_path)) {
    include $footer_path;
} else {
    echo "<!-- ERROR: Footer not found at $footer_path -->";
}
?>