<?php include __DIR__ . '/partials/header.php'; ?>

<style>
    .profil-user-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .profil-user-icon-box {
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
        .profile-user-img {
            width: 80px !important;
            height: 80px !important;
        }
        .profile-username {
            font-size: 0.98rem !important;
        }
        .card-body.box-profile p.text-muted,
        .card-body.box-profile small {
            font-size: 0.74rem !important;
        }
        .card-title {
            font-size: 0.88rem !important;
        }
        .form-group label {
            font-size: 0.76rem !important;
            margin-bottom: 2px !important;
        }
        .form-control {
            font-size: 0.78rem !important;
            height: calc(1.5em + 0.55rem + 2px) !important;
            padding: 0.3rem 0.55rem !important;
        }
        .btn-update-profil {
            font-size: 0.80rem !important;
            padding: 6px 14px !important;
            width: 100% !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-7 col-12 d-flex align-items-center">
                <div class="profil-user-icon-box mr-3">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Profil Akun Saya
                    </h4>
                </div>
            </div>
            <div class="col-sm-5 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Profil Akun</li>
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

                        <h3 class="profile-username text-center"><?= htmlspecialchars($user['nama_pengguna'] ?? '') ?>
                        </h3>
                        <p class="text-muted text-center"><?= htmlspecialchars($user['email'] ?? '') ?></p>

                        <div class="text-center text-muted mb-3">
                            <small>Username: <?= htmlspecialchars($user['username'] ?? '') ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Profil</h3>
                    </div>
                    <form action="<?= BASE_URL ?>profil/save" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_pengguna"
                                    value="<?= htmlspecialchars($user['nama_pengguna'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email"
                                    value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Username (Read Only)</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($user['username'] ?? '') ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Password Baru <small class="text-danger">(Kosongkan jika tidak ingin
                                        mengubah)</small></label>
                                <input type="password" class="form-control" name="password"
                                    placeholder="Masukkan password baru...">
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Foto Profil <small class="text-muted">(Format: JPG, PNG, WEBP. Maks. 5MB)</small></label>
                                
                                <!-- Tab Switcher: Unggah File & Kamera -->
                                <ul class="nav nav-pills camera-tab-nav mb-2" role="tablist" style="display: flex; gap: 6px;">
                                    <li class="nav-item" style="flex: 1;">
                                        <a class="nav-link active" id="tab-upload-mode-tab" data-toggle="pill" href="#tab-upload-mode" role="tab" onclick="stopCameraStream()" style="width: 100%; text-align: center; font-size: 0.82rem; font-weight: 700; border-radius: 10px; border: 1.5px solid #e2e8f0;">
                                            <i class="fas fa-folder-open mr-1"></i> Unggah
                                        </a>
                                    </li>
                                    <li class="nav-item" style="flex: 1;">
                                        <a class="nav-link" id="tab-camera-mode-tab" data-toggle="pill" href="#tab-camera-mode" role="tab" onclick="startCameraStream()" style="width: 100%; text-align: center; font-size: 0.82rem; font-weight: 700; border-radius: 10px; border: 1.5px solid #e2e8f0;">
                                            <i class="fas fa-camera mr-1"></i> Kamera
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content border rounded-lg p-2.5 bg-light" style="border-radius: 12px;">
                                    <!-- 1. Mode Unggah File -->
                                    <div class="tab-pane fade show active" id="tab-upload-mode" role="tabpanel">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="foto" name="foto"
                                                accept="image/*" onchange="previewImage(this)">
                                            <label class="custom-file-label" for="foto">Pilih file foto...</label>
                                        </div>
                                        <div class="mt-2 text-center">
                                            <img id="preview" src="#" alt="Preview Foto" class="img-thumbnail rounded-circle shadow-sm"
                                                style="display: none; width: 120px; height: 120px; object-fit: cover;">
                                        </div>
                                    </div>

                                    <!-- 2. Mode Live Camera Capture -->
                                    <div class="tab-pane fade" id="tab-camera-mode" role="tabpanel">
                                        <div class="camera-box-wrapper" style="min-height: 180px; background: #0f172a; border-radius: 12px; overflow: hidden; position: relative; text-align: center;">
                                            <video id="cameraVideo" autoplay playsinline muted style="width: 100%; max-height: 250px; object-fit: cover; display: none; background: #000;"></video>
                                            <canvas id="cameraCanvas" style="display:none;"></canvas>
                                            
                                            <!-- Error Box (Muncul hanya jika kamera gagal/diblokir) -->
                                            <div id="cameraErrorBox" class="p-4 text-center text-white" style="display:none; min-height: 180px;">
                                                <i class="fas fa-video-slash text-warning fa-2x mb-2"></i>
                                                <p class="small text-light mb-2" id="cameraStatusText">Kamera tidak dapat diakses.</p>
                                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 font-weight-bold" onclick="document.getElementById('nativeCameraInput').click()">
                                                    <i class="fas fa-camera-retro mr-1"></i> Buka Kamera HP
                                                </button>
                                            </div>

                                            <!-- Controls Bar -->
                                            <div id="cameraControlsBar" class="p-2 d-flex justify-content-center align-items-center flex-wrap" style="gap: 8px; background: rgba(15, 23, 42, 0.9); display: none !important;">
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 font-weight-bold shadow-sm" id="btnSnapPhoto" onclick="takeSnapshot()">
                                                    <i class="fas fa-camera mr-1"></i> Jepret Foto
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 font-weight-bold" id="btnSwitchCamera" onclick="switchCameraFacing()">
                                                    <i class="fas fa-sync-alt mr-1"></i> Balik Kamera
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 font-weight-bold" id="btnRetakePhoto" onclick="retakeSnapshot()" style="display:none;">
                                                    <i class="fas fa-redo mr-1"></i> Ulangi
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Native Mobile Camera Fallback Trigger -->
                                        <div class="mt-2 text-center">
                                            <input type="file" id="nativeCameraInput" accept="image/*" capture="environment" style="display:none;" onchange="previewNativeCamera(this)">
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 font-weight-bold btn-block" onclick="document.getElementById('nativeCameraInput').click()">
                                                <i class="fas fa-camera-retro mr-1"></i> Buka Aplikasi Kamera HP
                                            </button>
                                        </div>

                                        <div id="cameraCapturedBox" class="mt-2 text-center" style="display:none;">
                                            <small class="text-success font-weight-bold d-block mb-1"><i class="fas fa-check-circle mr-1"></i> Foto Berhasil Diambil:</small>
                                            <img id="cameraCapturedPreview" class="img-thumbnail rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;" src="">
                                        </div>
                                        
                                        <!-- Hidden Input to store Base64 Live Camera photo -->
                                        <input type="hidden" name="foto_cam_data" id="fotoCamData" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4 rounded-pill shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
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

    // ============================================================
    // 📸 LIVE CAMERA HANDLER
    // ============================================================
    let stream = null;
    let currentFacingMode = "user";

    async function startCameraStream() {
        stopCameraStream();
        const video = document.getElementById('cameraVideo');
        const errorBox = document.getElementById('cameraErrorBox');
        const statusText = document.getElementById('cameraStatusText');
        const controlsBar = document.getElementById('cameraControlsBar');

        if (errorBox) errorBox.style.display = 'none';
        if (video) video.style.display = 'none';
        if (controlsBar) controlsBar.style.setProperty('display', 'none', 'important');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (statusText) statusText.innerText = 'Browser tidak mendukung WebRTC kamera langsung.';
            if (errorBox) errorBox.style.display = 'block';
            return;
        }

        video.muted = true;
        video.setAttribute('playsinline', '');
        video.setAttribute('autoplay', '');
        video.setAttribute('muted', '');

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });
        } catch (err) {
            console.warn("Primary camera constraint failed, trying generic video...", err);
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            } catch (err2) {
                console.error("Camera access error: ", err2);
                if (statusText) statusText.innerText = 'Kamera tidak dapat diakses atau izin ditolak.';
                if (errorBox) errorBox.style.display = 'block';
                return;
            }
        }

        if (stream) {
            video.srcObject = stream;
            video.onloadedmetadata = function() {
                video.play().then(() => {
                    video.style.display = 'block';
                    if (controlsBar) controlsBar.style.setProperty('display', 'flex', 'important');
                }).catch(e => {
                    console.error("Video play error: ", e);
                    video.style.display = 'block';
                    if (controlsBar) controlsBar.style.setProperty('display', 'flex', 'important');
                });
            };
        }
    }

    function stopCameraStream() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        const video = document.getElementById('cameraVideo');
        if (video) {
            video.srcObject = null;
            video.style.display = 'none';
        }
        const controlsBar = document.getElementById('cameraControlsBar');
        if (controlsBar) {
            controlsBar.style.setProperty('display', 'none', 'important');
        }
    }

    function switchCameraFacing() {
        currentFacingMode = (currentFacingMode === "user") ? "environment" : "user";
        startCameraStream();
    }

    function takeSnapshot() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        const preview = document.getElementById('cameraCapturedPreview');
        const previewBox = document.getElementById('cameraCapturedBox');
        const hiddenInput = document.getElementById('fotoCamData');
        const btnSnap = document.getElementById('btnSnapPhoto');
        const btnRetake = document.getElementById('btnRetakePhoto');

        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;

        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, width, height);

        const base64Image = canvas.toDataURL('image/jpeg', 0.88);
        hiddenInput.value = base64Image;
        preview.src = base64Image;
        previewBox.style.display = 'block';

        video.pause();
        btnSnap.style.display = 'none';
        btnRetake.style.display = 'inline-block';
    }

    function retakeSnapshot() {
        const video = document.getElementById('cameraVideo');
        const previewBox = document.getElementById('cameraCapturedBox');
        const hiddenInput = document.getElementById('fotoCamData');
        const btnSnap = document.getElementById('btnSnapPhoto');
        const btnRetake = document.getElementById('btnRetakePhoto');

        hiddenInput.value = '';
        previewBox.style.display = 'none';
        btnSnap.style.display = 'inline-block';
        btnRetake.style.display = 'none';
        video.play();
    }

    function previewNativeCamera(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64Data = e.target.result;
                document.getElementById('fotoCamData').value = base64Data;
                document.getElementById('cameraCapturedPreview').src = base64Data;
                document.getElementById('cameraCapturedBox').style.display = 'block';
                stopCameraStream();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>