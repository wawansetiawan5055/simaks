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
  "Sejarah Indonesia",
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
  "Al Quran Hadits",
];
sort($kurikulum_merdeka_mapel);
?>

<?php include __DIR__ . '/partials/header.php'; ?>

<style>
  .mapel-form-card {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06) !important;
    overflow: hidden;
  }
  .mapel-form-card .card-header {
    background: #ffffff;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
  }
  .mapel-form-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0284c7, #0369a1);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);
  }

  @media (max-width: 768px) {
    .mapel-form-icon {
      width: 36px !important;
      height: 36px !important;
      font-size: 1.05rem !important;
      border-radius: 8px !important;
    }
    .content-header h4 {
      font-size: 0.95rem !important;
    }
    .mapel-form-card .card-body {
      padding: 1rem !important;
    }
    .mapel-form-card .form-control {
      font-size: 0.82rem !important;
      height: 38px !important;
    }
    .mapel-form-card label {
      font-size: 0.76rem !important;
    }
    .mapel-form-card .btn {
      font-size: 0.78rem !important;
      padding: 0.45rem 1rem !important;
      width: 100% !important;
      margin-bottom: 6px !important;
    }
    .mapel-form-card .card-footer {
      display: flex !important;
      flex-direction: column !important;
      padding: 0.85rem 1rem !important;
    }
  }
</style>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="d-flex align-items-center">
      <div class="mr-3 mapel-form-icon">
        <i class="fas fa-book"></i>
      </div>
      <div>
        <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
          <?= $mapel ? 'Edit' : 'Tambah' ?> Data Mata Pelajaran
        </h4>
        <small class="text-muted">Isi formulir data mata pelajaran dengan lengkap</small>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card mapel-form-card">
      <form action="<?= BASE_URL ?>mapel/save" method="POST">
        <?php if ($mapel): ?>
          <input type="hidden" name="id_mapel" value="<?= $mapel['id_mapel'] ?>">
        <?php endif; ?>

        <div class="card-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark small"><i class="fas fa-book-open mr-1 text-primary"></i> Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <select name="nama_mapel" class="form-control" style="border-radius: 8px;" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              <?php foreach ($kurikulum_merdeka_mapel as $nama_mapel): ?>
                <option value="<?= $nama_mapel ?>" <?= ($mapel['nama_mapel'] ?? '') == $nama_mapel ? 'selected' : '' ?>>
                  <?= $nama_mapel ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 col-12">
              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small"><i class="fas fa-tag mr-1 text-info"></i> Kode Mapel (Untuk Jadwal)</label>
                <input type="text" name="kode_mapel" value="<?= htmlspecialchars($mapel['kode_mapel'] ?? '') ?>" class="form-control"
                  placeholder="Contoh: A, B, atau PAI" style="border-radius: 8px;">
                <small class="form-text text-muted">Kode unik (huruf/singkatan) untuk cetak jadwal.</small>
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small"><i class="fas fa-sort-numeric-down mr-1 text-warning"></i> Urutan Tampil di Laporan <span class="text-danger">*</span></label>
                <input type="number" name="urutan" value="<?= htmlspecialchars($mapel['urutan'] ?? '0') ?>" class="form-control" style="border-radius: 8px;" required>
                <small class="form-text text-muted">Nomor urut tampilan di rapor/cetakan (misal: 1, 2, dst).</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 col-12">
              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small"><i class="fas fa-layer-group mr-1 text-secondary"></i> Kategori Mapel <span class="text-danger">*</span></label>
                <select name="kategori_mapel" class="form-control" style="border-radius: 8px;" required>
                  <option value="">-- Pilih Kategori --</option>
                  <option value="Mata Pelajaran Wajib" <?= ($mapel['kategori_mapel'] ?? '') == 'Mata Pelajaran Wajib' ? 'selected' : '' ?>>Mata Pelajaran Wajib</option>
                  <option value="Mata Pelajaran Pilihan" <?= ($mapel['kategori_mapel'] ?? '') == 'Mata Pelajaran Pilihan' ? 'selected' : '' ?>>Mata Pelajaran Pilihan</option>
                  <option value="Muatan Lokal" <?= ($mapel['kategori_mapel'] ?? '') == 'Muatan Lokal' ? 'selected' : '' ?>>Muatan Lokal</option>
                  <option value="Mulok Yayasan" <?= ($mapel['kategori_mapel'] ?? '') == 'Mulok Yayasan' ? 'selected' : '' ?>>Mulok Yayasan</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 col-12">
              <div class="form-group mb-3">
                <label class="font-weight-bold text-dark small"><i class="fas fa-bullseye mr-1 text-danger"></i> KKTP (Kriteria Ketercapaian Tujuan Pembelajaran) <span class="text-danger">*</span></label>
                <input type="number" name="kktp" value="<?= htmlspecialchars($mapel['kktp'] ?? '75') ?>" class="form-control" min="1" max="100" style="border-radius: 8px;" required>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer bg-light d-flex align-items-center justify-content-end" style="gap: 8px;">
          <a href="<?= BASE_URL ?>mapel" class="btn btn-secondary px-3" style="border-radius: 8px;">Batal</a>
          <button type="submit" class="btn btn-success px-4 font-weight-bold" style="border-radius: 8px;">
            <i class="fas fa-save mr-1"></i> Simpan Data
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>