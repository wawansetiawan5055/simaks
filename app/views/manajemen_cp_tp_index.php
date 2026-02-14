<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid"><h1>Manajemen Capaian & Tujuan Pembelajaran (CP & TP)</h1></div>
</section>
<section class="content">
<div class="container-fluid">
    <?php // Session messages now handled by toast notifications in footer.php ?>
    <?php // Session messages now handled by toast notifications in footer.php ?>
<div class="card card-info">
        <div class="card-header"><h3 class="card-title">Filter Data</h3></div>
        <form method="GET">
            <input type="hidden" name="mod" value="manajemen_cp_tp">
            <div class="card-body row">
                <div class="form-group col-md-6"><label>Mata Pelajaran</label><select name="id_mapel" class="form-control" onchange="this.form.submit()"><?php foreach($mapel_list as $m): ?><option value="<?= $m['id_mapel'] ?>" <?= ($id_mapel_filter == $m['id_mapel']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option><?php endforeach; ?></select></div>
                <div class="form-group col-md-6"><label>Fase</label><select name="fase" class="form-control" onchange="this.form.submit()"><option value="E" <?= ($fase_filter == 'E') ? 'selected' : '' ?>>E (Kelas X)</option><option value="F" <?= ($fase_filter == 'F') ? 'selected' : '' ?>>F (Kelas XI & XII)</option></select></div>
            </div>
        </form>
    </div>

    <?php if ($id_mapel_filter): ?>
    <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Tambah Capaian Pembelajaran (CP) Baru</h3></div>
        <form action="index.php?mod=manajemen_cp_tp&act=cp_save" method="POST">
            <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
            <input type="hidden" name="fase" value="<?= $fase_filter ?>">
            <div class="card-body">
                <div class="form-group"><label>Deskripsi Capaian Pembelajaran</label><textarea name="deskripsi_cp" class="form-control" rows="3" required></textarea></div>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan CP</button></div>
        </form>
    </div>
    
    <div class="mb-3">
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalImporCp"><i class="fa fa-upload"></i> Impor CP & TP dari Excel</button>
    </div>
    
    <hr>

    <?php if (empty($cp_list)): ?>
        <div class="alert alert-warning">Belum ada data CP untuk Mata Pelajaran dan Fase yang dipilih. Silakan tambahkan CP baru di atas.</div>
    <?php else: ?>
        <h4 class="mb-3">Daftar CP dan TP yang Sudah Ada:</h4>
        <?php foreach($cp_list as $cp): ?>
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header">
                <h3 class="card-title"><strong>CP:</strong> <?= nl2br(htmlspecialchars($cp['deskripsi_cp'])) ?></h3>
                <div class="card-tools"><button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button><a href="index.php?mod=manajemen_cp_tp&act=cp_delete&id=<?= $cp['id_cp'] ?>" class="btn btn-tool" onclick="return confirmDelete(event)"><i class="fas fa-times text-danger"></i></a></div>
            </div>
            <div class="card-body">
                <h5>Tujuan Pembelajaran (TP) Terkait:</h5>
                <?php if (!empty($tp_data[$cp['id_cp']])): ?>
                    <table class="table table-sm table-striped"><thead><tr><th>Kode</th><th>Deskripsi TP</th><th width="50px">Aksi</th></tr></thead><tbody>
                    <?php foreach($tp_data[$cp['id_cp']] as $tp): ?><tr><td><?= htmlspecialchars($tp['kode_tp']) ?></td><td><?= nl2br(htmlspecialchars($tp['deskripsi_tp'])) ?></td><td><a href="index.php?mod=manajemen_cp_tp&act=tp_delete&id=<?= $tp['id_tp'] ?>" class="btn btn-xs btn-danger" onclick="return confirmDelete(event)"><i class="fa fa-trash"></i></a></td></tr><?php endforeach; ?>
                    </tbody></table>
                <?php else: ?>
                    <p class="text-muted font-italic">Belum ada TP untuk CP ini.</p>
                <?php endif; ?>
                <hr>
                <h6>Tambah TP Baru untuk CP Ini:</h6>
                <form action="index.php?mod=manajemen_cp_tp&act=tp_save" method="POST" class="form-inline mb-3">
                    <input type="hidden" name="id_cp" value="<?= $cp['id_cp'] ?>"><input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>"><input type="hidden" name="fase" value="<?= $fase_filter ?>">
                    <div class="form-group mr-2"><input type="text" name="kode_tp" class="form-control form-control-sm" placeholder="Kode TP" required></div>
                    <div class="form-group flex-grow-1 mr-2"><textarea name="deskripsi_tp" class="form-control form-control-sm" placeholder="Deskripsi TP" rows="1" required></textarea></div>
                    <button type="submit" class="btn btn-sm btn-success">Tambah TP</button>
                </form>
                <h6>Impor TP dari Excel untuk CP Ini:</h6>
                <form action="index.php?mod=manajemen_cp_tp&act=tp_import" method="POST" enctype="multipart/form-data" class="form-inline">
                    <input type="hidden" name="id_cp" value="<?= $cp['id_cp'] ?>"><input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>"><input type="hidden" name="fase" value="<?= $fase_filter ?>">
                    <div class="form-group mr-2"><input type="file" name="file_excel" class="form-control-file" accept=".xls,.xlsx" required></div>
                    <button type="submit" class="btn btn-sm btn-secondary">Impor TP</button>
                </form>
                <small class="form-text text-muted">Format: Kolom A=Kode TP, Kolom B=Deskripsi TP.</small>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php endif; ?>
</div>
</section>

<div class="modal fade" id="modalImporCp">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Impor Capaian Pembelajaran (CP) dan Tujuan Pembelajaran (TP)</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="index.php?mod=manajemen_cp_tp&act=cp_import" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_mapel" value="<?= $id_mapel_filter ?>">
        <input type="hidden" name="fase" value="<?= $fase_filter ?>">
        <div class="modal-body">
          <div class="form-group">
            <label>File Excel (.xls atau .xlsx)</label>
            <input type="file" name="file_excel" class="form-control-file" accept=".xls,.xlsx" required>
          </div>
          
          <div class="alert alert-info">
            <strong><i class="fa fa-info-circle"></i> Format Excel:</strong>
            <table class="table table-sm table-bordered mt-2 bg-white">
              <thead class="bg-light">
                <tr>
                  <th>DESKRIPSI CP</th>
                  <th>KODE TP</th>
                  <th>DESKRIPSI TP</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Deskripsi CP 1</td>
                  <td>E.1.1</td>
                  <td>Deskripsi TP 1.1</td>
                </tr>
                <tr>
                  <td></td>
                  <td>E.1.2</td>
                  <td>Deskripsi TP 1.2</td>
                </tr>
                <tr>
                  <td>Deskripsi CP 2</td>
                  <td>E.2.1</td>
                  <td>Deskripsi TP 2.1</td>
                </tr>
              </tbody>
            </table>
            <small>
              <strong>Cara Pengisian:</strong><br>
              • <strong>Kolom A (DESKRIPSI CP):</strong> Isi dengan deskripsi CP. Jika kosong, baris tersebut dianggap TP untuk CP di atasnya.<br>
              • <strong>Kolom B (KODE TP):</strong> Isi dengan kode TP (contoh: E.1.1, E.2.1)<br>
              • <strong>Kolom C (DESKRIPSI TP):</strong> Isi dengan deskripsi TP<br>
              • Satu CP bisa memiliki banyak TP (baris berikutnya dengan Kolom A kosong)<br>
              • Baris header akan otomatis diskip<br>
              • Baris kosong akan otomatis diskip
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Impor CP & TP</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__.'/partials/footer.php'; ?>