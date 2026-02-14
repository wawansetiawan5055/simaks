<?php include '../app/views/partials/header.php'; ?>
<?php include '../app/views/partials/sidebar.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="fas fa-tags mr-2"></i> Setting Tarif Khusus</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Tarif Khusus</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Data Tarif berhasil disimpan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Tarif Khusus (Tiered Pricing)</h5>
                        <p class="text-muted small">
                            Gunakan menu ini untuk mengatur harga/biaya yang <strong>BERBEDA</strong> dari harga
                            standar.
                            <br>Prioritas Sistem: <strong>Tarif Siswa</strong> > <strong>Tarif Kelas</strong> >
                            <strong>Harga Default (COA)</strong>.
                        </p>

                        <a href="index.php?mod=keuangan_tarif&act=create" class="btn btn-primary mb-3">
                            <i class="bi bi-plus-circle"></i> Tambah Tarif Khusus
                        </a>

                        <div class="table-responsive">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Scope (Lingkup)</th>
                                        <th>Target (Kelas/Siswa)</th>
                                        <th>Nominal Khusus</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tarifs as $i => $t): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($t['nama_jenis']) ?></td>
                                            <td>
                                                <?php if ($t['id_siswa']): ?>
                                                    <span class="badge bg-success">Spesifik Siswa</span>
                                                <?php elseif ($t['id_kelas']): ?>
                                                    <span class="badge bg-primary">Spesifik Kelas</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Unknown</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($t['id_siswa']): ?>
                                                    <?= htmlspecialchars($t['nama_siswa']) ?>
                                                <?php elseif ($t['id_kelas']): ?>
                                                    <?= htmlspecialchars($t['nama_kelas']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end fw-bold"><?= number_format($t['nominal'], 0, ',', '.') ?>
                                            </td>
                                            <td><?= htmlspecialchars($t['keterangan']) ?></td>
                                            <td>
                                                <a href="index.php?mod=keuangan_tarif&act=delete&id=<?= $t['id_tarif'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus aturan tarif ini?')">
                                                    <i class="bi bi-trash"></i>
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
        </div>
    </section>
</main>

<?php include '../app/views/partials/footer.php'; ?>