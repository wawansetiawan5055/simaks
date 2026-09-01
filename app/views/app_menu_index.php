<?php include __DIR__ . '/partials/header.php'; ?>
<!-- app/views/app_menu_index.php - Drag & Drop Nested Menu Management with True Grouping & Collapse/Expand -->

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="mb-2 mb-md-0">
                <h1 class="m-0 text-dark font-weight-bold" style="font-family: 'Poppins', sans-serif;">
                    <i class="fas fa-sitemap mr-2 text-primary"></i> Manajemen Struktur Menu
                </h1>
                <p class="text-muted small mb-0">
                    Atur hierarki, kelompok grup, dan urutan navigasi menu aplikasi dengan drag &amp; drop.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm mr-2 font-weight-bold" style="border-radius: 8px;"
                    onclick="openAddModal()">
                    <i class="fas fa-plus mr-1"></i> Tambah Menu Baru
                </button>
                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm font-weight-bold" style="border-radius: 8px;"
                    id="saveMenuOrder">
                    <i class="fas fa-save mr-1"></i> Simpan Urutan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <span class="badge badge-primary px-3 py-2 mr-3 font-weight-bold" style="font-size: 0.82rem; border-radius: 8px;">
                        <i class="fas fa-layer-group mr-1"></i> STRUKTUR HIERARKI MENU
                    </span>
                    <div class="input-group input-group-sm" style="width: 240px;">
                        <input type="text" id="searchMenuInput" class="form-control" placeholder="Cari nama menu..." style="border-radius: 6px 0 0 6px;">
                        <div class="input-group-append">
                            <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        </div>
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 font-weight-bold" id="btnExpandAll"
                        style="border-radius: 8px 0 0 8px;">
                        <i class="fas fa-expand-arrows-alt mr-1 text-primary"></i> Expand All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 font-weight-bold" id="btnCollapseAll"
                        style="border-radius: 0 8px 8px 0;">
                        <i class="fas fa-compress-arrows-alt mr-1 text-secondary"></i> Collapse All
                    </button>
                </div>
            </div>
            
            <div class="card-body p-4 bg-light">
                <div class="alert alert-primary border-0 shadow-sm mb-4"
                    style="border-radius: 12px; background: #e0f2fe; color: #0369a1;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 bg-primary text-white p-2 rounded-circle shadow-sm"
                            style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="small">
                            <strong>Panduan Praktis:</strong>
                            <ul class="mb-0 pl-3">
                                <li><strong>Pindahkan Kelompok:</strong> Drag pada ikon baris <i class="fas fa-grip-vertical text-muted"></i> di Menu Utama untuk memindahkan satu kelompok besar beserta seluruh sub-menunya.</li>
                                <li><strong>Pindahkan Sub-Menu:</strong> Drag sub-menu di dalam grupnya atau pindahkan ke kelompok grup lain secara langsung.</li>
                                <li><strong>Buka/Tutup Grup:</strong> Klik ikon panah <i class="fas fa-chevron-down text-primary"></i> untuk melipat (*collapse*) atau membuka (*expand*) daftar sub-menu.</li>
                                <li>Klik tombol hijau <strong>"Simpan Urutan"</strong> setelah selesai melakukan penataan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- TREE CONTAINER -->
                <div id="menuTreeContainer" class="menu-root-container">
                    <!-- Dynamic rendering via JS -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL FORM MENU -->
<div class="modal fade" id="modalMenuForm" tabindex="-1" role="dialog" aria-labelledby="modalMenuFormLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalMenuFormLabel">
                    <i class="fas fa-bars mr-2"></i> Form Menu Aplikasi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>app_menu/save_action" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_menu" id="form_id_menu">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_nama_menu" class="font-weight-bold">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_menu" id="form_nama_menu"
                                    placeholder="Contoh: Bimbingan dan Konseling" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_link" class="font-weight-bold">Link (Modul) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="link" id="form_link"
                                    placeholder="Contoh: uks atau # (untuk Header Grup)" required>
                                <small class="form-text text-muted">Gunakan tanda <code>#</code> jika menu ini berfungsi sebagai <strong>Header / Kelompok Grup</strong>.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_icon" class="font-weight-bold">Icon (FontAwesome)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="icon" id="form_icon"
                                        placeholder="fas fa-heartbeat">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white">
                                            <i id="icon_preview" class="fas fa-circle text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Contoh: <code>fas fa-users</code>, <code>fas fa-book</code>, <code>fas fa-calendar</code></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_urutan" class="font-weight-bold">Urutan Tampilan</label>
                                <input type="number" class="form-control" name="urutan" id="form_urutan" value="100">
                                <small class="form-text text-muted">Urutan dapat diatur otomatis melalui drag &amp; drop.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_parent_id" class="font-weight-bold">Kelompok Induk (Parent Menu)</label>
                                <select class="form-control select2" name="parent_id" id="form_parent_id" style="width: 100%;">
                                    <option value="0">⭐ -- Menu Utama / Header Kelompok --</option>
                                    <?php foreach ($parent_menus as $pm): ?>
                                        <option value="<?= $pm['id_menu'] ?>">
                                            📁 <?= htmlspecialchars($pm['nama_menu']) ?> (ID: <?= $pm['id_menu'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_status" class="font-weight-bold">Status Aktif</label>
                                <select class="form-control" name="status" id="form_status">
                                    <option value="Aktif">Aktif (Ditampilkan)</option>
                                    <option value="Nonaktif">Nonaktif (Disembunyikan)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between bg-light p-3">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Data Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Nested Tree Styling */
    .menu-root-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Main Parent Card */
    .menu-group-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .menu-group-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.1);
    }

    .menu-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: #ffffff;
        border-left: 5px solid #3b82f6;
        cursor: default;
    }

    .menu-group-header.is-header-type {
        border-left-color: #6366f1;
        background: #fafafa;
    }

    /* Sub-menu container */
    .sub-menu-list {
        min-height: 12px;
        padding: 10px 18px 14px 48px;
        background: #f8fafc;
        border-top: 1px dashed #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sub-menu-list.is-empty {
        padding: 8px 18px 8px 48px;
        font-size: 0.8rem;
        color: #94a3b8;
        font-style: italic;
    }

    /* Sub Menu Item */
    .sub-menu-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #10b981;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.15s ease;
    }

    .sub-menu-item:hover {
        border-color: #10b981;
        background: #f0fdf4;
        box-shadow: 0 3px 8px rgba(16, 185, 129, 0.12);
    }

    /* Drag handles */
    .drag-handle-parent {
        cursor: grab;
        color: #94a3b8;
        font-size: 1.15rem;
        padding: 4px 8px;
        margin-right: 8px;
    }
    .drag-handle-parent:hover { color: #3b82f6; }

    .drag-handle-sub {
        cursor: grab;
        color: #cbd5e1;
        font-size: 1rem;
        padding: 2px 6px;
        margin-right: 8px;
    }
    .drag-handle-sub:hover { color: #10b981; }

    /* Icons */
    .menu-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        margin-right: 14px;
        flex-shrink: 0;
    }
    .menu-icon-box.parent-icon { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .menu-icon-box.sub-icon { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; width: 32px; height: 32px; font-size: 0.95rem; }

    /* Chevron collapse toggle */
    .btn-toggle-collapse {
        cursor: pointer;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        color: #64748b;
        background: #f1f5f9;
        transition: all 0.2s ease;
    }
    .btn-toggle-collapse:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .btn-toggle-collapse i {
        transition: transform 0.25s ease;
    }
    .btn-toggle-collapse.is-collapsed i {
        transform: rotate(-90deg);
    }

    /* Sortable states */
    .sortable-ghost {
        opacity: 0.4;
        background: #dbeafe !important;
        border: 2px dashed #2563eb !important;
    }
    .sortable-chosen {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .badge-sub-counter {
        font-size: 0.72rem;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>

<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
// Raw Menu Data from PHP
const rawMenuData = <?= json_encode($list_menu ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?: '[]' ?>;

// Build Hierarchy (Parent -> Children)
function buildTreeStructure(menus) {
    const parentList = [];
    const childrenMap = {};

    // Inisialisasi
    menus.forEach(m => {
        const pId = parseInt(m.parent_id) || 0;
        if (pId === 0) {
            parentList.push({ ...m, children: [] });
        } else {
            if (!childrenMap[pId]) childrenMap[pId] = [];
            childrenMap[pId].push(m);
        }
    });

    // Urutkan Parent
    parentList.sort((a, b) => parseInt(a.urutan) - parseInt(b.urutan));

    // Masukkan Children ke Parent masing-masing
    parentList.forEach(parent => {
        const cList = childrenMap[parent.id_menu] || [];
        cList.sort((a, b) => parseInt(a.urutan) - parseInt(b.urutan));
        parent.children = cList;
        delete childrenMap[parent.id_menu];
    });

    // Handle Orphan Children (jika parent_id tidak ditemukan, buatkan grup fallback atau jadikan root)
    for (const orphanParentId in childrenMap) {
        childrenMap[orphanParentId].forEach(orphan => {
            parentList.push({
                ...orphan,
                parent_id: 0,
                children: []
            });
        });
    }

    return parentList;
}

// Render Menu Tree ke HTML
function renderMenuTreeUI() {
    const tree = buildTreeStructure(rawMenuData);
    const container = document.getElementById('menuTreeContainer');

    if (!tree || tree.length === 0) {
        container.innerHTML = '<div class="alert alert-warning text-center p-4">Belum ada menu yang terdaftar. Silakan klik tombol "Tambah Menu Baru".</div>';
        return;
    }

    let html = '';

    tree.forEach(group => {
        const isHeaderType = group.link === '#' || group.link === '';
        const childCount = group.children ? group.children.length : 0;
        const jsonParent = JSON.stringify(group).replace(/"/g, '&quot;');

        html += `
        <div class="menu-group-card" data-id="${group.id_menu}">
            <!-- Parent Group Header -->
            <div class="menu-group-header ${isHeaderType ? 'is-header-type' : ''}">
                <div class="d-flex align-items-center flex-grow-1">
                    <span class="drag-handle-parent" title="Drag untuk memindahkan seluruh kelompok grup ini"><i class="fas fa-grip-vertical"></i></span>
                    
                    <div class="btn-toggle-collapse ${childCount === 0 ? 'd-none' : ''}" onclick="toggleGroupCollapse(${group.id_menu}, this)" title="Buka/Tutup Sub-Menu">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    ${childCount === 0 ? '<div style="width: 28px; margin-right: 8px;"></div>' : ''}

                    <div class="menu-icon-box parent-icon shadow-sm">
                        <i class="${group.icon || 'fas fa-circle'}"></i>
                    </div>

                    <div>
                        <div class="font-weight-bold text-dark d-flex align-items-center" style="font-size: 0.98rem;">
                            ${escapeHtml(group.nama_menu)}
                            ${isHeaderType ? '<span class="badge badge-indigo text-white ml-2 badge-sub-counter" style="background:#6366f1;">HEADER GRUP</span>' : ''}
                            ${group.status === 'Nonaktif' ? '<span class="badge badge-danger ml-2 badge-sub-counter">NONAKTIF</span>' : ''}
                            ${childCount > 0 ? `<span class="badge badge-secondary ml-2 badge-sub-counter"><i class="fas fa-sitemap mr-1"></i>${childCount} Sub-menu</span>` : ''}
                        </div>
                        <div class="small text-muted">
                            <span class="mr-3"><i class="fas fa-link mr-1"></i><code>${escapeHtml(group.link)}</code></span>
                            <span><i class="fas fa-sort-numeric-down mr-1"></i>Urutan: <strong>${group.urutan}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Parent -->
                <div class="d-flex align-items-center gap-1">
                    <a href="<?= BASE_URL ?>app_menu/duplicate_action?id=${group.id_menu}" class="btn btn-xs btn-outline-success mr-1 shadow-none" style="border-radius: 8px; padding: 4px 8px;" title="Duplikat Menu">
                        <i class="fas fa-copy"></i>
                    </a>
                    <button type="button" class="btn btn-xs btn-outline-warning mr-1 shadow-none" style="border-radius: 8px; padding: 4px 8px;" onclick="openEditModal(this)" data-json="${jsonParent}" title="Edit Menu">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <a href="<?= BASE_URL ?>app_menu/delete_action?id=${group.id_menu}" class="btn btn-xs btn-outline-danger shadow-none" style="border-radius: 8px; padding: 4px 8px;" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini beserta izin aksesnya?');" title="Hapus Menu">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </div>

            <!-- Sub-Menu Container List (Nested Sortable Target) -->
            <div class="sub-menu-list nested-sub-sortable" id="sub-list-${group.id_menu}" data-parent-id="${group.id_menu}">
                ${group.children && group.children.length > 0 ? group.children.map(child => {
                    const jsonChild = JSON.stringify(child).replace(/"/g, '&quot;');
                    return `
                    <div class="sub-menu-item" data-id="${child.id_menu}" data-parent-id="${group.id_menu}">
                        <div class="d-flex align-items-center flex-grow-1">
                            <span class="drag-handle-sub" title="Drag untuk memindahkan sub-menu ini"><i class="fas fa-grip-vertical"></i></span>
                            
                            <div class="menu-icon-box sub-icon shadow-sm">
                                <i class="${child.icon || 'far fa-circle'}"></i>
                            </div>

                            <div>
                                <div class="font-weight-bold text-dark" style="font-size: 0.92rem;">
                                    ${escapeHtml(child.nama_menu)}
                                    ${child.status === 'Nonaktif' ? '<span class="badge badge-danger ml-2 badge-sub-counter">NONAKTIF</span>' : ''}
                                </div>
                                <div class="small text-muted">
                                    <span class="mr-3"><i class="fas fa-link mr-1"></i><code>${escapeHtml(child.link)}</code></span>
                                    <span><i class="fas fa-sort-numeric-down mr-1"></i>Urutan: <strong>${child.urutan}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <a href="<?= BASE_URL ?>app_menu/duplicate_action?id=${child.id_menu}" class="btn btn-xs btn-light border mr-1 shadow-none" style="border-radius: 8px; padding: 3px 7px;" title="Duplikat Sub-menu">
                                <i class="fas fa-copy text-success"></i>
                            </a>
                            <button type="button" class="btn btn-xs btn-light border mr-1 shadow-none" style="border-radius: 8px; padding: 3px 7px;" onclick="openEditModal(this)" data-json="${jsonChild}" title="Edit Sub-menu">
                                <i class="fas fa-pencil-alt text-warning"></i>
                            </button>
                            <a href="<?= BASE_URL ?>app_menu/delete_action?id=${child.id_menu}" class="btn btn-xs btn-light border shadow-none" style="border-radius: 8px; padding: 3px 7px;" onclick="return confirm('Hapus sub-menu ini?');" title="Hapus Sub-menu">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        </div>
                    </div>
                    `;
                }).join('') : `
                    <div class="empty-placeholder py-1 text-muted small"><i class="fas fa-info-circle mr-1"></i>Tidak ada sub-menu. Tarik sub-menu ke sini untuk mengelompokkan.</div>
                `}
            </div>
        </div>
        `;
    });

    container.innerHTML = html;
    initAllSortables();
}

// Inisialisasi SortableJS pada Root Parent & Sub-menu Containers
function initAllSortables() {
    if (typeof Sortable === 'undefined') {
        console.error("SortableJS tidak ditemukan.");
        return;
    }

    // 1. Root Parent Sortable (Memindahkan seluruh kelompok grup)
    const rootEl = document.getElementById('menuTreeContainer');
    if (rootEl) {
        new Sortable(rootEl, {
            animation: 200,
            handle: '.drag-handle-parent',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function() {
                recalculateGlobalOrder();
            }
        });
    }

    // 2. Nested Sub-menu Sortables
    const subContainers = document.querySelectorAll('.nested-sub-sortable');
    subContainers.forEach(subEl => {
        new Sortable(subEl, {
            group: 'nested-sub-menus', // Memungkinkan drag antar kelompok
            animation: 200,
            handle: '.drag-handle-sub',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onAdd: function(evt) {
                // Hapus placeholder kosong jika ada item masuk
                const emptyMsg = evt.to.querySelector('.empty-placeholder');
                if (emptyMsg) emptyMsg.remove();
                recalculateGlobalOrder();
            },
            onEnd: function() {
                recalculateGlobalOrder();
            }
        });
    });
}

// Hitung Ulang Urutan & Parent ID Baru
function recalculateGlobalOrder() {
    const updates = [];
    let globalIndex = 1;

    const groupCards = document.querySelectorAll('#menuTreeContainer > .menu-group-card');
    
    groupCards.forEach(groupCard => {
        const parentId = groupCard.getAttribute('data-id');
        
        // 1. Catat Parent Menu (parent_id = 0)
        updates.push({
            id_menu: parseInt(parentId),
            parent_id: 0,
            urutan: globalIndex++
        });

        // 2. Catat seluruh Sub-menu di bawah parent ini
        const subItems = groupCard.querySelectorAll('.sub-menu-list > .sub-menu-item');
        subItems.forEach(subItem => {
            const subId = subItem.getAttribute('data-id');
            // Update attribute data-parent-id
            subItem.setAttribute('data-parent-id', parentId);

            updates.push({
                id_menu: parseInt(subId),
                parent_id: parseInt(parentId),
                urutan: globalIndex++
            });
        });
    });

    window.menuUpdatesPayload = updates;
}

// Collapse / Expand Toggle per Grup
function toggleGroupCollapse(groupId, btnEl) {
    const subList = document.getElementById(`sub-list-${groupId}`);
    if (!subList) return;

    $(subList).slideToggle(180, function() {
        if ($(subList).is(':visible')) {
            $(btnEl).removeClass('is-collapsed');
        } else {
            $(btnEl).addClass('is-collapsed');
        }
    });
}

// Expand All Groups
document.getElementById('btnExpandAll').addEventListener('click', function() {
    $('.sub-menu-list').slideDown(180);
    $('.btn-toggle-collapse').removeClass('is-collapsed');
});

// Collapse All Groups
document.getElementById('btnCollapseAll').addEventListener('click', function() {
    $('.sub-menu-list').slideUp(180);
    $('.btn-toggle-collapse').addClass('is-collapsed');
});

// Live Search Filter Menu
document.getElementById('searchMenuInput').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    
    if (query === '') {
        $('.menu-group-card').show();
        $('.sub-menu-item').show();
        return;
    }

    $('.menu-group-card').each(function() {
        const groupCard = $(this);
        const parentText = groupCard.find('.menu-group-header').text().toLowerCase();
        let matchInParent = parentText.includes(query);
        let matchInChild = false;

        groupCard.find('.sub-menu-item').each(function() {
            const subItem = $(this);
            const subText = subItem.text().toLowerCase();
            if (subText.includes(query)) {
                subItem.show();
                matchInChild = true;
            } else {
                subItem.hide();
            }
        });

        if (matchInParent || matchInChild) {
            groupCard.show();
            // Otomatis expand grup jika ada hasil pencarian di anaknya
            groupCard.find('.sub-menu-list').show();
            groupCard.find('.btn-toggle-collapse').removeClass('is-collapsed');
        } else {
            groupCard.hide();
        }
    });
});

// Save Menu Order via AJAX
document.getElementById('saveMenuOrder').addEventListener('click', function() {
    recalculateGlobalOrder();
    
    const payload = window.menuUpdatesPayload;
    if (!payload || payload.length === 0) {
        Swal.fire('Info', 'Tidak ada perubahan urutan yang perlu disimpan.', 'info');
        return;
    }

    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

    fetch('<?= BASE_URL ?>app_menu/save_order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ updates: payload })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disimpan!',
                text: 'Struktur dan urutan menu aplikasi telah diperbarui.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat menyimpan struktur menu.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        Swal.fire('Error', err.message, 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Modal Form Openers
function openAddModal() {
    $('#form_id_menu').val('');
    $('#form_nama_menu').val('');
    $('#form_link').val('');
    $('#form_icon').val('far fa-circle');
    $('#form_urutan').val('100');
    $('#form_parent_id').val('0').trigger('change');
    $('#form_status').val('Aktif');
    $('#icon_preview').attr('class', 'far fa-circle text-primary');

    $('#modalMenuFormLabel').html('<i class="fas fa-plus mr-2"></i> Tambah Menu Baru');
    $('#modalMenuForm').modal('show');
}

function openEditModal(btnEl) {
    const data = $(btnEl).data('json');
    if (!data) return;

    $('#form_id_menu').val(data.id_menu);
    $('#form_nama_menu').val(data.nama_menu);
    $('#form_link').val(data.link);
    $('#form_icon').val(data.icon);
    $('#form_urutan').val(data.urutan);
    $('#form_parent_id').val(data.parent_id).trigger('change');
    $('#form_status').val(data.status);

    $('#icon_preview').attr('class', data.icon || 'far fa-circle');

    $('#modalMenuFormLabel').html('<i class="fas fa-pencil-alt mr-2"></i> Edit Menu: ' + escapeHtml(data.nama_menu));
    $('#modalMenuForm').modal('show');
}

// Icon live preview listener
$('#form_icon').on('input', function() {
    $('#icon_preview').attr('class', $(this).val() || 'far fa-circle text-primary');
});

// Helper Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Document Ready Init
document.addEventListener('DOMContentLoaded', function() {
    renderMenuTreeUI();
    if ($('.select2').length) {
        $('.select2').select2({ dropdownParent: $('#modalMenuForm') });
    }
});
</script>