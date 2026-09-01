<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <h1><i class="fas fa-user-graduate mr-2"></i> <?= $siswa ? 'Edit' : 'Tambah' ?> Data Siswa</h1>
    <form action="<?= BASE_URL ?>siswa/save" method="POST" class="mt-3">
      <?php if ($siswa): ?>
        <input type="hidden" name="id_siswa" value="<?= $siswa['id_siswa'] ?>">
      <?php endif; ?>
      <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= $siswa['nama'] ?? '' ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>NISN</label>
        <input type="text" name="nisn" value="<?= $siswa['nisn'] ?? '' ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>NIPD</label>
        <input type="text" name="nipd" value="<?= $siswa['nipd'] ?? '' ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>NIK</label>
        <input type="text" name="nik" value="<?= $siswa['nik'] ?? '' ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Jenis Kelamin</label>
        <select name="jk" class="form-control" required>
          <option value="">-- Pilih --</option>
          <option value="Laki-laki" <?= ($siswa['jk'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
          <option value="Perempuan" <?= ($siswa['jk'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" value="<?= $siswa['tempat_lahir'] ?? '' ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="<?= $siswa['tanggal_lahir'] ?? '' ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Sekolah Asal</label>
        <input type="text" name="sekolah_asal" value="<?= $siswa['sekolah_asal'] ?? '' ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Tahun Ajaran Masuk <span class="text-danger">*</span></label>
        <select name="id_ta_masuk" class="form-control" required>
          <option value="">-- Pilih TA Masuk --</option>
          <?php foreach ($ta_list as $ta): ?>
            <option value="<?= $ta['id_ta'] ?>" <?= (($siswa['id_ta_masuk'] ?? '') == $ta['id_ta']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($ta['nama_ta']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Tahun ajaran pertama kali siswa ini terdaftar di sekolah.</small>
      </div>

      <div class="mb-3">
        <label>Status Siswa</label>
        <input type="text" class="form-control" value="<?= $siswa['status_aktif'] ?? 'Aktif' ?>" disabled>

        <input type="hidden" name="status_aktif" value="<?= $siswa['status_aktif'] ?? 'Aktif' ?>">

        <small class="form-text text-muted">
          Untuk mengubah status siswa (Mutasi Keluar/Lulus), silakan gunakan modul "Portal PPDB dan Mutasi".
        </small>
      </div>
      <button type="submit" class="btn btn-success">Simpan</button>
      <a href="<?= BASE_URL ?>siswa" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>