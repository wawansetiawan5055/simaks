<?php
// app/views/landing_admin/gallery_index.php
$title = "Manajemen Galeri Foto";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-images mr-2"></i> Daftar Foto Galeri</h1>
                <p class="text-muted small mb-0">Kelola foto dan gambar untuk landing page.</p>
            </div>
            <a href="index.php?mod=landing_admin&act=gallery_form" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="fas fa-plus mr-2"></i> Tambah Foto
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
                        <h6 class="mb-0 font-weight-bold text-muted"><i class="fas fa-th mr-2 text-primary"></i> GALERI
                            FOTO</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="badge badge-light border text-muted px-2 py-1"
                            style="font-size: 0.7rem; border-radius: 6px;">
                            <i class="fas fa-info-circle mr-1"></i> Total: <?= count($gallery_list) ?> foto
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <?php if (empty($gallery_list)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-image text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3"><em>Belum ada foto yang tersedia.</em></p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($gallery_list as $item): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card border-0 shadow-sm h-100"
                                    style="border-radius: 12px; overflow: hidden; transition: transform 0.2s;">
                                    <!-- Image -->
                                    <div class="position-relative"
                                        style="height: 200px; overflow: hidden; background: #f8f9fa;">
                                        <img src="<?= BASE_URL . $item['image_path'] ?>" class="w-100 h-100"
                                            alt="<?= htmlspecialchars($item['title']) ?>" style="object-fit: cover;">
                                        <div class="position-absolute" style="top: 10px; right: 10px;">
                                            <?php if ($item['is_slider']): ?>
                                                <span class="badge badge-warning px-2 py-1 shadow-sm"
                                                    style="font-size: 0.65rem; border-radius: 100px;">
                                                    <i class="fas fa-star mr-1"></i> Slider
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Body -->
                                    <div class="card-body p-3">
                                        <h6 class="card-title font-weight-bold text-dark mb-2"
                                            style="font-size: 0.9rem; line-height: 1.3;">
                                            <?= htmlspecialchars($item['title']) ?>
                                        </h6>
                                        <div class="mb-0">
                                            <span class="badge badge-info px-2 py-1"
                                                style="font-size: 0.7rem; border-radius: 6px;">
                                                <i class="fas fa-tag mr-1"></i> <?= $item['category'] ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div class="card-footer bg-light border-0 p-2">
                                        <div class="d-flex justify-content-center">
                                            <a href="index.php?mod=landing_admin&act=gallery_form&id=<?= $item['id'] ?>"
                                                class="btn btn-sm btn-outline-warning border-0 px-3 mr-2"
                                                style="background: #fffbeb; color: #d97706; border-radius: 8px;" title="Edit">
                                                <i class="fas fa-pencil-alt mr-1" style="font-size: 0.8rem;"></i> Edit
                                            </a>
                                            <a href="index.php?mod=landing_admin&act=gallery_delete&id=<?= $item['id'] ?>"
                                                class="btn btn-sm btn-outline-danger border-0 px-3"
                                                style="background: #fee; color: #dc3545; border-radius: 8px;"
                                                onclick="return confirmDelete(event)" title="Hapus">
                                                <i class="fas fa-trash-alt mr-1" style="font-size: 0.8rem;"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    /* Gallery hover effect */
    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<?php
include __DIR__ . '/../partials/footer.php';
?>