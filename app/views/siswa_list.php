<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">Data Siswa</h1>
    <a href="<?= BASE_URL ?>siswa/form" class="btn btn-primary mb-2"><i class="fas fa-plus"></i> Tambah Siswa</a>
    <a href="<?= BASE_URL ?>siswa/export" class="btn btn-success mb-2"><i class="fas fa-file-excel"></i> Export Excel</a>
    <form action="<?= BASE_URL ?>siswa/import" method="POST" enctype="multipart/form-data" class="d-inline">
      <input type="file" name="file_excel" accept=".xlsx,.xls" required>
      <button type="submit" class="btn btn-secondary mb-2"><i class="fas fa-file-import"></i> Import Excel</button>
    </form>
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>NISN</th>
            <th class="d-none d-sm-table-cell">NIPD</th>
            <th class="d-none d-md-table-cell">NIK</th>
            <th class="d-none d-md-table-cell">JK</th>
            <th class="d-none d-lg-table-cell">Tempat Lahir</th>
            <th class="d-none d-lg-table-cell">Tanggal Lahir</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($siswa_list as $s): ?>
          <tr>
            <td><?= $s['id_siswa'] ?></td>
            <td><?= $s['nama'] ?></td>
            <td><?= $s['nisn'] ?></td>
            <td class="d-none d-sm-table-cell"><?= $s['nipd'] ?></td>
            <td class="d-none d-md-table-cell"><?= $s['nik'] ?></td>
            <td class="d-none d-md-table-cell"><?= $s['jk'] ?></td>
            <td class="d-none d-lg-table-cell"><?= $s['tempat_lahir'] ?></td>
            <td class="d-none d-lg-table-cell"><?= $s['tanggal_lahir'] ?></td>
            <td><?= $s['status_aktif'] ?></td>
            <td>
              <a href="<?= BASE_URL ?>siswa/form?id=<?= $s['id_siswa'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
              <a href="<?= BASE_URL ?>siswa/delete?id=<?= $s['id_siswa'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div> 
  </div>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>