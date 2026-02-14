<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</h1>
    <a href="index.php?mod=guru&act=export" class="btn btn-success mb-2"><i class="fas fa-file-excel"></i> Export
      Excel</a>
    <form action="index.php?mod=guru&act=import" method="POST" enctype="multipart/form-data" class="d-inline">
      <input type="file" name="file_excel" accept=".xlsx,.xls" required>
      <button type="submit" class="btn btn-secondary mb-2"><i class="fas fa-file-import"></i> Import Excel</button>
    </form>
    <?php include __DIR__ . '/partials/header.php'; ?>
    <div class="content-header">
      <div class="container-fluid">
        <h1 class="m-0">Data Guru</h1>
        <a href="index.php?mod=guru&act=form" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah Guru</a>
        <table class="table table-bordered table-striped">
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>NUPTK</th>
            <th>NIK</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
          <?php foreach ($guru_list as $g): ?>
            <tr>
              <td><?= $g['id_guru'] ?></td>
              <td><?= $g['nama'] ?></td>
              <td><?= $g['nuptk'] ?></td>
              <td><?= $g['nik'] ?></td>
              <td><?= $g['status'] ?></td>
              <td>
                <a href="index.php?mod=guru&act=form&id=<?= $g['id_guru'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="index.php?mod=guru&act=delete&id=<?= $g['id_guru'] ?>" class="btn btn-danger btn-sm"
                  onclick="return confirmDelete(event)">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
    <?php include __DIR__ . '/partials/footer.php'; ?>