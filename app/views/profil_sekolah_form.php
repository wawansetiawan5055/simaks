<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-school mr-2"></i> Pengaturan Profil Sekolah</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <!-- Alerts handled by toast -->
        <div class="card card-primary">
            <form action="index.php?mod=profil_sekolah&act=save" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label>Nama Sekolah</label><input type="text" name="nama_sekolah"
                                    class="form-control" value="<?= $profil['nama_sekolah'] ?>"></div>
                            <div class="form-group"><label>NPSN</label><input type="text" name="npsn"
                                    class="form-control" value="<?= $profil['npsn'] ?>"></div>
                            <div class="form-group">
                                <label>Bentuk Pendidikan</label>
                                <select name="bentuk_pendidikan" class="form-control">
                                    <option value="SD/MI" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SD/MI' ? 'selected' : '' ?>>SD/MI</option>
                                    <option value="SMP/MTs" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SMP/MTs' ? 'selected' : '' ?>>SMP/MTs</option>
                                    <option value="SMA/MA" <?= ($profil['bentuk_pendidikan'] ?? '') == 'SMA/MA' ? 'selected' : '' ?>>SMA/MA</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kurikulum</label>
                                <select name="kurikulum" class="form-control">
                                    <option value="Kurikulum Merdeka" <?= ($profil['kurikulum'] ?? '') == 'Kurikulum Merdeka' ? 'selected' : '' ?>>Kurikulum Merdeka</option>
                                    <option value="Kurikulum 2013" <?= ($profil['kurikulum'] ?? '') == 'Kurikulum 2013' ? 'selected' : '' ?>>Kurikulum 2013</option>
                                </select>
                            </div>
                            <div class="form-group"><label>Nama Kepala Sekolah</label><input type="text"
                                    name="nama_kepala_sekolah" class="form-control"
                                    value="<?= $profil['nama_kepala_sekolah'] ?>"></div>
                            <div class="form-group"><label>Alamat</label><textarea name="alamat"
                                    class="form-control"><?= $profil['alamat'] ?></textarea></div>
                            <div class="form-group"><label>Titik Koordinat</label><input type="text" name="koordinat"
                                    class="form-control" value="<?= $profil['koordinat'] ?>"></div>
                            <div class="form-group"><label>Moto Sekolah</label><input type="text" name="moto"
                                    class="form-control" value="<?= $profil['moto'] ?>"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Nomor Telepon</label><input type="text" name="telepon"
                                    class="form-control" value="<?= $profil['telepon'] ?>"></div>
                            <div class="form-group"><label>Email</label><input type="email" name="email"
                                    class="form-control" value="<?= $profil['email'] ?>"></div>
                            <div class="form-group"><label>Website</label><input type="text" name="website"
                                    class="form-control" value="<?= $profil['website'] ?>"></div>
                            <div class="form-group"><label>Status Sekolah</label><select name="status_sekolah"
                                    class="form-control">
                                    <option value="Swasta" <?= $profil['status_sekolah'] == 'Swasta' ? 'selected' : '' ?>>
                                        Swasta</option>
                                    <option value="Negeri" <?= $profil['status_sekolah'] == 'Negeri' ? 'selected' : '' ?>>
                                        Negeri</option>
                                </select></div>
                            <div class="form-group"><label>Dinas atau Yayasan</label><input type="text"
                                    name="nama_yayasan" class="form-control" value="<?= $profil['nama_yayasan'] ?>">
                            </div>
                            <div class="form-group"><label>SK Izin Operasional</label><input type="text"
                                    name="sk_izin_operasional" class="form-control"
                                    value="<?= $profil['sk_izin_operasional'] ?>"></div>
                            <div class="form-group"><label>SK Akreditasi</label><input type="text" name="sk_akreditasi"
                                    class="form-control" value="<?= $profil['sk_akreditasi'] ?>"></div>
                            <div class="form-group"><label>Logo Sekolah</label><input type="file" name="logo_sekolah"
                                    class="form-control"><?php if ($profil['logo']): ?><img
                                        src="assets/img/<?= $profil['logo'] ?>" alt="Logo" class="mt-2"
                                        style="max-height: 50px;"><?php endif; ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Profil</button></div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>