<?php
// app/views/landing_admin/sambutan_index.php
$title = "Manajemen Sambutan Kepala Sekolah";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-user-tie mr-2"></i> Sambutan Kepala Sekolah</h1>
                <p class="text-muted small mb-0">Kelola sambutan kepala sekolah untuk landing page.</p>
            </div>
            <a href="<?= BASE_URL ?>landing_admin/sambutan_form" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="fas fa-plus mr-2"></i> Tambah Sambutan
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
                            DAFTAR SAMBUTAN</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-light border text-muted px-2 py-1"
                            style="font-size: 0.7rem; border-radius: 6px;">
                            <i class="fas fa-info-circle mr-1"></i> Total: <?= count($sambutan_list) ?> sambutan
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
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">KEPALA SEKOLAH</th>
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">JUDUL</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">TANGGAL</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sambutan_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">
                                    <em>Belum ada sambutan yang tersedia.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sambutan_list as $index => $item): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted small">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex align-items-start">
                                            <div class="rounded d-flex align-items-center justify-content-center mr-2 bg-light text-primary font-weight-bold"
                                                style="width: 40px; height: 40px; min-width: 40px; font-size: 0.9rem;">
                                                <?php if ($item['foto']): ?>
                                                    <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($item['foto']) ?>"
                                                         alt="Foto" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fas fa-user-tie"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <span class="font-weight-bold text-dark d-block" style="font-size: 0.9rem;">
                                                    <?= htmlspecialchars($item['nama_kepala']) ?>
                                                </span>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($item['jabatan']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark d-block" style="font-size: 0.9rem;">
                                            <?= htmlspecialchars($item['judul']) ?>
                                        </span>
                                        <small class="text-muted">
                                            <?= substr(strip_tags($item['konten']), 0, 80) ?>...
                                        </small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <code class="text-muted small">
                                            <?= date('d/m/Y', strtotime($item['tanggal_update'])) ?>
                                        </code>
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
                                                <i class="fas fa-pause mr-1"></i> Non-aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="<?= BASE_URL ?>landing_admin/sambutan_form?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                style="background: #fffbeb; width: 32px; height: 32px; border-radius: 8px; color: #d97706;"
                                                title="Edit">
                                                <i class="fas fa-pencil-alt" style="font-size: 0.85rem;"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>landing_admin/sambutan_delete?id=<?= $item['id'] ?>"
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

        <!-- Info Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title text-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Informasi Penting
                        </h6>
                        <ul class="mb-0 small">
                            <li>Hanya satu sambutan yang bisa aktif dalam satu waktu</li>
                            <li>Sambutan aktif akan ditampilkan di halaman publik</li>
                            <li>Pastikan foto kepala sekolah dalam format yang sesuai</li>
                            <li>Konten sambutan akan ditampilkan dengan formatting HTML</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function confirmDelete(event) {
    if (!confirm('Apakah Anda yakin ingin menghapus sambutan ini?')) {
        event.preventDefault();
        return false;
    }
    return true;
}
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>