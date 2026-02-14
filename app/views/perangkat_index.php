<?php
// app/views/perangkat_index.php
include __DIR__ . '/partials/header.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-file-alt me-2"></i> <?= htmlspecialchars($title) ?>
                </h3>
                <div class="card-tools">
                    <a href="index.php?mod=perangkat&act=form&type=<?= $type ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Dokumen
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($dokumen_list)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>Belum ada dokumen <?= htmlspecialchars($jenis) ?>.</p>
                        <a href="index.php?mod=perangkat&act=form&type=<?= $type ?>" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Buat Dokumen Pertama
                        </a>
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover" id="tableDokumen">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Terakhir Diubah</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dokumen_list as $i => $dok): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($dok['judul']) ?></strong></td>
                                    <td><?= htmlspecialchars($dok['mapel']) ?></td>
                                    <td><?= htmlspecialchars($dok['kelas']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($dok['updated_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="index.php?mod=perangkat&act=print&id=<?= $dok['id_perangkat'] ?>" 
                                               class="btn btn-info" title="Lihat/Print" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="index.php?mod=perangkat&act=form&type=<?= $type ?>&id=<?= $dok['id_perangkat'] ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?mod=perangkat&act=delete&type=<?= $type ?>&id=<?= $dok['id_perangkat'] ?>" 
                                               class="btn btn-danger" onclick="return confirmDelete(event)" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tableDokumen').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
