<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .chat-wrapper {
        position: fixed !important;
        top: 80px !important;
        left: 310px !important; /* Extremely large gap to ensure visibility */
        right: 30px !important;
        bottom: 80px !important; /* Extremely large gap from footer */
        height: auto !important;
        width: auto !important;
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 20px !important;
        display: flex !important;
        overflow: hidden !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        z-index: 1030 !important;
        transition: all 0.3s ease !important;
    }

    /* Adjust left position when main sidebar is collapsed */
    body.sidebar-collapse .chat-wrapper {
        left: 110px !important;
    }

    @media (max-width: 991px) {
        .chat-wrapper {
            position: fixed !important;
            top: 60px !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 65px !important;
            width: auto !important;
            height: auto !important;
            margin: 0 !important;
            border-radius: 0 !important;
            border: none !important;
            z-index: 1000 !important;
            display: flex !important;
            box-shadow: none !important;
        }
        
        .chat-sidebar {
            width: 100% !important;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 100;
            height: 100%;
            transition: all 0.3s ease;
            background: #fff;
        }
        .chat-sidebar.collapsed {
            display: none;
        }
        .chat-main {
            width: 100%;
            height: 100%;
        }
        #btn-back {
            display: block !important;
        }
        .chat-footer {
            padding: 8px 10px !important;
            gap: 6px !important;
        }
        .btn-icon {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
        }
        .btn-action {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
        }
        .input-pill {
            padding: 7px 12px !important;
            font-size: 0.82rem !important;
            margin: 0 !important;
        }
        .chat-messages-scroll {
            padding: 15px;
        }
        .msg-group {
            max-width: 85%;
        }
        .sidebar-header {
            padding: 15px;
        }
        .filter-panel {
            padding: 10px;
        }
        #footer-recording {
            padding: 5px 10px !important;
            font-size: 0.75rem;
        }
        #footer-recording .ml-3 {
            margin-left: 2px !important;
        }
    }

    /* Hide only the LEFT sidebar on mobile when in chat page */
    @media (max-width: 991px) {
        body.chat-page-mobile:not(.sidebar-open) .main-sidebar {
            display: none !important;
        }
        body.chat-page-mobile .content-wrapper {
            margin-left: 0 !important;
        }
        body.chat-page-mobile .chat-wrapper {
            top: 57px !important;    /* Below top navbar */
            left: 0 !important;
            right: 0 !important;
            bottom: 65px !important; /* Above bottom nav */
            border-radius: 0 !important;
            z-index: 1000 !important;
        }
    }

    /* Sidebar Styling */
    .chat-sidebar {
        width: 380px;
        border-right: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.5);
    }

    .sidebar-header {
        padding: 25px 20px;
        background: #fff;
    }

    .sidebar-header h4 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
    }

    .nav-tabs-custom {
        display: flex;
        padding: 0 20px;
        gap: 20px;
        border-bottom: 1px solid #eee;
        background: #fff;
    }

    .nav-tab-item {
        padding: 12px 5px;
        font-weight: 600;
        cursor: pointer;
        color: #64748b;
        position: relative;
        transition: all 0.3s;
    }

    .nav-tab-item.active {
        color: #6366f1;
    }

    .nav-tab-item.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #6366f1;
        border-radius: 3px 3px 0 0;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
    }

    /* Message Item Styling */
    .chat-item {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        transition: 0.2s;
        border-left: 4px solid transparent;
    }

    .chat-item:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .chat-item.active {
        background: rgba(99, 102, 241, 0.1);
        border-left-color: #6366f1;
    }

    .avatar-wrapper {
        position: relative;
    }

    .avatar-img {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        object-fit: cover;
        background: #e2e8f0;
    }

    .status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 3px solid #fff;
        background: #94a3b8;
    }

    .status-dot.online {
        background: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }

    .chat-info {
        flex: 1;
        min-width: 0;
    }

    .chat-name-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .chat-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .chat-time {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .chat-preview {
        font-size: 0.85rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Chat Area Styling */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .chat-active-header {
        padding: 15px 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .chat-messages-scroll {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .msg-group {
        display: flex;
        flex-direction: column;
        max-width: 70%;
    }

    .msg-group.sent {
        align-self: flex-end;
        align-items: flex-end;
    }

    .msg-group.received {
        align-self: flex-start;
        align-items: flex-start;
    }

    .msg-group.received .msg-meta {
        align-self: flex-start;
    }

    /* Preview Box */
    .chat-preview-box {
        padding: 10px 20px;
        background: #f1f5f9;
        border-top: 1px solid #e2e8f0;
        display: none;
        align-items: center;
        gap: 15px;
    }

    .preview-thumb {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #fff;
    }

    .preview-info {
        flex: 1;
        font-size: 0.85rem;
        color: #475569;
    }

    .btn-remove-preview {
        color: #ef4444;
        cursor: pointer;
        padding: 5px;
    }

    /* Modal Preview Styles */
    #modal-preview-send .modal-content {
        background: #fff;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    #modal-preview-img {
        border: 4px solid #fff;
    }
    
    #modal-preview-file {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
    }

    .input-pill {
        border-radius: 25px !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
    }

    .recording-active {
        background: #fee2e2 !important;
    }

    .blink {
        animation: blink-animation 1s steps(5, start) infinite;
    }

    @keyframes blink-animation {
        to { visibility: hidden; }
    }

    .msg-bubble {
        padding: 12px 18px;
        border-radius: 18px;
        font-size: 0.92rem;
        line-height: 1.5;
        position: relative;
    }

    .msg-group.sent .msg-bubble {
        background: #6366f1;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-group.received .msg-bubble {
        background: #fff;
        color: #334155;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .msg-meta {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 4px;
    }

    .chat-footer {
        padding: 8px 12px;
        background: #fff;
        border-top: 1px solid #eee;
        display: flex;
        align-items: center;
        position: relative;
        box-sizing: border-box !important;
        width: 100% !important;
        overflow: visible !important;
    }

    .attach-menu {
        position: absolute;
        bottom: 70px;
        left: 15px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 1000;
        min-width: 150px;
    }

    .attach-menu-item {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: 0.2s;
        color: #475569;
        font-size: 0.9rem;
    }

    .attach-menu-item:hover {
        background: #f1f5f9;
        color: #6366f1;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        max-width: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        flex-shrink: 0;
        padding: 0;
    }

    .btn-icon:hover {
        background: #e2e8f0;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        min-width: 40px;
        max-width: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        flex-shrink: 0;
        padding: 0;
        transition: 0.2s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-action i {
        font-size: 1rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-send-chat {
        background: #6366f1;
        color: #fff;
    }

    .btn-mic-chat {
        background: #ef4444;
        color: #fff;
    }

    .btn-send-chat:hover {
        background: #4f46e5;
        transform: translateY(-2px);
    }

    .input-pill {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 20px !important;
        padding: 8px 16px !important;
        font-size: 0.88rem !important;
        outline: none !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease;
    }

    .input-pill:focus {
        background: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }

    /* Filter UI */
    .filter-panel {
        padding: 15px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #eee;
    }

    .unread-count {
        background: #ef4444;
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .chat-sidebar {
            width: 100% !important;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 100;
            height: 100%;
            transition: all 0.3s ease;
            background: #fff;
        }
        .chat-sidebar.collapsed {
            display: none;
        }
        .chat-main {
            width: 100%;
            height: 100%;
        }
        #btn-back {
            display: block !important;
        }
        .chat-wrapper {
            margin: 0;
            height: calc(100vh - 125px); /* Adjusted for Navbar (top) and Bottom Nav (bottom) */
            border-radius: 0;
            border: none;
        }
        .chat-footer {
            padding: 8px 12px;
            gap: 8px;
        }
        .btn-icon, .btn-action {
            width: 38px;
            height: 38px;
            min-width: 38px;
        }
        .input-pill {
            padding: 8px 15px;
            font-size: 0.85rem;
            margin: 0;
        }
        .chat-messages-scroll {
            padding: 15px;
        }
        .msg-group {
            max-width: 85%;
        }
        .sidebar-header {
            padding: 15px;
        }
        .filter-panel {
            padding: 10px;
        }
        #footer-recording {
            padding: 5px 10px !important;
            font-size: 0.75rem;
        }
        #footer-recording .ml-3 {
            margin-left: 2px !important;
        }
    }

    #btn-back {
        display: none;
        margin-right: 10px;
        font-size: 1.2rem;
        color: #64748b;
        cursor: pointer;
    }

    /* === 3-dot Dropdown Menu on Message Bubble === */
    .msg-bubble {
        position: relative;
    }
    .msg-dot-menu {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        opacity: 0.5;
        transition: opacity 0.2s;
        z-index: 5;
    }
    .msg-group:hover .msg-dot-menu,
    .msg-dot-menu.active {
        opacity: 0.8;
    }
    .msg-dot-menu:hover, .msg-dot-menu.active {
        opacity: 1;
        background: rgba(0,0,0,0.08);
    }
    .msg-group.sent .msg-dot-menu {
        color: rgba(255,255,255,0.8);
    }
    .msg-group.sent .msg-dot-menu:hover,
    .msg-group.sent .msg-dot-menu.active {
        background: rgba(255,255,255,0.15);
        color: #fff;
    }

    /* Dropdown that appears on click */
    .msg-dropdown {
        position: absolute;
        top: 28px;
        right: 0;
        min-width: 150px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 100;
    }
    .msg-dropdown.show { display: flex; }
    .msg-group.sent .msg-dropdown {
        right: 0; left: auto;
    }
    .msg-group.received .msg-dropdown {
        left: 0; right: auto;
    }
    .msg-dropdown-item {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: 0.15s;
        font-size: 0.85rem;
        color: #334155;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }
    .msg-dropdown-item:hover {
        background: #f1f5f9;
    }
    .msg-dropdown-item.text-danger:hover {
        background: #fef2f2;
    }
    .msg-dropdown-item i {
        width: 16px;
        text-align: center;
    }

    /* === Deleted Message Placeholder === */
    .msg-deleted {
        font-style: italic;
        color: #94a3b8;
        font-size: 0.85rem;
    }
    .msg-group.sent .msg-deleted {
        color: rgba(255,255,255,0.6);
    }

    /* === Forward Badge === */
    .fwd-badge {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .msg-group.sent .fwd-badge {
        color: rgba(255,255,255,0.6);
    }

    /* === Forward Modal === */
    #modal-forward .modal-body { max-height: 400px; overflow-y: auto; }
    .chat-item {
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: 0.2s;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }
    .chat-item:hover { background: #f8fafc; }
    .chat-item.active { background: #eff6ff; border-left: 3px solid #3b82f6; }
    
    .chat-item-delete {
        position: absolute;
        right: 15px;
        bottom: 15px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        opacity: 0;
        transition: 0.2s;
        z-index: 5;
    }
    .chat-item:hover .chat-item-delete {
        opacity: 1;
    }
    .chat-item-delete:hover {
        background: #fee2e2;
        color: #ef4444;
    }
    .fwd-contact-item {
        padding: 12px 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }
    .fwd-contact-item:hover { background: #f8fafc; }
    .fwd-contact-item img {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        object-fit: cover;
        background: #e2e8f0;
    }
    .fwd-contact-name { font-weight: 600; font-size: 0.9rem; color: #1e293b; }
    .fwd-contact-sub { font-size: 0.78rem; color: #94a3b8; }

    /* === Linkify Styling === */
    .msg-bubble a {
        color: #3b82f6;
        text-decoration: underline;
        word-break: break-all;
    }
    .msg-group.sent .msg-bubble a {
        color: #fff;
        text-decoration: underline;
    }

    /* === Pending Message Style === */
    .msg-group.pending-msg {
        opacity: 0.6;
    }
    .msg-group.pending-msg .msg-bubble {
        border: 1px dashed rgba(255,255,255,0.3);
    }
    .pending-status {
        font-size: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        margin-top: 4px;
        font-style: italic;
    }
</style>

<div class="content-wrapper" style="background: #f1f5f9;">
    <div class="chat-wrapper">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h4 class="mb-3 font-weight-bold">Internal Chat</h4>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0" style="border-radius: 10px 0 0 10px;"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="global-search" class="form-control border-left-0" style="border-radius: 0 10px 10px 0;" placeholder="Cari orang...">
                </div>
            </div>

            <div class="nav-tabs-custom">
                <div class="nav-tab-item active" id="tab-pesan" onclick="switchTab('pesan')">Pesan</div>
                <div class="nav-tab-item" id="tab-teman" onclick="switchTab('teman')">Teman</div>
            </div>

            <!-- Contextual Filter (Visible only in 'Teman' tab) -->
            <div class="filter-panel d-none" id="contact-filters">
                <div class="row no-gutters gap-2">
                    <div class="col">
                        <select id="filter-role" class="form-control form-control-sm">
                            <option value="">Semua Peran</option>
                            <?php foreach ($peranList as $r): ?>
                                <option value="<?= $r['id_peran'] ?>"><?= $r['nama_peran'] ?></option>
                            <?php endforeach; ?>
                            <option value="group">Grup Kelas</option>
                        </select>
                    </div>
                    <div class="col" id="col-kelas">
                        <select id="filter-kelas" class="form-control form-control-sm">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="sidebar-content" id="sidebar-list">
                <!-- Content will be injected by JS -->
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main" id="chat-welcome">
            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5">
                <div class="bg-light rounded-circle p-4 mb-4">
                    <i class="fas fa-comments fa-4x text-muted"></i>
                </div>
                <h3>Pilih Teman Chat</h3>
                <p class="text-muted">Gunakan tab <strong>Teman</strong> untuk mencari guru atau staf lainnya dan mulailah berkolaborasi.</p>
            </div>
        </div>

        <div id="chat-active" class="chat-main d-none h-100 flex-column">
            <div class="chat-active-header">
                <div id="btn-back" onclick="toggleSidebar(true)">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <div class="avatar-wrapper">
                    <img id="active-avatar" src="assets/img/user-default.png" class="avatar-img" style="width:40px; height:40px; border-radius:10px;">
                    <div id="active-status" class="status-dot"></div>
                </div>
                <div>
                    <div class="font-weight-bold" id="active-name">--</div>
                    <div class="text-xs text-muted" id="active-subtitle">--</div>
                </div>
                
                <!-- Chat Header Options -->
                <div class="ml-auto position-relative">
                    <button class="btn-icon" onclick="toggleHeaderMenu(event)" title="Opsi Chat">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="msg-dropdown" id="header-menu" style="top: 45px; right: 0;">
                        <button class="msg-dropdown-item text-danger" onclick="confirmClearChat()">
                            <i class="fas fa-eraser"></i> Bersihkan Chat
                        </button>
                    </div>
                </div>
            </div>

            <div class="chat-messages-scroll" id="msg-container">
                <!-- Messages JS -->
            </div>

            <!-- Modal moved below -->

            <div class="chat-footer">
                <!-- Attachment Menu -->
                <div class="attach-menu" id="attach-menu">
                    <div class="attach-menu-item" onclick="$('#chat-camera').click(); hideAttachMenu();">
                        <i class="fas fa-camera text-primary"></i> Kamera
                    </div>
                    <div class="attach-menu-item" onclick="$('#chat-file').click(); hideAttachMenu();">
                        <i class="fas fa-file-image text-success"></i> Galeri / Foto
                    </div>
                    <div class="attach-menu-item" onclick="$('#chat-file').click(); hideAttachMenu();">
                        <i class="fas fa-file-video text-danger"></i> Video
                    </div>
                    <div class="attach-menu-item" onclick="$('#chat-file').click(); hideAttachMenu();">
                        <i class="fas fa-file-alt text-warning"></i> Dokumen
                    </div>
                </div>

                <div id="footer-normal" class="d-flex align-items-center w-100" style="gap: 8px;">
                    <button class="btn-icon" onclick="toggleAttachMenu()" title="Lampiran" style="flex-shrink: 0;">
                        <i class="fas fa-plus"></i>
                    </button>
                    
                    <input type="file" id="chat-file" class="d-none">
                    <input type="file" id="chat-camera" class="d-none" accept="image/*" capture="camera">
                    
                    <input type="text" id="chat-message-input" class="input-pill flex-grow-1" placeholder="Tulis pesan..." onkeyup="toggleActionButtons()">
                    
                    <div id="action-buttons" style="flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <button class="btn-action btn-send-chat d-none" id="btn-send" onclick="sendMessage()" title="Kirim">
                            <i class="fas fa-paper-plane" id="icon-send"></i>
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-send" role="status" aria-hidden="true"></span>
                        </button>
                        <button class="btn-action btn-mic-chat" id="btn-mic" onclick="startRecording()" title="Pesan Suara">
                            <i class="fas fa-microphone"></i>
                        </button>
                    </div>
                </div>
                
                <div id="footer-recording" class="d-none align-items-center justify-content-between w-100 p-2 recording-active" style="border-radius: 25px;">
                    <div class="ml-3 text-danger font-weight-bold d-flex align-items-center">
                        <i class="fas fa-circle blink mr-2"></i> <span class="d-none d-sm-inline">RECORDING</span> <span id="rec-timer" class="ml-1">00:00</span>
                    </div>
                    <div class="d-flex gap-2 mr-2">
                        <button class="btn btn-sm btn-light rounded-circle shadow-sm text-muted" onclick="stopRecording(false)" style="width:34px; height:34px;">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn btn-sm btn-danger rounded-circle shadow-sm" onclick="stopRecording(true)" style="width:34px; height:34px;">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forward Contact Picker Modal -->
<div class="modal fade" id="modal-forward" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 2001 !important;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-share mr-2 text-primary"></i>Teruskan Pesan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <input type="text" id="fwd-search" class="form-control input-pill" placeholder="Cari kontak..." onkeyup="searchForwardContacts()">
                </div>
                <div id="fwd-contact-list">
                    <div class="text-center p-4 text-muted small">Ketik nama untuk mencari kontak...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pre-send Preview Modal (WA Style) - Placed outside for proper backdrop -->
<div class="modal fade" id="modal-preview-send" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 2001 !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold">Kirim Media</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="modal-preview-container">
                    <img id="modal-preview-img" class="d-none shadow-sm" style="max-width:100%; max-height:300px; border-radius:15px; object-fit: contain;">
                    <div id="modal-preview-file" class="p-4 bg-light rounded-lg d-none">
                        <i class="fas fa-file-alt fa-4x text-primary mb-3"></i>
                        <div id="modal-preview-filename" class="font-weight-bold text-dark"></div>
                        <div id="modal-preview-filesize" class="text-xs text-muted mt-1"></div>
                    </div>
                    <div id="modal-preview-audio" class="p-4 bg-light rounded-lg d-none">
                        <i class="fas fa-microphone fa-4x text-danger mb-3"></i>
                        <div class="font-weight-bold mb-3">Pesan Suara</div>
                        <audio id="audio-playback" controls class="w-100"></audio>
                    </div>
                    <video id="modal-preview-video" controls class="d-none shadow-sm" style="max-width:100%; max-height:300px; border-radius:15px;"></video>
                </div>
                <input type="text" id="modal-preview-caption" class="form-control mt-4 input-pill" placeholder="Tambahkan keterangan (opsional)..." style="height: 45px !important;">
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 12px;">Batal</button>
                <button type="button" class="btn btn-primary px-5" onclick="sendMediaWithCaption()" style="border-radius: 12px;">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Chat Confirmation Modal -->
<div class="modal fade" id="modal-clear-chat" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 2001 !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-eraser mr-2"></i>Bersihkan Chat</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <p>Anda yakin ingin menghapus seluruh riwayat percakapan dengan <strong><span id="clear-target-name"></span></strong>?</p>
                <div class="alert alert-warning small">Tindakan ini tidak dapat dibatalkan.</div>
            </div>
            <div class="modal-footer flex-column border-0 p-3">
                <button type="button" class="btn btn-danger btn-block mb-2" onclick="executeClearChat(1)" style="border-radius: 12px;">
                    <i class="fas fa-users mr-2"></i> Hapus untuk Semua Orang
                </button>
                <button type="button" class="btn btn-outline-danger btn-block mb-2" onclick="executeClearChat(0)" style="border-radius: 12px;">
                    <i class="fas fa-user mr-2"></i> Hapus untuk Saya Saja
                </button>
                <button type="button" class="btn btn-light btn-block" data-dismiss="modal" style="border-radius: 12px;">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentTab = 'pesan';
    let activeUserId = null;
    let activeIsGroup = 0;
    let pollInterval = null;
    let lastMessageId = 0;
    let mediaRecorder;
    let audioChunks = [];
    let recInterval;
    let currentAudioBlob = null;
    let allMessages = [];
    let pendingMessages = [];

    const soundSent = new Audio('https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3');
    const soundReceived = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');

    $(document).ready(function() {
        if ($(window).width() <= 991) {
            $('body').addClass('chat-page-mobile');
        }

        // Direct tab click handlers
        $('#tab-pesan').on('click', function(e) {
            e.preventDefault();
            switchTab('pesan');
        });
        $('#tab-teman').on('click', function(e) {
            e.preventDefault();
            switchTab('teman');
        });

        // Search & Filter Listeners
        $('#global-search, #filter-role, #filter-kelas').on('input change keyup', function() {
            if (currentTab === 'teman') {
                searchContacts();
            } else {
                renderRecent();
            }
        });

        // Camera & file select logic
        $('#chat-camera').on('change', handleFileSelect);
        $('#chat-file').on('change', handleFileSelect);

        $('#chat-message-input').on('keydown', function(e) {
            if (e.which === 13) sendMessage();
        });

        $('#modal-preview-send').on('hidden.bs.modal', function () {
            clearFileSelection();
        });

        // Initial load
        loadSidebar();
    });

    function toggleAttachMenu() {
        $('#attach-menu').fadeToggle(200);
    }

    function hideAttachMenu() {
        $('#attach-menu').fadeOut(200);
    }

    function toggleActionButtons() {
        let text = $('#chat-message-input').val().trim();
        if (text.length > 0) {
            $('#btn-send').removeClass('d-none');
            $('#btn-mic').addClass('d-none');
        } else {
            $('#btn-send').addClass('d-none');
            $('#btn-mic').removeClass('d-none');
        }
    }

    function switchTab(tab) {
        currentTab = tab;
        $('.nav-tab-item').removeClass('active');
        $('#tab-' + tab).addClass('active');
        
        if (tab === 'teman') {
            $('#contact-filters').removeClass('d-none');
            searchContacts();
        } else {
            $('#contact-filters').addClass('d-none');
            renderRecent();
        }
    }

    function isOnline(lastActivity) {
        if (!lastActivity) return false;
        let last = new Date(lastActivity).getTime();
        let now = new Date().getTime();
        return (now - last) < (5 * 60 * 1000);
    }

    function loadSidebar() {
        if (currentTab === 'pesan') {
            renderRecent();
        } else {
            searchContacts();
        }
    }

    function renderRecent() {
        $.ajax({
            url: 'index.php?mod=api&type=chat&act=recent',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                let html = '';
                if (res.status === 'ok' && res.data && res.data.length > 0) {
                    res.data.forEach(function(u) {
                        let online = isOnline(u.last_activity);
                        let onlineDot = u.is_group == 0 ? '<div class="status-dot ' + (online ? 'online' : '') + '"></div>' : '';
                        let icon = u.is_group == 1 ? '<i class="fas fa-users fa-lg text-primary"></i>' : '<img src="' + (u.foto ? 'assets/img/profil/' + u.foto : 'assets/img/user-default.png') + '" class="avatar-img">';
                        let time = formatTime(u.last_time);
                        let badge = u.unread_count > 0 ? '<span class="unread-count">' + u.unread_count + '</span>' : '';
                        let lastMsg = u.attachment_type != 'text' ? '<i class="fas fa-image"></i> File' : (u.last_message || '');
                        let safeName = (u.name || '').replace(/'/g, "\\'");

                        html += '<div class="chat-item ' + (u.id == activeUserId && u.is_group == activeIsGroup ? 'active' : '') + '" onclick="openChat(' + u.id + ', \'' + safeName + '\', \'' + (u.foto || '') + '\', \'' + (u.last_activity || '') + '\', \'\', ' + (u.is_group || 0) + ')">' +
                            '<div class="avatar-wrapper">' + icon + onlineDot + '</div>' +
                            '<div class="flex-grow-1 overflow-hidden">' +
                                '<div class="d-flex justify-content-between align-items-center">' +
                                    '<div class="font-weight-bold text-truncate" style="max-width: 150px;">' + (u.name || '') + '</div>' +
                                    '<div class="text-xs text-muted">' + time + '</div>' +
                                '</div>' +
                                '<div class="d-flex justify-content-between align-items-center mt-1">' +
                                    '<div class="text-xs text-muted text-truncate w-75">' + lastMsg + '</div>' +
                                    badge +
                                '</div>' +
                            '</div>' +
                            '<div class="chat-item-delete" onclick="deleteConversation(event, ' + u.id + ', ' + (u.is_group || 0) + ', \'' + safeName + '\')" title="Hapus Percakapan">' +
                                '<i class="fas fa-trash-alt"></i>' +
                            '</div>' +
                        '</div>';
                    });
                } else {
                    html = '<div class="text-center p-4 text-muted small">' +
                        '<i class="fas fa-comments fa-2x mb-2 text-muted d-block"></i>' +
                        'Belum ada percakapan aktif.<br>' +
                        '<button class="btn btn-xs btn-primary mt-2 rounded-pill font-weight-bold px-3" onclick="switchTab(\'teman\')">' +
                            '<i class="fas fa-user-plus mr-1"></i> Cari Teman Chat' +
                        '</button>' +
                    '</div>';
                }
                $('#sidebar-list').html(html);
            },
            error: function() {
                $('#sidebar-list').html('<div class="text-center p-4 text-muted small"><button class="btn btn-xs btn-primary rounded-pill" onclick="switchTab(\'teman\')">Cari Teman Chat</button></div>');
            }
        });
    }

    function searchContacts() {
        let q = $('#global-search').val() || '';
        let rid = $('#filter-role').val() || '';
        let cid = $('#filter-kelas').val() || '';
        
        $('#sidebar-list').html('<div class="text-center p-4 text-muted small"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat kontak...</div>');

        $.ajax({
            url: 'index.php?mod=api&type=chat&act=search&q=' + encodeURIComponent(q) + '&role_id=' + rid + '&class_id=' + cid,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                let html = '';
                if (res.status === 'ok' && res.data && res.data.length > 0) {
                    res.data.forEach(function(u) {
                        let online = isOnline(u.last_activity);
                        let subtitle = (u.role_name || '') + (u.class_name ? ' • ' + u.class_name : '');
                        let icon = u.is_group == 1 ? '<i class="fas fa-users fa-lg text-primary"></i>' : '<img src="' + (u.foto ? 'assets/img/profil/' + u.foto : 'assets/img/user-default.png') + '" class="avatar-img">';
                        let safeName = (u.name || '').replace(/'/g, "\\'");
                        let safeSub = subtitle.replace(/'/g, "\\'");

                        html += '<div class="chat-item" onclick="openChat(' + (u.id || 0) + ', \'' + safeName + '\', \'' + (u.foto || '') + '\', \'' + (u.last_activity || '') + '\', \'' + safeSub + '\', ' + (u.is_group || 0) + ')">' +
                            '<div class="avatar-wrapper">' +
                                icon +
                                (u.is_group == 0 ? '<div class="status-dot ' + (online ? 'online' : '') + '"></div>' : '') +
                            '</div>' +
                            '<div class="chat-info ml-2 flex-grow-1 overflow-hidden">' +
                                '<div class="chat-name font-weight-bold text-truncate">' + (u.name || '') + '</div>' +
                                '<div class="chat-preview small text-muted text-truncate">' + subtitle + '</div>' +
                            '</div>' +
                        '</div>';
                    });
                } else {
                    html = '<div class="text-center p-4 text-muted small">Tidak ditemukan.</div>';
                }
                $('#sidebar-list').html(html);
            },
            error: function() {
                $('#sidebar-list').html('<div class="text-center p-4 text-danger small">Gagal memuat kontak. Silakan refresh halaman.</div>');
            }
        });
    }

    function openChat(id, name, foto, lastActivity, subtitle, isGroup) {
        subtitle = subtitle || '';
        isGroup = isGroup || 0;
        
        if (id == 0 && isGroup == 0) {
            alert('Siswa ini belum memiliki akun pengguna. Silakan hubungi Admin untuk generate akun di menu Manajemen Pengguna.');
            return;
        }
        activeUserId = id;
        activeIsGroup = isGroup;
        $('#active-name').text(name);
        
        if (isGroup) {
            $('#active-avatar').addClass('d-none');
            $('#active-status').addClass('d-none');
            $('#active-subtitle').text(subtitle || 'Grup Chat');
        } else {
            $('#active-avatar').removeClass('d-none').attr('src', foto && foto != 'null' ? 'assets/img/profil/' + foto : 'assets/img/user-default.png');
            let online = isOnline(lastActivity);
            $('#active-status').removeClass('d-none').toggleClass('online', online);
            $('#active-subtitle').text(subtitle || (online ? 'Online' : 'Offline'));
        }
        
        $('#chat-welcome').addClass('d-none');
        $('#chat-active').removeClass('d-none').addClass('d-flex');
        $('#msg-container').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
        
        loadMessages(true);
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(loadMessages, 4000);
        
        if (currentTab === 'pesan') renderRecent();

        if ($(window).width() <= 768) {
            toggleSidebar(false);
        }
    }

    function toggleSidebar(show) {
        if (show) {
            $('.chat-sidebar').removeClass('collapsed');
        } else {
            $('.chat-sidebar').addClass('collapsed');
        }
    }

    function loadMessages(forceScroll) {
        forceScroll = forceScroll || false;
        if (!activeUserId) return;
        $.ajax({
            url: 'index.php?mod=api&type=chat&act=history&id_other=' + activeUserId + '&is_group=' + activeIsGroup,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'ok') {
                    if (res.data && res.data.length > 0) {
                        let latest = res.data[res.data.length - 1];
                        if (lastMessageId !== 0 && latest.id > lastMessageId && latest.sender_id != <?= (int)($_SESSION['user_id'] ?? 0) ?>) {
                            soundReceived.play().catch(e => console.log('Audio play blocked'));
                        }
                        lastMessageId = latest.id;
                    }
                    renderMessages(res.data || [], forceScroll);
                }
            }
        });
    }

    function renderMessages(data, forceScroll) {
        allMessages = data;
        let container = $('#msg-container');
        let html = '';
        let myUserId = <?= (int)($_SESSION['user_id'] ?? 0) ?>;

        if (data.length === 0) {
            html = '<div class="text-center p-5 text-muted">Belum ada pesan. Mulai percakapan sekarang!</div>';
        }
        data.forEach(function(m) {
            let isMe = m.sender_id == myUserId;
            let senderName = (activeIsGroup && !isMe) ? '<div class="text-xs font-weight-bold text-primary mb-1">' + (m.sender_name || '') + '</div>' : '';
            let time = new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            let isDeleted = m.is_deleted == 1;

            html += '<div class="msg-group ' + (isMe ? 'sent' : 'received') + '">';
            html += '<div class="msg-bubble" style="padding-right: ' + (!isDeleted ? '30px' : '18px') + ';">';

            if (!isDeleted) {
                html += '<button class="msg-dot-menu" onclick="toggleMsgDropdown(event, ' + m.id + ')" title="Opsi"><i class="fas fa-ellipsis-v"></i></button>';
                html += '<div class="msg-dropdown" id="msg-dd-' + m.id + '">';
                if (isMe) {
                    html += '<button class="msg-dropdown-item text-danger" onclick="deleteMessage(' + m.id + ')"><i class="fas fa-trash-alt"></i> Hapus</button>';
                }
                html += '<button class="msg-dropdown-item" onclick="forwardMessage(' + m.id + ')"><i class="fas fa-share"></i> Teruskan</button>';
                html += '</div>';
            }

            html += senderName;

            if (isDeleted) {
                html += '<div class="msg-deleted"><i class="fas fa-ban mr-1"></i>Pesan ini telah dihapus</div>';
            } else {
                if (m.message && m.message.indexOf('[Diteruskan]') === 0) {
                    html += '<div class="fwd-badge"><i class="fas fa-share"></i> Diteruskan</div>';
                }

                let displayMsg = m.message || '';
                if (displayMsg.indexOf('[Diteruskan] ') === 0) {
                    displayMsg = displayMsg.substring(13);
                }
                if (displayMsg) html += '<div>' + linkify(displayMsg) + '</div>';

                if (m.attachment_path) {
                    let url = 'uploads/chat/' + m.attachment_path;
                    if (m.attachment_type === 'image') {
                        html += '<img src="' + url + '" style="max-width:100%; border-radius:10px; margin-top:5px; cursor:pointer;" onclick="window.open(\'' + url + '\')">';
                    } else if (m.attachment_type === 'audio') {
                        html += '<audio controls class="mt-2 w-100" style="max-width:250px;"><source src="' + url + '" type="audio/webm">Browser tidak mendukung audio.</audio>';
                    } else if (m.attachment_type === 'video') {
                        html += '<video controls class="mt-2 w-100" style="max-width:300px; border-radius:10px;"><source src="' + url + '">Browser tidak mendukung video.</video>';
                    } else {
                        let fname = m.attachment_path.indexOf('_') !== -1 ? m.attachment_path.split('_')[1] : m.attachment_path;
                        html += '<a href="' + url + '" target="_blank" class="p-2 bg-light d-flex align-items-center gap-2 rounded mt-2 text-decoration-none text-dark" style="font-size:0.8rem">' +
                            '<i class="fas fa-file-download fa-lg text-primary"></i>' +
                            '<span>' + fname + '</span>' +
                        '</a>';
                    }
                }
            }

            html += '</div><div class="msg-meta">' + time + '</div></div>';
        });

        if (container.html() !== html || forceScroll) {
            container.html(html);
            container.scrollTop(container[0].scrollHeight);
        }
    }

    function toggleMsgDropdown(e, msgId) {
        e.stopPropagation();
        $('.msg-dropdown.show').removeClass('show');
        $('.msg-dot-menu.active').removeClass('active');
        
        let dd = $('#msg-dd-' + msgId);
        let btn = dd.siblings('.msg-dot-menu');
        dd.toggleClass('show');
        btn.toggleClass('active');
    }

    function toggleHeaderMenu(e) {
        e.stopPropagation();
        $('#header-menu').toggleClass('show');
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.msg-dot-menu, .msg-dropdown, .btn-icon').length) {
            $('.msg-dropdown.show').removeClass('show');
            $('.msg-dot-menu.active').removeClass('active');
        }
    });

    function linkify(text) {
        var urlPattern = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\((?:[^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/ig;
        return text.replace(urlPattern, function(url) {
            var href = url;
            if (!href.match(/^https?:\/\//i) && !href.match(/^ftp:\/\//i)) {
                href = 'http://' + href;
            }
            return '<a href="' + href + '" target="_blank" rel="noopener noreferrer">' + url + '</a>';
        });
    }

    function deleteMessage(msgId) {
        if (!confirm('Yakin ingin menghapus pesan ini? Pesan yang dihapus tidak bisa dikembalikan.')) return;
        $.post('index.php?mod=api&type=chat&act=delete', { message_id: msgId }, function(res) {
            if (res.status === 'ok') {
                loadMessages(true);
            } else {
                alert(res.msg || 'Gagal menghapus pesan');
            }
        }, 'json');
    }

    function confirmClearChat() {
        $('#clear-target-name').text($('#active-name').text());
        $('#modal-clear-chat').modal('show');
        $('#header-menu').removeClass('show');
    }

    function executeClearChat(forEveryone) {
        if (!activeUserId) return;
        
        $.post('index.php?mod=api&type=chat&act=clear', {
            other_id: activeUserId,
            is_group: activeIsGroup,
            for_everyone: forEveryone
        }, function(res) {
            if (res.status === 'ok') {
                $('#modal-clear-chat').modal('hide');
                loadMessages(true);
                loadSidebar();
            } else {
                alert(res.msg || 'Gagal membersihkan chat');
            }
        }, 'json');
    }

    function deleteConversation(e, id, isGroup, name) {
        e.stopPropagation();
        if (!confirm('Hapus percakapan dengan ' + name + ' dari daftar? Riwayat pesan akan dihapus untuk Anda.')) return;
        
        $.post('index.php?mod=api&type=chat&act=clear', {
            other_id: id,
            is_group: isGroup,
            for_everyone: 0
        }, function(res) {
            if (res.status === 'ok') {
                if (id == activeUserId && isGroup == activeIsGroup) {
                    $('#chat-active').addClass('d-none').removeClass('d-flex');
                    $('#chat-welcome').removeClass('d-none');
                    activeUserId = null;
                }
                loadSidebar();
            } else {
                alert('Gagal menghapus percakapan');
            }
        }, 'json');
    }

    function forwardMessage(msgId) {
        pendingForwardId = msgId;
        $('#fwd-search').val('');
        $('#fwd-contact-list').html('<div class="text-center p-4 text-muted small">Ketik nama untuk mencari kontak...</div>');
        $('#modal-forward').modal('show');
    }

    function searchForwardContacts() {
        let q = ($('#fwd-search').val() || '').trim();
        if (q.length < 1) {
            $('#fwd-contact-list').html('<div class="text-center p-4 text-muted small">Ketik nama untuk mencari kontak...</div>');
            return;
        }
        $.get('index.php?mod=api&type=chat&act=search&q=' + encodeURIComponent(q), function(res) {
            let html = '';
            if (res.status === 'ok' && res.data && res.data.length > 0) {
                res.data.forEach(function(u) {
                    if (u.id == 0 && u.is_group == 0) return;
                    let icon = u.is_group == 1
                        ? '<div style="width:40px;height:40px;border-radius:12px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;"><i class="fas fa-users text-primary"></i></div>'
                        : '<img src="' + (u.foto ? 'assets/img/profil/' + u.foto : 'assets/img/user-default.png') + '">';
                    let subtitle = u.role_name ? u.role_name + (u.class_name ? ' • ' + u.class_name : '') : '';
                    let safeName = (u.name || '').replace(/'/g, "\\'");
                    html += '<div class="fwd-contact-item" onclick="executeForward(' + u.id + ', ' + (u.is_group || 0) + ', \'' + safeName + '\')">' +
                        icon +
                        '<div>' +
                            '<div class="fwd-contact-name">' + (u.name || '') + '</div>' +
                            '<div class="fwd-contact-sub">' + subtitle + '</div>' +
                        '</div>' +
                    '</div>';
                });
            } else {
                html = '<div class="text-center p-4 text-muted small">Tidak ditemukan.</div>';
            }
            $('#fwd-contact-list').html(html);
        }, 'json');
    }

    function executeForward(targetId, isGroup, targetName) {
        let msg = allMessages.find(m => m.id == pendingForwardId);
        if (!msg) { alert('Pesan tidak ditemukan'); return; }

        let fwdText = '[Diteruskan] ' + (msg.message || '');
        let fd = new FormData();
        fd.append('receiver_id', targetId);
        fd.append('is_group', isGroup);
        fd.append('message', fwdText);

        if (msg.attachment_path) {
            let url = 'uploads/chat/' + msg.attachment_path;
            fetch(url).then(r => r.blob()).then(blob => {
                let file = new File([blob], msg.attachment_path, { type: blob.type });
                fd.append('attachment', file);
                doForwardSend(fd, targetName);
            }).catch(() => {
                doForwardSend(fd, targetName);
            });
        } else {
            doForwardSend(fd, targetName);
        }
    }

    function doForwardSend(fd, targetName) {
        $('#modal-forward').modal('hide');
        $.ajax({
            url: 'index.php?mod=api&type=chat&act=send',
            type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json',
            success: function(res) {
                if (res.status === 'ok') {
                    alert('Pesan berhasil diteruskan ke ' + targetName);
                    loadMessages(true);
                } else {
                    alert(res.msg || 'Gagal meneruskan pesan');
                }
            },
            error: function() { alert('Kesalahan jaringan'); }
        });
    }

    function handleFileSelect(e) {
        let file = e.target.files[0];
        if (!file) return;
        showFilePreview(file);
    }

    function showFilePreview(file, isAudio) {
        isAudio = isAudio || false;
        $('#modal-preview-filename').text(file.name || 'Pesan Suara');
        $('#modal-preview-filesize').text((file.size / 1024 / 1024).toFixed(2) + ' MB');
        $('#modal-preview-caption').val($('#chat-message-input').val());

        $('#modal-preview-img').addClass('d-none');
        $('#modal-preview-file').addClass('d-none');
        $('#modal-preview-audio').addClass('d-none');
        $('#modal-preview-video').addClass('d-none');

        if (isAudio) {
            let url = URL.createObjectURL(file);
            $('#audio-playback').attr('src', url);
            $('#modal-preview-audio').removeClass('d-none');
        } else if (file.type && file.type.startsWith('image/')) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#modal-preview-img').attr('src', e.target.result).removeClass('d-none');
            }
            reader.readAsDataURL(file);
        } else if (file.type && file.type.startsWith('video/')) {
            let url = URL.createObjectURL(file);
            $('#modal-preview-video').attr('src', url);
            $('#modal-preview-video').removeClass('d-none');
        } else {
            $('#modal-preview-file').removeClass('d-none');
        }
        $('#modal-preview-send').modal('show');
    }

    let recStream;
    async function startRecording() {
        try {
            recStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(recStream);
            audioChunks = [];

            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
            mediaRecorder.start();

            $('#footer-normal').addClass('d-none');
            $('#footer-recording').removeClass('d-none');
            
            let sec = 0;
            recInterval = setInterval(() => {
                sec++;
                let m = Math.floor(sec / 60).toString().padStart(2, '0');
                let s = (sec % 60).toString().padStart(2, '0');
                $('#rec-timer').text(m + ':' + s);
            }, 1000);
        } catch (err) {
            alert('Gagal mengakses mikrofon: ' + err.message);
        }
    }

    function stopRecording(save) {
        clearInterval(recInterval);
        $('#rec-timer').text('00:00');
        $('#footer-recording').addClass('d-none');
        $('#footer-normal').removeClass('d-none');

        mediaRecorder.onstop = () => {
            if (save) {
                currentAudioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                let file = new File([currentAudioBlob], 'voice_' + Date.now() + '.webm', { type: 'audio/webm' });
                showFilePreview(file, true);
            }
            if (recStream) recStream.getTracks().forEach(track => track.stop());
        };
        mediaRecorder.stop();
    }

    function clearFileSelection() {
        $('#chat-file').val('');
        $('#chat-camera').val('');
        
        let audioSrc = $('#audio-playback').attr('src');
        if (audioSrc && audioSrc.startsWith('blob:')) {
            URL.revokeObjectURL(audioSrc);
            $('#audio-playback').attr('src', '');
        }
        
        let videoSrc = $('#modal-preview-video').attr('src');
        if (videoSrc && videoSrc.startsWith('blob:')) {
            URL.revokeObjectURL(videoSrc);
            $('#modal-preview-video').attr('src', '');
        }
        
        currentAudioBlob = null;
    }

    function sendMediaWithCaption() {
        let text = $('#modal-preview-caption').val();
        let file = $('#chat-file')[0].files[0] || $('#chat-camera')[0].files[0] || 
                   (currentAudioBlob ? new File([currentAudioBlob], 'voice_' + Date.now() + '.webm', { type: 'audio/webm' }) : null);
        
        if (!file) return;

        $('#modal-preview-send').modal('hide');
        $('#chat-message-input').val('');
        
        executeSend(text, file);
    }

    function sendMessage() {
        let text = ($('#chat-message-input').val() || '').trim();
        let file = ($('#chat-file')[0] && $('#chat-file')[0].files[0]) || ($('#chat-camera')[0] && $('#chat-camera')[0].files[0]);
        if (!text && !file) return;

        if (file) {
            sendMediaWithCaption();
            return;
        }

        executeSend(text, null);
    }

    function executeSend(text, file) {
        if (!activeUserId) {
            alert('Silakan pilih kontak terlebih dahulu');
            return;
        }

        if (file && file.size > 50 * 1024 * 1024) {
            alert('File terlalu besar. Maksimal 50MB.');
            return;
        }

        let fd = new FormData();
        fd.append('receiver_id', activeUserId);
        fd.append('is_group', activeIsGroup);
        fd.append('message', text);
        if (file) fd.append('attachment', file);
        
        let tempId = Date.now();
        let tempUrl = (file && file.type && file.type.startsWith('image/')) ? URL.createObjectURL(file) : null;
        pendingMessages.push({ id: tempId, text: text, file: file, tempUrl: tempUrl, time: new Date() });
        renderMessages(allMessages, true);
        $('#chat-message-input').val('');

        $('#btn-send').prop('disabled', true);
        $('#icon-send').addClass('d-none');
        $('#spinner-send').removeClass('d-none');

        $.ajax({
            url: 'index.php?mod=api&type=chat&act=send',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                $('#btn-send').prop('disabled', false);
                $('#icon-send').removeClass('d-none');
                $('#spinner-send').addClass('d-none');
                
                let pending = pendingMessages.find(p => p.id === tempId);
                if (pending && pending.tempUrl) URL.revokeObjectURL(pending.tempUrl);
                pendingMessages = pendingMessages.filter(p => p.id !== tempId);
                
                clearFileSelection();

                if (res.status === 'ok') {
                    soundSent.play().catch(e => console.log('Audio play blocked'));
                    loadMessages(true);
                } else {
                    alert(res.msg || 'Gagal mengirim pesan');
                    loadMessages(true);
                }
            },
            error: function(xhr) {
                $('#btn-send').prop('disabled', false);
                $('#icon-send').removeClass('d-none');
                $('#spinner-send').addClass('d-none');
                
                let pending = pendingMessages.find(p => p.id === tempId);
                if (pending && pending.tempUrl) URL.revokeObjectURL(pending.tempUrl);
                pendingMessages = pendingMessages.filter(p => p.id !== tempId);
                
                clearFileSelection();
                
                let errorMsg = 'Kesalahan jaringan: Gagal mengirim pesan';
                if (xhr.status === 413) {
                    errorMsg = 'File terlalu besar (melebihi batas server)';
                } else if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = 'Server Error: ' + xhr.responseJSON.msg;
                }
                alert(errorMsg);
                loadMessages(true);
            }
        });
    }

    function formatTime(t) {
        if (!t) return '';
        let date = new Date(t);
        return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>



