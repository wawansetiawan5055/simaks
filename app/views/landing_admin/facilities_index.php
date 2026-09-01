<?php
// app/views/landing_admin/facilities_index.php
$title = "Manajemen Fasilitas Sekolah";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-building mr-2"></i> Fasilitas Sekolah</h1>
                <p class="text-muted small mb-0">Kelola fasilitas dan infrastruktur sekolah.</p>
            </div>
            <a href="<?= BASE_URL ?>landing_admin/facilities_form" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="fas fa-plus mr-2"></i> Tambah Fasilitas
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
                            DAFTAR FASILITAS</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-light border text-muted px-2 py-1"
                            style="font-size: 0.7rem; border-radius: 6px;">
                            <i class="fas fa-info-circle mr-1"></i> Total: <?= count($facilities_list) ?> fasilitas
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
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">FASILITAS</th>
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">DESKRIPSI</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">KAPASITAS</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($facilities_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">
                                    <em>Belum ada fasilitas yang tersedia.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($facilities_list as $index => $item): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted small">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex align-items-start">
                                            <div class="rounded d-flex align-items-center justify-content-center mr-2 bg-light text-primary font-weight-bold"
                                                style="width: 40px; height: 40px; min-width: 40px; font-size: 0.9rem;">
                                                <?php if ($item['gambar']): ?>
                                                    <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($item['gambar']) ?>"
                                                         alt="Fasilitas" class="rounded" style="width: 36px; height: 36px; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fas fa-building"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold text-dark d-block" style="font-size: 0.9rem;">
                                                    <?= htmlspecialchars($item['nama_fasilitas']) ?>
                                                </span>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($item['kategori']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted small">
                                            <?= substr(strip_tags($item['deskripsi']), 0, 100) ?>...
                                        </span>
                                        <?php if ($item['lokasi']): ?>
                                            <br><small class="text-info">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                <?= htmlspecialchars($item['lokasi']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info px-2 py-1 shadow-none"
                                            style="font-size: 0.7rem; border-radius: 6px;">
                                            <?= htmlspecialchars($item['kapasitas'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php
                                        $status_class = 'secondary';
                                        $status_icon = 'pause';
                                        $status_text = 'Non-aktif';

                                        if ($item['status'] === 'Tersedia') {
                                            $status_class = 'success';
                                            $status_icon = 'check-circle';
                                            $status_text = 'Tersedia';
                                        } elseif ($item['status'] === 'Dalam Perbaikan') {
                                            $status_class = 'warning';
                                            $status_icon = 'tools';
                                            $status_text = 'Perbaikan';
                                        } elseif ($item['status'] === 'Tidak Tersedia') {
                                            $status_class = 'danger';
                                            $status_icon = 'times-circle';
                                            $status_text = 'Tidak Tersedia';
                                        }
                                        ?>
                                        <span class="badge badge-<?= $status_class ?> px-2 py-1 shadow-none"
                                            style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">
                                            <i class="fas fa-<?= $status_icon ?> mr-1"></i> <?= $status_text ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="<?= BASE_URL ?>landing_admin/facilities_form?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                style="background: #fffbeb; width: 32px; height: 32px; border-radius: 8px; color: #d97706;"
                                                title="Edit">
                                                <i class="fas fa-pencil-alt" style="font-size: 0.85rem;"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>landing_admin/facilities_delete?id=<?= $item['id'] ?>"
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

        <!-- Statistics Cards -->
        <?php if (!empty($facilities_list)): ?>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_filter($facilities_list, fn($f) => $f['status'] === 'Tersedia')) ?>
                        </h4>
                        <small class="text-muted">Tersedia</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-tools fa-2x text-warning mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_filter($facilities_list, fn($f) => $f['status'] === 'Dalam Perbaikan')) ?>
                        </h4>
                        <small class="text-muted">Perbaikan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_filter($facilities_list, fn($f) => $f['status'] === 'Tidak Tersedia')) ?>
                        </h4>
                        <small class="text-muted">Tidak Tersedia</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-list fa-2x text-info mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_unique(array_column($facilities_list, 'kategori'))) ?>
                        </h4>
                        <small class="text-muted">Kategori</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function confirmDelete(event) {
    if (!confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')) {
        event.preventDefault();
        return false;
    }
    return true;
}
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>