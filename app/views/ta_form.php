<?php
include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <h1><i class="fas fa-calendar-alt mr-2"></i>
      <?= isset($item['id_ta']) ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' ?></h1>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="card card-primary">
      <form action="index.php?mod=ta&act=save" method="POST">
        <?php if (!empty($item['id_ta'])): ?>
          <input type="hidden" name="id_ta" value="<?= htmlspecialchars($item['id_ta']) ?>">
        <?php endif; ?>
        <div class="card-body">
          <div class="form-group">
            <label>Nama Tahun Ajaran</label>
            <input type="text" name="nama_ta" class="form-control" placeholder="Contoh: 2024/2025 Ganjil"
              value="<?= htmlspecialchars($item['nama_ta'] ?? '') ?>" required>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control"
                  value="<?= htmlspecialchars($item['tanggal_mulai'] ?? '') ?>" required>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control"
                  value="<?= htmlspecialchars($item['tanggal_selesai'] ?? '') ?>" required>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="index.php?mod=ta&act=index" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>