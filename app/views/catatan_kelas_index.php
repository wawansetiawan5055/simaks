<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid"><h1>Catatan Kejadian Kelas</h1></div>
</section>
<section class="content">
<div class="container-fluid">
    <?php // Session messages now handled by toast notifications in footer.php ?>
<div class="card card-warning">
      <div class="card-header"><h3 class="card-title">Input Catatan Kejadian Hari Ini</h3></div>
      <form action="index.php?mod=catatan_kelas&act=save" method="POST">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
  <label>Kelas</label>
  <select name="id_kelas" id="id_kelas" class="form-control" required>
    <option value="">-- Pilih Kelas --</option>
    <?php if (!empty($kelas_diajar)): ?>
      <?php foreach($kelas_diajar as $kelas): ?>
        <option value="<?= $kelas['id_kelas'] ?>">
            <?= htmlspecialchars($kelas['nama_kelas']) ?>
        </option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
</div>

              <div class="form-group">
                  <label>Tanggal</label>
                  <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                  <label>Jam Mengajar (Pilih satu atau lebih)</label>
                  <div id="jam_mengajar_container">
                      <p class="text-muted">-- Pilih Kelas dan Tanggal Dahulu --</p>
                  </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Catatan Kejadian di Kelas</label>
            <textarea name="catatan_kejadian" class="form-control" rows="4" placeholder="Contoh: Lampu proyektor mati, AC bocor, siswa kelas XI MIPA 1 menemukan dompet di laci, dll." required></textarea>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Simpan Catatan</button>
        </div>
      </form>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Catatan Kejadian Kelas (TA Aktif)</h3>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">No</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Guru</th>
                        <th>Catatan Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat_catatan)): ?>
                        <tr><td colspan="6" class="text-center">Belum ada riwayat catatan.</td></tr>
                    <?php endif; ?>
                    
                    <?php $no = 1; foreach ($riwayat_catatan as $catatan): ?>
                    <tr>
                        <td><?= $no++; ?>.</td>
                        <td><?= htmlspecialchars($catatan['tanggal']); ?></td>
                        <td><?= htmlspecialchars($catatan['nama_kelas']); ?></td>
                        <td><?= htmlspecialchars($catatan['nama_mapel']); ?></td>
                        <td><?= htmlspecialchars($catatan['nama_guru']); ?></td>
                        <td><?= htmlspecialchars($catatan['catatan_kejadian']); ?></td>
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
    const kelasSelect = document.getElementById('id_kelas');
    const tanggalInput = document.getElementById('tanggal');
    const jamContainer = document.getElementById('jam_mengajar_container');

    function fetchData() {
        const idKelas = kelasSelect.value;
        const tanggal = tanggalInput.value;

        if (!idKelas || !tanggal) {
            jamContainer.innerHTML = '<p class="text-muted">-- Pilih Kelas dan Tanggal Dahulu --</p>';
            return;
        }
        
        jamContainer.innerHTML = '<p class="text-muted">Memuat jadwal...</p>';
        
        fetch(`../api/api.php?mod=jadwal&act=get_by_kelas_dan_tanggal&id_kelas=${idKelas}&tanggal=${tanggal}`)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'ok' && Array.isArray(result.data) && result.data.length > 0) {
                    jamContainer.innerHTML = ''; // Kosongkan
                    result.data.forEach(item => {
                        const jamMulai = item.jam_mulai.substring(0, 5);
                        const jamSelesai = item.jam_selesai.substring(0, 5);
                        const label = `${jamMulai} - ${jamSelesai} | ${item.nama_mapel} (${item.nama})`;
                        
                        const checkboxDiv = document.createElement('div');
                        checkboxDiv.className = 'custom-control custom-checkbox';
                        checkboxDiv.innerHTML = `
                            <input type="checkbox" class="custom-control-input" 
                                   id="jam_${item.id_jadwal_mengajar}" 
                                   name="jam_mengajar[]" 
                                   value="${item.id_jadwal_mengajar}">
                            <label class="custom-control-label" for="jam_${item.id_jadwal_mengajar}">
                                ${label}
                            </label>
                        `;
                        jamContainer.appendChild(checkboxDiv);
                    });
                } else {
                    jamContainer.innerHTML = '<p class="text-muted">Tidak ada jadwal mengajar untuk kelas dan tanggal ini.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching jadwal:', error);
                jamContainer.innerHTML = '<p class="text-danger">Gagal memuat jadwal. Silakan coba lagi.</p>';
            });
    }
    
    kelasSelect.addEventListener('change', fetchData);
    tanggalInput.addEventListener('change', fetchData);
});
</script>
<?php include __DIR__.'/partials/footer.php'; ?>