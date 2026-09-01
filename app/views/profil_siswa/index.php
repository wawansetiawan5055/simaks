<?php include __DIR__ . '/../partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-id-card mr-2"></i> Profil Siswa</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Profil Siswa</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Alert Section -->
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['pesan_sukses'];
                unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Daftar Siswa</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableSiswa">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>NISN</th>
                                <th>Nama Lengkap</th>
                                <th>L/P</th>
                                <th>Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($siswas as $s): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($s['nisn'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($s['nama']) ?></td>
                                    <td><?= htmlspecialchars($s['jk'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($s['status_aktif'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>profil_siswa/detail?id=<?= $s['id_siswa'] ?>"
                                            class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i class="fas fa-folder-open mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
    $(function () {
        $("#tableSiswa").DataTable({
            "responsive": true, "autoWidth": false,
        });
    });
</script>