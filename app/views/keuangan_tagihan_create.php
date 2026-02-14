<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        --navy: #1e293b;
        --soft-bg: #f8fafc;
    }

    .main {
        background: var(--soft-bg);
    }

    .premium-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .premium-card .card-header {
        background: var(--primary-gradient);
        color: white;
        padding: 1rem 1.5rem;
        border: none;
    }

    .premium-card .card-title {
        color: white !important;
        font-weight: 700 !important;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group-label {
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .premium-input {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0.6rem 0.75rem !important;
        font-size: 0.95rem !important;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .premium-input:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        outline: none;
    }

    .step-badge {
        width: 24px;
        height: 24px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
    }

    #studentListCheckbox {
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        max-height: 400px;
        overflow-y: auto;
    }

    .student-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-item:hover {
        background: #f8fafc;
    }

    .student-item:last-child {
        border-bottom: none;
    }

    .btn-generate {
        background: var(--primary-gradient);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4);
        transition: all 0.2s;
    }

    .btn-generate:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .btn-generate:active {
        transform: translateY(0);
    }

    .info-banner {
        background: #f1f5f9;
        /* Solid light grey for stability */
        border: 2px solid #475569;
        /* Thick dark border */
        border-left: 6px solid #4f46e5;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #0f172a !important;
        /* Force dark text */
        font-weight: 700;
        /* Bold for readability */
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .info-banner i {
        color: #4f46e5;
        font-size: 1.4rem;
    }

    .ta-badge-premium {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
</style>

<main id="main" class="main">
    <div class="container-fluid">
        <div class="pagetitle pt-3 mb-4 d-flex justify-content-between align-items-center">
            <h1 class="m-0"><i class="fas fa-plus-circle mr-2"></i> Generate Tagihan Massal</h1>
            <nav class="d-none d-md-block">
                <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?mod=keuangan_tagihan&act=index">Tagihan Siswa</a>
                    </li>
                    <li class="breadcrumb-item active">Generate</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card premium-card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-magic"></i> Wizard Penagihan Otomatis
                            </h5>
                        </div>
                        <div class="card-body px-4 py-4">

                            <div class="row mb-4 align-items-stretch">
                                <div class="col-md-12">
                                    <div class="ta-badge-premium">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                        <div class="ml-2">
                                            <div class="small text-muted text-uppercase font-weight-bold"
                                                style="font-size: 0.65rem;">Tahun Ajaran Aktif</div>
                                            <div class="font-weight-bold text-navy mb-0" style="font-size: 0.95rem;">
                                                <?= $taAktif ? $taAktif['nama_ta'] : '-' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form id="formGenerate" action="index.php?mod=keuangan_tagihan&act=store" method="POST">
                                <input type="hidden" name="tahun_ajaran"
                                    value="<?= $taAktif ? $taAktif['id_ta'] : '' ?>">

                                <!-- ROW 1: CORE TARGETS -->
                                <div class="row g-3 align-items-end mb-4">
                                    <!-- STEP 1: TARGET SISWA (KELAS) -->
                                    <div class="col-md-6">
                                        <label class="form-group-label mb-1" style="font-size: 0.75rem;"><span
                                                class="step-badge mr-1"
                                                style="width:18px; height:18px; font-size:0.6rem;">1</span> Target Siswa
                                            (Kelas)</label>
                                        <select class="form-control premium-input shadow-sm" id="id_kelas"
                                            name="id_kelas" required onchange="loadStudents()">
                                            <option value="">-- Pilih Kelas Target --</option>
                                            <?php foreach ($kelasList as $kelas): ?>
                                                <option value="<?= $kelas['id_kelas'] ?>"><?= $kelas['nama_kelas'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- STEP 2: KATEGORI PEMBIAYAAN -->
                                    <div class="col-md-6">
                                        <label class="form-group-label mb-1" style="font-size: 0.75rem;"><span
                                                class="step-badge mr-1"
                                                style="width:18px; height:18px; font-size:0.6rem;">2</span> Kategori
                                            Pembiayaan (Jenis)</label>
                                        <select class="form-control premium-input shadow-sm" name="id_jenis"
                                            id="id_jenis" required onchange="togglePeriodInputs()" disabled>
                                            <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- ROW 2: PARAMETERS -->
                                <div class="row g-3 align-items-end mb-4">
                                    <!-- STEP 3: PERIODE -->
                                    <div class="col-md-9 d-flex gap-2" id="periodInputsContainer">
                                        <!-- Mulai -->
                                        <div class="flex-fill">
                                            <label class="form-group-label mb-1" style="font-size: 0.75rem;"><span
                                                    class="step-badge mr-1"
                                                    style="width:18px; height:18px; font-size:0.6rem;">3</span> <span
                                                    id="labelBulanAwal">Bulan Awal</span></label>
                                            <input type="month" class="form-control premium-input shadow-sm bg-white"
                                                name="bulan_awal" required value="<?= date('Y-m') ?>">
                                        </div>
                                        <!-- Sampai -->
                                        <div class="flex-fill" id="bulanAkhirCol">
                                            <label class="form-group-label mb-1" style="font-size: 0.75rem;">Bulan
                                                Akhir</label>
                                            <input type="month" class="form-control premium-input shadow-sm bg-white"
                                                name="bulan_akhir" required
                                                value="<?= date('Y-m', strtotime('+11 months')) ?>">
                                        </div>
                                    </div>

                                    <!-- STEP 4: JATUH TEMPO -->
                                    <div class="col-md-3">
                                        <label class="form-group-label mb-1" style="font-size: 0.75rem;"><span
                                                class="step-badge mr-1"
                                                style="width:18px; height:18px; font-size:0.6rem;">4</span> Jatuh
                                            Tempo</label>
                                        <select class="form-control premium-input shadow-sm bg-white"
                                            name="tanggal_jatuh_tempo_day" required>
                                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                                <option value="<?= $i ?>" <?= $i == 10 ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- SEPARATOR FOR STUDENT LIST -->
                                <div class="mb-4">
                                    <div id="siswaContainer" style="display:none;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="font-weight-bold mb-0 text-navy"><i
                                                    class="fas fa-users mr-1"></i> Daftar Siswa Penerima Tagihan</h6>
                                            <div id="studentCounter" class="badge badge-info shadow-sm py-2 px-3"
                                                style="border-radius: 20px;">
                                                0 Siswa Ditemukan
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center p-3 bg-navy text-white"
                                            style="border-radius: 12px 12px 0 0;">
                                            <div class="font-weight-bold small">DAFTAR SISWA</div>
                                            <div class="custom-control custom-checkbox mr-2 d-flex align-items-center">
                                                <input class="custom-control-input" type="checkbox" id="checkAll"
                                                    checked onchange="toggleAllStudents()">
                                                <label class="custom-control-label small" for="checkAll"
                                                    style="padding-top: 2px;">Pilih Semua</label>
                                            </div>
                                        </div>
                                        <div id="studentListCheckbox" class="mb-3">
                                            <!-- Loaded via AJAX -->
                                        </div>
                                    </div>
                                </div>

                                <!-- ACTIONS -->
                                <div class="text-right mt-3 border-top pt-3">
                                    <a href="index.php?mod=keuangan_tagihan&act=index"
                                        class="btn btn-link text-muted font-weight-bold py-2 px-4 mr-3">Batal</a>
                                    <button type="submit" class="btn btn-generate" id="btnSubmit">
                                        <i class="fas fa-magic mr-2"></i> Jalankan Engine Penagihan
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
</main>

<script>
    function togglePeriodInputs() {
        const select = document.getElementById('id_jenis');
        const selectedOption = select.options[select.selectedIndex];
        const isRecurring = selectedOption.getAttribute('data-recurring') === '1' || !selectedOption.value;

        const container = document.getElementById('bulanAkhirCol');
        const labelAwal = document.getElementById('labelBulanAwal');

        if (isRecurring) {
            container.style.display = 'block';
            labelAwal.innerText = 'Bulan Awal';
        } else {
            container.style.display = 'none';
            labelAwal.innerText = 'Periode Tagihan';
        }
    }

    function loadStudents() {
        var idKelas = document.getElementById('id_kelas').value;
        var container = document.getElementById('siswaContainer');
        var list = document.getElementById('studentListCheckbox');
        var counter = document.getElementById('studentCounter');

        if (!idKelas) {
            container.style.display = 'none';
            counter.style.display = 'none';
            list.innerHTML = '';
            return;
        }

        // New: Load Active Kinds for this class
        loadActiveKinds(idKelas);

        // Show loading
        container.style.display = 'block';
        list.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <div class="text-muted font-italic">Mencari data siswa di kelas...</div>
        </div>
    `;

        // AJAX Call
        fetch('../api/api.php?mod=keuangan&act=students_by_class&id_kelas=' + idKelas)
            .then(response => response.json())
            .then(data => {
                list.innerHTML = '';
                if (data && data.length > 0) {
                    counter.style.display = 'inline-block';
                    counter.innerText = data.length + ' Siswa Ditemukan';

                    data.forEach(siswa => {
                        var div = document.createElement('div');
                        div.className = 'student-item';
                        div.innerHTML = `
                        <div class="custom-control custom-checkbox ml-2">
                            <input class="custom-control-input student-check" type="checkbox" 
                                   name="id_siswa_specific[]" value="${siswa.id_siswa}" 
                                   id="s_${siswa.id_siswa}" checked onchange="updateCounter()">
                            <label class="custom-control-label" for="s_${siswa.id_siswa}"></label>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="font-weight-bold text-navy">${siswa.nama}</span>
                            <span class="small text-muted">NISN: ${siswa.nisn || '-'}</span>
                        </div>
                    `;
                        list.appendChild(div);
                    });
                } else {
                    list.innerHTML = '<div class="text-center p-5 text-danger font-weight-bold">Tidak ada siswa aktif di kelas ini.</div>';
                    counter.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                list.innerHTML = '<p class="text-danger text-center p-5">Gagal memuat data siswa.</p>';
            });
    }

    function loadActiveKinds(idKelas) {
        const select = document.getElementById('id_jenis');
        const originalValue = select.value;

        select.disabled = true;
        select.innerHTML = '<option value="">-- Sedang Memuat Kategori... --</option>';

        fetch('../api/api.php?mod=keuangan&act=active_kinds_by_class&id_kelas=' + idKelas)
            .then(response => response.json())
            .then(data => {
                select.disabled = false;
                select.innerHTML = '<option value="">-- Pilih Jenis Pembayaran --</option>';

                if (data && data.length > 0) {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id_jenis;
                        option.text = `${item.nama_jenis} [${item.nama_kategori}]`;
                        option.setAttribute('data-recurring', item.is_recurring);
                        if (item.id_jenis == originalValue) option.selected = true;
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">-- Tidak ada kategori aktif (Cek Matriks) --</option>';
                }
                togglePeriodInputs();
            })
            .catch(error => {
                console.error('Error:', error);
                select.disabled = false;
                select.innerHTML = '<option value="">-- Gagal memuat kategori --</option>';
            });
    }

    function updateCounter() {
        var checked = document.querySelectorAll('.student-check:checked').length;
        var counter = document.getElementById('studentCounter');
        counter.innerText = checked + ' Siswa Terpilih';
    }

    function toggleAllStudents() {
        var master = document.getElementById('checkAll');
        var checkboxes = document.querySelectorAll('.student-check');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateCounter();
    }

    document.getElementById('formGenerate').addEventListener('submit', function (e) {
        e.preventDefault();

        var checked = document.querySelectorAll('.student-check:checked').length;
        if (checked === 0) {
            alert('Mohon pilih minimal satu siswa.');
            return;
        }

        if (!confirm('Apakah anda yakin ingin menjalankan proses penagihan massal? Sistem akan memproses data berdasarkan aturan Matriks yang ada.')) return;

        var btn = document.getElementById('btnSubmit');
        var originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status"></span> Sedang Memproses...';

        var formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text(); // Get raw text first for debugging
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#6366f1'
                        }).then(() => {
                            window.location.href = 'index.php?mod=keuangan_tagihan&act=index';
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                } catch (e) {
                    console.error('Invalid JSON:', text);
                    Swal.fire('Error Sistem', 'Terjadi kesalahan pada server. Response: ' + text.substring(0, 100), 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                Swal.fire('Koneksi Gagal', 'Terjadi kesalahan koneksi ke server: ' + error.message, 'error');
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>

<?php include '../app/views/partials/footer.php'; ?>