<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMAKS</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toast Notification CSS -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/notification.css">
    <!-- Filter Box CSS -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/filter-box.css">
    <!-- Flatpickr Time Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <?php
    // 1. Deteksi URL API secara otomatis (Global)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];
    $project_root = dirname(dirname($_SERVER['SCRIPT_NAME']));
    $api_url = $protocol . $domain . $project_root . '/api/api.php';
    ?>

    <?php
    // [THEME CONFIG LOAD]
    // Fetch theme settings from database or fallback to defaults
    $theme_config = [];
    if (file_exists(__DIR__ . '/../../models/AppConfigModel.php')) {
        require_once __DIR__ . '/../../models/AppConfigModel.php';
        if (isset($pdo)) {
            $theme_config = AppConfigModel::getAll($pdo);
        }
    }
    
    // Default Values
    // Default Values
    $t_font_size = $theme_config['theme_font_size'] ?? '0.8rem';
    $t_font_header = $theme_config['theme_font_header'] ?? '1.5rem';
    $t_font_body = $theme_config['theme_font_body'] ?? '1rem';
    $t_font_small = $theme_config['theme_font_small'] ?? '0.875rem';
    
    $t_accent = $theme_config['theme_accent_color'] ?? '#3b82f6';
    $t_sidebar = $theme_config['theme_sidebar_bg'] ?? 'midnight_blue';
    $t_body_bg = $theme_config['theme_body_bg'] ?? '#f8fafc';
    $t_navbar_bg = $theme_config['theme_navbar_bg'] ?? '#ffffff';
    $t_footer_bg = $theme_config['theme_footer_bg'] ?? '#ffffff';
    $t_table_header_bg = $theme_config['theme_table_header_bg'] ?? '#f8f9fa';
    
    // Sidebar Style Logic
    $sidebar_style_css = "";
    switch ($t_sidebar) {
        case 'glass_blue':
            $sidebar_style_css = "
                background-color: rgba(10, 35, 70, 0.85) !important;
                backdrop-filter: blur(10px) !important;
                -webkit-backdrop-filter: blur(10px) !important;
                box-shadow: 4px 0 15px rgba(20, 50, 100, 0.15) !important;
                border-right: 1px solid rgba(255, 255, 255, 0.08);";
            break;
        case 'royal_blue':
            $sidebar_style_css = "
                background: linear-gradient(180deg, rgba(20, 60, 120, 0.9) 0%, rgba(10, 30, 70, 0.9) 100%) !important;
                backdrop-filter: blur(8px) !important;
                -webkit-backdrop-filter: blur(8px) !important;
                box-shadow: 4px 0 15px rgba(30, 80, 150, 0.2) !important;
                border-right: 1px solid rgba(255, 255, 255, 0.1);";
            break;
        case 'slate_matte':
            $sidebar_style_css = "
                background: linear-gradient(180deg, #3f576c 0%, #2c3e50 100%) !important;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1) !important;
                border-right: none;";
            break;
        case 'midnight_blue':
        default:
            $sidebar_style_css = "
                background: linear-gradient(180deg, #020617 0%, #0f172a 100%) !important;
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2) !important;
                border-right: 1px solid rgba(255, 255, 255, 0.05);";
            break;
    }
    ?>

    <style>
        /* ========================================= */
        /* 🎨 MODERN THEME VARIABLES & RESET */
        /* ========================================= */
        :root {
            /* Core Palette (Matching Login Page) */
            --theme-primary: #0f172a;
            /* Navy - Sidebar Bg & Headings */
            --theme-primary-light: #1e293b;
            /* Lighter Navy - Hover states */
            --theme-accent: <?= $t_accent ?>;
            /* Bright Blue - Active links, Buttons */
            --theme-accent-hover: #2563eb;
            /* Darker Blue - Button hover */

            --theme-success: #10b981;
            /* Emerald Green */
            --theme-warning: #f59e0b;
            /* Amber */
            --theme-danger: #ef4444;
            /* Red */
            --theme-info: #06b6d4;
            /* Cyan */

            --body-bg: <?= $t_body_bg ?>;
            /* Slate 50 - Main background */
            --text-color: #334155;
            /* Slate 700 - Body text */
            --text-muted: #64748b;
            /* Slate 500 - Muted text */
            
            /* DYNAMIC THEME VARIABLES */
            --font-header: <?= $t_font_header ?>;
            --font-body: <?= $t_font_body ?>;
            --font-small: <?= $t_font_small ?>;
            
            --color-navbar: <?= $t_navbar_bg ?>;
            --color-footer: <?= $t_footer_bg ?>;
            --color-table-header: <?= $t_table_header_bg ?>;

            --border-radius-base: 10px;
            /* Rounded corners */
            --box-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --box-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            font-family: 'Poppins', sans-serif !important;
            font-size: var(--font-body) !important;
            color: var(--text-color);
            background-color: var(--body-bg);
        }

        /* Scaled down headers */
        h1, .h1 { font-size: var(--font-header) !important; }
        h2, .h2 { font-size: calc(var(--font-header) * 0.85) !important; }
        h3, .h3 { font-size: calc(var(--font-header) * 0.75) !important; }
        h4, .h4 { font-size: calc(var(--font-header) * 0.65) !important; }
        
        .text-sm, small, .small, .btn-sm, table, .table, .form-control {
            font-size: var(--font-small) !important;
        }
        
        /* Theme Application */
        .main-header { background-color: var(--color-navbar) !important; }
        .main-footer { background-color: var(--color-footer) !important; }
        .table thead th { background-color: var(--color-table-header) !important; }

        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Poppins', sans-serif !important;
            color: var(--theme-primary);
            font-weight: 600;
        }

        .content-wrapper {
            background-color: var(--body-bg) !important;
        }

        /* ========================================= */
        /* 🎨 SIDEBAR MODERNIZATION */
        /* ========================================= */
        /* ========================================= */
        /* 🎨 SIDEBAR MODERNIZATION */
        /* ========================================= */
        .main-sidebar {
            /* Glass/Transparent Dark Style */
            background-color: rgba(15, 23, 42, 0.85) !important; /* Semi-transparent dark */
            backdrop-filter: blur(12px) !important; /* Blur effect */
            -webkit-backdrop-filter: blur(12px) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08); /* Crisp glass edge */
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1) !important; /* Subtle separation shadow */
        }

        /* Brand Logo Area & Glow Effect */
        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            background-color: rgba(0, 0, 0, 0.1);
        }

        @keyframes logo-pulse {
            0% { filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.4)); transform: scale(1); }
            50% { filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.7)); transform: scale(1.05); }
            100% { filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.4)); transform: scale(1); }
        }

        .brand-image-custom {
            animation: logo-pulse 4s infinite ease-in-out;
            transition: all 0.3s ease;
        }

        .brand-text {
            color: #ffffff !important;
            font-weight: 700 !important;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
            letter-spacing: 1px;
        }

        .brand-link:hover .brand-image-custom {
            filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.9));
        }
        /* Sidebar Menu Items */
        .nav-sidebar .nav-item .nav-link,
        .nav-sidebar .nav-item .nav-link p {
            color: #cbd5e1 !important;
            /* Slate 300 */
            font-weight: 400;
            border-radius: var(--border-radius-base);
            font-size: 0.825rem !important; /* Adjusted back up for readability */
            margin-bottom: 2px;
        }

        .nav-sidebar .nav-item .nav-link {
            padding: 5px 10px; /* Tighter padding on link container */
        }

        /* Hover State */
        .nav-sidebar .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }

        /* Active State (Border Beam Effect) */
        .nav-sidebar .nav-item .nav-link.active {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            box-shadow: none !important;
            font-weight: 600;
            position: relative;
            z-index: 1;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        /* The Rotating Double Border Beam */
        .nav-sidebar .nav-item .nav-link.active::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 250%; /* Slightly larger for smoother overlap */
            height: 250%;
            background: conic-gradient(
                from 0deg, 
                transparent 0%, 
                var(--theme-accent) 15%, 
                #fff 25%, 
                transparent 50%, 
                var(--theme-accent) 65%, 
                #fff 75%, 
                transparent 100%
            );
            transform: translate(-50%, -50%);
            animation: rotate-beam 3s linear infinite; /* Slightly faster for more energy */
            z-index: -2;
        }

        /* Inner mask to show only the edge */
        .nav-sidebar .nav-item .nav-link.active::before {
            content: '';
            position: absolute;
            inset: 2px;
            background: #001F3F; /* Sidebar Bg Match */
            border-radius: inherit;
            z-index: -1;
        }

        @keyframes rotate-beam {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .nav-sidebar .nav-link.active .nav-icon {
            color: #ffffff !important;
            animation: none !important;
        }

        /* Nav Headers */
        .nav-header {
            color: #94a3b8 !important;
            /* Slate 400 */
            font-size: 0.75rem !important; /* Adjusted for balance */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
            padding: 0 1rem;
        }

        /* ========================================= */
        /* 🎨 UI COMPONENTS (Cards, Buttons, Inputs) */
        /* ========================================= */

        /* 1. CARDS / BOXES */
        .card {
            border: none !important;
            border-radius: var(--border-radius-base) !important;
            box-shadow: var(--box-shadow-sm) !important;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            /* Subtle lift on hover for interactive cards */
            box-shadow: var(--box-shadow-md) !important;
        }

        .card-header {
            background-color: #fff !important;
            border-bottom: 1px solid #f1f5f9;
            border-top-left-radius: var(--border-radius-base) !important;
            border-top-left-radius: var(--border-radius-base) !important;
            border-top-right-radius: var(--border-radius-base) !important;
            padding: 0.75rem 1rem; /* Compact padding */
        }

        .card-title {
            font-weight: 600;
            font-size: 0.85rem; /* Reduced card title size again */
            color: var(--theme-primary);
        }

        /* 2. BUTTONS */
        .btn {
            border-radius: 8px !important;
            /* Soft corners */
            font-weight: 500;
            padding: 8px 16px;
            box-shadow: none;
            transition: all 0.2s;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background-color: var(--theme-accent) !important;
            border-color: var(--theme-accent) !important;
        }

        .btn-primary:hover {
            background-color: var(--theme-accent-hover) !important;
            border-color: var(--theme-accent-hover) !important;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: var(--theme-success) !important;
            border-color: var(--theme-success) !important;
        }

        .btn-danger {
            background-color: var(--theme-danger) !important;
            border-color: var(--theme-danger) !important;
        }

        /* 3. FORM INPUTS */
        .form-control {
            border-radius: 8px !important;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--theme-accent) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }

        /* 4. TABLES */
        .table thead th {
            background-color: #f1f5f9;
            color: var(--theme-primary);
            font-weight: 600;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            /* Cleaner header */
            font-size: 0.8rem !important; /* Smaller table headers */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(248, 250, 252, 0.8);
        }

        /* 5. MODALS */
        .modal-content {
            border-radius: var(--border-radius-base);
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            border-top-left-radius: var(--border-radius-base);
            border-top-right-radius: var(--border-radius-base);
            background-color: var(--theme-primary);
            color: #fff;
        }

        .modal-title {
            color: #fff;
        }

        .modal-header .close {
            color: #fff;
            opacity: 0.8;
        }

        /* ========================================= */
        /* 🛠️ LAYOUT FIXES (REFINED) */
        /* ========================================= */

        /* 1. Sidebar Logo Fix (Vertical Stack) */
        .brand-link {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            padding: 1.5rem 0.5rem !important;
            height: auto !important;
        }

        /* Remove float interactions for centered layout */
        .brand-link .brand-image,
        .brand-link .brand-image-custom,
        .main-sidebar .brand-link img {
            float: none !important;
            margin-right: 0 !important;
            margin-bottom: 0.5rem !important;
            max-height: 50px !important;
            /* Increased size for vertical layout */
            width: auto !important;
        }

        /* 2. Dashboard Widgets & Shadows (Deep Depth) */
        :root {
            --box-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.08);
            --box-shadow-md: 0 6px 12px rgba(0, 0, 0, 0.12);
            --box-shadow-lg: 0 15px 25px rgba(0, 0, 0, 0.15);
        }

        .card,
        .small-box,
        .info-box {
            box-shadow: var(--box-shadow-md) !important;
            border: none !important;
            transition: box-shadow 0.3s ease;
        }

        .card:hover,
        .small-box:hover {
            box-shadow: var(--box-shadow-lg) !important;
            transform: translateY(-2px);
        }

        /* Text Contrast Fix */
        .small-box h3,
        .small-box p,
        .small-box .icon {
            color: #fff !important;
        }

        .bg-info,
        .bg-success,
        .bg-warning,
        .bg-danger,
        .bg-primary {
            color: #fff !important;
        }

        .info-banner,
        .info-banner h3,
        .info-banner p {
            color: #fff !important;
        }

        /* 3. Filter Fields & Inputs (Alignment Fix) */
        .filter-block {
            display: flex;
            align-items: flex-end;
            /* Align inputs to bottom */
            flex-wrap: wrap;
            gap: 15px;
            /* Spacing between items */
        }

        .filter-block .form-group {
            margin-bottom: 0 !important;
            /* Remove internal margin for alignment */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .filter-block .form-control-sm,
        .filter-block select.form-control-sm,
        .input-group-sm>.form-control {
            height: 32px !important;
            /* Standard height */
            padding: 4px 10px !important;
            font-size: 0.85rem !important;
            background-color: #fff !important;
            color: #333 !important;
            border: 1px solid rgba(0, 0, 0, 0.15) !important;
        }

        .filter-block label {
            color: #fff !important;
            margin-bottom: 6px;
            font-size: 0.85rem;
            line-height: 1.2;
        }

        /* 4. Buttons (Better Spacing) */
        .btn {
            margin-right: 5px;
            margin-bottom: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn:last-child {
            margin-right: 0;
        }

        /* 5. File Upload (Custom File) */
        .custom-file-label {
            height: auto !important;
            padding: 10px 15px !important;
            border-radius: 8px !important;
        }

        .custom-file-label::after {
            height: 100% !important;
            padding: 10px 15px !important;
            display: flex;
            align-items: center;
        }

        /* Input height global fix for alignment */
        .input-group-sm>.form-control,
        .input-group-sm>.input-group-append>.btn {
            height: 35px !important;
        }

        /* 🛠️ REFINED ALIGNMENT V2 (Override) */
        /* Force labels to have more bottom space ("sedikit ke atas") */
        .filter-block label,
        .form-group label,
        .card-body label {
            margin-bottom: 8px !important;
            display: inline-block;
            vertical-align: middle;
        }

        /* Ensure inputs are vertically centered if in flex container */
        .filter-block {
            align-items: center !important;
        }

        /* Enforce consistent input height */
        .form-control,
        .form-control-sm,
        select.form-control-sm,
        .custom-file-label {
            height: 38px !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
        }

        /* 🛠️ FORM & FILE UPLOAD FIXES V2 (Stronger Specificity) */
        /* Limit width of file upload input group */
        .card-tools .input-group-sm {
            max-width: 400px;
        }

        /* Fix custom file input - use more specific selectors */
        .card-tools .custom-file,
        .input-group .custom-file,
        .input-group-sm .custom-file {
            height: 38px !important;
            min-height: 38px !important;
            max-width: 220px;
            /* Limit width to reduce empty space */
            flex: 0 0 220px;
        }

        .card-tools .custom-file-label,
        .input-group .custom-file-label,
        .input-group-sm .custom-file-label {
            height: 38px !important;
            min-height: 38px !important;
            padding: 0.375rem 0.75rem !important;
            line-height: 1.5 !important;
            border-radius: 0.25rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            /* Right align text */
            text-align: right !important;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .card-tools .custom-file-label::after,
        .input-group .custom-file-label::after,
        .input-group-sm .custom-file-label::after {
            height: 38px !important;
            min-height: 38px !important;
            padding: 0.375rem 0.75rem !important;
            line-height: 1.5 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0.25rem !important;
        }

        /* Add gap between file input and import button */
        .card-tools .input-group-append {
            margin-left: 8px !important;
        }

        /* Input group append button alignment */
        .card-tools .input-group-append .btn,
        .input-group-sm .input-group-append .btn {
            height: 38px !important;
            min-height: 38px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.25rem !important;
        }

        /* All form controls consistent sizing */
        .form-control,
        select.form-control,
        input[type="text"].form-control,
        input[type="time"].form-control,
        input[type="date"].form-control,
        input[type="number"].form-control,
        input[type="email"].form-control,
        input[type="password"].form-control {
            min-height: 32px !important;
            height: 32px !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        textarea.form-control {
            min-height: 38px !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Card tools and form inline alignment */
        .card-tools .form-inline {
            display: flex !important;
            align-items: center !important;
            gap: 0;
        }

        .card-tools .input-group {
            display: flex !important;
            align-items: center !important;
        }
        /* ========================================= */
        /* 📱 MOBILE OPTIMIZATIONS (SUPER APP UI) */
        /* ========================================= */
        @media (max-width: 768px) {
            :root {
                --mobile-header-bg: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            }

            /* Hide Sidebar on Mobile (Use Burger Toggle) */
            .main-sidebar {
                display: none !important;
            }
            
            /* Restore Navbar on Mobile */
            .main-header {
                display: flex !important;
            }
            
            /* Hide Sidebar Toggle on Mobile */
            [data-widget="pushmenu"] { display: none !important; }

            /* Reset Content Padding */
            .content-wrapper {
                margin-left: 0 !important;
                padding-top: 50px !important; /* Matches navbar height exactly on mobile */
                padding-bottom: 80px !important; 
                background: #f8fafc !important;
            }

            
            /* Adjust Text Sizes */
            h1, .h1 { font-size: 1.4rem !important; }
            body { font-size: 0.85rem !important; }
            
            /* Hide Footer Credits */
            .main-footer { display: none !important; }

            /* Modal Bottom Drawer Style for Menu */
            .icon-grid-drawer .modal-content {
                border-radius: 0 !important;
                border: none !important;
                top: 0 !important;
                bottom: 70px !important;
                position: fixed;
                width: 100%;
                max-width: 100%;
                margin: 0;
                height: calc(100vh - 70px) !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important; /* Fixed header/footer support */
            }
            
            .icon-grid-drawer .modal-dialog {
                margin: 0;
                max-width: 100%;
                display: flex;
                align-items: flex-start; /* Align to top */
                height: 100vh;
                padding-bottom: 70px !important;
            }

            .icon-grid-drawer .modal-body {
                overflow-y: auto;
                flex: 1;
            }

            /* Ensure Backdrop is behind Bottom Nav */
            .modal-backdrop {
                z-index: 1030 !important;
            }
            .icon-grid-drawer {
                z-index: 1035 !important;
            }
            .mobile-bottom-nav {
                z-index: 1040 !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php include __DIR__ . '/navbar.php'; ?>
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="content-wrapper">

            <?php if (isset($_SESSION['pesan_error_global'])): ?>
                <div class="alert alert-danger m-3 alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <?= $_SESSION['pesan_error_global']; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['pesan_error_global']); ?>
            <?php endif; ?>