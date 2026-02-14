<?php include __DIR__ . '/partials/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <h1><i class="fas <?= $user ? 'fa-user-edit' : 'fa-user-plus' ?> mr-2"></i>
            <?= $user ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">

            <form action="index.php?mod=manajemen_pengguna&act=save" method="POST">
                <?php if ($user): ?>
                    <input type="hidden" name="id_pengguna" value="<?= $user['id_pengguna'] ?>">
                <?php endif; ?>

                <div class="card-body pt-0">
                    <div class="alert alert-info py-2 px-3 border-0 small mb-4"
                        style="border-radius: 10px; background-color: #f0f9ff; color: #0369a1;">
                        <i class="fas fa-info-circle mr-1"></i> Hubungkan pengguna dengan data Master Guru atau Siswa
                        untuk sinkronisasi nama otomatis.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">Tipe Pengguna /
                                    Relasi</label>
                                <select name="id_guru" id="id_guru_select" class="form-control"
                                    style="border-radius: 8px;">
                                    <option value="">-- Hubungkan dengan Guru --</option>
                                    <?php foreach ($available_guru as $guru): ?>
                                        <option value="<?= $guru['id_guru'] ?>"
                                            data-nama="<?= strtolower(str_replace(' ', '', $guru['nama'])) ?>"
                                            <?= ($linked_guru_id ?? '') == $guru['id_guru'] ? 'selected' : '' ?>>
                                            <?= $guru['nama'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted text-uppercase">&nbsp;</label>
                                <select name="id_siswa" id="id_siswa_select" class="form-control"
                                    style="border-radius: 8px;">
                                    <option value="">-- Hubungkan dengan Siswa --</option>
                                    <?php foreach ($available_siswa as $siswa): ?>
                                        <option value="<?= $siswa['id_siswa'] ?>"
                                            data-nama="<?= strtolower(str_replace(' ', '', $siswa['nama'])) ?>"
                                            <?= ($linked_siswa_id ?? '') == $siswa['id_siswa'] ? 'selected' : '' ?>>
                                            <?= $siswa['nama'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"
                                    style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i
                                        class="fas fa-at text-muted"></i></span>
                            </div>
                            <input type="text" name="username" id="username_input" class="form-control border-left-0"
                                value="<?= $user['username'] ?? '' ?>" required placeholder="Contoh: roni.paslah"
                                style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"
                                    style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i
                                        class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control border-left-0" <?= !$user ? 'required' : '' ?>
                                placeholder="<?= $user ? 'Isi hanya jika ingin ganti' : 'Minimal 6 karakter' ?>"
                                style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control" value="<?= $user['email'] ?? '' ?>"
                            placeholder="pembelajar@sekolah.sch.id" style="border-radius: 8px;">
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted text-uppercase d-block mb-3">Peran
                            (Roles)</label>
                        <div class="row bg-light p-3 mx-0 rounded" style="border: 1px dashed #cbd5e1;">
                            <?php foreach ($all_roles as $role): ?>
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="roles[]"
                                            value="<?= $role['id_peran'] ?>" id="role_<?= $role['id_peran'] ?>"
                                            <?= in_array($role['id_peran'], $user_roles) ? 'checked' : '' ?>>
                                        <label class="custom-control-label small font-weight-bold"
                                            for="role_<?= $role['id_peran'] ?>" style="cursor: pointer;">
                                            <i
                                                class="fas fa-user-shield text-<?= $role['nama_peran'] == 'Admin' ? 'danger' : 'secondary' ?> mr-1"></i>
                                            <?= $role['nama_peran'] ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 py-4 text-center">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold mr-2"
                        style="border-radius: 10px;">
                        <i class="fas fa-save mr-2"></i> Simpan Pengguna
                    </button>
                    <a href="index.php?mod=manajemen_pengguna" class="btn btn-light px-4 font-weight-bold"
                        style="border-radius: 10px; color: #64748b;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const guruSelect = document.getElementById('id_guru_select');
        const siswaSelect = document.getElementById('id_siswa_select');
        const usernameInput = document.getElementById('username_input');

        function updateUsername(selectedOption) {
            const nama = selectedOption ? selectedOption.getAttribute('data-nama') : '';
            if (nama && !usernameInput.value) { // Hanya isi otomatis jika username masih kosong
                usernameInput.value = nama;
            }
        }

        guruSelect.addEventListener('change', function () {
            if (this.value) {
                siswaSelect.value = '';
                updateUsername(this.options[this.selectedIndex]);
            }
        });

        siswaSelect.addEventListener('change', function () {
            if (this.value) {
                guruSelect.value = '';
                updateUsername(this.options[this.selectedIndex]);
            }
        });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>