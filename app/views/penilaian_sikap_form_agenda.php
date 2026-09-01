<?php include __DIR__.'/partials/header.php'; ?>

<div class="content-header pt-3 mb-2">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-sm-6 col-12 d-flex align-items-center">
        <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
          <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
            <?= $agenda ? 'Edit' : 'Buat' ?> Agenda Penilaian Sikap
          </h4>
        </div>
      </div>
      <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
        <a href="<?= BASE_URL ?>penilaian_sikap" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-weight-bold shadow-sm">
          <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8">
        <form action="<?= BASE_URL ?>penilaian_sikap/save_agenda" method="POST">
          <input type="hidden" name="id_agenda" value="<?= $agenda['id_agenda'] ?? '' ?>">
          
          <div class="card card-outline card-primary shadow">
            <div class="card-header">
              <h3 class="card-title font-weight-bold">Konfigurasi Penilaian</h3>
            </div>
            <div class="card-body">
              <?php if(is_admin()): ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="text-primary">Penilai (Guru)</label>
                    <select name="id_guru" class="form-control select2" required>
                      <option value="">-- Pilih Guru --</option>
                      <?php foreach($list_guru as $g): ?>
                        <option value="<?= $g['id_guru'] ?>" <?= ($agenda && $agenda['id_guru'] == $g['id_guru']) ? 'selected' : '' ?>>
                          <?= $g['nama'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
              <?php endif; ?>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Pilih Kelas</label>
                    <select name="id_kelas" class="form-control select2" required>
                      <option value="">-- Pilih Kelas --</option>
                      <?php foreach($list_kelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= ($agenda && $agenda['id_kelas'] == $k['id_kelas']) ? 'selected' : '' ?>>
                          <?= $k['nama_kelas'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Periode Penilaian</label>
                    <input type="text" name="periode" class="form-control" placeholder="Contoh: Agustus 2025 atau Semester Ganjil" value="<?= $agenda['periode'] ?? '' ?>" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Kategori Penilai</label>
                    <select name="kategori_penilai" id="kategori_penilai" class="form-control" required>
                      <option value="Guru Mapel" <?= ($agenda && $agenda['kategori_penilai'] == 'Guru Mapel') ? 'selected' : '' ?>>Guru Mata Pelajaran</option>
                      <option value="Wali Kelas" <?= ($agenda && $agenda['kategori_penilai'] == 'Wali Kelas') ? 'selected' : '' ?>>Wali Kelas</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" id="section-mapel">
                  <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <select name="id_mapel" class="form-control select2">
                      <option value="">-- Pilih Mapel --</option>
                      <?php foreach($list_mapel as $m): ?>
                        <option value="<?= $m['id_mapel'] ?>" <?= ($agenda && $agenda['id_mapel'] == $m['id_mapel']) ? 'selected' : '' ?>>
                          <?= $m['nama_mapel'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div id="section-tambahan">
                <hr>
                <div class="custom-control custom-switch mb-2">
                  <input type="checkbox" class="custom-control-input" id="is_nilai_tambahan" name="is_nilai_tambahan" <?= ($agenda && $agenda['is_nilai_tambahan']) ? 'checked' : '' ?>>
                  <label class="custom-control-label" for="is_nilai_tambahan">Jadikan sebagai Nilai Tambahan untuk Rapor Utama</label>
                </div>
                <div class="form-group mt-2" id="input-bobot" style="display: <?= ($agenda && $agenda['is_nilai_tambahan']) ? 'block' : 'none' ?>;">
                  <label>Bobot Nilai Tambahan (%)</label>
                  <div class="input-group" style="max-width: 150px;">
                    <input type="number" name="bobot_tambahan" class="form-control" value="<?= $agenda['bobot_tambahan'] ?? '5' ?>" min="0" max="100">
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <small class="text-muted">Nilai ini akan dikonversi ke skala 1-100 dan diberikan bobot sesuai inputan.</small>
                </div>
              </div>

              <hr>
              <label>Pilih Komponen yang Akan Dinilai</label>
              <p class="text-muted small">Centang komponen yang relevan untuk agenda penilaian ini.</p>
              
              <div class="row">
                <div class="col-md-4">
                  <h6 class="font-weight-bold text-info border-bottom pb-1">Kategori: Sikap (Afektif)</h6>
                  <?php foreach($komponen_master as $km): if($km['kategori'] == 'Sikap'): ?>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" name="komponen_ids[]" id="k-<?= $km['id_komponen'] ?>" value="<?= $km['id_komponen'] ?>" <?= in_array($km['id_komponen'], $selected_komponen_ids) ? 'checked' : '' ?>>
                    <label for="k-<?= $km['id_komponen'] ?>" class="custom-control-label font-weight-normal">
                      <?= $km['nama_komponen'] ?>
                      <i class="fas fa-info-circle text-xs text-muted ml-1" title="<?= htmlspecialchars($km['deskripsi']) ?>"></i>
                    </label>
                  </div>
                  <?php endif; endforeach; ?>
                </div>
                <div class="col-md-4">
                  <h6 class="font-weight-bold text-success border-bottom pb-1">Kategori: Keaktifan Belajar</h6>
                  <?php foreach($komponen_master as $km): if($km['kategori'] == 'Keaktifan Belajar'): ?>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" name="komponen_ids[]" id="k-<?= $km['id_komponen'] ?>" value="<?= $km['id_komponen'] ?>" <?= in_array($km['id_komponen'], $selected_komponen_ids) ? 'checked' : '' ?>>
                    <label for="k-<?= $km['id_komponen'] ?>" class="custom-control-label font-weight-normal">
                      <?= $km['nama_komponen'] ?>
                      <i class="fas fa-info-circle text-xs text-muted ml-1" title="<?= htmlspecialchars($km['deskripsi']) ?>"></i>
                    </label>
                  </div>
                  <?php endif; endforeach; ?>
                </div>
                <div class="col-md-4">
                  <h6 class="font-weight-bold text-primary border-bottom pb-1">Kategori: Profil Lulusan</h6>
                  <?php foreach($komponen_master as $km): if($km['kategori'] == 'Profil Lulusan'): ?>
                  <div class="custom-control custom-checkbox mb-2">
                    <input class="custom-control-input" type="checkbox" name="komponen_ids[]" id="k-<?= $km['id_komponen'] ?>" value="<?= $km['id_komponen'] ?>" <?= in_array($km['id_komponen'], $selected_komponen_ids) ? 'checked' : '' ?>>
                    <label for="k-<?= $km['id_komponen'] ?>" class="custom-control-label font-weight-normal">
                      <?= $km['nama_komponen'] ?>
                      <i class="fas fa-info-circle text-xs text-muted ml-1" title="<?= htmlspecialchars($km['deskripsi']) ?>"></i>
                    </label>
                  </div>
                  <?php endif; endforeach; ?>
                </div>
              </div>

            </div>
            <div class="card-footer text-right">
              <a href="<?= BASE_URL ?>penilaian_sikap" class="btn btn-default float-left">Kembali</a>
              <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save mr-1"></i> Simpan Agenda
              </button>
            </div>
          </div>
        </form>
      </div>
      
      <div class="col-md-4">
        <div class="card card-info card-outline shadow-sm">
          <div class="card-header border-0">
            <h3 class="card-title font-weight-bold"><i class="fas fa-lightbulb mr-1 text-warning"></i> Tips Penilaian</h3>
          </div>
          <div class="card-body text-sm">
            <ul>
              <li><strong>Guru Mapel</strong> biasanya menilai aspek partisipasi siswa selama KBM (keaktifan, kesiapan, dll).</li>
              <li><strong>Wali Kelas</strong> biasanya menilai aspek sikap sosial dan spiritual secara umum.</li>
              <li>Pilih komponen yang benar-benar ingin dipantau pada periode tersebut.</li>
              <li>Jika "Nilai Tambahan" diaktifkan, rata-rata predikat siswa akan dikonversi ke angka (1-100) untuk membantu perhitungan nilai rapor.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
$(function(){
  $('.select2').select2({ theme: 'bootstrap4' });

  function toggleFields() {
    const penilai = $('#kategori_penilai').val();
    if (penilai === 'Wali Kelas') {
      $('#section-mapel').hide();
      $('#section-tambahan').hide();
    } else {
      $('#section-mapel').show();
      $('#section-tambahan').show();
    }
  }

  $('#kategori_penilai').on('change', toggleFields);
  toggleFields();

  $('#is_nilai_tambahan').on('change', function(){
    if ($(this).is(':checked')) {
      $('#input-bobot').slideDown();
    } else {
      $('#input-bobot').slideUp();
    }
  });
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>
