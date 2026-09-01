<?php
// app/views/landing_admin/settings.php
$title = "Pengaturan Landing Page";
include __DIR__ . '/../partials/header.php';
?>

<!-- Content Header -->
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-globe mr-2"></i> Pengaturan Landing Page</h1>
                <p class="text-muted small mb-0">Kelola konfigurasi tampilan halaman depan publik.</p>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header bg-gradient-primary text-white py-3"
                        style="border-bottom: 3px solid #0056b3;">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-cogs mr-2"></i> Konfigurasi Umum</h5>
                    </div>
                    <form action="<?= BASE_URL ?>landing_admin/save_settings" method="post"
                        enctype="multipart/form-data">
                        <div class="card-body p-4">
                            <!-- Status Landing Page -->
                            <div class="card shadow-none border mb-4" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="font-weight-bold mb-1 text-dark">Status Landing Page</h6>
                                        <p class="text-muted small mb-0">Aktifkan atau nonaktifkan tampilan halaman depan publik.</p>
                                    </div>
                                    <div class="custom-control custom-switch custom-control-lg">
                                        <input type="checkbox" class="custom-control-input" id="landing_page_enabled"
                                            name="landing_page_enabled" value="1" <?= ($settings['landing_page_enabled'] ?? '') == '1' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="landing_page_enabled"></label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Nama Sekolah -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Nama Sekolah (Publik)</label>
                                <input type="text" class="form-control form-control-lg" name="school_name"
                                    value="<?= htmlspecialchars($settings['school_name'] ?? '') ?>"
                                    placeholder="Nama Sekolah Anda">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">Tahun Berdiri</label>
                                    <input type="number" class="form-control" name="tahun_berdiri"
                                        value="<?= htmlspecialchars($settings['tahun_berdiri'] ?? '') ?>"
                                        placeholder="Contoh: 2010">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark">Tahun Perubahan / Revisi</label>
                                    <input type="number" class="form-control" name="tahun_perubahan"
                                        value="<?= htmlspecialchars($settings['tahun_perubahan'] ?? '') ?>"
                                        placeholder="Tahun Izin Terakhir">
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Alamat</label>
                                <textarea class="form-control" name="school_address" rows="3"
                                    placeholder="Alamat Sekolah..."><?= htmlspecialchars($settings['school_address'] ?? '') ?></textarea>
                            </div>

                            <!-- Kontak -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark d-block mb-3">Kontak</label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fas fa-phone text-primary"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 pl-0"
                                                name="school_phone"
                                                value="<?= htmlspecialchars($settings['school_phone'] ?? '') ?>"
                                                placeholder="021-xxxxxx">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fas fa-envelope text-primary"></i></span>
                                            </div>
                                            <input type="email" class="form-control border-left-0 pl-0"
                                                name="school_email"
                                                value="<?= htmlspecialchars($settings['school_email'] ?? '') ?>"
                                                placeholder="info@sekolah.sch.id">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fas fa-globe text-primary"></i></span>
                                            </div>
                                            <input type="url" class="form-control border-left-0 pl-0"
                                                name="school_website"
                                                value="<?= htmlspecialchars($settings['school_website'] ?? '') ?>"
                                                placeholder="Website (https://...)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Media Sosial -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark d-block mb-3">Media Sosial</label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fab fa-facebook-f text-primary"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 pl-0"
                                                name="facebook_url"
                                                value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>"
                                                placeholder="Facebook URL">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fab fa-instagram text-primary"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 pl-0"
                                                name="instagram_url"
                                                value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>"
                                                placeholder="Instagram URL">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fab fa-youtube text-primary"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 pl-0"
                                                name="youtube_url"
                                                value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>"
                                                placeholder="YouTube URL">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fab fa-whatsapp text-success"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 pl-0"
                                                name="whatsapp_sekolah"
                                                value="<?= htmlspecialchars($settings['whatsapp_sekolah'] ?? '') ?>"
                                                placeholder="WhatsApp Sekolah (0812...)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Interval Slider -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Interval Slider</label>
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="number" class="form-control" name="landing_slider_interval"
                                        value="<?= htmlspecialchars($settings['landing_slider_interval'] ?? '5000') ?>"
                                        min="1000" step="500">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-light">milidetik</span>
                                    </div>
                                </div>
                                <small class="text-muted">Durasi setiap slide dalam milidetik (1000ms = 1 detik)</small>
                            </div>

                            <hr class="my-4">

                            <!-- Identitas Tambahan -->
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-id-card mr-2"></i> Identitas
                                & Profil</h6>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Logo Sekolah</label>
                                <?php if (!empty($settings['school_logo'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= BASE_URL . $settings['school_logo'] ?>" alt="Logo"
                                            style="height: 60px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="school_logo" name="school_logo">
                                    <label class="custom-file-label" for="school_logo">Cari gambar logo...</label>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Motto Sekolah</label>
                                <input type="text" class="form-control" name="school_motto"
                                    value="<?= htmlspecialchars($settings['school_motto'] ?? '') ?>"
                                    placeholder="Contoh: Cerdas, Berakhlaq, dan Mandiri">
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Akreditasi</label>
                                <input type="text" class="form-control" name="school_accreditation"
                                    value="<?= htmlspecialchars($settings['school_accreditation'] ?? '') ?>"
                                    placeholder="Contoh: Akreditasi A (Unggul)">
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Sejarah Sekolah (Lengkap)</label>
                                <textarea class="form-control" name="school_description" rows="5"
                                    placeholder="Tuliskan sejarah lengkap sekolah..."><?= htmlspecialchars($settings['school_description'] ?? '') ?></textarea>
                                <small class="text-muted">Teks ini akan muncul di halaman detail profil.</small>
                            </div>

                            <hr class="my-4">
                            <div class="bg-light p-3 border mb-4" style="border-radius: 10px; border-left: 4px solid #17a2b8 !important;">
                                <h6 class="font-weight-bold text-info mb-3"><i class="fas fa-star mr-2"></i> Highlight Beranda</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark">Foto Highight Profil (Beranda)</label>
                                    <?php if (!empty($settings['landing_school_profile_image'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= BASE_URL . $settings['landing_school_profile_image'] ?>" alt="Profile Highlight"
                                                style="max-width: 300px; border-radius: 8px; border: 1px solid #ddd;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="landing_school_profile_image" name="landing_school_profile_image">
                                        <label class="custom-file-label" for="landing_school_profile_image">Cari foto highlight...</label>
                                    </div>
                                    <small class="text-muted">Foto ini akan tampil lebar (full width) di bagian Profil Beranda. Rekomendasi: 800x400px.</small>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark">Ulasan Singkat Profil (Highlight)</label>
                                    <textarea class="form-control" name="landing_school_profile_excerpt" rows="3"
                                        placeholder="Tulis ulasan singkat yang menarik untuk beranda..."><?= htmlspecialchars($settings['landing_school_profile_excerpt'] ?? '') ?></textarea>
                                    <small class="text-muted">Ringkasan profil yang tampil di beranda sebelum tombol Selengkapnya.</small>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Visi Sekolah</label>
                                <textarea class="form-control" name="school_vision" rows="3"
                                    placeholder="Tuliskan visi sekolah..."><?= htmlspecialchars($settings['school_vision'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Misi Sekolah</label>
                                <textarea class="form-control" name="school_mission" rows="5"
                                    placeholder="Tuliskan misi sekolah (poin-poin)..."><?= htmlspecialchars($settings['school_mission'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Tujuan Sekolah</label>
                                <textarea class="form-control" name="school_goals" rows="5"
                                    placeholder="Tuliskan tujuan sekolah (poin-poin)..."><?= htmlspecialchars($settings['school_goals'] ?? '') ?></textarea>
                            </div>

                            <hr class="my-4">

                            <!-- Profil Kepala Sekolah -->
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-user-tie mr-2"></i> Profil
                                Kepala Sekolah</h6>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Nama Kepala Sekolah</label>
                                <input type="text" class="form-control" name="headmaster_name"
                                    value="<?= htmlspecialchars($settings['headmaster_name'] ?? '') ?>"
                                    placeholder="Nama Lengkap & Gelar">
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Foto Kepala Sekolah</label>
                                <?php if (!empty($settings['headmaster_photo'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= BASE_URL . $settings['headmaster_photo'] ?>" alt="Kepsek"
                                            style="height: 100px; border-radius: 8px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="headmaster_photo"
                                        name="headmaster_photo">
                                    <label class="custom-file-label" for="headmaster_photo">Cari foto...</label>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark">Sambutan Kepala Sekolah</label>
                                <textarea class="form-control" name="headmaster_message" rows="5"
                                    placeholder="Teks sambutan kepala sekolah..."><?= htmlspecialchars($settings['headmaster_message'] ?? '') ?></textarea>
                            </div>

                            <hr class="my-4">
                            <!-- Quotes / Hadits -->
                            <div class="card shadow-none border bg-light mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-transparent border-0 pt-3 flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-quote-left mr-2"></i> Quotes / Hadits</h6>
                                        <button type="button" class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalQuote">
                                            <i class="fas fa-plus mr-1"></i> Tambah Quote
                                        </button>
                                    </div>
                                    <p class="text-muted small mb-0">Kelola kutipan/hadits yang tampil di beranda (Maks 9).</p>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 60%;">Kutipan</th>
                                                    <th>Sumber</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($quotes)): ?>
                                                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada kutipan.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($quotes as $q): ?>
                                                        <tr>
                                                            <td class="text-truncate" style="max-width: 250px;"><?= htmlspecialchars($q['quote_text']) ?></td>
                                                            <td><span class="badge badge-info"><?= htmlspecialchars($q['quote_source'] ?: 'Anonim') ?></span></td>
                                                            <td class="text-center">
                                                                <a href="<?= BASE_URL ?>landing_admin/quote_delete?id=<?= $q['id'] ?>" class="text-danger" onclick="return confirm('Hapus kutipan ini?');">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <!-- Tautan Penting -->
                            <div class="card shadow-none border bg-light mb-0" style="border-radius: 12px; border-left: 5px solid #007bff !important;">
                                <div class="card-header bg-transparent border-0 pt-3 flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-link mr-2"></i> Tautan Penting (Sidebar)</h6>
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalLink">
                                            <i class="fas fa-plus mr-1"></i> Tambah Tautan
                                        </button>
                                    </div>
                                    <p class="text-muted small mb-0">Kelola daftar tautan cepat yang tampil di sidebar beranda.</p>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 40px;">Ikon</th>
                                                    <th>Judul</th>
                                                    <th>URL</th>
                                                    <th class="text-center">Urutan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($links)): ?>
                                                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada tautan.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($links as $l): ?>
                                                        <tr>
                                                            <td class="text-center"><i class="<?= htmlspecialchars($l['icon']) ?> text-primary"></i></td>
                                                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($l['title']) ?></td>
                                                            <td class="text-truncate" style="max-width: 150px;"><code class="small text-muted"><?= htmlspecialchars($l['url']) ?></code></td>
                                                            <td class="text-center"><?= $l['display_order'] ?></td>
                                                            <td class="text-center">
                                                                <a href="<?= BASE_URL ?>landing_admin/links_delete?id=<?= $l['id'] ?>" class="text-danger" onclick="return confirm('Hapus tautan ini?');">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3">
                            <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Pengaturan
                            </button>
                            <button type="reset" class="btn btn-secondary float-right">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm"
                    style="border-radius: 15px; overflow: hidden; border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info p-3 mr-3"
                                style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-info-circle text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5 class="mb-0 font-weight-bold text-info">Informasi</h5>
                        </div>
                        <p class="text-muted" style="line-height: 1.7;">
                            Pengaturan ini mengontrol tampilan halaman depan (<strong>Landing Page</strong>) yang dapat
                            diakses oleh
                            publik tanpa login.
                        </p>
                        <p class="mb-0 text-muted" style="line-height: 1.7;">
                            Gunakan menu <strong>Berita</strong> dan <strong>Galeri</strong> untuk mengelola konten yang
                            tampil.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Custom styling for seamless input groups */
    .input-group-text {
        background: transparent !important;
        border: none !important;
        padding-left: 1rem;
        padding-right: 0.5rem;
    }

    .input-group .form-control {
        height: calc(2.75rem + 2px);
        border-left: none !important;
        padding-left: 0 !important;
    }

    .input-group {
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.2s;
    }

    .input-group:focus-within {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }

    .custom-control-lg .custom-control-label {
        font-size: 1.05rem;
        padding-top: 0.15rem;
    }

    .custom-control-lg .custom-control-input {
        width: 3rem;
        height: 1.5rem;
    }

    .custom-control-lg .custom-control-label::before {
        width: 3rem;
        height: 1.5rem;
        border-radius: 1rem;
    }

    .custom-control-lg .custom-control-label::after {
        width: calc(1.5rem - 4px);
        height: calc(1.5rem - 4px);
    }

    .custom-control-lg .custom-control-input:checked~.custom-control-label::after {
        transform: translateX(1.5rem);
    }

    /* Better form control styling */
    .form-control-lg {
        font-size: 1.1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
    }

    textarea.form-control {
        border-radius: 0.5rem;
    }

    /* Number input specific */
    input[type="number"].form-control {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .input-group-append .input-group-text {
        border-left: 1px solid #ced4da !important;
        padding-left: 1rem;
        padding-right: 1rem;
    }
</style>

<!-- Modal Tambah Quote -->
<div class="modal fade" id="modalQuote" tabindex="-1" role="dialog" aria-labelledby="modalQuoteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title" id="modalQuoteLabel"><i class="fas fa-quote-left mr-2"></i> Tambah Quote Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>landing_admin/quote_save" method="post">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Isi Kutipan / Hadits <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="text" rows="4" placeholder="Tulis kutipan di sini..." required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Sumber / Tokoh</label>
                                <input type="text" class="form-control" name="source" placeholder="Contoh: HR. Bukhari">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Posisi</label>
                                <input type="number" class="form-control" name="position" value="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Quote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Tautan -->
<div class="modal fade" id="modalLink" tabindex="-1" role="dialog" aria-labelledby="modalLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title" id="modalLinkLabel"><i class="fas fa-link mr-2"></i> Tambah Tautan Penting</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= BASE_URL ?>landing_admin/links_save" method="post">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Judul Tautan <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Portal SIMAKS" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">URL / Link <span class="text-danger">*</span></label>
                        <input type="text" name="url" class="form-control" placeholder="Contoh: https://facebook.com/ atau index.php?mod=..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark">Ikon (FontAwesome)</label>
                                <input type="text" name="icon" class="form-control" placeholder="Contoh: fas fa-link" value="fas fa-link">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark">Urutan</label>
                                <input type="number" name="display_order" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_active" value="1">
                </div>
                <div class="modal-footer bg-light border-0 py-3" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Tautan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include __DIR__ . '/../partials/footer.php';
?>