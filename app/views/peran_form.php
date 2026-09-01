<?php include __DIR__ . '/partials/header.php'; ?>
<?php
$is_edit = isset($peran) && $peran !== null;
$action_url = BASE_URL . 'peran/save_action';
?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="m-0"><i class="fas fa-<?= $is_edit ? 'pencil-alt' : 'plus' ?> mr-2"></i>
                    <?= $is_edit ? 'Edit' : 'Tambah' ?> Peran</h1>
                <p class="text-muted small mb-0">
                    <?= $is_edit ? 'Perbarui informasi peran pengguna yang sudah ada.' : 'Definisikan peran baru untuk kategori pengguna sistem.' ?>
                </p>
            </div>
            <a href="<?= BASE_URL ?>peran" class="btn btn-light btn-sm px-3 border" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="alert alert-danger alert-dismissible shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;">
                        <div class="font-weight-bold my-1" style="font-size: 0.9rem;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> <?= $_SESSION['pesan_error'] ?>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 font-weight-bold text-muted small uppercase"><i
                                class="fas fa-edit mr-2 text-primary"></i> INFORMASI PERAN</h6>
                    </div>
                    <form action="<?= $action_url ?>" method="post">
                        <div class="card-body p-4">
                            <input type="hidden" name="id_peran" value="<?= $is_edit ? $peran['id_peran'] : '' ?>">

                            <div class="form-group mb-0">
                                <label class="text-muted small font-weight-bold mb-1">Nama Peran</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"
                                            style="border-radius: 8px 0 0 8px;"><i
                                                class="fas fa-tag text-muted"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="nama_peran" name="nama_peran"
                                        style="border-radius: 0 8px 8px 0;" placeholder="Masukkan Nama Peran"
                                        value="<?= $is_edit ? htmlspecialchars($peran['nama_peran']) : '' ?>" required autofocus>
                                </div>
                                <small class="form-text text-muted mt-2">Contoh: Kepala Sekolah, Bendahara, Pembina
                                    OSIS.</small>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 px-4">
                            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-none"
                                style="border-radius: 8px;">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                            <a href="<?= BASE_URL ?>peran" class="btn btn-link btn-sm text-muted float-right">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
