<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-circle mr-2"></i> Detail Siswa: <?= htmlspecialchars($siswa['nama']) ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="index.php?mod=profil_siswa" class="btn btn-default"><i class="fas fa-arrow-left"></i>
                    Kembali</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible">
                <?= $_SESSION['pesan_sukses'];
                unset($_SESSION['pesan_sukses']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <?= $_SESSION['pesan_error'];
                unset($_SESSION['pesan_error']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <!-- User Card -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                src="../public/assets/img/avatar-student.png" alt="User profile picture">
                        </div>
                        <h3 class="profile-username text-center"><?= htmlspecialchars($siswa['nama']) ?></h3>
                        <p class="text-muted text-center"><?= htmlspecialchars($siswa['nisn']) ?></p>

                        <button
                            onclick="previewPrint('index.php?mod=profil_siswa&act=print&id=<?= $siswa['id_siswa'] ?>')"
                            class="btn btn-primary btn-block"><b><i class="fas fa-print"></i> Cetak Biodata</b></button>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card card-primary card-outline card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?= (!isset($_GET['tab']) || $_GET['tab'] == 'data') ? 'active' : '' ?>"
                                    id="parent-tab" data-toggle="pill" href="#parent" role="tab" aria-controls="parent"
                                    aria-selected="<?= (!isset($_GET['tab']) || $_GET['tab'] == 'data') ? 'true' : 'false' ?>">Data
                                    Orang Tua</a>
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
                            <!-- TAB DATA ORTU -->
                            <div class="tab-pane fade <?= (!isset($_GET['tab']) || $_GET['tab'] == 'data') ? 'show active' : '' ?>"
                                id="parent" role="tabpanel" aria-labelledby="parent-tab">
                                <form action="index.php?mod=profil_siswa&act=save" method="post"
                                    class="form-horizontal">
                                    <input type="hidden" name="id_siswa" value="<?= $siswa['id_siswa'] ?>">
                                    <h5 class="text-primary mb-3">Data Ayah</h5>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Ayah</label>
                                        <div class="col-sm-9"><input type="text" class="form-control" name="nama_ayah"
                                                value="<?= htmlspecialchars($profil['nama_ayah'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Pekerjaan Ayah</label>
                                        <div class="col-sm-9"><input type="text" class="form-control"
                                                name="pekerjaan_ayah"
                                                value="<?= htmlspecialchars($profil['pekerjaan_ayah'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">No. Telepon Ayah</label>
                                        <div class="col-sm-9"><input type="text" class="form-control" name="telp_ayah"
                                                value="<?= htmlspecialchars($profil['telp_ayah'] ?? '') ?>"></div>
                                    </div>

                                    <h5 class="text-primary mb-3 mt-4">Data Ibu</h5>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Ibu</label>
                                        <div class="col-sm-9"><input type="text" class="form-control" name="nama_ibu"
                                                value="<?= htmlspecialchars($profil['nama_ibu'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Pekerjaan Ibu</label>
                                        <div class="col-sm-9"><input type="text" class="form-control"
                                                name="pekerjaan_ibu"
                                                value="<?= htmlspecialchars($profil['pekerjaan_ibu'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">No. Telepon Ibu</label>
                                        <div class="col-sm-9"><input type="text" class="form-control" name="telp_ibu"
                                                value="<?= htmlspecialchars($profil['telp_ibu'] ?? '') ?>"></div>
                                    </div>

                                    <h5 class="text-primary mb-3 mt-4">Data Wali (Opsional)</h5>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Wali</label>
                                        <div class="col-sm-9"><input type="text" class="form-control" name="nama_wali"
                                                value="<?= htmlspecialchars($profil['nama_wali'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Pekerjaan Wali</label>
                                        <div class="col-sm-9"><input type="text" class="form-control"
                                                name="pekerjaan_wali"
                                                value="<?= htmlspecialchars($profil['pekerjaan_wali'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">No. Telp Wali</label>
                                        <div class="col-sm-9"><input type="text" class="form-control" name="telp_wali"
                                                value="<?= htmlspecialchars($profil['telp_wali'] ?? '') ?>"></div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Alamat Wali</label>
                                        <div class="col-sm-9"><textarea class="form-control"
                                                name="alamat_wali"><?= htmlspecialchars($profil['alamat_wali'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success float-right"><i
                                            class="fas fa-save"></i> Simpan Data</button>
                                </form>
                            </div>

                            <!-- TAB BERKAS -->
                            <div class="tab-pane fade <?= (isset($_GET['tab']) && $_GET['tab'] == 'berkas') ? 'show active' : '' ?>"
                                id="berkas" role="tabpanel" aria-labelledby="berkas-tab">
                                <div class="row">
                                    <?php
                                    $files = [
                                        'file_ijazah' => 'Ijazah Terakhir',
                                        'file_kartu_keluarga' => 'Kartu Keluarga',
                                        'file_akte_lahir' => 'Akte Kelahiran',
                                        'file_ktp_ortu' => 'KTP Orang Tua',
                                        'file_kip' => 'Kartu Indonesia Pintar (KIP)'
                                    ];
                                    foreach ($files as $col => $label):
                                        $fVal = $profil[$col] ?? null;
                                        ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card shadow-sm <?= $fVal ? 'border-success' : '' ?>">
                                                <div class="card-body">
                                                    <h6 class="font-weight-bold"><?= $label ?></h6>
                                                    <?php if ($fVal): ?>
                                                        <p class="text-success text-sm"><i class="fas fa-check-circle"></i> File
                                                            Tersedia</p>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-info"
                                                                onclick="previewFile('../public/uploads/siswa/<?= $fVal ?>')">Lihat</button>
                                                            <a href="../public/uploads/siswa/<?= $fVal ?>" download
                                                                class="btn btn-default">Unduh</a>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-muted text-sm"><i class="fas fa-times-circle"></i> Belum
                                                            ada file</p>
                                                    <?php endif; ?>
                                                    <hr>
                                                    <form action="index.php?mod=profil_siswa&act=upload" method="post"
                                                        enctype="multipart/form-data">
                                                        <input type="hidden" name="id_siswa"
                                                            value="<?= $siswa['id_siswa'] ?>">
                                                        <input type="hidden" name="jenis_file" value="<?= $col ?>">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="up_<?= $col ?>"
                                                                name="file_upload" onchange="this.form.submit()" required>
                                                            <label class="custom-file-label"
                                                                for="up_<?= $col ?>">Upload...</label>
                                                        </div>
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

<!-- Modal Preview & Print -->
<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl" style="max-width:90%; height:90%;">
        <div class="modal-content" style="height:100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="siswaModalTitle">Preview</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" style="height:calc(100% - 60px);">
                <iframe src="" id="filePreviewFrame" style="width:100%; height:100%; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
    $(function () { bsCustomFileInput.init(); });
    function previewFile(url) {
        $('#siswaModalTitle').text('Preview Berkas');
        $('#filePreviewFrame').attr('src', url);
        $('#filePreviewModal').modal('show');
    }
    function previewPrint(url) {
        $('#siswaModalTitle').text('Preview Cetak');
        $('#filePreviewFrame').attr('src', url);
        $('#filePreviewModal').modal('show');
    }
</script>