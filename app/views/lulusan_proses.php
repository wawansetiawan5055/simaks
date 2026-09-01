<?php include __DIR__.'/partials/header.php'; ?>
<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Proses Kelulusan Siswa (Tingkat Akhir)
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <ol class="breadcrumb float-sm-right mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard" class="text-muted"><i class="fas fa-home mr-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item active text-primary font-weight-bold">Kelulusan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Alerts handled by toast -->

        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-graduate"></i> Daftar Calon Lulusan (Aktif & Kelas XII)</h3>
            </div>
            
            <form action="<?= BASE_URL ?>lulusan/save" method="POST" onsubmit="return confirm('Yakin ingin meluluskan siswa yang dipilih? Status mereka akan berubah menjadi LULUS.');">
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