<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <h1><i class="fas fa-user-plus mr-2"></i> <?= $guru ? 'Edit' : 'Tambah' ?> Data Guru</h1>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Formulir Data Guru</h3>
      </div>
      <form action="index.php?mod=guru&act=save" method="POST">
        <?php if ($guru): ?>
          <input type="hidden" name="id_guru" value="<?= $guru['id_guru'] ?>">
        <?php endif; ?>

        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $guru['nama'] ?? '' ?>" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Kode Guru (Untuk Jadwal)</label>
                <input type="text" name="kode_guru" value="<?= $guru['kode_guru'] ?? '' ?>" class="form-control"
                  placeholder="Contoh: 1, 2, atau A, B">
                <small class="form-text text-muted">Kode unik (angka/huruf) untuk cetak jadwal keseluruhan.</small>
              </div>

              <div class="form-group">
                <label>NUPTK</label>
                <input type="text" name="nuptk" value="<?= $guru['nuptk'] ?? '' ?>" class="form-control">
              </div>
              <div class="form-group">
                <label>NIK</label>
                <input type="text" name="nik" value="<?= $guru['nik'] ?? '' ?>" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jk" class="form-control" required>
                  <option value="">-- Pilih --</option>
                  <option value="Laki-laki" <?= ($guru['jk'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                  <option value="Perempuan" <?= ($guru['jk'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
              </div>
              <div class="form-group">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="<?= $guru['tempat_lahir'] ?? '' ?>" class="form-control">
              </div>
              <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?= $guru['tanggal_lahir'] ?? '' ?>"
                  class="form-control">
              </div>
              <div class="form-group">
                <label>Status Kepegawaian</label>
                <input type="text" name="status_kepegawaian" value="<?= $guru['status_kepegawaian'] ?? '' ?>"
                  class="form-control">
              </div>
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="Aktif" <?= ($guru['status'] ?? 'Aktif') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                  <option value="Nonaktif" <?= ($guru['status'] ?? '') == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                  <option value="Pensiun" <?= ($guru['status'] ?? '') == 'Pensiun' ? 'selected' : '' ?>>Pensiun</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <a href="index.php?mod=guru" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>