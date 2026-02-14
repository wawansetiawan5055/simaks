<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-user-tie mr-2"></i> Detail Profil Guru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?mod=profil_guru">Profil Guru</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($guru['nama']) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['pesan_sukses'];
                unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['pesan_error'];
                unset($_SESSION['pesan_error']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image & Basic Info -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="../public/assets/img/avatar.png"
                                alt="User profile picture">
                        </div>
                        <h3 class="profile-username text-center"><?= htmlspecialchars($guru['nama']) ?></h3>
                        <p class="text-muted text-center"><?= htmlspecialchars($guru['status_kepegawaian'] ?? 'Guru') ?>
                        </p>

                        <button onclick="previewPrint('index.php?mod=profil_guru&act=print&id=<?= $guru['id_guru'] ?>')"
                            class="btn btn-primary btn-block"><b><i class="fas fa-print"></i> Cetak Profil /
                                CV</b></button>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card card-primary card-outline card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?= (!isset($_GET['tab']) || $_GET['tab'] == 'data') ? 'active' : '' ?>"
                                    id="data-pribadi-tab" data-toggle="pill" href="#data_pribadi" role="tab"
                                    aria-controls="data_pribadi"
                                    aria-selected="<?= (!isset($_GET['tab']) || $_GET['tab'] == 'data') ? 'true' : 'false' ?>">Data
                                    Pribadi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= (isset($_GET['tab']) && $_GET['tab'] == 'berkas') ? 'active' : '' ?>"
                                    id="berkas-tab" data-toggle="pill" href="#berkas" role="tab" aria-controls="berkas"
                                    aria-selected="<?= (isset($_GET['tab']) && $_GET['tab'] == 'berkas') ? 'true' : 'false' ?>">Berkas
                                    Pendukung</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- TAB DATA PRIBADI -->
                            <div class="tab-pane fade <?= (!isset($_GET['tab']) || $_GET['tab'] == 'data') ? 'show active' : '' ?>"
                                id="data_pribadi" role="tabpanel" aria-labelledby="data-pribadi-tab">
                                <form class="form-horizontal" method="post" action="index.php?mod=profil_guru&act=save">
                                    <input type="hidden" name="id_guru" value="<?= $guru['id_guru'] ?>">

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Lengkap (Sesuai Dapodik)</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control"
                                                value="<?= htmlspecialchars($guru['nama']) ?>" disabled>
                                            <small class="text-muted">Diambil dari Data Master Guru</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Gelar Depan</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="gelar_depan"
                                                value="<?= htmlspecialchars($profil['gelar_depan'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Gelar Belakang</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="gelar_belakang"
                                                value="<?= htmlspecialchars($profil['gelar_belakang'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Pendidikan Terakhir</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="pendidikan_terakhir">
                                                <option value="">- Pilih -</option>
                                                <?php $jenjang = ['SMA/Sederajat', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
                                                foreach ($jenjang as $j) {
                                                    $sel = ($profil['pendidikan_terakhir'] ?? '') == $j ? 'selected' : '';
                                                    echo "<option value='$j' $sel>$j</option>";
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Alamat Lengkap</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="alamat_lengkap"
                                                rows="3"><?= htmlspecialchars($profil['alamat_lengkap'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">No. Handphone (WA)</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="no_hp"
                                                value="<?= htmlspecialchars($profil['no_hp'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email Pribadi</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" name="email_pribadi"
                                                value="<?= htmlspecialchars($profil['email_pribadi'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Ibu Kandung</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="nama_ibu_kandung"
                                                value="<?= htmlspecialchars($profil['nama_ibu_kandung'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                                Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- TAB BERKAS PENDUKUNG -->
                            <div class="tab-pane fade <?= (isset($_GET['tab']) && $_GET['tab'] == 'berkas') ? 'show active' : '' ?>"
                                id="berkas" role="tabpanel" aria-labelledby="berkas-tab">
                                <div class="row">
                                    <?php
                                    $files = [
                                        'file_ijazah_s1' => 'Ijazah S1 / Terakhir',
                                        'file_serdik' => 'Sertifikat Pendidik (Serdik)',
                                        'file_kk' => 'Kartu Keluarga (KK)',
                                        'file_ktp' => 'KTP',
                                        'file_akte' => 'Akte Kelahiran',
                                        'file_npwp' => 'NPWP'
                                    ];

                                    foreach ($files as $col => $label):
                                        $existingFile = $profil[$col] ?? null;
                                        ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div
                                                class="card h-100 <?= $existingFile ? 'card-success card-outline' : 'card-secondary card-outline' ?>">
                                                <div class="card-header">
                                                    <h5 class="card-title text-sm font-weight-bold"><?= $label ?></h5>
                                                </div>
                                                <div class="card-body text-center">
                                                    <?php if ($existingFile): ?>
                                                        <div class="mb-2">
                                                            <i class="fas fa-file-check fa-3x text-success"></i>
                                                            <br><small class="text-success font-weight-bold">Sudah
                                                                Diupload</small>
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-info"
                                                                onclick="previewFile('<?= $col ?>', '../public/uploads/guru/<?= $existingFile ?>')">
                                                                <i class="fas fa-eye"></i> Lihat
                                                            </button>
                                                            <a href="../public/uploads/guru/<?= $existingFile ?>" download
                                                                class="btn btn-default">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="mb-2">
                                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                                            <br><small class="text-muted">Belum ada file</small>
                                                        </div>
                                                    <?php endif; ?>

                                                    <hr>
                                                    <form action="index.php?mod=profil_guru&act=upload" method="post"
                                                        enctype="multipart/form-data">
                                                        <input type="hidden" name="id_guru" value="<?= $guru['id_guru'] ?>">
                                                        <input type="hidden" name="jenis_file" value="<?= $col ?>">
                                                        <div class="input-group input-group-sm">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    name="file_upload" id="file_<?= $col ?>"
                                                                    onchange="this.form.submit()" required>
                                                                <label class="custom-file-label text-left"
                                                                    for="file_<?= $col ?>"><?= $existingFile ? 'Ganti File' : 'Pilih File' ?></label>
                                                            </div>
                                                        </div>
                                                        <small class="text-xs text-muted">Max 5MB. PDF/JPG/PNG</small>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
    // Custom File Input Label
    $(function () {
        bsCustomFileInput.init();
    });

    function previewFile(type, url) {
        // Use Global Modal
        showGlobalPreview(url, 'iframe', 'Pratinjau Berkas (' + type + ')');
    }

    function previewPrint(url) {
        // Use Global Modal
        showGlobalPreview(url, 'iframe', 'Pratinjau Cetak');
    }
</script>