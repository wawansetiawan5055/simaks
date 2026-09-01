<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-book-reader"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Jurnal Pembelajaran KBM Guru
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Jurnal KBM</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Alerts handled by toast -->

        <div id="notif_absensi_container" style="display: none;"></div>

        <div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden; border-top: 3px solid #3b82f6;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center mr-3 bg-light rounded" style="width:38px; height:38px; flex-shrink:0; border: 1px solid #e2e8f0;">
                        <i class="fas fa-edit text-dark" style="font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 1rem;">Input Jurnal Harian</h6>
                        <small class="text-muted">Lengkapi semua kolom dengan benar</small>
                    </div>
                </div>
            </div>
            <form action="<?= BASE_URL ?>jurnal_kbm/save" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-right pr-md-4">

                            <!-- [REVISI 1] Menambahkan logika 'selected' pada dropdown kelas -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark"><i class="fas fa-chalkboard mr-1"></i> Kelas</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"
                                            style="border-radius: 8px 0 0 8px;"><i
                                                class="fas fa-chalkboard text-muted"></i></span>
                                    </div>
                                    <select name="id_kelas" id="id_kelas" class="form-control border-left-0"
                                        style="border-radius: 0 8px 8px 0;" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($kelas_diajar as $kelas): ?>
                                            <?php $selected = ($kelas['id_kelas'] == $id_kelas_prefill) ? 'selected' : ''; ?>
                                            <option value="<?= $kelas['id_kelas'] ?>" <?= $selected ?>>
                                                <?= $kelas['nama_kelas'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- [REVISI 2] Mengisi value input tanggal dari $tanggal_prefill -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark"><i class="fas fa-calendar-day mr-1"></i> Tanggal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"
                                            style="border-radius: 8px 0 0 8px;"><i
                                                class="fas fa-calendar-day text-muted"></i></span>
                                    </div>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control border-left-0"
                                        style="border-radius: 0 8px 8px 0;"
                                        value="<?= htmlspecialchars($tanggal_prefill) ?>" required>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark"><i class="fas fa-clock mr-1"></i> Jam Mengajar
                                    <span class="badge badge-light border ml-2">Pilih satu atau lebih</span></label>
                                <div id="jam_mengajar_container" class="p-3 rounded border bg-light"
                                    style="min-height: 100px;">
                                    <p class="text-muted text-center small my-4"><i class="fas fa-info-circle mr-1"></i>
                                        Pilih Kelas dan Tanggal Dahulu</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 pl-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark"><i class="fas fa-bullseye mr-1"></i> Capaian & Tujuan
                                    Pembelajaran</label>

                                <!-- [NEW] Dropdown Pilih CP -->
                                <div class="input-group mb-2" id="container_pilih_cp" style="display:none;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i
                                                class="fas fa-bullseye text-primary"></i></span>
                                    </div>
                                    <select class="form-control border-left-0" id="pilih_cp">
                                        <option value="">-- Pilih Capaian Pembelajaran (CP) --</option>
                                    </select>
                                </div>

                                <small id="info_pilih_jam" class="form-text text-muted mb-2 d-none">
                                    Pilih jam mengajar di kolom kiri terlebih dahulu, lalu CP/TP akan dimuat otomatis.
                                </small>

                                <!-- [NEW] Dropdown Pilih TP -->
                                <div class="input-group mb-2" id="container_pilih_tp" style="display:none;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i
                                                class="fas fa-list-ul text-primary"></i></span>
                                    </div>
                                    <select class="form-control border-left-0" id="pilih_tp">
                                        <option value="">-- Pilih Tujuan Pembelajaran --</option>
                                    </select>
                                </div>

                                <div class="alert alert-info py-2 mb-2 d-none" id="message_manual_tp">
                                    <small>Belum ada CP/TP yang diimpor untuk mata pelajaran ini. Silakan isi secara manual.</small>
                                </div>

                                <textarea name="tujuan_pembelajaran" id="tujuan_pembelajaran" rows="3"
                                    class="form-control" style="border-radius: 8px;"
                                    placeholder="Tuliskan tujuan pembelajaran hari ini atau pilih dari daftar..." style="border-radius: 8px; border: 1px solid #c7d2fe; transition: border-color 0.2s;"
                                    required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark"><i class="fas fa-tasks mr-1"></i> Tagihan / Tugas</label>
                                <textarea name="tagihan" rows="2" class="form-control" style="border-radius: 8px;"
                                    placeholder="Tugas atau PR untuk siswa (opsional)"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark"><i class="fas fa-user-check mr-1"></i> Rekap Absensi
                                    (Otomatis)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0"
                                            style="border-radius: 8px 0 0 8px;"><i
                                                class="fas fa-user-check text-muted"></i></span>
                                    </div>
                                    <textarea name="catatan_absensi" id="catatan_absensi" rows="2"
                                        class="form-control border-left-0"
                                        style="border-radius: 0 8px 8px 0; background-color: #f8f9fa;"
                                        placeholder="Akan terisi otomatis dari data absensi..." readonly></textarea>
                                </div>
                            </div>
                            <!-- FOTO / DOKUMENTASI KEGIATAN (Menggantikan Keterangan Tambahan) -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-uppercase mb-2 text-dark">
                                    <i class="fas fa-camera mr-1"></i> Dokumentasi / Foto Kegiatan
                                    <span class="text-muted font-weight-normal text-lowercase">(opsional)</span>
                                </label>
                                
                                <input type="file" name="foto_kegiatan" id="fotoInputHidden" accept="image/*" style="display:none;" onchange="previewFotoFromInput(this)">
                                
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm px-3 font-weight-bold" style="border-radius: 6px;" onclick="document.getElementById('fotoInputHidden').click()">
                                        <i class="fas fa-image mr-1"></i> Pilih Foto
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success shadow-sm px-3 font-weight-bold" style="border-radius: 6px;" onclick="bukaKameraLive()">
                                        <i class="fas fa-video mr-1"></i> Live Kamera
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Maksimal 2MB (JPG/PNG)</small>
                                
                                <!-- Preview Foto Ringkas / Compact -->
                                <div id="previewFotoContainer" style="display:none;" class="mt-2 p-2 bg-light rounded border">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center" style="gap: 10px;">
                                            <img id="previewFotoImg" src="#" alt="Preview" class="img-thumbnail" style="width: 55px; height: 55px; object-fit: cover; border-radius: 6px;">
                                            <div>
                                                <span id="namaFileFoto" class="small text-dark font-weight-bold d-block text-truncate" style="max-width: 180px;"></span>
                                                <span class="badge badge-success" style="font-size: 0.7rem;"><i class="fas fa-check mr-1"></i> Siap diupload</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusFotoJurnal()" title="Hapus foto" style="border-radius: 6px;">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                    <button type="submit" id="simpan_jurnal_btn" class="btn font-weight-bold px-5 py-3 shadow" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; border-radius: 12px; font-size: 1rem; letter-spacing: 0.5px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(79,70,229,0.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <i class="fas fa-save mr-2"></i> Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- ==== MODAL LIVE KAMERA (di luar form & section) ==== -->
<div class="modal fade" id="modalKameraJurnal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title mb-0 font-weight-bold">
                    <i class="fas fa-video mr-2 text-success"></i>Live Kamera
                </h6>
                <button type="button" class="close text-white opacity-8" onclick="tutupKameraJurnal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-black p-0 text-center" style="position: relative;">
                <video id="videoStreamJurnal" autoplay playsinline muted 
                    style="width:100%; max-height:360px; object-fit:cover; display:block;"></video>
                <canvas id="canvasCaptureJurnal" style="display:none;"></canvas>
                <div id="flashEffect" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:white; opacity:0.8;"></div>
            </div>
            <div class="modal-footer bg-dark d-flex justify-content-between align-items-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" onclick="gantiKameraJurnal()" title="Ganti kamera depan/belakang">
                    <i class="fas fa-sync-alt mr-1"></i> Ganti
                </button>
                <button type="button" class="btn btn-success px-4 py-2" onclick="jepretFotoJurnal()" 
                    style="border-radius: 50px; font-size: 1.1rem; box-shadow: 0 0 15px rgba(40,167,69,0.5);">
                    <i class="fas fa-circle mr-1"></i> Jepret!
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="tutupKameraJurnal()">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var _kameraStream = null;
var _facingMode = "environment";

function previewFotoFromInput(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        if (file.size > 2097152) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            hapusFotoJurnal();
            return;
        }
        tampilkanPreviewJurnal(file);
    }
}

function tampilkanPreviewJurnal(file) {
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewFotoImg').src = e.target.result;
        document.getElementById('namaFileFoto').innerHTML = '<i class="fas fa-file-image mr-1"></i>' + file.name;
        document.getElementById('previewFotoContainer').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function bukaKameraLive() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser Anda tidak mendukung akses kamera langsung. Gunakan tombol "Pilih Foto" untuk memilih dari galeri.');
        return;
    }
    $('#modalKameraJurnal').modal('show');
    _mulaiKameraJurnal();
}

function _mulaiKameraJurnal() {
    var constraints = { video: { facingMode: _facingMode, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false };
    navigator.mediaDevices.getUserMedia(constraints)
        .then(function(stream) {
            _kameraStream = stream;
            var v = document.getElementById('videoStreamJurnal');
            v.srcObject = stream;
            v.play();
        })
        .catch(function(err) {
            console.error('Camera error:', err);
            tutupKameraJurnal();
            if (err.name === 'NotAllowedError') {
                alert('Izin kamera ditolak. Buka pengaturan browser dan izinkan akses kamera untuk situs ini.');
            } else if (err.name === 'NotFoundError') {
                alert('Tidak ada kamera yang ditemukan di perangkat ini.');
            } else {
                alert('Gagal membuka kamera: ' + err.message);
            }
        });
}

function gantiKameraJurnal() {
    _facingMode = (_facingMode === "environment") ? "user" : "environment";
    if (_kameraStream) {
        _kameraStream.getTracks().forEach(function(t) { t.stop(); });
        _kameraStream = null;
    }
    _mulaiKameraJurnal();
}

function jepretFotoJurnal() {
    var video = document.getElementById('videoStreamJurnal');
    var canvas = document.getElementById('canvasCaptureJurnal');
    canvas.width = video.videoWidth || 1280;
    canvas.height = video.videoHeight || 720;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    // Efek flash
    var flash = document.getElementById('flashEffect');
    flash.style.display = 'block';
    setTimeout(function() { flash.style.display = 'none'; }, 200);
    
    canvas.toBlob(function(blob) {
        var fileName = 'kbm_' + new Date().toISOString().slice(0,19).replace(/[:T]/g, '-') + '.jpg';
        var file = new File([blob], fileName, { type: 'image/jpeg' });
        
        var dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('fotoInputHidden').files = dt.files;
        
        tampilkanPreviewJurnal(file);
        tutupKameraJurnal();
    }, 'image/jpeg', 0.92);
}

function tutupKameraJurnal() {
    if (_kameraStream) {
        _kameraStream.getTracks().forEach(function(t) { t.stop(); });
        _kameraStream = null;
    }
    $('#modalKameraJurnal').modal('hide');
}

function hapusFotoJurnal() {
    document.getElementById('fotoInputHidden').value = '';
    document.getElementById('previewFotoContainer').style.display = 'none';
    document.getElementById('previewFotoImg').src = '#';
    document.getElementById('namaFileFoto').innerHTML = '';
}

$('#modalKameraJurnal').on('hidden.bs.modal', function() {
    if (_kameraStream) {
        _kameraStream.getTracks().forEach(function(t) { t.stop(); });
        _kameraStream = null;
    }
});
</script>





<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kelasSelect = document.getElementById('id_kelas');
        const tanggalInput = document.getElementById('tanggal');
        const jamContainer = document.getElementById('jam_mengajar_container');
        const absensiTextarea = document.getElementById('catatan_absensi');
        const notifContainer = document.getElementById('notif_absensi_container');
        const simpanBtn = document.getElementById('simpan_jurnal_btn');

        // [NEW] Elements for CP/TP
        const containerPilihCp = document.getElementById('container_pilih_cp');
        const selectPilihCp = document.getElementById('pilih_cp');
        const containerPilihTp = document.getElementById('container_pilih_tp');
        const selectPilihTp = document.getElementById('pilih_tp');
        const messageManualTp = document.getElementById('message_manual_tp');
        const infoPilihJam = document.getElementById('info_pilih_jam');
        const textareaTp = document.getElementById('tujuan_pembelajaran');

        function fetchData() {
            const idKelas = kelasSelect.value;
            const tanggal = tanggalInput.value;

            // Reset semua state
            jamContainer.innerHTML = '<p class="text-muted">-- Pilih Kelas dan Tanggal Dahulu --</p>';
            absensiTextarea.value = '';
            notifContainer.style.display = 'none';
            simpanBtn.disabled = true;

            // Reset CP/TP
            containerPilihCp.style.display = 'none';
            containerPilihTp.style.display = 'none';
            selectPilihCp.innerHTML = '<option value="">-- Pilih Capaian Pembelajaran (CP) --</option>';
            selectPilihTp.innerHTML = '<option value="">-- Pilih TP dari Database --</option>';
            messageManualTp.classList.add('d-none');

            if (!idKelas || !tanggal) return;

            jamContainer.innerHTML = '<p class="text-muted">Memuat jadwal...</p>';

            // 1. Ambil data jadwal
            // REVISI: Menggunakan public/api.php directly
            fetch(`api.php?mod=jadwal&act=get_by_kelas_dan_tanggal&id_kelas=${idKelas}&tanggal=${tanggal}`)
                .then(res => res.json()).then(result => {
                    if (result.status === 'ok' && result.data.length > 0) {
                        jamContainer.innerHTML = '';
                        let hasTahfidz = false;

                        result.data.forEach(item => {
                            const jamMulai = item.jam_mulai.substring(0, 5);
                            const jamSelesai = item.jam_selesai.substring(0, 5);
                            const optionText = `${jamMulai} - ${jamSelesai} | ${item.nama_mapel}`;
                            const checkboxDiv = document.createElement('div');
                            checkboxDiv.className = 'form-check';

                            // [MODIFIED] Store id_mapel and tingkat in data attributes
                            // Note: item.tingkat comes from API (added recently)
                            checkboxDiv.innerHTML = `<input class="form-check-input jam-checkbox" type="checkbox" 
                                name="jam_mengajar[]" 
                                value="${item.id_jadwal_mengajar}" 
                                id="jam_${item.id_jadwal_mengajar}"
                                data-mapel="${item.id_mapel}"
                                data-tingkat="${item.tingkat}"
                                data-guru-mapel="${item.id_guru_mapel || ''}">
                                <label class="form-check-label" for="jam_${item.id_jadwal_mengajar}">${optionText}</label>`;
                            jamContainer.appendChild(checkboxDiv);

                            // Check for Tahfidz
                            if (item.nama_mapel.toLowerCase().includes('tahfidz')) {
                                hasTahfidz = true;
                            }
                        });

                        if (hasTahfidz) {
                            const linkDiv = document.createElement('div');
                            linkDiv.className = 'alert alert-info mt-2 mb-0 p-2';
                            linkDiv.innerHTML = '<small><i class="fas fa-info-circle"></i> Terdeteksi Jadwal Tahfidz. Jangan lupa isi jurnal kelompok/halaqah.</small> <a href="<?= BASE_URL ?>tahfidz" target="_blank" class="btn btn-xs btn-light text-info font-weight-bold ml-2">Buka Modul Tahfidz <i class="fas fa-external-link-alt"></i></a>';
                            jamContainer.appendChild(linkDiv);
                        }

                        // [NEW] Event Listener for Checkboxes
                        document.querySelectorAll('.jam-checkbox').forEach(chk => {
                            chk.addEventListener('change', function () {
                                updateCheckedMapel();
                                // [FIX] Refresh rekap absensi sesuai mapel yang dipilih
                                fetchRekapAbsensi(idKelas, tanggal);
                            });
                        });

                        function updateCheckedMapel() {
                            const checked = document.querySelectorAll('.jam-checkbox:checked');
                            if (checked.length === 0) {
                                containerPilihCp.style.display = 'none';
                                containerPilihTp.style.display = 'none';
                                messageManualTp.classList.add('d-none');
                                infoPilihJam.classList.remove('d-none');
                                selectPilihCp.innerHTML = '<option value="">-- Pilih Capaian Pembelajaran (CP) --</option>';
                                selectPilihTp.innerHTML = '<option value="">-- Pilih Tujuan Pembelajaran --</option>';
                                return;
                            }

                            infoPilihJam.classList.add('d-none');
                            const first = checked[0];
                            const idMapel = first.getAttribute('data-mapel');
                            const tingkat = first.getAttribute('data-tingkat');
                            loadCpByMapelTingkat(idMapel, tingkat);
                        }

                        if (result.data.length === 1) {
                            const onlyCheckbox = document.querySelector('.jam-checkbox');
                            if (onlyCheckbox) {
                                onlyCheckbox.checked = true;
                                updateCheckedMapel();
                            }
                        } else {
                            infoPilihJam.classList.remove('d-none');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    jamContainer.innerHTML = '<p class="text-danger font-italic">-- Gagal memuat jadwal --</p>';
                });

            // 2. Cek status absensi - [FIX] Kirim id_guru_mapel dari checkbox yang dipilih
            // Ambil id_guru_mapel dari data-attribute checkbox yang dicentang
            function fetchRekapAbsensi(idKelas, tanggal) {
                const checkedJam = document.querySelector('.jam-checkbox:checked');
                const idGuruMapel = checkedJam ? (checkedJam.dataset.guruMapel || '') : '';
                const apiUrl = `api.php?mod=absensi&act=get_status_for_jurnal&id_kelas=${idKelas}&tanggal=${tanggal}` +
                               (idGuruMapel ? `&id_guru_mapel=${idGuruMapel}` : '');

                fetch(apiUrl)
                    .then(res => res.json()).then(result => {
                        if (result.status === 'ok') {
                            if (result.absensi_diisi) {
                                absensiTextarea.value = result.rekap_absensi;
                                notifContainer.style.display = 'none';
                                simpanBtn.disabled = false;
                            } else {
                                const linkAbsen = `<?= BASE_URL ?>absensi_mapel/form?id_kelas=${idKelas}&tanggal=${tanggal}`;
                                notifContainer.innerHTML = `<div class="alert alert-warning"><h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>Absensi untuk kelas dan tanggal ini belum diisi. Silakan isi absensi terlebih dahulu.<br><a href="${linkAbsen}" class="btn btn-sm btn-warning mt-2"><b>Klik di sini untuk mengisi absensi</b></a></div>`;
                                notifContainer.style.display = 'block';
                                simpanBtn.disabled = true;
                            }
                        }
                    });
            }
            fetchRekapAbsensi(idKelas, tanggal);
        }

        // [NEW] Function to Load TP
        function loadCpByMapelTingkat(idMapel, tingkat) {
            if (!idMapel || !tingkat) return;

            selectPilihCp.innerHTML = '<option value="">Memuat CP...</option>';
            containerPilihCp.style.display = 'flex';
            containerPilihTp.style.display = 'none';
            messageManualTp.classList.add('d-none');
            selectPilihTp.innerHTML = '<option value="">-- Pilih Tujuan Pembelajaran --</option>';

            fetch(`api.php?mod=cptp&act=get_cp_by_mapel_tingkat&id_mapel=${idMapel}&tingkat=${tingkat}`)
                .then(res => res.json())
                .then(result => {
                    if (result.status === 'ok' && result.data.length > 0) {
                        selectPilihCp.innerHTML = '<option value="">-- Pilih Capaian Pembelajaran (CP) --</option>';
                        result.data.forEach(cp => {
                            const option = document.createElement('option');
                            option.value = cp.id_cp;
                            option.text = cp.deskripsi_cp.substring(0, 150);
                            option.dataset.deskripsiCp = cp.deskripsi_cp;
                            selectPilihCp.appendChild(option);
                        });
                    } else {
                        containerPilihCp.style.display = 'none';
                        messageManualTp.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    console.error(err);
                    selectPilihCp.innerHTML = '<option value="">-- Gagal Memuat CP --</option>';
                });
        }

        function loadTpByCp(idCp, idMapel) {
            if (!idCp || !idMapel) return;

            selectPilihTp.innerHTML = '<option value="">Memuat TP...</option>';
            containerPilihTp.style.display = 'flex';
            messageManualTp.classList.add('d-none');

            fetch(`api.php?mod=cptp&act=get_tp_by_cp&id_cp=${idCp}&id_mapel=${idMapel}`)
                .then(res => res.json())
                .then(result => {
                    if (result.status === 'ok' && result.data.length > 0) {
                        selectPilihTp.innerHTML = '<option value="">-- Pilih Tujuan Pembelajaran --</option>';
                        result.data.forEach(tp => {
                            const option = document.createElement('option');
                            option.value = tp.deskripsi_tp;
                            option.text = tp.kode_tp + ' - ' + tp.deskripsi_tp.substring(0, 100) + '...';
                            selectPilihTp.appendChild(option);
                        });
                    } else {
                        selectPilihTp.innerHTML = '<option value="">-- Belum ada TP untuk CP ini --</option>';
                        messageManualTp.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    console.error(err);
                    selectPilihTp.innerHTML = '<option value="">-- Gagal Memuat TP --</option>';
                });
        }

        // [NEW] Event Listener for CP Select
        selectPilihCp.addEventListener('change', function () {
            const selectedCpId = this.value;
            const checked = document.querySelectorAll('.jam-checkbox:checked');
            if (selectedCpId && checked.length > 0) {
                const idMapel = checked[0].getAttribute('data-mapel');
                loadTpByCp(selectedCpId, idMapel);
            } else {
                containerPilihTp.style.display = 'none';
                selectPilihTp.innerHTML = '<option value="">-- Pilih Tujuan Pembelajaran --</option>';
            }
        });

        // [NEW] Event Listener for TP Select
        selectPilihTp.addEventListener('change', function () {
            if (this.value) {
                textareaTp.value = this.value;
            }
        });

        kelasSelect.addEventListener('change', fetchData);
        tanggalInput.addEventListener('change', fetchData);

        // [REVISI 3] Panggil sekali saat halaman dimuat
        // Karena kelas dan tanggal sudah diisi otomatis,
        // ini akan langsung memuat jadwal dan absensi.
        fetchData();
    });
</script>
<?php include __DIR__ . '/partials/footer.php'; ?>