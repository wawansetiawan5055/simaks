<?php
// app/views/landing_admin/program_form.php
$title = "Form Program Sekolah";
$is_edit = isset($program);
$sync_data = $sync_data ?? [];
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <h1 class="m-0 mb-3"><i class="fas fa-graduation-cap mr-2"></i>
            <?= $is_edit ? 'Edit Program' : 'Tambah Program' ?></h1>
    </div>
</div>

<div class="card card-primary card-outline shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white">
        <h3 class="card-title font-weight-bold"><?= $is_edit ? 'Edit Program Sekolah' : 'Tambah Program Sekolah Baru' ?></h3>
    </div>
    <form action="<?= BASE_URL ?>landing_admin/program_save" method="post" enctype="multipart/form-data">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $program['id'] ?>">
        <?php endif; ?>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <!-- DATA UTAMA WEBSITE -->
                    <div class="form-group">
                        <label class="font-weight-bold">Judul Program <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title"
                            value="<?= $is_edit ? htmlspecialchars($program['title']) : '' ?>" required
                            placeholder="Contoh: Tahfidz Al-Qur'an">
                        <small class="text-muted">Nama program yang akan ditampilkan di halaman utama website.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Deskripsi Program <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="5"
                            required placeholder="Jelaskan secara singkat mengenai program ini..."><?= $is_edit ? htmlspecialchars($program['description']) : '' ?></textarea>
                        <small class="text-muted">Gunakan bahasa yang menarik untuk calon siswa/orang tua.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Ikon Program (FontAwesome)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="<?= $is_edit ? htmlspecialchars($program['icon'] ?? 'fas fa-star') : 'fas fa-star' ?>" id="icon-preview"></i></span>
                            </div>
                            <input type="text" class="form-control" name="icon" id="icon-input"
                                value="<?= $is_edit ? htmlspecialchars($program['icon'] ?? 'fas fa-star') : 'fas fa-star' ?>"
                                placeholder="Contoh: fas fa-book-quran">
                        </div>
                        <small class="text-muted">Gunakan class FontAwesome (Contoh: fas fa-mosque, fas fa-heart).</small>
                    </div>
                </div>

                <div class="col-md-4 border-left">
                    <!-- SINKRONISASI DATA OPERASIONAL -->
                    <div class="bg-light p-3 rounded-lg mb-4">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-sync-alt mr-1"></i> SINKRONISASI DATA</h6>
                        <p class="small text-muted mb-3">Tautkan program ini dengan modul Administrasi Sekolah untuk menarik data Pembina, Jadwal, dan Peserta secara otomatis.</p>
                        
                        <div class="form-group">
                            <label class="small fw-bold">Modul Sumber</label>
                            <select class="form-control form-control-sm" name="ref_module" id="ref_module" onchange="updateRefIdDropdown()">
                                <option value="custom" <?= ($is_edit && $program['ref_module'] == 'custom') ? 'selected' : '' ?>>- Input Manual -</option>
                                <option value="tahfidz" <?= ($is_edit && $program['ref_module'] == 'tahfidz') ? 'selected' : '' ?>>Tahfidz</option>
                                <option value="ekskul" <?= ($is_edit && $program['ref_module'] == 'ekskul') ? 'selected' : '' ?>>Ekstrakurikuler</option>
                                <option value="wirausaha" <?= ($is_edit && $program['ref_module'] == 'wirausaha') ? 'selected' : '' ?>>Kewirausahaan</option>
                            </select>
                        </div>

                        <div id="ref_id_container" class="form-group" style="<?= ($is_edit && $program['ref_module'] != 'custom') ? '' : 'display:none;' ?>">
                            <label class="small fw-bold">Pilih Kegiatan/Kelompok</label>
                            <select class="form-control form-control-sm" name="ref_id" id="ref_id">
                                <option value="">- Pilih Kegiatan -</option>
                                <?php 
                                if ($is_edit && $program['ref_module'] != 'custom') {
                                    $current_module = $program['ref_module'];
                                    foreach ($sync_data[$current_module] as $opt) {
                                        $sel = ($program['ref_id'] == $opt['id']) ? 'selected' : '';
                                        echo "<option value='{$opt['id']}' $sel>{$opt['nama_kegiatan']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Gambar Banner</label>
                        <?php if ($is_edit && !empty($program['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL . $program['image'] ?>"
                                    alt="Gambar Program" class="img-fluid rounded border" style="max-height: 120px;">
                            </div>
                        <?php endif; ?>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label text-truncate" for="image"><?= $is_edit && !empty($program['image']) ? 'Ganti Gambar' : 'Pilih Gambar' ?></label>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                <?= (!$is_edit || $program['is_active']) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_active">Tampilkan di Website</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-top">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save mr-1"></i> SIMPAN PERUBAHAN
            </button>
            <a href="<?= BASE_URL ?>landing_admin/program" class="btn btn-link text-muted">
                Batal
            </a>
        </div>
    </form>
</div>

<!-- DATA UNTUK JAVASCRIPT DROPDOWN -->
<script>
    const syncOptions = <?= json_encode($sync_data) ?>;

    function updateRefIdDropdown() {
        const module = document.getElementById('ref_module').value;
        const container = document.getElementById('ref_id_container');
        const select = document.getElementById('ref_id');
        
        if (module === 'custom') {
            container.style.display = 'none';
            select.innerHTML = '<option value="">- Pilih Kegiatan -</option>';
        } else {
            container.style.display = 'block';
            let options = '<option value="">- Pilih Kegiatan -</option>';
            if (syncOptions[module]) {
                syncOptions[module].forEach(item => {
                    options += `<option value="${item.id}">${item.nama_kegiatan}</option>`;
                });
            }
            select.innerHTML = options;
        }
    }

    // Icon preview live
    $('#icon-input').on('keyup', function() {
        $('#icon-preview').attr('class', $(this).val() || 'fas fa-star');
    });

    // Custom File Input Label
    $('#image').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih Gambar');
    });
</script>

<?php
include __DIR__ . '/../partials/footer.php';
?>