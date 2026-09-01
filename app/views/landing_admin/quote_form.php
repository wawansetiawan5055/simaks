<?php
// app/views/landing_admin/quote_form.php
$title = "Form Quote / Hadits";
$is_edit = isset($quote);
include __DIR__ . '/../partials/header.php';
?>

<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-quote-left mr-2"></i> <?= $is_edit ? 'Edit Quote' : 'Tambah Quote Baru' ?></h1>
                <p class="text-muted small mb-0">Kelola kutipan atau hadits yang tampil di landing page.</p>
            </div>
            <a href="<?= BASE_URL ?>landing_admin/quotes" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-edit mr-2"></i> Data Quote</h5>
                    </div>
                    <form action="<?= BASE_URL ?>landing_admin/quote_save" method="post">
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="id" value="<?= $quote['id'] ?>">
                        <?php endif; ?>

                        <div class="card-body p-4">
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark">Isi Kutipan / Hadits <span class="text-danger">*</span></label>
                                <textarea class="form-control form-control-lg" name="text" rows="4" 
                                    placeholder="Masukkan teks kutipan..." required><?= $is_edit ? htmlspecialchars($quote['text']) : '' ?></textarea>
                                <small class="text-muted">Teks yang akan ditampilkan di slider beranda.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark">Sumber / Tokoh</label>
                                        <input type="text" class="form-control" name="source" 
                                            value="<?= $is_edit ? htmlspecialchars($quote['source']) : '' ?>"
                                            placeholder="Contoh: HR. Bukhari atau Albert Einstein">
                                        <small class="text-muted">Nama perawi, tokoh, atau sumber kutipan.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark">Urutan Tampil</label>
                                        <input type="number" class="form-control" name="position" 
                                            value="<?= $is_edit ? $quote['position'] : '0' ?>" min="0">
                                        <small class="text-muted">Angka lebih kecil tampil duluan.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light border-0 py-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="fas fa-save mr-2"></i> Simpan Quote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body p-4">
                        <h5 class="font-weight-bold text-info mb-3">Informasi</h5>
                        <p class="text-muted small" style="line-height: 1.6;">
                            Kutipan ini akan muncul secara otomatis di slider vertikal landing page. 
                            Gunakan bahasa yang inspiratif dan pastikan sumbernya akurat.
                        </p>
                        <hr>
                        <ul class="text-muted small ps-3">
                            <li>Maksimum 9 kutipan.</li>
                            <li>Teks quote sebaiknya tidak terlalu panjang agar slider tetap rapi.</li>
                            <li>Gunakan urutan '0' jika ingin kutipan terbaru langsung di atas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/../partials/footer.php';
?>
