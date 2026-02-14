<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-arrow-right mr-2"></i> Detail Siswa Mutasi Masuk</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?mod=mutasi_masuk&act=index">Daftar Siswa Mutasi</a>
                    </li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Data Lengkap: <?= htmlspecialchars($data_mutasi['nama_lengkap']); ?></h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td style="width: 30%;">Status Penerimaan</td>
                        <td>
                            <?php
                            if ($data_mutasi['status_penerimaan'] == 'Pending')
                                echo '<span class="badge bg-warning">Pending</span>';
                            elseif ($data_mutasi['status_penerimaan'] == 'Diterima')
                                echo '<span class="badge bg-success">Diterima</span>';
                            else
                                echo '<span class="badge bg-danger">Ditolak</span>';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>ID Siswa Master (jika diterima)</td>
                        <td><?= htmlspecialchars($data_mutasi['id_siswa_master'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td><?= htmlspecialchars($data_mutasi['nama_lengkap']); ?></td>
                    </tr>
                    <tr>
                        <td>NISN / NIK</td>
                        <td><?= htmlspecialchars($data_mutasi['nisn']); ?> /
                            <?= htmlspecialchars($data_mutasi['nik']); ?></td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td><?= htmlspecialchars($data_mutasi['jk']); ?></td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td><?= htmlspecialchars($data_mutasi['tempat_lahir']); ?>,
                            <?= htmlspecialchars($data_mutasi['tanggal_lahir']); ?></td>
                    </tr>
                    <tr>
                        <td>Sekolah Asal</td>
                        <td><?= htmlspecialchars($data_mutasi['sekolah_asal']); ?></td>
                    </tr>
                    <tr>
                        <td>Tingkat Sebelumnya</td>
                        <td><?= htmlspecialchars($data_mutasi['tingkat_sebelumnya']); ?></td>
                    </tr>
                    <tr>
                        <td>Pindah Ke Tingkat</td>
                        <td><?= htmlspecialchars($data_mutasi['pindah_ke_tingkat']); ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Mutasi</td>
                        <td><?= htmlspecialchars($data_mutasi['tanggal_mutasi']); ?></td>
                    </tr>
                    <tr>
                        <td>Alasan Mutasi</td>
                        <td><?= htmlspecialchars($data_mutasi['alasan_mutasi']); ?></td>
                    </tr>
                    <tr>
                        <td>Diajukan pada TA</td>
                        <td><?= htmlspecialchars($data_mutasi['id_ta']); ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td><?= htmlspecialchars($data_mutasi['tanggal_pengajuan']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="index.php?mod=mutasi_masuk&act=index" class="btn btn-secondary">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>