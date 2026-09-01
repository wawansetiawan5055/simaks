<?php
// app/views/landing_admin/ekskul_form.php
$title = "Form Ekstrakurikuler";
$is_edit = isset($ekskul);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-futbol mr-2"></i>
            <?= $is_edit ? 'Edit Ekstrakurikuler' : 'Tambah Ekstrakurikuler' ?>
        </h1>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="mb-0 font-weight-bold text-muted">
            <?= $is_edit ? 'FORM EDIT DATA' : 'FORM TAMBAH DATA' ?>
        </h6>
    </div>

    <form action="<?= BASE_URL ?>landing_admin/ekskul_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $ekskul['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold text-muted small">NAMA EKSTRAKURIKULER <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama"
                                value="<?= $is_edit ? htmlspecialchars($ekskul['nama']) : '' ?>" required
                                placeholder="Contoh: Pramuka">
                        </div>
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold text-muted small">TAUTKAN KE DATA OPERASIONAL <i class="fas fa-info-circle" title="Pilih ini agar Pembina, Jadwal, dan Anggota sinkron otomatis di halaman publik"></i></label>
                            <select class="form-control" name="ref_id">
                                <option value="">-- Tidak Ditautkan (Manual) --</option>
                                <?php foreach ($sync_data['ekskul'] ?? [] as $op): ?>
                                    <option value="<?= $op['id'] ?>" <?= ($is_edit && $ekskul['ref_id'] == $op['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($op['nama_kegiatan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold text-muted small">NAMA PEMBINA</label>
                            <input type="text" class="form-control" name="pembina"
                                value="<?= $is_edit ? htmlspecialchars($ekskul['pembina']) : '' ?>"
                                placeholder="Contoh: Bpk. Hidayat">
                        </div>
                        <div class="col-md-6 form-group mb-4">
                            <label class="font-weight-bold text-muted small">JADWAL LATIHAN</label>
                            <input type="text" class="form-control" name="jadwal"
                                value="<?= $is_edit ? htmlspecialchars($ekskul['jadwal']) : '' ?>"
                                placeholder="Contoh: Setiap Jumat, 14:00">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">DESKRIPSI <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="5" required
                            placeholder="Deskripsi singkat mengenai kegiatan ekskul..."><?= $is_edit ? htmlspecialchars($ekskul['deskripsi']) : '' ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-light p-3 rounded mb-4" style="border: 1px dashed #ced4da;">
                        <label class="font-weight-bold text-muted small">GAMBAR EKSKUL</label>
                        <?php if ($is_edit && !empty($ekskul['gambar'])): ?>
                            <div class="mb-3 text-center">
                                <img src="<?= BASE_URL . $ekskul['gambar'] ?>" alt="Preview"
                                    class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="gambar" name="gambar">
                            <label class="custom-file-label" for="gambar" data-browse="Pilih">Cari file...</label>
                        </div>
                        <small class="text-muted d-block mt-2"><i class="fas fa-info-circle mr-1"></i> Format: JPG/PNG,
                            Max 2MB.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">LOKASI LATIHAN</label>
                        <input type="text" class="form-control" name="lokasi"
                            value="<?= $is_edit ? htmlspecialchars($ekskul['lokasi']) : '' ?>"
                            placeholder="Contoh: Lapangan Sekolah">
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">ICON (FONT AWESOME)</label>
                        <input type="text" class="form-control" name="icon"
                            value="<?= $is_edit ? htmlspecialchars($ekskul['icon']) : 'fas fa-futbol' ?>"
                            placeholder="Contoh: fas fa-campground">
                        <small class="text-muted"><a href="https://fontawesome.com/v5/search" target="_blank"
                                class="text-primary">Lihat daftar icon <i
                                    class="fas fa-external-link-alt"></i></a></small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted small">URUTAN TAMPIL</label>
                        <input type="number" class="form-control" name="order_display"
                            value="<?= $is_edit ? ($ekskul['display_order'] ?? 1) : 1 ?>" min="1">
                    </div>

                    <div class="form-group mt-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                value="1" <?= (!$is_edit || $ekskul['is_active']) ? 'checked' : '' ?>>
                            <label class="custom-control-label font-weight-bold text-muted small"
                                style="padding-top:2px;" for="is_active">Aktifkan?</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-top py-3">
            <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold"><i
                    class="fas fa-save mr-2"></i> SIMPAN</button>
            <a href="<?= BASE_URL ?>landing_admin/ekskul" class="btn btn-light border px-4 ml-2">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>