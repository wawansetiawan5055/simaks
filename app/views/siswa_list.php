<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0">Data Siswa</h1>
    <a href="index.php?mod=siswa&act=form" class="btn btn-primary mb-2"><i class="fas fa-plus"></i> Tambah Siswa</a>
    <a href="index.php?mod=siswa&act=export" class="btn btn-success mb-2"><i class="fas fa-file-excel"></i> Export Excel</a>
    <form action="index.php?mod=siswa&act=import" method="POST" enctype="multipart/form-data" class="d-inline">
      <input type="file" name="file_excel" accept=".xlsx,.xls" required>
      <button type="submit" class="btn btn-secondary mb-2"><i class="fas fa-file-import"></i> Import Excel</button>
    </form>
    <table class="table table-bordered table-striped mt-3">
      <tr>
        <th>ID</th><th>Nama</th><th>NISN</th><th>NIPD</th><th>NIK</th><th>JK</th>
        <th>Tempat Lahir</th><th>Tanggal Lahir</th><th>Status</th><th>Aksi</th>
      </tr>
      <?php foreach($siswa_list as $s): ?>
      <tr>
        <td><?= $s['id_siswa'] ?></td>
        <td><?= $s['nama'] ?></td>
        <td><?= $s['nisn'] ?></td>
        <td><?= $s['nipd'] ?></td>
        <td><?= $s['nik'] ?></td>
        <td><?= $s['jk'] ?></td>
        <td><?= $s['tempat_lahir'] ?></td>
        <td><?= $s['tanggal_lahir'] ?></td>
        <td><?= $s['status_aktif'] ?></td>
        <td>
          <a href="index.php?mod=siswa&act=form&id=<?= $s['id_siswa'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
          <a href="index.php?mod=siswa&act=delete&id=<?= $s['id_siswa'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
<?php include __DIR__.'/partials/footer.php'; ?>