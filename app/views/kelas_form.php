<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><?= $kelas ? 'Edit' : 'Tambah' ?> Data Kelas</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title"><?= $kelas ? 'Edit' : 'Input' ?> Kelas</h3>
      </div>

      <form action="<?= BASE_URL ?>kelas/save" method="POST">
        <div class="card-body">
          <?php if ($kelas): ?>
            <input type="hidden" name="id_kelas" value="<?= $kelas['id_kelas'] ?>">
          <?php else: ?>
            <!-- Hidden input id_ta untuk kelas baru (diambil dari session) -->
            <input type="hidden" name="id_ta" value="<?= $_SESSION['id_ta_viewing'] ?? $_SESSION['id_ta_aktif'] ?? '' ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="nama_kelas">Nama Kelas</label>
            <input type="text" name="nama_kelas" id="nama_kelas"
              value="<?= htmlspecialchars($kelas['nama_kelas'] ?? '') ?>" class="form-control"
              placeholder="Contoh: X IPA 1, VII A" required>
          </div>

          <div class="form-group">
            <label for="tingkat">Tingkat</label>
            <select name="tingkat" id="tingkat" class="form-control" required>
              <option value="">-- Pilih Tingkat --</option>
              <?php
              // Menampilkan daftar tingkat yang sudah disiapkan oleh Controller
              // Variabel $tingkat_list dikirim dari fungsi kelas_form() di Controller
              if (isset($tingkat_list) && is_array($tingkat_list)) {
                foreach ($tingkat_list as $tingkat):
                  $selected = (isset($kelas['tingkat']) && $kelas['tingkat'] == $tingkat) ? 'selected' : '';
                  ?>
                  <option value="<?= $tingkat ?>" <?= $selected ?>>
                    <?= $tingkat ?>
                  </option>
                <?php
                endforeach;
              }
              ?>
            </select>
          <div class="form-group">
            <label for="jenis_kelas">Jenis Program / Status Kelas</label>
            <select name="jenis_kelas" id="jenis_kelas" class="form-control" required>
              <option value="reguler" <?= (isset($kelas['jenis_kelas']) && $kelas['jenis_kelas'] == 'reguler') ? 'selected' : '' ?>>🏫 Reguler (Sekolah Induk Pusat - 5 Hari)</option>
              <option value="pjj" <?= (isset($kelas['jenis_kelas']) && $kelas['jenis_kelas'] == 'pjj') ? 'selected' : '' ?>>🌐 Pendidikan Jarak Jauh (PJJ / Terbuka - Hybrid)</option>
              <option value="menginduk" <?= (isset($kelas['jenis_kelas']) && $kelas['jenis_kelas'] == 'menginduk') ? 'selected' : '' ?>>🤝 Sekolah Menginduk (Mitra Filial - 6 Hari)</option>
            </select>
            <small class="form-text text-muted">Digunakan untuk klasifikasi otomatis pada jadwal pelajaran, presensi, dan koordinator lokasi.</small>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <a href="<?= BASE_URL ?>kelas" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>