<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1>Proses Kelulusan Siswa (Kelas XII)</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        <!-- Alerts handled by toast -->

        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-graduate"></i> Daftar Calon Lulusan (Aktif & Kelas XII)</h3>
            </div>
            
            <form action="index.php?mod=lulusan&act=save" method="POST" onsubmit="return confirm('Yakin ingin meluluskan siswa yang dipilih? Status mereka akan berubah menjadi LULUS.');">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">
                                    <input type="checkbox" id="check-all">
                                </th>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama Siswa</th>
                                <th>Kelas Saat Ini</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($calon_lulusan)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada siswa kelas XII yang berstatus Aktif.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no=1; foreach($calon_lulusan as $s): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="pilih_siswa[]" class="check-item" value="<?= $s['id_siswa'] ?>">
                                    </td>
                                    <td><?= $no++ ?></td>
                                    <td><?= $s['nisn'] ?></td>
                                    <td class="font-weight-bold"><?= $s['nama'] ?></td>
                                    <td><?= $s['nama_kelas'] ?></td>
                                    <td><span class="badge badge-success">Aktif</span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning float-right">
                        <i class="fas fa-graduation-cap"></i> Proses Lulus Terpilih
                    </button>
                    <small class="text-muted">* Hanya siswa yang dicentang yang akan diproses.</small>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Script Select All Checkbox
document.getElementById('check-all').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.check-item');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
});
</script>

<?php include __DIR__.'/partials/footer.php'; ?>