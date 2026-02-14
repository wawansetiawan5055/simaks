<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header p-0 pt-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 px-4">
            <div>
                <h1 class="m-0"><i class="fas fa-book-reader mr-2"></i> Formulir Jurnal KBM</h1>
                <p class="text-muted small mb-0">Input jurnal harian kegiatan belajar mengajar.</p>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Alerts handled by toast -->

        <div id="notif_absensi_container" style="display: none;"></div>

        <div class="card shadow-sm border-0"
            style="border-radius: 15px; overflow: hidden; border-top: 4px solid #3b82f6;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-edit mr-2 text-primary"></i> Input Jurnal
                    Harian</h6>
            </div>
            <form action="index.php?mod=jurnal_kbm&act=save" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-right">

                            <!-- [REVISI 1] Menambahkan logika 'selected' pada dropdown kelas -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Kelas</label>
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
                                <label class="font-weight-bold text-muted small text-uppercase">Tanggal</label>
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
                                <label class="font-weight-bold text-muted small text-uppercase mb-3 block">Jam Mengajar
                                    <span class="badge badge-light border ml-2">Pilih satu atau lebih</span></label>
                                <div id="jam_mengajar_container" class="p-3 bg-light rounded border"
                                    style="min-height: 100px;">
                                    <p class="text-muted text-center small my-4"><i class="fas fa-info-circle mr-1"></i>
                                        Pilih Kelas dan Tanggal Dahulu</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 pl-md-4">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Capaian & Tujuan
                                    Pembelajaran</label>

                                <!-- [NEW] Dropdown Pilih TP -->
                                <div class="input-group mb-2" id="container_pilih_tp" style="display:none;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i
                                                class="fas fa-list-ul text-primary"></i></span>
                                    </div>
                                    <select class="form-control border-left-0" id="pilih_tp">
                                        <option value="">-- Pilih TP dari Database --</option>
                                    </select>
                                </div>

                                <textarea name="tujuan_pembelajaran" id="tujuan_pembelajaran" rows="3"
                                    class="form-control" style="border-radius: 8px;"
                                    placeholder="Tuliskan tujuan pembelajaran hari ini atau pilih dari daftar..."
                                    required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Tagihan / Tugas</label>
                                <textarea name="tagihan" rows="2" class="form-control" style="border-radius: 8px;"
                                    placeholder="Tugas atau PR untuk siswa (opsional)"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Rekap Absensi
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
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Keterangan
                                    Tambahan</label>
                                <textarea name="keterangan" rows="2" class="form-control" style="border-radius: 8px;"
                                    placeholder="Catatan lain (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                    <button type="submit" id="simpan_jurnal_btn" class="btn btn-primary px-5 font-weight-bold shadow-sm"
                        style="border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kelasSelect = document.getElementById('id_kelas');
        const tanggalInput = document.getElementById('tanggal');
        const jamContainer = document.getElementById('jam_mengajar_container');
        const absensiTextarea = document.getElementById('catatan_absensi');
        const notifContainer = document.getElementById('notif_absensi_container');
        const simpanBtn = document.getElementById('simpan_jurnal_btn');

        // [NEW] Elements for TP
        const containerPilihTp = document.getElementById('container_pilih_tp');
        const selectPilihTp = document.getElementById('pilih_tp');
        const textareaTp = document.getElementById('tujuan_pembelajaran');

        function fetchData() {
            const idKelas = kelasSelect.value;
            const tanggal = tanggalInput.value;

            // Reset semua state
            jamContainer.innerHTML = '<p class="text-muted">-- Pilih Kelas dan Tanggal Dahulu --</p>';
            absensiTextarea.value = '';
            notifContainer.style.display = 'none';
            simpanBtn.disabled = true;

            // Reset TP
            containerPilihTp.style.display = 'none';
            selectPilihTp.innerHTML = '<option value="">-- Pilih TP dari Database --</option>';

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
                                data-tingkat="${item.tingkat}">
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
                            linkDiv.innerHTML = '<small><i class="fas fa-info-circle"></i> Terdeteksi Jadwal Tahfidz. Jangan lupa isi jurnal kelompok/halaqah.</small> <a href="index.php?mod=tahfidz" target="_blank" class="btn btn-xs btn-light text-info font-weight-bold ml-2">Buka Modul Tahfidz <i class="fas fa-external-link-alt"></i></a>';
                            jamContainer.appendChild(linkDiv);
                        }

                        // [NEW] Event Listener for Checkboxes
                        document.querySelectorAll('.jam-checkbox').forEach(chk => {
                            chk.addEventListener('change', function () {
                                if (this.checked) {
                                    loadTp(this.getAttribute('data-mapel'), this.getAttribute('data-tingkat'));
                                } else {
                                    // If uncheck, verify if any other is checked? 
                                    // For simplicity: if no checkbox checked, hide TP dropdown
                                    if (document.querySelectorAll('.jam-checkbox:checked').length === 0) {
                                        containerPilihTp.style.display = 'none';
                                    }
                                }
                            });
                        });

                    } else {
                        jamContainer.innerHTML = '<p class="text-danger font-italic">-- Tidak ada jadwal mengajar di hari ini --</p>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    jamContainer.innerHTML = '<p class="text-danger font-italic">-- Gagal memuat jadwal --</p>';
                });

            // 2. Cek status absensi
            // REVISI: Menggunakan public/api.php directly
            fetch(`api.php?mod=absensi&act=get_status_for_jurnal&id_kelas=${idKelas}&tanggal=${tanggal}`)
                .then(res => res.json()).then(result => {
                    if (result.status === 'ok') {
                        if (result.absensi_diisi) {
                            // JIKA SUDAH DIISI: Isi textarea, sembunyikan notif, aktifkan tombol simpan
                            absensiTextarea.value = result.rekap_absensi;
                            notifContainer.style.display = 'none';
                            simpanBtn.disabled = false;
                        } else {
                            // JIKA BELUM DIISI: Tampilkan notif, nonaktifkan tombol simpan
                            const linkAbsen = `index.php?mod=absensi_mapel&act=form&id_kelas=${idKelas}&tanggal=${tanggal}`;
                            notifContainer.innerHTML = `<div class="alert alert-warning"><h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian!</h5>Absensi untuk kelas dan tanggal ini belum diisi. Silakan isi absensi terlebih dahulu.<br><a href="${linkAbsen}" class="btn btn-sm btn-warning mt-2"><b>Klik di sini untuk mengisi absensi</b></a></div>`;
                            notifContainer.style.display = 'block';
                            simpanBtn.disabled = true;
                        }
                    }
                });
        }

        // [NEW] Function to Load TP
        function loadTp(idMapel, tingkat) {
            if (!idMapel || !tingkat) return;

            selectPilihTp.innerHTML = '<option value="">Memuat TP...</option>';
            containerPilihTp.style.display = 'flex';

            // REVISI: Menggunakan public/api.php directly
            fetch(`api.php?mod=cptp&act=get_tp_by_mapel_tingkat&id_mapel=${idMapel}&tingkat=${tingkat}`)
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
                        selectPilihTp.innerHTML = '<option value="">-- Tidak ada data TP untuk Mapel ini --</option>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    selectPilihTp.innerHTML = '<option value="">-- Gagal Memuat TP --</option>';
                });
        }

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