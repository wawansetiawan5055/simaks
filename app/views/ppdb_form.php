<?php include __DIR__ . '/partials/header.php'; ?>
<section class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-user-plus mr-2"></i> Formulir Pendaftaran Siswa Baru (PPDB)</h1>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <!-- Alerts handled by toast -->

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Input Data Calon Siswa</h3>
            </div>
            <form action="index.php?mod=ppdb&act=save" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>NISN (Opsional)</label>
                                <input type="text" name="nisn" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>NIK (Opsional)</label>
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
                                <label>Sekolah Asal (SD/SMP)</label>
                                <input type="text" name="sekolah_asal" class="form-control"
                                    placeholder="Contoh: SMPN 1 CIBADAK" required>
                            </div>
                            <div class="form-group">
                                <label>Jalur Pendaftaran</label>
                                <input type="text" name="jalur_pendaftaran" class="form-control"
                                    placeholder="Contoh: Zonasi, Prestasi, Afirmasi">
                            </div>
                            <div class="form-group">
                                <label>Nilai Seleksi (Rata-rata Rapor/Ujian)</label>
                                <input type="number" step="0.01" name="nilai_seleksi" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Nama Wali (Ayah/Ibu/Wali)</label>
                                <input type="text" name="nama_wali" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>No. Telepon Wali (Aktif WA)</label>
                                <input type="text" name="telp_wali" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Pendaftaran</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>