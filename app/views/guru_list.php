<?php include __DIR__ . '/partials/header.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <h1 class="m-0"><i class="fas fa-chalkboard-teacher mr-2"></i> Data Guru</h1>
    <a href="<?= BASE_URL ?>guru/export" class="btn btn-success mb-2"><i class="fas fa-file-excel"></i> Export Excel</a>
    <form action="<?= BASE_URL ?>guru/import" method="POST" enctype="multipart/form-data" class="d-inline">
      <input type="file" name="file_excel" accept=".xlsx,.xls" required>
      <button type="submit" class="btn btn-secondary mb-2"><i class="fas fa-file-import"></i> Import Excel</button>
    </form>
    <a href="<?= BASE_URL ?>guru/form" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah Guru</a>
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th class="d-none d-sm-table-cell">NUPTK</th>
            <th class="d-none d-md-table-cell">NIK</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($guru_list as $g): ?>
            <tr>
              <td><?= $g['id_guru'] ?></td>
              <td><?= $g['nama'] ?></td>
              <td class="d-none d-sm-table-cell"><?= $g['nuptk'] ?></td>
              <td class="d-none d-md-table-cell"><?= $g['nik'] ?></td>
              <td><?= $g['status'] ?></td>
              <td>
                <a href="<?= BASE_URL ?>guru/form?id=<?= $g['id_guru'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="<?= BASE_URL ?>guru/delete?id=<?= $g['id_guru'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete(event)">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>