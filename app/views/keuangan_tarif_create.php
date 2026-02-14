<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="fas fa-plus-circle mr-2"></i> Tambah Tarif Khusus</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php?mod=keuangan_tarif&act=index">Tarif Khusus</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Aturan Tarif</h5>

                        <form action="index.php?mod=keuangan_tarif&act=store" method="POST">

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Jenis Transaksi</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="id_jenis" required
                                        onchange="updateDefaultPrice(this)">
                                        <option value="">-- Pilih Jenis --</option>
                                        <?php foreach ($jenisList as $jenis): ?>
                                            <option value="<?= $jenis['id_jenis'] ?>"
                                                data-price="<?= $jenis['harga_default'] ?>">
                                                <?= $jenis['nama_jenis'] ?> (Std:
                                                <?= number_format($jenis['harga_default'], 0, ',', '.') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Lingkup (Scope)</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="scope_type" id="scope_kelas"
                                            value="class" checked onchange="toggleScope()">
                                        <label class="form-check-label" for="scope_kelas">Satu Kelas (Semua
                                            Siswa)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="scope_type" id="scope_siswa"
                                            value="student" onchange="toggleScope()">
                                        <label class="form-check-label" for="scope_siswa">Spesifik Siswa</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Scope: Kelas -->
                            <div class="row mb-3" id="class_container">
                                <label class="col-sm-3 col-form-label">Pilih Kelas</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="id_kelas" id="id_kelas">
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($kelasList as $kelas): ?>
                                            <option value="<?= $kelas['id_kelas'] ?>"><?= $kelas['nama_kelas'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Scope: Siswa (Modal Trigger) -->
                            <div class="row mb-3" id="student_container" style="display:none;">
                                <label class="col-sm-3 col-form-label">Pilih Siswa</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="hidden" name="id_siswa" id="id_siswa_input">
                                        <input type="text" class="form-control" id="nama_siswa_display" readonly
                                            placeholder="Klik tombol cari untuk memilih siswa -->">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalCariSiswa">
                                            <i class="bi bi-search"></i> Cari Siswa
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nominal Khusus (Rp)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nominal" id="nominal" required
                                        placeholder="Contoh: 175.000" onkeyup="formatRupiah(this)">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Keterangan</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="keterangan" rows="2"
                                        placeholder="Contoh: Tarif Khusus Kelas XII atau Beasiswa Prestasi"></textarea>
                                </div>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary">Simpan Aturan</button>
                                    <a href="index.php?mod=keuangan_tarif&act=index" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Modal Cari Siswa -->
    <div class="modal fade" id="modalCariSiswa" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cari Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Kelas di dalam Modal -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Filter Kelas</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="modal_id_kelas" onchange="loadStudentsToModal()">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelasList as $kelas): ?>
                                    <option value="<?= $kelas['id_kelas'] ?>"><?= $kelas['nama_kelas'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modal_student_list">
                                <tr>
                                    <td colspan="3" class="text-center">Silakan pilih kelas terlebih dahulu.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    function formatRupiah(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }

    function updateDefaultPrice(select) {
        var option = select.options[select.selectedIndex];
        var price = option.getAttribute('data-price');
    }

    function toggleScope() {
        var scope = document.querySelector('input[name="scope_type"]:checked').value;
        var classContainer = document.getElementById('class_container');
        var studentContainer = document.getElementById('student_container');
        var classSelect = document.getElementById('id_kelas');
        var studentInput = document.getElementById('id_siswa_input');

        if (scope === 'student') {
            classContainer.style.display = 'none';
            studentContainer.style.display = 'flex';

            // Remove Required from Class
            classSelect.removeAttribute('required');

            // Add Required to Student (Validation logic or custom check needed as hidden inputs dont work well with 'required' sometimes, but we check PHP side)
            studentInput.value = ''; // Reset
            document.getElementById('nama_siswa_display').value = '';

        } else {
            classContainer.style.display = 'flex';
            studentContainer.style.display = 'none';

            classSelect.setAttribute('required', 'required');
        }
    }

    function loadStudentsToModal() {
        var idKelas = document.getElementById('modal_id_kelas').value;
        var tbody = document.getElementById('modal_student_list');

        if (!idKelas) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center">Silakan pilih kelas terlebih dahulu.</td></tr>';
            return;
        }

        tbody.innerHTML = '<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"></div> Loading...</td></tr>';

        fetch('api/api.php?mod=keuangan&act=students_by_class&id_kelas=' + idKelas)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data && data.length > 0) {
                    data.forEach(siswa => {
                        var tr = document.createElement('tr');
                        tr.innerHTML = `
                        <td>${siswa.nisn || '-'}</td>
                        <td>${siswa.nama}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success" onclick="selectStudent('${siswa.id_siswa}', '${siswa.nama.replace(/'/g, "\\'")}')">
                                <i class="bi bi-check-circle"></i> Pilih
                            </button>
                        </td>
                    `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Tidak ada siswa ditemukan di kelas ini.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data.</td></tr>';
            });
    }

    function selectStudent(id, nama) {
        document.getElementById('id_siswa_input').value = id;
        document.getElementById('nama_siswa_display').value = nama;

        // Close Modal (Using Bootstrap 5 API)
        var modalEl = document.getElementById('modalCariSiswa');
        var modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
    }
</script>

<?php include '../app/views/partials/footer.php'; ?>