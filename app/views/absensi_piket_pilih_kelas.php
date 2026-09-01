<?php
require_once __DIR__ . '/../models/AbsensiPiketModel.php';
include __DIR__.'/partials/header.php';
$tanggal_cek = date('Y-m-d');
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6"><h1><i class="fas fa-user-clock mr-2"></i> Absensi Siswa Harian (Piket)</h1></div>
      <div class="col-sm-6 text-right">
        <a href="<?= BASE_URL ?>absensi_scan" class="btn btn-success font-weight-bold rounded-pill px-3 shadow-sm">
          <i class="fas fa-qrcode mr-1"></i> Scan QR / Barcode Absensi
        </a>
      </div>
    </div>
  </div>
</section>
<section class="content">
<div class="container-fluid">
<div class="card card-primary">
    <div class="card-header"><h3 class="card-title">Pilih Kelas dan Tanggal</h3></div>
    <form action="index.php" method="GET">
        <input type="hidden" name="mod" value="absensi_piket">
        <input type="hidden" name="act" value="form">
        <div class="card-body">
            <div class="form-group">
                <label>Kelas</label>
                <div class="row">
                    <?php foreach($kelas_list as $k):
                        $sudah = AbsensiPiketModel::sudahDiisi($pdo, $k['id_kelas'], $tanggal_cek);
                    ?>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="card border <?= $sudah ? 'border-success' : 'border-secondary' ?> h-100"
                             style="cursor:pointer; transition: all 0.2s;"
                             onclick="pilihKelas('<?= $k['id_kelas'] ?>')">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold"><?= htmlspecialchars($k['nama_kelas']) ?></div>
                                    <small class="text-muted">Tingkat <?= $k['tingkat'] ?></small>
                                </div>
                                <?php if ($sudah): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Sudah</span>
                                <?php else: ?>
                                <span class="badge badge-secondary"><i class="fas fa-clock"></i> Belum</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="id_kelas" id="id_kelas_selected" required>
                <div id="kelas_info" class="mt-2 text-muted small"></div>
            </div>
            <div class="form-group mt-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" id="tanggal_input" class="form-control" value="<?= $tanggal_cek ?>" required>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" id="btn_lanjut" class="btn btn-primary" disabled>
                <i class="fas fa-arrow-right mr-1"></i> Lanjutkan ke Formulir Absensi
            </button>
        </div>
    </form>
</div>
</div>
</section>

<script>
function pilihKelas(idKelas) {
    document.getElementById('id_kelas_selected').value = idKelas;
    document.getElementById('btn_lanjut').disabled = false;
    // Highlight selected card
    document.querySelectorAll('[onclick^="pilihKelas"]').forEach(el => {
        el.classList.remove('border-primary', 'shadow');
    });
    const selected = document.querySelector(`[onclick="pilihKelas('${idKelas}')"]`);
    if (selected) {
        selected.classList.add('border-primary', 'shadow');
        const nama = selected.querySelector('.font-weight-bold').innerText;
        document.getElementById('kelas_info').innerHTML = `<i class="fas fa-check-circle text-primary"></i> Kelas dipilih: <strong>${nama}</strong>`;
    }
}
</script>

<?php include __DIR__.'/partials/footer.php'; ?>