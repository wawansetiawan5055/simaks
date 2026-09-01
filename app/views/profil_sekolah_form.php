<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Pengaturan Profil Sekolah
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Profil Sekolah</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <!-- Alerts handled by toast -->
        <div class="card card-primary">
            <form action="<?= BASE_URL ?>profil_sekolah/save" method="POST" enctype="multipart/form-data">
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