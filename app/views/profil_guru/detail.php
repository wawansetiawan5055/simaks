<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
    .profile-header-bg {
        height: 150px;
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 15px 15px 0 0;
        position: relative;
    }
    .profile-img-container {
        position: relative;
        margin-top: -60px;
        margin-bottom: 15px;
    }
    .profile-user-img-custom {
        width: 120px;
        height: 120px;
        border: 5px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        background: #fff;
        object-fit: cover;
    }
    .card-profile-custom {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .nav-tabs-custom {
        border-bottom: 2px solid #f1f5f9;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }
    .nav-tabs-custom .nav-link.active {
        color: #6366f1;
        background: transparent;
        border-bottom: 3px solid #6366f1;
    }
    .info-box-minimal {
        background: #f8fafc;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        transition: transform 0.2s;
    }
    .info-box-minimal:hover {
        transform: translateY(-3px);
    }
    .info-box-minimal i {
        font-size: 1.5rem;
        margin-bottom: 8px;
        display: block;
        color: #6366f1;
    }
    .info-box-minimal span {
        font-size: 0.75rem;
        color: #94a3b8;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .file-card-custom {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    .file-card-custom:hover {
        transform: translateY(-5px);
    }
    .file-card-success {
        box-shadow: 0 10px 20px rgba(40, 167, 69, 0.15) !important;
        border-left: 4px solid #28a745 !important;
    }
    .file-card-empty {
        box-shadow: 0 10px 20px rgba(100, 116, 139, 0.1) !important;
        border-left: 4px solid #94a3b8 !important;
    }
    .file-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    .file-icon-success {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .file-icon-empty {
        background: rgba(148, 163, 184, 0.1);
        color: #94a3b8;
    }

    /* RESPONSIVE MOBILE VIEW STYLING */
    @media (max-width: 768px) {
        .content-header .profil-header-icon {
            width: 36px !important;
            height: 36px !important;
            font-size: 1.05rem !important;
            border-radius: 8px !important;
            margin-right: 8px !important;
        }
        .content-header h4 {
            font-size: 0.92rem !important;
            line-height: 1.25 !important;
        }
        .profile-header-bg {
            height: 85px !important;
        }
        .profile-img-container {
            margin-top: -42px !important;
            margin-bottom: 8px !important;
        }
        .profile-user-img-custom {
            width: 80px !important;
            height: 80px !important;
            border-width: 3px !important;
        }
        .profile-username {
            font-size: 0.98rem !important;
            margin-bottom: 2px !important;
        }
        .card-profile-custom p.text-muted {
            font-size: 0.74rem !important;
            margin-bottom: 10px !important;
        }
        .info-box-minimal {
            padding: 8px 10px !important;
            margin-bottom: 8px !important;
            border-radius: 8px !important;
        }
        .info-box-minimal i {
            font-size: 1.05rem !important;
            margin-bottom: 3px !important;
        }
        .info-box-minimal span {
            font-size: 0.60rem !important;
            letter-spacing: 0.3px !important;
        }
        .info-box-minimal h6 {
            font-size: 0.76rem !important;
            font-weight: 700 !important;
            margin-bottom: 0 !important;
            line-height: 1.25 !important;
        }
        .btn-print-profile {
            font-size: 0.78rem !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }
        .nav-tabs-custom .nav-link {
            padding: 0.55rem 0.85rem !important;
            font-size: 0.78rem !important;
        }
        .form-group {
            margin-bottom: 0.65rem !important;
        }
        .form-group label.col-form-label {
            font-size: 0.74rem !important;
            font-weight: 600 !important;
            padding-bottom: 2px !important;
            color: #334155 !important;
            line-height: 1.25 !important;
        }
        .form-control {
            font-size: 0.78rem !important;
            height: calc(1.5em + 0.55rem + 2px) !important;
            padding: 0.3rem 0.55rem !important;
            border-radius: 6px !important;
        }
        textarea.form-control {
            height: auto !important;
        }
        .form-group small.text-muted {
            font-size: 0.66rem !important;
        }
        .btn-simpan-profil {
            font-size: 0.78rem !important;
            padding: 6px 14px !important;
            width: 100% !important;
            border-radius: 6px !important;
        }
        .file-card-custom .card-body {
            padding: 0.75rem 0.55rem !important;
        }
        .file-icon-wrapper {
            width: 42px !important;
            height: 42px !important;
            margin-bottom: 6px !important;
        }
        .file-icon-wrapper i {
            font-size: 1.15rem !important;
        }
        .file-card-custom h6 {
            font-size: 0.74rem !important;
            margin-bottom: 2px !important;
        }
        .file-card-custom p.text-xs {
            font-size: 0.64rem !important;
            margin-bottom: 6px !important;
        }
        .custom-file, .custom-file-input, .custom-file-label {
            height: calc(1.5em + 0.45rem + 2px) !important;
            font-size: 0.68rem !important;
        }
        .custom-file-label {
            padding: 0.25rem 0.5rem !important;
            line-height: 1.5 !important;
        }
        .custom-file-label::after {
            padding: 0.25rem 0.5rem !important;
            height: 100% !important;
            line-height: 1.5 !important;
            font-size: 0.68rem !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="mr-3 profil-header-icon" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Profil Saya: <?= htmlspecialchars($guru['nama_guru'] ?? 'Pendidik') ?>
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-indigo font-weight-bold">Profil Guru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="row mb-5">
            <div class="col-md-4">
                <!-- Profile Image & Basic Info -->
                <div class="card card-profile-custom mb-4 mb-md-0">
                    <div class="profile-header-bg"></div>
                    <div class="card-body box-profile text-center">
                        <div class="profile-img-container">
                            <?php 
                            $profilePhoto = get_user_photo($guru['id_pengguna'] ?? null, $guru['nama'] ?? '');
                            ?>
                            <img class="profile-user-img-custom img-circle"
                                src="<?= $profilePhoto ?>" alt="User profile picture"
                                onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/img/avatar-default.svg'">
                        </div>
                        <h3 class="profile-username font-weight-bold"><?= htmlspecialchars($guru['nama']) ?></h3>
                        <p class="text-muted mb-4"><?= htmlspecialchars($jabatan_text) ?></p>

                        <div class="row text-left px-2">
                            <div class="col-6">
                                <div class="info-box-minimal">
                                    <i class="fas fa-id-card"></i>
                                    <span>NUPTK</span>
                                    <h6><?= htmlspecialchars(!empty($guru['nuptk']) ? $guru['nuptk'] : '-') ?></h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box-minimal">
                                    <i class="fas fa-briefcase"></i>
                                    <span>Status</span>
                                    <h6><?= htmlspecialchars(!empty($guru['status_kepegawaian']) ? $guru['status_kepegawaian'] : '-') ?></h6>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-box-minimal">
                                    <i class="fas fa-book"></i>
                                    <span>Mapel Diampu</span>
                                    <h6><?= htmlspecialchars($mapel_text) ?></h6>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-box-minimal">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Alamat</span>
                                    <h6><?= htmlspecialchars(!empty($profil['alamat_lengkap']) ? $profil['alamat_lengkap'] : 'Belum dilengkapi') ?></h6>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <button onclick="previewPrint('<?= BASE_URL ?>profil_guru/print?id=<?= $guru['id_guru'] ?>')"
                            class="btn btn-primary btn-block shadow-sm btn-print-profile">
                            <i class="fas fa-print mr-2"></i> Cetak Profil / CV
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4 mb-md-0">
                <div class="card card-profile-custom card-tabs h-100">
                    <div class="card-header p-0 pt-1 nav-tabs-custom">
                        <ul class="nav nav-tabs border-0" id="custom-tabs-three-tab" role="tablist">
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
                                <form class="form-horizontal" method="post" action="<?= BASE_URL ?>profil_guru/save">
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
                                        <label class="col-sm-3 col-form-label">Sertifikasi</label>
                                        <div class="col-sm-4">
                                            <select class="form-control" name="sertifikasi" id="sertifikasi">
                                                <option value="Belum Tersertifikasi" <?= ($profil['sertifikasi'] ?? '') == 'Belum Tersertifikasi' ? 'selected' : '' ?>>Belum Tersertifikasi</option>
                                                <option value="Tersertifikasi" <?= ($profil['sertifikasi'] ?? '') == 'Tersertifikasi' ? 'selected' : '' ?>>Tersertifikasi</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-5">
                                            <select class="form-control" name="mapel_sertifikasi" id="mapel_sertifikasi">
                                                <option value="">- Pilih Mapel Sertifikasi -</option>
                                                <?php foreach ($all_mapel as $mapel_item): ?>
                                                    <option value="<?= htmlspecialchars($mapel_item) ?>" <?= ($profil['mapel_sertifikasi'] ?? '') == $mapel_item ? 'selected' : '' ?>><?= htmlspecialchars($mapel_item) ?></option>
                                                <?php endforeach; ?>
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
                                        <div class="offset-sm-3 col-sm-9 text-right">
                                            <button type="submit" class="btn btn-success btn-simpan-profil"><i class="fas fa-save mr-1"></i>
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
                                        'file_ijazah_s1' => ['label' => 'Ijazah Terakhir', 'icon' => 'fas fa-graduation-cap'],
                                        'file_serdik' => ['label' => 'Sertifikat Pendidik', 'icon' => 'fas fa-certificate'],
                                        'file_kk' => ['label' => 'Kartu Keluarga', 'icon' => 'fas fa-users'],
                                        'file_ktp' => ['label' => 'KTP Elektronik', 'icon' => 'fas fa-id-card'],
                                        'file_akte' => ['label' => 'Akte Kelahiran', 'icon' => 'fas fa-file-invoice'],
                                        'file_npwp' => ['label' => 'NPWP', 'icon' => 'fas fa-wallet']
                                    ];

                                    foreach ($files as $col => $info):
                                        $label = $info['label'];
                                        $icon = $info['icon'];
                                        $existingFile = $profil[$col] ?? null;
                                        $cardClass = $existingFile ? 'file-card-success' : 'file-card-empty';
                                        $iconClass = $existingFile ? 'file-icon-success' : 'file-icon-empty';
                                        ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100 file-card-custom <?= $cardClass ?>">
                                                <div style="height: 6px; width: 100%; background: <?= $existingFile ? '#28a745' : '#cbd5e1' ?>;"></div>
                                                <div class="card-body text-center p-4">
                                                    <div class="file-icon-wrapper <?= $iconClass ?>">
                                                        <i class="<?= $icon ?> fa-2x"></i>
                                                    </div>
                                                    <h6 class="font-weight-bold mb-1"><?= $label ?></h6>
                                                    
                                                    <?php if ($existingFile): ?>
                                                        <p class="text-xs text-success mb-3"><i class="fas fa-check-circle mr-1"></i> Berkas Terverifikasi</p>
                                                        <div class="btn-group btn-group-sm w-100 mb-3">
                                                            <button type="button" class="btn btn-light border"
                                                                onclick="previewFile('<?= $col ?>', '<?= BASE_URL ?>uploads/guru/<?= $existingFile ?>')">
                                                                <i class="fas fa-eye text-info"></i>
                                                            </button>
                                                            <a href="<?= BASE_URL ?>uploads/guru/<?= $existingFile ?>" download
                                                                class="btn btn-light border">
                                                                <i class="fas fa-download text-success"></i>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <p class="text-xs text-muted mb-3">Belum ada berkas</p>
                                                        <div style="height: 31px;" class="mb-3"></div>
                                                    <?php endif; ?>

                                                    <form action="<?= BASE_URL ?>profil_guru/upload" method="post"
                                                        enctype="multipart/form-data">
                                                        <input type="hidden" name="id_guru" value="<?= $guru['id_guru'] ?>">
                                                        <input type="hidden" name="jenis_file" value="<?= $col ?>">
                                                        <div class="custom-file custom-file-sm">
                                                            <input type="file" class="custom-file-input"
                                                                name="file_upload" id="file_<?= $col ?>"
                                                                onchange="confirmUpload(this, '<?= $label ?>')" required>
                                                            <label class="custom-file-label text-xs text-left"
                                                                for="file_<?= $col ?>" data-existing="<?= $existingFile ? 'true' : 'false' ?>">
                                                                <?= $existingFile ? 'Ganti File' : 'Pilih File' ?>
                                                            </label>
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

    function confirmUpload(inputElement, labelName) {
        if (inputElement.files && inputElement.files[0]) {
            let file = inputElement.files[0];
            Swal.fire({
                title: 'Upload ' + labelName + '?',
                text: "Anda akan mengunggah file: " + file.name,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fas fa-upload mr-1"></i> Ya, Upload!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan notifikasi loading (popup)
                    Swal.fire({
                        title: 'Mengunggah Berkas...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    inputElement.form.submit();
                } else {
                    // Reset input jika dibatalkan
                    inputElement.value = '';
                    const label = inputElement.nextElementSibling;
                    const existingFile = label.getAttribute('data-existing');
                    label.innerHTML = existingFile === 'true' ? 'Ganti File' : 'Pilih File';
                }
            });
        }
    }
</script>