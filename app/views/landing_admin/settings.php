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
                    <form action="index.php?mod=landing_admin&act=save_settings" method="post">
                        <div class="card-body p-4">
                            <!-- Status Landing Page -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark d-block mb-3">Status Landing Page</label>
                                <div class="custom-control custom-switch custom-control-lg">
                                    <input type="checkbox" class="custom-control-input" id="landing_page_enabled"
                                        name="landing_page_enabled" value="1" <?= ($settings['landing_page_enabled'] ?? '') == '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label font-weight-semibold"
                                        for="landing_page_enabled">Aktifkan Halaman Depan Publik</label>
                                </div>
                                <small class="text-muted d-block mt-2">Jika nonaktif, pengunjung akan langsung diarahkan
                                    ke halaman Login.</small>
                            </div>

                            <hr class="my-4">

                            <!-- Nama Sekolah -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Nama Sekolah (Publik)</label>
                                <input type="text" class="form-control form-control-lg" name="school_name"
                                    value="<?= htmlspecialchars($settings['school_name'] ?? '') ?>"
                                    placeholder="Nama Sekolah Anda">
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
                                                name="social_facebook"
                                                value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>"
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
                                                name="social_instagram"
                                                value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>"
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
                                                name="social_youtube"
                                                value="<?= htmlspecialchars($settings['social_youtube'] ?? '') ?>"
                                                placeholder="YouTube URL">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-right-0"><i
                                                        class="fab fa-twitter text-primary"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 pl-0"
                                                name="social_twitter"
                                                value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>"
                                                placeholder="Twitter URL">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Interval Slider -->
                            <div class="form-group mb-0">
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

<?php
include __DIR__ . '/../partials/footer.php';
?>