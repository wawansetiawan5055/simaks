<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header"><div class="container-fluid"><div class="row mb-2">
  <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-plus text-danger mr-2"></i><?= $title ?></h1></div>
  <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="index.php?mod=cbt_bank_soal">Bank Soal</a></li><li class="breadcrumb-item active"><?= $title ?></li></ol></div>
</div></div></div>
<section class="content"><div class="container-fluid">
  <div class="card card-outline card-danger shadow-sm">
    <div class="card-header"><h3 class="card-title">Informasi Bank Soal</h3></div>
    <form method="POST" action="index.php?mod=cbt_bank_soal&act=store_bank">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nama Bank Soal <span class="text-danger">*</span></label>
          <div class="col-sm-6"><input type="text" name="nama_bank" class="form-control" placeholder="cth: Bank Soal Matematika Wajib X" required></div>
          <label class="col-sm-1 col-form-label">Kode</label>
          <div class="col-sm-3"><input type="text" name="kode_bank" class="form-control" placeholder="cth: MTK-X-01"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Mata Pelajaran <span class="text-danger">*</span></label>
          <div class="col-sm-6">
            <select name="id_mapel" class="form-control" required>
              <option value="">-- Pilih Mapel --</option>
              <?php foreach ($mapel_list as $m): ?>
              <option value="<?= $m['id_mapel'] ?>"><?= htmlspecialchars($m['nama_mapel']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <label class="col-sm-1 col-form-label">Tingkat</label>
          <div class="col-sm-3">
            <select name="tingkat" class="form-control">
              <option value="Semua">Semua Tingkat</option>
              <option value="X">Kelas X</option>
              <option value="XI">Kelas XI</option>
              <option value="XII">Kelas XII</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Opsi PG</label>
          <div class="col-sm-2">
            <select name="opsi_pg" class="form-control">
              <option value="5">5 Opsi (A, B, C, D, E)</option>
              <option value="4">4 Opsi (A, B, C, D)</option>
            </select>
          </div>
          <label class="col-sm-2 col-form-label">Target Jml PG</label>
          <div class="col-sm-2"><input type="number" name="jml_pg" class="form-control" value="20"></div>
          <label class="col-sm-2 col-form-label">Bobot PG (%)</label>
          <div class="col-sm-2"><input type="number" name="bobot_pg" class="form-control" value="100"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Deskripsi</label>
          <div class="col-sm-10"><textarea name="deskripsi" class="form-control" rows="2"></textarea></div>
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-danger"><i class="fas fa-save mr-1"></i> Simpan Bank Soal</button>
        <a href="index.php?mod=cbt_bank_soal" class="btn btn-secondary ml-2">Batal</a>
      </div>
    </form>
  </div>
</div></section>
<?php include __DIR__ . '/partials/footer.php'; ?>
