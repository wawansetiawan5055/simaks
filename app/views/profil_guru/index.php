<?php include __DIR__ . '/../partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-id-badge mr-2"></i> Profil Guru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?mod=dashboard">Home</a></li>
                    <li class="breadcrumb-item active">Profil Guru</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['pesan_sukses'];
                unset($_SESSION['pesan_sukses']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Daftar Guru & Staff</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>NIP / NUPTK</th>
                            <th>Status</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($gurus as $g): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($g['nama']) ?></td>
                                <td>
                                    <?= !empty($g['kode_guru']) ? '<span class="badge badge-info">' . $g['kode_guru'] . '</span> ' : '' ?>
                                    <?= htmlspecialchars($g['nuptk'] ?? '-') ?>
                                </td>
                                <td><?= htmlspecialchars($g['status_kepegawaian'] ?? '-') ?></td>
                                <td>
                                    <a href="index.php?mod=profil_guru&act=detail&id=<?= $g['id_guru'] ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-edit"></i> Kelola Profil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": true, "autoWidth": false,
        });
    });
</script>