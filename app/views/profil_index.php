<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-circle mr-2"></i> Profil Saya</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Profil Saya</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                src="<?= get_user_photo($user['id_pengguna']) ?>" alt="User profile picture"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        </div>

                        <h3 class="profile-username text-center"><?= htmlspecialchars($user['nama_pengguna']) ?></h3>
                        <p class="text-muted text-center"><?= htmlspecialchars($user['email']) ?></p>

                        <div class="text-center text-muted mb-3">
                            <small>Username: <?= htmlspecialchars($user['username']) ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Profil</h3>
                    </div>
                    <form action="index.php?mod=profil&act=save" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_pengguna"
                                    value="<?= htmlspecialchars($user['nama_pengguna']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email"
                                    value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Username (Read Only)</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($user['username']) ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Password Baru <small class="text-danger">(Kosongkan jika tidak ingin
                                        mengubah)</small></label>
                                <input type="password" class="form-control" name="password"
                                    placeholder="Masukkan password baru...">
                            </div>

                            <div class="form-group">
                                <label for="foto">Foto Profil <small class="text-muted">(Format: JPG, PNG. Max
                                        2MB)</small></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="foto" name="foto"
                                            accept="image/*" onchange="previewImage(this)">
                                        <label class="custom-file-label" for="foto">Pilih file</label>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <img id="preview" src="#" alt="Preview Foto" class="img-thumbnail"
                                        style="display: none; max-width: 150px; max-height: 150px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
    // Custom File Input Label Fix
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>