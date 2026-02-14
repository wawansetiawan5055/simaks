<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-exchange-alt mr-2"></i> Daftar Siswa Mutasi Masuk</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#modalTambahMutasi">
                            <i class="fas fa-plus"></i> Tambah Data Mutasi
                        </button>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Siswa Pindahan (Pending/Diterima)</h3>
                <div class="card-tools">
                    <a href="index.php?mod=mutasi_masuk&act=export_excel" class="btn btn-success btn-sm"><i
                            class="fas fa-file-excel"></i> Export Excel</a>
                    <a href="index.php?mod=mutasi_masuk&act=export_pdf" class="btn btn-danger btn-sm"><i
                            class="fas fa-file-pdf"></i> Export PDF</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">No</th>
                            <th>Tgl Pengajuan</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Sekolah Asal</th>
                            <th>Pindah Ke</th>
                            <th>Status</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list_mutasi)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data mutasi masuk.</td>
                            </tr>
                        <?php endif; ?>

                        <?php $no = 1;
                        foreach ($list_mutasi as $data): ?>
                            <tr>
                                <td><?= $no++; ?>.</td>
                                <td><?= htmlspecialchars($data['tanggal_pengajuan']); ?></td>
                                <td><?= htmlspecialchars($data['nama_lengkap']); ?></td>
                                <td><?= htmlspecialchars($data['nisn']); ?></td>
                                <td><?= htmlspecialchars($data['sekolah_asal']); ?></td>
                                <td><?= htmlspecialchars($data['pindah_ke_tingkat']); ?></td>
                                <td>
                                    <?php
                                    $status = $data['status_penerimaan'];
                                    if ($status == 'Pending') {
                                        echo '<span class="badge bg-warning">Pending</span>';
                                    } elseif ($status == 'Diterima') {
                                        // Jika sudah diterima, tampilkan ID Siswa Masternya
                                        echo '<span class="badge bg-success">Diterima (ID: ' . $data['id_siswa_master'] . ')</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Ditolak</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="index.php?mod=mutasi_masuk&act=detail&id=<?= $data['id_mutasi']; ?>"
                                        class="btn btn-info btn-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($status == 'Pending'): // Hanya tampilkan jika status masih Pending ?>
                                        <a href="index.php?mod=mutasi_masuk&act=promote&id=<?= $data['id_mutasi']; ?>"
                                            class="btn btn-success btn-sm" title="Verifikasi & Terima Siswa"
                                            onclick="return confirm('Apakah Anda yakin ingin menerima siswa ini dan memindahkannya ke Data Master Siswa Aktif?')">
                                            <i class="fas fa-check"></i> Terima
                                        </a>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- Modal Tambah Mutasi -->
<div class="modal fade" id="modalTambahMutasi" tabindex="-1" role="dialog" aria-labelledby="modalTambahMutasiLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalTambahMutasiLabel"><i class="fas fa-user-plus"></i> Formulir
                    Mutasi Masuk</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?mod=mutasi_masuk&act=save" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>NISN</label>
                                <input type="text" name="nisn" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jk" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sekolah Asal</label>
                                <input type="text" name="sekolah_asal" class="form-control"
                                    placeholder="Contoh: SMAN 1 JAKARTA" required>
                            </div>
                            <div class="form-group">
                                <label>Tingkat Terakhir di Sekolah Asal</label>
                                <input type="text" name="tingkat_sebelumnya" class="form-control"
                                    placeholder="Contoh: Kelas X">
                            </div>
                            <div class="form-group">
                                <label>Pindah ke Tingkat</label>
                                <input type="text" name="pindah_ke_tingkat" class="form-control"
                                    placeholder="Contoh: Kelas XI" required>
                            </div>
                            <div class="form-group">
                                <label><strong>Kelas Tujuan</strong> <span class="text-danger">*</span></label>
                                <select name="id_kelas_tujuan" class="form-control" required>
                                    <option value="">-- Pilih Kelas Tujuan --</option>
                                    <?php foreach ($daftar_kelas as $kelas): ?>
                                        <option value="<?= $kelas['id_kelas'] ?>">
                                            <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Mutasi</label>
                                <input type="date" name="tanggal_mutasi" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Alasan Mutasi</label>
                                <textarea name="alasan_mutasi" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>