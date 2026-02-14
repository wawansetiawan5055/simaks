<?php
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__ . '/partials/header.php';
?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-calendar-check mr-2"></i> Formulir Absensi</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">

        <!-- [REVISI DIMULAI] Menambahkan tombol "Lanjut ke Jurnal" di dalam pesan sukses -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="callout callout-success d-flex justify-content-between align-items-center mb-3">
                <div class="text-success">
                    <i class="fas fa-check-circle mr-2"></i> Data berhasil disimpan.
                </div>
                <a href="index.php?mod=jurnal_kbm&id_kelas=<?= $id_kelas ?>&tanggal=<?= $tanggal ?>"
                    class="btn btn-success font-weight-bold">
                    Lanjut ke Jurnal KBM <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        <?php endif; ?>
        <!-- [REVISI SELESAI] -->

        <form action="index.php?mod=absensi_mapel&act=save" method="POST">
            <input type="hidden" name="id_kelas" id="id_kelas_hidden" value="<?= $id_kelas ?>">
            <input type="hidden" name="tanggal" id="tanggal_hidden" value="<?= $tanggal ?>">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelas: <strong><?= $kelas['nama_kelas'] ?></strong> | Tanggal:
                        <strong><?= DateHelper::formatTanggal($tanggal, 'long') ?></strong>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group col-md-6">
                        <label>Jam Mengajar (Pilih satu atau lebih)</label>
                        <div id="jam_mengajar_container">
                            <p class="text-muted">-- Memuat jadwal... --</p>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kehadiran</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($siswa_list as $s): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $s['nama'] ?><br><small class="text-muted">NISN: <?= $s['nisn'] ?></small></td>

                                    <!-- [REVISI] Mengembalikan ke Radio Button (sesuai file asli Anda) -->
                                    <td>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-outline-success active"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Hadir" checked>
                                                Hadir</label>
                                            <label class="btn btn-outline-warning"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Sakit">
                                                Sakit</label>


                                            <label class="btn btn-outline-info"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Izin"> Izin</label>

                                            <label class="btn btn-outline-danger"><input type="radio"
                                                    name="absensi[<?= $s['id_siswa'] ?>][status]" value="Alpa"> Alpa</label>
                                        </div>
                                    </td>
                                    <!-- [AKHIR REVISI] -->

                                    <td><input type="text" name="absensi[<?= $s['id_siswa'] ?>][keterangan]"
                                            class="form-control form-control-sm"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Simpan Absensi</button>
                    <a href="index.php?mod=absensi_mapel" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const idKelas = document.getElementById('id_kelas_hidden').value;
        const tanggal = document.getElementById('tanggal_hidden').value;
        const jamContainer = document.getElementById('jam_mengajar_container');

        function fetchJadwal() {
            if (!idKelas || !tanggal) return;

            // REVISI: Routing public/api.php directly
            fetch(`api.php?mod=jadwal&act=get_by_kelas_dan_tanggal&id_kelas=${idKelas}&tanggal=${tanggal}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'ok' && result.data.length > 0) {
                        jamContainer.innerHTML = ''; // Kosongkan container
                        result.data.forEach(item => {
                            const jamMulai = item.jam_mulai.substring(0, 5);
                            const jamSelesai = item.jam_selesai.substring(0, 5);
                            const optionText = `${jamMulai} - ${jamSelesai} | ${item.nama_mapel}`;

                            // Buat elemen checkbox
                            const checkboxDiv = document.createElement('div');
                            checkboxDiv.className = 'form-check';
                            checkboxDiv.innerHTML = `
                            <input class="form-check-input" type="checkbox" name="jam_mengajar[]" value="${item.id_jadwal_mengajar}" id="jam_${item.id_jadwal_mengajar}">
                            <label class="form-check-label" for="jam_${item.id_jadwal_mengajar}">${optionText}</label>
                        `;
                            jamContainer.appendChild(checkboxDiv);
                        });
                    } else {
                        jamContainer.innerHTML = '<p class="text-danger font-italic">-- Tidak ada jadwal di hari ini --</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching schedule:', error);
                    jamContainer.innerHTML = '<p class="text-danger font-italic">-- Gagal memuat jadwal --</p>';
                });
        }
        fetchJadwal();
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>