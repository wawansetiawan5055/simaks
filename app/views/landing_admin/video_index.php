<?php
// app/views/landing_admin/video_index.php
$title = "Manajemen Video";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-video mr-2"></i> Manajemen Video</h1>
                <p class="text-muted small mb-0">Kelola galeri video kegiatan sekolah.</p>
            </div>
            <a href="<?= BASE_URL ?>landing_admin/video_form" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="fas fa-plus mr-2"></i> Tambah Baru
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
                            DAFTAR VIDEO</h6>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead style="background: #f8fafc;">
                        <tr class="text-muted">
                            <th class="text-center py-3 border-bottom"
                                style="width: 50px; font-size: 0.7rem; letter-spacing: 1px;">NO</th>
                            <th class="py-3 border-bottom" style="font-size: 0.7rem; letter-spacing: 1px;">JUDUL VIDEO
                            </th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">KATEGORI</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 100px;">STATUS</th>
                            <th class="text-center py-3 border-bottom"
                                style="font-size: 0.7rem; letter-spacing: 1px; width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($video_list)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small"><em>Belum ada data tersedia.</em>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($video_list as $index => $item): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted small">
                                        <?= $index + 1 ?>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex align-items-center">
                                            <?php
                                            // Handle YouTube Thumbnail
                                            $thumb = $item['thumbnail'];
                                            if (empty($thumb) && $item['tipe'] == 'youtube') {
                                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $item['video_url'], $matches);
                                                if (isset($matches[1])) {
                                                    $thumb = "https://img.youtube.com/vi/{$matches[1]}/default.jpg";
                                                }
                                            }
                                            ?>
                                            <?php if (!empty($thumb)): ?>
                                                <img src="<?= htmlspecialchars($thumb) ?>" alt="Thumb"
                                                    class="rounded mr-3 object-fit-cover"
                                                    style="width: 80px; height: 45px; border: 1px solid #ddd;">
                                            <?php else: ?>
                                                <div class="rounded mr-3 d-flex align-items-center justify-content-center bg-light text-primary"
                                                    style="width: 80px; height: 45px; border: 1px solid #ddd;">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <span class="font-weight-bold text-dark d-block" style="font-size: 0.95rem;">
                                                    <?= htmlspecialchars($item['judul']) ?>
                                                </span>
                                                <span class="text-muted small">
                                                    <?= htmlspecialchars(ucfirst($item['tipe'])) ?> &bull;
                                                    <?= htmlspecialchars($item['durasi'] ?: '-') ?>
                                                </span>
                                                <?php if ($item['is_featured']): ?>
                                                    <span class="badge badge-warning px-2 py-0 ml-1"
                                                        style="font-size: 0.6rem; border-radius: 10px;">Featured</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info px-3 py-2 shadow-none"
                                            style="font-size: 0.7rem; border-radius: 8px; font-weight: 600;">
                                            <?= htmlspecialchars($item['kategori'] ?: 'Umum') ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($item['is_active']): ?>
                                            <span class="badge badge-success px-2 py-1 shadow-none"
                                                style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1 shadow-none"
                                                style="font-size: 0.65rem; border-radius: 100px; font-weight: 600;">
                                                Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="<?= BASE_URL ?>landing_admin/video_form?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-warning border-0 p-1 mr-1"
                                                style="background: #fffbeb; width: 32px; height: 32px; border-radius: 8px; color: #d97706;"
                                                title="Edit">
                                                <i class="fas fa-pencil-alt" style="font-size: 0.85rem; line-height: 1.5;"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>landing_admin/video_delete?id=<?= $item['id'] ?>"
                                                class="btn btn-xs btn-outline-danger border-0 p-1"
                                                style="background: #fee; width: 32px; height: 32px; border-radius: 8px; color: #dc3545;"
                                                onclick="return confirmDelete(event)" title="Hapus">
                                                <i class="fas fa-trash-alt" style="font-size: 0.85rem; line-height: 1.5;"></i>
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

<?php include __DIR__ . '/../partials/footer.php'; ?>