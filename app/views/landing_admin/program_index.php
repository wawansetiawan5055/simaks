<?php
// app/views/landing_admin/program_index.php
$title = "Manajemen Program Sekolah";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-graduation-cap mr-2"></i> Program Sekolah</h1>
                <p class="text-muted small mb-0">Kelola program pendidikan yang ditawarkan sekolah.</p>
            </div>
            <a href="<?= BASE_URL ?>landing_admin/program_form" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="fas fa-plus mr-2"></i> Tambah Program
            </a>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-list-ul mr-2 text-primary"></i>
                            DAFTAR PROGRAM</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-light border text-muted px-2 py-1"
                            style="font-size: 0.7rem; border-radius: 6px;">
                            <i class="fas fa-info-circle mr-1"></i> Total: <?= is_array($program_list) ? count($program_list) : 0 ?> program
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead style="background: #f8fafc;">
                        <tr class="text-muted">
                            <th class="text-center py-3 border-bottom"
                                style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">PROGRAM</th>
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">DESKRIPSI
                            </th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($program_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">
                                    <em>Belum ada program yang tersedia.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($program_list as $index => $item): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted small">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex align-items-start">
                                            <div class="rounded d-flex align-items-center justify-content-center mr-2 bg-light text-primary font-weight-bold"
                                                style="width: 40px; height: 40px; min-width: 40px; font-size: 0.9rem;">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="<?= BASE_URL . $item['image'] ?>"
                                                        alt="Program" class="rounded"
                                                        style="width: 36px; height: 36px; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fas fa-graduation-cap"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold text-dark d-block" style="font-size: 0.9rem;">
                                                    <?= htmlspecialchars($item['title']) ?>
                                                </span>
                                                <?php if (!empty($item['ref_module']) && $item['ref_module'] !== 'custom'): ?>
                                                    <small class="text-success font-weight-bold">
                                                        <i class="fas fa-sync-alt"></i> Terhubung: <?= ucfirst($item['ref_module']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted small">
                                            <?= substr(strip_tags($item['description']), 0, 100) ?>...
                                        </span>
                                        <?php if (!empty($item['icon'])): ?>
                                            <br><small class="text-primary">
                                                <i class="<?= htmlspecialchars($item['icon']) ?> mr-1"></i> Ikon Aktif
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($item['is_active']): ?>
                                            <span class="badge badge-success px-2 py-1 shadow-none"
                                                style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">
                                                <i class="fas fa-check-circle mr-1"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1 shadow-none"
                                                style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">
                                                <i class="fas fa-pause mr-1"></i> Draft
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="<?= BASE_URL ?>landing_admin/program_form?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                style="background: #fffbeb; width: 32px; height: 32px; border-radius: 8px; color: #d97706;"
                                                title="Edit">
                                                <i class="fas fa-pencil-alt" style="font-size: 0.85rem;"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>landing_admin/program_delete?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-danger border-0 p-1"
                                                style="background: #fee; width: 32px; height: 32px; border-radius: 8px; color: #dc3545;"
                                                onclick="return confirmDelete(event)" title="Hapus">
                                                <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    function confirmDelete(event) {
        if (!confirm('Apakah Anda yakin ingin menghapus program ini?')) {
            event.preventDefault();
            return false;
        }
        return true;
    }
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>