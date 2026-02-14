<?php
// app/views/hak_akses_map_form.php

// 1. INCLUDE HEADER
// (Pastikan tidak ada spasi kosong di file header.php)
$path_header = __DIR__ . '/partials/header.php';
if (file_exists($path_header))
    include $path_header;
?>

<style>
    /* Style Matriks Perizinan Premium */
    .matrix-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
        border-bottom: 2px solid #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 15px;
    }

    .table-matrix tbody td {
        padding: 8px 15px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* Menu Grouping Styles */
    .row-header {
        background: #475569 !important;
        color: #fff !important;
    }

    .row-header td {
        font-weight: 800;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    .row-parent {
        background: #f8fafc !important;
    }

    .row-parent td {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.85rem;
    }

    .row-child td {
        font-size: 0.8rem;
        color: #475569;
    }

    .child-indent {
        display: flex;
        align-items: center;
    }

    .child-indent::before {
        content: '';
        width: 12px;
        height: 12px;
        border-left: 2px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
        margin-right: 10px;
        margin-top: -6px;
        border-bottom-left-radius: 4px;
    }

    /* Custom Checkbox Style */
    .perm-check {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #2563eb;
    }

    .cell-highlight:hover {
        background-color: #f1f5f9;
    }

    .sticky-footer-custom {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 15px;
        z-index: 20;
        text-align: center;
    }
</style>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1><i class="fas fa-user-shield mr-2"></i> Atur Izin: <?= htmlspecialchars($peran['nama_peran']) ?>
                </h1>
                <p class="text-muted small mb-0">Sesuaikan kemampuan baca, tulis, ubah, dan hapus untuk setiap modul
                    sistem.</p>
            </div>
            <a href="index.php?mod=hak_akses" class="btn btn-light btn-sm px-3 border shadow-none"
                style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid pf-0">
        <form action="index.php?mod=hak_akses&act=save_action" method="post" id="formPermissions">
            <input type="hidden" name="id_peran" value="<?= $peran['id_peran'] ?>">

            <div class="matrix-container">
                <table class="table table-matrix">
                    <thead>
                        <tr>
                            <th style="width: 40%;">MODUL / MENU SISTEM</th>
                            <th class="text-center" style="width: 15%;">READ</th>
                            <th class="text-center" style="width: 15%;">CREATE</th>
                            <th class="text-center" style="width: 15%;">UPDATE</th>
                            <th class="text-center" style="width: 15%;">DELETE</th>
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
                                        <i class="fas fa-layer-group mr-2 opacity-50"></i> <?= strtoupper($nama) ?>
                                    <?php elseif ($row_class == 'row-parent'): ?>
                                        <i class="<?= $icon ?> mr-2 text-primary shadow-sm" style="width: 20px;"></i>
                                        <?= $nama ?>
                                    <?php else: ?>
                                        <div class="child-indent"><?= $nama ?></div>
                                    <?php endif; ?>
                                </td>

                                <?php if ($row_class == 'row-header'): ?>
                                    <td colspan="4" class="text-center"><small class="opacity-50 font-weight-normal">Group
                                            Header</small></td>
                                <?php else: ?>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check" name="permissions[<?= $id ?>][read]" value="1"
                                            <?= $perm['can_read'] ? 'checked' : '' ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check" name="permissions[<?= $id ?>][create]"
                                            value="1" <?= $perm['can_create'] ? 'checked' : '' ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check" name="permissions[<?= $id ?>][update]"
                                            value="1" <?= $perm['can_update'] ? 'checked' : '' ?>>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="perm-check" name="permissions[<?= $id ?>][delete]"
                                            value="1" <?= $perm['can_delete'] ? 'checked' : '' ?>>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="sticky-footer-custom shadow-sm mt-0 border-top bg-light">
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm"
                        style="border-radius: 10px;">
                        <i class="fas fa-save mr-2"></i> SIMPAN PERUBAHAN AKSES
                    </button>
                    <p class="small text-muted mb-0 mt-2">Pastikan untuk memeriksa kembali setiap izin sebelum
                        menyimpan.</p>
                </div>
            </div>
        </form>
    </div>
</section>

<?php
// INCLUDE FOOTER
$path_footer = __DIR__ . '/partials/footer.php';
if (file_exists($path_footer))
    include $path_footer;
?>