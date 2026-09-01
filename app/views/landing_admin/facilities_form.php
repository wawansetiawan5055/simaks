<?php
// app/views/landing_admin/facilities_form.php
$title = "Form Fasilitas Sekolah";
$is_edit = isset($facility);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-building mr-2"></i> <?= $is_edit ? 'Edit Fasilitas' : 'Tambah Fasilitas' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?= $is_edit ? 'Edit Fasilitas Sekolah' : 'Tambah Fasilitas Sekolah Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/facilities_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $facility['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="form-group">
                <label>Nama Fasilitas <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_fasilitas"
                    value="<?= $is_edit ? htmlspecialchars($facility['nama_fasilitas']) : '' ?>" required>
                <small class="text-muted">Nama fasilitas yang akan ditampilkan</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select class="form-control" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Ruang Kelas" <?= ($is_edit && $facility['kategori'] == 'Ruang Kelas') ? 'selected' : '' ?>>Ruang Kelas</option>
                            <option value="Laboratorium" <?= ($is_edit && $facility['kategori'] == 'Laboratorium') ? 'selected' : '' ?>>Laboratorium</option>
                            <option value="Perpustakaan" <?= ($is_edit && $facility['kategori'] == 'Perpustakaan') ? 'selected' : '' ?>>Perpustakaan</option>
                            <option value="Olahraga" <?= ($is_edit && $facility['kategori'] == 'Olahraga') ? 'selected' : '' ?>>Olahraga</option>
                            <option value="Kesehatan" <?= ($is_edit && $facility['kategori'] == 'Kesehatan') ? 'selected' : '' ?>>Kesehatan</option>
                            <option value="Administrasi" <?= ($is_edit && $facility['kategori'] == 'Administrasi') ? 'selected' : '' ?>>Administrasi</option>
                            <option value="Lainnya" <?= ($is_edit && $facility['kategori'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="Tersedia" <?= ($is_edit && $facility['status'] == 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
                            <option value="Dalam Perbaikan" <?= ($is_edit && $facility['status'] == 'Dalam Perbaikan') ? 'selected' : '' ?>>Dalam Perbaikan</option>
                            <option value="Tidak Tersedia" <?= ($is_edit && $facility['status'] == 'Tidak Tersedia') ? 'selected' : '' ?>>Tidak Tersedia</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Fasilitas <span class="text-danger">*</span></label>
                <textarea class="form-control" name="deskripsi" rows="4" required><?= $is_edit ? htmlspecialchars($facility['deskripsi']) : '' ?></textarea>
                <small class="text-muted">Deskripsi lengkap tentang fasilitas ini</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kapasitas</label>
                        <input type="text" class="form-control" name="kapasitas"
                            value="<?= $is_edit ? htmlspecialchars($facility['kapasitas']) : '' ?>"
                            placeholder="Contoh: 30 Siswa, 50 Orang, dll">
                        <small class="text-muted">Kapasitas atau ukuran fasilitas</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" class="form-control" name="lokasi"
                            value="<?= $is_edit ? htmlspecialchars($facility['lokasi']) : '' ?>"
                            placeholder="Contoh: Lantai 1, Gedung A, dll">
                        <small class="text-muted">Lokasi fasilitas di sekolah</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Fasilitas Tambahan</label>
                <textarea class="form-control" name="fasilitas_tambahan" rows="3" placeholder="Masukkan fasilitas tambahan, satu per baris"><?= $is_edit ? htmlspecialchars($facility['fasilitas_tambahan']) : '' ?></textarea>
                <small class="text-muted">Fasilitas tambahan yang tersedia, satu per baris. Contoh:<br>• AC<br>• Proyektor<br>• Komputer</small>
            </div>

            <div class="form-group">
                <label>Gambar Fasilitas</label>
                <?php if ($is_edit && !empty($facility['gambar'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL ?>uploads/landing/<?= htmlspecialchars($facility['gambar']) ?>"
                             alt="Gambar Fasilitas" style="max-height: 150px; border-radius: 8px;">
                        <small class="text-muted d-block mt-1">Gambar saat ini</small>
                    </div>
                <?php endif; ?>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="gambar" name="gambar" accept="image/*">
                    <label class="custom-file-label" for="gambar">Pilih gambar fasilitas...</label>
                </div>
                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB. Rekomendasi ukuran: 400x300px</small>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                        <?= (!$is_edit || $facility['is_active']) ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="is_active">Tampilkan di Website</label>
                </div>
                <small class="text-muted">Fasilitas aktif akan ditampilkan di halaman publik</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Fasilitas
            </button>
            <a href="<?= BASE_URL ?>landing_admin/facilities" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
// Update file input label
$('#gambar').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName || 'Pilih gambar fasilitas...');
});
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>