<?php 
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__.'/partials/header.php'; 
?>
<section class="content-header">
  <div class="container-fluid"><h1>Catatan Kasus Siswa</h1></div>
</section>
<section class="content">
<div class="container-fluid">
    <?php // Session messages now handled by toast notifications in footer.php ?>
    <?php // Session messages now handled by toast notifications in footer.php ?>
<div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Input Catatan Kasus Baru</h3></div>
        <form action="index.php?mod=catatan_kasus&act=save" method="POST">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Kelas</label>
                            <select name="id_kelas_filter" id="id_kelas_filter" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach($kelas_list as $k): ?>
                                    <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Siswa</label>
                            <select name="id_siswa" id="id_siswa" class="form-control" required>
                                <option value="">-- Pilih Kelas Dahulu --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Catatan Kasus / Kejadian</label>
                    <textarea name="catatan" class="form-control" rows="3" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tindak Lanjut</label>
                            <textarea name="tindak_lanjut" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Catatan</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Daftar Kasus Tercatat (TA Aktif)</h3></div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead><tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Catatan</th><th>Tindak Lanjut</th><th>Pelapor</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $no=1; foreach($kasus_list as $k): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= DateHelper::formatTanggal($k['tanggal'], 'short') ?></td>
                        <td><?= htmlspecialchars($k['nama_siswa']) ?></td>
                        <td><?= htmlspecialchars($k['nama_kelas']) ?></td>
                        <td><?= htmlspecialchars($k['catatan']) ?></td>
                        <td><?= htmlspecialchars($k['tindak_lanjut']) ?></td>
                        <td><?= htmlspecialchars($k['nama_pelapor']) ?></td>
                        <td>
                            <a href="index.php?mod=catatan_kasus&act=delete&id=<?= $k['id_catatan'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasSelect = document.getElementById('id_kelas_filter');
    const siswaSelect = document.getElementById('id_siswa');
    
    kelasSelect.addEventListener('change', function() {
        const idKelas = this.value;
        siswaSelect.innerHTML = '<option value="">Memuat siswa...</option>';

        if (!idKelas) {
            siswaSelect.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
            return;
        }

        // Panggil API (Kita gunakan API 'sumatif' yang sudah ada, get_siswa_by_kelas)
        // Perlu API baru: 'get_siswa_by_kelas'
        // Untuk sekarang, kita buat API baru di SumatifApiController (perlu diganti nanti)
        
        // Kita perlu endpoint API baru untuk ini. Mari kita buat di 'api.php'
        fetch(`../api/api.php?mod=siswa&act=get_by_kelas&id_kelas=${idKelas}`)
            .then(response => response.json())
            .then(result => {
                siswaSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
                if (result.status === 'ok' && result.data.length > 0) {
                    result.data.forEach(siswa => {
                        const option = new Option(siswa.nama, siswa.id_siswa);
                        siswaSelect.appendChild(option);
                    });
                } else {
                    siswaSelect.innerHTML = '<option value="">-- Tidak ada siswa di kelas ini --</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching siswa:', error);
                siswaSelect.innerHTML = '<option value="">-- Gagal memuat siswa --</option>';
            });
    });
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>