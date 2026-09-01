<?php
// app/views/landing_admin/faq_index.php
$title = "Manajemen FAQ";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-question-circle mr-2"></i> FAQ</h1>
                <p class="text-muted small mb-0">Kelola pertanyaan yang sering ditanyakan.</p>
            </div>
            <a href="<?= BASE_URL ?>landing_admin/faq_form" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="fas fa-plus mr-2"></i> Tambah FAQ
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
                            DAFTAR FAQ</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-light border text-muted px-2 py-1"
                            style="font-size: 0.7rem; border-radius: 6px;">
                            <i class="fas fa-info-circle mr-1"></i> Total: <?= count($faq_list) ?> FAQ
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
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">PERTANYAAN</th>
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">JAWABAN</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">KATEGORI</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($faq_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">
                                    <em>Belum ada FAQ yang tersedia.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($faq_list as $index => $item): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted small">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="question-content">
                                            <span class="font-weight-bold text-dark d-block" style="font-size: 0.9rem;">
                                                <i class="fas fa-question-circle text-primary mr-2"></i>
                                                <?= htmlspecialchars($item['pertanyaan']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="answer-preview">
                                            <span class="text-muted small">
                                                <?= htmlspecialchars(substr(strip_tags($item['jawaban']), 0, 80)) ?>...
                                            </span>
                                            <?php if ($item['kontak']): ?>
                                                <br><small class="text-info">
                                                    <i class="fas fa-phone mr-1"></i>
                                                    Kontak: <?= htmlspecialchars($item['kontak']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info px-2 py-1 shadow-none"
                                            style="font-size: 0.7rem; border-radius: 6px;">
                                            <?= htmlspecialchars($item['kategori'] ?: 'Umum') ?>
                                        </span>
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
                                            <a href="<?= BASE_URL ?>landing_admin/faq_form?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                style="background: #fffbeb; width: 32px; height: 32px; border-radius: 8px; color: #d97706;"
                                                title="Edit">
                                                <i class="fas fa-pencil-alt" style="font-size: 0.85rem;"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>landing_admin/faq_delete?id=<?= $item['id'] ?>"
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

        <!-- Statistics -->
        <?php if (!empty($faq_list)): ?>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_filter($faq_list, fn($f) => $f['is_active'])) ?>
                        </h4>
                        <small class="text-muted">FAQ Aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-2x text-info mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_unique(array_filter(array_column($faq_list, 'kategori')))) ?>
                        </h4>
                        <small class="text-muted">Kategori</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1">
                            <?= count(array_filter($faq_list, fn($f) => !empty($f['kontak']))) ?>
                        </h4>
                        <small class="text-muted">Dengan Kontak</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Info Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Tips Pengelolaan FAQ
                        </h6>
                        <ul class="mb-0 small">
                            <li>Gunakan pertanyaan yang sering ditanyakan oleh calon siswa/orang tua</li>
                            <li>Jawaban harus jelas, lengkap, dan mudah dipahami</li>
                            <li>Kategorikan FAQ berdasarkan topik (Pendaftaran, Biaya, Kurikulum, dll)</li>
                            <li>Sertakan informasi kontak jika diperlukan untuk follow-up</li>
                            <li>Update FAQ secara berkala berdasarkan pertanyaan terbaru</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function confirmDelete(event) {
    if (!confirm('Apakah Anda yakin ingin menghapus FAQ ini?')) {
        event.preventDefault();
        return false;
    }
    return true;
}
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>