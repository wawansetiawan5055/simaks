<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-clipboard-list mr-2"></i> Buat Agenda Penilaian Sumatif Baru</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <form action="index.php?mod=penilaian_sumatif&act=save_agenda" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="id_kelas" id="id_kelas" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($kelas_diajar as $k): ?>
                                        <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Mata Pelajaran</label>
                                <select name="id_guru_mapel" id="id_guru_mapel" class="form-control" required>
                                    <option value="">-- Pilih Kelas Dahulu --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama Penilaian</label>
                                <input type="text" name="nama_penilaian" class="form-control"
                                    placeholder="Contoh: Sumatif Tengah Semester Ganjil" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Penilaian Sumatif</label>
                                <select name="jenis_sumatif" class="form-control" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Sumatif Lingkup Materi">Sumatif Lingkup Materi</option>
                                    <option value="Sumatif Tengah Semester">Sumatif Tengah Semester</option>
                                    <option value="Sumatif Akhir Semester">Sumatif Akhir Semester</option>
                                    <option value="Sumatif Akhir Tahun">Sumatif Akhir Tahun</option>
                                    <option value="Sumatif Akhir Jenjang">Sumatif Akhir Jenjang</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Penilaian (Opsional)</label>
                                <input type="date" name="tanggal_penilaian" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Pilih Tujuan Pembelajaran (TP) yang Dicakup Penilaian Ini:</label>
                        <div id="tp_container" class="row p-2"
                            style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: .25rem;">
                            <p class="text-muted">-- Pilih Kelas dan Mapel Dahulu --</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Agenda & Lanjut Input Nilai</button>
                    <a href="index.php?mod=penilaian_sumatif" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kelasSelect = document.getElementById('id_kelas');
        const mapelSelect = document.getElementById('id_guru_mapel');
        const tpContainer = document.getElementById('tp_container');

        function fetchMapel() {
            const idKelas = kelasSelect.value;
            mapelSelect.innerHTML = '<option value="">Memuat...</option>';
            tpContainer.innerHTML = '<p class="text-muted">-- Pilih Kelas dan Mapel Dahulu --</p>';
            if (!idKelas) {
                mapelSelect.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
                return;
            }

            // Panggil API untuk Mapel
            fetch(`../api/api.php?mod=sumatif&act=get_mapel_by_kelas&id_kelas=${idKelas}`)
                .then(response => response.json())
                .then(result => {
                    mapelSelect.innerHTML = '<option value="">-- Pilih Mapel --</option>';
                    if (result.status === 'ok' && result.data.length > 0) {
                        result.data.forEach(mapel => {
                            const option = new Option(mapel.nama_mapel, mapel.id_guru_mapel);
                            mapelSelect.appendChild(option);
                        });
                    } else {
                        mapelSelect.innerHTML = '<option value="">-- Tidak ada mapel diajar di kelas ini --</option>';
                    }
                    // Reset TP setelah mapel baru dimuat
                    fetchTp();
                })
                .catch(error => {
                    console.error('Error fetching mapel:', error);
                    mapelSelect.innerHTML = '<option value="">-- Gagal memuat mapel --</option>';
                });
        }

        function fetchTp() {
            const idGuruMapel = mapelSelect.value;
            tpContainer.innerHTML = '<p class="text-muted">Memuat TP...</p>';
            if (!idGuruMapel) {
                tpContainer.innerHTML = '<p class="text-muted">-- Pilih Mapel Dahulu --</p>';
                return;
            }

            // Panggil API untuk TP
            fetch(`../api/api.php?mod=sumatif&act=get_tp_by_mapel&id_guru_mapel=${idGuruMapel}`)
                .then(response => response.json())
                .then(result => {
                    tpContainer.innerHTML = ''; // Kosongkan
                    if (result.status === 'ok' && result.data.length > 0) {
                        result.data.forEach(tp => {
                            const checkboxDiv = document.createElement('div');
                            checkboxDiv.className = 'col-md-6 form-check';
                            checkboxDiv.innerHTML = `
                             <input class="form-check-input" type="checkbox" name="selected_tps[]" value="${tp.id_tp}" id="tp_${tp.id_tp}">
                             <label class="form-check-label" for="tp_${tp.id_tp}">${tp.kode_tp} - ${tp.deskripsi_tp}</label>
                         `;
                            tpContainer.appendChild(checkboxDiv);
                        });
                    } else {
                        tpContainer.innerHTML = '<p class="text-danger font-italic">-- Tidak ada TP untuk mapel ini --</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching TP:', error);
                    tpContainer.innerHTML = '<p class="text-danger font-italic">-- Gagal memuat TP --</p>';
                });
        }

        kelasSelect.addEventListener('change', fetchMapel);
        mapelSelect.addEventListener('change', fetchTp);
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>