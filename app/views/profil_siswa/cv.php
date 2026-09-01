<?php include __DIR__ . '/../partials/header.php'; ?>
<style>
    .cv-main-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        overflow: hidden;
        background: #ffffff;
    }
    .cv-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #2563eb 100%);
        color: white;
        padding: 28px 20px;
        position: relative;
    }
    .cv-avatar {
        width: 110px;
        height: 110px;
        border: 4px solid #ffffff;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        object-fit: cover;
        background: #f8fafc;
    }
    .cv-section-title {
        border-bottom: 2px solid #3b82f6;
        padding-bottom: 6px;
        margin-bottom: 16px;
        font-weight: 800;
        color: #1e293b;
        font-size: 1.05rem;
        font-family: 'Poppins', sans-serif;
    }
    .cv-data-row {
        display: flex;
        padding: 7px 0;
        border-bottom: 1px dashed #f1f5f9;
        font-size: 0.88rem;
    }
    .cv-data-row:last-child {
        border-bottom: none;
    }
    .cv-label {
        font-weight: 700;
        color: #64748b;
        width: 140px;
        flex-shrink: 0;
    }
    .cv-val {
        color: #1e293b;
        font-weight: 600;
        flex-grow: 1;
    }

    /* Berkas Card */
    .file-doc-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .file-doc-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }
    .file-doc-card.is-uploaded {
        background: #f0fdf4;
        border-color: #86efac;
    }

    /* ============================================================ */
    /* 📱 MOBILE RESPONSIVENESS (PROFIL SAYA SISWA)                 */
    /* ============================================================ */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 4px !important;
            padding-right: 4px !important;
        }
        .content-header {
            padding: 8px 4px 2px !important;
        }
        .content-header h4 {
            font-size: 0.90rem !important;
        }
        .cv-main-card {
            border-radius: 12px !important;
            margin-bottom: 10px !important;
        }
        .cv-header {
            padding: 18px 12px !important;
        }
        .cv-avatar {
            width: 80px !important;
            height: 80px !important;
            margin-bottom: 8px !important;
        }
        .cv-header h2 {
            font-size: 1.1rem !important;
            margin-bottom: 2px !important;
        }
        .cv-header p {
            font-size: 0.76rem !important;
        }
        .cv-main-card .card-body {
            padding: 12px 10px !important;
        }
        .cv-section-title {
            font-size: 0.88rem !important;
            margin-bottom: 10px !important;
        }
        .cv-data-row {
            font-size: 0.76rem !important;
            padding: 5px 0 !important;
        }
        .cv-label {
            width: 110px !important;
            font-size: 0.74rem !important;
        }
        .cv-val {
            font-size: 0.76rem !important;
        }
        .file-doc-card {
            padding: 10px 8px !important;
            border-radius: 10px !important;
            margin-bottom: 8px !important;
        }
        .file-doc-card h6 {
            font-size: 0.80rem !important;
        }
        .btn-sm {
            font-size: 0.72rem !important;
            padding: 5px 10px !important;
        }
    }
</style>

<div class="content-header pt-3 mb-2">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6 col-12 d-flex align-items-center">
                <div class="mr-3" style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h4 class="m-0 font-weight-bold text-dark" style="font-family: 'Poppins', sans-serif;">
                        Profil Saya (Biodata Siswa)
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 col-12 text-sm-right mt-2 mt-sm-0">
                <?php
                    $pengajuan_menunggu = count(array_filter($pengajuan_list ?? [], fn($p) => $p['status'] === 'Menunggu'));
                ?>
                <a href="<?= BASE_URL ?>profil_siswa/riwayat?id=<?= $siswa['id_siswa'] ?>" class="btn btn-outline-secondary btn-sm font-weight-bold rounded-pill px-3 shadow-sm mr-1">
                    <i class="fas fa-history mr-1"></i> Riwayat Pengajuan
                    <?php if ($pengajuan_menunggu > 0): ?>
                        <span class="badge badge-warning ml-1"><?= $pengajuan_menunggu ?></span>
                    <?php endif; ?>
                </a>
                <button type="button" class="btn btn-warning btn-sm font-weight-bold text-white rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#modalPengajuan">
                    <i class="fas fa-edit mr-1"></i> Ajukan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px;"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-check-circle mr-1"></i> <?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius:12px;"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-exclamation-circle mr-1"></i> <?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?></div>
        <?php endif; ?>

        <div class="cv-main-card">
            <!-- CV Header Banner -->
            <div class="cv-header text-center">
                <img src="<?= $avatar_src ?>" class="img-circle cv-avatar mb-2" alt="Foto Siswa">
                <h2 class="font-weight-bold mb-1" style="font-family: 'Poppins', sans-serif;"><?= htmlspecialchars($siswa['nama']) ?></h2>
                <p class="mb-0" style="opacity: 0.9;">
                    NISN: <strong><?= htmlspecialchars($siswa['nisn']) ?></strong> &bull; NIPD: <strong><?= htmlspecialchars($siswa['nipd']) ?></strong> &bull; 
                    <span class="badge badge-light text-primary font-weight-bold px-2 py-0.5 ml-1"><?= htmlspecialchars($siswa['status_aktif'] ?? 'Aktif') ?></span>
                </p>
            </div>

            <!-- CV Body -->
            <div class="card-body p-4 p-md-5">
                <div class="row">
                    <!-- Kolom Kiri: Data Diri -->
                    <div class="col-md-6 col-12 mb-4">
                        <h4 class="cv-section-title"><i class="fas fa-user-circle text-primary mr-1.5"></i> Data Pribadi</h4>
                        
                        <div class="cv-data-row">
                            <div class="cv-label">Nama Lengkap</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['nama']) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">NISN</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['nisn']) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">NIPD</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['nipd']) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">NIK</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['nik']) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Jenis Kelamin</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['jk']) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Tempat, Tgl Lahir</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['tempat_lahir']) ?>, <?= htmlspecialchars(tgl_indo($siswa['tanggal_lahir'])) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Sekolah Asal</div>
                            <div class="cv-val">: <?= htmlspecialchars($siswa['sekolah_asal']) ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Status Siswa</div>
                            <div class="cv-val">: <span class="badge badge-success px-2 py-0.5"><?= htmlspecialchars($siswa['status_aktif']) ?></span></div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Data Orang Tua -->
                    <div class="col-md-6 col-12 mb-4">
                        <h4 class="cv-section-title"><i class="fas fa-users text-info mr-1.5"></i> Data Orang Tua / Wali</h4>
                        
                        <div class="cv-data-row">
                            <div class="cv-label">Nama Ayah</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['nama_ayah'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Pekerjaan Ayah</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['pekerjaan_ayah'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Telp Ayah</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['telp_ayah'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Nama Ibu</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['nama_ibu'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Pekerjaan Ibu</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['pekerjaan_ibu'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Telp Ibu</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['telp_ibu'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Nama Wali</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['nama_wali'] ?? '-') ?></div>
                        </div>
                        <div class="cv-data-row">
                            <div class="cv-label">Pekerjaan Wali</div>
                            <div class="cv-val">: <?= htmlspecialchars($profil['pekerjaan_wali'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>

                <!-- Bagian Bawah: Berkas -->
                <h4 class="cv-section-title mt-2"><i class="fas fa-folder-open text-warning mr-1.5"></i> Berkas Pendukung</h4>
                <div class="row">
                    <?php
                    $files = [
                        'file_ijazah' => 'Ijazah Terakhir',
                        'file_kartu_keluarga' => 'Kartu Keluarga',
                        'file_akte_lahir' => 'Akte Kelahiran',
                        'file_ktp_ortu' => 'KTP Orang Tua',
                        'file_kip' => 'Kartu KIP'
                    ];
                    foreach ($files as $col => $label):
                        $fVal = $profil[$col] ?? null;
                    ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="file-doc-card <?= $fVal ? 'is-uploaded' : '' ?>">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-dark mb-0"><?= $label ?></h6>
                                    <?php if ($fVal): ?>
                                        <span class="badge badge-success px-2 py-0.5"><i class="fas fa-check-circle mr-1"></i> Ada</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-0.5">Belum Ada</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($fVal): ?>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold rounded-pill px-3" onclick="previewFile('<?= BASE_URL ?>uploads/siswa/<?= $fVal ?>')">
                                            <i class="fas fa-eye mr-1"></i> Lihat Berkas
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <form action="<?= BASE_URL ?>profil_siswa/upload" method="post" enctype="multipart/form-data" class="mt-2 pt-2 border-top">
                                <input type="hidden" name="id_siswa" value="<?= $siswa['id_siswa'] ?>">
                                <input type="hidden" name="jenis_file" value="<?= $col ?>">
                                <div class="custom-file text-left mb-1">
                                    <input type="file" class="custom-file-input custom-file-input-sm" id="up_<?= $col ?>" name="file_upload" onchange="this.form.submit()" required>
                                    <label class="custom-file-label" for="up_<?= $col ?>" style="font-size: 0.76rem; height: 28px; padding: 3px 8px;">Unggah File...</label>
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.64rem;">Maks. 5MB (PDF/JPG/PNG). Divalidasi TU.</small>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Modal Pengajuan Perubahan Data -->
<div class="modal fade" id="modalPengajuan" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class="fas fa-edit"></i> Formulir Pengajuan Perubahan Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= BASE_URL ?>profil_siswa/ajukan" method="post">
          <div class="modal-body">
              <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i> Isi hanya kolom yang ingin Anda ubah. Kolom yang dikosongkan tidak akan diubah. Data tidak akan langsung berubah melainkan menunggu persetujuan Admin/TU.
              </div>
              
              <div class="form-group">
                  <label>Kategori Perubahan</label>
                  <select name="kategori" class="form-control" required>
                      <option value="Perbaikan Nama/Identitas">Perbaikan Nama/Identitas</option>
                      <option value="Perubahan Data Orang Tua">Perubahan Data Orang Tua/Wali</option>
                      <option value="Lainnya">Lainnya</option>
                  </select>
              </div>

              <div class="row">
                  <div class="col-md-6">
                      <h6 class="font-weight-bold text-primary border-bottom pb-2">Identitas Siswa</h6>
                      <div class="form-group">
                          <label>Nama Lengkap Benar</label>
                          <input type="text" class="form-control" name="nama" placeholder="Contoh perbaikan ejaan...">
                      </div>
                      <div class="form-group">
                          <label>NIK Benar</label>
                          <input type="text" class="form-control" name="nik">
                      </div>
                      <div class="form-group">
                          <label>Tempat Lahir Benar</label>
                          <input type="text" class="form-control" name="tempat_lahir">
                      </div>
                      <div class="form-group">
                          <label>Tanggal Lahir Benar</label>
                          <input type="date" class="form-control" name="tanggal_lahir">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <h6 class="font-weight-bold text-primary border-bottom pb-2">Data Orang Tua</h6>
                      <div class="form-group">
                          <label>Nama Ayah Benar</label>
                          <input type="text" class="form-control" name="nama_ayah">
                      </div>
                      <div class="form-group">
                          <label>No. Telp Ayah Aktif</label>
                          <input type="text" class="form-control" name="telp_ayah">
                      </div>
                      <div class="form-group">
                          <label>Nama Ibu Benar</label>
                          <input type="text" class="form-control" name="nama_ibu">
                      </div>
                      <div class="form-group">
                          <label>No. Telp Ibu Aktif</label>
                          <input type="text" class="form-control" name="telp_ibu">
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Pengajuan</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Preview Berkas -->
<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl" style="max-width:90%; height:90%;">
        <div class="modal-content" style="height:100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="siswaModalTitle">Preview Berkas</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" style="height:calc(100% - 60px);">
                <iframe src="" id="filePreviewFrame" style="width:100%; height:100%; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script>
    function previewFile(url) {
        $('#filePreviewFrame').attr('src', url);
        $('#filePreviewModal').modal('show');
    }
</script>
