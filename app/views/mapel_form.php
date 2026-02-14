<?php
// Daftar mata pelajaran Kurikulum Merdeka (Fase E & F / Kelas X, XI, XII)
$kurikulum_merdeka_mapel = [
  // (Daftar mapel Anda tetap sama...)
  "Pendidikan Agama Islam dan Budi Pekerti",
  "Pendidikan Pancasila",
  "Bahasa Indonesia",
  "Matematika",
  "Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)",
  "Sejarah",
  "Seni Budaya dan Prakarya",
  "Bahasa Inggris",
  "Seni Budaya",
  "Prakarya dan Kewirausahaan",
  "Ilmu Pengetahuan Alam",
  "Biologi",
  "Fisika",
  "Kimia",
  "Informatika",
  "Matematika Tingkat Lanjut",
  "Ilmu Pengetahuan Sosial",
  "Sosiologi",
  "Ekonomi",
  "Geografi",
  "Sejarah Tingkat Lanjut",
  "Bahasa Indonesia Tingkat Lanjut",
  "Bahasa Inggris Tingkat Lanjut",
  "Bahasa Korea",
  "Bahasa Arab",
  "Bahasa Mandarin",
  "Bahasa Jepang",
  "Bahasa Jerman",
  "Bahasa Prancis",
  "Bahasa Sunda",
  "Tahfidz Al Quran",
];
sort($kurikulum_merdeka_mapel);
?>

<?php include __DIR__ . '/partials/header.php'; ?>

<section class="content-header">
  <div class="container-fluid">
    <h1><i class="fas fa-book mr-2"></i> <?= $mapel ? 'Edit' : 'Tambah' ?> Data Mata Pelajaran</h1>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="card card-primary">
      <form action="index.php?mod=mapel&act=save" method="POST">
        <?php if ($mapel): ?>
          <input type="hidden" name="id_mapel" value="<?= $mapel['id_mapel'] ?>">
        <?php endif; ?>

        <div class="card-body">
          <div class="form-group">
            <label>Nama Mata Pelajaran</label>
            <select name="nama_mapel" class="form-control" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              <?php foreach ($kurikulum_merdeka_mapel as $nama_mapel): ?>
                <option value="<?= $nama_mapel ?>" <?= ($mapel['nama_mapel'] ?? '') == $nama_mapel ? 'selected' : '' ?>>
                  <?= $nama_mapel ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Mapel (Untuk Jadwal)</label>
                <input type="text" name="kode_mapel" value="<?= $mapel['kode_mapel'] ?? '' ?>" class="form-control"
                  placeholder="Contoh: A, B, atau PAI">
                <small class="form-text text-muted">Kode unik (huruf) untuk cetak jadwal keseluruhan.</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Urutan Tampil (di Laporan)</label>
                <input type="number" name="urutan" value="<?= $mapel['urutan'] ?? '0' ?>" class="form-control" required>
                <small class="form-text text-muted">Contoh: 1 (untuk PAI), 2 (untuk PPKn), dst.</small>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_mapel" class="form-control" required>
              <option value="">-- Pilih Kategori --</option>
              <option value="Mata Pelajaran Wajib" <?= ($mapel['kategori_mapel'] ?? '') == 'Mata Pelajaran Wajib' ? 'selected' : '' ?>>Mata Pelajaran Wajib</option>
              <option value="Mata Pelajaran Pilihan" <?= ($mapel['kategori_mapel'] ?? '') == 'Mata Pelajaran Pilihan' ? 'selected' : '' ?>>Mata Pelajaran Pilihan</option>
              <option value="Muatan Lokal" <?= ($mapel['kategori_mapel'] ?? '') == 'Muatan Lokal' ? 'selected' : '' ?>>
                Muatan Lokal</option>
              <option value="Mulok Yayasan" <?= ($mapel['kategori_mapel'] ?? '') == 'Mulok Yayasan' ? 'selected' : '' ?>>
                Mulok Yayasan</option>
            </select>
          </div>

          <div class="form-group">
            <label>KKTP (Kriteria Ketercapaian Tujuan Pembelajaran)</label>
            <input type="number" name="kktp" value="<?= $mapel['kktp'] ?? '' ?>" class="form-control" min="1" max="100"
              required>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <a href="index.php?mod=mapel" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>