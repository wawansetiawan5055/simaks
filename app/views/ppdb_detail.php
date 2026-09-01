<?php include __DIR__.'/partials/header.php'; ?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Detail Pendaftar PPDB</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>ppdb">PPDB</a></li>
          <li class="breadcrumb-item active">Detail</li>
        </ol>
      </div>
    </div>
  </div>
</section>
<section class="content">
<div class="container-fluid">
    <div class="row">
        <!-- DATA PRIBADI -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user"></i> Data Pribadi</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">No. Pendaftaran</th>
                            <td><strong><?= htmlspecialchars($data_pendaftar['no_pendaftaran']) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Sumber Pendaftaran</th>
                            <td>
                                <?php if ($data_pendaftar['sumber_pendaftaran'] == 'online'): ?>
                                    <span class="badge badge-success"><i class="fas fa-laptop"></i> Online (Landing Page)</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fas fa-user-edit"></i> Manual Entry</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php 
                                    $status = $data_pendaftar['status'];
                                    if ($status == 'pending') echo '<span class="badge badge-secondary">Pending</span>';
                                    elseif ($status == 'diverifikasi') echo '<span class="badge badge-info">Diverifikasi</span>';
                                    elseif ($status == 'diterima') echo '<span class="badge badge-success">Diterima</span>';
                                    elseif ($status == 'ditolak') echo '<span class="badge badge-danger">Ditolak</span>';
                                    elseif ($status == 'diproses_jadi_siswa') echo '<span class="badge badge-primary">Sudah Jadi Siswa</span>';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td><?= htmlspecialchars($data_pendaftar['nama_lengkap']) ?></td>
                        </tr>
                        <tr>
                            <th>NISN</th>
                            <td><?= htmlspecialchars($data_pendaftar['nisn']) ?></td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td><?= htmlspecialchars($data_pendaftar['nik']) ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td><?= $data_pendaftar['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        </tr>
                        <tr>
                            <th>Tempat, Tgl Lahir</th>
                            <td>
                                <?= htmlspecialchars($data_pendaftar['tempat_lahir']) ?>, 
                                <?= $data_pendaftar['tanggal_lahir'] ? date('d-m-Y', strtotime($data_pendaftar['tanggal_lahir'])) : '-' ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Agama</th>
                            <td><?= htmlspecialchars($data_pendaftar['agama'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>No. HP Siswa</th>
                            <td><?= htmlspecialchars($data_pendaftar['no_hp_siswa'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Email Siswa</th>
                            <td><?= htmlspecialchars($data_pendaftar['email_siswa'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ALAMAT -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Alamat</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Alamat</th>
                            <td><?= htmlspecialchars($data_pendaftar['alamat'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>RT/RW</th>
                            <td><?= htmlspecialchars($data_pendaftar['rt'] ?? '-') ?> / <?= htmlspecialchars($data_pendaftar['rw'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Kelurahan</th>
                            <td><?= htmlspecialchars($data_pendaftar['kelurahan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Kecamatan</th>
                            <td><?= htmlspecialchars($data_pendaftar['kecamatan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Kota</th>
                            <td><?= htmlspecialchars($data_pendaftar['kota'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Provinsi</th>
                            <td><?= htmlspecialchars($data_pendaftar['provinsi'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Kode Pos</th>
                            <td><?= htmlspecialchars($data_pendaftar['kode_pos'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- ORANG TUA & SEKOLAH ASAL -->
        <div class="col-md-6">
            <!-- DATA ORANG TUA -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users"></i> Data Orang Tua / Wali</h3>
                </div>
                <div class="card-body">
                    <h5>Ayah</h5>
                    <table class="table table-sm table-bordered mb-3">
                        <tr>
                            <th width="150">Nama</th>
                            <td><?= htmlspecialchars($data_pendaftar['nama_ayah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td><?= htmlspecialchars($data_pendaftar['pekerjaan_ayah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Penghasilan</th>
                            <td><?= htmlspecialchars($data_pendaftar['penghasilan_ayah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td><?= htmlspecialchars($data_pendaftar['no_hp_ayah'] ?? '-') ?></td>
                        </tr>
                    </table>

                    <h5>Ibu</h5>
                    <table class="table table-sm table-bordered mb-3">
                        <tr>
                            <th width="150">Nama</th>
                            <td><?= htmlspecialchars($data_pendaftar['nama_ibu'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td><?= htmlspecialchars($data_pendaftar['pekerjaan_ibu'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Penghasilan</th>
                            <td><?= htmlspecialchars($data_pendaftar['penghasilan_ibu'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td><?= htmlspecialchars($data_pendaftar['no_hp_ibu'] ?? '-') ?></td>
                        </tr>
                    </table>

                    <h5>Wali</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="150">Nama</th>
                            <td><?= htmlspecialchars($data_pendaftar['nama_wali'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td><?= htmlspecialchars($data_pendaftar['pekerjaan_wali'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td><?= htmlspecialchars($data_pendaftar['no_hp_wali'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- SEKOLAH ASAL -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-school"></i> Sekolah Asal</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="150">Nama Sekolah</th>
                            <td><?= htmlspecialchars($data_pendaftar['asal_sekolah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Alamat Sekolah</th>
                            <td><?= htmlspecialchars($data_pendaftar['alamat_sekolah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>NPSN</th>
                            <td><?= htmlspecialchars($data_pendaftar['npsn_sekolah'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Jalur Pendaftaran</th>
                            <td><span class="badge badge-info"><?= htmlspecialchars($data_pendaftar['jalur_pendaftaran']) ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DOKUMEN UPLOAD (Jika ada) -->
    <?php if ($data_pendaftar['sumber_pendaftaran'] == 'online'): ?>
    <div class="row">
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-upload"></i> Dokumen Upload</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        $dokumen = [
                            'foto_siswa' => 'Foto Siswa',
                            'foto_kk' => 'Kartu Keluarga',
                            'foto_akta' => 'Akta Kelahiran',
                            'foto_ijazah' => 'Ijazah',
                            'foto_raport' => 'Raport'
                        ];
                        
                        foreach ($dokumen as $key => $label):
                            if (!empty($data_pendaftar[$key])):
                        ?>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <strong><?= $label ?></strong>
                                </div>
                                <div class="card-body text-center">
                                    <?php 
                                    $file_path = BASE_URL . $data_pendaftar[$key];
                                    $ext = strtolower(pathinfo($data_pendaftar[$key], PATHINFO_EXTENSION));
                                    ?>
                                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                                        <img src="<?= $file_path ?>" alt="<?= $label ?>" class="img-fluid" style="max-height: 200px;">
                                    <?php else: ?>
                                        <i class="fas fa-file-pdf fa-5x text-danger"></i>
                                        <p class="mt-2">File PDF</p>
                                    <?php endif; ?>
                                    <br>
                                    <a href="<?= $file_path ?>" target="_blank" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-external-link-alt"></i> Lihat File
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- CATATAN VERIFIKASI -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sticky-note"></i> Catatan Verifikasi</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>ppdb/update_catatan">
                        <input type="hidden" name="id" value="<?= $data_pendaftar['id'] ?>">
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tulis catatan verifikasi..."><?= htmlspecialchars($data_pendaftar['catatan_verifikasi'] ?? '') ?></textarea>
                        <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Simpan Catatan</button>
                    </form>
                    
                    <?php if (!empty($data_pendaftar['verified_by'])): ?>
                    <hr>
                    <small class="text-muted">
                        Diverifikasi oleh User ID: <?= $data_pendaftar['verified_by'] ?> 
                        pada <?= $data_pendaftar['verified_at'] ? date('d-m-Y H:i', strtotime($data_pendaftar['verified_at'])) : '-' ?>
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TOMBOL AKSI -->
    <div class="row">
        <div class="col-12">
            <a href="<?= BASE_URL ?>ppdb" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <?php if ($data_pendaftar['status'] == 'pending'): ?>
                <a href="<?= BASE_URL ?>ppdb/update_status?id=<?= $data_pendaftar['id'] ?>&status=diterima" 
                   class="btn btn-success">
                    <i class="fas fa-check"></i> Terima
                </a>
                <a href="<?= BASE_URL ?>ppdb/update_status?id=<?= $data_pendaftar['id'] ?>&status=ditolak" 
                   class="btn btn-danger">
                    <i class="fas fa-times"></i> Tolak
                </a>
            <?php endif; ?>
            
            <?php if (empty($data_pendaftar['id_siswa'])): ?>
                <a href="<?= BASE_URL ?>ppdb/delete?id=<?= $data_pendaftar['id'] ?>" 
                   class="btn btn-danger float-right" 
                   onclick="return confirmDelete(event, 'Data pendaftar ini akan dihapus permanen!')">
                    <i class="fas fa-trash"></i> Hapus Data
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
</section>
<?php include __DIR__.'/partials/footer.php'; ?>
