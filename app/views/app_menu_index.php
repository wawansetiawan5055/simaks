<?php include __DIR__ . '/partials/header.php'; ?>
<!-- app/views/app_menu_index.php - Drag & Drop Menu Management with Modal -->

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-sitemap mr-2"></i> Manajemen Menu Aplikasi</h1>
                <p class="text-muted small mb-0">Atur struktur, urutan, dan level menu navigasi aplikasi secara dinamis.
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-none mr-2" style="border-radius: 8px;"
                    onclick="openAddModal()">
                    <i class="fas fa-plus mr-1"></i> Tambah Menu Baru
                </button>
                <button type="button" class="btn btn-success btn-sm px-3 shadow-none" style="border-radius: 8px;"
                    id="saveMenuOrder">
                    <i class="fas fa-save mr-1"></i> Simpan Urutan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-muted small uppercase"><i
                        class="fas fa-list mr-2 text-primary"></i> STRUKTUR MENU APLIKASI</h6>
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-outline-secondary px-2" id="expandAll"
                        style="border-radius: 6px 0 0 6px;">
                        <i class="fas fa-expand-alt mr-1"></i> Expand
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary px-2" id="collapseAll"
                        style="border-radius: 0 6px 6px 0;">
                        <i class="fas fa-compress-alt mr-1"></i> Collapse
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 shadow-none mb-4"
                    style="border-radius: 12px; background: #f0f9ff; color: #0369a1;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 bg-info text-white p-2 rounded-circle shadow-sm"
                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="small">
                            <strong>Panduan:</strong> Drag & Drop untuk mengurutkan. Gunakan <strong>(→)</strong> untuk
                            sub-menu dan <strong>(←)</strong> untuk menaikkan level. Jangan lupa klik <strong>Simpan
                                Urutan</strong>.
                        </div>
                    </div>
                </div>

                <div id="menuTree" class="menu-tree">
                    <!-- Menu items will be rendered here -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL FORM MENU -->
<div class="modal fade" id="modalMenuForm" tabindex="-1" role="dialog" aria-labelledby="modalMenuFormLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalMenuFormLabel"><i class="fas fa-bars"></i> Form Menu Aplikasi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?mod=app_menu&act=save_action" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_menu" id="form_id_menu">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_nama_menu">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_menu" id="form_nama_menu"
                                    placeholder="Contoh: Data Guru" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_link">Link (Modul) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="link" id="form_link"
                                    placeholder="Contoh: index.php?mod=guru (Gunakan # untuk header)" required>
                                <small class="form-text text-muted">Gunakan <code>#</code> jika menu ini hanya sebagai
                                    Header (Parent/Kategori).</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_icon">Icon (FontAwesome)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="icon" id="form_icon"
                                        placeholder="far fa-circle">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i id="icon_preview"
                                                class="far fa-circle"></i></span>
                                    </div>
                                </div>
                                <small class="form-text text-muted"><a href="https://fontawesome.com/v5/search?m=free"
                                        target="_blank">Cari Icon di sini</a></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_urutan">Urutan</label>
                                <input type="number" class="form-control" name="urutan" id="form_urutan" value="100">
                                <small class="form-text text-muted">Urutan akan otomatis diupdate saat drag &
                                    drop.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_parent_id">Menu Induk (Parent)</label>
                                <select class="form-control select2" name="parent_id" id="form_parent_id"
                                    style="width: 100%;">
                                    <option value="0">-- Menu Utama (Top Level) --</option>
                                    <?php foreach ($parent_menus as $pm): ?>
                                        <option value="<?= $pm['id_menu'] ?>">
                                            <?= $pm['nama_menu'] ?> (Level Utama)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="form_status">Status</label>
                                <select class="form-control" name="status" id="form_status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Menu Tree Styles */
    .menu-tree {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 12px 18px;
        cursor: move;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: block;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .menu-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .menu-item.dragging {
        opacity: 0.5;
        background: #eff6ff;
        border: 2px dashed #3b82f6;
    }

    /* Identation */
    .menu-item[data-level="0"] {
        margin-left: 0;
        border-left: 4px solid #3b82f6;
    }

    .menu-item[data-level="1"] {
        margin-left: 40px;
        border-left: 4px solid #10b981;
    }

    .menu-item[data-level="2"] {
        margin-left: 80px;
        border-left: 4px solid #f59e0b;
    }

    .menu-item[data-level="3"] {
        margin-left: 120px;
        border-left: 4px solid #64748b;
    }

    .menu-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .menu-info {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .menu-icon-wrapper {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        margin-right: 15px;
        color: #475569;
        font-size: 1.1rem;
    }

    .menu-details {
        flex: 1;
    }

    .menu-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .menu-actions {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .drag-handle {
        cursor: grab;
        color: #cbd5e1;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .drag-handle:hover {
        color: #64748b;
    }

    .collapse-toggle {
        cursor: pointer;
        margin-right: 8px;
        color: #94a3b8;
        transition: transform 0.2s;
        width: 20px;
        text-align: center;
    }

    .collapse-toggle:hover {
        color: #3b82f6;
    }

    .collapse-toggle.collapsed {
        transform: rotate(-90deg);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    // Menu data from PHP
    const menuData = <?= json_encode($list_menu ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?: '[]' ?>;

    // --- MODAL & FORM FUNCTIONS ---

    function openAddModal() {
        // Reset Form
        $('#form_id_menu').val('');
        $('#form_nama_menu').val('');
        $('#form_link').val('');
        $('#form_icon').val('far fa-circle');
        $('#form_urutan').val('100');
        $('#form_parent_id').val('0').trigger('change');
        $('#form_status').val('Aktif');
        $('#icon_preview').attr('class', 'far fa-circle');

        // UI Update
        $('#modalMenuFormLabel').html('<i class="fas fa-plus"></i> Tambah Menu Baru');
        $('#modalMenuForm').modal('show');
    }

    function openEditModal(btn) {
        // Get Data from Button
        const data = $(btn).data('json');

        // Populate Form
        $('#form_id_menu').val(data.id_menu);
        $('#form_nama_menu').val(data.nama_menu);
        $('#form_link').val(data.link);
        $('#form_icon').val(data.icon);
        $('#form_urutan').val(data.urutan);
        $('#form_parent_id').val(data.parent_id).trigger('change');
        $('#form_status').val(data.status);

        // Preview Icon
        $('#icon_preview').attr('class', data.icon);

        // UI Update
        $('#modalMenuFormLabel').html('<i class="fas fa-edit"></i> Edit Menu');
        $('#modalMenuForm').modal('show');
    }

    // Icon live preview
    $('#form_icon').on('input', function () {
        let iconClass = $(this).val();
        $('#icon_preview').attr('class', iconClass);
    });


    // --- TREE RENDER & DRAG DROP ---

    // Build tree structure
    function buildMenuTree(menus) {
        const menuMap = {};
        const rootMenus = [];

        // Create menu map
        menus.forEach(menu => {
            menuMap[menu.id_menu] = { ...menu, children: [] };
        });

        // Build tree
        menus.forEach(menu => {
            if (menu.parent_id == 0) {
                rootMenus.push(menuMap[menu.id_menu]);
            } else {
                // Check if parent exists in map (handling orphans due to filtering)
                if (menuMap[menu.parent_id]) {
                    menuMap[menu.parent_id].children.push(menuMap[menu.id_menu]);
                } else {
                    console.warn('Orphan menu found (Parent not in list):', menu);
                    // Option: Add to root or just ignore? Let's add with warning to avoid data loss visual
                    // rootMenus.push(menuMap[menu.id_menu]); 
                }
            }
        });

        function sortByUrutan(items) {
            items.sort((a, b) => a.urutan - b.urutan);
            items.forEach(item => {
                if (item.children.length > 0) sortByUrutan(item.children);
            });
        }

        sortByUrutan(rootMenus);
        return rootMenus;
    }

    // Flatten tree for drag & drop
    function flattenTree(tree, level = 0, result = []) {
        tree.forEach(item => {
            result.push({ ...item, level: level });
            if (item.children && item.children.length > 0) {
                flattenTree(item.children, level + 1, result);
            }
        });
        return result;
    }

    // Render menu tree
    function renderMenuTree() {
        try {
            const tree = buildMenuTree(menuData);
            const flatMenu = flattenTree(tree);
            const container = document.getElementById('menuTree');

            if (flatMenu.length === 0) {
                container.innerHTML = '<div class="alert alert-warning">Tidak ada data menu untuk ditampilkan (Flat array 0 items). Cek apakah Parent ID valid.</div>';
                return;
            }

            container.innerHTML = flatMenu.map(menu => {
                // Prepare JSON for Edit Button safely
                const jsonString = JSON.stringify(menu).replace(/"/g, '&quot;');

                return `
            <div class="menu-item" 
                 data-id="${menu.id_menu}" 
                 data-parent="${menu.parent_id}"
                 data-level="${menu.level}">
                <div class="menu-content">
                    <div class="menu-info">
                        <i class="fas fa-grip-vertical drag-handle"></i>
                        ${menu.children && menu.children.length > 0 ?
                        `<i class="fas fa-chevron-down collapse-toggle" onclick="toggleChildren(${menu.id_menu})"></i>` :
                        '<span style="width: 20px; display: inline-block;"></span>'}
                        
                        <div class="menu-icon-wrapper shadow-sm">
                            <i class="${menu.icon}"></i>
                        </div>
                        
                        <div class="menu-details">
                            <div class="menu-name">
                                ${menu.nama_menu} 
                                ${menu.status === 'Nonaktif' ? '<span class="badge badge-pill badge-danger border ml-2" style="font-size: 0.65rem;">NONAKTIF</span>' : ''}
                            </div>
                        </div>
                    </div>
                    
                    <div class="menu-actions">
                        <div class="d-flex mr-2">
                            <button class="btn btn-xs btn-light border outdent-btn mr-1 shadow-none" onclick="outdentMenu(${menu.id_menu})" 
                                    ${menu.level === 0 ? 'disabled' : ''} title="Outdent (Kiri)" 
                                    style="width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fff; border-color: #e2e8f0 !important;">
                                <i class="fas fa-arrow-left text-muted" style="font-size: 0.85rem;"></i>
                            </button>
                            <button class="btn btn-xs btn-light border indent-btn shadow-none" onclick="indentMenu(${menu.id_menu})" 
                                    title="Indent (Kanan)" 
                                    style="width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #fff; border-color: #e2e8f0 !important;">
                                <i class="fas fa-arrow-right text-muted" style="font-size: 0.85rem;"></i>
                            </button>
                        </div>
                        
                        <div class="btn-group">
                            <a href="index.php?mod=app_menu&act=duplicate_action&id=${menu.id_menu}" 
                               class="btn btn-xs border-0 p-1 mr-1 shadow-none" 
                               style="background: #ecfdf4; width: 32px; height: 32px; border-radius: 10px; color: #10b981; display: flex; align-items: center; justify-content: center;"
                               title="Duplikat Menu">
                                <i class="fas fa-copy" style="font-size: 0.85rem;"></i>
                            </a>
                            <button type="button" class="btn btn-xs border-0 p-1 mr-1 shadow-none" 
                                    style="background: #fffbeb; width: 32px; height: 32px; border-radius: 10px; color: #d97706; display: flex; align-items: center; justify-content: center;"
                                    onclick="openEditModal(this)" data-json="${jsonString}" title="Edit Menu">
                                <i class="fas fa-pencil-alt" style="font-size: 0.85rem;"></i>
                            </button>
                            <a href="index.php?mod=app_menu&act=delete_action&id=${menu.id_menu}" 
                               class="btn btn-xs border-0 p-1 shadow-none" 
                               style="background: #fef2f2; width: 32px; height: 32px; border-radius: 10px; color: #dc2626; display: flex; align-items: center; justify-content: center;"
                               onclick="return confirmDelete(event)" title="Hapus Menu">
                                <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `}).join('');

            initSortable();
        } catch (e) {
            console.error("Error rendering menu tree:", e);
            // Log to console instead of removed debug div
            alert('Error rendering menu: ' + e.message);
        }
    }

    // Initialize SortableJS
    function initSortable() {
        if (typeof Sortable === 'undefined') {
            console.error("SortableJS Library not found. Drag & Drop disabled.");
            alert('SortableJS library tidak ter-load. Fitur drag & drop tidak tersedia.');
            return;
        }
        const container = document.getElementById('menuTree');
        new Sortable(container, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'dragging',
            dragClass: 'drag-over',
            onEnd: function (evt) {
                updateMenuOrder();
            }
        });
    }

    // Update menu order after drag
    function updateMenuOrder() {
        const items = document.querySelectorAll('.menu-item');
        const updates = [];

        items.forEach((item, index) => {
            const id = item.getAttribute('data-id');
            const level = parseInt(item.getAttribute('data-level'));

            // Determine parent based on previous item's level
            let parent_id = 0;
            if (index > 0) {
                const prevItem = items[index - 1];
                const prevLevel = parseInt(prevItem.getAttribute('data-level'));
                const prevId = prevItem.getAttribute('data-id');

                if (level > prevLevel) {
                    parent_id = prevId;
                } else if (level === prevLevel) {
                    parent_id = prevItem.getAttribute('data-parent');
                } else {
                    // Find parent by going back
                    for (let i = index - 1; i >= 0; i--) {
                        const checkLevel = parseInt(items[i].getAttribute('data-level'));
                        if (checkLevel === level - 1) {
                            parent_id = items[i].getAttribute('data-id');
                            break;
                        }
                    }
                }
            }

            // Update DOM data-parent attribute to reflect calculation
            item.setAttribute('data-parent', parent_id);

            updates.push({ id_menu: id, urutan: index + 1, parent_id: parent_id });
        });

        // Store globally
        window.menuUpdates = updates;
    }

    function indentMenu(menuId) {
        const item = document.querySelector(`[data-id="${menuId}"]`);
        const level = parseInt(item.getAttribute('data-level'));

        if (level < 3) {
            item.setAttribute('data-level', level + 1);
            // Do NOT re-render tree, just update order calculation
            updateMenuOrder();

            // Update button state visually
            const outdentBtn = item.querySelector('.outdent-btn');
            if (outdentBtn) outdentBtn.disabled = false;
        }
    }

    function outdentMenu(menuId) {
        const item = document.querySelector(`[data-id="${menuId}"]`);
        const level = parseInt(item.getAttribute('data-level'));

        if (level > 0) {
            item.setAttribute('data-level', level - 1);
            // Do NOT re-render tree
            updateMenuOrder();

            // Update button state
            if (level - 1 === 0) {
                const outdentBtn = item.querySelector('.outdent-btn');
                if (outdentBtn) outdentBtn.disabled = true;
            }
        }
    }

    function toggleChildren(menuId) {
        const toggle = document.querySelector(`[data-id="${menuId}"] .collapse-toggle`);
        // Logic for visual collapse is complex in flat list, so we settle for icon rotation for now
        // In a flat Sortable list, true hierarchy collapsing is tricky without breaking drag & drop.
        // For now, let's toggle the icon state.
        if (toggle) toggle.classList.toggle('collapsed');
    }

    // Save Order with SweetAlert2
    document.getElementById('saveMenuOrder').addEventListener('click', function () {
        if (!window.menuUpdates) updateMenuOrder();

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        fetch('index.php?mod=app_menu&act=save_order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ updates: window.menuUpdates })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Urutan menu berhasil disimpan!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat menyimpan.'
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });

    document.addEventListener('DOMContentLoaded', function () {
        renderMenuTree();
        // Re-init Select2 for modal if needed
        if ($('.select2').length) $('.select2').select2({ dropdownParent: $('#modalMenuForm') });
    });

</script>