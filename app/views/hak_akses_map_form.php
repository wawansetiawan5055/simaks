<?php
// app/views/hak_akses_map_form.php

// 1. INCLUDE HEADER
$path_header = __DIR__ . '/partials/header.php';
if (file_exists($path_header))
    include $path_header;
?>

<style>
    /* Matriks Perizinan Modern */
    .matrix-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .table-matrix {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-matrix thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8fafc;
        border-bottom: 2px solid #cbd5e1;
        color: #334155;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 10px 14px;
    }

    .table-matrix tbody td {
        padding: 8px 14px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* Menu Grouping */
    .row-header {
        background: #334155 !important;
        color: #ffffff !important;
    }

    .row-header td {
        font-weight: 700;
        font-size: 0.78rem;
        letter-spacing: 0.6px;
        padding: 8px 14px !important;
    }

    .row-parent {
        background: #f8fafc !important;
    }

    .row-parent td {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.84rem;
    }

    .row-child td {
        font-size: 0.82rem;
        color: #475569;
    }

    .child-indent {
        display: flex;
        align-items: center;
        padding-left: 8px;
    }

    .child-indent::before {
        content: '';
        width: 10px;
        height: 10px;
        border-left: 2px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
        margin-right: 8px;
        margin-top: -4px;
        border-bottom-left-radius: 3px;
    }

    /* Custom Checkbox */
    .perm-check {
        width: 17px;
        height: 17px;
        cursor: pointer;
        accent-color: #0284c7;
    }

    .cell-highlight:hover {
        background-color: #f0f9ff;
    }

    .sticky-footer-custom {
        position: sticky;
        bottom: 0;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 12px 16px;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.04);
    }
</style>

<div class="content-header pt-3 pb-2">
    <div class="container-fluid">
        <!-- HEADER FORM -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; background: linear-gradient(135deg, #1e293b 0%, #293548 100%); color: #ffffff;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 12px;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; color: #38bdf8; flex-shrink: 0;">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center">
                                <h4 class="font-weight-bold text-white mb-0" style="font-family: 'Poppins', sans-serif; font-size: 1.15rem;">
                                    Atur Izin: <?= htmlspecialchars($peran['nama_peran']) ?>
                                </h4>
                                <span class="badge badge-info ml-2 px-2 py-0.5" style="font-size: 0.65rem; border-radius: 6px;">ID: <?= $peran['id_peran'] ?></span>
                            </div>
                            <p class="mb-0 text-white-50 small mt-1">
                                Sesuaikan kontrol akses per modul sistem: <strong>Read</strong> (lihat), <strong>Create</strong> (tambah), <strong>Update</strong> (ubah), dan <strong>Delete</strong> (hapus).
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>hak_akses" class="btn btn-outline-light btn-sm font-weight-bold px-3" style="border-radius: 8px; font-size: 0.78rem;">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Peran
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTION TOOLBAR -->
        <div class="card border-0 shadow-xs mb-3" style="border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                    <span class="text-muted font-weight-bold mr-1" style="font-size: 0.74rem;"><i class="fas fa-bolt text-warning mr-1"></i> Quick Action:</span>
                    <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold" onclick="toggleColumn('read', true)" style="border-radius: 6px; font-size: 0.70rem;">
                        <i class="fas fa-check mr-1"></i> Semua Read
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-success font-weight-bold" onclick="toggleAllCRUD(true)" style="border-radius: 6px; font-size: 0.70rem;">
                        <i class="fas fa-check-double mr-1"></i> Semua Akses Penuh (CRUD)
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold" onclick="toggleAllCRUD(false)" style="border-radius: 6px; font-size: 0.70rem;">
                        <i class="fas fa-times mr-1"></i> Reset / Kosongkan
                    </button>
                </div>
                <div class="text-muted small">
                    <span class="badge badge-light border text-dark font-weight-bold" id="selected-count">0</span> izin aktif
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <form action="<?= BASE_URL ?>hak_akses/save_action" method="post" id="formPermissions">
            <input type="hidden" name="id_peran" value="<?= $peran['id_peran'] ?>">

            <div class="matrix-container">
                <table class="table table-matrix">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">Modul / Menu Sistem</th>
                            <th class="text-center" style="width: 14%;">
                                <label class="mb-0 d-flex align-items-center justify-content-center cursor-pointer" title="Centang / Hapus Semua Read">
                                    <input type="checkbox" id="header-check-read" class="mr-1 perm-check" onchange="toggleColumn('read', this.checked)">
                                    <span>READ</span>
                                </label>
                            </th>
                            <th class="text-center" style="width: 14%;">
                                <label class="mb-0 d-flex align-items-center justify-content-center cursor-pointer" title="Centang / Hapus Semua Create">
                                    <input type="checkbox" id="header-check-create" class="mr-1 perm-check" onchange="toggleColumn('create', this.checked)">
                                    <span>CREATE</span>
                                </label>
                            </th>
                            <th class="text-center" style="width: 14%;">
                                <label class="mb-0 d-flex align-items-center justify-content-center cursor-pointer" title="Centang / Hapus Semua Update">
                                    <input type="checkbox" id="header-check-update" class="mr-1 perm-check" onchange="toggleColumn('update', this.checked)">
                                    <span>UPDATE</span>
                                </label>
                            </th>
                            <th class="text-center" style="width: 14%;">
                                <label class="mb-0 d-flex align-items-center justify-content-center cursor-pointer" title="Centang / Hapus Semua Delete">
                                    <input type="checkbox" id="header-check-delete" class="mr-1 perm-check" onchange="toggleColumn('delete', this.checked)">
                                    <span>DELETE</span>
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list_menu as $menu):
                            $id = $menu['id_menu'];
                            $parent = $menu['parent_id'];
                            $link = $menu['link'];
                            $nama = htmlspecialchars($menu['nama_menu']);
                            $icon = $menu['icon'];
                            $perm = $izin_saat_ini[$id] ?? ['can_read' => 0, 'can_create' => 0, 'can_update' => 0, 'can_delete' => 0];

                            $row_class = 'row-child';
                            if ($parent == 0 && $link == '#')
                                $row_class = 'row-header';
                            elseif ($parent == 0)
                                $row_class = 'row-parent';
                            ?>
                            <tr class="<?= $row_class ?> cell-highlight">
                                <td>
                                    <?php if ($row_class == 'row-header'): ?>
                                        <i class="fas fa-folder-open mr-2 text-warning"></i> <?= strtoupper($nama) ?>
                                    <?php elseif ($row_class == 'row-parent'): ?>
                                        <i class="<?= $icon ?> mr-2 text-primary" style="width: 18px;"></i>
                                        <?= $nama ?>
                                    <?php else: ?>
                                        <div class="child-indent"><?= $nama ?></div>
                                    <?php endif; ?>
                                </td>

                                <?php if ($row_class == 'row-header'): ?>
                                    <td colspan="4" class="text-center">
                                        <small class="text-white-50 font-weight-normal font-italic">Kelompok Menu</small>
                                    </td>
                                <?php else: ?>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check col-read" name="permissions[<?= $id ?>][read]" value="1" <?= $perm['can_read'] ? 'checked' : '' ?> onchange="updateCounter()">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check col-create" name="permissions[<?= $id ?>][create]" value="1" <?= $perm['can_create'] ? 'checked' : '' ?> onchange="updateCounter()">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check col-update" name="permissions[<?= $id ?>][update]" value="1" <?= $perm['can_update'] ? 'checked' : '' ?> onchange="updateCounter()">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check col-delete" name="permissions[<?= $id ?>][delete]" value="1" <?= $perm['can_delete'] ? 'checked' : '' ?> onchange="updateCounter()">
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="sticky-footer-custom">
                    <div class="small text-muted">
                        <i class="fas fa-info-circle text-info mr-1"></i> Perubahan izin akan langsung berlaku pada sesi login berikutnya.
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px; font-size: 0.82rem;">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan Akses
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function toggleColumn(type, isChecked) {
    document.querySelectorAll('.col-' + type).forEach(cb => cb.checked = isChecked);
    updateCounter();
}

function toggleAllCRUD(isChecked) {
    document.querySelectorAll('.perm-check:not(#header-check-read):not(#header-check-create):not(#header-check-update):not(#header-check-delete)').forEach(cb => cb.checked = isChecked);
    const hRead = document.getElementById('header-check-read'); if (hRead) hRead.checked = isChecked;
    const hCreate = document.getElementById('header-check-create'); if (hCreate) hCreate.checked = isChecked;
    const hUpdate = document.getElementById('header-check-update'); if (hUpdate) hUpdate.checked = isChecked;
    const hDelete = document.getElementById('header-check-delete'); if (hDelete) hDelete.checked = isChecked;
    updateCounter();
}

function updateCounter() {
    const checkedBoxes = document.querySelectorAll('.table-matrix tbody .perm-check:checked');
    const counterEl = document.getElementById('selected-count');
    if (counterEl) counterEl.textContent = checkedBoxes.length;
}

document.addEventListener('DOMContentLoaded', function() {
    updateCounter();
});
</script>

<?php
// INCLUDE FOOTER
$path_footer = __DIR__ . '/partials/footer.php';
if (file_exists($path_footer))
    include $path_footer;
?>