<?php 
require_once __DIR__ . '/../helpers/DateHelper.php';
include __DIR__.'/partials/header.php'; 
?>
<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            Catatan Kasus Siswa (BK / Konseling)
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
          <li class="breadcrumb-item active text-primary font-weight-bold">Catatan Kasus</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<section class="content">
<div class="container-fluid">
    <?php // Session messages now handled by toast notifications in footer.php ?>
    <?php // Session messages now handled by toast notifications in footer.php ?>
<div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Input Catatan Kasus Baru</h3></div>
        <form action="<?= BASE_URL ?>catatan_kasus/save" method="POST">
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
                            <a href="<?= BASE_URL ?>catatan_kasus/delete?id=<?= $k['id_catatan'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)"><i class="fa fa-trash"></i></a>
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
    const idTa = <?= json_encode($_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? 0) ?>;
    
    kelasSelect.addEventListener('change', function() {
        const idKelas = this.value;
        siswaSelect.innerHTML = '<option value="">Memuat siswa...</option>';

        if (!idKelas) {
            siswaSelect.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
            return;
        }

        fetch(`<?= BASE_URL ?>api.php?mod=siswa&act=get_by_kelas&id_kelas=${idKelas}&id_ta=${idTa}`)
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